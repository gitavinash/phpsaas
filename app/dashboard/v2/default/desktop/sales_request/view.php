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
                <div class="widget no-padding box-1">
                    <div class="leadProfileCard">
                        <div class="img">
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower(((trim($enquiry_first_name[0]) != '') ? $enquiry_first_name[0] : 'unknown')) ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"
                                <?php echo (($enquiry_status == 4 || $enquiry_status == 5) ? "style='color:#f44336'" : "") ?>>
                                <?php echo $enquiry_first_name . ' ' . $enquiry_last_name . (($enquiry_status == 4) ? ' ( Closed ) ' : (($enquiry_status == 5) ? '( Duplicate )' : '')) ?>
                            </h2>
                            <p><span><strong>Last Update:
                                    </strong><?php echo date('d-m-Y h:i A', strtotime($enquiry_update)) ?></span></p>
                        </div>
                        <div class="verifications">

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="main-box">
            <section>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-file-circle-info"></i>
                            Customer details</a>
                    </div>
                </div>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a href="#" id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-file-circle-info"></i>
                            Agent details</a>
                    </div>
                </div>
                <div class="widget no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <table>
                            <?php

                            if ($enquiry_first_name != '') {
                                echo "<tr>
                                <td class='box-3'><strong>First name</strong></td>
                                <td class='box-7'>{$enquiry_first_name}</td>
                            </tr>";
                            }

                            if ($enquiry_middle_name != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Middle name</strong></td>
                                <td class='box-7'>{$enquiry_middle_name}</td>
                            </tr>";
                            }

                            if ($enquiry_last_name != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Last name</strong></td>
                                <td class='box-7'>{$enquiry_last_name}</td>
                            </tr>";
                            }

                            if ($enquiry_email != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Email</strong></td>
                                <td class='box-7'>{$enquiry_email}</td>
                            </tr>";
                            }

                            if ($enquiry_mobile != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Mobile</strong></td>
                                <td class='box-7'>{$enquiry_mobile}</td>
                            </tr>";
                            }

                            if ($enquiry_subject != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Tour package</strong></td>
                                <td class='box-7'>{$enquiry_subject}</td>
                            </tr>";
                            }

                            if ($date_of_travel != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Date of travel</strong></td>
                                <td class='box-7'>{$date_of_travel}</td>
                            </tr>";
                            }

                            if ($number_of_days != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Number of days</strong></td>
                                <td class='box-7'>{$number_of_days} Nights</td>
                            </tr>";
                            }

                            if ($number_of_adults != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Number of adults</strong></td>
                                <td class='box-7'>{$number_of_adults}</td>
                            </tr>";
                            }

                            if ($number_of_childrens != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Number of childrens</strong></td>
                                <td class='box-7'>{$number_of_childrens}</td>
                            </tr>";
                            }

                            if ($number_of_infants != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Number of infants</strong></td>
                                <td class='box-7'>{$number_of_infants}</td>
                            </tr>";
                            }

                            if ($current_location != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Current location</strong></td>
                                <td class='box-7'>{$current_location}</td>
                            </tr>";
                            }

                            if ($pickup_location != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Pickup location</strong></td>
                                <td class='box-7'>{$pickup_location}</td>
                            </tr>";
                            }

                            if ($drop_location != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Drop location</strong></td>
                                <td class='box-7'>{$drop_location}</td>
                            </tr>";
                            }

                            if ($requirements != '') {
                                echo "<tr>
                                <td class='box-3'><strong>Requirements</strong></td>
                                <td class='box-7'>{$requirements}</td>
                            </tr>";
                            } else {

                                $enquiryMessage = json_decode($enquiry_message, TRUE);

                                if (is_array($enquiryMessage)) {
                                    foreach ($enquiryMessage as $key => $value) {
                                        echo "<tr>
                                        <td class='box-3'><strong>" . str_replace('_', ' ', ucwords($key)) . "</strong></td>
                                        <td class='box-7'>{$value}</td>
                                    </tr>";
                                    }
                                }
                            }

                            if ($site_access_type != 'agent') {
                                if ($enquiry_source != '') {
                                    echo "<tr>
                                    <td class='box-3'><strong>Enquiry source</strong></td>
                                    <td class='box-7'>{$enquiry_source}</td>
                                </tr>";
                                }

                                if ($enquiry_medium != '') {
                                    echo "<tr>
                                    <td class='box-3'><strong>Enquiry medium</strong></td>
                                    <td class='box-7'>{$enquiry_medium}</td>
                                </tr>";
                                }

                                if ($enquiry_campaign != '') {
                                    echo "<tr>
                                    <td class='box-3'><strong>Enquiry campaign</strong></td>
                                    <td class='box-7'>{$enquiry_campaign}</td>
                                </tr>";
                                }
                            }

                            ?>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="widget no-padding box-2-5" id="fixedDiv">
        <div class="main-box">
            <section class="leadQuickLinks">
                <?php

                if ($enquiry_status != 5) {
                    if ($enquiry_status != 4) { ?>
                <a href="" class="btn btn-green" id="followUpBtn"><i class="fi-rr-alarm-clock"></i> Follow-ups</a>
                <?php

                        if (SITE_ID == 57) { ?>
                <a href="" class="btn btn-green" id="paymentBtn"><i class="fi-rr-indian-rupee-sign"></i> Send payment
                    links</a>
                <a href="" class="btn btn-green" id="updatePaymentBtn"><i class="fi-rr-notes"></i> Update payment
                    records</a>
                <a href="" class="btn btn-green" id="itineraryBtn"><i class="fi-rr-track"></i> Send itinerary</a>
                <a href="" class="btn btn-green" id="voucherBtn"><i class="fi-rr-ticket"></i> Send Vouchers</a>
                <?php
                        }

                        ?>
                <a href='#' data-type='sales' data-id='<?php echo $sales_id ?>' title='Delete sales'
                    class='btn btn-red deleteButton' data-reload='false' data-redirect='true'
                    data-url='/dashboard/sales_request/list/'><i class='fi fi-rr-trash bgRed'></i> Delete
                    request</a>

                <?php
                    } else { ?>

                <form method="POST" class="">
                    <!-- Design a form to assign lead to intern -->
                </form>

                <?php
                    }
                }

                ?>
            </section>
        </div>
    </div>
</div>