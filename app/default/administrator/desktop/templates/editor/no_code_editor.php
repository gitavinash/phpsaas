<?php

$url = ((isset($_GET['url'])) ? mysqli_real_escape_string($connection, $_GET['url']) : '');

if ($url == '') {
    die('No URL provided.');
}

$linkQuery = "SELECT link_type as urlType FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_status!=301 AND link_site_id=" . SITE_ID;
$linkInfo = mysqli_query($connection, $linkQuery);

if (!$linkInfo || mysqli_num_rows($linkInfo) == 0) {
    die('No link found for the provided URL.');
}

extract(mysqli_fetch_assoc($linkInfo));

$linkDataQuery = "SELECT * FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$urlType} ON link_relation_id={$urlType}_id AND {$urlType}_status=1 WHERE link_url='{$url}' AND link_status!=301 AND link_site_id=" . SITE_ID;
$linkData = mysqli_query($connection, $linkDataQuery);

if (!$linkData || mysqli_num_rows($linkData) == 0) {
    die('No link data found for the provided URL.');
}

$linkData = mysqli_fetch_assoc($linkData);
extract($linkData);

$urlTypeVariable = strtolower($urlType) . '_type';

$requiredThemeFile = '';
$dedicatedThemeFile = USER_PATH . ((isset(${$urlTypeVariable}) ? ${$urlTypeVariable} : '')) . '.php';
$urlTypeThemeFile = USER_PATH . strtolower($link_type) . '.php';

if (file_exists($dedicatedThemeFile)) {
    $requiredThemeFile = $dedicatedThemeFile;
} elseif (file_exists($urlTypeThemeFile)) {
    $requiredThemeFile = $urlTypeThemeFile;
}

if ($requiredThemeFile == '') {
    die('No theme file found for the provided URL.');
}

$templateSource = @file_get_contents($requiredThemeFile);

if ($templateSource === false || trim($templateSource) === '') {
    die('Unable to read theme template.');
}

$template = str_replace(array("\r\n", "\r"), "\n", $templateSource);

