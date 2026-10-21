<?php

$statusParam = isset($_GET['status']) ? $_GET['status'] : 'all';
$typeParam = isset($_GET['type']) ? $_GET['type'] : 'list';
$sortParam = isset($_GET['sort']) ? strtolower(trim($_GET['sort'])) : 'recent';

$allowedSorts = array('recent', 'oldest');
if (!in_array($sortParam, $allowedSorts, true)) {
    $sortParam = 'recent';
}

$sortDirection = ($sortParam === 'oldest') ? 'ASC' : 'DESC';
$orderByClause = "ORDER BY effective_delivery_date {$sortDirection}, order_id {$sortDirection}";
// $siteId = "IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
$siteId = "IN (" . SITE_ID . ")";
$isLogisticsApp = (
    (defined('SITE_APP') && strtolower(SITE_APP) === 'logistics') ||
    (isset($site_app) && strtolower($site_app) === 'logistics') ||
    (defined('SITE_APP_ID') && (int)SITE_APP_ID === 32) ||
    (isset($site_app_id) && (int)$site_app_id === 32)
);
$isApp32 = $isLogisticsApp;

$orderStatus = mysqli_real_escape_string($connection, $statusParam);
$orderStatus = (($orderStatus == 'all') ? "" : "AND order_status='{$orderStatus}'");

$companyParam = isset($_GET['company']) ? trim($_GET['company']) : (isset($_GET['company_id']) ? trim($_GET['company_id']) : 'all');
$companyCondition = "";
$companiesList = array();

if ($isLogisticsApp) {
    $currentSiteId = defined('SITE_ID') ? (int)SITE_ID : (isset($site_id) ? (int)$site_id : 0);
    $companySql = "SELECT company_id, company_title FROM " . TABLE_PREFIX . "company WHERE company_site_id = '" . $currentSiteId . "' ORDER BY company_title ASC";
    if ($compRes = mysqli_query($connection, $companySql)) {
        while ($cRow = mysqli_fetch_assoc($compRes)) {
            $companiesList[] = $cRow;
        }
    }

    if ($companyParam !== 'all' && $companyParam !== '') {
        $companyIdSafe = (int)$companyParam;
        if ($companyIdSafe > 0) {
            $companyCondition = "AND order_ref_by = '{$companyIdSafe}' AND order_ref_type = 'company'";
        }
    }
}

// Date Range Filter (By default show current Date Data)
$today = date('Y-m-d');
$isDateFormSubmitted = isset($_GET['date_filter']);
$dateRangeParam = isset($_GET['daterange']) ? trim($_GET['daterange']) : '';
$startDateParam = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$endDateParam = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

$parseDateHelper = function($str) {
    $str = trim((string)$str);
    if ($str === '') return false;
    $d = DateTime::createFromFormat('Y-m-d', $str);
    if ($d && $d->format('Y-m-d') === $str) return $str;
    $d = DateTime::createFromFormat('d/m/Y', $str);
    if ($d && $d->format('d/m/Y') === $str) return $d->format('Y-m-d');
    $t = strtotime(str_replace('/', '-', $str));
    return ($t !== false) ? date('Y-m-d', $t) : false;
};

if ($startDateParam !== '' && $endDateParam !== '') {
    $p1 = $parseDateHelper($startDateParam);
    $p2 = $parseDateHelper($endDateParam);
    if ($p1 !== false && $p2 !== false) {
        $startDate = $p1;
        $endDate = $p2;
    }
} elseif ($dateRangeParam !== '') {
    if (strpos($dateRangeParam, ' - ') !== false) {
        $parts = explode(' - ', $dateRangeParam);
        $p1 = $parseDateHelper($parts[0]);
        $p2 = $parseDateHelper($parts[1]);
        if ($p1 !== false && $p2 !== false) {
            $startDate = $p1;
            $endDate = $p2;
        }
    } else {
        $p1 = $parseDateHelper($dateRangeParam);
        if ($p1 !== false) {
            $startDate = $p1;
            $endDate = $p1;
        }
    }
}

if (!$isDateFormSubmitted && empty($startDate)) {
    // Default to current date on initial load
    $startDate = $today;
    $endDate = $today;
}

if (!empty($startDate) && !empty($endDate)) {
    if ($startDate > $endDate) {
        $tmp = $startDate;
        $startDate = $endDate;
        $endDate = $tmp;
    }
    $startDateEsc = mysqli_real_escape_string($connection, $startDate);
    $endDateEsc = mysqli_real_escape_string($connection, $endDate);
    $dateCondition = "AND COALESCE(dm.delivery_date_val, DATE(order_generate)) BETWEEN '{$startDateEsc}' AND '{$endDateEsc}'";
    $dateRangeDisplay = date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate));
} else {
    $dateCondition = "";
    $dateRangeDisplay = "";
}

