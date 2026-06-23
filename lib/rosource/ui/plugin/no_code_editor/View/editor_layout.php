<?php

/**
 * Variables available in this view:
 * - $request
 * - $result
 * - $previewUrl
 */

$request = (isset($request) && is_array($request)) ? $request : [
    'url' => '/',
    'type' => '',
    'id' => 0
];

$result = (isset($result) && is_array($result)) ? $result : [
    'success' => false,
    'message' => 'Preview data unavailable.'
];

$previewUrl = isset($previewUrl) ? (string)$previewUrl : '/';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>No-Code Theme Editor</title>
    <style>
        :root {
            --editor-bg: #f4f6fb;
            --panel-bg: #ffffff;
            --border: #d9e0ef;
            --text: #1f2937;
            --muted: #6b7280;
            --brand: #0f766e;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            height: 100%;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--editor-bg);
            color: var(--text);
        }

        .editor-shell {
            display: grid;
            grid-template-rows: auto 1fr;
            height: 100vh;
        }

        .editor-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            background: var(--panel-bg);
            border-bottom: 1px solid var(--border);
        }

        .toolbar-meta {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--muted);
        }

        .toolbar-meta b {
            color: var(--text);
            font-weight: 600;
        }

        .toolbar-status {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e7f8f6;
            color: var(--brand);
            border: 1px solid #bfece8;
        }

        .toolbar-status.error {
            background: #fff0f0;
            color: #b42318;
            border-color: #ffd4d4;
        }

        .editor-canvas {
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="editor-shell">
        <div class="editor-toolbar">
            <div class="toolbar-meta">
                <span><b>URL:</b> <?php echo htmlspecialchars($request['url'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span><b>Type:</b> <?php echo htmlspecialchars($request['type'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span><b>ID:</b> <?php echo (int)$request['id']; ?></span>
                <?php if (isset($result['resolvedLink']['link_type'])): ?>
                    <span><b>Resolved:</b> <?php echo htmlspecialchars($result['resolvedLink']['link_type'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>

            <div class="toolbar-status <?php echo $result['success'] ? '' : 'error'; ?>">
                <?php echo htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <iframe
            class="editor-canvas"
            title="No-Code Theme Preview"
            src="<?php echo htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?>">
        </iframe>
    </div>
</body>

</html>