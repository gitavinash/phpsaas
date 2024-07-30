var activeDamageBoxId = 0;
let count = 1;

var oldJsonObject = "";

function convertAreatoFeet(size, type) {
  switch (type) {
    case "mm":
      updateSize = size * 0.00328084;
      break;
    case "cm":
      updateSize = size * 0.0328084;
      break;
    case "in":
      updateSize = size * 0.0833333;
      break;
    default:
      updateSize = size;
      break;
  }
  return updateSize;
  // return parseFloat(updateSize.toFixed(2));
}

// Add new warehouse

$("#addWarehouse").click(function (e) {
  e.preventDefault();
  var name = $("#name").val();
  var address = $("#address").val();
  var state = $("#state").val();
  var city = $("#city").val();
  var pinCode = $("#pinCode").val();
  var email = $("#email").val();
  var mobile = $("#mobile").val();
  var companyId = $("#companyId").val();

  if (state == "select") {
    state = "";
  }

  if (city == "select") {
    city = "";
  }

  if (companyId == "select") {
    companyId = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                if (companyId != "") {
                  submitRequest = true;
                } else {
                  msg = "Kindly select company";
                }
              } else {
                msg = "Kindly enter warehouse mobile";
              }
            } else {
              msg = "Kindly enter warehouse email";
            }
          } else {
            msg = "Kindly enter warehouse address pin code";
          }
        } else {
          msg = "Kindly enter warehouse address city";
        }
      } else {
        msg = "Kindly select state of warehouse address";
      }
    } else {
      msg = "Kindly enter warehouse address";
    }
  } else {
    msg = "Kindly enter warehouse name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#addNewWarehouseForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/warehouse/create/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const warehouseId = btoa(successResponse.warehouseId);
            window.location.href = `/dashboard/warehouse/update/?id=${warehouseId}`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  }
});

// Update warehouse

$("#updateWarehouse").click(function (e) {
  e.preventDefault();
  var name = $("#name").val();
  var address = $("#address").val();
  var state = $("#state").val();
  var city = $("#city").val();
  var pinCode = $("#pinCode").val();
  var email = $("#email").val();
  var mobile = $("#mobile").val();

  if (state == "select") {
    state = "";
  }

  if (city == "select") {
    city = "";
  }

  if (companyId == "select") {
    companyId = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                if (companyId != "") {
                  submitRequest = true;
                } else {
                  msg = "Kindly select company";
                }
              } else {
                msg = "Kindly enter warehouse mobile";
              }
            } else {
              msg = "Kindly enter warehouse email";
            }
          } else {
            msg = "Kindly enter warehouse address pin code";
          }
        } else {
          msg = "Kindly enter warehouse address city";
        }
      } else {
        msg = "Kindly select state of warehouse address";
      }
    } else {
      msg = "Kindly enter warehouse address";
    }
  } else {
    msg = "Kindly enter warehouse name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#updateWarehouseForm").serialize();
    $.ajax({
      type: "POST",
      url: "/api/warehouse/update/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            window.location.href = `/dashboard/warehouse/list/`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  }
});

$(document).on("click", ".deleteButton", function (event) {
  event.preventDefault();
  var disableRedirect = false;
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.value) {
      var requestType = $(this).attr("data-type");
      var requestId = $(this).attr("data-id");
      var damageBoxId = $(this).attr("data-damage-id");
      if (typeof $(this).attr("data-action") !== "undefined") {
        var urlPostFix =
          btoa(requestId) +
          "&deteteType=" +
          $(this).attr("data-action") +
          "&damageIndex=" +
          (damageBoxId - 1);
      } else {
        var urlPostFix = btoa(requestId);
      }
      var pageReload = $(this).attr("data-reload");
      var pageRedirect = $(this).attr("data-redirect");
      $.ajax({
        type: "GET",
        url: `/api/${requestType}/delete/?id=` + urlPostFix,
        dataType: "json",
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          if (response.status == "success") {
            let parts = requestType.split("/");
            let lastPart = parts[parts.length - 1];
            switch (lastPart) {
              case "block":
                lastPart = "inventory";
                break;
            }
            switch (lastPart) {
              case "block":
                lastPart = "inventory";
                break;
              case "item":
                disableRedirect = true;
                break;
            }
            setTimeout(function () {
              if (disableRedirect) {
                window.location.href = `/dashboard/${lastPart}/list/`;
              } else {
                location.reload();
              }
            }, 1500);
          }
        },
        error: function (response) {
          console.log(`Error : `, response);
          popUpMessage(
            "center",
            response.responseJSON.msg,
            response.responseJSON.status
          );
        },
      });
    }
  });
});

