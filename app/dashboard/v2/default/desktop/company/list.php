<h2 class="heading">Properties list<span>All propertys list will be displayed her</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/vendors/property/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Property Status</option>
                </select>
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Property type</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($propertyList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($propertyList) > 0) {
                        while ($propertyData = mysqli_fetch_assoc($propertyList)) {
                            extract($propertyData);
                            echo "<tr>
                                <td>{$property_title}</td>
                                <td>{$property_email}</td>
                                <td>{$property_mobile}</td>
                                <td>{$property_address} {$property_city}  {$property_pincode}</td>
                                <td>" . ucwords(str_replace('_', ' ', $property_type)) . "</td>
                                <td>
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