var activeDamageBoxId = 0;
let count = 1;
var activeBlockId = 0;
var activeBlockTitle = 0;
var selectedBlocksLenght = 0;
var oldJsonObject = "";

var customerName = "";
var customerMobile = "";
var customerEmail = "";
var customerAddress = "";
var salesWarehouseId = "";

function deleteSalesBlocks(prefix) {
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key.startsWith(prefix)) {
      localStorage.removeItem(key);
      i--;
    }
  }
}

// Usage
deleteSalesBlocks("salesBlocks_");

function closePopup(btn) {
  var popupContainer = btn.closest(".popUp");
  popupContainer.style.display = "none";
  document.querySelector(".popUp").style.display = "none";
}

function getSalesBlocks(prefixValue) {
  const dataList = {};
  selectedBlocksLenght = 0;
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key.startsWith(prefixValue)) {
      dataList[key] = localStorage.getItem(key);
      selectedBlocksLenght++;
    }
  }

  return dataList;
}

$(document).ready(function () {
  // Main Box tabs

  if ($(".steps-tab").length) {
    $(".step-box").click(function (e) {
      e.preventDefault();
      currentElementID = $(this).attr("id");
      $(".step-box").removeClass("active");
      showCurrentBox = currentElementID + "_box";
      showCurrentLable = currentElementID;
      $(".step-open-box").hide();
      $("#" + showCurrentLable).addClass("active");
      if (currentElementID == "salesStep_2") {
        customerName = $("#name").val();
        customerEmail = $("#email").val();
        customerMobile = $("#mobile").val();
        customerAddress = $("#address").val();
        salesWarehouseId = $("#warehouse").val();
        if (customerName.trim() === "") {
          // Stop further processing
          showCurrentBox = "salesStep_1_box";
          popUpMessage("center", "Customer name is required.", "warning");
        } else if (customerMobile.trim() === "") {
          // Stop further processing
          showCurrentBox = "salesStep_1_box";
          popUpMessage(
            "center",
            "Customer mobile number is required.",
            "warning"
          );
        } else if (customerAddress.trim() === "") {
          // Stop further processing
          showCurrentBox = "salesStep_1_box";
          popUpMessage("center", "Customer address is required.", "warning");
        } else if (salesWarehouseId.trim() === "") {
          // Stop further processing
          showCurrentBox = "salesStep_1_box";
          popUpMessage("center", "Kindly select warehouse.", "warning");
        }

        if (showCurrentBox == "salesStep_2_box") {
          if ($.isNumeric(salesWarehouseId)) {
            $("#blockData").empty();
            $.ajax({
              type: "GET",
              url: "/api/report/blocks/?warehouseId=" + salesWarehouseId,
              dataType: "JSON",
              success: function (successResponse) {
                if (successResponse.status == "success") {
                  successResponse.items.forEach(function (data) {
                    var blockBtn = `<p class='openBlockSlab blockIcon' data-id='${data.block_id}' data-title='${data.block_code} - ${data.block_title}' >${data.block_code} - ${data.block_title} <span id='block_${data.block_id}' >0</span</p>`;
                    $("#blockData").append(blockBtn);
                  });
                }
              },
              error: function (errorResponse) {
                showCurrentBox = "salesStep_1_box";
                console.log(`Error warehouse:`, errorResponse);
                popUpMessage(
                  "center",
                  errorResponse.responseJSON.msg,
                  errorResponse.responseJSON.status
                );
              },
            });
          } else {
            popUpMessage("center", "Select valid warehouse.", "warning");
          }
        }
      }
      if (currentElementID == "salesStep_3") {
        // Sales confirmation data
        $("#customerName").text(customerName);
        $("#customerEmail").text(customerEmail);
        $("#customerMobile").text(customerMobile);
        $("#customerAddress").text(customerAddress);

        // Usage
        const salesBlocksData = getSalesBlocks("salesBlocks_");

        if (selectedBlocksLenght != 0) {
          let itemsContainer = $(".inventoryData");

          itemsContainer.empty();

          itemsContainer.append(
            `<div>
                            <h2 class="heading">Inventory details</h2>
                        </div>`
          );

          $.each(salesBlocksData, function (key, value) {
            // Split the key by underscore and get the last part
            let blockTitle = key.split("_");
            let blockTitleName = blockTitle[2];

            let slabsArray = value.split(",");
            let slabCounts = {};
            let totalCount = 0;

            // Loop through each slab entry
            slabsArray.forEach((slab) => {
              // Split the slab entry by underscore to extract details
              let slabParts = slab.split("_");

              // The second part of the split is the slabDimension (e.g., '150in x 110in')
              let slabDimension = slabParts[1];

              // Increment the count for this slabDimension in the slabCounts object
              slabCounts[slabDimension] = (slabCounts[slabDimension] || 0) + 1;
              totalCount++;
            });

            // Now format the result to display each unique slab size with its count
            let uniqueValues = Object.entries(slabCounts)
              .map(([dimension, count]) => {
                return `<p class='stockInfoList' >${dimension} <span>(x${count})</span></p>`;
              })
              .join("");

            // Create the table rows with the block title and inventory data
            let inventoryData = `
              <div ><strong class='stockInfoList'>${blockTitleName} <span>(x${totalCount})</span></strong></div>
              <div class='border'>${uniqueValues}</div>
            `;

            // Append the generated HTML to the container
            itemsContainer.append(inventoryData);
          });
        } else {
          popUpMessage(
            "center",
            "Select mimumum 1 block to continue",
            "warning"
          );
          showCurrentBox = "salesStep_2_box";
        }
      }

      $("." + showCurrentBox).show();
      hidePageLoader();
    });
  }
});

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

