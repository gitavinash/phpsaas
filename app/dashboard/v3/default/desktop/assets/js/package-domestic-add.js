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
      $(document).on("change", "#startingCity", function () {
        const isSame = $("#sameAsPickupToggle").is(":checked");
        if (isSame) {
          self.syncDropOffWithPickup();
        }
        self.updateLivePreview();
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
        self.updateLivePreview();
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
        $("#modalPackageTemplatePreview").removeClass("is-active");
        $("#templatePreviewCanvas").empty();
        self.notify("success", `Selected ${templateId.toUpperCase()} template layout.`, 2000);
      });

      // Close Preview Modal
      $(document).on("click", "#btnCloseTemplatePreviewModal", function (e) {
        e.preventDefault();
        $("#modalPackageTemplatePreview").removeClass("is-active");
        $("#templatePreviewCanvas").empty();
      });

      $(document).on("click", "#modalPackageTemplatePreview", function (e) {
        if (e.target === this) {
          $("#modalPackageTemplatePreview").removeClass("is-active");
          $("#templatePreviewCanvas").empty();
        }
      });

      // Escape key to close modal
      $(document).on("keydown", function (e) {
        if ($("#modalPackageTemplatePreview").hasClass("is-active")) {
          if (e.key === "Escape" || e.keyCode === 27) {
            $("#modalPackageTemplatePreview").removeClass("is-active");
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
          allowClear: true,
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
            return item.text || item.city || item.id || "";
          }
        }).on("change", function () {
          self.updateLivePreview();
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

    updateLivePreview: function () {
      const name = $("#packageName").val().trim() || "Untitled Domestic Package";
      const travelStyle = $("#travelStyle").val() || "Cultural";
      const startingCity = $("#startingCity").find("option:selected").text().trim() || $("#startingCity").val() || "Pickup Point";
      const isSameAsPickup = $("#sameAsPickupToggle").is(":checked");
      const endCity = isSameAsPickup
        ? startingCity
        : $("#endCity").find("option:selected").text().trim() || $("#endCity").val() || "Drop-off Point";

      const outstation = $("#outstationVehicle").val() || "Not Assigned";
      const local = $("#localSighseeignVehicle").val() || "Not Assigned";

      const destinations = [];
      $(".v3-destination-row").each(function () {
        const destCity = $(this).find(".v3-city-select option:selected").text().trim() || $(this).find(".v3-city-select").val() || "Destination";
        const duration = $(this).find("input[name='duration[]']").val() || "1";
        destinations.push({ city: destCity, nights: duration });
      });

      $("#summaryPackageTitle").text(name);
      $("#summaryTravelStyleBadge").html(`<i class="fi fi-rr-compass"></i> ${capitalize(travelStyle)}`);
      $("#summaryOutstationVehicle").text(outstation);
      $("#summaryLocalVehicle").text(local);

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
      const packageName = ($("#packageName").val() || "").trim();
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
          duration: durations
        })
      };

      // Console log all parameters before making API request
      console.log("%c=== [V3 Domestic Tour Package AI Generator] ===", "color: #4f46e5; font-size: 14px; font-weight: bold;");
      console.log("%cSubmitting Parameters to /api/v3/itinerary/generate.php:", "color: #047857; font-weight: 600;");
      console.table({
        "Package Title": packageName,
        "Travel Style": travelStyle,
        "Pickup City": startingCity,
        "Drop-off City": endCity,
        "Destination Stops": destinations.join(" -> "),
        "Stay Durations (Nights)": durations.join(", "),
        "Total Nights": durations.reduce(function(a, b) { return a + b; }, 0),
        "Total Days": durations.reduce(function(a, b) { return a + b; }, 0) + 1,
        "Outstation Vehicle": outstationVehicle,
        "Local Sightseeing Vehicle": localSighseeignVehicle
      });
      console.log("Full Payload Object:", self.currentStepData);

      // Switch to Step 2 & Trigger In-Page AI generation
      self.goToStep(2);
      self.generateAIItinerary();
    },

    goToStep: function (stepNumber) {
      const $stepper = $(".v3-stepper-container");

      if (stepNumber === 1) {
        $("#step2ItineraryContainer").hide();
        $("#step1ViewContainer").fadeIn(200);

        $stepper.find(".v3-step-item:eq(0)").removeClass("completed").addClass("active");
        $stepper.find(".v3-step-item:eq(1)").removeClass("active completed").addClass("pending");
        $stepper.find(".v3-step-item:eq(2)").removeClass("active completed").addClass("pending");

        // Scroll up smoothly
        const $scrollTarget = $("#packageDomesticAddApp, #packageInternationalAddApp").first();
        if ($scrollTarget.length) {
          $("html, body").animate({ scrollTop: $scrollTarget.offset().top - 20 }, 300);
        }
      } else if (stepNumber === 2) {
        $("#step1ViewContainer").hide();
        $("#step2ItineraryContainer").fadeIn(200);

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
      $("#itineraryPreviewContent").hide();
      $("#itineraryErrorState").hide();
      $("#itineraryLoadingState").fadeIn(200);

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
        data: self.currentStepData,
        dataType: "json",
        timeout: 60000,
        success: function (res) {
          clearInterval(self.loadingInterval);
          console.log("%c=== [V3 Domestic Tour Package AI Generator Response] ===", "color: #047857; font-size: 13px; font-weight: bold;");
          console.log(res);

          if (res && res.status === "success" && res.data) {
            $("#itineraryLoadingState").hide();
            self.renderItineraryPreview(res.data);
          } else {
            $("#itineraryLoadingState").hide();
            $("#aiErrorMessage").text((res && res.msg) || "Could not generate itinerary. Please retry.");
            $("#itineraryErrorState").fadeIn(200);
          }
        },
        error: function (xhr) {
          clearInterval(self.loadingInterval);
          console.error("%c[AI Generator Error Response]:", "color: #ef4444; font-weight: bold;", xhr);
          $("#itineraryLoadingState").hide();
          const errText = (xhr.responseJSON && xhr.responseJSON.msg) || "Network or server timeout occurred while generating AI itinerary.";
          $("#aiErrorMessage").text(errText);
          $("#itineraryErrorState").fadeIn(200);
        }
      });
    },

    renderItineraryPreview: function (data) {
      const self = this;

      // 1. Populate Hero Header
      $("#previewHeroTitle").text(data.packageName);

      const routePills = [];
      routePills.push(`<span class="v3-pill-badge duration"><i class="fi fi-rr-moon"></i> ${data.totalNights} Nights / <i class="fi fi-rr-sun"></i> ${data.totalDays} Days</span>`);
      routePills.push(`<span class="v3-pill-badge style"><i class="fi fi-rr-compass"></i> ${capitalize(data.travelStyle)}</span>`);

      const routePath = [data.startingCity, ...(data.destinationCity || []), data.endCity].filter(Boolean).join(" → ");
      routePills.push(`<span class="v3-pill-badge route"><i class="fi fi-rr-route"></i> ${selfEscape(routePath)}</span>`);
      routePills.push(`<span class="v3-pill-badge vehicle"><i class="fi fi-rr-car-alt"></i> Outstation: ${selfEscape(data.outstationVehicle)}</span>`);
      routePills.push(`<span class="v3-pill-badge vehicle"><i class="fi fi-rr-taxi"></i> Sightseeing: ${selfEscape(data.localSightseeingVehicle)}</span>`);

      $("#previewHeroPills").html(routePills.join(""));

      // 2. Populate Hidden Backend Form Fields
      $("#itineraryPackageName").val(data.packageName);
      $("#itineraryTravelStyle").val(data.travelStyle);
      $("#itineraryDuration").val(data.totalNights);
      $("#itineraryOutstationVehicle").val(data.outstationVehicle);
      $("#itineraryLocalVehicle").val(data.localSightseeingVehicle);
      $("#itineraryLocation").val(Array.isArray(data.destinationCity) ? data.destinationCity.join(",") : data.destinationCity);
      $("#itineraryStartCity").val(data.startingCity);
      $("#itineraryEndCity").val(data.endCity);

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

      if (Array.isArray(data.durations)) {
        data.durations.forEach(function (dayObj, idx) {
          const dayNum = idx + 1;
          const dayTitle = dayObj.day || `Day ${dayNum} - Sightseeing & Activities`;
          const dayDetails = dayObj.details || "";

          const dayCardHtml = `
            <div class="v3-day-card" data-day="${dayNum}">
              <div class="v3-day-card-header">
                <span class="v3-day-badge"><i class="fi fi-rr-calendar"></i> Day ${dayNum}</span>
                <div class="v3-day-title-wrap">
                  <input type="text" class="v3-form-control v3-day-title-input" name="dayTitle[]" value="${selfEscape(dayTitle)}" placeholder="Day ${dayNum} Title..." required />
                </div>
              </div>
              <div class="v3-day-card-body">
                <textarea class="v3-form-control v3-form-textarea v3-day-textarea" name="dayContent[]" rows="4" required placeholder="Describe day ${dayNum} activities, transfers and sightseeing...">${selfEscape(dayDetails)}</textarea>
              </div>
            </div>
          `;
          $daysContainer.append(dayCardHtml);
        });
      }

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
      $("#itineraryPreviewContent").fadeIn(250);
      self.initCitySelect2($("#itineraryPreviewContent"));
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

      const title = ($("#packageName").val() || "Tour Package").trim();
      const theme = ($("#travelStyle").val() || "cultural").trim();
      const hotel = ($("#itineraryHotelSelect").val() || "3_star_hotel").trim();
      const pickupCity = ($("#startingCity").val() || "").trim();
      const outstationVehicle = ($("#outstationVehicle").val() || "Volvo").trim();
      const localSightseeingVehicle = ($("#localSighseeignVehicle").val() || "Sedan").trim();
      const overview = ($("#itineraryOverviewField").val() || "").trim();
      const inclusions = ($("#itineraryInclusionField").val() || "").trim();
      const exclusions = ($("#itineraryExclusionField").val() || "").trim();

      // Resolve destination stops and night stay sequence
      const destinationStops = [];
      $("#destinationRowsContainer .v3-destination-row").each(function () {
        let cName = $(this).find(".v3-city-select").val() || "";
        if (cName.indexOf(",") !== -1) {
          cName = cName.split(",")[0].trim();
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
      const totalCards = $("#itineraryDaysCardsContainer .v3-day-card").length;
      $("#itineraryDaysCardsContainer .v3-day-card").each(function (i) {
        const dayNum = i + 1;
        const dayTitle = $(this).find(".v3-day-title-input").val() || `Day ${dayNum}`;
        const dayContent = $(this).find(".v3-day-textarea").val() || "";
        // Day overnight stay: assign destination stop if within total nights, otherwise leave empty on departure day
        const dayStayCity = i < nightStays.length ? nightStays[i] : "";
        days.push({
          day: dayTitle,
          overview: dayContent,
          stay: dayStayCity
        });
      });

      // Display loader in preview modal
      $("#templatePreviewCanvas").html(`
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 480px; width: 100%; gap: 16px; color: #94a3b8;">
          <div class="v3-btn-spinner" style="width: 2.5rem; height: 2.5rem; border-width: 3px; border-top-color: #6366f1;"></div>
          <span style="font-size: 14px; font-weight: 600; color: #64748b;">Rendering ${titles[templateId] || 'Template'} with your itinerary...</span>
        </div>
      `);

      $("#modalPackageTemplatePreview").addClass("is-active");
      $("#modalPackageTemplatePreview .v3-preview-modal-body").scrollTop(0);

      const payload = {
        id: 0,
        template: templateId,
        format: "json",
        title: title,
        theme: theme,
        hotel: hotel,
        city: primaryDestination,
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
            iframe.style.background = "#f8fafc";

            $("#templatePreviewCanvas").empty().append(iframe);

            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(res.html);
            iframeDoc.close();
          } else {
            $("#templatePreviewCanvas").html(`
              <div style="padding: 40px; text-align: center; color: #ef4444;">
                <i class="fi fi-rr-cross-circle" style="font-size: 36px; display: block; margin-bottom: 12px;"></i>
                <p style="font-size: 15px; font-weight: 600;">Unable to render template preview.</p>
              </div>
            `);
          }
        },
        error: function (xhr) {
          $("#templatePreviewCanvas").html(`
            <div style="padding: 40px; text-align: center; color: #ef4444;">
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
