<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block inventory details | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin_v2.min.css">
    <style>
        body {
            padding: 0;
            margin: 0 auto;
        }

        .main-box {
            padding: 0;
            margin: 0;
        }

        .footer {
            width: 100%;
            text-align: center;
            font-size: 14px;
            line-height: 32px;
        }

        .footer::after {
            content: "Developed by Blumox Technologies";
        }

        @media print {

            /* Reset default margins and paddings */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Page setup for A4 size */
            @page {
                size: A4;
                margin: 12mm;
            }

            /* Layout adjustments */
            body {
                font-size: 8pt;
                color: #333;
                line-height: 1.2;
                margin: 0;
            }

            section {
                box-shadow: none !important;
                border: none !important;
            }

            .main-box {
                padding: 0;
                margin: 0 auto;
                max-width: 100%;
            }

            .header {
                text-align: center;
                font-size: 12pt;
                font-weight: bold;
                margin-bottom: 8px;
            }

            /* Section title styling for visibility */
            .section-title {
                font-weight: bold;
                font-size: 10pt;
                margin-bottom: 5px;
                border-bottom: 1px solid #333;
                padding-bottom: 2px;
            }

            /* Container styling with equal height for Customer and Salesman sections */
            .widget-container {
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }

            /* Section Styling */
            .widget {
                border: 1px solid #ddd;
                padding: 6px;
                margin-bottom: 8px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-height: 150px;
                /* Set minimum height for uniformity */
            }

            .userInfo p {
                margin: 4px 0;
                font-size: 8pt;
            }

            .userInfo strong {
                display: inline-block;
                width: 100px;
            }

            /* Table Styling */
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8pt;
                margin-top: 8px;
            }

            th,
            td {
                border: 1px solid #666;
                padding: 4px 6px;
                text-align: left;
                vertical-align: middle;
            }

            th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-transform: uppercase;
            }

            .summary-row td {
                font-weight: bold;
                background-color: #f7f7f7;
            }

            /* Footer Styling */
            .footer {
                font-size: 7pt;
                color: #888;
                border-top: 1px solid #ccc;
                padding-top: 5mm;
                text-align: center;
                position: fixed;
                bottom: 10mm;
                width: 100%;
            }

            /* Hide non-print elements */
            .btn-group,
            .no-print {
                display: none;
            }

            /* Avoid page breaks inside rows */
            tr,
            td,
            th {
                page-break-inside: avoid;
            }

            /* Ensure .leadActions content is visible */
            .leadActions {
                display: block !important;
                margin-bottom: 10px;
            }

            .leadActions a {
                font-size: 8pt;
                padding: 4px 8px;
                text-decoration: none;
                color: #333;
                border: 1px solid #666;
                border-radius: 4px;
                display: inline-block;
                margin-right: 5px;
            }

            /* Additional styling for icons if needed */
            .leadActions i {
                font-size: 8pt;
                margin-right: 4px;
            }

            /* Ensure other non-print elements are still hidden */
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php
    if (!isset($id)) { ?>
        <script>
            alert('Sales request id is missing');
            window.location.href = '/dashboard/sales_request/list/';
        </script>
        <?php
    } else {
        if ($id == '') { ?>
            <script>
                alert('Sales request id cannot be blank');
                window.location.href = '/dashboard/sales_request/list/';
            </script>
        <?php
        } else {
            $salesId = base64_decode($id);
        }
    }

    if ($checkSalesRequest = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=sales_agent_id INNER JOIN " . TABLE_PREFIX . "warehouse ON warehouse_id=sales_warehouse_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id WHERE sales_id='{$salesId}' AND sales_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkSalesRequest) == 0) { ?>
            <script>
                alert('Invalid Sales request id found');
                window.location.href = '/dashboard/sales_request/list/';
            </script>
    <?php
        } else {
            extract(mysqli_fetch_assoc($checkSalesRequest));
        }
    } else {
        echo mysqli_error($connection);
    }

    ?>
    <div class="box-1">

        <div class="main-box">
            <section>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="laadDetailsBtn" class="btn btn-green"><i class="fi-rr-review"></i>
                            Customer details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $sales_name ?></p>
                        <p><strong>Mobile</strong><?php echo $sales_mobile ?></p>
                        <p><strong>Email</strong><?php echo $sales_email ?></p>
                        <p><strong style="width: 100px; height: 50px">Address</strong><?php echo $sales_address ?>
                        </p>
                    </div>
                </div>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-id-card-clip-alt"></i>
                            Salesman details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $profile_display_name ?></p>
                        <p><strong>Mobile</strong><?php echo $profile_mobile ?></p>
                        <p><strong>GST Number</strong><?php echo $warehouse_gst ?></p>
                        <p><strong style="width: 100px; height: 50px">Warehouse
                            </strong><?php echo ucwords($warehouse_title) . ', ' . $address_area . ', ' . $city_name . ', ' . $state_name ?>
                        </p>
                    </div>
                </div>
                <div class="widget no-padding box-1">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-to-do"></i>
                            Inventory details</a>
                    </div>

                    <div class="full-width">
                        <table class="stockInfo">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="sno-column">S No.</th>
                                    <th rowspan="2">Block Name</th>
                                    <th class="text-center" colspan="100">Slabs Details</th>
                                </tr>
                                <tr>
                                    <th>Slab ID</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Gross Qty.</th>
                                    <th>Damage Qty.</th>
                                    <th>Net Qty.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $blockData = json_decode($sales_data, TRUE);
                                $i = 1;
                                $totalBlockSlabs = 0;
                                $totalBlockGrossSize = 0;
                                $totalBlockDamageSize = 0;
                                $totalBlockNetSize = 0;
                                $totalBlockDimensionType = '';

                                foreach ($blockData as $key => $value) {
                                    $keyArray = explode('_', str_replace('salesBlocks_', '', $key));
                                    $productName = $keyArray[1];

                                    // Exploding string and processing data
                                    $objects = explode(",", $value);
                                    $valuesCount = [];
                                    $slabDetails = [];

                                    $j = 1;

                                    $sizeOfSlabDimension = sizeof($valuesCount);

                                    $totalSlabs = 0;
                                    $totalSlabsGrossSize = 0;
                                    $totalSlabsDamageSize = 0;
                                    $totalSlabsNetSize = 0;

                                    foreach ($objects as $slabDimension => $details) {

                                        if (isset($objects)) {
                                            $newObject = explode('_', $details);
                                            $number = $newObject[0] ?? null;
                                            $slabDimension = $newObject[1] ?? null;
                                            $grossSize = $newObject[2] ?? 0;
                                            $damageSize = $newObject[3] ?? 0;
                                            $netSize = sprintf("%.2f", $newObject[4] ?? 0);
                                            $dimensionsType = $newObject[5] ?? null;
                                            $slabNumber = $newObject[6] ?? null;


                                            echo "<tr " . (($j == 1) ? "class='starting'" : '') . ">";
                                            echo "<td class='sno-column'>" . (($j == 1) ? $i : '') . "</td>";
                                            echo "<td>" . (($j == 1) ? $productName : '') . "</td>";
                                            echo "<td>{$slabNumber}</td>";
                                            echo "<td>{$slabDimension}</td>";
                                            echo "<td>1 Pc.</td>";
                                            echo "<td>{$damageSize}</td>";
                                            echo "<td>{$damageSize}</td>";
                                            echo "<td>{$netSize} {$dimensionsType}</td>";
                                            echo "</tr>";

                                            $totalSlabs += 1;
                                            $totalSlabsGrossSize += $grossSize;
                                            $totalSlabsDamageSize += $damageSize;
                                            $totalSlabsNetSize += $netSize;

                                            // Each block total

                                            if ($j == count($objects)) {
                                                echo "<tr class='ending'>";
                                                echo "<td class='sno-column' colspan=4><strong>Slab total</strong></td>";
                                                echo "<td>{$totalSlabs} " . (($totalSlabs == 1) ? 'Pc.' : 'Pcs.') . "</td>";
                                                echo "<td>{$totalSlabsGrossSize} {$dimensionsType}</td>";
                                                echo "<td>{$totalSlabsDamageSize} {$dimensionsType}</td>";
                                                echo "<td>{$totalSlabsNetSize} {$dimensionsType}</td>";
                                                echo "</tr><tr><td colspan=100></td></tr>";
                                                $totalBlockSlabs += $totalSlabs;
                                                $totalBlockGrossSize += $totalSlabsGrossSize;
                                                $totalBlockDamageSize += $totalSlabsDamageSize;
                                                $totalBlockNetSize += $totalSlabsNetSize;
                                                $totalBlockDimensionType = $dimensionsType;
                                            }

                                            $j++;
                                        }
                                    }


                                    // Total 

                                    if ($i == sizeof($blockData)) {
                                        echo "<tr><td colspan=100></td></tr><tr>";
                                        echo "<td colspan=4><strong>Total</strong></td>";
                                        echo "<td>{$totalBlockSlabs} " . (($totalBlockSlabs == 1) ? 'Pc.' : 'Pcs.') . "</td>";
                                        echo "<td>{$totalBlockGrossSize} {$totalBlockDimensionType}</td>";
                                        echo "<td>{$totalBlockDamageSize} {$totalBlockDimensionType}</td>";
                                        echo "<td>{$totalBlockNetSize} {$totalBlockDimensionType}</td>";
                                        echo "</tr>";
                                    }
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <div class="footer"></div>
        </div>

    </div>
</body>

</html>
<?php
exit();
?>