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
    }

    img {
        max-height: 100px;
    }

    h1 {
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
        width: 96%;
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
                        switch ($block_item_dimension_type) {
                            case 'mm':
                                $block_item_dimension_type = 'Millimeter';
                                break;
                            case 'cm':
                                $block_item_dimension_type = 'Centimeter';
                                break;
                            case 'in':
                                $block_item_dimension_type = 'Inches';
                                break;
                            case 'mt':
                                $block_item_dimension_type = 'Meter';
                                break;
                            default:
                                $block_item_dimension_type = 'Feet';
                                break;
                        }
                        switch ($block_thickness_dimension) {
                            case 'mm':
                                $block_thickness_dimension = 'Millimeter';
                                break;
                            case 'cm':
                                $block_thickness_dimension = 'Centimeter';
                                break;
                            case 'in':
                                $block_thickness_dimension = 'Inches';
                                break;
                            case 'mt':
                                $block_thickness_dimension = 'Meter';
                                break;
                            default:
                                $block_thickness_dimension = 'Feet';
                                break;
                        }


                        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                        extract(json_decode($measurementData, TRUE));

                        switch ($measurementType) {
                            case 'sq. mm.':
                                $measurementType = 'Square Millimeter';
                                break;
                            case 'sq. cm.':
                                $measurementType = 'Square Centimeter';
                                break;
                            case 'sq. in.':
                                $measurementType = 'Square Inches';
                                break;
                            case 'sq. mt.':
                                $measurementType = 'Square Meter';
                                break;
                            default:
                                $measurementType = 'Square Feet';
                                break;
                        }
                        echo "<table>";
                        echo "<tr>
                        <td colspan=4><img src='" . SITE_LOGO . "'></td>
                        </tr>";
                        echo "<tr>
                        <td colspan=4><h1>{$block_title} " . (($block_code != '') ? " - {$block_code}" : '') . "</h1></td>
                        </tr>";
                        echo "<tr>
                        <th>S no.</th>
                        <th>Width ( {$block_item_dimension_type} )</th>
                        <th>Height ( {$block_item_dimension_type} )</th>
                        <th>Thickness ( {$block_thickness_dimension} )</th>
                    </tr>";
                    }
                    echo "
                    <tr>
                        <td>{$i}</td>
                        <td>{$block_item_width}</td>
                        <td>{$block_item_height}</td>
                        <td>{$block_thickness_size}</td>
                    </tr>
                ";
                    $i++;
                }

                echo "
                    <tr>
                        <td colspan='3' >Gross Size ( $measurementType )</td>
                        <td colspan='1'>{$block_gross_size}</td>
                    </tr>
                    <tr>
                        <td colspan='3' >Gross Size ( $measurementType )</td>
                        <td colspan='1'>{$block_net_size}</td>
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