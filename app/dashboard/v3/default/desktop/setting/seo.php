<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingSeoApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon blue">
                <i class="fi fi-rr-search-alt"></i>
            </div>
            <div class="package-header-titles">
                <h2>SEO Metadata &amp; Search Indexing</h2>
                <p>Define global search snippets, keywords, and search engine crawler instructions for your public website.</p>
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
                                    Meta Description <span class="opt v3-label-opt">(Search Snippet)</span>
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
                                    Meta Keywords <span class="opt v3-label-opt">(Comma-separated)</span>
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
                                <div class="v3-radio-group-wrap">
                                    <label class="v3-radio-option-card">
                                        <input type="radio" name="site_index" id="indexYes" value="index, follow" checked>
                                        <span><strong class="color-emerald">Allow Indexing</strong> (index, follow &mdash; Recommended for Live Sites)</span>
                                    </label>
                                    <label class="v3-radio-option-card">
                                        <input type="radio" name="site_index" id="indexNo" value="noindex, nofollow">
                                        <span><strong class="color-rose">Prevent Indexing</strong> (noindex, nofollow &mdash; Staging / Private)</span>
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
                <div class="v3-serp-preview-card">
                    <div class="v3-serp-preview-header">
                        <i class="fi fi-rr-search-alt"></i>
                        <span>Google Search Preview</span>
                        <span class="v3-status-badge status-active badge-xs ms-auto">Desktop SERP</span>
                    </div>

                    <div class="v3-serp-preview-body">
                        <div class="v3-serp-breadcrumb">
                            <span id="previewSeoDomain">yourdomain.com</span>
                            <span class="v3-serp-breadcrumb-sep">&rsaquo;</span>
                        </div>
                        <div id="previewSeoTitle" class="v3-serp-title">
                            Best Holiday Packages &amp; Tour Itineraries
                        </div>
                        <p id="previewSeoDesc" class="v3-serp-snippet">
                            Explore top rated customized holiday tour packages, luxury hotel stays, guided itineraries, and unforgettable travel experiences.
                        </p>
                    </div>
                </div>

                <!-- SEO Best Practices Checklist -->
                <div class="v3-card-section v3-checklist-card">
                    <h4 class="v3-checklist-title">
                        SEO Best Practices
                    </h4>
                    <ul class="v3-checklist-list">
                        <li>Keep titles under <strong>60 characters</strong> for full visibility on search engines.</li>
                        <li>Descriptions should be actionable and between <strong>140–160 characters</strong>.</li>
                        <li>Ensure <strong>Allow Indexing</strong> is active when your website is live to the public.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
