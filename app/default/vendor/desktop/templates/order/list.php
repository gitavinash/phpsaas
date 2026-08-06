<?php

if (VENDOR_ID == 0) {
    require_once $dashboardDirectory . '403.php';
}

$statusParam = isset($_GET['status']) ? $_GET['status'] : 'all';
$typeParam = isset($_GET['type']) ? $_GET['type'] : 'list';
$sortParam = isset($_GET['sort']) ? strtolower(trim($_GET['sort'])) : 'recent';

$allowedSorts = array('recent', 'oldest');
if (!in_array($sortParam, $allowedSorts, true)) {
    $sortParam = 'recent';
}

$sortDirection = ($sortParam === 'oldest') ? 'ASC' : 'DESC';
$orderByClause = "ORDER BY order_generate {$sortDirection}, order_id {$sortDirection}";
$siteId = "IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
$isApp32 = ((int)SITE_APP_ID === 32);

$orderStatus = mysqli_real_escape_string($connection, $statusParam);
$orderStatus = (($orderStatus == 'all') ? "" : "AND order_status='{$orderStatus}'");

?>

<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/buttons.min.css">

<style>
    .options-bar {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        width: 100% !important;
        margin: 15px 0 !important;
        float: none !important;
        clear: both !important;
    }

    .options-bar .left-box {
        float: none !important;
        width: auto !important;
        display: flex !important;
        align-items: center !important;
    }

    .options-bar .right-box {
        float: none !important;
        width: auto !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
    }

    .export-btn-group {
        display: inline-flex !important;
        gap: 8px !important;
    }

    .btn-export {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 6px 14px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        border-radius: 4px !important;
        border: 1px solid #dcdfe6 !important;
        background-color: #ffffff !important;
        color: #333333 !important;
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
        text-decoration: none !important;
        line-height: 1.4 !important;
    }

    .btn-export:hover {
        background-color: #f5f7fa !important;
        border-color: #c0c4cc !important;
    }

    .btn-export-excel {
        color: #1e7e34 !important;
        border-color: #28a745 !important;
    }

    .btn-export-excel:hover {
        background-color: #28a745 !important;
        color: #ffffff !important;
    }

    .btn-export-pdf {
        color: #bd2130 !important;
        border-color: #dc3545 !important;
    }

    .btn-export-pdf:hover {
        background-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .btn-export-csv {
        color: #0062cc !important;
        border-color: #007bff !important;
    }

    .btn-export-csv:hover {
        background-color: #007bff !important;
        color: #ffffff !important;
    }
</style>

<div class="modern-order-list">
    <h2>All Orders</h2>
    <div class="panel">
        <div class="options-bar">
            <div class="left-box">
                <form method="GET">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($typeParam); ?>">
                    <select class="form-control" name="status" id="changeStatus">
                        <option value="all" <?php echo (($statusParam == 'all') ? 'selected' : '') ?>>All
                            <?php echo $postType ?></option>
                        <option value="0" <?php echo (($statusParam == '0') ? 'selected' : '') ?>>Pending
                            <?php echo $postType ?></option>
                        <option value="1" <?php echo (($statusParam == '1') ? 'selected' : '') ?>>Confirm
                            <?php echo $postType ?></option>
                        <option value="2" <?php echo (($statusParam == '2') ? 'selected' : '') ?>>Reject
                            <?php echo $postType ?></option>
                        <option value="3" <?php echo (($statusParam == '3') ? 'selected' : '') ?>>Cancel
                            <?php echo $postType ?></option>
                        <option value="4" <?php echo (($statusParam == '4') ? 'selected' : '') ?>>Waiting
                            <?php echo $postType ?></option>
                        <option value="5" <?php echo (($statusParam == '5') ? 'selected' : '') ?>>RTO
                            <?php echo $postType ?>
                        </option>
                        <option value="6" <?php echo (($statusParam == '6') ? 'selected' : '') ?>>Delivered
                            <?php echo $postType ?></option>
                    </select>
                    <select class="form-control" name="sort" id="changeSort">
                        <option value="recent" <?php echo (($sortParam == 'recent') ? 'selected' : '') ?>>Newest Received First</option>
                        <option value="oldest" <?php echo (($sortParam == 'oldest') ? 'selected' : '') ?>>Oldest Received First</option>
                    </select>
                    <input type="submit" class="btn" value="Filter">
                </form>
            </div>
            <div class="right-box">
                <div class="export-btn-group">
                    <button type="button" id="exportExcelBtn" class="btn-export btn-export-excel" title="Export to Excel">
                        <i class="far fa-file-excel"></i> Export Orders to Excel
                    </button>
                </div>
            </div>
        </div>
        <table id="table" data-default-order-column="4" data-default-order-dir="desc">
            <thead>
                <tr>
                    <th style="width: 480px">Order</th>
                    <th style="width: 130px">Amount</th>
                    <th style="width: 120px">Payment</th>
                    <th style="width: 120px">Status</th>
                    <th style="width: 140px">Received Date</th>
                    <th style="width: 110px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasOrders = false;

                $query = "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_qty, inventory_currency_symbol, order_product_price, order_delivery_charges, order_currency, order_total_price, order_payment_mode, order_generate, order_status, address_mobile, product_title, product_image FROM " . TABLE_PREFIX . "order LEFT JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_site_id " . $siteId . " LEFT JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id" . ($isApp32 ? "" : " AND product_type='product'") . " WHERE order_vendor_id='" . VENDOR_ID . "' AND order_site_id " . $siteId . " " . ($isApp32 ? "" : "AND order_product_id!='' AND product_type='product' ") . "{$orderStatus} {$orderByClause} LIMIT 0, 1000";

                if ($dataList = mysqli_query($connection, $query)) {

                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            $hasOrders = true;

                            extract($data);

                            $statusClass = 'status-pending';
                            switch ($order_status) {

                                case 0:
                                    $order_status = 'Pending';
                                    $statusClass = 'status-pending';
                                    break;

                                case 1:
                                    $order_status = 'Confirm';
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

                                case 5:
                                    $order_status = 'RTO';
                                    $statusClass = 'status-rto';
                                    break;

                                case 6:
                                    $order_status = 'Delivered';
                                    $statusClass = 'status-delivered';
                                    break;
                            }

                            $orderAmount = (($order_total_price != '') ? $inventory_currency_symbol : '') . ' ' . $order_total_price;
                            $paymentLabel = ucwords(str_replace('_', ' ', trim($order_payment_mode)));
                            if ($paymentLabel == '') {
                                $paymentLabel = 'N/A';
                            }

                            $initials = 'OD';
                            if (trim($profile_display_name) != '') {
                                $nameParts = preg_split('/\s+/', trim($profile_display_name));
                                $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                $secondInitial = (isset($nameParts[1]) && $nameParts[1] != '') ? strtoupper(substr($nameParts[1], 0, 1)) : '';
                                $initials = $firstInitial . $secondInitial;
                            }

                            $productBadgeLabel = 'N/A';
                            $productImageSrc = '';
                            $productNameLabel = 'N/A';
                            $hasProduct = (trim((string)$order_product_id) !== '' && trim((string)$order_product_id) !== '0');
                            if ($hasProduct) {
                                if (trim((string)$product_title) !== '') {
                                    $productNameLabel = trim((string)$product_title);
                                }
                                if (trim((string)$product_image) !== '') {
                                    $productImageSrc = trim((string)$product_image);
                                }
                            }

                ?>
                            <tr data-name="<?php echo htmlspecialchars($profile_display_name, ENT_QUOTES); ?>"
                                data-order-id="<?php echo htmlspecialchars('OD' . $order_id, ENT_QUOTES); ?>"
                                data-product-name="<?php echo htmlspecialchars($productNameLabel, ENT_QUOTES); ?>"
                                data-mobile-number="<?php echo htmlspecialchars($address_mobile, ENT_QUOTES); ?>"
                                data-status="<?php echo htmlspecialchars($order_status, ENT_QUOTES); ?>">
                                <td>
                                    <div class="order-cell">
                                        <div class="order-badge">
                                            <?php if ($productImageSrc != '') { ?>
                                                <img src="<?php echo htmlspecialchars($productImageSrc); ?>" alt="Product image">
                                            <?php } else { ?>
                                                <span class="order-badge-fallback">N/A</span>
                                            <?php } ?>
                                        </div>
                                        <div class="order-meta">
                                            <p>
                                                <a class="order-title-link"
                                                    href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><?php echo 'OD' . $order_id; ?>
                                                    <span class="order-ids"> |
                                                        <?php echo htmlspecialchars($profile_display_name); ?></span>
                                                </a>
                                            </p>
                                            <div class="order-product-name">
                                                Product: <?php echo htmlspecialchars($productNameLabel); ?>
                                            </div>
                                            <div class="order-tags">
                                                <span class="order-tag"><?php echo htmlspecialchars($address_mobile); ?></span>
                                                <span
                                                    class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span>
                                                <span class="order-tag"><?php echo htmlspecialchars($paymentLabel); ?></span>
                                            </div>
                                            <!-- <div class="hover">
                                                <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>"><i
                                                        class="fas fa-eye"></i>
                                                    View</a>
                                            </div> -->
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-main"><?php echo $orderAmount; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($paymentLabel); ?></td>
                                <td><span class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span></td>
                                <td data-order="<?php echo (int)strtotime($order_generate); ?>"><?php echo date('d/m/Y h:i A', strtotime($order_generate)); ?></td>
                                <td>
                                    <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>" class="order-view-btn"><i
                                            class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                }

                if (!$hasOrders) {
                    echo '<tr><td colspan="6"><div class="empty-order-state">No orders found for this filter.</div></td></tr>';
                }

                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    (function() {
        var pluginPath = '<?php echo PLUGIN_LIBRARY ?>datatable/js/';

        function loadScript(src) {
            return new Promise(function(resolve, reject) {
                if (document.querySelector('script[src="' + src + '"]')) {
                    resolve();
                    return;
                }
                var s = document.createElement('script');
                s.src = src;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        function setupExportButtons() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                return;
            }

            var $table = $('#table');
            if (!$table.length) {
                return;
            }

            var table;
            if ($.fn.DataTable.isDataTable('#table')) {
                table = $table.DataTable();
            } else {
                var defaultOrderColumn = parseInt($table.attr("data-default-order-column"), 10);
                var defaultOrderDir = $table.attr("data-default-order-dir") || "desc";
                var pageLength = parseInt($table.attr("data-page-length"), 10) || 10;
                table = $table.DataTable({
                    bDestroy: true,
                    bStateSave: false,
                    pageLength: pageLength,
                    order: [
                        [!isNaN(defaultOrderColumn) ? defaultOrderColumn : 4, defaultOrderDir]
                    ]
                });
            }

            var exportButtonsCommon = {
                customizeData: function(data) {
                    data.header = ['Order ID', 'Client Name', 'Mobile Number', 'Product Name', 'Order Status'];
                    data.body = [];

                    var api = $('#table').DataTable();
                    var rows = api.rows({
                        search: 'applied',
                        order: 'applied'
                    }).nodes();

                    $(rows).each(function() {
                        var $tr = $(this);
                        var orderId = $tr.attr('data-order-id') || '';
                        var name = $tr.attr('data-name') || '';
                        var mobileNumber = $tr.attr('data-mobile-number') || '';
                        var productName = $tr.attr('data-product-name') || '';
                        var status = $tr.attr('data-status') || '';

                        data.body.push([orderId, name, mobileNumber, productName, status]);
                    });
                }
            };

            var dtButtons = new $.fn.dataTable.Buttons(table, {
                buttons: [
                    $.extend(true, {}, exportButtonsCommon, {
                        extend: 'excelHtml5',
                        title: 'Orders_List_' + new Date().toISOString().slice(0, 10),
                        className: 'dt-btn-excel-export'
                    }),
                    $.extend(true, {}, exportButtonsCommon, {
                        extend: 'pdfHtml5',
                        title: 'Orders List',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'dt-btn-pdf-export',
                        customize: function(doc) {
                            if (doc.content && doc.content[1] && doc.content[1].table) {
                                doc.content[1].table.widths = ['15%', '22%', '18%', '30%', '15%'];
                            }
                            if (doc.styles && doc.styles.tableHeader) {
                                doc.styles.tableHeader.alignment = 'left';
                                doc.styles.tableHeader.fillColor = '#00609e';
                            }
                        }
                    }),
                    $.extend(true, {}, exportButtonsCommon, {
                        extend: 'csvHtml5',
                        title: 'Orders_List_' + new Date().toISOString().slice(0, 10),
                        className: 'dt-btn-csv-export'
                    })
                ]
            });

            var $btnContainer = $(dtButtons.container());
            $btnContainer.css('display', 'none').appendTo('body');

            $('#exportExcelBtn').off('click').on('click', function(e) {
                e.preventDefault();
                $btnContainer.find('.dt-btn-excel-export').trigger('click');
            });
        }

        function init() {
            Promise.all([
                loadScript(pluginPath + 'jszip.min.js'),
                loadScript(pluginPath + 'pdfmake.min.js'),
                loadScript(pluginPath + 'vfs_fonts.js'),
                loadScript(pluginPath + 'buttons.min.js')
            ]).then(function() {
                return loadScript(pluginPath + 'buttons.html5.min.js');
            }).then(function() {
                setupExportButtons();
            }).catch(function(err) {
                console.error('Error loading DataTable export plugins:', err);
            });
        }

        if (document.readyState === 'complete') {
            init();
        } else {
            window.addEventListener('load', init);
        }
    })();
</script>