<?php

if (isset($id)) {

    $id = base64_decode($id);

    $siteId = " IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
    $isApp32 = ((int)SITE_APP_ID === 32);

    $productList = mysqli_query($connection, "SELECT product_id, product_title, product_image FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id" . $siteId);

    if ((mysqli_num_rows($productList) != 0) || $isApp32) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {

            extract($productLists);

            $productListData[] = array(
                'product_id' => $product_id,
                'product_title' => $product_title,
                'product_image' => $product_image
            );
        }

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status, product_title, product_image, inventory_currency_symbol FROM isw_order LEFT JOIN isw_address ON order_address_id=address_id LEFT JOIN isw_user ON order_user_id=user_id LEFT JOIN isw_profile ON profile_user_id=user_id LEFT JOIN isw_city ON city_id=address_city_id LEFT JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' AND product_site_id" . $siteId;
        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id" . $siteId;

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id" . $siteId;

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$transaction_status);
                $numericStatusMap = array(
                    '' => 'Pending',
                    '0' => 'Pending',
                    '1' => 'Confirmed',
                    '2' => 'Waiting',
                    '3' => 'Cancelled',
                    '4' => 'Returned',
                    '5' => 'Delivered',
                );

                if (isset($numericStatusMap[$orderStatusValue])) {
                    $orderStatusText = $numericStatusMap[$orderStatusValue];
                } elseif (is_numeric($orderStatusValue)) {
                    $orderStatusText = 'Status Updated';
                } else {
                    $orderStatusText = ucwords(str_replace(array('_', '-'), ' ', $orderStatusValue));
                }

                $orderStatusRaw = strtolower(trim((string)$orderStatusText));
                $orderStatusRaw = str_replace(array(' ', '-'), '_', $orderStatusRaw);
                $orderStatusChipClass = 'badge-default';

                if (in_array($orderStatusRaw, array('delivered', 'complete', 'completed', 'success', 'confirmed', 'confirm'))) {
                    $orderStatusChipClass = 'badge-delivered';
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress'))) {
                    $orderStatusChipClass = 'badge-pending';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'badge-cancelled';
                }

                // Fetch order meta details

                if ($orderMetaExits = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id={$id} AND order_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($orderMetaExits) != 0) {
                        while ($orderMetaData = mysqli_fetch_assoc($orderMetaExits)) {
                            extract($orderMetaData);
                            if (!empty($order_meta_key)) {
                                $variableName = 'order' . ucfirst($order_meta_key);
                                $$variableName = $order_meta_value;
                            }
                        }
                    }
                }

                $orderDeliveryDate = ((isset($orderDeliveryDate)) ? $orderDeliveryDate : '');

