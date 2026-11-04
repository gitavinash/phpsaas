/**
 * V3 Integration Hub & Plugin Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/integration.js
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

  window.V3Integration = {
    activeCategory: "all",
    searchQuery: "",
    eventsBound: false,

    init: function () {
      this.initHub();
      this.initPluginForms();
      this.initFacebookPagesOverview();
      this.initFacebookPageForms();
      this.initFacebookFormLeads();

      if (!this.eventsBound) {
        this.bindGlobalEvents();
        this.eventsBound = true;
      }
    },

    // -------------------------------------------------------------
    // 1. Integration Hub Main Listing Page
    // -------------------------------------------------------------
    initHub: function () {
      const $app = $("#integrationApp");
      if (!$app.length) return;

      this.activeCategory = "all";
      this.searchQuery = "";
      $("#integrationSearchInput").val("");
      this.updateTabCounts();
      this.filterCards();
      
      // Initialize card action visibility based on initial checkbox states
      const self = this;
      $(".integration-card").each(function () {
        const $card = $(this);
        const isChecked = $card.find(".integration-active-toggle").is(":checked");
        self.updateCardActionState($card, isChecked);
      });

      this.loadHubStatus();
    },

    updateTabCounts: function () {
      const countAll = $(".integration-card").length;
      const countLeadgen = $('.integration-card[data-category="leadgen"]').length;
      const countMessaging = $('.integration-card[data-category="messaging"]').length;
      const countPayments = $('.integration-card[data-category="payments"]').length;
      const countDeveloper = $('.integration-card[data-category="developer"]').length;

      if (countAll > 0) $("#countAll").text(countAll);
      if (countLeadgen > 0) $("#countLeadgen").text(countLeadgen);
      if (countMessaging > 0) $("#countMessaging").text(countMessaging);
      if (countPayments > 0) $("#countPayments").text(countPayments);
      if (countDeveloper > 0) $("#countDeveloper").text(countDeveloper);
    },

    updateCardActionState: function ($card, isActive) {
      const $status = $card.find(".integration-status-pill");
      const $btn = $card.find(".btn-integration-primary, .btn-integration-outline");
      if (isActive) {
        $card.removeClass("is-disabled").addClass("is-active-card");
        $status.removeClass("is-idle").addClass("is-active");
        $status.find(".status-text").text("Active");
        $btn.removeClass("btn-integration-outline").addClass("btn-integration-primary");
      } else {
        $card.removeClass("is-active-card").addClass("is-disabled");
        $status.removeClass("is-active").addClass("is-idle");
        $status.find(".status-text").text("Disabled");
        $btn.removeClass("btn-integration-primary").addClass("btn-integration-outline");
      }
    },

    updateAsidePluginState: function (slug, isActive, hasConnected) {
      if (slug === "whatsapp_web") {
        let isDeviceConnected = hasConnected;
        if (isDeviceConnected === undefined) {
          const $waCard = $('.integration-card[data-slug="whatsapp_web"]');
          if ($waCard.length) {
            isDeviceConnected = $waCard.attr("data-connected") === "1" || $waCard.data("connected") === true;
          }
        }
        if (isActive && isDeviceConnected === false) {
          isActive = false;
        }
      }

      const $asideItems = $('[data-aside-plugin="' + slug + '"]');
      const $asideAvailable = $('[data-aside-plugin="' + slug + '_available"]');
      if (isActive) {
        $asideItems.removeClass("plugin-item-disabled is-hidden").addClass("plugin-item-active").show();
        $asideItems.find(".aside-plugin-status-pill").removeClass("is-idle").addClass("is-active").text("Active");
        $asideAvailable.addClass("is-hidden").hide();
      } else {
        $asideItems.removeClass("plugin-item-active").addClass("plugin-item-disabled is-hidden").hide();
        $asideAvailable.removeClass("is-hidden").show();
      }

      // Live update counts
      const activeCount = $("#asidePluginsActivePanel .aside-plugin-item.plugin-item-active:not(.is-hidden)").length;
      const availCount = $("#asidePluginsAvailablePanel .aside-plugin-item:not(.is-hidden)").length;
      if ($("#asideActivePluginBadge").length) {
        $("#asideActivePluginBadge").text(activeCount + " Active");
      }
      if ($("#asideTabActiveCount").length) {
        $("#asideTabActiveCount").text(activeCount);
      }
      if ($("#asideTabAvailableCount").length) {
        $("#asideTabAvailableCount").text(availCount);
      }
    },

    togglePlugin: function (slug, key, isActive, callback) {
      const self = this;
      return $.ajax({
        url: "/api/v3/integration/",
        method: "POST",
        data: JSON.stringify({
          plugin_slug: slug,
          toggle_key: key,
          is_active: isActive ? 1 : 0,
        }),
        contentType: "application/json",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            self.showToast(res.msg || (isActive ? "Integration activated" : "Integration deactivated"), "success");
            if (typeof callback === "function") callback(true, res);
          } else {
            self.showToast(res.msg || "Failed to update integration state", "error");
            if (typeof callback === "function") callback(false, res);
          }
        },
        error: function () {
          self.showToast("Network error communicating with integrations API", "error");
          if (typeof callback === "function") callback(false);
        }
      });
    },

    loadHubStatus: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/integration/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && Array.isArray(res.plugins)) {
            res.plugins.forEach(function (plugin) {
              const $card = $('.integration-card[data-slug="' + plugin.slug + '"]');
              const isActive = !!plugin.is_active;
              if ($card.length) {
                const $toggle = $card.find(".integration-active-toggle");
                if ($toggle.length) {
                  $toggle.prop("checked", isActive);
                }
                self.updateCardActionState($card, isActive);
              }
              self.updateAsidePluginState(plugin.slug, isActive);
            });
          }
        },
      });
    },

    bindGlobalEvents: function () {
      const self = this;

      // Category Tabs Switching
      $(document).on("click", ".integration-cat-tab", function () {
        $(".integration-cat-tab").removeClass("active");
        $(this).addClass("active");
        self.activeCategory = $(this).data("category") || "all";
        self.filterCards();
      });

      // Interactive Real-Time Search
      $(document).on("input keyup", "#integrationSearchInput", function () {
        self.searchQuery = $(this).val().toLowerCase().trim();
        self.filterCards();
      });

      const paymentGateways = [
        "razorpay",
        "instamojo",
        "sabpaisa",
        "phonepe",
        "worldline",
        "payu",
        "phi_commerce",
        "cashfree"
      ];

      // Instant Active/Inactive Toggle Switch
      $(document).on("change", ".integration-active-toggle", function () {
        const $toggle = $(this);
        const $card = $toggle.closest(".integration-card");
        const slug = $toggle.data("slug");
        const key = $toggle.data("key");
        const isChecked = $toggle.is(":checked");
        const isPg = paymentGateways.includes(slug);

        // Optimistic UI updates
        self.updateCardActionState($card, isChecked);
        if (slug === "whatsapp_web") {
          const isCardConn = $card.attr("data-connected") === "1" || $card.data("connected") === true;
          self.updateAsidePluginState(slug, isChecked, isCardConn);
        } else {
          self.updateAsidePluginState(slug, isChecked);
        }

        // If activating a payment gateway, deactivate all other payment gateways
        if (isChecked && isPg) {
          paymentGateways.forEach(function (otherSlug) {
            if (otherSlug !== slug) {
              const $otherCard = $('.integration-card[data-slug="' + otherSlug + '"]');
              if ($otherCard.length) {
                $otherCard.find(".integration-active-toggle").prop("checked", false);
                self.updateCardActionState($otherCard, false);
              }
              self.updateAsidePluginState(otherSlug, false);
            }
          });
        }

        $toggle.prop("disabled", true);

        $.ajax({
          url: "/api/v3/integration/",
          method: "POST",
          data: JSON.stringify({
            plugin_slug: slug,
            toggle_key: key,
            is_active: isChecked ? 1 : 0,
          }),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $toggle.prop("disabled", false);
            if (res && res.status === "success") {
              if (slug === "whatsapp_web" && res.has_connected_device !== undefined) {
                $card.attr("data-connected", res.has_connected_device ? "1" : "0");
                self.updateAsidePluginState(slug, isChecked, !!res.has_connected_device);
              }
              // Ensure any deactivated slugs returned by API are synced
              if (Array.isArray(res.deactivated_slugs)) {
                res.deactivated_slugs.forEach(function (deactSlug) {
                  const $deactCard = $('.integration-card[data-slug="' + deactSlug + '"]');
                  if ($deactCard.length) {
                    $deactCard.find(".integration-active-toggle").prop("checked", false);
                    self.updateCardActionState($deactCard, false);
                  }
                  self.updateAsidePluginState(deactSlug, false);
                });
              }
              self.showToast(res.msg || (isChecked ? "Integration activated" : "Integration deactivated"), "success");
            } else {
              // Rollback
              $toggle.prop("checked", !isChecked);
              self.updateCardActionState($card, !isChecked);
              self.updateAsidePluginState(slug, !isChecked);
              self.showToast(res.msg || "Failed to update integration state", "error");
            }
          },
          error: function () {
            // Rollback
            $toggle.prop("disabled", false);
            $toggle.prop("checked", !isChecked);
            self.updateCardActionState($card, !isChecked);
            self.updateAsidePluginState(slug, !isChecked);
            self.showToast("Network error communicating with integrations API", "error");
          },
        });
      });
    },

    filterCards: function () {
      const self = this;
      let visibleCount = 0;

      $(".integration-card").each(function () {
        const $card = $(this);
        const cardCat = $card.data("category") || "";
        const title = $card.find(".integration-title, .integration-meta h3").text().toLowerCase();
        const desc = $card.find(".integration-desc").text().toLowerCase();
        const tag = $card.find(".integration-tag, .integration-card-pill, .integration-feature-chips").text().toLowerCase();

        const matchesCat = self.activeCategory === "all" || cardCat === self.activeCategory;
        const matchesSearch = !self.searchQuery || title.includes(self.searchQuery) || desc.includes(self.searchQuery) || tag.includes(self.searchQuery);

        if (matchesCat && matchesSearch) {
          $card.show();
          visibleCount++;
        } else {
          $card.hide();
        }
      });
    },

    // -------------------------------------------------------------
    // 2. Individual Plugin Configuration Pages
    // -------------------------------------------------------------
    initPluginForms: function () {
      this.initWhatsappPlugin();
      this.initWhatsappWebPlugin();
      this.initSmtpPlugin();
      this.initGoogleAdsPlugin();
      this.initWebhooksPlugin();
      // Payment Gateways
      this.initRazorpayPlugin();
      this.initInstamojoPlugin();
      this.initSabpaisaPlugin();
      this.initPhonepePlugin();
      this.initWorldlinePlugin();
      this.initPayuPlugin();
      this.initPhiCommercePlugin();
      this.initCashfreePlugin();
      this.initUpiPlugin();
    },

    // WhatsApp
    initWhatsappPlugin: function () {
      const self = this;
      const $form = $("#pluginWhatsappForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/whatsapp/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#waActiveToggle").prop("checked", d.enable_whatsapp_business_api === "1");
            $("#waPhoneNumberId").val(d.whatsapp_phone_number_id || "");
            $("#waWabaId").val(d.whatsapp_waba_id || "");
            $("#waAccessToken").val(d.whatsapp_access_token || "");
            $("#waCompanyNumber").val(d.company_whatsapp_number || "");
            $("#waVerifyToken").val(d.whatsapp_webhook_verify_token || "isw_wa_verify");
            $("#waEvtEnquiry").prop("checked", d.company_whatsapp_enquiry_thank_you === "1");
            $("#waEvtPayment").prop("checked", d.company_whatsapp_payment_request === "1");
            $("#waEvtItinerary").prop("checked", d.company_whatsapp_itinerary_sent === "1");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveWhatsappPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_whatsapp_business_api: $("#waActiveToggle").is(":checked") ? "1" : "0",
          whatsapp_phone_number_id: $("#waPhoneNumberId").val().trim(),
          whatsapp_waba_id: $("#waWabaId").val().trim(),
          whatsapp_access_token: $("#waAccessToken").val().trim(),
          company_whatsapp_number: $("#waCompanyNumber").val().trim(),
          company_whatsapp_enquiry_thank_you: $("#waEvtEnquiry").is(":checked") ? "1" : "0",
          company_whatsapp_payment_request: $("#waEvtPayment").is(":checked") ? "1" : "0",
          company_whatsapp_itinerary_sent: $("#waEvtItinerary").is(":checked") ? "1" : "0",
        };

        $.ajax({
          url: "/api/v3/integration/whatsapp/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "WhatsApp configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save WhatsApp settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving WhatsApp settings.", "error");
          },
        });
      });
    },

    // WhatsApp Web (QR Multi-Device)
    initWhatsappWebPlugin: function () {
      const self = this;
      const $app = $("#pluginWhatsappWebApp");
      if (!$app.length) return;

      let qrInterval = null;
      let countdown = 60;
      let statusPollInterval = null;
      let modalPollInterval = null;
      let modalCountdownInterval = null;
      let currentModalSessionId = null;
      let currentAgentSessionId = null;
      let stateDevices = [];
      let stateAgents = [];
      let stateDeviceLimit = 1;
      let stateDeviceCount = 0;
      let stateLimitReached = false;
      let isAdmin = false;

      // 1. Load Devices and Initial State
      function loadHubState() {
        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=get_devices",
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.status === "success") {
              isAdmin = !!res.is_admin;
              stateDevices = res.devices || [];
              stateAgents = res.agents || [];
              stateDeviceLimit = res.device_limit || 1;
              stateDeviceCount = res.device_count !== undefined ? res.device_count : stateDevices.length;
              stateLimitReached = res.limit_reached !== undefined ? res.limit_reached : (stateDeviceCount >= stateDeviceLimit);

              const isActive = !!res.is_active;
              $("#waWebActiveToggle").prop("checked", isActive);
              const hasConnectedDev = (res.has_connected_device !== undefined)
                ? !!res.has_connected_device
                : !!(stateDevices && stateDevices.some(function (d) {
                    return (d.status === "connected" || d.device_status === "connected");
                  }));
              self.updateAsidePluginState("whatsapp_web", isActive, hasConnectedDev);
              updateWaWebSectionsVisibility(isActive);

              if (isAdmin) {
                renderAdminView(res);
              } else {
                renderAgentView(res);
              }
            }
          }
        });

        // Also fetch configs for sync settings
        $.ajax({
          url: "/api/v3/integration/whatsapp_web/",
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.status === "success" && res.data) {
              const d = res.data;
              $("#waWebSyncIncoming").prop("checked", d.whatsapp_web_sync_incoming === "1");
              $("#waWebAutoCreateLead").prop("checked", d.whatsapp_web_auto_create_lead === "1");
              if (d.whatsapp_web_gateway_url && $("#waWebGatewayUrl").length) {
                $("#waWebGatewayUrl").val(d.whatsapp_web_gateway_url);
              }
              if (res.webhook_url) {
                $("#waWebhookUrlDisplay").val(res.webhook_url);
              }
            }
          }
        });
      }

      // 2. Render Admin Multi-Device View
      function renderAdminView(data) {
        $("#waAgentAssignedSection").addClass("d-none");
        $("#waAgentUnassignedSection").addClass("d-none");
        $("#waAdminSection").removeClass("d-none");

        const devices = data.devices || [];
        const deviceLimit = data.device_limit || stateDeviceLimit || 1;
        const deviceCount = data.device_count !== undefined ? data.device_count : devices.length;
        const limitReached = data.limit_reached !== undefined ? data.limit_reached : (deviceCount >= deviceLimit);
        stateDeviceLimit = deviceLimit;
        stateDeviceCount = deviceCount;
        stateLimitReached = limitReached;

        $("#waAdminDeviceCount").text(deviceCount + " / " + deviceLimit);
        if (limitReached) {
          $("#waAdminDeviceCount").addClass("wa-count-limit-reached");
          $("#btnBuyDeviceLicense").removeClass("d-none");
          $("#btnOpenAddDeviceModal").addClass("d-none");
        } else {
          $("#waAdminDeviceCount").removeClass("wa-count-limit-reached");
          $("#btnBuyDeviceLicense").addClass("d-none");
          $("#btnOpenAddDeviceModal").removeClass("d-none");
        }

        const $grid = $("#waAdminDevicesGrid");
        if (devices.length === 0) {
          const actionBtnHtml = limitReached
            ? '<button type="button" class="btn-wa-buy-license btn-contact-aleya btn-upgrade" title="Upgrade WhatsApp device licenses" data-modal-title="WhatsApp Device Limit Reached" data-modal-notice="You have reached your WhatsApp device license limit. Please contact Aleya to connect additional devices."><i class="fi fi-rr-sparkles"></i> <span>Buy More WhatsApp Licenses</span></button>'
            : '<button type="button" class="btn-integration-primary btn-sm btn-empty-add-device"><i class="fi fi-rr-plus"></i> <span>Add WhatsApp Device</span></button>';

          $grid.html(
            '<div class="wa-devices-empty-state">' +
              '<div class="wa-devices-empty-icon">' +
                '<i class="fi fi-rr-smartphone"></i>' +
              '</div>' +
              '<h4 class="wa-devices-empty-title">No WhatsApp Devices Linked Yet</h4>' +
              '<p class="wa-devices-empty-desc">Connect your first WhatsApp number to start chatting with CRM leads, sync messages in real-time, and assign lines to your sales team.</p>' +
              '<div class="wa-devices-empty-action">' +
                actionBtnHtml +
              '</div>' +
            '</div>'
          );
          return;
        }

        let html = "";
        devices.forEach(function (dev) {
          const isConn = dev.status === "connected";
          const statusClass = isConn ? "connected" : (dev.status === "qr_ready" ? "qr_ready" : "disconnected");
          const statusLabel = isConn ? "Connected" : (dev.status === "qr_ready" ? "QR Ready" : "Disconnected");

          let agentOptions = '<option value="0">-- Unassigned (Available) --</option>';
          if (Array.isArray(stateAgents)) {
            stateAgents.forEach(function (ag) {
              const isSelected = (dev.assigned_user_id && Number(dev.assigned_user_id) === Number(ag.user_id));
              // Check if agent is assigned to a different device
              const isOtherAssigned = !isSelected && stateDevices.some(function (od) {
                return od.device_id !== dev.device_id && Number(od.assigned_user_id) === Number(ag.user_id);
              });
              const disabledAttr = isOtherAssigned ? ' disabled="disabled"' : '';
              const extraNote = isOtherAssigned ? ' (Already assigned)' : '';
              const roleNote = ag.role ? ' [' + ag.role + ']' : '';
              agentOptions += '<option value="' + ag.user_id + '"' + (isSelected ? ' selected="selected"' : '') + disabledAttr + '>' + ag.name + roleNote + extraNote + '</option>';
            });
          }

          const defaultBadgeHtml = dev.is_default
            ? '<span class="wa-device-badge default-badge" title="Default device for sending alerts and notifications to customers"><i class="fi fi-rr-star"></i> Default</span>'
            : '';

          const defaultBtnHtml = dev.is_default
            ? '<button type="button" class="btn-device-action btn-device-default is-default" title="Default line for alerts &amp; customer notifications"><i class="fi fi-rr-check-circle"></i> <span>Default Line</span></button>'
            : '<button type="button" class="btn-device-action btn-device-default btn-set-default" data-device-id="' + dev.device_id + '" title="Mark as default to send alerts and notifications to customers"><i class="fi fi-rr-star"></i> <span>Set as Default</span></button>';

          html += '<div class="wa-device-card" data-device-id="' + dev.device_id + '" data-session-id="' + dev.session_id + '">' +
                    '<div class="wa-device-card-header">' +
                      '<div class="wa-device-phone-wrap">' +
                        '<div class="wa-device-icon"><i class="fi fi-rr-smartphone"></i></div>' +
                        '<div>' +
                          '<span class="wa-device-phone-number">' + (dev.phone || 'Device #' + dev.device_id) + '</span>' +
                          '<span class="wa-device-pushname">' + (dev.name || 'WhatsApp Web') + '</span>' +
                        '</div>' +
                      '</div>' +
                      '<div class="wa-device-header-badges">' +
                        defaultBadgeHtml +
                        '<span class="wa-device-badge ' + statusClass + '">' +
                          '<span class="wa-device-badge-dot"></span> ' + statusLabel +
                        '</span>' +
                      '</div>' +
                    '</div>' +
                    '<div class="wa-device-assignment-box">' +
                      '<label class="wa-device-assignment-label"><i class="fi fi-rr-user"></i> Assigned Sales Agent</label>' +
                      '<select class="wa-agent-select" data-device-id="' + dev.device_id + '" title="Assign this WhatsApp number to a sales agent">' +
                        agentOptions +
                      '</select>' +
                    '</div>' +
                    '<div class="wa-device-card-meta">' +
                      '<span>Session: ' + dev.session_id + '</span>' +
                      '<span>Synced: ' + dev.last_sync + '</span>' +
                    '</div>' +
                    '<div class="wa-device-card-actions">' +
                      defaultBtnHtml +
                      '<button type="button" class="btn-device-action btn-device-sync" data-session-id="' + dev.session_id + '" title="Synchronize chats from this device">' +
                        '<i class="fi fi-rr-refresh"></i> <span>Sync</span>' +
                      '</button>' +
                      '<button type="button" class="btn-device-action btn-device-disconnect" data-session-id="' + dev.session_id + '" data-device-id="' + dev.device_id + '" title="Disconnect this WhatsApp device">' +
                        '<i class="fi fi-rr-exit"></i> <span>Disconnect</span>' +
                      '</button>' +
                    '</div>' +
                  '</div>';
        });

        $grid.html(html);
      }

      // 3. Render Sales Agent View (Assigned vs Unassigned)
      function renderAgentView(data) {
        $("#waAdminSection").addClass("d-none");

        if (data.has_assigned_device && data.assigned_device) {
          // Rule: If whatsapp number is already assigned, DON'T show linked device option
          $("#waAgentUnassignedSection").addClass("d-none");
          $("#waAgentAssignedSection").removeClass("d-none");

          const dev = data.assigned_device;
          $("#waAgentDevicePhone").text(dev.phone || "Assigned WhatsApp Number");
          $("#waAgentMetaPhone").text(dev.phone || "-");
          $("#waAgentMetaName").text(dev.name || "Sales Agent WhatsApp");
          $("#waAgentMetaStatus").text(dev.status === "connected" ? "Connected & Active" : "Offline");
          $("#waAgentMetaSync").text(dev.last_sync || "Recent");
          $("#btnAgentDisconnect").attr("data-session-id", dev.session_id || "").attr("data-device-id", dev.device_id || "");

          clearInterval(qrInterval);
          clearInterval(statusPollInterval);
        } else {
          // Rule: If NO whatsapp number is assigned, SHOW linked device option (1 device limit)
          $("#waAgentAssignedSection").addClass("d-none");
          $("#waAgentUnassignedSection").removeClass("d-none");

          loadAgentQrCode();
          startAgentStatusPolling();
        }
      }

      // 4. Load QR Code for Unassigned Agent
      function loadAgentQrCode(force) {
        if (!$("#waAgentQrCodeContainer svg").length || force) {
          $("#waAgentQrCodeContainer").html('<div class="wa-qr-placeholder"><i class="fi fi-rr-spinner wa-spin"></i><span>Generating your QR Code...</span></div>');
        }
        const url = "/api/v3/integration/whatsapp_web/?action=generate_qr" + (force ? "&force=1" : "");
        $.ajax({
          url: url,
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && (res.status === "connected" || res.connection_status === "connected" || res.is_device_linked === true || res.is_connected === true)) {
              clearInterval(statusPollInterval);
              clearInterval(qrInterval);
              self.showToast("WhatsApp device linked successfully!", "success");
              loadHubState();
              return;
            }
            if (res && res.status === "success" && res.qr_svg) {
              $("#waAgentQrCodeContainer").html(res.qr_svg);
              resetAgentCountdown(Math.max(60, res.expires_in || 60));
              if (res.session_id) {
                currentAgentSessionId = res.session_id;
                startAgentStatusPolling(res.session_id);
              }
            } else if (res && res.status === "initializing") {
              setTimeout(function () {
                loadAgentQrCode(false);
              }, 1500);
            } else if (res && res.msg) {
              $("#waAgentQrCodeContainer").html('<div class="wa-qr-placeholder"><span>' + res.msg + '</span></div>');
            }
          },
          error: function (xhr) {
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Unable to generate QR code.";
            $("#waAgentQrCodeContainer").html('<div class="wa-qr-placeholder"><span>' + err + '</span></div>');
          }
        });
      }

      function resetAgentCountdown(duration) {
        clearInterval(qrInterval);
        countdown = Math.max(60, duration || 60);
        $("#waAgentQrTimer").text(countdown);
        qrInterval = setInterval(function () {
          countdown--;
          $("#waAgentQrTimer").text(countdown);
          if (countdown <= 0) {
            clearInterval(qrInterval);
            loadAgentQrCode(false);
          }
        }, 1000);
      }

      function startAgentStatusPolling(sessId) {
        if (sessId) currentAgentSessionId = sessId;
        clearInterval(statusPollInterval);
        statusPollInterval = setInterval(function () {
          if ($("#waAgentAssignedSection").is(":visible")) {
            clearInterval(statusPollInterval);
            return;
          }
          const pollUrl = currentAgentSessionId
            ? "/api/v3/integration/whatsapp_web/?action=check_status&session_id=" + encodeURIComponent(currentAgentSessionId)
            : "/api/v3/integration/whatsapp_web/?action=check_status";
          $.ajax({
            url: pollUrl,
            method: "GET",
            dataType: "json",
            success: function (res) {
              const isConnected = res && (
                res.connection_status === "connected" ||
                res.is_device_linked === true ||
                res.is_connected === true ||
                res.status === "connected"
              );
              if (isConnected) {
                clearInterval(statusPollInterval);
                clearInterval(qrInterval);
                self.showToast("WhatsApp device linked successfully!", "success");
                loadHubState();
              }
            }
          });
        }, 2500);
      }

      // 5. Admin Assign Device to Sales Agent
      $(document).on("change", ".wa-agent-select", function () {
        const $select = $(this);
        const deviceId = $select.data("device-id");
        const userId = Number($select.val());

        $select.prop("disabled", true);
        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=assign_device",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({
            device_id: deviceId,
            user_id: userId
          }),
          dataType: "json",
          success: function (res) {
            $select.prop("disabled", false);
            if (res && res.status === "success") {
              self.showToast(res.msg || "Device assigned successfully.", "success");
              window.dispatchEvent(new CustomEvent("v3:whatsapp-device-changed"));
              loadHubState();
            } else {
              self.showToast(res.msg || "Failed to assign device.", "error");
              loadHubState();
            }
          },
          error: function (xhr) {
            $select.prop("disabled", false);
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to assign device.";
            self.showToast(err, "error");
            loadHubState();
          }
        });
      });

      // 6. Admin Add Device Modal
      $("#btnOpenAddDeviceModal").on("click", function () {
        if (stateLimitReached) {
          const limitMsg = "You have reached your WhatsApp device license limit (" + stateDeviceCount + "/" + stateDeviceLimit + "). Please contact Aleya to upgrade your plan and connect additional WhatsApp devices.";
          if (typeof window.showContactAleyaModal === "function") {
            window.showContactAleyaModal("WhatsApp Device Limit Reached", limitMsg);
          } else {
            const swalFn = window.swal || window.Swal;
            if (typeof swalFn === "function") {
              swalFn({
                title: "Device Limit Reached",
                text: limitMsg,
                type: "warning",
                icon: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#2563eb"
              });
            } else {
              self.showToast(limitMsg, "warning");
            }
          }
          return;
        }

        currentModalSessionId = "device_" + Date.now();
        $("#waAddDeviceModal").addClass("is-active");
        loadModalQrCode(currentModalSessionId, true);
        startModalStatusPolling(currentModalSessionId);
      });

      $("#btnCloseAddDeviceModal").on("click", function () {
        $("#waAddDeviceModal").removeClass("is-active");
        clearInterval(modalPollInterval);
        if (modalCountdownInterval) clearInterval(modalCountdownInterval);
      });

      // Delegate click on empty state add device button
      $(document).on("click", ".btn-empty-add-device", function () {
        $("#btnOpenAddDeviceModal").trigger("click");
      });

      // Close modal on backdrop click
      $("#waAddDeviceModal").on("click", function (e) {
        if ($(e.target).is("#waAddDeviceModal")) {
          $("#waAddDeviceModal").removeClass("is-active");
          clearInterval(modalPollInterval);
          if (modalCountdownInterval) clearInterval(modalCountdownInterval);
        }
      });

      function loadModalQrCode(sessId, force) {
        if (!$("#waModalQrCodeContainer svg").length || force) {
          $("#waModalQrCodeContainer").html('<div class="wa-qr-placeholder"><i class="fi fi-rr-spinner wa-spin"></i><span>Generating QR code...</span></div>');
        }
        const url = "/api/v3/integration/whatsapp_web/?action=generate_qr&session_id=" + encodeURIComponent(sessId) + "&new_device=1" + (force ? "&force=1" : "");
        $.ajax({
          url: url,
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.limit_reached) {
              clearInterval(modalPollInterval);
              if (modalCountdownInterval) clearInterval(modalCountdownInterval);
              const limitNotice = res.msg || "You have reached your device license limit. Please contact Aleya to add more devices.";
              $("#waModalQrCodeContainer").html(
                '<div class="wa-qr-placeholder wa-qr-limit-error">' +
                  '<i class="fi fi-rr-shield-exclamation"></i>' +
                  '<h4>Device Limit Reached</h4>' +
                  '<p>' + limitNotice + '</p>' +
                  '<a href="javascript:void(0)" class="btn-wa-buy-license btn-contact-aleya" data-modal-title="WhatsApp Device Limit Reached" data-modal-notice="' + limitNotice + '"><i class="fi fi-rr-headset"></i> <span>Contact Aleya / Buy Licenses</span></a>' +
                '</div>'
              );
              return;
            }
            if (res && (res.status === "connected" || res.connection_status === "connected" || res.is_device_linked === true || res.is_connected === true)) {
              clearInterval(modalPollInterval);
              if (modalCountdownInterval) clearInterval(modalCountdownInterval);
              $("#waAddDeviceModal").removeClass("is-active");
              self.showToast("New WhatsApp device connected successfully!", "success");
              loadHubState();
              return;
            }
            if (res && res.status === "success" && res.qr_svg) {
              if (res.session_id) {
                currentModalSessionId = res.session_id;
                startModalStatusPolling(res.session_id);
              }
              $("#waModalQrCodeContainer").html(res.qr_svg);
              let modalCountdown = Math.max(60, res.expires_in || 60);
              $("#waModalQrTimer").text(modalCountdown);
              clearInterval(modalCountdownInterval);
              modalCountdownInterval = setInterval(function () {
                modalCountdown--;
                $("#waModalQrTimer").text(modalCountdown);
                if (modalCountdown <= 0) {
                  clearInterval(modalCountdownInterval);
                  if ($("#waAddDeviceModal").hasClass("is-active")) {
                    loadModalQrCode(currentModalSessionId || sessId, false);
                  }
                }
              }, 1000);
            } else if (res && res.status === "initializing") {
              setTimeout(function () {
                if ($("#waAddDeviceModal").hasClass("is-active")) {
                  loadModalQrCode(currentModalSessionId || sessId, false);
                }
              }, 1500);
            } else if (res && res.msg) {
              $("#waModalQrCodeContainer").html('<div class="wa-qr-placeholder"><span>' + res.msg + '</span></div>');
            }
          },
          error: function (xhr) {
            const errData = xhr.responseJSON;
            if (errData && errData.limit_reached) {
              clearInterval(modalPollInterval);
              if (modalCountdownInterval) clearInterval(modalCountdownInterval);
              const limitNotice = errData.msg || "You have reached your device license limit. Please contact Aleya to add more devices.";
              $("#waModalQrCodeContainer").html(
                '<div class="wa-qr-placeholder wa-qr-limit-error">' +
                  '<i class="fi fi-rr-shield-exclamation"></i>' +
                  '<h4>Device Limit Reached</h4>' +
                  '<p>' + limitNotice + '</p>' +
                  '<a href="javascript:void(0)" class="btn-wa-buy-license btn-contact-aleya" data-modal-title="WhatsApp Device Limit Reached" data-modal-notice="' + limitNotice + '"><i class="fi fi-rr-headset"></i> <span>Contact Aleya / Buy Licenses</span></a>' +
                '</div>'
              );
            } else {
              const err = errData ? errData.msg : "Unable to generate QR code.";
              $("#waModalQrCodeContainer").html('<div class="wa-qr-placeholder"><span>' + err + '</span></div>');
            }
          }
        });
      }

      function startModalStatusPolling(sessId) {
        if (sessId) currentModalSessionId = sessId;
        clearInterval(modalPollInterval);
        modalPollInterval = setInterval(function () {
          if (!$("#waAddDeviceModal").hasClass("is-active")) {
            clearInterval(modalPollInterval);
            if (modalCountdownInterval) clearInterval(modalCountdownInterval);
            return;
          }
          const pollTarget = currentModalSessionId || sessId;
          $.ajax({
            url: "/api/v3/integration/whatsapp_web/?action=check_status&session_id=" + encodeURIComponent(pollTarget),
            method: "GET",
            dataType: "json",
            success: function (res) {
              if (res && res.limit_reached) {
                clearInterval(modalPollInterval);
                if (modalCountdownInterval) clearInterval(modalCountdownInterval);
                const limitNotice = res.msg || "Device limit reached. Please contact Aleya to buy more licenses.";
                $("#waModalQrCodeContainer").html(
                  '<div class="wa-qr-placeholder wa-qr-limit-error">' +
                    '<i class="fi fi-rr-shield-exclamation"></i>' +
                    '<h4>Device Limit Reached</h4>' +
                    '<p>' + limitNotice + '</p>' +
                    '<a href="javascript:void(0)" class="btn-wa-buy-license btn-contact-aleya" data-modal-title="WhatsApp Device Limit Reached" data-modal-notice="' + limitNotice + '"><i class="fi fi-rr-headset"></i> <span>Contact Aleya / Buy Licenses</span></a>' +
                  '</div>'
                );
                return;
              }
              const isConnected = res && (
                res.connection_status === "connected" ||
                res.is_device_linked === true ||
                res.is_connected === true ||
                res.status === "connected"
              );
              if (isConnected) {
                clearInterval(modalPollInterval);
                if (modalCountdownInterval) clearInterval(modalCountdownInterval);
                $("#waAddDeviceModal").removeClass("is-active");
                self.showToast("New WhatsApp device connected successfully!", "success");
                loadHubState();
                setTimeout(function () {
                  loadHubState();
                }, 1000);
              }
            }
          });
        }, 2500);
      }

      $("#btnModalRefreshQr").on("click", function () {
        if (currentModalSessionId) loadModalQrCode(currentModalSessionId, true);
      });

      // Modal Phone Pairing
      $("#btnModalRequestPairingCode").on("click", function () {
        const phone = $("#waModalPairingPhoneInput").val().trim();
        if (!phone) {
          self.showToast("Please enter phone number with country code.", "warning");
          return;
        }
        const $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Requesting...');

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=pairing_code",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({
            phone: phone,
            session_id: currentModalSessionId,
            new_device: 1
          }),
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Code</span>');
            if (res && res.status === "success" && res.pairing_code) {
              if (res.session_id) {
                currentModalSessionId = res.session_id;
                startModalStatusPolling(res.session_id);
              }
              const code = res.pairing_code.match(/.{1,4}/g)?.join(" - ") || res.pairing_code;
              $("#waModalPairingCodeValue").text(code);
              $("#waModalPairingCodeResult").removeClass("d-none");
              self.showToast("Code generated! Enter this in WhatsApp Linked Devices.", "success");
            } else {
              self.showToast(res.msg || "Failed to generate pairing code.", "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Code</span>');
            const errData = xhr.responseJSON;
            if (errData && errData.limit_reached) {
              const limitMsg = errData.msg || "Device limit reached. Please contact Aleya to connect additional WhatsApp devices.";
              if (typeof window.showContactAleyaModal === "function") {
                window.showContactAleyaModal("WhatsApp Device Limit Reached", limitMsg);
              } else {
                const swalFn = window.swal || window.Swal;
                if (typeof swalFn === "function") {
                  swalFn({
                    title: "Device Limit Reached",
                    text: limitMsg,
                    type: "warning",
                    icon: "warning",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#2563eb"
                  });
                } else {
                  self.showToast(limitMsg, "error");
                }
              }
            } else {
              const err = errData ? errData.msg : "Failed to generate pairing code.";
              self.showToast(err, "error");
            }
          }
        });
      });

      // 7. Agent Request Pairing Code (Phone Linking)
      $("#btnAgentRequestPairingCode").on("click", function () {
        const phone = $("#waAgentPairingPhoneInput").val().trim();
        if (!phone) {
          self.showToast("Please enter your phone number with country code.", "warning");
          $("#waAgentPairingPhoneInput").focus();
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Requesting...');

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=pairing_code",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ phone: phone }),
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Pairing Code</span>');
            if (res && res.status === "success" && res.pairing_code) {
              if (res.session_id) {
                currentAgentSessionId = res.session_id;
                startAgentStatusPolling(res.session_id);
              }
              const code = res.pairing_code.match(/.{1,4}/g)?.join(" - ") || res.pairing_code;
              $("#waAgentPairingCodeValue").text(code);
              $("#waAgentPairingCodeResult").removeClass("d-none");
              self.showToast("Code generated! Enter this code on your WhatsApp app.", "success");
            } else {
              self.showToast(res.msg || "Failed to generate pairing code.", "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Pairing Code</span>');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to generate pairing code.";
            self.showToast(err, "error");
          }
        });
      });

      $("#btnAgentRefreshQr").on("click", function () {
        loadAgentQrCode(true);
      });

      // 8. Sync Chats
      $(document).on("click", ".btn-device-sync, #btnAgentSyncChats", function () {
        const $btn = $(this);
        const sessId = $btn.data("session-id") || "";
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Syncing...');

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=sync_chats" + (sessId ? "&session_id=" + encodeURIComponent(sessId) : ""),
          method: "GET",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-refresh"></i> Sync');
            if (res && res.status === "success") {
              self.showToast(res.msg || "Chats synchronized successfully!", "success");
              loadHubState();
            } else {
              self.showToast(res.msg || "Failed to sync chats.", "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-refresh"></i> Sync');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to sync chats.";
            self.showToast(err, "error");
          }
        });
      });

      // Helper: perform device disconnect
      function performDisconnect(sessId, devId, $btn) {
        $btn.prop("disabled", true).addClass("loading");

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=disconnect&session_id=" + encodeURIComponent(sessId) + "&device_id=" + encodeURIComponent(devId),
          method: "GET",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("loading");
            self.showToast(res.msg || "Device disconnected.", "info");
            window.dispatchEvent(new CustomEvent("v3:whatsapp-device-changed"));
            loadHubState();
          },
          error: function (xhr) {
            $btn.prop("disabled", false).removeClass("loading");
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to disconnect device.";
            self.showToast(err, "error");
          }
        });
      }

      // 9. Disconnect Device (SweetAlert confirmation)
      $(document).off("click", ".btn-device-disconnect").on("click", ".btn-device-disconnect", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $btn = $(this);
        const sessId = $btn.attr("data-session-id") || $btn.data("session-id") || "default";
        const devId = $btn.attr("data-device-id") || $btn.data("device-id") || "";

        const swalInstance = (typeof Swal !== "undefined" && typeof Swal.fire === "function")
          ? Swal
          : ((typeof window.Swal !== "undefined" && typeof window.Swal.fire === "function") ? window.Swal : null);

        if (swalInstance) {
          swalInstance.fire({
            title: "Disconnect WhatsApp Device?",
            text: "Are you sure you want to disconnect this device? You will need to scan the QR code again to link it.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Disconnect",
            cancelButtonText: "Cancel",
            reverseButtons: true
          }).then(function (result) {
            if (result.isConfirmed || result.value) {
              performDisconnect(sessId, devId, $btn);
            }
          });
        } else if (typeof swal === "function") {
          swal({
            title: "Disconnect WhatsApp Device?",
            text: "Are you sure you want to disconnect this device? You will need to scan the QR code again to link it.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Yes, Disconnect",
            cancelButtonText: "Cancel",
            closeOnConfirm: true
          }, function (isConfirm) {
            if (isConfirm) {
              performDisconnect(sessId, devId, $btn);
            }
          });
        } else if (confirm("Are you sure you want to disconnect this WhatsApp device?")) {
          performDisconnect(sessId, devId, $btn);
        }
      });

      // 9b. Set Device as Default Communication Line for Alerts & Notifications
      $(document).off("click", ".btn-set-default").on("click", ".btn-set-default", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $btn = $(this);
        const devId = $btn.attr("data-device-id") || $btn.data("device-id") || "";
        if (!devId) return;

        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> <span>Setting...</span>');

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=set_default_device",
          type: "POST",
          dataType: "json",
          contentType: "application/json",
          data: JSON.stringify({ device_id: Number(devId) }),
          success: function (res) {
            if (res && res.status === "success") {
              self.showToast(res.msg || "Device marked as default for customer alerts and notifications.", "success");
              loadHubState();
            } else {
              $btn.prop("disabled", false).html('<i class="fi fi-rr-star"></i> <span>Set as Default</span>');
              self.showToast((res && res.msg) ? res.msg : "Failed to set default device.", "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-star"></i> <span>Set as Default</span>');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Error setting default device.";
            self.showToast(err, "error");
          }
        });
      });

      // 10. Pairing Mode Tabs
      $(document).on("click", ".wa-pairing-mode-btn", function () {
        const $btn = $(this);
        const $nav = $btn.closest(".wa-pairing-mode-nav");
        $nav.find(".wa-pairing-mode-btn").removeClass("active");
        $btn.addClass("active");
        const target = $btn.data("pane");
        const $container = $btn.closest(".wa-pairing-container, .wa-modal-body");
        $container.find(".wa-pairing-pane").addClass("d-none");
        $(target).removeClass("d-none");
      });

      // 11. Refresh Devices List
      $("#btnAdminRefreshDevices").on("click", function () {
        loadHubState();
        self.showToast("Refreshed WhatsApp devices list.", "info");
      });

      // 11b. Repair Signal Encryption Ratchets (Bad MAC Auto-Fix)
      $(document).on("click", "#btnRepairSessions, #btnAgentRepairSessions", function () {
        const $btn = $(this);
        const origHtml = $btn.html();
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Repairing...');

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=repair_sessions",
          method: "GET",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html(origHtml);
            if (res && res.status === "success") {
              self.showToast(res.msg || "Encryption ratchets repaired! WhatsApp keys refreshed.", "success");
              loadHubState();
            } else {
              const msg = (res && res.msg) ? res.msg : "Could not contact WhatsApp gateway to repair sessions.";
              self.showToast(msg, "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html(origHtml);
            const err = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : "Could not contact WhatsApp gateway to repair sessions.";
            self.showToast(err, "error");
          }
        });
      });

      // 12. Copy Webhook
      $("#btnCopyWebhook").on("click", function () {
        const val = $("#waWebhookUrlDisplay").val();
        if (navigator.clipboard) {
          navigator.clipboard.writeText(val);
          self.showToast("Webhook URL copied to clipboard!", "success");
        } else {
          $("#waWebhookUrlDisplay").select();
          document.execCommand("copy");
          self.showToast("Webhook URL copied to clipboard!", "success");
        }
      });

      function updateWaWebSectionsVisibility(isActive) {
        if (isActive) {
          $("#waWebSectionsWrapper").removeClass("d-none").slideDown(200);
          $("#waWebSideCol").removeClass("d-none").slideDown(200);
          $("#waWebDisabledState").slideUp(200, function () {
            $(this).addClass("d-none");
          });
        } else {
          $("#waWebSectionsWrapper").slideUp(200, function () {
            $(this).addClass("d-none");
          });
          $("#waWebSideCol").slideUp(200, function () {
            $(this).addClass("d-none");
          });
          $("#waWebDisabledState").removeClass("d-none").slideDown(200);
        }
      }

      // 13. Active toggle switch
      $("#waWebActiveToggle").on("change", function () {
        const isChecked = $(this).is(":checked");
        const $toggle = $(this);
        $toggle.prop("disabled", true);

        const hasConnectedDev = !!(stateDevices && stateDevices.some(function (d) {
          return (d.status === "connected" || d.device_status === "connected");
        }));
        updateWaWebSectionsVisibility(isChecked);
        self.updateAsidePluginState("whatsapp_web", isChecked, hasConnectedDev);

        self.togglePlugin("whatsapp_web", "enable_whatsapp_web_api", isChecked, function (success) {
          $toggle.prop("disabled", false);
          if (!success) {
            $toggle.prop("checked", !isChecked);
            updateWaWebSectionsVisibility(!isChecked);
            self.updateAsidePluginState("whatsapp_web", !isChecked, hasConnectedDev);
          }
        });
      });

      // 14. Settings form submit
      $("#waWebSettingsForm").on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveWaWebSettings");
        $btn.prop("disabled", true);

        const payload = {
          enable_whatsapp_web_api: $("#waWebActiveToggle").is(":checked") ? "1" : "0",
          whatsapp_web_sync_incoming: $("#waWebSyncIncoming").is(":checked") ? "1" : "0",
          whatsapp_web_auto_create_lead: $("#waWebAutoCreateLead").is(":checked") ? "1" : "0"
        };
        if ($("#waWebGatewayUrl").length) {
          payload.whatsapp_web_gateway_url = ($("#waWebGatewayUrl").val() || "").trim();
        }

        $.ajax({
          url: "/api/v3/integration/whatsapp_web/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify(payload),
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false);
            if (res && res.status === "success") {
              self.showToast(res.msg || "Settings saved successfully.", "success");
            } else {
              self.showToast("Failed to save settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false);
            self.showToast("Network error saving settings.", "error");
          }
        });
      });

      // Initial load of hub state and devices
      loadHubState();
    },

    // SMTP Email Server
    initSmtpPlugin: function () {
      const self = this;
      const $form = $("#pluginSmtpForm");
      // Initialize protocol based on current port value immediately
      const initialPort = ($("#smtpPort").val() || "587").trim();
      if (initialPort === "465") {
        $("#smtpSsl").val("ssl");
      } else if (initialPort === "25") {
        $("#smtpSsl").val("false");
      } else {
        $("#smtpSsl").val("tls");
      }

      $.ajax({
        url: "/api/v3/integration/smtp/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#smtpActiveToggle").prop("checked", d.enable_smtp_email === "1");
            $("#smtpHost").val(d.email_smtp_host || "");
            const portVal = (d.email_smtp_port || "587").toString().trim();
            $("#smtpPort").val(portVal);

            let sslVal = (d.email_smtp_ssl || "").toString().toLowerCase().trim();
            if (portVal === "465" || sslVal === "ssl") {
              sslVal = "ssl";
            } else if (portVal === "25" || sslVal === "false" || sslVal === "0") {
              sslVal = "false";
            } else {
              sslVal = "tls";
            }
            $("#smtpSsl").val(sslVal);

            $("#smtpUsername").val(d.email_smtp_username || "");
            $("#smtpPassword").val(d.email_smtp_password || "");
            $("#smtpEmail").val(d.email_smtp_email || "");
            $("#smtpName").val(d.email_smtp_name || "");
            $("#smtpReplyEmail").val(d.email_smtp_reply_email || "");
            $("#smtpReplyName").val(d.email_smtp_reply_name || "");
          }
        },
      });

      // Port input event -> auto select protocol
      $("#smtpPort").off("input change").on("input change", function () {
        const p = $(this).val().trim();
        if (p === "587" || p === "2525" || p === "5870") {
          $("#smtpSsl").val("tls");
        } else if (p === "465" || p === "993" || p === "995") {
          $("#smtpSsl").val("ssl");
        } else if (p === "25") {
          $("#smtpSsl").val("false");
        } else {
          $("#smtpSsl").val("tls");
        }
      });

      // Protocol select change -> auto set standard port
      $("#smtpSsl").off("change").on("change", function () {
        const proto = $(this).val();
        const curPort = $("#smtpPort").val().trim();
        if (proto === "tls" && (curPort === "465" || curPort === "25" || !curPort)) {
          $("#smtpPort").val("587");
        } else if (proto === "ssl" && (curPort === "587" || curPort === "25" || !curPort)) {
          $("#smtpPort").val("465");
        } else if (proto === "false" && (curPort === "587" || curPort === "465" || !curPort)) {
          $("#smtpPort").val("25");
        }
      });

      // Toggle SMTP Password Visibility
      $("#btnToggleSmtpPassword").off("click").on("click", function (e) {
        e.preventDefault();
        const $pwd = $("#smtpPassword");
        const $icon = $(this).find("i");
        if ($pwd.attr("type") === "password") {
          $pwd.attr("type", "text");
          $icon.removeClass("fi-rr-eye").addClass("fi-rr-eye-crossed");
          $(this).attr("title", "Hide password");
        } else {
          $pwd.attr("type", "password");
          $icon.removeClass("fi-rr-eye-crossed").addClass("fi-rr-eye");
          $(this).attr("title", "Show password");
        }
      });

      $form.off("submit").on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveSmtpPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_smtp_email: $("#smtpActiveToggle").is(":checked") ? "1" : "0",
          email_smtp_host: $("#smtpHost").val().trim(),
          email_smtp_port: $("#smtpPort").val().trim(),
          email_smtp_ssl: $("#smtpSsl").val(),
          email_smtp_username: $("#smtpUsername").val().trim(),
          email_smtp_password: $("#smtpPassword").val().trim(),
          email_smtp_email: $("#smtpEmail").val().trim(),
          email_smtp_name: $("#smtpName").val().trim(),
          email_smtp_reply_email: $("#smtpReplyEmail").val().trim(),
          email_smtp_reply_name: $("#smtpReplyName").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/smtp/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "SMTP configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save SMTP settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving SMTP settings.", "error");
          },
        });
      });

      // Test SMTP Email Dispatch
      $("#btnTestSmtpConnection").off("click").on("click", function () {
        const recipient = $("#smtpTestRecipient").val().trim();
        if (!recipient) {
          self.showToast("Please enter a test recipient email address.", "error");
          $("#smtpTestRecipient").focus();
          return;
        }

        const $testBtn = $(this);
        const originalBtnHtml = '<i class="fi fi-rr-paper-plane"></i> <span>Send Test Email</span>';
        $testBtn.prop("disabled", true).addClass("is-loading").html('<i class="fi fi-rr-spinner animate-spin"></i> <span>Testing Connection...</span>');
        $("#smtpTestRecipient").prop("disabled", true);
        $("#smtpTestBadge").removeClass("v3-badge-green v3-badge-red").addClass("v3-badge-amber").html('<i class="fi fi-rr-spinner animate-spin"></i> Testing...');
        $("#smtpTestFeedback").html('<div class="v3-alert-inline process"><i class="fi fi-rr-spinner animate-spin"></i> <span>Connecting to SMTP server &amp; dispatching test email...</span></div>');

        const testPayload = {
          action: "send_test_email",
          recipient_email: recipient,
          email_smtp_host: $("#smtpHost").val().trim(),
          email_smtp_port: $("#smtpPort").val().trim(),
          email_smtp_ssl: $("#smtpSsl").val(),
          email_smtp_username: $("#smtpUsername").val().trim(),
          email_smtp_password: $("#smtpPassword").val().trim(),
          email_smtp_email: $("#smtpEmail").val().trim(),
          email_smtp_name: $("#smtpName").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/smtp/",
          method: "POST",
          data: JSON.stringify(testPayload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $testBtn.prop("disabled", false).removeClass("is-loading");
            $("#smtpTestRecipient").prop("disabled", false);
            if (res && res.status === "success") {
              $testBtn.html('<i class="fi fi-rr-check"></i> <span>Test Sent!</span>');
              $("#smtpTestBadge").removeClass("v3-badge-amber v3-badge-red").addClass("v3-badge-green").html('<i class="fi fi-rr-check"></i> Success');
              $("#smtpTestFeedback").html('<div class="v3-alert-inline info"><i class="fi fi-rr-check"></i> <span>' + (res.msg || 'Test email dispatched successfully!') + '</span></div>');
              self.showToast(res.msg || "Test email dispatched successfully!", "success");
              setTimeout(function () {
                $testBtn.html(originalBtnHtml);
              }, 4000);
            } else {
              $testBtn.html('<i class="fi fi-rr-paper-plane"></i> <span>Retry Test Email</span>');
              $("#smtpTestBadge").removeClass("v3-badge-amber v3-badge-green").addClass("v3-badge-red").html('<i class="fi fi-rr-cross"></i> Failed');
              $("#smtpTestFeedback").html('<div class="v3-alert-inline error"><i class="fi fi-rr-cross"></i> <span>' + (res.msg || 'Failed to send test email.') + '</span></div>');
              self.showToast(res.msg || "Failed to send test email.", "error");
            }
          },
          error: function () {
            $testBtn.prop("disabled", false).removeClass("is-loading").html('<i class="fi fi-rr-paper-plane"></i> <span>Retry Test Email</span>');
            $("#smtpTestRecipient").prop("disabled", false);
            $("#smtpTestBadge").removeClass("v3-badge-amber v3-badge-green").addClass("v3-badge-red").html('<i class="fi fi-rr-cross"></i> Error');
            $("#smtpTestFeedback").html('<div class="v3-alert-inline error"><i class="fi fi-rr-cross"></i> <span>Network error while communicating with mail server.</span></div>');
            self.showToast("Network error during test dispatch.", "error");
          },
        });
      });
    },

    // Razorpay
    initRazorpayPlugin: function () {
      const self = this;
      const $form = $("#pluginRazorpayForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/razorpay/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#rzpActiveToggle").prop("checked", d.enable_razorpay === "1");
            if (d.razorpay_mode === "test") {
              $("#rzpModeTest").prop("checked", true);
            } else {
              $("#rzpModeLive").prop("checked", true);
            }
            $("#rzpKeyId").val(d.razorpay_key_id || "");
            $("#rzpKeySecret").val(d.razorpay_key_secret || "");
            $("#rzpWebhookSecret").val(d.razorpay_webhook_secret || "");
            $("#rzpAutoReceipt").prop("checked", d.razorpay_auto_receipt === "1");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveRazorpayPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_razorpay: $("#rzpActiveToggle").is(":checked") ? "1" : "0",
          razorpay_mode: $('input[name="razorpay_mode"]:checked').val() || "live",
          razorpay_key_id: $("#rzpKeyId").val().trim(),
          razorpay_key_secret: $("#rzpKeySecret").val().trim(),
          razorpay_webhook_secret: $("#rzpWebhookSecret").val().trim(),
          razorpay_auto_receipt: $("#rzpAutoReceipt").is(":checked") ? "1" : "0",
        };

        $.ajax({
          url: "/api/v3/integration/razorpay/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Razorpay credentials saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Razorpay settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Razorpay settings.", "error");
          },
        });
      });
    },

    // Instamojo
    initInstamojoPlugin: function () {
      const self = this;
      const $form = $("#pluginInstamojoForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/instamojo/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#imActiveToggle").prop("checked", d.enable_instamojo === "1");
            if (d.instamojo_mode === "test") {
              $("#imModeTest").prop("checked", true);
            } else {
              $("#imModeLive").prop("checked", true);
            }
            $("#imApiKey").val(d.instamojo_api_key || "");
            $("#imAuthToken").val(d.instamojo_auth_token || "");
            $("#imSaltKey").val(d.instamojo_salt_key || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveInstamojoPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_instamojo: $("#imActiveToggle").is(":checked") ? "1" : "0",
          instamojo_mode: $('input[name="instamojo_mode"]:checked').val() || "live",
          instamojo_api_key: $("#imApiKey").val().trim(),
          instamojo_auth_token: $("#imAuthToken").val().trim(),
          instamojo_salt_key: $("#imSaltKey").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/instamojo/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Instamojo configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Instamojo settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Instamojo settings.", "error");
          },
        });
      });
    },

    // SabPaisa
    initSabpaisaPlugin: function () {
      const self = this;
      const $form = $("#pluginSabpaisaForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/sabpaisa/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#spActiveToggle").prop("checked", d.enable_sabpaisa === "1");
            if (d.sabpaisa_mode === "test") {
              $("#spModeTest").prop("checked", true);
            } else {
              $("#spModeLive").prop("checked", true);
            }
            $("#spClientCode").val(d.sabpaisa_client_code || "");
            $("#spUsername").val(d.sabpaisa_username || "");
            $("#spPassword").val(d.sabpaisa_password || "");
            $("#spAuthKey").val(d.sabpaisa_auth_key || "");
            $("#spAuthIv").val(d.sabpaisa_auth_iv || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveSabpaisaPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_sabpaisa: $("#spActiveToggle").is(":checked") ? "1" : "0",
          sabpaisa_mode: $('input[name="sabpaisa_mode"]:checked').val() || "live",
          sabpaisa_client_code: $("#spClientCode").val().trim(),
          sabpaisa_username: $("#spUsername").val().trim(),
          sabpaisa_password: $("#spPassword").val().trim(),
          sabpaisa_auth_key: $("#spAuthKey").val().trim(),
          sabpaisa_auth_iv: $("#spAuthIv").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/sabpaisa/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "SabPaisa configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save SabPaisa settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving SabPaisa settings.", "error");
          },
        });
      });
    },

    // PhonePe
    initPhonepePlugin: function () {
      const self = this;
      const $form = $("#pluginPhonepeForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/phonepe/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#ppActiveToggle").prop("checked", d.enable_phonepe === "1");
            if (d.phonepe_mode === "test") {
              $("#ppModeTest").prop("checked", true);
            } else {
              $("#ppModeLive").prop("checked", true);
            }
            $("#ppMerchantId").val(d.phonepe_merchant_id || "");
            $("#ppSaltKey").val(d.phonepe_salt_key || "");
            $("#ppSaltIndex").val(d.phonepe_salt_index || "1");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSavePhonepePlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_phonepe: $("#ppActiveToggle").is(":checked") ? "1" : "0",
          phonepe_mode: $('input[name="phonepe_mode"]:checked').val() || "live",
          phonepe_merchant_id: $("#ppMerchantId").val().trim(),
          phonepe_salt_key: $("#ppSaltKey").val().trim(),
          phonepe_salt_index: $("#ppSaltIndex").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/phonepe/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "PhonePe configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save PhonePe settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving PhonePe settings.", "error");
          },
        });
      });
    },

    // Worldline
    initWorldlinePlugin: function () {
      const self = this;
      const $form = $("#pluginWorldlineForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/worldline/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#wlActiveToggle").prop("checked", d.enable_worldline === "1");
            if (d.worldline_mode === "test") {
              $("#wlModeTest").prop("checked", true);
            } else {
              $("#wlModeLive").prop("checked", true);
            }
            $("#wlMerchantCode").val(d.worldline_merchant_code || "");
            $("#wlSaltKey").val(d.worldline_salt_key || "");
            $("#wlScheme").val(d.worldline_scheme || "FIRST");
            $("#wlCurrency").val(d.worldline_currency || "INR");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveWorldlinePlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_worldline: $("#wlActiveToggle").is(":checked") ? "1" : "0",
          worldline_mode: $('input[name="worldline_mode"]:checked').val() || "live",
          worldline_merchant_code: $("#wlMerchantCode").val().trim(),
          worldline_salt_key: $("#wlSaltKey").val().trim(),
          worldline_scheme: $("#wlScheme").val().trim(),
          worldline_currency: $("#wlCurrency").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/worldline/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Worldline configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Worldline settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Worldline settings.", "error");
          },
        });
      });
    },

    // PayU
    initPayuPlugin: function () {
      const self = this;
      const $form = $("#pluginPayuForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/payu/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#payuActiveToggle").prop("checked", d.enable_payu === "1");
            if (d.payu_mode === "test") {
              $("#payuModeTest").prop("checked", true);
            } else {
              $("#payuModeLive").prop("checked", true);
            }
            $("#payuMerchantKey").val(d.payu_merchant_key || "");
            $("#payuMerchantSalt").val(d.payu_merchant_salt || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSavePayuPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_payu: $("#payuActiveToggle").is(":checked") ? "1" : "0",
          payu_mode: $('input[name="payu_mode"]:checked').val() || "live",
          payu_merchant_key: $("#payuMerchantKey").val().trim(),
          payu_merchant_salt: $("#payuMerchantSalt").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/payu/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "PayU configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save PayU settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving PayU settings.", "error");
          },
        });
      });
    },

    // Phi Commerce
    initPhiCommercePlugin: function () {
      const self = this;
      const $form = $("#pluginPhiCommerceForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/phi_commerce/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#phiActiveToggle").prop("checked", d.enable_phi_commerce === "1");
            if (d.phi_commerce_mode === "test") {
              $("#phiModeTest").prop("checked", true);
            } else {
              $("#phiModeLive").prop("checked", true);
            }
            $("#phiMerchantId").val(d.phi_commerce_merchant_id || "");
            $("#phiEncryptionKey").val(d.phi_commerce_encryption_key || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSavePhiCommercePlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_phi_commerce: $("#phiActiveToggle").is(":checked") ? "1" : "0",
          phi_commerce_mode: $('input[name="phi_commerce_mode"]:checked').val() || "live",
          phi_commerce_merchant_id: $("#phiMerchantId").val().trim(),
          phi_commerce_encryption_key: $("#phiEncryptionKey").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/phi_commerce/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Phi Commerce configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Phi Commerce settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Phi Commerce settings.", "error");
          },
        });
      });
    },

    // Cashfree
    initCashfreePlugin: function () {
      const self = this;
      const $form = $("#pluginCashfreeForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/cashfree/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#cfActiveToggle").prop("checked", d.enable_cashfree === "1");
            if (d.cashfree_mode === "test") {
              $("#cfModeTest").prop("checked", true);
            } else {
              $("#cfModeLive").prop("checked", true);
            }
            $("#cfAppId").val(d.cashfree_app_id || "");
            $("#cfSecretKey").val(d.cashfree_secret_key || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveCashfreePlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_cashfree: $("#cfActiveToggle").is(":checked") ? "1" : "0",
          cashfree_mode: $('input[name="cashfree_mode"]:checked').val() || "live",
          cashfree_app_id: $("#cfAppId").val().trim(),
          cashfree_secret_key: $("#cfSecretKey").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/cashfree/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Cashfree configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Cashfree settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Cashfree settings.", "error");
          },
        });
      });
    },

    // Google Ads
    initGoogleAdsPlugin: function () {
      const self = this;
      const $form = $("#pluginGoogleAdsForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/google_ads/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#gadActiveToggle").prop("checked", d.enable_google_ads === "1");
            $("#gadCustomerId").val(d.google_ads_customer_id || "");
            $("#gadConversionId").val(d.google_ads_conversion_id || "");
            $("#gadConversionLabel").val(d.google_ads_conversion_label || "");
            $("#gadWebhookKey").val(d.google_ads_lead_webhook_key || "gad_secret_key");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveGoogleAdsPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_google_ads: $("#gadActiveToggle").is(":checked") ? "1" : "0",
          google_ads_customer_id: $("#gadCustomerId").val().trim(),
          google_ads_conversion_id: $("#gadConversionId").val().trim(),
          google_ads_conversion_label: $("#gadConversionLabel").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/google_ads/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Google Ads settings saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Google Ads settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Google Ads settings.", "error");
          },
        });
      });
    },

    // UPI & Dynamic QR
    initUpiPlugin: function () {
      const self = this;
      const $form = $("#pluginUpiForm");
      if (!$form.length) return;

      const updateUpiPreview = function (vpa) {
        const v = vpa || "company@upi";
        $("#previewUpiVpaText").text(v);
        const encoded = encodeURIComponent("upi://pay?pa=" + v + "&pn=TravelBooking&cu=INR");
        $("#previewUpiQrImage").attr("src", "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encoded);
      };

      $("#upiDetails").on("input", function () {
        updateUpiPreview($(this).val().trim());
      });

      $.ajax({
        url: "/api/v3/integration/upi/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#upiActiveToggle").prop("checked", d.enable_upi === "1");
            $("#upiDetails").val(d.upi_details || d.pg_upi_address || "");
            $("#upiPayeeName").val(d.bank_ac_name || "");
            $("#upiMcc").val(d.upi_merchant_code || "4722");
            updateUpiPreview(d.upi_details || d.pg_upi_address || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveUpiPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const upiVal = $("#upiDetails").val().trim();
        const payload = {
          enable_upi: $("#upiActiveToggle").is(":checked") ? "1" : "0",
          upi_details: upiVal,
          pg_upi_address: upiVal,
          bank_ac_name: $("#upiPayeeName").val().trim(),
          upi_merchant_code: $("#upiMcc").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/upi/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "UPI QR configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save UPI settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving UPI settings.", "error");
          },
        });
      });
    },

    // Custom Webhooks
    initWebhooksPlugin: function () {
      const self = this;
      const $form = $("#pluginWebhooksForm");
      if (!$form.length) return;

      $.ajax({
        url: "/api/v3/integration/webhooks/",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data) {
            const d = res.data;
            $("#whActiveToggle").prop("checked", d.enable_webhooks === "1");
            $("#whLeadUrl").val(d.webhook_lead_created_url || "");
            $("#whPaymentUrl").val(d.webhook_payment_received_url || "");
            $("#whTourUrl").val(d.webhook_tour_booked_url || "");
            $("#whSecretKey").val(d.webhook_secret_key || "");
          }
        },
      });

      $form.on("submit", function (e) {
        e.preventDefault();
        const $btn = $("#btnSaveWebhooksPlugin");
        $btn.prop("disabled", true).addClass("is-loading");

        const payload = {
          enable_webhooks: $("#whActiveToggle").is(":checked") ? "1" : "0",
          webhook_lead_created_url: $("#whLeadUrl").val().trim(),
          webhook_payment_received_url: $("#whPaymentUrl").val().trim(),
          webhook_tour_booked_url: $("#whTourUrl").val().trim(),
        };

        $.ajax({
          url: "/api/v3/integration/webhooks/",
          method: "POST",
          data: JSON.stringify(payload),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading");
            if (res && res.status === "success") {
              self.showToast(res.msg || "Webhooks configuration saved.", "success");
            } else {
              self.showToast(res.msg || "Failed to save Webhook settings.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading");
            self.showToast("Network error saving Webhook settings.", "error");
          },
        });
      });
    },


    // -------------------------------------------------------------
    // Facebook Pages Overview Manager (Live Search & Fast Copy)
    // -------------------------------------------------------------
    initFacebookPagesOverview: function () {
      const $app = $("#fbIntegrationApp");
      if (!$app.length) return;

      const self = this;

      // Live search filter across connected Facebook pages
      $(document).off("input.fbPageSearch").on("input.fbPageSearch", "#fbPageSearchInput", function () {
        const query = ($(this).val() || "").toLowerCase().trim();
        const $cards = $app.find(".fb-page-card");
        let visibleCount = 0;

        $cards.each(function () {
          const $card = $(this);
          const name = ($card.attr("data-page-name") || "").toLowerCase();
          const pageId = ($card.attr("data-page-id") || "").toLowerCase();
          const category = ($card.attr("data-page-category") || "").toLowerCase();

          if (!query || name.indexOf(query) > -1 || pageId.indexOf(query) > -1 || category.indexOf(query) > -1) {
            $card.show();
            visibleCount++;
          } else {
            $card.hide();
          }
        });

        const $noResults = $("#fbPagesNoResults");
        if (visibleCount === 0 && query) {
          $noResults.show();
        } else {
          $noResults.hide();
        }
      });

      // One-click copy for Page ID chip
      $(document).off("click.fbCopyPageId").on("click.fbCopyPageId", ".btn-copy-id-chip, .fb-page-id-chip", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $chip = $(this).closest(".fb-page-id-chip");
        const $btn = $chip.find(".btn-copy-id-chip");
        const text = $btn.attr("data-copy") || $chip.find(".id-val").text().trim();
        if (!text) return;

        if (navigator.clipboard) {
          navigator.clipboard.writeText(text).then(function () {
            $btn.html('<i class="fi fi-rr-check"></i>');
            self.showToast("Copied Page ID: " + text, "success");
            setTimeout(function () {
              $btn.html('<i class="fi fi-rr-copy"></i>');
            }, 1800);
          });
        }
      });
    },


    // -------------------------------------------------------------
    // Facebook Page Forms Manager (Search & Real-Time Sync)
    // -------------------------------------------------------------
    initFacebookPageForms: function () {
      const $app = $("#fbFormsPageApp");
      if (!$app.length) return;

      const self = this;
      const pageId = $app.data("page-id") || "";

      // Real-time table search for forms
      $(document).off("input.fbFormSearch").on("input.fbFormSearch", "#fbFormSearchInput", function () {
        const query = ($(this).val() || "").toLowerCase().trim();
        const $rows = $("#fbFormsTableBody tr.fb-form-row");

        $rows.each(function () {
          const text = $(this).text().toLowerCase();
          if (!query || text.indexOf(query) > -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });

        const visibleCount = $("#fbFormsTableBody tr.fb-form-row:visible").length;
        $("#formsCountBadge").text(visibleCount + " Active");
      });

      // Sync lead forms from Facebook via AJAX
      $(document).off("click.fbSyncForms").on("click.fbSyncForms", "#btnSyncFbForms, #btnSyncFbFormsEmpty", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const targetPageId = $btn.data("page-id") || pageId;
        if (!targetPageId) return;

        const originalHtml = $btn.html();
        $btn.prop("disabled", true).addClass("is-loading").html('<i class="fi fi-rr-spinner spinner"></i> <span>Syncing Forms...</span>');

        $.ajax({
          url: "/api/v3/integration/facebook.php",
          method: "POST",
          data: JSON.stringify({
            action: "sync_forms",
            page_id: targetPageId
          }),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).removeClass("is-loading").html(originalHtml);
            if (res && res.status === "success") {
              if (res.last_sync_time) {
                $("#fbPageLastSyncBadge").html('<i class="fi fi-rr-refresh"></i> Last Synced: Just now');
              }
              self.showToast(res.message || "Lead Forms synchronized from Facebook.", "success");
              setTimeout(function () {
                window.location.reload();
              }, 600);
            } else {
              self.showToast((res && res.msg) || "Failed to synchronize forms from Facebook.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).removeClass("is-loading").html(originalHtml);
            self.showToast("Network error synchronizing Facebook forms.", "error");
          }
        });
      });
    },

    // -------------------------------------------------------------
    // Facebook Form Leads Viewer (Pagination, 20 Leads Fetch, CRM Sync)
    // -------------------------------------------------------------
    initFacebookFormLeads: function () {
      const $wrap = $("#fbFormLeadsApp");
      if (!$wrap.length) return;

      const self = this;
      let isLoading = false;

      function formatNumber(num) {
        return (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }

      function escapeHtml(str) {
        if (!str) return "";
        return String(str)
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
      }

      function fetchPageLeads(targetPage, cursorAfter, cursorBefore) {
        if (isLoading) return;
        isLoading = true;

        const formId = $wrap.attr("data-form-id") || "";
        const limit = 20;

        const $tbody = $("#fbLeadsTableBody");
        $tbody.html(
          '<tr><td colspan="7" class="text-center py-4">' +
            '<div class="v3-table-loader-wrap">' +
              '<i class="fi fi-rr-spinner spinner v3-loader-spinner-icon"></i>' +
              '<span class="v3-table-loader-text">Fetching next set of leads from Facebook...</span>' +
            '</div>' +
          '</td></tr>'
        );

        $("#leadPrevPageBtn, #leadNextPageBtn").prop("disabled", true);

        const params = {
          action: "get_leads",
          form_id: formId,
          page: targetPage,
          limit: limit
        };
        if (cursorAfter) params.after = cursorAfter;
        if (cursorBefore) params.before = cursorBefore;

        $.ajax({
          url: "/api/v3/integration/facebook.php",
          method: "GET",
          data: params,
          dataType: "json",
          success: function (res) {
            isLoading = false;
            if (res && res.status === "success" && Array.isArray(res.data)) {
              renderLeadRows(res.data);

              // Update pagination state
              const pag = res.pagination || {};
              $wrap.attr("data-current-page", pag.page || targetPage);
              $wrap.attr("data-after", pag.after || "");
              $wrap.attr("data-before", pag.before || "");

              $("#leadPaginationInfo").text(
                "Showing " + (pag.start || 0) + " to " + (pag.end || 0) + " of " + formatNumber(pag.total || 0) + " campaign leads"
              );
              $("#leadPageIndicator").text(
                "Page " + (pag.page || targetPage) + " of " + (pag.total_pages || 1)
              );

              $("#leadPrevPageBtn").prop("disabled", !pag.has_prev);
              $("#leadNextPageBtn").prop("disabled", !pag.has_next);

              if (pag.total) {
                $("#headerCampaignLeadsBadge").html('<i class="fi fi-rr-users"></i> ' + formatNumber(pag.total) + ' Campaign Leads');
                $("#kpiTotalCampaignLeads").text(formatNumber(pag.total));
                $("#tableHeaderLeadCount").text(formatNumber(pag.total) + " Campaign Leads");
              }

              // Update URL state without reloading
              const newUrl = window.location.pathname + "?id=" + encodeURIComponent(formId) + "&page=" + (pag.page || targetPage) + (pag.after ? "&after=" + encodeURIComponent(pag.after) : "");
              if (window.history && window.history.pushState) {
                window.history.pushState(null, "", newUrl);
              }
            } else {
              $tbody.html(
                '<tr><td colspan="7" class="text-center py-4">' +
                  '<div style="padding:24px; color:#e11d48;">' +
                    '<i class="fi fi-rr-exclamation" style="font-size:24px;"></i>' +
                    '<p style="margin:8px 0 0;">' + ((res && res.msg) || "Failed to load leads from Facebook.") + '</p>' +
                  '</div>' +
                '</td></tr>'
              );
              $("#leadPrevPageBtn, #leadNextPageBtn").prop("disabled", false);
            }
          },
          error: function () {
            isLoading = false;
            $tbody.html(
              '<tr><td colspan="7" class="text-center py-4">' +
                '<div style="padding:24px; color:#e11d48;">' +
                  '<i class="fi fi-rr-exclamation" style="font-size:24px;"></i>' +
                  '<p style="margin:8px 0 0;">Network error communicating with leads API.</p>' +
                '</div>' +
              '</td></tr>'
            );
            $("#leadPrevPageBtn, #leadNextPageBtn").prop("disabled", false);
          }
        });
      }

      function renderLeadRows(leads) {
        const $tbody = $("#fbLeadsTableBody");
        $tbody.empty();

        if (!leads.length) {
          $tbody.html('<tr><td colspan="7" class="text-center py-4" style="color:#64748b; padding:30px;">No leads found on this page.</td></tr>');
          return;
        }

        leads.forEach(function (lead) {
          const leadPayload = {
            id: lead.id,
            created_time: lead.created_time,
            full_name: lead.full_name,
            phone: lead.phone,
            email: lead.email,
            questions: lead.questions,
            is_synced: lead.is_synced,
            crm_enquiry_id: lead.crm_enquiry_id
          };
          const jsonEncoded = encodeURIComponent(JSON.stringify(leadPayload));
          const timeParts = (lead.created_time || "N/A").split(",");
          const datePart = timeParts[0] || "N/A";
          const hourPart = timeParts[1] ? timeParts[1].trim() : "";

          let contactHtml = "";
          if (lead.phone) {
            const cleanWa = String(lead.phone).replace(/[^0-9]/g, "");
            const waNum = (cleanWa.length === 10) ? ("91" + cleanWa) : cleanWa;
            const waUrl = "https://wa.me/" + waNum + "?text=" + encodeURIComponent("Hello " + (lead.full_name || "") + ", thank you for your enquiry on Facebook. How may we assist you?");
            contactHtml += 
              '<div class="fb-contact-phone-row">' +
                '<a href="tel:' + escapeHtml(lead.phone) + '" class="fb-contact-link phone"><i class="fi fi-rr-phone-call"></i> <span>' + escapeHtml(lead.phone) + '</span></a>' +
                '<a href="' + waUrl + '" target="_blank" rel="noopener noreferrer" class="fb-wa-action-btn" title="Chat on WhatsApp"><i class="fi fi-brands-whatsapp"></i></a>' +
              '</div>';
          }
          if (lead.email) {
            contactHtml += '<a href="mailto:' + escapeHtml(lead.email) + '" class="fb-contact-link email"><i class="fi fi-rr-envelope"></i> <span>' + escapeHtml(lead.email) + '</span></a>';
          }
          if (!lead.phone && !lead.email) {
            contactHtml = '<span class="fb-contact-empty">—</span>';
          }

          let respHtml = "";
          if (lead.questions && lead.questions.length > 0) {
            const firstTwo = lead.questions.slice(0, 2);
            firstTwo.forEach(function (q) {
              respHtml += '<span class="v3-response-chip" title="' + escapeHtml(q.label + ': ' + q.val) + '"><span class="chip-label">' + escapeHtml(q.label) + ':</span> <span class="chip-val">' + escapeHtml(q.val) + '</span></span> ';
            });
            if (lead.questions.length > 2) {
              respHtml += '<button type="button" class="v3-chip-more btn-view-lead-modal" data-lead="' + jsonEncoded + '" title="Click to view all responses">+' + (lead.questions.length - 2) + ' more</button>';
            }
          } else {
            respHtml = '<span class="v3-response-chip is-default">Default Ad Capture</span>';
          }

          // Dynamic Avatar Color Variant
          const avatarVariants = ['avatar-blue', 'avatar-indigo', 'avatar-purple', 'avatar-emerald', 'avatar-amber'];
          let charSum = 0;
          for (let c = 0; c < (lead.full_name || "").length; c++) {
            charSum += lead.full_name.charCodeAt(c);
          }
          const avatarClass = avatarVariants[charSum % 5];

          // Lead ID with One-Click Copy
          const leadIdHtml =
            '<div class="v3-lead-id-wrap">' +
              '<span class="v3-code-pill" title="Lead ID: ' + escapeHtml(lead.id) + '">' + escapeHtml(lead.id) + '</span>' +
              '<button type="button" class="v3-copy-chip-btn btn-copy-lead-id" data-copy="' + escapeHtml(lead.id) + '" title="Copy Lead ID">' +
                '<i class="fi fi-rr-copy"></i>' +
              '</button>' +
            '</div>';

          // CRM Sync Column HTML
          let crmSyncHtml = "";
          if (lead.is_synced) {
            crmSyncHtml =
              '<div class="v3-crm-sync-status-group">' +
                '<span class="v3-sync-badge is-synced" title="Lead successfully ingested into CRM">' +
                  '<span class="status-pulse-dot"></span>' +
                  '<span>Synced to CRM</span>' +
                '</span>' +
                (lead.crm_enquiry_id
                  ? '<a href="/dashboard/leads/view/?id=' + lead.crm_enquiry_id + '" class="v3-crm-link-chip" title="Open Enquiry #' + lead.crm_enquiry_id + ' in CRM">' +
                      '<i class="fi fi-rr-arrow-up-right-from-square"></i>' +
                      '<span>Enquiry #' + lead.crm_enquiry_id + '</span>' +
                    '</a>'
                  : '') +
              '</div>';
          } else {
            crmSyncHtml =
              '<div class="v3-crm-sync-status-group">' +
                '<span class="v3-sync-badge is-pending" title="Lead pending CRM ingestion">' +
                  '<span class="status-pulse-dot dot-amber"></span>' +
                  '<span>Not in CRM</span>' +
                '</span>' +
                '<button type="button" class="btn-v3-sync-now btn-sync-lead-crm" data-lead-id="' + escapeHtml(lead.id) + '" data-form-id="' + escapeHtml($wrap.attr("data-form-id") || "") + '" title="Push lead into CRM immediately">' +
                  '<i class="fi fi-rr-cloud-upload"></i>' +
                  '<span>Sync to CRM</span>' +
                '</button>' +
              '</div>';
          }

          const destBadge = lead.destination
            ? '<span class="v3-lead-dest-chip" title="Destination: ' + escapeHtml(lead.destination) + '"><i class="fi fi-rr-map-marker"></i> ' + escapeHtml(lead.destination) + '</span>'
            : '<span class="v3-lead-source-chip"><i class="fi fi-brands-facebook"></i> Meta Lead</span>';

          const rowHtml =
            '<tr class="fb-lead-row" data-lead-id="' + escapeHtml(lead.id) + '">' +
              '<td>' +
                '<div class="fb-lead-date-box">' +
                  '<span class="lead-date">' + escapeHtml(datePart) + '</span>' +
                  '<span class="lead-time"><i class="fi fi-rr-clock-three"></i> ' + escapeHtml(hourPart) + '</span>' +
                '</div>' +
              '</td>' +
              '<td>' +
                '<div class="v3-table-form-cell">' +
                  '<div class="fb-lead-avatar ' + avatarClass + '">' + escapeHtml(lead.initial || "F") + '</div>' +
                  '<div class="v3-table-form-info">' +
                    '<h4 class="lead-customer-name" title="' + escapeHtml(lead.full_name) + '">' + escapeHtml(lead.full_name) + '</h4>' +
                    '<div class="lead-sub-chips">' + destBadge + '</div>' +
                  '</div>' +
                '</div>' +
              '</td>' +
              '<td><div class="fb-contact-cell">' + contactHtml + '</div></td>' +
              '<td><div class="v3-response-pills-wrap">' + respHtml + '</div></td>' +
              '<td>' + leadIdHtml + '</td>' +
              '<td class="crm-sync-status-cell">' + crmSyncHtml + '</td>' +
              '<td class="text-end">' +
                '<button type="button" class="v3-btn-table-action btn-view-lead-modal" data-lead="' + jsonEncoded + '" title="Open Full Lead Profile">' +
                  '<i class="fi fi-rr-eye"></i> <span>View Details</span>' +
                '</button>' +
              '</td>' +
            '</tr>';

          $tbody.append(rowHtml);
        });
      }

      // Next Page Click Handler
      $(document).off("click.fbLeadNext").on("click.fbLeadNext", "#leadNextPageBtn", function (e) {
        e.preventDefault();
        const curPage = parseInt($wrap.attr("data-current-page") || "1", 10);
        const nextCursor = $wrap.attr("data-after") || "";
        fetchPageLeads(curPage + 1, nextCursor, null);
      });

      // Previous Page Click Handler
      $(document).off("click.fbLeadPrev").on("click.fbLeadPrev", "#leadPrevPageBtn", function (e) {
        e.preventDefault();
        const curPage = parseInt($wrap.attr("data-current-page") || "1", 10);
        const prevCursor = $wrap.attr("data-before") || "";
        if (curPage > 1) {
          fetchPageLeads(curPage - 1, null, prevCursor);
        }
      });

      // Refresh / Retry Leads Button Handlers
      $(document).off("click.fbRefreshLeads").on("click.fbRefreshLeads", "#btnRefreshFbLeads, #btnRetryFetchLeads, #btnEmptyRefreshLeads", function (e) {
        e.preventDefault();
        fetchPageLeads(1, null, null);
      });

      // Client-side quick filter on current 20 leads
      $(document).off("input.fbLeadSearch").on("input.fbLeadSearch", "#leadSearchInput", function () {
        const query = ($(this).val() || "").toLowerCase().trim();
        const $rows = $("#fbLeadsTableBody tr.fb-lead-row");

        $rows.each(function () {
          const text = $(this).text().toLowerCase();
          if (!query || text.indexOf(query) > -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Sync Single Lead to CRM
      $(document).off("click.fbSyncLead").on("click.fbSyncLead", ".btn-sync-lead-crm", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const leadId = $btn.data("lead-id");
        const formId = $btn.data("form-id") || $wrap.attr("data-form-id") || "";
        if (!leadId || !formId) return;

        const $group = $btn.closest(".v3-crm-sync-status-group");
        $btn.prop("disabled", true).addClass("is-loading");
        $group.html('<span class="v3-sync-badge is-pending"><i class="fi fi-rr-spinner spinner"></i> <span>Syncing...</span></span>');

        $.ajax({
          url: "/api/v3/integration/facebook.php",
          method: "POST",
          data: JSON.stringify({
            action: "sync_single_lead",
            form_id: formId,
            lead_id: leadId
          }),
          contentType: "application/json",
          dataType: "json",
          success: function (res) {
            if (res && res.status === "success" && res.enquiry_id) {
              const $cell = $group.closest(".crm-sync-status-cell");
              $cell.html(
                '<div class="v3-crm-sync-status-group">' +
                  '<span class="v3-sync-badge is-synced" title="Lead successfully ingested into CRM">' +
                    '<span class="status-pulse-dot"></span>' +
                    '<span>Synced to CRM</span>' +
                  '</span>' +
                  '<a href="/dashboard/leads/view/?id=' + res.enquiry_id + '" class="v3-crm-link-chip" title="Open Enquiry #' + res.enquiry_id + ' in CRM">' +
                    '<i class="fi fi-rr-arrow-up-right-from-square"></i>' +
                    '<span>Enquiry #' + res.enquiry_id + '</span>' +
                  '</a>' +
                '</div>'
              );
              self.showToast(res.message || "Lead #" + res.enquiry_id + " synced to CRM.", "success");
            } else {
              $group.html(
                '<span class="v3-sync-badge is-pending" title="Lead pending CRM ingestion"><span class="status-pulse-dot dot-amber"></span><span>Not in CRM</span></span>' +
                '<button type="button" class="btn-v3-sync-now btn-sync-lead-crm" data-lead-id="' + escapeHtml(leadId) + '" data-form-id="' + escapeHtml(formId) + '" title="Push lead into CRM immediately">' +
                  '<i class="fi fi-rr-cloud-upload"></i> <span>Push to CRM</span>' +
                '</button>'
              );
              self.showToast((res && res.msg) || "Failed to sync lead into CRM.", "error");
            }
          },
          error: function () {
            $group.html(
              '<span class="v3-sync-badge is-pending" title="Lead pending CRM ingestion"><span class="status-pulse-dot dot-amber"></span><span>Not in CRM</span></span>' +
              '<button type="button" class="btn-v3-sync-now btn-sync-lead-crm" data-lead-id="' + escapeHtml(leadId) + '" data-form-id="' + escapeHtml(formId) + '" title="Push lead into CRM immediately">' +
                '<i class="fi fi-rr-cloud-upload"></i> <span>Push to CRM</span>' +
              '</button>'
            );
            self.showToast("Network error syncing lead to CRM.", "error");
          }
        });
      });

      // Copy Lead ID chip handler
      $(document).off("click.fbCopyLeadId").on("click.fbCopyLeadId", ".btn-copy-lead-id", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const text = $btn.attr("data-copy") || "";
        if (!text) return;
        if (navigator.clipboard) {
          navigator.clipboard.writeText(text).then(function () {
            $btn.addClass("copied").html('<i class="fi fi-rr-check"></i>');
            self.showToast("Copied ID: " + text, "success");
            setTimeout(function () {
              $btn.removeClass("copied").html('<i class="fi fi-rr-copy"></i>');
            }, 1800);
          });
        }
      });

      // View Details Modal Handler
      $(document).off("click.fbLeadModal").on("click.fbLeadModal", ".btn-view-lead-modal", function () {
        const leadJsonStr = $(this).attr("data-lead");
        if (!leadJsonStr) return;

        try {
          const lead = JSON.parse(decodeURIComponent(leadJsonStr));
          $("#fbModalLeadName").text(lead.full_name || "Facebook Lead");
          $("#fbModalLeadId").text(lead.id || "N/A");
          $("#fbModalCreatedTime").text(lead.created_time || "N/A");

          const $syncBadge = $("#fbModalSyncStatus");
          if (lead.is_synced) {
            $syncBadge.html(
              '<div class="v3-crm-sync-pill is-synced">' +
                '<span class="v3-sync-status-tag"><span class="status-pulse-dot"></span><span>Synced</span></span>' +
                (lead.crm_enquiry_id
                  ? '<a href="/dashboard/leads/view/?id=' + lead.crm_enquiry_id + '" class="v3-sync-enquiry-link" title="Open in CRM"><span class="enquiry-tag">#' + lead.crm_enquiry_id + '</span> <i class="fi fi-rr-arrow-up-right-from-square"></i></a>'
                  : '') +
              '</div>'
            );
            $("#fbModalCrmLeadBtn").attr("href", "/dashboard/leads/view/?id=" + lead.crm_enquiry_id);
          } else {
            $syncBadge.html(
              '<div class="v3-crm-sync-pill is-pending">' +
                '<span class="v3-sync-status-tag"><span class="status-pulse-dot"></span><span>Not Synced</span></span>' +
              '</div>'
            );
            $("#fbModalCrmLeadBtn").attr("href", "/dashboard/leads/list/");
          }

          const $contactCard = $("#fbModalContactInfo");
          $contactCard.empty();

          if (lead.phone) {
            $contactCard.append(
              '<div class="fb-modal-contact-row">' +
                '<i class="fi fi-rr-phone-call fb-modal-contact-icon"></i>' +
                '<span class="fb-modal-contact-text">Phone: <a href="tel:' + escapeHtml(lead.phone) + '">' + escapeHtml(lead.phone) + '</a></span>' +
                '<button type="button" class="btn-copy-mini fb-modal-btn-copy" data-copy="' + escapeHtml(lead.phone) + '" title="Copy Phone"><i class="fi fi-rr-copy"></i></button>' +
              '</div>'
            );
          }
          if (lead.email) {
            $contactCard.append(
              '<div class="fb-modal-contact-row">' +
                '<i class="fi fi-rr-envelope fb-modal-contact-icon"></i>' +
                '<span class="fb-modal-contact-text">Email: <a href="mailto:' + escapeHtml(lead.email) + '">' + escapeHtml(lead.email) + '</a></span>' +
                '<button type="button" class="btn-copy-mini fb-modal-btn-copy" data-copy="' + escapeHtml(lead.email) + '" title="Copy Email"><i class="fi fi-rr-copy"></i></button>' +
              '</div>'
            );
          }
          if (!lead.phone && !lead.email) {
            $contactCard.append(
              '<div class="fb-modal-contact-empty">Contact details are not available or restricted for this role.</div>'
            );
          }

          const $qaList = $("#fbModalQAList");
          $qaList.empty();

          if (lead.questions && lead.questions.length > 0) {
            lead.questions.forEach(function (q) {
              $qaList.append(
                '<div class="fb-modal-qa-card">' +
                  '<div class="qa-q">' + escapeHtml(q.label || "Question") + '</div>' +
                  '<div class="qa-a">' + escapeHtml(q.val || "—") + '</div>' +
                '</div>'
              );
            });
          } else {
            $qaList.append(
              '<div class="fb-modal-qa-empty">No additional question responses in this submission.</div>'
            );
          }

          $("#fbLeadDetailsModalOverlay").addClass("is-active active").show();
        } catch (e) {
          console.error("Error parsing lead data", e);
        }
      });

      // Dismiss Modal
      $(document).off("click.fbModalDismiss").on("click.fbModalDismiss", "#fbModalCloseBtn, .btn-modal-dismiss, #fbLeadDetailsModalOverlay", function (e) {
        if (e.target === this || $(this).is("#fbModalCloseBtn") || $(this).hasClass("btn-modal-dismiss")) {
          $("#fbLeadDetailsModalOverlay").removeClass("is-active active").hide();
        }
      });

      // Copy mini button inside modal
      $(document).off("click.fbCopyMini").on("click.fbCopyMini", ".btn-copy-mini", function () {
        const text = $(this).attr("data-copy");
        if (text && navigator.clipboard) {
          navigator.clipboard.writeText(text).then(function () {
            self.showToast("Copied to clipboard!", "success");
          });
        }
      });
    },

    showToast: function (msg, type) {
      var iconType = (type === "danger" || type === "failed") ? "error" : (type || "success");
      if (typeof popUpMessage === "function") {
        popUpMessage("center", msg, iconType);
      } else if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: (iconType === "error" || iconType === "warning" || iconType === "info") ? iconType : "success",
          title: msg,
          showConfirmButton: false,
          timer: 3000,
        });
      } else {
        alert(msg);
      }
    },
  };

  $(document).ready(function () {
    window.V3Integration.init();
  });

  $(document).on("spa:loaded", function () {
    if (window.V3Integration && typeof window.V3Integration.init === "function") {
      window.V3Integration.init();
    }
  });
});
