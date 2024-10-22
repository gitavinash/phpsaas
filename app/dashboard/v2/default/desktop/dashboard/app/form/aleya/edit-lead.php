<tr>
    <td class="box-3"><strong>First name<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="first_name"
            id="firstName" value="<?php echo $enquiry_first_name ?>" required></td>
</tr>
<tr>
    <td class="box-3"><strong>Middle name</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="middle_name"
            id="middleName" value="<?php echo $enquiry_middle_name ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Last name</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="last_name"
            id="lastName" value="<?php echo $enquiry_last_name ?>"></td>
</tr>
<tr>
    <td class="box-3"><strong>Email</strong></td>
    <td class="box-7"><input type="text" class="form-control" name="email" id="email"
            value="<?php echo $enquiry_email ?>">
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Mobile<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile"
            value="<?php echo $enquiry_mobile ?>" readonly>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Destination<sup>*</sup></strong></td>
    <td class="box-7">
        <select class="indianCityAutoComplete" style="float: left; width:100%" name="package" id="package">
            <option value="<?php echo $enquiry_subject ?>"><?php echo $enquiry_subject ?></option>
        </select>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Travel style<sup>*</sup></strong></td>
    <td class="box-7">
        <div class="form-group">
            <select class="selectJS form-control" name="travelStyle" id="travelStyle">
                <option value="select">Select travel style</option>
                <option value="adventure">Adventure Travel</option>
                <option value="cultural" selected>Cultural Travel</option>
                <option value="luxury">Luxury Travel</option>
                <option value="solo">Solo Travel</option>
                <option value="group">Group Travel</option>
                <option value="eco">Eco/Responsible Travel</option>
                <option value="honeymoon">Honeymoon Travel</option>
                <option value="couple">Couple Travel</option>
                <option value="family">Family Travel</option>
                <option value="wellness">Wellness/Retreat Travel</option>
                <option value="budget">Budget Travel</option>
                <option value="roadtrip">Road Trip</option>
                <option value="cruise">Cruise Travel</option>
                <option value="backpacking">Backpacking Travel</option>
                <option value="religious">Religious Travel</option>
            </select>
        </div>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Date of travel<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="datepickerForFutureDate"
            name="date_of_travel" id="date_of_travel"
            value="<?php echo date('d-m-Y', strtotime($date_of_travel)) ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Travel duration<span class="secondLine">( in Nights
                )</span><sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="number_of_days"
            id="number_of_days" placeholder="3" value="<?php echo $number_of_days ?>"
            required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Number of adults<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="number_of_adults"
            id="number_of_adults" placeholder="0"
            value="<?php echo $number_of_adults ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Number of children<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="number_of_childrens"
            id="number_of_childrens" placeholder="0"
            value="<?php echo $number_of_childrens ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Number of infants<span class="secondLine">( Below 5
                years)</span><sup>*</sup></strong>
    </td>
    <td class="box-7"><input type="text" class="form-control" name="number_of_infants"
            id="number_of_infants" placeholder="0"
            value="<?php echo $number_of_infants ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Current location<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="current_location"
            id="current_location" value="<?php echo $current_location ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Pickup location<sup>*</sup></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="pickup_location"
            id="pickup_location" value="<?php echo $pickup_location ?>" required>
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Drop location<span class="secondLine">( ignore if same
                )</span></strong></td>
    <td class="box-7"><input type="text" class="form-control" name="drop_location"
            id="drop_location" value="<?php echo $drop_location ?>">
    </td>
</tr>
<tr>
    <td class="box-3"><strong>Any requirements</strong></td>
    <td class="box-7">
        <textarea name="requirements" class="form-control"
            id="requirements"><?php echo (($requirements == '') ? $enquiry_message : $requirements) ?></textarea>
    </td>
</tr>
<tr>
    <td class="box-3">
    </td>
    <td class="box-7">
        <input type="hidden" name="leadId" value="<?php echo $enquiry_id ?>">
        <input type="button" id="updateEnquiry" class="btn btn-green"
            value="Update lead">
    </td>
</tr>