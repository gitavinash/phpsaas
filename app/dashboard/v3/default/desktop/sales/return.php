<?php

if (!isset($id)) { ?>
    <script>
        alert('Return request id is missing');
        window.location.href = '/dashboard/sales_request/list/';
    </script>
    <?php
} else {
    if ($id == '') { ?>
        <script>
            alert('Return request id cannot be blank');
            window.location.href = '/dashboard/sales_request/list/';
        </script>
    <?php
    } else {
        $salesId = base64_decode($id);
    }
}

if ($checkSalesRequest = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=sales_agent_id INNER JOIN " . TABLE_PREFIX . "warehouse ON warehouse_id=sales_warehouse_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id WHERE sales_id='{$salesId}' AND sales_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSalesRequest) == 0) { ?>
        <script>
            alert('Invalid Return request id found');
            window.location.href = '/dashboard/sales/list/';
        </script>
<?php
    } else {
        extract(mysqli_fetch_assoc($checkSalesRequest));
    }
} else {
    echo mysqli_error($connection);
}

?>
<h2 class="heading">Sales return details<span>Return inventory to warehouse</span></h2>
<div class="box-1">

    <div class="main-box">
        <section>
            <ul class="steps-tab">
                <li class="step-box active" id="salesStep_1"><span>1</span>Search
                    record</li>
                <li class="step-box" id="salesStep_2"><span>2</span>Select
                    inventory</li>
                <li class="step-box" id="salesStep_3"><span>3</span>Confirmation</li>
            </ul>
            <div class="steps-box">
                <form action="#" id="addNewSales" method="POST" enctype="multipart/form-data">
                    <section class="form">
                        <div class="step-open-box salesStep_1_box">
                            <div class="box-1">
                                <div class="form-header">
                                    <h1>Search sales record</h1>
                                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                                </div>
                                <div class="form-group">
                                    <input id="salesId" name="salesId" value="<?php echo base64_decode($id) ?>"
                                        type="number" onwheel="this.blur()" required />
                                    <label class="form-lable" id="Label">Sales id</label>
                                </div>
                                <div class="form-group">
                                    <input id="mobile" name="mobile" value="<?php echo $sales_mobile ?>" type="number"
                                        required />
                                    <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                                </div>
                                <div class="form-group">
                                    <!-- <input type="button" cla¯ss="btn btn-green step-box" id="salesStep_2" value="N new sale"> -->
                                    <a class="btn btn-green" style="float: right;" id="returnInventory">Next, select
                                        inventory<i class="fi fi-rr-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="step-open-box salesStep_2_box" style="display: none;">
                            <div class="box-1">
                                <div class="form-header">
                                    <h1>Select inventory for return</h1>
                                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                                </div>
                                <div class="form-group">
                                    <div class="returnBlockList">
                                        <div class='form-group-box returnSlabsListOptions'>
                                            <div class='box-3-2 left p-right-10'>
                                                <div class='form-group'>
                                                    <div class='input-box'>
                                                        <input type='checkbox' id='selectAllSlabs' />
                                                        <label class='radio-button'>Select All</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="box-5">
                                    <div class="form-group">
                                        <a class="btn btn-green" id="returnToForm1"><i class="fi fi-rr-arrow-left"></i>
                                            Back</a>
                                    </div>
                                </div>
                                <div class="box-5">
                                    <a class="btn btn-green" style="float: right;" id="confirmReturnInventory">Next,
                                        Confirm return
                                        <i class="fi fi-rr-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
            </div>
            <div class="step-open-box salesStep_3_box" style="display: none;">
                <div class="box-1">
                    <div class="form-header">
                        <h1>Confirmation</h1>
                        <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                    </div>
                    <div class="form-group">
                        <div class="returnBlockList"></div>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" value="<?php echo $sales_mobile ?>" type="number" onwheel="this.blur()" />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <div class="box-5">
                            <div class="form-group">
                                <a class="btn btn-green" id="returnToForm2"><i class="fi fi-rr-arrow-left"></i> Back</a>
                            </div>
                        </div>
                        <div class="box-5">
                            <a class="btn btn-green step-box" style="float: right;" id="submitReturn"><i
                                    class="fi fi-rr-check"></i> Submit
                                request</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </form>
    </div>
</div>