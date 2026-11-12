/**
 * Events Listing: Tab Switcher, Instant Filter, View Toggle & Sort Controller
 */
(function () {
    'use strict';

    const VIEW_STORAGE_KEY = 'andblooms_events_view';

    function initEventsListing() {
        const page = document.querySelector('.events-page');
        if (!page) return;

        const tabButtons = Array.from(document.querySelectorAll('.event-tab-btn'));
        const switchButtons = Array.from(document.querySelectorAll('.event-switch-tab-btn'));
        const viewButtons = Array.from(document.querySelectorAll('[data-events-view]'));
        const grids = Array.from(document.querySelectorAll('.events-grid'));
        const panels = {
            upcoming: document.getElementById('tab-panel-upcoming'),
            previous: document.getElementById('tab-panel-previous'),
            all: document.getElementById('tab-panel-all')
        };

        const tabInput = document.getElementById('eventSortTabInput');
        const sortSearchInput = document.getElementById('eventSortSearchInput');
        const sortSelect = document.getElementById('eventSortSelect');
        const searchInput = document.getElementById('eventSearchInput');
        const searchClear = document.getElementById('eventSearchClear');
        const filterStatus = document.getElementById('eventsFilterStatus');
        const filterStatusText = document.getElementById('eventsFilterStatusText');
        const filterResetBtn = document.getElementById('eventsResetSearch');
        const noMatchBox = document.getElementById('eventsNoFilterMatch');
        const noMatchClearBtn = document.getElementById('eventsNoMatchClearBtn');
        const noMatchAllBtn = document.getElementById('eventsNoMatchAllBtn');

        let activeTab = (tabInput && panels[tabInput.value]) ? tabInput.value : 'upcoming';

        function applyViewMode(mode, persist) {
            const safeMode = mode === 'list' ? 'list' : 'grid';
            grids.forEach((grid) => {
                grid.classList.toggle('events-view-list', safeMode === 'list');
                grid.classList.toggle('events-view-grid', safeMode !== 'list');
            });

            viewButtons.forEach((btn) => {
                const pressed = btn.getAttribute('data-events-view') === safeMode;
                btn.setAttribute('aria-pressed', pressed ? 'true' : 'false');
            });

            if (persist) {
                try {
                    window.localStorage.setItem(VIEW_STORAGE_KEY, safeMode);
                } catch (_) {
                    // Ignore storage restrictions
                }
            }
        }

        function applySearchFilter() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            if (sortSearchInput) {
                sortSearchInput.value = searchInput ? searchInput.value.trim() : '';
            }
            if (searchClear) {
                searchClear.hidden = query.length === 0;
            }

            const activePanel = panels[activeTab];
            if (!activePanel) return;

            const panelCards = Array.from(activePanel.querySelectorAll('.event-card'));
            let matchedCount = 0;

            panelCards.forEach((card) => {
                const haystack = (card.getAttribute('data-search') || '').toLowerCase();
                const isMatch = query === '' || haystack.indexOf(query) !== -1;
                card.hidden = !isMatch;
                if (isMatch) {
                    matchedCount++;
                }
            });

            // Hide empty section headers inside "All Events" while filtering
            const groups = Array.from(activePanel.querySelectorAll('.events-group'));
            groups.forEach((group) => {
                const visibleInGroup = group.querySelector('.event-card:not([hidden])');
                group.hidden = query !== '' && !visibleInGroup;
            });

            const isFiltering = query !== '' && panelCards.length > 0;
            if (filterStatus && filterStatusText) {
                filterStatus.hidden = !isFiltering;
                if (isFiltering) {
                    const noun = matchedCount === 1 ? 'event' : 'events';
                    filterStatusText.textContent = 'Showing ' + matchedCount + ' of ' + panelCards.length + ' ' + noun + ' for "' + searchInput.value.trim() + '"';
                }
            }

            if (noMatchBox) {
                noMatchBox.hidden = !(query !== '' && panelCards.length > 0 && matchedCount === 0);
            }
            if (noMatchAllBtn) {
                noMatchAllBtn.hidden = activeTab === 'all';
            }
        }

        function syncUrlParams(nextTab) {
            if (!(window.history && window.history.replaceState)) return;
            try {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('tab', nextTab);
                const rawQuery = searchInput ? searchInput.value.trim() : '';
                if (rawQuery !== '') {
                    currentUrl.searchParams.set('q', rawQuery);
                } else {
                    currentUrl.searchParams.delete('q');
                }
                window.history.replaceState(null, '', currentUrl.toString());
            } catch (_) {
                // Ignore invalid URL edge cases
            }
        }

        function setActiveTab(tabName, updateUrl) {
            const nextTab = panels[tabName] ? tabName : 'upcoming';
            activeTab = nextTab;

            Object.keys(panels).forEach((key) => {
                const panel = panels[key];
                if (!panel) return;
                const isSelected = key === nextTab;
                panel.hidden = !isSelected;
            });

            tabButtons.forEach((btn) => {
                const isSelected = btn.getAttribute('data-tab') === nextTab;
                btn.classList.toggle('is-active', isSelected);
                if (isSelected) {
                    btn.setAttribute('aria-current', 'page');
                } else {
                    btn.removeAttribute('aria-current');
                }
            });

            if (tabInput) {
                tabInput.value = nextTab;
            }

            applySearchFilter();

            if (updateUrl) {
                syncUrlParams(nextTab);
            }
        }

        // Restore preferred view mode synchronously to avoid layout jump
        let initialView = 'grid';
        try {
            const saved = window.localStorage.getItem(VIEW_STORAGE_KEY);
            if (saved === 'list' || saved === 'grid') {
                initialView = saved;
            }
        } catch (_) {
            // Ignore
        }
        applyViewMode(initialView, false);

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                setActiveTab(btn.getAttribute('data-tab'), true);
            });
        });

        switchButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                setActiveTab(btn.getAttribute('data-switch-tab'), true);
            });
        });

        viewButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                applyViewMode(btn.getAttribute('data-events-view'), true);
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applySearchFilter);
        }

        const clearSearch = () => {
            if (!searchInput) return;
            searchInput.value = '';
            applySearchFilter();
            syncUrlParams(activeTab);
            searchInput.focus();
        };

        if (searchClear) {
            searchClear.addEventListener('click', clearSearch);
        }
        if (filterResetBtn) {
            filterResetBtn.addEventListener('click', clearSearch);
        }
        if (noMatchClearBtn) {
            noMatchClearBtn.addEventListener('click', clearSearch);
        }

        // Collapse image track gracefully if any remote/legacy event or hero image fails to load
        Array.from(page.querySelectorAll('img.event-card-media, img.events-hero-bg')).forEach((img) => {
            if (img.complete && img.naturalWidth === 0) {
                img.remove();
            } else {
                img.addEventListener('error', () => {
                    img.remove();
                }, { once: true });
            }
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                if (this.form) {
                    this.form.submit();
                }
            });
        }

        setActiveTab(activeTab, false);
    }

    let initialized = false;
    function bootEventsListing() {
        if (initialized) return;
        if (document.querySelector('.events-page')) {
            initialized = true;
            initEventsListing();
        }
    }

    // Boot immediately right after </main> to restore view mode & filter before paint
    bootEventsListing();

    if (!initialized && document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootEventsListing);
    }
})();
