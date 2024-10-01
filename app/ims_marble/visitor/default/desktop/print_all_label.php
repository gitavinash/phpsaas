<?php

// Table name prefix definition
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

// Check if the required ID is set
if (isset($theme)) {
    // Decode the block ID
    $theme = isset($theme) ? $theme : 1;

    $query = "SELECT block_title, block_qr, block_code, block_total_item, block_available, block_thickness_size, block_thickness_dimension FROM {$tableNamePrefix}block 
              INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id = warehouse_id 
              INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id = address_id 
              INNER JOIN " . TABLE_PREFIX . "city ON address_city_id = city_id 
              INNER JOIN " . TABLE_PREFIX . "state ON address_state_id = state_id 
              WHERE block_status = 1 
              AND block_site_id = " . SITE_ID;

    // Execute the query
    if ($checkBlock = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($checkBlock) > 0) {
            $showPage = true;
        } else {
            $msg = "Block not found or inactive.";
        }
    } else {
        $msg = "Error executing query.";
        error_log("SQL Error: " . mysqli_error($connection));
    }
} else {
    $msg = "Missing required details";
}

// Display the page if $showPage is set
if (isset($showPage)) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print label | <?php echo htmlspecialchars(SITE_NAME); ?></title>

        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #eaeaea;
            }

            .label-box {
                width: 48%;
                /* Adjust width to fit two boxes per row */
                padding: 10px;
                background-color: #fff;
                border: 1px solid #ddd;
                transition: all 0.3s ease-in-out;
                margin: 1%;
                box-sizing: border-box;
                display: inline-block;
                vertical-align: top;
                float: left;
                /* Float left to place label-box side by side */
            }

            .label-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .logo {
                max-height: 40px;
            }

            .qr-code-image {
                width: 70px;
                height: 70px;
                border: 1px solid #f1f1f1;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .inventory-info {
                margin-top: 10px;
                font-size: 10pt;
            }

            .inventory-info h2 {
                margin: 0 0 8px 0;
                font-size: 14px;
                color: #333;
            }

            .inventory-info p {
                margin: 5px 0;
                font-size: 12px;
                color: #666;
            }

            .inventory-info p strong {
                color: #333;
            }

            .credit {
                font-size: 8px;
                text-align: right;
                margin-top: 5px;
            }

            @media print {

                body,
                html {
                    margin: 0;
                    padding: 0;
                }

                .label-box {
                    width: 49%;
                    /* Adjust to ensure two boxes per row on print */
                    height: 5cm;
                    /* Adjust height to fit the print page */
                    border: 1px solid #000;
                    page-break-inside: avoid;
                    break-inside: avoid;
                    margin: 0;
                    padding: 5mm;
                    box-sizing: border-box;
                    float: left;
                    /* Ensure two boxes are placed side by side */
                }

                .label-header {
                    margin-bottom: 5px;
                }

                .logo {
                    max-height: 30px;
                }

                .qr-code-image {
                    width: 40px;
                    height: 40px;
                    border: 1px solid #000;
                }

                .inventory-info {
                    font-size: 14pt;
                }

                .inventory-info h2 {
                    font-size: 9pt;
                }

                .inventory-info p {
                    font-size: 8pt;
                }

                .credit {
                    font-size: 6pt;
                }

                @page {
                    size: A4;
                    margin: 10mm;
                }
            }
        </style>


    </head>

    <body>
        <?php

        while ($labelData = mysqli_fetch_assoc($checkBlock)) {
            extract($labelData); ?>
            <div class="label-box">
                <div class="label-header">
                    <img src="<?php echo htmlspecialchars(SITE_LOGO); ?>" alt="Logo" class="logo">
                    <img src="<?php echo htmlspecialchars($block_qr); ?>" alt="QR Code" class="qr-code-image">
                </div>
                <div class='inventory-info'>
                    <?php if ($theme == 1): ?>
                        <h2>Block Information</h2>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($block_title); ?></p>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($block_code); ?></p>
                        <p><strong>Slabs:</strong> <?php echo htmlspecialchars($block_total_item); ?> <?php echo ($block_total_item == 1) ? 'Pc.' : 'Pcs.'; ?></p>
                        <p><strong>Thickness:</strong> <?php echo htmlspecialchars($block_thickness_size) . ' ' . htmlspecialchars($block_thickness_dimension); ?></p>
                    <?php else: ?>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($block_title); ?></p>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($block_code); ?>
                            <strong>Slabs:</strong> <?php echo htmlspecialchars($block_total_item) . (($block_total_item == 1) ? ' Pc.' : ' Pcs.'); ?>
                            <strong>Thickness:</strong> <?php echo htmlspecialchars($block_thickness_size) . ' ' . htmlspecialchars($block_thickness_dimension); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="credit">
                    <p><strong>Developed by</strong> Blumox Technologies</p>
                </div>
            </div>
        <?php
        }

        ?>
    </body>

    </html>
<?php
}
exit();
?>