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
    pollTimer: null,
    isSending: false,

    init: function () {
      const $app = $("#whatsappCrmApp");
      if (!$app.length) return;

      this.bindEvents();
      this.checkConnectionStatus();
      this.loadThreads();
      this.startPolling();
    },

    bindEvents: function () {
      const self = this;

      // Filter tabs
      $(".wa-filter-pill").on("click", function () {
        $(".wa-filter-pill").removeClass("active");
        $(this).addClass("active");
        self.currentFilter = $(this).data("filter") || "all";
        self.loadThreads();
      });

      // Search input
      let searchTimeout = null;
      $("#waThreadSearch").on("input", function () {
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

      $("#btnClearSearch").on("click", function () {
        $("#waThreadSearch").val("").trigger("input");
      });

      // Select thread
      $(document).on("click", ".wa-thread-item", function () {
        const chatId = $(this).data("chat-id");
        const phone = $(this).data("phone");
        self.selectChat(chatId, phone);
      });

      // Composer submit
      $("#waMessageSendForm").on("submit", function (e) {
        e.preventDefault();
        self.sendMessage();
      });

      // Composer keypress Enter to send (Shift+Enter for newline)
      $("#waComposerText").on("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          self.sendMessage();
        }
      });

      // Quick response chips
      $(".wa-quick-chip").on("click", function () {
        const text = $(this).data("text") || "";
        const $input = $("#waComposerText");
        const current = $input.val().trim();
        $input.val(current ? current + " " + text : text).focus();
      });

      // Refresh active chat
      $("#btnRefreshActiveChat").on("click", function () {
        if (self.activeChatId) {
          self.loadMessages(self.activeChatId, false);
        }
      });

      // Sync chats from WhatsApp into inbox
      $("#btnSyncChatsInbox").on("click", function () {
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

      // Modal: Start New Chat
      $("#btnOpenNewChatModal, #btnEmptyStartChat, #btnSidebarNewChat").on("click", function () {
        $("#waNewChatModal").addClass("is-active").attr("aria-hidden", "false");
        $("#newChatPhone").focus();
      });

      $("#btnCloseNewChatModal, #btnCancelNewChat").on("click", function () {
        $("#waNewChatModal").removeClass("is-active").attr("aria-hidden", "true");
        $("#waNewChatForm")[0].reset();
      });

      // Submit New Chat
      $("#waNewChatForm").on("submit", function (e) {
        e.preventDefault();
        self.submitNewChat();
      });
    },

    checkConnectionStatus: function () {
      $.ajax({
        url: "/api/v3/integration/whatsapp_web/?action=check_status",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            const isConnected = res.connection_status === "connected";
            const $badge = $("#waHeaderStatusBadge");
            const $text = $("#waHeaderStatusText");

            if (isConnected) {
              $badge.addClass("is-connected");
              $text.text("Linked: " + (res.phone || "Connected"));
            } else {
              $badge.removeClass("is-connected");
              $text.text(res.is_active ? "Device Not Linked (Scan QR)" : "WhatsApp Web Disabled");
            }
          }
        }
      });
    },

    loadThreads: function () {
      const self = this;
      const params = {
        filter: this.currentFilter,
        q: this.searchQuery
      };

      $.ajax({
        url: "/api/v3/whatsapp/chats/",
        method: "GET",
        data: params,
        dataType: "json",
        success: function (res) {
          const $container = $("#waThreadsContainer");
          if (!res || res.status !== "success" || !Array.isArray(res.chats)) {
            $container.html('<div class="wa-loading-threads"><span>Failed to load chats.</span></div>');
            return;
          }

          if (res.chats.length === 0) {
            $container.html('<div class="wa-loading-threads"><i class="fi fi-rr-comment-slash"></i><span>No conversations found.</span></div>');
            return;
          }

          let html = "";
          res.chats.forEach(function (chat) {
            const isActive = self.activeChatId === chat.chat_id;
            const hasLead = !!chat.lead;
            const avatarText = self.getInitials(chat.name || chat.phone);

            html += `
              <div class="wa-thread-item ${isActive ? "active" : ""}" data-chat-id="${chat.chat_id}" data-phone="${self.escapeHtml(chat.phone)}">
                <div class="wa-thread-avatar ${hasLead ? "has-lead" : ""}">${avatarText}</div>
                <div class="wa-thread-body">
                  <div class="wa-thread-top">
                    <span class="wa-thread-name">${self.escapeHtml(chat.name)}</span>
                    <span class="wa-thread-time">${self.escapeHtml(chat.time_display)}</span>
                  </div>
                  <div class="wa-thread-bottom">
                    <p class="wa-thread-preview">${self.escapeHtml(chat.last_message || "No messages yet")}</p>
                    ${chat.unread_count > 0 ? `<span class="wa-unread-badge">${chat.unread_count}</span>` : ""}
                  </div>
                </div>
              </div>
            `;
          });

          $container.html(html);
        }
      });
    },

    selectChat: function (chatId, phone) {
      this.activeChatId = chatId;
      this.activePhone = phone;

      $(".wa-thread-item").removeClass("active");
      $(`.wa-thread-item[data-chat-id="${chatId}"]`).addClass("active");
      $(`.wa-thread-item[data-chat-id="${chatId}"]`).find(".wa-unread-badge").remove();

      $("#waEmptyState").hide();
      $("#waActiveChatWrapper").addClass("is-open");

      this.loadMessages(chatId, true);
    },

    loadMessages: function (chatId, scrollToBottom) {
      const self = this;
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
            $("#waActiveAvatar").text(self.getInitials(chat.chat_name || chat.chat_phone));

            if (chat.chat_lead_id) {
              $("#waActiveLeadBadge").show();
              $("#waActiveLeadText").text("Lead #" + chat.chat_lead_id + (chat.enquiry_name ? " (" + chat.enquiry_name + ")" : ""));
              $("#waViewLeadLink").attr("href", "/dashboard/leads/view/?id=" + btoa(chat.chat_lead_id)).show();
            } else {
              $("#waActiveLeadBadge").hide();
              $("#waViewLeadLink").hide();
            }
          }

          const $stream = $("#waMessagesStream");
          if (!Array.isArray(res.messages) || res.messages.length === 0) {
            $stream.html('<div class="wa-loading-threads" style="color:rgba(0,0,0,0.5);"><span>No messages in this chat yet. Send the first message!</span></div>');
            return;
          }

          let html = "";
          res.messages.forEach(function (msg) {
            const isOut = msg.direction === "outgoing";
            html += `
              <div class="wa-msg-bubble ${isOut ? "outgoing" : "incoming"}">
                <div class="wa-msg-text">${self.formatMessageText(msg.text)}</div>
                <div class="wa-msg-meta">
                  <span>${self.escapeHtml(msg.time)}</span>
                  ${isOut ? '<span class="wa-ticks"><i class="fi fi-rr-check-double"></i></span>' : ""}
                </div>
              </div>
            `;
          });

          $stream.html(html);

          if (scrollToBottom) {
            $stream.scrollTop($stream[0].scrollHeight);
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
          text: text
        }),
        dataType: "json",
        success: function (res) {
          self.isSending = false;
          $btn.prop("disabled", false).find("i").attr("class", "fi fi-rr-paper-plane");

          if (res && res.status === "success") {
            $textarea.val("").focus();
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
            // Send initial message
            $.ajax({
              url: "/api/v3/whatsapp/messages/",
              method: "POST",
              contentType: "application/json",
              data: JSON.stringify({
                chat_id: chatId,
                phone: targetPhone,
                text: message
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

    startPolling: function () {
      const self = this;
      this.pollTimer = setInterval(function () {
        if (self.activeChatId) {
          // Poll messages silently without resetting scroll if user is viewing history
          const $stream = $("#waMessagesStream");
          const isAtBottom = $stream.length ? ($stream[0].scrollHeight - $stream.scrollTop() <= $stream.outerHeight() + 80) : true;
          self.loadMessages(self.activeChatId, isAtBottom);
        }
        self.loadThreads();
      }, 4500);
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
    }
  };

  $(document).ready(function () {
    window.V3WhatsApp.init();
  });
});
