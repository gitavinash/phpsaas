/**
 * V3 Company & Branch Office Update Frontend Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/company-update.js
 */

(function (window, $) {
  "use strict";

  const V3CompanyUpdate = {
    detailsApi: "/api/v3/company/details/",
    updateApi: "/api/v3/company/update/",
    statesApi: "/api/v3/geo/states/",
    citiesApi: "/api/v3/geo/cities/",
    companyId: "",
    companyData: null,
    selectedLogoFile: null,
    existingLogoUrl: "",

    init: function () {
      const $root = $("#updateCompanyApp");
      if (!$root.length) return;

      this.companyId = $root.data("company-id") || $("#editCompanyId").val();
      this.selectedLogoFile = null;
      this.existingLogoUrl = "";
      this.companyData = null;

      if (!this.companyId) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", "Invalid company identifier.", "error");
        }
        setTimeout(function () {
          window.location.href = "/dashboard/company/list/";
        }, 1500);
        return;
      }

      this.initSelect2();
      this.bindEvents();
      this.loadStatesAndData();
    },

    initSelect2: function () {
      if ($.fn.select2) {
        $("#companyType, #companyStatus").select2({
          minimumResultsForSearch: Infinity,
          width: "100%"
        });

        $("#companyState, #companyCity").select2({
          width: "100%",
          placeholder: "Select from list"
        });
      }
    },

    loadStatesAndData: function () {
      const self = this;
      const $state = $("#companyState");

      // 1. Fetch States
      $.ajax({
        url: self.statesApi,
        type: "GET",
        data: { country_id: 101 },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && Array.isArray(res.states)) {
            $state.empty().append('<option value="">Select State</option>');
            res.states.forEach(function (s) {
              $state.append(new Option(s.name, s.id));
            });
            if ($.fn.select2) {
              $state.trigger("change.select2");
            }
          }
          // 2. Fetch Company Details after states are loaded
          self.loadCompanyDetails();
        },
        error: function () {
          self.loadCompanyDetails();
        }
      });
    },

    loadCompanyDetails: function () {
      const self = this;

      $.ajax({
        url: self.detailsApi,
        type: "GET",
        data: { id: self.companyId },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.company) {
            self.companyData = res.company;
            self.populateForm(res.company);
          } else {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", (res && res.msg) || "Failed to load company details.", "error");
            }
          }
        },
        error: function () {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Network error while loading company details.", "error");
          }
        }
      });
    },

    populateForm: function (c) {
      const self = this;

      $("#companyName").val(c.name || "");
      $("#companyCode").val(c.code || "");
      $("#companyDescription").val(c.description || "");
      $("#companyEmail").val(c.email || "");
      $("#companyMobile").val(c.mobile || "");
      $("#companyGst").val(c.gst || "");
      $("#companyPan").val(c.pan || "");
      $("#companyCin").val(c.cin || "");

      if (c.type) {
        $("#companyType").val(c.type);
        if ($.fn.select2) $("#companyType").trigger("change.select2");
      }

      if (typeof c.status !== "undefined") {
        $("#companyStatus").val(String(c.status));
        if ($.fn.select2) $("#companyStatus").trigger("change.select2");
      }

      // Address Fields
      if (c.address) {
        $("#companyAddress").val(c.address.street || "");
        $("#companyPinCode").val(c.address.pincode || "");

        if (c.address.state_id) {
          $("#companyState").val(c.address.state_id);
          if ($.fn.select2) $("#companyState").trigger("change.select2");

          // Load Cities and set selected city
          self.loadCities(c.address.state_id, c.address.city_id);
        }
      }

      // Logo
      if (c.logo_url) {
        self.existingLogoUrl = c.logo_url;
        $("#logoPreviewImg").attr("src", c.logo_url);
        $("#logoUploadPrompt").hide();
        $("#logoPreviewWrap").show();
        $("#previewCompanyLogo").attr("src", c.logo_url).show();
        $("#previewCompanyInitial").hide();
      }

      self.updateLivePreview();
    },

    loadCities: function (stateId, selectedCityId) {
      const self = this;
      const $city = $("#companyCity");

      if (!stateId) {
        $city.empty().append('<option value="">Select City</option>');
        if ($.fn.select2) $city.trigger("change.select2");
        self.updateLivePreview();
        return;
      }

      $.ajax({
        url: self.citiesApi,
        type: "GET",
        data: { state_id: stateId },
        dataType: "json",
        success: function (res) {
          $city.empty().append('<option value="">Select City</option>');
          if (res && res.status === "success" && Array.isArray(res.cities)) {
            res.cities.forEach(function (c) {
              $city.append(new Option(c.name, c.id));
            });
          }
          if (selectedCityId) {
            $city.val(selectedCityId);
          }
          if ($.fn.select2) {
            $city.trigger("change.select2");
          }
          self.updateLivePreview();
        }
      });
    },

    bindEvents: function () {
      const self = this;
      $(document).off(".companyUpdate");

      // Real-time Input Syncing for Live Preview
      $(document).on("input.companyUpdate change.companyUpdate", "#companyName, #companyEmail, #companyMobile, #companyGst, #companyPan, #companyAddress, #companyPinCode, #companyType", function () {
        self.clearFieldError($(this).attr("id"));
        self.updateLivePreview();
      });

      // State Change -> Load Cities
      $(document).on("change.companyUpdate", "#companyState", function () {
        const stateId = $(this).val();
        self.clearFieldError("companyState");
        self.loadCities(stateId);
      });

      // City Change
      $(document).on("change.companyUpdate", "#companyCity", function () {
        self.clearFieldError("companyCity");
        self.updateLivePreview();
      });

      // Logo File Input / Drop Zone
      $(document).on("click.companyUpdate", "#logoDropZone, #btnBrowseLogo", function (e) {
        if (e.target.id !== "companyLogoInput") {
          $("#companyLogoInput").trigger("click");
        }
      });

      $(document).on("change.companyUpdate", "#companyLogoInput", function () {
        const file = this.files && this.files[0];
        if (file) {
          self.handleLogoSelected(file);
        }
      });

      // Drag and drop events
      const $dropZone = $("#logoDropZone");
      if ($dropZone.length) {
        $dropZone.off(".companyUpdateDrag").on("dragover.companyUpdateDrag dragenter.companyUpdateDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass("drag-active");
        });

        $dropZone.on("dragleave.companyUpdateDrag drop.companyUpdateDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass("drag-active");
        });

        $dropZone.on("drop.companyUpdateDrag", function (e) {
          const dt = e.originalEvent.dataTransfer;
          if (dt && dt.files && dt.files.length) {
            self.handleLogoSelected(dt.files[0]);
          }
        });
      }

      // Remove Logo
      $(document).on("click.companyUpdate", "#btnRemoveLogo", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.selectedLogoFile = null;
        self.existingLogoUrl = "";
        $("#companyLogoInput").val("");
        $("#logoUploadPrompt").show();
        $("#logoPreviewWrap").hide();
        $("#previewCompanyLogo").hide();
        $("#previewCompanyInitial").show();
        self.updateLivePreview();
      });

      // Form Submit
      $(document).on("submit.companyUpdate", "#updateCompanyForm", function (e) {
        e.preventDefault();
        self.submitForm();
      });
    },

    handleLogoSelected: function (file) {
      const self = this;
      if (!file.type.match("image.*")) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", "Please select a valid image file (PNG, JPG, WEBP, SVG).", "warning");
        } else {
          alert("Please select a valid image file (PNG, JPG, WEBP, SVG).");
        }
        return;
      }

      self.selectedLogoFile = file;
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#logoPreviewImg").attr("src", e.target.result);
        $("#logoUploadPrompt").hide();
        $("#logoPreviewWrap").show();
        $("#previewCompanyLogo").attr("src", e.target.result).show();
        $("#previewCompanyInitial").hide();
      };
      reader.readAsDataURL(file);
    },

    updateLivePreview: function () {
      const name = $("#companyName").val().trim() || "Company Legal Name";
      const email = $("#companyEmail").val().trim() || "contact@company.com";
      const mobile = $("#companyMobile").val().trim() || "+91 00000 00000";
      const gst = $("#companyGst").val().trim();
      const pan = $("#companyPan").val().trim();
      const typeText = $("#companyType option:selected").text() || "Private Limited Company";
      const address = $("#companyAddress").val().trim();
      const stateName = $("#companyState option:selected").val() ? $("#companyState option:selected").text() : "";
      const cityName = $("#companyCity option:selected").val() ? $("#companyCity option:selected").text() : "";
      const pincode = $("#companyPinCode").val().trim();

      // Titles & Badges
      $("#previewCompanyName").text(name);
      $("#previewCompanyType").text(typeText);

      // Initials Avatar (if no custom logo)
      if (!this.selectedLogoFile && !this.existingLogoUrl) {
        const initials = name
          .split(" ")
          .map((n) => n[0])
          .slice(0, 2)
          .join("")
          .toUpperCase() || "CO";
        $("#previewCompanyInitial").text(initials).show();
        $("#previewCompanyLogo").hide();
      }

      // Contact Details
      $("#previewCompanyEmail").text(email);
      $("#previewCompanyMobile").text(mobile);

      // Tax Badges
      if (gst) {
        $("#previewGstPill").html(`<i class="fi fi-rr-receipt"></i> GST: ${gst}`).show();
      } else {
        $("#previewGstPill").hide();
      }

      if (pan) {
        $("#previewPanPill").html(`<i class="fi fi-rr-document"></i> PAN: ${pan}`).show();
      } else {
        $("#previewPanPill").hide();
      }

      // Address string
      const addrParts = [address, cityName, stateName, pincode].filter(Boolean);
      const fullAddr = addrParts.length ? addrParts.join(", ") : "Address will appear here...";
      $("#previewCompanyAddress").text(fullAddr);
    },

    validate: function () {
      let isValid = true;

      const name = $("#companyName").val().trim();
      const email = $("#companyEmail").val().trim();
      const mobile = $("#companyMobile").val().trim();
      const state = $("#companyState").val();
      const city = $("#companyCity").val();
      const address = $("#companyAddress").val().trim();
      const pincode = $("#companyPinCode").val().trim();

      if (!name) {
        this.showFieldError("companyName", "Company legal name is required.");
        isValid = false;
      }

      if (!email) {
        this.showFieldError("companyEmail", "Official email address is required.");
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        this.showFieldError("companyEmail", "Please enter a valid email address.");
        isValid = false;
      }

      if (!mobile) {
        this.showFieldError("companyMobile", "Official contact mobile number is required.");
        isValid = false;
      }

      if (!address) {
        this.showFieldError("companyAddress", "Street address is required.");
        isValid = false;
      }

      if (!state) {
        this.showFieldError("companyState", "Please select a state.");
        isValid = false;
      }

      if (!city) {
        this.showFieldError("companyCity", "Please select a city.");
        isValid = false;
      }

      if (!pincode) {
        this.showFieldError("companyPinCode", "Please enter the area pincode.");
        isValid = false;
      }

      return isValid;
    },

    submitForm: function () {
      const self = this;
      if (!self.validate()) return;

      const $btn = $("#btnSubmitCompany");
      const originalText = $btn.html();

      $btn.prop("disabled", true).html('<span class="v3-btn-spinner"></span> Saving Changes...');

      const formElement = document.getElementById("updateCompanyForm");
      const formData = new FormData(formElement);

      formData.set("id", self.companyId);
      formData.set("name", $("#companyName").val().trim());
      formData.set("companyType", $("#companyType").val());
      formData.set("company_type", $("#companyType").val());
      formData.set("companyCode", $("#companyCode").val().trim());
      formData.set("description", $("#companyDescription").val().trim());
      formData.set("email", $("#companyEmail").val().trim());
      formData.set("mobile", $("#companyMobile").val().trim());
      formData.set("gst", $("#companyGst").val().trim());
      formData.set("pan", $("#companyPan").val().trim());
      formData.set("cin", $("#companyCin").val().trim());
      formData.set("status", $("#companyStatus").val());
      formData.set("address", $("#companyAddress").val().trim());
      formData.set("state", $("#companyState").val());
      formData.set("city", $("#companyCity").val());
      formData.set("pinCode", $("#companyPinCode").val().trim());

      if (self.selectedLogoFile) {
        formData.set("featureImage", self.selectedLogoFile);
      }

      $.ajax({
        url: self.updateApi,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (res) {
          $btn.prop("disabled", false).html(originalText);

          if (res && res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg || "Company details updated successfully!", "success");
              setTimeout(function () {
                window.location.href = "/dashboard/company/list/";
              }, 1500);
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Updated!",
                text: res.msg || "Company details updated successfully.",
                icon: "success",
                type: "success",
                timer: 1600,
                showConfirmButton: false
              }).then(function () {
                window.location.href = "/dashboard/company/list/";
              });
            } else {
              alert(res.msg || "Company details updated successfully.");
              window.location.href = "/dashboard/company/list/";
            }
          } else {
            const msg = (res && res.msg) || "Could not update company.";
            if (res && res.field) {
              self.showFieldError(res.field, msg);
            }
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Validation Error",
                text: msg,
                icon: "error",
                type: "error"
              });
            } else {
              alert(msg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html(originalText);

          let msg = "Network or server error while updating company.";
          try {
            const errRes = JSON.parse(xhr.responseText);
            if (errRes && errRes.msg) msg = errRes.msg;
          } catch (e) {}

          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, "error");
          } else if (window.Swal && typeof window.Swal.fire === "function") {
            window.Swal.fire({
              title: "Error",
              text: msg,
              icon: "error",
              type: "error"
            });
          } else {
            alert(msg);
          }
        }
      });
    },

    showFieldError: function (fieldId, msg) {
      const $field = $("#" + fieldId);
      $field.addClass("is-invalid");
      const $err = $("#err_" + fieldId);
      if ($err.length) {
        $err.text(msg).show();
      }
    },

    clearFieldError: function (fieldId) {
      $("#" + fieldId).removeClass("is-invalid");
      $("#err_" + fieldId).text("").hide();
    }
  };

  $(document).ready(function () {
    V3CompanyUpdate.init();
  });

  $(document).on("spa:loaded", function () {
    V3CompanyUpdate.init();
  });

  window.V3CompanyUpdate = V3CompanyUpdate;
})(window, jQuery);
