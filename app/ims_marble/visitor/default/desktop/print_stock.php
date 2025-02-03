<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Stock | <?php echo SITE_NAME ?></title>
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
                font-family: Arial, sans-serif;
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
        }
    </style>
</head>

<body>
    <?php
    $tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
    $block_size_dimension = 'sq. ft.';
    if (isset($_GET['warehouse'])) {
        if ($_GET['warehouse'] != '#') {
            $warehouseFilterValue = $_GET['warehouse'];
            $warehouseFilter = "AND block_warehouse_id='{$warehouseFilterValue}'";
        } else {
            $warehouseFilterValue = '';
            $warehouseFilter = '';
        }
    } else {
        $warehouseFilterValue = '';
        $warehouseFilter = '';
    }

    if (isset($_GET['product_type'])) {
        if ($_GET['product_type'] != '#') {
            $typeFilterValue = $_GET['product_type'];
            $typeFilter = "AND block_product_type='{$typeFilterValue}'";
        } else {
            $typeFilterValue = '';
            $typeFilter = '';
        }
    } else {
        $typeFilterValue = '';
        $typeFilter = '';
    }

    if (isset($_GET['status'])) {
        if ($_GET['status'] != '#') {
            $statusFilterValue = $_GET['status'];
            $statusFilter = "AND block_status='{$statusFilterValue}'";
        } else {
            $statusFilterValue = '';
            $statusFilter = 'AND block_status!=2';
        }
    } else {
        $statusFilterValue = '';
        $statusFilter = 'AND block_status!=2';
    }

    $query = "
    SELECT 
        w.warehouse_title, 
        CONCAT('[', 
            GROUP_CONCAT(
                REPLACE(REPLACE(
                    JSON_OBJECT(
                        'block_code', IFNULL(b.block_code, ''), 
                        'block_title', IFNULL(b.block_title, ''), 
                        'Net. Weight', IFNULL(b.block_net_size, ''), 
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
                    extract($inventoryData);
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
                        echo "<td>{$block_values['net_weight']}</td>";
                        echo "<td>{$block_values['block_thickness']}</td>";
                        echo "</tr>";
                        $serialNo++;
                        $totalNetQty[] = $block_values['net_weight'];
                    }
                }
            }
            echo "</tbody>";
            echo "
        <tfoot>
        <tr>
            <td colspan=3><strong>Total</strong></td>
            <td colspan=4>" . array_sum($totalNetQty) . " {$block_size_dimension}</td>
        </tr></tfoot>";
        } else {
            if (DEVELOPMENT_MODE == TRUE) {
                echo "Debugging: " . mysqli_error($connection);
            }
        }

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