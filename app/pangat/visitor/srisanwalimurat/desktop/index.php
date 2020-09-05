<?php

    require_once 'header.php';

?>

<section>
    <h1>Book Pangat Online</h1>
    <div class="booking-form">
        <h2>Select Date for Pangat</h2>
        <div class="booking-calender"></div>
        <div class="booking-slot">
            <h2>Select Time for Pangat</h2>
            <p class='green' >Breakfast</p>
            <p class='green' >Lunch</p>
            <p class='green' >Dinner</p>
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