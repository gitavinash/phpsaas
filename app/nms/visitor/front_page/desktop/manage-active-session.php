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
      return '<i class="ph ph-device-mobile"></i>';

    case 'tablet':
    case 'ipad':
      return '<i class="ph ph-device-tablet"></i>';

    case 'desktop':
    case 'windows':
    case 'mac':
      return '<i class="ph ph-desktop"></i>';

    default:
      return '<i class="ph ph-monitor"></i>';
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
  /* Dedicated Active Session & Device Suspension Layout Styles */
  .session-limit-wrapper {
    padding: 40px 0 80px 0;
    background-color: var(--bg-color, #fdfbf7);
    min-height: calc(100vh - 350px);
    font-family: var(--font-body, "Inter", -apple-system, BlinkMacSystemFont, sans-serif);
  }

  .session-limit-container {
    max-width: 840px;
    margin: 0 auto;
  }

  /* Top Prominent Action Hero Card */
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
    background: var(--accent-color);
  }

  .session-action-inner {
    padding: 36px;
    text-align: center;
  }

  .session-alert-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: #fee2e2;
    color: #dc2626;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
    border: 1px solid #fca5a5;
  }

  .session-hero-title {
    font-family: var(--font-heading, "Playfair Display", Georgia, serif);
    font-size: 2.1rem;
    font-weight: 700;
    color: var(--primary-color, #0a0a0a);
    margin: 0 0 12px 0;
    line-height: 1.2;
  }

  .session-hero-desc {
    font-size: 1rem;
    color: var(--text-secondary, #555555);
    max-width: 640px;
    margin: 0 auto 24px auto;
    line-height: 1.6;
  }

  /* Top Logout Button Box */
  .session-cta-box {
    background: #fdf2f2;
    border: 1px solid #f0f0f0;
    border-radius: 12px;
    padding: 24px;
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }

  .btn-logout-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #dc2626;
    color: #ffffff !important;
    font-weight: 700;
    font-size: 1.05rem;
    padding: 16px 36px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
    width: 100%;
    max-width: 420px;
    box-sizing: border-box;
  }

  .btn-logout-primary:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
  }

  .session-cta-note {
    font-size: 0.85rem;
    color: #7f1d1d;
    font-weight: 500;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* Metric Summary Grid */
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
  }

  .metric-card.alert {
    border-color: #fecaca;
    background: #fff5f5;
  }

  .metric-card .num {
    font-family: var(--font-heading, "Playfair Display", Georgia, serif);
    font-size: 2.2rem;
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

  /* Devices List Section */
  .devices-section-header {
    font-family: var(--font-heading, "Playfair Display", Georgia, serif);
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--primary-color, #0a0a0a);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .device-card-item {
    background: #ffffff;
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  }

  .device-header-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
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
    font-size: 1.3rem;
    color: var(--primary-color, #0a0a0a);
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

  .device-details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
  }

  .detail-box {
    background: #f8fafc;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
  }

  .detail-label {
    display: block;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 3px;
  }

  .detail-val {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--primary-color, #0a0a0a);
    display: flex;
    align-items: center;
    gap: 5px;
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

  .security-footer-note strong {
    color: var(--primary-color, #0a0a0a);
  }

  .security-footer-note a {
    color: var(--accent-color, #d93025);
    font-weight: 600;
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .session-action-inner {
      padding: 24px 20px;
    }

    .session-hero-title {
      font-size: 1.6rem;
    }

    .session-metrics-grid {
      grid-template-columns: 1fr;
    }

    .device-details-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>

<section class="session-limit-wrapper">
  <div class="container">
    <div class="session-limit-container">

      <!-- Prominent Top Hero Action Box -->
      <div class="session-hero-action-card">
        <div class="session-action-inner">

          <span class="session-alert-badge">
            <i class="ph ph-warning-circle"></i> Multiple Active Devices Detected
          </span>

          <h1 class="session-hero-title">Action Required: Sign Out All Devices</h1>

          <p class="session-hero-desc">
            Your account is currently active on <strong><?php echo $totalDevices; ?> devices</strong> (subscription limit is <strong><?php echo $allowedDevices; ?></strong>).
            To restore immediate access to premium content, click the button below to sign out from all sessions.
          </p>

          <!-- Top Prominent Logout Button -->
          <div class="session-cta-box">
            <a href="/logout-all-devices/" class="btn-logout-primary" id="logoutAllBtn">
              <i class="ph ph-sign-out" style="font-size: 1.3rem;"></i> Logout From All Devices &rarr;
            </a>

            <p class="session-cta-note">
              <i class="ph ph-info"></i> After signing out, simply log back in on this device to resume reading.
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
        <span><i class="ph ph-devices"></i> Connected Devices List</span>
        <span style="font-size: 0.85rem; font-weight: 500; color: #64748b; font-family: var(--font-body);"><?php echo $totalDevices; ?> total active</span>
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

              <div class="device-details-grid">
                <div class="detail-box">
                  <span class="detail-label">Location</span>
                  <div class="detail-val">
                    <i class="ph ph-map-pin"></i> <?php echo htmlspecialchars($location); ?>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Browser</span>
                  <div class="detail-val">
                    <i class="ph ph-globe"></i> <?php echo htmlspecialchars($device['device_browser'] ?: 'Unknown'); ?>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Operating System</span>
                  <div class="detail-val">
                    <i class="ph ph-cpu"></i> <?php echo htmlspecialchars($device['device_os'] ?: 'Unknown'); ?>
                  </div>
                </div>

                <div class="detail-box">
                  <span class="detail-label">Last Active</span>
                  <div class="detail-val">
                    <i class="ph ph-clock"></i> <?php echo timeAgo($device['us_active_time']); ?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div style="background: #ffffff; text-align: center; padding: 40px; border-radius: 12px; border: 1px solid #e0e0e0; color: #64748b;">
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