<section class="form">
    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h1>Enter lead details</h1>
            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
        </div>
        <div class="form-group">
            <input id="firstName" name="first_name" type="text" required />
            <label class="form-lable" id="Label">First name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="middleName" name="middle_name" type="text" />
            <label class="form-lable" id="Label">Middle name</label>
        </div>
        <div class="form-group">
            <input id="lastName" name="last_name" type="text" />
            <label class="form-lable" id="Label">Last name</label>
        </div>
        <div class="form-group">
            <input id="email" name="email" type="email" />
            <label class="form-lable" id="Label">Email</label>
        </div>
        <div class="form-group">
            <input id="mobile" name="mobile" type="text" required />
            <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
        </div>
        <div class="form-group">
            <p class="form-lable">Tour package for<sup>*</sup></p>
            <select class="internationalCityAutoComplete" style="float: left; width:100%" name="package" id="package">
                <option value=""></option>
            </select>
        </div>
        <div class="form-group">
            <input class="datepickerForFutureDate" id="dateOfTravel" name="date_of_travel" type="text" required />
            <label class="form-lable" id="Label">Date of travel<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="numberOfDays" name="number_of_days" type="text" required />
            <label class="form-lable" id="Label">Travel duration ( in nights )<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="numberOfAdults" name="number_of_adults" type="text" required />
            <label class="form-lable" id="Label">Number of adults<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="numberOfChildrens" name="number_of_childrens" type="text" />
            <label class="form-lable" id="Label">Number of childrens</label>
        </div>
        <div class="form-group">
            <input id="numberOfInfants" name="number_of_infants" type="text" />
            <label class="form-lable" id="Label">Number of infants ( Below 5 years)</label>
        </div>
        <div class="form-group">
            <p class="form-lable" id="Label">Current location<sup>*</sup></p>
            <select class="internationalCityAutoComplete" style="float: left; width:100%" name="current_location"
                id="current_location">
                <option value=""></option>
            </select>
        </div>
        <div class="form-group">
            <p class="form-lable" id="Label">Pickup location<sup>*</sup></p>
            <select class="internationalCityAutoComplete" style="float: left; width:100%" name="pickup_location"
                id="pickup_location">
                <option value=""></option>
            </select>
        </div>
        <div class="form-group">
            <p class="form-lable" id="Label">Drop location ( ignore if same
                )</p>
            <select class="internationalCityAutoComplete" style="float: left; width:100%" name="drop_location"
                id="drop_location">
                <option value=""></option>
            </select>
        </div>
        <div class="form-group">
            <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
            <label class="form-lable" id="Label">Any requirements</label>
        </div>
        <div class="box-1">
            <div class="form-group">
                <input type="button" class="btn btn-green" id="addEnquiry" value="Add new lead">
            </div>
        </div>
    </div>
</section>