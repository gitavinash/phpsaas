<h2 class="heading">Inventory list<span>All Inventory list will be displayed her</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/inventory/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Inventory Status</option>
                </select>
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Address</th>
                        <th>City</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($inventoryList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "inventory INNER JOIN " . TABLE_PREFIX . "address ON inventory_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "company ON inventory_company_id=company_id WHERE inventory_site_id=" . SITE_ID . " AND inventory_status=1 ORDER BY inventory_updated DESC")) {
                    echo "<tbody>";
                    if (mysqli_num_rows($inventoryList) > 0) {
                        while ($inventoryData = mysqli_fetch_assoc($inventoryList)) {
                            extract($inventoryData);
                            echo "<tr>
                                <td>{$inventory_title}</td>
                                <td>{$company_title}</td>
                                <td>{$address_area}, {$city_name} - {$address_pin_code}</td>
                                <td>{$city_name}</td>
                                <td>
                                <a href='/dashboard/inventory/update/?id=" . base64_encode($inventory_id) . "' class='tableActionIconBtn'><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='#' data-type='inventory' data-id='{$inventory_id}' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
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