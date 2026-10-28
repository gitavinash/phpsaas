/**
 * V3 Executive Onboarding & Workspace Setup Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/website-setup.js
 * Strict External JavaScript - Zero Inline Event Handlers
 */

(function ($) {
  "use strict";

  const WebsiteSetup = {
    currentStep: 1,
    totalSteps: 3,
    appType: "aleya",
    setupEmail: "",
    selectedPackageId: 0,
    selectedPackagePrice: 0,
    selectedPackageTitle: "",
    billingCycle: "yearly",
    copyTourPackages: true,
    domesticDestinations: [],
    intlDestinations: [],
    selectedDestinations: new Set(),
    activeDestTab: "domestic",

    // Subdomain Live Availability State
    subdomainDebounceTimer: null,
    subdomainAvailable: null,

    // Color Palettes & Shades System
    activeFamily: "teal",
    selectedHex: "#00647a",
    selectedShadeName: "Teal 700",

    colorPalettes: {
      teal: {
        name: "Teal",
        shades: [
          { level: "400", hex: "#14b8a6", name: "Teal 400" },
          { level: "500", hex: "#0d9488", name: "Teal 500" },
          { level: "600", hex: "#0087a3", name: "Teal 600" },
          { level: "700", hex: "#00647a", name: "Teal 700" },
          { level: "800", hex: "#004d5e", name: "Teal 800" },
          { level: "900", hex: "#003744", name: "Teal 900" }
        ]
      },
      blue: {
        name: "Ocean Blue",
        shades: [
          { level: "400", hex: "#60a5fa", name: "Blue 400" },
          { level: "500", hex: "#3b82f6", name: "Blue 500" },
          { level: "600", hex: "#2563eb", name: "Blue 600" },
          { level: "700", hex: "#1d4ed8", name: "Blue 700" },
          { level: "800", hex: "#1e40af", name: "Blue 800" },
          { level: "900", hex: "#1e3a8a", name: "Blue 900" }
        ]
      },
      indigo: {
        name: "Royal Indigo",
        shades: [
          { level: "400", hex: "#818cf8", name: "Indigo 400" },
          { level: "500", hex: "#6366f1", name: "Indigo 500" },
          { level: "600", hex: "#4f46e5", name: "Indigo 600" },
          { level: "700", hex: "#4338ca", name: "Indigo 700" },
          { level: "800", hex: "#3730a3", name: "Indigo 800" },
          { level: "900", hex: "#312e81", name: "Indigo 900" }
        ]
      },
      emerald: {
        name: "Emerald Green",
        shades: [
          { level: "400", hex: "#34d399", name: "Emerald 400" },
          { level: "500", hex: "#10b981", name: "Emerald 500" },
          { level: "600", hex: "#059669", name: "Emerald 600" },
          { level: "700", hex: "#047857", name: "Emerald 700" },
          { level: "800", hex: "#065f46", name: "Emerald 800" },
          { level: "900", hex: "#064e3b", name: "Emerald 900" }
        ]
      },
      rose: {
        name: "Rose Red",
        shades: [
          { level: "400", hex: "#fb7185", name: "Rose 400" },
          { level: "500", hex: "#f43f5e", name: "Rose 500" },
          { level: "600", hex: "#e11d48", name: "Rose 600" },
          { level: "700", hex: "#be123c", name: "Rose 700" },
          { level: "800", hex: "#9f1239", name: "Rose 800" },
          { level: "900", hex: "#881337", name: "Rose 900" }
        ]
      },
      amber: {
        name: "Sunset Amber",
        shades: [
          { level: "400", hex: "#fbbf24", name: "Amber 400" },
          { level: "500", hex: "#f59e0b", name: "Amber 500" },
          { level: "600", hex: "#d97706", name: "Amber 600" },
          { level: "700", hex: "#b45309", name: "Amber 700" },
          { level: "800", hex: "#92400e", name: "Amber 800" },
          { level: "900", hex: "#78350f", name: "Amber 900" }
        ]
      },
      purple: {
        name: "Violet Purple",
        shades: [
          { level: "400", hex: "#a78bfa", name: "Purple 400" },
          { level: "500", hex: "#8b5cf6", name: "Purple 500" },
          { level: "600", hex: "#7c3aed", name: "Purple 600" },
          { level: "700", hex: "#6d28d9", name: "Purple 700" },
          { level: "800", hex: "#5b21b6", name: "Purple 800" },
          { level: "900", hex: "#4c1d95", name: "Purple 900" }
        ]
      },
      slate: {
        name: "Charcoal Slate",
        shades: [
          { level: "400", hex: "#94a3b8", name: "Slate 400" },
          { level: "500", hex: "#64748b", name: "Slate 500" },
          { level: "600", hex: "#475569", name: "Slate 600" },
          { level: "700", hex: "#334155", name: "Slate 700" },
          { level: "800", hex: "#1e293b", name: "Slate 800" },
          { level: "900", hex: "#0f172a", name: "Slate 900" }
        ]
      }
    },

    init: function () {
      const self = this;
      self.appType = $("#setupAppType").val() || "aleya";
      self.setupEmail = $("#setupEmail").val() || "";

      // Bind events
      self.bindEvents();

      // Initialize Color Shades System
      self.initColorPicker();

      // Fetch pre-registered onboarding info (e.g. Agency Name from register step)
      self.fetchOnboardingInfo();

      // Check initial subdomain if populated
      const initialSub = $.trim($("#setupSubdomain").val());
      if (initialSub && initialSub.length >= 3) {
        self.checkSubdomainAvailability(initialSub);
      }

      // Load initial data
      if (self.appType === "aleya") {
        self.fetchDestinations();
      } else {
        self.totalSteps = 2;
        $("#pillStep2").hide();
      }

      self.fetchPackages();
      self.updateStepView();
    },

    bindEvents: function () {
      const self = this;

      // Next / Prev step navigation
      $(document).on("click", "#btnNextStep", function () {
        self.goNextStep();
      });

      $(document).on("click", "#btnPrevStep", function () {
        self.goPrevStep();
      });

      // Subdomain live availability input
      $(document).on("input", "#setupSubdomain", function () {
        const raw = $(this).val();
        const clean = raw.toLowerCase().replace(/[^a-z0-9-]/g, "");
        if (raw !== clean) {
          $(this).val(clean);
        }
        self.handleSubdomainInput(clean);
      });

      // Subdomain blur check
      $(document).on("blur", "#setupSubdomain", function () {
        const clean = $.trim($(this).val());
        if (clean.length >= 3) {
          self.checkSubdomainAvailability(clean);
        }
      });

      // Color Family button click
      $(document).on("click", ".color-family-btn", function () {
        const family = $(this).data("family");
        if (!family || !self.colorPalettes[family]) return;

        $(".color-family-btn").removeClass("active");
        $(this).addClass("active");

        self.activeFamily = family;
        self.renderShades(family);

        // Auto-select standard shade (e.g. 700) for this family
        const palette = self.colorPalettes[family];
        const defaultShade = palette.shades[3] || palette.shades[0];
        self.applyThemeColor(defaultShade.hex, defaultShade.name);
      });

      // Color shade chip click
      $(document).on("click", ".setup-shade-item", function () {
        const hex = $(this).data("hex");
        const name = $(this).data("name");
        if (hex) {
          self.applyThemeColor(hex, name);
        }
      });

      // Native color spectrum picker change
      $(document).on("input change", "#nativeColorPicker", function () {
        const hex = $(this).val();
        self.applyThemeColor(hex, "Custom Shade");
      });

      // Direct Hex code input
      $(document).on("input", "#setupHexInput", function () {
        let val = $(this).val().replace(/[^0-9a-fA-F]/g, "");
        if (val.length === 6) {
          self.applyThemeColor("#" + val, "Custom Hex");
        }
      });

      // Toggle copying master tour packages
      $(document).on("change", "#toggleCopyTourPackages", function () {
        self.copyTourPackages = $(this).is(":checked");
        if (self.copyTourPackages) {
          $(".tour-copy-toggle-card").addClass("checked");
          $("#destinationsContainer").slideDown(200);
        } else {
          $(".tour-copy-toggle-card").removeClass("checked");
          $("#destinationsContainer").slideUp(200);
        }
      });

      // Search destination live filter
      $(document).on("input", "#destSearchInput", function () {
        self.renderDestinations();
      });

      // Destination tabs
      $(document).on("click", "#tabDomestic", function () {
        self.activeDestTab = "domestic";
        $(".dest-tab-btn").removeClass("active");
        $(this).addClass("active");
        self.renderDestinations();
      });

      $(document).on("click", "#tabInternational", function () {
        self.activeDestTab = "intl";
        $(".dest-tab-btn").removeClass("active");
        $(this).addClass("active");
        self.renderDestinations();
      });

      // Select / Deselect All destinations in active tab
      $(document).on("click", "#btnSelectAllDest", function () {
        const list = self.activeDestTab === "domestic" ? self.domesticDestinations : self.intlDestinations;
        list.forEach(function (d) {
          self.selectedDestinations.add(d.key);
        });
        self.renderDestinations();
      });

      $(document).on("click", "#btnClearAllDest", function () {
        const list = self.activeDestTab === "domestic" ? self.domesticDestinations : self.intlDestinations;
        list.forEach(function (d) {
          self.selectedDestinations.delete(d.key);
        });
        self.renderDestinations();
      });

      // Toggle individual destination
      $(document).on("click", ".destination-card", function (e) {
        if ($(e.target).is("input[type='checkbox']")) {
          const key = $(this).data("key");
          if ($(e.target).is(":checked")) {
            self.selectedDestinations.add(key);
            $(this).addClass("selected");
          } else {
            self.selectedDestinations.delete(key);
            $(this).removeClass("selected");
          }
          self.updateDestinationStats();
          return;
        }

        const key = $(this).data("key");
        if (!key) return;

        if (self.selectedDestinations.has(key)) {
          self.selectedDestinations.delete(key);
          $(this).removeClass("selected");
          $(this).find(".dest-card-checkbox").prop("checked", false);
        } else {
          self.selectedDestinations.add(key);
          $(this).addClass("selected");
          $(this).find(".dest-card-checkbox").prop("checked", true);
        }
        self.updateDestinationStats();
      });

      // Plan selection from package cards
      $(document).on("click", ".pkg-card", function () {
        const pkgId = $(this).data("id");
        const price = parseInt($(this).data("price"), 10) || 0;
        const title = $(this).data("title") || "Workspace Plan";

        self.selectedPackageId = pkgId;
        self.selectedPackagePrice = price;
        self.selectedPackageTitle = title;

        $(".pkg-card").removeClass("selected");
        $(this).addClass("selected");

        self.updateOrderSummary();
      });

      // Form submission
      $(document).on("submit", "#formWebsiteSetup", function (e) {
        e.preventDefault();
        self.submitOnboarding();
      });
    },

    /* ==========================================================================
       Color Shades Engine
       ========================================================================== */
    initColorPicker: function () {
      const self = this;
      self.activeFamily = "teal";
      self.selectedHex = "#00647a";
      self.selectedShadeName = "Teal 700";
      self.renderShades(self.activeFamily);
      self.applyThemeColor(self.selectedHex, self.selectedShadeName);
    },

    renderShades: function (familyName) {
      const self = this;
      const palette = self.colorPalettes[familyName];
      if (!palette || !palette.shades) return;

      const $grid = $("#shadesPaletteGrid");
      $grid.empty();

      palette.shades.forEach(function (shade) {
        const isCurrent = (shade.hex.toLowerCase() === self.selectedHex.toLowerCase());
        const activeClass = isCurrent ? "active" : "";

        const itemHtml = `
          <button type="button" class="setup-shade-item ${activeClass}" data-hex="${shade.hex}" data-name="${shade.name}" data-level="${shade.level}" title="${shade.name}">
            <span class="shade-color-block" style="background-color: ${shade.hex};">
              <i class="fi fi-rr-check shade-check-icon"></i>
            </span>
            <span class="shade-tone-level">${shade.level}</span>
            <span class="shade-hex-label">${shade.hex}</span>
          </button>
        `;
        $grid.append(itemHtml);
      });
    },

    applyThemeColor: function (hex, name) {
      const self = this;
      if (!hex) return;

      self.selectedHex = hex;
      self.selectedShadeName = name || hex;

      // Update hidden form field
      $("#selectedThemeColor").val(hex);

      // Update header shade pill
      $("#activeShadeDot").css("background-color", hex);
      $("#activeShadeName").text(self.selectedShadeName);
      $("#activeShadeHex").text(hex);

      // Update native color picker & hex box
      $("#nativeColorPicker").val(hex);
      const cleanHex = hex.replace("#", "");
      $("#setupHexInput").val(cleanHex);

      // Update active highlight in shades palette
      $(".setup-shade-item").each(function () {
        const itemHex = $(this).data("hex");
        if (itemHex && itemHex.toLowerCase() === hex.toLowerCase()) {
          $(this).addClass("active");
        } else {
          $(this).removeClass("active");
        }
      });
    },

    /* ==========================================================================
       Subdomain Live Availability Engine
       ========================================================================== */
    handleSubdomainInput: function (slug) {
      const self = this;
      clearTimeout(self.subdomainDebounceTimer);

      if (!slug || slug.length === 0) {
        self.subdomainAvailable = null;
        self.showSubdomainFeedback("default", "fi fi-rr-info", "Enter 3+ characters to verify live availability");
        return;
      }

      if (slug.length < 3) {
        self.subdomainAvailable = false;
        self.showSubdomainFeedback("invalid", "fi fi-rr-info", "Subdomain must be at least 3 characters");
        return;
      }

      // Show checking state
      const suffix = (self.appType === "aleya") ? ".aleya.in" : ".blumox.com";
      self.showSubdomainFeedback("checking", "fi fi-rr-spinner fi-spin", `Checking availability for ${slug}${suffix}...`);

      // Debounce API check
      self.subdomainDebounceTimer = setTimeout(function () {
        self.checkSubdomainAvailability(slug);
      }, 350);
    },

    checkSubdomainAvailability: function (slug) {
      const self = this;
      if (!slug || slug.length < 3) return;

      $.ajax({
        url: "/api/v3/auth/check-domain/",
        type: "GET",
        data: {
          subdomain: slug,
          app: self.appType,
          setup_email: self.setupEmail
        },
        dataType: "json",
        success: function (res) {
          if (res && res.available) {
            self.subdomainAvailable = true;
            self.showSubdomainFeedback("available", "fi fi-rr-check-circle", `${res.display || (slug + ".aleya.in")} is available!`);
          } else {
            self.subdomainAvailable = false;
            self.showSubdomainFeedback("unavailable", "fi fi-rr-cross-circle", (res && res.msg) ? res.msg : "This workspace URL is already taken.");
          }
        },
        error: function () {
          // If network error, allow user to proceed
          self.subdomainAvailable = true;
          self.showSubdomainFeedback("available", "fi fi-rr-check-circle", `${slug} is available`);
        }
      });
    },

    showSubdomainFeedback: function (statusClass, iconClass, message) {
      const $wrap = $("#subdomainFeedback");
      const $icon = $("#subdomainFeedbackIcon");
      const $text = $("#subdomainFeedbackText");

      $wrap.removeClass("checking available unavailable invalid");
      if (statusClass !== "default") {
        $wrap.addClass(statusClass);
      }

      $icon.attr("class", `feedback-icon ${iconClass}`);
      $text.text(message);
    },

    /* ==========================================================================
       Onboarding Pre-fill & API
       ========================================================================== */
    fetchOnboardingInfo: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/auth/onboarding/",
        type: "GET",
        data: {
          setup_email: self.setupEmail
        },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            if (res.business_name) {
              $("#setupBizName").val(res.business_name);
              $("#agencyNameDisplay").text(res.business_name);

              const currentSub = $("#setupSubdomain").val();
              if (!currentSub || currentSub.length < 2) {
                const autoSub = res.subdomain || res.business_name.toLowerCase().replace(/[^a-z0-9]/g, "");
                $("#setupSubdomain").val(autoSub);
                if (autoSub.length >= 3) {
                  self.checkSubdomainAvailability(autoSub);
                }
              }
            }
            if (res.email && !self.setupEmail) {
              self.setupEmail = res.email;
              $("#setupEmail").val(res.email);
            }
          }
        },
        error: function () {
          // Graceful fallback
        }
      });
    },

    goNextStep: function () {
      const self = this;
      self.hideAlert();

      if (self.currentStep === 1) {
        const subdomain = $.trim($("#setupSubdomain").val());

        if (!subdomain) {
          self.showAlert("warning", "Please specify your preferred workspace subdomain.");
          $("#setupSubdomain").trigger("focus");
          return;
        }

        if (subdomain.length < 3) {
          self.showAlert("warning", "Workspace subdomain must be at least 3 characters.");
          $("#setupSubdomain").trigger("focus");
          return;
        }

        if (self.subdomainAvailable === false) {
          self.showAlert("warning", "This subdomain is already taken. Please choose an available name.");
          $("#setupSubdomain").trigger("focus");
          return;
        }

        // Fallback for hidden bizName
        if (!$.trim($("#setupBizName").val())) {
          $("#setupBizName").val(subdomain);
        }

        self.currentStep = (self.appType === "aleya") ? 2 : 3;
        self.updateStepView();
      } else if (self.currentStep === 2) {
        self.currentStep = 3;
        self.updateStepView();
      }
    },

    goPrevStep: function () {
      const self = this;
      self.hideAlert();

      if (self.currentStep === 3) {
        self.currentStep = (self.appType === "aleya") ? 2 : 1;
      } else if (self.currentStep === 2) {
        self.currentStep = 1;
      }
      self.updateStepView();
    },

    updateStepView: function () {
      const self = this;
      $(".setup-step-panel").removeClass("active");
      $("#setupStep" + self.currentStep).addClass("active");

      // Update progress bar
      let percent = 33;
      if (self.currentStep === 2) percent = 66;
      if (self.currentStep === 3) percent = 100;
      $("#progressBarFill").css("width", percent + "%");
      $("#stepLabel").text("Step " + self.currentStep + " of " + self.totalSteps);

      // Update pills
      $(".setup-step-pill").removeClass("active completed");
      for (let i = 1; i < self.currentStep; i++) {
        $("#pillStep" + i).addClass("completed");
      }
      $("#pillStep" + self.currentStep).addClass("active");

      // Controls visibility
      if (self.currentStep === 1) {
        $("#btnPrevStep").addClass("hidden");
        $("#btnNextStep").removeClass("hidden");
        $("#btnSubmitSetup").addClass("hidden");
      } else if (self.currentStep === 3) {
        $("#btnPrevStep").removeClass("hidden");
        $("#btnNextStep").addClass("hidden");
        $("#btnSubmitSetup").removeClass("hidden");
      } else {
        $("#btnPrevStep").removeClass("hidden");
        $("#btnNextStep").removeClass("hidden");
        $("#btnSubmitSetup").addClass("hidden");
      }

      window.scrollTo({ top: 0, behavior: "smooth" });
    },

    fetchDestinations: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/packages/destinations/",
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            const dataObj = res.data || res;
            self.domesticDestinations = dataObj.domestic || [];
            self.intlDestinations = dataObj.international || [];

            $("#domesticBadge").text(self.domesticDestinations.length);
            $("#intlBadge").text(self.intlDestinations.length);

            // Pre-select popular / default destinations
            self.domesticDestinations.forEach(function (d) {
              if (d.default) {
                self.selectedDestinations.add(d.key);
              }
            });
            self.intlDestinations.forEach(function (d) {
              if (d.default) {
                self.selectedDestinations.add(d.key);
              }
            });

            // Fallback: If none was pre-selected, select popular domestic ones
            if (self.selectedDestinations.size === 0) {
              self.domesticDestinations.slice(0, 8).forEach(function (d) {
                self.selectedDestinations.add(d.key);
              });
            }

            self.renderDestinations();
            self.updateDestinationStats();
          }
        },
        error: function () {
          // Fallback domestic destinations
          self.domesticDestinations = [
            { key: "himachal-pradesh", name: "Himachal Pradesh", packages_count: 31, highlights: "Manali, Shimla, Spiti Valley, Kasol" },
            { key: "rajasthan", name: "Rajasthan", packages_count: 29, highlights: "Jaipur, Udaipur, Jodhpur, Jaisalmer" },
            { key: "kerala", name: "Kerala", packages_count: 23, highlights: "Munnar, Alleppey, Thekkady, Kovalam" },
            { key: "uttarakhand", name: "Uttarakhand", packages_count: 7, highlights: "Nainital, Mussoorie, Rishikesh, Corbett" },
            { key: "ladakh-kashmir", name: "Ladakh & Kashmir", packages_count: 6, highlights: "Leh, Pangong, Gulmarg, Srinagar" },
            { key: "goa", name: "Goa", packages_count: 4, highlights: "North & South Goa Beaches, Cruises" },
            { key: "gujarat", name: "Gujarat", packages_count: 3, highlights: "Rann of Kutch, Dwarka, Somnath" },
            { key: "sikkim-darjeeling", name: "Sikkim & Darjeeling", packages_count: 1, highlights: "Gangtok, Pelling, Tsomgo Lake" },
            { key: "andaman-nicobar", name: "Andaman & Nicobar", packages_count: 1, highlights: "Havelock, Neil Island, Port Blair" },
            { key: "meghalaya-northeast", name: "Meghalaya & Northeast", packages_count: 1, highlights: "Shillong, Cherrapunji, Dawki" }
          ];
          self.intlDestinations = [
            { key: "vietnam", name: "Vietnam", packages_count: 3, highlights: "Hanoi, Da Nang, Ha Long Bay" },
            { key: "bali", name: "Bali (Indonesia)", packages_count: 2, highlights: "Ubud, Kuta, Seminyak, Nusa Penida" },
            { key: "dubai", name: "Dubai (UAE)", packages_count: 2, highlights: "Burj Khalifa, Desert Safari, Marina" },
            { key: "maldives", name: "Maldives", packages_count: 1, highlights: "Overwater Villas, Male, Water Sports" },
            { key: "thailand", name: "Thailand", packages_count: 1, highlights: "Phuket, Bangkok, Pattaya, Krabi" },
            { key: "japan", name: "Japan", packages_count: 1, highlights: "Tokyo, Kyoto, Mount Fuji, Osaka" }
          ];
          $("#domesticBadge").text(self.domesticDestinations.length);
          $("#intlBadge").text(self.intlDestinations.length);
          self.domesticDestinations.slice(0, 6).forEach(d => self.selectedDestinations.add(d.key));
          self.renderDestinations();
          self.updateDestinationStats();
        }
      });
    },

    renderDestinations: function () {
      const self = this;
      const list = self.activeDestTab === "domestic" ? self.domesticDestinations : self.intlDestinations;
      const $grid = $("#destinationsGrid");
      $grid.empty();

      const searchQuery = $.trim($("#destSearchInput").val() || "").toLowerCase();

      const filteredList = list.filter(function (dest) {
        if (!searchQuery) return true;
        const nameMatch = (dest.name || "").toLowerCase().includes(searchQuery);
        const highlightsMatch = (dest.highlights || "").toLowerCase().includes(searchQuery);
        return nameMatch || highlightsMatch;
      });

      if (!filteredList || filteredList.length === 0) {
        $grid.html(`
          <div class="dest-empty-box">
            <i class="fi fi-rr-search"></i>
            <p class="dest-empty-msg">${searchQuery ? `No destinations matching "${searchQuery}"` : "No destinations available in this category."}</p>
          </div>
        `);
        self.updateDestinationStats();
        return;
      }

      filteredList.forEach(function (dest) {
        const isChecked = self.selectedDestinations.has(dest.key);
        const cardClass = isChecked ? "destination-card selected" : "destination-card";
        const pkgCount = dest.packages_count || dest.count || 1;
        const highlights = dest.highlights ? dest.highlights : "";

        const cardHtml = `
          <div class="${cardClass}" data-key="${dest.key}">
            <div class="dest-card-header">
              <div class="dest-card-title-group">
                <span class="dest-card-name">${dest.name}</span>
                <span class="dest-card-pkg-pill">${pkgCount} ${pkgCount === 1 ? 'Tour' : 'Tours'}</span>
              </div>
              <input type="checkbox" class="dest-card-checkbox" ${isChecked ? "checked" : ""}>
            </div>
            ${highlights ? `<div class="dest-card-highlights" title="${highlights}">${highlights}</div>` : ""}
          </div>
        `;
        $grid.append(cardHtml);
      });

      self.updateDestinationStats();
    },

    updateDestinationStats: function () {
      const self = this;
      const totalSelected = self.selectedDestinations.size;

      let totalPkgs = 0;
      const allDests = [...self.domesticDestinations, ...self.intlDestinations];
      allDests.forEach(function (d) {
        if (self.selectedDestinations.has(d.key)) {
          totalPkgs += (d.packages_count || d.count || 1);
        }
      });

      $("#destSelectedCountText").text(`${totalSelected} destinations selected`);
      $("#destCatalogStats").text(`${totalPkgs} ready tour packages to clone`);
    },

    fetchPackages: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/packages/app-packages/",
        type: "GET",
        data: {
          app_type: self.appType
        },
        dataType: "json",
        success: function (res) {
          const pkgs = (res && (res.packages || (res.data && res.data.packages) || res.data)) || [];
          if (Array.isArray(pkgs) && pkgs.length > 0) {
            self.renderPackages(pkgs);
          } else {
            self.renderFallbackPackages();
          }
        },
        error: function () {
          self.renderFallbackPackages();
        }
      });
    },

    renderFallbackPackages: function () {
      const self = this;
      self.renderPackages([
        {
          id: 6,
          package_id: 6,
          title: "CRM Starter",
          package_title: "CRM Starter",
          description: "Perfect for boutique agencies & independent travel planners.",
          package_desc: "Perfect for boutique agencies & independent travel planners.",
          price: 1499,
          price_inr: 1499,
          package_price: 1499,
          is_popular: 0,
          features: ["Unlimited Leads & CRM", "Ready Tour Itineraries", "Customer Invoicing", "WhatsApp Quotations", "10 Executive Seats"]
        },
        {
          id: 7,
          package_id: 7,
          title: "ERP Complete",
          package_title: "ERP Complete",
          description: "End-to-end travel operations, accounting & vendor vouchers.",
          package_desc: "End-to-end travel operations, accounting & vendor vouchers.",
          price: 3999,
          price_inr: 3999,
          package_price: 3999,
          is_popular: 1,
          features: ["Everything in Starter", "Fleet & Driver Dispatch", "Hotel & Supplier Vouchers", "Automated Booking Engine", "Custom Subdomain & Brand"]
        },
        {
          id: 8,
          package_id: 8,
          title: "Enterprise Edition",
          package_title: "Enterprise Edition",
          description: "Multi-branch travel networks, high volume & custom white-label.",
          package_desc: "Multi-branch travel networks, high volume & custom white-label.",
          price: 9999,
          price_inr: 9999,
          package_price: 9999,
          is_popular: 0,
          features: ["Everything in ERP", "Multi-Branch & Unlimited Users", "Custom Domain Mapping", "Dedicated Account Manager", "Priority 24/7 SLA"]
        }
      ]);
    },

    renderPackages: function (packages) {
      const self = this;
      const $grid = $("#packagesGrid");
      $grid.empty();

      if (!packages || !Array.isArray(packages) || packages.length === 0) {
        self.renderFallbackPackages();
        return;
      }

      packages.forEach(function (pkg, index) {
        const pkgId = pkg.id || pkg.package_id || (index + 1);
        const pkgTitle = pkg.title || pkg.package_title || pkg.name || ("Workspace Plan " + (index + 1));
        const rawPrice = pkg.price_inr ?? pkg.package_price ?? pkg.price ?? 0;
        let pkgPrice = parseInt(rawPrice, 10);
        if (isNaN(pkgPrice) || !isFinite(pkgPrice) || pkgPrice <= 0) {
          pkgPrice = (index === 0 ? 1499 : (index === 1 ? 3999 : 9999));
        }

        const pkgDesc = pkg.description || pkg.package_desc || pkg.desc || "Comprehensive workspace edition with premium business tools.";
        const isPopular = (pkg.is_popular == 1 || index === 1);
        const isSelected = (index === 0);

        if (isSelected) {
          self.selectedPackageId = pkgId;
          self.selectedPackagePrice = pkgPrice;
          self.selectedPackageTitle = pkgTitle;
        }

        let features = Array.isArray(pkg.features) && pkg.features.length > 0 ? pkg.features : [];
        if (features.length === 0) {
          if (pkgPrice <= 2000) {
            features = ["Unlimited Leads & CRM", "Ready Tour Itineraries", "Customer Invoicing", "WhatsApp Quotations", "10 Executive Seats"];
          } else if (pkgPrice <= 5000) {
            features = ["Everything in Starter", "Fleet & Driver Dispatch", "Hotel & Supplier Vouchers", "Automated Booking Engine", "Custom Subdomain & Brand"];
          } else {
            features = ["Everything in ERP", "Multi-Branch & Unlimited Users", "Custom Domain Mapping", "Dedicated Account Manager", "Priority 24/7 SLA"];
          }
        }

        const featureList = features.map(f => `<li><i class="fi fi-rr-check"></i> <span>${f}</span></li>`).join("");

        const cardHtml = `
          <div class="pkg-card ${isSelected ? "selected" : ""}" data-id="${pkgId}" data-price="${pkgPrice}" data-title="${pkgTitle}">
            ${isPopular ? '<span class="pkg-badge-popular">Most Popular</span>' : ""}
            <div class="pkg-header">
              <h3 class="pkg-title">${pkgTitle}</h3>
              <p class="pkg-desc">${pkgDesc}</p>
            </div>
            <div class="pkg-price-wrap">
              <span class="pkg-price">₹${pkgPrice.toLocaleString()}</span>
              <span class="pkg-period">/ month</span>
            </div>
            <ul class="pkg-features">
              ${featureList}
            </ul>
          </div>
        `;
        $grid.append(cardHtml);
      });

      self.updateOrderSummary();
    },

    updateOrderSummary: function () {
      const self = this;
      const basePrice = parseInt(self.selectedPackagePrice, 10) || 0;
      const gst = Math.round(basePrice * 0.18);
      const total = basePrice + gst;

      $("#summaryPlanTitle").text(self.selectedPackageTitle || "Workspace Plan");
      $("#summaryPlanFee").text("₹" + basePrice.toLocaleString());
      $("#summaryGst").text("₹" + gst.toLocaleString());
      $("#summaryTotal").text("₹" + total.toLocaleString());
      $("#btnSubmitAmount").text("₹" + total.toLocaleString());
    },

    submitOnboarding: function () {
      const self = this;
      const bizName = $.trim($("#setupBizName").val()) || $.trim($("#setupSubdomain").val()) || "My Travel Workspace";
      const subdomain = $.trim($("#setupSubdomain").val());
      const currency = $("#setupCurrency").val() || "INR";
      const themeColor = $("#selectedThemeColor").val() || "#00647a";

      const destinationsArray = Array.from(self.selectedDestinations);

      // Show animated loading overlay
      $("#loadingOverlay").addClass("active");
      $("#loadingMsg").text("Generating order and opening payment gateway checkout...");

      const payload = {
        app_type: self.appType,
        business_name: bizName,
        subdomain: subdomain,
        setup_email: self.setupEmail,
        currency: currency,
        theme_color: themeColor,
        package_id: self.selectedPackageId,
        copy_tour_packages: self.copyTourPackages ? 1 : 0,
        destinations: destinationsArray
      };

      $.ajax({
        url: "/api/v3/auth/onboarding/",
        type: "POST",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            const redirectUrl = res.payment_url || res.redirect_url || res.url || "/login/?setup=success";
            $("#loadingMsg").text(res.msg || "Order generated! Opening payment checkout...");
            setTimeout(function () {
              window.location.href = redirectUrl;
            }, 600);
          } else {
            $("#loadingOverlay").removeClass("active");
            self.showAlert("error", (res && res.msg) ? res.msg : "Failed to provision workspace. Please try again.");
          }
        },
        error: function (xhr) {
          $("#loadingOverlay").removeClass("active");
          let errorMsg = "An unexpected error occurred during onboarding.";
          try {
            const errRes = JSON.parse(xhr.responseText);
            if (errRes && errRes.msg) errorMsg = errRes.msg;
          } catch (e) {
            // Ignore parse errors
          }
          self.showAlert("error", errorMsg);
        }
      });
    },

    showAlert: function (type, message) {
      const $alert = $("#setupAlert");
      const $text = $("#setupAlertText");

      $alert.removeClass("warning error success").addClass(type).addClass("active");
      $text.text(message);

      window.scrollTo({ top: $alert.offset().top - 40, behavior: "smooth" });
    },

    hideAlert: function () {
      $("#setupAlert").removeClass("active warning error success");
    }
  };

  // Launch on DOM Ready
  $(document).ready(function () {
    WebsiteSetup.init();
  });

})(jQuery);
