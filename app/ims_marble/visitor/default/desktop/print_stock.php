<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME . ' stocks list' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Lato", serif;
            font-style: normal;
            padding: 0;
            margin: 0px;
        }

        img {
            float: left;
            max-height: 100px;
            text-align: center;
        }

        .showCompanyDetails {
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            /* Removes the border of the table */
        }

        .showCompanyDetails table {
            border-collapse: collapse;
            /* Ensures borders do not double up */
            border: none;
            /* Removes table border */
        }

        .showCompanyDetails th,
        .showCompanyDetails td {
            border: none;
            /* Removes cell borders */
            padding: 10px;
            /* Optional: Adjusts spacing inside cells */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        thead {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tfoot {
            display: table-row-group;
            /* Prevent repeating on each page */
            background-color: #e6e6e6;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ddd;
        }

        .no-border {
            border: none !important;
        }

        .no-border td {
            border: none !important;
        }

        @media print {
            body {
                font-family: "Lato", serif;
                font-style: normal;
                padding: 0;
                margin: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                font-size: 12px;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }

            thead {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            tfoot {
                display: table-row-group;
                /* Prevent repeating on each page */
                background-color: #e6e6e6;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            tr:hover {
                background-color: #ddd;
            }

            .no-border {
                border: none !important;
            }

            .no-border td {
                border: none !important;
            }
        }
    </style>
</head>

<body>
    <table class="showCompanyDetails display nowrap" style="width: 100%;">
        <tbody>
            <tr>
                <td>
                    <h1><?php echo SITE_NAME ?></h1>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
    $measurementByWeight = false;
    $tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
    $block_size_dimension = 'sq. ft.';
    function getFilterCondition($param, $column, $default = '')
    {
        if (isset($_GET[$param]) && $_GET[$param] !== '' && $_GET[$param] !== '#') {
            $value = htmlspecialchars($_GET[$param], ENT_QUOTES, 'UTF-8'); // Sanitize input
            return ["AND {$column}='{$value}'", $value];
        }
        return [$default, ''];
    }

    // Get warehouse filter
    list($warehouseFilter, $warehouseFilterValue) = getFilterCondition('warehouse', 'block_warehouse_id');

    // Get product type filter
    list($typeFilter, $typeFilterValue) = getFilterCondition('product_type', 'block_product_type');

    // Get status filter (defaulting to `AND block_status!=2`)
    list($statusFilter, $statusFilterValue) = getFilterCondition('status', 'block_status', 'AND block_status!=2');

    if (DEVELOPMENT_MODE == true) {
        $query = "
    SELECT 
        w.warehouse_title, 
        b.block_size_dimension,
        CONCAT('[', 
            GROUP_CONCAT(
                REPLACE(REPLACE(
                    JSON_OBJECT(
                        'block_code', IFNULL(b.block_code, ''), 
                        'block_title', IFNULL(b.block_title, ''), 
                        'net_weight', FORMAT(ROUND(IFNULL(b.block_net_size, 0), 2), 2), 
                        'block_thickness', IFNULL(CONCAT(b.block_thickness_size, ' ', b.block_thickness_dimension), '')
                    ), '\\\\', ''), '\"', '\"'
                ) 
                ORDER BY b.block_code ASC
                SEPARATOR ','
            ), 
        ']') AS block_details
    FROM {$tableNamePrefix}block AS b
    INNER JOIN " . TABLE_PREFIX . "warehouse AS w ON b.block_warehouse_id = w.warehouse_id 
    LEFT JOIN " . TABLE_PREFIX . "supplier AS s ON b.block_supplier_id = s.supplier_id 
    INNER JOIN " . TABLE_PREFIX . "address AS a ON w.warehouse_address_id = a.address_id 
    INNER JOIN " . TABLE_PREFIX . "state AS st ON a.address_state_id = st.state_id 
    INNER JOIN " . TABLE_PREFIX . "city AS c ON a.address_city_id = c.city_id 
    WHERE b.block_site_id = '" . SITE_ID . "' 
    {$statusFilter} 
    {$typeFilter} 
    {$warehouseFilter} 
    GROUP BY w.warehouse_title
    ORDER BY w.warehouse_title ASC
";
    } else {

        $query = "
    SELECT 
    w.warehouse_title, 
    b.block_size_dimension,
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'block_code', IFNULL(b.block_code, ''), 
            'block_title', IFNULL(b.block_title, ''), 
            'net_weight', FORMAT(ROUND(IFNULL(b.block_net_size, 0), 2), 2), 
            'block_thickness', IFNULL(CONCAT(b.block_thickness_size, ' ', b.block_thickness_dimension), '')
        )
    ) AS block_details
FROM {$tableNamePrefix}block AS b
INNER JOIN " . TABLE_PREFIX . "warehouse AS w ON b.block_warehouse_id = w.warehouse_id 
LEFT JOIN " . TABLE_PREFIX . "supplier AS s ON b.block_supplier_id = s.supplier_id 
INNER JOIN " . TABLE_PREFIX . "address AS a ON w.warehouse_address_id = a.address_id 
INNER JOIN " . TABLE_PREFIX . "state AS st ON a.address_state_id = st.state_id 
INNER JOIN " . TABLE_PREFIX . "city AS c ON a.address_city_id = c.city_id 
WHERE b.block_site_id = '" . SITE_ID . "' 
{$statusFilter} 
{$typeFilter} 
{$warehouseFilter} 
GROUP BY w.warehouse_title, b.block_size_dimension
ORDER BY w.warehouse_title ASC;
";
    }

    ?>
    <table class="showInventoryTableList display nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th>S. No.</th>
                <th>Code</th>
                <th>Title</th>
                <th>Net weight</th>
                <th>Thickness</th>
            </tr>
        </thead>
        <?php

        if ($inventoryList = mysqli_query($connection, $query)) {
            echo "<tbody>";
            $totalNetQty = array();
            if (mysqli_num_rows($inventoryList) > 0) {
                while ($inventoryData = mysqli_fetch_assoc($inventoryList)) {
                    $blockNetSize = array();
                    extract($inventoryData);
                    switch ($block_size_dimension) {
                        case 'mt_weight':
                            $block_size_dimension = 'mt (t)';
                            $measurementByWeight = TRUE;
                            break;
                    }

                    $serialNo = 1;
                    echo "<tr>
                        <td colspan=5><strong>{$warehouse_title}</strong></td>";
                    echo "</tr>";
                    $blockLists = json_decode($block_details, TRUE);
                    foreach ($blockLists as $block_values) {
                        echo "<tr>";
                        echo "<td>{$serialNo}</td>";
                        echo "<td>{$block_values['block_code']}</td>";
                        echo "<td>{$block_values['block_title']}</td>";
                        echo "<td>{$block_values['net_weight']} {$block_size_dimension}</td>";
                        echo (($measurementByWeight == true) ? "<td></td>" : "<td>{$block_values['block_thickness']}</td>");
                        echo "</tr>";
                        $serialNo++;
                        $totalNetQty[] = $block_values['net_weight'];
                        $blockNetSize[] = $block_values['net_weight'];
                    }

                    echo "<tr>
                            <td colspan=3>Block total</td>
                            <td colspan=4>" . array_sum($blockNetSize) . " {$block_size_dimension}</td>
                        </tr>";

                    // echo "<tr class='no-border'>
                    //         <td colspan=7></td>
                    //     </tr>";
                    // echo "<tr class='no-border'>
                    //         <td colspan=7></td>
                    //     </tr>";

                    $measurementByWeight = false;
                }
            }
            echo "</tbody>";
        } else {
            if (DEVELOPMENT_MODE == TRUE) {
                echo "Debugging: " . mysqli_error($connection);
            }
        }

        // echo "
        //         <tfoot>
        //         <tr>
        //             <td colspan=3><strong>All Total</strong></td>
        //             <td colspan=4>" . array_sum($totalNetQty) . " {$block_size_dimension}</td>
        //         </tr>
        //     </tfoot>
        // ";

        ?>
    </table>
    <footer>
        <script>
            window.onload = function() {
                window.print();
            };
        </script>
    </footer>
</body>

</html>