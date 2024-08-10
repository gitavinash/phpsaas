<h2 class="heading">Companies list<span>All Companies list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/measurement/add/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value="">Measurement Status</option>
                </select>
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Measurement</th>
                        <th>Calculation</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($measurementList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%measurement_%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($measurementList) > 0) {
                        while ($measurementData = mysqli_fetch_assoc($measurementList)) {
                            extract($measurementData);
                            $measurementValues = json_decode($site_configuration_value, TRUE);
                            extract($measurementValues);

                            switch ($measurementType) {
                                case 'mm':
                                    $measurementType = 'Millimeter';
                                    break;
                                case 'cm':
                                    $measurementType = 'Centimeter';
                                    break;
                                case 'in':
                                    $measurementType = 'Inches';
                                    break;
                                case 'mt':
                                    $measurementType = 'Meter';
                                    break;
                                default:
                                    $measurementType = 'Feet';
                                    break;
                            }

                            switch ($calculationType) {
                                case 'sq. mm.':
                                    $calculationType = 'Square Millimeter';
                                    break;
                                case 'sq. cm.':
                                    $calculationType = 'Square Centimeter';
                                    break;
                                case 'sq. in.':
                                    $calculationType = 'Square Inches';
                                    break;
                                case 'sq. mt.':
                                    $calculationType = 'Square Meter';
                                    break;
                                default:
                                    $calculationType = 'Square Feet';
                                    break;
                            }

                            echo "<tr>
                                <td>" . ucwords(str_replace('measurement_', '', $site_configuration_key)) . "</td>
                                <td>{$description}</td>
                                <td>{$measurementType} ( $measurementValues[measurementType] )</td>
                                <td>{$calculationType} ( $measurementValues[calculationType] )</td>
                                <td>
                                <a href='/dashboard/measurement/update/?id=" . base64_encode($site_configuration_id) . "' class='tableActionIconBtn'><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='#' data-type='measurement' data-id='{$site_configuration_id}' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
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