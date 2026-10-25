<?php
/**
 * Automated Test Suite: WhatsApp Backend Unit, Integration & Security Tests
 * Covers: Phone normalization, LID detection, internal label serialization,
 * cross-contact phone isolation, and multi-tenant isolation boundaries.
 */

$_SERVER['DOCUMENT_ROOT'] = '/opt/homebrew/var/www/php/isw-app';
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/modules/connection.php';
require_once __DIR__ . '/../api/v3/whatsapp/phone_helper.php';

class WhatsAppBackendTestSuite {
    private $passed = 0;
    private $failed = 0;
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    private function assert($condition, $testName, $details = '') {
        if ($condition) {
            $this->passed++;
            echo "  ✓ PASS: {$testName}\n";
        } else {
            $this->failed++;
            echo "  ✗ FAIL: {$testName} " . ($details ? "({$details})" : "") . "\n";
        }
    }

    public function runAll() {
        echo "\n======================================================\n";
        echo "   WhatsApp CRM Backend Unit & Integration Tests\n";
        echo "======================================================\n\n";

        $this->testPhoneNormalization();
        $this->testPrivacyLidDetection();
        $this->testInternalLabelExtraction();
        $this->testCrossContactMessageIsolation();
        $this->testTenantSiteIsolation();

        echo "\n------------------------------------------------------\n";
        echo "Test Summary: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "------------------------------------------------------\n\n";

        return $this->failed === 0;
    }

    private function testPhoneNormalization() {
        echo "[1] Testing Phone Normalization (E.164 Canonization)\n";

        // Standard Indian numbers
        $this->assert(normalizeWhatsAppPhone('9810610879') === '+919810610879', '10-digit India defaults to +91');
        $this->assert(normalizeWhatsAppPhone('+919810610879') === '+919810610879', 'Already normalized +91 preserved');
        $this->assert(normalizeWhatsAppPhone('09810610879') === '+919810610879', 'Leading 0 stripped in favor of +91');
        $this->assert(normalizeWhatsAppPhone('+91 98106-10879') === '+919810610879', 'Spaces and hyphens stripped');

        // International numbers with same last 10 digits MUST NOT be merged
        $usNum = normalizeWhatsAppPhone('+18178539254');
        $inNum = normalizeWhatsAppPhone('+918178539254');
        $this->assert($usNum === '+18178539254', 'US country code +1 preserved');
        $this->assert($inNum === '+918178539254', 'India country code +91 preserved');
        $this->assert($usNum !== $inNum, 'US and IN numbers with same last 10 digits are DISTINCT');

        // UK numbers
        $this->assert(normalizeWhatsAppPhone('+447911123456') === '+447911123456', 'UK number preserved');

        // Empty / Invalid
        $this->assert(normalizeWhatsAppPhone('') === '', 'Empty string returns empty');
        $this->assert(normalizeWhatsAppPhone('abc') === '', 'Non-numeric string returns empty');
    }

    private function testPrivacyLidDetection() {
        echo "\n[2] Testing WhatsApp Privacy LID Detection\n";

        $this->assert(isWhatsAppPrivacyLid('274135028658248@lid'), 'JID with @lid recognized as LID');
        $this->assert(isWhatsAppPrivacyLid('+274135028658248'), '15-digit number recognized as Privacy LID');
        $this->assert(isWhatsAppPrivacyLid('14449737126102'), '14-digit Privacy LID recognized');
        $this->assert(!isWhatsAppPrivacyLid('+919810610879'), 'Standard Indian phone is NOT a LID');
        $this->assert(!isWhatsAppPrivacyLid('+18178539254'), 'Standard US phone is NOT a LID');
    }

