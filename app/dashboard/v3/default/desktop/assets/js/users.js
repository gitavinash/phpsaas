/**
 * V3 Team & Users Management Directory Frontend Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/users.js
 */

(function (window, $) {
  "use strict";

  const V3Users = {
    apiEndpoint: "/api/v3/users/",
    allUsers: [],
    filteredUsers: [],
    rolesMap: {},
    activeRole: "",
    activeStatus: "active",
    searchQuery: "",
    currentPage: 1,
    perPage: 25,
    activeFetchXhr: null,
    isLoading: false,

    init: function () {
      const $root = $("#usersListApp");
      if (!$root.length) return;

      this.currentPage = 1;
      this.perPage = parseInt($("#userPerPage").val(), 10) || 25;
      this.bindEvents();
      this.fetchUsers();
    },

    bindEvents: function () {
      const self = this;

      // Role Filter Dropdown
      $(document).off("change", "#filterUserRole").on("change", "#filterUserRole", function () {
        self.activeRole = $(this).val();
        self.currentPage = 1;
        self.applyFilters();
      });

      // Status Filter Dropdown
      $(document).off("change", "#filterUserStatus").on("change", "#filterUserStatus", function () {
        self.activeStatus = $(this).val();
        self.currentPage = 1;
        self.applyFilters();
      });

      // Search Filter with Debounce
      let searchTimer = null;
      $(document).off("input", "#userSearchInput").on("input", "#userSearchInput", function () {
        const val = $(this).val().trim();
        $("#clearUserSearchBtn").toggle(val.length > 0);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          self.searchQuery = val.toLowerCase();
          self.currentPage = 1;
          self.applyFilters();
        }, 180);
      });

      // Clear Search Button
      $(document).off("click", "#clearUserSearchBtn").on("click", "#clearUserSearchBtn", function () {
        $("#userSearchInput").val("").trigger("input").focus();
        $(this).hide();
      });

      // Reset All Filters
      $(document).off("click", "#btnResetUserFilters, #btnResetEmptyFilters").on("click", "#btnResetUserFilters, #btnResetEmptyFilters", function () {
        self.activeRole = "";
        self.activeStatus = "active";
        self.searchQuery = "";
        $("#userSearchInput").val("");
        $("#clearUserSearchBtn").hide();
        $("#filterUserRole").val("");
        $("#filterUserStatus").val("active");
        self.currentPage = 1;
        self.applyFilters();
      });

      // Rows Per Page Change
      $(document).off("change", "#userPerPage").on("change", "#userPerPage", function () {
        self.perPage = parseInt($(this).val(), 10) || 25;
        self.currentPage = 1;
        self.renderCurrentPage();
      });

      // Pagination Controls
      $(document).off("click", "#userPrevPage").on("click", "#userPrevPage", function () {
        if (self.currentPage > 1) {
          self.currentPage--;
          self.renderCurrentPage();
        }
      });

      $(document).off("click", "#userNextPage").on("click", "#userNextPage", function () {
        const totalPages = Math.ceil(self.filteredUsers.length / self.perPage);
        if (self.currentPage < totalPages) {
          self.currentPage++;
          self.renderCurrentPage();
        }
      });

      $(document).off("click", ".user-page-btn").on("click", ".user-page-btn", function () {
        const p = parseInt($(this).data("page"), 10);
        if (p && p !== self.currentPage) {
          self.currentPage = p;
          self.renderCurrentPage();
        }
      });

      // Delete User Action
      $(document).off("click", ".deleteUserBtn").on("click", ".deleteUserBtn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data("id");
        const name = $(this).data("name") || "this user";
        self.handleDeleteUser(id, name);
      });
    },

    fetchUsers: function () {
      const self = this;
      if (self.isLoading) return;
      if (self.activeFetchXhr) {
        self.activeFetchXhr.abort();
        self.activeFetchXhr = null;
      }
      self.isLoading = true;
      self.renderSkeletons();

      self.activeFetchXhr = $.ajax({
        url: self.apiEndpoint,
        method: "GET",
        dataType: "json",
        complete: function () {
          self.isLoading = false;
          self.activeFetchXhr = null;
        },
        success: function (res) {
          if (res && res.status === "success") {
            self.allUsers = res.data || res.users || [];
            self.renderKPIs(res.metrics || {});
            self.populateRoleDropdown(res.roles || []);
            self.applyFilters();
          } else {
            self.renderEmptyState(res ? res.msg : "Failed to load team members.");
          }
        },
        error: function (xhr, status) {
          if (status === "abort") return;
          self.renderEmptyState("Network error while loading team members.");
        }
      });
    },

    renderKPIs: function (metrics) {
      $("#statTotalUsers").text(metrics.total ?? this.allUsers.length);
      $("#statTotalAdmins").text(metrics.admins ?? 0);
      $("#statTotalAgents").text(metrics.agents ?? 0);
      $("#statTotalOperations").text(metrics.operations ?? 0);
    },

    populateRoleDropdown: function (roles) {
      if (!roles || !roles.length) return;
      const $select = $("#filterUserRole");
      const currentVal = $select.val();
      
      let html = '<option value="">All Roles</option>';
      roles.forEach(function (r) {
        html += `<option value="${selfEscapeHTML(r.code)}">${selfEscapeHTML(r.name)}</option>`;
      });
      $select.html(html);
      if (currentVal) $select.val(currentVal);
    },

    applyFilters: function () {
      const self = this;
      self.filteredUsers = self.allUsers.filter(function (u) {
        // Role Filter
        let matchRole = true;
        if (self.activeRole) {
          matchRole = (u.role || "").toLowerCase() === self.activeRole.toLowerCase();
        }

        // Status Filter
        let matchStatus = true;
        if (self.activeStatus === "active") {
          matchStatus = u.status === 1;
        } else if (self.activeStatus === "inactive") {
          matchStatus = u.status !== 1;
        }

        // Search Filter
        let matchSearch = true;
        if (self.searchQuery) {
          const q = self.searchQuery;
          matchSearch = (u.name || "").toLowerCase().indexOf(q) !== -1 ||
                        (u.email || "").toLowerCase().indexOf(q) !== -1 ||
                        (u.mobile || "").toLowerCase().indexOf(q) !== -1 ||
                        (u.role_label || "").toLowerCase().indexOf(q) !== -1 ||
                        (u.username || "").toLowerCase().indexOf(q) !== -1;
        }

        return matchRole && matchStatus && matchSearch;
      });

      self.renderCurrentPage();
    },

    renderCurrentPage: function () {
      const self = this;
      const total = self.filteredUsers.length;
      const $tbody = $("#usersTableBody");
      const $paginationBar = $("#userPaginationBar");

      if (total === 0) {
        const isFiltered = self.searchQuery || self.activeRole || self.activeStatus !== "active";
        $tbody.html(`
          <tr>
            <td colspan="5" class="table-empty-state" style="text-align: center; padding: 56px 24px;">
              <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 16px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);">
                <i class="fi fi-rr-users"></i>
              </div>
              <h4 style="font-size: 16px; color: #1e293b; margin: 0 0 6px 0; font-weight: 700;">No team members found</h4>
              <p style="color: #64748b; font-size: 13px; margin: 0 0 18px 0; max-width: 400px; display: inline-block;">
                ${isFiltered ? "No team members match your current filter settings. Try resetting your search or filter options." : "No team members have been added to this site yet."}
              </p>
              <div>
                ${isFiltered ? `
                  <button type="button" id="btnResetEmptyFilters" class="btn-v3-action" style="padding: 8px 18px; font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                    <i class="fi fi-rr-refresh"></i> Reset Filters
                  </button>
                ` : `
                  <a href="/dashboard/user/add/" class="btn-add-pkg-v3" style="padding: 8px 18px; font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fi fi-rr-user-add"></i> Add First Team Member
                  </a>
                `}
              </div>
            </td>
          </tr>
        `);
        $paginationBar.hide();
        return;
      }

      const totalPages = Math.ceil(total / self.perPage);
      if (self.currentPage > totalPages) self.currentPage = totalPages;
      if (self.currentPage < 1) self.currentPage = 1;

      const startIndex = (self.currentPage - 1) * self.perPage;
      const endIndex = Math.min(startIndex + self.perPage, total);
      const pageData = self.filteredUsers.slice(startIndex, endIndex);

      let html = "";
      pageData.forEach(function (u) {
        html += self.buildRowHTML(u);
      });

      $tbody.html(html);

      // Update Pagination Bar
      $("#userPaginationInfo").text(`Showing ${startIndex + 1} to ${endIndex} of ${total} team members`);
      $("#userPrevPage").prop("disabled", self.currentPage <= 1);
      $("#userNextPage").prop("disabled", self.currentPage >= totalPages);

      let pageBtnsHtml = "";
      for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= self.currentPage - 1 && p <= self.currentPage + 1)) {
          pageBtnsHtml += `<button type="button" class="pagination-page-btn user-page-btn ${p === self.currentPage ? 'active' : ''}" data-page="${p}">${p}</button>`;
        } else if (p === self.currentPage - 2 || p === self.currentPage + 2) {
          pageBtnsHtml += `<span class="pagination-ellipsis">&hellip;</span>`;
        }
      }
      $("#userPaginationPages").html(pageBtnsHtml);
      $paginationBar.show();
    },

    buildRowHTML: function (u) {
      const role = (u.role || "").toLowerCase();
      let roleIcon = "fi fi-rr-user";
      let roleBadgeClass = "badge-service-state";

      if (role === "administrator") {
        roleIcon = "fi fi-rr-shield-check";
        roleBadgeClass = "badge-service-state badge-role-admin";
      } else if (role === "agent") {
        roleIcon = "fi fi-rr-headset";
        roleBadgeClass = "badge-service-state badge-role-agent";
      } else if (role === "operations") {
        roleIcon = "fi fi-rr-settings-sliders";
        roleBadgeClass = "badge-service-state badge-role-operations";
      } else if (role === "accountant") {
        roleIcon = "fi fi-rr-calculator";
        roleBadgeClass = "badge-service-state badge-role-accounts";
      }

      const avatarContent = u.avatar
        ? `<img src="${u.avatar}" alt="${selfEscapeHTML(u.name)}" style="width: 38px; height: 38px; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; $(this).next().show();"><div class="transport-avatar-badge" style="width: 38px; height: 38px; font-size: 14px; flex-shrink: 0; display: none;">${u.initials}</div>`
        : `<div class="transport-avatar-badge" style="width: 38px; height: 38px; font-size: 14px; flex-shrink: 0;">${u.initials}</div>`;

      const deleteBtnHTML = (!u.is_self)
        ? `<button type="button" class="btn-action-icon delete deleteUserBtn" data-id="${u.id}" data-name="${selfEscapeHTML(u.name)}" data-tooltip="Remove access" title="Remove access">
             <i class="fi fi-rr-trash"></i>
           </button>`
        : ``;

      return `
        <tr class="user-row" data-id="${u.id}">
          <!-- Name & Info -->
          <td>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; overflow: hidden;">
                ${avatarContent}
              </div>
              <div style="min-width: 0;">
                <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; line-height: 1.3; margin-bottom: 2px;" title="${selfEscapeHTML(u.name)}">
                  ${selfEscapeHTML(u.name)}
                  ${u.is_self ? '<span class="v3-tag-self" style="font-size: 10px; background: #e0e7ff; color: #4338ca; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">You</span>' : ''}
                </div>
                <span style="color: #94a3b8; font-size: 11.5px; font-weight: 500;">User #${u.id}</span>
              </div>
            </div>
          </td>

          <!-- Contact details -->
          <td>
            <div style="display: flex; flex-direction: column; gap: 3px;">
              ${u.mobile ? `
                <div class="table-contact-link phone" title="${u.mobile}">
                  <i class="fi fi-rr-phone-call" style="color: #10b981; font-size: 11px;"></i> <span style="font-size: 12.5px; color: #334155; font-weight: 500;">+91 ${u.mobile}</span>
                </div>
              ` : '<span style="color: #94a3b8; font-size: 12px;">-</span>'}
              ${u.email ? `
                <a href="mailto:${u.email}" style="color: #6366f1; font-size: 12px; font-weight: 500; text-decoration: none; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;" title="${u.email}">
                  <i class="fi fi-rr-envelope" style="font-size: 10.5px; color: #818cf8;"></i> <span>${u.email}</span>
                </a>
              ` : ''}
            </div>
          </td>

          <!-- Role -->
          <td>
            <span class="${roleBadgeClass}" title="${selfEscapeHTML(u.role_label)}">
              <i class="${roleIcon}" style="font-size: 11px; margin-right: 4px;"></i> ${selfEscapeHTML(u.role_label)}
            </span>
          </td>

          <!-- Status -->
          <td style="text-align: center;">
            ${u.status === 1 
              ? '<span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>' 
              : '<span class="v3-status-badge status-inactive"><span class="status-pulse-dot"></span> Inactive</span>'}
          </td>

          <!-- Actions -->
          <td style="text-align: right;">
            <div class="table-actions-inline" style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
              <a href="/dashboard/user/edit/?id=${u.encoded_id}" class="btn-action-icon edit" data-tooltip="Edit User" title="Edit User">
                <i class="fi fi-rr-edit"></i>
              </a>
              <a href="/dashboard/user/kyc/?id=${u.encoded_id}" class="btn-action-icon kyc" data-tooltip="KYC Details" title="KYC Details">
                <i class="fi fi-rr-assessment"></i>
              </a>
              ${deleteBtnHTML}
            </div>
          </td>
        </tr>
      `;
    },

    renderSkeletons: function () {
      const $tbody = $("#usersTableBody");
      $tbody.html(`
        <tr>
          <td colspan="5" class="table-loading-state">
            <div class="table-loading-spinner"></div>
            <span>Loading team members...</span>
          </td>
        </tr>
      `);
    },

    renderEmptyState: function (msg) {
      const self = this;
      const $tbody = $("#usersTableBody");
      $tbody.html(`
        <tr>
          <td colspan="5" class="table-empty-state" style="text-align: center; padding: 56px 24px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 16px;">
              <i class="fi fi-rr-cross-circle"></i>
            </div>
            <h4 style="font-size: 16px; color: #1e293b; margin: 0 0 6px 0; font-weight: 700;">Unable to Load Team Members</h4>
            <p style="color: #64748b; font-size: 13px; margin: 0 0 16px 0;">${selfEscapeHTML(msg)}</p>
            <button type="button" class="btn-v3-action" onclick="window.V3Users.fetchUsers();" style="padding: 8px 18px; font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
              <i class="fi fi-rr-refresh"></i> Retry
            </button>
          </td>
        </tr>
      `);
      $("#userPaginationBar").hide();
    },

    handleDeleteUser: function (userId, userName) {
      const self = this;
      if (!userId) return;

      const swalFn = window.swal || window.Swal;
      if (!swalFn) {
        if (confirm(`Are you sure you want to remove ${userName} from the team?`)) {
          self.executeDeleteUser(userId);
        }
        return;
      }

      swalFn({
        title: "Remove Team Member?",
        text: `Are you sure you want to revoke access for ${userName}?`,
        type: "warning",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#94a3b8",
        confirmButtonText: "Yes, Remove",
        cancelButtonText: "Cancel"
      }).then(function (result) {
        if (result && (result.value || result.isConfirmed === true || result === true)) {
          self.executeDeleteUser(userId);
        }
      });
    },

    executeDeleteUser: function (userId) {
      const self = this;

      $.ajax({
        url: self.apiEndpoint + "delete.php",
        method: "POST",
        dataType: "json",
        data: { id: userId },
        success: function (res) {
          const swalFn = window.swal || window.Swal;
          if (res && res.status === "success") {
            if (swalFn) {
              swalFn({
                title: "Removed!",
                text: res.msg || "User removed successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            }
            self.fetchUsers();
          } else {
            if (swalFn) {
              swalFn({
                title: "Cannot Remove",
                text: res ? res.msg : "Failed to remove user.",
                type: "error",
                icon: "error"
              });
            } else {
              alert(res ? res.msg : "Failed to remove user.");
            }
          }
        },
        error: function () {
          alert("Network error while removing user.");
        }
      });
    }
  };

  function selfEscapeHTML(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.V3Users = V3Users;
})(window, jQuery);
