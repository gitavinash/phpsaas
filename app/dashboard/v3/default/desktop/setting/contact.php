<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
?>
<div class="package-page-wrapper setting-page-wrapper" id="settingContactApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon emerald">
                <i class="fi fi-rr-phone-call"></i>
            </div>
            <div class="package-header-titles">
                <h2>Public Contact &amp; Business Profile</h2>
                <p>Manage customer support helplines, WhatsApp channels, physical office address, and social links.</p>
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
            <form id="settingContactForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Business Identity & Helplines -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-building"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Business Identity &amp; Helplines</h3>
                            <p>Official trade name, contact person, helpline numbers, support email, and website domain.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Company Name -->
                            <div class="v3-form-group">
                                <label for="contactCompanyName" class="v3-form-label">
                                    Company / Trade Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <input type="text" id="contactCompanyName" name="company_name" class="v3-form-input" placeholder="e.g. Musafir India Trip" required autofocus>
                                </div>
                                <span class="v3-field-error" id="errContactCompanyName"></span>
                            </div>

                            <!-- Owner / Contact Person -->
                            <div class="v3-form-group">
                                <label for="contactContactPerson" class="v3-form-label">
                                    Owner / Contact Person
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="contactContactPerson" name="company_contact_person" class="v3-form-input" placeholder="e.g. Ravindra Sharma">
                                </div>
                            </div>

                            <!-- Customer Helpline Phone -->
                            <div class="v3-form-group">
                                <label for="contactSiteMobile" class="v3-form-label">
                                    Customer Helpline No. <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="contactSiteMobile" name="site_mobile" class="v3-form-input" placeholder="e.g. 9958719998" required>
                                </div>
                                <span class="v3-field-error" id="errContactSiteMobile"></span>
                            </div>

                            <!-- Official WhatsApp Number -->
                            <div class="v3-form-group">
                                <label for="contactCompanyWhatsappNumber" class="v3-form-label">
                                    Official WhatsApp No.
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <input type="tel" id="contactCompanyWhatsappNumber" name="company_whatsapp_number" class="v3-form-input" placeholder="e.g. 9958719998">
                                </div>
                            </div>

                            <!-- Support Email -->
                            <div class="v3-form-group">
                                <label for="contactSiteEmail" class="v3-form-label">
                                    Support Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="contactSiteEmail" name="site_email" class="v3-form-input" placeholder="info@example.com">
                                </div>
                            </div>

                            <!-- Website Domain -->
                            <div class="v3-form-group">
                                <label for="contactCompanySiteDomain" class="v3-form-label">
                                    Website Domain
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-globe input-icon"></i>
                                    <input type="text" id="contactCompanySiteDomain" name="company_site_domain" class="v3-form-input" placeholder="e.g. musafirindiatrip.com">
                                </div>
                                <span class="v3-field-error" id="errContactCompanySiteDomain"></span>
                            </div>

                            <!-- Short Intro -->
                            <div class="v3-form-group span-2">
                                <label for="contactCompanyIntro" class="v3-form-label">
                                    Company Tagline / Short Intro
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <textarea id="contactCompanyIntro" name="company_intro" class="v3-form-textarea" rows="2" placeholder="Brief 1-2 line intro about your travel services, packages, or enterprise offerings..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Registered Office & Location -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-marker"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Registered Office &amp; Location</h3>
                            <p>Official registered office address and Google Maps location.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Physical Address -->
                            <div class="v3-form-group span-2">
                                <label for="contactCompanyAddress" class="v3-form-label">
                                    Registered Office Address
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <textarea id="contactCompanyAddress" name="company_address" class="v3-form-textarea" rows="2" placeholder="Complete street address, building/suite, city, state, and postal code..."></textarea>
                                </div>
                            </div>

                            <!-- Google Maps URL -->
                            <div class="v3-form-group span-2">
                                <label for="contactCompanyGmap" class="v3-form-label">
                                    Google Maps Location Link
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-map-marker input-icon"></i>
                                    <input type="url" id="contactCompanyGmap" name="company_google_map" class="v3-form-input" placeholder="https://maps.google.com/?q=...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Social Media Profiles -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon rose">
                            <i class="fi fi-rr-share"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Social Media Channels</h3>
                            <p>Connect your verified brand profiles for customer outreach and credibility.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Facebook -->
                            <div class="v3-form-group">
                                <label for="contactFacebook" class="v3-form-label">
                                    Facebook Page
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-globe input-icon"></i>
                                    <input type="url" id="contactFacebook" name="facebook" class="v3-form-input" placeholder="https://facebook.com/yourbrand">
                                </div>
                            </div>

                            <!-- Instagram -->
                            <div class="v3-form-group">
                                <label for="contactInstagram" class="v3-form-label">
                                    Instagram Profile
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-camera input-icon"></i>
                                    <input type="url" id="contactInstagram" name="instagram" class="v3-form-input" placeholder="https://instagram.com/yourbrand">
                                </div>
                            </div>

                            <!-- X (Twitter) -->
                            <div class="v3-form-group">
                                <label for="contactX" class="v3-form-label">
                                    X (Formerly Twitter)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-share input-icon"></i>
                                    <input type="url" id="contactX" name="x" class="v3-form-input" placeholder="https://x.com/yourbrand">
                                </div>
                            </div>

                            <!-- LinkedIn -->
                            <div class="v3-form-group">
                                <label for="contactLinkedin" class="v3-form-label">
                                    LinkedIn Company Page
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-briefcase input-icon"></i>
                                    <input type="url" id="contactLinkedin" name="linkedin" class="v3-form-input" placeholder="https://linkedin.com/company/yourbrand">
                                </div>
                            </div>

                            <!-- YouTube -->
                            <div class="v3-form-group span-2">
                                <label for="contactYoutube" class="v3-form-label">
                                    YouTube Channel
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-play-alt input-icon"></i>
                                    <input type="url" id="contactYoutube" name="youtube" class="v3-form-input" placeholder="https://youtube.com/@yourchannel">
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
                    <button type="submit" id="btnSaveContact" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveContactText">Save Public Contact Details</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Contact Card Preview -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Public Contact Card -->
                <div class="v3-live-preview-card">
                    <div class="v3-live-preview-header">
                        <span class="v3-live-preview-label">Live Contact Preview</span>
                        <span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Public</span>
                    </div>

                    <div class="v3-preview-company-info">
                        <h3 id="previewContactCompany" class="v3-live-preview-name">Company Name</h3>
                        <p id="previewContactIntro" class="v3-live-preview-domain">
                            Company description and tagline will appear here...
                        </p>
                    </div>

                    <div class="v3-contact-preview-list">
                        <div class="v3-contact-preview-item">
                            <i class="fi fi-rr-globe brand-domain"></i>
                            <span id="previewContactDomain">No website domain</span>
                        </div>
                        <div class="v3-contact-preview-item">
                            <i class="fi fi-rr-phone-call brand-phone"></i>
                            <span id="previewContactMobile">+91 &mdash;</span>
                        </div>
                        <div class="v3-contact-preview-item">
                            <i class="fi fi-rr-comment-alt brand-wa"></i>
                            <span id="previewContactWhatsapp">+91 &mdash;</span>
                        </div>
                        <div class="v3-contact-preview-item">
                            <i class="fi fi-rr-envelope brand-email"></i>
                            <span id="previewContactEmail">No email</span>
                        </div>
                        <div class="v3-contact-preview-item align-start">
                            <i class="fi fi-rr-marker brand-map"></i>
                            <span id="previewContactAddress">No registered address</span>
                        </div>
                    </div>
                </div>

                <!-- Info Tips Box -->
                <div class="v3-tips-box emerald">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong>Customer Trust &amp; Conversion</strong>
                        Clear contact channels, WhatsApp support, and verified physical addresses significantly increase customer quote conversions.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>