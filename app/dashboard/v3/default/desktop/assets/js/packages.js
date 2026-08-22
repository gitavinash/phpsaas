/**
 * V3 Tour Packages Management Module
 * Powered by the global V3DataTable engine (table.js).
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

  window.V3Packages = {
    type: "domestic",
    tableInstance: null,
    eventsBound: false,

    init: function (opts) {
      if (opts && opts.type) {
        this.type = opts.type;
      }
      this.bindEvents();
      this.initDataTable();
    },

    autoInit: function () {
      const $wrapper = $("#packageListApp, .package-page-wrapper[data-package-type]");
      if ($wrapper.length > 0) {
        const pkgType = $wrapper.data("package-type") || $wrapper.attr("data-package-type") || "domestic";
        this.init({ type: pkgType });
      }
    },

    initDataTable: function () {
      const self = this;
      if (typeof window.V3Table === "undefined" || typeof window.V3Table.create !== "function") {
        return;
      }

      self.tableInstance = window.V3Table.create({
        tableSelector: ".package-v3-datatable",
        tbodySelector: "#packageTableBody",
        apiEndpoint: "/api/v3/packages/list/",
        entityName: "packages",
        getParams: function () {
          return {
            type: self.type
          };
        },
        searchSelector: "#pkgTableSearch",
        clearSearchSelector: "#clearSearchBtn",
        filterSelectors: "#filterStatus, #filterTheme, #filterDuration, #filterHotel, #filterDestination",
        resetFiltersSelector: "#resetPkgFilters",
        paginationBarSelector: "#packagePaginationBar",
        perPageSelector: "#pkgPerPage",
        prevPageSelector: "#pkgPrevPage",
        nextPageSelector: "#pkgNextPage",
        pagesContainerSelector: "#pkgPaginationPages",
        infoSelector: "#pkgPaginationInfo",
        defaultPageSize: 10,
        renderRow: function (pkg) {
          return self.renderPackageRow(pkg);
        },
        renderEmpty: function (isFiltered) {
          if (isFiltered) {
            return `
              <tr class="no-filter-match">
                <td colspan="4">
                  <div class="empty-state">
                    <i class="fi-rr-search"></i>
                    <span>No packages matching your filter criteria. Try adjusting your filters or search terms.</span>
                  </div>
                </td>
              </tr>
            `;
          }
          const typeLabel = self.type === "international" ? "international" : "domestic";
          return `
            <tr>
              <td colspan="4">
                <div class="empty-state">
                  <i class="fi-rr-folder-open"></i>
                  <span>No ${typeLabel} packages found. Click "+ Add Package" to create your first package.</span>
                </div>
              </td>
            </tr>
          `;
        },
        onDataLoaded: function (res) {
          self.renderKpis(res.kpis);
          self.populateDestinations(res.destinations);
        }
      });
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
            if (self.tableInstance) {
              self.tableInstance.refresh();
            }
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

    renderKpis: function (kpis) {
      if (!kpis) return;
      $("#statTotalPackages").text((kpis.total || 0).toLocaleString());
      $("#statActivePackages").text((kpis.active || 0).toLocaleString());
      $("#statDraftPackages").text((kpis.draft || 0).toLocaleString());
      $("#statDestinations").text((kpis.destinations || 0).toLocaleString());
    },

    populateDestinations: function (destinations) {
      const $select = $("#filterDestination");
      if ($select.find("option").length > 1) return;

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

    renderPackageRow: function (pkg) {
      const self = this;
      const isDomestic = self.type === "domestic";
      const targetMoveType = isDomestic ? "international" : "domestic";
      const targetMoveLabel = isDomestic ? "International Package" : "Domestic Package";
      const editBasePath = isDomestic ? "/dashboard/packages/domestic/" : "/dashboard/packages/international/";

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

      return `
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
    }
  };

  // Auto-initialize on direct page load & SPA navigation
  if (window.V3Packages && typeof window.V3Packages.autoInit === "function") {
    window.V3Packages.autoInit();
  }
  $(document).ready(function () {
    window.V3Packages.autoInit();
  });

  $(document).on("spa:loaded", function () {
    window.V3Packages.autoInit();
  });
});
