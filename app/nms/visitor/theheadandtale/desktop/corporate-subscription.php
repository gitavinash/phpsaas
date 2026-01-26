<?php

require_once LOGIN_CHECK;
require_once 'header.php';

?><style>
:root {
    --bg: #f5f6f8;
    --card: #ffffff;
    --accent: #ec1816;
    --text: #1d1d1f;
    --muted: #6b6b70;
    --line: #e6e7ec;
}

.corporate-subscription {
    padding: 80px 0 96px;
    background: linear-gradient(180deg, #fdf7f5 0%, #f5f6f8 48%, #f5f6f8 100%);
}

.corporate-subscription .container {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 20px;
}

.hero {
    text-align: center;
    margin-bottom: 52px;
}

.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: #fff;
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.4px;
    color: var(--muted);
}

.hero h1 {
    margin: 16px 0 12px;
    font-family: "Geologica", sans-serif;
    font-size: 50px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.6px;
}

.hero p {
    font-family: "Noto Sans", sans-serif;
    font-size: 17px;
    color: var(--muted);
    max-width: 760px;
    margin: 0 auto;
    line-height: 1.7;
}

.hero-meta {
    display: flex;
    justify-content: center;
    gap: 14px;
    margin-top: 18px;
    flex-wrap: wrap;
}

.meta-chip {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid var(--line);
    background: #fff;
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    color: var(--muted);
}

.layout {
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 26px;
}

.card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 14px;
    box-shadow: 0 14px 38px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-header {
    padding: 20px 24px 12px;
    border-bottom: 1px solid var(--line);
}

.card-header h3 {
    margin: 0;
    font-family: "Geologica", sans-serif;
    font-size: 22px;
    font-weight: 750;
    color: var(--text);
    letter-spacing: -0.2px;
}

.card-body {
    padding: 22px 24px 26px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 12px;
}

.input-label {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.3px;
    color: var(--text);
}

.license-input {
    width: 100%;
    padding: 18px;
    font-family: "Geologica", sans-serif;
    font-size: 34px;
    font-weight: 750;
    text-align: center;
    border: 2px solid var(--line);
    border-radius: 12px;
    transition: all 0.22s ease;
    color: var(--text);
    background: #fff;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
}

.license-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 12px 30px rgba(236, 24, 22, 0.16);
}

.button-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.license-add-btn {
    background: #fff;
    border: 1px solid var(--line);
    color: var(--text);
    padding: 10px 14px;
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.license-add-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    box-shadow: 0 8px 18px rgba(236, 24, 22, 0.14);
}

.helper-text {
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: var(--muted);
    letter-spacing: 0.35px;
}

.total-box {
    margin-top: 20px;
    padding: 20px 18px;
    border: 1px dashed var(--line);
    border-radius: 12px;
    background: linear-gradient(135deg, #fff 0%, #fafafa 100%);
}

.total-box h4 {
    margin: 0 0 6px;
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: var(--muted);
    letter-spacing: 0.4px;
    text-transform: uppercase;
}

.total-amount {
    font-family: "Geologica", sans-serif;
    font-size: 40px;
    font-weight: 800;
    color: var(--accent);
    letter-spacing: -0.4px;
}

.total-note {
    margin-top: 6px;
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: var(--muted);
}

.cta-primary {
    margin-top: 22px;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 18px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 10px 26px rgba(236, 24, 22, 0.22);
    transition: all 0.22s ease;
}

.cta-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(236, 24, 22, 0.28);
}

.price-card {
    background: #fffdfb;
    border-radius: 14px;
    border: 1px solid #f0d8c8;
    box-shadow: 0 14px 38px rgba(0, 0, 0, 0.05);
}

.price-card .card-body {
    padding: 8px 24px 22px;
}

.price-list {
    display: flex;
    flex-direction: column;
}

.price-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    padding: 18px 0;
    border-bottom: 1px dashed #dec8b8;
}

.price-row:last-child {
    border-bottom: none;
}

.seat-range {
    font-family: "Geologica", sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--text);
}

.seat-price {
    text-align: right;
    font-family: "Geologica", sans-serif;
    font-size: 22px;
    font-weight: 750;
    color: var(--text);
}

.seat-price .seat-period {
    display: block;
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
}

.price-footnote {
    margin-top: 14px;
    text-align: center;
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: var(--muted);
    letter-spacing: 0.4px;
    line-height: 1.6;
}

.checkout-inline {
    margin-top: 16px;
}

