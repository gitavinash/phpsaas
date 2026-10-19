<div class="package-page-wrapper user-form-wrapper" id="addUserApp">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-user-add"></i>
            </div>
            <div class="package-header-titles">
                <h2>Add Team Member</h2>
                <p>Register a new employee account, configure role access privileges, and setup login credentials.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/user/" class="btn-v3-action" data-tooltip="Back to Team Directory">
                <i class="fi fi-rr-arrow-left"></i> <span>Team Directory</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="addUserForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Personal Information -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-user"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Personal Information</h3>
                            <p>Enter the legal first and last name of the team member.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- First Name -->
                            <div class="v3-form-group">
                                <label for="firstName" class="v3-form-label">
                                    First Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="firstName" name="first_name" class="v3-form-input" placeholder="e.g. Rahul" required autofocus>
                                </div>
                                <span class="v3-field-error" id="errFirstName"></span>
                            </div>

                            <!-- Last Name -->
                            <div class="v3-form-group">
                                <label for="lastName" class="v3-form-label">
                                    Last Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-portrait input-icon"></i>
                                    <input type="text" id="lastName" name="last_name" class="v3-form-input" placeholder="e.g. Sharma">
                                </div>
                                <span class="v3-field-error" id="errLastName"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Contact & Authentication -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-phone-call"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Contact &amp; Login Credentials</h3>
                            <p>Mobile and email are used for two-factor authentication and official CRM access.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Primary Mobile -->
                            <div class="v3-form-group">
                                <label for="userMobile" class="v3-form-label">
                                    Primary Mobile Number <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="userMobile" name="mobile" class="v3-form-input" placeholder="10-digit mobile number" maxlength="10" required>
                                </div>
                                <span class="v3-field-error" id="errUserMobile"></span>
                            </div>

                            <!-- Official Email -->
                            <div class="v3-form-group">
                                <label for="userEmail" class="v3-form-label">
                                    Official Email Address <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="userEmail" name="email" class="v3-form-input" placeholder="e.g. rahul@example.com" required>
                                </div>
                                <span class="v3-field-error" id="errUserEmail"></span>
                            </div>

                            <!-- Custom Password -->
                            <div class="v3-form-group span-2">
                                <label for="userPassword" class="v3-form-label">
                                    Account Password <span class="opt v3-label-opt">(Optional &mdash; defaults to mobile number if empty)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-key input-icon"></i>
                                    <input type="password" id="userPassword" name="password" class="v3-form-input" placeholder="Create a secure account password" autocomplete="new-password">
                                </div>
                                <span class="v3-field-error" id="errUserPassword"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Role & System Privileges -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Role &amp; System Permissions</h3>
                            <p>Configure dashboard privileges and access scope for this team member.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Role Dropdown -->
                            <div class="v3-form-group">
                                <label for="userRole" class="v3-form-label">
                                    Assigned System Role <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-shield-check input-icon"></i>
                                    <select id="userRole" name="userRole" class="v3-form-select selectJS2" required>
                                        <option value="">Select Role</option>
                                        <option value="administrator">Administrator (Full Access)</option>
                                        <option value="agent" selected>Sales Agent</option>
                                        <option value="operations">Operations Manager</option>
                                        <option value="accountant">Accounts &amp; Finance</option>
                                        <option value="support">Customer Support</option>
                                    </select>
                                </div>
                                <span class="v3-field-error" id="errUserRole"></span>
                            </div>

                            <!-- Bio / Title Note -->
                            <div class="v3-form-group">
                                <label for="userBio" class="v3-form-label">
                                    Designation / Note <span class="opt v3-label-opt">(Optional)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="userBio" name="bio" class="v3-form-input" placeholder="e.g. Senior Tour Specialist">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/user/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="button" class="btn-wa-buy-license btn-contact-aleya btn-upgrade d-none" id="btnUpgradeUserLicenseForm" data-modal-title="Team Member Limit Reached" data-modal-notice="You have reached your team member license limit. Please contact Aleya to upgrade your plan and add more team members.">
                        <i class="fi fi-rr-sparkles"></i> <span>Buy More User Licenses</span>
                    </button>
                    <button type="submit" id="btnSubmitUser" class="v3-btn-primary">
                        <i class="fi fi-rr-check"></i> <span id="btnSubmitText">Create Team Member</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Member Preview & Security Guidelines -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <!-- Member Live Preview Card -->
                <div class="v3-user-preview-card">
                    <div class="v3-user-preview-header">
                        <span class="v3-user-preview-title">Live Member Preview</span>
                        <span class="v3-status-badge status-active badge-xs"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div class="v3-user-preview-hero">
                        <div class="transport-avatar-badge v3-user-preview-avatar" id="previewUserAvatar">
                            U
                        </div>
                        <h3 id="previewUserName" class="v3-user-preview-name">New Member</h3>
                        <span id="previewUserRole" class="badge-service-state badge-role-agent v3-user-preview-role">
                            <i class="fi fi-rr-headset"></i> Sales Agent
                        </span>
                    </div>

                    <div class="v3-user-preview-details">
                        <div class="v3-user-preview-row">
                            <i class="fi fi-rr-phone-call icon-phone-emerald"></i>
                            <span id="previewUserMobile" class="v3-user-preview-val">+91 &mdash;</span>
                        </div>
                        <div class="v3-user-preview-row">
                            <i class="fi fi-rr-envelope icon-mail-indigo"></i>
                            <span id="previewUserEmail" class="v3-user-preview-val">No email provided</span>
                        </div>
                    </div>
                </div>

                <!-- Security & Access Tips Card -->
                <div class="v3-tips-box emerald">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong>Access &amp; Security Policy</strong>
                        Credentials activate immediately. Administrators receive full workspace permissions; sales agents see assigned leads.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>