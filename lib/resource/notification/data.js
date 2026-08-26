// Update site analytics records

$(document).ready(function (e) {
  var longitude = "";
  var latitude = "";
  var apiDomain = typeof domain !== "undefined" && domain ? domain : "";

  $.ajax({
    url: apiDomain + "/api/analytics/record/",
    type: "POST",
    dataType: "json",
    data: {
      url: window.location.pathname + window.location.search,
      type: "analytics",
      action: "record",
      longitude: longitude,
      latitude: latitude,
    },
    error: function (data) {
      // ignore analytics fail
    },
  });
});
