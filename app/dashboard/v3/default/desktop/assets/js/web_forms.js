/**
 * V3 Landing Page Web Forms Plugin JavaScript Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/web_forms.js
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

    window.V3WebForms = {
        forms: [],
        suggestedFields: [],
        currentForm: null,
        activeTab: "tab-general",
        viewport: "desktop",
        primaryAdminEmail: "",
        apiEndpoint: "/api/v3/integration/web_forms/",
        eventsBound: false,
        isDraggingField: false,
        draggedFieldIdx: null,

        init: function () {
            var $app = $("#pluginWebFormsApp");
            if (!$app.length) return;

            if (!this.suggestedFields || !this.suggestedFields.length) {
                this.suggestedFields = this.getDefaultAleyaFields();
            }

            if (!this.eventsBound) {
                this.bindEvents();
                this.eventsBound = true;
            }

            this.initSelect2Dropdowns();
            this.fetchForms();
        },

        // -------------------------------------------------------------
        // Select2 Dropdowns Helper Initialization
        // -------------------------------------------------------------
        initSelect2Dropdowns: function () {
            if (typeof $.fn.select2 === "undefined") return;

            if ($("#wfStatusFilter").length) {
                try { $("#wfStatusFilter").select2("destroy"); } catch (e) {}
                $("#wfStatusFilter").select2({
                    width: "160px",
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: Infinity
                });
            }
        },

        initBuilderSelect2: function () {
            if (typeof $.fn.select2 === "undefined") return;

            if ($("#formBgModeSelect").length) {
                try { $("#formBgModeSelect").select2("destroy"); } catch (e) {}
                $("#formBgModeSelect").select2({
                    dropdownParent: $("#wfBuilderModal"),
                    width: "100%",
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: Infinity
                });
            }
        },

        initCustomFieldSelect2: function () {
            if (typeof $.fn.select2 === "undefined") return;

            $("#cfTypeSelect, #cfWidthSelect").each(function () {
                var $el = $(this);
                try { $el.select2("destroy"); } catch (e) {}
                $el.select2({
                    dropdownParent: $("#wfBuilderModal"),
                    width: "100%",
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: Infinity
                });
            });
        },

        // -------------------------------------------------------------
        // 1. API Data Fetching & Table Rendering
        // -------------------------------------------------------------
        fetchForms: function () {
            var self = this;
            var search = $("#wfSearchInput").val() || "";

            $.ajax({
                url: self.apiEndpoint,
                method: "GET",
                data: { action: "list", search: search },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success" && res.data) {
                        self.forms = res.data.forms || [];
                        if (res.data.suggested_fields && res.data.suggested_fields.length) {
                            self.suggestedFields = self.mergeDefaultAleyaFields(res.data.suggested_fields);
                        } else {
                            self.suggestedFields = self.getDefaultAleyaFields();
                        }
                        self.primaryAdminEmail = res.primary_admin_email || (res.data && res.data.primary_admin_email) || "";

                        $("#statTotalForms").text(res.data.total_forms || 0);
                        $("#statActiveForms").text(res.data.active_forms || 0);
                        $("#statTotalSubmissions").text(res.data.total_submissions || 0);

                        self.renderTable();
                    } else {
                        self.renderTable();
                    }
                },
                error: function () {
                    self.renderTable();
                }
            });
        },

        renderTable: function () {
            var self = this;
            var $tbody = $("#wfTableBody");
            var $table = $("#wfFormsTable");
            var $empty = $("#wfEmptyState");
            var filter = $("#wfStatusFilter").val() || "all";
            var search = ($("#wfSearchInput").val() || "").toLowerCase().trim();

            $tbody.empty();

            var filtered = this.forms.filter(function (f) {
                if (filter === "active" && f.form_status !== 1) return false;
                if (filter === "inactive" && f.form_status !== 0) return false;
                if (search.length > 0) {
                    var matchesTitle = f.form_title.toLowerCase().indexOf(search) !== -1;
                    var matchesSlug = f.form_slug.toLowerCase().indexOf(search) !== -1;
                    var matchesHeading = (f.form_heading || "").toLowerCase().indexOf(search) !== -1;
                    if (!matchesTitle && !matchesSlug && !matchesHeading) return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                $table.hide();
                $empty.show();
                return;
            }

            $table.show();
            $empty.hide();

            filtered.forEach(function (form) {
                var isActive = form.form_status === 1;
                var dotColor = form.form_theme_color || "#4f46e5";
                var rowHtml = [
                    '<tr data-id="' + form.form_id + '">',
                        '<td>',
                            '<div class="wf-title-cell">',
                                '<div style="display:flex;align-items:center;gap:8px;">',
                                    '<span class="wf-color-dot" style="background:' + dotColor + ';"></span>',
                                    '<span class="wf-form-name">' + self.escapeHtml(form.form_title) + '</span>',
                                '</div>',
                                '<span class="wf-form-heading-sub">' + self.escapeHtml(form.form_heading || "") + '</span>',
                            '</div>',
                        '</td>',
                        '<td>',
                            '<div style="display:flex;align-items:center;gap:6px;">',
                                '<span class="wf-slug-badge" title="' + form.public_url + '">/form/' + self.escapeHtml(form.form_slug || form.form_uid) + '</span>',
                                '<button type="button" class="wf-btn-action btn-copy-url" data-url="' + form.public_url + '" title="Copy Public URL">',
                                    '<i class="fi fi-rr-copy"></i>',
                                '</button>',
                                '<a href="' + form.public_url + '" target="_blank" class="wf-btn-action" title="Open Public View">',
                                    '<i class="fi fi-rr-arrow-up-right-from-square"></i>',
                                '</a>',
                            '</div>',
                        '</td>',
                        '<td>',
                            '<span class="wf-count-badge">' + form.fields_count + ' Fields</span>',
                        '</td>',
                        '<td>',
                            '<span class="wf-count-badge" style="background:#ecfdf5;color:#059669;">' + form.form_submissions_count + ' Leads</span>',
                        '</td>',
                        '<td>',
                            '<label class="v3-toggle-switch" title="Toggle Active Status">',
                                '<input type="checkbox" class="wf-row-status-toggle" data-id="' + form.form_id + '" ' + (isActive ? 'checked' : '') + '>',
                                '<span class="v3-toggle-slider emerald"></span>',
                            '</label>',
                        '</td>',
                        '<td>',
                            '<div class="wf-table-actions">',
                                '<button type="button" class="wf-btn-action btn-embed btn-show-embed" data-id="' + form.form_id + '" title="Get Embed Iframe Code">',
                                    '<i class="fi fi-rr-code-branch"></i>',
                                '</button>',
                                '<button type="button" class="wf-btn-action btn-edit-form" data-id="' + form.form_id + '" title="Edit Form">',
                                    '<i class="fi fi-rr-pencil"></i>',
                                '</button>',
                                '<button type="button" class="wf-btn-action btn-duplicate-form" data-id="' + form.form_id + '" title="Duplicate Form">',
                                    '<i class="fi fi-rr-duplicate"></i>',
                                '</button>',
                                '<button type="button" class="wf-btn-action btn-danger btn-delete-form" data-id="' + form.form_id + '" title="Delete Form">',
                                    '<i class="fi fi-rr-trash"></i>',
                                '</button>',
                            '</div>',
                        '</td>',
                    '</tr>'
                ].join('');

                $tbody.append(rowHtml);
            });
        },

        // -------------------------------------------------------------
        // 2. Form Builder (Modal, Tabs, Live Preview)
        // -------------------------------------------------------------
        openBuilder: function (formId) {
            var self = this;
            self.closeCustomFieldForm();
            self.switchTab("tab-general");

            if (!formId || formId === 0) {
                // New Form Mode
                $("#builderModalTitle").text("Create Web Form");
                $("#builderFormId").val("0");
                $("#builderFormUid").val("");
                $("#formTitleInput").val("");
                $("#formSlugInput").val("");
                $("#formHeadingInput").val("Get a Free Quote").removeClass("is-hidden-dimmed");
                $("#formHideHeadingCheck").prop("checked", false);
                $("#formSubheadingInput").val("Plan your dream vacation with our travel specialists").removeClass("is-hidden-dimmed");
                $("#formHideSubheadingCheck").prop("checked", false);
                $("#formSubmitTextInput").val("Get Free Quote");
                $("#formThemeColorInput").val("#4f46e5");
                $("#formThemeColorHex").val("#4f46e5");
                $("#formBgModeSelect").val("card").trigger("change");
                $("#formTrackingSource").val("landing_page");
                $("#formTrackingMedium").val("web_form");
                $("#formTrackingCampaign").val("");
                $("#formTrackingSubject").val("");
                $("#formRedirectUrlInput").val("");
                $("#formEmailToInput").val(self.primaryAdminEmail || "");
                $("#formEmailToInput").attr("placeholder", self.primaryAdminEmail ? ("e.g. " + self.primaryAdminEmail) : "e.g. sales@yourdomain.com");
                $("#formEmailCcInput").val("");
                $("#formEmailBccInput").val("");
                $("#formSuccessMsgInput").val("Thank you! We have received your inquiry and will contact you shortly.");

                self.isSlugManuallyEdited = false;
                $("#slugStatusIcon").empty().removeClass("is-loading is-available is-taken");
                $("#slugFeedback").empty().removeClass("is-visible is-available is-taken is-loading");

                self.currentForm = {
                    form_id: 0,
                    fields: self.mergeDefaultAleyaFields(self.suggestedFields && self.suggestedFields.length ? self.suggestedFields : self.getDefaultAleyaFields())
                };

                // Enforce mandatory fields
                self.enforceMandatoryFields(self.currentForm.fields);
                self.renderBuilderFields();
                self.updateLivePreview();
                $("#wfBuilderModal").addClass("is-open");
                self.initBuilderSelect2();
            } else {
                // Edit Form Mode
                $("#builderModalTitle").text("Edit Web Form");
                $.ajax({
                    url: self.apiEndpoint,
                    method: "GET",
                    data: { action: "get", form_id: formId },
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success" && res.data) {
                            var data = res.data;
                            self.currentForm = data;
                            self.currentForm.fields = self.mergeDefaultAleyaFields(data.fields || []);

                            if (res.data.primary_admin_email) {
                                self.primaryAdminEmail = res.data.primary_admin_email;
                            }
                            $("#builderFormId").val(data.form_id);
                            $("#builderFormUid").val(data.form_uid);
                            $("#formTitleInput").val(data.form_title);
                            $("#formSlugInput").val(data.form_slug || "");
                            $("#formHeadingInput").val(data.form_heading || "");
                            var isHideH = parseInt(data.form_hide_heading, 10) === 1;
                            $("#formHideHeadingCheck").prop("checked", isHideH);
                            $("#formHeadingInput").toggleClass("is-hidden-dimmed", isHideH);

                            $("#formSubheadingInput").val(data.form_subheading || "");
                            var isHideSub = parseInt(data.form_hide_subheading, 10) === 1;
                            $("#formHideSubheadingCheck").prop("checked", isHideSub);
                            $("#formSubheadingInput").toggleClass("is-hidden-dimmed", isHideSub);
                            $("#formSubmitTextInput").val(data.form_submit_btn_text);
                            $("#formThemeColorInput").val(data.form_theme_color || "#4f46e5");
                            $("#formThemeColorHex").val(data.form_theme_color || "#4f46e5");
                            $("#formBgModeSelect").val(data.form_bg_mode || "card").trigger("change");

                            var trk = data.tracking || {};
                            $("#formTrackingSource").val(trk.source || "landing_page");
                            $("#formTrackingMedium").val(trk.medium || "web_form");
                            $("#formTrackingCampaign").val(trk.campaign || "");
                            $("#formTrackingSubject").val(trk.subject || "");
                            $("#formRedirectUrlInput").val(data.form_redirect_url || "");
                            $("#formEmailToInput").attr("placeholder", self.primaryAdminEmail ? ("e.g. " + self.primaryAdminEmail) : "e.g. sales@yourdomain.com");
                            $("#formEmailToInput").val(data.form_email_to ? data.form_email_to : (self.primaryAdminEmail || ""));
                            $("#formEmailCcInput").val(data.form_email_cc || "");
                            $("#formEmailBccInput").val(data.form_email_bcc || "");
                            $("#formSuccessMsgInput").val(data.form_success_message || "Thank you! We have received your inquiry and will contact you shortly.");

                            self.isSlugManuallyEdited = Boolean(data.form_slug && data.form_slug.length > 0);
                            $("#slugStatusIcon").empty().removeClass("is-loading is-available is-taken");
                            $("#slugFeedback").empty().removeClass("is-visible is-available is-taken is-loading");
                            if (data.form_slug) {
                                self.checkSlugAvailability(data.form_slug);
                            }

                            self.enforceMandatoryFields(self.currentForm.fields);
                            self.renderBuilderFields();
                            self.updateLivePreview();
                            $("#wfBuilderModal").addClass("is-open");
                            self.initBuilderSelect2();
                        }
                    }
                });
            }
        },

        closeBuilder: function () {
            this.closeCustomFieldForm();
            $("#wfBuilderModal").removeClass("is-open");
        },

        slugify: function (text) {
            if (!text) return "";
            return String(text)
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },

        triggerSlugCheck: function (rawSlug) {
            var self = this;
            clearTimeout(self.slugCheckTimer);
            self.slugCheckTimer = setTimeout(function () {
                self.checkSlugAvailability(rawSlug);
            }, 300);
        },

        checkSlugAvailability: function (rawSlug) {
            var self = this;
            var slug = self.slugify(rawSlug);
            var formId = parseInt($("#builderFormId").val(), 10) || 0;
            var $icon = $("#slugStatusIcon");
            var $fb = $("#slugFeedback");

            if (!slug) {
                $icon.empty().removeClass("is-loading is-available is-taken");
                $fb.empty().removeClass("is-visible is-available is-taken is-loading");
                return;
            }

            if (self.slugCheckXhr && self.slugCheckXhr.abort) {
                try { self.slugCheckXhr.abort(); } catch (e) {}
            }

            $icon.empty().removeClass("is-available is-taken").addClass("is-loading");
            $fb.addClass("is-visible is-loading").removeClass("is-available is-taken")
               .html('<i class="fi fi-rr-spinner"></i> Checking availability for <strong>/form/' + self.escapeHtml(slug) + '</strong>...');

            self.slugCheckXhr = $.ajax({
                url: self.apiEndpoint,
                method: "GET",
                data: {
                    action: "check_slug",
                    slug: slug,
                    form_id: formId
                },
                dataType: "json",
                success: function (res) {
                    $icon.removeClass("is-loading");
                    $fb.removeClass("is-loading");

                    if (res.status === "success") {
                        if (res.available) {
                            $icon.addClass("is-available").html('<i class="fi fi-rr-check"></i>');
                            $fb.addClass("is-available").html(
                                '<i class="fi fi-rr-check"></i> <strong>/form/' + self.escapeHtml(res.slug) + '</strong> is available!'
                            );
                        } else {
                            $icon.addClass("is-taken").html('<i class="fi fi-rr-cross"></i>');
                            var html = '<i class="fi fi-rr-cross-circle"></i> ' + (res.msg || ('/form/' + self.escapeHtml(res.slug) + ' is already taken.'));
                            if (res.suggestion) {
                                html += ' &bull; Try: <button type="button" class="wf-slug-suggestion-btn" data-slug="' + self.escapeHtml(res.suggestion) + '" title="Click to use this slug">' + self.escapeHtml(res.suggestion) + '</button>';
                            }
                            $fb.addClass("is-taken").html(html);
                        }
                    } else {
                        $icon.addClass("is-taken").html('<i class="fi fi-rr-cross"></i>');
                        $fb.addClass("is-taken").text(res.msg || "Invalid slug.");
                    }
                },
                error: function (xhr, status) {
                    if (status === "abort") return;
                    $icon.removeClass("is-loading");
                    $fb.removeClass("is-loading is-available").addClass("is-taken").text("Unable to verify slug availability.");
                }
            });
        },

        getDefaultAleyaFields: function () {
            return [
                {
                    field_key: "full_name",
                    type: "text",
                    label: "Full Name",
                    placeholder: "Enter your full name",
                    required: true,
                    is_system: true,
                    enabled: true,
                    col_span: 12
                },
                {
                    field_key: "mobile",
                    type: "tel",
                    label: "Mobile Number",
                    placeholder: "Enter 10-digit mobile number",
                    required: true,
                    is_system: true,
                    enabled: true,
                    col_span: 12
                },
                {
                    field_key: "email",
                    type: "email",
                    label: "Email Address",
                    placeholder: "name@example.com",
                    required: false,
                    is_system: false,
                    enabled: true,
                    col_span: 12
                },
                {
                    field_key: "destination",
                    type: "text",
                    label: "Tour Destination",
                    placeholder: "e.g. Kashmir, Maldives, Dubai",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 12
                },
                {
                    field_key: "travel_date",
                    type: "date",
                    label: "Date of Travel",
                    placeholder: "Select tentative travel date",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "duration",
                    type: "text",
                    label: "Duration / Nights",
                    placeholder: "e.g. 5 Days / 4 Nights",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "persons",
                    type: "number",
                    label: "Total Travelers / Persons",
                    placeholder: "e.g. 2",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "adults",
                    type: "number",
                    label: "Adults (12+ yrs)",
                    placeholder: "2",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "children",
                    type: "number",
                    label: "Children (2-12 yrs)",
                    placeholder: "0",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "infants",
                    type: "number",
                    label: "Infants (< 2 yrs)",
                    placeholder: "0",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "pickup_city",
                    type: "text",
                    label: "Departure / Pickup City",
                    placeholder: "e.g. Mumbai, Delhi, Bengaluru",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 12
                },
                {
                    field_key: "budget",
                    type: "text",
                    label: "Approx. Budget per Person",
                    placeholder: "e.g. ₹25,000 - ₹40,000",
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "hotel_category",
                    type: "select",
                    label: "Hotel Preference",
                    placeholder: "Select star rating",
                    options: ["3 Star Standard", "4 Star Deluxe", "5 Star Luxury", "Boutique / Resort"],
                    required: false,
                    is_system: false,
                    enabled: false,
                    col_span: 6
                },
                {
                    field_key: "message",
                    type: "textarea",
                    label: "Special Requirements / Notes",
                    placeholder: "Any specific requests or preferred sightseeing...",
                    required: false,
                    is_system: false,
                    enabled: true,
                    col_span: 12
                }
            ];
        },

        mergeDefaultAleyaFields: function (existingFields) {
            var defaults = this.getDefaultAleyaFields();
            var list = Array.isArray(existingFields) ? JSON.parse(JSON.stringify(existingFields)) : [];
            var map = {};

            list.forEach(function (f) {
                if (f && f.field_key) {
                    map[f.field_key] = f;
                }
            });

            // Append any default Aleya fields that are not in the list
            defaults.forEach(function (def) {
                if (!map[def.field_key]) {
                    list.push(JSON.parse(JSON.stringify(def)));
                } else {
                    if (map[def.field_key].enabled === undefined) {
                        map[def.field_key].enabled = def.enabled;
                    }
                }
            });

            return list;
        },

        enforceMandatoryFields: function (fields) {
            var hasName = false;
            var hasMobile = false;

            fields.forEach(function (f) {
                if (f.field_key === "full_name") {
                    hasName = true;
                    f.required = true;
                    f.enabled = true;
                    f.is_system = true;
                }
                if (f.field_key === "mobile") {
                    hasMobile = true;
                    f.required = true;
                    f.enabled = true;
                    f.is_system = true;
                }
            });

            if (!hasName) {
                fields.unshift({
                    field_key: "full_name",
                    type: "text",
                    label: "Full Name",
                    placeholder: "Enter your full name",
                    required: true,
                    is_system: true,
                    enabled: true,
                    col_span: 12
                });
            }
            if (!hasMobile) {
                fields.splice(1, 0, {
                    field_key: "mobile",
                    type: "tel",
                    label: "Mobile Number",
                    placeholder: "Enter 10-digit mobile number",
                    required: true,
                    is_system: true,
                    enabled: true,
                    col_span: 12
                });
            }
        },

        renderBuilderFields: function () {
            var self = this;
            var $wrap = $("#builderFieldsContainer");
            $wrap.empty();

            var fields = self.currentForm.fields || [];

            fields.forEach(function (field, idx) {
                var isSystem = field.is_system || field.field_key === "full_name" || field.field_key === "mobile";
                var isCustom = field.is_custom === true;
                var isEnabled = field.enabled !== false;
                var isReq = field.required === true;
                var colSpan = field.col_span || 12;
                if (colSpan == 4) { colSpan = 6; field.col_span = 6; }
                var widthLabel = colSpan == 6 ? '2 in a row (50%)' : '1 in a row (100%)';
                var phDisplay = field.placeholder ? field.placeholder : '(No placeholder set)';
                var iconKeyClass = 'wf-icon-' + field.field_key;

                // 1. Header Right: Edit, Delete, Divider, Visibility Switch
                var editBtnHtml = '<button type="button" class="wf-btn-field-action btn-edit-field" data-idx="' + idx + '" title="Edit Label & Placeholder">' +
                    '<i class="fi fi-rr-pencil"></i>' +
                '</button>';

                var deleteBtnHtml = isCustom ? 
                    '<button type="button" class="wf-btn-field-action text-danger btn-delete-custom-field" data-idx="' + idx + '" title="Delete Custom Field">' +
                        '<i class="fi fi-rr-trash"></i>' +
                    '</button>' : '';

                var visibilityHtml = '';
                var eyeIconClass = isEnabled ? 'fi-rr-eye' : 'fi-rr-eye-crossed';
                var eyeIconStatus = isEnabled ? 'is-shown' : 'is-hidden';
                var eyeTitle = isEnabled ? 'Field visible in form' : 'Field hidden from form';

                if (isSystem) {
                    visibilityHtml = '<label class="v3-toggle-switch wf-field-top-switch is-locked" title="Mandatory core field — always enabled in form">' +
                        '<input type="checkbox" checked disabled>' +
                        '<span class="v3-toggle-slider"></span>' +
                        '<i class="fi fi-rr-eye wf-field-eye-icon is-shown" title="Always visible in form"></i>' +
                    '</label>';
                } else {
                    visibilityHtml = '<label class="v3-toggle-switch wf-field-top-switch" title="' + (isEnabled ? 'Click to hide field from form' : 'Click to show field in form') + '">' +
                        '<input type="checkbox" class="field-enable-toggle" data-idx="' + idx + '" ' + (isEnabled ? 'checked' : '') + '>' +
                        '<span class="v3-toggle-slider"></span>' +
                        '<i class="fi ' + eyeIconClass + ' wf-field-eye-icon ' + eyeIconStatus + '" title="' + eyeTitle + '"></i>' +
                    '</label>';
                }

                // 2. Width Segmented Switcher (1 in a row vs 2 in a row)
                var widthSegmentedHtml = '<div class="wf-width-segmented" data-idx="' + idx + '" title="Select input layout width">' +
                    '<button type="button" class="wf-btn-width ' + (colSpan == 12 ? 'active' : '') + '" data-idx="' + idx + '" data-width="12" title="Full Width (1 in a row / 100%)" ' + (!isEnabled ? 'disabled' : '') + '>' +
                        '<i class="fi fi-rr-rectangle-horizontal"></i> <span>1 in a row</span>' +
                    '</button>' +
                    '<button type="button" class="wf-btn-width ' + (colSpan == 6 ? 'active' : '') + '" data-idx="' + idx + '" data-width="6" title="Half Width (2 in a row / 50%)" ' + (!isEnabled ? 'disabled' : '') + '>' +
                        '<i class="fi fi-rr-columns-3"></i> <span>2 in a row</span>' +
                    '</button>' +
                '</div>';

                // 3. Required Control as Toggle Switch
                var reqControlHtml = '';
                if (isSystem) {
                    reqControlHtml = '<div class="wf-req-toggle-box is-locked" title="Mandatory field — always required">' +
                        '<i class="fi fi-rr-lock wf-req-lock-icon"></i>' +
                        '<span class="wf-req-text">Required</span>' +
                        '<span class="v3-toggle-switch wf-toggle-compact">' +
                            '<input type="checkbox" checked disabled>' +
                            '<span class="v3-toggle-slider rose"></span>' +
                        '</span>' +
                    '</div>';
                } else {
                    reqControlHtml = '<label class="wf-req-toggle-box ' + (isReq ? 'is-active' : '') + '" title="Toggle required validation for this field">' +
                        '<span class="wf-req-text">Required</span>' +
                        '<span class="v3-toggle-switch wf-toggle-compact">' +
                            '<input type="checkbox" class="field-req-toggle" data-idx="' + idx + '" ' + (isReq ? 'checked' : '') + ' ' + (!isEnabled ? 'disabled' : '') + '>' +
                            '<span class="v3-toggle-slider rose"></span>' +
                        '</span>' +
                    '</label>';
                }

                // 4. Placeholder as a sleek inline input box
                var placeholderHtml = '<div class="wf-field-ph-wrap" title="Directly edit placeholder for this field">' +
                    '<div class="wf-ph-inner ' + (!isEnabled ? 'is-disabled' : '') + '">' +
                        '<span class="wf-ph-lead-icon" title="Field placeholder"><i class="fi fi-rr-quote-right"></i></span>' +
                        '<input type="text" class="wf-field-ph-input v3-form-input" data-idx="' + idx + '" value="' + self.escapeHtml(field.placeholder || '') + '" placeholder="Placeholder hint (e.g. ' + self.escapeHtml(field.label) + ')..." ' + (!isEnabled ? 'disabled' : '') + ' spellcheck="false" autocomplete="off">' +
                    '</div>' +
                '</div>';

                var row = [
                    '<div class="wf-field-item-row ' + (isSystem ? 'is-locked' : '') + ' ' + (!isEnabled ? 'is-disabled-field' : '') + '" data-idx="' + idx + '" draggable="false">',
                        '<div class="wf-field-card-header">',
                            '<div class="wf-field-header-left">',
                                '<div class="wf-drag-handle" title="Drag up or down to rearrange position">',
                                    '<svg width="12" height="18" viewBox="0 0 12 18" fill="currentColor">',
                                        '<circle cx="3" cy="3" r="1.5"/>',
                                        '<circle cx="9" cy="3" r="1.5"/>',
                                        '<circle cx="3" cy="9" r="1.5"/>',
                                        '<circle cx="9" cy="9" r="1.5"/>',
                                        '<circle cx="3" cy="15" r="1.5"/>',
                                        '<circle cx="9" cy="15" r="1.5"/>',
                                    '</svg>',
                                '</div>',
                                '<div class="wf-field-icon ' + iconKeyClass + '">',
                                    '<i class="fi ' + self.getFieldIcon(field.field_key, field.type) + '"></i>',
                                '</div>',
                                '<div class="wf-field-title-wrap">',
                                    '<div class="wf-field-title-line">',
                                        '<span class="wf-field-title">' + self.escapeHtml(field.label) + '</span>',
                                        (isSystem ? '<span class="wf-badge-locked"><i class="fi fi-rr-lock"></i> Mandatory</span>' : (isCustom ? '<span class="wf-badge-custom"><i class="fi fi-rr-sparkles"></i> Custom</span>' : '')),
                                    '</div>',
                                    '<div class="wf-field-meta-sub">',
                                        '<span class="wf-field-order-num">#' + (idx + 1) + '</span> &bull; <code>' + field.field_key + '</code> &bull; <span class="wf-type-badge">' + field.type + '</span> &bull; <span class="wf-width-text-hint">' + widthLabel + '</span>',
                                    '</div>',
                                '</div>',
                            '</div>',
                            '<div class="wf-field-header-right">',
                                editBtnHtml,
                                deleteBtnHtml,
                                '<div class="wf-header-divider"></div>',
                                visibilityHtml,
                            '</div>',
                        '</div>',

                        '<div class="wf-field-card-body">',
                            placeholderHtml,
                            '<div class="wf-field-actions-toolbar">',
                                widthSegmentedHtml,
                                reqControlHtml,
                            '</div>',
                        '</div>',
                    '</div>'
                ].join('');

                $wrap.append(row);
            });
        },

        getPlaceholderSuggestions: function (key, type) {
            var map = {
                full_name: [
                    "e.g. Rahul Sharma",
                    "Enter your full name",
                    "Your Name (as on Passport)",
                    "First & Last Name"
                ],
                mobile: [
                    "e.g. +91 98765 43210",
                    "10-digit mobile number",
                    "WhatsApp / Calling number",
                    "e.g. 9876543210"
                ],
                email: [
                    "e.g. rahul@example.com",
                    "Where should we send the quote?",
                    "Enter your email address"
                ],
                destination: [
                    "e.g. Bali, Maldives, Dubai",
                    "Where would you like to travel?",
                    "Dream holiday destination",
                    "e.g. Kashmir / Switzerland"
                ],
                travel_date: [
                    "Select tentative travel date",
                    "When are you planning to travel?",
                    "Departure date"
                ],
                duration: [
                    "e.g. 5 Days / 4 Nights",
                    "Trip duration in days/nights",
                    "e.g. 1 Week"
                ],
                persons: [
                    "e.g. 2 Travelers",
                    "Total number of persons",
                    "Number of guests traveling"
                ],
                adults: [
                    "e.g. 2 Adults",
                    "Adults (12+ yrs)",
                    "Number of adults"
                ],
                children: [
                    "e.g. 1 Child",
                    "Children (2-12 yrs)",
                    "Number of kids (optional)"
                ],
                infants: [
                    "e.g. 0",
                    "Infants (< 2 yrs)",
                    "Number of infants"
                ],
                pickup_city: [
                    "e.g. Mumbai, Delhi, Bengaluru",
                    "Departure / Flying from",
                    "Nearest airport or city"
                ],
                budget: [
                    "e.g. ₹50,000 - ₹1,00,000 per person",
                    "Estimated total trip budget",
                    "Approximate budget in ₹"
                ],
                hotel_category: [
                    "e.g. 4-Star or 5-Star Luxury",
                    "Standard, Deluxe, or Luxury",
                    "Preferred hotel rating"
                ],
                message: [
                    "Tell us about your dream trip, hotel preference, places to visit...",
                    "Any special requests, flight preferences, or itinerary notes?",
                    "Share your travel plans or questions here..."
                ]
            };

            if (key && map[key]) return map[key];

            if (type === "number") return ["e.g. 1", "e.g. 5", "Enter count / number"];
            if (type === "date") return ["Select tentative date", "DD/MM/YYYY"];
            if (type === "email") return ["e.g. name@example.com", "your.name@domain.com"];
            if (type === "tel") return ["e.g. +91 98765 43210", "10-digit mobile number"];
            if (type === "textarea") return ["Write your details or special requests here...", "Provide additional information..."];
            if (type === "select") return ["Choose an option", "Select from dropdown"];
            return ["e.g. Enter details here...", "Type your response...", "Required information"];
        },

        renderPlaceholderSuggestions: function (key, type) {
            var self = this;
            var list = self.getPlaceholderSuggestions(key, type);
            var $box = $("#cfPhChipsList");
            $box.empty();

            list.forEach(function (phText) {
                var $btn = $('<button type="button" class="wf-ph-chip-btn" title="Click to use this improved placeholder"></button>');
                $btn.text(phText);
                $btn.on("click", function (e) {
                    e.preventDefault();
                    $("#cfPlaceholderInput").val(phText).focus();
                });
                $box.append($btn);
            });
        },

        openCustomFieldForm: function (editIdx) {
            var self = this;
            var isEdit = typeof editIdx === "number" && editIdx >= 0;
            $("#cfEditIndex").val(isEdit ? editIdx : -1);

            if (isEdit && self.currentForm && self.currentForm.fields[editIdx]) {
                var field = self.currentForm.fields[editIdx];
                var isSystem = field.is_system || field.field_key === "full_name" || field.field_key === "mobile";
                var isCustom = field.is_custom === true;

                $("#cfFormTitle").text(isCustom ? "Edit Custom Field" : "Configure Field: " + (field.label || field.field_key));
                $("#cfSaveBtnLabel").text("Update Field");
                $("#cfLabelInput").val(field.label || "");
                $("#cfKeyInput").val(field.field_key || "").prop("disabled", !isCustom);
                $("#cfTypeSelect").val(field.type || "text").prop("disabled", !isCustom).trigger("change");
                $("#cfWidthSelect").val((field.col_span == 6) ? 6 : 12).trigger("change");
                $("#cfPlaceholderInput").val(field.placeholder || "");
                $("#cfRequiredCheckbox").prop("checked", field.required === true).prop("disabled", isSystem);
                
                if (isSystem) {
                    $("#cfReqWrap").hide();
                } else {
                    $("#cfReqWrap").show();
                }

                if (field.type === "select") {
                    $("#cfOptionsRow").addClass("is-active");
                    $("#cfOptionsInput").val((field.options || []).join(", "));
                } else {
                    $("#cfOptionsRow").removeClass("is-active");
                    $("#cfOptionsInput").val("");
                }

                // Render Smart Placeholder Suggestions
                self.renderPlaceholderSuggestions(field.field_key, field.type);
            } else {
                $("#cfFormTitle").text("Add Custom Field");
                $("#cfSaveBtnLabel").text("Add to Form");
                $("#cfLabelInput").val("");
                $("#cfKeyInput").val("").prop("disabled", false);
                $("#cfTypeSelect").val("text").prop("disabled", false).trigger("change");
                $("#cfWidthSelect").val("12").trigger("change");
                $("#cfPlaceholderInput").val("");
                $("#cfRequiredCheckbox").prop("checked", false).prop("disabled", false);
                $("#cfReqWrap").show();
                $("#cfOptionsRow").removeClass("is-active");
                $("#cfOptionsInput").val("");

                // Default suggestions for text
                self.renderPlaceholderSuggestions("", "text");
            }

            $("#wfCustomFieldCard").addClass("is-open");
            self.initCustomFieldSelect2();
            var cardEl = document.getElementById("wfCustomFieldCard");
            if (cardEl && cardEl.scrollIntoView) {
                cardEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
            }
            setTimeout(function () {
                $("#cfPlaceholderInput").focus();
            }, 100);
        },

        closeCustomFieldForm: function () {
            $("#wfCustomFieldCard").removeClass("is-open");
            $("#cfEditIndex").val("-1");
        },

        saveCustomField: function () {
            var self = this;
            var label = $.trim($("#cfLabelInput").val());
            var rawKey = $.trim($("#cfKeyInput").val());
            var key = rawKey.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/^_+|_+$/g, '');
            var type = $("#cfTypeSelect").val() || "text";
            var colSpan = parseInt($("#cfWidthSelect").val(), 10) || 12;
            var placeholder = $.trim($("#cfPlaceholderInput").val());
            var isRequired = $("#cfRequiredCheckbox").is(":checked");
            var editIdx = parseInt($("#cfEditIndex").val(), 10);

            if (!label) {
                if (typeof swal !== "undefined") {
                    swal("Missing Label", "Please enter a field label.", "warning");
                } else {
                    alert("Please enter a field label.");
                }
                $("#cfLabelInput").focus();
                return;
            }

            if (editIdx >= 0 && self.currentForm && self.currentForm.fields && self.currentForm.fields[editIdx] && !self.currentForm.fields[editIdx].is_custom) {
                key = self.currentForm.fields[editIdx].field_key;
            }

            if (!key) {
                if (typeof swal !== "undefined") {
                    swal("Missing Key", "Please enter a unique field key (e.g. passport_number).", "warning");
                } else {
                    alert("Please enter a field key.");
                }
                $("#cfKeyInput").focus();
                return;
            }

            if (!self.currentForm) {
                self.currentForm = { fields: [] };
            }
            if (!self.currentForm.fields) {
                self.currentForm.fields = [];
            }

            // Check key collision with other fields
            var duplicate = false;
            self.currentForm.fields.forEach(function (f, i) {
                if (i !== editIdx && f.field_key === key) {
                    duplicate = true;
                }
            });

            if (duplicate) {
                if (typeof swal !== "undefined") {
                    swal("Duplicate Key", "A field with parameter key '" + key + "' already exists. Please choose a different key.", "error");
                } else {
                    alert("A field with key '" + key + "' already exists.");
                }
                $("#cfKeyInput").focus();
                return;
            }

            var opts = [];
            if (type === "select") {
                var rawOpts = $.trim($("#cfOptionsInput").val());
                if (rawOpts) {
                    opts = rawOpts.split(',').map(function (s) { return $.trim(s); }).filter(Boolean);
                }
            }

            var fieldObj = {
                field_key: key,
                type: type,
                label: label,
                placeholder: placeholder,
                required: isRequired,
                enabled: true,
                is_system: false,
                is_custom: true,
                col_span: colSpan
            };
            if (type === "select") {
                fieldObj.options = opts;
            }

            if (editIdx >= 0 && self.currentForm.fields[editIdx]) {
                var existing = self.currentForm.fields[editIdx];
                existing.label = label;
                existing.placeholder = placeholder;
                existing.col_span = colSpan;
                if (!existing.is_system) {
                    existing.required = isRequired;
                }
                if (existing.is_custom) {
                    existing.field_key = key;
                    existing.type = type;
                    if (type === "select") {
                        existing.options = opts;
                    }
                }
            } else {
                self.currentForm.fields.push(fieldObj);
            }

            self.closeCustomFieldForm();
            self.renderBuilderFields();
            self.updateLivePreview();
        },

        deleteCustomField: function (idx) {
            var self = this;
            if (!self.currentForm || !self.currentForm.fields || !self.currentForm.fields[idx]) return;

            var target = self.currentForm.fields[idx];
            if (target.is_system || target.field_key === "full_name" || target.field_key === "mobile") {
                return;
            }

            var doDelete = function () {
                self.currentForm.fields.splice(idx, 1);
                self.renderBuilderFields();
                self.updateLivePreview();
            };

            if (typeof swal !== "undefined") {
                swal({
                    title: "Remove Custom Field?",
                    text: "Remove '" + target.label + "' from this form?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    confirmButtonText: "Yes, Remove",
                    closeOnConfirm: true
                }, function (confirmed) {
                    if (confirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm("Remove '" + target.label + "' from this form?")) {
                    doDelete();
                }
            }
        },

        getFieldIcon: function (key, type) {
            switch (key) {
                case "full_name": return "fi-rr-user";
                case "mobile": return "fi-rr-phone-call";
                case "email": return "fi-rr-envelope";
                case "destination": return "fi-rr-map-marker";
                case "travel_date": return "fi-rr-calendar";
                case "duration": return "fi-rr-clock";
                case "persons": case "adults": case "children": case "infants": return "fi-rr-users";
                case "pickup_city": return "fi-rr-paper-plane";
                case "budget": return "fi-rr-wallet";
                case "hotel_category": return "fi-rr-star";
                case "message": return "fi-rr-comment-alt-edit";
                default:
                    if (type === "textarea") return "fi-rr-comment-alt-edit";
                    if (type === "select") return "fi-rr-list";
                    if (type === "date") return "fi-rr-calendar";
                    if (type === "number") return "fi-rr-calculator";
                    if (type === "tel") return "fi-rr-phone-call";
                    if (type === "email") return "fi-rr-envelope";
                    return "fi-rr-form";
            }
        },

        updateLivePreview: function () {
            var self = this;
            var hideHeading = $("#formHideHeadingCheck").is(":checked");
            var hideSubheading = $("#formHideSubheadingCheck").is(":checked");
            var rawHeading = $("#formHeadingInput").val();
            var rawSubheading = $("#formSubheadingInput").val();
            var heading = (!hideHeading && rawHeading !== undefined) ? (rawHeading || "") : "";
            var subheading = (!hideSubheading && rawSubheading !== undefined) ? (rawSubheading || "") : "";
            var btnText = $("#formSubmitTextInput").val() || "Get Free Quote";
            var color = $("#formThemeColorInput").val() || "#4f46e5";
            var bgMode = $("#formBgModeSelect").val() || "card";
            var defaultHeight = "44px";
            var fields = (this.currentForm && this.currentForm.fields) ? this.currentForm.fields : [];

            var $box = $("#livePreviewContainer");
            if (bgMode === "transparent") {
                $box.addClass("is-transparent");
            } else {
                $box.removeClass("is-transparent");
            }

            var html = [
                '<div style="--live-color:' + color + ';">',
                    (heading || subheading ? (
                        '<div style="text-align:center;margin-bottom:16px;">' +
                            (heading ? '<h3 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:4px;">' + this.escapeHtml(heading) + '</h3>' : '') +
                            (subheading ? '<p style="font-size:12.5px;color:#64748b;margin:0;">' + this.escapeHtml(subheading) + '</p>' : '') +
                        '</div>'
                    ) : ''),
                    '<div style="display:grid;grid-template-columns:repeat(12, 1fr);gap:12px;">'
            ];

            fields.forEach(function (f) {
                if (f.enabled === false) return;
                var reqStar = f.required ? '<span style="color:#ef4444;margin-left:2px;">*</span>' : '';
                var colSpan = f.col_span || 12;
                var type = f.type || 'text';
                var fieldHeight = (f.input_height && f.input_height !== 'default') ? f.input_height : defaultHeight;
                var numH = parseInt(fieldHeight, 10) || 44;
                var taHeight = Math.round(numH * 1.85) + 'px';
                var inputMarkup = '';

                if (type === 'textarea') {
                    inputMarkup = '<textarea disabled placeholder="' + self.escapeHtml(f.placeholder || '') + '" style="width:100%;min-height:' + taHeight + ';padding:10px 12px;font-size:13px;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#64748b;outline:none;resize:none;font-family:inherit;box-sizing:border-box;line-height:1.45;"></textarea>';
                } else if (type === 'select') {
                    var opts = f.options || [];
                    inputMarkup = '<select disabled style="width:100%;height:' + fieldHeight + ';padding:0 12px;font-size:13px;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#64748b;outline:none;box-sizing:border-box;">' +
                        '<option selected>' + self.escapeHtml(f.placeholder || 'Select an option') + '</option>' +
                        opts.map(function(o) { return '<option>' + self.escapeHtml(o) + '</option>'; }).join('') +
                    '</select>';
                } else {
                    inputMarkup = '<input type="' + (type === 'number' ? 'number' : (type === 'date' ? 'date' : 'text')) + '" placeholder="' + self.escapeHtml(f.placeholder || '') + '" disabled style="width:100%;height:' + fieldHeight + ';padding:0 12px;font-size:13px;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#64748b;outline:none;box-sizing:border-box;">';
                }

                html.push(
                    '<div style="grid-column:span ' + colSpan + ';display:flex;flex-direction:column;gap:4px;">' +
                        '<label style="font-size:12px;font-weight:600;color:#334155;">' + self.escapeHtml(f.label) + reqStar + '</label>' +
                        inputMarkup +
                    '</div>'
                );
            });

            html.push(
                    '<div style="grid-column:span 12;margin-top:6px;">' +
                        '<button type="button" disabled style="width:100%;height:' + defaultHeight + ';padding:0 16px;font-size:14px;font-weight:600;background:' + color + ';color:#ffffff !important;border:none;border-radius:8px;box-shadow:0 3px 10px rgba(0,0,0,0.1);cursor:default;display:flex;align-items:center;justify-content:center;box-sizing:border-box;">' +
                            this.escapeHtml(btnText) +
                        '</button>' +
                        '<div style="text-align:center;font-size:11px;color:#94a3b8;margin-top:10px;display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;">' +
                            '<span style="display:inline-flex;align-items:center;gap:4px;"><i class="fi fi-rr-shield-check"></i> Protected by reCAPTCHA v3</span>' +
                            '<span style="color:#cbd5e1;">&bull;</span>' +
                            '<span>Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color:#64748b;text-decoration:none;font-weight:600;">Aleya.in</a></span>' +
                        '</div>' +
                    '</div>',
                '</div>',
                '</div>'
            );

            $("#livePreviewContent").html(html.join(''));
        },

        switchTab: function (tabId) {
            this.activeTab = tabId;
            $(".wf-builder-tab").removeClass("active");
            $('.wf-builder-tab[data-tab="' + tabId + '"]').addClass("active");
            $(".wf-tab-pane").removeClass("active");
            $("#" + tabId).addClass("active");

            if (tabId === "tab-style") {
                this.initBuilderSelect2();
            } else if (tabId === "tab-fields" && $("#wfCustomFieldCard").hasClass("is-open")) {
                this.initCustomFieldSelect2();
            }
        },

        // -------------------------------------------------------------
        // 3. Form Persistence & Operations
        // -------------------------------------------------------------
        saveForm: function () {
            var self = this;
            var formId = parseInt($("#builderFormId").val(), 10) || 0;
            var title = $.trim($("#formTitleInput").val());

            if (!title) {
                if (typeof swal !== "undefined") {
                    swal("Missing Title", "Please enter a form title for your landing page form.", "warning");
                } else {
                    alert("Please enter a form title.");
                }
                self.switchTab("tab-general");
                $("#formTitleInput").focus();
                return;
            }

            if ($("#slugStatusIcon").hasClass("is-taken")) {
                if (typeof swal !== "undefined") {
                    swal("Slug Already Taken", "The custom URL slug you entered is already in use by another form. Please choose an available slug.", "warning");
                } else {
                    alert("The custom URL slug is already in use. Please choose another slug.");
                }
                self.switchTab("tab-general");
                $("#formSlugInput").focus();
                return;
            }

            var slug = self.slugify($("#formSlugInput").val()) || self.slugify(title);

            var postData = {
                action: "save",
                form_id: formId,
                form_title: title,
                form_slug: slug,
                form_heading: $.trim($("#formHeadingInput").val()),
                form_hide_heading: $("#formHideHeadingCheck").is(":checked") ? 1 : 0,
                form_subheading: $.trim($("#formSubheadingInput").val()),
                form_hide_subheading: $("#formHideSubheadingCheck").is(":checked") ? 1 : 0,
                form_submit_btn_text: $.trim($("#formSubmitTextInput").val()),
                form_theme_color: $.trim($("#formThemeColorHex").val()) || "#4f46e5",
                form_bg_mode: $("#formBgModeSelect").val() || "card",
                form_input_height: "44px",
                form_redirect_url: $.trim($("#formRedirectUrlInput").val()),
                form_email_to: $.trim($("#formEmailToInput").val()),
                form_email_cc: $.trim($("#formEmailCcInput").val()),
                form_email_bcc: $.trim($("#formEmailBccInput").val()),
                form_success_message: $.trim($("#formSuccessMsgInput").val()),
                tracking_source: $.trim($("#formTrackingSource").val()) || "landing_page",
                tracking_medium: $.trim($("#formTrackingMedium").val()) || "web_form",
                tracking_campaign: $.trim($("#formTrackingCampaign").val()),
                tracking_subject: $.trim($("#formTrackingSubject").val()),
                form_fields: self.currentForm.fields
            };

            var $btn = $("#btnSaveWebForm");
            $btn.prop("disabled", true).find("span").text("Saving...");

            $.ajax({
                url: self.apiEndpoint,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(postData),
                dataType: "json",
                success: function (res) {
                    $btn.prop("disabled", false).find("span").text("Save Web Form");
                    if (res.status === "success") {
                        self.closeBuilder();
                        self.fetchForms();
                        self.showSuccessAlert("Success!", res.msg || "Web form saved successfully.");
                    } else {
                        if (typeof swal !== "undefined") {
                            swal("Error", res.msg || "Failed to save form.", "error");
                        } else {
                            alert(res.msg || "Failed to save form.");
                        }
                    }
                },
                error: function () {
                    $btn.prop("disabled", false).find("span").text("Save Web Form");
                    if (typeof swal !== "undefined") {
                        swal("Error", "Server error while saving web form.", "error");
                    }
                }
            });
        },

        toggleStatus: function (formId, isChecked) {
            var self = this;
            $.ajax({
                url: self.apiEndpoint,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    action: "toggle",
                    form_id: formId,
                    status: isChecked ? 1 : 0
                }),
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        self.fetchForms();
                    }
                }
            });
        },

        duplicateForm: function (formId) {
            var self = this;
            $.ajax({
                url: self.apiEndpoint,
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({ action: "duplicate", form_id: formId }),
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        self.fetchForms();
                        self.showSuccessAlert("Duplicated!", "Form copy created successfully.");
                    }
                }
            });
        },

        deleteForm: function (formId) {
            var self = this;
            var executeDelete = function () {
                $.ajax({
                    url: self.apiEndpoint,
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ action: "delete", form_id: formId }),
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            self.fetchForms();
                            self.showSuccessAlert("Deleted!", "Web form has been deleted.");
                        }
                    }
                });
            };

            if (typeof swal !== "undefined") {
                swal({
                    title: "Delete Web Form?",
                    text: "Existing landing pages embedding this form will no longer display it.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    confirmButtonText: "Yes, Delete It",
                    closeOnConfirm: true
                }, function (confirmed) {
                    if (confirmed) {
                        executeDelete();
                    }
                });
            } else {
                if (confirm("Are you sure you want to delete this form?")) {
                    executeDelete();
                }
            }
        },

        showSuccessAlert: function (title, text) {
            var swalFn = window.swal || window.Swal;
            if (swalFn) {
                swalFn({
                    title: title || "Success!",
                    text: text || "Web form saved successfully.",
                    type: "success",
                    icon: "success",
                    timer: 1600,
                    showConfirmButton: false,
                    buttons: false
                });
                setTimeout(function () {
                    if (window.swal && typeof window.swal.close === "function") {
                        window.swal.close();
                    } else if (window.Swal && typeof window.Swal.close === "function") {
                        window.Swal.close();
                    }
                }, 1600);
            }
        },

        // -------------------------------------------------------------
        // 4. Embed Modal & Clipboard Copy
        // -------------------------------------------------------------
        openEmbedModal: function (formId) {
            var target = this.forms.find(function (f) { return f.form_id == formId; });
            if (!target) return;

            $("#embedPublicUrlInput").val(target.public_url);
            $("#btnOpenPublicUrl").attr("href", target.public_url);
            $("#embedIframeSnippetText").text(target.iframe_snippet);

            $("#wfEmbedModal").addClass("is-open");
        },

        closeEmbedModal: function () {
            $("#wfEmbedModal").removeClass("is-open");
        },

        copyText: function (text, btnElem, successLabel) {
            var doFeedback = function () {
                if (!btnElem) return;
                var $b = $(btnElem);
                var $span = $b.find("span");
                var originalHtml = $b.html();
                var originalTitle = $b.attr("title") || "";

                if ($span.length > 0) {
                    $b.addClass("is-copied");
                    $b.html('<i class="fi fi-rr-check"></i> <span>' + (successLabel || 'Copied!') + '</span>');
                    setTimeout(function () {
                        $b.removeClass("is-copied").html(originalHtml);
                    }, 1800);
                } else {
                    $b.addClass("is-copied");
                    $b.html('<i class="fi fi-rr-check"></i>');
                    $b.attr("title", "Copied to clipboard!");

                    var $tip = $('<div class="wf-floating-copied-toast"><i class="fi fi-rr-check"></i> ' + (successLabel || 'Copied!') + '</div>');
                    $("body").append($tip);
                    var offset = $b.offset();
                    if (offset) {
                        var bWidth = $b.outerWidth() || 32;
                        var tWidth = $tip.outerWidth() || 64;
                        $tip.css({
                            top: Math.max(0, offset.top - 32) + "px",
                            left: Math.max(0, offset.left + (bWidth / 2) - (tWidth / 2)) + "px"
                        });
                        requestAnimationFrame(function () {
                            $tip.addClass("is-visible");
                        });
                    }

                    setTimeout(function () {
                        $tip.removeClass("is-visible");
                        setTimeout(function () { $tip.remove(); }, 200);
                        $b.removeClass("is-copied").html(originalHtml).attr("title", originalTitle);
                    }, 1800);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    doFeedback();
                }).catch(function () {
                    doFallback();
                });
            } else {
                doFallback();
            }

            function doFallback() {
                var temp = $("<textarea>");
                temp.css({ position: "fixed", opacity: "0", top: "0", left: "0", width: "1px", height: "1px" });
                $("body").append(temp);
                temp.val(text).focus().select();
                try {
                    document.execCommand("copy");
                    doFeedback();
                } catch (e) {}
                temp.remove();
            }
        },

        escapeHtml: function (str) {
            if (!str) return "";
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        },

        // -------------------------------------------------------------
        // 5. Global Event Bindings
        // -------------------------------------------------------------
        bindEvents: function () {
            var self = this;

            // Search and filter
            $(document).on("input", "#wfSearchInput", function () {
                self.renderTable();
            });

            $(document).on("change", "#wfStatusFilter", function () {
                self.renderTable();
            });

            $(document).on("click", "#btnRefreshForms", function () {
                self.fetchForms();
            });

            // Open builder
            $(document).on("click", "#btnCreateNewForm, #btnEmptyCreateForm", function () {
                self.openBuilder(0);
            });

            $(document).on("click", ".btn-edit-form", function () {
                var id = $(this).data("id");
                self.openBuilder(id);
            });

            // Close builder
            $(document).on("click", "#btnCloseBuilderModal, #btnCancelBuilder", function () {
                self.closeBuilder();
            });

            // Builder tabs
            $(document).on("click", ".wf-builder-tab", function () {
                var tab = $(this).data("tab");
                self.switchTab(tab);
            });

            // Viewport preview toggle
            $(document).on("click", "#btnPreviewDesktop", function () {
                $(".wf-btn-viewport").removeClass("active");
                $(this).addClass("active");
                $("#livePreviewContainer").css("max-width", "440px");
            });

            $(document).on("click", "#btnPreviewMobile", function () {
                $(".wf-btn-viewport").removeClass("active");
                $(this).addClass("active");
                $("#livePreviewContainer").css("max-width", "320px");
            });

            // Form Title generates URL Slug in real-time
            $(document).on("input", "#formTitleInput", function () {
                var title = $(this).val();
                if (!self.isSlugManuallyEdited) {
                    var slug = self.slugify(title);
                    $("#formSlugInput").val(slug);
                    self.triggerSlugCheck(slug);
                }
            });

            // Form Custom Slug manually edited
            $(document).on("input", "#formSlugInput", function () {
                var rawVal = $(this).val();
                if (!rawVal) {
                    self.isSlugManuallyEdited = false;
                    $("#slugStatusIcon").empty().removeClass("is-loading is-available is-taken");
                    $("#slugFeedback").empty().removeClass("is-visible is-available is-taken is-loading");
                } else {
                    self.isSlugManuallyEdited = true;
                    self.triggerSlugCheck(rawVal);
                }
            });

            // Clean slug format on blur
            $(document).on("blur", "#formSlugInput", function () {
                var clean = self.slugify($(this).val());
                $(this).val(clean);
                if (clean) {
                    self.checkSlugAvailability(clean);
                }
            });

            // Use Suggested Slug
            $(document).on("click", ".wf-slug-suggestion-btn", function (e) {
                e.preventDefault();
                var sug = $(this).data("slug");
                if (sug) {
                    $("#formSlugInput").val(sug);
                    self.isSlugManuallyEdited = true;
                    self.checkSlugAvailability(sug);
                }
            });

            // Live updates from inputs
            $(document).on("input", "#formHeadingInput, #formSubheadingInput, #formSubmitTextInput", function () {
                self.updateLivePreview();
            });

            // Toggle Hide Display Heading
            $(document).on("change", "#formHideHeadingCheck", function () {
                var checked = $(this).is(":checked");
                $("#formHeadingInput").toggleClass("is-hidden-dimmed", checked);
                self.updateLivePreview();
            });

            // Toggle Hide Display Subheading
            $(document).on("change", "#formHideSubheadingCheck", function () {
                var checked = $(this).is(":checked");
                $("#formSubheadingInput").toggleClass("is-hidden-dimmed", checked);
                self.updateLivePreview();
            });

            // Color picker & presets
            $(document).on("input change", "#formThemeColorInput", function () {
                var val = $(this).val();
                $("#formThemeColorHex").val(val);
                $(".wf-color-preset-btn").removeClass("active");
                self.updateLivePreview();
            });

            $(document).on("input", "#formThemeColorHex", function () {
                var val = $(this).val();
                if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                    $("#formThemeColorInput").val(val);
                }
                $(".wf-color-preset-btn").removeClass("active");
                self.updateLivePreview();
            });

            $(document).on("click", ".wf-color-preset-btn", function () {
                var color = $(this).data("color");
                $(".wf-color-preset-btn").removeClass("active");
                $(this).addClass("active");
                $("#formThemeColorInput").val(color);
                $("#formThemeColorHex").val(color);
                self.updateLivePreview();
            });

            $(document).on("change", "#formBgModeSelect", function () {
                self.updateLivePreview();
            });

            // Field toggles (Show/Hide in top-right of input box)
            $(document).on("change", ".field-enable-toggle", function () {
                var idx = parseInt($(this).data("idx"), 10);
                var isChecked = $(this).is(":checked");
                if (self.currentForm && self.currentForm.fields && self.currentForm.fields[idx]) {
                    self.currentForm.fields[idx].enabled = isChecked;
                    self.renderBuilderFields();
                    self.updateLivePreview();
                }
            });

            $(document).on("change", ".field-req-toggle", function () {
                var idx = parseInt($(this).data("idx"), 10);
                var isChecked = $(this).is(":checked");
                if (self.currentForm && self.currentForm.fields && self.currentForm.fields[idx]) {
                    self.currentForm.fields[idx].required = isChecked;
                    $(this).closest(".wf-req-toggle-box").toggleClass("is-active", isChecked);
                    self.updateLivePreview();
                }
            });

            // Live inline placeholder editing
            $(document).on("input change", ".wf-field-ph-input", function () {
                var idx = parseInt($(this).data("idx"), 10);
                var val = $(this).val();
                if (self.currentForm && self.currentForm.fields && self.currentForm.fields[idx]) {
                    self.currentForm.fields[idx].placeholder = val;
                    self.updateLivePreview();
                }
            });

            // -------------------------------------------------------------
            // Drag and Drop Field Position Rearranging (Up / Down)
            // -------------------------------------------------------------
            $(document).on("mouseenter mousedown", ".wf-drag-handle, .wf-field-header-left", function (e) {
                if (!$(e.target).closest("input, select, textarea, button, .v3-toggle-switch, .wf-btn-field-action").length) {
                    $(this).closest(".wf-field-item-row").attr("draggable", "true");
                }
            });

            $(document).on("mouseleave", ".wf-field-header-left", function () {
                if (!self.isDraggingField) {
                    $(this).closest(".wf-field-item-row").attr("draggable", "false");
                }
            });

            $(document).on("mousedown focusin", "input, select, textarea, button, .v3-toggle-switch, .wf-btn-field-action", function () {
                $(this).closest(".wf-field-item-row").attr("draggable", "false");
            });

            $(document).on("dragstart", ".wf-field-item-row", function (e) {
                if ($(e.target).closest("input, select, textarea, button, .v3-toggle-switch, .wf-btn-field-action").length && !$(e.target).closest(".wf-drag-handle").length) {
                    e.preventDefault();
                    return false;
                }
                self.isDraggingField = true;
                var idx = parseInt($(this).data("idx"), 10);
                self.draggedFieldIdx = idx;

                if (e.originalEvent && e.originalEvent.dataTransfer) {
                    e.originalEvent.dataTransfer.effectAllowed = "move";
                    try {
                        e.originalEvent.dataTransfer.setData("text/plain", String(idx));
                    } catch (err) {}
                }

                var $row = $(this);
                setTimeout(function () {
                    $row.addClass("is-dragging");
                }, 10);
            });

            $(document).on("dragover", ".wf-field-item-row", function (e) {
                e.preventDefault();
                if (e.originalEvent && e.originalEvent.dataTransfer) {
                    e.originalEvent.dataTransfer.dropEffect = "move";
                }

                var targetIdx = parseInt($(this).data("idx"), 10);
                if (targetIdx === self.draggedFieldIdx) {
                    $(".wf-field-item-row").removeClass("is-drag-over-top is-drag-over-bottom");
                    return;
                }

                var rect = this.getBoundingClientRect();
                var relY = e.originalEvent.clientY - rect.top;
                var isTop = relY < (rect.height / 2);

                $(".wf-field-item-row").not(this).removeClass("is-drag-over-top is-drag-over-bottom");
                if (isTop) {
                    if (!$(this).hasClass("is-drag-over-top")) {
                        $(this).removeClass("is-drag-over-bottom").addClass("is-drag-over-top");
                    }
                } else {
                    if (!$(this).hasClass("is-drag-over-bottom")) {
                        $(this).removeClass("is-drag-over-top").addClass("is-drag-over-bottom");
                    }
                }
            });

            $(document).on("dragleave", ".wf-field-item-row", function (e) {
                var rect = this.getBoundingClientRect();
                var clientX = e.originalEvent.clientX;
                var clientY = e.originalEvent.clientY;
                if (clientX < rect.left || clientX >= rect.right || clientY < rect.top || clientY >= rect.bottom) {
                    $(this).removeClass("is-drag-over-top is-drag-over-bottom");
                }
            });

            $(document).on("drop", ".wf-field-item-row", function (e) {
                e.preventDefault();
                $(".wf-field-item-row").removeClass("is-dragging is-drag-over-top is-drag-over-bottom");

                var fromIdx = self.draggedFieldIdx;
                var toIdx = parseInt($(this).data("idx"), 10);

                var rect = this.getBoundingClientRect();
                var relY = e.originalEvent.clientY - rect.top;
                var isTop = relY < (rect.height / 2);

                if (fromIdx !== undefined && fromIdx !== null && !isNaN(fromIdx) && !isNaN(toIdx) && fromIdx !== toIdx) {
                    if (self.currentForm && self.currentForm.fields) {
                        var movedField = self.currentForm.fields.splice(fromIdx, 1)[0];
                        var insertIdx = toIdx;
                        if (!isTop) {
                            insertIdx = (fromIdx < toIdx) ? toIdx : toIdx + 1;
                        } else {
                            insertIdx = (fromIdx < toIdx) ? toIdx - 1 : toIdx;
                        }
                        if (insertIdx < 0) insertIdx = 0;
                        if (insertIdx > self.currentForm.fields.length) insertIdx = self.currentForm.fields.length;

                        self.currentForm.fields.splice(insertIdx, 0, movedField);
                        self.renderBuilderFields();
                        self.updateLivePreview();
                    }
                }

                self.isDraggingField = false;
                self.draggedFieldIdx = null;
            });

            $(document).on("dragend", ".wf-field-item-row", function () {
                self.isDraggingField = false;
                self.draggedFieldIdx = null;
                $(".wf-field-item-row").attr("draggable", "false").removeClass("is-dragging is-drag-over-top is-drag-over-bottom");
            });

            $(document).on("dragover", "#builderFieldsContainer", function (e) {
                e.preventDefault();
            });

            $(document).on("drop", "#builderFieldsContainer", function (e) {
                if (!$(e.target).closest(".wf-field-item-row").length) {
                    e.preventDefault();
                    var fromIdx = self.draggedFieldIdx;
                    if (fromIdx !== undefined && fromIdx !== null && !isNaN(fromIdx)) {
                        if (self.currentForm && self.currentForm.fields) {
                            var movedField = self.currentForm.fields.splice(fromIdx, 1)[0];
                            self.currentForm.fields.push(movedField);
                            self.renderBuilderFields();
                            self.updateLivePreview();
                        }
                    }
                    self.isDraggingField = false;
                    self.draggedFieldIdx = null;
                    $(".wf-field-item-row").attr("draggable", "false").removeClass("is-dragging is-drag-over-top is-drag-over-bottom");
                }
            });

            // Custom Fields events
            $(document).on("click", "#btnOpenAddCustomField, #btnOpenAddCustomFieldBottom", function () {
                self.openCustomFieldForm(-1);
            });

            $(document).on("click", "#btnCancelCustomField, #btnDismissCustomField", function () {
                self.closeCustomFieldForm();
            });

            $(document).on("change", "#cfTypeSelect", function () {
                var type = $(this).val();
                if (type === "select") {
                    $("#cfOptionsRow").addClass("is-active");
                } else {
                    $("#cfOptionsRow").removeClass("is-active");
                }
                var key = $("#cfKeyInput").val();
                self.renderPlaceholderSuggestions(key, type);
            });

            $(document).on("input", "#cfLabelInput", function () {
                var editIdx = parseInt($("#cfEditIndex").val(), 10);
                if (editIdx < 0) {
                    var val = $(this).val() || "";
                    var slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
                    $("#cfKeyInput").val(slug);
                    var type = $("#cfTypeSelect").val();
                    self.renderPlaceholderSuggestions(slug, type);
                }
            });

            $(document).on("input", "#cfKeyInput", function () {
                var editIdx = parseInt($("#cfEditIndex").val(), 10);
                if (editIdx < 0) {
                    var key = $(this).val();
                    var type = $("#cfTypeSelect").val();
                    self.renderPlaceholderSuggestions(key, type);
                }
            });

            $(document).on("click", "#btnSaveCustomField", function () {
                self.saveCustomField();
            });

            $(document).on("click", ".btn-edit-custom-field, .btn-edit-field, .wf-field-ph-chip", function () {
                var idx = parseInt($(this).data("idx"), 10);
                self.openCustomFieldForm(idx);
            });

            $(document).on("click", ".btn-delete-custom-field", function () {
                var idx = parseInt($(this).data("idx"), 10);
                self.deleteCustomField(idx);
            });

            // Per-field row layout width segmented buttons (1 in a row vs 2 in a row)
            $(document).on("click", ".wf-btn-width", function (e) {
                e.preventDefault();
                if ($(this).prop("disabled")) return;
                var idx = parseInt($(this).data("idx"), 10);
                var val = parseInt($(this).data("width"), 10) || 12;
                if (self.currentForm && self.currentForm.fields && self.currentForm.fields[idx]) {
                    self.currentForm.fields[idx].col_span = val;
                    self.renderBuilderFields();
                    self.updateLivePreview();
                }
            });

            // Quick persons row layout pills (1 in a row vs 2 in a row)
            $(document).on("click", ".wf-btn-pill-layout", function () {
                var layout = parseInt($(this).data("layout"), 10);
                var colSpan = layout === 1 ? 12 : 6;
                $(".wf-btn-pill-layout").removeClass("active");
                $(this).addClass("active");

                if (self.currentForm && self.currentForm.fields) {
                    var personKeys = ["persons", "adults", "children", "infants"];
                    self.currentForm.fields.forEach(function (f) {
                        if (personKeys.indexOf(f.field_key) !== -1 || (f.label && f.label.toLowerCase().indexOf("person") !== -1)) {
                            f.col_span = colSpan;
                        }
                    });
                    self.renderBuilderFields();
                    self.updateLivePreview();
                }
            });

            // Save Form
            $(document).on("click", "#btnSaveWebForm", function (e) {
                e.preventDefault();
                self.saveForm();
            });

            // Row action buttons
            $(document).on("change", ".wf-row-status-toggle", function () {
                var id = $(this).data("id");
                var isChecked = $(this).is(":checked");
                self.toggleStatus(id, isChecked);
            });

            $(document).on("click", ".btn-duplicate-form", function () {
                var id = $(this).data("id");
                self.duplicateForm(id);
            });

            $(document).on("click", ".btn-delete-form", function () {
                var id = $(this).data("id");
                self.deleteForm(id);
            });

            // Embed Modal
            $(document).on("click", ".btn-show-embed", function () {
                var id = $(this).data("id");
                self.openEmbedModal(id);
            });

            $(document).on("click", "#btnCloseEmbedModal, #btnDoneEmbedModal", function () {
                self.closeEmbedModal();
            });

            // Copy Snippets
            $(document).on("click", ".btn-copy-url", function () {
                var url = $(this).data("url");
                self.copyText(url, this, "Copied");
            });

            $(document).on("click", "#btnCopyPublicUrl", function () {
                var url = $("#embedPublicUrlInput").val();
                self.copyText(url, this, "Copied");
            });

            $(document).on("click", "#btnCopyIframeSnippet", function () {
                var code = $("#embedIframeSnippetText").text();
                self.copyText(code, this, "Copied Code");
            });
        }
    };

    // Auto-initialize if DOM already ready
    if ($("#pluginWebFormsApp").length > 0) {
        window.V3WebForms.init();
    }
});
