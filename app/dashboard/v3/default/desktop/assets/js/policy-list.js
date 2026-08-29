/**
 * V3 Policies & Legal Terms Directory Controller
 * Path: /app/dashboard/v3/default/desktop/assets/js/policy-list.js
 */

(function (window, $) {
  "use strict";

  const V3PolicyList = {
    listApi: "/api/v3/policy/list/",
    deleteApi: "/api/v3/policy/delete/",
    allPolicies: [],
    filteredPolicies: [],
    activeStatus: "",
    searchQuery: "",
    currentPage: 1,
    perPage: 25,

    init: function () {
      const $root = $("#policyListApp");
      if (!$root.length) return;

      this.currentPage = 1;
      this.perPage = parseInt($("#policyPerPage").val(), 10) || 25;
      this.initSelect2();
      this.bindEvents();
      this.fetchPolicies();
    },

    initSelect2: function () {
      if ($.fn.select2) {
        $("#filterPolicyStatus").select2({
          minimumResultsForSearch: Infinity,
          width: "resolve"
        });
      }
    },

    bindEvents: function () {
      const self = this;
      $(document).off(".policyList");

      // Status Filter
      $(document).on("change.policyList", "#filterPolicyStatus", function () {
        self.activeStatus = $(this).val();
        self.currentPage = 1;
        self.applyFilters();
      });

      // Search with debounce
      let searchTimer = null;
      $(document).on("input.policyList", "#policySearchInput", function () {
        const val = $(this).val().trim();
        $("#clearPolicySearchBtn").toggle(val.length > 0);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          self.searchQuery = val.toLowerCase();
          self.currentPage = 1;
          self.applyFilters();
        }, 180);
      });

      // Clear Search
      $(document).on("click.policyList", "#clearPolicySearchBtn", function () {
        $("#policySearchInput").val("").trigger("input").focus();
        $(this).hide();
      });

      // Reset Filters
      $(document).on("click.policyList", "#btnResetPolicyFilters, #btnResetEmptyPolicyFilters", function () {
        self.activeStatus = "";
        self.searchQuery = "";
        $("#policySearchInput").val("");
        $("#clearPolicySearchBtn").hide();

        if ($.fn.select2) {
          $("#filterPolicyStatus").val("").trigger("change.select2");
        } else {
          $("#filterPolicyStatus").val("");
        }

        self.currentPage = 1;
        self.applyFilters();
      });

      // Rows Per Page
      $(document).on("change.policyList", "#policyPerPage", function () {
        self.perPage = parseInt($(this).val(), 10) || 25;
        self.currentPage = 1;
        self.renderCurrentPage();
      });

      // Pagination Clicks
      $(document).on("click.policyList", "#policyPrevPage", function () {
        if (self.currentPage > 1) {
          self.currentPage--;
          self.renderCurrentPage();
        }
      });

      $(document).on("click.policyList", "#policyNextPage", function () {
        const totalPages = Math.ceil(self.filteredPolicies.length / self.perPage) || 1;
        if (self.currentPage < totalPages) {
          self.currentPage++;
          self.renderCurrentPage();
        }
      });

      $(document).on("click.policyList", ".policy-page-btn", function () {
        const page = parseInt($(this).data("page"), 10);
        if (page && page !== self.currentPage) {
          self.currentPage = page;
          self.renderCurrentPage();
        }
      });

      // Delete Policy
      $(document).on("click.policyList", ".btn-delete-policy", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const title = $(this).data("title") || "this policy";
        self.confirmDeletePolicy(id, title);
      });
    },

    fetchPolicies: function () {
      const self = this;
      const $tbody = $("#policyTableBody");

      $tbody.html(`
        <tr>
          <td colspan="5" class="table-loading-state">
            <div class="table-loading-spinner"></div>
            <span>Loading policies...</span>
          </td>
        </tr>
      `);

      $.ajax({
        url: self.listApi,
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && Array.isArray(res.policies)) {
            self.allPolicies = res.policies;
            self.updateKpiCards(res.summary || {});
            self.currentPage = 1;
            self.applyFilters();
          } else {
            self.allPolicies = [];
            self.renderEmptyState();
            $("#policyPaginationBar").hide();
          }
        },
        error: function () {
          self.allPolicies = [];
          $tbody.html(`
            <tr>
              <td colspan="5" class="table-empty-state">
                <p style="color: #ef4444; margin-bottom: 8px;">Failed to load policy records.</p>
                <button type="button" class="btn-v3-outline-sm" onclick="V3PolicyList.fetchPolicies()">
                  <i class="fi fi-rr-refresh"></i> Retry
                </button>
              </td>
            </tr>
          `);
          $("#policyPaginationBar").hide();
        }
      });
    },

    updateKpiCards: function (summary) {
      $("#statTotalPolicies").text(summary.total || 0);
      $("#statActivePolicies").text(summary.active || 0);
      $("#statPublishedPolicies").text(summary.published || 0);
      $("#statDraftPolicies").text(summary.draft || 0);
    },

    applyFilters: function () {
      const self = this;
      let list = self.allPolicies.slice();

      // Status Filter
      if (self.activeStatus !== "" && self.activeStatus !== "all") {
        list = list.filter((p) => String(p.status) === String(self.activeStatus));
      }

      // Search Query
      if (self.searchQuery) {
        const q = self.searchQuery;
        list = list.filter((p) => {
          return (
            (p.title && p.title.toLowerCase().includes(q)) ||
            (p.slug && p.slug.toLowerCase().includes(q)) ||
            (p.excerpt && p.excerpt.toLowerCase().includes(q))
          );
        });
      }

      self.filteredPolicies = list;
      self.renderCurrentPage();
    },

    renderCurrentPage: function () {
      const self = this;
      const totalItems = self.filteredPolicies.length;
      const totalPages = Math.ceil(totalItems / self.perPage) || 1;

      if (self.currentPage > totalPages) {
        self.currentPage = totalPages;
      }
      if (self.currentPage < 1) {
        self.currentPage = 1;
      }

      const startIdx = (self.currentPage - 1) * self.perPage;
      const endIdx = Math.min(startIdx + self.perPage, totalItems);
      const pageData = self.filteredPolicies.slice(startIdx, endIdx);

      const $tbody = $("#policyTableBody");
      $tbody.empty();

      if (totalItems === 0) {
        self.renderEmptyState();
        $("#policyPaginationBar").hide();
        return;
      }

      pageData.forEach(function (p) {
        $tbody.append(self.buildRowHtml(p));
      });

      // Update Pagination Meta
      const startDisplay = totalItems > 0 ? startIdx + 1 : 0;
      $("#policyPaginationInfo").text(`Showing ${startDisplay} to ${endIdx} of ${totalItems} policies`);
      $("#policyPrevPage").prop("disabled", self.currentPage <= 1);
      $("#policyNextPage").prop("disabled", self.currentPage >= totalPages);

      let pageBtnsHtml = "";
      for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= self.currentPage - 1 && i <= self.currentPage + 1)) {
          pageBtnsHtml += `<button type="button" class="pagination-page-btn policy-page-btn ${i === self.currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        } else if (i === self.currentPage - 2 || i === self.currentPage + 2) {
          pageBtnsHtml += `<span class="pagination-ellipsis">&hellip;</span>`;
        }
      }
      $("#policyPaginationPages").html(pageBtnsHtml);
      $("#policyPaginationBar").show();
    },

    buildRowHtml: function (p) {
      const self = this;

      // Status Badge
      let statusBadge = `<span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>`;
      if (String(p.status) === "0" || p.status === "inactive" || p.status === "draft") {
        statusBadge = `<span class="v3-status-badge status-inactive"><span class="status-pulse-dot"></span> Draft</span>`;
      }

      // Thumbnail Image
      const bannerHtml = p.image_url
        ? `<img src="${self.escapeHtml(p.image_url)}" alt="Banner" class="company-row-logo" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px;" />`
        : `<div class="company-initial-badge" style="width: 48px; height: 36px; border-radius: 6px; font-size: 14px;"><i class="fi fi-rr-document"></i></div>`;

      return `
        <tr class="v3-policy-row" data-id="${p.id}">
          <td class="col-policy-title">
            <div class="company-entity-flex">
              <div class="company-entity-meta">
                <h4 class="company-name-title" style="font-size: 13.5px; font-weight: 600; color: #1e293b;">
                  <a href="${p.edit_url}" style="color: inherit; text-decoration: none;">${self.escapeHtml(p.title)}</a>
                </h4>
                <div class="company-tax-row mt-1">
                  <a href="${self.escapeHtml(p.public_url)}" target="_blank" class="company-tax-pill" style="text-decoration: none;" title="Open live public URL">
                    <i class="fi fi-rr-link-alt"></i> ${self.escapeHtml(p.slug)}
                  </a>
                </div>
              </div>
            </div>
          </td>
          <td class="col-policy-banner">
            ${bannerHtml}
          </td>
          <td class="col-policy-published">
            <div style="font-size: 12.5px; color: #475569; font-weight: 500;">
              ${p.published_at || p.created_at || "Recently"}
            </div>
          </td>
          <td class="col-policy-status">
            ${statusBadge}
          </td>
          <td class="col-policy-actions text-right">
            <div class="table-actions-inline" style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
              <a href="${self.escapeHtml(p.public_url)}" target="_blank" class="btn-action-icon preview" data-tooltip="View live webpage" title="View live page">
                <i class="fi fi-rr-eye"></i>
              </a>
              <a href="${p.edit_url}" class="btn-action-icon edit" data-tooltip="Edit policy" title="Edit policy">
                <i class="fi fi-rr-edit"></i>
              </a>
              <button type="button" class="btn-action-icon delete btn-delete-policy" data-id="${p.id}" data-title="${self.escapeHtml(p.title)}" data-tooltip="Delete policy" title="Delete policy">
                <i class="fi fi-rr-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    },

    renderEmptyState: function () {
      const isFiltered = this.searchQuery || this.activeStatus !== "";
      $("#policyTableBody").html(`
        <tr>
          <td colspan="5" class="table-empty-state">
            <div class="empty-state-icon-circle">
              <i class="fi fi-rr-document-signed"></i>
            </div>
            <h4>No policy records found</h4>
            <p>
              ${isFiltered ? "No policies match your search criteria. Try clearing your filters." : "Create legal terms, privacy policies, or cancellation rules for your website."}
            </p>
            <div class="empty-actions-row">
              ${isFiltered ? `
                <button type="button" id="btnResetEmptyPolicyFilters" class="btn-v3-action" style="padding: 8px 18px; font-size: 12.5px;">
                  <i class="fi fi-rr-refresh"></i> Reset Filters
                </button>
              ` : `
                <a href="/dashboard/policy/add/" class="btn-add-pkg-v3" style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px;">
                  <i class="fi fi-rr-plus"></i> Add First Policy
                </a>
              `}
            </div>
          </td>
        </tr>
      `);
      $("#policyPaginationBar").hide();
    },

    confirmDeletePolicy: function (id, title) {
      const self = this;

      if (window.Swal && typeof window.Swal.fire === "function") {
        window.Swal.fire({
          title: "Delete Policy?",
          text: `Are you sure you want to remove "${title}"? This cannot be undone.`,
          icon: "warning",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#ef4444",
          cancelButtonColor: "#94a3b8",
          confirmButtonText: "Yes, Delete",
          cancelButtonText: "Cancel"
        }).then(function (result) {
          if (result.isConfirmed || result.value) {
            self.executeDelete(id);
          }
        });
      } else if (confirm(`Are you sure you want to delete "${title}"?`)) {
        self.executeDelete(id);
      }
    },

    executeDelete: function (id) {
      const self = this;

      $.ajax({
        url: self.deleteApi,
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg || "Policy deleted successfully.", "success");
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Deleted!",
                text: res.msg || "Policy deleted successfully.",
                icon: "success",
                type: "success",
                timer: 1400,
                showConfirmButton: false
              });
            }
            self.fetchPolicies();
          } else {
            const msg = (res && res.msg) || "Could not delete policy.";
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            } else {
              alert(msg);
            }
          }
        },
        error: function () {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Network error while deleting policy.", "error");
          } else {
            alert("Network error while deleting policy.");
          }
        }
      });
    },

    escapeHtml: function (text) {
      if (!text) return "";
      return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  };

  $(document).ready(function () {
    V3PolicyList.init();
  });

  $(document).on("spa:loaded", function () {
    V3PolicyList.init();
  });

  window.V3PolicyList = V3PolicyList;
})(window, jQuery);
