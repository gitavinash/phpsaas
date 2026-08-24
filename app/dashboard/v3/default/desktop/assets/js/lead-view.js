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

    notify: function (title, type, text) {
      type = type || "info";
      if (typeof popUpMessage === "function" && !text) {
        popUpMessage("center", title, type);
      } else if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
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

    getLeadId: function () {
      if (this.leadId) return this.leadId;
      if (this.leadData && this.leadData.id) {
        this.leadId = String(this.leadData.id);
        return this.leadId;
      }
      const urlParams = new URLSearchParams(window.location.search);
      let id = urlParams.get("id") || urlParams.get("leadId") || "";
      if (!id) {
        const $app = $("#leadViewApp");
        id = $app.data("lead-id") || $app.attr("data-lead-id") || "";
      }
      if (!id) {
        const match = window.location.pathname.match(/\/leads\/view\/([^\/]+)/);
        if (match && match[1]) {
          id = match[1];
        }
      }
      this.leadId = id ? String(id) : "";
      return this.leadId;
    },

    init: function () {
      const $app = $("#leadViewApp");
      if (!$app.length) return;

      this.leadId = this.getLeadId();
      if (!this.leadId) {
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

      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }

      this.loadLeadDetails();
    },

    loadLeadDetails: function () {
      const self = this;
      if (self.isLoading) return;
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

      $.ajax({
        url: "/api/v3/leads/view.php",
        type: "GET",
        data: { id: self.leadId },
        dataType: "json",
        success: function (res) {
          self.isLoading = false;
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
        error: function () {
          self.isLoading = false;
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

      document.title = (lead.name ? lead.name + " - Lead Details" : "Lead Details") + " | V3 Dashboard";

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
          let extraContent = t.description || "";
          if (t.type === "itinerary" && t.description) {
            extraContent = `
              <div>${t.description}</div>
              <div class="v3-timeline-itinerary-action">
                <a href="/download-itinerary/${btoa(t.description)}/" target="_blank" class="v3-btn-contact-action call v3-timeline-itinerary-btn">
                  <i class="fi fi-rr-file-pdf"></i> View & Download Itinerary PDF
                </a>
              </div>
            `;
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
              ${extraContent ? `<div class="v3-timeline-card-content">${extraContent}</div>` : ""}
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
      let paymentsHtml = "";
      if (lead.payments && lead.payments.length > 0) {
        paymentsHtml = `
          <table class="v3-payments-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Recorded By</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
        `;
        lead.payments.forEach((p) => {
          paymentsHtml += `
            <tr>
              <td>${p.date}</td>
              <td><strong>₹ ${parseFloat(p.amount).toLocaleString("en-IN")}</strong></td>
              <td><span class="v3-lead-badge badge-fresh">${p.method}</span></td>
              <td>${p.reference || "-"}</td>
              <td>${p.recorded_by}</td>
              <td>${p.notes || "-"}</td>
            </tr>
          `;
        });
        paymentsHtml += `</tbody></table>`;
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

      const st = parseInt(lead.status || 0);

      // 4. Complete Unified CRM Assembly
      const html = `
        <div class="v3-lead-view-wrapper">
          <!-- 1. Minimal Hero Header -->
          <div class="v3-lead-hero-banner">
            <div class="v3-lead-hero-left">
              <a href="/dashboard/leads/list/" class="v3-lead-back-circle" data-tooltip="Back to Leads Pipeline">
                <i class="fi fi-rr-arrow-left"></i>
              </a>
              <div class="v3-lead-hero-title-wrap">
                <div class="v3-lead-hero-name-row">
                  <h2 class="v3-lead-hero-name">${lead.name}</h2>
                  
                  <!-- Minimal Clickable Status Picker -->
                  <div class="v3-minimal-status-picker">
                    <button type="button" class="v3-minimal-status-trigger ${lead.status_class}" id="btnMinimalStatusTrigger" title="Click to change status">
                      <span class="lead-dot"></span>
                      <span>${lead.status_label}</span>
                      <i class="fi fi-rr-angle-small-down"></i>
                    </button>
                    <div class="v3-status-dropdown-menu" id="statusDropdownMenu">
                      <div class="v3-status-opt ${st === 0 ? "selected" : ""}" data-status="0">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot fresh"></span> Fresh Lead
                        </div>
                        ${st === 0 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 3 ? "selected" : ""}" data-status="3">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot followup"></span> In Follow-Up
                        </div>
                        ${st === 3 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 1 ? "selected" : ""}" data-status="1">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot confirmed"></span> Confirmed Sale
                        </div>
                        ${st === 1 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                      <div class="v3-status-opt ${st === 4 || st === 8 ? "selected" : ""}" data-status="4">
                        <div class="v3-status-opt-left">
                          <span class="v3-status-opt-dot closed"></span> Closed / Cancelled
                        </div>
                        ${st === 4 || st === 8 ? '<i class="fi fi-rr-check"></i>' : ""}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="v3-lead-hero-meta">
                  <span><i class="fi fi-rr-hashtag"></i> Lead #${lead.id}</span>
                  <span>•</span>
                  <span><i class="fi fi-rr-calendar"></i> Added ${lead.created_at || "Recently"}</span>
                  <span>•</span>
                  <span><i class="fi fi-rr-globe"></i> Source: <strong>${lead.source}</strong></span>
                  ${lead.campaign ? `<span>•</span><span><i class="fi fi-rr-bullhorn"></i> ${lead.campaign}</span>` : ""}
                </div>
              </div>
            </div>

            <div class="v3-lead-hero-actions">
              ${lead.wa_mobile ? `
                <a href="https://wa.me/${lead.wa_mobile}?text=Hello%20${encodeURIComponent(lead.first_name || lead.name)}" target="_blank" class="v3-btn-contact-action wa" data-tooltip="Chat on WhatsApp">
                  ${WA_SVG} WhatsApp
                </a>
              ` : ""}
              ${lead.mobile ? `
                <a href="tel:${lead.mobile}" class="v3-btn-contact-action call" data-tooltip="Call Primary Phone">
                  <i class="fi fi-rr-phone-call"></i> ${lead.mobile}
                </a>
              ` : ""}
              <button type="button" class="btn-v3-action btn-outline" id="btnHeroEditLead" data-tooltip="Edit Lead & Requirements">
                <i class="fi fi-rr-edit"></i> Edit Lead
              </button>
              <button type="button" class="btn-v3-action btn-outline" id="btnHeroSendItinerary" data-tooltip="Search, Customize & Send Itinerary">
                <i class="fi fi-rr-paper-plane"></i> Send Itinerary
              </button>
              <button type="button" class="btn-v3-action btn-orange" id="btnHeroSetFollowUp" data-tooltip="Set Follow-Up Reminder">
                <i class="fi fi-rr-alarm-clock"></i> Follow-Up
              </button>
              ${st !== 1 ? `
                <button type="button" class="btn-v3-action btn-green" id="btnHeroConvertToSale" data-tooltip="Convert Lead to Confirmed Sale">
                  <i class="fi fi-rr-check"></i> Convert to Sale
                </button>
              ` : ""}
            </div>
          </div>

          <!-- 2. Top 4-Card Snapshot Grid -->
          <div class="v3-lead-snapshot-grid">
            <!-- Card 1: Package / Subject -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Package / Requirement</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-plane"></i></div>
              </div>
              <div class="v3-snapshot-value">${lead.subject || "Custom Package"}</div>
              <div class="v3-snapshot-sub"><i class="fi fi-rr-calendar"></i> Travel: ${travel.date_of_travel || "Flexible"}</div>
            </div>

            <!-- Card 2: Pax & Duration -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Duration & Guests</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-users"></i></div>
              </div>
              <div class="v3-snapshot-value">${travel.duration_days > 0 ? `${travel.duration_days} Nights` : "Duration N/A"}</div>
              <div class="v3-snapshot-sub">${travel.adults || 1} Adults • ${travel.children || 0} Kids • ${travel.infants || 0} Infants</div>
            </div>

            <!-- Card 3: Assigned Representative -->
            <div class="v3-snapshot-card">
              <div class="v3-snapshot-header">
                <span class="v3-snapshot-label">Assigned Agent</span>
                <div class="v3-snapshot-icon"><i class="fi fi-rr-user"></i></div>
              </div>
              <div class="v3-snapshot-value">${lead.agent_name || "Unassigned"}</div>
              <div class="v3-snapshot-sub">${lead.agent_role || "Representative"}</div>
            </div>

            <!-- Card 4: Quotation & Value -->
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
                <button type="button" class="v3-tab-link" data-tab="tab-payments">
                  <i class="fi fi-rr-money-check"></i> Payments (${lead.payments ? lead.payments.length : 0})
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

                  ${travel.requirements ? `
                    <h4 class="v3-info-section-heading">Special Notes & Requirements</h4>
                    <div class="v3-requirements-box">${travel.requirements}</div>
                  ` : ""}
                </div>

                <!-- Tab 2: Activity & Notes (HubSpot/Linear Style) -->
                <div class="v3-lead-tab-pane" id="tab-activity">
                  <!-- Quick Composer Box -->
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
                          <option value="General Follow-up">General Follow-up</option>
                        </select>
                      </div>
                    </div>

                    <div class="v3-composer-footer">
                      <button type="button" class="btn-v3-action btn-green" id="btnSubmitComposer">
                        <i class="fi fi-rr-check"></i> Post Update
                      </button>
                    </div>
                  </div>

                  <!-- Timeline Stream -->
                  ${timelineHtml}
                </div>

                <!-- Tab 3: Payment History -->
                <div class="v3-lead-tab-pane" id="tab-payments">
                  <div class="v3-flex-end-row">
                    <button type="button" class="btn-v3-action btn-green" id="btnOpenPaymentModalTab" data-tooltip="Record Payment">
                      <i class="fi fi-rr-plus"></i> Record Payment
                    </button>
                  </div>
                  ${paymentsHtml}
                </div>
              </div>
            </div>

            <!-- Right Area: Sidebar Panels -->
            <div class="v3-sidebar-panel">
              <!-- Assigned Agent Box -->
              <div class="v3-sidebar-card">
                <div class="v3-sidebar-card-title">
                  <span>Assigned Representative</span>
                  <i class="fi fi-rr-user-gear"></i>
                </div>
                <select id="leadDetailAgentSelect" class="leads-filter-select v3-select-full">
                  ${agentOptions}
                </select>
              </div>

              <!-- Fast Action Buttons -->
              <div class="v3-sidebar-card">
                <div class="v3-sidebar-card-title">
                  <span>Fast Actions</span>
                  <i class="fi fi-rr-bolt"></i>
                </div>
                <div class="v3-sidebar-actions-list">
                  <button type="button" class="btn-v3-side-action" id="btnSideEditLead" data-tooltip="Edit Lead Details">
                    <i class="fi fi-rr-edit"></i> Edit Lead Details
                  </button>
                  <button type="button" class="btn-v3-side-action" id="btnSideSendItinerary" data-tooltip="Search & Send Tour Itinerary">
                    <i class="fi fi-rr-paper-plane"></i> Send Itinerary
                  </button>
                  <button type="button" class="btn-v3-side-action" id="btnSideSendPaymentLink" data-tooltip="Generate Payment Link">
                    <i class="fi fi-rr-link"></i> Send Payment Link
                  </button>
                  <button type="button" class="btn-v3-side-action" id="btnSideRecordPayment" data-tooltip="Record Payment Received">
                    <i class="fi fi-rr-money-bill-wave"></i> Record Payment
                  </button>
                  <button type="button" class="btn-v3-side-action" id="btnSideCloseLeadModal" data-tooltip="Close Lead with Reason">
                    <i class="fi fi-rr-cross-circle"></i> Close / Archive Lead
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;

      $container.html(html);

      // Initialize Select2 on agent dropdown
      if (typeof $.fn.select2 !== "undefined") {
        $("#leadDetailAgentSelect, #composerFollowupReason").select2({
          dropdownAutoWidth: true,
          width: "100%",
        });
      }
    },

    // Render structured day cards inside the full screen itinerary customizer
    renderItineraryDays: function (days) {
      const self = this;
      const $stack = $("#itinDaysStack");
      $stack.empty();

      if (!days || days.length === 0) {
        days = [
          { day: "Day 1 - Arrival & Sightseeing", overview: "Arrive at destination, transfer to hotel, and explore local sights." },
          { day: "Day 2 - Full Day Tour & Activities", overview: "Enjoy full day guided tour and iconic attractions." },
          { day: "Day 3 - Departure", overview: "Check out from hotel and transfer to airport/station for departure." }
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
      $(document).on("click", ".v3-tab-link", function () {
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

        const currentStatus = self.leadData ? parseInt(self.leadData.status) : 0;
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
          $("#composerNoteInput").attr("placeholder", "Add instructions for this follow-up reminder...");
          $("#btnSubmitComposer").html('<i class="fi fi-rr-alarm-clock"></i> Set Reminder');
        } else {
          $("#composerFollowupRow").removeClass("is-visible");
          $("#composerNoteInput").attr("placeholder", "Type a note, call log, or customer update...");
          $("#btnSubmitComposer").html('<i class="fi fi-rr-check"></i> Post Note');
        }
      });

      // 4. Submit Composer Update
      $(document).on("click", "#btnSubmitComposer", function () {
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
          title: type === "followup" ? $("#composerFollowupReason").val() : "Note",
          msg: msg || "Follow-up reminder set",
        };

        if (type === "followup") {
          postData.followupTime = $("#composerFollowupTime").val();
        }

        $.ajax({
          url: "/api/v3/leads/add-note.php",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Post Update');
            if (res.status === "success") {
              $("#composerNoteInput").val("");
              self.notify("Update logged successfully", "success");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to post update.", "error");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Post Update');
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
              $("#leadDetailAgentSelect").val(currentAgentId).trigger("change.select2");
            }
          });
        } else if (confirm(`Assign this lead to ${newAgentName}?`)) {
          self.performAgentAssign(newAgentId);
        } else {
          $("#leadDetailAgentSelect").val(currentAgentId).trigger("change.select2");
        }
      });

      // 6. Convert to Sale
      $(document).on("click", "#btnHeroConvertToSale", function () {
        self.confirmConvertToSale();
      });

      // 7. Edit Lead Modal Trigger & Submission
      $(document).on("click", "#btnHeroEditLead, #btnTabEditLead, #btnSideEditLead", function () {
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
        $("#editLeadCurrentLocation").val(t.current_location || "");

        $("#editLeadSubject").val(d.subject || "");
        $("#editLeadTravelDate").val(t.date_of_travel || "");
        $("#editLeadDuration").val(t.duration_days || 0);
        $("#editLeadPickup").val(t.pickup_location || "");
        $("#editLeadDrop").val(t.drop_location || "");

        $("#editLeadAdults").val(t.adults || 1);
        $("#editLeadChildren").val(t.children || 0);
        $("#editLeadInfants").val(t.infants || 0);

        $("#editLeadRequirements").val(t.requirements || "");

        $("#modalEditLead").addClass("is-active");
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
        const currentLeadId = $("#editLeadId").val() || self.leadId || self.getLeadId() || (self.leadData ? (self.leadData.id || self.leadData.encoded_id) : "");
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
          current_location: $("#editLeadCurrentLocation").val().trim(),
          subject: $("#editLeadSubject").val().trim(),
          package: $("#editLeadSubject").val().trim(),
          date_of_travel: $("#editLeadTravelDate").val(),
          duration_days: $("#editLeadDuration").val(),
          pickup_location: $("#editLeadPickup").val().trim(),
          drop_location: $("#editLeadDrop").val().trim(),
          adults: $("#editLeadAdults").val(),
          children: $("#editLeadChildren").val(),
          infants: $("#editLeadInfants").val(),
          requirements: $("#editLeadRequirements").val().trim(),
        };

        $.ajax({
          url: "/api/v3/leads/update.php",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Save Changes');
            if (res.status === "success") {
              $("#modalEditLead").removeClass("is-active");
              self.notify("Lead updated successfully", "success");
              self.loadLeadDetails();
            } else {
              self.notify(res.message || "Failed to update lead.", "error");
            }
          },
          error: function (xhr) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Save Changes');
            const err = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Network error while updating lead.";
            self.notify(err, "error");
          },
        });
      });

      // 8. Full Screen Itinerary Modal Trigger & Logic
      $(document).on("click", "#btnHeroSendItinerary, #btnSideSendItinerary", function () {
        const d = self.leadData || {};
        const dest = (d.subject || "").trim();

        $("#modalSendItinerary").addClass("is-active");

        // Reset state
        $("#itinerarySearchLoader").addClass("v3-hidden");
        $("#itineraryNotFoundBox").addClass("v3-hidden");
        $("#itineraryResultContainer").addClass("v3-hidden");
        $("#itineraryCustomSection").addClass("v3-hidden");
        $("#btnPreviewItinerary").addClass("v3-hidden");
        $("#itineraryPackageSelect").html('<option value="">-- Choose an Itinerary Package --</option>');
        self.selectedPackage = null;

        if (self.leadData) {
          $("#itinModalLeadBadge").text(`Lead #${self.leadData.id}: ${self.leadData.name}`);
          if (dest) {
            const $destInput = $("#itineraryDestinationInput");
            if (!$destInput.find(`option[value="${dest}"]`).length) {
              $destInput.append(new Option(dest, dest, true, true));
            }
            $destInput.val(dest).trigger("change");
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

        // Initialize V3 Cities API for destination
        self.initCitySelect2($("#itineraryDestinationInput"), $("#modalSendItinerary"), "-- Search or Select Destination City / Country --");
      });

      // Find Itineraries in Modal
      $(document).on("click", "#btnFindItineraries", function () {
        const dest = $("#itineraryDestinationInput").val();
        const theme = $("#itineraryTravelStyle").val() || "";

        if (!dest) {
          self.notify("Please select or type a destination.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner fi-spin"></i> Searching...');

        $("#itinerarySearchLoaderText").text(`Searching matching tour packages for "${dest}"...`);
        $("#itinerarySearchLoader").removeClass("v3-hidden");
        $("#itineraryNotFoundBox").addClass("v3-hidden");
        $("#itineraryResultContainer").addClass("v3-hidden");
        $("#itineraryCustomSection").addClass("v3-hidden");
        $("#btnPreviewItinerary").addClass("v3-hidden");

        $.ajax({
          url: "/api/v3/itinerary/list.php",
          type: "GET",
          data: {
            destination: dest,
            theme: theme,
          },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");

            if (res && res.status === "success" && res.itineraryList && res.itineraryList.length > 0) {
              self.availablePackages = res.itineraryList;
              $("#itineraryNotFoundBox").addClass("v3-hidden");
              $("#itineraryPackageCountBadge").text(`${res.itineraryList.length} Packages Found`);

              let options = `<option value="">-- Choose an Itinerary Package (${res.itineraryList.length} Available) --</option>`;
              res.itineraryList.forEach(function (pkg, idx) {
                const adultRate = parseFloat(pkg.tour_package_price_adult || 0).toLocaleString("en-IN");
                let duration = pkg.tour_package_duration ? String(pkg.tour_package_duration).trim() : "Standard";
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
                    return $(`<span><strong>${title}</strong> ${duration ? `(${duration})` : ""} • ₹${price}/person</span>`);
                  }
                });
              }
            } else {
              // Show Inline No Package Found Box
              self.availablePackages = [];
              $("#itineraryResultContainer").addClass("v3-hidden");
              $("#itineraryCustomSection").addClass("v3-hidden");
              $("#btnPreviewItinerary").addClass("v3-hidden");
              $("#itineraryNotFoundDesc").text(`No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`);
              $("#itineraryNotFoundBox").removeClass("v3-hidden");
            }
          },
          error: function () {
            $btn.prop("disabled", false).html('<i class="fi fi-rr-search"></i> Search Packages');
            $("#itinerarySearchLoader").addClass("v3-hidden");
            self.availablePackages = [];
            $("#itineraryResultContainer").addClass("v3-hidden");
            $("#itineraryCustomSection").addClass("v3-hidden");
            $("#btnPreviewItinerary").addClass("v3-hidden");
            $("#itineraryNotFoundDesc").text(`No predefined tour packages found for "${dest}". You can build a customized itinerary from scratch.`);
            $("#itineraryNotFoundBox").removeClass("v3-hidden");
          },
        });
      });

      // Quick CTA from No-Package-Found box to start custom plan
      $(document).on("click", "#btnStartCustomItinFromEmpty", function () {
        const dest = $("#itineraryDestinationInput").val() || (self.leadData ? self.leadData.subject : "Custom Tour");
        self.selectedPackage = { tour_package_id: 0, tour_package_title: dest + " Custom Tour Package" };
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
          const dest = $("#itineraryDestinationInput").val() || (self.leadData ? self.leadData.subject : "Custom Destination");
          self.selectedPackage = { tour_package_id: 0, tour_package_title: dest + " Tour Package" };
          self.populateCustomizerFields(self.selectedPackage);
        } else if (self.availablePackages && self.availablePackages[val]) {
          const pkg = self.availablePackages[val];
          self.selectedPackage = pkg;
          self.populateCustomizerFields(pkg);
        }
      });

      // Add Day (Top & Bottom CTA)
      $(document).on("click", "#btnAddItinDayTop, #btnAddItinDayBottom", function () {
        const nextNum = $("#itinDaysStack .v3-itin-day-card").length + 1;
        self.appendDayCard({
          day: `Day ${nextNum} - Sightseeing & Exploration`,
          overview: "",
          hotel: "",
          meal_plan: "CP",
          vehicle: "Private Sedan",
          transfer_notes: ""
        }, nextNum);
      });

      // Delete Day
      $(document).on("click", ".btn-delete-itin-day", function () {
        if ($("#itinDaysStack .v3-itin-day-card").length <= 1) {
          self.notify("At least 1 day is required in the itinerary.", "warning");
          return;
        }
        $(this).closest(".v3-itin-day-card").remove();
        self.reindexDays();
      });

      // Recalculate total price on per-person rate change
      $(document).on("input", "#itineraryCustomAdultPrice, #itineraryCustomChildPrice, #itineraryCustomInfantPrice", function () {
        const adults = self.leadData && self.leadData.travel_details ? parseInt(self.leadData.travel_details.adults || 1) : 1;
        const children = self.leadData && self.leadData.travel_details ? parseInt(self.leadData.travel_details.children || 0) : 0;
        const infants = self.leadData && self.leadData.travel_details ? parseInt(self.leadData.travel_details.infants || 0) : 0;

        const ap = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
        const cp = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
        const ip = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

        const total = (ap * adults) + (cp * children) + (ip * infants);
        $("#itineraryCustomTotalPrice").val(total);
      });

      // Helper: Publish/Download Itinerary
      self.publishItinerary = function (isDownload, $btn, onSuccess) {
        const title = $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
        const totalPrice = parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
        const adultPrice = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
        const childPrice = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
        const infantPrice = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;

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
          const transferNotes = $(this).find(".itin-day-transfer-notes").val().trim();

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
          $btn.prop("disabled", true).html(isDownload ? '<i class="fi fi-rr-spinner fi-spin"></i> Generating PDF...' : '<i class="fi fi-rr-spinner fi-spin"></i> Sending...');
        }

        const pkgId = (self.selectedPackage && self.selectedPackage.tour_package_id) ? self.selectedPackage.tour_package_id : 1;

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
          url: "/api/v3/itinerary/publish.php",
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

      // Preview Itinerary CTA in Builder Modal (Client-side Real Preview matching download-itinerary-mit.php)
      $(document).on("click", "#btnPreviewItinerary, #btnPreviewItineraryBottom", function (e) {
        e.preventDefault();
        const title = $("#itineraryCustomTitle").val().trim() || "Custom Tour Itinerary";
        const totalPrice = parseFloat($("#itineraryCustomTotalPrice").val()) || 0;
        const adultPrice = parseFloat($("#itineraryCustomAdultPrice").val()) || 0;
        const childPrice = parseFloat($("#itineraryCustomChildPrice").val()) || 0;
        const infantPrice = parseFloat($("#itineraryCustomInfantPrice").val()) || 0;
        const overview = $("#itineraryCustomOverview").val().trim();
        const inclusions = $("#itineraryCustomInclusions").val().trim();
        const exclusions = $("#itineraryCustomExclusions").val().trim();
        const dest = $("#itineraryDestinationInput").val() || (self.leadData ? self.leadData.subject : "Custom Destination");
        const clientName = self.leadData ? self.leadData.name : "Valued Customer";
        const travelDate = (self.leadData && self.leadData.travel_details && self.leadData.travel_details.date_of_travel) ? self.leadData.travel_details.date_of_travel : "Flexible";
        const adults = (self.leadData && self.leadData.travel_details) ? parseInt(self.leadData.travel_details.adults || 1) : 1;
        const children = (self.leadData && self.leadData.travel_details) ? parseInt(self.leadData.travel_details.children || 0) : 0;
        const infants = (self.leadData && self.leadData.travel_details) ? parseInt(self.leadData.travel_details.infants || 0) : 0;

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
          const transferNotes = $(this).find(".itin-day-transfer-notes").val().trim();

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
          self.notify("Please add at least 1 day to preview itinerary.", "warning");
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
          const dayTitleClean = titleParts.length > 1 ? titleParts.slice(1).join(" - ") : titleParts[0];

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
        (incLines.length ? incLines : ["Hotel accommodation with breakfast", "Airport and sightseeing transfers", "All toll taxes and parking charges"]).forEach(function (inc) {
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
        (excLines.length ? excLines : ["Airfare / Train tickets", "Personal expenses & tips", "Entry fees and camera tickets"]).forEach(function (exc) {
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
        const validDate = new Date(Date.now() + 7 * 86400000).toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });

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
            const wave1 = Math.sin(progress * Math.PI * peakFreq1) * amplitude1;
            const wave2 = Math.cos(progress * Math.PI * peakFreq2 + Math.random() * 0.3) * amplitude2;
            const wave3 = Math.sin(progress * Math.PI * peakFreq3 + Math.random() * 0.5) * amplitude3;
            const dramaticPeak = Math.random() > 0.75 ? Math.random() * 15 : 0;
            const noise = (Math.random() - 0.5) * 5;
            const baseHeight = 20 + Math.random() * 8;
            const y = Math.max(3, Math.min(55, baseHeight + wave1 + wave2 + wave3 + dramaticPeak + noise));
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
          const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
          svg.setAttribute("viewBox", "0 0 100 100");
          svg.setAttribute("preserveAspectRatio", "none");
          svg.style.width = "100%";
          svg.style.height = "100%";
          svg.style.display = "block";

          const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
          path.setAttribute("d", wavePath);
          path.setAttribute("fill", "#ffffff");
          path.setAttribute("stroke", "none");

          svg.appendChild(path);
          waveContainer.appendChild(svg);
          this.appendChild(waveContainer);
        });

        $("#modalItineraryLivePreview").addClass("is-active");
      });

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
          self.notify("Itinerary published and shared with customer!", "success");
          self.loadLeadDetails();
        });
      });

      $(document).on("click", ".v3-preview-close-trigger", function () {
        $("#modalItineraryLivePreview").removeClass("is-active");
      });

      // 9. Other Modals
      $(document).on("click", "#btnHeroSetFollowUp", function () {
        $(".v3-tab-link[data-tab='tab-activity']").trigger("click");
        $(".v3-composer-type-btn[data-type='followup']").trigger("click");
        $("#composerNoteInput").focus();
      });

      $(document).on("click", "#btnSideSendPaymentLink", function () {
        if (self.leadData && self.leadData.quotation) {
          $("#modalPaymentAmountInput").val(self.leadData.quotation.price || "");
        }
        $("#modalSendPaymentLink").addClass("is-active");
      });

      $(document).on("click", "#btnOpenPaymentModal, #btnOpenPaymentModalTab, #btnSideRecordPayment", function () {
        $("#modalUpdatePayment").addClass("is-active");
      });

      $(document).on("click", "#btnSideCloseLeadModal", function () {
        $("#modalCloseLead").addClass("is-active");
      });

      $(document).on("click", ".v3-modal-close-trigger", function () {
        $(".v3-modal-overlay").removeClass("is-active");
      });

      // Close any active modal when user presses Escape key
      $(document).on("keydown", function (e) {
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

        if (!amount || parseFloat(amount) <= 0) {
          self.notify("Please enter a valid payment amount.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");

        $.ajax({
          url: "/api/v3/leads/payment-record.php",
          type: "POST",
          data: {
            leadId: self.leadId,
            amount: amount,
            payment_method: method,
            payment_date: date,
            reference_number: ref,
            payment_notes: notes,
          },
          dataType: "json",
          success: function (res) {
            $btn.prop("disabled", false).text("Record Payment");
            $(".v3-modal-overlay").removeClass("is-active");
            if (res.status === "success") {
              self.notify("Payment recorded successfully", "success");
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
          self.notify("Please select a reason for closing this lead.", "warning");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true).text("Closing...");

        $.ajax({
          url: "/api/v3/leads/add-note.php",
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
      const adults = self.leadData && self.leadData.travel_details ? parseInt(self.leadData.travel_details.adults || 1) : 1;
      const children = self.leadData && self.leadData.travel_details ? parseInt(self.leadData.travel_details.children || 0) : 0;
      const totalPrice = (adultPrice * adults) + (childPrice * children);

      // Parse structured day-wise itinerary
      let daysList = [];
      try {
        if (pkg.tour_package_itinerary) {
          const rawItin = typeof pkg.tour_package_itinerary === "string" ? JSON.parse(pkg.tour_package_itinerary) : pkg.tour_package_itinerary;
          if (Array.isArray(rawItin)) {
            daysList = rawItin;
          }
        }
      } catch (e) {
        if (typeof pkg.tour_package_itinerary === "string" && pkg.tour_package_itinerary.trim()) {
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
      $("#itineraryCustomTotalPrice").val(totalPrice > 0 ? totalPrice : adultPrice);
      $("#itineraryCustomOverview").val(stripHtml(pkg.tour_package_overview));
      $("#itineraryCustomInclusions").val(stripHtml(pkg.tour_package_inclusions) || "Hotel accommodation with breakfast\nAirport & sightseeing transfers\nAll toll taxes and parking charges");
      $("#itineraryCustomExclusions").val(stripHtml(pkg.tour_package_exclusions) || "Airfare / Train tickets\nPersonal expenses & tips\nEntry fees and camera tickets");

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
        url: "/api/v3/leads/assign.php",
        type: "POST",
        data: {
          leadId: self.leadId,
          agentId: agentId,
        },
        dataType: "json",
        success: function (res) {
          if (res.status === 200 || res.status === "success") {
            self.notify("Agent reassigned successfully", "success");
            self.loadLeadDetails();
          } else {
            self.notify(res.msg || res.message || "Failed to reassign agent.", "error");
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

    initCitySelect2: function ($el, $dropdownParent, placeholder) {
      if (!$el || !$el.length || typeof $.fn.select2 === "undefined") return;

      $el.select2({
        placeholder: placeholder || "-- Search Destination City / Country --",
        allowClear: true,
        width: "100%",
        tags: true,
        dropdownParent: $dropdownParent && $dropdownParent.length ? $dropdownParent : undefined,
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
  };

  // Run on page ready and SPA transitions
  $(document).ready(function () {
    window.V3LeadView.init();
  });

  $(document).on("spa:loaded", function () {
    window.V3LeadView.init();
  });
});
