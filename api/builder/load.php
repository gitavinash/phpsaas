<?php

/* Load Page Builder Content */

require_once LOGIN_CHECK;

function get_type_mapping($type)
{
    $type = strtolower(trim($type));

    $mappings = [
        'page' => [
            'table' => 'page',
            'id_col' => 'page_id',
            'title_col' => 'page_title',
            'desc_col' => 'page_description',
            'type_col' => 'page_type'
        ],
        'articles' => [
            'table' => 'page',
            'id_col' => 'page_id',
            'title_col' => 'page_title',
            'desc_col' => 'page_description',
            'type_col' => 'page_type'
        ],
        'post' => [
            'table' => 'post',
            'id_col' => 'post_id',
            'title_col' => 'post_title',
            'desc_col' => 'post_description',
            'type_col' => null
        ],
        'news' => [
            'table' => 'news',
            'id_col' => 'news_id',
            'title_col' => 'news_title',
            'desc_col' => 'news_description',
            'type_col' => null
        ],
        'treatment' => [
            'table' => 'treatment',
            'id_col' => 'treatment_id',
            'title_col' => 'treatment_title',
            'desc_col' => 'treatment_description',
            'type_col' => null
        ],
        'career' => [
            'table' => 'career',
            'id_col' => 'career_id',
            'title_col' => 'career_title',
            'desc_col' => 'career_description',
            'type_col' => null
        ],
        'offer' => [
            'table' => 'offer',
            'id_col' => 'offer_id',
            'title_col' => 'offer_title',
            'desc_col' => 'offer_description',
            'type_col' => null
        ]
    ];

    if (isset($mappings[$type])) {
        return $mappings[$type];
    }

    return [
        'table' => $type,
        'id_col' => $type . '_id',
        'title_col' => $type . '_title',
        'desc_col' => $type . '_description',
        'type_col' => null
    ];
}

function load($connection, $data)
{
    if (!isset($data['id']) || !isset($data['type'])) {
        echo json_encode([
            'status' => 400,
            'msg' => 'Missing ID or Type parameters'
        ]);
        exit();
    }

    $id = base64_decode($data['id']);
    $type = mysqli_real_escape_string($connection, $data['type']);
    $mapping = get_type_mapping($type);

    $table = TABLE_PREFIX . $mapping['table'];
    $id_col = $mapping['id_col'];
    $title_col = $mapping['title_col'];
    $desc_col = $mapping['desc_col'];

    // Validate that table and ID column exist
    $id = mysqli_real_escape_string($connection, $id);

    $query = "SELECT `{$title_col}`, `{$desc_col}` FROM `{$table}` WHERE `{$id_col}` = '{$id}' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo json_encode([
            'status' => 404,
            'msg' => 'Content not found'
        ]);
        exit();
    }

    $row = mysqli_fetch_assoc($result);
    $title = $row[$title_col];
    $description = html_entity_decode($row[$desc_col]);

    // Parse JSON layout if it exists inside script tags
    $sections = [];
    $has_json = false;

    if (preg_match('/<script[^>]*id="builder-layout-data"[^>]*>(.*?)<\/script>/is', $description, $matches)) {
        $json_str = trim($matches[1]);
        $json_data = json_decode($json_str, true);
        if ($json_data && isset($json_data['sections'])) {
            $sections = $json_data['sections'];
            $has_json = true;
        }
    }

    // Fallback if no structured JSON builder data is found (legacy HTML content)
    if (!$has_json) {
        // Remove existing toolbar or rte styles if they leaked
        require_once EDITOR_CONTENT_FILTER;

        // Use clean description
        $clean_desc = $description;

        $sections = [
            [
                'id' => 'sec_legacy_' . uniqid(),
                'type' => 'full_width_section',
                'settings' => [
                    'background_color' => '#ffffff',
                    'padding_top' => '40',
                    'padding_bottom' => '40'
                ],
                'blocks' => [
                    [
                        'id' => 'block_legacy_' . uniqid(),
                        'type' => 'static_html',
                        'content' => $clean_desc
                    ]
                ]
            ]
        ];
    }

    echo json_encode([
        'status' => 200,
        'title' => $title,
        'sections' => $sections,
        'type'     => $type
    ]);
    exit();
}

// ── Dispatcher ──────────────────────────────────────────────────────────
// The API router includes this file; we must call the function ourselves.

if (rtrim(str_replace('/api/', '', BASE_URL), '/') === 'builder/load') {
    $action = isset($_GET['action']) ? trim($_GET['action']) : 'load';

    if ($action === 'load') {
        load($connection, $_GET);
    } else {
        echo json_encode(['status' => 400, 'msg' => 'Unknown action']);
    }
}

