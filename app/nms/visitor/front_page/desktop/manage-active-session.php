<?php

require_once 'header.php';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function timeAgo($datetime)
{
  if (empty($datetime)) {
    return 'Unknown';
  }

  $timestamp = strtotime($datetime);

  if (!$timestamp) {
    return 'Unknown';
  }

  $diff = time() - $timestamp;

  if ($diff < 60) {
    return 'Just now';
  }

  if ($diff < 3600) {
    return floor($diff / 60) . ' min ago';
  }

  if ($diff < 86400) {
    return floor($diff / 3600) . ' hrs ago';
  }

  if ($diff < 2592000) {
    return floor($diff / 86400) . ' days ago';
  }

  return date('d M Y h:i A', $timestamp);
}

function deviceIcon($type)
{
  $type = strtolower((string)$type);

  switch ($type) {
    case 'mobile':
    case 'android':
    case 'iphone':
      return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>';

    case 'tablet':
    case 'ipad':
      return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>';

    case 'desktop':
    case 'windows':
    case 'mac':
      return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>';

    default:
      return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>';
  }
}

/*
|--------------------------------------------------------------------------
| Devices Query
|--------------------------------------------------------------------------
*/

$query = "
SELECT
    us.us_id,
    us.us_active_time,
    d.device_token,
    d.device_type,
    d.device_os,
    d.device_browser,
    d.device_country,
    d.device_state,
    d.device_city
FROM " . TABLE_PREFIX . "user_sessions us
LEFT JOIN " . TABLE_PREFIX . "device d
ON us.us_active_device = d.device_token
WHERE
    us.us_user_id = '" . USER_ID . "'
    AND us.us_site_id = '" . SITE_ID . "'
    AND us.us_status = '1'
ORDER BY us.us_active_time DESC
";

$devices = mysqli_query($connection, $query);

$totalDevices = ($devices)
  ? mysqli_num_rows($devices)
  : 0;

$allowedDevices = 3;
$excessDevices = max(0, ($totalDevices - $allowedDevices));

?>

<style>
.session-limit-wrapper {
  padding: 44px 0 80px 0;
  background-color: var(--bg-color, #fdfbf7);
  min-height: calc(100vh - 350px);
  font-family: var(--font-body, "Inter", -apple-system, BlinkMacSystemFont, sans-serif);
}

.session-limit-container {
  max-width: 940px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
}

.session-hero-action-card {
  background: #ffffff;
  border: 2px solid #fecaca;
  border-radius: 16px;
  box-shadow: 0 12px 35px rgba(220, 38, 38, 0.08);
  position: relative;
  overflow: hidden;
  margin-bottom: 28px;
}

.session-hero-action-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: var(--accent-color, #d93025);
}

.session-action-inner {
  padding: 36px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  text-align: left;
}

.session-hero-content {
  flex: 1;
  min-width: 0;
}

.session-alert-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.76rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  background: #fee2e2;
  color: #dc2626;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 14px;
  border: 1px solid #fca5a5;
}

