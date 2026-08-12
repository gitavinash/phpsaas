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
              <i class="ph ph-sign-out ph-lg"></i> Logout From All Devices &rarr;
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