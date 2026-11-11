(function () {
    'use strict';

    var root = document.getElementById('crmOrderListApp');
    if (!root) {
        return;
    }

    var config = {
        listApi: root.getAttribute('data-list-api') || '/api/crm/order/list/',
        exportApi: root.getAttribute('data-export-api') || '/api/crm/order/export/',
        pdfPluginBase: root.getAttribute('data-pdf-plugin-base') || '/lib/resource/ui/plugin/datatable/js/',
        defaultStatus: root.getAttribute('data-default-status') || 'all',
        defaultLimit: parseInt(root.getAttribute('data-default-limit') || '25', 10) || 25,
        todayIso: root.getAttribute('data-today') || '',
        defaultStartDate: root.getAttribute('data-default-start-date') || '',
        defaultEndDate: root.getAttribute('data-default-end-date') || '',
        viewMode: root.getAttribute('data-view') || 'desktop'
    };

    var els = {
        pageSubtitle: document.getElementById('crmPageSubtitle'),
        filterDrawer: document.getElementById('crmFilterDrawer'),
        filterToggleBtn: document.getElementById('crmFilterToggleBtn'),
        filterCloseBtn: document.getElementById('crmFilterCloseBtn'),
        filterCancelBtn: document.getElementById('crmFilterCancelBtn'),
        filterActiveCount: document.getElementById('crmFilterActiveCount'),
        quickResetBtn: document.getElementById('crmQuickResetBtn'),
        activeFiltersBar: document.getElementById('crmActiveFiltersBar'),
        activeChipsList: document.getElementById('crmActiveChipsList'),
        chipsClearAllBtn: document.getElementById('crmChipsClearAllBtn'),
        filterForm: document.getElementById('crmOrderFilterForm'),
        allDatesInput: document.getElementById('crmFilterAllDates'),
        statusSelect: document.getElementById('crmFilterStatus'),
        dateBasisSelect: document.getElementById('crmFilterDateBasis'),
        startDateInput: document.getElementById('crmFilterStartDate'),
        endDateInput: document.getElementById('crmFilterEndDate'),
        startDateTrigger: document.getElementById('crmStartDateTrigger'),
        endDateTrigger: document.getElementById('crmEndDateTrigger'),
        dpPopover: document.getElementById('crmDatepickerPopover'),
        dpTitle: document.getElementById('crmDpTitle'),
        dpCloseBtn: document.getElementById('crmDpCloseBtn'),
        dpPrevBtn: document.getElementById('crmDpPrevBtn'),
        dpNextBtn: document.getElementById('crmDpNextBtn'),
        dpMonthSelect: document.getElementById('crmDpMonthSelect'),
        dpYearSelect: document.getElementById('crmDpYearSelect'),
        dpDays: document.getElementById('crmDpDays'),
        dpTodayBtn: document.getElementById('crmDpTodayBtn'),
        dpClearBtn: document.getElementById('crmDpClearBtn'),
        paymentSelect: document.getElementById('crmFilterPaymentMode'),
        citySelect: document.getElementById('crmFilterCity'),
        searchInput: document.getElementById('crmFilterSearch'),
        sortSelect: document.getElementById('crmFilterSort'),
        limitSelect: document.getElementById('crmFilterLimit'),
        applyBtn: document.getElementById('crmFilterApplyBtn'),
        resetBtn: document.getElementById('crmFilterResetBtn'),
        presetChips: Array.prototype.slice.call(root.querySelectorAll('.crm-preset-chip[data-preset]')),
        statusPills: Array.prototype.slice.call(root.querySelectorAll('.crm-status-pill[data-status]')),
        exportToolbar: root.querySelector('.crm-export-toolbar'),
        exportColsBtn: document.getElementById('crmExportColumnsBtn'),
        exportColsBadge: document.getElementById('crmExportColsBadge'),
        exportColsDialog: document.getElementById('crmExportColumnsDialog'),
        exportColsCloseBtn: document.getElementById('crmExportColsCloseBtn'),
        exportColsFilterInput: document.getElementById('crmExportColsFilterInput'),
        exportColsDefaultBtn: document.getElementById('crmExportColsDefaultBtn'),
        exportColsAllBtn: document.getElementById('crmExportColsAllBtn'),
        exportColsClearBtn: document.getElementById('crmExportColsClearBtn'),
        exportColsSummary: document.getElementById('crmExportColsSummary'),
        exportColsForm: document.getElementById('crmExportColumnsForm'),
        exportColsGroups: document.getElementById('crmExportColsGroups'),
        exportColsEmptySearch: document.getElementById('crmExportColsEmptySearch'),
        exportColsError: document.getElementById('crmExportColsError'),
        exportColsCancelBtn: document.getElementById('crmExportColsCancelBtn'),
        exportColsSaveBtn: document.getElementById('crmExportColsSaveBtn'),
        exportPdfBtn: document.getElementById('crmExportPdfBtn'),
        exportCsvBtn: document.getElementById('crmExportCsvBtn'),
        exportXlsxBtn: document.getElementById('crmExportXlsxBtn'),
        exportPrintBtn: document.getElementById('crmExportPrintBtn'),
        statusRegion: document.getElementById('crmOrderStatusRegion'),
        summaryTotal: document.getElementById('crmSummaryTotalOrders'),
        summaryRevenue: document.getElementById('crmSummaryRevenue'),
        summaryPending: document.getElementById('crmSummaryPending'),
        summaryDelivered: document.getElementById('crmSummaryDelivered'),
        tableCaption: document.getElementById('crmOrdersTableCaption'),
        tableBody: document.getElementById('crmOrdersTableBody'),
        paginationInfo: document.getElementById('crmPaginationInfo'),
        paginationControls: document.getElementById('crmPaginationControls')
    };

    var state = {
        status: config.defaultStatus,
        date_basis: 'created',
        start_date: '',
        end_date: '',
        all_dates: false,
        payment_mode: 'all',
        city: 'all',
        q: '',
        sort_by: 'newest',
        page: 1,
        limit: config.defaultLimit
    };

    var facetsLoaded = false;
    var activeAbortController = null;
    var pdfLibsPromise = null;
    var isComposingSearch = false;
    var statusClearTimer = null;

    var EXPORT_COLS_STORAGE_KEY = 'crm_orders_export_cols_v1';
    var DEFAULT_EXPORT_COLUMN_CATALOG = [
        // Order Details
        { key: 'order_code', label: 'Order ID', group: 'order', group_label: 'Order Details', is_default: true, pdf_width: 38, align: 'left' },
        { key: 'order_date', label: 'Order Date', group: 'order', group_label: 'Order Details', is_default: true, pdf_width: 42, align: 'left' },
        { key: 'order_time', label: 'Order Time', group: 'order', group_label: 'Order Details', is_default: false, pdf_width: 36, align: 'left' },
        { key: 'order_status', label: 'Order Status', group: 'order', group_label: 'Order Details', is_default: true, pdf_width: 42, align: 'center' },

        // Product & Pricing
        { key: 'product_name', label: 'Product Name', group: 'product', group_label: 'Product & Pricing', is_default: true, pdf_width: 84, align: 'left' },
        { key: 'product_link', label: 'Product Website Link', group: 'product', group_label: 'Product & Pricing', is_default: true, pdf_width: 98, align: 'left' },
        { key: 'product_sku', label: 'Product SKU', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 44, align: 'left' },
        { key: 'item_qty', label: 'Item Qty', group: 'product', group_label: 'Product & Pricing', is_default: true, pdf_width: 24, align: 'right' },
        { key: 'total_qty', label: 'Total Qty', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 26, align: 'right' },
        { key: 'unit_price', label: 'Product Unit Price', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 44, align: 'right' },
        { key: 'product_subtotal', label: 'Product Subtotal', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 44, align: 'right' },
        { key: 'delivery_charges', label: 'Delivery Charges', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 42, align: 'right' },
        { key: 'coupon_code', label: 'Coupon Code', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 38, align: 'left' },
        { key: 'order_price', label: 'Order Price', group: 'product', group_label: 'Product & Pricing', is_default: true, pdf_width: 50, align: 'right' },
        { key: 'currency', label: 'Currency', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 28, align: 'center' },
        { key: 'payment_mode', label: 'Payment Mode', group: 'product', group_label: 'Product & Pricing', is_default: true, pdf_width: 42, align: 'left' },
        { key: 'payment_status', label: 'Payment Status', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 40, align: 'left' },
        { key: 'transaction_ref', label: 'Transaction Ref', group: 'product', group_label: 'Product & Pricing', is_default: false, pdf_width: 64, align: 'left' },

        // Sender Details
        { key: 'sender_name', label: 'Sender Name', group: 'sender', group_label: 'Sender Details', is_default: true, pdf_width: 60, align: 'left' },
        { key: 'sender_mobile', label: 'Sender Mobile', group: 'sender', group_label: 'Sender Details', is_default: true, pdf_width: 48, align: 'left' },
        { key: 'sender_alt_mobile', label: 'Sender Alt Mobile', group: 'sender', group_label: 'Sender Details', is_default: false, pdf_width: 48, align: 'left' },
        { key: 'sender_email', label: 'Sender Email', group: 'sender', group_label: 'Sender Details', is_default: false, pdf_width: 68, align: 'left' },

        // Receiver Details
        { key: 'receiver_name', label: 'Receiver Name', group: 'receiver', group_label: 'Receiver Details', is_default: true, pdf_width: 60, align: 'left' },
        { key: 'receiver_mobile', label: 'Receiver Mobile', group: 'receiver', group_label: 'Receiver Details', is_default: true, pdf_width: 48, align: 'left' },
        { key: 'receiver_alt_mobile', label: 'Receiver Alt Mobile', group: 'receiver', group_label: 'Receiver Details', is_default: false, pdf_width: 48, align: 'left' },
        { key: 'receiver_street_address', label: 'Receiver Street Address', group: 'receiver', group_label: 'Receiver Details', is_default: true, pdf_width: 86, align: 'left' },
        { key: 'receiver_city', label: 'Receiver City', group: 'receiver', group_label: 'Receiver Details', is_default: true, pdf_width: 46, align: 'left' },
        { key: 'receiver_state', label: 'Receiver State', group: 'receiver', group_label: 'Receiver Details', is_default: false, pdf_width: 44, align: 'left' },
        { key: 'receiver_country', label: 'Receiver Country', group: 'receiver', group_label: 'Receiver Details', is_default: false, pdf_width: 38, align: 'left' },
        { key: 'receiver_pin_code', label: 'Receiver Pin Code', group: 'receiver', group_label: 'Receiver Details', is_default: false, pdf_width: 36, align: 'left' },
        { key: 'address_type', label: 'Address Type', group: 'receiver', group_label: 'Receiver Details', is_default: false, pdf_width: 36, align: 'left' },

        // Delivery Details
        { key: 'delivery_date', label: 'Delivery Date', group: 'delivery', group_label: 'Delivery Details', is_default: true, pdf_width: 42, align: 'left' },
        { key: 'delivery_slot', label: 'Delivery Slot', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 42, align: 'left' },
        { key: 'delivery_time', label: 'Delivery Time', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 48, align: 'left' },
        { key: 'occasion', label: 'Occasion', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 42, align: 'left' },
        { key: 'gift_message', label: 'Gift Message', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 84, align: 'left' },
        { key: 'delivery_note', label: 'Delivery Note', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 64, align: 'left' },
        { key: 'return_reason', label: 'Return Reason', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 50, align: 'left' },
        { key: 'return_comment', label: 'Return Comment', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 56, align: 'left' },
        { key: 'delivery_photo_status', label: 'Delivery Photo Status', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 40, align: 'left' },
        { key: 'delivery_photo_link', label: 'Delivery Photo Link', group: 'delivery', group_label: 'Delivery Details', is_default: false, pdf_width: 76, align: 'left' }
    ];
    var exportColumnCatalog = DEFAULT_EXPORT_COLUMN_CATALOG.slice();
    var selectedExportColKeys = [];
    var draftExportColSet = new Set();

    var MONTH_NAMES = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    var SHORT_MONTH_NAMES = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];

    var dpState = {
        isOpen: false,
        targetKey: 'start',
        viewYear: 2026,
        viewMonth: 1,
        openedVia: 'trigger'
    };

    // Single polite announcer for blurred/invalid date fields (placed outside drawer so it stays active)
    var formErrorAnnouncer = document.createElement('div');
    formErrorAnnouncer.className = 'visually-hidden';
    formErrorAnnouncer.setAttribute('aria-live', 'polite');
    document.body.appendChild(formErrorAnnouncer);
    var errorAnnounceTimer = null;
    var errorAnnouncedField = null;
    var dirtyDateInputs = new WeakSet();
    var announcedDateErrors = new WeakSet();
    var flaggedAriaFields = new WeakSet();
    var pointerDownInDatepicker = false;

    function pad2(num) {
        return num < 10 ? ('0' + String(num)) : String(num);
    }

    function isValidYmd(y, m, d) {
        if (!Number.isInteger(y) || !Number.isInteger(m) || !Number.isInteger(d)) {
            return false;
        }
        if (y < 1900 || y > 2100 || m < 1 || m > 12 || d < 1 || d > 31) {
            return false;
        }
        var dt = new Date(Date.UTC(y, m - 1, d));
        return dt.getUTCFullYear() === y && (dt.getUTCMonth() + 1) === m && dt.getUTCDate() === d;
    }

    function formatIsoParts(y, m, d) {
        return String(y).padStart(4, '0') + '-' + pad2(m) + '-' + pad2(d);
    }

    function parseIsoParts(iso) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(iso || '').trim());
        if (!m) {
            return null;
        }
        var y = parseInt(m[1], 10);
        var mo = parseInt(m[2], 10);
        var d = parseInt(m[3], 10);
        if (!isValidYmd(y, mo, d)) {
            return null;
        }
        return { y: y, m: mo, d: d, iso: formatIsoParts(y, mo, d) };
    }

    function getLocalTodayIso() {
        var now = new Date();
        return formatIsoParts(now.getFullYear(), now.getMonth() + 1, now.getDate());
    }

    function getTodayIso() {
        var parsed = parseIsoParts(config.todayIso);
        return parsed ? parsed.iso : getLocalTodayIso();
    }

    function addDaysIso(iso, deltaDays) {
        var parts = parseIsoParts(iso) || parseIsoParts(getTodayIso());
        var dt = new Date(Date.UTC(parts.y, parts.m - 1, parts.d) + (deltaDays * 86400000));
        return formatIsoParts(dt.getUTCFullYear(), dt.getUTCMonth() + 1, dt.getUTCDate());
    }

    function daysInMonth(y, m) {
        return new Date(Date.UTC(y, m, 0)).getUTCDate();
    }

    function dayOfWeekUtc(y, m, d) {
        return new Date(Date.UTC(y, m - 1, d)).getUTCDay();
    }

    // Returns '' when blank, 'YYYY-MM-DD' when valid calendar date, or null when malformed/invalid
    function parseFlexibleDateToIso(raw) {
        var val = String(raw || '').trim();
        if (!val) {
            return '';
        }
        var mIso = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(val);
        if (mIso) {
            var iy = parseInt(mIso[1], 10);
            var im = parseInt(mIso[2], 10);
            var id = parseInt(mIso[3], 10);
            return isValidYmd(iy, im, id) ? formatIsoParts(iy, im, id) : null;
        }
        var mDmy = /^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/.exec(val);
        if (mDmy) {
            var p1 = parseInt(mDmy[1], 10);
            var p2 = parseInt(mDmy[2], 10);
            var yr = parseInt(mDmy[3], 10);
            if (isValidYmd(yr, p2, p1)) {
                return formatIsoParts(yr, p2, p1);
            }
            if (isValidYmd(yr, p1, p2)) {
                return formatIsoParts(yr, p1, p2);
            }
            return null;
        }
        return null;
    }

    function formatDisplayDate(iso) {
        var parts = parseIsoParts(iso);
        if (!parts) {
            return String(iso || '');
        }
        return pad2(parts.d) + '/' + pad2(parts.m) + '/' + String(parts.y).padStart(4, '0');
    }

    function formatAriaDateLabel(y, m, d) {
        return d + ' ' + (MONTH_NAMES[m - 1] || '') + ' ' + y;
    }

    function getDefaultDateRange() {
        var today = getTodayIso();
        var defEnd = parseIsoParts(config.defaultEndDate);
        var defStart = parseIsoParts(config.defaultStartDate);
        if (defStart && defEnd && defEnd.iso === today && defStart.iso <= defEnd.iso) {
            return { start: defStart.iso, end: defEnd.iso };
        }
        return { start: addDaysIso(today, -29), end: today };
    }

    function isDefaultDatesState() {
        if (state.all_dates || state.date_basis !== 'created') {
            return false;
        }
        var def = getDefaultDateRange();
        return state.start_date === def.start && state.end_date === def.end;
    }

    function isAtDefaultState() {
        return state.status === 'all' &&
            isDefaultDatesState() &&
            state.payment_mode === 'all' &&
            state.city === 'all' &&
            !state.q &&
            state.sort_by === 'newest' &&
            state.limit === config.defaultLimit;
    }

    function getPresetDates(presetName) {
        var today = getTodayIso();
        var todayParts = parseIsoParts(today);

        if (presetName === 'today') {
            return { start: today, end: today, allDates: false };
        }
        if (presetName === 'yesterday') {
            var isoYest = addDaysIso(today, -1);
            return { start: isoYest, end: isoYest, allDates: false };
        }
        if (presetName === 'last7') {
            return { start: addDaysIso(today, -6), end: today, allDates: false };
        }
        if (presetName === 'last30') {
            return { start: addDaysIso(today, -29), end: today, allDates: false };
        }
        if (presetName === 'month') {
            var monthStart = formatIsoParts(todayParts.y, todayParts.m, 1);
            return { start: monthStart, end: today, allDates: false };
        }
        return { start: '', end: '', allDates: true };
    }

    function getDateFieldElements(targetKey) {
        var isEnd = (targetKey === 'end');
        var inputEl = isEnd ? els.endDateInput : els.startDateInput;
        return {
            key: isEnd ? 'end' : 'start',
            input: inputEl,
            trigger: isEnd ? els.endDateTrigger : els.startDateTrigger,
            wrap: inputEl ? inputEl.closest('.crm-date-input-wrap') : null,
            field: inputEl ? inputEl.closest('.crm-field') : null
        };
    }

    function linkInputError(inputEl, shown) {
        if (!inputEl) {
            return;
        }
        var errorId = inputEl.getAttribute('data-error');
        if (!errorId) {
            return;
        }
        var ids = (inputEl.getAttribute('aria-describedby') || '')
            .split(/\s+/)
            .filter(function (id) { return id && id !== errorId; });
        if (shown) {
            ids.push(errorId);
        }
        if (ids.length) {
            inputEl.setAttribute('aria-describedby', ids.join(' '));
        } else {
            inputEl.removeAttribute('aria-describedby');
        }
    }

    function getLabelTextForInput(inputEl) {
        if (!inputEl || !inputEl.id) {
            return '';
        }
        var lbl = root.querySelector('label[for="' + inputEl.id + '"]');
        return lbl ? lbl.textContent.replace(/\s+/g, ' ').trim() : '';
    }

    function scheduleErrorAnnouncement(inputEl, errMsg) {
        if (!inputEl || !errMsg) {
            return;
        }
        var label = getLabelTextForInput(inputEl);
        window.clearTimeout(errorAnnounceTimer);
        formErrorAnnouncer.textContent = '';
        errorAnnouncedField = inputEl;
        errorAnnounceTimer = window.setTimeout(function () {
            formErrorAnnouncer.textContent = (label ? (label + ': ') : '') + 'Error: ' + errMsg;
        }, 500);
    }

    function clearFieldValidation(inputEl, preserveCustomValidity) {
        if (!inputEl) {
            return;
        }
        if (!preserveCustomValidity) {
            inputEl.setCustomValidity('');
        }
        inputEl.classList.remove('user-invalid-fallback', 'user-valid-fallback');
        if (flaggedAriaFields.has(inputEl) || inputEl.getAttribute('aria-invalid') !== 'true') {
            inputEl.setAttribute('aria-invalid', 'false');
            linkInputError(inputEl, false);
            flaggedAriaFields.delete(inputEl);
        }
        announcedDateErrors.delete(inputEl);
        var errorId = inputEl.getAttribute('data-error');
        var errEl = errorId ? document.getElementById(errorId) : null;
        if (errEl) {
            errEl.classList.remove('is-visible');
            errEl.hidden = true;
            var txtEl = errEl.querySelector('.crm-error-text');
            if (txtEl) {
                txtEl.textContent = '';
            }
        }
        if (errorAnnouncedField === inputEl) {
            window.clearTimeout(errorAnnounceTimer);
            formErrorAnnouncer.textContent = '';
            errorAnnouncedField = null;
        }
    }

    function computeDateFieldError(inputEl, checkRangeOrder) {
        if (!inputEl) {
            return '';
        }
        var raw = (inputEl.value || '').trim();
        if (!raw) {
            return '';
        }
        var today = getTodayIso();
        var iso = parseFlexibleDateToIso(raw);
        if (iso === null) {
            return 'Enter a valid date in DD/MM/YYYY format.';
        }
        if (iso > today) {
            return 'Future dates are not allowed (up to ' + formatDisplayDate(today) + ').';
        }
        if (checkRangeOrder && inputEl === els.endDateInput && els.startDateInput) {
            var sIso = parseFlexibleDateToIso(els.startDateInput.value);
            if (sIso && sIso <= today && iso < sIso) {
                return 'To Date must be on or after From Date (' + formatDisplayDate(sIso) + ').';
            }
        }
        return '';
    }

    function applyFieldValidation(inputEl, opts) {
        if (!inputEl) {
            return true;
        }
        var options = opts || {};
        var errMsg = computeDateFieldError(inputEl, !!options.checkRangeOrder);
        inputEl.setCustomValidity(errMsg);

        if (!errMsg) {
            // Canonicalize DD/MM/YYYY formatting cleanly once validated on blur/submit
            var parsed = parseFlexibleDateToIso(inputEl.value);
            if (parsed) {
                inputEl.value = formatDisplayDate(parsed);
            }
            clearFieldValidation(inputEl, true);
            if (parsed && options.markValid) {
                inputEl.classList.add('user-valid-fallback');
            }
            return true;
        }

        if (!options.afterInteraction) {
            return false;
        }

        var errorId = inputEl.getAttribute('data-error');
        var errEl = errorId ? document.getElementById(errorId) : null;
        if (errEl) {
            var txtEl = errEl.querySelector('.crm-error-text');
            if (txtEl) {
                txtEl.textContent = errMsg;
            }
            errEl.hidden = false;
            errEl.classList.add('is-visible');
        }

        inputEl.classList.remove('user-valid-fallback');
        if (!inputEl.matches(':user-invalid')) {
            inputEl.classList.add('user-invalid-fallback');
        }
        inputEl.setAttribute('aria-invalid', 'true');
        linkInputError(inputEl, true);
        flaggedAriaFields.add(inputEl);

        if (options.announce && !announcedDateErrors.has(inputEl)) {
            announcedDateErrors.add(inputEl);
            scheduleErrorAnnouncement(inputEl, errMsg);
        }
        return false;
    }

    function positionDatepicker() {
        if (!els.dpPopover || els.dpPopover.hidden || !dpState.isOpen) {
            return;
        }
        var field = getDateFieldElements(dpState.targetKey);
        var anchor = field.wrap || field.input || field.trigger;
        if (!anchor) {
            return;
        }
        var fieldAnchor = field.field || anchor;

        var rect = anchor.getBoundingClientRect();
        var fieldRect = fieldAnchor.getBoundingClientRect();
        var popRect = els.dpPopover.getBoundingClientRect();
        var vw = window.innerWidth || document.documentElement.clientWidth || 1024;
        var vh = window.innerHeight || document.documentElement.clientHeight || 768;
        var margin = 8;

        var left = rect.left;
        if (left + popRect.width > vw - margin) {
            left = vw - popRect.width - margin;
        }
        if (left < margin) {
            left = margin;
        }

        var spaceBelow = vh - rect.bottom - margin;
        var spaceAboveField = fieldRect.top - margin;
        var top = rect.bottom + 6;
        if (spaceBelow < popRect.height + 6 && spaceAboveField >= popRect.height + 6) {
            top = fieldRect.top - popRect.height - 6;
        } else if (spaceBelow < popRect.height + 6) {
            var spaceAbove = rect.top - margin;
            if (spaceAbove > spaceBelow && spaceAbove >= popRect.height + 6) {
                top = rect.top - popRect.height - 6;
            } else {
                top = Math.max(margin, vh - popRect.height - margin);
            }
        }

        if (top + popRect.height > vh - margin) {
            top = vh - popRect.height - margin;
        }
        if (top < margin) {
            top = margin;
        }

        els.dpPopover.style.left = Math.round(left) + 'px';
        els.dpPopover.style.top = Math.round(top) + 'px';
    }

    function closeDatepicker(options) {
        if (!els.dpPopover) {
            return;
        }
        var opts = options || {};
        var wasOpen = dpState.isOpen;
        var prevKey = dpState.targetKey;
        var prevOpenedVia = dpState.openedVia;

        dpState.isOpen = false;
        els.dpPopover.hidden = true;
        els.dpPopover.style.left = '';
        els.dpPopover.style.top = '';

        if (els.startDateTrigger) {
            els.startDateTrigger.setAttribute('aria-expanded', 'false');
        }
        if (els.endDateTrigger) {
            els.endDateTrigger.setAttribute('aria-expanded', 'false');
        }

        if (wasOpen && opts.restoreFocus) {
            var field = getDateFieldElements(prevKey);
            var targetEl = (opts.focusTarget === 'input' || (opts.focusTarget !== 'trigger' && prevOpenedVia === 'input'))
                ? (field.input || field.trigger)
                : (field.trigger || field.input);
            if (targetEl && typeof targetEl.focus === 'function') {
                targetEl.focus();
            }
        }
    }

    function populateDatepickerYearMonthSelects() {
        var todayParts = parseIsoParts(getTodayIso());
        var maxYear = todayParts.y;
        var minYear = Math.min(2018, maxYear - 8);

        if (els.dpYearSelect) {
            if (els.dpYearSelect.options.length === 0 || parseInt(els.dpYearSelect.dataset.maxYear || '0', 10) !== maxYear) {
                while (els.dpYearSelect.firstChild) {
                    els.dpYearSelect.removeChild(els.dpYearSelect.firstChild);
                }
                for (var yr = minYear; yr <= maxYear; yr++) {
                    var yOpt = document.createElement('option');
                    yOpt.value = String(yr);
                    yOpt.textContent = String(yr);
                    els.dpYearSelect.appendChild(yOpt);
                }
                els.dpYearSelect.dataset.maxYear = String(maxYear);
            }
            els.dpYearSelect.value = String(dpState.viewYear);
        }

        if (els.dpMonthSelect) {
            if (els.dpMonthSelect.options.length !== 12) {
                while (els.dpMonthSelect.firstChild) {
                    els.dpMonthSelect.removeChild(els.dpMonthSelect.firstChild);
                }
                for (var mo = 1; mo <= 12; mo++) {
                    var mOpt = document.createElement('option');
                    mOpt.value = String(mo);
                    mOpt.textContent = SHORT_MONTH_NAMES[mo - 1];
                    els.dpMonthSelect.appendChild(mOpt);
                }
            }
            for (var i = 0; i < els.dpMonthSelect.options.length; i++) {
                var opt = els.dpMonthSelect.options[i];
                var mVal = parseInt(opt.value, 10);
                opt.disabled = (dpState.viewYear > maxYear) || (dpState.viewYear === maxYear && mVal > todayParts.m);
            }
            els.dpMonthSelect.value = String(dpState.viewMonth);
        }
    }

    function selectDateFromPicker(iso, focusTarget) {
        var today = getTodayIso();
        if (!iso || iso > today) {
            return;
        }
        var targetField = getDateFieldElements(dpState.targetKey);
        if (!targetField.input) {
            return;
        }
        targetField.input.value = formatDisplayDate(iso);
        clearFieldValidation(targetField.input);
        dirtyDateInputs.delete(targetField.input);

        var otherKey = dpState.targetKey === 'start' ? 'end' : 'start';
        var otherField = getDateFieldElements(otherKey);
        if (otherField.input) {
            var otherIso = parseFlexibleDateToIso(otherField.input.value);
            if (otherIso && otherIso <= today) {
                if (dpState.targetKey === 'start' && otherIso < iso) {
                    otherField.input.value = formatDisplayDate(iso);
                    clearFieldValidation(otherField.input);
                } else if (dpState.targetKey === 'end' && otherIso > iso) {
                    otherField.input.value = formatDisplayDate(iso);
                    clearFieldValidation(otherField.input);
                }
            }
        }

        if (els.allDatesInput) {
            els.allDatesInput.value = '';
        }
        syncPresetChipsFromInputs();
        closeDatepicker({ restoreFocus: true, focusTarget: focusTarget || 'trigger' });
    }

    function renderDatepickerCalendar(focusIsoTarget) {
        if (!els.dpDays) {
            return;
        }
        var todayIso = getTodayIso();
        var todayParts = parseIsoParts(todayIso);

        // Clamp viewed month so calendar cannot navigate into future months
        if (dpState.viewYear > todayParts.y) {
            dpState.viewYear = todayParts.y;
        }
        if (dpState.viewYear === todayParts.y && dpState.viewMonth > todayParts.m) {
            dpState.viewMonth = todayParts.m;
        }

        populateDatepickerYearMonthSelects();

        if (els.dpPrevBtn) {
            els.dpPrevBtn.disabled = (dpState.viewYear <= 2018 && dpState.viewMonth <= 1);
        }
        if (els.dpNextBtn) {
            els.dpNextBtn.disabled = (dpState.viewYear > todayParts.y) || (dpState.viewYear === todayParts.y && dpState.viewMonth >= todayParts.m);
        }

        var startIsoRaw = els.startDateInput ? parseFlexibleDateToIso(els.startDateInput.value) : '';
        var endIsoRaw = els.endDateInput ? parseFlexibleDateToIso(els.endDateInput.value) : '';
        var startIso = (startIsoRaw && startIsoRaw <= todayIso) ? startIsoRaw : '';
        var endIso = (endIsoRaw && endIsoRaw <= todayIso) ? endIsoRaw : '';
        var targetIso = (dpState.targetKey === 'end') ? endIso : startIso;

        while (els.dpDays.firstChild) {
            els.dpDays.removeChild(els.dpDays.firstChild);
        }

        var firstDow = dayOfWeekUtc(dpState.viewYear, dpState.viewMonth, 1);
        var curDays = daysInMonth(dpState.viewYear, dpState.viewMonth);
        var prevY = dpState.viewMonth === 1 ? (dpState.viewYear - 1) : dpState.viewYear;
        var prevM = dpState.viewMonth === 1 ? 12 : (dpState.viewMonth - 1);
        var prevDays = daysInMonth(prevY, prevM);
        var nextY = dpState.viewMonth === 12 ? (dpState.viewYear + 1) : dpState.viewYear;
        var nextM = dpState.viewMonth === 12 ? 1 : (dpState.viewMonth + 1);

        var frag = document.createDocumentFragment();
        var focusTargetBtn = null;
        var totalCells = Math.ceil((firstDow + curDays) / 7) * 7;

        for (var idx = 0; idx < totalCells; idx++) {
            var cellY = dpState.viewYear;
            var cellM = dpState.viewMonth;
            var cellD = 1;
            var isOutside = false;

            if (idx < firstDow) {
                cellY = prevY;
                cellM = prevM;
                cellD = prevDays - firstDow + idx + 1;
                isOutside = true;
            } else if (idx >= firstDow + curDays) {
                cellY = nextY;
                cellM = nextM;
                cellD = idx - (firstDow + curDays) + 1;
                isOutside = true;
            } else {
                cellD = idx - firstDow + 1;
            }

            var cellIso = formatIsoParts(cellY, cellM, cellD);
            var isFuture = cellIso > todayIso;
            var isToday = cellIso === todayIso;
            var isStart = !!(startIso && cellIso === startIso);
            var isEnd = !!(endIso && cellIso === endIso);
            var isSelected = isStart || isEnd;
            var isTarget = !!(targetIso && cellIso === targetIso);
            var isInRange = !!(startIso && endIso && startIso <= endIso && cellIso >= startIso && cellIso <= endIso);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'crm-dp-day';
            if (isOutside) {
                btn.classList.add('is-outside');
            }
            if (isToday) {
                btn.classList.add('is-today');
            }
            if (isInRange) {
                btn.classList.add('is-in-range');
            }
            if (isSelected) {
                btn.classList.add('is-selected');
            }
            if (isTarget) {
                btn.classList.add('is-active-target');
            }
            if (isFuture) {
                btn.classList.add('is-future');
                btn.disabled = true;
            }

            btn.setAttribute('data-iso', cellIso);
            btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            btn.setAttribute(
                'aria-label',
                formatAriaDateLabel(cellY, cellM, cellD) +
                    (isToday ? ', Today' : '') +
                    (isFuture ? ', Future date disabled' : '')
            );
            btn.textContent = String(cellD);

            if (!isFuture && focusIsoTarget && cellIso === focusIsoTarget) {
                focusTargetBtn = btn;
            }
            frag.appendChild(btn);
        }

        els.dpDays.appendChild(frag);
        if (focusTargetBtn && typeof focusTargetBtn.focus === 'function') {
            focusTargetBtn.focus();
        }
    }

    function openDatepicker(targetKey, options) {
        if (!els.dpPopover) {
            return;
        }
        var opts = options || {};
        var todayIso = getTodayIso();
        var todayParts = parseIsoParts(todayIso);

        dpState.targetKey = (targetKey === 'end') ? 'end' : 'start';
        dpState.openedVia = opts.openedVia || 'trigger';

        var activeField = getDateFieldElements(dpState.targetKey);
        var otherField = getDateFieldElements(dpState.targetKey === 'start' ? 'end' : 'start');
        var rawIso = activeField.input ? parseFlexibleDateToIso(activeField.input.value) : '';
        if (!rawIso && otherField.input) {
            rawIso = parseFlexibleDateToIso(otherField.input.value);
        }
        var seedParts = (rawIso && rawIso <= todayIso) ? parseIsoParts(rawIso) : todayParts;
        dpState.viewYear = seedParts.y;
        dpState.viewMonth = seedParts.m;

        if (els.dpTitle) {
            els.dpTitle.textContent = (dpState.targetKey === 'end') ? 'Select To Date' : 'Select From Date';
        }
        if (els.startDateTrigger) {
            els.startDateTrigger.setAttribute('aria-expanded', dpState.targetKey === 'start' ? 'true' : 'false');
        }
        if (els.endDateTrigger) {
            els.endDateTrigger.setAttribute('aria-expanded', dpState.targetKey === 'end' ? 'true' : 'false');
        }

        dpState.isOpen = true;
        els.dpPopover.hidden = false;
        renderDatepickerCalendar();
        if (activeField.field && typeof activeField.field.scrollIntoView === 'function') {
            var fRect = activeField.field.getBoundingClientRect();
            var vhNow = window.innerHeight || document.documentElement.clientHeight || 768;
            if (fRect.top < 12 || fRect.bottom > vhNow - 12) {
                activeField.field.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        }
        positionDatepicker();

        if (opts.focusPopover) {
            var targetDayBtn = els.dpDays
                ? (els.dpDays.querySelector('.crm-dp-day.is-active-target:not(:disabled)') ||
                    els.dpDays.querySelector('.crm-dp-day.is-today:not(:disabled)') ||
                    els.dpDays.querySelector('.crm-dp-day:not(:disabled)'))
                : null;
            var focusTarget = targetDayBtn || els.dpMonthSelect;
            if (focusTarget && typeof focusTarget.focus === 'function') {
                try {
                    focusTarget.focus({ preventScroll: true });
                } catch (e) {
                    focusTarget.focus();
                }
            }
        }
    }

    function syncPresetChipsFromInputs() {
        var sRaw = els.startDateInput ? parseFlexibleDateToIso(els.startDateInput.value) : '';
        var eRaw = els.endDateInput ? parseFlexibleDateToIso(els.endDateInput.value) : '';
        var sIso = (typeof sRaw === 'string') ? sRaw : '';
        var eIso = (typeof eRaw === 'string') ? eRaw : '';
        var isAll = (!sIso && !eIso && els.allDatesInput && els.allDatesInput.value === '1') || (!sIso && !eIso);
        syncDatePresetChipsForValues(sIso, eIso, isAll);
    }

    function setFilterDrawerOpen(isOpen, options) {
        if (!els.filterDrawer || !els.filterToggleBtn) {
            return;
        }
        var opts = options || {};

        if (isOpen) {
            syncControlsFromState();
            els.filterDrawer.hidden = false;
            els.filterToggleBtn.setAttribute('aria-expanded', 'true');
            if (!opts.skipFocus && els.searchInput && typeof window.requestAnimationFrame === 'function') {
                window.requestAnimationFrame(function () {
                    els.searchInput.focus();
                });
            }
            return;
        }

        closeDatepicker({ restoreFocus: false });

        if (opts.discardUnapplied) {
            syncControlsFromState();
        }

        var focusWasInsideDrawer = els.filterDrawer.contains(document.activeElement);
        if (opts.restoreFocus || focusWasInsideDrawer) {
            els.filterToggleBtn.focus();
        }

        els.filterToggleBtn.setAttribute('aria-expanded', 'false');
        els.filterDrawer.hidden = true;
    }

    function readStateFromLocation() {
        var params = new URLSearchParams(window.location.search);
        var today = getTodayIso();
        var def = getDefaultDateRange();

        state.status = params.get('status') || config.defaultStatus || 'all';
        var basis = (params.get('date_basis') || 'created').toLowerCase();
        state.date_basis = (basis === 'delivery') ? 'delivery' : 'created';

        var allFlagRaw = (params.get('all_dates') || params.get('date_scope') || '').toLowerCase();
        var allFlag = (allFlagRaw === '1' || allFlagRaw === 'true' || allFlagRaw === 'yes' || allFlagRaw === 'all');

        var parsedStart = parseFlexibleDateToIso(params.get('start_date') || '');
        var parsedEnd = parseFlexibleDateToIso(params.get('end_date') || '');
        var startIso = (typeof parsedStart === 'string') ? parsedStart : '';
        var endIso = (typeof parsedEnd === 'string') ? parsedEnd : '';

        if (allFlag && !startIso && !endIso) {
            state.all_dates = true;
            state.start_date = '';
            state.end_date = '';
        } else if (!startIso && !endIso) {
            state.all_dates = false;
            state.start_date = def.start;
            state.end_date = def.end;
        } else {
            if (!startIso) startIso = endIso;
            if (!endIso) endIso = startIso;
            if (startIso > today) startIso = today;
            if (endIso > today) endIso = today;
            if (startIso > endIso) {
                var tmp = startIso;
                startIso = endIso;
                endIso = tmp;
            }
            state.all_dates = false;
            state.start_date = startIso;
            state.end_date = endIso;
        }

        state.payment_mode = params.get('payment_mode') || 'all';
        state.city = params.get('city') || 'all';
        state.q = params.get('q') || '';
        state.sort_by = params.get('sort_by') || 'newest';

        var pageVal = parseInt(params.get('page') || '1', 10);
        state.page = (Number.isFinite(pageVal) && pageVal >= 1) ? pageVal : 1;

        var limitVal = parseInt(params.get('limit') || String(config.defaultLimit), 10);
        if (Number.isFinite(limitVal) && limitVal >= 10 && limitVal <= 100) {
            state.limit = limitVal;
        } else {
            state.limit = config.defaultLimit;
        }
    }

    function ensureOptionExists(selectEl, value, label) {
        if (!selectEl || !value) {
            return;
        }
        for (var i = 0; i < selectEl.options.length; i++) {
            if (selectEl.options[i].value === value) {
                return;
            }
        }
        var opt = document.createElement('option');
        opt.value = value;
        opt.textContent = label || value;
        selectEl.appendChild(opt);
    }

    function getOptionLabel(selectEl, value) {
        if (!selectEl) {
            return value;
        }
        for (var i = 0; i < selectEl.options.length; i++) {
            if (selectEl.options[i].value === value) {
                return selectEl.options[i].textContent;
            }
        }
        return value;
    }

    function syncDatePresetChipsForValues(startDate, endDate, forceAll) {
        var matchedPreset = '';
        if (forceAll || (!startDate && !endDate)) {
            matchedPreset = 'all';
        } else {
            ['today', 'yesterday', 'last7', 'last30', 'month'].forEach(function (name) {
                var range = getPresetDates(name);
                if (range.start === startDate && range.end === endDate) {
                    matchedPreset = name;
                }
            });
        }

        els.presetChips.forEach(function (chip) {
            var pName = chip.getAttribute('data-preset') || '';
            chip.setAttribute('aria-pressed', pName === matchedPreset ? 'true' : 'false');
        });
    }

    function syncStatusPills() {
        els.statusPills.forEach(function (pill) {
            var code = pill.getAttribute('data-status') || 'all';
            pill.setAttribute('aria-pressed', code === state.status ? 'true' : 'false');
        });
    }

    function updatePageSubtitle() {
        if (!els.pageSubtitle) {
            return;
        }
        var def = getDefaultDateRange();
        if (state.all_dates) {
            els.pageSubtitle.textContent = config.viewMode === 'mobile'
                ? 'Showing all dates. Filter or reset to Last 30 Days.'
                : 'Showing all dates. Filter on demand or reset to Last 30 Days.';
            return;
        }
        var s = state.start_date || state.end_date || def.start;
        var e = state.end_date || state.start_date || def.end;
        var rangeText = (s === e) ? formatDisplayDate(s) : (formatDisplayDate(s) + ' – ' + formatDisplayDate(e));
        if (s === def.start && e === def.end && state.date_basis === 'created') {
            els.pageSubtitle.textContent = config.viewMode === 'mobile'
                ? ('Showing Last 30 Days (' + rangeText + '). Filter or export.')
                : ('Showing Last 30 Days (' + rangeText + '). Filter or export anytime.');
        } else {
            var basisWord = (state.date_basis === 'delivery') ? 'delivery dates' : 'created dates';
            els.pageSubtitle.textContent = 'Showing orders for ' + basisWord + ' (' + rangeText + '). Filter or export anytime.';
        }
    }

    function syncControlsFromState() {
        if (els.statusSelect) {
            els.statusSelect.value = state.status;
        }
        if (els.dateBasisSelect) {
            els.dateBasisSelect.value = state.date_basis;
        }
        if (els.allDatesInput) {
            els.allDatesInput.value = state.all_dates ? '1' : '';
        }
        if (els.startDateInput) {
            els.startDateInput.value = state.all_dates ? '' : formatDisplayDate(state.start_date);
            clearFieldValidation(els.startDateInput);
            dirtyDateInputs.delete(els.startDateInput);
        }
        if (els.endDateInput) {
            els.endDateInput.value = state.all_dates ? '' : formatDisplayDate(state.end_date);
            clearFieldValidation(els.endDateInput);
            dirtyDateInputs.delete(els.endDateInput);
        }
        if (els.paymentSelect) {
            if (state.payment_mode !== 'all') {
                ensureOptionExists(els.paymentSelect, state.payment_mode, state.payment_mode);
            }
            els.paymentSelect.value = state.payment_mode;
        }
        if (els.citySelect) {
            if (state.city !== 'all') {
                ensureOptionExists(els.citySelect, state.city, state.city);
            }
            els.citySelect.value = state.city;
        }
        if (els.searchInput) {
            els.searchInput.value = state.q;
        }
        if (els.sortSelect) {
            els.sortSelect.value = state.sort_by;
        }
        if (els.limitSelect) {
            els.limitSelect.value = String(state.limit);
        }

        syncStatusPills();
        syncDatePresetChipsForValues(state.all_dates ? '' : state.start_date, state.all_dates ? '' : state.end_date, state.all_dates);
    }

    function readStateFromControls() {
        var today = getTodayIso();
        if (els.statusSelect) {
            state.status = els.statusSelect.value || 'all';
        }
        if (els.dateBasisSelect) {
            state.date_basis = els.dateBasisSelect.value === 'delivery' ? 'delivery' : 'created';
        }

        var startRaw = els.startDateInput ? parseFlexibleDateToIso(els.startDateInput.value) : '';
        var endRaw = els.endDateInput ? parseFlexibleDateToIso(els.endDateInput.value) : '';
        var sIso = (typeof startRaw === 'string') ? startRaw : '';
        var eIso = (typeof endRaw === 'string') ? endRaw : '';

        if (!sIso && !eIso) {
            state.all_dates = true;
            state.start_date = '';
            state.end_date = '';
        } else {
            if (!sIso) sIso = eIso;
            if (!eIso) eIso = sIso;
            if (sIso > today) sIso = today;
            if (eIso > today) eIso = today;
            if (sIso > eIso) {
                var tmp = sIso;
                sIso = eIso;
                eIso = tmp;
            }
            state.all_dates = false;
            state.start_date = sIso;
            state.end_date = eIso;
        }

        if (els.paymentSelect) {
            state.payment_mode = els.paymentSelect.value || 'all';
        }
        if (els.citySelect) {
            state.city = els.citySelect.value || 'all';
        }
        if (els.searchInput) {
            state.q = (els.searchInput.value || '').trim();
        }
        if (els.sortSelect) {
            state.sort_by = els.sortSelect.value || 'newest';
        }
        if (els.limitSelect) {
            var lv = parseInt(els.limitSelect.value || String(config.defaultLimit), 10);
            state.limit = (Number.isFinite(lv) && lv >= 10 && lv <= 100) ? lv : config.defaultLimit;
        }
    }

    function updateActiveFiltersUI() {
        var chips = [];
        var def = getDefaultDateRange();

        if (state.q) {
            chips.push({
                label: 'Search: "' + state.q + '"',
                onClear: function () { state.q = ''; }
            });
        }
        if (!state.all_dates && (state.start_date || state.end_date)) {
            var s = state.start_date || state.end_date;
            var e = state.end_date || state.start_date;
            var basisLabel = (state.date_basis === 'delivery') ? 'Delivery' : 'Created';
            var rangeStr = (s === e) ? formatDisplayDate(s) : (formatDisplayDate(s) + ' – ' + formatDisplayDate(e));
            var isDefLast30 = (s === def.start && e === def.end && state.date_basis === 'created');
            chips.push({
                label: (isDefLast30 ? 'Last 30 Days: ' : (basisLabel + ' Date: ')) + rangeStr,
                onClear: function () {
                    state.all_dates = true;
                    state.start_date = '';
                    state.end_date = '';
                    state.date_basis = 'created';
                }
            });
        }
        if (state.payment_mode && state.payment_mode !== 'all') {
            chips.push({
                label: 'Payment: ' + getOptionLabel(els.paymentSelect, state.payment_mode),
                onClear: function () { state.payment_mode = 'all'; }
            });
        }
        if (state.city && state.city !== 'all') {
            chips.push({
                label: 'City: ' + state.city,
                onClear: function () { state.city = 'all'; }
            });
        }
        if (state.sort_by && state.sort_by !== 'newest') {
            chips.push({
                label: 'Sort: ' + getOptionLabel(els.sortSelect, state.sort_by),
                onClear: function () { state.sort_by = 'newest'; }
            });
        }
        if (state.limit !== config.defaultLimit) {
            chips.push({
                label: 'Rows: ' + state.limit,
                onClear: function () { state.limit = config.defaultLimit; }
            });
        }

        var drawerActiveCount = chips.length;

        if (els.filterActiveCount) {
            els.filterActiveCount.textContent = String(drawerActiveCount);
            els.filterActiveCount.hidden = (drawerActiveCount === 0);
        }
        if (els.filterToggleBtn) {
            els.filterToggleBtn.classList.toggle('has-active', drawerActiveCount > 0);
        }
        if (els.quickResetBtn) {
            els.quickResetBtn.hidden = isAtDefaultState();
            els.quickResetBtn.setAttribute('aria-label', 'Reset filters to default Last 30 Days');
        }

        updatePageSubtitle();

        if (els.activeFiltersBar && els.activeChipsList) {
            while (els.activeChipsList.firstChild) {
                els.activeChipsList.removeChild(els.activeChipsList.firstChild);
            }

            if (drawerActiveCount === 0) {
                els.activeFiltersBar.hidden = true;
            } else {
                chips.forEach(function (chipData) {
                    var chip = document.createElement('span');
                    chip.className = 'crm-active-chip';

                    var text = document.createElement('span');
                    text.textContent = chipData.label;
                    chip.appendChild(text);

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'crm-chip-remove';
                    removeBtn.setAttribute('aria-label', 'Remove ' + chipData.label);
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', function () {
                        chipData.onClear();
                        state.page = 1;
                        syncControlsFromState();
                        updateActiveFiltersUI();
                        loadOrders(true);
                    });

                    chip.appendChild(removeBtn);
                    els.activeChipsList.appendChild(chip);
                });
                els.activeFiltersBar.hidden = false;
            }
        }
    }

    function clearAllFilters() {
        state.status = 'all';
        state.date_basis = 'created';
        state.all_dates = true;
        state.start_date = '';
        state.end_date = '';
        state.payment_mode = 'all';
        state.city = 'all';
        state.q = '';
        state.sort_by = 'newest';
        state.page = 1;
        state.limit = config.defaultLimit;
        syncControlsFromState();
        updateActiveFiltersUI();
        if (els.filterDrawer && !els.filterDrawer.hidden) {
            setFilterDrawerOpen(false, { restoreFocus: true });
        }
        loadOrders(true);
    }

    function resetAllFilters() {
        var def = getDefaultDateRange();
        state.status = 'all';
        state.date_basis = 'created';
        state.all_dates = false;
        state.start_date = def.start;
        state.end_date = def.end;
        state.payment_mode = 'all';
        state.city = 'all';
        state.q = '';
        state.sort_by = 'newest';
        state.page = 1;
        state.limit = config.defaultLimit;
        syncControlsFromState();
        updateActiveFiltersUI();
        if (els.filterDrawer && !els.filterDrawer.hidden) {
            setFilterDrawerOpen(false, { restoreFocus: true });
        }
        loadOrders(true);
    }

    function pushBrowserHistoryUrl() {
        if (!window.history || typeof window.history.replaceState !== 'function') {
            return;
        }
        var current = new URLSearchParams(window.location.search);
        var next = new URLSearchParams();
        next.set('type', current.get('type') || 'list');
        next.set('status', state.status || 'all');

        if (state.date_basis !== 'created') {
            next.set('date_basis', state.date_basis);
        }
        if (state.all_dates) {
            next.set('all_dates', '1');
        } else if (!isDefaultDatesState()) {
            if (state.start_date) {
                next.set('start_date', state.start_date);
            }
            if (state.end_date) {
                next.set('end_date', state.end_date);
            }
        }
        if (state.payment_mode !== 'all') {
            next.set('payment_mode', state.payment_mode);
        }
        if (state.city !== 'all') {
            next.set('city', state.city);
        }
        if (state.q) {
            next.set('q', state.q);
        }
        if (state.sort_by !== 'newest') {
            next.set('sort_by', state.sort_by);
        }
        if (state.page > 1) {
            next.set('page', String(state.page));
        }
        if (state.limit !== config.defaultLimit) {
            next.set('limit', String(state.limit));
        }

        var nextUrl = window.location.pathname + '?' + next.toString();
        window.history.replaceState(null, '', nextUrl);
    }

    // IMPORTANT: Never send `type=` to /api/crm/order/* because router/index.php
    // enters formManager.php and exits on non-/dashboard/ URLs when `type` is set.
    function buildApiParams(includePagination) {
        var params = new URLSearchParams();
        params.set('status', state.status || 'all');
        params.set('date_basis', state.date_basis || 'created');
        if (state.all_dates) {
            params.set('all_dates', '1');
        } else {
            if (state.start_date) {
                params.set('start_date', state.start_date);
            }
            if (state.end_date) {
                params.set('end_date', state.end_date);
            }
        }
        if (state.payment_mode && state.payment_mode !== 'all') {
            params.set('payment_mode', state.payment_mode);
        }
        if (state.city && state.city !== 'all') {
            params.set('city', state.city);
        }
        if (state.q) {
            params.set('q', state.q);
        }
        if (state.sort_by && state.sort_by !== 'newest') {
            params.set('sort_by', state.sort_by);
        }
        if (includePagination) {
            params.set('page', String(state.page));
            params.set('limit', String(state.limit));
            params.set('include_facets', facetsLoaded ? '0' : '1');
        }
        return params;
    }

    function announceStatus(message, tone) {
        if (!els.statusRegion) {
            return;
        }
        if (statusClearTimer) {
            window.clearTimeout(statusClearTimer);
            statusClearTimer = null;
        }

        els.statusRegion.classList.remove('is-idle', 'is-visible', 'is-error', 'is-success', 'is-busy');

        if (tone === 'error' || tone === 'success' || tone === 'busy') {
            els.statusRegion.classList.add('is-visible', 'is-' + tone);
            els.statusRegion.textContent = message || '';
            if (tone === 'success') {
                statusClearTimer = window.setTimeout(function () {
                    els.statusRegion.classList.remove('is-visible', 'is-success');
                    els.statusRegion.classList.add('is-idle');
                }, 6000);
            }
        } else {
            els.statusRegion.classList.add('is-idle');
            els.statusRegion.textContent = message || '';
        }
    }

    function formatCurrency(symbol, amount) {
        var sym = symbol || '₹';
        var num = Number(amount || 0);
        if (!Number.isFinite(num)) {
            num = 0;
        }
        try {
            return sym + ' ' + new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(num);
        } catch (e) {
            return sym + ' ' + num.toFixed(2);
        }
    }

    function populateFacets(facets) {
        if (!facets || typeof facets !== 'object') {
            return;
        }

        if (els.paymentSelect && Array.isArray(facets.payment_modes)) {
            var currentPm = state.payment_mode;
            while (els.paymentSelect.options.length > 1) {
                els.paymentSelect.remove(1);
            }
            facets.payment_modes.forEach(function (pm) {
                var opt = document.createElement('option');
                opt.value = pm.value;
                opt.textContent = pm.label;
                els.paymentSelect.appendChild(opt);
            });
            if (currentPm !== 'all') {
                ensureOptionExists(els.paymentSelect, currentPm, currentPm);
            }
            els.paymentSelect.value = currentPm;
        }

        if (els.citySelect && Array.isArray(facets.cities)) {
            var currentCity = state.city;
            while (els.citySelect.options.length > 1) {
                els.citySelect.remove(1);
            }
            facets.cities.forEach(function (cityName) {
                var opt = document.createElement('option');
                opt.value = cityName;
                opt.textContent = cityName;
                els.citySelect.appendChild(opt);
            });
            if (currentCity !== 'all') {
                ensureOptionExists(els.citySelect, currentCity, currentCity);
            }
            els.citySelect.value = currentCity;
        }

        if (Array.isArray(facets.export_columns) && facets.export_columns.length > 0) {
            exportColumnCatalog = facets.export_columns.slice();
            selectedExportColKeys = normalizeSelectedExportKeys(selectedExportColKeys, exportColumnCatalog);
            updateExportColumnsButtonUI();
            if (els.exportColsDialog && els.exportColsDialog.open) {
                prepareExportColumnsDialog();
            }
        }

        facetsLoaded = true;
    }

    function getDefaultExportColumnKeys(catalog) {
        var list = Array.isArray(catalog) ? catalog : exportColumnCatalog;
        var out = [];
        list.forEach(function (col) {
            if (col && col.is_default) {
                out.push(col.key);
            }
        });
        if (out.length === 0) {
            list.forEach(function (col) {
                if (col && col.key) {
                    out.push(col.key);
                }
            });
        }
        return out;
    }

    function normalizeSelectedExportKeys(rawKeys, catalog) {
        var list = Array.isArray(catalog) ? catalog : exportColumnCatalog;
        var wantSet = {};
        if (Array.isArray(rawKeys)) {
            rawKeys.forEach(function (k) {
                var clean = String(k || '').trim().toLowerCase();
                if (clean) {
                    wantSet[clean] = true;
                }
            });
        }
        var matched = [];
        list.forEach(function (col) {
            if (col && wantSet[col.key]) {
                matched.push(col.key);
            }
        });
        if (matched.length > 0) {
            return matched;
        }
        return getDefaultExportColumnKeys(list);
    }

    function loadSavedExportColKeys() {
        try {
            var raw = window.localStorage.getItem(EXPORT_COLS_STORAGE_KEY);
            if (!raw) {
                return getDefaultExportColumnKeys(exportColumnCatalog);
            }
            var parsed = JSON.parse(raw);
            if (Array.isArray(parsed) && parsed.length > 0) {
                return normalizeSelectedExportKeys(parsed, exportColumnCatalog);
            }
        } catch (e) {
            // Ignore restricted storage environments and fall back to defaults
        }
        return getDefaultExportColumnKeys(exportColumnCatalog);
    }

    function persistExportColKeys(keys) {
        try {
            var defaults = getDefaultExportColumnKeys(exportColumnCatalog);
            var isSame = (keys.length === defaults.length) && keys.every(function (k, idx) {
                return k === defaults[idx];
            });
            if (isSame) {
                window.localStorage.removeItem(EXPORT_COLS_STORAGE_KEY);
            } else {
                window.localStorage.setItem(EXPORT_COLS_STORAGE_KEY, JSON.stringify(keys));
            }
        } catch (e) {
            // Ignore quota / privacy mode storage restrictions
        }
    }

    function isDefaultExportSelection(keys) {
        var list = Array.isArray(keys) ? keys : selectedExportColKeys;
        var defaults = getDefaultExportColumnKeys(exportColumnCatalog);
        if (list.length !== defaults.length) {
            return false;
        }
        return list.every(function (k, idx) {
            return k === defaults[idx];
        });
    }

    function updateExportColumnsButtonUI() {
        var count = selectedExportColKeys.length;
        var isCustom = !isDefaultExportSelection(selectedExportColKeys);
        if (els.exportColsBadge) {
            els.exportColsBadge.textContent = String(count);
        }
        if (els.exportColsBtn) {
            els.exportColsBtn.classList.toggle('has-custom', isCustom);
            els.exportColsBtn.setAttribute(
                'aria-label',
                'Choose export columns (' + count + ' selected' + (isCustom ? ', custom' : ', default') + ')'
            );
        }
    }

    function clearExportColumnsError() {
        if (els.exportColsGroups) {
            var flagged = els.exportColsGroups.querySelectorAll('.crm-col-checkbox[aria-describedby="crmExportColsError"]');
            Array.prototype.forEach.call(flagged, function (cb) {
                cb.removeAttribute('aria-describedby');
            });
        }
        if (!els.exportColsError) {
            return;
        }
        els.exportColsError.hidden = true;
    }

    function showExportColumnsError(msg) {
        if (!els.exportColsError) {
            return;
        }
        var txt = els.exportColsError.querySelector('.crm-cols-error-text');
        if (txt && msg) {
            txt.textContent = msg;
        }
        els.exportColsError.hidden = false;
    }

    function syncExportColumnsDialogSummaryAndCounts() {
        var totalCatalog = exportColumnCatalog.length;
        var selectedCount = draftExportColSet.size;
        var defKeys = getDefaultExportColumnKeys(exportColumnCatalog);
        var isExactDefault = (selectedCount === defKeys.length) && defKeys.every(function (k) {
            return draftExportColSet.has(k);
        });

        if (els.exportColsSummary) {
            var suffix = isExactDefault
                ? ' (Default)'
                : (selectedCount === totalCatalog ? ' (All)' : '');
            els.exportColsSummary.textContent = selectedCount + ' of ' + totalCatalog + ' columns selected' + suffix;
        }

        if (!els.exportColsGroups) {
            return;
        }
        var fieldsets = els.exportColsGroups.querySelectorAll('.crm-col-group');
        Array.prototype.forEach.call(fieldsets, function (fs) {
            var inputs = Array.prototype.slice.call(fs.querySelectorAll('.crm-col-checkbox'));
            var checkedCnt = 0;
            inputs.forEach(function (cb) {
                var isChecked = draftExportColSet.has(cb.value);
                cb.checked = isChecked;
                if (isChecked) {
                    checkedCnt++;
                }
            });
            var cntEl = fs.querySelector('.crm-col-group-count');
            if (cntEl) {
                cntEl.textContent = checkedCnt + ' of ' + inputs.length + ' selected';
            }
            var togBtn = fs.querySelector('.crm-col-group-toggle');
            if (togBtn) {
                var allChecked = inputs.length > 0 && checkedCnt === inputs.length;
                togBtn.textContent = allChecked ? 'Clear group' : 'Select all';
                togBtn.setAttribute('data-action', allChecked ? 'clear' : 'all');
            }
        });
    }

    function applyExportColumnsSearchFilter() {
        if (!els.exportColsGroups) {
            return;
        }
        var q = els.exportColsFilterInput ? String(els.exportColsFilterInput.value || '').trim().toLowerCase() : '';
        var visibleTotal = 0;

        var fieldsets = els.exportColsGroups.querySelectorAll('.crm-col-group');
        Array.prototype.forEach.call(fieldsets, function (fs) {
            var grpText = (fs.getAttribute('data-group-label') || '').toLowerCase();
            var grpMatched = q !== '' && grpText.indexOf(q) !== -1;
            var opts = fs.querySelectorAll('.crm-col-option');
            var visibleInGroup = 0;
            Array.prototype.forEach.call(opts, function (lbl) {
                var searchHay = (lbl.getAttribute('data-search') || '').toLowerCase();
                var matches = (q === '') || grpMatched || searchHay.indexOf(q) !== -1;
                lbl.hidden = !matches;
                if (matches) {
                    visibleInGroup++;
                    visibleTotal++;
                }
            });
            fs.hidden = (visibleInGroup === 0);
        });

        if (els.exportColsEmptySearch) {
            els.exportColsEmptySearch.hidden = (visibleTotal > 0);
        }
    }

    function renderExportColumnsDialogGroups() {
        if (!els.exportColsGroups) {
            return;
        }
        while (els.exportColsGroups.firstChild) {
            els.exportColsGroups.removeChild(els.exportColsGroups.firstChild);
        }

        var groupList = [];
        var groupMap = {};
        exportColumnCatalog.forEach(function (col) {
            var gKey = col.group || 'other';
            if (!groupMap[gKey]) {
                groupMap[gKey] = {
                    group: gKey,
                    group_label: col.group_label || gKey,
                    cols: []
                };
                groupList.push(groupMap[gKey]);
            }
            groupMap[gKey].cols.push(col);
        });

        var frag = document.createDocumentFragment();

        groupList.forEach(function (grp) {
            var fs = document.createElement('fieldset');
            fs.className = 'crm-col-group';
            fs.setAttribute('data-group', grp.group);
            fs.setAttribute('data-group-label', grp.group_label);

            // Mandatory first child of fieldset is <legend>
            var legend = document.createElement('legend');
            legend.className = 'crm-col-group-legend';
            legend.textContent = grp.group_label;
            fs.appendChild(legend);

            var metaRow = document.createElement('div');
            metaRow.className = 'crm-col-group-meta';

            var countSpan = document.createElement('span');
            countSpan.className = 'crm-col-group-count';
            metaRow.appendChild(countSpan);

            var togBtn = document.createElement('button');
            togBtn.type = 'button';
            togBtn.className = 'crm-col-group-toggle';
            togBtn.setAttribute('aria-label', grp.group_label + ' columns toggle');
            togBtn.addEventListener('click', function () {
                var act = togBtn.getAttribute('data-action') === 'clear' ? 'clear' : 'all';
                grp.cols.forEach(function (col) {
                    if (act === 'clear') {
                        draftExportColSet.delete(col.key);
                    } else {
                        draftExportColSet.add(col.key);
                    }
                });
                if (draftExportColSet.size > 0) {
                    clearExportColumnsError();
                }
                syncExportColumnsDialogSummaryAndCounts();
            });
            metaRow.appendChild(togBtn);
            fs.appendChild(metaRow);

            var grid = document.createElement('div');
            grid.className = 'crm-col-group-grid';

            grp.cols.forEach(function (col) {
                var inputId = 'crmExportCol_' + col.key;
                var lbl = document.createElement('label');
                lbl.className = 'crm-col-option';
                lbl.setAttribute('for', inputId);
                lbl.setAttribute('data-search', (col.label + ' ' + col.key).toLowerCase());

                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'crm-col-checkbox';
                cb.id = inputId;
                cb.name = 'export_cols[]';
                cb.value = col.key;
                cb.checked = draftExportColSet.has(col.key);
                cb.addEventListener('change', function () {
                    if (cb.checked) {
                        draftExportColSet.add(col.key);
                        clearExportColumnsError();
                    } else {
                        draftExportColSet.delete(col.key);
                    }
                    syncExportColumnsDialogSummaryAndCounts();
                });

                var bodySpan = document.createElement('span');
                bodySpan.className = 'crm-col-option-body';

                var txtSpan = document.createElement('span');
                txtSpan.className = 'crm-col-option-label';
                txtSpan.textContent = col.label;
                bodySpan.appendChild(txtSpan);

                if (col.is_default) {
                    var defTag = document.createElement('span');
                    defTag.className = 'crm-col-default-tag';
                    defTag.textContent = 'Default';
                    bodySpan.appendChild(defTag);
                }

                lbl.appendChild(cb);
                lbl.appendChild(bodySpan);
                grid.appendChild(lbl);
            });

            fs.appendChild(grid);
            frag.appendChild(fs);
        });

        els.exportColsGroups.appendChild(frag);
        syncExportColumnsDialogSummaryAndCounts();
        applyExportColumnsSearchFilter();
    }

    function prepareExportColumnsDialog() {
        draftExportColSet = new Set(selectedExportColKeys);
        clearExportColumnsError();
        if (els.exportColsFilterInput) {
            els.exportColsFilterInput.value = '';
        }
        renderExportColumnsDialogGroups();
    }

    function initExportColumns() {
        selectedExportColKeys = loadSavedExportColKeys();
        updateExportColumnsButtonUI();
        renderExportColumnsDialogGroups();
    }

    function updateSummaryAndPills(summary) {
        if (!summary || typeof summary !== 'object') {
            return;
        }
        var counts = summary.status_counts || {};
        var allCount = 0;
        ['0', '1', '2', '3', '4', '5', '6'].forEach(function (code) {
            allCount += parseInt(counts[code] || 0, 10) || 0;
        });

        if (els.summaryTotal) {
            els.summaryTotal.textContent = String(summary.total_orders || 0);
        }
        if (els.summaryRevenue) {
            els.summaryRevenue.textContent = formatCurrency(summary.currency_symbol || '₹', summary.filtered_revenue || 0);
        }
        if (els.summaryPending) {
            els.summaryPending.textContent = String(counts['0'] || 0);
        }
        if (els.summaryDelivered) {
            els.summaryDelivered.textContent = String(counts['6'] || 0);
        }

        els.statusPills.forEach(function (pill) {
            var code = pill.getAttribute('data-status') || 'all';
            var countEl = pill.querySelector('.crm-pill-count');
            if (!countEl) {
                return;
            }
            if (code === 'all') {
                countEl.textContent = String(allCount);
            } else {
                countEl.textContent = String(counts[code] || 0);
            }
        });
    }

    function isSafeHttpUrl(url) {
        if (typeof url !== 'string' || !url.trim()) {
            return false;
        }
        var trimmed = url.trim();
        return /^https?:\/\//i.test(trimmed) || trimmed.charAt(0) === '/';
    }

    function makeTextLine(className, text, prefixStrong) {
        var div = document.createElement('div');
        div.className = className;
        if (prefixStrong) {
            var strong = document.createElement('strong');
            strong.textContent = prefixStrong + ' ';
            div.appendChild(strong);
        }
        div.appendChild(document.createTextNode(text));
        return div;
    }

    function appendWrappedEmail(container, emailStr) {
        var atIdx = emailStr.indexOf('@');
        if (atIdx > 0 && atIdx < emailStr.length - 1) {
            container.appendChild(document.createTextNode(emailStr.slice(0, atIdx + 1)));
            container.appendChild(document.createElement('wbr'));
            container.appendChild(document.createTextNode(emailStr.slice(atIdx + 1)));
        } else {
            container.appendChild(document.createTextNode(emailStr));
        }
    }

    function makeChevronSvg() {
        var ns = 'http://www.w3.org/2000/svg';
        var svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('focusable', 'false');
        svg.setAttribute('class', 'crm-chevron-icon');
        svg.setAttribute('width', '13');
        svg.setAttribute('height', '13');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '2.2');
        svg.setAttribute('stroke-linecap', 'round');
        svg.setAttribute('stroke-linejoin', 'round');
        var poly = document.createElementNS(ns, 'polyline');
        poly.setAttribute('points', '6 9 12 15 18 9');
        svg.appendChild(poly);
        return svg;
    }

    function buildDetailDrawer(row, currSym) {
        var prod = row.product || {};
        var items = Array.isArray(prod.items) ? prod.items : [];
        var sender = row.sender || {};
        var receiver = row.receiver || {};
        var deliv = row.delivery || {};
        var pay = row.payment || {};

        var drawer = document.createElement('div');
        drawer.className = 'crm-detail-drawer';

        var grid = document.createElement('div');
        grid.className = 'crm-detail-grid';

        function createSection(headingText) {
            var sec = document.createElement('section');
            sec.className = 'crm-detail-section';
            var h = document.createElement('h3');
            h.className = 'crm-detail-heading';
            h.textContent = headingText;
            sec.appendChild(h);
            return sec;
        }

        function appendDlRow(dl, keyText, valText, opts) {
            if (!valText && valText !== 0) {
                return;
            }
            var rowWrap = document.createElement('div');
            rowWrap.className = 'crm-detail-kv';
            var dt = document.createElement('dt');
            dt.textContent = keyText;
            var dd = document.createElement('dd');
            if (opts && opts.wrapEmail) {
                dd.className = 'crm-cell-email';
                appendWrappedEmail(dd, String(valText));
            } else if (opts && opts.mono) {
                var code = document.createElement('code');
                code.className = 'crm-detail-code';
                code.textContent = String(valText);
                dd.appendChild(code);
            } else {
                dd.textContent = String(valText);
            }
            rowWrap.appendChild(dt);
            rowWrap.appendChild(dd);
            dl.appendChild(rowWrap);
        }

        // 1. Products & Pricing Breakdown
        var secProd = createSection('Items & Pricing');
        if (items.length > 0) {
            var ul = document.createElement('ul');
            ul.className = 'crm-detail-items';
            items.forEach(function (it) {
                var li = document.createElement('li');
                li.className = 'crm-detail-item';

                var itemHead = document.createElement('div');
                itemHead.className = 'crm-detail-item-head';
                if (it.website_url && isSafeHttpUrl(it.website_url)) {
                    var prodLink = document.createElement('a');
                    prodLink.className = 'crm-detail-link';
                    prodLink.href = it.website_url;
                    prodLink.target = '_blank';
                    prodLink.rel = 'noopener noreferrer';
                    prodLink.textContent = it.title || 'Item';
                    itemHead.appendChild(prodLink);
                } else {
                    var prodName = document.createElement('span');
                    prodName.className = 'crm-detail-item-title';
                    prodName.textContent = it.title || 'Item';
                    itemHead.appendChild(prodName);
                }
                var qPill = document.createElement('span');
                qPill.className = 'crm-qty-pill';
                qPill.textContent = '×' + String(it.qty || 1);
                itemHead.appendChild(qPill);
                li.appendChild(itemHead);

                var metaBits = [];
                if (it.sku) {
                    metaBits.push('SKU: ' + it.sku);
                }
                if (typeof it.unit_price_num === 'number' && Number.isFinite(it.unit_price_num)) {
                    metaBits.push('Unit: ' + formatCurrency(currSym, it.unit_price_num));
                } else if (it.unit_price) {
                    metaBits.push('Unit: ' + currSym + ' ' + it.unit_price);
                }
                if (metaBits.length > 0) {
                    li.appendChild(makeTextLine('crm-detail-item-meta', metaBits.join(' · ')));
                }
                ul.appendChild(li);
            });
            secProd.appendChild(ul);
        }
        var dlPrice = document.createElement('dl');
        dlPrice.className = 'crm-detail-dl';
        if (typeof prod.delivery_charges_num === 'number' && prod.delivery_charges_num > 0) {
            appendDlRow(dlPrice, 'Delivery Fee', formatCurrency(currSym, prod.delivery_charges_num));
        }
        if (prod.coupon) {
            appendDlRow(dlPrice, 'Coupon', prod.coupon);
        }
        if (prod.amount_display) {
            appendDlRow(dlPrice, 'Order Total', prod.amount_display);
        }
        if (dlPrice.childNodes.length > 0) {
            secProd.appendChild(dlPrice);
        }
        grid.appendChild(secProd);

        // 2. Sender & Receiver Full Contact / Address
        var secContact = createSection('Contact & Shipping Address');
        var dlContact = document.createElement('dl');
        dlContact.className = 'crm-detail-dl';
        if (sender.email) {
            appendDlRow(dlContact, 'Sender Email', sender.email, { wrapEmail: true });
        }
        if (sender.alt_mobile) {
            appendDlRow(dlContact, 'Sender Alt Ph', sender.alt_mobile);
        }
        if (receiver.alt_mobile) {
            appendDlRow(dlContact, 'Receiver Alt Ph', receiver.alt_mobile);
        }
        if (receiver.address_type) {
            appendDlRow(dlContact, 'Address Type', receiver.address_type);
        }
        appendDlRow(dlContact, 'Full Address', receiver.full_address || receiver.city || 'N/A');
        secContact.appendChild(dlContact);
        grid.appendChild(secContact);

        // 3. Delivery Schedule & Gift Instructions
        var secDeliv = createSection('Delivery & Instructions');
        var dlDeliv = document.createElement('dl');
        dlDeliv.className = 'crm-detail-dl';
        appendDlRow(dlDeliv, 'Scheduled', deliv.date_display || 'Not scheduled');
        var slotWindow = [deliv.slot, (deliv.time && String(deliv.time).toLowerCase() !== String(deliv.slot || '').toLowerCase()) ? deliv.time : '']
            .filter(Boolean)
            .join(' · ');
        if (slotWindow) {
            appendDlRow(dlDeliv, 'Slot / Window', slotWindow);
        }
        if (deliv.occasion) {
            appendDlRow(dlDeliv, 'Occasion', deliv.occasion);
        }
        if (dlDeliv.childNodes.length > 0) {
            secDeliv.appendChild(dlDeliv);
        }
        if (deliv.message) {
            var msgBox = document.createElement('div');
            msgBox.className = 'crm-detail-msg';
            var msgLabel = document.createElement('span');
            msgLabel.className = 'crm-detail-msg-label';
            msgLabel.textContent = 'Gift Message';
            var msgBody = document.createElement('p');
            msgBody.className = 'crm-detail-msg-text';
            msgBody.textContent = deliv.message;
            msgBox.appendChild(msgLabel);
            msgBox.appendChild(msgBody);
            secDeliv.appendChild(msgBox);
        }
        if (deliv.comment) {
            var dlNote = document.createElement('dl');
            dlNote.className = 'crm-detail-dl';
            appendDlRow(dlNote, 'Delivery Note', deliv.comment);
            secDeliv.appendChild(dlNote);
        }
        if (deliv.return_reason || deliv.return_comment) {
            var dlRet = document.createElement('dl');
            dlRet.className = 'crm-detail-dl';
            appendDlRow(dlRet, 'Return Info', [deliv.return_reason, deliv.return_comment].filter(Boolean).join(' — '));
            secDeliv.appendChild(dlRet);
        }
        if (deliv.photo_url && isSafeHttpUrl(deliv.photo_url)) {
            var pWrap = document.createElement('div');
            pWrap.className = 'crm-detail-photo-wrap';
            var photoA = document.createElement('a');
            photoA.className = 'crm-photo-link';
            photoA.href = deliv.photo_url;
            photoA.target = '_blank';
            photoA.rel = 'noopener noreferrer';
            photoA.textContent = 'View Delivery Photo (' + (row.order_code || row.order_id) + ')';
            pWrap.appendChild(photoA);
            secDeliv.appendChild(pWrap);
        }
        grid.appendChild(secDeliv);

        // 4. Payment & Reference
        var secPay = createSection('Payment & Audit');
        var dlPay = document.createElement('dl');
        dlPay.className = 'crm-detail-dl';
        appendDlRow(dlPay, 'Created', row.created_display || 'N/A');
        appendDlRow(dlPay, 'Payment Mode', pay.mode_label || 'N/A');
        if (pay.transaction_status_label) {
            appendDlRow(dlPay, 'Tx Status', pay.transaction_status_label);
        }
        if (pay.transaction_ref) {
            appendDlRow(dlPay, 'Transaction Ref', pay.transaction_ref, { mono: true });
        }
        secPay.appendChild(dlPay);
        grid.appendChild(secPay);

        drawer.appendChild(grid);
        return drawer;
    }

    function resolveOrderViewUrl(row) {
        if (row.view_url && String(row.view_url).charAt(0) === '/') {
            return String(row.view_url);
        }
        if (row.view_url) {
            return '/dashboard/' + String(row.view_url).replace(/^\/+/, '');
        }
        return '/dashboard/order/?type=view&id=' + encodeURIComponent(btoa(String(row.order_id || '')));
    }

    function renderRows(rows, pagination) {
        if (!els.tableBody) {
            return;
        }
        while (els.tableBody.firstChild) {
            els.tableBody.removeChild(els.tableBody.firstChild);
        }

        if (els.tableCaption) {
            var total = (pagination && typeof pagination.total === 'number') ? pagination.total : rows.length;
            if (total === 0) {
                els.tableCaption.textContent = 'Orders Directory — 0 matching orders';
            } else {
                var page = pagination ? pagination.page : 1;
                var limit = pagination ? pagination.limit : rows.length;
                var startIdx = ((page - 1) * limit) + 1;
                var endIdx = Math.min(total, startIdx + rows.length - 1);
                els.tableCaption.textContent = 'Orders Directory — Showing ' + startIdx + '–' + endIdx + ' of ' + total + ' matching order(s)';
            }
        }

        if (!Array.isArray(rows) || rows.length === 0) {
            var emptyTr = document.createElement('tr');
            var emptyTd = document.createElement('td');
            emptyTd.colSpan = 7;
            emptyTd.className = 'crm-empty-cell';
            emptyTd.textContent = 'No orders matched your active filter criteria.';
            emptyTr.appendChild(emptyTd);
            els.tableBody.appendChild(emptyTr);
            return;
        }

        var fragment = document.createDocumentFragment();

        rows.forEach(function (row, rowIdx) {
            var tr = document.createElement('tr');
            tr.className = 'crm-order-row';
            var viewHref = resolveOrderViewUrl(row);
            var orderCode = row.order_code || ('OD' + row.order_id);
            var detailId = 'crmOrderDetail_' + String(row.order_id || rowIdx) + '_' + rowIdx;
            var prod = row.product || {};
            var currSym = prod.currency_symbol || '₹';

            // 1. Order (<th scope="row">) — Thumb + Order ID + single-line timestamp
            var thOrder = document.createElement('th');
            thOrder.scope = 'row';

            var cellOrderWrap = document.createElement('div');
            cellOrderWrap.className = 'crm-cell-order';

            var thumbBox = document.createElement('div');
            thumbBox.className = 'crm-thumb';
            var primaryImg = prod.primary_image ? String(prod.primary_image).trim() : '';
            if (isSafeHttpUrl(primaryImg)) {
                var imgEl = document.createElement('img');
                imgEl.src = primaryImg;
                imgEl.alt = '';
                imgEl.loading = 'lazy';
                imgEl.decoding = 'async';
                imgEl.addEventListener('error', function () {
                    if (imgEl.parentNode) {
                        imgEl.parentNode.removeChild(imgEl);
                    }
                    var fallbackAbbr = document.createElement('span');
                    fallbackAbbr.textContent = 'OD';
                    thumbBox.appendChild(fallbackAbbr);
                }, { once: true });
                thumbBox.appendChild(imgEl);
            } else {
                var abbr = document.createElement('span');
                abbr.textContent = 'OD';
                thumbBox.appendChild(abbr);
            }

            var orderInfo = document.createElement('div');
            orderInfo.className = 'crm-order-meta';
            var idLink = document.createElement('a');
            idLink.className = 'crm-order-id-link';
            idLink.href = viewHref;
            idLink.textContent = orderCode;
            orderInfo.appendChild(idLink);

            var cDate = row.created_date_display || '';
            var cTime = row.created_time_display || '';
            if (!cDate && row.created_display) {
                var splitIdx = row.created_display.indexOf(' ');
                if (splitIdx > 0) {
                    cDate = row.created_display.slice(0, splitIdx);
                    cTime = row.created_display.slice(splitIdx + 1);
                } else {
                    cDate = row.created_display;
                }
            }
            var stampWrap = document.createElement('div');
            stampWrap.className = 'crm-order-stamp';
            var dateSpan = document.createElement('span');
            dateSpan.className = 'crm-order-date';
            dateSpan.textContent = cDate || 'N/A';
            stampWrap.appendChild(dateSpan);
            if (cTime) {
                var sepSpan = document.createElement('span');
                sepSpan.className = 'crm-inline-dot';
                sepSpan.setAttribute('aria-hidden', 'true');
                sepSpan.textContent = '·';
                stampWrap.appendChild(sepSpan);

                var timeSpan = document.createElement('span');
                timeSpan.className = 'crm-order-time';
                timeSpan.textContent = cTime;
                stampWrap.appendChild(timeSpan);
            }
            orderInfo.appendChild(stampWrap);

            cellOrderWrap.appendChild(thumbBox);
            cellOrderWrap.appendChild(orderInfo);
            thOrder.appendChild(cellOrderWrap);
            tr.appendChild(thOrder);

            // 2. Product — Primary item title + inline qty badge (+ concise extra items count if multi-item)
            var tdProd = document.createElement('td');
            var items = Array.isArray(prod.items) ? prod.items : [];
            if (items.length === 0) {
                tdProd.appendChild(makeTextLine('crm-cell-primary crm-cell-truncate', prod.primary_title || 'N/A'));
            } else {
                var firstItem = items[0];
                var firstQty = firstItem.qty || 1;
                var prodRow = document.createElement('div');
                prodRow.className = 'crm-product-row';

                var titleSpan = document.createElement('span');
                titleSpan.className = 'crm-product-title';
                titleSpan.textContent = firstItem.title || 'Item';
                prodRow.appendChild(titleSpan);

                var qtyBadge = document.createElement('span');
                qtyBadge.className = 'crm-qty-pill' + (firstQty > 1 ? ' is-multi' : '');
                qtyBadge.textContent = '×' + String(firstQty);
                prodRow.appendChild(qtyBadge);
                tdProd.appendChild(prodRow);

                if (items.length > 1) {
                    var totalQty = prod.total_qty || items.length;
                    var extraCount = items.length - 1;
                    tdProd.appendChild(makeTextLine(
                        'crm-cell-secondary',
                        '+' + extraCount + ' more item' + (extraCount > 1 ? 's' : '') + ' · ' + totalQty + ' qty'
                    ));
                }
            }
            tr.appendChild(tdProd);

            // 3. Sender — Name + Mobile cleanly (Email/Alt moved to Details drawer)
            var tdSender = document.createElement('td');
            var sender = row.sender || {};
            tdSender.appendChild(makeTextLine('crm-cell-primary crm-cell-truncate', sender.name || 'N/A'));
            if (sender.mobile) {
                tdSender.appendChild(makeTextLine('crm-cell-secondary crm-cell-truncate', sender.mobile));
            } else if (sender.email) {
                var emailDiv = document.createElement('div');
                emailDiv.className = 'crm-cell-secondary crm-cell-email crm-cell-truncate';
                appendWrappedEmail(emailDiv, sender.email);
                tdSender.appendChild(emailDiv);
            }
            tr.appendChild(tdSender);

            // 4. Receiver — Name + Mobile · City concisely (Full street address moved to Details drawer)
            var tdReceiver = document.createElement('td');
            var receiver = row.receiver || {};
            tdReceiver.appendChild(makeTextLine('crm-cell-primary crm-cell-truncate', receiver.name || 'N/A'));
            var locLabel = receiver.city || receiver.state || receiver.pin_code || '';
            if (receiver.mobile || locLabel) {
                var recMeta = document.createElement('div');
                recMeta.className = 'crm-receiver-meta crm-cell-secondary';
                if (receiver.mobile) {
                    var phoneSpan = document.createElement('span');
                    phoneSpan.className = 'crm-inline-phone';
                    phoneSpan.textContent = receiver.mobile;
                    recMeta.appendChild(phoneSpan);
                }
                if (receiver.mobile && locLabel) {
                    var sepDot = document.createElement('span');
                    sepDot.className = 'crm-inline-dot';
                    sepDot.setAttribute('aria-hidden', 'true');
                    sepDot.textContent = '·';
                    recMeta.appendChild(sepDot);
                }
                if (locLabel) {
                    var citySpan = document.createElement('span');
                    citySpan.className = 'crm-inline-city';
                    citySpan.textContent = locLabel;
                    recMeta.appendChild(citySpan);
                }
                tdReceiver.appendChild(recMeta);
            }
            tr.appendChild(tdReceiver);

            // 5. Delivery — Date · Slot + compact secondary tags (Gift messages/notes in Details drawer)
            var tdDeliv = document.createElement('td');
            var deliv = row.delivery || {};
            var delivHead = document.createElement('div');
            delivHead.className = 'crm-deliv-head';

            var delivDateSpan = document.createElement('span');
            delivDateSpan.className = 'crm-deliv-date' + ((!deliv.date_display || deliv.date_display === 'Not scheduled') ? ' is-muted' : '');
            delivDateSpan.textContent = deliv.date_display || 'Not scheduled';
            delivHead.appendChild(delivDateSpan);

            if (deliv.slot) {
                var slotDot = document.createElement('span');
                slotDot.className = 'crm-inline-dot';
                slotDot.setAttribute('aria-hidden', 'true');
                slotDot.textContent = '·';
                delivHead.appendChild(slotDot);

                var slotSpan = document.createElement('span');
                slotSpan.className = 'crm-deliv-slot';
                slotSpan.textContent = deliv.slot;
                delivHead.appendChild(slotSpan);
            }
            tdDeliv.appendChild(delivHead);

            var hasDistinctTime = !!(deliv.time && String(deliv.time).toLowerCase() !== String(deliv.slot || '').toLowerCase());
            var hasPhoto = !!(deliv.photo_url && isSafeHttpUrl(deliv.photo_url));
            var hasReturn = !!(deliv.return_reason || deliv.return_comment);
            var hasGiftMsg = !!deliv.message;
            var hasDelivNote = !!deliv.comment;

            if (hasDistinctTime || deliv.occasion || hasReturn || hasGiftMsg || hasDelivNote || hasPhoto) {
                var delivTags = document.createElement('div');
                delivTags.className = 'crm-deliv-tags';
                var metaTextParts = [];
                if (hasDistinctTime) {
                    metaTextParts.push(deliv.time);
                }
                if (deliv.occasion) {
                    metaTextParts.push(deliv.occasion);
                }
                if (metaTextParts.length > 0) {
                    var occSpan = document.createElement('span');
                    occSpan.className = 'crm-deliv-subtext';
                    occSpan.textContent = metaTextParts.join(' · ');
                    delivTags.appendChild(occSpan);
                }
                if (hasReturn) {
                    var retTag = document.createElement('span');
                    retTag.className = 'crm-note-tag is-return';
                    retTag.textContent = 'Return';
                    delivTags.appendChild(retTag);
                } else if (hasGiftMsg || hasDelivNote) {
                    var msgTag = document.createElement('span');
                    msgTag.className = 'crm-note-tag';
                    msgTag.textContent = hasGiftMsg ? 'Gift note' : 'Note';
                    delivTags.appendChild(msgTag);
                }
                if (hasPhoto) {
                    var photoLink = document.createElement('a');
                    photoLink.className = 'crm-photo-link';
                    photoLink.href = deliv.photo_url;
                    photoLink.target = '_blank';
                    photoLink.rel = 'noopener noreferrer';
                    photoLink.setAttribute('aria-label', 'View delivery photo for ' + orderCode);
                    photoLink.textContent = 'Photo';
                    delivTags.appendChild(photoLink);
                }
                tdDeliv.appendChild(delivTags);
            }
            tr.appendChild(tdDeliv);

            // 6. Status & Payment — Status pill + Amount · Mode (Tx Ref & Coupon moved to Details drawer)
            var tdStatus = document.createElement('td');
            var st = row.status || { label: 'Unknown', class: 'status-pending' };
            var badge = document.createElement('span');
            badge.className = 'crm-badge ' + (st.class || 'status-pending');
            badge.textContent = st.label || 'Unknown';
            tdStatus.appendChild(badge);

            var pay = row.payment || {};
            var amtDisplay = prod.amount_display ? prod.amount_display : '';
            if (amtDisplay || pay.mode_label) {
                var payRow = document.createElement('div');
                payRow.className = 'crm-pay-row';
                if (amtDisplay) {
                    var amtSpan = document.createElement('span');
                    amtSpan.className = 'crm-amount-val';
                    amtSpan.textContent = amtDisplay;
                    payRow.appendChild(amtSpan);
                }
                if (amtDisplay && pay.mode_label) {
                    var payDot = document.createElement('span');
                    payDot.className = 'crm-inline-dot';
                    payDot.setAttribute('aria-hidden', 'true');
                    payDot.textContent = '·';
                    payRow.appendChild(payDot);
                }
                if (pay.mode_label) {
                    var modeSpan = document.createElement('span');
                    modeSpan.className = 'crm-pay-mode';
                    modeSpan.textContent = pay.mode_label;
                    payRow.appendChild(modeSpan);
                }
                tdStatus.appendChild(payRow);
            }
            tr.appendChild(tdStatus);

            // 7. Action — Progressive Details toggle + View link
            var tdAction = document.createElement('td');
            tdAction.className = 'crm-action-cell';
            var actionGroup = document.createElement('div');
            actionGroup.className = 'crm-action-group';

            var detailTr = document.createElement('tr');
            detailTr.id = detailId;
            detailTr.className = 'crm-detail-row';
            detailTr.hidden = true;
            var detailTd = document.createElement('td');
            detailTd.colSpan = 7;
            detailTd.className = 'crm-detail-cell';
            detailTd.appendChild(buildDetailDrawer(row, currSym));
            detailTr.appendChild(detailTd);

            var expandBtn = document.createElement('button');
            expandBtn.type = 'button';
            expandBtn.className = 'crm-btn crm-detail-toggle-btn';
            expandBtn.setAttribute('aria-expanded', 'false');
            expandBtn.setAttribute('aria-controls', detailId);
            expandBtn.setAttribute('aria-label', 'Show full details for ' + orderCode);
            var expText = document.createElement('span');
            expText.className = 'crm-detail-toggle-text';
            expText.textContent = 'More';
            expandBtn.appendChild(expText);
            expandBtn.appendChild(makeChevronSvg());

            expandBtn.addEventListener('click', function () {
                var isExpanded = expandBtn.getAttribute('aria-expanded') === 'true';
                var nextExpanded = !isExpanded;
                expandBtn.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
                expandBtn.setAttribute('aria-label', (nextExpanded ? 'Hide' : 'Show') + ' full details for ' + orderCode);
                detailTr.hidden = !nextExpanded;
                tr.classList.toggle('is-expanded', nextExpanded);
            });

            var viewBtn = document.createElement('a');
            viewBtn.className = 'crm-btn crm-view-btn';
            viewBtn.href = viewHref;
            viewBtn.setAttribute('aria-label', 'View ' + orderCode);
            viewBtn.textContent = 'View';

            actionGroup.appendChild(expandBtn);
            actionGroup.appendChild(viewBtn);
            tdAction.appendChild(actionGroup);
            tr.appendChild(tdAction);

            fragment.appendChild(tr);
            fragment.appendChild(detailTr);
        });

        els.tableBody.appendChild(fragment);
    }

    function renderPagination(pagination) {
        if (!els.paginationInfo || !els.paginationControls) {
            return;
        }
        while (els.paginationControls.firstChild) {
            els.paginationControls.removeChild(els.paginationControls.firstChild);
        }

        var total = pagination ? (pagination.total || 0) : 0;
        var page = pagination ? (pagination.page || 1) : 1;
        var limit = pagination ? (pagination.limit || state.limit) : state.limit;
        var totalPages = pagination ? (pagination.total_pages || 1) : 1;

        if (total === 0) {
            els.paginationInfo.textContent = '0 orders found';
            return;
        }

        var startIdx = ((page - 1) * limit) + 1;
        var endIdx = Math.min(total, startIdx + (total > 0 ? Math.min(limit, total - startIdx + 1) : 0) - 1);
        els.paginationInfo.textContent = 'Showing ' + startIdx + '–' + endIdx + ' of ' + total + ' orders (Page ' + page + ' of ' + totalPages + ')';

        function createPageBtn(label, targetPage, disabled, isCurrent) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'crm-page-btn';
            btn.textContent = label;
            btn.disabled = !!disabled;
            if (isCurrent) {
                btn.setAttribute('aria-current', 'page');
            }
            if (!disabled && !isCurrent) {
                btn.addEventListener('click', function () {
                    state.page = targetPage;
                    loadOrders(true);
                });
            }
            return btn;
        }

        els.paginationControls.appendChild(createPageBtn('Prev', Math.max(1, page - 1), page <= 1, false));

        var windowStart = Math.max(1, page - 2);
        var windowEnd = Math.min(totalPages, page + 2);

        if (windowStart > 1) {
            els.paginationControls.appendChild(createPageBtn('1', 1, false, page === 1));
            if (windowStart > 2) {
                var ellStart = document.createElement('span');
                ellStart.className = 'crm-cell-secondary';
                ellStart.textContent = '…';
                els.paginationControls.appendChild(ellStart);
            }
        }

        for (var p = windowStart; p <= windowEnd; p++) {
            els.paginationControls.appendChild(createPageBtn(String(p), p, false, p === page));
        }

        if (windowEnd < totalPages) {
            if (windowEnd < totalPages - 1) {
                var ellEnd = document.createElement('span');
                ellEnd.className = 'crm-cell-secondary';
                ellEnd.textContent = '…';
                els.paginationControls.appendChild(ellEnd);
            }
            els.paginationControls.appendChild(createPageBtn(String(totalPages), totalPages, false, page === totalPages));
        }

        els.paginationControls.appendChild(createPageBtn('Next', Math.min(totalPages, page + 1), page >= totalPages, false));
    }

    async function loadOrders(syncUrl) {
        if (activeAbortController) {
            activeAbortController.abort();
        }
        activeAbortController = (typeof AbortController !== 'undefined') ? new AbortController() : null;

        if (syncUrl !== false) {
            pushBrowserHistoryUrl();
        }

        var params = buildApiParams(true);
        var url = config.listApi + '?' + params.toString();

        try {
            var res = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
                signal: activeAbortController ? activeAbortController.signal : undefined
            });

            if (res.redirected) {
                throw new Error('Session expired or redirected. Please refresh and sign in again.');
            }

            var contentType = res.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') === -1) {
                throw new Error('Unexpected response from server (' + res.status + ').');
            }

            var payload = await res.json();
            if (!res.ok || payload.status !== 'success') {
                throw new Error(payload.message || ('Unable to load orders (' + res.status + ').'));
            }

            if (payload.meta && payload.meta.today && parseIsoParts(payload.meta.today)) {
                config.todayIso = payload.meta.today;
            }
            if (payload.facets && (payload.facets.payment_modes || payload.facets.cities || payload.facets.export_columns)) {
                populateFacets(payload.facets);
            }
            updateSummaryAndPills(payload.summary);
            updateActiveFiltersUI();
            renderRows(payload.data || [], payload.pagination);
            renderPagination(payload.pagination);

            var totalFound = payload.pagination ? payload.pagination.total : 0;
            announceStatus('Loaded ' + totalFound + ' matching order(s).', '');
        } catch (err) {
            if (err && err.name === 'AbortError') {
                return;
            }
            announceStatus((err && err.message) ? err.message : 'Failed to load orders.', 'error');
        }
    }

    function buildExportUrl(formatName) {
        var params = buildApiParams(false);
        params.set('export_format', formatName);
        if (Array.isArray(selectedExportColKeys) && selectedExportColKeys.length > 0) {
            params.set('columns', selectedExportColKeys.join(','));
        }
        return config.exportApi + '?' + params.toString();
    }

    function extractFilename(contentDisposition, fallbackName) {
        if (!contentDisposition) {
            return fallbackName;
        }
        var utfMatch = /filename\*=UTF-8''([^;]+)/i.exec(contentDisposition);
        if (utfMatch && utfMatch[1]) {
            try {
                return decodeURIComponent(utfMatch[1].trim());
            } catch (e) {
                return utfMatch[1].trim();
            }
        }
        var asciiMatch = /filename="?([^";\r\n]+)"?/i.exec(contentDisposition);
        if (asciiMatch && asciiMatch[1]) {
            return asciiMatch[1].trim();
        }
        return fallbackName;
    }

    function triggerBlobDownload(blob, filename) {
        var blobUrl = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename;
        link.hidden = true;
        document.body.appendChild(link);
        link.click();
        window.setTimeout(function () {
            if (link.parentNode) {
                link.parentNode.removeChild(link);
            }
            URL.revokeObjectURL(blobUrl);
        }, 1500);
    }

    function setButtonBusy(btn, busy, busyLabel) {
        if (!btn) {
            return;
        }
        var labelTarget = btn.querySelector('.crm-btn-label') || btn;
        if (busy) {
            if (!btn.dataset.originalLabel) {
                btn.dataset.originalLabel = labelTarget.textContent;
            }
            btn.setAttribute('aria-disabled', 'true');
            labelTarget.textContent = busyLabel || 'Exporting...';
        } else {
            btn.setAttribute('aria-disabled', 'false');
            if (btn.dataset.originalLabel) {
                labelTarget.textContent = btn.dataset.originalLabel;
            }
        }
    }

    async function handleFileDownloadExport(formatName, fallbackExt, btn) {
        if (btn && btn.getAttribute('aria-disabled') === 'true') {
            return;
        }
        var todaySlug = getTodayIso();
        var fallbackFilename = 'crm-orders-' + todaySlug + '.' + fallbackExt;

        setButtonBusy(btn, true, 'Generating ' + fallbackExt.toUpperCase() + '...');
        announceStatus('Preparing ' + fallbackExt.toUpperCase() + ' export...', 'busy');

        try {
            var res = await fetch(buildExportUrl(formatName), {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (res.redirected) {
                throw new Error('Session expired. Please sign in again before exporting.');
            }

            var contentType = (res.headers.get('content-type') || '').toLowerCase();
            if (!res.ok || contentType.indexOf('application/json') !== -1 || contentType.indexOf('text/html') !== -1) {
                var errMsg = 'Export failed (' + res.status + ').';
                if (contentType.indexOf('application/json') !== -1) {
                    var errJson = await res.json();
                    if (errJson && errJson.message) {
                        errMsg = errJson.message;
                    }
                }
                throw new Error(errMsg);
            }

            var blob = await res.blob();
            var filename = extractFilename(res.headers.get('content-disposition'), fallbackFilename);
            triggerBlobDownload(blob, filename);
            announceStatus(fallbackExt.toUpperCase() + ' export downloaded (' + filename + ').', 'success');
        } catch (err) {
            announceStatus((err && err.message) ? err.message : 'Unable to complete export.', 'error');
        } finally {
            setButtonBusy(btn, false);
        }
    }

    function loadScriptSequential(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                if (existing.getAttribute('data-loaded') === '1') {
                    resolve();
                    return;
                }
                existing.addEventListener('load', function () { resolve(); }, { once: true });
                existing.addEventListener('error', function () { reject(new Error('Failed to load ' + src)); }, { once: true });
                return;
            }
            var script = document.createElement('script');
            script.src = src;
            script.async = false;
            script.addEventListener('load', function () {
                script.setAttribute('data-loaded', '1');
                resolve();
            }, { once: true });
            script.addEventListener('error', function () {
                reject(new Error('Failed to load ' + src));
            }, { once: true });
            document.head.appendChild(script);
        });
    }

    function ensurePdfMakeReady() {
        if (window.pdfMake && window.pdfMake.vfs && typeof window.pdfMake.createPdf === 'function') {
            return Promise.resolve(window.pdfMake);
        }
        if (!pdfLibsPromise) {
            var base = config.pdfPluginBase.replace(/\/+$/, '') + '/';
            pdfLibsPromise = loadScriptSequential(base + 'pdfmake.min.js')
                .then(function () {
                    return loadScriptSequential(base + 'vfs_fonts.min.js');
                })
                .then(function () {
                    if (!window.pdfMake || typeof window.pdfMake.createPdf !== 'function') {
                        throw new Error('PDF generator failed to initialize.');
                    }
                    return window.pdfMake;
                })
                .catch(function (err) {
                    pdfLibsPromise = null;
                    throw err;
                });
        }
        return pdfLibsPromise;
    }

    function pdfSafeText(str) {
        return String(str === null || typeof str === 'undefined' ? '' : str)
            .replace(/₹/g, 'Rs. ')
            .replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, ' ')
            .replace(/[\u2600-\u27BF\uFE0E\uFE0F\u200D]/g, ' ')
            .replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, ' ')
            .replace(/ {2,}/g, ' ')
            .trim();
    }

    function pdfWrapUrl(url) {
        return pdfSafeText(String(url || '').replace(/\//g, '/\u200B'));
    }

    function pdfWrapEmail(email) {
        var str = String(email || '');
        var idx = str.indexOf('@');
        if (idx > 0 && idx < str.length - 1) {
            return pdfSafeText(str.slice(0, idx + 1) + '\u200B' + str.slice(idx + 1));
        }
        return pdfSafeText(str);
    }

    function pdfFormatAmount(symbol, numVal, fallbackStr) {
        var safeSym = pdfSafeText((symbol || 'Rs.').trim());
        if (typeof numVal === 'number' && !isNaN(numVal)) {
            var rounded = Math.round(numVal * 100) / 100;
            var hasCents = Math.abs(rounded - Math.floor(rounded)) >= 0.005;
            var formatted = rounded.toLocaleString('en-US', {
                minimumFractionDigits: hasCents ? 2 : 0,
                maximumFractionDigits: hasCents ? 2 : 0
            });
            return safeSym + ' ' + formatted;
        }
        return fallbackStr ? pdfSafeText(fallbackStr) : '—';
    }

    function pdfStatusColors(statusObj) {
        var cls = (statusObj && statusObj.class) ? String(statusObj.class) : '';
        if (cls === 'status-confirm' || cls === 'status-delivered') {
            return { color: '#065F46', fillColor: '#ECFDF5' };
        }
        if (cls === 'status-pending' || cls === 'status-waiting') {
            return { color: '#92400E', fillColor: '#FFFBEB' };
        }
        if (cls === 'status-reject' || cls === 'status-cancel' || cls === 'status-rto') {
            return { color: '#991B1B', fillColor: '#FEF2F2' };
        }
        return { color: '#334155', fillColor: '#F1F5F9' };
    }

    function resolveActivePdfColumns(meta) {
        var catalog = (meta && Array.isArray(meta.export_columns) && meta.export_columns.length > 0)
            ? meta.export_columns
            : exportColumnCatalog;
        var wantedKeys = (meta && Array.isArray(meta.selected_columns) && meta.selected_columns.length > 0)
            ? meta.selected_columns
            : selectedExportColKeys;
        var keyMap = {};
        wantedKeys.forEach(function (k) {
            var clean = String(k || '').trim().toLowerCase();
            if (clean) {
                keyMap[clean] = true;
            }
        });
        var cols = [];
        catalog.forEach(function (col) {
            if (col && keyMap[col.key]) {
                cols.push(col);
            }
        });
        if (cols.length > 0) {
            return cols;
        }
        return catalog.filter(function (col) {
            return !!(col && col.is_default);
        });
    }

    function pdfGroupFillColor(groupKey) {
        if (groupKey === 'order' || groupKey === 'sender' || groupKey === 'delivery') {
            return '#0F172A';
        }
        if (groupKey === 'product') {
            return '#1E3A8A';
        }
        return '#1E293B';
    }

    function computePdfTableLayout(pdfCols) {
        var count = pdfCols.length;
        var useA4 = count <= 9;
        var pageSize = useA4 ? 'A4' : 'A3';
        var pageRawWidth = useA4 ? 841.89 : 1190.55;
        var horizontalPaddingPerCol = count > 26 ? 3 : 4;
        var availWidth = pageRawWidth - 20 - (count * horizontalPaddingPerCol) - 4;

        var totalWeight = 0;
        pdfCols.forEach(function (col) {
            totalWeight += Number(col.pdf_width || 44);
        });
        if (totalWeight <= 0) {
            totalWeight = count * 44;
        }

        var scale = availWidth / totalWeight;
        var widths = pdfCols.map(function (col) {
            var raw = Number(col.pdf_width || 44) * scale;
            return Math.max(14, Math.floor(raw * 10) / 10);
        });

        var widthSum = widths.reduce(function (acc, w) { return acc + w; }, 0);
        if (widthSum > availWidth && widths.length > 0) {
            var excess = Math.ceil((widthSum - availWidth) * 10) / 10;
            var widestIdx = 0;
            for (var wi = 1; wi < widths.length; wi++) {
                if (widths[wi] > widths[widestIdx]) {
                    widestIdx = wi;
                }
            }
            if (widths[widestIdx] - excess >= 18) {
                widths[widestIdx] = Math.floor((widths[widestIdx] - excess) * 10) / 10;
            }
        }

        var fontProfile;
        if (count <= 10) {
            fontProfile = { body: 7.6, muted: 6.9, link: 6.6, thGroup: 7.5, thSub: 7.2, padX: 2.5, padY: 3.6 };
        } else if (count <= 18) {
            fontProfile = { body: 6.7, muted: 6.1, link: 5.8, thGroup: 6.9, thSub: 6.5, padX: 2.2, padY: 3.3 };
        } else if (count <= 26) {
            fontProfile = { body: 5.8, muted: 5.3, link: 5.0, thGroup: 6.2, thSub: 5.8, padX: 1.8, padY: 2.9 };
        } else {
            fontProfile = { body: 4.8, muted: 4.4, link: 4.2, thGroup: 5.2, thSub: 4.9, padX: 1.3, padY: 2.4 };
        }

        return {
            pageSize: pageSize,
            widths: widths,
            fontProfile: fontProfile
        };
    }

    function buildPdfCellForColumn(row, colDef, fill, isMulti, fontProfile) {
        var key = colDef.key;
        var prod = row.product || {};
        var items = Array.isArray(prod.items) ? prod.items : [];
        var currSym = prod.currency_symbol || '₹';
        var sender = row.sender || {};
        var receiver = row.receiver || {};
        var deliv = row.delivery || {};
        var pay = row.payment || {};

        var cDate = row.created_date_display || '';
        var cTime = row.created_time_display || '';
        if (!cDate && row.created_display) {
            var partsDt = String(row.created_display).split(' ');
            cDate = partsDt[0] || 'N/A';
            cTime = partsDt.slice(1).join(' ');
        }

        switch (key) {
            case 'order_code':
                return { fillColor: fill, text: pdfSafeText(row.order_code || ('OD' + row.order_id)), bold: true, color: '#0F172A' };
            case 'order_date':
                return { fillColor: fill, text: pdfSafeText(cDate || 'N/A') };
            case 'order_time':
                return { fillColor: fill, text: pdfSafeText(cTime || '—'), style: 'tdMuted' };
            case 'order_status': {
                var stColors = pdfStatusColors(row.status);
                return { fillColor: stColors.fillColor, text: pdfSafeText((row.status && row.status.label) || 'Unknown'), bold: true, color: stColors.color, alignment: 'center' };
            }
            case 'product_name': {
                if (items.length === 0) {
                    return { fillColor: fill, text: pdfSafeText(prod.names_text || prod.primary_title || 'N/A') };
                }
                var nameStack = items.map(function (it, itIdx) {
                    return {
                        text: pdfSafeText(it.title || 'Item'),
                        bold: true,
                        margin: itIdx > 0 ? [0, 2, 0, 0] : [0, 0, 0, 0]
                    };
                });
                return { fillColor: fill, stack: nameStack };
            }
            case 'product_link': {
                if (items.length === 0) {
                    return { fillColor: fill, text: '—', style: 'tdMuted' };
                }
                var linkStack = [];
                items.forEach(function (it, itIdx) {
                    var topGap = itIdx > 0 ? [0, 2, 0, 0] : [0, 0, 0, 0];
                    if (it.website_url && isSafeHttpUrl(it.website_url)) {
                        linkStack.push({
                            text: pdfWrapUrl(it.website_url),
                            link: it.website_url,
                            color: '#1D4ED8',
                            decoration: 'underline',
                            fontSize: fontProfile.link,
                            margin: topGap
                        });
                    } else if (isMulti || itIdx === 0) {
                        linkStack.push({ text: '—', style: 'tdMuted', margin: topGap });
                    }
                });
                return { fillColor: fill, stack: linkStack.length ? linkStack : [{ text: '—', style: 'tdMuted' }] };
            }
            case 'product_sku': {
                if (items.length === 0) {
                    return { fillColor: fill, text: '—', style: 'tdMuted' };
                }
                return {
                    fillColor: fill,
                    stack: items.map(function (it, itIdx) {
                        return {
                            text: pdfSafeText(it.sku || '—'),
                            style: it.sku ? undefined : 'tdMuted',
                            margin: itIdx > 0 ? [0, 2, 0, 0] : [0, 0, 0, 0]
                        };
                    })
                };
            }
            case 'item_qty': {
                if (items.length === 0) {
                    return { fillColor: fill, text: String(prod.total_qty || 1), alignment: 'right' };
                }
                return {
                    fillColor: fill,
                    stack: items.map(function (it, itIdx) {
                        return {
                            text: String(it.qty || 1),
                            alignment: 'right',
                            margin: itIdx > 0 ? [0, 2, 0, 0] : [0, 0, 0, 0]
                        };
                    })
                };
            }
            case 'total_qty':
                return { fillColor: fill, text: String(prod.total_qty || 1), alignment: 'right' };
            case 'unit_price': {
                if (items.length === 0) {
                    return { fillColor: fill, text: '—', alignment: 'right', style: 'tdMuted' };
                }
                return {
                    fillColor: fill,
                    stack: items.map(function (it, itIdx) {
                        return {
                            text: pdfFormatAmount(currSym, it.unit_price_num, it.unit_price),
                            alignment: 'right',
                            margin: itIdx > 0 ? [0, 2, 0, 0] : [0, 0, 0, 0]
                        };
                    })
                };
            }
            case 'product_subtotal':
                return { fillColor: fill, text: pdfFormatAmount(currSym, prod.subtotal_num, prod.subtotal), alignment: 'right' };
            case 'delivery_charges':
                return { fillColor: fill, text: pdfFormatAmount(currSym, prod.delivery_charges_num, prod.delivery_charges), alignment: 'right' };
            case 'coupon_code':
                return { fillColor: fill, text: pdfSafeText(prod.coupon || '—'), style: prod.coupon ? undefined : 'tdMuted' };
            case 'order_price':
                return {
                    fillColor: '#EEF2FF',
                    text: pdfFormatAmount(currSym, prod.total_amount_num, prod.amount_display || prod.total_amount),
                    bold: true,
                    color: '#0F172A',
                    alignment: 'right'
                };
            case 'currency':
                return { fillColor: fill, text: pdfSafeText(prod.currency_code || 'INR'), alignment: 'center' };
            case 'payment_mode':
                return { fillColor: fill, text: pdfSafeText(pay.mode_label || 'Unspecified') };
            case 'payment_status':
                return { fillColor: fill, text: pdfSafeText(pay.transaction_status_label || '—'), style: pay.transaction_status_label ? undefined : 'tdMuted' };
            case 'transaction_ref':
                return { fillColor: fill, text: pdfSafeText(pay.transaction_ref || '—'), style: pay.transaction_ref ? undefined : 'tdMuted' };
            case 'sender_name':
                return { fillColor: fill, text: pdfSafeText(sender.name || 'N/A') };
            case 'sender_mobile':
                return { fillColor: fill, text: pdfSafeText(sender.mobile || '—'), style: sender.mobile ? undefined : 'tdMuted' };
            case 'sender_alt_mobile':
                return { fillColor: fill, text: pdfSafeText(sender.alt_mobile || '—'), style: sender.alt_mobile ? undefined : 'tdMuted' };
            case 'sender_email':
                return { fillColor: fill, text: sender.email ? pdfWrapEmail(sender.email) : '—', style: sender.email ? undefined : 'tdMuted' };
            case 'receiver_name':
                return { fillColor: fill, text: pdfSafeText(receiver.name || 'N/A') };
            case 'receiver_mobile':
                return { fillColor: fill, text: pdfSafeText(receiver.mobile || '—'), style: receiver.mobile ? undefined : 'tdMuted' };
            case 'receiver_alt_mobile':
                return { fillColor: fill, text: pdfSafeText(receiver.alt_mobile || '—'), style: receiver.alt_mobile ? undefined : 'tdMuted' };
            case 'receiver_street_address':
                return { fillColor: fill, text: pdfSafeText(receiver.street_address || receiver.full_address || '—') };
            case 'receiver_city':
                return { fillColor: fill, text: pdfSafeText(receiver.city || '—'), style: receiver.city ? undefined : 'tdMuted' };
            case 'receiver_state':
                return { fillColor: fill, text: pdfSafeText(receiver.state || '—'), style: receiver.state ? undefined : 'tdMuted' };
            case 'receiver_country':
                return { fillColor: fill, text: pdfSafeText(receiver.country || '—'), style: receiver.country ? undefined : 'tdMuted' };
            case 'receiver_pin_code':
                return { fillColor: fill, text: pdfSafeText(receiver.pin_code || '—'), style: receiver.pin_code ? undefined : 'tdMuted' };
            case 'address_type':
                return { fillColor: fill, text: pdfSafeText(receiver.address_type || '—'), style: receiver.address_type ? undefined : 'tdMuted' };
            case 'delivery_date':
                return { fillColor: fill, text: pdfSafeText(deliv.date_display || 'Not scheduled') };
            case 'delivery_slot':
                return { fillColor: fill, text: pdfSafeText(deliv.slot || '—'), style: deliv.slot ? undefined : 'tdMuted' };
            case 'delivery_time':
                return { fillColor: fill, text: pdfSafeText(deliv.time || '—'), style: deliv.time ? undefined : 'tdMuted' };
            case 'occasion':
                return { fillColor: fill, text: pdfSafeText(deliv.occasion || '—'), style: deliv.occasion ? undefined : 'tdMuted' };
            case 'gift_message':
                return { fillColor: fill, text: pdfSafeText(deliv.message || '—'), style: deliv.message ? undefined : 'tdMuted' };
            case 'delivery_note':
                return { fillColor: fill, text: pdfSafeText(deliv.comment || '—'), style: deliv.comment ? undefined : 'tdMuted' };
            case 'return_reason':
                return { fillColor: fill, text: pdfSafeText(deliv.return_reason || '—'), style: deliv.return_reason ? undefined : 'tdMuted' };
            case 'return_comment':
                return { fillColor: fill, text: pdfSafeText(deliv.return_comment || '—'), style: deliv.return_comment ? undefined : 'tdMuted' };
            case 'delivery_photo_status':
                return { fillColor: fill, text: pdfSafeText(deliv.photo_status || 'Not Uploaded') };
            case 'delivery_photo_link':
                if (deliv.photo_url && isSafeHttpUrl(deliv.photo_url)) {
                    return {
                        fillColor: fill,
                        text: pdfWrapUrl(deliv.photo_url),
                        link: deliv.photo_url,
                        color: '#1D4ED8',
                        decoration: 'underline',
                        fontSize: fontProfile.link
                    };
                }
                return { fillColor: fill, text: '—', style: 'tdMuted' };
            default:
                return { fillColor: fill, text: '—', style: 'tdMuted' };
        }
    }

    async function handlePdfExport() {
        if (els.exportPdfBtn && els.exportPdfBtn.getAttribute('aria-disabled') === 'true') {
            return;
        }
        setButtonBusy(els.exportPdfBtn, true, 'Generating PDF...');
        announceStatus('Loading PDF generator and preparing filtered orders...', 'busy');

        try {
            var libsAndData = await Promise.all([
                ensurePdfMakeReady(),
                fetch(buildExportUrl('json'), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                })
            ]);

            var pdfMakeLib = libsAndData[0];
            var res = libsAndData[1];

            if (res.redirected) {
                throw new Error('Session expired. Please sign in again before exporting.');
            }
            var contentType = (res.headers.get('content-type') || '').toLowerCase();
            if (contentType.indexOf('application/json') === -1) {
                throw new Error('Unexpected response from export endpoint (' + res.status + ').');
            }
            var payload = await res.json();
            if (!res.ok || payload.status !== 'success') {
                throw new Error(payload.message || 'Unable to fetch orders for PDF export.');
            }

            var rows = Array.isArray(payload.rows) ? payload.rows : [];
            var meta = payload.meta || {};
            var summary = meta.summary || {};
            var statusCounts = summary.status_counts || {};
            var todaySlug = meta.date_slug || getTodayIso();
            var filename = 'crm-orders-' + todaySlug + '.pdf';

            var totalRevenueVal = typeof summary.filtered_revenue === 'number'
                ? summary.filtered_revenue
                : rows.reduce(function (acc, r) {
                    var n = r.product && typeof r.product.total_amount_num === 'number' ? r.product.total_amount_num : 0;
                    return acc + n;
                }, 0);
            var summarySym = summary.currency_symbol || '₹';
            var deliveredCnt = parseInt(statusCounts['6'] || 0, 10) || 0;
            var confirmedCnt = parseInt(statusCounts['1'] || 0, 10) || 0;
            var pendingWaitCnt = (parseInt(statusCounts['0'] || 0, 10) || 0) + (parseInt(statusCounts['4'] || 0, 10) || 0);

            var pdfCols = resolveActivePdfColumns(meta);
            var colCount = pdfCols.length;
            var layoutCfg = computePdfTableLayout(pdfCols);
            var fp = layoutCfg.fontProfile;

            // Contiguous 2-tier grouped header for selected columns
            var headerGroups = [];
            pdfCols.forEach(function (col) {
                var gKey = col.group || 'order';
                var gLabel = col.group_label || 'Orders';
                var last = headerGroups[headerGroups.length - 1];
                if (last && last.group === gKey) {
                    last.span++;
                } else {
                    headerGroups.push({ group: gKey, label: gLabel, span: 1 });
                }
            });

            var groupRow = [];
            headerGroups.forEach(function (grp) {
                var cell = {
                    text: pdfSafeText(grp.label.toUpperCase()),
                    style: 'thGroup',
                    fillColor: pdfGroupFillColor(grp.group)
                };
                if (grp.span > 1) {
                    cell.colSpan = grp.span;
                }
                groupRow.push(cell);
                for (var s = 1; s < grp.span; s++) {
                    groupRow.push({});
                }
            });

            var subHeaderRow = pdfCols.map(function (col) {
                var h = { text: pdfSafeText(col.label), style: 'thSub' };
                if (col.align === 'right' || col.align === 'center') {
                    h.alignment = col.align;
                }
                return h;
            });

            var tableBody = [groupRow, subHeaderRow];

            if (rows.length === 0) {
                var emptyCell = {
                    text: 'No orders matched the active filter criteria.',
                    alignment: 'center',
                    margin: [0, 14, 0, 14],
                    style: 'tdMuted'
                };
                if (colCount > 1) {
                    emptyCell.colSpan = colCount;
                }
                var emptyRow = [emptyCell];
                for (var eIdx = 1; eIdx < colCount; eIdx++) {
                    emptyRow.push({});
                }
                tableBody.push(emptyRow);
            } else {
                rows.forEach(function (row, idx) {
                    var fill = (idx % 2 === 1) ? '#F8FAFC' : '#FFFFFF';
                    var prod = row.product || {};
                    var items = Array.isArray(prod.items) ? prod.items : [];
                    var isMulti = items.length > 1;
                    var pdfRow = pdfCols.map(function (col) {
                        return buildPdfCellForColumn(row, col, fill, isMulti, fp);
                    });
                    tableBody.push(pdfRow);
                });
            }

            var docDefinition = {
                pageSize: layoutCfg.pageSize,
                pageOrientation: 'landscape',
                pageMargins: [10, 18, 10, 22],
                info: {
                    title: pdfSafeText((meta.site_name || 'CRM') + ' - Orders Report (' + todaySlug + ')'),
                    subject: 'Filtered CRM Orders Detailed Export'
                },
                header: function () {
                    return null;
                },
                footer: function (currentPage, pageCount) {
                    return {
                        columns: [
                            { text: pdfSafeText((meta.site_name || 'CRM Orders') + ' — ' + colCount + ' column(s) — Generated ' + (meta.exported_at || todaySlug)), style: 'footerText', alignment: 'left' },
                            { text: 'Page ' + currentPage + ' of ' + pageCount, style: 'footerText', alignment: 'right' }
                        ],
                        margin: [10, 4, 10, 0]
                    };
                },
                content: [
                    {
                        columns: [
                            {
                                stack: [
                                    { text: pdfSafeText((meta.site_name || 'CRM') + ' — Orders Export Report'), style: 'title' },
                                    { text: pdfSafeText((meta.filter_summary || 'All Orders') + ' · ' + colCount + ' selected column(s)'), style: 'subtitle' }
                                ]
                            },
                            {
                                width: 200,
                                alignment: 'right',
                                stack: [
                                    { text: 'Total Matching Orders: ' + String(meta.total || rows.length), bold: true, fontSize: 9, color: '#0F172A' },
                                    { text: pdfSafeText('Exported: ' + (meta.exported_at || todaySlug)), style: 'subtitle' }
                                ]
                            }
                        ],
                        margin: [0, 0, 0, 8]
                    },
                    {
                        table: {
                            widths: ['*', '*', '*', '*'],
                            body: [[
                                {
                                    fillColor: '#F8FAFC',
                                    stack: [
                                        { text: 'TOTAL ORDERS', style: 'kpiLabel' },
                                        { text: String(rows.length), style: 'kpiValue' }
                                    ]
                                },
                                {
                                    fillColor: '#EEF2FF',
                                    stack: [
                                        { text: 'TOTAL ORDER VALUE', style: 'kpiLabel' },
                                        { text: pdfFormatAmount(summarySym, totalRevenueVal, 'Rs. 0'), style: 'kpiValue' }
                                    ]
                                },
                                {
                                    fillColor: '#F8FAFC',
                                    stack: [
                                        { text: 'DELIVERED / CONFIRMED', style: 'kpiLabel' },
                                        { text: String(deliveredCnt) + ' / ' + String(confirmedCnt), style: 'kpiValue' }
                                    ]
                                },
                                {
                                    fillColor: '#F8FAFC',
                                    stack: [
                                        { text: 'PENDING / WAITING', style: 'kpiLabel' },
                                        { text: String(pendingWaitCnt), style: 'kpiValue' }
                                    ]
                                }
                            ]]
                        },
                        layout: {
                            hLineWidth: function () { return 0.6; },
                            vLineWidth: function () { return 0.6; },
                            hLineColor: function () { return '#CBD5E1'; },
                            vLineColor: function () { return '#CBD5E1'; },
                            paddingLeft: function () { return 7; },
                            paddingRight: function () { return 7; },
                            paddingTop: function () { return 4.5; },
                            paddingBottom: function () { return 4.5; }
                        },
                        margin: [0, 0, 0, 8]
                    },
                    {
                        table: {
                            headerRows: 2,
                            dontBreakRows: true,
                            widths: layoutCfg.widths,
                            body: tableBody
                        },
                        layout: {
                            hLineWidth: function () { return 0.4; },
                            vLineWidth: function () { return 0.4; },
                            hLineColor: function () { return '#CBD5E1'; },
                            vLineColor: function () { return '#E2E8F0'; },
                            paddingLeft: function () { return fp.padX; },
                            paddingRight: function () { return fp.padX; },
                            paddingTop: function () { return fp.padY; },
                            paddingBottom: function () { return fp.padY; }
                        }
                    }
                ],
                defaultStyle: {
                    fontSize: fp.body,
                    lineHeight: 1.22,
                    color: '#0F172A'
                },
                styles: {
                    title: { fontSize: 13, bold: true, color: '#0F172A' },
                    subtitle: { fontSize: 7.5, color: '#475569', margin: [0, 2, 0, 0] },
                    kpiLabel: { fontSize: 6, bold: true, color: '#475569', letterSpacing: 0.4 },
                    kpiValue: { fontSize: 9.5, bold: true, color: '#0F172A', margin: [0, 1.5, 0, 0] },
                    thGroup: { fontSize: fp.thGroup, bold: true, color: '#FFFFFF', alignment: 'center', margin: [0, 1.5, 0, 1.5] },
                    thSub: { fontSize: fp.thSub, bold: true, color: '#1E293B', fillColor: '#F1F5F9', margin: [0, 1.5, 0, 1.5] },
                    tdMuted: { fontSize: fp.muted, color: '#475569' },
                    footerText: { fontSize: 7, color: '#64748B' }
                }
            };

            pdfMakeLib.createPdf(docDefinition).download(filename);
            announceStatus('PDF report generated and downloaded (' + rows.length + ' order(s), ' + colCount + ' column(s)).', 'success');
        } catch (err) {
            announceStatus((err && err.message) ? err.message : 'Unable to generate PDF export.', 'error');
        } finally {
            setButtonBusy(els.exportPdfBtn, false);
        }
    }

    function handlePrintExport() {
        var url = buildExportUrl('print');
        var win = window.open(url, '_blank', 'noopener');
        if (!win) {
            announceStatus('Pop-up blocked by browser. Allow pop-ups for this site to open Print View.', 'error');
        } else {
            announceStatus('Opened printable order report in a new tab.', 'success');
        }
    }

    function installToolbarKeyboardFallback() {
        if (!els.exportToolbar || ('focusGroup' in HTMLElement.prototype)) {
            return;
        }
        var items = Array.prototype.slice.call(els.exportToolbar.querySelectorAll('button, a'));
        if (items.length === 0) {
            return;
        }
        items.forEach(function (item, idx) {
            item.tabIndex = idx === 0 ? 0 : -1;
        });

        els.exportToolbar.addEventListener('focusin', function (event) {
            var target = event.target;
            if (items.indexOf(target) !== -1) {
                items.forEach(function (item) {
                    item.tabIndex = (item === target) ? 0 : -1;
                });
            }
        });

        els.exportToolbar.addEventListener('keydown', function (event) {
            var currentIndex = items.indexOf(document.activeElement);
            if (currentIndex === -1) {
                return;
            }
            var isVertical = els.exportToolbar.getAttribute('aria-orientation') === 'vertical';
            var isRtl = window.getComputedStyle(els.exportToolbar).direction === 'rtl';
            var delta = null;

            if (isVertical) {
                if (event.key === 'ArrowDown') delta = 1;
                if (event.key === 'ArrowUp') delta = -1;
            } else {
                if (event.key === 'ArrowRight') delta = isRtl ? -1 : 1;
                if (event.key === 'ArrowLeft') delta = isRtl ? 1 : -1;
            }

            var nextIndex = null;
            if (delta !== null) {
                nextIndex = (currentIndex + delta + items.length) % items.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = items.length - 1;
            }

            if (nextIndex !== null) {
                event.preventDefault();
                items[nextIndex].tabIndex = 0;
                items[nextIndex].focus();
            }
        });
    }

    function bindEvents() {
        if (els.filterToggleBtn && els.filterDrawer) {
            els.filterToggleBtn.addEventListener('click', function () {
                var isCurrentlyHidden = !!els.filterDrawer.hidden;
                if (isCurrentlyHidden) {
                    setFilterDrawerOpen(true);
                } else {
                    setFilterDrawerOpen(false, { discardUnapplied: true });
                }
            });
        }

        if (els.filterCloseBtn) {
            els.filterCloseBtn.addEventListener('click', function () {
                setFilterDrawerOpen(false, { discardUnapplied: true, restoreFocus: true });
            });
        }

        if (els.filterCancelBtn) {
            els.filterCancelBtn.addEventListener('click', function () {
                setFilterDrawerOpen(false, { discardUnapplied: true, restoreFocus: true });
            });
        }

        if (els.filterDrawer) {
            els.filterDrawer.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    if (dpState.isOpen) {
                        event.stopPropagation();
                        closeDatepicker({ restoreFocus: true });
                        return;
                    }
                    setFilterDrawerOpen(false, { discardUnapplied: true, restoreFocus: true });
                }
            });
        }

        if (els.searchInput) {
            els.searchInput.addEventListener('compositionstart', function () {
                isComposingSearch = true;
            });
            els.searchInput.addEventListener('compositionend', function () {
                isComposingSearch = false;
            });
            els.searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && (event.isComposing || isComposingSearch || event.keyCode === 229)) {
                    event.preventDefault();
                }
            });
        }

        if (els.filterForm) {
            els.filterForm.addEventListener('submit', function (event) {
                event.preventDefault();
                closeDatepicker({ restoreFocus: false });

                var startOk = applyFieldValidation(els.startDateInput, { afterInteraction: true, checkRangeOrder: false });
                var endOk = applyFieldValidation(els.endDateInput, { afterInteraction: true, checkRangeOrder: true });
                if (!startOk || !endOk) {
                    var firstInvalid = !startOk ? els.startDateInput : els.endDateInput;
                    if (firstInvalid && typeof firstInvalid.focus === 'function') {
                        firstInvalid.focus();
                    }
                    return;
                }

                readStateFromControls();
                state.page = 1;
                syncControlsFromState();
                updateActiveFiltersUI();
                setFilterDrawerOpen(false, { restoreFocus: true });
                loadOrders(true);
            });
        }

        [els.startDateTrigger, els.endDateTrigger].forEach(function (trigger) {
            if (!trigger) {
                return;
            }
            trigger.addEventListener('click', function () {
                var key = trigger.getAttribute('data-date-target') === 'end' ? 'end' : 'start';
                if (dpState.isOpen && dpState.targetKey === key) {
                    closeDatepicker({ restoreFocus: true, focusTarget: 'trigger' });
                } else {
                    openDatepicker(key, { openedVia: 'trigger', focusPopover: true });
                }
            });
        });

        [els.startDateInput, els.endDateInput].forEach(function (inp) {
            if (!inp) {
                return;
            }
            var key = inp.getAttribute('data-date-role') === 'end' ? 'end' : 'start';

            inp.addEventListener('click', function () {
                openDatepicker(key, { openedVia: 'input', focusPopover: false });
            });

            inp.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown' && !event.altKey) {
                    event.preventDefault();
                    openDatepicker(key, { openedVia: 'trigger', focusPopover: true });
                }
            });

            inp.addEventListener('input', function () {
                dirtyDateInputs.add(inp);
                clearFieldValidation(inp);
                if (els.allDatesInput) {
                    els.allDatesInput.value = '';
                }
                syncPresetChipsFromInputs();
                if (dpState.isOpen && dpState.targetKey === key) {
                    var iso = parseFlexibleDateToIso(inp.value);
                    var parts = (iso && iso <= getTodayIso()) ? parseIsoParts(iso) : null;
                    if (parts) {
                        dpState.viewYear = parts.y;
                        dpState.viewMonth = parts.m;
                        renderDatepickerCalendar();
                    }
                }
            });

            inp.addEventListener('blur', function (event) {
                if (pointerDownInDatepicker) {
                    return;
                }
                var nextFocused = event.relatedTarget;
                if (nextFocused && (
                    (els.dpPopover && els.dpPopover.contains(nextFocused)) ||
                    (els.startDateTrigger && nextFocused === els.startDateTrigger) ||
                    (els.endDateTrigger && nextFocused === els.endDateTrigger)
                )) {
                    return;
                }
                if (dirtyDateInputs.has(inp) || (inp.value || '').trim() !== '') {
                    applyFieldValidation(inp, {
                        afterInteraction: true,
                        checkRangeOrder: key === 'end',
                        announce: true
                    });
                    if (key === 'start' && els.endDateInput && (els.endDateInput.value || '').trim() !== '' && dirtyDateInputs.has(els.endDateInput)) {
                        applyFieldValidation(els.endDateInput, {
                            afterInteraction: true,
                            checkRangeOrder: true,
                            announce: false
                        });
                    }
                }
            });
        });

        if (els.dpCloseBtn) {
            els.dpCloseBtn.addEventListener('click', function () {
                closeDatepicker({ restoreFocus: true });
            });
        }

        if (els.dpPrevBtn) {
            els.dpPrevBtn.addEventListener('click', function () {
                if (dpState.viewMonth === 1) {
                    dpState.viewYear -= 1;
                    dpState.viewMonth = 12;
                } else {
                    dpState.viewMonth -= 1;
                }
                renderDatepickerCalendar();
            });
        }

        if (els.dpNextBtn) {
            els.dpNextBtn.addEventListener('click', function () {
                var todayParts = parseIsoParts(getTodayIso());
                if (dpState.viewYear > todayParts.y || (dpState.viewYear === todayParts.y && dpState.viewMonth >= todayParts.m)) {
                    return;
                }
                if (dpState.viewMonth === 12) {
                    dpState.viewYear += 1;
                    dpState.viewMonth = 1;
                } else {
                    dpState.viewMonth += 1;
                }
                renderDatepickerCalendar();
            });
        }

        if (els.dpMonthSelect) {
            els.dpMonthSelect.addEventListener('change', function () {
                var todayParts = parseIsoParts(getTodayIso());
                var mVal = parseInt(els.dpMonthSelect.value || '1', 10);
                if (dpState.viewYear === todayParts.y && mVal > todayParts.m) {
                    mVal = todayParts.m;
                }
                dpState.viewMonth = mVal;
                renderDatepickerCalendar();
            });
        }

        if (els.dpYearSelect) {
            els.dpYearSelect.addEventListener('change', function () {
                var todayParts = parseIsoParts(getTodayIso());
                var yVal = parseInt(els.dpYearSelect.value || String(todayParts.y), 10);
                if (yVal > todayParts.y) {
                    yVal = todayParts.y;
                }
                dpState.viewYear = yVal;
                if (dpState.viewYear === todayParts.y && dpState.viewMonth > todayParts.m) {
                    dpState.viewMonth = todayParts.m;
                }
                renderDatepickerCalendar();
            });
        }

        if (els.dpDays) {
            els.dpDays.addEventListener('click', function (event) {
                var dayBtn = event.target.closest ? event.target.closest('.crm-dp-day') : null;
                if (!dayBtn || dayBtn.disabled) {
                    return;
                }
                var iso = dayBtn.getAttribute('data-iso') || '';
                if (!iso || iso > getTodayIso()) {
                    return;
                }
                selectDateFromPicker(iso, dpState.openedVia === 'input' ? 'input' : 'trigger');
            });

            els.dpDays.addEventListener('keydown', function (event) {
                var activeBtn = document.activeElement;
                if (!activeBtn || !activeBtn.classList || !activeBtn.classList.contains('crm-dp-day')) {
                    return;
                }
                var currentIso = activeBtn.getAttribute('data-iso');
                if (!currentIso) {
                    return;
                }
                var delta = null;
                if (event.key === 'ArrowLeft') delta = -1;
                else if (event.key === 'ArrowRight') delta = 1;
                else if (event.key === 'ArrowUp') delta = -7;
                else if (event.key === 'ArrowDown') delta = 7;

                if (delta === null) {
                    return;
                }
                event.preventDefault();
                var todayIso = getTodayIso();
                var nextIso = addDaysIso(currentIso, delta);
                if (nextIso > todayIso) {
                    nextIso = todayIso;
                }
                var targetBtn = els.dpDays.querySelector('.crm-dp-day[data-iso="' + nextIso + '"]:not(:disabled)');
                if (targetBtn && !targetBtn.classList.contains('is-outside')) {
                    targetBtn.focus();
                    return;
                }
                var parts = parseIsoParts(nextIso);
                if (parts) {
                    dpState.viewYear = parts.y;
                    dpState.viewMonth = parts.m;
                    renderDatepickerCalendar(parts.iso);
                }
            });
        }

        if (els.dpTodayBtn) {
            els.dpTodayBtn.addEventListener('click', function () {
                selectDateFromPicker(getTodayIso(), dpState.openedVia === 'input' ? 'input' : 'trigger');
            });
        }

        if (els.dpClearBtn) {
            els.dpClearBtn.addEventListener('click', function () {
                var targetField = getDateFieldElements(dpState.targetKey);
                if (targetField.input) {
                    targetField.input.value = '';
                    clearFieldValidation(targetField.input);
                    dirtyDateInputs.delete(targetField.input);
                }
                var bothEmpty = (!els.startDateInput || !els.startDateInput.value.trim()) && (!els.endDateInput || !els.endDateInput.value.trim());
                if (els.allDatesInput) {
                    els.allDatesInput.value = bothEmpty ? '1' : '';
                }
                syncPresetChipsFromInputs();
                closeDatepicker({ restoreFocus: true, focusTarget: dpState.openedVia === 'input' ? 'input' : 'trigger' });
            });
        }

        if (els.dpPopover) {
            els.dpPopover.addEventListener('pointerdown', function () {
                pointerDownInDatepicker = true;
                window.setTimeout(function () {
                    pointerDownInDatepicker = false;
                }, 0);
            });
            els.dpPopover.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    event.stopPropagation();
                    closeDatepicker({ restoreFocus: true });
                }
            });
        }

        document.addEventListener('pointerdown', function (event) {
            if (!dpState.isOpen || !els.dpPopover) {
                return;
            }
            var t = event.target;
            var insidePopover = els.dpPopover.contains(t);
            var startWrap = els.startDateInput ? els.startDateInput.closest('.crm-date-input-wrap') : null;
            var endWrap = els.endDateInput ? els.endDateInput.closest('.crm-date-input-wrap') : null;
            if (!insidePopover && (!startWrap || !startWrap.contains(t)) && (!endWrap || !endWrap.contains(t))) {
                closeDatepicker({ restoreFocus: false });
            }
        });

        window.addEventListener('resize', function () {
            if (dpState.isOpen) {
                positionDatepicker();
            }
        });

        window.addEventListener('scroll', function () {
            if (dpState.isOpen) {
                positionDatepicker();
            }
        }, true);

        els.presetChips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                var presetName = chip.getAttribute('data-preset') || 'last30';
                var range = getPresetDates(presetName);
                closeDatepicker({ restoreFocus: false });
                if (els.allDatesInput) {
                    els.allDatesInput.value = range.allDates ? '1' : '';
                }
                if (els.startDateInput) {
                    els.startDateInput.value = range.start ? formatDisplayDate(range.start) : '';
                    clearFieldValidation(els.startDateInput);
                    dirtyDateInputs.delete(els.startDateInput);
                }
                if (els.endDateInput) {
                    els.endDateInput.value = range.end ? formatDisplayDate(range.end) : '';
                    clearFieldValidation(els.endDateInput);
                    dirtyDateInputs.delete(els.endDateInput);
                }
                syncDatePresetChipsForValues(range.start, range.end, !!range.allDates);
            });
        });

        els.statusPills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                state.status = pill.getAttribute('data-status') || 'all';
                state.page = 1;
                syncControlsFromState();
                updateActiveFiltersUI();
                loadOrders(true);
            });
        });

        if (els.chipsClearAllBtn) {
            els.chipsClearAllBtn.addEventListener('click', clearAllFilters);
        }

        [els.resetBtn, els.quickResetBtn].forEach(function (btn) {
            if (btn) {
                btn.addEventListener('click', resetAllFilters);
            }
        });

        if (els.exportColsBtn && els.exportColsDialog) {
            var colsPreparedThisTick = false;
            var prepDialogOnce = function () {
                if (colsPreparedThisTick) {
                    return;
                }
                colsPreparedThisTick = true;
                prepareExportColumnsDialog();
                window.setTimeout(function () {
                    colsPreparedThisTick = false;
                }, 0);
            };

            els.exportColsBtn.addEventListener('click', function () {
                prepDialogOnce();
                if (!('commandForElement' in HTMLButtonElement.prototype) && !els.exportColsDialog.open) {
                    els.exportColsDialog.showModal();
                }
            });

            els.exportColsDialog.addEventListener('command', function (event) {
                if (event.command === 'show-modal' && !els.exportColsDialog.open) {
                    prepDialogOnce();
                }
            });
        }

        [els.exportColsCloseBtn, els.exportColsCancelBtn].forEach(function (closeBtn) {
            if (!closeBtn) {
                return;
            }
            closeBtn.addEventListener('click', function () {
                if (!('commandForElement' in HTMLButtonElement.prototype) && els.exportColsDialog && els.exportColsDialog.open) {
                    els.exportColsDialog.close('cancel');
                }
            });
        });

        if (els.exportColsFilterInput) {
            els.exportColsFilterInput.addEventListener('input', function () {
                applyExportColumnsSearchFilter();
            });
            els.exportColsFilterInput.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && els.exportColsFilterInput.value !== '' && !event.isComposing) {
                    event.preventDefault();
                    event.stopPropagation();
                    els.exportColsFilterInput.value = '';
                    applyExportColumnsSearchFilter();
                }
            });
        }

        if (els.exportColsDefaultBtn) {
            els.exportColsDefaultBtn.addEventListener('click', function () {
                draftExportColSet = new Set(getDefaultExportColumnKeys(exportColumnCatalog));
                clearExportColumnsError();
                syncExportColumnsDialogSummaryAndCounts();
            });
        }

        if (els.exportColsAllBtn) {
            els.exportColsAllBtn.addEventListener('click', function () {
                exportColumnCatalog.forEach(function (col) {
                    if (col && col.key) {
                        draftExportColSet.add(col.key);
                    }
                });
                clearExportColumnsError();
                syncExportColumnsDialogSummaryAndCounts();
            });
        }

        if (els.exportColsClearBtn) {
            els.exportColsClearBtn.addEventListener('click', function () {
                draftExportColSet.clear();
                clearExportColumnsError();
                syncExportColumnsDialogSummaryAndCounts();
            });
        }

        if (els.exportColsForm) {
            els.exportColsForm.addEventListener('submit', function (event) {
                var submitter = event.submitter || null;
                if (submitter && submitter.value === 'cancel') {
                    clearExportColumnsError();
                    return;
                }

                if (draftExportColSet.size === 0) {
                    event.preventDefault();
                    if (els.exportColsFilterInput && els.exportColsFilterInput.value !== '') {
                        els.exportColsFilterInput.value = '';
                        applyExportColumnsSearchFilter();
                    }
                    showExportColumnsError('Select at least one column to export.');
                    if (els.exportColsGroups) {
                        var firstCheckbox = els.exportColsGroups.querySelector('.crm-col-checkbox');
                        if (firstCheckbox) {
                            firstCheckbox.setAttribute('aria-describedby', 'crmExportColsError');
                            if (typeof firstCheckbox.focus === 'function') {
                                firstCheckbox.focus();
                            }
                        }
                    }
                    return;
                }

                clearExportColumnsError();
                selectedExportColKeys = normalizeSelectedExportKeys(Array.from(draftExportColSet), exportColumnCatalog);
                persistExportColKeys(selectedExportColKeys);
                updateExportColumnsButtonUI();
                announceStatus(
                    'Export columns updated (' + selectedExportColKeys.length + ' column' + (selectedExportColKeys.length === 1 ? '' : 's') + ' selected).',
                    'success'
                );

                if (els.exportColsDialog && els.exportColsDialog.open && els.exportColsForm.getAttribute('method') !== 'dialog') {
                    els.exportColsDialog.close('save');
                }
            });
        }

        if (els.exportColsDialog) {
            els.exportColsDialog.addEventListener('close', function () {
                clearExportColumnsError();
                els.exportColsDialog.returnValue = '';
            });

            if (!('closedBy' in HTMLDialogElement.prototype)) {
                var pressedOutsideCols = false;
                var isOutsideDialogBox = function (ev, dlg) {
                    var r = dlg.getBoundingClientRect();
                    return ev.clientX < r.left || ev.clientX > r.right || ev.clientY < r.top || ev.clientY > r.bottom;
                };
                els.exportColsDialog.addEventListener('pointerdown', function (ev) {
                    if (ev.pointerType === 'mouse' && ev.button !== 0) {
                        pressedOutsideCols = false;
                        return;
                    }
                    pressedOutsideCols = isOutsideDialogBox(ev, els.exportColsDialog);
                });
                els.exportColsDialog.addEventListener('pointerup', function (ev) {
                    if (pressedOutsideCols && isOutsideDialogBox(ev, els.exportColsDialog) && els.exportColsDialog.open) {
                        els.exportColsDialog.close('dismiss');
                    }
                    pressedOutsideCols = false;
                });
            }
        }

        if (els.exportCsvBtn) {
            els.exportCsvBtn.addEventListener('click', function () {
                handleFileDownloadExport('csv', 'csv', els.exportCsvBtn);
            });
        }
        if (els.exportXlsxBtn) {
            els.exportXlsxBtn.addEventListener('click', function () {
                handleFileDownloadExport('xlsx', 'xlsx', els.exportXlsxBtn);
            });
        }
        if (els.exportPdfBtn) {
            els.exportPdfBtn.addEventListener('click', handlePdfExport);
        }
        if (els.exportPrintBtn) {
            els.exportPrintBtn.addEventListener('click', handlePrintExport);
        }

        window.addEventListener('popstate', function () {
            readStateFromLocation();
            syncControlsFromState();
            updateActiveFiltersUI();
            loadOrders(false);
        });
    }

    // Boot (filter drawer intentionally stays collapsed until requested)
    initExportColumns();
    readStateFromLocation();
    syncControlsFromState();
    updateActiveFiltersUI();
    installToolbarKeyboardFallback();
    bindEvents();
    loadOrders(false);
})();
