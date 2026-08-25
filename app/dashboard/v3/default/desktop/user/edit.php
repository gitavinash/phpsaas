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
            <div class="package-header-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                <i class="fi fi-rr-edit" style="color: #ffffff;"></i>
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
                                    <select id="editUserRole" name="userRole" class="v3-form-select" required>
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
                                    Designation / Note <span class="opt" style="font-weight: normal; color: #94a3b8;">(Optional)</span>
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
                <div class="v3-live-preview-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Member Profile Preview</span>
                        <span class="v3-status-badge status-active" style="font-size: 11px;"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 16px;">
                        <div class="transport-avatar-badge" id="previewUserAvatar" style="width: 64px; height: 64px; font-size: 22px; font-weight: 700; border-radius: 16px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                            U
                        </div>
                        <h3 id="previewUserName" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Team Member</h3>
                        <span id="previewUserRole" class="badge-service-state badge-role-agent" style="font-size: 11.5px; margin-top: 2px;">
                            <i class="fi fi-rr-headset"></i> Sales Agent
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; background: #f8fafc; border-radius: 10px; padding: 12px; border: 1px solid #edf2f7; font-size: 12.5px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155;">
                            <i class="fi fi-rr-phone-call" style="color: #10b981; font-size: 12px; width: 14px;"></i>
                            <span id="previewUserMobile" style="font-weight: 500;">+91 &mdash;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <i class="fi fi-rr-envelope" style="color: #6366f1; font-size: 12px; width: 14px;"></i>
                            <span id="previewUserEmail" style="font-weight: 500; overflow: hidden; text-overflow: ellipsis;">No email provided</span>
                        </div>
                    </div>
                </div>

                <!-- KYC Link & Security Box -->
                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-shield-check"></i></div>
                    <div class="tips-content">
                        <strong style="color: #166534; display: block; margin-bottom: 2px;">Security &amp; Audit</strong>
                        Role updates take effect immediately across all dashboard modules. Review documents on the <a href="/dashboard/user/kyc/?id=<?php echo htmlspecialchars($encodedId); ?>" style="color: #15803d; font-weight: 600; text-decoration: underline;">KYC Review page &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>