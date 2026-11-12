/**
 * Website Theme Preview Control Bar Controller
 * Enhances the storefront preview highlight and enables Apply / Exit actions without reload leaks.
 */
(function () {
  'use strict';

  function initThemePreviewBar() {
    var bar = document.getElementById('iswThemePreviewBar');
    if (!bar || bar.dataset.tpbInit === '1') {
      return;
    }
    bar.dataset.tpbInit = '1';

    if (document.title && document.title.indexOf('[PREVIEW]') !== 0) {
      document.title = '[PREVIEW] ' + document.title;
    }

    var inFrame = false;
    try {
      inFrame = (window.self !== window.top);
    } catch (err) {
      inFrame = true;
    }
    if (inFrame) {
      bar.classList.add('isw-tpb-in-frame');
    }

    var applyBtn = document.getElementById('iswTpbApplyBtn');
    var exitLink = document.getElementById('iswTpbExitLink');
    var toggleBtn = document.getElementById('iswTpbToggleBtn');
    var noteEl = document.getElementById('iswTpbNote');

    var apiUrl = bar.dataset.apiUrl || '/api/v3/appearance/themes/';
    var themeId = Number(bar.dataset.themeId || 0);
    var themeTitle = bar.dataset.themeTitle || 'Preview Theme';
    var cleanUrl = bar.dataset.cleanUrl || '/';
    var storageKey = 'isw_tpb_collapsed';

    function setNote(text, status) {
      if (!noteEl) return;
      noteEl.textContent = text;
      noteEl.classList.remove('is-error', 'is-success');
      if (status === 'error') noteEl.classList.add('is-error');
      if (status === 'success') noteEl.classList.add('is-success');
    }

    function applyCollapseState(collapsed) {
      bar.classList.toggle('is-collapsed', collapsed);
      if (toggleBtn) {
        toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        toggleBtn.setAttribute('aria-label', collapsed ? 'Expand preview bar' : 'Minimize preview bar');
        toggleBtn.title = collapsed ? 'Expand preview bar' : 'Minimize preview bar';
        toggleBtn.innerHTML = collapsed ? '&#43;' : '&#8722;';
      }
      try {
        window.sessionStorage.setItem(storageKey, collapsed ? '1' : '0');
      } catch (err) {
        // Ignore storage errors
      }
    }

    try {
      if (window.sessionStorage.getItem(storageKey) === '1') {
        applyCollapseState(true);
      }
    } catch (err) {
      // Ignore storage errors
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        applyCollapseState(!bar.classList.contains('is-collapsed'));
      });
    }

    if (exitLink) {
      exitLink.addEventListener('click', function (event) {
        if (!inFrame) {
          return;
        }
        event.preventDefault();
        fetch(apiUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ action: 'exit_preview' })
        }).catch(function () {
          return null;
        }).finally(function () {
          try {
            window.parent.postMessage({
              type: 'isw-theme-preview:exit',
              themeId: themeId
            }, window.location.origin);
          } catch (err) {
            window.location.href = bar.dataset.exitUrl || cleanUrl;
          }
        });
      });
    }

    if (applyBtn && themeId > 0) {
      applyBtn.addEventListener('click', function () {
        applyBtn.disabled = true;
        var prevLabel = applyBtn.textContent;
        applyBtn.textContent = 'Applying...';
        setNote('Activating "' + themeTitle + '" for your live website...', '');

        fetch(apiUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            action: 'apply',
            theme_id: themeId
          })
        }).then(function (resp) {
          return resp.json().catch(function () {
            return { status: 'error', message: 'Invalid response from server.' };
          });
        }).then(function (data) {
          if (!data || data.status !== 'success') {
            throw new Error((data && (data.message || data.msg)) || 'Could not apply theme.');
          }
          setNote('"' + themeTitle + '" is now live!', 'success');
          if (inFrame) {
            try {
              window.parent.postMessage({
                type: 'isw-theme-preview:applied',
                themeId: themeId,
                themeTitle: themeTitle
              }, window.location.origin);
            } catch (err) {
              // fallback reload below
            }
          }
          window.setTimeout(function () {
            window.location.href = cleanUrl || '/';
          }, 450);
        }).catch(function (err) {
          applyBtn.disabled = false;
          applyBtn.textContent = prevLabel;
          setNote((err && err.message) ? err.message : 'Unable to apply theme.', 'error');
        });
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemePreviewBar);
  } else {
    initThemePreviewBar();
  }
})();

