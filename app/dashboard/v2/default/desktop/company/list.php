<h2 class="heading">Companies list<span>All Companies list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/company/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Company Status</option>
                </select>
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Company type</th>
                        <th>City</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($companyList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company LEFT JOIN " . TABLE_PREFIX . "address ON company_address_id=address_id LEFT JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE company_site_id=" . SITE_ID . " AND company_status=1 ORDER BY company_update DESC")) {
                    echo "<tbody>";
                    if (mysqli_num_rows($companyList) > 0) {
                        while ($companyData = mysqli_fetch_assoc($companyList)) {
                            extract($companyData);
                            $addressDisplayParts = array_filter([(!empty($address_area) ? $address_area : ''), (!empty($city_name) ? $city_name : '')]);
                            $addressDisplay = implode(', ', $addressDisplayParts) . (!empty($address_pin_code) ? ' - ' . $address_pin_code : '');
                            $addressDisplay = !empty($addressDisplay) ? $addressDisplay : 'N/A';
                            $cityDisplay = !empty($city_name) ? $city_name : 'N/A';
                            $companyTypeDisplay = !empty($company_type) ? ucwords(str_replace('_', ' ', $company_type)) : 'N/A';
                            echo "<tr>
                                <td>{$company_title}</td>
                                <td>{$addressDisplay}</td>
                                <td>{$companyTypeDisplay}</td>
                                <td>{$cityDisplay}</td>
                                <td>
                                <a href='/dashboard/company/update/?id=" . base64_encode($company_id) . "' data-title='Edit company' class='tableActionIconBtn'><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='#' data-type='company' data-id='{$company_id}' data-title='Delete company' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
                                </td>
                            </tr>";
                        }
                    }
                    echo "</tbody>";
                }

                ?>
            </table>
        </div>
    </section>
</div>