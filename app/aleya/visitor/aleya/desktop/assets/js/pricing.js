/**
 * Aleya SaaS — Pricing Logic
 * Billing frequency toggle, GST calculations, and user tier computation
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const pricingToggle = document.getElementById('pricingBillingToggle');
  const amountElements = document.querySelectorAll('.price-amount-dynamic');
  const periodElements = document.querySelectorAll('.price-period-dynamic');
  const billingNotes = document.querySelectorAll('.billing-note-dynamic');
  const buyButtons = document.querySelectorAll('.pricing-buy-btn');

  function updatePricing(isYearly) {
    amountElements.forEach(function (el) {
      const yearlyVal = el.getAttribute('data-yearly');
      const monthlyVal = el.getAttribute('data-monthly');
      el.textContent = isYearly ? yearlyVal : monthlyVal;
    });

    periodElements.forEach(function (el) {
      el.textContent = isYearly ? '/mo (billed annually)' : '/mo (billed monthly)';
    });

    billingNotes.forEach(function (el) {
      el.textContent = isYearly ? 'Billed annually + 18% GST' : 'Billed monthly + 18% GST';
    });

    // Update query params on Buy buttons
    buyButtons.forEach(function (btn) {
      const currentHref = btn.getAttribute('href');
      if (currentHref) {
        const url = new URL(currentHref, window.location.origin);
        url.searchParams.set('cycle', isYearly ? 'yearly' : 'monthly');
        btn.setAttribute('href', url.pathname + url.search);
      }
    });

    // Update toggle labels active state
    const labelMonthly = document.getElementById('labelMonthly');
    const labelYearly = document.getElementById('labelYearly');
    if (labelMonthly && labelYearly) {
      if (isYearly) {
        labelYearly.classList.add('active');
        labelMonthly.classList.remove('active');
      } else {
        labelMonthly.classList.add('active');
        labelYearly.classList.remove('active');
      }
    }
  }

  if (pricingToggle) {
    pricingToggle.addEventListener('change', function () {
      updatePricing(pricingToggle.checked);
    });
  }

  // Comparison toggle button
  const comparisonBtn = document.getElementById('toggleComparisonBtn');
  const comparisonWrapper = document.getElementById('comparisonWrapper');

  if (comparisonBtn && comparisonWrapper) {
    comparisonBtn.addEventListener('click', function () {
      const isHidden = comparisonWrapper.style.display === 'none' || !comparisonWrapper.style.display;
      comparisonWrapper.style.display = isHidden ? 'block' : 'none';
      comparisonBtn.innerHTML = isHidden 
        ? '<span>Hide full feature comparison</span> <i class="fas fa-chevron-up"></i>' 
        : '<span>Compare all features</span> <i class="fas fa-chevron-down"></i>';
    });
  }
});
