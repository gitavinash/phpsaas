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
                    <table class='stockInfo'>
                        <tr class="rowBox">
                            <th class="cell-content">S No.</th>
                            <th class="cell-content">Block name</th>
                            <th class="cell-content">Slabs ( Size and Pcs )</th>
                            <th class="cell-content">Total Pcs.</th>
                        </tr>
                        <?php
                        $blockData = json_decode($sales_data, TRUE);
                        $i = 0;
                        $totalBlockSlabs = 0;
                        foreach ($blockData as $key => $value) {
                            $keyArray = (explode('_', str_replace('salesBlocks_', '', $key)));
                            $productName = $keyArray[1];
                            echo "
                                <tr class='rowBox'>
                                <td class='rowSno cell-content stockInfoTitle'>" . ++$i . "</td>
                                <td class='rowTitle cell-content stockInfoTitle' >{$productName}</td>
                                ";
                            // Step 1: Explode the string by commas to get individual objects
                            $objects = explode(",", $value);

                            // Step 2: Extract the values and count occurrences
                            $valuesCount = [];
                            $totalSlabs = 0;

                            foreach ($objects as $object) {
                                // Explode each object by underscore and take the second part (the actual value)
                                list($number, $value) = explode("_", $object);

                                // Count occurrences of each value
                                if (isset($valuesCount[$value])) {
                                    $valuesCount[$value]++;
                                } else {
                                    $valuesCount[$value] = 1;
                                }
                                $totalBlockSlabs++;
                                $totalSlabs++;
                            }
                            echo "<td class='slabsDetails'>";
                            foreach ($valuesCount as $value => $count) {
                                echo "<p class='stockInfoList'>{$value}<span>{$count}</span></p>";
                            }
                            echo "
                                </td>
                                <td class='totalSlabs' >{$totalSlabs}</td>
                                </tr>
                            ";
                        }
                        echo "<tr class='rowBox'>
                                <td></td>
                                <td></td>
                                <td class='totalSlabs'>Total slabs</td>
                                <td class='totalSlabs'>{$totalBlockSlabs}</td>
                            </tr>";
                        ?>
                    </table>
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