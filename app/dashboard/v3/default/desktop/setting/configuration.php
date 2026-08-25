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
            <a href="/dashboard/setting/profile/" class="btn-v3-action" data-tooltip="Manage Personal Profile">
                <i class="fi fi-rr-portrait"></i> <span>My Profile</span>
            </a>
            <a href="/dashboard/" class="btn-v3-action" data-tooltip="Back to Dashboard">
                <i class="fi fi-rr-arrow-left"></i> <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="settingConfigForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Website Identity -->
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
                                    <input type="text" id="configSiteName" name="site_name" class="v3-form-input" placeholder="e.g. Musafir India Trip" required autofocus>
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

                <!-- Card 2: SEO & Search Metadata -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-search-alt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>SEO Metadata &amp; Search Indexing</h3>
                            <p>Define global description snippet and search engine crawler instructions.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Site Description -->
                            <div class="v3-form-group span-2">
                                <label for="configSiteDescription" class="v3-form-label">
                                    Meta Description (Search Snippet)
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <textarea id="configSiteDescription" name="site_description" class="v3-form-textarea" rows="3" placeholder="Brief summary of your travel services, packages, and offerings for Google search..."></textarea>
                                </div>
                                <span class="v3-field-hint">Recommended length: 140–160 characters.</span>
                            </div>

                            <!-- Site Keywords -->
                            <div class="v3-form-group span-2">
                                <label for="configSiteKeyword" class="v3-form-label">
                                    Meta Keywords <span class="opt" style="font-weight: normal; color: #94a3b8;">(Comma-separated)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-tags input-icon"></i>
                                    <input type="text" id="configSiteKeyword" name="site_keyword" class="v3-form-input" placeholder="e.g. tour packages, travel agency, himachal tours, taxi booking">
                                </div>
                            </div>

                            <!-- Search Engine Indexing Toggle -->
                            <div class="v3-form-group span-2">
                                <label class="v3-form-label">
                                    Search Engine Indexing Visibility
                                </label>
                                <div style="display: flex; gap: 14px; margin-top: 4px; flex-wrap: wrap;">
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155; transition: all 0.15s ease;">
                                        <input type="radio" name="site_index" id="indexYes" value="index, follow" checked>
                                        <span><strong style="color: #10b981;">Allow Indexing</strong> (index, follow &mdash; Recommended for Live Sites)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #ffffff; cursor: pointer; font-size: 13px; font-weight: 500; color: #334155; transition: all 0.15s ease;">
                                        <input type="radio" name="site_index" id="indexNo" value="noindex, nofollow">
                                        <span><strong style="color: #ef4444;">Prevent Indexing</strong> (noindex, nofollow &mdash; Staging / Private)</span>
                                    </label>
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
                    <a href="/dashboard/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveConfig" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveConfigText">Save Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Search Snippet Preview & SEO Tips -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Search Result Preview Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Google Search Preview</span>
                        <span id="previewIndexBadge" class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Indexed</span>
                    </div>

                    <div style="font-family: Arial, sans-serif;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <div style="width: 20px; height: 20px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #4f46e5; font-weight: bold;">
                                <i class="fi fi-rr-globe"></i>
                            </div>
                            <span id="previewSearchDomain" style="font-size: 12px; color: #202124;">https://example.com</span>
                        </div>
                        <h4 id="previewSearchTitle" style="font-size: 16px; color: #1a0dab; margin: 0 0 4px 0; font-weight: 500; cursor: pointer; text-decoration: none;">
                            Website Title
                        </h4>
                        <p id="previewSearchSnippet" style="font-size: 13px; color: #4d5156; margin: 0; line-height: 1.45;">
                            Website description snippet will appear here as you configure metadata...
                        </p>
                    </div>
                </div>

                <!-- Pro-Tips Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-bulb"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">SEO Optimization Tip</strong>
                        Keep your Website Title concise (under 60 characters) and include your primary brand or service region for higher search rankings.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>