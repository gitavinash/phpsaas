<h2 class="heading">Companies list<span>All Companies list will be displayed her</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/supplier/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Supplier Status</option>
                </select>
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Supplier type</th>
                        <th>City</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($supplierList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "supplier INNER JOIN " . TABLE_PREFIX . "address ON supplier_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE supplier_site_id=" . SITE_ID . " AND supplier_status=1 ORDER BY supplier_update DESC")) {
                    echo "<tbody>";
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($supplierData = mysqli_fetch_assoc($supplierList)) {
                            extract($supplierData);
                            echo "<tr>
                                <td>{$supplier_title}</td>
                                <td>{$address_area}, {$city_name} - {$address_pin_code}</td>
                                <td>" . ucwords(str_replace('_', ' ', $supplier_type)) . "</td>
                                <td>{$city_name}</td>
                                <td>
                                <a href='/dashboard/supplier/update/?id=" . base64_encode($supplier_id) . "' class='tableActionIconBtn'><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='#' data-type='supplier' data-id='{$supplier_id}' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
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