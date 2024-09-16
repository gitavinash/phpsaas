<tr>
    <td class="box-3"><strong>First name<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="first_name" id="firstName" value="<?php echo $enquiry_first_name ?>" required></td>
</tr>
<tr>
    <td class="box-3"><strong>Middle name</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="middle_name" id="middleName" value="<?php echo $enquiry_middle_name ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Last name</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="last_name" id="lastName" value="<?php echo $enquiry_last_name ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Email</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="email" id="email" value="<?php echo $enquiry_email ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Mobile<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile" value="<?php echo $enquiry_mobile ?>" readonly></td>
</tr>
<tr>
    <td class="box-3"><strong>Alternate Mobile<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile" value="<?php echo $enquiry_alt_mobile ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Date of Birth<sup>*</sup></strong></td>
    <td class="box-7"><input id="dateOfBirth" class="datepicker" name="date_of_birth" value="<?php echo $date_of_birth ?>" type="text" required /></td>
</tr>
<tr>
    <td class="box-3"><strong>Place of Birth<sup>*</sup></strong></td>
    <td class="box-7"><input id="placeOfBirth" name="place_of_birth" value="<?php echo $place_of_birth ?>" type="text" required /></td>
</tr>
<tr>
    <td class="box-3"><strong>Time of birth in hour<sup>*</sup></strong></td>
    <td class="box-7">
        <select class="selectJS form-control" name="time_of_birth_in_hour" id="timeOfBirthInHour" required>
            <option value="">Select Time of Birth ( Hour )</option>
            <?php
for ($i = 0; $i <= 23; $i++) {
    echo "<option value='{$i}' " . (($time_of_birth_in_hour == $i) ? 'selected' : '') . " >{$i}</option>";
}
?>
        </select>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Time of birth in minute<sup>*</sup></strong></td>
    <td class="box-7">
        <select class="selectJS form-control" name="time_of_birth_in_minute" id="timeOfBirthInMinute" required>
            <option value="">Select Time of Birth ( Minutes )</option>
            <?php
for ($i = 0; $i <= 59; $i++) {
    echo "<option value='{$i}' " . (($time_of_birth_in_minute == $i) ? 'selected' : '') . " >{$i}</option>";
}
?>
        </select>
    </td>
</tr>