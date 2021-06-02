<h2>Manage calender</h2>
<div class="form-box">
    <div class="calender-box">
        <div id="myCalendar" class="vanilla-calendar">
            <div class="vanilla-calendar-header">
                <button class="vanilla-calendar-btn" id="prearrow" disabled="">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div class="vanilla-calendar-header__label" id="month"></div>
                <button class="vanilla-calendar-btn" id="nexarrow">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <div class="vanilla-calendar-week">
                <span>S</span>
                <span>M</span>
                <span>T</span>
                <span>W</span>
                <span>T</span>
                <span>F</span>
                <span>S</span>
            </div>
            <div class="calendar-body-wraper d-none">
                <ul class="calendar-dates vanilla-calendar-body m-0 p-0" id="calendardates"></ul>
            </div>
            <button id="blockDate" class="submit-data">Block calender date</button>
        </div>
    </div>
    <div class="block-dates">
        <table id="table">
            <thead>
                <tr>
                    <th>Booked Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php

					if($dataList=mysqli_query($connection, "SELECT booking_date FROM ".TABLE_PREFIX."booking WHERE booking_status=4 AND booking_date >= CURDATE() GROUP BY booking_date ORDER BY booking_date ASC")) {

						if(mysqli_num_rows($dataList)!=0) {
								
							while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
                <tr>
                    <td><?php echo date('d-M-Y', strtotime($booking_date))?></td>
                    <td><button class="unblockDate btn btn-sm btn-green"
                            id="<?php echo date('Y-m-d', strtotime($booking_date)) ?>">Unblock</button></td>
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
    </div>
</div>