.session-hero-title {
  font-family: var(--font-heading, "Playfair Display", Georgia, serif);
  font-size: 1.95rem;
  font-weight: 700;
  color: var(--primary-color, #0a0a0a);
  margin: 0 0 10px 0;
  line-height: 1.25;
}

.session-hero-desc {
  font-size: 0.95rem;
  color: var(--text-secondary, #555555);
  margin: 0;
  line-height: 1.6;
}

.session-cta-box {
  background: #fdf2f2;
  border: 1px solid #fecaca;
  border-radius: 12px;
  padding: 22px;
  width: 320px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  box-sizing: border-box;
}

.btn-logout-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #dc2626;
  color: #ffffff !important;
  font-weight: 700;
  font-size: 0.98rem;
  padding: 14px 20px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 6px 18px rgba(220, 38, 38, 0.28);
  width: 100%;
  box-sizing: border-box;
  text-align: center;
}

.btn-logout-primary:hover {
  background: #b91c1c;
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(220, 38, 38, 0.38);
}

.session-cta-note {
  font-size: 0.8rem;
  color: #7f1d1d;
  font-weight: 500;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 5px;
  text-align: center;
  line-height: 1.35;
}

.session-metrics-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.metric-card {
  background: #ffffff;
  border: 1px solid var(--border-color, #e0e0e0);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-2px);
  border-color: #cbd5e1;
}

.metric-card.alert {
  border-color: #fecaca;
  background: #fff5f5;
}

.metric-card .num {
  font-family: var(--font-heading, "Playfair Display", Georgia, serif);
  font-size: 2.1rem;
  font-weight: 700;
  color: var(--primary-color, #0a0a0a);
  line-height: 1;
  margin-bottom: 6px;
}

.metric-card.alert .num {
  color: #dc2626;
}

.metric-card .label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #64748b;
}

.devices-section-header {
  font-family: var(--font-heading, "Playfair Display", Georgia, serif);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--primary-color, #0a0a0a);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.devices-section-header span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.device-card-item {
  background: #ffffff;
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.device-card-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}

.device-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
  width: 100%;
}

.device-info-meta {
  display: flex;
  align-items: center;
  gap: 14px;
}

.device-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0f172a;
  flex-shrink: 0;
}

.device-title-text {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--primary-color, #0a0a0a);
  margin-bottom: 2px;
}

.device-meta-badges {
  font-size: 0.8rem;
  color: #64748b;
  display: flex;
  gap: 6px;
  align-items: center;
}

.device-meta-badges span {
  background: #f1f5f9;
  padding: 2px 8px;
  border-radius: 4px;
  font-weight: 600;
}

.device-status-badge {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 4px 10px;
  border-radius: 20px;
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
  flex-shrink: 0;
}

.device-details-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.detail-box {
  background: #f8fafc;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #f1f5f9;
  min-width: 0;
  box-sizing: border-box;
}

.detail-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 4px;
}

.detail-val {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--primary-color, #0a0a0a);
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 0;
}

