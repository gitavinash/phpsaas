/**
 * Events Page Tab Switcher & Filter Controller
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var tabButtons = document.querySelectorAll('.event-tab-btn');
        var switchButtons = document.querySelectorAll('.event-switch-tab-btn');
        var panels = {
            upcoming: document.getElementById('tab-panel-upcoming'),
            previous: document.getElementById('tab-panel-previous'),
            all: document.getElementById('tab-panel-all')
        };
        var tabInput = document.getElementById('eventSortTabInput');
        var sortSelect = document.getElementById('eventSortSelect');

        function setActiveTab(tabName, updateUrl) {
            if (!panels[tabName]) {
                tabName = 'upcoming';
            }

            // Update panel visibility
            Object.keys(panels).forEach(function(key) {
                var panel = panels[key];
                if (!panel) return;
                if (key === tabName) {
                    panel.classList.remove('hidden');
                    panel.classList.add('block');
                } else {
                    panel.classList.remove('block');
                    panel.classList.add('hidden');
                }
            });

            // Update tab button styles
            tabButtons.forEach(function(btn) {
                var targetTab = btn.getAttribute('data-tab');
                var icon = btn.querySelector('i');
                var badge = btn.querySelector('.tab-count-badge');

                if (targetTab === tabName) {
                    btn.classList.remove('text-gray-600', 'hover:text-gray-900', 'hover:bg-white/60');
                    btn.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'border', 'border-gray-200/80');
                    if (icon) {
                        icon.classList.remove('text-gray-400');
                        icon.classList.add('text-primary');
                    }
                    if (badge) {
                        badge.classList.remove('bg-gray-200/80', 'text-gray-600');
                        badge.classList.add('bg-primary/10', 'text-primary', 'font-bold');
                    }
                } else {
                    btn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'border', 'border-gray-200/80');
                    btn.classList.add('text-gray-600', 'hover:text-gray-900', 'hover:bg-white/60');
                    if (icon) {
                        icon.classList.remove('text-primary');
                        icon.classList.add('text-gray-400');
                    }
                    if (badge) {
                        badge.classList.remove('bg-primary/10', 'text-primary', 'font-bold');
                        badge.classList.add('bg-gray-200/80', 'text-gray-600');
                    }
                }
            });

            // Update hidden sort input
            if (tabInput) {
                tabInput.value = tabName;
            }

            // Update URL without reload if requested
            if (updateUrl && window.history && window.history.replaceState) {
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('tab', tabName);
                window.history.replaceState(null, '', currentUrl.toString());
            }
        }

        // Attach click handlers to tab buttons
        tabButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                setActiveTab(tab, true);
            });
        });

        // Attach click handlers to in-page switch buttons (e.g. from empty states)
        switchButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var tab = this.getAttribute('data-switch-tab');
                setActiveTab(tab, true);
            });
        });

        // Sort dropdown auto-submit
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                if (this.form) {
                    this.form.submit();
                }
            });
        }
    });
})();
