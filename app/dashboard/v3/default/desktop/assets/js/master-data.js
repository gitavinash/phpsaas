/**
 * V3 Master Data Copy & Workspace Onboarding Controller
 * Path: app/dashboard/v3/default/desktop/assets/js/master-data.js
 */

(function ($) {
  "use strict";

  const MasterDataApp = {
    catalogLoaded: false,
    selectedDestinations: new Set(["all"]),
    catalogData: null,

    init: function () {
      const self = this;
      self.bindEvents();
    },

    bindEvents: function () {
      const self = this;

      // Open Modal
      $(document).on("click", ".btn-open-master-modal", function (e) {
        e.preventDefault();
        const defaultType = $(this).data("default-tab") || "all";
        self.openModal(defaultType);
      });

      // Close Modal
      $(document).on("click", "#btnCloseMasterModal, #btnCancelMasterModal", function (e) {
        e.preventDefault();
        self.closeModal();
      });

      // Close on Backdrop Click
      $(document).on("click", "#masterDataModal", function (e) {
        if ($(e.target).is("#masterDataModal")) {
          self.closeModal();
        }
      });

      // Close on Escape Key
      $(document).on("keydown", function (e) {
        if (e.key === "Escape" && $("#masterDataModal").hasClass("active")) {
          self.closeModal();
        }
      });

      // Dataset checkbox toggle
      $(document).on("change", ".master-dataset-checkbox", function () {
        const isChecked = $(this).is(":checked");
        const $label = $(this).closest(".master-dataset-item");
        if (isChecked) {
          $label.addClass("selected");
        } else {
          $label.removeClass("selected");
        }

        // Toggle destination filter visibility if tour package is unchecked
        const hasTourPkg = $("#masterDatasetTourPkg").is(":checked");
        if (hasTourPkg) {
          $("#masterDestFilterSection").slideDown(200);
        } else {
          $("#masterDestFilterSection").slideUp(200);
        }

        self.updateFooterSummary();
      });

      // Destination Pill Click
      $(document).on("click", ".master-dest-pill", function (e) {
        e.preventDefault();
        const dest = $(this).data("dest");
        if (dest === "all") {
          $(".master-dest-pill").removeClass("active");
          $(this).addClass("active");
          self.selectedDestinations.clear();
          self.selectedDestinations.add("all");
          $("#masterDestFilterHint").text("All destinations selected");
        } else {
          $('.master-dest-pill[data-dest="all"]').removeClass("active");
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            self.selectedDestinations.delete(dest);
          } else {
            $(this).addClass("active");
            self.selectedDestinations.add(dest);
          }
          if (self.selectedDestinations.size === 0) {
            $('.master-dest-pill[data-dest="all"]').addClass("active");
            self.selectedDestinations.add("all");
            $("#masterDestFilterHint").text("All destinations selected");
          } else {
            $("#masterDestFilterHint").text(self.selectedDestinations.size + " destination(s) selected");
          }
        }
      });

      // Confirm Copy Button
      $(document).on("click", "#btnExecuteMasterCopy", function (e) {
        e.preventDefault();
        self.executeCopy();
      });
    },

    openModal: function (preselectType) {
      const self = this;
      const $modal = $("#masterDataModal");
      if (!$modal.length) return;

      // Reset checkboxes if preselected
      if (preselectType && preselectType !== "all") {
        $(".master-dataset-checkbox").prop("checked", false);
        $(".master-dataset-item").removeClass("selected");

        let targetId = "";
        if (preselectType === "tour_package") targetId = "#masterDatasetTourPkg";
        if (preselectType === "hotels") targetId = "#masterDatasetHotels";
        if (preselectType === "vehicles") targetId = "#masterDatasetVehicles";
        if (preselectType === "transporter") targetId = "#masterDatasetTransporters";
        if (preselectType === "travel_destinations") targetId = "#masterDatasetDestinations";

        if (targetId && $(targetId).length) {
          $(targetId).prop("checked", true);
          $(targetId).closest(".master-dataset-item").addClass("selected");
        }
      } else {
        $(".master-dataset-checkbox").prop("checked", true);
        $(".master-dataset-item").addClass("selected");
      }

      $modal.addClass("active").attr("aria-hidden", "false");
      $("body").addClass("modal-open");

      // Load Catalog Data
      self.fetchCatalog();
    },

    closeModal: function () {
      const $modal = $("#masterDataModal");
      $modal.removeClass("active").attr("aria-hidden", "true");
      $("body").removeClass("modal-open");
    },

    fetchCatalog: function () {
      const self = this;
      $.ajax({
        url: "/api/v3/master/catalog.php",
        type: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            self.catalogData = res;
            self.catalogLoaded = true;

            // Update Master Counts Badges
            if (res.master_counts) {
              $("#badgeMasterPackages").text(res.master_counts.tour_packages + " Ready");
              $("#badgeMasterHotels").text(res.master_counts.hotels + " Ready");
              $("#badgeMasterVehicles").text(res.master_counts.vehicles + " Models");
              $("#badgeMasterTransporters").text(res.master_counts.transporters + " Vendor");
              $("#badgeMasterDestinations").text(res.master_counts.destinations + " Hubs");
            }

            // Update Current Workspace Stats
            if (res.site_counts) {
              const sc = res.site_counts;
              $("#textWorkspacePackages").text(sc.tour_packages > 0 ? sc.tour_packages + " in your workspace" : "Workspace is empty");
              $("#textWorkspaceHotels").text(sc.hotels > 0 ? sc.hotels + " in your workspace" : "Workspace is empty");
              $("#textWorkspaceTransporters").text(sc.transporters > 0 ? sc.transporters + " in your workspace" : "Workspace is empty");
            }

            // Populate Destination Pills
            if (res.destinations && res.destinations.length > 0) {
              const $container = $("#masterDestPillsContainer");
              let html = '<button type="button" class="master-dest-pill active" data-dest="all"><i class="fi fi-rr-check"></i> All Destinations</button>';
              res.destinations.slice(0, 15).forEach(function (d) {
                html += '<button type="button" class="master-dest-pill" data-dest="' + d.key + '">';
                html += d.name + ' <span class="count">(' + d.packages_count + ')</span>';
                html += "</button>";
              });
              $container.html(html);
            }

            self.updateFooterSummary();
          }
        },
        error: function () {
          console.warn("Failed to load master catalog data.");
        }
      });
    },

    updateFooterSummary: function () {
      const checkedCount = $(".master-dataset-checkbox:checked").length;
      const $btn = "#btnExecuteMasterCopy";

      if (checkedCount === 0) {
        $("#masterFooterSummary").text("Please select at least one dataset to copy.");
        $($btn).prop("disabled", true);
      } else {
        $("#masterFooterSummary").text("Ready to copy " + checkedCount + " selected dataset(s) into your dashboard.");
        $($btn).prop("disabled", false);
      }
    },

    executeCopy: function () {
      const self = this;
      const selectedTypes = [];
      $(".master-dataset-checkbox:checked").each(function () {
        selectedTypes.push($(this).val());
      });

      if (selectedTypes.length === 0) {
        if (typeof swal !== "undefined") {
          swal("Notice", "Please select at least one dataset to copy.", "info");
        } else {
          alert("Please select at least one dataset to copy.");
        }
        return;
      }

      const destArr = Array.from(self.selectedDestinations);
      const isAllDest = destArr.includes("all");

      const $btn = $("#btnExecuteMasterCopy");
      const originalText = $("#btnExecuteMasterText").text();
      $btn.prop("disabled", true);
      $("#btnExecuteMasterText").text("Copying Master Data...");

      $.ajax({
        url: "/api/v3/master/copy.php",
        type: "POST",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify({
          data_types: selectedTypes,
          destinations: isAllDest ? [] : destArr
        }),
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false);
          $("#btnExecuteMasterText").text(originalText);

          if (res && res.status === "success") {
            self.closeModal();

            const c = res.copied || {};
            let details = [];
            if (c.tour_packages > 0) details.push(c.tour_packages + " Tour Packages");
            if (c.hotels > 0) details.push(c.hotels + " Hotels (" + (c.rooms || 0) + " Rooms)");
            if (c.transporters > 0) details.push(c.transporters + " Transporters");
            if (c.vehicles > 0) details.push(c.vehicles + " Fleet Vehicles");
            if (c.travel_destinations > 0) details.push(c.travel_destinations + " Destinations");

            const detailMsg = details.length > 0 ? "Imported: " + details.join(", ") : res.msg;

            if (typeof swal !== "undefined") {
              swal({
                title: "Master Data Imported!",
                text: detailMsg,
                type: "success",
                confirmButtonText: "Awesome"
              }, function () {
                self.refreshActiveView();
              });
            } else {
              alert("Master Data Imported!\n" + detailMsg);
              self.refreshActiveView();
            }
          } else {
            const errMsg = (res && res.msg) ? res.msg : "Something went wrong while copying data.";
            if (typeof swal !== "undefined") {
              swal("Error", errMsg, "error");
            } else {
              alert(errMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false);
          $("#btnExecuteMasterText").text(originalText);
          let errText = "Server connection error occurred.";
          try {
            const json = JSON.parse(xhr.responseText);
            if (json && json.msg) errText = json.msg;
          } catch (e) {}

          if (typeof swal !== "undefined") {
            swal("Error", errText, "error");
          } else {
            alert(errText);
          }
        }
      });
    },

    refreshActiveView: function () {
      // SPA Table / View Refresh without full page reload
      if (typeof window.fetchPackagesData === "function") {
        window.fetchPackagesData();
      } else if (typeof window.loadPackages === "function") {
        window.loadPackages();
      } else if (typeof window.loadProperties === "function") {
        window.loadProperties();
      } else if (typeof window.loadTransporters === "function") {
        window.loadTransporters();
      } else if (typeof window.loadVehiclesList === "function") {
        window.loadVehiclesList();
      } else if (window.location && typeof window.location.reload === "function") {
        window.location.reload();
      }
    }
  };

  $(document).ready(function () {
    MasterDataApp.init();
  });

  window.MasterDataApp = MasterDataApp;
})(jQuery);
