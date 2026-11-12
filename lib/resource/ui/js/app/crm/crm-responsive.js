(function () {
    'use strict';

    var drawerQuery = window.matchMedia('(max-width: 1024px)');
    var body = document.body;
    var aside = document.querySelector('aside');
    var toggleBtn = document.getElementById('nav-toggle');
    var overlay = document.getElementById('nav-overlay');
    var mainEl = document.querySelector('main');
    var footerEl = document.querySelector('footer');
    var drawerCloseBtn = null;

    function isDrawerMode() {
        return drawerQuery.matches;
    }

    function setBackgroundInert(isInert) {
        if (mainEl && 'inert' in mainEl) {
            mainEl.inert = isInert;
        }
        if (footerEl && 'inert' in footerEl) {
            footerEl.inert = isInert;
        }
    }

    function syncDrawerSemantics() {
        if (!aside) {
            return;
        }
        if (isDrawerMode()) {
            aside.setAttribute('role', 'dialog');
            aside.setAttribute('aria-label', 'Navigation menu');
            if (body.classList.contains('sidebar-open')) {
                aside.setAttribute('aria-modal', 'true');
            } else {
                aside.removeAttribute('aria-modal');
            }
        } else {
            aside.removeAttribute('role');
            aside.removeAttribute('aria-modal');
            aside.removeAttribute('aria-label');
        }
    }

    function openDrawer() {
        if (!isDrawerMode() || !aside) {
            return;
        }
        body.classList.add('sidebar-open', 'crm-nav-locked');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'true');
        }
        aside.setAttribute('aria-modal', 'true');
        setBackgroundInert(true);
        if (drawerCloseBtn) {
            drawerCloseBtn.focus();
        }
    }

    function closeDrawer(restoreFocus) {
        if (!body.classList.contains('sidebar-open') && !body.classList.contains('crm-nav-locked')) {
            return;
        }
        body.classList.remove('sidebar-open', 'crm-nav-locked');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
        if (aside) {
            aside.removeAttribute('aria-modal');
        }
        setBackgroundInert(false);
        if (restoreFocus && toggleBtn && isDrawerMode()) {
            toggleBtn.focus();
        }
    }

    function initNavigationDrawer() {
        if (aside && !aside.querySelector('.crm-drawer-top')) {
            var topBar = document.createElement('div');
            topBar.className = 'crm-drawer-top';

            var label = document.createElement('span');
            label.className = 'crm-drawer-label';
            label.textContent = 'Navigation';

            drawerCloseBtn = document.createElement('button');
            drawerCloseBtn.type = 'button';
            drawerCloseBtn.className = 'crm-drawer-close';
            drawerCloseBtn.setAttribute('aria-label', 'Close navigation');
            drawerCloseBtn.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';

            drawerCloseBtn.addEventListener('click', function () {
                closeDrawer(true);
            });

            topBar.appendChild(label);
            topBar.appendChild(drawerCloseBtn);
            aside.insertBefore(topBar, aside.firstChild);
        } else if (aside) {
            drawerCloseBtn = aside.querySelector('.crm-drawer-close');
        }

        if (toggleBtn && !toggleBtn.dataset.crmDrawerBound) {
            toggleBtn.dataset.crmDrawerBound = '1';
            toggleBtn.setAttribute('aria-expanded', 'false');

            toggleBtn.addEventListener('click', function () {
                if (!isDrawerMode()) {
                    return;
                }
                if (body.classList.contains('sidebar-open')) {
                    closeDrawer(true);
                } else {
                    openDrawer();
                }
            });
        }

        if (overlay && !overlay.dataset.crmDrawerBound) {
            overlay.dataset.crmDrawerBound = '1';
            overlay.addEventListener('click', function () {
                closeDrawer(true);
            });
        }

        syncDrawerSemantics();

        var onMediaChange = function () {
            if (!isDrawerMode()) {
                closeDrawer(false);
            }
            syncDrawerSemantics();
        };

        if (typeof drawerQuery.addEventListener === 'function') {
            drawerQuery.addEventListener('change', onMediaChange);
        } else {
            window.addEventListener('resize', onMediaChange);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                if (body.classList.contains('sidebar-open')) {
                    closeDrawer(true);
                }
                closeAllProfileMenus();
            }
        });

        document.addEventListener('click', function (event) {
            if (body.classList.contains('sidebar-open') && isDrawerMode()) {
                var inAside = aside && aside.contains(event.target);
                var onToggle = toggleBtn && toggleBtn.contains(event.target);
                if (!inAside && !onToggle) {
                    closeDrawer(false);
                }
            }
        });
    }

    function initSubmenuAccordions() {
        var menuItems = document.querySelectorAll('nav.side-nav > ul > li');
        for (var i = 0; i < menuItems.length; i++) {
            (function (item) {
                if (item.dataset.crmSubmenuInit) {
                    return;
                }
                var topAnchor = item.firstElementChild;
                while (topAnchor && topAnchor.tagName !== 'A') {
                    topAnchor = topAnchor.nextElementSibling;
                }
                var hasSub = item.querySelector('.side-menu li, .bottom-menu li');
                if (!topAnchor || !hasSub) {
                    return;
                }

                item.dataset.crmSubmenuInit = '1';
                item.classList.add('has-submenu');

                var shouldOpenByDefault = item.classList.contains('active') && isDrawerMode();
                if (shouldOpenByDefault) {
                    item.classList.add('is-submenu-open');
                }

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'crm-submenu-toggle';
                btn.setAttribute('aria-expanded', shouldOpenByDefault ? 'true' : 'false');
                btn.setAttribute('aria-label', 'Toggle ' + (topAnchor.textContent || 'menu').trim() + ' submenu');
                btn.innerHTML = '<i class="fas fa-chevron-down" aria-hidden="true"></i>';

                btn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var nextOpen = !item.classList.contains('is-submenu-open');
                    item.classList.toggle('is-submenu-open', nextOpen);
                    btn.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
                });

                item.insertBefore(btn, topAnchor.nextSibling);
            })(menuItems[i]);
        }

        var searchInput = document.getElementById('side-nav-search');
        if (searchInput && !searchInput.dataset.crmSearchSubBound) {
            searchInput.dataset.crmSearchSubBound = '1';
            var menuRoot = document.querySelector('nav.side-nav > ul');
            var topItems = menuRoot ? menuRoot.children : [];

            searchInput.addEventListener('input', function () {
                var term = searchInput.value.toLowerCase().trim();

                for (var idx = 0; idx < topItems.length; idx++) {
                    var topItem = topItems[idx];
                    var anchor = topItem.firstElementChild;
                    while (anchor && anchor.tagName !== 'A') {
                        anchor = anchor.nextElementSibling;
                    }
                    if (!anchor) {
                        continue;
                    }

                    var searchableText = anchor.textContent.toLowerCase();
                    var allSubLinks = topItem.querySelectorAll('.side-menu a, .bottom-menu a');
                    for (var sIdx = 0; sIdx < allSubLinks.length; sIdx++) {
                        searchableText += ' ' + allSubLinks[sIdx].textContent.toLowerCase();
                    }

                    topItem.style.display = (term === '' || searchableText.indexOf(term) !== -1) ? '' : 'none';
                }

                var items = document.querySelectorAll('nav.side-nav > ul > li.has-submenu');
                for (var j = 0; j < items.length; j++) {
                    var li = items[j];
                    var toggle = li.querySelector(':scope > .crm-submenu-toggle');
                    if (term !== '' && li.style.display !== 'none') {
                        var subText = '';
                        var subLinks = li.querySelectorAll('.side-menu a, .bottom-menu a');
                        for (var k = 0; k < subLinks.length; k++) {
                            subText += ' ' + subLinks[k].textContent.toLowerCase();
                        }
                        if (subText.indexOf(term) !== -1) {
                            li.classList.add('is-submenu-open');
                            if (toggle) {
                                toggle.setAttribute('aria-expanded', 'true');
                            }
                        }
                    }
                }
            });
        }
    }

    function closeAllProfileMenus(exceptLi) {
        var openItems = document.querySelectorAll('header .right-side > ul > li.is-open');
        for (var i = 0; i < openItems.length; i++) {
            if (!exceptLi || openItems[i] !== exceptLi) {
                openItems[i].classList.remove('is-open');
                var link = openItems[i].querySelector(':scope > a');
                if (link) {
                    link.setAttribute('aria-expanded', 'false');
                }
            }
        }
    }

    function initProfileDropdown() {
        var profileItems = document.querySelectorAll('header .right-side > ul > li');
        for (var i = 0; i < profileItems.length; i++) {
            (function (li) {
                var trigger = li.querySelector(':scope > a');
                var submenu = li.querySelector(':scope > ul');
                if (!trigger || !submenu || trigger.dataset.crmProfileBound) {
                    return;
                }
                trigger.dataset.crmProfileBound = '1';
                trigger.setAttribute('aria-haspopup', 'true');
                trigger.setAttribute('aria-expanded', 'false');

                trigger.addEventListener('click', function (event) {
                    var rawHref = (trigger.getAttribute('href') || '').trim();
                    if (rawHref === '' || rawHref === '#') {
                        event.preventDefault();
                        event.stopPropagation();
                        var nextOpen = !li.classList.contains('is-open');
                        closeAllProfileMenus(li);
                        li.classList.toggle('is-open', nextOpen);
                        trigger.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
                    }
                });
            })(profileItems[i]);
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('header .right-side > ul > li')) {
                closeAllProfileMenus();
            }
        });
    }

    function applyTableDataLabels(tableEl) {
        if (!tableEl || tableEl.classList.contains('crm-orders-table')) {
            return;
        }
        var headerCells = tableEl.querySelectorAll('thead th');
        if (!headerCells.length) {
            return;
        }

        var labels = [];
        for (var i = 0; i < headerCells.length; i++) {
            var text = (headerCells[i].textContent || '').replace(/\s+/g, ' ').trim();
            labels.push(text !== '' ? text : 'Action');
        }

        var rows = tableEl.querySelectorAll('tbody tr');
        for (var r = 0; r < rows.length; r++) {
            var cells = rows[r].children;
            if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
                continue;
            }
            for (var c = 0; c < cells.length; c++) {
                if (c < labels.length && !cells[c].hasAttribute('data-label')) {
                    cells[c].setAttribute('data-label', labels[c]);
                }
            }
        }
    }

    function wrapTableInScroller(tableEl) {
        if (!tableEl || !tableEl.parentNode) {
            return;
        }
        if (tableEl.closest('.crm-dt-scroll, .crm-table-wrapper, .table-wrap')) {
            return;
        }
        var wrapper = document.createElement('div');
        wrapper.className = 'crm-dt-scroll';
        tableEl.parentNode.insertBefore(wrapper, tableEl);
        wrapper.appendChild(tableEl);
    }

    function initResponsiveTables() {
        var tables = document.querySelectorAll('main .container table:not(.crm-orders-table)');
        for (var i = 0; i < tables.length; i++) {
            applyTableDataLabels(tables[i]);
            if (tables[i].id === 'table' || tables[i].classList.contains('dataTable')) {
                wrapTableInScroller(tables[i]);
            }
        }

        if (window.jQuery) {
            window.jQuery(document).on('init.dt draw.dt', function (e) {
                if (e && e.target) {
                    applyTableDataLabels(e.target);
                    wrapTableInScroller(e.target);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initNavigationDrawer();
        initSubmenuAccordions();
        initProfileDropdown();
        initResponsiveTables();
    });
})();
