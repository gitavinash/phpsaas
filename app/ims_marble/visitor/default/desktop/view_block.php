<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($id)) {
    $blockId = base64_decode($id);

    if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) > 0) {
            extract(mysqli_fetch_assoc($checkBlock));

            // $supplierAddress = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "supplier INNER JOIN " . TABLE_PREFIX . "address ON supplier_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id WHERE supplier_id={$block_supplier_id}");

            // if (mysqli_num_rows($supplierAddress) > 0) {
            //     print_r(mysqli_fetch_assoc($supplierAddress));
            // }

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
    <title>View Inventory status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <style>
    body {
        text-align: center;
        padding: auto;
        margin: 0 auto;
        font-family: "Rubik", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
        color: #333;
    }

    strong {
        font-weight: 600;
        color: dimgray;
    }

    .companyLogo img {
        padding: 10px 0px;
        max-height: 80px;
    }

    h1 {
        font-size: 2rem;
        color: #CA5F37;
        border-bottom: 1px solid #CA5F37;
    }

    .dataBox {
        float: left;
        width: 90%;
        margin: 10px 5%;
        padding: 0px 10px;
        box-sizing: border-box;
        border-radius: 4px;
        border: 1px solid #CA5F37;
    }

    .dataRow {
        float: left;
        width: 100%;
        text-align: left;
        border-bottom: 1px solid #CA5F37;
        margin-bottom: 10px;
    }

    .dataRow:last-child {
        border: none;
    }

    img.preview {
        width: 100px;
        height: auto;
        max-height: 100px;
        float: left;
        padding: 10px;
        box-sizing: border-box;
    }
    </style>
</head>

<body>
    <div class="companyLogo">
        <img src="<?php echo SITE_APP_IMAGE ?>" alt="">
    </div>
    <div class="dataBox" id="blockInfo">
        <div class="heading">
            <h1>Block information</h1>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Name</strong></p>
            </div>
            <div class="Description">
                <p><?php echo $block_title ?></p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Block code</strong></p>
            </div>
            <div class="Description">
                <p><?php echo $block_code ?></p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>About block</strong></p>
            </div>
            <div class="Description">
                <p><?php echo $block_description ?></p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Block total items</strong></p>
            </div>
            <div class="Description">
                <p><?php echo (($block_total_item == '') ? 0 : $block_total_item) ?> Pcs</p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Block last updated on</strong></p>
            </div>
            <div class="Description">
                <p><?php echo date('l, d, M Y, H:i:s', strtotime($block_update)) ?></p>
            </div>
        </div>
    </div>
    <div class="dataBox" id="blockGallery">

    </div>
    <div class="dataBox" id="blockGallery">
        <div class="heading">
            <h1>Block gallery</h1>
        </div>
        <?php
            $block_gallery = json_decode($block_gallery, true);
            for ($i = 0; $i < sizeof($block_gallery); $i++) {
                echo "<img src='{$block_gallery[$i]['path']}' class='preview' >";
            }
            ?>
    </div>
</body>

</html>

<?php
}

?>