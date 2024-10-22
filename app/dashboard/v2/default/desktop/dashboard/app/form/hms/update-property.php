<?php

$propertyId = base64_decode($id);

if ($propertyData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_id={$propertyId} AND property_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($propertyData) != 0) {
        extract(mysqli_fetch_assoc($propertyData));
    } else {
        echo "invalid request received";
        exit();
    }
} else {
    echo "Something went wrong";
    exit();
}

?>
<section>
    <ul class="steps-tab">
        <li class="step-box active" id="step_1"><span>1</span>Property details</li>
    </ul>
    <div class="steps-box">
        <form action="#" id="updatePropertyForm" method="POST">
            <section class="form">

                <div class="step-open-box step_1_box">
                    <div class="form-header">
                        <h1>Enter property details</h1>
                        <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                    </div>
                    <div class="form-group">
                        <input id="name" name="name" value="<?php echo $property_title ?>" type="text" />
                        <label class="form-lable" id="Label">Property name<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="description" id="description" cols="30"
                            rows="10"><?php echo $property_description ?></textarea>
                        <label class="form-lable" id="Label">About hotel</label>
                    </div>
                    <div class="form-group">
                        <textarea name="address" id="address" cols="30"
                            rows="10"><?php echo $property_address ?></textarea>
                        <label class="form-lable" id="Label">Address<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <select class="indianCityAutoCompleteWithCompleteAddress form-control" name="city" id="city">
                            <option value="<?php echo $property_city ?>"><?php echo $property_city ?></option>
                        </select>
                        <label class="form-lable " id="Label">Property city<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="pinCode" name="pinCode" value="<?php echo $property_pincode ?>" type="number" onwheel="this.blur()" />
                        <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="email" name="email" value="<?php echo $property_email ?>" type="email" />
                        <label class="form-lable" id="Label">Email<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" value="<?php echo $property_mobile ?>" type="number" onwheel="this.blur()" />
                        <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="supportEmail" name="supportEmail" value="<?php echo $property_support_email ?>"
                            type="email" />
                        <label class="form-lable" id="Label">Support Email</label>
                    </div>
                    <div class="form-group">
                        <input id="supportMobile" name="supportMobile" value="<?php echo $property_support_mobile ?>"
                            type="number" onwheel="this.blur()" />
                        <label class="form-lable" id="Label">Support Contact no.</label>
                    </div>
                    <div class="form-group">
                        <p>Property type</p>
                        <select class="selectJS form-control" name="type" id="type">
                            <option value=""></option>
                            <option value="apartments"
                                <?php echo (($property_type == 'apartments') ? 'selected' : '') ?>>
                                Apartments</option>
                            <option value="capsule_hotel"
                                <?php echo (($property_type == 'capsule_hotel') ? 'selected' : '') ?>>
                                Capsule hotel</option>
                            <option value="guest_house"
                                <?php echo (($property_type == 'guest_house') ? 'selected' : '') ?>>Guest
                                house</option>
                            <option value="inn" <?php echo (($property_type == 'inn') ? 'selected' : '') ?>>Inn</option>
                            <option value="hostel" <?php echo (($property_type == 'hostel') ? 'selected' : '') ?>>Hostel
                            </option>
                            <option value="house" <?php echo (($property_type == 'house') ? 'selected' : '') ?>>House
                            </option>
                            <option value="home_stay" <?php echo (($property_type == 'home_stay') ? 'selected' : '') ?>>
                                Home stay
                            </option>
                            <option value="1_star_hotel"
                                <?php echo (($property_type == '1_star_hotel') ? 'selected' : '') ?>>1 Star
                                Hotel ( Tourist )</option>
                            <option value="2_star_hotel"
                                <?php echo (($property_type == '2_star_hotel') ? 'selected' : '') ?>>2 Star
                                Hotel ( Standard )</option>
                            <option value="3_star_hotel"
                                <?php echo (($property_type == '3_star_hotel') ? 'selected' : '') ?>>3 Star
                                Hotel ( Comfort )</option>
                            <option value="4_star_hotel"
                                <?php echo (($property_type == '4_star_hotel') ? 'selected' : '') ?>>4 Star
                                Hotel ( First Class )</option>
                            <option value="5_star_hotel"
                                <?php echo (($property_type == '5_star_hotel') ? 'selected' : '') ?>>5 Star
                                Hotel ( Luxury )</option>
                            <option value="motel" <?php echo (($property_type == 'motel') ? 'selected' : '') ?>>Motel
                            </option>
                            <option value="lodge" <?php echo (($property_type == 'lodge') ? 'selected' : '') ?>>Lodge
                            </option>
                            <option value="tent" <?php echo (($property_type == 'tent') ? 'selected' : '') ?>>Tent
                            </option>
                            <option value="farm_stay" <?php echo (($property_type == 'farm_stay') ? 'selected' : '') ?>>
                                Farm stay</option>
                            <option value="resorts" <?php echo (($property_type == 'resorts') ? 'selected' : '') ?>>
                                Resorts</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="mapUrl" name="mapUrl" value="<?php echo $property_map_url ?>" type="text" />
                        <label class="form-lable" id="Label">Google map url</label>
                    </div>
                    <div class="form-group">
                        <input id="totalItem" name="featureImage" type="file"
                            accept="image/x-png, image/gif, image/jpeg" />
                        <label class="form-lable" id="Label">Feature image</label>
                    </div>
                    <div class="form-group">
                        <div class="input-field">
                            <div class="updateGallery" style="padding-top: .5rem;"></div>
                            <label class="form-lable" id="Label">Gallery</label>

                            <?php

                            if ($property_gallery != '') {

                                $blockGalleryList = json_decode($property_gallery, true);

                                $oldGalleryImages = array();

                                for ($i = 0; $i < sizeof($blockGalleryList); $i++) {

                                    $oldGalleryImages[] = array(
                                        'id' => $i + 1,
                                        'name' => $blockGalleryList[$i]['name'],
                                        'src' => $blockGalleryList[$i]['path'],
                                        'type' => $blockGalleryList[$i]['type'],
                                    );
                                }

                                $oldGalleryImages = json_encode($oldGalleryImages, TRUE);
                            } else {

                                $oldGalleryImages = json_encode('', TRUE);
                            }

                            echo "<script> oldGalleryImages = {$oldGalleryImages}</script>";

                            ?>
                        </div>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="hidden" name="oldFeatureImage" value="<?php echo $property_thumbnail ?>">
                            <input type="hidden" name="propertyId" value="<?php echo base64_encode($propertyId) ?>">
                            <input type="button" class="btn btn-green" id="updateProperty" value="Update property">
                        </div>
                    </div>
                </div>

            </section>
        </form>
    </div>
</section>