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
      const $actions = $card.find(".integration-card-actions, .integration-card-footer");
      if (isActive) {
        $actions.stop(true, true).slideDown(180);
        $card.removeClass("is-disabled");
      } else {
        $actions.stop(true, true).slideUp(180);
        $card.addClass("is-disabled");
      }
    },

    updateAsidePluginState: function (slug, isActive) {
      const $asideItems = $('[data-aside-plugin="' + slug + '"]');
      if (isActive) {
        $asideItems.show();
      } else {
        $asideItems.hide();
      }
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
        self.updateAsidePluginState(slug, isChecked);

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
        const title = $card.find(".integration-meta h3").text().toLowerCase();
        const desc = $card.find(".integration-desc").text().toLowerCase();
        const tag = $card.find(".integration-tag").text().toLowerCase();

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
      let countdown = 30;
      let statusPollInterval = null;
      let modalPollInterval = null;
      let currentModalSessionId = null;
      let stateDevices = [];
      let stateAgents = [];
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

              $("#waWebActiveToggle").prop("checked", !!res.is_active);
              self.updateAsidePluginState("whatsapp_web", !!res.is_active);

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
        $("#waAdminDeviceCount").text(devices.length);

        const $grid = $("#waAdminDevicesGrid");
        if (devices.length === 0) {
          $grid.html('<div class="wa-qr-placeholder"><i class="fi fi-rr-smartphone"></i><span>No WhatsApp devices linked yet. Click "+ Add WhatsApp Device" above to connect your first number.</span></div>');
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

          html += '<div class="wa-device-card" data-device-id="' + dev.device_id + '" data-session-id="' + dev.session_id + '">' +
                    '<div class="wa-device-card-header">' +
                      '<div class="wa-device-phone-wrap">' +
                        '<div class="wa-device-icon"><i class="fi fi-rr-smartphone"></i></div>' +
                        '<div>' +
                          '<span class="wa-device-phone-number">' + (dev.phone || 'Device #' + dev.device_id) + '</span>' +
                          '<span class="wa-device-pushname">' + (dev.name || 'WhatsApp Web') + '</span>' +
                        '</div>' +
                      '</div>' +
                      '<span class="wa-device-badge ' + statusClass + '">' +
                        '<span class="wa-device-badge-dot"></span> ' + statusLabel +
                      '</span>' +
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
        $("#waAgentQrCodeContainer").html('<div class="wa-qr-placeholder"><i class="fi fi-rr-spinner wa-spin"></i><span>Generating your QR Code...</span></div>');
        const url = "/api/v3/integration/whatsapp_web/?action=generate_qr" + (force ? "&force=1" : "");
        $.ajax({
          url: url,
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.status === "success" && res.qr_svg) {
              $("#waAgentQrCodeContainer").html(res.qr_svg);
              resetAgentCountdown(res.expires_in || 30);
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
        countdown = duration || 30;
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

      function startAgentStatusPolling() {
        clearInterval(statusPollInterval);
        statusPollInterval = setInterval(function () {
          if ($("#waAgentAssignedSection").is(":visible")) {
            clearInterval(statusPollInterval);
            return;
          }
          $.ajax({
            url: "/api/v3/integration/whatsapp_web/?action=check_status",
            method: "GET",
            dataType: "json",
            success: function (res) {
              if (res && res.status === "success" && res.connection_status === "connected") {
                clearInterval(statusPollInterval);
                clearInterval(qrInterval);
                self.showToast("WhatsApp device linked successfully!", "success");
                loadHubState();
              }
            }
          });
        }, 3500);
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
              loadHubState();
            } else {
              self.showToast(res.msg || "Failed to assign device.", "danger");
              loadHubState();
            }
          },
          error: function (xhr) {
            $select.prop("disabled", false);
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to assign device.";
            self.showToast(err, "danger");
            loadHubState();
          }
        });
      });

      // 6. Admin Add Device Modal
      $("#btnOpenAddDeviceModal").on("click", function () {
        currentModalSessionId = "device_" + Date.now();
        $("#waAddDeviceModal").addClass("is-active");
        loadModalQrCode(currentModalSessionId);
        startModalStatusPolling(currentModalSessionId);
      });

      $("#btnCloseAddDeviceModal").on("click", function () {
        $("#waAddDeviceModal").removeClass("is-active");
        clearInterval(modalPollInterval);
      });

      // Close modal on backdrop click
      $("#waAddDeviceModal").on("click", function (e) {
        if ($(e.target).is("#waAddDeviceModal")) {
          $("#waAddDeviceModal").removeClass("is-active");
          clearInterval(modalPollInterval);
        }
      });

      function loadModalQrCode(sessId) {
        $("#waModalQrCodeContainer").html('<div class="wa-qr-placeholder"><i class="fi fi-rr-spinner wa-spin"></i><span>Generating QR code...</span></div>');
        $.ajax({
          url: "/api/v3/integration/whatsapp_web/?action=generate_qr&session_id=" + encodeURIComponent(sessId) + "&new_device=1",
          method: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.status === "success" && res.qr_svg) {
              $("#waModalQrCodeContainer").html(res.qr_svg);
              let modalCountdown = res.expires_in || 30;
              $("#waModalQrTimer").text(modalCountdown);
            } else if (res && res.status === "initializing") {
              setTimeout(function () {
                if ($("#waAddDeviceModal").hasClass("is-active")) {
                  loadModalQrCode(sessId);
                }
              }, 1500);
            } else if (res && res.msg) {
              $("#waModalQrCodeContainer").html('<div class="wa-qr-placeholder"><span>' + res.msg + '</span></div>');
            }
          }
        });
      }

      function startModalStatusPolling(sessId) {
        clearInterval(modalPollInterval);
        modalPollInterval = setInterval(function () {
          if (!$("#waAddDeviceModal").hasClass("is-active")) {
            clearInterval(modalPollInterval);
            return;
          }
          $.ajax({
            url: "/api/v3/integration/whatsapp_web/?action=check_status&session_id=" + encodeURIComponent(sessId),
            method: "GET",
            dataType: "json",
            success: function (res) {
              if (res && res.status === "success" && res.connection_status === "connected") {
                clearInterval(modalPollInterval);
                $("#waAddDeviceModal").removeClass("is-active");
                self.showToast("New WhatsApp device connected successfully!", "success");
                loadHubState();
                setTimeout(function () {
                  loadHubState();
                }, 1000);
              }
            }
          });
        }, 3000);
      }

      $("#btnModalRefreshQr").on("click", function () {
        if (currentModalSessionId) loadModalQrCode(currentModalSessionId);
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
              const code = res.pairing_code.match(/.{1,4}/g)?.join(" - ") || res.pairing_code;
              $("#waModalPairingCodeValue").text(code);
              $("#waModalPairingCodeResult").removeClass("d-none");
              self.showToast("Code generated! Enter this in WhatsApp Linked Devices.", "success");
            } else {
              self.showToast(res.msg || "Failed to generate pairing code.", "danger");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Code</span>');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to generate pairing code.";
            self.showToast(err, "danger");
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
              const code = res.pairing_code.match(/.{1,4}/g)?.join(" - ") || res.pairing_code;
              $("#waAgentPairingCodeValue").text(code);
              $("#waAgentPairingCodeResult").removeClass("d-none");
              self.showToast("Code generated! Enter this code on your WhatsApp app.", "success");
            } else {
              self.showToast(res.msg || "Failed to generate pairing code.", "danger");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-key"></i> <span>Get Pairing Code</span>');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to generate pairing code.";
            self.showToast(err, "danger");
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
              self.showToast(res.msg || "Failed to sync chats.", "danger");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-refresh"></i> Sync');
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to sync chats.";
            self.showToast(err, "danger");
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
            loadHubState();
          },
          error: function (xhr) {
            $btn.prop("disabled", false).removeClass("loading");
            const err = xhr.responseJSON ? xhr.responseJSON.msg : "Failed to disconnect device.";
            self.showToast(err, "danger");
          }
        });
      }

      // 9. Disconnect Device (SweetAlert confirmation)
      $(document).off("click", ".btn-device-disconnect").on("click", ".btn-device-disconnect", function (e) {
        e.preventDefault();
        const sessId = $(this).data("session-id");
        const devId = $(this).data("device-id") || "";
        const $btn = $(this);

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
        } else {
          performDisconnect(sessId, devId, $btn);
        }
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

      // 13. Active toggle switch
      $("#waWebActiveToggle").on("change", function () {
        const isChecked = $(this).is(":checked");
        self.togglePlugin("whatsapp_web", "enable_whatsapp_web_api", isChecked);
        self.updateAsidePluginState("whatsapp_web", isChecked);
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
    // Facebook Form Leads Viewer (Search, Pagination, Details Modal)
    // -------------------------------------------------------------
    initFacebookFormLeads: function () {
      const $wrap = $("#fbFormLeadsApp");
      if (!$wrap.length) return;

      const self = this;
      let currentPage = 1;
      const pageSize = 10;
      const $rows = $("#fbLeadsTableBody tr.fb-lead-row");

      function renderPagination() {
        const searchTerm = ($("#leadSearchInput").val() || "").toLowerCase().trim();
        let visibleRows = [];

        $rows.each(function () {
          const text = $(this).text().toLowerCase();
          if (!searchTerm || text.indexOf(searchTerm) > -1) {
            visibleRows.push($(this));
          } else {
            $(this).hide();
          }
        });

        const totalFiltered = visibleRows.length;
        const totalPages = Math.max(1, Math.ceil(totalFiltered / pageSize));

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = startIdx + pageSize;

        visibleRows.forEach(function ($row, idx) {
          if (idx >= startIdx && idx < endIdx) {
            $row.show();
          } else {
            $row.hide();
          }
        });

        // Update info text
        const displayStart = totalFiltered > 0 ? startIdx + 1 : 0;
        const displayEnd = Math.min(endIdx, totalFiltered);
        $("#leadPaginationInfo").text(
          "Showing " + displayStart + " to " + displayEnd + " of " + totalFiltered + " leads"
        );

        // Render page buttons
        const $controls = $("#leadPaginationControls");
        $controls.empty();

        if (totalPages > 1) {
          const $prevBtn = $(
            '<button type="button" class="v3-page-btn" ' +
              (currentPage === 1 ? "disabled" : "") +
              '><i class="fi fi-rr-angle-small-left"></i></button>'
          );
          $prevBtn.on("click", function () {
            if (currentPage > 1) {
              currentPage--;
              renderPagination();
            }
          });
          $controls.append($prevBtn);

          for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
              const $pBtn = $(
                '<button type="button" class="v3-page-btn ' +
                  (p === currentPage ? "active" : "") +
                  '">' +
                  p +
                  "</button>"
              );
              (function (pageNumber) {
                $pBtn.on("click", function () {
                  currentPage = pageNumber;
                  renderPagination();
                });
              })(p);
              $controls.append($pBtn);
            } else if (
              (p === currentPage - 2 && p > 1) ||
              (p === currentPage + 2 && p < totalPages)
            ) {
              $controls.append('<span style="color:#94a3b8; padding:0 4px;">...</span>');
            }
          }

          const $nextBtn = $(
            '<button type="button" class="v3-page-btn " ' +
              (currentPage === totalPages ? "disabled" : "") +
              '><i class="fi fi-rr-angle-small-right"></i></button>'
          );
          $nextBtn.on("click", function () {
            if (currentPage < totalPages) {
              currentPage++;
              renderPagination();
            }
          });
          $controls.append($nextBtn);
        }
      }

      $("#leadSearchInput").on("input", function () {
        currentPage = 1;
        renderPagination();
      });

      renderPagination();

      // View Details Modal Handler
      $(document).off("click.fbLeadModal").on("click.fbLeadModal", ".btn-view-lead-modal", function () {
        const leadJsonStr = $(this).attr("data-lead");
        if (!leadJsonStr) return;

        try {
          const lead = JSON.parse(decodeURIComponent(leadJsonStr));
          $("#fbModalLeadName").text(lead.full_name || "Facebook Lead");
          $("#fbModalLeadId").text(lead.id || "N/A");
          $("#fbModalCreatedTime").text(lead.created_time || "N/A");

          const $contactCard = $("#fbModalContactInfo");
          $contactCard.empty();

          if (lead.phone) {
            $contactCard.append(
              '<div style="display:flex; align-items:center; gap:8px;">' +
                '<i class="fi fi-rr-phone-call" style="color:#0284c7;"></i>' +
                '<span>Phone: <a href="tel:' + lead.phone + '" style="font-weight:600; color:#0f172a; text-decoration:none;">' + lead.phone + '</a></span>' +
                '<button type="button" class="btn-copy-mini" data-copy="' + lead.phone + '" style="background:none; border:none; color:#64748b; cursor:pointer;" title="Copy Phone"><i class="fi fi-rr-copy"></i></button>' +
              '</div>'
            );
          }
          if (lead.email) {
            $contactCard.append(
              '<div style="display:flex; align-items:center; gap:8px; margin-top:6px;">' +
                '<i class="fi fi-rr-envelope" style="color:#0284c7;"></i>' +
                '<span>Email: <a href="mailto:' + lead.email + '" style="font-weight:600; color:#0f172a; text-decoration:none;">' + lead.email + '</a></span>' +
                '<button type="button" class="btn-copy-mini" data-copy="' + lead.email + '" style="background:none; border:none; color:#64748b; cursor:pointer;" title="Copy Email"><i class="fi fi-rr-copy"></i></button>' +
              '</div>'
            );
          }
          if (!lead.phone && !lead.email) {
            $contactCard.append(
              '<div style="color:#64748b; font-size:12.5px; font-style:italic;">Contact details are not available or restricted for this role.</div>'
            );
          }

          const $qaList = $("#fbModalQAList");
          $qaList.empty();

          if (lead.questions && lead.questions.length > 0) {
            lead.questions.forEach(function (q) {
              $qaList.append(
                '<div class="fb-modal-qa-card">' +
                  '<div class="qa-q">' + (q.label || "Question") + '</div>' +
                  '<div class="qa-a">' + (q.val || "—") + '</div>' +
                '</div>'
              );
            });
          } else {
            $qaList.append(
              '<div style="font-size:13px; color:#94a3b8; text-align:center; padding:12px;">No additional question responses in this submission.</div>'
            );
          }

          $("#fbLeadDetailsModalOverlay").addClass("is-active active").show();
        } catch (e) {
          console.error("Error parsing lead data", e);
        }
      });

      // Dismiss Modal
      $(document).on("click", "#fbModalCloseBtn, .btn-modal-dismiss, #fbLeadDetailsModalOverlay", function (e) {
        if (e.target === this || $(this).is("#fbModalCloseBtn") || $(this).hasClass("btn-modal-dismiss")) {
          $("#fbLeadDetailsModalOverlay").removeClass("is-active active").hide();
        }
      });

      // Copy mini button inside modal
      $(document).on("click", ".btn-copy-mini", function () {
        const text = $(this).attr("data-copy");
        if (text && navigator.clipboard) {
          navigator.clipboard.writeText(text).then(function () {
            self.showToast("Copied to clipboard!", "success");
          });
        }
      });
    },

    showToast: function (msg, type) {
      if (typeof popUpMessage === "function") {
        popUpMessage("center", msg, type || "success");
      } else if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: type === "error" ? "error" : "success",
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
