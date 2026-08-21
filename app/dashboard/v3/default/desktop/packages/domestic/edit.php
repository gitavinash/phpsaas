<h2 class="heading"><?php echo 'Edit ' . ucwords($urlStructure[1]) ?><span>Update tour package</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Edit Itinerary details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="createTourPackageForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <?php

                        if (isset($id)) {
                            $tourPackageId = base64_decode($id);
                            if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id='{$tourPackageId}' AND tour_package_site_id=" . SITE_ID)) {
                                if (mysqli_num_rows($checkPackage) != 0) {
                                    extract(mysqli_fetch_assoc($checkPackage)); ?>
                        <div class="form-header">
                            <h1 style="float:left; width:100%">Update domestic tour package</h1>
                        </div>
                        <div class="form-group">
                            <p class="form-lable" id="Label">Package name<sup>*</sup></p>
                            <input id="name" name="packageName" value="<?php echo $tour_package_title ?>" type="text" />
                        </div>
                        <div class="form-group">
                            <p class="form-lable" id="Label">Trip duration (in Nights)</p>
                            <input type="text" readonly value="<?php echo $tour_package_duration ?> Nights" />
                        </div>
                        <div class="form-group">
                            <p>Travel style<sup>*</sup></p>
                            <select class="selectJS form-control" name="travelStyle" id="travelStyle">
                                <option value="select">Select travel style</option>
                                <option value="adventure"
                                    <?php echo (($tour_package_theme == 'adventure') ? 'selected' : '') ?>>Adventure
                                    Travel</option>
                                <option value="cultural"
                                    <?php echo (($tour_package_theme == 'cultural') ? 'selected' : '') ?>>Cultural
                                    Travel</option>
                                <option value="luxury"
                                    <?php echo (($tour_package_theme == 'luxury') ? 'selected' : '') ?>>Luxury Travel
                                </option>
                                <option value="solo" <?php echo (($tour_package_theme == 'solo') ? 'selected' : '') ?>>
                                    Solo Travel</option>
                                <option value="group"
                                    <?php echo (($tour_package_theme == 'group') ? 'selected' : '') ?>>Group Travel
                                </option>
                                <option value="eco" <?php echo (($tour_package_theme == 'eco') ? 'selected' : '') ?>>
                                    Eco/Responsible Travel</option>
                                <option value="honeymoon"
                                    <?php echo (($tour_package_theme == 'honeymoon') ? 'selected' : '') ?>>Honeymoon
                                    Travel</option>
                                <option value="couple"
                                    <?php echo (($tour_package_theme == 'couple') ? 'selected' : '') ?>>Couple Travel
                                </option>
                                <option value="family"
                                    <?php echo (($tour_package_theme == 'family') ? 'selected' : '') ?>>Family Travel
                                </option>
                                <option value="wellness"
                                    <?php echo (($tour_package_theme == 'wellness') ? 'selected' : '') ?>>
                                    Wellness/Retreat Travel</option>
                                <option value="budget"
                                    <?php echo (($tour_package_theme == 'budget') ? 'selected' : '') ?>>Budget Travel
                                </option>
                                <option value="roadtrip"
                                    <?php echo (($tour_package_theme == 'roadtrip') ? 'selected' : '') ?>>Road Trip
                                </option>
                                <option value="cruise"
                                    <?php echo (($tour_package_theme == 'cruise') ? 'selected' : '') ?>>Cruise Travel
                                </option>
                                <option value="backpacking"
                                    <?php echo (($tour_package_theme == 'backpacking') ? 'selected' : '') ?>>Backpacking
                                    Travel</option>
                                <option value="religious"
                                    <?php echo (($tour_package_theme == 'religious') ? 'selected' : '') ?>>Religious
                                    Travel</option>
                                <option value="heritage">Heritage Travel</option>
                            </select>
                        </div>
                        <input type="hidden" name="duration" value="<?php echo $tour_package_duration ?>">
                        <div class="form-group">
                            <p class='form-label'>Overview</p>
                            <div class="editor" id="editor1" style="min-height: 200px">
                                <?php echo $tour_package_overview; ?>
                            </div>
                            <textarea name="overview" id="editor-0"
                                style="display:none"><?php echo $tour_package_overview ?></textarea>
                        </div>
                        <?php
                                    $i = 1;

                                    // decode safely and ensure array
                                    $tour_package_itinerary = json_decode($tour_package_itinerary, TRUE);
                                    if (!is_array($tour_package_itinerary) || empty($tour_package_itinerary)) {
                                        $showDeleteBtn = true;
                                    }


                                    // iterate and render
                                    foreach ($tour_package_itinerary as $days) {
                                        $dayTitle = htmlspecialchars($days['day'] ?? "Day {$i}", ENT_QUOTES);
                                        $dayOverview = isset($days['overview']) ? htmlspecialchars($days['overview'], ENT_QUOTES, 'UTF-8') : '';

                                        echo "
                                         <div class='form-group'>
                                            <p class='form-label'>Day {$i}</p>
                                            <input name='dayTitle[]' value='{$dayTitle}' type='text' />
                                         </div>
                                         <div class='form-group'>
                                            <div class='editor' style='min-height: 200px'>
                                                {$dayOverview}
                                            </div>
                                            <textarea name='dayContent[]' id='editor-{$i}' style='display:none'>{$dayOverview}</textarea>
                                        </div>
                                    ";
                                        $i++;
                                    }
                                    ?>

                        <div class="form-group">
                            <p>Inclusions</p>
                            <div class="editor" style="min-height: 200px">
                                <?php echo $tour_package_inclusions ?>
                            </div>
                            <textarea name="inclusion" id="editor-<?php echo $i ?>"
                                style="display:none"><?php echo $tour_package_inclusions; ?></textarea>
                        </div>
                        <div class="form-group">
                            <p>Exclusion</p>
                            <div class="editor" style="min-height: 200px">
                                <?php echo $tour_package_exclusions ?>
                            </div>
                            <textarea name="exclusion" id="editor-<?php echo $i + 1 ?>"
                                style="display:none"><?php echo $tour_package_exclusions; ?></textarea>
                        </div>
                        <div class="form-group">
                            <p>Outstataion vehicle<sup>*</sup></p>
                            <select class="selectJS form-control" name="outstationVehicle" id="outstationVehicle">
                                <option value="select">Select vehicle</option>
                                <?php
                                            if ($vehicles = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
                                                if (mysqli_num_rows($vehicles)) {
                                                    while ($vehiclesData = mysqli_fetch_assoc($vehicles)) {
                                                        echo "<option value='{$vehiclesData['vehicle_title']}' " . ((strtolower($vehiclesData['vehicle_title']) == strtolower($tour_package_outstation_vehicle)) ? 'selected' : '') . ">{$vehiclesData['vehicle_title']}, " . ucwords(str_replace('_', ' ', $vehiclesData['vehicle_type'])) . "</option>";
                                                    }
                                                }
                                            } else {
                                                echo mysqli_error($connection);
                                            }
                                            ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Local Sightseeing vehicle<sup>*</sup></p>
                            <select class="selectJS form-control" name="localSighseeignVehicle"
                                id="localSighseeignVehicle">
                                <option value="select">Select vehicle</option>
                                <?php
                                            if ($vehicles = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM " . TABLE_PREFIX . "vehicles")) {
                                                if (mysqli_num_rows($vehicles)) {
                                                    while ($vehiclesData = mysqli_fetch_assoc($vehicles)) {
                                                        echo "<option value='{$vehiclesData['vehicle_title']}' " . ((strtolower($vehiclesData['vehicle_title']) == strtolower($tour_package_localsightseen_vehicle)) ? 'selected' : '') . ">{$vehiclesData['vehicle_title']}, " . ucwords(str_replace('_', ' ', $vehiclesData['vehicle_type'])) . "</option>";
                                                    }
                                                }
                                            } else {
                                                echo mysqli_error($connection);
                                            }
                                            ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p>Hotel type<sup>*</sup></p>
                            <select class="selectJS form-control" name="hotel" id="hotel">
                                <option value="select">Select hotel type</option>
                                <option value="apartments"
                                    <?php echo (($tour_package_hotel == 'apartments') ? 'selected' : '') ?>>Apartments
                                </option>
                                <option value="capsule_hotel"
                                    <?php echo (($tour_package_hotel == 'capsule_hotel') ? 'selected' : '') ?>>Capsule
                                    hotel</option>
                                <option value="guest_house"
                                    <?php echo (($tour_package_hotel == 'guest_house') ? 'selected' : '') ?>>Guest house
                                </option>
                                <option value="inn" <?php echo (($tour_package_hotel == 'inn') ? 'selected' : '') ?>>Inn
                                </option>
                                <option value="hostel"
                                    <?php echo (($tour_package_hotel == 'hostel') ? 'selected' : '') ?>>Hostel</option>
                                <option value="house"
                                    <?php echo (($tour_package_hotel == 'house') ? 'selected' : '') ?>>House</option>
                                <option value="home_stay"
                                    <?php echo (($tour_package_hotel == 'home_stay') ? 'selected' : '') ?>>Home stay
                                </option>
                                <option value="1_star_hotel"
                                    <?php echo (($tour_package_hotel == '1_star_hotel') ? 'selected' : '') ?>>1 Star
                                    Hotel ( Tourist )</option>
                                <option value="2_star_hotel"
                                    <?php echo (($tour_package_hotel == '2_star_hotel') ? 'selected' : '') ?>>2 Star
                                    Hotel ( Standard )</option>
                                <option value="3_star_hotel"
                                    <?php echo (($tour_package_hotel == '3_star_hotel') ? 'selected' : '') ?>>3 Star
                                    Hotel ( Comfort )</option>
                                <option value="4_star_hotel"
                                    <?php echo (($tour_package_hotel == '4_star_hotel') ? 'selected' : '') ?>>4 Star
                                    Hotel ( First Class )</option>
                                <option value="5_star_hotel"
                                    <?php echo (($tour_package_hotel == '5_star_hotel') ? 'selected' : '') ?>>5 Star
                                    Hotel ( Luxury )</option>
                                <option value="motel"
                                    <?php echo (($tour_package_hotel == 'motel') ? 'selected' : '') ?>>Motel</option>
                                <option value="lodge"
                                    <?php echo (($tour_package_hotel == 'lodge') ? 'selected' : '') ?>>Lodge</option>
                                <option value="tent" <?php echo (($tour_package_hotel == 'tent') ? 'selected' : '') ?>>
                                    Tent</option>
                                <option value="farm_stay"
                                    <?php echo (($tour_package_hotel == 'farm_stay') ? 'selected' : '') ?>>Farm stay
                                </option>
                                <option value="resorts"
                                    <?php echo (($tour_package_hotel == 'resorts') ? 'selected' : '') ?>>Resorts
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="packageType" id="packageType" value="domestic">
                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                            <?php

                                        if (isset($showDeleteBtn)) {
                                            echo "<input type='button' data-type='packages' data-id='{$tour_package_id}' title='Delete package' data-title='Delete package' class='btn bgRed deleteButton' data-redirect='/dashboard/packages/domestic/list/' id='deleteTourPackageBtn' value='Delete itinerary'>";
                                        } else {
                                            echo "<input type='button' class='btn btn-green' id='updateTourPackageBtn' value='Update itinerary'>";
                                        }

                                        ?>
                        </div>

                        <?php
                                } else {
                                    echo "Invalid request found";
                                }
                            }
                        } else {
                            echo "Required paramater is missing";
                        }

                        ?>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>