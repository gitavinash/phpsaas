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

      // Multi-device switcher change
      $(document).on("change select2:select", "#waDeviceSelect", function () {
        if (self.isUpdatingDeviceSelect) return;
        self.selectedDeviceId = $(this).val() || "all";
        self.lastThreadsHtml = null;
        self.loadThreads();
      });

      // Open device switcher dropdown when clicking anywhere on the header container
      $(document).on("click", "#waDeviceSwitcherBox", function (e) {
        if (!$(e.target).closest(".select2-container").length) {
          const $sel = $("#waDeviceSelect");
          if ($sel.data("select2")) {
            $sel.select2("open");
          }
        }
      });

      // Quick user shortcut card click (Admin switching between team inboxes)
      $(document).on("click", ".wa-team-card, .wa-user-shortcut-pill", function () {
        const uid = parseInt($(this).data("user-id"), 10) || 0;
        if (self.selectedUserId === uid) {
          self.selectedUserId = 0; // toggle off back to all team
        } else {
          self.selectedUserId = uid;
        }
        self.renderUserShortcuts();
        self.lastThreadsHtml = null;
        self.loadThreads();
      });

      // Keyboard support for team filter cards
      $(document).on("keydown", ".wa-team-card", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          $(this).trigger("click");
        }
      });

      // Reset user filter button & dismiss banner
      $(document).on("click", "#btnResetUserFilter, #btnDismissFilterBanner", function () {
        self.selectedUserId = 0;
        self.renderUserShortcuts();
        self.lastThreadsHtml = null;
        self.loadThreads();
      });

      // Team filter card next / prev navigation
      $(document).on("click", "#btnNextTeamCard, #btnEdgeNextTeamCard", function (e) {
        e.preventDefault();
        self.scrollTeamCards("next");
      });

      $(document).on("click", "#btnPrevTeamCard, #btnEdgePrevTeamCard", function (e) {
        e.preventDefault();
        self.scrollTeamCards("prev");
      });

      $(document).on("scroll", "#waUsersShortcutBar", function () {
        self.updateTeamScrollArrows();
      });

      $(window).off("resize.waTeamScroll").on("resize.waTeamScroll", function () {
        self.updateTeamScrollArrows();
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
        if (e.key === "Escape" && $("#waNewChatModal").hasClass("is-active")) {
          $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
          $("#waNewChatForm")[0].reset();
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

    formatDeviceOption: function (opt) {
      const self = this;
      if (!opt.id || !opt.element) {
        return opt.text;
      }
      const $el = $(opt.element);
      const type = $el.attr("data-type");
      const isConn = $el.attr("data-status") === "connected";

      if (type === "all") {
        const count = $el.attr("data-count") || "0";
        const total = $el.attr("data-total") || "0";
        return $(`
          <div class="wa-device-menu-item is-all">
            <div class="wa-device-menu-avatar is-all">
              <i class="fi fi-rr-apps"></i>
            </div>
            <div class="wa-device-menu-info">
              <div class="wa-device-menu-title-row">
                <span class="wa-device-menu-title">All WhatsApp Lines</span>
                <span class="wa-device-menu-badge is-active">${count} Active</span>
              </div>
              <div class="wa-device-menu-sub">
                <span>Combined inbox • ${total} registered lines</span>
              </div>
            </div>
          </div>
        `);
      }

      const phone = $el.attr("data-phone") || opt.text;
      const name = $el.attr("data-name") || "WhatsApp Line";
      const agent = $el.attr("data-agent") || "";

      return $(`
        <div class="wa-device-menu-item">
          <div class="wa-device-menu-avatar ${isConn ? "is-connected" : "is-offline"}">
            <i class="fi fi-rr-smartphone"></i>
            <span class="wa-device-menu-dot ${isConn ? "is-connected" : "is-offline"}"></span>
          </div>
          <div class="wa-device-menu-info">
            <div class="wa-device-menu-title-row">
              <span class="wa-device-menu-phone">${self.escapeHtml(phone)}</span>
              <span class="wa-device-menu-status-pill ${isConn ? "is-online" : "is-offline"}">
                ${isConn ? "Connected" : "Offline"}
              </span>
            </div>
            <div class="wa-device-menu-sub">
              <span class="wa-device-menu-line-name" title="${self.escapeHtml(name)}">${self.escapeHtml(name)}</span>
              ${agent ? `<span class="wa-device-menu-agent" title="Assigned Agent: ${self.escapeHtml(agent)}"><i class="fi fi-rr-user"></i> ${self.escapeHtml(agent)}</span>` : ""}
            </div>
          </div>
        </div>
      `);
    },

    formatDeviceSelection: function (opt) {
      const self = this;
      if (!opt.id) return opt.text;
      const $el = $(opt.element);
      if (!$el.length) return opt.text;

      const type = $el.attr("data-type");
      if (type === "all") {
        const count = $el.attr("data-count") || "0";
        return $(`
          <span class="wa-device-chip-sel">
            <span class="wa-device-chip-dot is-connected"></span>
            <span class="wa-device-chip-text">All Devices (${count} Active)</span>
          </span>
        `);
      }

      const phone = $el.attr("data-phone") || opt.text;
      const name = $el.attr("data-name") || "";
      const isConn = $el.attr("data-status") === "connected";

      return $(`
        <span class="wa-device-chip-sel">
          <span class="wa-device-chip-dot ${isConn ? "is-connected" : "is-offline"}"></span>
          <span class="wa-device-chip-text">${self.escapeHtml(phone)}</span>
          ${name ? `<span class="wa-device-chip-meta">(${self.escapeHtml(name)})</span>` : ""}
        </span>
      `);
    },

    renderDeviceSwitcher: function () {
      const self = this;
      const $select = $("#waDeviceSelect");
      const $box = $("#waDeviceSwitcherBox");
      if (!$select.length) return;

      // RULE: Do NOT give device choose option to sales agent user.
      // Only show assigned device to sales user (displayed in header status badge).
      // Device switcher dropdown is strictly for administrators with multiple devices.
      if (!self.isAdmin || !self.availableDevices || self.availableDevices.length <= 1) {
        $box.addClass("d-none").hide();
        return;
      }

      $box.removeClass("d-none").css("display", "inline-flex").show();
      const connectedCount = self.availableDevices.filter(function (d) { return d.status === "connected"; }).length;
      const totalCount = self.availableDevices.length;
      const isAllSelected = !self.selectedDeviceId || self.selectedDeviceId === "all";

      let optHtml = `<option value="all" data-type="all" data-status="connected" data-count="${connectedCount}" data-total="${totalCount}" ${isAllSelected ? "selected" : ""}>All Devices (${connectedCount} Active)</option>`;

      self.availableDevices.forEach(function (d) {
        const devName = self.escapeHtml(d.name || "Line");
        const phone = self.escapeHtml(d.phone || "");
        const agent = (d.assigned_agent_name && d.assigned_agent_name !== "Unassigned") ? self.escapeHtml(d.assigned_agent_name) : "";
        const isSelected = String(self.selectedDeviceId) === String(d.device_id);
        optHtml += `<option value="${d.device_id}" data-type="device" data-phone="${phone}" data-name="${devName}" data-agent="${agent}" data-status="${d.status}" ${isSelected ? "selected" : ""}>${phone} (${devName})</option>`;
      });

      self.isUpdatingDeviceSelect = true;
      try {
        const hasSelect2 = typeof $.fn.select2 !== "undefined";
        const isSelect2Attached = hasSelect2 && !!$select.data("select2");
        const htmlChanged = $select.data("last-html") !== optHtml;

        if (htmlChanged || !isSelect2Attached) {
          if (isSelect2Attached) {
            try { $select.select2("destroy"); } catch (e) {}
          }
          $select.html(optHtml).data("last-html", optHtml);
          $select.val(self.selectedDeviceId || "all");

          if (hasSelect2) {
            $select.select2({
              dropdownParent: $("body"),
              dropdownAutoWidth: false,
              width: "100%",
              minimumResultsForSearch: 6,
              dropdownCssClass: "wa-device-select2-dropdown",
              templateResult: function (data) { return self.formatDeviceOption(data); },
              templateSelection: function (data) { return self.formatDeviceSelection(data); },
              escapeMarkup: function (m) { return m; }
            });
            $select.off("select2:open select2:close").on("select2:open", function () {
              $("#waDeviceSwitcherBox").addClass("has-focus");
            }).on("select2:close", function () {
              $("#waDeviceSwitcherBox").removeClass("has-focus");
            });
          }
        } else if (isSelect2Attached) {
          if ($select.val() !== (self.selectedDeviceId || "all")) {
            $select.val(self.selectedDeviceId || "all").trigger("change.select2");
          }
        }
      } catch (err) {
        console.warn("Device switcher render warning:", err);
      } finally {
        self.isUpdatingDeviceSelect = false;
      }

      // Populate New Chat Modal device options (Admin only when multiple devices exist)
      const $modalSelect = $("#newChatDeviceSelect");
      const $modalGroup = $("#waNewChatDeviceGroup");
      if ($modalSelect.length) {
        if (self.isAdmin && self.availableDevices.length > 1) {
          $modalGroup.removeClass("d-none").show();
          let mOpts = "";
          self.availableDevices.forEach(function (d) {
            const devName = self.escapeHtml(d.name || "Line");
            const phone = self.escapeHtml(d.phone || "");
            const agent = (d.assigned_agent_name && d.assigned_agent_name !== "Unassigned") ? self.escapeHtml(d.assigned_agent_name) : "";
            mOpts += `<option value="${d.device_id}" data-type="device" data-phone="${phone}" data-name="${devName}" data-agent="${agent}" data-status="${d.status}">${phone} (${devName})</option>`;
          });
          const hasSelect2 = typeof $.fn.select2 !== "undefined";
          const mAttached = hasSelect2 && !!$modalSelect.data("select2");
          if ($modalSelect.data("last-html") !== mOpts || !mAttached) {
            if (mAttached) {
              try { $modalSelect.select2("destroy"); } catch (e) {}
            }
            $modalSelect.html(mOpts).data("last-html", mOpts);
            if (hasSelect2) {
              try {
                $modalSelect.select2({
                  dropdownParent: $("#waNewChatModal"),
                  width: "100%",
                  dropdownCssClass: "wa-device-select2-dropdown",
                  templateResult: function (data) { return self.formatDeviceOption(data); },
                  templateSelection: function (data) { return self.formatDeviceSelection(data); },
                  escapeMarkup: function (m) { return m; }
                });
              } catch (e) {}
            }
          }
        } else {
          $modalGroup.addClass("d-none").hide();
        }
      }
    },

    renderUserShortcuts: function () {
      const self = this;
      const $section = $("#waUsersShortcutSection");
      const $bar = $("#waUsersShortcutBar");
      const $banner = $("#waActiveFilterBanner");
      const $bannerText = $("#waActiveFilterText");
      const $resetBtn = $("#btnResetUserFilter");

      if (!$section.length || !$bar.length) return;

      // Only show for Admin when there are team members linked with a WhatsApp device
      const linkedAgents = (self.availableAgents || []).filter(function (ag) {
        return ag && (ag.is_connected || (ag.device_phone && String(ag.device_phone).trim() !== ""));
      });

      if (!self.isAdmin || linkedAgents.length === 0) {
        $section.addClass("d-none").hide();
        $banner.addClass("d-none").hide();
        return;
      }

      $section.removeClass("d-none").show();

      if (self.selectedUserId > 0 && !linkedAgents.some(function (ag) { return parseInt(ag.user_id, 10) === parseInt(self.selectedUserId, 10); })) {
        self.selectedUserId = 0;
      }

      const isAllActive = !self.selectedUserId || self.selectedUserId === 0;
      let cardsHtml = `
        <div class="wa-team-card ${isAllActive ? "active" : ""}" data-user-id="0" role="button" tabindex="0" title="Show all team conversations">
          <div class="wa-team-card-avatar-wrap">
            <div class="wa-team-card-avatar wa-team-card-avatar-all">
              <i class="fi fi-rr-users"></i>
            </div>
          </div>
          <div class="wa-team-card-info">
            <div class="wa-team-card-name">All Team</div>
            <div class="wa-team-card-sub">${linkedAgents.length} Agents</div>
          </div>
        </div>
      `;

      let activeAgentName = "";
      linkedAgents.forEach(function (ag, idx) {
        const isCur = self.selectedUserId && parseInt(self.selectedUserId, 10) === parseInt(ag.user_id, 10);
        if (isCur) activeAgentName = ag.name;
        const initials = self.getInitials(ag.name);
        const gradIdx = idx % 6;
        const hasAvatar = ag.avatar && String(ag.avatar).trim() !== "" && !String(ag.avatar).includes("default-profile.png");
        const subText = ag.device_phone ? ag.device_phone : (ag.device_name || "WhatsApp");

        let avatarHtml = "";
        if (hasAvatar) {
          avatarHtml = `
            <img src="${self.escapeHtml(ag.avatar)}" class="wa-team-card-avatar-img" alt="${self.escapeHtml(ag.name)}" onerror="this.style.display='none';if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
            <div class="wa-team-card-avatar wa-avatar-gradient-${gradIdx}" style="display:none;">${initials}</div>
          `;
        } else {
          avatarHtml = `<div class="wa-team-card-avatar wa-avatar-gradient-${gradIdx}">${initials}</div>`;
        }

        cardsHtml += `
          <div class="wa-team-card ${isCur ? "active" : ""}" data-user-id="${ag.user_id}" role="button" tabindex="0" title="Filter chats for ${self.escapeHtml(ag.name)} (${self.escapeHtml(subText)})">
            <div class="wa-team-card-avatar-wrap">
              ${avatarHtml}
              <span class="wa-team-card-status-dot ${ag.is_connected ? "" : "is-offline"}" title="${ag.is_connected ? "WhatsApp Connected" : "Device Offline"}"></span>
            </div>
            <div class="wa-team-card-info">
              <div class="wa-team-card-name" title="${self.escapeHtml(ag.name)}">${self.escapeHtml(ag.name)}</div>
              <div class="wa-team-card-sub" title="${self.escapeHtml(subText)}">${self.escapeHtml(subText)}</div>
            </div>
          </div>
        `;
      });

      if ($bar.data("last-html") !== cardsHtml) {
        $bar.html(cardsHtml).data("last-html", cardsHtml);
      }

      if (self.selectedUserId > 0 && activeAgentName) {
        $resetBtn.removeClass("d-none").show();
        $banner.removeClass("d-none").show();
        $bannerText.html(`<i class="fi fi-rr-filter"></i> Filtering chats for: <strong>${self.escapeHtml(activeAgentName)}</strong>`);
      } else {
        $resetBtn.addClass("d-none").hide();
        $banner.addClass("d-none").hide();
      }

      setTimeout(function () {
        self.updateTeamScrollArrows();
      }, 50);
    },

    scrollTeamCards: function (direction) {
      const el = document.getElementById("waUsersShortcutBar");
      if (!el) return;
      const $firstCard = $(el).find(".wa-team-card").first();
      const cardWidth = $firstCard.length ? ($firstCard.outerWidth() + 8) : 170;
      const delta = direction === "next" ? cardWidth : -cardWidth;
      el.scrollBy({ left: delta, behavior: "smooth" });
    },

    updateTeamScrollArrows: function () {
      const el = document.getElementById("waUsersShortcutBar");
      if (!el) return;
      const maxScroll = el.scrollWidth - el.clientWidth;
      const canScroll = maxScroll > 4;
      const isAtStart = el.scrollLeft <= 5;
      const isAtEnd = el.scrollLeft >= maxScroll - 5;

      const $allPrev = $("#btnPrevTeamCard, #btnEdgePrevTeamCard");
      const $allNext = $("#btnNextTeamCard, #btnEdgeNextTeamCard");

      if (!canScroll) {
        $allPrev.prop("disabled", true).addClass("is-disabled d-none");
        $allNext.prop("disabled", true).addClass("is-disabled d-none");
      } else {
        $("#btnPrevTeamCard").removeClass("d-none").prop("disabled", isAtStart).toggleClass("is-disabled", isAtStart);
        $("#btnNextTeamCard").removeClass("d-none").prop("disabled", isAtEnd).toggleClass("is-disabled", isAtEnd);

        $("#btnEdgePrevTeamCard").toggleClass("d-none", isAtStart);
        $("#btnEdgeNextTeamCard").toggleClass("d-none", isAtEnd);
      }
    },

    updateDeviceLinkedUI: function (isLinked, phone, msg) {
      this.isDeviceLinked = !!isLinked;
      const $workspace = $("#waChatWorkspace");
      const $unlinked = $("#waUnlinkedStateCard");
      const $badge = $("#waHeaderStatusBadge");
      const $text = $("#waHeaderStatusText");
      const $syncBtn = $("#btnSyncChatsInbox");
      const $newChatBtn = $("#btnOpenNewChatModal");

      if (isLinked) {
        $workspace.removeClass("d-none");
        $unlinked.addClass("d-none");
        $syncBtn.removeClass("d-none");
        $newChatBtn.removeClass("d-none");

        $badge.addClass("is-connected");
        $text.text("Linked: " + (phone || "Connected"));
      } else {
        $workspace.addClass("d-none");
        $unlinked.removeClass("d-none");
        $syncBtn.addClass("d-none");
        $newChatBtn.addClass("d-none");

        $badge.removeClass("is-connected");
        $text.text("Device Not Linked");

        if (msg) {
          $("#waUnlinkedMessage").text(msg);
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
          self.updateDeviceLinkedUI(isConnected, res ? res.phone : null);
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
          const hasChats = res && Array.isArray(res.chats) && res.chats.length > 0;
          if (res && res.is_device_linked === false && !hasChats) {
            self.activeChatId = null;
            self.activePhone = null;
            if (self.knownChats) self.knownChats.clear();
            $("#waThreadsContainer").empty();
            $("#waMessagesStream").empty();
            $("#waActiveChatWrapper").removeClass("is-open");
            $("#waEmptyState").show();
            self.updateDeviceLinkedUI(false, null);
            return;
          } else if (res && (res.is_device_linked === true || hasChats)) {
            self.updateDeviceLinkedUI(true, res.linked_phone || "");
          }

          self.isAdmin = !!(res && res.is_admin);
          self.availableDevices = (res && Array.isArray(res.devices)) ? res.devices : [];
          self.availableAgents = (res && Array.isArray(res.agents)) ? res.agents : [];

          try {
            // Render / Update Multi-Device Switcher dropdown
            self.renderDeviceSwitcher();
          } catch (eDev) {
            console.warn("Device switcher render error:", eDev);
          }

          try {
            // Render / Update Quick User Shortcut Bar (for Admin)
            self.renderUserShortcuts();
          } catch (eUsr) {
            console.warn("User shortcuts render error:", eUsr);
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
                    <p class="wa-thread-preview">${self.escapeHtml(chat.last_message || "No messages yet")}</p>
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
                contentHtml += `<div class="wa-msg-media"><img src="${self.escapeHtml(msg.media_url)}" alt="Media" style="max-width:100%;border-radius:8px;margin-bottom:4px;display:block;" /></div>`;
              } else {
                contentHtml += `<div class="wa-msg-media"><a href="${self.escapeHtml(msg.media_url)}" target="_blank" rel="noopener noreferrer" style="color:#2563EB;text-decoration:underline;"><i class="fi fi-rr-document"></i> View Attachment</a></div>`;
              }
            }
            if (msg.text) {
              contentHtml += `<div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>`;
            } else if (!msg.media_url) {
              contentHtml += `<div class="wa-msg-text"><em>[Message]</em></div>`;
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
      if (!text) return;

      this.isSending = true;
      const $btn = $("#btnSendMessage");
      $btn.prop("disabled", true).find("i").attr("class", "fi fi-rr-spinner wa-spin");

      $.ajax({
        url: "/api/v3/whatsapp/messages/",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          chat_id: self.activeChatId,
          phone: self.activePhone,
          text: text,
          device_id: (self.selectedDeviceId && self.selectedDeviceId !== "all") ? self.selectedDeviceId : undefined
        }),
        dataType: "json",
        success: function (res) {
          self.isSending = false;
          $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");

          if (res && res.status === "success") {
            $textarea.val("").css("height", "auto").focus();
            self.loadMessages(self.activeChatId, true);
            self.loadThreads();
          } else {
            alert(res.msg || "Failed to send WhatsApp message.");
          }
        },
        error: function () {
          self.isSending = false;
          $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");
          alert("Network error sending WhatsApp message.");
        }
      });
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
