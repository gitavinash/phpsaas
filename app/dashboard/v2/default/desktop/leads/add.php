<h2 class="heading"><?php echo 'Add New lead' ?><span>Insert new lead into CRM</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Lead details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
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
                            <input id="email" name="email" type="text" />
                            <label class="form-lable" id="Label">Emai</label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="text" required />
                            <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>Tour package<sup>*</sup></p>
                            <select class="selectJS form-control" name="package" id="package">
                                <option value="">Select destination</option>
                                <option value="himachal">Himachal</option>
                                <option value="uttarakhand">Uttarakhand</option>
                                <option value="jammu & kashmir">Jammu & Kashmir</option>
                                <option value="ladakh">Ladakh</option>
                                <option value="punjab">Punjab</option>
                                <option value=""></option>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input class="datepickerForFutureDate" id="dateOfTravel" name="date_of_travel" type="text"
                                required />
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
                            <input id="currentLocation" name="current_location" type="text" required />
                            <label class="form-lable" id="Label">Current location<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="pickupLocation" name="pickup_location" type="text" required />
                            <label class="form-lable" id="Label">Pickup location<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="dropLocation" name="drop_location" type="text" />
                            <label class="form-lable" id="Label">Drop location ( ignore if same
                                )</label>
                        </div>
                        <div class="form-group">
                            <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Any requirements</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="submit" class="btn btn-green" id="addEnquiry" value="Add new lead">
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>