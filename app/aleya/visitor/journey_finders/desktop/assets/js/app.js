$(document).ready(function () {
  $("#menuToggle").on("click", function () {
    $("#mobileMenu").removeClass("-translate-x-full").addClass("translate-x-0");
  });

  $("#menuClose").on("click", function () {
    $("#mobileMenu").removeClass("translate-x-0").addClass("-translate-x-full");
  });
  const $faqItems = $(".faq-item");

  if ($faqItems.length > 0) {
    $faqItems.each(function () {
      const $question = $(this).find(".faq-question");

      $question.on("click", () => {
        $(this).toggleClass("active");
      });
    });
  }

  // Quick Filter Functionality

  $(".quick-filter").on("click", function (e) {
    e.preventDefault();

    const url = new URL(window.location.href);
    const filter = $(this).data("filter");
    const value = $(this).data("value");

    // Set or update the filter and value parameters
    url.searchParams.set("filter", filter);
    url.searchParams.set("value", value);
    $('select[name="sort"]').val("#");
    // Redirect with the updated URL
    window.location.href = url.toString();
  });

  // Highlight the active filter based on current URL
  const currentUrl = new URL(window.location.href);
  const activeFilter = currentUrl.searchParams.get("filter");
  const activeValue = currentUrl.searchParams.get("value");

  $(".quick-filter").each(function () {
    const filter = $(this).data("filter");
    const value = $(this).data("value");

    if (filter == activeFilter && value == activeValue) {
      // Add active styling
      $(this)
        .removeClass("bg-gray-100 text-gray-700 hover:bg-gray-200")
        .addClass("bg-primary text-white");
    } else {
      // Reset others to default
      $(this)
        .removeClass("bg-primary text-white")
        .addClass("bg-gray-100 text-gray-700 hover:bg-gray-200");
    }
  });

  var $sortSelect = $('select[name="sort"], select');

  if ($sortSelect.length) {
    // Pre-select the dropdown if 'sort' exists in URL
    var currentSort = new URLSearchParams(window.location.search).get("sort");
    if (currentSort) {
      $sortSelect.val(currentSort);
    }

    // Bind change event
    $sortSelect.on("change", function () {
      var sortValue = $(this).val();
      var urlParams = new URLSearchParams(window.location.search);
      urlParams.set("sort", sortValue); // Set or update 'sort' param
      window.location.search = urlParams.toString(); // Reload with updated URL
    });
  }

  // Autocomplete functionality
  const $results = $("#autocomplete-results");

  if ($results.length > 0) {
    const $input = $("#destination");
    let debounceTimer;

    $input.on("input", function () {
      const query = $(this).val().trim();

      clearTimeout(debounceTimer);
      if (query.length < 2) {
        $results.empty().hide();
        return;
      }

      debounceTimer = setTimeout(() => {
        $.ajax({
          url: "/api/locations/read/",
          method: "GET",
          data: { q: query },
          success: function (data) {
            console.log("Autocomplete data received:", data);

            const suggestions = data.locations || [];
            $results.empty();

            if (suggestions.length === 0) {
              $results.append(`
        <li class="px-4 py-2 text-sm text-gray-500 italic text-center cursor-default">
          No results found
        </li>
      `);
              $results.show();
              return;
            }

            suggestions.forEach((location) => {
              $results.append(`
        <li class="flex items-center gap-2 px-4 py-2 hover:bg-primary/10 cursor-pointer text-sm text-gray-700 transition rounded"
            data-value="${location.location}">
          <i class="ri-map-pin-line text-primary text-base"></i>
          <span>${location.full_location}</span>
        </li>
      `);
            });

            $results.show();
          },
          error: function (xhr) {
            console.error("Autocomplete request failed:", xhr.responseText);
            $results
              .empty()
              .append(
                `
      <li class="px-4 py-2 text-sm text-red-500 italic cursor-default">
        Destination not found
      </li>
    `
              )
              .show();
          },
        });
      }, 300);
    });

    // Suggestion click handler
    $results.on("click", "li", function () {
      const value = $(this).data("value");
      $input.val(value);
      window.location.href = `/search/?q=${encodeURIComponent(value)}`;
      $results.empty().hide();
    });

    // Hide dropdown on outside click
    $(document).on("click", function (e) {
      if (!$(e.target).closest(".relative").length) {
        $results.empty().hide();
      }
    });
  }
});
