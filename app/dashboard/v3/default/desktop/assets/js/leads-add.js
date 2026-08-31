/**
 * V3 Lead Creation Module
 * File: app/dashboard/v3/default/desktop/assets/js/leads-add.js
 * SPA Compliant, REST API communication with /api/v3/leads/create/ and /api/v3/cities/
 */
(function (factory) {
  if (typeof window.jQuery !== "undefined") {
    factory(window.jQuery);
  } else {
    document.addEventListener("DOMContentLoaded", function () {
      if (typeof window.jQuery !== "undefined") {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  "use strict";

  window.V3LeadAdd = {
    isSubmitting: false,
    eventsBound: false,

    init: function () {
      const $app = $("#leadAddApp");
      if (!$app.length) return;

      this.initPlugins();
      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }
    },

    initPlugins: function () {
      const self = this;

      // 1. Regular Select2 Dropdowns (Source, Stage, Agent)
      if (typeof $.fn.select2 !== "undefined") {
        $(".v3-lead-select").each(function () {
          if (!$(this).data("select2")) {
            $(this).select2({
              dropdownAutoWidth: true,
              width: "100%",
            });
          }
        });

        // 2. Enhanced Tour Package / Destination Dropdown (with rich card template & tags)
        $(".v3-package-select").each(function () {
          if (!$(this).data("select2")) {
            const placeholder = $(this).data("placeholder") || "Choose a tour package or type custom destination...";
            $(this).select2({
              placeholder: placeholder,
              allowClear: true,
              tags: true,
              width: "100%",
              templateResult: function (data) {
                if (!data.id) return data.text;
                const $el = $(data.element);
                if (!$el.length || !$el.data("type")) {
                  // Custom typed tag or simple option
                  return $(`
                    <div class="v3-pkg-option-item custom">
                      <div class="v3-pkg-option-icon custom"><i class="fi fi-rr-search-location"></i></div>
                      <div class="v3-pkg-option-details">
                        <div class="v3-pkg-option-title">${data.text}</div>
                        <span class="v3-pkg-badge custom">Custom Destination</span>
                      </div>
                    </div>
                  `);
                }

                const duration = $el.data("duration");
                const days = $el.data("days") || (parseInt(duration) + 1);
                const city = $el.data("city") || "";
                const type = $el.data("type") || "domestic";
                const theme = $el.data("theme") || "";
                const isIntl = type.toLowerCase() === "international";

                return $(`
                  <div class="v3-pkg-option-item">
                    <div class="v3-pkg-option-icon ${isIntl ? 'intl' : 'dom'}">
                      <i class="fi ${isIntl ? 'fi-rr-plane' : 'fi-rr-route'}"></i>
                    </div>
                    <div class="v3-pkg-option-details">
                      <div class="v3-pkg-option-title">${data.text}</div>
                      <div class="v3-pkg-option-badges">
                        ${duration ? `<span class="v3-pkg-badge duration"><i class="fi fi-rr-moon"></i> ${duration}N / ${days}D</span>` : ''}
                        ${city ? `<span class="v3-pkg-badge destination"><i class="fi fi-rr-marker"></i> ${city}</span>` : ''}
                        ${theme ? `<span class="v3-pkg-badge theme"><i class="fi fi-rr-star"></i> ${theme}</span>` : ''}
                        <span class="v3-pkg-badge type ${isIntl ? 'intl' : 'dom'}">${isIntl ? 'International' : 'Domestic'}</span>
                      </div>
                    </div>
                  </div>
                `);
              },
              templateSelection: function (data) {
                return data.text || data.id;
              }
            });
          }
        });

        // 3. Dynamic City Dropdowns via /api/v3/cities/
        $(".v3-city-select").each(function () {
          if (!$(this).data("select2")) {
            const placeholder = $(this).data("placeholder") || "Search or enter city / location...";
            $(this).select2({
              placeholder: placeholder,
              allowClear: true,
              tags: true,
              width: "100%",
              ajax: {
                url: "/api/v3/cities/",
                dataType: "json",
                delay: 250,
                data: function (params) {
                  return {
                    search: params.term || "",
                    page: params.page || 1,
                  };
                },
                processResults: function (data) {
                  if (data && Array.isArray(data.results)) {
                    return { results: data.results };
                  }
                  if (data && Array.isArray(data.cities)) {
                    const mapped = data.cities.map(function (c) {
                      return { id: c, text: c };
                    });
                    return { results: mapped };
                  }
                  return { results: [] };
                },
                cache: true,
              },
            });
          }
        });
      }

      // 4. Future Travel Datepicker
      const $dateInput = $("#leadTravelDate");
      if ($dateInput.length && typeof $.fn.daterangepicker !== "undefined") {
        $dateInput.daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          autoUpdateInput: false,
          minDate: moment(),
          locale: {
            format: "YYYY-MM-DD",
            cancelLabel: "Clear",
          },
        });

        $dateInput.on("apply.daterangepicker", function (ev, picker) {
          $(this).val(picker.startDate.format("YYYY-MM-DD"));
        });

        $dateInput.on("cancel.daterangepicker", function () {
          $(this).val("");
        });
      }
    },

    bindEvents: function () {
      const self = this;

      // Package selection change: update preview card and auto-fill logistics
      $(document).on("change", "#leadPackage", function () {
        const val = $(this).val();
        const $selected = $(this).find("option:selected");
        const $card = $("#leadSelectedPkgCard");

        if (!val) {
          $card.slideUp(150);
          return;
        }

        const duration = parseInt($selected.data("duration"));
        const days = $selected.data("days") || (duration ? duration + 1 : 0);
        const city = $selected.data("city") || "";
        const type = $selected.data("type") || "domestic";
        const theme = $selected.data("theme") || "Custom Itinerary";
        const isIntl = String(type).toLowerCase() === "international";

        // Update Snapshot Card
        $("#cardPkgTitle").text(val);
        $("#cardPkgTypeBadge").text(isIntl ? "International" : "Domestic Tour")
          .toggleClass("intl", isIntl)
          .toggleClass("dom", !isIntl);

        if (duration > 0) {
          $("#cardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${duration}N / ${days}D</strong>`).show();
          // Auto-fill duration input
          $("#leadNumberOfDays").val(duration);
        } else {
          const currentNights = parseInt($("#leadNumberOfDays").val(), 10);
          if (!isNaN(currentNights) && currentNights > 0) {
            $("#cardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${currentNights}N / ${currentNights + 1}D</strong>`).show();
          } else {
            $("#cardPkgDuration").hide();
          }
        }

        if (city) {
          $("#cardPkgLocation").html(`<i class="fi fi-rr-marker"></i> ${city}`).show();
        } else {
          $("#cardPkgLocation").hide();
        }

        if (theme) {
          $("#cardPkgTheme").html(`<i class="fi fi-rr-star"></i> ${theme}`).show();
        } else {
          $("#cardPkgTheme").hide();
        }

        $card.slideDown(200);
      });

      // Duration input change: dynamically update selected package preview badge (Nights / Days)
      $(document).on("input change keyup", "#leadNumberOfDays", function () {
        const nights = parseInt($(this).val(), 10);
        if (!isNaN(nights) && nights > 0) {
          const days = nights + 1;
          $("#cardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${nights}N / ${days}D</strong>`).show();
        } else {
          $("#cardPkgDuration").hide();
        }
      });

      // Clear package selection from preview card
      $(document).on("click", "#btnClearPkgCard", function (e) {
        e.preventDefault();
        $("#leadPackage").val("").trigger("change");
      });

      // Auto-default drop location to chosen pickup location
      $(document).on("change select2:select", "#leadPickupLocation", function () {
        const pickupVal = $(this).val();
        if (pickupVal) {
          const currentDrop = $("#leadDropLocation").val();
          if (!currentDrop || currentDrop === pickupVal || !$("#leadDropLocation").data("userModified")) {
            $("#leadDropLocation")
              .empty()
              .append(new Option(pickupVal, pickupVal, true, true))
              .trigger("change");
          }
        }
      });

      $(document).on("select2:select change", "#leadDropLocation", function () {
        const dropVal = $(this).val();
        const pickupVal = $("#leadPickupLocation").val();
        if (dropVal && dropVal !== pickupVal) {
          $("#leadDropLocation").data("userModified", true);
        }
      });

      // Form submission
      $(document).on("submit", "#leadAddForm", function (e) {
        e.preventDefault();
        self.submitLeadForm($(this));
      });

      // Quick passenger count stepper buttons
      $(document).on("click", ".v3-counter-btn", function () {
        const targetId = $(this).data("target");
        const isIncrement = $(this).hasClass("btn-inc");
        const $input = $("#" + targetId);
        if ($input.length) {
          let val = parseInt($input.val()) || 0;
          const min = parseInt($input.attr("min")) || 0;
          const max = parseInt($input.attr("max")) || 99;
          if (isIncrement && val < max) {
            val++;
          } else if (!isIncrement && val > min) {
            val--;
          }
          $input.val(val).trigger("change");
        }
      });
    },

    submitLeadForm: function ($form) {
      const self = this;
      if (self.isSubmitting) return;

      const firstName = $.trim($("#leadFirstName").val());
      const mobile = $.trim($("#leadMobile").val());
      const pkg = $.trim($("#leadPackage").val());

      if (!firstName) {
        self.showAlert("Please enter customer's first name.", "warning");
        $("#leadFirstName").focus();
        return;
      }

      if (!mobile) {
        self.showAlert("Please enter customer's mobile number.", "warning");
        $("#leadMobile").focus();
        return;
      }

      if (!pkg) {
        self.showAlert("Please select or enter the tour package / destination.", "warning");
        $("#leadPackage").select2("open");
        return;
      }

      const $submitBtn = $("#btnCreateLeadSubmit");
      const originalText = $submitBtn.html();

      self.isSubmitting = true;
      $submitBtn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Saving Lead...');

      const formData = $form.serialize();

      $.ajax({
        url: "/api/v3/leads/create/",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (res) {
          self.isSubmitting = false;
          $submitBtn.prop("disabled", false).html(originalText);

          if (res.status === 200 || res.code === "success" || res.status === "success") {
            const rawLeadId = res.lead_id || res.leadId || (res.data && (res.data.lead_id || res.data.id));
            let encodedLeadId = "";
            if (rawLeadId) {
              try {
                encodedLeadId = btoa(String(rawLeadId));
              } catch (e) {
                encodedLeadId = String(rawLeadId);
              }
            }

            const targetDetailsUrl = encodedLeadId ? ("/dashboard/leads/view/?id=" + encodeURIComponent(encodedLeadId)) : "/dashboard/leads/list/";
            const targetListUrl = "/dashboard/leads/list/";

            let navigated = false;
            function handleNavigate(url) {
              if (navigated) return;
              navigated = true;
              if (typeof window.spaNavigate === "function") {
                window.spaNavigate(url);
              } else {
                window.location.href = url;
              }
            }

            if (typeof window.swal === "function" || typeof swal !== "undefined") {
              const swalFn = window.swal || swal;
              const swalRes = swalFn({
                title: "Lead Created!",
                text: res.message || "New customer lead has been recorded in the pipeline.",
                type: "success",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#0284c7",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Open Lead Details",
                cancelButtonText: "Back to Leads List",
                closeOnConfirm: true,
                closeOnCancel: true,
              }, function (isConfirm) {
                if (isConfirm) {
                  handleNavigate(targetDetailsUrl);
                } else {
                  handleNavigate(targetListUrl);
                }
              });

              if (swalRes && typeof swalRes.then === "function") {
                swalRes.then(function (result) {
                  const isConfirm = (result === true || (result && (result.value || result.isConfirmed)));
                  if (isConfirm) {
                    handleNavigate(targetDetailsUrl);
                  } else {
                    handleNavigate(targetListUrl);
                  }
                });
              }
            } else {
              handleNavigate(targetListUrl);
            }
          } else {
            self.showAlert(res.message || "Failed to create lead. Please check details.", "error");
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $submitBtn.prop("disabled", false).html(originalText);
          let errorMsg = "Something went wrong while saving the lead.";
          try {
            const parsed = JSON.parse(xhr.responseText);
            if (parsed && parsed.message) errorMsg = parsed.message;
          } catch (e) {}
          self.showAlert(errorMsg, "error");
        },
      });
    },

    showAlert: function (msg, type = "warning") {
      if (typeof popUpMessage === "function") {
        popUpMessage("center", msg, type);
      } else if (typeof swal !== "undefined") {
        swal({
          title: type === "error" ? "Error" : "Attention",
          text: msg,
          type: type,
          confirmButtonColor: "#0284c7",
        });
      } else {
        alert(msg);
      }
    },
  };

  // Auto initialize
  $(document).ready(function () {
    window.V3LeadAdd.init();
  });
});
