/**
 * URL Redirections Manager SPA Controller
 * Shared across Admin and Author dashboards; communicates exclusively via /api/v3/setting/redirection/
 */
(function () {
  'use strict';

  var STATUS_CATALOG = {
    '301': { label: '301 (Moved Permanently)', short: 'Moved Permanently', category: 'permanent' },
    '302': { label: '302 (Found / Temporary)', short: 'Found (Temporary)', category: 'temporary' },
    '307': { label: '307 (Temporary Redirect)', short: 'Temporary Redirect', category: 'temporary' },
    '308': { label: '308 (Permanent Redirect)', short: 'Permanent Redirect', category: 'permanent' },
    '300': { label: '300 (Multiple Choices)', short: 'Multiple Choices', category: 'other' },
    '303': { label: '303 (See Other)', short: 'See Other', category: 'other' },
    '304': { label: '304 (Not Modified)', short: 'Not Modified', category: 'other' }
  };

  var RESERVED_PREFIXES = [
    '/dashboard',
    '/api',
    '/login',
    '/logout',
    '/register',
    '/sso',
    '/oauth',
    '/viewmedia'
  ];

  var textCollator = typeof Intl !== 'undefined' && typeof Intl.Collator === 'function'
    ? new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' })
    : null;

  function boot() {
    var root = document.getElementById('redirectionManager');
    if (!root || root.dataset.rmInitialized === '1') {
      return;
    }
    root.dataset.rmInitialized = '1';

    var apiUrl = root.dataset.apiUrl || '/api/v3/setting/redirection/';
    var siteBase = (root.dataset.siteBase || window.location.origin || '').replace(/\/+$/, '');
    var siteDomain = (root.dataset.siteDomain || window.location.host || '').toLowerCase();
    var initStatus = normalizeFilterStatus(root.dataset.initStatus || 'all');
    var initQuery = String(root.dataset.initQ || '').trim();
    var initMsgType = String(root.dataset.initMsgType || '').trim();
    var initMsg = String(root.dataset.initMsg || '').trim();

    var siteHosts = buildSiteHosts(siteDomain, siteBase);

    // Elements
    var els = {
      root: root,
      announcer: document.getElementById('rmFieldErrorAnnouncer'),
      statTotal: document.getElementById('rmStatTotal'),
      statPermanent: document.getElementById('rmStatPermanent'),
      statTemporary: document.getElementById('rmStatTemporary'),
      statOther: document.getElementById('rmStatOther'),
      alertBanner: document.getElementById('rmAlertBanner'),
      alertText: document.getElementById('rmAlertText'),
      alertDismiss: document.getElementById('rmAlertDismiss'),
      focusAddBtn: document.getElementById('rmFocusAddBtn'),
      addForm: document.getElementById('rmAddForm'),
      addFrom: document.getElementById('rmAddFrom'),
      addFromPreview: document.getElementById('rmAddFromPreview'),
      addFromError: document.getElementById('rmAddFromError'),
      addTo: document.getElementById('rmAddTo'),
      addToError: document.getElementById('rmAddToError'),
      addStatus: document.getElementById('rmAddStatus'),
      addStatusError: document.getElementById('rmAddStatusError'),
      addSubmitBtn: document.getElementById('rmAddSubmitBtn'),
      addResetBtn: document.getElementById('rmAddResetBtn'),
      countBadge: document.getElementById('rmCountBadge'),
      filterForm: document.getElementById('rmFilterForm'),
      searchInput: document.getElementById('rmSearchInput'),
      filterStatus: document.getElementById('rmFilterStatus'),
      clearFilterBtn: document.getElementById('rmClearFilterBtn'),
      sortStatus: document.getElementById('rmSortStatus'),
      tableWrapper: document.getElementById('rmTableWrapper'),
      table: document.getElementById('redirectionTable'),
      tbody: document.getElementById('redirectionTableBody'),
      emptyState: document.getElementById('rmEmptyState'),
      emptyTitle: document.getElementById('rmEmptyTitle'),
      emptyCopy: document.getElementById('rmEmptyCopy'),
      emptyResetBtn: document.getElementById('rmEmptyResetBtn'),
      editDialog: document.getElementById('rmEditDialog'),
      editForm: document.getElementById('rmEditForm'),
      editId: document.getElementById('rmEditId'),
      editMeta: document.getElementById('rmEditMeta'),
      editServerError: document.getElementById('rmEditServerError'),
      editFrom: document.getElementById('rmEditFrom'),
      editFromPreview: document.getElementById('rmEditFromPreview'),
      editFromError: document.getElementById('rmEditFromError'),
      editTo: document.getElementById('rmEditTo'),
      editToError: document.getElementById('rmEditToError'),
      editStatus: document.getElementById('rmEditStatus'),
      editStatusError: document.getElementById('rmEditStatusError'),
      editSaveBtn: document.getElementById('rmEditSaveBtn'),
      deleteDialog: document.getElementById('rmDeleteDialog'),
      deleteSummary: document.getElementById('rmDeleteSummary'),
      deleteServerError: document.getElementById('rmDeleteServerError'),
      deleteConfirmBtn: document.getElementById('rmDeleteConfirmBtn')
    };

    var state = {
      items: [],
      rowsById: new Map(),
      filterStatus: initStatus,
      filterQuery: initQuery,
      sortCol: 3, // Updated column default
      sortType: 'number',
      sortDir: 'descending',
      deletingId: 0,
      lastDeleteInvoker: null,
      isLoading: false,
      isSavingEdit: false,
      isDeleting: false
    };

    // Apply initial filter controls
    if (els.filterStatus) {
      els.filterStatus.value = state.filterStatus;
    }
    if (els.searchInput && state.filterQuery !== '') {
      els.searchInput.value = state.filterQuery;
    }

    // One-time banner forwarded from server redirect query params
    if (initMsg !== '') {
      showAlert(initMsg, initMsgType === 'error' ? 'error' : 'success');
      cleanTransientUrlParams();
    }

    // -----------------------------------------------------------------------
    // Helper & Validation Utilities
    // -----------------------------------------------------------------------
    function buildSiteHosts(domain, base) {
      var map = Object.create(null);
      function addHost(h) {
        var clean = String(h || '').trim().toLowerCase();
        if (!clean) return;
        map[clean] = true;
        var noPort = clean.replace(/:\d+$/, '');
        map[noPort] = true;
        if (noPort.indexOf('www.') === 0) {
          map[noPort.slice(4)] = true;
        } else {
          map['www.' + noPort] = true;
        }
      }
      addHost(domain);
      addHost(window.location.host);
      if (base) {
        try {
          addHost(new URL(base).host);
        } catch (err) {
          // ignore invalid base
        }
      }
      return map;
    }

    function normalizeFilterStatus(raw) {
      var clean = String(raw || '').trim();
      if (!clean || clean === '#' || clean.toLowerCase() === 'all') {
        return 'all';
      }
      return Object.prototype.hasOwnProperty.call(STATUS_CATALOG, clean) ? clean : 'all';
    }

    function safeHref(raw) {
      var val = String(raw || '').trim();
      if (!val) return '';
      if (val.charAt(0) === '/' && val.charAt(1) !== '/') {
        return siteBase ? (siteBase + val) : val;
      }
      if (/^https?:\/\//i.test(val)) {
        return val;
      }
      return '';
    }

    function normalizeFromClient(raw) {
      var value = String(raw || '').trim();
      if (!value || value === '#') {
        return { ok: false, error: 'Enter the old source path to redirect from (for example: /old-page/).' };
      }
      if (/^\s*(?:javascript|data|vbscript|mailto|tel):/i.test(value)) {
        return { ok: false, error: 'Source path cannot use a script or protocol scheme.' };
      }
      if (/[\u0000-\u001f\u007f\s]/.test(value)) {
        return { ok: false, error: 'Source path cannot contain spaces (use hyphens like /old-page/).' };
      }
      if (value.indexOf('//') === 0) {
        return { ok: false, error: 'Enter a relative path on this site starting with "/".' };
      }
      if (/^https?:\/\//i.test(value)) {
        var parsed;
        try {
          parsed = new URL(value);
        } catch (err) {
          return { ok: false, error: 'Source URL format is invalid.' };
        }
        var h = parsed.hostname.toLowerCase();
        var hp = parsed.host.toLowerCase();
        if (!siteHosts[h] && !siteHosts[hp]) {
          return { ok: false, error: 'Source URL must belong to this website domain (for example: /old-page/).' };
        }
        if (parsed.search || parsed.hash || value.indexOf('?') !== -1 || value.indexOf('#') !== -1) {
          return { ok: false, error: 'Source path cannot include "?" query parameters or "#" fragments.' };
        }
        value = parsed.pathname || '/';
      } else if (value.indexOf('?') !== -1 || value.indexOf('#') !== -1) {
        return { ok: false, error: 'Source path cannot include "?" query parameters or "#" fragments.' };
      }

      value = ('/' + value.replace(/^\/+/, '')).replace(/\/+/g, '/');
      if (value === '/' || value === '') {
        return { ok: false, error: 'The homepage "/" cannot be redirected. Specify a specific path such as /old-page/.' };
      }

      if (value.indexOf('.') === -1 && value.slice(-1) !== '/') {
        value += '/';
      }

      var lower = value.toLowerCase();
      for (var i = 0; i < RESERVED_PREFIXES.length; i++) {
        var prefix = RESERVED_PREFIXES[i];
        if (lower === prefix || lower.indexOf(prefix + '/') === 0) {
          return { ok: false, error: 'Protected system route "' + prefix + '/" cannot be redirected.' };
        }
      }

      if (value.length > 255) {
        return { ok: false, error: 'Source path must be 255 characters or fewer.' };
      }

      return { ok: true, value: value };
    }

    function normalizeToClient(raw, normalizedFrom) {
      var value = String(raw || '').trim();
      if (!value) {
        return { ok: false, error: 'Enter the destination URL or path visitors should reach.' };
      }
      if (/^\s*(?:javascript|data|vbscript):/i.test(value)) {
        return { ok: false, error: 'Destination URL scheme is not allowed.' };
      }
      if (/[\u0000-\u001f\u007f\s]/.test(value)) {
        return { ok: false, error: 'Destination URL cannot contain spaces or control characters.' };
      }
      if (value.indexOf('//') === 0) {
        return { ok: false, error: 'Use an explicit https:// URL or a root-relative path starting with "/".' };
      }

      var isAbs = /^https?:\/\//i.test(value);
      var isRel = value.charAt(0) === '/';
      if (!isAbs && !isRel) {
        return { ok: false, error: 'Destination must start with "/" for an internal path or "https://" for an external URL.' };
      }

      if (isAbs) {
        var parsed;
        try {
          parsed = new URL(value);
        } catch (err) {
          return { ok: false, error: 'Enter a valid destination URL including https:// or http://.' };
        }
        if (!parsed.hostname) {
          return { ok: false, error: 'Destination URL must include a valid host name.' };
        }
        var toHost = parsed.hostname.toLowerCase();
        var toHostPort = parsed.host.toLowerCase();
        var sameSite = Boolean(siteHosts[toHost] || siteHosts[toHostPort]);
        if (sameSite && !parsed.search && !parsed.hash && normalizedFrom) {
          var toPath = ('/' + (parsed.pathname || '/').replace(/^\/+/, '')).replace(/\/+/g, '/');
          if (toPath !== '/' && toPath.indexOf('.') === -1 && toPath.slice(-1) !== '/') {
            toPath += '/';
          }
          if (toPath === normalizedFrom) {
            return { ok: false, error: 'Destination points back to the same source path and would cause a redirect loop.' };
          }
        }
      } else {
        var cleanRel = value.replace(/\/+/g, '/');
        var qIdx = cleanRel.search(/[?#]/);
        var pathPart = qIdx === -1 ? cleanRel : cleanRel.slice(0, qIdx);
        var cmpPath = pathPart;
        if (cmpPath !== '/' && cmpPath.indexOf('.') === -1 && cmpPath.slice(-1) !== '/') {
          cmpPath += '/';
        }
        if (qIdx === -1 && normalizedFrom && cmpPath === normalizedFrom) {
          return { ok: false, error: 'Destination URL cannot be the same as the source path (infinite redirect loop).' };
        }
        value = cleanRel;
      }

      if (value.length > 255) {
        return { ok: false, error: 'Destination URL must be 255 characters or fewer.' };
      }

      return { ok: true, value: value };
    }

    function parseTimestampMs(raw) {
      var text = String(raw || '').trim();
      if (!text || text.indexOf('0000-00-00') === 0) {
        return 0;
      }
      var iso = text.replace(' ', 'T');
      var ms = Date.parse(iso);
      return Number.isFinite(ms) ? ms : 0;
    }

    function formatDisplayDate(raw) {
      var ms = parseTimestampMs(raw);
      if (!ms) {
        return { display: '—', iso: '', epoch: 0 };
      }
      var d = new Date(ms);
      var display = d.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
      return {
        display: display,
        iso: d.toISOString(),
        epoch: ms
      };
    }

    function formatMetaDateTime(raw) {
      var ms = parseTimestampMs(raw);
      if (!ms) return '—';
      return new Date(ms).toLocaleString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }

    // -----------------------------------------------------------------------
    // Accessible Deferred Field Validation & Error Announcement
    // -----------------------------------------------------------------------
    var fieldDirtyState = new WeakMap();
    var announcedFields = new WeakSet();
    var serverFlaggedFields = new WeakSet();
    var announceTimer = null;
    var activeAnnouncedField = null;

    function getFieldPairFor(el) {
      if (el === els.addFrom) return { fromEl: els.addFrom, toEl: els.addTo };
      if (el === els.addTo) return { fromEl: els.addFrom, toEl: els.addTo };
      if (el === els.editFrom) return { fromEl: els.editFrom, toEl: els.editTo };
      if (el === els.editTo) return { fromEl: els.editFrom, toEl: els.editTo };
      return null;
    }

    function linkErrorDescribedBy(el, shown) {
      var errorId = el && el.dataset ? el.dataset.error : '';
      if (!errorId) return;
      var ids = (el.getAttribute('aria-describedby') || '')
        .split(/\s+/)
        .filter(function (id) { return id && id !== errorId; });
      if (shown) {
        ids.push(errorId);
      }
      if (ids.length) {
        el.setAttribute('aria-describedby', ids.join(' '));
      } else {
        el.removeAttribute('aria-describedby');
      }
    }

    function setErrorMessageText(el, msg) {
      var errorId = el && el.dataset ? el.dataset.error : '';
      if (!errorId) return;
      var box = document.getElementById(errorId);
      if (!box) return;
      var textSpan = box.querySelector('.rm-error-text');
      if (textSpan) {
        textSpan.textContent = msg || '';
      } else {
        box.textContent = msg || '';
      }
    }

    function spokenText(node) {
      if (!node) return '';
      var clone = node.cloneNode(true);
      clone.querySelectorAll('[aria-hidden="true"]').forEach(function (n) { n.remove(); });
      clone.querySelectorAll('[role="img"][aria-label]').forEach(function (n) {
        n.replaceWith(document.createTextNode(n.getAttribute('aria-label') + ': '));
      });
      return clone.textContent.replace(/\s+/g, ' ').trim();
    }

    function announceFieldError(el) {
      if (!els.announcer || !el || !el.dataset || !el.dataset.error) return;
      var errBox = document.getElementById(el.dataset.error);
      if (!errBox) return;
      var labelEl = el.labels && el.labels[0] ? el.labels[0] : null;
      var labelPrefix = labelEl ? spokenText(labelEl).replace(/\s*\*\s*$/, '').trim() + ': ' : '';
      var msgText = spokenText(errBox);
      if (!msgText) return;
      clearTimeout(announceTimer);
      els.announcer.textContent = '';
      activeAnnouncedField = el;
      announceTimer = setTimeout(function () {
        if (els.announcer) {
          els.announcer.textContent = labelPrefix + msgText;
        }
      }, 500);
    }

    function clearPendingAnnouncement(el) {
      if (activeAnnouncedField === el && els.announcer) {
        clearTimeout(announceTimer);
        els.announcer.textContent = '';
        activeAnnouncedField = null;
      }
    }

    function computeControlValidation(el) {
      if (!el) return { ok: true, error: '' };
      if (el === els.addFrom || el === els.editFrom) {
        return normalizeFromClient(el.value);
      }
      if (el === els.addTo || el === els.editTo) {
        var pair = getFieldPairFor(el);
        var fromNorm = '';
        if (pair && pair.fromEl) {
          var fc = normalizeFromClient(pair.fromEl.value);
          if (fc.ok) fromNorm = fc.value;
        }
        return normalizeToClient(el.value, fromNorm);
      }
      if (el === els.addStatus || el === els.editStatus) {
        var code = String(el.value || '').trim();
        if (!Object.prototype.hasOwnProperty.call(STATUS_CATALOG, code)) {
          return { ok: false, error: 'Select a valid HTTP redirect status code.' };
        }
        return { ok: true, value: code };
      }
      return { ok: el.checkValidity(), error: el.validationMessage || '' };
    }

    function applyControlValidation(el, options) {
      options = options || {};
      var res = computeControlValidation(el);
      if (res.ok) {
        el.setCustomValidity('');
        el.classList.remove('user-invalid-fallback');
        el.setAttribute('aria-invalid', 'false');
        linkErrorDescribedBy(el, false);
        setErrorMessageText(el, '');
        announcedFields.delete(el);
        serverFlaggedFields.delete(el);
        clearPendingAnnouncement(el);
        return true;
      }

      el.setCustomValidity(res.error);
      if (options.showVisual) {
        el.classList.add('user-invalid-fallback');
        el.setAttribute('aria-invalid', 'true');
        linkErrorDescribedBy(el, true);
        setErrorMessageText(el, res.error);
        if (options.announce && !announcedFields.has(el)) {
          announceFieldError(el);
          announcedFields.add(el);
        }
      }
      return false;
    }

    function setFieldServerError(el, message) {
      if (!el) return;
      var msg = String(message || 'Invalid value.');
      el.setCustomValidity(msg);
      el.classList.add('user-invalid-fallback');
      el.setAttribute('aria-invalid', 'true');
      linkErrorDescribedBy(el, true);
      setErrorMessageText(el, msg);
      serverFlaggedFields.add(el);
      announcedFields.add(el);
      fieldDirtyState.set(el, { hasInteracted: true, hasBlurred: true });
    }

    function resetFormValidation(formEl, previewEl) {
      if (!formEl) return;
      Array.prototype.forEach.call(formEl.elements, function (ctrl) {
        if (!ctrl.matches || !ctrl.matches('input, select, textarea')) return;
        fieldDirtyState.delete(ctrl);
        announcedFields.delete(ctrl);
        serverFlaggedFields.delete(ctrl);
        ctrl.setCustomValidity('');
        ctrl.classList.remove('user-invalid-fallback');
        ctrl.setAttribute('aria-invalid', 'false');
        linkErrorDescribedBy(ctrl, false);
        setErrorMessageText(ctrl, '');
        clearPendingAnnouncement(ctrl);
      });
      if (previewEl) {
        previewEl.textContent = '';
      }
    }

    function validateFormAll(formEl) {
      var controls = Array.prototype.filter.call(formEl.elements, function (c) {
        return c.matches && c.matches('input:not([type="hidden"]), select, textarea');
      });
      var firstInvalid = null;
      for (var i = 0; i < controls.length; i++) {
        var ctrl = controls[i];
        fieldDirtyState.set(ctrl, { hasInteracted: true, hasBlurred: true });
        announcedFields.add(ctrl); // Focus moves to firstInvalid on submit so screen reader reads it
        var valid = applyControlValidation(ctrl, { showVisual: true, announce: false });
        if (!valid && !firstInvalid) {
          firstInvalid = ctrl;
        }
      }
      return { valid: !firstInvalid, firstInvalid: firstInvalid };
    }

    function updatePathPreview(inputEl, previewEl) {
      if (!inputEl || !previewEl) return;
      var raw = String(inputEl.value || '').trim();
      previewEl.textContent = '';
      if (!raw) return;
      var check = normalizeFromClient(raw);
      if (check.ok) {
        var label = document.createTextNode('Resolved source: ');
        var code = document.createElement('code');
        code.textContent = (siteBase ? siteBase : '') + check.value;
        previewEl.append(label, code);
      }
    }

    function wireValidationOnForm(formEl, previewEl, fromEl, toEl, bannerResetter) {
      if (!formEl) return;

      formEl.addEventListener('input', function (event) {
        var target = event.target;
        if (target === fromEl && previewEl) {
          updatePathPreview(fromEl, previewEl);
        }
        if (typeof bannerResetter === 'function') {
          bannerResetter();
        }
        if (!target || !target.matches || !target.matches('input, select, textarea')) return;

        var st = fieldDirtyState.get(target) || { hasInteracted: false, hasBlurred: false };
        st.hasInteracted = true;
        fieldDirtyState.set(target, st);

        // Clear existing error state while typing as soon as field is valid; never show premature errors mid-typing
        var check = computeControlValidation(target);
        if (check.ok) {
          applyControlValidation(target, { showVisual: false, announce: false });
          // Also clear loop error on paired destination field if fixing source resolves the loop
          if (target === fromEl && toEl && serverFlaggedFields.has(toEl) === false && toEl.getAttribute('aria-invalid') === 'true') {
            var toCheck = computeControlValidation(toEl);
            if (toCheck.ok) {
              applyControlValidation(toEl, { showVisual: false, announce: false });
            }
          }
        } else if (!st.hasBlurred && target.getAttribute('aria-invalid') !== 'true') {
          // Clear customValidity so native :user-invalid doesn't prematurely misfire
          target.setCustomValidity('');
        }
      });

      formEl.addEventListener('blur', function (event) {
        var target = event.target;
        if (!target || !target.matches || !target.matches('input:not([type="hidden"]), select, textarea')) return;
        var st = fieldDirtyState.get(target) || { hasInteracted: false, hasBlurred: false };
        st.hasBlurred = true;
        fieldDirtyState.set(target, st);
        if (st.hasInteracted && !serverFlaggedFields.has(target)) {
          applyControlValidation(target, { showVisual: true, announce: true });
        }
      }, true);

      formEl.addEventListener('change', function (event) {
        var target = event.target;
        if (!target || !target.matches || !target.matches('select')) return;
        var st = fieldDirtyState.get(target) || { hasInteracted: false, hasBlurred: false };
        st.hasInteracted = true;
        st.hasBlurred = true;
        fieldDirtyState.set(target, st);
        applyControlValidation(target, { showVisual: true, announce: true });
      });

      formEl.addEventListener('reset', function () {
        setTimeout(function () {
          resetFormValidation(formEl, previewEl);
        }, 0);
      });
    }

    // -----------------------------------------------------------------------
    // Alert Banner & KPI Stats
    // -----------------------------------------------------------------------
    function showAlert(message, type) {
      if (!els.alertBanner || !els.alertText) return;
      els.alertBanner.classList.remove('rm-alert-success', 'rm-alert-error');
      els.alertBanner.classList.add(type === 'error' ? 'rm-alert-error' : 'rm-alert-success');
      els.alertBanner.setAttribute('role', type === 'error' ? 'alert' : 'status');
      els.alertBanner.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
      els.alertText.textContent = message;
      els.alertBanner.hidden = false;
    }

    function hideAlert() {
      if (!els.alertBanner) return;
      els.alertBanner.hidden = true;
      if (els.alertText) {
        els.alertText.textContent = '';
      }
    }

    function updateStatsDisplay(stats) {
      if (!stats) {
        var perm = 0;
        var temp = 0;
        var other = 0;
        for (var i = 0; i < state.items.length; i++) {
          var code = String(state.items[i].http_status || '');
          if (code === '301' || code === '308') perm++;
          else if (code === '302' || code === '307') temp++;
          else other++;
        }
        stats = {
          total: state.items.length,
          permanent: perm,
          temporary: temp,
          other: other
        };
      }
      if (els.statTotal) els.statTotal.textContent = String(stats.total);
      if (els.statPermanent) els.statPermanent.textContent = String(stats.permanent);
      if (els.statTemporary) els.statTemporary.textContent = String(stats.temporary);
      if (els.statOther) els.statOther.textContent = String(stats.other);
    }

    function cleanTransientUrlParams() {
      if (!window.history || typeof window.history.replaceState !== 'function') return;
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete('statusType');
        url.searchParams.delete('message');
        url.searchParams.delete('responseMsg');
        window.history.replaceState(null, '', url.pathname + url.search + url.hash);
      } catch (err) {
        // ignore URL manipulation issues
      }
    }

    function syncFilterUrlState() {
      if (!window.history || typeof window.history.replaceState !== 'function') return;
      try {
        var url = new URL(window.location.href);
        url.searchParams.set('type', 'redirection');
        url.searchParams.set('status', state.filterStatus || 'all');
        if (state.filterQuery) {
          url.searchParams.set('q', state.filterQuery);
        } else {
          url.searchParams.delete('q');
        }
        window.history.replaceState(null, '', url.pathname + url.search + url.hash);
      } catch (err) {
        // ignore
      }
    }

    // -----------------------------------------------------------------------
    // Single-line Overflow Truncation Detection & --scroll-width Measurement
    // -----------------------------------------------------------------------
    var needsTruncFallback = typeof CSS === 'undefined' ||
      typeof CSS.supports !== 'function' ||
      !CSS.supports('animation-timeline', 'scroll()');

    var labelResizeObserver = typeof ResizeObserver === 'function'
      ? new ResizeObserver(function (entries) {
        for (var i = 0; i < entries.length; i++) {
          measureLabelOverflow(entries[i].target);
        }
      })
      : null;

    function measureLabelOverflow(labelEl) {
      if (!labelEl) return;
      var overflowPx = Math.max(0, labelEl.scrollWidth - labelEl.clientWidth);
      if (overflowPx > 1) {
        labelEl.style.setProperty('--scroll-width', String(Math.min(450, Math.max(80, overflowPx))));
      } else {
        labelEl.style.removeProperty('--scroll-width');
      }
      if (needsTruncFallback) {
        labelEl.classList.toggle('truncated', overflowPx > 1);
      }
    }

    function observeRowLabels(tr) {
      if (!tr) return;
      var labels = tr.querySelectorAll('.rm-url-label');
      for (var i = 0; i < labels.length; i++) {
        measureLabelOverflow(labels[i]);
        if (labelResizeObserver) {
          labelResizeObserver.observe(labels[i]);
        }
      }
    }

    // -----------------------------------------------------------------------
    // Row DOM Construction & In-Place Update (Never rebuilds full tbody on sort)
    // -----------------------------------------------------------------------
    function buildCopyControl(targetId, ariaLabelText) {
      var wrap = document.createElement('span');
      wrap.className = 'rm-copy-wrap';

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'rm-copy-btn';
      btn.setAttribute('command', '--copy');
      btn.setAttribute('commandfor', targetId);
      btn.setAttribute('aria-label', ariaLabelText);

      var icon = document.createElement('span');
      icon.className = 'rm-icon rm-copy-icon';
      icon.setAttribute('aria-hidden', 'true');
      btn.appendChild(icon);

      var tip = document.createElement('span');
      tip.className = 'rm-copy-tip';
      tip.setAttribute('role', 'status');

      wrap.append(btn, tip);
      return wrap;
    }

    function buildUrlCellInner(urlText, hrefVal, elId, copyAriaLabel) {
      var container = document.createElement('div');
      container.className = 'rm-url-cell-inner';

      var labelEl;
      if (hrefVal) {
        labelEl = document.createElement('a');
        labelEl.href = hrefVal;
        labelEl.target = '_blank';
        labelEl.rel = 'noopener noreferrer';
      } else {
        labelEl = document.createElement('span');
      }
      labelEl.id = elId;
      labelEl.className = 'rm-url-label';

      var innerSpan = document.createElement('span');
      innerSpan.textContent = urlText;
      labelEl.appendChild(innerSpan);

      container.append(labelEl, buildCopyControl(elId, copyAriaLabel));
      return container;
    }

    function buildStatusBadge(rule) {
      var cat = rule.status_category || 'other';
      var badge = document.createElement('span');
      badge.className = 'rm-status-badge rm-status-' + (cat === 'permanent' || cat === 'temporary' ? cat : 'other');

      var codeEl = document.createElement('strong');
      codeEl.className = 'rm-status-code';
      codeEl.textContent = String(rule.http_status || '');

      var shortEl = document.createElement('span');
      shortEl.textContent = String(rule.status_short || 'Redirect');

      badge.append(codeEl, shortEl);
      return badge;
    }

    function populateRowElement(tr, rule) {
      var id = Number(rule.id);
      var fromId = 'rmFromText-' + id;
      var toId = 'rmToText-' + id;
      var dateInfo = formatDisplayDate(rule.updated_at || rule.created_at);

      tr.dataset.id = String(id);
      tr.dataset.status = String(rule.http_status || '');
      tr.dataset.search = (
        String(rule.from_url || '') + ' ' +
        String(rule.to_url || '') + ' ' +
        String(rule.http_status || '') + ' ' +
        String(rule.status_short || '')
      ).toLowerCase();

      // Column 1: From Path (<th scope="row">)
      var thFrom = document.createElement('th');
      thFrom.setAttribute('scope', 'row');
      thFrom.className = 'rm-cell-from';
      thFrom.dataset.value = String(rule.from_url || '');
      thFrom.appendChild(
        buildUrlCellInner(
          String(rule.from_url || ''),
          safeHref(rule.test_url || rule.from_url),
          fromId,
          'Copy source path ' + String(rule.from_url || '')
        )
      );
      if (rule.link_state && rule.link_state !== 'active') {
        var warnBadge = document.createElement('span');
        warnBadge.className = 'rm-route-warn-badge';
        warnBadge.textContent = rule.link_state === 'superseded'
          ? 'Route overridden (re-save to reactivate)'
          : 'Route sync pending (save to activate)';
        thFrom.appendChild(warnBadge);
      }

      // Column 2: Redirect To
      var tdTo = document.createElement('td');
      tdTo.className = 'rm-cell-to';
      tdTo.dataset.value = String(rule.to_url || '');
      tdTo.appendChild(
        buildUrlCellInner(
          String(rule.to_url || ''),
          safeHref(rule.to_url),
          toId,
          'Copy destination URL ' + String(rule.to_url || '')
        )
      );

      // Column 3: HTTP Status
      var tdStatus = document.createElement('td');
      tdStatus.className = 'rm-cell-status';
      tdStatus.dataset.value = String(parseInt(rule.http_status, 10) || 0);
      tdStatus.appendChild(buildStatusBadge(rule));

      // Column 4: Updated
      var tdUpdated = document.createElement('td');
      tdUpdated.className = 'rm-cell-updated';
      tdUpdated.dataset.value = String(dateInfo.epoch);
      var timeEl = document.createElement('time');
      timeEl.className = 'rm-updated-time';
      if (dateInfo.iso) {
        timeEl.setAttribute('datetime', dateInfo.iso);
      }
      timeEl.textContent = dateInfo.display;
      tdUpdated.appendChild(timeEl);

      // Column 5: Actions
      var tdActions = document.createElement('td');
      tdActions.className = 'rm-cell-actions';
      var actionWrap = document.createElement('div');
      actionWrap.className = 'rm-row-actions';

      var testHref = safeHref(rule.test_url || rule.from_url);
      if (testHref) {
        var testLink = document.createElement('a');
        testLink.className = 'rm-btn-row rm-btn-test rm-icon-start';
        testLink.href = testHref;
        testLink.target = '_blank';
        testLink.rel = 'noopener noreferrer';
        testLink.appendChild(document.createTextNode('Test'));
        var testContext = document.createElement('span');
        testContext.className = 'rm-visually-hidden';
        testContext.textContent = ' ' + String(rule.from_url || '');
        testLink.appendChild(testContext);
        actionWrap.appendChild(testLink);
      }

      var editBtn = document.createElement('button');
      editBtn.type = 'button';
      editBtn.className = 'rm-btn-row rm-action-edit rm-icon-start';
      editBtn.dataset.action = 'edit';
      editBtn.dataset.id = String(id);
      editBtn.appendChild(document.createTextNode('Edit'));
      var editContext = document.createElement('span');
      editContext.className = 'rm-visually-hidden';
      editContext.textContent = ' ' + String(rule.from_url || '');
      editBtn.appendChild(editContext);

      var delBtn = document.createElement('button');
      delBtn.type = 'button';
      delBtn.className = 'rm-btn-row rm-action-delete rm-icon-start';
      delBtn.dataset.action = 'delete';
      delBtn.dataset.id = String(id);
      delBtn.appendChild(document.createTextNode('Delete'));
      var delContext = document.createElement('span');
      delContext.className = 'rm-visually-hidden';
      delContext.textContent = ' ' + String(rule.from_url || '');
      delBtn.appendChild(delContext);

      actionWrap.append(editBtn, delBtn);
      tdActions.appendChild(actionWrap);

      tr.replaceChildren(thFrom, tdTo, tdStatus, tdUpdated, tdActions);
      return tr;
    }

    function createRowElement(rule) {
      var tr = document.createElement('tr');
      populateRowElement(tr, rule);
      return tr;
    }

    // -----------------------------------------------------------------------
    // Sort & Reorder Existing Rows (moveBefore + View Transitions)
    // -----------------------------------------------------------------------
    function compareRows(a, b, colIndex, type, direction) {
      var factor = direction === 'ascending' ? 1 : -1;
      var cellA = a.cells[colIndex];
      var cellB = b.cells[colIndex];
      var rawA = cellA ? (cellA.dataset.value !== undefined ? cellA.dataset.value : cellA.textContent.trim()) : '';
      var rawB = cellB ? (cellB.dataset.value !== undefined ? cellB.dataset.value : cellB.textContent.trim()) : '';

      if (type === 'number') {
        var numA = Number(rawA) || 0;
        var numB = Number(rawB) || 0;
        return factor * (numA - numB);
      }
      if (textCollator) {
        return factor * textCollator.compare(String(rawA), String(rawB));
      }
      return factor * String(rawA).localeCompare(String(rawB));
    }

    function moveNodesIntoOrder(container, orderedRows) {
      var move = Object.hasOwn(Element.prototype, 'moveBefore')
        ? function (node) { container.moveBefore(node, null); }
        : function (node) { container.append(node); };
      for (var i = 0; i < orderedRows.length; i++) {
        move(orderedRows[i]);
      }
    }

    function reorderWithTransition(orderedRows) {
      if (!els.tbody || orderedRows.length < 2) return;
      var update = function () { moveNodesIntoOrder(els.tbody, orderedRows); };

      // Skip transitions on large lists (> 250 items) to avoid capturing hundreds of snapshots
      if (orderedRows.length > 250) {
        update();
        return;
      }

      if (els.table && typeof els.table.startViewTransition === 'function') {
        els.table.startViewTransition(update);
      } else if (typeof document.startViewTransition === 'function') {
        document.startViewTransition(update);
      } else {
        update();
      }
    }

    function applyCurrentSort(animate, announceLabel) {
      if (!els.tbody) return;
      var rows = Array.prototype.slice.call(els.tbody.rows).filter(function (r) {
        return !r.classList.contains('rm-table-status-row');
      });
      if (!rows.length) return;

      rows.sort(function (a, b) {
        return compareRows(a, b, state.sortCol, state.sortType, state.sortDir);
      });

      if (animate) {
        reorderWithTransition(rows);
      } else {
        moveNodesIntoOrder(els.tbody, rows);
      }

      if (announceLabel && els.sortStatus) {
        var visibleCount = rows.filter(function (r) { return !r.hidden; }).length;
        els.sortStatus.textContent = 'Sorted by ' + announceLabel + ', ' + state.sortDir + ', ' + visibleCount + ' rows';
      }
    }

    function handleSortActivation(triggerBtn) {
      if (!triggerBtn || !els.table || !els.table.tHead) return;
      var th = triggerBtn.closest('th');
      if (!th) return;

      var direction = th.getAttribute('aria-sort') === 'ascending' ? 'descending' : 'ascending';
      var headers = els.table.tHead.rows[0].cells;
      for (var i = 0; i < headers.length; i++) {
        headers[i].removeAttribute('aria-sort');
      }
      th.setAttribute('aria-sort', direction);

      state.sortCol = th.cellIndex;
      state.sortType = th.dataset.type || 'text';
      state.sortDir = direction;

      applyCurrentSort(true, triggerBtn.textContent.trim());
    }

    if (els.table) {
      els.table.addEventListener('command', function (event) {
        if (event.command !== '--sort') return;
        handleSortActivation(event.source);
      });

      if (!('CommandEvent' in window)) {
        els.table.addEventListener('click', function (event) {
          var btn = event.target.closest('button[command="--sort"]');
          if (!btn) return;
          handleSortActivation(btn);
        });
      }
    }

    // -----------------------------------------------------------------------
    // Copy Button Handling (Invoker Commands + Fallback + VoiceOver Dedup)
    // -----------------------------------------------------------------------
    var copyTimers = new WeakMap();
    var lastCopyMessage = '';

    function legacyCopy(text) {
      var field = document.createElement('textarea');
      field.value = text;
      field.setAttribute('readonly', '');
      field.className = 'rm-copy-legacy-field';
      document.body.append(field);
      field.select();
      try {
        return document.execCommand('copy');
      } catch (err) {
        return false;
      } finally {
        field.remove();
      }
    }

    function reportCopy(button, ok) {
      var tip = button.nextElementSibling;
      clearTimeout(copyTimers.get(button));
      button.removeAttribute('data-copied');
      button.removeAttribute('data-failed');

      var message = ok ? 'Copied' : 'Copy failed';
      if (message === lastCopyMessage) {
        message += '\u00A0';
      }
      lastCopyMessage = message;

      if (tip) {
        tip.textContent = '';
      }
      void button.offsetWidth;
      button.setAttribute(ok ? 'data-copied' : 'data-failed', '');
      if (tip) {
        requestAnimationFrame(function () {
          tip.textContent = message;
        });
      }

      copyTimers.set(button, setTimeout(function () {
        button.removeAttribute('data-copied');
        button.removeAttribute('data-failed');
        if (tip) {
          tip.textContent = '';
        }
      }, 2000));
    }

    async function performCopy(button, target) {
      if (!button || !target) return;
      var text = target.matches('input, textarea')
        ? target.value
        : (target.innerText || target.textContent || '').trim();
      if (!text) return;

      try {
        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
          await navigator.clipboard.writeText(text);
          reportCopy(button, true);
        } else if (legacyCopy(text)) {
          reportCopy(button, true);
        } else {
          reportCopy(button, false);
        }
      } catch (err) {
        if (legacyCopy(text)) {
          reportCopy(button, true);
        } else {
          reportCopy(button, false);
        }
      }
    }

    document.addEventListener('command', function (event) {
      if (!event.command || event.command.indexOf('--copy') !== 0) return;
      if (!els.root.contains(event.source)) return;
      performCopy(event.source, event.target);
    }, true);

    if (!('CommandEvent' in window)) {
      els.root.addEventListener('click', function (event) {
        var btn = event.target.closest('button[command^="--copy"]');
        if (!btn) return;
        var targetId = btn.getAttribute('commandfor');
        var target = targetId ? document.getElementById(targetId) : null;
        if (target) {
          performCopy(btn, target);
        }
      });
    }

    // -----------------------------------------------------------------------
    // Filtering & Empty State
    // -----------------------------------------------------------------------
    function applyFilters(announceChange) {
      var q = String(state.filterQuery || '').trim().toLowerCase();
      var st = normalizeFilterStatus(state.filterStatus);
      var totalCount = state.items.length;
      var visibleCount = 0;

      state.rowsById.forEach(function (tr) {
        var matchesStatus = (st === 'all') || (tr.dataset.status === st);
        var matchesQuery = !q || ((tr.dataset.search || '').indexOf(q) !== -1);
        var visible = matchesStatus && matchesQuery;
        tr.hidden = !visible;
        if (visible) {
          visibleCount++;
        }
      });

      var isFiltered = (st !== 'all') || (q !== '');
      if (els.clearFilterBtn) {
        els.clearFilterBtn.hidden = !isFiltered;
      }

      if (els.countBadge) {
        if (isFiltered) {
          els.countBadge.textContent = visibleCount + ' of ' + totalCount + (totalCount === 1 ? ' rule' : ' rules');
        } else {
          els.countBadge.textContent = totalCount + (totalCount === 1 ? ' rule' : ' rules');
        }
      }

      if (!state.isLoading) {
        var hasVisible = visibleCount > 0;
        if (els.tableWrapper) {
          els.tableWrapper.hidden = !hasVisible;
        }
        if (els.emptyState) {
          els.emptyState.hidden = hasVisible;
          if (!hasVisible) {
            if (isFiltered) {
              if (els.emptyTitle) els.emptyTitle.textContent = 'No matching redirection rules';
              if (els.emptyCopy) els.emptyCopy.textContent = 'Adjust your search query or HTTP status filter to view other configured redirects.';
              if (els.emptyResetBtn) els.emptyResetBtn.hidden = false;
            } else {
              if (els.emptyTitle) els.emptyTitle.textContent = 'No redirection rules yet';
              if (els.emptyCopy) els.emptyCopy.textContent = 'Add your first URL redirection rule above to keep retired links pointing to live pages.';
              if (els.emptyResetBtn) els.emptyResetBtn.hidden = true;
            }
          }
        }
      }

      // Re-measure truncated labels newly revealed after filter changes
      if (visibleCount > 0) {
        requestAnimationFrame(function () {
          state.rowsById.forEach(function (tr) {
            if (!tr.hidden) observeRowLabels(tr);
          });
        });
      }

      if (announceChange && els.sortStatus) {
        els.sortStatus.textContent = visibleCount + ' matching redirection ' + (visibleCount === 1 ? 'rule' : 'rules');
      }

      syncFilterUrlState();
    }

    function renderInitialRows(items) {
      state.items = Array.isArray(items) ? items : [];
      state.rowsById.clear();

      var frag = document.createDocumentFragment();
      for (var i = 0; i < state.items.length; i++) {
        var rule = state.items[i];
        var tr = createRowElement(rule);
        state.rowsById.set(Number(rule.id), tr);
        frag.appendChild(tr);
      }

      if (els.tbody) {
        els.tbody.replaceChildren(frag);
      }

      applyCurrentSort(false, '');
      applyFilters(false);

      state.rowsById.forEach(function (tr) {
        if (!tr.hidden) observeRowLabels(tr);
      });
    }

    function resetFilters() {
      state.filterQuery = '';
      state.filterStatus = 'all';
      if (els.searchInput) els.searchInput.value = '';
      if (els.filterStatus) els.filterStatus.value = 'all';
      applyFilters(true);
    }

    // -----------------------------------------------------------------------
    // Native Modal Dialog Helpers & Light-Dismiss Fallback
    // -----------------------------------------------------------------------
    function initDialogBehaviors(dialogEl) {
      if (!dialogEl) return;

      dialogEl.addEventListener('close', function () {
        dialogEl.returnValue = '';
      });

      // Fallback light dismiss where dialog.closedBy is unsupported (e.g. Safari)
      if (!('closedBy' in HTMLDialogElement.prototype) && dialogEl.getAttribute('closedby') === 'any') {
        var pressedOutside = false;
        var isOutside = function (evt) {
          var rect = dialogEl.getBoundingClientRect();
          return evt.clientX < rect.left || evt.clientX > rect.right ||
                 evt.clientY < rect.top || evt.clientY > rect.bottom;
        };
        dialogEl.addEventListener('pointerdown', function (evt) {
          pressedOutside = isOutside(evt);
        });
        dialogEl.addEventListener('pointerup', function (evt) {
          if (pressedOutside && isOutside(evt)) {
            if (typeof dialogEl.requestClose === 'function') {
              dialogEl.requestClose();
            } else {
              dialogEl.close();
            }
          }
          pressedOutside = false;
        });
      }
    }

    initDialogBehaviors(els.editDialog);
    initDialogBehaviors(els.deleteDialog);

    // Cancel interception when async request is active inside Edit/Delete modal
    if (els.editDialog) {
      els.editDialog.addEventListener('cancel', function (event) {
        if (state.isSavingEdit) {
          event.preventDefault();
        }
      });
    }
    if (els.deleteDialog) {
      els.deleteDialog.addEventListener('cancel', function (event) {
        if (state.isDeleting) {
          event.preventDefault();
        }
      });
    }

    // Fallback for close/request-close invokers when browser lacks HTMLButtonElement.commandForElement
    if (!('commandForElement' in HTMLButtonElement.prototype)) {
      els.root.addEventListener('click', function (event) {
        var btn = event.target.closest('button[command="close"], button[command="request-close"]');
        if (!btn) return;
        var targetId = btn.getAttribute('commandfor');
        var dlg = targetId ? document.getElementById(targetId) : null;
        if (!dlg || typeof dlg.close !== 'function') return;
        if (btn.getAttribute('command') === 'request-close') {
          if (typeof dlg.requestClose === 'function') {
            dlg.requestClose();
          } else {
            var cancelEvt = new Event('cancel', { cancelable: true });
            if (dlg.dispatchEvent(cancelEvt)) {
              dlg.close();
            }
          }
        } else {
          dlg.close();
        }
      });
    }

    function openEditModal(id) {
      var rule = null;
      for (var i = 0; i < state.items.length; i++) {
        if (Number(state.items[i].id) === id) {
          rule = state.items[i];
          break;
        }
      }
      if (!rule || !els.editDialog) return;

      resetFormValidation(els.editForm, els.editFromPreview);
      if (els.editServerError) {
        els.editServerError.hidden = true;
        els.editServerError.textContent = '';
      }

      if (els.editId) els.editId.value = String(rule.id);
      if (els.editFrom) els.editFrom.value = String(rule.from_url || '');
      if (els.editTo) els.editTo.value = String(rule.to_url || '');
      if (els.editStatus) {
        var stCode = String(rule.http_status || '301');
        els.editStatus.value = Object.prototype.hasOwnProperty.call(STATUS_CATALOG, stCode) ? stCode : '301';
      }
      updatePathPreview(els.editFrom, els.editFromPreview);

      if (els.editMeta) {
        els.editMeta.replaceChildren();
        var ruleLabel = document.createElement('span');
        var strongId = document.createElement('strong');
        strongId.textContent = 'Rule #' + rule.id;
        ruleLabel.appendChild(strongId);

        var updatedSpan = document.createElement('span');
        updatedSpan.textContent = 'Last updated: ' + formatMetaDateTime(rule.updated_at || rule.created_at);

        els.editMeta.append(ruleLabel, updatedSpan);
      }

      els.editDialog.showModal();
    }

    function openDeleteModal(id, invokerBtn) {
      var rule = null;
      for (var i = 0; i < state.items.length; i++) {
        if (Number(state.items[i].id) === id) {
          rule = state.items[i];
          break;
        }
      }
      if (!rule || !els.deleteDialog) return;

      state.deletingId = Number(rule.id);
      state.lastDeleteInvoker = invokerBtn || null;
      if (els.deleteServerError) {
        els.deleteServerError.hidden = true;
        els.deleteServerError.textContent = '';
      }

      if (els.deleteSummary) {
        els.deleteSummary.replaceChildren();

        var pathLine = document.createElement('div');
        pathLine.className = 'rm-delete-path';

        var fromSpan = document.createElement('span');
        fromSpan.textContent = String(rule.from_url || '');

        var arrowSpan = document.createElement('span');
        arrowSpan.className = 'rm-icon rm-delete-arrow';
        arrowSpan.setAttribute('aria-hidden', 'true');

        var toSpan = document.createElement('span');
        toSpan.textContent = String(rule.to_url || '');

        pathLine.append(fromSpan, arrowSpan, toSpan);

        var statusLine = document.createElement('div');
        statusLine.appendChild(buildStatusBadge(rule));

        els.deleteSummary.append(pathLine, statusLine);
      }

      els.deleteDialog.showModal();
    }

    // -----------------------------------------------------------------------
    // API Transport
    // -----------------------------------------------------------------------
    async function requestApi(method, payload) {
      var opts = {
        method: method,
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json'
        }
      };

      if (payload !== undefined) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(payload);
      }

      var response = await fetch(apiUrl, opts);
      var json = null;
      try {
        json = await response.json();
      } catch (err) {
        json = null;
      }

      if (!response.ok || !json || json.status === 'error') {
        var errMsg = (json && (json.message || json.msg)) || ('Request failed with HTTP ' + response.status + '.');
        var err = new Error(errMsg);
        err.code = (json && json.code) || 'request_failed';
        err.field = (json && json.field) || '';
        err.httpStatus = response.status;
        throw err;
      }

      return json;
    }

    async function loadRedirections() {
      state.isLoading = true;
      if (els.tableWrapper) els.tableWrapper.hidden = false;
      if (els.emptyState) els.emptyState.hidden = true;

      if (els.tbody) {
        var loadingTr = document.createElement('tr');
        loadingTr.className = 'rm-table-status-row';
        var loadingTd = document.createElement('td');
        loadingTd.colSpan = 5;
        loadingTd.textContent = 'Loading redirection rules...';
        loadingTr.appendChild(loadingTd);
        els.tbody.replaceChildren(loadingTr);
      }

      try {
        var result = await requestApi('GET');
        state.isLoading = false;
        renderInitialRows(result.data || []);
        updateStatsDisplay(result.stats);
      } catch (err) {
        state.isLoading = false;
        if (els.tbody) {
          var errTr = document.createElement('tr');
          errTr.className = 'rm-table-status-row';
          var errTd = document.createElement('td');
          errTd.colSpan = 5;
          errTd.textContent = 'Unable to load redirection rules.';
          errTr.appendChild(errTd);
          els.tbody.replaceChildren(errTr);
        }
        showAlert(err.message || 'Unable to load redirection rules.', 'error');
      }
    }

    // -----------------------------------------------------------------------
    // Form Submissions (Create / Edit / Delete / Search)
    // -----------------------------------------------------------------------
    wireValidationOnForm(els.addForm, els.addFromPreview, els.addFrom, els.addTo);
    wireValidationOnForm(els.editForm, els.editFromPreview, els.editFrom, els.editTo, function () {
      if (els.editServerError) {
        els.editServerError.hidden = true;
        els.editServerError.textContent = '';
      }
    });

    if (els.focusAddBtn && els.addFrom) {
      els.focusAddBtn.addEventListener('click', function () {
        var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (typeof els.addFrom.scrollIntoView === 'function') {
          els.addFrom.scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth', block: 'center' });
        }
        try {
          els.addFrom.focus({ preventScroll: true });
        } catch (err) {
          els.addFrom.focus();
        }
      });
    }

    if (els.alertDismiss) {
      els.alertDismiss.addEventListener('click', hideAlert);
    }

    if (els.addForm) {
      els.addForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        hideAlert();

        var validation = validateFormAll(els.addForm);
        if (!validation.valid) {
          if (validation.firstInvalid) {
            validation.firstInvalid.focus();
          }
          return;
        }

        var fromCheck = normalizeFromClient(els.addFrom.value);
        var toCheck = normalizeToClient(els.addTo.value, fromCheck.value);
        var statusVal = String(els.addStatus.value || '301').trim();

        var origLabel = els.addSubmitBtn ? els.addSubmitBtn.textContent : 'Add Redirection';
        if (els.addSubmitBtn) {
          els.addSubmitBtn.disabled = true;
          els.addSubmitBtn.textContent = 'Adding...';
        }

        try {
          var res = await requestApi('POST', {
            from_url: fromCheck.value,
            to_url: toCheck.value,
            http_status: statusVal
          });

          var created = res.data;
          if (created && created.id) {
            state.items.unshift(created);
            var tr = createRowElement(created);
            state.rowsById.set(Number(created.id), tr);
            if (els.tbody) {
              els.tbody.prepend(tr);
            }
            applyCurrentSort(false, '');
            applyFilters(false);
            observeRowLabels(tr);
          }

          updateStatsDisplay(res.stats);
          els.addForm.reset();
          showAlert(res.message || 'Redirection rule added successfully.', 'success');
        } catch (err) {
          if (err.field === 'from_url') {
            setFieldServerError(els.addFrom, err.message);
            els.addFrom.focus();
          } else if (err.field === 'to_url') {
            setFieldServerError(els.addTo, err.message);
            els.addTo.focus();
          } else if (err.field === 'http_status') {
            setFieldServerError(els.addStatus, err.message);
            els.addStatus.focus();
          } else {
            showAlert(err.message || 'Unable to create redirection rule.', 'error');
          }
        } finally {
          if (els.addSubmitBtn) {
            els.addSubmitBtn.disabled = false;
            els.addSubmitBtn.textContent = origLabel;
          }
        }
      });
    }

    if (els.editForm) {
      els.editForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (els.editServerError) {
          els.editServerError.hidden = true;
          els.editServerError.textContent = '';
        }

        var validation = validateFormAll(els.editForm);
        if (!validation.valid) {
          if (validation.firstInvalid) {
            validation.firstInvalid.focus();
          }
          return;
        }

        var idNum = parseInt(els.editId ? els.editId.value : '0', 10) || 0;
        var fromCheck = normalizeFromClient(els.editFrom.value);
        var toCheck = normalizeToClient(els.editTo.value, fromCheck.value);
        var statusVal = String(els.editStatus.value || '301').trim();

        var origLabel = els.editSaveBtn ? els.editSaveBtn.textContent : 'Save Changes';
        state.isSavingEdit = true;
        if (els.editSaveBtn) {
          els.editSaveBtn.disabled = true;
          els.editSaveBtn.textContent = 'Saving...';
        }

        try {
          var res = await requestApi('PUT', {
            id: idNum,
            from_url: fromCheck.value,
            to_url: toCheck.value,
            http_status: statusVal
          });

          var updated = res.data;
          var editedRow = null;
          if (updated && updated.id) {
            for (var i = 0; i < state.items.length; i++) {
              if (Number(state.items[i].id) === Number(updated.id)) {
                state.items[i] = updated;
                break;
              }
            }
            var existingRow = state.rowsById.get(Number(updated.id));
            if (existingRow) {
              populateRowElement(existingRow, updated);
            } else {
              existingRow = createRowElement(updated);
              state.rowsById.set(Number(updated.id), existingRow);
              if (els.tbody) els.tbody.prepend(existingRow);
            }
            editedRow = existingRow;
            applyCurrentSort(false, '');
            applyFilters(false);
            observeRowLabels(existingRow);
          }

          updateStatsDisplay(res.stats);
          if (els.editDialog && els.editDialog.open) {
            els.editDialog.close('saved');
          }
          showAlert(res.message || 'Redirection rule updated successfully.', 'success');

          if (editedRow && !editedRow.hidden) {
            var restoredEditBtn = editedRow.querySelector('.rm-action-edit');
            if (restoredEditBtn) restoredEditBtn.focus();
          } else if (els.emptyResetBtn && !els.emptyResetBtn.hidden) {
            els.emptyResetBtn.focus();
          } else if (els.filterStatus) {
            els.filterStatus.focus();
          }
        } catch (err) {
          if (err.field === 'from_url') {
            setFieldServerError(els.editFrom, err.message);
            els.editFrom.focus();
          } else if (err.field === 'to_url') {
            setFieldServerError(els.editTo, err.message);
            els.editTo.focus();
          } else if (err.field === 'http_status') {
            setFieldServerError(els.editStatus, err.message);
            els.editStatus.focus();
          } else if (els.editServerError) {
            els.editServerError.textContent = err.message || 'Unable to update redirection rule.';
            els.editServerError.hidden = false;
          }
        } finally {
          state.isSavingEdit = false;
          if (els.editSaveBtn) {
            els.editSaveBtn.disabled = false;
            els.editSaveBtn.textContent = origLabel;
          }
        }
      });
    }

    if (els.deleteConfirmBtn) {
      els.deleteConfirmBtn.addEventListener('click', async function () {
        if (!state.deletingId || state.isDeleting) return;
        if (els.deleteServerError) {
          els.deleteServerError.hidden = true;
          els.deleteServerError.textContent = '';
        }

        var targetId = state.deletingId;
        var origLabel = els.deleteConfirmBtn.textContent;
        state.isDeleting = true;
        els.deleteConfirmBtn.disabled = true;
        els.deleteConfirmBtn.textContent = 'Deleting...';

        try {
          var res = await requestApi('DELETE', { id: targetId });

          state.items = state.items.filter(function (item) {
            return Number(item.id) !== targetId;
          });
          var rowEl = state.rowsById.get(targetId);
          var prevVisibleRow = null;
          if (rowEl) {
            var sib = rowEl.nextElementSibling;
            while (sib && sib.hidden) sib = sib.nextElementSibling;
            if (!sib) {
              sib = rowEl.previousElementSibling;
              while (sib && sib.hidden) sib = sib.previousElementSibling;
            }
            prevVisibleRow = sib;
            rowEl.remove();
            state.rowsById.delete(targetId);
          }

          updateStatsDisplay(res.stats);
          applyFilters(false);

          if (els.deleteDialog && els.deleteDialog.open) {
            els.deleteDialog.close('deleted');
          }

          showAlert(res.message || 'Redirection rule deleted successfully.', 'success');

          // Move focus cleanly to the nearest remaining row control only when the deleted row's invoker disappeared
          if (prevVisibleRow) {
            var focusTarget = prevVisibleRow.querySelector('.rm-action-edit, .rm-action-delete, a');
            if (focusTarget) focusTarget.focus();
          } else if (els.emptyResetBtn && !els.emptyResetBtn.hidden) {
            els.emptyResetBtn.focus();
          } else if (els.addFrom) {
            els.addFrom.focus();
          }
        } catch (err) {
          if (els.deleteServerError) {
            els.deleteServerError.textContent = err.message || 'Unable to delete redirection rule.';
            els.deleteServerError.hidden = false;
          }
        } finally {
          state.isDeleting = false;
          els.deleteConfirmBtn.disabled = false;
          els.deleteConfirmBtn.textContent = origLabel;
        }
      });
    }

    // -----------------------------------------------------------------------
    // Row Action Delegation (Edit & Delete Buttons)
    // -----------------------------------------------------------------------
    if (els.tbody) {
      els.tbody.addEventListener('click', function (event) {
        var btn = event.target.closest('button[data-action]');
        if (!btn) return;
        var id = parseInt(btn.dataset.id || '0', 10) || 0;
        if (!id) return;

        if (btn.dataset.action === 'edit') {
          openEditModal(id);
        } else if (btn.dataset.action === 'delete') {
          openDeleteModal(id, btn);
        }
      });
    }

    // -----------------------------------------------------------------------
    // Filter & Search Events
    // -----------------------------------------------------------------------
    var searchTimer = null;
    if (els.filterForm) {
      els.filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        state.filterQuery = els.searchInput ? els.searchInput.value.trim() : '';
        state.filterStatus = normalizeFilterStatus(els.filterStatus ? els.filterStatus.value : 'all');
        applyFilters(true);
      });
    }

    if (els.filterStatus) {
      els.filterStatus.addEventListener('change', function () {
        state.filterStatus = normalizeFilterStatus(els.filterStatus.value);
        applyFilters(true);
      });
    }

    if (els.searchInput) {
      els.searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          state.filterQuery = els.searchInput.value.trim();
          applyFilters(true);
        }, 120);
      });
    }

    if (els.clearFilterBtn) {
      els.clearFilterBtn.addEventListener('click', resetFilters);
    }
    if (els.emptyResetBtn) {
      els.emptyResetBtn.addEventListener('click', resetFilters);
    }

    // Initial data load
    loadRedirections();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
