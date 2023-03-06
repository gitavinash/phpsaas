$(document).ready(function (e) {
  /** Datepicker */

  $(".datepicker").daterangepicker({
    singleDatePicker: true,
    autoUpdateInput: true,
    locale: {
      format: "DD/MM/YYYY",
    },
    minDate: new Date().toLocaleDateString("en-GB"),
  });

  /** Start product details accordion tabs */

  $("#productGeneral").click(function () {
    $(".productGeneral").show();
    $("#productGeneral").addClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productInventory").click(function () {
    $(".productInventory").show();
    $("#productInventory").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productShipping").click(function () {
    $(".productShipping").show();
    $("#productShipping").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productGallery").click(function () {
    $(".productGallery").show();
    $("#productGallery").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productAttributesDetails").click(function () {
    $(".productAttributesDetails").show();
    $("#productAttributesDetails").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productAdditionalDetails").click(function () {
    $(".productAdditionalDetails").show();
    $("#productAdditionalDetails").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
  });

  /** Product gallery */

  $("#gallery").on("change", function (e) {
    var files = e.target.files,
      filesLength = files.length;
    for (var i = 0; i < filesLength; i++) {
      var f = files[i];
      var fileReader = new FileReader();
      fileReader.onload = function (e) {
        var file = e.target;
        $(
          '<span class="pip">' +
            '<img class="imageThumb" src="' +
            e.target.result +
            '" title="' +
            file.name +
            '"/>' +
            '<br/><span class="remove">Remove image</span>' +
            "</span>"
        ).insertAfter("#gallery");
        $(".remove").click(function () {
          $(this).parent(".pip").remove();
        });

        // Old code here
        /*$("<img></img>", {
			  class: "imageThumb",
			  src: e.target.result,
			  title: file.name + " | Click to remove"
			}).insertAfter("#gallery").click(function(){$(this).remove();});*/
      };
      fileReader.readAsDataURL(f);
    }
  });

  /** Add new coupon */

  $("#addNewCoupon").click(function (e) {
    e.preventDefault();
    var title = $("#title").val();
    var code = $("#code").val();
    var discount = $("#discount").val();
    var maxDiscount = $("#maxDiscount").val();
    var minCartAmount = $("#minCartAmount").val();
    var limit = $("#limit").val();

    if (title != "") {
      if (code != "") {
        if (discount >= 0) {
          if (maxDiscount >= 0) {
            if (minCartAmount >= 0) {
              if (limit >= 0) {
                $("#addNewCoupon").hide();

                $.ajax({
                  type: "POST",
                  dataType: "json",
                  data: $("#formData").serialize(),
                  success: function (data) {
                    popUpMessage("center", data.msg, data.status);
                    if (data.status == 200) {
                      window.setTimeout(function () {
                        window.location.href = data.url;
                      }, 1500);
                    } else {
                      $("#addNewCoupon").show();
                    }
                  },
                  error: function (data) {
                    console.log(data);
                    $("#addNewCoupon").show();
                  },
                });
              } else {
                popUpMessage(
                  "center",
                  "Limit cannot be a negative value",
                  "warning"
                );
              }
            } else {
              popUpMessage(
                "center",
                "Minimum cart cannot be a negative amount",
                "warning"
              );
            }
          } else {
            popUpMessage(
              "center",
              "Maximum discount cannot be a negative amount",
              "warning"
            );
          }
        } else {
          popUpMessage(
            "center",
            "Discount cannot be a negative value",
            "warning"
          );
        }
      } else {
        popUpMessage("center", "Please enter coupon code", "warning");
      }
    } else {
      popUpMessage("center", "Please enter coupon title", "warning");
    }
  });

  // Menu

  $("#menuBuilder").domenu({
    data: "",
  });
});