// User

var companyDropDown = document.getElementById("companyDropDown");

if (companyDropDown) {
  if ($("#companyDropDown")) {
    $("#company").on("change", function (e) {
      e.preventDefault();
      var companyId = $("#company").val();
      $.ajax({
        type: "GET",
        url: "/api/warehouse/list/?company_id=" + companyId,
        dataType: "json",
        success: function (response) {
          $("#warehouseBox").show();
          $("#warehouse").empty();
          $("#warehouse").append(new Option("Select warehouse", ""));
          response.warehouseData.forEach(function (warehouse) {
            $("#warehouse").append(new Option(warehouse.name, warehouse.id));
          });
        },
        error: function (response) {
          console.log(`Error : `, response);
        },
      });
    });
  }
}

// Add new supplier

$("#addSupplier").click(function (e) {
  e.preventDefault();
  var name = $("#name").val();
  var address = $("#address").val();
  var state = $("#state").val();
  var city = $("#city").val();
  var pinCode = $("#pinCode").val();
  var email = $("#email").val();
  var mobile = $("#mobile").val();

  if (state == "select") {
    state = "";
  }

  if (city == "select") {
    city = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                submitRequest = true;
              } else {
                msg = "Kindly enter supplier mobile";
              }
            } else {
              msg = "Kindly enter supplier email";
            }
          } else {
            msg = "Kindly enter supplier address pin code";
          }
        } else {
          msg = "Kindly enter supplier address city";
        }
      } else {
        msg = "Kindly select state of supplier address";
      }
    } else {
      msg = "Kindly enter supplier address";
    }
  } else {
    msg = "Kindly enter supplier name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#addNewSupplierForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/supplier/create/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const supplierId = btoa(successResponse.supplierId);
            window.location.href = `/dashboard/supplier/update/?id=${supplierId}`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  }
});

// Update supplier

$("#updateSupplier").click(function (e) {
  e.preventDefault();
  var name = $("#name").val();
  var address = $("#address").val();
  var state = $("#state").val();
  var city = $("#city").val();
  var pinCode = $("#pinCode").val();
  var email = $("#email").val();
  var mobile = $("#mobile").val();

  if (state == "select") {
    state = "";
  }

  if (city == "select") {
    city = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                submitRequest = true;
              } else {
                msg = "Kindly enter supplier mobile";
              }
            } else {
              msg = "Kindly enter supplier email";
            }
          } else {
            msg = "Kindly enter supplier address pin code";
          }
        } else {
          msg = "Kindly enter supplier address city";
        }
      } else {
        msg = "Kindly select state of supplier address";
      }
    } else {
      msg = "Kindly enter supplier address";
    }
  } else {
    msg = "Kindly enter supplier name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#updateSupplierForm").serialize();
    $.ajax({
      type: "POST",
      url: "/api/supplier/update/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            window.location.href = `/dashboard/supplier/list/`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  }
});

// Add new block

// Add new supplier