$extractKeys = function ($source) {
    $keys = array();

    if (preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', $source, $matches)) {
        foreach ($matches[1] as $name) {
            if (!in_array($name, array('i', 'j', 'k', 'connection', 'GLOBALS'), true)) {
                $keys[] = '$' . $name;
            }
        }
    }

    if (preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\[\s*[\'\"]([^\'\"]+)[\'\"]\s*\]/', $source, $arrayMatches, PREG_SET_ORDER)) {
        foreach ($arrayMatches as $match) {
            $keys[] = '$' . $match[1] . '["' . $match[2] . '"]';
        }
    }

    $keys = array_values(array_unique($keys));
    sort($keys);
    return $keys;
};

$allTemplateKeys = $extractKeys($template);
$previewUrl = SITE_URL . '/' . ltrim($url, '/');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simple Web Page Editor</title>
    <style>
        :root {
            --bg: #f4f6f8;
            --panel: #ffffff;
            --line: #dbe1e8;
            --text: #1f2937;
            --muted: #667085;
            --brand: #1d4ed8;
            --brand-soft: #e8f0ff;
            --danger: #dc2626;
            --ok: #15803d;
            --soft: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font: 14px/1.4 "Segoe UI", -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .topbar {
            height: 58px;
            border-bottom: 1px solid var(--line);
            background: var(--panel);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar .title {
            font-weight: 600;
        }

        .topbar .meta {
            color: var(--muted);
            font-size: 12px;
            max-width: 60%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status {
            color: var(--muted);
            font-size: 12px;
            margin-left: 10px;
        }

        .app {
            display: grid;
            grid-template-columns: 320px minmax(520px, 1fr) 360px;
            min-height: calc(100vh - 58px);
        }

        .panel {
            background: var(--panel);
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .panel.right {
            border-right: 0;
            border-left: 1px solid var(--line);
        }

        .panel-head {
            padding: 11px 12px;
            border-bottom: 1px solid var(--line);
            font-weight: 600;
            font-size: 13px;
        }

        .panel-body {
            padding: 12px;
            overflow: auto;
        }

        .builder-group {
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            margin-bottom: 10px;
            background: var(--soft);
        }

        .builder-group h4 {
            margin: 0;
            padding: 10px;
            border-bottom: 1px solid #e6ebf1;
            font-size: 12px;
            color: #4b5563;
        }

        .builder-group .inside {
            padding: 10px;
        }

        .row2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .btn {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
        }

        .btn:hover {
            border-color: #c8d1dc;
            background: #fbfcfd;
        }

        .btn.primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn.soft {
            border-color: #d7dde8;
            background: #ffffff;
            color: #334155;
        }

        .btn.ok {
            border-color: #cde9d8;
            background: #f4fbf7;
            color: #166534;
        }

        .btn.danger {
            border-color: #fecaca;
            background: #fff1f2;
            color: var(--danger);
        }

        .item {
            width: 100%;
            text-align: left;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            margin-bottom: 8px;
            padding: 9px 10px;
            cursor: pointer;
        }

        .item[draggable="true"] {
            cursor: grab;
        }

        .item:hover {
            border-color: #c8d0db;
        }

        .item .name {
            font-weight: 600;
            font-size: 13px;
        }

        .item .small {
            color: var(--muted);
            font-size: 11px;
            margin-top: 4px;
        }

        .hint {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 8px;
        }

        .center {
            display: grid;
            grid-template-rows: auto 1fr;
            min-height: 0;
        }

        .toolbar {
            padding: 9px 12px;
            border-bottom: 1px solid var(--line);
            background: var(--panel);
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .canvas {
            padding: 14px;
            min-height: 0;
            overflow: auto;
        }

        .canvas-shell {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            min-height: calc(100vh - 160px);
            padding: 14px;
        }

        .dropzone,
        .section-dropzone,
        .column-dropzone {
            border: 2px dashed #c7d2fe;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 12px;
            background: #f8faff;
        }

        .dropzone.active,
        .section-dropzone.active,
        .column-dropzone.active {
            border-color: var(--brand);
            color: var(--brand);
            background: var(--brand-soft);
        }

        .section-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            margin-bottom: 10px;
            padding: 10px;
            cursor: pointer;
            position: relative;
        }

        .section-card:hover {
            border-color: #c8d0db;
        }

        .section-card.selected {
            border-color: var(--brand);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .chip {
            border: 1px solid #ced6e1;
            border-radius: 999px;
            padding: 2px 8px;
            color: #3b4554;
            font-size: 11px;
            background: #f8fafc;
        }

        .section-columns {
            display: grid;
            gap: 10px;
            margin-top: 10px;
        }

        .column-card {
            border: 1px solid #dbe5f0;
            border-radius: 8px;
            background: #fafcff;
            padding: 10px;
            min-height: 100px;
        }

        .column-card.selected {
            border-color: var(--brand);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
        }

        .component-card {
            border: 1px solid #d6dfeb;
            border-radius: 8px;
            background: #fff;
            padding: 8px;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .component-card.selected {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .component-preview {
            color: #374151;
            font-size: 12px;
            margin-top: 4px;
            word-break: break-word;
        }

        .component-preview img,
        .component-preview video {
            max-width: 100%;
            border-radius: 6px;
            height: auto;
        }

        .empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            color: var(--muted);
            text-align: center;
            padding: 24px 12px;
            background: #fafbfd;
        }

        .field,
        .select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 8px 9px;
            background: #fff;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .field:focus,
        .select:focus {
            outline: none;
            border-color: var(--brand);
        }

        textarea.field {
            min-height: 88px;
            resize: vertical;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .label {
            color: var(--muted);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
        }

        .card {
            border: 1px solid #e7ebf1;
            border-radius: 8px;
            padding: 9px;
            margin-bottom: 8px;
            background: #fff;
        }

        .option-label {
            color: #475467;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .compact-note {
            color: #667085;
            font-size: 12px;
            margin-top: 6px;
        }

        .suggest-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .tag {
            background: #f1f4f8;
            border: 1px solid #d8dfe8;
            color: #344055;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 11px;
        }

        pre {
            margin: 0;
            padding: 10px;
            border-radius: 8px;
            background: #0f172a;
            color: #e5e7eb;
            overflow: auto;
            font-size: 11px;
            max-height: 220px;
        }

        @media (max-width: 1180px) {
            .app {
                grid-template-columns: 1fr;
            }

            .panel,
            .panel.right {
                border: 0;
                border-bottom: 1px solid var(--line);
            }

            .canvas-shell {
                min-height: 420px;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="title">Simple Web Page Editor</div>
        <div class="meta"><?php echo htmlspecialchars(str_replace(ROOT . '/', '', $requiredThemeFile), ENT_QUOTES, 'UTF-8'); ?></div>
    </header>

    <div class="app">
        <aside class="panel">
            <div class="panel-head">Builder Steps</div>
            <div class="panel-body">
                <div class="builder-group">
                    <h4>1. Add Section</h4>
                    <div class="inside">
                        <div class="row2">
                            <button class="btn primary" data-add-section="1" type="button">1 Col</button>
                            <button class="btn primary" data-add-section="2" type="button">2 Col</button>
                        </div>
                        <div class="row2" style="margin-top:8px;">
                            <button class="btn primary" data-add-section="3" type="button">3 Col</button>
                            <button class="btn primary" data-add-section="4" type="button">4 Col</button>
                        </div>
                        <div class="hint" style="margin-top:8px;">Start from section. Then select section and add columns/components.</div>
                    </div>
                </div>

                <div class="builder-group">
                    <h4>2. Add Column</h4>
                    <div class="inside">
                        <button class="btn soft" id="addColumnBtn" type="button">Add Column To Selected Section</button>
                    </div>
                </div>

                <div class="builder-group">
                    <h4>3. Components</h4>
                    <div class="inside">
                        <div id="componentPalette"></div>
                    </div>
                </div>

                <div class="builder-group">
                    <h4>Dynamic Blocks</h4>
                    <div class="inside">
                        <div id="dynamicPalette"></div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="center">
            <div class="toolbar">
                <button class="btn" id="clearAll" type="button">Clear Canvas</button>
                <button class="btn" id="copyVars" type="button">Copy Used Variables</button>
                <button class="btn primary" id="copyLayout" type="button">Copy Layout JSON</button>
                <button class="btn" id="openPreview" type="button">Open Live Page</button>
                <span class="status" id="statusText">Start by adding a section</span>
            </div>
            <div class="canvas">
                <div class="canvas-shell" id="canvasShell">
                    <div class="dropzone" id="dropzone">Drop section layouts here (1-4 columns)</div>
                    <div id="canvasList"></div>
                </div>
            </div>
        </main>

        <aside class="panel right">
            <div class="panel-head">Inspector</div>
            <div class="panel-body" id="properties"></div>
        </aside>
    </div>

    <script>
        const allTemplateKeys = <?php echo json_encode($allTemplateKeys, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
        const previewUrl = <?php echo json_encode($previewUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

        const componentLibrary = [{
                id: 'cmp-heading',
                name: 'Heading',
                type: 'heading',
                defaults: {
                    content: 'Your Heading',
                    src: '',
                    link: '',
                    cssText: '',
                    styles: {
                        bgColor: '#ffffff',
                        textColor: '#111827',
                        padding: '10',
                        borderRadius: '6',
                        align: 'left',
                        fontSize: '28'
                    }
                }
            },
            {
                id: 'cmp-text',
                name: 'Text',
                type: 'text',
                defaults: {
                    content: 'Write your paragraph here.',
                    src: '',
                    link: '',
                    cssText: '',
                    styles: {
                        bgColor: '#ffffff',
                        textColor: '#374151',
                        padding: '8',
                        borderRadius: '6',
                        align: 'left',
                        fontSize: '14'
                    }
                }
            },
            {
                id: 'cmp-image',
                name: 'Image',
                type: 'image',
                defaults: {
                    content: '',
                    src: 'https://via.placeholder.com/720x360?text=Image',
                    link: '',
                    cssText: '',
                    styles: {
                        bgColor: '#ffffff',
                        textColor: '#111827',
                        padding: '8',
                        borderRadius: '6',
                        align: 'center',
                        fontSize: '14'
                    }
                }
            },
            {
                id: 'cmp-video',
                name: 'Video',
                type: 'video',
                defaults: {
                    content: '',
                    src: 'https://www.w3schools.com/html/mov_bbb.mp4',
                    link: '',
                    cssText: '',
                    styles: {
                        bgColor: '#ffffff',
                        textColor: '#111827',
                        padding: '8',
                        borderRadius: '6',
                        align: 'center',
                        fontSize: '14'
                    }
                }
            },
            {
                id: 'cmp-button',
                name: 'Button',
                type: 'button',
                defaults: {
                    content: 'Button',
                    src: '',
                    link: '#',
                    cssText: '',
                    styles: {
                        bgColor: '#1d4ed8',
                        textColor: '#ffffff',
                        padding: '10',
                        borderRadius: '8',
                        align: 'left',
                        fontSize: '14'
                    }
                }
            },
            {
                id: 'cmp-css',
                name: 'Custom CSS',
                type: 'css',
                defaults: {
                    content: '',
                    src: '',
                    link: '',
                    cssText: '.selector {\n  color: #111827;\n}',
                    styles: {
                        bgColor: '#f8fafc',
                        textColor: '#111827',
                        padding: '10',
                        borderRadius: '6',
                        align: 'left',
                        fontSize: '12'
                    }
                }
            }
        ];

        const dynamicLibrary = [{
                id: 'dyn-news',
                name: 'Recent News',
                type: 'dynamic',
                dynamicType: 'recent_news'
            },
            {
                id: 'dyn-gallery',
                name: 'Gallery',
                type: 'dynamic',
                dynamicType: 'gallery'
            },
            {
                id: 'dyn-slider',
                name: 'Slider',
                type: 'dynamic',
                dynamicType: 'slider'
            }
        ];

        const sectionSuggestionLibrary = [{
                id: 's-hero',
                name: 'Hero Banner'
            },
            {
                id: 's-feature',
                name: 'Feature Grid'
            },
            {
                id: 's-news',
                name: 'Recent News'
            },
            {
                id: 's-gallery',
                name: 'Gallery Section'
            }
        ];

        const componentPaletteEl = document.getElementById('componentPalette');
        const dynamicPaletteEl = document.getElementById('dynamicPalette');
        const propertiesEl = document.getElementById('properties');
        const canvasListEl = document.getElementById('canvasList');
        const dropzoneEl = document.getElementById('dropzone');
        const statusText = document.getElementById('statusText');
        const addColumnBtn = document.getElementById('addColumnBtn');

        let sections = [];
        let selected = {
            type: '',
            sectionId: '',
            columnId: '',
            componentId: ''
        };
        let dragPayload = null;
        let dragSectionId = '';

        function esc(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function uid(prefix) {
            return prefix + '-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 6);
        }

        function baseSection(columns) {
            const cols = [];
            for (let i = 0; i < columns; i++) {
                cols.push({
                    id: uid('col'),
                    components: []
                });
            }

            return {
                id: uid('section'),
                name: 'Section ' + (sections.length + 1),
                styles: {
                    bgColor: '#ffffff',
                    padding: '16',
                    gap: '12',
                    borderRadius: '10'
                },
                columns: cols
            };
        }

        function getSelectedSection() {
            return sections.find((s) => s.id === selected.sectionId) || null;
        }

        function getSelectedColumn() {
            const section = getSelectedSection();
            if (!section) return null;
            return section.columns.find((c) => c.id === selected.columnId) || null;
        }

        function getSelectedComponent() {
            const column = getSelectedColumn();
            if (!column) return null;
            return column.components.find((c) => c.id === selected.componentId) || null;
        }

        function buildPaletteItem(source, group) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'item';
            btn.draggable = true;
            btn.innerHTML = '<div class="name">' + esc(source.name) + '</div><div class="small">' + esc(group) + '</div>';

            btn.addEventListener('dragstart', (event) => {
                dragPayload = {
                    group: group,
                    sourceId: source.id
                };
                event.dataTransfer.setData('text/plain', source.id);
                event.dataTransfer.effectAllowed = 'copy';
            });

            btn.addEventListener('click', () => {
                if (group === 'component') {
                    addComponentToSelectedColumn(source.id);
                } else if (group === 'dynamic') {
                    addDynamicToSelectedColumn(source.id);
                }
            });

            return btn;
        }

        function renderPalette() {
            componentPaletteEl.innerHTML = '';
            dynamicPaletteEl.innerHTML = '';

            componentLibrary.forEach((cmp) => {
                componentPaletteEl.appendChild(buildPaletteItem(cmp, 'component'));
            });

            dynamicLibrary.forEach((dyn) => {
                dynamicPaletteEl.appendChild(buildPaletteItem(dyn, 'dynamic'));
            });
        }

        function addSection(columnCount) {
            const section = baseSection(columnCount);
            sections.push(section);
            selected = {
                type: 'section',
                sectionId: section.id,
                columnId: section.columns[0] ? section.columns[0].id : '',
                componentId: ''
            };
            statusText.textContent = section.name + ' added';
            renderCanvas();
            renderProperties();
        }

        function addColumnToSelectedSection() {
            const section = getSelectedSection();
            if (!section) {
                statusText.textContent = 'Select a section first';
                return;
            }

            section.columns.push({
                id: uid('col'),
                components: []
            });
            statusText.textContent = 'Column added to ' + section.name;
            renderCanvas();
            renderProperties();
        }

        function newComponentFromLibrary(componentId) {
            const source = componentLibrary.find((c) => c.id === componentId);
            if (!source) return null;

            return {
                id: uid('cmp'),
                name: source.name,
                type: source.type,
                dynamicType: '',
                content: source.defaults.content,
                src: source.defaults.src,
                link: source.defaults.link,
                cssText: source.defaults.cssText,
                styles: {
                    ...source.defaults.styles
                },
                keys: []
            };
        }

        function newDynamicFromLibrary(dynamicId) {
            const source = dynamicLibrary.find((d) => d.id === dynamicId);
            if (!source) return null;

            return {
                id: uid('cmp'),
                name: source.name,
                type: 'dynamic',
                dynamicType: source.dynamicType,
                content: source.name,
                src: '',
                link: '',
                cssText: '',
                styles: {
                    bgColor: '#f8fafc',
                    textColor: '#111827',
                    padding: '10',
                    borderRadius: '8',
                    align: 'left',
                    fontSize: '14'
                },
                keys: allTemplateKeys.slice(0, 12)
            };
        }

        function addComponentToSelectedColumn(componentId, targetSectionId = '', targetColumnId = '') {
            let section = targetSectionId ? sections.find((s) => s.id === targetSectionId) : getSelectedSection();
            if (!section) {
                statusText.textContent = 'Select or add a section first';
                return;
            }

            let column = targetColumnId ? section.columns.find((c) => c.id === targetColumnId) : getSelectedColumn();
            if (!column) {
                column = section.columns[0] || null;
            }

            if (!column) {
                statusText.textContent = 'No column found in section';
                return;
            }

            const comp = newComponentFromLibrary(componentId);
            if (!comp) return;

            column.components.push(comp);
            selected = {
                type: 'component',
                sectionId: section.id,
                columnId: column.id,
                componentId: comp.id
            };
            statusText.textContent = comp.name + ' added';
            renderCanvas();
            renderProperties();
        }

        function addDynamicToSelectedColumn(dynamicId, targetSectionId = '', targetColumnId = '') {
            let section = targetSectionId ? sections.find((s) => s.id === targetSectionId) : getSelectedSection();
            if (!section) {
                statusText.textContent = 'Select or add a section first';
                return;
            }

            let column = targetColumnId ? section.columns.find((c) => c.id === targetColumnId) : getSelectedColumn();
            if (!column) {
                column = section.columns[0] || null;
            }

            if (!column) {
                statusText.textContent = 'No column found in section';
                return;
            }

            const comp = newDynamicFromLibrary(dynamicId);
            if (!comp) return;

            column.components.push(comp);
            selected = {
                type: 'component',
                sectionId: section.id,
                columnId: column.id,
                componentId: comp.id
            };
            statusText.textContent = comp.name + ' added';
            renderCanvas();
            renderProperties();
        }

        function applySectionSuggestion(suggestionId) {
            const section = getSelectedSection();
            if (!section) return;

            const resetColumns = (count) => {
                section.columns = [];
                for (let i = 0; i < count; i++) {
                    section.columns.push({
                        id: uid('col'),
                        components: []
                    });
                }
            };

            if (suggestionId === 's-hero') {
                resetColumns(1);
                section.styles.bgColor = '#eef4ff';
                addComponentToSelectedColumn('cmp-heading', section.id, section.columns[0].id);
                addComponentToSelectedColumn('cmp-text', section.id, section.columns[0].id);
                addComponentToSelectedColumn('cmp-button', section.id, section.columns[0].id);
            } else if (suggestionId === 's-feature') {
                resetColumns(3);
                section.styles.bgColor = '#ffffff';
                section.columns.forEach((col) => {
                    addComponentToSelectedColumn('cmp-heading', section.id, col.id);
                    addComponentToSelectedColumn('cmp-text', section.id, col.id);
                });
            } else if (suggestionId === 's-news') {
                resetColumns(1);
                addDynamicToSelectedColumn('dyn-news', section.id, section.columns[0].id);
            } else if (suggestionId === 's-gallery') {
                resetColumns(1);
                addDynamicToSelectedColumn('dyn-gallery', section.id, section.columns[0].id);
            }

            selected = {
                type: 'section',
                sectionId: section.id,
                columnId: section.columns[0].id,
                componentId: ''
            };
            statusText.textContent = 'Suggested layout applied';
            renderCanvas();
            renderProperties();
        }

        function componentPreview(comp) {
            const text = esc(comp.content || '');
            if (comp.type === 'image') {
                return '<img src="' + esc(comp.src || '') + '" alt="image">';
            }
            if (comp.type === 'video') {
                return '<video src="' + esc(comp.src || '') + '" controls></video>';
            }
            if (comp.type === 'button') {
                return '<button style="background:' + esc(comp.styles.bgColor) + ';color:' + esc(comp.styles.textColor) + ';border:0;border-radius:6px;padding:8px 12px;">' + text + '</button>';
            }
            if (comp.type === 'css') {
                return '<pre>' + esc(comp.cssText || '') + '</pre>';
            }
            if (comp.type === 'dynamic') {
                return '<strong>Dynamic:</strong> ' + esc(comp.dynamicType);
            }
            if (comp.type === 'heading') {
                return '<h3 style="margin:0;">' + text + '</h3>';
            }
            return text.replace(/\n/g, '<br>');
        }

        function renderCanvas() {
            if (!sections.length) {
                canvasListEl.innerHTML = '<div class="empty-state">No section yet. Use left panel to add section first.</div>';
                return;
            }

            canvasListEl.innerHTML = '';

            sections.forEach((section) => {
                const sectionEl = document.createElement('article');
                sectionEl.className = 'section-card' + (selected.sectionId === section.id ? ' selected' : '');
                sectionEl.draggable = true;
                sectionEl.dataset.sectionId = section.id;
                sectionEl.style.background = section.styles.bgColor;
                sectionEl.style.padding = (parseInt(section.styles.padding, 10) || 0) + 'px';
                sectionEl.style.borderRadius = (parseInt(section.styles.borderRadius, 10) || 0) + 'px';

                const columnHtml = section.columns.map((column) => {
                    const comps = column.components.length ?
                        column.components.map((comp) => {
                            const selectedClass = selected.componentId === comp.id ? ' selected' : '';
                            return '<div class="component-card' + selectedClass + '" data-component-id="' + esc(comp.id) + '" data-column-id="' + esc(column.id) + '">' +
                                '<div class="small"><strong>' + esc(comp.name) + '</strong></div>' +
                                '<div class="component-preview">' + componentPreview(comp) + '</div>' +
                                '</div>';
                        }).join('') : '<div class="hint">Drop component here</div>';

                    return '<div class="column-card' + (selected.columnId === column.id ? ' selected' : '') + '" data-column-id="' + esc(column.id) + '">' +
                        '<div class="small" style="margin-bottom:6px;"><strong>Column</strong></div>' +
                        '<div class="column-dropzone" data-column-drop="' + esc(column.id) + '">Drop component/widget</div>' +
                        comps +
                        '</div>';
                }).join('');

                sectionEl.innerHTML =
                    '<div class="section-head">' +
                    '<strong>' + esc(section.name) + '</strong>' +
                    '<span class="chip">' + section.columns.length + ' cols</span>' +
                    '</div>' +
                    '<div class="section-dropzone" data-section-drop="' + esc(section.id) + '">Drop section here to reorder</div>' +
                    '<div class="section-columns" style="grid-template-columns: repeat(' + section.columns.length + ', minmax(0, 1fr)); gap:' + (parseInt(section.styles.gap, 10) || 0) + 'px;">' + columnHtml + '</div>';

                sectionEl.addEventListener('click', (event) => {
                    if (event.target.closest('[data-component-id]') || event.target.closest('[data-column-id]')) return;
                    selected = {
                        type: 'section',
                        sectionId: section.id,
                        columnId: section.columns[0] ? section.columns[0].id : '',
                        componentId: ''
                    };
                    renderCanvas();
                    renderProperties();
                });

                sectionEl.addEventListener('dragstart', (event) => {
                    dragSectionId = section.id;
                    event.dataTransfer.setData('text/plain', section.id);
                    event.dataTransfer.effectAllowed = 'move';
                });

                sectionEl.addEventListener('dragend', () => {
                    dragSectionId = '';
                });

                canvasListEl.appendChild(sectionEl);
            });

            bindCanvasInteractions();
        }

        function bindCanvasInteractions() {
            document.querySelectorAll('[data-column-id]').forEach((el) => {
                el.addEventListener('click', () => {
                    const columnId = el.getAttribute('data-column-id');
                    const section = sections.find((s) => s.columns.some((c) => c.id === columnId));
                    if (!section) return;

                    selected = {
                        type: 'column',
                        sectionId: section.id,
                        columnId: columnId,
                        componentId: ''
                    };
                    renderCanvas();
                    renderProperties();
                });
            });

            document.querySelectorAll('[data-component-id]').forEach((el) => {
                el.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const componentId = el.getAttribute('data-component-id');
                    const columnId = el.getAttribute('data-column-id');
                    const section = sections.find((s) => s.columns.some((c) => c.id === columnId));
                    if (!section) return;

                    selected = {
                        type: 'component',
                        sectionId: section.id,
                        columnId: columnId,
                        componentId: componentId
                    };
                    renderCanvas();
                    renderProperties();
                });
            });

            document.querySelectorAll('[data-column-drop]').forEach((el) => {
                el.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    el.classList.add('active');
                });

                el.addEventListener('dragleave', () => {
                    el.classList.remove('active');
                });

                el.addEventListener('drop', (event) => {
                    event.preventDefault();
                    el.classList.remove('active');
                    const columnId = el.getAttribute('data-column-drop');
                    const section = sections.find((s) => s.columns.some((c) => c.id === columnId));
                    if (!section) return;

                    if (dragPayload && dragPayload.group === 'component') {
                        addComponentToSelectedColumn(dragPayload.sourceId, section.id, columnId);
                    }

                    if (dragPayload && dragPayload.group === 'dynamic') {
                        addDynamicToSelectedColumn(dragPayload.sourceId, section.id, columnId);
                    }
                });
            });

            document.querySelectorAll('[data-section-drop]').forEach((el) => {
                el.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    el.classList.add('active');
                });

                el.addEventListener('dragleave', () => {
                    el.classList.remove('active');
                });

                el.addEventListener('drop', (event) => {
                    event.preventDefault();
                    el.classList.remove('active');
                    const targetId = el.getAttribute('data-section-drop');
                    if (!dragSectionId || dragSectionId === targetId) return;

                    const fromIndex = sections.findIndex((s) => s.id === dragSectionId);
                    const toIndex = sections.findIndex((s) => s.id === targetId);
                    if (fromIndex < 0 || toIndex < 0) return;

                    const moved = sections.splice(fromIndex, 1)[0];
                    sections.splice(toIndex, 0, moved);
                    statusText.textContent = 'Section order updated';
                    renderCanvas();
                });
            });
        }

        function renderProperties() {
            const section = getSelectedSection();
            const column = getSelectedColumn();
            const component = getSelectedComponent();

            if (!section) {
                propertiesEl.innerHTML = '<div class="card">Add and select a section to edit content and styles.</div>';
                return;
            }

            if (selected.type === 'section') {
                propertiesEl.innerHTML =
                    '<div class="card">' +
                    '<div class="label">Section</div>' +
                    '<input class="field" id="sectionName" value="' + esc(section.name) + '" placeholder="Section name">' +
                    '<div class="row">' +
                    '<div><div class="option-label">Background</div><input class="field" id="sectionBg" type="color" value="' + esc(section.styles.bgColor || '#ffffff') + '"></div>' +
                    '<div><div class="option-label">Padding</div><input class="field" id="sectionPad" type="number" min="0" max="120" value="' + esc(section.styles.padding || '16') + '"></div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div><div class="option-label">Gap</div><input class="field" id="sectionGap" type="number" min="0" max="80" value="' + esc(section.styles.gap || '12') + '"></div>' +
                    '<div><div class="option-label">Radius</div><input class="field" id="sectionRadius" type="number" min="0" max="40" value="' + esc(section.styles.borderRadius || '10') + '"></div>' +
                    '</div>' +
                    '<button class="btn soft" id="addColumnInside" type="button">Add Column</button>' +
                    '</div>' +
                    '<div class="card">' +
                    '<div class="label">Suggested Section UI</div>' +
                    '<div class="suggest-grid">' +
                    sectionSuggestionLibrary.map((s) => '<button class="btn ok" data-suggest="' + esc(s.id) + '" type="button">' + esc(s.name) + '</button>').join('') +
                    '</div>' +
                    '<div class="compact-note">Starter suggestions. You can add more templates later.</div>' +
                    '</div>';

                const sectionName = document.getElementById('sectionName');
                const sectionBg = document.getElementById('sectionBg');
                const sectionPad = document.getElementById('sectionPad');
                const sectionGap = document.getElementById('sectionGap');
                const sectionRadius = document.getElementById('sectionRadius');

                const applySectionStyles = () => {
                    section.name = sectionName.value;
                    section.styles.bgColor = sectionBg.value;
                    section.styles.padding = sectionPad.value;
                    section.styles.gap = sectionGap.value;
                    section.styles.borderRadius = sectionRadius.value;
                    renderCanvas();
                };

                [sectionName, sectionBg, sectionPad, sectionGap, sectionRadius].forEach((el) => {
                    if (!el) return;
                    el.addEventListener('input', applySectionStyles);
                    el.addEventListener('change', applySectionStyles);
                });

                document.getElementById('addColumnInside').addEventListener('click', addColumnToSelectedSection);
                document.querySelectorAll('[data-suggest]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        applySectionSuggestion(btn.getAttribute('data-suggest'));
                    });
                });
                return;
            }

            if (selected.type === 'column' && column) {
                propertiesEl.innerHTML =
                    '<div class="card">' +
                    '<div class="label">Column</div>' +
                    '<div class="muted">This column has ' + column.components.length + ' components.</div>' +
                    '<div class="compact-note">Drag components from left panel to this column.</div>' +
                    '</div>';
                return;
            }

            if (selected.type === 'component' && component) {
                const style = component.styles || {};

                propertiesEl.innerHTML =
                    '<div class="card">' +
                    '<div class="label">Component</div>' +
                    '<strong>' + esc(component.name) + '</strong>' +
                    '<div class="muted">Type: ' + esc(component.type) + '</div>' +
                    '</div>' +
                    '<div class="card">' +
                    '<div class="label">Content</div>' +
                    (component.type === 'image' || component.type === 'video' ? '<input class="field" id="compSrc" placeholder="Media URL" value="' + esc(component.src || '') + '">' : '') +
                    (component.type === 'button' ? '<input class="field" id="compLink" placeholder="Button link" value="' + esc(component.link || '') + '">' : '') +
                    (component.type !== 'image' && component.type !== 'video' ? '<textarea class="field" id="compContent" placeholder="Text content">' + esc(component.content || '') + '</textarea>' : '') +
                    (component.type === 'css' ? '<textarea class="field" id="compCss" placeholder="Custom CSS">' + esc(component.cssText || '') + '</textarea>' : '') +
                    '</div>' +
                    '<div class="card">' +
                    '<div class="label">Style</div>' +
                    '<div class="row">' +
                    '<div><div class="option-label">Background</div><input class="field" id="compBg" type="color" value="' + esc(style.bgColor || '#ffffff') + '"></div>' +
                    '<div><div class="option-label">Text</div><input class="field" id="compText" type="color" value="' + esc(style.textColor || '#111827') + '"></div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div><div class="option-label">Padding</div><input class="field" id="compPad" type="number" min="0" max="100" value="' + esc(style.padding || '8') + '"></div>' +
                    '<div><div class="option-label">Radius</div><input class="field" id="compRadius" type="number" min="0" max="60" value="' + esc(style.borderRadius || '6') + '"></div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div><div class="option-label">Align</div><select class="select" id="compAlign"><option value="left" ' + (style.align === 'left' ? 'selected' : '') + '>Left</option><option value="center" ' + (style.align === 'center' ? 'selected' : '') + '>Center</option><option value="right" ' + (style.align === 'right' ? 'selected' : '') + '>Right</option></select></div>' +
                    '<div><div class="option-label">Font</div><input class="field" id="compFont" type="number" min="10" max="72" value="' + esc(style.fontSize || '14') + '"></div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card"><button class="btn danger" id="removeComponent" type="button">Remove Component</button></div>';

                const applyComp = () => {
                    const compContent = document.getElementById('compContent');
                    const compSrc = document.getElementById('compSrc');
                    const compLink = document.getElementById('compLink');
                    const compCss = document.getElementById('compCss');
                    const compBg = document.getElementById('compBg');
                    const compText = document.getElementById('compText');
                    const compPad = document.getElementById('compPad');
                    const compRadius = document.getElementById('compRadius');
                    const compAlign = document.getElementById('compAlign');
                    const compFont = document.getElementById('compFont');

                    if (compContent) component.content = compContent.value;
                    if (compSrc) component.src = compSrc.value;
                    if (compLink) component.link = compLink.value;
                    if (compCss) component.cssText = compCss.value;

                    component.styles.bgColor = compBg ? compBg.value : component.styles.bgColor;
                    component.styles.textColor = compText ? compText.value : component.styles.textColor;
                    component.styles.padding = compPad ? compPad.value : component.styles.padding;
                    component.styles.borderRadius = compRadius ? compRadius.value : component.styles.borderRadius;
                    component.styles.align = compAlign ? compAlign.value : component.styles.align;
                    component.styles.fontSize = compFont ? compFont.value : component.styles.fontSize;

                    renderCanvas();
                    statusText.textContent = component.name + ' updated';
                };

                ['compContent', 'compSrc', 'compLink', 'compCss', 'compBg', 'compText', 'compPad', 'compRadius', 'compAlign', 'compFont'].forEach((id) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.addEventListener('input', applyComp);
                    el.addEventListener('change', applyComp);
                });

                document.getElementById('removeComponent').addEventListener('click', () => {
                    const col = getSelectedColumn();
                    if (!col) return;
                    col.components = col.components.filter((c) => c.id !== component.id);
                    selected.componentId = '';
                    selected.type = 'column';
                    renderCanvas();
                    renderProperties();
                    statusText.textContent = 'Component removed';
                });
            }
        }

        document.getElementById('clearAll').addEventListener('click', () => {
            sections = [];
            selected = {
                type: '',
                sectionId: '',
                columnId: '',
                componentId: ''
            };
            renderCanvas();
            renderProperties();
            statusText.textContent = 'Canvas cleared';
        });

        document.getElementById('copyLayout').addEventListener('click', async () => {
            const output = sections.map((section, idx) => ({
                order: idx + 1,
                section_id: section.id,
                section_name: section.name,
                section_style: section.styles,
                columns: section.columns.map((col, colIndex) => ({
                    order: colIndex + 1,
                    column_id: col.id,
                    components: col.components.map((cmp, cmpIndex) => ({
                        order: cmpIndex + 1,
                        component_id: cmp.id,
                        type: cmp.type,
                        dynamic_type: cmp.dynamicType || '',
                        name: cmp.name,
                        content: cmp.content,
                        src: cmp.src,
                        link: cmp.link,
                        css_text: cmp.cssText,
                        style: cmp.styles,
                        variables: cmp.keys || []
                    }))
                }))
            }));
            await navigator.clipboard.writeText(JSON.stringify(output, null, 2));
            statusText.textContent = 'Layout JSON copied';
        });

        document.getElementById('copyVars').addEventListener('click', async () => {
            const keys = sections
                .flatMap((section) => section.columns)
                .flatMap((column) => column.components)
                .flatMap((cmp) => cmp.keys || []);
            const unique = (keys.length ? keys : allTemplateKeys).filter((v, i, a) => a.indexOf(v) === i).sort();
            await navigator.clipboard.writeText(unique.join('\n'));
            statusText.textContent = 'Variables copied';
        });

        document.getElementById('openPreview').addEventListener('click', () => {
            window.open(previewUrl, '_blank');
        });

        dropzoneEl.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzoneEl.classList.add('active');
        });

        dropzoneEl.addEventListener('dragleave', () => {
            dropzoneEl.classList.remove('active');
        });

        dropzoneEl.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzoneEl.classList.remove('active');
            if (dragPayload && dragPayload.group === 'section') {
                const cols = parseInt(dragPayload.sourceId.replace('section-', ''), 10) || 1;
                addSection(cols);
            }
        });

        document.querySelectorAll('[data-add-section]').forEach((btn) => {
            btn.draggable = true;
            btn.addEventListener('dragstart', (event) => {
                const cols = btn.getAttribute('data-add-section');
                dragPayload = {
                    group: 'section',
                    sourceId: 'section-' + cols
                };
                event.dataTransfer.setData('text/plain', 'section-' + cols);
                event.dataTransfer.effectAllowed = 'copy';
            });

            btn.addEventListener('click', () => {
                addSection(parseInt(btn.getAttribute('data-add-section'), 10));
            });
        });

        addColumnBtn.addEventListener('click', addColumnToSelectedSection);

        renderPalette();
        renderCanvas();
        renderProperties();
    </script>
</body>

</html>