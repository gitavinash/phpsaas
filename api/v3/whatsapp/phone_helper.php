<?php
/**
 * Canonical Phone Normalization & Contact Isolation Helper
 * Path: /api/v3/whatsapp/phone_helper.php
 * Enforces strict phone normalization, LID detection, and cross-contact privacy isolation.
 */

if (!function_exists('normalizeWhatsAppPhone')) {
    /**
     * Normalizes a phone number to standard E.164 format (+<country><national>).
     * Strictly isolates numbers to prevent cross-contact collision.
     *
     * @param string $rawPhone
     * @param string $defaultCountry
     * @return string
     */
    function normalizeWhatsAppPhone(string $rawPhone, string $defaultCountry = '91'): string {
        $trimmed = trim($rawPhone);
        if (empty($trimmed)) {
            return '';
        }

        // Remove whitespace, dashes, parentheses, and special characters
        $digits = preg_replace('/[^0-9]/', '', $trimmed);
        if (empty($digits)) {
            return '';
        }

        // Strip leading zeros (trunk prefix)
        if (str_starts_with($trimmed, '0')) {
            $digits = ltrim($digits, '0');
        }

        // Standard 10-digit national number (e.g. Indian mobile)
        if (strlen($digits) === 10) {
            return '+' . $defaultCountry . $digits;
        }

        // If explicitly prefixed with '+'
        if (str_starts_with($trimmed, '+')) {
            return '+' . $digits;
        }

        // If digits already include the country code without plus (e.g., 919876543210)
        return '+' . $digits;
    }
}

if (!function_exists('isWhatsAppPrivacyLid')) {
    /**
     * Detects if an identifier is a WhatsApp privacy LID (not a verified phone number).
     *
     * @param string $phoneOrJid
     * @return bool
     */
    function isWhatsAppPrivacyLid(string $phoneOrJid): bool {
        $p = strtolower(trim($phoneOrJid));
        if (str_contains($p, '@lid') || str_contains($p, 'lid')) {
            return true;
        }
        $digits = preg_replace('/[^0-9]/', '', $p);
        // Privacy LIDs typically have 14 to 17 digits
        return strlen($digits) >= 14;
    }
}

if (!function_exists('buildStrictPhoneCondition')) {
    /**
     * Builds strict, non-ambiguous SQL conditions matching ONLY the specified phone number.
     * Prevents partial/substring LIKE matches that leak messages across contacts.
     *
     * @param mysqli $connection
     * @param string $column
     * @param string $normalizedPhone
     * @return string
     */
    function buildStrictPhoneCondition(mysqli $connection, string $column, string $normalizedPhone): string {
        $clean = preg_replace('/[^0-9]/', '', $normalizedPhone);
        if (empty($clean)) {
            return '1=0';
        }
        $withPlus = '+' . $clean;

        $escWithPlus = mysqli_real_escape_string($connection, $withPlus);
        $escDigits = mysqli_real_escape_string($connection, $clean);

        return "({$column} = '{$escWithPlus}' OR {$column} = '{$escDigits}')";
    }
}

if (!function_exists('extractInternalMessageType')) {
    /**
     * Normalizes message text and extracts internal markers like [Reaction: ...], [Doc: ...].
     * Returns structured metadata so frontend never displays internal implementation labels.
     *
     * @param string $text
     * @param string $currentType
     * @return array
     */
    function extractInternalMessageType(string $text, string $currentType = 'text'): array {
        $trimmed = trim($text);

        // 1. WhatsApp Reaction: [Reaction: 😂]
        if (preg_match('/^\[Reaction:\s*(.*?)\]$/u', $trimmed, $mReaction)) {
            return [
                'type'           => 'reaction',
                'reaction_emoji' => trim($mReaction[1]),
                'clean_text'     => trim($mReaction[1]),
                'is_internal'    => true
            ];
        }

        // 2. WhatsApp Document: [Doc: filename.ext] or [Document]
        if (preg_match('/^\[Doc:\s*(.*?)\]$/i', $trimmed, $mDoc)) {
            return [
                'type'        => 'document',
                'doc_name'    => trim($mDoc[1]),
                'clean_text'  => trim($mDoc[1]),
                'is_internal' => true
            ];
        }
        if ($trimmed === '[Document]' || $trimmed === '[Attachment]') {
            return [
                'type'        => 'document',
                'doc_name'    => 'Document',
                'clean_text'  => 'Document',
                'is_internal' => true
            ];
        }

        // 3. Audio / Voice Note placeholders
        if ($trimmed === '[Voice Note]' || $trimmed === '[Audio]') {
            return [
                'type'        => 'audio',
                'clean_text'  => '',
                'is_internal' => true
            ];
        }

        // 4. Photo / Video placeholders
        if ($trimmed === '[Image]' || $trimmed === '[Photo]') {
            return [
                'type'        => 'image',
                'clean_text'  => '',
                'is_internal' => true
            ];
        }
        if ($trimmed === '[Video]' || $trimmed === '[Video Note]') {
            return [
                'type'        => 'video',
                'clean_text'  => '',
                'is_internal' => true
            ];
        }

        // 5. Contact placeholder
        if (preg_match('/^\[Contact:\s*(.*?)\]$/i', $trimmed, $mContact)) {
            return [
                'type'         => 'contact',
                'contact_name' => trim($mContact[1]),
                'clean_text'   => trim($mContact[1]),
                'is_internal'  => true
            ];
        }

        // 6. Location placeholder
        if (preg_match('/^\[Location:\s*(.*?)\]$/i', $trimmed, $mLoc) || $trimmed === '[Live Location]') {
            return [
                'type'          => 'location',
                'location_name' => isset($mLoc[1]) ? trim($mLoc[1]) : 'Shared Location',
                'clean_text'    => isset($mLoc[1]) ? trim($mLoc[1]) : 'Shared Location',
                'is_internal'   => true
            ];
        }

        // Normal genuine user text (including text that happens to have brackets like "[Urgent] Please check")
        return [
            'type'        => $currentType ?: 'text',
            'clean_text'  => $text,
            'is_internal' => false
        ];
    }
}