$("#addBlock").click(function (e) {
  e.preventDefault();
  $("#addBlock").hide();
  var name = $("#name").val();
  var warehouseId = $("#warehouseId").val();
  var itemCode = $("#itemCode").val();
  var totalItem = $("#totalItem").val();
  var thickness = $("#thickness").val();
  var productType = $("#productType").val();

  if (warehouseId == "select") {
    warehouseId = "";
  }

  if (supplier == "select") {
    supplier = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (warehouseId != "") {
      if (itemCode != "") {
        if (totalItem != "") {
          if (thickness != "") {
            if (productType != "") {
              submitRequest = true;
            } else {
              msg = "Kindly select product type";
            }
          } else {
            msg = "Kindly enter block item thickness";
          }
        } else {
          msg = "Kindly enter the total number of items";
        }
      } else {
        msg = "Kinldy enter item code of block";
      }
    } else {
      msg = "Kindly select block warehouse";
    }
  } else {
    msg = "Kindly enter block name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
    $("#addBlock").show();
  } else {
    var formData = new FormData(document.getElementById("addNewInventoryForm"));

    $.ajax({
      type: "POST",
      url: "/api/block/publish/",
      dataType: "JSON",
      data: formData,
      processData: false,
      contentType: false,
      encrypt: "multipart/form-data",
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const blockId = btoa(successResponse.id);
            // window.location.href = `/dashboard/inventory/update/?id=${blockId}`;
            window.location.href = `dashboard/inventory/update/?updateType=item&id=${blockId}`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
        $("#addBlock").show();
      },
    });
  }
});

// Update block

// Add new supplier

$("#updateBlock").click(function (e) {
  e.preventDefault();
  $("#updateBlock").hide();
  var name = $("#name").val();
  var warehouseId = $("#warehouseId").val();
  var itemCode = $("#itemCode").val();
  var totalItem = $("#totalItem").val();
  var thickness = $("#thickness").val();
  var productType = $("#supplier").val();

  if (warehouseId == "select") {
    warehouseId = "";
  }

  if (supplier == "select") {
    supplier = "";
  }

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (warehouseId != "") {
      if (itemCode != "") {
        if (totalItem != "") {
          if (thickness != "") {
            if (productType != "") {
              submitRequest = true;
            } else {
              msg = "Kindly select product type";
            }
          } else {
            msg = "Kindly enter block item thickness";
          }
        } else {
          msg = "Kindly enter the total number of items";
        }
      } else {
        msg = "Kinldy enter item code of block";
      }
    } else {
      msg = "Kindly select block warehouse";
    }
  } else {
    msg = "Kindly enter block name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
    $("#updateBlock").show();
  } else {
    var formData = new FormData(document.getElementById("updateInventoryForm"));

    $.ajax({
      type: "POST",
      url: "/api/block/update/",
      dataType: "JSON",
      data: formData,
      processData: false,
      contentType: false,
      encrypt: "multipart/form-data",
      success: function (successResponse) {
        $("#updateBlock").show();
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const blockId = btoa(successResponse.id);
            window.location.href = `/dashboard/inventory/update/?id=${blockId}`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        $("#updateBlock").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  }
});

$("#updateBlockItem").click(function (e) {
  e.preventDefault();
  $("#updateBlockItem").hide();
  var allFilled = true;

  $(".heightValues").each(function () {
    if ($(this).val() === "") {
      allFilled = false;
      return false; // Breaks the .each() loop
    }
  });

  if (allFilled) {
    var allFilled = true;

    $(".widthValues").each(function () {
      if ($(this).val() === "") {
        allFilled = false;
        return false; // Breaks the .each() loop
      }
    });
  } else {
    popUpMessage("center", "Kinldy fill all missing items height", "warning");
    $("#updateBlockItem").show();
  }

  if (allFilled) {
    var formData = $("#updateInventoryForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/item/publish/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            location.reload();
          }, 1500);
        }
        $("#updateBlockItem").show();
      },
      error: function (errorResponse) {
        $("#updateBlockItem").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  } else {
    popUpMessage("center", "Kinldy fill all missing items width", "warning");
    $("#updateBlockItem").show();
  }
});

