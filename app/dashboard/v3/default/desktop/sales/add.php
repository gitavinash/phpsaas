<h2 class="heading"><?php echo 'Add new sale' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <?php
        $leadFormPath = $dashboardDirectory . '/dashboard/app/form/' . SITE_APP;
        $leadForm = $leadFormPath . '/sales.php';
        if (!file_exists($leadForm)) {
            $leadForm =
                $dashboardDirectory . '/dashboard/app/form/default/lead.php';
        }

        require_once $leadForm;

        ?>
    </section>
</div>