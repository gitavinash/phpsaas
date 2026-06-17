<?php
// Variables may be pre-set by router's extract($secure), but ensure they're defined
$id   = isset($_GET['id'])   ? trim($_GET['id'])   : '';
$type = isset($_GET['type']) ? mysqli_real_escape_string($connection, trim($_GET['type'])) : '';

$theme_header_file = '';
if (defined('USER_PATH') && file_exists(USER_PATH . 'header.php')) {
    $theme_header_file = USER_PATH . 'header.php';
} elseif (defined('DEFAULT_USER_PATH') && file_exists(DEFAULT_USER_PATH . 'header.php')) {
    $theme_header_file = DEFAULT_USER_PATH . 'header.php';
}

$theme_footer_file = '';
if (defined('USER_PATH') && file_exists(USER_PATH . 'footer.php')) {
    $theme_footer_file = USER_PATH . 'footer.php';
} elseif (defined('DEFAULT_USER_PATH') && file_exists(DEFAULT_USER_PATH . 'footer.php')) {
    $theme_footer_file = DEFAULT_USER_PATH . 'footer.php';
}

function get_clean_header_html($file)
{
    if (!$file || !file_exists($file)) return '';
    ob_start();
    try {
        $checkCart = false;
        include $file;
    } catch (Throwable $e) {
        // Ignore
    }
    $html = ob_get_clean();
    if (preg_match('/<body[^>]*>([\s\S]*)$/i', $html, $matchBody)) {
        $html = $matchBody[1];
    }
    if (preg_match('/([\s\S]*?)<main[^>]*>/i', $html, $matchMain)) {
        $html = $matchMain[1];
    }
    return trim($html);
}

function get_clean_footer_html($file)
{
    if (!$file || !file_exists($file)) return '';
    ob_start();
    try {
        include $file;
    } catch (Throwable $e) {
        // Ignore
    }
    $html = ob_get_clean();
    $html = ltrim(preg_replace('/^<\/main[^>]*>/i', '', trim($html)));
    $html = preg_replace('/<\/body\s*>/i', '', $html);
    $html = preg_replace('/<\/html\s*>/i', '', $html);
    return trim($html);
}

$header_html = get_clean_header_html($theme_header_file);
$footer_html = get_clean_footer_html($theme_footer_file);
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (($id == '') ? 'Add new ' : 'Edit ') . $type ?> | <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?php echo SITE_ICON; ?>">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY; ?>all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY; ?>uicons-regular-rounded.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY; ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="/config.min.css">
    <?php if (defined('DEFAULT_USER_STYLESHEET')): ?>
        <link rel="stylesheet" href="<?php echo DEFAULT_USER_STYLESHEET ?>checkout.min.css">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_LIBRARY; ?>admin_new.css">
    <?php if (defined('USER_STYLESHEET') && USER_STYLESHEET): ?>
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" id="visitor-theme-stylesheet">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY; ?>no_code_editor/no_code.css">
    <style>
        /* Dynamic accent variables — overrides static fallback in admin_new.css */
        :root {
            --accent: <?php echo SITE_THEME_COLOUR; ?>;
            --accent-dim: <?php echo SITE_THEME_COLOUR; ?>22;
            --accent-mid: <?php echo SITE_THEME_COLOUR; ?>55;
            --bg-0: #f8fafc;
            --bg-1: #ffffff;
            --bg-2: #f3f5f8;
            --bg-3: #ffffff;
            --bg-4: #e5e7eb;
            --border: #d1d5db;
            --text-0: #111827;
            --text-1: #374151;
            --text-2: #6b7280;
            --text-3: #9ca3af;
        }
    </style>
</head>

