<?php
// offline.php
// This page shows when the user is offline
?>
<!DOCTYPE html>
<html lang="<?php echo SITE_LANG; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Offline • <?php echo SITE_NAME; ?></title>

    <!-- Brand Colors -->
    <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR; ?>">

    <!-- App Icon -->
    <link rel="icon" href="<?php echo SITE_ICON; ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo SITE_ICON; ?>">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="/dashboard/assets/app.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
        }

        .offline-box {
            max-width: 400px;
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .offline-box img {
            max-width: 120px;
            margin-bottom: 15px;
        }

        .offline-box h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #222;
        }

        .offline-box p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .offline-box button {
            background: <?php echo SITE_THEME_COLOUR;
                        ?>;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .offline-box button:hover {
            background: #0f3ab5;
        }
    </style>
</head>

<body>
    <div class="offline-box">
        <img src="<?php echo SITE_ICON; ?>" alt="<?php echo SITE_NAME; ?> Logo">
        <h1>You're Offline</h1>
        <p>It looks like you're not connected to the internet.<br>Some features may be unavailable.</p>
        <button onclick="location.reload()">Retry</button>
    </div>
</body>

</html>