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

function render_hero_slider($connection, $settings) {
    $query = "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slide_site_id=" . SITE_ID . " ORDER BY slide_id DESC";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return "<p style='color:red;'>Slider query error: " . htmlspecialchars(mysqli_error($connection)) . "</p>";
    }
    
    $slidesCount = mysqli_num_rows($result);
    if ($slidesCount === 0) {
        return "
        <div style='text-align:center; padding:60px 20px; color:#888; border:2px dashed #ddd; border-radius:12px; background:#fafafa; font-family:sans-serif;'>
            <i class='fi-rr-picture' style='font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;'></i>
            <h4 style='margin:0 0 8px; color:#475569;'>No Active Slides Found</h4>
            <p style='margin:0 0 16px; font-size:13px; color:#94a3b8; max-width:280px; margin-left:auto; margin-right:auto;'>Create and activate slides in your dashboard under Appearance > Sliders.</p>
            <a href='/dashboard/appearance/?type=sliders' class='fc-btn' style='text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:var(--accent, " . SITE_THEME_COLOUR . "); color:#fff; padding:8px 16px; border-radius:6px; font-size:12px; font-weight:600;'><i class='fi-rr-plus'></i> Manage Sliders</a>
        </div>";
    }
    
    // Build Slider HTML
    $sliderId = 'slider_' . uniqid();
    $html = '<div id="' . $sliderId . '" class="premium-hero-slider" style="position: relative; width: 100%; height: 480px; overflow: hidden; font-family: \'Manrope\', sans-serif; border-radius: 12px; background: #1e293b; color: #fff; margin-bottom: 20px;">';
    
    // Slides container
    $html .= '  <div class="slides-container" style="display: flex; width: 100%; height: 100%; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);">';
    
    $i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $img = htmlspecialchars($row['slide_image']);
        if ($img !== '') {
            $img = '//' . SITE_DOMAIN . $img;
        } else {
            $img = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&auto=format&fit=crop&q=80';
        }
        $title = htmlspecialchars($row['slide_title']);
        $subtitle = htmlspecialchars($row['slide_subtitle'] ?? '');
        $desc = htmlspecialchars($row['slide_description'] ?? '');
        $align = htmlspecialchars($row['slide_alignment'] ?: 'center');
        $overlay = intval($row['slide_overlay'] ?? 1);
        
        $cta1Text = htmlspecialchars($row['slider_cta'] ?? '');
        $cta1Link = htmlspecialchars($row['slide_link'] ?: '#');
        $cta1Bg = htmlspecialchars($row['cta1_bg_color'] ?: '#F5D154');
        $cta1Color = htmlspecialchars($row['cta1_text_color'] ?: '#1E293B');
        $cta1Style = htmlspecialchars($row['cta1_style'] ?: 'solid');
        
        $cta2Text = htmlspecialchars($row['slider_cta2'] ?? '');
        $cta2Link = htmlspecialchars($row['slide_link2'] ?: '#');
        $cta2Bg = htmlspecialchars($row['cta2_bg_color'] ?: '#ffffff');
        $cta2Color = htmlspecialchars($row['cta2_text_color'] ?: '#1e293b');
        $cta2Style = htmlspecialchars($row['cta2_style'] ?: 'outline');
        
        // Alignment styles
        $alignItems = $align === 'left' ? 'flex-start' : ($align === 'right' ? 'flex-end' : 'center');
        
        $html .= '
        <div class="hero-slide-item" style="flex: 0 0 100%; width: 100%; height: 100%; background-image: url(\'' . $img . '\'); background-size: cover; background-position: center; position: relative; display: flex; align-items: center; justify-content: center; box-sizing: border-box; padding: 40px 60px;">';
        
        if ($overlay === 1) {
            $html .= '  <div class="slide-overlay" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 1;"></div>';
        }
        
        $html .= '
            <div class="slide-body" style="position: relative; z-index: 2; width: 100%; max-width: 800px; text-align: ' . $align . '; display: flex; flex-direction: column; align-items: ' . $alignItems . '; gap: 15px;">';
            
        if ($subtitle !== '') {
            $html .= '  <span class="slide-subtitle" style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #f5d154;">' . $subtitle . '</span>';
        }
        
        $html .= '      <h2 class="slide-title" style="font-size: 38px; font-weight: 800; line-height: 1.2; margin: 0; color: #ffffff;">' . $title . '</h2>';
        $html .= '      <p class="slide-desc" style="font-size: 15px; line-height: 1.6; margin: 0; color: rgba(255,255,255,0.9); max-width: 600px;">' . $desc . '</p>';
        
        $html .= '      <div class="slide-actions" style="display: flex; gap: 12px; margin-top: 10px;">';
        
        if ($cta1Text !== '') {
            $cta1BtnStyle = "display: inline-flex; align-items: center; justify-content: center; padding: 10px 24px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;";
            if ($cta1Style === 'outline') {
                $cta1BtnStyle .= " background: transparent; color: {$cta1Bg}; border: 2px solid {$cta1Bg};";
            } else {
                $cta1BtnStyle .= " background: {$cta1Bg}; color: {$cta1Color}; border: 2px solid {$cta1Bg};";
            }
            $html .= '      <a href="' . $cta1Link . '" class="slide-btn" style="' . $cta1BtnStyle . '">' . $cta1Text . '</a>';
        }
        
        if ($cta2Text !== '') {
            $cta2BtnStyle = "display: inline-flex; align-items: center; justify-content: center; padding: 10px 24px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;";
            if ($cta2Style === 'outline') {
                $cta2BtnStyle .= " background: transparent; color: {$cta2Color}; border: 2px solid {$cta2Bg};";
            } else {
                $cta2BtnStyle .= " background: {$cta2Bg}; color: {$cta2Color}; border: 2px solid {$cta2Bg};";
            }
            $html .= '      <a href="' . $cta2Link . '" class="slide-btn" style="' . $cta2BtnStyle . '">' . $cta2Text . '</a>';
        }
        
        $html .= '      </div>'; // actions
        $html .= '    </div>'; // body
        $html .= '  </div>'; // slide item
    }
    
    $html .= '  </div>'; // slides container
    
    // Pagination & Navigation
    if ($slidesCount > 1) {
        $html .= '  <button class="slider-arrow prev" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; z-index: 10; transition: background 0.2s;" onclick="event.stopPropagation(); window.changeSlide_' . $sliderId . '(-1)">&lsaquo;</button>';
        $html .= '  <button class="slider-arrow next" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; z-index: 10; transition: background 0.2s;" onclick="event.stopPropagation(); window.changeSlide_' . $sliderId . '(1)">&rsaquo;</button>';
        
        $html .= '  <div class="slider-dots" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">';
        for ($k = 0; $k < $slidesCount; $k++) {
            $activeDotStyle = $k === 0 ? 'background: #fff; transform: scale(1.2);' : 'background: rgba(255,255,255,0.4);';
            $html .= '  <span class="slider-dot" style="width: 8px; height: 8px; border-radius: 50%; cursor: pointer; transition: all 0.3s; ' . $activeDotStyle . '" onclick="event.stopPropagation(); window.goToSlide_' . $sliderId . '(' . $k . ')"></span>';
        }
        $html .= '  </div>';
    }
    
    $html .= '</div>'; // premium-hero-slider
    
    // Add client script for the slider interaction
    if ($slidesCount > 1) {
        $html .= '
        <script>
        (function() {
            var slider = document.getElementById("' . $sliderId . '");
            if (!slider) return;
            var container = slider.querySelector(".slides-container");
            var dots = slider.querySelectorAll(".slider-dot");
            var currentIdx = 0;
            var slideCount = ' . $slidesCount . ';
            var interval;
            
            function updateSlider() {
                container.style.transform = "translateX(-" + (currentIdx * 100) + "%)";
                dots.forEach(function(dot, idx) {
                    if (idx === currentIdx) {
                        dot.style.background = "#fff";
                        dot.style.transform = "scale(1.2)";
                    } else {
                        dot.style.background = "rgba(255,255,255,0.4)";
                        dot.style.transform = "scale(1)";
                    }
                });
            }
            
            window.changeSlide_' . $sliderId . ' = function(direction) {
                currentIdx += direction;
                if (currentIdx >= slideCount) currentIdx = 0;
                if (currentIdx < 0) currentIdx = slideCount - 1;
                updateSlider();
                resetAutoplay();
            };
            
            window.goToSlide_' . $sliderId . ' = function(idx) {
                currentIdx = idx;
                updateSlider();
                resetAutoplay();
            };
            
            function startAutoplay() {
                interval = setInterval(function() {
                    window.changeSlide_' . $sliderId . '(1);
                }, 5000);
            }
            
            function resetAutoplay() {
                clearInterval(interval);
                startAutoplay();
            }
            
            startAutoplay();
        })();
        </script>
        ';
    }
    
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
            
        case 'hero_slider':
            $html = render_hero_slider($connection, $settings);
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

// ── Dispatcher ──────────────────────────────────────────────────────────
// The API router includes this file; we must call the function ourselves.

$action = isset($_GET['action']) ? trim($_GET['action']) : 'render';

if ($action === 'render') {
    render($connection, $_GET);
} else {
    echo json_encode(['status' => 400, 'msg' => 'Unknown action']);
}

