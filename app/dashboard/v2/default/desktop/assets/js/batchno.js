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
  // Update invertory block slabs
  $("#updateInventoryItem").hide();
  // Store the original value of each input in data-original
  $(".widthValues, .heightValues").each(function () {
    $(this).data("original", $(this).val());
  });

  // On any input change, check for modifications
  $(document).on("input change", ".widthValues, .heightValues", function () {
    let changed = false;

    $(".widthValues, .heightValues").each(function () {
      if ($(this).val() != $(this).data("original")) {
        changed = true;
        return false; // Exit loop early
      }
    });

    if (changed) {
      $("#updateInventoryItem").show();
    } else {
      $("#updateInventoryItem").hide();
    }
  });

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

$("#addInventory").click(function (e) {
  e.preventDefault();
  $("#addInventory").hide();
  var name = $("#name").val();
  var warehouseId = $("#warehouseId").val();
  var itemCode = $("#itemCode").val();
  var totalItem = $("#totalItem").val();
  var productType = $("#productType").val();

  var productCost = $("#productCost").val();
  var productMRP = $("#productMRP").val();
  var sellingPrice = $("#sellingPrice").val();

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
          if (productType != "") {
            submitRequest = true;
          } else {
            msg = "Kindly select product type";
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
    $("#addInventory").show();
  } else {
    var formData = new FormData(document.getElementById("addNewInventoryForm"));
    $.ajax({
      type: "POST",
      url: "/api/inventory/publish/",
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
            window.location.href = `/dashboard/inventory/list/`;
          }, 1500);
        }
        $("#addInventory").show();
      },
      error: function (errorResponse) {
        $("#addInventory").show();
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

// Update block

// Add new supplier

$("#updateInventory").click(function (e) {
  e.preventDefault();
  $("#updateInventory").hide();
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
    $("#updateInventory").show();
  } else {
    var formData = new FormData(document.getElementById("updateInventoryForm"));

    $.ajax({
      type: "POST",
      url: "/api/inventory/update/",
      dataType: "JSON",
      data: formData,
      processData: false,
      contentType: false,
      encrypt: "multipart/form-data",
      success: function (successResponse) {
        $("#updateInventory").show();
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const blockId = btoa(successResponse.id);
            window.location.href = `/dashboard/inventory/update/?id=${blockId}`;
          }, 1500);
        }
      },
      error: function (errorResponse) {
        $("#updateInventory").show();
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

// Category management

var $modal = $("#categoryModal");
var $body = $("body");
var $form = $("#categoryForm");

var $titleEl = $("#categoryModalTitle");
var $idHidden = $("#category_id");
var $nameInput = $("#catTitleInput");
var $parentSel = $("#catParentSelect");
var $statusSel = $("#catStatusSelect");

function openModal() {
  $modal.css("display", "block").attr("aria-hidden", "false");
  $body.addClass("more-open"); // reuse scroll lock utility if present
  setTimeout(function () {
    $nameInput.trigger("focus");
  }, 50);
}

function closeModal() {
  $(".shortcut").show();
  $modal.css("display", "none").attr("aria-hidden", "true");
  $body.removeClass("more-open");
  // Reset form but keep defaults for parent/status
  $form[0].reset();
  $idHidden.val("");
}

function openAdd() {
  $(".shortcut").hide();
  $titleEl.text("Add category");
  $idHidden.val("");
  $nameInput.val("");
  if ($parentSel.length) $parentSel.val("");
  if ($statusSel.length) $statusSel.val("1");
  openModal();
}

function openEdit(b64Id, presetTitle) {
  $titleEl.text("Edit category");
  $idHidden.val(b64Id || "");
  $(".shortcut").hide();
  var resolvedTitle = presetTitle || "";
  if (!resolvedTitle) {
    // Try to read from the same table row if not provided
    var $btn = $('.editCategoryPopUp[data-id="' + (b64Id || "") + '"]').first();
    if ($btn.length) {
      var $row = $btn.closest("tr");
      var $nameCell = $row.find(".cell-title strong").first();
      if ($nameCell.length) {
        resolvedTitle = $.trim($nameCell.text());
      }
    }
  }
  $nameInput.val(resolvedTitle || "");

  // Optionally prefill parent/status if you add data-parent / data-status on the button
  // var parentId = $btn.data('parent'); if (parentId !== undefined) $parentSel.val(parentId);
  // var status   = $btn.data('status'); if (status   !== undefined) $statusSel.val(status);

  openModal();
}

// Openers (event delegation)
$(document).on("click", ".addCategoryPopUp", function (e) {
  e.preventDefault();
  openAdd();
});

$(document).on("click", ".editCategoryPopUp", function (e) {
  e.preventDefault();
  var b64Id = $(this).attr("data-id") || "";
  var title = $(this).attr("data-title") || "";
  openEdit(b64Id, title);
});

// Close buttons
$(document).on("click", "#closeCategoryModal", function (e) {
  e.preventDefault();
  closeModal();
});

// Click outside to close
$modal.on("click", function (e) {
  if (e.target === this) {
    closeModal();
  }
});

// ESC to close
$(document).on("keydown", function (e) {
  if (
    (e.key === "Escape" || e.keyCode === 27) &&
    $modal.attr("aria-hidden") === "false"
  ) {
    closeModal();
  }
});

// Save category

$("#saveCategoryBtn").click(function (e) {
  e.preventDefault();
  $(".actionArea").hide();
  const categoryId = $idHidden.val();
  const categoryTitle = $nameInput.val().trim();
  const categoryParent = $parentSel.val();
  const categoryDescription = $("#catDescriptionInput").val().trim();

  console.log(`categoryId`, categoryId);

  if (categoryTitle != "") {
    // Proceed with the AJAX request
    $.ajax({
      url:
        "/api/inventory/category/" + (categoryId ? "update" : "publish") + "/",
      method: "POST",
      data: {
        categoryID: categoryId,
        title: categoryTitle,
        categoryParentID: categoryParent,
        description: categoryDescription,
        postType: "product",
      },
      success: function (response) {
        $(".actionArea").show();
        popUpMessage("center", response.msg, response.status);
        if (response.status == "success") {
          setTimeout(function () {
            window.location.reload();
          }, 1500);
        }
      },
      error: function (errorResponse) {
        $(".actionArea").show();
        console.log(`Error:`, errorResponse);
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
      },
    });
  } else {
    popUpMessage("center", "Please enter a category title.", "warning");
    $(".actionArea").show();
  }
});
