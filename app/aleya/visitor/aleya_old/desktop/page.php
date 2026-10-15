<?php
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Aleya';
$siteDesc = defined('SITE_DESC') ? SITE_DESC : 'Aleya';

$meta_title = !empty($page_title) ? ($page_title . ' | ' . $siteName) : $siteDesc;

$cleanDescription = trim(preg_replace('/\s+/', ' ', strip_tags((string)($page_description ?? ''))));
$meta_description = !empty($cleanDescription) ? substr($cleanDescription, 0, 160) : $siteDesc;
?>

<title><?php echo htmlspecialchars($meta_title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?>">

<style>
.editor-page-shell {
    background: linear-gradient(180deg, #0f172a 0%, #111827 36%, #f8fafc 36%, #f8fafc 100%);
    padding: 56px 0 72px;
}

.editor-page-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 16px;
}

.editor-page-hero {
    color: #f8fafc;
    margin-bottom: 28px;
}

.editor-page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 13px;
    margin-bottom: 12px;
    color: rgba(248, 250, 252, 0.78);
}

.editor-page-breadcrumb a {
    color: #e2e8f0;
    text-decoration: none;
}

.editor-page-breadcrumb a:hover {
    color: #ffffff;
}

.editor-page-title {
    margin: 0;
    font-size: clamp(1.8rem, 4vw, 2.7rem);
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.editor-page-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 16px 40px rgba(2, 6, 23, 0.12);
    padding: clamp(20px, 4vw, 40px);
    border: 1px solid #e2e8f0;
}

.editor-page-content {
    color: #334155;
    font-size: 16px;
    line-height: 1.8;
}

.editor-page-content h1,
.editor-page-content h2,
.editor-page-content h3,
.editor-page-content h4,
.editor-page-content h5,
.editor-page-content h6 {
    color: #0f172a;
    line-height: 1.3;
    margin: 0 0 14px;
}

.editor-page-content p,
.editor-page-content ul,
.editor-page-content ol,
.editor-page-content blockquote {
    margin-bottom: 14px;
}

.editor-page-content ul,
.editor-page-content ol {
    padding-left: 20px;
}

.editor-page-content a {
    color: #0f766e;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.editor-page-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

@media (max-width: 767px) {
    .editor-page-shell {
        padding: 38px 0 56px;
        background: linear-gradient(180deg, #0f172a 0%, #111827 28%, #f8fafc 28%, #f8fafc 100%);
    }
}
</style>

<section class="editor-page-shell" id="feature">
    <div class="editor-page-wrap">
        <header class="editor-page-hero">
            <div class="editor-page-breadcrumb">
                <a href="/">Home</a>
                <span>/</span>
                <span><?php echo $page_title; ?></span>
            </div>
            <h1 class="editor-page-title"><?php echo $page_title; ?></h1>
        </header>

        <article class="editor-page-card">
            <div class="editor-page-content">
                <?php echo $page_description; ?>
            </div>
        </article>
    </div>
</section>