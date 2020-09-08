<?php

    require_once 'header.php';

    $calenderDate=((isset($date)) ? $date : date('Y-m-d'));

?>

<section>
    <!-- <h3>September 2020 ><span>Choose a date below to place a request for pangat</span></h3> -->
    <div class="booking-form">
        <h3>Select Date and Time<span>Choose a date below to place a request for pangat</span></h3>
        <div class="calender">
        </div>
        <div class="calender-info">
            <p><span class="red">Not available</span></p>
            <p><span class="yellow">Request received</span></p>
            <p><span class="green">Available</span></p>
        </div>
    </div>
    <div class="form" style="display:none" >
        <h2>Enter Details</h2>
        <form id="enquiryForm" >
            <div class="form-group">
                <input type="text" name="name" class="form-control inside-shadow" id="name" placeholder="Full Name" >
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control inside-shadow" id="email" placeholder="Email" >
            </div>
            <div class="form-group">
                <input type="number" name="mobile" class="form-control inside-shadow" id="mobile" placeholder="Mobile Number" >
            </div>
            <div class="form-group">
                <input type="checkbox" name="self" id="self" > I will be there in pangat.
            </div>
            <input type="submit" class="btn btn-green" value="Submit" id="pangatBookBtn" >
        </form>
    </div>
</section>

<?php

    require_once 'footer.php';

?>