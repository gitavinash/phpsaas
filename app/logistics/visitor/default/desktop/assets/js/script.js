"use strict";

/* =============================================
       CONFIG
    ============================================= */
/** API endpoint for order tracking */
const API_ENDPOINT = "/api/order/tracking/";

/**
 * Delivery stage definitions with keyword matchers and icons.
 * Used to determine the current stage in the progress tracker.
 */
const STAGES = [
  {
    key: "booked",
    label: "Booked",
    keywords: ["booked", "created", "registered", "placed", "pending", "order received", "received"],
    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                 <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                 <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
               </svg>`,
  },
  {
    key: "pickup",
    label: "Picked Up",
    keywords: ["picked", "pickup", "collected", "dispatched"],
    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                 <circle cx="12" cy="10" r="3"/>
               </svg>`,
  },
  {
    key: "transit",
    label: "In Transit",
    keywords: [
      "transit",
      "in transit",
      "on the way",
      "hub",
      "reached",
      "arrived",
    ],
    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/>
                 <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
               </svg>`,
  },
  {
    key: "delivery",
    label: "Out for Delivery",
    keywords: ["out for delivery", "out for", "delivery agent", "delivery"],
    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M5 12h14M12 5l7 7-7 7"/>
               </svg>`,
  },
  {
    key: "delivered",
    label: "Delivered",
    keywords: ["delivered", "complete", "received"],
    icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <polyline points="20 6 9 17 4 12"/>
               </svg>`,
  },
];

/* =============================================
       STATE
    ============================================= */
let currentOrderId = null; // Last queried order ID (for retry)
let currentData = null; // Last successful API response data

/* =============================================
       DOM REFERENCES
    ============================================= */
const $loadingOverlay = document.getElementById("loading-overlay");
const $errorAlert = document.getElementById("error-alert");
const $errorMsg = document.getElementById("error-message-text");
const $emptyState = document.getElementById("empty-state");
const $trackingResult = document.getElementById("tracking-result");
const $orderInput = document.getElementById("order-id-input");
const $footerYear = document.getElementById("footer-year");

/* =============================================
       INIT
    ============================================= */
(function init() {
  // Set footer year dynamically
  $footerYear.textContent = new Date().getFullYear();

  // Submit on Enter key
  $orderInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter") handleTrack();
  });

  // Auto-track if ?orderid= is present in URL
  const params = new URLSearchParams(window.location.search);
  const orderId = params.get("orderid");

  if (orderId && orderId.trim()) {
    $orderInput.value = orderId.trim();
    fetchTracking(orderId.trim()); // Automatic: show loading immediately
  } else {
    showEmptyState(); // Manual: show empty state until user submits
  }
})();

/* =============================================
       showLoading / hideLoading
    ============================================= */
/**
 * Display the fullscreen frosted-glass loading overlay.
 */
function showLoading() {
  $loadingOverlay.classList.add("visible");
}

/**
 * Hide the fullscreen loading overlay.
 */
function hideLoading() {
  $loadingOverlay.classList.remove("visible");
}

/* =============================================
       showError / hideError
    ============================================= */
/**
 * Show the error alert card with an optional message.
 * @param {string} [message] - Custom error message.
 */
function showError(message) {
  if (message) $errorMsg.textContent = message;
  $errorAlert.classList.add("visible");
  $emptyState.classList.remove("visible");
  $trackingResult.classList.remove("visible");
}

/**
 * Hide the error alert card.
 */
function hideError() {
  $errorAlert.classList.remove("visible");
}

/* =============================================
       showEmptyState / showTrackingResult
    ============================================= */
function showEmptyState() {
  $emptyState.classList.add("visible");
  $trackingResult.classList.remove("visible");
  hideError();
}

function showTrackingResult() {
  $trackingResult.classList.add("visible");
  $emptyState.classList.remove("visible");
  hideError();
}

/* =============================================
       handleTrack
    ============================================= */
/**
 * Triggered by button click or Enter key.
 * Validates input and initiates API fetch.
 */
function handleTrack() {
  const id = $orderInput.value.trim();
  if (!id) {
    $orderInput.focus();
    $orderInput.style.borderColor = "var(--color-danger)";
    $orderInput.style.boxShadow = "0 0 0 3px rgba(239,68,68,.12)";
    setTimeout(() => {
      $orderInput.style.borderColor = "";
      $orderInput.style.boxShadow = "";
    }, 2500);
    return;
  }
  fetchTracking(id);
}

/* =============================================
       handleRetry
    ============================================= */
/**
 * Retry button click handler — re-runs the last fetch.
 */
function handleRetry() {
  hideError();
  if (currentOrderId) {
    fetchTracking(currentOrderId);
  } else {
    handleTrack();
  }
}

/* =============================================
       fetchTracking
    ============================================= */
/**
 * Fetch tracking information from the API.
 * @param {string} orderId - The order ID to query.
 */
async function fetchTracking(orderId) {
  currentOrderId = orderId;
  showLoading();
  hideError();

  try {
    const url = `${API_ENDPOINT}?orderId=${encodeURIComponent(orderId)}`;
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`Server error (${response.status}). Please try again.`);
    }

    const json = await response.json();

    // API returns status:"success" (string) and data.order / data.logs
    if (json.status !== "success" || !json.data || !json.data.order) {
      const msg =
        json.msg || json.message || "No tracking information found for this Order ID.";
      throw new Error(msg);
    }

    currentData = json.data;
    renderTracking(json.data);
    showTrackingResult();
  } catch (err) {
    let userMsg =
      "We couldn't fetch tracking details. Please check your connection and try again.";
    if (err.message && !err.message.toLowerCase().includes("fetch")) {
      userMsg = err.message;
    }
    showError(userMsg);
  } finally {
    hideLoading();
  }
}

/* =============================================
       renderTracking
    ============================================= */
/**
 * Populate all tracking result sections with API data.
 * @param {object} data - Tracking data object from API response.
 */
function renderTracking(data) {
  const order = data.order || {};
  const logs  = data.logs  || [];

  // Derive a location string: deliveryCity + deliveryState (if present)
  const city  = order.deliveryCity  || null;
  const state = order.deliveryState || null;
  const deliveryLocation = [city, state].filter(Boolean).join(', ') || '—';

  // Latest log entry (first element — API returns newest first or only entry)
  const latestLog = logs.length ? logs[0] : null;

  // -- Summary Card --
  setText("display-tracking-id",   order.trackingId                              || "—");
  setText("display-order-id",      order.trackingId                              || "—");
  setText("display-order-status",  order.orderStatus                             || "—");
  setText("display-order-date",    order.orderDate ? formatDate(order.orderDate) : "—");
  setText("display-delivery-city", deliveryLocation);

  // -- Shipment Details Card --
  setText("display-origin",      order.pickupCity    || order.origin      || "—");
  setText("display-destination", deliveryLocation);
  setText("display-courier-2",   order.courier       || "—");
  setText("display-service-2",   order.service       || "—");
  setText("display-weight-2",    order.weight        || "—");

  // -- Receiver Details Card --
  setText("display-receiver",    order.receiverName  || order.receiver    || "—");
  setText("display-mobile",      order.mobile        || order.phone       || "—");
  setText("display-address",     deliveryLocation);
  setText("display-eta-2",       order.eta           || "—");

  // -- Latest Update Card --
  const latestStatus = latestLog ? latestLog.status   : (order.orderStatus || "—");
  const latestMsg    = latestLog ? latestLog.message  : "—";
  const latestTime   = latestLog ? formatDate(latestLog.date) : (order.orderDate ? formatDate(order.orderDate) : "—");

  setText("display-latest-status",   latestStatus);
  setText("display-latest-location", latestMsg);     // use message as the descriptive line
  setText("display-latest-time",     latestTime);

  // -- Status Badge --
  renderStatusBadge(order.orderStatus);

  // -- Latest Card Icon --
  renderLatestIcon(order.orderStatus);

  // -- Progress Tracker --
  updateProgress(order.orderStatus);

  // -- Timeline (uses logs array) --
  renderTimeline(logs);
}

/* =============================================
       renderStatusBadge
    ============================================= */
/**
 * Render a colored status badge in the summary header.
 * @param {string} status - Current shipment status string.
 */
function renderStatusBadge(status) {
  const wrap = document.getElementById("status-badge-wrap");
  const s = (status || "").toLowerCase();

  let cls = "badge-neutral";
  if      (s.includes("delivered"))               cls = "badge-success";
  else if (s.includes("out for delivery"))         cls = "badge-warning";
  else if (s.includes("delivery") && !s.includes("out")) cls = "badge-warning";
  else if (s.includes("transit"))                 cls = "badge-primary";
  else if (s.includes("picked") || s.includes("dispatched")) cls = "badge-primary";
  else if (s.includes("pending") || s.includes("booked") || s.includes("received")) cls = "badge-neutral";
  else if (s.includes("cancelled") || s.includes("failed") || s.includes("returned")) cls = "badge-danger";

  wrap.innerHTML = `
        <span class="badge ${cls}" role="status" aria-label="Current status: ${escHtml(status)}">
          <span class="badge-dot" aria-hidden="true"></span>
          ${escHtml(status || "Unknown")}
        </span>`;
}

/* =============================================
       renderLatestIcon
    ============================================= */
/**
 * Inject a contextual SVG icon into the latest update card.
 * @param {string} status - Current shipment status.
 */
function renderLatestIcon(status) {
  const wrap = document.getElementById("latest-icon-wrap");
  const s = (status || "").toLowerCase();

  let svg;
  if (s.includes("delivered")) {
    svg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
  } else if (s.includes("delivery")) {
    svg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>`;
  } else if (s.includes("transit")) {
    svg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>`;
  } else {
    svg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`;
  }

  wrap.innerHTML = svg;
}

