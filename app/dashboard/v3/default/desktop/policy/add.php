<?php
/**
 * V3 Add New Policy View
 * File: app/dashboard/v3/default/desktop/policy/add.php
 * Fully compliant with V3 SPA guidelines, external assets, Select2 dropdowns, and REST API data binding.
 */
?>
<div class="package-page-wrapper policy-add-wrapper" id="policyAddApp">
    <!-- Header Section -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-document-signed"></i>
            </div>
            <div class="package-header-titles">
                <h2>Add New Policy</h2>
                <p>Create legal terms of service, privacy policy, refund &amp; cancellation rules, or company disclaimers.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/policy/list/" class="btn-v3-outline-sm" data-tooltip="Back to policies list">
                <i class="fi fi-rr-arrow-left"></i> <span>Policy List</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Split Form Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="policyForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Document Basics -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-document"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Document Profile &amp; URL</h3>
                            <p>Enter the policy title and permanent website URL slug.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Policy Title -->
                            <div class="v3-form-group col-span-2 span-2">
                                <label for="policyTitle" class="v3-form-label">
                                    Policy Title <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-document-signed input-icon"></i>
                                    <input type="text" id="policyTitle" name="title" class="v3-form-input" placeholder="e.g. Terms of Service or Privacy Policy" required autofocus>
                                </div>
                                <span class="v3-field-error" id="err_policyTitle"></span>
                            </div>

                            <!-- URL Slug -->
                            <div class="v3-form-group">
                                <label for="policySlug" class="v3-form-label">
                                    URL Slug
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-link-alt input-icon"></i>
                                    <input type="text" id="policySlug" name="slug" class="v3-form-input" placeholder="terms-of-service">
                                </div>
                                <span class="v3-field-hint">Auto-generated from title. Visible at <code id="slugPreviewText">/terms-of-service/</code></span>
                                <span class="v3-field-error" id="err_policySlug"></span>
                            </div>

                            <!-- Status -->
                            <div class="v3-form-group">
                                <label for="policyStatus" class="v3-form-label">
                                    Publishing Status
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-check-circle input-icon"></i>
                                    <select id="policyStatus" name="status" class="v3-form-control selectJS2">
                                        <option value="1" selected>Active &amp; Published</option>
                                        <option value="0">Draft / Inactive</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="err_policyStatus"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Policy Terms Content -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-file-edit"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Policy Clauses &amp; Terms Body</h3>
                            <p>Write or paste the full policy text, clauses, disclaimers, and legal conditions.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-group span-2">
                            <label for="policyDescription" class="v3-form-label">
                                Policy Content <span class="required">*</span>
                            </label>
                            <textarea id="policyDescription" name="description" class="v3-form-textarea" rows="12" placeholder="Enter complete policy clauses, cancellation timelines, refund rules, and terms of service..." required></textarea>
                            <div class="v3-textarea-footer-meta">
                                <span id="policyCharCount">0 characters</span>
                                <span id="policyWordCount">0 words</span>
                            </div>
                            <span class="v3-field-error" id="err_policyDescription"></span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Featured Banner Image -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon sky">
                            <i class="fi fi-rr-picture"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Header Cover &amp; Banner Image</h3>
                            <p>Upload a banner or cover illustration displayed at the top of the policy page.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="logo-dropzone-box" id="policyDropZone">
                            <div class="logo-upload-prompt" id="policyUploadPrompt">
                                <i class="fi fi-rr-cloud-upload"></i>
                                <p><strong>Drag &amp; drop policy banner image here</strong> or <a href="javascript:void(0)" id="btnBrowsePolicyBanner">browse files</a></p>
                                <span>Supported formats: PNG, JPG, WEBP, GIF (Max 5MB)</span>
                            </div>
                            <div class="logo-preview-box is-hidden" id="policyPreviewWrap">
                                <img src="" alt="Policy Banner Preview" id="policyPreviewImg" class="v3-preview-banner-img" />
                                <div class="logo-preview-meta">
                                    <p><strong>Policy Banner</strong></p>
                                    <button type="button" class="btn-v3-outline-sm mt-1" id="btnRemovePolicyBanner">
                                        <i class="fi fi-rr-cross-small"></i> Remove Image
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="policyBannerInput" name="featureImage" accept="image/png, image/jpeg, image/webp, image/gif" class="d-none">
                        </div>
                    </div>
                </div>

                <!-- Card 4: SEO & Meta Tags -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-left">
                            <div class="v3-card-header-icon purple">
                                <i class="fi fi-rr-search-alt"></i>
                            </div>
                            <div class="v3-card-header-text">
                                <h3>Search Engine Optimization (SEO)</h3>
                                <p>Custom meta tags for search engines and social media previews.</p>
                            </div>
                        </div>
                        <button type="button" class="v3-card-action-btn" id="btnAutoSuggestSeo" title="AI suggest meta title, description, and keywords from policy content">
                            <i class="fi fi-rr-sparkles"></i> <span>Suggest Meta</span>
                        </button>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Meta Title -->
                            <div class="v3-form-group col-span-2 span-2">
                                <div class="v3-label-row">
                                    <label for="policyMetaTitle" class="v3-form-label">
                                        SEO Meta Title
                                    </label>
                                    <span id="metaTitleCharCount" class="v3-field-char-count">0 / 60 chars</span>
                                </div>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-heading input-icon"></i>
                                    <input type="text" id="policyMetaTitle" name="meta_title" class="v3-form-input" placeholder="e.g. Terms of Service | Company Legal Policies">
                                </div>
                            </div>

                            <!-- Meta Description -->
                            <div class="v3-form-group col-span-2 span-2">
                                <div class="v3-label-row">
                                    <label for="policyMetaDescription" class="v3-form-label">
                                        SEO Meta Description
                                    </label>
                                    <span id="metaDescCharCount" class="v3-field-char-count">0 / 160 chars</span>
                                </div>
                                <div class="v3-input-icon-wrap textarea-icon-wrap">
                                    <i class="fi fi-rr-text input-icon"></i>
                                    <textarea id="policyMetaDescription" name="meta_description" class="v3-form-textarea" rows="2" placeholder="Brief summary of policy clauses for Google search snippets..."></textarea>
                                </div>
                            </div>

                            <!-- Keywords -->
                            <div class="v3-form-group col-span-2 span-2">
                                <label for="policyMetaKeywords" class="v3-form-label">
                                    Meta Keywords
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-tags input-icon"></i>
                                    <input type="text" id="policyMetaKeywords" name="meta_keywords" class="v3-form-input" placeholder="terms, privacy policy, refund policy, legal clauses">
                                </div>
                                <span class="v3-field-hint">Comma-separated key phrases relevant to this policy.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/policy/list/" class="v3-btn-secondary">
                        <i class="fi fi-rr-cross"></i> <span>Cancel</span>
                    </a>
                    <button type="submit" id="btnSubmitPolicy" class="v3-btn-primary">
                        <i class="fi fi-rr-check"></i> <span>Publish Policy</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Sticky Live Preview Card & Legal Guidance -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Live Policy Card Preview -->
                <div class="company-live-preview-card">
                    <div class="company-preview-banner indigo"></div>
                    <div class="company-preview-content">
                        <div class="company-preview-avatar-row">
                            <div class="company-preview-avatar indigo">
                                <i class="fi fi-rr-document-signed"></i>
                            </div>
                            <span class="company-type-pill company-badge-hq" id="previewPolicyStatusBadge">Active &amp; Published</span>
                        </div>

                        <div class="company-preview-title-box">
                            <h3 id="previewPolicyTitle">Policy Title</h3>
                            <div class="mt-1">
                                <span class="company-tax-pill"><i class="fi fi-rr-link-alt"></i> <span id="previewPolicyUrl">/policy-url/</span></span>
                            </div>
                        </div>

                        <div class="company-preview-meta-list">
                            <div class="company-preview-meta-item">
                                <i class="fi fi-rr-clock"></i>
                                <span id="previewReadTime">1 min read</span>
                            </div>
                            <div class="company-preview-meta-item">
                                <i class="fi fi-rr-align-left"></i>
                                <span id="previewExcerpt">Policy summary and clauses will appear here...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Google Search Snippet Preview -->
                <div class="v3-serp-preview-card">
                    <div class="v3-serp-preview-header">
                        <i class="fi fi-rr-globe"></i> Google Search Snippet Preview
                    </div>
                    <div class="v3-serp-preview-body">
                        <div class="v3-serp-breadcrumb" id="serpBreadcrumb">
                            <span>https://example.com</span> <span class="v3-serp-breadcrumb-sep">›</span> <span id="serpSlugPreview">policy</span>
                        </div>
                        <h4 id="serpTitlePreview" class="v3-serp-title">Policy Title | Legal Terms</h4>
                        <p id="serpSnippetPreview" class="v3-serp-snippet">Policy summary and clauses will appear here in Google search engine result snippets...</p>
                    </div>
                </div>

                <!-- Compliance Tip Box -->
                <div class="v3-tips-box blue">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong>Mandatory Compliance</strong>
                        Payment gateways (e.g. Razorpay, Cashfree, PayU) mandate standard Privacy Policy, Terms of Service, and Refund/Cancellation Policies before account activation.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>