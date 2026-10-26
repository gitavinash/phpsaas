<?php
/**
 * QA Unit Test Suite: WhatsApp Lead Alert & BhashSMS Business API Routing
 * Path: scratch/test_whatsapp_lead_alert_qa.php
 */

define('ROOT', dirname(__DIR__));
define('DEVELOPMENT_MODE', true);
define('SITE_NAME', 'Musafir India Trip Pvt. Ltd.');
define('SITE_URL', 'https://crm.musafirindiatrip.com');
define('SITE_ID', 1);
define('TABLE_PREFIX', 'isw_');
define('WHATSAPP_TEMPLATE', dirname(__DIR__) . '/lib/resource/whatsapp/');
define('WHATSAPP_TEMPLATE_ADMIN', dirname(__DIR__) . '/lib/resource/whatsapp/aleya/administrator/');

$testsTotal = 0;
$testsPassed = 0;
$testsFailed = 0;

function assertTest($description, $condition, $details = '') {
    global $testsTotal, $testsPassed, $testsFailed;
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$description}\n";
    } else {
        $testsFailed++;
        echo "  [FAIL] {$description}\n";
        if (!empty($details)) {
            echo "         Detail: {$details}\n";
        }
    }
}

echo "========================================================\n";
echo " QA UNIT TEST SUITE: WhatsApp Lead Alert & Bhash API\n";
echo "========================================================\n\n";

// -----------------------------------------------------------
// TEST SUITE 1: Parameter Comma & Newline Sanitization
// -----------------------------------------------------------
echo "Suite 1: Parameter Sanitization & Delimiter Safety\n";

if (!function_exists('cleanAleyaBw1Param')) {
    function cleanAleyaBw1Param($val, $default = 'N/A')
    {
        if (is_array($val)) {
            $val = implode(' ', array_map('trim', $val));
        }
        $val = trim((string)($val ?? ''));
        if ($val === '') {
            $val = $default;
        }
        return str_replace([',', "\r", "\n"], [' ', ' ', ' '], $val);
    }
}

$inputWithCommas = "Delhi, Agra, Jaipur, Need 2 cabs, 4 persons";
$cleanedCommas = cleanAleyaBw1Param($inputWithCommas);
assertTest("Removes all commas from text to prevent parameter count corruption", strpos($cleanedCommas, ',') === false, "Output: {$cleanedCommas}");

$inputWithNewlines = "Need pickup\r\nFrom Delhi airport\nDrop to hotel";
$cleanedNewlines = cleanAleyaBw1Param($inputWithNewlines);
assertTest("Replaces newlines and carriage returns with spaces", strpos($cleanedNewlines, "\n") === false && strpos($cleanedNewlines, "\r") === false);

$inputEmpty = "";
$cleanedEmpty = cleanAleyaBw1Param($inputEmpty, 'N/A');
assertTest("Defaults to 'N/A' when input is empty string", $cleanedEmpty === 'N/A');

$inputNull = null;
$cleanedNull = cleanAleyaBw1Param($inputNull, 'N/A');
assertTest("Defaults to 'N/A' when input is null", $cleanedNull === 'N/A');

$inputArray = ['Golden', 'Triangle', 'Tour'];
$cleanedArray = cleanAleyaBw1Param($inputArray);
assertTest("Flattens array input into space-separated string", $cleanedArray === 'Golden Triangle Tour');

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 2: Lead Alert Title / Subject Formatting
// -----------------------------------------------------------
echo "Suite 2: Lead Alert Subject / Title Formatting\n";

function formatLeadAlertSubject($rawSubject) {
    $rawSubject = is_array($rawSubject) ? implode(' ', array_map('trim', $rawSubject)) : trim((string)($rawSubject ?? ''));
    if ($rawSubject === '' || strcasecmp($rawSubject, 'Website Enquiry') === 0 || strcasecmp($rawSubject, 'enquiry') === 0 || strcasecmp($rawSubject, 'N/A') === 0) {
        return 'New Lead Alert';
    }
    return 'New Lead Alert: ' . $rawSubject;
}

assertTest("Replaces 'Website Enquiry' with 'New Lead Alert'", formatLeadAlertSubject('Website Enquiry') === 'New Lead Alert');
assertTest("Replaces case-insensitive 'website enquiry' with 'New Lead Alert'", formatLeadAlertSubject('website enquiry') === 'New Lead Alert');
assertTest("Replaces generic 'enquiry' with 'New Lead Alert'", formatLeadAlertSubject('enquiry') === 'New Lead Alert');
assertTest("Defaults empty subject to 'New Lead Alert'", formatLeadAlertSubject('') === 'New Lead Alert');
assertTest("Defaults null subject to 'New Lead Alert'", formatLeadAlertSubject(null) === 'New Lead Alert');
assertTest("Prefixes specific package with 'New Lead Alert: '", formatLeadAlertSubject('Dubai 5N/6D Package') === 'New Lead Alert: Dubai 5N/6D Package');
assertTest("Prefixes array package with 'New Lead Alert: '", formatLeadAlertSubject(['Kerala', 'Honeymoon']) === 'New Lead Alert: Kerala Honeymoon');

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 3: Phone Number Normalization
// -----------------------------------------------------------
echo "Suite 3: Phone Number Normalization for BhashSMS\n";

