<h2 class="heading">Warehouses list<span>All Warehouses list will be displayed her</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/warehouse/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Warehouse Status</option>
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

                if ($warehouseList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "company ON warehouse_company_id=company_id WHERE warehouse_site_id=" . SITE_ID . " ORDER BY warehouse_updated DESC")) {
                    echo "<tbody>";
                    if (mysqli_num_rows($warehouseList) > 0) {
                        while ($warehouseData = mysqli_fetch_assoc($warehouseList)) {
                            extract($warehouseData);
                            echo "<tr>
                                <td>{$warehouse_title}</td>
                                <td>{$company_title}</td>
                                <td>{$address_area}, {$city_name} - {$address_pin_code}</td>
                                <td>{$city_name}</td>
                                <td>
                                <a href='/dashboard/warehouse/update/?id=" . base64_encode($warehouse_id) . "' class='tableActionIconBtn'><i class='fi fi-rr-edit bgLightGreen'></i></a>
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