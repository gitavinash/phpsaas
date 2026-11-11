<?php

$rawStatus = isset($_GET['status']) ? trim((string)$_GET['status']) : 'all';
$allowedStatuses = ['all', '0', '1', '2', '3', '4', '5', '6'];
$statusParam = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'all';
$typeParam = isset($_GET['type']) ? trim((string)$_GET['type']) : 'list';

$todayIso = date('Y-m-d');
$defaultStartIso = date('Y-m-d', strtotime('-29 days', strtotime($todayIso)));
$defaultEndIso = $todayIso;
$todayDisplay = date('d/m/Y', strtotime($todayIso));
$defaultStartDisplay = date('d/m/Y', strtotime($defaultStartIso));
$defaultEndDisplay = $todayDisplay;

$allDatesGet = strtolower(trim((string)($_GET['all_dates'] ?? ($_GET['date_scope'] ?? ''))));
$isAllDatesInitial = in_array($allDatesGet, ['1', 'true', 'yes', 'all'], true)
    && empty($_GET['start_date'])
    && empty($_GET['end_date']);
$initialStartDisplay = $isAllDatesInitial ? '' : $defaultStartDisplay;
$initialEndDisplay = $isAllDatesInitial ? '' : $defaultEndDisplay;
$initialSubtitle = $isAllDatesInitial
    ? 'Showing all dates. Filter or reset to Last 30 Days.'
    : ('Showing Last 30 Days (' . $defaultStartDisplay . ' – ' . $defaultEndDisplay . '). Filter or export.');

