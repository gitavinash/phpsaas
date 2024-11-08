<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Slab Measurement | <?php echo SITE_NAME ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Rubik", sans-serif;
            margin: 0;
            padding: 0;
            font-size: 0.8rem;
        }

        img {
            max-height: 100px;
        }

        h1 {
            font-size: 1.5rem;
            text-align: center;
            margin: 20px 0;
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

        /* Hide the download button in print/PDF */
        @media print {
            #download {
                display: none !important;
            }
        }

        a#download {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: <?php echo SITE_THEME_COLOUR ?>;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        a#download:hover {
            background-color: #333;
        }

        p.credit strong {
            font-size: 12px;
            font-weight: 300;
            line-height: 24px;
        }

        p.credit {
            font-size: 14px;
            font-weight: 700;
            float: left;
            width: 100%;
            text-align: center;
            padding: 5rem 0rem 0rem 0rem;
        }

        a {
            color: #143153;
            text-decoration: none;
        }
    </style>

</head>

<body>
    <div id="content">
        <?php
        $tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

        if (isset($id)) {
            $blockId = base64_decode($id);
            $query = "SELECT * FROM {$tableNamePrefix}block_item 
                      INNER JOIN {$tableNamePrefix}block 
                      ON block_item_parent_id = block_id 
                      WHERE block_item_parent_id = {$blockId} 
                      AND block_item_status IN (1, 3) 
                      AND block_item_site_id = " . SITE_ID;

            if ($checkBlock = mysqli_query($connection, $query)) {
                if (mysqli_num_rows($checkBlock) > 0) {
                    echo "<table>";
                    $i = 1;
                    while ($slabSize = mysqli_fetch_assoc($checkBlock)) {
                        extract($slabSize);

                        if ($i == 1) {
                            $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                            extract(json_decode($measurementData, true));

                            echo "<tr>
                                <td colspan=100><img src='" . SITE_LOGO . "'></td>
                            </tr>
                            <tr>
                                <td colspan=100><h1>{$block_title} " . ($block_code ? " - {$block_code}" : '') . "</h1></td>
                            </tr>
                            <tr>
                                <th>S No.</th>
                                <th>Width ({$block_item_dimension_type})</th>
                                <th>Height ({$block_item_dimension_type})</th>
                                <th>Thickness ({$block_thickness_dimension})</th>
                                <th>Gross Qty ({$calculationType})</th>
                                <th>Damage Qty ({$calculationType})</th>
                                <th>Net Qty ({$calculationType})</th>
                            </tr>";
                        }

                        $totalArea = $block_item_width * $block_item_height;

                        switch ([$measurementType, $calculationType]) {
                            case ['cm', 'sq. ft.']:
                                $totalArea *= 0.001076391;
                                break;
                            case ['in', 'sq. ft.']:
                                $totalArea *= 0.00694444;
                                break;
                            case ['ft', 'sq. mt.']:
                                $totalArea *= 0.09290304;
                                break;
                            case ['mt', 'sq. ft.']:
                                $totalArea *= 10.7639;
                                break;
                        }

                        $totalArea = number_format((float)$totalArea, 2, '.', '');
                        $netArea = number_format($totalArea - $block_item_damage_size, 2, '.', '');

                        if ($block_item_status == 1) {
                            echo "
                            <tr>
                                <td>{$i}</td>
                                <td>{$block_item_width}</td>
                                <td>{$block_item_height}</td>
                                <td>{$block_thickness_size}</td>
                                <td>{$totalArea}</td>
                                <td>{$block_item_damage_size}</td>
                                <td>{$netArea}</td>
                            </tr>";
                        }
                        $i++;
                    }
                    echo "</table>";
                } else {
                    echo "No data found.";
                }
            } else {
                echo "Error in query execution.";
            }
        } else {
            echo "Missing required details.";
        }
        ?>
        <a id="download">Download PDF</a>
        <p class="credit"><strong>Design &amp; Developed by</strong><a href="https://blumox.com"
                title="ERP for Marble Warehouse"><br />Blumox Technologies</a></p>
    </div>

    <script>
        document.getElementById('download').addEventListener('click', function() {
            const content = document.getElementById('content');
            const downloadButton = document.getElementById('download');

            // Temporarily hide the download button before generating the PDF
            downloadButton.style.display = 'none';

            // Generate the PDF and save it
            html2pdf()
                .from(content)
                .save('<?php echo strtolower(str_replace(' ', '_', $block_title)) . '_' . time() ?>.pdf')
                .then(() => {
                    // Restore the download button after the PDF is saved
                    downloadButton.style.display = 'block';
                });
        });
    </script>

</body>

</html>