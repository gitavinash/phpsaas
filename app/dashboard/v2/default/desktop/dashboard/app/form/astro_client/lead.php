<section class="form">
    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h1>Enter lead details</h1>
            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
        </div>
        <div class="form-group">
            <input id="firstName" name="name" type="text" required />
            <label class="form-lable" id="Label">Full name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="email" name="email" type="email" />
            <label class="form-lable" id="Label">Email</label>
        </div>
        <div class="form-group">
            <input id="mobile" name="mobile" type="number" required />
            <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="mobile" name="alternate_mobile" type="number" />
            <label class="form-lable" id="Label">Alternate Mobile</label>
        </div>
        <div class="form-group">
            <input id="dateOfBirth" class="datepicker" name="date_of_birth" type="text" required />
            <label class="form-lable" id="Label">Date of Birth<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="placeOfBirth" name="place_of_birth" type="text" required />
            <label class="form-lable" id="Label">Place of Birth<sup>*</sup></label>
        </div>
        <div class="box-5">
            <div class="form-group">
                <select class="selectJS form-control" name="time_of_birth_in_hour" id="timeOfBirthInHour" required>
                    <option value="">Select Time of Birth ( Hour )</option>
                    <?php
for ($i = 0; $i <= 23; $i++) {
    echo "<option value='{$i}'>{$i}</option>";
}
?>
                </select>
                <label class="form-lable" id="timeOfBirthInHour">Time of Birth ( Hour )<sup>*</sup></label>
            </div>
        </div>
        <div class="box-5">
            <div class="form-group">
                <select class="selectJS form-control" name="time_of_birth_in_minute" id="timeOfBirthInMinute" required>
                    <option value="">Select Time of Birth ( Minute )</option>
                    <?php
for ($i = 0; $i <= 59; $i++) {
    echo "<option value='{$i}'>{$i}</option>";
}
?>
                </select>
                <label class="form-lable" id="timeOfBirthInMinute">Time of Birth ( Minute )<sup>*</sup></label>
            </div>
        </div>
        <div class="box-1">
            <div class="form-group">
                <!-- <input type="submit" class="btn btn-green" id="addEnquiry" value="Add new lead"> -->
                <input type="hidden" name="type" value="enquiry">
                <input type="hidden" name="action" value="new">
                <input type="hidden" name="subject" value="New client">
                <input type="hidden" name="sender" value="<?php echo base64_encode("aviansh@blumox.com") ?>">
                <input type="hidden" name="redirect_url" value="/dashboard/leads/list/">
                <input type="submit" class="btn btn-green" value="Add lead">
            </div>
        </div>
    </div>
</section>