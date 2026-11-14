<?php

/**
 * Public & Client-Facing Travel Voucher View / Print Template
 * Path: /api/v3/operations/voucher-show.php
 */

header("Content-Type: text/html; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : 0;
$siteDomain = SITE_URL;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya Holidays';

$rawId = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($rawId)) {
    echo '<h1>Invalid Voucher Link</h1><p>Voucher reference identifier is missing.</p>';
    exit();
}

$decoded = base64_decode($rawId, true);
$leadId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (is_numeric($rawId) ? (int)$rawId : 0);

if ($leadId <= 0) {
    echo '<h1>Voucher Not Found</h1><p>The requested travel voucher could not be located.</p>';
    exit();
}

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$lRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteClause} LIMIT 1");
if (!$lRes || mysqli_num_rows($lRes) === 0) {
    echo '<h1>Voucher Not Found</h1><p>This travel reservation has not been generated or does not exist.</p>';
    exit();
}
$lead = mysqli_fetch_assoc($lRes);

// Customer details
$fName = trim($lead['enquiry_first_name'] ?? '');
$lName = trim($lead['enquiry_last_name'] ?? '');
$guestName = trim("{$fName} {$lName}");
if (empty($guestName)) $guestName = 'Valued Traveler';

$guestMobile = trim($lead['enquiry_mobile'] ?? '');
$guestEmail  = trim($lead['enquiry_email'] ?? '');
$destination = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Custom Tour Package';

// Parse travel dates & pax from message
$extra = [];
if (!empty($lead['enquiry_message'])) {
    $p = json_decode($lead['enquiry_message'], true);
    if (is_array($p)) $extra = $p;
}

$checkIn  = !empty($extra['travel_date']) ? $extra['travel_date'] : (!empty($extra['departure_date']) ? $extra['departure_date'] : 'On Schedule');
$checkOut = !empty($extra['return_date']) ? $extra['return_date'] : 'On Schedule';
$paxAdults = isset($extra['adults']) ? (int)$extra['adults'] : (isset($extra['pax']) ? (int)$extra['pax'] : 2);
$paxChildren = isset($extra['children']) ? (int)$extra['children'] : 0;

// Fetch booked services from isw_booking
$bookings = [];
$bRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}booking WHERE booking_enquiry_id = {$leadId} ORDER BY booking_id ASC");
if ($bRes) {
    while ($bRow = mysqli_fetch_assoc($bRes)) {
        $details = !empty($bRow['booking_details']) ? json_decode($bRow['booking_details'], true) : [];
        $bookings[] = [
            'id'          => (int)$bRow['booking_id'],
            'service'     => strtolower(trim($bRow['booking_service'] ?? 'service')),
            'status'      => (int)$bRow['booking_status'],
            'details'     => $details,
            'ref_id'      => $details['confirmation_ref'] ?? '',
            'vendor_name' => $details['vendor_name'] ?? 'Confirmed Partner'
        ];
    }
}

