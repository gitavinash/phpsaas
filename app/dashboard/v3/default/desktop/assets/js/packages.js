/**
 * V3 Tour Packages Management Module
 * Server-side pagination with in-memory caching and automatic background next-page prefetching.
 */
(function ($) {
  "use strict";

  window.V3Packages = {
    type: "domestic",
    isLoading: false,
    currentPage: 1,
    pageSize: 10,
    paginationMeta: null,
    pageCache: {},
    prefetchingPages: {},
    searchDebounceTimer: null,
    eventsBound: false,

    init: function (opts) {
      if (opts && opts.type) {
        this.type = opts.type;
      }
      this.currentPage = 1;
      this.pageSize = parseInt($("#pkgPerPage").val(), 10) || 10;
      this.pageCache = {};
      this.prefetchingPages = {};
      this.bindEvents();
      this.loadPage(this.currentPage, false);
    },

    autoInit: function () {
      const $wrapper = $("#packageListApp, .package-page-wrapper[data-package-type]");
      if ($wrapper.length > 0) {
        const pkgType = $wrapper.data("package-type") || $wrapper.attr("data-package-type") || "domestic";
        this.init({ type: pkgType });
      }
    },

    getFilterParams: function (page) {
      return {
        type: this.type,
        page: page || this.currentPage,
        limit: this.pageSize,
        search: ($("#pkgTableSearch").val() || "").trim(),
        status: ($("#filterStatus").val() || "").trim(),
        theme: ($("#filterTheme").val() || "").trim(),
        duration: ($("#filterDuration").val() || "").trim(),
        hotel: ($("#filterHotel").val() || "").trim(),
        destination: ($("#filterDestination").val() || "").trim()
      };
    },

    getCacheKey: function (params) {
      return [
        params.type,
        params.page,
        params.limit,
        params.search,
        params.status,
        params.theme,
        params.duration,
        params.hotel,
        params.destination
      ].join("||");
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // 1. Move Package Type Handler
      $(document).off("click.movePkg").on("click.movePkg", ".movePackageBtn", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const packageId = $btn.data("id");
        const targetType = $btn.data("target-type") || (self.type === "domestic" ? "international" : "domestic");
        const targetLabel = targetType === "international" ? "International" : "Domestic";

        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: `Move to ${targetLabel} Package?`,
            text: `Are you sure you want to move this package to ${targetLabel} packages?`,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0284c7",
            cancelButtonColor: "#64748b",
            confirmButtonText: `Yes, move to ${targetLabel} Package`
          }).then((result) => {
            if (result.value) {
              self.executeMovePackage(packageId, targetType);
            }
          });
        } else if (confirm(`Are you sure you want to move this package to ${targetLabel} packages?`)) {
          self.executeMovePackage(packageId, targetType);
        }
      });

      // 2. Real-time Search Input & Clear Trigger
      $(document).on("input", "#pkgTableSearch", function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#clearSearchBtn").show();
        } else {
          $("#clearSearchBtn").hide();
        }
        clearTimeout(self.searchDebounceTimer);
        self.searchDebounceTimer = setTimeout(function () {
          self.pageCache = {};
          self.loadPage(1, false);
        }, 280);
      });

      $(document).on("click", "#clearSearchBtn", function () {
        $("#pkgTableSearch").val("");
        $(this).hide();
        clearTimeout(self.searchDebounceTimer);
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 3. Dropdown Filter Change Handlers
      $(document).on(
        "change",
        "#filterStatus, #filterTheme, #filterDuration, #filterHotel, #filterDestination",
        function () {
          self.pageCache = {};
          self.loadPage(1, false);
        }
      );

      // 4. Reset All Filters
      $(document).on("click", "#resetPkgFilters", function () {
        $("#pkgTableSearch").val("");
        $("#clearSearchBtn").hide();
        $("#filterStatus").val("");
        $("#filterTheme").val("");
        $("#filterDuration").val("");
        $("#filterHotel").val("");
        $("#filterDestination").val("");
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 5. Pagination: Rows Per Page Selector
      $(document).on("change", "#pkgPerPage", function () {
        self.pageSize = parseInt($(this).val(), 10) || 10;
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 6. Pagination: Previous Page
      $(document).on("click", "#pkgPrevPage", function () {
        if (self.currentPage > 1) {
          self.loadPage(self.currentPage - 1, false);
        }
      });

      // 7. Pagination: Next Page
      $(document).on("click", "#pkgNextPage", function () {
        if (self.paginationMeta && self.paginationMeta.hasNextPage) {
          self.loadPage(self.currentPage + 1, false);
        }
      });

      // 8. Pagination: Specific Page Number Click
      $(document).on("click", ".pagination-page-btn[data-page]", function () {
        const targetPage = parseInt($(this).data("page"), 10);
        if (targetPage && targetPage !== self.currentPage) {
          self.loadPage(targetPage, false);
        }
      });
    },

    executeMovePackage: function (packageId, targetType) {
      const self = this;
      $.ajax({
        url: "/api/v3/packages/move-type/",
        type: "POST",
        data: {
          packageId: packageId,
          targetType: targetType
        },
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg, "success");
            }
            self.pageCache = {};
            self.loadPage(self.currentPage, false);
          } else {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.msg || "Failed to move package", "error");
            }
          }
        },
        error: function () {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Error moving tour package", "error");
          }
        }
      });
    },

    /**
     * Load a specific page.
     * Checks in-memory cache first for instantaneous rendering, otherwise fetches from API.
     * After loading, automatically triggers background prefetch for the next page.
     */
    loadPage: function (page, isBackgroundPrefetch) {
      const self = this;
      const params = self.getFilterParams(page);
      const cacheKey = self.getCacheKey(params);

      // If cached, render immediately!
      if (self.pageCache[cacheKey]) {
        const cachedRes = self.pageCache[cacheKey];
        if (!isBackgroundPrefetch) {
          self.currentPage = page;
          self.applyApiResponse(cachedRes);
          self.prefetchNextPage(page + 1);
        }
        return;
      }

      // If it's a background prefetch and already in progress, avoid duplicate request
      if (isBackgroundPrefetch) {
        if (self.prefetchingPages[cacheKey]) return;
        self.prefetchingPages[cacheKey] = true;
      } else {
        self.isLoading = true;
        self.currentPage = page;
        $("#packageTableBody").html(`
          <tr>
            <td colspan="4" class="table-loading-state">
              <div class="table-loading-spinner"></div>
              <span>Loading tour packages...</span>
            </td>
          </tr>
        `);
      }

      $.ajax({
        url: "/api/v3/packages/list/",
        type: "GET",
        data: params,
        dataType: "json",
        success: function (res) {
          if (isBackgroundPrefetch) {
            delete self.prefetchingPages[cacheKey];
          } else {
            self.isLoading = false;
          }

          if (res.status === "success") {
            self.pageCache[cacheKey] = res;

            if (!isBackgroundPrefetch) {
              self.applyApiResponse(res);
              // Always trigger next page background prefetch!
              self.prefetchNextPage(page + 1);
            }
          } else if (!isBackgroundPrefetch) {
            $("#packageTableBody").html(`
              <tr>
                <td colspan="4" class="table-loading-state" style="color: #ef4444;">
                  ${res.message || "Failed to load package data."}
                </td>
              </tr>
            `);
          }
        },
        error: function () {
          if (isBackgroundPrefetch) {
            delete self.prefetchingPages[cacheKey];
          } else {
            self.isLoading = false;
            $("#packageTableBody").html(`
              <tr>
                <td colspan="4" class="table-loading-state" style="color: #ef4444;">
                  Error connecting to server. Please refresh the page.
                </td>
              </tr>
            `);
          }
        }
      });
    },

    /**
     * Automatically pre-fetch the next page in the background.
     */
    prefetchNextPage: function (nextPage) {
      const self = this;
      if (!self.paginationMeta || !self.paginationMeta.hasNextPage) return;
      if (nextPage > self.paginationMeta.totalPages) return;

      const nextParams = self.getFilterParams(nextPage);
      const nextCacheKey = self.getCacheKey(nextParams);

      if (self.pageCache[nextCacheKey] || self.prefetchingPages[nextCacheKey]) return;

      // Silently pre-fetch next page data in the background
      setTimeout(function () {
        self.loadPage(nextPage, true);
      }, 60);
    },

    applyApiResponse: function (res) {
      const self = this;
      self.paginationMeta = res.pagination || {
        page: self.currentPage,
        limit: self.pageSize,
        totalRecords: (res.packages || []).length,
        totalPages: 1,
        hasNextPage: false,
        hasPrevPage: false
      };

      self.renderKpis(res.kpis);
      self.populateDestinations(res.destinations);
      self.renderPackageTable(res.packages, self.paginationMeta);
    },

    renderKpis: function (kpis) {
      if (!kpis) return;
      $("#statTotalPackages").text(kpis.total.toLocaleString());
      $("#statActivePackages").text(kpis.active.toLocaleString());
      $("#statDraftPackages").text(kpis.draft.toLocaleString());
      $("#statDestinations").text(kpis.destinations.toLocaleString());
    },

    populateDestinations: function (destinations) {
      const $select = $("#filterDestination");
      if ($select.find("option").length > 1) return; // already populated

      const currentVal = $select.val();
      const defaultLabel = this.type === "international" ? "All Countries" : "All Destinations";

      let optionsHtml = `<option value="">${defaultLabel}</option>`;
      if (Array.isArray(destinations)) {
        destinations.forEach(function (dest) {
          const val = dest.toLowerCase();
          optionsHtml += `<option value="${val}">${dest}</option>`;
        });
      }
      $select.html(optionsHtml);
      if (currentVal) {
        $select.val(currentVal);
      }
    },

    renderPackageTable: function (packages, pagination) {
      const self = this;
      const $tbody = $("#packageTableBody");
      const totalRecords = pagination.totalRecords || 0;

      if (!packages || packages.length === 0) {
        const hasSearchOrFilter = Boolean(
          $("#pkgTableSearch").val() ||
          $("#filterStatus").val() ||
          $("#filterTheme").val() ||
          $("#filterDuration").val() ||
          $("#filterHotel").val() ||
          $("#filterDestination").val()
        );

        if (hasSearchOrFilter) {
          $tbody.html(`
            <tr class="no-filter-match">
              <td colspan="4">
                <div class="empty-state">
                  <i class="fi-rr-search"></i>
                  <span>No packages matching your filter criteria. Try adjusting your filters or search terms.</span>
                </div>
              </td>
            </tr>
          `);
        } else {
          const typeLabel = self.type === "international" ? "international" : "domestic";
          $tbody.html(`
            <tr>
              <td colspan="4">
                <div class="empty-state">
                  <i class="fi-rr-folder-open"></i>
                  <span>No ${typeLabel} packages found. Click "+ Add Package" to create your first package.</span>
                </div>
              </td>
            </tr>
          `);
        }

        self.updatePaginationUI(0, 0, 0, 1);
        return;
      }

      let html = "";
      const isDomestic = self.type === "domestic";
      const targetMoveType = isDomestic ? "international" : "domestic";
      const targetMoveLabel = isDomestic ? "International Package" : "Domestic Package";
      const editBasePath = isDomestic ? "/dashboard/packages/domestic/" : "/dashboard/packages/international/";

      packages.forEach(function (pkg) {
        const pkgId = pkg.id;
        const encodedId = pkg.encodedId;
        const title = pkg.title;
        const theme = pkg.theme;
        const themeClass = pkg.themeClass;
        const themeLabel = pkg.themeLabel;
        const durationN = pkg.durationN;
        const durationD = pkg.durationD;
        const location = pkg.location;
        const hotelLabel = pkg.hotelLabel;
        const hotelRaw = pkg.hotel;
        const statusClass = pkg.statusClass;
        const statusLabel = pkg.statusLabel;
        const liveUrl = pkg.linkUrl;

        let liveBtnHtml = "";
        if (liveUrl) {
          liveBtnHtml = `
            <a href="${liveUrl}" target="_blank" class="btn-action-icon preview" data-tooltip="View Live Itinerary" title="View Live Itinerary">
              <i class="fi fi-rr-eye"></i>
            </a>
          `;
        }

        html += `
          <tr data-status="${statusLabel.toLowerCase()}"
              data-theme="${theme}"
              data-duration="${durationN}"
              data-hotel="${hotelRaw.toLowerCase()}"
              data-city="${location.toLowerCase()}">
            <!-- 1. Package Main Info -->
            <td class="col-pkg-main">
              <div class="pkg-main-block">
                <a href="${editBasePath}edit/?id=${encodedId}" class="pkg-title-link" title="${title}">
                  ${title}
                </a>
                <div class="pkg-meta-row">
                  <span class="pkg-theme-pill ${themeClass}">
                    ${themeLabel}
                  </span>
                  <span class="pkg-duration-badge">
                    <i class="fi-rr-moon pkg-duration-moon"></i> ${durationN}N / <i class="fi-rr-sun pkg-duration-sun"></i> ${durationD}D
                  </span>
                </div>
              </div>
            </td>

            <!-- 2. Destination & Hotel -->
            <td class="col-pkg-location">
              <div class="pkg-loc-hotel-block">
                <div class="pkg-loc-line" title="${location}">
                  <i class="fi-rr-marker loc-icon"></i>
                  <span class="loc-text">${location}</span>
                </div>
                <div class="pkg-hotel-line">
                  <i class="fi-rr-hotel hotel-icon"></i>
                  <span class="hotel-text">${hotelLabel}</span>
                </div>
              </div>
            </td>

            <!-- 3. Status Badge -->
            <td class="col-pkg-status">
              <span class="v3-status-badge ${statusClass}">
                <span class="status-pulse-dot"></span> ${statusLabel}
              </span>
            </td>

            <!-- 4. Actions Toolbar -->
            <td class="col-pkg-actions">
              <div class="pkg-actions-toolbar">
                <a href="${editBasePath}edit/?id=${encodedId}" class="btn-action-pill" data-tooltip="Edit Package Details" title="Edit Package Details">
                  <i class="fi fi-rr-edit"></i> <span>Edit</span>
                </a>
                ${liveBtnHtml}
                <div class="btn-action-divider"></div>
                <a href="${editBasePath}price-list/?id=${encodedId}" class="btn-action-icon price" data-tooltip="Pricing & Plans" title="Pricing & Plans">
                  <i class="fi fi-rr-indian-rupee-sign"></i>
                </a>
                <a href="${editBasePath}hotel-list/?id=${encodedId}" class="btn-action-icon hotel" data-tooltip="Hotel Configuration" title="Hotel Configuration">
                  <i class="fi fi-rr-hotel"></i>
                </a>
                <a href="${editBasePath}gallery/?id=${encodedId}" class="btn-action-icon gallery" data-tooltip="Photo Gallery" title="Photo Gallery">
                  <i class="fi fi-rr-gallery"></i>
                </a>
                <a href="${editBasePath}transpotation-list/?id=${encodedId}" class="btn-action-icon transport" data-tooltip="Transport Options" title="Transport Options">
                  <i class="fi fi-rr-cars"></i>
                </a>
                <div class="btn-action-divider"></div>
                <a href="#" class="btn-action-icon move movePackageBtn" data-id="${pkgId}" data-target-type="${targetMoveType}" data-tooltip="Move to ${targetMoveLabel}" title="Move to ${targetMoveLabel}">
                  <i class="fi fi-rr-plane-alt"></i>
                </a>
                <a href="#" data-type="packages" data-id="${pkgId}" data-title="Delete package" class="btn-action-icon delete deleteButton" data-redirect="false" data-tooltip="Delete Package" title="Delete Package">
                  <i class="fi fi-rr-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);

      const startIdx = (pagination.page - 1) * pagination.limit;
      const endIdx = Math.min(startIdx + packages.length, totalRecords);
      self.updatePaginationUI(startIdx, endIdx, totalRecords, pagination.totalPages);
    },

    updatePaginationUI: function (startIdx, endIdx, totalCount, totalPages) {
      const self = this;

      // 1. Summary Info Text
      if (totalCount === 0) {
        $("#pkgPaginationInfo").text("Showing 0 of 0 packages");
      } else {
        $("#pkgPaginationInfo").text(
          `Showing ${(startIdx + 1)} – ${endIdx} of ${totalCount.toLocaleString()} packages`
        );
      }

      // 2. Prev / Next Navigation Buttons
      $("#pkgPrevPage").prop("disabled", self.currentPage <= 1);
      $("#pkgNextPage").prop("disabled", self.currentPage >= totalPages || totalCount === 0);

      // 3. Numbered Pagination Buttons with Ellipses
      let pagesHtml = "";
      if (totalPages <= 1 || totalCount === 0) {
        $("#pkgPaginationPages").html("");
        return;
      }

      const maxButtons = 5;
      let startPage = Math.max(1, self.currentPage - 2);
      let endPage = Math.min(totalPages, startPage + maxButtons - 1);
      if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
      }

      if (startPage > 1) {
        pagesHtml += `<button type="button" class="pagination-page-btn" data-page="1">1</button>`;
        if (startPage > 2) {
          pagesHtml += `<span class="pagination-page-btn ellipsis">...</span>`;
        }
      }

      for (let p = startPage; p <= endPage; p++) {
        const activeClass = p === self.currentPage ? "active" : "";
        pagesHtml += `<button type="button" class="pagination-page-btn ${activeClass}" data-page="${p}">${p}</button>`;
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          pagesHtml += `<span class="pagination-page-btn ellipsis">...</span>`;
        }
        pagesHtml += `<button type="button" class="pagination-page-btn" data-page="${totalPages}">${totalPages}</button>`;
      }

      $("#pkgPaginationPages").html(pagesHtml);
    }
  };

  // Auto-initialize on direct page load & SPA navigation
  $(document).ready(function () {
    window.V3Packages.autoInit();
  });

  $(document).on("spa:loaded", function () {
    window.V3Packages.autoInit();
  });
})(jQuery);
