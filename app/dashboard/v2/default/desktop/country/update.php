<?php
ob_start();
if(!isset($id)) { ?>
<script type="text/javascript">
alert('Invalid request');
window.location.href = '/dashboard/country/';
</script>
<?php
exit();
}

$countryId=base64_decode($id);

if($checkCountry=mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}country WHERE visa_country_id='{$countryId}' AND visa_country_status='1' AND visa_country_site_id=".SITE_ID)) {
    if(mysqli_num_rows($checkCountry) != 0) {
        extract(mysqli_fetch_assoc($checkCountry));
    } else { ?>
<script type="text/javascript">
alert('No country details found');
window.location.href = '/dashboard/country/list/';
</script>
<?php
        exit();
    }
} else {
    echo "<script>alert('Error fetching country data');</script>";
    exit();
}

?>
<h2 class="heading"><?php echo 'Add New country' ?><span>Country that we are offer VISA services</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Update country details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter country details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <span style="display: none;" id="oldGallery"><?php echo $visa_country_image ?></span>
                            <p><strong>Country feature image</strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" data-limit="1" name="featureImage[]"
                                accept=".jpg, .jpeg, .png" />
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" value="<?php echo $visa_country_title ?>" type="text"
                                required />
                            <label class="form-lable" id="Label">Name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="code" name="code" value="<?php echo $visa_country_code ?>" type="text" />
                            <label class="form-lable" id="Label">Country code</label>
                        </div>
                        <div class="form-group">
                            <label class="form-lable" id="Label">Documents Required</label>
                        </div>
                        <div class="form-group editableBox" id="requirements" contenteditable="true">
                            <?php echo $visa_country_documents ?>
                        </div>
                        <div class="form-group">
                            <label class="form-lable" id="Label">Important Information</label>
                        </div>
                        <div class="form-group editableBox" id="information" contenteditable="true">
                            <?php echo $visa_country_documents ?>
                        </div>
                        <div class="form-group">
                            <label class="form-lable" id="Label">FAQ's</label>
                        </div>
                        <div class="form-group editableBox" id="faq" contenteditable="true">
                            <?php echo $visa_country_documents ?>
                        </div>
                        <?php
$selectedVisaTypes = json_decode($visa_country_services, true); // Decode JSON to PHP array

$allVisaTypes = [
    'tourist' => 'Tourist',
    'business' => 'Business',
    'transit' => 'Transit',
    'student' => 'Student',
    'renewal' => 'Renewal',
];
?>

                        <div class="form-group-box">
                            <p>VISA service options<sup>*</sup></p>
                            <?php foreach ($allVisaTypes as $value => $label): ?>
                            <?php
            $checkboxId = 'visasType_' . $value;
            $isChecked = in_array($value, $selectedVisaTypes) ? 'checked' : '';
        ?>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="checkbox" id="<?= $checkboxId ?>" name="visaTypes"
                                            value="<?= $value ?>" <?= $isChecked ?> />
                                        <label class="radio-button" for="<?= $checkboxId ?>"><?= $label ?></label>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" id="countryId" name="countryId" value="<?php echo $id ?>">
                                <a href="#" class="btn popUpBtn" id="editCountry"><i class="fi-rr-check"></i> Update
                                    country</a>
                                <!-- <input type="submit" class="btn btn-green" value="Add country"> -->
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>
<?php
ob_end_flush();
?>