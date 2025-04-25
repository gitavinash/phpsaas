<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($listStatus)) {
    $statusCondition = "visa_package_status={$listStatus} AND ";
} else {
    $statusCondition = "visa_package_status=1 AND ";
}

if ($packageInfoList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}package INNER JOIN {$tableNamePrefix}country ON visa_package_country_id=visa_country_id WHERE {$statusCondition} visa_country_status=1 AND visa_country_site_id=" . SITE_ID . " AND visa_package_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($packageInfoList) != 0) {
        while ($packageData = mysqli_fetch_assoc($packageInfoList)) {
            $packageListData[] = $packageData;
        }
    }
}

?>
<h2 class="heading"><?php echo ucwords(str_replace('_', ' ', $urlStructure[1])) ?> List<span>All packages list will be
        displayed here</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/visa_packages/add/"><i class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <!-- <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value=""><?php echo ucwords($urlStructure[1]) ?> Status</option>
                    <option value="0">Fresh</option>
                    <option value="1">Confirm</option>
                    <option value="2">Pending</option>
                    <option value="3">Follow up</option>
                    <option value="4">Cancel</option>
                    <option value="5">Duplicate</option>
                </select>
            </div>
            <div class="form-group right">
                <label>Date</label>
                <input type="text" class="dateRangePicker" placeholder="Select date range">
            </div>
        </div> -->
        <div class="list-box">
            <table class="showTableList nowrap">
                <thead>
                    <tr>
                        <th>S No.</th>
                        <th>Title</th>
                        <th>Country</th>
                        <th>VISA</th>
                        <th>Entry</th>
                        <th>MRP</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($packageListData as $singlePackageData) {
                        switch ($singlePackageData['visa_package_status']) {
                            case 1:
                                $package_status = 'Active';
                                break;
                            case 3:
                                $package_status = 'Sold out';
                                break;
                            case 4:
                                $package_status = 'Return';
                                break;
                            case 2:
                                $package_status = 'Delete';
                                break;
                            case 5:
                                $package_status = 'Transfer request pending';
                                break;
                            default:
                                $package_status = 'Pending';
                                break;
                        }
                        echo "
                            <tr>
                                <td>{$i}</td>
                                <td>{$singlePackageData['visa_package_title']}</td>
                                <td>{$singlePackageData['visa_country_title']} ( {$singlePackageData['visa_country_code']} )</td>
                                <td>" . ucwords($singlePackageData['visa_package_visa_type']) . " VISA</td>
                                <td>{$singlePackageData['visa_package_entry_type']}</td>
                                <td>₹ {$singlePackageData['visa_package_mrp']}</td>
                                <td>₹ {$singlePackageData['visa_package_price']}</td>
                                <td>{$package_status}</td>
                                <td>
                                    <a href='//" . SITE_DOMAIN . "/package/?package_id=" . base64_encode($singlePackageData['visa_package_id']) . "&package_name=" . strtolower(str_replace(' ', '_', $singlePackageData['visa_package_title'])) . "' class='tableActionIconBtn' title='View country' data-title='View package' target='_blank'><i class='fi fi-rr-eye bgLightGreen'></i></a>
                                    <a href='/dashboard/visa_packages/update/?id=" . base64_encode($singlePackageData['visa_package_id']) . "' class='tableActionIconBtn' title='Edit package' data-title='Edit package' target='_blank'><i class='fi fi-rr-edit bgWhite'></i></a>
                                    <a href='#' data-type='visa_packages' data-id='{$singlePackageData['visa_package_id']}' title='Delete package' data-title='Delete package' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false' target='_blank'><i class='fi fi-rr-trash bgRed'></i></a>
                                </td>
                            </tr>
                        ";
                        $i++;
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>