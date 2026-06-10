<?php

/* Render Page Builder Widget */

// Check user login is NOT required on frontend visitor views!
// So we do NOT enforce require_once LOGIN_CHECK here, since it needs to run on the public website.
// But we will allow anyone to render a preview of widgets based on query params.

function render_recent_articles($connection, $settings) {
    $limit = intval($settings['limit'] ?? 3);
    if ($limit <= 0) $limit = 3;
    if ($limit > 12) $limit = 12;
    
    $query = "SELECT p.page_id, p.page_title, p.page_image, l.link_url FROM " . TABLE_PREFIX . "page p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.page_id WHERE p.page_type = 'article' AND l.link_type = 'page' AND l.link_status = 200 ORDER BY p.page_id DESC LIMIT {$limit}";
    
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return "<p style='color:red;'>Query error: " . htmlspecialchars(mysqli_error($connection)) . "</p>";
    }
    
    if (mysqli_num_rows($result) === 0) {
        return "<div style='text-align:center; padding:40px; color:#888; border:1px solid #eee; border-radius:8px;'>No articles published yet.</div>";
    }
    
    $html = '<div class="recent-articles-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin: 20px 0; font-family: \'Manrope\', sans-serif;">';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $title = htmlspecialchars($row['page_title']);
        $url = htmlspecialchars(SITE_URL . $row['link_url']);
        $img = htmlspecialchars($row['page_image']);
        if ($img === '') {
            $img = '//' . SITE_DOMAIN . '/lib/resource/ui/img/favicon.png'; // Fallback icon/logo
        } else {
            $img = '//' . SITE_DOMAIN . $img;
        }
        
        $html .= '
        <a href="' . $url . '" class="article-card" style="text-decoration: none; color: inherit; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease;">
            <div class="card-img-container" style="position: relative; width: 100%; padding-top: 56.25%; overflow: hidden; background: #f0f0f0;">
                <img src="' . $img . '" alt="' . $title . '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" />
            </div>
            <div class="card-body" style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                <h3 class="card-title" style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; line-height: 1.4; color: #1a1a1a; transition: color 0.2s;">' . $title . '</h3>
                <span class="card-link" style="margin-top: auto; font-size: 14px; font-weight: 600; color: ' . SITE_THEME_COLOUR . '; display: flex; align-items: center; gap: 5px;">Read Article &rarr;</span>
            </div>
        </a>';
    }
    
    $html .= '</div>';
    
    // Add CSS transition styling specifically for cards (only once)
    $html .= '
    <style>
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
    }
    .article-card:hover img {
        transform: scale(1.05);
    }
    .article-card:hover .card-title {
        color: ' . SITE_THEME_COLOUR . ' !important;
    }
    </style>';
    
    return $html;
}

function render($connection, $data) {
    header("Content-Type: application/json; charset=utf-8");
    
    if (!isset($data['type'])) {
        echo json_encode([
            'status' => 400,
            'msg' => 'Missing widget type parameter'
        ]);
        exit();
    }
    
    $widget_type = strtolower(trim($data['type']));
    $settings_json = $data['settings'] ?? '{}';
    $settings = json_decode($settings_json, true) ?: [];
    
    $html = "";
    
    switch ($widget_type) {
        case 'recent_articles':
            $html = render_recent_articles($connection, $settings);
            break;
            
        default:
            $html = "<div style='color:red; padding:15px; border:1px solid red; border-radius:4px;'>Unknown widget type: " . htmlspecialchars($widget_type) . "</div>";
            break;
    }
    
    echo json_encode([
        'status' => 200,
        'html' => $html
    ]);
    exit();
}
