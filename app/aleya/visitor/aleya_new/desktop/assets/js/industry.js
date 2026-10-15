/**
 * Aleya SaaS — Industry Landing Pages & Showcase Logic
 * Interactive Savings & ROI Calculator and UI Tab Switchers
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // --- Showcase Tab Switcher ---
  const tabButtons = document.querySelectorAll('.showcase-tab-btn');
  const tabPanels = document.querySelectorAll('.showcase-panel');

  if (tabButtons.length > 0) {
    tabButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        const targetId = button.getAttribute('data-target');

        tabButtons.forEach(function (btn) { btn.classList.remove('active'); });
        tabPanels.forEach(function (panel) { panel.classList.remove('active'); });

        button.classList.add('active');
        const targetPanel = document.getElementById(targetId);
        if (targetPanel) {
          targetPanel.classList.add('active');
        }
      });
    });
  }

  // --- Transparent Savings & Time ROI Calculator ---
  const calcTeamSize = document.getElementById('calcTeamSize');
  const calcWeeklyHours = document.getElementById('calcWeeklyHours');
  const calcHourlyCost = document.getElementById('calcHourlyCost');
  const calcSoftwareCost = document.getElementById('calcSoftwareCost');

  const valTeamSize = document.getElementById('valTeamSize');
  const valWeeklyHours = document.getElementById('valWeeklyHours');
  const valHourlyCost = document.getElementById('valHourlyCost');
  const valSoftwareCost = document.getElementById('valSoftwareCost');

  const outAdminHours = document.getElementById('outAdminHours');
  const outHoursSaved = document.getElementById('outHoursSaved');
  const outTimeValue = document.getElementById('outTimeValue');
  const outAleyaPlan = document.getElementById('outAleyaPlan');
  const outNetMonthly = document.getElementById('outNetMonthly');
  const outAnnualSavings = document.getElementById('outAnnualSavings');

  function formatCurrencyINR(amount) {
    return '₹' + Math.round(amount).toLocaleString('en-IN');
  }

  function updateSavingsCalculator() {
    if (!calcTeamSize || !calcWeeklyHours || !calcHourlyCost || !calcSoftwareCost) return;

    const teamSize = parseInt(calcTeamSize.value, 10) || 5;
    const weeklyHours = parseInt(calcWeeklyHours.value, 10) || 12;
    const hourlyCost = parseInt(calcHourlyCost.value, 10) || 350;
    const softwareCost = parseInt(calcSoftwareCost.value, 10) || 2500;

    // Aleya plan cost baseline: CRM Starter is ₹1,499, ERP Complete is ₹3,999
    // Read from data attribute or default to ERP Complete ₹3,999
    const calculatorRoot = document.getElementById('savingsCalculator');
    const aleyaMonthlyCost = calculatorRoot && calculatorRoot.dataset.planCost 
      ? parseInt(calculatorRoot.dataset.planCost, 10) 
      : 3999;

    // Display labels
    if (valTeamSize) valTeamSize.textContent = teamSize + ' Members';
    if (valWeeklyHours) valWeeklyHours.textContent = weeklyHours + ' hrs / week';
    if (valHourlyCost) valHourlyCost.textContent = formatCurrencyINR(hourlyCost) + ' / hr';
    if (valSoftwareCost) valSoftwareCost.textContent = formatCurrencyINR(softwareCost) + ' / mo';

    // Core calculations
    const monthlyAdminHours = Math.round(teamSize * weeklyHours * 4.33);
    const estimatedHoursSaved = Math.round(monthlyAdminHours * 0.60); // 60% repetitive reduction
    const monthlyTimeValue = estimatedHoursSaved * hourlyCost;
    const netMonthlyBenefit = monthlyTimeValue + softwareCost - aleyaMonthlyCost;
    const estimatedAnnualBenefit = Math.max(0, netMonthlyBenefit * 12);

    // Render outputs
    if (outAdminHours) outAdminHours.textContent = monthlyAdminHours + ' hrs';
    if (outHoursSaved) outHoursSaved.textContent = estimatedHoursSaved + ' hrs / mo';
    if (outTimeValue) outTimeValue.textContent = formatCurrencyINR(monthlyTimeValue);
    if (outAleyaPlan) outAleyaPlan.textContent = '-' + formatCurrencyINR(aleyaMonthlyCost);
    if (outNetMonthly) outNetMonthly.textContent = formatCurrencyINR(Math.max(0, netMonthlyBenefit));
    if (outAnnualSavings) outAnnualSavings.textContent = formatCurrencyINR(estimatedAnnualBenefit);
  }

  if (calcTeamSize) {
    calcTeamSize.addEventListener('input', updateSavingsCalculator);
    calcWeeklyHours.addEventListener('input', updateSavingsCalculator);
    calcHourlyCost.addEventListener('input', updateSavingsCalculator);
    calcSoftwareCost.addEventListener('input', updateSavingsCalculator);

    // Initial calculation run
    updateSavingsCalculator();
  }
});
