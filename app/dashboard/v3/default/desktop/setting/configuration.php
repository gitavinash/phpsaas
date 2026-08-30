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
            <div class="package-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);">
                <i class="fi fi-rr-settings-sliders" style="color: #ffffff;"></i>
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
                                    <select id="configSiteLang" name="site_lang" class="v3-form-select" required>
                                        <option value="en">English (en)</option>
                                        <option value="hi_IN">Hindi (हिंदी - hi_IN)</option>
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
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
                            <!-- Column 1: Website Main Logo (Rectangular Preview) -->
                            <div class="v3-profile-hero-card" style="margin-bottom: 0; height: 100%;">
                                <div class="v3-logo-wrapper">
                                    <div class="v3-logo-frame">
                                        <img id="configLogoPreview" class="is-v3-hidden" src="" alt="">
                                        <div id="configLogoPlaceholder" class="v3-avatar-initials" style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #64748b; font-size: 22px;">
                                            <i class="fi fi-rr-picture"></i>
                                        </div>
                                    </div>
                                    <label for="configLogoInput" class="v3-avatar-edit-badge" title="Upload Logo">
                                        <i class="fi fi-rr-camera"></i>
                                    </label>
                                </div>
                                <div class="v3-profile-hero-info">
                                    <h4 style="font-size: 14px;">Website Main Logo</h4>
                                    <p style="margin-bottom: 10px;">Header &amp; invoice logo (PNG, JPG, SVG, WEBP &mdash; max 5MB).</p>
                                    <div class="v3-profile-hero-actions">
                                        <label for="configLogoInput" class="v3-btn-photo-upload">
                                            <i class="fi fi-rr-upload"></i> <span>Upload Logo</span>
                                        </label>
                                        <input type="file" id="configLogoInput" name="site_logo" accept="image/png, image/jpeg, image/webp, image/svg+xml" style="display: none;">
                                        <button type="button" id="btnRemoveLogo" class="v3-btn-photo-remove is-v3-hidden" title="Remove Logo">
                                            <i class="fi fi-rr-trash"></i> <span>Remove</span>
                                        </button>
                                        <span id="configLogoFileName" style="font-size: 11.5px; color: #64748b; font-weight: 500; width: 100%;"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Browser Tab Favicon (Square Preview) -->
                            <div class="v3-profile-hero-card" style="margin-bottom: 0; height: 100%;">
                                <div class="v3-favicon-wrapper">
                                    <div class="v3-favicon-frame">
                                        <img id="configFaviconPreview" class="is-v3-hidden" src="" alt="">
                                        <div id="configFaviconPlaceholder" class="v3-avatar-initials" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; font-size: 22px;">
                                            <i class="fi fi-rr-globe"></i>
                                        </div>
                                    </div>
                                    <label for="configFaviconInput" class="v3-avatar-edit-badge" title="Upload Favicon" style="background: #16a34a;">
                                        <i class="fi fi-rr-camera"></i>
                                    </label>
                                </div>
                                <div class="v3-profile-hero-info">
                                    <h4 style="font-size: 14px;">Browser Tab Favicon</h4>
                                    <p style="margin-bottom: 10px;">Square icon for tabs &amp; bookmarks (ICO, PNG, SVG &mdash; max 2MB).</p>
                                    <div class="v3-profile-hero-actions">
                                        <label for="configFaviconInput" class="v3-btn-photo-upload">
                                            <i class="fi fi-rr-upload"></i> <span>Upload Favicon</span>
                                        </label>
                                        <input type="file" id="configFaviconInput" name="site_icon" accept="image/x-icon, image/png, image/svg+xml, image/webp, image/jpeg" style="display: none;">
                                        <button type="button" id="btnRemoveFavicon" class="v3-btn-photo-remove is-v3-hidden" title="Remove Favicon">
                                            <i class="fi fi-rr-trash"></i> <span>Remove</span>
                                        </button>
                                        <span id="configFaviconFileName" style="font-size: 11.5px; color: #64748b; font-weight: 500; width: 100%;"></span>
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
                        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="color" id="configThemeColorPicker" value="#4f46e5" style="width: 44px; height: 38px; border: 1.5px solid #cbd5e1; border-radius: 8px; cursor: pointer; padding: 2px; background: #ffffff;">
                                <input type="text" id="configThemeColorInput" name="site_theme_color" value="#4f46e5" class="v3-form-input" style="width: 110px; font-family: monospace; font-weight: 600; text-transform: uppercase;">
                            </div>

                            <!-- Color Presets -->
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Presets:</span>
                                <button type="button" class="theme-preset-btn" data-color="#4f46e5" style="width: 26px; height: 26px; border-radius: 50%; background: #4f46e5; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Indigo"></button>
                                <button type="button" class="theme-preset-btn" data-color="#0284c7" style="width: 26px; height: 26px; border-radius: 50%; background: #0284c7; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Sky Blue"></button>
                                <button type="button" class="theme-preset-btn" data-color="#10b981" style="width: 26px; height: 26px; border-radius: 50%; background: #10b981; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Emerald"></button>
                                <button type="button" class="theme-preset-btn" data-color="#d97706" style="width: 26px; height: 26px; border-radius: 50%; background: #d97706; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Amber"></button>
                                <button type="button" class="theme-preset-btn" data-color="#7c3aed" style="width: 26px; height: 26px; border-radius: 50%; background: #7c3aed; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Purple"></button>
                                <button type="button" class="theme-preset-btn" data-color="#0f172a" style="width: 26px; height: 26px; border-radius: 50%; background: #0f172a; border: 2px solid #ffffff; box-shadow: 0 0 0 1px #cbd5e1; cursor: pointer;" title="Slate"></button>
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
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Brand Identity Preview</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
                        <div class="v3-logo-frame" style="width: 80px; height: 45px; padding: 4px;">
                            <img id="sidebarLogoPreview" class="is-v3-hidden" src="" alt="Brand Logo">
                            <div id="sidebarLogoPlaceholder" class="v3-avatar-initials" style="background: #f1f5f9; color: #64748b; font-size: 16px;">
                                <i class="fi fi-rr-picture"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="sidebarSiteName" style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Website Name</h4>
                            <p id="sidebarSiteDomain" style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">yourdomain.com</p>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f8fafc; padding-top: 12px; display: flex; align-items: center; justify-content: space-between; font-size: 12.5px;">
                        <span style="color: #64748b;">Theme Accent:</span>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span id="sidebarThemeDot" style="width: 14px; height: 14px; border-radius: 50%; background: #4f46e5; display: inline-block;"></span>
                            <span id="sidebarThemeHex" style="font-family: monospace; font-weight: 600; color: #334155;">#4F46E5</span>
                        </div>
                    </div>
                </div>

                <!-- Pro-Tips Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-bulb"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">Branding Tip</strong>
                        Use a transparent PNG or SVG logo for crisp rendering on customer portals and quotation PDFs.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>