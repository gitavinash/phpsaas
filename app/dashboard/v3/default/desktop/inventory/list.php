<h2 class="heading">Inventory list<span>All Inventory list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/inventory/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="list-box">
            <?php

            $leadFormPath = $dashboardDirectory . '/dashboard/app/list/' . SITE_APP;
            $leadForm = $leadFormPath . '/list-inventory.php';
            if (file_exists($leadForm)) {
                require_once $leadForm;
            } else { ?>
                <table class="showTableList">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Address</th>
                            <th>City</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td colspan="5">Table informartion is missing</td>
                        </tr>
                    </thead>
                </table>
            <?php
            }

            ?>
        </div>
    </section>
</div>