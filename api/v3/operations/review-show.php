<?php

/**
 * Public Traveler Smart Review & Rating Page
 * Path: /api/v3/operations/review-show.php
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
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya Holidays';

$rawId = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($rawId)) {
    echo '<h1>Review Page</h1><p>Reservation reference missing.</p>';
    exit();
}

$decoded = base64_decode($rawId, true);
$leadId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (is_numeric($rawId) ? (int)$rawId : 0);

if ($leadId <= 0) {
    echo '<h1>Review Page</h1><p>Invalid booking link.</p>';
    exit();
}

$siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
$lRes = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId}{$siteClause} LIMIT 1");
if (!$lRes || mysqli_num_rows($lRes) === 0) {
    echo '<h1>Review Page</h1><p>Booking record not found.</p>';
    exit();
}
$lead = mysqli_fetch_assoc($lRes);

$fName = trim($lead['enquiry_first_name'] ?? '');
$guestName = $fName ?: 'Valued Traveler';
$destination = !empty($lead['enquiry_subject']) ? $lead['enquiry_subject'] : 'Your Holiday Destination';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Experience - <?php echo htmlspecialchars($siteName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand-primary: #1e3a8a;
            --brand-amber: #f59e0b;
            --brand-emerald: #10b981;
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
            background: #f8fafc;
            color: var(--slate-800);
            line-height: 1.5;
            font-size: 14px;
            padding: 30px 16px 60px;
        }

        .review-card {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        .review-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 36px 30px;
            text-align: center;
        }

        .review-header h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .review-header p {
            font-size: 13px;
            color: #cbd5e1;
        }

        .review-body {
            padding: 32px 30px;
        }

        .rating-group {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--slate-100);
            padding-bottom: 18px;
        }

        .rating-group:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .rating-label {
            font-weight: 700;
            font-size: 13.5px;
            color: var(--slate-800);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .rating-label small {
            font-weight: 500;
            color: #64748b;
            font-size: 12px;
        }

        .stars-row {
            display: flex;
            gap: 12px;
            cursor: pointer;
        }

        .star-item {
            font-size: 26px;
            color: #cbd5e1;
            transition: color 0.12s ease, transform 0.12s ease;
        }

        .star-item:hover,
        .star-item.selected {
            color: var(--brand-amber);
            transform: scale(1.15);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
        }

        textarea {
            width: 100%;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 12px 14px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            resize: vertical;
        }

        textarea:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
        }

        .btn-submit-review {
            width: 100%;
            background: var(--brand-primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-submit-review:hover {
            background: #1e40af;
        }

        /* Success & Google Prompt State */
        .celebration-wrap {
            display: none;
            text-align: center;
            padding: 36px 30px;
        }

        .celebration-wrap.active {
            display: block;
        }

        .celeb-icon {
            font-size: 48px;
            color: var(--brand-emerald);
            margin-bottom: 14px;
        }

        .celeb-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--slate-800);
        }

        .celeb-text {
            color: var(--slate-600);
            font-size: 13.5px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .btn-google-review {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-google-review:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="review-card">
        <div class="review-header">
            <h1><?php echo htmlspecialchars($siteName); ?></h1>
            <p>Hi <?php echo htmlspecialchars($guestName); ?>, tell us how was your trip to <?php echo htmlspecialchars($destination); ?>!</p>
        </div>

        <!-- Form View -->
        <div class="review-body" id="reviewFormBody">
            <input type="hidden" id="revLeadId" value="<?php echo base64_encode((string)$leadId); ?>">

            <div class="rating-group" data-category="hotel">
                <div class="rating-label">
                    <span>1. Stays &amp; Hotel Quality</span>
                    <small id="lblRatingHotel">5 / 5 Stars</small>
                </div>
                <div class="stars-row" data-input="rating_hotel">
                    <i class="fa fa-star star-item selected" data-val="1"></i>
                    <i class="fa fa-star star-item selected" data-val="2"></i>
                    <i class="fa fa-star star-item selected" data-val="3"></i>
                    <i class="fa fa-star star-item selected" data-val="4"></i>
                    <i class="fa fa-star star-item selected" data-val="5"></i>
                </div>
            </div>

            <div class="rating-group" data-category="driver">
                <div class="rating-label">
                    <span>2. Chauffeur &amp; Cab Service</span>
                    <small id="lblRatingDriver">5 / 5 Stars</small>
                </div>
                <div class="stars-row" data-input="rating_driver">
                    <i class="fa fa-star star-item selected" data-val="1"></i>
                    <i class="fa fa-star star-item selected" data-val="2"></i>
                    <i class="fa fa-star star-item selected" data-val="3"></i>
                    <i class="fa fa-star star-item selected" data-val="4"></i>
                    <i class="fa fa-star star-item selected" data-val="5"></i>
                </div>
            </div>

            <div class="rating-group" data-category="itinerary">
                <div class="rating-label">
                    <span>3. Sightseeing &amp; Itinerary Flow</span>
                    <small id="lblRatingItin">5 / 5 Stars</small>
                </div>
                <div class="stars-row" data-input="rating_itinerary">
                    <i class="fa fa-star star-item selected" data-val="1"></i>
                    <i class="fa fa-star star-item selected" data-val="2"></i>
                    <i class="fa fa-star star-item selected" data-val="3"></i>
                    <i class="fa fa-star star-item selected" data-val="4"></i>
                    <i class="fa fa-star star-item selected" data-val="5"></i>
                </div>
            </div>

            <div class="rating-group" data-category="overall">
                <div class="rating-label">
                    <span>4. Overall Trip Experience</span>
                    <small id="lblRatingOverall">5 / 5 Stars</small>
                </div>
                <div class="stars-row" data-input="rating_overall">
                    <i class="fa fa-star star-item selected" data-val="1"></i>
                    <i class="fa fa-star star-item selected" data-val="2"></i>
                    <i class="fa fa-star star-item selected" data-val="3"></i>
                    <i class="fa fa-star star-item selected" data-val="4"></i>
                    <i class="fa fa-star star-item selected" data-val="5"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Trip Highlights &amp; Comments</label>
                <textarea id="revComments" rows="3" placeholder="Share what you loved most about this journey..."></textarea>
            </div>

            <button type="button" class="btn-submit-review" id="btnSubmitTravelerReview">
                Submit Review
            </button>
        </div>

        <!-- Success & Google Prompt View -->
        <div class="celebration-wrap" id="reviewCelebrationWrap">
            <div class="celeb-icon"><i class="fa fa-circle-check"></i></div>
            <h2 class="celeb-title">Thank You, <?php echo htmlspecialchars($guestName); ?>!</h2>
            <p class="celeb-text" id="celebText">
                We're delighted you had a memorable journey with <?php echo htmlspecialchars($siteName); ?>! Would you take 10 seconds to share this review on our official Google Maps page?
            </p>
            <div id="googleReviewBtnWrap">
                <a href="https://maps.google.com/?q=<?php echo urlencode($siteName); ?>" target="_blank" class="btn-google-review">
                    <img src="https://www.google.com/favicon.ico" width="18" height="18" alt="Google"> Post Review on Google Maps
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            var ratings = {
                rating_hotel: 5,
                rating_driver: 5,
                rating_itinerary: 5,
                rating_overall: 5
            };

            $('.stars-row').on('click', '.star-item', function() {
                var val = parseInt($(this).data('val'), 10);
                var inputKey = $(this).parent().data('input');
                ratings[inputKey] = val;

                var $row = $(this).parent();
                $row.find('.star-item').each(function() {
                    var sVal = parseInt($(this).data('val'), 10);
                    $(this).toggleClass('selected', sVal <= val);
                });

                if (inputKey === 'rating_hotel') $('#lblRatingHotel').text(val + ' / 5 Stars');
                if (inputKey === 'rating_driver') $('#lblRatingDriver').text(val + ' / 5 Stars');
                if (inputKey === 'rating_itinerary') $('#lblRatingItin').text(val + ' / 5 Stars');
                if (inputKey === 'rating_overall') $('#lblRatingOverall').text(val + ' / 5 Stars');
            });

            $('#btnSubmitTravelerReview').on('click', function() {
                var payload = {
                    action: 'submit_review',
                    lead_id: $('#revLeadId').val(),
                    rating_hotel: ratings.rating_hotel,
                    rating_driver: ratings.rating_driver,
                    rating_itinerary: ratings.rating_itinerary,
                    rating_overall: ratings.rating_overall,
                    comments: $('#revComments').val()
                };

                $(this).prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: '/api/v3/operations/feedback.php?action=submit_review',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(res) {
                        $('#reviewFormBody').hide();
                        $('#reviewCelebrationWrap').addClass('active');

                        if (ratings.rating_overall < 4) {
                            $('#celebText').text('Thank you for sharing your feedback. Our team has received your comments and will work hard to make your next vacation flawless.');
                            $('#googleReviewBtnWrap').hide();
                        }
                    },
                    error: function() {
                        alert('Error submitting review. Please try again.');
                        $('#btnSubmitTravelerReview').prop('disabled', false).text('Submit Review');
                    }
                });
            });
        });
    </script>
</body>
</html>
