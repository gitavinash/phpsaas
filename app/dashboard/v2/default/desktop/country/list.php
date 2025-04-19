<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$statusCondition = '';
if (isset($leadStatus)) {
    $statusCondition = "e.enquiry_status={$leadStatus} AND ";
}

if ($countryInfoList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($countryInfoList) != 0) {
        while($countryData=mysqli_fetch_assoc($countryInfoList)) {
            $countryListData[] = $countryData;
        }
    }
}

?>
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed here</span>
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
                    <?php
                    $i=1;
                    foreach ($countryListData as $country) {
                        $visaService=json_decode($country['visa_country_services'], true);
                        $visaService = implode(", ", $visaService);
                        echo "<tr>";
                        echo "<td>{$i}</td>";
                        echo "<td>{$country['visa_country_title']}</td>";
                        echo "<td>{$country['visa_country_code']}</td>";
                        echo "<td>".ucwords($visaService)."</td>";
                        echo "<td>" . ($country['visa_country_status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                        echo "<td>
                            <div class='tableActionButton'>
                                <a href='//".SITE_DOMAIN."/start-application/?country=".strtolower(str_replace(' ', '_', $country['visa_country_title']))."' class='tableActionIconBtn' title='View country' data-title='View country' target='_blank'><i class='fi fi-rr-eye bgLightGreen'></i></a>
                                <a href='/dashboard/country/update/?id=" . base64_encode($country['visa_country_id']) . "' class='tableActionIconBtn' title='Edit country' data-title='Edit country' target='_blank'><i class='fi fi-rr-edit bgWhite'></i></a>
                                <a href='#' data-type='country' data-id='{$country['visa_country_id']}' title='Delete country' data-title='Delete country' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false' target='_blank'><i class='fi fi-rr-trash bgRed'></i></a>
                            </div>
                        </td>";
                        echo"</tr>";
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>