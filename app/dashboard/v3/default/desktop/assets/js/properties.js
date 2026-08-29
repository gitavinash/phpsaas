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

  function renderPropertyRow(p) {
    const title    = escapeHTML(p.title);
    const location = [p.address, p.city, p.pincode]
      .filter(Boolean)
      .map(escapeHTML)
      .join(", ") || "-";
    const type = escapeHTML(
      (p.type || "hotel")
        .replace(/_/g, " ")
        .replace(/\b\w/g, function (l) { return l.toUpperCase(); })
    );
    const identity = p.thumbnail
      ? '<div class="property-identity has-image"><img class="property-thumbnail" src="' +
        escapeHTML(p.thumbnail) +
        '" alt="" data-fallback="' + title + '" loading="lazy"></div>'
      : '<div class="property-identity has-icon" title="' + title + '"><i class="fi fi-rr-building"></i></div>';
    const featuredBadge = p.is_featured === 1
      ? '<span class="property-featured-badge" title="Featured hotel"><i class="fi fi-rr-star"></i></span>'
      : "";

    return (
      "<tr><td>" +
      '<div class="property-identity-wrap">' +
      identity +
      '<div class="property-identity-meta">' +
      '<strong class="property-name">' + title + "</strong>" +
      featuredBadge +
      "</div>" +
      "</div>" +
      "</td><td>" +
      (p.mobile
        ? '<div><i class="fi fi-rr-phone-call"></i> ' + escapeHTML(p.mobile) + "</div>"
        : "-") +
      (p.email
        ? '<a href="mailto:' + escapeHTML(p.email) + '"><i class="fi fi-rr-envelope"></i> ' + escapeHTML(p.email) + "</a>"
        : "") +
      '</td><td><i class="fi fi-rr-marker"></i> ' +
      location +
      "</td><td>" +
      type +
      '</td><td><span class="v3-status-badge ' +
      (p.status === 1 ? "status-active" : "status-inactive") +
      '"><span class="status-pulse-dot"></span> ' +
      (p.status === 1 ? "Active" : "Inactive") +
      '</span></td><td><div class="table-actions-inline">' +
      '<a href="/dashboard/vendors/property/update/?id=' + p.encoded_id + '" class="btn-action-icon edit" title="Edit property"><i class="fi fi-rr-edit"></i></a>' +
      '<a href="#" data-type="property" data-id="' + p.id + '" data-title="' + title + '" class="btn-action-icon delete deleteButton" data-reload="true" data-redirect="false" title="Delete property"><i class="fi fi-rr-trash"></i></a>' +
      "</div></td></tr>"
    );
  }

  window.V3Properties = {
    table: null,
    root:  null,

    init: function () {
      const root = $("#propertiesListApp")[0];
      if (!root || root === this.root) return;
      this.root = root;

      /* Image-load error → fall back to building icon */
      $(document)
        .off("error.properties", ".property-thumbnail")
        .on("error.properties", ".property-thumbnail", function () {
          const $identity = $(this).closest(".property-identity");
          $identity.removeClass("has-image").addClass("has-icon");
          $(this).replaceWith($('<i class="fi fi-rr-building"></i>'));
        });

      /* Initialise Select2 widgets */
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
            data: function (params) { return { search: params.term || "" }; },
            processResults: function (response) {
              return {
                results: (response.results || []).map(function (item) {
                  return {
                    id:   item.city || item.id,
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
            data: function (params) { return { search: params.term || "" }; },
            processResults: function (response) {
              return {
                results: (response.room_types || []).map(function (item) {
                  const id    = item.id    || item.title || item;
                  const title = item.title || item.name  || item;
                  return { id: title, text: title, value: id };
                }),
              };
            },
            cache: true,
          },
        });
      }

      /* Wire reset button: clear select2 fields then let V3DataTable handle reload */
      $(document)
        .off("click.properties", "#btnResetPropertyFilters")
        .on("click.properties", "#btnResetPropertyFilters", function () {
          $(
            "#filterPropertyType, #filterPropertyLocation, #filterPropertyRoomType, #filterPropertyStatus"
          )
            .val("")
            .trigger("change.select2");
        });

      /* Boot V3DataTable — it owns search, filter-change, pagination events.
       * Patch applyApiResponse on the prototype BEFORE instantiation so the
       * alias "properties → records" is in place for the auto-load inside init(). */
      const _origApply = V3DataTable.prototype.applyApiResponse;
      V3DataTable.prototype.applyApiResponse = function (res) {
        if (!res.records && res.properties) res.records = res.properties;
        _origApply.call(this, res);
      };

      this.table = new V3DataTable({
        apiEndpoint:  "/api/v3/properties/",
        tableSelector: "#propertiesTable",
        tbodySelector: "#propertiesTableBody",

        searchSelector:      "#propertySearchInput",
        clearSearchSelector: "#clearPropertySearchBtn",
        filterSelectors:
          "#filterPropertyType, #filterPropertyLocation, #filterPropertyRoomType, #filterPropertyStatus",
        resetFiltersSelector: "#btnResetPropertyFilters",

        paginationBarSelector: "#propertiesPaginationBar",
        perPageSelector:       "#propertiesPerPage",
        prevPageSelector:      "#propertiesPrevPage",
        nextPageSelector:      "#propertiesNextPage",
        pagesContainerSelector:"#propertiesPaginationPages",
        infoSelector:          "#propertiesPaginationInfo",

        defaultPageSize: 50,
        entityName:      "properties",

        getParams: function () {
          return {
            property_type: $("#filterPropertyType").val()     || "",
            location:      $("#filterPropertyLocation").val() || "",
            room_type:     $("#filterPropertyRoomType").val() || "",
            status:        $("#filterPropertyStatus").val()   || "",
          };
        },

        renderRow: function (p) {
          return renderPropertyRow(p);
        },

        /* Map API response key "properties" → items array for V3DataTable */
        onDataLoaded: function (res) {
          const m = res.metrics || {};
          $("#statTotalProperties").text(m.total    ?? "-");
          $("#statActiveProperties").text(m.active  ?? "-");
          $("#statPropertyRooms").text(m.rooms       ?? "-");
          $("#statPropertyLocations").text(m.locations ?? "-");
        },

        renderEmpty: function (hasFilters) {
          return '<tr><td colspan="6" class="table-loading-state">' +
            (hasFilters ? "No properties match your filters." : "No properties found.") +
            "</td></tr>";
        },
      });
    },
  };

  $(function () {
    window.V3Properties.init();
  });
});
