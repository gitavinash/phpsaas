<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed here.</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/packages/domestic/add/"><i class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value=""><?php echo ucwords($urlStructure[1]) ?> Status</option>
                    <option value="1">Confirm</option>
                    <option value="2">Pending</option>
                    <option value="3">Cancel</option>
                </select>
            </div>
            <div class="form-group right">
                <label>Date</label>
                <input type="text" class="dateRangePicker" placeholder="Select date range">
            </div>
        </div>
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

                    if ($packageData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_status!=3 AND tour_package_site_id=" . SITE_ID . " ORDER BY tour_package_created_at DESC")) {
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
                                        <td>{$i}. {$tour_package_title}</td>
                                        <td>" . ucwords($tour_package_theme) . "</td>
                                        <td>{$tour_package_city}</td>
                                        <td>{$tour_package_duration}N/" . ($tour_package_duration + 1) . "D</td>
                                        <td>" . ucwords(str_replace('_', ' ', $tour_package_hotel)) . "</td>
                                        <td>{$packageStatus}</td>
                                        <td>
                                            <a href='/dashboard/packages/domestic/edit/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Edit package' data-title='Edit package'><i class='fi fi-rr-edit bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/price/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Edit price' data-title='Edit price'><i class='fi fi-rr-indian-rupee-sign bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/price-list/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Price lists' data-title='Price list'><i class='fi fi-rr-menu-burger bgWhite'></i></a>
                                            <a href='/dashboard/packages/domestic/gallery/?id=" . base64_encode($tour_package_id) . "' class='tableActionIconBtn' title='Manage gallery' data-title='Manage gallery'><i class='fi fi-rr-images bgWhite'></i></a>
                                            <a href='/view-itinerary/?id=" . base64_encode($tour_package_id) . "&theme=" . base64_encode(1) . "' target='_blank' class='tableActionIconBtn' title='View package' data-title='View package'><i class='fi fi-rr-eye btn-blue'></i></a>
                                            <a href='#' data-type='packages' data-id='{$tour_package_id}' title='Delete package' data-type='{$tour_package_type}' data-title='Delete package' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
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