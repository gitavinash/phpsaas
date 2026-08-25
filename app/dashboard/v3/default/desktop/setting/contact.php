<div class="package-page-wrapper setting-page-wrapper" id="settingContactApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="fi fi-rr-phone-call" style="color: #ffffff;"></i>
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
                            <p>Official trade name, proprietor identity, and primary communication numbers.</p>
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

                            <!-- Owner / Proprietor Name -->
                            <div class="v3-form-group">
                                <label for="contactOwnerName" class="v3-form-label">
                                    Owner / Proprietor Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="contactOwnerName" name="owner_name" class="v3-form-input" placeholder="e.g. Ravindra Sharma">
                                </div>
                            </div>

                            <!-- Primary Mobile -->
                            <div class="v3-form-group">
                                <label for="contactMobile" class="v3-form-label">
                                    Customer Helpline Phone <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="contactMobile" name="mobile" class="v3-form-input" placeholder="e.g. 9958719998" required>
                                </div>
                                <span class="v3-field-error" id="errContactMobile"></span>
                            </div>

                            <!-- WhatsApp Number -->
                            <div class="v3-form-group">
                                <label for="contactWhatsapp" class="v3-form-label">
                                    Official WhatsApp Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <input type="tel" id="contactWhatsapp" name="whatsapp" class="v3-form-input" placeholder="e.g. 9958719998">
                                </div>
                            </div>

                            <!-- Short Intro -->
                            <div class="v3-form-group span-2">
                                <label for="contactIntro" class="v3-form-label">
                                    Company Tagline / Short Intro
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-document input-icon"></i>
                                    <textarea id="contactIntro" name="intro" class="v3-form-textarea" rows="2" placeholder="Brief 1-2 line intro about your travel services, packages, or enterprise offerings..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Support Email & Physical Address -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-marker"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Support Email &amp; Office Address</h3>
                            <p>Official support email addresses, alert notifications, and office location.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Public Email -->
                            <div class="v3-form-group">
                                <label for="contactEmail" class="v3-form-label">
                                    Public Support Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="contactEmail" name="email" class="v3-form-input" placeholder="info@example.com">
                                </div>
                            </div>

                            <!-- Notification Email -->
                            <div class="v3-form-group">
                                <label for="contactEmailNotify" class="v3-form-label">
                                    Alert &amp; Booking Notification Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-bell input-icon"></i>
                                    <input type="email" id="contactEmailNotify" name="email_notify" class="v3-form-input" placeholder="alerts@example.com">
                                </div>
                            </div>

                            <!-- Physical Address -->
                            <div class="v3-form-group span-2">
                                <label for="contactAddress" class="v3-form-label">
                                    Registered Office Address
                                </label>
                                <div class="v3-input-icon-wrap textarea-wrap">
                                    <i class="fi fi-rr-building input-icon"></i>
                                    <textarea id="contactAddress" name="address" class="v3-form-textarea" rows="2" placeholder="Complete street address, building/suite, city, state, and postal code..."></textarea>
                                </div>
                            </div>

                            <!-- Google Maps URL -->
                            <div class="v3-form-group span-2">
                                <label for="contactMapsUrl" class="v3-form-label">
                                    Google Maps Location Link
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-map-marker input-icon"></i>
                                    <input type="url" id="contactMapsUrl" name="maps_url" class="v3-form-input" placeholder="https://maps.google.com/?q=...">
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
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Live Contact Preview</span>
                        <span class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Public</span>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <h3 id="previewContactCompany" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Company Name</h3>
                        <p id="previewContactIntro" style="font-size: 12.5px; color: #64748b; margin: 0; line-height: 1.4;">
                            Company description and tagline will appear here...
                        </p>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #edf2f7; font-size: 12.5px;">
                        <div style="display: flex; align-items: center; gap: 10px; color: #334155;">
                            <i class="fi fi-rr-phone-call" style="color: #0284c7; font-size: 13px; width: 16px;"></i>
                            <span id="previewContactMobile" style="font-weight: 600;">+91 &mdash;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; color: #334155;">
                            <i class="fi fi-rr-comment-alt" style="color: #10b981; font-size: 13px; width: 16px;"></i>
                            <span id="previewContactWhatsapp" style="font-weight: 600;">+91 &mdash;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <i class="fi fi-rr-envelope" style="color: #6366f1; font-size: 13px; width: 16px;"></i>
                            <span id="previewContactEmail" style="font-weight: 500; overflow: hidden; text-overflow: ellipsis;">No email</span>
                        </div>
                        <div style="display: flex; align-items: flex-start; gap: 10px; color: #334155; line-height: 1.35;">
                            <i class="fi fi-rr-marker" style="color: #ea580c; font-size: 13px; width: 16px; margin-top: 2px;"></i>
                            <span id="previewContactAddress" style="font-size: 12px; color: #475569;">No registered address</span>
                        </div>
                    </div>
                </div>

                <!-- Info Tips Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">Customer Trust &amp; Conversion</strong>
                        Clear contact channels, WhatsApp support, and verified physical addresses significantly increase customer quote conversions.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
