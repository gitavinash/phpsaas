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

            // Reactive Billing Calculations
            $(document).on("input change", "#tqTotalCost, #tqAdvancePercent", function () {
                self.recalculateBilling();
            });

            // Change Vehicle Type filter (support both native change and Select2 events)
            $(document).on("change select2:select", "#tqVehicleType", function () {
                var selectedCat = $(this).val();
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
                $("#modalTransporterQuotePreview").removeClass("is-active");
                $("#modalTransporterQuoteShare").addClass("is-active");
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
            $("#tqTotalCost").val(quoteVal > 0 ? quoteVal : "");
            $("#tqKmRunning").val(250);
            $("#tqExtraKmCharge").val(14);

            self.recalculateBilling();

            // Initialize and sync Select2 dropdowns
            self.initSelect2Dropdowns();
            $("#tqVehicleType").val("").trigger("change.select2");
            self.filterVehicleModels("");

            // Open Modal
            $("#modalSendTransporterQuotation").addClass("is-active");
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
                        self.filterVehicleModels($("#tqVehicleType").val());
                    }
                },
                error: function () {
                    self.vehiclesLoading = false;
                }
            });
        },

        filterVehicleModels: function (category) {
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

            filtered.forEach(function (v) {
                var label = v.title + (v.seating ? " (" + v.seating + " Seater)" : "");
                var $opt = $("<option></option>")
                    .val(v.id)
                    .text(label)
                    .attr("data-title", v.title)
                    .attr("data-type", v.type || "")
                    .attr("data-seating", v.seating || "");
                $select.append($opt);
            });

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

            // Close quote creation modal
            $("#modalSendTransporterQuotation").removeClass("is-active");

            // Populate and open live preview modal
            var iframe = document.getElementById("tqPreviewIframe");
            if (iframe) {
                iframe.srcdoc = res.rendered_html || "<p style='padding:20px;text-align:center;'>Quotation preview generated.</p>";
            }
            $("#modalTransporterQuotePreview").addClass("is-active");

            // Pre-populate WhatsApp Share Modal for Send Quote action
            $("#tqWaPreviewBox").text(res.whatsapp_text || "");

            var rawPhone = customerMobile || (res.customer ? res.customer.mobile : "") || $("#tqCustomerMobile").val() || "";
            var cleanPhone = String(rawPhone).replace(/[^0-9]/g, "");
            if (cleanPhone.length === 10) cleanPhone = "91" + cleanPhone; // default country code if missing
            var waUrl = "https://api.whatsapp.com/send?phone=" + encodeURIComponent(cleanPhone) + "&text=" + encodeURIComponent(res.whatsapp_text || "");
            $("#tqBtnSendWaDirect").attr("href", waUrl);

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
