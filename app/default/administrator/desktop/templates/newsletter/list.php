<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
?>
<h2>All Subscribers</h2>
<table id="table">
    <thead>
        <tr>
            <th style="width: 400px">Email</th>
            <th style="width: 100px">Joining date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "newsletter WHERE newsletter_status=1 AND newsletter_site_id='" . SITE_ID . "' ORDER BY newsletter_email ASC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr style="height: 80px">
            <td>
                <p><?php echo $newsletter_email ?></p>
                <div class="hover">
                    <a href="#" id="<?php echo base64_encode($newsletter_id) ?>_newsletter" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                </div>
            </td>
            <td><?php echo date('d F Y h:i A', strtotime($newsletter_time)); ?></td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>