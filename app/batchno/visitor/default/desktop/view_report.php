<?php

require_once LOGIN_CHECK;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Block slab measurment report | <?php echo SITE_NAME ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <!-- <script>
    window.addEventListener('load', function() {
        window.print();
    });
    </script> -->
    <style>
        @page {
            size: landscape;
            margin: 1in;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 0.8rem;
        }

        img {
            max-height: 100px;
        }

        h1 {
            font-size: 1.5rem !important;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            float: left;
        }

        th,
        td {
            border: 1px solid #000;
            text-align: center;
            padding: 8px;
            width: auto;
            /* Each column will take up equal space */
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        a {
            background-color: #4CAF50;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            float: left;
            width: 100%;
            text-align: center;
            padding: 1rem 1rem;
            box-sizing: border-box;
            border-radius: 7px;
            border: none;
            font-size: 1rem;
            color: #fff;
            letter-spacing: 1px;
        }

        @media print {
            body {
                margin: 0;
            }

            .content {
                page-break-after: always;
            }

            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 12px;
            }

            .footer::after {
                content: "Developed by Blumox Technologies";
            }
        }
    </style>
</head>

<body>

    <?php

    $tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

    if (isset($id)) {
        $blockId = base64_decode($id);
        if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status IN (1,3) AND block_item_site_id=" . SITE_ID . " ORDER BY block_item_id ASC")) {

            if (mysqli_num_rows($checkBlock) > 0) {
                $i = 0;
                $slabDamageCount = array();

                // ✅ Helper: unit conversion
                function convertArea($width, $height, $measurementType, $calculationType)
                {
                    $area = $width * $height;
                    switch ([$measurementType, $calculationType]) {
                        // Centimeter
                        case ['cm', 'sq. mm.']:
                            $area *= 100;
                            break;
                        case ['cm', 'sq. in.']:
                            $area *= 0.15500031;
                            break;
                        case ['cm', 'sq. ft.']:
                            $area *= 0.001076391;
                            break;
                        case ['cm', 'sq. mt.']:
                            $area *= 0.0001;
                            break;

                        // Millimeter
                        case ['mm', 'sq. cm.']:
                            $area *= 0.01;
                            break;
                        case ['mm', 'sq. in.']:
                            $area *= 0.0015500031;
                            break;
                        case ['mm', 'sq. ft.']:
                            $area *= 0.00001076391;
                            break;
                        case ['mm', 'sq. mt.']:
                            $area *= 0.000001;
                            break;

                        // Inches
                        case ['in', 'sq. cm.']:
                            $area *= 6.4516;
                            break;
                        case ['in', 'sq. mm.']:
                            $area *= 645.16;
                            break;
                        case ['in', 'sq. ft.']:
                            $area *= 0.00694444;
                            break;
                        case ['in', 'sq. mt.']:
                            $area *= 0.00064516;
                            break;

                        // Feet
                        case ['ft', 'sq. cm.']:
                            $area *= 929.0304;
                            break;
                        case ['ft', 'sq. mm.']:
                            $area *= 92903.04;
                            break;
                        case ['ft', 'sq. in.']:
                            $area *= 144;
                            break;
                        case ['ft', 'sq. mt.']:
                            $area *= 0.09290304;
                            break;

                        // Meter
                        case ['mt', 'sq. cm.']:
                            $area *= 10000;
                            break;
                        case ['mt', 'sq. mm.']:
                            $area *= 1000000;
                            break;
                        case ['mt', 'sq. in.']:
                            $area *= 1550.0031;
                            break;
                        case ['mt', 'sq. ft.']:
                            $area *= 10.7639;
                            break;
                    }
                    return (float)$area;
                }

                while ($slabSize = mysqli_fetch_assoc($checkBlock)) {
                    extract($slabSize);

                    if ($i == 0) {
                        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                        extract(json_decode($measurementData, TRUE));

                        echo "<table>";
                        echo "<tr><td colspan=100><img src='" . SITE_LOGO . "'></td></tr>";
                        echo "<tr><td colspan=100><h1>{$block_title} " . (($block_code != '') ? " - {$block_code}" : '') . "</h1></td></tr>";
                        echo "<tr>
                <th rowspan=2>Slab no.</th>
                <th rowspan=2>Width<br />({$block_item_dimension_type})</th>
                <th rowspan=2>Height<br />({$block_item_dimension_type})</th>
                <th rowspan=2>Net qty.<br/>({$calculationType})</th>
                <th colspan=100>Damage Area</th>
            </tr>
            <tr>";

                        // 6 damage slots
                        for ($k = 0; $k < 6; $k++) {
                            echo "<th>Width<br />({$block_item_dimension_type})</th>
                      <th>Height<br />({$block_item_dimension_type})</th>
                      <th>Net qty.<br/>({$calculationType})</th>";
                        }
                        echo "</tr>";
                    }

                    $totalArea = convertArea($block_item_width, $block_item_height, $measurementType, $calculationType);

                    if ($block_item_status == 1) {
                        echo "<tr>
                <td>" . ($i + 1) . "</td>
                <td>{$block_item_width}</td>
                <td>{$block_item_height}</td>
                <td><strong>" . number_format($totalArea, 2, '.', '') . "</strong></td>";

                        $damageItems = !empty($block_item_damage_area) ? json_decode($block_item_damage_area, TRUE) : [];
                        $slabTotalDamage = 0;

                        for ($j = 0; $j < 6; $j++) {
                            if (isset($damageItems[$j])) {
                                $damage = $damageItems[$j];
                                $damageItemTotal = convertArea($damage['damageAreaWidth'], $damage['damageAreaHeight'], $measurementType, $calculationType);

                                echo "<td>{$damage['damageAreaWidth']}</td>
                          <td>{$damage['damageAreaHeight']}</td>
                          <td><strong>" . number_format($damageItemTotal, 2, '.', '') . "</strong></td>";

                                $slabDamageCount[$i][$j] = $damageItemTotal;
                            } else {
                                echo "<td>0</td><td>0</td><td>0</td>";
                                $slabDamageCount[$i][$j] = 0;
                            }
                        }
                        echo "</tr>";
                    }
                    $i++;
                }

                // Sum all damage columns
                $numCols = ((isset($slabDamageCount[0])) ? count($slabDamageCount[0]) : 0);
                $damageSumArray = array_fill(0, $numCols, 0);
                foreach ($slabDamageCount as $row) {
                    foreach ($row as $index => $value) {
                        $damageSumArray[$index] += $value;
                    }
                }

                echo "<tr>
        <td colspan='3' style='text-align:right'><strong>Gross Qty. ( $calculationType )</strong></td>
        <td style='text-align:right'><strong>" . number_format($block_gross_size, 2, '.', '') . "</strong></td>";

                for ($j = 0; $j < 6; $j++) {
                    echo "<td></td><td></td><td><strong>" . number_format($damageSumArray[$j] ?? 0, 2, '.', '') . "</strong></td>";
                }
                echo "</tr>
    <tr>
        <td colspan='3' style='text-align:right'><strong>Damage Qty. ( $calculationType )</strong></td>
        <td style='text-align:right'><strong>" . number_format($block_damage_size, 2, '.', '') . "</strong></td>
        <td colspan=100 ></td>
    </tr>
    <tr>
        <td colspan='3' style='text-align:right'><strong>Net Qty. ( $calculationType )</strong></td>
        <td style='text-align:right'><strong>" . number_format(($block_gross_size - $block_damage_size), 2, '.', '') . "</strong></td>
        <td colspan=100 ></td>
    </tr>";

                if ($i == 1) {
                    echo "</table>";
                }
            }
        }
    } else {
        echo "Missing required details";
    }
    ?>
    <script>
        document.getElementById('download').addEventListener('click', function() {
            const element = document.getElementById('content');
            html2pdf().from(element).save();
        });
    </script>
</body>

</html>
<?php
exit();
?>