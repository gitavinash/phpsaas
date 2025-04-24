$(document).ready(function () {
  if ($("#addNewCountry").length > 0) {
    $("#addNewCountry").on("click", function (e) {
      e.preventDefault();
      $("#addNewCountry").hide();

      // Collecting field values
      var countryName = $("#name").val().trim();
      var countryCode = $("#code").val().trim();
      var documentRequired = $("#requirements").text().trim();
      var importantInformation = $("#information").text().trim();
      var faqs = $("#faq").text().trim();

      // Collect selected visa types
      var visaTypes = [];
      $("input[name='visaTypes']:checked").each(function () {
        visaTypes.push($(this).val());
      });

      let isFeaturedValue = $('input[name="isFeatured"]:checked').val();

      // Validation
      if (
        !countryName ||
        !countryCode ||
        !documentRequired ||
        !importantInformation ||
        !faqs ||
        visaTypes.length === 0
      ) {
        popUpMessage(
          "center",
          "Please fill out all required fields and select at least one visa type.",
          "error"
        );
        $("#addNewCountry").show();
        return;
      }

      // Prepare form data using `selectedFiles`
      var formData = new FormData();
      formData.append("name", countryName);
      formData.append("code", countryCode);
      formData.append("requirements", documentRequired);
      formData.append("information", importantInformation);
      formData.append("faqs", faqs);
      formData.append("visaTypes", JSON.stringify(visaTypes));
      formData.append("isFeatured", isFeaturedValue);

      // AJAX call
      $.ajax({
        type: "POST",
        url: "/api/country/publish/",
        dataType: "JSON",
        processData: false,
        contentType: false,
        data: formData,
        encrypt: "multipart/form-data",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);

          if (successResponse.status == "success") {
            setTimeout(function () {
              const countryId = btoa(successResponse.id);
              window.location.href = `/dashboard/country/update/?id=${countryId}`;
            }, 1500);
          } else {
            $("#addNewCountry").show();
          }
        },
        error: function (errorResponse) {
          $("#addNewCountry").show();
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    });
  }

  // edit-country-form.js
  if ($("#editCountry").length > 0) {
    $("#editCountry").on("click", function (e) {
      e.preventDefault();
      $("#editCountry").hide();

      // Collecting field values
      var countryName = $("#name").val().trim();
      var countryCode = $("#code").val().trim();
      var documentRequired = $("#requirements").text().trim();
      var importantInformation = $("#information").text().trim();
      var faqs = $("#faq").text().trim();

      // Collect selected visa types
      var visaTypes = [];
      $("input[name='visaTypes']:checked").each(function () {
        visaTypes.push($(this).val());
      });

      let isFeaturedValue = $('input[name="isFeatured"]:checked').val();

      // Validation
      if (
        !countryName ||
        !countryCode ||
        !documentRequired ||
        !importantInformation ||
        !faqs ||
        visaTypes.length === 0
      ) {
        popUpMessage(
          "center",
          "Please fill out all required fields and select at least one visa type.",
          "error"
        );
        $("#editCountry").show();
        return;
      }

      // Prepare form data using `selectedFiles`
      var formData = new FormData();
      formData.append("name", countryName);
      formData.append("code", countryCode);
      formData.append("requirements", documentRequired);
      formData.append("information", importantInformation);
      formData.append("faqs", faqs);
      formData.append("visaTypes", JSON.stringify(visaTypes));
      formData.append("isFeatured", isFeaturedValue);
      formData.append("id", $("#countryId").val().trim());

      // AJAX call
      $.ajax({
        type: "POST",
        url: "/api/country/update/",
        dataType: "JSON",
        processData: false,
        contentType: false,
        data: formData,
        encrypt: "multipart/form-data",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            popUpMessage("center", successResponse.msg, successResponse.status);
            setTimeout(function () {
              location.reload();
              $("#editCountry").show();
            }, 1500);
          } else {
            popUpMessage("center", successResponse.msg, successResponse.status);
          }
        },
        error: function (errorResponse) {
          $("#editCountry").show();
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    });
  }
});