// Manage damage box

var manageDamageBtn = document.getElementsByClassName("manageDamage");

if (manageDamageBtn) {
  $(".manageDamage").click(function (e) {
    e.preventDefault();
    activeDamageBoxId = $(this).attr("data-id");
    $("#blockItemId").text($(this).attr("data-index"));
    $(".popUpUpdateDamageForm")
      .find("input")
      .each(function () {
        $(this).val("");
      });

    $(".cloneDamageForm").empty();
    $("#damageFormPrevious").empty();
    $(".popUpUpdateDamageFormInputs").empty();
    var localValues = localStorage.getItem(`blockItem_${activeDamageBoxId}`);
    oldJsonObject = localValues;
    if (localValues != "") {
      localValues = JSON.parse(localValues);
      if (Array.isArray(localValues)) {
        var i = 1;
        localValues.forEach(function (value) {
          if (value["damageAreaWidth"] != "") {
            var formHtml =
              `
            <div class="full-width popUpUpdateDamageForm formBox_${i}" id="damageForm_${i}">
                <div class="col-2">
                    <div class="form-group">
                        <input id="damageAreaWidth_${i}" class="width" name="damageAreaWidth" value="${value["damageAreaWidth"]}" type="number" />
                        <label class="form-lable" for="damageAreaWidth_${i}">Length</label>
                    </div>
                </div>
                <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control"
                                                name="damageAreaWidthThicknessDimension">
                                                <option value="mm" ` +
              (value["damageAreaWidthThicknessDimension"] == "mm"
                ? "selected"
                : "") +
              `>Millimeter</option>
                                                <option value="cm" ` +
              (value["damageAreaWidthThicknessDimension"] == "cm"
                ? "selected"
                : "") +
              `>Centimeter</option>
                                                <option value="in" ` +
              (value["damageAreaWidthThicknessDimension"] == "in"
                ? "selected"
                : "") +
              `>Inches</option>
                                                <option value="ft" ` +
              (value["damageAreaWidthThicknessDimension"] == "ft"
                ? "selected"
                : "") +
              `>Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                <div class="col-2">
                    <div class="form-group">
                        <input id="damageAreaHeight_${i}" class="height" name="damageAreaHeight" value="${value["damageAreaHeight"]}" type="number" />
                        <label class="form-lable" for="damageAreaHeight_${i}">Height</label>
                    </div>
                </div>
                <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control"
                                                name="damageAreaHeightThicknessDimension"><option value="mm" ` +
              (value["damageAreaHeightThicknessDimension"] == "mm"
                ? "selected"
                : "") +
              `>Millimeter</option>
                                                <option value="cm" ` +
              (value["damageAreaHeightThicknessDimension"] == "cm"
                ? "selected"
                : "") +
              `>Centimeter</option>
                                                <option value="in" ` +
              (value["damageAreaHeightThicknessDimension"] == "in"
                ? "selected"
                : "") +
              `>Inches</option>
                                                <option value="ft" ` +
              (value["damageAreaHeightThicknessDimension"] == "ft"
                ? "selected"
                : "") +
              `>Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                <div class='col-2'>
                    <div class="form-group">
                      <div class='actionBtn'>
                          <span class='bgRed deleteButton' data-type='ims_marble/item/' data-action='damage' data-damage-id='${i}' data-id='${activeDamageBoxId}' data-reload='false' data-redirect='false'><i
                                  class='fi fi-rr-trash'></i></span>
                      </div>
                    </div>
                </div>
            </div>
        `;
            $(".popUpUpdateDamageFormInputs").append(formHtml);
          }
          i++;
        });
      }
    }
    $(".manageDamageBox").show();
  });
}

var addNewDamageBtn = document.getElementById("addNewDamage");

