/**
 * Website Themes Switcher & Live Preview Controller
 * Zero inline JS; communicates exclusively via /api/v3/appearance/themes/
 */
(function () {
  'use strict';

  function boot() {
    var root = document.getElementById('themeManagerApp');
    if (!root || root.dataset.tmInitialized === '1') {
      return;
    }
    root.dataset.tmInitialized = '1';

    var apiUrl = root.dataset.apiUrl || '/api/v3/appearance/themes/';
    var initQuery = String(root.dataset.initQ || '').trim();

    var els = {
      root: root,
      announcer: document.getElementById('tmLiveAnnouncer'),
      appBadge: document.getElementById('tmAppBadge'),
      domainBadge: document.getElementById('tmDomainBadge'),
      visitLiveLink: document.getElementById('tmVisitLiveLink'),
      refreshBtn: document.getElementById('tmRefreshBtn'),
      statLiveTitle: document.getElementById('tmStatLiveTitle'),
      statLiveMeta: document.getElementById('tmStatLiveMeta'),
      statTotal: document.getElementById('tmStatTotal'),
      statAppMeta: document.getElementById('tmStatAppMeta'),
      statReady: document.getElementById('tmStatReady'),
      statMissingNote: document.getElementById('tmStatMissingNote'),
      statPreviewTitle: document.getElementById('tmStatPreviewTitle'),
      statPreviewNote: document.getElementById('tmStatPreviewNote'),
      previewBanner: document.getElementById('tmPreviewBanner'),
      previewBannerHeading: document.getElementById('tmPreviewBannerHeading'),
      previewBannerSub: document.getElementById('tmPreviewBannerSub'),
      bannerReopenBtn: document.getElementById('tmBannerReopenBtn'),
      bannerNewTabLink: document.getElementById('tmBannerNewTabLink'),
      bannerApplyBtn: document.getElementById('tmBannerApplyBtn'),
      bannerExitBtn: document.getElementById('tmBannerExitBtn'),
      alertBanner: document.getElementById('tmAlertBanner'),
      alertText: document.getElementById('tmAlertText'),
      alertDismiss: document.getElementById('tmAlertDismiss'),
      catalogCount: document.getElementById('tmCatalogCount'),
      filterForm: document.getElementById('tmFilterForm'),
      searchInput: document.getElementById('tmSearchInput'),
      statusFilter: document.getElementById('tmStatusFilter'),
      clearFilterBtn: document.getElementById('tmClearFilterBtn'),
      grid: document.getElementById('tmThemeGrid'),
      emptyState: document.getElementById('tmEmptyState'),
      emptyTitle: document.getElementById('tmEmptyTitle'),
      emptyCopy: document.getElementById('tmEmptyCopy'),
      emptyResetBtn: document.getElementById('tmEmptyResetBtn'),
      previewDialog: document.getElementById('tmPreviewDialog'),
      previewDialogTitle: document.getElementById('tmPreviewDialogTitle'),
      previewDialogSub: document.getElementById('tmPreviewDialogSub'),
      previewDialogCloseBtn: document.getElementById('tmPreviewDialogCloseBtn'),
      modalNewTabLink: document.getElementById('tmModalNewTabLink'),
      modalExitPreviewBtn: document.getElementById('tmModalExitPreviewBtn'),
      modalApplyBtn: document.getElementById('tmModalApplyBtn'),
      previewStage: document.getElementById('tmPreviewStage'),
      iframeLoading: document.getElementById('tmIframeLoading'),
      previewIframe: document.getElementById('tmPreviewIframe'),
      confirmDialog: document.getElementById('tmConfirmDialog'),
      confirmCloseBtn: document.getElementById('tmConfirmCloseBtn'),
      confirmCompare: document.getElementById('tmConfirmCompare'),
      confirmError: document.getElementById('tmConfirmError'),
      confirmCancelBtn: document.getElementById('tmConfirmCancelBtn'),
      confirmSubmitBtn: document.getElementById('tmConfirmSubmitBtn')
    };

    var state = {
      items: [],
      byId: new Map(),
      site: null,
      preview: { active: false, theme_id: 0, theme_title: '', preview_url: '' },
      stats: null,
      searchQuery: initQuery,
      statusFilter: 'all',
      modalThemeId: 0,
      confirmThemeId: 0,
      isMutating: false
    };

    if (els.searchInput && state.searchQuery !== '') {
      els.searchInput.value = state.searchQuery;
    }

    function announce(message) {
      if (!els.announcer) return;
      els.announcer.textContent = '';
      window.setTimeout(function () {
        if (els.announcer) {
          els.announcer.textContent = message;
        }
      }, 30);
    }

    function showAlert(message, type) {
      if (!els.alertBanner || !els.alertText) return;
      els.alertBanner.classList.remove('tm-alert-success', 'tm-alert-error');
      els.alertBanner.classList.add(type === 'error' ? 'tm-alert-error' : 'tm-alert-success');
      els.alertBanner.setAttribute('role', type === 'error' ? 'alert' : 'status');
      els.alertText.textContent = String(message || '');
      els.alertBanner.hidden = false;
      announce(String(message || ''));
    }

    function hideAlert() {
      if (!els.alertBanner) return;
      els.alertBanner.hidden = true;
      if (els.alertText) els.alertText.textContent = '';
    }

    function apiRequest(method, body) {
      var opts = {
        method: method,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      };
      if (body) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
      }
      return fetch(apiUrl, opts).then(function (resp) {
        return resp.json().catch(function () {
          return { status: 'error', message: 'Invalid JSON response from server.' };
        }).then(function (data) {
          if (!resp.ok || !data || data.status !== 'success') {
            var errMsg = (data && (data.message || data.msg)) || ('Request failed with HTTP ' + resp.status);
            throw new Error(errMsg);
          }
          return data;
        });
      });
    }

    function hydratePayload(data) {
      state.items = Array.isArray(data.data) ? data.data : [];
      state.byId.clear();
      state.items.forEach(function (item) {
        state.byId.set(Number(item.id), item);
      });
      state.site = data.site || null;
      state.preview = data.preview || { active: false, theme_id: 0, theme_title: '', preview_url: '' };
      state.stats = data.stats || null;

      renderHeaderAndStats();
      renderPreviewBanner();
      renderGrid();
    }

    function renderHeaderAndStats() {
      var site = state.site;
      var stats = state.stats;
      var preview = state.preview;

      if (site) {
        if (els.appBadge && site.app_title) {
          els.appBadge.textContent = site.app_title;
        }
        if (els.domainBadge && site.domain) {
          els.domainBadge.textContent = site.domain;
        }
        if (els.visitLiveLink && site.base_url) {
          els.visitLiveLink.href = site.base_url + '/';
        }
        if (els.statLiveTitle) {
          els.statLiveTitle.textContent = site.live_theme_title || 'Default';
        }
        if (els.statLiveMeta) {
          els.statLiveMeta.textContent = site.live_theme_path
            ? ('Code: ' + site.live_theme_path)
            : 'Visible to all visitors';
        }
        if (els.statAppMeta) {
          els.statAppMeta.textContent = 'App: ' + (site.app_title || site.app_path || 'Current');
        }
      }

      if (stats) {
        if (els.statTotal) els.statTotal.textContent = String(stats.total || 0);
        if (els.statReady) els.statReady.textContent = String(stats.ready || 0);
        if (els.statMissingNote) {
          var miss = Number(stats.missing || 0);
          els.statMissingNote.textContent = miss > 0
            ? (miss + ' theme' + (miss === 1 ? '' : 's') + ' missing template files')
            : 'All app templates verified';
        }
      }

      if (els.statPreviewTitle && els.statPreviewNote) {
        if (preview && preview.active && preview.theme_title) {
          els.statPreviewTitle.textContent = preview.theme_title;
          els.statPreviewNote.textContent = 'Highlighted in your browser';
        } else {
          els.statPreviewTitle.textContent = 'Off';
          els.statPreviewNote.textContent = 'Viewing live theme';
        }
      }
    }

    function renderPreviewBanner() {
      var preview = state.preview;
      if (!els.previewBanner) return;

      if (!preview || !preview.active || !preview.theme_id) {
        els.previewBanner.hidden = true;
        return;
      }

      var liveTitle = (state.site && state.site.live_theme_title) ? state.site.live_theme_title : 'current live theme';
      if (els.previewBannerHeading) {
        els.previewBannerHeading.textContent = 'Previewing "' + preview.theme_title + '"';
      }
      if (els.previewBannerSub) {
        els.previewBannerSub.textContent = 'Preview highlight is active on the website for your browser. Live visitors still see "' + liveTitle + '".';
      }
      if (els.bannerNewTabLink) {
        els.bannerNewTabLink.href = preview.preview_url || '/';
      }
      if (els.bannerApplyBtn) {
        var sameAsLive = state.site && Number(preview.theme_id) === Number(state.site.live_theme_id);
        els.bannerApplyBtn.hidden = Boolean(sameAsLive);
      }

      els.previewBanner.hidden = false;
    }

    function getFilteredItems() {
      var q = state.searchQuery.trim().toLowerCase();
      var filter = state.statusFilter;

      return state.items.filter(function (item) {
        if (filter === 'ready' && !item.template_exists) {
          return false;
        }
        if (filter === 'missing' && item.template_exists) {
          return false;
        }
        if (q !== '') {
          var hay = (
            String(item.title || '') + ' ' +
            String(item.path || '') + ' ' +
            String(item.description || '') + ' ' +
            String(item.version || '')
          ).toLowerCase();
          if (hay.indexOf(q) === -1) {
            return false;
          }
        }
        return true;
      });
    }

    function buildCardVisual(item, index) {
      var wrap = document.createElement('div');
      wrap.className = 'tm-card-thumb';

      if (item.image) {
        var img = document.createElement('img');
        img.src = item.image;
        img.alt = item.title + ' preview';
        img.loading = 'lazy';
        img.addEventListener('error', function () {
          if (img.parentNode === wrap) {
            wrap.replaceChild(buildMockupElement(item, index), img);
          }
        });
        wrap.appendChild(img);
      } else {
        wrap.appendChild(buildMockupElement(item, index));
      }

      var badges = document.createElement('div');
      badges.className = 'tm-card-badges';

      var leftGroup = document.createElement('div');
      leftGroup.className = 'tm-thumb-pill-group';

      if (item.is_active) {
        var livePill = document.createElement('span');
        livePill.className = 'tm-thumb-pill tm-pill-live';
        livePill.textContent = 'Live Theme';
        leftGroup.appendChild(livePill);
      }

      if (item.is_previewing) {
        var prevPill = document.createElement('span');
        prevPill.className = 'tm-thumb-pill tm-pill-preview';
        prevPill.textContent = 'Previewing';
        leftGroup.appendChild(prevPill);
      }

      if (!item.template_exists) {
        var missPill = document.createElement('span');
        missPill.className = 'tm-thumb-pill tm-pill-missing';
        missPill.textContent = 'Files Missing';
        leftGroup.appendChild(missPill);
      }

      var verPill = document.createElement('span');
      verPill.className = 'tm-thumb-pill tm-pill-version';
      verPill.textContent = 'v' + (item.version || '1.0');

      badges.append(leftGroup, verPill);
      wrap.appendChild(badges);

      return wrap;
    }

    function buildMockupElement(item, index) {
      var mockup = document.createElement('div');
      mockup.className = 'tm-card-mockup tone-' + String(index % 4);
      mockup.setAttribute('aria-hidden', 'true');

      var browser = document.createElement('div');
      browser.className = 'tm-mockup-browser';

      var chrome = document.createElement('div');
      chrome.className = 'tm-mockup-chrome';

      var dots = document.createElement('span');
      dots.className = 'tm-mockup-dots';
      dots.append(document.createElement('span'), document.createElement('span'), document.createElement('span'));

      var slug = document.createElement('span');
      slug.className = 'tm-mockup-slug';
      slug.textContent = item.path || 'default';

      chrome.append(dots, slug);

      var hero = document.createElement('div');
      hero.className = 'tm-mockup-hero';

      var nameEl = document.createElement('div');
      nameEl.className = 'tm-mockup-name';
      nameEl.textContent = item.title;

      var bars = document.createElement('div');
      bars.className = 'tm-mockup-bars';
      bars.append(document.createElement('span'), document.createElement('span'));

      hero.append(nameEl, bars);
      browser.append(chrome, hero);
      mockup.appendChild(browser);

      return mockup;
    }

    function buildThemeCard(item, index) {
      var card = document.createElement('article');
      card.className = 'tm-card';
      if (item.is_active) card.classList.add('is-live');
      if (item.is_previewing) card.classList.add('is-previewing');
      if (!item.template_exists) card.classList.add('is-missing');
      card.dataset.themeId = String(item.id);

      var visual = buildCardVisual(item, index);

      var body = document.createElement('div');
      body.className = 'tm-card-body';

      var head = document.createElement('div');
      head.className = 'tm-card-head';

      var titleEl = document.createElement('h3');
      titleEl.className = 'tm-card-title';
      titleEl.textContent = item.title;

      var codeEl = document.createElement('code');
      codeEl.className = 'tm-path-code';
      codeEl.textContent = item.path || 'default';

      head.append(titleEl, codeEl);

      var descEl = document.createElement('p');
      descEl.className = 'tm-card-desc';
      descEl.textContent = item.description || ('Storefront theme template for ' + (item.app_title || 'this app') + '.');

      var metaEl = document.createElement('div');
      metaEl.className = 'tm-card-meta';

      var statusSpan = document.createElement('span');
      if (item.template_exists) {
        statusSpan.className = 'tm-meta-status-ok';
        statusSpan.textContent = item.has_mobile ? 'Desktop + Mobile ready' : 'Desktop template ready';
      } else {
        statusSpan.className = 'tm-meta-status-warn';
        statusSpan.textContent = 'Template directory missing on server';
      }

      var idSpan = document.createElement('span');
      idSpan.textContent = 'Theme #' + item.id;

      metaEl.append(statusSpan, idSpan);
      body.append(head, descEl, metaEl);

      var footer = document.createElement('div');
      footer.className = 'tm-card-footer';

      var previewBtn = document.createElement('button');
      previewBtn.type = 'button';
      previewBtn.className = item.is_previewing ? 'tm-btn tm-btn-amber' : 'tm-btn tm-btn-secondary';
      previewBtn.dataset.action = 'preview';
      previewBtn.dataset.themeId = String(item.id);
      previewBtn.disabled = !item.template_exists;
      previewBtn.textContent = item.is_previewing ? 'Inspect Preview' : 'Preview Website';

      var applyBtn = document.createElement('button');
      applyBtn.type = 'button';
      if (item.is_active) {
        applyBtn.className = 'tm-btn tm-btn-live';
        applyBtn.disabled = true;
        applyBtn.textContent = 'Currently Live';
      } else if (!item.template_exists) {
        applyBtn.className = 'tm-btn tm-btn-ghost';
        applyBtn.disabled = true;
        applyBtn.textContent = 'Unavailable';
      } else {
        applyBtn.className = 'tm-btn tm-btn-primary';
        applyBtn.dataset.action = 'apply';
        applyBtn.dataset.themeId = String(item.id);
        applyBtn.textContent = 'Apply Theme';
      }

      footer.append(previewBtn, applyBtn);
      card.append(visual, body, footer);
      return card;
    }

    function renderGrid() {
      var filtered = getFilteredItems();
      if (els.clearFilterBtn) {
        els.clearFilterBtn.hidden = (state.searchQuery.trim() === '' && state.statusFilter === 'all');
      }
      if (els.catalogCount) {
        els.catalogCount.textContent = filtered.length + ' theme' + (filtered.length === 1 ? '' : 's');
      }

      if (!filtered.length) {
        if (els.grid) {
          els.grid.replaceChildren();
          els.grid.hidden = true;
        }
        if (els.emptyState) {
          if (state.items.length === 0) {
            if (els.emptyTitle) els.emptyTitle.textContent = 'No themes configured for this app';
            if (els.emptyCopy) els.emptyCopy.textContent = 'No active themes were found in isw_theme for this application yet.';
            if (els.emptyResetBtn) els.emptyResetBtn.hidden = true;
          } else {
            if (els.emptyTitle) els.emptyTitle.textContent = 'No matching themes found';
            if (els.emptyCopy) els.emptyCopy.textContent = 'Try clearing your search or status filter.';
            if (els.emptyResetBtn) els.emptyResetBtn.hidden = false;
          }
          els.emptyState.hidden = false;
        }
        return;
      }

      if (els.emptyState) els.emptyState.hidden = true;
      if (els.grid) {
        var frag = document.createDocumentFragment();
        filtered.forEach(function (item, idx) {
          frag.appendChild(buildThemeCard(item, idx));
        });
        els.grid.replaceChildren(frag);
        els.grid.hidden = false;
      }
    }

    function setPreviewViewport(mode) {
      if (!els.previewStage) return;
      els.previewStage.classList.remove('tm-vp-desktop', 'tm-vp-tablet', 'tm-vp-mobile');
      els.previewStage.classList.add('tm-vp-' + mode);

      var btns = root.querySelectorAll('.tm-vp-btn');
      btns.forEach(function (btn) {
        var active = btn.dataset.viewport === mode;
        btn.classList.toggle('is-selected', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
    }

    function openPreviewModal(themeItem, previewUrl) {
      if (!els.previewDialog || !themeItem) return;

      state.modalThemeId = Number(themeItem.id);
      if (els.previewDialogTitle) {
        els.previewDialogTitle.textContent = themeItem.title + ' Preview';
      }
      if (els.previewDialogSub) {
        els.previewDialogSub.textContent = themeItem.is_active
          ? 'Previewing the active live theme'
          : 'Isolated browser preview — live visitors remain on "' + ((state.site && state.site.live_theme_title) || 'current theme') + '"';
      }
      if (els.modalNewTabLink) {
        els.modalNewTabLink.href = previewUrl || themeItem.preview_url || '/';
      }
      if (els.modalApplyBtn) {
        els.modalApplyBtn.hidden = Boolean(themeItem.is_active);
        els.modalApplyBtn.disabled = false;
        els.modalApplyBtn.textContent = 'Apply This Theme';
      }

      setPreviewViewport('desktop');

      if (els.iframeLoading) els.iframeLoading.hidden = false;
      if (els.previewIframe) {
        els.previewIframe.src = previewUrl || themeItem.preview_url || '/';
      }

      if (!els.previewDialog.open) {
        els.previewDialog.showModal();
      }
    }

    function closePreviewModal() {
      if (els.previewDialog && els.previewDialog.open) {
        els.previewDialog.close();
      }
      if (els.previewIframe) {
        els.previewIframe.src = 'about:blank';
      }
      state.modalThemeId = 0;
    }

    function openConfirmModal(themeId) {
      var item = state.byId.get(Number(themeId));
      if (!item || !els.confirmDialog) return;

      state.confirmThemeId = Number(item.id);
      if (els.confirmError) {
        els.confirmError.hidden = true;
        els.confirmError.textContent = '';
      }
      if (els.confirmSubmitBtn) {
        els.confirmSubmitBtn.disabled = false;
        els.confirmSubmitBtn.textContent = 'Confirm & Switch Theme';
      }

      if (els.confirmCompare) {
        var liveName = (state.site && state.site.live_theme_title) ? state.site.live_theme_title : 'Current Theme';
        var r1 = document.createElement('div');
        r1.className = 'tm-confirm-row';
        var k1 = document.createElement('span');
        k1.textContent = 'Current Live Theme';
        var v1 = document.createElement('strong');
        v1.textContent = liveName;
        r1.append(k1, v1);

        var r2 = document.createElement('div');
        r2.className = 'tm-confirm-row';
        var k2 = document.createElement('span');
        k2.textContent = 'New Live Theme';
        var v2 = document.createElement('strong');
        v2.textContent = item.title + ' (' + (item.path || 'default') + ')';
        r2.append(k2, v2);

        els.confirmCompare.replaceChildren(r1, r2);
      }

      if (!els.confirmDialog.open) {
        els.confirmDialog.showModal();
      }
    }

    function closeConfirmModal() {
      if (els.confirmDialog && els.confirmDialog.open) {
        els.confirmDialog.close();
      }
      state.confirmThemeId = 0;
    }

    function startPreview(themeId) {
      if (state.isMutating) return;
      state.isMutating = true;
      hideAlert();

      apiRequest('POST', { action: 'preview', theme_id: Number(themeId) })
        .then(function (data) {
          hydratePayload(data);
          var item = state.byId.get(Number(themeId));
          openPreviewModal(item, data.preview_url || (item && item.preview_url));
        })
        .catch(function (err) {
          showAlert(err.message || 'Unable to start preview.', 'error');
        })
        .finally(function () {
          state.isMutating = false;
        });
    }

    function exitPreview() {
      if (state.isMutating) return;
      state.isMutating = true;
      hideAlert();

      apiRequest('POST', { action: 'exit_preview' })
        .then(function (data) {
          closePreviewModal();
          hydratePayload(data);
          showAlert(data.message || 'Exited preview mode.', 'success');
        })
        .catch(function (err) {
          showAlert(err.message || 'Unable to exit preview mode.', 'error');
        })
        .finally(function () {
          state.isMutating = false;
        });
    }

    function confirmApplyTheme() {
      var targetId = Number(state.confirmThemeId);
      if (!targetId || state.isMutating) return;
      state.isMutating = true;

      if (els.confirmError) {
        els.confirmError.hidden = true;
        els.confirmError.textContent = '';
      }
      if (els.confirmSubmitBtn) {
        els.confirmSubmitBtn.disabled = true;
        els.confirmSubmitBtn.textContent = 'Applying...';
      }

      apiRequest('POST', { action: 'apply', theme_id: targetId })
        .then(function (data) {
          closeConfirmModal();
          closePreviewModal();
          hydratePayload(data);
          showAlert(data.message || 'Website theme switched successfully.', 'success');
        })
        .catch(function (err) {
          if (els.confirmError) {
            els.confirmError.textContent = err.message || 'Failed to switch website theme.';
            els.confirmError.hidden = false;
          }
          if (els.confirmSubmitBtn) {
            els.confirmSubmitBtn.disabled = false;
            els.confirmSubmitBtn.textContent = 'Confirm & Switch Theme';
          }
        })
        .finally(function () {
          state.isMutating = false;
        });
    }

    function loadCatalog() {
      hideAlert();
      apiRequest('GET', null)
        .then(function (data) {
          hydratePayload(data);
        })
        .catch(function (err) {
          if (els.grid) {
            els.grid.replaceChildren();
            els.grid.hidden = true;
          }
          if (els.emptyState) {
            if (els.emptyTitle) els.emptyTitle.textContent = 'Could not load themes';
            if (els.emptyCopy) els.emptyCopy.textContent = err.message || 'Please refresh and try again.';
            if (els.emptyResetBtn) els.emptyResetBtn.hidden = true;
            els.emptyState.hidden = false;
          }
          showAlert(err.message || 'Failed to load themes.', 'error');
        });
    }

    // Event wiring (100% external JS, zero inline handlers)
    if (els.alertDismiss) {
      els.alertDismiss.addEventListener('click', hideAlert);
    }
    if (els.refreshBtn) {
      els.refreshBtn.addEventListener('click', loadCatalog);
    }
    if (els.filterForm) {
      els.filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        renderGrid();
      });
    }
    if (els.searchInput) {
      els.searchInput.addEventListener('input', function () {
        state.searchQuery = els.searchInput.value;
        renderGrid();
      });
    }
    if (els.statusFilter) {
      els.statusFilter.addEventListener('change', function () {
        state.statusFilter = els.statusFilter.value || 'all';
        renderGrid();
      });
    }
    function resetFilters() {
      state.searchQuery = '';
      state.statusFilter = 'all';
      if (els.searchInput) els.searchInput.value = '';
      if (els.statusFilter) els.statusFilter.value = 'all';
      renderGrid();
    }
    if (els.clearFilterBtn) {
      els.clearFilterBtn.addEventListener('click', resetFilters);
    }
    if (els.emptyResetBtn) {
      els.emptyResetBtn.addEventListener('click', resetFilters);
    }

    if (els.grid) {
      els.grid.addEventListener('click', function (event) {
        var btn = event.target.closest('button[data-action][data-theme-id]');
        if (!btn) return;
        var tid = Number(btn.dataset.themeId || 0);
        if (tid <= 0) return;
        if (btn.dataset.action === 'preview') {
          startPreview(tid);
        } else if (btn.dataset.action === 'apply') {
          openConfirmModal(tid);
        }
      });
    }

    if (els.bannerReopenBtn) {
      els.bannerReopenBtn.addEventListener('click', function () {
        if (state.preview && state.preview.theme_id) {
          var item = state.byId.get(Number(state.preview.theme_id));
          if (item) {
            openPreviewModal(item, state.preview.preview_url);
          }
        }
      });
    }
    if (els.bannerApplyBtn) {
      els.bannerApplyBtn.addEventListener('click', function () {
        if (state.preview && state.preview.theme_id) {
          openConfirmModal(state.preview.theme_id);
        }
      });
    }
    if (els.bannerExitBtn) {
      els.bannerExitBtn.addEventListener('click', exitPreview);
    }

    if (els.previewIframe) {
      els.previewIframe.addEventListener('load', function () {
        if (els.iframeLoading) {
          els.iframeLoading.hidden = true;
        }
      });
    }

    root.querySelectorAll('.tm-vp-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        setPreviewViewport(btn.dataset.viewport || 'desktop');
      });
    });

    if (els.previewDialogCloseBtn) {
      els.previewDialogCloseBtn.addEventListener('click', closePreviewModal);
    }
    if (els.modalExitPreviewBtn) {
      els.modalExitPreviewBtn.addEventListener('click', exitPreview);
    }
    if (els.modalApplyBtn) {
      els.modalApplyBtn.addEventListener('click', function () {
        if (state.modalThemeId > 0) {
          openConfirmModal(state.modalThemeId);
        }
      });
    }

    if (els.confirmCloseBtn) {
      els.confirmCloseBtn.addEventListener('click', closeConfirmModal);
    }
    if (els.confirmCancelBtn) {
      els.confirmCancelBtn.addEventListener('click', closeConfirmModal);
    }
    if (els.confirmSubmitBtn) {
      els.confirmSubmitBtn.addEventListener('click', confirmApplyTheme);
    }

    [els.previewDialog, els.confirmDialog].forEach(function (dlg) {
      if (!dlg) return;
      dlg.addEventListener('close', function () {
        dlg.returnValue = '';
        if (dlg === els.previewDialog && els.previewIframe) {
          els.previewIframe.src = 'about:blank';
        }
      });
      dlg.addEventListener('click', function (event) {
        if (event.target === dlg) {
          if (dlg === els.previewDialog) closePreviewModal();
          else closeConfirmModal();
        }
      });
    });

    window.addEventListener('message', function (event) {
      if (event.origin !== window.location.origin) return;
      if (!event.data || typeof event.data.type !== 'string') return;
      if (event.data.type === 'isw-theme-preview:applied') {
        closePreviewModal();
        loadCatalog();
        showAlert('Theme switched to "' + (event.data.themeTitle || 'selected theme') + '".', 'success');
      } else if (event.data.type === 'isw-theme-preview:exit') {
        closePreviewModal();
        loadCatalog();
        showAlert('Website preview mode ended.', 'success');
      }
    });

    loadCatalog();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

