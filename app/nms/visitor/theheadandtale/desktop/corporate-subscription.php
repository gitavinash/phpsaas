<?php
require_once LOGIN_CHECK;
require_once 'header.php';
?>

<style>
:root {
    --bg: #f7f5ef;
    --card: #ffffff;
    --border: #e2e0da;
    --text: #2a2a2a;
    --muted: #7b7b7b;
    --dark: #4f4f4f;
}

/* wrapper */
.corporate-page {
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 60px 15px;
    background: var(--bg);
}

.corporate-inner {
    width: 600px;
    text-align: center;
}

/* heading */
.corporate-inner h2 {
    font-size: 20px;
    letter-spacing: 1px;
    font-weight: 700;
    margin-bottom: 6px;
}

.corporate-inner p {
    font-size: 14px;
    color: var(--muted);
    margin-bottom: 28px;
}

/* card */
.pricing-card {
    background: var(--card);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    margin-top: -50px;
}

/* selector */
.selector {
    padding: 26px 24px 22px;
    border-bottom: 1px solid var(--border);
}

.selector h4 {
    font-size: 12px;
    letter-spacing: 1px;
    margin-bottom: 14px !important;
    color: #444;
}

.seat-control {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 10px;
    width: 220px;
    margin: 0 auto 12px;
    background: #fff;
}

.seat-control button {
    width: 42px;
    height: 42px;
    border: none;
    font-size: 18px;
    color: #555;
    width: 100%;
    line-height: 0px;
    cursor: pointer;
}

.seat-control input {
    width: 90px;
    border: none;
    text-align: center;
    font-size: 26px;
    font-weight: 600;
    background: #fff;
    color: #222;
}

/* chips */
.chips {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 10px;
}

.chips button {
    border: 1px solid var(--border);
    background: #fff;
    border-radius: 8px;
    padding: 7px 14px;
    font-size: 12px;
    cursor: pointer;
    color: #444;
}

/* helper */
.helper {
    font-size: 12px;
    color: var(--muted);
}

/* tiers */
.tiers {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    padding: 40px 20px;
    background: #fafafa;
}

.tier {
    background: #ffffff;
    border-radius: 8px;
    padding: 15px 25px;
    border: 2px solid #e8e8e8;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.tier:hover {
    border-color: #ee171b;
    box-shadow: 0 8px 24px rgba(238, 23, 27, 0.15);
    transform: translateY(-4px);
}

.tier span {
    font-size: 16px;
    font-weight: 700;
    color: var(--black-color, #1a1a1a);
    margin-bottom: 20px;
    display: block;
}

.tier strong {
    font-size: 24px;
    font-weight: 800;
    color: #333;
    display: block;
}

.tier small {
    display: block;
    font-size: 12px;
    color: #666;
    font-weight: 500;
    margin-bottom: 20px;
}

.tier-badge {
    background: #ee171b;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    margin-top: 10px;
    display: inline-block;
}

/* total bar (flat) */
.total-bar {
    background: #f5c3aa;
    color: #fff;
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #333;
}

.total-bar strong {
    float: left;
    font-size: 24px;
}

.total-bar button {
    background: #ee171b;
    border: none;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
}

/* footer strip */
.footer-strip {
    padding: 10px;
    font-size: 12px;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
}

/* features */
.features {
    display: flex;
    justify-content: space-around;
    padding: 35px 20px 30px;
    font-size: 13px;
    color: #555;
    font-weight: 600;
}

.features .feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    text-align: center;
}

.features .feature-icon {
    font-size: 36px;
}

