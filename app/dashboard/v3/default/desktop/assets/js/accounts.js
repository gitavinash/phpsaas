/**
 * Accounts & Transactions Ledger Module
 * File: app/dashboard/v3/default/desktop/assets/js/accounts.js
 */

(function ($) {
  "use strict";

  const AccountsApp = {
    transactions: [],
    currentPage: 1,
    currentLimit: 20,
    totalPages: 1,
    currentReceiptIdx: 0,
    activeStatus: "all",
    detailData: null,

    init: function () {
      if ($("#accountsApp").length) {
        this.parseUrlParams();
        this.initDateRangePicker();
        this.bindLedgerEvents();
        this.loadTransactions();
      }

      if ($("#accountDetailApp").length) {
        this.initDetailView();
      }
    },

    initDateRangePicker: function () {
      const self = this;
      const $box = $("#accDateRangePicker");
      if (!$box.length || typeof $.fn.daterangepicker === "undefined" || typeof moment === "undefined") return;

      if ($box.data("daterangepicker")) {
        $box.data("daterangepicker").remove();
      }

      $box.daterangepicker({
        autoUpdateInput: false,
        opens: "left",
        ranges: {
          "All Dates": [moment().subtract(5, "years"), moment().add(1, "years")],
          "Today": [moment(), moment()],
          "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        }
      }, function (start, end, label) {
        if (label === "All Dates") {
          $("#accFilterDateRange").val("");
          $("#accDateRangeLabel").text("All Dates");
        } else {
          const str = start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD");
          $("#accFilterDateRange").val(str);
          $("#accDateRangeLabel").text(label || (start.format("D MMM") + " - " + end.format("D MMM")));
        }
        self.currentPage = 1;
        self.loadTransactions();
      });
    },

    parseUrlParams: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const statusParam = urlParams.get("status");
      if (statusParam) {
        this.activeStatus = statusParam.toLowerCase();
        $("#accFilterStatus").val(this.activeStatus);
        $(`#headerStatusGroup .btn-date-preset[data-status="${this.activeStatus}"]`)
          .addClass("active")
          .siblings()
          .removeClass("active");
      }
    },

    bindLedgerEvents: function () {
      const self = this;

      // Header status pill click
      $(document).on("click", "#headerStatusGroup .btn-date-preset", function () {
        const status = $(this).data("status");
        self.activeStatus = status;
        $("#accFilterStatus").val(status);
        $(this).addClass("active").siblings().removeClass("active");
        self.currentPage = 1;
        self.loadTransactions();
      });

      // Filter change events
      $(document).on("change", "#accFilterStatus", function () {
        self.activeStatus = $(this).val();
        $(`#headerStatusGroup .btn-date-preset[data-status="${self.activeStatus}"]`)
          .addClass("active")
          .siblings()
          .removeClass("active");
        self.currentPage = 1;
        self.loadTransactions();
      });

      $(document).on("change", "#accFilterAgent, #accFilterMethod", function () {
        self.currentPage = 1;
        self.loadTransactions();
      });

      // Search input with debounce
      let searchTimeout = null;
      $(document).on("input", "#accFilterSearch", function () {
        const val = $(this).val().trim();
        if (val.length > 0) {
          $("#btnClearAccSearch").show();
        } else {
          $("#btnClearAccSearch").hide();
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
          self.currentPage = 1;
          self.loadTransactions();
        }, 300);
      });

      $(document).on("click", "#btnClearAccSearch", function () {
        $("#accFilterSearch").val("");
        $(this).hide();
        self.currentPage = 1;
        self.loadTransactions();
      });

      // Reset filters
      $(document).on("click", "#btnResetAccFilters", function () {
        $("#accFilterSearch").val("");
        $("#btnClearAccSearch").hide();
        $("#accFilterAgent").val("");
        $("#accFilterStatus").val("all");
        $("#accFilterMethod").val("");
        $("#accFilterDateRange").val("");
        $("#accDateRangeLabel").text("All Dates");
        self.activeStatus = "all";
        $("#headerStatusGroup .btn-date-preset[data-status='all']")
          .addClass("active")
          .siblings()
          .removeClass("active");
        self.currentPage = 1;
        self.loadTransactions();
      });

      // Refresh button
      $(document).on("click", "#btnRefreshAccounts", function () {
        const $btn = $(this);
        $btn.find("i").addClass("fa-spin");
        self.loadTransactions(function () {
          $btn.find("i").removeClass("fa-spin");
        });
      });

      // Pagination
      $(document).on("click", "#btnAccPrevPage", function () {
        if (self.currentPage > 1) {
          self.currentPage--;
          self.loadTransactions();
        }
      });

      $(document).on("click", "#btnAccNextPage", function () {
        if (self.currentPage < self.totalPages) {
          self.currentPage++;
          self.loadTransactions();
        }
      });

      // Open Record Payment Modal
      $(document).on("click", "#btnOpenAccountsPaymentModal", function () {
        $("#accPayLeadId").val("");
        $("#accPayAmount").val("");
        $("#accPayRef").val("");
        $("#accPayNotes").val("");
        $("#accPayFile").val("");
        $("#accPayFileText").text("Click to upload payment receipt or screenshot");
        $("#btnClearAccPayFile").addClass("v3-hidden");
        $("#accPayFilePreview").empty().addClass("v3-hidden");
        $("#modalAccountsRecordPayment").addClass("is-active");
      });

      // File upload trigger
      $(document).on("click", "#accPayUploadZone", function (e) {
        if (e.target.id !== "btnClearAccPayFile" && !$(e.target).closest("#btnClearAccPayFile").length) {
          document.getElementById("accPayFile").click();
        }
      });

      $(document).on("change", "#accPayFile", function () {
        const file = this.files && this.files[0];
        if (file) {
          $("#accPayFileText").text(file.name + " (" + (file.size / 1024).toFixed(1) + " KB)");
          $("#btnClearAccPayFile").removeClass("v3-hidden");

          if (file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
              $("#accPayFilePreview").html(`<img src="${e.target.result}" alt="Preview" style="max-height: 90px; border-radius: 6px; margin-top: 8px;">`).removeClass("v3-hidden");
            };
            reader.readAsDataURL(file);
          } else {
            $("#accPayFilePreview").html(`<div style="font-size: 12px; color: #0284c7; margin-top: 6px;"><i class="fi fi-rr-file-pdf"></i> PDF Document selected</div>`).removeClass("v3-hidden");
          }
        }
      });

      $(document).on("click", "#btnClearAccPayFile", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $("#accPayFile").val("");
        $("#accPayFileText").text("Click to upload payment receipt or screenshot");
        $("#btnClearAccPayFile").addClass("v3-hidden");
        $("#accPayFilePreview").empty().addClass("v3-hidden");
      });

      // Submit Record Payment
      $(document).on("click", "#btnSubmitAccountsPayment", function () {
        const leadId = $("#accPayLeadId").val().trim();
        const amount = $("#accPayAmount").val().trim();
        const method = $("#accPayMethod").val();
        const date   = $("#accPayDate").val();
        const ref    = $("#accPayRef").val().trim();
        const notes  = $("#accPayNotes").val().trim();
        const fileInput = document.getElementById("accPayFile");

        if (!leadId || parseInt(leadId, 10) <= 0) {
          self.notify("Please enter a valid Lead / Customer ID.", "warning");
          return;
        }

        if (!amount || parseFloat(amount) <= 0) {
          self.notify("Please enter a valid payment amount.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Recording...");

        const formData = new FormData();
        formData.append("lead_id", leadId);
        formData.append("amount", amount);
        formData.append("payment_method", method);
        formData.append("payment_date", date);
        formData.append("reference_number", ref);
        formData.append("payment_notes", notes);

        if (fileInput && fileInput.files && fileInput.files[0]) {
          formData.append("files", fileInput.files[0]);
        }

        $.ajax({
          url: "/api/v3/leads/payment-record/",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Record &amp; Verify Payment');
            if (res.status === "success") {
              self.notify(res.message || "Payment recorded successfully!", "success");
              $("#modalAccountsRecordPayment").removeClass("is-active");
              self.loadTransactions();
            } else {
              self.notify(res.message || "Failed to record payment.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Record &amp; Verify Payment');
            self.notify("Server communication error. Please try again.", "error");
          },
        });
      });

      // Quick Verify / Approve Payment
      $(document).on("click", ".btn-acc-approve-payment", function (e) {
        e.preventDefault();
        const noteId = $(this).data("note-id");
        const $btn = $(this);
        if (!noteId) return;

        const doApprove = function () {
          $btn.prop("disabled", true).html('<i class="fi fi-rr-refresh fa-spin"></i>');

          $.ajax({
            url: "/api/v3/accounts/verify-payment/",
            type: "POST",
            data: { note_id: noteId, action: "approve" },
            dataType: "json",
            success: function (res) {
              if (res.status === "success") {
                if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
                  Swal.fire({
                    icon: "success",
                    type: "success",
                    title: "Payment Verified!",
                    text: res.message || "Payment verified successfully!",
                    showConfirmButton: false,
                    timer: 1800,
                  });
                } else {
                  self.notify(res.message || "Payment verified successfully!", "success");
                }
                self.loadTransactions();
              } else {
                $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i>');
                if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
                  Swal.fire({
                    icon: "error",
                    type: "error",
                    title: "Verification Failed",
                    text: res.message || "Could not verify payment.",
                    confirmButtonColor: "#dc2626",
                  });
                } else {
                  self.notify(res.message || "Could not verify payment.", "error");
                }
              }
            },
            error: function () {
              $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i>');
              self.notify("Error connecting to server.", "error");
            },
          });
        };

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Verify & Approve Payment?",
            text: "Are you sure you want to verify and approve this payment record? This will mark the lead as Confirmed.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Approve Payment",
            cancelButtonText: "Cancel",
          }).then(function (result) {
            if (result && (result.isConfirmed || result.value)) {
              doApprove();
            }
          });
        } else if (confirm("Are you sure you want to verify and approve this payment record? This will mark the lead as Confirmed.")) {
          doApprove();
        }
      });

      // Open Reject Payment with SweetAlert
      $(document).on("click", ".btn-acc-reject-payment", function (e) {
        e.preventDefault();
        const noteId = $(this).data("note-id");
        if (!noteId) return;

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Reject Payment Record?",
            text: "Please provide a reason or remarks for rejecting this payment (optional):",
            input: "textarea",
            inputPlaceholder: "Enter rejection reason...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Reject Payment",
            cancelButtonText: "Cancel",
          }).then(function (result) {
            if (result && (result.isConfirmed || result.value !== undefined)) {
              const reason = (result.value || "").trim();

              $.ajax({
                url: "/api/v3/accounts/verify-payment/",
                type: "POST",
                data: { note_id: noteId, action: "reject", reason: reason },
                dataType: "json",
                success: function (res) {
                  if (res.status === "success") {
                    Swal.fire({
                      icon: "success",
                      type: "success",
                      title: "Payment Rejected",
                      text: res.message || "Payment marked as rejected.",
                      showConfirmButton: false,
                      timer: 1800,
                    });
                    self.loadTransactions();
                  } else {
                    Swal.fire({
                      icon: "error",
                      type: "error",
                      title: "Rejection Failed",
                      text: res.message || "Failed to reject payment.",
                      confirmButtonColor: "#dc2626",
                    });
                  }
                },
                error: function () {
                  self.notify("Error communicating with server.", "error");
                },
              });
            }
          });
        } else {
          $("#accRejectNoteId").val(noteId);
          $("#accRejectReason").val("");
          $("#modalAccountsRejectPayment").addClass("is-active");
        }
      });

      // Confirm Reject Payment from fallback modal
      $(document).on("click", "#btnConfirmRejectPayment", function () {
        const noteId = $("#accRejectNoteId").val();
        const reason = $("#accRejectReason").val().trim();
        const $btn = $(this);

        if (!noteId) return;
        $btn.prop("disabled", true).text("Rejecting...");

        $.ajax({
          url: "/api/v3/accounts/verify-payment/",
          type: "POST",
          data: { note_id: noteId, action: "reject", reason: reason },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Confirm Rejection');
            if (res.status === "success") {
              self.notify(res.message || "Payment marked as rejected.", "success");
              $("#modalAccountsRejectPayment").removeClass("is-active");
              self.loadTransactions();
            } else {
              self.notify(res.message || "Failed to reject payment.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Confirm Rejection');
            self.notify("Error communicating with server.", "error");
          },
        });
      });

      // Close modals
      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(".v3-modal-overlay").removeClass("is-active");
      });

      $(document).on("keydown", function (e) {
        if (e.key === "Escape" || e.keyCode === 27) {
          $(".v3-modal-overlay.is-active").removeClass("is-active");
        }
      });
    },

    renderLoadingSkeleton: function () {
      const $tbody = $("#accountsTableBody");
      let html = "";
      for (let i = 0; i < 5; i++) {
        html += `
          <tr class="v3-skeleton-row">
            <td><div class="v3-skeleton-line w-70" style="margin-bottom: 5px;"></div><div class="v3-skeleton-line h-10 w-40"></div></td>
            <td><div class="v3-skeleton-line w-80" style="margin-bottom: 5px;"></div><div class="v3-skeleton-line h-10 w-50"></div></td>
            <td><div class="v3-skeleton-line h-16 w-60" style="margin-bottom: 5px;"></div><div class="v3-skeleton-pill w-80" style="width: 65px;"></div></td>
            <td><div class="v3-skeleton-line w-70" style="margin-bottom: 5px;"></div><div class="v3-skeleton-line h-10 w-40"></div></td>
            <td><div class="v3-skeleton-pill w-80" style="width: 80px;"></div></td>
            <td style="text-align: right;"><div class="v3-skeleton-btn w-50" style="width: 90px; margin-left: auto;"></div></td>
          </tr>
        `;
      }
      $tbody.html(html);
    },

    loadTransactions: function (callback) {
      const self = this;
      self.renderLoadingSkeleton();

      const params = {
        page: self.currentPage,
        limit: self.currentLimit,
        search: $("#accFilterSearch").val().trim(),
        status: self.activeStatus,
        agent_id: $("#accFilterAgent").val() || "",
        method: $("#accFilterMethod").val() || "",
        daterange: $("#accFilterDateRange").val() || "",
      };

      $.ajax({
        url: "/api/v3/accounts/transactions/",
        type: "GET",
        data: params,
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            self.transactions = res.transactions || [];
            self.renderKPIs(res.kpis || {});
            self.renderAgentOptions(res.agents || []);
            self.renderTable(res.transactions || []);
            self.renderPagination(res.pagination || {});
          } else {
            $("#accountsTableBody").html(`
              <tr><td colspan="6" style="text-align:center; padding: 30px; color: #ef4444;">
                ${res.message || "Failed to load transactions."}
              </td></tr>
            `);
          }
          if (typeof callback === "function") callback();
        },
        error: function () {
          $("#accountsTableBody").html(`
            <tr><td colspan="6" style="text-align:center; padding: 30px; color: #ef4444;">
              Error connecting to server. Please try again.
            </td></tr>
          `);
          if (typeof callback === "function") callback();
        },
      });
    },

    renderKPIs: function (kpis) {
      $("#statTotalVerifiedAmount").text(`₹ ${parseFloat(kpis.total_verified_amount || 0).toLocaleString("en-IN")}`);
      $("#statTotalPendingAmount").text(`₹ ${parseFloat(kpis.total_pending_amount || 0).toLocaleString("en-IN")}`);
      $("#statTodayVerifiedAmount").text(`₹ ${parseFloat(kpis.today_verified_amount || 0).toLocaleString("en-IN")}`);
      $("#statTotalCount").text(parseInt(kpis.total_count || 0, 10));

      const pendingCount = parseInt(kpis.pending_count || 0, 10);
      if (pendingCount > 0) {
        $("#hdrPendingBadge").text(pendingCount).show();
      } else {
        $("#hdrPendingBadge").hide();
      }
    },

    renderAgentOptions: function (agents) {
      const $sel = $("#accFilterAgent");
      if ($sel.children("option").length > 1) return;

      agents.forEach(function (a) {
        $sel.append(`<option value="${a.id}">${a.name} (${a.role})</option>`);
      });
    },

    renderTable: function (txs) {
      const $tbody = $("#accountsTableBody");

      if (!txs || txs.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="6" style="text-align:center; padding: 40px 0;">
              <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;"><i class="fi fi-rr-wallet"></i></div>
              <h4 style="margin: 0 0 4px 0; color: #475569; font-size: 15px;">No Transactions Found</h4>
              <p style="margin: 0; color: #94a3b8; font-size: 13px;">No payment records match your active search and filter criteria.</p>
            </td>
          </tr>
        `);
        return;
      }

      let html = "";
      txs.forEach(function (tx) {
        let statusBadge = `<span class="v3-status-pill status-verified"><i class="fi fi-rr-check"></i> Verified</span>`;
        if (tx.status === "pending") {
          statusBadge = `<span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending Approval</span>`;
        } else if (tx.status === "rejected") {
          statusBadge = `<span class="v3-status-pill status-rejected"><i class="fi fi-rr-cross-circle"></i> Rejected</span>`;
        }

        // Method badge
        const m = (tx.method || "").toLowerCase();
        let mIcon = "fi-rr-wallet";
        let mCls = "badge-method-default";
        if (m.includes("upi") || m.includes("qr")) {
          mIcon = "fi-rr-qr-scan";
          mCls = "badge-method-upi";
        } else if (m.includes("bank") || m.includes("neft") || m.includes("imps")) {
          mIcon = "fi-rr-bank";
          mCls = "badge-method-bank";
        } else if (m.includes("card")) {
          mIcon = "fi-rr-credit-card";
          mCls = "badge-method-card";
        } else if (m.includes("cash")) {
          mIcon = "fi-rr-money-bill-wave";
          mCls = "badge-method-cash";
        } else if (m.includes("cheque") || m.includes("dd")) {
          mIcon = "fi-rr-document";
          mCls = "badge-method-cheque";
        }

        let pendingActionBtns = "";
        if (tx.status === "pending") {
          pendingActionBtns = `
            <button type="button" class="btn-v3-action btn-green btn-xs btn-acc-approve-payment" data-note-id="${tx.note_id}" title="Verify &amp; Approve">
              <i class="fi fi-rr-check"></i>
            </button>
            <button type="button" class="btn-v3-action btn-red btn-xs btn-acc-reject-payment" data-note-id="${tx.note_id}" title="Reject">
              <i class="fi fi-rr-cross"></i>
            </button>
          `;
        }

        html += `
          <tr class="v3-acc-row">
            <td>
              <div class="v3-cell-date-wrap">
                <span class="v3-tx-date-main">${tx.date || "-"}</span>
                <span class="v3-tx-id-tag">#TX-${tx.note_id}</span>
              </div>
            </td>
            <td>
              <div class="v3-cell-customer-wrap">
                <a href="/dashboard/accounts/view/?id=${tx.note_id}" class="v3-customer-name-link">
                  ${tx.customer_name}
                </a>
                <span class="v3-customer-meta-sub">
                  <i class="fi fi-rr-phone-call"></i> ${tx.customer_mobile}
                  <span class="v3-lead-num-chip">Lead #${tx.lead_id}</span>
                </span>
              </div>
            </td>
            <td>
              <div class="v3-cell-amount-wrap">
                <strong class="v3-tx-amount-val">${tx.amount_formatted}</strong>
                <span class="v3-payment-method-chip ${mCls}"><i class="fi ${mIcon}"></i> ${tx.method}</span>
              </div>
            </td>
            <td>
              <div class="v3-cell-agent-wrap">
                <span class="v3-agent-name">${tx.recorded_by}</span>
                <span class="v3-agent-role-pill">${tx.recorded_by_role}</span>
              </div>
            </td>
            <td>${statusBadge}</td>
            <td style="text-align: right;">
              <div class="v3-tx-actions-wrap" style="justify-content: flex-end;">
                <a href="/dashboard/accounts/view/?id=${tx.note_id}" class="btn-table-details" title="View Full Transaction &amp; Customer Details">
                  <i class="fi fi-rr-eye"></i> Details
                </a>
                ${pendingActionBtns}
              </div>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    },

    renderPagination: function (pagination) {
      this.totalPages = pagination.total_pages || 1;
      this.currentPage = pagination.page || 1;

      if (pagination.total > 0) {
        $("#accountsPaginationBar").show();
        $("#accountsPaginationInfo").text(`Showing ${this.transactions.length} of ${pagination.total} transactions`);
        $("#accPageIndicator").text(`Page ${this.currentPage} of ${this.totalPages}`);

        $("#btnAccPrevPage").prop("disabled", this.currentPage <= 1);
        $("#btnAccNextPage").prop("disabled", this.currentPage >= this.totalPages);
      } else {
        $("#accountsPaginationBar").hide();
      }
    },

    // =========================================================================
    // TRANSACTION DETAILS PAGE INITIALIZATION & LOGIC
    // =========================================================================
    initDetailView: function () {
      const self = this;
      const $root = $("#accountDetailApp");
      let txId = $root.data("tx-id");

      if (!txId) {
        const urlParams = new URLSearchParams(window.location.search);
        txId = urlParams.get("id") || urlParams.get("note_id");
      }

      if (!txId) {
        $("#accDetailLoadingWrap").html(`
          <div class="v3-unauthorized-box" style="padding: 40px; text-align: center;">
            <i class="fi fi-rr-exclamation" style="font-size: 36px; color: #ef4444;"></i>
            <h3 style="margin-top: 12px;">Transaction Not Found</h3>
            <p style="color: #64748b;">No valid transaction ID provided.</p>
            <a href="/dashboard/accounts/" class="btn-v3-action btn-blue" style="margin-top: 10px; display: inline-block;">Back to Accounts</a>
          </div>
        `);
        return;
      }

      self.loadTransactionDetail(txId);
      self.bindDetailEvents(txId);
    },

    loadTransactionDetail: function (txId) {
      const self = this;
      $("#accDetailLoadingWrap").show();
      $("#accDetailContentWrap").hide();
      $("#dtlApprovalActionWrap").empty();

      $.ajax({
        url: "/api/v3/accounts/detail/",
        type: "GET",
        data: { id: txId },
        dataType: "json",
        success: function (res) {
          $("#accDetailLoadingWrap").hide();
          if (res.status === "success") {
            self.detailData = res;
            self.renderDetailView(res);
            $("#accDetailContentWrap").show();
          } else {
            $("#accDetailLoadingWrap").html(`
              <div class="v3-unauthorized-box" style="padding: 40px; text-align: center;">
                <i class="fi fi-rr-cross-circle" style="font-size: 36px; color: #ef4444;"></i>
                <h3 style="margin-top: 12px;">${res.message || "Failed to load transaction."}</h3>
                <a href="/dashboard/accounts/" class="btn-v3-action btn-blue" style="margin-top: 10px; display: inline-block;">Back to Accounts</a>
              </div>
            `).show();
          }
        },
        error: function () {
          $("#accDetailLoadingWrap").html(`
            <div class="v3-unauthorized-box" style="padding: 40px; text-align: center;">
              <i class="fi fi-rr-cross-circle" style="font-size: 36px; color: #ef4444;"></i>
              <h3 style="margin-top: 12px;">Error connecting to server</h3>
              <a href="/dashboard/accounts/" class="btn-v3-action btn-blue" style="margin-top: 10px; display: inline-block;">Back to Accounts</a>
            </div>
          `).show();
        },
      });
    },

    renderDetailView: function (data) {
      const tx = data.transaction || {};
      const cust = data.customer || {};
      const pkg = data.package || {};
      const history = data.history || [];

      $("#dtlTxIdBadge").text(`#TX-${tx.note_id}`);

      let recordedTimeStr = "";
      if (tx.recorded_at && typeof moment !== "undefined") {
        recordedTimeStr = moment(tx.recorded_at).format("D MMM YYYY, hh:mm A");
      } else {
        recordedTimeStr = tx.date || "recently";
      }

      let agentContactSub = tx.recorded_by_email || tx.recorded_by_mobile || tx.recorded_by_role || "";
      $("#dtlTxSubheader").text(`Recorded on ${recordedTimeStr} by ${tx.recorded_by}${agentContactSub ? ` (${agentContactSub})` : ""}`);

      // Header actions - Lead CRM URL with base64 encoded lead id
      const leadIdB64 = cust.lead_id_base64 || (cust.lead_id ? btoa(cust.lead_id.toString()) : "");
      if (leadIdB64) {
        $("#dtlOpenLeadBtn").attr("href", `/dashboard/leads/view/?id=${encodeURIComponent(leadIdB64)}`).show();
      } else {
        $("#dtlOpenLeadBtn").hide();
      }

      // Show verify and reject buttons ONLY if payment is strictly pending
      const statusNormalized = String(tx.status || "").toLowerCase().trim();
      const isPending = (statusNormalized === "pending" || statusNormalized === "pending approval" || statusNormalized === "unverified");
      if (isPending) {
        $("#dtlApprovalActionWrap").html(`
          <button type="button" class="btn-v3-action btn-green" id="dtlApproveBtn">
            <i class="fi fi-rr-check"></i> Verify &amp; Approve
          </button>
          <button type="button" class="btn-v3-action btn-red" id="dtlRejectBtn">
            <i class="fi fi-rr-cross"></i> Reject
          </button>
        `);
        $("#dtlStatusWrap").html(`<span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending Approval</span>`);
      } else {
        $("#dtlApprovalActionWrap").empty();
        if (statusNormalized === "rejected" || statusNormalized === "cancelled") {
          $("#dtlStatusWrap").html(`<span class="v3-status-pill status-rejected"><i class="fi fi-rr-cross-circle"></i> Rejected</span>`);
        } else {
          $("#dtlStatusWrap").html(`<span class="v3-status-pill status-verified"><i class="fi fi-rr-check"></i> Verified</span>`);
        }
      }

      // Amount & Metadata
      $("#dtlAmount").text(tx.amount_formatted);

      let formattedPayDate = tx.date || "-";
      if (tx.date && typeof moment !== "undefined") {
        const m = moment(tx.date, ["DD-MM-YYYY", "YYYY-MM-DD", "D-M-YYYY"]);
        if (m.isValid()) formattedPayDate = m.format("D MMMM YYYY");
      }
      $("#dtlDate").text(formattedPayDate);

      // Method chip
      const m = (tx.method || "").toLowerCase();
      let mIcon = "fi-rr-wallet";
      let mCls = "badge-method-default";
      if (m.includes("upi") || m.includes("qr")) {
        mIcon = "fi-rr-qr-scan";
        mCls = "badge-method-upi";
      } else if (m.includes("bank") || m.includes("neft") || m.includes("imps")) {
        mIcon = "fi-rr-bank";
        mCls = "badge-method-bank";
      } else if (m.includes("card")) {
        mIcon = "fi-rr-credit-card";
        mCls = "badge-method-card";
      } else if (m.includes("cash")) {
        mIcon = "fi-rr-money-bill-wave";
        mCls = "badge-method-cash";
      } else if (m.includes("cheque") || m.includes("dd")) {
        mIcon = "fi-rr-document";
        mCls = "badge-method-cheque";
      }
      $("#dtlMethod").html(`<span class="v3-payment-method-chip ${mCls}"><i class="fi ${mIcon}"></i> ${tx.method}</span>`);
      $("#dtlRef").text(tx.reference || "None");

      // Recorded By with name + email or phone
      let agentContactInfo = "";
      if (tx.recorded_by_email) {
        agentContactInfo += `<span class="agent-contact-line"><i class="fi fi-rr-envelope"></i> ${tx.recorded_by_email}</span>`;
      }
      if (tx.recorded_by_mobile) {
        agentContactInfo += `<span class="agent-contact-line"><i class="fi fi-rr-phone-call"></i> ${tx.recorded_by_mobile}</span>`;
      }

      $("#dtlRecordedBy").html(`
        <div class="recorded-agent-box">
          <span class="agent-name-main">${tx.recorded_by} <small class="v3-agent-role-pill">(${tx.recorded_by_role})</small></span>
          ${agentContactInfo}
        </div>
      `);

      $("#dtlNotes").text(tx.notes || "No remarks provided.");

      if (tx.verified_by) {
        let vTime = tx.verified_at || "";
        if (vTime && typeof moment !== "undefined") vTime = moment(vTime).format("D MMM YYYY, hh:mm A");
        $("#dtlVerifiedBy").text(`${tx.verified_by} · ${vTime}`);
        $("#dtlVerifiedByRow").show();
      } else {
        $("#dtlVerifiedByRow").hide();
      }

      if (tx.rejected_by) {
        $("#dtlRejectionReason").text(`Rejected by ${tx.rejected_by}: ${tx.rejection_reason || "No reason given"}`);
        $("#dtlRejectionRow").show();
      } else {
        $("#dtlRejectionRow").hide();
      }

      // Receipt Proof Area
      const $receiptArea = $("#dtlReceiptArea");
      const $downloadBtn = $("#dtlDownloadReceiptBtn");
      if (tx.image_url) {
        const isPdf = tx.image_url.toLowerCase().includes(".pdf");
        if (isPdf) {
          $receiptArea.html(`
            <div class="v3-receipt-pdf-placeholder">
              <i class="fi fi-rr-file-pdf"></i>
              <h5>PDF Document Attached</h5>
              <p>Click below to preview or download the receipt PDF.</p>
              <a href="${tx.image_url}" target="_blank" class="btn-v3-action btn-green"><i class="fi fi-rr-eye"></i> View PDF Document</a>
            </div>
          `);
        } else {
          $receiptArea.html(`
            <div class="acc-receipt-full-preview-box">
              <a href="${tx.image_url}" target="_blank" title="Click to view full image">
                <img src="${tx.image_url}" alt="Receipt Proof" class="acc-receipt-img-tag">
              </a>
            </div>
          `);
        }
        $downloadBtn.attr("href", tx.image_url).show();
      } else {
        $receiptArea.html(`
          <div class="v3-receipt-empty-preview">
            <i class="fi fi-rr-picture"></i>
            <p>No receipt image attached for this transaction.</p>
          </div>
        `);
        $downloadBtn.hide();
      }

      // Customer Info
      $("#dtlCustomerName").text(cust.name);
      $("#dtlCustomerPhone").text(cust.mobile);
      $("#dtlCustomerEmail").text(cust.email);
      $("#dtlLeadStatusBadge").text(`Lead #${cust.lead_id}`);

      const cleanPhone = (cust.mobile || "").replace(/\D/g, "");
      if (cleanPhone.length >= 10) {
        const phone10 = cleanPhone.slice(-10);
        $("#dtlCustWaLink").attr("href", `https://wa.me/91${phone10}`).show();
        $("#dtlCustCallLink").attr("href", `tel:${phone10}`).show();
      } else {
        $("#dtlCustWaLink").hide();
        $("#dtlCustCallLink").hide();
      }

      const firstLetter = (cust.name || "C").charAt(0).toUpperCase();
      $("#dtlCustomerAvatar").text(firstLetter);

      // Package & Financials - Plain text without link
      $("#dtlPackageTitle").text(pkg.title || "Custom Tour Package");

      $("#dtlQuotePrice").text(pkg.quote_formatted || "₹ 0.00");
      $("#dtlTotalPaid").text(pkg.paid_formatted || "₹ 0.00");
      $("#dtlPendingBalance").text(pkg.pending_formatted || "₹ 0.00");

      const $pendingCard = $("#dtlPendingCard");
      if (pkg.is_paid_in_full) {
        $pendingCard.removeClass("is-due").addClass("is-settled");
        $("#dtlBalanceIcon").html('<i class="fi fi-rr-badge-check"></i>');
        $("#dtlBalanceLabel").text("Balance Settled");
        // $("#dtlBalanceTag").text("✓ Paid in Full").show();
      } else {
        $pendingCard.removeClass("is-settled").addClass("is-due");
        $("#dtlBalanceIcon").html('<i class="fi fi-rr-clock"></i>');
        $("#dtlBalanceLabel").text("Balance Due");
        $("#dtlBalanceTag").hide();
      }

      // History Table
      $("#dtlHistoryCount").text(`${history.length} ${history.length === 1 ? "Record" : "Records"}`);
      const $histBody = $("#dtlHistoryTableBody");
      if (history.length === 0) {
        $histBody.html(`<tr><td colspan="5" style="text-align:center; padding: 20px; color: #94a3b8;">No payment history records found.</td></tr>`);
      } else {
        let hHtml = "";
        history.forEach(function (h) {
          let hStatusBadge = `<span class="v3-status-pill status-verified" style="font-size: 11px; padding: 3px 9px;"><i class="fi fi-rr-check"></i> Verified</span>`;
          if (h.status === "pending") {
            hStatusBadge = `<span class="v3-status-pill status-pending" style="font-size: 11px; padding: 3px 9px;"><i class="fi fi-rr-clock"></i> Pending</span>`;
          } else if (h.status === "rejected") {
            hStatusBadge = `<span class="v3-status-pill status-rejected" style="font-size: 11px; padding: 3px 9px;"><i class="fi fi-rr-cross-circle"></i> Rejected</span>`;
          }

          const isCurrentTag = h.is_current ? `<span class="current-tx-pill">Current</span>` : "";

          let hDateFormatted = h.date;
          if (h.date && typeof moment !== "undefined") {
            const hm = moment(h.date, ["DD-MM-YYYY", "YYYY-MM-DD", "D-M-YYYY"]);
            if (hm.isValid()) hDateFormatted = hm.format("D MMM YYYY");
          }

          hHtml += `
            <tr class="${h.is_current ? 'highlight-current-row' : ''}">
              <td>
                <span class="hist-date" style="font-weight: 600; color: #334155;">${hDateFormatted}</span>
                ${isCurrentTag}
              </td>
              <td><strong style="color: #0f172a; font-size: 13.5px;">${h.amount_formatted}</strong></td>
              <td><span style="font-size: 12px; color: #475569;">${h.method}</span></td>
              <td>${hStatusBadge}</td>
              <td style="text-align: right;">
                ${!h.is_current ? `<a href="/dashboard/accounts/view/?id=${h.note_id}" class="btn-table-details" style="padding: 4px 10px; font-size: 11.5px;"><i class="fi fi-rr-eye"></i> View</a>` : `<span class="current-active-chip">Active</span>`}
              </td>
            </tr>
          `;
        });
        $histBody.html(hHtml);
      }
    },

    bindDetailEvents: function (txId) {
      const self = this;

      // Approve action with SweetAlert
      $(document).on("click", "#dtlApproveBtn", function () {
        const doApprove = function () {
          const $btn = $("#dtlApproveBtn");
          $btn.prop("disabled", true).html('<i class="fi fi-rr-refresh fa-spin"></i> Approving...');

          $.ajax({
            url: "/api/v3/accounts/verify-payment/",
            type: "POST",
            data: { note_id: txId, action: "approve" },
            dataType: "json",
            success: function (res) {
              $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Verify &amp; Approve');
              if (res.status === "success") {
                if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
                  Swal.fire({
                    icon: "success",
                    type: "success",
                    title: "Payment Verified!",
                    text: res.message || "Payment verified and customer lead marked as Confirmed.",
                    showConfirmButton: false,
                    timer: 1800,
                  });
                } else {
                  self.notify(res.message || "Payment verified successfully!", "success");
                }
                self.loadTransactionDetail(txId);
              } else {
                if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
                  Swal.fire({
                    icon: "error",
                    type: "error",
                    title: "Verification Failed",
                    text: res.message || "Could not verify payment.",
                    confirmButtonColor: "#dc2626",
                  });
                } else {
                  self.notify(res.message || "Could not verify payment.", "error");
                }
              }
            },
            error: function () {
              $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Verify &amp; Approve');
              self.notify("Error communicating with server.", "error");
            },
          });
        };

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Verify & Approve Payment?",
            text: "Are you sure you want to verify and approve this payment record? This will mark the customer lead as Confirmed.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Approve Payment",
            cancelButtonText: "Cancel",
          }).then(function (result) {
            if (result && (result.isConfirmed || result.value)) {
              doApprove();
            }
          });
        } else if (confirm("Are you sure you want to verify and approve this payment record? This will mark the customer lead as Confirmed.")) {
          doApprove();
        }
      });

      // Open Reject action with SweetAlert prompt
      $(document).on("click", "#dtlRejectBtn", function () {
        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Reject Payment Record?",
            text: "Please provide a reason or remarks for rejecting this payment (optional):",
            input: "textarea",
            inputPlaceholder: "Enter rejection reason...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Reject Payment",
            cancelButtonText: "Cancel",
          }).then(function (result) {
            if (result && (result.isConfirmed || result.value !== undefined)) {
              const reason = (result.value || "").trim();
              const $btn = $("#dtlRejectBtn");
              $btn.prop("disabled", true).html('<i class="fi fi-rr-refresh fa-spin"></i> Rejecting...');

              $.ajax({
                url: "/api/v3/accounts/verify-payment/",
                type: "POST",
                data: { note_id: txId, action: "reject", reason: reason },
                dataType: "json",
                success: function (res) {
                  $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Reject');
                  if (res.status === "success") {
                    Swal.fire({
                      icon: "success",
                      type: "success",
                      title: "Payment Rejected",
                      text: res.message || "Payment marked as rejected.",
                      showConfirmButton: false,
                      timer: 1800,
                    });
                    self.loadTransactionDetail(txId);
                  } else {
                    Swal.fire({
                      icon: "error",
                      type: "error",
                      title: "Rejection Failed",
                      text: res.message || "Failed to reject payment.",
                      confirmButtonColor: "#dc2626",
                    });
                  }
                },
                error: function () {
                  $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Reject');
                  self.notify("Error connecting to server.", "error");
                },
              });
            }
          });
        } else {
          $("#dtlRejectReason").val("");
          $("#modalDtlRejectPayment").addClass("is-active");
        }
      });

      // Confirm Reject from fallback modal
      $(document).on("click", "#btnConfirmDtlReject", function () {
        const reason = $("#dtlRejectReason").val().trim();
        const $btn = $(this);
        $btn.prop("disabled", true).text("Rejecting...");

        $.ajax({
          url: "/api/v3/accounts/verify-payment/",
          type: "POST",
          data: { note_id: txId, action: "reject", reason: reason },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Confirm Rejection');
            if (res.status === "success") {
              self.notify(res.message || "Payment marked as rejected.", "success");
              $("#modalDtlRejectPayment").removeClass("is-active");
              self.loadTransactionDetail(txId);
            } else {
              self.notify(res.message || "Failed to reject payment.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-cross"></i> Confirm Rejection');
            self.notify("Error connecting to server.", "error");
          },
        });
      });

      // Copy reference button
      $(document).on("click", "#btnCopyDtlRef", function () {
        const ref = $("#dtlRef").text().trim();
        if (ref && ref !== "None" && navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(ref).then(function () {
            self.notify("Reference copied to clipboard!", "success");
          });
        }
      });
    },

    notify: function (message, type) {
      if (typeof window.v3Notify === "function") {
        window.v3Notify(message, type);
      } else if (typeof toastr !== "undefined") {
        toastr[type || "info"](message);
      } else {
        alert(message);
      }
    },
  };

  $(document).ready(function () {
    AccountsApp.init();
  });

  $(document).on("spa:loaded", function () {
    AccountsApp.init();
  });

  window.AccountsApp = AccountsApp;
})(jQuery);
