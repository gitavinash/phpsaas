<h2 class="heading"><?php echo 'Add New lead' ?><span>Insert new lead into CRM</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Lead details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
                <?php

                $leadFormPath = $dashboardDirectory . '/dashboard/app/form/' . SITE_APP;
                $leadForm = $leadFormPath . '/lead.php';
                if (!file_exists($leadForm)) {
                    $leadForm =
                        $dashboardDirectory . '/dashboard/app/form/default/lead.php';
                }

                require_once $leadForm;

                ?>
            </form>
        </div>
    </section>
</div>