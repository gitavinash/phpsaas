<?php

/**
 * Blumox Control Panel: Dedicated Team Members & Roles Page
 * Path: /app/dashboard/v3/default/desktop/users.php
 * Route: /dashboard/users/ (and /dashboard/team/)
 * Strict V3 Rule: External CSS and External JS only, no inline code
 * Scoped strictly to current site access context
 */

$userRole = isset($site_access_type) ? strtolower(trim($site_access_type)) : 'administrator';
$roleTitles = [
    'administrator' => 'System Administrator',
    'partner' => 'Partner / Referral',
    'sales' => 'Sales Executive',
    'sales_executive' => 'Sales Executive',
    'support' => 'Customer Support',
    'on_ground_sales' => 'On Ground Sales',
    'agent' => 'Calling Agent / Executive',
    'team_lead' => 'Team Lead',
    'operations' => 'Operations Executive',
    'digital_marketing' => 'Digital Marketing',
    'member' => 'Team Member'
];
$displayRole = $roleTitles[$userRole] ?? ucwords(str_replace('_', ' ', $userRole));

$currentSiteId = isset($_GET['site_id']) && is_numeric($_GET['site_id']) && (int)$_GET['site_id'] > 0
    ? (int)$_GET['site_id']
    : (defined('SITE_ID') ? (int)SITE_ID : 1);

$currentSiteName = defined('SITE_NAME') ? SITE_NAME : 'Current Workspace';
$isControlPanel = (defined('SITE_ID') && (int)SITE_ID === 1);
?>

