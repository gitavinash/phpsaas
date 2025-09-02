<?php
// Inputs expected (from caller):
// $profile_first_name, $quotationId, $content (decoded assoc array), SITE_NAME, SITE_DOMAIN

$itinerary  = isset($content['itinerary']) && is_array($content['itinerary']) ? $content['itinerary'] : [];
$totalDays  = count($itinerary);

// Extract quick highlights (up to 3) from the 'day' field like "Day 1 - Delhi to Shimla"
$highlights = [];
foreach ($itinerary as $d) {
  $raw = trim((string)($d['day'] ?? ''));
  if ($raw === '') continue;
  // Try to split by " - " to get the title part
  $parts = explode(' - ', $raw, 2);
  $title = isset($parts[1]) ? $parts[1] : $raw;
  $title = trim($title);
  if ($title !== '') $highlights[] = $title;
  if (count($highlights) >= 3) break;
}

// First/last “titles” (for the mini range display)
$firstTitle = $highlights[0] ?? null;
$lastTitle  = null;
if ($totalDays > 1) {
  // Try to derive last title from the last “day” row
  $lastRaw = trim((string)($itinerary[$totalDays - 1]['day'] ?? ''));
  if ($lastRaw !== '') {
    $parts = explode(' - ', $lastRaw, 2);
    $lastTitle = isset($parts[1]) ? trim($parts[1]) : $lastRaw;
  }
}

// Build URLs (correct mapping)
$encodedId = base64_encode($quotationId);
$viewUrl   = 'https://' . SITE_DOMAIN . "/view-itinerary/{$encodedId}/";
$pdfUrl    = 'https://' . SITE_DOMAIN . "/download-itinerary/{$encodedId}/";

// Safety
$safeName     = htmlspecialchars($profile_first_name ?? 'Traveler', ENT_QUOTES, 'UTF-8');
$safeTour     = htmlspecialchars((string)($content['title'] ?? 'Your Trip'), ENT_QUOTES, 'UTF-8');
$safeSiteName = htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8');

// Subject + preheader
$subject   = "Your {$safeTour} — Quick Itinerary Overview | " . SITE_NAME;
$preheader = "See the highlights at a glance, then view online or download your itinerary as a PDF.";

include_once 'header.php';

$email_body = "
    <!-- Preheader -->
    <div style='display:none;max-height:0;overflow:hidden;opacity:0;color:transparent'>{$preheader}</div>

    <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='font-family:Arial,sans-serif;border-collapse:collapse'>
      <tr>
        <td style='padding:22px 20px 8px 20px'>
          <p style='margin:0 0 8px 0;font-size:14px;line-height:24px;color:#333'>Dear <strong>{$safeName}</strong>,</p>
          <p style='margin:0;font-size:14px;line-height:24px;color:#333'>
            Thanks for your interest in <strong>{$safeTour}</strong> with <strong>{$safeSiteName}</strong>. Here's a quick overview of your itinerary.
          </p>
        </td>
      </tr>

      <tr>
        <td style='padding:14px 20px 4px 20px'>
          <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #eee;border-radius:8px'>
            <tr>
              <td style='padding:14px 16px'>
                <p style='margin:0 0 6px 0;font-size:16px;line-height:24px;color:#111'><strong>Itinerary at a glance</strong></p>
                <p style='margin:0 0 8px 0;font-size:14px;line-height:22px;color:#333'>
                  " . ($totalDays ? "<strong>{$totalDays} Day" . ($totalDays > 1 ? "s" : "") . "</strong>" : "Multi-day") .
  ($firstTitle && $lastTitle ? " • <em>{$firstTitle}</em> to <em>{$lastTitle}</em>" : "") . "
                </p>"
  . (!empty($highlights) ? "
                <p style='margin:0 0 8px 0;font-size:14px;line-height:22px;color:#333'>
                  <strong>Highlights:</strong> " . htmlspecialchars(implode(', ', $highlights), ENT_QUOTES, 'UTF-8') . "
                </p>" : "") . "
                <p style='margin:0;font-size:13px;line-height:20px;color:#666'>
                  For full day-by-day details (stays, inclusions, timings), use the options below.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- CTAs -->
      <tr>
        <td style='padding:16px 20px 24px 20px'>
          <table role='presentation' cellpadding='0' cellspacing='0'>
            <tr>
              <td style='padding-right:10px'>
                <a href='" . htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') . "'
                   style='display:inline-block;background:#0b5fff;color:#fff;text-decoration:none;font-size:14px;line-height:18px;padding:12px 18px;border-radius:6px'>
                   View Itinerary
                </a>
              </td>
              <td>
                <a href='" . htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') . "'
                   style='display:inline-block;background:#222;color:#fff;text-decoration:none;font-size:14px;line-height:18px;padding:12px 18px;border-radius:6px'>
                   Download PDF
                </a>
              </td>
            </tr>
          </table>
          <p style='margin:14px 0 0 0;font-size:12px;color:#777;line-height:18px'>
            Can't access the buttons? Copy and paste:<br>
            View: " . htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') . "<br>
            Download: " . htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') . "
          </p>
        </td>
      </tr>

      <tr>
        <td style='padding:0 20px 24px 20px'>
          <p style='margin:0;font-size:13px;line-height:20px;color:#666'>
            Need edits or have questions? Reply to this email and our travel specialist will customise the plan for you.
          </p>
        </td>
      </tr>
    </table>
";

include_once 'footer.php';