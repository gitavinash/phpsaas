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
    <title>Block inventory details | <?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: "Rubik", sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    a {
        text-decoration: none;
        color: <?php echo SITE_THEME_COLOUR ?>;
    }

    .container {
        max-width: 100%;
        min-height: 100vh;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .logo {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .logo img {
        max-width: 200px;
        height: auto;
    }

    .feature-image {
        width: 100%;
        height: auto;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .gallery {
        display: flex;
        gap: 10px;
        overflow-x: auto;
    }

    .gallery img {
        width: calc(50% - 10px);
        height: auto;
        border-radius: 10px;
        cursor: pointer;
    }

    .inventory-info {
        margin-bottom: 20px;
    }

    .inventory-info h2 {
        font-size: 24px;
        margin-bottom: 10px;
        color: <?php echo SITE_THEME_COLOUR ?>;
    }

    .inventory-info p {
        font-size: 0.9rem;
        margin: 5px 0;
        color: #333;
        font-weight: 300;
        line-height: 1.6rem;
    }

    .inventory-info p strong {
        color: #000;
        font-weight: 600;
    }

    .download-btn {
        display: block;
        width: 100%;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: <?php echo SITE_THEME_COLOUR ?>;
        border-radius: 5px;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.3s ease;
        box-sizing: border-box;
    }

    .download-btn:hover {
        background-color: <?php echo SITE_THEME_COLOUR ?>;
    }

    /* Fullscreen Image Viewer Styles */
    .fullscreen-viewer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .fullscreen-viewer img {
        width: 90%;
        height: auto;
        max-width: 600px;
        max-height: 90%;
        border-radius: 10px;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 24px;
        color: #fff;
        cursor: pointer;
    }

    /* Floating Share Button Styles */
    .share-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: <?php echo SITE_THEME_COLOUR ?>;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .share-btn:hover {
        background-color: <?php echo SITE_THEME_COLOUR ?>;
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

    /* Responsive Styles */
    @media (max-width: 768px) {
        .gallery img {
            width: calc(100% - 10px);
            margin-bottom: 10px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
        </div>
        <?php

            if ($block_feature_image != '') {
                echo "<img src='{$block_feature_image}' alt='{$block_title}' class='feature-image'>";
            }

            ?>
        <div class="gallery">
            <?php
                if ($block_gallery != '') {
                    $block_gallery = json_decode($block_gallery, true);
                    for ($i = 0; $i < sizeof($block_gallery); $i++) {
                        echo "<img src='{$block_gallery[$i]['path']}' class='preview thumbnail' >";
                    }
                }
                ?>
        </div>
        <div class="inventory-info">
            <h2>Block information</h2>
            <p><strong>Item Name:</strong> <?php echo $block_title ?></p>
            <p><strong>Item ID:</strong> <?php echo $block_code ?></p>
            <p><strong>Quantity:</strong> <?php echo (($block_total_item == '') ? 0 : $block_total_item) ?> Pcs</p>
            <p><strong>Net qty.:</strong> <?php echo $block_net_size . ' ' . $block_size_dimension ?></p>
            <p><strong>Thickness: </strong><?php echo $block_thickness_size . ' ' . $block_thickness_dimension; ?></p>
            <?php

                if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID . " ORDER BY block_item_id ASC LIMIT 0,1 ")) {
                    if (mysqli_num_rows($checkBlock) != 0) {
                        while ($blockItemData = mysqli_fetch_assoc($checkBlock)) {
                            extract($blockItemData);
                ?>

            <p><strong>Slab
                    Dimension:
                </strong><?php echo $block_item_width . " {$block_item_dimension_type} x " . $block_item_height . " {$block_item_dimension_type}" ?>
            </p>
            <?php
                        }
                    }
                } else {
                    echo mysqli_error($connection);
                }

                ?>
            <p><strong>Description:</strong> <?php echo $block_description ?></p>
        </div>
        <a href="/view_inventory/?id=<?php echo $id ?>" class="download-btn">Download Now</a>
        <p class="credit"><strong>Design &amp; Developed by</strong><a href="https://blumox.com"
                title="ERP for Marble Warehouse"><br />Blumox Technologies</a></p>
    </div>

    <!-- Fullscreen Image Viewer -->
    <div class="fullscreen-viewer" id="fullscreenViewer">
        <span class="close-btn" onclick="closeFullscreen()">&times;</span>
        <img id="fullscreenImage" src="" alt="Fullscreen Image">
    </div>

    <!-- Floating Share Button -->
    <button class="share-btn" onclick="share()"><i class="fas fa-share-alt"></i></button>

    <script>
    let currentImageIndex = 0;
    const galleryImages = document.querySelectorAll('.gallery img');

    function openFullscreen(imgElement) {
        currentImageIndex = Array.from(galleryImages).indexOf(imgElement);
        const fullscreenViewer = document.getElementById('fullscreenViewer');
        const fullscreenImage = document.getElementById('fullscreenImage');
        fullscreenImage.src = imgElement.src;
        fullscreenViewer.style.display = 'flex';
    }

    function closeFullscreen() {
        document.getElementById('fullscreenViewer').style.display = 'none';
    }

    function showImage(index) {
        if (index >= 0 && index < galleryImages.length) {
            currentImageIndex = index;
            const fullscreenImage = document.getElementById('fullscreenImage');
            fullscreenImage.src = galleryImages[currentImageIndex].src;
        }
    }

    document.getElementById('fullscreenViewer').addEventListener('touchstart', handleTouchStart, false);
    document.getElementById('fullscreenViewer').addEventListener('touchmove', handleTouchMove, false);

    let xDown = null;

    function getTouches(evt) {
        return evt.touches || evt.originalEvent.touches;
    }

    function handleTouchStart(evt) {
        const firstTouch = getTouches(evt)[0];
        xDown = firstTouch.clientX;
    }

    function handleTouchMove(evt) {
        if (!xDown) {
            return;
        }

        let xUp = evt.touches[0].clientX;
        let xDiff = xDown - xUp;

        if (xDiff > 0) {
            // Swipe left
            showImage(currentImageIndex + 1);
        } else {
            // Swipe right
            showImage(currentImageIndex - 1);
        }
        xDown = null;
    }

    function share() {
        if (navigator.share) {
            navigator.share({
                    title: 'Inventory Details',
                    text: 'Check out this item from our inventory!',
                    url: window.location.href,
                })
                .catch((error) => console.error('Error sharing:', error));
        } else {
            alert('Web Share API not supported in your browser.');
        }
    }
    </script>
</body>

</html>

<?php
}

?>