<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Offline | <?php echo SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts (Optional) -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
    :root {
        --brand-700: <?php echo SITE_THEME_COLOUR ?>;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html,
    body {
        height: 100%;
        width: 100%;
        font-family: "Fira Sans", sans-serif;
        background: #f6f6f6;
        overflow: hidden;
        /* Prevent scrolling */
    }

    .offline-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #f6f6f6;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .offline-box {
        max-width: 400px;
        width: 100%;
        text-align: center;
        /* background: #fff; */
        padding: 30px 20px 20px;
        border-radius: 12px;
        /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 420px;
    }

    .offline-box img {
        max-width: 120px;
        margin: 0 auto 15px;
    }

    .offline-box h1 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #222;
    }

    .offline-box p {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #555;
    }

    .offline-box button {
        background: var(--brand-700);
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

    .powered-by {
        font-size: 0.85rem;
        color: #888;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .powered-by a {
        color: #167347;
        text-decoration: none;
        font-weight: 500;
    }

    .powered-by a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="offline-wrapper">
        <div class="offline-box">
            <img src="<?php echo SITE_ICON; ?>" alt="<?php echo SITE_NAME; ?> Logo">
            <h1>You're Offline</h1>
            <p>It looks like you're not connected to the internet.<br>Some features may be unavailable.</p>
            <button onclick="location.reload()">Retry</button>
            <div class="powered-by">
                Powered by <a href="https://blumox.com" target="_blank">Blumox Technologies</a>
            </div>
        </div>
    </div>
</body>

</html>