@media (max-width: 1024px) {
    .layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .corporate-subscription {
        padding: 54px 0 64px;
    }

    .hero h1 {
        font-size: 38px;
    }

    .layout {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .price-card {
        order: 1;
    }

    .card-primary {
        order: 2;
    }

    .checkout-inline {
        order: 3;
    }

    .license-input {
        /* max-width: 200px; */
        font-size: 30px;
    }

    .cta-primary {
        font-size: 15px;
        padding: 14px 16px;
    }
}

@media (max-width: 480px) {
    .hero h1 {
        font-size: 30px;
    }

    .seat-range {
        font-size: 18px;
    }

    .seat-price {
        font-size: 20px;
    }
}
</style>

<section class="corporate-subscription">
    <div class="container">
        <div class="hero">
            <h1>Corporate Subscription Plans</h1>
            <p>Power every team with premium reporting, analysis, and market intelligence designed for decision-makers.
            </p>
        </div>

        <div class="layout">
            <div class="card card-primary">
                <div class="card-header">
                    <h3>Select seats & calculate</h3>
                </div>
                <form id="subscriptionForm" class="card-body" action="/buy-corporate-subscription/" method="GET">
                    <div class="input-group">
                        <span class="input-label">How many licenses do you require?</span>
                        <input type="number" id="numUsers" name="num_users" class="license-input" min="10" value="10"
                            required>
                        <div class="button-row">
                            <button type="button" class="license-add-btn" data-add="5">+5</button>
                            <button type="button" class="license-add-btn" data-add="10">+10</button>
                            <button type="button" class="license-add-btn" data-add="25">+25</button>
                            <button type="button" class="license-add-btn" data-add="100">+100</button>
                        </div>
                        <div class="helper-text">* Minimum 10 licenses required. Prices shown include all taxes.</div>
                    </div>

                    <div class="total-box">
                        <h4>Total annual cost</h4>
                        <div class="total-amount"><span class="currency">₹</span><span id="displayTotal">26,990</span>
                        </div>
                        <div class="total-note">Annual billing • No hidden charges • GST invoice issued</div>
                    </div>

                    <button type="submit" class="cta-primary">Proceed to Checkout <span
                            aria-hidden="true">→</span></button>
                </form>
            </div>

            <div class="card price-card">
                <div class="card-header">
                    <h3>Pricing Structure</h3>
                </div>
                <div class="card-body">
                    <div class="price-list">
                        <div class="price-row">
                            <div class="seat-range">10 - 25 seats</div>
                            <div class="seat-price">₹ 2,699<span class="seat-period">per seat / year</span></div>
                        </div>
                        <div class="price-row">
                            <div class="seat-range">26 - 50 seats</div>
                            <div class="seat-price">₹ 2,299<span class="seat-period">per seat / year</span></div>
                        </div>
                        <div class="price-row">
                            <div class="seat-range">51+ seats</div>
                            <div class="seat-price">₹ 1,799<span class="seat-period">per seat / year</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numUsersInput = document.getElementById('numUsers');
    const displayTotal = document.getElementById('displayTotal');
    const licenseAddBtns = document.querySelectorAll('.license-add-btn');
    const subscriptionForm = document.getElementById('subscriptionForm');

    licenseAddBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const addValue = parseInt(this.getAttribute('data-add'), 10);
            const currentValue = parseInt(numUsersInput.value, 10) || 10;
            numUsersInput.value = currentValue + addValue;
            calculatePrice();
        });
    });

    function calculatePrice() {
        const numUsers = parseInt(numUsersInput.value, 10) || 0;
        let pricePerPerson = 0;

        if (numUsers >= 10 && numUsers <= 25) {
            pricePerPerson = 2699;
        } else if (numUsers >= 26 && numUsers <= 50) {
            pricePerPerson = 2299;
        } else if (numUsers >= 51) {
            pricePerPerson = 1799;
        }

        const totalAmount = numUsers * pricePerPerson;
        displayTotal.textContent = totalAmount.toLocaleString('en-IN');
    }

    calculatePrice();
    numUsersInput.addEventListener('input', calculatePrice);
    numUsersInput.addEventListener('change', calculatePrice);

    subscriptionForm.addEventListener('submit', function() {
        const rawValue = parseInt(numUsersInput.value, 10) || 0;
        const clampedValue = Math.max(10, rawValue);
        if (clampedValue !== rawValue) {
            numUsersInput.value = clampedValue;
            calculatePrice();
        }
    });
});
</script>

<?php
require_once 'footer.php';
?>