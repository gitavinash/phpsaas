<?php

$tmEsc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$tmCssLib = defined('CSS_LIBRARY') ? CSS_LIBRARY : '/lib/resource/ui/css/';
$tmJsLib = defined('JS_LIBRARY') ? JS_LIBRARY : '/lib/resource/ui/js/';
$tmRootDir = defined('ROOT') ? ROOT : dirname(__DIR__, 5);

$tmCssRelPath = 'app/default/desktop/themes.css';
$tmJsRelPath = 'app/default/desktop/themes.js';
$tmCssDisk = $tmRootDir . $tmCssLib . $tmCssRelPath;
$tmJsDisk = $tmRootDir . $tmJsLib . $tmJsRelPath;
$tmCssVer = file_exists($tmCssDisk) ? (string) filemtime($tmCssDisk) : '1';
$tmJsVer = file_exists($tmJsDisk) ? (string) filemtime($tmJsDisk) : '1';

$tmSiteDomain = defined('SITE_DOMAIN') && SITE_DOMAIN !== '' ? strtolower(trim((string) SITE_DOMAIN)) : '';
$tmSiteBase = defined('SITE_URL') && SITE_URL !== '' ? rtrim((string) SITE_URL, '/') : '';
if ($tmSiteBase === '' && $tmSiteDomain !== '') {
    $tmProto = defined('HTTP_PROTOCOL') ? HTTP_PROTOCOL : 'http://';
    $tmSiteBase = rtrim($tmProto . $tmSiteDomain, '/');
}

$tmAppName = defined('SITE_APP_NAME') && SITE_APP_NAME !== '' ? (string) SITE_APP_NAME : (defined('SITE_APP') ? (string) SITE_APP : 'Application');
$tmAppPath = defined('SITE_APP') ? (string) SITE_APP : '';
$tmRawQuery = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
$tmInitQuery = function_exists('mb_substr') ? mb_substr($tmRawQuery, 0, 120, 'UTF-8') : substr($tmRawQuery, 0, 120);
?>
<link rel="stylesheet" href="<?php echo $tmEsc($tmCssLib . $tmCssRelPath . '?v=' . $tmCssVer); ?>">

