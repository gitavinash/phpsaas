<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>You’re Offline</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: <?php echo SITE_THEME_COLOUR ?>;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #fff;
        text-align: center;
        padding: 20px;
        line-height: 1.5;
    }

    .icon {
        font-size: 80px;
        margin-bottom: 20px;
        animation: pulse 1.5s infinite ease-in-out;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.6;
            transform: scale(1.05);
        }
    }

    h1 {
        font-size: 2.2em;
        margin-bottom: 10px;
    }

    p {
        font-size: 1.1em;
        max-width: 400px;
    }

    .retry-button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #ffffff;
        color: <?php echo SITE_THEME_COLOUR ?>;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .retry-button:hover {
        background-color: #f0f0f0;
    }

    footer {
        position: absolute;
        bottom: 10px;
        font-size: 0.9em;
        color: #ccc;
    }

    footer a {
        font-weight: bold;
        color: #fff;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="icon" role="img" aria-label="No connection">📡</div>
    <h1>You’re Offline</h1>
    <p>It looks like your internet connection was lost. Please check your network settings and try again.</p>
    <button class="retry-button" onclick="manualRetry()">Retry</button>

    <footer>
        Powered by <a href="https://aleya.in/" target="_blank" rel="noopener noreferrer">aleya.in</a>
    </footer>

    <script>
    // Auto-redirect back if connection returns
    function autoReconnect() {
        fetch("/", {
                method: "HEAD",
                cache: "no-store"
            })
            .then(() => {
                window.location.href = "/";
            })
            .catch(() => {
                setTimeout(autoReconnect, 5000); // retry every 5s
            });
    }

    // Retry button also tries to reconnect
    function manualRetry() {
        fetch("/", {
                method: "HEAD",
                cache: "no-store"
            })
            .then(() => window.location.href = "/")
            .catch(() => alert("Still offline. Please check your connection."));
    }

    autoReconnect(); // Start auto reconnection check
    </script>
</body>

</html>
<?php exit(); ?>