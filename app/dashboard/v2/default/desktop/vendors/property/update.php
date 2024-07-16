<h2 class="heading"><?php echo 'Add New property' ?><span></span>
</h2>
<div class="main-box">
    <?php

    $updateType = ((isset($updateType)) ? $updateType : 'property');
    $leadFormPath = $dashboardDirectory . '/dashboard/app/form/' . SITE_APP;
    $leadForm = $leadFormPath . "/update-{$updateType}.php";
    if (file_exists($leadForm)) {
        require_once $leadForm;
    } else { ?>
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Inventory details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateInventoryForm" method="POST" enctype="multipart/form-data">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Form is missing</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/missing.png" alt="">
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
    <?php
    }

    ?>
</div>