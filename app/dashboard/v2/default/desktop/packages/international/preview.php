<h2 class="heading"><?php echo 'Preview of ' . ucwords($urlStructure[1]) ?><span>Generate with Smart AI</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="<?php echo ACTIVE_URL ?>" data-title="Regerate itinerary"><i class="fi fi-rr-refresh"></i></a>
    </div>
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>2</span>Itinerary details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="createTourPackageForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <?php

                        if (isset($travelStyle) && isset($startingCity) && isset($destinationCity) && isset($tripDuration) && isset($outstationVehicle) && isset($localSighseeignVehicle)) {

                            $travelCompanyName = SITE_NAME;

                            $destinationCity = explode('_', $destinationCity);
                            $tripDuration = explode('_', $tripDuration);

                            if (sizeof($destinationCity) == 1) {
                                $multiCity = false;
                                $destinationCity = $destinationCity[0];
                                $tripDuration = $tripDuration[0];
                                require_once MODULES . 'itinerary/single-location.php';
                            } else {
                                require_once MODULES . 'itinerary/multiple-location.php';
                                $destinationCity = $destinationCity[0];
                                $tripDuration = array_sum($tripDuration);
                            }

                            if (isset($tripData)) { ?>
                        <div class="form-header">
                            <h1 style="float:left; width:100%; line-height:60px; padding: 3rem 0px;">
                                <?php echo $packageName ?>
                                <span
                                    style="float: left;width: 100%;font-size: 24px;line-height: 32px; font-weight:400; color: #333">
                                    <strong
                                        style="font-size: 24px;line-height: 32px; font-weight: 700; color: #03637B">Duration:
                                    </strong><?php echo $tripDuration . ' nights and ' . ($tripDuration + 1) . ' days' ?>
                                </span>
                                <span
                                    style="float: left;width: 100%;font-size: 24px;line-height: 32px; font-weight:400; color: #333"><strong
                                        style="font-size: 24px;line-height: 32px; font-weight: 700; color: #03637B">Style:
                                    </strong><?php echo ucwords($travelStyle) ?></span>
                            </h1>
                        </div>
                        <input type="hidden" name="packageName" value="<?php echo $packageName ?>">
                        <input type="hidden" name="travelStyle" value="<?php echo $travelStyle ?>">
                        <input type="hidden" name="duration" value="<?php echo $tripDuration ?>">
                        <div class="form-group">
                            <p class='form-label'>Overview</p>
                            <div class="itineraryEditor" id="editor1" style="min-height: 200px">
                                <?php echo $tripData['overview']; ?>
                            </div>
                            <textarea name="overview" id="editor-0"
                                style="display:none"><?php echo $tripData['overview']['intro'] ?></textarea>
                        </div>
                        <?php
                                $i = 1;
                                foreach ($tripData['durations'] as $days) {
                                    echo "
                                         <div class='form-group'>
                                            <p class='form-label'>Day {$i}</p>
                                            <input name='dayTitle[]' value='{$days['day']}' type='text' />
                                         </div>
                                         <div class='form-group'>
                                            <div class='editor' style='min-height: 200px'>
                                                {$days['details']}
                                            </div>
                                            <textarea name='dayContent[]' id='editor-{$i}' style='display:none'>{$days['details']}</textarea>
                                        </div>
                                    ";
                                    $i++;
                                }
                                ?>
                        <div class="form-group">
                            <p>Inclusions</p>
                            <div class="itineraryEditor" style="min-height: 200px">
                                <?php echo $tripData['overview']['inclusion'] ?>
                            </div>
                            <textarea name="inclusion" id="editor-<?php echo $i ?>"
                                style="display:none"><?php echo $tripData['overview']['inclusion']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <p>Exclusion</p>
                            <div class="itineraryEditor" style="min-height: 200px">
                                <?php echo $tripData['overview']['exclusion'] ?>
                            </div>
                            <textarea name="exclusion" id="editor-<?php echo $i + 1 ?>"
                                style="display:none"><?php echo $tripData['overview']['exclusion']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <p>Hotel type<sup>*</sup></p>
                            <select class="selectJS form-control" name="hotel" id="hotel">
                                <option value="select">Select hotel type</option>
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
                            <input type="hidden" name="packageType" id="packageType" value="international">
                            <input type="hidden" name="outstationVehicle" value="<?php echo $outstationVehicle ?>">
                            <input type="hidden" name="localSighseeignVehicle"
                                value="<?php echo $localSighseeignVehicle ?>">
                            <input type="hidden" name="location" value="<?php echo $destinationCity ?>">
                            <input type="hidden" name="startCity" value="<?php echo $startingCity ?>">
                            <input type="hidden" name="endCity" value="<?php echo $endCity ?>">
                            <input type="button" class="btn" id="createTourPackageBtn" value="Save ininenary">
                        </div>
                        <?php
                            } else {
                                $redirectUrl = "/dashboard/packages/international/preview/?packageName={$packageName}&destinationCity={$destinationCity}&startingCity={$startingCity}&endCity={$endCity}&tripDuration={$tripDuration}&outstationVehicle={$outstationVehicle}&localSighseeignVehicle={$localSighseeignVehicle}&travelStyle={$travelStyle}";

                                echo "
                                <p><br /><br /><br />Something went wrong while generating Itinerary<br /><br /><br /></p>
                                <p><br /><br /><a href='{$redirectUrl}' class='btn center' >Click here to retry</a><br /><br /><br /><br /><br /><br /></p>
                            ";

                                if (DEVELOPMENT_MODE == TRUE) {
                                    $decodedContent = json_decode($result['choices'][0]['message']['content'], TRUE);

                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                        echo "JSON Decode Error: " . json_last_error_msg();
                                        echo "<br />";
                                        echo "<br />";
                                        echo "<br />";
                                        echo "<br />";
                                        print_r($result['choices'][0]['message']['content']);

                                        $updateJSONOutput = str_replace('." } }', '." }', str_replace('```', '}', str_replace('```json', '', $result['choices'][0]['message']['content'])));

                                        echo "<br /><br /><br />New object̉<br /><br />";

                                        print_r($updateJSONOutput);
                                        echo "<br /><br /><br />New object̉<br /><br />";

                                        print_r(json_decode($updateJSONOutput, TRUE));
                                    } else {
                                        print_r($decodedContent);
                                    }
                                } else { ?>
                        <script>
                        window.location.reload();
                        </script>
                        <?php
                                }
                            }
                        } else {
                            echo "Invalid request found";
                        }
                        ?>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>