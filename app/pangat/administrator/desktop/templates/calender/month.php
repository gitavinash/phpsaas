<h2>Manage month </h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <select class="form-control" id="blockMonth" name="month" style="width: 200px" required>
                <option value="">Select month</option>
                <option value="01">January</option>
                <option value="02">Febuary</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select class="form-control" id="blockYear" name="year" style="width: 200px" required>
                <option value="">Select year</option>
                <?php
                
                    for($i=date('Y'); $i<=date('Y', strtotime('+5 years')); $i++) {

                        echo "<option value='{$i}'>{$i}</option>";

                    }

                ?>
            </select>
            <input type="submit" id="blockMonthBookingDates" class="btn" value="Block month booking">
        </form>
    </div>
    <div class="right-box">

    </div>

</div>
<h2 style="float:left; width: 100%; margin-bottom: 20px; margin-top: 50px">Blocked months</h2>
<table id="table">
    <thead>
        <tr>
            <th>Month</th>
            <th>Year</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php

		    if($dataList=mysqli_query($connection, "SELECT booking_date FROM ".TABLE_PREFIX."booking WHERE booking_status=4 AND booking_date >= CURDATE() AND booking_details='block by month' GROUP BY MONTH(booking_date) ORDER BY booking_date ASC")) {

			    if(mysqli_num_rows($dataList)!=0) {
								
				    while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
        <tr>
            <td><?php echo date('F', strtotime($booking_date))?></td>
            <td><?php echo date('Y', strtotime($booking_date))?></td>
            <td><button class="unblockMonth btn btn-sm btn-green"
                    id="<?php echo date('Y-m', strtotime($booking_date)) ?>">Unblock month</button></td>
        </tr>
        <?php
								
					}
				}
			
            } else {

				echo mysqli_error($connection);

			}

		?>
    </tbody>
</table>