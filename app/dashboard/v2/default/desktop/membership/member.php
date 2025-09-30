<h2 class="heading">Membership List
    <span>All membership list will be displayed here</span>
</h2>

<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/membership/sell/"><i class="fi fi-rr-plus"></i></a>
    </div>

    <div class="list-box">
        <?php
        $rows = [];
        $sql = "SELECT membership_id, membership_profile_name, membership_total_visit, membership_avl_visit, user_mobile, membership_status
        FROM " . TABLE_PREFIX . "membership
        INNER JOIN " . TABLE_PREFIX . "warehouse ON membership_branch_id=warehouse_id
        INNER JOIN " . TABLE_PREFIX . "user ON membership_user_id=user_id
        WHERE membership_site_id=" . SITE_ID . "
        ORDER BY membership_profile_name ASC, membership_id DESC";

        if ($dataList = mysqli_query($connection, $sql)) {
            while ($r = mysqli_fetch_assoc($dataList)) {
                $rows[] = $r;
            }
        } else if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "<pre>SQL Error: " . htmlspecialchars(mysqli_error($connection)) . "</pre>";
        }
        ?>
        <div class="only-mobile membership-card-list">
            <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $data): ?>
            <?php
                    $id   = (int)$data['membership_id'];
                    $name = htmlspecialchars(ucwords($data['membership_profile_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $mob  = htmlspecialchars($data['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_visits  = htmlspecialchars($data['membership_total_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    $membership_avl_visits  = htmlspecialchars($data['membership_avl_visit'] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
            <article class="widget-setting-info membership-card" data-id="<?php echo $id; ?>">
                <div class="about-feature" style="max-width:100%;">
                    <h3 class="heading" style="margin:0;">
                        <?php echo $name; ?>
                        <span>#<?php echo $id; ?></span>
                    </h3>

                    <hr>

                    <div class="row">
                        <div class="box-12 info-stack">
                            <p style="margin:0;"><strong>Mobile</strong><br><span><?php echo $mob; ?></span></p>
                            <p style="margin:0;"><strong>Total
                                    Visits</strong><br><span><?php echo $membership_visits; ?></span>
                            </p>
                            <p style="margin:0;"><strong>Avl.
                                    Visits</strong><br><span><?php echo $membership_avl_visits; ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Actions stacked on new lines -->
                    <div class="action-stack" style="margin-top:10px;">
                        <div class="action-item">
                            <a class="btn btn-light dataTableBtn"
                                href="membership/update/?id=<?php echo base64_encode($id); ?>" title="View membership"
                                data-title="View membership">
                                <i class="fi-rr-pencil"></i> Edit
                            </a>
                        </div>
                        <div class="action-item">
                            <button class="btn btn-red dataTableBtn deleteButton" data-type="membership"
                                data-id="<?php echo $id; ?>" data-title="Delete membership">
                                <i class="fi-rr-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state"><i class="fi fi-rr-box-open"></i> No memberships found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>