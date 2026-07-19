<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>


<div class="modern-vendor-list">
    <h2>All Vendors</h2>
    <a href="vendor/?type=add" class="floating-add-btn" title="Add New Vendor">
        <i class="fas fa-plus"></i>
        <span>Add Vendor</span>
    </a>
    <div class="panel">
        <table id="table" data-default-order-column="0" data-page-length="10">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Contact</th>
                    <th>Registration</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php

                $hasVendors = false;

                $sql = "SELECT c.*, a.*, s.state_name, ct.city_name FROM " . TABLE_PREFIX . "vendor c LEFT JOIN " . TABLE_PREFIX . "address a ON a.address_id = c.vendor_address_id LEFT JOIN " . TABLE_PREFIX . "state s ON s.state_id = a.address_state_id LEFT JOIN " . TABLE_PREFIX . "city ct ON ct.city_id = a.address_city_id WHERE c.vendor_site_id='" . SITE_ID . "' ORDER BY c.vendor_title ASC";

                if ($vendorList = mysqli_query($connection, $sql)) {

                    if (mysqli_num_rows($vendorList) > 0) {

                        while ($vendor = mysqli_fetch_assoc($vendorList)) {

                            $hasVendors = true;
                            extract($vendor);

                            $statusClass = 'status-pending';
                            switch ($vendor_status) {

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

                            $initial = strtoupper(substr(trim($vendor_title), 0, 1));

                ?>

                            <tr>
                                <td>
                                    <div class="order-cell">
                                        <div class="order-badge">
                                            <?php if ($vendor_logo != '') { ?>
                                                <img src="<?php echo htmlspecialchars($vendor_logo); ?>" alt="<?php echo htmlspecialchars($vendor_title); ?>" class="order-badge-image">
                                            <?php } else { ?>
                                                <span class="order-badge-fallback"><?php echo htmlspecialchars($initial); ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="order-meta">
                                            <p>
                                                <a class="order-title-link"
                                                    href="vendor/?type=view&id=<?php echo base64_encode($vendor_id); ?>">
                                                    <?php echo htmlspecialchars($vendor_title); ?>
                                                </a>
                                            </p>
                                            <div class="order-product-name">
                                                <?php echo (($vendor_code != '') ? htmlspecialchars($vendor_code) : ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($vendor_email ?? ''); ?>
                                    </div>

                                    <div class="table-info">
                                        <i class="fas fa-phone flip-horizontal"></i>
                                        <?php echo htmlspecialchars($vendor_mobile ?? ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo ucwords(str_replace('_', ' ', $vendor_type)); ?></strong>

                                    <?php if (!empty($vendor_gst)) { ?>
                                        <div class="small-text">GST : <?php echo htmlspecialchars($vendor_gst); ?></div>
                                    <?php } ?>

                                    <?php if (!empty($vendor_cin)) { ?>
                                        <div class="small-text">CIN : <?php echo htmlspecialchars($vendor_cin); ?></div>
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
                                    <a href="vendor/?type=edit&editor=true&id=<?php echo base64_encode($vendor_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                    <a href="#" id="<?php echo base64_encode($vendor_id) ?>_vendor" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>

                    <?php

                        }
                    }
                }

                if (!$hasVendors) { ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-vendor-state">
                                <i class="fas fa-building"></i>
                                <h3>No vendors found</h3>
                                <p>Create your first vendor to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>