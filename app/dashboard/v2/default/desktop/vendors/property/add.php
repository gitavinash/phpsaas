<h2 class="heading"><?php echo 'Add New property' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Property details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addPropertyForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter property details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Property name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About property</label>
                        </div>
                        <div class="form-group">
                            <textarea name="address" id="address" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <select class="indianCityAutoCompleteWithCompleteAddress form-control" name="city" id="city">
                                <option value=""></option>
                            </select>
                            <label class="form-lable " id="Label">Property city<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" type="email" />
                            <label class="form-lable" id="Label">Reservation Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Reservation Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="supportEmail" name="supportEmail" type="email" />
                            <label class="form-lable" id="Label">Support Email</label>
                        </div>
                        <div class="form-group">
                            <input id="supportMobile" name="supportMobile" type="number" onwheel="this.blur()" />
                            <label class="form-lable" id="Label">Support Contact no.</label>
                        </div>
                        <div class="form-group">
                            <p>Property type</p>
                            <select class="selectJS form-control" name="type" id="type">
                                <option value=""></option>
                                <option value="apartments">Apartments</option>
                                <option value="capsule_hotel">Capsule hotel</option>
                                <option value="guest_house">Guest house</option>
                                <option value="inn">Inn</option>
                                <option value="hostel">Hostel</option>
                                <option value="house">House</option>
                                <option value="home_stay">Home stay</option>
                                <option value="1_star_hotel">1 Star Hotel ( Tourist )</option>
                                <option value="2_star_hotel">2 Star Hotel ( Standard )</option>
                                <option value="3_star_hotel" selected>3 Star Hotel ( Comfort )</option>
                                <option value="4_star_hotel">4 Star Hotel ( First Class )</option>
                                <option value="5_star_hotel">5 Star Hotel ( Luxury )</option>
                                <option value="motel">Motel</option>
                                <option value="lodge">Lodge</option>
                                <option value="tent">Tent</option>
                                <option value="farm_stay">Farm stay</option>
                                <option value="resorts">Resorts</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="mapUrl" name="mapUrl" type="text" />
                            <label class="form-lable" id="Label">Google map url</label>
                        </div>
                        <div class="form-group">
                            <input id="totalItem" name="featureImage" type="file" accept="image/x-png, image/gif, image/jpeg" />
                            <label class="form-lable" id="Label">Feature image</label>
                        </div>
                        <div class="form-group">
                            <div class="input-field">
                                <div class="addGallery" style="padding-top: .5rem;"></div>
                                <label class="form-lable" id="Label">Gallery</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" name="roomType[]" id="roomType" multiple>
                                <option value=""></option>
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
                            <label class="form-lable" id="Label">Room types</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="button" class="btn btn-green" id="addProperty" value="Add new property">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>