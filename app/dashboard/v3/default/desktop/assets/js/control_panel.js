/**
 * Blumox Control Panel V3 Client-Side SPA Engine
 * Path: /app/dashboard/v3/default/desktop/assets/js/control_panel.js
 * Strict V3 Rule: External JS only, zero inline handlers, no duplicate API hits
 */

(function (window, $) {
  "use strict";

  // Prevent multiple initializations across SPA/PJAX navigation
  if (window._cpInitialized) {
    if (window.V3ControlPanel && $("#controlPanelApp").length) {
      window.V3ControlPanel.onPageEnter();
    }
    return;
  }
  window._cpInitialized = true;

  const V3ControlPanel = {
    activeTab: "overview",
    userRole: "administrator",
    eventsBound: false,
    appsList: [],
    sitesList: [],
    partnersList: [],
    loadedTabs: {}, // Cache of loaded tabs to eliminate duplicate network calls
    dropdownsLoaded: { apps: false, sites: false, partners: false },
    activeXhr: null,

    init: function () {
      this.onPageEnter();
    },

    initSelect2: function (selectorOrScope) {
      if (typeof $.fn.select2 !== "function") return;
      try {
        let $selects;
        const selQuery = "select.selectJS2, select.selectJS, select.cp-select2, select.cp-modal-select2, select.cp-form-select, select.v3-form-select, select";
        if (typeof selectorOrScope === "string") {
          const $target = $(selectorOrScope);
          $selects = $target.is("select") ? $target : $target.find(selQuery);
        } else if (selectorOrScope instanceof jQuery) {
          $selects = selectorOrScope.is("select") ? selectorOrScope : selectorOrScope.find(selQuery);
        } else {
          $selects = $("select.selectJS2, select.selectJS, select.cp-select2, select.cp-modal-select2, .cp-toolbar select, .cp-modal-box select, .cp-modal-dialog select");
        }

        if (!$selects || !$selects.length) return;

        $selects.each(function () {
          const $el = $(this);
          if (!$el.is("select")) return;

          // If inside a hidden tab or closed modal, postpone initialization until pane is shown
          if ($el.closest(".cp-modal-backdrop:not(.active), .cp-modal-pane:not(.active)").length) {
            return;
          }

          // If already initialized on this element, safely destroy before re-initializing
          if ($el.hasClass("select2-hidden-accessible")) {
            try {
              $el.select2("destroy");
            } catch (e) {}
          }

          const $closestModal = $el.closest(".cp-modal-box, .cp-modal-dialog");
          const isInToolbar = $el.closest(".cp-toolbar, .cp-filters-wrap").length > 0;
          let width = $el.attr("data-width") || $el.data("width");
          if (!width) {
            if ($closestModal.length) {
              width = "100%";
            } else if (isInToolbar) {
              width = "auto";
            } else {
              width = "100%";
            }
          }

          const placeholder = $el.attr("data-placeholder") || $el.data("placeholder") || $el.find("option[value='']").text() || "Select option";

          const opts = {
            width: width,
            placeholder: placeholder,
            allowClear: false,
            dropdownCssClass: "cp-select2-dropdown",
            minimumResultsForSearch: 6
          };

          if ($closestModal.length) {
            opts.dropdownParent = $closestModal;
          }

          $el.select2(opts);
        });
      } catch (err) {
        console.warn("Select2 init notice:", err);
      }
    },

    initDataTable: function (tableSelector, options) {
      if (typeof $.fn.DataTable !== "function") return null;
      try {
        const $tbl = $(tableSelector);
        if (!$tbl.length) return null;

        // Avoid initializing if table only has a loading or empty placeholder cell
        if ($tbl.find("tbody td.cp-loading-cell, tbody td.cp-table-empty-cell").length) {
          if ($.fn.DataTable.isDataTable(tableSelector)) {
            $tbl.DataTable().destroy();
          }
          return null;
        }

        if ($.fn.DataTable.isDataTable(tableSelector)) {
          $tbl.DataTable().destroy();
        }

        const defaultOpts = {
          paging: false,
          searching: false,
          info: false,
          ordering: true,
          autoWidth: false,
          order: [],
          columnDefs: [
            { targets: "no-sort", orderable: false }
          ],
          language: {
            emptyTable: "No records found"
          }
        };

        return $tbl.DataTable($.extend(true, {}, defaultOpts, options || {}));
      } catch (e) {
        console.warn("DataTable init notice:", e);
        return null;
      }
    },

    onPageEnter: function () {
      const $root = $("#controlPanelApp");
      if (!$root.length) {
        this.onPageLeave();
        return;
      }

      this.userRole = $root.attr("data-user-role") || $root.data("user-role") || "administrator";
      this.bindEvents();

      // Reset cache when entering fresh so active tab API triggers
      this.loadedTabs = {};
      this.dropdownsLoaded = { apps: false, sites: false, partners: false };

      // Determine dedicated page from URL pathname first, falling back to data-page attribute
      const path = window.location.pathname;
      let dedicatedPage = "";
      if (path.indexOf("/dashboard/apps") !== -1) {
        dedicatedPage = "apps";
      } else if (path.indexOf("/dashboard/themes") !== -1) {
        dedicatedPage = "themes";
      } else if (path.indexOf("/dashboard/sites") !== -1) {
        dedicatedPage = "sites";
      } else if (path.indexOf("/dashboard/users") !== -1) {
        dedicatedPage = "users";
      } else if (path.indexOf("/dashboard/invoices") !== -1) {
        dedicatedPage = "invoices";
      } else if (path.indexOf("/dashboard/commissions") !== -1) {
        dedicatedPage = "commissions";
      } else if (path.indexOf("/dashboard/configurations") !== -1) {
        dedicatedPage = "configurations";
      } else {
        dedicatedPage = $root.attr("data-page") || $root.data("page") || "";
      }

      if (dedicatedPage) {
        this.activeTab = dedicatedPage;

        // 1. Immediately trigger active tab content fetch so data loads instantly
        this.loadActiveTabContent();

        // 2. Initialize toolbar Select2 safely
        this.initSelect2();

        // 3. Lazy load dependent dropdowns in background
        if (dedicatedPage === "themes" || dedicatedPage === "sites") {
          this.loadAppDropdowns();
        } else if (dedicatedPage === "invoices") {
          this.loadSiteDropdowns();
          this.loadPartnerDropdowns();
        } else if (dedicatedPage === "commissions") {
          this.loadPartnerDropdowns();
          this.loadSiteDropdowns();
        }
        return;
      }

      // Read initial tab from URL hash if provided
      const hash = window.location.hash.replace("#", "").trim();
      if (hash === "configurations") {
        window.location.href = "/dashboard/configurations/";
        return;
      }
      const validTab = hash && $(`[data-tab="${hash}"]`).length ? hash : "overview";
      this.switchTab(validTab);
      this.initSelect2();
    },

    onPageLeave: function () {
      if (this.activeXhr && this.activeXhr.readyState !== 4) {
        this.activeXhr.abort();
      }
      this.loadedTabs = {};
      this.dropdownsLoaded = { apps: false, sites: false, partners: false };
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // 1. Tab Navigation (SPA tabs with data-tab)
      $(document).on("click", ".cp-tab-btn[data-tab]", function (e) {
        e.preventDefault();
        const tab = $(this).data("tab");
        self.switchTab(tab);
      });

      // Aside navigation links with data-cp-tab
      $(document).on("click", "a[data-cp-tab]", function (e) {
        const targetTab = $(this).data("cp-tab");
        if ($("#controlPanelApp").length && targetTab) {
          e.preventDefault();
          e.stopImmediatePropagation();
          self.switchTab(targetTab);
        }
      });

      // Explicit Refresh Button (Bypasses cache and re-fetches current tab + overview)
      $(document).on("click", "#btnCpRefreshData", function () {
        self.loadedTabs = {}; // Invalidate cache
        if (self.activeTab === "overview") {
          self.fetchOverview(true);
        } else {
          self.loadActiveTabContent(true);
        }
      });

      // 2. Modals open / close
      $(document).on("click", "[data-close-modal]", function () {
        $(".cp-modal-backdrop").removeClass("active");
      });

      $(document).on("click", ".cp-modal-backdrop", function (e) {
        if ($(e.target).hasClass("cp-modal-backdrop")) {
          $(this).removeClass("active");
        }
      });

      // Header quick action buttons (lazy load required dropdowns on demand)
      $(document).on("click", "#btnHeaderProvisionSite, #btnOpenProvisionModal", function () {
        self.loadAppDropdowns(false, function () {
          self.initSelect2("#modalProvisionSite");
        });
        self.loadPartnerDropdowns(false, function () {
          self.initSelect2("#modalProvisionSite");
        });
        $("#modalProvisionSite").addClass("active");
        self.initSelect2("#modalProvisionSite");
      });

      $(document).on("click", "#btnHeaderNewInvoice, #btnOpenCreateInvoiceModal", function () {
        self.loadSiteDropdowns(false, function () {
          self.initSelect2("#modalCreateInvoice");
        });
        self.loadPartnerDropdowns(false, function () {
          self.initSelect2("#modalCreateInvoice");
        });
        $("#modalCreateInvoice").addClass("active");
        self.initSelect2("#modalCreateInvoice");
      });

      $(document).on("click", "#btnOpenNewAppModal", function () {
        self.openAppModal(0);
      });

      $(document).on("click", ".btn-edit-app, .cp-app-title-link", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        if (id) {
          self.openAppModal(id);
        }
      });

      $(document).on("change", ".cp-app-status-toggle", function () {
        const id = $(this).data("id");
        const isChecked = $(this).is(":checked");
        self.toggleAppStatus(id, isChecked, $(this));
      });

      // Apps Filter Pills & Sync with Select2
      $(document).on("click", "#cpAppStatusTabs .cp-filter-pill", function () {
        $("#cpAppStatusTabs .cp-filter-pill").removeClass("active");
        $(this).addClass("active");
        const status = $(this).data("status");
        $("#cpFilterAppStatus").val(status).trigger("change.select2");
        self.fetchApps();
      });

      $(document).on("change", "#cpFilterAppStatus", function () {
        const val = $(this).val();
        $("#cpAppStatusTabs .cp-filter-pill").removeClass("active");
        $(`#cpAppStatusTabs .cp-filter-pill[data-status="${val}"]`).addClass("active");
        self.fetchApps();
      });

      $(document).on("change", "#cpSortApps", function () {
        self.fetchApps();
      });

      $(document).on("click", "#btnViewAppsGrid", function () {
        $("#btnViewAppsGrid").addClass("active");
        $("#btnViewAppsTable").removeClass("active");
        $("#cpAppsGrid").show();
        $("#cpAppsTableWrap").hide();
      });

      $(document).on("click", "#btnViewAppsTable", function () {
        $("#btnViewAppsTable").addClass("active");
        $("#btnViewAppsGrid").removeClass("active");
        $("#cpAppsGrid").hide();
        $("#cpAppsTableWrap").show();
        self.initDataTable("#cpAppsTable");
      });

      let appsSearchTimer = null;
      $(document).on("input", "#cpSearchApps", function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#btnClearSearchApps").show();
        } else {
          $("#btnClearSearchApps").hide();
        }
        clearTimeout(appsSearchTimer);
        appsSearchTimer = setTimeout(() => {
          self.fetchApps();
        }, 300);
      });

      $(document).on("click", "#btnClearSearchApps", function () {
        $("#cpSearchApps").val("");
        $(this).hide();
        self.fetchApps();
      });

      $(document).on("click", ".cp-btn-copy-slug", function (e) {
        e.preventDefault();
        const slug = $(this).data("slug");
        const btn = $(this);
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(slug).then(() => {
            btn.html('<i class="fi fi-rr-check"></i>');
            setTimeout(() => {
              btn.html('<i class="fi fi-rr-copy"></i>');
            }, 1500);
          });
        }
      });

      $(document).on("input change", "#appColorPicker", function () {
        const val = $(this).val();
        $("#appColorInput").val(val);
        self.updateAppBrandPreview();
      });

      $(document).on("input", "#appColorInput", function () {
        const val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
          $("#appColorPicker").val(val);
        }
        self.updateAppBrandPreview();
      });

      $(document).on("input change", "#appSecondaryColorPicker", function () {
        const val = $(this).val();
        $("#appSecondaryColorInput").val(val);
        self.updateAppBrandPreview();
      });

      $(document).on("input", "#appSecondaryColorInput", function () {
        const val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
          $("#appSecondaryColorPicker").val(val);
        }
        self.updateAppBrandPreview();
      });

      $(document).on("input", "#appTitleInput", function () {
        const title = $(this).val();
        $("#appPreviewTitle").text(title || "Application Preview");
        const id = parseInt($("#appIdInput").val()) || 0;
        if (id === 0) {
          const slug = title.toLowerCase().replace(/[^a-z0-9_]/g, "_").replace(/_+/g, "_").replace(/^_|_$/g, "");
          $("#appPathInput").val(slug);
          $("#appPreviewPath").text("/" + (slug || "app_path") + "/");
        }
      });

      $(document).on("click", "#btnOpenNewThemeModal", function () {
        self.loadAppDropdowns(false, function () {
          self.initSelect2("#themeAppSelect");
        });
        $("#formNewTheme")[0].reset();
        $("#themeIdInput").val("0");
        $("#modalNewTheme").addClass("active");
        self.initSelect2("#themeAppSelect");
      });

      // Themes Quick Status Filter Tabs & Sync with Select2
      $(document).on("click", "#cpThemeStatusTabs .cp-filter-pill", function () {
        $("#cpThemeStatusTabs .cp-filter-pill").removeClass("active");
        $(this).addClass("active");
        const st = $(this).data("filter-status");
        $("#cpFilterThemeStatus").val(st).trigger("change.select2");
        self.fetchThemes();
      });

      $(document).on("change", "#cpFilterThemeStatus", function () {
        const val = $(this).val();
        $("#cpThemeStatusTabs .cp-filter-pill").removeClass("active");
        $(`#cpThemeStatusTabs .cp-filter-pill[data-filter-status="${val}"]`).addClass("active");
        self.fetchThemes();
      });

      $(document).on("change", "#cpFilterThemeApp, #cpSortThemes", function () {
        self.fetchThemes();
      });

      let themesSearchTimer = null;
      $(document).on("input", "#cpSearchThemes", function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#cpClearThemesSearch").show();
        } else {
          $("#cpClearThemesSearch").hide();
        }
        clearTimeout(themesSearchTimer);
        themesSearchTimer = setTimeout(() => {
          self.fetchThemes();
        }, 300);
      });

      $(document).on("click", "#cpClearThemesSearch", function () {
        $("#cpSearchThemes").val("");
        $(this).hide();
        self.fetchThemes();
      });

      $(document).on("click", "#btnViewThemesGrid", function () {
        $("#btnViewThemesGrid").addClass("active");
        $("#btnViewThemesTable").removeClass("active");
        $("#cpThemesGrid").show();
        $("#cpThemesTableWrap").hide();
      });

      $(document).on("click", "#btnViewThemesTable", function () {
        $("#btnViewThemesTable").addClass("active");
        $("#btnViewThemesGrid").removeClass("active");
        $("#cpThemesGrid").hide();
        $("#cpThemesTableWrap").show();
        self.initDataTable("#cpThemesTable");
      });

      $(document).on("click", "#btnOpenNewUserModal", function () {
        $("#formNewUser")[0].reset();
        $("#modalNewUser").addClass("active");
        self.initSelect2("#modalNewUser");
      });

      $(document).on("click", "#btnOpenNewConfigModal", function () {
        $("#formNewConfig")[0].reset();
        $("#modalNewConfig").addClass("active");
        self.initSelect2("#modalNewConfig");
      });

      // 4. Form Submissions (Delegated for SPA safety)
      // Provision Site
      $(document).on("submit", "#formProvisionSite", function (e) {
        e.preventDefault();
        self.submitProvisionSite();
      });

      $(document).on("click", "#btnOpenManualCommissionModal", function () {
        self.loadPartnerDropdowns(true, function () {
          self.initSelect2("#modalManualCommission");
        });
        self.loadSiteDropdowns(true, function () {
          self.initSelect2("#modalManualCommission");
        });
        $("#formManualCommission")[0].reset();
        $("#modalManualCommission").addClass("active");
        self.initSelect2("#modalManualCommission");
      });

      // Register App
      $(document).on("submit", "#formNewApp", function (e) {
        e.preventDefault();
        self.submitNewApp();
      });

      // Register Theme
      $(document).on("submit", "#formNewTheme", function (e) {
        e.preventDefault();
        self.submitNewTheme();
      });

      // Create User
      $(document).on("submit", "#formNewUser", function (e) {
        e.preventDefault();
        self.submitNewUser();
      });

      // Create Invoice
      $(document).on("submit", "#formCreateInvoice", function (e) {
        e.preventDefault();
        self.submitCreateInvoice();
      });

      // Record Manual Commission
      $(document).on("submit", "#formManualCommission", function (e) {
        e.preventDefault();
        self.submitManualCommission();
      });

      // Save Configuration
      $(document).on("submit", "#formNewConfig", function (e) {
        e.preventDefault();
        self.submitNewConfig();
      });

      // Save Site Licenses & Features (Legacy Support)
      $(document).on("submit", "#formSiteLicenses", function (e) {
        e.preventDefault();
        self.submitSiteLicenses();
      });

      // =====================================================================
      // Client Sites & Renewal Reminder Events
      // =====================================================================

      // Quick Segmented Status Tabs Filter & Sync with Select2
      $(document).on("click", "#cpSiteStatusTabs .cp-filter-pill", function () {
        $("#cpSiteStatusTabs .cp-filter-pill").removeClass("active");
        $(this).addClass("active");
        const st = $(this).data("filter-status");
        $("#cpFilterSiteStatus").val(st).trigger("change.select2");
        self.sitesPage = 1;
        self.fetchSites();
      });

      $(document).on("change", "#cpFilterSiteStatus", function () {
        const val = $(this).val();
        $("#cpSiteStatusTabs .cp-filter-pill").removeClass("active");
        $(`#cpSiteStatusTabs .cp-filter-pill[data-filter-status="${val}"]`).addClass("active");
        self.sitesPage = 1;
        self.fetchSites();
      });

      $(document).on("change", "#cpFilterSiteApp, #cpFilterSitePlan, #cpSortSites", function () {
        self.sitesPage = 1;
        self.fetchSites();
      });

      let sitesSearchTimer = null;
      $(document).on("input", "#cpSearchSites", function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#cpClearSitesSearch").show();
        } else {
          $("#cpClearSitesSearch").hide();
        }
        clearTimeout(sitesSearchTimer);
        sitesSearchTimer = setTimeout(() => {
          self.sitesPage = 1;
          self.fetchSites();
        }, 300);
      });

      // Clear Search Input
      $(document).on("click", "#cpClearSitesSearch", function () {
        $("#cpSearchSites").val("");
        $(this).hide();
        self.sitesPage = 1;
        self.fetchSites();
      });

      // Pagination Clicks
      $(document).on("click", ".btn-sites-page", function () {
        const p = $(this).data("page");
        if (p && p !== self.sitesPage) {
          self.sitesPage = p;
          self.fetchSites();
        }
      });

      // Open Renewal Reminder Modal
      $(document).on("click", ".btn-send-renewal-reminder", function () {
        self.openRenewalReminderModal(this);
      });

      // Reset Reminder Template
      $(document).on("click", "#btnResetReminderTemplate", function () {
        self.generateReminderTemplate();
      });

      // Quick Extend Expiry inside Reminder Modal
      $(document).on("click", ".btn-quick-extend-expiry", function () {
        const months = $(this).data("months");
        let newDateStr = "";
        if (months === "lifetime") {
          newDateStr = "2099-12-31";
        } else {
          const cur = $("#remSumExpiryDate").text().trim();
          let base = new Date();
          if (cur && cur.length >= 10 && !isNaN(new Date(cur + "T00:00:00").getTime())) {
            const parsed = new Date(cur + "T00:00:00");
            if (parsed > base) base = parsed;
          }
          base.setMonth(base.getMonth() + parseInt(months, 10));
          newDateStr = base.toISOString().substring(0, 10);
        }
        $("#remSumExpiryDate").text(newDateStr);
        self.generateReminderTemplate(newDateStr);
      });

      // Submit Renewal Reminder Form
      $(document).on("submit", "#formRenewalReminder", function (e) {
        e.preventDefault();
        self.submitRenewalReminder();
      });

      // Open Quick Details Modal
      $(document).on("click", ".btn-view-site-details", function () {
        const id = $(this).data("id");
        self.openSiteQuickDetails(id);
      });

      // Quick Details Modal Footer Action: Send Renewal Reminder
      $(document).on("click", "#btnSqdSendReminder", function () {
        $("#modalSiteQuickDetails").removeClass("active");
        self.openRenewalReminderModal(this);
      });

      // Quick Details Modal Footer Action: Open Full Settings
      $(document).on("click", "#btnSqdOpenSettings", function () {
        $("#modalSiteQuickDetails").removeClass("active");
        const id = $(this).data("id");
        self.openSiteSettingsModal(id);
      });

      // Currency Select change in Site Settings Modal
      $(document).on("change", "#mscCurrencySelect", function () {
        const val = $(this).val();
        const currSymbols = { INR: "₹", USD: "$", EUR: "€", GBP: "£", AED: "AED " };
        const sym = currSymbols[val] || (val + " ");
        $("#mscCurrencyPrefixMonthly").text(sym);
        $("#mscCurrencyPrefixYearly").text(sym);
      });

      // Dynamic Renewal Reminder Message update when Price or Interval changes
      $(document).on("input change", "#remCustomPriceInput, #remBillingIntervalSelect", function () {
        const interval = $("#remBillingIntervalSelect").val() || "yearly";
        $("#remPriceSuffix").text(interval === "monthly" ? "/ month" : (interval === "quarterly" ? "/ quarter" : "/ year"));
        self.generateReminderTemplate();
      });

      // Channel Card Click & Checked State Visual Indicator
      $(document).on("change", ".cp-channel-card input[type='checkbox']", function () {
        const isChecked = $(this).is(":checked");
        $(this).closest(".cp-channel-card").toggleClass("is-checked", isChecked);
      });

      // Open Site Settings & Configurations Modal
      $(document).on("click", ".btn-manage-site-config, .btn-manage-site-license", function () {
        const id = $(this).data("id");
        self.openSiteSettingsModal(id);
      });

      // Modal Tab Switching
      $(document).on("click", "#siteModalNavTabs .cp-modal-nav-tab", function () {
        const paneId = $(this).data("pane");
        $("#siteModalNavTabs .cp-modal-nav-tab").removeClass("active");
        $(this).addClass("active");
        $("#modalSiteSettingsConfig .cp-modal-pane").removeClass("active");
        const $targetPane = $(`#${paneId}`);
        $targetPane.addClass("active");
        self.initSelect2($targetPane);
      });

      // Preset Pills for Team Members Limit in Config Modal
      $(document).on("click", ".btn-set-msc-user-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#mscUserLimitInput").val(val).trigger("change");
      });

      // Preset Pills for WhatsApp Device Limit in Config Modal
      $(document).on("click", ".btn-set-msc-wa-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#mscWaLimitInput").val(val).trigger("change");
      });

      // Preset Pills for Storage Quota in Config Modal
      $(document).on("click", ".btn-set-msc-storage-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#mscStorageLimitInput").val(val).trigger("change");
      });

      $(document).on("input change", "#mscUserLimitInput, #mscWaLimitInput", function () {
        self.updateMscMeters();
      });

      // Preset Pills for Quick Expiry Date Extend in Config Modal
      $(document).on("click", ".btn-msc-extend-date", function (e) {
        e.preventDefault();
        const months = $(this).data("months");
        if (months === "lifetime") {
          $("#mscExpiryInput").val("2099-12-31");
        } else {
          const cur = $("#mscExpiryInput").val();
          let base = new Date();
          if (cur && cur.length >= 10 && !isNaN(new Date(cur + "T00:00:00").getTime())) {
            const parsed = new Date(cur + "T00:00:00");
            if (parsed > base) base = parsed;
          }
          base.setMonth(base.getMonth() + parseInt(months, 10));
          $("#mscExpiryInput").val(base.toISOString().substring(0, 10));
        }
      });

      // Color Picker Sync in Config Modal
      $(document).on("input change", "#mscSiteThemeColorInput", function () {
        $("#mscSiteThemeColorHex").val($(this).val());
      });
      $(document).on("input", "#mscSiteThemeColorHex", function () {
        const val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
          $("#mscSiteThemeColorInput").val(val);
        }
      });
      $(document).on("click", ".btn-pick-color", function () {
        const color = $(this).data("color");
        $("#mscSiteThemeColorInput").val(color);
        $("#mscSiteThemeColorHex").val(color);
      });

      // Change App Select in Config Modal -> reload themes
      $(document).on("change", "#mscSiteAppSelect", function () {
        const appId = $(this).val();
        self.loadThemesForSelect(appId, "#mscSiteThemeSelect");
      });

      // Submit Site Settings Form
      $(document).on("submit", "#formSiteSettingsConfig", function (e) {
        e.preventDefault();
        self.submitSiteSettings();
      });

      // Site Parameter Management (Tab 4)
      $(document).on("click", "#btnOpenAddParamRow", function () {
        $("#mscNewParamRow").toggleClass("active");
        if ($("#mscNewParamRow").hasClass("active")) {
          $("#newParamKeyInput").focus();
        }
      });

      $(document).on("click", "#btnCancelNewParam", function () {
        $("#mscNewParamRow").removeClass("active");
        $("#newParamKeyInput").val("");
        $("#newParamValueInput").val("");
      });

      $(document).on("click", "#btnSaveNewParam", function () {
        self.saveNewSiteParameter();
      });

      $(document).on("click", ".btn-save-inline-param", function () {
        const cfgId = $(this).data("id");
        const key = $(this).data("key");
        const row = $(this).closest("tr");
        const val = row.find(".cp-inline-param-input").val();
        self.saveInlineSiteParameter(cfgId, key, val);
      });

      $(document).on("click", ".btn-delete-inline-param", function () {
        const cfgId = $(this).data("id");
        const key = $(this).data("key");
        self.deleteInlineSiteParameter(cfgId, key);
      });

      $(document).on("input", "#mscSearchSiteParams", function () {
        const q = $(this).val().toLowerCase().trim();
        $("#mscParamsTbody tr").each(function () {
          const k = ($(this).data("key") || "").toLowerCase();
          if (!q || k.indexOf(q) !== -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Legacy presets support
      $(document).on("click", ".btn-set-user-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#licUserLimitInput").val(val).trigger("change");
        self.updateLicenseMeters();
      });

      $(document).on("click", ".btn-set-wa-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#licWaLimitInput").val(val).trigger("change");
        self.updateLicenseMeters();
      });

      $(document).on("input change", "#licUserLimitInput, #licWaLimitInput", function () {
        self.updateLicenseMeters();
      });

      // 6. Action Handlers (Status toggles, mark paid, approvals)
      $(document).on("click", ".btn-toggle-site-status", function () {
        const id = $(this).data("id");
        const status = $(this).data("status") == 1 ? 0 : 1;
        self.toggleSiteStatus(id, status);
      });

      $(document).on("click", ".btn-mark-invoice-paid", function () {
        const id = $(this).data("id");
        self.markInvoicePaid(id);
      });

      $(document).on("click", ".btn-view-invoice", function () {
        const id = $(this).data("id");
        self.viewInvoiceDetails(id);
      });

      $(document).on("click", "#btnInvoicePrint", function () {
        window.print();
      });

      $(document).on("click", ".btn-approve-commission", function () {
        const id = $(this).data("id");
        self.approveCommission(id);
      });

      $(document).on("click", ".btn-payout-commission", function () {
        const id = $(this).data("id");
        self.payoutCommissionPrompt(id);
      });

      $(document).on("click", ".btn-toggle-user-status", function () {
        const id = $(this).data("id");
        const status = $(this).data("status") == 1 ? 0 : 1;
        self.toggleUserStatus(id, status);
      });

      $(document).on("click", ".btn-reset-user-password", function () {
        const id = $(this).data("id");
        self.resetUserPasswordPrompt(id);
      });
    },

    switchTab: function (tab) {
      if (!tab) return;
      if (tab === "configurations") {
        window.location.href = "/dashboard/configurations/";
        return;
      }
      this.activeTab = tab;
      window.location.hash = tab;

      $(".cp-tab-btn").removeClass("active");
      $(`.cp-tab-btn[data-tab="${tab}"]`).addClass("active");

      $("a[data-cp-tab]").parent().removeClass("active");
      $(`a[data-cp-tab="${tab}"]`).parent().addClass("active");

      $(".cp-tab-content").removeClass("active");
      const targetContent = `#tabContent${tab.charAt(0).toUpperCase() + tab.slice(1)}`;
      $(targetContent).addClass("active");

      // Lazy load filter dropdowns if this tab needs them
      if (tab === "themes" || tab === "sites") {
        this.loadAppDropdowns();
      }

      // Load content only if not already loaded in this session
      if (!this.loadedTabs[tab]) {
        this.loadActiveTabContent(false);
      }
    },

    loadActiveTabContent: function (forceRefresh = false) {
      const tab = this.activeTab;

      // Abort previous pending request if any
      if (this.activeXhr && this.activeXhr.readyState !== 4) {
        this.activeXhr.abort();
      }

      switch (tab) {
        case "overview":
          this.fetchOverview(forceRefresh);
          break;
        case "apps":
          this.fetchApps();
          break;
        case "themes":
          this.fetchThemes();
          break;
        case "sites":
          this.fetchSites();
          break;
        case "users":
          this.fetchUsers();
          break;
        case "invoices":
          this.fetchInvoices();
          break;
        case "commissions":
          this.fetchCommissions();
          break;
        case "configurations":
          this.fetchConfigurations();
          break;
      }
    },

    // ------------------------------------------------------------------------
    // 1. Overview API
    // ------------------------------------------------------------------------
    fetchOverview: function (forceRefresh = false) {
      if (this.loadedTabs["overview"] && !forceRefresh) return;

      const self = this;
      this.activeXhr = $.getJSON("/api/v3/control_panel/overview/", function (res) {
        if (res.status === "success" && res.data) {
          const d = res.data;
          $("#cpStatApps").text(d.apps.total);
          $("#cpTabBadgeApps").text(d.apps.total);
          $("#cpStatSites").text(d.sites.active);
          $("#cpTabBadgeSites").text(d.sites.total);
          $("#cpStatSitesSub").text(`${d.sites.total} total instances`);
          $("#cpStatThemes").text(d.themes.total);
          $("#cpTabBadgeThemes").text(d.themes.total);
          $("#cpStatUsers").text(d.users.total);

          const fmtRev = "₹" + parseFloat(d.financials.total_paid || 0).toLocaleString("en-IN", { minimumFractionDigits: 2 });
          $("#cpStatRevenue").text(fmtRev);
          $("#cpStatRevenueSub").text(`₹${parseFloat(d.financials.total_unpaid || 0).toLocaleString("en-IN")} pending`);

          const fmtComm = "₹" + parseFloat(d.commissions.pending_commissions || 0).toLocaleString("en-IN", { minimumFractionDigits: 2 });
          $("#cpStatCommissions").text(fmtComm);
          $("#cpStatCommissionsSub").text(`₹${parseFloat(d.commissions.paid_commissions || 0).toLocaleString("en-IN")} paid out`);

          let html = "";
          if (d.recent_sites && d.recent_sites.length) {
            d.recent_sites.forEach(function (s) {
              const statusPill = s.status == 1 ? '<span class="cp-status-pill active">Live</span>' : '<span class="cp-status-pill inactive">Suspended</span>';
              html += `<tr>
                <td><strong>${s.name}</strong></td>
                <td><a href="http://${s.domain}/dashboard/" target="_blank">${s.domain}</a></td>
                <td>${s.app_title}</td>
                <td>V${s.version}</td>
                <td>${statusPill}</td>
                <td>
                  <a href="http://${s.domain}/dashboard/" target="_blank" class="cp-btn-secondary" title="Open Site">
                    <i class="fi fi-rr-arrow-up-right-from-square"></i> Open
                  </a>
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="6" class="text-center">No provisioned sites recorded.</td></tr>';
          }
          $("#cpOverviewSitesTbody").html(html);
          self.loadedTabs["overview"] = true;
        }
      });
    },

    // ------------------------------------------------------------------------
    // 2. Apps Management (Catalog, Modify Details, Register New, Status Toggles)
    // ------------------------------------------------------------------------
    fetchApps: function () {
      const self = this;
      const search = $("#cpSearchApps").val() || "";
      const status = $("#cpFilterAppStatus").val() || "";
      const sort = $("#cpSortApps").val() || "newest";

      $("#cpAppsCountLabel").html('<i class="fi fi-rr-spinner-alt cp-spin"></i> Loading applications...');

      this.activeXhr = $.getJSON(`/api/v3/control_panel/apps/?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&sort=${encodeURIComponent(sort)}`)
        .done(function (res) {
          if (res.status === "success") {
            // 1. Update KPI Counters & Filter Badges
            if (res.metrics) {
              $("#cpAppsKpiTotal").text(res.metrics.total);
              $("#cpAppsKpiActive").text(res.metrics.active);
              $("#cpAppsKpiInactive").text(res.metrics.inactive);
              $("#cpAppsKpiThemes").text(res.metrics.total_themes);
              $("#cpAppsKpiSites").text(res.metrics.total_sites);

              $("#cpBadgeAllApps").text(res.metrics.total);
              $("#cpBadgeActiveApps").text(res.metrics.active);
              $("#cpBadgeInactiveApps").text(res.metrics.inactive);
            }

            const count = res.data ? res.data.length : 0;
            const total = res.metrics ? res.metrics.total : count;
            let countText = `Showing <strong>${count}</strong> of <strong>${total}</strong> applications`;
            if (status === "1") countText += " (Active only)";
            else if (status === "0") countText += " (Inactive only)";
            if (search) countText += ` matching "<em>${search}</em>"`;
            $("#cpAppsCountLabel").html(countText);

            // 2. Render Cards (Grid View) & Rows (Table View)
            let gridHtml = "";
            let tableHtml = "";

            if (res.data && res.data.length) {
              res.data.forEach(function (app) {
                const isLive = app.status == 1;
                const pColor = app.primary_color || "#2563eb";
                const sColor = app.secondary_color || "#60a5fa";
                const isVisible = app.visibility == 1;

                // Grid Card
                gridHtml += `<div class="cp-app-item" data-app-id="${app.id}">
                  <div class="cp-app-color-bar" style="background: linear-gradient(90deg, ${pColor}, ${sColor});"></div>
                  <div class="cp-app-body">
                    <div class="cp-app-card-header">
                      <div class="cp-app-header-left">
                        <div class="cp-app-icon-wrap" style="background: ${pColor};">
                          ${app.image ? `<img src="${app.image}" class="cp-app-icon-img" alt="${app.title}" onerror="this.onerror=null;this.parentElement.innerHTML='<i class=\\'fi fi-rr-apps\\'></i>';">` : `<i class="fi fi-rr-apps"></i>`}
                        </div>
                        <div class="cp-app-title-group">
                          <a href="javascript:void(0)" class="cp-app-title-link btn-edit-app" data-id="${app.id}" title="Modify ${app.title}">
                            ${app.title}
                          </a>
                          <span class="cp-app-id-pill">#APP-${app.id}</span>
                        </div>
                      </div>
                      <span class="cp-app-vis-badge ${isVisible ? 'visible' : ''}">
                        ${isVisible ? 'Catalog' : 'Internal'}
                      </span>
                    </div>

                    <div class="cp-app-code-pill">
                      <span>/${app.path}/</span>
                      <button type="button" class="cp-btn-copy-slug" data-slug="${app.path}" title="Copy slug to clipboard">
                        <i class="fi fi-rr-copy"></i>
                      </button>
                    </div>

                    <p class="cp-app-desc-clamp">${app.description || "Blumox Multi-Tenant Core Application Suite Module."}</p>

                    <div class="cp-app-ecosystem">
                      <a href="/dashboard/themes/?app_id=${app.id}" class="cp-app-eco-link themes" title="Browse Themes for ${app.title}">
                        <i class="fi fi-rr-palette"></i> <strong>${app.themes_count}</strong> Themes
                      </a>
                      <a href="/dashboard/sites/?app_id=${app.id}" class="cp-app-eco-link sites" title="View Sites running ${app.title}">
                        <i class="fi fi-rr-globe"></i> <strong>${app.sites_count}</strong> Sites
                      </a>
                    </div>
                  </div>

                  <div class="cp-app-card-footer">
                    <div class="cp-app-status-toggle-wrap">
                      <label class="cp-toggle" title="Toggle Active / Inactive status">
                        <input type="checkbox" class="cp-app-status-toggle" data-id="${app.id}" ${isLive ? 'checked' : ''}>
                        <span class="cp-toggle-slider"></span>
                      </label>
                      <span class="cp-app-status-text ${isLive ? 'active' : 'inactive'}">
                        ${isLive ? 'Active' : 'Inactive'}
                      </span>
                    </div>

                    <button type="button" class="cp-btn-edit-app btn-edit-app" data-id="${app.id}">
                      <i class="fi fi-rr-edit"></i> Modify
                    </button>
                  </div>
                </div>`;

                // Table Row
                tableHtml += `<tr>
                  <td><strong>#${app.id}</strong></td>
                  <td>
                    <div class="cp-app-header-left">
                      <div class="cp-app-icon-wrap cp-app-icon-mini" style="background: ${pColor};">
                        ${app.image ? `<img src="${app.image}" class="cp-app-icon-img" alt="${app.title}" onerror="this.onerror=null;this.parentElement.innerHTML='<i class=\\'fi fi-rr-apps\\'></i>';">` : `<i class="fi fi-rr-apps"></i>`}
                      </div>
                      <div>
                        <a href="javascript:void(0)" class="cp-site-title btn-edit-app" data-id="${app.id}"><strong>${app.title}</strong></a>
                        <span class="cp-site-sub"><code>/${app.path}/</code></span>
                      </div>
                    </div>
                  </td>
                  <td class="col-app-desc">
                    <span class="cp-app-desc-clamp">${app.description || "--"}</span>
                  </td>
                  <td>
                    <a href="/dashboard/themes/?app_id=${app.id}" class="cp-app-eco-link themes">
                      <i class="fi fi-rr-palette"></i> ${app.themes_count}
                    </a>
                  </td>
                  <td>
                    <a href="/dashboard/sites/?app_id=${app.id}" class="cp-app-eco-link sites">
                      <i class="fi fi-rr-globe"></i> ${app.sites_count}
                    </a>
                  </td>
                  <td>
                    <span class="cp-color-chip" style="background: ${pColor};"></span>
                    <code>${pColor}</code>
                  </td>
                  <td>
                    <span class="cp-app-vis-badge ${isVisible ? 'visible' : ''}">
                      ${isVisible ? 'Catalog' : 'Internal'}
                    </span>
                  </td>
                  <td>
                    <label class="cp-toggle" title="Toggle status">
                      <input type="checkbox" class="cp-app-status-toggle" data-id="${app.id}" ${isLive ? 'checked' : ''}>
                      <span class="cp-toggle-slider"></span>
                    </label>
                  </td>
                  <td class="text-right">
                    <button type="button" class="cp-btn-secondary btn-edit-app" data-id="${app.id}" title="Modify Details">
                      <i class="fi fi-rr-edit"></i> Modify
                    </button>
                  </td>
                </tr>`;
              });
            } else {
              gridHtml = `<div class="cp-empty-state-full">
                <i class="fi fi-rr-apps"></i>
                <h4>No Applications Found</h4>
                <p>No applications match your search or filter criteria.</p>
                <button type="button" class="cp-btn-secondary" id="btnClearSearchApps">Reset Filters</button>
              </div>`;
              tableHtml = `<tr><td colspan="9" class="cp-table-empty-cell">No applications found matching query.</td></tr>`;
            }

            $("#cpAppsGrid").html(gridHtml);
            if ($.fn.DataTable && $.fn.DataTable.isDataTable("#cpAppsTable")) {
              $("#cpAppsTable").DataTable().destroy();
            }
            $("#cpAppsTableBody").html(tableHtml);
            if ($("#cpAppsTableWrap").is(":visible")) {
              self.initDataTable("#cpAppsTable");
            }
            self.loadedTabs["apps"] = true;
          }
        })
        .fail(function (xhr, status) {
          if (status !== "abort") {
            $("#cpAppsCountLabel").text("Error loading applications.");
            $("#cpAppsGrid").html('<div class="cp-empty-state-full"><i class="fi fi-rr-cross-circle"></i><h4>Error</h4><p>Failed to load applications.</p></div>');
          }
        });
    },

    openAppModal: function (id) {
      const self = this;
      id = parseInt(id) || 0;

      if (id > 0) {
        // Edit Mode: Fetch fresh details from API
        $("#appModalHeading").text("Modify Application Details");
        $("#appModalSubheading").text("Update branding, operational settings and parameters for this application");
        $("#appIdInput").val(id);
        $("#btnSubmitAppText").text("Save Application Details");

        // Lock path slug (Immutable)
        $("#appPathInput").prop("readonly", true).prop("disabled", true);
        $("#appPathLockBadge").show();
        $("#appPathLockedNotice").show();
        $("#appPathCreateHint").hide();

        $.getJSON(`/api/v3/control_panel/apps/?app_id=${id}`, function (res) {
          if (res.status === "success" && res.data) {
            const app = res.data;
            $("#appTitleInput").val(app.title);
            $("#appPathInput").val(app.path);
            $("#appDescInput").val(app.description);
            $("#appColorInput").val(app.primary_color || "#2563eb");
            $("#appColorPicker").val(app.primary_color || "#2563eb");
            $("#appSecondaryColorInput").val(app.secondary_color || "#60a5fa");
            $("#appSecondaryColorPicker").val(app.secondary_color || "#60a5fa");
            $("#appImageInput").val(app.image);
            $("#appStatusSelect").val(app.status);
            $("#appVisibilitySelect").val(app.visibility);

            $("#appPreviewTitle").text(app.title);
            $("#appPreviewPath").text("/" + app.path + "/");
            self.updateAppBrandPreview();
            $("#modalNewApp").addClass("active");
            self.initSelect2("#appStatusSelect, #appVisibilitySelect");
          } else {
            swal("Error", "Could not load application details.", "error");
          }
        }).fail(function () {
          swal("Error", "Failed to retrieve application from server.", "error");
        });
      } else {
        // Create Mode
        $("#formNewApp")[0].reset();
        $("#appIdInput").val("0");
        $("#appModalHeading").text("Register New Application");
        $("#appModalSubheading").text("Deploy a new software product module into the platform");
        $("#btnSubmitAppText").text("Register Application");

        // Unlock path slug
        $("#appPathInput").prop("readonly", false).prop("disabled", false).val("");
        $("#appPathLockBadge").hide();
        $("#appPathLockedNotice").hide();
        $("#appPathCreateHint").show();

        $("#appColorInput").val("#2563eb");
        $("#appColorPicker").val("#2563eb");
        $("#appSecondaryColorInput").val("#60a5fa");
        $("#appSecondaryColorPicker").val("#60a5fa");
        $("#appStatusSelect").val("1");
        $("#appVisibilitySelect").val("1");

        $("#appPreviewTitle").text("Application Preview");
        $("#appPreviewPath").text("/app_path/");
        self.updateAppBrandPreview();
        $("#modalNewApp").addClass("active");
        self.initSelect2("#appStatusSelect, #appVisibilitySelect");
      }
    },

    updateAppBrandPreview: function () {
      const pColor = $("#appColorInput").val() || "#2563eb";
      const sColor = $("#appSecondaryColorInput").val() || "#60a5fa";
      $("#appPreviewAccent").css("background", `linear-gradient(90deg, ${pColor}, ${sColor})`);
      $("#appPreviewIcon").css("background", pColor);
    },

    toggleAppStatus: function (id, isChecked, $toggle) {
      const self = this;
      const status = isChecked ? 1 : 0;
      $.ajax({
        url: "/api/v3/control_panel/apps/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          action: "toggle_status",
          app_id: id,
          status: status
        }),
        success: function (res) {
          const statusText = isChecked ? "Active" : "Inactive";
          $toggle.closest(".cp-app-item").find(".cp-app-status-text")
            .text(statusText)
            .removeClass("active inactive")
            .addClass(isChecked ? "active" : "inactive");
          
          delete self.loadedTabs["overview"]; // refresh overview on next visit
          self.fetchApps();
        },
        error: function (xhr) {
          $toggle.prop("checked", !isChecked);
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to update status";
          swal("Error", err, "error");
        }
      });
    },

    submitNewApp: function () {
      const self = this;
      const appId = parseInt($("#appIdInput").val()) || 0;
      const title = $("#appTitleInput").val().trim();
      const path = $("#appPathInput").val().trim();

      if (!title) {
        swal("Required", "Please provide an application title.", "warning");
        return;
      }

      if (appId === 0 && !path) {
        swal("Required", "Please provide a unique directory path slug.", "warning");
        return;
      }

      const data = {
        action: "save",
        app_id: appId,
        app_title: title,
        app_description: $("#appDescInput").val().trim(),
        app_image: $("#appImageInput").val().trim(),
        app_primary_color: $("#appColorInput").val().trim() || "#2563eb",
        app_secondary_color: $("#appSecondaryColorInput").val().trim() || "#60a5fa",
        app_status: parseInt($("#appStatusSelect").val()) || 1,
        app_visiblity: parseInt($("#appVisibilitySelect").val()) || 1
      };

      // Only pass path when registering a new app
      if (appId === 0) {
        data.app_path = path;
      }

      const $submitBtn = $("#btnSubmitApp");
      $submitBtn.prop("disabled", true).find("span").text("Saving...");

      $.ajax({
        url: "/api/v3/control_panel/apps/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $submitBtn.prop("disabled", false).find("span").text("Save Application");
          $("#modalNewApp").removeClass("active");
          swal("Success", res.msg || (appId > 0 ? "Application updated successfully!" : "Application registered successfully!"), "success");
          self.fetchApps();
          self.loadAppDropdowns(true);
          delete self.loadedTabs["overview"]; // refresh overview on next visit
        },
        error: function (xhr) {
          $submitBtn.prop("disabled", false).find("span").text("Save Application");
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving application.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 3. Themes Matrix
    // ------------------------------------------------------------------------
    fetchThemes: function () {
      const self = this;
      const search = ($("#cpSearchThemes").val() || "").trim();
      const appId = $("#cpFilterThemeApp").val() || "";
      const status = $("#cpFilterThemeStatus").val() || "";
      const sort = $("#cpSortThemes").val() || "newest";

      $("#cpThemesCountLabel").html('<i class="fi fi-rr-spinner-alt cp-spin"></i> Loading themes catalog...');

      const url = `/api/v3/control_panel/themes/?search=${encodeURIComponent(search)}&app_id=${encodeURIComponent(appId)}&status=${encodeURIComponent(status)}&sort=${encodeURIComponent(sort)}`;

      this.activeXhr = $.getJSON(url)
        .done(function (res) {
          if (res.status === "success") {
            // 1. Update KPI Counters & Filter Badges
            if (res.metrics) {
              $("#cpThemesKpiTotal").text(res.metrics.total);
              $("#cpThemesKpiActive").text(res.metrics.active);
              $("#cpThemesKpiInactive").text(res.metrics.inactive);
              $("#cpThemesKpiApps").text(res.metrics.apps);
              $("#cpThemesKpiSites").text(res.metrics.sites);

              $("#cpBadgeAllThemes").text(res.metrics.total);
              $("#cpBadgeActiveThemes").text(res.metrics.active);
              $("#cpBadgeInactiveThemes").text(res.metrics.inactive);
            }

            const count = res.data ? res.data.length : 0;
            const total = res.metrics ? res.metrics.total : count;
            let countText = `Showing <strong>${count}</strong> of <strong>${total}</strong> themes`;
            if (status === "1") countText += " (Active only)";
            else if (status === "0") countText += " (Archived)";
            if (search) countText += ` matching "<em>${search}</em>"`;
            $("#cpThemesCountLabel").html(countText);

            // 2. Render Grid Cards & Table Rows
            let gridHtml = "";
            let tableHtml = "";

            if (res.data && res.data.length) {
              res.data.forEach(function (th) {
                const isLive = th.status == 1;
                const statusPill = isLive
                  ? '<span class="cp-status-pill active"><i class="fi fi-rr-bullet"></i> Active</span>'
                  : '<span class="cp-status-pill inactive">Archived</span>';

                // Grid Card
                gridHtml += `<div class="cp-app-item">
                  <div class="cp-app-item-top">
                    <div class="cp-app-badge-icon">
                      <i class="fi fi-rr-palette"></i>
                    </div>
                    <div class="cp-app-meta">
                      <h4>${th.title}</h4>
                      <p>App: <strong>${th.app_title}</strong> (v${th.version || '1.0'})</p>
                      <p class="cp-site-sub">Slug: <code>${th.path}</code></p>
                    </div>
                  </div>
                  <div class="cp-app-footer">
                    <span><strong>${th.sites_count}</strong> Client Sites</span>
                    ${statusPill}
                  </div>
                </div>`;

                // Table Row
                tableHtml += `<tr>
                  <td><strong>#${th.id}</strong></td>
                  <td>
                    <div class="cp-app-header-left">
                      <div class="cp-app-badge-icon" style="width:32px;height:32px;font-size:14px;border-radius:8px;">
                        <i class="fi fi-rr-palette"></i>
                      </div>
                      <div>
                        <span class="cp-site-title">${th.title}</span>
                        <span class="cp-site-sub"><code>${th.path}</code></span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <strong>${th.app_title}</strong>
                    ${th.app_path ? `<span class="cp-site-sub">/${th.app_path}/</span>` : ''}
                  </td>
                  <td><code>${th.path}</code></td>
                  <td><span class="cp-badge-plan">v${th.version || '1.0'}</span></td>
                  <td>
                    <a href="/dashboard/sites/?theme_id=${th.id}" class="cp-app-eco-link sites">
                      <i class="fi fi-rr-globe"></i> ${th.sites_count}
                    </a>
                  </td>
                  <td>${statusPill}</td>
                  <td class="text-right">
                    <a href="/dashboard/sites/?theme_id=${th.id}" class="cp-btn-secondary" title="View Sites using Theme">
                      <i class="fi fi-rr-globe"></i> Sites
                    </a>
                  </td>
                </tr>`;
              });
            } else {
              gridHtml = `<div class="cp-empty-state-full">
                <i class="fi fi-rr-palette"></i>
                <h4>No Themes Found</h4>
                <p>No themes match your search or filter criteria.</p>
                <button type="button" class="cp-btn-secondary" id="cpClearThemesSearch">Reset Filters</button>
              </div>`;
              tableHtml = `<tr><td colspan="8" class="cp-table-empty-cell">No themes found matching criteria.</td></tr>`;
            }

            $("#cpThemesGrid").html(gridHtml);

            if ($.fn.DataTable && $.fn.DataTable.isDataTable("#cpThemesTable")) {
              $("#cpThemesTable").DataTable().destroy();
            }
            $("#cpThemesTableBody").html(tableHtml);

            if ($("#cpThemesTableWrap").is(":visible")) {
              self.initDataTable("#cpThemesTable");
            }
            self.loadedTabs["themes"] = true;
          }
        })
        .fail(function (xhr, status) {
          if (status !== "abort") {
            $("#cpThemesCountLabel").text("Error loading themes.");
            $("#cpThemesGrid").html('<div class="cp-empty-state-full"><i class="fi fi-rr-cross-circle"></i><h4>Error</h4><p>Failed to load themes.</p></div>');
          }
        });
    },

    submitNewTheme: function () {
      const self = this;
      const data = {
        action: "save",
        theme_app_id: $("#themeAppSelect").val(),
        theme_title: $("#themeTitleInput").val().trim(),
        theme_path: $("#themePathInput").val().trim(),
        theme_version: $("#themeVersionInput").val().trim()
      };

      $.ajax({
        url: "/api/v3/control_panel/themes/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalNewTheme").removeClass("active");
          swal("Success", res.msg || "Theme registered successfully!", "success");
          self.fetchThemes();
          delete self.loadedTabs["overview"];
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving theme.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 4. Client Sites & Tenancy with Quotas & Renewal Management
    // ------------------------------------------------------------------------
    sitesPage: 1,

    fetchSites: function () {
      const search = ($("#cpSearchSites").val() || "").trim();
      const appId = $("#cpFilterSiteApp").val() || "";
      const plan = $("#cpFilterSitePlan").val() || "";
      const status = $("#cpFilterSiteStatus").val() || "";
      const sort = $("#cpSortSites").val() || "newest";
      const page = this.sitesPage || 1;

      if (search.length > 0) {
        $("#cpClearSitesSearch").show();
      } else {
        $("#cpClearSitesSearch").hide();
      }

      const self = this;
      const url = `/api/v3/control_panel/sites/?search=${encodeURIComponent(search)}&app_id=${encodeURIComponent(appId)}&plan=${encodeURIComponent(plan)}&status=${encodeURIComponent(status)}&sort=${encodeURIComponent(sort)}&page=${page}&limit=25`;

      this.activeXhr = $.getJSON(url)
        .done(function (res) {
          if (res.status === "success") {
            // Update KPI Metrics Cards
            if (res.metrics) {
              $("#cpSitesKpiTotal").text(res.metrics.total);
              $("#cpSitesKpiActive").text(res.metrics.active);
              $("#cpSitesKpiExpiring").text(res.metrics.expiring_soon);
              $("#cpSitesKpiSuspended").text(res.metrics.suspended);

              $("#cpBadgeAllSites").text(res.metrics.total);
              $("#cpBadgeActiveSites").text(res.metrics.active);
              $("#cpBadgeExpiringSites").text(res.metrics.expiring_soon);
              $("#cpBadgeSuspendedSites").text(res.metrics.suspended);
            }

            let html = "";
            if (res.data && res.data.length) {
              res.data.forEach(function (s) {
                const statusPill = s.status == 1 ? '<span class="cp-status-pill active">Live</span>' : '<span class="cp-status-pill inactive">Suspended</span>';
                const isLive = s.status == 1;

                // Expiry Badge calculation
                let expiryBadge = '<span class="cp-expiry-pill">No Expiry</span>';
                if (s.subscription_expiry) {
                  const expDate = new Date(s.subscription_expiry + "T00:00:00");
                  const today = new Date();
                  today.setHours(0, 0, 0, 0);
                  const diffTime = expDate.getTime() - today.getTime();
                  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                  if (expDate.getFullYear() >= 2090) {
                    expiryBadge = '<span class="cp-expiry-pill lifetime" title="Lifetime Subscription"><i class="fi fi-rr-infinite"></i> Lifetime</span>';
                  } else if (diffDays < 0) {
                    expiryBadge = `<span class="cp-expiry-pill expired" title="Subscription overdue since ${s.subscription_expiry}"><i class="fi fi-rr-exclamation"></i> Overdue ${Math.abs(diffDays)}d</span>`;
                  } else if (diffDays === 0) {
                    expiryBadge = `<span class="cp-expiry-pill expired" title="Expires today!"><i class="fi fi-rr-clock"></i> Due Today</span>`;
                  } else if (diffDays <= 30) {
                    expiryBadge = `<span class="cp-expiry-pill warning" title="Renewal due on ${s.subscription_expiry}"><i class="fi fi-rr-clock"></i> Due in ${diffDays}d</span>`;
                  } else {
                    expiryBadge = `<span class="cp-expiry-pill active" title="Active until ${s.subscription_expiry}"><i class="fi fi-rr-calendar"></i> ${s.subscription_expiry}</span>`;
                  }
                }

                // License Quotas display
                const userPct = s.user_limit > 0 ? Math.min(100, Math.round((s.users_count / s.user_limit) * 100)) : 0;
                const userChipClass = userPct >= 100 ? "is-danger" : (userPct >= 80 ? "is-warning" : "");
                const dashVer = s.dashboard_version || 3;

                html += `<tr>
                  <td>
                    <div class="cp-site-title-row">
                      <span class="cp-site-title" title="${s.name}">${s.name}</span>
                      <span class="cp-version-badge" title="Dashboard Framework Version">V${dashVer}</span>
                    </div>
                    <div class="cp-site-domain-row">
                      <a href="http://${s.domain}/dashboard/" target="_blank" class="cp-domain-link" title="Open ${s.domain}">
                        ${s.domain} <i class="fi fi-rr-arrow-up-right-from-square"></i>
                      </a>
                      <span class="cp-site-id-badge" title="Site Tenant ID">#${s.id}</span>
                    </div>
                  </td>
                  <td>
                    <div class="cp-app-tag" title="Application: ${s.app_title}">
                      <i class="fi fi-rr-apps"></i> <span>${s.app_title || "Generic"}</span>
                    </div>
                    <div class="cp-site-sub cp-theme-sub" title="Theme: ${s.theme_title}">
                      <i class="fi fi-rr-palette"></i> <span>${s.theme_title || "Default Theme"}</span>
                    </div>
                  </td>
                  <td>
                    <div class="cp-plan-row">
                      <span class="cp-badge-plan" title="Plan: ${s.subscription_plan}">${s.subscription_plan}</span>
                    </div>
                    <div class="cp-expiry-row">
                      ${expiryBadge}
                    </div>
                  </td>
                  <td>
                    <div class="cp-quota-chips-wrap">
                      <span class="cp-quota-chip ${userChipClass}" title="Users: ${s.users_count} of ${s.user_limit} used (${userPct}%)">
                        <i class="fi fi-rr-users"></i> ${s.users_count}/${s.user_limit}
                      </span>
                      <span class="cp-quota-chip" title="WhatsApp: ${s.wa_devices_count} of ${s.wa_device_limit} connected">
                        <i class="fi fi-brands-whatsapp"></i> ${s.wa_devices_count}/${s.wa_device_limit}
                      </span>
                    </div>
                  </td>
                  <td>
                    <div class="cp-client-name" title="${s.owner_name || 'Unassigned'}">
                      ${s.owner_name ? s.owner_name : '<span class="cp-site-sub">Unassigned</span>'}
                    </div>
                    ${s.owner_email ? `<div class="cp-site-sub cp-truncate-email" title="${s.owner_email}">${s.owner_email}</div>` : ""}
                  </td>
                  <td>${statusPill}</td>
                  <td class="text-right">
                    <div class="cp-action-icon-group">
                      <button type="button" class="cp-action-icon-btn btn-view-site-details" 
                        data-id="${s.id}" 
                        data-name="${s.name}" 
                        data-domain="${s.domain}" 
                        data-tooltip="View Site Details" 
                        title="View Site Details">
                        <i class="fi fi-rr-eye"></i>
                      </button>
                      <button type="button" class="cp-action-icon-btn btn-send-renewal-reminder ${s.last_reminder_sent ? 'is-reminded' : ''}" 
                        data-id="${s.id}" 
                        data-name="${s.name}" 
                        data-domain="${s.domain}" 
                        data-plan="${s.subscription_plan}" 
                        data-expiry="${s.subscription_expiry}" 
                        data-client="${s.owner_name}" 
                        data-email="${s.owner_email}" 
                        data-mobile="${s.owner_mobile}" 
                        data-last-reminder="${s.last_reminder_sent}" 
                        data-price-monthly="${s.subscription_price_monthly || 0}" 
                        data-price-yearly="${s.subscription_price_yearly || 0}" 
                        data-currency="${s.subscription_currency || 'INR'}" 
                        data-billing-interval="${s.billing_interval || 'yearly'}" 
                        data-price-override="${s.renewal_price_override ? '1' : '0'}" 
                        data-tooltip="Send Renewal Reminder" 
                        title="Send Renewal Reminder">
                        <i class="fi fi-rr-bell"></i>
                      </button>
                      <button type="button" class="cp-action-icon-btn btn-manage-site-config" 
                        data-id="${s.id}" 
                        data-name="${s.name}" 
                        data-domain="${s.domain}" 
                        data-tooltip="Site Settings &amp; Configuration" 
                        title="Site Settings &amp; Configuration">
                        <i class="fi fi-rr-settings-sliders"></i>
                      </button>
                      <button type="button" class="cp-action-icon-btn btn-toggle-site-status ${isLive ? 'is-danger' : 'is-success'}" 
                        data-id="${s.id}" 
                        data-status="${s.status}" 
                        data-tooltip="${isLive ? 'Suspend Tenant' : 'Activate Tenant'}" 
                        title="${isLive ? 'Suspend Tenant' : 'Activate Tenant'}">
                        <i class="fi ${isLive ? 'fi-rr-ban' : 'fi-rr-play'}"></i>
                      </button>
                      <a href="http://${s.domain}/dashboard/" target="_blank" class="cp-action-icon-btn" 
                        data-tooltip="Open Live Dashboard" 
                        title="Open Live Dashboard">
                        <i class="fi fi-rr-arrow-up-right-from-square"></i>
                      </a>
                    </div>
                  </td>
                </tr>`;
              });
            } else {
              html = '<tr><td colspan="7" class="text-center cp-table-empty-cell"><i class="fi fi-rr-globe"></i><br>No client sites found matching criteria.</td></tr>';
            }

            if ($.fn.DataTable && $.fn.DataTable.isDataTable("#cpSitesTable")) {
              $("#cpSitesTable").DataTable().destroy();
            }
            $("#cpSitesTbody").html(html);
            self.initDataTable("#cpSitesTable");

            // Pagination rendering
            const total = res.total || 0;
            const curPage = res.page || 1;
            const limit = res.limit || 25;
            const totalPages = res.total_pages || 1;

            const start = total > 0 ? (curPage - 1) * limit + 1 : 0;
            const end = Math.min(total, curPage * limit);
            $("#cpSitesPageStart").text(start);
            $("#cpSitesPageEnd").text(end);
            $("#cpSitesTotalCount").text(total);

            let paginationHtml = "";
            if (totalPages > 1) {
              if (curPage > 1) {
                paginationHtml += `<button type="button" class="cp-pagination-btn btn-sites-page" data-page="${curPage - 1}"><i class="fi fi-rr-angle-left"></i> Prev</button>`;
              }
              for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= curPage - 2 && i <= curPage + 2)) {
                  paginationHtml += `<button type="button" class="cp-pagination-btn btn-sites-page ${i === curPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
                } else if (i === curPage - 3 || i === curPage + 3) {
                  paginationHtml += `<span class="cp-pagination-ellipsis">...</span>`;
                }
              }
              if (curPage < totalPages) {
                paginationHtml += `<button type="button" class="cp-pagination-btn btn-sites-page" data-page="${curPage + 1}">Next <i class="fi fi-rr-angle-right"></i></button>`;
              }
            }
            $("#cpSitesPaginationControls").html(paginationHtml);
            self.loadedTabs["sites"] = true;
          }
        })
        .fail(function (xhr, status) {
          if (status !== "abort") {
            $("#cpSitesTbody").html('<tr><td colspan="8" class="text-center cp-table-empty-cell"><i class="fi fi-rr-cross-circle"></i><br>Error loading client sites. Please refresh.</td></tr>');
          }
        });
    },

    submitProvisionSite: function () {
      const self = this;
      const data = {
        action: "provision",
        site_name: $("#siteNameInput").val().trim(),
        site_domain: $("#siteDomainInput").val().trim(),
        site_app_id: $("#siteAppSelect").val(),
        site_theme_id: $("#siteThemeSelect").val(),
        owner_email: $("#siteOwnerEmailInput").val().trim(),
        partner_id: $("#sitePartnerSelect").val(),
        user_limit: $("#provUserLimitInput").val() || 10,
        whatsapp_device_limit: $("#provWaLimitInput").val() || 1
      };

      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalProvisionSite").removeClass("active");
          $("#formProvisionSite")[0].reset();
          swal("Success", res.msg || "Site provisioned successfully!", "success");
          self.fetchSites();
          delete self.loadedTabs["overview"];
          self.loadSiteDropdowns(true);
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error provisioning site.";
          swal("Error", err, "error");
        }
      });
    },

    toggleSiteStatus: function (id, status) {
      const self = this;
      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "toggle_status", site_id: id, status: status }),
        success: function (res) {
          swal("Updated", res.msg, "success");
          self.fetchSites();
          delete self.loadedTabs["overview"];
        }
      });
    },

    // ------------------------------------------------------------------------
    // 5. Site Quick Details Overview Modal
    // ------------------------------------------------------------------------
    openSiteQuickDetails: function (siteId) {
      const self = this;
      siteId = parseInt(siteId) || 0;
      if (siteId <= 0) return;

      $("#modalSiteQuickDetails").addClass("active");
      $("#sqdSiteName").text("Loading tenant details...");
      $("#sqdSiteDomainSub").text("Fetching live parameters from server...");
      $("#sqdFeatureChips").html('<span class="cp-site-sub"><i class="fi fi-rr-spinner-alt cp-spin"></i> Loading features...</span>');

      $.getJSON(`/api/v3/control_panel/sites/?action=get_license&site_id=${siteId}`, function (res) {
        if (res.status === "success" && res.data) {
          const d = res.data;

          // Header
          $("#sqdSiteName").text(d.site_name || "Client Site");
          $("#sqdSiteDomainSub").text(`Domain: ${d.site_domain} • App: ${d.app_title} (#${d.site_id})`);
          if (d.site_status == 1) {
            $("#sqdSiteStatusPill").attr("class", "cp-status-pill active").text("Live");
          } else {
            $("#sqdSiteStatusPill").attr("class", "cp-status-pill inactive").text("Suspended");
          }

          // Stat Cards
          $("#sqdPlanBadge").text(d.subscription_plan || "Professional SaaS");
          let expiryText = "No Expiry";
          if (d.subscription_expiry) {
            const expDate = new Date(d.subscription_expiry.substring(0, 10) + "T00:00:00");
            if (expDate.getFullYear() >= 2090) {
              expiryText = "Lifetime Subscription";
            } else {
              const today = new Date();
              today.setHours(0, 0, 0, 0);
              const diffDays = Math.ceil((expDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
              if (diffDays < 0) {
                expiryText = `Overdue ${Math.abs(diffDays)} days`;
              } else if (diffDays === 0) {
                expiryText = "Expires Today";
              } else {
                expiryText = `${d.subscription_expiry.substring(0, 10)} (${diffDays} days left)`;
              }
            }
          }
          $("#sqdExpiryText").text(expiryText);
          $("#sqdUsersQuota").text(`${d.active_users || 0} / ${d.user_limit || 10}`);
          $("#sqdWaQuota").text(`${d.active_wa_devices || 0} / ${d.wa_device_limit || 1}`);
          const storageGb = Math.round(((d.storage_limit_mb || 5120) / 1024) * 10) / 10;
          $("#sqdStorageQuota").text(`${storageGb} GB`);

          // Identity & Platform
          $("#sqdSiteIdVersion").html(`#${d.site_id} &bull; <span class="cp-version-badge">V${d.dashboard_version || 3}</span>`);
          $("#sqdDomainLink").attr("href", `http://${d.site_domain}/dashboard/`).html(`${d.site_domain} <i class="fi fi-rr-arrow-up-right-from-square"></i>`);
          $("#sqdFullUrl").text(d.site_url || `http://${d.site_domain}/dashboard/`);
          $("#sqdAppTitle").text(d.app_title || "Core Application");
          $("#sqdThemeTitle").text(d.theme_title || "Default Theme");

          // Client & Renewal
          $("#sqdOwnerName").text(d.owner_name || "Unassigned");
          $("#sqdOwnerEmail").text(d.owner_email || "N/A");
          $("#sqdOwnerMobile").text(d.owner_mobile || "N/A");
          $("#sqdLastReminder").text(d.last_reminder_sent ? d.last_reminder_sent : "Never Sent");
          $("#sqdReminderCount").text(d.reminder_count || 0);

          // Pricing Summary
          const currSymbols = { INR: "₹", USD: "$", EUR: "€", GBP: "£", AED: "AED " };
          const cur = d.subscription_currency || "INR";
          const sym = currSymbols[cur] || (cur + " ");
          const pMonthly = parseFloat(d.subscription_price_monthly) || 0;
          const pYearly = parseFloat(d.subscription_price_yearly) || 0;
          const isOverridden = d.renewal_price_override !== false;

          let pricingSummary = "Standard Plan Pricing";
          if (pMonthly > 0 || pYearly > 0) {
            pricingSummary = `${sym}${pMonthly.toLocaleString()}/mo &bull; ${sym}${pYearly.toLocaleString()}/yr`;
            if (isOverridden) {
              pricingSummary += ' <span class="cp-override-pill">Custom Rate</span>';
            }
          }
          $("#sqdPricingDetail").html(pricingSummary);

          // Active Features Chips
          const feats = d.features || {};
          const featLabels = [
            { key: "whatsapp_enable", name: "WhatsApp CRM", icon: "fi-brands-whatsapp" },
            { key: "feature_leads", name: "Lead Flow & Stages", icon: "fi-rr-funnel" },
            { key: "feature_accounts", name: "Financial Invoicing", icon: "fi-rr-file-invoice-dollar" },
            { key: "feature_transporters", name: "Fleet & Transporters", icon: "fi-rr-car" },
            { key: "feature_packages", name: "Tour Packages", icon: "fi-rr-box-open" },
            { key: "site_custom_domain", name: "Custom Domain", icon: "fi-rr-globe" },
            { key: "feature_api_access", name: "REST API Access", icon: "fi-rr-key" }
          ];

          let fHtml = "";
          featLabels.forEach(function (f) {
            const isEnabled = !!feats[f.key];
            fHtml += `
              <span class="cp-sqd-feature-chip ${isEnabled ? 'active' : 'disabled'}">
                <i class="fi ${isEnabled ? 'fi-rr-check' : 'fi-rr-cross'}"></i>
                <i class="fi ${f.icon}"></i>
                ${f.name}
              </span>
            `;
          });
          $("#sqdFeatureChips").html(fHtml);

          // Wire CTA button datasets for one-click modal opening
          $("#btnSqdSendReminder")
            .data("id", d.site_id)
            .data("name", d.site_name)
            .data("domain", d.site_domain)
            .data("plan", d.subscription_plan)
            .data("expiry", d.subscription_expiry)
            .data("client", d.owner_name)
            .data("email", d.owner_email)
            .data("mobile", d.owner_mobile)
            .data("last-reminder", d.last_reminder_sent)
            .data("price-monthly", pMonthly)
            .data("price-yearly", pYearly)
            .data("currency", cur)
            .data("billing-interval", d.billing_interval || "yearly")
            .data("price-override", isOverridden ? "1" : "0");

          $("#btnSqdOpenSettings").data("id", d.site_id);
          $("#btnSqdOpenLive").attr("href", `http://${d.site_domain}/dashboard/`);
        } else {
          $("#sqdSiteName").text("Failed to load details");
          $("#sqdSiteDomainSub").text(res.msg || "An unexpected error occurred");
        }
      }).fail(function () {
        $("#sqdSiteName").text("Error connecting to server");
        $("#sqdSiteDomainSub").text("Please check your network and try again.");
      });
    },

    // ------------------------------------------------------------------------
    // 6. Renewal Reminder Workflow
    // ------------------------------------------------------------------------
    openRenewalReminderModal: function (btn) {
      const $btn = $(btn);
      const siteId = $btn.data("id");
      const siteName = $btn.data("name") || "";
      const domain = $btn.data("domain") || "";
      const plan = $btn.data("plan") || "Professional SaaS";
      const expiry = $btn.data("expiry") || "";
      const clientName = $btn.data("client") || "Valued Partner";
      const email = $btn.data("email") || "";
      const mobile = $btn.data("mobile") || "";
      const lastReminder = $btn.data("last-reminder") || "";
      const priceMonthly = parseFloat($btn.data("price-monthly")) || 0;
      const priceYearly = parseFloat($btn.data("price-yearly")) || 0;
      const currency = $btn.data("currency") || "INR";
      const billingInterval = $btn.data("billing-interval") || "yearly";

      const currSymbols = { INR: "₹", USD: "$", EUR: "€", GBP: "£", AED: "AED " };
      const currSym = currSymbols[currency] || (currency + " ");

      $("#remSiteIdInput").val(siteId);
      $("#remSumSiteName").text(siteName);
      $("#remSumDomain").text(domain);
      $("#remSumClientName").text(clientName);
      $("#remSumContacts").text((mobile ? mobile + " • " : "") + (email || "No email configured"));
      $("#remSumPlan").text(plan);
      $("#remSumExpiryDate").text(expiry || "No expiry set");

      // Set Renewal Pricing in Reminder Summary and inputs
      const defaultPrice = (billingInterval === "monthly") ? priceMonthly : priceYearly;
      $("#remCurrencyPrefix").text(currSym);
      $("#remCustomPriceInput").val(defaultPrice > 0 ? defaultPrice : "");
      $("#remBillingIntervalSelect").val(billingInterval);
      $("#remPriceSuffix").text(billingInterval === "monthly" ? "/ month" : (billingInterval === "quarterly" ? "/ quarter" : "/ year"));

      const priceSummaryText = defaultPrice > 0 ? `${currSym}${defaultPrice.toLocaleString()}` : "Standard Rate";
      $("#remSumPrice").text(priceSummaryText);
      $("#remSumInterval").text(billingInterval.toUpperCase() + (defaultPrice > 0 ? " (Custom)" : ""));

      // Calculate days remaining/overdue badge
      let badgeHtml = '<span class="cp-expiry-pill">No Expiry</span>';
      if (expiry) {
        const expDate = new Date(expiry + "T00:00:00");
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const diffDays = Math.ceil((expDate - today) / (1000 * 60 * 60 * 24));
        if (expDate.getFullYear() >= 2090) {
          badgeHtml = '<span class="cp-expiry-pill lifetime">Lifetime</span>';
        } else if (diffDays < 0) {
          badgeHtml = `<span class="cp-expiry-pill expired">Overdue by ${Math.abs(diffDays)}d</span>`;
        } else if (diffDays === 0) {
          badgeHtml = '<span class="cp-expiry-pill expired">Due Today</span>';
        } else if (diffDays <= 30) {
          badgeHtml = `<span class="cp-expiry-pill warning">Due in ${diffDays}d</span>`;
        } else {
          badgeHtml = `<span class="cp-expiry-pill active">Valid (${diffDays}d left)</span>`;
        }
      }
      $("#remSumExpiryBadge").replaceWith($(badgeHtml).attr("id", "remSumExpiryBadge"));

      // Last reminder info
      if (lastReminder) {
        $("#remHistoryText").html(`Last renewal reminder sent on <strong>${lastReminder}</strong>`);
      } else {
        $("#remHistoryText").text("No renewal reminders sent yet for this billing cycle.");
      }

      // Channel toggles & text
      $("#remWaRecipientText").text(mobile ? `To: ${mobile}` : "To: (No mobile configured - skipped)");
      $("#remEmailRecipientText").text(email ? `To: ${email}` : "To: (No email configured - skipped)");
      $("#chkChannelWhatsApp").prop("checked", !!mobile).prop("disabled", !mobile);
      $("#chkChannelEmail").prop("checked", !!email).prop("disabled", !email);

      // Store data on form for resetting template or extending
      $("#formRenewalReminder").data("site-data", {
        siteId, siteName, domain, plan, expiry, clientName, email, mobile
      });

      this.generateReminderTemplate();
      $("#modalRenewalReminder").addClass("active");
      this.initSelect2("#modalRenewalReminder");
    },

    generateReminderTemplate: function (overrideExpiry) {
      const d = $("#formRenewalReminder").data("site-data") || {};
      const exp = overrideExpiry || d.expiry || "Upcoming Due Date";
      const customPrice = parseFloat($("#remCustomPriceInput").val()) || 0;
      const interval = $("#remBillingIntervalSelect").val() || "yearly";
      const currSym = $("#remCurrencyPrefix").text() || "₹";

      let priceText = "Standard Plan Rate";
      const intervalText = interval === "monthly" ? "per month" : (interval === "quarterly" ? "per quarter" : "per year");
      if (customPrice > 0) {
        priceText = `${currSym}${customPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${intervalText})`;
      }

      const msg = `Dear ${d.clientName || 'Partner'},\n\nThis is a friendly reminder that your subscription for ${d.siteName || d.domain} (${d.domain}) is due for renewal.\n\nPlan Tier: ${d.plan || 'Professional SaaS'}\nRenewal Due Date: ${exp}\nRenewal Investment: ${priceText}\n\nPlease renew your subscription to ensure uninterrupted service, team access, and automated WhatsApp workflows.\n\nRenewal & Dashboard Access: http://${d.domain}/dashboard/\n\nWarm regards,\nBlumox Operations Team`;
      $("#remCustomMessageInput").val(msg);
    },

    submitRenewalReminder: function () {
      const self = this;
      const siteId = $("#remSiteIdInput").val();
      const channels = [];
      if ($("#chkChannelWhatsApp").is(":checked")) channels.push("whatsapp");
      if ($("#chkChannelEmail").is(":checked")) channels.push("email");

      if (!channels.length) {
        swal("Channel Required", "Please select at least one delivery channel (WhatsApp or Email).", "warning");
        return;
      }

      const message = $("#remCustomMessageInput").val().trim();
      if (!message) {
        swal("Message Required", "Please enter a reminder message body.", "warning");
        return;
      }

      const $btn = $("#btnSubmitRenewalReminder");
      $btn.prop("disabled", true).find("span").text("Sending...");

      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          action: "send_renewal_reminder",
          site_id: siteId,
          channels: channels,
          message: message,
          renewal_price: parseFloat($("#remCustomPriceInput").val()) || 0,
          billing_interval: $("#remBillingIntervalSelect").val() || "yearly"
        }),
        success: function (res) {
          $btn.prop("disabled", false).find("span").text("Send Renewal Reminder");
          $("#modalRenewalReminder").removeClass("active");
          const deliveryInfo = res.data && res.data.delivery ? Object.entries(res.data.delivery).map(([k, v]) => `${k.toUpperCase()}: ${v}`).join("\n") : "";
          swal("Reminder Dispatched", (res.msg || "Renewal reminder processed successfully!") + "\n\n" + deliveryInfo, "success");
          self.fetchSites();
        },
        error: function (xhr) {
          $btn.prop("disabled", false).find("span").text("Send Renewal Reminder");
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error sending renewal reminder.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 6. Comprehensive Site Settings, Licenses & Configurations Modal
    // ------------------------------------------------------------------------
    openSiteSettingsModal: function (siteId) {
      const self = this;
      $("#mscSiteIdInput").val(siteId);

      // Default active pane to General
      $("#siteModalNavTabs .cp-modal-nav-tab").removeClass("active");
      $('[data-pane="tabPaneGeneral"]').addClass("active");
      $("#modalSiteSettingsConfig .cp-modal-pane").removeClass("active");
      $("#tabPaneGeneral").addClass("active");

      $.getJSON(`/api/v3/control_panel/sites/?action=get_license&site_id=${siteId}`, function (res) {
        if (res.status === "success" && res.data) {
          const d = res.data;
          $("#mscSiteTitle").text(`Manage Settings: ${d.site_name}`);
          $("#mscSiteDomainSub").text(`Domain: ${d.site_domain} • App: ${d.app_title} (#${d.site_id})`);

          // Tab 1: General & Identity
          $("#mscSiteNameInput").val(d.site_name);
          $("#mscSiteDomainInput").val(d.site_domain);
          $("#mscSiteUrlInput").val(d.site_url);
          $("#mscSiteDashVersionSelect").val(d.dashboard_version || 3).trigger("change.select2");
          const themeColor = d.site_theme_color || "#2563eb";
          $("#mscSiteThemeColorInput").val(themeColor);
          $("#mscSiteThemeColorHex").val(themeColor);

          // Populate Apps & Themes dropdowns
          self.loadAppDropdowns(false, function () {
            $("#mscSiteAppSelect").val(d.site_app_id).trigger("change.select2");
            self.loadThemesForSelect(d.site_app_id, "#mscSiteThemeSelect", d.site_theme_id);
          });

          // Tab 2: Licenses & Quotas
          $("#mscUserLimitInput").val(d.user_limit);
          $("#mscWaLimitInput").val(d.wa_device_limit);
          $("#mscStorageLimitInput").val(d.storage_limit_mb || 5120);
          $("#mscPlanSelect").val(d.subscription_plan || "Professional SaaS").trigger("change.select2");
          $("#mscExpiryInput").val(d.subscription_expiry ? d.subscription_expiry.substring(0, 10) : "");
          $("#mscSiteStatusSelect").val(d.site_status).trigger("change.select2");

          // Subscription Pricing & Renewal Overrides
          const currSymbols = { INR: "₹", USD: "$", EUR: "€", GBP: "£", AED: "AED " };
          const pMonthly = parseFloat(d.subscription_price_monthly) || 0;
          const pYearly = parseFloat(d.subscription_price_yearly) || 0;
          $("#mscPriceMonthlyInput").val(pMonthly > 0 ? pMonthly : "");
          $("#mscPriceYearlyInput").val(pYearly > 0 ? pYearly : "");
          const cur = d.subscription_currency || "INR";
          $("#mscCurrencySelect").val(cur).trigger("change.select2");
          const sym = currSymbols[cur] || (cur + " ");
          $("#mscCurrencyPrefixMonthly").text(sym);
          $("#mscCurrencyPrefixYearly").text(sym);
          $("#mscBillingIntervalSelect").val(d.billing_interval || "yearly").trigger("change.select2");
          $("#mscPriceOverrideSwitch").prop("checked", d.renewal_price_override !== false);
          $("#mscRenewalNotesInput").val(d.renewal_pricing_notes || "");

          // Store current active counts on form for live calculation
          $("#formSiteSettingsConfig").data("active-users", d.active_users);
          $("#formSiteSettingsConfig").data("active-wa", d.active_wa_devices);

          // Render meters
          self.renderMscMeters(d.active_users, d.user_limit, d.active_wa_devices, d.wa_device_limit);

          // Tab 3: Feature Modules
          $("#mscSwWhatsapp").prop("checked", !!d.features.whatsapp_enable);
          $("#mscSwLeads").prop("checked", !!d.features.feature_leads);
          $("#mscSwAccounts").prop("checked", !!d.features.feature_accounts);
          $("#mscSwTransporters").prop("checked", !!d.features.feature_transporters);
          $("#mscSwPackages").prop("checked", !!d.features.feature_packages);
          $("#mscSwCustomDomain").prop("checked", !!d.features.site_custom_domain);
          $("#mscSwApiAccess").prop("checked", !!d.features.feature_api_access);

          // Tab 4: Site Parameters
          self.fetchSiteParameters(siteId);

          $("#modalSiteSettingsConfig").addClass("active");
          const $activePane = $("#modalSiteSettingsConfig .cp-modal-pane.active");
          self.initSelect2($activePane.length ? $activePane : "#modalSiteSettingsConfig");
        }
      });
    },

    loadThemesForSelect: function (appId, targetSelect = "#mscSiteThemeSelect", selectedThemeId = 0) {
      const self = this;
      const $sel = $(targetSelect);
      $sel.html('<option value="0">Loading themes...</option>');
      const url = appId ? `/api/v3/control_panel/themes/?app_id=${encodeURIComponent(appId)}` : "/api/v3/control_panel/themes/";
      $.getJSON(url, function (res) {
        let opts = '<option value="0">Default System Theme</option>';
        if (res.status === "success" && res.data && res.data.length) {
          res.data.forEach(function (t) {
            const isSel = selectedThemeId && parseInt(selectedThemeId, 10) === parseInt(t.id, 10) ? " selected" : "";
            opts += `<option value="${t.id}"${isSel}>${t.title} (${t.path})</option>`;
          });
        }
        $sel.html(opts);
        self.initSelect2($sel);
      });
    },

    renderMscMeters: function (usersActive, usersLimit, waActive, waLimit) {
      const userPct = usersLimit > 0 ? Math.min(100, Math.round((usersActive / usersLimit) * 100)) : 0;
      $("#mscMeterUsersText").text(`${usersActive} / ${usersLimit} (${userPct}%)`);
      const $userFill = $("#mscMeterUsersFill");
      $userFill.css("width", `${userPct}%`).removeClass("warning danger");
      if (userPct >= 100) $userFill.addClass("danger");
      else if (userPct >= 80) $userFill.addClass("warning");

      const waPct = waLimit > 0 ? Math.min(100, Math.round((waActive / waLimit) * 100)) : 0;
      $("#mscMeterWaText").text(`${waActive} / ${waLimit} (${waPct}%)`);
      const $waFill = $("#mscMeterWaFill");
      $waFill.css("width", `${waPct}%`).removeClass("warning danger");
      if (waPct >= 100) $waFill.addClass("danger");
      else if (waPct >= 80) $waFill.addClass("warning");
    },

    updateMscMeters: function () {
      const activeUsers = parseInt($("#formSiteSettingsConfig").data("active-users"), 10) || 0;
      const userLimit = parseInt($("#mscUserLimitInput").val(), 10) || 1;
      const activeWa = parseInt($("#formSiteSettingsConfig").data("active-wa"), 10) || 0;
      const waLimit = parseInt($("#mscWaLimitInput").val(), 10) || 1;
      this.renderMscMeters(activeUsers, userLimit, activeWa, waLimit);
    },

    submitSiteSettings: function () {
      const self = this;
      const siteId = $("#mscSiteIdInput").val();
      const $btn = $("#btnSubmitSiteSettings");
      $btn.prop("disabled", true).find("span").text("Saving...");

      const data = {
        action: "save_settings",
        site_id: siteId,
        site_name: $("#mscSiteNameInput").val().trim(),
        site_url: $("#mscSiteUrlInput").val().trim(),
        site_app_id: $("#mscSiteAppSelect").val(),
        site_theme_id: $("#mscSiteThemeSelect").val(),
        site_dashboard_version: $("#mscSiteDashVersionSelect").val(),
        site_theme_color: $("#mscSiteThemeColorInput").val(),
        user_limit: $("#mscUserLimitInput").val(),
        whatsapp_device_limit: $("#mscWaLimitInput").val(),
        storage_limit_mb: $("#mscStorageLimitInput").val(),
        subscription_plan: $("#mscPlanSelect").val(),
        subscription_expiry: $("#mscExpiryInput").val(),
        subscription_price_monthly: parseFloat($("#mscPriceMonthlyInput").val()) || 0,
        subscription_price_yearly: parseFloat($("#mscPriceYearlyInput").val()) || 0,
        subscription_currency: $("#mscCurrencySelect").val() || "INR",
        billing_interval: $("#mscBillingIntervalSelect").val() || "yearly",
        renewal_price_override: $("#mscPriceOverrideSwitch").is(":checked") ? 1 : 0,
        renewal_pricing_notes: $("#mscRenewalNotesInput").val().trim(),
        site_status: $("#mscSiteStatusSelect").val(),
        features: {
          whatsapp_enable: $("#mscSwWhatsapp").is(":checked") ? 1 : 0,
          feature_leads: $("#mscSwLeads").is(":checked") ? 1 : 0,
          feature_accounts: $("#mscSwAccounts").is(":checked") ? 1 : 0,
          feature_transporters: $("#mscSwTransporters").is(":checked") ? 1 : 0,
          feature_packages: $("#mscSwPackages").is(":checked") ? 1 : 0,
          site_custom_domain: $("#mscSwCustomDomain").is(":checked") ? 1 : 0,
          feature_api_access: $("#mscSwApiAccess").is(":checked") ? 1 : 0
        }
      };

      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $btn.prop("disabled", false).find("span").text("Save Site Settings");
          $("#modalSiteSettingsConfig").removeClass("active");
          swal("Updated", res.msg || "Site settings and configurations updated successfully!", "success");
          self.fetchSites();
        },
        error: function (xhr) {
          $btn.prop("disabled", false).find("span").text("Save Site Settings");
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving site settings.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // Tab 4: Site Parameter Overrides (isw_site_configuration)
    // ------------------------------------------------------------------------
    fetchSiteParameters: function (siteId) {
      $("#mscParamsTbody").html('<tr><td colspan="4" class="text-center cp-loading-cell"><i class="fi fi-rr-spinner-alt cp-spin"></i> Loading configuration overrides...</td></tr>');
      $("#mscNewParamRow").removeClass("active");
      $("#newParamKeyInput").val("");
      $("#newParamValueInput").val("");

      $.getJSON(`/api/v3/control_panel/configurations/?scope=site&site_id=${siteId}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (cfg) {
              html += `<tr data-key="${cfg.key}">
                <td><strong><code>${cfg.key}</code></strong></td>
                <td>
                  <input type="text" class="cp-form-input cp-inline-param-input" value="${cfg.value}">
                </td>
                <td><span class="cp-status-pill site">Site Override</span></td>
                <td>
                  <button type="button" class="cp-btn-secondary btn-save-inline-param" data-id="${cfg.id}" data-key="${cfg.key}" title="Save Override Value">
                    <i class="fi fi-rr-disk"></i>
                  </button>
                  <button type="button" class="cp-btn-secondary btn-delete-inline-param" data-id="${cfg.id}" data-key="${cfg.key}" title="Delete Override">
                    <i class="fi fi-rr-trash"></i>
                  </button>
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="4" class="text-center">No site-specific parameter overrides defined. Global defaults apply.</td></tr>';
          }
          $("#mscParamsTbody").html(html);
        }
      });
    },

    saveNewSiteParameter: function () {
      const self = this;
      const siteId = $("#mscSiteIdInput").val();
      const key = $("#newParamKeyInput").val().trim();
      const value = $("#newParamValueInput").val().trim();

      if (!key) {
        swal("Key Required", "Please enter a configuration parameter key.", "warning");
        return;
      }

      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          action: "save",
          key: key,
          value: value,
          site_id: siteId,
          global: 0,
          status: 1
        }),
        success: function (res) {
          $("#mscNewParamRow").removeClass("active");
          $("#newParamKeyInput").val("");
          $("#newParamValueInput").val("");
          swal("Saved", res.msg || "Configuration parameter saved!", "success");
          self.fetchSiteParameters(siteId);
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving parameter.";
          swal("Error", err, "error");
        }
      });
    },

    saveInlineSiteParameter: function (cfgId, key, val) {
      const self = this;
      const siteId = $("#mscSiteIdInput").val();
      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          action: "update_value",
          config_id: cfgId,
          value: val
        }),
        success: function (res) {
          swal("Saved", res.msg || `Updated ${key}`, "success");
          self.fetchSiteParameters(siteId);
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error updating parameter.";
          swal("Error", err, "error");
        }
      });
    },

    deleteInlineSiteParameter: function (cfgId, key) {
      const self = this;
      const siteId = $("#mscSiteIdInput").val();
      swal({
        title: "Delete Parameter Override?",
        text: `Are you sure you want to delete parameter override "${key}"? Global default value will apply.`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        confirmButtonText: "Yes, delete it"
      }, function (isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: "/api/v3/control_panel/configurations/",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
              action: "delete",
              config_id: cfgId
            }),
            success: function (res) {
              swal("Deleted", res.msg || "Parameter deleted.", "success");
              self.fetchSiteParameters(siteId);
            },
            error: function (xhr) {
              const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error deleting parameter.";
              swal("Error", err, "error");
            }
          });
        }
      });
    },

    // Legacy Fallback Helpers
    openSiteLicensesModal: function (siteId) {
      this.openSiteSettingsModal(siteId);
    },

    renderMeter: function (meterPrefix, current, limit) {
      const pct = limit > 0 ? Math.min(100, Math.round((current / limit) * 100)) : 0;
      $(`#${meterPrefix}Text`).text(`${current} / ${limit} (${pct}%)`);
      const $fill = $(`#${meterPrefix}Fill`);
      $fill.css("width", `${pct}%`).removeClass("warning danger");
      if (pct >= 100) $fill.addClass("danger");
      else if (pct >= 80) $fill.addClass("warning");
    },

    updateLicenseMeters: function () {
      this.updateMscMeters();
    },

    submitSiteLicenses: function () {
      this.submitSiteSettings();
    },

    // ------------------------------------------------------------------------
    // 6. Users & RBAC
    // ------------------------------------------------------------------------
    fetchUsers: function () {
      const search = $("#cpSearchUsers").val() || "";
      const role = $("#cpFilterUserRole").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/users/?search=${encodeURIComponent(search)}&role=${encodeURIComponent(role)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (u) {
              const statusPill = u.status == 1 ? '<span class="cp-status-pill active">Active</span>' : '<span class="cp-status-pill inactive">Disabled</span>';
              let roleClass = u.role === "on_ground_sales" ? "ground" : u.role;
              let roleLabel = u.role.replace(/_/g, " ").toUpperCase();

              html += `<tr>
                <td>
                  <strong>${u.display_name}</strong>
                  <span class="cp-site-sub">@${u.username} &bull; ID: #${u.id}</span>
                </td>
                <td>
                  <span>${u.email}</span>
                  <span class="cp-site-sub">${u.mobile || "No mobile registered"}</span>
                </td>
                <td>
                  <span class="cp-badge-role ${roleClass}">${roleLabel}</span>
                </td>
                <td>${u.site_name}</td>
                <td>${statusPill}</td>
                <td>
                  <button type="button" class="cp-btn-secondary btn-reset-user-password" data-id="${u.id}" title="Reset Password">
                    <i class="fi fi-rr-key"></i>
                  </button>
                  <button type="button" class="cp-btn-secondary btn-toggle-user-status" data-id="${u.id}" data-status="${u.status}">
                    ${u.status == 1 ? "Disable" : "Enable"}
                  </button>
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="6" class="text-center">No users found.</td></tr>';
          }
          $("#cpUsersTbody").html(html);
          self.loadedTabs["users"] = true;
        }
      });
    },

    submitNewUser: function () {
      const self = this;
      const data = {
        email: $("#userEmailInput").val().trim(),
        display_name: $("#userDisplayNameInput").val().trim(),
        mobile: $("#userMobileInput").val().trim(),
        role: $("#userRoleSelect").val(),
        password: $("#userPasswordInput").val().trim()
      };

      $.ajax({
        url: "/api/v3/control_panel/users/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalNewUser").removeClass("active");
          $("#formNewUser")[0].reset();
          swal("Created", res.msg || "User created successfully!", "success");
          self.fetchUsers();
          delete self.loadedTabs["overview"];
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error creating user.";
          swal("Error", err, "error");
        }
      });
    },

    toggleUserStatus: function (id, status) {
      const self = this;
      $.ajax({
        url: "/api/v3/control_panel/users/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "toggle_status", user_id: id, status: status }),
        success: function (res) {
          swal("Updated", res.msg, "success");
          self.fetchUsers();
        }
      });
    },

    resetUserPasswordPrompt: function (id) {
      const newPass = prompt("Enter new password for this user (minimum 6 characters):");
      if (newPass && newPass.trim().length >= 6) {
        $.ajax({
          url: "/api/v3/control_panel/users/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ action: "reset_password", user_id: id, new_password: newPass.trim() }),
          success: function (res) {
            swal("Password Reset", res.msg, "success");
          }
        });
      }
    },

    // ------------------------------------------------------------------------
    // 7. Invoices & Billing
    // ------------------------------------------------------------------------
    fetchInvoices: function () {
      const search = $("#cpSearchInvoices").val() || "";
      const status = $("#cpFilterInvoiceStatus").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/invoices/?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (inv) {
              const statusPill = `<span class="cp-status-pill ${inv.status}">${inv.status.toUpperCase()}</span>`;
              const markPaidBtn = inv.status !== "paid" ? `<button type="button" class="cp-btn-secondary btn-mark-invoice-paid" data-id="${inv.id}"><i class="fi fi-rr-check"></i> Mark Paid</button>` : "";

              html += `<tr>
                <td><strong>${inv.invoice_number}</strong></td>
                <td>
                  <span class="cp-site-title">${inv.site_name}</span>
                  <span class="cp-site-sub">${inv.site_domain}</span>
                </td>
                <td>${inv.plan}</td>
                <td><strong>₹${inv.total.toLocaleString("en-IN", { minimumFractionDigits: 2 })}</strong></td>
                <td>${inv.due_date ? inv.due_date.substring(0, 10) : "N/A"}</td>
                <td>${statusPill}</td>
                <td>
                  <button type="button" class="cp-btn-secondary btn-view-invoice" data-id="${inv.id}"><i class="fi fi-rr-eye"></i> View</button>
                  ${markPaidBtn}
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="7" class="text-center">No invoices found.</td></tr>';
          }
          $("#cpInvoicesTbody").html(html);
          self.loadedTabs["invoices"] = true;
        }
      });
    },

    submitCreateInvoice: function () {
      const self = this;
      const data = {
        action: "create",
        site_id: $("#invSiteSelect").val(),
        plan: $("#invPlanInput").val().trim(),
        amount: $("#invAmountInput").val(),
        tax: $("#invTaxInput").val() || 0,
        due_date: $("#invDueDateInput").val(),
        partner_id: $("#invPartnerSelect").val()
      };

      $.ajax({
        url: "/api/v3/control_panel/invoices/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalCreateInvoice").removeClass("active");
          $("#formCreateInvoice")[0].reset();
          swal("Invoice Created", `Invoice #${res.invoice_number} generated successfully.`, "success");
          self.fetchInvoices();
          delete self.loadedTabs["overview"];
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error creating invoice.";
          swal("Error", err, "error");
        }
      });
    },

    markInvoicePaid: function (id) {
      const self = this;
      swal({
        title: "Record Payment?",
        text: "This will mark the invoice as PAID and automatically credit partner referral commission if applicable.",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "Yes, Mark Paid"
      }, function (confirmed) {
        if (confirmed) {
          $.ajax({
            url: "/api/v3/control_panel/invoices/",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ action: "mark_paid", invoice_id: id }),
            success: function (res) {
              swal("Payment Recorded", res.msg, "success");
              self.fetchInvoices();
              delete self.loadedTabs["commissions"];
              delete self.loadedTabs["overview"];
            }
          });
        }
      });
    },

    viewInvoiceDetails: function (id) {
      $.getJSON(`/api/v3/control_panel/invoices/?invoice_id=${id}`, function (res) {
        if (res.status === "success" && res.data) {
          const inv = res.data;
          $("#invModalTitle").text(`Invoice #${inv.invoice_number}`);
          let sheet = `
            <div class="cp-invoice-head">
              <div class="cp-invoice-logo">
                <h2>Blumox Technologies</h2>
                <p>Cloud SaaS &amp; Infrastructure Services</p>
              </div>
              <div>
                <h3>INVOICE</h3>
                <p><strong>#${inv.invoice_number}</strong></p>
                <p>Status: <span class="cp-status-pill ${inv.invoice_status}">${inv.invoice_status.toUpperCase()}</span></p>
              </div>
            </div>
            <div class="cp-invoice-details">
              <div>
                <strong>Billed To:</strong>
                <p>${inv.site_name}<br>Domain: ${inv.site_domain}<br>Client: ${inv.user_email || "N/A"}</p>
              </div>
              <div>
                <strong>Billing Dates:</strong>
                <p>Issued: ${inv.invoice_created_at.substring(0, 10)}<br>Due: ${inv.invoice_due_date ? inv.invoice_due_date.substring(0, 10) : "Due on receipt"}</p>
              </div>
            </div>
            <table class="cp-table">
              <thead>
                <tr>
                  <th>Description</th>
                  <th>Amount (INR)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>${inv.invoice_plan}</td>
                  <td>₹${parseFloat(inv.invoice_amount).toFixed(2)}</td>
                </tr>
                <tr>
                  <td>Tax / GST</td>
                  <td>₹${parseFloat(inv.invoice_tax).toFixed(2)}</td>
                </tr>
              </tbody>
            </table>
            <div class="cp-invoice-total-row">
              Total: ₹${parseFloat(inv.invoice_total).toFixed(2)} ${inv.invoice_currency}
            </div>
          `;
          $("#invoiceSheetContent").html(sheet);
          $("#modalViewInvoice").addClass("active");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 8. Partner Commissions
    // ------------------------------------------------------------------------
    fetchCommissions: function () {
      const search = $("#cpSearchCommissions").val() || "";
      const status = $("#cpFilterCommissionStatus").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/commissions/?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (c) {
              const statusPill = `<span class="cp-status-pill ${c.status}">${c.status.toUpperCase()}</span>`;
              let actions = "";
              if (c.status === "pending") {
                actions = `<button type="button" class="cp-btn-secondary btn-approve-commission" data-id="${c.id}">Approve</button>`;
              } else if (c.status === "approved") {
                actions = `<button type="button" class="cp-btn-primary btn-payout-commission" data-id="${c.id}">Process Payout</button>`;
              } else if (c.status === "paid") {
                actions = `<span class="cp-site-sub">Paid: ${c.payout_date ? c.payout_date.substring(0, 10) : ""}</span>`;
              }

              html += `<tr>
                <td>
                  <strong>${c.partner_name}</strong>
                  <span class="cp-site-sub">${c.partner_email}</span>
                </td>
                <td>
                  <span class="cp-site-title">${c.site_name}</span>
                  <span class="cp-site-sub">${c.site_domain}</span>
                </td>
                <td>${c.invoice_number}</td>
                <td>${c.rate_percent}%</td>
                <td><strong>₹${c.amount.toLocaleString("en-IN", { minimumFractionDigits: 2 })}</strong></td>
                <td>${statusPill}</td>
                <td>${c.payout_ref || "-"}</td>
                <td>${actions}</td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="8" class="text-center">No partner commission records found.</td></tr>';
          }
          $("#cpCommissionsTbody").html(html);
          self.loadedTabs["commissions"] = true;
        }
      });
    },

    approveCommission: function (id) {
      const self = this;
      $.ajax({
        url: "/api/v3/control_panel/commissions/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "approve", commission_id: id }),
        success: function (res) {
          swal("Approved", res.msg, "success");
          self.fetchCommissions();
          delete self.loadedTabs["overview"];
        }
      });
    },

    payoutCommissionPrompt: function (id) {
      const self = this;
      const ref = prompt("Enter Payout Transaction Reference / UTR Number:");
      if (ref && ref.trim().length > 0) {
        $.ajax({
          url: "/api/v3/control_panel/commissions/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ action: "payout", commission_id: id, payout_ref: ref.trim() }),
          success: function (res) {
            swal("Payout Recorded", res.msg, "success");
            self.fetchCommissions();
            delete self.loadedTabs["overview"];
          }
        });
      }
    },

    // ------------------------------------------------------------------------
    // 9. Configurations
    // ------------------------------------------------------------------------
    fetchConfigurations: function () {
      const search = $("#cpSearchConfigs").val() || "";
      const isGlobal = $("#cpFilterConfigScope").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/configurations/?search=${encodeURIComponent(search)}&global=${encodeURIComponent(isGlobal)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (cfg) {
              const scopeBadge = cfg.global == 1 ? '<span class="cp-badge-role admin">Global</span>' : '<span class="cp-badge-role support">Site</span>';
              html += `<tr>
                <td>${scopeBadge}</td>
                <td><code>${cfg.key}</code></td>
                <td>${cfg.value || "<em>(empty)</em>"}</td>
                <td><span class="cp-status-pill ${cfg.status == 1 ? "active" : "inactive"}">${cfg.status == 1 ? "Active" : "Disabled"}</span></td>
                <td>${cfg.updated_at ? cfg.updated_at.substring(0, 10) : "-"}</td>
                <td>
                  <span class="cp-site-sub">Config #${cfg.id}</span>
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="6" class="text-center">No configuration parameters set.</td></tr>';
          }
          $("#cpConfigsTbody").html(html);
          self.loadedTabs["configurations"] = true;
        }
      });
    },

    submitNewConfig: function () {
      const self = this;
      const data = {
        action: "save",
        key: $("#cfgKeyInput").val().trim(),
        value: $("#cfgValueInput").val().trim(),
        global: $("#cfgScopeSelect").val()
      };

      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalNewConfig").removeClass("active");
          $("#formNewConfig")[0].reset();
          swal("Saved", res.msg, "success");
          self.fetchConfigurations();
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving configuration.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 10. Dropdown Loaders (Lazy-Loaded and Cached)
    // ------------------------------------------------------------------------
    loadAppDropdowns: function (force = false, callback) {
      if (typeof force === "function") {
        callback = force;
        force = false;
      }
      if (this.dropdownsLoaded.apps && !force) {
        if (typeof callback === "function") callback();
        return;
      }
      const self = this;
      $.getJSON("/api/v3/control_panel/apps/", function (res) {
        if (res.status === "success" && res.data) {
          self.appsList = res.data;
          let opts = '<option value="">All Applications</option>';
          let modalOpts = '<option value="">Select Application</option>';
          res.data.forEach(function (a) {
            opts += `<option value="${a.id}">${a.title}</option>`;
            modalOpts += `<option value="${a.id}">${a.title} (${a.path})</option>`;
          });
          const curSiteApp = $("#cpFilterSiteApp").val();
          const curThemeApp = $("#cpFilterThemeApp").val();

          $("#cpFilterThemeApp, #cpFilterSiteApp").each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
              $(this).select2("destroy");
            }
          });
          $("#cpFilterThemeApp, #cpFilterSiteApp").html(opts);
          if (curSiteApp) $("#cpFilterSiteApp").val(curSiteApp);
          if (curThemeApp) $("#cpFilterThemeApp").val(curThemeApp);

          $("#siteAppSelect, #themeAppSelect, #mscSiteAppSelect").html(modalOpts);
          self.dropdownsLoaded.apps = true;
          self.initSelect2("#cpFilterThemeApp, #cpFilterSiteApp, #siteAppSelect, #themeAppSelect, #mscSiteAppSelect");
          if (typeof callback === "function") callback();
        }
      });
    },

    loadSiteDropdowns: function (force = false, callback) {
      if (typeof force === "function") {
        callback = force;
        force = false;
      }
      if (this.dropdownsLoaded.sites && !force) {
        if (typeof callback === "function") callback();
        return;
      }
      const self = this;
      $.getJSON("/api/v3/control_panel/sites/?limit=100", function (res) {
        if (res.status === "success" && res.data) {
          self.sitesList = res.data;
          let opts = '<option value="">Select Client Site</option>';
          res.data.forEach(function (s) {
            opts += `<option value="${s.id}">${s.name} (${s.domain})</option>`;
          });
          $("#invSiteSelect, #manualCommSiteSelect").each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
              $(this).select2("destroy");
            }
          });
          $("#invSiteSelect, #manualCommSiteSelect").html(opts);
          self.dropdownsLoaded.sites = true;
          self.initSelect2("#invSiteSelect, #manualCommSiteSelect");
          if (typeof callback === "function") callback();
        }
      });
    },

    loadPartnerDropdowns: function (force = false, callback) {
      if (typeof force === "function") {
        callback = force;
        force = false;
      }
      if (this.dropdownsLoaded.partners && !force) {
        if (typeof callback === "function") callback();
        return;
      }
      const self = this;
      $.getJSON("/api/v3/control_panel/users/?role=partner&limit=100", function (res) {
        if (res.status === "success" && res.data) {
          self.partnersList = res.data;
          let pOpts = '<option value="0">Direct / No Partner</option>';
          let pSelectOpts = '<option value="">Select Partner</option>';
          res.data.forEach(function (p) {
            pOpts += `<option value="${p.id}">${p.display_name} (${p.email})</option>`;
            pSelectOpts += `<option value="${p.id}">${p.display_name} (${p.email})</option>`;
          });
          $("#sitePartnerSelect, #invPartnerSelect, #manualCommPartnerSelect").each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
              $(this).select2("destroy");
            }
          });
          $("#sitePartnerSelect, #invPartnerSelect").html(pOpts);
          $("#manualCommPartnerSelect").html(pSelectOpts);
          self.dropdownsLoaded.partners = true;
          self.initSelect2("#sitePartnerSelect, #invPartnerSelect, #manualCommPartnerSelect");
          if (typeof callback === "function") callback();
        }
      });
    },

    submitManualCommission: function () {
      const self = this;
      const data = {
        action: "create",
        partner_id: $("#manualCommPartnerSelect").val(),
        site_id: $("#manualCommSiteSelect").val(),
        amount: $("#manualCommAmountInput").val(),
        rate_percent: $("#manualCommRateInput").val()
      };

      if (!data.partner_id || !data.site_id || !data.amount) {
        swal("Validation Error", "Please select a partner, referred site, and commission amount.", "warning");
        return;
      }

      $.ajax({
        url: "/api/v3/control_panel/commissions/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalManualCommission").removeClass("active");
          $("#formManualCommission")[0].reset();
          swal("Recorded", res.msg || "Commission recorded successfully!", "success");
          self.fetchCommissions();
          delete self.loadedTabs["overview"];
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error recording commission.";
          swal("Error", err, "error");
        }
      });
    }
  };

  // 1. Full Page Load
  $(document).ready(function () {
    V3ControlPanel.init();
  });

  // If DOM is already parsed/complete when script executes, ensure onPageEnter triggers
  if (document.readyState === "complete" || document.readyState === "interactive") {
    setTimeout(function () {
      if ($("#controlPanelApp").length) {
        V3ControlPanel.onPageEnter();
      }
    }, 10);
  }

  // 2. SPA AJAX Page Navigation (triggered by app.js on loadPage)
  $(document).on("spa:loaded", function () {
    V3ControlPanel.onPageEnter();
  });

  // 3. Browser History Navigation
  $(window).on("popstate", function () {
    if ($("#controlPanelApp").length) {
      V3ControlPanel.onPageEnter();
    }
  });

  // 4. Tab hash changes in address bar
  $(window).on("hashchange", function () {
    if ($("#controlPanelApp").length) {
      const hash = window.location.hash.replace("#", "").trim();
      if (hash === "configurations") {
        window.location.href = "/dashboard/configurations/";
        return;
      }
      if (hash && hash !== V3ControlPanel.activeTab && $(`[data-tab="${hash}"]`).length) {
        V3ControlPanel.switchTab(hash);
      }
    }
  });

  window.V3ControlPanel = V3ControlPanel;
})(window, jQuery);
