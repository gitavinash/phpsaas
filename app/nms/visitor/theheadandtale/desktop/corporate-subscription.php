<?php

require_once 'header.php';
?>

<style>
.corporate-subscription {
    padding: 80px 0;
    background: #fafafa;
    width: 100%;
}

.corporate-subscription .container {
    max-width: 1200px;
    width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.corporate-header {
    text-align: center;
    padding-bottom: 50px;
}

.corporate-header h1 {
    font-family: "Geologica", sans-serif;
    font-size: 56px;
    font-weight: 700;
    color: var(--black-color, #000);
    margin-bottom: 20px;
    line-height: 1.15;
    letter-spacing: -0.5px;
}

.corporate-header p {
    font-family: "Noto Sans", sans-serif;
    font-size: 18px;
    color: #555;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.7;
    font-weight: 300;
}

.purchase-box {
    background: var(--white-color, #fff);
    border-radius: 12px;
    padding: 60px 50px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    margin-bottom: 60px;
    border: 1px solid #f0f0f0;
}

.purchase-box-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

.left-purchase-section {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 50px;
}

.license-selector-section {
    margin-bottom: 0;
    background: linear-gradient(135deg, #fafafa 0%, #fff 100%);
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.license-selector-section label {
    display: block;
    font-family: "Geologica", sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--black-color, #000);
    margin-bottom: 28px;
    letter-spacing: -0.3px;
    line-height: 1.3;
}

.license-quantity-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 28px;
}

.license-input {
    width: 100%;
    padding: 10px 0px;
    font-size: 28px;
    font-weight: 700;
    text-align: center;
    border: 1px solid #ec1816;
    border-radius: 7px;
    transition: all 0.3s ease;
    background: #fff;
    box-shadow: 0 4px 16px rgba(236, 24, 22, 0.1);
    color: #ec1816;
}

.license-input:focus {
    outline: none;
    border-color: #ec1816;
    background: #fff;
    box-shadow: 0 6px 20px rgba(236, 24, 22, 0.2);
    transform: scale(1.02);
}

.license-add-btn {
    background: #fff;
    border: 1px solid #d0d0d0;
    color: var(--black-color, #000);
    padding: 5px 15px;
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: capitalize;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
}

.license-buttons-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.license-add-btn:hover {
    background: #ec1816;
    color: var(--white-color, #fff);
    border-color: #ec1816;
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.15);
}

.license-info {
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    color: #888;
    margin-top: 14px;
    font-weight: 400;
    letter-spacing: 0.3px;
}

.final-price-section {
    background: linear-gradient(135deg, #fff 0%, #fafafa 100%);
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 45px 40px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    position: sticky;
    top: 100px;
}

.final-price-section h4 {
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    color: #999;
    margin: 0 0 24px 0;
    font-weight: 500;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.final-price-display {
    font-family: "Geologica", sans-serif;
    font-size: 48px;
    font-weight: 700;
    color: #ec1816;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.final-price-display .currency {
    font-size: 32px;
}

.final-price-note {
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: #888;
    margin-top: 18px;
    font-weight: 400;
    letter-spacing: 0.3px;
    line-height: 1.6;
}

.pricing-structure {
    background: #ffe9e9;
    border-radius: 0;
    padding: 1em;
    box-shadow: none;
    margin-bottom: 0;
    border: none;
    box-sizing: border-box;
    width: 100%;
}

.pricing-structure h2 {
    font-family: "Geologica", sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--black-color, #000);
    margin-bottom: 40px;
    text-align: center;
    letter-spacing: -0.3px;
    line-height: 3em;
}

.pricing-tiers {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.pricing-tier-item {
    background: transparent;
    border-radius: 0;
    padding: 25px 0;
    text-align: left;
    border: none;
    border-bottom: 2px dotted #d0d0d0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pricing-tier-item:last-child {
    border-bottom: none;
}

.pricing-tier-item:hover {
    border-color: #ec1816;
    background: transparent;
    box-shadow: none;
}

.tier-range {
    font-family: "Noto Sans", sans-serif;
    font-size: 18px;
    font-weight: 400;
    color: var(--black-color, #000);
    margin-bottom: 0;
    text-transform: none;
    letter-spacing: 0;
    flex: 1;
}

.tier-price {
    font-family: "Geologica", sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--black-color, #000);
    margin-left: auto;
    text-align: right;
    display: inline;
}

.tier-price-wrapper {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: auto;
}

.tier-price .currency {
    font-size: 16px;
}

.tier-period {
    font-family: "Noto Sans", sans-serif;
    font-size: 12px;
    color: #999;
    margin-top: 2px;
    margin-left: 0;
    font-weight: 400;
    letter-spacing: 0.5px;
    white-space: nowrap;
    text-transform: uppercase;
}

.gst-notice {
    display: block;
    text-align: center;
    color: #999;
    font-size: 12px;
    margin-top: 30px;
    font-style: normal;
    font-family: "Noto Sans", sans-serif;
    font-weight: 400;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    line-height: 1.8;
}

.proceed-checkout-btn {
    background: #ec1816;
    color: var(--white-color, #fff);
    border: none;
    outline: none;
    padding: 20px 50px;
    font-family: "Noto Sans", sans-serif;
    font-size: 18px;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 40px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 6px 20px rgba(236, 24, 22, 0.3);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.checkout-btn-wrapper {
    width: 100%;
    max-width: 100%;
}

.proceed-checkout-btn::after {
    content: '→';
    font-size: 22px;
    font-weight: 700;
    transition: transform 0.3s ease;
}

.proceed-checkout-btn:hover {
    background: var(--black-color, #000);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    transform: translateY(-3px);
}

.proceed-checkout-btn:hover::after {
    transform: translateX(5px);
}

.proceed-checkout-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.3);
}

.subscription-section {
    background: var(--white-color, #fff);
    border-radius: 12px;
    padding: 60px 50px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    margin-bottom: 40px;
    border: 1px solid #f0f0f0;
}

.subscription-section h2 {
    font-family: "Geologica", sans-serif;
    font-size: 36px;
    font-weight: 700;
    color: var(--black-color, #000);
    margin-bottom: 50px;
    text-align: center;
    letter-spacing: -0.3px;
}

.two-column-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 40px;
    align-items: start;
}

@media (max-width: 968px) {
    .two-column-layout {
        grid-template-columns: 1fr;
    }

    .pricing-sidebar {
        order: -1;
    }
}

.left-column {
    flex: 1;
}

.pricing-sidebar {
    position: sticky;
    top: 20px;
}

.quantity-selector {
    text-align: center;
    margin-bottom: 40px;
}

.quantity-selector label {
    display: block;
    font-family: "Geologica", sans-serif;
    font-size: 20px;
    font-weight: 600;
    color: var(--black-color, #000);
    margin-bottom: 25px;
    letter-spacing: -0.2px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.quantity-input {
    width: 130px;
    padding: 16px 20px;
    font-family: "Geologica", sans-serif;
    font-size: 26px;
    font-weight: 700;
    text-align: center;
    border: 2px solid #ddd;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: #fafafa;
}

.quantity-input:focus {
    outline: none;
    border-color: #ec1816;
    background: #fff;
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.1);
}

.quick-add-btn {
    background: #fafafa;
    border: 2px solid #ddd;
    color: var(--black-color, #000);
    padding: 12px 24px;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: capitalize;
}

.quick-add-btn:hover {
    background: #ec1816;
    color: var(--white-color, #fff);
    border-color: #ec1816;
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.2);
}

.price-display {
    background: linear-gradient(135deg, #fafafa 0%, #fff 100%);
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 35px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.price-display.sidebar {
    margin: 0;
    position: sticky;
    top: 100px;
}

.price-display h3 {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    color: #888;
    margin-bottom: 18px;
    font-weight: 400;
    line-height: 1.6;
    letter-spacing: 0.3px;
}

.price-display .total-price {
    font-family: "Geologica", sans-serif;
    font-size: 52px;
    font-weight: 700;
    color: #ec1816;
    margin-bottom: 15px;
    letter-spacing: -0.5px;
}

.price-display .total-price .currency {
    font-size: 32px;
}

.price-display .price-breakdown {
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    color: #888;
    margin-top: 15px;
    font-weight: 400;
    padding-top: 15px;
    border-top: 1px solid #e8e8e8;
}

.form-section {
    margin-top: 30px;
}

.form-section.hidden {
    display: none;
}

.step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 50px;
    padding: 30px;
    background: #fafafa;
    border-radius: 12px;
}

.step {
    display: flex;
    align-items: center;
    gap: 12px;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e8e8e8;
    color: #888;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Geologica", sans-serif;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: #ec1816;
    color: var(--white-color, #fff);
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.2);
}

.step.completed .step-number {
    background: var(--black-color, #000);
    color: var(--white-color, #fff);
}

.step-label {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    color: #888;
    font-weight: 600;
    transition: color 0.3s ease;
}

.step.active .step-label {
    color: #ec1816;
}

.step.completed .step-label {
    color: var(--black-color, #000);
}

.step-divider {
    width: 50px;
    height: 2px;
    background: #ddd;
    transition: background 0.3s ease;
}

.continue-btn {
    background: #ec1816;
    color: var(--white-color, #fff);
    border: none;
    outline: none;
    padding: 12px 30px;
    font-family: "Noto Sans", sans-serif;
    font-size: 18px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 30px;
    text-transform: capitalize;
}

.continue-btn:hover {
    opacity: 0.7;
}

.continue-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 1;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--black-color, #000);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.form-group input[type="text"],
.form-group input[type="email"] {
    width: 100%;
    padding: 14px 16px;
    font-family: "Noto Sans", sans-serif;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #fafafa;
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus {
    outline: none;
    border-color: #ec1816;
    background: #fff;
    box-shadow: 0 4px 12px rgba(236, 24, 22, 0.1);
}

.checkout-btn {
    background: var(--black-color, #000);
    color: var(--white-color, #fff);
    border: none;
    outline: none;
    padding: 12px 30px;
    font-family: "Noto Sans", sans-serif;
    font-size: 18px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 20px;
    text-transform: capitalize;
}

.checkout-btn:hover {
    opacity: 0.7;
}

.checkout-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 1;
}

.info-notice {
    background: #fafafa;
    border-left: 4px solid #ec1816;
    padding: 20px 24px;
    margin: 40px 0;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #e8e8e8;
}

.info-notice p {
    margin: 0;
    color: var(--black-color, #000);
    font-family: "Noto Sans", sans-serif;
    font-weight: 500;
    font-size: 14px;
    letter-spacing: 0.2px;
}

.error-message {
    background: #fff5f5;
    color: #c4001d;
    padding: 14px 16px;
    border-radius: 10px;
    font-family: "Noto Sans", sans-serif;
    font-size: 13px;
    margin-top: 12px;
    display: none;
    border-left: 4px solid #c4001d;
    border: 1px solid #ffe5e5;
}

.error-message.show {
    display: block;
}

@media (max-width: 1200px) {
    .corporate-subscription .container {
        width: 100%;
        max-width: 100%;
    }

    .purchase-box-layout {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .purchase-box {
        padding: 40px 30px;
    }

    .license-selector-section {
        padding: 30px;
    }

    .final-price-section {
        padding: 35px 30px;
    }
}

@media (max-width: 768px) {
    .corporate-subscription {
        padding: 40px 0;
    }

    .corporate-header {
        margin-bottom: 40px;
        padding-bottom: 30px;
    }

    .corporate-header h1 {
        font-size: 32px;
        margin-bottom: 15px;
    }

    .corporate-header p {
        font-size: 16px;
        line-height: 1.6;
    }

    .purchase-box {
        padding: 30px 20px;
        margin-bottom: 40px;
    }

    .purchase-box-layout {
        gap: 30px;
        display: flex;
        flex-direction: column;
    }

    .left-purchase-section {
        gap: 30px;
        order: 2;
    }

    .pricing-structure {
        order: 1;
    }

    .checkout-btn-wrapper {
        margin-top: 2em;
        order: 3;
    }

    .license-selector-section {
        padding: 25px 20px;
    }

    .license-selector-section label {
        font-size: 18px;
        margin-bottom: 20px;
    }

    .license-input {
        font-size: 36px;
        padding: 20px 24px;
    }

    .license-buttons-row {
        gap: 8px;
    }

    .license-add-btn {
        padding: 10px 18px;
        font-size: 13px;
    }

    .license-info {
        font-size: 12px;
    }

    .final-price-section {
        padding: 30px 25px;
        position: static;
    }

    .final-price-section h4 {
        font-size: 12px;
        margin-bottom: 18px;
    }

    .final-price-display {
        font-size: 38px;
    }

    .final-price-display .currency {
        font-size: 24px;
    }

    .final-price-note {
        font-size: 11px;
        line-height: 1.5;
    }

    .pricing-structure h2 {
        font-size: 22px;
        margin-bottom: 25px;
    }

    .pricing-tiers {
        margin-bottom: 35px;
    }

    .pricing-tier-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        padding: 20px 0;
    }

    .tier-range {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .tier-price-wrapper {
        align-items: flex-start;
        margin-left: 0;
    }

    .tier-price {
        margin-left: 0;
        margin-top: 0;
        font-size: 20px;
    }

    .tier-price .currency {
        font-size: 14px;
    }

    .tier-period {
        margin-left: 0;
        margin-top: 2px;
        white-space: normal;
        font-size: 11px;
    }

    .gst-notice {
        font-size: 11px;
        margin-top: 20px;
        line-height: 1.6;
    }

    .proceed-checkout-btn {
        padding: 18px 35px;
        font-size: 16px;
        margin-top: 0;
        letter-spacing: 0.8px;
    }

    .proceed-checkout-btn::after {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .corporate-header h1 {
        font-size: 26px;
    }

    .corporate-header p {
        font-size: 14px;
    }

    .purchase-box {
        padding: 25px 15px;
    }

    .license-selector-section {
        padding: 20px 15px;
    }

    .license-selector-section label {
        font-size: 16px;
    }

    .license-input {
        font-size: 32px;
        padding: 18px 20px;
    }

    .license-buttons-row {
        gap: 6px;
    }

    .license-add-btn {
        padding: 8px 14px;
        font-size: 12px;
    }

    .final-price-section {
        padding: 25px 20px;
    }

    .final-price-display {
        font-size: 32px;
    }

    .pricing-structure h2 {
        font-size: 20px;
    }

    .tier-range {
        font-size: 15px;
    }

    .tier-price {
        font-size: 18px;
    }

    .proceed-checkout-btn {
        padding: 16px 28px;
        font-size: 14px;
        letter-spacing: 0.6px;
    }

    .proceed-checkout-btn::after {
        font-size: 18px;
    }
}
</style>

<section class="corporate-subscription">
    <div class="container">
        <div class="corporate-header">
            <h1>Corporate Subscription Plans</h1>
            <p>Unlock comprehensive access to premium business journalism, in-depth analysis, and market intelligence
                for your entire organization</p>
        </div>

        <div class="purchase-box">
            <div class="purchase-box-layout">
                <!-- Left Column: License Selector + Final Price -->
                <div class="left-purchase-section">
                    <div>
                        <div class="license-selector-section">
                            <label>How many licenses do you require?</label>
                            <div class="license-quantity-controls">
                                <input type="number" id="numUsers" name="num_users" class="license-input" min="10"
                                    value="10" required>
                                <div class="license-buttons-row">
                                    <button type="button" class="license-add-btn" data-add="5">+5</button>
                                    <button type="button" class="license-add-btn" data-add="10">+10</button>
                                    <button type="button" class="license-add-btn" data-add="25">+25</button>
                                    <button type="button" class="license-add-btn" data-add="100">+100</button>
                                </div>
                            </div>
                            <div class="license-info"><sup><strong>*</strong></sup>Minimum 10 licenses required</div>
                        </div>
                    </div>

                    <div class="final-price-section">
                        <h4>Total Annual Cost</h4>
                        <div class="final-price-display">
                            <span class="currency">₹</span><span id="displayTotal">26,990</span>
                        </div>
                        <div class="final-price-note">Prices include all taxes. No hidden charges.</div>
                    </div>
                </div>

                <!-- Right Column: Price Structure -->
                <div class="pricing-structure">
                    <h2>Pricing Structure</h2>
                    <div class="pricing-tiers">
                        <div class="pricing-tier-item">
                            <div class="tier-range">10 - 25 seats</div>
                            <div class="tier-price-wrapper">
                                <div class="tier-price"><span class="currency">₹</span> 2,699</div>
                                <div class="tier-period">per seat / year</div>
                            </div>
                        </div>
                        <div class="pricing-tier-item">
                            <div class="tier-range">26 - 50 seats</div>
                            <div class="tier-price-wrapper">
                                <div class="tier-price"><span class="currency">₹</span> 2,299</div>
                                <div class="tier-period">per seat / year</div>
                            </div>
                        </div>
                        <div class="pricing-tier-item">
                            <div class="tier-range">51 and above seats</div>
                            <div class="tier-price-wrapper">
                                <div class="tier-price"><span class="currency">₹</span> 1,799</div>
                                <div class="tier-period">per seat / year</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkout-btn-wrapper">
                <button type="button" class="proceed-checkout-btn" id="proceedCheckoutBtn">Proceed to Checkout</button>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numUsersInput = document.getElementById('numUsers');
    const displayTotal = document.getElementById('displayTotal');
    const licenseAddBtns = document.querySelectorAll('.license-add-btn');

    // Quick add buttons
    licenseAddBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const addValue = parseInt(this.getAttribute('data-add'));
            const currentValue = parseInt(numUsersInput.value) || 10;
            numUsersInput.value = currentValue + addValue;
            calculatePrice();
        });
    });

    function calculatePrice() {
        const numUsers = parseInt(numUsersInput.value) || 0;

        let pricePerPerson = 0;

        if (numUsers >= 10 && numUsers <= 25) {
            pricePerPerson = 2699;
        } else if (numUsers >= 26 && numUsers <= 50) {
            pricePerPerson = 2299;
        } else if (numUsers >= 51) {
            pricePerPerson = 1799;
        }

        const totalAmount = numUsers * pricePerPerson;

        // Update display - NO GST calculation
        displayTotal.textContent = totalAmount.toLocaleString('en-IN');
    }

    // Initialize with default value
    calculatePrice();

    numUsersInput.addEventListener('input', calculatePrice);
    numUsersInput.addEventListener('change', calculatePrice);
});
</script>

<?php

require_once 'footer.php';

?>