<div class="cp-wrapper cp-page-dedicated" id="controlPanelApp" data-page="users" data-site-id="<?php echo (int)$currentSiteId; ?>" data-site-name="<?php echo htmlspecialchars($currentSiteName); ?>" data-is-cp="<?php echo $isControlPanel ? '1' : '0'; ?>" data-user-role="<?php echo htmlspecialchars($userRole); ?>">

    <!-- Top Executive Header -->
    <div class="cp-header-bar">
        <div class="cp-header-left">
            <div class="cp-header-icon">
                <i class="fi fi-rr-users-alt"></i>
            </div>
            <div class="cp-header-titles">
                <h1>Team Members &amp; Site Access Control</h1>
                <p>
                    <span>Site Members, Role Permissions &amp; Credentials for <strong><?php echo htmlspecialchars($currentSiteName); ?></strong></span>
                    <span class="cp-badge-role <?php echo htmlspecialchars($userRole === 'on_ground_sales' ? 'ground' : $userRole); ?>">
                        <i class="fi fi-rr-shield-check"></i> <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="cp-header-actions">
            <?php if ($userRole === 'administrator' || $userRole === 'support') { ?>
                <button type="button" class="cp-btn-primary" id="btnOpenNewUserModal">
                    <i class="fi fi-rr-user-add"></i> <span>Add Team Member</span>
                </button>
            <?php } ?>
            <button type="button" class="cp-btn-secondary" id="btnCpRefreshData" title="Refresh Users">
                <i class="fi fi-rr-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Executive KPI Metric Counters -->
    <div class="cp-kpi-grid">
        <div class="cp-kpi-card">
            <div class="cp-kpi-icon blue">
                <i class="fi fi-rr-users-alt"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Team Members</p>
                <h3 id="statTotalUsers">-</h3>
                <span class="cp-kpi-sub" id="statSiteLabel"><?php echo htmlspecialchars($currentSiteName); ?></span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon green">
                <i class="fi fi-rr-check-circle"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Active Access</p>
                <h3 id="statActiveUsers">-</h3>
                <span class="cp-kpi-sub">Authorized credentials</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon purple">
                <i class="fi fi-rr-shield-check"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Administrators</p>
                <h3 id="statAdminUsers">-</h3>
                <span class="cp-kpi-sub">Full site governance</span>
            </div>
        </div>

        <div class="cp-kpi-card">
            <div class="cp-kpi-icon orange">
                <i class="fi fi-rr-briefcase"></i>
            </div>
            <div class="cp-kpi-meta">
                <p>Staff &amp; Operations</p>
                <h3 id="statStaffUsers">-</h3>
                <span class="cp-kpi-sub">Executives, agents &amp; reps</span>
            </div>
        </div>
    </div>

    <!-- Users Main Card -->
    <div class="cp-card">
        <div class="cp-card-header">
            <div class="cp-card-title">
                <h3>Site Users &amp; Team Directory</h3>
                <p>Manage access levels, operational roles, and credentials for this site</p>
            </div>
        </div>

        <div class="cp-toolbar">
            <div class="cp-search-wrap">
                <i class="fi fi-rr-search"></i>
                <input type="text" class="cp-search-input" id="cpSearchUsers" placeholder="Search team by name, email, or mobile...">
                <button type="button" class="cp-search-clear-btn cp-hidden" id="cpClearUsersSearch" title="Clear Search">&times;</button>
            </div>
            <div class="cp-filters-wrap">
                <?php if ($isControlPanel) { ?>
                    <select class="cp-select selectJS2 cp-select2" id="cpFilterUserSite" data-placeholder="Site Access Context" data-width="220px">
                        <option value="<?php echo (int)$currentSiteId; ?>" selected><?php echo htmlspecialchars($currentSiteName); ?> (#<?php echo (int)$currentSiteId; ?>)</option>
                    </select>
                <?php } ?>
                <select class="cp-select selectJS2 cp-select2" id="cpFilterUserRole" data-placeholder="All Roles" data-width="170px">
                    <option value="">All Roles</option>
                    <option value="administrator">Administrator</option>
                    <option value="agent">Agent / Executive</option>
                    <option value="team_lead">Team Lead</option>
                    <option value="operations">Operations</option>
                    <option value="digital_marketing">Digital Marketing</option>
                    <option value="sales_executive">Sales Executive</option>
                    <option value="support">Customer Support</option>
                    <option value="partner">Partner / Referral</option>
                    <option value="on_ground_sales">On Ground Sales</option>
                </select>
                <select class="cp-select selectJS2 cp-select2" id="cpFilterUserStatus" data-placeholder="All Status" data-width="140px">
                    <option value="">All Status</option>
                    <option value="1">Active Only</option>
                    <option value="0">Disabled Only</option>
                </select>
            </div>
        </div>

        <div class="cp-table-wrap">
            <table class="cp-table" id="cpUsersTable">
                <thead>
                    <tr>
                        <th>User &amp; Identity</th>
                        <th>Contact Details</th>
                        <th>Site Role</th>
                        <th>Site Workspace</th>
                        <th>Access Status</th>
                        <th class="text-right cp-actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody id="cpUsersTbody">
                    <tr>
                        <td colspan="6" class="text-center cp-loading-cell">
                            <i class="fi fi-rr-spinner-alt cp-spin"></i> Loading team directory for current site access...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="cp-pagination-bar cp-hidden" id="cpUsersPaginationWrap">
            <div class="cp-pagination-info" id="cpUsersPaginationInfo"></div>
            <div class="cp-pagination-controls" id="cpUsersPaginationControls"></div>
        </div>
    </div>

</div>

<!-- Modal: Add New Team Member -->
<div class="cp-modal-backdrop" id="modalNewUser">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Add Member &amp; Assign Site Access</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formNewUser">
            <input type="hidden" id="userSiteIdInput" value="<?php echo (int)$currentSiteId; ?>">
            <div class="cp-modal-body">
                <div class="cp-form-group">
                    <label for="userEmailInput">Email Address *</label>
                    <input type="email" class="cp-form-input" id="userEmailInput" required placeholder="user@company.com">
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
                    <label for="userRoleSelect">Role &amp; Permissions for this Site *</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="userRoleSelect" required data-placeholder="Select Role" data-width="100%">
                        <option value="administrator">System Administrator</option>
                        <option value="agent">Agent / Executive</option>
                        <option value="team_lead">Team Lead</option>
                        <option value="operations">Operations Executive</option>
                        <option value="digital_marketing">Digital Marketing</option>
                        <option value="sales_executive">Sales Executive</option>
                        <option value="support">Customer Support</option>
                        <option value="partner">Partner / Referral</option>
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
                <button type="submit" class="cp-btn-primary">Grant Site Access</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit User Site Role -->
<div class="cp-modal-backdrop" id="modalEditUserRole">
    <div class="cp-modal-box">
        <div class="cp-modal-header">
            <h3>Update User Site Role</h3>
            <button type="button" class="cp-modal-close-btn" data-close-modal>&times;</button>
        </div>
        <form id="formEditUserRole">
            <input type="hidden" id="editUserRoleUserId" value="0">
            <input type="hidden" id="editUserRoleSiteId" value="0">
            <div class="cp-modal-body">
                <div class="cp-user-role-summary-box">
                    <div class="cp-user-role-avatar" id="editUserAvatar">U</div>
                    <div class="cp-user-role-info">
                        <h4 id="editUserNameDisplay">User Name</h4>
                        <p id="editUserEmailDisplay">user@example.com</p>
                        <span class="cp-badge-site" id="editUserSiteDisplay">Site Name</span>
                    </div>
                </div>
                <div class="cp-form-group">
                    <label for="editUserRoleSelect">Assigned Role for this Site *</label>
                    <select class="cp-form-select selectJS2 cp-select2 cp-modal-select2" id="editUserRoleSelect" required data-placeholder="Select Role" data-width="100%">
                        <option value="administrator">System Administrator</option>
                        <option value="agent">Agent / Executive</option>
                        <option value="team_lead">Team Lead</option>
                        <option value="operations">Operations Executive</option>
                        <option value="digital_marketing">Digital Marketing</option>
                        <option value="sales_executive">Sales Executive</option>
                        <option value="support">Customer Support</option>
                        <option value="partner">Partner / Referral</option>
                        <option value="on_ground_sales">On Ground Sales</option>
                    </select>
                </div>
            </div>
            <div class="cp-modal-footer">
                <button type="button" class="cp-btn-secondary" data-close-modal>Cancel</button>
                <button type="submit" class="cp-btn-primary">Save Role Access</button>
            </div>
        </form>
    </div>
</div>
