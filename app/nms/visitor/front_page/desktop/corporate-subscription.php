<?php
require_once 'header.php';
?>

<style>
  .corp-hero {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 60px 20px 48px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
  }

  .corp-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: #fee2e2;
    color: #dc2626;
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
  }

  .corp-hero h1 {
    font-size: 2.4rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 12px;
    line-height: 1.25;
  }

  .corp-hero p {
    font-size: 1.05rem;
    color: #475569;
    max-width: 620px;
    margin: 0 auto;
    line-height: 1.6;
  }

  .corp-main-section {
    background: #f8fafc;
    padding: 48px 16px 80px;
  }

  .corp-container {
    max-width: 860px;
    margin: 0 auto;
  }

  .pricing-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .selector-box {
    padding: 36px 32px 28px;
    text-align: center;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
  }

  .selector-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #64748b;
    margin-bottom: 18px;
  }

  .seat-control-wrap {
    display: inline-flex;
    align-items: center;
    border: 2px solid #cbd5e1;
    border-radius: 14px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    margin-bottom: 16px;
  }

  .seat-btn {
    width: 52px;
    height: 52px;
    border: none;
    background: #f8fafc;
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    cursor: pointer;
    transition: background-color 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .seat-btn:hover {
    background: #e2e8f0;
    color: var(--accent-color, #d93025);
  }

  .seat-input {
    width: 110px;
    height: 52px;
    border: none;
    border-left: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    text-align: center;
    font-size: 1.6rem;
    font-weight: 800;
    color: #0f172a;
    outline: none;
  }

  .chips-row {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .chip-btn {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .chip-btn:hover {
    background: #f1f5f9;
    border-color: var(--accent-color, #d93025);
    color: var(--accent-color, #d93025);
  }

  .helper-text {
    font-size: 0.82rem;
    color: #64748b;
  }

  /* Tiers */
  .tiers-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    padding: 28px 28px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }

  .tier-card {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 16px;
    text-align: center;
    transition: all 0.2s ease;
  }

  .tier-card.active-tier {
    border-color: var(--accent-color, #d93025) !important;
    background: #fff5f5 !important;
    box-shadow: 0 8px 20px rgba(217, 48, 37, 0.12);
  }

  .tier-range {
    font-size: 0.9rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
  }

  .tier-price {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
  }

  .tier-unit {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 2px;
  }

  /* Total Bar */
  .total-bar {
    background: #0f172a;
    color: #ffffff;
    padding: 20px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
  }

  .total-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #94a3b8;
  }

  .total-amount {
    font-size: 1.7rem;
    font-weight: 800;
    color: #ffffff;
  }

  .btn-checkout {
    background: var(--accent-color, #d93025);
    color: #ffffff;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.98rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(217, 48, 37, 0.3);
    transition: all 0.2s ease;
  }

  .btn-checkout:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(217, 48, 37, 0.4);
  }

  .footer-strip {
    padding: 12px 20px;
    font-size: 0.82rem;
    color: #64748b;
    text-align: center;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
  }

  /* Feature Grid */
  .feature-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    padding: 28px 24px;
    background: #ffffff;
  }

  .feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
  }

  .feature-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
  }

  /* FAQ Accordion Section */
  .corp-faq-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    margin-top: 32px;
    padding: 32px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
  }

  .corp-faq-card h3 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 20px;
  }

  .faq-accordion-item {
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 0;
  }

  .faq-accordion-item:last-child {
    border-bottom: none;
  }

  .faq-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    user-select: none;
    list-style: none;
  }

  .faq-header::-webkit-details-marker {
    display: none;
  }

  .faq-header::marker {
    content: "";
  }

  .faq-header:hover {
    color: var(--accent-color, #d93025);
  }

  .faq-icon-chevron {
    font-size: 1.2rem;
    color: #94a3b8;
    transition: transform 0.2s ease;
  }

  .faq-accordion-item[open] .faq-icon-chevron {
    transform: rotate(180deg);
    color: var(--accent-color, #d93025);
  }

  .faq-body {
    display: none;
    padding-top: 12px;
    font-size: 0.92rem;
    line-height: 1.6;
    color: #475569;
  }

  .faq-accordion-item[open] .faq-body {
    display: block;
  }

  @media (max-width: 768px) {
    .corp-hero h1 {
      font-size: 1.8rem;
    }

    .tiers-grid {
      grid-template-columns: 1fr;
    }

    .feature-grid {
      grid-template-columns: 1fr;
    }

    .total-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 9999;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.2);
      padding: 14px 20px;
    }
  }
</style>

<div class="corp-hero">
  <span class="corp-badge">Corporate Membership</span>
  <h1>Power Your Team With Market Intelligence</h1>
  <p>Empower your analysts, executives, and leaders with independent reporting, deepdives, and market data across Fintech, AI, and Tech.</p>
</div>

