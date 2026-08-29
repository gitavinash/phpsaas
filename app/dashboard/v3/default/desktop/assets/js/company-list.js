/**
 * V3 Companies & Branches Management Directory Frontend Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/company-list.js
 */

(function (window, $) {
  "use strict";

  const V3Company = {
    apiEndpoint: "/api/v3/company/list/",
    deleteEndpoint: "/api/v3/company/delete/",
    allCompanies: [],
    filteredCompanies: [],
    activeType: "",
    activeStatus: "",
    searchQuery: "",
    currentPage: 1,
    perPage: 25,
    activeFetchXhr: null,
    isLoading: false,

    init: function () {
      const $root = $("#companyListApp");
      if (!$root.length) return;

      this.currentPage = 1;
      this.perPage = parseInt($("#companyPerPage").val(), 10) || 25;
      this.initSelect2();
      this.bindEvents();
      this.fetchCompanies();
    },

    initSelect2: function () {
      if ($.fn.select2) {
        $("#filterCompanyType, #filterCompanyStatus").select2({
          minimumResultsForSearch: Infinity,
          width: "resolve"
        });
      }
    },

    bindEvents: function () {
      const self = this;

      // Type Filter Dropdown
      $(document).off("change", "#filterCompanyType").on("change", "#filterCompanyType", function () {
        self.activeType = $(this).val();
        self.currentPage = 1;
        self.applyFilters();
      });

      // Status Filter Dropdown
      $(document).off("change", "#filterCompanyStatus").on("change", "#filterCompanyStatus", function () {
        self.activeStatus = $(this).val();
        self.currentPage = 1;
        self.applyFilters();
      });

      // Search Filter with Debounce
      let searchTimer = null;
      $(document).off("input", "#companySearchInput").on("input", "#companySearchInput", function () {
        const val = $(this).val().trim();
        $("#clearCompanySearchBtn").toggle(val.length > 0);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          self.searchQuery = val.toLowerCase();
          self.currentPage = 1;
          self.applyFilters();
        }, 180);
      });

      // Clear Search Button
      $(document).off("click", "#clearCompanySearchBtn").on("click", "#clearCompanySearchBtn", function () {
        $("#companySearchInput").val("").trigger("input").focus();
        $(this).hide();
      });

      // Reset All Filters
      $(document).off("click", "#btnResetCompanyFilters, #btnResetEmptyCompanyFilters").on("click", "#btnResetCompanyFilters, #btnResetEmptyCompanyFilters", function () {
        self.activeType = "";
        self.activeStatus = "";
        self.searchQuery = "";
        $("#companySearchInput").val("");
        $("#clearCompanySearchBtn").hide();

        if ($.fn.select2) {
          $("#filterCompanyType").val("").trigger("change.select2");
          $("#filterCompanyStatus").val("").trigger("change.select2");
        } else {
          $("#filterCompanyType").val("");
          $("#filterCompanyStatus").val("");
        }

        self.currentPage = 1;
        self.applyFilters();
      });

      // Refresh Button
      $(document).off("click", "#btnRefreshCompanies").on("click", "#btnRefreshCompanies", function () {
        self.fetchCompanies();
      });

      // Page Number Click
      $(document).off("click.companyList", ".company-page-btn").on("click.companyList", ".company-page-btn", function () {
        const page = parseInt($(this).data("page"), 10);
        if (page && page !== self.currentPage) {
          self.currentPage = page;
          self.renderCurrentPage();
        }
      });

      // Rows Per Page Change
      $(document).off("change.companyList", "#companyPerPage").on("change.companyList", "#companyPerPage", function () {
        self.perPage = parseInt($(this).val(), 10) || 25;
        self.currentPage = 1;
        self.renderCurrentPage();
      });

      // Pagination Controls
      $(document).off("click.companyList", "#companyPrevPage").on("click.companyList", "#companyPrevPage", function () {
        if (self.currentPage > 1) {
          self.currentPage--;
          self.renderCurrentPage();
        }
      });

      $(document).off("click.companyList", "#companyNextPage").on("click.companyList", "#companyNextPage", function () {
        const totalPages = Math.ceil(self.filteredCompanies.length / self.perPage) || 1;
        if (self.currentPage < totalPages) {
          self.currentPage++;
          self.renderCurrentPage();
        }
      });

      // Delete Company Action
      $(document).off("click.companyList", ".btn-delete-company").on("click.companyList", ".btn-delete-company", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const name = $(this).data("name") || "this company";
        self.confirmDeleteCompany(id, name);
      });
    },

    fetchCompanies: function () {
      const self = this;
      const $tbody = $("#companyTableBody");

      $tbody.html(`
        <tr>
          <td colspan="6" class="table-loading-state">
            <div class="table-loading-spinner"></div>
            <span>Loading registered companies...</span>
          </td>
        </tr>
      `);

      $.ajax({
        url: self.apiEndpoint,
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && Array.isArray(res.companies)) {
            self.allCompanies = res.companies;
            self.updateKpiCards(res.stats || {});
            self.currentPage = 1;
            self.applyFilters();
          } else {
            self.allCompanies = [];
            self.renderEmptyState();
            $("#companyPaginationBar").hide();
          }
        },
        error: function () {
          self.allCompanies = [];
          $tbody.html(`
            <tr>
              <td colspan="6" class="table-empty-state">
                <p style="color: #ef4444; margin-bottom: 8px;">Failed to load company records.</p>
                <button type="button" id="btnRefreshCompanies" class="btn-v3-outline-sm">
                  <i class="fi fi-rr-refresh"></i> Retry
                </button>
              </td>
            </tr>
          `);
          $("#companyPaginationBar").hide();
        }
      });
    },

    updateKpiCards: function (summary) {
      $("#statTotalCompanies").text(summary.total || 0);
      $("#statActiveCompanies").text(summary.active || 0);
      $("#statHeadOffices").text(summary.head || 0);
      $("#statBranchOffices").text(summary.branches || 0);
    },

    applyFilters: function () {
      const self = this;
      let list = self.allCompanies.slice();

      // Filter by Status
      if (self.activeStatus !== "" && self.activeStatus !== "all") {
        const targetStatus = parseInt(self.activeStatus, 10);
        list = list.filter((c) => c.status === targetStatus);
      }

      // Filter by Type
      if (self.activeType !== "" && self.activeType !== "all") {
        list = list.filter((c) => (c.type || "").toLowerCase() === self.activeType.toLowerCase());
      }

      // Filter by Search Query
      if (self.searchQuery) {
        const q = self.searchQuery;
        list = list.filter((c) => {
          return (
            (c.name && c.name.toLowerCase().includes(q)) ||
            (c.code && c.code.toLowerCase().includes(q)) ||
            (c.email && c.email.toLowerCase().includes(q)) ||
            (c.mobile && c.mobile.toLowerCase().includes(q)) ||
            (c.city && c.city.toLowerCase().includes(q)) ||
            (c.state && c.state.toLowerCase().includes(q)) ||
            (c.gst && c.gst.toLowerCase().includes(q)) ||
            (c.pan && c.pan.toLowerCase().includes(q)) ||
            (c.formatted_address && c.formatted_address.toLowerCase().includes(q))
          );
        });
      }

      self.filteredCompanies = list;
      self.renderCurrentPage();
    },

    renderCurrentPage: function () {
      const self = this;
      const totalItems = self.filteredCompanies.length;
      const totalPages = Math.ceil(totalItems / self.perPage) || 1;

      if (self.currentPage > totalPages) {
        self.currentPage = totalPages;
      }
      if (self.currentPage < 1) {
        self.currentPage = 1;
      }

      const startIdx = (self.currentPage - 1) * self.perPage;
      const endIdx = Math.min(startIdx + self.perPage, totalItems);
      const pageCompanies = self.filteredCompanies.slice(startIdx, endIdx);

      const $tbody = $("#companyTableBody");
      $tbody.empty();

      if (totalItems === 0) {
        self.renderEmptyState();
        $("#companyPaginationBar").hide();
        return;
      }

      pageCompanies.forEach(function (c) {
        const trHtml = self.buildRowHtml(c);
        $tbody.append(trHtml);
      });

      // Update Pagination Meta
      const startDisplay = totalItems > 0 ? startIdx + 1 : 0;
      $("#companyPaginationInfo").text(`Showing ${startDisplay} to ${endIdx} of ${totalItems} companies`);
      $("#companyPrevPage").prop("disabled", self.currentPage <= 1);
      $("#companyNextPage").prop("disabled", self.currentPage >= totalPages);

      let pageBtnsHtml = "";
      for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= self.currentPage - 1 && p <= self.currentPage + 1)) {
          pageBtnsHtml += `<button type="button" class="pagination-page-btn company-page-btn ${p === self.currentPage ? 'active' : ''}" data-page="${p}">${p}</button>`;
        } else if (p === self.currentPage - 2 || p === self.currentPage + 2) {
          pageBtnsHtml += `<span class="pagination-ellipsis">&hellip;</span>`;
        }
      }
      $("#companyPaginationPages").html(pageBtnsHtml);
      $("#companyPaginationBar").show();
    },

    buildRowHtml: function (c) {
      const self = this;

      // Status Badge
      let statusBadge = `<span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>`;
      if (c.status === 2) {
        statusBadge = `<span class="v3-status-badge status-pending"><span class="status-pulse-dot"></span> Pending</span>`;
      } else if (c.status === 0 || c.status === 3) {
        statusBadge = `<span class="v3-status-badge status-inactive"><span class="status-pulse-dot"></span> Inactive</span>`;
      }

      // Initials / Logo
      const initials = (c.name || "CO")
        .split(" ")
        .map((n) => n[0])
        .slice(0, 2)
        .join("")
        .toUpperCase();

      const logoHtml = c.logo
        ? `<img src="${self.escapeHtml(c.logo)}" alt="${self.escapeHtml(c.name)}" class="company-row-logo" />`
        : `<div class="company-initial-badge">${initials}</div>`;

      // Type Badge
      const typeLabel = c.type_label || "Registered Entity";
      const isHeadOffice = ["head_office", "headquarters", "private", "partnerships", "proprietorship", "company"].includes(c.type);
      const typeBadgeClass = isHeadOffice ? "company-badge-hq" : "company-badge-branch";

      // Tax Badges
      const taxBadges = [];
      if (c.gst) {
        taxBadges.push(`<span class="company-tax-pill"><i class="fi fi-rr-receipt"></i> GST: ${self.escapeHtml(c.gst)}</span>`);
      }
      if (c.pan) {
        taxBadges.push(`<span class="company-tax-pill"><i class="fi fi-rr-document"></i> PAN: ${self.escapeHtml(c.pan)}</span>`);
      }
      const taxHtml = taxBadges.length > 0 ? `<div class="company-tax-row">${taxBadges.join("")}</div>` : "";

      // Contact HTML
      const contactBits = [];
      if (c.email) {
        contactBits.push(`<a href="mailto:${self.escapeHtml(c.email)}" class="company-contact-item"><i class="fi fi-rr-envelope"></i> ${self.escapeHtml(c.email)}</a>`);
      }
      if (c.mobile) {
        contactBits.push(`<a href="tel:${self.escapeHtml(c.mobile)}" class="company-contact-item"><i class="fi fi-rr-phone-call"></i> ${self.escapeHtml(c.mobile)}</a>`);
      }
      const contactHtml = contactBits.length > 0 ? contactBits.join("") : `<span class="text-muted-dash">-</span>`;

      // Address HTML
      const addressHtml = c.formatted_address
        ? `<div class="company-address-wrap"><i class="fi fi-rr-marker"></i> <span>${self.escapeHtml(c.formatted_address)}</span></div>`
        : `<span class="text-muted-dash">No address specified</span>`;

      return `
        <tr class="v3-company-row" data-id="${c.id}">
          <td class="col-company-entity">
            <div class="company-entity-flex">
              ${logoHtml}
              <div class="company-entity-meta">
                <h4 class="company-name-title">${self.escapeHtml(c.name)}</h4>
                ${c.code ? `<span class="company-code-badge">ID: ${self.escapeHtml(c.code)}</span>` : ""}
              </div>
            </div>
          </td>
          <td class="col-company-type">
            <span class="company-type-pill ${typeBadgeClass}">${self.escapeHtml(typeLabel)}</span>
            ${taxHtml}
          </td>
          <td class="col-company-contact">
            <div class="company-contact-stack">
              ${contactHtml}
            </div>
          </td>
          <td class="col-company-address">
            ${addressHtml}
          </td>
          <td class="col-company-status">
            ${statusBadge}
          </td>
          <td class="col-company-actions text-right">
            <div class="table-actions-inline" style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
              <a href="${c.edit_url}" class="btn-action-icon edit" data-tooltip="Edit company details" title="Edit company details">
                <i class="fi fi-rr-edit"></i>
              </a>
              <button type="button" class="btn-action-icon delete btn-delete-company" data-id="${c.id}" data-name="${self.escapeHtml(c.name)}" data-tooltip="Delete company" title="Delete company">
                <i class="fi fi-rr-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    },

    confirmDeleteCompany: function (id, name) {
      const self = this;

      if (window.Swal && typeof window.Swal.fire === "function") {
        window.Swal.fire({
          title: "Delete Company?",
          text: `Are you sure you want to delete "${name}"? This action cannot be undone.`,
          icon: "warning",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#ef4444",
          cancelButtonColor: "#64748b",
          confirmButtonText: "Yes, Delete",
          cancelButtonText: "Cancel"
        }).then(function (result) {
          if (result.isConfirmed || result.value) {
            self.executeDelete(id);
          }
        });
      } else {
        if (confirm(`Are you sure you want to delete "${name}"?`)) {
          self.executeDelete(id);
        }
      }
    },

    executeDelete: function (id) {
      const self = this;

      $.ajax({
        url: self.deleteEndpoint,
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg || "Company deleted successfully.", "success");
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Deleted!",
                text: res.msg || "Company deleted successfully.",
                icon: "success",
                type: "success",
                timer: 1600,
                showConfirmButton: false
              });
            }
            self.fetchCompanies();
          } else {
            const msg = (res && res.msg) || "Could not delete company.";
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            } else if (window.Swal && typeof window.Swal.fire === "function") {
              window.Swal.fire({
                title: "Error",
                text: msg,
                icon: "error",
                type: "error"
              });
            } else {
              alert(msg);
            }
          }
        },
        error: function () {
          const msg = "Network or server error while deleting company.";
          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, "error");
          } else if (window.Swal && typeof window.Swal.fire === "function") {
            window.Swal.fire({
              title: "Error",
              text: msg,
              icon: "error",
              type: "error"
            });
          } else {
            alert(msg);
          }
        }
      });
    },

    renderLoadingSkeleton: function () {
      const $tbody = $("#companyTableBody");
      $tbody.empty();

      for (let i = 0; i < 5; i++) {
        $tbody.append(`
          <tr class="v3-skeleton-row">
            <td><div class="v3-skeleton-line w-75"></div></td>
            <td><div class="v3-skeleton-line w-50"></div></td>
            <td><div class="v3-skeleton-line w-60"></div></td>
            <td><div class="v3-skeleton-line w-80"></div></td>
            <td><div class="v3-skeleton-line w-40"></div></td>
            <td><div class="v3-skeleton-line w-30"></div></td>
          </tr>
        `);
      }
    },

    renderEmptyState: function () {
      const self = this;
      const isFiltered = self.searchQuery || self.activeType || self.activeStatus;
      const $tbody = $("#companyTableBody");

      $tbody.html(`
        <tr>
          <td colspan="6" class="table-empty-state">
            <div class="empty-state-icon-circle">
              <i class="fi fi-rr-building"></i>
            </div>
            <h4>No Companies Found</h4>
            <p>
              ${isFiltered ? "No registered companies matched your current search filters or criteria. Try clearing filters or modifying your query." : "No registered company entities have been created yet."}
            </p>
            <div class="empty-actions-row">
              ${isFiltered ? `
                <button type="button" id="btnResetEmptyCompanyFilters" class="btn-v3-action">
                  <i class="fi fi-rr-refresh"></i> <span>Reset Filters</span>
                </button>
              ` : `
                <a href="/dashboard/company/add/" class="btn-add-pkg-v3">
                  <i class="fi fi-rr-plus"></i> <span>Add First Company</span>
                </a>
              `}
            </div>
          </td>
        </tr>
      `);
    },

    renderErrorState: function (msg) {
      const $tbody = $("#companyTableBody");
      $tbody.html(`
        <tr>
          <td colspan="6" class="text-center py-5">
            <div class="v3-error-banner">
              <i class="fi fi-rr-exclamation"></i>
              <p>${this.escapeHtml(msg)}</p>
              <button type="button" class="btn-v3-outline-sm mt-2" id="btnRefreshCompanies">Retry</button>
            </div>
          </td>
        </tr>
      `);
      $("#companyPaginationWrap").hide();
    },

    escapeHtml: function (str) {
      if (!str) return "";
      return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  };

  $(document).ready(function () {
    V3Company.init();
  });

  $(document).on("spa:loaded", function () {
    V3Company.init();
  });

  window.V3Company = V3Company;
})(window, jQuery);
