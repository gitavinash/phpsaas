<div class="shortcut">
    <a href="#" id="addNewRoom"><i class="fi fi-rr-plus"></i></a>
</div>
<section>
    <ul class="steps-tab">
        <li class="step-box active" id="step_1"><span>1</span>Property room details</li>
    </ul>
    <div class="steps-box">
        <form action="#" id="updatePropertyForm" method="POST">
            <section class="form">

                <div class="step-open-box step_1_box">
                    <div class="form-header">
                        <h1>Property room details</h1>
                        <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                    </div>
                </div>

            </section>
        </form>
        <div class="row" style="padding: 0px 1rem; box-sizing: border-box">
            <?php

            if ($checkHotel = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_id='{$id}' AND property_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkHotel) != 0) {
                    while ($hotelData = mysqli_fetch_assoc($checkHotel)) {
                        extract($hotelData); ?>
            <div class="box-4">
                <div class="roomBox">
                    <div class="featureImage">
                        <img src="/upload/others/image-not-found.png" alt="">
                    </div>
                    <div class="details">
                        <p>Room type <span>₹ 9999</span></p>
                    </div>
                    <div class="btnGroup">
                        <div class="row">
                            <div class="box-5">
                                <a href="#" id="editRoom" data-id=""><i class="fi fi-rr-pencil"></i> Edit</a>
                            </div>
                            <div class="box-5">
                                <a href='#' data-type='room' data-id='' title='Delete room' class='deleteButton'
                                    data-reload='true' data-redirect='false' style="background: #C70039 !important;"><i
                                        class="fi fi-rr-trash"></i> Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    }
                }
            }

            ?>
        </div>
    </div>
</section>
<div class="popUp addNewRoomBox" style="display:block">
    <div class="fullScreenPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form method="POST" action="#">
                    <section class="form">
                        <div class="form-header">
                            <h4 class="heading">Add new room</h4>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" id="type" name="type">
                                <?php
                                if ($roomsData = mysqli_query($connection, "SELECT category_id, category_title, category_image, category_description, category_deletable FROM " . TABLE_PREFIX . "category WHERE category_type='room'")) {
                                    $roomsList = array();
                                    if (mysqli_num_rows($roomsData) != 0) {
                                        while ($data = mysqli_fetch_assoc($roomsData)) {
                                            extract($data);
                                            echo "<option value='" . str_replace(' ', '_', strtolower($category_title)) . "'>" . ucwords($category_title) . "</option>";
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <label class="form-lable">Room type</label>
                        </div>
                        <div class="form-group">
                            <textarea type="text" name="comment" id="comment" placeholder="Add your comment"></textarea>
                            <label class="form-lable" id="commentLable">About room</label>
                        </div>
                        <h4>Only room</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )</label>
                            </div>
                        </div>
                        <h4>Room with Breakfast</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )</label>
                            </div>
                        </div>
                        <h4>Room with Breakfast and Lunch/Dinner</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )</label>
                            </div>
                        </div>
                        <h4>Room with Breakfast, Lunch and Dinner</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )</label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="followupTime" id="followUpTime"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input id="totalItem" name="featureImage" type="file"
                                accept="image/x-png, image/gif, image/jpeg" />
                            <label class="form-lable" id="Label">Feature image</label>
                        </div>
                        <div class="form-group">
                            <div class="input-field">
                                <div class="addGallery" style="padding-top: .5rem;"></div>
                                <label class="form-lable" id="Label">Gallery</label>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <a href="#" class="btn popUpBtn"><i class="fi-rr-check"></i> Add room</a>
                <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</a>
            </div>
        </div>
    </div>
</div>