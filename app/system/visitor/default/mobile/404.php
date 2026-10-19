<?php
http_response_code(404);
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?php echo htmlspecialchars($siteName); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px; }
        .error-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 36px 20px; max-width: 360px; width: 100%; text-align: center; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
        .error-code { font-size: 56px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 8px; }
        .error-badge { display: inline-block; background: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 10px; border-radius: 100px; margin-bottom: 12px; }
        h1 { font-size: 18px; font-weight: 700; margin-bottom: 6px; color: #0f172a; }
        p { font-size: 13px; color: #64748b; margin-bottom: 20px; line-height: 1.45; }
        .btn-home { display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; width: 100%; box-sizing: border-box; }
        .btn-home:hover { background: #334155; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">404</div>
        <div class="error-badge">Page Not Found</div>
        <h1>Lost in Exploration?</h1>
        <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="/" class="btn-home">Return Home</a>
    </div>
</body>
</html>
