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
    isCallingAgent: false,
    userRole: "",
    pollTimer: null,
    isDeviceLinked: false,
    eventsBound: false,
    isLoadingThreads: false,
    isLoadingMessages: false,
    pendingLeadPhone: null,
    pendingLeadName: null,
    pendingLeadId: null,

    init: function () {
      const $app = $("#whatsappCrmApp");
      if (!$app.length) {
        this.stopPolling();
        return;
      }

      const isAdminAttr = $app.data("is-admin");
      const isCallingAgentAttr = $app.data("is-calling-agent");
      const userRoleAttr = $app.data("user-role");

      if (typeof isAdminAttr !== "undefined") {
        this.isAdmin = String(isAdminAttr) === "1" || isAdminAttr === true;
      }
      if (typeof isCallingAgentAttr !== "undefined") {
        this.isCallingAgent = String(isCallingAgentAttr) === "1" || isCallingAgentAttr === true;
      }
      if (typeof userRoleAttr !== "undefined") {
        this.userRole = String(userRoleAttr).toLowerCase();
        if (["agent", "calling_agent", "crm_calling_agent", "crm calling agent", "sales_executive"].includes(this.userRole)) {
          this.isCallingAgent = true;
        }
      }

      this.stopPolling();
      this.lastThreadsHtml = null;

      try {
        const urlParams = new URLSearchParams(window.location.search);
        const qPhone = urlParams.get("phone");
        const qChatId = urlParams.get("chat_id");
        if (qChatId) {
          this.pendingChatId = qChatId;
        }
        if (qPhone) {
          this.pendingLeadPhone = qPhone;
          this.pendingLeadName = urlParams.get("name") || "";
          this.pendingLeadId = urlParams.get("lead_id") || "";
        }
      } catch (e) {
        console.warn("Error parsing URL params:", e);
      }

      this.bindEvents();
      this.checkConnectionStatus();
      this.initNotificationPermission();
      this.loadThreads();
      this.startPolling();
    },


    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // Browser Push Notification enable button
      $(document).on("click", "#btnEnableNotifications", function () {
        self.requestNotificationPermission(true);
      });

      // User gesture trigger for notification permission prompt
      const triggerPermOnGesture = function () {
        if ("Notification" in window && Notification.permission === "default") {
          self.requestNotificationPermission(false);
        }
        document.removeEventListener("click", triggerPermOnGesture);
      };
      document.addEventListener("click", triggerPermOnGesture, { once: true });

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
      $(document).on("click", ".wa-device-tab, .wa-device-rail-item", function () {
        const devId = $(this).data("device-id");
        self.selectedDeviceId = devId || "all";
        $(".wa-device-tab, .wa-device-rail-item").removeClass("active");
        $(this).addClass("active");
        self.updateHeaderStatusBadge();
        self.lastThreadsHtml = null;
        self.loadThreads();
      });

      // Keyboard support for device tabs
      $(document).on("keydown", ".wa-device-tab, .wa-device-rail-item", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          $(this).trigger("click");
        }
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
      $(document).on("click", ".btn-wa-copy-phone, #btnCopyActivePhone, #btnCopyActivePhoneName", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const phone = self.activePhone || $("#waActivePhone").text().trim() || $("#waActiveName").text().trim();
        if (!phone) return;
        const $btn = $(this);
        const originalHtml = $btn.html();
        const copySuccess = function () {
          $btn.html('<i class="fi fi-rr-check" style="color:#10B981;"></i>');
          setTimeout(function () {
            $btn.html(originalHtml);
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

      // Lightbox view for chat images (WhatsApp Web style)
      $(document).on("click", ".wa-msg-img", function () {
        const src = $(this).attr("src");
        if (src) {
          const $bubble = $(this).closest(".wa-msg-bubble");
          const isOut = $bubble.hasClass("outgoing");
          const activeName = $("#waActiveChatName").text().trim() || "WhatsApp Contact";
          const timeText = $bubble.find(".wa-msg-meta span:first").text().trim();

          $("#waLightboxImage").attr("src", src).removeClass("is-zoomed");
          $("#btnZoomLightbox i").attr("class", "fi fi-rr-zoom-in");
          $("#waLightboxSender").text(isOut ? "You" : activeName);
          $("#waLightboxTime").text(timeText);
          $("#btnDownloadLightbox").attr("href", src).attr("download", src.split("/").pop() || "whatsapp_image.jpg");
          $("#waImageLightboxModal").removeClass("d-none").addClass("is-active").attr("aria-hidden", "false");
        }
      });

      // Zoom toggle in Lightbox
      $(document).on("click", "#btnZoomLightbox", function () {
        const $img = $("#waLightboxImage");
        $img.toggleClass("is-zoomed");
        const isZ = $img.hasClass("is-zoomed");
        $(this).find("i").attr("class", isZ ? "fi fi-rr-zoom-out" : "fi fi-rr-zoom-in");
      });

      // Close Lightbox
      $(document).on("click", "#btnCloseLightbox, #btnCloseLightboxTop", function () {
        $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
        $("#waLightboxImage").attr("src", "").removeClass("is-zoomed");
      });

      $(document).on("click", "#waLightboxBody", function (e) {
        if (e.target === this) {
          $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
          $("#waLightboxImage").attr("src", "").removeClass("is-zoomed");
        }
      });

      $(document).on("keydown", function (e) {
        if (e.key === "Escape" && !$("#waImageLightboxModal").hasClass("d-none")) {
          $("#waImageLightboxModal").addClass("d-none").removeClass("is-active").attr("aria-hidden", "true");
          $("#waLightboxImage").attr("src", "").removeClass("is-zoomed");
        }
      });

      // Capture phase listeners for non-bubbling image load/error events
      document.addEventListener("load", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-msg-img")) {
          const img = e.target;
          img.classList.add("is-ready");
          const loader = img.parentElement ? img.parentElement.querySelector(".wa-img-skeleton-loader") : null;
          if (loader) {
            loader.remove();
          }
        }
      }, true);

      document.addEventListener("error", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-msg-img")) {
          const img = e.target;
          const box = img.closest(".wa-msg-img-box");
          if (box) {
            const bubble = box.closest(".wa-msg-bubble");
            const waId = bubble ? (bubble.getAttribute("data-wa-id") || "") : "";
            const retries = parseInt(box.getAttribute("data-retries") || "0", 10);
            if (retries < 1 && waId) {
              box.setAttribute("data-retries", (retries + 1).toString());
              self.fetchSingleMedia(waId, $(box));
            } else {
              box.innerHTML = `
                <div class="wa-img-error-fallback" data-retry-wa-id="${self.escapeHtml(waId)}">
                  <i class="fi fi-rr-refresh"></i>
                  <span>Failed to load photo · Click to retry</span>
                </div>
              `;
            }
          }
        }
      }, true);

      // Voice Note Player Listeners (WhatsApp Web Audio Experience)
      $(document).on("click", ".wa-voice-play-btn", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const $player = $btn.closest(".wa-voice-player");

        // Pending voice note download
        if ($player.hasClass("is-pending")) {
          const waId = $player.closest(".wa-msg-voice-card").attr("data-retry-wa-id");
          if (waId) {
            $btn.find("i").attr("class", "fi fi-rr-spinner wa-spin");
            self.fetchSingleMedia(waId, $player.closest(".wa-msg-voice-card"));
          }
          return;
        }

        const audio = $player.find("audio.wa-voice-audio-el")[0];
        if (!audio) return;

        // Pause any other active audio elements on the page
        $("audio.wa-voice-audio-el").each(function () {
          if (this !== audio && !this.paused) {
            this.pause();
            const $other = $(this).closest(".wa-voice-player");
            $other.find(".wa-voice-play-btn i").attr("class", "fi fi-rr-play");
          }
        });

        if (audio.paused) {
          audio.play().then(function () {
            $btn.find("i").attr("class", "fi fi-rr-pause");
          }).catch(function (err) {
            console.warn("Audio playback failed:", err);
          });
        } else {
          audio.pause();
          $btn.find("i").attr("class", "fi fi-rr-play");
        }
      });

      // Voice speed toggle (1x -> 1.5x -> 2x -> 1x)
      $(document).on("click", ".wa-voice-speed-pill", function (e) {
        e.preventDefault();
        const $pill = $(this);
        const $player = $pill.closest(".wa-voice-player");
        const audio = $player.find("audio.wa-voice-audio-el")[0];
        if (!audio) return;

        let rate = audio.playbackRate || 1.0;
        if (rate === 1.0) rate = 1.5;
        else if (rate === 1.5) rate = 2.0;
        else rate = 1.0;

        audio.playbackRate = rate;
        $pill.text(rate + "x");
      });

      // Slider scrubbing
      $(document).on("input change", ".wa-voice-slider", function () {
        const $slider = $(this);
        const $player = $slider.closest(".wa-voice-player");
        const audio = $player.find("audio.wa-voice-audio-el")[0];
        if (!audio || !audio.duration) return;

        const seekTime = (parseFloat($slider.val()) / 100) * audio.duration;
        audio.currentTime = seekTime;
        self.updateVoiceProgress($player, audio);
      });

      // Waveform click seeking
      $(document).on("click", ".wa-voice-waveform", function (e) {
        const $wave = $(this);
        const $player = $wave.closest(".wa-voice-player");
        const audio = $player.find("audio.wa-voice-audio-el")[0];
        if (!audio || !audio.duration) return;

        const rect = this.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const ratio = Math.max(0, Math.min(1, clickX / rect.width));
        audio.currentTime = ratio * audio.duration;
        self.updateVoiceProgress($player, audio);
      });

      // Native audio listeners via capture phase
      document.addEventListener("timeupdate", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-voice-audio-el")) {
          const audio = e.target;
          const $player = $(audio).closest(".wa-voice-player");
          if ($player.length) {
            self.updateVoiceProgress($player, audio);
          }
        }
      }, true);

      document.addEventListener("loadedmetadata", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-voice-audio-el")) {
          const audio = e.target;
          const $player = $(audio).closest(".wa-voice-player");
          if ($player.length) {
            $player.find(".wa-voice-time").text(self.formatAudioTime(audio.duration));
          }
        }
      }, true);

      document.addEventListener("ended", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-voice-audio-el")) {
          const audio = e.target;
          const $player = $(audio).closest(".wa-voice-player");
          if ($player.length) {
            $player.find(".wa-voice-play-btn i").attr("class", "fi fi-rr-play");
            audio.currentTime = 0;
            self.updateVoiceProgress($player, audio);
          }
        }
      }, true);

      document.addEventListener("pause", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("wa-voice-audio-el")) {
          const audio = e.target;
          const $player = $(audio).closest(".wa-voice-player");
          if ($player.length) {
            $player.find(".wa-voice-play-btn i").attr("class", "fi fi-rr-play");
          }
        }
      }, true);

      // Click to retry loading image, audio, video, or document
      $(document).on("click", ".wa-img-error-fallback[data-retry-wa-id], .wa-media-photo-card[data-retry-wa-id], .wa-video-skeleton-loader, .wa-media-doc-card-pending[data-retry-wa-id], .wa-media-voice-card-pending[data-retry-wa-id]", function (e) {
        const $el = $(this);
        const waId = $el.attr("data-retry-wa-id") || $el.closest("[data-retry-wa-id]").attr("data-retry-wa-id");
        if (!waId) return;

        const $container = $el.closest(".wa-msg-img-box, .wa-msg-voice-card, .wa-msg-doc-card, .wa-msg-video-box");
        if ($container.length) {
          $container.html(`
            <div class="wa-img-skeleton-loader">
              <i class="fi fi-rr-spinner wa-spin"></i>
              <span>Downloading media...</span>
            </div>
          `);
          self.fetchSingleMedia(waId, $container);
        } else {
          $el.addClass("is-loading");
          self.fetchSingleMedia(waId, $el);
        }
      });

      // Contact card Message / Chat button
      $(document).on("click", ".btn-wa-contact-chat", function (e) {
        e.preventDefault();
        const phone = $(this).attr("data-contact-phone") || "";
        const name = $(this).attr("data-contact-name") || "";

        if (!phone) {
          $("#btnSidebarNewChat").trigger("click");
          if (name) {
            $("#newChatName").val(name);
          }
          return;
        }

        const cleanPhone = phone.replace(/[^0-9+]/g, "");
        const rawDigits = phone.replace(/[^0-9]/g, "");
        const last10 = rawDigits.slice(-10);

        // Check if thread already exists in the sidebar
        let $foundThread = null;
        $(".wa-thread-item").each(function () {
          const tPhone = ($(this).data("phone") || "").toString().replace(/[^0-9]/g, "");
          if (tPhone && (tPhone.includes(rawDigits) || tPhone.endsWith(last10) || rawDigits.endsWith(tPhone.slice(-10)))) {
            $foundThread = $(this);
            return false;
          }
        });

        if ($foundThread && $foundThread.length) {
          $foundThread.trigger("click");
        } else {
          // Open New Chat modal prefilled with this contact
          $("#newChatPhone").val(cleanPhone);
          $("#newChatName").val(name);
          $("#waNewChatModal").removeClass("is-hidden").attr("aria-hidden", "false");
          setTimeout(() => $("#newChatMessage").focus(), 150);
        }
      });

      // Contact card Copy phone button
      $(document).on("click", ".btn-wa-contact-copy", function (e) {
        e.preventDefault();
        const phone = $(this).attr("data-contact-phone") || "";
        if (!phone) return;
        const $btn = $(this);

        const onCopied = function () {
          const origHtml = $btn.html();
          $btn.html('<i class="fi fi-rr-check" style="color:#22C55E;"></i>');
          setTimeout(() => $btn.html(origHtml), 1500);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(phone).then(onCopied).catch(function () {
            const $temp = $("<input>");
            $("body").append($temp);
            $temp.val(phone).select();
            document.execCommand("copy");
            $temp.remove();
            onCopied();
          });
        } else {
          const $temp = $("<input>");
          $("body").append($temp);
          $temp.val(phone).select();
          document.execCommand("copy");
          $temp.remove();
          onCopied();
        }
      });

      // Refresh active chat (delegated)
      $(document).on("click", "#btnRefreshActiveChat", function () {
        if (!self.activeChatId) return;
        const $btn = $(this);
        const $icon = $btn.find("i");
        $icon.addClass("wa-spin");
        $btn.prop("disabled", true);

        // Abort existing message request and reset lock guard
        if (self.currentMessagesXhr) {
          try { self.currentMessagesXhr.abort(); } catch (e) {}
          self.currentMessagesXhr = null;
        }
        self.isLoadingMessages = false;
        self.lastStreamHtml = "";

        self.loadMessages(self.activeChatId, false, function () {
          $icon.removeClass("wa-spin");
          $btn.prop("disabled", false);
        });
        self.loadThreads();
      });

      // Sync chats from WhatsApp into inbox (delegated)
      $(document).on("click", "#btnSyncChatsInbox", function () {
        const $btn = $(this);
        const $icon = $btn.find("i");
        $icon.addClass("wa-spin");
        $btn.prop("disabled", true);

        let syncUrl = "/api/v3/whatsapp/chats/?action=sync";
        if (self.selectedDeviceId && self.selectedDeviceId !== "all") {
          syncUrl += "&device_id=" + encodeURIComponent(self.selectedDeviceId);
        }

        $.ajax({
          url: syncUrl,
          method: "GET",
          dataType: "json",
          timeout: 30000,
          success: function (res) {
            self.loadThreads();
            if (self.activeChatId) {
              self.lastStreamHtml = "";
              self.loadMessages(self.activeChatId, false);
            }
          },
          error: function () {
            self.loadThreads();
            if (self.activeChatId) {
              self.lastStreamHtml = "";
              self.loadMessages(self.activeChatId, false);
            }
          },
          complete: function () {
            $icon.removeClass("wa-spin");
            $btn.prop("disabled", false);
          }
        });
      });

      // Modal: Start New Chat (delegated)
      $(document).on("click", "#btnOpenNewChatModal, #btnEmptyStartChat, #btnSidebarNewChat", function () {
        $("#waNewChatModal").removeClass("is-hidden").addClass("is-active").attr("aria-hidden", "false");
        $("#newChatPhone").focus();
      });

      $(document).on("click", "#btnCloseNewChatModal, #btnCancelNewChat", function () {
        $("#waNewChatModal").addClass("is-hidden").removeClass("is-active").attr("aria-hidden", "true");
        $("#waNewChatForm")[0].reset();
      });

      // Close modal on Escape or clicking outside
      $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
          if ($("#waNewChatModal").hasClass("is-active")) {
            $("#waNewChatModal").addClass("is-hidden").removeClass("is-active").attr("aria-hidden", "true");
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
          $("#waNewChatModal").addClass("is-hidden").removeClass("is-active").attr("aria-hidden", "true");
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
      const $rail = $("#waDevicesRail");
      const $list = $("#waDevicesRailList");
      const $count = $("#waRailDeviceCount");
      const $workspace = $("#waChatWorkspace");

      const devices = self.availableDevices || [];
      const connectedDevices = devices.filter(function (d) { return d.status === "connected"; });

      // If single device is connected (or total devices <= 1), do NOT show the line section
      if (devices.length <= 1 || connectedDevices.length <= 1) {
        if ($rail.length) $rail.addClass("d-none").hide();
        if ($workspace.length) $workspace.removeClass("has-devices-rail");
        const activeDev = connectedDevices.length === 1 ? connectedDevices[0] : (devices.length === 1 ? devices[0] : null);
        if (activeDev && (!self.selectedDeviceId || self.selectedDeviceId === "all")) {
          self.selectedDeviceId = activeDev.device_id;
        }

        // Hide line selection in New Chat modal as well since there is only one line
        const $modalGroup = $("#waNewChatDeviceGroup");
        if ($modalGroup.length) $modalGroup.addClass("d-none").hide();

        return;
      }

      if ($rail.length) $rail.removeClass("d-none").show();
      if ($workspace.length) $workspace.addClass("has-devices-rail");
      if ($count.length) $count.text(devices.length);

      const connectedCount = connectedDevices.length;
      const isAllSelected = !self.selectedDeviceId || self.selectedDeviceId === "all";

      let html = "";
      if (self.isAdmin && devices.length > 1) {
        html += `
          <div class="wa-device-rail-item wa-device-tab ${isAllSelected ? "active" : ""}" data-device-id="all" role="button" tabindex="0" title="View all WhatsApp lines">
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
          <div class="wa-device-rail-item wa-device-tab ${isCur ? "active" : ""}" data-device-id="${d.device_id}" role="button" tabindex="0" title="${self.escapeHtml(d.phone || d.name)}">
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

      if ($list.length && $list.data("last-html") !== html) {
        $list.html(html).data("last-html", html);
      }

      // Populate New Chat Modal device options (Admin only when multiple devices exist and multiple connected)
      const $modalSelect = $("#newChatDeviceSelect");
      const $modalGroup = $("#waNewChatDeviceGroup");
      if ($modalSelect.length) {
        if (self.isAdmin && devices.length > 1 && connectedDevices.length > 1) {
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

      if (this.isAdmin && devices.length > 1 && connectedCount > 1) {
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
        const curDev = (connectedCount === 1) ? devices.find(function (d) { return d.status === "connected"; }) : devices[0];
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
          if (res && typeof res.is_calling_agent !== "undefined") {
            self.isCallingAgent = !!res.is_calling_agent;
          } else if (!self.isAdmin) {
            self.isCallingAgent = true;
          }
          if (res && res.user_role) {
            self.userRole = String(res.user_role).toLowerCase();
          }
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
            self.handlePendingLeadChat([]);
            return;
          }

          let newIncomingDetected = false;
          let latestMsgText = "";
          let latestSenderName = "";
          let latestChatId = null;
          let latestPhone = "";
          let latestAvatar = "";

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

              if (!isActive || document.hidden) {
                newIncomingDetected = true;
                latestMsgText = chat.last_message;
                latestSenderName = chat.name || chat.phone;
                latestChatId = chat.chat_id;
                latestPhone = chat.phone;
                latestAvatar = chat.avatar || "";
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
            const displayName = chat.name || chat.phone;
            const phone = chat.phone || "";
            const cleanPhoneDigits = self.cleanDigits(phone);
            const cleanNameDigits = self.cleanDigits(displayName);
            const hasDistinctName = displayName && phone && cleanNameDigits !== cleanPhoneDigits && !displayName.startsWith("+") && !displayName.toLowerCase().includes("blumox");

            const initials = hasDistinctName ? self.getInitials(displayName) : "";
            const avatarContent = chat.avatar
              ? `<img src="${self.escapeHtml(chat.avatar)}" alt="" class="wa-avatar-img" />`
              : (initials || '<i class="fi fi-rr-user"></i>');

            const devInfo = chat.device || {};
            const devPhone = devInfo.phone || "";
            const agName = (devInfo.agent_name && devInfo.agent_name !== "Unassigned" && devInfo.agent_name !== "Unassigned Agent") ? devInfo.agent_name : "";

            // User requirement: "For label just show Default Line No. or Assinged agent name."
            let badgeText = "";
            let badgeIcon = "fi-rr-smartphone";
            let badgeClass = "wa-tag-device";
            let badgeTitle = "";

            if (agName) {
              badgeText = agName;
              badgeIcon = "fi-rr-user";
              badgeClass = "wa-tag-agent";
              badgeTitle = "Assigned Agent: " + agName;
            } else if (devPhone) {
              badgeText = devPhone;
              badgeIcon = "fi-rr-smartphone";
              badgeClass = "wa-tag-device";
              badgeTitle = "Line: " + devPhone;
            }

            let deviceTagHtml = "";
            if (badgeText) {
              deviceTagHtml = `
                <div class="wa-thread-device-tag">
                  <span class="${badgeClass}" title="${self.escapeHtml(badgeTitle)}"><i class="fi ${badgeIcon}"></i> ${self.escapeHtml(badgeText)}</span>
                </div>
              `;
            }

            const hasLead = !!(chat.lead && chat.lead.id);

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
            self.showIncomingAlert(latestSenderName, latestMsgText, latestChatId, latestPhone, latestAvatar);
          }
          self.hasInitialLoaded = true;

          if (self.lastThreadsHtml === html && $container.children(".wa-thread-item").length > 0) {
            self.handlePendingLeadChat(res.chats);
            return;
          }
          self.lastThreadsHtml = html;
          $container.html(html);
          self.handlePendingLeadChat(res.chats);

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
      if (!chatId) return;

      // Abort any in-flight messages request from previous chat
      if (this.currentMessagesXhr && typeof this.currentMessagesXhr.abort === "function") {
        try { this.currentMessagesXhr.abort(); } catch (e) {}
        this.currentMessagesXhr = null;
      }
      this.isLoadingMessages = false;

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

      // Clear stream immediately so previous chat messages never linger
      $("#waMessagesStream").html('<div class="wa-loading-threads"><i class="fi fi-rr-spinner wa-spin"></i><span>Loading conversation...</span></div>');

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

      this.currentMessagesXhr = $.ajax({
        url: "/api/v3/whatsapp/messages/?chat_id=" + chatId,
        method: "GET",
        dataType: "json",
        success: function (res) {
          // Strictly ensure user has not switched to another chat while request was in-flight
          if (String(self.activeChatId) !== String(chatId)) {
            return;
          }
          if (!res || res.status !== "success") return;
          if (res.chat && String(res.chat.chat_id) !== String(self.activeChatId)) {
            return;
          }

          const chat = res.chat;
          if (chat) {
            const phone = (chat.chat_phone || "").trim();
            let rawName = (chat.chat_name || "").trim();
            if (rawName.startsWith("WhatsApp (") || rawName.toLowerCase().includes("blumox")) {
              rawName = "";
            }
            const leadName = (chat.enquiry_name || "").trim();
            const cleanLeadName = (leadName.startsWith("WhatsApp (") || leadName.toLowerCase().includes("blumox")) ? "" : leadName;
            
            // Priority: 1. Lead Name, 2. Contact Name, 3. Contact Phone Number
            const displayName = chat.display_name || cleanLeadName || rawName || phone;
            const cleanPhoneDigits = self.cleanDigits(phone);
            const cleanNameDigits = self.cleanDigits(displayName);
            const isNameReal = displayName && cleanNameDigits !== cleanPhoneDigits && !displayName.startsWith("+") && !displayName.toLowerCase().includes("blumox");

            if (isNameReal) {
              $("#waActiveName").text(displayName);
              $("#waActivePhone").text(phone);
              $("#waActivePhoneWrap").removeClass("d-none").show();
              $("#btnCopyActivePhoneName").addClass("d-none").hide();
            } else {
              $("#waActiveName").text(phone || "WhatsApp Contact");
              $("#waActivePhoneWrap").addClass("d-none").hide();
              if (phone) {
                $("#btnCopyActivePhoneName").removeClass("d-none").show();
              } else {
                $("#btnCopyActivePhoneName").addClass("d-none").hide();
              }
            }

            if (chat.chat_avatar) {
              $("#waActiveAvatar").removeClass("is-fallback").html(`<img src="${self.escapeHtml(chat.chat_avatar)}" alt="" class="wa-avatar-img" />`);
            } else if (isNameReal) {
              const initials = self.getInitials(displayName);
              $("#waActiveAvatar").removeClass("is-fallback").html(`<span>${self.escapeHtml(initials)}</span>`);
            } else {
              $("#waActiveAvatar").addClass("is-fallback").html('<i class="fi fi-rr-user"></i>');
            }

            if (chat.chat_lead_id) {
              $("#waActiveLeadBadge").removeClass("d-none").show().attr("title", "CRM Lead #" + chat.chat_lead_id + (chat.enquiry_name ? " (" + chat.enquiry_name + ")" : ""));
              $("#waActiveLeadText").text("Lead #" + chat.chat_lead_id);
              $("#waViewLeadLink").attr("href", "/dashboard/leads/view/?id=" + btoa(chat.chat_lead_id)).removeClass("d-none").show();
            } else {
              $("#waActiveLeadBadge").addClass("d-none").hide();
              $("#waViewLeadLink").addClass("d-none").hide();
            }

            if (chat.device && (chat.device.phone || chat.device.name || chat.device.agent_name)) {
              const devPhone = chat.device.phone || "";
              const agName = (chat.device.agent_name && chat.device.agent_name !== "Unassigned" && chat.device.agent_name !== "Unassigned Agent")
                ? chat.device.agent_name
                : "";

              // For label just show Default Line No. or Assigned agent name
              const badgeText = agName || devPhone;
              const badgeIcon = agName ? "fi-rr-user" : "fi-rr-smartphone";
              const badgeTitle = agName ? "Assigned Agent: " + agName : "Line: " + devPhone;

              let badgeHtml = "";
              if (badgeText) {
                badgeHtml = `
                  <div class="wa-line-pill-icon"><i class="fi ${badgeIcon}"></i></div>
                  <div class="wa-line-pill-content">
                    <span class="wa-line-pill-name" title="${self.escapeHtml(badgeTitle)}">${self.escapeHtml(badgeText)}</span>
                  </div>
                `;
                $("#waActiveDeviceBadge").html(badgeHtml).attr("title", badgeTitle).removeClass("d-none").show();
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
          let lastDateLabel = "";
          const seenMsgKeys = new Set();
          res.messages.forEach(function (msg) {
            const mKey = (msg.wa_id && !msg.wa_id.startsWith("crm_out_"))
              ? msg.wa_id
              : (msg.id ? "id_" + msg.id : (msg.direction + "_" + (msg.text || "") + "_" + (msg.created_at || "")));
            if (seenMsgKeys.has(mKey)) return;
            seenMsgKeys.add(mKey);

            const dateLabel = msg.date_label || msg.date || "";
            if (dateLabel && dateLabel !== lastDateLabel) {
              lastDateLabel = dateLabel;
              html += `<div class="wa-date-divider"><span class="wa-date-divider-pill">${self.escapeHtml(dateLabel)}</span></div>`;
            }

            const isOut = msg.direction === "outgoing";
            let contentHtml = "";
            const trimmedText = (msg.text || "").trim();
            const isContact = msg.type === "contact" || !!msg.contact_data || /^\[Contacts?:\s*.*?\]$/i.test(trimmedText);
            const isLocation = msg.type === "location" || msg.type === "live_location" || !!msg.location_data || (/^\[Location:\s*.*?\]$/i.test(trimmedText) || /^\[Live Location\]$/i.test(trimmedText));
            const isVoice = msg.type === "audio" || msg.type === "voice" || (msg.media_url && msg.media_url.match(/\.(ogg|mp3|m4a|wav|aac|opus)($|\?)/i)) || trimmedText === "[Voice Note]" || trimmedText === "[Audio]";
            const isVideo = msg.type === "video" || (msg.media_url && msg.media_url.match(/\.(mp4|mov|webm|mkv|avi)($|\?)/i)) || trimmedText === "[Video]";
            const isImage = msg.type === "image" || (msg.media_url && msg.media_url.match(/\.(jpeg|jpg|gif|png|webp)($|\?)/i)) || trimmedText === "[Image]" || trimmedText === "[Photo]";
            const isDoc = msg.type === "document" || (msg.media_url && !isVoice && !isVideo && !isImage) || trimmedText === "[Attachment]" || trimmedText === "[Document]";

            let isMedia = false;
            if (isContact) {
              contentHtml = self.renderContactCard(msg);
            } else if (isLocation) {
              contentHtml = self.renderLocationCard(msg);
            } else if (isVoice) {
              isMedia = true;
              contentHtml = msg.media_url ? self.renderVoiceCard(msg) : self.renderPendingVoiceCard(msg);
            } else if (isVideo) {
              isMedia = true;
              contentHtml = msg.media_url ? self.renderVideoCard(msg) : self.renderPendingVideoCard(msg);
            } else if (isImage) {
              isMedia = true;
              contentHtml = msg.media_url ? self.renderImageCard(msg) : self.renderPendingImageCard(msg);
            } else if (isDoc) {
              isMedia = true;
              contentHtml = msg.media_url ? self.renderDocCard(msg) : self.renderPendingDocCard(msg);
            } else if (msg.text) {
              contentHtml = `<div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>`;
            } else {
              contentHtml = `<div class="wa-msg-text"><em>[Message]</em></div>`;
            }

            // Append text caption if media has non-placeholder text
            if (isMedia && msg.text) {
              const skipPlaceholders = ["[Voice Note]", "[Audio]", "[Video]", "[Image]", "[Photo]", "[Media]", "[Attachment]", "[Document]"];
              if (!skipPlaceholders.includes(trimmedText)) {
                contentHtml += `<div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>`;
              }
            }

            html += `
              <div class="wa-msg-bubble ${isOut ? "outgoing" : "incoming"}" data-id="${msg.id}" data-wa-id="${self.escapeHtml(msg.wa_id || '')}">
                ${contentHtml}
                <div class="wa-msg-meta">
                  <span>${self.escapeHtml(msg.time)}</span>
                  ${isOut ? '<span class="wa-ticks"><i class="fi fi-rr-check-double"></i></span>' : ""}
                </div>
              </div>
            `;
          });

          if (self.lastStreamHtml === html && $stream.children(".wa-msg-bubble").length > 0) {
            self.autoFetchPendingImages();
            if (scrollToBottom) {
              $stream.scrollTop($stream[0].scrollHeight);
            }
            return;
          }
          self.lastStreamHtml = html;
          $stream.html(html);

          // Check if any loaded images are already cached in browser
          $stream.find("img.wa-msg-img").each(function () {
            if (this.complete && this.naturalWidth > 0) {
              $(this).addClass("is-ready");
              $(this).siblings(".wa-img-skeleton-loader").remove();
            }
          });

          // Automatically trigger background download for pending images
          self.autoFetchPendingImages();

          if (scrollToBottom) {
            $stream.scrollTop($stream[0].scrollHeight);
            setTimeout(function () {
              if ($stream.length) $stream.scrollTop($stream[0].scrollHeight);
            }, 60);
            setTimeout(function () {
              if ($stream.length) $stream.scrollTop($stream[0].scrollHeight);
            }, 250);
            $stream.find("img.wa-msg-img").one("load", function () {
              if ($stream.length) $stream.scrollTop($stream[0].scrollHeight);
            });
          } else {
            const isScrolledUp = ($stream[0].scrollHeight - $stream.scrollTop()) > ($stream[0].clientHeight + 120);
            if (isScrolledUp) {
              $("#waScrollUnreadDot").removeClass("d-none");
              $("#btnScrollToBottom").removeClass("d-none");
            }
          }
        },
        complete: function () {
          self.currentMessagesXhr = null;
          self.isLoadingMessages = false;
          if (typeof callback === "function") {
            callback();
          }
        }
      });
    },

    getDocInfo: function (url, fallbackText) {
      const cleanUrl = (url || "").split("?")[0];
      let rawFilename = cleanUrl.split("/").pop() || "";
      if (rawFilename.length > 45) {
        rawFilename = rawFilename.substring(0, 42) + "...";
      }

      const extMatch = cleanUrl.match(/\.([a-z0-9]+)$/i);
      const ext = extMatch ? extMatch[1].toLowerCase() : "";

      let docName = (fallbackText && fallbackText !== "[Attachment]" && fallbackText !== "[Document]" && fallbackText !== "[Media]")
        ? fallbackText
        : (rawFilename || "Document");

      let icon = "fi-rr-document";
      let theme = "wa-doc-theme-default";
      let label = (ext ? ext.toUpperCase() : "FILE") + " Document";
      let extPill = ext ? ext.toUpperCase().substring(0, 4) : "DOC";

      switch (ext) {
        case "pdf":
          icon = "fi-rr-file-pdf";
          theme = "wa-doc-theme-pdf";
          label = "PDF Document";
          extPill = "PDF";
          break;
        case "doc":
        case "docx":
        case "rtf":
          icon = "fi-rr-file-word";
          theme = "wa-doc-theme-word";
          label = "Word Document";
          extPill = "DOC";
          break;
        case "xls":
        case "xlsx":
          icon = "fi-rr-file-excel";
          theme = "wa-doc-theme-excel";
          label = "Excel Spreadsheet";
          extPill = "XLS";
          break;
        case "csv":
          icon = "fi-rr-file-csv";
          theme = "wa-doc-theme-excel";
          label = "CSV Spreadsheet";
          extPill = "CSV";
          break;
        case "ppt":
        case "pptx":
          icon = "fi-rr-file-powerpoint";
          theme = "wa-doc-theme-ppt";
          label = "PowerPoint Presentation";
          extPill = "PPT";
          break;
        case "zip":
        case "rar":
        case "7z":
        case "tar":
        case "gz":
          icon = "fi-rr-file-zipper";
          theme = "wa-doc-theme-zip";
          label = "Compressed Archive";
          extPill = ext.toUpperCase();
          break;
        case "mp3":
        case "wav":
        case "ogg":
        case "m4a":
        case "aac":
        case "opus":
          icon = "fi-rr-file-audio";
          theme = "wa-doc-theme-audio";
          label = "Audio File";
          extPill = "AUDIO";
          break;
        case "mp4":
        case "mkv":
        case "mov":
        case "webm":
        case "avi":
          icon = "fi-rr-file-video";
          theme = "wa-doc-theme-video";
          label = "Video File";
          extPill = "VIDEO";
          break;
        case "txt":
        case "log":
          icon = "fi-rr-file-alt";
          theme = "wa-doc-theme-default";
          label = "Text File";
          extPill = "TXT";
          break;
        case "json":
        case "xml":
        case "html":
        case "js":
        case "css":
        case "sql":
          icon = "fi-rr-file-code";
          theme = "wa-doc-theme-default";
          label = "Source Code";
          extPill = ext.toUpperCase();
          break;
      }

      return {
        filename: docName,
        icon: icon,
        theme: theme,
        label: label,
        ext: extPill
      };
    },

    renderContactCard: function (msg) {
      let cData = msg.contact_data || null;
      if (!cData && msg.text) {
        const trimmed = (msg.text || "").trim();
        if (trimmed.startsWith("{") && trimmed.endsWith("}")) {
          try {
            const parsed = JSON.parse(trimmed);
            if (parsed.type === "contact" || parsed.type === "contacts") {
              cData = parsed;
            }
          } catch (e) {}
        }
        if (!cData) {
          const match = trimmed.match(/^\[Contact:\s*(.*?)\]$/i);
          if (match) {
            const name = match[1].trim();
            let phone = "";
            const digits = name.replace(/[^0-9]/g, "");
            if (digits.length >= 10 && digits.length <= 13) {
              phone = "+" + digits;
            }
            cData = { name: name || "Contact", phone: phone, vcard: "" };
          }
        }
      }

      if (!cData) {
        cData = { name: "WhatsApp Contact", phone: "", vcard: "" };
      }

      const name = cData.name || "Contact";
      const phone = cData.phone || "";
      const displayPhone = phone ? phone : "WhatsApp Contact";
      const hasPhone = !!phone;

      return `
        <div class="wa-msg-contact-card">
          <div class="wa-contact-header">
            <div class="wa-contact-avatar-box">
              <svg viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
              </svg>
            </div>
            <div class="wa-contact-details">
              <div class="wa-contact-name" title="${this.escapeHtml(name)}">${this.escapeHtml(name)}</div>
              <div class="wa-contact-phone" title="${this.escapeHtml(displayPhone)}">${this.escapeHtml(displayPhone)}</div>
            </div>
          </div>
          <div class="wa-contact-actions-bar">
            <button type="button" class="btn-wa-contact-chat" data-contact-phone="${this.escapeHtml(phone)}" data-contact-name="${this.escapeHtml(name)}">
              <i class="fi fi-rr-comment-alt-middle"></i>
              <span>Message</span>
            </button>
            ${hasPhone ? `
              <a href="tel:${this.escapeHtml(phone)}" class="btn-wa-contact-sub" title="Call ${this.escapeHtml(phone)}">
                <i class="fi fi-rr-phone-call"></i>
              </a>
              <button type="button" class="btn-wa-contact-sub btn-wa-contact-copy" data-contact-phone="${this.escapeHtml(phone)}" title="Copy Phone Number">
                <i class="fi fi-rr-copy"></i>
              </button>
            ` : ""}
          </div>
        </div>
      `;
    },

    renderLocationCard: function (msg) {
      let lData = msg.location_data || null;
      if (!lData && msg.text) {
        const trimmed = (msg.text || "").trim();
        if (trimmed.startsWith("{") && trimmed.endsWith("}")) {
          try {
            const parsed = JSON.parse(trimmed);
            if (parsed.type === "location" || parsed.type === "live_location") {
              lData = parsed;
            }
          } catch (e) {}
        }
        if (!lData) {
          const match = trimmed.match(/^\[Location:\s*(.*?)\]$/i);
          const liveMatch = trimmed.match(/^\[Live Location\]$/i);
          if (match || liveMatch) {
            const locStr = match ? match[1].trim() : "Live Location";
            lData = {
              name: locStr || "Shared Location",
              address: locStr,
              latitude: 0,
              longitude: 0,
              url: locStr ? "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(locStr) : ""
            };
          }
        }
      }

      if (!lData) {
        lData = { name: "Shared Location", address: "", latitude: 0, longitude: 0, url: "" };
      }

      const name = lData.name || (lData.address ? lData.address : "Shared Location");
      const address = lData.address || (lData.latitude && lData.longitude ? `${Number(lData.latitude).toFixed(5)}, ${Number(lData.longitude).toFixed(5)}` : "");
      let mapUrl = lData.url || "";
      if (!mapUrl) {
        if (lData.latitude && lData.longitude) {
          mapUrl = `https://www.google.com/maps/search/?api=1&query=${lData.latitude},${lData.longitude}`;
        } else if (name && name !== "Shared Location") {
          mapUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(name)}`;
        } else if (address) {
          mapUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
        } else {
          mapUrl = "https://maps.google.com";
        }
      }

      const mediaUrl = msg.media_url || lData.media_url || "";
      const hasPreviewImg = !!mediaUrl && !mediaUrl.endsWith(".pdf");

      return `
        <div class="wa-msg-location-card">
          <a href="${this.escapeHtml(mapUrl)}" target="_blank" rel="noopener noreferrer" class="wa-location-preview-box" title="Open in Google Maps">
            ${hasPreviewImg ? `
              <img src="${this.escapeHtml(mediaUrl)}" class="wa-location-map-img" alt="Map Location" loading="lazy" />
            ` : `
              <div class="wa-location-vector-bg"></div>
              <div class="wa-location-pin-wrap">
                <div class="wa-location-pin-icon">
                  <i class="fi fi-rr-marker"></i>
                </div>
                <div class="wa-location-pin-shadow"></div>
              </div>
            `}
          </a>
          <div class="wa-location-details">
            <div class="wa-location-title" title="${this.escapeHtml(name)}">${this.escapeHtml(name)}</div>
            ${address && address !== name ? `<div class="wa-location-address" title="${this.escapeHtml(address)}">${this.escapeHtml(address)}</div>` : ""}
          </div>
          <div class="wa-location-actions-bar">
            <a href="${this.escapeHtml(mapUrl)}" target="_blank" rel="noopener noreferrer" class="btn-wa-location-open">
              <i class="fi fi-rr-map-marker"></i>
              <span>Open in Google Maps</span>
              <i class="fi fi-rr-arrow-up-right-from-square btn-wa-location-ext-icon"></i>
            </a>
          </div>
        </div>
      `;
    },

    formatAudioTime: function (sec) {
      if (!sec || isNaN(sec) || !isFinite(sec)) return "0:00";
      const m = Math.floor(sec / 60);
      const s = Math.floor(sec % 60);
      return m + ":" + (s < 10 ? "0" : "") + s;
    },

    updateVoiceProgress: function ($player, audio) {
      const self = this;
      if (!audio || !audio.duration) return;
      const ratio = Math.max(0, Math.min(1, audio.currentTime / audio.duration));
      $player.find(".wa-voice-slider").val(ratio * 100);
      $player.find(".wa-voice-time").text(self.formatAudioTime(audio.currentTime));

      const $bars = $player.find(".wa-voice-bar");
      const total = $bars.length;
      const activeIdx = Math.floor(ratio * total);
      $bars.each(function (idx) {
        if (idx <= activeIdx) {
          $(this).addClass("is-played");
        } else {
          $(this).removeClass("is-played");
        }
      });
    },

    renderVoiceCard: function (msg) {
      const self = this;
      const waveHeights = [8, 14, 20, 10, 16, 22, 14, 8, 18, 24, 16, 12, 20, 26, 18, 10, 14, 22, 16, 8, 12, 18, 24, 14, 10, 16, 12, 8];
      let barsHtml = "";
      for (let i = 0; i < waveHeights.length; i++) {
        barsHtml += `<span class="wa-voice-bar" style="height:${waveHeights[i]}px" data-idx="${i}"></span>`;
      }

      return `
        <div class="wa-msg-voice-card" data-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-voice-player">
            <button type="button" class="wa-voice-play-btn" title="Play voice note">
              <i class="fi fi-rr-play"></i>
            </button>
            <div class="wa-voice-track-wrap">
              <div class="wa-voice-waveform" title="Seek position">
                ${barsHtml}
              </div>
              <input type="range" class="wa-voice-slider" min="0" max="100" value="0" step="0.1" aria-label="Seek voice note" />
              <div class="wa-voice-meta-row">
                <span class="wa-voice-time">0:00</span>
                <button type="button" class="wa-voice-speed-pill" title="Playback speed">1x</button>
              </div>
            </div>
            <div class="wa-voice-avatar-badge" title="Voice Note">
              <i class="fi fi-rr-microphone"></i>
            </div>
            <audio class="wa-voice-audio-el" src="${self.escapeHtml(msg.media_url)}" preload="metadata"></audio>
          </div>
        </div>
      `;
    },

    renderPendingVoiceCard: function (msg) {
      const self = this;
      const waveHeights = [8, 14, 20, 10, 16, 22, 14, 8, 18, 24, 16, 12, 20, 26, 18, 10, 14, 22, 16, 8, 12, 18, 24, 14, 10, 16, 12, 8];
      let barsHtml = "";
      for (let i = 0; i < waveHeights.length; i++) {
        barsHtml += `<span class="wa-voice-bar" style="height:${waveHeights[i]}px"></span>`;
      }

      return `
        <div class="wa-msg-voice-card wa-auto-fetch-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-voice-player is-pending" title="Click to download voice note">
            <button type="button" class="wa-voice-play-btn is-download" title="Download voice note">
              <i class="fi fi-rr-arrow-down-to-bracket"></i>
            </button>
            <div class="wa-voice-track-wrap">
              <div class="wa-voice-waveform">
                ${barsHtml}
              </div>
              <div class="wa-voice-meta-row">
                <span class="wa-voice-pending-label">Voice Note · Click to load</span>
                <span class="wa-voice-time"><i class="fi fi-rr-download"></i></span>
              </div>
            </div>
            <div class="wa-voice-avatar-badge" title="Voice Note">
              <i class="fi fi-rr-microphone"></i>
            </div>
          </div>
        </div>
      `;
    },

    renderVideoCard: function (msg) {
      const self = this;
      return `
        <div class="wa-msg-video-media" data-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-video-box">
            <video class="wa-msg-video" src="${self.escapeHtml(msg.media_url)}" controls playsinline preload="metadata"></video>
          </div>
        </div>
      `;
    },

    renderPendingVideoCard: function (msg) {
      const self = this;
      return `
        <div class="wa-msg-video-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-video-box wa-video-skeleton-loader wa-auto-fetch-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}" title="Click to download video">
            <i class="fi fi-rr-play-alt"></i>
            <span>Video · Click to download</span>
          </div>
        </div>
      `;
    },

    renderImageCard: function (msg) {
      const self = this;
      return `
        <div class="wa-msg-media" data-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-img-box">
            <div class="wa-img-skeleton-loader">
              <i class="fi fi-rr-spinner wa-spin"></i>
              <span>Loading photo...</span>
            </div>
            <img src="${self.escapeHtml(msg.media_url)}" class="wa-msg-img" alt="Photo" loading="lazy" />
          </div>
        </div>
      `;
    },

    renderPendingImageCard: function (msg) {
      const self = this;
      return `
        <div class="wa-msg-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-img-box wa-auto-fetch-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
            <div class="wa-img-skeleton-loader">
              <i class="fi fi-rr-spinner wa-spin"></i>
              <span>Loading photo...</span>
            </div>
          </div>
        </div>
      `;
    },

    renderDocCard: function (msg) {
      const self = this;
      const docInfo = self.getDocInfo(msg.media_url, msg.text);
      return `
        <div class="wa-msg-media" data-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-doc-card ${docInfo.theme}">
            <a href="${self.escapeHtml(msg.media_url)}" target="_blank" rel="noopener noreferrer" class="wa-doc-main-link" download="${self.escapeHtml(docInfo.filename)}">
              <div class="wa-doc-icon-box">
                <i class="fi ${docInfo.icon}"></i>
                <span class="wa-doc-pill-ext">${self.escapeHtml(docInfo.ext)}</span>
              </div>
              <div class="wa-doc-details">
                <div class="wa-doc-name" title="${self.escapeHtml(docInfo.filename)}">${self.escapeHtml(docInfo.filename)}</div>
                <div class="wa-doc-meta-row">
                  <span>${self.escapeHtml(docInfo.label)}</span>
                  <span class="wa-doc-action-btn" title="Download"><i class="fi fi-rr-download"></i></span>
                </div>
              </div>
            </a>
          </div>
        </div>
      `;
    },

    renderPendingDocCard: function (msg) {
      const self = this;
      return `
        <div class="wa-msg-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
          <div class="wa-msg-doc-card wa-doc-theme-default wa-media-doc-card-pending wa-auto-fetch-media" data-retry-wa-id="${self.escapeHtml(msg.wa_id || '')}">
            <div class="wa-doc-main-link">
              <div class="wa-doc-icon-box">
                <i class="fi fi-rr-file-download"></i>
                <span class="wa-doc-pill-ext">DOC</span>
              </div>
              <div class="wa-doc-details">
                <div class="wa-doc-name">Attachment</div>
                <div class="wa-doc-meta-row">
                  <span>Click to load document</span>
                  <span class="wa-doc-action-btn"><i class="fi fi-rr-download"></i></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    },

    fetchSingleMedia: function (waId, $targetEl) {
      const self = this;
      if (!waId) return;

      $.ajax({
        url: "/api/v3/whatsapp/messages/?action=retry_media&msg_id=" + encodeURIComponent(waId),
        type: "GET",
        timeout: 15000,
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.media_url) {
            let type = res.type || "";
            const cleanUrl = res.media_url.toLowerCase();
            if (type === "audio" || cleanUrl.match(/\.(ogg|mp3|m4a|wav|aac|opus)($|\?)/i)) {
              type = "audio";
            } else if (type === "video" || cleanUrl.match(/\.(mp4|mov|webm|mkv|avi)($|\?)/i)) {
              type = "video";
            } else if (type === "image" || cleanUrl.match(/\.(jpeg|jpg|png|webp|gif)($|\?)/i)) {
              type = "image";
            } else {
              type = "document";
            }

            const dummyMsg = {
              wa_id: waId,
              media_url: res.media_url,
              type: type,
              text: ""
            };

            let newHtml = "";
            if (type === "audio") {
              newHtml = self.renderVoiceCard(dummyMsg);
            } else if (type === "video") {
              newHtml = self.renderVideoCard(dummyMsg);
            } else if (type === "image") {
              newHtml = self.renderImageCard(dummyMsg);
            } else {
              newHtml = self.renderDocCard(dummyMsg);
            }

            if ($targetEl && $targetEl.length) {
              $targetEl.replaceWith(newHtml);
            }
          } else {
            if ($targetEl && $targetEl.length) {
              $targetEl.removeClass("is-fetching");
              $targetEl.html(`
                <div class="wa-img-error-fallback" data-retry-wa-id="${self.escapeHtml(waId)}">
                  <i class="fi fi-rr-picture"></i>
                  <span>Photo unavailable · Click to retry</span>
                </div>
              `);
            }
          }
        },
        error: function () {
          if ($targetEl && $targetEl.length) {
            $targetEl.removeClass("is-fetching");
            $targetEl.html(`
              <div class="wa-img-error-fallback" data-retry-wa-id="${self.escapeHtml(waId)}">
                <i class="fi fi-rr-refresh"></i>
                <span>Failed to load photo · Click to retry</span>
              </div>
            `);
          }
        }
      });
    },

    autoFetchPendingMedia: function () {
      const self = this;
      const $pending = $("#waMessagesStream").find(".wa-auto-fetch-media[data-retry-wa-id]:not(.is-fetching)");
      if (!$pending.length) return;

      $pending.each(function () {
        const $box = $(this);
        const waId = $box.attr("data-retry-wa-id");
        if (!waId) return;

        $box.addClass("is-fetching");
        self.fetchSingleMedia(waId, $box);
      });
    },

    autoFetchPendingImages: function () {
      this.autoFetchPendingMedia();
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
          self.showAlert(res.msg || "Failed to send WhatsApp message.", "Message Error", "error");
        }
      };

      ajaxOptions.error = function () {
        self.isSending = false;
        $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");
        self.showAlert("Network error sending WhatsApp message.", "Connection Error", "error");
      };

      $.ajax(ajaxOptions);
    },

    submitNewChat: function () {
      const self = this;
      let phone = $("#newChatPhone").val().trim();
      const name = $("#newChatName").val().trim();
      const message = $("#newChatMessage").val().trim();

      if (!phone || !message) {
        self.showAlert("Please enter customer phone and initial message.", "Required Fields", "warning");
        return;
      }

      let digits = phone.replace(/[^0-9]/g, "");
      if (digits.startsWith("0")) digits = digits.replace(/^0+/, "");
      if (digits.length === 10) digits = "91" + digits;
      if (digits.length < 8) {
        self.showAlert("Please enter a valid mobile number (at least 8 digits).", "Invalid Phone Number", "warning");
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
                  $("#waNewChatModal").addClass("is-hidden").removeClass("is-active").attr("aria-hidden", "true");
                  $("#waNewChatForm")[0].reset();
                  self.loadThreads();
                  self.selectChat(chatId, targetPhone);
                } else {
                  self.showAlert(sendRes.msg || "Failed to send WhatsApp message.", "Message Error", "error");
                }
              },
              error: function (xhr) {
                $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
                const err = xhr.responseJSON ? (xhr.responseJSON.msg || xhr.responseJSON.message) : "Failed to dispatch message to WhatsApp.";
                self.showAlert(err, "Dispatch Failed", "error");
              }
            });
          } else {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
            self.showAlert(chatRes.msg || "Failed to initiate chat.", "Conversation Error", "error");
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-paper-plane"></i> Send &amp; Open Chat');
          const err = xhr.responseJSON ? (xhr.responseJSON.msg || xhr.responseJSON.message) : "Network error initiating chat.";
          self.showAlert(err, "Network Error", "error");
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

        const isHidden = document.hidden;
        const pollDelay = isHidden ? 6000 : (self.activeChatId ? 3000 : 7000);

        // When tab is visible and chat is active, poll messages silently
        if (!isHidden && self.activeChatId && !self.isLoadingMessages) {
          const $stream = $("#waMessagesStream");
          const isAtBottom = $stream.length ? ($stream[0].scrollHeight - $stream.scrollTop() <= $stream.outerHeight() + 80) : true;
          self.loadMessages(self.activeChatId, isAtBottom);
        }

        // Always poll threads (even when tab is hidden) so incoming messages trigger browser push notifications
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
      if (!str) return "";
      const clean = String(str).trim();
      if (!/[a-zA-Z]/.test(clean)) {
        return "";
      }
      const parts = clean.split(/\s+/).filter(Boolean);
      if (parts.length >= 2 && parts[0] && parts[1]) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }
      return clean.substring(0, 2).toUpperCase();
    },

    showAlert: function (message, title, icon) {
      title = title || "WhatsApp CRM";
      icon = icon || "info";
      const swalInstance = (typeof Swal !== "undefined" && typeof Swal.fire === "function")
        ? Swal
        : ((typeof window.Swal !== "undefined" && typeof window.Swal.fire === "function") ? window.Swal : null);
      if (swalInstance) {
        swalInstance.fire({
          title: title,
          text: message,
          icon: icon,
          confirmButtonColor: "#008060"
        });
      } else if (typeof swal === "function") {
        swal(title, message, icon);
      } else {
        alert((title ? title + "\n" : "") + message);
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

    showIncomingAlert: function (sender, text, chatId, phone, avatar) {
      const self = this;
      $(".wa-incoming-alert-toast").remove();

      let previewText = text || "New message received";
      if (previewText === "[Image]" || previewText === "[Photo]") {
        previewText = "📷 Photo";
      } else if (previewText === "[Media]") {
        previewText = "📎 Media";
      }

      const notifTitle = sender || phone || "WhatsApp Message";
      const notifIcon = avatar || "/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg";

      if ("Notification" in window) {
        if (Notification.permission === "granted") {
          try {
            const notif = new Notification(notifTitle, {
              body: previewText,
              icon: notifIcon,
              badge: notifIcon,
              tag: "wa-chat-" + (chatId || "incoming"),
              renotify: true
            });

            notif.onclick = function (e) {
              e.preventDefault();
              window.focus();
              if (chatId) {
                self.selectChat(chatId, phone);
              }
              try { notif.close(); } catch (err) {}
            };
          } catch (e) {
            console.warn("Browser Notification error:", e);
          }
        } else if (Notification.permission === "default") {
          self.requestNotificationPermission(false, function (permission) {
            if (permission === "granted") {
              try {
                const notif = new Notification(notifTitle, {
                  body: previewText,
                  icon: notifIcon,
                  badge: notifIcon,
                  tag: "wa-chat-" + (chatId || "incoming"),
                  renotify: true
                });
                notif.onclick = function (e) {
                  e.preventDefault();
                  window.focus();
                  if (chatId) {
                    self.selectChat(chatId, phone);
                  }
                  try { notif.close(); } catch (err) {}
                };
              } catch (err) {}
            }
          });
        }
      }
    },

    initNotificationPermission: function () {
      this.updateNotificationButtonState();
    },

    requestNotificationPermission: function (fromUserClick, callback) {
      const self = this;
      if (!("Notification" in window)) {
        if (typeof callback === "function") callback("unsupported");
        return;
      }

      if (Notification.permission === "default") {
        try {
          Notification.requestPermission().then(function (permission) {
            self.updateNotificationButtonState();
            if (permission === "granted" && fromUserClick) {
              try {
                const welcomeNotif = new Notification("WhatsApp Notifications Enabled", {
                  body: "You will now receive desktop notifications for incoming messages.",
                  icon: "/app/dashboard/v3/default/desktop/assets/images/integration/whatsapp.svg"
                });
                welcomeNotif.onclick = function () {
                  window.focus();
                  try { welcomeNotif.close(); } catch (e) {}
                };
              } catch (err) {}
            }
            if (typeof callback === "function") callback(permission);
          }).catch(function () {
            if (typeof callback === "function") callback(Notification.permission);
          });
        } catch (e) {
          Notification.requestPermission(function (permission) {
            self.updateNotificationButtonState();
            if (typeof callback === "function") callback(permission);
          });
        }
      } else {
        self.updateNotificationButtonState();
        if (typeof callback === "function") callback(Notification.permission);
      }
    },

    updateNotificationButtonState: function () {
      if (!("Notification" in window)) {
        $("#btnEnableNotifications").addClass("d-none");
        return;
      }
      if (Notification.permission === "default") {
        $("#btnEnableNotifications").removeClass("d-none");
      } else {
        $("#btnEnableNotifications").addClass("d-none");
      }
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
        self.showAlert("Please select an image file (JPG, PNG, GIF, WebP).", "Invalid File", "warning");
        return;
      }
      if (file.size > 20 * 1024 * 1024) {
        self.showAlert("Image size exceeds maximum limit of 20MB.", "File Too Large", "warning");
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
    },

    handlePendingLeadChat: function (chats) {
      if (!this.pendingLeadPhone && !this.pendingChatId) return;
      const self = this;
      const targetPhone = this.pendingLeadPhone;
      const targetChatId = this.pendingChatId;
      const targetName = this.pendingLeadName || "";
      const targetLeadId = this.pendingLeadId || "";
      const targetDigits = targetPhone ? this.cleanDigits(targetPhone) : "";

      // Reset pending state
      this.pendingLeadPhone = null;
      this.pendingChatId = null;
      this.pendingLeadName = null;
      this.pendingLeadId = null;

      // Clean browser address bar query string without reloading
      if (window.history && window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
      }

      // Check if thread exists by chat_id or phone
      let matchedChat = null;
      if (Array.isArray(chats)) {
        if (targetChatId) {
          matchedChat = chats.find(function (c) {
            return String(c.chat_id) === String(targetChatId);
          });
        }
        if (!matchedChat && targetDigits) {
          matchedChat = chats.find(function (c) {
            return self.cleanDigits(c.phone) === targetDigits;
          });
        }
      }

      if (matchedChat) {
        // Open the existing conversation
        self.selectChat(matchedChat.chat_id, matchedChat.phone);
        setTimeout(function () {
          $("#waMessageInput").focus();
        }, 350);
      } else if (targetChatId) {
        // Direct selection by ID
        self.selectChat(targetChatId, targetPhone || "");
        setTimeout(function () {
          $("#waMessageInput").focus();
        }, 350);
      } else {
        // No conversation exists yet: automatically open New Chat modal prefilled with this lead!
        $("#waNewChatModal").removeClass("is-hidden").addClass("is-active").attr("aria-hidden", "false");
        let formattedPhone = (targetPhone || "").trim();
        let onlyDigits = formattedPhone.replace(/[^0-9]/g, "");
        if (onlyDigits.length === 10) formattedPhone = "+91" + onlyDigits;
        else if (!formattedPhone.startsWith("+")) formattedPhone = "+" + onlyDigits;

        $("#newChatPhone").val(formattedPhone);
        $("#newChatName").val(targetName);
        if (!$("#newChatMessage").val()) {
          $("#newChatMessage").val(targetName ? ("Hello " + targetName + ", ") : "Hello, ");
        }
        setTimeout(function () {
          $("#newChatMessage").focus();
        }, 350);
      }
    }
  };

  /* ==========================================================================
     V3 Global WhatsApp Lead Chat Helper
     ========================================================================== */
  window.V3WhatsAppLeadChat = {
    cachedStatus: null,
    cacheTime: 0,
    CACHE_TTL: 10000, // 10 seconds cache

    checkStatus: function (callback, force, queryParams) {
      const now = Date.now();
      const hasSpecificParams = !!(queryParams && (queryParams.phone || queryParams.lead_id));
      if (!force && !hasSpecificParams && this.cachedStatus !== null && (now - this.cacheTime < this.CACHE_TTL)) {
        if (typeof callback === "function") {
          callback(this.cachedStatus.isConnected, this.cachedStatus.res);
        }
        return;
      }
      const self = this;
      let url = "/api/v3/integration/whatsapp_web/?action=check_status";
      if (queryParams) {
        if (queryParams.phone) url += "&phone=" + encodeURIComponent(queryParams.phone);
        if (queryParams.name) url += "&name=" + encodeURIComponent(queryParams.name);
        if (queryParams.lead_id) url += "&lead_id=" + encodeURIComponent(queryParams.lead_id);
        if (queryParams.destination) url += "&destination=" + encodeURIComponent(queryParams.destination);
      }
      $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (res) {
          const isConnected = res && res.status === "success" && (res.connection_status === "connected" || res.is_device_linked === true);
          if (!hasSpecificParams) {
            self.cachedStatus = { isConnected: isConnected, res: res };
            self.cacheTime = Date.now();
          }
          if (typeof callback === "function") {
            callback(isConnected, res);
          }
        },
        error: function () {
          if (!hasSpecificParams) {
            self.cachedStatus = { isConnected: false, res: null };
            self.cacheTime = Date.now();
          }
          if (typeof callback === "function") {
            callback(false, null);
          }
        }
      });
    },

    open: function (opts) {
      opts = opts || {};
      const self = this;
      const phone = (opts.phone || "").trim();
      const name = (opts.name || "").trim();
      const leadId = opts.leadId || "";
      const chatId = opts.chatId || "";
      const destination = opts.destination || "";
      const $btn = opts.$btn || null;

      if (!phone) {
        self.showAlert("No WhatsApp phone number available for this contact.", "Missing Phone", "info");
        return;
      }

      if ($btn && $btn.length) {
        $btn.addClass("is-checking-wa");
      }

      this.checkStatus(function (isConnected, res) {
        if ($btn && $btn.length) {
          $btn.removeClass("is-checking-wa");
        }

        const isAdmin = !!(res && (res.is_admin === true || res.user_role === "administrator" || res.user_role === "admin"));
        const hasAssigned = !!(res && res.has_assigned_device);
        const canUseCrm = isAdmin || hasAssigned;
        const isWame = res && typeof res.is_wame !== "undefined" ? !!res.is_wame : !canUseCrm;

        // If WhatsApp is NOT assigned to agent and agent is not Admin: open direct wa.me link with predefined intro message
        if (isWame || !canUseCrm) {
          if (res && res.wa_me_url) {
            window.open(res.wa_me_url, "_blank");
            return;
          }
          let onlyDigits = phone.replace(/[^0-9]/g, "");
          if (onlyDigits.length === 10) onlyDigits = "91" + onlyDigits;
          const currentUserName = (res && res.user_name) || $("#leadViewApp").data("user-name") || $("#leadsListApp").data("user-name") || $("#kanbanApp").data("user-name") || "Our Team";
          const siteName = (res && res.site_name) || $("#leadViewApp").data("site-name") || $("#leadsListApp").data("site-name") || $("#kanbanApp").data("site-name") || "Our Team";
          let introMsg = (res && res.intro_message) || "";
          if (!introMsg) {
            introMsg = `Hello ${name || "there"}! Welcome to *${siteName}*.\n\nI am *${currentUserName}*, your dedicated Travel Specialist.\n\nHow can I assist you today?`;
          }
          window.open("https://wa.me/" + onlyDigits + "?text=" + encodeURIComponent(introMsg), "_blank");
          return;
        }

        if (isConnected) {
          // Open connected device chat inbox: open existing chat if available, else new chat
          let targetUrl = "";
          const resolvedChatId = chatId || (res && res.existing_chat_id) || "";
          if (resolvedChatId) {
            targetUrl = "/dashboard/whatsapp/?phone=" + encodeURIComponent(phone) + "&chat_id=" + encodeURIComponent(resolvedChatId);
          } else if (res && res.has_existing_chat && res.existing_chat_id) {
            targetUrl = "/dashboard/whatsapp/?phone=" + encodeURIComponent(phone) + "&chat_id=" + encodeURIComponent(res.existing_chat_id);
          } else if (res && res.crm_chat_url) {
            targetUrl = res.crm_chat_url;
          } else {
            targetUrl = "/dashboard/whatsapp/?phone=" + encodeURIComponent(phone) +
              (name ? "&name=" + encodeURIComponent(name) : "") +
              (leadId ? "&lead_id=" + encodeURIComponent(leadId) : "") +
              "&new_chat=1";
          }
          self.navigateTo(targetUrl);
        } else {
          // Device is not connected: show popup
          self.showConnectPrompt(phone, name);
        }
      }, false, {
        phone: phone,
        name: name,
        lead_id: leadId,
        destination: destination
      });
    },

    showConnectPrompt: function (phone, name) {
      this.ensureModalHtml();
      const $modal = $("#modalWaConnectDevicePrompt");
      if (!$modal.length) return;

      if (name || phone) {
        $("#waConnectLeadName").text(name || "Customer");
        $("#waConnectLeadPhone").text(phone || "");
        $("#waConnectLeadPreview").removeClass("d-none");
      } else {
        $("#waConnectLeadPreview").addClass("d-none");
      }

      $modal.addClass("is-active").attr("aria-hidden", "false");
    },

    closeConnectPrompt: function () {
      $("#modalWaConnectDevicePrompt").removeClass("is-active").attr("aria-hidden", "true");
    },

    navigateTo: function (url) {
      const link = document.createElement("a");
      link.href = url;
      document.body.appendChild(link);
      link.click();
      link.remove();
    },

    ensureModalHtml: function () {
      if ($("#modalWaConnectDevicePrompt").length > 0) return;
      const html = `
        <div class="v3-modal-overlay wa-connect-prompt-modal-overlay" id="modalWaConnectDevicePrompt" aria-hidden="true" role="dialog" aria-labelledby="waConnectPromptModalTitle">
          <div class="v3-modal-dialog v3-modal-dialog-md wa-connect-prompt-dialog">
            <div class="v3-modal-header">
              <div class="v3-modal-header-left">
                <div class="v3-modal-header-icon-box wa-connect-header-icon">
                  <i class="fi fi-brands-whatsapp"></i>
                </div>
                <div class="v3-modal-title-wrap">
                  <h4 class="v3-modal-title" id="waConnectPromptModalTitle">Connect Device to Start WhatsApp Msg</h4>
                  <span class="v3-modal-subtitle">WhatsApp CRM Live Messaging</span>
                </div>
              </div>
              <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" aria-label="Close dialog" title="Close"><i class="fi fi-rr-cross"></i></button>
            </div>
            <div class="v3-modal-body wa-connect-prompt-body">
              <div class="wa-connect-illustration-wrap">
                <div class="wa-connect-pulse-ring">
                  <div class="wa-connect-phone-icon">
                    <i class="fi fi-rr-smartphone"></i>
                    <span class="wa-connect-status-indicator">
                      <i class="fi fi-rr-cross"></i>
                    </span>
                  </div>
                </div>
              </div>
              <h3 class="wa-connect-prompt-headline">No WhatsApp Device Connected</h3>
              <p class="wa-connect-prompt-desc">
                Please connect your WhatsApp device to start chatting with leads directly from your CRM inbox.
              </p>
              <div class="wa-connect-lead-preview d-none" id="waConnectLeadPreview">
                <div class="wa-connect-lead-info">
                  <span class="wa-connect-lead-label">Lead Contact:</span>
                  <strong class="wa-connect-lead-name" id="waConnectLeadName">-</strong>
                  <span class="wa-connect-lead-phone" id="waConnectLeadPhone">-</span>
                </div>
              </div>
              <div class="wa-connect-info-callout">
                <i class="fi fi-rr-info"></i>
                <span>Linking your device allows real-time incoming messages, live team replies, and full conversation history inside CRM.</span>
              </div>
            </div>
            <div class="v3-modal-footer wa-connect-prompt-footer">
              <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
              <a href="/dashboard/integration/whatsapp_web/" class="btn-v3-action btn-wa-connect-primary" id="btnGoToConnectWaDevice">
                <i class="fi fi-rr-qr-scan"></i> <span>Connect WhatsApp Device</span>
              </a>
            </div>
          </div>
        </div>
      `;
      $("body").append(html);
    }
  };

  // Delegated click listener for WhatsApp chat buttons across all V3 pages
  $(document).on("click", ".lead-wa-btn-vibrant, .v3-btn-contact-action.wa, .btn-fast-tile.tile-wa, .kanban-cta-btn.cta-whatsapp, [data-action='whatsapp-chat']", function (e) {
    const $btn = $(this);
    const href = $btn.attr("href") || "";

    // If marked as wa.me link or starts with https://wa.me, let the browser handle standard link opening
    if ($btn.data("is-wame") === "1" || $btn.data("is-wame") === 1 || href.startsWith("https://wa.me") || href.startsWith("http://wa.me")) {
      return;
    }

    e.preventDefault();
    let phone = $btn.data("phone") || $btn.attr("data-phone") || "";
    let name = $btn.data("name") || $btn.attr("data-name") || "";
    let leadId = $btn.data("lead-id") || $btn.attr("data-lead-id") || "";
    let chatId = $btn.data("chat-id") || $btn.attr("data-chat-id") || "";
    let destination = $btn.data("destination") || $btn.attr("data-destination") || "";

    if (name && typeof name === "string" && name.includes("%")) {
      try { name = decodeURIComponent(name); } catch (_) {}
    }
    if (destination && typeof destination === "string" && destination.includes("%")) {
      try { destination = decodeURIComponent(destination); } catch (_) {}
    }

    if (!phone) {
      const match = href.match(/wa\.me\/([0-9+]+)/) || href.match(/phone=([0-9+]+)/);
      if (match && match[1]) {
        phone = match[1];
      }
    }

    window.V3WhatsAppLeadChat.open({
      phone: phone,
      name: name,
      leadId: leadId,
      chatId: chatId,
      destination: destination,
      $btn: $btn
    });
  });

  // Modal events for Connect Device prompt
  $(document).on("click", "#modalWaConnectDevicePrompt .v3-modal-close-trigger", function () {
    window.V3WhatsAppLeadChat.closeConnectPrompt();
  });
  $(document).on("click", "#modalWaConnectDevicePrompt", function (e) {
    if (e.target === this) {
      window.V3WhatsAppLeadChat.closeConnectPrompt();
    }
  });
  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $("#modalWaConnectDevicePrompt").hasClass("is-active")) {
      window.V3WhatsAppLeadChat.closeConnectPrompt();
    }
  });

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

