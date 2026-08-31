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

    playNotificationSound: function () {
      console.log("[Follow-Up Reminder] 🔊 Playing notification sound for assigned user...");
      try {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
          const audioCtx = new AudioContextClass();
          const playAudioChime = function () {
            const playTone = (freq, startTime, duration) => {
              const osc = audioCtx.createOscillator();
              const gain = audioCtx.createGain();
              osc.type = "sine";
              osc.frequency.setValueAtTime(freq, audioCtx.currentTime + startTime);
              gain.gain.setValueAtTime(0.35, audioCtx.currentTime + startTime);
              gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + startTime + duration);
              osc.connect(gain);
              gain.connect(audioCtx.destination);
              osc.start(audioCtx.currentTime + startTime);
              osc.stop(audioCtx.currentTime + startTime + duration);
            };
            // 3-tone bright chime
            playTone(880.00, 0, 0.18);
            playTone(1174.66, 0.12, 0.18);
            playTone(1479.98, 0.24, 0.35);
            console.log("[Follow-Up Reminder] 🔔 Notification chime successfully played.");
          };

          if (audioCtx.state === "suspended") {
            audioCtx.resume().then(playAudioChime).catch(playAudioChime);
          } else {
            playAudioChime();
          }
        }
      } catch (e) {
        console.warn("[Follow-Up Reminder] ⚠️ Audio playback error:", e);
      }
    },

    scheduleClientReminder: function (followupTimeStr, customerName, agentId, leadId) {
      // Global follow-up reminders are coordinated by app.js across the dashboard
      // to avoid duplicate notification banners, popups, and audio triggers.
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
          const type = (t.type || "note").toLowerCase();
          let extraContent = "";
          let cardExtraClass = `v3-timeline-card-${type}`;
          let categoryHtml = "";

          if (type === "followup") {
            const cat = t.category || "General Follow-up";
            categoryHtml = `<span class="v3-timeline-category-pill pill-followup"><i class="fi fi-rr-flag"></i> ${cat}</span>`;
            
            let timeChip = "";
            if (t.followup_time) {
              timeChip = `
                <div class="v3-timeline-followup-time-box">
                  <i class="fi fi-rr-calendar-clock"></i>
                  <span>Follow-Up Due: <strong>${t.followup_time}</strong></span>
                </div>
              `;
            }
            
            extraContent = `
              <div class="v3-timeline-followup-body">
                ${timeChip}
                ${t.description ? `<div class="v3-timeline-note-text">${t.description}</div>` : ""}
              </div>
            `;
          } else if (type === "payment") {
            const p = t.payment_data || {};
            const amt = p.amount ? parseFloat(p.amount) : 0;
            const method = p.method || "Standard";
            const ref = p.reference || "";
            const status = (p.status || "verified").toLowerCase();
            const statusLabel = (status === "pending") ? "Pending Verification" : (status === "rejected" ? "Rejected" : "Verified");
            const statusClass = (status === "pending") ? "status-pending" : (status === "rejected" ? "status-rejected" : "status-verified");

            categoryHtml = `<span class="v3-status-pill ${statusClass} v3-tl-status-badge">${statusLabel}</span>`;
            extraContent = `
              <div class="v3-timeline-payment-card">
                <div class="v3-tl-pay-top">
                  <div class="v3-tl-pay-amount">₹ ${amt > 0 ? amt.toLocaleString("en-IN", { minimumFractionDigits: 2 }) : (t.description ? t.description.replace(/^Recorded payment of /, "") : "0.00")}</div>
                  <span class="v3-tl-pay-method"><i class="fi fi-rr-credit-card"></i> ${method}</span>
                </div>
                ${ref ? `<div class="v3-tl-pay-ref">Reference / Transaction ID: <strong>${ref}</strong></div>` : ""}
              </div>
            `;
          } else if (t.type === "itinerary") {
            const q = t.quotation || {};
            const encId = q.encoded_id || (t.description && !isNaN(t.description) ? btoa(t.description) : "");
            const title = q.title || (t.description ? (isNaN(t.description) ? t.description : `Tour Itinerary #${t.description}`) : "Tour Itinerary");
            const price = parseFloat(q.price || 0);
            const mrp = parseFloat(q.mrp || 0);
            const viewUrl = q.view_url || (encId ? `/view-itinerary/${encId}/` : "#");
            const downloadUrl = q.download_url || (encId ? `/download-itinerary/${encId}/` : "#");
            const hasMeta = !!(q.destination || q.duration || q.theme);

            categoryHtml = `<span class="v3-timeline-category-pill pill-itinerary"><i class="fi fi-rr-route"></i> Itinerary Proposal</span>`;
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
          } else if (type === "note") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-note"><i class="fi fi-rr-comment"></i> Note</span>`;
            extraContent = `<div class="v3-timeline-note-text">${t.description || "Note logged"}</div>`;
          } else if (type === "update") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-update"><i class="fi fi-rr-refresh"></i> Update</span>`;
            extraContent = `<div class="v3-timeline-update-text">${t.description || "Lead details updated."}</div>`;
          } else if (type === "assign") {
            categoryHtml = `<span class="v3-timeline-category-pill pill-assign"><i class="fi fi-rr-user-check"></i> Assignment</span>`;
            extraContent = `<div class="v3-timeline-assign-text">${t.description || "Lead assigned to agent."}</div>`;
          } else {
            extraContent = t.description ? `<div class="v3-timeline-note-text">${t.description}</div>` : "";
          }

          timelineHtml += `
            <div class="v3-timeline-row type-${type}">
              <div class="v3-timeline-bullet"><i class="${t.icon || "fi fi-rr-comment"}"></i></div>
              <div class="v3-timeline-row-meta">
                <span class="v3-timeline-tag">${t.title}</span>
                ${categoryHtml}
                <span>•</span>
                <span class="v3-timeline-author">${t.user_name}</span>
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
      let totalPendingVerification = 0;

      self.leadPayments.forEach((p) => {
        const pStatus = String(p.status || "verified").toLowerCase().trim();
        const amt = parseFloat(p.amount || 0);
        if (pStatus === "verified") {
          totalPaid += amt;
        } else if (pStatus === "pending" || pStatus === "pending approval" || pStatus === "unverified") {
          totalPendingVerification += amt;
        }
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
      } else if (totalPendingVerification > 0) {
        pendingHtml = `
          <div class="v3-pay-stat-pill stat-pending">
            <i class="fi fi-rr-clock"></i>
            <span class="v3-pay-stat-label">Pending</span>
            <span class="v3-pay-stat-val">₹ ${totalPendingVerification.toLocaleString("en-IN")}</span>
          </div>
        `;
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
                    <th>Status</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
        `;
        const userRole = (
          (self.leadData && self.leadData.access_type) ||
          $("#leadViewApp").data("user-role") ||
          (typeof siteAdminType !== "undefined" ? siteAdminType : "")
        ).toLowerCase().trim();
        const canVerifyPayment = (userRole === "administrator" || userRole === "accountant" || userRole === "superadmin");

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

          let statusBadge = `<span class="v3-status-pill status-verified"><i class="fi fi-rr-check"></i> Verified</span>`;
          if (p.status === "pending") {
            if (canVerifyPayment) {
              statusBadge = `
                <div style="display: inline-flex; align-items: center; gap: 6px;">
                  <span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending</span>
                  <button type="button" class="btn-v3-action btn-green btn-xs btn-lead-approve-payment" data-note-id="${p.id}" title="Approve & Verify Payment">
                    <i class="fi fi-rr-check"></i> Verify
                  </button>
                </div>
              `;
            } else {
              statusBadge = `<span class="v3-status-pill status-pending"><i class="fi fi-rr-clock"></i> Pending</span>`;
            }
          } else if (p.status === "rejected") {
            statusBadge = `<span class="v3-status-pill status-rejected"><i class="fi fi-rr-cross-circle"></i> Rejected</span>`;
          }

          paymentsHtml += `
            <tr class="v3-payment-row">
              <td><span class="v3-pay-date-text"><i class="fi fi-rr-calendar"></i> ${p.date}</span></td>
              <td><strong class="v3-pay-amount-text">₹ ${parseFloat(p.amount).toLocaleString("en-IN")}</strong></td>
              <td>${getPaymentMethodBadge(p.method)}</td>
              <td>${refHtml}</td>
              <td>${receiptHtml}</td>
              <td><span class="v3-pay-recorded-by"><i class="fi fi-rr-user"></i> ${p.recorded_by}</span></td>
              <td>${statusBadge}</td>
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

      const defaultFollowupMoment = self.getDefaultFollowupDateTime();
      const defaultFollowupStr = typeof moment !== "undefined" && defaultFollowupMoment && defaultFollowupMoment.format
        ? defaultFollowupMoment.format("YYYY-MM-DD, hh:mm A")
        : "";

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
                      <div class="v3-followup-schedule-card">
                        <div class="v3-followup-presets-wrap">
                          <span class="v3-preset-label"><i class="fi fi-rr-bolt"></i> Quick Presets:</span>
                          <div class="v3-preset-chips" data-target="#composerFollowupTime">
                            <button type="button" class="v3-preset-chip" data-preset="in_2_hours"><i class="fi fi-rr-bolt"></i> In 2 Hours</button>
                            <button type="button" class="v3-preset-chip active" data-preset="tomorrow_morning"><i class="fi fi-rr-sun"></i> Tomorrow 10:30 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_afternoon"><i class="fi fi-rr-sun"></i> Tomorrow 3:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="tomorrow_evening"><i class="fi fi-rr-sunset"></i> Tomorrow 6:00 PM</button>
                            <button type="button" class="v3-preset-chip" data-preset="in_2_days"><i class="fi fi-rr-calendar"></i> In 2 Days 11:00 AM</button>
                            <button type="button" class="v3-preset-chip" data-preset="next_monday"><i class="fi fi-rr-calendar-clock"></i> Next Mon 10:30 AM</button>
                          </div>
                        </div>
                        <div class="v3-datetime-split-grid">
                          <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="composerFollowupDate"><i class="fi fi-rr-calendar"></i> Date</label>
                            <div class="v3-native-input-wrap">
                              <input type="date" id="composerFollowupDate" class="v3-native-datetime-input">
                            </div>
                          </div>
                          <div class="v3-datetime-col">
                            <label class="v3-sub-label" for="composerFollowupTimeInput"><i class="fi fi-rr-clock"></i> Time</label>
                            <div class="v3-native-input-wrap">
                              <input type="time" id="composerFollowupTimeInput" class="v3-native-datetime-input">
                            </div>
                          </div>
                        </div>
                        <input type="hidden" id="composerFollowupTime" value="${defaultFollowupStr}">
                        <div class="v3-time-quick-pills" data-target="#composerFollowupTimeInput">
                          <span class="v3-time-pills-label">Popular slots:</span>
                          <button type="button" class="v3-time-pill" data-time="10:00">10:00 AM</button>
                          <button type="button" class="v3-time-pill active" data-time="10:30">10:30 AM</button>
                          <button type="button" class="v3-time-pill" data-time="14:30">02:30 PM</button>
                          <button type="button" class="v3-time-pill" data-time="16:00">04:00 PM</button>
                          <button type="button" class="v3-time-pill" data-time="18:00">06:00 PM</button>
                          <button type="button" class="v3-time-pill" data-time="19:30">07:30 PM</button>
                        </div>
                        <div class="v3-slot-status-box" id="composerSlotStatus"></div>
                      </div>
                      <div class="v3-modal-form-group" style="margin-top: 10px;">
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

      // Initialize Single Datepickers & Follow-up DateTime Pickers
      self.initSingleDatePickers();
      self.initDateTimePicker();

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

      // Check & schedule upcoming follow-up reminder if assigned to current user
      if (
        lead.kpis &&
        lead.kpis.next_followup &&
        lead.kpis.next_followup.is_scheduled &&
        lead.kpis.next_followup.raw_time
      ) {
        self.scheduleClientReminder(
          lead.kpis.next_followup.raw_time,
          lead.name || "Customer",
          lead.agent_id,
          self.leadId
        );
      }
    },

    // Load and cache active hotel properties, vehicles, and stay cities
    loadItineraryMeta: function (callback) {
      const self = this;
      let pending = 2;
      const done = () => {
        pending--;
        if (pending <= 0 && typeof callback === "function") {
          callback();
        }
      };

      if (self.propertiesList && self.propertiesList.length > 0) {
        pending--;
      } else {
        $.ajax({
          url: "/api/v3/properties/?status=active&limit=200",
          type: "GET",
          dataType: "json",
          success: function (res) {
            if (res && res.properties) {
              self.propertiesList = res.properties;
              const citySet = new Set();
              res.properties.forEach((p) => {
                if (p.city && p.city.trim()) {
                  citySet.add(p.city.trim());
                }
              });
              if (res.locations && Array.isArray(res.locations)) {
                res.locations.forEach((loc) => {
                  if (loc && loc.trim()) citySet.add(loc.trim());
                });
              }
              self.availableStayCities = Array.from(citySet).sort();
            }
            done();
          },
          error: function () {
            self.propertiesList = self.propertiesList || [];
            self.availableStayCities = self.availableStayCities || [];
            done();
          },
        });
      }

      if (self.vehiclesList && self.vehiclesList.length > 0) {
        pending--;
      } else {
        $.ajax({
          url: "/api/v3/vehicles/list/",
          type: "GET",
          dataType: "json",
          success: function (vRes) {
            if (vRes && (vRes.vehicles || vRes.data)) {
              self.vehiclesList = vRes.vehicles || vRes.data || [];
            }
            done();
          },
          error: function () {
            self.vehiclesList = self.vehiclesList || [];
            done();
          },
        });
      }

      if (pending <= 0 && typeof callback === "function") {
        callback();
      }
    },

    buildCitySelectHtml: function (currentCity, dayIndex) {
      const self = this;
      const cities = self.availableStayCities || [];
      const curr = (currentCity || "").toLowerCase().trim();

      let destCity = "";
      if (
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.destination
      ) {
        destCity = self.leadData.travel_details.destination.trim();
      } else if (self.selectedPackage && self.selectedPackage.tour_package_city) {
        destCity = self.selectedPackage.tour_package_city.trim();
      }

      const allOptions = new Set(cities);
      if (destCity) allOptions.add(destCity);
      if (
        currentCity &&
        currentCity.trim() &&
        !currentCity.toLowerCase().includes("overnight") &&
        !currentCity.toLowerCase().includes("trip end") &&
        !currentCity.toLowerCase().includes("tour end") &&
        !currentCity.toLowerCase().includes("no stay") &&
        !currentCity.toLowerCase().includes("transit")
      ) {
        allOptions.add(currentCity.trim());
      }

      const isOvernight =
        curr.includes("overnight") ||
        curr.includes("in transit") ||
        curr === "overnight_journey";
      const isTripEnd =
        curr.includes("trip end") ||
        curr.includes("tour end") ||
        curr.includes("departure (no stay)") ||
        curr === "trip_end";
      const isSelfNoStay =
        curr.includes("self / no") ||
        curr.includes("no hotel") ||
        curr === "no_stay";

      let html = `<option value="">Select Stay City</option>`;

      // Special Transit / Non-Stay Options
      html += `<optgroup label="Transit & Tour Status (No Hotel Stay)">`;
      html += `<option value="Overnight Journey (In Transit)" ${isOvernight ? "selected" : ""}>🚌 Overnight Journey (In Transit)</option>`;
      html += `<option value="Tour End / Departure (No Stay)" ${isTripEnd ? "selected" : ""}>🏁 Tour End / Departure (No Stay)</option>`;
      html += `<option value="Self / No Hotel Stay Needed" ${isSelfNoStay ? "selected" : ""}>❌ Self / No Hotel Stay Needed</option>`;
      html += `</optgroup>`;

      // Regular Destination Cities
      html += `<optgroup label="Stay Cities & Destinations">`;
      Array.from(allOptions)
        .sort()
        .forEach((c) => {
          const isSel = c.toLowerCase().trim() === curr ? "selected" : "";
          html += `<option value="${escapeHtml(c)}" ${isSel}>${escapeHtml(c)}</option>`;
        });
      html += `</optgroup>`;

      return html;
    },

    setupPickupDropSelects: function (pickupVal, dropVal) {
      const self = this;
      const cities = self.availableStayCities || [];

      const buildOptions = (selectedVal, defaultLabel) => {
        const valLower = (selectedVal || "").toLowerCase().trim();
        let html = `<option value="">-- ${defaultLabel} --</option>`;
        const allOpts = new Set(cities);
        if (selectedVal && selectedVal.trim()) allOpts.add(selectedVal.trim());

        Array.from(allOpts)
          .sort()
          .forEach((c) => {
            const isSel = c.toLowerCase().trim() === valLower ? "selected" : "";
            html += `<option value="${escapeHtml(c)}" ${isSel}>${escapeHtml(c)}</option>`;
          });
        return html;
      };

      const $pickup = $("#itineraryCustomPickup");
      const $drop = $("#itineraryCustomDrop");

      if ($pickup.hasClass("select2-hidden-accessible")) {
        $pickup.select2("destroy");
      }
      if ($drop.hasClass("select2-hidden-accessible")) {
        $drop.select2("destroy");
      }

      $pickup.html(buildOptions(pickupVal, "Select or Type Pickup Location"));
      $drop.html(buildOptions(dropVal, "Select or Type Drop Location"));

      if (typeof $.fn.select2 !== "undefined") {
        $pickup.select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          tags: true,
          placeholder: "Select or type pickup location...",
        });

        $drop.select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          tags: true,
          placeholder: "Select or type drop location...",
        });
      }
    },

    getPropertyCategoryOptionsHtml: function (selectedCategory) {
      const cats = [
        { id: "all",            label: "All Categories" },
        { id: "3_star_hotel",   label: "3 Star (Comfort)" },
        { id: "4_star_hotel",   label: "4 Star (First Class)" },
        { id: "5_star_hotel",   label: "5 Star (Luxury)" },
        { id: "resorts",        label: "Resort & Spa" },
        { id: "heritage_hotel", label: "Heritage Hotel" },
        { id: "home_stay",      label: "Homestay / Villa" },
        { id: "tent",           label: "Camp / Tent" },
        { id: "2_star_hotel",   label: "2 Star (Standard)" },
      ];
      const sel = (selectedCategory || "3_star_hotel").toLowerCase().trim();
      let html = "";
      cats.forEach((c) => {
        const isSel =
          c.id === sel ||
          (sel === "3" && c.id === "3_star_hotel") ||
          (sel === "4" && c.id === "4_star_hotel") ||
          (sel === "5" && c.id === "5_star_hotel") ||
          (sel === "luxury" && c.id === "5_star_hotel");
        html += `<option value="${c.id}" ${isSel ? "selected" : ""}>${c.label}</option>`;
      });
      return html;
    },

    buildHotelSelectHtml: function (currentCity, selectedCategory, selectedHotelName) {
      const self = this;
      const props = self.propertiesList || [];
      const currCityLower = (currentCity || "").toLowerCase().trim();
      const selHotelLower = (selectedHotelName || "").toLowerCase().trim();
      const selCatLower = (selectedCategory || "all").toLowerCase().trim();

      // City-based no-stay detection (driven by Stay City selection)
      const isNoStayOption =
        currCityLower.includes("overnight") ||
        currCityLower.includes("transit") ||
        currCityLower.includes("tour end") ||
        currCityLower.includes("trip end") ||
        currCityLower.includes("no stay") ||
        currCityLower.includes("no hotel");

      if (isNoStayOption) {
        let label = "No Hotel Required";
        if (currCityLower.includes("overnight") || currCityLower.includes("transit")) {
          label = "Overnight / In Transit — No Hotel Stay";
        } else if (currCityLower.includes("end") || currCityLower.includes("departure")) {
          label = "Tour End / Departure — No Hotel Stay";
        } else if (currCityLower.includes("self")) {
          label = "Self Arranged — No Hotel Stay";
        }
        return `<option value="no_hotel" selected>${label}</option>`;
      }

      let matchedProps = [];

      props.forEach((p) => {
        const pCity = (p.city || "").toLowerCase().trim();
        if (currCityLower && pCity === currCityLower) {
          if (selCatLower && selCatLower !== "all") {
            const pType = (p.type || "").toLowerCase().trim();
            if (
              pType === selCatLower ||
              (selCatLower === "3_star_hotel" && pType.includes("3")) ||
              (selCatLower === "4_star_hotel" && pType.includes("4")) ||
              (selCatLower === "5_star_hotel" && (pType.includes("5") || pType.includes("luxury"))) ||
              (selCatLower === "resorts" && pType.includes("resort"))
            ) {
              matchedProps.push(p);
            }
          } else {
            matchedProps.push(p);
          }
        }
      });

      let html = `<option value="">-- Choose Hotel in ${escapeHtml(currentCity || 'Stay City')} (${matchedProps.length} Available) --</option>`;

      if (matchedProps.length > 0) {
        matchedProps.forEach((p) => {
          const isSel =
            p.title.toLowerCase().trim() === selHotelLower ? "selected" : "";
          const stateLabel = p.state ? `, ${escapeHtml(p.state)}` : "";
          html += `<option value="${p.id}" data-type="${escapeHtml(p.type || "")}" data-city="${escapeHtml(p.city || "")}" data-state="${escapeHtml(p.state || "")}" ${isSel}>${escapeHtml(p.title)} — ${escapeHtml(p.city || "")}${stateLabel}</option>`;
        });
      } else {
        html += `<option value="" disabled>No properties found in ${escapeHtml(currentCity || 'selected city')}</option>`;
      }

      const isCustomSelected =
        selectedHotelName &&
        !matchedProps.some((p) => p.title.toLowerCase().trim() === selHotelLower);

      html += `<option value="custom" ${isCustomSelected ? "selected" : ""}>Enter Custom Hotel Name</option>`;

      return html;
    },

    getHotelSelect2Opts: function ($parentModal) {
      return {
        dropdownParent: $parentModal || $("#modalSendItinerary"),
        width: "100%",
        placeholder: "-- Select Property / Hotel --",
        templateResult: function (data) {
          if (!data.id || !data.element) return data.text;
          const $el = $(data.element);
          const city  = $el.attr("data-city")  || "";
          const state = $el.attr("data-state") || "";
          // Strip the appended " — City, State" from the raw option text to get just the name
          const rawText = data.text || "";
          const name = rawText.replace(/\s+—\s+.*$/, "").trim() || rawText;
          const locationLine = [city, state].filter(Boolean).join(", ");
          if (!locationLine) return $("<span>").text(name);
          return $(
            `<span class="v3-hotel-opt">
               <span class="v3-hotel-opt-name">${$("<span>").text(name).html()}</span>
               <span class="v3-hotel-opt-loc"><i class="fi fi-rr-marker"></i> ${$("<span>").text(locationLine).html()}</span>
             </span>`
          );
        },
        templateSelection: function (data) {
          if (!data.id) return data.text;
          // Show only the hotel name (strip " — City, State" suffix)
          return (data.text || "").replace(/\s+—\s+.*$/, "").trim() || data.text;
        },
      };
    },

    buildVehicleSelectHtml: function (selectedVehicle) {

      const self = this;
      const vehicles = self.vehiclesList || [];
      const selLower = (selectedVehicle || "").toLowerCase().trim();

      let html = `<option value="">-- Choose Transport / Fleet --</option>`;

      if (vehicles.length > 0) {
        html += `<optgroup label="Fleet Vehicles">`;
        vehicles.forEach((v) => {
          const vTitle = v.title || v.name;
          const vType = v.type_label || v.type || "";
          const isSel =
            vTitle.toLowerCase() === selLower ||
            (selLower && selLower.includes(vTitle.toLowerCase()));
          html += `<option value="${escapeHtml(vTitle)}" ${isSel ? "selected" : ""}>${escapeHtml(vTitle)}${vType ? ` (${escapeHtml(vType)})` : ""}</option>`;
        });
        html += `</optgroup>`;
      }

      const standardTransports = [
        "Private Sedan (Dzire / Etios)",
        "SUV (Innova / Ertiga)",
        "Tempo Traveller",
        "Luxury Coach",
        "Speedboat / Ferry",
        "Flight / Air Transfer",
      ];

      html += `<optgroup label="Standard Categories">`;
      standardTransports.forEach((st) => {
        const isSel =
          st.toLowerCase() === selLower ||
          (selLower && st.toLowerCase().includes(selLower));
        html += `<option value="${escapeHtml(st)}" ${isSel ? "selected" : ""}>${escapeHtml(st)}</option>`;
      });
      html += `</optgroup>`;

      const isCustomSelected =
        selectedVehicle &&
        !vehicles.some((v) => (v.title || "").toLowerCase() === selLower) &&
        !standardTransports.some((st) => st.toLowerCase() === selLower);

      html += `<option value="custom" ${isCustomSelected ? "selected" : ""}>Enter Custom Transport</option>`;

      return html;
    },

    loadRoomTypesForHotel: function (propertyId, $roomSelect, selectedRoomType) {
      const selRoomLower = (selectedRoomType || "deluxe room")
        .toLowerCase()
        .trim();

      const renderDefaultRooms = () => {
        const defaultRooms = [
          "Deluxe Room",
          "Standard Room",
          "Superior Room",
          "Executive Suite",
          "Family Room",
        ];
        let html = `<option value="">-- Select Room Type --</option>`;
        defaultRooms.forEach((r) => {
          const isSel = r.toLowerCase() === selRoomLower ? "selected" : "";
          html += `<option value="${r}" ${isSel}>${r}</option>`;
        });
        html += `<option value="custom">Enter Custom Room</option>`;
        $roomSelect.prop("disabled", false).html(html);
        if (
          typeof $.fn.select2 !== "undefined" &&
          $roomSelect.hasClass("select2-hidden-accessible")
        ) {
          $roomSelect.trigger("change.select2");
        }
      };

      if (!propertyId || propertyId === "" || propertyId === "0" || propertyId === "no_hotel") {
        const placeholderText =
          propertyId === "no_hotel"
            ? "Not Applicable (No Stay)"
            : "Select Hotel First";
        $roomSelect
          .prop("disabled", true)
          .html(`<option value="${placeholderText}">${placeholderText}</option>`);
        if (
          typeof $.fn.select2 !== "undefined" &&
          $roomSelect.hasClass("select2-hidden-accessible")
        ) {
          $roomSelect.trigger("change.select2");
        }
        return;
      }

      if (propertyId === "custom" || parseInt(propertyId) <= 0) {
        renderDefaultRooms();
        return;
      }

      $roomSelect
        .prop("disabled", false)
        .html('<option value="">Loading room types...</option>');
      if (
        typeof $.fn.select2 !== "undefined" &&
        $roomSelect.hasClass("select2-hidden-accessible")
      ) {
        $roomSelect.trigger("change.select2");
      }

      $.ajax({
        url: `/api/v3/properties/rooms/?property_id=${propertyId}`,
        type: "GET",
        dataType: "json",
        success: function (res) {
          $roomSelect.prop("disabled", false);
          if (res && res.rooms && res.rooms.length > 0) {
            let html = `<option value="">-- Select Room Type (${res.rooms.length} Available) --</option>`;
            let found = false;
            res.rooms.forEach((rm) => {
              const rName = rm.type_label || rm.type || "Deluxe Room";
              const isSel =
                rName.toLowerCase().trim() === selRoomLower ||
                (rm.type && rm.type.toLowerCase() === selRoomLower);
              if (isSel) found = true;
              html += `<option value="${escapeHtml(rName)}" ${isSel ? "selected" : ""}>${escapeHtml(rName)}</option>`;
            });
            if (!found && selectedRoomType && selectedRoomType !== "Deluxe Room") {
              html =
                `<option value="${escapeHtml(selectedRoomType)}" selected>${escapeHtml(selectedRoomType)}</option>` +
                html;
            }
            html += `<option value="custom">Enter Custom Room</option>`;
            $roomSelect.html(html);
            if (
              typeof $.fn.select2 !== "undefined" &&
              $roomSelect.hasClass("select2-hidden-accessible")
            ) {
              $roomSelect.trigger("change.select2");
            }
          } else {
            renderDefaultRooms();
          }
        },
        error: function () {
          renderDefaultRooms();
        },
      });
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

      self.loadItineraryMeta(function () {
        days.forEach((d, idx) => {
          self.appendDayCard(d, idx + 1);
        });
      });
    },

    appendDayCard: function (d, num) {
      const self = this;
      const title = d.day || `Day ${num} - Tour Plan`;
      const overview = d.overview || "";
      const city = d.city || d.stay_city || "";
      const hotel = d.hotel || "";
      const hotelStars = d.hotel_stars || "3_star_hotel";
      const roomType = d.room_type || "Deluxe Room";
      const mealPlan = d.meal_plan || "CP";
      const vehicle = d.vehicle || "Private Sedan";
      const transferNotes = d.transfer_notes || "";

      const cityLower = city.toLowerCase();
      const isNoStayCity =
        cityLower.includes("overnight") ||
        cityLower.includes("transit") ||
        cityLower.includes("tour end") ||
        cityLower.includes("trip end") ||
        cityLower.includes("no stay") ||
        cityLower.includes("no hotel");

      const citySelectHtml = self.buildCitySelectHtml(city, num);
      const categorySelectHtml = self.getPropertyCategoryOptionsHtml(hotelStars);
      const hotelSelectHtml = self.buildHotelSelectHtml(city, hotelStars, hotel);
      const vehicleSelectHtml = self.buildVehicleSelectHtml(vehicle);

      const html = `
        <div class="v3-itin-day-card" data-day-index="${num}">
          <div class="v3-itin-day-card-header">
            <span class="v3-itin-day-badge">Day ${num}</span>
            <div class="v3-itin-stay-city-chip" title="Select stay city or transit status for this day">
              <i class="fi fi-rr-marker"></i>
              <span class="stay-city-label">Night Stay / City:</span>
              <select class="itin-day-city-select" data-day-index="${num}">
                ${citySelectHtml}
              </select>
            </div>
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
              
              <div class="v3-form-grid-2 v3-mb-10">
                <div>
                  <label class="v3-modal-label">Stay Type / Rating</label>
                  <select class="leads-filter-select v3-select-full itin-day-hotel-stars">
                    ${categorySelectHtml}
                  </select>
                </div>
                <div>
                  <label class="v3-modal-label">Property / Hotel</label>
                  <select class="leads-filter-select v3-select-full itin-day-hotel-select" data-day-index="${num}" ${isNoStayCity ? 'disabled' : ''}>
                    ${hotelSelectHtml}
                  </select>
                  <input type="text" class="v3-modal-input itin-day-hotel-custom ${hotel && !hotelSelectHtml.includes('selected') ? '' : 'v3-hidden'}" value="${hotel}" placeholder="Enter custom hotel name...">
                </div>
              </div>

              <div class="v3-form-grid-2">
                <div>
                  <label class="v3-modal-label">Room Type</label>
                  <select class="leads-filter-select v3-select-full itin-day-room-type" ${(!hotel || isNoStayCity) ? 'disabled' : ''}>
                    <!-- Populated dynamically based on property -->
                  </select>
                  <input type="text" class="v3-modal-input itin-day-room-type-custom v3-hidden" value="${roomType}" placeholder="Enter custom room">
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
                  ${vehicleSelectHtml}
                </select>
                <input type="text" class="v3-modal-input itin-day-vehicle-custom ${vehicle && !vehicleSelectHtml.includes('selected') ? '' : 'v3-hidden'}" value="${vehicle}" placeholder="Enter custom transport...">
              </div>
              <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transfer Route / Notes</label>
                <input type="text" class="v3-modal-input itin-day-transfer-notes" value="${transferNotes}" placeholder="e.g. Airport to Hotel Transfer & Evening City Drive">
              </div>
            </div>
          </div>
        </div>
      `;
      const $card = $(html);
      $("#itinDaysStack").append($card);

      // Load initial room types for selected hotel
      const selectedPropId = $card.find(".itin-day-hotel-select").val();
      self.loadRoomTypesForHotel(
        selectedPropId,
        $card.find(".itin-day-room-type"),
        roomType,
      );

      // Initialize Select2 on all Day Card Dropdowns
      if (typeof $.fn.select2 !== "undefined") {
        $card.find(".itin-day-city-select").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "Stay City",
        });

        $card.find(".itin-day-hotel-select").select2(self.getHotelSelect2Opts($("#modalSendItinerary")));

        $card.find(".itin-day-hotel-stars").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });

        $card.find(".itin-day-room-type").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "Select Room Type",
        });

        $card.find(".itin-day-meal-plan").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });

        $card.find(".itin-day-vehicle-type").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          placeholder: "-- Choose Transport / Fleet --",
        });
      }

      const $overviewTextarea = $card.find(".itin-day-overview");
      if (typeof $.fn.v3Editor === "function") {
        $overviewTextarea.v3Editor({
          minHeight: 60,
          placeholder: "Describe the day's schedule, sightseeing spots, and activities...",
          toolbar: [
            "history",
            "format",
            "styling",
            "colors",
            "alignment",
            "lists",
            "inserts",
          ],
        });
      }
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
          self.initDateTimePicker();
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

        const followupReason = $("#composerFollowupReason").val() || "General Follow-up";
        const postData = {
          leadId: self.leadId,
          title: type === "followup" ? followupReason : "Note",
          category: type === "followup" ? followupReason : "Note",
          msg: msg,
        };

        if (type === "followup") {
          postData.followupTime = $("#composerFollowupTime").val();
        }

        self.submitComposerNote(postData, $btn);
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

      // Duration input change in modal: dynamically update snapshot badge (Nights / Days)
      $(document).on("input change keyup", "#editLeadDuration", function () {
        const nights = parseInt($(this).val(), 10);
        if (!isNaN(nights) && nights > 0) {
          const days = nights + 1;
          $("#editCardPkgDuration").html(`<i class="fi fi-rr-moon"></i> <strong>${nights}N / ${days}D</strong>`).show();
        } else {
          $("#editCardPkgDuration").hide();
        }
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
          $("#itinerarySearchSection").removeClass("v3-hidden");
          $("#btnChangeItinerary").addClass("v3-hidden");
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
              options += `<option value="custom" data-title="Create Custom Itinerary from Scratch" data-duration="Custom" data-price="0">Create Custom Itinerary from Scratch</option>`;

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

      // Recalculate total price on traveler count or per-person rate change
      $(document).on(
        "input change",
        "#itineraryCustomAdultCount, #itineraryCustomChildCount, #itineraryCustomInfantCount, #itineraryCustomAdultPrice, #itineraryCustomChildPrice, #itineraryCustomInfantPrice",
        function () {
          const adults = parseInt($("#itineraryCustomAdultCount").val()) || 0;
          const children = parseInt($("#itineraryCustomChildCount").val()) || 0;
          const infants = parseInt($("#itineraryCustomInfantCount").val()) || 0;

          const ap = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
          const cp = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
          const ip = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

          const total = ap * adults + cp * children + ip * infants;
          $("#itineraryCustomTotalPrice").val(total);

          const travelerParts = [];
          if (adults) travelerParts.push(`${adults} Adults`);
          if (children) travelerParts.push(`${children} Children`);
          if (infants) travelerParts.push(`${infants} Infants`);
          $("#itinTravelerCountBadge").text(
            travelerParts.join(", ") || "0 Travelers",
          );
        },
      );

      // Night Stay / City Dropdown Change on Itinerary Day
      $(document).on("change", ".itin-day-city-select", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const selectedCity = $(this).val();
        const selectedCategory = $card.find(".itin-day-hotel-stars").val();
        const $hotelSelect = $card.find(".itin-day-hotel-select");
        const $roomSelect  = $card.find(".itin-day-room-type");
        const $customInput = $card.find(".itin-day-hotel-custom");

        const cityLower = (selectedCity || "").toLowerCase();
        const noStayCity =
          cityLower.includes("overnight") ||
          cityLower.includes("transit")   ||
          cityLower.includes("tour end")  ||
          cityLower.includes("trip end")  ||
          cityLower.includes("no stay")   ||
          cityLower.includes("no hotel");

        if ($hotelSelect.hasClass("select2-hidden-accessible")) {
          $hotelSelect.select2("destroy");
        }

        if (noStayCity) {
          // Lock hotel & room — no stay needed for this city
          let noStayLabel = "No Hotel Stay";
          if (cityLower.includes("overnight") || cityLower.includes("transit")) {
            noStayLabel = "Overnight / In Transit \u2014 No Hotel Stay";
          } else if (cityLower.includes("end") || cityLower.includes("departure")) {
            noStayLabel = "Tour End / Departure \u2014 No Hotel Stay";
          } else if (cityLower.includes("self")) {
            noStayLabel = "Self Arranged \u2014 No Hotel Stay";
          }
          $hotelSelect
            .html(`<option value="no_hotel" selected>${noStayLabel}</option>`)
            .prop("disabled", true);
          $customInput.addClass("v3-hidden");
          $roomSelect
            .prop("disabled", true)
            .html(`<option value="">Not Applicable (No Stay)</option>`);
          if (typeof $.fn.select2 !== "undefined" && $roomSelect.hasClass("select2-hidden-accessible")) {
            $roomSelect.trigger("change.select2");
          }
          return;
        }

        // Normal city — re-enable and rebuild hotel list
        $hotelSelect.prop("disabled", false);
        $hotelSelect.html(
          self.buildHotelSelectHtml(selectedCity, selectedCategory, ""),
        );

        if (typeof $.fn.select2 !== "undefined") {
          $hotelSelect.select2(self.getHotelSelect2Opts($("#modalSendItinerary")));
        }
        $hotelSelect.trigger("change");
      });

      // Stay Type / Rating Change -> Filter Hotels
      $(document).on("change", ".itin-day-hotel-stars", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const selectedCategory = $(this).val();
        const selectedCity = $card.find(".itin-day-city-select").val();
        const $hotelSelect = $card.find(".itin-day-hotel-select");
        const currentHotelName = $hotelSelect
          .find("option:selected")
          .text()
          .replace(/\s+—\s+.*$/, "")
          .trim();

        if ($hotelSelect.hasClass("select2-hidden-accessible")) {
          $hotelSelect.select2("destroy");
        }

        $hotelSelect.html(
          self.buildHotelSelectHtml(selectedCity, selectedCategory, currentHotelName),
        );

        if (typeof $.fn.select2 !== "undefined") {
          $hotelSelect.select2(self.getHotelSelect2Opts($("#modalSendItinerary")));
        }
        $hotelSelect.trigger("change");
      });

      // Hotel Select Change on Itinerary Day
      $(document).on("change", ".itin-day-hotel-select", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const propId = $(this).val();
        const $customInput = $card.find(".itin-day-hotel-custom");
        const $roomSelect = $card.find(".itin-day-room-type");
        const $starSelect = $card.find(".itin-day-hotel-stars");

        if (propId === "custom") {
          $customInput.removeClass("v3-hidden").focus();
        } else {
          $customInput.addClass("v3-hidden");
        }

        const selectedOption = $(this).find("option:selected");
        const pType = (selectedOption.data("type") || "").toLowerCase();
        const pCity = selectedOption.data("city");

        if (pCity && !$card.find(".itin-day-city-select").val()) {
          $card.find(".itin-day-city-select").val(pCity).trigger("change.select2");
        }

        // Sync star rating/category if property has a valid type and category is 'all'
        if (pType && $starSelect.val() === "all") {
          $starSelect.val(pType).trigger("change.select2");
        }

        self.loadRoomTypesForHotel(propId, $roomSelect, $roomSelect.val());
      });

      // Room Type Custom Toggle
      $(document).on("change", ".itin-day-room-type", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const val = $(this).val();
        const $custom = $card.find(".itin-day-room-type-custom");
        if (val === "custom") {
          $custom.removeClass("v3-hidden").focus();
        } else {
          $custom.addClass("v3-hidden");
        }
      });

      // Vehicle Type Custom Toggle
      $(document).on("change", ".itin-day-vehicle-type", function () {
        const $card = $(this).closest(".v3-itin-day-card");
        const val = $(this).val();
        const $custom = $card.find(".itin-day-vehicle-custom");
        if (val === "custom") {
          $custom.removeClass("v3-hidden").focus();
        } else {
          $custom.addClass("v3-hidden");
        }
      });

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
          const $overviewTextarea = $(this).find(".itin-day-overview");
          const editor = $overviewTextarea.data("v3Editor");
          const dayOverview = (
            editor ? editor.getContent() : $overviewTextarea.val()
          ).trim();
          const stayCity = $(this).find(".itin-day-city-select").val() || "";

          let hotelName = "";
          const $hotelSel = $(this).find(".itin-day-hotel-select");
          const hotelVal = $hotelSel.val();
          if (hotelVal === "custom") {
            hotelName = $(this).find(".itin-day-hotel-custom").val().trim();
          } else if (hotelVal && hotelVal !== "no_hotel") {
            hotelName = $hotelSel
              .find("option:selected")
              .text()
              .replace(/\s+—\s+.*$/, "")
              .trim();
          } else {
            hotelName = $(this).find(".itin-day-hotel-custom").val().trim() || "";
          }

          const hotelStars = $(this).find(".itin-day-hotel-stars").val();

          let roomType = $(this).find(".itin-day-room-type").val() || "Deluxe Room";
          if (roomType === "custom") {
            roomType =
              $(this).find(".itin-day-room-type-custom").val().trim() ||
              "Deluxe Room";
          }

          const mealPlan = $(this).find(".itin-day-meal-plan").val();
          let vehicleType = $(this).find(".itin-day-vehicle-type").val();
          if (vehicleType === "custom" || !vehicleType) {
            vehicleType =
              $(this).find(".itin-day-vehicle-custom").val().trim() ||
              "Private Sedan (Dzire / Etios)";
          }
          const transferNotes = $(this)
            .find(".itin-day-transfer-notes")
            .val()
            .trim();

          structuredItin.push({
            day: dayTitle,
            city: stayCity,
            stay_city: stayCity,
            overview: dayOverview,
            hotel: hotelName,
            stay: hotelName,
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

        const templateToUse =
          (self.selectedPackage && self.selectedPackage.tour_package_template) ||
          self.selectedPackageTemplate ||
          "mit";

        const travelDate =
          $("#itineraryCustomTravelDate").val().trim() ||
          (self.leadData &&
          self.leadData.travel_details &&
          self.leadData.travel_details.date_of_travel
            ? self.leadData.travel_details.date_of_travel
            : "Flexible");

        const adults = parseInt($("#itineraryCustomAdultCount").val()) || 1;
        const children = parseInt($("#itineraryCustomChildCount").val()) || 0;
        const infants = parseInt($("#itineraryCustomInfantCount").val()) || 0;
        const pickupLocation = $("#itineraryCustomPickup").val() || "";
        const dropLocation = $("#itineraryCustomDrop").val() || "";
        const travelTheme = $("#itineraryCustomTheme").val();

        const postData = {
          id: pkgId,
          refId: self.leadData ? self.leadData.id : self.leadId,
          refType: "enquiry",
          mrp: adultPrice || 0,
          isDownload: !!isDownload,
          content: {
            title: title,
            travel_date: travelDate,
            pickup_location: pickupLocation,
            drop_location: dropLocation,
            travel_theme: travelTheme,
            theme: travelTheme,
            adults_count: adults,
            children_count: children,
            infants_count: infants,
            template: templateToUse,
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

      // Change Itinerary CTA in Builder Modal
      $(document).on("click", "#btnChangeItinerary", function (e) {
        e.preventDefault();
        $("#itinerarySearchSection").removeClass("v3-hidden");
        $("#btnChangeItinerary").addClass("v3-hidden");
        $("#modalSendItinerary .v3-modal-fullscreen-body").animate(
          { scrollTop: 0 },
          250,
        );
      });

      // Preview Itinerary CTA in Builder Modal (Server-driven Iframe Preview using Selected Package Template)
      $(document).on(
        "click",
        "#btnPreviewItinerary, #btnPreviewItineraryBottom",
        function (e) {
          e.preventDefault();
          const title =
            $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
          const travelDate =
            $("#itineraryCustomTravelDate").val().trim() ||
            (self.leadData &&
            self.leadData.travel_details &&
            self.leadData.travel_details.date_of_travel
              ? self.leadData.travel_details.date_of_travel
              : "Flexible");
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
          const adults =
            parseInt($("#itineraryCustomAdultCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.adults || 1)
              : 1);
          const children =
            parseInt($("#itineraryCustomChildCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.children || 0)
              : 0);
          const infants =
            parseInt($("#itineraryCustomInfantCount").val()) ||
            (self.leadData && self.leadData.travel_details
              ? parseInt(self.leadData.travel_details.infants || 0)
              : 0);

          const structuredItin = [];
          $("#itinDaysStack .v3-itin-day-card").each(function () {
            const dayTitle = $(this).find(".itin-day-title").val().trim();
            const $overviewTextarea = $(this).find(".itin-day-overview");
            const editor = $overviewTextarea.data("v3Editor");
            const dayOverview = (
              editor ? editor.getContent() : $overviewTextarea.val()
            ).trim();
            const stayCity = $(this).find(".itin-day-city-select").val() || "";

            let hotelName = "";
            const $hotelSel = $(this).find(".itin-day-hotel-select");
            const hotelVal = $hotelSel.val();
            if (hotelVal === "custom") {
              hotelName = $(this).find(".itin-day-hotel-custom").val().trim();
            } else if (hotelVal) {
              hotelName = $hotelSel
                .find("option:selected")
                .text()
                .replace(/\s*\(.*?\)\s*$/, "")
                .trim();
            } else {
              hotelName = $(this).find(".itin-day-hotel-custom").val().trim() || "";
            }

            const hotelStars = $(this).find(".itin-day-hotel-stars").val();

            let roomType = $(this).find(".itin-day-room-type").val() || "Deluxe Room";
            if (roomType === "custom") {
              roomType =
                $(this).find(".itin-day-room-type-custom").val().trim() ||
                "Deluxe Room";
            }

            const mealPlan = $(this).find(".itin-day-meal-plan").val();
            let vehicleType = $(this).find(".itin-day-vehicle-type").val();
            if (vehicleType === "custom" || !vehicleType) {
              vehicleType =
                $(this).find(".itin-day-vehicle-custom").val().trim() ||
                "Private Sedan (Dzire / Etios)";
            }
            const transferNotes = $(this)
              .find(".itin-day-transfer-notes")
              .val()
              .trim();

            structuredItin.push({
              day: dayTitle,
              city: stayCity,
              stay_city: stayCity,
              overview: dayOverview,
              hotel: hotelName,
              stay: hotelName,
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

          const templateToUse =
            (self.selectedPackage && self.selectedPackage.tour_package_template) ||
            self.selectedPackageTemplate ||
            "mit";

          const pickupLocation = $("#itineraryCustomPickup").val() || "";
          const dropLocation = $("#itineraryCustomDrop").val() || "";
          const travelTheme = $("#itineraryCustomTheme").val() || "standard";

          const payload = {
            title: title,
            city: dest,
            theme: travelTheme,
            travelTheme: travelTheme,
            pickupLocation: pickupLocation,
            dropLocation: dropLocation,
            pickup_location: pickupLocation,
            drop_location: dropLocation,
            adults: adults,
            children: children,
            infants: infants,
            template: templateToUse,
            hotel: self.selectedPackage
              ? self.selectedPackage.tour_package_hotel
              : "",
            totalPrice: totalPrice,
            adultPrice: adultPrice,
            childPrice: childPrice,
            infantPrice: infantPrice,
            clientName: clientName,
            travelDate: travelDate,
            overview: overview,
            inclusions: inclusions,
            exclusions: exclusions,
            days: structuredItin,
            coverImage: self.selectedPackage
              ? self.selectedPackage.tour_package_image ||
                self.selectedPackage.gallery_image
              : "",
            format: "html",
            hideActions: true,
            preview: true,
          };

          $("#livePreviewHeaderTitle").text(title);
          $("#modalItineraryLivePreview").addClass("is-active");

          const iframe = document.getElementById("livePreviewIframe");
          if (iframe) {
            iframe.srcdoc = "";
          }
          $("#livePreviewLoader").removeClass("v3-hidden");

          $.ajax({
            url: "/api/v3/itinerary/show/",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            dataType: "html",
            success: function (renderedHtml) {
              $("#livePreviewLoader").addClass("v3-hidden");
              if (iframe) {
                iframe.srcdoc = renderedHtml;
              }
            },
            error: function () {
              $("#livePreviewLoader").addClass("v3-hidden");
              self.notify("Failed to render preview template.", "error");
            },
          });
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

      // 9. Other Modals & Follow-up Presets
      $(document).on("click", "#btnHeroSetFollowUp, #btnSideSetFollowUp", function () {
        $(".v3-tab-link[data-tab='tab-activity']").trigger("click");
        $(".v3-composer-type-btn[data-type='followup']").trigger("click");
        $("#composerNoteInput").focus();
        self.initDateTimePicker();
      });

      // Follow-Up Quick Preset Chips Click
      $(document).on("click", ".v3-preset-chip", function (e) {
        e.preventDefault();
        const $chip = $(this);
        const $wrap = $chip.closest(".v3-followup-presets-wrap");
        const targetSelector = $chip.closest(".v3-preset-chips").data("target");
        const $targetInput = $(targetSelector);
        const preset = $chip.data("preset");

        $wrap.find(".v3-preset-chip").removeClass("active");
        $chip.addClass("active");

        const now = typeof moment !== "undefined" ? moment() : null;
        if (!now) return;

        let targetMoment;

        switch (preset) {
          case "in_2_hours": {
            targetMoment = now.clone().add(2, "hours");
            const remMin = targetMoment.minute() % 15;
            if (remMin > 0) targetMoment.add(15 - remMin, "minutes");
            targetMoment.second(0);
            if (targetMoment.hour() >= 21 || targetMoment.hour() < 9) {
              targetMoment = now.clone().add(1, "days").hour(10).minute(0).second(0);
            }
            break;
          }
          case "tomorrow_morning": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(10).minute(30).second(0);
            break;
          }
          case "tomorrow_afternoon": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(15).minute(0).second(0);
            break;
          }
          case "tomorrow_evening": {
            let nextDay = now.clone().add(1, "days");
            if (now.day() === 6) nextDay = now.clone().add(2, "days");
            targetMoment = nextDay.hour(18).minute(0).second(0);
            break;
          }
          case "in_2_days": {
            targetMoment = now.clone().add(2, "days").hour(11).minute(0).second(0);
            break;
          }
          case "next_monday": {
            const daysUntilMon = (1 + 7 - now.day()) % 7 || 7;
            targetMoment = now.clone().add(daysUntilMon, "days").hour(10).minute(30).second(0);
            break;
          }
          default: {
            targetMoment = self.getDefaultFollowupDateTime();
          }
        }

        const datePart = targetMoment.format("YYYY-MM-DD");
        const time24Part = targetMoment.format("HH:mm");
        const time12Part = targetMoment.format("hh:mm A");
        const formattedStr = datePart + ", " + time12Part;

        $targetInput.val(formattedStr);

        // Sync native date and time inputs
        if (targetSelector === "#modalFollowUpTime") {
          $("#modalFollowUpDate").val(datePart);
          $("#modalFollowUpTimeInput").val(time24Part);
          const $pills = $('.v3-time-quick-pills[data-target="#modalFollowUpTimeInput"]');
          $pills.find(".v3-time-pill").removeClass("active");
          $pills.find(`.v3-time-pill[data-time="${time24Part}"]`).addClass("active");
          self.checkSlotConflict(formattedStr, $("#modalSlotStatus"));
        } else if (targetSelector === "#composerFollowupTime") {
          $("#composerFollowupDate").val(datePart);
          $("#composerFollowupTimeInput").val(time24Part);
          const $pills = $('.v3-time-quick-pills[data-target="#composerFollowupTimeInput"]');
          $pills.find(".v3-time-pill").removeClass("active");
          $pills.find(`.v3-time-pill[data-time="${time24Part}"]`).addClass("active");
          self.checkSlotConflict(formattedStr, $("#composerSlotStatus"));
        }

        $targetInput.trigger("change");
      });

      // Quick Time Pill Click
      $(document).on("click", ".v3-time-pill", function (e) {
        e.preventDefault();
        const $pill = $(this);
        const time24 = $pill.data("time");
        const targetSelect = $pill.closest(".v3-time-quick-pills").data("target");
        const $input = $(targetSelect);

        $pill.siblings(".v3-time-pill").removeClass("active");
        $pill.addClass("active");

        if ($input.length) {
          $input.val(time24).trigger("change");
        }
      });

      // Native Date/Time input changes: Sync master hidden input
      $(document).on("change", "#modalFollowUpDate, #modalFollowUpTimeInput", function () {
        const dateVal = $("#modalFollowUpDate").val() || (typeof moment !== "undefined" ? moment().add(1, "days").format("YYYY-MM-DD") : "");
        const time24Val = $("#modalFollowUpTimeInput").val() || "10:30";
        let time12 = "10:30 AM";
        if (typeof moment !== "undefined") {
          time12 = moment("2000-01-01 " + time24Val).format("hh:mm A");
        }
        const combined = dateVal + ", " + time12;
        $("#modalFollowUpTime").val(combined);

        const $pills = $('.v3-time-quick-pills[data-target="#modalFollowUpTimeInput"]');
        $pills.find(".v3-time-pill").removeClass("active");
        $pills.find(`.v3-time-pill[data-time="${time24Val}"]`).addClass("active");

        self.checkSlotConflict(combined, $("#modalSlotStatus"));
      });

      $(document).on("change", "#composerFollowupDate, #composerFollowupTimeInput", function () {
        const dateVal = $("#composerFollowupDate").val() || (typeof moment !== "undefined" ? moment().add(1, "days").format("YYYY-MM-DD") : "");
        const time24Val = $("#composerFollowupTimeInput").val() || "10:30";
        let time12 = "10:30 AM";
        if (typeof moment !== "undefined") {
          time12 = moment("2000-01-01 " + time24Val).format("hh:mm A");
        }
        const combined = dateVal + ", " + time12;
        $("#composerFollowupTime").val(combined);

        const $pills = $('.v3-time-quick-pills[data-target="#composerFollowupTimeInput"]');
        $pills.find(".v3-time-pill").removeClass("active");
        $pills.find(`.v3-time-pill[data-time="${time24Val}"]`).addClass("active");

        self.checkSlotConflict(combined, $("#composerSlotStatus"));
      });

      // Submit Schedule Follow-Up Modal (if opened directly)
      $(document).on("click", "#btnSubmitModalFollowUp", function () {
        if (!self.leadData || !(parseInt(self.leadData.agent_id || 0) > 0)) {
          self.notify("Lead is unassigned. Please assign an agent first.", "warning");
          return;
        }

        const reason = $("#modalFollowUpReason").val() || "General Follow-up";
        const fTime = $("#modalFollowUpTime").val();
        const comment = $("#modalFollowUpComment").val().trim();

        if (!fTime) {
          self.notify("Please select a valid follow-up date and time.", "warning");
          return;
        }

        const $btn = $(this);
        const postData = {
          leadId: self.leadId,
          title: reason,
          category: reason,
          msg: comment || ("Scheduled callback for " + reason),
          followupTime: fTime,
        };

        self.submitModalFollowUp(postData, $btn);
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

      // Quick verify payment from Lead Payments table with SweetAlert
      $(document).on("click", ".btn-lead-approve-payment", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const noteId = $(this).data("note-id");
        const $btn = $(this);
        if (!noteId) return;

        const doApproveLeadPayment = function () {
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
                    text: res.message || "Payment verified and lead marked as Confirmed.",
                    showConfirmButton: false,
                    timer: 1800,
                  });
                } else {
                  self.notify(res.message || "Payment verified successfully!", "success");
                }
                self.loadLeadDetails();
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
                $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Verify');
              }
            },
            error: function () {
              $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Verify');
              self.notify("Error communicating with server.", "error");
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
              doApproveLeadPayment();
            }
          });
        } else if (confirm("Are you sure you want to verify and approve this payment record? This will mark the lead as Confirmed.")) {
          doApproveLeadPayment();
        }
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
          ? parseInt(self.leadData.travel_details.adults || 2)
          : 2;
      const children =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.children || 0)
          : 0;
      const infants =
        self.leadData && self.leadData.travel_details
          ? parseInt(self.leadData.travel_details.infants || 0)
          : 0;
      const totalPrice =
        adultPrice * (adults > 0 ? adults : 2) + childPrice * children;

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

      const formatBulletList = (input, defaultFallback = "") => {
        if (!input) return defaultFallback;
        if (Array.isArray(input)) {
          return input.map((s) => formatBulletList(s)).filter(Boolean).join("\n");
        }
        let str = String(input);
        // Replace HTML break and list tags with newlines
        str = str.replace(/<\/?(li|p|div|br|tr|td)[^>]*>/gi, "\n");
        // Remove remaining HTML tags
        str = str.replace(/<[^>]*>?/gm, "");
        // Remove intro prefix phrases
        str = str.replace(/^(our\s+)?(package\s+)?(inclusions?\s*(cover|include|includes|are|:)?|exclusions?\s*(cover|covers|include|includes|are|:)?)\s*/i, "");
        str = str.replace(/^(our\s+)?package\s+includes\s+/i, "");
        str = str.replace(/^(inclusions?|exclusions?)\s*:\s*/i, "");
        // Split on comma / and if not already multiline
        if (!str.includes("\n") || str.split("\n").length <= 1) {
          str = str.replace(/,\s*and\s+/gi, "\n");
          str = str.replace(/;\s*/g, "\n");
          str = str.replace(/,\s*/g, "\n");
        }

        const lines = str
          .split("\n")
          .map((line) => {
            let l = line.trim();
            // Remove leading bullets/numbers/checks/dashes
            l = l.replace(/^[•\-*✓✗✔✕\d.):\s]+/u, "").trim();
            // Remove leading 'and '
            l = l.replace(/^and\s+/i, "").trim();
            // Remove trailing period
            l = l.replace(/\.$/, "").trim();
            // Capitalize first letter
            if (l.length > 0) {
              l = l.charAt(0).toUpperCase() + l.slice(1);
            }
            return l;
          })
          .filter((line) => line.length > 1);

        const unique = [...new Set(lines)];
        return unique.length > 0 ? unique.join("\n") : defaultFallback;
      };

      // Validate travel date — must be a real future date, else default to +7 days
      let resolvedTravelDate = "";
      const rawTravelDate =
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.date_of_travel
          ? (self.leadData.travel_details.date_of_travel || "").trim()
          : "";
      if (
        rawTravelDate &&
        rawTravelDate.toLowerCase() !== "flexible" &&
        typeof moment !== "undefined" &&
        moment(rawTravelDate, "YYYY-MM-DD", true).isValid() &&
        moment(rawTravelDate, "YYYY-MM-DD").isSameOrAfter(moment().startOf("day"))
      ) {
        resolvedTravelDate = rawTravelDate;
      } else if (typeof moment !== "undefined") {
        resolvedTravelDate = moment().add(7, "days").format("YYYY-MM-DD");
      }
      $("#itineraryCustomTravelDate").val(resolvedTravelDate);
      self.initSingleDatePickers();


      const defaultPickup =
        (pkg.tour_package_pickup || "").trim() ||
        (self.leadData &&
        self.leadData.travel_details &&
        (self.leadData.travel_details.departure_city ||
          self.leadData.travel_details.pickup_location)
          ? self.leadData.travel_details.departure_city ||
            self.leadData.travel_details.pickup_location
          : "");

      const defaultDrop =
        (pkg.tour_package_drop || "").trim() ||
        (self.leadData &&
        self.leadData.travel_details &&
        (self.leadData.travel_details.drop_location ||
          self.leadData.travel_details.destination)
          ? self.leadData.travel_details.drop_location ||
            self.leadData.travel_details.destination
          : "");

      self.loadItineraryMeta(function () {
        self.setupPickupDropSelects(defaultPickup, defaultDrop);
      });

      const defaultTheme = (
        pkg.tour_package_theme ||
        pkg.tour_package_travel_style ||
        $("#itineraryTravelStyle").val() ||
        "standard"
      )
        .toLowerCase()
        .trim();
      if ($("#itineraryCustomTheme option[value='" + defaultTheme + "']").length > 0) {
        $("#itineraryCustomTheme").val(defaultTheme);
      } else {
        $("#itineraryCustomTheme").val("standard");
      }

      if (typeof $.fn.select2 !== "undefined") {
        $("#itineraryCustomTheme").select2({
          dropdownParent: $("#modalSendItinerary"),
          width: "100%",
          minimumResultsForSearch: Infinity,
        });
      }

      $("#itineraryCustomTitle").val(pkg.tour_package_title || "");
      $("#itineraryCustomAdultCount").val(adults > 0 ? adults : 2);
      $("#itineraryCustomChildCount").val(children);
      $("#itineraryCustomInfantCount").val(infants);

      $("#itineraryCustomAdultPrice").val(adultPrice);
      $("#itineraryCustomChildPrice").val(childPrice);
      $("#itineraryCustomInfantPrice").val(0);
      $("#itineraryCustomTotalPrice").val(
        totalPrice > 0 ? totalPrice : adultPrice * (adults > 0 ? adults : 2),
      );
      $("#itineraryCustomOverview").val(stripHtml(pkg.tour_package_overview));
      $("#itineraryCustomInclusions").val(
        formatBulletList(
          pkg.tour_package_inclusions,
          "Hotel accommodation with breakfast\nAirport & sightseeing transfers\nAll toll taxes and parking charges",
        ),
      );
      $("#itineraryCustomExclusions").val(
        formatBulletList(
          pkg.tour_package_exclusions,
          "Airfare / Train tickets\nPersonal expenses & tips\nEntry fees and camera tickets",
        ),
      );

      const travelerParts = [];
      if (adults) travelerParts.push(`${adults} Adults`);
      if (children) travelerParts.push(`${children} Children`);
      if (infants) travelerParts.push(`${infants} Infants`);
      $("#itinTravelerCountBadge").text(travelerParts.join(", ") || "Auto-calculates per traveler");

      const packageCity = (pkg.tour_package_city || "").trim();
      const leadDest = (
        self.leadData &&
        self.leadData.travel_details &&
        self.leadData.travel_details.destination
          ? self.leadData.travel_details.destination
          : ""
      ).trim();
      const defaultCity = packageCity || leadDest;

      // Smart city extractor from day title or overview text
      const detectCityFromText = (txt) => {
        if (!txt) return "";
        const allCities = self.availableStayCities || [];
        const lowerTxt = txt.toLowerCase();

        // 1. Check for direct "to [City]" pattern
        for (const c of allCities) {
          const cLower = c.toLowerCase();
          const toPattern = new RegExp(
            `(?:to|in|at|stay\\s+in|arrive\\s+in|reach)\\s+${cLower}\\b`,
            "i",
          );
          if (toPattern.test(txt)) {
            return c;
          }
        }
        // 2. Check if city name is in text
        for (const c of allCities) {
          if (lowerTxt.includes(c.toLowerCase())) {
            return c;
          }
        }
        return "";
      };

      daysList = daysList.map((d, i) => {
        if (!d.city && !d.stay_city) {
          const detected =
            detectCityFromText(d.day) || detectCityFromText(d.overview);
          d.city = detected || defaultCity || "";
          d.stay_city = d.city;
        }
        return d;
      });

      self.selectedPackageTemplate = pkg.tour_package_template || "mit";

      if (pkg.tour_package_id && parseInt(pkg.tour_package_id) > 0) {
        $.ajax({
          url: `/api/v3/packages/hotels.php?package_id=${pkg.tour_package_id}`,
          type: "GET",
          dataType: "json",
          success: function (hRes) {
            if (hRes && hRes.hotels && hRes.hotels.length > 0) {
              const assignedMap = {};
              hRes.hotels.forEach((h) => {
                if (h.assigned_night > 0) {
                  assignedMap[h.assigned_night] = h;
                }
              });
              daysList = daysList.map((d, i) => {
                const nightNum = i + 1;
                if (assignedMap[nightNum]) {
                  const hObj = assignedMap[nightNum];
                  if (!d.hotel) d.hotel = hObj.title;
                  if (!d.city || d.city === defaultCity) d.city = hObj.city || d.city;
                  if (hObj.assigned_room_type && !d.room_type) {
                    d.room_type = hObj.assigned_room_type;
                  }
                }
                return d;
              });
            }
            self.renderItineraryDays(daysList);
          },
          error: function () {
            self.renderItineraryDays(daysList);
          },
        });
      } else {
        self.renderItineraryDays(daysList);
      }

      $("#itinerarySearchSection").addClass("v3-hidden");
      $("#btnChangeItinerary").removeClass("v3-hidden");
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

      if ($el.hasClass("select2-hidden-accessible") || $el.data("select2")) {
        try {
          $el.select2("destroy");
        } catch (e) {}
      }

      $el.select2({
        placeholder: placeholder || "-- Search Destination City / Country --",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent:
          $dropdownParent && $dropdownParent.length
            ? $dropdownParent
            : undefined,
        minimumInputLength: 0,
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

    getDefaultFollowupDateTime: function () {
      if (typeof moment !== "undefined") {
        const now = moment();
        const currentHour = now.hour();
        const dayOfWeek = now.day(); // 0 = Sunday, 6 = Saturday

        // Calculate next working day
        let nextDay = now.clone().add(1, "days");
        if (dayOfWeek === 6) {
          // Saturday -> Monday
          nextDay = now.clone().add(2, "days");
        } else if (dayOfWeek === 0) {
          // Sunday -> Monday
          nextDay = now.clone().add(1, "days");
        }

        // Determine default hour within morning 9 AM to evening 9 PM (09:00 - 21:00)
        let defaultHour = 10;
        let defaultMinute = 30;

        if (currentHour >= 9 && currentHour < 18) {
          // Active working hours: same time next working day (rounded to neat 15/30 min slot)
          defaultHour = currentHour;
          defaultMinute = now.minute() >= 30 ? 30 : 0;
        } else if (currentHour >= 18 && currentHour <= 21) {
          // Evening hours (6 PM - 9 PM) -> Next working day 11:00 AM
          defaultHour = 11;
          defaultMinute = 0;
        } else {
          // Night or early morning (before 9 AM or after 9 PM) -> 10:30 AM
          defaultHour = 10;
          defaultMinute = 30;
        }

        nextDay.hour(defaultHour).minute(defaultMinute).second(0);
        return nextDay;
      }

      // Plain Date fallback
      const d = new Date();
      d.setDate(d.getDate() + 1);
      d.setHours(10, 30, 0, 0);
      return {
        format: function (fmt) {
          const y = d.getFullYear();
          const m = String(d.getMonth() + 1).padStart(2, "0");
          const day = String(d.getDate()).padStart(2, "0");
          return `${y}-${m}-${day} 10:30 AM`;
        }
      };
    },

    initDateTimePicker: function () {
      const self = this;
      const defaultMoment = self.getDefaultFollowupDateTime();
      const defaultDateStr = typeof moment !== "undefined" && defaultMoment && defaultMoment.format ? defaultMoment.format("YYYY-MM-DD") : "";
      const defaultTime24Str = typeof moment !== "undefined" && defaultMoment && defaultMoment.format ? defaultMoment.format("HH:mm") : "10:30";
      const defaultTime12Str = typeof moment !== "undefined" && defaultMoment && defaultMoment.format ? defaultMoment.format("hh:mm A") : "10:30 AM";
      const defaultFullStr = typeof moment !== "undefined" && defaultMoment && defaultMoment.format ? defaultMoment.format("YYYY-MM-DD, hh:mm A") : "";
      const todayStr = typeof moment !== "undefined" ? moment().format("YYYY-MM-DD") : "";

      // 1. Initialize Native Date Inputs
      if (todayStr) {
        $("#modalFollowUpDate, #composerFollowupDate").attr("min", todayStr);
      }

      if (!$("#modalFollowUpDate").val()) $("#modalFollowUpDate").val(defaultDateStr);
      if (!$("#modalFollowUpTimeInput").val()) $("#modalFollowUpTimeInput").val(defaultTime24Str);
      if (!$("#modalFollowUpTime").val()) $("#modalFollowUpTime").val(defaultFullStr);

      if (!$("#composerFollowupDate").val()) $("#composerFollowupDate").val(defaultDateStr);
      if (!$("#composerFollowupTimeInput").val()) $("#composerFollowupTimeInput").val(defaultTime24Str);
      if (!$("#composerFollowupTime").val()) $("#composerFollowupTime").val(defaultFullStr);

      // Initial conflict check
      if ($("#modalSlotStatus").length && $("#modalFollowUpTime").val()) {
        self.checkSlotConflict($("#modalFollowUpTime").val(), $("#modalSlotStatus"));
      }
      if ($("#composerSlotStatus").length && $("#composerFollowupTime").val()) {
        self.checkSlotConflict($("#composerFollowupTime").val(), $("#composerSlotStatus"));
      }
    },

    checkSlotConflict: function (datetimeStr, $statusBox) {
      const self = this;
      if (!datetimeStr || !$statusBox || !$statusBox.length) return;
      const agentId = self.leadData ? (self.leadData.agent_id || 0) : 0;

      $.ajax({
        url: "/api/v3/leads/check-slot/",
        type: "GET",
        data: {
          lead_id: self.leadId,
          agent_id: agentId,
          datetime: datetimeStr
        },
        dataType: "json",
        success: function (res) {
          if (res && res.status === "conflict") {
            $statusBox.html(`
              <div class="v3-slot-conflict-alert">
                <i class="fi fi-rr-exclamation"></i>
                <div>
                  <strong>Slot Conflict:</strong> Already scheduled with <span>${res.conflict_customer}</span> (Lead #${res.conflict_lead_id}) at ${res.conflict_time}.
                </div>
              </div>
            `);
          } else if (res && res.status === "available") {
            $statusBox.html(`
              <div class="v3-slot-available-pill">
                <i class="fi fi-rr-check"></i> Time slot is available (${res.time_formatted})
              </div>
            `);
          } else {
            $statusBox.empty();
          }
        },
        error: function () {
          $statusBox.empty();
        }
      });
    },

    submitComposerNote: function (postData, $btn) {
      const self = this;
      $btn.prop("disabled", true).text("Posting...");

      $.ajax({
        url: "/api/v3/leads/add-note/",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Post Note');

          if (res && res.status === "conflict") {
            if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
              Swal.fire({
                title: "⚠️ Time Slot Conflict",
                html: `<div style="text-align:left; font-size:13.5px; line-height:1.5;">
                        <p style="margin-bottom:8px;">You already have another follow-up scheduled with <strong>${res.conflict_customer}</strong> (Lead #${res.conflict_lead_id}) at <strong>${res.conflict_time}</strong>.</p>
                        <p style="color:#64748b; font-size:12.5px;">Would you like to select a different follow-up time or schedule anyway?</p>
                       </div>`,
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#059669",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Schedule Anyway",
                cancelButtonText: "Change Time"
              }).then(function (result) {
                if (result.value || result.isConfirmed) {
                  postData.force_override = 1;
                  self.submitComposerNote(postData, $btn);
                } else {
                  $("#composerFollowupTimeSelect").focus();
                }
              });
            } else {
              if (confirm(res.message + "\n\nDo you want to schedule anyway?")) {
                postData.force_override = 1;
                self.submitComposerNote(postData, $btn);
              } else {
                $("#composerFollowupTimeSelect").focus();
              }
            }
            return;
          }

          if (res && res.status === "success") {
            $("#composerNoteInput").val("");
            self.notify(res.message || "Update logged successfully", "success");
            
            if (res.followup_time) {
              self.scheduleClientReminder(
                res.followup_time,
                self.leadData ? self.leadData.name : "Customer",
                self.leadData ? self.leadData.agent_id : 0,
                self.leadId
              );
            }
            
            self.loadLeadDetails();
          } else {
            self.notify((res && res.message) || "Failed to post update.", "error");
          }
        },
        error: function () {
          $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Post Note');
          self.notify("Network connection error.", "error");
        }
      });
    },

    submitModalFollowUp: function (postData, $btn) {
      const self = this;
      $btn.prop("disabled", true).text("Scheduling...");

      $.ajax({
        url: "/api/v3/leads/add-note/",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function (res) {
          $btn.prop("disabled", false).text("Set Reminder");

          if (res && res.status === "conflict") {
            if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
              Swal.fire({
                title: "⚠️ Time Slot Conflict",
                html: `<div style="text-align:left; font-size:13.5px; line-height:1.5;">
                        <p style="margin-bottom:8px;">You already have another follow-up scheduled with <strong>${res.conflict_customer}</strong> (Lead #${res.conflict_lead_id}) at <strong>${res.conflict_time}</strong>.</p>
                        <p style="color:#64748b; font-size:12.5px;">Would you like to select a different follow-up time or schedule anyway?</p>
                       </div>`,
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#059669",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Schedule Anyway",
                cancelButtonText: "Change Time"
              }).then(function (result) {
                if (result.value || result.isConfirmed) {
                  postData.force_override = 1;
                  self.submitModalFollowUp(postData, $btn);
                } else {
                  $("#modalFollowUpTimeSelect").focus();
                }
              });
            } else {
              if (confirm(res.message + "\n\nDo you want to schedule anyway?")) {
                postData.force_override = 1;
                self.submitModalFollowUp(postData, $btn);
              } else {
                $("#modalFollowUpTimeSelect").focus();
              }
            }
            return;
          }

          if (res && res.status === "success") {
            self.notify(res.message || "Follow-up reminder set successfully.", "success");
            $("#modalSetFollowUp").removeClass("is-active");
            self.loadLeadDetails();
          } else {
            self.notify((res && res.message) || "Failed to set follow-up reminder.", "error");
          }
        },
        error: function () {
          $btn.prop("disabled", false).text("Set Reminder");
          self.notify("Network error while setting follow-up reminder.", "error");
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
