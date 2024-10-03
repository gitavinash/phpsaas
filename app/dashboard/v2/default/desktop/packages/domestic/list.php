<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed here.</span>
</h2>
<div class="main-box">
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
                        <th>S no.</th>
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

                    if ($packageData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_status!=3 AND tour_package_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($packageData) != 0) {
                            $i = 1;
                            while ($packageDataInfo = mysqli_fetch_assoc($packageData)) {
                                extract($packageDataInfo);
                                switch ($tour_package_status) {
                                    case 1:
                                        $packageStatus = 'Active';
                                        $packageStatusBackground = '#82e0aa';
                                        break;
                                    case 2:
                                        $packageStatus = 'Draft';
                                        $packageStatusBackground = '#6C757D';
                                        break;
                                    case 3:
                                        $packageStatus = 'Delete';
                                        $packageStatusBackground = '#DC3545';
                                        break;
                                    default:
                                        $packageStatus = 'Pending';
                                        $packageStatusBackground = '#FFC107';
                                        break;
                                }
                                echo "
                                    <tr style='background-color: {$packageStatusBackground}' >
                                        <td>{$i}</td>
                                        <td>{$tour_package_title}</td>
                                        <td>{$tour_package_theme}</td>
                                        <td>{$tour_package_city}</td>
                                        <td>{$tour_package_duration} Nights</td>
                                        <td>" . ucwords(str_replace('_', ' ', $tour_package_hotel)) . "</td>
                                        <td>{$packageStatus}</td>
                                        <td></td>
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