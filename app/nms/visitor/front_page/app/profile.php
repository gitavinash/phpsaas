<?php

require_once 'header.php';

$activeSubscription = false;
if (isset($_COOKIE['user_data'])) {
    require_once LOGIN_CHECK;
    $userSaltKey = base64_decode($_COOKIE['user_data']);

    if ($checkUser = mysqli_query($connection, "SELECT subscription_status, subscription_relation_id, subscription_type, subscription_mode, subscription_time, subscription_expiry FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND CURDATE() <= subscription_expiry AND subscription_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
            if ($subscription_status == 1) {
                $activeSubscription = true;

                // Fetch subscription details

                if ($subsriptionDetails = mysqli_query($connection, "SELECT {$subscription_type}_image, {$subscription_type}_title, {$subscription_type}_description, {$subscription_type}_highlight, {$subscription_type}_type, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "{$subscription_type} INNER JOIN " . TABLE_PREFIX . "inventory ON {$subscription_type}_id=inventory_relation_id WHERE {$subscription_type}_id='{$subscription_relation_id}' AND {$subscription_type}_type='subscription' AND {$subscription_type}_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($subsriptionDetails) != 0) {
                        extract(mysqli_fetch_assoc($subsriptionDetails));
                    }
                }
            }
        }
    }

    // Check user active session

    if (!in_array($user_id, [9423, 9473])) {
        if ($checkUserSession = mysqli_query($connection, "SELECT us_active_device FROM " . TABLE_PREFIX . "user_sessions WHERE us_status=1 AND us_user_id='{$user_id}' AND us_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkUserSession) > 2) {
                $showLogoutBtn = 0;
                $excluded_paths = ['/manage-subscription/', '/terms-and-conditions/', '/about-us/', '/terms-and-conditions/', '/privacy-policy/', '/refund-policy/'];
                $is_excluded = false;
                foreach ($excluded_paths as $path) {
                    if (strpos(BASE_URL, $path) !== false) {
                        $is_excluded = true;
                        break;
                    }
                }

                if (!$is_excluded) {
                    require_once 'user-suspend.php';
                    exit();
                }
            } else {
                $showLogoutBtn = 1;
            }
        }
    }
}

?>
<!-- PROFILE PAGE CONTENT -->
<div id="view-profile" class="page-view active">

    <?php

    if (isset($_COOKIE['user_data'])) { ?>

        <div id='logged-in-profile'>
            <div class='profile-header'>
                <a href='#' class='avatar'>
                    <?php echo ((PROFILE_IMAGE != '') ? "<img src='" . PROFILE_IMAGE . "' alt='" . PROFILE_DISPLAY_NAME . "' />" : strtoupper(substr(PROFILE_DISPLAY_NAME, 0, 2))); ?>
                </a>
                <div class='profile-info'>
                    <h2 class='profile-name'><?php echo ucwords(PROFILE_DISPLAY_NAME) ?></h2>
                    <p class='profile-email'><?php echo strtolower(PROFILE_EMAIL) ?></p>
                </div>
            </div>

            <?php

            if ($activeSubscription == true) { ?>
                <div class='subscription-card native-card'>
                    <div class='sub-card-top'>
                        <div class='sub-card-left'>
                            <div class='sub-icon'><i class='ph-fill ph-crown'></i></div>
                            <div class='sub-text'>
                                <h4>Pro Member</h4>
                                <p class='sub-expiry'>
                                    <i class='ph ph-calendar-blank'></i> Expires on <?php echo date('M d, Y', strtotime($subscription_expiry)) ?>
                                </p>
                            </div>
                        </div>
                        <span class='sub-status-badge'>Active</span>
                    </div>
                    <!-- 
                    <div class='sub-card-divider'></div>

                    <div class='sub-card-bottom'>
                        <span class='sub-plan-desc'>Unlimited access to deepdives & explainers</span>
                        <a href='/subscribe/' class='renew-btn'>
                            <i class='ph ph-arrows-clockwise'></i> Renew Now
                        </a>
                    </div>
                    -->
                </div>
            <?php
            } else { ?>
                <div class='guest-cta-card'>
                    <h4>Get Unlimited Access</h4>
                    <p>Subscribe to read independent fintech reporting.</p>
                    <div class='guest-cta-buttons'>
                        <a href='/subscribe/' class='auth-btn primary-btn'>
                            View Plans
                        </a>
                    </div>
                </div>
            <?php
            }
            ?>

        </div>


    <?php
    } else { ?>

        <div id='guest-profile'>
            <div class='guest-profile-header'>
                <div class='guest-avatar'>
                    <i class='ph ph-user'></i>
                </div>
                <h2 class='guest-title'>Welcome Guest</h2>
                <p class='guest-subtitle'>
                    Sign in to access your subscription, saved articles, and personalized settings.
                </p>
                <a href='/login/' class='auth-btn primary-btn'>
                    Sign In / Register
                </a>
            </div>
        </div>

    <?php
    }
    ?>

    <div class="settings-list">
        <!-- <a
            href="/corporate-subscription/"
            class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-briefcase"></i> Corporate Subscription
            </div>
            <i class="ph ph-caret-right"></i>
        </a> -->
        <!-- <div class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-bell"></i> Push Notifications
            </div>
            <i class="ph ph-caret-right"></i>
        </div> -->
        <div class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-moon"></i> Dark Mode
            </div>
            <label class="switch">
                <input type="checkbox" id="profile-theme-toggle" />
                <span class="slider round"></span>
            </label>
        </div>
        <a
            href="/about-us/"
            class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-info"></i> About <?php echo SITE_NAME ?>
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <a href="/contact-us/" class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-envelope-simple"></i> Contact Us
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <a href="/terms-and-conditions/" class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-file-text"></i> Terms and Conditions
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <a href="/privacy-policy/" class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-shield-check"></i> Privacy Policy
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <a href="/refund-policy/" class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-receipt"></i> Refund Policy
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <a href="/partner-insights-guidelines-/" class="settings-item native-list-item">
            <div class="settings-label">
                <i class="ph ph-lightbulb"></i> Partner Insights Guidelines
            </div>
            <i class="ph ph-caret-right"></i>
        </a>
        <?php

        if (isset($_COOKIE['user_data'])) { ?>
            <!-- <a href="/cancel-subscription/" class="settings-item native-list-item">
                <div class="settings-label">
                    <i class="ph ph-x-circle"></i> Cancel Subscription
                </div>
                <i class="ph ph-caret-right"></i>
            </a> -->
            <a
                href="/logout/"
                class="settings-item logout-btn native-list-item">
                <div class="settings-label">
                    <i class="ph ph-sign-out"></i> Log Out
                </div>
            </a>
        <?php
        }

        ?>
    </div>
</div>
<?php
require_once 'footer.php';
?>