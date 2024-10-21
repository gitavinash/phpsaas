<div class="shortcut">
    <a href="#" id="openRoomPopUP"><i class="fi fi-rr-plus"></i></a>
</div>
<section>
    <ul class="steps-tab">
        <li class="step-box active" id="step_1"><span>1</span>Property room details</li>
    </ul>
    <div class="steps-box">
        <section class="form" style="margin-bottom: 50px">

            <div class="step-open-box step_1_box">
                <div class="form-header">
                    <h1>Property room details</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>
            </div>

        </section>
        <div class="row" style="padding: 0px 1rem; box-sizing: border-box">
            <?php

            if ($checkHotel = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "room WHERE room_property_id='" . base64_decode($id) . "' AND room_site_id=" . SITE_ID . " ORDER BY room_ep_b2c ASC ")) {
                if (mysqli_num_rows($checkHotel) != 0) {
                    while ($hotelData = mysqli_fetch_assoc($checkHotel)) {

                        $hotelDataJSON = json_encode($hotelData, TRUE);

            ?>
                        <script>
                            for (let i = 0; i < localStorage.length; i++) {
                                const key = localStorage.key(i);
                                if (key && key.startsWith('roomInfo_')) {
                                    localStorage.removeItem(key);
                                }
                            }
                        </script>
            <?php
                        extract($hotelData);

                        echo "
                                <script>
                                localStorage.setItem('roomInfo_{$room_id}', JSON.stringify({$hotelDataJSON}));
                                </script>
                                ";

                        echo "
            <div class='box-4'>
            <div class='safeBox'>
                <div class='roomBox'>
                    <div class='featureImage'>
                        <img src='" . (($room_feature_image != '') ? $room_feature_image : '/upload/others/image-not-found.png') . "' alt=''>
                    </div>
                    <div class='details'>
                        <p>" . ucwords(str_replace('_', ' ', $room_type)) . (($room_status == 0) ? "<sup>Draft</sup>" : '') . " <span>₹ {$room_ep_b2c}</span></p>
                    </div>
                    <div class='btnGroup'>
                        <div class='row'>
                            <div class='box-5'>
                                <a href='#' class='editRoom' data-id='{$room_id}'><i class='fi fi-rr-pencil'></i> Edit</a>
                            </div>
                            <div class='box-5'>
                                <a href='#' data-type='room' data-id='{$room_id}' title='Delete room' class='deleteButton'
                                    data-reload='true' data-redirect='false' style='background: #C70039 !important;'><i
                                        class='fi fi-rr-trash'></i> Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>";
                    }
                } else {
                    echo "<h2>No room find in this property</h2>";
                }
            } else {
                echo mysqli_error($connection);
            }

            ?>

        </div>
    </div>
</section>
<div class="popUp addNewRoomBox">
    <div class="fullScreenPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form method="POST" id="addNewRoomForm">
                    <section class="form">
                        <div class="form-header">
                            <h4 class="heading">Add new room</h4>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" id="roomType" name="roomType">
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
                            <label class="form-lable">Room type<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea type="text" name="comment" id="comment" placeholder="Add your comment"></textarea>
                            <label class="form-lable" id="commentLable">About room</label>
                        </div>
                        <h4>Only room ( EP - European Plan )</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceMRP" id="priceMRP"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceB2C" id="priceB2C"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceB2B" id="priceB2B" placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <h4>Room with Breakfast ( CP - Continental plan )</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceCP" id="priceCP" placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceCPB2C" id="priceCPB2C"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceCPB2B" id="priceCPB2B"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <h4>Room with Breakfast and Lunch/Dinner ( MAP - Modified American Plan )</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceMAP" id="priceMAP"
                                    placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceMAPB2C" id="priceMAPB2C"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceMAPB2B" id="priceMAPB2B"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <h4>Room with Breakfast, Lunch and Dinner ( AP - American Plan )</h4>
                        <hr>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceAP" id="priceAP" placeholder="1200">
                                <label class="form-lable">Price ( Without discount in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceAPB2C" id="priceAPB2C"
                                    placeholder="1000">
                                <label class="form-lable">Price ( B2C in ₹ )<sup>*</sup></label>
                            </div>
                        </div>
                        <div class="box-3-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priceAPB2B" id="priceAPB2B"
                                    placeholder="800">
                                <label class="form-lable">Price ( B2B in ₹ )<sup>*</sup></label>
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
                    <input type="hidden" name="propertyId" value="<?php echo $id ?>">
                </form>
            </div>
        </div>
        <div class="widget box-3">
            <div class="actionArea">
                <a href="#" class="btn popUpBtn" id="addNewRoom"><i class="fi-rr-check"></i> Add room</a>
                <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</a>
            </div>
        </div>
    </div>
</div>