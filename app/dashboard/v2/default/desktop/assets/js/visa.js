$(document).ready(function () {
  // Add new country

  if ($("#addNewCountry").length > 0) {
    $("#addNewCountry").on("click", function (e) {
      e.preventDefault();

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

      if (!countryName) {
        popUpMessage("center", "Please enter the country name.", "error");
        $("#countryName").focus();
        return;
      }

      if (!countryCode) {
        popUpMessage("center", "Please enter the country code.", "error");
        $("#countryCode").focus();
        return;
      }

      if (!documentRequired) {
        popUpMessage(
          "center",
          "Please provide the required documents.",
          "error"
        );
        $("#documentRequired").focus();
        return;
      }

      if (!importantInformation) {
        popUpMessage("center", "Please enter important information.", "error");
        $("#importantInformation").focus();
        return;
      }

      if (!faqs) {
        popUpMessage("center", "Please provide FAQs.", "error");
        $("#faqs").focus();
        return;
      }

      if (visaTypes.length === 0) {
        popUpMessage(
          "center",
          "Please select at least one visa type.",
          "error"
        );
        return;
      }

      // Check image file
      var fileInput = document.getElementById("fileInput");
      var selectedFiles = fileInput.files;

      if (selectedFiles.length === 0) {
        popUpMessage(
          "center",
          "Please upload a country feature image.",
          "error"
        );
        fileInput.click();
        return;
      }

      // All validations passed
      $("#addNewCountry").hide();

      // Prepare form data using `selectedFiles`
      var formData = new FormData();
      formData.append("name", countryName);
      formData.append("code", countryCode);
      formData.append("requirements", documentRequired);
      formData.append("information", importantInformation);
      formData.append("faqs", faqs);
      formData.append("visaTypes", JSON.stringify(visaTypes));
      formData.append("isFeatured", isFeaturedValue);
      formData.append("featureImage", selectedFiles[0]); // Append image

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

  // Edit country

  if ($("#editCountry").length > 0) {
    $("#editCountry").on("click", function (e) {
      e.preventDefault();

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

      if (!countryName) {
        popUpMessage("center", "Please enter the country name.", "error");
        $("#countryName").focus();
        return;
      }

      if (!countryCode) {
        popUpMessage("center", "Please enter the country code.", "error");
        $("#countryCode").focus();
        return;
      }

      if (!documentRequired) {
        popUpMessage(
          "center",
          "Please provide document requirements.",
          "error"
        );
        $("#documentRequired").focus();
        return;
      }

      if (!importantInformation) {
        popUpMessage("center", "Please enter important information.", "error");
        $("#importantInformation").focus();
        return;
      }

      if (!faqs) {
        popUpMessage("center", "Please provide FAQs.", "error");
        $("#faqs").focus();
        return;
      }

      if (visaTypes.length === 0) {
        popUpMessage(
          "center",
          "Please select at least one visa type.",
          "error"
        );
        return;
      }

      // Check image file
      var fileInput = document.getElementById("fileInput");
      var selectedFiles = fileInput.files;

      if (selectedFiles.length === 0) {
        popUpMessage(
          "center",
          "Please upload a country feature image.",
          "error"
        );
        fileInput.click();
        return;
      }

      // If all validations pass
      $("#editCountry").hide();

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
      formData.append("featureImage", selectedFiles[0]); // Append image

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

  // Add new package

  if ($("#addNewPackage").length > 0) {
    $("#addNewPackage").on("click", function (e) {
      e.preventDefault();

      // Collecting field values
      var countryId = $("#country").val().trim();
      var title = $("#title").val().trim();
      var promoText = $("#promoText").val().trim();
      var appointment = $("#appointment").val().trim();
      var visaType = $("#visaType").val().trim();
      var stayUpto = $("#stayUpto").val().trim();
      var validity = $("#validity").val().trim();
      var entryType = $("#entryType").val().trim();
      var priceMrp = $("#priceMrp").val().trim();
      var price = $("#price").val().trim();
      var planDisclaimer = $("#planDisclaimer").text().trim();
      var visaProcessingTime = $("#visaProcessingTime").text().trim();
      var visaApplicationCenters = $("#visaApplicationCenters").text().trim();

      let isFeaturedValue = $('input[name="isFeatured"]:checked').val();

      // Validation

      if (!countryId) {
        popUpMessage("center", "Please select a country.", "error");
        $("#countryId").focus();
        return;
      }

      if (!title) {
        popUpMessage("center", "Please enter the title of package.", "error");
        $("#promoText").focus();
        return;
      }

      if (!promoText) {
        popUpMessage("center", "Please enter the promo text.", "error");
        $("#promoText").focus();
        return;
      }

      if (!appointment) {
        popUpMessage(
          "center",
          "Please specify if appointment is required.",
          "error"
        );
        $("#appointment").focus();
        return;
      }

      if (!visaType) {
        popUpMessage(
          "center",
          "Please select at least one visa type.",
          "error"
        );
        return;
      }

      if (!stayUpto) {
        popUpMessage("center", "Please enter the stay duration.", "error");
        $("#stayUpto").focus();
        return;
      }

      if (!validity) {
        popUpMessage(
          "center",
          "Please enter the visa validity period.",
          "error"
        );
        $("#validity").focus();
        return;
      }

      if (!entryType) {
        popUpMessage("center", "Please select entry type.", "error");
        $("#entryType").focus();
        return;
      }

      if (!priceMrp) {
        popUpMessage("center", "Please enter MRP price.", "error");
        $("#priceMrp").focus();
        return;
      }

      if (!price) {
        popUpMessage("center", "Please enter offer price.", "error");
        $("#price").focus();
        return;
      }

      if (!planDisclaimer) {
        popUpMessage("center", "Please add a disclaimer.", "error");
        $("#planDisclaimer").focus();
        return;
      }

      if (!visaProcessingTime) {
        popUpMessage("center", "Please enter visa processing time.", "error");
        $("#visaProcessingTime").focus();
        return;
      }

      if (!visaApplicationCenters) {
        popUpMessage(
          "center",
          "Please enter visa application center(s).",
          "error"
        );
        $("#visaApplicationCenters").focus();
        return;
      }

      // If all validations pass
      $("#addNewPackage").hide();

      // Prepare form data using `selectedFiles`
      var formData = new FormData();
      formData.append("countryId", countryId);
      formData.append("title", title);
      formData.append("promoText", promoText);
      formData.append("appointment", appointment);
      formData.append("visaType", visaType);
      formData.append("stayUpto", stayUpto);
      formData.append("validity", validity);
      formData.append("entryType", entryType);
      formData.append("priceMrp", priceMrp);
      formData.append("price", price);
      formData.append("planDisclaimer", planDisclaimer);
      formData.append("visaProcessingTime", visaProcessingTime);
      formData.append("visaApplicationCenters", visaApplicationCenters);
      formData.append("isFeatured", isFeaturedValue);

      // AJAX call
      $.ajax({
        type: "POST",
        url: "/api/visa_packages/publish/",
        dataType: "JSON",
        processData: false,
        contentType: false,
        data: formData,
        encrypt: "multipart/form-data",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              const packageId = btoa(successResponse.id);
              window.location.href = `/dashboard/visa_packages/update/?id=${packageId}`;
            }, 1500);
          } else {
            $("#addNewPackage").show();
          }
        },
        error: function (errorResponse) {
          $("#addNewPackage").show();
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    });
  }
  // Edit package

  if ($("#editPackage").length > 0) {
    $("#editPackage").on("click", function (e) {
      e.preventDefault();

      // Collecting field values
      var packageId = $("#packageId").val().trim();
      var countryId = $("#country").val().trim();
      var title = $("#title").val().trim();
      var promoText = $("#promoText").val().trim();
      var appointment = $("#appointment").val().trim();
      var visaType = $("#visaType").val().trim();
      var stayUpto = $("#stayUpto").val().trim();
      var validity = $("#validity").val().trim();
      var entryType = $("#entryType").val().trim();
      var priceMrp = $("#priceMrp").val().trim();
      var price = $("#price").val().trim();
      var planDisclaimer = $("#planDisclaimer").text().trim();
      var visaProcessingTime = $("#visaProcessingTime").text().trim();
      var visaApplicationCenters = $("#visaApplicationCenters").text().trim();

      let isFeaturedValue = $('input[name="isFeatured"]:checked').val();

      // Validation

      if (!packageId) {
        popUpMessage("center", "Please package id is missing.", "error");
        $("#countryId").focus();
        return;
      }

      if (!countryId) {
        popUpMessage("center", "Please select a country.", "error");
        $("#countryId").focus();
        return;
      }

      if (!title) {
        popUpMessage("center", "Please enter the title of package.", "error");
        $("#promoText").focus();
        return;
      }

      if (!promoText) {
        popUpMessage("center", "Please enter the promo text.", "error");
        $("#promoText").focus();
        return;
      }

      if (!appointment) {
        popUpMessage(
          "center",
          "Please specify if appointment is required.",
          "error"
        );
        $("#appointment").focus();
        return;
      }

      if (!visaType) {
        popUpMessage(
          "center",
          "Please select at least one visa type.",
          "error"
        );
        return;
      }

      if (!stayUpto) {
        popUpMessage("center", "Please enter the stay duration.", "error");
        $("#stayUpto").focus();
        return;
      }

      if (!validity) {
        popUpMessage(
          "center",
          "Please enter the visa validity period.",
          "error"
        );
        $("#validity").focus();
        return;
      }

      if (!entryType) {
        popUpMessage("center", "Please select entry type.", "error");
        $("#entryType").focus();
        return;
      }

      if (!priceMrp) {
        popUpMessage("center", "Please enter MRP price.", "error");
        $("#priceMrp").focus();
        return;
      }

      if (!price) {
        popUpMessage("center", "Please enter offer price.", "error");
        $("#price").focus();
        return;
      }

      if (!planDisclaimer) {
        popUpMessage("center", "Please add a disclaimer.", "error");
        $("#planDisclaimer").focus();
        return;
      }

      if (!visaProcessingTime) {
        popUpMessage("center", "Please enter visa processing time.", "error");
        $("#visaProcessingTime").focus();
        return;
      }

      if (!visaApplicationCenters) {
        popUpMessage(
          "center",
          "Please enter visa application center(s).",
          "error"
        );
        $("#visaApplicationCenters").focus();
        return;
      }

      // If all validations pass
      $("#editPackage").hide();

      // Prepare form data using `selectedFiles`
      var formData = new FormData();
      formData.append("packageId", packageId);
      formData.append("countryId", countryId);
      formData.append("title", title);
      formData.append("promoText", promoText);
      formData.append("appointment", appointment);
      formData.append("visaType", visaType);
      formData.append("stayUpto", stayUpto);
      formData.append("validity", validity);
      formData.append("entryType", entryType);
      formData.append("priceMrp", priceMrp);
      formData.append("price", price);
      formData.append("planDisclaimer", planDisclaimer);
      formData.append("visaProcessingTime", visaProcessingTime);
      formData.append("visaApplicationCenters", visaApplicationCenters);
      formData.append("isFeatured", isFeaturedValue);

      // AJAX call
      $.ajax({
        type: "POST",
        url: "/api/visa_packages/update/",
        dataType: "JSON",
        processData: false,
        contentType: false,
        data: formData,
        encrypt: "multipart/form-data",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              packageId = btoa(packageId);
              window.location.href = `/dashboard/visa_packages/update/?id=${packageId}`;
            }, 1500);
          } else {
            $("#editPackage").show();
          }
        },
        error: function (errorResponse) {
          $("#editPackage").show();
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