<section class="corp-main-section">
  <div class="corp-container">
    <div class="pricing-card">
      <form action="/buy-corporate-subscription/" method="GET">
        <div class="selector-box">
          <div class="selector-title">Select Number of Seats</div>

          <div class="seat-control-wrap">
            <button type="button" class="seat-btn" id="minusBtn">−</button>
            <input type="number" name="num_users" id="numUsers" class="seat-input" value="10" min="10">
            <button type="button" class="seat-btn" id="plusBtn">+</button>
          </div>

          <div class="chips-row">
            <button type="button" class="chip-btn" data-add="5">+5 Seats</button>
            <button type="button" class="chip-btn" data-add="10">+10 Seats</button>
            <button type="button" class="chip-btn" data-add="25">+25 Seats</button>
            <button type="button" class="chip-btn" data-add="100">+100 Seats</button>
          </div>

          <div class="helper-text">*Minimum 10 licenses required. All prices inclusive of applicable GST.</div>
        </div>

        <!-- Tier Highlights -->
        <div class="tiers-grid">
          <div class="tier-card" id="tier1">
            <div class="tier-range">10 – 25 Seats</div>
            <div class="tier-price">₹2,799</div>
            <div class="tier-unit">per seat / year</div>
          </div>

          <div class="tier-card" id="tier2">
            <div class="tier-range">26 – 50 Seats</div>
            <div class="tier-price">₹2,399</div>
            <div class="tier-unit">per seat / year</div>
          </div>

          <div class="tier-card" id="tier3">
            <div class="tier-range">51+ Seats</div>
            <div class="tier-price">₹1,899</div>
            <div class="tier-unit">per seat / year</div>
          </div>
        </div>

        <div class="total-bar">
          <div>
            <div class="total-label">Total Annual Cost</div>
            <div class="total-amount">₹ <span id="totalPrice">27,990</span></div>
          </div>
          <button type="submit" class="btn-checkout">Proceed to Checkout →</button>
        </div>
      </form>

      <div class="footer-strip">
        Annual billing • No hidden charges • GST Tax Invoice Issued Automatically
      </div>

      <div class="feature-grid">
        <div class="feature-item">
          <div class="feature-icon-box">✓</div>
          <span>Individual Work Email Access</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon-box">✓</div>
          <span>No Shared Logins Required</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon-box">✓</div>
          <span>GST Invoice Included</span>
        </div>
      </div>
    </div>

    <!-- Accordion FAQ & Terms Section -->
    <div class="corp-faq-card">
      <h3>Corporate Subscription Terms & FAQ</h3>

      <details class="faq-accordion-item">
        <summary class="faq-header">
          <span>1. Scope & Seat-Based Access</span>
          <span class="faq-icon-chevron">▾</span>
        </summary>
        <div class="faq-body">
          Corporate subscriptions provide full digital access to premium content for employees within a single organization. Access is granted on a per-seat basis linked to unique work email addresses. Shared logins are strictly prohibited.
        </div>
      </details>

      <details class="faq-accordion-item">
        <summary class="faq-header">
          <span>2. Minimum Seats & Device Rules</span>
          <span class="faq-icon-chevron">▾</span>
        </summary>
        <div class="faq-body">
          Standard corporate plans start at a minimum of 10 seats. Each seat may be accessed on up to 2 active devices simultaneously. Accessing from a 3rd device automatically triggers a temporary 48-hour security pause.
        </div>
      </details>

      <details class="faq-accordion-item">
        <summary class="faq-header">
          <span>3. Invoicing & Billing Policy</span>
          <span class="faq-icon-chevron">▾</span>
        </summary>
        <div class="faq-body">
          Corporate plans are billed annually in advance. GST invoices are generated and emailed to your billing contact within 48 hours of successful payment. For customized team plans, write to info@theheadandtale.com.
        </div>
      </details>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const numUsers = document.getElementById("numUsers");
    const totalPrice = document.getElementById("totalPrice");
    const minusBtn = document.getElementById("minusBtn");
    const plusBtn = document.getElementById("plusBtn");
    const chipBtns = document.querySelectorAll(".chip-btn");
    const tier1 = document.getElementById("tier1");
    const tier2 = document.getElementById("tier2");
    const tier3 = document.getElementById("tier3");

    if (!numUsers || !totalPrice || !minusBtn || !plusBtn || !tier1 || !tier2 || !tier3) {
      return;
    }

    function priceForSeats(n) {
      if (n <= 25) return 2799;
      if (n <= 50) return 2399;
      return 1899;
    }

    function calculate() {
      let n = parseInt(numUsers.value) || 10;
      if (n < 10) n = 10;
      numUsers.value = n;

      const rate = priceForSeats(n);
      totalPrice.textContent = (n * rate).toLocaleString("en-IN");

      tier1.classList.remove("active-tier");
      tier2.classList.remove("active-tier");
      tier3.classList.remove("active-tier");

      if (n <= 25) tier1.classList.add("active-tier");
      else if (n <= 50) tier2.classList.add("active-tier");
      else tier3.classList.add("active-tier");
    }

    minusBtn.onclick = function() {
      numUsers.value = Math.max(10, (parseInt(numUsers.value) || 10) - 1);
      calculate();
    };

    plusBtn.onclick = function() {
      numUsers.value = (parseInt(numUsers.value) || 10) + 1;
      calculate();
    };

    chipBtns.forEach(function(btn) {
      btn.onclick = function() {
        numUsers.value = (parseInt(numUsers.value) || 10) + parseInt(btn.dataset.add);
        calculate();
      };
    });

    numUsers.oninput = calculate;
    calculate();
  });
</script>

<?php require_once 'footer.php'; ?>