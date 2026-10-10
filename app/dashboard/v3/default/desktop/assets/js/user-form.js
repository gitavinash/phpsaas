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

      // Check license limit status
      $.ajax({
        url: "/api/v3/users/?limit=1",
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.limit_reached) {
            $("#btnSubmitUser").addClass("d-none");
            $("#btnUpgradeUserLicenseForm").removeClass("d-none");
          } else {
            $("#btnSubmitUser").removeClass("d-none");
            $("#btnUpgradeUserLicenseForm").addClass("d-none");
          }
        }
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
        url: self.apiEndpoint + "create/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const redirectUrl = "/dashboard/user/";
            const successMsg = res.msg || "Team member added successfully.";

            let redirected = false;
            const doRedirect = function () {
              if (!redirected) {
                redirected = true;
                if (typeof window.spaNavigate === "function") {
                  window.spaNavigate(redirectUrl);
                } else {
                  window.location.href = redirectUrl;
                }
              }
            };

            const swalFn = window.swal || window.Swal;
            if (typeof swalFn === "function") {
              const swalRes = swalFn({
                title: "User Created!",
                text: successMsg,
                type: "success",
                icon: "success",
                timer: 1500,
                showConfirmButton: false
              }, function () {
                doRedirect();
              });

              if (swalRes && typeof swalRes.then === "function") {
                swalRes.then(doRedirect);
              }
              setTimeout(doRedirect, 1600);
            } else if (typeof window.popUpMessage === "function") {
              window.popUpMessage("center", successMsg, "success");
              setTimeout(doRedirect, 1200);
            } else {
              self.showAlert("success", successMsg);
              setTimeout(doRedirect, 800);
            }
          } else {
            if (res && res.limit_reached) {
              self.showLicenseLimitAlert(res.msg || "Team member limit reached. Please buy more licenses to add more team members.");
            } else {
              self.showAlert("error", res ? res.msg : "Failed to create user.");
            }
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          const errData = xhr ? xhr.responseJSON : null;
          if (errData && errData.limit_reached) {
            self.showLicenseLimitAlert(errData.msg || "Team member limit reached. Please buy more licenses to add more team members.");
          } else {
            self.showAlert("error", (errData && errData.msg) ? errData.msg : "Network error while creating user.");
          }
        }
      });
    },

    showLicenseLimitAlert: function (msg) {
      const notice = msg || "You have reached your team member license limit. Please contact Aleya to add more team members.";
      if (typeof window.showContactAleyaModal === "function") {
        window.showContactAleyaModal("Team Member Limit Reached", notice);
        return;
      }
      const swalFn = window.swal || window.Swal;
      if (typeof swalFn === "function") {
        swalFn({
          title: "Team Member Limit Reached",
          text: notice,
          type: "warning",
          icon: "warning",
          confirmButtonText: "OK",
          confirmButtonColor: "#2563eb"
        });
      } else {
        this.showAlert("warning", notice);
      }
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
        url: self.apiEndpoint + "list/?id=" + encodeURIComponent(userId),
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
        url: self.apiEndpoint + "update/",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(postData),
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const redirectUrl = "/dashboard/user/";
            const successMsg = res.msg || "User details updated successfully.";

            let redirected = false;
            const doRedirect = function () {
              if (!redirected) {
                redirected = true;
                if (typeof window.spaNavigate === "function") {
                  window.spaNavigate(redirectUrl);
                } else {
                  window.location.href = redirectUrl;
                }
              }
            };

            const swalFn = window.swal || window.Swal;
            if (typeof swalFn === "function") {
              const swalRes = swalFn({
                title: "Updated!",
                text: successMsg,
                type: "success",
                icon: "success",
                timer: 1500,
                showConfirmButton: false
              }, function () {
                doRedirect();
              });

              if (swalRes && typeof swalRes.then === "function") {
                swalRes.then(doRedirect);
              }
              setTimeout(doRedirect, 1600);
            } else if (typeof window.popUpMessage === "function") {
              window.popUpMessage("center", successMsg, "success");
              setTimeout(doRedirect, 1200);
            } else {
              self.showAlert("success", successMsg);
              setTimeout(doRedirect, 800);
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
      let userId = $("#userKycApp").data("user-id") || $("#kycUserId").val();
      if (!userId) {
        const urlParams = new URLSearchParams(window.location.search);
        userId = urlParams.get("id") || urlParams.get("user_id");
        if (userId) {
          $("#kycUserId").val(userId);
        }
      }
      if (!userId) return;

      self.loadKycDetails(userId);

      // Copy Link Button
      $(document).off("click", "#btnCopyKycUrl").on("click", "#btnCopyKycUrl", function () {
        const url = $("#kycPublicUrlInput").val();
        if (url) {
          if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function () {
              self.showAlert("success", "KYC Verification URL copied to clipboard!");
            });
          } else {
            $("#kycPublicUrlInput").select();
            document.execCommand("copy");
            self.showAlert("success", "KYC Verification URL copied to clipboard!");
          }
        }
      });

      // Send Email Reminder Button
      $(document).off("click", "#btnSendKycEmail").on("click", "#btnSendKycEmail", function () {
        const $btn = $(this);
        const origHtml = $btn.html();
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Sending...</span>');

        $.ajax({
          url: self.apiEndpoint + "kyc/",
          method: "POST",
          dataType: "json",
          contentType: "application/json",
          data: JSON.stringify({ id: userId, action: "send_reminder" }),
          success: function (res) {
            $btn.prop("disabled", false).html(origHtml);
            if (res && res.status === "success") {
              self.showAlert("success", res.msg || "KYC reminder email dispatched successfully!");
            } else {
              self.showAlert("error", res ? res.msg : "Failed to dispatch email reminder.");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html(origHtml);
            self.showAlert("error", "Network error while dispatching KYC reminder email.");
          }
        });
      });

      // Upload Documents Form Submit
      $(document).off("submit", "#userKycUploadForm").on("submit", "#userKycUploadForm", function (e) {
        e.preventDefault();
        self.submitKycForm(userId);
      });
    },

    loadKycDetails: function (userId) {
      const self = this;

      $.ajax({
        url: self.apiEndpoint + "kyc/?id=" + encodeURIComponent(userId),
        method: "GET",
        dataType: "json",
        success: function (res) {
          if (res && res.status === "success") {
            const u = res.user || {};
            const kyc = res.kyc;
            const kycUrl = res.kyc_url || "";
            const waUrl = res.whatsapp_url || "#";

            // User Info
            $("#kycUserName").text(u.name || "Team Member");
            $("#kycUserEmail").text(u.email || "No email provided");
            $("#kycUserMobile").text(u.mobile ? "+91 " + u.mobile : "+91 \u2014");

            let initials = "U";
            if (u.first_name) {
              initials = u.first_name.charAt(0).toUpperCase();
              if (u.last_name) initials += u.last_name.charAt(0).toUpperCase();
            } else if (u.name) {
              const parts = u.name.trim().split(" ");
              initials = parts[0].charAt(0).toUpperCase();
              if (parts.length > 1) initials += parts[1].charAt(0).toUpperCase();
            }
            $("#kycUserAvatar").text(initials);

            const role = (u.role || "agent").toLowerCase();
            let badgeClass = "badge-service-state badge-role-agent";
            let iconClass = "fi fi-rr-headset";
            let roleName = "Sales Agent";
            if (role === "administrator") {
              badgeClass = "badge-service-state badge-role-admin";
              iconClass = "fi fi-rr-shield-check";
              roleName = "Administrator";
            } else if (role === "operations") {
              badgeClass = "badge-service-state badge-role-operations";
              iconClass = "fi fi-rr-settings-sliders";
              roleName = "Operations Manager";
            } else if (role === "accountant") {
              badgeClass = "badge-service-state badge-role-accounts";
              iconClass = "fi fi-rr-calculator";
              roleName = "Accounts & Finance";
            }
            $("#kycUserRole").attr("class", badgeClass).html(`<i class="${iconClass}"></i> ${selfEscapeHTML(roleName)}`);

            // URLs
            $("#kycPublicUrlInput").val(kycUrl);
            $("#btnSendKycWhatsapp").attr("href", waUrl);

            // KYC Status Alert Box & Form Fields
            if (kyc) {
              $("#kycAadharNumber").val(kyc.aadhar_number || "");
              $("#kycPanNumber").val(kyc.pan_number || "");
              $("#kycGstNumber").val(kyc.gst_number || "");
              $("#kycStatusSelect").val(kyc.status !== undefined ? String(kyc.status) : "0");
              $("#kycRemarks").val(kyc.aadhar_remark || "");

              // Document links
              if (kyc.aadhar_file) {
                $("#aadharFilePreview").html(`
                  <a href="${selfEscapeHTML(kyc.aadhar_file)}" target="_blank" class="v3-doc-link" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#4f46e5; font-weight:600; text-decoration:none;">
                    <i class="fi fi-rr-file-check" style="color:#10b981;"></i> View Uploaded Aadhaar File &rarr;
                  </a>
                `).show();
              }
              if (kyc.pan_file) {
                $("#panFilePreview").html(`
                  <a href="${selfEscapeHTML(kyc.pan_file)}" target="_blank" class="v3-doc-link" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#4f46e5; font-weight:600; text-decoration:none;">
                    <i class="fi fi-rr-file-check" style="color:#10b981;"></i> View Uploaded PAN File &rarr;
                  </a>
                `).show();
              }
              if (kyc.gst_file) {
                $("#gstFilePreview").html(`
                  <a href="${selfEscapeHTML(kyc.gst_file)}" target="_blank" class="v3-doc-link" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#4f46e5; font-weight:600; text-decoration:none;">
                    <i class="fi fi-rr-file-check" style="color:#10b981;"></i> View Uploaded GST File &rarr;
                  </a>
                `).show();
              }

              if (kyc.status === 1) {
                $("#kycStatusBox").attr("style", "background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:12px; margin-bottom:18px;");
                $("#kycStatusIcon").html('<i class="fi fi-rr-checkbox" style="color:#16a34a; font-size:22px;"></i>');
                $("#kycStatusTitle").attr("style", "color:#166534; font-weight:700; margin:0 0 2px 0; font-size:14px;").text("KYC Verified & Compliant");
                $("#kycStatusDesc").attr("style", "color:#15803d; font-size:12.5px; margin:0;").text("This user has completed verification and identity documents have been approved.");
                $("#kycSidebarBadge").attr("class", "v3-status-badge status-active").html('<span class="status-pulse-dot"></span> Verified');
              } else if (kyc.status === 2) {
                $("#kycStatusBox").attr("style", "background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:12px; margin-bottom:18px;");
                $("#kycStatusIcon").html('<i class="fi fi-rr-cross-circle" style="color:#dc2626; font-size:22px;"></i>');
                $("#kycStatusTitle").attr("style", "color:#991b1b; font-weight:700; margin:0 0 2px 0; font-size:14px;").text("KYC Rejected / Incomplete");
                $("#kycStatusDesc").attr("style", "color:#b91c1c; font-size:12.5px; margin:0;").text("Documents were rejected or require resubmission.");
                $("#kycSidebarBadge").attr("class", "v3-status-badge status-inactive").text("Rejected");
              } else {
                $("#kycStatusBox").attr("style", "background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:12px; margin-bottom:18px;");
                $("#kycStatusIcon").html('<i class="fi fi-rr-info" style="color:#d97706; font-size:22px;"></i>');
                $("#kycStatusTitle").attr("style", "color:#92400e; font-weight:700; margin:0 0 2px 0; font-size:14px;").text("KYC Documents Submitted (Under Review)");
                $("#kycStatusDesc").attr("style", "color:#b45309; font-size:12.5px; margin:0;").text("Documents have been submitted and are awaiting compliance approval.");
                $("#kycSidebarBadge").attr("class", "v3-status-badge status-warning").text("Pending Review");
              }
            } else {
              $("#kycStatusBox").attr("style", "background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:12px; margin-bottom:18px;");
              $("#kycStatusIcon").html('<i class="fi fi-rr-info" style="color:#d97706; font-size:22px;"></i>');
              $("#kycStatusTitle").attr("style", "color:#92400e; font-weight:700; margin:0 0 2px 0; font-size:14px;").text("KYC Documents Pending");
              $("#kycStatusDesc").attr("style", "color:#b45309; font-size:12.5px; margin:0;").text("This user has not yet submitted identification and verification documents.");
              $("#kycSidebarBadge").attr("class", "v3-status-badge status-warning").text("Pending");
            }
          } else {
            self.showAlert("error", res ? res.msg : "Failed to load KYC records.");
          }
        },
        error: function () {
          self.showAlert("error", "Network error while loading KYC records.");
        }
      });
    },

    submitKycForm: function (userId) {
      const self = this;
      if (self.isSubmitting) return;

      const formElement = document.getElementById("userKycUploadForm");
      const formData = new FormData(formElement);

      const $btn = $("#btnSaveKyc");
      const origHtml = $btn.html();
      $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner-alt rotating"></i> <span>Saving Documents...</span>');
      self.isSubmitting = true;

      $.ajax({
        url: self.apiEndpoint + "kyc/",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);

          if (res && res.status === "success") {
            const swalFn = window.swal || window.Swal;
            if (swalFn) {
              swalFn({
                title: "Saved!",
                text: res.msg || "KYC details updated successfully.",
                type: "success",
                icon: "success",
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              alert(res.msg || "KYC details updated successfully.");
            }
            self.loadKycDetails(userId);
          } else {
            self.showAlert("error", res ? res.msg : "Failed to save KYC documents.");
          }
        },
        error: function () {
          self.isSubmitting = false;
          $btn.prop("disabled", false).html(origHtml);
          self.showAlert("error", "Network error while saving KYC documents.");
        }
      });
    },

    // ----------------------------------------------------
    // HELPERS
    // ----------------------------------------------------
    fetchRoles: function (selectSelector, callback) {
      const self = this;
      $.ajax({
        url: self.apiEndpoint + "roles/",
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
