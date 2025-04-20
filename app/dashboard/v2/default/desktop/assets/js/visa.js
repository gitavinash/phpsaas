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

      // Check if any image was added via drag-and-drop
      if (dragAndDropFileList.length === 0) {
        popUpMessage(
          "center",
          "Please upload a country feature image.",
          "error"
        );
        $("#addNewCountry").show();
        return;
      }

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

      // Dynamically create file inputs for drag-and-drop files
      dragAndDropFileList.forEach(function (fileName) {
        // Create a hidden file input
        var fileInput = $("<input>", {
          type: "file",
          name: "featureImage[]",
          style: "display:none",
          "data-name": fileName,
        }).appendTo("body"); // Append it to the body

        // Create a File object for the dropped file
        var file = new File([fileName], fileName, { type: "image/png" });

        // Create a FileList object to append the file
        var fileList = new DataTransfer();
        fileList.items.add(file);
        fileInput[0].files = fileList.files; // Set the file input's files property

        // Append the file to the formData
        formData.append("featureImage", fileInput[0].files[0]); // Append file to FormData

        console.log(`fileInput`, fileInput);
        console.log(`fileInput[0]`, fileInput[0]);
        console.log(`fileInput[0]`, fileInput[0].files[0]);

        // Clean up by removing the dynamic file input after it's used
        fileInput.remove();
      });

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
      var countryId = $("#countryId").val().trim();
      var oldGallery = $("#oldGallery").val().trim();

      // Collect selected visa types
      var visaTypes = [];
      $("input[name='visaTypes']:checked").each(function () {
        visaTypes.push($(this).val());
      });

      // Check if any image was added via drag-and-drop
      // if (dragAndDropFileList.length === 0) {
      //   popUpMessage(
      //     "center",
      //     "Please upload a country feature image.",
      //     "error"
      //   );
      //   $("#addNewCountry").show();
      //   return;
      // }

      // Validation
      if (
        !countryId ||
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
      formData.append("id", countryId);
      formData.append("name", countryName);
      formData.append("code", countryCode);
      formData.append("requirements", documentRequired);
      formData.append("information", importantInformation);
      formData.append("faqs", faqs);
      formData.append("visaTypes", JSON.stringify(visaTypes));

      // Dynamically create file inputs for drag-and-drop files
      dragAndDropFileList.forEach(function (fileName) {
        // Create a hidden file input
        var fileInput = $("<input>", {
          type: "file",
          name: "featureImage[]",
          style: "display:none",
          "data-name": fileName,
        }).appendTo("body"); // Append it to the body

        // Create a File object for the dropped file
        var file = new File([fileName], fileName, { type: "image/png" });

        // Create a FileList object to append the file
        var fileList = new DataTransfer();
        fileList.items.add(file);
        fileInput[0].files = fileList.files; // Set the file input's files property

        // Append the file to the formData
        formData.append("featureImage", fileInput[0].files[0]); // Append file to FormData

        console.log(`fileInput`, fileInput);
        console.log(`fileInput[0]`, fileInput[0]);
        console.log(`fileInput[0]`, fileInput[0].files[0]);

        // Clean up by removing the dynamic file input after it's used
        fileInput.remove();
      });

      // AJAX call
      $.ajax({
        type: "POST",
        url: "/api/country/update/",
        dataType: "JSON",
        processData: false,
        contentType: false,
        data: formData,
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);

          if (successResponse.status == "success") {
            setTimeout(function () {
              const countryId = btoa(successResponse.id);
              window.location.href = `/dashboard/country/update/?id=${countryId}`;
            }, 1500);
          } else {
            $("#editCountry").show();
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
