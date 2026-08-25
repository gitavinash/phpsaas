<div class="package-page-wrapper setting-page-wrapper" id="settingSeoApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <i class="fi fi-rr-search-alt" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2 style="margin: 0;">SEO Metadata &amp; Search Indexing</h2>
                <p style="margin-top: 4px;">Define global search snippets, keywords, and search engine crawler instructions for your public website.</p>
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
            <form id="settingSeoForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Search Engine Meta Tags -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-globe"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>SEO Metadata &amp; Search Snippets</h3>
                            <p>Global meta description, target keywords, and crawler index visibility.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Site Meta Title -->
                            <div class="v3-form-group span-2">
                                <label for="siteMetaTitle" class="v3-form-label">
                                    Meta Title <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <input type="text" id="siteMetaTitle" name="site_name" class="v3-form-input" placeholder="e.g. Best Holiday Packages &amp; Customized Tour Itineraries" required>
                                </div>
                            </div>

                            <!-- Meta Description -->
                            <div class="v3-form-group span-2">
                                <label for="siteMetaDesc" class="v3-form-label">
                                    Meta Description <span class="opt" style="font-weight: normal; color: #94a3b8;">(Search Snippet)</span>
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <textarea id="siteMetaDesc" name="site_description" class="v3-form-textarea" rows="3" placeholder="Brief summary of your travel services, holiday packages, and offerings for Google search..."></textarea>
                                </div>
                                <span class="v3-field-hint">Recommended length: 140–160 characters.</span>
                            </div>

                            <!-- Meta Keywords -->
                            <div class="v3-form-group span-2">
                                <label for="siteMetaKw" class="v3-form-label">
                                    Meta Keywords <span class="opt" style="font-weight: normal; color: #94a3b8;">(Comma-separated)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-tags input-icon"></i>
                                    <input type="text" id="siteMetaKw" name="site_keyword" class="v3-form-input" placeholder="e.g. tour packages, travel agency, himachal tours, taxi booking">
                                </div>
                            </div>

                            <!-- Search Engine Indexing Visibility -->
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

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveSeo" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveSeoText">Save SEO Metadata</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Google Search Result Simulator -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Search Result Preview Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Google Search Preview</span>
                        <span class="v3-status-badge status-active" style="font-size: 10px;">Desktop SERP</span>
                    </div>

                    <div style="font-family: Arial, sans-serif;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #202124; margin-bottom: 4px;">
                            <div style="width: 18px; height: 18px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                <i class="fi fi-rr-globe" style="color: #4f46e5;"></i>
                            </div>
                            <span id="previewSeoDomain" style="color: #202124;">yourdomain.com</span>
                        </div>
                        <div id="previewSeoTitle" style="font-size: 16px; color: #1a0dab; font-weight: 500; line-height: 1.3; margin-bottom: 4px; cursor: pointer;">
                            Best Holiday Packages &amp; Tour Itineraries
                        </div>
                        <div id="previewSeoDesc" style="font-size: 12.5px; color: #4d5156; line-height: 1.45;">
                            Explore top rated customized holiday tour packages, luxury hotel stays, guided itineraries, and unforgettable travel experiences.
                        </div>
                    </div>
                </div>

                <!-- SEO Best Practices Checklist -->
                <div class="v3-card-section" style="padding: 16px;">
                    <h4 style="font-size: 12.5px; font-weight: 700; color: #334155; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.04em;">
                        SEO Best Practices
                    </h4>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b; line-height: 1.6;">
                        <li>Keep titles under <strong>60 characters</strong> for full visibility on search engines.</li>
                        <li>Descriptions should be actionable and between <strong>140–160 characters</strong>.</li>
                        <li>Ensure <strong>Allow Indexing</strong> is active when your website is live to the public.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
