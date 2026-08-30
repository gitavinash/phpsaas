/**
 * V3 Lead View & Profile SPA Module
 * Full Screen Cover Itinerary Builder with Rich Accommodation, Transport & Activity Controls.
 */
(function (factory) {
  if (typeof window.jQuery !== "undefined") {
    factory(window.jQuery);
  } else {
    document.addEventListener("DOMContentLoaded", function () {
      if (typeof window.jQuery !== "undefined") {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  "use strict";

  const WA_SVG = `<svg class="v3-wa-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>`;

  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  window.V3LeadView = {
    leadId: null,
    leadData: null,
    isLoading: false,
    eventsBound: false,
    activeComposerType: "note", // "note" | "followup"
    availablePackages: [],
    selectedPackage: null,

    activeXhr: null,

    notify: function (title, type, text) {
      type = type || "info";
      if (typeof popUpMessage === "function" && !text) {
        popUpMessage("center", title, type);
      } else if (
        typeof Swal !== "undefined" &&
        typeof Swal.fire === "function"
      ) {
        Swal.fire({
          position: "center",
          type: type,
          icon: type,
          title: title,
          text: text || "",
          showConfirmButton: !!text,
          timer: text ? undefined : 1800,
        });
      } else {
        alert(title + (text ? "\n" + text : ""));
      }
    },

    scheduleClientReminder: function (followupTimeStr, customerName) {
      if (!followupTimeStr) return;

      // Ask for browser notification permission if not yet decided
      if ("Notification" in window && Notification.permission === "default") {
        try {
          Notification.requestPermission();
        } catch (e) {}
      }

      // Compute 5 minutes before follow-up in milliseconds
      const followupDate = new Date(followupTimeStr.replace(/-/g, "/"));
      const targetTimeMs = followupDate.getTime() - (5 * 60 * 1000);
      const delayMs = targetTimeMs - Date.now();

      // Only schedule if within the next 24 hours and still in the future
      if (delayMs > 0 && delayMs < 24 * 60 * 60 * 1000) {
        setTimeout(() => {
          // 1. Browser Native Notification
          if ("Notification" in window && Notification.permission === "granted") {
            try {
              new Notification(`🔔 Follow-Up in 5 Minutes: ${customerName}`, {
                body: `Scheduled follow-up is due at ${followupDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}.`,
                icon: "/favicon.ico",
              });
            } catch (e) {}
          }

          // 2. In-App SweetAlert Toast Modal
          if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
            Swal.fire({
              title: "🔔 Follow-Up in 5 Minutes!",
              text: `Your scheduled follow-up with ${customerName} is due in 5 minutes.`,
              icon: "warning",
              confirmButtonColor: "#ea580c",
              confirmButtonText: "View Activity",
            });
          }
        }, delayMs);
      }
    },

    getLeadId: function () {
      const urlParams = new URLSearchParams(window.location.search);
      let id = urlParams.get("id") || urlParams.get("leadId") || "";
      if (!id) {
        const $app = $("#leadViewApp");
        if ($app.length) {
          id = $app.data("lead-id") || $app.attr("data-lead-id") || "";
        }
      }
      if (!id) {
        const match = window.location.pathname.match(/\/leads\/view\/([^\/]+)/);
        if (match && match[1]) {
          id = match[1];
        }
      }
      return id ? String(id).trim() : "";
    },

    init: function () {
      const $app = $("#leadViewApp");
      if (!$app.length) return;

      const currentId = this.getLeadId();
      if (!currentId) {
        $("#leadViewMainContainer").html(`
          <div class="v3-leads-empty-state">
            <div class="v3-empty-icon-wrap"><i class="fi fi-rr-exclamation"></i></div>
            <h4 class="v3-empty-title">Lead ID Missing</h4>
            <p class="v3-empty-subtitle">No valid lead identifier was provided in the URL request.</p>
            <a href="/dashboard/leads/list/" class="btn-empty-reset-filters"><i class="fi fi-rr-arrow-left"></i> Back to Leads</a>
          </div>
        `);
        return;
      }

      // If we are ALREADY fetching or already loaded for this exact leadId, ignore redundant calls
      if (this.leadId === currentId && (this.isLoading || this.leadData)) {
        return;
      }

      this.leadId = currentId;
      this.leadData = null;

      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }

      this.loadLeadDetails();
    },

    loadLeadDetails: function () {
      const self = this;
      if (!self.leadId) return;

      if (self.activeXhr) {
        self.activeXhr.abort();
        self.activeXhr = null;
      }
      self.isLoading = true;

      const $container = $("#leadViewMainContainer");
      $container.html(`
        <div class="v3-leads-table-loader">
          <div class="v3-table-spinner">
            <div class="v3-spinner-ring"></div>
            <div class="v3-spinner-inner-dot"></div>
          </div>
          <h4 class="v3-loader-title">Loading Lead Details...</h4>
          <p class="v3-loader-subtitle">Fetching customer profile, timeline, and booking records.</p>
        </div>
      `);

      self.activeXhr = $.ajax({
        url: "/api/v3/leads/view/",
        type: "GET",
        data: { id: self.leadId },
        dataType: "json",
        success: function (res) {
          self.isLoading = false;
          self.activeXhr = null;
          if (res.status === "success" && res.data) {
            self.leadData = res.data;
            self.renderView(res.data);
          } else {
            $container.html(`
              <div class="v3-leads-empty-state">
                <div class="v3-empty-icon-wrap"><i class="fi fi-rr-cross-circle"></i></div>
                <h4 class="v3-empty-title">Lead Not Found</h4>
                <p class="v3-empty-subtitle">${res.message || "The requested lead could not be located or access is restricted."}</p>
                <a href="/dashboard/leads/list/" class="btn-empty-reset-filters"><i class="fi fi-rr-arrow-left"></i> Back to Leads</a>
              </div>
            `);
          }
        },
        error: function (xhr, status) {
          if (status === "abort") return;
          self.isLoading = false;
          self.activeXhr = null;
          $container.html(`
            <div class="v3-leads-empty-state">
              <div class="v3-empty-icon-wrap"><i class="fi fi-rr-exclamation"></i></div>
              <h4 class="v3-empty-title">Connection Error</h4>
              <p class="v3-empty-subtitle">Unable to fetch lead details from server. Please check your network and retry.</p>
              <button type="button" class="btn-empty-reset-filters" id="btnRetryLeadView"><i class="fi fi-rr-refresh"></i> Retry</button>
            </div>
          `);
        },
      });
    },

    renderView: function (lead) {
      const self = this;
      const $container = $("#leadViewMainContainer");

      document.title =
        (lead.name ? lead.name + " - Lead Details" : "Lead Details") +
        " | V3 Dashboard";

      const travel = lead.travel_details || {};
      const quote = lead.quotation || {};

      // 1. Build Agent Select Options
      let agentOptions = `<option value="0">Unassigned</option>`;
      (lead.agents || []).forEach(function (ag) {
        const isSel = lead.agent_id === ag.id ? "selected" : "";
        agentOptions += `<option value="${ag.id}" ${isSel}>${ag.name} (${ag.role})</option>`;
      });

      // 2. Build Timeline Stream
      let timelineHtml = "";
      if (lead.timeline && lead.timeline.length > 0) {
        timelineHtml = '<div class="v3-timeline-stream">';
        lead.timeline.forEach((t) => {
          let extraContent = "";
          let cardExtraClass = "";

          if (t.type === "itinerary") {
            const q = t.quotation || {};
            const encId = q.encoded_id || (t.description && !isNaN(t.description) ? btoa(t.description) : "");
            const title = q.title || (t.description ? (isNaN(t.description) ? t.description : `Tour Itinerary #${t.description}`) : "Tour Itinerary");
            const price = parseFloat(q.price || 0);
            const mrp = parseFloat(q.mrp || 0);
            const viewUrl = q.view_url || (encId ? `/view-itinerary/${encId}/` : "#");
            const downloadUrl = q.download_url || (encId ? `/download-itinerary/${encId}/` : "#");
            const hasMeta = !!(q.destination || q.duration || q.theme);

            cardExtraClass = "v3-timeline-card-itinerary";
            extraContent = `
              <div class="v3-timeline-itinerary-card">
                <div class="v3-tin-card-top">
                  <div class="v3-tin-card-info">
                    <div class="v3-tin-title-row">
                      <span class="v3-tin-title"><i class="fi fi-rr-route"></i> ${title}</span>
                      ${q.id || (t.description && !isNaN(t.description)) ? `<span class="v3-tin-chip">#${q.id || t.description}</span>` : ""}
                    </div>
                    ${hasMeta ? `
                      <div class="v3-tin-tags">
                        ${q.destination ? `<span class="v3-tin-tag"><i class="fi fi-rr-marker"></i> ${q.destination}</span>` : ""}
                        ${q.duration ? `<span class="v3-tin-tag"><i class="fi fi-rr-moon"></i> ${q.duration}</span>` : ""}
                        ${q.theme ? `<span class="v3-tin-tag theme"><i class="fi fi-rr-star"></i> ${q.theme}</span>` : ""}
                      </div>
                    ` : ""}
                  </div>
                  ${price > 0 ? `
                    <div class="v3-tin-price-box">
                      <span class="v3-tin-price-val">₹ ${price.toLocaleString("en-IN")}</span>
                      ${mrp > price ? `<span class="v3-tin-price-mrp">₹ ${mrp.toLocaleString("en-IN")}</span>` : ""}
                    </div>
                  ` : ""}
                </div>
                ${encId ? `
                  <div class="v3-tin-actions-row">
                    <a href="${viewUrl}" target="_blank" class="v3-tin-action-btn primary" title="View interactive itinerary webpage">
                      <i class="fi fi-rr-eye"></i> View Itinerary
                    </a>
                    <a href="${downloadUrl}" target="_blank" class="v3-tin-action-btn secondary" title="Download official PDF proposal">
                      <i class="fi fi-rr-file-pdf"></i> Download PDF
                    </a>
                    <button type="button" class="v3-tin-action-btn outline btn-copy-itin-link" data-url="${window.location.origin + viewUrl}" title="Copy itinerary link to clipboard">
                      <i class="fi fi-rr-copy"></i> Copy Link
                    </button>
                  </div>
                ` : ""}
              </div>
            `.trim();
          } else if (t.type === "payment" && t.description) {
            extraContent = `<div class="v3-timeline-payment-text"><i class="fi fi-rr-check-circle"></i> ${t.description}</div>`;
          } else if (t.description) {
            extraContent = t.description;
          }

          timelineHtml += `
            <div class="v3-timeline-row">
              <div class="v3-timeline-bullet"><i class="${t.icon}"></i></div>
              <div class="v3-timeline-row-meta">
                <span class="v3-timeline-tag">${t.title}</span>
                <span>•</span>
                <span>${t.user_name}</span>
                <span>•</span>
                <span class="v3-timeline-time-ago">${t.time_relative || t.time}</span>
              </div>
              ${extraContent ? `<div class="v3-timeline-card-content ${cardExtraClass}">${extraContent}</div>` : ""}
            </div>
          `;
        });
        timelineHtml += "</div>";
      } else {
        timelineHtml = `
          <div class="v3-leads-empty-state v3-empty-compact">
            <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-comment-alt"></i></div>
            <h4 class="v3-empty-title">No Activity Logged Yet</h4>
            <p class="v3-empty-subtitle">Use the composer above to write customer notes or set follow-up reminders.</p>
          </div>
        `;
      }

      // 3. Build Payment Ledger
      self.leadPayments = lead.payments || [];
      let totalPaid = 0;
      self.leadPayments.forEach((p) => {
        totalPaid += parseFloat(p.amount || 0);
      });

      // Calculate Total Package Value & Pending Amount
      let quotePrice = parseFloat((lead.quotation && lead.quotation.price) || (self.leadData && self.leadData.quotation && self.leadData.quotation.price) || 0);
      if (quotePrice <= 0 && lead.timeline && lead.timeline.length > 0) {
        lead.timeline.forEach((t) => {
          if (t.quotation && t.quotation.price && parseFloat(t.quotation.price) > 0 && quotePrice <= 0) {
            quotePrice = parseFloat(t.quotation.price);
          }
        });
      }

      let pendingAmount = 0;
      let pendingHtml = "";

      if (quotePrice > 0) {
        pendingAmount = Math.max(0, quotePrice - totalPaid);
        if (pendingAmount > 0) {
          pendingHtml = `
            <div class="v3-pay-stat-pill stat-pending">
              <i class="fi fi-rr-clock"></i>
              <span class="v3-pay-stat-label">Pending</span>
              <span class="v3-pay-stat-val">₹ ${pendingAmount.toLocaleString("en-IN")}</span>
            </div>
          `;
        } else {
          pendingHtml = `
            <div class="v3-pay-stat-pill stat-settled">
              <i class="fi fi-rr-badge-check"></i>
              <span class="v3-pay-stat-label">Pending</span>
              <span class="v3-pay-stat-val">₹ 0 (Paid in Full)</span>
            </div>
          `;
        }
      } else {
        pendingHtml = `
          <div class="v3-pay-stat-pill stat-pending">
            <i class="fi fi-rr-clock"></i>
            <span class="v3-pay-stat-label">Pending</span>
            <span class="v3-pay-stat-val">₹ 0</span>
          </div>
        `;
      }

      function getPaymentMethodBadge(method) {
        const m = (method || "").toLowerCase();
        let icon = "fi-rr-wallet";
        let cls = "badge-method-default";
        if (m.includes("upi") || m.includes("qr")) {
          icon = "fi-rr-qr-scan";
          cls = "badge-method-upi";
        } else if (m.includes("bank") || m.includes("neft") || m.includes("imps")) {
          icon = "fi-rr-bank";
          cls = "badge-method-bank";
        } else if (m.includes("card")) {
          icon = "fi-rr-credit-card";
          cls = "badge-method-card";
        } else if (m.includes("cash")) {
          icon = "fi-rr-money-bill-wave";
          cls = "badge-method-cash";
        } else if (m.includes("cheque") || m.includes("dd")) {
          icon = "fi-rr-document";
          cls = "badge-method-cheque";
        }
        return `<span class="v3-payment-method-chip ${cls}"><i class="fi ${icon}"></i> ${method || "Standard"}</span>`;
      }

      let paymentsHtml = "";
      if (self.leadPayments.length > 0) {
        paymentsHtml = `
          <div class="v3-payments-container-card">
            <div class="v3-payments-header-bar">
              <div class="v3-payments-summary-group">
                ${quotePrice > 0 ? `
                  <div class="v3-pay-stat-pill stat-package">
                    <i class="fi fi-rr-box-alt"></i>
                    <span class="v3-pay-stat-label">Package Total</span>
                    <span class="v3-pay-stat-val">₹ ${quotePrice.toLocaleString("en-IN")}</span>
                  </div>
                ` : ""}
                <div class="v3-pay-stat-pill stat-collected">
                  <i class="fi fi-rr-check-circle"></i>
                  <span class="v3-pay-stat-label">Collected</span>
                  <span class="v3-pay-stat-val">₹ ${totalPaid.toLocaleString("en-IN")}</span>
                </div>
                ${pendingHtml}
                <div class="v3-pay-stat-pill stat-entries">
                  <i class="fi fi-rr-receipt"></i>
                  <span class="v3-pay-stat-label">Entries</span>
                  <span class="v3-pay-stat-val">${self.leadPayments.length}</span>
                </div>
              </div>
              <button type="button" class="btn-v3-action btn-green" id="btnOpenPaymentModalTab" data-tooltip="Record Payment">
                <i class="fi fi-rr-plus"></i> Record Payment
              </button>
            </div>
            <div class="v3-table-responsive-wrapper">
              <table class="v3-payments-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference / UTR</th>
                    <th>Receipt Proof</th>
                    <th>Recorded By</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
        `;
        self.leadPayments.forEach((p, idx) => {
          const imgUrl = p.image_url || (p.image && typeof p.image === "object" ? p.image.path : (typeof p.image === "string" && (p.image.includes("/") || p.image.includes(".")) ? p.image : ""));
          const receiptHtml = imgUrl
            ? `<button type="button" class="v3-payment-receipt-btn btn-view-payment-receipt" data-payment-idx="${idx}" title="Click to view receipt image and payment details">
                <i class="fi fi-rr-picture"></i> View Receipt
               </button>`
            : `<span class="v3-receipt-none-chip"><i class="fi fi-rr-cross-small"></i> None</span>`;

          const refHtml = p.reference
            ? `<span class="v3-pay-ref-text monospace" title="${p.reference}">${p.reference}</span>`
            : `<span class="v3-text-muted">-</span>`;

          paymentsHtml += `
            <tr class="v3-payment-row">
              <td><span class="v3-pay-date-text"><i class="fi fi-rr-calendar"></i> ${p.date}</span></td>
              <td><strong class="v3-pay-amount-text">₹ ${parseFloat(p.amount).toLocaleString("en-IN")}</strong></td>
              <td>${getPaymentMethodBadge(p.method)}</td>
              <td>${refHtml}</td>
              <td>${receiptHtml}</td>
              <td><span class="v3-pay-recorded-by"><i class="fi fi-rr-user"></i> ${p.recorded_by}</span></td>
              <td><span class="v3-pay-remarks-text">${p.notes || "-"}</span></td>
            </tr>
          `;
        });
        paymentsHtml += `</tbody></table></div></div>`;
      } else {
        paymentsHtml = `
          <div class="v3-leads-empty-state v3-empty-compact">
            <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-indian-rupee-sign"></i></div>
            <h4 class="v3-empty-title">No Payments Recorded</h4>
            <p class="v3-empty-subtitle">No advance deposit or final settlement payment has been recorded yet.</p>
            <button type="button" class="btn-v3-action btn-green" id="btnOpenPaymentModal" data-tooltip="Record Payment">
              <i class="fi fi-rr-plus"></i> Record Payment
            </button>
          </div>
        `;
      }

      const kpis = lead.kpis || {
        next_followup: {
          is_scheduled: false,
          time_formatted: "Not Scheduled",
          status_badge: "none",
          label: "Not Scheduled",
        },
        lead_age: {
          days: 0,
          text: "Active Today",
        },
        touchpoints: {
          total: lead.timeline ? lead.timeline.length : 0,
          summary: `${lead.timeline ? lead.timeline.length : 0} Touchpoints`,
        },
      };
      const followup = kpis.next_followup;
      const leadAge = kpis.lead_age;
      const touchpoints = kpis.touchpoints;

      const st = parseInt(lead.status || 0);
      const isAssigned = parseInt(lead.agent_id || 0) > 0;

      // Role check: Only Administrator and Operational can see/manage Assigned Representative
      const userRole = (lead.access_type || $("#leadViewApp").data("user-role") || "").toLowerCase().trim();
      const canManageAgent = (
        userRole === "administrator" ||
        userRole === "operations" ||
        userRole === "superadmin" ||
        userRole === "operational"
      );

      const agentSidebarHtml = canManageAgent
        ? `
          <!-- Assigned Representative Box (Administrator & Operational Only) -->
          <div class="v3-sidebar-card">
            <div class="v3-sidebar-card-title">
              <span>Assigned Representative</span>
              <i class="fi fi-rr-user-gear"></i>
            </div>
            <div class="v3-assigned-agent-wrapper">
              <select id="leadDetailAgentSelect" class="leads-filter-select v3-select-full">
                <option value="0">-- Unassigned --</option>
                ${(lead.agents || [])
                  .map((ag) => {
                    const agId = parseInt(ag.id || ag.user_id || 0);
                    const isSelected = parseInt(lead.agent_id || 0) === agId;
                    return `<option value="${agId}" ${isSelected ? "selected" : ""}>${ag.name} (${ag.role})</option>`;
                  })
                  .join("")}
              </select>
            </div>
          </div>
        `
        : "";

      // 4. Complete Unified CRM Assembly
      const html = `
        <div class="v3-lead-view-wrapper">
          <!-- 1. Minimal Hero Header Banner -->
          <div class="v3-lead-hero-banner">
            <div class="v3-lead-hero-left">
              <a href="/dashboard/leads/list/" class="v3-lead-back-circle" id="btnBackToLeads" data-tooltip="Back to Leads Pipeline">
                <i class="fi fi-rr-arrow-left"></i>
              </a>

              <div class="v3-lead-hero-title-wrap">
                <div class="v3-lead-hero-name-row">
                  <h2 class="v3-lead-hero-name">${lead.name}</h2>
                  <div class="v3-minimal-status-picker">
                    <button type="button" class="v3-minimal-status-trigger ${lead.status_class}" id="btnMinimalStatusTrigger" title="Click to change status">
                      <span class="lead-dot" style="background-color: ${lead.status_color}"></span>
                      <span>${lead.status_label}</span>
                      <i class="fi fi-rr-angle-small-down"></i>
                    </button>

                    <div class="v3-status-dropdown-menu" id="statusDropdownMenu">
                      <div class="v3-status-opt ${st === 0 ? "selected" : ""}" data-status="0">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot fresh"></span>
                          <span>Fresh Lead</span>
                        </div>
                        ${st === 0 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 2 ? "selected" : ""}" data-status="2">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot opportunity"></span>
                          <span>Opportunity</span>
                        </div>
                        ${st === 2 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 3 ? "selected" : ""}" data-status="3">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot followup"></span>
                          <span>Follow-Up</span>
                        </div>
                        ${st === 3 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 7 ? "selected" : ""}" data-status="7">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot duplicate"></span>
                          <span>Duplicate</span>
                        </div>
                        ${st === 7 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 1 ? "selected" : ""}" data-status="1">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot confirmed"></span>
                          <span>Converted (Sale)</span>
                        </div>
                        ${st === 1 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 4 || st === 8 ? "selected" : ""}" data-status="4">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot closed"></span>
                          <span>Lost / Cancelled</span>
                        </div>
                        ${st === 4 || st === 8 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="v3-lead-hero-meta">
                  <span class="v3-hero-meta-item"><i class="fi fi-rr-calendar"></i> Added ${lead.created_at || "Recently"}</span>
                  <span class="v3-meta-sep">•</span>
                  <span class="v3-hero-meta-item"><i class="fi fi-rr-time-check"></i> Updated ${lead.updated_at || lead.created_at || "Recently"}</span>
                  <span class="v3-meta-sep">•</span>
                  <span class="v3-hero-meta-badge source"><i class="fi fi-rr-globe"></i> ${lead.source}</span>
                  ${lead.campaign ? `<span class="v3-meta-sep">•</span><span class="v3-hero-meta-badge campaign"><i class="fi fi-rr-bullhorn"></i> ${lead.campaign}</span>` : ""}
                </div>
              </div>
            </div>

            <div class="v3-lead-hero-actions">
              ${
                lead.wa_mobile
                  ? `
                <a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="v3-btn-contact-action wa" data-tooltip="Chat on WhatsApp">
                  ${WA_SVG} <span>WhatsApp</span>
                </a>
              `
                  : ""
              }
              ${
                lead.mobile
                  ? `
                <a href="tel:${lead.mobile}" class="v3-btn-contact-action call" data-tooltip="Call Primary Phone">
                  <i class="fi fi-rr-phone-call"></i> <span>${lead.mobile}</span>
                </a>
              `
                  : ""
              }
              <button type="button" class="btn-v3-action btn-outline" id="btnHeroEditLead" data-tooltip="Edit Lead & Requirements">
                <i class="fi fi-rr-edit"></i> <span>Edit Lead</span>
              </button>
              ${
                isAssigned
                  ? `
                <button type="button" class="btn-v3-action btn-outline" id="btnHeroSendItinerary" data-tooltip="Search, Customize & Send Itinerary">
                  <i class="fi fi-rr-paper-plane"></i> <span>Send Itinerary</span>
                </button>
                <button type="button" class="btn-v3-action btn-orange" id="btnHeroSetFollowUp" data-tooltip="Set Follow-Up Reminder">
                  <i class="fi fi-rr-alarm-clock"></i> <span>Follow-Up</span>
                </button>
                ${
                  st !== 1
                    ? `
                  <button type="button" class="btn-v3-action btn-green" id="btnHeroConvertToSale" data-tooltip="Convert Lead to Confirmed Sale">
                    <i class="fi fi-rr-check"></i> <span>Convert to Sale</span>
                  </button>
                `
                    : ""
                }
              `
                  : ""
              }
            </div>
          </div>

          <!-- 2. Top 5-Card Snapshot Grid -->
          <div class="v3-lead-snapshot-grid">
            <!-- Card 1: Package / Requirement -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Package / Requirement</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-plane"></i></div>
              </div>
              <div class="v3-snapshot-value" title="${lead.subject || "Custom Package"}">${lead.subject || "Custom Package"}</div>
              <div class="v3-snapshot-sub"><i class="fi fi-rr-calendar"></i> Travel: ${travel.date_of_travel || "Flexible"}</div>
            </div>

            <!-- Card 2: Duration & Guests -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Duration & Guests</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-users"></i></div>
              </div>
              <div class="v3-snapshot-value">${travel.duration_days > 0 ? `${travel.duration_days} Nights` : "Duration N/A"}</div>
              <div class="v3-snapshot-sub">${travel.adults || 1} Adults • ${travel.children || 0} Kids • ${travel.infants || 0} Infants</div>
            </div>

            <!-- Card 3: Next Follow-Up (KPI 1) -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Next Follow-Up</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-alarm-clock"></i></div>
              </div>
              <div class="v3-snapshot-value" title="${followup.time_formatted}">${followup.is_scheduled ? followup.time_formatted : "No Follow-Up Set"}</div>
              <div class="v3-snapshot-sub">
                <span class="v3-kpi-pill ${followup.status_badge}">${followup.label}</span>
              </div>
            </div>

            <!-- Card 4: Lead Age & Activity (KPI 2 & 3) -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Lead Age & Activity</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-time-past"></i></div>
              </div>
              <div class="v3-snapshot-value">${leadAge.text}</div>
              <div class="v3-snapshot-sub"><i class="fi fi-rr-comment-dots"></i> ${touchpoints.summary || `${touchpoints.total} Touchpoints`}</div>
            </div>

            <!-- Card 5: Quotation & Value -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Quotation Value</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-receipt"></i></div>
              </div>
              <div class="v3-snapshot-value">₹ ${parseFloat(quote.price || 0).toLocaleString("en-IN")}</div>
              <div class="v3-snapshot-sub">
                ${quote.min_payment > 0 ? `Min. Deposit: ₹ ${parseFloat(quote.min_payment).toLocaleString("en-IN")}` : "Direct Payment Available"}
              </div>
            </div>
          </div>

          <!-- 3. Main 2-Column Section -->
          <div class="v3-lead-main-layout">
            <!-- Left Area: Tabs for Details, Notes, and Payments -->
            <div class="v3-lead-tabs-card">
              <div class="v3-tabs-header-bar">
                <button type="button" class="v3-tab-link active" data-tab="tab-details">
                  <i class="fi fi-rr-info"></i> Trip & Lead Details
                </button>
                <button type="button" class="v3-tab-link" data-tab="tab-activity">
                  <i class="fi fi-rr-time-past"></i> Activity & Notes (${lead.timeline ? lead.timeline.length : 0})
                </button>
                <button type="button" class="v3-tab-link ${!isAssigned ? "v3-tab-disabled" : ""}" data-tab="tab-payments" ${!isAssigned ? 'disabled title="Lead must be assigned to an agent to access payments"' : ""}>
                  <i class="fi fi-rr-money-check"></i> Payments (${lead.payments ? lead.payments.length : 0}) ${!isAssigned ? '<i class="fi fi-rr-lock v3-tab-lock-icon"></i>' : ""}
                </button>
              </div>

              <div class="v3-tab-body">
                <!-- Tab 1: Trip & Lead Details -->
                <div class="v3-lead-tab-pane active" id="tab-details">
                  <div class="v3-flex-end-row">
                    <button type="button" class="btn-v3-action btn-outline" id="btnTabEditLead">
                      <i class="fi fi-rr-edit"></i> Edit Details
                    </button>
                  </div>

                  <h4 class="v3-info-section-heading">Customer Information</h4>
                  <div class="v3-data-grid">
                    <div class="v3-data-item">
                      <span class="v3-data-label">Customer Name</span>
                      <span class="v3-data-val">${lead.name}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Email Address</span>
                      <span class="v3-data-val">${lead.email ? `<a href="mailto:${lead.email}">${lead.email}</a>` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Primary Mobile</span>
                      <span class="v3-data-val">${lead.mobile ? `<a href="tel:${lead.mobile}">${lead.mobile}</a>` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Alternate Mobile</span>
                      <span class="v3-data-val">${lead.alt_mobile || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Current City / Location</span>
                      <span class="v3-data-val">${travel.current_location || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Inquiry Date</span>
                      <span class="v3-data-val">${lead.created_at || "-"}</span>
                    </div>
                  </div>

                  <h4 class="v3-info-section-heading">Travel Logistics</h4>
                  <div class="v3-data-grid">
                    <div class="v3-data-item">
                      <span class="v3-data-label">Package / Destination</span>
                      <span class="v3-data-val">${lead.subject || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Date of Travel</span>
                      <span class="v3-data-val">${travel.date_of_travel || "Flexible"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Trip Duration</span>
                      <span class="v3-data-val">${travel.duration_days > 0 ? `${travel.duration_days} Nights` : "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Total Travelers</span>
                      <span class="v3-data-val">${travel.adults || 1} Adults, ${travel.children || 0} Kids, ${travel.infants || 0} Infants</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Pickup Location</span>
                      <span class="v3-data-val">${travel.pickup_location || "-"}</span>
                    </div>
                    <div class="v3-data-item">
                      <span class="v3-data-label">Drop Location</span>
                      <span class="v3-data-val">${travel.drop_location || "-"}</span>
                    </div>
                  </div>

                  ${
                    travel.requirements
                      ? `
                    <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
                    <div class="v3-requirements-box">${travel.requirements}</div>
                  `
                      : ""
                  }
                </div>

                <!-- Tab 2: Activity & Notes (HubSpot/Linear Style) -->
                <div class="v3-lead-tab-pane" id="tab-activity">
                  <!-- Quick Composer Box -->
                  ${
                    isAssigned
                      ? `
                  <div class="v3-note-composer-card">
                    <div class="v3-composer-type-nav">
                      <button type="button" class="v3-composer-type-btn active" data-type="note">
                        <i class="fi fi-rr-comment-alt"></i> Add Note
                      </button>
                      <button type="button" class="v3-composer-type-btn" data-type="followup">
                        <i class="fi fi-rr-alarm-clock"></i> Set Follow-Up
                      </button>
                    </div>

                    <textarea id="composerNoteInput" class="v3-composer-input" placeholder="Type a note, call log, or customer update..."></textarea>

                    <div class="v3-composer-followup-row" id="composerFollowupRow">
                      <div class="v3-modal-form-group">
                        <label class="v3-modal-label">Reminder Date & Time *</label>
                        <input type="datetime-local" id="composerFollowupTime" class="v3-modal-input" value="${new Date(Date.now() + 86400000).toISOString().slice(0, 16)}">
                      </div>
                      <div class="v3-modal-form-group">
                        <label class="v3-modal-label">Reason / Objective *</label>
                        <select id="composerFollowupReason" class="leads-filter-select v3-select-full">
                          <option value="Interested - Price Discussion">Price Discussion</option>
                          <option value="Itinerary Customization">Itinerary Customization</option>
                          <option value="Call Back Request">Call Back Request</option>
                          <option value="Payment Follow-up">Payment Follow-up</option>
                          <option value="General Follow-up" selected>General Follow-up</option>
                        </select>
                      </div>
                    </div>

                    <div class="v3-composer-footer">
                      <button type="button" class="btn-v3-action btn-green" id="btnSubmitComposer">
                        <i class="fi fi-rr-check"></i> Post Update
                      </button>
                    </div>
                  </div>
                  `
                      : `
                  <div class="v3-composer-disabled">
                    <div class="v3-unassigned-banner">
                      <div class="v3-unassigned-banner-icon"><i class="fi fi-rr-lock"></i></div>
                      <div class="v3-unassigned-banner-text">
                        <h5>Activity & Notes Form Disabled</h5>
                        <p>This lead is currently unassigned. Please assign an agent or representative to log call notes or schedule follow-up reminders.</p>
                      </div>
                    </div>
                  </div>
                  `
                  }

                  <!-- Timeline Stream -->
                  ${timelineHtml}
                </div>

                <!-- Tab 3: Payment History -->
                <div class="v3-lead-tab-pane" id="tab-payments">
                  ${
                    isAssigned
                      ? `
                    ${paymentsHtml}
                  `
                      : `
                    <div class="v3-leads-empty-state v3-empty-compact">
                      <div class="v3-empty-icon-wrap v3-empty-icon-compact"><i class="fi fi-rr-lock"></i></div>
                      <h4 class="v3-empty-title">Payments Disabled</h4>
                      <p class="v3-empty-subtitle">This lead is unassigned. Please assign an agent or representative before recording or managing payments.</p>
                    </div>
                  `
                  }
                </div>
              </div>
            </div>

            <!-- Right Area: Sidebar Panels -->
            <div class="v3-sidebar-panel">
              ${agentSidebarHtml}

              <!-- Fast Actions Sidebar for Telecallers -->
              <div class="v3-sidebar-card v3-fast-actions-card">
                <div class="v3-sidebar-card-title">
                  <span>Fast Actions</span>
                  <i class="fi fi-rr-bolt"></i>
                </div>
                ${
                  isAssigned
                    ? `
                  <div class="v3-fast-actions-grid">
                    ${
                      lead.wa_mobile
                        ? `
                      <a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="btn-fast-tile tile-wa" data-tooltip="Chat on WhatsApp">
                        <div class="fast-tile-icon">${WA_SVG}</div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">WhatsApp Chat</span>
                          <span class="fast-tile-sub">Send instant message</span>
                        </div>
                      </a>
                    `
                        : ""
                    }

                    ${
                      lead.mobile
                        ? `
                      <a href="tel:${lead.mobile}" class="btn-fast-tile tile-call" data-tooltip="Call customer mobile">
                        <div class="fast-tile-icon"><i class="fi fi-rr-phone-call"></i></div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">Call Customer</span>
                          <span class="fast-tile-sub">${lead.mobile}</span>
                        </div>
                      </a>
                    `
                        : ""
                    }

                    <button type="button" class="btn-fast-tile tile-followup" id="btnSideSetFollowUp" data-tooltip="Schedule next callback reminder">
                      <div class="fast-tile-icon"><i class="fi fi-rr-alarm-clock"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Schedule Follow-Up</span>
                        <span class="fast-tile-sub">Set reminder / call later</span>
                      </div>
                    </button>

                    <button type="button" class="btn-fast-tile tile-itinerary" id="btnSideSendItinerary" data-tooltip="Create and send tour proposal">
                      <div class="fast-tile-icon"><i class="fi fi-rr-paper-plane"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Send Tour Itinerary</span>
                        <span class="fast-tile-sub">Build & share quotation</span>
                      </div>
                    </button>

                    <button type="button" class="btn-fast-tile tile-edit" id="btnSideEditLead" data-tooltip="Edit customer information and trip requirements">
                      <div class="fast-tile-icon"><i class="fi fi-rr-edit"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Edit Lead & Travel</span>
                        <span class="fast-tile-sub">Update customer needs</span>
                      </div>
                    </button>

                    <button type="button" class="btn-fast-tile tile-pay-record" id="btnSideRecordPayment" data-tooltip="Record payment received from client">
                      <div class="fast-tile-icon"><i class="fi fi-rr-indian-rupee-sign"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Record Payment</span>
                        <span class="fast-tile-sub">Log received payment</span>
                      </div>
                    </button>

                    <button type="button" class="btn-fast-tile tile-pay-link" id="btnSideSendPaymentLink" data-tooltip="Generate and share payment link">
                      <div class="fast-tile-icon"><i class="fi fi-rr-link"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Send Payment Link</span>
                        <span class="fast-tile-sub">Request online deposit</span>
                      </div>
                    </button>

                    ${
                      st !== 1
                        ? `
                      <button type="button" class="btn-fast-tile tile-convert" id="btnSideConvertToSale" data-tooltip="Mark lead as confirmed booking">
                        <div class="fast-tile-icon"><i class="fi fi-rr-check-circle"></i></div>
                        <div class="fast-tile-meta">
                          <span class="fast-tile-title">Convert to Confirmed Sale</span>
                          <span class="fast-tile-sub">Confirm tour booking</span>
                        </div>
                      </button>
                    `
                        : ""
                    }

                    <button type="button" class="btn-fast-tile tile-close" id="btnSideCloseLeadModal" data-tooltip="Archive or close lead with reason">
                      <div class="fast-tile-icon"><i class="fi fi-rr-cross-circle"></i></div>
                      <div class="fast-tile-meta">
                        <span class="fast-tile-title">Close / Lost Lead</span>
                        <span class="fast-tile-sub">Mark cancelled / not interested</span>
                      </div>
                    </button>
                  </div>
                `
                    : `
                  <div class="v3-unassigned-lead-banner">
                    <div class="v3-unassigned-icon"><i class="fi fi-rr-lock"></i></div>
                    <h5 class="v3-unassigned-title">Lead Unassigned</h5>
                    <p class="v3-unassigned-desc">Fast Actions will be visible and enabled once this lead is assigned to a representative.</p>
                  </div>
                `
                }
              </div>
            </div>
          </div>
        </div>
      `;

      $container.html(html);

      // Initialize Single Datepickers
      self.initSingleDatePickers();

      if (typeof $.fn.select2 !== "undefined") {
        if (canManageAgent && $("#leadDetailAgentSelect").length) {
          $("#leadDetailAgentSelect").select2({
            dropdownAutoWidth: true,
            width: "100%",
          });
        }
        if ($("#composerFollowupReason").length) {
          $("#composerFollowupReason").select2({
            dropdownAutoWidth: true,
            width: "100%",
          });
        }
      }
    },

    // Render structured day cards inside the full screen itinerary customizer
    renderItineraryDays: function (days) {
      const self = this;
      const $stack = $("#itinDaysStack");
      $stack.empty();

      if (!days || days.length === 0) {
        days = [
          {
            day: "Day 1 - Arrival & Sightseeing",
            overview:
              "Arrive at destination, transfer to hotel, and explore local sights.",
          },
          {
            day: "Day 2 - Full Day Tour & Activities",
            overview: "Enjoy full day guided tour and iconic attractions.",
          },
          {
            day: "Day 3 - Departure",
            overview:
              "Check out from hotel and transfer to airport/station for departure.",
          },
        ];
      }

      days.forEach((d, idx) => {
        self.appendDayCard(d, idx + 1);
      });
    },

    appendDayCard: function (d, num) {
      const title = d.day || `Day ${num} - Tour Plan`;
      const overview = d.overview || "";
      const hotel = d.hotel || "";
      const hotelStars = d.hotel_stars || "4";
      const roomType = d.room_type || "Deluxe Room";
      const mealPlan = d.meal_plan || "CP";
      const vehicle = d.vehicle || "Private Sedan";
      const transferNotes = d.transfer_notes || "";

      const html = `
        <div class="v3-itin-day-card" data-day-index="${num}">
          <div class="v3-itin-day-card-header">
            <span class="v3-itin-day-badge">Day ${num}</span>
            <input type="text" class="v3-itin-day-title-input itin-day-title" value="${title}" placeholder="e.g. Day ${num} - Arrival & City Tour">
            <button type="button" class="v3-itin-day-delete-btn btn-delete-itin-day" title="Delete Day ${num}">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>

          <div class="v3-modal-form-group">
            <label class="v3-modal-label">Day Schedule & Activities *</label>
            <textarea class="v3-modal-textarea itin-day-overview" rows="3" placeholder="Describe the day's schedule, sightseeing spots, and activities...">${overview}</textarea>
          </div>

          <div class="v3-itin-meta-boxes-grid">
            <!-- Hotel & Accommodation -->
            <div class="v3-itin-sub-box">
              <div class="v3-itin-sub-box-title">
                <i class="fi fi-rr-bed"></i> Hotel & Stay
              </div>
              <div class="v3-modal-form-group">
                <input type="text" class="v3-modal-input itin-day-hotel-name" value="${hotel}" placeholder="Hotel Name (e.g. Holiday Inn / Grand Palace)">
              </div>
              <div class="v3-form-grid-3">
                <div>
                  <label class="v3-modal-label">Star Rating</label>
                  <select class="leads-filter-select v3-select-full itin-day-hotel-stars">
                    <option value="3" ${hotelStars === "3" ? "selected" : ""}>3 Star</option>
                    <option value="4" ${hotelStars === "4" ? "selected" : ""}>4 Star</option>
                    <option value="5" ${hotelStars === "5" ? "selected" : ""}>5 Star</option>
                    <option value="luxury" ${hotelStars === "luxury" ? "selected" : ""}>Luxury</option>
                  </select>
                </div>
                <div>
                  <label class="v3-modal-label">Room Type</label>
                  <input type="text" class="v3-modal-input itin-day-room-type" value="${roomType}" placeholder="Deluxe">
                </div>
                <div>
                  <label class="v3-modal-label">Meal Plan</label>
                  <select class="leads-filter-select v3-select-full itin-day-meal-plan">
                    <option value="EP" ${mealPlan === "EP" ? "selected" : ""}>EP (Room Only)</option>
                    <option value="CP" ${mealPlan === "CP" ? "selected" : ""}>CP (Breakfast)</option>
                    <option value="MAP" ${mealPlan === "MAP" ? "selected" : ""}>MAP (Breakfast + Dinner)</option>
                    <option value="AP" ${mealPlan === "AP" ? "selected" : ""}>AP (All Meals)</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Vehicle & Transport -->
            <div class="v3-itin-sub-box">
              <div class="v3-itin-sub-box-title">
                <i class="fi fi-rr-car"></i> Vehicle & Transfers
              </div>
              <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transport Type</label>
                <select class="leads-filter-select v3-select-full itin-day-vehicle-type">
                  <option value="Private Sedan" ${vehicle.includes("Sedan") ? "selected" : ""}>Private Sedan (Dzire / Etios)</option>
                  <option value="SUV / Innova" ${vehicle.includes("SUV") || vehicle.includes("Innova") ? "selected" : ""}>SUV (Innova / Ertiga)</option>
                  <option value="Tempo Traveller" ${vehicle.includes("Tempo") ? "selected" : ""}>Tempo Traveller</option>
                  <option value="Luxury Coach" ${vehicle.includes("Coach") ? "selected" : ""}>Luxury Coach</option>
                  <option value="Speedboat / Ferry" ${vehicle.includes("Boat") || vehicle.includes("Ferry") ? "selected" : ""}>Speedboat / Ferry</option>
                  <option value="Flight / Air Transfer" ${vehicle.includes("Flight") ? "selected" : ""}>Flight / Air Transfer</option>
                </select>
              </div>
              <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transfer Route / Notes</label>
                <input type="text" class="v3-modal-input itin-day-transfer-notes" value="${transferNotes}" placeholder="e.g. Airport to Hotel Transfer & Evening City Drive">
              </div>
            </div>
          </div>
        </div>
      `;

      $("#itinDaysStack").append(html);
    },

    reindexDays: function () {
      $("#itinDaysStack .v3-itin-day-card").each(function (idx) {
        const num = idx + 1;
        $(this).attr("data-day-index", num);
        $(this).find(".v3-itin-day-badge").text(`Day ${num}`);
        $(this).find(".btn-delete-itin-day").attr("title", `Delete Day ${num}`);
      });
    },

    bindEvents: function () {
      const self = this;

      // 1. Tab Navigation
      $(document).on("click", ".btn-copy-itin-link", function (e) {
        e.preventDefault();
        const url = $(this).data("url") || $(this).attr("data-url");
        if (!url) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(function () {
            self.notify("Itinerary link copied to clipboard!", "success");
          }).catch(function () {
            self.notify("Failed to copy itinerary link.", "error");
          });
        } else {
          const $temp = $("<input>");
          $("body").append($temp);
          $temp.val(url).select();
          document.execCommand("copy");
          $temp.remove();
          self.notify("Itinerary link copied to clipboard!", "success");
        }
      });

      $(document).on("click", ".v3-tab-link", function () {
        if ($(this).hasClass("v3-tab-disabled") || $(this).prop("disabled")) {
          self.notify("Please assign an agent to this lead before accessing payments.", "warning");
          return;
        }
        const tabId = $(this).data("tab");
        $(".v3-tab-link").removeClass("active");
        $(this).addClass("active");
        $(".v3-lead-tab-pane").removeClass("active");
        $(`#${tabId}`).addClass("active");
      });

      // 2. Minimal Status Picker Toggle & Selection
      $(document).on("click", "#btnMinimalStatusTrigger", function (e) {
        e.stopPropagation();
        $("#statusDropdownMenu").toggleClass("is-open");
      });

      $(document).on("click", function (e) {
        if (!$(e.target).closest(".v3-minimal-status-picker").length) {
          $("#statusDropdownMenu").removeClass("is-open");
        }
      });

      $(document).on("click", ".v3-status-opt", function () {
        const targetStatus = parseInt($(this).data("status"));
        $("#statusDropdownMenu").removeClass("is-open");

        const currentStatus = self.leadData
          ? parseInt(self.leadData.status)
          : 0;
        if (targetStatus === currentStatus) return;

        if (targetStatus === 1) {
          self.confirmConvertToSale();
        } else if (targetStatus === 4) {
          $("#modalCloseLead").addClass("is-active");
        } else {
          self.updateStatus(targetStatus);
        }
      });

      // 3. Composer Switch (Note vs Follow-Up)
      $(document).on("click", ".v3-composer-type-btn", function () {
        const type = $(this).data("type");
        self.activeComposerType = type;
        $(".v3-composer-type-btn").removeClass("active");
        $(this).addClass("active");

        if (type === "followup") {
          $("#composerFollowupRow").addClass("is-visible");
          $("#composerNoteInput").attr(
            "placeholder",
            "Add instructions for this follow-up reminder...",
          );
          $("#btnSubmitComposer").html(
            '<i class="fi fi-rr-alarm-clock"></i> Set Reminder',
          );
        } else {
          $("#composerFollowupRow").removeClass("is-visible");
          $("#composerNoteInput").attr(
            "placeholder",
            "Type a note, call log, or customer update...",
          );
          $("#btnSubmitComposer").html(
            '<i class="fi fi-rr-check"></i> Post Note',
          );
        }
      });

      // 4. Submit Composer Update
      $(document).on("click", "#btnSubmitComposer", function () {
        if (!self.leadData || !(parseInt(self.leadData.agent_id || 0) > 0)) {
          self.notify("Lead is unassigned. Please assign an agent first.", "warning");
          return;
        }

        const msg = $("#composerNoteInput").val().trim();
        const type = self.activeComposerType;

        if (type === "note" && !msg) {
          self.notify("Please type a note before posting.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Posting...");

        const postData = {
          leadId: self.leadId,
          title:
            type === "followup" ? $("#composerFollowupReason").val() : "Note",
          msg: msg || "Follow-up reminder set",
        };

        if (type === "followup") {
          postData.followupTime = $("#composerFollowupTime").val();
        }

        $.ajax({
          url: "/api/v3/leads/add-note/",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function (res) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Post Update');
            if (res.status === "success") {
              $("#composerNoteInput").val("");
              self.notify(res.message || "Update logged successfully", "success");
              
              if (res.followup_time) {
                self.scheduleClientReminder(res.followup_time, self.leadData ? self.leadData.name : "Customer");
              }
              
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to post update.", "error");
            }
          },
          error: function () {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Post Update');
            self.notify("Network connection error.", "error");
          },
        });
      });

      // 5. Agent Re-assign with SweetAlert Confirmation
      $(document).on("change", "#leadDetailAgentSelect", function () {
        const newAgentId = $(this).val();
        const newAgentName = $(this).find("option:selected").text();
        const currentAgentId = self.leadData ? self.leadData.agent_id : 0;

        if (parseInt(newAgentId) === parseInt(currentAgentId)) return;

        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
          Swal.fire({
            title: "Reassign Lead?",
            text: `Assign this lead to ${newAgentName}?`,
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0284c7",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Reassign",
            cancelButtonText: "Cancel",
          }).then((res) => {
            if (res.isConfirmed || res.value) {
              self.performAgentAssign(newAgentId);
            } else {
              $("#leadDetailAgentSelect")
                .val(currentAgentId)
                .trigger("change.select2");
            }
          });
        } else if (confirm(`Assign this lead to ${newAgentName}?`)) {
          self.performAgentAssign(newAgentId);
        } else {
          $("#leadDetailAgentSelect")
            .val(currentAgentId)
            .trigger("change.select2");
        }
      });

      // 6. Convert to Sale
      $(document).on("click", "#btnHeroConvertToSale, #btnSideConvertToSale", function () {
        self.confirmConvertToSale();
      });

      // 7. Edit Lead Modal Trigger & Submission
      $(document).on(
        "click",
        "#btnHeroEditLead, #btnTabEditLead, #btnSideEditLead",
        function () {
          if (!self.leadData) return;
          const d = self.leadData;
          const t = d.travel_details || {};

          let fName = (d.first_name || d.name || "").trim();
          let lName = (d.last_name || "").trim();
          if (!lName && fName.indexOf(" ") !== -1) {
            const parts = fName.split(/\s+/);
            fName = parts[0];
            lName = parts.slice(1).join(" ");
          }

          $("#editLeadId").val(d.id || self.leadId || "");
          $("#editLeadFirstName").val(fName);
          $("#editLeadLastName").val(lName);
          $("#editLeadEmail").val(d.email || "");
          $("#editLeadMobile").val(d.mobile || "");
          $("#editLeadAltMobile").val(d.alt_mobile || "");

          $("#editLeadSubject").val(d.subject || "");
          $("#editLeadTravelDate").val(t.date_of_travel || "");
          $("#editLeadDuration").val(t.duration_days || 0);

          $("#editLeadAdults").val(t.adults || 1);
          $("#editLeadChildren").val(t.children || 0);
          $("#editLeadInfants").val(t.infants || 0);

          $("#editLeadRequirements").val(t.requirements || "");

          $("#modalEditLead").addClass("is-active");

          // Initialize Select2 for Tour Package / Destination via Packages API
          self.initPackageSelect2(
            $("#editLeadPackage"),
            $("#modalEditLead"),
            "Choose a tour package or type custom destination...",
          );

          // Initialize Select2 with Cities API for locations
          self.initCitySelect2(
            $("#editLeadCurrentLocation"),
            $("#modalEditLead"),
            "-- Search / Select Current Location --",
          );
          self.initCitySelect2(
            $("#editLeadPickup"),
            $("#modalEditLead"),
            "-- Search / Select Pickup Point / City --",
          );
          self.initCitySelect2(
            $("#editLeadDrop"),
            $("#modalEditLead"),
            "-- Search / Select Drop Point / City --",
          );

          // Helper to set Select2 option value
          function setSelect2Val($el, val, text) {
            if (!$el || !$el.length) return;
            $el.empty();
            if (val) {
              $el.append(new Option(text || val, val, true, true));
            }
            $el.val(val || "").trigger("change");
          }

          const pickupVal = t.pickup_location || "Delhi";
          const dropVal = t.drop_location || pickupVal;

          setSelect2Val($("#editLeadCurrentLocation"), t.current_location || "");
          setSelect2Val($("#editLeadPickup"), pickupVal, pickupVal);
          setSelect2Val($("#editLeadDrop"), dropVal, dropVal);
          $("#editLeadDrop").data("userModified", t.drop_location && t.drop_location !== pickupVal);

          const initialSubject = (d.subject || "").trim();
          const initialDest = (t.destination || "").trim();
          const initialPkgVal = initialSubject || initialDest;

          if (initialPkgVal) {
            setSelect2Val($("#editLeadPackage"), initialPkgVal, initialPkgVal);

            // Fetch package details from API to populate snapshot card if exact or matching package
            $.ajax({
              url: "/api/v3/packages/options/?q=" + encodeURIComponent(initialPkgVal),
              dataType: "json",
              success: function (res) {
                if (res && res.data && res.data.all && res.data.all.length > 0) {
                  const match = res.data.all.find(
                    (p) =>
                      p.title.toLowerCase() === initialPkgVal.toLowerCase() ||
                      p.city.toLowerCase() === initialPkgVal.toLowerCase() ||
                      initialPkgVal.toLowerCase().includes(p.title.toLowerCase())
                  ) || res.data.all[0];

                  if (match) {
                    const isIntl = (match.type || "").toLowerCase() === "international";
                    const dur = match.duration || 0;
                    const days = match.days || (dur > 0 ? dur + 1 : 1);
                    $("#editCardPkgTitle").text(match.title);
                    $("#editCardPkgTypeBadge")
                      .text(isIntl ? "International" : "Domestic")
                      .attr("class", `v3-pkg-type-pill ${isIntl ? "intl" : "dom"}`);
                    $("#editCardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${dur}N / ${days}D</strong>`);
                    $("#editCardPkgLocation").html(`<i class="fi fi-rr-marker"></i> ${match.city}`);
                    $("#editCardPkgTheme").html(`<i class="fi fi-rr-star"></i> ${match.theme}`);
                    $("#editCardPkgIcon").html(`<i class="fi ${isIntl ? 'fi-rr-plane' : 'fi-rr-route'}"></i>`);
                    $("#editLeadSelectedPkgCard").show();
                  }
                }
              }
            });
          } else {
            setSelect2Val($("#editLeadPackage"), "");
            $("#editLeadSelectedPkgCard").hide();
          }

          self.initSingleDatePickers();
          if (t.date_of_travel && $("#editLeadTravelDate").data("daterangepicker")) {
            if (typeof moment !== "undefined" && moment(t.date_of_travel, "YYYY-MM-DD", true).isValid()) {
              $("#editLeadTravelDate").data("daterangepicker").setStartDate(moment(t.date_of_travel, "YYYY-MM-DD"));
              $("#editLeadTravelDate").data("daterangepicker").setEndDate(moment(t.date_of_travel, "YYYY-MM-DD"));
            }
          }
        },
      );

      // Package select change in Edit Lead Modal: Update Snapshot Card, Duration, and Subject
      $(document).on("select2:select", "#editLeadPackage", function (e) {
        const data = e.params ? e.params.data : null;
        if (!data) return;

        const isIntl = (data.type || "").toLowerCase() === "international";
        const duration = parseInt(data.duration) || 0;
        const days = data.days || (duration > 0 ? duration + 1 : 1);
        const city = data.city || data.text || "";
        const theme = data.theme || "Standard";

        // Auto-fill Package / Subject
        $("#editLeadSubject").val(data.text || data.title || city);

        // Auto-fill duration if package has duration
        if (duration > 0) {
          $("#editLeadDuration").val(duration);
        }

        // Render selected package snapshot card
        if (data.pkg_id) {
          $("#editCardPkgTitle").text(data.text || data.title);
          $("#editCardPkgTypeBadge")
            .text(isIntl ? "International" : "Domestic")
            .attr("class", `v3-pkg-type-pill ${isIntl ? "intl" : "dom"}`);
          $("#editCardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${duration}N / ${days}D</strong>`);
          $("#editCardPkgLocation").html(`<i class="fi fi-rr-marker"></i> ${city}`);
          $("#editCardPkgTheme").html(`<i class="fi fi-rr-star"></i> ${theme}`);
          $("#editCardPkgIcon").html(`<i class="fi ${isIntl ? 'fi-rr-plane' : 'fi-rr-route'}"></i>`);
          $("#editLeadSelectedPkgCard").slideDown(150);
        } else {
          $("#editLeadSelectedPkgCard").slideUp(150);
        }
      });

      // Clear package snapshot card in modal
      $(document).on("click", "#editBtnClearPkgCard", function () {
        $("#editLeadPackage").val(null).trigger("change");
        $("#editLeadSelectedPkgCard").slideUp(150);
      });

      $(document).on("change", "#editLeadPackage", function () {
        if (!$(this).val()) {
          $("#editLeadSelectedPkgCard").slideUp(150);
        }
      });

      // Auto-default drop point to chosen pickup point in modal
      $(document).on("change select2:select", "#editLeadPickup", function () {
        const pickupVal = $(this).val();
        if (pickupVal) {
          const currentDrop = $("#editLeadDrop").val();
          if (!currentDrop || currentDrop === pickupVal || !$("#editLeadDrop").data("userModified")) {
            $("#editLeadDrop")
              .empty()
              .append(new Option(pickupVal, pickupVal, true, true))
              .trigger("change");
          }
        }
      });

      $(document).on("select2:select change", "#editLeadDrop", function () {
        const dropVal = $(this).val();
        const pickupVal = $("#editLeadPickup").val();
        if (dropVal && dropVal !== pickupVal) {
          $("#editLeadDrop").data("userModified", true);
        }
      });

      // Split name when space entered in First Name if Last Name is empty
      $(document).on("blur change", "#editLeadFirstName", function () {
        const val = $(this).val().trim();
        const currentLast = $("#editLeadLastName").val().trim();
        if (!currentLast && val.indexOf(" ") !== -1) {
          const parts = val.split(/\s+/);
          if (parts.length > 1) {
            $(this).val(parts[0]);
            $("#editLeadLastName").val(parts.slice(1).join(" "));
          }
        }
      });

      // Submit Edit Lead
      $(document).on("click", "#btnSubmitModalEditLead", function () {
        const currentLeadId =
          $("#editLeadId").val() ||
          self.leadId ||
          self.getLeadId() ||
          (self.leadData ? self.leadData.id || self.leadData.encoded_id : "");
        if (!currentLeadId) {
          self.notify("Lead ID is missing. Please reload the page.", "error");
          return;
        }

        let firstName = $("#editLeadFirstName").val().trim();
        let lastName = $("#editLeadLastName").val().trim();

        if (!lastName && firstName.indexOf(" ") !== -1) {
          const parts = firstName.split(/\s+/);
          firstName = parts[0];
          lastName = parts.slice(1).join(" ");
          $("#editLeadFirstName").val(firstName);
          $("#editLeadLastName").val(lastName);
        }
        const mobile = $("#editLeadMobile").val().trim();

        if (!firstName) {
          self.notify("First name is required.", "warning");
          return;
        }
        if (!mobile) {
          self.notify("Primary mobile is required.", "warning");
          return;
        }

        const travelDate = $("#editLeadTravelDate").val();
        if (travelDate && typeof moment !== "undefined") {
          const mDate = moment(travelDate, "YYYY-MM-DD", true);
          if (mDate.isValid() && mDate.isBefore(moment().startOf("day"))) {
            self.notify("Date of travel must be today or a future date.", "warning");
            return;
          }
        }

        const packageVal = $("#editLeadPackage").val() || "";
        let subjectVal = $("#editLeadSubject").val().trim();
        if (!subjectVal && packageVal) {
          subjectVal = packageVal;
          $("#editLeadSubject").val(subjectVal);
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");

        const postData = {
          leadId: currentLeadId,
          lead_id: currentLeadId,
          id: currentLeadId,
          enquiry_id: currentLeadId,
          first_name: firstName,
          last_name: $("#editLeadLastName").val().trim(),
          email: $("#editLeadEmail").val().trim(),
          mobile: mobile,
          alt_mobile: $("#editLeadAltMobile").val().trim(),
          current_location: $("#editLeadCurrentLocation").val() || "",
          destination: packageVal,
          destination_city: packageVal,
          city: packageVal,
          subject: subjectVal || packageVal,
          package: packageVal || subjectVal,
          date_of_travel: $("#editLeadTravelDate").val(),
          duration_days: $("#editLeadDuration").val(),
          pickup_location: $("#editLeadPickup").val() || "",
          drop_location: $("#editLeadDrop").val() || "",
          adults: $("#editLeadAdults").val(),
          children: $("#editLeadChildren").val(),
          infants: $("#editLeadInfants").val(),
          requirements: $("#editLeadRequirements").val().trim(),
        };

        $.ajax({
          url: "/api/v3/leads/update/",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function (res) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Save Changes');
            if (res.status === "success") {
              $("#modalEditLead").removeClass("is-active");
              self.notify("Lead updated successfully", "success");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to update lead.", "error");
            }
          },
          error: function (xhr) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-check"></i> Save Changes');
            const err =
              xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : "Network error while updating lead.";
            self.notify(err, "error");
          },
        });
      });

      // 8. Full Screen Itinerary Modal Trigger & Logic
      $(document).on(
        "click",
        "#btnHeroSendItinerary, #btnSideSendItinerary",
        function () {
          const d = self.leadData || {};
          const t = d.travel_details || {};
          const dest = (t.destination || d.subject || "").trim();

          $("#modalSendItinerary").addClass("is-active");

          // Reset state
          $("#itinerarySearchLoader").addClass("v3-hidden");
          $("#itineraryNotFoundBox").addClass("v3-hidden");
          $("#itineraryResultContainer").addClass("v3-hidden");
          $("#itineraryCustomSection").addClass("v3-hidden");
          $("#btnPreviewItinerary").addClass("v3-hidden");
          $("#itineraryPackageSelect").html(
            '<option value="">-- Choose an Itinerary Package --</option>',
          );
          self.selectedPackage = null;

          // Initialize V3 Cities API for destination
          self.initCitySelect2(
            $("#itineraryDestinationInput"),
            $("#modalSendItinerary"),
            "-- Search or Select Destination City / Country --",
          );

          if (self.leadData) {
            $("#itinModalLeadBadge").text(
              `Lead #${self.leadData.id}: ${self.leadData.name}`,
            );
            if (dest) {
              const $destInput = $("#itineraryDestinationInput");
              $destInput.empty().append(new Option(dest, dest, true, true)).trigger("change");
            }
          }

          // Initialize Select2 for travel style
          if (typeof $.fn.select2 !== "undefined") {
            $("#itineraryTravelStyle").select2({
              dropdownParent: $("#modalSendItinerary"),
              width: "100%",
              minimumResultsForSearch: Infinity,
            });
          }
        },
      );

      // Find Itineraries in Modal
      $(document).on("click", "#btnFindItineraries", function () {
        const dest = $("#itineraryDestinationInput").val();
        const theme = $("#itineraryTravelStyle").val() || "";

        if (!dest) {
          self.notify("Please select or type a destination.", "warning");
          return;
        }

        const $btn = $(this);
        $btn
          .prop("disabled", true)
          .html('<i class="fi fi-rr-spinner fi-spin"></i> Searching...');

        $("#itinerarySearchLoaderText").text(
          `Searching matching tour packages for "${dest}"...`,
        );
        $("#itinerarySearchLoader").removeClass("v3-hidden");
        $("#itineraryNotFoundBox").addClass("v3-hidden");
        $("#itineraryResultContainer").addClass("v3-hidden");
        $("#itineraryCustomSection").addClass("v3-hidden");
        $("#btnPreviewItinerary").addClass("v3-hidden");

        $.ajax({
          url: "/api/v3/itinerary/list/",
          type: "GET",
          data: {
            destination: dest,
            theme: theme,
          },
          dataType: "json",
          success: function (res) {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");

            if (
              res &&
              res.status === "success" &&
              res.itineraryList &&
              res.itineraryList.length > 0
            ) {
              self.availablePackages = res.itineraryList;
              $("#itineraryNotFoundBox").addClass("v3-hidden");
              $("#itineraryPackageCountBadge").text(
                `${res.itineraryList.length} Packages Found`,
              );

              let options = `<option value="">-- Choose an Itinerary Package (${res.itineraryList.length} Available) --</option>`;
              res.itineraryList.forEach(function (pkg, idx) {
                const adultRate = parseFloat(
                  pkg.tour_package_price_adult || 0,
                ).toLocaleString("en-IN");
                let duration = pkg.tour_package_duration
                  ? String(pkg.tour_package_duration).trim()
                  : "Standard";
                if (/^\d+$/.test(duration)) {
                  const d = parseInt(duration, 10);
                  duration = d > 1 ? `${d} Days / ${d - 1} Nights` : `${d} Day`;
                }
                options += `<option value="${idx}" data-title="${pkg.tour_package_title}" data-duration="${duration}" data-price="${adultRate}">${pkg.tour_package_title} • ${duration} • ₹${adultRate}/person</option>`;
              });
              options += `<option value="custom" data-title="+ Create Custom Itinerary from Scratch" data-duration="Custom" data-price="0">+ Create Custom Itinerary from Scratch</option>`;

              const $pkgSelect = $("#itineraryPackageSelect");
              if ($pkgSelect.hasClass("select2-hidden-accessible")) {
                $pkgSelect.select2("destroy");
              }

              $pkgSelect.html(options).val("");
              $("#itineraryResultContainer").removeClass("v3-hidden");

              if (typeof $.fn.select2 !== "undefined") {
                $pkgSelect.select2({
                  dropdownParent: $("#modalSendItinerary"),
                  width: "100%",
                  placeholder: "Choose an Itinerary Package...",
                  templateResult: function (opt) {
                    if (!opt.id || opt.id === "") return opt.text;
                    const $el = $(opt.element);
                    const title = $el.data("title") || opt.text;
                    const duration = $el.data("duration") || "";
                    const price = $el.data("price") || "";

                    if (opt.id === "custom") {
                      return $(`
                        <div class="v3-select2-pkg-row">
                          <span class="v3-select2-pkg-title"><i class="fi fi-rr-plus"></i> ${title}</span>
                        </div>
                      `);
                    }

                    return $(`
                      <div class="v3-select2-pkg-row">
                        <span class="v3-select2-pkg-title">${title}</span>
                        <div class="v3-select2-pkg-meta">
                          ${duration ? `<span class="v3-select2-pkg-badge"><i class="fi fi-rr-calendar"></i> ${duration}</span>` : ""}
                          <span class="v3-select2-pkg-price">₹${price} / person</span>
                        </div>
                      </div>
                    `);
                  },
                  templateSelection: function (opt) {
                    if (!opt.id || opt.id === "") return opt.text;
                    const $el = $(opt.element);
                    const title = $el.data("title") || opt.text;
                    const duration = $el.data("duration") || "";
                    const price = $el.data("price") || "";
                    if (opt.id === "custom") return title;
                    return $(
                      `<span><strong>${title}</strong> ${duration ? `(${duration})` : ""} • ₹${price}/person</span>`,
                    );
                  },
                });
              }
            } else {
              // Show Inline No Package Found Box
              self.availablePackages = [];
              $("#itineraryResultContainer").addClass("v3-hidden");
              $("#itineraryCustomSection").addClass("v3-hidden");
              $("#btnPreviewItinerary").addClass("v3-hidden");
              $("#itineraryNotFoundDesc").text(
                `No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`,
              );
              $("#itineraryNotFoundBox").removeClass("v3-hidden");
            }
          },
          error: function () {
            $btn
              .prop("disabled", false)
              .html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");
            self.availablePackages = [];
            $("#itineraryResultContainer").addClass("v3-hidden");
            $("#itineraryCustomSection").addClass("v3-hidden");
            $("#btnPreviewItinerary").addClass("v3-hidden");
            $("#itineraryNotFoundDesc").text(
              `No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`,
            );
            $("#itineraryNotFoundBox").removeClass("v3-hidden");
          },
        });
      });

      // Quick CTA from No-Package-Found box to start custom plan
      $(document).on("click", "#btnStartCustomItinFromEmpty", function () {
        const dest =
          $("#itineraryDestinationInput").val() ||
          (self.leadData ? self.leadData.subject : "Custom Tour");
        self.selectedPackage = {
          tour_package_id: 0,
          tour_package_title: dest + " Custom Tour Package",
        };
        self.populateCustomizerFields(self.selectedPackage);
      });

      // Package Select Change - Reveal customization options only after an option is selected
      $(document).on("change", "#itineraryPackageSelect", function () {
        const val = $(this).val();
        if (!val) {
          $("#itineraryCustomSection").addClass("v3-hidden");
          $("#btnPreviewItinerary").addClass("v3-hidden");
          self.selectedPackage = null;
          return;
        }

        if (val === "custom") {
          const dest =
            $("#itineraryDestinationInput").val() ||
            (self.leadData ? self.leadData.subject : "Custom Destination");
          self.selectedPackage = {
            tour_package_id: 0,
            tour_package_title: dest + " Tour Package",
          };
          self.populateCustomizerFields(self.selectedPackage);
        } else if (self.availablePackages && self.availablePackages[val]) {
          const pkg = self.availablePackages[val];
          self.selectedPackage = pkg;
          self.populateCustomizerFields(pkg);
        }
      });

      // Add Day (Top & Bottom CTA)
      $(document).on(
        "click",
        "#btnAddItinDayTop, #btnAddItinDayBottom",
        function () {
          const nextNum = $("#itinDaysStack .v3-itin-day-card").length + 1;
          self.appendDayCard(
            {
              day: `Day ${nextNum} - Sightseeing & Exploration`,
              overview: "",
              hotel: "",
              meal_plan: "CP",
              vehicle: "Private Sedan",
              transfer_notes: "",
            },
            nextNum,
          );
        },
      );

      // Delete Day
      $(document).on("click", ".btn-delete-itin-day", function () {
        if ($("#itinDaysStack .v3-itin-day-card").length <= 1) {
          self.notify(
            "At least 1 day is required in the itinerary.",
            "warning",
          );
          return;
        }
        $(this).closest(".v3-itin-day-card").remove();
        self.reindexDays();
      });

      // Recalculate total price on per-person rate change
      $(document).on(
        "input",
        "#itineraryCustomAdultPrice, #itineraryCustomChildPrice, #itineraryCustomInfantPrice",
        function () {
          const adults =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.adults || 1)
              : 1;
          const children =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.children || 0)
              : 0;
          const infants =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.infants || 0)
              : 0;

          const ap = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
          const cp = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
          const ip = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

          const total = ap * adults + cp * children + ip * infants;
          $("#itineraryCustomTotalPrice").val(total);
        },
      );

      // Helper: Publish/Download Itinerary
      self.publishItinerary = function (isDownload, $btn, onSuccess) {
        const title =
          $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
        const totalPrice =
          parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
        const adultPrice =
          parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
        const childPrice =
          parseFloat($("#itineraryCustomChildPrice").val()) || 0;
        const infantPrice =
          parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

        if (!title) {
          self.notify("Itinerary title is required.", "warning");
          return;
        }

        const structuredItin = [];
        $("#itinDaysStack .v3-itin-day-card").each(function () {
          const dayTitle = $(this).find(".itin-day-title").val().trim();
          const dayOverview = $(this).find(".itin-day-overview").val().trim();
          const hotelName = $(this).find(".itin-day-hotel-name").val().trim();
          const hotelStars = $(this).find(".itin-day-hotel-stars").val();
          const roomType = $(this).find(".itin-day-room-type").val().trim();
          const mealPlan = $(this).find(".itin-day-meal-plan").val();
          const vehicleType = $(this).find(".itin-day-vehicle-type").val();
          const transferNotes = $(this)
            .find(".itin-day-transfer-notes")
            .val()
            .trim();

          structuredItin.push({
            day: dayTitle,
            overview: dayOverview,
            hotel: hotelName,
            hotel_stars: hotelStars,
            room_type: roomType,
            meal_plan: mealPlan,
            vehicle: vehicleType,
            transfer_notes: transferNotes,
          });
        });

        if (structuredItin.length === 0) {
          self.notify("Please add at least 1 day to the itinerary.", "warning");
          return;
        }

        const originalBtnHtml = $btn ? $btn.html() : "";
        if ($btn) {
          $btn
            .prop("disabled", true)
            .html(
              isDownload
                ? '<i class="fi fi-rr-spinner fi-spin"></i> Generating PDF...'
                : '<i class="fi fi-rr-spinner fi-spin"></i> Sending...',
            );
        }

        const pkgId =
          self.selectedPackage && self.selectedPackage.tour_package_id
            ? self.selectedPackage.tour_package_id
            : 1;

        const postData = {
          id: pkgId,
          refId: self.leadData ? self.leadData.id : self.leadId,
          refType: "enquiry",
          mrp: adultPrice || 0,
          isDownload: !!isDownload,
          content: {
            title: title,
            adult_per_person: adultPrice || 0,
            child_per_person: childPrice || 0,
            infant_per_person: infantPrice || 0,
            total_price: totalPrice || 0,
            overview: $("#itineraryCustomOverview").val().trim(),
            itinerary: structuredItin,
            inclusions: $("#itineraryCustomInclusions").val().trim(),
            exclusions: $("#itineraryCustomExclusions").val().trim(),
          },
        };

        $.ajax({
          url: "/api/v3/itinerary/publish/",
          type: "POST",
          contentType: "application/json",
          data: JSON.stringify(postData),
          dataType: "json",
          success: function (res) {
            if ($btn) {
              $btn.prop("disabled", false).html(originalBtnHtml);
            }
            if (res.status === "success" || res.itineraryId) {
              if (typeof onSuccess === "function") {
                onSuccess(res);
              }
            } else {
              self.notify(res.msg || "Failed to process itinerary.", "error");
            }
          },
          error: function () {
            if ($btn) {
              $btn.prop("disabled", false).html(originalBtnHtml);
            }
            self.notify("Network error while processing itinerary.", "error");
          },
        });
      };

      // Preview Itinerary CTA in Builder Modal (Client-side Real Preview matching download-itinerary-mit/)
      $(document).on(
        "click",
        "#btnPreviewItinerary, #btnPreviewItineraryBottom",
        function (e) {
          e.preventDefault();
          const title =
            $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
          const totalPrice =
            parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
          const adultPrice =
            parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
          const childPrice =
            parseFloat($("#itineraryCustomChildPrice").val()) || 0;
          const infantPrice =
            parseFloat($("#itineraryCustomInfantPrice").val()) || 0;
          const overview = $("#itineraryCustomOverview").val().trim();
          const inclusions = $("#itineraryCustomInclusions").val().trim();
          const exclusions = $("#itineraryCustomExclusions").val().trim();
          const dest =
            $("#itineraryDestinationInput").val() ||
            (self.leadData ? self.leadData.subject : "Custom Destination");
          const clientName = self.leadData
            ? self.leadData.name
            : "Valued Customer";
          const travelDate =
            self.leadData &&
            self.leadData.travel_details &&
            self.leadData.travel_details.date_of_travel
              ? self.leadData.travel_details.date_of_travel
              : "Flexible";
          const adults =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.adults || 1)
              : 1;
          const children =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.children || 0)
              : 0;
          const infants =
            self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.infants || 0)
              : 0;

          const $app = $("#leadViewApp");
          const siteName = $app.data("site-name") || "Travel";
          const siteLogo = $app.data("site-logo") || "";
          const siteDomain = $app.data("site-domain") || "travel.com";
          const siteTheme = $app.data("site-theme") || "#e76317";
          const siteBg = $app.data("site-bg") || "#fb923c";
          const leadId = self.leadData ? self.leadData.id : self.leadId;

          const structuredItin = [];
          $("#itinDaysStack .v3-itin-day-card").each(function () {
            const dayTitle = $(this).find(".itin-day-title").val().trim();
            const dayOverview = $(this).find(".itin-day-overview").val().trim();
            const hotelName = $(this).find(".itin-day-hotel-name").val().trim();
            const hotelStars = $(this).find(".itin-day-hotel-stars").val();
            const roomType = $(this).find(".itin-day-room-type").val().trim();
            const mealPlan = $(this).find(".itin-day-meal-plan").val();
            const vehicleType = $(this).find(".itin-day-vehicle-type").val();
            const transferNotes = $(this)
              .find(".itin-day-transfer-notes")
              .val()
              .trim();

            structuredItin.push({
              day: dayTitle,
              overview: dayOverview,
              hotel: hotelName,
              hotel_stars: hotelStars,
              room_type: roomType,
              meal_plan: mealPlan,
              vehicle: vehicleType,
              transfer_notes: transferNotes,
            });
          });

          if (structuredItin.length === 0) {
            self.notify(
              "Please add at least 1 day to preview itinerary.",
              "warning",
            );
            return;
          }

          const totalDays = structuredItin.length;
          const totalNights = Math.max(1, totalDays - 1);
          const totalPages = totalDays + 4; // Cover + Days + Inclusions + Exclusions + Pricing
          let currentPageNum = 1;

          function makeFooter(pageNum) {
            return `
            <footer style="position: absolute; bottom: 20px; width: 100%; text-align: center;">
              <div class="bottom-decor" style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;">
                ${siteLogo ? `<img src="${siteLogo}" class="center-logo" style="max-height: 28px; width: auto; object-fit: contain;">` : ""}
                <span style="font-size: 11px; color: #666; letter-spacing: 1px; white-space: nowrap;">${escapeHtml(siteDomain)}</span>
              </div>
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:nowrap; margin: 6px 50px 0 50px;">
                <span style="font-size: 11px; color: #666; white-space: nowrap;">Powered by Aleya.in</span>
                <span style="font-size: 11px; color: #666; white-space: nowrap;">Page ${pageNum} of ${totalPages}</span>
              </div>
            </footer>
          `;
          }

          // Page 1: Overview & Stats
          let pagesHtml = `
          <section class="page">
            <header class="hero">
              <div class="default-day-image" style="background: linear-gradient(135deg, ${siteTheme}, ${siteBg}); width: 100%; height: 100%; display: grid; place-items: center; text-align: center; color: #fff;">
                <h2 style="font-size: 2.8rem; font-weight: 700; text-transform: uppercase; text-shadow: 0 4px 15px rgba(0,0,0,0.3); padding: 0 40px;">${escapeHtml(title)}</h2>
              </div>
              <div class="refid">Ref. id: ${leadId ? `Lead #${leadId}` : "Custom Quote"}</div>
            </header>
            <div class="container" style="padding: 24px 30px;">
              <div class="title-section" style="display:flex; align-items:center; justify-content:center; text-align:center;">
                <span class="line-left">|</span>
                <h1 style="font-size: 28px; font-weight: 700; padding: 0 12px; color: #111;">${escapeHtml(title)}</h1>
                <span class="line-right">|</span>
              </div>

              <div class="stats" style="margin-top: 24px; margin-bottom: 30px; display: flex; gap: 16px; justify-content: center; text-align: center;">
                <div class="box" style="background: ${siteTheme}; width: 180px; padding: 10px 14px; border-radius: 8px; color: #fff;">
                  <p style="font-size: 0.8rem; margin-bottom: 2px;">Travel date</p>
                  <p style="font-size: 1.05rem; font-weight: 700; margin: 0;">${escapeHtml(travelDate)}</p>
                </div>
                <div class="box" style="background: ${siteTheme}; width: 180px; padding: 10px 14px; border-radius: 8px; color: #fff;">
                  <p style="font-size: 0.8rem; margin-bottom: 2px;">Travel Duration</p>
                  <p style="font-size: 1.05rem; font-weight: 700; margin: 0;">${totalNights} Nights / ${totalDays} Days</p>
                </div>
                <div class="box" style="background: ${siteTheme}; width: 180px; padding: 10px 14px; border-radius: 8px; color: #fff;">
                  <p style="font-size: 0.8rem; margin-bottom: 2px;">Travel Location</p>
                  <p style="font-size: 1.05rem; font-weight: 700; margin: 0;">${escapeHtml(dest)}</p>
                </div>
              </div>

              <div class="top-section" style="padding: 0 10px;">
                <div class="top-heading" style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                  <h2 style="font-size: 22px; font-weight: 700; color: #111; margin: 0;">Tour Overview</h2>
                </div>
                <p class="paragraph" style="font-size: 15px; line-height: 1.7; color: #333;">${overview ? escapeHtml(overview).replace(/\n/g, "<br>") : "Discover unforgettable sights, carefully curated accommodations, and hassle-free transfers on this personalized tour package."}</p>
              </div>
            </div>
            ${makeFooter(currentPageNum++)}
          </section>
        `;

          // Day Pages
          structuredItin.forEach(function (day, idx) {
            const padDay = String(idx + 1).padStart(2, "0");
            const titleParts = (day.day || `Day ${idx + 1}`).split(" - ");
            const dayTitleClean =
              titleParts.length > 1
                ? titleParts.slice(1).join(" - ")
                : titleParts[0];

            let stayTags = "";
            if (day.hotel) {
              stayTags += `<div style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#92400e; background:#fef3c7; border:1px solid #fde68a; padding:4px 10px; border-radius:6px;"><i class="ri-hotel-line"></i> ${escapeHtml(day.hotel)} (${day.hotel_stars || 3}★) - ${escapeHtml(day.room_type || "Deluxe")}</div>`;
            }
            if (day.meal_plan) {
              stayTags += `<div style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#065f46; background:#ecfdf5; border:1px solid #a7f3d0; padding:4px 10px; border-radius:6px;"><i class="ri-restaurant-line"></i> ${escapeHtml(day.meal_plan)} Plan</div>`;
            }
            if (day.vehicle) {
              stayTags += `<div style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#1e40af; background:#eff6ff; border:1px solid #bfdbfe; padding:4px 10px; border-radius:6px;"><i class="ri-car-line"></i> ${escapeHtml(day.vehicle)}</div>`;
            }

            pagesHtml += `
            <section class="page days-page">
              <header class="hero">
                <div class="default-day-image">
                  <h2>Day ${padDay}</h2>
                </div>
              </header>
              <div class="container" style="padding: 24px 30px;">
                <div class="day-header" style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
                  <div class="day-number-badge" style="background: ${siteTheme}; color: #fff; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 14px;">Day ${padDay}</div>
                  <h2 class="day-title" style="font-size: 22px; font-weight: 700; color: #111; margin: 0;">${escapeHtml(dayTitleClean)}</h2>
                </div>
                <div class="day-content">
                  <p class="day-description" style="font-size: 15px; line-height: 1.7; color: #333; margin-bottom: 16px;">${day.overview ? escapeHtml(day.overview).replace(/\n/g, "<br>") : "Sightseeing, exploration and leisure activities."}</p>
                  ${stayTags ? `<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px;">${stayTags}</div>` : ""}
                </div>
              </div>
              ${makeFooter(currentPageNum++)}
            </section>
          `;
          });

          // Inclusions Page
          const incLines = inclusions.split("\n").filter(Boolean);
          let incItemsHtml = "";
          (incLines.length
            ? incLines
            : [
                "Hotel accommodation with breakfast",
                "Airport and sightseeing transfers",
                "All toll taxes and parking charges",
              ]
          ).forEach(function (inc) {
            incItemsHtml += `
            <li class="flex items-center" style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; font-size: 15px; color: #333;">
              <div class="tour_check" style="width: 22px; height: 22px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="ri-check-line" style="font-size: 14px; font-weight: bold;"></i>
              </div>
              <span>${escapeHtml(inc.trim())}</span>
            </li>
          `;
          });

          pagesHtml += `
          <section class="page days-page">
            <header class="hero">
              <div class="default-day-image greenGradient">
                <h2>Inclusions</h2>
              </div>
            </header>
            <div class="container" style="padding: 24px 30px;">
              <div class="climax-section">
                <div class="tour_info_wrapper">
                  <div class="tour_info_card tour_included" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px;">
                    <h3 class="tour_info_title" style="font-size: 18px; font-weight: 700; color: #166534; margin-bottom: 16px;">What's Included:</h3>
                    <div class="tour_info_grid">
                      <ul class="tour_info_list" style="list-style: none; padding: 0; margin: 0;">
                        ${incItemsHtml}
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            ${makeFooter(currentPageNum++)}
          </section>
        `;

          // Exclusions Page
          const excLines = exclusions.split("\n").filter(Boolean);
          let excItemsHtml = "";
          (excLines.length
            ? excLines
            : [
                "Airfare / Train tickets",
                "Personal expenses & tips",
                "Entry fees and camera tickets",
              ]
          ).forEach(function (exc) {
            excItemsHtml += `
            <li class="flex items-center" style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; font-size: 15px; color: #333;">
              <div class="tour_cross" style="width: 22px; height: 22px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="ri-close-line" style="font-size: 14px; font-weight: bold;"></i>
              </div>
              <span>${escapeHtml(exc.trim())}</span>
            </li>
          `;
          });

          pagesHtml += `
          <section class="page days-page">
            <header class="hero">
              <div class="default-day-image redGradient">
                <h2>Exclusions</h2>
              </div>
            </header>
            <div class="container" style="padding: 24px 30px;">
              <div class="climax-section">
                <div class="tour_info_wrapper">
                  <div class="tour_info_card tour_excluded" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 24px;">
                    <h3 class="tour_info_title" style="font-size: 18px; font-weight: 700; color: #991b1b; margin-bottom: 16px;">Not Included in Package:</h3>
                    <div class="tour_info_grid">
                      <ul class="tour_info_list" style="list-style: none; padding: 0; margin: 0;">
                        ${excItemsHtml}
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            ${makeFooter(currentPageNum++)}
          </section>
        `;

          // Pricing Page
          const passangerInfo = `${adults} Adults${children > 0 ? `, ${children} Children` : ""}${infants > 0 ? `, ${infants} Infants` : ""}`;
          const basePrice = Math.round(totalPrice / 1.05);
          const gstAmount = Math.round(totalPrice - basePrice);
          const validDate = new Date(
            Date.now() + 7 * 86400000,
          ).toLocaleDateString("en-US", {
            month: "long",
            day: "numeric",
            year: "numeric",
          });

          pagesHtml += `
          <section class="page days-page">
            <header class="hero">
              <div class="default-day-image">
                <h2>Pricing</h2>
              </div>
            </header>
            <div class="container" style="padding: 24px 30px;">
              <div class="climax-section">
                <div class="tour_container">
                  <h2 class="tour_title" style="font-size: 22px; font-weight: 700; color: #111; margin-bottom: 16px;">Tour Pricing</h2>
                  <div class="tour_card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div class="tour_card_header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                      <div>
                        <h3 class="tour_package_title" style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">${escapeHtml(title)} - Quotation</h3>
                        <p class="tour_package_meta" style="font-size: 13px; color: #64748b; margin: 0;">
                          ${passangerInfo} | Deluxe Accommodation | ${totalNights} Nights, ${totalDays} Days
                        </p>
                      </div>
                      <span class="tour_badge" style="background: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">Best Value</span>
                    </div>
                    <hr class="tour_divider" style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;" />
                    <div class="tour_price_row" style="display: flex; justify-content: space-between; font-size: 14.5px; color: #334155; margin-bottom: 10px;">
                      <span><i class="ri-hotel-line"></i> Base Price ( ${passangerInfo} )</span>
                      <span style="font-weight: 600;">₹ ${basePrice.toLocaleString("en-IN")}</span>
                    </div>
                    <div class="tour_price_row" style="display: flex; justify-content: space-between; font-size: 14.5px; color: #334155; margin-bottom: 10px;">
                      <span><i class="ri-percent-line text-primary"></i> GST ( 5% )</span>
                      <span style="font-weight: 600;">+ ₹ ${gstAmount.toLocaleString("en-IN")}</span>
                    </div>
                    <div class="tour_total_section" style="display: flex; justify-content: flex-end; margin: 20px 0 16px 0;">
                      <div class="text-right" style="text-align: right;">
                        <div class="tour_total_text">
                          <p style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0;">Total Price</p>
                          <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 0 0;">₹ ${totalPrice.toLocaleString("en-IN")}</h1>
                        </div>
                      </div>
                    </div>
                    <div class="tour_btn" style="display: block; width: 100%; text-align: center; background: ${siteTheme}; color: #fff; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 15px; margin-bottom: 16px;">
                      Click here to Book Now
                    </div>
                    <div class="tour_notes_section" style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #64748b;">
                      <div class="tour_note_item" style="display: flex; align-items: center; gap: 6px;">
                        <i class="ri-calendar-line"></i>
                        <span>Prices valid until <strong>${validDate}</strong></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            ${makeFooter(currentPageNum++)}
          </section>
        `;

          const fullPreviewHtml = `
          <div class="itinerary-mit-preview-container" style="--primary-color: ${siteTheme}; --secondary-color: ${siteBg};">
            ${pagesHtml}
          </div>
        `;

          $("#livePreviewHeaderTitle").text(title);
          const $sheet = $("#livePreviewSheet").html(fullPreviewHtml);

          // Apply dynamic mountain curves to hero headers
          function generateWavePath(heroIndex) {
            const steps = 25 + Math.floor(Math.random() * 15);
            const points = [];
            for (let i = 0; i <= steps; i++) {
              const progress = i / steps;
              const x = progress * 100;
              const peakFreq1 = 1.5 + Math.random() * 2.5;
              const peakFreq2 = 2 + Math.random() * 3;
              const peakFreq3 = 0.5 + Math.random() * 1.5;
              const amplitude1 = 8 + Math.random() * 18;
              const amplitude2 = 5 + Math.random() * 12;
              const amplitude3 = 3 + Math.random() * 8;
              const wave1 =
                Math.sin(progress * Math.PI * peakFreq1) * amplitude1;
              const wave2 =
                Math.cos(progress * Math.PI * peakFreq2 + Math.random() * 0.3) *
                amplitude2;
              const wave3 =
                Math.sin(progress * Math.PI * peakFreq3 + Math.random() * 0.5) *
                amplitude3;
              const dramaticPeak =
                Math.random() > 0.75 ? Math.random() * 15 : 0;
              const noise = (Math.random() - 0.5) * 5;
              const baseHeight = 20 + Math.random() * 8;
              const y = Math.max(
                3,
                Math.min(
                  55,
                  baseHeight + wave1 + wave2 + wave3 + dramaticPeak + noise,
                ),
              );
              points.push({ x, y });
            }
            let pathData = "M 0,100 L 0,100";
            for (let i = 0; i < points.length; i++) {
              if (i === 0) {
                pathData += ` L ${points[i].x.toFixed(2)},${points[i].y.toFixed(2)}`;
              } else {
                const prevPoint = points[i - 1];
                const currPoint = points[i];
                const tension = 0.3 + Math.random() * 0.2;
                const dx = currPoint.x - prevPoint.x;
                const cp1x = prevPoint.x + dx * tension;
                const cp1y = prevPoint.y;
                const cp2x = currPoint.x - dx * tension;
                const cp2y = currPoint.y;
                pathData += ` C ${cp1x.toFixed(2)},${cp1y.toFixed(2)} ${cp2x.toFixed(2)},${cp2y.toFixed(2)} ${currPoint.x.toFixed(2)},${currPoint.y.toFixed(2)}`;
              }
            }
            pathData += " L 100,100 Z";
            return pathData;
          }

          $sheet.find(".hero").each(function (index) {
            const waveContainer = document.createElement("div");
            waveContainer.className = "hero-wave-container";
            waveContainer.style.position = "absolute";
            waveContainer.style.bottom = "0";
            waveContainer.style.left = "0";
            waveContainer.style.width = "100%";
            waveContainer.style.height = "40px";
            waveContainer.style.zIndex = "5";
            waveContainer.style.overflow = "hidden";

            const wavePath = generateWavePath(index);
            const svg = document.createElementNS(
              "http://www.w3.org/2000/svg",
              "svg",
            );
            svg.setAttribute("viewBox", "0 0 100 100");
            svg.setAttribute("preserveAspectRatio", "none");
            svg.style.width = "100%";
            svg.style.height = "100%";
            svg.style.display = "block";

            const path = document.createElementNS(
              "http://www.w3.org/2000/svg",
              "path",
            );
            path.setAttribute("d", wavePath);
            path.setAttribute("fill", "#ffffff");
            path.setAttribute("stroke", "none");

            svg.appendChild(path);
            waveContainer.appendChild(svg);
            this.appendChild(waveContainer);
          });

          $("#modalItineraryLivePreview").addClass("is-active");
        },
      );

      // Actions inside Live Proposal Preview Modal
      $(document).on("click", "#btnLivePreviewDownloadPdf", function () {
        self.publishItinerary(true, $(this), function (res) {
          const encId = res.encodedId || btoa(res.itineraryId);
          const downloadUrl = `/download-itinerary/${encId}/`;
          window.open(downloadUrl, "_blank");
          self.notify("Itinerary PDF opened in new tab!", "success");
        });
      });

      $(document).on("click", "#btnLivePreviewSendItinerary", function () {
        self.publishItinerary(false, $(this), function (res) {
          $("#modalItineraryLivePreview").removeClass("is-active");
          $("#modalSendItinerary").removeClass("is-active");
          self.notify(
            "Itinerary published and shared with customer!",
            "success",
          );
          self.loadLeadDetails();
        });
      });

      $(document).on("click", ".v3-preview-close-trigger", function () {
        $("#modalItineraryLivePreview").removeClass("is-active");
      });

      // 9. Other Modals
      $(document).on("click", "#btnHeroSetFollowUp, #btnSideSetFollowUp", function () {
        $(".v3-tab-link[data-tab='tab-activity']").trigger("click");
        $(".v3-composer-type-btn[data-type='followup']").trigger("click");
        $("#composerNoteInput").focus();
      });

      $(document).on("click", "#btnSideSendPaymentLink", function () {
        if (self.leadData && self.leadData.quotation) {
          $("#modalPaymentAmountInput").val(
            self.leadData.quotation.price || "",
          );
        }
        $("#modalSendPaymentLink").addClass("is-active");
      });

      $(document).on(
        "click",
        "#btnOpenPaymentModal, #btnOpenPaymentModalTab, #btnSideRecordPayment",
        function () {
          const fileInput = document.getElementById("modalPayFile");
          if (fileInput) fileInput.value = "";
          $("#modalPayFileText").text("Click to upload payment receipt or screenshot");
          $("#btnClearPayFile").addClass("v3-hidden");
          $("#modalPayFilePreview").empty().addClass("v3-hidden");
          $("#modalUpdatePayment").addClass("is-active");
          self.initSingleDatePickers();
        },
      );

      // Payment Receipt File Change & Clear Handlers
      $(document).on("change", "#modalPayFile", function () {
        const file = this.files && this.files[0];
        const $preview = $("#modalPayFilePreview");
        const $text = $("#modalPayFileText");
        const $clearBtn = $("#btnClearPayFile");

        if (file) {
          const fileSizeKb = Math.round(file.size / 1024);
          const sizeStr = fileSizeKb > 1024 ? (fileSizeKb / 1024).toFixed(1) + " MB" : fileSizeKb + " KB";
          $text.html(`<strong>${file.name}</strong> <span class="v3-file-size">(${sizeStr})</span>`);
          $clearBtn.removeClass("v3-hidden");

          if (file.type && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
              $preview
                .html(`<img src="${e.target.result}" alt="Preview" class="v3-receipt-thumb-img">`)
                .removeClass("v3-hidden");
            };
            reader.readAsDataURL(file);
          } else {
            $preview
              .html(`<div class="v3-receipt-doc-chip"><i class="fi fi-rr-document"></i> <span>${file.name}</span></div>`)
              .removeClass("v3-hidden");
          }
        } else {
          $text.text("Click to upload payment receipt or screenshot");
          $clearBtn.addClass("v3-hidden");
          $preview.empty().addClass("v3-hidden");
        }
      });

      $(document).on("click", "#btnClearPayFile", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const fileInput = document.getElementById("modalPayFile");
        if (fileInput) fileInput.value = "";
        $("#modalPayFileText").text("Click to upload payment receipt or screenshot");
        $("#btnClearPayFile").addClass("v3-hidden");
        $("#modalPayFilePreview").empty().addClass("v3-hidden");
      });

      // Open Payment Receipt & Breakdown Modal on current page with Infinite Navigation
      self.currentReceiptIdx = 0;

      self.renderReceiptDetails = function (idx) {
        if (!self.leadPayments || self.leadPayments.length === 0) return;
        const total = self.leadPayments.length;
        const wrappedIdx = ((idx % total) + total) % total;
        self.currentReceiptIdx = wrappedIdx;
        const p = self.leadPayments[wrappedIdx];
        if (!p) return;

        const rawImg = p.image_url || (p.image && typeof p.image === "object" ? p.image.path : (typeof p.image === "string" && (p.image.includes("/") || p.image.includes(".")) ? p.image : ""));
        const $imgContainer = $("#receiptImgContainer");
        const $downloadBtn = $("#receiptModalDownloadBtn");

        if (rawImg) {
          const isPdf = rawImg.toLowerCase().includes(".pdf");
          if (isPdf) {
            $imgContainer.html(`
              <div class="v3-receipt-pdf-placeholder">
                <i class="fi fi-rr-file-pdf"></i>
                <h5>PDF Document Attached</h5>
                <p>Click below to preview or download the receipt PDF.</p>
                <a href="${rawImg}" target="_blank" class="btn-v3-action btn-green"><i class="fi fi-rr-eye"></i> View PDF Document</a>
              </div>
            `);
          } else {
            $imgContainer.html(`
              <img src="${rawImg}" alt="Payment Receipt" class="v3-receipt-full-preview">
            `);
          }
          $downloadBtn.attr("href", rawImg).show();
        } else {
          $imgContainer.html(`
            <div class="v3-receipt-empty-preview">
              <i class="fi fi-rr-picture"></i>
              <p>No receipt image attached for this transaction.</p>
            </div>
          `);
          $downloadBtn.hide();
        }

        $("#receiptModalAmount").text(`₹ ${parseFloat(p.amount || 0).toLocaleString("en-IN")}`);
        $("#receiptModalDate").text(p.date || "-");
        $("#receiptModalMethod").text(p.method || "Standard");
        $("#receiptModalRef").text(p.reference || "None");
        $("#receiptModalRecordedBy").text(p.recorded_by || "System");
        $("#receiptModalNotes").text(p.notes || "No remarks provided.");
        $("#receiptModalSubtitle").text(`Recorded on ${p.date || "recent"} by ${p.recorded_by || "Representative"}`);
        $("#receiptNavCounter").text(`${wrappedIdx + 1} of ${total}`);

        if (total <= 1) {
          $("#receiptNavBar").hide();
        } else {
          $("#receiptNavBar").show();
        }
      };

      $(document).on("click", ".btn-view-payment-receipt", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const idx = parseInt($(this).data("payment-idx"), 10) || 0;
        self.renderReceiptDetails(idx);
        $("#modalViewPaymentReceipt").addClass("is-active");
      });

      // Infinite Left / Right Navigation Handlers
      $(document).on("click", "#btnReceiptNavPrev", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.renderReceiptDetails(self.currentReceiptIdx - 1);
      });

      $(document).on("click", "#btnReceiptNavNext", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.renderReceiptDetails(self.currentReceiptIdx + 1);
      });

      // Copy UTR / Reference from Receipt Modal
      $(document).on("click", "#btnCopyReceiptRef", function (e) {
        e.preventDefault();
        const ref = $("#receiptModalRef").text().trim();
        if (!ref || ref === "None" || ref === "-") return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(ref).then(function () {
            self.notify("Reference copied to clipboard!", "success");
          });
        }
      });

      $(document).on("click", "#btnSideCloseLeadModal", function () {
        $("#modalCloseLead").addClass("is-active");
      });

      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(".v3-modal-overlay").removeClass("is-active");
      });

      // Close modal on Escape key or navigate with Left/Right Arrow keys
      $(document).on("keydown", function (e) {
        if ($("#modalViewPaymentReceipt").hasClass("is-active")) {
          if (e.key === "ArrowLeft" || e.keyCode === 37) {
            e.preventDefault();
            self.renderReceiptDetails(self.currentReceiptIdx - 1);
            return;
          } else if (e.key === "ArrowRight" || e.keyCode === 39) {
            e.preventDefault();
            self.renderReceiptDetails(self.currentReceiptIdx + 1);
            return;
          }
        }

        if (e.key === "Escape" || e.keyCode === 27) {
          if ($(".v3-modal-overlay.is-active").length) {
            $(".v3-modal-overlay.is-active").removeClass("is-active");
          }
          if ($(".popUp").is(":visible")) {
            $(".popUp").hide();
          }
        }
      });

      // 10. Submit Payment Record Modal
      $(document).on("click", "#btnSubmitModalPayment", function () {
        const amount = $("#modalPayAmount").val().trim();
        const method = $("#modalPayMethod").val();
        const date = $("#modalPayDate").val();
        const ref = $("#modalPayRef").val().trim();
        const notes = $("#modalPayNotes").val().trim();
        const fileInput = document.getElementById("modalPayFile");

        if (!amount || parseFloat(amount) <= 0) {
          self.notify("Please enter a valid payment amount.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");

        const formData = new FormData();
        formData.append("leadId", self.leadId);
        formData.append("amount", amount);
        formData.append("payment_method", method);
        formData.append("payment_date", date);
        formData.append("reference_number", ref);
        formData.append("payment_notes", notes);

        if (fileInput && fileInput.files && fileInput.files.length > 0) {
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
            $btn.prop("disabled", false).text("Record Payment");
            $(".v3-modal-overlay").removeClass("is-active");
            if (res.status === "success") {
              self.notify("Payment recorded successfully", "success");
              // Reset modal inputs
              $("#modalPayAmount").val("");
              $("#modalPayRef").val("");
              $("#modalPayNotes").val("");
              if (fileInput) fileInput.value = "";
              $("#modalPayFileText").text("Click to upload payment receipt or screenshot");
              $("#btnClearPayFile").addClass("v3-hidden");
              $("#modalPayFilePreview").empty().addClass("v3-hidden");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to record payment.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).text("Record Payment");
            self.notify("Network connection error.", "error");
          },
        });
      });

      // 11. Submit Close Lead Modal
      $(document).on("click", "#btnSubmitModalCloseLead", function () {
        const reason = $("#modalCloseReason").val();
        const msg = $("#modalCloseMsg").val().trim();

        if (!reason) {
          self.notify(
            "Please select a reason for closing this lead.",
            "warning",
          );
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Closing...");

        $.ajax({
          url: "/api/v3/leads/add-note/",
          type: "POST",
          data: {
            leadId: self.leadId,
            title: reason,
            msg: msg,
            finalNotes: true,
          },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).text("Close Lead");
            $(".v3-modal-overlay").removeClass("is-active");
            if (res.status === "success") {
              self.notify("Lead closed successfully", "success");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to close lead.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).text("Close Lead");
            self.notify("Network connection error.", "error");
          },
        });
      });

      // 12. Retry
      $(document).on("click", "#btnRetryLeadView", function () {
        self.loadLeadDetails();
      });
    },

    populateCustomizerFields: function (pkg) {
      const self = this;
      const adultPrice = parseFloat(pkg.tour_package_price_adult || 0);
      const childPrice = parseFloat(pkg.tour_package_price_child || 0);
      const adults =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.adults || 1)
          : 1;
      const children =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.children || 0)
          : 0;
      const totalPrice = adultPrice * adults + childPrice * children;

      // Parse structured day-wise itinerary
      let daysList = [];
      try {
        if (pkg.tour_package_itinerary) {
          const rawItin =
            typeof pkg.tour_package_itinerary === "string"
              ? JSON.parse(pkg.tour_package_itinerary)
              : pkg.tour_package_itinerary;
          if (Array.isArray(rawItin)) {
            daysList = rawItin;
          }
        }
      } catch (e) {
        if (
          typeof pkg.tour_package_itinerary === "string" &&
          pkg.tour_package_itinerary.trim()
        ) {
          const rawBlocks = pkg.tour_package_itinerary.split("\n\n");
          rawBlocks.forEach((b, i) => {
            if (b.trim()) {
              daysList.push({ day: `Day ${i + 1}`, overview: b.trim() });
            }
          });
        }
      }

      const stripHtml = (s) => (s ? s.replace(/<[^>]*>?/gm, "").trim() : "");

      $("#itineraryCustomTitle").val(pkg.tour_package_title || "");
      $("#itineraryCustomAdultPrice").val(adultPrice);
      $("#itineraryCustomChildPrice").val(childPrice);
      $("#itineraryCustomInfantPrice").val(0);
      $("#itineraryCustomTotalPrice").val(
        totalPrice > 0 ? totalPrice : adultPrice,
      );
      $("#itineraryCustomOverview").val(stripHtml(pkg.tour_package_overview));
      $("#itineraryCustomInclusions").val(
        stripHtml(pkg.tour_package_inclusions) ||
          "Hotel accommodation with breakfast\nAirport & sightseeing transfers\nAll toll taxes and parking charges",
      );
      $("#itineraryCustomExclusions").val(
        stripHtml(pkg.tour_package_exclusions) ||
          "Airfare / Train tickets\nPersonal expenses & tips\nEntry fees and camera tickets",
      );

      self.selectedPackageTemplate = pkg.tour_package_template || "mit";
      self.renderItineraryDays(daysList);

      $("#itineraryCustomSection").removeClass("v3-hidden");
      $("#btnPreviewItinerary").removeClass("v3-hidden");
    },

    confirmConvertToSale: function () {
      const self = this;
      if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
        Swal.fire({
          title: "Convert to Confirmed Sale?",
          text: "This will transition the lead to confirmed status in your revenue pipeline.",
          type: "warning",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#10b981",
          cancelButtonColor: "#64748b",
          confirmButtonText: "Yes, Confirm Sale",
          cancelButtonText: "Cancel",
        }).then((res) => {
          if (res.isConfirmed || res.value) {
            self.updateStatus(1);
          }
        });
      } else if (confirm("Convert this lead to confirmed sale?")) {
        self.updateStatus(1);
      }
    },

    performAgentAssign: function (agentId) {
      const self = this;
      $.ajax({
        url: "/api/v3/leads/assign/",
        type: "POST",
        data: {
          leadId: self.leadId,
          agentId: agentId,
        },
        dataType: "json",
        success: function (res) {
          if (res.status === 200 || res.status === "success") {
            self.notify("Agent reassigned successfully", "success");

            if ("Notification" in window && res.notification) {
              if (Notification.permission === "granted") {
                const notifTitle = res.notification.title || "Lead Assigned";
                const notifOptions = {
                  body: res.notification.body || "Lead assignment updated.",
                  icon:
                    res.notification.icon ||
                    "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                  badge:
                    res.notification.icon ||
                    "https://aleya.in/app/aleya/visitor/aleya/desktop/assets/images/aleya_favicon.png",
                  data: {
                    url: res.notification.url || window.location.href,
                    click_action: res.notification.url || window.location.href,
                  },
                };

                if (
                  "serviceWorker" in navigator &&
                  navigator.serviceWorker.controller
                ) {
                  navigator.serviceWorker.ready
                    .then(function (reg) {
                      reg.showNotification(notifTitle, notifOptions);
                    })
                    .catch(function () {
                      try {
                        const notif = new Notification(
                          notifTitle,
                          notifOptions,
                        );
                        if (res.notification.url) {
                          notif.onclick = function () {
                            window.open(res.notification.url, "_blank");
                          };
                        }
                      } catch (err) {}
                    });
                } else {
                  try {
                    const notif = new Notification(notifTitle, notifOptions);
                    if (res.notification.url) {
                      notif.onclick = function () {
                        window.open(res.notification.url, "_blank");
                      };
                    }
                  } catch (err) {}
                }
              }
            }

            self.loadLeadDetails();
          } else {
            self.notify(
              res.msg || res.message || "Failed to reassign agent.",
              "error",
            );
            self.loadLeadDetails();
          }
        },
        error: function () {
          self.notify("Network error during assignment.", "error");
          self.loadLeadDetails();
        },
      });
    },

    updateStatus: function (status) {
      const self = this;
      $.ajax({
        url: "/api/v3/leads/update-status/",
        type: "POST",
        data: {
          lead_id: self.leadId,
          status: status,
        },
        dataType: "json",
        success: function (res) {
          if (res.status === 200 || res.status === "success") {
            self.notify("Status updated successfully", "success");
            self.loadLeadDetails();
          } else {
            self.notify(res.msg || "Failed to update status.", "error");
          }
        },
        error: function () {
          self.notify("Network error.", "error");
        },
      });
    },

    findBestCityMatch: function (originalText, results) {
      if (!results || !results.length) return null;
      const lower = (originalText || "").toLowerCase().trim();

      // Priority 1: Match city or id directly in originalText
      for (let i = 0; i < results.length; i++) {
        const item = results[i];
        const cityName = (item.city || item.id || "").toLowerCase().trim();
        if (cityName && (lower.includes(cityName) || cityName.includes(lower))) {
          return item;
        }
      }

      // Priority 2: Match state directly in originalText
      for (let i = 0; i < results.length; i++) {
        const item = results[i];
        const stateName = (item.state || "").toLowerCase().trim();
        if (stateName && (lower.includes(stateName) || stateName.includes(lower))) {
          return item;
        }
      }

      // Priority 3: First result from search
      return results[0] || null;
    },

    detectDestinationFromSubject: function (subjectText, callback) {
      const self = this;
      if (!subjectText || typeof subjectText !== "string") {
        if (callback) callback(null);
        return;
      }

      // Clean common tourism noise words
      const cleanText = subjectText
        .replace(/\b(\d+N\/?\d*D|\d+\s*(nights?|days?)|tour|package|holiday|trip|honeymoon|special|custom|itinerary|vacation|group|couple|family|deluxe|standard|premium|budget|super|luxury|stay|resort|hotel|enquiry|booking)\b/gi, " ")
        .replace(/[^a-zA-Z\s]/g, " ")
        .replace(/\s+/g, " ")
        .trim();

      const candidateWords = cleanText ? cleanText.split(/\s+/).filter(function (w) { return w.length >= 3; }) : [];
      const searchTerm = cleanText || (candidateWords.length ? candidateWords[0] : subjectText.trim());

      if (!searchTerm) {
        if (callback) callback(null);
        return;
      }

      $.ajax({
        url: "/api/v3/cities/",
        type: "GET",
        data: { q: searchTerm },
        dataType: "json",
        success: function (res) {
          const results = (res && res.results) || [];
          if (!results.length && candidateWords.length > 1) {
            // Fallback: search with first individual keyword
            $.ajax({
              url: "/api/v3/cities/",
              type: "GET",
              data: { q: candidateWords[0] },
              dataType: "json",
              success: function (res2) {
                const results2 = (res2 && res2.results) || [];
                const match = self.findBestCityMatch(subjectText, results2);
                if (callback) callback(match);
              },
              error: function () {
                if (callback) callback(null);
              },
            });
            return;
          }

          const match = self.findBestCityMatch(subjectText, results);
          if (callback) callback(match);
        },
        error: function () {
          if (callback) callback(null);
        },
      });
    },

    initCitySelect2: function ($el, $dropdownParent, placeholder) {
      if (!$el || !$el.length || typeof $.fn.select2 === "undefined") return;

      $el.select2({
        placeholder: placeholder || "-- Search Destination City / Country --",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent:
          $dropdownParent && $dropdownParent.length
            ? $dropdownParent
            : undefined,
        ajax: {
          url: "/api/v3/cities/",
          dataType: "json",
          delay: 150,
          data: function (params) {
            return {
              q: params.term || "",
            };
          },
          processResults: function (data) {
            return {
              results: (data && data.results) || [],
            };
          },
          cache: true,
        },
      });
    },

    initPackageSelect2: function ($el, $dropdownParent, placeholder) {
      if (!$el || !$el.length || typeof $.fn.select2 === "undefined") return;

      $el.select2({
        placeholder: placeholder || "Choose a tour package or type custom destination...",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent:
          $dropdownParent && $dropdownParent.length
            ? $dropdownParent
            : undefined,
        ajax: {
          url: "/api/v3/packages/options/",
          dataType: "json",
          delay: 150,
          data: function (params) {
            return {
              q: params.term || "",
            };
          },
          processResults: function (data) {
            return {
              results: (data && data.results) || [],
            };
          },
          cache: true,
        },
        templateResult: function (data) {
          if (!data.id) return data.text;
          if (data.children) return data.text;

          const isCustom = !data.type && !data.pkg_id;
          if (isCustom) {
            return $(`
              <div class="v3-pkg-option-item custom">
                <div class="v3-pkg-option-icon custom"><i class="fi fi-rr-search-location"></i></div>
                <div class="v3-pkg-option-details">
                  <div class="v3-pkg-option-title">${data.text}</div>
                  <span class="v3-pkg-badge custom">Custom Destination</span>
                </div>
              </div>
            `);
          }

          const duration = data.duration;
          const days = data.days || (parseInt(duration) + 1);
          const city = data.city || "";
          const type = data.type || "domestic";
          const theme = data.theme || "";
          const isIntl = type.toLowerCase() === "international";

          return $(`
            <div class="v3-pkg-option-item">
              <div class="v3-pkg-option-icon ${isIntl ? 'intl' : 'dom'}">
                <i class="fi ${isIntl ? 'fi-rr-plane' : 'fi-rr-route'}"></i>
              </div>
              <div class="v3-pkg-option-details">
                <div class="v3-pkg-option-title">${data.text}</div>
                <div class="v3-pkg-option-badges">
                  ${duration ? `<span class="v3-pkg-badge duration"><i class="fi fi-rr-moon"></i> ${duration}N / ${days}D</span>` : ''}
                  ${city ? `<span class="v3-pkg-badge destination"><i class="fi fi-rr-marker"></i> ${city}</span>` : ''}
                  ${theme ? `<span class="v3-pkg-badge theme"><i class="fi fi-rr-star"></i> ${theme}</span>` : ''}
                  <span class="v3-pkg-badge type ${isIntl ? 'intl' : 'dom'}">${isIntl ? 'International' : 'Domestic'}</span>
                </div>
              </div>
            </div>
          `);
        },
        templateSelection: function (data) {
          return data.text || data.id;
        }
      });
    },

    initSingleDatePickers: function () {
      if (typeof $.fn.daterangepicker === "undefined") return;

      $(".v3-single-datepicker-input").each(function () {
        const $el = $(this);
        if ($el.data("daterangepicker")) {
          $el.data("daterangepicker").remove();
        }

        const initialVal = $el.val();
        let startMoment = typeof moment !== "undefined" ? moment() : undefined;
        if (
          initialVal &&
          typeof moment !== "undefined" &&
          moment(initialVal, "YYYY-MM-DD", true).isValid()
        ) {
          startMoment = moment(initialVal, "YYYY-MM-DD");
        }

        const curYear = typeof moment !== "undefined" ? moment().year() : 2026;
        const isFutureOnly =
          $el.attr("id") === "editLeadTravelDate" ||
          $el.data("future-only") === true ||
          $el.attr("data-future-only") === "true";

        let minDateOpt = false;
        let minYearOpt = curYear - 10;
        if (isFutureOnly) {
          minDateOpt = typeof moment !== "undefined" ? moment().startOf("day") : false;
          minYearOpt = curYear;
          if (startMoment && typeof moment !== "undefined" && startMoment.isBefore(moment().startOf("day"))) {
            startMoment = moment().startOf("day");
          }
        }

        $el.daterangepicker(
          {
            singleDatePicker: true,
            showDropdowns: true,
            minDate: minDateOpt,
            minYear: minYearOpt,
            maxYear: curYear + 15,
            autoApply: true,
            autoUpdateInput: false,
            startDate: startMoment,
            opens: "left",
            drops: "down",
            locale: {
              format: "YYYY-MM-DD",
            },
          },
          function (chosen) {
            $el.val(chosen.format("YYYY-MM-DD")).trigger("change");
          }
        );

        $el.on("apply.daterangepicker", function (ev, picker) {
          $(this).val(picker.startDate.format("YYYY-MM-DD")).trigger("change");
        });

        $el.on("show.daterangepicker", function (ev, picker) {
          if (picker && picker.container) {
            picker.container.addClass("single");
            picker.container.find(".drp-calendar.right").css("display", "none");
            picker.container.find(".ranges").css("display", "none");
            picker.container.find(".drp-buttons").css("display", "none");
          }
        });

        // Ensure container is strictly hidden upon instantiation
        const pickerObj = $el.data("daterangepicker");
        if (pickerObj && pickerObj.container) {
          pickerObj.container.hide().css("display", "none");
        }
      });
    },
  };

  // Run on page ready and SPA transitions
  $(document).ready(function () {
    window.V3LeadView.init();
  });

  $(document)
    .off("spa:loaded.v3LeadView")
    .on("spa:loaded.v3LeadView", function () {
      window.V3LeadView.init();
    });
});
