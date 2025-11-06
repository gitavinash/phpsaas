<h2 class="heading">Check-in<span>All membership visits are redeemed here</span></h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewInventoryForm" method="POST" enctype="multipart/form-data">
            <section class="form">
                <div id="loader" style="display: none; text-align: center;">
                    <img src="<?php echo IMG_LIBRARY ?>circle-loader.gif" alt="Loading..." />
                </div>
                <div class="step-open-box step_1_box">
                    <?php

                    if (isset($siteAccessMeta['warehouse'])) {
                        if ($siteAccessMeta['warehouse'] == 'all') {
                            $showWarehouse = 'all';
                        } else {
                            $showWarehouse = $siteAccessMeta['warehouse'];
                        }
                    } else {
                        $showWarehouse = 'all';
                    }

                    if ($showWarehouse == "all") {
                    ?>

                    <div class="form-group">
                        <select class="selectJS form-control" name="warehouseId" id="warehouseId">
                            <option value="select">Select branch</option>
                            <?php

                                if ($warehouseList = mysqli_query($connection, "SELECT warehouse_id, warehouse_title, warehouse_code, warehouse_title, company_title FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_status=1 AND company_address_id!=0 AND warehouse_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($warehouseList) > 0) {
                                        $i = 0;
                                        while ($row = mysqli_fetch_array($warehouseList)) {
                                            echo "<option value='{$row['warehouse_id']}' " . (($i == 0) ? 'selected' : '') . ">{$row['warehouse_title']}</option>";
                                            $i++;
                                        }
                                    }
                                }

                                ?>
                        </select>
                        <label class="form-lable" id="Label">Branch<sup>*</sup></label>
                    </div>

                    <?php
                    } else {
                        echo "<input type='hidden' name='warehouseId' value='{$showWarehouse}' id='warehouseId'>";
                    }

                    ?>
                    <div class="form-group">
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number" autofocus required />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visits" name="visits" value="1" type="number" required />
                        <label class="form-lable" id="Label">How many visits to redeem?<sup>*</sup></label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-primary" id="checkInMembership" value="Continue">
                        </div>
                    </div>
                </div>
                <div class="step-open-box step_2_box" style="display:none">
                    <div class="stepNavigation">
                        <button type="button" class="btn btn-primary backToStep1">
                            <i class="fi-rr-arrow-left"></i> Back
                        </button>
                    </div>
                    <div class="showMembershipList"></div>
                </div>
                <div class="step-open-box step_3_box" style="display:none">
                    <div class="stepNavigation">
                        <button type="button" class="btn btn-primary backToStep2">
                            <i class="fi-rr-arrow-left"></i> Back
                        </button>
                    </div>
                    <div class="allotRoomAndStaff">
                        <p class="formBoxHeading">Select for 1st Person</p>
                        <div class="form-group">
                            <input id="duration" name="duration" value="60" type="number" required />
                            <label class="form-lable" id="Label">Massage duration</label>
                        </div>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="hidden" name="membershipId" value="">
                            <input type="hidden" name="visits" value="">
                            <input type="button" class="btn btn-green" id="startRedeemVisit" value="Send OTP">
                        </div>
                    </div>
                </div>
                <div class="step-open-box step_4_box" style="display:none">
                    <div class="stepNavigation">
                        <button type="button" class="btn btn-primary backToStep2">
                            <i class="fi-rr-arrow-left"></i> Back
                        </button>
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <input id="otpValue" name="otpValue" placeholder="123456" type="number" required />
                        <label class="form-lable" id="Label">Verify OTP<sup>*</sup></label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="hidden" id="otpRefId" name="otpRefId" value="">
                            <input type="button" class="btn btn-green" id="redeemVisit" value="Verify OTP">
                        </div>
                        <div class="form-group">
                            <input type="button" class="btn btn-blue" id="resendOTPBtn" value="Resend
                                OTP">
                        </div>
                    </div>
                </div>
                <div class="step-open-box step_5_box" style="display:none">
                    <div class="success-card">
                        <div class="success-hero">
                            <img src="<?php echo IMG_LIBRARY ?>green-tick-animation.gif">
                        </div>

                        <h2 class="success-title">Thank you!</h2>
                        <p class="success-text">The visit has been redeemed successfully.</p>

                        <div class="success-actions">
                            <button type="button" class="btn btn-light backToStep1">
                                <i class="fi-rr-arrow-left"></i> New check-in
                            </button>
                            <a href="/dashboard/" class="btn btn-green">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="step-open-box step_6_box historyBox" style="display:none">
                    <h2 style="font-size:24px;font-weight:700;margin-bottom:12px; margin-left: 5px;">
                        Visit Logs
                    </h2>
                    <div class="showVisitorHistory"></div>
                    <div class="stepNavigation">
                        <button type="button" style="width: 100%;" class="btn btn-primary backToStep2">
                            <i class="fi-rr-arrow-left"></i> Back
                        </button>
                    </div>
                </div>
                <div class="step-open-box step_7_box clearDueBox" style="display:none">
                    <h2 style="font-size:24px;font-weight:700;margin-bottom:12px; margin-left: 5px;">
                        Collect Due Payment
                    </h2>
                    <div class="form-group" style="margin-top: 20px;">
                        <input id="amount" name="amount" placeholder="1000" type="number" required />
                        <label class="form-lable" id="Label">Amount ( In ₹ )<sup>*</sup></label>
                    </div>
                    <div class="form-group" style="margin-bottom:10px;">
                        <select class="selectJS form-control" id="paymentMode" name="paymentMode" required
                            style="width:100%;height:44px;border:1px solid #dfe3e8;border-radius:8px;padding:0 10px;font-size:16px;background:#fff;">
                            <option value="">Select mode</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                        <label class="form-lable" id="paymentModeLabel">Payment Mode <sup>*</sup></label>
                    </div>
                    <div class="form-group" style="margin-top: 20px; margin-bottom:10px;">
                        <textarea name="comment" id="comment" placeholder="Any comment?"></textarea>
                        <label class="form-lable" id="Label">Notes (optional)</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="hidden" id="membershipId" name="membershipId" value="">
                            <input type="button" class="btn btn-green" id="collectPaymentBtn" value="Collect Payment">
                        </div>
                        <div class="form-group">
                            <button type="button" style="width: 100%;" class="btn btn-primary backToStep2">
                                <i class="fi-rr-arrow-left"></i> Back
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>