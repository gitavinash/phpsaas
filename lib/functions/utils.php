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