<h2 class="heading">Transporters list<span>All transporters list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/vendors/transporter/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Transporter Status</option>
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
                        <th>Service State's</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($transporterList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "transporter WHERE transporter_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($transporterList) > 0) {
                        while ($transporterData = mysqli_fetch_assoc($transporterList)) {
                            extract($transporterData);
                            echo "<tr>
                                <td>{$transporter_title}</td>
                                <td>{$transporter_email}</td>
                                <td>{$transporter_mobile}</td>
                                <td>{$transporter_address} {$transporter_city}  {$transporter_pincode}</td>
                                <td>{$transporter_service_state}</td>
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