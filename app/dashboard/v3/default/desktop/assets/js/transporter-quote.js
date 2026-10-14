/**
 * Transporter Quotation Builder & Billing Terms Manager
 * Path: app/dashboard/v3/default/desktop/assets/js/transporter-quote.js
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

    var TransporterQuoteApp = {
        vehiclesCache: [],
        currentLeadData: null,
        activeCurrencySymbol: "₹",
        defaultAdvancePercent: 25,
        defaultRules: [],
        isConfirmingClose: false,
        eventsBound: false,
        isSubmitting: false,
        vehiclesLoaded: false,
        vehiclesLoading: false,
        termsLoaded: false,
        termsLoading: false,
        lastCreatedQuote: null,
        lastSavedPdfUrl: null,
        lastSavedPdfFullUrl: null,
        lastSavedPdfFileName: null,
        lastCustomerMobile: "",
        isGeneratingPdf: false,
        pdfGenerationCallbacks: [],

        init: function () {
            if (!$("#modalSendTransporterQuotation").length) {
                return;
            }
            this.bindEvents();
            this.initSelect2Dropdowns();
            this.initDatePicker();
        },

        bindEvents: function () {
            if (this.eventsBound) return;
            this.eventsBound = true;
            var self = this;

            // Trigger to open Transporter Quotation Modal
            $(document).on("click", "#btnHeroSendQuotation, #btnSideSendQuotation", function (e) {
                e.preventDefault();
                self.openQuoteModal();
            });

            // Trigger datepicker open when calendar icon is clicked
            $(document).on("click", ".tq-input-with-icon .tq-input-icon", function () {
                $(this).siblings(".v3-single-datepicker-input").focus().click();
            });

            // When KM Running (Daily Limit) changes, update minimum Total Running KM
            $(document).on("input change", "#tqKmRunning", function () {
                var dailyLimit = parseFloat($(this).val()) || 0;
                if (dailyLimit > 0) {
                    $("#tqTotalRunningKm").attr("min", dailyLimit);
                    var currentTotalKm = parseFloat($("#tqTotalRunningKm").val());
                    if (isNaN(currentTotalKm) || currentTotalKm < dailyLimit) {
                        $("#tqTotalRunningKm").val(dailyLimit);
                    }
                }
                self.recalculateTotalFromKm();
            });

            // Reactive Billing Calculations from Total Running KM and Extra KM Rate
            $(document).on("input change", "#tqTotalRunningKm, #tqExtraKmCharge", function () {
                self.recalculateTotalFromKm();
            });

            // Enforce minimum on blur / change of Total Running KM
            $(document).on("blur change", "#tqTotalRunningKm", function () {
                var dailyLimit = parseFloat($("#tqKmRunning").val()) || 0;
                var currentTotalKm = parseFloat($(this).val());
                if (dailyLimit > 0 && (isNaN(currentTotalKm) || currentTotalKm < dailyLimit)) {
                    $(this).val(dailyLimit);
                    self.recalculateTotalFromKm();
                }
            });

            // Reactive Billing Calculations from Total Cost or Advance %
            $(document).on("input change", "#tqTotalCost, #tqAdvancePercent", function () {
                self.recalculateBilling();
            });

            // Change Vehicle Type filter (support both native change and Select2 events)
            $(document).on("change select2:select", "#tqVehicleType", function () {
                var selectedCat = $(this).val();
                self.pendingVehicleType = selectedCat;
                self.filterVehicleModels(selectedCat);
            });

            // Add Custom Rule (ensuring only 1 rule added per click)
            $(document).on("click", "#tqBtnAddRule", function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (self.isAddingRule) return;
                self.isAddingRule = true;
                setTimeout(function () {
                    self.isAddingRule = false;
                }, 300);

                self.appendCustomRuleRow({
                    id: "rule_" + Date.now() + "_" + Math.floor(Math.random() * 1000),
                    title: "",
                    description: "",
                    enabled: true
                }, true);
            });

            // Delete Custom Rule
            $(document).on("click", ".tq-rule-delete-btn", function (e) {
                e.preventDefault();
                $(this).closest(".tq-rule-item").fadeOut(200, function () {
                    $(this).remove();
                });
            });


            // Toggle Custom Rule Checkbox
            $(document).on("change", ".tq-rule-item-check", function () {
                var $item = $(this).closest(".tq-rule-item");
                if ($(this).is(":checked")) {
                    $item.removeClass("is-disabled");
                } else {
                    $item.addClass("is-disabled");
                }
            });

            // Save Terms as Business Default
            $(document).on("click", "#tqBtnSaveDefaultTerms", function (e) {
                e.preventDefault();
                self.saveDefaultTerms();
            });

            // Submit Quotation
            $(document).on("click", "#tqBtnSubmitQuote", function (e) {
                e.preventDefault();
                self.submitQuotation();
            });

            // Copy WhatsApp Text
            $(document).on("click", "#tqBtnCopyWaText", function (e) {
                e.preventDefault();
                var text = $("#tqWaPreviewBox").text();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        self.notify("Quotation text copied to clipboard!", "success");
                    });
                } else {
                    var $temp = $("<textarea>");
                    $("body").append($temp);
                    $temp.val(text).select();
                    document.execCommand("copy");
                    $temp.remove();
                    self.notify("Quotation text copied to clipboard!", "success");
                }
            });

            // Close / Cancel Transporter Quotation Modal Trigger with confirmation
            $(document).on("click", "#modalSendTransporterQuotation .v3-modal-close-trigger, #tqBtnCancelQuote", function (e) {
                e.preventDefault();
                self.confirmCloseModal();
            });

            // Preview Modal Actions
            $(document).on("click", "#tqBtnPreviewBack", function (e) {
                e.preventDefault();
                $("#modalTransporterQuotePreview").removeClass("is-active");
                $("#modalSendTransporterQuotation").addClass("is-active");
            });

            $(document).on("click", "#tqBtnPreviewDownloadPdf", function (e) {
                e.preventDefault();
                if (self.lastSavedPdfFullUrl) {
                    window.open(self.lastSavedPdfFullUrl, "_blank");
                    return;
                }
                var iframe = document.getElementById("tqPreviewIframe");
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } else {
                    window.print();
                }
            });

            $(document).on("click", "#tqBtnPreviewSendQuote", function (e) {
                e.preventDefault();
                self.sendQuotationViaWhatsApp(false);
            });

            $(document).on("click", "#tqBtnSendWaDirect", function (e) {
                e.preventDefault();
                self.sendQuotationViaWhatsApp(true);
            });

            // Bidirectional sync for Transporter Delivery Channels (Email & WhatsApp)
            $(document).off("change.v3TqDeliveryEmail", "#toggleTqSendEmail, #toggleTqSendEmailPreview")
                .on("change.v3TqDeliveryEmail", "#toggleTqSendEmail, #toggleTqSendEmailPreview", function () {
                    var isChecked = $(this).is(":checked");
                    $("#toggleTqSendEmail, #toggleTqSendEmailPreview").prop("checked", isChecked);
                    $("#itemTqSendEmail, #itemTqSendEmailPreview").toggleClass("is-email-checked", isChecked);
                });

            $(document).off("change.v3TqDeliveryWhatsApp", "#toggleTqSendWhatsApp, #toggleTqSendWhatsAppPreview")
                .on("change.v3TqDeliveryWhatsApp", "#toggleTqSendWhatsApp, #toggleTqSendWhatsAppPreview", function () {
                    var isChecked = $(this).is(":checked");
                    $("#toggleTqSendWhatsApp, #toggleTqSendWhatsAppPreview").prop("checked", isChecked);
                    $("#itemTqSendWhatsApp, #itemTqSendWhatsAppPreview").toggleClass("is-whatsapp-checked", isChecked);
                });

            $(document).on("click", "#modalTransporterQuotePreview .v3-modal-close-trigger", function (e) {
                e.preventDefault();
                $("#modalTransporterQuotePreview").removeClass("is-active");
            });

            // Close Share Modal directly
            $(document).on("click", "#modalTransporterQuoteShare .v3-modal-close-trigger", function (e) {
                e.preventDefault();
                $("#modalTransporterQuoteShare").removeClass("is-active");
            });

            // Intercept ESC key when preview or quotation modal is open
            $(document).on("keydown", function (e) {
                if (e.key === "Escape" || e.keyCode === 27) {
                    if ($("#modalTransporterQuotePreview").hasClass("is-active")) {
                        e.preventDefault();
                        e.stopPropagation();
                        $("#modalTransporterQuotePreview").removeClass("is-active");
                        return;
                    }
                    if ($("#modalSendTransporterQuotation").hasClass("is-active")) {
                        var swalObj = (typeof window.Swal !== "undefined")
                            ? window.Swal
                            : ((typeof Swal !== "undefined") ? Swal : null);
                        var isSwalVisible = (swalObj && typeof swalObj.isVisible === "function" && swalObj.isVisible()) || $(".swal2-container").is(":visible");
                        if (isSwalVisible) {
                            return;
                        }
                        e.preventDefault();
                        e.stopPropagation();
                        self.confirmCloseModal();
                    }
                }
            });
        },

        confirmCloseModal: function (onConfirm, onCancel) {
            var self = this;
            if (self.isConfirmingClose) {
                if (typeof onCancel === "function") onCancel();
                return;
            }
            self.isConfirmingClose = true;

            var swalObj = (typeof window.Swal !== "undefined")
                ? window.Swal
                : ((typeof Swal !== "undefined") ? Swal : null);
            var swalFn = (swalObj && typeof swalObj.fire === "function")
                ? swalObj.fire.bind(swalObj)
                : (typeof swalObj === "function" ? swalObj : null);

            var doConfirm = function () {
                self.isConfirmingClose = false;
                if (typeof onConfirm === "function") {
                    onConfirm();
                } else {
                    $("#modalSendTransporterQuotation").removeClass("is-active");
                }
            };

            var doCancel = function () {
                self.isConfirmingClose = false;
                if (typeof onCancel === "function") {
                    onCancel();
                }
            };

            if (swalFn) {
                swalFn({
                    title: "Discard Quotation?",
                    text: "Are you sure you want to close without saving this transporter quote?",
                    type: "warning",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#64748b",
                    confirmButtonText: "Yes, Discard",
                    cancelButtonText: "Keep Editing",
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result && (result.isConfirmed || result.value)) {
                        doConfirm();
                    } else {
                        doCancel();
                    }
                });
            } else {
                if (window.confirm("Are you sure you want to close without saving this transporter quote?")) {
                    doConfirm();
                } else {
                    doCancel();
                }
            }
        },

        initSelect2Dropdowns: function () {
            if (!$.fn.select2) return;
            var $modal = $("#modalSendTransporterQuotation");

            if (!$("#tqVehicleType").hasClass("select2-hidden-accessible")) {
                $("#tqVehicleType").select2({
                    dropdownParent: $modal,
                    placeholder: "All Categories",
                    width: "100%"
                });
            }

            if (!$("#tqVehicleModel").hasClass("select2-hidden-accessible")) {
                $("#tqVehicleModel").select2({
                    dropdownParent: $modal,
                    placeholder: "Choose vehicle model...",
                    width: "100%"
                });
            }
        },

        initDatePicker: function () {
            var $input = $("#tqTravelDate");
            if (!$input.length || typeof $.fn.daterangepicker === "undefined") return;

            if ($input.data("daterangepicker")) {
                $input.data("daterangepicker").remove();
            }

            var initialVal = $input.val();
            var startMoment = (typeof moment !== "undefined") ? moment().startOf("day") : undefined;
            if (initialVal && typeof moment !== "undefined" && moment(initialVal, "YYYY-MM-DD", true).isValid()) {
                var parsed = moment(initialVal, "YYYY-MM-DD");
                if (!parsed.isBefore(moment().startOf("day"))) {
                    startMoment = parsed;
                }
            }

            var curYear = (typeof moment !== "undefined") ? moment().year() : 2026;

            $input.daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: (typeof moment !== "undefined") ? moment().startOf("day") : false,
                minYear: curYear,
                maxYear: curYear + 15,
                autoApply: true,
                autoUpdateInput: false,
                startDate: startMoment,
                opens: "left",
                drops: "down",
                locale: {
                    format: "YYYY-MM-DD"
                }
            }, function (chosen) {
                $input.val(chosen.format("YYYY-MM-DD")).trigger("change");
            });

            $input.off("apply.daterangepicker").on("apply.daterangepicker", function (ev, picker) {
                $(this).val(picker.startDate.format("YYYY-MM-DD")).trigger("change");
            });

            $input.off("show.daterangepicker").on("show.daterangepicker", function (ev, picker) {
                if (picker && picker.container) {
                    picker.container.addClass("single");
                    picker.container.find(".drp-calendar.right").css("display", "none");
                    picker.container.find(".ranges").css("display", "none");
                    picker.container.find(".drp-buttons").css("display", "none");
                    picker.container.css("z-index", "100060");
                }
            });

            var pickerObj = $input.data("daterangepicker");
            if (pickerObj && pickerObj.container) {
                pickerObj.container.hide().css("display", "none");
            }
        },

        openQuoteModal: function () {
            var self = this;
            if (!self.vehiclesLoaded && (!self.vehiclesCache || self.vehiclesCache.length === 0)) {
                self.preloadVehicles();
            }
            if (!self.termsLoaded) {
                self.loadDefaultTerms();
            }
            var lead = (window.V3LeadView && window.V3LeadView.leadData) 
                ? window.V3LeadView.leadData 
                : ((window.leadViewApp && window.leadViewApp.leadData) ? window.leadViewApp.leadData : {});
            self.currentLeadData = lead;

            // Resolve Currency Symbol from configured app
            var currencySym = lead.currency_symbol || (defined("CURRENCY_SYMBOL") ? CURRENCY_SYMBOL : "₹");
            if (currencySym) {
                self.activeCurrencySymbol = currencySym;
                $(".tq-currency-symbol").text(currencySym);
            }

            // Resolve Advance Payment % from configured app
            if (lead.advance_payment_percent && !isNaN(lead.advance_payment_percent)) {
                self.defaultAdvancePercent = parseFloat(lead.advance_payment_percent);
            }
            $("#tqAdvancePercent").val(self.defaultAdvancePercent);

            // Pre-fill Customer Section
            var cName = lead.name || ((lead.first_name || "") + " " + (lead.last_name || "")).trim();
            $("#tqCustomerName").val(cName || "");
            $("#tqCustomerMobile").val(lead.mobile || lead.wa_mobile || "");
            $("#tqCustomerEmail").val(lead.email || "");

            var travel = lead.travel_details || {};
            $("#tqCustomerAddress").val(travel.current_location || lead.enquiry_city || "");

            // Pre-fill Travel / Transport Section
            var dest = travel.destination || lead.subject || "";
            if (dest.toLowerCase() === "tour package enquiry" || dest.toLowerCase() === "general enquiry") {
                dest = "";
            }
            $("#tqDestination").val(dest);
            $("#tqPickupLocation").val(travel.pickup_location || "");
            $("#tqDropLocation").val(travel.drop_location || "");

            // Travel Date - Ensure not before today
            var today = (typeof moment !== "undefined") ? moment().format("YYYY-MM-DD") : new Date().toISOString().split("T")[0];
            var tDate = travel.date_of_travel_raw || "";
            if (!tDate && travel.date_of_travel) {
                var parsedDate = new Date(travel.date_of_travel);
                if (!isNaN(parsedDate.getTime())) {
                    tDate = parsedDate.toISOString().split("T")[0];
                }
            }
            if (tDate) {
                if (typeof moment !== "undefined" && moment(tDate, "YYYY-MM-DD", true).isValid()) {
                    if (moment(tDate, "YYYY-MM-DD").isBefore(moment().startOf("day"))) {
                        tDate = today;
                    }
                }
                $("#tqTravelDate").val(tDate);
            } else {
                $("#tqTravelDate").val(today);
            }

            // Initialize / refresh datepicker with minDate = today
            self.initDatePicker();

            var duration = travel.duration_days || 1;
            $("#tqTravelDuration").val(duration);

            // Pre-fill Billing
            var quoteVal = (lead.quotation && lead.quotation.price) ? parseFloat(lead.quotation.price) : 0;
            var details = (lead.quotation && (lead.quotation.details || lead.quotation.content || lead.quotation.parsed_content)) ? (lead.quotation.details || lead.quotation.content || lead.quotation.parsed_content) : {};
            var billing = details.billing || (lead.quotation && lead.quotation.billing) || {};

            var kmDaily = (billing.km_running !== undefined && billing.km_running !== null) ? parseFloat(billing.km_running) : 250;
            var rawTotalRunning = (billing.total_running_km !== undefined && billing.total_running_km !== null && billing.total_running_km !== "") ? parseFloat(billing.total_running_km) : null;
            var totalRunning = (rawTotalRunning !== null && !isNaN(rawTotalRunning) && rawTotalRunning >= kmDaily) ? rawTotalRunning : kmDaily;
            var extraKm = (billing.extra_km_charge !== undefined && billing.extra_km_charge !== null) ? parseFloat(billing.extra_km_charge) : 14;
            var advPercent = (billing.advance_percent !== undefined && billing.advance_percent !== null) ? parseFloat(billing.advance_percent) : self.defaultAdvancePercent;

            $("#tqKmRunning").val(kmDaily);
            $("#tqTotalRunningKm").val(totalRunning).attr("min", kmDaily);
            $("#tqExtraKmCharge").val(extraKm);
            $("#tqAdvancePercent").val(advPercent);

            if (quoteVal > 0) {
                $("#tqTotalCost").val(quoteVal);
            } else if (parseFloat(totalRunning) > 0 && parseFloat(extraKm) > 0) {
                var autoCost = roundToTwo(parseFloat(totalRunning) * parseFloat(extraKm));
                $("#tqTotalCost").val(autoCost > 0 ? autoCost : "");
            } else {
                $("#tqTotalCost").val("");
            }

            self.recalculateBilling();

            // Resolve vehicle and fleet preference from lead details
            var leadVehicle = self.resolveLeadVehicle(lead);
            var targetCategory = leadVehicle.category || "";
            var targetModel = leadVehicle.vehicleId || leadVehicle.model || "";

            // If we have cached vehicles and targetModel but no category yet, try finding category from vehiclesCache
            if (!targetCategory && targetModel && self.vehiclesCache.length > 0) {
                var sModel = String(targetModel).toLowerCase();
                for (var i = 0; i < self.vehiclesCache.length; i++) {
                    var vItem = self.vehiclesCache[i];
                    if (String(vItem.id) === sModel || (vItem.title && (vItem.title.toLowerCase().indexOf(sModel) !== -1 || sModel.indexOf(vItem.title.toLowerCase()) !== -1))) {
                        targetCategory = (vItem.type || "").toLowerCase();
                        break;
                    }
                }
            }

            self.pendingVehicleType = targetCategory;
            self.pendingVehicleModel = targetModel;

            // Initialize and sync Select2 dropdowns
            self.initSelect2Dropdowns();
            $("#tqVehicleType").val(targetCategory || "").trigger("change.select2");
            self.filterVehicleModels(targetCategory, targetModel);

            // Configure Delivery Channels (Email & WhatsApp)
            self.setupTqDeliveryToggles(lead);

            // Open Modal
            $("#modalSendTransporterQuotation").addClass("is-active");
        },

        normalizeVehicleCategory: function (catStr) {
            if (!catStr) return "";
            var s = String(catStr).toLowerCase().trim();
            if (s.indexOf("sedan") !== -1 || s.indexOf("dzire") !== -1 || s.indexOf("etios") !== -1) return "sedan";
            if (s.indexOf("suv") !== -1 || s.indexOf("crysta") !== -1 || s.indexOf("innova") !== -1 || s.indexOf("scorpio") !== -1 || s.indexOf("ertiga") !== -1) return "suv";
            if (s.indexOf("mpv") !== -1 || s.indexOf("marazzo") !== -1 || s.indexOf("carens") !== -1) return "mpv";
            if (s.indexOf("tempo") !== -1 || s.indexOf("traveller") !== -1 || s.indexOf("traveler") !== -1 || s.indexOf("winger") !== -1) return "traveller";
            if (s.indexOf("bus") !== -1 || s.indexOf("coach") !== -1) return "bus";
            if (s.indexOf("hatch") !== -1 || s.indexOf("swift") !== -1 || s.indexOf("wagon") !== -1 || s.indexOf("i10") !== -1) return "hatchback";
            if (s.indexOf("van") !== -1 || s.indexOf("commercial") !== -1 || s.indexOf("lcv") !== -1) return "light_commercial_vehicle";
            return s;
        },

        resolveLeadVehicle: function (lead) {
            var self = this;
            lead = lead || {};
            var travel = lead.travel_details || {};
            var details = (lead.quotation && (lead.quotation.details || lead.quotation.content || lead.quotation.parsed_content)) ? (lead.quotation.details || lead.quotation.content || lead.quotation.parsed_content) : {};
            var qVeh = (lead.quotation && lead.quotation.vehicle) ? lead.quotation.vehicle : (details.vehicle || {});
            var tCust = travel.custom_fields || {};
            var lCust = lead.custom_fields || {};

            var rawType = travel.vehicle_type || qVeh.vehicle_type || qVeh.type || lead.vehicle_type || tCust.vehicle_type || lCust.vehicle_type || "";
            var rawModel = travel.vehicle_model || qVeh.vehicle_title || qVeh.title || qVeh.vehicle_id || lead.vehicle_model || lead.preferred_vehicle || lead.vehicle || tCust.vehicle_model || lCust.vehicle_model || "";

            var vehicleId = qVeh.vehicle_id || (isFinite(rawModel) && parseInt(rawModel, 10) > 0 ? parseInt(rawModel, 10) : null);

            var category = self.normalizeVehicleCategory(rawType);
            if (!category && rawModel) {
                category = self.normalizeVehicleCategory(rawModel);
            }

            return {
                category: category,
                model: rawModel,
                vehicleId: vehicleId
            };
        },

        preloadVehicles: function () {
            var self = this;
            if (self.vehiclesLoading) return;
            self.vehiclesLoading = true;
            $.ajax({
                url: "/api/v3/vehicles/list/",
                type: "GET",
                dataType: "json",
                success: function (res) {
                    self.vehiclesLoading = false;
                    self.vehiclesLoaded = true;
                    if (res && res.status === "success" && Array.isArray(res.data)) {
                        self.vehiclesCache = res.data;
                        var catToFilter = self.pendingVehicleType !== undefined ? self.pendingVehicleType : ($("#tqVehicleType").val() || "");
                        var modelToSelect = self.pendingVehicleModel !== undefined ? self.pendingVehicleModel : "";
                        if (!catToFilter && modelToSelect) {
                            var sM = String(modelToSelect).toLowerCase();
                            for (var k = 0; k < self.vehiclesCache.length; k++) {
                                var vMatch = self.vehiclesCache[k];
                                if (String(vMatch.id) === sM || (vMatch.title && (vMatch.title.toLowerCase().indexOf(sM) !== -1 || sM.indexOf(vMatch.title.toLowerCase()) !== -1))) {
                                    catToFilter = (vMatch.type || "").toLowerCase();
                                    $("#tqVehicleType").val(catToFilter).trigger("change.select2");
                                    break;
                                }
                            }
                        }
                        self.filterVehicleModels(catToFilter, modelToSelect);
                    }
                },
                error: function () {
                    self.vehiclesLoading = false;
                }
            });
        },

        filterVehicleModels: function (category, selectedModelOrId) {
            var self = this;
            var $select = $("#tqVehicleModel");
            $select.empty();
            $select.append('<option value="">-- Choose Vehicle Model --</option>');

            var filtered = self.vehiclesCache;
            if (category && category !== "all") {
                filtered = self.vehiclesCache.filter(function (v) {
                    return (v.type || "").toLowerCase() === category.toLowerCase();
                });
            }

            if (filtered.length === 0 && self.vehiclesCache.length > 0) {
                filtered = self.vehiclesCache; // fallback to all if no direct match
            }

            var targetSearch = selectedModelOrId ? String(selectedModelOrId).toLowerCase().trim() : "";
            var matchedOptionValue = null;

            filtered.forEach(function (v) {
                var label = v.title + (v.seating ? " (" + v.seating + " Seater)" : "");
                var $opt = $("<option></option>")
                    .val(v.id)
                    .text(label)
                    .attr("data-title", v.title)
                    .attr("data-type", v.type || "")
                    .attr("data-seating", v.seating || "");

                if (targetSearch) {
                    var vIdStr = String(v.id).toLowerCase();
                    var vTitleLower = (v.title || "").toLowerCase();
                    if (vIdStr === targetSearch || vTitleLower === targetSearch || vTitleLower.indexOf(targetSearch) !== -1 || targetSearch.indexOf(vTitleLower) !== -1) {
                        $opt.prop("selected", true);
                        matchedOptionValue = v.id;
                    }
                }

                $select.append($opt);
            });

            if (matchedOptionValue) {
                $select.val(matchedOptionValue);
            }

            // Initialize or update Select2
            if ($select.hasClass("select2-hidden-accessible")) {
                $select.trigger("change.select2");
            } else if ($.fn.select2) {
                $select.select2({
                    dropdownParent: $("#modalSendTransporterQuotation"),
                    placeholder: "Choose vehicle model...",
                    width: "100%"
                });
            }
        },

        recalculateTotalFromKm: function () {
            var self = this;
            var dailyLimit = parseFloat($("#tqKmRunning").val()) || 0;
            var rawVal = $("#tqTotalRunningKm").val();
            var totalKm = parseFloat(rawVal);
            var extraKm = parseFloat($("#tqExtraKmCharge").val()) || 0;

            if (dailyLimit > 0) {
                $("#tqTotalRunningKm").attr("min", dailyLimit);
                if (rawVal === "" || isNaN(totalKm) || totalKm < dailyLimit) {
                    totalKm = dailyLimit;
                    $("#tqTotalRunningKm").val(dailyLimit);
                }
            } else {
                if (isNaN(totalKm)) totalKm = 0;
            }

            if (totalKm > 0 && extraKm > 0) {
                var totalCost = roundToTwo(totalKm * extraKm);
                $("#tqTotalCost").val(totalCost);
            } else if (rawVal !== "" && totalKm === 0) {
                $("#tqTotalCost").val(0);
            }
            self.recalculateBilling();
        },

        recalculateBilling: function () {
            var self = this;
            var total = parseFloat($("#tqTotalCost").val()) || 0;
            var advPercent = parseFloat($("#tqAdvancePercent").val()) || 0;

            if (advPercent < 0) advPercent = 0;
            if (advPercent > 100) advPercent = 100;

            var advanceAmount = roundToTwo((total * advPercent) / 100);
            var balanceAmount = Math.max(0, roundToTwo(total - advanceAmount));

            $("#tqAdvanceAmountDisplay").text(self.formatNumber(advanceAmount));
            $("#tqBalanceAmountDisplay").text(self.formatNumber(balanceAmount));
            $("#tqFooterTotalDisplay").text(self.formatNumber(total));
            $("#tqAmountInWordsDisplay").text(self.getIndianCurrencyWords(total));
        },

        loadDefaultTerms: function () {
            var self = this;
            if (self.termsLoading) return;
            self.termsLoading = true;
            $.ajax({
                url: "/api/v3/transporters/terms/",
                type: "GET",
                dataType: "json",
                success: function (res) {
                    self.termsLoading = false;
                    self.termsLoaded = true;
                    if (res && res.status === "success" && res.data) {
                        var d = res.data;
                        // Radio Toggles
                        if (d.parking_charges === "yes") {
                            $("#tqParkingYes").prop("checked", true);
                        } else {
                            $("#tqParkingNo").prop("checked", true);
                        }

                        if (d.garage_to_garage === "no") {
                            $("#tqGarageNo").prop("checked", true);
                        } else {
                            $("#tqGarageYes").prop("checked", true);
                        }

                        if (d.toll_tax === "no") {
                            $("#tqTollNo").prop("checked", true);
                        } else {
                            $("#tqTollYes").prop("checked", true);
                        }

                        if (d.state_tax === "no") {
                            $("#tqStateTaxNo").prop("checked", true);
                        } else {
                            $("#tqStateTaxYes").prop("checked", true);
                        }

                        if (d.driver_allowance === "no") {
                            $("#tqDriverNo").prop("checked", true);
                        } else {
                            $("#tqDriverYes").prop("checked", true);
                        }

                        // Custom Rules
                        if (Array.isArray(d.custom_rules)) {
                            self.defaultRules = d.custom_rules;
                            self.renderCustomRules(d.custom_rules);
                        }
                    }
                },
                error: function () {
                    self.termsLoading = false;
                }
            });
        },

        renderCustomRules: function (rules) {
            var self = this;
            var $list = $("#tqCustomRulesList");
            $list.empty();
            if (!Array.isArray(rules) || rules.length === 0) return;

            rules.forEach(function (r) {
                self.appendCustomRuleRow(r, false);
            });
        },

        appendCustomRuleRow: function (rule, focusNew) {
            var id = rule.id || ("rule_" + Date.now());
            var isChecked = (rule.enabled !== false);
            var title = rule.title || "";
            var desc = rule.description || "";

            var html = [
                '<div class="tq-rule-item ' + (!isChecked ? 'is-disabled' : '') + '" data-rule-id="' + id + '">',
                '    <input type="checkbox" class="tq-rule-item-check" ' + (isChecked ? 'checked' : '') + ' title="Toggle rule inclusion">',
                '    <div class="tq-rule-item-body">',
                '        <input type="text" class="tq-rule-item-title" placeholder="Rule Title (e.g. Daily KM Limit)" value="' + escapeHtml(title) + '">',
                '        <input type="text" class="tq-rule-item-desc" placeholder="Rule description or condition..." value="' + escapeHtml(desc) + '">',
                '    </div>',
                '    <button type="button" class="tq-rule-delete-btn" title="Delete rule">✕</button>',
                '</div>'
            ].join("\n");

            var $row = $(html);
            $("#tqCustomRulesList").append($row);

            if (focusNew) {
                $row.find(".tq-rule-item-title").focus();
            }
        },

        collectCustomRules: function () {
            var rules = [];
            $("#tqCustomRulesList .tq-rule-item").each(function () {
                var $item = $(this);
                var ruleId = $item.data("rule-id") || ("rule_" + Date.now());
                var enabled = $item.find(".tq-rule-item-check").is(":checked");
                var title = $.trim($item.find(".tq-rule-item-title").val());
                var desc = $.trim($item.find(".tq-rule-item-desc").val());

                if (title) {
                    rules.push({
                        id: ruleId,
                        title: title,
                        description: desc,
                        enabled: enabled
                    });
                }
            });
            return rules;
        },

        saveDefaultTerms: function () {
            var self = this;
            var payload = {
                parking_charges: $('input[name="tq_term_parking"]:checked').val() || "no",
                garage_to_garage: $('input[name="tq_term_garage"]:checked').val() || "yes",
                toll_tax: $('input[name="tq_term_toll"]:checked').val() || "yes",
                state_tax: $('input[name="tq_term_state_tax"]:checked').val() || "yes",
                driver_allowance: $('input[name="tq_term_driver"]:checked').val() || "yes",
                default_km_running: parseFloat($("#tqKmRunning").val()) || 250,
                custom_rules: self.collectCustomRules()
            };

            var $btn = $("#tqBtnSaveDefaultTerms");
            $btn.prop("disabled", true).text("Saving...");

            $.ajax({
                url: "/api/v3/transporters/terms/",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                dataType: "json",
                success: function (res) {
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-disk"></i> Save Terms as Business Default');
                    if (res && res.status === "success") {
                        self.notify("Default transporter billing terms saved!", "success");
                    } else {
                        self.notify(res.message || "Failed to save terms.", "warning");
                    }
                },
                error: function () {
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-disk"></i> Save Terms as Business Default');
                    self.notify("Error saving default terms.", "error");
                }
            });
        },

        submitQuotation: function () {
            var self = this;
            if (self.isSubmitting) return;

            var leadId = self.currentLeadData ? (self.currentLeadData.id || self.currentLeadData.encoded_id) : null;
            if (!leadId) {
                if (window.V3LeadView && typeof window.V3LeadView.getLeadId === "function") {
                    leadId = window.V3LeadView.getLeadId();
                } else if (window.leadViewApp && typeof window.leadViewApp.getLeadId === "function") {
                    leadId = window.leadViewApp.getLeadId();
                }
            }
            if (!leadId) {
                var urlParams = new URLSearchParams(window.location.search);
                var rawId = urlParams.get("id");
                if (rawId) {
                    try {
                        var decoded = atob(rawId);
                        if (decoded && !isNaN(decoded)) {
                            leadId = decoded;
                        } else {
                            leadId = rawId;
                        }
                    } catch (e) {
                        leadId = rawId;
                    }
                }
            }

            var customerName = $.trim($("#tqCustomerName").val());
            var customerMobile = $.trim($("#tqCustomerMobile").val());
            var customerEmail = $.trim($("#tqCustomerEmail").val());
            var customerAddress = $.trim($("#tqCustomerAddress").val());

            var destination = $.trim($("#tqDestination").val());
            var pickupLocation = $.trim($("#tqPickupLocation").val());
            var dropLocation = $.trim($("#tqDropLocation").val());
            var travelDate = $.trim($("#tqTravelDate").val());
            var travelDuration = $.trim($("#tqTravelDuration").val()) || "1";

            var vehicleId = $("#tqVehicleModel").val();
            var $selectedVehicle = $("#tqVehicleModel option:selected");
            var vehicleTitle = $selectedVehicle.attr("data-title") || $selectedVehicle.text();
            var vehicleType = $("#tqVehicleType").val() || $selectedVehicle.attr("data-type") || "";
            var vehicleSeating = $selectedVehicle.attr("data-seating") || "";

            var totalCost = parseFloat($("#tqTotalCost").val()) || 0;
            var kmRunning = parseFloat($("#tqKmRunning").val()) || 250;
            var totalRunningKm = parseFloat($("#tqTotalRunningKm").val()) || 0;
            if (kmRunning > 0 && totalRunningKm < kmRunning) {
                totalRunningKm = kmRunning;
                $("#tqTotalRunningKm").val(kmRunning);
            }
            var extraKmCharge = parseFloat($("#tqExtraKmCharge").val()) || 0;
            var advancePercent = parseFloat($("#tqAdvancePercent").val()) || 25;
            var advanceAmount = roundToTwo((totalCost * advancePercent) / 100);
            var balanceAmount = Math.max(0, roundToTwo(totalCost - advanceAmount));

            // Validation
            if (!customerName) {
                self.notify("Please enter customer name.", "warning");
                $("#tqCustomerName").focus();
                return;
            }
            if (!customerMobile) {
                self.notify("Please enter customer mobile number.", "warning");
                $("#tqCustomerMobile").focus();
                return;
            }
            if (!destination) {
                self.notify("Please specify destination.", "warning");
                $("#tqDestination").focus();
                return;
            }
            if (!pickupLocation) {
                self.notify("Please enter pickup location.", "warning");
                $("#tqPickupLocation").focus();
                return;
            }
            if (!dropLocation) {
                self.notify("Please enter drop location.", "warning");
                $("#tqDropLocation").focus();
                return;
            }
            if (!travelDate) {
                self.notify("Please select a travel date.", "warning");
                $("#tqTravelDate").focus();
                return;
            }
            if (typeof moment !== "undefined" && moment(travelDate, "YYYY-MM-DD", true).isValid()) {
                if (moment(travelDate, "YYYY-MM-DD").isBefore(moment().startOf("day"))) {
                    self.notify("Travel date cannot be in the past.", "warning");
                    $("#tqTravelDate").focus();
                    return;
                }
            }
            if (!vehicleId) {
                self.notify("Please choose a vehicle model.", "warning");
                $("#tqVehicleModel").focus();
                return;
            }
            if (totalCost <= 0) {
                self.notify("Please provide a valid Total Cost.", "warning");
                $("#tqTotalCost").focus();
                return;
            }

            var terms = {
                parking_charges: $('input[name="tq_term_parking"]:checked').val() || "no",
                garage_to_garage: $('input[name="tq_term_garage"]:checked').val() || "yes",
                toll_tax: $('input[name="tq_term_toll"]:checked').val() || "yes",
                state_tax: $('input[name="tq_term_state_tax"]:checked').val() || "yes",
                driver_allowance: $('input[name="tq_term_driver"]:checked').val() || "yes",
                custom_rules: self.collectCustomRules()
            };

            var postData = {
                lead_id: leadId,
                customer: {
                    name: customerName,
                    mobile: customerMobile,
                    email: customerEmail,
                    address: customerAddress
                },
                travel: {
                    destination: destination,
                    pickup_location: pickupLocation,
                    drop_location: dropLocation,
                    travel_date: travelDate,
                    duration_days: travelDuration
                },
                vehicle: {
                    vehicle_id: vehicleId,
                    vehicle_type: vehicleType,
                    vehicle_title: vehicleTitle,
                    vehicle_seating: vehicleSeating
                },
                billing: {
                    currency_symbol: self.activeCurrencySymbol,
                    total_cost: totalCost,
                    km_running: kmRunning,
                    total_running_km: totalRunningKm,
                    extra_km_charge: extraKmCharge,
                    advance_percent: advancePercent,
                    advance_amount: advanceAmount,
                    balance_amount: balanceAmount
                },
                terms: terms
            };

            self.isSubmitting = true;
            var $btn = $("#tqBtnSubmitQuote");
            $btn.prop("disabled", true).text("Generating Proposal...");

            $.ajax({
                url: "/api/v3/transporters/quotation/",
                type: "POST",
                data: JSON.stringify(postData),
                contentType: "application/json",
                dataType: "json",
                success: function (res) {
                    self.isSubmitting = false;
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-eye"></i> Preview &amp; Generate Quotation');
                    self.handleQuotationSuccess(res, customerMobile);
                },
                error: function (xhr) {
                    self.isSubmitting = false;
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-eye"></i> Preview &amp; Generate Quotation');
                    var res = xhr.responseJSON;
                    if (!res && xhr.responseText) {
                        try {
                            var jsonStart = xhr.responseText.indexOf("{");
                            if (jsonStart !== -1) {
                                res = JSON.parse(xhr.responseText.substring(jsonStart));
                            }
                        } catch (e) {}
                    }
                    if (res && res.status === "success") {
                        self.handleQuotationSuccess(res, customerMobile);
                        return;
                    }
                    var err = (res && (res.message || res.msg)) || "Error generating quotation.";
                    self.notify(err, "error");
                }
            });
        },

        handleQuotationSuccess: function (res, customerMobile) {
            var self = this;
            if (!res || res.status !== "success") {
                self.notify((res && (res.message || res.msg)) ? (res.message || res.msg) : "Failed to create quotation.", "error");
                return;
            }

            self.lastCreatedQuote = res;
            self.lastSavedPdfUrl = null;
            self.lastSavedPdfFullUrl = null;
            self.lastSavedPdfFileName = null;

            var rawPhone = customerMobile || (res.customer ? res.customer.mobile : "") || $("#tqCustomerMobile").val() || "";
            var cleanPhone = String(rawPhone).replace(/[^0-9]/g, "");
            if (cleanPhone.length === 10) cleanPhone = "91" + cleanPhone;
            self.lastCustomerMobile = cleanPhone;

            // Reset Share modal status indicators
            $("#tqWaSentBanner").addClass("d-none");
            $("#tqWaViewPdfLink").addClass("d-none").attr("href", "#");
            $("#tqBtnOpenCrmChat").addClass("d-none").attr("href", "#");
            $("#tqBtnSendWaDirect span").text("Send on WhatsApp");

            // Close quote creation modal
            $("#modalSendTransporterQuotation").removeClass("is-active");

            // Populate and open live preview modal
            var iframe = document.getElementById("tqPreviewIframe");
            if (iframe) {
                iframe.srcdoc = res.rendered_html || "<p class=\"tq-empty-preview\">Quotation preview generated.</p>";
                iframe.onload = function () {
                    if (res.quotation_id) {
                        self.ensureQuotationPdf(res.quotation_id, function () {});
                    }
                };
            }
            $("#modalTransporterQuotePreview").addClass("is-active");

            // Sync Delivery Channels from Builder to Preview
            var isEmailChecked = $("#toggleTqSendEmail").is(":checked");
            var isWaChecked = $("#toggleTqSendWhatsApp").is(":checked");
            $("#toggleTqSendEmailPreview").prop("checked", isEmailChecked);
            $("#itemTqSendEmailPreview").toggleClass("is-email-checked", isEmailChecked);
            $("#toggleTqSendWhatsAppPreview").prop("checked", isWaChecked);
            $("#itemTqSendWhatsAppPreview").toggleClass("is-whatsapp-checked", isWaChecked);

            // Pre-populate WhatsApp Share Modal for Send Quote action
            $("#tqWaPreviewBox").text(res.whatsapp_text || "");

            var waUrl = "https://api.whatsapp.com/send?phone=" + encodeURIComponent(cleanPhone) + "&text=" + encodeURIComponent(res.whatsapp_text || "");
            $("#tqBtnSendWaDirect").attr("data-fallback-url", waUrl);

            self.notify("Quotation generated! Review proposal preview below.", "success");

            // Refresh Lead Details SPA timeline if available
            if (window.V3LeadView && typeof window.V3LeadView.loadLeadDetails === "function") {
                window.V3LeadView.loadLeadDetails();
            } else if (window.leadViewApp && typeof window.leadViewApp.loadLeadDetails === "function") {
                window.leadViewApp.loadLeadDetails();
            }
        },


        formatNumber: function (num) {
            return parseFloat(num || 0).toLocaleString("en-IN", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        getIndianCurrencyWords: function (num) {
            var n = Math.round(parseFloat(num || 0));
            if (n <= 0) return "Rupees Zero Only";
            var ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine",
                "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
            var tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

            function convertLessThanOneThousand(val) {
                var str = "";
                if (val >= 100) {
                    str += ones[Math.floor(val / 100)] + " Hundred ";
                    val = val % 100;
                }
                if (val >= 20) {
                    str += tens[Math.floor(val / 10)] + " ";
                    val = val % 10;
                }
                if (val > 0) {
                    str += ones[val] + " ";
                }
                return str.trim();
            }

            var crore = Math.floor(n / 10000000);
            n = n % 10000000;
            var lakh = Math.floor(n / 100000);
            n = n % 100000;
            var thousand = Math.floor(n / 1000);
            n = n % 1000;
            var remainder = n;

            var res = "";
            if (crore > 0) {
                res += convertLessThanOneThousand(crore) + " Crore ";
            }
            if (lakh > 0) {
                res += convertLessThanOneThousand(lakh) + " Lakh ";
            }
            if (thousand > 0) {
                res += convertLessThanOneThousand(thousand) + " Thousand ";
            }
            if (remainder > 0) {
                res += convertLessThanOneThousand(remainder) + " ";
            }
            return "Rupees " + res.trim() + " Only";
        },

        notify: function (msg, type) {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: type || "info",
                    title: msg,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else if (typeof popUpMessage === "function") {
                popUpMessage("center", msg, type || "info");
            } else {
                alert(msg);
            }
        },

        loadHtml2Pdf: function (callback) {
            if (typeof window.html2pdf !== "undefined") {
                return callback(null, window.html2pdf);
            }
            var existingScript = document.querySelector('script[src*="html2pdf"]');
            if (existingScript) {
                var checkCount = 0;
                var checkInterval = setInterval(function () {
                    checkCount++;
                    if (typeof window.html2pdf !== "undefined") {
                        clearInterval(checkInterval);
                        return callback(null, window.html2pdf);
                    }
                    if (checkCount >= 50) {
                        clearInterval(checkInterval);
                        return callback(new Error("Timeout waiting for html2pdf bundle to load."));
                    }
                }, 100);
                return;
            }
            var script = document.createElement("script");
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js";
            script.onload = function () {
                callback(null, window.html2pdf);
            };
            script.onerror = function () {
                callback(new Error("Failed to load html2pdf script bundle."));
            };
            document.head.appendChild(script);
        },

        ensureQuotationPdf: function (quoteId, callback) {
            var self = this;
            if (self.lastSavedPdfUrl) {
                return callback(null, {
                    pdf_url: self.lastSavedPdfUrl,
                    full_url: self.lastSavedPdfFullUrl,
                    file_name: self.lastSavedPdfFileName
                });
            }

            if (self.isGeneratingPdf) {
                self.pdfGenerationCallbacks.push(callback);
                return;
            }

            self.isGeneratingPdf = true;
            self.pdfGenerationCallbacks = [callback];

            var triggerCallbacks = function (err, result) {
                self.isGeneratingPdf = false;
                var list = self.pdfGenerationCallbacks;
                self.pdfGenerationCallbacks = [];
                for (var i = 0; i < list.length; i++) {
                    if (typeof list[i] === "function") {
                        list[i](err, result);
                    }
                }
            };

            var iframe = document.getElementById("tqPreviewIframe");
            if (!iframe) {
                return triggerCallbacks(new Error("Preview iframe not found"));
            }

            var iframeDoc = iframe.contentDocument || (iframe.contentWindow ? iframe.contentWindow.document : null);
            if (!iframeDoc || !iframeDoc.body) {
                return triggerCallbacks(new Error("Preview document not yet loaded"));
            }

            // Temporarily hide any floating print action bar inside iframe for clean PDF output
            var actionBars = iframeDoc.querySelectorAll(".v3-pdf-actions-bar, .pdf-actions-bar");
            for (var a = 0; a < actionBars.length; a++) {
                actionBars[a].style.display = "none";
            }
            var restoreActionBars = function () {
                for (var b = 0; b < actionBars.length; b++) {
                    actionBars[b].style.display = "";
                }
            };

            var targetEl = iframeDoc.querySelector("#page-quotation") ||
                           iframeDoc.querySelector(".page") ||
                           iframeDoc.querySelector(".mit-quote-page") ||
                           iframeDoc.querySelector(".mit-quote-container") ||
                           iframeDoc.querySelector(".tq-proposal-wrapper") ||
                           iframeDoc.body;

            self.loadHtml2Pdf(function (libErr, html2pdf) {
                if (libErr || !html2pdf) {
                    restoreActionBars();
                    return triggerCallbacks(libErr || new Error("html2pdf library is unavailable"));
                }

                try {
                    var opt = {
                        margin: [6, 6, 6, 6],
                        filename: "Quotation_TQ-" + quoteId + ".pdf",
                        image: { type: "jpeg", quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true, logging: false },
                        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
                    };

                    html2pdf().set(opt).from(targetEl).toPdf().get("pdf").then(function (pdf) {
                        restoreActionBars();
                        var pdfDataUri = pdf.output("datauristring");
                        if (!pdfDataUri || pdfDataUri.length < 50) {
                            return triggerCallbacks(new Error("Invalid or empty PDF data URI rendered"));
                        }

                        $.ajax({
                            url: "/api/v3/transporters/quotation.php?action=save_pdf",
                            type: "POST",
                            dataType: "json",
                            contentType: "application/json",
                            data: JSON.stringify({
                                quotation_id: quoteId,
                                pdf_data: pdfDataUri
                            }),
                            success: function (saveRes) {
                                if (saveRes && saveRes.status === "success") {
                                    self.lastSavedPdfUrl = saveRes.pdf_url;
                                    self.lastSavedPdfFullUrl = saveRes.full_url;
                                    self.lastSavedPdfFileName = saveRes.file_name;
                                    triggerCallbacks(null, saveRes);
                                } else {
                                    triggerCallbacks(new Error((saveRes && saveRes.message) ? saveRes.message : "Failed to save quotation PDF"));
                                }
                            },
                            error: function () {
                                triggerCallbacks(new Error("Network error occurred while saving quotation PDF"));
                            }
                        });
                    }).catch(function (e) {
                        restoreActionBars();
                        triggerCallbacks(e);
                    });
                } catch (e) {
                    restoreActionBars();
                    triggerCallbacks(e);
                }
            });
        },

        setupTqDeliveryToggles: function (lead) {
            var self = this;
            lead = lead || self.currentLeadData || {};
            var email = String(lead.email || $("#tqCustomerEmail").val() || "").trim();
            var mobile = String(lead.mobile || lead.wa_mobile || $("#tqCustomerMobile").val() || "").trim();
            var waDevice = lead.whatsapp_device || null;

            // 1. Email Toggle Configuration
            var hasEmail = !!(email && email.indexOf("@") !== -1);
            var $emailToggles = $("#toggleTqSendEmail, #toggleTqSendEmailPreview");
            var $emailItems = $("#itemTqSendEmail, #itemTqSendEmailPreview");
            $emailToggles.prop("disabled", !hasEmail);
            if (!hasEmail) {
                $emailToggles.prop("checked", false);
                $emailItems.addClass("is-disabled").removeClass("is-email-checked").attr("title", "Customer has no email address on file");
            } else {
                $emailToggles.prop("checked", true);
                $emailItems.removeClass("is-disabled").addClass("is-email-checked").attr("title", "Send quotation proposal to " + email);
            }

            // 2. WhatsApp Toggle Configuration
            var hasPhone = !!(mobile && mobile.replace(/\D/g, "").length >= 7);
            var $waToggles = $("#toggleTqSendWhatsApp, #toggleTqSendWhatsAppPreview");
            var $waItems = $("#itemTqSendWhatsApp, #itemTqSendWhatsAppPreview");

            var applyDeviceStatus = function (device) {
                var isAvailable = !!(device && (device.available || device.is_connected));
                var canSendWa = hasPhone && isAvailable;
                $waToggles.prop("disabled", !canSendWa);

                var waBadgeText = "";
                var waBadgeClass = "";

                if (!hasPhone) {
                    $waToggles.prop("checked", false);
                    $waItems.addClass("is-disabled").removeClass("is-whatsapp-checked").attr("title", "Customer has no phone number on file");
                    waBadgeText = "No Phone";
                    waBadgeClass = "warning";
                } else if (!isAvailable) {
                    $waToggles.prop("checked", false);
                    $waItems.addClass("is-disabled").removeClass("is-whatsapp-checked").attr("title", "No WhatsApp device is connected on this site. Please connect a device in WhatsApp Settings.");
                    waBadgeText = "Offline";
                    waBadgeClass = "offline";
                } else {
                    $waToggles.prop("checked", true);
                    var devNum = device.phone_number ? ("+" + String(device.phone_number).replace(/^\+/, "")) : "";
                    var devTitle = device.is_assigned
                        ? ("Sending via your assigned WhatsApp device (" + devNum + ")")
                        : ("Sending via default WhatsApp device (" + devNum + ")");
                    $waItems.removeClass("is-disabled").addClass("is-whatsapp-checked").attr("title", devTitle);
                    waBadgeText = device.is_assigned ? ("Assigned (" + devNum + ")") : ("Default (" + devNum + ")");
                    waBadgeClass = "online";
                }

                if (waBadgeText) {
                    var badgeHtml = '<span class="v3-chan-device-pill ' + waBadgeClass + '" title="' + escapeHtml(waBadgeText) + '">' + escapeHtml(waBadgeText) + '</span>';
                    $("#tqWaDeviceBadge, #tqWaDeviceBadgePreview").html(badgeHtml);
                } else {
                    $("#tqWaDeviceBadge, #tqWaDeviceBadgePreview").empty();
                }
            };

            if (waDevice) {
                applyDeviceStatus(waDevice);
            } else {
                self.checkDefaultWhatsAppDevice(function (err, res) {
                    var dev = (res && res.device) ? res.device : null;
                    if (dev) {
                        dev.available = !!res.is_connected;
                        dev.is_connected = !!res.is_connected;
                    }
                    applyDeviceStatus(dev);
                });
            }
        },

        checkDefaultWhatsAppDevice: function (callback) {
            $.ajax({
                url: "/api/v3/whatsapp/messages.php?action=check_default_device",
                type: "GET",
                dataType: "json",
                cache: false,
                success: function (res) {
                    callback(null, res);
                },
                error: function () {
                    callback(null, { status: "error", is_connected: false });
                }
            });
        },

        sendQuotationViaWhatsApp: function (isTriggeredFromShareModal) {
            var self = this;
            var quote = self.lastCreatedQuote;
            if (!quote || !quote.quotation_id) {
                self.notify("No active quotation proposal found to send.", "error");
                return;
            }

            var quoteId = quote.quotation_id;
            var rawPhone = self.lastCustomerMobile || (quote.customer ? quote.customer.mobile : "") || $("#tqCustomerMobile").val() || "";
            var cleanPhone = String(rawPhone).replace(/[^0-9]/g, "");
            if (cleanPhone.length === 10) cleanPhone = "91" + cleanPhone;
            var whatsappText = quote.whatsapp_text || $("#tqWaPreviewBox").text() || "";

            // Check selected delivery channels
            var isEmailChecked = !isTriggeredFromShareModal && ($("#toggleTqSendEmailPreview").is(":checked") || $("#toggleTqSendEmail").is(":checked"));
            var isWaChecked = isTriggeredFromShareModal || ($("#toggleTqSendWhatsAppPreview").is(":checked") || $("#toggleTqSendWhatsApp").is(":checked"));

            if (!isEmailChecked && !isWaChecked) {
                self.notify("Please select at least one delivery channel (Email or WhatsApp).", "warning");
                return;
            }

            var $btn = isTriggeredFromShareModal ? $("#tqBtnSendWaDirect") : $("#tqBtnPreviewSendQuote");
            var originalBtnHtml = $btn.html();
            $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Sending Quotation...');

            // Step 1: Ensure PDF is generated/ready
            self.ensureQuotationPdf(quoteId, function (pdfErr, pdfRes) {
                var fullPdfUrl = (!pdfErr && pdfRes && (pdfRes.full_url || pdfRes.pdf_url)) ? (pdfRes.full_url || pdfRes.pdf_url) : "";

                // Step 2: Dispatch through backend API (action=send)
                $.ajax({
                    url: "/api/v3/transporters/quotation.php?action=send",
                    type: "POST",
                    dataType: "json",
                    contentType: "application/json",
                    data: JSON.stringify({
                        quotation_id: quoteId,
                        send_email: isEmailChecked ? 1 : 0,
                        send_whatsapp: isWaChecked ? 1 : 0,
                        pdf_url: fullPdfUrl,
                        whatsapp_text: whatsappText
                    }),
                    success: function (res) {
                        $btn.prop("disabled", false).html(originalBtnHtml);

                        if (res && res.status === "success") {
                            var noticeMsg = res.message || "Quotation proposal sent to customer!";
                            self.notify(noticeMsg, "success");

                            if (res.whatsapp_error && res.whatsapp_error !== "Skipped by user toggle.") {
                                self.notify("WhatsApp Notice: " + res.whatsapp_error, "warning");
                            }
                            if (res.email_error && res.email_error !== "Skipped by user toggle.") {
                                self.notify("Email Notice: " + res.email_error, "warning");
                            }

                            // If sent over WhatsApp, update Share Modal banner if shown
                            if (res.whatsapp_sent) {
                                $("#tqWaSentBanner").removeClass("d-none");
                                $("#tqWaSentTitle").text("Quotation & Proposal PDF Sent Successfully");
                                $("#tqWaSentDesc").html("Sent to <strong>+" + cleanPhone + "</strong> with proposal document attached.");
                                if (fullPdfUrl) {
                                    $("#tqWaViewPdfLink").attr("href", fullPdfUrl).removeClass("d-none");
                                }
                                $("#tqBtnOpenCrmChat").attr("href", "/dashboard/whatsapp_chat/?phone=" + encodeURIComponent(cleanPhone)).removeClass("d-none");
                                $("#tqBtnSendWaDirect span").text("Resend on WhatsApp");
                            }

                            // Close live preview modal
                            $("#modalTransporterQuotePreview").removeClass("is-active");

                            // Refresh Lead Timeline in SPA
                            if (window.V3LeadView && typeof window.V3LeadView.loadLeadDetails === "function") {
                                window.V3LeadView.loadLeadDetails();
                            } else if (window.leadViewApp && typeof window.leadViewApp.loadLeadDetails === "function") {
                                window.leadViewApp.loadLeadDetails();
                            }
                        } else {
                            var errMsg = (res && res.message) ? res.message : "Failed to dispatch quotation.";
                            self.notify(errMsg, "error");

                            // Fallback if WhatsApp was checked but could not be sent directly
                            if (isWaChecked && (!res || !res.whatsapp_sent)) {
                                if (isTriggeredFromShareModal) {
                                    var fallbackUrl = "https://api.whatsapp.com/send?phone=" + encodeURIComponent(cleanPhone) + "&text=" + encodeURIComponent(whatsappText);
                                    window.open(fallbackUrl, "_blank");
                                } else {
                                    $("#modalTransporterQuotePreview").removeClass("is-active");
                                    $("#modalTransporterQuoteShare").addClass("is-active");
                                }
                            }
                        }
                    },
                    error: function (xhr) {
                        $btn.prop("disabled", false).html(originalBtnHtml);
                        var errResp = xhr.responseJSON;
                        var errMsg = (errResp && (errResp.message || errResp.msg)) ? (errResp.message || errResp.msg) : "Error sending quotation.";
                        self.notify(errMsg, "error");

                        if (isWaChecked) {
                            if (isTriggeredFromShareModal) {
                                var fallbackUrl = "https://api.whatsapp.com/send?phone=" + encodeURIComponent(cleanPhone) + "&text=" + encodeURIComponent(whatsappText);
                                window.open(fallbackUrl, "_blank");
                            } else {
                                $("#modalTransporterQuotePreview").removeClass("is-active");
                                $("#modalTransporterQuoteShare").addClass("is-active");
                            }
                        }
                    }
                });
            });
        }
    };

    function roundToTwo(num) {
        return +(Math.round(num + "e+2") + "e-2");
    }

    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function defined(name) {
        return typeof window[name] !== "undefined";
    }

    window.TransporterQuoteApp = TransporterQuoteApp;

    $(document).ready(function () {
        TransporterQuoteApp.init();
    });

    $(document)
        .off("spa:loaded.transporterQuote")
        .on("spa:loaded.transporterQuote", function () {
            TransporterQuoteApp.init();
        });

});
