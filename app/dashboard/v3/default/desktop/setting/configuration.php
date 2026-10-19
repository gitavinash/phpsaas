<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingConfigApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon sky">
                <i class="fi fi-rr-settings-sliders"></i>
            </div>
            <div class="package-header-titles">
                <h2>Website Configuration</h2>
                <p>Manage general website identity, search engine indexing, metadata, and branding theme.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/setting/" class="btn-v3-action" data-tooltip="Back to Settings">
                <i class="fi fi-rr-settings"></i> <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="settingConfigForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Website Identity & Domain -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-globe"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Website Identity &amp; Domain</h3>
                            <p>Configure primary public site name, active domain, and default locale.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Website Name -->
                            <div class="v3-form-group span-2">
                                <label for="configSiteName" class="v3-form-label">
                                    Website Title / Business Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="configSiteName" name="site_name" class="v3-form-input" placeholder="e.g. Company Name" required autofocus>
                                </div>
                                <span class="v3-field-error" id="errConfigSiteName"></span>
                            </div>

                            <!-- Primary Domain (Readonly) -->
                            <div class="v3-form-group">
                                <label for="configSiteDomain" class="v3-form-label">
                                    Registered Domain
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-link-alt input-icon"></i>
                                    <input type="text" id="configSiteDomain" class="v3-form-input" readonly placeholder="Loading domain...">
                                </div>
                            </div>

                            <!-- Website Language -->
                            <div class="v3-form-group">
                                <label for="configSiteLang" class="v3-form-label">
                                    Default Site Language <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <select id="configSiteLang" name="site_lang" class="v3-form-select selectJS2" required>
                                        <option value="en">English (en)</option>
                                        <option value="hi_IN">Hindi (हिंदी - hi_IN)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Website Timezone -->
                            <div class="v3-form-group span-2">
                                <label for="configSiteTimezone" class="v3-form-label">
                                    Website Time Zone <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-clock input-icon"></i>
                                    <select id="configSiteTimezone" name="site_timezone" class="v3-form-select selectJS2" required>
                                        <option value="Asia/Kolkata">Asia/Kolkata (IST - UTC+05:30) - India Standard Time</option>
                                        <option value="UTC">UTC (Coordinated Universal Time - UTC+00:00)</option>
                                        <option value="Asia/Dubai">Asia/Dubai (GST - UTC+04:00) - UAE, Gulf</option>
                                        <option value="Asia/Singapore">Asia/Singapore (SGT - UTC+08:00) - Singapore</option>
                                        <option value="Asia/Kathmandu">Asia/Kathmandu (NPT - UTC+05:45) - Nepal</option>
                                        <option value="Asia/Dhaka">Asia/Dhaka (BST - UTC+06:00) - Bangladesh</option>
                                        <option value="Asia/Bangkok">Asia/Bangkok (ICT - UTC+07:00) - Thailand, Indochina</option>
                                        <option value="Asia/Tokyo">Asia/Tokyo (JST - UTC+09:00) - Japan</option>
                                        <option value="Europe/London">Europe/London (GMT/BST - UTC+00:00 / UTC+01:00) - UK</option>
                                        <option value="Europe/Paris">Europe/Paris (CET/CEST - UTC+01:00 / UTC+02:00) - Central Europe</option>
                                        <option value="Europe/Berlin">Europe/Berlin (CET/CEST - UTC+01:00 / UTC+02:00) - Germany</option>
                                        <option value="America/New_York">America/New_York (EST/EDT - UTC-05:00) - US Eastern</option>
                                        <option value="America/Chicago">America/Chicago (CST/CDT - UTC-06:00) - US Central</option>
                                        <option value="America/Denver">America/Denver (MST/MDT - UTC-07:00) - US Mountain</option>
                                        <option value="America/Los_Angeles">America/Los_Angeles (PST/PDT - UTC-08:00) - US Pacific</option>
                                        <option value="America/Toronto">America/Toronto (EST/EDT - UTC-05:00) - Canada Eastern</option>
                                        <option value="Australia/Sydney">Australia/Sydney (AEST/AEDT - UTC+10:00 / UTC+11:00) - Sydney</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Brand Assets & Visual Identity (2 Columns) -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon rose">
                            <i class="fi fi-rr-picture"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Brand Assets &amp; Visual Identity</h3>
                            <p>Upload your website header logo and browser tab favicon.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-brand-grid">
                            <!-- Column 1: Website Main Logo (Rectangular Preview) -->
                            <div class="v3-brand-upload-item">
                                <div class="v3-logo-wrapper">
                                    <div class="v3-logo-frame">
                                        <img id="configLogoPreview" class="is-v3-hidden" src="" alt="Website Main Logo">
                                        <div id="configLogoPlaceholder" class="v3-avatar-initials">
                                            <i class="fi fi-rr-picture"></i>
                                        </div>
                                    </div>
                                    <label for="configLogoInput" class="v3-avatar-edit-badge" title="Upload Logo">
                                        <i class="fi fi-rr-camera"></i>
                                    </label>
                                </div>
                                <div class="v3-profile-hero-info">
                                    <h4>Website Main Logo</h4>
                                    <p>Header &amp; invoice logo (PNG, JPG, SVG, WEBP &mdash; max 5MB).</p>
                                    <div class="v3-profile-hero-actions">
                                        <label for="configLogoInput" class="v3-btn-photo-upload">
                                            <i class="fi fi-rr-upload"></i> <span>Upload Logo</span>
                                        </label>
                                        <input type="file" id="configLogoInput" name="site_logo" accept="image/png, image/jpeg, image/webp, image/svg+xml" class="d-none">
                                        <button type="button" id="btnRemoveLogo" class="v3-btn-photo-remove is-v3-hidden" title="Remove Logo">
                                            <i class="fi fi-rr-trash"></i> <span>Remove</span>
                                        </button>
                                        <span id="configLogoFileName" class="v3-profile-file-name"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Browser Tab Favicon (Square Preview) -->
                            <div class="v3-brand-upload-item">
                                <div class="v3-favicon-wrapper">
                                    <div class="v3-favicon-frame">
                                        <img id="configFaviconPreview" class="is-v3-hidden" src="" alt="Browser Tab Favicon">
                                        <div id="configFaviconPlaceholder" class="v3-avatar-initials">
                                            <i class="fi fi-rr-globe"></i>
                                        </div>
                                    </div>
                                    <label for="configFaviconInput" class="v3-avatar-edit-badge" title="Upload Favicon">
                                        <i class="fi fi-rr-camera"></i>
                                    </label>
                                </div>
                                <div class="v3-profile-hero-info">
                                    <h4>Browser Tab Favicon</h4>
                                    <p>Square icon for tabs &amp; bookmarks (ICO, PNG, SVG &mdash; max 2MB).</p>
                                    <div class="v3-profile-hero-actions">
                                        <label for="configFaviconInput" class="v3-btn-photo-upload">
                                            <i class="fi fi-rr-upload"></i> <span>Upload Favicon</span>
                                        </label>
                                        <input type="file" id="configFaviconInput" name="site_icon" accept="image/x-icon, image/png, image/svg+xml, image/webp, image/jpeg" class="d-none">
                                        <button type="button" id="btnRemoveFavicon" class="v3-btn-photo-remove is-v3-hidden" title="Remove Favicon">
                                            <i class="fi fi-rr-trash"></i> <span>Remove</span>
                                        </button>
                                        <span id="configFaviconFileName" class="v3-profile-file-name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Theme & Branding Color -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-palette"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Branding Theme Color</h3>
                            <p>Select your website's primary theme accent color.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-theme-color-wrap">
                            <div class="v3-theme-picker-group">
                                <input type="color" id="configThemeColorPicker" value="#4f46e5" class="v3-color-picker-input">
                                <input type="text" id="configThemeColorInput" name="site_theme_color" value="#4f46e5" class="v3-form-input v3-color-hex-input">
                            </div>

                            <!-- Color Presets -->
                            <div class="v3-theme-presets-wrap">
                                <span class="v3-theme-presets-label">Presets:</span>
                                <button type="button" class="theme-preset-btn preset-indigo" data-color="#4f46e5" title="Indigo"></button>
                                <button type="button" class="theme-preset-btn preset-sky" data-color="#0284c7" title="Sky Blue"></button>
                                <button type="button" class="theme-preset-btn preset-emerald" data-color="#10b981" title="Emerald"></button>
                                <button type="button" class="theme-preset-btn preset-amber" data-color="#d97706" title="Amber"></button>
                                <button type="button" class="theme-preset-btn preset-purple" data-color="#7c3aed" title="Purple"></button>
                                <button type="button" class="theme-preset-btn preset-slate" data-color="#0f172a" title="Slate"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveConfig" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveConfigText">Save Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Brand Preview Card -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Website Brand Identity Card -->
                <div class="v3-live-preview-card">
                    <div class="v3-live-preview-header">
                        <span class="v3-live-preview-label">Brand Identity Preview</span>
                        <span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div class="v3-live-preview-profile">
                        <div class="v3-logo-frame v3-live-preview-logo-frame">
                            <img id="sidebarLogoPreview" class="is-v3-hidden" src="" alt="Brand Logo">
                            <div id="sidebarLogoPlaceholder" class="v3-avatar-initials">
                                <i class="fi fi-rr-picture"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="sidebarSiteName" class="v3-live-preview-name">Website Name</h4>
                            <p id="sidebarSiteDomain" class="v3-live-preview-domain">yourdomain.com</p>
                        </div>
                    </div>

                    <div class="v3-live-preview-footer">
                        <span>Theme Accent:</span>
                        <div class="v3-theme-picker-group">
                            <span id="sidebarThemeDot" class="v3-live-preview-theme-dot"></span>
                            <span id="sidebarThemeHex" class="v3-live-preview-theme-hex">#4F46E5</span>
                        </div>
                    </div>
                </div>

                <!-- Pro-Tips Box -->
                <div class="v3-tips-box emerald">
                    <div class="tips-icon"><i class="fi fi-rr-bulb"></i></div>
                    <div class="tips-content">
                        <strong>Branding Tip</strong>
                        Use a transparent PNG or SVG logo for crisp rendering on customer portals and quotation PDFs.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>