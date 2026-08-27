(function (factory) {
  if (typeof window.jQuery !== "undefined") factory(window.jQuery);
  else
    document.addEventListener("DOMContentLoaded", function () {
      if (window.jQuery) factory(window.jQuery);
    });
})(function ($) {
  "use strict";

  const escapeHTML = function (value) {
    return $("<div>")
      .text(value == null ? "" : value)
      .html();
  };
  const icons = {
    basic_facilities: "fi-rr-home",
    beauty_and_spa: "fi-rr-spa",
    food_and_drinks: "fi-rr-utensils",
    swimming_pool: "fi-rr-swimming-pool",
    parking: "fi-rr-parking",
    safety_andsecurity: "fi-rr-shield-check",
  };
  const setImagePreview = function (file, previewId, imageId) {
    const $preview = $(previewId),
      $image = $(imageId);
    if (!file || !file.type || file.type.indexOf("image/") !== 0) {
      $preview.addClass("is-visible").find("img").attr("src", "").hide();
      $preview.find(".amenity-preview-placeholder").show();
      return;
    }
    const reader = new FileReader();
    reader.onload = function (event) {
      $image.attr("src", event.target.result);
      $image.show();
      $preview.find(".amenity-preview-placeholder").hide();
      $preview.addClass("is-visible");
    };
    reader.readAsDataURL(file);
  };

  window.V3Amenities = {
    data: [],
    timer: null,
    initializedRoot: null,
    init: function () {
      const root = $("#amenitiesListApp")[0];
      if (!root || this.initializedRoot === root) return;
      this.initializedRoot = root;
      $(".amenitiesBox, .editAmenitiesBox").hide();
      this.bindEvents();
      this.load();
    },
    bindEvents: function () {
      const self = this;
      $(document)
        .off("click.amenities", ".addNewAmenities")
        .on("click.amenities", ".addNewAmenities", function (event) {
          event.preventDefault();
          $(".popUp").hide();
          $("#addAmenitiesForm")[0].reset();
          $("#addAmenityPreview").addClass("is-visible");
          $("#addAmenityPreviewImage").attr("src", "").hide();
          $("#addAmenityPreview .amenity-preview-placeholder").show();
          $("#type").val("basic_facilities").trigger("change");
          $(".amenitiesBox").css("display", "flex");
        });
      $(document)
        .off("change.amenities", "#icon, #editIcon")
        .on("change.amenities", "#icon, #editIcon", function () {
          const isEdit = this.id === "editIcon";
          setImagePreview(
            this.files && this.files[0],
            isEdit ? "#editAmenityPreview" : "#addAmenityPreview",
            isEdit ? "#editAmenityPreviewImage" : "#addAmenityPreviewImage",
          );
        });
      $(document)
        .off("click.amenities", ".amenity-remove-button")
        .on("click.amenities", ".amenity-remove-button", function () {
          const inputId = $(this).data("input");
          const previewId = $(this).data("preview");
          $("#" + inputId).val("");
          $("#" + previewId + " img")
            .attr("src", "")
            .hide();
          $("#" + previewId + " .amenity-preview-placeholder").show();
          $("#" + previewId).addClass("is-visible");
        });
      $(document)
        .off("input.amenities", "#amenitySearchInput")
        .on("input.amenities", "#amenitySearchInput", function () {
          $("#clearAmenitySearchBtn").toggle(!!$(this).val());
          clearTimeout(self.timer);
          self.timer = setTimeout(function () {
            self.load();
          }, 250);
        });
      $(document)
        .off("click.amenities", "#clearAmenitySearchBtn")
        .on("click.amenities", "#clearAmenitySearchBtn", function () {
          $("#amenitySearchInput").val("");
          $(this).hide();
          self.load();
        });
      $(document)
        .off("change.amenities", "#amenityCategoryFilter")
        .on("change.amenities", "#amenityCategoryFilter", function () {
          self.load();
        });
      if (
        $.fn.select2 &&
        !$("#amenityCategoryFilter").hasClass("select2-hidden-accessible")
      ) {
        $("#amenityCategoryFilter").select2({
          width: "220px",
          dropdownAutoWidth: true,
          minimumResultsForSearch: 8,
        });
      }
      $(document)
        .off("click.amenities", "#btnResetAmenityFilters")
        .on("click.amenities", "#btnResetAmenityFilters", function () {
          $("#amenitySearchInput").val("");
          $("#clearAmenitySearchBtn").hide();
          $("#amenityCategoryFilter").val("");
          self.load();
        });
      $(document)
        .off("click.amenities", ".v3-amenity-card")
        .on("click.amenities", ".v3-amenity-card", function () {
          const item = $(this).data();
          $(".popUp").hide();
          window.updateAmenitiesId = item.id;
          $("#editName").val(item.name);
          $("#editDescription").val(item.description);
          $("#editType").val(item.type).trigger("change");
          $("#amenitiesIcon").attr("src", item.image || "");
          $("#editAmenityPreviewImage").attr("src", item.image || "");
          $("#editAmenityPreviewImage").toggle(!!item.image);
          $("#editAmenityPreview .amenity-preview-placeholder").toggle(
            !item.image,
          );
          $("#editAmenityPreview").addClass("is-visible");
          $(".editAmenitiesBox").css("display", "flex");
        });
      $(document)
        .off("error.amenities", ".amenity-card-image")
        .on("error.amenities", ".amenity-card-image", function () {
          $(this).replaceWith(
            '<i class="fi fi-rr-star amenity-card-fallback"></i>',
          );
        });
    },
    load: function () {
      const self = this;
      $.getJSON(
        "/api/v3/amenities/",
        {
          search: $("#amenitySearchInput").val() || "",
          category: $("#amenityCategoryFilter").val() || "",
        },
        function (response) {
          self.data = response.amenities || [];
          const metrics = response.metrics || {};
          $("#statTotalAmenities").text(metrics.total ?? "-");
          $("#statAmenityCategories").text(metrics.categories ?? "-");
          $("#statAmenitiesWithIcons").text(metrics.with_icons ?? "-");
          $("#statTopAmenityCategory").text(metrics.top_category || "-");
          const $filter = $("#amenityCategoryFilter"),
            selected = $filter.val();
          $filter.find("option:not(:first)").remove();
          (response.categories || []).forEach(function (value) {
            $filter.append(
              $("<option>")
                .val(value)
                .text(
                  value.replace(/_/g, " ").replace(/\b\w/g, function (letter) {
                    return letter.toUpperCase();
                  }),
                ),
            );
          });
          $filter.val(selected);
          if ($filter.hasClass("select2-hidden-accessible")) {
            $filter.trigger("change.select2");
          }
          self.render();
        },
      ).fail(function () {
        $("#amenitiesGrid").html(
          '<div class="amenities-loading-state">Unable to load amenities. Please try again.</div>',
        );
      });
    },
    render: function () {
      const self = this;
      if (!this.data.length) {
        $("#amenitiesGrid").html(
          '<div class="amenities-loading-state">No amenities match your filters.</div>',
        );
        return;
      }
      $("#amenitiesGrid").html(
        this.data
          .map(function (item) {
            const title = escapeHTML(item.title),
              description = escapeHTML(
                item.description || "No description provided",
              );
            const media = item.thumbnail
              ? '<img class="amenity-card-image" src="' +
                escapeHTML(item.thumbnail) +
                '" alt="" loading="lazy">'
              : '<i class="fi ' +
                (icons[item.type] || "fi-rr-star") +
                ' amenity-card-fallback"></i>';
            return (
              '<button type="button" class="v3-amenity-card" data-id="' +
              item.id +
              '" data-name="' +
              title +
              '" data-description="' +
              description +
              '" data-image="' +
              escapeHTML(item.thumbnail) +
              '" data-type="' +
              escapeHTML(item.type) +
              '"><span class="amenity-card-media">' +
              media +
              '</span><span class="amenity-card-content"><strong>' +
              title +
              "</strong><small>" +
              escapeHTML(item.type_label) +
              "</small><span>" +
              description +
              '</span></span><i class="fi fi-rr-edit amenity-card-edit"></i></button>'
            );
          })
          .join(""),
      );
    },
  };

  $(function () {
    window.V3Amenities.init();
  });
});
