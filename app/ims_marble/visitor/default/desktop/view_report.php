<?php

require_once LOGIN_CHECK;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block slab measurment</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
    window.addEventListener('load', function() {
        window.print();
    });
    </script>
    <style>
    @page {
        size: landscape;
        margin: 1in;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 20px;
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
        if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID . " ORDER BY block_item_id ASC")) {
            if (mysqli_num_rows($checkBlock) > 0) {
                $i = 1;
                while ($slabSize = mysqli_fetch_assoc($checkBlock)) {
                    extract($slabSize);
                    if ($i == 1) {

                        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                        extract(json_decode($measurementData, TRUE));
                        $slabDamageCount[] = $block_item_damage_size;
                        echo "<table>";
                        echo "<tr>
                        <td colspan=100><img src='" . SITE_LOGO . "'></td>
                        </tr>";
                        echo "<tr>
                        <td colspan=100><h1>{$block_title} " . (($block_code != '') ? " - {$block_code}" : '') . "</h1></td>
                        </tr>";
                        echo "
                        <tr>
                            <th rowspan=2>S no.</th>
                            <th rowspan=2>Width<br />({$block_item_dimension_type})</th>
                            <th rowspan=2>Height<br />({$block_item_dimension_type})</th>
                            <th rowspan=2>Net qty.<br/>({$calculationType})</th>
                            <th colspan=100>Damage Area</th>
                        </tr>
                        <tr>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th>Net qty.<br/>({$calculationType})</th>
                        </tr>
                    ";
                    }

                    $totalArea = $block_item_width * $block_item_height;

                    switch ([$measurementType, $calculationType]) {

                            // Centimeter

                        case ['cm', 'sq. mm.']:
                            $totalArea = $totalArea * 100;
                            break;
                        case ['cm', 'sq. in.']:
                            $totalArea = $totalArea * 0.15500031;
                            break;
                        case ['cm', 'sq. ft.']:
                            $totalArea = $totalArea * 0.001076391;
                            break;
                        case ['cm', 'sq. mt.']:
                            $totalArea = $totalArea * 0.0001;
                            break;

                            // Millimeter

                        case ['mm', 'sq. cm.']:
                            $totalArea = $totalArea * 0.01;
                            break;
                        case ['mm', 'sq. in.']:
                            $totalArea = $totalArea * 0.0015500031;
                            break;
                        case ['mm', 'sq. ft.']:
                            $totalArea = $totalArea * 0.00001076391;
                            break;
                        case ['mm', 'sq. mt.']:
                            $totalArea = $totalArea * 0.000001;
                            break;

                            // Inches

                        case ['in', 'sq. cm.']:
                            $totalArea = $totalArea * 6.4516;
                            break;
                        case ['in', 'sq. mm.']:
                            $totalArea = $totalArea * 645.16;
                            break;
                        case ['in', 'sq. ft.']:
                            $totalArea = $totalArea * 0.00694444;
                            break;
                        case ['in', 'sq. mt.']:
                            $totalArea = $totalArea * 0.00064516;
                            break;

                            // Feet

                        case ['ft', 'sq. cm.']:
                            $totalArea = $totalArea * 929.0304;
                            break;
                        case ['ft', 'sq. mm.']:
                            $totalArea = $totalArea * 92903.04;
                            break;
                        case ['ft', 'sq. in.']:
                            $totalArea = $totalArea * 144;
                            break;
                        case ['ft', 'sq. mt.']:
                            $totalArea = $totalArea * 0.09290304;
                            break;

                            // Meter

                        case ['mt', 'sq. cm.']:
                            $totalArea = $totalArea * 10000;
                            break;
                        case ['mt', 'sq. mm.']:
                            $totalArea = $totalArea * 1000000;
                            break;
                        case ['mt', 'sq. in.']:
                            $totalArea = $totalArea * 1550.0031;
                            break;
                        case ['mt', 'sq. ft.']:
                            $totalArea = $totalArea * 10.7639;
                            break;
                    }

                    $totalArea = number_format((float)$totalArea, 2, '.', '');

                    echo "
                    <tr>
                        <td>{$i}</td>
                        <td>{$block_item_width}</td>
                        <td>{$block_item_height}</td>
                        <td><strong>{$totalArea}</strong></td>
                    ";

                    if ($block_item_damage_area != '') {
                        $damageItems = json_decode($block_item_damage_area, TRUE);
                        for ($j = 0; $j <= 5; $j++) {
                            if (isset($damageItems[$j])) {
                                extract($damageItems[$j]);

                                $damageItemTotal = $damageAreaWidth * $damageAreaHeight;

                                switch ([$measurementType, $calculationType]) {

                                        // Centimeter

                                    case ['cm', 'sq. mm.']:
                                        $damageItemTotal = $damageItemTotal * 100;
                                        break;
                                    case ['cm', 'sq. in.']:
                                        $damageItemTotal = $damageItemTotal * 0.15500031;
                                        break;
                                    case ['cm', 'sq. ft.']:
                                        $damageItemTotal = $damageItemTotal * 0.001076391;
                                        break;
                                    case ['cm', 'sq. mt.']:
                                        $damageItemTotal = $damageItemTotal * 0.0001;
                                        break;

                                        // Millimeter

                                    case ['mm', 'sq. cm.']:
                                        $damageItemTotal = $damageItemTotal * 0.01;
                                        break;
                                    case ['mm', 'sq. in.']:
                                        $damageItemTotal = $damageItemTotal * 0.0015500031;
                                        break;
                                    case ['mm', 'sq. ft.']:
                                        $damageItemTotal = $damageItemTotal * 0.00001076391;
                                        break;
                                    case ['mm', 'sq. mt.']:
                                        $damageItemTotal = $damageItemTotal * 0.000001;
                                        break;

                                        // Inches

                                    case ['in', 'sq. cm.']:
                                        $damageItemTotal = $damageItemTotal * 6.4516;
                                        break;
                                    case ['in', 'sq. mm.']:
                                        $damageItemTotal = $damageItemTotal * 645.16;
                                        break;
                                    case ['in', 'sq. ft.']:
                                        $damageItemTotal = $damageItemTotal * 0.00694444;
                                        break;
                                    case ['in', 'sq. mt.']:
                                        $damageItemTotal = $damageItemTotal * 0.00064516;
                                        break;

                                        // Feet

                                    case ['ft', 'sq. cm.']:
                                        $damageItemTotal = $damageItemTotal * 929.0304;
                                        break;
                                    case ['ft', 'sq. mm.']:
                                        $damageItemTotal = $damageItemTotal * 92903.04;
                                        break;
                                    case ['ft', 'sq. in.']:
                                        $damageItemTotal = $damageItemTotal * 144;
                                        break;
                                    case ['ft', 'sq. mt.']:
                                        $damageItemTotal = $damageItemTotal * 0.09290304;
                                        break;

                                        // Meter

                                    case ['mt', 'sq. cm.']:
                                        $damageItemTotal = $damageItemTotal * 10000;
                                        break;
                                    case ['mt', 'sq. mm.']:
                                        $damageItemTotal = $damageItemTotal * 1000000;
                                        break;
                                    case ['mt', 'sq. in.']:
                                        $damageItemTotal = $damageItemTotal * 1550.0031;
                                        break;
                                    case ['mt', 'sq. ft.']:
                                        $damageItemTotal = $damageItemTotal * 10.7639;
                                        break;
                                }

                                $damageItemTotal = number_format((float)$damageItemTotal, 2, '.', '');
                                echo "<td>{$damageAreaWidth}</td>";
                                echo "<td>{$damageAreaHeight}</td>";
                                echo "<td><strong>{$damageItemTotal}</strong></td>";
                            } else {
                                echo "<td>0</td>";
                                echo "<td>0</td>";
                                echo "<td><strong>0</strong></td>";
                            }
                        }
                    } else {
                        echo "
                            <td>0</td><td></td><td><strong>0</strong></td>
                            <td>0</td><td></td><td><strong>0</strong></td>
                            <td>0</td><td></td><td><strong>0</strong></td>
                            <td>0</td><td></td><td><strong>0</strong></td>
                            <td>0</td><td></td><td><strong>0</strong></td>
                            <td>0</td><td></td><td><strong>0</strong></td>
                            ";
                    }
                    echo "</tr>";
                    $i++;
                }

                echo "
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Gross Qty. ( $calculationType )</strong></td>
                        <td colspan='1' style='text-align:right' ><strong>{$block_gross_size}</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[0])) ? $slabDamageCount[0] : 0) . "</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[1])) ? $slabDamageCount[1] : 0) . "</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[2])) ? $slabDamageCount[2] : 0) . "</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[3])) ? $slabDamageCount[3] : 0) . "</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[4])) ? $slabDamageCount[4] : 0) . "</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>" . ((isset($slabDamageCount[5])) ? $slabDamageCount[5] : 0) . "</strong></td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Damage Qty. ( $calculationType )</strong></td>
                        <td colspan='1' style='text-align:right' ><strong>{$block_damage_size}</strong></td>
                        <td colspan=100 ></td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Net Qty. ( $calculationType )</strong></td>
                        <td colspan='1' style='text-align:right' ><strong>{$block_net_size}</strong></td>
                        <td colspan=100 ></td>
                    </tr>
                    ";

                if ($i == 1) {
                    echo "
                    </table>
                    ";
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