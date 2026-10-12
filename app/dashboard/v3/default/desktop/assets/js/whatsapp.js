/**
 * V3 WhatsApp CRM Live Chat & Messaging Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/whatsapp.js
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

  window.V3WhatsApp = {
    activeChatId: null,
    activePhone: null,
    attachedFile: null,
    currentFilter: "all",
    searchQuery: "",
    selectedDeviceId: "all",
    selectedUserId: 0,
    availableDevices: [],
    availableAgents: [],
    isAdmin: false,
    pollTimer: null,
    isDeviceLinked: false,
    eventsBound: false,
    isLoadingThreads: false,
    isLoadingMessages: false,

    init: function () {
      const $app = $("#whatsappCrmApp");
      if (!$app.length) {
        this.stopPolling();
        return;
      }

      this.stopPolling();
      this.lastThreadsHtml = null;
      this.bindEvents();
      this.checkConnectionStatus();
      this.loadThreads();
      this.startPolling();
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // Tab visibility listener: resume immediately when user returns to tab
      document.addEventListener("visibilitychange", function () {
        if (!document.hidden && $("#whatsappCrmApp").length) {
          if (!self.isLoadingThreads) {
            self.loadThreads();
          }
        }
      });

      // Filter tabs (delegated)
      $(document).on("click", ".wa-filter-pill", function () {
        $(".wa-filter-pill").removeClass("active");
        $(this).addClass("active");
        self.currentFilter = $(this).data("filter") || "all";
        self.loadThreads();
      });

      // Device Tab click (Sidebar line switcher)
      $(document).on("click", ".wa-device-tab", function () {
        const devId = $(this).data("device-id");
        self.selectedDeviceId = devId || "all";
        $(".wa-device-tab").removeClass("active");
        $(this).addClass("active");
        self.updateHeaderStatusBadge();
        self.lastThreadsHtml = null;
        self.loadThreads();
      });

      // Keyboard support for device tabs
      $(document).on("keydown", ".wa-device-tab", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          $(this).trigger("click");
        }
      });

      // Device tabs horizontal scroll arrows
      $(document).on("click", "#btnPrevDeviceTab", function (e) {
        e.preventDefault();
        const $bar = $("#waDeviceTabsBar");
        if ($bar.length) $bar.animate({ scrollLeft: "-=220" }, 200);
      });

      $(document).on("click", "#btnNextDeviceTab", function (e) {
        e.preventDefault();
        const $bar = $("#waDeviceTabsBar");
        if ($bar.length) $bar.animate({ scrollLeft: "+=220" }, 200);
      });

      // Search input (delegated)
      let searchTimeout = null;
      $(document).on("input", "#waThreadSearch", function () {
        const val = $(this).val().trim();
        self.searchQuery = val;
        if (val.length > 0) {
          $("#btnClearSearch").show();
        } else {
          $("#btnClearSearch").hide();
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
          self.loadThreads();
        }, 300);
      });

      $(document).on("click", "#btnClearSearch", function () {
        $("#waThreadSearch").val("").trigger("input").focus();
      });

      // Select thread (delegated)
      $(document).on("click", ".wa-thread-item", function () {
        const chatId = $(this).data("chat-id");
        const phone = $(this).data("phone");
        self.selectChat(chatId, phone);
      });

      // Mobile back button to thread list
      $(document).on("click", "#btnBackToThreads", function () {
        $("#waChatWorkspace").removeClass("mobile-chat-open");
        $("#waActiveChatWrapper").removeClass("is-open");
        $("#waEmptyState").show();
        $(".wa-thread-item").removeClass("active");
        self.activeChatId = null;
        self.activePhone = null;
      });

      // Copy Active Phone Number
      $(document).on("click", "#btnCopyActivePhone", function () {
        const phone = self.activePhone || $("#waActivePhone").text().trim();
        if (!phone) return;
        const $btn = $(this);
        const copySuccess = function () {
          $btn.html('<i class="fi fi-rr-check" style="color:#10B981;"></i>');
          setTimeout(function () {
            $btn.html('<i class="fi fi-rr-copy"></i>');
          }, 1800);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(phone).then(copySuccess).catch(function () {
            self.fallbackCopy(phone, copySuccess);
          });
        } else {
          self.fallbackCopy(phone, copySuccess);
        }
      });

      // Auto-expanding composer textarea
      $(document).on("input", "#waComposerText", function () {
        this.style.height = "auto";
        this.style.height = Math.min(this.scrollHeight, 120) + "px";
      });

      // Scroll to bottom button
      $(document).on("click", "#btnScrollToBottom", function () {
        const $stream = $("#waMessagesStream");
        if ($stream.length) {
          $stream.stop().animate({ scrollTop: $stream[0].scrollHeight }, 200);
          $("#btnScrollToBottom").addClass("d-none");
          $("#waScrollUnreadDot").addClass("d-none");
        }
      });

      // Stream scroll listener to toggle scroll-to-bottom button
      $(document).on("scroll", "#waMessagesStream", function () {
        const el = this;
        const isScrolledUp = (el.scrollHeight - el.scrollTop) > (el.clientHeight + 120);
        if (isScrolledUp) {
          $("#btnScrollToBottom").removeClass("d-none");
        } else {
          $("#btnScrollToBottom").addClass("d-none");
          $("#waScrollUnreadDot").addClass("d-none");
        }
      });

      // Composer submit (delegated)
      $(document).on("submit", "#waMessageSendForm", function (e) {
        e.preventDefault();
        self.sendMessage();
      });

      // Composer keypress Enter to send (Shift+Enter for newline) (delegated)
      $(document).on("keydown", "#waComposerText", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          self.sendMessage();
        }
      });

      // Quick response chips (delegated)
      $(document).on("click", ".wa-quick-chip", function () {
        const text = $(this).data("text") || "";
        const $input = $("#waComposerText");
        const current = $input.val().trim();
        $input.val(current ? current + " " + text : text).focus();
        const el = $input[0];
        if (el) {
          el.style.height = "auto";
          el.style.height = Math.min(el.scrollHeight, 120) + "px";
        }
      });

      // Attach Image button click
      $(document).on("click", "#btnWaAttachImage", function () {
        $("#waImageFileInput").trigger("click");
      });

      // File input change
      $(document).on("change", "#waImageFileInput", function () {
        const file = this.files && this.files[0];
        if (file) {
          self.setAttachment(file);
        }
      });

      // Remove attachment
      $(document).on("click", "#btnWaRemoveAttach", function () {
        self.clearAttachment();
      });

      // Paste image directly into composer textarea
      $(document).on("paste", "#waComposerText", function (e) {
        const clipboardData = e.originalEvent && e.originalEvent.clipboardData;
        if (!clipboardData || !clipboardData.items) return;
        for (let i = 0; i < clipboardData.items.length; i++) {
          const item = clipboardData.items[i];
          if (item.type && item.type.indexOf("image") !== -1) {
            const file = item.getAsFile();
            if (file) {
              self.setAttachment(file);
              break;
            }
          }
        }
      });

      // Lightbox view for chat images
      $(document).on("click", ".wa-msg-img", function () {
        const src = $(this).attr("src");
        if (src) {
          $("#waLightboxImage").attr("src", src);
          $("#waImageLightboxModal").removeClass("d-none").addClass("is-active").attr("aria-hidden", "false");
        }
      });

      // Close Lightbox
      $(document).on("click", "#btnCloseLightbox", function () {
        $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
        $("#waLightboxImage").attr("src", "");
      });

      $(document).on("click", "#waImageLightboxModal", function (e) {
        if (e.target === this) {
          $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
          $("#waLightboxImage").attr("src", "");
        }
      });

      // Refresh active chat (delegated)
      $(document).on("click", "#btnRefreshActiveChat", function () {
        if (self.activeChatId) {
          self.loadMessages(self.activeChatId, false);
        }
      });

      // Sync chats from WhatsApp into inbox (delegated)
      $(document).on("click", "#btnSyncChatsInbox", function () {
        const $btn = $(this);
        $btn.prop("disabled", true).find("i").addClass("wa-spin");
        $.ajax({
          url: "/api/v3/whatsapp/chats/?action=sync",
          method: "GET",
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).find("i").removeClass("wa-spin");
            self.loadThreads();
          },
          error: function () {
            $btn.prop("disabled", false).find("i").removeClass("wa-spin");
            self.loadThreads();
          }
        });
      });

      // Modal: Start New Chat (delegated)
      $(document).on("click", "#btnOpenNewChatModal, #btnEmptyStartChat, #btnSidebarNewChat", function () {
        $("#waNewChatModal").addClass("is-active").attr("aria-hidden", "false");
        $("#newChatPhone").focus();
      });

      $(document).on("click", "#btnCloseNewChatModal, #btnCancelNewChat", function () {
        $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
        $("#waNewChatForm")[0].reset();
      });

      // Close modal on Escape or clicking outside
      $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
          if ($("#waNewChatModal").hasClass("is-active")) {
            $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
            $("#waNewChatForm")[0].reset();
          }
          if (!$("#waImageLightboxModal").hasClass("d-none")) {
            $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
            $("#waLightboxImage").attr("src", "");
          }
        }
      });

      $(document).on("click", "#waNewChatModal", function (e) {
        if (e.target === this) {
          $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
          $("#waNewChatForm")[0].reset();
        }
      });

      // Submit New Chat (delegated)
      $(document).on("submit", "#waNewChatForm", function (e) {
        e.preventDefault();
        self.submitNewChat();
      });

      // Recheck connection button in unlinked state card (delegated)
      $(document).on("click", "#btnRecheckConnection", function () {
        const $btn = $(this);
        $btn.prop("disabled", true).find("i").addClass("wa-spin");
        self.checkConnectionStatus(function (isConnected) {
          $btn.prop("disabled", false).find("i").removeClass("wa-spin");
          if (isConnected) {
            self.loadThreads();
          }
        });
      });
    },

    renderDeviceTabs: function () {
      const self = this;
      const $sec = $("#waDeviceTabsSection");
      const $bar = $("#waDeviceTabsBar");
      const $nav = $("#waDeviceTabsNav");
      if (!$sec.length || !$bar.length) return;

      const devices = self.availableDevices || [];
      if (!devices.length) {
        $sec.addClass("d-none").hide();
        return;
      }

      $sec.removeClass("d-none").show();
      if (devices.length > 2) {
        $nav.removeClass("d-none").show();
      } else {
        $nav.addClass("d-none").hide();
      }

      const connectedCount = devices.filter(function (d) { return d.status === "connected"; }).length;
      const isAllSelected = !self.selectedDeviceId || self.selectedDeviceId === "all";

      let html = "";
      if (self.isAdmin && devices.length > 1) {
        html += `
          <div class="wa-device-tab ${isAllSelected ? "active" : ""}" data-device-id="all" role="button" tabindex="0" title="View all WhatsApp lines">
            <div class="wa-device-tab-avatar-wrap">
              <div class="wa-device-tab-avatar is-all">
                <i class="fi fi-rr-apps"></i>
              </div>
              <span class="wa-device-tab-status-dot is-connected"></span>
            </div>
            <div class="wa-device-tab-info">
              <div class="wa-device-tab-phone">All Lines</div>
              <div class="wa-device-tab-name">${connectedCount} Active / ${devices.length} Total</div>
              <div class="wa-device-tab-agent"><i class="fi fi-rr-users"></i> All Chats</div>
            </div>
          </div>
        `;
      }

      devices.forEach(function (d, idx) {
        const isCur = String(self.selectedDeviceId) === String(d.device_id);
        const isConn = d.status === "connected";
        const hasAgent = d.assigned_agent_name && d.assigned_agent_name !== "Unassigned";
        const initials = hasAgent ? self.getInitials(d.assigned_agent_name) : "WA";
        const gradIdx = idx % 6;
        const hasAvatar = d.avatar && String(d.avatar).trim() !== "" && !String(d.avatar).includes("default-profile.png");

        let avatarHtml = "";
        if (hasAvatar) {
          avatarHtml = `
            <img src="${self.escapeHtml(d.avatar)}" class="wa-device-tab-avatar-img" alt="${self.escapeHtml(d.assigned_agent_name)}" onerror="this.style.display='none';if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
            <div class="wa-device-tab-avatar wa-avatar-gradient-${gradIdx}" style="display:none;">${initials}</div>
          `;
        } else if (hasAgent) {
          avatarHtml = `<div class="wa-device-tab-avatar wa-avatar-gradient-${gradIdx}">${initials}</div>`;
        } else {
          avatarHtml = `<div class="wa-device-tab-avatar is-phone"><i class="fi fi-rr-smartphone"></i></div>`;
        }

        const agentDisplay = hasAgent
          ? `<div class="wa-device-tab-agent" title="Assigned Agent: ${self.escapeHtml(d.assigned_agent_name)}"><i class="fi fi-rr-user"></i> ${self.escapeHtml(d.assigned_agent_name)}</div>`
          : `<div class="wa-device-tab-agent is-unassigned"><i class="fi fi-rr-user-add"></i> Unassigned</div>`;

        html += `
          <div class="wa-device-tab ${isCur ? "active" : ""}" data-device-id="${d.device_id}" role="button" tabindex="0" title="${self.escapeHtml(d.phone || d.name)}">
            <div class="wa-device-tab-avatar-wrap">
              ${avatarHtml}
              <span class="wa-device-tab-status-dot ${isConn ? "is-connected" : "is-offline"}" title="${isConn ? "Online" : "Offline"}"></span>
            </div>
            <div class="wa-device-tab-info">
              <div class="wa-device-tab-phone">${self.escapeHtml(d.phone || "WhatsApp Line")}</div>
              <div class="wa-device-tab-name">${self.escapeHtml(d.name || "Line")}</div>
              ${agentDisplay}
            </div>
          </div>
        `;
      });

      if ($bar.data("last-html") !== html) {
        $bar.html(html).data("last-html", html);
      }

      // Populate New Chat Modal device options (Admin only when multiple devices exist)
      const $modalSelect = $("#newChatDeviceSelect");
      const $modalGroup = $("#waNewChatDeviceGroup");
      if ($modalSelect.length) {
        if (self.isAdmin && devices.length > 1) {
          $modalGroup.removeClass("d-none").show();
          let mOpts = "";
          devices.forEach(function (d) {
            const isConn = d.status === "connected";
            const ag = d.assigned_agent_name && d.assigned_agent_name !== "Unassigned" ? ` • ${d.assigned_agent_name}` : "";
            mOpts += `<option value="${d.device_id}">📱 ${d.phone} (${d.name || "Line"}${ag})${isConn ? " [Online]" : " [Offline]"}</option>`;
          });
          const hasSelect2 = typeof $.fn.select2 !== "undefined";
          if ($modalSelect.data("last-html") !== mOpts) {
            $modalSelect.html(mOpts).data("last-html", mOpts);
            if (hasSelect2) {
              try {
                if ($modalSelect.data("select2")) $modalSelect.select2("destroy");
                $modalSelect.select2({
                  dropdownParent: $("#waNewChatModal"),
                  width: "100%",
                  dropdownCssClass: "wa-select2-dropdown"
                });
              } catch (e) {}
            }
          }
        } else {
          $modalGroup.addClass("d-none").hide();
        }
      }
    },

    updateHeaderStatusBadge: function () {
      const self = this;
      const $badge = $("#waHeaderStatusBadge");
      const $text = $("#waHeaderStatusText");
      if (!$badge.length || !$text.length) return;

      if (!this.isDeviceLinked) {
        $badge.removeClass("is-connected").addClass("is-disconnected");
        $text.text("Device Offline");
        return;
      }

      $badge.removeClass("is-disconnected").addClass("is-connected");
      const devices = this.availableDevices || [];
      const connectedCount = devices.filter(function (d) { return d.status === "connected"; }).length;

      if (this.isAdmin) {
        if (!this.selectedDeviceId || this.selectedDeviceId === "all") {
          $text.text(connectedCount > 0 ? `All Lines (${connectedCount} Connected)` : "All Lines Active");
        } else {
          const curDev = devices.find(function (d) { return String(d.device_id) === String(self.selectedDeviceId); });
          if (curDev) {
            const agentLabel = curDev.assigned_agent_name && curDev.assigned_agent_name !== "Unassigned" ? ` • ${curDev.assigned_agent_name}` : "";
            $text.text(`${curDev.phone || "Line"}${agentLabel}`);
          } else {
            $text.text("Line Active");
          }
        }
      } else {
        const curDev = devices[0];
        if (curDev && curDev.phone) {
          $text.text(`${curDev.phone} (Online)`);
        } else {
          $text.text("Device Online");
        }
      }
    },

    updateDeviceLinkedUI: function (isLinked, phone, msg, hasAssignedDevice, isAdmin) {
      this.isDeviceLinked = !!isLinked;
      if (typeof isAdmin !== "undefined") {
        this.isAdmin = !!isAdmin;
      }
      if (typeof hasAssignedDevice !== "undefined") {
        this.hasAssignedDevice = !!hasAssignedDevice;
      }

      const $workspace = $("#waChatWorkspace");
      const $unlinked = $("#waUnlinkedStateCard");
      const $syncBtn = $("#btnSyncChatsInbox");
      const $newChatBtn = $("#btnOpenNewChatModal");
      const $settingsBtn = $("#btnWaDeviceSettings");
      const $unlinkedTitle = $("#waUnlinkedTitle");
      const $unlinkedMsg = $("#waUnlinkedMessage");
      const $linkBtn = $("#btnLinkDeviceNow");

      if (!this.isAdmin) {
        $settingsBtn.addClass("d-none").hide();
      } else {
        $settingsBtn.removeClass("d-none").show();
      }

      if (isLinked) {
        $workspace.removeClass("d-none");
        $unlinked.addClass("d-none");
        $syncBtn.removeClass("d-none");
        $newChatBtn.removeClass("d-none");
        this.updateHeaderStatusBadge();
      } else {
        $workspace.addClass("d-none");
        $unlinked.removeClass("d-none");
        $syncBtn.addClass("d-none");
        $newChatBtn.addClass("d-none");
        this.updateHeaderStatusBadge();

        if (!this.isAdmin && this.hasAssignedDevice === false) {
          if ($unlinkedTitle.length) $unlinkedTitle.text("No WhatsApp Device Assigned");
          if ($unlinkedMsg.length) $unlinkedMsg.text(msg || "No WhatsApp device has been assigned to your user account. Please contact an administrator to assign a device to your profile.");
          if ($linkBtn.length) $linkBtn.addClass("d-none").hide();
        } else {
          if ($unlinkedTitle.length) $unlinkedTitle.text("No WhatsApp Device Linked");
          if ($unlinkedMsg.length) $unlinkedMsg.text(msg || "You must link a WhatsApp device before you can access the CRM Live Inbox, read incoming customer messages, or reply to leads.");
          if ($linkBtn.length) $linkBtn.removeClass("d-none").show();
        }
      }
    },

    checkConnectionStatus: function (callback) {
      const self = this;
      $.ajax({
        url: "/api/v3/integration/whatsapp_web/?action=check_status",
        method: "GET",
        dataType: "json",
        success: function (res) {
          const isConnected = res && res.status === "success" && (res.connection_status === "connected" || res.is_device_linked === true);
          self.updateDeviceLinkedUI(isConnected, res ? res.phone : null, res ? res.msg : null, res ? res.has_assigned_device : true, res ? res.is_admin : false);
          if (typeof callback === "function") {
            callback(isConnected);
          }
        },
        error: function () {
          self.updateDeviceLinkedUI(false, null, "Unable to verify WhatsApp device connection status. Please ensure gateway is running.");
          if (typeof callback === "function") {
            callback(false);
          }
        }
      });
    },

    loadThreads: function (callback) {
      const self = this;
      if (this.isLoadingThreads) {
        if (typeof callback === "function") callback();
        return;
      }
      this.isLoadingThreads = true;

      const params = {
        filter: this.currentFilter,
        q: this.searchQuery,
        device_id: this.selectedDeviceId || "all",
        user_id: this.selectedUserId || 0
      };

      $.ajax({
        url: "/api/v3/whatsapp/chats/",
        method: "GET",
        data: params,
        dataType: "json",
        success: function (res) {
          self.isAdmin = !!(res && res.is_admin);
          self.availableDevices = (res && Array.isArray(res.devices)) ? res.devices : [];
          self.availableAgents = (res && Array.isArray(res.agents)) ? res.agents : [];

          const hasChats = res && Array.isArray(res.chats) && res.chats.length > 0;
          if (res && res.is_device_linked === false && !hasChats) {
            self.activeChatId = null;
            self.activePhone = null;
            if (self.knownChats) self.knownChats.clear();
            $("#waThreadsContainer").empty();
            $("#waMessagesStream").empty();
            $("#waActiveChatWrapper").removeClass("is-open");
            $("#waEmptyState").show();
            self.updateDeviceLinkedUI(false, null, res.msg, res.has_assigned_device, res.is_admin);
            return;
          } else if (res && (res.is_device_linked === true || hasChats)) {
            self.updateDeviceLinkedUI(true, res.linked_phone || "", null, res.has_assigned_device, res.is_admin);
          }

          try {
            self.renderDeviceTabs();
            self.updateHeaderStatusBadge();
          } catch (eDev) {
            console.warn("Device tabs render error:", eDev);
          }

          const $container = $("#waThreadsContainer");
          if (!res || res.status !== "success" || !Array.isArray(res.chats)) {
            $container.html('<div class="wa-loading-threads"><span>Failed to load chats.</span></div>');
            return;
          }

          if (res.chats.length === 0) {
            $container.html('<div class="wa-loading-threads"><i class="fi fi-rr-comment-slash"></i><span>No conversations found.</span></div>');
            return;
          }

          let newIncomingDetected = false;
          let latestMsgText = "";
          let latestSenderName = "";
          let latestChatId = null;
          let latestPhone = "";

          let html = "";
          res.chats.forEach(function (chat) {
            const prev = self.knownChats ? self.knownChats.get(chat.chat_id) : null;
            const isBrandNew = !prev && self.hasInitialLoaded && !!chat.last_message;
            const hasIncreasedUnread = prev && (chat.unread_count || 0) > (prev.unread_count || 0);
            const hasNewMsg = prev && chat.last_message && chat.last_message !== prev.last_message;

            if (isBrandNew || hasNewMsg || hasIncreasedUnread) {
              const chatDigits = self.cleanDigits(chat.phone);
              const activeDigits = self.cleanDigits(self.activePhone);
              const isPhoneMatch = chatDigits && activeDigits && chatDigits === activeDigits;
              const isActive = self.activeChatId && (String(chat.chat_id) === String(self.activeChatId) || isPhoneMatch);

              if (!isActive) {
                newIncomingDetected = true;
                latestMsgText = chat.last_message;
                latestSenderName = chat.name || chat.phone;
                latestChatId = chat.chat_id;
                latestPhone = chat.phone;
              } else {
                // Instantly refresh current chat stream and scroll to bottom
                self.loadMessages(self.activeChatId, true);
              }
            }
            if (!self.knownChats) self.knownChats = new Map();
            self.knownChats.set(chat.chat_id, {
              time_display: chat.time_display,
              unread_count: chat.unread_count || 0,
              last_message: chat.last_message
            });

            const isActive = self.activeChatId && (String(self.activeChatId) === String(chat.chat_id) || (self.cleanDigits(chat.phone) && self.cleanDigits(self.activePhone) && self.cleanDigits(chat.phone) === self.cleanDigits(self.activePhone)));
            if (isActive && String(self.activeChatId) !== String(chat.chat_id)) {
              self.activeChatId = chat.chat_id;
            }
            const hasLead = !!chat.lead;
            const avatarContent = chat.avatar
              ? `<img src="${self.escapeHtml(chat.avatar)}" alt="" class="wa-avatar-img" />`
              : self.getInitials(chat.name || chat.phone);

            const displayName = chat.name || chat.phone;
            const hasDistinctName = chat.name && chat.name !== chat.phone && !chat.name.includes(chat.phone);

            const devInfo = chat.device || {};
            const devPhone = devInfo.phone || "";
            const devName = devInfo.name || "";
            const agName = (devInfo.agent_name && devInfo.agent_name !== "Unassigned") ? devInfo.agent_name : "";
            const devLabel = devName && devName !== "WhatsApp Line" ? `${devName} (${devPhone})` : devPhone;

            let deviceTagHtml = "";
            if (devLabel || agName) {
              deviceTagHtml = `
                <div class="wa-thread-device-tag">
                  ${devLabel ? `<span class="wa-tag-device" title="WhatsApp Device / Line"><i class="fi fi-rr-smartphone"></i> ${self.escapeHtml(devLabel)}</span>` : ""}
                  ${agName ? `<span class="wa-tag-agent" title="Handling Sales Agent"><i class="fi fi-rr-user"></i> ${self.escapeHtml(agName)}</span>` : ""}
                </div>
              `;
            }

            html += `
              <div class="wa-thread-item ${isActive ? "active" : ""}" data-chat-id="${chat.chat_id}" data-phone="${self.escapeHtml(chat.phone)}">
                <div class="wa-thread-avatar ${hasLead ? "has-lead" : ""}">${avatarContent}</div>
                <div class="wa-thread-body">
                  <div class="wa-thread-top">
                    <span class="wa-thread-name">${self.escapeHtml(displayName)}</span>
                    <span class="wa-thread-time">${self.escapeHtml(chat.time_display)}</span>
                  </div>
                  ${hasDistinctName ? `<div class="wa-thread-phone-sub">${self.escapeHtml(chat.phone)}</div>` : ""}
                  ${deviceTagHtml}
                  <div class="wa-thread-bottom">
                    <p class="wa-thread-preview">${self.escapeHtml((chat.last_message === "[Image]" || chat.last_message === "[Photo]" || chat.last_message === "[Media]") ? "📷 Photo" : (chat.last_message || "No messages yet"))}</p>
                    ${chat.unread_count > 0 ? `<span class="wa-unread-badge">${chat.unread_count}</span>` : ""}
                  </div>
                </div>
              </div>
            `;
          });

          if (newIncomingDetected && self.hasInitialLoaded) {
            self.playNotificationSound();
            self.showIncomingAlert(latestSenderName, latestMsgText, latestChatId, latestPhone);
          }
          self.hasInitialLoaded = true;

          if (self.lastThreadsHtml === html && $container.children(".wa-thread-item").length > 0) {
            return;
          }
          self.lastThreadsHtml = html;
          $container.html(html);
        },
        error: function () {
          const $container = $("#waThreadsContainer");
          if (!self.hasInitialLoaded) {
            $container.html('<div class="wa-loading-threads"><i class="fi fi-rr-refresh"></i><span>Unable to load chats. <a href="javascript:void(0)" id="waRetryLoadThreads" style="color:#25D366;text-decoration:underline;">Retry</a></span></div>');
            $("#waRetryLoadThreads").on("click", function () { self.loadThreads(); });
          }
        },
        complete: function () {
          self.isLoadingThreads = false;
          if (typeof callback === "function") {
            callback();
          }
        }
      });
    },

    selectChat: function (chatId, phone) {
      this.activeChatId = chatId;
      this.activePhone = phone;
      this.lastStreamHtml = null;
      this.clearAttachment();

      $(".wa-thread-item").removeClass("active");
      $(`.wa-thread-item[data-chat-id="${chatId}"]`).addClass("active");
      $(`.wa-thread-item[data-chat-id="${chatId}"]`).find(".wa-unread-badge").remove();

      $("#waEmptyState").hide();
      $("#waActiveChatWrapper").addClass("is-open");
      $("#waChatWorkspace").addClass("mobile-chat-open");

      this.loadMessages(chatId, true);
    },

    loadMessages: function (chatId, scrollToBottom, callback) {
      const self = this;
      if (!chatId) {
        if (typeof callback === "function") callback();
        return;
      }
      if (this.isLoadingMessages) {
        if (typeof callback === "function") callback();
        return;
      }
      this.isLoadingMessages = true;

      $.ajax({
        url: "/api/v3/whatsapp/messages/?chat_id=" + chatId,
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (!res || res.status !== "success") return;

          const chat = res.chat;
          if (chat) {
            $("#waActiveName").text(chat.chat_name || chat.chat_phone);
            $("#waActivePhone").text(chat.chat_phone);
            if (chat.chat_avatar) {
              $("#waActiveAvatar").html(`<img src="${self.escapeHtml(chat.chat_avatar)}" alt="" class="wa-avatar-img" />`);
            } else {
              $("#waActiveAvatar").text(self.getInitials(chat.chat_name || chat.chat_phone));
            }

            if (chat.chat_lead_id) {
              $("#waActiveLeadBadge").show();
              $("#waActiveLeadText").text("Lead #" + chat.chat_lead_id + (chat.enquiry_name ? " (" + chat.enquiry_name + ")" : ""));
              $("#waViewLeadLink").attr("href", "/dashboard/leads/view/?id=" + btoa(chat.chat_lead_id)).show();
            } else {
              $("#waActiveLeadBadge").hide();
              $("#waViewLeadLink").hide();
            }

            if (chat.device && (chat.device.phone || chat.device.name || chat.device.agent_name)) {
              const devLine = chat.device.name && chat.device.name !== "WhatsApp Line"
                ? `${chat.device.name} (${chat.device.phone || ""})`
                : (chat.device.phone || "");
              const agName = (chat.device.agent_name && chat.device.agent_name !== "Unassigned")
                ? chat.device.agent_name
                : "";
              let badgeHtml = "";
              if (devLine && agName) {
                badgeHtml = `<i class="fi fi-rr-smartphone"></i> Line: <strong>${self.escapeHtml(devLine)}</strong> <span class="wa-badge-sep">•</span> <i class="fi fi-rr-user"></i> Agent: <strong>${self.escapeHtml(agName)}</strong>`;
              } else if (devLine) {
                badgeHtml = `<i class="fi fi-rr-smartphone"></i> Line: <strong>${self.escapeHtml(devLine)}</strong>`;
              } else if (agName) {
                badgeHtml = `<i class="fi fi-rr-user"></i> Agent: <strong>${self.escapeHtml(agName)}</strong>`;
              }
              if (badgeHtml) {
                $("#waActiveDeviceBadge").html(badgeHtml).removeClass("d-none").show();
              } else {
                $("#waActiveDeviceBadge").addClass("d-none").hide();
              }
            } else {
              $("#waActiveDeviceBadge").addClass("d-none").hide();
            }
          }

          const $stream = $("#waMessagesStream");
          if (!Array.isArray(res.messages) || res.messages.length === 0) {
            $stream.html('<div class="wa-loading-threads" style="color:rgba(0,0,0,0.5);"><span>No messages in this chat yet. Send the first message!</span></div>');
            self.lastStreamHtml = "";
            return;
          }

          let html = "";
          res.messages.forEach(function (msg) {
            const isOut = msg.direction === "outgoing";
            let contentHtml = "";
            if (msg.media_url) {
              if (msg.type === "image" || msg.media_url.match(/\.(jpeg|jpg|gif|png|webp)($|\?)/i)) {
                contentHtml += `<div class="wa-msg-media"><img src="${self.escapeHtml(msg.media_url)}" class="wa-msg-img" alt="Photo" loading="lazy" /></div>`;
              } else {
                contentHtml += `<div class="wa-msg-media"><a href="${self.escapeHtml(msg.media_url)}" class="wa-msg-doc-link" target="_blank" rel="noopener noreferrer"><i class="fi fi-rr-document"></i> View Attachment</a></div>`;
              }
              if (msg.text && msg.text !== "[Image]" && msg.text !== "[Photo]" && msg.text !== "[Media]") {
                contentHtml += `<div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>`;
              }
            } else {
              if (msg.text === "[Image]" || msg.text === "[Photo]" || msg.type === "image") {
                contentHtml += `<div class="wa-media-photo-card"><i class="fi fi-rr-picture"></i><span>Photo</span></div>`;
              } else if (msg.text) {
                contentHtml += `<div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>`;
              } else {
                contentHtml += `<div class="wa-msg-text"><em>[Message]</em></div>`;
              }
            }

            html += `
              <div class="wa-msg-bubble ${isOut ? "outgoing" : "incoming"}">
                ${contentHtml}
                <div class="wa-msg-meta">
                  <span>${self.escapeHtml(msg.time)}</span>
                  ${isOut ? '<span class="wa-ticks"><i class="fi fi-rr-check-double"></i></span>' : ""}
                </div>
              </div>
            `;
          });

          if (self.lastStreamHtml === html && $stream.children(".wa-msg-bubble").length > 0) {
            if (scrollToBottom) {
              $stream.scrollTop($stream[0].scrollHeight);
            }
            return;
          }
          self.lastStreamHtml = html;
          $stream.html(html);

          if (scrollToBottom) {
            $stream.scrollTop($stream[0].scrollHeight);
            setTimeout(function () {
              if ($stream.length) $stream.scrollTop($stream[0].scrollHeight);
            }, 60);
          } else {
            const isScrolledUp = ($stream[0].scrollHeight - $stream.scrollTop()) > ($stream[0].clientHeight + 120);
            if (isScrolledUp) {
              $("#waScrollUnreadDot").removeClass("d-none");
              $("#btnScrollToBottom").removeClass("d-none");
            }
          }
        },
        complete: function () {
          self.isLoadingMessages = false;
          if (typeof callback === "function") {
            callback();
          }
        }
      });
    },

    sendMessage: function () {
      const self = this;
      if (this.isSending || !this.activeChatId) return;

      const $textarea = $("#waComposerText");
      const text = $textarea.val().trim();
      if (!text && !self.attachedFile) return;

      this.isSending = true;
      const $btn = $("#btnSendMessage");
      $btn.prop("disabled", true).find("i").attr("class", "fi fi-rr-spinner wa-spin");

      let ajaxOptions = {};
      if (self.attachedFile) {
        const formData = new FormData();
        formData.append("chat_id", self.activeChatId);
        formData.append("phone", self.activePhone || "");
        formData.append("text", text);
        if (self.selectedDeviceId && self.selectedDeviceId !== "all") {
          formData.append("device_id", self.selectedDeviceId);
        }
        formData.append("media", self.attachedFile);

        ajaxOptions = {
          url: "/api/v3/whatsapp/messages/",
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json"
        };
      } else {
        ajaxOptions = {
          url: "/api/v3/whatsapp/messages/",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({
            chat_id: self.activeChatId,
            phone: self.activePhone,
            text: text,
            device_id: (self.selectedDeviceId && self.selectedDeviceId !== "all") ? self.selectedDeviceId : undefined
          }),
          dataType: "json"
        };
      }

      ajaxOptions.success = function (res) {
        self.isSending = false;
        $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");

        if (res && res.status === "success") {
          $textarea.val("").css("height", "auto").focus();
          self.clearAttachment();
          self.loadMessages(self.activeChatId, true);
          self.loadThreads();
        } else {
          alert(res.msg || "Failed to send WhatsApp message.");
        }
      };

      ajaxOptions.error = function () {
        self.isSending = false;
        $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");
        alert("Network error sending WhatsApp message.");
      };

      $.ajax(ajaxOptions);
    },

    submitNewChat: function () {
      const self = this;
      let phone = $("#newChatPhone").val().trim();
      const name = $("#newChatName").val().trim();
      const message = $("#newChatMessage").val().trim();

      if (!phone || !message) {
        alert("Please enter customer phone and message.");
        return;
      }

      let digits = phone.replace(/[^0-9]/g, "");
      if (digits.startsWith("0")) digits = digits.replace(/^0+/, "");
      if (digits.length === 10) digits = "91" + digits;
      if (digits.length < 8) {
        alert("Please enter a valid mobile number (at least 8 digits).");
        return;
      }
      phone = "+" + digits;

      const $btn = $("#btnSubmitNewChat");
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner wa-spin"></i> Dispatching...');

      $.ajax({
        url: "/api/v3/whatsapp/chats/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ phone: phone, name: name }),
        dataType: "json",
        success: function (chatRes) {
          if (chatRes && chatRes.status === "success" && chatRes.chat) {
            const chatId = chatRes.chat.chat_id;
            const targetPhone = chatRes.chat.chat_phone || phone;
            const selectedLine = $("#newChatDeviceSelect").val() || (self.selectedDeviceId !== "all" ? self.selectedDeviceId : undefined);
            // Send initial message
            $.ajax({
              url: "/api/v3/whatsapp/messages/",
              method: "POST",
              contentType: "application/json",
              data: JSON.stringify({
                chat_id: chatId,
                phone: targetPhone,
                text: message,
                device_id: selectedLine
              }),
              dataType: "json",
              success: function (sendRes) {
                $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
                if (sendRes && sendRes.status === "success") {
                  $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
                  $("#waNewChatForm")[0].reset();
                  self.loadThreads();
                  self.selectChat(chatId, targetPhone);
                } else {
                  alert(sendRes.msg || "Failed to send WhatsApp message.");
                }
              },
              error: function (xhr) {
                $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
                const err = xhr.responseJSON ? (xhr.responseJSON.msg || xhr.responseJSON.message) : "Failed to dispatch message to WhatsApp.";
                alert(err);
              }
            });
          } else {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
            alert(chatRes.msg || "Failed to initiate chat.");
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
          const err = xhr.responseJSON ? (xhr.responseJSON.msg || xhr.responseJSON.message) : "Network error initiating chat.";
          alert(err);
        }
      });
    },

    stopPolling: function () {
      if (this.pollTimer) {
        clearTimeout(this.pollTimer);
        this.pollTimer = null;
      }
    },

    startPolling: function () {
      this.stopPolling();
      const self = this;

      const runPollCycle = function () {
        // Automatically stop polling if WhatsApp UI is no longer present in DOM (SPA navigation)
        if (!$("#whatsappCrmApp").length) {
          self.stopPolling();
          return;
        }

        // If browser tab is in background, pause polling
        if (document.hidden) {
          self.pollTimer = setTimeout(runPollCycle, 5000);
          return;
        }

        const pollDelay = self.activeChatId ? 3000 : 7000;

        // Poll messages silently without resetting scroll if user is viewing history
        if (self.activeChatId && !self.isLoadingMessages) {
          const $stream = $("#waMessagesStream");
          const isAtBottom = $stream.length ? ($stream[0].scrollHeight - $stream.scrollTop() <= $stream.outerHeight() + 80) : true;
          self.loadMessages(self.activeChatId, isAtBottom);
        }

        // Poll threads, schedule next poll strictly AFTER current request completes
        if (!self.isLoadingThreads) {
          self.loadThreads(function () {
            if ($("#whatsappCrmApp").length) {
              self.pollTimer = setTimeout(runPollCycle, pollDelay);
            }
          });
        } else {
          self.pollTimer = setTimeout(runPollCycle, pollDelay);
        }
      };

      this.pollTimer = setTimeout(runPollCycle, 3000);
    },

    cleanDigits: function (p) {
      if (!p) return "";
      const d = String(p).replace(/[^0-9]/g, "");
      return d.length >= 10 ? d.slice(-10) : d;
    },

    getInitials: function (str) {
      if (!str) return "WA";
      const parts = str.trim().split(/\s+/);
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return str.substring(0, 2).toUpperCase();
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

    formatMessageText: function (text) {
      if (!text) return "";
      let safe = this.escapeHtml(text);
      // Convert newlines to br
      safe = safe.replace(/\n/g, "<br>");
      // Convert links
      const urlRegex = /(https?:\/\/[^\s]+)/g;
      return safe.replace(urlRegex, function (url) {
        return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" style="color:#2563EB;text-decoration:underline;">' + url + "</a>";
      });
    },

    fallbackCopy: function (text, callback) {
      try {
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        textarea.style.opacity = "0";
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);
        if (typeof callback === "function") callback();
      } catch (e) {}
    },

    playNotificationSound: function () {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = "sine";
        osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12); // A5
        gain.gain.setValueAtTime(0.15, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 0.32);
      } catch (e) {}
    },

    showIncomingAlert: function (sender, text, chatId, phone) {
      const self = this;
      $(".wa-incoming-alert-toast").remove();
      const $alert = $(`
        <div class="wa-incoming-alert-toast" role="alert">
          <span class="wa-incoming-alert-icon"><i class="fi fi-rr-comment-alt-check"></i></span>
          <div>
            <div class="wa-incoming-alert-title">${self.escapeHtml(sender || "New WhatsApp Message")}</div>
            <div class="wa-incoming-alert-msg">${self.escapeHtml(text || "New message received")}</div>
          </div>
        </div>
      `);
      $("body").append($alert);
      $alert.on("click", function () {
        if (chatId) {
          self.selectChat(chatId, phone);
        }
        $alert.fadeOut(200, function () { $(this).remove(); });
      });
      setTimeout(function () {
        $alert.fadeOut(400, function () { $(this).remove(); });
      }, 7000);
    },

    formatFileSize: function (bytes) {
      if (!bytes || bytes <= 0) return "0 B";
      const k = 1024;
      const sizes = ["B", "KB", "MB", "GB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
    },

    setAttachment: function (file) {
      const self = this;
      if (!file) return;
      if (!file.type || !file.type.startsWith("image/")) {
        alert("Please select an image file (JPG, PNG, GIF, WebP).");
        return;
      }
      if (file.size > 20 * 1024 * 1024) {
        alert("Image size exceeds maximum limit of 20MB.");
        return;
      }

      self.attachedFile = file;
      $("#waAttachFileName").text(file.name || "image.jpg");
      $("#waAttachFileSize").text(self.formatFileSize(file.size));

      const reader = new FileReader();
      reader.onload = function (e) {
        $("#waAttachThumb").attr("src", e.target.result);
        $("#waComposerAttachPreview").removeClass("d-none");
        $("#waComposerText").focus();
      };
      reader.readAsDataURL(file);
    },

    clearAttachment: function () {
      const self = this;
      self.attachedFile = null;
      $("#waImageFileInput").val("");
      $("#waAttachThumb").attr("src", "");
      $("#waAttachFileName").text("");
      $("#waAttachFileSize").text("");
      $("#waComposerAttachPreview").addClass("d-none");
    }
  };

  $(document).ready(function () {
    window.V3WhatsApp.init();
  });

  $(document).on("spa:loaded", function () {
    if ($("#whatsappCrmApp").length) {
      window.V3WhatsApp.init();
    } else {
      window.V3WhatsApp.stopPolling();
    }
  });

  $(window).on("popstate", function () {
    setTimeout(function () {
      if ($("#whatsappCrmApp").length) {
        window.V3WhatsApp.init();
      } else {
        window.V3WhatsApp.stopPolling();
      }
    }, 50);
  });
});
