<?php

require_once UTIL_FUNCTIONS;

/**
 * CHECK ACTIVE MENU
 */
function is_menu_active(string $menuUrl): bool
{
    $currentUri = trim(
        (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH),
        '/'
    );

    if (!empty($menuUrl)) {
        $menuPath = trim(
            (string) parse_url($menuUrl, PHP_URL_PATH),
            '/'
        );
    } else {
        $menuPath = '';
    }

    // homepage
    if ($menuPath === '' && $currentUri === '') {
        return true;
    }

    return $menuPath === $currentUri;
}

/**
 * DROPDOWN MENU
 */
function render_menu_with_children(
    array $items,
    int $depth = 0,
    string $customClass = ''
): void {

    foreach ($items as $item) {

        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url'] ?? '#');

        $children = (
            isset($item['children']) &&
            is_array($item['children'])
        ) ? $item['children'] : [];

        // remove blank child entries
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        $isActive = is_menu_active($url);

        $menuClass = !empty($customClass)
            ? $customClass
            : 'text-gray-700 hover:text-primary transition';

        $submenuLinkClass = !empty($customClass)
            ? $customClass
            : 'block hover:text-primary';

        $menuClass .= $isActive ? ' active' : '';
        $submenuLinkClass .= $isActive ? ' active' : '';

        if ($depth === 0) {

            if (!empty($children)) {

                echo '<div class="relative group">';

                echo '<a href="' . $url . '" class="' . $menuClass . '">';
                echo $label;
                echo '</a>';

                echo '<div class="absolute left-0 top-full mt-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-150 bg-white border w-64 shadow-lg z-50">';

                echo '<ul class="p-4 space-y-2 text-sm text-gray-700">';

                render_menu_with_children(
                    $children,
                    $depth + 1,
                    $customClass
                );

                echo '</ul>';

                echo '</div>';

                echo '</div>';
            } else {

                echo '<a href="' . $url . '" class="' . $menuClass . '">';
                echo $label;
                echo '</a>';
            }
        } else {

            echo '<li>';

            if (!empty($children)) {

                echo '<div class="relative group/fly">';

                echo '<a href="' . $url . '" class="' . $submenuLinkClass . ' pr-6">';
                echo $label;
                echo '</a>';

                echo '<div class="absolute left-full top-0 ml-2 invisible opacity-0 group-hover/fly:visible group-hover/fly:opacity-100 transition-all duration-150 bg-white border w-64 shadow-lg z-50">';

                echo '<ul class="p-4 space-y-2 text-sm text-gray-700">';

                render_menu_with_children(
                    $children,
                    $depth + 1,
                    $customClass
                );

                echo '</ul>';

                echo '</div>';

                echo '</div>';
            } else {

                echo '<a href="' . $url . '" class="' . $submenuLinkClass . '">';
                echo $label;
                echo '</a>';
            }

            echo '</li>';
        }
    }
}

/**
 * PLAIN MENU
 */
function render_plain_menu(
    array $items,
    string $customClass = ''
): string {

    $html = '';

    foreach ($items as $item) {

        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url'] ?? '#');

        if ($label === '') {
            continue;
        }

        $isActive = is_menu_active($url);

        $class = !empty($customClass)
            ? $customClass
            : 'block text-grey-700 hover:text-opacity-5 transition';

        $class .= $isActive ? ' active' : '';

        $html .= '<li>';

        $html .= '<a href="' . $url . '" class="' . $class . '">';
        $html .= $label;
        $html .= '</a>';

        $html .= '</li>';
    }

    return $html;
}

/**
 * NON LIST MENU
 */
function render_non_list_menu(
    array $items,
    string $customClass = ''
): string {

    $html = '';

    foreach ($items as $item) {

        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url'] ?? '#');

        if ($label === '') {
            continue;
        }

        $isActive = is_menu_active($url);

        $class = !empty($customClass)
            ? $customClass
            : 'text-gray-700 hover:text-primary transition';

        $class .= $isActive ? ' active' : '';

        $html .= '<a href="' . $url . '" class="' . $class . '">';
        $html .= $label;
        $html .= '</a>';
    }

    return $html;
}

/**
 * MOBILE MENU
 */
function render_mobile_menu(
    array $items,
    int $depth = 0,
    string $customClass = ''
): void {

    foreach ($items as $item) {

        $label = safe_html($item['label'] ?? '');
        $url   = safe_html($item['url'] ?? '#');

        $children = (
            isset($item['children']) &&
            is_array($item['children'])
        ) ? $item['children'] : [];

        // strip blanks
        $children = array_values(array_filter($children, function ($child) {
            return is_array($child) && !empty($child['label']);
        }));

        $isActive = is_menu_active($url);

        $summary_classes = !empty($customClass)
            ? $customClass
            : 'cursor-pointer text-gray-700 hover:opacity-50 transition';

        $link_classes = !empty($customClass)
            ? $customClass
            : 'text-white-700 hover:opacity-50 transition';

        $block_link = !empty($customClass)
            ? $customClass
            : 'block hover:opacity-50';

        $summary_classes .= $isActive ? ' active' : '';
        $link_classes .= $isActive ? ' active' : '';
        $block_link .= $isActive ? ' active' : '';

        $list_classes = 'pl-4 mt-2 space-y-1 text-sm';

        // ROOT level
        if ($depth === 0) {

            if (!empty($children)) {

                echo '<details class="group">';

                echo '<summary class="' . $summary_classes . '">';
                echo $label;
                echo '</summary>';

                echo '<ul class="' . $list_classes . '">';

                render_mobile_menu(
                    $children,
                    $depth + 1,
                    $customClass
                );

                echo '</ul>';

                echo '</details>';
            } else {

                echo '<li>';

                echo '<a href="' . $url . '" class="' . $link_classes . '">';
                echo $label;
                echo '</a>';

                echo '</li>';
            }

            continue;
        }

        // NESTED LEVELS
        echo '<li>';

        if (!empty($children)) {

            echo '<details class="group">';

            echo '<summary class="' . $summary_classes . '">';
            echo $label;
            echo '</summary>';

            echo '<ul class="' . $list_classes . '">';

            render_mobile_menu(
                $children,
                $depth + 1,
                $customClass
            );

            echo '</ul>';

            echo '</details>';
        } else {

            echo '<a href="' . $url . '" class="' . $block_link . '">';
            echo $label;
            echo '</a>';
        }

        echo '</li>';
    }
}

/**
 * MAIN MENU GENERATOR
 */
function generateMenu(
    string $menu_name,
    string $type = 'dropdown',
    bool $echo = true,
    string $customClass = ''
) {

    $items = get_object_from_constant($menu_name);

    switch ($type) {

        case 'plain':

            $html = render_plain_menu(
                $items,
                $customClass
            );

            if ($echo) {
                echo $html;
                return;
            }

            return $html;

        case 'mobile':

            if ($echo) {

                render_mobile_menu(
                    $items,
                    0,
                    $customClass
                );

                return;
            }

            ob_start();

            render_mobile_menu(
                $items,
                0,
                $customClass
            );

            return ob_get_clean();

        case 'non_list':

            $html = render_non_list_menu(
                $items,
                $customClass
            );

            if ($echo) {
                echo $html;
                return;
            }

            return $html;

        case 'dropdown':
        default:

            if ($echo) {

                render_menu_with_children(
                    $items,
                    0,
                    $customClass
                );

                return;
            }

            ob_start();

            render_menu_with_children(
                $items,
                0,
                $customClass
            );

            return ob_get_clean();
    }
}
