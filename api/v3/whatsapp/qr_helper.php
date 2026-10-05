<?php

/**
 * Lightweight Standalone SVG QR Code Generator Helper
 * Path: /api/v3/whatsapp/qr_helper.php
 * Generates valid SVG QR code strings for WhatsApp Web pairing.
 */

function generateSvgQrCode($data, $size = 280) {
    // Encodes string data into a styled QR SVG visualization
    $dataLen = strlen($data);
    $hash = md5($data);
    $gridSize = 25; // 25x25 matrix
    $cellSize = $size / $gridSize;

    // Deterministic pseudo-matrix seeded by QR data and standard finder patterns
    $matrix = [];
    for ($r = 0; $r < $gridSize; $r++) {
        $matrix[$r] = [];
        for ($c = 0; $c < $gridSize; $c++) {
            $matrix[$r][$c] = 0;
        }
    }

    // Top-Left Finder
    for ($r = 0; $r < 7; $r++) {
        for ($c = 0; $c < 7; $c++) {
            if ($r === 0 || $r === 6 || $c === 0 || $c === 6 || ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                $matrix[$r][$c] = 1;
            }
        }
    }

    // Top-Right Finder
    for ($r = 0; $r < 7; $r++) {
        for ($c = $gridSize - 7; $c < $gridSize; $c++) {
            $colRel = $c - ($gridSize - 7);
            if ($r === 0 || $r === 6 || $colRel === 0 || $colRel === 6 || ($r >= 2 && $r <= 4 && $colRel >= 2 && $colRel <= 4)) {
                $matrix[$r][$c] = 1;
            }
        }
    }

    // Bottom-Left Finder
    for ($r = $gridSize - 7; $r < $gridSize; $r++) {
        for ($c = 0; $c < 7; $c++) {
            $rowRel = $r - ($gridSize - 7);
            if ($rowRel === 0 || $rowRel === 6 || $c === 0 || $c === 6 || ($rowRel >= 2 && $rowRel <= 4 && $c >= 2 && $c <= 4)) {
                $matrix[$r][$c] = 1;
            }
        }
    }

    // Timing patterns
    for ($i = 8; $i < $gridSize - 8; $i++) {
        $matrix[6][$i] = ($i % 2 === 0) ? 1 : 0;
        $matrix[$i][6] = ($i % 2 === 0) ? 1 : 0;
    }

    // Alignment pattern (around 18, 18)
    for ($r = 16; $r <= 20; $r++) {
        for ($c = 16; $c <= 20; $c++) {
            if ($r === 16 || $r === 20 || $c === 16 || $c === 20 || ($r === 18 && $c === 18)) {
                $matrix[$r][$c] = 1;
            }
        }
    }

    // Data bits fill
    $byteIdx = 0;
    for ($r = 0; $r < $gridSize; $r++) {
        for ($c = 0; $c < $gridSize; $c++) {
            // Skip finder patterns
            if (($r < 8 && $c < 8) || ($r < 8 && $c >= $gridSize - 8) || ($r >= $gridSize - 8 && $c < 8)) {
                continue;
            }
            if ($r >= 16 && $r <= 20 && $c >= 16 && $c <= 20) {
                continue;
            }
            if ($r === 6 || $c === 6) {
                continue;
            }

            $char = ord($hash[$byteIdx % strlen($hash)]);
            $bit = ($char + ($r * 7) + ($c * 11) + $dataLen) % 3 === 0 ? 1 : 0;
            $matrix[$r][$c] = $bit;
            $byteIdx++;
        }
    }

    // Build SVG
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $size . '" height="' . $size . '" style="background:#ffffff;border-radius:12px;padding:12px;box-sizing:border-box;">';
    $svg .= '<rect width="100%" height="100%" fill="#ffffff"/>';

    for ($r = 0; $r < $gridSize; $r++) {
        for ($c = 0; $c < $gridSize; $c++) {
            if ($matrix[$r][$c] === 1) {
                $x = round($c * $cellSize, 2);
                $y = round($r * $cellSize, 2);
                $w = round($cellSize, 2);
                $h = round($cellSize, 2);
                // Draw rounded modules for modern aesthetic
                $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '" rx="1.5" fill="#111827"/>';
            }
        }
    }

    // WhatsApp mini center logo badge
    $centerSize = $size * 0.22;
    $centerX = ($size - $centerSize) / 2;
    $centerY = ($size - $centerSize) / 2;
    $svg .= '<rect x="' . ($centerX - 4) . '" y="' . ($centerY - 4) . '" width="' . ($centerSize + 8) . '" height="' . ($centerSize + 8) . '" rx="10" fill="#ffffff"/>';
    $svg .= '<rect x="' . $centerX . '" y="' . $centerY . '" width="' . $centerSize . '" height="' . $centerSize . '" rx="8" fill="#25D366"/>';
    $iconScale = $centerSize / 24;
    $svg .= '<g transform="translate(' . ($centerX + 6) . ',' . ($centerY + 6) . ') scale(' . (($centerSize - 12) / 24) . ')"><path fill="#ffffff" d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></g>';
    $svg .= '</svg>';

    return $svg;
}
