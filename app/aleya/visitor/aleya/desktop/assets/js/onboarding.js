/**
 * Aleya Travel CRM & ERP — Onboarding & Provisioning Flow
 * High-conversion multi-step workspace activation module.
 */

document.addEventListener("DOMContentLoaded", function () {
  let currentStep = 1;
  const totalSteps = 4;

  // Pricing rates
  const PRICING = {
    crm: {
      yearly: 1499,
      monthly: 1874,
      name: "CRM Starter",
    },
    erp: {
      yearly: 3999,
      monthly: 4999,
      name: "ERP Complete",
    },
  };

  let selectedPlan = "erp"; // Default to featured plan
  let billingCycle = "yearly"; // Default to annual savings

  // DOM Elements - Master Controls
  const form = document.getElementById("onboardingForm");
  const prevBtn = document.getElementById("btnPrev");
  const nextBtn = document.getElementById("btnNext");
  const submitBtn = document.getElementById("btnSubmit");
  const progressBar = document.getElementById("progressBarFill");
  const stepLabel = document.getElementById("stepLabel");
  const stepPercent = document.getElementById("stepPercent");
  const alertBox = document.getElementById("onboardingAlert");
  const loadingOverlay = document.getElementById("loadingOverlay");
  const loadingStatusText = document.getElementById("loadingStatusText");

  // Step 1 Elements
  const businessNameInput = document.getElementById("businessName");
  const subdomainPreview = document.getElementById("subdomainPreview");
  const subdomainHidden = document.getElementById("subdomainInput");
  const subdomainAvailabilityBadge = document.getElementById("subdomainAvailabilityBadge");
  const subdomainStatusIcon = document.getElementById("subdomainStatusIcon");
  const subdomainStatusText = document.getElementById("subdomainStatusText");
  const planCycleMonthly = document.getElementById("cycleMonthly");
  const planCycleYearly = document.getElementById("cycleYearly");
  const planCardCrm = document.getElementById("planCardCrm");
  const planCardErp = document.getElementById("planCardErp");
  const priceDisplayCrm = document.getElementById("priceDisplayCrm");
  const priceDisplayErp = document.getElementById("priceDisplayErp");
  const periodDisplayCrm = document.getElementById("periodDisplayCrm");
  const periodDisplayErp = document.getElementById("periodDisplayErp");
  const step1ActivePlanBar = document.getElementById("step1ActivePlanBar");
  const step1PlanPriceSection = document.getElementById("step1PlanPriceSection");
  const btnSwitchToCrm = document.getElementById("btnSwitchToCrm");
  const erpSelectIndicator = document.getElementById("erpSelectIndicator");
  const crmSelectIndicator = document.getElementById("crmSelectIndicator");

  // Step 2: Tour Package Destinations Management Elements
  const tabDomestic = document.getElementById("tabDomestic");
  const tabInternational = document.getElementById("tabInternational");
  const gridDomestic = document.getElementById("gridDomestic");
  const gridInternational = document.getElementById("gridInternational");
  const destSelectedCount = document.getElementById("destSelectedCount");
  const destTotalPackagesCount = document.getElementById("destTotalPackagesCount");
  const summaryPackagesCount = document.getElementById("summaryPackagesCount");
  const summaryPackagesBadge = document.getElementById("summaryPackagesBadge");
  const summaryPackagesDest = document.getElementById("summaryPackagesDest");
  const btnSelectAllTab = document.getElementById("btnSelectAllTab");
  const btnDeselectAllTab = document.getElementById("btnDeselectAllTab");

  // Custom Team Size (Step 2)
  const customTeamSizeWrap = document.getElementById("customTeamSizeWrap");
  const customTeamSizeInput = document.getElementById("customTeamSizeInput");

  // Summary Elements (Step 4)
  const summaryPlanName = document.getElementById("summaryPlanName");
  const summaryBillingCycle = document.getElementById("summaryBillingCycle");
  const summarySubdomain = document.getElementById("summarySubdomain");
  const summaryPrice = document.getElementById("summaryPrice");
  const summaryUsersCount = document.getElementById("summaryUsersCount");
  const summaryPlanChargeLabel = document.getElementById("summaryPlanChargeLabel");
  const summaryPlanChargeVal = document.getElementById("summaryPlanChargeVal");
  const summarySetupFeeRow = document.getElementById("summarySetupFeeRow");
  const summarySetupFeeVal = document.getElementById("summarySetupFeeVal");
  const summarySetupBadge = document.getElementById("summarySetupBadge");
  const summaryGstVal = document.getElementById("summaryGstVal");
  const summaryPriceNumber = document.getElementById("summaryPriceNumber");
  const summaryBillingBreakdown = document.getElementById("summaryBillingBreakdown");
  const upgradeToErpCard = document.getElementById("upgradeToErpCard");
  const btnUpgradeToErp = document.getElementById("btnUpgradeToErp");
  const summaryPricingCard = document.getElementById("summaryPricingCard");

  // Domain check state variables
  let isDomainChecking = false;
  let isDomainAvailable = true;
  let domainCheckTimer = null;

  // Read URL Query Params for Pre-selection (?plan=crm or ?plan=erp)
  const urlParams = new URLSearchParams(window.location.search);
  const planParam = urlParams.get("plan");
  if (planParam && (planParam.toLowerCase() === "crm" || planParam.toLowerCase() === "erp")) {
    selectedPlan = planParam.toLowerCase();
  }
  const cycleParam = urlParams.get("cycle");
  if (cycleParam && (cycleParam.toLowerCase() === "monthly" || cycleParam.toLowerCase() === "yearly")) {
    billingCycle = cycleParam.toLowerCase();
  }

  function getActiveDestGrid() {
    if (gridDomestic && gridDomestic.classList.contains("active")) {
      return gridDomestic;
    }
    return gridInternational;
  }

  function updateDestinationStats() {
    const checkedBoxes = document.querySelectorAll('input[name="destinations[]"]:checked');
    const selectedCount = checkedBoxes.length;
    let totalPackages = 0;

    checkedBoxes.forEach(function (cb) {
      const card = cb.closest(".destination-card");
      if (card) {
        const pkgCount = parseInt(card.getAttribute("data-packages") || "0", 10);
        totalPackages += pkgCount;
      }
    });

    if (destSelectedCount) {
      destSelectedCount.textContent = selectedCount;
    }
    if (destTotalPackagesCount) {
      destTotalPackagesCount.textContent = totalPackages;
    }
    if (summaryPackagesBadge) {
      summaryPackagesBadge.textContent = totalPackages + " Packages";
    }
    if (summaryPackagesDest) {
      summaryPackagesDest.textContent = selectedCount + " Selected Destinations";
    }
    if (summaryPackagesCount) {
      summaryPackagesCount.textContent = totalPackages + " Pre-Loaded Packages (" + selectedCount + " Destinations)";
    }
  }

  function updatePlanPrices() {
    if (priceDisplayCrm && priceDisplayErp) {
      priceDisplayCrm.textContent = PRICING.crm[billingCycle].toLocaleString("en-IN");
      priceDisplayErp.textContent = PRICING.erp[billingCycle].toLocaleString("en-IN");
      const periodText = billingCycle === "yearly" ? "/mo (billed annually)" : "/mo";
      if (periodDisplayCrm) periodDisplayCrm.textContent = periodText;
      if (periodDisplayErp) periodDisplayErp.textContent = periodText;
    }
    updateSummary();
  }

  function updatePlanView() {
    const radioCrm = document.getElementById("planRadioCrm");
    const radioErp = document.getElementById("planRadioErp");

    if (selectedPlan === "crm") {
      if (planCardCrm) planCardCrm.classList.add("selected");
      if (planCardErp) planCardErp.classList.remove("selected");
      if (radioCrm) radioCrm.checked = true;

      // When user selected low plan, show Step 1 plan price / upgrade section with ERP option!
      if (step1ActivePlanBar) step1ActivePlanBar.classList.add("hidden");
      if (step1PlanPriceSection) step1PlanPriceSection.classList.remove("hidden");

      if (crmSelectIndicator) crmSelectIndicator.textContent = "Selected (Current Plan)";
      if (erpSelectIndicator) erpSelectIndicator.textContent = "Upgrade to ERP Complete \u2192";

      // Lower plan selected: show upgrade option card in Step 4
      if (upgradeToErpCard) upgradeToErpCard.classList.remove("hidden");
    } else {
      if (planCardErp) planCardErp.classList.add("selected");
      if (planCardCrm) planCardCrm.classList.remove("selected");
      if (radioErp) radioErp.checked = true;

      // High plan already selected: don't show price section in Step 1! Show sleek active plan bar instead
      if (step1ActivePlanBar) step1ActivePlanBar.classList.remove("hidden");
      if (step1PlanPriceSection) step1PlanPriceSection.classList.add("hidden");

      if (crmSelectIndicator) crmSelectIndicator.textContent = "Select CRM Starter";
      if (erpSelectIndicator) erpSelectIndicator.textContent = "Selected";

      // High plan already selected: hide upgrade card in Step 4
      if (upgradeToErpCard) upgradeToErpCard.classList.add("hidden");
    }

    // In Step 4, always show the complete pricing breakdown of how much client will pay
    if (summaryPricingCard) summaryPricingCard.classList.remove("hidden");

    updatePlanPrices();
  }

  function updateSummary() {
    if (summaryPlanName) {
      summaryPlanName.textContent = PRICING[selectedPlan].name;
    }
    if (summaryBillingCycle) {
      summaryBillingCycle.textContent = billingCycle === "yearly" ? "Annual (Save 25%)" : "Monthly";
      summaryBillingCycle.className = "summary-pill " + (billingCycle === "yearly" ? "cycle-pill" : "plan-pill");
    }

    const monthlyRate = PRICING[selectedPlan].monthly;
    const yearlyRate = PRICING[selectedPlan].yearly;
    
    let planSubtotal = 0;
    let setupFee = 0;
    
    if (billingCycle === "yearly") {
      planSubtotal = yearlyRate * 12;
      setupFee = 0; // Waived for Annual
      if (summaryPlanChargeLabel) {
        summaryPlanChargeLabel.textContent = PRICING[selectedPlan].name + " (1 Year Access)";
      }
      if (summarySetupBadge) {
        summarySetupBadge.textContent = "WAIVED";
        summarySetupBadge.className = "setup-badge-free";
      }
      if (summarySetupFeeVal) {
        summarySetupFeeVal.textContent = "₹0";
      }
      if (summaryBillingBreakdown) {
        summaryBillingBreakdown.textContent = "Billed annually for 12 months (₹0 setup fee) + 18% GST";
      }
    } else {
      planSubtotal = monthlyRate;
      setupFee = 5000; // One-time setup fee
      if (summaryPlanChargeLabel) {
        summaryPlanChargeLabel.textContent = PRICING[selectedPlan].name + " (1st Month Subscription)";
      }
      if (summarySetupBadge) {
        summarySetupBadge.textContent = "ONE-TIME";
        summarySetupBadge.className = "setup-badge-fee";
      }
      if (summarySetupFeeVal) {
        summarySetupFeeVal.textContent = "₹5,000";
      }
      if (summaryBillingBreakdown) {
        summaryBillingBreakdown.textContent = "1st Month (₹" + monthlyRate.toLocaleString("en-IN") + ") + One-Time Setup (₹5,000) + 18% GST";
      }
    }

    if (summaryPlanChargeVal) {
      summaryPlanChargeVal.textContent = "₹" + planSubtotal.toLocaleString("en-IN");
    }

    const subtotal = planSubtotal + setupFee;
    const gstAmount = Math.round(subtotal * 0.18);
    const totalPayable = subtotal + gstAmount;

    if (summaryGstVal) {
      summaryGstVal.textContent = "₹" + gstAmount.toLocaleString("en-IN");
    }

    if (summaryPriceNumber) {
      summaryPriceNumber.textContent = totalPayable.toLocaleString("en-IN");
    }

    // Dynamic Team Seats Update (10 licenses included in plan)
    if (summaryUsersCount) {
      const teamSizeRadio = document.querySelector('input[name="team_size"]:checked');
      const val = teamSizeRadio ? teamSizeRadio.value : "1-10";
      if (val === "10+" && customTeamSizeInput && customTeamSizeInput.value.trim()) {
        const count = parseInt(customTeamSizeInput.value.trim(), 10);
        if (!isNaN(count) && count > 10) {
          const extra = count - 10;
          summaryUsersCount.textContent = count + " Licenses (10 Inc. + " + extra + " Extra)";
        } else {
          summaryUsersCount.textContent = "10 Agent Licenses (Included)";
        }
      } else {
        summaryUsersCount.textContent = "10 Agent Licenses (Included)";
      }
    }

    updateDestinationStats();
  }

  // Upgrade Button Handler in Step 4
  if (btnUpgradeToErp) {
    btnUpgradeToErp.addEventListener("click", function () {
      selectedPlan = "erp";
      updatePlanView();
      updateSummary();
      showAlert("Upgraded to ERP Complete! Full AI automations and master tour packages are now included.", true);
    });
  }

  // Real-time domain availability check function
  function checkDomainAvailability(slug) {
    if (!slug || slug.length < 2) {
      if (subdomainAvailabilityBadge) {
        subdomainAvailabilityBadge.className = "subdomain-status-badge hidden";
      }
      isDomainAvailable = true;
      isDomainChecking = false;
      return;
    }

    if (subdomainAvailabilityBadge) {
      subdomainAvailabilityBadge.className = "subdomain-status-badge checking";
      if (subdomainStatusIcon) {
        subdomainStatusIcon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      }
      if (subdomainStatusText) {
        subdomainStatusText.textContent = "Checking...";
      }
    }
    isDomainChecking = true;

    clearTimeout(domainCheckTimer);
    domainCheckTimer = setTimeout(function () {
      fetch("/api/v3/auth/check-domain/?subdomain=" + encodeURIComponent(slug))
        .then(function (res) {
          return res.json();
        })
        .then(function (data) {
          isDomainChecking = false;
          // Guard against stale asynchronous responses
          const currentSlug = (subdomainHidden ? subdomainHidden.value : "");
          if (currentSlug !== slug) return;

          if (data && data.available) {
            isDomainAvailable = true;
            if (subdomainAvailabilityBadge) {
              subdomainAvailabilityBadge.className = "subdomain-status-badge available";
              if (subdomainStatusIcon) {
                subdomainStatusIcon.innerHTML = '<i class="fas fa-circle-check"></i>';
              }
              if (subdomainStatusText) {
                subdomainStatusText.textContent = "Available";
              }
            }
          } else {
            isDomainAvailable = false;
            if (subdomainAvailabilityBadge) {
              subdomainAvailabilityBadge.className = "subdomain-status-badge taken";
              if (subdomainStatusIcon) {
                subdomainStatusIcon.innerHTML = '<i class="fas fa-circle-xmark"></i>';
              }
              if (subdomainStatusText) {
                subdomainStatusText.textContent = (data && data.msg) ? "Taken" : "Not Available";
              }
            }
          }
        })
        .catch(function () {
          isDomainChecking = false;
          // Silent fallback on network error
        });
    }, 320);
  }

  // Subdomain live slug generator & real-time checker
  if (businessNameInput && subdomainPreview) {
    businessNameInput.addEventListener("input", function (e) {
      const cleanSlug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "");
      const displaySlug = cleanSlug || "youragency";
      subdomainPreview.textContent = displaySlug + ".aleya.in";
      if (subdomainHidden) {
        subdomainHidden.value = cleanSlug;
      }
      if (summarySubdomain) {
        summarySubdomain.textContent = displaySlug + ".aleya.in";
      }

      checkDomainAvailability(cleanSlug);
    });

    // Check if initial value already exists (e.g. from browser autofill)
    if (businessNameInput.value.trim()) {
      const initialSlug = businessNameInput.value.toLowerCase().replace(/[^a-z0-9]/g, "");
      if (subdomainHidden) subdomainHidden.value = initialSlug;
      checkDomainAvailability(initialSlug);
    }
  }

  // Billing Cycle Toggle
  if (planCycleMonthly && planCycleYearly) {
    planCycleMonthly.addEventListener("click", function () {
      billingCycle = "monthly";
      planCycleMonthly.classList.add("active");
      planCycleYearly.classList.remove("active");
      updatePlanPrices();
    });

    planCycleYearly.addEventListener("click", function () {
      billingCycle = "yearly";
      planCycleYearly.classList.add("active");
      planCycleMonthly.classList.remove("active");
      updatePlanPrices();
    });
  }

  // Switch to CRM Starter from Active ERP bar in Step 1
  if (btnSwitchToCrm) {
    btnSwitchToCrm.addEventListener("click", function () {
      selectedPlan = "crm";
      updatePlanView();
      updateSummary();
      showAlert("Switched to CRM Starter plan. You can upgrade to ERP Complete anytime.", true);
    });
  }

  // Plan Selection Cards & Upgrade Action
  if (planCardCrm && planCardErp) {
    planCardCrm.addEventListener("click", function () {
      selectedPlan = "crm";
      updatePlanView();
    });

    planCardErp.addEventListener("click", function () {
      const wasCrm = (selectedPlan === "crm");
      selectedPlan = "erp";
      updatePlanView();
      if (wasCrm) {
        showAlert("Upgraded to ERP Complete! All premium automations and master tour packages are now included.", true);
      }
    });
  }

  // Bind destination card click handler
  function initDestinationCards() {
    document.querySelectorAll(".destination-card").forEach(function (card) {
      card.addEventListener("click", function (e) {
        if (e.target.tagName.toLowerCase() === "input") return;
        e.preventDefault();
        const checkbox = card.querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = !checkbox.checked;
          if (checkbox.checked) {
            card.classList.add("selected");
          } else {
            card.classList.remove("selected");
          }
          updateDestinationStats();
        }
      });
    });
  }

  initDestinationCards();

  // Tab switching
  if (tabDomestic && tabInternational && gridDomestic && gridInternational) {
    tabDomestic.addEventListener("click", function () {
      tabDomestic.classList.add("active");
      tabInternational.classList.remove("active");
      gridDomestic.classList.add("active");
      gridInternational.classList.remove("active");
    });

    tabInternational.addEventListener("click", function () {
      tabInternational.classList.add("active");
      tabDomestic.classList.remove("active");
      gridInternational.classList.add("active");
      gridDomestic.classList.remove("active");
    });
  }

  // Quick select/deselect in active tab
  if (btnSelectAllTab) {
    btnSelectAllTab.addEventListener("click", function () {
      const activeGrid = getActiveDestGrid();
      if (!activeGrid) return;
      activeGrid.querySelectorAll(".destination-card").forEach(function (card) {
        card.classList.add("selected");
        const cb = card.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = true;
      });
      updateDestinationStats();
    });
  }

  if (btnDeselectAllTab) {
    btnDeselectAllTab.addEventListener("click", function () {
      const activeGrid = getActiveDestGrid();
      if (!activeGrid) return;
      activeGrid.querySelectorAll(".destination-card").forEach(function (card) {
        card.classList.remove("selected");
        const cb = card.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = false;
      });
      updateDestinationStats();
    });
  }

  // Sync latest destination counts live via V3 API
  fetch("/api/v3/packages/destinations/")
    .then(function (res) {
      return res.json();
    })
    .then(function (res) {
      if (res && res.status === "success" && res.data) {
        const domesticList = res.data.domestic || [];
        const intlList = res.data.international || [];

        const countDomBadge = document.getElementById("countDomesticBadge");
        if (countDomBadge) countDomBadge.textContent = domesticList.length;

        const countIntlBadge = document.getElementById("countIntlBadge");
        if (countIntlBadge) countIntlBadge.textContent = intlList.length;

        domesticList.concat(intlList).forEach(function (item) {
          const card = document.querySelector('.destination-card[data-dest-key="' + item.key + '"]');
          if (card) {
            card.setAttribute("data-packages", item.packages_count);
            const pill = card.querySelector(".dest-pkg-pill");
            if (pill) {
              const label = item.packages_count === 1 ? "1 Package" : item.packages_count + " Packages";
              pill.innerHTML = '<i class="fas fa-route"></i> ' + label;
            }
          }
        });
        updateDestinationStats();
      }
    })
    .catch(function () {
      // Fallback already pre-rendered gracefully
    });

  // Step 2: Team Size Cards & Custom Input Toggle
  document.querySelectorAll(".team-size-card, .team-size-label").forEach(function (pill) {
    pill.addEventListener("click", function () {
      document.querySelectorAll(".team-size-card, .team-size-label").forEach(function (p) {
        p.classList.remove("selected");
      });
      this.classList.add("selected");
      const radio = this.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
        if (radio.value === "10+") {
          if (customTeamSizeWrap) {
            customTeamSizeWrap.classList.remove("hidden");
            if (customTeamSizeInput) {
              setTimeout(function () {
                customTeamSizeInput.focus();
              }, 80);
            }
          }
        } else {
          if (customTeamSizeWrap) {
            customTeamSizeWrap.classList.add("hidden");
          }
        }
        updateSummary();
      }
    });
  });

  if (customTeamSizeInput) {
    customTeamSizeInput.addEventListener("input", function () {
      const val = parseInt(this.value.trim(), 10);
      const helpEl = document.getElementById("customTeamSizeHelp");
      if (helpEl) {
        if (!isNaN(val) && val > 10) {
          const extra = val - 10;
          helpEl.textContent = "Includes 10 standard plan licenses + " + extra + " additional license" + (extra > 1 ? "s" : "") + ".";
        } else if (!isNaN(val) && val <= 10) {
          helpEl.textContent = "10 licenses are already included in your plan with no extra cost.";
        } else {
          helpEl.textContent = "10 licenses included in plan. Additional licenses will be configured.";
        }
      }
      updateSummary();
    });
  }

  // Initialize UI (all elements & functions are guaranteed to be defined now!)
  updatePlanView();
  updateUI();

  // Navigation Logic
  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
          currentStep++;
          updateUI();
          window.scrollTo({ top: 0, behavior: "smooth" });
        }
      }
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      if (currentStep > 1) {
        currentStep--;
        updateUI();
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    });
  }

  function validateCurrentStep() {
    hideAlert();
    const currentStepEl = document.getElementById("step-" + currentStep);
    if (!currentStepEl) return true;

    // Step 1: Subdomain availability validation
    if (currentStep === 1) {
      if (isDomainChecking) {
        showAlert("Verifying workspace URL availability. Please wait a moment...");
        return false;
      }
      if (!isDomainAvailable) {
        const slug = (subdomainHidden ? subdomainHidden.value : "");
        showAlert("The workspace URL '" + slug + ".aleya.in' is already in use. Please choose a different agency name.");
        if (businessNameInput) businessNameInput.focus();
        return false;
      }
    }

    // Step 2 Destination Check & Custom Team Size Check
    if (currentStep === 2) {
      const selectedDests = document.querySelectorAll('input[name="destinations[]"]:checked');
      if (selectedDests.length === 0) {
        showAlert("Please select at least one tour package destination to pre-load into your workspace.");
        return false;
      }

      const teamSizeRadio = document.querySelector('input[name="team_size"]:checked');
      if (teamSizeRadio && teamSizeRadio.value === "10+") {
        const val = customTeamSizeInput ? parseInt(customTeamSizeInput.value.trim(), 10) : 0;
        if (!val || val <= 10) {
          showAlert("Please enter your team size count in numbers (minimum 11 agents for extra licenses).");
          if (customTeamSizeInput) customTeamSizeInput.focus();
          return false;
        }
      }
    }

    const requiredInputs = currentStepEl.querySelectorAll("input[required]");
    for (let i = 0; i < requiredInputs.length; i++) {
      const input = requiredInputs[i];
      if (input.type === "radio") {
        const name = input.name;
        const checked = currentStepEl.querySelector('input[name="' + name + '"]:checked');
        if (!checked) {
          showAlert("Please select an option to continue.");
          return false;
        }
      } else if (!input.value.trim()) {
        showAlert(input.getAttribute("data-error") || "Please fill in all required fields.");
        input.focus();
        return false;
      } else if (input.type === "email") {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value.trim())) {
          showAlert("Please enter a valid work email address.");
          input.focus();
          return false;
        }
      } else if (input.id === "mobile") {
        const cleanMobile = input.value.replace(/\D/g, "");
        if (cleanMobile.length < 10) {
          showAlert("Please enter a valid 10-digit mobile number.");
          input.focus();
          return false;
        }
      }
    }
    return true;
  }

  function updateUI() {
    // Show/hide step panels
    document.querySelectorAll(".onboarding-step").forEach(function (panel, idx) {
      if (idx === currentStep - 1) {
        panel.classList.add("active");
      } else {
        panel.classList.remove("active");
      }
    });

    // Update progress bar & indicators
    const progressPercent = (currentStep / totalSteps) * 100;
    if (progressBar) {
      progressBar.style.width = progressPercent + "%";
    }
    if (stepLabel) {
      stepLabel.textContent = "Step " + currentStep + " of " + totalSteps;
    }
    if (stepPercent) {
      stepPercent.textContent = Math.round(progressPercent) + "% Complete";
    }

    // Step indicators
    document.querySelectorAll(".step-indicator-item").forEach(function (item, idx) {
      item.classList.remove("active", "completed");
      if (idx + 1 === currentStep) {
        item.classList.add("active");
      } else if (idx + 1 < currentStep) {
        item.classList.add("completed");
      }
    });

    // Button states
    if (prevBtn) {
      if (currentStep === 1) {
        prevBtn.classList.add("hidden");
      } else {
        prevBtn.classList.remove("hidden");
      }
    }

    if (nextBtn && submitBtn) {
      if (currentStep === totalSteps) {
        nextBtn.classList.add("hidden");
        submitBtn.classList.remove("hidden");
        updateSummary();
      } else {
        nextBtn.classList.remove("hidden");
        submitBtn.classList.add("hidden");
      }
    }
  }

  function showAlert(msg, isSuccess) {
    if (!alertBox) return;
    alertBox.textContent = msg;
    alertBox.className = "onboarding-alert " + (isSuccess ? "success" : "error");
    alertBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function hideAlert() {
    if (!alertBox) return;
    alertBox.className = "onboarding-alert";
  }

  // ========================================================
  // FORM SUBMISSION: Payment Gateway & Package Migration Flow
  // ========================================================
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      if (!validateCurrentStep()) return;

      const businessName = (businessNameInput ? businessNameInput.value.trim() : "");
      let subdomain = (subdomainHidden ? subdomainHidden.value.trim() : "");
      if (!subdomain) {
        subdomain = businessName.toLowerCase().replace(/[^a-z0-9]/g, "");
      }

      const firstName = (document.getElementById("firstName") ? document.getElementById("firstName").value.trim() : "");
      const lastName = (document.getElementById("lastName") ? document.getElementById("lastName").value.trim() : "");
      const email = (document.getElementById("email") ? document.getElementById("email").value.trim().toLowerCase() : "");
      const mobile = (document.getElementById("mobile") ? document.getElementById("mobile").value.replace(/\D/g, "") : "");

      const categoryChecked = document.querySelector('input[name="category"]:checked');
      const category = categoryChecked ? categoryChecked.value : "leisure";

      const teamSizeChecked = document.querySelector('input[name="team_size"]:checked');
      let finalTeamSize = teamSizeChecked ? teamSizeChecked.value : "1-10";
      if (finalTeamSize === "10+" && customTeamSizeInput && customTeamSizeInput.value.trim()) {
        const count = parseInt(customTeamSizeInput.value.trim(), 10);
        const extra = count > 10 ? (count - 10) : 0;
        finalTeamSize = count + " agents (10 plan" + (extra > 0 ? " + " + extra + " extra" : "") + ")";
      } else {
        finalTeamSize = "10 agent licenses (included in plan)";
      }

      // Collect selected tour destinations
      const selectedDestinations = [];
      document.querySelectorAll('input[name="destinations[]"]:checked').forEach(function (cb) {
        selectedDestinations.push(cb.value);
      });

      // Show Loading Overlay while preparing checkout
      if (loadingOverlay) {
        loadingOverlay.classList.add("active");
      }
      if (loadingStatusText) {
        loadingStatusText.textContent = "Connecting to payment gateway...";
      }

      // 1. Create Razorpay Order
      fetch("/api/v3/auth/create-order/", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          plan: selectedPlan,
          billing_cycle: billingCycle,
          business_name: businessName,
          email: email,
          mobile: mobile,
        }),
      })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, status: res.status, data: data };
          });
        })
        .then(function (res) {
          const resPayload = res.data || {};
          const orderData = resPayload.data || resPayload;

          if (!res.ok || resPayload.status !== "success" || !orderData || !orderData.key) {
            if (loadingOverlay) loadingOverlay.classList.remove("active");
            showAlert(resPayload.msg || "Unable to initiate payment. Please try again.");
            return;
          }

          // Check if Razorpay SDK is loaded
          if (typeof Razorpay === "undefined") {
            if (loadingOverlay) loadingOverlay.classList.remove("active");
            showAlert("Payment gateway script could not be loaded. Please disable ad-blockers and try again.");
            return;
          }

          // Hide overlay to let Razorpay modal take over smoothly
          if (loadingOverlay) loadingOverlay.classList.remove("active");

          // 2. Open Razorpay Checkout Modal
          const rzpOptions = {
            key: orderData.key,
            amount: orderData.amount,
            currency: orderData.currency || "INR",
            name: orderData.name || "Aleya Travel CRM",
            description: orderData.description || (PRICING[selectedPlan].name + " Plan"),
            prefill: orderData.prefill || {
              name: (firstName + " " + lastName).trim() || businessName,
              email: email,
              contact: mobile,
            },
            notes: {
              plan: selectedPlan,
              cycle: billingCycle,
              business_name: businessName,
              subdomain: subdomain,
            },
            theme: {
              color: "#00647a",
            },
            modal: {
              ondismiss: function () {
                if (loadingOverlay) loadingOverlay.classList.remove("active");
                showAlert("Payment process paused. You can click 'Setup My Travel CRM' whenever you are ready.", false);
              },
            },
            handler: function (paymentResponse) {
              // 3. Payment SUCCESS -> Start Migration and Provisioning Flow!
              initiateWorkspaceProvisioning(paymentResponse, {
                businessName: businessName,
                subdomain: subdomain,
                firstName: firstName,
                lastName: lastName,
                email: email,
                mobile: mobile,
                plan: selectedPlan,
                billingCycle: billingCycle,
                category: category,
                teamSize: finalTeamSize,
                selectedDestinations: selectedDestinations,
              });
            },
          };

          if (orderData.order_id) {
            rzpOptions.order_id = orderData.order_id;
          }

          const rzp = new Razorpay(rzpOptions);
          rzp.on("payment.failed", function (response) {
            if (loadingOverlay) loadingOverlay.classList.remove("active");
            const errDesc = response.error && response.error.description ? response.error.description : "Payment failed. Please try again.";
            showAlert(errDesc);
          });
          rzp.open();
        })
        .catch(function (err) {
          if (loadingOverlay) loadingOverlay.classList.remove("active");
          showAlert("Connection error while reaching payment gateway. Please check your internet connection.");
        });
    });
  }

  // Provisioning & Tour Package Migration Orchestrator
  function initiateWorkspaceProvisioning(paymentResponse, context) {
    if (loadingOverlay) {
      loadingOverlay.classList.add("active");
    }

    const destCountText = context.selectedDestinations.length > 0 ? context.selectedDestinations.length : "all";
    const statusSequence = [
      "Payment verified! Creating your secure account...",
      "Migrating master tour packages for " + destCountText + " selected destinations...",
      "Importing day-wise itineraries, supplier hotels & rates...",
      "Configuring your new Travel CRM workspace...",
    ];

    let seqIndex = 0;
    if (loadingStatusText) {
      loadingStatusText.textContent = statusSequence[0];
    }
    const statusInterval = setInterval(function () {
      seqIndex++;
      if (loadingStatusText && seqIndex < statusSequence.length) {
        loadingStatusText.textContent = statusSequence[seqIndex];
      }
    }, 800);

    const registerPayload = {
      app_type: "aleya",
      business_name: context.businessName,
      subdomain: context.subdomain,
      first_name: context.firstName,
      last_name: context.lastName,
      email: context.email,
      mobile: context.mobile,
      plan: context.plan,
      billing_cycle: context.billingCycle,
      category: context.category,
      destinations: context.selectedDestinations,
      team_size: context.teamSize,
      payment_id: paymentResponse ? paymentResponse.razorpay_payment_id : "",
      order_id: paymentResponse ? paymentResponse.razorpay_order_id : "",
      payment_signature: paymentResponse ? paymentResponse.razorpay_signature : "",
      redirect_url: "/dashboard/",
    };

    fetch("/api/v3/auth/register/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(registerPayload),
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, status: res.status, data: data };
        });
      })
      .then(function (result) {
        // Now trigger workspace package migration
        return fetch("/api/v3/auth/onboarding/", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            app_type: "aleya",
            business_name: context.businessName,
            subdomain: context.subdomain,
            setup_email: context.email,
            category: context.category,
            destinations: context.selectedDestinations,
            theme_color: "#00647a",
            payment_id: paymentResponse ? paymentResponse.razorpay_payment_id : "",
          }),
        })
          .then(function (onbRes) {
            return onbRes.json();
          })
          .then(function (onbData) {
            clearInterval(statusInterval);
            if (loadingStatusText) {
              loadingStatusText.textContent = "Workspace ready! Redirecting to dashboard...";
            }
            setTimeout(function () {
              window.location.href = (result.data && result.data.redirect_url) || (onbData && onbData.redirect_url) || "/dashboard/";
            }, 600);
          });
      })
      .catch(function (err) {
        clearInterval(statusInterval);
        // Even if secondary call has glitch, attempt redirect
        if (loadingStatusText) {
          loadingStatusText.textContent = "Finalizing dashboard access...";
        }
        setTimeout(function () {
          window.location.href = "/dashboard/";
        }, 800);
      });
  }
});
