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
        viewMode: root.getAttribute('data-view') || 'desktop'
    };

    var els = {
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
        statusSelect: document.getElementById('crmFilterStatus'),
        dateBasisSelect: document.getElementById('crmFilterDateBasis'),
        startDateInput: document.getElementById('crmFilterStartDate'),
        endDateInput: document.getElementById('crmFilterEndDate'),
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

    function formatIsoDate(d) {
        var yr = d.getFullYear();
        var mo = String(d.getMonth() + 1).padStart(2, '0');
        var da = String(d.getDate()).padStart(2, '0');
        return yr + '-' + mo + '-' + da;
    }

    function formatDisplayDate(iso) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(iso || ''));
        if (!m) {
            return iso || '';
        }
        return m[3] + '/' + m[2] + '/' + m[1];
    }

    function getPresetDates(presetName) {
        var now = new Date();
        var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        if (presetName === 'today') {
            var isoToday = formatIsoDate(today);
            return { start: isoToday, end: isoToday };
        }
        if (presetName === 'yesterday') {
            var yest = new Date(today.getTime());
            yest.setDate(yest.getDate() - 1);
            var isoYest = formatIsoDate(yest);
            return { start: isoYest, end: isoYest };
        }
        if (presetName === 'last7') {
            var weekAgo = new Date(today.getTime());
            weekAgo.setDate(weekAgo.getDate() - 6);
            return { start: formatIsoDate(weekAgo), end: formatIsoDate(today) };
        }
        if (presetName === 'month') {
            var monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            return { start: formatIsoDate(monthStart), end: formatIsoDate(today) };
        }
        return { start: '', end: '' };
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

        state.status = params.get('status') || config.defaultStatus || 'all';
        var basis = (params.get('date_basis') || 'created').toLowerCase();
        state.date_basis = (basis === 'delivery') ? 'delivery' : 'created';
        state.start_date = params.get('start_date') || '';
        state.end_date = params.get('end_date') || '';
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

    function syncDatePresetChipsForValues(startDate, endDate) {
        var matchedPreset = '';
        if (!startDate && !endDate) {
            matchedPreset = 'all';
        } else {
            ['today', 'yesterday', 'last7', 'month'].forEach(function (name) {
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

    function syncControlsFromState() {
        if (els.statusSelect) {
            els.statusSelect.value = state.status;
        }
        if (els.dateBasisSelect) {
            els.dateBasisSelect.value = state.date_basis;
        }
        if (els.startDateInput) {
            els.startDateInput.value = state.start_date;
        }
        if (els.endDateInput) {
            els.endDateInput.value = state.end_date;
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
        syncDatePresetChipsForValues(state.start_date, state.end_date);
    }

    function readStateFromControls() {
        if (els.statusSelect) {
            state.status = els.statusSelect.value || 'all';
        }
        if (els.dateBasisSelect) {
            state.date_basis = els.dateBasisSelect.value === 'delivery' ? 'delivery' : 'created';
        }
        if (els.startDateInput) {
            state.start_date = (els.startDateInput.value || '').trim();
        }
        if (els.endDateInput) {
            state.end_date = (els.endDateInput.value || '').trim();
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

        if (state.q) {
            chips.push({
                label: 'Search: "' + state.q + '"',
                onClear: function () { state.q = ''; }
            });
        }
        if (state.start_date || state.end_date) {
            var s = state.start_date || state.end_date;
            var e = state.end_date || state.start_date;
            var basisLabel = (state.date_basis === 'delivery') ? 'Delivery' : 'Created';
            var rangeStr = (s === e) ? formatDisplayDate(s) : (formatDisplayDate(s) + ' – ' + formatDisplayDate(e));
            chips.push({
                label: basisLabel + ' Date: ' + rangeStr,
                onClear: function () {
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
        var hasAnyFilter = drawerActiveCount > 0 || (state.status !== 'all');

        if (els.filterActiveCount) {
            els.filterActiveCount.textContent = String(drawerActiveCount);
            els.filterActiveCount.hidden = (drawerActiveCount === 0);
        }
        if (els.filterToggleBtn) {
            els.filterToggleBtn.classList.toggle('has-active', drawerActiveCount > 0);
        }
        if (els.quickResetBtn) {
            els.quickResetBtn.hidden = !hasAnyFilter;
        }

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

    function resetAllFilters() {
        state.status = 'all';
        state.date_basis = 'created';
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
        if (state.start_date) {
            next.set('start_date', state.start_date);
        }
        if (state.end_date) {
            next.set('end_date', state.end_date);
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
        if (state.start_date) {
            params.set('start_date', state.start_date);
        }
        if (state.end_date) {
            params.set('end_date', state.end_date);
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

        facetsLoaded = true;
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

    function makeMetaRow(keyText, valText, isPrimary) {
        var div = document.createElement('div');
        div.className = 'crm-meta-row' + (isPrimary ? ' is-primary' : '');
        var keySpan = document.createElement('span');
        keySpan.className = 'crm-meta-key';
        keySpan.textContent = keyText;
        div.appendChild(keySpan);
        var valSpan = document.createElement('span');
        valSpan.className = 'crm-meta-val';
        valSpan.textContent = valText;
        div.appendChild(valSpan);
        return div;
    }

    // Allows natural line wrapping cleanly after '@' instead of mid-TLD breaks like '.co\nm'
    function makeEmailLine(emailStr) {
        var div = document.createElement('div');
        div.className = 'crm-cell-secondary crm-cell-email';
        var atIdx = emailStr.indexOf('@');
        if (atIdx > 0 && atIdx < emailStr.length - 1) {
            div.appendChild(document.createTextNode(emailStr.slice(0, atIdx + 1)));
            div.appendChild(document.createElement('wbr'));
            div.appendChild(document.createTextNode(emailStr.slice(atIdx + 1)));
        } else {
            div.appendChild(document.createTextNode(emailStr));
        }
        return div;
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

        rows.forEach(function (row) {
            var tr = document.createElement('tr');
            var viewHref = resolveOrderViewUrl(row);

            // 1. Order & Created Date (<th scope="row">)
            var thOrder = document.createElement('th');
            thOrder.scope = 'row';

            var cellOrderWrap = document.createElement('div');
            cellOrderWrap.className = 'crm-cell-order';

            var thumbBox = document.createElement('div');
            thumbBox.className = 'crm-thumb';
            var primaryImg = row.product && row.product.primary_image ? String(row.product.primary_image).trim() : '';
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
            idLink.textContent = row.order_code || ('OD' + row.order_id);
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
            orderInfo.appendChild(makeTextLine('crm-order-date', cDate || 'N/A'));
            if (cTime) {
                orderInfo.appendChild(makeTextLine('crm-order-time', cTime));
            }

            cellOrderWrap.appendChild(thumbBox);
            cellOrderWrap.appendChild(orderInfo);
            thOrder.appendChild(cellOrderWrap);
            tr.appendChild(thOrder);

            // 2. Product Details
            var tdProd = document.createElement('td');
            var items = (row.product && Array.isArray(row.product.items)) ? row.product.items : [];
            if (items.length === 0) {
                tdProd.appendChild(makeTextLine('crm-cell-primary', 'N/A'));
            } else {
                items.forEach(function (item) {
                    var line = document.createElement('div');
                    line.className = 'crm-item-line';

                    var titleSpan = document.createElement('span');
                    titleSpan.className = 'crm-cell-primary';
                    titleSpan.textContent = item.title || 'Item';
                    line.appendChild(titleSpan);

                    var qtyBadge = document.createElement('span');
                    qtyBadge.className = 'crm-qty-pill';
                    qtyBadge.textContent = '×' + String(item.qty || 1);
                    line.appendChild(qtyBadge);

                    if (item.sku) {
                        var skuSpan = document.createElement('span');
                        skuSpan.className = 'crm-cell-secondary';
                        skuSpan.textContent = ' [' + item.sku + ']';
                        line.appendChild(skuSpan);
                    }
                    tdProd.appendChild(line);
                });
            }
            var totalQty = row.product && row.product.total_qty ? row.product.total_qty : 1;
            tdProd.appendChild(makeTextLine('crm-cell-secondary', 'Total Qty: ' + totalQty));
            tr.appendChild(tdProd);

            // 3. Order Sender Details
            var tdSender = document.createElement('td');
            var sender = row.sender || {};
            tdSender.appendChild(makeTextLine('crm-cell-primary', sender.name || 'N/A'));
            if (sender.mobile) {
                tdSender.appendChild(makeTextLine('crm-cell-secondary', sender.mobile));
            }
            if (sender.email) {
                tdSender.appendChild(makeEmailLine(sender.email));
            }
            tr.appendChild(tdSender);

            // 4. Receiver Details
            var tdReceiver = document.createElement('td');
            var receiver = row.receiver || {};
            tdReceiver.appendChild(makeTextLine('crm-cell-primary', receiver.name || 'N/A'));
            if (receiver.mobile) {
                tdReceiver.appendChild(makeTextLine('crm-cell-secondary', receiver.mobile));
            }
            if (receiver.alt_mobile) {
                tdReceiver.appendChild(makeTextLine('crm-cell-secondary', 'Alt: ' + receiver.alt_mobile));
            }
            if (receiver.full_address) {
                tdReceiver.appendChild(makeTextLine('crm-cell-secondary', receiver.full_address));
            }
            tr.appendChild(tdReceiver);

            // 5. Delivery Details
            var tdDeliv = document.createElement('td');
            var deliv = row.delivery || {};
            tdDeliv.appendChild(makeMetaRow('Date:', deliv.date_display || 'Not scheduled', true));
            if (deliv.slot) {
                tdDeliv.appendChild(makeMetaRow('Slot:', deliv.slot, false));
            }
            if (deliv.time && String(deliv.time).toLowerCase() !== String(deliv.slot || '').toLowerCase()) {
                tdDeliv.appendChild(makeMetaRow('Time:', deliv.time, false));
            }
            if (deliv.occasion) {
                tdDeliv.appendChild(makeMetaRow('Occasion:', deliv.occasion, false));
            }
            if (deliv.message) {
                tdDeliv.appendChild(makeMetaRow('Msg:', deliv.message, false));
            }
            if (deliv.comment) {
                tdDeliv.appendChild(makeMetaRow('Note:', deliv.comment, false));
            }
            if (deliv.return_reason || deliv.return_comment) {
                var rText = [deliv.return_reason, deliv.return_comment].filter(Boolean).join(' — ');
                tdDeliv.appendChild(makeMetaRow('Return:', rText, false));
            }
            if (deliv.photo_url && isSafeHttpUrl(deliv.photo_url)) {
                var photoLink = document.createElement('a');
                photoLink.className = 'crm-photo-link';
                photoLink.href = deliv.photo_url;
                photoLink.target = '_blank';
                photoLink.rel = 'noopener noreferrer';
                photoLink.textContent = 'Delivery Photo (' + (row.order_code || row.order_id) + ')';
                tdDeliv.appendChild(photoLink);
            }
            tr.appendChild(tdDeliv);

            // 6. Status & Payment
            var tdStatus = document.createElement('td');
            var st = row.status || { label: 'Unknown', class: 'status-pending' };
            var badge = document.createElement('span');
            badge.className = 'crm-badge ' + (st.class || 'status-pending');
            badge.textContent = st.label || 'Unknown';
            tdStatus.appendChild(badge);

            var pay = row.payment || {};
            var amtDisplay = (row.product && row.product.amount_display) ? row.product.amount_display : '';
            if (amtDisplay) {
                tdStatus.appendChild(makeTextLine('crm-amount-line', amtDisplay));
            }
            if (pay.mode_label) {
                tdStatus.appendChild(makeTextLine('crm-cell-secondary', pay.mode_label));
            }
            if (row.product && row.product.coupon) {
                tdStatus.appendChild(makeTextLine('crm-cell-secondary', 'Coupon: ' + row.product.coupon));
            }
            if (pay.transaction_ref) {
                var refPill = document.createElement('div');
                refPill.className = 'crm-ref-pill';
                refPill.title = pay.transaction_ref;

                var refLabel = document.createElement('span');
                refLabel.className = 'crm-ref-label';
                refLabel.textContent = 'Ref';

                var refCode = document.createElement('span');
                refCode.className = 'crm-ref-code';
                refCode.textContent = pay.transaction_ref;

                refPill.appendChild(refLabel);
                refPill.appendChild(refCode);
                tdStatus.appendChild(refPill);
            }
            tr.appendChild(tdStatus);

            // 7. Action
            var tdAction = document.createElement('td');
            var viewBtn = document.createElement('a');
            viewBtn.className = 'crm-btn crm-view-btn';
            viewBtn.href = viewHref;
            viewBtn.setAttribute('aria-label', 'View ' + (row.order_code || ('order ' + row.order_id)));
            viewBtn.textContent = 'View';
            tdAction.appendChild(viewBtn);
            tr.appendChild(tdAction);

            fragment.appendChild(tr);
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

            if (payload.facets && (payload.facets.payment_modes || payload.facets.cities)) {
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
        var todaySlug = formatIsoDate(new Date());
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
            var todaySlug = meta.date_slug || formatIsoDate(new Date());
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

            // 2-tier grouped header covering 21 separated value-type columns (4+4+2+3+4+4 = 21)
            var tableBody = [
                [
                    { text: 'ORDER', colSpan: 4, style: 'thGroup', fillColor: '#0F172A' }, {}, {}, {},
                    { text: 'PRODUCT', colSpan: 4, style: 'thGroup', fillColor: '#1E293B' }, {}, {}, {},
                    { text: 'PRICING & PAYMENT', colSpan: 2, style: 'thGroup', fillColor: '#1E3A8A' }, {},
                    { text: 'SENDER DETAILS', colSpan: 3, style: 'thGroup', fillColor: '#0F172A' }, {}, {},
                    { text: 'RECEIVER DETAILS', colSpan: 4, style: 'thGroup', fillColor: '#1E293B' }, {}, {}, {},
                    { text: 'DELIVERY DETAILS', colSpan: 4, style: 'thGroup', fillColor: '#0F172A' }, {}, {}, {}
                ],
                [
                    { text: 'Order ID', style: 'thSub' },
                    { text: 'Order Date', style: 'thSub' },
                    { text: 'Order Time', style: 'thSub' },
                    { text: 'Status', style: 'thSub', alignment: 'center' },
                    { text: 'Product Name', style: 'thSub' },
                    { text: 'Product Website Link', style: 'thSub' },
                    { text: 'Qty', style: 'thSub', alignment: 'right' },
                    { text: 'Prod. Price', style: 'thSub', alignment: 'right' },
                    { text: 'Order Price', style: 'thSub', alignment: 'right' },
                    { text: 'Payment', style: 'thSub' },
                    { text: 'Sender Name', style: 'thSub' },
                    { text: 'Sender Mobile', style: 'thSub' },
                    { text: 'Sender Email', style: 'thSub' },
                    { text: 'Receiver Name', style: 'thSub' },
                    { text: 'Receiver Mobile', style: 'thSub' },
                    { text: 'Street Address', style: 'thSub' },
                    { text: 'City / Pin', style: 'thSub' },
                    { text: 'Deliv. Date', style: 'thSub' },
                    { text: 'Slot / Time', style: 'thSub' },
                    { text: 'Occasion', style: 'thSub' },
                    { text: 'Gift Message / Note', style: 'thSub' }
                ]
            ];

            if (rows.length === 0) {
                var emptyRow = [{
                    text: 'No orders matched the active filter criteria.',
                    colSpan: 21,
                    alignment: 'center',
                    margin: [0, 14, 0, 14],
                    style: 'tdMuted'
                }];
                for (var eIdx = 1; eIdx < 21; eIdx++) {
                    emptyRow.push({});
                }
                tableBody.push(emptyRow);
            } else {
                rows.forEach(function (row, idx) {
                    var fill = (idx % 2 === 1) ? '#F8FAFC' : '#FFFFFF';
                    var prod = row.product || {};
                    var items = Array.isArray(prod.items) ? prod.items : [];
                    var isMulti = items.length > 1;
                    var currSym = prod.currency_symbol || '₹';

                    // Product columns separated cleanly per item
                    var nameStack = [];
                    var linkStack = [];
                    var qtyStack = [];
                    var unitPriceStack = [];

                    if (items.length === 0) {
                        nameStack.push({ text: 'N/A', style: 'tdMuted' });
                        linkStack.push({ text: '—', style: 'tdMuted' });
                        qtyStack.push({ text: '1', alignment: 'right' });
                        unitPriceStack.push({ text: '—', alignment: 'right', style: 'tdMuted' });
                    } else {
                        items.forEach(function (it, itIdx) {
                            var topGap = itIdx > 0 ? [0, 2.5, 0, 0] : [0, 0, 0, 0];
                            var titleLine = it.title || 'Item';
                            if (it.sku) {
                                titleLine += ' [' + it.sku + ']';
                            }
                            nameStack.push({
                                text: pdfSafeText(titleLine),
                                bold: true,
                                margin: topGap
                            });

                            if (it.website_url && isSafeHttpUrl(it.website_url)) {
                                linkStack.push({
                                    text: pdfWrapUrl(it.website_url),
                                    link: it.website_url,
                                    color: '#1D4ED8',
                                    decoration: 'underline',
                                    fontSize: 5.4,
                                    margin: topGap
                                });
                            } else {
                                linkStack.push({ text: '—', style: 'tdMuted', margin: topGap });
                            }

                            qtyStack.push({
                                text: String(it.qty || 1),
                                alignment: 'right',
                                margin: topGap
                            });

                            unitPriceStack.push({
                                text: pdfFormatAmount(currSym, it.unit_price_num, it.unit_price),
                                alignment: 'right',
                                margin: topGap
                            });
                        });
                        if (isMulti) {
                            qtyStack.push({
                                text: 'Sum: ' + String(prod.total_qty || 1),
                                alignment: 'right',
                                style: 'tdMuted',
                                margin: [0, 2, 0, 0]
                            });
                        }
                    }

                    // Order Price cell (prominent numeric total + optional deliv charge / coupon note)
                    var orderPriceStack = [{
                        text: pdfFormatAmount(currSym, prod.total_amount_num, prod.amount_display || prod.total_amount),
                        bold: true,
                        color: '#0F172A',
                        alignment: 'right'
                    }];
                    if (typeof prod.delivery_charges_num === 'number' && prod.delivery_charges_num > 0) {
                        orderPriceStack.push({
                            text: '+Deliv: ' + pdfFormatAmount(currSym, prod.delivery_charges_num, ''),
                            style: 'tdMuted',
                            alignment: 'right',
                            margin: [0, 1.5, 0, 0]
                        });
                    }
                    if (prod.coupon) {
                        orderPriceStack.push({
                            text: pdfSafeText('Cpn: ' + prod.coupon),
                            style: 'tdMuted',
                            alignment: 'right',
                            margin: [0, 1, 0, 0]
                        });
                    }

                    // Payment cell
                    var pay = row.payment || {};
                    var payStack = [{ text: pdfSafeText(pay.mode_label || 'Unspecified') }];
                    if (pay.transaction_status_label) {
                        payStack.push({ text: pdfSafeText(pay.transaction_status_label), style: 'tdMuted', margin: [0, 1, 0, 0] });
                    }

                    // Sender cells
                    var sender = row.sender || {};
                    var senderMobStack = [{ text: pdfSafeText(sender.mobile || '—') }];
                    if (sender.alt_mobile) {
                        senderMobStack.push({ text: pdfSafeText('Alt: ' + sender.alt_mobile), style: 'tdMuted', margin: [0, 1, 0, 0] });
                    }

                    // Receiver cells
                    var receiver = row.receiver || {};
                    var recMobStack = [{ text: pdfSafeText(receiver.mobile || '—') }];
                    if (receiver.alt_mobile) {
                        recMobStack.push({ text: pdfSafeText('Alt: ' + receiver.alt_mobile), style: 'tdMuted', margin: [0, 1, 0, 0] });
                    }
                    var cityStateText = [receiver.city, receiver.state].filter(Boolean).join(', ');
                    var cityPinStack = [{ text: pdfSafeText(cityStateText || '—') }];
                    if (receiver.pin_code) {
                        cityPinStack.push({ text: pdfSafeText('PIN: ' + receiver.pin_code), style: 'tdMuted', margin: [0, 1, 0, 0] });
                    }

                    // Delivery cells
                    var deliv = row.delivery || {};
                    var slotTimeStack = [];
                    if (deliv.slot) {
                        slotTimeStack.push({ text: pdfSafeText(deliv.slot) });
                    }
                    if (deliv.time && String(deliv.time).toLowerCase() !== String(deliv.slot || '').toLowerCase()) {
                        slotTimeStack.push({ text: pdfSafeText(deliv.time), style: 'tdMuted', margin: [0, 1, 0, 0] });
                    }
                    if (slotTimeStack.length === 0) {
                        slotTimeStack.push({ text: '—', style: 'tdMuted' });
                    }

                    var msgNoteStack = [];
                    if (deliv.message) {
                        msgNoteStack.push({ text: pdfSafeText(deliv.message) });
                    }
                    if (deliv.comment) {
                        msgNoteStack.push({ text: pdfSafeText('Note: ' + deliv.comment), style: 'tdMuted', margin: [0, 1.5, 0, 0] });
                    }
                    if (deliv.return_reason || deliv.return_comment) {
                        var rStr = [deliv.return_reason, deliv.return_comment].filter(Boolean).join(' - ');
                        msgNoteStack.push({ text: pdfSafeText('Return: ' + rStr), style: 'tdMuted', margin: [0, 1.5, 0, 0] });
                    }
                    if (msgNoteStack.length === 0) {
                        msgNoteStack.push({ text: '—', style: 'tdMuted' });
                    }

                    // Created date / time fallback
                    var cDate = row.created_date_display || '';
                    var cTime = row.created_time_display || '';
                    if (!cDate && row.created_display) {
                        var partsDt = String(row.created_display).split(' ');
                        cDate = partsDt[0] || 'N/A';
                        cTime = partsDt.slice(1).join(' ');
                    }

                    var stColors = pdfStatusColors(row.status);

                    tableBody.push([
                        { fillColor: fill, text: pdfSafeText(row.order_code || ('OD' + row.order_id)), bold: true, color: '#0F172A' },
                        { fillColor: fill, text: pdfSafeText(cDate || 'N/A') },
                        { fillColor: fill, text: pdfSafeText(cTime || '—'), style: 'tdMuted' },
                        { fillColor: stColors.fillColor, text: pdfSafeText((row.status && row.status.label) || 'Unknown'), bold: true, color: stColors.color, alignment: 'center' },
                        { fillColor: fill, stack: nameStack },
                        { fillColor: fill, stack: linkStack },
                        { fillColor: fill, stack: qtyStack },
                        { fillColor: fill, stack: unitPriceStack },
                        { fillColor: '#EEF2FF', stack: orderPriceStack },
                        { fillColor: fill, stack: payStack },
                        { fillColor: fill, text: pdfSafeText(sender.name || 'N/A') },
                        { fillColor: fill, stack: senderMobStack },
                        { fillColor: fill, text: sender.email ? pdfWrapEmail(sender.email) : '—' },
                        { fillColor: fill, text: pdfSafeText(receiver.name || 'N/A') },
                        { fillColor: fill, stack: recMobStack },
                        { fillColor: fill, text: pdfSafeText(receiver.street_address || receiver.full_address || '—') },
                        { fillColor: fill, stack: cityPinStack },
                        { fillColor: fill, text: pdfSafeText(deliv.date_display || 'Not scheduled') },
                        { fillColor: fill, stack: slotTimeStack },
                        { fillColor: fill, text: pdfSafeText(deliv.occasion || '—') },
                        { fillColor: fill, stack: msgNoteStack }
                    ]);
                });
            }

            var docDefinition = {
                pageSize: 'A3',
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
                            { text: pdfSafeText((meta.site_name || 'CRM Orders') + ' — Generated ' + (meta.exported_at || todaySlug)), style: 'footerText', alignment: 'left' },
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
                                    { text: pdfSafeText(meta.filter_summary || 'All Orders'), style: 'subtitle' }
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
                            widths: [34, 39, 33, 38, 76, 90, 21, 40, 48, 38, 54, 43, 64, 54, 43, 78, 48, 39, 48, 38, '*'],
                            body: tableBody
                        },
                        layout: {
                            hLineWidth: function () { return 0.4; },
                            vLineWidth: function () { return 0.4; },
                            hLineColor: function () { return '#CBD5E1'; },
                            vLineColor: function () { return '#E2E8F0'; },
                            paddingLeft: function () { return 2; },
                            paddingRight: function () { return 2; },
                            paddingTop: function () { return 3.2; },
                            paddingBottom: function () { return 3.2; }
                        }
                    }
                ],
                defaultStyle: {
                    fontSize: 6.2,
                    lineHeight: 1.22,
                    color: '#0F172A'
                },
                styles: {
                    title: { fontSize: 13, bold: true, color: '#0F172A' },
                    subtitle: { fontSize: 7.5, color: '#475569', margin: [0, 2, 0, 0] },
                    kpiLabel: { fontSize: 6, bold: true, color: '#475569', letterSpacing: 0.4 },
                    kpiValue: { fontSize: 9.5, bold: true, color: '#0F172A', margin: [0, 1.5, 0, 0] },
                    thGroup: { fontSize: 6.5, bold: true, color: '#FFFFFF', alignment: 'center', margin: [0, 1.5, 0, 1.5] },
                    thSub: { fontSize: 6.1, bold: true, color: '#1E293B', fillColor: '#F1F5F9', margin: [0, 1.5, 0, 1.5] },
                    tdMuted: { fontSize: 5.7, color: '#475569' },
                    footerText: { fontSize: 7, color: '#64748B' }
                }
            };

            pdfMakeLib.createPdf(docDefinition).download(filename);
            announceStatus('PDF report generated and downloaded (' + rows.length + ' order(s)).', 'success');
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
                readStateFromControls();
                state.page = 1;
                syncControlsFromState();
                updateActiveFiltersUI();
                setFilterDrawerOpen(false, { restoreFocus: true });
                loadOrders(true);
            });
        }

        [els.startDateInput, els.endDateInput].forEach(function (inp) {
            if (!inp) {
                return;
            }
            inp.addEventListener('change', function () {
                var s = els.startDateInput ? (els.startDateInput.value || '').trim() : '';
                var e = els.endDateInput ? (els.endDateInput.value || '').trim() : '';
                syncDatePresetChipsForValues(s, e);
            });
        });

        els.presetChips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                var presetName = chip.getAttribute('data-preset') || 'all';
                var range = getPresetDates(presetName);
                if (els.startDateInput) {
                    els.startDateInput.value = range.start;
                }
                if (els.endDateInput) {
                    els.endDateInput.value = range.end;
                }
                syncDatePresetChipsForValues(range.start, range.end);
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

        [els.resetBtn, els.quickResetBtn, els.chipsClearAllBtn].forEach(function (btn) {
            if (btn) {
                btn.addEventListener('click', resetAllFilters);
            }
        });

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
    readStateFromLocation();
    syncControlsFromState();
    updateActiveFiltersUI();
    installToolbarKeyboardFallback();
    bindEvents();
    loadOrders(false);
})();
