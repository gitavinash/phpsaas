<?php

$postStatus = mysqli_real_escape_string($connection, $_GET['status']);
$postStatus = (($postStatus == 'all') ? '' : "AND enquiry_status='{$postStatus}'");
$selectedMonth = mysqli_real_escape_string($connection, ((isset($_GET['month'])) ? $_GET['month'] : date('m')));
$selectedYear = mysqli_real_escape_string($connection, ((isset($_GET['year'])) ? $_GET['year'] : date('Y')));

$selectedDate = mysqli_real_escape_string($connection, ((isset($_GET['date'])) ? $_GET['date'] : ''));
// $yearStatus=(($selectedDate=='') ? '' : "AND year(enquiry_time) = {$selectedDate}" );

if ($selectedDate != '') {

    $enquiryDate = "AND enquiry_time='" . date("{$selectedYear}-{$selectedMonth}-{$selectedDate}") . "'";
} else {

    $enquiryDate = (($selectedMonth == '') ? '' : " AND month(enquiry_time)={$selectedMonth}");
    $enquiryDate .= (($selectedYear == '') ? '' : " AND year(enquiry_time)={$selectedYear}");
}

// echo $enquiryDate;
// exit();

$selectedSlot = mysqli_real_escape_string($connection, ((isset($_GET['slot'])) ? $_GET['slot'] : ''));
$enquirySlot = (($selectedSlot == '') ? '' : "AND enquiry_time='{$selectedSlot}'");

?>
<h2>All Enquiries</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All
                    <?php echo $postType ?></option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Pending</option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Confirmed</option>
                <option value="2" <?php echo (($_GET['status'] == '2') ? 'selected' : '') ?>>Cancelled</option>
            </select>
            <select class="form-control" name="month" id="changeMonth" required>
                <option value="">Select month</option>
                <option value="01" <?php echo (($selectedMonth == '01') ? 'selected' : '') ?>>January</option>
                <option value="02" <?php echo (($selectedMonth == '02') ? 'selected' : '') ?>>Febuary</option>
                <option value="03" <?php echo (($selectedMonth == '03') ? 'selected' : '') ?>>March</option>
                <option value="04" <?php echo (($selectedMonth == '04') ? 'selected' : '') ?>>April</option>
                <option value="05" <?php echo (($selectedMonth == '05') ? 'selected' : '') ?>>May</option>
                <option value="06" <?php echo (($selectedMonth == '06') ? 'selected' : '') ?>>June</option>
                <option value="07" <?php echo (($selectedMonth == '07') ? 'selected' : '') ?>>July</option>
                <option value="08" <?php echo (($selectedMonth == '08') ? 'selected' : '') ?>>August</option>
                <option value="09" <?php echo (($selectedMonth == '09') ? 'selected' : '') ?>>September</option>
                <option value="10" <?php echo (($selectedMonth == '10') ? 'selected' : '') ?>>October</option>
                <option value="11" <?php echo (($selectedMonth == '11') ? 'selected' : '') ?>>November</option>
                <option value="12" <?php echo (($selectedMonth == '12') ? 'selected' : '') ?>>December</option>
            </select>
            <select class="form-control" name="year" id="changeYear" required>
                <option value="">Select year</option>
                <option value="2020" <?php echo (($selectedYear == '2020') ? 'selected' : '') ?>>2020</option>
                <option value="2021" <?php echo (($selectedYear == '2021') ? 'selected' : '') ?>>2021</option>
                <option value="2022" <?php echo (($selectedYear == '2022') ? 'selected' : '') ?>>2022</option>
                <option value="2023" <?php echo (($selectedYear == '2023') ? 'selected' : '') ?>>2023</option>
                <option value="2024" <?php echo (($selectedYear == '2024') ? 'selected' : '') ?>>2024</option>
                <option value="2025" <?php echo (($selectedYear == '2025') ? 'selected' : '') ?>>2025</option>
            </select>
            <select class="form-control" name="date" id="changeDater">
                <option value="">Select date</option>
                <?php

                for ($i = 1; $i <= 31; $i++) {

                    echo "<option value='{$i}' " . (($selectedDate == $i) ? 'selected' : '') . " >{$i}</option>";
                }

                ?>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table">
    <thead>
        <tr>
            <th>Enquiry Time</th>
            <th>Enquiry For</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Message</th>
            <!-- <th></th> -->
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_time >= CURDATE() {$postStatus} {$enquiryDate} {$enquirySlot} AND enquiry_site_id=" . APP_SITE_ID . " ORDER BY enquiry_time DESC")) {

            if (mysqli_num_rows($dataList) != 0) {

                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo date('d-M-Y H:s', strtotime($enquiry_time)) ?></td>
            <td><?php echo $enquiry_subject; ?></td>
            <td><?php echo $enquiry_first_name . ' ' . $enquiry_last_name; ?></td>
            <td><?php echo $enquiry_mobile; ?></td>
            <td><?php echo ucfirst($enquiry_message); ?></td>
            <!-- <td>
                <?php

                    switch ($enquiry_status) {

                        case 1:
                            echo "<a href='" . base64_encode($enquiry_id) . "' msg='You are cancelling the pangat booked on " . date('d-M-Y', strtotime($enquiry_time)) . " by " . $enquiry_first_name . "' data='{$enquiry_id}' action='cancel' class='btn btn-sm btn-red bookingCancelBtn'><i class='fas fa-times-circle'></i> Cancel</a>";
                            break;

                        case 2:
                            echo "<a href='" . base64_encode($enquiry_id) . "' date='" . date('Y-m-d') . "' time='{$enquiry_time}' msg='You want to cancel previous bookings and confirm the pangat booked on " . date('d-M-Y', strtotime($enquiry_time)) . " by " . $enquiry_first_name . "' data='{$enquiry_id}' action='confirm' class='btn btn-sm btn-blue bookingConfirmBtn'><i class='fas fa-times-circle'></i> Cancel Previous and Confirm</a>";
                            break;

                        default:
                            echo "<a href='" . base64_encode($enquiry_id) . "' date='" . date('Y-m-d') . "' time='{$enquiry_time}' msg='You are confirm the pangat booked on " . date('d-M-Y', strtotime($enquiry_time)) . " by " . $enquiry_first_name . "' data='{$enquiry_id}' action='confirm' class='btn btn-sm btn-green bookingConfirmBtn'><i class='fas fa-times-circle'></i> Confirm</a>";
                            echo "<a href='" . base64_encode($enquiry_id) . "' msg='You are cancelling the pangat booked on " . date('d-M-Y', strtotime($enquiry_time)) . " by " . $enquiry_first_name . "' data='{$enquiry_id}' action='cancel' class='btn btn-sm btn-red bookingCancelBtn'><i class='fas fa-times-circle'></i> Cancel</a>";
                            break;
                    }

                ?>
            </td> -->
        </tr>
        <?php

                }
            }
        } else {
            if (DEVELOPMENT_MODE == true) {
                echo mysqli_error($connection);
            }
        }

        ?>
    </tbody>
</table>