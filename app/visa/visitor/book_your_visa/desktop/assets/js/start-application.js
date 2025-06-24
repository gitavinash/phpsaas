$(document).ready(function () {
  $("#page-loader").show();

  function updateQueryParamInURL(key, value) {
    const url = new URL(window.location.href);
    if (value === null || value === "" || value === undefined) {
      url.searchParams.delete(key);
    } else {
      url.searchParams.set(key, value);
    }
    window.history.pushState({}, "", url);
  }

  var queryCountry = urlParams.get("country");
  var queryPurpose = urlParams.get("purpose");

  if (queryCountry && queryCountry.trim() !== "") {
    localStorage.setItem("visaApplicationCountry", queryCountry.trim());
  }

  if (queryPurpose != "") {
    localStorage.setItem("visaApplicationPurpose", queryPurpose);
  }

  var country = localStorage.getItem("visaApplicationCountry");
  var purpose = localStorage.getItem("visaApplicationPurpose");

  function transitionSections(fromSelector, toSelector) {
    return new Promise((resolve) => {
      $(fromSelector).fadeOut(200, function () {
        $(toSelector).fadeIn(300, function () {
          window.scrollTo({ top: 0, behavior: "smooth" });
          resolve();
        });
      });
    });
  }

  function showVisaTypes() {
    country = localStorage.getItem("visaApplicationCountry");
    $(".countryName").text(country.replaceAll("_", " "));
    const visaContainer = $("#visaPurposeListBox");

    if (visaContainer.length) {
      $.ajax({
        url: `/api/country/visas/?country=${country}`,
        method: "GET",
        success: function (res) {
          visaContainer.empty();

          if (Array.isArray(res.countryList)) {
            res.countryList.forEach((type) => {
              const formattedType =
                type.charAt(0).toUpperCase() + type.slice(1);
              const purposeUrl = formattedType.toLowerCase().replace(" ", "_");

              const visaCard = `
                  <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                    <a class='showVisaPackage' data-purpose='${formattedType.toLowerCase()}'>
                      <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                          <i class="ri-more-line text-gray-600 ri-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900">${formattedType}</h3>
                      </div>
                      <p class="text-gray-600 mb-4">Visa information for ${formattedType.toLowerCase()} travel purposes.</p>
                    </a>
                  </div>`;
              visaContainer.append(visaCard);
              $("#page-loader").hide();
            });
          } else {
            visaContainer.html(
              '<p class="text-gray-500">No visa information found.</p>'
            );
            $("#page-loader").hide();
          }

          $("#purposeOfVisa").hide().fadeIn(400);
        },
        error: function (error) {
          console.error("Visa type error:", error);
          popUpMessage(
            "center",
            error?.responseJSON?.msg || "Something went wrong!",
            "error"
          );
          $("#page-loader").hide();
        },
      });
      $("#purposeOfVisa").fadeIn();
    }
  }

  function showVisaPackage() {
    country = localStorage.getItem("visaApplicationCountry");
    purpose = localStorage.getItem("visaApplicationPurpose");

    const packageContainer = $("#visaPackagesListBox");
    $(".countryName").text(country.replaceAll("_", " "));

    if (packageContainer.length) {
      $.ajax({
        url: `/api/visa_packages/list/?country=${country}&purpose=${purpose}`,
        method: "GET",
        success: function (res) {
          if (Array.isArray(res.packageList) && res.packageList.length > 0) {
            res.packageList.forEach((pkg) => {
              const formattedTitle =
                pkg.visa_package_title.charAt(0).toUpperCase() +
                pkg.visa_package_title.slice(1);
              const encodedId = btoa(pkg.visa_package_id);
              const mrp = parseFloat(pkg.visa_package_mrp);
              const price = parseFloat(pkg.visa_package_price);
              const discountAmount = mrp - price;
              const discountPercent = Math.round((discountAmount / mrp) * 100);
              const packageUrl = pkg.visa_package_title
                .toLowerCase()
                .replace(/\s+/g, "_");

              const packageCard = `
                  <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                    <a href='/package/?package_id=${encodedId}&package_name=${encodeURIComponent(
                packageUrl
              )}'>
                      <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                          <i class="ri-passport-line text-gray-600 ri-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900">${formattedTitle}</h3>
                      </div>
                      <p class="text-gray-600 mb-4">Stay Duration: ${
                        pkg.visa_package_stay_duration
                      } days, Validity: ${
                pkg.visa_package_validity_duration
              } days.</p>
                      <div class="text-gray-600 mb-2">Price: <del>₹${mrp}</del></div>
                      <div class="text-green-600 font-bold text-lg">Offer Price: ₹${price}</div>
                      <div class="text-red-600 text-sm mt-1">You save ₹${discountAmount} (${discountPercent}% off)</div>
                    </a>
                  </div>`;
              packageContainer.empty();
              setTimeout(() => {
                packageContainer.append(packageCard);
              }, 500);
            });
          } else {
            setTimeout(() => {
              packageContainer.html(
                '<p class="text-gray-500">No visa packages found.</p>'
              );
            }, 500);
          }

          $("#visaPackages").hide().fadeIn(400);
        },
        error: function (error) {
          console.error("Visa package error:", error);
          setTimeout(() => {
            packageContainer.html(
              '<p class="text-gray-500">No visa packages found.</p>'
            );
          }, 500);
        },
      });
    }
  }

  // Hide all first
  $("#selectCountry, #purposeOfVisa").hide();

  if (queryCountry && !queryPurpose) {
    // Show purpose section
    showVisaTypes();
    $("#purposeOfVisa").fadeIn();
    $("#page-loader").hide();
  } else if (queryCountry && queryPurpose) {
    // Show VISA Packacge section
    showVisaPackage();
    $("#visaPackages").fadeIn();
    $("#page-loader").hide();
  } else {
    // Show country section
    $("#selectCountry").show();
    $("#page-loader").hide();
  }

  // Hide all initially

  $(document).on("click", ".showVisaTypes", function (e) {
    e.preventDefault();
    var selectedCountry = $(this).data("country");
    localStorage.setItem("visaApplicationCountry", selectedCountry);
    $("#visaPurposeListBox").text("");
    updateQueryParamInURL("country", selectedCountry);
    updateQueryParamInURL("purpose");
    transitionSections("#selectCountry", "#purposeOfVisa").then(() => {
      showVisaTypes();
    });
  });

  $(document).on("click", ".showVisaPackage", function (e) {
    e.preventDefault();
    $("#visaPackagesListBox").text("");
    var purposeValue = $(this).data("purpose");
    localStorage.setItem("visaApplicationPurpose", purposeValue);
    updateQueryParamInURL("purpose", purposeValue);
    transitionSections("#purposeOfVisa", "#visaPackages").then(() => {
      showVisaPackage();
    });
  });

  // Back button actions

  $(document).on("click", "#goToSelectCountry", function (e) {
    e.preventDefault();
    updateQueryParamInURL("country", "");
    updateQueryParamInURL("purpose", "");
    transitionSections("#purposeOfVisa", "#selectCountry");
  });

  $(document).on("click", "#goToSelectVisaTypes", function (e) {
    e.preventDefault();
    updateQueryParamInURL("purpose", "");
    $("#visaPackages").fadeOut(200);
    transitionSections("#purposeOfVisa", "#visaPackages").then(() => {
      showVisaTypes();
    });
  });
});