<div
    id="themeManagerApp"
    class="theme-manager"
    data-api-url="/api/v3/appearance/themes/"
    data-site-base="<?php echo $tmEsc($tmSiteBase); ?>"
    data-site-domain="<?php echo $tmEsc($tmSiteDomain); ?>"
    data-app-path="<?php echo $tmEsc($tmAppPath); ?>"
    data-init-q="<?php echo $tmEsc($tmInitQuery); ?>">

    <div id="tmLiveAnnouncer" class="tm-visually-hidden" aria-live="polite"></div>

    <div class="tm-page-header">
        <div class="tm-title-group">
            <div class="tm-title-row">
                <h1 class="tm-page-title">Website Themes</h1>
                <span id="tmAppBadge" class="tm-pill tm-pill-app"><?php echo $tmEsc(ucwords(str_replace('_', ' ', $tmAppName))); ?></span>
                <?php if ($tmSiteDomain !== '') { ?>
                    <span id="tmDomainBadge" class="tm-pill tm-pill-domain"><?php echo $tmEsc($tmSiteDomain); ?></span>
                <?php } ?>
            </div>
            <p class="tm-page-subtitle">Switch between themes built for your current application or preview your entire website before applying changes live.</p>
        </div>

        <div class="tm-header-actions">
            <a
                id="tmVisitLiveLink"
                class="tm-btn tm-btn-secondary tm-icon-start tm-icon-external"
                href="<?php echo $tmEsc($tmSiteBase !== '' ? $tmSiteBase . '/' : '/'); ?>"
                target="_blank"
                rel="noopener noreferrer">
                Visit Live Website
            </a>
            <button type="button" id="tmRefreshBtn" class="tm-btn tm-btn-ghost tm-icon-start tm-icon-refresh">
                Refresh
            </button>
        </div>
    </div>

    <section class="tm-stats-grid" aria-label="Theme catalog summary">
        <article class="tm-stat-card">
            <div class="tm-stat-top">
                <span class="tm-stat-icon tm-stat-icon-emerald" aria-hidden="true"><span class="tm-icon tm-i-check"></span></span>
                <span class="tm-stat-tag">Live</span>
            </div>
            <strong id="tmStatLiveTitle" class="tm-stat-value tm-stat-value-sm">Loading...</strong>
            <span class="tm-stat-label">Active Website Theme</span>
            <span id="tmStatLiveMeta" class="tm-stat-note">Visible to all visitors</span>
        </article>

        <article class="tm-stat-card">
            <div class="tm-stat-top">
                <span class="tm-stat-icon tm-stat-icon-blue" aria-hidden="true"><span class="tm-icon tm-i-layers"></span></span>
                <span class="tm-stat-tag">Catalog</span>
            </div>
            <strong id="tmStatTotal" class="tm-stat-value">—</strong>
            <span class="tm-stat-label">App Themes Available</span>
            <span id="tmStatAppMeta" class="tm-stat-note">Filtered to current app</span>
        </article>

        <article class="tm-stat-card">
            <div class="tm-stat-top">
                <span class="tm-stat-icon tm-stat-icon-indigo" aria-hidden="true"><span class="tm-icon tm-i-sparkles"></span></span>
                <span class="tm-stat-tag">Install Status</span>
            </div>
            <strong id="tmStatReady" class="tm-stat-value">—</strong>
            <span class="tm-stat-label">Ready to Activate</span>
            <span id="tmStatMissingNote" class="tm-stat-note">Verified theme templates</span>
        </article>

        <article class="tm-stat-card">
            <div class="tm-stat-top">
                <span class="tm-stat-icon tm-stat-icon-amber" aria-hidden="true"><span class="tm-icon tm-i-eye"></span></span>
                <span class="tm-stat-tag">Session</span>
            </div>
            <strong id="tmStatPreviewTitle" class="tm-stat-value tm-stat-value-sm">Off</strong>
            <span class="tm-stat-label">Preview Mode</span>
            <span id="tmStatPreviewNote" class="tm-stat-note">Private browser preview</span>
        </article>
    </section>

    <section id="tmPreviewBanner" class="tm-preview-banner" aria-label="Active theme preview notice" hidden>
        <div class="tm-preview-banner-main">
            <span class="tm-preview-badge">PREVIEW MODE ACTIVE</span>
            <div class="tm-preview-banner-copy">
                <strong id="tmPreviewBannerHeading">Previewing theme</strong>
                <span id="tmPreviewBannerSub">Only your browser sees this preview. Live website visitors are unaffected.</span>
            </div>
        </div>
        <div class="tm-preview-banner-actions">
            <button type="button" id="tmBannerReopenBtn" class="tm-btn tm-btn-amber">Inspect Preview</button>
            <a id="tmBannerNewTabLink" class="tm-btn tm-btn-secondary tm-icon-start tm-icon-external" href="/" target="_blank" rel="noopener noreferrer">Open Preview Tab</a>
            <button type="button" id="tmBannerApplyBtn" class="tm-btn tm-btn-primary">Apply Live</button>
            <button type="button" id="tmBannerExitBtn" class="tm-btn tm-btn-danger-outline">Exit Preview</button>
        </div>
    </section>

    <div id="tmAlertBanner" class="tm-alert" role="status" aria-live="polite" hidden>
        <div class="tm-alert-body">
            <span class="tm-icon tm-alert-icon" aria-hidden="true"></span>
            <span id="tmAlertText"></span>
        </div>
        <button type="button" id="tmAlertDismiss" class="tm-alert-dismiss" aria-label="Dismiss notification">
            <span class="tm-icon tm-i-close" aria-hidden="true"></span>
        </button>
    </div>

    <section class="tm-panel" aria-labelledby="tmCatalogHeading">
        <div class="tm-toolbar">
            <div class="tm-toolbar-title">
                <h2 id="tmCatalogHeading" class="tm-panel-title">Available App Themes</h2>
                <span id="tmCatalogCount" class="tm-count-badge">0 themes</span>
            </div>

            <search class="tm-search-region">
                <form id="tmFilterForm" class="tm-filter-form" method="GET" action="/dashboard/appearance/">
                    <div class="tm-search-box">
                        <label for="tmSearchInput" class="tm-visually-hidden">Search themes</label>
                        <span class="tm-icon tm-search-icon" aria-hidden="true"></span>
                        <input
                            type="search"
                            id="tmSearchInput"
                            name="q"
                            class="tm-input tm-search-input"
                            value="<?php echo $tmEsc($tmInitQuery); ?>"
                            placeholder="Search themes by name, path, or version..."
                            autocomplete="off"
                            spellcheck="false">
                    </div>

                    <label for="tmStatusFilter" class="tm-visually-hidden">Filter themes by status</label>
                    <select id="tmStatusFilter" class="tm-select">
                        <option value="all">All App Themes</option>
                        <option value="ready">Ready to Switch</option>
                        <option value="missing">Files Missing</option>
                    </select>

                    <button type="button" id="tmClearFilterBtn" class="tm-btn tm-btn-ghost" hidden>Clear</button>
                </form>
            </search>
        </div>

        <div id="tmThemeGrid" class="tm-grid" aria-live="polite">
            <div class="tm-skeleton-card" aria-hidden="true"></div>
            <div class="tm-skeleton-card" aria-hidden="true"></div>
            <div class="tm-skeleton-card" aria-hidden="true"></div>
        </div>

        <div id="tmEmptyState" class="tm-empty-state" hidden>
            <h3 id="tmEmptyTitle" class="tm-empty-title">No matching themes found</h3>
            <p id="tmEmptyCopy" class="tm-empty-copy">No themes match your current search or filter for this application.</p>
            <button type="button" id="tmEmptyResetBtn" class="tm-btn tm-btn-secondary">Show All Themes</button>
        </div>
    </section>

    <dialog id="tmPreviewDialog" class="tm-dialog tm-dialog-preview" closedby="any" aria-labelledby="tmPreviewDialogTitle">
        <div class="tm-preview-modal-header">
            <div class="tm-preview-modal-title-group">
                <span class="tm-preview-badge">LIVE PREVIEW</span>
                <div class="tm-preview-modal-headings">
                    <h2 id="tmPreviewDialogTitle" class="tm-dialog-title">Theme Preview</h2>
                    <span id="tmPreviewDialogSub" class="tm-preview-modal-sub">Not yet applied to live site</span>
                </div>
            </div>

            <div class="tm-viewport-switch" role="group" aria-label="Preview screen size">
                <button type="button" class="tm-vp-btn is-selected" data-viewport="desktop" aria-pressed="true">Desktop</button>
                <button type="button" class="tm-vp-btn" data-viewport="tablet" aria-pressed="false">Tablet</button>
                <button type="button" class="tm-vp-btn" data-viewport="mobile" aria-pressed="false">Mobile</button>
            </div>

            <div class="tm-preview-modal-actions">
                <a id="tmModalNewTabLink" class="tm-btn tm-btn-secondary tm-icon-start tm-icon-external" href="/" target="_blank" rel="noopener noreferrer">Open in Tab</a>
                <button type="button" id="tmModalExitPreviewBtn" class="tm-btn tm-btn-danger-outline">Exit Preview</button>
                <button type="button" id="tmModalApplyBtn" class="tm-btn tm-btn-primary">Apply This Theme</button>
                <button type="button" id="tmPreviewDialogCloseBtn" class="tm-dialog-close" aria-label="Close live preview">
                    <span class="tm-icon tm-i-close" aria-hidden="true"></span>
                </button>
            </div>
        </div>

        <div class="tm-preview-stage tm-vp-desktop" id="tmPreviewStage">
            <div class="tm-iframe-loading" id="tmIframeLoading" role="status">
                <span class="tm-spinner" aria-hidden="true"></span>
                <span>Loading isolated website preview...</span>
            </div>
            <iframe
                id="tmPreviewIframe"
                class="tm-preview-iframe"
                title="Live website preview in selected theme"
                src="about:blank"></iframe>
        </div>
    </dialog>

    <dialog id="tmConfirmDialog" class="tm-dialog tm-dialog-confirm" closedby="any" aria-labelledby="tmConfirmTitle" aria-describedby="tmConfirmDesc">
        <div class="tm-dialog-header">
            <h2 id="tmConfirmTitle" class="tm-dialog-title">Switch Website Theme?</h2>
            <button type="button" id="tmConfirmCloseBtn" class="tm-dialog-close" aria-label="Close confirmation dialog">
                <span class="tm-icon tm-i-close" aria-hidden="true"></span>
            </button>
        </div>

        <div class="tm-dialog-body">
            <p id="tmConfirmDesc" class="tm-confirm-copy">
                Visitors opening your website will immediately see the selected theme. Pages, products, menus, and settings stay intact.
            </p>
            <div id="tmConfirmCompare" class="tm-confirm-compare"></div>
            <div id="tmConfirmError" class="tm-dialog-error" role="alert" hidden></div>
        </div>

        <div class="tm-dialog-footer">
            <button type="button" id="tmConfirmCancelBtn" class="tm-btn tm-btn-secondary" autofocus>Cancel</button>
            <button type="button" id="tmConfirmSubmitBtn" class="tm-btn tm-btn-primary">Confirm &amp; Switch Theme</button>
        </div>
    </dialog>
</div>

<script defer src="<?php echo $tmEsc($tmJsLib . $tmJsRelPath . '?v=' . $tmJsVer); ?>"></script>