$statusPills = [
    ['code' => 'all', 'label' => 'All'],
    ['code' => '0',   'label' => 'Pending'],
    ['code' => '1',   'label' => 'Confirm'],
    ['code' => '4',   'label' => 'Waiting'],
    ['code' => '6',   'label' => 'Delivered'],
    ['code' => '3',   'label' => 'Cancel'],
    ['code' => '2',   'label' => 'Reject'],
    ['code' => '5',   'label' => 'RTO'],
];
?>
<div class="crm-order-list" id="crmOrderListApp"
    data-list-api="/api/crm/order/list/"
    data-export-api="/api/crm/order/export/"
    data-pdf-plugin-base="/lib/resource/ui/plugin/datatable/js/"
    data-default-status="<?php echo htmlspecialchars($statusParam, ENT_QUOTES, 'UTF-8'); ?>"
    data-default-limit="15"
    data-today="<?php echo htmlspecialchars($todayIso, ENT_QUOTES, 'UTF-8'); ?>"
    data-default-start-date="<?php echo htmlspecialchars($defaultStartIso, ENT_QUOTES, 'UTF-8'); ?>"
    data-default-end-date="<?php echo htmlspecialchars($defaultEndIso, ENT_QUOTES, 'UTF-8'); ?>"
    data-view="mobile">

    <div class="crm-order-header">
        <div class="crm-header-copy">
            <h1 class="crm-page-title">Orders</h1>
            <p class="crm-page-subtitle" id="crmPageSubtitle"><?php echo htmlspecialchars($initialSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="crm-export-toolbar" role="toolbar" focusgroup="toolbar wrap" aria-label="Order exports" aria-controls="crmOrdersTable">
            <div class="crm-export-group" role="group" aria-label="Export files">
                <button type="button" class="crm-btn crm-btn-pdf" id="crmExportPdfBtn" aria-disabled="false">
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span class="crm-btn-label">PDF</span>
                </button>
                <button type="button" class="crm-btn crm-btn-csv" id="crmExportCsvBtn" aria-disabled="false">
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span class="crm-btn-label">CSV</span>
                </button>
                <button type="button" class="crm-btn crm-btn-xlsx" id="crmExportXlsxBtn" aria-disabled="false">
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    <span class="crm-btn-label">Excel</span>
                </button>
            </div>
            <span class="crm-toolbar-sep" role="separator" aria-orientation="vertical"></span>
            <div class="crm-export-group" role="group" aria-label="Print report">
                <button type="button" class="crm-btn crm-btn-print" id="crmExportPrintBtn" aria-disabled="false">
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    <span class="crm-btn-label">Print</span>
                </button>
            </div>
        </div>
    </div>

    <div class="crm-panel">
        <div class="crm-summary-grid">
            <div class="crm-summary-card is-orders">
                <div class="crm-summary-head">
                    <span class="crm-summary-label">Matching Orders</span>
                    <span class="crm-summary-icon" aria-hidden="true">
                        <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                </div>
                <div class="crm-summary-value" id="crmSummaryTotalOrders">-</div>
            </div>
            <div class="crm-summary-card is-revenue">
                <div class="crm-summary-head">
                    <span class="crm-summary-label">Filtered Value</span>
                    <span class="crm-summary-icon" aria-hidden="true">
                        <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12"/><path d="M6 8h12"/><path d="m6 13 8.5 8"/><path d="M6 13h3"/><path d="M9 13c6.667 0 6.667-10 0-10"/></svg>
                    </span>
                </div>
                <div class="crm-summary-value" id="crmSummaryRevenue">-</div>
            </div>
            <div class="crm-summary-card is-pending">
                <div class="crm-summary-head">
                    <span class="crm-summary-label">Pending</span>
                    <span class="crm-summary-icon" aria-hidden="true">
                        <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                </div>
                <div class="crm-summary-value" id="crmSummaryPending">-</div>
            </div>
            <div class="crm-summary-card is-delivered">
                <div class="crm-summary-head">
                    <span class="crm-summary-label">Delivered</span>
                    <span class="crm-summary-icon" aria-hidden="true">
                        <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                </div>
                <div class="crm-summary-value" id="crmSummaryDelivered">-</div>
            </div>
        </div>

        <div class="crm-controls-bar">
            <div class="crm-status-pills" role="group" aria-label="Quick status filters">
                <?php foreach ($statusPills as $pill) {
                    $isActive = ($statusParam === $pill['code']) ? 'true' : 'false'; ?>
                    <button type="button"
                        class="crm-status-pill"
                        data-status="<?php echo htmlspecialchars($pill['code'], ENT_QUOTES, 'UTF-8'); ?>"
                        aria-pressed="<?php echo $isActive; ?>">
                        <span><?php echo htmlspecialchars($pill['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="crm-pill-count">0</span>
                    </button>
                <?php } ?>
            </div>

            <div class="crm-filter-controls">
                <button type="button" class="crm-btn crm-btn-ghost crm-quick-reset-btn" id="crmQuickResetBtn" hidden>
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    <span>Reset</span>
                </button>
                <button type="button"
                    class="crm-btn crm-filter-toggle-btn"
                    id="crmFilterToggleBtn"
                    aria-expanded="false"
                    aria-controls="crmFilterDrawer">
                    <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filters</span>
                    <span class="crm-filter-count-badge" id="crmFilterActiveCount" hidden>0</span>
                    <svg aria-hidden="true" focusable="false" class="crm-chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
            </div>
        </div>

        <div class="crm-active-filters" id="crmActiveFiltersBar" hidden aria-label="Applied filters">
            <span class="crm-active-filters-label">Applied:</span>
            <div class="crm-active-chips" id="crmActiveChipsList"></div>
            <button type="button" class="crm-clear-all-link" id="crmChipsClearAllBtn">Clear all</button>
        </div>

        <search id="crmFilterDrawer" class="crm-filter-drawer" hidden aria-labelledby="crmFilterDrawerHeading">
            <div class="crm-drawer-header">
                <div class="crm-drawer-title-wrap">
                    <h2 id="crmFilterDrawerHeading" class="crm-drawer-title">Filter &amp; Sort Orders</h2>
                    <p class="crm-drawer-hint">Narrow orders by keyword, date range, payment, or city.</p>
                </div>
                <button type="button" class="crm-drawer-close" id="crmFilterCloseBtn" aria-label="Close filter panel">
                    <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form class="crm-filter-form" id="crmOrderFilterForm" method="GET" action="order/" aria-labelledby="crmFilterDrawerHeading">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($typeParam, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="all_dates" id="crmFilterAllDates" value="<?php echo $isAllDatesInitial ? '1' : ''; ?>">
                <div class="crm-filter-grid">
                    <div class="crm-field crm-field-search">
                        <label for="crmFilterSearch">Search Order ID, Product, Sender, Receiver, Mobile, City</label>
                        <input type="search" id="crmFilterSearch" name="q" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" placeholder="Search order ID, name, phone, city...">
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterStatus">Order Status</label>
                        <select id="crmFilterStatus" name="status">
                            <option value="all" <?php echo ($statusParam === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="0" <?php echo ($statusParam === '0') ? 'selected' : ''; ?>>Pending</option>
                            <option value="1" <?php echo ($statusParam === '1') ? 'selected' : ''; ?>>Confirm</option>
                            <option value="2" <?php echo ($statusParam === '2') ? 'selected' : ''; ?>>Reject</option>
                            <option value="3" <?php echo ($statusParam === '3') ? 'selected' : ''; ?>>Cancel</option>
                            <option value="4" <?php echo ($statusParam === '4') ? 'selected' : ''; ?>>Waiting</option>
                            <option value="5" <?php echo ($statusParam === '5') ? 'selected' : ''; ?>>RTO</option>
                            <option value="6" <?php echo ($statusParam === '6') ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterDateBasis">Date Filter Type</label>
                        <select id="crmFilterDateBasis" name="date_basis">
                            <option value="created">Order Created Date</option>
                            <option value="delivery">Delivery Date</option>
                        </select>
                    </div>

                    <div class="crm-field crm-field-date" id="crmStartDateField">
                        <label for="crmFilterStartDate">From Date</label>
                        <span id="crmStartDateHint" class="crm-field-hint">DD/MM/YYYY · up to <?php echo htmlspecialchars($todayDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                        <div class="crm-date-input-wrap">
                            <input type="text"
                                id="crmFilterStartDate"
                                name="start_date"
                                class="crm-date-input"
                                value="<?php echo htmlspecialchars($initialStartDisplay, ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="DD/MM/YYYY"
                                inputmode="numeric"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="none"
                                spellcheck="false"
                                enterkeyhint="next"
                                maxlength="10"
                                aria-invalid="false"
                                aria-describedby="crmStartDateHint"
                                data-error="crmStartDateError"
                                data-date-role="start">
                            <button type="button"
                                id="crmStartDateTrigger"
                                class="crm-date-trigger"
                                aria-label="Open calendar for From Date"
                                aria-haspopup="dialog"
                                aria-expanded="false"
                                aria-controls="crmDatepickerPopover"
                                data-date-target="start">
                                <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </button>
                        </div>
                        <div id="crmStartDateError" class="crm-field-error" hidden>
                            <span class="crm-error-icon" role="img" aria-label="Error">!</span>
                            <span class="crm-error-text"></span>
                        </div>
                    </div>

                    <div class="crm-field crm-field-date" id="crmEndDateField">
                        <label for="crmFilterEndDate">To Date</label>
                        <span id="crmEndDateHint" class="crm-field-hint">DD/MM/YYYY · up to <?php echo htmlspecialchars($todayDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                        <div class="crm-date-input-wrap">
                            <input type="text"
                                id="crmFilterEndDate"
                                name="end_date"
                                class="crm-date-input"
                                value="<?php echo htmlspecialchars($initialEndDisplay, ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="DD/MM/YYYY"
                                inputmode="numeric"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="none"
                                spellcheck="false"
                                enterkeyhint="done"
                                maxlength="10"
                                aria-invalid="false"
                                aria-describedby="crmEndDateHint"
                                data-error="crmEndDateError"
                                data-date-role="end">
                            <button type="button"
                                id="crmEndDateTrigger"
                                class="crm-date-trigger"
                                aria-label="Open calendar for To Date"
                                aria-haspopup="dialog"
                                aria-expanded="false"
                                aria-controls="crmDatepickerPopover"
                                data-date-target="end">
                                <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </button>
                        </div>
                        <div id="crmEndDateError" class="crm-field-error" hidden>
                            <span class="crm-error-icon" role="img" aria-label="Error">!</span>
                            <span class="crm-error-text"></span>
                        </div>
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterPaymentMode">Payment Mode</label>
                        <select id="crmFilterPaymentMode" name="payment_mode">
                            <option value="all">All Payment Modes</option>
                        </select>
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterCity">Delivery City</label>
                        <select id="crmFilterCity" name="city">
                            <option value="all">All Cities</option>
                        </select>
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterSort">Sort Orders</label>
                        <select id="crmFilterSort" name="sort_by">
                            <option value="newest">Order ID (Newest First)</option>
                            <option value="oldest">Order ID (Oldest First)</option>
                            <option value="delivery_desc">Delivery Date (Latest First)</option>
                            <option value="delivery_asc">Delivery Date (Earliest First)</option>
                            <option value="amount_desc">Order Total (High to Low)</option>
                            <option value="amount_asc">Order Total (Low to High)</option>
                        </select>
                    </div>

                    <div class="crm-field">
                        <label for="crmFilterLimit">Rows Per Page</label>
                        <select id="crmFilterLimit" name="limit">
                            <option value="10">10</option>
                            <option value="15" selected>15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="crm-filter-footer">
                    <div class="crm-presets" role="group" aria-label="Quick date presets">
                        <span class="crm-presets-label">Quick Dates:</span>
                        <button type="button" class="crm-preset-chip" data-preset="today" aria-pressed="false">Today</button>
                        <button type="button" class="crm-preset-chip" data-preset="yesterday" aria-pressed="false">Yesterday</button>
                        <button type="button" class="crm-preset-chip" data-preset="last7" aria-pressed="false">Last 7 Days</button>
                        <button type="button" class="crm-preset-chip" data-preset="last30" aria-pressed="<?php echo $isAllDatesInitial ? 'false' : 'true'; ?>">Last 30 Days</button>
                        <button type="button" class="crm-preset-chip" data-preset="month" aria-pressed="false">This Month</button>
                        <button type="button" class="crm-preset-chip" data-preset="all" aria-pressed="<?php echo $isAllDatesInitial ? 'true' : 'false'; ?>">All Dates</button>
                    </div>

                    <div class="crm-filter-actions">
                        <button type="button" class="crm-btn" id="crmFilterResetBtn">Reset</button>
                        <button type="button" class="crm-btn crm-btn-ghost" id="crmFilterCancelBtn">Close</button>
                        <button type="submit" class="crm-btn crm-btn-primary" id="crmFilterApplyBtn">
                            <svg aria-hidden="true" focusable="false" class="crm-btn-icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Apply Filters</span>
                        </button>
                    </div>
                </div>
            </form>

            <div id="crmDatepickerPopover"
                class="crm-datepicker-popover"
                role="dialog"
                aria-modal="false"
                aria-labelledby="crmDpTitle"
                hidden>
                <div class="crm-dp-header">
                    <h3 id="crmDpTitle" class="crm-dp-title">Select Date</h3>
                    <button type="button" id="crmDpCloseBtn" class="crm-dp-close" aria-label="Close calendar">
                        <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="crm-dp-nav">
                    <button type="button" id="crmDpPrevBtn" class="crm-dp-nav-btn" aria-label="Previous month">
                        <svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div class="crm-dp-ym">
                        <label for="crmDpMonthSelect" class="visually-hidden">Month</label>
                        <select id="crmDpMonthSelect" class="crm-dp-select"></select>
                        <label for="crmDpYearSelect" class="visually-hidden">Year</label>
                        <select id="crmDpYearSelect" class="crm-dp-select"></select>
                    </div>
                    <button type="button" id="crmDpNextBtn" class="crm-dp-nav-btn" aria-label="Next month">
                        <svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
                <div class="crm-dp-weekdays" aria-hidden="true">
                    <span>Su</span>
                    <span>Mo</span>
                    <span>Tu</span>
                    <span>We</span>
                    <span>Th</span>
                    <span>Fr</span>
                    <span>Sa</span>
                </div>
                <div id="crmDpDays" class="crm-dp-days" role="group" aria-label="Calendar days"></div>
                <div class="crm-dp-footer">
                    <span class="crm-dp-note" id="crmDpNote">Future dates are disabled</span>
                    <div class="crm-dp-footer-actions">
                        <button type="button" id="crmDpTodayBtn" class="crm-dp-foot-btn">Today</button>
                        <button type="button" id="crmDpClearBtn" class="crm-dp-foot-btn">Clear</button>
                    </div>
                </div>
            </div>
        </search>

        <p id="crmOrderStatusRegion" class="crm-status-region is-idle" role="status" aria-live="polite"></p>

        <div tabindex="0" role="region" aria-labelledby="crmOrdersTableCaption" class="crm-table-wrapper">
            <table id="crmOrdersTable" class="crm-orders-table">
                <caption id="crmOrdersTableCaption">Orders Directory</caption>
                <thead>
                    <tr>
                        <th scope="col">Order</th>
                        <th scope="col">Product</th>
                        <th scope="col">Sender</th>
                        <th scope="col">Receiver</th>
                        <th scope="col">Delivery</th>
                        <th scope="col">Order Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="crmOrdersTableBody">
                    <tr>
                        <td colspan="7" class="crm-empty-cell">Loading orders...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="crm-pagination-bar">
            <div id="crmPaginationInfo" class="crm-pagination-info"></div>
            <nav class="crm-pagination-controls" id="crmPaginationControls" aria-label="Orders pagination"></nav>
        </div>
    </div>
</div>
