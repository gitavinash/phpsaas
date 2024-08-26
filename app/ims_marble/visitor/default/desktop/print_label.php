<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($id)) {
    $blockId = base64_decode($id);
    $theme = ((isset($theme)) ? $theme : 1);
    if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) > 0) {
            extract(mysqli_fetch_assoc($checkBlock));
            $showPage = true;
        }
    }
} else {
    $msg = "Missing required details";
}

if (isset($showPage)) { ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print label | <?php echo SITE_NAME ?></title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        /* display: flex; */
        /* justify-content: center;
        align-items: center; */
        /* height: 100vh; */
        background-color: #eaeaea;
    }

    .label-box {
        width: 500px;
        padding: 30px;
        background-color: #fff;
        border: 1px solid #ddd;
        /* border-radius: 15px; */
        /* box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); */
        transition: all 0.3s ease-in-out;
    }

    .label-box:hover {
        /* box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); */
    }

    .label-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .logo {
        width: auto;
        max-height: 80px;
    }

    .qr-code {
        width: 80px;
        height: 80px;
        position: relative;
    }

    .qr-text {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        font-size: 14px;
        color: #555;
        margin-top: 5px;
    }

    .inventory-info {
        margin-top: 20px;
    }

    .inventory-info h2 {
        margin: 0 0 15px 0;
        font-size: 24px;
        color: #333;
    }

    .inventory-info p {
        margin: 10px 0;
        font-size: 16px;
        color: #666;
    }

    .inventory-info p strong {
        color: #333;
    }

    img.qr-code-image {
        width: 150px;
        height: 150px;
        border: 1px solid #f1f1f1;
        float: right;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .credit {
        position: relative;
        right: 0;
        float: right;
        /* margin: -1rem; */
        font-size: 0.6rem;
    }

    /* Print styles */
    @media print {

        body,
        html {
            margin: 0;
            padding: 0;
        }

        .label-box {
            width: 10cm;
            height: 6cm;
            box-shadow: none;
            border: none;
            page-break-inside: avoid;
        }

        .label-header {
            flex-direction: row;
            justify-content: space-between;
        }

        .logo,
        .qr-code {
            width: 2cm;
            height: 2cm;
        }

        .inventory-info h2 {
            font-size: 14pt;
        }

        .inventory-info p {
            font-size: 12pt;
        }

        .qr-text {
            font-size: 10pt;
        }
    }
    </style>
</head>

<body>
    <div class="label-box">
        <div class="label-header">
            <img src="<?php echo SITE_LOGO ?>" alt="Logo" class="logo">
            <div class="qr-code">
                <img src="<?php echo $block_qr ?>" alt="QR Code" class="qr-code-image">
                <!-- <div class="qr-text">Scan this QR to know more</div> -->
            </div>
        </div>
        <?php

            switch ($theme) {
                case 1:
                    echo "
                    <div class='inventory-info'>
                        <p><strong>Name:</strong> {$block_title}</p>
                        <p><strong>ID:</strong> {$block_code}</p>
                        <p><strong>Slabs:</strong> {$block_total_item} " . (($block_total_item == 1) ? 'Pc.' : 'Pcs.') . "</p>
                        <p><strong>Thickness:</strong> {$block_thickness_size} {$block_thickness_dimension}</p>
                    </div>";
                    break;
                default:
                    echo "
                    <div class='inventory-info'>
                        <p><strong>Name:</strong> {$block_title}</p>
                        <p><strong>ID:</strong> {$block_code} <strong>Slabs:</strong>
                            {$block_total_item} " . (($block_total_item == 1) ? 'Pc.' : 'Pcs.') . "
                            <strong>Thickness:</strong> {$block_thickness_size} {$block_thickness_dimension}
                        </p>
                    </div>";
                    break;
            }

            ?>
        <div class="credit">
            <p><strong>Developed by</strong> Blumox Technologies</p>
        </div>
    </div>
</body>

</html>

<?php
}
exit();
?>