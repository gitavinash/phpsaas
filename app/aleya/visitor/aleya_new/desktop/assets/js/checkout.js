/**
 * Aleya SaaS — Checkout & Order Flow Logic
 * Plan calculation, subdomain availability verification, and order processing
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const PRICING = {
    crm: {
      name: 'CRM Commercial Suite',
      desc: 'Lead pipeline, WhatsApp automation, quotation builder & payment ledger',
      yearlyMonthlyRate: 1499,
      monthlyRate: 1799,
      extraUserRate: 149
    },
    erp: {
      name: 'ERP Complete Suite',
      desc: 'Full operations, itineraries, vouchers, fleet management & billing',
      yearlyMonthlyRate: 3999,
      monthlyRate: 4999,
      extraUserRate: 349
    }
  };

  const form = document.getElementById('checkoutForm');
  const planSelector = document.getElementById('checkoutPlan');
  const cycleRadios = document.querySelectorAll('input[name="billing_cycle"]');
  const userCountInput = document.getElementById('checkoutUsers');
  const subdomainInput = document.getElementById('checkoutSubdomain');
  const subdomainStatus = document.getElementById('subdomainStatus');

  const summaryPlanName = document.getElementById('summaryPlanName');
  const summaryCycle = document.getElementById('summaryCycle');
  const summaryBasePrice = document.getElementById('summaryBasePrice');
  const summaryExtraUsers = document.getElementById('summaryExtraUsers');
  const summaryGst = document.getElementById('summaryGst');
  const summaryTotal = document.getElementById('summaryTotal');
  const checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');

  // Read initial query params
  const urlParams = new URLSearchParams(window.location.search);
  const initialPlan = urlParams.get('plan') || 'erp';
  const initialCycle = urlParams.get('cycle') || 'yearly';

  if (planSelector && (initialPlan === 'crm' || initialPlan === 'erp')) {
    planSelector.value = initialPlan;
  }

  if (cycleRadios.length > 0) {
    cycleRadios.forEach(function (radio) {
      if (radio.value === initialCycle) radio.checked = true;
    });
  }

  function formatCurrency(amount) {
    return '₹' + Math.round(amount).toLocaleString('en-IN');
  }

  function calculateTotals() {
    const selectedPlanKey = planSelector ? planSelector.value : 'erp';
    const planInfo = PRICING[selectedPlanKey] || PRICING.erp;

    let selectedCycle = 'yearly';
    cycleRadios.forEach(function (radio) {
      if (radio.checked) selectedCycle = radio.value;
    });

    const isYearly = selectedCycle === 'yearly';
    const months = isYearly ? 12 : 1;
    const baseMonthlyRate = isYearly ? planInfo.yearlyMonthlyRate : planInfo.monthlyRate;
    const baseSubtotal = baseMonthlyRate * months;

    const userCount = parseInt(userCountInput?.value || '10', 10);
    const extraUsers = Math.max(0, userCount - 10);
    const extraUsersSubtotal = extraUsers * planInfo.extraUserRate * months;

    const setupFee = isYearly ? 0 : 5000;

    const netTaxableAmount = baseSubtotal + extraUsersSubtotal + setupFee;
    const gstAmount = Math.round(netTaxableAmount * 0.18);
    const grandTotal = netTaxableAmount + gstAmount;

    const summaryPlanRate = document.getElementById('summaryPlanRate');
    const summaryPlanDesc = document.getElementById('summaryPlanDesc');
    const summaryCycleBadge = document.getElementById('summaryCycleBadge');
    const summaryBaseSubtext = document.getElementById('summaryBaseSubtext');
    const summaryExtraUsersSubtext = document.getElementById('summaryExtraUsersSubtext');
    const summarySavingsBanner = document.getElementById('summarySavingsBanner');
    const summarySavingsText = document.getElementById('summarySavingsText');

    if (summaryPlanName) summaryPlanName.textContent = planInfo.name;
    if (summaryPlanRate) summaryPlanRate.textContent = Math.round(baseMonthlyRate).toLocaleString('en-IN');
    if (summaryPlanDesc) summaryPlanDesc.textContent = planInfo.desc || '';
    if (summaryCycle) summaryCycle.textContent = isYearly ? 'Annual (12 Months)' : 'Monthly';

    if (summaryCycleBadge) {
      if (isYearly) {
        summaryCycleBadge.className = 'summary-plan-badge';
        summaryCycleBadge.textContent = 'Annual • Save 20%';
      } else {
        summaryCycleBadge.className = 'summary-plan-badge badge-monthly';
        summaryCycleBadge.textContent = 'Monthly Plan';
      }
    }

    if (summaryBasePrice) summaryBasePrice.textContent = formatCurrency(baseSubtotal);
    if (summaryBaseSubtext) {
      summaryBaseSubtext.textContent = isYearly 
        ? `${formatCurrency(baseMonthlyRate)}/mo × 12 months` 
        : `${formatCurrency(baseMonthlyRate)}/mo (1 month)`;
    }

    if (summaryExtraUsers) {
      summaryExtraUsers.innerHTML = extraUsers > 0 
        ? `${formatCurrency(extraUsersSubtotal)} <span class="badge-setup-fee">+${extraUsers} seat${extraUsers > 1 ? 's' : ''}</span>` 
        : '<span class="badge-waived">10 Included</span>';
    }
    if (summaryExtraUsersSubtext) {
      summaryExtraUsersSubtext.textContent = extraUsers > 0
        ? `10 included + ${extraUsers} extra seat(s) at ${formatCurrency(planInfo.extraUserRate)}/mo`
        : '10 users included out of the box';
    }

    const summarySetupFee = document.getElementById('summarySetupFee');
    if (summarySetupFee) {
      summarySetupFee.innerHTML = isYearly 
        ? '<span class="badge-waived">₹0 (Waived on Annual)</span>' 
        : formatCurrency(setupFee);
    }
    if (summaryGst) summaryGst.textContent = formatCurrency(gstAmount);
    if (summaryTotal) summaryTotal.textContent = formatCurrency(grandTotal);

    // Calculate annual savings
    const annualSavingsValue = (planInfo.monthlyRate - planInfo.yearlyMonthlyRate) * 12;
    if (summarySavingsBanner && summarySavingsText) {
      if (isYearly) {
        summarySavingsBanner.className = 'summary-savings-banner is-annual';
        summarySavingsText.innerHTML = `<strong>Annual Discount Active:</strong> Saving ${formatCurrency(annualSavingsValue)}/yr on subscription + <strong>₹0 Setup Fee</strong> (Save ₹5,000)!`;
      } else {
        summarySavingsBanner.className = 'summary-savings-banner is-monthly';
        summarySavingsText.innerHTML = `<strong>Pro Tip:</strong> Switch to Annual to waive the <strong>₹5,000 setup fee</strong> & save ${formatCurrency(annualSavingsValue)}/yr!`;
      }
    }

    return {
      plan: selectedPlanKey,
      cycle: selectedCycle,
      users: userCount,
      setupFee: setupFee,
      taxable: netTaxableAmount,
      gst: gstAmount,
      total: grandTotal
    };
  }

  if (planSelector) planSelector.addEventListener('change', calculateTotals);
  cycleRadios.forEach(function (radio) { radio.addEventListener('change', calculateTotals); });
  if (userCountInput) userCountInput.addEventListener('input', calculateTotals);

  // Allow clicking on savings banner when monthly to quickly switch to annual
  const summarySavingsBanner = document.getElementById('summarySavingsBanner');
  if (summarySavingsBanner) {
    summarySavingsBanner.addEventListener('click', function () {
      let isMonthlyActive = false;
      cycleRadios.forEach(function (radio) {
        if (radio.checked && radio.value === 'monthly') isMonthlyActive = true;
      });
      if (isMonthlyActive) {
        cycleRadios.forEach(function (radio) {
          if (radio.value === 'yearly') radio.checked = true;
        });
        calculateTotals();
      }
    });
  }

  // Summary Submit Button (Sticky Card CTA)
  const summarySubmitBtn = document.getElementById('summarySubmitBtn');
  if (summarySubmitBtn && form) {
    summarySubmitBtn.addEventListener('click', function () {
      if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
      } else {
        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
      }
    });
  }

  // Subdomain Live Checker
  let domainCheckTimer = null;
  if (subdomainInput && subdomainStatus) {
    subdomainInput.addEventListener('input', function () {
      clearTimeout(domainCheckTimer);
      const rawVal = subdomainInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
      subdomainInput.value = rawVal;

      if (rawVal.length < 3) {
        subdomainStatus.innerHTML = '<span class="domain-taken"><i class="fas fa-times-circle"></i> Minimum 3 characters required</span>';
        return;
      }

      subdomainStatus.innerHTML = '<span><i class="fas fa-spinner fa-spin"></i> Checking availability...</span>';

      domainCheckTimer = setTimeout(function () {
        fetch('/api/v3/auth/check-domain/?subdomain=' + encodeURIComponent(rawVal))
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.available || data.status === 'success') {
              subdomainStatus.innerHTML = '<span class="domain-available"><i class="fas fa-check-circle"></i> <strong>' + rawVal + '.aleya.in</strong> is available!</span>';
            } else {
              subdomainStatus.innerHTML = '<span class="domain-taken"><i class="fas fa-times-circle"></i> ' + (data.message || 'Subdomain already registered. Try another.') + '</span>';
            }
          })
          .catch(function () {
            subdomainStatus.innerHTML = '<span class="domain-available"><i class="fas fa-check-circle"></i> <strong>' + rawVal + '.aleya.in</strong> is valid</span>';
          });
      }, 400);
    });
  }

  // Handle Form Submission
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const totals = calculateTotals();
      const customerName = (form.querySelector('[name="name"]')?.value || '').trim();
      const customerEmail = (form.querySelector('[name="email"]')?.value || '').trim();
      const customerPhone = (form.querySelector('[name="phone"]')?.value || '').trim();
      const businessName = (form.querySelector('[name="business_name"]')?.value || '').trim();
      const subdomain = (subdomainInput?.value || '').trim();

      if (!customerName || !customerEmail || !customerPhone || !businessName || !subdomain) {
        alert('Please fill in all required fields before proceeding.');
        return;
      }

      if (checkoutSubmitBtn) {
        checkoutSubmitBtn.disabled = true;
        checkoutSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initializing order...';
      }
      if (summarySubmitBtn) {
        summarySubmitBtn.disabled = true;
        summarySubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initializing order...';
      }

      // Track begin checkout event
      if (typeof window.trackConversion === 'function') {
        window.trackConversion('begin_checkout', {
          plan: totals.plan,
          billing_cycle: totals.cycle,
          value: totals.total,
          currency: 'INR'
        });
      }

      // Pass selections to dedicated onboarding provisioning flow
      const onboardingUrl = '/onboarding/?plan=' + encodeURIComponent(totals.plan) + 
        '&cycle=' + encodeURIComponent(totals.cycle) + 
        '&subdomain=' + encodeURIComponent(subdomain) + 
        '&name=' + encodeURIComponent(customerName) + 
        '&email=' + encodeURIComponent(customerEmail) + 
        '&phone=' + encodeURIComponent(customerPhone) + 
        '&business=' + encodeURIComponent(businessName);

      window.location.href = onboardingUrl;
    });
  }

  // Initial calculation
  calculateTotals();
});
