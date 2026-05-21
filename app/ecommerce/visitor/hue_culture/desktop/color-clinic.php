<?php

require_once 'header.php';

?>

<!-- Hero Section -->
<section class="hero"
    style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo USER_IMAGES ?>color-clinic-hero.jpeg') center/cover no-repeat; padding-top: 200px; padding-bottom: 150px; align-items: center; min-height: 70vh; display: flex; justify-content: center; text-align: center;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <div class="hero-content reveal-on-load">
            <h1 class="hero-premium-headline"
                style="margin-bottom: 20px; font-size: 4.5rem; color: var(--white); text-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                Color Clinic
            </h1>
            <p
                style="font-size: 1.35rem; color: #fff; line-height: 1.6; text-shadow: 0 2px 10px rgba(0,0,0,0.5); margin: 0 auto; max-width: 700px;">
                Our expert team employs scientific color analysis and refined intuition to transform personal styling,
                interior environments, and energetic alignments. We empower you to see and utilize color the way the
                experts do.
            </p>
        </div>
    </div>
</section>


<?php

if ($checkProductCategory = mysqli_query($connection, "SELECT link_url, category_title, category_image, category_description FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id = category_id AND link_site_id=" . SITE_ID . " AND link_status=200 AND link_type='category' WHERE category_type='product' AND category_status=1 ORDER BY link_published ASC")) {
    if (mysqli_num_rows($checkProductCategory) != 0) {
        $i = 0;
        while ($productCategoryDetails = mysqli_fetch_assoc($checkProductCategory)) {
            extract($productCategoryDetails);
            if ($i % 2 == 0) {
                echo "<section class='soft-green-bg' style=' padding: 100px 8%;'>
                    <div class='container'
                        style='display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;'>

                        <div class='image-content reveal' style='flex: 1; min-width: 320px;'>
                            <div class='icon-box-wrapper'
                                style='border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;'
                                onmouseover='this.style.transform='translateY(-10px)''
                                onmouseout='this.style.transform='translateY(0)''>
                                <img src='{$category_image}' style='max-height: 250px;
width: auto; object-fit: contain;' alt='Spaces'>
</div>
</div>

<div class='text-content reveal' style='flex: 1; min-width: 320px;'>
    <h2 class='serif-normal' style='font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);'>{$category_title}</h2>
    <p style='font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;'>
        {$category_description}
    </p>
    <a href='{$link_url}' class='btn-primary light-pink-bg' style='display: inline-block;'>Know More</a>
</div>
</div>
</section>";
            } else {
                echo "<section class='soft-green-bg' style=' padding: 100px 8%;'>
                    <div class='container'
                        style='display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; max-width: 1400px; width: 100%;'>

<div class='text-content reveal' style='flex: 1; min-width: 320px;'>
    <h2 class='serif-normal' style='font-size: 2.8rem; margin-bottom: 20px; color: var(--charcoal);'>{$category_title}</h2>
    <p style='font-size: 1.2rem; color: #555; line-height: 1.8; margin-bottom: 30px;'>
        {$category_description}
    </p>
    <a href='{$link_url}' class='btn-primary light-pink-bg' style='display: inline-block;'>Know More</a>
</div>
                        <div class='image-content reveal' style='flex: 1; min-width: 320px;'>
                            <div class='icon-box-wrapper'
                                style='border: 1px solid rgba(0,0,0,0.05); border-radius: 24px; padding: 60px 40px; text-align: center; background: #fafafa; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s; display: flex; justify-content: center; align-items: center;'
                                onmouseover='this.style.transform='translateY(-10px)''
                                onmouseout='this.style.transform='translateY(0)''>
                                <img src='{$category_image}' style='max-height: 250px;
width: auto; object-fit: contain;' alt='Spaces'>
</div>
</div>
</div>
</section>";
            }
            $i++;
        }
    }
}

?>

<?php

require_once 'footer.php';

?>