var activeDamageBoxId = 0;
let count = 1;

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
                var pageReload = $(this).attr("data-reload");
                if (typeof pageReload === undefined) {
                  location.reload();
                } else {
                  $(`.formBox_${damageBoxId}`).hide();
                }
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
  var supplier = $("#supplier").val();

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
            if (supplier != "") {
              if (blockSize != "") {
                submitRequest = true;
              } else {
                msg = "Kindly select block size calculation";
              }
            } else {
              msg = "Kindly select block supplier";
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
  var supplier = $("#supplier").val();

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
            if (supplier != "") {
              submitRequest = true;
            } else {
              msg = "Kindly select block supplier";
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
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const blockId = btoa(successResponse.id);
            window.location.href = `/dashboard/inventory/update/?id=${blockId}`;
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
        $("#updateBlock").show();
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
    $(".cloneDamageForm").empty();
    $("#damageFormPrevious").empty();
    $(".popUpUpdateDamageFormInputs").empty();
    var localValues = localStorage.getItem(`blockItem_${activeDamageBoxId}`);
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
                        <label class="form-lable" for="damageAreaWidth_${i}">Width</label>
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
      if (formData.damageAreaWidth != "" || formData.damageAreaHeight != "") {
        allFormData.push(formData);
      }
    });
    allFormData = JSON.stringify(allFormData);

    $.ajax({
      type: "POST",
      url: "/api/item/update/",
      dataType: "JSON",
      data: {
        id: btoa(activeDamageBoxId),
        damageArea: allFormData,
      },
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          localStorage.setItem("blockItem_" + activeDamageBoxId, allFormData);
          setTimeout(function () {
            location.reload();
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
        $("#updateDamage").show();
      },
    });
  });
}
