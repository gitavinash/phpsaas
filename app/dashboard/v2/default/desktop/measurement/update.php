<?php

if (!isset($id)) { ?>
    <script>
        window.location.href = '/dashboard/measurement/list/';
    </script>
<?php
} else if ($id == '') { ?>
    <script>
        window.location.href = '/dashboard/measurement/list/';
    </script>
<?php
}

$measurementId = base64_decode($id);

if ($checkMeasurement = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_id='" . base64_decode($id) . "' AND site_configuration_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkMeasurement) > 0) {
        extract(mysqli_fetch_assoc($checkMeasurement));
        $measurementValues = json_decode($site_configuration_value, TRUE);
        extract($measurementValues);
    }
}

?>
<h2 class="heading"><?php echo 'Update measurement details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Measurement details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateMeasurementForm" method="POST">
                <section class="form">


                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter Measurement details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="productType" name="productType"
                                value="<?php echo ucwords(str_replace('measurement_', '', $site_configuration_key)) ?>"
                                type="text" />
                            <label class="form-lable" id="Label">Product type<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30"
                                rows="10"><?php echo $description ?></textarea>
                            <label class="form-lable" id="Label">About measurement</label>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" name="measurementType" id="measurementType">
                                <option value="mm" <?php echo (($measurementType == 'mm') ? 'selected' : '') ?>>
                                    Millimeter
                                </option>
                                <option value="cm" <?php echo (($measurementType == 'cm') ? 'selected' : '') ?>>
                                    Centimeter
                                </option>
                                <option value="in" <?php echo (($measurementType == 'in') ? 'selected' : '') ?>>Inches
                                </option>
                                <option value="ft" <?php echo (($measurementType == 'ft') ? 'selected' : '') ?>>Feet
                                </option>
                                <option value="mt" <?php echo (($measurementType == 'mt') ? 'selected' : '') ?>>Meter
                                </option>
                            </select>
                            <label class="form-lable" id="Label">Measurement<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" name="calculationType" id="calculationType">
                                <option value="sq. mm."
                                    <?php echo (($calculationType == 'sq. mm.') ? 'selected' : '') ?>>
                                    Square Millimeter</option>
                                <option value="sq. cm."
                                    <?php echo (($calculationType == 'sq. cm.') ? 'selected' : '') ?>>
                                    Square Centimeter</option>
                                <option value="sq. in."
                                    <?php echo (($calculationType == 'sq. in.') ? 'selected' : '') ?>>
                                    Square Inches</option>
                                <option value="sq. ft."
                                    <?php echo (($calculationType == 'sq. ft.') ? 'selected' : '') ?>>
                                    Square Feet</option>
                                <option value="sq. mt."
                                    <?php echo (($calculationType == 'sq. mt.') ? 'selected' : '') ?>>
                                    Square Meter</option>
                            </select>
                            <label class="form-lable" id="Label">Calculation<sup>*</sup></label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="configurationId" value="<?php echo $id ?>">
                                <input type="button" class="btn btn-green" id="updateMeasurement"
                                    value="Update measurement">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>