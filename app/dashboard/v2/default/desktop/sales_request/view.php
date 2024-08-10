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
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-review"></i>
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
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-id-card-clip-alt"></i>
                            Agent details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $profile_display_name ?></p>
                        <p><strong>Mobile</strong><?php echo $profile_mobile ?></p>
                    </div>
                </div>
                <div class="widget no-padding box-1">
                    <div class="btn-group leadActions">
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-to-do"></i>
                            Inventory details</a>
                    </div>
                    <table>
                        <?php
                        $blockData = json_decode($sales_data, TRUE);
                        for ($i = 0; $i < sizeof($blockData); $i++) {
                            echo "<tr>";
                            foreach ($blockData[$i] as $value) {
                                print_r($value);
                            }
                            print_r($blockData[$i]);
                            // $slabsDetails = explode(",", $blockData);
                            // print_r($slabsDetails);
                            echo "</tr>";
                        }
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
                <a href='#' data-type='sales' data-id='<?php echo $sales_id ?>' title='Delete sales'
                    class='btn btn-red deleteButton' data-reload='false' data-redirect='true'
                    data-url='/dashboard/sales_request/list/'><i class='fi fi-rr-trash bgRed'></i> Delete
                    request</a>
            </section>
        </div>
    </div>
</div>