?>
                <div class="order-info-ui" data-order-id="<?php echo $id ?>">

                    <div class="premium-header">
                        <div class="premium-header-left">
                            <h2>Order #<?php echo $id ?></h2>
                            <p class="order-info-subtitle">Review, update customer profile, and process the lifecycle of this order.</p>
                        </div>
                        <div>
                            <span class="premium-badge <?php echo $orderStatusChipClass ?>">
                                <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i> <?php echo $orderStatusText ?>
                            </span>
                        </div>
                    </div>

                    <div class="premium-stats-bar">
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Payment Mode</span>
                            <span class="premium-stat-value"><?php echo ucwords((($product_title != '') ? $product_title : 'N/A')) ?></span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Items Count</span>
                            <span class="premium-stat-value"><?php echo sizeof(explode(',', $order_product_id)) ?> items</span>
                        </div>
                        <div class="premium-stat-item" style="border-right: none;">
                            <span class="premium-stat-label">Delivery Date</span>
                            <span class="premium-stat-value"><?php echo date('Y-m-d', strtotime($orderDeliveryDate)); ?></span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Delivered At</span>
                            <span class="premium-stat-value"></span>
                        </div>
                    </div>

                    <div class="order-info-shell">
                        <form id="orderData">
                            <div class="premium-grid">

                                <!-- RIGHT PANEL: Billing summary and Receiver Info -->
                                <div class="premium-grid-right">

                                    <!-- Card 4: Receiver Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-map-marker-alt"></i> Receiver Details</h3>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Receiver Name</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_name ?>" readonly>
                                        </div>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Contact Number</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_mobile ?>" readonly>
                                        </div>

                                        <?php
                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id=" . SITE_ID)) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);
                                                    if ($order_meta_key == 'message') { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <textarea class="premium-form-control" readonly><?php echo $order_meta_value ?></textarea>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <input type="text" class="premium-form-control" value="<?php echo $order_meta_value ?>" readonly>
                                                        </div>
                                        <?php }
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Actions Bar at bottom of cards -->
                            <div class="premium-action-row">
                                <input type="hidden" name="type" value="order">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <a href="#" id="orderRescheduleBtn" class="btn-premium-action btn-reschedule"><i class="fas fa-calendar-plus"></i> Other Day Delivery</a>
                                <a href="#" id="orderReturnBtn" class="btn-premium-action btn-return-action"><i class="fas fa-undo"></i> Return</a>
                                <a href="#" id="orderDeliverBtn" class="btn-premium-action btn-delivered"><i class="fas fa-shipping-fast"></i> Delivered</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Action Modal -->
                <div id="orderActionModal" class="order-action-modal">
                    <div class="order-action-modal-backdrop"></div>
                    <div class="order-action-modal-content">
                        <div class="order-action-modal-header">
                            <h3 id="modalTitle">Process Order Action</h3>
                            <button type="button" class="close-modal-btn" aria-label="Close modal">&times;</button>
                        </div>
                        <form id="orderActionForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action_type" id="actionTypeInput" value="">

                            <div class="premium-form-group">
                                <label for="actionComment">Comment <span class="required">*</span></label>
                                <textarea id="actionComment" name="comment" class="premium-form-control" placeholder="Enter comments here..." required></textarea>
                            </div>

                            <div id="rescheduleFields" style="display: none;">
                                <div class="premium-form-group">
                                    <label for="deliveryDateInput">Delivery Date <span id="deliveryDateRequiredMark" class="required" style="display: none;">*</span></label>
                                    <input type="date" id="deliveryDateInput" name="delivery_date" class="premium-form-control">
                                </div>
                                <div class="premium-form-group">
                                    <label for="deliveryPreferredTimeInput">Delivery Preferred Time <span id="deliveryTimeRequiredMark" class="required" style="display: none;">*</span></label>
                                    <select id="deliveryPreferredTimeInput" name="delivery_preferred_time" class="premium-form-control">
                                        <option value="">Select time slot</option>
                                        <option value="00:00-01:00">12:00 AM - 01:00 AM</option>
                                        <option value="01:00-02:00">01:00 AM - 02:00 AM</option>
                                        <option value="02:00-03:00">02:00 AM - 03:00 AM</option>
                                        <option value="03:00-04:00">03:00 AM - 04:00 AM</option>
                                        <option value="04:00-05:00">04:00 AM - 05:00 AM</option>
                                        <option value="05:00-06:00">05:00 AM - 06:00 AM</option>
                                        <option value="06:00-07:00">06:00 AM - 07:00 AM</option>
                                        <option value="07:00-08:00">07:00 AM - 08:00 AM</option>
                                        <option value="08:00-09:00">08:00 AM - 09:00 AM</option>
                                        <option value="09:00-10:00">09:00 AM - 10:00 AM</option>
                                        <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                                        <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                                        <option value="12:00-13:00">12:00 PM - 01:00 PM</option>
                                        <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                                        <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                                        <option value="15:00-16:00">03:00 PM - 04:00 PM</option>
                                        <option value="16:00-17:00">04:00 PM - 05:00 PM</option>
                                        <option value="17:00-18:00">05:00 PM - 06:00 PM</option>
                                        <option value="18:00-19:00">06:00 PM - 07:00 PM</option>
                                        <option value="19:00-20:00">07:00 PM - 08:00 PM</option>
                                        <option value="20:00-21:00">08:00 PM - 09:00 PM</option>
                                        <option value="21:00-22:00">09:00 PM - 10:00 PM</option>
                                        <option value="22:00-23:00">10:00 PM - 11:00 PM</option>
                                        <option value="23:00-24:00">11:00 PM - 12:00 AM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="premium-form-group" id="actionPhotoGroup">
                                <label for="actionPhoto">Upload Photo <span id="photoRequiredMark" class="required" style="display: none;">*</span></label>
                                <div class="photo-upload-container">
                                    <input type="file" id="actionPhoto" name="photo" accept="image/*" class="premium-form-control-file">
                                    <div class="photo-preview-box" id="photoPreviewBox">
                                        <i class="fas fa-camera"></i>
                                        <span>Select or capture photo</span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-action-modal-footer">
                                <button type="button" class="btn-premium-action btn-secondary close-modal-btn">Cancel</button>
                                <button type="submit" class="btn-premium-action btn-primary" id="submitActionBtn">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Toast Notification Container -->
                <div id="orderToastNotification" class="order-toast-notification"></div>

                <style>
                    /* Custom Button Styles */
                    .btn-reschedule {
                        background-color: #3b82f6 !important;
                    }

                    .btn-reschedule:hover {
                        background-color: #2563eb !important;
                    }

                    .btn-return-action {
                        background-color: #f43f5e !important;
                    }

                    .btn-return-action:hover {
                        background-color: #e11d48 !important;
                    }

                    /* Custom Modal Styles */
                    .order-action-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 10000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        opacity: 0;
                        pointer-events: none;
                        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    }

                    .order-action-modal.is-active {
                        opacity: 1;
                        pointer-events: auto;
                    }

                    .order-action-modal-backdrop {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(15, 23, 42, 0.6);
                        backdrop-filter: blur(8px);
                        -webkit-backdrop-filter: blur(8px);
                    }

                    .order-action-modal-content {
                        position: relative;
                        background: #ffffff;
                        width: 90%;
                        max-width: 500px;
                        border-radius: 16px;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                        transform: scale(0.9);
                        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                        z-index: 1;
                        overflow: hidden;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    }

                    .order-action-modal.is-active .order-action-modal-content {
                        transform: scale(1);
                    }

                    .order-action-modal-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 20px 24px;
                        border-bottom: 1px solid #f1f5f9;
                    }

                    .order-action-modal-header h3 {
                        margin: 0;
                        font-size: 1.25rem;
                        font-weight: 700;
                        color: #0f172a;
                        transition: color 0.3s;
                    }

                    .order-action-modal-header .close-modal-btn {
                        background: transparent;
                        border: none;
                        font-size: 1.5rem;
                        cursor: pointer;
                        color: #64748b;
                        padding: 0;
                        line-height: 1;
                        transition: color 0.2s;
                    }

                    .order-action-modal-header .close-modal-btn:hover {
                        color: #0f172a;
                    }

                    .order-action-modal form {
                        padding: 24px;
                        margin: 0;
                    }

                    .order-action-modal .premium-form-group {
                        margin-bottom: 20px;
                    }

                    .order-action-modal .premium-form-group label {
                        display: block;
                        margin-bottom: 8px;
                        font-weight: 600;
                        color: #334155;
                        font-size: 0.9rem;
                    }

                    .order-action-modal .premium-form-group label .required {
                        color: #ef4444;
                    }

                    .order-action-modal textarea.premium-form-control {
                        width: 100%;
                        min-height: 100px;
                        padding: 12px;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        font-size: 0.95rem;
                        resize: vertical;
                        outline: none;
                        transition: border-color 0.2s, box-shadow 0.2s;
                        background: #f8fafc;
                    }

                    .order-action-modal textarea.premium-form-control:focus {
                        border-color: #3b82f6;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                        background: #ffffff;
                    }

                    .photo-upload-container {
                        position: relative;
                        overflow: hidden;
                    }

                    .premium-form-control-file {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        opacity: 0;
                        cursor: pointer;
                        z-index: 2;
                    }

                    .photo-preview-box {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        border: 2px dashed #cbd5e1;
                        border-radius: 12px;
                        padding: 24px;
                        background: #f8fafc;
                        text-align: center;
                        transition: background 0.2s, border-color 0.2s;
                        cursor: pointer;
                        min-height: 140px;
                    }

                    .photo-preview-box i {
                        font-size: 2rem;
                        color: #94a3b8;
                        margin-bottom: 8px;
                        transition: color 0.2s;
                    }

                    .photo-preview-box span {
                        font-size: 0.85rem;
                        color: #64748b;
                        font-weight: 500;
                    }

                    .photo-upload-container:hover .photo-preview-box {
                        background: #f1f5f9;
                        border-color: #94a3b8;
                    }

                    .photo-upload-container:hover .photo-preview-box i {
                        color: #64748b;
                    }

                    .photo-preview-box.has-image {
                        border-style: solid;
                        border-color: #3b82f6;
                        background-size: cover;
                        background-position: center;
                        min-height: 160px;
                    }

                    .photo-preview-box.has-image i,
                    .photo-preview-box.has-image span {
                        background: rgba(15, 23, 42, 0.75);
                        color: #ffffff;
                        padding: 4px 10px;
                        border-radius: 6px;
                        backdrop-filter: blur(4px);
                    }

                    .order-action-modal-footer {
                        display: flex;
                        justify-content: flex-end;
                        gap: 12px;
                        margin-top: 24px;
                        border-top: 1px solid #f1f5f9;
                        padding-top: 16px;
                    }

                    .order-action-modal-footer .btn-premium-action {
                        padding: 10px 20px;
                        font-size: 0.95rem;
                        font-weight: 600;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: all 0.2s;
                        border: none;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .order-action-modal-footer .btn-secondary {
                        background: #f1f5f9;
                        color: #475569;
                    }

                    .order-action-modal-footer .btn-secondary:hover {
                        background: #e2e8f0;
                        color: #0f172a;
                    }

                    .order-action-modal-footer .btn-primary {
                        background: #3b82f6;
                        color: #ffffff;
                    }

                    .order-action-modal-footer .btn-primary:hover {
                        background: #2563eb;
                    }

                    .order-action-modal-footer .btn-primary:disabled {
                        background: #93c5fd;
                        cursor: not-allowed;
                    }

                    /* Theme Colors based on action type */
                    .order-action-modal.action-reschedule h3 {
                        color: #2563eb;
                    }

                    .order-action-modal.action-reschedule .btn-primary {
                        background: #2563eb;
                    }

                    .order-action-modal.action-reschedule .btn-primary:hover {
                        background: #1d4ed8;
                    }

                    .order-action-modal.action-return h3 {
                        color: #dc2626;
                    }

                    .order-action-modal.action-return .btn-primary {
                        background: #dc2626;
                    }

                    .order-action-modal.action-return .btn-primary:hover {
                        background: #b91c1c;
                    }

                    .order-action-modal.action-deliver h3 {
                        color: #059669;
                    }

                    .order-action-modal.action-deliver .btn-primary {
                        background: #059669;
                    }

                    .order-action-modal.action-deliver .btn-primary:hover {
                        background: #047857;
                    }

                    .order-toast-notification {
                        position: fixed;
                        bottom: 24px;
                        right: 24px;
                        z-index: 10001;
                        padding: 16px 24px;
                        border-radius: 12px;
                        background: #0f172a;
                        color: #ffffff;
                        font-weight: 600;
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                        transform: translateY(100px);
                        opacity: 0;
                        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
                    }

                    .order-toast-notification.is-visible {
                        transform: translateY(0);
                        opacity: 1;
                    }

                    .order-toast-notification.success {
                        background: #10b981;
                    }

                    .order-toast-notification.error {
                        background: #ef4444;
                    }
                </style>

                <script>
                    (function() {
                        var modal = document.getElementById('orderActionModal');
                        var form = document.getElementById('orderActionForm');
                        var actionTypeInput = document.getElementById('actionTypeInput');
                        var modalTitle = document.getElementById('modalTitle');
                        var submitBtn = document.getElementById('submitActionBtn');
                        var closeBtns = modal.querySelectorAll('.close-modal-btn');
                        var actionPhotoGroup = document.getElementById('actionPhotoGroup');
                        var fileInput = document.getElementById('actionPhoto');
                        var rescheduleFields = document.getElementById('rescheduleFields');
                        var deliveryDateInput = document.getElementById('deliveryDateInput');
                        var deliveryPreferredTimeInput = document.getElementById('deliveryPreferredTimeInput');
                        var deliveryDateRequiredMark = document.getElementById('deliveryDateRequiredMark');
                        var deliveryTimeRequiredMark = document.getElementById('deliveryTimeRequiredMark');
                        var photoRequiredMark = document.getElementById('photoRequiredMark');
                        var previewBox = document.getElementById('photoPreviewBox');
                        var toast = document.getElementById('orderToastNotification');
                        var today = new Date();
                        var tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);

                        function formatDateForInput(dateObj) {
                            var yyyy = dateObj.getFullYear();
                            var mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                            var dd = String(dateObj.getDate()).padStart(2, '0');
                            return yyyy + '-' + mm + '-' + dd;
                        }

                        function isFutureDateOnly(dateValue) {
                            if (!dateValue) {
                                return false;
                            }
                            return dateValue >= formatDateForInput(tomorrow);
                        }

                        var minAllowedDate = formatDateForInput(tomorrow);
                        if (deliveryDateInput) {
                            deliveryDateInput.setAttribute('min', minAllowedDate);
                            deliveryDateInput.addEventListener('focus', function() {
                                if (typeof this.showPicker === 'function') {
                                    this.showPicker();
                                }
                            });
                            deliveryDateInput.addEventListener('click', function() {
                                if (typeof this.showPicker === 'function') {
                                    this.showPicker();
                                }
                            });
                            deliveryDateInput.addEventListener('change', function() {
                                if (this.value && !isFutureDateOnly(this.value)) {
                                    this.value = '';
                                    showToast('Please select a future date only.', 'error');
                                }
                            });
                        }

                        // Map button IDs to action type and modal UI configurations
                        var configs = {
                            'orderRescheduleBtn': {
                                action: 'reschulde',
                                title: 'Confirm Other Day Delivery',
                                btnText: 'Confirm Other Day Delivery',
                                colorClass: 'action-reschedule',
                                requirePhoto: false,
                                requireSchedule: true,
                                showPhotoField: false
                            },
                            'orderReturnBtn': {
                                action: 'return',
                                title: 'Confirm Return',
                                btnText: 'Confirm Return',
                                colorClass: 'action-return',
                                requirePhoto: false,
                                requireSchedule: false,
                                showPhotoField: true
                            },
                            'orderDeliverBtn': {
                                action: 'deliverd',
                                title: 'Confirm Delivery',
                                btnText: 'Confirm Delivery',
                                colorClass: 'action-deliver',
                                requirePhoto: true,
                                requireSchedule: false,
                                showPhotoField: true
                            }
                        };

                        // Close modal function
                        function closeModal() {
                            modal.classList.remove('is-active');
                            form.reset();
                            if (fileInput) {
                                fileInput.required = false;
                            }
                            if (photoRequiredMark) {
                                photoRequiredMark.style.display = 'none';
                            }
                            if (rescheduleFields) {
                                rescheduleFields.style.display = 'none';
                            }
                            if (deliveryDateInput) {
                                deliveryDateInput.required = false;
                                deliveryDateInput.value = '';
                            }
                            if (deliveryPreferredTimeInput) {
                                deliveryPreferredTimeInput.required = false;
                                deliveryPreferredTimeInput.value = '';
                            }
                            if (deliveryDateRequiredMark) {
                                deliveryDateRequiredMark.style.display = 'none';
                            }
                            if (deliveryTimeRequiredMark) {
                                deliveryTimeRequiredMark.style.display = 'none';
                            }
                            if (actionPhotoGroup) {
                                actionPhotoGroup.style.display = 'block';
                            }
                            previewBox.classList.remove('has-image');
                            previewBox.style.backgroundImage = '';
                            previewBox.querySelector('i').style.display = '';
                            previewBox.querySelector('span').textContent = 'Select or capture photo';
                        }

                        // Bind triggers
                        Object.keys(configs).forEach(function(btnId) {
                            var btn = document.getElementById(btnId);
                            if (btn) {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    var config = configs[btnId];
                                    actionTypeInput.value = config.action;
                                    modalTitle.textContent = config.title;
                                    submitBtn.textContent = config.btnText;
                                    if (fileInput) {
                                        fileInput.required = !!config.requirePhoto;
                                    }
                                    if (photoRequiredMark) {
                                        photoRequiredMark.style.display = config.requirePhoto ? 'inline' : 'none';
                                    }
                                    if (rescheduleFields) {
                                        rescheduleFields.style.display = config.requireSchedule ? 'block' : 'none';
                                    }
                                    if (deliveryDateInput) {
                                        deliveryDateInput.setAttribute('min', minAllowedDate);
                                        deliveryDateInput.required = !!config.requireSchedule;
                                        if (!config.requireSchedule) {
                                            deliveryDateInput.value = '';
                                        }
                                    }
                                    if (deliveryPreferredTimeInput) {
                                        deliveryPreferredTimeInput.required = !!config.requireSchedule;
                                        if (!config.requireSchedule) {
                                            deliveryPreferredTimeInput.value = '';
                                        }
                                    }
                                    if (deliveryDateRequiredMark) {
                                        deliveryDateRequiredMark.style.display = config.requireSchedule ? 'inline' : 'none';
                                    }
                                    if (deliveryTimeRequiredMark) {
                                        deliveryTimeRequiredMark.style.display = config.requireSchedule ? 'inline' : 'none';
                                    }
                                    if (actionPhotoGroup) {
                                        actionPhotoGroup.style.display = config.showPhotoField ? 'block' : 'none';
                                    }

                                    // Set theme color class on modal
                                    modal.className = 'order-action-modal ' + config.colorClass;
                                    modal.classList.add('is-active');

                                    // Focus on comment textarea
                                    setTimeout(function() {
                                        document.getElementById('actionComment').focus();
                                    }, 100);
                                });
                            }
                        });

                        // Bind close buttons
                        closeBtns.forEach(function(btn) {
                            btn.addEventListener('click', closeModal);
                        });

                        // Close on clicking backdrop
                        modal.querySelector('.order-action-modal-backdrop').addEventListener('click', closeModal);

                        // Close on Escape key
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape' && modal.classList.contains('is-active')) {
                                closeModal();
                            }
                        });

                        // File input thumbnail preview
                        fileInput.addEventListener('change', function() {
                            var file = this.files[0];
                            if (file) {
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    previewBox.classList.add('has-image');
                                    previewBox.style.backgroundImage = 'url(' + e.target.result + ')';
                                    previewBox.querySelector('i').style.display = 'none';
                                    previewBox.querySelector('span').textContent = file.name;
                                };
                                reader.readAsDataURL(file);
                            } else {
                                previewBox.classList.remove('has-image');
                                previewBox.style.backgroundImage = '';
                                previewBox.querySelector('i').style.display = '';
                                previewBox.querySelector('span').textContent = 'Select or capture photo';
                            }
                        });

                        // Toast show helper
                        function showToast(message, status) {
                            toast.textContent = message;
                            toast.className = 'order-toast-notification ' + (status === 'success' ? 'success' : 'error');
                            toast.classList.add('is-visible');
                            setTimeout(function() {
                                toast.classList.remove('is-visible');
                            }, 3000);
                        }

                        // Form submission
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();

                            var action = actionTypeInput.value;
                            var comment = document.getElementById('actionComment').value.trim();
                            var selectedPhoto = fileInput && fileInput.files ? fileInput.files[0] : null;
                            var deliveryDateValue = deliveryDateInput ? deliveryDateInput.value.trim() : '';
                            var deliveryPreferredTimeValue = deliveryPreferredTimeInput ? deliveryPreferredTimeInput.value.trim() : '';

                            if (!comment) {
                                showToast('Comment is required.', 'error');
                                return;
                            }

                            if (action === 'reschulde' && !deliveryDateValue) {
                                showToast('Delivery date is required for reschedule.', 'error');
                                return;
                            }

                            if (action === 'reschulde' && !isFutureDateOnly(deliveryDateValue)) {
                                showToast('Please select a future date only.', 'error');
                                return;
                            }

                            if (action === 'reschulde' && !deliveryPreferredTimeValue) {
                                showToast('Delivery preferred time is required for reschedule.', 'error');
                                return;
                            }

                            if (action === 'deliverd' && !selectedPhoto) {
                                showToast('Delivery photo is required.', 'error');
                                return;
                            }

                            // Disable submit button and show loading text
                            submitBtn.disabled = true;
                            var originalBtnText = submitBtn.textContent;
                            submitBtn.textContent = 'Processing...';

                            var formData = new FormData(form);

                            // Fetch post to /api/order/{action_type}/
                            fetch('/api/order/' + action + '/', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(function(response) {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(function(data) {
                                    if (data.status === 200 || data.status === 'success') {
                                        showToast(data.msg || 'Action processed successfully!', 'success');
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 1500);
                                    } else {
                                        showToast(data.msg || 'Failed to process action.', 'error');
                                        submitBtn.disabled = false;
                                        submitBtn.textContent = originalBtnText;
                                    }
                                })
                                .catch(function(error) {
                                    console.error('Error submitting order action:', error);
                                    showToast('Something went wrong. Please try again.', 'error');
                                    submitBtn.disabled = false;
                                    submitBtn.textContent = originalBtnText;
                                });
                        });
                    })();
                </script>


                <script>
                    (function() {
                        var root = document.querySelector('.order-info-ui');

                        if (!root) {
                            return;
                        }

                        var orderId = root.getAttribute('data-order-id') || 'default';
                        var storagePrefix = 'order-ui-collapse-' + orderId + '-';
                        var toggleList = root.querySelectorAll('.order-collapse-toggle');

                        function saveState(key, value) {
                            try {
                                localStorage.setItem(key, value);
                            } catch (e) {
                                // Ignore storage errors silently.
                            }
                        }

                        function readState(key) {
                            try {
                                return localStorage.getItem(key);
                            } catch (e) {
                                return null;
                            }
                        }

                        function applyState(panel, toggle, isOpen) {
                            panel.classList.toggle('is-open', isOpen);
                            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        }

                        for (var i = 0; i < toggleList.length; i++) {
                            (function(toggle) {
                                var targetId = toggle.getAttribute('data-target');
                                var panel = toggle.parentNode;
                                var storageKey = storagePrefix + targetId;
                                var savedValue = readState(storageKey);

                                if (savedValue !== null) {
                                    applyState(panel, toggle, savedValue === 'open');
                                }

                                toggle.addEventListener('click', function() {
                                    var targetId = this.getAttribute('data-target');
                                    var body = root.querySelector('#' + targetId);

                                    if (!body) {
                                        return;
                                    }

                                    var panel = this.parentNode;
                                    var isOpen = panel.classList.contains('is-open');
                                    var nextOpen = !isOpen;

                                    applyState(panel, this, nextOpen);
                                    saveState(storagePrefix + targetId, nextOpen ? 'open' : 'closed');
                                });
                            })(toggleList[i]);
                        }
                    })();
                </script>

                <script>
                    window.allProductsList = <?php echo json_encode($productListData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                </script>

<?php
            }
        } else {

            echo 'no order found';
        }
    } else {

        echo "Invalid data received";
    }
}

?>