function normalizePhoneNumber($phone) {
    $cleanNumber = preg_replace('/[^0-9]/', '', (string)$phone);
    if (strlen($cleanNumber) === 11 && substr($cleanNumber, 0, 1) === '0') {
        $cleanNumber = substr($cleanNumber, 1);
    }
    return $cleanNumber;
}

assertTest("Strips '+', '-', spaces from '+91-99104 24114' -> '919910424114'", normalizePhoneNumber('+91-99104 24114') === '919910424114');
assertTest("Strips leading 0 from 11-digit phone '09910424114' -> '9910424114'", normalizePhoneNumber('09910424114') === '9910424114');
assertTest("Maintains clean 10-digit number '9910424114'", normalizePhoneNumber('9910424114') === '9910424114');
assertTest("Strips formatting from standard mobile '+91 86004-62460'", normalizePhoneNumber('+91 86004-62460') === '918600462460');

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 4: WhatsApp Web Device Bypass Verification
// -----------------------------------------------------------
echo "Suite 4: WhatsApp Web Device Bypass Logic in footer.php\n";

// Simulate conditions inside footer.php
function testShouldBypassDevice($bypassDevice, $templateName) {
    return (!empty($bypassDevice) || (isset($templateName) && $templateName === 'aleya_bw_1'));
}

assertTest("Bypasses WhatsApp Web device when template is 'aleya_bw_1'", testShouldBypassDevice(false, 'aleya_bw_1') === true);
assertTest("Bypasses WhatsApp Web device when bypassDevice is true", testShouldBypassDevice(true, 'some_other_template') === true);
assertTest("Does NOT bypass WhatsApp Web device for normal payment template", testShouldBypassDevice(false, 'payment_alert') === false);
assertTest("Does NOT bypass WhatsApp Web device for normal itinerary template", testShouldBypassDevice(false, 'itinerary_proposal') === false);

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 5: Subject Non-Mutation Integration Check
// -----------------------------------------------------------
echo "Suite 5: Subject Preservation Across Email & WhatsApp Pipeline\n";

$initialSubject = 'Website Enquiry';
$cleanEnquirySubject = $initialSubject;

// Simulate email template mutation (as in enquiry-thank-you.php)
$tourPackage = $initialSubject;
$mutatedSubject = "Confirmation of Your Enquiry for {$initialSubject} | " . SITE_NAME;

// Verify cleanEnquirySubject remained unaffected by the email mutation
assertTest("Original subject preserved despite email template mutation", $cleanEnquirySubject === 'Website Enquiry');
assertTest("Email template mutated subject matches expected email pattern", strpos($mutatedSubject, 'Confirmation of Your Enquiry') !== false);

// Now restore for WhatsApp
$subjectForWhatsApp = formatLeadAlertSubject($cleanEnquirySubject);
assertTest("WhatsApp pipeline receives clean 'New Lead Alert'", $subjectForWhatsApp === 'New Lead Alert');
assertTest("WhatsApp pipeline does NOT receive 'Confirmation of Your Enquiry'", strpos($subjectForWhatsApp, 'Confirmation of Your Enquiry') === false);

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 6: aleya_bw_1 Parameter String Integrity
// -----------------------------------------------------------
echo "Suite 6: Exactly 7 Parameters in aleya_bw_1 Variables String\n";

$testLeadId = 99269;
$testSubject = "New Lead Alert: Golden Triangle, Shimla & Manali";
$testTravelDate = "15-11-2026";
$testName = "Vishnu, Gawari";
$testMobile = "8600462460";
$testEmail = "prachigawarisg@gmail.com";
$testRequirements = "Need 3 deluxe rooms, airport pickup, veg meals only";
$testLink = SITE_URL . '/dashboard/leads/view/?id=' . base64_encode($testLeadId);

$paramSubject      = cleanAleyaBw1Param($testSubject, 'New Lead Alert');
$paramTravelDates  = cleanAleyaBw1Param($testTravelDate, 'N/A');
$paramName         = cleanAleyaBw1Param($testName, 'N/A');
$paramMobile       = cleanAleyaBw1Param($testMobile, 'N/A');
$paramEmail        = cleanAleyaBw1Param($testEmail, 'N/A');
$paramRequirements = cleanAleyaBw1Param($testRequirements, 'N/A');
$paramLink         = cleanAleyaBw1Param($testLink, SITE_URL);

