<h2 class="heading"><?php echo 'Add New inventory' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Inventory details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewInventoryForm" method="POST">
                <section class="form">

                    <?php

                    $leadFormPath = $dashboardDirectory . '/dashboard/app/form/' . SITE_APP;
                    $leadForm = $leadFormPath . '/inventory.php';
                    if (!file_exists($leadForm)) {
                        $leadForm =
                            $dashboardDirectory . '/dashboard/app/form/default/lead.php';
                    }

                    require_once $leadForm;

                    ?>

                </section>
            </form>
        </div>
    </section>
</div>