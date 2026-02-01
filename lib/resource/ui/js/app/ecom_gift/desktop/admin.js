$(document).ready(function (e) {
  /** SweetAlert Pop Up Box */

  function popUpMessage(msgPostion, msgTxt, msgType) {
    if (msgType == 200) {
      msgType = "success";
    }

    Swal.fire({
      position: msgPostion,
      type: msgType,
      title: msgTxt,
      showConfirmButton: false,
      timer: 1500,
    });
  }

  /** Start product details accordion tabs */

  $("#productGeneral").click(function () {
    $(".productGeneral").show();
    $("#productGeneral").addClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productSpecifications").click(function () {
    $(".productSpecifications").show();
    $("#productSpecifications").addClass("active");
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
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productInventory").click(function () {
    $(".productInventory").show();
    $("#productInventory").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productShipping").click(function () {
    $(".productShipping").show();
    $("#productShipping").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productGallery").click(function () {
    $(".productGallery").show();
    $("#productGallery").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productAttributesDetails").click(function () {
    $(".productAttributesDetails").show();
    $("#productAttributesDetails").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAdditionalDetails").hide();
    $("#productAdditionalDetails").removeClass("active");
  });

  $("#productAddOnsDetails").click(function () {
    $(".productAddOnsDetails").show();
    $("#productAddOnsDetails").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
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

  $("#productAdditionalDetails").click(function () {
    $(".productAdditionalDetails").show();
    $("#productAdditionalDetails").addClass("active");
    $(".productGeneral").hide();
    $("#productGeneral").removeClass("active");
    $(".productSpecifications").hide();
    $("#productSpecifications").removeClass("active");
    $(".productInventory").hide();
    $("#productInventory").removeClass("active");
    $(".productShipping").hide();
    $("#productShipping").removeClass("active");
    $(".productGallery").hide();
    $("#productGallery").removeClass("active");
    $(".productAddOnsDetails").hide();
    $("#productAddOnsDetails").removeClass("active");
    $(".productAttributesDetails").hide();
    $("#productAttributesDetails").removeClass("active");
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
                  "warning",
                );
              }
            } else {
              popUpMessage(
                "center",
                "Minimum cart cannot be a negative amount",
                "warning",
              );
            }
          } else {
            popUpMessage(
              "center",
              "Maximum discount cannot be a negative amount",
              "warning",
            );
          }
        } else {
          popUpMessage(
            "center",
            "Discount cannot be a negative value",
            "warning",
          );
        }
      } else {
        popUpMessage("center", "Please enter coupon code", "warning");
      }
    } else {
      popUpMessage("center", "Please enter coupon title", "warning");
    }
  });

  $("#updateCoupon").click(function (e) {
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
                $("#updateCoupon").hide();

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
                      $("#updateCoupon").show();
                    }
                  },
                  error: function (data) {
                    console.log(data);
                    $("#updateCoupon").show();
                  },
                });
              } else {
                popUpMessage(
                  "center",
                  "Limit cannot be a negative value",
                  "warning",
                );
              }
            } else {
              popUpMessage(
                "center",
                "Minimum cart cannot be a negative amount",
                "warning",
              );
            }
          } else {
            popUpMessage(
              "center",
              "Maximum discount cannot be a negative amount",
              "warning",
            );
          }
        } else {
          popUpMessage(
            "center",
            "Discount cannot be a negative value",
            "warning",
          );
        }
      } else {
        popUpMessage("center", "Please enter coupon code", "warning");
      }
    } else {
      popUpMessage("center", "Please enter coupon title", "warning");
    }
  });
});
