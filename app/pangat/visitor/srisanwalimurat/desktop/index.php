<?php

    require_once 'header.php';

    $calenderDate=((isset($date)) ? $date : date('Y-m-d'));

?>

<section>
    <!-- <h3>September 2020 ><span>Choose a date below to place a request for pangat</span></h3> -->
    <div class="booking-form">
        <div class="calender">
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