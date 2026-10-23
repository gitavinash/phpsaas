/**
 * V3 Domestic Tour Package Add Module
 * Path: app/dashboard/v3/default/desktop/assets/js/package-domestic-add.js
 * Handles multi-destination planning, city auto-complete, vehicle API fetching,
 * live preview, in-page Smart AI itinerary generation & preview, and package publishing.
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

  window.V3PackageDomesticAdd = {
    isInitialized: false,
    eventsBound: false,
    vehiclesLoaded: false,
    vehicleData: [],
    loadingInterval: null,
    currentStepData: null,

    init: function () {
      const $app = $("#packageDomesticAddApp, #packageInternationalAddApp, .package-domestic-add-wrapper, .package-international-add-wrapper");
      if (!$app.length) {
        this.isInitialized = false;
        return;
      }

      this.isInitialized = true;
      this.bindEvents();
      this.initCitySelect2();
      this.fetchVehicles();
      this.updateTotalDuration();
      this.updateLivePreview();
    },

    autoInit: function () {
      this.init();
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      const self = this;

      // Add Destination Row
      $(document).on("click", "#btnAddDestination", function (e) {
        e.preventDefault();
        self.addDestinationRow();
      });

      // Remove Destination Row
      $(document).on("click", ".btn-remove-dest-row", function (e) {
        e.preventDefault();
        const $row = $(this).closest(".v3-destination-row");
        self.removeDestinationRow($row);
      });

      // Duration input change
      $(document).on("input change", "input[name='duration[]']", function () {
        let val = parseInt($(this).val(), 10);
        if (isNaN(val) || val < 1) {
          val = 1;
          $(this).val(1);
        }
        self.updateTotalDuration();
        self.updateLivePreview();
      });

      // Duration plus/minus step buttons
      $(document).on("click", ".v3-duration-btn-plus", function (e) {
        e.preventDefault();
        const $input = $(this).siblings("input[name='duration[]']");
        let val = parseInt($input.val(), 10) || 1;
        $input.val(val + 1).trigger("change");
      });

      $(document).on("click", ".v3-duration-btn-minus", function (e) {
        e.preventDefault();
        const $input = $(this).siblings("input[name='duration[]']");
        let val = parseInt($input.val(), 10) || 1;
        if (val > 1) {
          $input.val(val - 1).trigger("change");
        }
      });

      // Live inputs update preview
      $(document).on("input change", "#packageName, #travelStyle, #outstationVehicle, #localSighseeignVehicle", function () {
        self.updateLivePreview();
      });

      // Starting city change
      $(document).on("change select2:select select2:clear", "#startingCity", function () {
        const isSame = $("#sameAsPickupToggle").is(":checked");
        if (isSame) {
          self.syncDropOffWithPickup();
        }
        self.updateTotalDuration();
        self.updateLivePreview();
        self.syncDaySchedulePlanner();
      });

      // Drop-off city change
      $(document).on("change select2:select select2:clear", "#endCity", function () {
        self.updateTotalDuration();
        self.updateLivePreview();
        self.syncDaySchedulePlanner();
      });

      // Destination city select change
      $(document).on("change select2:select select2:clear", ".v3-destination-row .v3-city-select", function () {
        self.updateTotalDuration();
        self.updateLivePreview();
        self.syncDaySchedulePlanner();
      });

      // Same as pickup toggle
      $(document).on("change", "#sameAsPickupToggle", function () {
        const isChecked = $(this).is(":checked");
        const $endCityWrap = $("#endCityWrap");
        if (isChecked) {
          $endCityWrap.addClass("is-synced-pickup");
          self.syncDropOffWithPickup();
        } else {
          $endCityWrap.removeClass("is-synced-pickup");
        }
        self.updateTotalDuration();
        self.updateLivePreview();
        self.syncDaySchedulePlanner();
      });

      // Form Reset
      $(document).on("click", "#btnResetPackageForm", function (e) {
        e.preventDefault();
        if (typeof swal !== "undefined") {
          swal({
            title: "Reset Form?",
            text: "Are you sure you want to reset all fields in this form?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Yes, Reset",
            closeOnConfirm: true
          }, function (isConfirm) {
            if (isConfirm) {
              self.resetForm();
            }
          });
        } else {
          if (confirm("Are you sure you want to reset the form?")) {
            self.resetForm();
          }
        }
      });

      // Step 1: Submit / Generate Itinerary button -> In-Page Step 2 AI Preview
      $(document).on("click", "#addDomesticPackageBtn, #btnGeneratePackage", function (e) {
        e.preventDefault();
        self.handleGeneratePreview();
      });

      // Step 1: Intercept native form submit to trigger preview without page reload
      $(document).on("submit", "#addDomesticPackageForm, #addInternationalPackageForm", function (e) {
        e.preventDefault();
        self.handleGeneratePreview();
      });

      // Step 2 Navigation: Back to Route Setup
      $(document).on("click", "#btnTopBackToStep1, #btnBottomBackToStep1, #btnErrorBackToStep1", function (e) {
        e.preventDefault();
        self.goToStep(1);
      });

      // Step 2 Action: Regenerate AI Itinerary
      $(document).on("click", "#btnRegenerateItinerary, #btnErrorRetry", function (e) {
        e.preventDefault();
        self.generateAIItinerary();
      });

      // Auto-suggest package title button
      $(document).on("click", "#btnSuggestTitle", function (e) {
        e.preventDefault();
        self.suggestPackageTitle();
      });

      // Travel style pills click
      $(document).on("click", ".v3-style-pill", function (e) {
        e.preventDefault();
        const style = $(this).data("style") || $(this).attr("data-style");
        if (!style) return;
        $(".v3-style-pill").removeClass("active");
        $(this).addClass("active");
        $("#travelStyle").val(style).trigger("change");
      });

      // Travel style dropdown sync with pills
      $(document).on("change", "#travelStyle", function () {
        const style = $(this).val();
        $(".v3-style-pill").removeClass("active");
        $(`.v3-style-pill[data-style='${style}']`).addClass("active");
      });

      // Sticky sidebar generate button proxy
      $(document).on("click", "#btnSidebarGenerate", function (e) {
        e.preventDefault();
        $("#btnGeneratePackage").trigger("click");
      });

      // Step 1: Meals Preset Quick Buttons
      $(document).on("click", ".v3-meal-preset-btn", function (e) {
        e.preventDefault();
        const preset = $(this).data("preset");
        $(".v3-meal-preset-btn").removeClass("active");
        $(this).addClass("active");

        const $checks = $("input[name='meals[]']");
        $checks.prop("checked", false);

        if (preset === "cp") {
          $checks.filter("[value='Breakfast']").prop("checked", true);
        } else if (preset === "map") {
          $checks.filter("[value='Breakfast'], [value='Dinner']").prop("checked", true);
        } else if (preset === "ap") {
          $checks.prop("checked", true);
        }

        $(".v3-meal-card").each(function () {
          const isChk = $(this).find(".v3-meal-checkbox").is(":checked");
          $(this).toggleClass("is-selected", isChk);
        });

        self.updateLivePreview();
      });

      // Step 1: Individual Meal Card Toggle
      $(document).on("change", ".v3-meal-checkbox", function () {
        const $card = $(this).closest(".v3-meal-card");
        $card.toggleClass("is-selected", $(this).is(":checked"));

        const checkedVals = [];
        $("input[name='meals[]']:checked").each(function () {
          checkedVals.push($(this).val());
        });

        $(".v3-meal-preset-btn").removeClass("active");
        if (checkedVals.length === 0) {
          $(".v3-meal-preset-btn[data-preset='none']").addClass("active");
        } else if (checkedVals.length === 1 && checkedVals[0] === "Breakfast") {
          $(".v3-meal-preset-btn[data-preset='cp']").addClass("active");
        } else if (checkedVals.length === 2 && checkedVals.includes("Breakfast") && checkedVals.includes("Dinner")) {
          $(".v3-meal-preset-btn[data-preset='map']").addClass("active");
        } else if (checkedVals.length === 4) {
          $(".v3-meal-preset-btn[data-preset='ap']").addClass("active");
        }

        self.updateLivePreview();
      });

      // Step 2 Action: Toggle Collapse All Days
      $(document).on("click", "#btnToggleAllDaysCollapse", function (e) {
        e.preventDefault();
        const $cards = $("#itineraryDaysCardsContainer .v3-day-card");
        const areAllCollapsed = $cards.length && $cards.filter(".is-collapsed").length === $cards.length;
        if (areAllCollapsed) {
          $cards.removeClass("is-collapsed");
          $(this).html('<i class="fi fi-rr-compress-alt"></i> <span>Collapse All</span>');
        } else {
          $cards.addClass("is-collapsed");
          $(this).html('<i class="fi fi-rr-expand-alt"></i> <span>Expand All</span>');
        }
      });

      // Step 2 Action: Accordion collapse on card header click
      $(document).on("click", ".v3-day-card-header", function (e) {
        if ($(e.target).closest("input, button, select, a, textarea").length) return;
        const $card = $(this).closest(".v3-day-card");
        $card.toggleClass("is-collapsed");
      });

      // Step 2 Action: Quick snippet chip click
      $(document).on("click", ".btn-snippet", function (e) {
        e.preventDefault();
        const text = $(this).data("text") || $(this).attr("data-text");
        if (!text) return;
        const $textarea = $(this).closest(".v3-day-card-body").find(".v3-day-textarea");
        const curVal = $textarea.val().trim();
        if (curVal) {
          $textarea.val(curVal + " " + text);
        } else {
          $textarea.val(text);
        }
        $textarea.trigger("input").focus();
      });


      // Step 2 Action: Add Day to Itinerary Preview
      $(document).on("click", "#btnAddPreviewDay", function (e) {
        e.preventDefault();
        self.addPreviewDay();
      });

      // Step 2 Action: Remove Day from Itinerary Preview
      $(document).on("click", ".btn-remove-day-card", function (e) {
        e.preventDefault();
        const $card = $(this).closest(".v3-day-card");
        self.removePreviewDay($card);
      });

      // Step 1 Day Planner: Stay Type toggle (transit vs in_hotel)
      $(document).on("change", ".v3-day-plan-stay-type-select", function () {
        const $row = $(this).closest(".v3-day-plan-row");
        const $citySelect = $row.find(".v3-day-plan-city-select, .v3-day-plan-city-input");
        const val = $(this).val();

        if (val === "in_hotel") {
          $citySelect.prop("disabled", false).removeClass("is-disabled");
          $citySelect.next(".select2-container").removeClass("select2-container--disabled");
          $citySelect.find("option[value='Transit'], option[value='']").remove();
          let prevCity = $citySelect.attr("data-prev-city") || $citySelect.attr("data-default-city") || "";
          if (prevCity && prevCity !== "Transit" && !$citySelect.find(`option[value="${prevCity}"]`).length) {
            $citySelect.append(new Option(prevCity, prevCity, true, true));
          }
          if (prevCity && prevCity !== "Transit") {
            $citySelect.val(prevCity).trigger("change");
          } else {
            $citySelect.val(null).trigger("change");
          }
          if ($citySelect.data("select2") && typeof $citySelect.data("select2")._syncAttributes === "function") {
            try { $citySelect.data("select2")._syncAttributes(); } catch (e) {}
          }
          $citySelect.focus();
        } else {
          // transit
          const curVal = $citySelect.val();
          if (curVal && curVal !== "Transit") {
            $citySelect.attr("data-prev-city", curVal);
          }
          $citySelect.empty().append('<option value="Transit" disabled selected>— In Transit —</option>');
          $citySelect.val("Transit").prop("disabled", true).addClass("is-disabled");
          $citySelect.next(".select2-container").addClass("select2-container--disabled");
          $citySelect.trigger("change");
          if ($citySelect.data("select2") && typeof $citySelect.data("select2")._syncAttributes === "function") {
            try { $citySelect.data("select2")._syncAttributes(); } catch (e) {}
          }
        }
      });

      // Step 1 Day Planner: City dropdown changes
      $(document).on("change", ".v3-day-plan-city-select", function () {
        const val = $(this).val();
        if (val) {
          $(this).attr("data-prev-city", val);
        }
      });

      // Step 2 Day Cards: Stay Type toggle (transit vs in_hotel)
      $(document).on("change", ".v3-day-card-stay-type-select", function () {
        const $field = $(this).closest(".v3-day-stay-field");
        const $citySelect = $field.find(".v3-day-card-city-select, .v3-day-card-city-input");
        const $hiddenStay = $field.find("input[name='dayStay[]']");
        const val = $(this).val();

        if (val === "in_hotel") {
          $citySelect.prop("disabled", false).removeClass("is-disabled");
          $citySelect.next(".select2-container").removeClass("select2-container--disabled");
          $citySelect.find("option[value='']").remove();
          let prevCity = $citySelect.attr("data-prev-city") || $citySelect.attr("data-default-city") || "";
          if (prevCity && !$citySelect.find(`option[value="${prevCity}"]`).length) {
            $citySelect.append(new Option(prevCity, prevCity, true, true));
          }
          if (prevCity) {
            $citySelect.val(prevCity).trigger("change");
          } else {
            $citySelect.val(null).trigger("change");
          }
          if ($citySelect.data("select2") && typeof $citySelect.data("select2")._syncAttributes === "function") {
            try { $citySelect.data("select2")._syncAttributes(); } catch (e) {}
          }
          $hiddenStay.val(prevCity || "In Hotel");
          $field.closest(".v3-day-card").find(".v3-day-collapsed-pill").html('<i class="fi fi-rr-bed"></i> ' + (prevCity || "In Hotel"));
          $citySelect.focus();
        } else {
          // transit
          const curVal = $citySelect.val();
          if (curVal) {
            $citySelect.attr("data-prev-city", curVal);
          }
          $citySelect.empty().append('<option value="" disabled selected>— In Transit —</option>');
          $citySelect.val("").prop("disabled", true).addClass("is-disabled");
          $citySelect.next(".select2-container").addClass("select2-container--disabled");
          $citySelect.trigger("change");
          if ($citySelect.data("select2") && typeof $citySelect.data("select2")._syncAttributes === "function") {
            try { $citySelect.data("select2")._syncAttributes(); } catch (e) {}
          }
          $hiddenStay.val("Transit");
          $field.closest(".v3-day-card").find(".v3-day-collapsed-pill").html('<i class="fi fi-rr-bed"></i> Transit');
        }

        if ($("#modalPackageTemplatePreview").is(":visible") && self.activePreviewTemplate) {
          self.loadTemplatePreview(self.activePreviewTemplate);
        }
      });

      // Step 2 Day Cards: City dropdown changes
      $(document).on("change", ".v3-day-card-city-select", function () {
        const $field = $(this).closest(".v3-day-stay-field");
        const $hiddenStay = $field.find("input[name='dayStay[]']");
        const val = $(this).val();
        if (val) {
          $(this).attr("data-prev-city", val);
          $hiddenStay.val(val);
        } else {
          $hiddenStay.val("Destination City");
        }

        if ($("#modalPackageTemplatePreview").is(":visible") && self.activePreviewTemplate) {
          self.loadTemplatePreview(self.activePreviewTemplate);
        }
      });
      // Step 2 Action: Select Proposal Template Card
      $(document).on("click", ".v3-template-card", function (e) {
        if ($(e.target).closest(".btn-template-preview").length) {
          return;
        }
        const templateId = $(this).attr("data-template-id") || $(this).data("template-id") || "mit";
        $(".v3-template-card").removeClass("active");
        $(`.v3-template-card[data-template-id="${templateId}"]`).addClass("active");
        $("#tourPackageTemplate").val(templateId);
      });

      // Step 2 Action: Preview Template in Live Modal
      $(document).on("click", ".btn-template-preview", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const templateId = $(this).attr("data-template-id") || $(this).closest(".v3-template-card").attr("data-template-id") || "mit";
        self.openTemplatePreviewModal(templateId);
      });

      // Next / Prev Template Navigation in Preview Modal
      $(document).on("click", "#btnPrevPreviewTemplate", function (e) {
        e.preventDefault();
        self.navigateTemplatePreview(-1);
      });

      $(document).on("click", "#btnNextPreviewTemplate", function (e) {
        e.preventDefault();
        self.navigateTemplatePreview(1);
      });

      // Apply Previewed Template
      $(document).on("click", "#btnApplyPreviewedTemplate", function (e) {
        e.preventDefault();
        const templateId = $(this).data("template-id") || "mit";
        $(".v3-template-card").removeClass("active");
        $(`.v3-template-card[data-template-id="${templateId}"]`).addClass("active");
        $("#tourPackageTemplate").val(templateId);
        $("#modalPackageTemplatePreview").removeClass("is-active active").hide();
        $("#templatePreviewCanvas").empty();
      });

      // Close Preview Modal
      $(document).on("click", "#btnCloseTemplatePreviewModal", function (e) {
        e.preventDefault();
        $("#modalPackageTemplatePreview").removeClass("is-active active").hide();
        $("#templatePreviewCanvas").empty();
      });

      $(document).on("click", "#modalPackageTemplatePreview", function (e) {
        if (e.target === this) {
          $("#modalPackageTemplatePreview").removeClass("is-active active").hide();
          $("#templatePreviewCanvas").empty();
        }
      });

      // Escape key to close modal
      $(document).on("keydown", function (e) {
        if ($("#modalPackageTemplatePreview").hasClass("is-active") || $("#modalPackageTemplatePreview").is(":visible")) {
          if (e.key === "Escape" || e.keyCode === 27) {
            $("#modalPackageTemplatePreview").removeClass("is-active active").hide();
            $("#templatePreviewCanvas").empty();
          } else if (e.key === "ArrowLeft" || e.keyCode === 37) {
            e.preventDefault();
            self.navigateTemplatePreview(-1);
          } else if (e.key === "ArrowRight" || e.keyCode === 39) {
            e.preventDefault();
            self.navigateTemplatePreview(1);
          }
        }
      });

      // Step 2 Action: Publish / Save Itinerary Form
      $(document).on("click", "#btnPublishPackage, #createTourPackageBtn", function (e) {
        e.preventDefault();
        self.handlePublishItinerary();
      });

      this.eventsBound = true;
    },

    initCitySelect2: function ($context) {
      const self = this;
      const $scope = $context || $("#packageDomesticAddApp, #packageInternationalAddApp, .package-domestic-add-wrapper, .package-international-add-wrapper");

      if (typeof $.fn.select2 === "undefined") return;

      // Initialize destination & logistics city selects
      $scope.find(".v3-city-select").each(function () {
        const $select = $(this);
        if ($select.hasClass("select2-hidden-accessible")) return;

        $select.select2({
          placeholder: $select.data("placeholder") || "Search city, state or location...",
          allowClear: $select.data("allow-clear") !== false && $select.attr("data-allow-clear") !== "false",
          width: "100%",
          tags: true,
          dropdownAutoWidth: true,
          ajax: {
            url: "/api/v3/cities/",
            dataType: "json",
            delay: 250,
            data: function (params) {
              const countryVal = $("#destinationCountry").val() || "";
              return {
                search: params.term || "",
                q: params.term || "",
                country: countryVal
              };
            },
            processResults: function (data) {
              return {
                results: (data && data.results) || []
              };
            },
            cache: true
          },
          templateResult: self.formatCityOption,
          templateSelection: function (item) {
            return item.city || item.text || item.id || "";
          }
        }).on("change select2:select select2:clear", function () {
          if ($select.hasClass("v3-day-plan-city-select")) {
            const v = $select.val();
            if (v && v !== "Transit" && v !== "— In Transit —") {
              $select.attr("data-prev-city", v);
            }
            self.updateLivePreview();
          } else {
            self.updateTotalDuration();
            self.updateLivePreview();
            self.syncDaySchedulePlanner();
          }
        });
      });

      // Standard single select2 components (Travel style, Hotel select, etc.)
      $scope.find(".v3-standard-select").each(function () {
        const $sel = $(this);
        if ($sel.hasClass("select2-hidden-accessible")) return;
        $sel.select2({
          width: "100%",
          minimumResultsForSearch: 10
        });
      });
    },

    formatCityOption: function (item) {
      if (item.loading) return item.text || "Searching locations...";
      if (!item.id) return item.text;

      const cityName = item.city || item.text || item.id;
      const stateName = item.state || "";
      const countryName = item.country || "India";
      const meta = [stateName, countryName].filter(Boolean).join(", ");

      const $res = $(
        '<div class="v3-select2-city-row">' +
          '<div class="v3-select2-city-name"><i class="fi fi-rr-marker"></i> <span>' +
          selfEscape(cityName) +
          "</span></div>" +
          (meta ? '<div class="v3-select2-city-meta"><i class="fi fi-rr-flag"></i> ' + selfEscape(meta) + "</div>" : "") +
        "</div>"
      );
      return $res;
    },

    addDestinationRow: function () {
      const $container = $("#destinationRowsContainer");
      const rowCount = $container.find(".v3-destination-row").length + 1;

      const rowHtml = `
        <div class="v3-destination-row" data-row="${rowCount}">
          <div class="v3-dest-handle">
            <span class="v3-dest-badge"><i class="fi fi-rr-marker"></i> Stop ${rowCount}</span>
          </div>
          <div class="v3-form-group v3-dest-city-group">
            <label class="v3-form-label">Destination City <sup class="required">*</sup></label>
            <div class="v3-input-icon-wrap">
              <i class="fi fi-rr-marker input-icon"></i>
              <select class="v3-city-select v3-form-control" name="destination[]" data-placeholder="Select destination stopover...">
              </select>
            </div>
          </div>
          <div class="v3-form-group v3-dest-duration-group">
            <label class="v3-form-label">Stay Duration <sup class="required">*</sup></label>
            <div class="v3-duration-input-stepper">
              <button type="button" class="v3-duration-btn v3-duration-btn-minus" title="Decrease nights">−</button>
              <input type="number" class="v3-form-control v3-duration-field" name="duration[]" value="2" min="1" max="60" />
              <span class="v3-duration-suffix">Nights</span>
              <button type="button" class="v3-duration-btn v3-duration-btn-plus" title="Increase nights">+</button>
            </div>
          </div>
          <div class="v3-dest-action">
            <button type="button" class="btn-remove-dest-row" title="Remove this stop">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        </div>
      `;

      const $newRow = $(rowHtml).hide();
      $container.append($newRow);
      $newRow.slideDown(200);

      this.initCitySelect2($newRow);
      this.refreshDestinationIndices();
      this.updateTotalDuration();
      this.updateLivePreview();
    },

    removeDestinationRow: function ($row) {
      const $container = $("#destinationRowsContainer");
      if ($container.find(".v3-destination-row").length <= 1) {
        this.notify("warning", "At least one destination stop is required.");
        return;
      }

      const self = this;
      $row.slideUp(180, function () {
        $row.remove();
        self.refreshDestinationIndices();
        self.updateTotalDuration();
        self.updateLivePreview();
      });
    },

    refreshDestinationIndices: function () {
      const $rows = $("#destinationRowsContainer .v3-destination-row");
      $rows.each(function (index) {
        const idx = index + 1;
        $(this).attr("data-row", idx);
        $(this).find(".v3-dest-badge").html(`<i class="fi fi-rr-marker"></i> Stop ${idx}`);
        if ($rows.length === 1) {
          $(this).find(".v3-dest-action").hide();
        } else {
          $(this).find(".v3-dest-action").show();
        }
      });
    },

    updateTotalDuration: function () {
      let totalNights = 0;
      $("input[name='duration[]']").each(function () {
        const n = parseInt($(this).val(), 10);
        if (!isNaN(n) && n > 0) {
          totalNights += n;
        }
      });

      if (totalNights === 0) totalNights = 1;
      const totalDays = totalNights + 1;

      $("#statTotalNights").text(totalNights);
      $("#statTotalDays").text(totalDays);
      $("#summaryDurationBadge").html(`<i class="fi fi-rr-moon"></i> ${totalNights}N / <i class="fi fi-rr-sun"></i> ${totalDays}D`);
      $("#summaryTotalDurationText").text(`${totalNights} Nights & ${totalDays} Days`);

      this.syncDaySchedulePlanner();
    },

    getAvailableCities: function () {
      const cities = [];
      const addCity = function (val) {
        if (!val) return;
        let c = val.trim();
        if (c.indexOf("(") !== -1) c = c.split("(")[0].trim();
        if (c.indexOf(",") !== -1) c = c.split(",")[0].trim();
        if (
          c &&
          !c.startsWith("Stop ") &&
          !["scenic destination", "destination city", "destination", "transit", "— in transit —"].includes(c.toLowerCase()) &&
          !cities.some(x => x.toLowerCase() === c.toLowerCase())
        ) {
          cities.push(c);
        }
      };

      addCity($("#startingCity").val() || $("#startingCity").find("option:selected").text());

      $("#destinationRowsContainer .v3-destination-row").each(function () {
        addCity($(this).find(".v3-city-select").val() || $(this).find(".v3-city-select option:selected").text());
      });

      addCity($("#endCity").val() || $("#endCity").find("option:selected").text());

      return cities;
    },

    syncDaySchedulePlanner: function () {
      const $container = $("#v3DaySchedulePlannerList");
      if (!$container.length) return;

      const self = this;
      const stops = [];
      let totalNightsFromRows = 0;

      $("#destinationRowsContainer .v3-destination-row").each(function (idx) {
        let c = $(this).find(".v3-city-select").val() || $(this).find(".v3-city-select option:selected").text() || "";
        c = c.trim();
        if (c.indexOf("(") !== -1) c = c.split("(")[0].trim();
        if (c.indexOf(",") !== -1) c = c.split(",")[0].trim();
        const nights = parseInt($(this).find("input[name='duration[]']").val() || "1", 10);
        const validNights = Math.max(1, isNaN(nights) ? 1 : nights);
        totalNightsFromRows += validNights;
        const isNamed = !!(c && !c.startsWith("Stop ") && !["scenic destination", "destination city", "destination"].includes(c.toLowerCase()));
        stops.push({
          city: isNamed ? c : `Stop ${idx + 1}`,
          isNamed: isNamed,
          nights: validNights
        });
      });

      let startingCity = ($("#startingCity").val() || $("#startingCity").find("option:selected").text() || "").trim();
      if (startingCity.indexOf("(") !== -1) startingCity = startingCity.split("(")[0].trim();
      if (startingCity.indexOf(",") !== -1) startingCity = startingCity.split(",")[0].trim();

      let endCity = ($("#endCity").val() || $("#endCity").find("option:selected").text() || startingCity).trim();
      if (endCity.indexOf("(") !== -1) endCity = endCity.split("(")[0].trim();
      if (endCity.indexOf(",") !== -1) endCity = endCity.split(",")[0].trim();

      const defaultStays = [];
      stops.forEach(function (stop) {
        for (let n = 0; n < stop.nights; n++) {
          defaultStays.push(stop.city);
        }
      });

      let totalNights = defaultStays.length;
      if (totalNights === 0) {
        totalNights = totalNightsFromRows > 0 ? totalNightsFromRows : 1;
        const fallbackCity = stops[0] ? stops[0].city : (startingCity || "Destination City");
        for (let k = 0; k < totalNights; k++) {
          defaultStays.push(fallbackCity);
        }
      }
      const totalDays = totalNights + 1;

      $("#dshDaysCountBadge").text(`${totalDays} Days Scheduled`);

      const availableCities = self.getAvailableCities();
      if (startingCity && !availableCities.some(x => x.toLowerCase() === startingCity.toLowerCase())) {
        availableCities.unshift(startingCity);
      }
      if (endCity && !availableCities.some(x => x.toLowerCase() === endCity.toLowerCase())) {
        availableCities.push(endCity);
      }
      stops.forEach(function (s) {
        if (s.isNamed && !availableCities.some(x => x.toLowerCase() === s.city.toLowerCase())) {
          availableCities.push(s.city);
        }
      });
      if (availableCities.length === 0) {
        availableCities.push("Destination City");
      }

      // Update cities datalist for auto-suggesting stopovers
      const $datalist = $("#v3PlannerCitiesDatalist");
      if ($datalist.length) {
        let dlHtml = "";
        availableCities.forEach(function (c) {
          dlHtml += `<option value="${selfEscape(c)}">`;
        });
        $datalist.html(dlHtml);
      }

      // Preserve existing user choices if already entered in row
      const preservedData = {};
      $container.find(".v3-day-plan-row").each(function () {
        const dNum = $(this).data("day");
        const $citySel = $(this).find(".v3-day-plan-city-select");
        preservedData[dNum] = {
          stayType: $(this).find(".v3-day-plan-stay-type-select").val(),
          city: $citySel.val(),
          notes: $(this).find(".v3-day-plan-notes-input").val(),
          defaultCity: $citySel.attr("data-default-city") || ""
        };
      });

      // Cleanly destroy select2 instances before re-rendering
      $container.find(".v3-day-plan-city-select.select2-hidden-accessible").each(function () {
        try { $(this).select2("destroy"); } catch (e) {}
      });
      $container.empty();

      for (let i = 0; i < totalDays; i++) {
        const dayNum = i + 1;
        const isFirstDay = (dayNum === 1);
        const isDeparture = (dayNum === totalDays);
        const isDefaultTransit = (isFirstDay || isDeparture);
        const defaultStayType = isDefaultTransit ? "transit" : "in_hotel";
        const defaultHotelCity = isDeparture
          ? (endCity || startingCity || (stops[0] ? stops[0].city : "Destination City"))
          : (isFirstDay
              ? (stops[0] ? stops[0].city : startingCity || "Destination City")
              : (defaultStays[dayNum - 2] || defaultStays[dayNum - 1] || defaultStays[0] || (stops[0] ? stops[0].city : startingCity) || "Destination City"));

        // ONLY Day 1 and the final day default to Transit; all intermediate days MUST be in_hotel
        let currentStayType = isDefaultTransit ? "transit" : "in_hotel";
        let currentCity = (currentStayType === "transit") ? "" : defaultHotelCity;

        if (preservedData[dayNum]) {
          const prevRow = preservedData[dayNum];
          // Intermediate days must never inherit transit from previous last-day states
          if (!isDefaultTransit) {
            currentStayType = "in_hotel";
          }

          const prevCity = (prevRow.city || "").trim();
          const prevDefault = (prevRow.defaultCity || "").trim().toLowerCase();
          const isPlaceholder = !prevCity || [
            "scenic destination", "destination city", "destination", "transit", "— in transit —"
          ].includes(prevCity.toLowerCase()) || prevCity.startsWith("Stop ");

          if (!isPlaceholder && prevDefault && prevCity.toLowerCase() !== prevDefault) {
            currentCity = prevCity;
          } else {
            currentCity = (currentStayType === "transit") ? "" : defaultHotelCity;
          }
        }

        const isTransit = (currentStayType === "transit");
        const currentNotes = (preservedData[dayNum] && preservedData[dayNum].notes) ? preservedData[dayNum].notes : "";

        // Build options for cities dropdown
        let cityOptionsHtml = "";
        let foundMatch = false;

        if (isTransit) {
          cityOptionsHtml = '<option value="Transit" disabled selected>— In Transit —</option>';
          availableCities.forEach(function (c) {
            cityOptionsHtml += `<option value="${selfEscape(c)}">${selfEscape(c)}</option>`;
          });
        } else {
          availableCities.forEach(function (c) {
            const isSel = (currentCity && c.toLowerCase() === currentCity.toLowerCase());
            if (isSel) foundMatch = true;
            cityOptionsHtml += `<option value="${selfEscape(c)}" ${isSel ? "selected" : ""}>${selfEscape(c)}</option>`;
          });

          if (currentCity && !foundMatch) {
            cityOptionsHtml = `<option value="${selfEscape(currentCity)}" selected>${selfEscape(currentCity)}</option>` + cityOptionsHtml;
          }
        }

        const placeholderText = isDeparture
          ? "e.g. Hotel check-out, souvenir shopping, airport transfer"
          : (dayNum === 1
            ? "e.g. Arrival pickup, hotel check-in, evening leisure walk"
            : `e.g. Sightseeing visits, monuments, scenic viewpoints in ${currentCity || defaultHotelCity}`);

        const rowHtml = `
          <div class="v3-day-plan-row" data-day="${dayNum}">
            <div class="v3-day-plan-header">
              <span class="v3-day-plan-tag"><i class="fi fi-rr-calendar"></i> Day ${dayNum}</span>
            </div>
            <div class="v3-day-plan-inputs-grid">
              <div class="v3-day-plan-stay-type-wrap">
                <label class="v3-day-plan-label"><i class="fi fi-rr-bed"></i> Night Stay</label>
                <select class="v3-form-control v3-day-plan-stay-type-select" data-day="${dayNum}">
                  <option value="in_hotel" ${!isTransit ? "selected" : ""}>In Hotel</option>
                  <option value="transit" ${isTransit ? "selected" : ""}>Transit</option>
                </select>
              </div>
              <div class="v3-day-plan-city-wrap">
                <label class="v3-day-plan-label"><i class="fi fi-rr-marker"></i> City Name</label>
                <select class="v3-form-control v3-city-select v3-day-plan-city-select ${isTransit ? 'is-disabled' : ''}"
                        name="dayStayCity[${dayNum}]"
                        data-day="${dayNum}"
                        data-allow-clear="false"
                        data-placeholder="Search stay city..."
                        data-default-city="${selfEscape(defaultHotelCity)}"
                        data-prev-city="${selfEscape(currentCity || defaultHotelCity)}"
                        ${isTransit ? 'disabled' : ''}>
                  ${cityOptionsHtml}
                </select>
              </div>
              <div class="v3-day-plan-notes-wrap">
                <label class="v3-day-plan-label"><i class="fi fi-rr-sparkles"></i> Day Highlights / Focus (Optional)</label>
                <input type="text" class="v3-form-control v3-day-plan-notes-input" placeholder="${placeholderText}" value="${selfEscape(currentNotes)}" />
              </div>
            </div>
          </div>
        `;

        $container.append(rowHtml);
      }

      // Initialize Select2 city autocomplete on day planner rows
      self.initCitySelect2($container);
    },

    syncDropOffWithPickup: function () {
      const pickupVal = $("#startingCity").val();
      const pickupText = $("#startingCity").find("option:selected").text() || pickupVal;
      if (pickupVal) {
        const $endSelect = $("#endCity");
        if ($endSelect.find(`option[value="${pickupVal}"]`).length) {
          $endSelect.val(pickupVal).trigger("change");
        } else {
          const newOption = new Option(pickupText, pickupVal, true, true);
          $endSelect.append(newOption).trigger("change");
        }
      }
    },

    fetchVehicles: function () {
      const self = this;
      if (this.vehiclesLoaded && this.vehicleData.length) {
        self.populateVehicleDropdowns(this.vehicleData);
        return;
      }

      $.ajax({
        url: "/api/v3/vehicles/",
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && Array.isArray(res.vehicles) && res.vehicles.length > 0) {
            self.vehicleData = res.vehicles;
            self.vehiclesLoaded = true;
            self.populateVehicleDropdowns(res.vehicles);
          } else {
            self.populateDefaultVehicles();
          }
        },
        error: function () {
          self.populateDefaultVehicles();
        }
      });
    },

    populateVehicleDropdowns: function (vehicles) {
      const $outstation = $("#outstationVehicle");
      const $local = $("#localSighseeignVehicle");

      const outstationCurrent = $outstation.val();
      const localCurrent = $local.val();

      $outstation.empty().append(new Option("Select Outstation Vehicle...", "", false, false));
      $local.empty().append(new Option("Select Local Sightseeing Vehicle...", "", false, false));

      vehicles.forEach(function (v) {
        const title = v.title || v.name || "Vehicle";
        const type = v.type_label || v.type || "";
        const seating = v.seating ? ` (${v.seating} Seats)` : "";
        const label = `${title} - ${type}${seating}`;
        const val = v.title || v.id;

        const opt1 = new Option(label, val, false, val.toLowerCase() === (outstationCurrent || "volvo").toLowerCase());
        const opt2 = new Option(label, val, false, val.toLowerCase() === (localCurrent || "maruti alto").toLowerCase());

        $outstation.append(opt1);
        $local.append(opt2);
      });

      if (!$outstation.val() && $outstation.find("option").length > 1) {
        $outstation.val($outstation.find("option:eq(1)").val());
      }
      if (!$local.val() && $local.find("option").length > 1) {
        $local.val($local.find("option:eq(1)").val());
      }

      $outstation.trigger("change");
      $local.trigger("change");
      this.updateLivePreview();
    },

    populateDefaultVehicles: function () {
      const defaultVehicles = [
        { title: "Volvo", type_label: "Bus / Coach", seating: 45 },
        { title: "Toyota Innova Crysta", type_label: "MPV / SUV", seating: 7 },
        { title: "Maruti Ertiga", type_label: "MPV", seating: 6 },
        { title: "Swift Dzire", type_label: "Sedan", seating: 4 },
        { title: "Maruti Alto", type_label: "Hatchback", seating: 4 },
        { title: "Tempo Traveller", type_label: "Tempo Traveller", seating: 12 },
        { title: "Mahindra Scorpio", type_label: "SUV", seating: 7 }
      ];
      this.populateVehicleDropdowns(defaultVehicles);
    },

    
    suggestPackageTitle: function (silent) {
      const stops = [];
      $("#destinationRowsContainer .v3-destination-row").each(function () {
        let c = $(this).find(".v3-city-select option:selected").text().trim() || $(this).find(".v3-city-select").val() || "";
        if (c.indexOf(",") !== -1) c = c.split(",")[0].trim();
        if (c && !c.toLowerCase().includes("select") && !c.toLowerCase().includes("search") && !stops.includes(c)) stops.push(c);
      });

      const destCountry = ($("#destinationCountry option:selected").text().trim() || $("#destinationCountry").val() || "").trim();
      const startingCityText = ($("#startingCity option:selected").text().trim() || $("#startingCity").val() || "").trim();
      let destStr = stops.length > 0 ? stops.slice(0, 3).join(" & ") : "";
      if (!destStr && destCountry && !destCountry.toLowerCase().includes("select")) {
        destStr = destCountry;
      }
      if (!destStr && startingCityText && !startingCityText.toLowerCase().includes("select") && !startingCityText.toLowerCase().includes("search")) {
        destStr = startingCityText.indexOf(",") !== -1 ? startingCityText.split(",")[0].trim() : startingCityText;
      }
      if (!destStr) {
        destStr = "Scenic Destinations";
      }

      const totalDays = parseInt($("#statTotalDays").text(), 10) || 3;
      const totalNights = parseInt($("#statTotalNights").text(), 10) || (totalDays - 1);
      let theme = $("#travelStyle option:selected").text().trim() || "Getaway";
      if (theme.indexOf("&") !== -1) theme = theme.split("&")[0].trim();

      const titleTemplates = [
        `${totalDays} Days Enchanting ${destStr} Tour`,
        `${totalNights}N/${totalDays}D Best of ${destStr} (${theme})`,
        `${totalDays} Days Scenic ${destStr} Holiday Experience`,
        `${totalDays} Days ${destStr} Explorer & Sightseeing Stays`,
        `${totalNights}N/${totalDays}D Signature ${destStr} Escape`
      ];

      this.titleCycleIndex = (this.titleCycleIndex || 0) % titleTemplates.length;
      const suggested = titleTemplates[this.titleCycleIndex];
      this.titleCycleIndex++;

      const $input = $("#packageName");
      $input.val(suggested).trigger("input");
      $input.closest(".v3-input-icon-wrap").removeClass("is-invalid");
      $("#summaryPackageTitle").text(suggested);

      // Subtle inline visual feedback without SweetAlert
      const $btn = $("#btnSuggestTitle");
      $btn.css({ opacity: "0.6" });
      setTimeout(function () {
        $btn.css({ opacity: "1" });
      }, 300);

      return suggested;
    },

    updateJourneyFlow: function (startingCity, destinations, endCity) {
      const $flow = $("#v3RouteJourneyFlow");
      if (!$flow.length) return;

      let flowHtml = '<span class="v3-rjf-title"><i class="fi fi-rr-route"></i> Route Flow:</span>';
      flowHtml += `<span class="v3-rjf-node pickup"><i class="fi fi-rr-plane-departure"></i> ${selfEscape(startingCity)}</span>`;

      if (Array.isArray(destinations)) {
        destinations.forEach(function (d, i) {
          flowHtml += ` <span class="v3-rjf-arrow"><i class="fi fi-rr-angle-right"></i></span> `;
          flowHtml += `<span class="v3-rjf-node stop"><i class="fi fi-rr-marker"></i> ${selfEscape(d.city)} (${d.nights}N)</span>`;
        });
      }

      flowHtml += ` <span class="v3-rjf-arrow"><i class="fi fi-rr-angle-right"></i></span> `;
      flowHtml += `<span class="v3-rjf-node drop"><i class="fi fi-rr-plane-arrival"></i> ${selfEscape(endCity)}</span>`;

      $flow.html(flowHtml);
    },

    updateLivePreview: function () {
      const name = $("#packageName").val().trim() || "Untitled Domestic Package";
      const travelStyle = $("#travelStyle").val() || "Cultural";
      const startingCityRaw = ($("#startingCity").find("option:selected").text().trim() || $("#startingCity").val() || "").trim();
      let startingCity = startingCityRaw;
      if (startingCity.indexOf("(") !== -1) startingCity = startingCity.split("(")[0].trim();
      if (startingCity.indexOf(",") !== -1) startingCity = startingCity.split(",")[0].trim();
      if (!startingCity) startingCity = "Pickup Point";

      const isSameAsPickup = $("#sameAsPickupToggle").is(":checked");
      let endCity = startingCity;
      if (!isSameAsPickup) {
        let endRaw = ($("#endCity").find("option:selected").text().trim() || $("#endCity").val() || "").trim();
        if (endRaw.indexOf("(") !== -1) endRaw = endRaw.split("(")[0].trim();
        if (endRaw.indexOf(",") !== -1) endRaw = endRaw.split(",")[0].trim();
        endCity = endRaw || "Drop-off Point";
      }

      const outstation = $("#outstationVehicle").val() || "Not Assigned";
      const local = $("#localSighseeignVehicle").val() || "Not Assigned";

      const destinations = [];
      $(".v3-destination-row").each(function (idx) {
        let destCity = $(this).find(".v3-city-select option:selected").text().trim() || $(this).find(".v3-city-select").val() || "";
        if (destCity.indexOf("(") !== -1) destCity = destCity.split("(")[0].trim();
        if (destCity.indexOf(",") !== -1) destCity = destCity.split(",")[0].trim();
        if (!destCity || destCity.toLowerCase() === "destination") destCity = `Stop ${idx + 1}`;
        const duration = $(this).find("input[name='duration[]']").val() || "1";
        destinations.push({ city: destCity, nights: duration });
      });

      $("#summaryPackageTitle").text(name);
      $("#summaryTravelStyleBadge").html(`<i class="fi fi-rr-compass"></i> ${capitalize(travelStyle)}`);
      $("#summaryOutstationVehicle").text(outstation);
      $("#summaryLocalVehicle").text(local);

      // Update sidebar meal inclusions
      const selectedMeals = [];
      $("input[name='meals[]']:checked").each(function () {
        selectedMeals.push($(this).val());
      });
      const $mealsTags = $("#summaryMealsTags");
      if ($mealsTags.length) {
        if (selectedMeals.length === 0) {
          $mealsTags.html('<span class="v3-meal-tag-pill none">None (Room Only)</span>');
        } else {
          let mHtml = "";
          const mealIcons = {
            "Breakfast": "fi-rr-coffee",
            "Lunch": "fi-rr-utensils",
            "Evening Snacks": "fi-rr-mug-hot-alt",
            "Dinner": "fi-rr-soup"
          };
          selectedMeals.forEach(function (m) {
            const ico = mealIcons[m] || "fi-rr-restaurant";
            mHtml += `<span class="v3-meal-tag-pill"><i class="fi ${ico}"></i> ${selfEscape(m)}</span>`;
          });
          $mealsTags.html(mHtml);
        }
      }

      let timelineHtml = `
        <div class="v3-timeline-item origin">
          <div class="v3-timeline-marker"><i class="fi fi-rr-car"></i></div>
          <div class="v3-timeline-content">
            <span class="v3-timeline-label">Pickup Point</span>
            <strong class="v3-timeline-val">${selfEscape(startingCity)}</strong>
          </div>
        </div>
      `;

      destinations.forEach(function (d, i) {
        timelineHtml += `
          <div class="v3-timeline-item stop">
            <div class="v3-timeline-marker"><span>${i + 1}</span></div>
            <div class="v3-timeline-content">
              <span class="v3-timeline-label">Stop ${i + 1} (${d.nights}N)</span>
              <strong class="v3-timeline-val">${selfEscape(d.city)}</strong>
            </div>
          </div>
        `;
      });

      timelineHtml += `
        <div class="v3-timeline-item destination">
          <div class="v3-timeline-marker"><i class="fi fi-rr-flag-checkered"></i></div>
          <div class="v3-timeline-content">
            <span class="v3-timeline-label">Drop-off Point</span>
            <strong class="v3-timeline-val">${selfEscape(endCity)}</strong>
          </div>
        </div>
      `;

      $("#summaryRouteTimeline").html(timelineHtml);
      this.updateJourneyFlow(startingCity, destinations, endCity);
    },

    handleGeneratePreview: function () {
      const self = this;

      function getSelectVal($sel) {
        if (!$sel || !$sel.length) return "";
        let val = ($sel.val() || "").trim();
        let s2Data = null;
        try {
          if ($sel.data("select2")) {
            s2Data = $sel.select2("data");
          }
        } catch (e) {}

        if (s2Data && s2Data.length && s2Data[0]) {
          const t = (s2Data[0].city || s2Data[0].text || s2Data[0].id || "").trim();
          if (t && t !== $sel.data("placeholder") && !t.includes("Search city") && !t.includes("Select drop-off") && !t.includes("Select pickup") && !t.includes("Select destination")) {
            return t;
          }
        }

        const optSelected = $sel.find("option:selected");
        if (optSelected.length) {
          const optText = (optSelected.text() || "").trim();
          if (optText && optText !== $sel.data("placeholder") && !optText.includes("Search city") && !optText.includes("Select drop-off") && !optText.includes("Select pickup") && !optText.includes("Select destination")) {
            return optText;
          }
        }

        if (val && val !== $sel.data("placeholder") && !val.includes("Search city")) {
          return val;
        }
        return "";
      }

      // Gather form values
      let packageName = ($("#packageName").val() || "").trim();

      // Auto-generate title if left blank on submit so user isn't blocked
      if (!packageName) {
        packageName = self.suggestPackageTitle(true);
        if (!packageName) {
          packageName = ($("#packageName").val() || "").trim();
        }
      }

      const startingCity = getSelectVal($("#startingCity"));
      const isSameAsPickup = $("#sameAsPickupToggle").is(":checked");
      const endCity = isSameAsPickup ? startingCity : getSelectVal($("#endCity"));
      const travelStyle = ($("#travelStyle").val() || "").trim() || "Cultural & Heritage";
      const outstationVehicle = ($("#outstationVehicle").val() || "").trim() || "Volvo";
      const localSighseeignVehicle = ($("#localSighseeignVehicle").val() || "").trim() || "Sedan";

      const errors = [];
      if (!packageName) {
        errors.push("Please enter a package title / name.");
        $("#packageName").closest(".v3-input-icon-wrap").addClass("is-invalid");
      } else {
        $("#packageName").closest(".v3-input-icon-wrap").removeClass("is-invalid");
      }

      if (!startingCity) {
        errors.push("Please select a pickup city.");
        $("#startingCity").closest(".v3-input-icon-wrap").addClass("is-invalid");
      } else {
        $("#startingCity").closest(".v3-input-icon-wrap").removeClass("is-invalid");
      }

      if (!endCity) {
        errors.push("Please select a drop-off city.");
        $("#endCity").closest(".v3-input-icon-wrap").addClass("is-invalid");
      } else {
        $("#endCity").closest(".v3-input-icon-wrap").removeClass("is-invalid");
      }

      const destinations = [];
      const durations = [];
      let destinationsValid = true;

      $("#destinationRowsContainer .v3-destination-row").each(function () {
        const $row = $(this);
        const $destSelect = $row.find(".v3-city-select");
        const $durInput = $row.find("input[name='duration[]']");

        let dest = getSelectVal($destSelect);
        let dur = parseInt($durInput.val(), 10);
        if (isNaN(dur) || dur < 1) dur = 2;

        if (!dest) {
          destinationsValid = false;
          $row.find(".v3-input-icon-wrap").addClass("is-invalid");
        } else {
          $row.find(".v3-input-icon-wrap").removeClass("is-invalid");
          destinations.push(dest);
          durations.push(dur);
        }
      });

      if (!destinationsValid || destinations.length === 0) {
        errors.push("Please select a destination city for each stopover.");
      }

      if (errors.length > 0) {
        self.notify("warning", errors[0]);
        return;
      }

      const pkgType = $("#packageType").val() || ($("#packageInternationalAddApp").length ? "international" : "domestic");
      const countryVal = pkgType === "international" ? ($("#destinationCountry").val() || "") : "India";

      // Collect Day-by-Day schedule with night stays & custom notes
      const daySchedule = [];
      $("#v3DaySchedulePlannerList .v3-day-plan-row").each(function () {
        const dNum = $(this).data("day");
        const stayType = $(this).find(".v3-day-plan-stay-type-select").val() || "in_hotel";
        let stayCity = "";
        if (stayType === "transit") {
          stayCity = "Transit";
        } else {
          stayCity = ($(this).find(".v3-day-plan-city-select").val() || "").trim();
          if (!stayCity || stayCity.toLowerCase() === "transit") {
            stayCity = $(this).find(".v3-day-plan-city-select").attr("data-default-city") || "Destination City";
          }
        }
        const notes = $(this).find(".v3-day-plan-notes-input").val() || "";
        daySchedule.push({
          day: dNum,
          stayType: stayType,
          stayCity: stayCity,
          notes: notes
        });
      });

      // Collect Included Meals
      const includedMeals = [];
      $("input[name='meals[]']:checked").each(function () {
        const val = $(this).val();
        if (val) includedMeals.push(val);
      });
      const mealPlanText = includedMeals.length > 0 ? includedMeals.join(" & ") : "Room Only";

      // Store current valid inputs with all aliases
      self.currentStepData = {
        packageName: packageName,
        name: packageName,
        packageType: pkgType,
        country: countryVal,
        startingCity: startingCity,
        startCity: startingCity,
        endCity: endCity,
        travelStyle: travelStyle,
        outstationVehicle: outstationVehicle,
        localSighseeignVehicle: localSighseeignVehicle,
        localSightseeingVehicle: localSighseeignVehicle,
        destination: destinations,
        destinationCity: destinations,
        destinations: destinations,
        duration: durations,
        tripDuration: durations,
        durations: durations,
        meals: includedMeals,
        includedMeals: includedMeals,
        mealPlan: mealPlanText,
        days: daySchedule,
        daySchedule: daySchedule,
        _json_payload: JSON.stringify({
          packageName: packageName,
          packageType: pkgType,
          country: countryVal,
          startingCity: startingCity,
          endCity: endCity,
          travelStyle: travelStyle,
          outstationVehicle: outstationVehicle,
          localSighseeignVehicle: localSighseeignVehicle,
          destination: destinations,
          duration: durations,
          meals: includedMeals,
          mealPlan: mealPlanText,
          days: daySchedule
        })
      };

      // Switch to Step 2 & Trigger In-Page AI generation
      self.goToStep(2);
      self.generateAIItinerary();
    },

    goToStep: function (stepNumber) {
      const $stepper = $(".v3-stepper-container");

      if (stepNumber === 1) {
        $("#step2ItineraryContainer").addClass("is-hidden").hide();
        $("#step1ViewContainer").removeClass("is-hidden").fadeIn(200);

        $stepper.find(".v3-step-item:eq(0)").removeClass("completed").addClass("active");
        $stepper.find(".v3-step-item:eq(1)").removeClass("active completed").addClass("pending");
        $stepper.find(".v3-step-item:eq(2)").removeClass("active completed").addClass("pending");

        // Scroll up smoothly
        const $scrollTarget = $("#packageDomesticAddApp, #packageInternationalAddApp").first();
        if ($scrollTarget.length) {
          $("html, body").animate({ scrollTop: $scrollTarget.offset().top - 20 }, 300);
        }
      } else if (stepNumber === 2) {
        $("#step1ViewContainer").addClass("is-hidden").hide();
        $("#step2ItineraryContainer").removeClass("is-hidden").fadeIn(200);

        $stepper.find(".v3-step-item:eq(0)").removeClass("active pending").addClass("completed");
        $stepper.find(".v3-step-item:eq(1)").removeClass("pending completed").addClass("active");
        $stepper.find(".v3-step-item:eq(2)").removeClass("active completed").addClass("pending");

        const $scrollTarget = $("#packageDomesticAddApp, #packageInternationalAddApp").first();
        if ($scrollTarget.length) {
          $("html, body").animate({ scrollTop: $scrollTarget.offset().top - 20 }, 300);
        }
      }
    },

    generateAIItinerary: function () {
      const self = this;
      if (!self.currentStepData) {
        self.goToStep(1);
        return;
      }

      // Show Loading State, Hide others
      $("#itineraryPreviewContent").addClass("is-hidden").hide();
      $("#itineraryErrorState").addClass("is-hidden").hide();
      $("#itineraryLoadingState").removeClass("is-hidden").fadeIn(200);

      // Start dynamic loading text animation
      const messages = [
        "Analyzing destination routes and stay durations...",
        "Crafting bespoke day-by-day sightseeing with Smart AI...",
        "Selecting optimal route checkpoints & transfer schedules...",
        "Compiling inclusions, exclusions & hotel recommendations...",
        "Finalizing your professional travel package..."
      ];
      let msgIdx = 0;
      if (self.loadingInterval) clearInterval(self.loadingInterval);
      self.loadingInterval = setInterval(function () {
        msgIdx = (msgIdx + 1) % messages.length;
        $("#aiLoadingDesc").text(messages[msgIdx]);
      }, 2500);

      console.log("%c[AI Generator Request Triggered]", "color: #a855f7; font-weight: bold;");

      // Make API Request to generate itinerary
      $.ajax({
        url: "/api/v3/itinerary/generate/",
        type: "POST",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(self.currentStepData),
        dataType: "json",
        timeout: 90000,
        success: function (res) {
          clearInterval(self.loadingInterval);
          console.log("%c=== [V3 Domestic Tour Package AI Generator Response] ===", "color: #047857; font-size: 13px; font-weight: bold;");
          console.log(res);

          if (res && res.status === "success" && res.data) {
            $("#itineraryLoadingState").addClass("is-hidden").hide();
            $("#itineraryErrorState").addClass("is-hidden").hide();
            self.renderItineraryPreview(res.data);
          } else {
            $("#itineraryLoadingState").addClass("is-hidden").hide();
            $("#itineraryPreviewContent").addClass("is-hidden").hide();
            $("#aiErrorMessage").text((res && res.msg) || "Could not generate itinerary. Please retry.");
            $("#itineraryErrorState").removeClass("is-hidden").fadeIn(200);
          }
        },
        error: function (xhr) {
          clearInterval(self.loadingInterval);
          console.error("%c[AI Generator Error Response]:", "color: #ef4444; font-weight: bold;", xhr);
          $("#itineraryLoadingState").addClass("is-hidden").hide();
          $("#itineraryPreviewContent").addClass("is-hidden").hide();
          const errText = (xhr.responseJSON && xhr.responseJSON.msg) || "Network or server timeout occurred while generating AI itinerary.";
          $("#aiErrorMessage").text(errText);
          $("#itineraryErrorState").removeClass("is-hidden").fadeIn(200);
        }
      });
    },

    renderItineraryPreview: function (data) {
      const self = this;

      // 1. Populate Hero Header
      $("#previewHeroTitle").text(data.packageName || "Tour Itinerary");

      const routePills = [];
      const totalNights = data.totalNights || 0;
      const totalDays = data.totalDays || (totalNights + 1);
      routePills.push(`<span class="v3-pill-badge duration"><i class="fi fi-rr-moon"></i> ${totalNights} Nights / <i class="fi fi-rr-sun"></i> ${totalDays} Days</span>`);
      if (data.travelStyle) {
        routePills.push(`<span class="v3-pill-badge style"><i class="fi fi-rr-compass"></i> ${capitalize(data.travelStyle)}</span>`);
      }

      const destsArr = Array.isArray(data.destinationCity)
        ? data.destinationCity
        : (typeof data.destinationCity === "string" && data.destinationCity ? data.destinationCity.split(",").map(function (s) { return s.trim(); }) : []);
      const routePath = [data.startingCity, ...destsArr, data.endCity].filter(Boolean).join(" → ");
      if (routePath) {
        routePills.push(`<span class="v3-pill-badge route"><i class="fi fi-rr-route"></i> ${selfEscape(routePath)}</span>`);
      }
      if (data.outstationVehicle) {
        routePills.push(`<span class="v3-pill-badge vehicle"><i class="fi fi-rr-car-alt"></i> Outstation: ${selfEscape(data.outstationVehicle)}</span>`);
      }
      if (data.localSightseeingVehicle) {
        routePills.push(`<span class="v3-pill-badge vehicle"><i class="fi fi-rr-taxi"></i> Sightseeing: ${selfEscape(data.localSightseeingVehicle)}</span>`);
      }

      const mealPlan = data.mealPlan || (Array.isArray(data.includedMeals) && data.includedMeals.length > 0 ? data.includedMeals.join(" & ") : (self.currentStepData && self.currentStepData.mealPlan ? self.currentStepData.mealPlan : ""));
      if (mealPlan && mealPlan !== "Room Only") {
        routePills.push(`<span class="v3-pill-badge meal"><i class="fi fi-rr-restaurant"></i> Meals: ${selfEscape(mealPlan)}</span>`);
      }

      $("#previewHeroPills").html(routePills.join(""));

      // 2. Populate Hidden Backend Form Fields
      $("#itineraryPackageName").val(data.packageName);
      $("#itineraryTravelStyle").val(data.travelStyle);
      $("#itineraryDuration").val(totalNights);
      $("#itineraryOutstationVehicle").val(data.outstationVehicle);
      $("#itineraryLocalVehicle").val(data.localSightseeingVehicle);
      $("#itineraryLocation").val(destsArr.join(","));
      $("#itineraryStartCity").val(data.startingCity);
      $("#itineraryEndCity").val(data.endCity);
      $("#itineraryMealPlan").val(mealPlan || "Room Only");

      const pkgType = $("#packageType").val() || (self.currentStepData && self.currentStepData.packageType) || "domestic";
      let countryVal = "India";
      if (pkgType === "international") {
        countryVal = $("#destinationCountry").val() || (self.currentStepData && self.currentStepData.country) || (data.country || "");
      }
      $("#itineraryCountry").val(countryVal);
      $("#packageType").val(pkgType);

      // 3. Populate Overview Text
      const introText = (data.overview && (data.overview.intro || data.overview)) || "";
      $("#itineraryOverviewField").val(typeof introText === "string" ? introText.trim() : JSON.stringify(introText));

      // 4. Render Day-by-Day Itinerary Cards
      const $daysContainer = $("#itineraryDaysCardsContainer");
      $daysContainer.empty();

      const availableCities = self.getAvailableCities();

      if (Array.isArray(data.durations)) {
        data.durations.forEach(function (dayObj, idx) {
          const dayNum = idx + 1;
          const dayTitle = dayObj.day || `Day ${dayNum} - Sightseeing & Activities`;
          const dayDetails = dayObj.details || "";
          const dayStay = dayObj.stay || (self.currentStepData && self.currentStepData.days && self.currentStepData.days[idx] && self.currentStepData.days[idx].stayCity) || "";

          const dayCardHtml = self.buildPreviewDayCardHtml(dayNum, dayTitle, dayStay, dayDetails, availableCities);
          $daysContainer.append(dayCardHtml);
        });
      }

      self.refreshPreviewDayNumbers();

      // 5. Populate Inclusions & Exclusions
      const incText = (data.overview && data.overview.inclusion) || "";
      const excText = (data.overview && data.overview.exclusion) || "";
      $("#itineraryInclusionField").val(incText);
      $("#itineraryExclusionField").val(excText);

      // 6. Set Hotel Category
      if (data.hotel) {
        $("#itineraryHotelSelect").val(data.hotel).trigger("change");
      }

      // Show Preview Form
      $("#itineraryLoadingState").addClass("is-hidden").hide();
      $("#itineraryErrorState").addClass("is-hidden").hide();
      $("#itineraryPreviewContent").removeClass("is-hidden").fadeIn(250);
      self.initCitySelect2($("#itineraryPreviewContent"));
    },

    buildPreviewDayCardHtml: function (dayNum, dayTitle, dayStay, dayDetails, availableCities) {
      availableCities = availableCities || this.getAvailableCities();
      const isTransit = (dayStay && (dayStay.toLowerCase() === "transit" || dayStay.toLowerCase() === "tour departure" || dayStay.toLowerCase() === "no stay"));
      const hotelCity = isTransit ? "" : (dayStay || (availableCities[0] || "Destination City"));
      const finalStay = isTransit ? "Transit" : hotelCity;

      let cityOptions = "";
      let found = false;
      availableCities.forEach(function (c) {
        const isSel = (!isTransit && hotelCity && c.toLowerCase() === hotelCity.toLowerCase());
        if (isSel) found = true;
        cityOptions += `<option value="${selfEscape(c)}" ${isSel ? "selected" : ""}>${selfEscape(c)}</option>`;
      });

      if (hotelCity && !found && !isTransit) {
        cityOptions = `<option value="${selfEscape(hotelCity)}" selected>${selfEscape(hotelCity)}</option>` + cityOptions;
      }

      if (isTransit) {
        cityOptions = `<option value="" disabled selected>— In Transit —</option>` + cityOptions;
      }

      return `
        <div class="v3-day-card" data-day="${dayNum}">
          <div class="v3-day-card-header">
            <span class="v3-day-badge"><i class="fi fi-rr-calendar"></i> Day ${dayNum}</span>
            <span class="v3-day-collapsed-pill"><i class="fi fi-rr-bed"></i> ${selfEscape(finalStay)}</span>
            <div class="v3-day-title-wrap">
              <input type="text" class="v3-form-control v3-day-title-input" name="dayTitle[]" value="${selfEscape(dayTitle)}" placeholder="Day ${dayNum} Title..." required />
            </div>
            <div class="v3-day-card-actions">
              <button type="button" class="btn-remove-day-card" title="Delete Day ${dayNum}"><i class="fi fi-rr-trash"></i></button>
              <i class="fi fi-rr-angle-down v3-day-card-chevron" title="Toggle Day Details"></i>
            </div>
          </div>
          <div class="v3-day-stay-bar">
            <div class="v3-day-stay-field">
              <label class="v3-day-stay-label"><i class="fi fi-rr-bed"></i> Night Stay:</label>
              <select class="v3-form-control v3-day-card-stay-type-select" data-day="${dayNum}">
                <option value="transit" ${isTransit ? "selected" : ""}>Transit</option>
                <option value="in_hotel" ${!isTransit ? "selected" : ""}>In Hotel</option>
              </select>
              <div class="v3-day-card-city-wrap">
                <select class="v3-form-control v3-city-select v3-day-card-city-select ${isTransit ? 'is-disabled' : ''}"
                        data-day="${dayNum}"
                        data-placeholder="Search stay city..."
                        data-prev-city="${selfEscape(hotelCity || availableCities[0] || 'Destination City')}"
                        ${isTransit ? 'disabled' : ''}>
                  ${cityOptions}
                </select>
              </div>
              <input type="hidden" name="dayStay[]" class="v3-day-card-final-stay" value="${selfEscape(finalStay)}" />
            </div>
          </div>
          <div class="v3-day-card-body">
            <textarea class="v3-form-control v3-form-textarea v3-day-textarea" name="dayContent[]" rows="4" required placeholder="Describe day ${dayNum} activities, transfers and sightseeing...">${selfEscape(dayDetails)}</textarea>
            <div class="v3-day-quick-snippets">
              <span class="snippet-label">Quick Add:</span>
              <button type="button" class="btn-snippet" data-text="Morning breakfast at hotel followed by transfer and sightseeing.">+ Breakfast & Transfer</button>
              <button type="button" class="btn-snippet" data-text="Full-day guided excursion visiting key attractions and viewpoints.">+ Full Day Tour</button>
              <button type="button" class="btn-snippet" data-text="Evening at leisure for shopping, local cafe visits, and strolls.">+ Evening Leisure</button>
              <button type="button" class="btn-snippet" data-text="Delicious dinner and peaceful overnight stay in the hotel.">+ Dinner & Stay</button>
            </div>
          </div>
        </div>
      `;
    },

    addPreviewDay: function () {
      const $container = $("#itineraryDaysCardsContainer");
      const currentCards = $container.find(".v3-day-card").length;
      const newDayNum = currentCards + 1;
      const cities = this.getAvailableCities();
      const defaultStay = cities[cities.length - 1] || "Destination City";

      const dayTitle = `Day ${newDayNum} - Sightseeing & Exploration`;
      const dayDetails = `Enjoy local experiences, sightseeing visits, and leisure activities throughout the day. Conclude with dinner and an overnight stay in ${defaultStay}.`;

      const cardHtml = this.buildPreviewDayCardHtml(newDayNum, dayTitle, defaultStay, dayDetails, cities);
      $container.append(cardHtml);
      this.refreshPreviewDayNumbers();
    },

    removePreviewDay: function ($card) {
      const $container = $("#itineraryDaysCardsContainer");
      if ($container.find(".v3-day-card").length <= 1) {
        this.notify("warning", "Itinerary must contain at least one day.");
        return;
      }
      const self = this;
      $card.slideUp(180, function () {
        $card.remove();
        self.refreshPreviewDayNumbers();
      });
    },

    refreshPreviewDayNumbers: function () {
      const $cards = $("#itineraryDaysCardsContainer .v3-day-card");
      $("#previewDaysCountLabel").text(`Daily Itinerary Schedule (${$cards.length} Days)`);
      $cards.each(function (idx) {
        const num = idx + 1;
        $(this).attr("data-day", num);
        $(this).find(".v3-day-badge").html(`<i class="fi fi-rr-calendar"></i> Day ${num}`);
        const $btn = $(this).find(".btn-remove-day-card");
        $btn.attr("title", `Delete Day ${num}`);
        if ($cards.length === 1) {
          $btn.hide();
        } else {
          $btn.show();
        }
      });
    },

    handlePublishItinerary: function () {
      const self = this;
      const $btn = $("#btnPublishPackage, #createTourPackageBtn");

      // Validate overview & days
      const overview = $("#itineraryOverviewField").val().trim();
      if (!overview) {
        self.notify("warning", "Please provide the tour overview.");
        $("#itineraryOverviewField").focus();
        return;
      }

      let allDaysValid = true;
      $("input[name='dayTitle[]']").each(function () {
        if (!$(this).val().trim()) {
          allDaysValid = false;
          $(this).addClass("is-invalid");
        } else {
          $(this).removeClass("is-invalid");
        }
      });

      if (!allDaysValid) {
        self.notify("warning", "Please ensure all day titles are specified.");
        return;
      }

      // Prepare UI button loading
      $btn.prop("disabled", true).addClass("btn-loading");
      const originalBtnText = $btn.html();
      $btn.html('<span class="v3-btn-spinner"></span> Saving Itinerary & Creating Package...');

      // Ensure package title is populated on backend hidden field
      let pName = ($("#itineraryPackageName").val() || "").trim();
      if (!pName) {
        pName = ($("#packageName").val() || "").trim() || self.suggestPackageTitle(true);
        $("#itineraryPackageName").val(pName);
      }

      const formData = $("#createTourPackageForm").serialize();

      $.ajax({
        type: "POST",
        url: "/api/v3/packages/publish/",
        data: formData,
        dataType: "json",
        success: function (res) {
          if (res && (res.status === 200 || res.status === "success" || res.status === true || res.id)) {
            self.notify("success", res.msg || "Tour Package created successfully!");

            setTimeout(function () {
              const pkgId = res.id || "";
              const packageType = $("#packageType").val() || "domestic";
              window.location.href = `/dashboard/packages/${packageType}/price-list/?id=${pkgId}&request=new`;
            }, 1200);
          } else {
            $btn.prop("disabled", false).removeClass("btn-loading").html(originalBtnText);
            self.notify("warning", (res && res.msg) || "Something went wrong while saving the package.");
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).removeClass("btn-loading").html(originalBtnText);
          const err = (xhr.responseJSON && xhr.responseJSON.msg) || "Failed to save itinerary package. Please try again.";
          self.notify("warning", err);
        }
      });
    },

    openTemplatePreviewModal: function (templateId) {
      this.loadTemplatePreview(templateId);
    },

    navigateTemplatePreview: function (dir) {
      const templates = ["mit", "standard", "modern", "aleya"];
      let current = this.activePreviewTemplate || $("#tourPackageTemplate").val() || "mit";
      let idx = templates.indexOf(current);
      if (idx === -1) idx = 0;
      let nextIdx = (idx + dir + templates.length) % templates.length;
      this.loadTemplatePreview(templates[nextIdx]);
    },

    loadTemplatePreview: function (templateId) {
      const self = this;
      templateId = templateId || $("#tourPackageTemplate").val() || "mit";
      this.activePreviewTemplate = templateId;

      const templates = ["mit", "standard", "modern", "aleya"];
      let idx = templates.indexOf(templateId);
      if (idx === -1) idx = 0;
      $("#previewTemplateCounter").text(`${idx + 1} / ${templates.length}`);

      const titles = {
        mit: "MIT Multi-Page Proposal",
        standard: "Standard Timeline Proposal",
        modern: "Modern Card Proposal",
        aleya: "Aleya Luxury Proposal"
      };
      $("#previewModalTitle").text(titles[templateId] || "Template Preview");
      $("#btnApplyPreviewedTemplate").data("template-id", templateId);

      // Open modal and show loader immediately
      $("#templatePreviewCanvas").html(`
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 480px; width: 100%; gap: 16px; color: #94a3b8;">
          <div class="v3-btn-spinner" style="width: 2.5rem; height: 2.5rem; border-width: 3px; border-top-color: #6366f1;"></div>
          <span style="font-size: 14px; font-weight: 600; color: #cbd5e1;">Rendering ${titles[templateId] || 'Template'} with your itinerary...</span>
        </div>
      `);
      $("#modalPackageTemplatePreview").addClass("is-active active").show();
      $("#modalPackageTemplatePreview .v3-preview-modal-body").scrollTop(0);

      const title = ($("#packageName").val() || "Tour Package").trim();
      const theme = ($("#travelStyle").val() || "cultural").trim();
      const hotel = ($("#itineraryHotelSelect").val() || "3_star_hotel").trim();
      const pickupCity = ($("#startingCity option:selected").text().trim() || $("#startingCity").val() || "").trim();
      const outstationVehicle = ($("#outstationVehicle").val() || "Volvo").trim();
      const localSightseeingVehicle = ($("#localSighseeignVehicle").val() || "Sedan").trim();

      const defaultOverview = "Experience an unforgettable travel journey featuring handpicked accommodations, curated sightseeing highlights, seamless point-to-point private transfers, and authentic cultural experiences tailored for comfort and memorable moments.";
      const defaultInclusions = "Accommodation in selected category hotels\nDaily buffet breakfast\nPrivate AC vehicle for all transfers and sightseeing\nDriver allowances, toll tax, fuel charges, and parking fees\nAll current applicable government taxes";
      const defaultExclusions = "Airfare or train fare\nPersonal expenses such as laundry, telephone calls, tips, and minibar\nEntry fees to monuments, adventure activity charges, and guide services\nAnything not specifically mentioned in inclusions";

      const overview = ($("#itineraryOverviewField").val() || "").trim() || defaultOverview;
      const inclusions = ($("#itineraryInclusionField").val() || "").trim() || defaultInclusions;
      const exclusions = ($("#itineraryExclusionField").val() || "").trim() || defaultExclusions;

      // Resolve destination stops and night stay sequence
      const destinationStops = [];
      $("#destinationRowsContainer .v3-destination-row").each(function () {
        let cName = $(this).find(".v3-city-select option:selected").text().trim() || $(this).find(".v3-city-select").val() || "";
        if (cName.indexOf(",") !== -1) {
          cName = cName.split(",")[0].trim();
        }
        if (cName.toLowerCase().includes("select") || cName.toLowerCase().includes("search")) {
          cName = "";
        }
        const nights = parseInt($(this).find("input[name='duration[]']").val() || "1", 10);
        if (cName && nights > 0) {
          destinationStops.push({ city: cName, nights: nights });
        }
      });

      const nightStays = [];
      destinationStops.forEach(function (stop) {
        for (let n = 0; n < stop.nights; n++) {
          nightStays.push(stop.city);
        }
      });

      const primaryDestination = destinationStops.length > 0 ? destinationStops.map(function (s) { return s.city; }).join(" - ") : (pickupCity || "Scenic Destination");

      const days = [];
      $("#itineraryDaysCardsContainer .v3-day-card").each(function (i) {
        const dayNum = i + 1;
        const dayTitle = $(this).find(".v3-day-title-input, input[name='dayTitle[]']").val() || `Day ${dayNum}`;
        const dayStayCity = $(this).find(".v3-day-card-final-stay, .v3-day-card-city-select, .v3-day-stay-select, input[name='dayStay[]']").val() || (i < nightStays.length ? nightStays[i] : "");
        const dayContent = $(this).find(".v3-day-textarea, textarea[name='dayContent[]']").val() || "";
        days.push({
          day: dayTitle,
          title: dayTitle,
          overview: dayContent,
          description: dayContent,
          stay: dayStayCity,
          stay_city: dayStayCity
        });
      });

      // If cards are not rendered yet, supply sample/pre-built days so preview always works
      if (days.length === 0) {
        if (self.currentStepData && Array.isArray(self.currentStepData.days) && self.currentStepData.days.length > 0) {
          self.currentStepData.days.forEach(function (d, i) {
            const num = i + 1;
            const stayCity = d.stayCity || (i < nightStays.length ? nightStays[i] : (pickupCity || primaryDestination));
            days.push({
              day: `Day ${num}: Exploration in ${stayCity}`,
              title: `Day ${num}: Exploration in ${stayCity}`,
              overview: d.notes || `Arrival, transfer and scenic sightseeing activities in ${stayCity}.`,
              description: d.notes || `Arrival, transfer and scenic sightseeing activities in ${stayCity}.`,
              stay: stayCity,
              stay_city: stayCity
            });
          });
        } else {
          const totalDays = parseInt($("#statTotalDays").text(), 10) || (destinationStops.reduce(function (sum, s) { return sum + s.nights; }, 0) + 1) || 3;
          for (let d = 1; d <= totalDays; d++) {
            const stayCity = (d <= nightStays.length) ? nightStays[d - 1] : (pickupCity || primaryDestination);
            days.push({
              day: `Day ${d}: Sightseeing in ${stayCity}`,
              title: `Day ${d}: Sightseeing in ${stayCity}`,
              overview: `Guided tour, attraction visits, local exploration and comfortable overnight stay in ${stayCity}.`,
              description: `Guided tour, attraction visits, local exploration and comfortable overnight stay in ${stayCity}.`,
              stay: stayCity,
              stay_city: stayCity
            });
          }
        }
      }

      const totalNightsCount = parseInt($("#itineraryDuration").val(), 10) || parseInt($("#statTotalNights").text(), 10) || (days.length > 1 ? (days.length - 1) : 1);

      const payload = {
        id: 0,
        template: templateId,
        format: "json",
        title: title,
        theme: theme,
        hotel: hotel,
        city: primaryDestination,
        duration: totalNightsCount,
        outstationVehicle: outstationVehicle,
        localSightseeingVehicle: localSightseeingVehicle,
        coverImage: "",
        overview: overview,
        inclusions: inclusions,
        exclusions: exclusions,
        days: days
      };

      $.ajax({
        url: "/api/v3/packages/render/",
        method: "POST",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(payload),
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.html) {
            const iframe = document.createElement("iframe");
            iframe.id = "previewTemplateIframe";
            iframe.style.width = "100%";
            iframe.style.height = "100%";
            iframe.style.minHeight = "650px";
            iframe.style.border = "none";
            iframe.style.display = "block";
            iframe.style.background = "#475569";

            $("#templatePreviewCanvas").empty().append(iframe);

            try {
              const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
              iframeDoc.open();
              iframeDoc.write(res.html);
              iframeDoc.close();
            } catch (err) {
              console.error("[Template Preview Iframe write error]:", err);
              iframe.srcdoc = res.html;
            }
          } else {
            $("#templatePreviewCanvas").html(`
              <div style="padding: 40px; text-align: center; color: #f87171;">
                <i class="fi fi-rr-cross-circle" style="font-size: 36px; display: block; margin-bottom: 12px;"></i>
                <p style="font-size: 15px; font-weight: 600;">Unable to render template preview.</p>
              </div>
            `);
          }
        },
        error: function (xhr) {
          console.error("[Template Preview Render Error]:", xhr);
          $("#templatePreviewCanvas").html(`
            <div style="padding: 40px; text-align: center; color: #f87171;">
              <i class="fi fi-rr-cross-circle" style="font-size: 36px; display: block; margin-bottom: 12px;"></i>
              <p style="font-size: 15px; font-weight: 600;">Failed to load template preview.</p>
            </div>
          `);
        }
      });
    },

    resetForm: function () {
      const $form = $("#addDomesticPackageForm, #addInternationalPackageForm");
      if ($form.length && $form[0]) {
        $form[0].reset();
      }
      $("#destinationRowsContainer .v3-destination-row:not(:first)").remove();
      this.refreshDestinationIndices();
      $("#startingCity").val("").trigger("change");
      $("#endCity").val("").trigger("change");
      $("#sameAsPickupToggle").prop("checked", true).trigger("change");
      $("#destinationRowsContainer .v3-city-select").val("").trigger("change");
      $("#destinationRowsContainer input[name='duration[]']").val("2");
      $("#travelStyle").val("cultural").trigger("change");
      $("input[name='meals[]']").prop("checked", false);
      $(".v3-meal-card").removeClass("is-selected");
      $(".v3-meal-preset-btn").removeClass("active");
      $(".v3-meal-preset-btn[data-preset='none']").addClass("active");
      this.fetchVehicles();
      this.updateTotalDuration();
      this.updateLivePreview();
      $(".is-invalid").removeClass("is-invalid");
    },

    notify: function (type, message, timer) {
      if (typeof window.popUpMessage === "function") {
        window.popUpMessage("center", message, type);
      } else if (typeof swal !== "undefined") {
        swal({
          title: type === "success" ? "Success!" : "Notice",
          text: message,
          type: type,
          timer: timer || 3000,
          showConfirmButton: !timer
        });
      } else {
        alert(message);
      }
    }
  };

  window.V3PackageAdd = window.V3PackageDomesticAdd;

  function selfEscape(str) {
    if (!str) return "";
    return $("<div>").text(str).html();
  }

  function capitalize(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  $(document).ready(function () {
    window.V3PackageDomesticAdd.init();
  });
});
