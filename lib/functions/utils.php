<?php
function getInitials($string)
{
    // Remove any special characters except letters, numbers and spaces
    $cleanString = preg_replace('/[^A-Za-z0-9\s]/', '', $string);

    // Split into words
    $words = preg_split('/\s+/', trim($cleanString));

    // Take first letter of each word
    $initials = '';
    foreach ($words as $word) {
        if ($word !== '') {
            $initials .= strtoupper($word[0]);
        }
    }

    return $initials;
}
function isEmailValid(string $email): bool
{
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    [$local] = explode('@', strtolower($email), 2);

    // Reject plus addressing for login
    if (strpos($local, '+') !== false) {
        return false;
    }

    return true;
}

function getRootDomain($host)
{
    $host = preg_replace('#^https?://#', '', $host); // Remove http/https
    $host = explode(':', $host)[0];                  // Remove port if any
    $host = rtrim($host, '/');                       // Remove trailing slash

    $parts = explode('.', $host);
    $count = count($parts);

    if ($count >= 2) {
        return $parts[$count - 2] . '.' . $parts[$count - 1]; // last two parts
    } else {
        return $host;
    }
}

function getPastDuration($pastTimestamp)
{
    // Convert string datetime to timestamp if necessary
    if (!is_numeric($pastTimestamp)) {
        $pastTimestamp = strtotime($pastTimestamp);
        if ($pastTimestamp === false) {
            return "Invalid date";
        }
    }

    $diff = time() - $pastTimestamp;

    if ($diff < 0) {
        return "In the future";
    } elseif ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } else {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}

function truncateText($text, $maxLength = 300)
{
    // Remove any extra whitespace
    $text = trim($text);

    // If text is already short enough, return as is
    if (strlen($text) <= $maxLength) {
        return $text;
    }

    // Truncate text to maxLength
    $truncated = substr($text, 0, $maxLength);

    // Make sure not to cut off in the middle of a word
    // Find the last space within the truncated string
    $lastSpace = strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }

    // Add ellipsis to indicate truncation
    return $truncated . '...';
}

function clearAllCookies()
{
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

    foreach ($_COOKIE as $name => $value) {
        // Clear cookie without specifying domain — applies to current host
        setcookie($name, '', [
            'expires'  => time() - 3600, // past date
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);

        // Also try clearing with root domain wildcard
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '.' . $_SERVER['SERVER_NAME'], // root + subdomains
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);
    }
}