if (addNewDamageBtn) {
  $("#addNewDamage").on("click", function (e) {
    e.preventDefault();

    var $originalForm = $("#damageForm").clone(true);
    $originalForm.find("input").each(function () {
      $(this).val("");
    });
    $originalForm.attr("id", "damageForm_" + count);
    count++;
    $(".cloneDamageForm").append($originalForm);
  });
}

var updateDamageBtn = document.getElementById("updateDamage");

if (updateDamageBtn) {
  $("#updateDamage").on("click", function (e) {
    e.preventDefault();
    var widthSizes;
    var heightSizes;
    var totatDamageCount = 0;
    var totalSizes = [];
    $("#updateDamage").hide();
    // Collect data from all forms
    var allFormData = [];

    $(".popUpUpdateDamageForm").each(function () {
      var formData = {};
      $(this)
        .find("input, select")
        .each(function () {
          formData[$(this).attr("name")] = $(this).val();
        });

      widthSizes = "";
      heightSizes = "";
      if (formData.damageAreaWidth != "" || formData.damageAreaHeight != "") {
        widthSizes = convertAreatoFeet(
          formData.damageAreaWidth,
          formData.damageAreaWidthThicknessDimension
        );
        heightSizes = convertAreatoFeet(
          formData.damageAreaHeight,
          formData.damageAreaHeightThicknessDimension
        );

        totalSizes.push(widthSizes * heightSizes);
        allFormData.push(formData);
        totatDamageCount++;
      }
    });
    allFormData = JSON.stringify(allFormData);

    if (allFormData == oldJsonObject) {
      var sameJson = true;
    } else {
      var sameJson = false;
    }

    totalSizes = totalSizes.reduce(
      (total, width) => total + parseFloat(width),
      0
    );

    $.ajax({
      type: "POST",
      url: "/api/item/update/",
      dataType: "JSON",
      data: {
        id: btoa(activeDamageBoxId),
        damageAreaSize: totalSizes,
        damageAreaSizeType: "ft",
        damageArea: allFormData,
        totatDamageCount: totatDamageCount,
        sameJson,
      },
      success: function (successResponse) {
        $("#updateDamage").show();
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          localStorage.setItem("blockItem_" + activeDamageBoxId, allFormData);
          setTimeout(function () {
            location.reload();
          }, 1500);
        }
      },
      error: function (errorResponse) {
        $("#updateDamage").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  });
}

$("#addMeasurement").click(function (e) {
  e.preventDefault();
  $("#addMeasurement").hide();
  var productType = $("#productType").val();
  var measurementType = $("#measurementType").val();
  var calculationType = $("#calculationType").val();
  var submitRequest = false;

  if (productType != "") {
    if (measurementType != "") {
      if (calculationType != "") {
        submitRequest = true;
      } else {
        msg = "Select calculation type";
      }
    } else {
      msg = "Select measurement type";
    }
  } else {
    msg = "Enter name of product type";
  }

  if (submitRequest) {
    var formData = $("#addNewMeasurementForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/measurement/publish/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        console.log(`successResponse`, successResponse);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const configurationId = btoa(successResponse.configurationId);
            window.location.href = `/dashboard/measurement/update/?id=${configurationId}`;
          }, 1500);
        }
        $("#addMeasurement").show();
      },
      error: function (errorResponse) {
        $("#addMeasurement").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  } else {
    popUpMessage("center", msg, "warning");
    $("#addMeasurement").show();
  }
});

