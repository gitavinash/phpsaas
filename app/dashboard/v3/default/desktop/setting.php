<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
$isFullSettingAccess = in_array($currentUserType, ['administrator', 'operator']);
?>
<div class="package-page-wrapper setting-page-wrapper">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <i class="fi fi-rr-settings" style="color: #ffffff;"></i>
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
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-top: 16px;">
        <?php if ($isFullSettingAccess) { ?>
        <!-- Card 1: Configuration -->
        <a href="/dashboard/setting/configuration/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease;">
                <div class="v3-card-header-icon indigo" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px;">
                    <i class="fi fi-rr-settings-sliders"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Website Configuration</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Configure website name, logo, favicon, domain, accent theme color, and portal branding.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 2: Public Contact & Business Details -->
        <a href="/dashboard/setting/contact/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease;">
                <div class="v3-card-header-icon emerald" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px;">
                    <i class="fi fi-rr-phone-call"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Public Contact Information</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Manage customer helpline numbers, official WhatsApp, support email, registered office address, and social links.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 3: Alert & Notification Receiving (Powered by Notify360) -->
        <a href="/dashboard/setting/notifications/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease; position: relative; overflow: hidden;">
                <div class="v3-card-header-icon violet" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px; background: #ede9fe; color: #7c3aed;">
                    <i class="fi fi-rr-bell-ring"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Alert &amp; Notifications</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Set recipient emails, WhatsApp numbers, SMS, and browser push to receive instant inquiry and payment alerts.
                    </p>
                </div>
                <!-- Bottom Right Powered By Watermark -->
                <div style="position: absolute; right: 16px; bottom: 10px; display: flex; align-items: center; gap: 4px; opacity: 0.5; pointer-events: none;">
                    <span style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Powered by</span>
                    <img src="/app/dashboard/v3/default/desktop/assets/images/notify360.png" alt="Notify360" style="height: 14px; width: auto; object-fit: contain;">
                </div>
            </div>
        </a>

        <!-- Card 4: Bank Accounts & UPI Details -->
        <a href="/dashboard/setting/banking/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease;">
                <div class="v3-card-header-icon blue" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px; background: #e0f2fe; color: #0284c7;">
                    <i class="fi fi-rr-bank"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Bank Accounts &amp; UPI</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Configure official bank accounts, IFSC codes, and UPI payment addresses displayed on customer invoices and vouchers.
                    </p>
                </div>
            </div>
        </a>

        <!-- Card 5: SEO Metadata & Search Indexing -->
        <a href="/dashboard/setting/seo/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease;">
                <div class="v3-card-header-icon sky" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px; background: #e0f2fe; color: #0369a1;">
                    <i class="fi fi-rr-search-alt"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">SEO &amp; Search Indexing</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Define global search snippets, keywords, and search engine crawler instructions for your public website.
                    </p>
                </div>
            </div>
        </a>
        <?php } ?>

        <!-- Card 6: Personal Profile (Visible to all users) -->
        <a href="/dashboard/setting/profile/" style="text-decoration: none; color: inherit; display: block;">
            <div class="v3-card-section" style="padding: 24px; display: flex; align-items: flex-start; gap: 16px; height: 100%; transition: all 0.2s ease;">
                <div class="v3-card-header-icon purple" style="width: 48px; height: 48px; min-width: 48px; font-size: 22px; border-radius: 12px;">
                    <i class="fi fi-rr-portrait"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Personal Profile</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.45;">
                        Update your profile photo, display name, personal phone numbers, notification email, and account credentials.
                    </p>
                </div>
            </div>
        </a>
    </div>
</div>