    private function testInternalLabelExtraction() {
        echo "\n[3] Testing Internal Message Type & Label Extraction\n";

        // Reaction extraction
        $r1 = extractInternalMessageType('[Reaction: 😂]', 'text');
        $this->assert($r1['type'] === 'reaction', 'Reaction type detected');
        $this->assert($r1['reaction_emoji'] === '😂', 'Reaction emoji extracted');
        $this->assert($r1['clean_text'] === '😂' && strpos($r1['clean_text'], '[Reaction:') === false, 'Raw reaction prefix hidden from clean_text');

        // Document prefix extraction
        $d1 = extractInternalMessageType('[Doc: Annual_Report_2026.pdf]', 'text');
        $this->assert($d1['type'] === 'document', 'Doc type detected');
        $this->assert($d1['doc_name'] === 'Annual_Report_2026.pdf', 'Doc name extracted cleanly');
        $this->assert($d1['clean_text'] === 'Annual_Report_2026.pdf', 'Doc prefix [Doc: ...] hidden');

        // Media placeholders
        $v1 = extractInternalMessageType('[Voice Note]', 'audio');
        $this->assert($v1['type'] === 'audio', 'Voice type preserved');
        $this->assert($v1['clean_text'] === '', 'Raw placeholder [Voice Note] cleared');

        // Legitimate user text with brackets MUST BE PRESERVED
        $userMsg = 'Please meet me in [Conference Room B] at 3pm';
        $u1 = extractInternalMessageType($userMsg, 'text');
        $this->assert($u1['type'] === 'text', 'Legitimate user text remains type text');
        $this->assert($u1['clean_text'] === $userMsg, 'Legitimate brackets in user text preserved intact');

        $userMsg2 = '[URGENT] Please review contract';
        $u2 = extractInternalMessageType($userMsg2, 'text');
        $this->assert($u2['clean_text'] === $userMsg2, '[URGENT] bracketed user text preserved');
    }

    private function testCrossContactMessageIsolation() {
        echo "\n[4] Testing Cross-Contact Message Isolation (Database Queries)\n";

        // Test Chat #620 (+18178539254) vs Chat #3487 (+918178539254)
        $cond620 = buildStrictPhoneCondition($this->connection, 'm.message_receiver', '+18178539254');
        $cond3487 = buildStrictPhoneCondition($this->connection, 'm.message_receiver', '+918178539254');

        $this->assert(strpos($cond620, '+18178539254') !== false, 'Strict condition contains +18178539254');
        $this->assert(strpos($cond3487, '+918178539254') !== false, 'Strict condition contains +918178539254');

        // Query Chat #620 messages with strict phone isolation
        $q620 = mysqli_query($this->connection, "
            SELECT m.message_id, m.message_receiver, m.message_sender 
            FROM isw_whatsapp_message m
            WHERE m.message_chat_id = 620 AND ({$cond620})
        ");
        $leakFound = false;
        while ($r = mysqli_fetch_assoc($q620)) {
            if (strpos($r['message_receiver'], '918178539254') !== false) {
                $leakFound = true;
            }
        }
        $this->assert(!$leakFound, 'Chat #620 messages NEVER contain +918178539254 recipient messages');

        // Query Chat #3487 messages
        $q3487 = mysqli_query($this->connection, "
            SELECT m.message_id, m.message_receiver, m.message_sender 
            FROM isw_whatsapp_message m
            WHERE m.message_chat_id = 3487 AND ({$cond3487})
        ");
        $hasGenuine = (mysqli_num_rows($q3487) > 0);
        $this->assert($hasGenuine, 'Chat #3487 contains its genuine +918178539254 messages');
    }

    private function testTenantSiteIsolation() {
        echo "\n[5] Testing Tenant / Site Boundary Isolation\n";

        // Query with invalid or different site_id must yield 0 results
        $fakeSiteId = 99999999;
        $q = mysqli_query($this->connection, "
            SELECT message_id FROM isw_whatsapp_message WHERE message_site_id = {$fakeSiteId}
        ");
        $this->assert(mysqli_num_rows($q) === 0, 'Unauthorized / foreign site_id returns 0 messages');
    }
}

$suite = new WhatsAppBackendTestSuite($connection);
$success = $suite->runAll();
exit($success ? 0 : 1);
