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
      const $tbody = $("#packageTableBody");
      if (!$tbody.length) {
        return;
      }
      const newType = opts && opts.type ? opts.type : "domestic";
      this.type = newType;
      this.isInitialized = true;
      this.bindEvents();
      this.initDataTable();
    },

    autoInit: function () {
      const $wrapper = $("#packageListApp, .package-list-wrapper");
      if ($wrapper.length > 0) {
        const pkgType = $wrapper.data("package-type") || $wrapper.attr("data-package-type") || "domestic";
        this.init({ type: pkgType });
      } else {
        this.isInitialized = false;
        this.tableInstance = null;
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
                <td colspan="5">
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
              <td colspan="5">
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
      const hotelRaw = pkg.hotel || "";
      const hotelCount = pkg.hotelCount || 0;
      const route = pkg.route || "";
      const vehicle = pkg.vehicle || "";
      const statusClass = pkg.statusClass;
      const statusLabel = pkg.statusLabel;
      const updatedAtHuman = pkg.updatedAtHuman || "";
      const liveUrl = pkg.linkUrl;
      const pricing = pkg.pricing || null;

      let liveBtnHtml = "";
      if (liveUrl) {
        liveBtnHtml = `
          <a href="${liveUrl}" target="_blank" class="btn-action-icon preview" data-tooltip="View Live Itinerary" title="View Live Itinerary">
            <i class="fi fi-rr-eye"></i>
          </a>
        `;
      }

      // 1. Vehicle Meta Badge
      let vehicleHtml = "";
      if (vehicle) {
        vehicleHtml = `
          <span class="pkg-vehicle-badge" title="Fleet / Vehicles: ${vehicle}">
            <i class="fi fi-rr-car-alt"></i> ${vehicle}
          </span>
        `;
      }

      // 2. Route Meta Line
      let routeHtml = "";
      if (route && route.toLowerCase() !== location.toLowerCase()) {
        routeHtml = `
          <div class="pkg-route-line" title="Route: ${route}">
            <i class="fi fi-rr-route route-icon"></i>
            <span>${route}</span>
          </div>
        `;
      }

      // 3. Hotel Count Tag
      let hotelCountHtml = "";
      if (hotelCount > 0) {
        hotelCountHtml = `
          <span class="pkg-assigned-hotels-chip" title="${hotelCount} specific property/properties configured">
            <i class="fi fi-rr-bed"></i> ${hotelCount}
          </span>
        `;
      }

      // 4. Pricing Block Construction
      let pricingHtml = "";
      if (pricing && pricing.hasPricing) {
        const mrpHtml = (pricing.mrpDisplay && pricing.minMrp > pricing.minSelling)
          ? `<span class="pkg-price-mrp" title="Rack Rate / MRP">${pricing.mrpDisplay}</span>`
          : "";

        let b2bHtml = "";
        if (pricing.netDisplay || pricing.agentDisplay) {
          const netPart = pricing.netDisplay
            ? `<span class="pkg-net-tag" title="Net Cost to Company (Supplier Base)">Net: <strong>${pricing.netDisplay}</strong></span>`
            : "";
          const agentPart = pricing.agentDisplay
            ? `<span class="pkg-agent-tag" title="B2B Agent Price">B2B: <strong>${pricing.agentDisplay}</strong></span>`
            : "";
          b2bHtml = `${netPart}${agentPart}`;
        }

        const typesPart = pricing.types
          ? `<span class="pkg-slab-types">(${pricing.types})</span>`
          : "";
        const slabsCountLabel = `${pricing.totalSlabs} Plan${pricing.totalSlabs > 1 ? "s" : ""}`;

        pricingHtml = `
          <div class="pkg-pricing-block">
            <div class="pkg-price-main-line">
              <span class="pkg-price-amount">${pricing.sellingDisplay || "₹0"}</span>
              <span class="pkg-price-unit">/ adult</span>
              ${mrpHtml}
            </div>
            <div class="pkg-price-sub-line">
              ${b2bHtml}
              <a href="${editBasePath}price-list/?id=${encodedId}" class="pkg-slabs-tag" title="Click to view & manage pricing plans">
                <i class="fi fi-rr-layers"></i> <span>${slabsCountLabel}</span> ${typesPart}
              </a>
            </div>
          </div>
        `;
      } else {
        pricingHtml = `
          <div class="pkg-no-price-block">
            <span class="pkg-price-missing" title="No pricing slabs configured yet">
              <i class="fi fi-rr-exclamation"></i> No price set
            </span>
            <a href="${editBasePath}price-list/?id=${encodedId}" class="btn-quick-add-price" title="Add Pricing Plans">
              <i class="fi fi-rr-plus"></i> Set Price
            </a>
          </div>
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
                <span class="pkg-id-chip" title="Package ID">#${pkgId}</span>
                <span class="pkg-theme-pill ${themeClass}">
                  ${themeLabel}
                </span>
                <span class="pkg-duration-badge">
                  <i class="fi-rr-moon pkg-duration-moon"></i> ${durationN}N / <i class="fi-rr-sun pkg-duration-sun"></i> ${durationD}D
                </span>
                ${vehicleHtml}
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
              ${routeHtml}
              <div class="pkg-hotel-line">
                <i class="fi-rr-hotel hotel-icon"></i>
                <span class="hotel-text">${hotelLabel}</span>
                ${hotelCountHtml}
              </div>
            </div>
          </td>

          <!-- 3. Pricing & Plans (New Dedicated Column for Backend Team) -->
          <td class="col-pkg-pricing">
            ${pricingHtml}
          </td>

          <!-- 4. Status -->
          <td class="col-pkg-status">
            <span class="v3-status-badge ${statusClass}">
              <span class="status-pulse-dot"></span> ${statusLabel}
            </span>
          </td>

          <!-- 5. Actions Toolbar -->
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
});
