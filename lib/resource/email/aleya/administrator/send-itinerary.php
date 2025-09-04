<?php

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