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
            text-align: center;
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
            .left {
                text-align: left !important;
            }
            .right {
                text-align: right !important;
            }
            .center {
                text-align: center !important;
            }

        @media print {
            body {
                font-family: "Lato", serif;
                font-style: normal;
                padding: 0;
                margin: 0;
            }
            .left {
                text-align: left !important;
            }
            
            .right {
                text-align: right !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                font-size: 12px;
                text-align: center
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }

            .center {
                text-align: center !important;
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
    list($typeFilter, $typeFilterValue) = getFilterCondition('productType', 'block_product_type');

    // Get status filter (defaulting to `AND block_status!=2`)
    list($statusFilter, $statusFilterValue) = getFilterCondition('status', 'block_status', 'AND block_status!=2');

    mysqli_query($connection, "SET SESSION group_concat_max_len = 10000000");

        $query = "
    SELECT
        w.warehouse_title,
        b.block_size_dimension,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'block_code', IFNULL(b.block_code, ''),
                'block_title', IFNULL(b.block_title, ''),
                'gross_weight', FORMAT(ROUND(IFNULL(b.block_gross_size, 0), 2), 2),
                'damage_weight', FORMAT(ROUND(IFNULL(b.block_damage_size, 0), 2), 2),
                'net_weight', FORMAT(ROUND(IFNULL(b.block_net_size, 0), 2), 2),
                'block_thickness', IFNULL(CONCAT(b.block_thickness_size, ' ', b.block_thickness_dimension), ''),
                'block_dimension_type', IFNULL(b.block_size_dimension, ''),
                'block_slab_dimension_type', (
                    SELECT GROUP_CONCAT(DISTINCT bi.block_item_dimension_type SEPARATOR ', ') 
                    FROM {$tableNamePrefix}block_item AS bi
                    WHERE bi.block_item_parent_id = b.block_id
                ),
                'block_slabs', (
                    SELECT JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'width', IFNULL(bi.block_item_width, ''),
                            'height', IFNULL(bi.block_item_height, ''),
                            'slab_gross_size', IFNULL(bi.block_item_gross_size, ''),
                            'slab_net_size', IFNULL(bi.block_item_net_size, ''),
                            'slab_damage_size', IFNULL(bi.block_item_damage_size, ''),
                            'damage_area', IFNULL(bi.block_item_damage_area, ''),
                            'total_damage', IFNULL(bi.block_item_damage_count, ''),
                            'slab_status', IFNULL(bi.block_item_status, '')
                        )
                    ) 
                    FROM {$tableNamePrefix}block_item AS bi
                    WHERE bi.block_item_parent_id = b.block_id
                )
            )
        ) AS block_details
    FROM
        {$tableNamePrefix}block AS b
        INNER JOIN " . TABLE_PREFIX . "warehouse AS w ON b.block_warehouse_id = w.warehouse_id
        LEFT JOIN " . TABLE_PREFIX . "supplier AS s ON b.block_supplier_id = s.supplier_id
        INNER JOIN " . TABLE_PREFIX . "address AS a ON w.warehouse_address_id = a.address_id
        INNER JOIN " . TABLE_PREFIX . "state AS st ON a.address_state_id = st.state_id
        INNER JOIN " . TABLE_PREFIX . "city AS c ON a.address_city_id = c.city_id
    WHERE
        b.block_site_id = '" . SITE_ID . "'
        {$statusFilter} 
        {$typeFilter} 
        {$warehouseFilter}
    GROUP BY
        w.warehouse_title, b.block_size_dimension
    ORDER BY
        w.warehouse_title ASC";

    if ($inventoryList = mysqli_query($connection, $query)) {
        if(mysqli_num_rows($inventoryList) > 0) {
            $warehouseBlockData=extract(mysqli_fetch_assoc($inventoryList));

            echo "
                <table>
                    <tr>
                        <td colspan=100 class='left' ><strong><h1>{$warehouse_title}</h1></strong><br /><br /></td>
                    </tr>
                </table>
            ";

            $warehouseBlockDetails = json_decode($block_details, true);

            // print_r($warehouseBlockDetails);

            foreach($warehouseBlockDetails as $blockKey => $blockValue) {
                extract($blockValue);
                echo "<table>
                    <tr>
                        <td colspan=100 class='left' ><strong><h2>{$block_title} - {$block_code}</h2></strong></td>
                    </tr>
                    <div class='center'>
                        <tr>
                            <td rowspan=2>S no.</td>
                            <td rowspan=2>Width ({$block_slab_dimension_type})</td>
                            <td rowspan=2>Height ({$block_slab_dimension_type})</td>
                            <td rowspan=2>Net Weight ({$block_dimension_type})</td>
                            <td colspan=100>Damage Area</td>
                        </tr>
                        <tr>
                            
                            <td>Width ({$block_slab_dimension_type})</td>
                            <td>Height ({$block_slab_dimension_type})</td>
                            <td>Net Weight ({$block_dimension_type})</td>

                            <td>Width ({$block_slab_dimension_type})</td>
                            <td>Height ({$block_slab_dimension_type})</td>
                            <td>Net Weight ({$block_dimension_type})</td>

                            <td>Width ({$block_slab_dimension_type})</td>
                            <td>Height ({$block_slab_dimension_type})</td>
                            <td>Net Weight ({$block_dimension_type})</td>

                            <td>Width ({$block_slab_dimension_type})</td>
                            <td>Height ({$block_slab_dimension_type})</td>
                            <td>Net Weight ({$block_dimension_type})</td>

                            <td>Width ({$block_slab_dimension_type})</td>
                            <td>Height ({$block_slab_dimension_type})</td>
                            <td>Net Weight ({$block_dimension_type})</td>
                        </tr>
                    </div>";

                $i=0;
                foreach($block_slabs as $slabsKey => $slabsvalue) {
                    echo "
                        <tr>
                            <td>".($i+1)."</td>
                            <td>{$slabsvalue['width']}</td>
                            <td>{$slabsvalue['height']}</td>
                            <td>{$slabsvalue['slab_net_size']}</td>";

                            $warehouseBlockSlabDamageArea=json_decode($slabsvalue['damage_area'], true);

                            for($j=0; $j<5; $j++) {
                                if(isset($warehouseBlockSlabDamageArea[$j])) {
                                    echo "
                                        <td>{$warehouseBlockSlabDamageArea[$j]['damageAreaWidth']}</td>
                                        <td>{$warehouseBlockSlabDamageArea[$j]['damageAreaHeight']}</td>
                                        <td></td>
                                    ";
                                } else {
                                    echo "
                                        <td class='center' >-</td>
                                        <td class='center'>-</td>
                                        <td class='center'>-</td>
                                    ";
                                }
                            }

                            // foreach($warehouseBlockSlabDamageArea as $key => $value) {
                            //     echo "
                            //         <td>{$value['damageAreaWidth']}</td>
                            //         <td>{$value['damageAreaHeight']}</td>
                            //         <td></td>
                            //     ";
                            // }

                        echo "</tr>
                    ";
                    $i++;
                }

                echo "
                    <tr>
                        <td colspan=3 class='right'><strong>Gross Qty. ( {$block_dimension_type} )</strong></td>
                        <td>{$gross_weight}</td>
                        <td colspan=2></td>
                        <td class='right'></td>
                        <td colspan=2></td>
                        <td class='right'></td>
                        <td colspan=2></td>
                        <td class='right'></td>
                        <td colspan=2></td>
                        <td class='right'></td>
                        <td colspan=2></td>
                        <td class='right'></td>
                    </tr>
                    <tr>
                        <td colspan=3 class='right'><strong>Damage Qty. ( {$block_dimension_type} )</strong></td>
                        <td colspan=100 class='left'>{$damage_weight}</td>
                    </tr>
                    <tr>
                        <td colspan=3 class='right'><strong>Net Qty. ( {$block_dimension_type} )</strong></td>
                        <td colspan=100 class='left'>{$net_weight}</td>
                    </tr>
                    <tr>
                        <td colspan=100><br /><br /></td>
                    </tr>
                </table>";
            }


            // echo "<pre>";
            // print_r($warehouseBlockData);
            // echo"</pre>";
            // echo "<pre>";
            // print_r(json_decode($warehouseBlockData["block_details"], true));
            // echo"</pre>";
        }
    }

    ?>
    
</body>

</html>