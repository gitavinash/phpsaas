$(document).ready(function (e) {
  if ($(".selectJS").length) {
    function formatCountry(country) {
      if (!country.id) {
        return country.text;
      }
      var flagUrl = $(country.element).data("flag");
      var $country = $(
        '<span><img src="' +
          flagUrl +
          '" class="flag-img" style="width: 30px; height: 30px; margin-right: 10px; border-radius: 100%" />' +
          country.text +
          "</span>"
      );
      return $country;
    }

    $(".selectJS").select2({
      placeholder: "Search for country",
      templateResult: formatCountry,
      tag: true,
      //   allowClear: true,
      templateSelection: formatCountry,
      width: "100%", // Optional: Adjust width
    });
  }
});
