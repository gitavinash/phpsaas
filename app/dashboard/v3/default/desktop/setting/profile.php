<div class="package-page-wrapper setting-page-wrapper" id="settingProfileApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <i class="fi fi-rr-portrait" style="color: #ffffff;"></i>
            </div>
            <div class="package-header-titles">
                <h2>Account &amp; Personal Profile</h2>
                <p>Manage your account credentials, official display name, contact channels, and system bio.</p>
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
            <form id="settingProfileForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Personal Details -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-user"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Personal Information</h3>
                            <p>Update your legal name and public display profile identity.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <!-- Profile Photo Hero Box -->
                        <div class="v3-profile-hero-card">
                            <div class="v3-avatar-wrapper">
                                <div class="v3-avatar-frame">
                                    <img id="profileImagePreview" class="v3-avatar-img is-v3-hidden" src="" alt="Profile Photo">
                                    <div id="profileImageInitials" class="v3-avatar-initials">U</div>
                                </div>
                                <label for="profileImageInput" class="v3-avatar-edit-badge" title="Change photo">
                                    <i class="fi fi-rr-camera"></i>
                                </label>
                            </div>
                            <div class="v3-profile-hero-info">
                                <h4 id="heroProfileName">Profile Photo</h4>
                                <p>Upload a clear square portrait (JPG, PNG or WEBP &mdash; max 5MB).</p>
                                <div class="v3-profile-hero-actions">
                                    <label for="profileImageInput" class="v3-btn-photo-upload">
                                        <i class="fi fi-rr-upload"></i> <span>Upload Photo</span>
                                    </label>
                                    <input type="file" id="profileImageInput" name="profile_image" accept="image/png, image/jpeg, image/webp" style="display: none;">
                                    <button type="button" id="btnRemoveProfileImage" class="v3-btn-photo-remove is-v3-hidden">
                                        <i class="fi fi-rr-trash"></i> <span>Remove</span>
                                    </button>
                                    <span id="profileImageFileName" style="font-size: 12px; color: #64748b; font-weight: 500;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="v3-form-grid">
                            <!-- First Name -->
                            <div class="v3-form-group">
                                <label for="profileFirstName" class="v3-form-label">
                                    First Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="profileFirstName" name="first_name" class="v3-form-input" placeholder="First name" required autofocus>
                                </div>
                                <span class="v3-field-error" id="errProfileFirstName"></span>
                            </div>

                            <!-- Last Name -->
                            <div class="v3-form-group">
                                <label for="profileLastName" class="v3-form-label">
                                    Last Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-portrait input-icon"></i>
                                    <input type="text" id="profileLastName" name="last_name" class="v3-form-input" placeholder="Last name">
                                </div>
                            </div>

                            <!-- Public Display Name -->
                            <div class="v3-form-group">
                                <label for="profileDisplayName" class="v3-form-label">
                                    Display Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="profileDisplayName" name="display_name" class="v3-form-input" placeholder="e.g. Ravindra Sharma">
                                </div>
                            </div>

                            <!-- Bio / Designation -->
                            <div class="v3-form-group">
                                <label for="profileBio" class="v3-form-label">
                                    Role Title / Bio
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-briefcase input-icon"></i>
                                    <input type="text" id="profileBio" name="bio" class="v3-form-input" placeholder="e.g. Senior Operations Manager">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Contact Information -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-phone-call"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Contact Details</h3>
                            <p>Primary and alternate communication channels for notifications and authentication.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Primary Mobile -->
                            <div class="v3-form-group">
                                <label for="profileMobile" class="v3-form-label">
                                    Primary Mobile Number <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="profileMobile" name="mobile" class="v3-form-input" placeholder="10-digit mobile number" maxlength="10" required>
                                </div>
                                <span class="v3-field-error" id="errProfileMobile"></span>
                            </div>

                            <!-- Alternate Mobile -->
                            <div class="v3-form-group">
                                <label for="profileAltMobile" class="v3-form-label">
                                    WhatsApp / Alt Mobile
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-comment-alt input-icon"></i>
                                    <input type="tel" id="profileAltMobile" name="alt_mobile" class="v3-form-input" placeholder="Secondary mobile number" maxlength="10">
                                </div>
                            </div>

                            <!-- Primary Email -->
                            <div class="v3-form-group">
                                <label for="profileEmail" class="v3-form-label">
                                    Primary Account Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="profileEmail" class="v3-form-input" readonly placeholder="Loading email...">
                                </div>
                            </div>

                            <!-- Alternate Email -->
                            <div class="v3-form-group">
                                <label for="profileAltEmail" class="v3-form-label">
                                    Alternate Email
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-at input-icon"></i>
                                    <input type="email" id="profileAltEmail" name="alt_email" class="v3-form-input" placeholder="e.g. personal@example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Account & Authentication Info -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Account &amp; System Clearance</h3>
                            <p>Current account privileges and registered login credentials.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Username -->
                            <div class="v3-form-group">
                                <label for="profileUsername" class="v3-form-label">
                                    System Username
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user-tag input-icon"></i>
                                    <input type="text" id="profileUsername" class="v3-form-input" readonly placeholder="Loading username...">
                                </div>
                            </div>

                            <!-- Role Info -->
                            <div class="v3-form-group">
                                <label for="profileRoleDisplay" class="v3-form-label">
                                    Access Role Clearance
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <input type="text" id="profileRoleDisplay" class="v3-form-input" readonly placeholder="Loading role...">
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
                    <button type="submit" id="btnSaveProfile" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveProfileText">Update Profile</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Profile Preview & Security Sidebar -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Live Profile Preview Card -->
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Profile Preview</span>
                        <span class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 16px;">
                        <div class="v3-avatar-frame" style="width: 72px; height: 72px; margin-bottom: 12px;">
                            <img id="previewProfileImg" class="v3-avatar-img is-v3-hidden" src="" alt="Profile">
                            <div id="previewProfileAvatar" class="v3-avatar-initials" style="font-size: 24px;">U</div>
                        </div>
                        <h3 id="previewProfileName" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Loading...</h3>
                        <span id="previewProfileRole" class="badge-service-state badge-role-admin" style="font-size: 11.5px; margin-top: 2px;">
                            <i class="fi fi-rr-shield-check"></i> Administrator
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; background: #f8fafc; border-radius: 10px; padding: 12px; border: 1px solid #edf2f7; font-size: 12.5px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155;">
                            <i class="fi fi-rr-phone-call" style="color: #10b981; font-size: 12px; width: 14px;"></i>
                            <span id="previewProfileMobile" style="font-weight: 500;">+91 &mdash;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <i class="fi fi-rr-envelope" style="color: #6366f1; font-size: 12px; width: 14px;"></i>
                            <span id="previewProfileEmail" style="font-weight: 500; overflow: hidden; text-overflow: ellipsis;">No email</span>
                        </div>
                    </div>
                </div>

                <!-- Account Security Tips Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">Account Security</strong>
                        Your profile name is displayed on all customer itinerary quotes and team chat notifications. Keep your contact numbers up to date.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>