<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
$isFullSettingAccess = in_array($currentUserType, ['administrator', 'operator']);
?>
<div class="package-page-wrapper setting-page-wrapper">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-settings"></i>
            </div>
            <div class="package-header-titles">
                <h2><?php echo $isFullSettingAccess ? 'Workspace &amp; Account Settings' : 'Account &amp; Profile Settings'; ?></h2>
                <p><?php echo $isFullSettingAccess ? 'Manage website branding, public contact channels, banking &amp; UPI, alert routing, SEO, and personal profile.' : 'Manage your personal profile photo, display name, contact phone, and account credentials.'; ?></p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/" class="btn-v3-action" data-tooltip="Back to Dashboard">
                <i class="fi fi-rr-arrow-left"></i> <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Modern Settings Cards Grid -->
    <div class="v3-settings-cards-grid">
        <?php if ($isFullSettingAccess) { ?>
        <!-- Card 1: Configuration -->
        <a href="/dashboard/setting/configuration/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon indigo v3-setting-card-icon">
                    <i class="fi fi-rr-settings-sliders"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">Website Configuration</h3>
                    <p class="v3-setting-card-desc">
                        Configure website name, logo, favicon, domain, accent theme color, and portal branding.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 2: Public Contact & Business Details -->
        <a href="/dashboard/setting/contact/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon emerald v3-setting-card-icon">
                    <i class="fi fi-rr-phone-call"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">Public Contact Information</h3>
                    <p class="v3-setting-card-desc">
                        Manage customer helpline numbers, official WhatsApp, support email, registered office address, and social links.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 3: Alert & Notification Receiving (Powered by Notify360) -->
        <a href="/dashboard/setting/notifications/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item v3-setting-notify-card">
                <div class="v3-card-header-icon purple v3-setting-card-icon">
                    <i class="fi fi-rr-bell-ring"></i>
                </div>
                <div class="v3-setting-card-content">
                    <h3 class="v3-setting-card-title">Alert &amp; Notifications</h3>
                    <p class="v3-setting-card-desc">
                        Set recipient emails, WhatsApp numbers, SMS, and browser push to receive instant inquiry and payment alerts.
                    </p>
                </div>
                <!-- Bottom Right Powered By Watermark -->
                <div class="v3-setting-watermark">
                    <span class="v3-watermark-text">Powered by</span>
                    <img src="/app/dashboard/v3/default/desktop/assets/images/notify360.png" alt="Notify360" class="v3-watermark-img">
                </div>
            </div>
        </a>

        <!-- Card 4: Bank Accounts & UPI Details -->
        <a href="/dashboard/setting/banking/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon blue v3-setting-card-icon">
                    <i class="fi fi-rr-bank"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">Bank Accounts &amp; UPI</h3>
                    <p class="v3-setting-card-desc">
                        Configure official bank accounts, IFSC codes, and UPI payment addresses displayed on customer invoices and vouchers.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 5: SEO Metadata & Search Indexing -->
        <a href="/dashboard/setting/seo/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon sky v3-setting-card-icon">
                    <i class="fi fi-rr-search-alt"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">SEO &amp; Search Indexing</h3>
                    <p class="v3-setting-card-desc">
                        Define global search snippets, keywords, and search engine crawler instructions for your public website.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 6: App & Business Configuration -->
        <a href="/dashboard/setting/app/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon amber v3-setting-card-icon">
                    <i class="fi fi-rr-apps-sort"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">App &amp; Business Configuration</h3>
                    <p class="v3-setting-card-desc">
                        Manage business nature (Travel Agency, Transporter), quotation workflows, proposal modes, and operational features.
                    </p>
                </div>
            </div>
        </a>
        <?php } ?>

        <!-- Card 7: Personal Profile (Visible to all users) -->
        <a href="/dashboard/setting/profile/" class="v3-setting-card-link">
            <div class="v3-card-section v3-setting-card-item">
                <div class="v3-card-header-icon purple v3-setting-card-icon">
                    <i class="fi fi-rr-portrait"></i>
                </div>
                <div>
                    <h3 class="v3-setting-card-title">Personal Profile</h3>
                    <p class="v3-setting-card-desc">
                        Update your profile photo, display name, personal phone numbers, notification email, and account credentials.
                    </p>
                </div>
            </div>
        </a>
    </div>
</div>