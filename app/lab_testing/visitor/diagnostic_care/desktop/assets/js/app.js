$(document).ready(function () {
  $(".pt-slider").slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
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
          slidesToShow: 2,
          slidesToScroll: 2,
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
  const prescription = document.querySelector(".Prescription");
  const prescriptionForm = document.querySelector(".prescription-form");
  const opacity = document.querySelector(".opacity");
  prescription.addEventListener("click", () => {
    prescriptionForm.classList.add("active");
    opacity.classList.add("active");
  });

  const closeForm = document.querySelector(".close-form");
  closeForm.addEventListener("click", () => {
    prescriptionForm.classList.remove("active");
    opacity.classList.remove("active");
  });

  opacity.addEventListener("click", () => {
    prescriptionForm.classList.remove("active");
    opacity.classList.remove("active");
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
});