/* =============================================
       updateProgress
    ============================================= */
/**
 * Render the 5-step horizontal progress tracker.
 * Completed steps = blue filled, current step = animated ring, future = gray.
 * @param {string} stageOrStatus - Stage or status string from API.
 */
function updateProgress(stageOrStatus) {
  const container = document.getElementById("progress-steps");
  const val = (stageOrStatus || "").toLowerCase();

  // Resolve current stage index via keyword matching (last match wins)
  let currentIdx = 0;
  for (let i = STAGES.length - 1; i >= 0; i--) {
    if (STAGES[i].keywords.some((kw) => val.includes(kw))) {
      currentIdx = i;
      break;
    }
  }

  container.innerHTML = STAGES.map((stage, idx) => {
    let cls = "";
    let aria = `${stage.label} — pending`;
    if (idx < currentIdx) {
      cls = "completed";
      aria = `${stage.label} — completed`;
    } else if (idx === currentIdx) {
      cls = "current";
      aria = `${stage.label} — current step`;
    }

    return `
          <div class="progress-step ${cls}" role="listitem" aria-label="${escHtml(aria)}">
            <div class="step-icon" aria-hidden="true">${stage.icon}</div>
            <span class="step-label">${escHtml(stage.label)}</span>
          </div>`;
  }).join("");
}

