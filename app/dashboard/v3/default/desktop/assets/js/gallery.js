/**
 * V3 Package Gallery Module
 * Handles dynamic API loading, day assignment, Pexels stock photos, lightbox, and image upload.
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

  window.V3Gallery = {
    packageId: "",
    encodedId: "",
    galleryId: 0,
    totalDays: 1,
    galleryData: null,
    pexelsLoaded: false,
    pexelsSuggestionsData: [],
    currentPexelsPage: 1,
    activeSearchQuery: "",
    previewImages: [],
    currentLightboxIndex: 0,
    selectedUploadFiles: [],
    eventsBound: false,
    isLoading: false,
    loadedPackageId: null,

    init: function (opts) {
      if (!opts || !opts.packageId) {
        return;
      }
      const $grid = $("#galleryGrid");
      if (!$grid.length) {
        return;
      }
      const pkgId = String(opts.packageId);
      if (this.loadedPackageId === pkgId && (this.isLoading || this.galleryData)) {
        return;
      }
      this.loadedPackageId = pkgId;
      this.packageId = pkgId;
      this.encodedId = opts.encodedId || "";
      this.galleryData = null;
      this.pexelsLoaded = false;
      this.pexelsSuggestionsData = [];
      this.currentPexelsPage = 1;
      this.previewImages = [];
      this.selectedUploadFiles = [];
      this.bindEvents();
      this.loadGalleryFromApi();
    },

    autoInit: function () {
      const $wrapper = $("#packageGalleryApp, .package-gallery-wrapper");
      if ($wrapper.length > 0) {
        let pkgId = $wrapper.attr("data-package-id") || $wrapper.data("package-id");
        let encId = $wrapper.attr("data-encoded-id") || $wrapper.data("encoded-id") || "";

        if (!pkgId || pkgId === "0") {
          const urlParams = new URLSearchParams(window.location.search);
          const rawId = urlParams.get("id");
          if (rawId) {
            try {
              const decoded = atob(rawId);
              if (decoded && !isNaN(decoded)) {
                pkgId = decoded;
                encId = rawId;
              } else if (!isNaN(rawId)) {
                pkgId = rawId;
                encId = btoa(rawId);
              }
            } catch (e) {
              if (!isNaN(rawId)) {
                pkgId = rawId;
                encId = btoa(rawId);
              }
            }
          }
        }

        if (pkgId && pkgId !== "0") {
          this.init({ packageId: pkgId, encodedId: encId });
        }
      }
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // 1. Single Upload Image Trigger -> Open Modal Popup
      $(document).on("click", ".open-gallery-upload-modal, #btnOpenGalleryUpload", function (e) {
        e.preventDefault();
        self.openUploadModal();
      });

      // Close Upload Modal Popup
      $(document).on("click", ".closeGalleryModal, .galleryBox .popUpBtn[onclick*='closePopup'], .galleryBox .closePopup", function (e) {
        e.preventDefault();
        self.closeUploadModal();
      });

      // Upload submit button (prevent double submit with off().on())
      $(document)
        .off("click", "#btnSubmitGalleryUpload, #addGallery")
        .on("click", "#btnSubmitGalleryUpload, #addGallery", function (e) {
          e.preventDefault();
          e.stopPropagation();
          self.submitGalleryUpload();
        });

      // DropZone Click -> Open Native File Picker
      $(document).on("click", "#dropZone", function (e) {
        if ($(e.target).closest(".removeBtn").length > 0) return;
        $("#fileInput").trigger("click");
      });

      // DropZone Drag and Drop Handlers
      $(document).on("dragenter dragover", "#dropZone", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass("dragover");
      });

      $(document).on("dragleave", "#dropZone", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("dragover");
      });

      $(document).on("drop", "#dropZone", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("dragover");
        const dt = e.originalEvent && e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer : null;
        if (dt && dt.files && dt.files.length > 0) {
          self.handleFilesAdded(dt.files);
        }
      });

      // File Input Change
      $(document).on("change", "#fileInput", function () {
        if (this.files && this.files.length > 0) {
          self.handleFilesAdded(this.files);
        }
      });

      // Remove individual photo preview from dropZone
      $(document).on("click", "#dropZone .removeBtn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const index = parseInt($(this).closest(".previewItem").data("index"), 10);
        self.removeUploadFile(index);
      });

      // Real-time client-side search inside gallery
      $(document).on("input keyup", "#galleryLocalSearch", function () {
        self.applyGalleryFilters();
      });

      // Individual Card Checkbox Selection
      $(document).on("change", ".gallery-select-checkbox", function () {
        const $card = $(this).closest(".dash-gallery-card");
        if ($(this).is(":checked")) {
          $card.addClass("is-selected");
        } else {
          $card.removeClass("is-selected");
        }
        self.updateBulkSelectionState();
      });

      // "Select All" Checkbox Toggle
      $(document).on("change", "#chkSelectAllGallery", function () {
        const isChecked = $(this).is(":checked");
        $(".dash-gallery-card:not(.is-filtered-out)").each(function () {
          const $chk = $(this).find(".gallery-select-checkbox");
          $chk.prop("checked", isChecked);
          if (isChecked) {
            $(this).addClass("is-selected");
          } else {
            $(this).removeClass("is-selected");
          }
        });
        self.updateBulkSelectionState();
      });

      // "Unselect All" Button Click
      $(document).on("click", "#btnUnselectAllGallery", function (e) {
        e.preventDefault();
        self.clearSelection();
      });

      // Single Image Delete Button
      $(document).on("click", ".deleteSingleGalleryImage", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const imagePath = $(this).data("image");
        if (!imagePath) return;

        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Delete Photo?",
            text: "Are you sure you want to permanently delete this photo from the gallery?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Delete"
          }).then(function (result) {
            if (result.value) {
              self.executeDeleteImages([imagePath]);
            }
          });
        } else if (confirm("Are you sure you want to permanently delete this photo?")) {
          self.executeDeleteImages([imagePath]);
        }
      });

      // Bulk Delete Selected Photos Button
      $(document).on("click", "#btnBulkDeleteGallery", function (e) {
        e.preventDefault();
        const selectedImages = [];
        $(".gallery-select-checkbox:checked").each(function () {
          const img = $(this).data("image");
          if (img) selectedImages.push(img);
        });

        if (selectedImages.length === 0) {
          if (typeof popUpMessage === "function") {
            popUpMessage("center", "Please select at least one photo to delete.", "warning");
          }
          return;
        }

        const countText = selectedImages.length === 1 ? "1 photo" : selectedImages.length + " photos";
        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Delete " + countText + "?",
            text: "Are you sure you want to delete " + countText + "? This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Delete Selected"
          }).then(function (result) {
            if (result.value) {
              self.executeDeleteImages(selectedImages);
            }
          });
        } else if (confirm("Are you sure you want to delete " + countText + "?")) {
          self.executeDeleteImages(selectedImages);
        }
      });

      // Tab switching
      $(document).on("click", ".theme-filter-btn", function () {
        const btnId = $(this).attr("id");

        if (btnId === "btnToggleSuggestions") {
          $(".theme-filter-btn").removeClass("active");
          $(this).addClass("active");

          $("#galleryGrid").hide();
          $("#galleryBulkBar").hide();
          $("#pexelsSuggestionPanel").slideDown(200);

          if (!self.pexelsLoaded) {
            self.loadPexelsSuggestions();
          }
        } else {
          $(".theme-filter-btn").removeClass("active");
          $(this).addClass("active");

          $("#pexelsSuggestionPanel").slideUp(200);
          $("#galleryGrid").show();

          self.applyGalleryFilters();
        }
      });

      // Pexels Search & Quick Filters
      $(document).on("click", ".pexels-tag-btn", function () {
        $(".pexels-tag-btn").removeClass("active");
        $(this).addClass("active");
        const tag = $(this).data("tag");

        const defaultQuery = self.galleryData && self.galleryData.package ? self.galleryData.package.defaultSearchQuery : "";
        let searchQuery = defaultQuery;
        if (tag) {
          searchQuery = defaultQuery + " " + tag;
        }
        $("#pexelsSearchQuery").val(searchQuery);
        self.loadPexelsSuggestions(searchQuery);
      });

      $(document).on("click", "#btnSearchPexels", function () {
        const q = $("#pexelsSearchQuery").val().trim();
        self.loadPexelsSuggestions(q);
      });

      $(document).on("keypress", "#pexelsSearchQuery", function (e) {
        if (e.which === 13) {
          e.preventDefault();
          $("#btnSearchPexels").click();
        }
      });

      // Infinite load more Pexels photos
      $(document).on("click", "#btnLoadMorePexels", function () {
        self.currentPexelsPage++;
        self.loadPexelsSuggestions(self.activeSearchQuery, true);
      });

      // Single Add Suggested Pexels Photo
      $(document)
        .off("click", ".btn-add-single-pexels")
        .on("click", ".btn-add-single-pexels", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const $btn = $(this);
          if ($btn.attr("data-busy") === "1") return;
          $btn.attr("data-busy", "1").prop("disabled", true);

          const idx = $btn.data("idx");
          const item = self.pexelsSuggestionsData[idx];
          const chosenDay =
            parseInt(
              $btn
                .closest(".pexels-card-body")
                .find(".pexels-day-select")
                .val()
            ) || 0;

          $btn.html('<i class="fi fi-rr-spinner spinner"></i> Adding...');

          $.ajax({
            url: "/api/v3/gallery/add-suggested/",
            type: "POST",
            data: {
              packageId: self.packageId,
              galleryId: self.galleryId,
              imagePath: item.url,
              day: chosenDay,
            },
            dataType: "json",
            success: function (res) {
              $btn.removeAttr("data-busy");
              if (res.status === "success") {
                if (typeof popUpMessage === "function") {
                  popUpMessage("center", res.msg, "success");
                }
                self.loadGalleryFromApi();
                $btn.html('<i class="fi fi-rr-check"></i> Added');
              } else {
                $btn
                  .prop("disabled", false)
                  .html('<i class="fi fi-rr-plus"></i> Add to Gallery');
                if (typeof popUpMessage === "function") {
                  popUpMessage("center", res.msg || "Failed to add image", "error");
                }
              }
            },
            error: function () {
              $btn
                .removeAttr("data-busy")
                .prop("disabled", false)
                .html('<i class="fi fi-rr-plus"></i> Add to Gallery');
              if (typeof popUpMessage === "function") {
                popUpMessage("center", "Error adding image to gallery", "error");
              }
            },
          });
        });

      // Auto-assign All Suggested Day Photos
      $(document)
        .off("click", "#btnAddAllSuggested")
        .on("click", "#btnAddAllSuggested", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const $btn = $(this);
          if ($btn.attr("data-busy") === "1") return;
          $btn.attr("data-busy", "1").prop("disabled", true);

          $btn.html(
            '<i class="fi fi-rr-spinner spinner"></i> Searching & Auto-Assigning Day Photos...'
          );

          $.ajax({
            url: "/api/v3/gallery/add-suggested/",
            type: "POST",
            data: {
              packageId: self.packageId,
              galleryId: self.galleryId,
              autoAssign: 1,
            },
            dataType: "json",
            success: function (res) {
              $btn.removeAttr("data-busy");
              if (res.status === "success") {
                if (typeof popUpMessage === "function") {
                  popUpMessage("center", res.msg, "success");
                }
                self.loadGalleryFromApi();
              } else {
                $btn
                  .prop("disabled", false)
                  .html(
                    '<i class="fi fi-rr-magic-wand"></i> ✨ Auto-Assign All Day Photos'
                  );
                if (typeof popUpMessage === "function") {
                  popUpMessage(
                    "center",
                    res.msg || "Failed to assign suggested images",
                    "error"
                  );
                }
              }
            },
            error: function () {
              $btn
                .removeAttr("data-busy")
                .prop("disabled", false)
                .html(
                  '<i class="fi fi-rr-magic-wand"></i> ✨ Auto-Assign All Day Photos'
                );
              if (typeof popUpMessage === "function") {
                popUpMessage("center", "Error assigning suggested images", "error");
              }
            },
          });
        });

      // Day Assignment dropdown changes
      $(document).on("change", ".assign-day-select", function () {
        const $select = $(this);
        const imagePath = $select.data("image");
        const newDay = parseInt($select.val()) || 0;
        const $card = $select.closest(".dash-gallery-card");

        $select.prop("disabled", true);

        $.ajax({
          url: "/api/v3/gallery/assign-day/",
          type: "POST",
          data: {
            packageId: self.packageId,
            galleryId: self.galleryId,
            imagePath: imagePath,
            day: newDay,
          },
          dataType: "json",
          success: function (res) {
            $select.prop("disabled", false);
            if (res.status === "success") {
              if (typeof popUpMessage === "function") {
                popUpMessage("center", res.msg, "success");
              }

              // Update data state and re-render/recalculate
              self.loadGalleryFromApi();
            } else {
              if (typeof popUpMessage === "function") {
                popUpMessage("center", res.msg || "Failed to assign day", "error");
              }
            }
          },
          error: function (err) {
            $select.prop("disabled", false);
            const msg =
              err.responseJSON && err.responseJSON.msg
                ? err.responseJSON.msg
                : "An error occurred";
            if (typeof popUpMessage === "function") {
              popUpMessage("center", msg, "error");
            }
          },
        });
      });

      // Set as Featured Image
      $(document).on("click", ".setGalleryFeatureImage", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const image = $btn.data("image");
        const action = $btn.data("action") || "set";
        const categoryId = self.packageId;
        const galleryId = self.galleryId;

        $btn.addClass("disabled").prop("disabled", true);

        $.ajax({
          type: "POST",
          url: "/api/v3/gallery/set-featured-image/",
          data: {
            featureImage: image,
            action: action,
            categoryId: categoryId,
            galleryId: galleryId,
          },
          dataType: "json",
          success: function (response) {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", response.msg, response.status);
            }
            self.loadGalleryFromApi();
          },
          error: function (error) {
            $btn.removeClass("disabled").prop("disabled", false);
            if (typeof popUpMessage === "function") {
              popUpMessage(
                "center",
                error.responseJSON ? error.responseJSON.msg : "Failed to update featured image",
                error.responseJSON ? error.responseJSON.status : "error"
              );
            }
          },
        });
      });

      // Lightbox preview triggers
      $(document).on("click", ".btn-preview-img", function (e) {
        e.preventDefault();
        self.collectPreviewImages();
        const src = $(this).data("image");
        self.currentLightboxIndex = self.previewImages.findIndex(
          (img) => img.src === src
        );
        if (self.currentLightboxIndex < 0) self.currentLightboxIndex = 0;
        self.showLightboxModal();
      });

      $(document).on("click", ".btn-preview-pexels-img", function (e) {
        e.preventDefault();
        const fullUrl = $(this).data("url");
        const alt = $(this).data("alt") || "Pexels Stock Photo";

        $("#lightboxImg").attr("src", fullUrl);
        $("#lightboxTitle").text(alt);
        $("#lightboxDayBadge")
          .removeClass("is-orange")
          .addClass("is-green")
          .html('<i class="fi fi-rr-magic-wand"></i> Pexels High-Res Stock Photo');
        $("#lightboxModal").addClass("active");
      });

      $(document).on("click", "#lightboxClose", function () {
        $("#lightboxModal").removeClass("active");
      });

      $(document).on("click", "#lightboxPrev", function () {
        if (self.previewImages.length === 0) return;
        self.currentLightboxIndex =
          (self.currentLightboxIndex - 1 + self.previewImages.length) %
          self.previewImages.length;
        self.showLightboxModal();
      });

      $(document).on("click", "#lightboxNext", function () {
        if (self.previewImages.length === 0) return;
        self.currentLightboxIndex =
          (self.currentLightboxIndex + 1) % self.previewImages.length;
        self.showLightboxModal();
      });

      $(document).on("keydown", function (e) {
        if ($("#lightboxModal").hasClass("active")) {
          if (e.key === "Escape") $("#lightboxModal").removeClass("active");
          if (e.key === "ArrowLeft") $("#lightboxPrev").click();
          if (e.key === "ArrowRight") $("#lightboxNext").click();
        }
      });
    },

    openUploadModal: function () {
      $(".popUp").hide();
      this.selectedUploadFiles = [];
      this.syncFileInputAndPreviews();
      $(".galleryBox").css("display", "flex").fadeIn(200);
    },

    closeUploadModal: function () {
      this.isUploading = false;
      $(".galleryBox").fadeOut(200);
      const formElement = document.getElementById("addGalleryForm");
      if (formElement) formElement.reset();
      this.selectedUploadFiles = [];
      this.syncFileInputAndPreviews();
    },

    handleFilesAdded: function (fileList) {
      const self = this;
      if (!fileList || fileList.length === 0) return;

      Array.from(fileList).forEach(function (file) {
        // Accept images only
        if (file.type && !file.type.match(/^image\//i)) {
          return;
        }
        self.selectedUploadFiles.push(file);
      });

      self.syncFileInputAndPreviews();
    },

    removeUploadFile: function (index) {
      const self = this;
      if (index >= 0 && index < self.selectedUploadFiles.length) {
        self.selectedUploadFiles.splice(index, 1);
      }
      self.syncFileInputAndPreviews();
    },

    syncFileInputAndPreviews: function () {
      const self = this;
      const $dropZone = $("#dropZone");
      $dropZone.find(".previewItem").remove();

      // Synchronize native input files via DataTransfer if supported
      try {
        const dt = new DataTransfer();
        self.selectedUploadFiles.forEach(function (file) {
          dt.items.add(file);
        });
        const fileInput = document.getElementById("fileInput");
        if (fileInput) {
          fileInput.files = dt.files;
        }
      } catch (err) {
        // Browser fallback
      }

      if (self.selectedUploadFiles.length > 0) {
        $dropZone.addClass("has-files");
        $dropZone.find(".defaultText").addClass("is-hidden").hide();

        self.selectedUploadFiles.forEach(function (file, idx) {
          const objectUrl = URL.createObjectURL(file);
          const previewHtml = `
            <div class="previewItem" data-index="${idx}" title="${file.name}">
              <img src="${objectUrl}" alt="${file.name}" />
              <button type="button" class="removeBtn" title="Remove photo">
                <i class="fi fi-rr-cross"></i>
              </button>
            </div>
          `;
          $dropZone.append(previewHtml);
        });
      } else {
        $dropZone.removeClass("has-files");
        $dropZone.find(".defaultText").removeClass("is-hidden").show();
        const fileInput = document.getElementById("fileInput");
        if (fileInput) {
          fileInput.value = "";
        }
      }
    },

    updateDropZoneState: function () {
      this.syncFileInputAndPreviews();
    },

    applyGalleryFilters: function () {
      const activeFilter = $(".gallery-filter-tabs .theme-filter-btn.active").data("filter") || "all";
      const query = $.trim($("#galleryLocalSearch").val()).toLowerCase();

      $(".dash-gallery-card").each(function () {
        const $card = $(this);
        const day = parseInt($card.attr("data-day")) || parseInt($card.data("day")) || 0;
        const dayStr = day.toString();
        const dayText = "day " + dayStr;

        let matchesTab = true;
        if (activeFilter === "assigned") {
          matchesTab = (day > 0);
        } else if (activeFilter === "unassigned") {
          matchesTab = (day === 0);
        }

        let matchesSearch = true;
        if (query) {
          matchesSearch = (dayStr.includes(query) || dayText.includes(query));
        }

        if (matchesTab && matchesSearch) {
          $card.removeClass("is-filtered-out").show();
        } else {
          $card.addClass("is-filtered-out").hide();
        }
      });

      this.updateBulkSelectionState();
    },

    submitGalleryUpload: function () {
      const self = this;
      if (self.isUploading) return;

      if (!self.selectedUploadFiles || self.selectedUploadFiles.length === 0) {
        if (typeof popUpMessage === "function") {
          popUpMessage("center", "Please select or drop at least one image to upload", "warning");
        }
        return;
      }

      self.isUploading = true;
      const $btn = $("#btnSubmitGalleryUpload, #addGallery");
      $btn.prop("disabled", true).addClass("disabled").html('<i class="fi fi-rr-spinner spinner"></i> Uploading...');

      const data = new FormData();
      data.append("packageId", self.encodedId || btoa(self.packageId));
      self.selectedUploadFiles.forEach(function (file) {
        data.append("gallery[]", file);
      });

      $.ajax({
        url: "/api/v3/gallery/publish/",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        dataType: "JSON",
        encrypt: "multipart/form-data",
        success: function (response) {
          self.isUploading = false;
          $btn.prop("disabled", false).removeClass("disabled").html('<i class="fi-rr-check"></i> Upload Now');
          if (typeof popUpMessage === "function") {
            popUpMessage("center", response.msg, response.status);
          }
          self.closeUploadModal();
          // Reload gallery items via API
          self.loadGalleryFromApi();
        },
        error: function (error) {
          self.isUploading = false;
          $btn.prop("disabled", false).removeClass("disabled").html('<i class="fi-rr-check"></i> Upload Now');
          const msg =
            error.responseJSON && error.responseJSON.msg
              ? error.responseJSON.msg
              : "Upload failed. Please check files and try again.";
          const status =
            error.responseJSON && error.responseJSON.status
              ? error.responseJSON.status
              : "error";
          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, status);
          }
        },
      });
    },

    loadGalleryFromApi: function () {
      const self = this;
      const $grid = $("#galleryGrid");
      self.isLoading = true;

      $.ajax({
        url: "/api/v3/gallery/list/",
        type: "GET",
        data: { packageId: self.packageId },
        dataType: "json",
        success: function (res) {
          self.isLoading = false;
          if (res.status === "success") {
            self.galleryData = res;
            self.galleryId = res.galleryId || 0;
            self.totalDays = res.package.totalDays || 1;

            self.renderHeaderMeta(res.package);
            self.renderKpis(res.stats);
            self.renderFilterTabs(res.stats);
            self.renderGalleryGrid(res.images, res.assignedDayMap);

            if (!self.pexelsLoaded && (!res.images || res.images.length === 0)) {
              self.loadPexelsSuggestions();
            }
          }
        },
        error: function () {
          self.isLoading = false;
          $grid.html(
            '<div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #ef4444;">Failed to load gallery data from API. Please refresh the page.</div>'
          );
        },
      });
    },

    renderHeaderMeta: function (pkg) {
      if (!pkg) return;
      $("#pkgHeaderTitle").text(pkg.title + " — Gallery");
      $("#pkgMetaTheme")
        .attr("class", "pkg-theme-pill " + pkg.themeClass)
        .text(pkg.themeLabel);
      $("#pkgMetaDuration").html(
        `<i class="fi-rr-moon" style="color: #6366f1;"></i> ${pkg.duration}N / <i class="fi-rr-sun" style="color: #f59e0b;"></i> ${pkg.totalDays}D`
      );
      $("#pkgMetaLocation").html(
        `<i class="fi-rr-marker" style="color: #0284c7;"></i> ${pkg.city || pkg.country || "Domestic"}`
      );
      if (pkg.linkUrl) {
        $("#btnViewLivePackage").attr("href", pkg.linkUrl).show();
      }
      if (!$("#pexelsSearchQuery").val()) {
        $("#pexelsSearchQuery").val(pkg.defaultSearchQuery || "");
      }
    },

    renderKpis: function (stats) {
      if (!stats) return;
      $("#statTotalImages").text(stats.totalPhotos || 0);
      $("#statDaysCovered").text(
        (stats.daysCovered || 0) + " / " + (stats.totalDays || 1)
      );
      $("#statDaysMissing").text(stats.daysMissing || 0);
      $("#statFeaturedCover").text(stats.isFeaturedSet ? "Assigned" : "Not Set");

      const $missingCard = $("#statDaysMissing").closest(".pkg-mini-kpi");
      if (stats.daysMissing > 0) {
        $missingCard.removeClass("emerald").addClass("amber");
      } else {
        $missingCard.removeClass("amber").addClass("emerald");
      }
    },

    renderFilterTabs: function (stats) {
      if (!stats) return;
      $("#tabCountAll").text(stats.totalPhotos || 0);
      $("#tabCountAssigned").text(stats.assignedPhotos || 0);
      $("#tabCountUnassigned").text(stats.unassignedPhotos || 0);
    },

    updateBulkSelectionState: function () {
      const isSuggestionsActive = $("#btnToggleSuggestions").hasClass("active");
      if (isSuggestionsActive) {
        $("#galleryBulkBar").removeClass("is-visible").hide();
        return;
      }

      const $visibleCheckboxes = $(".dash-gallery-card:not(.is-filtered-out) .gallery-select-checkbox");
      const totalVisible = $visibleCheckboxes.length;
      const totalChecked = $visibleCheckboxes.filter(":checked").length;

      if (totalChecked > 0) {
        $("#galleryBulkBar").addClass("is-visible").show();
        $("#selectedGalleryCount").text(totalChecked + (totalChecked === 1 ? " photo selected" : " photos selected"));
        $("#btnDeleteCount").text(totalChecked);
      } else {
        $("#galleryBulkBar").removeClass("is-visible").hide();
      }

      const $selectAll = $("#chkSelectAllGallery");
      if (totalVisible > 0 && totalChecked === totalVisible) {
        $selectAll.prop("checked", true).prop("indeterminate", false);
      } else if (totalChecked > 0 && totalChecked < totalVisible) {
        $selectAll.prop("checked", false).prop("indeterminate", true);
      } else {
        $selectAll.prop("checked", false).prop("indeterminate", false);
      }
    },

    clearSelection: function () {
      $(".gallery-select-checkbox").prop("checked", false);
      $(".dash-gallery-card").removeClass("is-selected");
      $("#chkSelectAllGallery").prop("checked", false).prop("indeterminate", false);
      $("#galleryBulkBar").removeClass("is-visible").hide();
    },

    executeDeleteImages: function (imagesArray) {
      const self = this;
      if (!imagesArray || imagesArray.length === 0) return;

      if (typeof showPageLoader === "function") showPageLoader();

      $.ajax({
        url: "/api/v3/gallery/delete/",
        type: "POST",
        data: {
          packageId: self.packageId,
          galleryId: self.galleryId,
          images: imagesArray
        },
        dataType: "json",
        success: function (res) {
          if (typeof hidePageLoader === "function") hidePageLoader();
          if (res.status === "success") {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.message || "Photos deleted successfully.", "success");
            }
            self.clearSelection();
            self.loadGalleryFromApi();
          } else {
            if (typeof popUpMessage === "function") {
              popUpMessage("center", res.message || "Failed to delete photos.", "error");
            }
          }
        },
        error: function (xhr) {
          if (typeof hidePageLoader === "function") hidePageLoader();
          const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Error deleting photos.";
          if (typeof popUpMessage === "function") {
            popUpMessage("center", msg, "error");
          }
        }
      });
    },

    renderGalleryGrid: function (images, assignedDayMap) {
      const self = this;
      const $grid = $("#galleryGrid");

      if (!images || images.length === 0) {
        self.clearSelection();
        $grid.html(`
          <div class="empty-state" style="grid-column: 1 / -1; padding: 50px 20px; text-align: center; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <i class="fi-rr-picture" style="font-size: 36px; color: #94a3b8;"></i>
            <p style="margin-top: 10px; color: #64748b;">No gallery photos uploaded yet. Pick suggested photos or click <b>Upload Image</b> to add photos.</p>
          </div>
        `);
        return;
      }

      let html = "";
      images.forEach((item, index) => {
        const assignedDay = item.day || 0;
        const isFeatured = item.isFeatured || false;
        let cardClasses = "dash-gallery-card";
        if (assignedDay > 0) cardClasses += " is-assigned";
        if (isFeatured) cardClasses += " is-featured";

        let dayBadgeHtml =
          assignedDay > 0
            ? `<span class="dash-badge-chip badge-day"><i class="fi fi-rr-calendar"></i> Day ${assignedDay}</span>`
            : `<span class="dash-badge-chip badge-none">Unassigned</span>`;

        let featBadgeHtml = isFeatured
          ? `<span class="dash-badge-chip badge-star"><i class="fi fi-rr-star"></i> Featured</span>`
          : "";

        let optionsHtml = `<option value="0">-- Unassigned --</option>`;
        for (let d = 1; d <= self.totalDays; d++) {
          const sel = assignedDay === d ? "selected" : "";
          const occupiedByOther =
            assignedDayMap &&
            assignedDayMap[d] &&
            assignedDayMap[d] !== item.path;
          const labelSuffix = occupiedByOther ? " (Occupied)" : "";
          optionsHtml += `<option value="${d}" ${sel}>Day ${d}${labelSuffix}</option>`;
        }

        html += `
          <div class="${cardClasses}"
               data-index="${index}"
               data-image="${item.path}"
               data-day="${assignedDay}"
               data-featured="${isFeatured ? "1" : "0"}">
            <label class="dash-card-check-wrap" data-tooltip="Select photo for deletion">
              <input type="checkbox" class="gallery-select-checkbox" data-image="${item.path}">
              <span class="dash-card-checkbox-custom"></span>
            </label>
            <div class="dash-thumb-container">
              <img src="${item.path}" alt="${item.name}" loading="lazy">
              <div class="dash-card-badges">
                <div>${dayBadgeHtml}</div>
                <div>${featBadgeHtml}</div>
              </div>
              <div class="dash-thumb-overlay">
                <button type="button" class="dash-preview-btn btn-preview-img"
                        data-image="${item.path}"
                        data-day="${assignedDay}"
                        data-name="${item.name}"
                        data-tooltip="Enlarge Photo Preview">
                  <i class="fi fi-rr-search"></i>
                </button>
              </div>
            </div>
            <div class="dash-card-body">
              <div class="dash-day-select-wrap">
                <label><i class="fi fi-rr-calendar"></i> Assign Day:</label>
                <select class="selectJS form-control assign-day-select" data-image="${item.path}">
                  ${optionsHtml}
                </select>
              </div>
              <div class="dash-card-actions">
                ${
                  isFeatured
                    ? `<a href="#" class="dash-btn-featured setGalleryFeatureImage is-active"
                          data-image="${item.path}"
                          data-action="remove"
                          data-tooltip="Click to remove as featured cover image">
                         <i class="fi fi-rr-star-slash"></i> Remove Featured
                       </a>`
                    : `<a href="#" class="dash-btn-featured setGalleryFeatureImage"
                          data-image="${item.path}"
                          data-action="set"
                          data-tooltip="Set as package cover photo">
                         <i class="fi fi-rr-star"></i> Set as Featured
                       </a>`
                }
                <button type="button" class="dash-btn-delete deleteSingleGalleryImage"
                        data-image="${item.path}"
                        data-tooltip="Delete photo">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </div>
          </div>
        `;
      });

      $grid.html(html);

      if ($.fn.select2) {
        $grid.find(".assign-day-select").select2({
          minimumResultsForSearch: Infinity,
          width: "100%",
        });
      }

      self.applyGalleryFilters();
      self.updateBulkSelectionState();
    },

    loadPexelsSuggestions: function (customQuery, isAppend) {
      const self = this;
      if (!isAppend) {
        self.currentPexelsPage = 1;
        self.activeSearchQuery = customQuery || "";
        self.pexelsSuggestionsData = [];
        const loadingHtml = `
          <div class="pexels-loading-state" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #0284c7;">
            <i class="fi fi-rr-spinner" style="font-size: 24px; animation: spin 1s linear infinite;"></i>
            <p style="margin-top: 8px; font-weight: 600;">Fetching day-wise stock photos from Pexels...</p>
          </div>
        `;
        $("#pexelsGrid").html(loadingHtml);
        $("#btnLoadMorePexels").hide();
      } else {
        $("#btnLoadMorePexels")
          .prop("disabled", true)
          .html('<i class="fi fi-rr-spinner spinner"></i> Fetching More...');
      }

      $.ajax({
        url: "/api/v3/gallery/suggest-images/",
        type: "GET",
        data: {
          packageId: self.packageId,
          query: self.activeSearchQuery,
          page: self.currentPexelsPage,
        },
        dataType: "json",
        success: function (res) {
          self.pexelsLoaded = true;
          $("#btnLoadMorePexels")
            .prop("disabled", false)
            .html('<i class="fi fi-rr-refresh"></i> Load More Stock Photos');

          if (res.status === "success" && res.suggestions && res.suggestions.length > 0) {
            const startIndex = self.pexelsSuggestionsData.length;
            self.pexelsSuggestionsData = self.pexelsSuggestionsData.concat(res.suggestions);
            self.renderPexelsGrid(res.suggestions, isAppend, startIndex);

            if (res.hasMore) {
              $("#btnLoadMorePexels").show();
            } else {
              $("#btnLoadMorePexels").hide();
            }
          } else {
            if (!isAppend) {
              $("#pexelsGrid").html(
                '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">No stock photo suggestions found. Try searching another keyword above.</div>'
              );
            }
            $("#btnLoadMorePexels").hide();
          }
        },
        error: function () {
          $("#btnLoadMorePexels")
            .prop("disabled", false)
            .html('<i class="fi fi-rr-refresh"></i> Load More Stock Photos');
          if (!isAppend) {
            $("#pexelsGrid").html(
              '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #ef4444;">Failed to fetch Pexels suggestions. Please check internet connection or try again.</div>'
            );
          }
        },
      });
    },

    renderPexelsGrid: function (suggestions, isAppend, startIndex) {
      const self = this;
      let html = "";
      suggestions.forEach((item, idx) => {
        const dataIndex = startIndex + idx;
        const dayLabel = item.day > 0 ? "Day " + item.day : "Suggested";
        html += `
          <div class="pexels-card">
            <div class="pexels-thumb-wrap">
              <img src="${item.thumb}" alt="${item.alt}" loading="lazy">
              <div class="dash-card-badges">
                <span class="dash-badge-chip badge-day"><i class="fi fi-rr-calendar"></i> ${dayLabel}</span>
              </div>
              <div class="dash-thumb-overlay">
                <button type="button" class="dash-preview-btn btn-preview-pexels-img"
                        data-url="${item.url}"
                        data-alt="${item.alt}"
                        data-tooltip="View Full Image">
                  <i class="fi fi-rr-search"></i>
                </button>
              </div>
            </div>
            <div class="pexels-card-body">
              <div class="dash-day-select-wrap pexels-day-select-wrap">
                <label><i class="fi fi-rr-calendar"></i> Target Day:</label>
                <select class="selectJS pexels-day-select" data-idx="${dataIndex}">
                  <option value="0">-- Unassigned --</option>`;
        for (let d = 1; d <= self.totalDays; d++) {
          const sel = item.day === d ? "selected" : "";
          html += `<option value="${d}" ${sel}>Day ${d}</option>`;
        }
        html += `        </select>
              </div>
              <button type="button" class="btn-pexels-add-card btn-add-single-pexels" data-idx="${dataIndex}" data-tooltip="Add this photo to package gallery">
                <i class="fi fi-rr-plus"></i> Add to Gallery
              </button>
            </div>
          </div>
        `;
      });

      if (isAppend) {
        $("#pexelsGrid").append(html);
      } else {
        $("#pexelsGrid").html(html);
      }

      if ($.fn.select2) {
        $("#pexelsGrid").find(".pexels-day-select").select2({
          minimumResultsForSearch: Infinity,
          width: "100%",
        });
      }
    },

    collectPreviewImages: function () {
      this.previewImages = [];
      const self = this;
      $(".dash-gallery-card:visible").each(function () {
        const $btn = $(this).find(".btn-preview-img");
        self.previewImages.push({
          src: $btn.data("image"),
          name: $btn.data("name"),
          day: parseInt($(this).data("day")) || 0,
        });
      });
    },

    showLightboxModal: function () {
      if (this.previewImages.length === 0) return;
      const item = this.previewImages[this.currentLightboxIndex];
      $("#lightboxImg").attr("src", item.src);
      $("#lightboxTitle").text(
        item.name || "Image " + (this.currentLightboxIndex + 1)
      );

      if (item.day > 0) {
        $("#lightboxDayBadge")
          .removeClass("is-orange")
          .addClass("is-green")
          .html('<i class="fi fi-rr-calendar"></i> Day ' + item.day + ' Assigned');
      } else {
        $("#lightboxDayBadge")
          .removeClass("is-green")
          .addClass("is-orange")
          .html('<i class="fi fi-rr-calendar"></i> Unassigned');
      }

      $("#lightboxModal").addClass("active");
    },
  };
});
