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

    init: function () {
      if ($("#settingConfigApp").length > 0) {
        this.initConfig();
      }
      if ($("#settingProfileApp").length > 0) {
        this.initProfile();
      }
    },

    // ------------------------------------------------------------------------
    // WEBSITE CONFIGURATION
    // ------------------------------------------------------------------------
    initConfig: function () {
      const self = this;
      self.bindConfigEvents();
      self.loadConfigData();
    },

    bindConfigEvents: function () {
      const self = this;

      // Theme Preset Buttons
      $(document).off("click", ".theme-preset-btn").on("click", ".theme-preset-btn", function () {
        const color = $(this).data("color");
        if (color) {
          $("#configThemeColorPicker").val(color);
          $("#configThemeColorInput").val(color);
        }
      });

      // Color Picker Sync
      $(document).off("input change", "#configThemeColorPicker").on("input change", "#configThemeColorPicker", function () {
        $("#configThemeColorInput").val($(this).val());
      });

      $(document).off("input change", "#configThemeColorInput").on("input change", "#configThemeColorInput", function () {
        const val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
          $("#configThemeColorPicker").val(val);
        }
      });

      // Live Google Search Result Preview
      $(document).off("input change", "#configSiteName, #configSiteDescription, input[name='site_index']").on("input change", "#configSiteName, #configSiteDescription, input[name='site_index']", function () {
        self.updateSearchPreview();
      });

      // Submit Configuration Form
      $(document).off("submit", "#settingConfigForm").on("submit", "#settingConfigForm", function (e) {
        e.preventDefault();
        self.submitConfigForm();
      });
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
            $("#configSiteLang").val(d.site_lang || "en");
            $("#configSiteDescription").val(d.site_description || "");
            $("#configSiteKeyword").val(d.site_keyword || "");

            if (d.site_index === "noindex, nofollow" || d.site_index === "no") {
              $("#indexNo").prop("checked", true);
            } else {
              $("#indexYes").prop("checked", true);
            }

            const themeColor = d.site_theme_color || "#4f46e5";
            $("#configThemeColorPicker").val(themeColor);
            $("#configThemeColorInput").val(themeColor);

            self.updateSearchPreview();
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

    updateSearchPreview: function () {
      const name = $("#configSiteName").val().trim() || "Website Title";
      const desc = $("#configSiteDescription").val().trim() || "Website description snippet will appear here as you configure metadata...";
      const domain = $("#configSiteDomain").val() || window.location.hostname;
      const isIndex = $("#indexYes").is(":checked");

      $("#previewSearchDomain").text("https://" + domain);
      $("#previewSearchTitle").text(name);
      $("#previewSearchSnippet").text(desc);

      if (isIndex) {
        $("#previewIndexBadge").attr("class", "v3-status-badge status-active").html('<span class="status-pulse-dot"></span> Indexed');
      } else {
        $("#previewIndexBadge").attr("class", "v3-status-badge status-inactive").text("Noindex");
      }
    },

    submitConfigForm: function () {
      const self = this;
      if (self.isSubmitting) return;

      const siteName = $("#configSiteName").val().trim();
      const siteDescription = $("#configSiteDescription").val().trim();
      const siteKeyword = $("#configSiteKeyword").val().trim();
      const siteLang = $("#configSiteLang").val();
      const siteIndex = $("input[name='site_index']:checked").val() || "index, follow";
      const siteTheme = $("#configThemeColorInput").val().trim() || "#4f46e5";

      if (!siteName) {
        self.showAlert("warning", "Website Title is required.");
        $("#configSiteName").focus();
        return;
      }

      const postData = {
        site_name: siteName,
        site_description: siteDescription,
        site_keyword: siteKeyword,
        site_lang: siteLang,
        site_index: siteIndex,
        site_theme_color: siteTheme
      };

      const $btn = $("#btnSaveConfig");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/configuration/",
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
                title: "Configuration Saved!",
                text: res.msg || "Website configuration updated successfully.",
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

      // Submit Profile Form
      $(document).off("submit", "#settingProfileForm").on("submit", "#settingProfileForm", function (e) {
        e.preventDefault();
        self.submitProfileForm();
      });
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

      $("#previewProfileAvatar").text(initials);
      $("#previewProfileName").text(dn);

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

      const postData = {
        first_name: firstName,
        last_name: lastName,
        display_name: displayName,
        bio: bio,
        mobile: mobile,
        alt_mobile: altMobile,
        alt_email: altEmail
      };

      const $btn = $("#btnSaveProfile");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Updating...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: "/api/v3/setting/profile/",
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
})(window, jQuery);
