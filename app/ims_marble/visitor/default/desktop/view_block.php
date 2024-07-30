<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

if (isset($id)) {
    $blockId = base64_decode($id);

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

    p {
        font-size: 1rem;
        padding: 0;
        margin: 0 auto;
    }

    p strong {
        font-size: 0.8rem;
        padding: 0;
        margin: 0 auto;
    }

    strong {
        font-weight: 600;
        color: dimgray;
    }

    .description {
        float: left;
        margin-top: 5px;
        margin-bottom: 1rem;
    }

    .companyLogo img {
        padding: 10px 0px;
        max-height: 80px;
    }

    h1 {
        font-size: 2rem;
        color: <?php echo SITE_THEME_COLOUR ?>;
        border-bottom: 1px solid <?php echo SITE_THEME_COLOUR ?>;
    }

    .dataBox {
        float: left;
        width: 90%;
        margin: 10px 5%;
        padding: 0px 10px;
        box-sizing: border-box;
        border-radius: 4px;
        border: 1px solid <?php echo SITE_THEME_COLOUR ?>;
    }

    .dataRow {
        float: left;
        width: 100%;
        text-align: left;
        border-bottom: 1px solid <?php echo SITE_THEME_COLOUR ?>;
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

    .gallery {
        display: flex;
        flex-wrap: wrap;
    }

    .thumbnail {
        width: 100px;
        height: 100px;
        margin: 10px;
        cursor: pointer;
    }

    #full-screen-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    #full-screen-image {
        max-width: 90%;
        max-height: 90%;
    }

    .hidden {
        display: none;
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
            <div class="description">
                <p><?php echo $block_title ?></p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Code</strong></p>
            </div>
            <div class="description">
                <p><?php echo $block_code ?></p>
            </div>
        </div>
        <?php

            if ($block_description != '') { ?>
        <div class="dataRow">
            <div class="title">
                <p><strong>Intro</strong></p>
            </div>
            <div class="description">
                <p><?php echo $block_description ?></p>
            </div>
        </div>
        <?php
            }

            ?>
        <div class="dataRow">
            <div class="title">
                <p><strong>Total items</strong></p>
            </div>
            <div class="description">
                <p><?php echo (($block_total_item == '') ? 0 : $block_total_item) ?> Pcs</p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Net qty.</strong></p>
            </div>
            <div class="description">
                <p><?php echo $block_net_size . ' ' . $block_size_dimension ?></p>
            </div>
        </div>
        <div class="dataRow">
            <div class="title">
                <p><strong>Thickness</strong></p>
            </div>
            <div class="description">
                <p><?php echo $block_thickness_size . ' ' . $block_thickness_dimension; ?>
                </p>
            </div>
        </div>
        <?php

            if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID . " ORDER BY block_item_id ASC LIMIT 0,1 ")) {
                if (mysqli_num_rows($checkBlock) != 0) {
                    while ($blockItemData = mysqli_fetch_assoc($checkBlock)) {
                        extract($blockItemData);
            ?>

        <div class="dataRow">
            <div class="title">
                <p><strong>Slab Dimension</strong></p>
            </div>
            <div class="description">
                <p><?php echo $block_item_width . " {$block_item_dimension_type} x " . $block_item_height . " {$block_item_dimension_type}" ?>
                </p>
            </div>
        </div>
        <?php
                    }
                }
            } else {
                echo mysqli_error($connection);
            }

            ?>
    </div>
    <div class="dataBox" id="blockGallery">

    </div>
    <div class="dataBox" id="blockGallery">
        <div class="heading">
            <h1>Block gallery</h1>
        </div>
        <div class="gallery">
            <?php
                if ($block_gallery != '') {
                    $block_gallery = json_decode($block_gallery, true);
                    for ($i = 0; $i < sizeof($block_gallery); $i++) {
                        echo "<img src='{$block_gallery[$i]['path']}' class='preview thumbnail' >";
                    }
                } else {
                    echo "<p>No image found</p>";
                }
                ?>
        </div>
    </div>
    <div class="dataBox" id="blockInventory">
        <div class="heading">
            <a href="/view_inventory/?id=<?php echo $id ?>" taget="_blank">Download Slabs size</a>
        </div>
    </div>
    <div class="credit">
        <p>Design &amp; Developed by <a href="https://blumox.com">Blumox Technologies</a></p>
    </div>
    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script>
    $('.thumbnail').click(function() {
        var src = $(this).attr('src');
        $('#full-screen-image').attr('src', src);
        $('#full-screen-container').removeClass('hidden');
    });

    $('#full-screen-container').click(function() {
        $(this).addClass('hidden');
    });
    </script>
</body>

</html>

<?php
}

?>