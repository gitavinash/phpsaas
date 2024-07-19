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
        /* Green background */
        border: none;
        /* Remove borders */
        color: white;
        /* White text */
        padding: 15px 32px;
        /* Some padding */
        text-align: center;
        /* Center the text */
        text-decoration: none;
        /* Remove underline */
        display: inline-block;
        /* Get it to line up */
        font-size: 16px;
        /* Increase font size */
        margin: 4px 2px;
        /* Add some margin */
        cursor: pointer;
        /* Add a pointer cursor on hover */
        border-radius: 12px;
        /* Rounded corners */
        transition: background-color 0.3s, transform 0.3s;
        width: 90%;
        box-sizing: border-box;
        position: fixed;
        bottom: 1rem;
    }
    </style>
</head>

<body>

    <?php

    $tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

    if (isset($id)) {
        $blockId = base64_decode($id);
        if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkBlock) > 0) {
                $i = 1;
                while ($slabSize = mysqli_fetch_assoc($checkBlock)) {
                    extract($slabSize);
                    if ($i == 1) {

                        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                        extract(json_decode($measurementData, TRUE));

                        echo "<table>";
                        echo "<tr>
                        <td colspan=5><img src='" . SITE_LOGO . "'></td>
                        </tr>";
                        echo "<tr>
                        <td colspan=5><h1>{$block_title} " . (($block_code != '') ? " - {$block_code}" : '') . "</h1></td>
                        </tr>";
                        echo "<tr>
                        <th>S no.</th>
                        <th>Width<br />({$block_item_dimension_type})</th>
                        <th>Height<br />({$block_item_dimension_type})</th>
                        <th>Thickness<br />({$block_thickness_dimension})</th>
                        <th>Net size<br/>({$calculationType})</th>
                    </tr>";
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
                    </tr>
                ";
                    $i++;
                }

                echo "
                    <tr>
                        <td colspan='3' ><strong>Block Gross Size ( $calculationType )</strong></td>
                        <td colspan='2'>{$block_gross_size}</td>
                    </tr>
                    <tr>
                        <td colspan='3' ><strong>Block Net Size ( $calculationType )</strong></td>
                        <td colspan='2'>{$block_net_size}</td>
                    </tr>
                    ";
                if ($i == 1) {
                    echo "</table>";
                }
                echo "<a id='download'>Download measurements as PDF</a>";
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