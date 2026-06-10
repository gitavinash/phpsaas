<?php

/* Save Page Builder Content */

require_once LOGIN_CHECK;
require_once __DIR__ . '/load.php'; // Reuse get_type_mapping

function compile_widget_client_script() {
    return '
<script>
(function() {
    function loadWidgets() {
        var widgets = document.querySelectorAll(".builder-widget:not(.loaded)");
        widgets.forEach(function(w) {
            var widgetType = w.getAttribute("data-widget");
            var settings = w.getAttribute("data-settings") || "{}";
            w.classList.add("loaded");
            
            fetch("/api/builder/widget/?action=render&type=" + encodeURIComponent(widgetType) + "&settings=" + encodeURIComponent(settings))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.html) {
                        w.innerHTML = data.html;
                    } else {
                        w.innerHTML = "<p style=\'color:red;\'>Failed to load widget</p>";
                    }
                })
                .catch(function(err) {
                    w.innerHTML = "<p style=\'color:red;\'>Error loading widget</p>";
                });
        });
    }
    
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", loadWidgets);
    } else {
        loadWidgets();
    }
})();
</script>
';
}

function compile_layout_to_html($sections) {
    $html = "";
    $has_widgets = false;
    
    foreach ($sections as $section) {
        $sec_id = htmlspecialchars($section['id'] ?? 'sec_' . uniqid());
        $settings = $section['settings'] ?? [];
        $bg_color = htmlspecialchars($settings['background_color'] ?? '#ffffff');
        $bg_image = htmlspecialchars($settings['background_image'] ?? '');
        $padding_top = intval($settings['padding_top'] ?? '40');
        $padding_bottom = intval($settings['padding_bottom'] ?? '40');
        $margin_top = intval($settings['margin_top'] ?? '0');
        $margin_bottom = intval($settings['margin_bottom'] ?? '0');
        
        $style = "background-color: {$bg_color}; ";
        if ($bg_image !== '') {
            $style .= "background-image: url('{$bg_image}'); background-size: cover; background-position: center; ";
        }
        $style .= "padding-top: {$padding_top}px; padding-bottom: {$padding_bottom}px; ";
        $style .= "margin-top: {$margin_top}px; margin-bottom: {$margin_bottom}px; ";
        
        $html .= "<section id=\"{$sec_id}\" class=\"builder-section\" style=\"{$style}\">\n";
        $html .= "  <div class=\"builder-container\" style=\"max-width: 1200px; margin: 0 auto; padding: 0 15px; box-sizing: border-box;\">\n";
        $html .= "    <div class=\"builder-row\" style=\"display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;\">\n";
        
        $blocks = $section['blocks'] ?? [];
        foreach ($blocks as $block) {
            $block_type = $block['type'] ?? 'static_html';
            $block_width = intval($block['width'] ?? 100);
            $flex_basis = $block_width;
            
            $col_style = "flex: 0 0 {$block_width}%; max-width: {$block_width}%; padding: 15px; box-sizing: border-box; width: 100%;";
            
            $html .= "      <div class=\"builder-col\" style=\"{$col_style}\">\n";
            
            switch ($block_type) {
                case 'static_html':
                    $html .= "        " . ($block['content'] ?? '') . "\n";
                    break;
                    
                case 'heading':
                    $tag = htmlspecialchars($block['tag'] ?? 'h2');
                    $text = ($block['text'] ?? 'Heading');
                    $align = htmlspecialchars($block['align'] ?? 'left');
                    $color = htmlspecialchars($block['color'] ?? '#333333');
                    $size = htmlspecialchars($block['size'] ?? '');
                    
                    $heading_style = "text-align: {$align}; color: {$color}; margin: 0; font-family: 'Manrope', sans-serif;";
                    if ($size !== '') {
                        $heading_style .= " font-size: {$size};";
                    }
                    
                    $html .= "        <{$tag} style=\"{$heading_style}\">{$text}</{$tag}>\n";
                    break;
                    
                case 'rich_text':
                case 'paragraph':
                    $text = ($block['text'] ?? 'Paragraph text goes here...');
                    $align = htmlspecialchars($block['align'] ?? 'left');
                    $color = htmlspecialchars($block['color'] ?? '#555555');
                    $size = htmlspecialchars($block['size'] ?? '16px');
                    
                    $p_style = "text-align: {$align}; color: {$color}; font-size: {$size}; line-height: 1.6; margin: 0; font-family: 'Roboto', sans-serif;";
                    $html .= "        <div style=\"{$p_style}\">{$text}</div>\n";
                    break;
                    
                case 'image':
                    $src = htmlspecialchars($block['src'] ?? '');
                    $alt = htmlspecialchars($block['alt'] ?? 'Image');
                    $align = htmlspecialchars($block['align'] ?? 'center');
                    $radius = intval($block['radius'] ?? '0');
                    $link = htmlspecialchars($block['link'] ?? '');
                    
                    $img_html = "<img src=\"{$src}\" alt=\"{$alt}\" style=\"max-width: 100%; height: auto; display: block; margin: 0 auto; border-radius: {$radius}px;\" />";
                    if ($link !== '') {
                        $img_html = "<a href=\"{$link}\">{$img_html}</a>";
                    }
                    
                    $html .= "        <div style=\"text-align: {$align};\">{$img_html}</div>\n";
                    break;
                    
                case 'button':
                    $text = htmlspecialchars($block['text'] ?? 'Click Here');
                    $link = htmlspecialchars($block['link'] ?? '#');
                    $align = htmlspecialchars($block['align'] ?? 'center');
                    $bg = htmlspecialchars($block['bg_color'] ?? '#007bff');
                    $color = htmlspecialchars($block['color'] ?? '#ffffff');
                    $radius = intval($block['radius'] ?? '4');
                    $py = intval($block['padding_y'] ?? '12');
                    $px = intval($block['padding_x'] ?? '24');
                    
                    $btn_style = "display: inline-block; padding: {$py}px {$px}px; background-color: {$bg}; color: {$color}; border-radius: {$radius}px; text-decoration: none; font-weight: 600; text-align: center; border: none; font-family: 'Manrope', sans-serif; transition: opacity 0.2s;";
                    $html .= "        <div style=\"text-align: {$align};\">\n";
                    $html .= "          <a href=\"{$link}\" style=\"{$btn_style}\">{$text}</a>\n";
                    $html .= "        </div>\n";
                    break;
                    
                case 'spacer':
                    $height = intval($block['height'] ?? '30');
                    $html .= "        <div class=\"builder-spacer\" style=\"height: {$height}px; width: 100%;\"></div>\n";
                    break;
                    
                case 'widget':
                    $widget_type = htmlspecialchars($block['widget_type'] ?? 'recent_articles');
                    $widget_settings = $block['settings'] ?? [];
                    $settings_json = htmlspecialchars(json_encode($widget_settings));
                    
                    $html .= "        <div class=\"builder-widget\" data-widget=\"{$widget_type}\" data-settings=\"{$settings_json}\" style=\"width: 100%;\">\n";
                    $html .= "          <div class=\"widget-placeholder\" style=\"padding: 20px; border: 1px dashed #ccc; text-align: center; color: #888; background-color: #f9f9f9;\">Loading widget content...</div>\n";
                    $html .= "        </div>\n";
                    $has_widgets = true;
                    break;
            }
            
            $html .= "      </div>\n";
        }
        
        $html .= "    </div>\n";
        $html .= "  </div>\n";
        $html .= "</section>\n";
    }
    
    if ($has_widgets) {
        $html .= compile_widget_client_script();
    }
    
    return $html;
}

