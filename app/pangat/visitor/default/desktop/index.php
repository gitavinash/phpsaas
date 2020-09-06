<?php

    require_once 'header.php';

?>

<section>
    <h1>Book Pangat Online</h1>
    <div class="booking-form">
        <div class="booking-calender-box">
            <h2 id="booking-calender-title" >Select Date for Pangat</h2>
            <div class="booking-calender">
            </div>
        </div>
        <div class="booking-slot" style='display: none' >
            <h2>Select Time for Pangat</h2>
            <p class='green' id='breakfast' >Breakfast</p>
            <p class='green' id='lunch' >Lunch</p>
            <p class='green' id='dinner' >Dinner</p>
        </div>
        <div class="form" style='display: none' >
            <h2>Fill this form to complete booking</h2>
            <form id='enquiry-form'>
                <div class="form-group">
                    <input type="text" class="form-control inside-shadow" id="name" placeholder="Full Name*" name="" autofocus required >
                </div>
                <div class="form-group">
                    <input type="email" class="form-control inside-shadow" id="email" placeholder="E-mail*" name="" autofocus required >
                </div>
                <div class="form-group">
                    <input type="number" class="form-control inside-shadow" id="mobile" placeholder="Mobile*" name="" autofocus required >
                </div>
                <div class="form-group">
                    <textarea name="details" class="form-control inside-shadow" id="details" placeholder="Add on details ( if any )" ></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" id="submitForm" class="btn btn-green" value="Complete Booking" >
                </div>
            </form>
        </div>
    </div>
    <div class="calender-info">
        <p><span class="red">Not available</span></p>
        <p><span class="yellow">Request received</span></p>
        <p><span class="green">Available</span></p>
    </div>
</section>

<?php

    require_once 'footer.php';

?>