/* Responsive Tiers */
@media (max-width: 1024px) {
    .tiers {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .tiers {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 25px 15px;
    }
}

/* Terms & Conditions */
.terms-section {
    margin-top: -50px;
    border-radius: 4px;
    padding: 50px 60px;
}

.terms-section h3 {
    font-size: 28px;
    font-weight: 700;
    color: var(--black-color, #1a1a1a);
    margin-bottom: 10px;
    text-align: left;
    display: inline-block;
}

.terms-section .subtitle {
    font-size: 16px;
    color: #666;
    margin-bottom: 40px;
    font-style: italic;
    text-align: left;
}

.terms-section h4 {
    font-size: 18px;
    font-weight: 700;
    color: var(--black-color, #1a1a1a);
    margin-top: 40px;
    margin-bottom: 18px;
    text-align: left;
    letter-spacing: 0.5px;
}

.terms-section p,
.terms-section ul {
    font-size: 15px;
    line-height: 1.8;
    color: #333;
    margin-bottom: 18px;
    text-align: left;
}

.terms-section p {
    letter-spacing: 0.3px;
}

.terms-section ul {
    padding-left: 32px;
    margin-top: 12px;
    list-style-position: outside;
}

.terms-section ul li {
    margin-bottom: 12px;
    color: #333;
}

.terms-section hr {
    border: none;
    border-top: 2px solid #f0f0f0;
    margin: 35px 0;
}

.terms-section .contact-info {
    background: linear-gradient(to right, #fafafa, #f5f5f5);
    padding: 25px;
    border-radius: 6px;
    margin-top: 20px;
    font-weight: 600;
    color: var(--black-color, #1a1a1a);
    border-left: 4px solid #ee171b;
    letter-spacing: 0.3px;
}

.terms-section.collapsed .terms-body {
    display: none;
}

.answer {
    text-align: left;
    padding: 10px 0px;
    line-height: 32px;
}

.terms-toggle {
    background: transparent;
    color: var(--black-color, #1a1a1a);
    border: 2px solid #e0e0e0;
    padding: 10px 15px;
    border-radius: 50%;
    font-weight: 900;
    cursor: pointer;
    margin-left: 15px;
    transition: all 0.3s ease;
    font-size: 20px;
    width: 45px;
    height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    vertical-align: middle;
}

.terms-toggle:hover {
    background: #fafafa;
    border-color: #ee171b;
    color: #ee171b;
}

.terms-section:not(.collapsed) .terms-toggle {
    transform: rotate(180deg);
}

/* newsletter */
.newsletter {
    margin-top: 34px;
}

.newsletter h4 {
    margin-bottom: 12px;
    font-size: 16px;
}

.newsletter-box {
    display: flex;
    justify-content: center;
}

.newsletter-box input {
    width: 280px;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 10px 0 0 10px;
}

.newsletter-box button {
    padding: 12px 16px;
    border: none;
    background: #6f6f6f;
    color: #fff;
    border-radius: 0 10px 10px 0;
}

.subscribe_wrapper__heading h1 {
    font-weight: 600 !important;
}

.subscribe_wrapper__heading p {
    margin-top: 0px !important;
    font-weight: 400;
    font-size: 18px;
    line-height: 42px;
}
</style>

<section class="corporate-page">
    <div class="subscribe_wrapper__heading">
        <h1>Corporate Subscription Plans</h1>
        <p>Power your team with independent reporting, sharp analysis, and market intelligence on money and technology.
        </p>
    </div>
</section>
<section class="corporate-page">
    <div class="corporate-inner">


        <div class="pricing-card">

            <form action="/buy-corporate-subscription/" method="get">
                <div class="selector">
                    <h4>SELECT NUMBER OF SEATS</h4>

                    <div class="seat-control">
                        <button type="button" id="minusBtn">−</button>
                        <input type="number" name="num_users" id="numUsers" value="10" min="10">
                        <button type="button" id="plusBtn">+</button>
                    </div>

                    <div class="chips">
                        <button type="button" data-add="5">+5</button>
                        <button type="button" data-add="10">+10</button>
                        <button type="button" data-add="25">+25</button>
                        <button type="button" data-add="100">+100</button>
                    </div>

                    <div class="helper"><sup>*</sup>Minimum 10 licenses required. Prices shown inclusive of all taxes.
                    </div>
                </div>

                <div class="tiers">
                    <div class="tier">
                        <div>
                            <span>10 – 25 seats</span>
                            <strong>₹2,699</strong>
                            <small>per seat / year</small>
                        </div>
                    </div>
                    <div class="tier">
                        <div>
                            <span>26 – 50 seats</span>
                            <strong>₹2,299</strong>
                            <small>per seat / year</small>
                        </div>
                    </div>
                    <div class="tier">
                        <div>
                            <span>51+ seats</span>
                            <strong>₹1,799</strong>
                            <small>per seat / year</small>
                        </div>
                    </div>
                </div>

                <div class="total-bar">
                    <div>
                        <div style="font-size:11px;">TOTAL ANNUAL COST</div>
                        <strong>₹ <span id="totalPrice">26,990</span></strong>
                    </div>
                    <button>Proceed to Checkout →</button>
                </div>
            </form>

            <div class="footer-strip">
                Annual billing • No hidden charges • GST invoice
            </div>

            <div class="features">
                <div class="feature-item">
                    <span class="feature-icon">👤</span>
                    <span>Individual email access</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🔒</span>
                    <span>No shared logins</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">📄</span>
                    <span>GST invoice</span>
                </div>
            </div>

        </div>


    </div>
</section>

<div class="subscription_mail">
    <div class="container">
        <div class="faq_wrapper">
            <div class="faq__box-container">
                <div class="faq__box">
                    <div class="question">
                        <h3>Corporate Subscription: Terms & Conditions</h3>
                        <i class='bx bx-chevron-down'></i>
                    </div>
                    <div class="answer">
                        <h4>1. Scope of Corporate Subscription</h4>
                        <p>Corporate subscriptions provide access to The Head and Tale's premium content for
                            multiple users within a
                            single organisation. Access is granted on a per-seat basis and is intended solely for
                            internal,
                            professional use by the subscribing entity.</p>
                        <hr>

                        <h4>2. Seat-Based Access</h4>
                        <ul>
                            <li>Each subscription seat is linked to a unique, individual work email ID.</li>
                            <li>Shared logins, credential pooling, or rotation of access among multiple users are
                                strictly
                                prohibited.</li>
                            <li>The subscribing organisation is responsible for ensuring that access is limited to
                                authorised users
                                only.</li>
                        </ul>
                        <hr>

                        <h4>3. Minimum Seats & Eligibility</h4>
                        <ul>
                            <li>Standard corporate plans start at a minimum of 10 seats.</li>
                            <li>Small businesses seeking fewer than 10 seats may submit an enquiry, which will be
                                reviewed on a
                                case-by-case basis.</li>
                            <li>Approval of such requests is at the sole discretion of The Head and Tale.</li>
                        </ul>
                        <hr>

                        <h4>4. Login Usage & Device Limits</h4>
                        <ul>
                            <li>Each user account may be accessed on up to two devices or browsers simultaneously.
                            </li>
                            <li>If an account is accessed on a third device or browser, the account will be
                                automatically suspended
                                for 48 hours.</li>
                            <li>Access will be restored automatically after the suspension period.</li>
                            <li>Repeated violations may lead to further restrictions or termination of access.</li>
                        </ul>
                        <p>This policy is in place to prevent misuse and ensure fair access across corporate seats
                            as well as
                            individual subscriptions.</p>
                        <hr>

                        <h4>5. Seat Transfer Policy</h4>
                        <ul>
                            <li>Subscription seats may be transferred to a new user only after six (6) months from
                                the original
                                activation date.</li>
                            <li>All transfer requests must be submitted in writing to The Head and Tale.</li>
                            <li>Access to the new email ID will be granted only after review and approval.</li>
                            <li>Frequent seat transfers are not permitted.</li>
                        </ul>
                        <hr>

                        <h4>6. Usage & Content Restrictions</h4>
                        <p>Corporate subscribers may not:</p>
                        <ul>
                            <li>Republish, redistribute, or syndicate content externally</li>
                            <li>Share content outside the organisation, including with clients, media, or third
                                parties</li>
                            <li>Use content for marketing, promotional, or commercial redistribution without prior
                                permission</li>
                        </ul>
                        <p>All content remains the intellectual property of The Head and Tale.</p>
                        <hr>

                        <h4>7. Monitoring & Fair Use</h4>
                        <p>The Head and Tale reserves the right to monitor usage patterns to ensure compliance with
                            seat limits,
                            login rules, and fair use policies. Any misuse or violation may result in temporary
                            suspension or
                            termination of access.</p>
                        <hr>

                        <h4>8. Billing & Invoicing</h4>
                        <ul>
                            <li>Corporate subscriptions are billed annually.</li>
                            <li>Pricing is calculated on a per-seat, per-year basis.</li>
                            <li>For GST invoices, please write to us at info@theheadandtale.com. It will be issued
                                within 48 hours.
                            </li>
                            <li>Full payment must be made in advance for activation.</li>
                        </ul>
                        <hr>

                        <h4>9. Cancellations & Refunds</h4>
                        <ul>
                            <li>All corporate subscriptions are non-cancellable and non-refundable once activated.
                            </li>
                            <li>No refunds, credits, or adjustments will be provided for unused seats, partial
                                usage, or early
                                termination.</li>
                        </ul>
                        <hr>

                        <h4>10. Termination of Access</h4>
                        <p>The Head and Tale reserves the right to suspend or terminate access if:</p>
                        <ul>
                            <li>Login credentials are misused or shared.</li>
                        </ul>
                        <p>Termination does not entitle the subscriber to any refund.</p>
                        <hr>

                        <h4>11. Editorial Independence</h4>
                        <p>Corporate subscriptions do not influence editorial coverage. Access does not imply
                            endorsement,
                            partnership, or preferential treatment in reporting.</p>
                        <hr>

                        <h4>12. Amendments</h4>
                        <p>These terms may be updated periodically. Continued use of corporate access after updates
                            are published
                            constitutes acceptance of the revised terms.</p>
                        <hr>

                        <h4>13. Contact</h4>
                        <p>For corporate subscription queries, seat transfers, or access issues, contact:</p>
                        <div class="contact-info">info@theheadandtale.com</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
const numUsers = document.getElementById("numUsers");
const totalPrice = document.getElementById("totalPrice");
const minusBtn = document.getElementById("minusBtn");
const plusBtn = document.getElementById("plusBtn");
const chips = document.querySelectorAll(".chips button");

function priceForSeats(n) {
    if (n <= 25) return 2699;
    if (n <= 50) return 2299;
    return 1799;
}

function calculate() {
    let n = parseInt(numUsers.value);
    if (n < 10) n = 10;
    numUsers.value = n;
    totalPrice.textContent = (n * priceForSeats(n)).toLocaleString("en-IN");
}

minusBtn.onclick = () => {
    numUsers.value = Math.max(10, numUsers.value - 1);
    calculate();
}
plusBtn.onclick = () => {
    numUsers.value++;
    calculate();
}
chips.forEach(b => {
    b.onclick = () => {
        numUsers.value = parseInt(numUsers.value) + parseInt(b.dataset.add);
        calculate();
    }
});
numUsers.oninput = calculate;
calculate();

// Terms & Conditions toggle
const termsSection = document.querySelector('.terms-section');
const termsToggle = document.getElementById('termsToggle');
const termsBody = document.querySelector('.terms-body');

if (termsSection && termsToggle && termsBody) {
    termsSection.classList.add('collapsed');
    termsToggle.onclick = () => {
        termsSection.classList.toggle('collapsed');
    };
}
</script>

<?php require_once 'footer.php'; ?>