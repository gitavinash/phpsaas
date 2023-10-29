$(document).ready(function () {
  $(".pt-slider").slick({
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 1500,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ],
  });

  $(".packge-slider").slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 3,
    autoplay: true,
    autoplaySpeed: 2000,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ],
  });

  try {
    const humburger = document.querySelector(".humburger");
    const mbNav = document.querySelector(".mobile-nav");
    const opacity = document.querySelector(".opacity");
    const close = document.querySelector(".close-icon");

    humburger.addEventListener("click", () => {
      mbNav.classList.add("active");
      opacity.classList.add("active");
    });

    close.addEventListener("click", () => {
      mbNav.classList.remove("active");
      opacity.classList.removec("active");
    });
  } catch (error) {}

  // Input field and results container
  var inputSearch = $("#searchBar");
  var resultsContainer = $("#searchResults");

  // API endpoint URL
  var apiEndpoint = "/api/lab_testing/lab_test/";

  // Function to fetch data from the API
  function fetchData(query) {
    $.get(apiEndpoint + "?test_name=" + query, function (data) {
      // Handle the API response data
      var results = data;

      // Clear previous results
      resultsContainer.empty();

      // Create autocomplete suggestions
      for (var i = 0; i < results.length; i++) {
        var suggestion = $("<li>").text(results[i].product_title);
        resultsContainer.append(suggestion);

        // Handle click on a suggestion
        suggestion.on("click", function () {
          // inputSearch.val($(this).text());
          window.location.href =
            "/book-now/?test=" + encodeURI($(this).text().toLowerCase());
          // resultsContainer.empty();
        });
      }
    });
  }

  // Event listener for input changes
  inputSearch.on("input", function () {
    var query = inputSearch.val();
    if (query.length >= 2) {
      // Adjust the minimum length as needed
      fetchData(query);
    } else {
      resultsContainer.empty();
    }
  });

  // Handle clicks outside the results container
  $(document).on("click", function (e) {
    if (
      !resultsContainer.is(e.target) &&
      resultsContainer.has(e.target).length === 0
    ) {
      resultsContainer.empty();
    }
  });

  $(".removeTest").on("click", function (e) {
    window.location.href = "/";
    e.preventDefault();
  });

  $("#pickupDate").datepicker({
    todayHighlight: true,
    startDate: "0",
    maxViewMode: 0,
    format: "dd MM yyyy",
    autoclose: true,
  });
});
