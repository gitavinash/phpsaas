<?php
require_once 'header.php';
?>



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

    // FAQ Accordion Toggle Handler
    const faqItems = document.querySelectorAll('.faq-accordion-item');
    faqItems.forEach(function(item) {
      const header = item.querySelector('.faq-header');
      if (header) {
        header.addEventListener('click', function(e) {
          e.preventDefault();
          const isOpen = item.hasAttribute('open') || item.classList.contains('is-open');

          faqItems.forEach(function(otherItem) {
            otherItem.removeAttribute('open');
            otherItem.classList.remove('is-open', 'active');
          });

          if (!isOpen) {
            item.setAttribute('open', '');
            item.classList.add('is-open', 'active');
          }
        });
      }
    });
  });
</script>

<?php require_once 'footer.php'; ?>