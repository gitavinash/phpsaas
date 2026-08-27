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
  window.V3RoomTypes = {
    initializedRoot: null,
    timer: null,
    data: [],
    init: function () {
      const root = $("#roomTypesListApp")[0];
      if (!root || root === this.initializedRoot) return;
      this.initializedRoot = root;
      $(".roomTypeBox, .editRoomTypeBox").hide();
      this.bindEvents();
      this.load();
    },
    bindEvents: function () {
      const self = this;
      $(document)
        .off("click.roomTypes", ".addNewRoomType")
        .on("click.roomTypes", ".addNewRoomType", function (event) {
          event.preventDefault();
          $(".popUp").hide();
          $(".roomTypeBox form")[0].reset();
          $(".roomTypeBox").css("display", "flex");
        });
      $(document)
        .off("input.roomTypes", "#roomTypeSearchInput")
        .on("input.roomTypes", "#roomTypeSearchInput", function () {
          $("#clearRoomTypeSearchBtn").toggle(!!$(this).val());
          clearTimeout(self.timer);
          self.timer = setTimeout(function () {
            self.load();
          }, 250);
        });
      $(document)
        .off("click.roomTypes", "#clearRoomTypeSearchBtn")
        .on("click.roomTypes", "#clearRoomTypeSearchBtn", function () {
          $("#roomTypeSearchInput").val("");
          $(this).hide();
          self.load();
        });
      $(document)
        .off("click.roomTypes", "#btnResetRoomTypeFilters")
        .on("click.roomTypes", "#btnResetRoomTypeFilters", function () {
          $("#roomTypeSearchInput").val("");
          $("#clearRoomTypeSearchBtn").hide();
          self.load();
        });
      $(document)
        .off("click.roomTypes", ".v3-room-type-card")
        .on("click.roomTypes", ".v3-room-type-card", function () {
          const item = $(this).data();
          $(".popUp").hide();
          window.updateRoomTypeId = item.id;
          $("#editName").val(item.name);
          $("#editDescription").val(item.description);
          $(".editRoomTypeBox").css("display", "flex");
        });
    },
    load: function () {
      const self = this;
      $.getJSON(
        "/api/v3/room-types/",
        { search: $("#roomTypeSearchInput").val() || "" },
        function (response) {
          self.data = response.room_types || [];
          const metrics = response.metrics || {};
          $("#statTotalRoomTypes").text(metrics.total ?? "-");
          $("#statRoomTypesWithDescriptions").text(metrics.descriptions ?? "-");
          $("#statRoomTypeCategories").text(metrics.categories ?? "-");
          self.render();
        },
      ).fail(function () {
        $("#roomTypesGrid").html(
          '<div class="room-types-loading-state">Unable to load room types. Please try again.</div>',
        );
      });
    },
    render: function () {
      const self = this;
      if (!this.data.length) {
        $("#roomTypesGrid").html(
          '<div class="room-types-loading-state">No room types match your search.</div>',
        );
        return;
      }
      $("#roomTypesGrid").html(
        this.data
          .map(function (item) {
            const title = escapeHTML(item.title),
              description = escapeHTML(
                item.description || "No description provided",
              );
            const media = item.image
              ? '<img class="room-type-card-image" src="' +
                escapeHTML(item.image) +
                '" alt="" loading="lazy">'
              : '<i class="fi fi-rr-bed-empty room-type-card-fallback"></i>';
            return (
              '<button type="button" class="v3-room-type-card" data-id="' +
              item.id +
              '" data-name="' +
              title +
              '" data-description="' +
              description +
              '"><span class="room-type-card-media">' +
              media +
              '</span><span class="room-type-card-content"><strong>' +
              title +
              "</strong><span>" +
              description +
              '</span></span><i class="fi fi-rr-edit room-type-card-edit"></i></button>'
            );
          })
          .join(""),
      );
    },
  };
  $(function () {
    window.V3RoomTypes.init();
  });
});
