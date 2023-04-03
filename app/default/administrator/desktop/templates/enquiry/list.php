<?php

$enquiryStatus = mysqli_real_escape_string($connection, $_GET['status']);
$enquiryStatus = (($enquiryStatus == 'all') ? '' : "AND enquiry_status='{$enquiryStatus}'");

?>
<h2>All Enquiries</h2>
<div class="options-bar">
    <div class="left-box">
        <!-- <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <input type="submit" class="btn" value="Filter">
        </form> -->
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Message</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($dataList) != 0) {

                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo $enquiry_first_name . ' ' . $enquiry_last_name; ?></td>
            <td><?php echo $enquiry_email; ?></td>
            <td><?php echo $enquiry_mobile; ?></td>
            <td><?php echo $enquiry_message; ?></td>
            <td><?php echo (($enquiry_query != '') ? $enquiry_query : 'No'); ?></td>
            <td></td>
        </tr>
        <?php

                }
            }
        }

        ?>
    </tbody>
</table>