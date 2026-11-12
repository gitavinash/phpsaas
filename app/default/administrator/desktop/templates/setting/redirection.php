<?php

$rmEsc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$rmCssLib = defined('CSS_LIBRARY') ? CSS_LIBRARY : '/lib/resource/ui/css/';
$rmJsLib = defined('JS_LIBRARY') ? JS_LIBRARY : '/lib/resource/ui/js/';
$rmRootDir = defined('ROOT') ? ROOT : dirname(__DIR__, 5);

$rmCssRelPath = 'app/default/desktop/redirection.css';
$rmJsRelPath = 'app/default/desktop/redirection.js';
$rmCssDisk = $rmRootDir . $rmCssLib . $rmCssRelPath;
$rmJsDisk = $rmRootDir . $rmJsLib . $rmJsRelPath;
$rmCssVer = file_exists($rmCssDisk) ? (string) filemtime($rmCssDisk) : '1';
$rmJsVer = file_exists($rmJsDisk) ? (string) filemtime($rmJsDisk) : '1';

$rmSiteBase = defined('SITE_URL') && SITE_URL !== '' ? rtrim((string) SITE_URL, '/') : '';
$rmSiteDomain = defined('SITE_DOMAIN') && SITE_DOMAIN !== '' ? strtolower(trim((string) SITE_DOMAIN)) : '';
if ($rmSiteBase === '' && $rmSiteDomain !== '') {
    $rmProto = defined('HTTP_PROTOCOL') ? HTTP_PROTOCOL : 'http://';
    $rmSiteBase = rtrim($rmProto . $rmSiteDomain, '/');
}
if ($rmSiteDomain === '' && $rmSiteBase !== '') {
    $rmParsedHost = parse_url($rmSiteBase, PHP_URL_HOST);
    if (is_string($rmParsedHost) && $rmParsedHost !== '') {
        $rmSiteDomain = strtolower($rmParsedHost);
    }
}

$rmAllowedStatuses = array('301', '302', '307', '308', '300', '303', '304');
$rmRawStatus = (isset($_GET['status']) && is_string($_GET['status'])) ? trim($_GET['status']) : 'all';
$rmInitStatus = in_array($rmRawStatus, $rmAllowedStatuses, true) ? $rmRawStatus : 'all';

$rmRawQuery = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
$rmInitQuery = function_exists('mb_substr') ? mb_substr($rmRawQuery, 0, 120, 'UTF-8') : substr($rmRawQuery, 0, 120);

$rmRawMsgType = (isset($_GET['statusType']) && is_string($_GET['statusType'])) ? strtolower(trim($_GET['statusType'])) : '';
$rmInitMsgType = in_array($rmRawMsgType, array('success', 'error'), true) ? $rmRawMsgType : '';
$rmInitMsgText = (isset($_GET['message']) && is_string($_GET['message'])) ? trim($_GET['message']) : '';

$rmStatusOptions = array(
    '301' => '301 (Moved Permanently)',
    '302' => '302 (Found / Temporary)',
    '307' => '307 (Temporary Redirect)',
    '308' => '308 (Permanent Redirect)',
    '300' => '300 (Multiple Choices)',
    '303' => '303 (See Other)',
    '304' => '304 (Not Modified)',
);
?>
<link rel="stylesheet" href="<?php echo $rmEsc($rmCssLib . $rmCssRelPath . '?v=' . $rmCssVer); ?>">

