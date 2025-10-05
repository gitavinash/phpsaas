$(document).ready(function () {
  let redeemVisit = 0;
  // ===== Resend OTP timer =====
  let resendTimerId = null;
  let resendRemaining = 0;

  // Helper to set label for both <input type="button"> and <button>
  function setBtnLabel($btn, label) {
    if ($btn.is('input[type="button"], input[type="submit"]')) {
      $btn.val(label);
    } else {
      $btn.text(label);
    }
  }

  // Start/Restart 30s countdown: always visible, disabled, with live label
  function startResendTimer(seconds) {
    const $btn = $("#resendOTPBtn");
    clearInterval(resendTimerId);
    resendRemaining = seconds;

    $btn.show().prop("disabled", true);
    setBtnLabel($btn, `Resend OTP in ${resendRemaining} Seconds`);

    resendTimerId = setInterval(() => {
      resendRemaining -= 1;

      if (resendRemaining <= 0) {
        clearInterval(resendTimerId);
        resendTimerId = null;
        $btn.prop("disabled", false);
        setBtnLabel($btn, "Resend OTP");
        return;
      }

      setBtnLabel($btn, `Resend OTP in ${resendRemaining} Seconds`);
    }, 1000);
  }

  // Call this when you enter Step 4 the first time
  function onEnterStep4StartResendClock() {
    startResendTimer(30); // first 30s disabled countdown
  }

  // Optional: clear timer when leaving Step 4
  function clearResendTimer() {
    if (resendTimerId) {
      clearInterval(resendTimerId);
      resendTimerId = null;
    }
  }

  // ===== Resend OTP click =====
  $(document).on("click", "#resendOTPBtn", function () {
    const $btn = $(this);
    const otpRefId = ($("#otpRefId").val() || "").trim();
    const url = $btn.data("url") || "/api/auth/resend-otp/"; // your endpoint

    // Reset value

    $("#otpValue").val("");

    if (!otpRefId) {
      popUpMessage("center", "Missing OTP reference.", "error");
      return;
    }

    $btn.prop("disabled", true);
    $("#loader").show();
    $(".step_4_box").hide();

    $.ajax({
      url: url,
      type: "GET", // keep GET as per your API
      dataType: "json",
      data: { otp_ref_id: otpRefId, otp_mode: "sms" },
      success: function (resp) {
        if (resp.status === "success") {
          popUpMessage(
            "center",
            resp.msg || "OTP resent successfully.",
            "success"
          );
          // Start a fresh 30s disabled countdown
          startResendTimer(30);
          $("#loader").hide();
          $(".step_4_box").show();
        } else {
          popUpMessage("center", resp.msg || "Failed to resend OTP.", "error");
          // Allow immediate retry on failure
          $("#resendOTPBtn").prop("disabled", false);
          setBtnLabel($("#resendOTPBtn"), "Resend OTP");
          $("#loader").hide();
          $(".step_4_box").show();
        }
      },
      error: function (xhr) {
        popUpMessage(
          "center",
          (xhr.responseJSON && xhr.responseJSON.msg) || "Request failed.",
          (xhr.responseJSON && xhr.responseJSON.status) || "error"
        );
        $("#resendOTPBtn").prop("disabled", false);
        setBtnLabel($("#resendOTPBtn"), "Resend OTP");
        $("#loader").hide();
        $(".step_4_box").show();
      },
      complete: function () {
        $("#loader").hide();
        $(".step_4_box").show();
      },
    });
  });

  $("#addMembership").on("click", function (e) {
    e.preventDefault();
    $("#addMembership").hide();
    // Get values
    var name = $("#name").val().trim();
    var warehouseId = $("#warehouseId").val().trim();
    var mobile = $("#mobile").val().trim();
    var packageType = $("#package").val();
    var price = $("#price").val().trim();
    var dueAmount = $("#dueAmount").val().trim();
    var visit = $("#visit").val().trim();
    var avlVisit = $("#avlVisit").val().trim();
    var requirements = $("#requirements").val().trim();

    // ===== VALIDATION =====
    if (name === "") {
      popUpMessage("center", "Please enter customer name", "warning");
      $("#addMembership").show();
      return;
    }

    if (mobile === "" || !/^[0-9]{10}$/.test(mobile)) {
      popUpMessage(
        "center",
        "Please enter a valid 10-digit mobile number",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (packageType === "") {
      popUpMessage("center", "Please select a room type", "warning");
      $("#addMembership").show();
      return;
    }

    if (price === "" || parseFloat(price) <= 0) {
      popUpMessage(
        "center",
        "Please enter a valid price greater than 0",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (dueAmount === "" || parseFloat(dueAmount) < 0) {
      popUpMessage("center", "Due amount cannot be negative", "warning");
      $("#addMembership").show();
      return;
    }

    if (parseFloat(dueAmount) > parseFloat(price)) {
      popUpMessage(
        "center",
        "Due amount cannot exceed the total price",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (visit === "" || parseInt(visit) < 1) {
      popUpMessage("center", "Total visit must be at least 1", "warning");
      $("#addMembership").show();
      return;
    }

    if (avlVisit === "" || parseInt(avlVisit) < 1) {
      popUpMessage(
        "center",
        "Total available visit must be at least 1",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    $.ajax({
      url: "/api/membership/add/",
      type: "POST",
      data: {
        warehouseId: warehouseId,
        name: name,
        mobile: mobile,
        package: packageType,
        price: price,
        dueAmount: dueAmount,
        visit: visit,
        avlVisit: avlVisit,
        requirements: requirements,
      },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          // Reset form after success
          $("#addNewMembershipForm")[0].reset();
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "success"
          );
          window.location.href = "/dashboard/membership/";
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $("#addMembership").show();
        }
      },
      error: function (xhr) {
        $("#addMembership").show();
        var errorMsg = "An error occurred while saving membership.";
        if (xhr.responseJSON && xhr.responseJSON.msg) {
          errorMsg = xhr.responseJSON.msg;
        }
        popUpMessage("center", errorMsg, "error");
      },
    });
  });

  $("#updateMembership").on("click", function (e) {
    e.preventDefault();
    $("#updateMembership").hide();
    // Get values
    var name = $("#name").val().trim();
    var warehouseId = $("#warehouseId").val().trim();
    var membershipId = $("#membershipId").val().trim();
    var packageType = $("#package").val();
    var price = $("#price").val().trim();
    var dueAmount = $("#dueAmount").val().trim();
    var visit = $("#visit").val().trim();
    var avlVisit = $("#avlVisit").val().trim();
    var requirements = $("#requirements").val().trim();

    // ===== VALIDATION =====
    if (name === "") {
      popUpMessage("center", "Please enter customer name", "warning");
      $("#updateMembership").show();
      return;
    }

    if (packageType === "") {
      popUpMessage("center", "Please select a room type", "warning");
      $("#updateMembership").show();
      return;
    }

    if (price === "" || parseFloat(price) <= 0) {
      popUpMessage(
        "center",
        "Please enter a valid price greater than 0",
        "warning"
      );
      $("#updateMembership").show();
      return;
    }

    if (dueAmount === "" || parseFloat(dueAmount) < 0) {
      popUpMessage("center", "Due amount cannot be negative", "warning");
      $("#updateMembership").show();
      return;
    }

    if (parseFloat(dueAmount) > parseFloat(price)) {
      popUpMessage(
        "center",
        "Due amount cannot exceed the total price",
        "warning"
      );
      $("#updateMembership").show();
      return;
    }

    if (visit === "" || parseInt(visit) < 1) {
      popUpMessage("center", "Total visit must be at least 1", "warning");
      $("#updateMembership").show();
      return;
    }

    if (avlVisit === "" || parseInt(avlVisit) > parseInt(visit)) {
      popUpMessage(
        "center",
        "Total available visit must be at below than Total Visits",
        "warning"
      );
      $("#updateMembership").show();
      return;
    }

    $.ajax({
      url: "/api/membership/update/",
      type: "POST",
      data: {
        warehouseId: warehouseId,
        membershipId: membershipId,
        name: name,
        package: packageType,
        price: price,
        dueAmount: dueAmount,
        visit: visit,
        avlVisit: avlVisit,
        requirements: requirements,
      },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          // Reset form after success
          $("#updateMembershipForm")[0].reset();
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "success"
          );
          window.location.href = "/dashboard/membership/member/";
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $("#updateMembership").show();
        }
      },
      error: function (xhr) {
        $("#updateMembership").show();
        var errorMsg = "An error occurred while saving membership.";
        if (xhr.responseJSON && xhr.responseJSON.msg) {
          errorMsg = xhr.responseJSON.msg;
        }
        popUpMessage("center", errorMsg, "error");
      },
    });
  });

  $(".backToStep1").on("click", function (e) {
    e.preventDefault();
    $(".step_1_box").show();
    $(".step_2_box").hide();
    $(".step_3_box").hide();
    $(".step_4_box").hide();
    $(".step_5_box").hide();
  });

  $(".backToStep2").on("click", function (e) {
    e.preventDefault();
    $(".step_2_box").show();
    $(".step_1_box").hide();
    $(".step_3_box").hide();
    $(".step_4_box").hide();
    $(".step_5_box").hide();
  });

  $(".backToStep3").on("click", function (e) {
    e.preventDefault();
    $(".step_3_box").show();
    $(".step_1_box").hide();
    $(".step_2_box").hide();
    $(".step_4_box").hide();
    $(".step_5_box").hide();
  });

  $("#checkInMembership").on("click", function (e) {
    e.preventDefault();

    var $btn = $("#checkInMembership");
    var $loader = $("#loader");
    $(".step_1_box").hide();
    $btn.hide();
    $loader.show();

    // Get values
    redeemVisit = $("#visits").val().trim();
    var mobile = $("#mobile").val().trim();
    var warehouseId = $("#warehouseId").val().trim();

    // Reset value

    $("#mobile").val("");

    // ===== VALIDATION =====
    if (redeemVisit === "") {
      popUpMessage("center", "Please enter customer's visits", "warning");
      $btn.show();
      $loader.hide();
      $(".step_1_box").show();
      return;
    }

    if (mobile === "" || !/^[0-9]{10}$/.test(mobile)) {
      popUpMessage(
        "center",
        "Please enter a valid 10-digit mobile number",
        "warning"
      );
      $btn.show();
      $(".step_1_box").show();
      $loader.hide();
      return;
    }

    $.ajax({
      url: "/api/membership/check-in/",
      type: "POST",
      data: { warehouseId: warehouseId, mobile: mobile },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          var $step2 = $(".showMembershipList");
          $step2.empty();

          // parse requested visits once
          var redeemQty = parseInt(redeemVisit, 10) || 0;

          $.each(response.data, function (index, item) {
            // Red strip (amount only, large)
            var dueStrip = "";
            var due = parseFloat(item.membership_amount_due || 0);
            if (due > 0) {
              dueStrip = `
        <div class="membership-due-strip">
          <div style="font-size:13px;opacity:0.9;">Amount Due</div>
          <div style="font-size:24px; line-height:1.1; font-weight:800;">₹ ${due}</div>
        </div>
      `;
            }
            adminEditBtn = "";
            if (siteAdminType == "administrator") {
              encryptMembershipId = btoa(item.membership_id);
              adminEditBtn = `
              <div class="action-item">
              <a href='/dashboard/membership/update/?id=${encryptMembershipId}' class="btn btn-blue dataTableBtn"
                       title="Edit membership">
                <i class="fi-rr-pencil"></i> Edit
              </a>
            </div>
              `;
            }

            var startDate = item.membership_created
              ? item.membership_created.split(" ")[0]
              : "—";
            var comment = item.membership_comment || "";
            var packageName = item.packageName || "Normal";

            // --- NEW: enable/disable Continue based on available visits ---
            var avl = parseInt(item.membership_avl_visit, 10) || 0;
            var canContinue = redeemQty > 0 && redeemQty <= avl;

            // Optional: show a small warning if insufficient visits
            var visitsWarn = "";
            if (!canContinue) {
              visitsWarn = `
        <div class='membership-not-redeem'>
          Insufficient visits. Requested: <strong>${redeemQty}</strong>
        </div>
      `;
            }
            //       var visitsWarn = "";
            //       if (!canContinue) {
            //         visitsWarn = `
            //   <div class='membership-not-redeem'>
            //     Requested: <strong>${redeemQty}</strong>, Available: <strong>${avl}</strong>
            //   </div>
            // `;
            //       }

            //         // Build Continue button (disabled if not allowed)
            //         var continueBtn = `
            //   <button class="btn btn-green dataTableBtn continueButton"
            //           data-id="${item.membership_id}" title="${
            //           canContinue ? "Continue" : "Insufficient available visits"
            //         }"
            //           ${canContinue ? "" : "disabled aria-disabled='true'"} >
            //     <i class="fi-rr-arrow-right"></i> Continue
            //   </button>
            // `;

            // If you prefer to HIDE the button instead of disabling it, replace above with:
            var continueBtn = canContinue
              ? `
              <button class="btn btn-green dataTableBtn continueButton"
                      data-id="${item.membership_id}" title="Continue">
                <i class="fi-rr-arrow-right"></i> Continue
              </button>`
              : "";

            var cardHtml = `
      <article class="widget-setting-info membership-card" data-id="${item.membership_id}">
        <div class="about-feature" style="max-width:100%;">
          <h3 class="heading name-cap" style="margin:0;">
            ${item.membership_profile_name}
            <span>Room: ${packageName}</span>
          </h3>

          <hr>

          <div class="row">
            <div class="box-12 info-stack">
              <p style="margin:0;">
                <strong>Mobile</strong><br>
                <span>${mobile}</span>
              </p>
              <p style="margin:0;">
                <strong>Total Visits</strong><br>
                <span>${item.membership_total_visit}</span>
              </p>
              <p style="margin:0;">
                <strong>Avl. Visits</strong><br>
                <span>${avl}</span>
              </p>
            </div>
          </div>

          <div class="row">
            <div class="box-12 info-stack">
              <p style="margin:0;">
                <strong>Start Date</strong><br>
                <span>${startDate}</span>
              </p>
            </div>
            <div class="box-12 info-stack">
              <p style="margin:0;">
                <strong>Comment</strong><br>
                <span>${comment}</span>
              </p>
            </div>
          </div>

          ${dueStrip}
          ${visitsWarn}

          <!-- Actions -->
          <div class="action-stack" style="margin-top:10px;">
            <div class="action-item">
              ${continueBtn}
            </div>
            ${adminEditBtn}
          </div>
        </div>
      </article>
    `;

            $step2.append(cardHtml);
          });

          $(".step_1_box").hide();
          $(".step_2_box").show();
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $(".step_1_box").show();
        }
      },
      error: function (xhr) {
        popUpMessage(
          "center",
          (xhr.responseJSON && xhr.responseJSON.msg) || "Request failed",
          (xhr.responseJSON && xhr.responseJSON.status) || "error"
        );
        $(".step_1_box").show();
      },
      complete: function () {
        $btn.show();
        $loader.hide();
      },
    });
  });

  // Handle Continue button → Step 3
  // helper: 1st, 2nd, 3rd...
  function ordinal(n) {
    const s = ["th", "st", "nd", "rd"],
      v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
  }

  // build duration fields inside .allotRoomAndStaff
  function renderDurationFields(visitsCount) {
    const $step3 = $(".step_3_box");
    const $box = $step3.find(".allotRoomAndStaff");

    // default from existing input (if present), else 60
    const defaultDuration = Number($step3.find("#duration").val()) || 60;

    // clear previous clones (keep area clean)
    $box.empty();

    // generate visit blocks
    for (let i = 1; i <= visitsCount; i++) {
      const id = `duration_${i}`;
      const block = `
      <div class="duration-block cloned-duration" data-visit="${i}">
        <p class="formBoxHeading">Select for ${ordinal(i)} Visit</p>
        <div class="form-group">
          <input id="${id}" name="duration[]" value="${defaultDuration}" type="number" min="1" required />
          <label class="form-lable" for="${id}">Massage duration?<sup>*</sup></label>
        </div>
      </div>
    `;
      $box.append(block);
    }
  }

  $(document).on("click", ".continueButton", function (e) {
    e.preventDefault();
    $("#loader").show();
    const membershipId = String($(this).data("id") || "");
    // get visit count from your global or the input; coerce to a safe int
    const raw =
      typeof redeemVisit !== "undefined" && redeemVisit
        ? redeemVisit
        : $("#visits").val().trim();
    const v = Math.floor(Number(raw));
    const visitsCount = Number.isFinite(v) && v > 0 ? v : 1;

    const $step3 = $(".step_3_box");

    // assign to hidden inputs
    $step3.find('input[name="membershipId"]').val(membershipId);
    $step3.find('input[name="visits"]').val(visitsCount);

    // render duration fields for each visit
    renderDurationFields(visitsCount);

    // move to step 3
    $(".step_2_box").hide();
    $("#loader").hide();
    $step3.show();

    // focus first duration input & scroll
    const $first = $step3.find('input[id^="duration_"]').first();
    $first
      .trigger("focus")[0]
      ?.scrollIntoView({ behavior: "smooth", block: "start" });
  });

  $("#startRedeemVisit").click(function (e) {
    e.preventDefault();
    const $step3 = $(".step_3_box");
    const $step4 = $(".step_4_box");
    $("#loader").show();
    $(".step_3_box").hide();
    const membershipId = (
      $step3.find('input[name="membershipId"]').val() || ""
    ).trim();
    const visitsVal = $step3.find('input[name="visits"]').val();
    const visits = parseInt(visitsVal, 10);

    // collect durations
    const durations = [];
    let invalid = false;

    // clear previous invalid styles
    $step3.find('input[name="duration[]"]').removeClass("is-invalid");

    $step3.find('input[name="duration[]"]').each(function () {
      const v = parseInt($(this).val(), 10);
      if (!Number.isFinite(v) || v <= 0) {
        invalid = true;
        $(this).addClass("is-invalid");
      } else {
        durations.push(v);
      }
    });

    // basic validation
    if (!membershipId) {
      popUpMessage("center", "Missing membership ID.", "error");
      $("#loader").hide();
      $(".step_3_box").show();
      return;
    }
    if (!Number.isInteger(visits) || visits <= 0) {
      popUpMessage("center", "Invalid visits count.", "warning");
      $("#loader").hide();
      $(".step_3_box").show();
      return;
    }
    if (invalid || durations.length !== visits) {
      popUpMessage(
        "center",
        "Please enter duration for all visits.",
        "warning"
      );

      $("#loader").hide();
      $(".step_3_box").show();
      return;
    }

    $.ajax({
      url: "/api/membership/start-redeem/",
      type: "POST",
      data: { membershipId: membershipId, visits: visits, duration: durations },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          popUpMessage("center", response.msg, response.status);
          $('input[name="otpRefId"]').val(response.otpRefId);
          $(".step_3_box").hide();
          $(".step_4_box").show();
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $(".step_3_box").show();
          $(".step_4_box").hide();
        }
        $("#loader").hide();
      },
      error: function (xhr) {
        popUpMessage(
          "center",
          (xhr.responseJSON && xhr.responseJSON.msg) || "Request failed",
          (xhr.responseJSON && xhr.responseJSON.status) || "error"
        );
        $(".step_3_box").show();
        $("#loader").hide();
      },
    });
  });

  $("#redeemVisit").click(function (e) {
    e.preventDefault();
    $("#loader").show();
    $(".step_4_box").hide();

    var otpRefId = $("#otpRefId").val().trim();
    var otpValue = $("#otpValue").val().trim();

    // Reset value

    $("#otpValue").val("");

    if (otpRefId === "") {
      popUpMessage("center", "otpRefId is missing", "error");
      $("#loader").hide();
      $(".step_4_box").show();
      return;
    }

    if (otpValue === "") {
      popUpMessage("center", "Please enter a OTP code", "warning");
      $("#loader").hide();
      $(".step_4_box").show();
      return;
    }

    $.ajax({
      url: "/api/membership/redeem/",
      type: "POST",
      data: { otpRefId: otpRefId, otpCode: otpValue },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          // popUpMessage("center", response.msg, response.status);
          $('input[name="otpRefId"]').val(response.otpRefId);
          $(".step_4_box").hide();
          $(".step_5_box").show();
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $(".step_4_box").show();
          $(".step_5_box").hide();
        }
        $("#loader").hide();
      },
      error: function (xhr) {
        popUpMessage(
          "center",
          (xhr.responseJSON && xhr.responseJSON.msg) || "Request failed",
          (xhr.responseJSON && xhr.responseJSON.status) || "error"
        );
        $(".step_4_box").show();
        $("#loader").hide();
      },
    });
  });

  $(".addNewRoomType").click(function (e) {
    e.preventDefault();
    $(".popUp").hide();
    document.querySelector(".roomTypeBox").style.display = "flex";
    $(".roomTypeBox").show();
  });

  $(".editRoomType").click(function (e) {
    e.preventDefault();
    $(".popUp").hide();
    updateRoomTypeId = $(this).attr("id");
    roomTypeName = $(this).attr("data_name");
    roomTypeDescription = $(this).attr("data_desc");
    $("#updateRoomType").show();
    $("#editName").val(roomTypeName);
    $("#editDescription").val(roomTypeDescription);
    document.querySelector(".editRoomTypeBox").style.display = "flex";
    $(".editRoomTypeBox").show();
  });

  $("#addRoomType").on("click", function (e) {
    e.preventDefault();
    var name = $("#name").val();
    if (name != "") {
      var description = $("#description").val();
      $.ajax({
        url: "/api/room_type/publish/",
        type: "POST",
        data: {
          name,
          description,
        },
        dataType: "JSON",
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          setTimeout(function () {
            location.reload();
          }, 1500);
        },
        error: function (error, response) {
          popUpMessage(
            "center",
            error.responseJSON.msg,
            error.responseJSON.status
          );
          $("#updateAmenities").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter name of room type", "warning");
    }
  });

  $("#updateRoomType").on("click", function (e) {
    e.preventDefault();
    var name = $("#editName").val();
    if (name != "") {
      var description = $("#editDescription").val();
      $.ajax({
        url: "/api/room_type/update/",
        type: "POST",
        data: {
          id: updateRoomTypeId,
          name,
          description,
        },
        dataType: "JSON",
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          setTimeout(function () {
            location.reload();
          }, 1500);
        },
        error: function (error, response) {
          popUpMessage(
            "center",
            error.responseJSON.msg,
            error.responseJSON.status
          );
          $("#updateAmenities").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter name of room type", "warning");
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
                  msg = "Kindly enter branch mobile";
                }
              } else {
                msg = "Kindly enter branch email";
              }
            } else {
              msg = "Kindly enter branch address pin code";
            }
          } else {
            msg = "Kindly enter branch address city";
          }
        } else {
          msg = "Kindly select state of branch address";
        }
      } else {
        msg = "Kindly enter branch address";
      }
    } else {
      msg = "Kindly enter branch name";
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
                  msg = "Kindly enter branch mobile";
                }
              } else {
                msg = "Kindly enter branch email";
              }
            } else {
              msg = "Kindly enter branch address pin code";
            }
          } else {
            msg = "Kindly enter branch address city";
          }
        } else {
          msg = "Kindly select state of branch address";
        }
      } else {
        msg = "Kindly enter branch address";
      }
    } else {
      msg = "Kindly enter branch name";
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
});
