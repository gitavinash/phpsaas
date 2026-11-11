/**
 * V3 Executive Authentication, Registration & Onboarding JavaScript Module
 * Supports E-Commerce, Aleya, and Gift Studio Workspaces
 * Path: /app/dashboard/v3/default/desktop/assets/js/auth.js
 */

(function ($) {
  "use strict";

  const showcaseData = {
    aleya: {
      badge: "Travel & Hospitality ERP",
      heading: "The Operating System for Travel, Spa & Transport.",
      subheading: "Aleya automates day-wise tour proposals, vehicle dispatch, spa bookings, WhatsApp follow-ups, and payment tracking.",
      features: [
        { icon: "fi fi-rr-route", title: "Tour Packages & Itinerary Engine", desc: "Generate beautiful day-wise proposals, vouchers and itineraries in seconds." },
        { icon: "fi fi-rr-taxi", title: "Transporter & Fleet Operations", desc: "Manage cabs, drivers, vehicle assignments, duty slips and fuel tracking." },
        { icon: "fi fi-rr-sparkles", title: "Spa & Wellness Management", desc: "Seamless appointment slots, therapist scheduling, treatment packages and guest billing." },
        { icon: "fi fi-rr-paper-plane", title: "Multi-Channel Lead Follow-ups", desc: "Auto-capture leads from Meta Ads, Google & website with instant WhatsApp reminders." }
      ],
      stat_value: "500+",
      stat_label: "Travel agencies, spas & transporters scaled with Aleya"
    },
    ecom: {
      badge: "E-Commerce Cloud OS",
      heading: "Scale your storefront with speed & intelligence",
      subheading: "Manage product catalog, high-frequency customer orders, automated billing, and live store analytics.",
      features: [
        { icon: "fi fi-rr-shopping-bag", title: "Instant Checkout", desc: "High-conversion checkout with lightning-fast catalog search." },
        { icon: "fi fi-rr-chart-line-up", title: "Real-Time Insights", desc: "Live revenue metrics, order velocity, and customer cohorts." },
        { icon: "fi fi-rr-boxes", title: "Inventory & Billing", desc: "Automated stock management and instant digital tax invoices." }
      ],
      stat_value: "99.99%",
      stat_label: "Uptime &bull; Powers modern digital storefronts"
    },
    ecom_gift: {
      badge: "Gift & Bakery Studio",
      heading: "Craft unforgettable orders, cakes & celebration gifts",
      subheading: "Empower custom cake configurations, bespoke gift hampers, date-slot delivery, and greeting cards.",
      features: [
        { icon: "fi fi-rr-gift", title: "Hamper Customizer", desc: "Bespoke item mixing, packaging themes, and occasion selectors." },
        { icon: "fi fi-rr-clock-three", title: "Delivery Slot Dispatch", desc: "Pinpoint date and time slot scheduling with rider routing." },
        { icon: "fi fi-rr-cake-slice", title: "Flavor & Custom Modifiers", desc: "Eggless choices, weight options, and custom message cards." }
      ],
      stat_value: "250,000+",
      stat_label: "Festive celebrations and hampers delivered"
    }
  };

  const appCategories = {
    aleya: [
      { val: "travel_agency", label: "Travel Agency & Tour Operator" },
      { val: "transporter", label: "Transporter & Fleet Operator (Cabs, Coaches, Rentals)" },
      { val: "spa_wellness", label: "Spa, Wellness & Retreat Center" },
      { val: "dmc", label: "Destination Management Company (DMC)" },
      { val: "hotel_resort", label: "Hotel, Resort & Homestay" },
      { val: "adventure_tours", label: "Adventure, Trekking & Safari Organizer" },
      { val: "sightseeing_excursions", label: "Day Tours & Sightseeing Excursions" },
      { val: "corporate_mice", label: "Corporate Travel & MICE Organizers" },
      { val: "other_travel", label: "Other Travel & Hospitality Business" }
    ],
    ecom: [
      { val: "fashion", label: "Fashion & Apparel" },
      { val: "electronics", label: "Electronics & Gadgets" },
      { val: "beauty", label: "Beauty & Cosmetics" },
      { val: "home", label: "Home Decor & Living" },
      { val: "grocery", label: "Grocery & Gourmet" },
      { val: "general", label: "General Merchandise" }
    ],
    ecom_gift: [
      { val: "bakery_cakes", label: "Bakery, Cakes & Desserts" },
      { val: "florist_flowers", label: "Florist & Fresh Flowers" },
      { val: "custom_hampers", label: "Personalised Hampers & Gifts" },
      { val: "sweets_chocolates", label: "Artisanal Chocolates & Sweets" },
      { val: "occasions_decor", label: "Occasions & Party Supplies" }
    ]
  };

  const V3Auth = {
    isSubmitting: false,
    resendInterval: null,
    currentOtpRef: "",
    currentOtpTarget: "",
    currentOtpMode: "email",
    currentOtpEmail: "",

    init: function () {
      this.bindEvents();
      this.checkUrlParams();
      this.initAppChoices();
      this.initOnboardingAutoSlug();
      this.autoFocus();
    },

    bindEvents: function () {
      const self = this;

      // App choice cards on Registration page
      $(document).on("click", ".app-choice-card", function () {
        const selectedApp = $(this).data("app");
        self.selectApp(selectedApp);
      });

      // 1. User Registration Form Submit (Passwordless)
      $(document).on("submit", "form#register", function (e) {
        e.preventDefault();
        self.handleRegistration();
      });

      $(document).on("click", "#registerBtn", function (e) {
        e.preventDefault();
        $("form#register").trigger("submit");
      });

      // 2. Onboarding Setup Form Submit
      $(document).on("submit", "form#formOnboarding", function (e) {
        e.preventDefault();
        self.handleOnboarding();
      });

      $(document).on("click", "#btnCompleteSetup", function (e) {
        e.preventDefault();
        $("form#formOnboarding").trigger("submit");
      });

      // 3. Password Login Submit
      $(document).on("submit", "#formPasswordLogin", function (e) {
        e.preventDefault();
        self.handlePasswordLogin();
      });

      // Toggle Password Visibility
      $(document).on("click", "#btnTogglePassword", function (e) {
        e.preventDefault();
        const $pwd = $("#loginPassword");
        const $icon = $("#togglePasswordIcon");
        if ($pwd.attr("type") === "password") {
          $pwd.attr("type", "text");
          $icon.removeClass("fi-rr-eye").addClass("fi-rr-eye-crossed");
        } else {
          $pwd.attr("type", "password");
          $icon.removeClass("fi-rr-eye-crossed").addClass("fi-rr-eye");
        }
      });

      // 4. Unified OTP: Input Auto-Detect Email or Phone
      $(document).on("input", "#otpTarget, #otpEmail", function () {
        self.handleTargetInput($(this).val());
      });

      // 4. Unified OTP: Step A - Send Code
      $(document).on("submit", "#formSendOtp", function (e) {
        e.preventDefault();
        self.handleSendOtp();
      });

      // 4. Unified OTP: Step B - Verify Code
      $(document).on("submit", "#formVerifyOtp", function (e) {
        e.preventDefault();
        self.handleVerifyOtp();
      });

      // Change destination in OTP verify pane
      $(document).on("click", "#btnChangeOtpEmail", function (e) {
        e.preventDefault();
        self.resetOtpPane();
      });

      // Resend OTP
      $(document).on("click", "#btnResendOtp", function (e) {
        e.preventDefault();
        if (!$(this).hasClass("is-disabled")) {
          self.handleResendOtp();
        }
      });

      // Clear alert on typing
      $(document).on("input", "#loginIdentifier, #loginPassword, #otpTarget, #otpEmail, #otpCodeInput, #business_name, #first_name, #last_name, #email, #mobile, #onboardingBizName, #onboardingSubdomain", function () {
        self.hideAlert();
      });
    },

    initAppChoices: function () {
      const initialApp = $("#selectedAppType").val() || "aleya";
      this.selectApp(initialApp);
    },

    selectApp: function (appType) {
      $(".app-choice-card").removeClass("active");
      $(`.app-choice-card[data-app="${appType}"]`).addClass("active");
      $("#selectedAppType").val(appType);

      const $lbl = $("#lblBusinessName");
      const $input = $("#business_name");
      const $categorySelect = $("#category");

      if (appType === "aleya") {
        $lbl.text("Agency / Business Name");
        $input.attr("placeholder", "e.g. Skyline Holidays & Travels / Royal Cabs / Lotus Spa");
        $("#iconBusiness").attr("class", "fi fi-rr-briefcase input-icon");
      } else if (appType === "ecom_gift") {
        $lbl.text("Gift Studio / Bakery Name");
        $input.attr("placeholder", "e.g. Sweet Occasions Studio");
        $("#iconBusiness").attr("class", "fi fi-rr-gift input-icon");
      } else {
        $lbl.text("Store / Brand Name");
        $input.attr("placeholder", "e.g. Apex E-Commerce");
        $("#iconBusiness").attr("class", "fi fi-rr-shop input-icon");
      }

      // Populate categories
      const cats = appCategories[appType] || appCategories.aleya;
      let html = "";
      cats.forEach(function (c) {
        html += `<option value="${c.val}">${c.label}</option>`;
      });
      $categorySelect.html(html);

      // Update showcase panel if present on page
      if (typeof showcaseData !== "undefined" && showcaseData[appType]) {
        const sc = showcaseData[appType];
        if ($("#showcaseBadge").length) $("#showcaseBadge").text(sc.badge);
        if ($("#showcaseTitle").length) $("#showcaseTitle").text(sc.heading);
        if ($("#showcaseDesc").length) $("#showcaseDesc").text(sc.subheading);
        if ($("#showcaseFeatures").length) {
          let featHtml = "";
          sc.features.forEach(function (f) {
            featHtml += `<div class="showcase-feature-item">
              <div class="feature-icon-box"><i class="${f.icon}"></i></div>
              <div class="feature-text-group">
                <strong>${f.title}</strong>
                <span>${f.desc}</span>
              </div>
            </div>`;
          });
          $("#showcaseFeatures").html(featHtml);
        }
        if ($("#showcaseStatValue").length) $("#showcaseStatValue").text(sc.stat_value);
        if ($("#showcaseStatLabel").length) $("#showcaseStatLabel").html(sc.stat_label);
      }
    },

    initOnboardingAutoSlug: function () {
      $(document).on("input", "#onboardingBizName", function () {
        const val = $(this).val();
        const cleanSlug = val.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-").replace(/^-|-$/g, "");
        if (!$("#onboardingSubdomain").data("user-edited")) {
          $("#onboardingSubdomain").val(cleanSlug);
        }
      });

      $(document).on("input", "#onboardingSubdomain", function () {
        $(this).data("user-edited", true);
      });
    },

    autoFocus: function () {
      if ($("#loginIdentifier").is(":visible") && !$("#loginIdentifier").val()) {
        $("#loginIdentifier").trigger("focus");
      } else if ($("#business_name").is(":visible") && !$("#business_name").val()) {
        $("#business_name").trigger("focus");
      } else if ($("#onboardingBizName").is(":visible") && !$("#onboardingBizName").val()) {
        $("#onboardingBizName").trigger("focus");
      } else if (($("#otpTarget").is(":visible") && !$("#otpTarget").val()) || ($("#otpEmail").is(":visible") && !$("#otpEmail").val())) {
        ($("#otpTarget").length ? $("#otpTarget") : $("#otpEmail")).trigger("focus");
      }
    },

    cleanRedirectUrl: function (url) {
      if (!url || typeof url !== "string") return "/dashboard/";
      let cleaned = url.trim();
      for (let i = 0; i < 3; i++) {
        if (cleaned.includes("%2F") || cleaned.includes("%2f") || cleaned.includes("%3A") || cleaned.includes("%3a") || cleaned.includes("%25")) {
          try {
            const decoded = decodeURIComponent(cleaned);
            if (decoded === cleaned) break;
            cleaned = decoded.trim();
          } catch (e) {
            break;
          }
        } else {
          break;
        }
      }
      if (!cleaned || cleaned === "#" || cleaned === "dashboard" || cleaned === "dashboard/" || cleaned === "/login/dashboard/" || cleaned === "/login/dashboard" || cleaned.includes("\\") || cleaned.startsWith("//")) {
        return "/dashboard/";
      }
      if (cleaned.startsWith("http://") || cleaned.startsWith("https://")) {
        try {
          const parsed = new URL(cleaned);
          if (parsed.origin === window.location.origin) {
            return (parsed.pathname || "/") + (parsed.search || "");
          }
        } catch (e) {}
        return "/dashboard/";
      }
      if (/^[a-z0-9+.-]+:/i.test(cleaned)) {
        return "/dashboard/";
      }
      return "/" + cleaned.replace(/^\/+/, "");
    },

    checkUrlParams: function () {
      const params = new URLSearchParams(window.location.search);
      const alertMsg = params.get("alertMsg") || params.get("msg");
      if (alertMsg) {
        this.showAlert("warning", alertMsg);
      }

      let redirect = params.get("redirect_url") || params.get("redirect");
      if (redirect) {
        const cleaned = this.cleanRedirectUrl(redirect);
        $("input[name='redirect_url']").val(cleaned);
      }

      const mobileParam = params.get("mobile") || params.get("phone");
      const emailParam = params.get("email");
      const targetParam = params.get("identifier") || mobileParam || emailParam;
      if (targetParam && $("#otpTarget, #otpEmail").length) {
        const $targetInput = $("#otpTarget").length ? $("#otpTarget") : $("#otpEmail");
        $targetInput.val(targetParam.trim());
        this.handleTargetInput(targetParam.trim());
      }
    },

    showAlert: function (type, message) {
      const $box = $("#authAlertBox");
      const $text = $("#authAlertText");
      const $icon = $("#authAlertIcon");

      if (!$box.length) return;

      $box.removeClass("alert-error alert-success alert-warning");

      let iconClass = "fi fi-rr-exclamation";
      if (type === "success") {
        $box.addClass("alert-success");
        iconClass = "fi fi-rr-check-circle";
      } else if (type === "warning") {
        $box.addClass("alert-warning");
        iconClass = "fi fi-rr-triangle-warning";
      } else {
        $box.addClass("alert-error");
        iconClass = "fi fi-rr-circle-cross";
      }

      $icon.attr("class", iconClass);
      $text.text(message);
      $box.fadeIn(150);
    },

    hideAlert: function () {
      $("#authAlertBox").hide();
    },

    // -----------------------------------------------------------------------
    // APP REGISTRATION WORKFLOW (Passwordless)
    // -----------------------------------------------------------------------
    handleRegistration: function () {
      const self = this;
      if (self.isSubmitting) return;

      const businessName = $.trim($("#business_name").val());
      const firstName = $.trim($("#first_name").val());
      const lastName = $.trim($("#last_name").val());
      const email = $.trim($("#email").val());
      const mobile = $.trim($("#mobile").val());
      const agreeTerms = $("#agreeTerms").is(":checked");

      if (!businessName) {
        self.showAlert("warning", "Please enter your business or brand name.");
        $("#business_name").trigger("focus");
        return;
      }

      if (!firstName) {
        self.showAlert("warning", "Please enter your first name.");
        $("#first_name").trigger("focus");
        return;
      }

      if (!lastName) {
        self.showAlert("warning", "Please enter your last name.");
        $("#last_name").trigger("focus");
        return;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) {
        self.showAlert("warning", "Please enter a valid work email address.");
        $("#email").trigger("focus");
        return;
      }

      if (mobile && mobile.replace(/\D/g, "").length < 10) {
        self.showAlert("warning", "Please enter a valid 10-digit phone number.");
        $("#mobile").trigger("focus");
        return;
      }

      if (!agreeTerms) {
        self.showAlert("warning", "Please accept the Terms of Service to proceed.");
        return;
      }

      const $btn = $("#registerBtn");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Creating Workspace...");
      self.hideAlert();

      const formData = $("form#register").serialize();

      $.ajax({
        url: "/api/v3/auth/register/",
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (res) {
          if (res && res.status === "success") {
            self.showAlert("success", res.msg || "Account created successfully!");
            $btnText.text("Configuring Environment...");

            const destUrl = self.cleanRedirectUrl(res.url || res.redirect_url || "/dashboard/");
            setTimeout(function () {
              window.location.href = destUrl;
            }, 500);
          } else {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            const msg = (res && res.msg) ? res.msg : "Registration failed.";
            self.showAlert((res && res.status) ? res.status : "error", msg);
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).removeClass("is-loading");
          $btnText.text(origText);

          const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "An error occurred while creating your account.";
          self.showAlert("error", errorMsg);
        }
      });
    },

    // -----------------------------------------------------------------------
    // ONBOARDING WORKFLOW
    // -----------------------------------------------------------------------
    handleOnboarding: function () {
      const self = this;
      if (self.isSubmitting) return;

      const bizName = $.trim($("#onboardingBizName").val());
      const subdomain = $.trim($("#onboardingSubdomain").val());

      if (!bizName) {
        self.showAlert("warning", "Please enter your business or brand name.");
        $("#onboardingBizName").trigger("focus");
        return;
      }

      if (!subdomain) {
        self.showAlert("warning", "Please specify your desired subdomain.");
        $("#onboardingSubdomain").trigger("focus");
        return;
      }

      const $btn = $("#btnCompleteSetup");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Launching Workspace...");
      self.hideAlert();

      // Show animated progress steps
      $("#onboardingProgressBox").fadeIn(200);

      const formData = $("form#formOnboarding").serialize();

      $.ajax({
        url: "/api/v3/auth/onboarding/",
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (res) {
          if (res && res.status === "success") {
            setTimeout(function () {
              $("#pStep2 i").attr("class", "fi fi-rr-check-circle");
            }, 300);
            setTimeout(function () {
              $("#pStep3 i").attr("class", "fi fi-rr-check-circle");
              self.showAlert("success", "Workspace launched successfully!");
            }, 600);

            const destUrl = res.url || res.redirect_url || "/dashboard/";
            setTimeout(function () {
              window.location.href = destUrl;
            }, 1000);
          } else {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);
            $("#onboardingProgressBox").hide();

            const msg = (res && res.msg) ? res.msg : "Setup failed.";
            self.showAlert("error", msg);
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).removeClass("is-loading");
          $btnText.text(origText);
          $("#onboardingProgressBox").hide();

          const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Error completing onboarding.";
          self.showAlert("error", errorMsg);
        }
      });
    },

    // -----------------------------------------------------------------------
    // PASSWORD LOGIN WORKFLOW
    // -----------------------------------------------------------------------
    handlePasswordLogin: function () {
      const self = this;
      if (self.isSubmitting) return;

      const identifier = $.trim($("#loginIdentifier").val());
      const password = $("#loginPassword").val();
      const redirectUrl = $.trim($("input[name='redirect_url']").val()) || "/dashboard/";

      if (!identifier) {
        self.showAlert("warning", "Please enter your email or username.");
        $("#loginIdentifier").trigger("focus");
        return;
      }

      if (!password) {
        self.showAlert("warning", "Please enter your password.");
        $("#loginPassword").trigger("focus");
        return;
      }

      const $btn = $("#btnLoginPassword");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Signing In...");
      self.hideAlert();

      const payload = {
        identifier: identifier,
        password: password,
        redirect_url: redirectUrl
      };

      $.ajax({
        url: "/api/v3/auth/login/",
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            self.showAlert("success", res.msg || "Login successful!");
            $btnText.text("Redirecting...");

            const destUrl = self.cleanRedirectUrl(res.url || res.redirect_url || "/dashboard/");
            setTimeout(function () {
              window.location.href = destUrl;
            }, 400);
          } else {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            const msg = (res && res.msg) ? res.msg : "Invalid login credentials.";
            self.showAlert("error", msg);
            $("#loginPassword").trigger("focus");
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).removeClass("is-loading");
          $btnText.text(origText);

          const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Invalid username or password.";
          self.showAlert("error", errorMsg);
          $("#loginPassword").trigger("focus");
        }
      });
    },

    // -----------------------------------------------------------------------
    // AUTO-DETECTION HELPERS FOR EMAIL & MOBILE
    // -----------------------------------------------------------------------
    cleanMobile: function (val) {
      if (!val) return "";
      let cleaned = $.trim(val).replace(/[\s\-\(\)]/g, "");
      if (cleaned.startsWith("+91")) {
        cleaned = cleaned.substring(3);
      } else if (/^91[6-9]\d{9}$/.test(cleaned)) {
        cleaned = cleaned.substring(2);
      } else if (cleaned.startsWith("0")) {
        cleaned = cleaned.substring(1);
      }
      return cleaned.replace(/\D/g, "");
    },

    detectType: function (val) {
      const trimmed = $.trim(val);
      if (!trimmed) return "unknown";

      const cleanPhone = this.cleanMobile(trimmed);
      const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed);
      const hasAt = trimmed.includes("@");
      const hasLetters = /[a-zA-Z]/.test(trimmed);

      // If valid 10-digit mobile starting with 6-9
      if (/^[6-9]\d{9}$/.test(cleanPhone)) {
        return "phone";
      }

      // If valid email format
      if (isEmail) {
        return "email";
      }

      // Typing in progress: if contains @ or letters, identify as email
      if (hasAt || (hasLetters && !trimmed.startsWith("+"))) {
        return "email";
      }

      // Typing in progress: digits or starts with +, identify as phone
      if (/^\+?\d+$/.test(trimmed.replace(/[\s\-]/g, ""))) {
        return "phone";
      }

      return "unknown";
    },

    handleTargetInput: function (val) {
      const type = this.detectType(val);
      const $icon = $("#iconOtpTarget");
      const $btnText = $("#btnSendOtp .btn-text");

      if (type === "phone") {
        $icon.attr("class", "fi fi-rr-phone-call input-icon");
        $btnText.text("Continue with Mobile");
      } else if (type === "email") {
        $icon.attr("class", "fi fi-rr-envelope input-icon");
        $btnText.text("Continue with Email");
      } else {
        $icon.attr("class", "fi fi-rr-envelope input-icon");
        $btnText.text("Continue");
      }
    },

    // -----------------------------------------------------------------------
    // UNIFIED PASSWORDLESS OTP WORKFLOW (Email & Mobile SMS)
    // -----------------------------------------------------------------------
    handleSendOtp: function () {
      const self = this;
      if (self.isSubmitting) return;

      const $input = $("#otpTarget").length ? $("#otpTarget") : $("#otpEmail");
      const rawVal = $.trim($input.val());

      if (!rawVal) {
        self.showAlert("warning", "Please enter your email or mobile number.");
        $input.trigger("focus");
        return;
      }

      const detected = self.detectType(rawVal);
      const cleanPhone = self.cleanMobile(rawVal);
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      let mode = "email";
      let target = rawVal;

      if (detected === "phone" || /^[6-9]\d{9}$/.test(cleanPhone)) {
        if (!/^[6-9]\d{9}$/.test(cleanPhone)) {
          self.showAlert("warning", "Please enter a valid 10-digit mobile number starting with 6-9.");
          $input.trigger("focus");
          return;
        }
        mode = "sms";
        target = cleanPhone;
      } else {
        if (!emailRegex.test(rawVal)) {
          self.showAlert("warning", "Please enter a valid email address or 10-digit mobile number.");
          $input.trigger("focus");
          return;
        }
        mode = "email";
        target = rawVal;
      }

      const $btn = $("#btnSendOtp");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Sending Code...");
      self.hideAlert();

      if (mode === "sms") {
        // Send SMS OTP via existing mobile auth API
        $.ajax({
          url: "/api/auth/auth-by-mobile/",
          type: "GET",
          dataType: "json",
          data: {
            mobile: target,
            reg_type: "administrator"
          },
          success: function (res) {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            if (res && res.status === "success") {
              self.currentOtpRef = res.otpRef || "";
              self.currentOtpTarget = target;
              self.currentOtpEmail = target;
              self.currentOtpMode = "sms";

              $("#lblOtpSentTarget").text("Code sent via SMS to");
              $("#displayOtpTarget").text("+91 " + target);
              $("#paneOtpStepEmail").hide();
              $("#paneOtpStepVerify").fadeIn(150);
              $("#otpCodeInput").val("").trigger("focus");

              self.showAlert("success", res.msg || "Verification code sent via SMS!");
              self.startResendTimer(30);
            } else {
              const msg = (res && res.msg) ? res.msg : "Failed to send verification code.";
              self.showAlert("error", msg);
            }
          },
          error: function (xhr) {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Failed to send code via SMS. Please try again.";
            self.showAlert("error", errorMsg);
          }
        });
      } else {
        // Send Email OTP via V3 auth API
        $.ajax({
          url: "/api/v3/auth/send-otp/",
          type: "POST",
          dataType: "json",
          contentType: "application/json",
          data: JSON.stringify({ email: target }),
          success: function (res) {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            if (res && res.status === "success") {
              self.currentOtpRef = res.otp_ref || "";
              self.currentOtpTarget = target;
              self.currentOtpEmail = target;
              self.currentOtpMode = "email";

              $("#lblOtpSentTarget").text("Code sent to");
              $("#displayOtpTarget").text(target);
              $("#paneOtpStepEmail").hide();
              $("#paneOtpStepVerify").fadeIn(150);
              $("#otpCodeInput").val("").trigger("focus");

              self.showAlert("success", res.msg || "Verification code sent to your email!");
              self.startResendTimer(30);
            } else {
              const msg = (res && res.msg) ? res.msg : "Failed to send verification code.";
              self.showAlert("error", msg);
            }
          },
          error: function (xhr) {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Failed to send code. Please try again.";
            self.showAlert("error", errorMsg);
          }
        });
      }
    },

    handleVerifyOtp: function () {
      const self = this;
      if (self.isSubmitting) return;

      const code = $.trim($("#otpCodeInput").val()).replace(/\D/g, "");
      const redirectUrl = $.trim($("input[name='redirect_url']").val()) || "/dashboard/";

      if (code.length !== 6) {
        self.showAlert("warning", "Please enter the 6-digit verification code.");
        $("#otpCodeInput").trigger("focus");
        return;
      }

      const $btn = $("#btnVerifyOtp");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Verifying...");
      self.hideAlert();

      const payload = {
        otp_ref: self.currentOtpRef,
        otp_code: code,
        identifier: self.currentOtpTarget,
        email: (self.currentOtpMode === "email") ? self.currentOtpTarget : "",
        mobile: (self.currentOtpMode === "sms") ? self.currentOtpTarget : "",
        otp_mode: self.currentOtpMode,
        redirect_url: redirectUrl
      };

      $.ajax({
        url: "/api/v3/auth/verify-otp/",
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          if (res && res.status === "success") {
            self.showAlert("success", res.msg || "Login successful!");
            $btnText.text("Redirecting...");

            const destUrl = self.cleanRedirectUrl(res.url || res.redirect_url || "/dashboard/");
            setTimeout(function () {
              window.location.href = destUrl;
            }, 400);
          } else {
            self.isSubmitting = false;
            $btn.prop("disabled", false).removeClass("is-loading");
            $btnText.text(origText);

            const msg = (res && res.msg) ? res.msg : "Incorrect verification code.";
            self.showAlert("error", msg);
            $("#otpCodeInput").val("").trigger("focus");
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).removeClass("is-loading");
          $btnText.text(origText);

          const errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Invalid verification code.";
          self.showAlert("error", errorMsg);
          $("#otpCodeInput").val("").trigger("focus");
        }
      });
    },

    handleResendOtp: function () {
      const self = this;
      if (!self.currentOtpTarget) return;

      const $link = $("#btnResendOtp");
      $link.addClass("is-disabled").text("Sending...");

      if (self.currentOtpMode === "sms") {
        // Resend via SMS API
        $.ajax({
          url: "/api/auth/resend-otp/",
          type: "GET",
          dataType: "json",
          data: {
            otp_ref_id: self.currentOtpRef,
            otp_mode: "sms"
          },
          success: function (res) {
            if (res && res.status === "success") {
              self.showAlert("success", res.msg || "A new verification code has been sent via SMS.");
              self.startResendTimer(30);
            } else {
              self.fallbackResendMobile();
            }
          },
          error: function () {
            self.fallbackResendMobile();
          }
        });
      } else {
        // Resend via Email API
        $.ajax({
          url: "/api/v3/auth/send-otp/",
          type: "POST",
          dataType: "json",
          contentType: "application/json",
          data: JSON.stringify({ email: self.currentOtpTarget }),
          success: function (res) {
            if (res && res.status === "success") {
              self.currentOtpRef = res.otp_ref || self.currentOtpRef;
              self.showAlert("success", "A new verification code has been sent to your email.");
              self.startResendTimer(30);
            } else {
              self.showAlert("error", (res && res.msg) ? res.msg : "Failed to resend code.");
              self.startResendTimer(15);
            }
          },
          error: function () {
            self.showAlert("error", "Network error resending code.");
            self.startResendTimer(15);
          }
        });
      }
    },

    fallbackResendMobile: function () {
      const self = this;
      $.ajax({
        url: "/api/auth/auth-by-mobile/",
        type: "GET",
        dataType: "json",
        data: {
          mobile: self.currentOtpTarget,
          reg_type: "administrator"
        },
        success: function (res) {
          if (res && res.status === "success") {
            self.currentOtpRef = res.otpRef || self.currentOtpRef;
            self.showAlert("success", "A new verification code has been sent via SMS.");
            self.startResendTimer(30);
          } else {
            self.showAlert("error", (res && res.msg) ? res.msg : "Failed to resend SMS code.");
            self.startResendTimer(15);
          }
        },
        error: function () {
          self.showAlert("error", "Network error resending SMS code.");
          self.startResendTimer(15);
        }
      });
    },

    startResendTimer: function (seconds) {
      const self = this;
      if (self.resendInterval) {
        clearInterval(self.resendInterval);
      }

      let remaining = seconds;
      const $link = $("#btnResendOtp");
      $link.addClass("is-disabled").html(`Resend code in <span id="resendTimer">${remaining}</span>s`);

      self.resendInterval = setInterval(function () {
        remaining--;
        if (remaining <= 0) {
          clearInterval(self.resendInterval);
          self.resendInterval = null;
          $link.removeClass("is-disabled").text("Resend code");
        } else {
          $("#resendTimer").text(remaining);
        }
      }, 1000);
    },

    resetOtpPane: function () {
      if (this.resendInterval) {
        clearInterval(this.resendInterval);
      }
      $("#paneOtpStepVerify").hide();
      $("#paneOtpStepEmail").fadeIn(150);
      const $input = $("#otpTarget").length ? $("#otpTarget") : $("#otpEmail");
      $input.trigger("focus");
    }
  };

  $(document).ready(function () {
    V3Auth.init();
  });

})(jQuery);