<div
    id="redirectionManager"
    class="redirection-manager"
    data-api-url="/api/v3/setting/redirection/"
    data-site-base="<?php echo $rmEsc($rmSiteBase); ?>"
    data-site-domain="<?php echo $rmEsc($rmSiteDomain); ?>"
    data-init-status="<?php echo $rmEsc($rmInitStatus); ?>"
    data-init-q="<?php echo $rmEsc($rmInitQuery); ?>"
    data-init-msg-type="<?php echo $rmEsc($rmInitMsgType); ?>"
    data-init-msg="<?php echo $rmEsc($rmInitMsgText); ?>">

    <div id="rmFieldErrorAnnouncer" class="rm-visually-hidden" aria-live="polite"></div>

    <div class="rm-page-header">
        <div class="rm-title-group">
            <div class="rm-title-row">
                <h1 class="rm-page-title">URL Redirections</h1>
                <?php if ($rmSiteDomain !== '') { ?>
                    <span id="rmDomainBadge" class="rm-domain-pill"><?php echo $rmEsc($rmSiteDomain); ?></span>
                <?php } ?>
            </div>
            <p class="rm-page-subtitle">Forward outdated paths to active destinations and update HTTP status codes without page reloads.</p>
        </div>
        <div class="rm-header-actions">
            <button type="button" id="rmFocusAddBtn" class="rm-btn rm-btn-primary rm-icon-start">Add Redirection</button>
        </div>
    </div>

    <section class="rm-stats-grid" aria-label="Redirection summary metrics">
        <article class="rm-stat-card rm-stat-card-total">
            <div class="rm-stat-top">
                <span class="rm-stat-icon rm-stat-icon-blue" aria-hidden="true">
                    <span class="rm-icon"></span>
                </span>
                <span class="rm-stat-tag">All Rules</span>
            </div>
            <strong id="rmStatTotal" class="rm-stat-value">—</strong>
            <span class="rm-stat-label">Total Redirections</span>
            <span class="rm-stat-note">Active URL forwards</span>
        </article>
        <article class="rm-stat-card rm-stat-card-perm">
            <div class="rm-stat-top">
                <span class="rm-stat-icon rm-stat-icon-green" aria-hidden="true">
                    <span class="rm-icon"></span>
                </span>
                <span class="rm-stat-tag">301 / 308</span>
            </div>
            <strong id="rmStatPermanent" class="rm-stat-value">—</strong>
            <span class="rm-stat-label">Permanent Redirects</span>
            <span class="rm-stat-note">SEO link equity preserved</span>
        </article>
        <article class="rm-stat-card rm-stat-card-temp">
            <div class="rm-stat-top">
                <span class="rm-stat-icon rm-stat-icon-amber" aria-hidden="true">
                    <span class="rm-icon"></span>
                </span>
                <span class="rm-stat-tag">302 / 307</span>
            </div>
            <strong id="rmStatTemporary" class="rm-stat-value">—</strong>
            <span class="rm-stat-label">Temporary Redirects</span>
            <span class="rm-stat-note">Short-term campaign routes</span>
        </article>
        <article class="rm-stat-card rm-stat-card-other">
            <div class="rm-stat-top">
                <span class="rm-stat-icon rm-stat-icon-violet" aria-hidden="true">
                    <span class="rm-icon"></span>
                </span>
                <span class="rm-stat-tag">300 / 303 / 304</span>
            </div>
            <strong id="rmStatOther" class="rm-stat-value">—</strong>
            <span class="rm-stat-label">Other Status Rules</span>
            <span class="rm-stat-note">Specialized HTTP codes</span>
        </article>
    </section>

    <div id="rmAlertBanner" class="rm-alert" role="status" aria-live="polite" hidden>
        <div class="rm-alert-body">
            <span class="rm-icon rm-alert-icon" aria-hidden="true"></span>
            <span id="rmAlertText"></span>
        </div>
        <button type="button" id="rmAlertDismiss" class="rm-alert-dismiss" aria-label="Dismiss status message">
            <span class="rm-icon" aria-hidden="true"></span>
        </button>
    </div>

    <section class="rm-card" aria-labelledby="rmAddHeading">
        <div class="rm-card-header">
            <div>
                <h2 id="rmAddHeading" class="rm-card-title">Add New Redirection</h2>
                <p class="rm-card-subtitle">Route retired URLs cleanly to active internal pages or external destinations.</p>
            </div>
        </div>
        <form id="rmAddForm" class="rm-add-form" method="POST" action="/api/v3/setting/redirection/" aria-labelledby="rmAddHeading" novalidate>
            <div class="rm-form-grid">
                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmAddFrom">From Path <span class="rm-req" aria-hidden="true">*</span></label>
                    </div>
                    <span id="rmAddFromHint" class="rm-hint">Relative path on this site (for example: <code>/old-page/</code>). No spaces, <code>?</code>, or <code>#</code>.</span>
                    <div class="rm-input-prefix-wrap">
                        <?php if ($rmSiteBase !== '') { ?>
                            <span id="rmAddOriginPrefix" class="rm-origin-prefix" aria-hidden="true"><?php echo $rmEsc($rmSiteBase); ?></span>
                        <?php } ?>
                        <input
                            type="text"
                            id="rmAddFrom"
                            name="from_url"
                            class="rm-input"
                            required
                            maxlength="255"
                            placeholder="/old-page/"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="none"
                            spellcheck="false"
                            enterkeyhint="next"
                            aria-describedby="rmAddFromHint"
                            aria-invalid="false"
                            data-error="rmAddFromError">
                    </div>
                    <span id="rmAddFromPreview" class="rm-path-preview" aria-live="off"></span>
                    <div id="rmAddFromError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>

                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmAddTo">Redirect To <span class="rm-req" aria-hidden="true">*</span></label>
                    </div>
                    <span id="rmAddToHint" class="rm-hint">Internal path like <code>/new-page/</code> or full URL like <code>https://example.com/</code>.</span>
                    <input
                        type="text"
                        id="rmAddTo"
                        name="to_url"
                        class="rm-input"
                        required
                        maxlength="255"
                        placeholder="/new-page/ or https://..."
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="none"
                        spellcheck="false"
                        enterkeyhint="next"
                        aria-describedby="rmAddToHint"
                        aria-invalid="false"
                        data-error="rmAddToError">
                    <div id="rmAddToError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>

                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmAddStatus">HTTP Status <span class="rm-req" aria-hidden="true">*</span></label>
                        <a class="rm-help-link" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections" target="_blank" rel="noopener noreferrer">Status reference</a>
                    </div>
                    <span id="rmAddStatusHint" class="rm-hint">Use 301 for permanent SEO moves; 302 or 307 for temporary forwards.</span>
                    <select
                        id="rmAddStatus"
                        name="http_status"
                        class="rm-select"
                        required
                        aria-describedby="rmAddStatusHint"
                        aria-invalid="false"
                        data-error="rmAddStatusError">
                        <?php foreach ($rmStatusOptions as $rmCode => $rmLabel) { ?>
                            <option value="<?php echo $rmEsc($rmCode); ?>" <?php echo ($rmCode === '301') ? 'selected' : ''; ?>><?php echo $rmEsc($rmLabel); ?></option>
                        <?php } ?>
                    </select>
                    <div id="rmAddStatusError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>
            </div>

            <div class="rm-form-actions">
                <button type="submit" id="rmAddSubmitBtn" class="rm-btn rm-btn-primary rm-icon-start">Add Redirection</button>
                <button type="reset" id="rmAddResetBtn" class="rm-btn rm-btn-ghost">Reset</button>
            </div>
        </form>
    </section>

    <section class="rm-card" aria-labelledby="rmListHeading">
        <div class="rm-toolbar">
            <div class="rm-toolbar-heading">
                <h2 id="rmListHeading" class="rm-card-title">Configured Redirects</h2>
                <span id="rmCountBadge" class="rm-count-badge">0 rules</span>
            </div>

            <search class="rm-search-region">
                <form id="rmFilterForm" class="rm-filter-form" method="GET" action="/dashboard/setting/">
                    <input type="hidden" name="type" value="redirection">
                    <div class="rm-search-box">
                        <label for="rmSearchInput" class="rm-visually-hidden">Search redirection paths</label>
                        <span class="rm-icon rm-search-icon" aria-hidden="true"></span>
                        <input
                            type="search"
                            id="rmSearchInput"
                            name="q"
                            class="rm-input rm-search-input"
                            value="<?php echo $rmEsc($rmInitQuery); ?>"
                            placeholder="Search path or target..."
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="none"
                            spellcheck="false"
                            enterkeyhint="search">
                    </div>

                    <label for="rmFilterStatus" class="rm-visually-hidden">Filter by HTTP status</label>
                    <select id="rmFilterStatus" name="status" class="rm-select rm-filter-select">
                        <option value="all" <?php echo ($rmInitStatus === 'all') ? 'selected' : ''; ?>>All HTTP Statuses</option>
                        <?php foreach ($rmStatusOptions as $rmCode => $rmLabel) { ?>
                            <option value="<?php echo $rmEsc($rmCode); ?>" <?php echo ($rmInitStatus === $rmCode) ? 'selected' : ''; ?>><?php echo $rmEsc($rmLabel); ?></option>
                        <?php } ?>
                    </select>

                    <div class="rm-filter-actions">
                        <button type="submit" id="rmFilterApplyBtn" class="rm-btn rm-btn-secondary">Filter</button>
                        <button type="button" id="rmClearFilterBtn" class="rm-btn rm-btn-ghost" <?php echo ($rmInitStatus === 'all' && $rmInitQuery === '') ? 'hidden' : ''; ?>>Clear</button>
                    </div>
                </form>
            </search>
        </div>

        <p id="rmSortStatus" class="rm-visually-hidden" role="status"></p>

        <div id="rmTableWrapper" class="table-wrapper" tabindex="0" role="region" aria-labelledby="rmTableCaption">
            <table id="redirectionTable" class="rm-table">
                <caption id="rmTableCaption" class="rm-visually-hidden">Configured URL redirection rules for this website</caption>
                <thead>
                    <tr>
                        <th scope="col" class="rm-col-from" data-type="text">
                            <button type="button" commandfor="redirectionTable" command="--sort" class="rm-sort-btn">
                                <span>From Path</span>
                                <span class="rm-icon rm-sort-icon" aria-hidden="true"></span>
                            </button>
                        </th>
                        <th scope="col" class="rm-col-to" data-type="text">
                            <button type="button" commandfor="redirectionTable" command="--sort" class="rm-sort-btn">
                                <span>Redirect To</span>
                                <span class="rm-icon rm-sort-icon" aria-hidden="true"></span>
                            </button>
                        </th>
                        <th scope="col" class="rm-col-status" data-type="number">
                            <button type="button" commandfor="redirectionTable" command="--sort" class="rm-sort-btn">
                                <span>HTTP Status</span>
                                <span class="rm-icon rm-sort-icon" aria-hidden="true"></span>
                            </button>
                        </th>
                        <th scope="col" class="rm-col-updated" data-type="number" aria-sort="descending">
                            <button type="button" commandfor="redirectionTable" command="--sort" class="rm-sort-btn">
                                <span>Updated</span>
                                <span class="rm-icon rm-sort-icon" aria-hidden="true"></span>
                            </button>
                        </th>
                        <th scope="col" class="rm-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="redirectionTableBody">
                    <tr class="rm-table-status-row">
                        <td colspan="5">Loading redirection rules...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="rmEmptyState" class="rm-empty-panel" hidden>
            <h3 id="rmEmptyTitle" class="rm-empty-title">No redirection rules yet</h3>
            <p id="rmEmptyCopy" class="rm-empty-copy">Create your first redirection rule above to forward outdated URLs cleanly.</p>
            <button type="button" id="rmEmptyResetBtn" class="rm-btn rm-btn-secondary" hidden>Reset filters</button>
        </div>
    </section>

    <dialog id="rmEditDialog" class="rm-dialog" closedby="any" aria-labelledby="rmEditDialogTitle">
        <div class="rm-dialog-header">
            <h2 id="rmEditDialogTitle" class="rm-dialog-title">Edit Redirection</h2>
            <button type="button" class="rm-dialog-close" command="request-close" commandfor="rmEditDialog" aria-label="Close edit dialog">
                <span class="rm-icon" aria-hidden="true"></span>
            </button>
        </div>
        <form id="rmEditForm" method="POST" action="/api/v3/setting/redirection/" aria-labelledby="rmEditDialogTitle" novalidate>
            <input type="hidden" id="rmEditId" name="id" value="">
            <div class="rm-dialog-body">
                <div id="rmEditMeta" class="rm-dialog-meta"></div>
                <div id="rmEditServerError" class="rm-dialog-error" role="alert" hidden></div>

                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmEditFrom">From Path <span class="rm-req" aria-hidden="true">*</span></label>
                    </div>
                    <span id="rmEditFromHint" class="rm-hint">Relative path on this site (for example: <code>/old-page/</code>). Changing this updates the active route.</span>
                    <div class="rm-input-prefix-wrap">
                        <?php if ($rmSiteBase !== '') { ?>
                            <span id="rmEditOriginPrefix" class="rm-origin-prefix" aria-hidden="true"><?php echo $rmEsc($rmSiteBase); ?></span>
                        <?php } ?>
                        <input
                            type="text"
                            id="rmEditFrom"
                            name="from_url"
                            class="rm-input"
                            required
                            autofocus
                            maxlength="255"
                            placeholder="/old-page/"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="none"
                            spellcheck="false"
                            enterkeyhint="next"
                            aria-describedby="rmEditFromHint"
                            aria-invalid="false"
                            data-error="rmEditFromError">
                    </div>
                    <span id="rmEditFromPreview" class="rm-path-preview" aria-live="off"></span>
                    <div id="rmEditFromError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>

                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmEditTo">Redirect To <span class="rm-req" aria-hidden="true">*</span></label>
                    </div>
                    <span id="rmEditToHint" class="rm-hint">Destination internal path (<code>/new-page/</code>) or full <code>https://</code> URL.</span>
                    <input
                        type="text"
                        id="rmEditTo"
                        name="to_url"
                        class="rm-input"
                        required
                        maxlength="255"
                        placeholder="/new-page/ or https://..."
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="none"
                        spellcheck="false"
                        enterkeyhint="next"
                        aria-describedby="rmEditToHint"
                        aria-invalid="false"
                        data-error="rmEditToError">
                    <div id="rmEditToError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>

                <div class="rm-field">
                    <div class="rm-label-row">
                        <label for="rmEditStatus">HTTP Status <span class="rm-req" aria-hidden="true">*</span></label>
                        <a class="rm-help-link" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections" target="_blank" rel="noopener noreferrer">Status reference</a>
                    </div>
                    <span id="rmEditStatusHint" class="rm-hint">Select how search engines and browsers should treat this redirect.</span>
                    <select
                        id="rmEditStatus"
                        name="http_status"
                        class="rm-select"
                        required
                        aria-describedby="rmEditStatusHint"
                        aria-invalid="false"
                        data-error="rmEditStatusError">
                        <?php foreach ($rmStatusOptions as $rmCode => $rmLabel) { ?>
                            <option value="<?php echo $rmEsc($rmCode); ?>"><?php echo $rmEsc($rmLabel); ?></option>
                        <?php } ?>
                    </select>
                    <div id="rmEditStatusError" class="rm-error-msg">
                        <span class="rm-error-icon" role="img" aria-label="Error"></span>
                        <span class="rm-error-text"></span>
                    </div>
                </div>
            </div>
            <div class="rm-dialog-footer">
                <button type="button" id="rmEditCancelBtn" class="rm-btn rm-btn-secondary" command="request-close" commandfor="rmEditDialog">Cancel</button>
                <button type="submit" id="rmEditSaveBtn" class="rm-btn rm-btn-save rm-icon-start">Save Changes</button>
            </div>
        </form>
    </dialog>

    <dialog id="rmDeleteDialog" class="rm-dialog rm-dialog-sm" closedby="any" aria-labelledby="rmDeleteDialogTitle" aria-describedby="rmDeleteLead">
        <div class="rm-dialog-header">
            <h2 id="rmDeleteDialogTitle" class="rm-dialog-title">Delete Redirection Rule?</h2>
            <button type="button" class="rm-dialog-close" command="close" commandfor="rmDeleteDialog" aria-label="Close confirmation dialog">
                <span class="rm-icon" aria-hidden="true"></span>
            </button>
        </div>
        <div class="rm-dialog-body">
            <p id="rmDeleteLead" class="rm-hint">Visitors accessing the source path will no longer be redirected. This action cannot be undone.</p>
            <div id="rmDeleteSummary" class="rm-delete-summary"></div>
            <div id="rmDeleteServerError" class="rm-dialog-error" role="alert" hidden></div>
        </div>
        <div class="rm-dialog-footer">
            <button type="button" id="rmDeleteCancelBtn" class="rm-btn rm-btn-secondary" command="close" commandfor="rmDeleteDialog" autofocus>Cancel</button>
            <button type="button" id="rmDeleteConfirmBtn" class="rm-btn rm-btn-danger rm-icon-start">Delete Rule</button>
        </div>
    </dialog>
</div>

<script defer src="<?php echo $rmEsc($rmJsLib . $rmJsRelPath . '?v=' . $rmJsVer); ?>"></script>
