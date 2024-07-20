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
    <style>
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
                            <th rowspan=2>Thickness<br />({$block_thickness_dimension})</th>
                            <th rowspan=2>Net size<br/>({$calculationType})</th>
                            <th colspan=100>Damage Area</th>
                        </tr>
                        <tr>
                            <th>Width<br />({$block_item_dimension_type})</th>
                            <th>Height<br />({$block_item_dimension_type})</th>
                            <th colspan=100>Net size<br/>({$calculationType})</th>
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
                        <td>{$block_thickness_size}</td>
                        <td>{$totalArea}</td>
                    ";

                    if ($block_item_damage_area != '') {
                        $damageItems = json_decode($block_item_damage_area, TRUE);
                        for ($j = 0; $j < sizeof($damageItems); $j++) {
                            extract($damageItems[0]);
                            echo "<td>{$damageAreaWidth}</td>";
                            echo "<td>{$damageAreaHeight}</td>";
                        }
                    }
                    echo "</tr>";
                    $i++;
                }

                echo "
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Block Gross Size ( $calculationType )</strong></td>
                        <td colspan='2' style='text-align:left' >{$block_gross_size}</td>
                        <td colspan=100 ></td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Block Damage Size ( $calculationType )</strong></td>
                        <td colspan='2' style='text-align:left' >{$block_damage_size}</td>
                        <td colspan=100 ></td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right' ><strong>Block Net Size ( $calculationType )</strong></td>
                        <td colspan='2' style='text-align:left' >{$block_net_size}</td>
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