?>

<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/buttons.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.css">

<style>
    .options-bar .left-box form {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }

    .date-range-input {
        min-width: 210px !important;
        cursor: pointer !important;
        text-align: center !important;
        background-color: #ffffff !important;
    }
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
                    <input type="hidden" name="date_filter" value="1">
                    <input type="hidden" name="start_date" id="startDateInput" value="<?php echo htmlspecialchars($startDate); ?>">
                    <input type="hidden" name="end_date" id="endDateInput" value="<?php echo htmlspecialchars($endDate); ?>">
                    <select class="form-control" name="status" id="changeStatus">
                        <option value="all" <?php echo (($statusParam == 'all') ? 'selected' : '') ?>>All</option>
                        <option value="0" <?php echo (($statusParam == '0') ? 'selected' : '') ?>>Pending</option>
                        <option value="1" <?php echo (($statusParam == '1') ? 'selected' : '') ?>>Order Accepted by Vendor</option>
                        <option value="2" <?php echo (($statusParam == '2') ? 'selected' : '') ?>>Assigned to Vendor</option>
                        <option value="3" <?php echo (($statusParam == '3') ? 'selected' : '') ?>>Processing</option>
                        <option value="4" <?php echo (($statusParam == '4') ? 'selected' : '') ?>>Shipped</option>
                        <option value="5" <?php echo (($statusParam == '5') ? 'selected' : '') ?>>Out for Delivery</option>
                        <option value="6" <?php echo (($statusParam == '6') ? 'selected' : '') ?>>Delivered</option>
                        <option value="7" <?php echo (($statusParam == '7') ? 'selected' : '') ?>>Cancelled</option>
                        <option value="8" <?php echo (($statusParam == '8') ? 'selected' : '') ?>>Returned</option>
                    </select>
                    <?php if ($isLogisticsApp) { ?>
                        <select class="form-control" name="company" id="changeCompany">
                            <option value="all" <?php echo (($companyParam === 'all' || $companyParam === '') ? 'selected' : ''); ?>>All Companies</option>
                            <?php foreach ($companiesList as $comp) {
                                $cId = (int)$comp['company_id'];
                                $cTitle = trim($comp['company_title']) !== '' ? $comp['company_title'] : ('Company #' . $cId);
                            ?>
                                <option value="<?php echo $cId; ?>" <?php echo (((string)$companyParam === (string)$cId) ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($cTitle); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <input type="text" class="form-control date-range-input" name="daterange" id="changeDateRange" value="<?php echo htmlspecialchars($dateRangeDisplay); ?>" placeholder="Select Delivery Date" autocomplete="off" title="Filter by Delivery Date">
                    <select class="form-control" name="sort" id="changeSort">
                        <option value="recent" <?php echo (($sortParam == 'recent') ? 'selected' : '') ?>>Newest Delivery First</option>
                        <option value="oldest" <?php echo (($sortParam == 'oldest') ? 'selected' : '') ?>>Oldest Delivery First</option>
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
        <table id="table" data-default-order-column="3" data-default-order-dir="desc">
            <thead>
                <tr>
                    <th style="width: 480px">Order</th>
                    <th style="width: 140px">Contact No.</th>
                    <th style="width: 130px">Status</th>
                    <th style="width: 140px">Delivery Date</th>
                    <th style="width: 110px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasOrders = false;

                $query = "SELECT profile_user_id, profile_display_name, profile_mobile, order_id, order_product_id, order_product_qty, inventory_currency_symbol, order_delivery_charges, order_currency, order_generate, order_status, address_mobile, product_title, product_image, dm.delivery_date_val, COALESCE(dm.delivery_date_val, DATE(order_generate)) AS effective_delivery_date FROM " . TABLE_PREFIX . "order LEFT JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=order_user_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_site_id " . $siteId . " LEFT JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id" . ($isApp32 ? "" : " AND product_type='product'") . " LEFT JOIN (SELECT order_relation_id, MAX(CASE WHEN order_meta_value LIKE '__/__/____' THEN DATE(STR_TO_DATE(order_meta_value, '%m/%d/%Y')) WHEN order_meta_value LIKE '____-__-__%' THEN DATE(order_meta_value) ELSE NULL END) AS delivery_date_val FROM " . TABLE_PREFIX . "order_meta WHERE order_meta_key = 'deliveryDate' GROUP BY order_relation_id) dm ON dm.order_relation_id = order_id WHERE order_site_id " . $siteId . " " . ($isApp32 ? "" : "AND order_product_id!='' AND product_type='product' ") . "{$companyCondition} {$dateCondition} {$orderStatus} {$orderByClause} LIMIT 0, 1000";

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
                                    $order_status = 'Order Accepted';
                                    $statusClass = 'status-confirm';
                                    break;

                                case 2:
                                    $order_status = 'Assigned to Vendor';
                                    $statusClass = 'status-confirm';
                                    break;

                                case 3:
                                    $order_status = 'Processing';
                                    $statusClass = 'status-waiting';
                                    break;

                                case 4:
                                    $order_status = 'Shipped';
                                    $statusClass = 'status-waiting';
                                    break;

                                case 5:
                                    $order_status = 'Out for Delivery';
                                    $statusClass = 'status-waiting';
                                    break;

                                case 6:
                                    $order_status = 'Delivered';
                                    $statusClass = 'status-delivered';
                                    break;

                                case 7:
                                    $order_status = 'Cancelled';
                                    $statusClass = 'status-cancel';
                                    break;

                                case 8:
                                    $order_status = 'Returned';
                                    $statusClass = 'status-rto';
                                    break;
                            }

                            $effectiveDate = !empty($effective_delivery_date) ? $effective_delivery_date : (!empty($order_generate) ? date('Y-m-d', strtotime($order_generate)) : '');
                            $deliveryDateDisplay = !empty($effectiveDate) ? date('d/m/Y', strtotime($effectiveDate)) : 'N/A';
                            $deliveryTimestamp = !empty($effectiveDate) ? (int)strtotime($effectiveDate) : 0;

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
                                data-status="<?php echo htmlspecialchars($order_status, ENT_QUOTES); ?>"
                                data-delivery-date="<?php echo htmlspecialchars($deliveryDateDisplay, ENT_QUOTES); ?>">
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
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-main"><?php echo htmlspecialchars($address_mobile); ?></div>
                                </td>
                                <td><span class="order-tag <?php echo $statusClass; ?>"><?php echo $order_status; ?></span></td>
                                <td data-order="<?php echo $deliveryTimestamp; ?>"><?php echo $deliveryDateDisplay; ?></td>
                                <td>
                                    <a href="order/?type=view&id=<?php echo base64_encode($order_id); ?>" class="order-view-btn"><i
                                            class="fas fa-eye"></i> View</a>
                                    <a href="/tracking/?orderid=OD<?php echo $order_id; ?>" target="_blank" class="order-view-btn"><i
                                            class="fas fa-route"></i> Track <i class="fas fa-external-link-alt" style="font-size: 10px; margin-left: 3px;"></i></a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                }

                if (!$hasOrders) {
                    echo '<tr><td colspan="5"><div class="empty-order-state">No orders found for this filter.</div></td></tr>';
                }

                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    (function() {
        var pluginPath = '<?php echo PLUGIN_LIBRARY ?>datatable/js/';
        var momentPath = '<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js';
        var daterangePath = '<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js';

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

        function initDateRangePicker() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.daterangepicker || typeof moment === 'undefined') {
                return;
            }

            var $dateInput = $('#changeDateRange');
            if (!$dateInput.length) {
                return;
            }

            var startVal = $('#startDateInput').val();
            var endVal = $('#endDateInput').val();
            var startMoment = startVal ? moment(startVal, 'YYYY-MM-DD') : moment();
            var endMoment = endVal ? moment(endVal, 'YYYY-MM-DD') : moment();

            $dateInput.daterangepicker({
                startDate: startMoment,
                endDate: endMoment,
                autoUpdateInput: Boolean(startVal && endVal),
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Clear',
                    customRangeLabel: 'Custom Range'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Next 7 Days': [moment(), moment().add(6, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
                }
            });

            $dateInput.on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                $('#startDateInput').val(picker.startDate.format('YYYY-MM-DD'));
                $('#endDateInput').val(picker.endDate.format('YYYY-MM-DD'));
                $(this).closest('form').submit();
            });

            $dateInput.on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#startDateInput').val('');
                $('#endDateInput').val('');
                $(this).closest('form').submit();
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
                        [!isNaN(defaultOrderColumn) ? defaultOrderColumn : 3, defaultOrderDir]
                    ]
                });
            }

            var exportButtonsCommon = {
                customizeData: function(data) {
                    data.header = ['Order ID', 'Client Name', 'Mobile Number', 'Product Name', 'Order Status', 'Delivery Date'];
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
                        var deliveryDate = $tr.attr('data-delivery-date') || '';

                        data.body.push([orderId, name, mobileNumber, productName, status, deliveryDate]);
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
                                doc.content[1].table.widths = ['14%', '18%', '16%', '24%', '14%', '14%'];
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
            // Load Date Range Picker dependencies
            loadScript(momentPath).then(function() {
                return loadScript(daterangePath);
            }).then(function() {
                initDateRangePicker();
            }).catch(function(err) {
                console.error('Error loading Date Range Picker plugins:', err);
            });

            // Load DataTable export plugins
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