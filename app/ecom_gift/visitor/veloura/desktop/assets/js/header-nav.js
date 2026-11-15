(function () {
    'use strict';

    function initHeaderNav() {
        const header = document.getElementById('site-header');
        const menu = document.getElementById('site-menu');
        const menuButton = document.getElementById('mobile-menu-button');
        const searchButton = document.getElementById('header-search-button');
        const searchPopover = document.getElementById('header-search-popover');
        const searchInput = searchPopover ? searchPopover.querySelector('input[type="search"]') : null;

        if (!header || !menu) return;

        // Create or get mobile overlay
        let overlay = document.getElementById('vlMobileOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'vlMobileOverlay';
            overlay.className = 'vl-mobile-overlay';
            overlay.setAttribute('aria-hidden', 'true');
            document.body.appendChild(overlay);
        }

        const isMobileView = () => window.innerWidth < 1024;

        // Mobile drawer toggling
        function openMobileMenu() {
            menu.classList.add('is-open');
            overlay.classList.add('is-active');
            if (menuButton) {
                menuButton.setAttribute('aria-expanded', 'true');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            menu.classList.remove('is-open');
            overlay.classList.remove('is-active');
            if (menuButton) {
                menuButton.setAttribute('aria-expanded', 'false');
            }
            document.body.style.overflow = '';
        }

        if (menuButton) {
            menuButton.addEventListener('click', function (e) {
                e.preventDefault();
                if (menu.classList.contains('is-open')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });
        }

        overlay.addEventListener('click', closeMobileMenu);

        // Dropdown triggers: hover and click
        const dropdownItems = Array.from(menu.querySelectorAll('.site-nav-dropdown'));
        const subDropdownItems = Array.from(menu.querySelectorAll('.site-subdropdown'));

        dropdownItems.forEach((item) => {
            const trigger = item.querySelector(':scope > .site-dropdown-trigger');
            const sub = item.querySelector(':scope > .site-dropdown-list');
            if (!trigger || !sub) return;

            // Accessibility: reflect hover state in aria-expanded
            item.addEventListener('mouseenter', () => {
                if (!isMobileView()) {
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });

            item.addEventListener('mouseleave', () => {
                if (!isMobileView()) {
                    trigger.setAttribute('aria-expanded', 'false');
                    item.classList.remove('is-open');
                }
            });

            // On mobile: click trigger toggles accordion
            trigger.addEventListener('click', (e) => {
                if (isMobileView()) {
                    e.preventDefault();
                    const isOpen = item.classList.contains('is-open');
                    // Close sibling dropdowns
                    dropdownItems.forEach((other) => {
                        if (other !== item) {
                            other.classList.remove('is-open');
                            const otherTrig = other.querySelector(':scope > .site-dropdown-trigger');
                            if (otherTrig) otherTrig.setAttribute('aria-expanded', 'false');
                        }
                    });

                    item.classList.toggle('is-open', !isOpen);
                    trigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
                }
            });

            // Keyboard accessibility
            trigger.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    if (!isMobileView()) {
                        item.classList.add('is-open');
                        trigger.setAttribute('aria-expanded', 'true');
                        const firstLink = sub.querySelector('a');
                        if (firstLink && e.key === 'ArrowDown') {
                            e.preventDefault();
                            firstLink.focus();
                        }
                    }
                } else if (e.key === 'Escape') {
                    item.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                    trigger.focus();
                }
            });
        });

        // Nested subdropdowns
        subDropdownItems.forEach((item) => {
            const trigger = item.querySelector(':scope > .site-subdropdown-trigger');
            const sub = item.querySelector(':scope > .site-subdropdown-list');
            if (!trigger || !sub) return;

            item.addEventListener('mouseenter', () => {
                if (!isMobileView()) {
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });

            item.addEventListener('mouseleave', () => {
                if (!isMobileView()) {
                    trigger.setAttribute('aria-expanded', 'false');
                    item.classList.remove('is-open');
                }
            });

            trigger.addEventListener('click', (e) => {
                if (isMobileView()) {
                    e.preventDefault();
                    const isOpen = item.classList.contains('is-open');
                    item.classList.toggle('is-open', !isOpen);
                    trigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileMenu();
                dropdownItems.forEach((item) => {
                    item.classList.remove('is-open');
                    const trig = item.querySelector(':scope > .site-dropdown-trigger');
                    if (trig) trig.setAttribute('aria-expanded', 'false');
                });
            }
        });

        // Search popover handling
        if (searchButton && searchPopover) {
            searchButton.addEventListener('click', function (e) {
                e.preventDefault();
                const isOpen = searchPopover.matches && searchPopover.matches(':popover-open');
                if (isOpen) {
                    if (typeof searchPopover.hidePopover === 'function') {
                        searchPopover.hidePopover();
                    } else {
                        searchPopover.classList.remove('is-open');
                    }
                } else {
                    if (typeof searchPopover.showPopover === 'function') {
                        try {
                            searchPopover.showPopover();
                        } catch (_) {
                            searchPopover.classList.add('is-open');
                        }
                    } else {
                        searchPopover.classList.add('is-open');
                    }
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 60);
                    }
                }
            });
        }

        // Window resize: clean up states when switching breakpoints
        window.addEventListener('resize', () => {
            if (!isMobileView() && menu.classList.contains('is-open')) {
                closeMobileMenu();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeaderNav);
    } else {
        initHeaderNav();
    }
})();
