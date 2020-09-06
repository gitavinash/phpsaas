<?php

    require_once 'header.php';

    $calenderDate=((isset($date)) ? $date : date('Y-m-d'));

?>

<section>
    <h1>Book Pangat Online</h1>
    <div class="booking-form">
        <div class="calender">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                        <th>Sun</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                        // Week Days

                        echo "<tr class='week-day' >";
                        echo "<td></td>";
                        for($i=0; $i<7; $i++) {

                            $weekDate=date('d-m-Y', strtotime("+{$i} days"));

                            if($i>=date('w', strtotime($calenderDate))) {
                                echo "<td>{$weekDate}</td>";
                            } else {
                                echo "<td></td>";
                            }

                        }
                        echo "</tr>";

                        // Breakfast Check

                        echo "<tr>";
                        echo "<td>Breakfast</td>";
                        for($i=0; $i<7; $i++) {

                            $weekDate=date('d-m-Y', strtotime("+{$i} days"));

                            if($i>=date('w', strtotime($calenderDate))) {
                                echo "<td><a herf='#' id='".date('w', strtotime($calenderDate))."' class='green bookBeakfast '></a></td>";
                            } else {
                                echo "<td><p class='red'></p></td>";
                            }

                        }
                        echo "</tr>";
                        
                        // Lunch Check

                        echo "<tr>";
                        echo "<td>Lunch</td>";
                        for($i=0; $i<7; $i++) {

                            if($i>=date('w', strtotime($calenderDate))) {
                                echo "<td><a herf='#' class='green'></a></td>";
                            } else {
                                echo "<td><p class='red'></p></td>";
                            }

                        }
                        echo "</tr>";
                        
                        // Dinner Check

                        echo "<tr>";
                        echo "<td>Dinner</td>";
                        for($i=0; $i<7; $i++) {

                            if($i>=date('w', strtotime($calenderDate))) {
                                echo "<td><a herf='#' class='green'></a></td>";
                            } else {
                                echo "<td><p class='red'></p></td>";
                            }

                        }
                        echo "</tr>";

                    ?>
                    <tr>
                        <td colspan="4" >Previous Month</td>
                        <td colspan="4" style="float:right;"><a href="?date=<?php echo date('Y-m-d', strtotime("+7 Days", strtotime($weekDate))) ?>" style="text-decoration: none; color: inherit" >Next Month</a></td>
                    </tr>
                </tbody>
            </table>
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