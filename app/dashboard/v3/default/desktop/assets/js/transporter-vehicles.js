/**
 * V3 Fleet & Vehicles Master Frontend Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/transporter-vehicles.js
 */

(function (window, $) {
  "use strict";

  const V3TransporterVehicles = {
    apiEndpoint: "/api/v3/vehicles/",
    allVehicles: [],
    categoriesMap: {},
    activeCategory: "all",
    searchQuery: "",
    selectedFile: null,
    editingVehicleId: null,
    transporterId: null,
    masterVehicles: [],
    selectedFleetTypes: [],
    initialized: false,
    initializedRoot: null,

    init: function () {
      const $root = $("#vehiclesMasterApp");
      if (!$root.length) return;

      this.transporterId = $root.data("transporter-id") || null;
      if ($root.data("fleet-scope") === "transporter" && !this.transporterId) {
        this.renderEmptyState(
          "Select a transporter to view its assigned fleet.",
        );
        return;
      }
      if (this.initialized && this.initializedRoot === $root[0]) return;
      if (this.activeXhr && typeof this.activeXhr.abort === "function") {
        this.activeXhr.abort();
      }
      this.initialized = true;
      this.initializedRoot = $root[0];
      this.allVehicles = [];
      this.masterVehicles = [];
      this.bindEvents();
      this.fetchVehicles();
    },

    bindEvents: function () {
      const self = this;

      // Filter Tabs
      $(document)
        .off("click", "#vehicleFilterTabs .theme-filter-btn")
        .on("click", "#vehicleFilterTabs .theme-filter-btn", function (e) {
          e.preventDefault();
          $("#vehicleFilterTabs .theme-filter-btn").removeClass("active");
          $(this).addClass("active");
          self.activeCategory = $(this).data("category") || "all";
          self.applyFilters();
        });

      // Category Dropdown Filter
      $(document)
        .off("change", "#vehicleCategoryDropdown")
        .on("change", "#vehicleCategoryDropdown", function () {
          const selectedVal = $(this).val();
          if (selectedVal) {
            self.activeCategory = selectedVal;
            // Deactivate tabs if specific subcategory chosen
            $("#vehicleFilterTabs .theme-filter-btn").removeClass("active");
          } else {
            self.activeCategory = "all";
            $(
              '#vehicleFilterTabs .theme-filter-btn[data-category="all"]',
            ).addClass("active");
          }
          self.applyFilters();
        });

      // Search Filter with Debounce
      let searchTimer = null;
      $(document)
        .off("input", "#vehicleSearchInput")
        .on("input", "#vehicleSearchInput", function () {
          const val = $(this).val().trim();
          $("#btnVehicleSearchClear").toggleClass("is-visible", val.length > 0);
          clearTimeout(searchTimer);
          searchTimer = setTimeout(function () {
            self.searchQuery = val.toLowerCase();
            self.applyFilters();
          }, 180);
        });

      // Clear Search
      $(document)
        .off("click", "#btnVehicleSearchClear")
        .on("click", "#btnVehicleSearchClear", function () {
          $("#vehicleSearchInput").val("").trigger("input").focus();
          $(this).removeClass("is-visible");
        });

      // Reset Filters
      $(document)
        .off("click", "#btnResetVehicleFilters")
        .on("click", "#btnResetVehicleFilters", function () {
          self.activeCategory = "all";
          self.searchQuery = "";
          $("#vehicleSearchInput").val("");
          $("#btnVehicleSearchClear").removeClass("is-visible");
          $("#vehicleCategoryDropdown").val("");
          $("#vehicleFilterTabs .theme-filter-btn").removeClass("active");
          $(
            '#vehicleFilterTabs .theme-filter-btn[data-category="all"]',
          ).addClass("active");
          self.applyFilters();
        });

      // View Switcher (Grid vs Table)
      $(document)
        .off("click", "#btnViewGrid, #btnViewTable")
        .on("click", "#btnViewGrid, #btnViewTable", function (e) {
          e.preventDefault();
          const view = $(this).data("view") || "grid";
          self.switchView(view);
        });

      // Open Add Modal
      $(document)
        .off(
          "click",
          "#btnOpenAddVehicleModal, #vehiclesEmptyState .btn-add-vehicle-v3",
        )
        .on(
          "click",
          "#btnOpenAddVehicleModal, #vehiclesEmptyState .btn-add-vehicle-v3",
          function () {
            self.openAddModal();
          },
        );

      $(document)
        .off("click", "#btnCreateMasterVehicle")
        .on("click", "#btnCreateMasterVehicle", function () {
          window.location.href = "/dashboard/vendors/transporter/vehicle/";
        });

      $(document)
        .off("input", "#transporterFleetSearch")
        .on("input", "#transporterFleetSearch", function () {
          const query = ($(this).val() || "").toLowerCase().trim();
          $(".transporter-fleet-check-item").each(function () {
            $(this).toggle(
              !query || $(this).text().toLowerCase().indexOf(query) !== -1,
            );
          });
        });
      $(document)
        .off("change", ".transporter-fleet-checkbox")
        .on("change", ".transporter-fleet-checkbox", function () {
          self.updateFleetSelectedCount();
        });
      $(document)
        .off("click", "#btnSelectAllFleet")
        .on("click", "#btnSelectAllFleet", function () {
          $(".transporter-fleet-check-item:visible .transporter-fleet-checkbox")
            .prop("checked", true)
            .trigger("change");
        });
      $(document)
        .off("click", "#btnClearAllFleet")
        .on("click", "#btnClearAllFleet", function () {
          $(".transporter-fleet-checkbox")
            .prop("checked", false)
            .trigger("change");
        });

      // Close Modal
      $(document)
        .off("click", "#btnCloseVehicleModal, #btnCancelVehicleModal")
        .on(
          "click",
          "#btnCloseVehicleModal, #btnCancelVehicleModal",
          function () {
            self.closeModal();
          },
        );

      // Close modal on background click
      $(document)
        .off("click", "#vehicleModalOverlay")
        .on("click", "#vehicleModalOverlay", function (e) {
          if ($(e.target).is("#vehicleModalOverlay")) {
            self.closeModal();
          }
        });

      // Modal File Upload Click
      $(document)
        .off("click", "#modalUploadPlaceholder")
        .on("click", "#modalUploadPlaceholder", function (e) {
          if (!$(e.target).is("#modalVehicleFileInput")) {
            $("#modalVehicleFileInput").trigger("click");
          }
        });

      // Modal File Input Change
      $(document)
        .off("change", "#modalVehicleFileInput")
        .on("change", "#modalVehicleFileInput", function (e) {
          const file = e.target.files && e.target.files[0];
          if (file) {
            self.handleFilePreview(file);
          }
        });

      // Remove Preview Photo
      $(document)
        .off("click", "#btnRemoveImagePreview")
        .on("click", "#btnRemoveImagePreview", function (e) {
          e.stopPropagation();
          self.clearFilePreview();
        });

      // Modal Drag & Drop Zone
      $(document)
        .off("dragover dragenter", "#vehicleUploadZone")
        .on("dragover dragenter", "#vehicleUploadZone", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass("dragover");
        });

      $(document)
        .off("dragleave drop", "#vehicleUploadZone")
        .on("dragleave drop", "#vehicleUploadZone", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass("dragover");
          if (e.type === "drop") {
            const files =
              e.originalEvent.dataTransfer &&
              e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
              self.handleFilePreview(files[0]);
            }
          }
        });

      // Form Submit
      $(document)
        .off("submit", "#vehicleForm")
        .on("submit", "#vehicleForm", function (e) {
          e.preventDefault();
          self.handleFormSubmit();
        });

      // Edit Button in Cards / Table
      $(document)
        .off("click", ".btn-edit-vehicle")
        .on("click", ".btn-edit-vehicle", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).data("id");
          self.openEditModal(id);
        });

      // Delete Button in Cards / Table
      $(document)
        .off("click", ".btn-delete-vehicle")
        .on("click", ".btn-delete-vehicle", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).data("id");
          const title = $(this).data("title") || "this vehicle";
          self.handleDeleteVehicle(id, title);
        });
    },

    switchView: function (view) {
      this.currentView = view;
      try {
        localStorage.setItem("v3_vehicle_view_mode", view);
      } catch (e) {}

      $(".vehicle-view-btn").removeClass("active");
      if (view === "grid") {
        $("#btnViewGrid").addClass("active");
      } else {
        $("#btnViewTable").addClass("active");
      }
      this.applyFilters();
    },

    fetchVehicles: function () {
      const self = this;
      self.renderSkeletons();

      const requestData = self.transporterId
        ? { transporter_id: self.transporterId }
        : {};

      self.activeXhr = $.ajax({
        url: self.apiEndpoint,
        method: "GET",
        data: requestData,
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            self.activeXhr = null;
            self.allVehicles = res.data || res.vehicles || [];
            self.masterVehicles = res.master_vehicles || self.allVehicles;
            self.categoriesMap = res.categories || {};
            self.renderKPIs(res.metrics || {});
            self.renderFleetChecklist();
            self.applyFilters();
          } else {
            self.renderEmptyState(res ? res.msg : "Failed to load vehicles.");
          }
        },
        error: function (xhr, status) {
          if (status === "abort") return;
          self.activeXhr = null;
          self.renderEmptyState("Network error while loading fleet vehicles.");
        },
      });
    },

    renderKPIs: function (metrics) {
      const assignedVehicles = this.allVehicles || [];
      const assignedMetrics = assignedVehicles.reduce(
        function (summary, vehicle) {
          const type = (vehicle.type || "").toLowerCase();
          summary.total += 1;
          if (["mpv", "suv"].indexOf(type) !== -1) summary.mpv_suv += 1;
          if (
            ["bus", "traveller", "light_commercial_vehicle"].indexOf(type) !==
            -1
          )
            summary.tempo_bus += 1;
          if (["sedan", "micro", "hatchback"].indexOf(type) !== -1)
            summary.sedan_hatch += 1;
          return summary;
        },
        { total: 0, mpv_suv: 0, tempo_bus: 0, sedan_hatch: 0 },
      );

      $("#statTotalVehicles").text(assignedMetrics.total);
      $("#statMpvSuv").text(assignedMetrics.mpv_suv);
      $("#statTempoBus").text(assignedMetrics.tempo_bus);
      $("#statSedanHatch").text(assignedMetrics.sedan_hatch);

      $("#badgeAll").text(metrics.total ?? this.allVehicles.length);
      $("#badgeSedan").text(metrics.sedan ?? 0);
      $("#badgeMpvSuv").text(metrics.mpv_suv ?? 0);
      $("#badgeTempoBus").text(metrics.tempo_bus ?? 0);
      $("#badgeMicroHatch").text(metrics.micro_hatch ?? 0);
    },

    renderFleetChecklist: function () {
      const $list = $("#transporterFleetChecklist");
      if (!$list.length) return;
      const assignedIds = {};
      (this.allVehicles || []).forEach(function (vehicle) {
        assignedIds[String(vehicle.id)] = true;
      });
      if (!this.masterVehicles.length) {
        $list.html(
          '<span class="v3-field-hint">No master vehicles available. Use Create New Master Vehicle below.</span>',
        );
        return;
      }
      $list.html(
        this.masterVehicles
          .map(function (vehicle) {
            const id = String(vehicle.id);
            return `<label class="transporter-fleet-check-item">
          <input type="checkbox" class="transporter-fleet-checkbox" value="${id}" data-title="${selfEscapeHTML(vehicle.title)}" ${assignedIds[id] ? "checked" : ""}>
          <span>${selfEscapeHTML(vehicle.title)}</span>
          <small>${selfEscapeHTML(vehicle.type_label || vehicle.type || "")}</small>
        </label>`;
          })
          .join(""),
      );
      this.updateFleetSelectedCount();
    },

    updateFleetSelectedCount: function () {
      $("#transporterFleetSelectedCount").text(
        $(".transporter-fleet-checkbox:checked").length + " selected",
      );
    },

    saveFleetAssignments: function (extraTitle, done) {
      if (!this.transporterId) {
        done(true);
        return;
      }
      const selected = [];
      $(".transporter-fleet-checkbox:checked").each(function () {
        selected.push($(this).data("title"));
      });
      if (
        extraTitle &&
        !selected.some(function (title) {
          return String(title).toLowerCase() === extraTitle.toLowerCase();
        })
      ) {
        selected.push(extraTitle);
      }
      $.ajax({
        url: "/api/v3/transporters/",
        method: "POST",
        data: JSON.stringify({
          action: "update_fleet",
          transporter_id: this.transporterId,
          fleet_types: selected,
        }),
        contentType: "application/json",
        dataType: "json",
        success: function (res) {
          done(res && res.status === "success", res && res.msg);
        },
        error: function (xhr) {
          const response = xhr.responseJSON || {};
          done(false, response.msg || "Unable to save fleet assignments.");
        },
      });
    },

    applyFilters: function () {
      const self = this;
      let filtered = self.allVehicles.filter(function (v) {
        // Category Filter
        let matchCategory = true;
        const type = (v.type || "").toLowerCase();

        if (self.activeCategory === "sedan") {
          matchCategory = type === "sedan";
        } else if (self.activeCategory === "mpv_suv") {
          matchCategory = ["mpv", "suv"].indexOf(type) !== -1;
        } else if (self.activeCategory === "tempo_bus") {
          matchCategory =
            ["bus", "traveller", "light_commercial_vehicle"].indexOf(type) !==
            -1;
        } else if (self.activeCategory === "micro_hatch") {
          matchCategory = ["micro", "hatchback"].indexOf(type) !== -1;
        } else if (self.activeCategory !== "all") {
          matchCategory = type === self.activeCategory;
        }

        // Search Filter
        let matchSearch = true;
        if (self.searchQuery) {
          const q = self.searchQuery;
          matchSearch =
            (v.title || "").toLowerCase().indexOf(q) !== -1 ||
            (v.type_label || "").toLowerCase().indexOf(q) !== -1 ||
            (v.description || "").toLowerCase().indexOf(q) !== -1;
        }

        return matchCategory && matchSearch;
      });

      const $empty = $("#vehiclesEmptyState");
      const $tableCard = $("#vehicleTableCard");

      if (!filtered || filtered.length === 0) {
        $tableCard.hide();
        $empty.show();
        return;
      }

      $empty.hide();
      $tableCard.show();
      self.renderTable(filtered);
    },

    renderTable: function (vehicles) {
      const $tbody = $("#vehiclesTableBody");
      const self = this;
      const html = vehicles
        .map(function (v) {
          return self.buildTableRowHTML(v);
        })
        .join("");

      $tbody.html(html);
    },

    buildTableRowHTML: function (v) {
      const type = (v.type || "").toLowerCase();
      let fallbackIcon = "fi fi-rr-car-alt";

      if (["mpv", "suv"].indexOf(type) !== -1) {
        fallbackIcon = "fi fi-rr-car";
      } else if (
        ["bus", "traveller", "light_commercial_vehicle"].indexOf(type) !== -1
      ) {
        fallbackIcon = "fi fi-rr-bus";
      } else if (type === "sedan") {
        fallbackIcon = "fi fi-rr-taxi";
      } else if (["micro", "hatchback"].indexOf(type) !== -1) {
        fallbackIcon = "fi fi-rr-car-alt";
      }

      const seatingLabel = v.seating > 0 ? `${v.seating} Seater` : `Flexible`;
      const descSnippet = v.description
        ? `<div style="color: #475569; font-size: 12.5px; line-height: 1.4;" title="${selfEscapeHTML(v.description)}">${selfEscapeHTML(v.description)}</div>`
        : `<span style="color: #94a3b8; font-size: 12px;">No specifications provided</span>`;

      const avatarHTML = v.thumbnail
        ? `<img src="${v.thumbnail}" alt="${selfEscapeHTML(v.title)}" style="width: 38px; height: 38px; object-fit: cover; border-radius: 8px;" onerror="this.style.display='none'; $(this).next().show();"><div class="transport-avatar-badge" style="width: 38px; height: 38px; font-size: 15px; flex-shrink: 0; display: none;"><i class="${fallbackIcon}"></i></div>`
        : `<div class="transport-avatar-badge" style="width: 38px; height: 38px; font-size: 15px; flex-shrink: 0;"><i class="${fallbackIcon}"></i></div>`;

      const deleteBtnHTML =
        !this.transporterId && v.is_deletable !== false
          ? `<button type="button" class="btn-action-icon delete btn-delete-vehicle" data-id="${v.id}" data-title="${selfEscapeHTML(v.title)}" title="Delete vehicle" data-tooltip="Delete vehicle">
             <i class="fi fi-rr-trash"></i>
           </button>`
          : ``;

      return `
        <tr class="vehicle-row" data-id="${v.id}">
          <!-- Model & Thumbnail -->
          <td>
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0;">
                ${avatarHTML}
              </div>
              <div style="min-width: 0;">
                <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; line-height: 1.3; margin-bottom: 2px; text-transform: capitalize;" title="${selfEscapeHTML(capitalizeWords(v.title))}">
                  ${selfEscapeHTML(capitalizeWords(v.title))}
                </div>
                <span style="color: #94a3b8; font-size: 11.5px; font-weight: 500;">Fleet Model #${v.id}</span>
              </div>
            </div>
          </td>

          <!-- Category / Type -->
          <td>
            <span class="badge-service-state" title="${selfEscapeHTML(v.type_label || v.type)}">
              <i class="${fallbackIcon}" style="font-size: 11px; margin-right: 4px;"></i> ${selfEscapeHTML(v.type_label || v.type)}
            </span>
          </td>

          <!-- Seating Capacity -->
          <td class="col-capacity-cell">
            <span class="badge-pkg-assigned">
              <i class="fi fi-rr-users-alt" style="font-size: 10px; margin-right: 4px;"></i> ${seatingLabel}
            </span>
          </td>

          <!-- Specifications / Features -->
          <td>
            ${descSnippet}
          </td>

          <!-- Actions -->
          <td class="col-actions-cell">
            <div class="table-actions-inline" style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
              ${this.transporterId ? '<span class="transporter-assigned-badge"><i class="fi fi-rr-check"></i> Assigned</span>' : `<button type="button" class="btn-action-icon edit btn-edit-vehicle" data-id="${v.id}" title="Edit specifications" data-tooltip="Edit specifications"><i class="fi fi-rr-edit"></i></button>`}
              ${deleteBtnHTML}
            </div>
          </td>
        </tr>
      `;
    },

    renderSkeletons: function () {
      const $empty = $("#vehiclesEmptyState");
      const $tbody = $("#vehiclesTableBody");
      const $tableCard = $("#vehicleTableCard");

      $empty.hide();
      $tableCard.show();

      $tbody.html(`
        <tr>
          <td colspan="5" class="table-loading-state">
            <div class="table-loading-spinner"></div>
            <span class="loading-label">Loading fleet vehicles...</span>
          </td>
        </tr>
      `);
    },

    renderEmptyState: function (msg) {
      const $tbody = $("#vehiclesTableBody");
      const $tableCard = $("#vehicleTableCard");
      const $empty = $("#vehiclesEmptyState");

      $tbody.empty();
      $tableCard.hide();

      if (msg) {
        $empty.find("#vehicleEmptyStateMsg").text(msg);
      }
      $empty.show();
    },

    openAddModal: function () {
      this.editingVehicleId = null;
      $("#modalVehicleId").val("");
      $("#modalVehicleTitle").val("");
      $("#modalVehicleType").val("");
      $("#modalVehicleSeating").val("");
      $("#modalVehicleDescription").val("");
      $("#modalVehicleThumbnail").val("");
      this.clearFilePreview();
      this.clearErrors();

      $("#vehicleModalTitle").text("Add New Fleet Vehicle");
      $("#vehicleModalSubtitle").text(
        "Configure vehicle category, seating capacity, and specifications",
      );
      $("#btnSaveVehicleText").text("Save Vehicle");
      $("#modalHeaderIcon").attr("class", "fi fi-rr-car");

      $("#vehicleModalOverlay").addClass("active is-active");
      if (this.transporterId) {
        $("#vehicleModalTitle").text("Choose Fleet Vehicles");
        $("#vehicleModalSubtitle").text(
          "Select the master vehicles available to this transporter",
        );
        $("#btnSaveVehicleText").text("Save Fleet Selection");
      } else {
        setTimeout(() => $("#modalVehicleTitle").focus(), 150);
      }
    },

    openEditModal: function (vehicleId) {
      const v = this.allVehicles.find(
        (item) => Number(item.id) === Number(vehicleId),
      );
      if (!v) return;

      this.editingVehicleId = v.id;
      $("#modalVehicleId").val(v.id);
      $("#modalVehicleTitle").val(v.title);
      $("#modalVehicleType").val(v.type);
      $("#modalVehicleSeating").val(v.seating > 0 ? v.seating : "");
      $("#modalVehicleDescription").val(v.description || "");
      $("#modalVehicleThumbnail").val(v.thumbnail || "");
      this.clearErrors();

      if (v.thumbnail) {
        $("#modalImagePreview").attr("src", v.thumbnail);
        $("#modalImagePreviewWrap").show();
        $("#modalUploadPlaceholder").hide();
      } else {
        this.clearFilePreview();
      }

      $("#vehicleModalTitle").text(`Edit Vehicle — ${v.title}`);
      $("#vehicleModalSubtitle").text(
        "Update model specs, category, and capacity",
      );
      $("#btnSaveVehicleText").text("Update Vehicle");
      $("#modalHeaderIcon").attr("class", "fi fi-rr-settings-sliders");

      $("#vehicleModalOverlay").addClass("active is-active");
      setTimeout(() => $("#modalVehicleTitle").focus(), 150);
    },

    closeModal: function () {
      $("#vehicleModalOverlay").removeClass("active is-active");
      this.editingVehicleId = null;
      this.selectedFile = null;
    },

    handleFilePreview: function (file) {
      this.selectedFile = file;
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#modalImagePreview").attr("src", e.target.result);
        $("#modalImagePreviewWrap").show();
        $("#modalUploadPlaceholder").hide();
      };
      reader.readAsDataURL(file);
    },

    clearFilePreview: function () {
      this.selectedFile = null;
      $("#modalVehicleFileInput").val("");
      $("#modalVehicleThumbnail").val("");
      $("#modalImagePreview").attr("src", "");
      $("#modalImagePreviewWrap").hide();
      $("#modalUploadPlaceholder").show();
    },

    clearErrors: function () {
      $(".v3-field-error").text("");
      $(".v3-form-control").removeClass("is-invalid");
    },

    handleFormSubmit: function () {
      const self = this;
      if (self.transporterId) {
        const $btn = $("#btnSaveVehicleModal");
        $btn.prop("disabled", true);
        self.saveFleetAssignments("", function (saved, message) {
          $btn.prop("disabled", false);
          if (!saved) {
            self.showToast(
              "error",
              message || "Unable to save fleet assignments.",
            );
            return;
          }
          self.closeModal();
          self.showToast("success", "Fleet selection saved successfully.");
          self.fetchVehicles();
        });
        return;
      }
      self.clearErrors();

      const title = $("#modalVehicleTitle").val().trim();
      const type = $("#modalVehicleType").val().trim();
      const seating = parseInt($("#modalVehicleSeating").val(), 10) || 0;
      const description = $("#modalVehicleDescription").val().trim();
      const existingThumbnail = $("#modalVehicleThumbnail").val().trim();

      let hasError = false;
      if (!title) {
        $("#modalVehicleTitle").addClass("is-invalid");
        $("#errModalTitle").text("Please enter vehicle model name.");
        hasError = true;
      }
      if (!type) {
        $("#modalVehicleType").addClass("is-invalid");
        $("#errModalType").text("Please select vehicle category/type.");
        hasError = true;
      }

      if (hasError) return;

      const $btn = $("#btnSaveVehicleModal");
      const origText = $("#btnSaveVehicleText").text();
      $btn
        .prop("disabled", true)
        .find("i")
        .attr("class", "fi fi-rr-spinner fi-spin");
      $("#btnSaveVehicleText").text("Saving...");

      const formData = new FormData();
      formData.append("title", title);
      formData.append("category", type);
      formData.append("seating", seating);
      formData.append("description", description);
      formData.append("thumbnail", existingThumbnail);

      if (self.selectedFile) {
        formData.append("icon", self.selectedFile);
      }

      if (self.editingVehicleId) {
        formData.append("vehicle_id", self.editingVehicleId);
        formData.append("action", "update");
      }

      $.ajax({
        url: self.apiEndpoint,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
          $btn
            .prop("disabled", false)
            .find("i")
            .attr("class", "fi fi-rr-check");
          $("#btnSaveVehicleText").text(origText);

          if (res && res.status === "success") {
            self.saveFleetAssignments(
              self.editingVehicleId ? "" : title,
              function (saved, message) {
                if (!saved) {
                  self.showToast(
                    "error",
                    message || "Unable to save fleet assignments.",
                  );
                  return;
                }
                self.closeModal();
                self.showToast(
                  "success",
                  res.msg || "Vehicle saved successfully!",
                );
                self.fetchVehicles();
              },
            );
          } else {
            self.showToast(
              "error",
              res && res.msg ? res.msg : "Failed to save vehicle.",
            );
          }
        },
        error: function (xhr) {
          $btn
            .prop("disabled", false)
            .find("i")
            .attr("class", "fi fi-rr-check");
          $("#btnSaveVehicleText").text(origText);

          let errorMsg = "Something went wrong while saving vehicle.";
          if (xhr.responseJSON && xhr.responseJSON.msg) {
            errorMsg = xhr.responseJSON.msg;
          }
          self.showToast("error", errorMsg);
        },
      });
    },

    handleDeleteVehicle: function (vehicleId, title) {
      const self = this;
      if (typeof swal === "function") {
        swal({
          title: "Delete Vehicle?",
          text: `Are you sure you want to delete '${title}' from the fleet directory?`,
          type: "warning",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc2626",
          confirmButtonText: "Yes, Delete",
          cancelButtonText: "Cancel",
          buttons: ["Cancel", "Yes, Delete"],
          dangerMode: true,
        }).then(function (willDelete) {
          if (willDelete) {
            self.executeDelete(vehicleId);
          }
        });
      } else if (confirm(`Are you sure you want to delete '${title}'?`)) {
        self.executeDelete(vehicleId);
      }
    },

    executeDelete: function (vehicleId) {
      const self = this;
      $.ajax({
        url: self.apiEndpoint + "?action=delete",
        method: "POST",
        data: JSON.stringify({ vehicle_id: vehicleId, action: "delete" }),
        contentType: "application/json",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            self.showToast(
              "success",
              res.msg || "Vehicle deleted successfully.",
            );
            self.fetchVehicles();
          } else {
            self.showToast(
              "error",
              res && res.msg ? res.msg : "Failed to delete vehicle.",
            );
          }
        },
        error: function (xhr) {
          let errorMsg = "Failed to delete vehicle.";
          if (xhr.responseJSON && xhr.responseJSON.msg) {
            errorMsg = xhr.responseJSON.msg;
          }
          self.showToast("error", errorMsg);
        },
      });
    },

    showToast: function (type, message) {
      const isSuccess = type === "success";
      const iconType = isSuccess ? "success" : "error";
      const titleText = isSuccess ? "Success" : "Notice";

      if (typeof window.swal === "function") {
        window.swal({
          title: titleText,
          text: message,
          type: iconType,
          icon: iconType,
          timer: 2000,
          button: false,
          buttons: false,
          showConfirmButton: false,
        });
      } else if (typeof window.popUpMessage === "function") {
        window.popUpMessage("center", message, iconType);
      } else {
        alert(message);
      }
    },
  };

  function capitalizeWords(str) {
    if (!str) return "";
    return String(str)
      .toLowerCase()
      .replace(/\b\w/g, function (c) {
        return c.toUpperCase();
      });
  }

  function selfEscapeHTML(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.V3TransporterVehicles = V3TransporterVehicles;

  $(document).ready(function () {
    if ($("#vehiclesMasterApp").length > 0) {
      V3TransporterVehicles.init();
    }
  });

  $(document).on("spa:loaded", function () {
    if ($("#vehiclesMasterApp").length > 0) {
      V3TransporterVehicles.init();
    }
  });
})(window, jQuery);