.detail-val span {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.detail-val svg {
  flex-shrink: 0;
  color: #64748b;
}

.security-footer-note {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-left: 4px solid var(--accent-color, #d93025);
  border-radius: 10px;
  padding: 18px 22px;
  margin-top: 28px;
  font-size: 0.88rem;
  color: #64748b;
  line-height: 1.55;
}

.security-footer-note strong { color: var(--primary-color, #0a0a0a); }
.security-footer-note a { color: var(--accent-color, #d93025); font-weight: 600; text-decoration: underline; }

@media (max-width: 768px) {
  .session-action-inner {
    flex-direction: column;
    text-align: center;
    padding: 24px 20px;
    gap: 20px;
  }
  .session-cta-box {
    width: 100%;
  }
  .session-hero-title { font-size: 1.6rem; }
  .session-metrics-grid { grid-template-columns: 1fr; }
  .device-header-row { flex-wrap: wrap; }
  .device-details-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 480px) {
  .device-details-grid { grid-template-columns: 1fr; }
}
</style>

<section class="session-limit-wrapper">
  <div class="container">
    <div class="session-limit-container">

      <!-- Hero Action Card (Desktop Optimized) -->
      <div class="session-hero-action-card">
        <div class="session-action-inner">

          <div class="session-hero-content">
            <span class="session-alert-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              Multiple Active Devices Detected
            </span>

            <h1 class="session-hero-title">Action Required: Sign Out All Devices</h1>

            <p class="session-hero-desc">
              Your account is currently active on <strong><?php echo $totalDevices; ?> devices</strong> (subscription limit is <strong><?php echo $allowedDevices; ?></strong>).
              To restore immediate access to premium content, click the button to sign out from all sessions.
            </p>
          </div>

          <!-- Top Prominent Logout Button -->
          <div class="session-cta-box">
            <a href="/logout-all-devices/" class="btn-logout-primary" id="logoutAllBtn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
              Logout From All Devices &rarr;
            </a>

            <p class="session-cta-note">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              Log back in on this device to resume reading.
            </p>
          </div>

        </div>
      </div>

      <!-- Quick Metrics Grid -->
      <div class="session-metrics-grid">
        <div class="metric-card <?php echo ($totalDevices > $allowedDevices) ? 'alert' : ''; ?>">
          <div class="num"><?php echo $totalDevices; ?></div>
          <div class="label">Currently Active</div>
        </div>

        <div class="metric-card">
          <div class="num"><?php echo $allowedDevices; ?></div>
          <div class="label">Allowed Limit</div>
        </div>

        <div class="metric-card <?php echo ($excessDevices > 0) ? 'alert' : ''; ?>">
          <div class="num"><?php echo $excessDevices; ?></div>
          <div class="label">Excess Sessions</div>
        </div>
      </div>

      <!-- Active Devices Section -->
      <div class="devices-section-header">
        <span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          Connected Devices List
        </span>
        <span class="devices-total-badge"><?php echo $totalDevices; ?> total active</span>
      </div>

      <div class="devices-list">
        <?php if ($totalDevices > 0) { ?>
          <?php while ($device = mysqli_fetch_assoc($devices)) {
            $locationParts = array_filter([
              $device['device_city'] ?? '',
              $device['device_state'] ?? '',
              $device['device_country'] ?? ''
            ]);

            $location = implode(', ', $locationParts);
            if (empty($location)) {
              $location = 'Unknown Location';
            }
          ?>
            <div class="device-card-item">
              <div class="device-header-row">
                <div class="device-info-meta">
                  <div class="device-icon-box">
                    <?php echo deviceIcon($device['device_type'] ?? ''); ?>
                  </div>
                  <div>
                    <div class="device-title-text">
                      <?php echo htmlspecialchars(ucfirst($device['device_type'] ?: 'Unknown Device')); ?>
                    </div>
                    <div class="device-meta-badges">
                      <span><?php echo htmlspecialchars($device['device_browser'] ?: 'Unknown Browser'); ?></span>
                      &bull;
                      <span><?php echo htmlspecialchars($device['device_os'] ?: 'Unknown OS'); ?></span>
                    </div>
                  </div>
                </div>
                <span class="device-status-badge">Active Session</span>
              </div>

              <div class="device-details-grid">
                <div class="detail-box">
                  <span class="detail-label">Location</span>
                  <div class="detail-val" title="<?php echo htmlspecialchars($location); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span><?php echo htmlspecialchars($location); ?></span>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Browser</span>
                  <div class="detail-val" title="<?php echo htmlspecialchars($device['device_browser'] ?: 'Unknown'); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    <span><?php echo htmlspecialchars($device['device_browser'] ?: 'Unknown'); ?></span>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Operating System</span>
                  <div class="detail-val" title="<?php echo htmlspecialchars($device['device_os'] ?: 'Unknown'); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="15" x2="23" y2="15"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="15" x2="4" y2="15"/></svg>
                    <span><?php echo htmlspecialchars($device['device_os'] ?: 'Unknown'); ?></span>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Last Active</span>
                  <div class="detail-val" title="<?php echo timeAgo($device['us_active_time']); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><?php echo timeAgo($device['us_active_time']); ?></span>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="no-devices-box">
            <h3>No Active Sessions Found</h3>
          </div>
        <?php } ?>
      </div>

      <!-- Security Notice -->
      <div class="security-footer-note">
        <strong>Security Policy Notice:</strong> Subscriptions are for single-user personal use (max <?php echo $allowedDevices; ?> active devices). Signing out of all devices clears extra sessions so you can log back in immediately. For account assistance, contact <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a>.
      </div>

    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const logoutBtn = document.getElementById('logoutAllBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      const targetUrl = this.getAttribute('href');

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Sign Out All Devices?',
          text: 'This will end active sessions across all devices. You can log back in immediately on this device.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Sign Out All',
          cancelButtonText: 'Cancel',
          customClass: {
            popup: 'swal2-editorial-popup'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = targetUrl;
          }
        });
      } else {
        if (confirm('This will log out your account from all active devices. Continue?')) {
          window.location.href = targetUrl;
        }
      }
    });
  }
});
</script>

<?php
require_once 'footer.php';
?>