$(document).ready(function () {
  $(".brandSlider").slick(
    {
      dots: true,
      infinite: true,
      speed: 500,
      slidesToShow: 5,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 1500,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 4,
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
    },
    500
  );

  $(".packge-slider").slick(
    {
      dots: true,
      infinite: true,
      speed: 800,
      slidesToShow: 4,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 4,
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
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ],
    },
    1000
  );

  $(".tests-slider").slick(
    {
      dots: true,
      infinite: true,
      speed: 800,
      slidesToShow: 4,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 4,
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
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ],
    },
    1000
  );

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
          // $(".selectedTests").show();
          // inputSearch.val($(this).text());
          // window.location.href =
          //   "/book-now/?test=" + encodeURI($(this).text().toLowerCase());
          // $(".testsName").append("<li>" + $(this).text() + "</li>");
          showSelectedTests($(this).text());
        });
      }
    });
  }

  testLists = [];
  testsValues = [];

  function showSelectedTests(testsName) {
    resultsContainer.empty();
    inputSearch.val("");
    // $(".tags").val("");
    $(".selectTag").empty();
    if (testsName != "") {
      $(".selectedTests").show();

      var testsName = testsName
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");

      /* remove dublicate value from tags */

      var position = $.inArray(testsName, testsValues);
      if (position == -1) {
        var tag = $("<span >" + testsName + "</span>").on("click", function () {
          /* Remove tag on click */
          $(this).remove();
          $(".testsName").val(testLists);
          var remoVePosition = $.inArray(testsName, testLists);
          testLists.splice(remoVePosition, 1);
          $(".testsName").val(testLists);
        });

        /* Show inserted value to user */

        testsValues.push(testsName);

        var bookingLink = encodeURIComponent(
          testsValues.join("_").toLowerCase()
        );
        console.log(`testsValues : `, bookingLink);

        $("#selectedTestBookNow").attr(
          "href",
          "/book-now/?test=" + bookingLink
        );

        $(".testsName").append(tag);
      }
    }
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
    para = atob(getUrlParameter("test_id"));
    checkoutTypePara = getUrlParameter("checkout_type");
    var testIdArray = para.split(",");
    // window.location.href = "/";
    console.log(`para : `, testIdArray);
    var idValue = $(this).attr("id_val");
    console.log(`idValue : `, idValue);

    testIdArray.indexOf(idValue) !== -1 &&
      testIdArray.splice(testIdArray.indexOf(idValue), 1);
    var finalUrl =
      "/checkout/?test_id=" +
      btoa(testIdArray.toString()) +
      "&checkout_type=" +
      checkoutTypePara;
    window.location.href = finalUrl;
    e.preventDefault();
  });

  $("#pickupDate").datepicker({
    todayHighlight: true,
    startDate: "0",
    maxViewMode: 0,
    format: "dd MM yyyy",
    autoclose: true,
  });

  function getUrlParameter(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }
});
