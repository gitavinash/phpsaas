(function (factory) {
  if (window.jQuery) factory(window.jQuery);
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
  window.V3Properties = {
    data: [],
    timer: null,
    root: null,
    init: function () {
      const root = $("#propertiesListApp")[0];
      if (!root || root === this.root) return;
      this.root = root;
      this.bindEvents();
      this.load();
    },
    bindEvents: function () {
      const self = this;
      $(document)
        .off("error.properties", ".property-thumbnail")
        .on("error.properties", ".property-thumbnail", function () {
          const $identity = $(this).closest(".property-identity");
          $identity.removeClass("has-image").addClass("has-text");
          $(this).replaceWith($("<strong>").text($(this).data("fallback")));
        });
      $(document)
        .off("input.properties", "#propertySearchInput")
        .on("input.properties", "#propertySearchInput", function () {
          $("#clearPropertySearchBtn").toggle(!!$(this).val());
          clearTimeout(self.timer);
          self.timer = setTimeout(function () {
            self.load();
          }, 250);
        });
      $(document)
        .off("click.properties", "#clearPropertySearchBtn")
        .on("click.properties", "#clearPropertySearchBtn", function () {
          $("#propertySearchInput").val("");
          $(this).hide();
          self.load();
        });
      $(document)
        .off("change.properties", "#filterPropertyStatus")
        .on("change.properties", "#filterPropertyStatus", function () {
          self.load();
        });
      $(document)
        .off(
          "change.properties",
          "#filterPropertyType, #filterPropertyLocation, #filterPropertyRoomType",
        )
        .on(
          "change.properties",
          "#filterPropertyType, #filterPropertyLocation, #filterPropertyRoomType",
          function () {
            self.load();
          },
        );
      $("#filterPropertyType, #filterPropertyStatus").each(function () {
        if ($.fn.select2 && !$(this).hasClass("select2-hidden-accessible"))
          $(this).select2({
            width: "180px",
            dropdownAutoWidth: true,
            minimumResultsForSearch: 8,
            placeholder: $(this).data("placeholder") || "Select option",
            allowClear: true,
          });
      });
      if ($.fn.select2) {
        $("#filterPropertyLocation").select2({
          width: "180px",
          dropdownAutoWidth: true,
          allowClear: true,
          placeholder: "All Locations",
          ajax: {
            url: "/api/v3/cities/",
            dataType: "json",
            delay: 200,
            data: function (params) {
              return { search: params.term || "" };
            },
            processResults: function (response) {
              return {
                results: (response.results || []).map(function (item) {
                  return {
                    id: item.city || item.id,
                    text: item.city + (item.state ? ", " + item.state : ""),
                  };
                }),
              };
            },
            cache: true,
          },
        });
        $("#filterPropertyRoomType").select2({
          width: "180px",
          dropdownAutoWidth: true,
          allowClear: true,
          placeholder: "All Room Types",
          ajax: {
            url: "/api/v3/room-types/",
            dataType: "json",
            delay: 200,
            data: function (params) {
              return { search: params.term || "" };
            },
            processResults: function (response) {
              return {
                results: (response.room_types || []).map(function (item) {
                  const roomTypeId = item.id || item.title || item;
                  const roomTypeTitle = item.title || item.name || item;
                  return {
                    id: roomTypeTitle,
                    text: roomTypeTitle,
                    value: roomTypeId,
                  };
                }),
              };
            },
            cache: true,
          },
        });
      }
      $(document)
        .off("click.properties", "#btnResetPropertyFilters")
        .on("click.properties", "#btnResetPropertyFilters", function () {
          $("#propertySearchInput").val("");
          $(
            "#filterPropertyType, #filterPropertyLocation, #filterPropertyRoomType, #filterPropertyStatus",
          )
            .val("")
            .trigger("change.select2");
          $("#clearPropertySearchBtn").hide();
          self.load();
        });
    },
    load: function () {
      const self = this;
      $("#propertiesTableBody").html(
        '<tr><td colspan="6" class="table-loading-state"><div class="table-loading-spinner"></div><span>Loading properties directory...</span></td></tr>',
      );
      $.getJSON(
        "/api/v3/properties/",
        {
          search: $("#propertySearchInput").val() || "",
          status: $("#filterPropertyStatus").val() || "",
          property_type: $("#filterPropertyType").val() || "",
          location: $("#filterPropertyLocation").val() || "",
          room_type: $("#filterPropertyRoomType").val() || "",
        },
        function (response) {
          self.data = response.properties || [];
          const m = response.metrics || {};
          $("#statTotalProperties").text(m.total ?? "-");
          $("#statActiveProperties").text(m.active ?? "-");
          $("#statPropertyRooms").text(m.rooms ?? "-");
          $("#statPropertyLocations").text(m.locations ?? "-");
          self.render();
        },
      ).fail(function () {
        $("#propertiesTableBody").html(
          '<tr><td colspan="6" class="table-loading-state">Unable to load properties. Please try again.</td></tr>',
        );
      });
    },
    render: function () {
      if (!this.data.length) {
        $("#propertiesTableBody").html(
          '<tr><td colspan="6" class="table-loading-state">No properties match your filters.</td></tr>',
        );
        return;
      }
      $("#propertiesTableBody").html(
        this.data
          .map(function (p) {
            const title = escapeHTML(p.title),
              location =
                [p.address, p.city, p.pincode]
                  .filter(Boolean)
                  .map(escapeHTML)
                  .join(", ") || "-";
            const type = escapeHTML(
              (p.type || "hotel")
                .replace(/_/g, " ")
                .replace(/\b\w/g, function (l) {
                  return l.toUpperCase();
                }),
            );
            const identity = p.thumbnail
              ? '<div class="property-identity has-image"><img class="property-thumbnail" src="' +
                escapeHTML(p.thumbnail) +
                '" alt="" data-fallback="' +
                title +
                '" loading="lazy"></div>'
              : '<div class="property-identity has-text"><strong>' +
                title +
                "</strong></div>";
            const featuredBadge =
              p.is_featured === 1
                ? '<span class="property-featured-badge" title="Featured hotel"><i class="fi fi-rr-star"></i></span>'
                : "";
            return (
              "<tr><td>" +
              '<div class="property-identity-wrap">' +
              identity +
              featuredBadge +
              "</div>" +
              "</td><td>" +
              (p.mobile
                ? '<div><i class="fi fi-rr-phone-call"></i> ' +
                  escapeHTML(p.mobile) +
                  "</div>"
                : "-") +
              (p.email
                ? '<a href="mailto:' +
                  escapeHTML(p.email) +
                  '"><i class="fi fi-rr-envelope"></i> ' +
                  escapeHTML(p.email) +
                  "</a>"
                : "") +
              '</td><td><i class="fi fi-rr-marker"></i> ' +
              location +
              "</td><td>" +
              type +
              '</td><td><span class="v3-status-badge ' +
              (p.status === 1 ? "status-active" : "status-inactive") +
              '"><span class="status-pulse-dot"></span> ' +
              (p.status === 1 ? "Active" : "Inactive") +
              '</span></td><td><div class="table-actions-inline"><a href="/dashboard/vendors/property/update/?id=' +
              p.encoded_id +
              '" class="btn-action-icon edit" title="Edit property"><i class="fi fi-rr-edit"></i></a><a href="/dashboard/vendors/property/update/?updateType=rooms&id=' +
              p.encoded_id +
              '" class="btn-action-icon transport" title="Manage rooms"><i class="fi fi-rr-bed-empty"></i></a><a href="#" data-type="property" data-id="' +
              p.id +
              '" data-title="' +
              title +
              '" class="btn-action-icon delete deleteButton" data-reload="true" data-redirect="false" title="Delete property"><i class="fi fi-rr-trash"></i></a></div></td></tr>'
            );
          })
          .join(""),
      );
    },
  };
  $(function () {
    window.V3Properties.init();
  });
});
