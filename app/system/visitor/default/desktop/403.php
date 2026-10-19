<?php
http_response_code(403);
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | <?php echo htmlspecialchars($siteName); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .error-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 48px 32px; max-width: 460px; width: 100%; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .error-code { font-size: 72px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 12px; }
        .error-badge { display: inline-block; background: #fef3c7; color: #b45309; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 12px; border-radius: 100px; margin-bottom: 16px; }
        h1 { font-size: 20px; font-weight: 700; margin-bottom: 8px; color: #0f172a; }
        p { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
        .btn-home { display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <div class="error-badge">Access Restricted</div>
        <h1>Access Denied</h1>
        <p>You do not have permission to view this resource.</p>
        <a href="/" class="btn-home">Return Home</a>
    </div>
</body>
</html>
