$(document).ready(function (e) {
  if ($(".selectJS").length) {
    function formatCountry(country) {
      if (!country.id) {
        return country.text;
      }
      var flagUrl = $(country.element).data("flag");
      var $country = $(
        `<div style="display: flex; align-items: center; padding: 5px;">
          <img src="${flagUrl}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;" />
          <span>${country.text}</span>
        </div>`
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

  if ($(".leftToRightSlider").length) {
    $(".leftToRightSlider").slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      autoplay: true,
      arrow: false,
      dots: false,
      infinite: true,
      autoplaySpeed: 2000,
      centerMode: false, // Disable center mode to prevent overlap
      variableWidth: true, // Allow variable width for items
      responsive: [
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2, // Adjust number of slides for smaller screens
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1, // Show one slide for very small screens
          },
        },
      ],
    });
  }
});
