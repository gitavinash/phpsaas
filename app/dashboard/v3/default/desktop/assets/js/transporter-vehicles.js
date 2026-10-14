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
    editingVehicleId: null,
    transporterId: null,
    masterVehicles: [],
    selectedFleetTypes: [],
    initialized: false,
    initializedRoot: null,
    activeXhr: null,

    // Media & Gallery State
    featureImageFile: null,
    featureImageUrl: "",
    galleryItems: [], // array of { id, path, file, isExisting, isCover }

    // Lightbox State
    lightboxPhotos: [],
    lightboxIndex: 0,

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
      this.initToolbarSelect2();
      this.initSelect2Dropdowns();
      this.fetchVehicles();
    },

    initToolbarSelect2: function () {
      if (!$.fn.select2) return;
      const $cat = $("#vehicleCategoryDropdown");
      if ($cat.length) {
        try {
          $cat.select2("destroy");
        } catch (e) {}
        $cat.select2({
          width: "200px",
          minimumResultsForSearch: Infinity,
        });
      }
    },

    formatCategoryOption: function (state) {
      if (!state.id) return state.text;
      let icon = "fi-rr-cars";
      if (state.element) {
        icon = $(state.element).data("icon") || $(state.element).attr("data-icon") || icon;
      } else {
        const $opt = $("#modalVehicleType").find('option[value="' + state.id + '"]');
        if ($opt.length) {
          icon = $opt.data("icon") || $opt.attr("data-icon") || icon;
        }
      }
      if (icon === "fi-rr-jeep") icon = "fi-rr-truck-pickup";
      return $(
        '<span class="fleet-select2-item">' +
        '<i class="fi ' +
        icon +
        ' fleet-select2-item-icon"></i>' +
        '<span class="fleet-select2-item-text">' +
        selfEscapeHTML(state.text) +
        "</span>" +
        "</span>"
      );
    },

    formatFuelOption: function (state) {
      if (!state.id) return state.text;
      let icon = "fi-rr-gas-pump";
      if (state.element) {
        icon = $(state.element).data("icon") || $(state.element).attr("data-icon") || icon;
      } else {
        const $opt = $("#modalVehicleFuel").find('option[value="' + state.id + '"]');
        if ($opt.length) {
          icon = $opt.data("icon") || $opt.attr("data-icon") || icon;
        }
      }
      return $(
        '<span class="fleet-select2-item">' +
        '<i class="fi ' +
        icon +
        ' fleet-select2-item-icon"></i>' +
        '<span class="fleet-select2-item-text">' +
        selfEscapeHTML(state.text) +
        "</span>" +
        "</span>"
      );
    },

    initSelect2Dropdowns: function () {
      if (!$.fn.select2) return;
      const self = this;
      const $overlay = $("#vehicleModalOverlay");
      const parentContainer = $overlay.length ? $overlay : $(document.body);

      const $typeSelect = $("#modalVehicleType");
      if ($typeSelect.length) {
        if ($typeSelect.hasClass("select2-hidden-accessible") || $typeSelect.data("select2")) {
          try {
            $typeSelect.select2("destroy");
          } catch (e) {}
        }
        try {
          $typeSelect.select2({
            dropdownParent: parentContainer,
            width: "100%",
            placeholder: "Select Category",
            allowClear: false,
            templateResult: function (state) {
              return self.formatCategoryOption(state);
            },
            templateSelection: function (state) {
              return self.formatCategoryOption(state);
            },
            escapeMarkup: function (m) {
              return m;
            },
          });
          $typeSelect.off("change.select2err").on("change.select2err", function () {
            $("#errModalType").text("");
            $typeSelect.next(".select2-container").removeClass("is-invalid");
          });
        } catch (err) {
          console.error("Select2 initialization failed on modalVehicleType:", err);
        }
      }

      const $fuelSelect = $("#modalVehicleFuel");
      if ($fuelSelect.length) {
        if ($fuelSelect.hasClass("select2-hidden-accessible") || $fuelSelect.data("select2")) {
          try {
            $fuelSelect.select2("destroy");
          } catch (e) {}
        }
        try {
          $fuelSelect.select2({
            dropdownParent: parentContainer,
            width: "100%",
            placeholder: "Select Fuel (Optional)",
            allowClear: false,
            minimumResultsForSearch: Infinity,
            templateResult: function (state) {
              return self.formatFuelOption(state);
            },
            templateSelection: function (state) {
              return self.formatFuelOption(state);
            },
            escapeMarkup: function (m) {
              return m;
            },
          });
        } catch (err) {
          console.error("Select2 initialization failed on modalVehicleFuel:", err);
        }
      }
    },

    bindEvents: function () {
      const self = this;

      // Thumbnail Image Fallback Error
      $(document)
        .off("error.vehicleModel", ".vehicle-model-thumbnail")
        .on("error.vehicleModel", ".vehicle-model-thumbnail", function () {
          $(this).replaceWith(
            $("<i>").addClass(
              $(this).data("fallback-icon") + " vehicle-model-fallback-icon",
            ),
          );
        });

      // Category Dropdown Filter
      $(document)
        .off("change", "#vehicleCategoryDropdown")
        .on("change", "#vehicleCategoryDropdown", function () {
          self.activeCategory = $(this).val() || "all";
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
          if ($.fn.select2 && $("#vehicleCategoryDropdown").data("select2")) {
            $("#vehicleCategoryDropdown").val("").trigger("change");
          } else {
            $("#vehicleCategoryDropdown").val("");
          }
          self.applyFilters();
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

      // Transporter Scope Fleet Checklist
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

      // Close Vehicle Modal
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

      // -------------------------------------------------------------
      // Feature Image Upload & Drag-Drop Handling
      // -------------------------------------------------------------
      $(document)
        .off("click", "#fleetFeaturePreviewZone")
        .on("click", "#fleetFeaturePreviewZone", function (e) {
          if (!$(e.target).closest("#btnRemoveFeaturePhoto").length) {
            $("#modalVehicleFeatureInput").trigger("click");
          }
        });

      $(document)
        .off("change", "#modalVehicleFeatureInput")
        .on("change", "#modalVehicleFeatureInput", function (e) {
          const file = e.target.files && e.target.files[0];
          if (file) {
            self.handleFeatureFile(file);
          }
        });

      $(document)
        .off("click", "#btnRemoveFeaturePhoto")
        .on("click", "#btnRemoveFeaturePhoto", function (e) {
          e.stopPropagation();
          self.clearFeaturePhoto();
        });

      $(document)
        .off("dragover dragenter", "#fleetFeaturePreviewZone")
        .on("dragover dragenter", "#fleetFeaturePreviewZone", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).addClass("dragover");
        });

      $(document)
        .off("dragleave drop", "#fleetFeaturePreviewZone")
        .on("dragleave drop", "#fleetFeaturePreviewZone", function (e) {
          e.preventDefault();
          e.stopPropagation();
          $(this).removeClass("dragover");
          if (e.type === "drop") {
            const files =
              e.originalEvent.dataTransfer &&
              e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
              self.handleFeatureFile(files[0]);
            }
          }
        });

      // -------------------------------------------------------------
      // Gallery Multi-File Upload & Drag-Drop Handling
      // -------------------------------------------------------------
      $(document)
        .off(
          "click",
          "#fleetGalleryEmptyDropzone, #btnBrowseGalleryEmpty, #btnBrowseGalleryFilesTop, #btnGalleryAddTile",
        )
        .on(
          "click",
          "#fleetGalleryEmptyDropzone, #btnBrowseGalleryEmpty, #btnBrowseGalleryFilesTop, #btnGalleryAddTile",
          function (e) {
            if (!$(e.target).is("#modalVehicleGalleryInput")) {
              $("#modalVehicleGalleryInput").trigger("click");
            }
          },
        );

      $(document)
        .off("click", "#btnClearAllGalleryFiles")
        .on("click", "#btnClearAllGalleryFiles", function (e) {
          e.stopPropagation();
          self.galleryItems = [];
          self.renderGalleryGrid();
        });

      $(document)
        .off("change", "#modalVehicleGalleryInput")
        .on("change", "#modalVehicleGalleryInput", function (e) {
          const files = e.target.files;
          if (files && files.length > 0) {
            self.addGalleryFiles(files);
            $(this).val("");
          }
        });

      $(document)
        .off(
          "dragover dragenter",
          "#fleetGalleryEmptyDropzone, #fleetGalleryActiveContainer",
        )
        .on(
          "dragover dragenter",
          "#fleetGalleryEmptyDropzone, #fleetGalleryActiveContainer",
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass("dragover");
          },
        );

      $(document)
        .off(
          "dragleave drop",
          "#fleetGalleryEmptyDropzone, #fleetGalleryActiveContainer",
        )
        .on(
          "dragleave drop",
          "#fleetGalleryEmptyDropzone, #fleetGalleryActiveContainer",
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass("dragover");
            if (e.type === "drop") {
              const files =
                e.originalEvent.dataTransfer &&
                e.originalEvent.dataTransfer.files;
              if (files && files.length > 0) {
                self.addGalleryFiles(files);
              }
            }
          },
        );

      // Set Gallery Item as Feature Cover
      $(document)
        .off("click", ".gallery-action-btn.set-cover")
        .on("click", ".gallery-action-btn.set-cover", function (e) {
          e.stopPropagation();
          const itemId = $(this).closest(".fleet-gallery-item").data("id");
          self.setGalleryItemAsCover(itemId);
        });

      // Delete Gallery Item
      $(document)
        .off("click", ".gallery-action-btn.delete-item")
        .on("click", ".gallery-action-btn.delete-item", function (e) {
          e.stopPropagation();
          const itemId = $(this).closest(".fleet-gallery-item").data("id");
          self.removeGalleryItem(itemId);
        });

      // Amenity Quick Tag Chips
      $(document)
        .off("click", ".fleet-amenity-pill")
        .on("click", ".fleet-amenity-pill", function () {
          const tag = $(this).data("tag");
          $(this).toggleClass("selected");
          self.syncAmenityTagsToTextarea(tag, $(this).hasClass("selected"));
        });

      // Form Submit
      $(document)
        .off("submit", "#vehicleForm")
        .on("submit", "#vehicleForm", function (e) {
          e.preventDefault();
          self.handleFormSubmit();
        });

      // Edit Button
      $(document)
        .off("click", ".btn-edit-vehicle")
        .on("click", ".btn-edit-vehicle", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).data("id");
          self.openEditModal(id);
        });

      // Delete Button
      $(document)
        .off("click", ".btn-delete-vehicle")
        .on("click", ".btn-delete-vehicle", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const id = $(this).data("id");
          const title = $(this).data("title") || "this vehicle";
          self.handleDeleteVehicle(id, title);
        });

      // -------------------------------------------------------------
      // Lightbox Quick Gallery Viewer Events
      // -------------------------------------------------------------
      $(document)
        .off(
          "click",
          ".vehicle-thumbnail-wrapper, .btn-action-icon.view-gallery",
        )
        .on(
          "click",
          ".vehicle-thumbnail-wrapper, .btn-action-icon.view-gallery",
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            const vehicleId = $(this).data("id");
            self.openLightbox(vehicleId);
          },
        );

      $(document)
        .off("click", "#btnCloseLightboxModal")
        .on("click", "#btnCloseLightboxModal", function () {
          $("#vehicleGalleryViewerModal").removeClass("active is-active");
        });

      $(document)
        .off("click", "#vehicleGalleryViewerModal")
        .on("click", "#vehicleGalleryViewerModal", function (e) {
          if ($(e.target).is("#vehicleGalleryViewerModal")) {
            $("#vehicleGalleryViewerModal").removeClass("active is-active");
          }
        });

      $(document)
        .off("click", "#btnLightboxPrev")
        .on("click", "#btnLightboxPrev", function () {
          self.navigateLightbox(-1);
        });

      $(document)
        .off("click", "#btnLightboxNext")
        .on("click", "#btnLightboxNext", function () {
          self.navigateLightbox(1);
        });

      $(document)
        .off("click", ".lightbox-thumb-item")
        .on("click", ".lightbox-thumb-item", function () {
          const idx = parseInt($(this).data("index"), 10);
          self.setLightboxPhoto(idx);
        });

      $(document)
        .off("keydown.fleetLightbox")
        .on("keydown.fleetLightbox", function (e) {
          if ($("#vehicleGalleryViewerModal").hasClass("is-active")) {
            if (e.key === "ArrowLeft") self.navigateLightbox(-1);
            if (e.key === "ArrowRight") self.navigateLightbox(1);
            if (e.key === "Escape")
              $("#vehicleGalleryViewerModal").removeClass("active is-active");
          }
        });
    },

    // -------------------------------------------------------------
    // Media & Gallery Functions
    // -------------------------------------------------------------
    handleFeatureFile: function (file) {
      const self = this;
      self.featureImageFile = file;
      self.featureImageUrl = "";

      const reader = new FileReader();
      reader.onload = function (e) {
        $("#fleetFeatureImg").attr("src", e.target.result);
        $("#fleetFeatureActiveState").removeClass("is-hidden");
        $("#fleetFeatureEmptyState").addClass("is-hidden");
        $("#fleetFeaturePreviewZone").addClass("has-image");
        $("#modalVehicleThumbnail").val("");
      };
      reader.readAsDataURL(file);
    },

    clearFeaturePhoto: function () {
      this.featureImageFile = null;
      this.featureImageUrl = "";
      $("#modalVehicleFeatureInput").val("");
      $("#modalVehicleThumbnail").val("");
      $("#fleetFeatureImg").attr("src", "");
      $("#fleetFeatureActiveState").addClass("is-hidden");
      $("#fleetFeatureEmptyState").removeClass("is-hidden");
      $("#fleetFeaturePreviewZone").removeClass("has-image");
    },

    addGalleryFiles: function (files) {
      const self = this;
      const fileList = Array.from(files);

      fileList.forEach(function (file) {
        if (!file.type.match(/^image\//i)) return;
        const itemId = "new_" + Date.now() + "_" + Math.random().toString(36).substring(2, 7);
        const reader = new FileReader();

        reader.onload = function (e) {
          self.galleryItems.push({
            id: itemId,
            path: e.target.result,
            file: file,
            isExisting: false,
            isCover: false,
          });

          // If no cover photo is set yet, default this first photo as cover
          if (!self.featureImageFile && !self.featureImageUrl) {
            self.setGalleryItemAsCover(itemId);
          } else {
            self.renderGalleryGrid();
          }
        };
        reader.readAsDataURL(file);
      });
    },

    removeGalleryItem: function (itemId) {
      const idx = this.galleryItems.findIndex((it) => it.id === itemId);
      if (idx !== -1) {
        const removed = this.galleryItems.splice(idx, 1)[0];
        if (removed.isCover) {
          this.clearFeaturePhoto();
          if (this.galleryItems.length > 0) {
            this.setGalleryItemAsCover(this.galleryItems[0].id);
          }
        }
        this.renderGalleryGrid();
      }
    },

    setGalleryItemAsCover: function (itemId) {
      const item = this.galleryItems.find((it) => it.id === itemId);
      if (!item) return;

      this.galleryItems.forEach((it) => (it.isCover = it.id === itemId));

      if (item.file) {
        this.featureImageFile = item.file;
        this.featureImageUrl = "";
        $("#modalVehicleThumbnail").val("");
      } else {
        this.featureImageFile = null;
        this.featureImageUrl = item.path;
        $("#modalVehicleThumbnail").val(item.path);
      }

      $("#fleetFeatureImg").attr("src", item.path);
      $("#fleetFeatureActiveState").removeClass("is-hidden");
      $("#fleetFeatureEmptyState").addClass("is-hidden");
      $("#fleetFeaturePreviewZone").addClass("has-image");

      this.renderGalleryGrid();
    },

    renderGalleryGrid: function () {
      const $grid = $("#fleetGalleryGrid");
      const $badge = $("#fleetGalleryCountBadge");
      const $emptyDropzone = $("#fleetGalleryEmptyDropzone");
      const $activeContainer = $("#fleetGalleryActiveContainer");
      const $topActions = $("#fleetGalleryTopActions");
      const total = this.galleryItems.length;

      $badge.text(total + (total === 1 ? " photo" : " photos"));

      if (!total) {
        $emptyDropzone.removeClass("is-hidden");
        $activeContainer.addClass("is-hidden");
        $topActions.addClass("is-hidden");
        $grid.empty();
        return;
      }

      $emptyDropzone.addClass("is-hidden");
      $activeContainer.removeClass("is-hidden");
      $topActions.removeClass("is-hidden");

      const itemsHtml = this.galleryItems
        .map(function (item) {
          const coverClass = item.isCover ? " is-cover" : "";
          const coverBadge = item.isCover
            ? '<span class="gallery-cover-star-badge"><i class="fi fi-rr-star"></i> Cover</span>'
            : "";

          return `
            <div class="fleet-gallery-item${coverClass}" data-id="${item.id}">
              <img src="${selfEscapeHTML(item.path)}" alt="Gallery photo" loading="lazy">
              ${coverBadge}
              <div class="gallery-item-actions">
                <button type="button" class="gallery-action-btn set-cover" title="Set as Cover Photo">
                  <i class="fi fi-rr-star"></i>
                </button>
                <button type="button" class="gallery-action-btn delete-item" title="Remove photo">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </div>
          `;
        })
        .join("");

      const addTileHtml = `
        <div class="fleet-gallery-add-tile" id="btnGalleryAddTile" title="Add more gallery photos">
          <i class="fi fi-rr-plus"></i>
          <span>Add More</span>
        </div>
      `;

      $grid.html(itemsHtml + addTileHtml);
    },

    // -------------------------------------------------------------
    // Amenity Tag Synchronization
    // -------------------------------------------------------------
    syncAmenityTagsToTextarea: function (tag, isSelected) {
      const $textarea = $("#modalVehicleDescription");
      let currentVal = $textarea.val().trim();
      let parts = currentVal
        ? currentVal.split(",").map((s) => s.trim()).filter(Boolean)
        : [];

      if (isSelected) {
        if (!parts.includes(tag)) parts.push(tag);
      } else {
        parts = parts.filter((s) => s !== tag);
      }
      $textarea.val(parts.join(", "));
    },

    refreshAmenityPillStates: function () {
      const currentVal = ($("#modalVehicleDescription").val() || "").toLowerCase();
      $(".fleet-amenity-pill").each(function () {
        const tag = ($(this).data("tag") || "").toLowerCase();
        if (currentVal.indexOf(tag) !== -1) {
          $(this).addClass("selected");
        } else {
          $(this).removeClass("selected");
        }
      });
    },

    // -------------------------------------------------------------
    // Lightbox Quick Gallery Viewer
    // -------------------------------------------------------------
    openLightbox: function (vehicleId) {
      const v = this.allVehicles.find((item) => Number(item.id) === Number(vehicleId));
      if (!v) return;

      const photos = [];
      if (v.thumbnail) photos.push(v.thumbnail);
      if (Array.isArray(v.gallery)) {
        v.gallery.forEach((p) => {
          if (p && !photos.includes(p)) photos.push(p);
        });
      }

      if (!photos.length) {
        this.showToast("info", "No photos available for this vehicle.");
        return;
      }

      this.lightboxPhotos = photos;
      this.lightboxIndex = 0;

      $("#lightboxVehicleTitle").text(v.title);
      $("#lightboxVehicleSubtitle").text(
        `${v.type_label || v.type} • ${v.seating > 0 ? v.seating + " Seater" : "Flexible Capacity"} • ${photos.length} photos`,
      );

      this.renderLightboxView();
      $("#vehicleGalleryViewerModal").addClass("active is-active");
    },

    renderLightboxView: function () {
      const currentPhoto = this.lightboxPhotos[this.lightboxIndex];
      $("#lightboxMainImg").attr("src", currentPhoto);

      const thumbsHTML = this.lightboxPhotos
        .map(
          (p, i) => `
          <div class="lightbox-thumb-item ${i === this.lightboxIndex ? "active" : ""}" data-index="${i}">
            <img src="${selfEscapeHTML(p)}" alt="">
          </div>
        `,
        )
        .join("");

      $("#lightboxThumbStrip").html(thumbsHTML);

      $("#btnLightboxPrev").toggle(this.lightboxPhotos.length > 1);
      $("#btnLightboxNext").toggle(this.lightboxPhotos.length > 1);
    },

    setLightboxPhoto: function (idx) {
      if (idx >= 0 && idx < this.lightboxPhotos.length) {
        this.lightboxIndex = idx;
        this.renderLightboxView();
      }
    },

    navigateLightbox: function (direction) {
      if (!this.lightboxPhotos.length) return;
      let nextIdx = this.lightboxIndex + direction;
      if (nextIdx < 0) nextIdx = this.lightboxPhotos.length - 1;
      if (nextIdx >= this.lightboxPhotos.length) nextIdx = 0;
      this.setLightboxPhoto(nextIdx);
    },

    // -------------------------------------------------------------
    // Data Fetching & KPIs
    // -------------------------------------------------------------
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

    // -------------------------------------------------------------
    // Filters & Table Render
    // -------------------------------------------------------------
    applyFilters: function () {
      const self = this;
      const filtered = self.allVehicles.filter(function (v) {
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
            (v.fuel_type || "").toLowerCase().indexOf(q) !== -1 ||
            (v.description || "").toLowerCase().indexOf(q) !== -1 ||
            String(v.rate_per_km_with_tax || "").indexOf(q) !== -1 ||
            String(v.rate_per_km_without_tax || "").indexOf(q) !== -1;
        }

        return matchCategory && matchSearch;
      });

      const $empty = $("#vehiclesEmptyState");
      const $tableCard = $("#vehicleTableCard");

      if (!filtered || filtered.length === 0) {
        $tableCard.addClass("is-hidden");
        $empty.removeClass("is-hidden");
        return;
      }

      $empty.addClass("is-hidden");
      $tableCard.removeClass("is-hidden");
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

      const totalPhotos = (v.gallery_count || 0) + (v.thumbnail ? 1 : 0);
      const photoBadge =
        totalPhotos > 0
          ? `<span class="vehicle-photo-count-badge" title="${totalPhotos} photos in gallery"><i class="fi fi-rr-camera"></i> ${totalPhotos}</span>`
          : "";

      const avatarHTML = v.thumbnail
        ? `<img class="vehicle-model-thumbnail" src="${selfEscapeHTML(v.thumbnail)}" alt="" data-fallback-icon="${fallbackIcon}" loading="lazy">`
        : `<i class="${fallbackIcon} vehicle-model-fallback-icon"></i>`;

      const fuelPillHTML = v.fuel_type
        ? `<span class="vehicle-fuel-pill ${selfEscapeHTML(v.fuel_type.toLowerCase())}">${selfEscapeHTML(v.fuel_type)}</span>`
        : "";

      const seatingLabel = v.seating > 0 ? `${v.seating} Seater` : `Flexible`;
      const luggageLabel = v.luggage > 0 ? `${v.luggage} Bags` : `Standard Luggage`;

      // Dual Rate Badges
      const rateWithTaxHTML =
        v.rate_per_km_with_tax > 0
          ? `<span class="fleet-rate-badge incl-tax" title="Rate per KM including all toll & state permit taxes">
               <span class="rate-label">All-in</span>
               <span class="rate-value">₹${v.rate_per_km_with_tax}/km</span>
             </span>`
          : `<span class="fleet-rate-badge not-set" title="Rate including taxes not specified">
               <span class="rate-label">All-in</span>
               <span class="rate-value">—</span>
             </span>`;

      const rateWithoutTaxHTML =
        v.rate_per_km_without_tax > 0
          ? `<span class="fleet-rate-badge excl-tax" title="Rate per KM without toll & state permit taxes">
               <span class="rate-label">Base</span>
               <span class="rate-value">₹${v.rate_per_km_without_tax}/km</span>
             </span>`
          : `<span class="fleet-rate-badge not-set" title="Base rate without taxes not specified">
               <span class="rate-label">Base</span>
               <span class="rate-value">—</span>
             </span>`;

      const descSnippet = v.description
        ? `<div class="vehicle-desc-text" title="${selfEscapeHTML(v.description)}">${selfEscapeHTML(v.description)}</div>`
        : `<span class="vehicle-desc-empty">No amenities provided</span>`;

      const galleryBtnHTML =
        totalPhotos > 0
          ? `<button type="button" class="btn-action-icon view-gallery" data-id="${v.id}" title="View vehicle photo gallery" data-tooltip="View Gallery (${totalPhotos} photos)">
               <i class="fi fi-rr-images"></i>
             </button>`
          : "";

      const deleteBtnHTML =
        !this.transporterId && v.is_deletable !== false
          ? `<button type="button" class="btn-action-icon delete btn-delete-vehicle" data-id="${v.id}" data-title="${selfEscapeHTML(v.title)}" title="Delete vehicle" data-tooltip="Delete vehicle">
               <i class="fi fi-rr-trash"></i>
             </button>`
          : "";

      return `
        <tr class="vehicle-row" data-id="${v.id}">
          <!-- Model Image & Visual Identity -->
          <td>
            <div class="vehicle-model-cell">
              <div class="vehicle-thumbnail-wrapper" data-id="${v.id}" title="Click to preview photos">
                ${avatarHTML}
                ${photoBadge}
              </div>
              <div class="vehicle-meta-info">
                <span class="vehicle-model-name">${selfEscapeHTML(v.title)}</span>
                <div class="vehicle-sub-pills">
                  ${fuelPillHTML}
                </div>
              </div>
            </div>
          </td>

          <!-- Category / Type -->
          <td>
            <span class="badge-service-state" title="${selfEscapeHTML(v.type_label || v.type)}">
              <i class="${fallbackIcon} vehicle-badge-icon"></i> ${selfEscapeHTML(v.type_label || v.type)}
            </span>
          </td>

          <!-- Capacity -->
          <td class="col-capacity-cell">
            <div class="vehicle-capacity-block">
              <span class="capacity-chip pax" title="Passenger Seating Capacity">
                <i class="fi fi-rr-users-alt"></i> ${seatingLabel}
              </span>
              <span class="capacity-chip luggage" title="Boot Luggage Capacity">
                <i class="fi fi-rr-briefcase"></i> ${luggageLabel}
              </span>
            </div>
          </td>

          <!-- Per KM Tariff Rates -->
          <td class="col-rates-cell">
            <div class="vehicle-rates-block">
              ${rateWithTaxHTML}
              ${rateWithoutTaxHTML}
            </div>
          </td>

          <!-- Specifications & Features -->
          <td>
            ${descSnippet}
          </td>

          <!-- Actions -->
          <td class="col-actions-cell">
            <div class="vehicle-actions-group">
              ${galleryBtnHTML}
              ${this.transporterId ? '<span class="transporter-assigned-badge"><i class="fi fi-rr-check"></i> Assigned</span>' : `<button type="button" class="btn-action-icon edit btn-edit-vehicle" data-id="${v.id}" title="Edit vehicle details" data-tooltip="Edit specifications"><i class="fi fi-rr-edit"></i></button>`}
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

      $empty.addClass("is-hidden");
      $tableCard.removeClass("is-hidden");

      $tbody.html(`
        <tr>
          <td colspan="6" class="table-loading-state">
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
      $tableCard.addClass("is-hidden");

      if (msg) {
        $empty.find("#vehicleEmptyStateMsg").text(msg);
      }
      $empty.removeClass("is-hidden");
    },

    // -------------------------------------------------------------
    // Modal Open & Populate
    // -------------------------------------------------------------
    openAddModal: function () {
      this.editingVehicleId = null;
      $("#modalVehicleId").val("");
      $("#modalVehicleTitle").val("");
      $("#modalVehicleType").val("");
      $("#modalVehicleFuel").val("");
      $("#modalVehicleSeating").val("");
      $("#modalVehicleLuggage").val("");
      $("#modalVehicleRateWithTax").val("");
      $("#modalVehicleRateWithoutTax").val("");
      $("#modalVehicleDescription").val("");
      $("#modalVehicleThumbnail").val("");
      $("#modalVehicleExistingGallery").val("[]");

      this.clearFeaturePhoto();
      this.galleryItems = [];
      this.renderGalleryGrid();
      this.clearErrors();
      $(".fleet-amenity-pill").removeClass("selected");

      $("#vehicleModalTitle").text("Add New Fleet Vehicle");
      $("#vehicleModalSubtitle").text(
        "Configure vehicle model specifications, photo gallery, tariff rates, and seating capacity",
      );
      $("#btnSaveVehicleText").text("Save Vehicle");
      $("#modalHeaderIcon").attr("class", "fi fi-rr-car-alt");

      $("#vehicleModalOverlay").addClass("active is-active");
      this.initSelect2Dropdowns();
      if ($.fn.select2) {
        $("#modalVehicleType").val("").trigger("change");
        $("#modalVehicleFuel").val("").trigger("change");
      }

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
      $("#modalVehicleFuel").val(v.fuel_type || "");
      $("#modalVehicleSeating").val(v.seating > 0 ? v.seating : "");
      $("#modalVehicleLuggage").val(v.luggage > 0 ? v.luggage : "");
      $("#modalVehicleRateWithTax").val(
        v.rate_per_km_with_tax > 0 ? v.rate_per_km_with_tax : "",
      );
      $("#modalVehicleRateWithoutTax").val(
        v.rate_per_km_without_tax > 0 ? v.rate_per_km_without_tax : "",
      );
      $("#modalVehicleDescription").val(v.description || "");
      this.clearErrors();

      // Feature Cover Photo
      this.featureImageFile = null;
      if (v.thumbnail) {
        this.featureImageUrl = v.thumbnail;
        $("#modalVehicleThumbnail").val(v.thumbnail);
        $("#fleetFeatureImg").attr("src", v.thumbnail);
        $("#fleetFeatureActiveState").removeClass("is-hidden");
        $("#fleetFeatureEmptyState").addClass("is-hidden");
        $("#fleetFeaturePreviewZone").addClass("has-image");
      } else {
        this.clearFeaturePhoto();
      }

      // Populate Gallery Items
      this.galleryItems = [];
      const galleryList = Array.isArray(v.gallery) ? v.gallery : [];
      galleryList.forEach(function (path, idx) {
        if (!path) return;
        V3TransporterVehicles.galleryItems.push({
          id: "ex_" + idx,
          path: path,
          file: null,
          isExisting: true,
          isCover: path === v.thumbnail,
        });
      });
      this.renderGalleryGrid();
      this.refreshAmenityPillStates();

      $("#vehicleModalTitle").text(`Edit Fleet Vehicle — ${v.title}`);
      $("#vehicleModalSubtitle").text(
        "Update model specifications, photo gallery, per KM rates, and seating capacity",
      );
      $("#btnSaveVehicleText").text("Update Vehicle");
      $("#modalHeaderIcon").attr("class", "fi fi-rr-settings-sliders");

      $("#vehicleModalOverlay").addClass("active is-active");
      this.initSelect2Dropdowns();

      // Sync select2 values
      if ($.fn.select2) {
        $("#modalVehicleType").val(v.type).trigger("change");
        $("#modalVehicleFuel").val(v.fuel_type || "").trigger("change");
      }

      setTimeout(() => $("#modalVehicleTitle").focus(), 150);
    },

    closeModal: function () {
      if ($.fn.select2) {
        try {
          $("#modalVehicleType").select2("close");
        } catch (e) {}
        try {
          $("#modalVehicleFuel").select2("close");
        } catch (e) {}
      }
      $("#vehicleModalOverlay").removeClass("active is-active");
      this.editingVehicleId = null;
      this.featureImageFile = null;
      this.featureImageUrl = "";
      this.galleryItems = [];
    },

    clearErrors: function () {
      $(".v3-field-error").text("");
      $(".v3-form-control").removeClass("is-invalid");
      $("#modalVehicleType").next(".select2-container").removeClass("is-invalid");
    },

    // -------------------------------------------------------------
    // Form Submission
    // -------------------------------------------------------------
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
      const type = $("#modalVehicleType").val() ? $("#modalVehicleType").val().trim() : "";
      const fuelType = $("#modalVehicleFuel").val() ? $("#modalVehicleFuel").val().trim() : "";
      const seating = parseInt($("#modalVehicleSeating").val(), 10) || 0;
      const luggage = parseInt($("#modalVehicleLuggage").val(), 10) || 0;
      const rateWithTax = parseFloat($("#modalVehicleRateWithTax").val()) || 0;
      const rateWithoutTax = parseFloat($("#modalVehicleRateWithoutTax").val()) || 0;
      const description = $("#modalVehicleDescription").val().trim();

      let hasError = false;
      if (!title) {
        $("#modalVehicleTitle").addClass("is-invalid");
        $("#errModalTitle").text("Please enter vehicle model name.");
        hasError = true;
      }
      if (!type) {
        $("#modalVehicleType").next(".select2-container").addClass("is-invalid");
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
      formData.append("fuel_type", fuelType);
      formData.append("seating", seating);
      formData.append("luggage", luggage);
      formData.append("rate_per_km_with_tax", rateWithTax);
      formData.append("rate_per_km_without_tax", rateWithoutTax);
      formData.append("description", description);

      // Feature Cover Photo
      if (self.featureImageFile) {
        formData.append("feature_image", self.featureImageFile);
      } else if (self.featureImageUrl) {
        formData.append("thumbnail", self.featureImageUrl);
      }

      // Existing retained gallery image paths
      const existingPaths = self.galleryItems
        .filter((it) => it.isExisting && it.path)
        .map((it) => it.path);
      formData.append("existing_gallery", JSON.stringify(existingPaths));

      // Newly uploaded gallery image files
      self.galleryItems.forEach(function (item) {
        if (!item.isExisting && item.file) {
          formData.append("gallery_files[]", item.file);
        }
      });

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

    // -------------------------------------------------------------
    // Delete Vehicle
    // -------------------------------------------------------------
    handleDeleteVehicle: function (vehicleId, title) {
      const self = this;
      if (typeof swal === "function") {
        swal({
          title: "Delete Vehicle?",
          text: `Are you sure you want to delete '${title}' from the fleet master directory?`,
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
