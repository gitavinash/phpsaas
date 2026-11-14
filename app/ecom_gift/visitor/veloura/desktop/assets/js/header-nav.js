(function () {
    'use strict';

    // Conditionally load popover polyfill ONLY when native popover support is missing
    if (!Object.hasOwn(HTMLElement.prototype, 'popover')) {
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/@oddbird/popover-polyfill@latest/dist/popover.min.js';
        script.async = true;
        document.head.appendChild(script);
    }

    function isPopoverOpen(el) {
        if (!el) return false;
        try {
            if (el.matches(':popover-open')) return true;
        } catch (_) {
            // Browser lacks :popover-open selector
        }
        return el.classList.contains(':popover-open');
    }

    function safeShowPopover(el, source) {
        if (!el || typeof el.showPopover !== 'function' || isPopoverOpen(el)) return;
        try {
            if (source) {
                el.showPopover({ source: source });
            } else {
                el.showPopover();
            }
        } catch (_) {
            try {
                el.showPopover();
            } catch (__) {
                // Ignore if element is temporarily detached or hidden
            }
        }
    }

    function safeHidePopover(el) {
        if (!el || typeof el.hidePopover !== 'function' || !isPopoverOpen(el)) return;
        try {
            el.hidePopover();
        } catch (_) {
            // Ignore
        }
    }

    function positionFloatingBelowHeader(panel, header) {
        if (!panel || !header) return;
        const headerRect = header.getBoundingClientRect();
        const top = Math.max(8, Math.round(headerRect.bottom + 8));
        panel.style.insetBlockStart = top + 'px';
        panel.style.maxBlockSize = 'calc(100dvh - ' + (top + 16) + 'px)';
    }

    function positionSearchPopoverFallback(trigger, panel, header) {
        if (!panel || !header) return;
        const headerRect = header.getBoundingClientRect();
        const top = Math.max(8, Math.round(headerRect.bottom + 8));
        panel.style.insetBlockStart = top + 'px';

        if (window.matchMedia('(min-width: 640px)').matches && trigger) {
            const triggerRect = trigger.getBoundingClientRect();
            const panelWidth = panel.getBoundingClientRect().width || Math.min(window.innerWidth * 0.92, 368);
            const rightOffset = Math.max(12, Math.round(window.innerWidth - triggerRect.right));
            if (rightOffset + panelWidth <= window.innerWidth - 12) {
                panel.style.insetInlineEnd = rightOffset + 'px';
                panel.style.insetInlineStart = 'auto';
            } else {
                panel.style.insetInlineEnd = '12px';
                panel.style.insetInlineStart = 'auto';
            }
        } else {
            panel.style.insetInlineEnd = '';
            panel.style.insetInlineStart = '';
        }
    }

    function positionSubmenuFallback(trigger, submenu) {
        if (!trigger || !submenu) return;
        const triggerRect = trigger.getBoundingClientRect();
        const menuRect = submenu.getBoundingClientRect();
        const margin = 12;
        const gap = 8;

        let top = triggerRect.bottom + gap;
        if (top + menuRect.height > window.innerHeight - margin) {
            const aboveTop = triggerRect.top - menuRect.height - gap;
            if (aboveTop >= margin) {
                top = aboveTop;
            } else {
                top = Math.max(margin, window.innerHeight - menuRect.height - margin);
            }
        }

        const maxLeft = Math.max(margin, window.innerWidth - menuRect.width - margin);
        const left = Math.max(margin, Math.min(triggerRect.left, maxLeft));

        submenu.style.top = Math.round(top) + 'px';
        submenu.style.left = Math.round(left) + 'px';
    }

    function doesWideRowFit(inner, logo, menu, actions) {
        if (!inner || !logo || !menu || !actions) return false;
        const cs = window.getComputedStyle(inner);
        const padLeft = parseFloat(cs.paddingLeft) || 0;
        const padRight = parseFloat(cs.paddingRight) || 0;
        const colGap = parseFloat(cs.columnGap) || 0;
        const innerWidth = inner.getBoundingClientRect().width;
        const requiredWidth =
            padLeft +
            padRight +
            logo.getBoundingClientRect().width +
            menu.getBoundingClientRect().width +
            actions.getBoundingClientRect().width +
            colGap * 2;

        return requiredWidth <= innerWidth - 4 && inner.scrollWidth <= inner.clientWidth;
    }

    function initResponsiveHeader() {
        const header = document.getElementById('site-header');
        const inner = document.getElementById('site-header-inner');
        const logo = inner ? inner.querySelector('.site-logo') : null;
        const actions = inner ? inner.querySelector('.site-actions') : null;
        const menu = document.getElementById('site-menu');
        const menuButton = document.getElementById('mobile-menu-button');
        const searchButton = document.getElementById('header-search-button');
        const searchPopover = document.getElementById('header-search-popover');
        const searchPopoverInput = document.getElementById('header-search-popover-input');

        if (!header || !inner || !logo || !actions || !menu || !menuButton) return;

        const branchItems = Array.from(menu.querySelectorAll('.site-nav-dropdown'));
        const nestedBranchItems = Array.from(menu.querySelectorAll('.site-subdropdown'));
        const supportsAnchorPos = typeof CSS !== 'undefined' && typeof CSS.supports === 'function' && CSS.supports('position-area', 'block-end');
        const supportsAnchorFunc = typeof CSS !== 'undefined' && typeof CSS.supports === 'function' && CSS.supports('top', 'anchor(bottom)');
        const canHover = window.matchMedia('(hover: hover)');
        const desktopSearchMq = window.matchMedia('(min-width: 640px)');

        const hoverPreviewMap = new WeakMap();
        const hoverCloseTimers = new WeakMap();

        let currentWide = null;
        let currentCompactSearch = false;
        let lastCollapsedHeaderWidth = 0;
        let rafMeasureId = 0;

        function clearSubmenuInlinePosition(submenu) {
            submenu.style.top = '';
            submenu.style.left = '';
        }

        function syncLayout(forceMeasure) {
            const headerWidth = header.getBoundingClientRect().width;
            if (!forceMeasure && currentWide === false && headerWidth <= lastCollapsedHeaderWidth + 1) {
                lastCollapsedHeaderWidth = headerWidth;
                if (searchPopover && isPopoverOpen(searchPopover) && !supportsAnchorPos) {
                    positionSearchPopoverFallback(searchButton, searchPopover, header);
                }
                return;
            }

            const wasMenuOpen = !currentWide && isPopoverOpen(menu);
            const prevWide = currentWide;

            header.classList.add('is-measuring', 'is-wide');
            header.classList.remove('is-compact-search');
            menu.removeAttribute('popover');

            // Temporarily ensure inline submenus don't distort natural horizontal row width
            branchItems.forEach((item) => {
                const sub = item.querySelector(':scope > .site-dropdown-list');
                if (sub) {
                    sub.removeAttribute('popover');
                    sub.hidden = true;
                }
            });

            // Tier 1: Full inline menu + inline search box
            let wide = false;
            let compactSearch = false;

            if (doesWideRowFit(inner, logo, menu, actions)) {
                wide = true;
                compactSearch = false;
            } else {
                // Tier 2: Keep full inline menu visible and collapse search to an icon button
                header.classList.add('is-compact-search');
                if (doesWideRowFit(inner, logo, menu, actions)) {
                    wide = true;
                    compactSearch = true;
                } else {
                    // Tier 3: Collapsed menu button when even compact nav + icons cannot fit on one line
                    wide = false;
                    compactSearch = false;
                }
            }

            currentWide = wide;
            currentCompactSearch = compactSearch;

            header.classList.toggle('is-wide', wide);
            header.classList.toggle('is-compact-search', compactSearch);

            if (wide) {
                lastCollapsedHeaderWidth = 0;
                menu.removeAttribute('popover');
                menu.hidden = false;
                menu.style.insetBlockStart = '';
                menu.style.maxBlockSize = '';
                menuButton.setAttribute('aria-expanded', 'false');

                branchItems.forEach((item) => {
                    const trigger = item.querySelector(':scope > .site-dropdown-trigger');
                    const sub = item.querySelector(':scope > .site-dropdown-list');
                    if (!trigger || !sub) return;
                    sub.hidden = false;
                    sub.setAttribute('popover', 'auto');
                    trigger.setAttribute('aria-expanded', 'false');
                });
            } else {
                lastCollapsedHeaderWidth = headerWidth;
                menu.setAttribute('popover', 'auto');

                branchItems.forEach((item) => {
                    const trigger = item.querySelector(':scope > .site-dropdown-trigger');
                    const sub = item.querySelector(':scope > .site-dropdown-list');
                    if (!trigger || !sub) return;
                    clearSubmenuInlinePosition(sub);
                    sub.removeAttribute('popover');
                    if (prevWide !== false) {
                        sub.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                    } else {
                        const expanded = trigger.getAttribute('aria-expanded') === 'true';
                        sub.hidden = !expanded;
                    }
                });

                if (wasMenuOpen) {
                    safeShowPopover(menu, menuButton);
                    if (!supportsAnchorFunc) {
                        positionFloatingBelowHeader(menu, header);
                    }
                }
            }

            // Keep search popover synced with viewport resize / compact-search transitions
            const searchIconVisible = currentCompactSearch || !desktopSearchMq.matches;
            if (searchPopover && isPopoverOpen(searchPopover)) {
                if (!searchIconVisible) {
                    safeHidePopover(searchPopover);
                } else if (!supportsAnchorPos) {
                    positionSearchPopoverFallback(searchButton, searchPopover, header);
                }
            }

            if (rafMeasureId) cancelAnimationFrame(rafMeasureId);
            rafMeasureId = requestAnimationFrame(() => {
                header.classList.remove('is-measuring');
            });
        }

        // Track main collapsed menu open/close state
        menu.addEventListener('toggle', (event) => {
            if (currentWide) return;
            const open = event.newState === 'open';
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open && !supportsAnchorFunc) {
                positionFloatingBelowHeader(menu, header);
            }
        });

        // Search popover positioning & autofocus (works for both <640px and compact-search mode)
        if (searchPopover) {
            searchPopover.addEventListener('toggle', (event) => {
                const open = event.newState === 'open';
                if (searchButton) {
                    searchButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                if (open) {
                    if (!supportsAnchorPos) {
                        positionSearchPopoverFallback(searchButton, searchPopover, header);
                    }
                    if (searchPopoverInput) {
                        requestAnimationFrame(() => searchPopoverInput.focus());
                    }
                } else {
                    searchPopover.style.insetBlockStart = '';
                    searchPopover.style.insetInlineEnd = '';
                    searchPopover.style.insetInlineStart = '';
                }
            });
        }

        // Top-level dropdown triggers & submenus (wide popover + narrow disclosure)
        branchItems.forEach((item) => {
            const trigger = item.querySelector(':scope > .site-dropdown-trigger');
            const sub = item.querySelector(':scope > .site-dropdown-list');
            if (!trigger || !sub) return;

            trigger.addEventListener('click', (event) => {
                if (currentWide) {
                    if (hoverPreviewMap.get(sub)) {
                        hoverPreviewMap.delete(sub);
                        event.preventDefault();
                        return;
                    }
                    return;
                }

                // Narrow mode: inline disclosure
                event.preventDefault();
                const willExpand = sub.hidden;
                sub.hidden = !willExpand;
                trigger.setAttribute('aria-expanded', willExpand ? 'true' : 'false');
            });

            sub.addEventListener('toggle', (event) => {
                if (!currentWide) return;
                const open = event.newState === 'open';
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    if (!supportsAnchorPos) {
                        positionSubmenuFallback(trigger, sub);
                    }
                } else {
                    hoverPreviewMap.delete(sub);
                    clearSubmenuInlinePosition(sub);
                }
            });

            item.addEventListener('pointerenter', (event) => {
                if (!currentWide || !canHover.matches || event.pointerType === 'touch') return;
                const pendingClose = hoverCloseTimers.get(sub);
                if (pendingClose) {
                    clearTimeout(pendingClose);
                    hoverCloseTimers.delete(sub);
                }
                if (!isPopoverOpen(sub)) {
                    hoverPreviewMap.set(sub, true);
                    safeShowPopover(sub, trigger);
                }
            });

            sub.addEventListener('pointerenter', () => {
                if (!currentWide || !canHover.matches) return;
                const pendingClose = hoverCloseTimers.get(sub);
                if (pendingClose) {
                    clearTimeout(pendingClose);
                    hoverCloseTimers.delete(sub);
                }
            });

            item.addEventListener('pointerleave', () => {
                if (!currentWide) return;
                if (hoverPreviewMap.get(sub)) {
                    const timer = setTimeout(() => {
                        hoverCloseTimers.delete(sub);
                        if (hoverPreviewMap.get(sub)) {
                            hoverPreviewMap.delete(sub);
                            safeHidePopover(sub);
                        }
                    }, 110);
                    hoverCloseTimers.set(sub, timer);
                }
            });

            item.addEventListener('focusout', (event) => {
                if (!currentWide) return;
                const next = event.relatedTarget;
                if (next && !item.contains(next) && isPopoverOpen(sub)) {
                    hoverPreviewMap.delete(sub);
                    safeHidePopover(sub);
                }
            });
        });

        // 3rd-level nested submenus (inline disclosure in both narrow and wide popovers)
        nestedBranchItems.forEach((item) => {
            const trigger = item.querySelector(':scope > .site-subdropdown-trigger');
            const sub = item.querySelector(':scope > .site-subdropdown-list');
            if (!trigger || !sub) return;

            sub.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');

            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                const willExpand = sub.hidden;
                sub.hidden = !willExpand;
                trigger.setAttribute('aria-expanded', willExpand ? 'true' : 'false');
            });
        });

        // Cart preview continue button (avoids inline onclick)
        const continueBtn = header.querySelector('[data-cart-continue]');
        if (continueBtn) {
            continueBtn.addEventListener('click', () => {
                const cartLink = continueBtn.closest('.cart-dropdown-trigger')?.querySelector('a');
                if (cartLink) cartLink.click();
            });
        }

        // Close popovers when navigating via link inside menu
        menu.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;
            if (!currentWide) {
                safeHidePopover(menu);
            } else {
                const openSub = link.closest('.site-dropdown-list[popover]');
                if (openSub) safeHidePopover(openSub);
            }
        });

        // Initial measurement + observers
        syncLayout(true);

        if (typeof ResizeObserver === 'function') {
            const ro = new ResizeObserver(() => syncLayout(false));
            ro.observe(header);
            ro.observe(menu);
        } else {
            window.addEventListener('resize', () => syncLayout(true));
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => syncLayout(true));
        }

        window.addEventListener('load', () => syncLayout(true));
    }

    let initialized = false;
    function bootResponsiveHeader() {
        if (initialized) return;
        if (document.getElementById('site-header')) {
            initialized = true;
            initResponsiveHeader();
        }
    }

    // Run immediately when parsed right after </header> to prevent FOUC/CLS
    bootResponsiveHeader();

    if (!initialized && document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootResponsiveHeader);
    }
})();
