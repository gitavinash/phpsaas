<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($listStatus)) {
    $statusCondition = "product_status={$listStatus} AND ";
} else {
    $statusCondition = "product_status=1 AND ";
}

if ($countryInfoList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "product WHERE {$statusCondition} product_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($countryInfoList) != 0) {
        while ($countryData = mysqli_fetch_assoc($countryInfoList)) {
            $countryListData[] = $countryData;
        }
    }
}

?>
<h2 class="heading"><?php echo ucwords(str_replace('_', ' ', $urlStructure[1])) ?> List<span>All packages list will be
        displayed here</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/country/add/"><i class="fi fi-rr-plus"></i></a>
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
                        <th>Country name</th>
                        <th>Country code</th>
                        <th>Visa services</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </section>
</div>