$(document).on("click", ".deleteButtonForDamageArea", function (event) {
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
      var requestActionType = $(this).attr("data-action");
      var damageBoxId = $(this).attr("data-damage-id");
      var pageReload = $(this).attr("data-reload") || false;
      var pageRedirect = $(this).attr("data-redirect") || false;
      if (pageReload || pageRedirect) {
        var redirectTo = $(this).attr("data-url") || `#`;
      }
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

      const parentDiv = $(this).closest(".popUpUpdateDamageForm");
      var parentId = parentDiv.attr("id");

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
              if (disableRedirect == true && pageRedirect == true) {
                if (disableRedirect) {
                  window.location.href = redirectTo;
                }
                if (pageRedirect) {
                  window.location.href = redirectTo;
                }
              } else {
                $("#" + parentId).hide();
              }
              console.log(`disableRedirect`, disableRedirect);
              console.log(`pageRedirect`, pageRedirect);
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

  if (productType == "select") {
    productType = "";
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
            window.location.href = `/dashboard/inventory/update/?updateType=item&id=${blockId}`;
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
                        <input id="damageAreaWidth_${i}" class="width" name="damageAreaWidth" value="${value["damageAreaWidth"]}" type="number" onwheel="this.blur()" />
                        <label class="form-lable" for="damageAreaWidth_${i}">Length</label>
                    </div>
                </div>
                <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control"
                                                name="damageAreaWidthThicknessDimension" disabled>
                                                <option value="mm" ` +
              (measurementType == "millimeter" ? "selected" : "") +
              `>Millimeter</option>
                                                <option value="cm" ` +
              (measurementType == "centimeter" ? "selected" : "") +
              `>Centimeter</option>
                                                <option value="in" ` +
              (measurementType == "inches" ? "selected" : "") +
              `>Inches</option>
                                                <option value="ft" ` +
              (measurementType == "feet" ? "selected" : "") +
              `>Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                <div class="col-2">
                    <div class="form-group">
                        <input id="damageAreaHeight_${i}" class="height" name="damageAreaHeight" value="${value["damageAreaHeight"]}" type="number" onwheel="this.blur()" />
                        <label class="form-lable" for="damageAreaHeight_${i}">Height</label>
                    </div>
                </div>
                <div class="col-2">
                                        <div class="form-group">
                                            <select class="form-control"
                                                name="damageAreaHeightThicknessDimension" disabled><option value="mm" ` +
              (measurementType == "millimeter" ? "selected" : "") +
              `>Millimeter</option>
                                                <option value="cm" ` +
              (measurementType == "centimeter" ? "selected" : "") +
              `>Centimeter</option>
                                                <option value="in" ` +
              (measurementType == "inches" ? "selected" : "") +
              `>Inches</option>
                                                <option value="ft" ` +
              (measurementType == "feet" ? "selected" : "") +
              `>Feet</option>
                                            </select>
                                            <label class="form-lable" id="Label">Size<sup>*</sup></label>
                                        </div>
                                    </div>
                <div class='col-2'>
                    <div class="form-group">
                      <div class='actionBtn'>
                          <span class='bgRed deleteButtonForDamageArea' data-type='ims_marble/item/' data-action='damage' data-damage-id='${i}' data-id='${activeDamageBoxId}' data-reload='false' data-redirect='false'><i
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
  // Use class selector for cloneSlabForm
  $(document).on("click", ".cloneSlabForm", function (e) {
    e.preventDefault();

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

    // Show the remove button in the cloned row
    $clone.find(".removeRow").show();

    // Append the cloned div to #generateBlockRowBox
    $("#generateBlockRowBox").append($clone);

    // Hide the cloneSlabForm button in all rows and show it only in the last row
    $(".generateBlockRow").find(".cloneSlabForm").hide();
    $(".generateBlockRow").last().find(".cloneSlabForm").show();

    // Ensure only the last row's remove button is hidden
    $(".generateBlockRow").find(".removeRow").show(); // Show remove buttons in all rows
    $(".generateBlockRow").last().find(".removeRow").hide(); // Hide remove button in the last row

    // Attach event handler for the new remove button
    attachRemoveEvent();
  });

  function attachRemoveEvent() {
    $(".removeRow")
      .off("click")
      .on("click", function (e) {
        e.preventDefault();
        if ($(".generateBlockRow").length > 1) {
          $(this).closest(".generateBlockRow").remove();

          // Ensure the clone button stays only on the last row
          $(".generateBlockRow").find(".cloneSlabForm").hide();
          $(".generateBlockRow").last().find(".cloneSlabForm").show();

          // Ensure the last row's remove button is hidden after removing a row
          $(".generateBlockRow").find(".removeRow").show();
          $(".generateBlockRow").last().find(".removeRow").hide();
        }
      });
  }

  // Ensure that the first row hides the remove button on load, but it's shown on others except the last one
  $(document).ready(function () {
    // Initially hide the remove button on the first row, and ensure it's hidden only on the last row
    $(".generateBlockRow").find(".removeRow").show(); // Show remove buttons for all rows
    $(".generateBlockRow").last().find(".removeRow").hide(); // Hide remove button for the last row only
    attachRemoveEvent(); // Attach the event handler on page load
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

    if (totalSlabsSum != totalSlabs) {
      Swal.fire({
        title: "Save this task?",
        text: "Would you like to save this task now? You can continue working on it after saving.",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#03637B",
        cancelButtonColor: "#C60239",
        confirmButtonText: "Save and Continue",
      }).then((result) => {
        if (result.value) {
          createBlockSlabs(payload);
        }
        $("#createBlockItem").show();
      });
    } else {
      createBlockSlabs(payload);
      $("#createBlockItem").show();
    }
  });

  // Send request to server to create block slabs

  function createBlockSlabs(payload) {
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
  }

  $(document).on("click", ".openBlockSlab", function (e) {
    e.preventDefault();
    var blockId = $(this).attr("data-id");
    activeBlockId = blockId;
    activeBlockTitle = $(this).attr("data-title");

    $.ajax({
      type: "GET",
      url: `/api/block/items/?blockId=` + blockId,
      dataType: "json",
      success: function (response) {
        let itemsContainer = $(".slabsListOptions");
        itemsContainer.empty();

        let selectAllCheckbox = `
        <div class='show-5-box box-mb-3-3'>
          <div class='checkbox-wrapper-16'>
              <label class='checkbox-wrapper'>
                <input type='checkbox' class='checkbox-input selectAllSlabs' />
                <span class='checkbox-tile'>
                  <span class='checkbox-label'>
                    Select All
                  </span>
                </span>
              </label>
              </div>
            </div>
        `;

        itemsContainer.append(selectAllCheckbox);

        // Add individual item checkboxes
        $.each(response.items, function (index, item) {
          let slabNetSize = item.slab_total_area - item.slab_damage_area;
          let blockSlabValue =
            item.slab_id +
            "_" +
            item.slab_dimension +
            "_" +
            item.slab_total_area +
            "_" +
            item.slab_damage_area +
            "_" +
            slabNetSize +
            "_" +
            item.slab_dimension_type +
            "_" +
            item.block_slab_id;

          let checkboxLabel = `
            <div class='show-5-box box-mb-3-3'>
              <div class='checkbox-wrapper-16'>
                <label class='checkbox-wrapper'>
                  <input type='checkbox' class='checkbox-input slabCheckbox' name='slabs' value='${blockSlabValue}' />
                  <span class='checkbox-tile'>
                    <span class='checkbox-label'>
                      <p><strong style='float: left;width: 100%;font-size: 24px;line-height: 32px;color: #333;'>${item.block_slab_id}</strong><span>${item.slab_dimension}</span></p>
                    </span>
                  </span>
                </label>
              </div>
          </div>
        `;
          itemsContainer.append(checkboxLabel);
        });

        $("#blockTitle").html(activeBlockTitle);
        $(".blockSlabBox").show();
      },
      error: function (response) {
        console.log(`Error: `, response);
        popUpMessage(
          "center",
          response.responseJSON.msg,
          response.responseJSON.status
        );
      },
    });
  });

  // Handle "Select All" functionality
  $(document).on("change", ".selectAllSlabs", function () {
    var isChecked = $(this).is(":checked");
    $(".slabCheckbox").prop("checked", isChecked);
  });

  // Add slabs btn action

  $(".saveSlabs").click(function (e) {
    e.preventDefault();
    let checkedValues = [];
    $("input[name='slabs']:checked").each(function () {
      checkedValues.push($(this).val());
    });
    $(`#block_${activeBlockId}`).text(checkedValues.length);
    $(`#block_${activeBlockId}`).closest("p").addClass("activeBlockIcon");
    localStorage.setItem(
      `salesBlocks_${activeBlockId}_${activeBlockTitle}`,
      checkedValues
    );
    $(".blockSlabBox").hide();
  });

  $("#salesRequest").click(function (e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "/api/sales/create/",
      dataType: "JSON",
      contentType: "application/json",
      processData: false,
      contentType: false,
      data: JSON.stringify({
        name: customerName,
        email: customerEmail,
        mobile: customerMobile,
        address: customerAddress,
        warehouse: salesWarehouseId,
        refId: $("#refId").val(),
        refType: $("#refType").val(),
        inventory: JSON.stringify(getSalesBlocks("salesBlocks_")),
      }),
      success: function (successResponse) {
        console.log(`successResponse`, successResponse);
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            window.location.href =
              `/dashboard/sales_request/view/?id=` +
              btoa(successResponse.salesId);
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
  });

  $("#approveSalesRequest").click(function (e) {
    e.preventDefault();
    var requestId = $(this).attr("data-id");
    var agentID = $(this).attr("data-agent");
    var refId = $(this).attr("data-refid");
    var refType = $(this).attr("data-reftype");
    var aerialAdjustment = $("#aerialAdjustment").val();
    var aerialValue = $("#aerialValue").val();
    var aerialComment = $("#aerialComment").val();
    if (requestId != "") {
      $.ajax({
        type: "GET",
        url:
          "/api/sales/approve/?id=" +
          requestId +
          "&agent=" +
          agentID +
          "&refId=" +
          refId +
          "&refType=" +
          refType +
          "&aerialAdjustment=" +
          aerialAdjustment +
          "&aerialValue=" +
          aerialValue +
          "&aerialComment=" +
          aerialComment,
        dataType: "JSON",
        success: function (successResponse) {
          if (successResponse.status == "success") {
            popUpMessage("center", successResponse.msg, successResponse.status);
            setTimeout(function () {
              window.location.href = `/dashboard/sales_request/list/`;
            }, 1500);
          }
        },
        error: function (errorResponse) {
          console.log(`Error :`, errorResponse);
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    } else {
      popUpMessage("center", "Invalid sales request id received", "warning");
    }
  });

  // Return invenroy slow

  $("#returnToForm2").click(function (e) {
    e.preventDefault();
    $(".salesStep_3_box").hide();
    $("#salesStep_3").removeClass("active");
    $(".salesStep_2_box").show();
    $("#salesStep_2").removeClass("active");
    $("#salesStep_2").addClass("active");
  });

  $("#returnToForm1").click(function (e) {
    e.preventDefault();
    $(".salesStep_2_box").hide();
    $("#salesStep_2").removeClass("active");
    $(".salesStep_1_box").show();
    $("#salesStep_1").removeClass("active");
    $("#salesStep_1").addClass("active");
  });

  $("#returnInventory").click(function (e) {
    e.preventDefault();
    $(".salesStep_1_box").hide();
    $("#salesStep_1").removeClass("active");
    $(".salesStep_3_box").hide();
    $("#salesStep_3").removeClass("active");
    $(".salesStep_2_box").show();
    $("#salesStep_2").addClass("active");

    var salesId = $("#salesId").val();
    var mobile = $("#mobile").val();
    var msg = "";
    var processRequest = false;
    if (salesId != "") {
      if (mobile != "") {
        processRequest = true;
      } else {
        msg = "Customer mobile number is required";
      }
    } else {
      msg = "Sales id is required";
    }

    if (processRequest) {
      $.ajax({
        type: "GET",
        url: "/api/sales/info/?id=" + btoa(salesId) + "&mobile=" + mobile,
        dataType: "JSON",
        success: function (successResponse) {
          if (successResponse.status == "success") {
            $.each(successResponse.blocks, function (blockKey, blockValues) {
              $(".slabsListOptions").empty();
              var blockHeading = $("<h3>").text(blockKey);
              $(".returnSlabsListOptions").append(blockHeading);
              console.log(`blockKey`, blockKey);
              console.log(`blockValues`, blockValues);

              $.each(blockValues, function (index, value) {
                console.log(`checkboxLabel index :`, index);
                console.log(`checkboxLabel value:`, value);

                // Append the checkbox and label to the container
                $(".returnSlabsListOptions").append(checkboxLabel);
              });
            });
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

  $("#confirmReturnInventory").click(function (e) {
    e.preventDefault();
    $(".salesStep_1_box").hide();
    $("#salesStep_1").removeClass("active");
    $(".salesStep_2_box").hide();
    $("#salesStep_2").removeClass("active");
    $(".salesStep_3_box").show();
    $("#salesStep_3").addClass("active");
  });

  // Mark as sold

  $(".markAsSold").click(function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    var productType = $(this).attr("data-type");
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, Mark it as Sold.",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          type: "GET",
          url: `/api/${productType}/mark-as-sold/?blockId=` + btoa(id),
          dataType: "JSON",
          success: function (successResponse) {
            popUpMessage("center", successResponse.msg, successResponse.status);
            if (successResponse.status == "success") {
              setTimeout(function () {
                window.location.reload();
              }, 1500);
            }
          },
          error: function (errorResponse) {
            popUpMessage(
              "center",
              errorResponse.responseJSON.msg,
              errorResponse.responseJSON.status
            );
          },
        });
      }
    });
  });

  // Transfer block

  // Add new supplier

  $("#transferBlock").click(function (e) {
    e.preventDefault();
    $("#transferBlock").hide();
    var newWareHouseId = $("#newWareHouseId").val();
    var reason = $("#reason").val();
    console.log(`Reason: `, reason);

    if (newWareHouseId == "select") {
      newWareHouseId = "";
    }

    submitRequest = false;
    msg = "Required input is missing";

    if (newWareHouseId != "") {
      if (reason != "") {
        submitRequest = true;
      } else {
        msg = "Kindly enter reason for transfer";
      }
    } else {
      msg = "Kindly select new warehouse location";
    }

    if (!submitRequest) {
      popUpMessage("center", msg, "warning");
      $("#transferBlock").show();
    } else {
      var formData = new FormData(document.getElementById("blockTransferForm"));

      console.log(`formData`, formData);

      $.ajax({
        type: "POST",
        url: "/api/block/transfer/",
        dataType: "JSON",
        data: formData,
        processData: false,
        contentType: false,
        encrypt: "multipart/form-data",
        success: function (successResponse) {
          $("#transferBlock").show();
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              const blockId = btoa(successResponse.id);
              window.location.href = `/dashboard/inventory/list/`;
            }, 1500);
          }
        },
        error: function (errorResponse) {
          $("#transferBlock").show();
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

  $("#approveRequestBtn").click(function (e) {
    $("#approveRequestBtn").hide();
    e.preventDefault();
    var requestId = $(this).attr("data-id");

    var processRequest = false;
    if (requestId != "") {
      processRequest = true;
    } else {
      msg = "Request id is required";
    }

    if (processRequest) {
      $.ajax({
        type: "GET",
        url: "/api/request/approve/?id=" + requestId,
        dataType: "JSON",
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/request/list/`;
            }, 1500);
          }
          $("#approveRequestBtn").show();
        },
        error: function (errorResponse) {
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
          $("#approveRequestBtn").show();
        },
      });
    } else {
      popUpMessage("center", msg, "warning");
    }
  });

  $("#applyInventoryFilter").click(function (e) {
    e.preventDefault();
    var warehouseFilter = $("#warehouseFilter").val();
    var typeFilter = $("#typeFilter").val();
    var statusFilter = $("#statusFilter").val();
    if (warehouseFilter != "#" || typeFilter != "#" || statusFilter != "#") {
      var currentUrl = `${window.location.origin}${window.location.pathname}`;
      var redirectTo = `${currentUrl}?warehouse=${warehouseFilter}&product_type=${typeFilter}&status=${statusFilter}`;
      window.location.href = redirectTo;
    } else {
      popUpMessage(
        "center",
        "Kinldy select minimum 1 filter conditions",
        "warning"
      );
    }
  });

  // Show inventory print

  $("#printInventory").change(function () {
    var selectedValue = $(this).val();

    // Check if the value is not empty or "#"
    if (selectedValue && selectedValue !== "#") {
      var printInventoryURL =
        "/print_stock/?inventoryType=" + encodeURI(selectedValue);
      window.open(printInventoryURL, "_blank");
      $(this).val("#");
    }
  });
  $(".showInventoryTableList").DataTable({
    pageLength: 50,
    fixedHeader: true,
    responsive: false,
    searching: true,
    lengthChange: false,
    autoWidth: false,
    ordering: true,
    scrollX: true,
    scrollCollapse: true,
    columnDefs: [
      {
        width: "200px",
        targets: -1,
      },
      {
        targets: "_all",
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).css({
            "white-space": "normal",
            "word-wrap": "break-word",
          });
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      var rowDataStatus = data[data.length - 2].toLowerCase().replace(" ", "_");
      var colors = {
        draft: "#f9e29f",
        initiate: "#f5f5f5",
        active: "#b4e2b4",
        approved: "#b4e2b4",
        sold_out: "#b87085",
        success: "#A9D5D1",
        duplicate: "#C8C8F0",
        follow_up: "#E6AF60",
        payment_initiate: "#B3E6F0",
        payment_refund: "#F6B0DB",
        failed: "#F8B3B3",
        soft_delete: "#F5B0B0",
        delete: "#F8A6A6",
      };

      var color = colors[rowDataStatus] || "#D1D2D4";
      $(row).css("background-color", color);
      $(row)
        .find("td")
        .css("background-color", color + " !important");
    },
    fixedColumns: {
      leftColumns: 2,
      rightColumns: 1,
    },
  });

  // Aerial adjustment
  let $aerialValue = $("#aerialValue");
  let $aerialAdjustment = $("#aerialAdjustment");
  let $salesRequestNetValue = $("#salesRequestNetValue");

  let originalValue = parseFloat($salesRequestNetValue.text()) || 0; // Store initial value

  function updateNetValue() {
    let aerialValue = parseFloat($aerialValue.val()) || 0;

    // Ensure the value never goes below 0
    if (aerialValue < 0) {
      aerialValue = 0;
      $aerialValue.val(0);
    }

    let adjustmentType = $aerialAdjustment.val();
    let newValue = originalValue; // Reset to original value

    if (adjustmentType === "increase") {
      newValue += aerialValue;
    } else if (adjustmentType === "decrease") {
      newValue -= aerialValue;
    }

    $salesRequestNetValue.text(newValue.toFixed(2)); // Update with 2 decimal places
  }

  $aerialValue.on("input", updateNetValue);
  $aerialAdjustment.on("change", updateNetValue);
});
