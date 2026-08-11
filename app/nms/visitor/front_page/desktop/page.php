<?php
require_once 'header.php';

$pageTitle = !empty($page_title) ? $page_title : 'Page';
$pageDescription = !empty($page_description) ? $page_description : 'Add your page content here to make the experience feel polished and easy to read.';
?>

<section class="page-shell">
    <div class="page-banner">
        <div class="container page-banner-inner">
            <div class="page-banner-copy">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
        </div>
    </div>

    <div class="container page-content-block">
        <article class="page-content-card">
            <div class="page-content-body">
                <?php echo $pageDescription; ?>
            </div>
        </article>
    </div>
</section>

<?php require_once 'footer.php'; ?>