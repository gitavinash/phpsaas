/**
 * V3 Settings & Profile Management Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/settings.js
 */

(function (window, $) {
  "use strict";

  const V3Settings = {
    isSubmitting: false,
    activeConfigXhr: null,
    activeProfileXhr: null,
    activeContactXhr: null,
    activeNotifyXhr: null,
    currentProfileImage: "",
    currentSiteLogo: "",
    currentSiteIcon: "",

    init: function () {
      if ($("#settingConfigApp").length > 0) {
        this.initConfig();
      }
      if ($("#settingProfileApp").length > 0) {
        this.initProfile();
      }
      if ($("#settingContactApp").length > 0) {
        this.initContact();
      }
      if ($("#settingNotifyApp").length > 0) {
        this.initNotification();
      }
      if ($("#settingBankingApp").length > 0) {
        this.initBanking();
      }
      if ($("#settingSeoApp").length > 0) {
        this.initSeo();
      }
      if ($("#settingAppConfigApp").length > 0) {
        this.initAppConfig();
      }
    },

    // ------------------------------------------------------------------------
    // WEBSITE CONFIGURATION
    // ------------------------------------------------------------------------
    initConfig: function () {
      const self = this;
      self.initConfigSelect2();
      self.bindConfigEvents();
      self.loadConfigData();
    },

    initConfigSelect2: function () {
      if (typeof $.fn.select2 === "undefined") return;

      const $tz = $("#configSiteTimezone");
      if ($tz.length > 0) {
        if ($tz.hasClass("select2-hidden-accessible")) {
          try {
            $tz.select2("destroy");
          } catch (e) {}
        }
        $tz.select2({
          width: "100%",
          placeholder: "Select Time Zone",
          dropdownAutoWidth: false
        });
      }

      const $lang = $("#configSiteLang");
      if ($lang.length > 0) {
        if ($lang.hasClass("select2-hidden-accessible")) {
          try {
            $lang.select2("destroy");
          } catch (e) {}
        }
        $lang.select2({
          width: "100%",
          minimumResultsForSearch: -1,
          dropdownAutoWidth: false
        });
      }
    },

    bindConfigEvents: function () {
      const self = this;

      // Theme Preset Buttons
      $(document).off("click", ".theme-preset-btn").on("click", ".theme-preset-btn", function () {
        const color = $(this).data("color");
        if (color) {
          $("#configThemeColorPicker").val(color);
          $("#configThemeColorInput").val(color);
          self.updateBrandPreview();
        }
      });

      // Color Picker Sync
      $(document).off("input change", "#configThemeColorPicker").on("input change", "#configThemeColorPicker", function () {
        $("#configThemeColorInput").val($(this).val());
        self.updateBrandPreview();
      });

      $(document).off("input change", "#configThemeColorInput").on("input change", "#configThemeColorInput", function () {
        const val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
          $("#configThemeColorPicker").val(val);
          self.updateBrandPreview();
        }
      });

      // Website Logo Upload & Live Preview
      $(document).off("change", "#configLogoInput").on("change", "#configLogoInput", function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
          self.showAlert("warning", "Website logo must be less than 5MB.");
          $(this).val("");
          $("#configLogoFileName").text("");
          return;
        }

        const allowed = ["image/jpeg", "image/png", "image/webp", "image/svg+xml"];
        if (!allowed.includes(file.type)) {
          self.showAlert("warning", "Please select a valid image file for logo (PNG, JPG, SVG, or WEBP).");
          $(this).val("");
          $("#configLogoFileName").text("");
          return;
        }

        $("#configLogoFileName").text(file.name);

        const reader = new FileReader();
        reader.onload = function (e) {
          self.displayLogo(e.target.result);
        };
        reader.readAsDataURL(file);
      });

      // Remove / Reset Logo
      $(document).off("click", "#btnRemoveLogo").on("click", "#btnRemoveLogo", function () {
        $("#configLogoInput").val("");
        $("#configLogoFileName").text("");
        self.displayLogo(self.currentSiteLogo || "");
      });

      // Favicon Upload & Live Preview
      $(document).off("change", "#configFaviconInput").on("change", "#configFaviconInput", function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
          self.showAlert("warning", "Favicon must be less than 2MB.");
          $(this).val("");
          $("#configFaviconFileName").text("");
          return;
        }

        const allowed = ["image/x-icon", "image/vnd.microsoft.icon", "image/png", "image/svg+xml", "image/webp", "image/jpeg"];
        if (!allowed.includes(file.type)) {
          self.showAlert("warning", "Please select a valid icon file (ICO, PNG, SVG, or WEBP).");
          $(this).val("");
          $("#configFaviconFileName").text("");
          return;
        }

        $("#configFaviconFileName").text(file.name);

        const reader = new FileReader();
        reader.onload = function (e) {
          self.displayFavicon(e.target.result);
        };
        reader.readAsDataURL(file);
      });

      // Remove / Reset Favicon
      $(document).off("click", "#btnRemoveFavicon").on("click", "#btnRemoveFavicon", function () {
        $("#configFaviconInput").val("");
        $("#configFaviconFileName").text("");
        self.displayFavicon(self.currentSiteIcon || "");
      });

      // Live Brand Identity Preview
      $(document).off("input change", "#configSiteName, #configSiteDomain").on("input change", "#configSiteName, #configSiteDomain", function () {
        self.updateBrandPreview();
      });

      // Submit Configuration Form
      $(document).off("submit", "#settingConfigForm").on("submit", "#settingConfigForm", function (e) {
        e.preventDefault();
        self.submitConfigForm();
      });
    },

    displayLogo: function (imageUrl) {
      if (imageUrl) {
        $("#configLogoPreview").attr("src", imageUrl).removeClass("is-v3-hidden");
        $("#configLogoPlaceholder").addClass("is-v3-hidden");
        $("#btnRemoveLogo").removeClass("is-v3-hidden");
        $("#sidebarLogoPreview").attr("src", imageUrl).removeClass("is-v3-hidden");
        $("#sidebarLogoPlaceholder").addClass("is-v3-hidden");
      } else {
        $("#configLogoPreview").attr("src", "").addClass("is-v3-hidden");
        $("#configLogoPlaceholder").removeClass("is-v3-hidden");
        $("#btnRemoveLogo").addClass("is-v3-hidden");
        $("#sidebarLogoPreview").attr("src", "").addClass("is-v3-hidden");
        $("#sidebarLogoPlaceholder").removeClass("is-v3-hidden");
      }
    },

    displayFavicon: function (imageUrl) {
      if (imageUrl) {
        $("#configFaviconPreview").attr("src", imageUrl).removeClass("is-v3-hidden");
        $("#configFaviconPlaceholder").addClass("is-v3-hidden");
        $("#btnRemoveFavicon").removeClass("is-v3-hidden");
      } else {
        $("#configFaviconPreview").attr("src", "").addClass("is-v3-hidden");
        $("#configFaviconPlaceholder").removeClass("is-v3-hidden");
        $("#btnRemoveFavicon").addClass("is-v3-hidden");
      }
    },

    loadConfigData: function () {
      const self = this;

      if (self.activeConfigXhr && typeof self.activeConfigXhr.abort === "function") {
        self.activeConfigXhr.abort();
      }

      self.activeConfigXhr = $.ajax({
        url: "/api/v3/setting/configuration/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#configSiteName").val(d.site_name || "");
            $("#configSiteDomain").val(d.site_domain || window.location.hostname);

            const lang = d.site_lang || "en";
            $("#configSiteLang").val(lang);
            if (typeof $.fn.select2 !== "undefined") {
              $("#configSiteLang").trigger("change.select2");
            }

            const tz = d.site_timezone || "Asia/Kolkata";
            if ($("#configSiteTimezone").find('option[value="' + tz + '"]').length === 0) {
              $("#configSiteTimezone").append(new Option(tz, tz, true, true));
            }
            $("#configSiteTimezone").val(tz);
            if (typeof $.fn.select2 !== "undefined") {
              $("#configSiteTimezone").trigger("change.select2");
            }

            const themeColor = d.site_theme_color || "#4f46e5";
            $("#configThemeColorPicker").val(themeColor);
            $("#configThemeColorInput").val(themeColor);

            self.currentSiteLogo = d.site_logo || "";
            self.displayLogo(self.currentSiteLogo);

            self.currentSiteIcon = d.site_icon || "";
            self.displayFavicon(self.currentSiteIcon);

            self.updateBrandPreview();
          }
        },
        error: function (xhr, status) {
          if (status !== "abort") {
            self.showAlert("error", "Failed to load website configuration.");
          }
        },
        complete: function () {
          self.activeConfigXhr = null;
        }
      });
    },

    updateBrandPreview: function () {
      const name = $("#configSiteName").val().trim() || "Website Name";
      const domain = $("#configSiteDomain").val() || window.location.hostname || "yourdomain.com";
      const themeColor = $("#configThemeColorInput").val().trim() || "#4F46E5";

      $("#sidebarSiteName").text(name);
      $("#sidebarSiteDomain").text(domain);
      $("#sidebarThemeDot").css("background", themeColor);
      $("#sidebarThemeHex").text(themeColor.toUpperCase());
    },

    submitConfigForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const siteName = $("#configSiteName").val().trim();
      const siteLang = $("#configSiteLang").val();
      const siteTheme = $("#configThemeColorInput").val().trim() || "#4f46e5";

      if (!siteName) {
        self.showAlert("warning", "Website Title is required.");
        $("#configSiteName").focus();
        return;
      }

      const formData = new FormData();
      formData.append("site_name", siteName);
      formData.append("site_lang", siteLang);
      formData.append("site_timezone", $("#configSiteTimezone").val() || "Asia/Kolkata");
      formData.append("site_theme_color", siteTheme);

      const logoInput = document.getElementById("configLogoInput");
      if (logoInput && logoInput.files && logoInput.files[0]) {
        formData.append("site_logo", logoInput.files[0]);
      }

      const faviconInput = document.getElementById("configFaviconInput");
      if (faviconInput && faviconInput.files && faviconInput.files[0]) {
        formData.append("site_icon", faviconInput.files[0]);
      }

      const $btn = $("#btnSaveConfig");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/configuration/",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            if (res.site_logo) {
              self.currentSiteLogo = res.site_logo;
              self.displayLogo(res.site_logo);
              $("#configLogoFileName").text("");
            }
            if (res.site_icon) {
              self.currentSiteIcon = res.site_icon;
              self.displayFavicon(res.site_icon);
              $("#configFaviconFileName").text("");
            }

            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Configuration Saved!",
                text: res.msg || "Website configuration and brand assets updated successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "Website configuration saved successfully.");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update configuration.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving configuration.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // USER PROFILE
    // ------------------------------------------------------------------------
    initProfile: function () {
      const self = this;
      self.bindProfileEvents();
      self.loadProfileData();
    },

    bindProfileEvents: function () {
      const self = this;

      // Live Profile Preview Update
      $(document).off("input change", "#profileFirstName, #profileLastName, #profileDisplayName, #profileMobile, #profileBio").on("input change", "#profileFirstName, #profileLastName, #profileDisplayName, #profileMobile, #profileBio", function () {
        self.updateProfilePreview();
      });

      // Profile Photo Selection & Live Preview
      $(document).off("change", "#profileImageInput").on("change", "#profileImageInput", function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
          self.showAlert("warning", "Profile photo must be less than 5MB.");
          $(this).val("");
          $("#profileImageFileName").text("");
          return;
        }

        const allowed = ["image/jpeg", "image/png", "image/webp", "image/gif"];
        if (!allowed.includes(file.type)) {
          self.showAlert("warning", "Please select a valid image file (JPG, PNG, or WEBP).");
          $(this).val("");
          $("#profileImageFileName").text("");
          return;
        }

        $("#profileImageFileName").text(file.name);
        self.isPhotoRemoved = false;

        const reader = new FileReader();
        reader.onload = function (e) {
          const dataUrl = e.target.result;
          self.displayAvatar(dataUrl);
        };
        reader.readAsDataURL(file);
      });

      // Remove / Reset Profile Photo
      $(document).off("click", "#btnRemoveProfileImage").on("click", "#btnRemoveProfileImage", function () {
        $("#profileImageInput").val("");
        $("#profileImageFileName").text("");
        self.currentProfileImage = "";
        self.isPhotoRemoved = true;
        self.displayAvatar("");
      });

      // Submit Profile Form
      $(document).off("submit", "#settingProfileForm").on("submit", "#settingProfileForm", function (e) {
        e.preventDefault();
        self.submitProfileForm();
      });
    },

    displayAvatar: function (imageUrl) {
      if (imageUrl) {
        $("#profileImagePreview").attr("src", imageUrl).removeClass("is-v3-hidden");
        $("#profileImageInitials").addClass("is-v3-hidden");
        $("#previewProfileImg").attr("src", imageUrl).removeClass("is-v3-hidden");
        $("#previewProfileAvatar").addClass("is-v3-hidden");
        $("#btnRemoveProfileImage").removeClass("is-v3-hidden");
      } else {
        $("#profileImagePreview").attr("src", "").addClass("is-v3-hidden");
        $("#profileImageInitials").removeClass("is-v3-hidden");
        $("#previewProfileImg").attr("src", "").addClass("is-v3-hidden");
        $("#previewProfileAvatar").removeClass("is-v3-hidden");
        $("#btnRemoveProfileImage").addClass("is-v3-hidden");
      }
    },

    loadProfileData: function () {
      const self = this;

      if (self.activeProfileXhr && typeof self.activeProfileXhr.abort === "function") {
        self.activeProfileXhr.abort();
      }

      self.activeProfileXhr = $.ajax({
        url: "/api/v3/setting/profile/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#profileFirstName").val(d.first_name || "");
            $("#profileLastName").val(d.last_name || "");
            $("#profileDisplayName").val(d.display_name || "");
            $("#profileBio").val(d.bio || "");
            $("#profileMobile").val(d.mobile || "");
            $("#profileAltMobile").val(d.alt_mobile || "");
            $("#profileEmail").val(d.email || "");
            $("#profileAltEmail").val(d.alt_email || "");
            $("#profileUsername").val(d.username || "");

            const role = (d.role || "administrator").toLowerCase();
            let roleFormatted = "Administrator";
            let roleClass = "badge-role-admin";
            let iconClass = "fi fi-rr-shield-check";
            if (role === "agent") {
              roleFormatted = "Sales Agent";
              roleClass = "badge-role-agent";
              iconClass = "fi fi-rr-headset";
            } else if (role === "operations") {
              roleFormatted = "Operations Manager";
              roleClass = "badge-role-operations";
              iconClass = "fi fi-rr-settings-sliders";
            } else if (role === "accountant") {
              roleFormatted = "Accounts & Finance";
              roleClass = "badge-role-accounts";
              iconClass = "fi fi-rr-calculator";
            }

            $("#profileRoleDisplay").val(roleFormatted);
            $("#previewProfileRole").attr("class", `badge-service-state ${roleClass}`).html(`<i class="${iconClass}"></i> ${selfEscapeHTML(roleFormatted)}`);

            // Profile Picture
            self.currentProfileImage = d.profile_image || "";
            self.displayAvatar(self.currentProfileImage);

            self.updateProfilePreview();
          }
        },
        error: function (xhr, status) {
          if (status !== "abort") {
            self.showAlert("error", "Failed to load user profile.");
          }
        },
        complete: function () {
          self.activeProfileXhr = null;
        }
      });
    },

    updateProfilePreview: function () {
      const fn = $("#profileFirstName").val().trim();
      const ln = $("#profileLastName").val().trim();
      let dn = $("#profileDisplayName").val().trim();
      if (!dn) {
        dn = (fn || ln) ? (fn + " " + ln).trim() : "Team Member";
      }

      let initials = "U";
      if (fn) {
        initials = fn.charAt(0).toUpperCase();
        if (ln) initials += ln.charAt(0).toUpperCase();
      } else if (dn) {
        const parts = dn.split(" ");
        initials = parts[0].charAt(0).toUpperCase();
        if (parts.length > 1) initials += parts[1].charAt(0).toUpperCase();
      }

      $("#profileImageInitials").text(initials);
      $("#previewProfileAvatar").text(initials);
      $("#previewProfileName").text(dn);
      $("#heroProfileName").text(dn);

      const mob = $("#profileMobile").val().trim();
      $("#previewProfileMobile").text(mob ? "+91 " + mob : "+91 \u2014");

      const em = $("#profileEmail").val().trim();
      $("#previewProfileEmail").text(em || "No email provided");
    },

    submitProfileForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const firstName = $("#profileFirstName").val().trim();
      const lastName = $("#profileLastName").val().trim();
      const displayName = $("#profileDisplayName").val().trim();
      const bio = $("#profileBio").val().trim();
      const mobile = $("#profileMobile").val().trim();
      const altMobile = $("#profileAltMobile").val().trim();
      const altEmail = $("#profileAltEmail").val().trim();

      if (!firstName) {
        self.showAlert("warning", "First Name is required.");
        $("#profileFirstName").focus();
        return;
      }

      if (!mobile || mobile.length < 10) {
        self.showAlert("warning", "Please enter a valid 10-digit primary mobile number.");
        $("#profileMobile").focus();
        return;
      }

      const formData = new FormData();
      formData.append("first_name", firstName);
      formData.append("last_name", lastName);
      formData.append("display_name", displayName);
      formData.append("bio", bio);
      formData.append("mobile", mobile);
      formData.append("alt_mobile", altMobile);
      formData.append("alt_email", altEmail);

      const fileInput = document.getElementById("profileImageInput");
      if (fileInput && fileInput.files && fileInput.files[0]) {
        formData.append("profile_image", fileInput.files[0]);
      } else if (self.isPhotoRemoved) {
        formData.append("remove_profile_image", "1");
      }

      const $btn = $("#btnSaveProfile");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Updating...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/profile/",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            self.currentProfileImage = res.profile_image || "";
            self.displayAvatar(self.currentProfileImage);
            self.isPhotoRemoved = false;
            $("#profileImageFileName").text("");

            // Update top header profile photo
            if (self.currentProfileImage) {
              $(".profile-img img").attr("src", self.currentProfileImage);
            } else {
              $(".profile-img img").attr("src", "/upload/library/default-profile.png");
            }

            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Profile Updated!",
                text: res.msg || "Your profile details have been saved successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "Profile updated successfully.");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update profile.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving profile.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // PUBLIC CONTACT INFORMATION
    // ------------------------------------------------------------------------
    initContact: function () {
      const self = this;
      self.bindContactEvents();
      self.loadContactData();
    },

    bindContactEvents: function () {
      const self = this;

      $(document).off("input change", "#contactCompanyName, #contactCompanySiteDomain, #contactCompanyIntro, #contactSiteMobile, #contactCompanyWhatsappNumber, #contactSiteEmail, #contactCompanyAddress").on("input change", "#contactCompanyName, #contactCompanySiteDomain, #contactCompanyIntro, #contactSiteMobile, #contactCompanyWhatsappNumber, #contactSiteEmail, #contactCompanyAddress", function () {
        self.updateContactPreview();
      });

      $(document).off("submit", "#settingContactForm").on("submit", "#settingContactForm", function (e) {
        e.preventDefault();
        self.submitContactForm();
      });
    },

    loadContactData: function () {
      const self = this;

      if (self.activeContactXhr && typeof self.activeContactXhr.abort === "function") {
        self.activeContactXhr.abort();
      }

      self.activeContactXhr = $.ajax({
        url: "/api/v3/setting/contact/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#contactCompanyName").val(d.company_name || "");
            $("#contactContactPerson").val(d.company_contact_person || d.owner_name || "");
            $("#contactSiteMobile").val(d.site_mobile || d.mobile || "");
            $("#contactCompanyWhatsappNumber").val(d.company_whatsapp_number || d.whatsapp || "");
            $("#contactSiteEmail").val(d.site_email || d.email || "");
            $("#contactCompanySiteDomain").val(d.company_site_domain || d.site_domain || "");
            $("#contactCompanyIntro").val(d.company_intro || d.intro || "");
            $("#contactCompanyAddress").val(d.company_address || d.address || "");
            $("#contactCompanyGmap").val(d.company_google_map || d.maps_url || "");

            // Social media
            const soc = d.company_social_media || {};
            $("#contactFacebook").val(soc.facebook || d.facebook || "");
            $("#contactInstagram").val(soc.instagram || d.instagram || "");
            $("#contactX").val(soc.x || d.x || "");
            $("#contactLinkedin").val(soc.linkedin || d.linkedin || "");
            $("#contactYoutube").val(soc.youtube || d.youtube || "");

            self.updateContactPreview();
          }
        },
        error: function (xhr, status) {
          if (status !== "abort") {
            self.showAlert("error", "Failed to load public contact information.");
          }
        },
        complete: function () {
          self.activeContactXhr = null;
        }
      });
    },

    updateContactPreview: function () {
      const name = (typeof $("#contactCompanyName").val() === "string" ? $("#contactCompanyName").val().trim() : "") || "Company Name";
      const domain = typeof $("#contactCompanySiteDomain").val() === "string" ? $("#contactCompanySiteDomain").val().trim() : "";
      const intro = (typeof $("#contactCompanyIntro").val() === "string" ? $("#contactCompanyIntro").val().trim() : "") || "Company description and tagline will appear here...";
      const mob = typeof $("#contactSiteMobile").val() === "string" ? $("#contactSiteMobile").val().trim() : "";
      const wa = typeof $("#contactCompanyWhatsappNumber").val() === "string" ? $("#contactCompanyWhatsappNumber").val().trim() : "";
      const em = typeof $("#contactSiteEmail").val() === "string" ? $("#contactSiteEmail").val().trim() : "";
      const addr = typeof $("#contactCompanyAddress").val() === "string" ? $("#contactCompanyAddress").val().trim() : "";

      $("#previewContactCompany").text(name);
      $("#previewContactDomain").text(domain || "No website domain");
      $("#previewContactIntro").text(intro);
      $("#previewContactMobile").text(mob ? "+91 " + mob : "+91 \u2014");
      $("#previewContactWhatsapp").text(wa ? "+91 " + wa : "+91 \u2014");
      $("#previewContactEmail").text(em || "No email provided");
      $("#previewContactAddress").text(addr || "No registered address");
    },

    submitContactForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const safeVal = function (id) {
        const v = $(id).val();
        return typeof v === "string" ? v.trim() : "";
      };

      const companyName = safeVal("#contactCompanyName");
      const siteMobile = safeVal("#contactSiteMobile");

      if (!companyName) {
        self.showAlert("warning", "Company / Trade Name is required.");
        $("#contactCompanyName").focus();
        return;
      }

      if (!siteMobile) {
        self.showAlert("warning", "Customer Helpline No. is required.");
        $("#contactSiteMobile").focus();
        return;
      }

      const postData = {
        company_name: companyName,
        company_contact_person: safeVal("#contactContactPerson"),
        site_mobile: siteMobile,
        company_whatsapp_number: safeVal("#contactCompanyWhatsappNumber"),
        site_email: safeVal("#contactSiteEmail"),
        company_site_domain: safeVal("#contactCompanySiteDomain"),
        company_intro: safeVal("#contactCompanyIntro"),
        company_address: safeVal("#contactCompanyAddress"),
        company_google_map: safeVal("#contactCompanyGmap"),
        company_social_media: {
          facebook: safeVal("#contactFacebook"),
          instagram: safeVal("#contactInstagram"),
          x: safeVal("#contactX"),
          linkedin: safeVal("#contactLinkedin"),
          youtube: safeVal("#contactYoutube")
        },
        facebook: safeVal("#contactFacebook"),
        instagram: safeVal("#contactInstagram"),
        x: safeVal("#contactX"),
        linkedin: safeVal("#contactLinkedin"),
        youtube: safeVal("#contactYoutube")
      };

      const $btn = $("#btnSaveContact");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/contact/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Contact Details Saved!",
                text: res.msg || "Public contact information updated successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "Contact information saved successfully.");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update contact settings.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving contact settings.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // NOTIFICATION & ALERT RECEIVING SETTINGS (Powered by Notify360)
    // ------------------------------------------------------------------------
    initNotification: function () {
      const self = this;
      self.bindNotificationEvents();
      self.loadNotificationData();
    },

    bindNotificationEvents: function () {
      const self = this;

      $(document).off("input change", "#notifyAdminEmail, #notifyEnquiryEmail, #notifySiteEmailAlert, #notifyAdminWhatsapp, #notifyEmailEnabled, #notifyWhatsappEnabled, #notifySmsEnabled, #notifyPushEnabled, #notifyInAppEnabled").on("input change", "#notifyAdminEmail, #notifyEnquiryEmail, #notifySiteEmailAlert, #notifyAdminWhatsapp, #notifyEmailEnabled, #notifyWhatsappEnabled, #notifySmsEnabled, #notifyPushEnabled, #notifyInAppEnabled", function () {
        self.updateNotificationPreview();
      });

      // Browser Push Notification Permission Handler on Toggle
      $(document).off("change", "#notifyPushEnabled").on("change", "#notifyPushEnabled", function () {
        if ($(this).is(":checked")) {
          self.requestPushPermission(false);
        }
      });

      // Test Push Button
      $(document).off("click", "#btnTestPush").on("click", "#btnTestPush", function () {
        self.requestPushPermission(true);
      });

      // Test Audio Chime Button
      $(document).off("click", "#btnTestAudio").on("click", "#btnTestAudio", function () {
        self.playChimeSound();
      });

      $(document).off("submit", "#settingNotifyForm").on("submit", "#settingNotifyForm", function (e) {
        e.preventDefault();
        self.submitNotificationForm();
      });
    },

    requestPushPermission: function (isTest) {
      const self = this;
      if (!("Notification" in window)) {
        self.showAlert("warning", "Web Push notifications are not supported by your current browser.");
        $("#notifyPushEnabled").prop("checked", false);
        self.updateNotificationPreview();
        return;
      }

      if (Notification.permission === "granted") {
        if (isTest) {
          self.showSampleNotification();
          self.playChimeSound();
        }
        return;
      }

      if (Notification.permission === "denied") {
        self.showAlert("warning", "Browser notification permission is blocked. Please click the padlock or tune icon in your browser address bar and set Notifications to 'Allow'.");
        $("#notifyPushEnabled").prop("checked", false);
        self.updateNotificationPreview();
        return;
      }

      // Request permission
      Notification.requestPermission().then(function (permission) {
        if (permission === "granted") {
          $("#notifyPushEnabled").prop("checked", true);
          self.showSampleNotification();
          self.playChimeSound();
        } else {
          $("#notifyPushEnabled").prop("checked", false);
          self.showAlert("warning", "Push notification permission was not granted.");
        }
        self.updateNotificationPreview();
      });
    },

    showSampleNotification: function () {
      try {
        if (Notification.permission === "granted") {
          new Notification("Notify360 Alert", {
            body: "Web Push notifications are active! You will receive real-time traveler booking and lead alerts.",
            icon: "/app/dashboard/v3/default/desktop/assets/images/notify360.png"
          });
        }
      } catch (err) {
        console.warn("Could not display notification:", err);
      }
    },

    playChimeSound: function () {
      try {
        const audio = new Audio("/app/dashboard/v3/default/desktop/assets/audio/notification.wav");
        audio.play().catch(function () {
          // Fallback to Web Audio API synthesized chime
          try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = "sine";
            osc1.frequency.setValueAtTime(587.33, now); // D5
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.25);

            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = "sine";
            osc2.frequency.setValueAtTime(880, now + 0.15); // A5
            gain2.gain.setValueAtTime(0.35, now + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.15);
            osc2.stop(now + 0.5);
          } catch (e) {}
        });
      } catch (e) {}
    },

    loadNotificationData: function () {
      const self = this;

      if (self.activeNotifyXhr && typeof self.activeNotifyXhr.abort === "function") {
        self.activeNotifyXhr.abort();
      }

      self.activeNotifyXhr = $.ajax({
        url: "/api/v3/setting/notifications/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#notifyAdminEmail").val(d.admin_email || "");
            $("#notifyEnquiryEmail").val(d.enquiry_email || "");
            $("#notifySiteEmailAlert").val(d.site_email_alert || "");
            $("#notifyAdminWhatsapp").val(d.admin_whatsapp_number || "");
            $("#notifyCompanyWhatsapp").val(d.company_whatsapp_number || "");
            $("#notifySmsDefault").val(d.sms_default || "");

            const isEmailActive = d.email_notification === "1" || d.email_notification === 1 || d.admin_email_notification === "1" || d.admin_email_notification === 1;
            $("#notifyEmailEnabled").prop("checked", isEmailActive);
            $("#notifyWhatsappEnabled").prop("checked", d.enable_whatsapp_business_api === "1" || d.enable_whatsapp_business_api === 1);
            $("#notifySmsEnabled").prop("checked", d.sms_enable === "1" || d.sms_enable === 1);
            
            // Check browser push permission state
            const pushAllowed = ("Notification" in window) && Notification.permission === "granted";
            $("#notifyPushEnabled").prop("checked", (d.enable_push_notification === "1" || d.enable_push_notification === 1) && pushAllowed);
            
            $("#notifyInAppEnabled").prop("checked", d.enable_inapp_notification === "1" || d.enable_inapp_notification === 1);

            $("#evtEnquiry").prop("checked", d.company_whatsapp_enquiry_thank_you === "1" || d.company_whatsapp_enquiry_thank_you === 1);
            $("#evtPayment").prop("checked", d.company_whatsapp_payment_request === "1" || d.company_whatsapp_payment_request === 1);
            $("#evtItinerary").prop("checked", d.company_whatsapp_itinerary_sent === "1" || d.company_whatsapp_itinerary_sent === 1);

            self.updateNotificationPreview();
          }
        },
        error: function (xhr, status) {
          if (status !== "abort") {
            self.showAlert("error", "Failed to load alert notification settings.");
          }
        },
        complete: function () {
          self.activeNotifyXhr = null;
        }
      });
    },

    updateNotificationPreview: function () {
      const adminEmail = $("#notifyAdminEmail").val().trim();
      const enquiryEmail = $("#notifyEnquiryEmail").val().trim();
      const securityEmail = $("#notifySiteEmailAlert").val().trim();
      const adminWhatsapp = $("#notifyAdminWhatsapp").val().trim();

      $("#previewAdminEmail").text(adminEmail || "None");
      $("#previewEnquiryEmail").text(enquiryEmail || (adminEmail || "None"));
      $("#previewSecurityEmail").text(securityEmail || (adminEmail || "Same as Admin"));

      const waFormatted = adminWhatsapp ? "+91 " + adminWhatsapp : "+91 \u2014";
      $("#previewAdminWhatsapp").text(waFormatted);
      $("#previewAdminWhatsapp2").text(waFormatted);

      // Channel Matrix Status Badges
      const isEmail = $("#notifyEmailEnabled").is(":checked");
      const isWa = $("#notifyWhatsappEnabled").is(":checked");
      const isSms = $("#notifySmsEnabled").is(":checked");
      const isPush = $("#notifyPushEnabled").is(":checked");
      const isInApp = $("#notifyInAppEnabled").is(":checked");

      $("#badgeStatusEmail").attr("class", isEmail ? "v3-status-badge status-active" : "v3-status-badge status-inactive").text(isEmail ? "Active" : "Disabled");
      $("#badgeStatusWhatsapp").attr("class", isWa ? "v3-status-badge status-active" : "v3-status-badge status-inactive").text(isWa ? "Active" : "Disabled");
      $("#badgeStatusSms").attr("class", isSms ? "v3-status-badge status-active" : "v3-status-badge status-inactive").text(isSms ? "Active" : "Disabled");
      $("#badgeStatusPush").attr("class", isPush ? "v3-status-badge status-active" : "v3-status-badge status-inactive").text(isPush ? "Active" : "Disabled");
      $("#badgeStatusInApp").attr("class", isInApp ? "v3-status-badge status-active" : "v3-status-badge status-inactive").text(isInApp ? "Active" : "Disabled");
    },

    submitNotificationForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const adminEmail = $("#notifyAdminEmail").val().trim();
      const adminWhatsapp = $("#notifyAdminWhatsapp").val().trim();

      if (!adminEmail) {
        self.showAlert("warning", "Primary Admin Alert Email is required.");
        $("#notifyAdminEmail").focus();
        return;
      }

      const postData = {
        notification_email_notification: $("#notifyEmailEnabled").is(":checked") ? "1" : "0",
        notification_admin_email_notification: $("#notifyEmailEnabled").is(":checked") ? "1" : "0",
        notification_admin_email: adminEmail,
        notification_enquiry_email: $("#notifyEnquiryEmail").val().trim(),
        notification_site_email_alert: $("#notifySiteEmailAlert").val().trim(),
        notification_enable_whatsapp_business_api: $("#notifyWhatsappEnabled").is(":checked") ? "1" : "0",
        notification_admin_whatsapp_number: adminWhatsapp,
        notification_company_whatsapp_number: $("#notifyCompanyWhatsapp").val().trim(),
        notification_sms_enable: $("#notifySmsEnabled").is(":checked") ? "1" : "0",
        notification_sms_default: $("#notifySmsDefault").val().trim(),
        notification_enable_push_notification: $("#notifyPushEnabled").is(":checked") ? "1" : "0",
        notification_enable_inapp_notification: $("#notifyInAppEnabled").is(":checked") ? "1" : "0",
        notification_company_whatsapp_enquiry_thank_you: $("#evtEnquiry").is(":checked") ? "1" : "0",
        notification_company_whatsapp_payment_request: $("#evtPayment").is(":checked") ? "1" : "0",
        notification_company_whatsapp_itinerary_sent: $("#evtItinerary").is(":checked") ? "1" : "0"
      };

      const $btn = $("#btnSaveNotify");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/notifications/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Settings Saved!",
                text: res.msg || "Notification channels updated successfully with Notify360.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "Notification settings saved successfully.");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update notification settings.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving notification settings.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // BANK ACCOUNTS & UPI SETTINGS
    // ------------------------------------------------------------------------
    initBanking: function () {
      const self = this;
      self.bindBankingEvents();
      self.loadBankingData();
    },

    bindBankingEvents: function () {
      const self = this;

      $(document).off("input change", "#bankName, #bankAcName, #bankAcNumber, #bankIfsc, #upiDetails").on("input change", "#bankName, #bankAcName, #bankAcNumber, #bankIfsc, #upiDetails", function () {
        self.updateBankingPreview();
      });

      $(document).off("submit", "#settingBankingForm").on("submit", "#settingBankingForm", function (e) {
        e.preventDefault();
        self.submitBankingForm();
      });
    },

    loadBankingData: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/setting/banking/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#bankName").val(d.bank_name || "");
            $("#bankAcName").val(d.bank_ac_name || "");
            $("#bankAcNumber").val(d.bank_ac_number || "");
            $("#bankIfsc").val(d.bank_ifsc || "");
            $("#bankAddress").val(d.bank_address || "");
            $("#bankDetails").val(d.bank_details || "");
            $("#upiDetails").val(d.upi_details || d.pg_upi_address || "");
            self.updateBankingPreview();
          }
        }
      });
    },

    updateBankingPreview: function () {
      const bankName = $("#bankName").val().trim() || "Bank Name";
      const acName = $("#bankAcName").val().trim() || "Company Name";
      const acNum = $("#bankAcNumber").val().trim() || "•••• •••• ••••";
      const ifsc = $("#bankIfsc").val().trim().toUpperCase() || "IFSC0000000";
      const upi = $("#upiDetails").val().trim() || "company@upi";

      $("#previewBankName").text(bankName);
      $("#previewBankAcName").text(acName);
      $("#previewBankAcNumber").text(acNum);
      $("#previewBankIfsc").text(ifsc);
      $("#previewUpiVpa").text(upi);

      const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=" + encodeURIComponent("upi://pay?pa=" + upi + "&pn=" + encodeURIComponent(acName) + "&cu=INR");
      $("#previewUpiQrImage").attr("src", qrUrl);
    },

    submitBankingForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const bankName = $("#bankName").val().trim();
      const acName = $("#bankAcName").val().trim();
      const acNum = $("#bankAcNumber").val().trim();
      const ifsc = $("#bankIfsc").val().trim();

      if (!bankName || !acName || !acNum || !ifsc) {
        self.showAlert("warning", "Please fill in all required bank details.");
        return;
      }

      const postData = {
        bank_name: bankName,
        bank_ac_name: acName,
        bank_ac_number: acNum,
        bank_ifsc: ifsc,
        bank_address: $("#bankAddress").val().trim(),
        bank_details: $("#bankDetails").val().trim(),
        upi_details: $("#upiDetails").val().trim()
      };

      const $btn = $("#btnSaveBanking");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/banking/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          if (res && res.status === "success") {
            self.showAlert("success", res.msg || "Bank & UPI details updated.");
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update bank details.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving bank details.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // SEO & SEARCH INDEXING SETTINGS
    // ------------------------------------------------------------------------
    initSeo: function () {
      const self = this;
      self.bindSeoEvents();
      self.loadSeoData();
    },

    bindSeoEvents: function () {
      const self = this;
      $(document).off("input change", "#siteMetaTitle, #siteMetaDesc, input[name='site_index']").on("input change", "#siteMetaTitle, #siteMetaDesc, input[name='site_index']", function () {
        self.updateSeoPreview();
      });

      $(document).off("submit", "#settingSeoForm").on("submit", "#settingSeoForm", function (e) {
        e.preventDefault();
        self.submitSeoForm();
      });
    },

    loadSeoData: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/setting/seo/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#siteMetaTitle").val(d.site_name || "");
            $("#siteMetaDesc").val(d.site_description || "");
            $("#siteMetaKw").val(d.site_keyword || "");

            if (d.site_index === "noindex, nofollow" || d.site_index === "no") {
              $("#indexNo").prop("checked", true);
            } else {
              $("#indexYes").prop("checked", true);
            }

            self.updateSeoPreview(d.site_domain);
          }
        }
      });
    },

    updateSeoPreview: function (customDomain) {
      const title = $("#siteMetaTitle").val().trim() || "Best Holiday Packages & Tour Itineraries";
      const desc = $("#siteMetaDesc").val().trim() || "Explore top rated customized holiday tour packages, luxury hotel stays, and unforgettable travel experiences.";
      const domain = customDomain || window.location.hostname || "yourdomain.com";

      $("#previewSeoTitle").text(title);
      $("#previewSeoDesc").text(desc);
      $("#previewSeoDomain").text(domain);
    },

    submitSeoForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const title = $("#siteMetaTitle").val().trim();
      const desc = $("#siteMetaDesc").val().trim();
      const kw = $("#siteMetaKw").val().trim();
      const indexVal = $("input[name='site_index']:checked").val() || "index, follow";

      if (!title) {
        self.showAlert("warning", "Meta Title is required.");
        $("#siteMetaTitle").focus();
        return;
      }

      const postData = {
        site_name: title,
        site_description: desc,
        site_keyword: kw,
        site_index: indexVal
      };

      const $btn = $("#btnSaveSeo");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/seo/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "SEO Settings Saved!",
                text: res.msg || "SEO metadata and search indexing preferences saved successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "SEO settings saved successfully.");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update SEO settings.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving SEO settings.");
        }
      });
    },

    // ------------------------------------------------------------------------
    // APP & BUSINESS CONFIGURATION
    // ------------------------------------------------------------------------
    initAppConfig: function () {
      const self = this;
      self.initAppConfigSelect2();
      self.bindAppConfigEvents();
      self.updateAppConfigPreview();
      self.loadAppConfigData();
    },

    initAppConfigSelect2: function () {
      if (typeof $.fn.select2 !== "undefined") {
        $("#settingAppConfigApp .selectJS").each(function () {
          const $sel = $(this);
          if ($sel.data("select2")) {
            $sel.select2("destroy");
          }
          $sel.select2({
            width: "100%",
            dropdownAutoWidth: false,
            minimumResultsForSearch: -1
          });
        });
      }
    },

    bindAppConfigEvents: function () {
      const self = this;

      // Nature Option Card Selection
      $(document).off("click", ".v3-nature-option-card").on("click", ".v3-nature-option-card", function () {
        const $card = $(this);
        const nature = $card.data("nature");
        if (!nature) return;

        $(".v3-nature-option-card").removeClass("is-selected");
        $card.addClass("is-selected");
        $("#configCompanyNature").val(nature);

        // Toggle Fleet & Transporter tariff card visibility
        if (nature === "transporter") {
          $("#cardFleetTariffPreferences").removeClass("is-hidden");
          $("#previewFleetRow").removeClass("is-hidden");
          self.initAppConfigSelect2();
        } else {
          $("#cardFleetTariffPreferences").addClass("is-hidden");
          $("#previewFleetRow").addClass("is-hidden");
        }

        // Default aside menu groups based on business nature:
        // By default if Business Nature is "Travel Agency" and "DMC" then only show "Tour Packages", "Supply & Vendors"
        // As on now default hide "Marketing & CMS" for all
        if (nature === "travel_agency" || nature === "dmc") {
          $("#cfgAsideMenuPackages").prop("checked", true);
          $("#cfgAsideMenuVendors").prop("checked", true);
        } else {
          $("#cfgAsideMenuPackages").prop("checked", false);
          $("#cfgAsideMenuVendors").prop("checked", false);
        }
        $("#cfgAsideMenuCms").prop("checked", false);

        self.updateAppConfigPreview();
      });

      // Currency Auto-Symbol Change
      $(document).off("change", "#cfgCurrencyDefault").on("change", "#cfgCurrencyDefault", function () {
        const curr = $(this).val();
        const symbolMap = {
          INR: "₹",
          USD: "$",
          EUR: "€",
          AED: "د.إ",
          GBP: "£",
          THB: "฿",
          SGD: "S$",
          CAD: "C$",
          AUD: "A$"
        };
        const sym = symbolMap[curr] || "₹";
        $("#cfgCurrencySymbol").val(sym);
        $("#cfgCurrencyIcon").text(sym);
        $("#cfgCurrencySymbolIcon").text(sym);
        self.updateAppConfigPreview();
      });

      // Manual currency symbol input update
      $(document).off("input", "#cfgCurrencySymbol").on("input", "#cfgCurrencySymbol", function () {
        const sym = $(this).val().trim() || "₹";
        $("#cfgCurrencyIcon").text(sym);
        $("#cfgCurrencySymbolIcon").text(sym);
      });

      // Advance Payment Percent Change (Custom Toggle)
      $(document).off("change", "#cfgAdvancePaymentPercent").on("change", "#cfgAdvancePaymentPercent", function () {
        const val = $(this).val();
        if (val === "custom") {
          $("#cfgAdvanceCustomGroup").removeClass("is-hidden");
          if (!$("#cfgAdvancePaymentPercentCustom").val()) {
            $("#cfgAdvancePaymentPercentCustom").val("35");
          }
          $("#cfgAdvancePaymentPercentCustom").focus();
        } else {
          $("#cfgAdvanceCustomGroup").addClass("is-hidden");
        }
        self.updateAppConfigPreview();
      });

      // Select2 & Input Changes to Live Preview
      $(document).off("input change", "#settingAppConfigApp .selectJS, #cfgCurrencySymbol, #cfgAdvancePaymentPercentCustom, #cfgLeadViewPaymentLink, #cfgLeadViewRecordPayment, #cfgLeadViewConvertSale, #cfgLeadViewCallButton, #cfgLeadViewWhatsappButton, #cfgLeadViewSendItinerary, #cfgLeadViewSendQuotation, #cfgAsideMenuPackages, #cfgAsideMenuVendors, #cfgAsideMenuCms").on("input change", "#settingAppConfigApp .selectJS, #cfgCurrencySymbol, #cfgAdvancePaymentPercentCustom, #cfgLeadViewPaymentLink, #cfgLeadViewRecordPayment, #cfgLeadViewConvertSale, #cfgLeadViewCallButton, #cfgLeadViewWhatsappButton, #cfgLeadViewSendItinerary, #cfgLeadViewSendQuotation, #cfgAsideMenuPackages, #cfgAsideMenuVendors, #cfgAsideMenuCms", function () {
        self.updateAppConfigPreview();
      });

      // Locked feature notification click
      $(document).off("click", ".v3-feature-toggle-row.is-locked").on("click", ".v3-feature-toggle-row.is-locked", function (e) {
        e.preventDefault();
        const swalFn = window.swal || window.Swal;
        const alertMsg = "This premium action requires an upgrade from your current package. Please contact support or upgrade your subscription to unlock.";
        if (swalFn) {
          swalFn({
            title: "Upgrade Required",
            text: alertMsg,
            type: "warning",
            icon: "warning",
            confirmButtonText: "Got it"
          });
        } else {
          alert(alertMsg);
        }
      });

      // App Config Form Submission
      $(document).off("submit", "#settingAppConfigForm").on("submit", "#settingAppConfigForm", function (e) {
        e.preventDefault();
        self.saveAppConfigData();
      });
    },

    loadAppConfigData: function () {
      const self = this;

      $.ajax({
        type: "GET",
        url: "/api/v3/setting/app/",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            const nature = d.company_nature || "travel_agency";
            $("#configCompanyNature").val(nature);

            $(".v3-nature-option-card").removeClass("is-selected");
            $(`.v3-nature-option-card[data-nature="${nature}"]`).addClass("is-selected");

            const symbolMap = {
              INR: "₹",
              USD: "$",
              EUR: "€",
              AED: "د.إ",
              GBP: "£",
              THB: "฿",
              SGD: "S$",
              CAD: "C$",
              AUD: "A$"
            };

            // Financial & Currency
            if (d.currency_default) $("#cfgCurrencyDefault").val(d.currency_default).trigger("change.select2");
            const activeSym = d.currency_symbol || symbolMap[d.currency_default] || "₹";
            $("#cfgCurrencySymbol").val(activeSym);
            $("#cfgCurrencyIcon").text(activeSym);
            $("#cfgCurrencySymbolIcon").text(activeSym);
            if (d.tax_mode) $("#cfgTaxMode").val(d.tax_mode).trigger("change.select2");
            if (d.quotation_validity_days) $("#cfgQuotationValidityDays").val(d.quotation_validity_days).trigger("change.select2");
            
            // Advance payment deposit
            const advVal = String(d.advance_payment_percent || "25");
            const standardDeposits = ["20", "25", "30", "50", "100"];
            if (standardDeposits.includes(advVal)) {
              $("#cfgAdvancePaymentPercent").val(advVal).trigger("change.select2");
              $("#cfgAdvancePaymentPercentCustom").val(advVal);
              $("#cfgAdvanceCustomGroup").addClass("is-hidden");
            } else {
              $("#cfgAdvancePaymentPercent").val("custom").trigger("change.select2");
              $("#cfgAdvancePaymentPercentCustom").val(advVal);
              $("#cfgAdvanceCustomGroup").removeClass("is-hidden");
            }

            // Lead Routing
            if (d.lead_assignment_mode) $("#cfgLeadAssignmentMode").val(d.lead_assignment_mode).trigger("change.select2");
            if (d.lead_default_followup_hours) $("#cfgLeadFollowupHours").val(d.lead_default_followup_hours).trigger("change.select2");
            if (d.lead_sla_inactivity_hours) $("#cfgLeadSlaHours").val(d.lead_sla_inactivity_hours).trigger("change.select2");

            // Fleet & Transport Tariff
            if (d.transporter_tariff_mode) $("#cfgTransporterTariffMode").val(d.transporter_tariff_mode).trigger("change.select2");
            if (d.transporter_toll_policy) $("#cfgTransporterTollPolicy").val(d.transporter_toll_policy).trigger("change.select2");
            if (d.transporter_parking_policy) $("#cfgTransporterParkingPolicy").val(d.transporter_parking_policy).trigger("change.select2");
            if (d.transporter_garage_policy) $("#cfgTransporterGaragePolicy").val(d.transporter_garage_policy).trigger("change.select2");
            if (d.transporter_driver_allowance) $("#cfgTransporterDriverAllowance").val(d.transporter_driver_allowance).trigger("change.select2");

            // Lead View Visibility Toggles
            const isOptEnabled = function (v) {
              return v !== "0" && v !== 0 && v !== false && v !== "false";
            };

            const isPkgSix = (d.is_package_six === true || d.is_package_six === 1 || d.is_package_six === "1" || $("#settingAppConfigApp").data("is-package-six") === 1 || $("#settingAppConfigApp").data("is-package-six") === "1");
            self.isPackageSix = isPkgSix;

            if (isPkgSix) {
              $("#cfgLeadViewPaymentLink, #cfgLeadViewRecordPayment, #cfgLeadViewSendItinerary, #cfgLeadViewSendQuotation")
                .prop("disabled", true)
                .prop("checked", false)
                .closest(".v3-feature-toggle-row")
                .addClass("is-locked");
            } else {
              $("#cfgLeadViewPaymentLink").prop("checked", isOptEnabled(d.lead_view_show_payment_link));
              $("#cfgLeadViewRecordPayment").prop("checked", isOptEnabled(d.lead_view_show_record_payment));
              $("#cfgLeadViewSendItinerary").prop("checked", isOptEnabled(d.lead_view_show_send_itinerary));
              $("#cfgLeadViewSendQuotation").prop("checked", isOptEnabled(d.lead_view_show_send_quotation));
            }

            $("#cfgLeadViewConvertSale").prop("checked", isOptEnabled(d.lead_view_show_convert_sale));
            $("#cfgLeadViewCallButton").prop("checked", isOptEnabled(d.lead_view_show_call_button));
            $("#cfgLeadViewWhatsappButton").prop("checked", isOptEnabled(d.lead_view_show_whatsapp_button));

            // Aside Navigation Menu Groups
            $("#cfgAsideMenuPackages").prop("checked", isOptEnabled(d.aside_menu_show_packages));
            $("#cfgAsideMenuVendors").prop("checked", isOptEnabled(d.aside_menu_show_vendors));
            $("#cfgAsideMenuCms").prop("checked", isOptEnabled(d.aside_menu_show_cms));

            // Transporter conditional card
            if (nature === "transporter") {
              $("#cardFleetTariffPreferences").removeClass("is-hidden");
              $("#previewFleetRow").removeClass("is-hidden");
            } else {
              $("#cardFleetTariffPreferences").addClass("is-hidden");
              $("#previewFleetRow").addClass("is-hidden");
            }

            self.initAppConfigSelect2();
            self.updateAppConfigPreview();
          }
        },
        error: function () {
          console.warn("Could not fetch app configuration.");
        }
      });
    },

    updateAppConfigPreview: function () {
      const self = this;
      const nature = $("#configCompanyNature").val() || "travel_agency";

      const natureMeta = {
        travel_agency: {
          title: "Travel Agency & Tour Operator",
          desc: "Optimized for holiday package sales & proposal sharing.",
          icon: "fi fi-rr-plane-departure",
          action: "Send Tour Itinerary",
          actionIcon: "fi fi-rr-paper-plane"
        },
        transporter: {
          title: "Transporter & Fleet Operator",
          desc: "Optimized for vehicle dispatching & route quotations.",
          icon: "fi fi-rr-bus",
          action: "Send Quotation",
          actionIcon: "fi fi-rr-document"
        },
        dmc: {
          title: "Destination Management (DMC)",
          desc: "Optimized for inbound ground services & B2B contracts.",
          icon: "fi fi-rr-city",
          action: "Send Tour Itinerary",
          actionIcon: "fi fi-rr-paper-plane"
        },
        hotel_resort: {
          title: "Hotel, Resort & Homestay",
          desc: "Optimized for room reservations & stay hospitality.",
          icon: "fi fi-rr-bed-alt",
          action: "Send Tour Itinerary",
          actionIcon: "fi fi-rr-paper-plane"
        },
        spa_wellness: {
          title: "Spa, Wellness & Retreat",
          desc: "Optimized for therapy appointments & wellness plans.",
          icon: "fi fi-rr-flower",
          action: "Send Tour Itinerary",
          actionIcon: "fi fi-rr-paper-plane"
        },
        general: {
          title: "General Business & Custom",
          desc: "Standard sales pipeline and customized client estimates.",
          icon: "fi fi-rr-briefcase",
          action: "Send Tour Itinerary",
          actionIcon: "fi fi-rr-paper-plane"
        }
      };

      const meta = natureMeta[nature] || natureMeta.travel_agency;

      $("#previewNatureIcon").html(`<i class="${meta.icon}"></i>`);
      $("#previewNatureTitle").text(meta.title);
      $("#previewNatureDesc").text(meta.desc);
      $("#previewLeadAction").html(`<i class="${meta.actionIcon}"></i> <span>${meta.action}</span>`);

      // Currency & Tax Preview
      const curr = $("#cfgCurrencyDefault").val() || "INR";
      const sym = $("#cfgCurrencySymbol").val() || "₹";
      $("#cfgCurrencyIcon").text(sym);
      $("#cfgCurrencySymbolIcon").text(sym);
      const taxMode = $("#cfgTaxMode").val() || "exclusive";
      const taxLabel = taxMode === "inclusive" ? "Inclusive Tax" : (taxMode === "exempt" ? "Tax Exempt" : "Exclusive Tax");
      $("#previewCurrencyText").text(`${curr} (${sym}) • ${taxLabel}`);

      // Validity Preview
      const valDays = $("#cfgQuotationValidityDays").val() || "7";
      $("#previewValidityText").text(`${valDays} Days`);

      // Fleet Tariff Preview
      if (nature === "transporter") {
        $("#cardFleetTariffPreferences").removeClass("is-hidden");
        $("#previewFleetRow").removeClass("is-hidden");
        const tMode = $("#cfgTransporterTariffMode").val() || "per_km";
        const tLabel = tMode === "fixed_route" ? "Fixed Route" : (tMode === "daily_hire" ? "Daily Hire" : "Per-Kilometer");
        $("#previewFleetTariff").text(tLabel);
      } else {
        $("#cardFleetTariffPreferences").addClass("is-hidden");
        $("#previewFleetRow").addClass("is-hidden");
      }

      // Requirement: "Send Tour Itinerary" visible default and show "Send Quotation" only if business nature is "transporter" else hide.
      // Requirement: Only 1 option will be visible from both.
      const isTransporter = (nature === "transporter");
      if (isTransporter) {
        $("#rowLeadViewSendQuotation").removeClass("is-hidden");
        $("#rowLeadViewSendItinerary").addClass("is-hidden");
      } else {
        $("#rowLeadViewSendItinerary").removeClass("is-hidden");
        $("#rowLeadViewSendQuotation").addClass("is-hidden");
      }

      // Fast Tile Badges
      const isPkg6 = self.isPackageSix || $("#settingAppConfigApp").data("is-package-six") === 1 || $("#settingAppConfigApp").data("is-package-six") === "1";
      const showPayLink = !isPkg6 && $("#cfgLeadViewPaymentLink").is(":checked");
      const showRecPay = !isPkg6 && $("#cfgLeadViewRecordPayment").is(":checked");
      const showItinerary = !isTransporter && !isPkg6 && $("#cfgLeadViewSendItinerary").is(":checked");
      const showQuotation = isTransporter && !isPkg6 && $("#cfgLeadViewSendQuotation").is(":checked");
      const showConvSale = $("#cfgLeadViewConvertSale").is(":checked");
      const showCall = $("#cfgLeadViewCallButton").is(":checked");
      const showWa = $("#cfgLeadViewWhatsappButton").is(":checked");

      $("#tagPrevCallButton").toggleClass("tag-active", showCall).toggleClass("is-hidden", !showCall);
      $("#tagPrevWhatsappButton").toggleClass("tag-active", showWa).toggleClass("is-hidden", !showWa);
      $("#tagPrevItinerary").toggleClass("tag-active", showItinerary).toggleClass("is-hidden", !showItinerary);
      $("#tagPrevQuotation").toggleClass("tag-active", showQuotation).toggleClass("is-hidden", !showQuotation);
      $("#tagPrevPaymentLink").toggleClass("tag-active", showPayLink).toggleClass("is-hidden", !showPayLink);
      $("#tagPrevRecordPayment").toggleClass("tag-active", showRecPay).toggleClass("is-hidden", !showRecPay);
      $("#tagPrevConvertSale").toggleClass("tag-active", showConvSale).toggleClass("is-hidden", !showConvSale);

      // Aside Navigation Menu Groups Preview
      const showPackages = $("#cfgAsideMenuPackages").is(":checked");
      const showVendors = $("#cfgAsideMenuVendors").is(":checked");
      const showCms = $("#cfgAsideMenuCms").is(":checked");

      $("#tagPrevMenuPackages").toggleClass("tag-active", showPackages).toggleClass("is-hidden", !showPackages);
      $("#tagPrevMenuVendors").toggleClass("tag-active", showVendors).toggleClass("is-hidden", !showVendors);
      $("#tagPrevMenuCms").toggleClass("tag-active", showCms).toggleClass("is-hidden", !showCms);
    },

    saveAppConfigData: function () {
      const self = this;
      if (self.isSubmitting) return;

      const $btn = $("#btnSaveAppConfig");
      const origHtml = $btn.html();

      self.isSubmitting = true;
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Saving...');

      const rawAdv = $("#cfgAdvancePaymentPercent").val();
      const advPercent = (rawAdv === "custom")
        ? ($("#cfgAdvancePaymentPercentCustom").val().trim() || "25")
        : (rawAdv || "25");

      const nature = $("#configCompanyNature").val() || "travel_agency";
      const isTransporter = (nature === "transporter");

      const payload = {
        company_nature: nature,
        currency_default: $("#cfgCurrencyDefault").val(),
        currency_symbol: $("#cfgCurrencySymbol").val() || "₹",
        tax_mode: $("#cfgTaxMode").val(),
        quotation_validity_days: $("#cfgQuotationValidityDays").val(),
        advance_payment_percent: advPercent,
        lead_assignment_mode: $("#cfgLeadAssignmentMode").val(),
        lead_default_followup_hours: $("#cfgLeadFollowupHours").val(),
        lead_sla_inactivity_hours: $("#cfgLeadSlaHours").val(),
        transporter_tariff_mode: $("#cfgTransporterTariffMode").val(),
        transporter_toll_policy: $("#cfgTransporterTollPolicy").val(),
        transporter_parking_policy: $("#cfgTransporterParkingPolicy").val(),
        transporter_garage_policy: $("#cfgTransporterGaragePolicy").val(),
        transporter_driver_allowance: $("#cfgTransporterDriverAllowance").val(),
        lead_view_show_send_itinerary: $("#cfgLeadViewSendItinerary").is(":checked") ? "1" : "0",
        lead_view_show_send_quotation: $("#cfgLeadViewSendQuotation").is(":checked") ? "1" : "0",
        lead_view_show_payment_link: $("#cfgLeadViewPaymentLink").is(":checked") ? "1" : "0",
        lead_view_show_record_payment: $("#cfgLeadViewRecordPayment").is(":checked") ? "1" : "0",
        lead_view_show_convert_sale: $("#cfgLeadViewConvertSale").is(":checked") ? "1" : "0",
        lead_view_show_call_button: $("#cfgLeadViewCallButton").is(":checked") ? "1" : "0",
        lead_view_show_whatsapp_button: $("#cfgLeadViewWhatsappButton").is(":checked") ? "1" : "0",
        aside_menu_show_packages: $("#cfgAsideMenuPackages").is(":checked") ? "1" : "0",
        aside_menu_show_vendors: $("#cfgAsideMenuVendors").is(":checked") ? "1" : "0",
        aside_menu_show_cms: $("#cfgAsideMenuCms").is(":checked") ? "1" : "0"
      };

      $.ajax({
        type: "POST",
        url: "/api/v3/setting/app/",
        data: JSON.stringify(payload),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Configuration Saved!",
                text: res.msg || "App and business configuration saved successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "App configuration saved successfully.");
            }
            self.updateAppConfigPreview();
          } else {
            self.showAlert("error", res ? res.msg : "Failed to save configuration.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving app configuration.");
        }
      });
    },

    showAlert: function (type, msg) {
      const swalFn = window.swal || window.Swal;
      if (swalFn) {
        swalFn({
          title: type === "success" ? "Success!" : (type === "warning" ? "Notice" : "Error"),
          text: msg,
          type: type,
          icon: type
        });
      } else {
        alert(msg);
      }
    }
  };

  function selfEscapeHTML(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.V3Settings = V3Settings;

  $(function () {
    V3Settings.init();
  });
})(window, jQuery);
