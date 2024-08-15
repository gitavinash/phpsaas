<ul class="steps-tab">
    <li class="step-box active" id="salesStep_1"><span>1</span>Customer details</li>
    <li class="step-box" id="salesStep_2"><span>2</span>Inventory details</li>
    <li class="step-box" id="salesStep_3"><span>3</span>Confirmation</li>
</ul>
<div class="steps-box">
    <form action="#" id="addNewSales" method="POST" enctype="multipart/form-data">
        <section class="form">
            <div class="step-open-box salesStep_1_box">
                <div class="form-header">
                    <h1>Enter customer details</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>

                <div class="form-group">
                    <input id="name" name="name" type="text" />
                    <label class="form-lable" id="Label">Full name<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="mobile" name="mobile" type="number" />
                    <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="email" name="email" type="email" />
                    <label class="form-lable" id="Label">Email</label>
                </div>
                <div class="form-group">
                    <textarea name="address" id="address" cols="30" rows="10"></textarea>
                    <label class="form-lable" id="Label">Address<sup>*</sup></label>
                </div>
                <div class="box-1">
                    <div class="form-group">
                        <!-- <input type="submit" cla¯ss="btn btn-green step-box" id="salesStep_2" value="N new sale"> -->
                        <a class="btn btn-green step-box" style="float: right;" id="salesStep_2">Next, add Inventory <i
                                class="fi fi-rr-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="step-open-box salesStep_2_box" style="display: none;">
                <div class="form-header">
                    <h1>Select block</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>

                <div class="productBox">
                    <!-- <h1>Select block<sup>*</sup></h1> -->
                    <div class="form-group">
                        <?php

                        if ($blockList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_status=1 AND block_site_id=" . SITE_ID)) {
                            if (mysqli_num_rows($blockList) > 0) {
                                while ($block = mysqli_fetch_assoc($blockList)) {
                                    echo "<p class='openBlockSlab blockIcon' data-id={$block['block_id']} data-title='{$block['block_code']} - {$block['block_title']}' >{$block['block_code']} - {$block['block_title']} <span id='block_{$block['block_id']}' >0</span</p>";
                                }
                            }
                        }

                        ?>
                    </div>
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <a class="btn btn-green step-box" id="salesStep_1"><i class="fi fi-rr-arrow-left"></i> Back</a>
                    </div>
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <?php

                        if (SITE_VERSION == 'desktop') {
                            echo '<a class="btn btn-green step-box" style="float: right;" id="salesStep_3">Next, add sales <i
                                class="fi fi-rr-arrow-right"></i></a>';
                        } else {
                            echo '<a class="btn btn-green step-box" style="float: right;" id="salesStep_3">Next <i
                                class="fi fi-rr-arrow-right"></i></a>';
                        }

                        ?>
                    </div>
                </div>
            </div>
            <div class="step-open-box salesStep_3_box" style="display: none;">
                <div class="form-header">
                    <h1>Confirm details</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>
                <div class="productBoxClone">
                    <table class="salesInfo">
                        <tr>
                            <th colspan="2" class="heading">Customer details</th>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td id="customerName"></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="customerEmail"></td>
                        </tr>
                        <tr>
                            <th>Mobile:</th>
                            <td id="customerMobile"></td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td id="customerAddress"></td>
                        </tr>
                    </table>
                    <table class="salesInfo inventoryData">

                    </table>
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <a class="btn btn-green step-box" id="salesStep_2"><i class="fi fi-rr-arrow-left"></i> Back</a>
                    </div>
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <a class="btn btn-green" style="float: right;" id="salesRequest">Submit request <i
                                class="fi fi-rr-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>


<div class="popUp blockSlabBox" style="display: none;">
    <div class="fullScreenPopUpBox">
        <div class="widget box-1">
            <div class="contentArea">
                <h4 class="heading">Select slabs form <strong id="blockTitle"></strong>
                    </span></h2>
                    <div class="slabsList">
                        <!-- <section> -->
                        <!-- <form action=""> -->
                        <div class='form-group-box slabsListOptions'>
                            <div class='box-3-2 left p-right-10'>
                                <div class='form-group'>
                                    <div class='input-box'>
                                        <input type='checkbox' id='selectAllSlabs' />
                                        <label class='radio-button'>Select All</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </form>
                        </section> -->
                    </div>
            </div>
        </div>
        <div class="bottomMenu">
            <span href="#" class="btn popUpBtn btn-red" id="addNewDamage" onclick="closePopup(this)"><i
                    class=" fi-rr-cross"></i> Cancel</span>
            <span class="btn popUpBtn btn-green saveSlabs"><i class="fi-rr-check"></i> Add Slabs</span>
        </div>
    </div>
</div>