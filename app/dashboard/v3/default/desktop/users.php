<?php

/**
 * Blumox Control Panel: Dedicated Team Members & Roles Page
 * Path: /app/dashboard/v3/default/desktop/users.php
 * Route: /dashboard/users/ (and /dashboard/team/)
 * Strict V3 Rule: External CSS and External JS only, no inline code
 */

$userRole = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$roleTitles = [
    'administrator' => 'System Administrator',
    'partner' => 'Partner / Referral',
    'sales' => 'Sales Executive',
    'support' => 'Customer Support',
    'on_ground_sales' => 'On Ground Sales'
];
$displayRole = $roleTitles[$userRole] ?? 'Administrator';

?>

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="users" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-users-alt"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Team Members &amp; Role-Based Access Control</h1>
                <p>
                    <span>System Administrators, Sales Executives, On-Ground Force, Support &amp; Partners</span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator' || $userRole === 'support') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenNewUserModal">
                    <i class="fi fi-rr-user-add"></i> <span>Add New User</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Users">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Users Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>System Users &amp; Team Directory</h3>
                <p>Manage access levels, operational roles, and credentials</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchUsers" placeholder="Search name, email, or mobile...">
            </div>
            <div class="cp-filters-wrap">
                <select class="cp-select" id="cpFilterUserRole">
                    <option value="">All Roles</option>
                    <option value="administrator">System Administrator</option>
                    <option value="partner">Partner / Referral</option>
                    <option value="sales">Sales Executive</option>
                    <option value="support">Customer Support</option>
                    <option value="on_ground_sales">On Ground Sales</option>
                </select>
            </div>
        </div>

        <div class="cp-table-wrap">
            <table class="cp-table" id="cpUsersTable">
                <thead>
                    <tr>
                        <th>User / Name</th>
                        <th>Contact Email &amp; Mobile</th>
                        <th>System Role</th>
                        <th>Assigned Workspace</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cpUsersTbody">
                    <tr>
                        <td colspan="6" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading team directory...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add User -->
<div class="cp-modal-backdrop" id="modalNewUser">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Add New User &amp; Assign Role</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewUser">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="userEmailInput">Email Address *</label>
                    <input type="email" class="cp-form-input" id="userEmailInput" required placeholder="user@blumox.com">
                </div>
                <div class="cp-form-group">
                    <label for="userDisplayNameInput">Display / Full Name</label>
                    <input type="text" class="cp-form-input" id="userDisplayNameInput" placeholder="e.g. John Doe">
                </div>
                <div class="cp-form-group">
                    <label for="userMobileInput">Mobile Number</label>
                    <input type="text" class="cp-form-input" id="userMobileInput" placeholder="+91 9876543210">
                </div>
                <div class="cp-form-group">
                    <label for="userRoleSelect">Role &amp; Permissions *</label>
                    <select class="cp-form-select" id="userRoleSelect" required>
                        <option value="administrator">System Administrator</option>
                        <option value="partner">Partner / Referral</option>
                        <option value="sales">Sales Executive</option>
                        <option value="support">Customer Support</option>
                        <option value="on_ground_sales">On Ground Sales</option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="userPasswordInput">Initial Password</label>
                    <input type="password" class="cp-form-input" id="userPasswordInput" value="Blumox@2026">
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Create User Account</button>
            </div>
        </form>
    </div>
</div>
