<?php
function buildBooleanQuery(string $q): string
{
    $q = trim(mb_strtolower($q));
    // keep letters/digits/space
    $q = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $q);
    $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

    $tokens = [];
    foreach ($parts as $w) {
        if (mb_strlen($w) >= 3) { // ignore very short words
            $tokens[] = '+' . $w . '*'; // prefix match
        }
    }
    // if everything filtered, fall back to original (safe min)
    if (!$tokens && $q !== '') {
        $tokens[] = '+' . mb_substr($q, 0, 20) . '*';
    }
    return implode(' ', $tokens);
}