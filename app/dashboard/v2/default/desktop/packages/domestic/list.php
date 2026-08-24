<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All tour packages list will be displayed here.</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/packages/domestic/add/"><i class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Theme</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Hotel</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($packageData = mysqli_query($connection, "SELECT tour_package_id, tour_package_title, tour_package_type, tour_package_city, tour_package_theme, tour_package_duration, tour_package_hotel, tour_package_status, link_url FROM " . TABLE_PREFIX . "tour_package INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=tour_package_id AND link_status=200 AND link_site_id=" . SITE_ID . " AND link_type='tour_package' WHERE tour_package_status!=3 AND tour_package_type='domestic' AND tour_package_site_id=" . SITE_ID . " ORDER BY tour_package_created_at DESC")) {
                        if (mysqli_num_rows($packageData) != 0) {
                            $i = 1;
                            while ($packageDataInfo = mysqli_fetch_assoc($packageData)) {
                                extract($packageDataInfo);
                                switch ($tour_package_status) {
                                    case 1:
                                        $packageStatus = 'Active';
                                        break;
                                    case 2:
                                        $packageStatus = 'Draft';
                                        break;
                                    case 3:
                                        $packageStatus = 'Delete';
                                        break;
                                    default:
                                        $packageStatus = 'Pending';
                                        break;
                                }
                                echo "
                                    <tr>
                                        <td>{$tour_package_title}</td>
                                        <td>" . ucwords($tour_package_theme) . "</td>
                                        <td>{$tour_package_city}</td>
                                        <td>{$tour_package_duration}N/" . ($tour_package_duration + 1) . "D</td>
                                        <td>" . ucwords(str_replace('_', ' ', $tour_package_hotel)) . "</td>
                                        <td>{$packageStatus}</td>
                                        <td>
                                            <a href='/dashboard/packages/domestic/edit/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Edit package' data-title='Edit package'><i class='fi fi-rr-edit bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/gallery/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Package gallery' data-title='Package gallery'><i class='fi fi-rr-gallery bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/price-list/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Price lists' data-title='Price list'><i class='fi fi-rr-indian-rupee-sign bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/hotel-list/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Hotel lists' data-title='Hotel list'><i class='fi fi-rr-hotel bgWhite'></i></a>
                                            <a href='#' class='tableActionIconBtn movePackageBtn' data-id='{$tour_package_id}' data-target-type='international' title='Move to International Package' data-title='Move to International Package'><i class='fi fi-rr-plane-alt bgWhite' style='color: #0284c7;'></i></a>
                                            <a href='{$link_url}' target='_blank' class='tableActionIconBtn' title='View package' data-title='View package'><i class='fi fi-rr-eye btn-blue'></i></a>
                                            <a href='#' data-type='packages' data-id='{$tour_package_id}' title='Delete package' data-type='{$tour_package_type}' data-title='Delete package' class='tableActionIconBtn deleteButton' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                                        </td>
                                    </tr>
                                ";
                                $i++;
                            }
                        } else {
                            echo "<tr>
                            <td colspan=100>No Data Found</td>
                            </tr>";
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $(document).on("click", ".movePackageBtn", function(e) {
            e.preventDefault();
            const $btn = $(this);
            const packageId = $btn.data("id");
            const targetType = $btn.data("target-type") || "international";
            const targetLabel = targetType === "international" ? "International" : "Domestic";

            Swal.fire({
                title: `Move to ${targetLabel}?`,
                text: `Are you sure you want to move this package to ${targetLabel} packages?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0284c7",
                cancelButtonColor: "#64748b",
                confirmButtonText: `Yes, move to ${targetLabel}`
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "/api/aleya/packages/move-type/",
                        type: "POST",
                        data: {
                            packageId: packageId,
                            targetType: targetType
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === "success") {
                                popUpMessage("center", res.msg, "success");
                                setTimeout(() => location.reload(), 1200);
                            } else {
                                popUpMessage("center", res.msg || "Failed to move package", "error");
                            }
                        },
                        error: function() {
                            popUpMessage("center", "Error moving tour package", "error");
                        }
                    });
                }
            });
        });
    });
</script>