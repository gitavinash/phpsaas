/**
 * V3 Executive Authentication, Registration & Onboarding JavaScript Module
 * Supports E-Commerce, Aleya, and Gift Studio Workspaces
 * Path: /app/dashboard/v3/default/desktop/assets/js/auth.js
 */

(function ($) {
  "use strict";

  const appCategories = {
    ecom: [
      { val: "fashion", label: "Fashion & Apparel" },
      { val: "electronics", label: "Electronics & Gadgets" },
      { val: "beauty", label: "Beauty & Cosmetics" },
      { val: "home", label: "Home Decor & Living" },
      { val: "grocery", label: "Grocery & Gourmet" },
      { val: "general", label: "General Merchandise" }
    ],
    aleya: [
      { val: "hair_salon", label: "Hair & Beauty Salon" },
      { val: "luxury_spa", label: "Luxury Spa & Wellness" },
      { val: "aesthetic_clinic", label: "Aesthetic & Skin Clinic" },
      { val: "nail_bar", label: "Nail Art & Studio" },
      { val: "barber_grooming", label: "Barber & Men's Grooming" }
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

      // 3. Email OTP: Step A - Send Code
      $(document).on("submit", "#formSendOtp", function (e) {
        e.preventDefault();
        self.handleSendOtp();
      });

      // 3. Email OTP: Step B - Verify Code
      $(document).on("submit", "#formVerifyOtp", function (e) {
        e.preventDefault();
        self.handleVerifyOtp();
      });

      // Change email in OTP verify pane
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
      $(document).on("input", "#otpEmail, #otpCodeInput, #business_name, #first_name, #last_name, #email, #mobile, #onboardingBizName, #onboardingSubdomain", function () {
        self.hideAlert();
      });
    },

    initAppChoices: function () {
      const initialApp = $("#selectedAppType").val() || "ecom";
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
        $lbl.text("Salon / Spa Brand Name");
        $input.attr("placeholder", "e.g. Glow Beauty Lounge");
        $("#iconBusiness").attr("class", "fi fi-rr-sparkles input-icon");
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
      const cats = appCategories[appType] || appCategories.ecom;
      let html = "";
      cats.forEach(function (c) {
        html += `<option value="${c.val}">${c.label}</option>`;
      });
      $categorySelect.html(html);
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
      if ($("#business_name").is(":visible") && !$("#business_name").val()) {
        $("#business_name").trigger("focus");
      } else if ($("#onboardingBizName").is(":visible") && !$("#onboardingBizName").val()) {
        $("#onboardingBizName").trigger("focus");
      } else if ($("#otpEmail").is(":visible") && !$("#otpEmail").val()) {
        $("#otpEmail").trigger("focus");
      }
    },

    cleanRedirectUrl: function (url) {
      if (!url || typeof url !== "string") return "/dashboard/";
      let cleaned = url.trim();
      while (cleaned.includes("%2F") || cleaned.includes("%2f") || cleaned.includes("%3A") || cleaned.includes("%3a")) {
        try {
          cleaned = decodeURIComponent(cleaned);
        } catch (e) {
          break;
        }
      }
      cleaned = cleaned.trim();
      if (!cleaned || cleaned === "#" || cleaned === "dashboard" || cleaned === "dashboard/" || cleaned === "/login/dashboard/" || cleaned === "/login/dashboard") {
        return "/dashboard/";
      }
      if (cleaned.startsWith("http://") || cleaned.startsWith("https://")) {
        return cleaned;
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
    // EMAIL OTP WORKFLOW
    // -----------------------------------------------------------------------
    handleSendOtp: function () {
      const self = this;
      if (self.isSubmitting) return;

      const email = $.trim($("#otpEmail").val());
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!email || !emailRegex.test(email)) {
        self.showAlert("warning", "Please enter a valid email address.");
        $("#otpEmail").trigger("focus");
        return;
      }

      const $btn = $("#btnSendOtp");
      const $btnText = $btn.find(".btn-text");
      const origText = $btnText.text();

      self.isSubmitting = true;
      $btn.prop("disabled", true).addClass("is-loading");
      $btnText.text("Sending Code...");
      self.hideAlert();

      $.ajax({
        url: "/api/v3/auth/send-otp/",
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify({ email: email }),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).removeClass("is-loading");
          $btnText.text(origText);

          if (res && res.status === "success") {
            self.currentOtpRef = res.otp_ref || "";
            self.currentOtpEmail = email;

            $("#displayOtpTarget").text(email);
            $("#paneOtpStepEmail").hide();
            $("#paneOtpStepVerify").fadeIn(150);
            $("#otpCodeInput").val("").trigger("focus");

            self.showAlert("success", res.msg || "Verification code sent!");
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
        email: self.currentOtpEmail,
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
      if (!self.currentOtpEmail) return;

      $("#btnResendOtp").addClass("is-disabled").text("Sending...");

      $.ajax({
        url: "/api/v3/auth/send-otp/",
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify({ email: self.currentOtpEmail }),
        success: function (res) {
          if (res && res.status === "success") {
            self.currentOtpRef = res.otp_ref || self.currentOtpRef;
            self.showAlert("success", "A new verification code has been sent.");
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
      $("#otpEmail").trigger("focus");
    }
  };

  $(document).ready(function () {
    V3Auth.init();
  });

})(jQuery);