/* =============================================
       renderTimeline
    ============================================= */
/**
 * Render the vertical tracking history timeline, newest entry first.
 * @param {Array<{title:string, location:string, time:string}>} history
 */
function renderTimeline(logs) {
  const container = document.getElementById("timeline");

  if (!logs || !logs.length) {
    container.innerHTML = `<p style="color:var(--color-text-secondary);font-size:.875rem;padding:8px 0;">No history events available.</p>`;
    return;
  }

  container.innerHTML = logs
    .map((item, idx) => {
      const isFirst = idx === 0;
      const dotCls  = isFirst ? "first" : "normal";

      // Resolve fields: API uses status / message / date
      const title   = item.status   || item.title    || "—";
      const message = item.message  || item.location || "";
      const time    = item.date     || item.time     || "";

      const icon = isFirst
        ? `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`
        : `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>`;

      const lineHtml =
        idx < logs.length - 1
          ? `<div class="timeline-line" aria-hidden="true"></div>`
          : "";

      // Build message row only if message text exists
      const messageRow = message
        ? `<span class="timeline-location">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                  </svg>
                  ${escHtml(message)}
                </span>`
        : "";

      return `
          <div class="timeline-item" role="listitem">
            <div class="timeline-left" aria-hidden="true">
              <div class="timeline-dot ${dotCls}">${icon}</div>
              ${lineHtml}
            </div>
            <div class="timeline-content">
              <div class="timeline-title">${escHtml(title)}</div>
              <div class="timeline-meta">
                ${messageRow}
                <span class="timeline-time">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                  </svg>
                  ${escHtml(time ? formatDate(time) : "—")}
                </span>
              </div>
            </div>
          </div>`;
    })
    .join("");
}

