/**
 * Blumox Configurations Studio & Code Engine V3
 * Path: /app/dashboard/v3/default/desktop/assets/js/configurations.js
 * Strict V3 Rule: External JS, zero inline handlers, SPA navigation safe
 */

(function (window, $) {
  "use strict";

  if (window._cfgStudioInitialized) {
    if (window.V3ConfigurationsStudio && $("#configurationsApp").length) {
      window.V3ConfigurationsStudio.onPageEnter();
    }
    return;
  }
  window._cfgStudioInitialized = true;

  const V3ConfigurationsStudio = {
    primaryMode: "global",
    currentSiteWiseId: null,
    activeScope: "all",
    activeSiteId: "all",
    activeCategory: "all",
    currentView: "visual", // 'visual' or 'code'
    codeFormat: "env", // 'env' or 'json'
    configsList: [],
    sitesList: [],
    eventsBound: false,
    activeXhr: null,

    init: function () {
      this.onPageEnter();
    },

    onPageEnter: function () {
      const $root = $("#configurationsApp");
      if (!$root.length) {
        this.onPageLeave();
        return;
      }

      this.bindEvents();
      this.loadSitesDropdown();
      this.loadConfigurations();
    },

    onPageLeave: function () {
      if (this.activeXhr && this.activeXhr.readyState !== 4) {
        this.activeXhr.abort();
      }
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // 0. Primary Mode Switching (Global Unique vs Site-Wise Operations)
      $(document).on("click", ".cfg-mode-tab", function () {
        const mode = $(this).data("mode");
        $(".cfg-mode-tab").removeClass("active");
        $(this).addClass("active");
        self.primaryMode = mode;

        if (mode === "global") {
          $("#cfgGlobalContainer").addClass("active");
          $("#cfgSiteWiseContainer").removeClass("active");
          $("#btnToggleCodeView, #btnExportEnv").show();
        } else {
          $("#cfgGlobalContainer").removeClass("active");
          $("#cfgSiteWiseContainer").addClass("active");
          $("#btnToggleCodeView, #btnExportEnv").hide();
          if (!self.currentSiteWiseId && self.sitesList && self.sitesList.length > 0) {
            self.currentSiteWiseId = self.sitesList[0].id;
            $("#cfgSiteWisePicker").val(self.currentSiteWiseId);
            self.loadSiteWiseData(self.currentSiteWiseId);
          } else if (self.currentSiteWiseId) {
            self.loadSiteWiseData(self.currentSiteWiseId);
          }
        }
      });

      // Site-Wise Picker Dropdown Change
      $(document).on("change", "#cfgSiteWisePicker", function () {
        const siteId = $(this).val();
        if (siteId) {
          self.currentSiteWiseId = siteId;
          self.loadSiteWiseData(siteId);
        }
      });

      // Quick Expiry Extension Buttons (+1M, +3M, +6M, +1Y, Lifetime)
      $(document).on("click", ".btn-extend-expiry", function (e) {
        e.preventDefault();
        const days = $(this).data("days");
        const $input = $("#cfgSiteWiseExpiryInput");
        if (days === "lifetime") {
          $input.val("2099-12-31");
          return;
        }
        let baseDate = new Date();
        const curVal = $input.val();
        if (curVal) {
          const parsed = new Date(curVal);
          if (!isNaN(parsed.getTime()) && parsed > baseDate) {
            baseDate = parsed;
          }
        }
        baseDate.setDate(baseDate.getDate() + parseInt(days, 10));
        const yyyy = baseDate.getFullYear();
        const mm = String(baseDate.getMonth() + 1).padStart(2, "0");
        const dd = String(baseDate.getDate()).padStart(2, "0");
        $input.val(`${yyyy}-${mm}-${dd}`);
      });

      // Preset Pills for Quotas
      $(document).on("click", ".btn-set-cfg-user", function (e) {
        e.preventDefault();
        $("#cfgSiteWiseUserLimit").val($(this).data("val"));
      });
      $(document).on("click", ".btn-set-cfg-wa", function (e) {
        e.preventDefault();
        $("#cfgSiteWiseWaLimit").val($(this).data("val"));
      });
      $(document).on("click", ".btn-set-cfg-storage", function (e) {
        e.preventDefault();
        $("#cfgSiteWiseStorageLimit").val($(this).data("val"));
      });

      // Save Site-Wise Billing, Expiry, Status & Quotas
      $(document).on("submit", "#formSiteWiseOperations", function (e) {
        e.preventDefault();
        self.submitSaveSiteWiseOperations();
      });

      // Open Add Site Parameter Modal
      $(document).on("click", "#btnOpenAddSiteParamModal", function () {
        self.openConfigModal({
          site_id: self.currentSiteWiseId || 1,
          global: 0,
          key: "",
          value: ""
        });
      });

      // 1. View Switching (Visual vs Code View)
      $(document).on("click", "#btnToggleCodeView", function () {
        self.toggleView();
      });

      // 2. Code Format Toggle (.ENV vs JSON)
      $(document).on("click", ".cfg-code-format-btn", function () {
        const fmt = $(this).data("format");
        self.switchCodeFormat(fmt);
      });

      // 3. Scope Tabs (All, Global, Site)
      $(document).on("click", ".cfg-scope-btn", function () {
        $(".cfg-scope-btn").removeClass("active");
        $(this).addClass("active");
        self.activeScope = $(this).data("scope");
        self.loadConfigurations();
      });

      // 4. Site Filter Dropdown
      $(document).on("change", "#cfgSiteFilterSelect", function () {
        self.activeSiteId = $(this).val();
        self.loadConfigurations();
      });

      // 5. Category Pills
      $(document).on("click", ".cfg-cat-pill", function () {
        $(".cfg-cat-pill").removeClass("active");
        $(this).addClass("active");
        self.activeCategory = $(this).data("cat");
        self.renderVisualTable();
      });

      // 6. Live Search Input
      let searchTimer = null;
      $(document).on("input", "#cfgSearchInput", function () {
        const q = $(this).val().trim();
        $("#cfgSearchClear").toggleClass("visible", q.length > 0);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
          self.loadConfigurations();
        }, 250);
      });

      $(document).on("click", "#cfgSearchClear", function () {
        $("#cfgSearchInput").val("");
        $(this).removeClass("visible");
        self.loadConfigurations();
      });

      // 7. Refresh Button
      $(document).on("click", "#btnRefreshConfig", function () {
        self.loadConfigurations(true);
      });

      // 8. Secret Reveal / Mask Toggles
      $(document).on("click", ".btn-toggle-secret", function () {
        const $mask = $(this).siblings(".cfg-secret-mask");
        const $plain = $(this).siblings(".cfg-secret-plain");
        const $icon = $(this).find("i");
        if ($plain.is(":visible")) {
          $plain.hide();
          $mask.show();
          $icon.removeClass("fi-rr-eye-crossed").addClass("fi-rr-eye");
        } else {
          $mask.hide();
          $plain.show();
          $icon.removeClass("fi-rr-eye").addClass("fi-rr-eye-crossed");
        }
      });

      // 9. Copy Value Button
      $(document).on("click", ".btn-copy-config-val", function () {
        const val = $(this).data("val") || "";
        self.copyToClipboard(val, "Value copied to clipboard!");
      });

      // 10. Copy Key Name Button
      $(document).on("click", ".btn-copy-config-key", function () {
        const key = $(this).data("key") || "";
        self.copyToClipboard(key, `Key "${key}" copied to clipboard!`);
      });

      // 11. Live Boolean Toggle Switch
      $(document).on("change", ".cfg-boolean-toggle", function () {
        const id = $(this).data("id");
        const newVal = $(this).is(":checked") ? "1" : "0";
        self.updateConfigValue(id, newVal);
      });

      // 12. Toggle Status Pill
      $(document).on("click", ".btn-toggle-config-status", function () {
        const id = $(this).data("id");
        const curStatus = $(this).data("status");
        const nextStatus = curStatus == 1 ? 0 : 1;
        self.toggleStatus(id, nextStatus);
      });

      // 13. Delete Parameter
      $(document).on("click", ".btn-delete-config", function () {
        const id = $(this).data("id");
        const key = $(this).data("key");
        self.deleteConfigPrompt(id, key);
      });

      // 14. Modals Open / Close
      $(document).on("click", "[data-close-modal]", function () {
        $(".cp-modal-backdrop").removeClass("active");
      });

      $(document).on("click", "#btnOpenAddConfigModal", function () {
        self.openConfigModal(null);
      });

      $(document).on("click", ".btn-edit-config", function () {
        const id = $(this).data("id");
        const cfg = self.configsList.find((c) => c.id == id);
        if (cfg) {
          self.openConfigModal(cfg);
        }
      });

      // 15. Scope Change inside Modal (Show/Hide Target Site select)
      $(document).on("change", "#cfgParamScope", function () {
        const isGlobal = $(this).val() == "1";
        $("#cfgModalSiteRow").toggle(!isGlobal);
      });

      // 16. Form Submit (Save Parameter)
      $(document).on("submit", "#formConfigParam", function (e) {
        e.preventDefault();
        self.submitSaveConfigParam();
      });

      // 17. Code Editor: Line numbers gutter sync & Syntax validation
      $(document).on("input scroll", "#cfgRawEditor", function () {
        self.updateCodeEditorGutter();
        self.validateCodeSyntax();
      });

      // 18. Code Editor: Save Raw Code
      $(document).on("click", "#btnSaveRawCode", function () {
        self.saveRawCode();
      });

      // 19. Code Editor: Format Raw Code
      $(document).on("click", "#btnFormatRawCode", function () {
        self.formatRawCode();
      });

      // 20. Code Editor: Copy Raw Code
      $(document).on("click", "#btnCopyRawCode", function () {
        const raw = $("#cfgRawEditor").val();
        self.copyToClipboard(raw, "All code copied to clipboard!");
      });

      // 21. Export .ENV File
      $(document).on("click", "#btnExportEnv", function () {
        self.exportAsEnvFile();
      });

      // 22. JSON Inspector Modal
      $(document).on("click", ".btn-inspect-json", function () {
        const id = $(this).data("id");
        const cfg = self.configsList.find((c) => c.id == id);
        if (cfg) {
          try {
            const pretty = JSON.stringify(JSON.parse(cfg.raw_value), null, 2);
            $("#cfgJsonPrettyContent code").text(pretty);
            $("#modalJsonTitle").text(`JSON Inspector: ${cfg.key}`);
            $("#modalJsonInspector").addClass("active");
          } catch (e) {
            $("#cfgJsonPrettyContent code").text(cfg.raw_value);
            $("#modalJsonInspector").addClass("active");
          }
        }
      });

      $(document).on("click", "#btnCopyJsonContent", function () {
        const text = $("#cfgJsonPrettyContent code").text();
        self.copyToClipboard(text, "JSON copied to clipboard!");
      });
    },

    // ------------------------------------------------------------------------
    // API Loaders
    // ------------------------------------------------------------------------
    loadConfigurations: function (forceNotify = false) {
      const self = this;
      const search = $("#cfgSearchInput").val().trim();
      const scope = this.activeScope;
      const siteId = this.activeSiteId;

      let url = `/api/v3/control_panel/configurations/?scope=${encodeURIComponent(scope)}&site_id=${encodeURIComponent(siteId)}`;
      if (search) {
        url += `&search=${encodeURIComponent(search)}`;
      }

      if (this.activeXhr && this.activeXhr.readyState !== 4) {
        this.activeXhr.abort();
      }

      this.activeXhr = $.getJSON(url, function (res) {
        if (res.status === "success") {
          self.configsList = res.data || [];
          self.updateKpiCounters();
          self.renderVisualTable();
          self.syncCodeEditor();
          if (forceNotify) {
            swal("Refreshed", `Loaded ${self.configsList.length} configuration keys.`, "success");
          }
        }
      });
    },

    loadSitesDropdown: function () {
      const self = this;
      $.getJSON("/api/v3/control_panel/sites/?limit=100", function (res) {
        if (res.status === "success" && res.data) {
          self.sitesList = res.data;
          let filterOpts = '<option value="all">All Sites &amp; Global</option>';
          let modalOpts = "";
          let pickerOpts = '<option value="">Select a Client Site...</option>';
          res.data.forEach(function (s) {
            filterOpts += `<option value="${s.id}">${s.name} (${s.domain})</option>`;
            modalOpts += `<option value="${s.id}">${s.name} (${s.domain})</option>`;
            pickerOpts += `<option value="${s.id}">${s.name} (${s.domain})</option>`;
          });
          $("#cfgSiteFilterSelect").html(filterOpts);
          $("#cfgParamSiteId").html(modalOpts);
          $("#cfgSiteWisePicker").html(pickerOpts);

          // If no site is currently selected in Site-Wise mode, select first site automatically
          if (!self.currentSiteWiseId && res.data.length > 0) {
            self.currentSiteWiseId = res.data[0].id;
            $("#cfgSiteWisePicker").val(self.currentSiteWiseId);
            self.loadSiteWiseData(self.currentSiteWiseId);
          }
        }
      });
    },

    loadSiteWiseData: function (siteId) {
      if (!siteId) return;
      const self = this;

      // 1. Fetch site operational details, subscription plan & expiry
      $.getJSON(`/api/v3/control_panel/sites/?action=get_license&site_id=${siteId}`, function (res) {
        if (res.status === "success" && res.data) {
          const d = res.data;
          $("#cfgSiteWiseAppTitle").text(d.app_title || "Application");
          $("#cfgSiteWiseDomainLink").text(d.site_domain || "domain.com").attr("href", `http://${d.site_domain}/dashboard/`);
          $("#cfgSiteWiseOwnerName").text(d.owner_name || "Owner");
          $("#cfgSiteWiseOwnerEmail").val(d.owner_email || "");

          const isLive = d.site_status == 1;
          $("#cfgSiteWiseStatusBadge")
            .text(isLive ? "Live" : "Suspended")
            .attr("class", `cfg-site-badge ${isLive ? "status-live" : "status-suspended"}`);
          $("#cfgSiteWiseStatusSelect").val(d.site_status);

          $("#cfgSiteWisePlanSelect").val(d.subscription_plan || "Professional SaaS");
          if (d.subscription_expiry) {
            $("#cfgSiteWiseExpiryInput").val(d.subscription_expiry.substring(0, 10));
          } else {
            $("#cfgSiteWiseExpiryInput").val("");
          }

          $("#cfgSiteWiseUserLimit").val(d.user_limit || 10);
          $("#cfgSiteWiseWaLimit").val(d.wa_device_limit || 1);
          $("#cfgSiteWiseStorageLimit").val(d.storage_limit_mb || 5120);

          const feats = d.features || {};
          $("#cfgSwWhatsapp").prop("checked", feats.whatsapp_enable == 1);
          $("#cfgSwLeads").prop("checked", feats.feature_leads == 1);
          $("#cfgSwAccounts").prop("checked", feats.feature_accounts == 1);
          $("#cfgSwTransporters").prop("checked", feats.feature_transporters == 1);
          $("#cfgSwPackages").prop("checked", feats.feature_packages == 1);
          $("#cfgSwCustomDomain").prop("checked", feats.site_custom_domain == 1);
          $("#cfgSwApiAccess").prop("checked", feats.feature_api_access == 1);
        }
      });

      // 2. Fetch site-specific configuration parameters
      $.getJSON(`/api/v3/control_panel/configurations/?scope=site&site_id=${siteId}`, function (res) {
        let html = "";
        if (res.status === "success" && res.data && res.data.length > 0) {
          res.data.forEach(function (cfg) {
            const isSecret = self.detectType(cfg.key, cfg.raw_value) === "secret";
            const valPreview = isSecret ? "••••••••••••" : (cfg.value || "<em>(empty)</em>");
            const statusPill = cfg.status == 1 ? '<span class="cfg-badge active">Active</span>' : '<span class="cfg-badge inactive">Disabled</span>';

            html += `<tr>
              <td><code>${cfg.key}</code></td>
              <td><span class="cfg-type-tag">${self.detectType(cfg.key, cfg.raw_value)}</span></td>
              <td>${valPreview}</td>
              <td>${statusPill}</td>
              <td>${cfg.updated_at ? cfg.updated_at.substring(0, 10) : "-"}</td>
              <td>
                <button type="button" class="cfg-btn-subtle btn-edit-config" data-id="${cfg.id}" title="Edit"><i class="fi fi-rr-edit"></i></button>
                <button type="button" class="cfg-btn-subtle btn-delete-config" data-id="${cfg.id}" data-key="${cfg.key}" title="Delete"><i class="fi fi-rr-trash"></i></button>
              </td>
            </tr>`;
          });
        } else {
          html = '<tr><td colspan="6" class="text-center" style="padding: 24px; color: #64748b;">No tenant-specific configuration overrides found for this site.</td></tr>';
        }
        $("#cfgSiteWiseParamsTbody").html(html);
      });
    },

    submitSaveSiteWiseOperations: function () {
      const siteId = this.currentSiteWiseId || $("#cfgSiteWisePicker").val();
      if (!siteId) {
        swal("Select Site", "Please select a client site first.", "warning");
        return;
      }
      const self = this;
      const plan = $("#cfgSiteWisePlanSelect").val();
      const expiry = $("#cfgSiteWiseExpiryInput").val();
      const status = $("#cfgSiteWiseStatusSelect").val();
      const userLimit = $("#cfgSiteWiseUserLimit").val();
      const waLimit = $("#cfgSiteWiseWaLimit").val();
      const storageLimit = $("#cfgSiteWiseStorageLimit").val();

      const payload = {
        action: "save_license",
        site_id: parseInt(siteId, 10),
        subscription_plan: plan,
        subscription_expiry: expiry,
        site_status: parseInt(status, 10),
        user_limit: parseInt(userLimit, 10),
        whatsapp_device_limit: parseInt(waLimit, 10),
        storage_limit_mb: parseInt(storageLimit, 10),
        features: {
          whatsapp_enable: $("#cfgSwWhatsapp").is(":checked") ? 1 : 0,
          feature_leads: $("#cfgSwLeads").is(":checked") ? 1 : 0,
          feature_accounts: $("#cfgSwAccounts").is(":checked") ? 1 : 0,
          feature_transporters: $("#cfgSwTransporters").is(":checked") ? 1 : 0,
          feature_packages: $("#cfgSwPackages").is(":checked") ? 1 : 0,
          site_custom_domain: $("#cfgSwCustomDomain").is(":checked") ? 1 : 0,
          feature_api_access: $("#cfgSwApiAccess").is(":checked") ? 1 : 0
        }
      };

      $.ajax({
        url: "/api/v3/control_panel/sites/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        success: function (res) {
          $("#cfgSiteWiseSaveFeedback").text("✓ Saved successfully at " + new Date().toLocaleTimeString()).fadeIn().delay(3000).fadeOut();
          swal("Saved", res.msg || "Site operational status, billing plan & expiry updated!", "success");
          self.loadSiteWiseData(siteId);
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving site operations.";
          swal("Error", err, "error");
        }
      });
    },

    // ------------------------------------------------------------------------
    // Visual Studio Renderer
    // ------------------------------------------------------------------------
    renderVisualTable: function () {
      const self = this;
      const filtered = this.configsList.filter((item) => {
        if (self.activeCategory === "all") return true;
        const cat = self.detectCategory(item.key);
        return cat === self.activeCategory;
      });

      if (!filtered.length) {
        $("#cfgTbody").html('<tr><td colspan="7" class="text-center" style="padding: 30px;">No configuration parameters match the active filter criteria.</td></tr>');
        return;
      }

      let html = "";
      filtered.forEach(function (cfg) {
        const type = self.detectType(cfg.key, cfg.raw_value);
        const cat = self.detectCategory(cfg.key);

        const scopeBadge = cfg.global == 1
          ? '<span class="cfg-scope-badge global"><i class="fi fi-rr-globe"></i> Global</span>'
          : `<span class="cfg-scope-badge site"><i class="fi fi-rr-building"></i> Site #${cfg.site_id}</span><span class="cfg-site-target">${cfg.site_domain || cfg.site_name}</span>`;

        const typeBadge = `<span class="cfg-type-badge ${type}">${type.toUpperCase()}</span>`;

        // Value column rendering based on type
        let valHtml = "";
        if (type === "boolean") {
          const isChecked = cfg.raw_value == "1" || cfg.raw_value.toLowerCase() === "true";
          valHtml = `
            <div class="cfg-val-box">
              <label class="cfg-switch">
                <input type="checkbox" class="cfg-boolean-toggle" data-id="${cfg.id}" ${isChecked ? "checked" : ""}>
                <span class="cfg-slider"></span>
              </label>
              <span>${isChecked ? "True (1)" : "False (0)"}</span>
            </div>`;
        } else if (type === "secret") {
          const masked = "••••••••••••••••";
          valHtml = `
            <div class="cfg-val-box">
              <span class="cfg-secret-mask">${masked}</span>
              <span class="cfg-secret-plain" style="display:none;">${cfg.value}</span>
              <button type="button" class="cfg-btn-reveal btn-toggle-secret" title="Reveal / Hide Secret"><i class="fi fi-rr-eye"></i></button>
              <button type="button" class="cfg-btn-copy-val btn-copy-config-val" data-val="${cfg.raw_value}" title="Copy Value"><i class="fi fi-rr-copy"></i></button>
            </div>`;
        } else if (type === "json") {
          valHtml = `
            <div class="cfg-val-box">
              <code>${cfg.value.substring(0, 36)}${cfg.value.length > 36 ? "..." : ""}</code>
              <button type="button" class="cfg-btn-subtle btn-inspect-json" data-id="${cfg.id}" style="padding: 2px 6px; font-size: 11px;"><i class="fi fi-rr-search"></i> JSON</button>
              <button type="button" class="cfg-btn-copy-val btn-copy-config-val" data-val="${cfg.raw_value}" title="Copy JSON"><i class="fi fi-rr-copy"></i></button>
            </div>`;
        } else {
          valHtml = `
            <div class="cfg-val-box">
              <span>${cfg.value || "<em>(empty)</em>"}</span>
              <button type="button" class="cfg-btn-copy-val btn-copy-config-val" data-val="${cfg.raw_value}" title="Copy Value"><i class="fi fi-rr-copy"></i></button>
            </div>`;
        }

        const statusPill = cfg.status == 1
          ? `<span class="cfg-status-pill active btn-toggle-config-status" data-id="${cfg.id}" data-status="1" title="Click to disable"><i class="fi fi-rr-check"></i> Active</span>`
          : `<span class="cfg-status-pill inactive btn-toggle-config-status" data-id="${cfg.id}" data-status="0" title="Click to enable"><i class="fi fi-rr-cross"></i> Disabled</span>`;

        const updated = cfg.updated_at ? cfg.updated_at.substring(0, 10) : "-";

        html += `
          <tr>
            <td>
              <div class="cfg-key-col">
                <span class="cfg-key-code">${cfg.key}</span>
                <span class="cfg-key-desc">${self.getCategoryLabel(cat)}</span>
              </div>
            </td>
            <td>${scopeBadge}</td>
            <td>${typeBadge}</td>
            <td>${valHtml}</td>
            <td>${statusPill}</td>
            <td><small class="text-muted">${updated}</small></td>
            <td>
              <div class="cfg-actions-cell">
                <button type="button" class="cfg-action-btn btn-copy-config-key" data-key="${cfg.key}" title="Copy Key Name"><i class="fi fi-rr-copy-alt"></i></button>
                <button type="button" class="cfg-action-btn btn-edit-config" data-id="${cfg.id}" title="Edit Parameter"><i class="fi fi-rr-edit"></i></button>
                <button type="button" class="cfg-action-btn delete btn-delete-config" data-id="${cfg.id}" data-key="${cfg.key}" title="Delete Parameter"><i class="fi fi-rr-trash"></i></button>
              </div>
            </td>
          </tr>`;
      });

      $("#cfgTbody").html(html);
    },

    updateKpiCounters: function () {
      const total = this.configsList.length;
      const globals = this.configsList.filter((c) => c.global == 1).length;
      const sites = this.configsList.filter((c) => c.global == 0).length;
      const secrets = this.configsList.filter((c) => this.detectType(c.key, c.raw_value) === "secret").length;

      $("#cfgKpiTotal").text(total);
      $("#cfgKpiGlobal").text(globals);
      $("#cfgKpiSite").text(sites);
      $("#cfgKpiSecrets").text(secrets);
    },

    // ------------------------------------------------------------------------
    // Code View Studio (.ENV & JSON Engine)
    // ------------------------------------------------------------------------
    toggleView: function () {
      if (this.currentView === "visual") {
        this.currentView = "code";
        $("#cfgVisualView").removeClass("active");
        $("#cfgCodeView").addClass("active");
        $("#btnToggleCodeViewText").text("Visual View (Studio)");
        $("#btnToggleCodeView").find("i").removeClass("fi-rr-terminal").addClass("fi-rr-table");
        this.syncCodeEditor();
      } else {
        this.currentView = "visual";
        $("#cfgCodeView").removeClass("active");
        $("#cfgVisualView").addClass("active");
        $("#btnToggleCodeViewText").text("Code View (.env)");
        $("#btnToggleCodeView").find("i").removeClass("fi-rr-table").addClass("fi-rr-terminal");
        this.renderVisualTable();
      }
    },

    switchCodeFormat: function (format) {
      this.codeFormat = format;
      $(".cfg-code-format-btn").removeClass("active");
      $(`.cfg-code-format-btn[data-format="${format}"]`).addClass("active");
      this.syncCodeEditor();
    },

    syncCodeEditor: function () {
      const self = this;
      let code = "";

      if (this.codeFormat === "env") {
        code += "# =========================================================\n";
        code += "# Blumox SaaS System & Environment Configurations\n";
        code += `# Scope: ${this.activeScope.toUpperCase()} | Generated: ${new Date().toISOString()}\n`;
        code += "# =========================================================\n\n";

        // Group by category for clean readability
        const groups = {
          general: [],
          security: [],
          features: [],
          quotas: [],
          comms: [],
          other: []
        };

        this.configsList.forEach((c) => {
          const cat = self.detectCategory(c.key);
          if (groups[cat]) groups[cat].push(c);
          else groups.other.push(c);
        });

        Object.keys(groups).forEach((gKey) => {
          const list = groups[gKey];
          if (list.length) {
            code += `\n# --- ${self.getCategoryLabel(gKey).toUpperCase()} ---\n`;
            list.forEach((item) => {
              let val = item.raw_value;
              if (val.includes(" ") || val.includes('"') || val.includes("\n")) {
                val = `"${val.replace(/"/g, '\\"')}"`;
              }
              code += `${item.key}=${val}\n`;
            });
          }
        });
      } else {
        // JSON Format
        const obj = {};
        this.configsList.forEach((item) => {
          let val = item.raw_value;
          if (val === "1" || val === "0") {
            val = val === "1";
          } else if (/^\d+$/.test(val)) {
            val = parseInt(val, 10);
          } else {
            try {
              val = JSON.parse(val);
            } catch (e) {}
          }
          obj[item.key] = val;
        });
        code = JSON.stringify(obj, null, 2);
      }

      $("#cfgRawEditor").val(code);
      this.updateCodeEditorGutter();
      this.validateCodeSyntax();
    },

    updateCodeEditorGutter: function () {
      const text = $("#cfgRawEditor").val() || "";
      const lines = text.split("\n").length;
      let gutterHtml = "";
      for (let i = 1; i <= lines; i++) {
        gutterHtml += i + "<br>";
      }
      $("#cfgEditorGutter").html(gutterHtml);
      $("#cfgCodeLineCount").text(`${lines} lines`);
    },

    validateCodeSyntax: function () {
      const text = $("#cfgRawEditor").val() || "";
      let isValid = true;
      let errorMsg = "";

      if (this.codeFormat === "json") {
        try {
          JSON.parse(text);
        } catch (e) {
          isValid = false;
          errorMsg = e.message;
        }
      } else {
        // Check .env lines
        const lines = text.split("\n");
        for (let i = 0; i < lines.length; i++) {
          const l = lines[i].trim();
          if (l === "" || l.startsWith("#")) continue;
          if (!l.includes("=")) {
            isValid = false;
            errorMsg = `Line ${i + 1}: Missing "=" assignment`;
            break;
          }
        }
      }

      const $badge = $("#cfgSyntaxBadge");
      if (isValid) {
        $badge.removeClass("invalid").addClass("valid").html('<i class="fi fi-rr-check"></i> Syntax Valid');
      } else {
        $badge.removeClass("valid").addClass("invalid").html(`<i class="fi fi-rr-cross"></i> ${errorMsg}`);
      }

      return isValid;
    },

    formatRawCode: function () {
      if (this.codeFormat === "json") {
        try {
          const parsed = JSON.parse($("#cfgRawEditor").val());
          $("#cfgRawEditor").val(JSON.stringify(parsed, null, 2));
          this.updateCodeEditorGutter();
          this.validateCodeSyntax();
          swal("Formatted", "JSON code formatted cleanly.", "success");
        } catch (e) {
          swal("Format Error", "Cannot format invalid JSON: " + e.message, "error");
        }
      } else {
        this.syncCodeEditor();
        swal("Formatted", ".ENV code formatted with section groupings.", "success");
      }
    },

    saveRawCode: function () {
      if (!this.validateCodeSyntax()) {
        swal("Syntax Error", "Please correct the syntax errors before saving.", "error");
        return;
      }

      const self = this;
      const raw = $("#cfgRawEditor").val();
      const isGlobal = this.activeScope === "global" ? 1 : 0;
      const siteId = this.activeSiteId !== "all" ? parseInt(this.activeSiteId, 10) : 1;

      const action = this.codeFormat === "json" ? "save_raw_json" : "save_raw_env";
      const payload = {
        action: action,
        global: isGlobal,
        site_id: siteId
      };

      if (this.codeFormat === "json") {
        payload.raw_json = raw;
      } else {
        payload.raw_env = raw;
      }

      swal({
        title: "Confirm Code Save?",
        text: "This will bulk parse and update parameters in the database.",
        icon: "warning",
        buttons: ["Cancel", "Yes, Save All"],
        dangerMode: true
      }).then((proceed) => {
        if (!proceed) return;

        $.ajax({
          url: "/api/v3/control_panel/configurations/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify(payload),
          success: function (res) {
            swal("Saved", res.msg || "Configurations updated successfully!", "success");
            self.loadConfigurations();
          },
          error: function (xhr) {
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving configurations.";
            swal("Save Failed", err, "error");
          }
        });
      });
    },

    exportAsEnvFile: function () {
      const text = $("#cfgRawEditor").val();
      const blob = new Blob([text], { type: "text/plain;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `blumox_${this.activeScope}_config.env`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    },

    // ------------------------------------------------------------------------
    // Parameter Modals & CRUD
    // ------------------------------------------------------------------------
    openConfigModal: function (cfg) {
      if (cfg) {
        $("#modalConfigTitle").text(`Edit Parameter: ${cfg.key}`);
        $("#cfgParamId").val(cfg.id);
        $("#cfgParamKey").val(cfg.key).prop("readonly", true);
        $("#cfgParamScope").val(cfg.global);
        $("#cfgParamSiteId").val(cfg.site_id);
        $("#cfgModalSiteRow").toggle(cfg.global == 0);
        $("#cfgParamType").val(this.detectType(cfg.key, cfg.raw_value));
        $("#cfgParamStatus").val(cfg.status);
        $("#cfgParamValue").val(cfg.raw_value);
      } else {
        $("#modalConfigTitle").text("Add Configuration Parameter");
        $("#formConfigParam")[0].reset();
        $("#cfgParamId").val("0");
        $("#cfgParamKey").prop("readonly", false);
        $("#cfgParamScope").val(this.activeScope === "site" ? "0" : "1");
        $("#cfgModalSiteRow").toggle(this.activeScope === "site");
        if (this.activeSiteId !== "all") {
          $("#cfgParamSiteId").val(this.activeSiteId);
        }
      }
      $("#modalConfigParam").addClass("active");
    },

    submitSaveConfigParam: function () {
      const self = this;
      const data = {
        action: "save",
        config_id: parseInt($("#cfgParamId").val(), 10) || 0,
        key: $("#cfgParamKey").val().trim(),
        value: $("#cfgParamValue").val().trim(),
        global: $("#cfgParamScope").val() == "1" ? 1 : 0,
        site_id: parseInt($("#cfgParamSiteId").val(), 10) || 1,
        status: parseInt($("#cfgParamStatus").val(), 10)
      };

      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (res) {
          $("#modalConfigParam").removeClass("active");
          swal("Success", res.msg, "success");
          self.loadConfigurations();
        },
        error: function (xhr) {
          const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error saving parameter.";
          swal("Error", err, "error");
        }
      });
    },

    updateConfigValue: function (id, newVal) {
      const self = this;
      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "update_value", config_id: id, value: newVal }),
        success: function () {
          const target = self.configsList.find((c) => c.id == id);
          if (target) target.raw_value = newVal;
          self.syncCodeEditor();
        },
        error: function () {
          swal("Update Failed", "Could not update parameter value.", "error");
          self.loadConfigurations();
        }
      });
    },

    toggleStatus: function (id, status) {
      const self = this;
      $.ajax({
        url: "/api/v3/control_panel/configurations/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "toggle_status", config_id: id, status: status }),
        success: function () {
          self.loadConfigurations();
        },
        error: function () {
          swal("Error", "Could not toggle parameter status.", "error");
        }
      });
    },

    deleteConfigPrompt: function (id, key) {
      const self = this;
      swal({
        title: `Delete Parameter "${key}"?`,
        text: "This will permanently remove this configuration key from the database.",
        icon: "warning",
        buttons: ["Cancel", "Yes, Delete"],
        dangerMode: true
      }).then((confirmed) => {
        if (!confirmed) return;
        $.ajax({
          url: "/api/v3/control_panel/configurations/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ action: "delete", config_id: id }),
          success: function (res) {
            swal("Deleted", res.msg, "success");
            self.loadConfigurations();
          },
          error: function (xhr) {
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error deleting parameter.";
            swal("Delete Failed", err, "error");
          }
        });
      });
    },

    // ------------------------------------------------------------------------
    // Helpers & Classifiers
    // ------------------------------------------------------------------------
    detectType: function (key, val) {
      const k = key.toLowerCase();
      if (k.includes("secret") || k.includes("token") || k.includes("password") || k.includes("api_key") || k.includes("private_key")) {
        return "secret";
      }
      if (val === "1" || val === "0" || val === "true" || val === "false" || k.startsWith("enable_") || k.startsWith("feature_") || k.endsWith("_visible")) {
        return "boolean";
      }
      if ((val.startsWith("{") && val.endsWith("}")) || (val.startsWith("[") && val.endsWith("]"))) {
        try {
          JSON.parse(val);
          return "json";
        } catch (e) {}
      }
      if (/^-?\d+(\.\d+)?$/.test(val) && !k.includes("phone") && !k.includes("date")) {
        return "number";
      }
      return "string";
    },

    detectCategory: function (key) {
      const k = key.toLowerCase();
      if (k.includes("secret") || k.includes("token") || k.includes("key") || k.includes("password") || k.includes("recaptcha") || k.includes("auth")) {
        return "security";
      }
      if (k.startsWith("feature_") || k.startsWith("enable_") || k.endsWith("_visible") || k.includes("flag")) {
        return "features";
      }
      if (k.includes("limit") || k.includes("quota") || k.includes("storage") || k.includes("price") || k.includes("plan") || k.includes("expiry")) {
        return "quotas";
      }
      if (k.includes("whatsapp") || k.includes("smtp") || k.includes("mail") || k.includes("sms") || k.includes("notify")) {
        return "comms";
      }
      return "general";
    },

    getCategoryLabel: function (cat) {
      const map = {
        general: "General & Branding",
        security: "Security & API Keys",
        features: "Features & Flags",
        quotas: "Limits & Quotas",
        comms: "Communications",
        other: "Other"
      };
      return map[cat] || "General";
    },

    copyToClipboard: function (text, message) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          swal("Copied", message, "success");
        });
      } else {
        const $temp = $("<textarea>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        swal("Copied", message, "success");
      }
    }
  };

  // Full page load
  $(document).ready(function () {
    V3ConfigurationsStudio.init();
  });

  // SPA AJAX Navigation
  $(document).on("spa:loaded", function () {
    V3ConfigurationsStudio.onPageEnter();
  });

  // History Popstate
  $(window).on("popstate", function () {
    if ($("#configurationsApp").length) {
      V3ConfigurationsStudio.onPageEnter();
    }
  });

  window.V3ConfigurationsStudio = V3ConfigurationsStudio;
})(window, jQuery);
