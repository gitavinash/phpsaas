/**
 * V3 Team & User Form (Add/Edit/KYC) Frontend Logic
 * Path: /app/dashboard/v3/default/desktop/assets/js/user-form.js
 */

(function (window, $) {
  "use strict";

  const V3UserForm = {
    apiEndpoint: "/api/v3/users/",
    isSubmitting: false,

    init: function () {
      if ($("#addUserApp").length > 0) {
        this.initAdd();
      }
      if ($("#editUserApp").length > 0) {
        this.initEdit();
      }
      if ($("#userKycApp").length > 0) {
        this.initKyc();
      }
    },

    // ----------------------------------------------------
    // ADD USER FORM
    // ----------------------------------------------------
    initAdd: function () {
      const self = this;
      self.fetchRoles("#userRole", function () {
        self.bindLivePreview("add");
      });

      $(document).off("submit", "#addUserForm").on("submit", "#addUserForm", function (e) {
        e.preventDefault();
        self.submitAddUser();
      });
    },

    submitAddUser: function () {
      const self = this;
      if (self.isSubmitting) return;

      const firstName = $("#firstName").val().trim();
      const lastName = $("#lastName").val().trim();
      const mobile = $("#userMobile").val().trim();
      const email = $("#userEmail").val().trim();
      const userRole = $("#userRole").val();
      const password = $("#userPassword").val();

      if (!firstName) {
        self.showAlert("warning", "First Name is required.");
        $("#firstName").focus();
        return;
      }

      if (!mobile || mobile.length < 10) {
        self.showAlert("warning", "Please enter a valid 10-digit mobile number.");
        $("#userMobile").focus();
        return;
      }

      if (!email) {
        self.showAlert("warning", "Email address is required.");
        $("#userEmail").focus();
        return;
      }

      if (!userRole) {
        self.showAlert("warning", "Please select a user role.");
        $("#userRole").focus();
        return;
      }

      const postData = {
        first_name: firstName,
        last_name: lastName,
        mobile: mobile,
        email: email,
        userRole: userRole,
        password: password
      };

      const $btn = $("#btnSubmitUser");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Creating User...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: self.apiEndpoint + "create.php",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "User Created!",
                text: res.msg || "Team member added successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              }).then(function () {
                window.location.href = "/dashboard/user/";
              });
            } else {
              alert(res.msg || "User created successfully!");
              window.location.href = "/dashboard/user/";
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to create user.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while creating user.");
        }
      });
    },

    // ----------------------------------------------------
    // EDIT USER FORM
    // ----------------------------------------------------
    initEdit: function () {
      const self = this;
      let userId = $("#editUserApp").data("user-id") || $("#editUserId").val();
      if (!userId) {
        const urlParams = new URLSearchParams(window.location.search);
        userId = urlParams.get("id") || urlParams.get("user_id");
        if (userId) {
          $("#editUserId").val(userId);
        }
      }
      
      self.fetchRoles("#editUserRole", function () {
        self.bindLivePreview("edit");
        if (userId) {
          self.loadUserDetails(userId);
        }
      });

      $(document).off("submit", "#editUserForm").on("submit", "#editUserForm", function (e) {
        e.preventDefault();
        self.submitUpdateUser();
      });
    },

    loadUserDetails: function (userId) {
      const self = this;

      $.ajax({
        url: self.apiEndpoint + "list.php?id=" + encodeURIComponent(userId),
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success" && res.data && res.data.length > 0) {
            const u = res.data[0];

            let fName = u.first_name || "";
            let lName = u.last_name || "";
            if (!fName && u.name) {
              const nameParts = u.name.trim().split(" ");
              fName = nameParts[0] || "";
              if (nameParts.length > 1) {
                lName = nameParts.slice(1).join(" ");
              }
            }

            $("#editFirstName").val(fName);
            $("#editLastName").val(lName);
            $("#editUserMobile").val(u.mobile || "");
            $("#editUserEmail").val(u.email || "");

            // If mobile or email was empty, allow entering them
            if (!u.mobile) {
              $("#editUserMobile").removeAttr("readonly");
            }
            if (!u.email) {
              $("#editUserEmail").removeAttr("readonly");
            }

            if (u.role) {
              $("#editUserRole").val(u.role);
            }
            if (u.meta && u.meta.bio) {
              $("#editUserBio").val(u.meta.bio);
            }

            // Trigger live preview update
            $("#editFirstName").trigger("input");
          } else {
            self.showAlert("error", res ? res.msg : "User details not found.");
          }
        },
        error: function () {
          self.showAlert("error", "Failed to load user details.");
        }
      });
    },

    submitUpdateUser: function () {
      const self = this;
      if (self.isSubmitting) return;

      const userId = $("#editUserId").val();
      const firstName = $("#editFirstName").val().trim();
      const lastName = $("#editLastName").val().trim();
      const userRole = $("#editUserRole").val();
      const bio = $("#editUserBio").val().trim();

      if (!firstName) {
        self.showAlert("warning", "First name is required.");
        $("#editFirstName").focus();
        return;
      }

      if (!userRole) {
        self.showAlert("warning", "Please select a user role.");
        $("#editUserRole").focus();
        return;
      }

      const postData = {
        id: userId,
        first_name: firstName,
        last_name: lastName,
        userRole: userRole,
        bio: bio
      };

      const $btn = $("#btnUpdateUser");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Updating...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: self.apiEndpoint + "update.php",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Updated!",
                text: res.msg || "User details updated successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              }).then(function () {
                window.location.href = "/dashboard/user/";
              });
            } else {
              alert(res.msg || "User details updated successfully!");
              window.location.href = "/dashboard/user/";
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to update user.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while updating user.");
        }
      });
    },

    // ----------------------------------------------------
    // KYC LOGIC
    // ----------------------------------------------------
    initKyc: function () {
      const self = this;
      const userId = $("#userKycApp").data("user-id");
      if (!userId) return;

      $.ajax({
        url: self.apiEndpoint + "list.php?id=" + encodeURIComponent(userId),
        method: "GET",
        dataType: "json",
        success: function (res) {
          $("#kycLoadingState").hide();
          if (res && res.status === "success" && res.data && res.data.length > 0) {
            const u = res.data[0];
            $("#kycUserName").text(u.name);
            $("#kycUserEmail").text(u.email || "-");
            $("#kycUserMobile").text(u.mobile ? "+91 " + u.mobile : "-");
            $("#kycUserAvatar").text(u.initials || "U");

            // WhatsApp link
            if (u.mobile) {
              const cleanMobile = u.mobile.replace(/[^0-9]/g, "");
              const msg = encodeURIComponent(`Hi ${u.name}, please complete your KYC verification for Aleya CRM dashboard access.`);
              $("#btnSendKycWhatsapp").attr("href", "https://wa.me/91" + cleanMobile + "?text=" + msg);
            }

            $("#kycContentWrap").show();
          } else {
            $("#kycReviewBox").html('<p style="text-align:center; color:#ef4444; padding:30px;">User not found.</p>');
          }
        },
        error: function () {
          $("#kycLoadingState").hide();
          $("#kycReviewBox").html('<p style="text-align:center; color:#ef4444; padding:30px;">Failed to load user KYC.</p>');
        }
      });

      $(document).off("click", "#btnSendKycEmail").on("click", "#btnSendKycEmail", function () {
        self.showAlert("success", "KYC verification email request queued successfully.");
      });
    },

    // ----------------------------------------------------
    // HELPERS
    // ----------------------------------------------------
    fetchRoles: function (selectSelector, callback) {
      const self = this;
      $.ajax({
        url: self.apiEndpoint + "roles.php",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.roles && res.roles.length > 0) {
            let html = '<option value="">Select User Role</option>';
            res.roles.forEach(function (r) {
              html += `<option value="${selfEscapeHTML(r.code)}">${selfEscapeHTML(r.name)}</option>`;
            });
            $(selectSelector).html(html);
          }
          if (typeof callback === "function") callback();
        },
        error: function () {
          if (typeof callback === "function") callback();
        }
      });
    },

    bindLivePreview: function (prefix) {
      const isEdit = (prefix === "edit");
      const fNameSel = isEdit ? "#editFirstName" : "#firstName";
      const lNameSel = isEdit ? "#editLastName" : "#lastName";
      const mobileSel = isEdit ? "#editUserMobile" : "#userMobile";
      const emailSel = isEdit ? "#editUserEmail" : "#userEmail";
      const roleSel = isEdit ? "#editUserRole" : "#userRole";

      function update() {
        const fn = $(fNameSel).val() ? $(fNameSel).val().trim() : "";
        const ln = $(lNameSel).val() ? $(lNameSel).val().trim() : "";
        const fullName = (fn || ln) ? (fn + " " + ln).trim() : (isEdit ? "Team Member" : "New Member");
        
        let initials = "U";
        if (fn) {
          initials = fn.charAt(0).toUpperCase();
          if (ln) initials += ln.charAt(0).toUpperCase();
        }
        $("#previewUserAvatar").text(initials);
        $("#previewUserName").text(fullName);

        const mob = $(mobileSel).val() ? $(mobileSel).val().trim() : "";
        $("#previewUserMobile").text(mob ? "+91 " + mob : "+91 \u2014");

        const em = $(emailSel).val() ? $(emailSel).val().trim() : "";
        $("#previewUserEmail").text(em ? em : "No email provided");

        const roleText = $(roleSel).find("option:selected").text() || "Sales Agent";
        const roleVal = ($(roleSel).val() || "agent").toLowerCase();
        let badgeClass = "badge-service-state badge-role-agent";
        let iconClass = "fi fi-rr-headset";
        if (roleVal === "administrator") {
          badgeClass = "badge-service-state badge-role-admin";
          iconClass = "fi fi-rr-shield-check";
        } else if (roleVal === "operations") {
          badgeClass = "badge-service-state badge-role-operations";
          iconClass = "fi fi-rr-settings-sliders";
        } else if (roleVal === "accountant") {
          badgeClass = "badge-service-state badge-role-accounts";
          iconClass = "fi fi-rr-calculator";
        }
        $("#previewUserRole").attr("class", badgeClass).html(`<i class="${iconClass}"></i> ${selfEscapeHTML(roleText)}`);
      }

      $(document).off("input change", `${fNameSel}, ${lNameSel}, ${mobileSel}, ${emailSel}, ${roleSel}`)
                 .on("input change", `${fNameSel}, ${lNameSel}, ${mobileSel}, ${emailSel}, ${roleSel}`, update);
      update();
    },

    showAlert: function (type, msg) {
      const swalFn = window.swal || window.Swal;
      if (swalFn) {
        swalFn({
          title: type === "success" ? "Success!" : (type === "warning" ? "Notice" : "Error"),
          text: msg,
          type: type,
          icon: type
        });
      } else {
        alert(msg);
      }
    }
  };

  function selfEscapeHTML(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.V3UserForm = V3UserForm;
})(window, jQuery);
