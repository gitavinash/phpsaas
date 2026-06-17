<?php

require_once 'header.php';

?>
<!-- Page Header -->
<section class="inner-page-header" style="background: var(--bg-2, #f8fafc); padding: 60px 0 40px; border-bottom: 1px solid var(--border, #e2e8f0); margin-bottom: 40px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px; box-sizing: border-box;">
        <h1 style="font-size: 36px; font-weight: 800; color: var(--text-0, #1e293b); margin: 0 0 10px; font-family: 'Manrope', sans-serif;"><?php echo ucwords($page_title) ?></h1>
        <div class="breadcrumbs" style="font-size: 14px; color: var(--text-2, #64748b); font-family: 'Manrope', sans-serif;">
            <a href="/" style="color: var(--accent); text-decoration: none; font-weight: 500;">Home</a> 
            <span style="margin: 0 8px;">&rsaquo;</span> 
            <span><?php echo ucwords($page_title) ?></span>
        </div>
    </div>
</section>

<!-- Page Content -->
<main class="page-content-wrapper" style="font-family: 'Manrope', sans-serif;">
    <?php echo $page_description; ?>
</main>

<?php

require_once 'footer.php';

?>
