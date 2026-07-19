<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>


<div class="modern-company-list">
    <h2>All Companies</h2>
    <a href="company/?type=add" class="floating-add-btn" title="Add New Company">
        <i class="fas fa-plus"></i>
        <span>Add Company</span>
    </a>
    <div class="panel">
        <table id="table" data-default-order-column="0" data-page-length="10">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Registration</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php

                $hasCompanies = false;

                $sql = "SELECT c.*, a.*, s.state_name, ct.city_name FROM " . TABLE_PREFIX . "company c LEFT JOIN " . TABLE_PREFIX . "address a ON a.address_id = c.company_address_id LEFT JOIN " . TABLE_PREFIX . "state s ON s.state_id = a.address_state_id LEFT JOIN " . TABLE_PREFIX . "city ct ON ct.city_id = a.address_city_id WHERE c.company_site_id='" . SITE_ID . "' ORDER BY c.company_title ASC";

                if ($companyList = mysqli_query($connection, $sql)) {

                    if (mysqli_num_rows($companyList) > 0) {

                        while ($company = mysqli_fetch_assoc($companyList)) {

                            $hasCompanies = true;
                            extract($company);

                            $statusClass = 'status-pending';
                            switch ($company_status) {

                                case 0:
                                    $order_status = 'Pending';
                                    $statusClass = 'status-pending';
                                    break;

                                case 1:
                                    $order_status = 'Active';
                                    $statusClass = 'status-confirm';
                                    break;

                                case 2:
                                    $order_status = 'Reject';
                                    $statusClass = 'status-reject';
                                    break;

                                case 3:
                                    $order_status = 'Cancel';
                                    $statusClass = 'status-cancel';
                                    break;

                                case 4:
                                    $order_status = 'Waiting';
                                    $statusClass = 'status-waiting';
                                    break;
                            }

                            $initial = strtoupper(substr(trim($company_title), 0, 1));

                ?>

                            <tr>
                                <td>
                                    <div class="order-cell">
                                        <div class="order-badge">
                                            <?php if ($company_logo != '') { ?>
                                                <img src="<?php echo htmlspecialchars($company_logo); ?>" alt="<?php echo htmlspecialchars($company_title); ?>" class="order-badge-image">
                                            <?php } else { ?>
                                                <span class="order-badge-fallback"><?php echo htmlspecialchars($initial); ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="order-meta">
                                            <p>
                                                <a class="order-title-link"
                                                    href="company/?type=view&id=<?php echo base64_encode($company_id); ?>">
                                                    <?php echo htmlspecialchars($company_title); ?>
                                                </a>
                                            </p>
                                            <div class="order-product-name">
                                                <?php echo (($company_code != '') ? htmlspecialchars($company_code) : ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($company_email ?? ''); ?>
                                    </div>

                                    <div class="table-info">
                                        <i class="fas fa-phone flip-horizontal"></i>
                                        <?php echo htmlspecialchars($company_mobile ?? ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo ucwords(str_replace('_', ' ', $company_type)); ?></strong>

                                    <?php if (!empty($company_gst)) { ?>
                                        <div class="small-text">GST : <?php echo htmlspecialchars($company_gst); ?></div>
                                    <?php } ?>

                                    <?php if (!empty($company_cin)) { ?>
                                        <div class="small-text">CIN : <?php echo htmlspecialchars($company_cin); ?></div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php echo (($city_name != '') ? htmlspecialchars($city_name) . ' - ' : ''); ?>

                                    <div class="small-text">
                                        <?php echo htmlspecialchars($state_name ?? ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="order-tag <?php echo $statusClass; ?>">
                                        <?php echo $order_status; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="company/?type=edit&editor=true&id=<?php echo base64_encode($company_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                    <a href="#" id="<?php echo base64_encode($company_id) ?>_company" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>

                    <?php

                        }
                    }
                }

                if (!$hasCompanies) { ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-company-state">
                                <i class="fas fa-building"></i>
                                <h3>No companies found</h3>
                                <p>Create your first company to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>