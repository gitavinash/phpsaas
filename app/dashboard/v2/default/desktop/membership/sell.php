<h2 class="heading">Add new membership</h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewMembershipForm" method="POST" enctype="multipart/form-data">
            <section class="form" style="padding-top:25px">
                <div class="step-open-box step_1_box">
                    <div class="form-group">
                        <input id="name" placeholder="Ajay gupta" name="name" type="text" required />
                        <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number" required />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Room type<sup>*</sup></label>
                        <select class="selectJS" style="float: left; width:100%" name="package" id="package">
                            <option value="">Select room type</option>
                            <?php

                            if ($roomType = mysqli_query($connection, "SELECT category_id, category_title FROM " . TABLE_PREFIX . "category WHERE category_status=1 AND category_site_id='" . SITE_ID . "' AND category_type='" . ((SITE_APP == 'spa' ? 'spa_' : '')) . "room'")) {
                                if (mysqli_num_rows($roomType) != 0) {
                                    while ($room = mysqli_fetch_assoc($roomType)) {
                                        echo "<option value='{$room['category_id']}' " . (($room['category_title'] == 'deluxe') ? 'selected' : '') . ">{$room['category_title']}</option>";
                                    }
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="price" placeholder="5000" name="price" type="number" required />
                        <label class="form-lable" id="Label">Price<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="dueAmount" value="0" name="dueAmount" type="number" required />
                        <label class="form-lable" id="Label">Any Due Amount?<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visit" value="5" name="visit" type="number" required />
                        <label class="form-lable" id="Label">Total visit<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
                        <label class="form-lable" id="Label">Any requirements</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-green" id="addMembership" value="Add new membership">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>