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

    onPageEnter: function () {
      const $root = $("#controlPanelApp");
      if (!$root.length) {
        this.onPageLeave();
        return;
      }

      this.userRole = $root.data("user-role") || "administrator";
      this.bindEvents();

      // Reset cache when entering fresh so active tab API triggers
      this.loadedTabs = {};
      this.dropdownsLoaded = { apps: false, sites: false, partners: false };

      // Check if current view is a dedicated page (e.g. apps, themes, sites, users, invoices, commissions)
      const dedicatedPage = $root.data("page");
      if (dedicatedPage) {
        this.activeTab = dedicatedPage;
        if (dedicatedPage === "themes" || dedicatedPage === "sites") {
          this.loadAppDropdowns();
        }
        if (dedicatedPage === "invoices") {
          this.loadSiteDropdowns();
          this.loadPartnerDropdowns();
        }
        if (dedicatedPage === "commissions") {
          this.loadPartnerDropdowns();
          this.loadSiteDropdowns();
        }
        this.loadActiveTabContent();
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
        self.loadAppDropdowns();
        self.loadPartnerDropdowns();
        $("#modalProvisionSite").addClass("active");
      });

      $(document).on("click", "#btnHeaderNewInvoice, #btnOpenCreateInvoiceModal", function () {
        self.loadSiteDropdowns();
        self.loadPartnerDropdowns();
        $("#modalCreateInvoice").addClass("active");
      });

      $(document).on("click", "#btnOpenNewAppModal", function () {
        $("#formNewApp")[0].reset();
        $("#appIdInput").val("0");
        $("#modalNewApp").addClass("active");
      });

      $(document).on("click", "#btnOpenNewThemeModal", function () {
        self.loadAppDropdowns();
        $("#formNewTheme")[0].reset();
        $("#themeIdInput").val("0");
        $("#modalNewTheme").addClass("active");
      });

      $(document).on("click", "#btnOpenNewUserModal", function () {
        $("#formNewUser")[0].reset();
        $("#modalNewUser").addClass("active");
      });

      $(document).on("click", "#btnOpenNewConfigModal", function () {
        $("#formNewConfig")[0].reset();
        $("#modalNewConfig").addClass("active");
      });

      // 3. Search & Filter debouncing (Only fires when search/filter changes)
      let searchTimer = null;
      $(document).on("input", ".cp-search-input", function () {
        if (!$("#controlPanelApp").length) return;
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
          self.loadActiveTabContent(true); // Forced reload with query
        }, 250);
      });

      $(document).on("change", ".cp-select", function () {
        if (!$("#controlPanelApp").length) return;
        self.loadActiveTabContent(true);
      });

      // 4. Form Submissions (Delegated for SPA safety)
      // Provision Site
      $(document).on("submit", "#formProvisionSite", function (e) {
        e.preventDefault();
        self.submitProvisionSite();
      });

      $(document).on("click", "#btnOpenManualCommissionModal", function () {
        self.loadPartnerDropdowns(true);
        self.loadSiteDropdowns(true);
        $("#formManualCommission")[0].reset();
        $("#modalManualCommission").addClass("active");
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

      // Save Site Licenses & Features
      $(document).on("submit", "#formSiteLicenses", function (e) {
        e.preventDefault();
        self.submitSiteLicenses();
      });

      // 5. License Management Modal Triggers & Presets
      $(document).on("click", ".btn-manage-site-license", function () {
        const id = $(this).data("id");
        self.openSiteLicensesModal(id);
      });

      // Quick Preset Pills for User Limits
      $(document).on("click", ".btn-set-user-preset", function (e) {
        e.preventDefault();
        const val = $(this).data("val");
        $("#licUserLimitInput").val(val).trigger("change");
        self.updateLicenseMeters();
      });

      // Quick Preset Pills for WhatsApp Device Limits
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

      this.loadedTabs[tab] = true;
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
    // 2. Apps Management
    // ------------------------------------------------------------------------
    fetchApps: function () {
      const search = $("#cpSearchApps").val() || "";
      const status = $("#cpFilterAppStatus").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/apps/?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (app) {
              const statusPill = app.status == 1 ? '<span class="cp-status-pill active">Active</span>' : '<span class="cp-status-pill inactive">Inactive</span>';
              html += `<div class="cp-app-item">
                <div class="cp-app-item-top">
                  <div class="cp-app-badge-icon">
                    <i class="fi fi-rr-apps"></i>
                  </div>
                  <div class="cp-app-meta">
                    <h4>${app.title}</h4>
                    <p>${app.description || "Blumox Application Suite Module"}</p>
                    <p class="cp-site-sub">Path: <code>${app.path}</code></p>
                  </div>
                </div>
                <div class="cp-app-footer">
                  <span><strong>${app.themes_count}</strong> Themes &bull; <strong>${app.sites_count}</strong> Sites</span>
                  ${statusPill}
                </div>
              </div>`;
            });
          } else {
            html = '<div class="text-center">No applications found matching query.</div>';
          }
          $("#cpAppsGrid").html(html);
        }
      });
    },

    submitNewApp: function () {
      const self = this;
      const data = {
        action: "save",
        app_title: $("#appTitleInput").val().trim(),
        app_path: $("#appPathInput").val().trim(),
        app_description: $("#appDescInput").val().trim(),
        app_primary_color: $("#appColorInput").val().trim()
      };

      $.ajax({
        url: "/api/v3/control_panel/apps/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalNewApp").removeClass("active");
          swal("Success", res.msg || "Application registered successfully!", "success");
          self.fetchApps();
          self.loadAppDropdowns(true);
          delete self.loadedTabs["overview"]; // refresh overview on next visit
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving application.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // 3. Themes Matrix
    // ------------------------------------------------------------------------
    fetchThemes: function () {
      const search = $("#cpSearchThemes").val() || "";
      const appId = $("#cpFilterThemeApp").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/themes/?search=${encodeURIComponent(search)}&app_id=${encodeURIComponent(appId)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (th) {
              const statusPill = th.status == 1 ? '<span class="cp-status-pill active">Active</span>' : '<span class="cp-status-pill inactive">Archived</span>';
              html += `<div class="cp-app-item">
                <div class="cp-app-item-top">
                  <div class="cp-app-badge-icon">
                    <i class="fi fi-rr-palette"></i>
                  </div>
                  <div class="cp-app-meta">
                    <h4>${th.title}</h4>
                    <p>App: <strong>${th.app_title}</strong> (v${th.version})</p>
                    <p class="cp-site-sub">Path: ${th.path}</p>
                  </div>
                </div>
                <div class="cp-app-footer">
                  <span><strong>${th.sites_count}</strong> Active Sites</span>
                  ${statusPill}
                </div>
              </div>`;
            });
          } else {
            html = '<div class="text-center">No themes found.</div>';
          }
          $("#cpThemesGrid").html(html);
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
    // 4. Client Sites & Tenancy with Quota Badges
    // ------------------------------------------------------------------------
    fetchSites: function () {
      const search = $("#cpSearchSites").val() || "";
      const appId = $("#cpFilterSiteApp").val() || "";
      const status = $("#cpFilterSiteStatus").val() || "";

      this.activeXhr = $.getJSON(`/api/v3/control_panel/sites/?search=${encodeURIComponent(search)}&app_id=${encodeURIComponent(appId)}&status=${encodeURIComponent(status)}`, function (res) {
        if (res.status === "success") {
          let html = "";
          if (res.data && res.data.length) {
            res.data.forEach(function (s) {
              const statusPill = s.status == 1 ? '<span class="cp-status-pill active">Live</span>' : '<span class="cp-status-pill inactive">Suspended</span>';
              const toggleBtnText = s.status == 1 ? "Suspend" : "Activate";

              // License Quotas display
              const userQuotaBadge = `<span class="cp-quota-badge" title="Team Members used / limit"><i class="fi fi-rr-users"></i> ${s.users_count}/${s.user_limit}</span>`;
              const waQuotaBadge = `<span class="cp-quota-badge" title="WhatsApp Devices used / limit"><i class="fi fi-brands-whatsapp"></i> ${s.wa_devices_count}/${s.wa_device_limit}</span>`;

              html += `<tr>
                <td>
                  <span class="cp-site-title">${s.name}</span>
                  <span class="cp-site-sub">ID: #${s.id} &bull; V${s.dashboard_version}</span>
                </td>
                <td>
                  <a href="http://${s.domain}/dashboard/" target="_blank"><strong>${s.domain}</strong></a>
                  <span class="cp-site-sub">${s.url}</span>
                </td>
                <td>${s.app_title}</td>
                <td>
                  ${userQuotaBadge} ${waQuotaBadge}
                </td>
                <td>
                  <span>${s.owner_email || "N/A"}</span>
                  <span class="cp-site-sub">${s.owner_mobile || ""}</span>
                </td>
                <td>${statusPill}</td>
                <td>
                  <button type="button" class="cp-btn-secondary btn-manage-site-license" data-id="${s.id}" data-name="${s.name}" data-domain="${s.domain}" title="Manage License Quotas &amp; Feature Flags">
                    <i class="fi fi-rr-key"></i> Licenses
                  </button>
                  <button type="button" class="cp-btn-secondary btn-toggle-site-status" data-id="${s.id}" data-status="${s.status}">
                    ${toggleBtnText}
                  </button>
                  <a href="http://${s.domain}/dashboard/" target="_blank" class="cp-btn-secondary" title="Direct Dashboard Access">
                    <i class="fi fi-rr-arrow-up-right-from-square"></i>
                  </a>
                </td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="7" class="text-center">No client sites found matching criteria.</td></tr>';
          }
          $("#cpSitesTbody").html(html);
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
    // 5. Site License & Feature Controls Modal
    // ------------------------------------------------------------------------
    openSiteLicensesModal: function (siteId) {
      const self = this;
      $("#licSiteIdInput").val(siteId);

      $.getJSON(`/api/v3/control_panel/sites/?action=get_license&site_id=${siteId}`, function (res) {
        if (res.status === "success" && res.data) {
          const d = res.data;
          $("#licModalSiteName").text(`Licenses & Features: ${d.site_name}`);
          $("#licModalSiteDomain").text(`Domain: ${d.site_domain} | App: ${d.app_title}`);

          // Active values
          $("#licUserLimitInput").val(d.user_limit);
          $("#licWaLimitInput").val(d.wa_device_limit);
          $("#licPlanSelect").val(d.subscription_plan || "Professional SaaS");
          $("#licExpiryInput").val(d.subscription_expiry ? d.subscription_expiry.substring(0, 10) : "");
          $("#licSiteStatusSelect").val(d.site_status);

          // Module switches
          $("#swWhatsapp").prop("checked", !!d.features.whatsapp_enable);
          $("#swLeads").prop("checked", !!d.features.feature_leads);
          $("#swAccounts").prop("checked", !!d.features.feature_accounts);
          $("#swTransporters").prop("checked", !!d.features.feature_transporters);
          $("#swPackages").prop("checked", !!d.features.feature_packages);
          $("#swCustomDomain").prop("checked", !!d.features.site_custom_domain);
          $("#swApiAccess").prop("checked", !!d.features.feature_api_access);

          // Update Meters
          self.renderMeter("licMeterUsers", d.active_users, d.user_limit);
          self.renderMeter("licMeterWa", d.active_wa_devices, d.wa_device_limit);

          // Store current active counts on form for live calculation
          $("#formSiteLicenses").data("active-users", d.active_users);
          $("#formSiteLicenses").data("active-wa", d.active_wa_devices);

          $("#modalSiteLicenses").addClass("active");
        }
      });
    },

    renderMeter: function (meterPrefix, current, limit) {
      const pct = limit > 0 ? Math.min(100, Math.round((current / limit) * 100)) : 0;
      $(`#${meterPrefix}Text`).text(`${current} / ${limit} (${pct}%)`);
      const $fill = $(`#${meterPrefix}Fill`);
      $fill.css("width", `${pct}%`).removeClass("warning danger");

      if (pct >= 100) {
        $fill.addClass("danger");
      } else if (pct >= 80) {
        $fill.addClass("warning");
      }
    },

    updateLicenseMeters: function () {
      const activeUsers = parseInt($("#formSiteLicenses").data("active-users"), 10) || 0;
      const userLimit = parseInt($("#licUserLimitInput").val(), 10) || 1;
      this.renderMeter("licMeterUsers", activeUsers, userLimit);

      const activeWa = parseInt($("#formSiteLicenses").data("active-wa"), 10) || 0;
      const waLimit = parseInt($("#licWaLimitInput").val(), 10) || 1;
      this.renderMeter("licMeterWa", activeWa, waLimit);
    },

    submitSiteLicenses: function () {
      const self = this;
      const siteId = $("#licSiteIdInput").val();
      const data = {
        action: "save_license",
        site_id: siteId,
        user_limit: $("#licUserLimitInput").val(),
        whatsapp_device_limit: $("#licWaLimitInput").val(),
        subscription_plan: $("#licPlanSelect").val(),
        subscription_expiry: $("#licExpiryInput").val(),
        site_status: $("#licSiteStatusSelect").val(),
        features: {
          whatsapp_enable: $("#swWhatsapp").is(":checked") ? 1 : 0,
          feature_leads: $("#swLeads").is(":checked") ? 1 : 0,
          feature_accounts: $("#swAccounts").is(":checked") ? 1 : 0,
          feature_transporters: $("#swTransporters").is(":checked") ? 1 : 0,
          feature_packages: $("#swPackages").is(":checked") ? 1 : 0,
          site_custom_domain: $("#swCustomDomain").is(":checked") ? 1 : 0,
          feature_api_access: $("#swApiAccess").is(":checked") ? 1 : 0
        }
      };

      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalSiteLicenses").removeClass("active");
          swal("Updated", res.msg || "Site licenses and features updated successfully!", "success");
          self.fetchSites();
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving licenses.";
          swal("Error", err, "error");
        }
      });
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
          $("#cpFilterThemeApp, #cpFilterSiteApp").html(opts);
          $("#siteAppSelect, #themeAppSelect").html(modalOpts);
          self.dropdownsLoaded.apps = true;
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
          $("#invSiteSelect, #manualCommSiteSelect").html(opts);
          self.dropdownsLoaded.sites = true;
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
          $("#sitePartnerSelect, #invPartnerSelect").html(pOpts);
          $("#manualCommPartnerSelect").html(pSelectOpts);
          self.dropdownsLoaded.partners = true;
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
