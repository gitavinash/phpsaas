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
            <th>Sunscription Plan</th>
            <th>Expiry date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT profile_display_name, profile_email, site_access_user_id, 
       CASE WHEN subscription_user_id IS NOT NULL THEN product_id ELSE NULL END AS product_id,
       CASE WHEN subscription_user_id IS NOT NULL THEN product_title ELSE NULL END AS product_title,
       subscription_status,
       subscription_mode,
       subscription_time
FROM " . TABLE_PREFIX . "site_access sa
LEFT JOIN " . TABLE_PREFIX . "subscription sub ON site_access_user_id = subscription_user_id
LEFT JOIN " . TABLE_PREFIX . "product prod ON subscription_relation_id = product_id
INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id
WHERE site_access_type='subscriber' AND site_access_site_id = " . SITE_ID." ORDER BY subscription_time DESC )) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo $profile_email; ?></td>
            <td><?php echo $product_title; ?></td>
            <td>
                <?php

                            if ($subscription_time != null) {

                                if ($subscription_mode == 'month') {
                                    $increseDuration = "+1 month";
                                } else {
                                    $increseDuration = "+1 year";
                                }

                                echo date('d M Y', strtotime($subscription_time . $increseDuration));;
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