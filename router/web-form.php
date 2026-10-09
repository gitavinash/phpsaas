<?php

/**
 * Public Landing Page Web Form Embed Endpoint
 * Path: /router/web-form.php
 * Handles iframe embedding: /form/{form_uid}/
 */

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$rawUid = '';
if (!empty($urlStructure[1])) {
    $rawUid = trim($urlStructure[1]);
} elseif (!empty($_GET['id'])) {
    $rawUid = trim($_GET['id']);
} elseif (!empty($_GET['uid'])) {
    $rawUid = trim($_GET['uid']);
}

$escUid = mysqli_real_escape_string($connection, $rawUid);
$tableName = TABLE_PREFIX . 'web_form';

$form = null;
if (!empty($escUid)) {
    $q = mysqli_query($connection, "SELECT * FROM `{$tableName}` WHERE (form_uid = '{$escUid}' OR form_slug = '{$escUid}') LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $form = mysqli_fetch_assoc($q);
    }
}

// 404 / Inactive fallback
if (!$form) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Not Found</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #475569; text-align: center; }
            .err-card { padding: 32px 24px; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; max-width: 380px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .err-icon { font-size: 36px; margin-bottom: 12px; }
            h2 { font-size: 18px; margin: 0 0 8px; color: #0f172a; }
            p { font-size: 14px; margin: 0; line-height: 1.5; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="err-card">
            <div class="err-icon">⚠️</div>
            <h2>Form Unavailable</h2>
            <p>The requested form could not be found or is currently inactive. Please contact the administrator.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ((int)$form['form_status'] !== 1) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Temporarily Inactive</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #475569; text-align: center; }
            .err-card { padding: 32px 24px; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; max-width: 380px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            h2 { font-size: 18px; margin: 0 0 8px; color: #0f172a; }
            p { font-size: 14px; margin: 0; line-height: 1.5; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="err-card">
            <h2>Form Inactive</h2>
            <p>This inquiry form is currently not accepting new submissions.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Parse configuration
$fields = json_decode($form['form_fields_json'], true) ?: [];
$tracking = json_decode($form['form_tracking_json'], true) ?: [];

// Allow query params to override tracking (URL UTM parameters)
$source   = !empty($_GET['utm_source']) ? $_GET['utm_source'] : (!empty($tracking['source']) ? $tracking['source'] : 'landing_page');
$medium   = !empty($_GET['utm_medium']) ? $_GET['utm_medium'] : (!empty($tracking['medium']) ? $tracking['medium'] : 'web_form');
$campaign = !empty($_GET['utm_campaign']) ? $_GET['utm_campaign'] : (!empty($tracking['campaign']) ? $tracking['campaign'] : '');
$subject  = !empty($_GET['subject']) ? $_GET['subject'] : (!empty($tracking['subject']) ? $tracking['subject'] : $form['form_title']);

$recaptchaSiteKey = defined('GOOGLE_RECAPTCHA_SITE_KEY') ? GOOGLE_RECAPTCHA_SITE_KEY : '';
$themeColor = !empty($form['form_theme_color']) ? htmlspecialchars($form['form_theme_color']) : '#4f46e5';
$bgMode = $form['form_bg_mode'] === 'transparent' ? 'transparent' : 'card';
$redirectUrl = !empty($form['form_redirect_url']) ? htmlspecialchars($form['form_redirect_url']) : '';
$successMsg = !empty($form['form_success_message']) ? htmlspecialchars($form['form_success_message']) : 'Thank you! We have received your inquiry and will contact you shortly.';
$inputHeight = !empty($form['form_input_height']) ? htmlspecialchars($form['form_input_height']) : '44px';

$currentDomain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$formAction = $currentDomain . '/enquiry/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($form['form_title']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --wf-primary: <?php echo $themeColor; ?>;
            --wf-primary-hover: <?php echo $themeColor; ?>dd;
            --wf-primary-rgb: <?php 
                // Convert hex to rgb
                $hex = ltrim($themeColor, '#');
                if (strlen($hex) == 3) {
                    $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
                    $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
                    $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
                } elseif (strlen($hex) == 6) {
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                } else {
                    $r = 79; $g = 70; $b = 229;
                }
                echo "$r, $g, $b";
            ?>;
            --wf-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --wf-radius: 10px;
            --wf-input-height: <?php echo $inputHeight; ?>;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--wf-font);
            background: <?php echo ($bgMode === 'transparent' ? 'transparent' : '#f8fafc'); ?>;
            color: #1e293b;
            padding: <?php echo ($bgMode === 'transparent' ? '8px' : '16px'); ?>;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .wf-container {
            width: 100%;
            max-width: 640px;
            background: <?php echo ($bgMode === 'transparent' ? 'transparent' : '#ffffff'); ?>;
            <?php if ($bgMode !== 'transparent') { ?>
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            padding: 24px 24px;
            <?php } else { ?>
            padding: 8px 4px;
            <?php } ?>
        }

        .wf-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .wf-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .wf-header p {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.45;
        }

        .wf-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 14px 12px;
        }

        .wf-field-group {
            display: flex;
            flex-direction: column;
        }

        .wf-col-12 { grid-column: span 12; }
        .wf-col-6  { grid-column: span 6; }
        .wf-col-4  { grid-column: span 4; }

        @media (max-width: 520px) {
            .wf-col-6, .wf-col-4 {
                grid-column: span 12;
            }
            .wf-container {
                padding: 18px 16px;
            }
        }

        .wf-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .wf-req {
            color: #ef4444;
            font-weight: 700;
            margin-left: 3px;
        }

        .wf-input-wrap {
            position: relative;
        }

        .wf-input, .wf-select {
            width: 100%;
            height: var(--wf-input-height);
            min-height: var(--wf-input-height);
            line-height: var(--wf-input-height);
            font-family: inherit;
            font-size: 13.5px;
            color: #0f172a;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: var(--wf-radius);
            padding: 0 14px;
            box-sizing: border-box;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }

        .wf-textarea {
            width: 100%;
            min-height: calc(var(--wf-input-height) * 1.85);
            font-family: inherit;
            font-size: 13.5px;
            color: #0f172a;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: var(--wf-radius);
            padding: 10px 14px;
            box-sizing: border-box;
            resize: vertical;
            line-height: 1.45;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }

        .wf-input:hover, .wf-select:hover, .wf-textarea:hover {
            border-color: #94a3b8;
        }

        .wf-input:focus, .wf-select:focus, .wf-textarea:focus {
            border-color: var(--wf-primary);
            box-shadow: 0 0 0 3px rgba(var(--wf-primary-rgb), 0.15);
        }

        .wf-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
            cursor: pointer;
        }

        .wf-submit-wrap {
            grid-column: span 12;
            margin-top: 6px;
        }

        .wf-submit-btn {
            width: 100%;
            height: var(--wf-input-height);
            min-height: var(--wf-input-height);
            background: var(--wf-primary);
            color: #ffffff;
            border: none;
            border-radius: var(--wf-radius);
            padding: 0 20px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(var(--wf-primary-rgb), 0.35);
        }

        .wf-submit-btn:hover {
            background: var(--wf-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(var(--wf-primary-rgb), 0.42);
        }

        .wf-submit-btn:active {
            transform: translateY(0);
        }

        .wf-submit-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .wf-spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: wf-spin 0.8s linear infinite;
        }

        @keyframes wf-spin {
            to { transform: rotate(360deg); }
        }

        .wf-security-badge {
            margin-top: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 7px;
            font-size: 11.5px;
            color: #94a3b8;
        }

        .wf-security-badge svg {
            width: 13px;
            height: 13px;
            fill: #94a3b8;
            flex-shrink: 0;
            vertical-align: middle;
        }

        .wf-badge-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .wf-badge-sep {
            color: #cbd5e1;
            font-size: 11px;
        }

        .wf-badge-link {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .wf-badge-link:hover {
            color: var(--wf-primary);
            text-decoration: underline;
        }

        /* Inline Error Alert */
        .wf-alert {
            display: none;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 14px;
            line-height: 1.4;
        }
        .wf-alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Success Animation Screen */
        .wf-success-card {
            display: none;
            text-align: center;
            padding: 36px 18px;
            animation: wf-fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes wf-fadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
        .wf-success-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            border: 2px solid #a7f3d0;
        }
        .wf-success-icon-wrap svg {
            width: 32px;
            height: 32px;
            stroke: #10b981;
            stroke-width: 2.5;
            fill: none;
        }
        .wf-success-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .wf-success-card p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            max-width: 440px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="wf-container">
    <div id="wfFormWrapper">
        <?php 
        $hideHeading = !empty($form['form_hide_heading']);
        $hideSubheading = !empty($form['form_hide_subheading']);
        $showHeading = !$hideHeading && !empty($form['form_heading']);
        $showSubheading = !$hideSubheading && !empty($form['form_subheading']);
        if ($showHeading || $showSubheading) { ?>
        <div class="wf-header">
            <?php if ($showHeading) { ?>
                <h2><?php echo htmlspecialchars($form['form_heading']); ?></h2>
            <?php } ?>
            <?php if ($showSubheading) { ?>
                <p><?php echo htmlspecialchars($form['form_subheading']); ?></p>
            <?php } ?>
        </div>
        <?php } ?>

        <div id="wfAlert" class="wf-alert wf-alert-error"></div>

        <form id="wfPublicForm" action="<?php echo $formAction; ?>" method="POST">
            <!-- Hidden Core Architecture Inputs -->
            <input type="hidden" name="type" value="enquiry">
            <input type="hidden" name="action" value="new">
            <input type="hidden" name="form_uid" value="<?php echo htmlspecialchars($form['form_uid']); ?>">
            <input type="hidden" name="format" value="json">
            <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="">

            <!-- Tracking Attribution Inputs -->
            <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
            <input type="hidden" name="medium" value="<?php echo htmlspecialchars($medium); ?>">
            <input type="hidden" name="campaign" value="<?php echo htmlspecialchars($campaign); ?>">
            <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
            <?php if (!empty($redirectUrl)) { ?>
            <input type="hidden" name="redirect_url" value="<?php echo $redirectUrl; ?>">
            <?php } ?>

            <div class="wf-grid">
                <?php
                foreach ($fields as $field) {
                    if (empty($field['enabled'])) continue;

                    $key = htmlspecialchars($field['field_key'] ?? '');
                    $label = htmlspecialchars($field['label'] ?? '');
                    $placeholder = htmlspecialchars($field['placeholder'] ?? '');
                    $type = $field['type'] ?? 'text';
                    $required = !empty($field['required']);
                    $colSpan = !empty($field['col_span']) ? (int)$field['col_span'] : 12;
                    $colClass = 'wf-col-' . $colSpan;
                    $reqHtml = $required ? '<span class="wf-req">*</span>' : '';
                    $fieldHeight = (!empty($field['input_height']) && $field['input_height'] !== 'default') ? htmlspecialchars($field['input_height']) : '';
                    $hStyle = $fieldHeight ? ' style="height:' . $fieldHeight . ';min-height:' . $fieldHeight . ';line-height:' . $fieldHeight . ';"' : '';
                    $taHStyle = $fieldHeight ? ' style="min-height:calc(' . $fieldHeight . ' * 1.85);"' : '';
                    ?>
                    <div class="wf-field-group <?php echo $colClass; ?>">
                        <label for="wf_<?php echo $key; ?>" class="wf-label">
                            <span><?php echo $label . $reqHtml; ?></span>
                        </label>
                        <div class="wf-input-wrap">
                            <?php if ($type === 'textarea') { ?>
                                <textarea 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-textarea" 
                                    <?php echo $taHStyle; ?>
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>></textarea>
                            <?php } elseif ($type === 'select') { 
                                $options = $field['options'] ?? [];
                                ?>
                                <select 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-select" 
                                    <?php echo $hStyle; ?>
                                    <?php echo $required ? 'required' : ''; ?>>
                                    <?php if (!empty($placeholder)) { ?>
                                        <option value="" disabled selected><?php echo $placeholder; ?></option>
                                    <?php } ?>
                                    <?php foreach ($options as $opt) { ?>
                                        <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($type === 'date') { ?>
                                <input 
                                    type="date" 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-input" 
                                    <?php echo $hStyle; ?>
                                    min="<?php echo date('Y-m-d'); ?>"
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>>
                            <?php } elseif ($type === 'tel') { ?>
                                <input 
                                    type="tel" 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-input" 
                                    <?php echo $hStyle; ?>
                                    pattern="[0-9+ -]{7,15}"
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>>
                            <?php } elseif ($type === 'email') { ?>
                                <input 
                                    type="email" 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-input" 
                                    <?php echo $hStyle; ?>
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>>
                            <?php } elseif ($type === 'number') { ?>
                                <input 
                                    type="number" 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-input" 
                                    <?php echo $hStyle; ?>
                                    min="0"
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>>
                            <?php } else { ?>
                                <input 
                                    type="text" 
                                    name="<?php echo $key; ?>" 
                                    id="wf_<?php echo $key; ?>" 
                                    class="wf-input" 
                                    <?php echo $hStyle; ?>
                                    placeholder="<?php echo $placeholder; ?>" 
                                    <?php echo $required ? 'required' : ''; ?>>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="wf-submit-wrap">
                    <button type="submit" id="wfSubmitBtn" class="wf-submit-btn">
                        <span class="wf-spinner" id="wfSpinner"></span>
                        <span id="wfBtnText"><?php echo htmlspecialchars($form['form_submit_btn_text'] ?? 'Get Free Quote'); ?></span>
                    </button>
                    <div class="wf-security-badge">
                        <span class="wf-badge-item">
                            <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                            <span>Protected by reCAPTCHA v3</span>
                        </span>
                        <span class="wf-badge-sep">&bull;</span>
                        <span class="wf-badge-item">
                            <span>Powered by</span>
                            <a href="https://aleya.in" target="_blank" rel="noopener" class="wf-badge-link">Aleya.in</a>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Success Feedback View -->
    <div id="wfSuccessCard" class="wf-success-card">
        <div class="wf-success-icon-wrap">
            <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3>Request Received!</h3>
        <p><?php echo $successMsg; ?></p>
    </div>
</div>

<?php if (!empty($recaptchaSiteKey)) { ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptchaSiteKey); ?>"></script>
<?php } ?>

<script>
(function() {
    var form = document.getElementById('wfPublicForm');
    var submitBtn = document.getElementById('wfSubmitBtn');
    var btnText = document.getElementById('wfBtnText');
    var spinner = document.getElementById('wfSpinner');
    var alertBox = document.getElementById('wfAlert');
    var formWrapper = document.getElementById('wfFormWrapper');
    var successCard = document.getElementById('wfSuccessCard');
    var recaptchaSiteKey = <?php echo json_encode($recaptchaSiteKey); ?>;
    var redirectUrl = <?php echo json_encode($redirectUrl); ?>;

    function showAlert(msg) {
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
    }

    function hideAlert() {
        alertBox.textContent = '';
        alertBox.style.display = 'none';
    }

    function setLoading(isLoading) {
        submitBtn.disabled = isLoading;
        if (isLoading) {
            spinner.style.display = 'inline-block';
            btnText.style.opacity = '0.85';
        } else {
            spinner.style.display = 'none';
            btnText.style.opacity = '1';
        }
    }

    function doSubmit() {
        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) {
            return res.json().catch(function() {
                // If response is not JSON, check status
                if (res.ok) {
                    return { status: 'success' };
                }
                return { status: 'error', msg: 'Submission encountered an issue. Please try again.' };
            });
        })
        .then(function(data) {
            setLoading(false);
            if (data.status === 'success' || (data.status && data.status.toString() === '200')) {
                // If redirect URL provided
                var targetRedirect = data.redirect_url || redirectUrl;
                if (targetRedirect && targetRedirect.length > 3) {
                    window.top.location.href = targetRedirect;
                    return;
                }
                // Show in-place animated success screen
                formWrapper.style.display = 'none';
                successCard.style.display = 'block';
            } else {
                showAlert(data.msg || 'Unable to submit enquiry. Please check your inputs.');
            }
        })
        .catch(function(err) {
            setLoading(false);
            showAlert('Network connection error. Please try again.');
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        hideAlert();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        setLoading(true);

        if (recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function() {
                grecaptcha.execute(recaptchaSiteKey, { action: 'submit' })
                .then(function(token) {
                    document.getElementById('recaptcha_token').value = token;
                    doSubmit();
                })
                .catch(function(err) {
                    console.warn('reCAPTCHA error: ', err);
                    // Fallback proceed if recaptcha script fails
                    doSubmit();
                });
            });
        } else {
            doSubmit();
        }
    });
})();
</script>

</body>
</html>
