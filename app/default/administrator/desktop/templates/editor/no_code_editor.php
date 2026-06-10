<?php
// Variables may be pre-set by router's extract($secure), but ensure they're defined
$id   = isset($_GET['id'])   ? trim($_GET['id'])   : '';
$type = isset($_GET['type']) ? mysqli_real_escape_string($connection, trim($_GET['type'])) : '';
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web page builder | <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?php echo SITE_ICON; ?>">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY; ?>all.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY; ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_LIBRARY; ?>admin_new.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        /* ── Builder-only base resets (scoped to this standalone page) ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', system-ui, sans-serif;
            background: #111113;
            color: #dcdce8;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #303038;
            border-radius: 6px
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #454550
        }

        /* Dynamic accent variables — overrides static fallback in admin_new.css */
        :root {
            --accent: <?php echo SITE_THEME_COLOUR; ?>;
            --accent-dim: <?php echo SITE_THEME_COLOUR; ?>22;
            --accent-mid: <?php echo SITE_THEME_COLOUR; ?>55;
        }
    </style>

</head>

<body>

    <script>
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
                <a href="/dashboard/<?php echo strtolower($type); ?>/?type=edit&editor=true&id=<?php echo $id ?>" class="btn-back" title="Back to Dashboard">
                    <i class="fi-rr-arrow-left"></i>
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
                        <div class="sec-card" data-type="hero" data-cat="layout" data-kw="hero banner headline cta">
                            <i class="fi-rr-layout-fluid"></i><span>Hero Banner</span>
                        </div>
                        <div class="sec-card" data-type="text_image" data-cat="layout" data-kw="text image two column split">
                            <i class="fi-rr-columns"></i><span>Text &amp; Image</span>
                        </div>
                        <div class="sec-card" data-type="features" data-cat="layout" data-kw="features grid cards icons">
                            <i class="fi-rr-grid"></i><span>Feature Grid</span>
                        </div>
                        <div class="sec-card" data-type="spacer" data-cat="layout" data-kw="spacer divider gap hr">
                            <i class="fi-rr-resize"></i><span>Spacer / HR</span>
                        </div>
                    </div>

                    <div class="cat-label" data-lbl="content">Content</div>
                    <div class="sec-grid">
                        <div class="sec-card" data-type="faq" data-cat="content" data-kw="faq accordion questions">
                            <i class="fi-rr-interrogation"></i><span>FAQ</span>
                        </div>
                        <div class="sec-card" data-type="testimonials" data-cat="content" data-kw="testimonials reviews quotes">
                            <i class="fi-rr-comments"></i><span>Testimonials</span>
                        </div>
                        <div class="sec-card" data-type="cta" data-cat="content" data-kw="call to action cta banner">
                            <i class="fi-rr-megaphone"></i><span>Call to Action</span>
                        </div>
                        <div class="sec-card" data-type="custom_html" data-cat="content" data-kw="custom html code raw embed">
                            <i class="fi-rr-code"></i><span>Custom HTML</span>
                        </div>
                    </div>

                    <div class="cat-label" data-lbl="dynamic">Dynamic</div>
                    <div class="sec-grid">
                        <div class="sec-card" data-type="recent_articles" data-cat="dynamic" data-kw="recent articles blog posts feed">
                            <i class="fi-rr-rss"></i><span>Recent Articles</span>
                        </div>
                    </div>

                    <p class="no-results" id="no-results"><i class="fi-rr-search"></i>No sections found</p>
                </div>
            </div>

            <!-- CANVAS -->
            <div class="canvas-area" id="canvas-area">
                <div class="canvas-ruler">
                    <div class="canvas-label" id="canvas-label">Desktop — 1200px</div>
                </div>
                <div class="canvas-frame" id="canvas">
                    <div class="skeleton-wrap" id="skel">
                        <div class="skel" style="height:220px"></div>
                        <div class="skel" style="height:56px;width:55%"></div>
                        <div class="skel" style="height:100px"></div>
                        <div class="skel" style="height:56px;width:70%"></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="panel-r" id="panel-r">
                <div class="panel-r-head">
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

    <!-- ─── Scripts ───────────────────────────────────────────── -->
    <script src="<?php echo PLUGIN_LIBRARY; ?>sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY; ?>no_code_editor/no_code.js"></script>
</body>

</html>