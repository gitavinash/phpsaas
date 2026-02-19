<?php

require_once UTIL_FUNCTIONS;

function render_menu_with_children(array $items, int $depth = 0): void
{
    foreach ($items as $item) {
        $label    = safe_html($item['label'] ?? '');
        $url      = safe_html($item['url']   ?? '#');
        $children = (isset($item['children']) && is_array($item['children'])) ? $item['children'] : [];

        // remove blank child entries
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        if ($depth === 0) {
            if (!empty($children)) {
                echo '<div class="relative group">';
                echo   '<a href="' . $url . '" class="text-gray-700 hover:text-primary transition">' . $label . '</a>';
                echo   '<div class="absolute left-0 top-full mt-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-150 bg-white border w-64 shadow-lg z-50">';
                echo     '<ul class="p-4 space-y-2 text-sm text-gray-700">';
                render_menu_with_children($children, $depth + 1);
                echo     '</ul>';
                echo   '</div>';
                echo '</div>';
            } else {
                echo '<a href="' . $url . '" class="text-gray-700 hover:text-primary transition">' . $label . '</a>';
            }
        } else {
            echo '<li>';
            if (!empty($children)) {
                echo '<div class="relative group/fly">';
                echo   '<a href="' . $url . '" class="block hover:text-primary pr-6">' . $label . '</a>';
                echo   '<div class="absolute left-full top-0 ml-2 invisible opacity-0 group-hover/fly:visible group-hover/fly:opacity-100 transition-all duration-150 bg-white border w-64 shadow-lg z-50">';
                echo     '<ul class="p-4 space-y-2 text-sm text-gray-700">';
                render_menu_with_children($children, $depth + 1);
                echo     '</ul>';
                echo   '</div>';
                echo '</div>';
            } else {
                echo '<a href="' . $url . '" class="block hover:text-primary">' . $label . '</a>';
            }
            echo '</li>';
        }
    }
}

/**
 * PLAIN list renderer (returns <li>…</li> items only; no dropdowns).
 */
function render_plain_menu(array $items): string
{
    $html = '';
    foreach ($items as $item) {
        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url']   ?? '#');
        if ($label === '') {
            continue;
        }
        $html .= '<li><a href="' . $url . '" class="block text-grey-700 hover:text-opacity-5 transition">' . $label . '</a></li>';
    }
    return $html;
}

/**
 * MOBILE renderer using <details><summary> for groups, <a> for leaves.
 * - Root level: siblings are either <a> (leaf) or <details> (with nested <ul>).
 * - Nested levels: inside <ul>, each child is <li> containing either <a> or <details>.
 */
function render_mobile_menu(array $items, int $depth = 0): void
{
    foreach ($items as $item) {
        $label    = safe_html($item['label'] ?? '');
        $url      = safe_html($item['url']   ?? '#');
        $children = (isset($item['children']) && is_array($item['children'])) ? $item['children'] : [];

        // strip blanks
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        $summary_classes = 'cursor-pointer text-gray-700 hover:opacity-50 transition';
        $link_classes    = 'text-white-700 hover:opacity-50 transition';
        $list_classes    = 'pl-4 mt-2 space-y-1 text-sm';
        $block_link      = 'block hover:opacity-50';

        // ROOT level: render siblings inline (no wrapper)
        if ($depth === 0) {
            if (!empty($children)) {
                echo '<details class="group">';
                echo   '<summary class="' . $summary_classes . '">' . $label . '</summary>';
                echo   '<ul class="' . $list_classes . '">';
                // recurse inside UL
                render_mobile_menu($children, $depth + 1);
                echo   '</ul>';
                echo '</details>';
            } else {
                echo '<li><a href="' . $url . '" class="' . $link_classes . '">' . $label . '</a></li>';
            }
            continue;
        }

        // NESTED levels: wrap each child in <li>
        echo '<li>';
        if (!empty($children)) {
            echo '<details class="group">';
            echo   '<summary class="' . $summary_classes . '">' . $label . '</summary>';
            echo   '<ul class="' . $list_classes . '">';
            render_mobile_menu($children, $depth + 1);
            echo   '</ul>';
            echo '</details>';
        } else {
            echo '<a href="' . $url . '" class="' . $block_link . '">' . $label . '</a>';
        }
        echo '</li>';
    }
}

/**
 * Main entry point.
 * $type: 'dropdown' | 'plain' | 'mobile'
 * $echo: true to echo; false to return string (useful for 'plain' or captured output)
 */
function generateMenu(string $menu_name, string $type = 'dropdown', bool $echo = true)
{
    $items = get_object_from_constant($menu_name);

    switch ($type) {
        case 'plain':
            $html = render_plain_menu($items);
            if ($echo) {
                echo $html;
                return;
            }
            return $html;

        case 'mobile':
            if ($echo) {
                render_mobile_menu($items, 0);
                return;
            }
            ob_start();
            render_mobile_menu($items, 0);
            return ob_get_clean();

        case 'dropdown':
        default:
            if ($echo) {
                render_menu_with_children($items, 0);
                return;
            }
            ob_start();
            render_menu_with_children($items, 0);
            return ob_get_clean();
    }
}