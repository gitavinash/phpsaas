<?php
/**
 * V3 Add Company / Register Entity View
 * File: app/dashboard/v3/default/desktop/company/add.php
 * Fully compliant with V3 guidelines: external CSS/JS, Select2, AJAX data binding, and live card preview.
 */
?>
<div class="package-page-wrapper company-add-wrapper" id="addCompanyApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon company-header-badge">
                <i class="fi fi-rr-building"></i>
            </div>
            <div class="package-header-titles">
                <h2>Register New Company</h2>
                <p>Add a new registered legal company entity or branch office with tax compliance and official address details.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/company/list/" class="btn-v3-action" data-tooltip="Back to Companies Directory">
                <i class="fi fi-rr-arrow-left"></i> <span>Companies Directory</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Split Form Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="addCompanyForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Basic Company Information -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-building"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Entity Profile &amp; Overview</h3>
                            <p>Enter the legal registration name, company type, and official overview.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Company Name -->
                            <div class="v3-form-group col-span-2">
                                <label for="companyName" class="v3-form-label">
                                    Company Legal Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="companyName" name="name" class="v3-form-input" placeholder="e.g. Acme Holidays Private Limited" required autofocus>
                                </div>
                                <span class="v3-field-error" id="err_companyName"></span>
                            </div>

                            <!-- Company Type -->
                            <div class="v3-form-group">
                                <label for="companyType" class="v3-form-label">
                                    Entity / Registration Type <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-apps-sort input-icon"></i>
                                    <select id="companyType" name="companyType" class="v3-form-control">
                                        <option value="private" selected>Private Limited Company</option>
                                        <option value="public">Public Limited Company</option>
                                        <option value="partnerships">Partnership Firm</option>
                                        <option value="llp">Limited Liability Partnership (LLP)</option>
                                        <option value="opc">One Person Company (OPC)</option>
                                        <option value="proprietorship">Sole Proprietorship</option>
                                        <option value="section8">Section 8 Non-Profit</option>
                                        <option value="branch">Branch Office</option>
                                        <option value="clinic">Clinic / Retail Store</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="err_companyType"></span>
                            </div>

                            <!-- Company Code / Identifier -->
                            <div class="v3-form-group">
                                <label for="companyCode" class="v3-form-label">
                                    Company Identifier / Code
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-fingerprint input-icon"></i>
                                    <input type="text" id="companyCode" name="companyCode" class="v3-form-input" placeholder="e.g. COMP-HQ01 (Auto if blank)">
                                </div>
                                <span class="v3-field-error" id="err_companyCode"></span>
                            </div>

                            <!-- About / Description -->
                            <div class="v3-form-group col-span-2">
                                <label for="companyDescription" class="v3-form-label">
                                    About Company / Summary
                                </label>
                                <div class="v3-input-icon-wrap textarea-icon-wrap">
                                    <i class="fi fi-rr-document-signed input-icon"></i>
                                    <textarea id="companyDescription" name="description" class="v3-form-textarea" rows="3" placeholder="Brief description of operations, business scope, or branch highlights..."></textarea>
                                </div>
                                <span class="v3-field-error" id="err_companyDescription"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Official Contact Information -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-phone-call"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Official Contact Details</h3>
                            <p>Contact channels used for invoicing, vouchers, and customer communication.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Official Email -->
                            <div class="v3-form-group">
                                <label for="companyEmail" class="v3-form-label">
                                    Official Email Address <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="companyEmail" name="email" class="v3-form-input" placeholder="e.g. accounts@acme.com" required>
                                </div>
                                <span class="v3-field-error" id="err_companyEmail"></span>
                            </div>

                            <!-- Official Mobile / Phone -->
                            <div class="v3-form-group">
                                <label for="companyMobile" class="v3-form-label">
                                    Official Contact Mobile <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="companyMobile" name="mobile" class="v3-form-input" placeholder="10-digit mobile number" maxlength="15" required>
                                </div>
                                <span class="v3-field-error" id="err_companyMobile"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tax & Legal Identifiers -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-receipt"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Tax &amp; Legal Compliance</h3>
                            <p>GSTIN, PAN, and Corporate Identification Numbers (CIN) for compliance billing.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- GSTIN -->
                            <div class="v3-form-group">
                                <label for="companyGst" class="v3-form-label">
                                    GSTIN Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-receipt input-icon"></i>
                                    <input type="text" id="companyGst" name="gst" class="v3-form-input" placeholder="e.g. 07AAAAA0000A1Z5" maxlength="15">
                                </div>
                                <span class="v3-field-error" id="err_companyGst"></span>
                            </div>

                            <!-- PAN -->
                            <div class="v3-form-group">
                                <label for="companyPan" class="v3-form-label">
                                    Permanent Account Number (PAN)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <input type="text" id="companyPan" name="pan" class="v3-form-input" placeholder="e.g. ABCDE1234F" maxlength="10">
                                </div>
                                <span class="v3-field-error" id="err_companyPan"></span>
                            </div>

                            <!-- CIN / Reg No -->
                            <div class="v3-form-group col-span-2">
                                <label for="companyCin" class="v3-form-label">
                                    Registration No. / CIN
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="text" id="companyCin" name="cin" class="v3-form-input" placeholder="e.g. U74999DL2021PTC123456">
                                </div>
                                <span class="v3-field-error" id="err_companyCin"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Registered Office Address -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon sky">
                            <i class="fi fi-rr-marker"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Registered Office Address</h3>
                            <p>Official address location for legal notices and billing footers.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Street Address -->
                            <div class="v3-form-group col-span-2">
                                <label for="companyAddress" class="v3-form-label">
                                    Street Address / Suite / Building <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-marker input-icon"></i>
                                    <input type="text" id="companyAddress" name="address" class="v3-form-input" placeholder="e.g. Plot No. 45, Sector 18, Cyber City" required>
                                </div>
                                <span class="v3-field-error" id="err_companyAddress"></span>
                            </div>

                            <!-- State (Loaded via AJAX) -->
                            <div class="v3-form-group">
                                <label for="companyState" class="v3-form-label">
                                    State / Province <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-map input-icon"></i>
                                    <select id="companyState" name="state" class="v3-form-control" required>
                                        <option value="">Loading states...</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="err_companyState"></span>
                            </div>

                            <!-- City (Loaded dynamically on State change) -->
                            <div class="v3-form-group">
                                <label for="companyCity" class="v3-form-label">
                                    City <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-city input-icon"></i>
                                    <select id="companyCity" name="city" class="v3-form-control" required>
                                        <option value="">Select State first</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="err_companyCity"></span>
                            </div>

                            <!-- Pincode -->
                            <div class="v3-form-group">
                                <label for="companyPinCode" class="v3-form-label">
                                    PIN / Postal Code <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-map-marker input-icon"></i>
                                    <input type="text" id="companyPinCode" name="pinCode" class="v3-form-input" placeholder="e.g. 122002" maxlength="10" required>
                                </div>
                                <span class="v3-field-error" id="err_companyPinCode"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Branding & Logo -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon amber">
                            <i class="fi fi-rr-picture"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Branding &amp; Company Logo</h3>
                            <p>Upload square or rectangular PNG/JPG company logo for reports and invoices.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="logo-dropzone-box" id="logoDropZone">
                            <div class="logo-upload-prompt" id="logoUploadPrompt">
                                <i class="fi fi-rr-cloud-upload"></i>
                                <p><strong>Drag &amp; drop company logo here</strong> or <a href="javascript:void(0)" id="btnBrowseLogo">browse files</a></p>
                                <span>Supported formats: PNG, JPG, WEBP, SVG (Max 5MB)</span>
                            </div>
                            <div class="logo-preview-box" id="logoPreviewWrap" style="display: none;">
                                <img src="" alt="Company Logo Preview" id="logoPreviewImg" />
                                <div class="logo-preview-meta">
                                    <p><strong>Selected Logo</strong></p>
                                    <button type="button" class="btn-v3-outline-sm mt-1" id="btnRemoveLogo">
                                        <i class="fi fi-rr-cross-small"></i> Remove Logo
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="companyLogoInput" name="featureImage" accept="image/png, image/jpeg, image/webp, image/svg+xml" style="display: none;">
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/company/list/" class="v3-btn-secondary">
                        <i class="fi fi-rr-cross"></i> <span>Cancel</span>
                    </a>
                    <button type="submit" id="btnSubmitCompany" class="v3-btn-primary">
                        <i class="fi fi-rr-check"></i> <span>Save &amp; Register Entity</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Sticky Live Preview Card & Guidelines -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Live Entity Card Preview -->
                <div class="company-live-preview-card">
                    <div class="company-preview-banner"></div>
                    <div class="company-preview-content">
                        <div class="company-preview-avatar-row">
                            <div class="company-preview-avatar">
                                <span id="previewCompanyInitial">CO</span>
                                <img src="" alt="Logo" id="previewCompanyLogo" style="display: none;" />
                            </div>
                            <span class="company-type-pill company-badge-hq" id="previewCompanyType">Private Limited Company</span>
                        </div>

                        <div class="company-preview-title-box">
                            <h3 id="previewCompanyName">Company Legal Name</h3>
                            <div class="company-tax-row mt-1">
                                <span class="company-tax-pill" id="previewGstPill" style="display: none;"></span>
                                <span class="company-tax-pill" id="previewPanPill" style="display: none;"></span>
                            </div>
                        </div>

                        <div class="company-preview-meta-list">
                            <div class="company-preview-meta-item">
                                <i class="fi fi-rr-envelope"></i>
                                <span id="previewCompanyEmail">contact@company.com</span>
                            </div>
                            <div class="company-preview-meta-item">
                                <i class="fi fi-rr-phone-call"></i>
                                <span id="previewCompanyMobile">+91 00000 00000</span>
                            </div>
                            <div class="company-preview-meta-item">
                                <i class="fi fi-rr-marker"></i>
                                <span id="previewCompanyAddress">Address will appear here as you type...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance & Verification Tip -->
                <div class="v3-tips-box" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px; display: flex; gap: 10px; font-size: 12px; color: #166534; line-height: 1.4;">
                    <i class="fi fi-rr-shield-check" style="font-size: 18px; color: #16a34a; flex-shrink: 0; margin-top: 1px;"></i>
                    <div>
                        <strong style="display: block; margin-bottom: 2px; color: #14532d;">GST &amp; Legal Verification</strong>
                        Tax details (GSTIN and PAN) will appear on generated invoices, vouchers, and customer quotations.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>