$voucherNo = 'VCH-' . date('Y') . '-' . str_pad($leadId, 5, '0', STR_PAD_LEFT);
$issueDate = date('d M Y');
$autoDownload = (isset($_GET['auto_download']) && $_GET['auto_download'] === 'true') || (isset($_GET['download']) && $_GET['download'] === 'true');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Voucher - <?php echo htmlspecialchars($guestName); ?> - <?php echo htmlspecialchars($voucherNo); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <style>
        :root {
            --brand-primary: #1e3a8a;
            --brand-accent: #0284c7;
            --emerald: #059669;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-600: #475569;
            --slate-800: #1e293b;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: var(--slate-800);
            line-height: 1.5;
            font-size: 13.5px;
            padding: 24px 16px 60px;
        }

        .voucher-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        /* Top Action Bar */
        .voucher-actions-bar {
            max-width: 860px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-vch-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid var(--slate-200);
            background: #ffffff;
            color: var(--slate-800);
            transition: all 0.15s ease;
        }

        .btn-vch-action:hover {
            background: var(--slate-100);
        }

        .btn-vch-action.primary {
            background: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
        }

        .btn-vch-action.whatsapp {
            background: #25d366;
            color: #ffffff;
            border-color: #25d366;
        }

        /* Voucher Header */
        .voucher-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 32px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .voucher-brand-info h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .voucher-brand-info p {
            font-size: 12px;
            color: #94a3b8;
        }

        .voucher-badge-box {
            text-align: right;
        }

        .voucher-title-tag {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #38bdf8;
            margin-bottom: 6px;
        }

        .voucher-number {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .voucher-date {
            font-size: 11.5px;
            color: #cbd5e1;
        }

        /* Guest & Summary Bar */
        .voucher-summary-grid {
            background: var(--slate-50);
            border-bottom: 1px solid var(--slate-200);
            padding: 20px 36px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        @media (max-width: 700px) {
            .voucher-summary-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 16px 20px;
            }
            .voucher-header {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .voucher-badge-box {
                text-align: left;
            }
        }

        .summary-item label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--slate-600);
            margin-bottom: 2px;
        }

        .summary-item span {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--slate-800);
        }

        /* Voucher Content Body */
        .voucher-body {
            padding: 32px 36px;
        }

        @media (max-width: 700px) {
            .voucher-body {
                padding: 20px;
            }
        }

        .voucher-section-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-800);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 24px 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--slate-100);
        }

        .voucher-section-title:first-child {
            margin-top: 0;
        }

        .voucher-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 14px;
            transition: all 0.15s ease;
        }

        .voucher-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .voucher-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--brand-primary);
        }

        .voucher-ref-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 700;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .voucher-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px 16px;
            font-size: 12.5px;
        }

        .detail-row label {
            display: block;
            font-size: 11px;
            color: #64748b;
        }

        .detail-row strong {
            font-weight: 600;
            color: var(--slate-800);
        }

        /* Guidelines & Terms */
        .voucher-guidelines {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 16px 20px;
            margin-top: 28px;
            font-size: 12px;
            color: #92400e;
        }

        .voucher-guidelines h4 {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .voucher-guidelines ul {
            padding-left: 18px;
            line-height: 1.6;
        }

        /* Voucher Footer */
        .voucher-footer {
            background: var(--slate-50);
            border-top: 1px solid var(--slate-200);
            padding: 20px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .voucher-actions-bar {
                display: none !important;
            }
            .voucher-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .voucher-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="voucher-actions-bar">
        <div>
            <a href="/dashboard/operations/" class="btn-vch-action">
                <i class="fi fi-rr-arrow-left"></i> Return to Operations
            </a>
        </div>
        <div style="display:flex; gap:10px;">
            <?php
            $waShareMsg = "Dear " . $guestName . ", please find your official Travel Confirmation Voucher from " . $siteName . ":\n" . $siteDomain . "/view-voucher/" . base64_encode((string)$leadId) . "/";
            $waShareUrl = "https://wa.me/" . (strlen(preg_replace('/[^0-9]/', '', $guestMobile)) === 10 ? '91' . preg_replace('/[^0-9]/', '', $guestMobile) : preg_replace('/[^0-9]/', '', $guestMobile)) . "?text=" . rawurlencode($waShareMsg);
            ?>
            <a href="<?php echo $waShareUrl; ?>" target="_blank" class="btn-vch-action whatsapp">
                <i class="fi fi-brands-whatsapp"></i> Share on WhatsApp
            </a>
            <button type="button" class="btn-vch-action primary" onclick="window.print()">
                <i class="fi fi-rr-print"></i> Print / Download PDF
            </button>
        </div>
    </div>

    <!-- Main Voucher Document -->
    <div class="voucher-container">
        <!-- Header -->
        <div class="voucher-header">
            <div class="voucher-brand-info">
                <h1><?php echo htmlspecialchars($siteName); ?></h1>
                <p>Official Travel Reservation &amp; Confirmation Certificate</p>
            </div>
            <div class="voucher-badge-box">
                <span class="voucher-title-tag">Confirmed Voucher</span>
                <div class="voucher-number"><?php echo htmlspecialchars($voucherNo); ?></div>
                <div class="voucher-date">Issue Date: <?php echo htmlspecialchars($issueDate); ?></div>
            </div>
        </div>

        <!-- Summary Bar -->
        <div class="voucher-summary-grid">
            <div class="summary-item">
                <label>Primary Traveler</label>
                <span><?php echo htmlspecialchars($guestName); ?></span>
            </div>
            <div class="summary-item">
                <label>Destination / Package</label>
                <span><?php echo htmlspecialchars($destination); ?></span>
            </div>
            <div class="summary-item">
                <label>Travel Dates</label>
                <span><?php echo htmlspecialchars($checkIn . ' to ' . $checkOut); ?></span>
            </div>
            <div class="summary-item">
                <label>Total Guests</label>
                <span><?php echo (int)$paxAdults; ?> Adults<?php echo $paxChildren > 0 ? ', ' . (int)$paxChildren . ' Children' : ''; ?></span>
            </div>
        </div>

        <!-- Voucher Body -->
        <div class="voucher-body">
            <!-- 1. ACCOMMODATION / HOTELS -->
            <h3 class="voucher-section-title">
                <i class="fi fi-rr-bed"></i> Hotel &amp; Resort Reservations
            </h3>

            <?php
            $hasHotels = false;
            foreach ($bookings as $b) {
                if ($b['service'] === 'hotel') {
                    $hasHotels = true;
                    $d = $b['details'];
                    ?>
                    <div class="voucher-card">
                        <div class="voucher-card-header">
                            <div class="voucher-card-title"><?php echo htmlspecialchars($b['vendor_name']); ?></div>
                            <div class="voucher-ref-badge">
                                <i class="fi fi-rr-check-circle"></i>
                                Booking Ref: <?php echo htmlspecialchars(!empty($b['ref_id']) ? $b['ref_id'] : 'CONFIRMED'); ?>
                            </div>
                        </div>
                        <div class="voucher-details-grid">
                            <div class="detail-row">
                                <label>Check-In Date</label>
                                <strong><?php echo htmlspecialchars($d['check_in'] ?? $checkIn); ?></strong>
                            </div>
                            <div class="detail-row">
                                <label>Check-Out Date</label>
                                <strong><?php echo htmlspecialchars($d['check_out'] ?? $checkOut); ?></strong>
                            </div>
                            <div class="detail-row">
                                <label>Room Category</label>
                                <strong><?php echo htmlspecialchars($d['room_category'] ?? 'Standard'); ?> (<?php echo (int)($d['room_count'] ?? 1); ?> Rooms)</strong>
                            </div>
                            <div class="detail-row">
                                <label>Meal Plan</label>
                                <strong><?php echo htmlspecialchars($d['meal_plan'] ?? 'CP (Breakfast)'); ?></strong>
                            </div>
                            <?php if (!empty($d['special_notes'])) { ?>
                            <div class="detail-row" style="grid-column: 1 / -1;">
                                <label>Special Remarks</label>
                                <strong><?php echo htmlspecialchars($d['special_notes']); ?></strong>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            }
            if (!$hasHotels) {
                echo '<div class="voucher-card"><p class="text-muted">Direct hotel vouchers as per customized smart itinerary.</p></div>';
            }
            ?>

            <!-- 2. TRANSPORT & LOGISTICS -->
            <h3 class="voucher-section-title">
                <i class="fi fi-rr-car"></i> Transport, Driver &amp; Transfers
            </h3>

            <?php
            $hasTransport = false;
            foreach ($bookings as $b) {
                if ($b['service'] === 'transport') {
                    $hasTransport = true;
                    $d = $b['details'];
                    ?>
                    <div class="voucher-card">
                        <div class="voucher-card-header">
                            <div class="voucher-card-title"><?php echo htmlspecialchars($b['vendor_name']); ?></div>
                            <div class="voucher-ref-badge">
                                <i class="fi fi-rr-check-circle"></i>
                                Transport Ref: <?php echo htmlspecialchars(!empty($b['ref_id']) ? $b['ref_id'] : 'ALLOCATED'); ?>
                            </div>
                        </div>
                        <div class="voucher-details-grid">
                            <div class="detail-row">
                                <label>Vehicle Model / Fleet</label>
                                <strong><?php echo htmlspecialchars($d['vehicle_type'] ?? 'Sedan / SUV'); ?></strong>
                            </div>
                            <div class="detail-row">
                                <label>Vehicle Registration Number</label>
                                <strong><?php echo htmlspecialchars(!empty($d['vehicle_no']) ? $d['vehicle_no'] : 'Provided 12h prior'); ?></strong>
                            </div>
                            <div class="detail-row">
                                <label>Driver / Chauffeur Contact</label>
                                <strong><?php echo htmlspecialchars(!empty($d['driver_phone']) ? $d['driver_phone'] : 'Provided on departure eve'); ?></strong>
                            </div>
                            <div class="detail-row">
                                <label>Service Type</label>
                                <strong>Private Tour &amp; Dedicated Sightseeing</strong>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            if (!$hasTransport) {
                echo '<div class="voucher-card"><p class="text-muted">Dedicated cab and airport/station transfer included as per package specifications.</p></div>';
            }
            ?>

            <!-- Important Guidelines -->
            <div class="voucher-guidelines">
                <h4><i class="fi fi-rr-info"></i> Important Travel Instructions:</h4>
                <ul>
                    <li>All adult guests must carry original government-issued photo ID (Aadhaar / Passport / Voter ID) for hotel check-in.</li>
                    <li>Standard hotel check-in time is 12:00 PM / 02:00 PM and check-out is 10:00 AM / 11:00 AM.</li>
                    <li>Driver contact details are re-verified and shared on WhatsApp on the evening prior to departure.</li>
                    <li>For 24/7 on-tour emergency support, please contact the dedicated operations helpline at <strong>+91 98765 43210</strong>.</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="voucher-footer">
            <div>
                <strong><?php echo htmlspecialchars($siteName); ?> Operations Desk</strong><br>
                Email: support@aleyaholidays.com | Helpline: +91 98765 43210
            </div>
            <div>
                Verified Electronic Voucher &bull; No physical signature required.
            </div>
        </div>
    </div>

    <?php if ($autoDownload) { ?>
    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>
    <?php } ?>
</body>
</html>
