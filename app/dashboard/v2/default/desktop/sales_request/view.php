<?php

if (!isset($id)) { ?>
<script>
alert('Sales request id is missing');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
} else {
    if ($id == '') { ?>
<script>
alert('Sales request id cannot be blank');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
    } else {
        $salesId = base64_decode($id);
    }
}

if ($checkSalesRequest = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=sales_agent_id WHERE sales_status=0 AND sales_id='{$salesId}' AND sales_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSalesRequest) == 0) { ?>
<script>
alert('Invalid Sales request id found');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
    } else {
        extract(mysqli_fetch_assoc($checkSalesRequest));
    }
} else {
    echo mysqli_error($connection);
}

?>
<h2 class="heading">Sales request details<span>Complete information of sales request</span></h2>
<div class="box-1">

    <div class="widget box-7-5 no-padding">
        <div class="main-box">
            <section>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="laadDetailsBtn" class="btn btn-green"><i class="fi-rr-review"></i>
                            Customer details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $sales_name ?></p>
                        <p><strong>Email</strong><?php echo $sales_email ?></p>
                        <p><strong>Mobile</strong><?php echo $sales_mobile ?></p>
                        <p><strong>Address</strong><?php echo $sales_address ?></p>
                    </div>
                </div>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-id-card-clip-alt"></i>
                            Salesman details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $profile_display_name ?></p>
                        <p><strong>Mobile</strong><?php echo $profile_mobile ?></p>
                    </div>
                </div>
                <div class="widget no-padding box-1">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-to-do"></i>
                            Inventory details</a>
                    </div>

                    <div class="full-width">
                        <div class="tableHeader">
                            <div style="float:left; width: 50px;">S No.</div>
                            <div style="float:left; width: 250px;">Block Name</div>
                            <div style="float:left; min-width: 500px;">
                                Slabs Details
                                <div class="row header-row">
                                    <div style="float:left;">Size</div>
                                    <div style="float:left;">Qty</div>
                                    <div style="float:left;">Gross Qty.</div>
                                    <div style="float:left;">Damage Qty.</div>
                                    <div style="float:left;">Net Qty.</div>
                                </div>
                            </div>
                        </div>

                        <?php

                        $blockData = json_decode($sales_data, TRUE);
                        $i = 0;
                        $totalBlockSlabs = 0;

                        foreach ($blockData as $key => $value) {
                            $keyArray = explode('_', str_replace('salesBlocks_', '', $key));
                            $productName = $keyArray[1];

                            echo "<div class='full-width'>";
                            echo "<div style='float:left; width: 50px;'>" . ++$i . "</div>";
                            echo "<div style='float:left; width: 250px;'>{$productName}</div>";

                            // Exploding string and processing data
                            $objects = explode(",", $value);
                            $valuesCount = [];

                            foreach ($objects as $object) {
                                list($number, $slabDimension, $grossSize, $damageSize, $netSize) = explode("_", $object);
                                if (isset($valuesCount[$slabDimension])) {
                                    $valuesCount[$slabDimension]++;
                                } else {
                                    $valuesCount[$slabDimension] = 1;
                                }
                                $totalBlockSlabs++;
                            }

                            // Display slab details
                            echo "<div class='slabs-details' style='float:left; min-width: 500px;' >";
                            foreach ($valuesCount as $slabDimension => $count) {
                                echo "<div class='full-width'>";
                                echo "<div style='float:left;'>{$slabDimension}</div>";
                                echo "<div style='float:left;'>{$count}</div>";
                                echo "<div style='float:left;'>{$grossSize}</div>";
                                echo "<div style='float:left;'>{$damageSize}</div>";
                                echo "<div style='float:left;'>{$netSize}</div>";
                                echo "</div>";
                            }
                            echo "</div>";  // End of slabs-details
                            echo "</div>";  // End of row
                        }

                        ?>

                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="widget no-padding box-2-5" id="fixedDiv">
        <div class="main-box">
            <section class="leadQuickLinks">
                <a href="" class="btn btn-green" id="approveSalesRequest"><i class="fi-rr-check"></i> Approve
                    request</a>
                <a href="" class="btn btn-green" id="approveSalesRequest"><i class="fi-rr-print"></i> Print</a>
                <a href='#' data-type='sales' data-id='<?php echo $sales_id ?>' title='Delete sales'
                    class='btn btn-red deleteButton' data-reload='false' data-redirect='true'
                    data-url='/dashboard/sales_request/list/'><i class='fi fi-rr-trash bgRed'></i> Delete
                    request</a>
            </section>
        </div>
    </div>
</div>