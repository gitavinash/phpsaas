<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cartt - Coming Soon | E-Commerce Store Builder</title>
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-x: hidden;
    }

    .coming-soon-container {
        width: 100%;
        padding: 20px;
        text-align: center;
        color: white;
    }

    .coming-soon-content {
        max-width: 800px;
        margin: 0 auto;
        animation: fadeInUp 1s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .coming-soon-icon {
        font-size: 5rem;
        margin-bottom: 20px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .coming-soon-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        line-height: 1.2;
    }

    .coming-soon-subtitle {
        font-size: 1.5rem;
        margin-bottom: 30px;
        opacity: 0.95;
        font-weight: 300;
        letter-spacing: 0.5px;
    }

    .coming-soon-description {
        font-size: 1.1rem;
        margin-bottom: 40px;
        opacity: 0.9;
        line-height: 1.6;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .countdown {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 50px;
        flex-wrap: wrap;
    }

    .countdown-item {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 20px 25px;
        border-radius: 10px;
        min-width: 100px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: slideIn 0.8s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .countdown-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
    }

    .countdown-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }

    .subscription-form {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }

    .subscription-form input {
        padding: 14px 20px;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        min-width: 300px;
        background: rgba(255, 255, 255, 0.95);
        color: #333;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .subscription-form input:focus {
        outline: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    .subscription-form input::placeholder {
        color: #999;
    }

    .subscription-form button {
        padding: 14px 40px;
        background: #ff6b6b;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .subscription-form button:hover {
        background: #ff5252;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .subscription-form button:active {
        transform: translateY(0);
    }

    .social-links {
        margin-top: 40px;
    }

    .social-links a {
        display: inline-block;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        line-height: 50px;
        color: white;
        margin: 0 10px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 1.2rem;
    }

    .social-links a:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-5px);
        color: #ff6b6b;
    }

    .background-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .shape {
        position: absolute;
        opacity: 0.1;
    }

    .circle-1 {
        width: 300px;
        height: 300px;
        background: white;
        border-radius: 50%;
        top: -50px;
        right: -50px;
        animation: moveShape 20s ease-in-out infinite;
    }

    .circle-2 {
        width: 200px;
        height: 200px;
        background: white;
        border-radius: 50%;
        bottom: -50px;
        left: -50px;
        animation: moveShapeReverse 25s ease-in-out infinite;
    }

    @keyframes moveShape {

        0%,
        100% {
            transform: translate(0, 0);
        }

        50% {
            transform: translate(20px, -20px);
        }
    }

    @keyframes moveShapeReverse {

        0%,
        100% {
            transform: translate(0, 0);
        }

        50% {
            transform: translate(-20px, 20px);
        }
    }

    .notification-message {
        margin-top: 30px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1);
        border-left: 4px solid #ff6b6b;
        border-radius: 5px;
        backdrop-filter: blur(10px);
        display: none;
        animation: slideInNotification 0.5s ease-out;
    }

    .notification-message.show {
        display: block;
    }

    @keyframes slideInNotification {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .notification-message.success {
        border-left-color: #51cf66;
    }

    .notification-message.success::before {
        content: '✓ ';
        color: #51cf66;
        font-weight: bold;
    }

    .notification-message.error::before {
        content: '✕ ';
        color: #ff6b6b;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .coming-soon-title {
            font-size: 2.5rem;
        }

        .coming-soon-subtitle {
            font-size: 1.2rem;
        }

        .coming-soon-icon {
            font-size: 3.5rem;
        }

        .subscription-form input {
            min-width: 100%;
        }

        .countdown {
            gap: 10px;
        }

        .countdown-item {
            padding: 15px 20px;
            min-width: 80px;
        }

        .countdown-number {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .coming-soon-title {
            font-size: 2rem;
        }

        .coming-soon-subtitle {
            font-size: 1rem;
        }

        .coming-soon-icon {
            font-size: 2.5rem;
        }

        .countdown {
            gap: 8px;
        }

        .countdown-item {
            padding: 12px 15px;
            min-width: 70px;
        }

        .countdown-number {
            font-size: 1.3rem;
        }

        .countdown-label {
            font-size: 0.75rem;
        }
    }
    </style>
</head>

<body>
    <div class="background-shapes">
        <div class="shape circle-1"></div>
        <div class="shape circle-2"></div>
    </div>

    <div class="coming-soon-container">
        <div class="coming-soon-content">
            <div class="coming-soon-icon">
                <i class="fas fa-rocket"></i>
            </div>

            <h1 class="coming-soon-title">Cartt</h1>

            <p class="coming-soon-subtitle">Build Your Store. Grow Your Business. Sell Your Way.</p>

            <p class="coming-soon-description">
                Cartt is the all-in-one e-commerce platform designed for entrepreneurs and businesses of all sizes.
                Create a stunning online store, manage inventory, process payments, and scale your business—all without
                coding. Get notified when we launch and be among the first to start selling!
            </p>

            <div class="countdown">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">00</span>
                    <span class="countdown-label">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">00</span>
                    <span class="countdown-label">Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">00</span>
                    <span class="countdown-label">Minutes</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">00</span>
                    <span class="countdown-label">Seconds</span>
                </div>
            </div>

            <div class="notification-message" id="notificationMessage"></div>

            <div style="margin-top: 50px; font-size: 0.9rem; opacity: 0.8;">
                <a href="/privacy-policy/" title="Privacy Policy" rel="noopener noreferrer"
                    style="color: white; text-decoration: none; margin: 0 15px;">Privacy
                    Policy</a>
                <a href="/terms-of-service/" title="Terms of Use" rel="noopener noreferrer"
                    style="color: white; text-decoration: none; margin: 0 15px;">Terms of
                    Use</a>
                <a href="tel:+91-9990026008" title="Contact Us" rel="noopener noreferrer"
                    style="color: white; text-decoration: none; margin: 0 15px;">Contact Us</a>
            </div>

            <script>
            document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const email = document.getElementById('emailInput').value;
                const notification = document.getElementById('notificationMessage');

                if (email) {
                    notification.textContent = 'Thanks for subscribing!';
                    notification.className = 'notification-message show success';
                    document.getElementById('emailInput').value = '';
                    setTimeout(() => notification.classList.remove('show'), 5000);
                }
            });
            </script>
        </div>
    </div>

    <script>
    // Countdown Timer
    function updateCountdown() {
        const launchDate = new Date('2026-01-31').getTime();
        const now = new Date().getTime();
        const difference = launchDate - now;

        if (difference > 0) {
            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }
    }

    // Update countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call
    </script>

    <div
        style="position: fixed; bottom: 20px; right: 20px; font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); text-align: right;">
        <p style="margin: 0;">Powered by <a href="https://blumox.com" target="_blank" rel="noopener noreferrer"
                style="color: rgba(255, 255, 255, 0.9); text-decoration: none; font-weight: 600; transition: color 0.3s ease;">Blumox
                Technologies</a></p>
    </div>
</body>

</html>