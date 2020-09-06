<?php

    require_once 'header.php';

    $calenderDate=((isset($date)) ? $date : date('Y-m-d'));

?>

<section>
    <h1>Book Pangat Online</h1>
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