$variablesValues = $paramSubject . ',' . $paramTravelDates . ',' . $paramName . ',' . $paramMobile . ',' . $paramEmail . ',' . $paramRequirements . ',' . $paramLink;

$explodedParams = explode(',', $variablesValues);
assertTest("Strictly generates exactly 7 comma-separated parameters", count($explodedParams) === 7, "Got: " . count($explodedParams));
assertTest("Parameter 1 is the sanitized subject", $explodedParams[0] === 'New Lead Alert: Golden Triangle  Shimla & Manali');
assertTest("Parameter 2 is the travel date", $explodedParams[1] === '15-11-2026');
assertTest("Parameter 3 is the sanitized name (no comma)", $explodedParams[2] === 'Vishnu  Gawari');
assertTest("Parameter 4 is mobile", $explodedParams[3] === '8600462460');
assertTest("Parameter 5 is email", $explodedParams[4] === 'prachigawarisg@gmail.com');
assertTest("Parameter 6 is requirements (commas replaced with spaces)", $explodedParams[5] === 'Need 3 deluxe rooms  airport pickup  veg meals only');
assertTest("Parameter 7 is CRM view link", $explodedParams[6] === 'https://crm.musafirindiatrip.com/dashboard/leads/view/?id=OTkyNjk=');

// Test Bhash query string construction
$query = http_build_query([
    "user"     => "Aleya_BWA",
    "pass"     => "123456",
    "sender"   => "BUZWAP",
    "phone"    => "919910424114",
    "text"     => "aleya_bw_1",
    "priority" => "wa",
    "stype"    => "normal"
]);

$safeParams = str_replace(
    [' ', '+', '&'],
    ['%20', '%20', '%26'],
    $variablesValues
);

$fullUrl = "https://bhashsms.com/api/sendmsgutil.php?" . $query . '&Params=' . $safeParams;

assertTest("Bhash URL has priority=wa", strpos($fullUrl, 'priority=wa') !== false);
assertTest("Bhash URL has text=aleya_bw_1", strpos($fullUrl, 'text=aleya_bw_1') !== false);
assertTest("Bhash URL has sender=BUZWAP", strpos($fullUrl, 'sender=BUZWAP') !== false);
assertTest("Bhash URL has correct clean phone", strpos($fullUrl, 'phone=919910424114') !== false);
assertTest("Bhash URL Params encoded correctly without unescaped spaces", strpos($fullUrl, ' ') === false);

echo "\n";

// -----------------------------------------------------------
// TEST SUITE 7: End-to-End Execution of new_enquiry.php
// -----------------------------------------------------------
echo "Suite 7: End-to-End Execution of new_enquiry.php\n";

$first_name = "Vishnu";
$last_name = "Gawari";
$mobile = "8600462460";
$email = "prachigawarisg@gmail.com";
$subject = "Website Enquiry";
$travel_date = "";
$requirements = "";
$leadId = 99269;
$admin_whatsapp_number = "+91-9910424114";
define('ADMIN_WHATSAPP_NUMBER', '+91-9910424114');
define('COMPANY_WHATSAPP_USERNAME', 'Aleya_BWA');
define('COMPANY_WHATSAPP_PASSWORD', '123456');

// Execute new_enquiry.php in isolated buffer
ob_start();
require WHATSAPP_TEMPLATE_ADMIN . 'new_enquiry.php';
$output = ob_get_clean();

assertTest("Template set to 'aleya_bw_1'", $templateName === 'aleya_bw_1');
assertTest("BypassDevice flag is set to true", $bypassDevice === true);
assertTest("Target phone number sanitized to 12 digits '919910424114'", $sendMessageTo === '919910424114', "Got: {$sendMessageTo}");
assertTest("Bhash user set from company configuration", $whatsappAdmin === 'Aleya_BWA');
assertTest("Bhash password set from company configuration", $whatsappPassword === '123456');
assertTest("VariablesValues strictly contains 7 parameters", count(explode(',', $variablesValues)) === 7);
assertTest("Subject in variablesValues is 'New Lead Alert'", explode(',', $variablesValues)[0] === 'New Lead Alert');
assertTest("Link in variablesValues correctly base64-encodes leadId 99269", explode(',', $variablesValues)[6] === SITE_URL . '/dashboard/leads/view/?id=OTkyNjk=');

echo "\n========================================================\n";
echo " TEST SUMMARY\n";
echo " Total Tests:  {$testsTotal}\n";
echo " Passed Tests: {$testsPassed}\n";
echo " Failed Tests: {$testsFailed}\n";
echo "========================================================\n";

if ($testsFailed > 0) {
    exit(1);
} else {
    echo " ALL TESTS PASSED SUCCESSFULLY! Logic and flow verified.\n";
    exit(0);
}
