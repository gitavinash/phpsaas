<?php
$encodedId = $id ?? ($_GET['id'] ?? '');
if (empty($encodedId) && !empty($_GET['user_id'])) {
    $encodedId = base64_encode((string)$_GET['user_id']);
}
?>
<div class="package-page-wrapper user-form-wrapper" id="editUserApp" data-user-id="<?php echo htmlspecialchars($encodedId); ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon indigo">
                <i class="fi fi-rr-edit"></i>
            </div>
            <div class="package-header-titles">
                <h2>Update Team Member</h2>
                <p>Modify user details, role permissions, and access privileges.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/user/kyc/?id=<?php echo htmlspecialchars($encodedId); ?>" class="btn-v3-action" data-tooltip="Review KYC Verification">
                <i class="fi fi-rr-assessment"></i> <span>KYC Details</span>
            </a>
            <a href="/dashboard/user/" class="btn-v3-action" data-tooltip="Back to Team Directory">
                <i class="fi fi-rr-arrow-left"></i> <span>Team Directory</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="editUserForm" class="v3-form-container" autocomplete="off" novalidate>
                <input type="hidden" id="editUserId" name="id" value="<?php echo htmlspecialchars($encodedId); ?>">

                <!-- Card 1: Personal Information -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-user"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Personal Information</h3>
                            <p>Update the legal first and last name of the team member.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- First Name -->
                            <div class="v3-form-group">
                                <label for="editFirstName" class="v3-form-label">
                                    First Name <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user input-icon"></i>
                                    <input type="text" id="editFirstName" name="first_name" class="v3-form-input" placeholder="e.g. Rahul" required autofocus>
                                </div>
                                <span class="v3-field-error" id="errEditFirstName"></span>
                            </div>

                            <!-- Last Name -->
                            <div class="v3-form-group">
                                <label for="editLastName" class="v3-form-label">
                                    Last Name
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-portrait input-icon"></i>
                                    <input type="text" id="editLastName" name="last_name" class="v3-form-input" placeholder="e.g. Sharma">
                                </div>
                                <span class="v3-field-error" id="errEditLastName"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Contact Details -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-phone-call"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Contact Credentials</h3>
                            <p>Mobile and email are linked with the member's account access.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Primary Mobile -->
                            <div class="v3-form-group">
                                <label for="editUserMobile" class="v3-form-label">
                                    Primary Mobile Number
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-phone-call input-icon"></i>
                                    <input type="tel" id="editUserMobile" name="mobile" class="v3-form-input" placeholder="10-digit mobile number" maxlength="10">
                                </div>
                                <span class="v3-field-error" id="errEditUserMobile"></span>
                            </div>

                            <!-- Official Email -->
                            <div class="v3-form-group">
                                <label for="editUserEmail" class="v3-form-label">
                                    Official Email Address
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-envelope input-icon"></i>
                                    <input type="email" id="editUserEmail" name="email" class="v3-form-input" placeholder="e.g. rahul@example.com">
                                </div>
                                <span class="v3-field-error" id="errEditUserEmail"></span>
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
                            <p>Update dashboard access level and system permissions.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Role Dropdown -->
                            <div class="v3-form-group">
                                <label for="editUserRole" class="v3-form-label">
                                    Assigned System Role <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                     <i class="fi fi-rr-shield-check input-icon"></i>
                                     <select id="editUserRole" name="userRole" class="v3-form-select selectJS2" required>
                                         <option value="">Select Role</option>
                                         <option value="administrator">Administrator (Full Access)</option>
                                         <option value="agent">Sales Agent</option>
                                         <option value="operations">Operations Manager</option>
                                         <option value="accountant">Accounts &amp; Finance</option>
                                         <option value="support">Customer Support</option>
                                     </select>
                                 </div>
                                 <span class="v3-field-error" id="errEditUserRole"></span>
                            </div>

                            <!-- Bio / Title Note -->
                            <div class="v3-form-group">
                                <label for="editUserBio" class="v3-form-label">
                                    Designation / Note <span class="opt v3-label-opt">(Optional)</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-id-badge input-icon"></i>
                                    <input type="text" id="editUserBio" name="bio" class="v3-form-input" placeholder="e.g. Senior Tour Specialist">
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
                    <button type="submit" id="btnUpdateUser" class="v3-btn-primary">
                        <i class="fi fi-rr-check"></i> <span id="btnUpdateText">Save Changes</span>
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
                        <span class="v3-user-preview-title">Member Profile Preview</span>
                        <span class="v3-status-badge status-active badge-xs"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div class="v3-user-preview-hero">
                        <div class="transport-avatar-badge v3-user-preview-avatar" id="previewUserAvatar">
                            U
                        </div>
                        <h3 id="previewUserName" class="v3-user-preview-name">Team Member</h3>
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

                <!-- KYC Link & Security Box -->
                <div class="v3-tips-box emerald">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong>Security &amp; Audit</strong>
                        Role updates take effect immediately across all dashboard modules. Review documents on the <a href="/dashboard/user/kyc/?id=<?php echo htmlspecialchars($encodedId); ?>" class="v3-tips-link-emerald">KYC Review page &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>