/* =============================================
       copyTracking
    ============================================= */
/**
 * Copy the current tracking ID to the user's clipboard.
 * Falls back to execCommand for older browsers.
 */
async function copyTracking() {
  // currentData now holds the full data object: { order, logs }
  const trackingId = currentData && currentData.order && currentData.order.trackingId;
  if (!trackingId) return;

  try {
    await navigator.clipboard.writeText(trackingId);
  } catch {
    // Fallback for browsers without Clipboard API
    const el = document.createElement("textarea");
    el.value = trackingId;
    el.style.cssText = "position:fixed;opacity:0;pointer-events:none;";
    document.body.appendChild(el);
    el.select();
    document.execCommand("copy");
    document.body.removeChild(el);
  }

  // Visual feedback
  const btn = document.getElementById("copy-btn");
  const label = document.getElementById("copy-label");
  const icon = document.getElementById("copy-icon");

  btn.classList.add("copied");
  label.textContent = "Copied!";
  icon.innerHTML = `<polyline points="20 6 9 17 4 12"/>`;

  setTimeout(() => {
    btn.classList.remove("copied");
    label.textContent = "Copy";
    icon.innerHTML = `<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>`;
  }, 2500);
}

/* =============================================
       HELPERS
    ============================================= */
/**
 * Set textContent of an element by its ID.
 * @param {string} id - Element ID.
 * @param {string} value - Text value to set.
 */
function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

/**
 * Escape HTML special characters to prevent XSS.
 * @param {string} str - Input string.
 * @returns {string} Escaped string safe for innerHTML.
 */
function escHtml(str) {
  if (typeof str !== "string") return String(str || "");
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

/**
 * Format a MySQL datetime string ("YYYY-MM-DD HH:MM:SS") or ISO string
 * into a human-friendly format: "23 Jul 2026, 12:35 PM".
 * Falls back to the raw string if parsing fails.
 * @param {string} dateStr - Date string from the API.
 * @returns {string} Formatted date string.
 */
function formatDate(dateStr) {
  if (!dateStr) return "—";
  try {
    // MySQL datetime uses space separator; replace with T for valid ISO parse
    const normalized = dateStr.replace(" ", "T");
    const d = new Date(normalized);
    if (isNaN(d.getTime())) return dateStr; // not parseable — return raw
    return d.toLocaleString("en-IN", {
      day:    "2-digit",
      month:  "short",
      year:   "numeric",
      hour:   "2-digit",
      minute: "2-digit",
      hour12: true,
    });
  } catch {
    return dateStr;
  }
}
