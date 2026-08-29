/**
 * V3 Add Company Controller Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/company-add.js
 */

(function (window, $) {
  "use strict";

  const V3CompanyAdd = {
    statesApi: "/api/v3/geo/states/",
    citiesApi: "/api/v3/geo/cities/",
    createApi: "/api/v3/company/create/",
    selectedLogoFile: null,

    init: function () {
      const $root = $("#addCompanyApp");
      if (!$root.length) return;

      this.selectedLogoFile = null;
      this.initSelect2();
      this.loadStates();
      this.bindEvents();
      this.updateLivePreview();
    },

    initSelect2: function () {
      if ($.fn.select2) {
        $("#companyType").select2({
          minimumResultsForSearch: Infinity,
          width: "100%"
        });

        $("#companyState, #companyCity").select2({
          width: "100%",
          placeholder: "Select from list"
        });
      }
    },

    loadStates: function () {
      const self = this;
      const $state = $("#companyState");

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
        }
      });
    },

    loadCities: function (stateId) {
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
          if ($.fn.select2) {
            $city.trigger("change.select2");
          }
          self.updateLivePreview();
        }
      });
    },

    bindEvents: function () {
      const self = this;
      $(document).off(".companyAdd");

      // Real-time Input Syncing for Live Preview
      $(document).on("input.companyAdd change.companyAdd", "#companyName, #companyEmail, #companyMobile, #companyGst, #companyPan, #companyAddress, #companyPinCode, #companyType", function () {
        self.clearFieldError($(this).attr("id"));
        self.updateLivePreview();
      });

      // State Change -> Load Cities
      $(document).on("change.companyAdd", "#companyState", function () {
        const stateId = $(this).val();
        self.clearFieldError("companyState");
        self.loadCities(stateId);
      });

      // City Change
      $(document).on("change.companyAdd", "#companyCity", function () {
        self.clearFieldError("companyCity");
        self.updateLivePreview();
      });

      // Logo File Input / Drop Zone
      $(document).on("click.companyAdd", "#logoDropZone, #btnBrowseLogo", function (e) {
        if (e.target.id !== "companyLogoInput") {
          $("#companyLogoInput").trigger("click");
        }
      });

      $(document).on("change.companyAdd", "#companyLogoInput", function () {
        const file = this.files && this.files[0];
        if (file) {
          self.handleLogoSelected(file);
        }
      });

      // Drag and drop events
      const $dropZone = $("#logoDropZone");
      if ($dropZone.length) {
        $dropZone.off(".companyAddDrag").on("dragover.companyAddDrag dragenter.companyAddDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass("drag-active");
        });

        $dropZone.on("dragleave.companyAddDrag drop.companyAddDrag", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass("drag-active");
        });

        $dropZone.on("drop.companyAddDrag", function (e) {
          const dt = e.originalEvent.dataTransfer;
          if (dt && dt.files && dt.files.length) {
            self.handleLogoSelected(dt.files[0]);
          }
        });
      }

      // Remove Logo
      $(document).on("click.companyAdd", "#btnRemoveLogo", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.selectedLogoFile = null;
        $("#companyLogoInput").val("");
        $("#logoUploadPrompt").show();
        $("#logoPreviewWrap").hide();
        self.updateLivePreview();
      });

      // Form Submit
      $(document).on("submit.companyAdd", "#addCompanyForm", function (e) {
        e.preventDefault();
        self.submitForm();
      });
    },

    handleLogoSelected: function (file) {
      const self = this;
      if (!file.type.match("image.*")) {
        alert("Please select a valid image file (PNG, JPG, WEBP, SVG).");
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
      const cityName = $("#companyCity option:selected").val() ? $("#companyCity option:selected").text() : "";
      const stateName = $("#companyState option:selected").val() ? $("#companyState option:selected").text() : "";
      const pincode = $("#companyPinCode").val().trim();

      // Titles & Badges
      $("#previewCompanyName").text(name);
      $("#previewCompanyType").text(typeText);

      // Initials Avatar (if no custom logo)
      if (!this.selectedLogoFile) {
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
      const fullAddr = addrParts.length ? addrParts.join(", ") : "Address will appear here as you type...";
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
        this.showFieldError("companyName", "Please enter the company legal name.");
        isValid = false;
      }

      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        this.showFieldError("companyEmail", "Please enter a valid official email address.");
        isValid = false;
      }

      if (!mobile) {
        this.showFieldError("companyMobile", "Please enter official contact mobile number.");
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

      if (!address) {
        this.showFieldError("companyAddress", "Please enter the street address.");
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

      $btn.prop("disabled", true).html('<span class="v3-btn-spinner"></span> Saving...');

      const formElement = document.getElementById("addCompanyForm");
      const formData = new FormData(formElement);

      // Explicitly ensure all required fields are set in FormData
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
      formData.set("address", $("#companyAddress").val().trim());
      formData.set("state", $("#companyState").val());
      formData.set("city", $("#companyCity").val());
      formData.set("pinCode", $("#companyPinCode").val().trim());

      if (self.selectedLogoFile) {
        formData.set("featureImage", self.selectedLogoFile);
      }

      $.ajax({
        url: self.createApi,
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (res) {
          $btn.prop("disabled", false).html(originalText);

          if (res && res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg || "Company registered successfully!", "success");
              setTimeout(function () {
                window.location.href = "/dashboard/company/list/";
              }, 1500);
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Registered!",
                text: res.msg || "Company registered successfully.",
                icon: "success",
                type: "success",
                timer: 1600,
                showConfirmButton: false
              }).then(function () {
                window.location.href = "/dashboard/company/list/";
              });
            } else {
              alert(res.msg || "Company registered successfully.");
              window.location.href = "/dashboard/company/list/";
            }
          } else {
            const msg = (res && res.msg) || "Could not register company.";
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

          let msg = "Network or server error while registering company.";
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
    V3CompanyAdd.init();
  });

  $(document).on("spa:loaded", function () {
    V3CompanyAdd.init();
  });

  window.V3CompanyAdd = V3CompanyAdd;
})(window, jQuery);
