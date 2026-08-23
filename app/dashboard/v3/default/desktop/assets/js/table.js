/**
 * V3 Global Reusable DataTable Module
 * Provides server-side pagination, instant in-memory caching, background prefetching,
 * search debouncing, multi-filter synchronization, and UI rendering for any table in V3.
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

  function V3DataTable(options) {
    this.options = $.extend(
      {
        tableSelector: ".package-v3-datatable, .v3-datatable",
        tbodySelector: "#packageTableBody, .v3-table-body, tbody",
        apiEndpoint: "",
        getParams: function () {
          return {};
        },

        // Search & Filters
        searchSelector: "#pkgTableSearch, .filter-search-input, .table-search-input",
        clearSearchSelector: "#clearSearchBtn, .clear-search-btn",
        filterSelectors:
          "#filterStatus, #filterTheme, #filterDuration, #filterHotel, #filterDestination, .v3-table-filter",
        resetFiltersSelector: "#resetPkgFilters, .btn-filter-reset",

        // Pagination Elements
        paginationBarSelector: "#packagePaginationBar, .package-pagination-bar",
        perPageSelector: "#pkgPerPage, .pagination-size-select",
        prevPageSelector: "#pkgPrevPage, .pagination-nav-btn.prev, #pkgPrevPage",
        nextPageSelector: "#pkgNextPage, .pagination-nav-btn.next, #pkgNextPage",
        pagesContainerSelector: "#pkgPaginationPages, .pagination-page-numbers",
        infoSelector: "#pkgPaginationInfo, .pagination-info-text",
        defaultPageSize: 10,
        maxPageButtons: 5,
        entityName: "records",

        // Callbacks
        renderRow: function (item, index) {
          return "";
        },
        renderEmpty: null,
        onDataLoaded: null,
        onError: null
      },
      options || {}
    );

    this.currentPage = 1;
    this.pageSize = this.options.defaultPageSize;
    this.paginationMeta = null;
    this.pageCache = {};
    this.prefetchingPages = {};
    this.searchDebounceTimer = null;
    this.isLoading = false;

    this.init();
  }

  V3DataTable.prototype = {
    init: function () {
      const $table = $(this.options.tableSelector);
      const $tbody = $(this.options.tbodySelector);
      if (!$table.length || !$tbody.length) {
        return;
      }
      const $perPage = $(this.options.perPageSelector);
      if ($perPage.length > 0) {
        this.pageSize = parseInt($perPage.val(), 10) || this.options.defaultPageSize;
      }
      this.bindEvents();
      this.loadPage(this.currentPage, false);
    },

    getQueryParams: function (page) {
      const userParams =
        typeof this.options.getParams === "function"
          ? this.options.getParams(page || this.currentPage, this.pageSize)
          : this.options.getParams || {};

      const searchVal = ($(this.options.searchSelector).val() || "").trim();

      const filterValues = {};
      $(this.options.filterSelectors).each(function () {
        const id = $(this).attr("id") || $(this).attr("name");
        if (id) {
          const key = id.replace(/^filter/, "").toLowerCase();
          const val = $(this).val();
          if (val !== null && val !== undefined) {
            filterValues[key] = (val + "").trim();
          }
        }
      });

      return $.extend(
        {
          page: page || this.currentPage,
          limit: this.pageSize,
          search: searchVal
        },
        filterValues,
        userParams
      );
    },

    getCacheKey: function (params) {
      const keys = Object.keys(params).sort();
      return keys
        .map(function (k) {
          return k + "=" + encodeURIComponent(params[k]);
        })
        .join("&");
    },

    bindEvents: function () {
      const self = this;

      // 1. Live Search with Debounce
      $(document).off("input.v3dt", self.options.searchSelector).on("input.v3dt", self.options.searchSelector, function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $(self.options.clearSearchSelector).show();
        } else {
          $(self.options.clearSearchSelector).hide();
        }

        clearTimeout(self.searchDebounceTimer);
        self.searchDebounceTimer = setTimeout(function () {
          self.pageCache = {};
          self.loadPage(1, false);
        }, 280);
      });

      // 2. Clear Search
      $(document).off("click.v3dt", self.options.clearSearchSelector).on("click.v3dt", self.options.clearSearchSelector, function () {
        $(self.options.searchSelector).val("");
        $(this).hide();
        clearTimeout(self.searchDebounceTimer);
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 3. Dropdown Filters
      $(document).off("change.v3dt", self.options.filterSelectors).on("change.v3dt", self.options.filterSelectors, function () {
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 4. Reset All Filters
      $(document).off("click.v3dt", self.options.resetFiltersSelector).on("click.v3dt", self.options.resetFiltersSelector, function () {
        $(self.options.searchSelector).val("");
        $(self.options.clearSearchSelector).hide();
        $(self.options.filterSelectors).val("");
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 5. Rows Per Page Change
      $(document).off("change.v3dt", self.options.perPageSelector).on("change.v3dt", self.options.perPageSelector, function () {
        self.pageSize = parseInt($(this).val(), 10) || self.options.defaultPageSize;
        self.pageCache = {};
        self.loadPage(1, false);
      });

      // 6. Previous Page
      $(document).off("click.v3dt", self.options.prevPageSelector).on("click.v3dt", self.options.prevPageSelector, function () {
        if (self.currentPage > 1) {
          self.loadPage(self.currentPage - 1, false);
        }
      });

      // 7. Next Page
      $(document).off("click.v3dt", self.options.nextPageSelector).on("click.v3dt", self.options.nextPageSelector, function () {
        if (self.paginationMeta && self.paginationMeta.hasNextPage) {
          self.loadPage(self.currentPage + 1, false);
        }
      });

      // 8. Specific Page Number
      $(document).off("click.v3dt", ".pagination-page-btn[data-page]").on("click.v3dt", ".pagination-page-btn[data-page]", function () {
        const targetPage = parseInt($(this).data("page"), 10);
        if (targetPage && targetPage !== self.currentPage) {
          self.loadPage(targetPage, false);
        }
      });
    },

    loadPage: function (page, isBackgroundPrefetch) {
      const self = this;
      if (!self.options.apiEndpoint) return;

      const params = self.getQueryParams(page);
      const cacheKey = self.getCacheKey(params);

      // Instant in-memory cache hit
      if (self.pageCache[cacheKey]) {
        const cachedRes = self.pageCache[cacheKey];
        if (!isBackgroundPrefetch) {
          self.currentPage = page;
          self.applyApiResponse(cachedRes);
        }
        return;
      }

      if (isBackgroundPrefetch) {
        if (self.prefetchingPages[cacheKey]) return;
        self.prefetchingPages[cacheKey] = true;
      } else {
        if (self.isLoading) return;
        self.isLoading = true;
        self.currentPage = page;
        const $tbody = $(self.options.tbodySelector);
        $tbody.html(`
          <tr>
            <td colspan="100" class="table-loading-state">
              <div class="table-loading-spinner"></div>
              <span>Loading ${self.options.entityName}...</span>
            </td>
          </tr>
        `);
      }

      $.ajax({
        url: self.options.apiEndpoint,
        type: "GET",
        data: params,
        dataType: "json",
        success: function (res) {
          if (isBackgroundPrefetch) {
            delete self.prefetchingPages[cacheKey];
          } else {
            self.isLoading = false;
          }

          if (res && res.status === "success") {
            self.pageCache[cacheKey] = res;

            if (!isBackgroundPrefetch) {
              self.applyApiResponse(res);
            }
          } else if (!isBackgroundPrefetch) {
            $(self.options.tbodySelector).html(`
              <tr>
                <td colspan="100" class="table-loading-state" style="color: #ef4444;">
                  ${res && res.message ? res.message : "Failed to load records."}
                </td>
              </tr>
            `);
            if (typeof self.options.onError === "function") {
              self.options.onError(res);
            }
          }
        },
        error: function (err) {
          if (isBackgroundPrefetch) {
            delete self.prefetchingPages[cacheKey];
          } else {
            self.isLoading = false;
            $(self.options.tbodySelector).html(`
              <tr>
                <td colspan="100" class="table-loading-state" style="color: #ef4444;">
                  Error connecting to server. Please refresh the page.
                </td>
              </tr>
            `);
            if (typeof self.options.onError === "function") {
              self.options.onError(err);
            }
          }
        }
      });
    },

    prefetchNextPage: function (nextPage) {
      const self = this;
      if (!self.paginationMeta || !self.paginationMeta.hasNextPage) return;
      if (nextPage > self.paginationMeta.totalPages) return;

      const nextParams = self.getQueryParams(nextPage);
      const nextCacheKey = self.getCacheKey(nextParams);

      if (self.pageCache[nextCacheKey] || self.prefetchingPages[nextCacheKey]) return;

      setTimeout(function () {
        self.loadPage(nextPage, true);
      }, 60);
    },

    applyApiResponse: function (res) {
      const self = this;
      const items = res.packages || res.records || res.data || res.items || res.list || [];

      self.paginationMeta = res.pagination || {
        page: self.currentPage,
        limit: self.pageSize,
        totalRecords: items.length,
        totalPages: Math.ceil(items.length / self.pageSize) || 1,
        hasNextPage: false,
        hasPrevPage: false
      };

      if (typeof self.options.onDataLoaded === "function") {
        self.options.onDataLoaded(res);
      }

      self.renderTable(items, self.paginationMeta);
    },

    renderTable: function (items, pagination) {
      const self = this;
      const $tbody = $(self.options.tbodySelector);
      const totalRecords = pagination.totalRecords || 0;

      if (!items || items.length === 0) {
        if (typeof self.options.renderEmpty === "function") {
          $tbody.html(self.options.renderEmpty(self.hasActiveFilters()));
        } else {
          $tbody.html(`
            <tr class="no-filter-match">
              <td colspan="100">
                <div class="empty-state">
                  <i class="fi-rr-search"></i>
                  <span>No ${self.options.entityName} found matching your criteria.</span>
                </div>
              </td>
            </tr>
          `);
        }

        self.updatePaginationUI(0, 0, 0, 1);
        return;
      }

      let rowsHtml = "";
      items.forEach(function (item, index) {
        if (typeof self.options.renderRow === "function") {
          rowsHtml += self.options.renderRow(item, index);
        }
      });

      $tbody.html(rowsHtml);

      const startIdx = (pagination.page - 1) * pagination.limit;
      const endIdx = Math.min(startIdx + items.length, totalRecords);
      self.updatePaginationUI(startIdx, endIdx, totalRecords, pagination.totalPages);
    },

    hasActiveFilters: function () {
      const searchVal = ($(this.options.searchSelector).val() || "").trim();
      let hasFilters = searchVal.length > 0;
      $(this.options.filterSelectors).each(function () {
        if ($(this).val()) {
          hasFilters = true;
        }
      });
      return hasFilters;
    },

    updatePaginationUI: function (startIdx, endIdx, totalCount, totalPages) {
      const self = this;
      const entity = self.options.entityName;

      // 1. Text Summary
      if (totalCount === 0) {
        $(self.options.infoSelector).text(`Showing 0 of 0 ${entity}`);
      } else {
        $(self.options.infoSelector).text(
          `Showing ${(startIdx + 1)} – ${endIdx} of ${totalCount.toLocaleString()} ${entity}`
        );
      }

      // 2. Prev / Next Navigation Buttons
      $(self.options.prevPageSelector).prop("disabled", self.currentPage <= 1);
      $(self.options.nextPageSelector).prop("disabled", self.currentPage >= totalPages || totalCount === 0);

      // 3. Numbered Pagination Buttons with Ellipses
      let pagesHtml = "";
      if (totalPages <= 1 || totalCount === 0) {
        $(self.options.pagesContainerSelector).html("");
        return;
      }

      const maxButtons = self.options.maxPageButtons || 5;
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

      $(self.options.pagesContainerSelector).html(pagesHtml);
    },

    refresh: function () {
      this.pageCache = {};
      this.loadPage(this.currentPage, false);
    },

    reset: function () {
      this.pageCache = {};
      this.loadPage(1, false);
    }
  };

  window.V3DataTable = V3DataTable;
  window.V3Table = {
    create: function (options) {
      return new V3DataTable(options);
    }
  };
});
