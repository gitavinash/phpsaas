<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Track your shipment in real-time. Enter your Order ID to get live updates on your package location, delivery status, and estimated arrival time." />
  <meta name="robots" content="index, follow" />
  <title>Track Your Shipment — Logistics</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>

<body>

  <!-- ============================================================
       FULLSCREEN LOADING OVERLAY
  ============================================================ -->
  <div id="loading-overlay" role="status" aria-live="polite" aria-label="Loading tracking details">
    <div class="spinner" aria-hidden="true"></div>
    <p class="loading-text">Fetching tracking details...</p>
    <p class="loading-brand" aria-hidden="true">Powered by Blumox Technologies</p>
  </div>

  <!-- ============================================================
       HEADER
  ============================================================ -->
  <header class="header" role="banner">
    <div class="header-inner">
      <a href="/" class="logo" aria-label="<?php echo SITE_NAME ?>">
        <img src='<?php echo SITE_LOGO ?>' alt="<?php echo SITE_NAME ?>" title="<?php echo SITE_NAME ?>" class="logo-img" />
      </a>
    </div>
  </header>

  <!-- ============================================================
       MAIN
  ============================================================ -->
  <main class="main" role="main">

    <!-- HERO -->
    <section class="hero" aria-labelledby="hero-heading">
      <h1 class="hero-title" id="hero-heading">
        Track Your<br /><span>Shipment</span>
      </h1>
      <p class="hero-subtitle">
        Enter your Order ID to get real-time updates of your order.
      </p>
    </section>

    <div class="container">

      <!-- SEARCH CARD -->
      <section aria-label="Order tracking search">
        <div class="search-card" role="search">
          <label class="search-label" for="order-id-input">Enter Order ID</label>
          <div class="search-row">
            <div class="search-input-wrap">
              <span class="search-input-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="8" />
                  <path d="m21 21-4.35-4.35" />
                </svg>
              </span>
              <input
                type="text"
                id="order-id-input"
                class="search-input"
                placeholder="e.g. ODMTY2NDQ="
                aria-label="Order ID"
                autocomplete="off"
                autocorrect="off"
                spellcheck="false" />
            </div>
            <button class="btn-track" id="track-btn" onclick="handleTrack()" aria-label="Track shipment">
              Track Order
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </button>
          </div>
          <p class="search-hint">
            Press <kbd style="background:var(--color-border-light);border:1px solid var(--color-border);border-radius:4px;padding:1px 6px;font-size:.75rem;">Enter</kbd> or click Track Order to search
          </p>
        </div>

        <!-- ERROR ALERT -->
        <div id="error-alert" role="alert" aria-live="assertive">
          <div class="error-icon-wrap" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" />
              <path d="M15 9l-6 6M9 9l6 6" />
            </svg>
          </div>
          <div class="error-body">
            <p class="error-title">Tracking Failed</p>
            <p class="error-message" id="error-message-text">We couldn't find tracking details for this Order ID. Please check the ID and try again.</p>
            <div class="error-actions">
              <button class="btn-retry" onclick="handleRetry()" aria-label="Retry tracking">Retry</button>
              <button class="btn-dismiss" onclick="hideError()" aria-label="Dismiss error">Dismiss</button>
            </div>
          </div>
        </div>
      </section>

      <!-- EMPTY STATE -->
      <div id="empty-state" aria-label="No tracking information">
        <div class="empty-icon" aria-hidden="true">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
            <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
            <line x1="12" y1="22.08" x2="12" y2="12" />
          </svg>
        </div>
        <h2 class="empty-title">Track Your Shipment</h2>
        <p class="empty-subtitle">Enter your Order ID above to get real-time tracking information for your shipment.</p>
      </div>

      <!-- TRACKING RESULT -->
      <div id="tracking-result" aria-live="polite" aria-label="Tracking information">

        <!-- 1. SUMMARY CARD -->
        <article class="card" aria-label="Shipment summary">
          <div class="summary-header">
            <div class="tracking-id-group">
              <span class="tracking-id-label">Tracking ID</span>
              <div class="tracking-id-row">
                <span class="tracking-id-value" id="display-tracking-id">—</span>
                <button class="btn-copy" id="copy-btn" onclick="copyTracking()" aria-label="Copy tracking ID">
                  <svg id="copy-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="9" y="9" width="13" height="13" rx="2" />
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                  </svg>
                  <span id="copy-label">Copy</span>
                </button>
              </div>
            </div>
            <div id="status-badge-wrap"></div>
          </div>

          <div class="summary-meta">
            <div class="meta-item">
              <div class="meta-label">Order ID</div>
              <div class="meta-value" id="display-order-id">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-label">Status</div>
              <div class="meta-value" id="display-order-status">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-label">Order Date</div>
              <div class="meta-value" id="display-order-date">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-label">Delivery City</div>
              <div class="meta-value" id="display-delivery-city">—</div>
            </div>
          </div>
        </article>

        <!-- 2. PROGRESS TRACKER -->
        <article class="card progress-card" aria-label="Shipment progress">
          <div class="card-header">
            <div class="card-title">
              <div class="card-title-icon" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
              </div>
              Delivery Progress
            </div>
          </div>
          <div class="card-body">
            <div class="progress-steps" id="progress-steps" role="list" aria-label="Delivery stages">
              <!-- Injected by JS -->
            </div>
          </div>
        </article>

        <!-- 4. LATEST UPDATE CARD -->
        <article class="card latest-card" aria-label="Latest shipment update">
          <div class="card-header" style="padding-top:24px;">
            <div class="card-title">
              <div class="card-title-icon" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
              </div>
              Latest Update
            </div>
          </div>
          <div class="latest-body">
            <div class="latest-icon-wrap" id="latest-icon-wrap" aria-hidden="true">
              <!-- Injected by JS -->
            </div>
            <div class="latest-content">
              <div class="latest-status" id="display-latest-status">—</div>
              <div class="latest-location">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <span id="display-latest-location">—</span>
              </div>
              <div class="latest-time" id="display-latest-time">—</div>
            </div>
          </div>
        </article>

        <!-- 5. TRACKING HISTORY -->
        <article class="card" aria-label="Tracking history timeline">
          <div class="card-header">
            <div class="card-title">
              <div class="card-title-icon" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="8" y1="6" x2="21" y2="6" />
                  <line x1="8" y1="12" x2="21" y2="12" />
                  <line x1="8" y1="18" x2="21" y2="18" />
                  <line x1="3" y1="6" x2="3.01" y2="6" />
                  <line x1="3" y1="12" x2="3.01" y2="12" />
                  <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
              </div>
              Tracking History
            </div>
          </div>
          <div class="card-body">
            <div class="timeline" id="timeline" role="list" aria-label="Shipment history events">
              <!-- Injected by JS -->
            </div>
          </div>
        </article>

      </div><!-- /#tracking-result -->

    </div><!-- /.container -->
  </main>

  <!-- ============================================================
       FOOTER
  ============================================================ -->
  <footer class="footer" role="contentinfo">
    <div class="footer-inner">
      <p class="footer-copy">
        Developed by <a href="#" rel="noopener noreferrer">Blumox Technologies</a> &mdash; All rights reserved &copy; <span id="footer-year"></span>
      </p>
    </div>
  </footer>

  <script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>