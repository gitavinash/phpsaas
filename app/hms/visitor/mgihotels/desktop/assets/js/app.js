$(document).ready(function () {
  // Form Validation
  function validateForm() {
    let isValid = true;

    // Validate text inputs
    $('#kycFrom input[type="text"]').each(function () {
      if ($(this).val().trim() === "") {
        $(this).addClass("is-invalid");
        isValid = false;
      } else {
        $(this).removeClass("is-invalid");
      }
    });
    $('#kycFrom input[type="email"]').each(function () {
      if ($(this).val().trim() === "") {
        $(this).addClass("is-invalid");
        isValid = false;
      } else {
        $(this).removeClass("is-invalid");
      }
    });
    $('#kycFrom input[type="number"]').each(function () {
      if ($(this).val().trim() === "") {
        $(this).addClass("is-invalid");
        isValid = false;
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    // Validate select input
    if ($("#companyType").val() === "") {
      $("#companyType").addClass("is-invalid");
      isValid = false;
    } else {
      $("#companyType").removeClass("is-invalid");
    }

    if ($("#tourismCategory").val() === "") {
      $("#tourismCategory").addClass("is-invalid");
      isValid = false;
    } else {
      $("#tourismCategory").removeClass("is-invalid");
    }

    if ($("#state").val() === "") {
      $("#state").addClass("is-invalid");
      isValid = false;
    } else {
      $("#state").removeClass("is-invalid");
    }

    // Validate file inputs
    $('#kycFrom input[type="file"]').each(function () {
      if ($(this).val() === "") {
        $(this).addClass("is-invalid");
        isValid = false;
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    return isValid;
  }

  // Submit button click event
  $("#submitKYC").click(function (e) {
    $("#submitKYC").hide();
    e.preventDefault(); // Prevent the form from submitting
    // If validation is required before submission
    if (validateForm()) {
      // Create FormData object for AJAX request
      let formData = new FormData($("#kycFrom")[0]);

      // AJAX request
      $.ajax({
        url: "/api/onboarding/kyc/", // Replace with your server endpoint
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          // Handle success response
          window.location.href = "/kyc-request-sent/";
          $("#submitKYC").show();
        },
        error: function (xhr) {
          // Handle error response
          var responseJSON = xhr.responseJSON;

          // Check if responseJSON and responseJSON.msg are objects
          if (
            responseJSON &&
            typeof responseJSON.msg === "object" &&
            responseJSON.msg !== null
          ) {
            // Get the first key from the 'msg' object
            var firstKey = Object.keys(responseJSON.msg)[0];
            // Get the value corresponding to the first key
            var firstValue = responseJSON.msg[firstKey];
            // Log the first value to the console
            console.log(firstValue);
          } else {
            console.log(
              'Unexpected response format or missing "msg" property.'
            );
          }

          $("#submitKYC").show();
        },
      });
    } else {
      alert("Please fill in all required fields.");
      $("#submitKYC").show();
    }
  });
});
