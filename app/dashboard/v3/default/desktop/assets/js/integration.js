/**
 * V3 Integration Hub Module
 * Handles category filtering, interactive search, and webhook modals.
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
      const $app = $("#integrationApp");
      if (!$app.length) return;

      this.activeCategory = "all";
      this.searchQuery = "";
      $("#integrationSearchInput").val("");
      this.filterCards();

      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }
    },

    bindEvents: function () {
      const self = this;

      // 1. Category Tabs Switching
      $(document).on("click", ".integration-cat-tab", function () {
        $(".integration-cat-tab").removeClass("active");
        $(this).addClass("active");
        self.activeCategory = $(this).data("category") || "all";
        self.filterCards();
      });

      // 2. Interactive Search
      $(document).on("input keyup", "#integrationSearchInput", function () {
        self.searchQuery = $(this).val().toLowerCase().trim();
        self.filterCards();
      });

      // 3. Webhook Modal
      $(document).on("click", ".btn-show-webhook-modal", function () {
        $("#modalWebhookDetails").addClass("is-active");
      });

      // Close modal
      $(document).on("click", "#modalWebhookDetails .v3-modal-close-trigger, #modalWebhookDetails .v3-modal-overlay", function (e) {
        if (e.target === this || $(this).hasClass("v3-modal-close-trigger") || $(this).closest(".v3-modal-close-trigger").length) {
          $("#modalWebhookDetails").removeClass("is-active");
        }
      });

      // Copy Webhook URL
      $(document).on("click", "#btnCopyWebhookUrl", function () {
        const url = $("#webhookEndpointText").text().trim();
        if (navigator.clipboard) {
          navigator.clipboard.writeText(url).then(function () {
            self.showToast("Webhook URL copied to clipboard!", "success");
          });
        } else {
          const $temp = $("<input>");
          $("body").append($temp);
          $temp.val(url).select();
          document.execCommand("copy");
          $temp.remove();
          self.showToast("Webhook URL copied to clipboard!", "success");
        }
      });

      // Connect Google Ads action
      $(document).on("click", ".btn-connect-google", function () {
        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Google Ads Integration",
            text: "Google Ads Lead Extension sync is ready. Please contact your administrator or follow the API setup guide.",
            icon: "info",
            confirmButtonColor: "#0284c7",
            confirmButtonText: "Got it",
          });
        } else {
          alert("Google Ads Lead Extension sync is ready. Please contact your administrator for API credentials.");
        }
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

        const matchesCat = (self.activeCategory === "all" || cardCat === self.activeCategory);
        const matchesSearch = (!self.searchQuery || title.includes(self.searchQuery) || desc.includes(self.searchQuery) || tag.includes(self.searchQuery));

        if (matchesCat && matchesSearch) {
          $card.removeClass("is-hidden");
          visibleCount++;
        } else {
          $card.addClass("is-hidden");
        }
      });

      if (visibleCount === 0) {
        $("#integrationEmptyState").addClass("is-visible").removeClass("is-hidden");
      } else {
        $("#integrationEmptyState").removeClass("is-visible").addClass("is-hidden");
      }
    },

    showToast: function (msg, type) {
      if (typeof popUpMessage === "function") {
        popUpMessage("center", msg, type || "success");
      } else {
        alert(msg);
      }
    }
  };

  $(document).ready(function () {
    window.V3Integration.init();
  });
});
