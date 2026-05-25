<?php

require_once 'header-2.php';

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
            return '📱';

        case 'tablet':
        case 'ipad':
            return '📟';

        case 'desktop':
        case 'windows':
        case 'mac':
            return '💻';

        default:
            return '🖥️';
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

?>

<style>
.device-limit-page {
    background: #f7f8fc;
    min-height: 100vh;
    padding: 60px 0;
}

.limit-box {
    background: #fff4e5;
    border: 1px solid #ffd08a;
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 30px;
}

.limit-box h1 {
    margin: 0 0 10px;
    font-size: 30px;
    font-weight: 700;
    color: #7a4b00;
}

.limit-box p {
    margin: 0;
    color: #8b5e12;
    line-height: 1.7;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
}

.summary-card h2 {
    margin: 0;
    font-size: 34px;
    font-weight: 700;
}

.summary-card span {
    color: #777;
}

.device-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.device-card {
    background: #fff;
    border-radius: 18px;
    padding: 24px;
    border: 1px solid #ececec;
    box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
}

.device-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
}

.device-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f4f6fb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.device-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
}

.device-subtitle {
    color: #666;
    font-size: 14px;
}

.device-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
}

.info-box {
    background: #f8f9fc;
    padding: 14px;
    border-radius: 12px;
}

.info-label {
    display: block;
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #222;
}

.device-actions {
    margin-top: 40px;
    text-align: center;
}

.logout-btn {
    display: inline-block;
    background: #dc3545;
    color: #fff;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 12px;
    font-weight: 700;
    transition: .3s;
}

.logout-btn:hover {
    background: #c82333;
    color: #fff;
}

.security-note {
    margin-top: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #dc3545;
}

.empty-state {
    background: #fff;
    text-align: center;
    padding: 60px;
    border-radius: 16px;
}

@media(max-width:768px) {

    .limit-box h1 {
        font-size: 24px;
    }

}
</style>

<section class="device-limit-page">

    <div class="container">

        <div class="limit-box">

            <h1>⚠ Multiple Active Devices Detected</h1>

            <p>
                We detected that your account is currently signed in on more devices than permitted under our
                subscription policy.

                To protect account security and prevent unauthorized account sharing, access to premium content has been
                temporarily restricted until the number of active devices is reduced.

                Please review the active devices listed below and use the button provided to sign out from all devices.
                Once signed out, you can log back in only on the devices you personally use.

                Access will be restored automatically after your account complies with the active device limit.
            </p>

        </div>

        <div class="summary-grid">

            <div class="summary-card">
                <h2><?php echo $totalDevices; ?></h2>
                <span>Active Devices</span>
            </div>

            <div class="summary-card">
                <h2><?php echo $allowedDevices; ?></h2>
                <span>Allowed Devices</span>
            </div>

            <div class="summary-card">
                <h2><?php echo max(0, ($totalDevices - $allowedDevices)); ?></h2>
                <span>Excess Devices</span>
            </div>

        </div>

        <div class="device-grid">

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

            <div class="device-card">

                <div class="device-header">

                    <div class="device-icon">
                        <?php echo deviceIcon($device['device_type'] ?? ''); ?>
                    </div>

                    <div>

                        <div class="device-title">
                            <?php
                                    echo htmlspecialchars(
                                        ucfirst($device['device_type'] ?: 'Unknown Device')
                                    );
                                    ?>
                        </div>

                        <div class="device-subtitle">
                            <?php
                                    echo htmlspecialchars(
                                        $device['device_browser'] ?: 'Unknown Browser'
                                    );
                                    ?>
                            •
                            <?php
                                    echo htmlspecialchars(
                                        $device['device_os'] ?: 'Unknown OS'
                                    );
                                    ?>
                        </div>

                    </div>

                </div>

                <div class="device-info">

                    <div class="info-box">
                        <span class="info-label">Location</span>
                        <div class="info-value">
                            📍 <?php echo htmlspecialchars($location); ?>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Browser</span>
                        <div class="info-value">
                            <?php echo htmlspecialchars($device['device_browser'] ?: 'Unknown'); ?>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Operating System</span>
                        <div class="info-value">
                            <?php echo htmlspecialchars($device['device_os'] ?: 'Unknown'); ?>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Last Active</span>
                        <div class="info-value">
                            🕒 <?php echo timeAgo($device['us_active_time']); ?>
                        </div>
                    </div>

                </div>

            </div>

            <?php } ?>

            <?php } else { ?>

            <div class="empty-state">
                <h3>No Active Devices Found</h3>
            </div>

            <?php } ?>

        </div>

        <div class="device-actions">

            <a href="/logout-all-devices/" class="logout-btn"
                onclick="return confirm('This will logout your account from every device. Continue?');">

                Logout From All Devices

            </a>

        </div>

        <div class="security-note">

            <strong>Important Account Notice</strong>

            <p>
                Your subscription is intended for personal use and supports a limited number of simultaneous active
                devices.

                Our system has detected activity that exceeds the permitted device limit associated with your account.
                As a result, premium access has been temporarily restricted.

                To continue using the service, please sign out from all active devices and log in again only on devices
                that belong to you.

                If you believe this restriction was applied in error, please contact our support team for assistance.
            </p>

        </div>

    </div>

</section>

<?php
require_once 'footer.php';
?>