<body>

    <script>
        // Monkey-patch document.addEventListener to execute DOMContentLoaded callbacks immediately if the document is already loaded
        (function() {
            const originalAddEventListener = document.addEventListener;
            document.addEventListener = function(type, listener, options) {
                if (type === 'DOMContentLoaded' && (document.readyState === 'interactive' || document.readyState === 'complete')) {
                    try {
                        listener({
                            type: 'DOMContentLoaded',
                            target: document,
                            currentTarget: document
                        });
                    } catch (e) {
                        console.error("Error in deferred DOMContentLoaded listener:", e);
                    }
                }
                return originalAddEventListener.apply(this, arguments);
            };
        })();

        window.BUILDER_CONFIG = {
            id: "<?php echo htmlspecialchars($id); ?>",
            type: "<?php echo htmlspecialchars($type); ?>",
            theme_color: "<?php echo SITE_THEME_COLOUR; ?>",
            has_content: true,
            admin_url: "<?php echo rtrim(ADMIN_URL, '/'); ?>"
        };
    </script>

    <!-- ─── FULL BUILDER INTERFACE ───────────────────────────── -->
    <div class="builder" id="builder">

        <!-- TOP BAR -->
        <div class="topbar">
            <div class="topbar-l">
                <?php
                $url = '/dashboard/' . strtolower($type) . '/?type=' . (($id != '') ? 'edit' : 'add') . '&editor=true';

                if (($id != '')) {
                    $url .= '&id=' . urlencode($id);
                }
                ?>

                <a href="<?php echo $url; ?>" class="btn-back" title="Back to Dashboard">
                    <i class="fi fi-rr-arrow-left" aria-hidden="true"></i>
                </a>
                <div class="page-info">
                    <div class="title" id="pg-title">Loading…</div>
                    <div class="meta">
                        <span class="pill"><?php echo htmlspecialchars(ucwords($type)); ?></span>
                        <span>Visual Builder</span>
                    </div>
                </div>
            </div>

            <div class="topbar-c">
                <button class="dev-btn active" data-device="desktop" title="Desktop (D)"><i class="fi-rr-computer"></i></button>
                <button class="dev-btn" data-device="tablet" title="Tablet (T)"><i class="fi-rr-tablet"></i></button>
                <button class="dev-btn" data-device="mobile" title="Mobile (M)"><i class="fi-rr-smartphone"></i></button>
            </div>

            <div class="topbar-r">
                <div class="save-ind" id="save-ind">
                    <div class="save-dot"></div><span id="save-txt">Ready</span>
                </div>
                <button class="icon-btn" id="btn-undo" title="Undo · Ctrl+Z" disabled><i class="fi-rr-rotate-left"></i></button>
                <button class="icon-btn" id="btn-redo" title="Redo · Ctrl+Y" disabled><i class="fi-rr-rotate-right"></i></button>
                <button class="icon-btn" id="btn-help" title="Help &amp; Shortcuts"><i class="fi-rr-interrogation"></i></button>
                <button class="btn-preview" id="btn-preview" title="Preview (P)"><i class="fi-rr-eye"></i> Preview</button>
                <button class="btn-save" id="btn-save" title="Save · Ctrl+S"><i class="fi-rr-disk"></i> Save</button>
            </div>
        </div>

        <!-- WORKSPACE -->
        <div class="workspace">

            <!-- LEFT PANEL -->
            <div class="panel-l" id="panel-l">
                <div class="panel-tabs">
                    <button class="p-tab active" data-cat="all">All</button>
                    <button class="p-tab" data-cat="layout">Layout</button>
                    <button class="p-tab" data-cat="content">Content</button>
                    <button class="p-tab" data-cat="dynamic">Dynamic</button>
                </div>
                <div class="search-wrap">
                    <i class="fi-rr-search"></i>
                    <input type="text" id="sec-search" placeholder="Search sections…" autocomplete="off">
                </div>
                <div class="sections-list" id="sections-list">

                    <div class="cat-label" data-lbl="layout">Layout</div>
                    <div class="sec-grid">
                        <div class="sec-card" draggable="true" data-type="hero" data-cat="layout" data-kw="hero banner headline cta" title="Hero Banner: A prominent banner with a large heading, text, and Call-to-Action button.">
                            <i class="fi-rr-layout-fluid"></i><span>Hero Banner</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="text_image" data-cat="layout" data-kw="text image two column split" title="Text &amp; Image: A flexible 2-column layout with text on one side and an image on the other.">
                            <i class="fi-rr-text-box-edit"></i><span>Text &amp; Image</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="features" data-cat="layout" data-kw="features grid cards icons" title="Feature Grid: Showcase your key features or services in a clean column grid layout.">
                            <i class="fi-rr-grid"></i><span>Feature Grid</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="why_choose_us" data-cat="layout" data-kw="why choose us benefits checks checklist features list highlights" title="Why Choose Us: Split section highlighting benefits with customizable icons.">
                            <i class="fi-rr-list-check"></i><span>Why Choose Us</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="about_doctor" data-cat="layout" data-kw="about doctor profile bio physician team founder credentials" title="About Doctor: Profile banner detailing credentials and background of doctors or team members.">
                            <i class="fi-rr-user-md"></i><span>About Doctor</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="services_list" data-cat="layout" data-kw="services grid treatments specialties cards list" title="Services Grid: Visual cards showcasing services, treatments, or specialties.">
                            <i class="fi-rr-briefcase"></i><span>Services Grid</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="spacer" data-cat="layout" data-kw="spacer divider gap hr" title="Spacer: Add adjustable vertical spacing or divider lines between sections.">
                            <i class="fi-rr-resize"></i><span>Spacer / HR</span>
                        </div>
                    </div>

                    <div class="cat-label" data-lbl="content">Content</div>
                    <div class="sec-grid">
                        <div class="sec-card" draggable="true" data-type="faq" data-cat="content" data-kw="faq accordion questions" title="FAQ Accordion: Interactive collapsable questions and answers.">
                            <i class="fi-rr-interrogation"></i><span>FAQ</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="testimonials" data-cat="content" data-kw="testimonials reviews quotes" title="Testimonials: Display quotes and positive reviews from your customers.">
                            <i class="fi-rr-comments"></i><span>Testimonials</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="cta" data-cat="content" data-kw="call to action cta banner" title="Call to Action: A full-width colorful banner to grab user attention and drive clicks.">
                            <i class="fi-rr-megaphone"></i><span>Call to Action</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="contact_info" data-cat="content" data-kw="contact address telephone email map layout info" title="Contact &amp; Map: Address details, phone, email cards, and embed map.">
                            <i class="fi-rr-envelope"></i><span>Contact &amp; Map</span>
                        </div>
                        <div class="sec-card" draggable="true" data-type="custom_html" data-cat="content" data-kw="custom html code raw embed" title="Custom HTML: Embed custom code, raw scripts, videos, or external iframe widgets.">
                            <i class="fi-rr-brackets-curly"></i><span>Custom HTML</span>
                        </div>
                    </div>

                    <div class="cat-label" data-lbl="dynamic">Dynamic</div>
                    <div class="sec-grid">
                        <div class="sec-card" draggable="true" data-type="recent_articles" data-cat="dynamic" data-kw="recent articles blog posts feed" title="Recent Articles: Dynamically display the latest posts from your blog feed.">
                            <i class="fi-rr-rss"></i><span>Recent Articles</span>
                        </div>
                    </div>

                    <p class="no-results" id="no-results"><i class="fi-rr-search"></i>No sections found</p>
                </div>
            </div>

            <!-- CANVAS -->
            <div class="canvas-area" id="canvas-area">
                <div class="canvas-ruler">
                    <div class="canvas-label" id="canvas-label"><i class="fi-rr-computer"></i> <span>Desktop — 1200px</span></div>
                </div>
                <div class="canvas-frame" id="canvas">
                    <?php if (!empty($header_html)): ?>
                        <div id="builder-header">
                            <?php echo $header_html; ?>
                        </div>
                    <?php endif; ?>

                    <div id="canvas-sections">
                        <div class="skeleton-wrap" id="skel">
                            <div class="skel" style="height:220px"></div>
                            <div class="skel" style="height:56px;width:55%"></div>
                            <div class="skel" style="height:100px"></div>
                            <div class="skel" style="height:56px;width:70%"></div>
                        </div>
                    </div>

                    <?php if (!empty($footer_html)): ?>
                        <div id="builder-footer">
                            <?php echo $footer_html; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="panel-r" id="panel-r">
                <div class="panel-r-head">
                    <div class="props-tabs" id="props-tabs" style="display: none;">
                        <button class="props-tab active" data-tab="themes" title="Layout Templates"><i class="fi-rr-layout-fluid"></i> Layouts</button>
                        <button class="props-tab" data-tab="design" title="Custom CSS Design"><i class="fi-rr-palette"></i> Design</button>
                        <button class="props-tab" data-tab="content" title="Section Block Content"><i class="fi-rr-document-signed"></i> Content</button>
                    </div>
                    <h2 id="props-title">Properties</h2>
                </div>
                <div class="panel-r-body" id="props-body">
                    <div class="empty-panel">
                        <i class="fi-rr-cursor"></i>
                        <p>Click on a <strong>section</strong> or <strong>block</strong> inside the canvas to edit its properties.</p>
                    </div>
                </div>
            </div>

        </div><!-- /.workspace -->
    </div><!-- /.builder -->

    <!-- HELP MODAL -->
    <div class="help-modal" id="help-modal">
        <div class="help-modal-content">
            <div class="help-modal-header">
                <h3><i class="fi-rr-interrogation"></i> Editor Guide &amp; Shortcuts</h3>
                <button class="close-help" id="close-help">&times;</button>
            </div>
            <div class="help-modal-body">
                <div class="help-section">
                    <h4><i class="fi-rr-mouse-pointer"></i> Visual Builder Basics</h4>
                    <ul>
                        <li><strong>Click to Select:</strong> Click on any section or block inside the canvas to edit its styles and settings in the right properties panel.</li>
                        <li><strong>Direct Text Edit:</strong> Click and type directly on any heading, paragraph, or button text inside the canvas.</li>
                        <li><strong>Image &amp; Link Edit:</strong> Click on any image or link inside the canvas to open its dedicated quick editor in the right sidebar.</li>
                        <li><strong>Responsive Previews:</strong> Click the computer, tablet, and smartphone icons at the top center to toggle viewport widths.</li>
                    </ul>
                </div>
                <div class="help-section">
                    <h4><i class="fi-rr-keyboard"></i> Keyboard Shortcuts</h4>
                    <div class="shortcut-list">
                        <div class="shortcut-item"><kbd>Ctrl</kbd>+<kbd>S</kbd> <span>Save layout</span></div>
                        <div class="shortcut-item"><kbd>Ctrl</kbd>+<kbd>Z</kbd> <span>Undo change</span></div>
                        <div class="shortcut-item"><kbd>Ctrl</kbd>+<kbd>Y</kbd> <span>Redo change</span></div>
                        <div class="shortcut-item"><kbd>P</kbd> <span>Toggle Preview</span></div>
                        <div class="shortcut-item"><kbd>Esc</kbd> <span>Deselect all</span></div>
                        <div class="shortcut-item"><kbd>D</kbd>/<kbd>T</kbd>/<kbd>M</kbd> <span>Device width</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Scripts ───────────────────────────────────────────── -->
    <script src="<?php echo PLUGIN_LIBRARY; ?>sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY; ?>no_code_editor/no_code.js"></script>
</body>

</html>