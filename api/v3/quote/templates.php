<?php
/**
 * V3 Commercial Quotation Templates List API
 * Endpoint: /api/v3/quote/templates.php
 * Method: GET
 * Description: Dynamically discovers and returns available commercial quote templates.
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $_SERVER['DOCUMENT_ROOT'] . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$templatesDir = $_SERVER['DOCUMENT_ROOT'] . '/app/dashboard/v3/default/desktop/templates/quote';
if (!is_dir($templatesDir)) {
    $templatesDir = realpath(__DIR__ . '/../../../app/dashboard/v3/default/desktop/templates/quote');
}

$templates = [];

if ($templatesDir && is_dir($templatesDir)) {
    $items = scandir($templatesDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $itemPath = $templatesDir . '/' . $item;

        if (is_dir($itemPath)) {
            $themeJsonPath = $itemPath . '/theme.json';
            $readmePath    = $itemPath . '/readme.md';
            $indexPath     = $itemPath . '/index.php';

            if (file_exists($indexPath) || file_exists($themeJsonPath)) {
                $meta = [];
                if (file_exists($themeJsonPath)) {
                    $jsonContent = file_get_contents($themeJsonPath);
                    $decoded = json_decode($jsonContent, true);
                    if (is_array($decoded)) {
                        $meta = $decoded;
                    }
                }

                $id = $meta['id'] ?? $item;
                $name = $meta['name'] ?? ucfirst($item);
                $title = $meta['title'] ?? ($name . ' Proposal');
                $description = $meta['description'] ?? 'Commercial transport quotation template.';
                $tag = $meta['tag'] ?? 'Custom';
                $badge = $meta['badge'] ?? '';
                $badgeStyle = $meta['badge_style'] ?? '';
                $icon = $meta['icon'] ?? 'fi fi-rr-document-signed';
                $iconStyle = $meta['icon_style'] ?? '';
                $accentColor = $meta['accent_color'] ?? '#0f172a';
                $tags = $meta['tags'] ?? [];
                $order = isset($meta['order']) ? (int)$meta['order'] : 99;

                $screenshot = '';
                $screenshotTypes = ['screenshot.svg', 'screenshot.png', 'screenshot.jpg', 'screenshot.webp'];
                foreach ($screenshotTypes as $imgFile) {
                    if (file_exists($itemPath . '/' . $imgFile)) {
                        $screenshot = '/app/dashboard/v3/default/desktop/templates/quote/' . $item . '/' . $imgFile;
                        break;
                    }
                }

                $templates[] = [
                    'id'           => $id,
                    'name'         => $name,
                    'title'        => $title,
                    'description'  => $description,
                    'tag'          => $tag,
                    'badge'        => $badge,
                    'badge_style'  => $badgeStyle,
                    'icon'         => $icon,
                    'icon_style'   => $iconStyle,
                    'accent_color' => $accentColor,
                    'tags'         => $tags,
                    'screenshot'   => $screenshot,
                    'order'        => $order
                ];
            }
        }
    }
}

usort($templates, function ($a, $b) {
    return ($a['order'] ?? 99) <=> ($b['order'] ?? 99);
});

echo json_encode([
    'status' => 'success',
    'count'  => count($templates),
    'data'   => $templates
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
