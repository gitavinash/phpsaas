<?php

    require_once 'header.php';

    $calenderDate=((isset($date)) ? $date : date('Y-m-d'));

?>

<section>
    <!-- <h3>September 2020 ><span>Choose a date below to place a request for pangat</span></h3> -->
    <div class="booking-form">
        <h3>Select Date and Time<span>Choose a date below to place a request for pangat</span></h3>
        <div class="loader"><img src="<?php echo IMG_LIBRARY ?>circle-loader.gif" alt=""></div>
        <div class="calender">
        </div>
        <div class="calenderNote">Note: In case of any issue, please call ​Mandir ​at 011-2367</div>
        <div class="calender-info">
            <p><span class="red">Not available</span></p>
            <p><span class="yellow">Request received</span></p>
            <p><span class="green">Available</span></p>
        </div>
    </div>
    <div class="form" style="display:none" >
        <p id="bookingSlotInfo"></p>
        <h2>Enter Details</h2>
        <form id="enquiryForm" >
            <div class="form-group">
                <input type="text" name="first_name" class="form-control inside-shadow" id="first_name" placeholder="First Name*" >
            </div>
            <div class="form-group">
                <input type="text" name="last_name" class="form-control inside-shadow" id="last_name" placeholder="Last Name" >
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control inside-shadow" id="email" placeholder="Email*" >
            </div>
            <div class="form-group">
                <input type="number" name="mobile" class="form-control inside-shadow" id="mobile" placeholder="Mobile Number*" >
            </div>
            <div class="form-group">
                <input type="number" name="guest" class="form-control inside-shadow" id="guest" placeholder="Guest*" >
            </div>
            <!-- <div class="form-group">
                <textarea name="details" id="details" class="form-control inside-shadow" placeholder="Add on details ( if any )" ></textarea>
            </div> -->
            <div class="form-group">
                <input type="checkbox" name="self" id="selfCheckin" > I will be there in pangat.
            </div>
            <input type="submit" class="btn btn-green" value="Submit" id="pangatBookBtn" >
        </form>
    </div>
    <div class="statusMsg" id="thankYou" style="display: none" >
        <h2>Thank You</h2>
        <p>Your request has been taken. You will be informed on the number - <span id="mobilenumber"></span></p>
        <p><a href="//srisanwalimurat.com/">Go to homepage</a></p>
    </div>
    <div class="statusMsg" id="error" style="display: none" >
        <h2>Error</h2>
        <p>Something is wrong while sending your request to server</p>
        <p><a href="/">Try Again</a></p>
    </div>
</section>

<?php

    require_once 'footer.php';

?>