$("#updateMeasurement").click(function (e) {
  e.preventDefault();
  $("#updateMeasurement").hide();
  var productType = $("#productType").val();
  var measurementType = $("#measurementType").val();
  var calculationType = $("#calculationType").val();
  var submitRequest = false;

  if (productType != "") {
    if (measurementType != "") {
      if (calculationType != "") {
        submitRequest = true;
      } else {
        msg = "Select calculation type";
      }
    } else {
      msg = "Select measurement type";
    }
  } else {
    msg = "Enter name of product type";
  }

  if (submitRequest) {
    var formData = $("#updateMeasurementForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/measurement/update/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        console.log(`successResponse`, successResponse);
        if (successResponse.status == "success") {
          setTimeout(function () {
            window.location.href = `/dashboard/measurement/update/?id=${successResponse.configurationId}`;
          }, 1500);
        }
        $("#updateMeasurement").show();
      },
      error: function (errorResponse) {
        $("#updateMeasurement").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  } else {
    popUpMessage("center", msg, "warning");
    $("#updateMeasurement").show();
  }
});

$(document).ready(function () {
  $("#cloneSlabForm").click(function (e) {
    e.preventDefault();

    // Function to validate if all inputs have numerical values
    function allInputsHaveValues() {
      var isValid = true;
      $(".generateBlockRow").each(function () {
        $(this)
          .find("input")
          .each(function () {
            if (!$.isNumeric($(this).val()) || $(this).val().trim() === "") {
              isValid = false;
              return false; // Break out of the each loop
            }
          });
        if (!isValid) {
          return false; // Break out of the each loop
        }
      });
      return isValid;
    }

    // Check if all inputs have numerical values
    if (!allInputsHaveValues()) {
      popUpMessage("center", "Please fill in all required fields", "warning");
      return; // Exit the function if validation fails
    }

    // Clone the .generateBlockRow div
    var $clone = $(".generateBlockRow").first().clone();

    // Clear the input values in the cloned div
    $clone.find("input").val("");

    // Remove the clone button from the cloned div
    $clone.find("#cloneSlabForm").parent().remove();

    // Append the cloned div to #generateBlockRowBox
    $("#generateBlockRowBox").append($clone);

    // Show the clone button in the last form
    $(".generateBlockRow").last().find("#cloneSlabForm").show();
  });

  $("#createBlockItem").click(function (e) {
    e.preventDefault();
    $("#createBlockItem").hide();

    function allInputsHaveValues() {
      var isValid = true;
      $(".generateBlockRow").each(function () {
        $(this)
          .find("input")
          .each(function () {
            if (!$.isNumeric($(this).val()) || $(this).val().trim() === "") {
              isValid = false;
              return false; // Break out of the each loop
            }
          });
        if (!isValid) {
          return false; // Break out of the each loop
        }
      });
      return isValid;
    }

    // Check if all inputs have numerical values
    if (!allInputsHaveValues()) {
      popUpMessage("center", "Please fill in all required fields", "warning");
      $("#createBlockItem").show();
      return; // Exit the function if validation fails
    }
    // Gather data from the form
    var data = [];
    var totalSlabsSum = 0;
    $("#generateBlockRowBox .generateBlockRow").each(function () {
      var row = {
        slabLenght: $(this).find('input[name="slabLenght"]').val(),
        slabHeight: $(this).find('input[name="slabHeight"]').val(),
        totalSlab: $(this).find('input[name="totalSlab"]').val(),
      };
      data.push(row);
      var totalSlabValue = parseFloat(row.totalSlab);
      if (!isNaN(totalSlabValue)) {
        totalSlabsSum += totalSlabValue;
      }
    });

    var totalSlabs = $("#blockTotalCount").val();

    var payload = {
      blockId: btoa($("#blockId").val()),
      totalSlabs,
      slabsData: data,
    };

    if (totalSlabsSum == totalSlabs) {
      $.ajax({
        url: "/api/item/generate/",
        method: "POST",
        data: JSON.stringify(payload),
        contentType: "application/json",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          $("#createBlockItem").show();
          if (successResponse.status == "success") {
            setTimeout(function () {
              location.reload();
            }, 1500);
          }
        },
        error: function (errorResponse) {
          console.log(`Error:`, errorResponse);
          $("#createBlockItem").show();
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    } else {
      popUpMessage(
        "center",
        `Total number of slab must be ${totalSlabs}`,
        "warning"
      );
      $("#createBlockItem").show();
    }
  });
});
