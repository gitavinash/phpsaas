<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block slab measurment</title>
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
    }

    th,
    td {
        border: 1px solid #000;
        text-align: center;
        padding: 8px;
        width: 20%;
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
                        echo "<table>";
                        echo "<tr>
                        <td colspan=4><img src='" . SITE_LOGO . "'></td>
                        </tr>";
                        echo "<tr>
                        <td colspan=4><h1>{$block_title} " . (($block_code != '') ? " - {$block_code}" : '') . "</h1></td>
                        </tr>";
                        echo "<tr>
                        <th>S no.</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Thickness</th>
                    </tr>";
                    }
                    switch ($block_item_dimenstion_type) {
                        case 'mm':
                            $block_item_dimenstion_type = 'Millimeter';
                            break;
                        case 'cm':
                            $block_item_dimenstion_type = 'Centimeter';
                            break;
                        case 'in':
                            $block_item_dimenstion_type = 'Inches';
                            break;
                        case 'mt':
                            $block_item_dimenstion_type = 'Meter';
                            break;
                        default:
                            $block_item_dimenstion_type = 'Feet';
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
                    echo "
                    <tr>
                    <td>{$i}</td>
                    <td>{$block_item_width} {$block_item_dimenstion_type}</td>
                    <td>{$block_item_height} {$block_item_dimenstion_type}</td>
                    <td>{$block_thickness_size} {$block_thickness_dimension}</td>
                    </tr>
                ";
                    $i++;
                }
                if ($i == 1) {
                    echo "</table>";
                }
            }
        }
    } else {
        echo "Missing required details";
    }
    ?>
</body>

</html>