function save($connection, $data) {
    if (!isset($data['id']) || !isset($data['type']) || !isset($data['sections'])) {
        echo json_encode([
            'status' => 400,
            'msg' => 'Missing required save parameters (id, type, or sections)'
        ]);
        exit();
    }
    
    $id = base64_decode($data['id']);
    $type = mysqli_real_escape_string($connection, $data['type']);
    $mapping = get_type_mapping($type);
    
    $table = TABLE_PREFIX . $mapping['table'];
    $id_col = $mapping['id_col'];
    $desc_col = $mapping['desc_col'];
    
    $sections = $data['sections'];
    if (is_string($sections)) {
        $sections = json_decode($sections, true);
    }
    
    if (!is_array($sections)) {
        echo json_encode([
            'status' => 400,
            'msg' => 'Invalid sections format'
        ]);
        exit();
    }
    
    // Compile JSON metadata
    $metadata_json = json_encode(['sections' => $sections], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $metadata_block = '<script type="application/json" id="builder-layout-data">' . $metadata_json . '</script>';
    
    // Compile Layout to HTML
    $compiled_html = compile_layout_to_html($sections);
    
    // Final description string to save
    $final_description = $metadata_block . "\n" . $compiled_html;
    
    // Escape final content
    $escaped_description = mysqli_real_escape_string($connection, $final_description);
    $id = mysqli_real_escape_string($connection, $id);
    
    mysqli_query($connection, "START TRANSACTION");
    
    $query = "UPDATE `{$table}` SET `{$desc_col}` = '{$escaped_description}' WHERE `{$id_col}` = '{$id}'";
    $update = mysqli_query($connection, $query);
    
    if ($update) {
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status' => 200,
            'msg' => 'Content saved successfully'
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        echo json_encode([
            'status' => 500,
            'msg' => 'Database error while saving content: ' . mysqli_error($connection)
        ]);
    }
    exit();
}

// ── Dispatcher ──────────────────────────────────────────────────────────
// The API router includes this file; we must call the function ourselves.

$action = isset($_POST['action']) ? trim($_POST['action']) : 'save';

if ($action === 'save') {
    save($connection, $_POST);
} else {
    echo json_encode(['status' => 400, 'msg' => 'Unknown action']);
}

