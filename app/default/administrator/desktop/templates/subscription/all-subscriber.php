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
        $subscriptionAndUsersquery = "
SELECT 
    p.profile_display_name, 
    p.profile_email, 
    sa.site_access_user_id, 

    sub.subscription_id,
    sub.subscription_status,
    sub.subscription_mode,
    sub.subscription_time,
    sub.subscription_expiry,

    prod.product_id,
    prod.product_title

FROM " . TABLE_PREFIX . "site_access sa

INNER JOIN " . TABLE_PREFIX . "profile p 
    ON p.profile_user_id = sa.site_access_user_id

LEFT JOIN " . TABLE_PREFIX . "subscription sub 
    ON sa.site_access_user_id = sub.subscription_user_id 
    AND sub.subscription_expiry > NOW()

LEFT JOIN " . TABLE_PREFIX . "product prod 
    ON sub.subscription_relation_id = prod.product_id

WHERE sa.site_access_type = 'subscriber' 
  AND sa.site_access_site_id = " . SITE_ID . "

ORDER BY 
    CASE 
        WHEN sub.subscription_status = 1 THEN 0
        WHEN sub.subscription_status = 2 THEN 1
        ELSE 2
    END,
    sub.subscription_expiry ASC";

        if ($dataList = mysqli_query($connection, $subscriptionAndUsersquery)) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo $profile_email; ?></td>
            <td>
                <?php
                            if ($subscription_status == 1) {
                                echo 'Yes';
                            } elseif ($subscription_status == 2) {
                                echo 'No';
                            } else {
                                echo ''; // Show nothing if $subscription_status is NULL or any other value
                            }
                            ?>
            </td>
            <td><?php echo $product_title; ?></td>
            <td>
                <?php

                            if ($subscription_status != '') {
                                echo date('d M Y', strtotime($subscription_expiry));
                            }

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
                                echo "<a href='#' id='" . base64_encode($site_access_user_id) . "_user'
                class='btn btn-red btn-sm
                deleteBtn'><i class='fas fa-trash'></i> Delete user</a>";
                            } else {
                                echo "
                <a href='#' id='" . base64_encode($site_access_user_id) . "_subscriptionactive1month'
                class='btn btn-blue btn-sm deleteBtn'><i class='fas fa-check'></i> Active 1 Month</a>";
                                echo "
                <a href='#' id='" . base64_encode($site_access_user_id) . "_subscriptionactive1year'
                class='btn btn-blue btn-sm deleteBtn'><i class='fas fa-check'></i> Active 1 Year</a>";
                                echo "<a href='#' id='" . base64_encode($site_access_user_id) . "_user'
                class='btn btn-red btn-sm
                deleteBtn'><i class='fas fa-trash'></i> Delete user</a>";
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