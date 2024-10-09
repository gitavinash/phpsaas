<h2 class="heading"><?php echo 'Edit ' . ucwords($urlStructure[1]) ?><span>Update tour package price</span>
</h2>
<div class="main-box">

    <?php

    if (isset($id)) {
        $tourPackageId = base64_decode($id);
        if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package LEFT JOIN " . TABLE_PREFIX . "tour_package_price ON tour_package_id=tour_package_price_parent_id WHERE tour_package_id='{$tourPackageId}' AND tour_package_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkPackage) != 0) {
                extract(mysqli_fetch_assoc($checkPackage)); ?>
                <section>
                    <ul class="steps-tab">
                        <li class="step-box active" id="step_1"><span>1</span>Single sharing price</li>
                        <li class="step-box" id="step_2"><span>2</span>Double sharing price</li>
                        <li class="step-box" id="step_3"><span>3</span>Triple sharing price</li>
                        <li class="step-box" id="step_4"><span>4</span>Quad sharing price</li>
                    </ul>
                    <div class="steps-box">
                        <form action="#" id="createTourPackageForm">
                            <section class="form">
                                <div class="step-open-box step_1_box">
                                    <div class="form-header">
                                        <h1 style="float:left; width:100%">Single room sharing price</h1>
                                    </div>
                                    <div class="priceBox">
                                        <div class="form-group">
                                            <div class="box-5">
                                                <p class="form-lable" id="Label">From<sup>*</sup></p>
                                                <input id="name" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartSingleSharing[]" type="text" />
                                            </div>
                                            <div class="box-5">
                                                <p class="form-lable" id="Label">To<sup>*</sup></p>
                                                <input id="name" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndSingleSharing[]" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <h2>Price per Adult ( in ₹ )</h2>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                <input id="name" name="netCostAdultSingleSharing[]" placeholder="999" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">MRP<sup>*</sup></p>
                                                <input id="name" name="mrpAdultSingleSharing[]" placeholder="1999" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                <input id="name" name="sellingPriceAdultSingleSharing[]" placeholder="1799" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Sales team price<sup>*</sup></p>
                                                <input id="name" name="agentPriceAdultSingleSharing[]" placeholder="1499" type="number" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <h2>Price per Children ( in ₹ )</h2>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                <input id="name" name="netCostChildSingleSharing[]" placeholder="999" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">MRP<sup>*</sup></p>
                                                <input id="name" name="mrpChildSingleSharing[]" placeholder="1999" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                <input id="name" name="sellingPriceChildSingleSharing[]" placeholder="1799" type="number" />
                                            </div>
                                            <div class="box-2-5">
                                                <p class="form-lable" id="Label">Sales team price<sup>*</sup></p>
                                                <input id="name" name="agentPriceChildSingleSharing[]" placeholder="1499" type="number" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <a href="#" class="cloneSingleSharingPriceBox btn" style="float: right; width:50%"><i class="fi fi-rr-multiple"></i> Add more dates</a>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                                        <input type="submit" class="btn" id="updateTourPackagePriceBtn" value="Update ininenary price">
                                    </div>
                                </div>
                            </section>
                        </form>
                    </div>
                </section>

    <?php
            } else {
                echo "Invalid request found";
            }
        } else {
            echo ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
        }
    } else {
        echo "Required paramater is missing";
    }

    ?>
</div>