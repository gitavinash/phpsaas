<h2>All Subscribers</h2>
<div class="options-bar">
    <div class="left-box">
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table">
    <thead>
        <tr>
            <!-- <th style="width: 50px"></th> -->
            <th>Full name</th>
            <th>Email</th>
            <th>Subscription Active</th>
            <th>Subscription Plan</th>
            <th>Expiry date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT profile_display_name, profile_email, site_access_user_id, 
       CASE WHEN subscription_user_id IS NOT NULL THEN product_id ELSE NULL END AS product_id,
       CASE WHEN subscription_user_id IS NOT NULL THEN product_title ELSE NULL END AS product_title,
       subscription_id,
       subscription_status,
       subscription_mode,
       subscription_time,
       subscription_expiry
FROM " . TABLE_PREFIX . "site_access sa
LEFT JOIN " . TABLE_PREFIX . "subscription sub ON site_access_user_id = subscription_user_id
LEFT JOIN " . TABLE_PREFIX . "product prod ON subscription_relation_id = product_id
INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id
WHERE site_access_type='subscriber' AND subscription_expiry > NOW() AND site_access_site_id = " . SITE_ID . " ORDER BY subscription_expiry ASC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo $profile_email; ?></td>
            <td><?php echo (($subscription_status == 1) ? 'Yes' : 'No'); ?></td>
            <td><?php echo $product_title; ?></td>
            <td>
                <?php

                            echo date('d M Y', strtotime($subscription_expiry));

                            ?>
            </td>
            <td>
                <?php

                            if ($subscription_status == 1) {
                                echo "<a href='#' id='" . base64_encode($subscription_id) . "_subscription'
                class='btn btn-red btn-sm
                deleteBtn'><i class='fas fa-times'></i> Suspend</a>";
                            } else if ($subscription_status == 2) {
                                echo "<a href='#' id='" . base64_encode($subscription_id) . "_subscriptionactive'
                class='btn btn-blue btn-sm
                deleteBtn'><i class='fas fa-times'></i> Active</a>";
                            } else {
                                echo "
                <a href='#' id='" . base64_encode($site_access_user_id) . "_subscriptionactive1month'
                class='btn btn-blue btn-sm deleteBtn'><i class='fas fa-check'></i> Active 1 Month</a>";
                                echo "
                <a href='#' id='" . base64_encode($site_access_user_id) . "_subscriptionactive1year'
                class='btn btn-blue btn-sm deleteBtn'><i class='fas fa-check'></i> Active 1 Year</a>";
                            }

                            ?>
            </td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>