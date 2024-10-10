<?php

require_once 'header.php';

?>

<section>
    <div class="introBox">
        <h1><?php echo ucwords(strtolower($category_title)) ?></h1>
    </div>
</section>


<section>
    <?php

    if (isset($categorySinglePost)) {
        for ($i = 0; $i < sizeof($categorySinglePost); $i++) {
            extract($categorySinglePost[$i]);
            if ($i % 2 == 0) {
                echo "
                        <div class='projectBox'>
                            <div class='image'>
                                <img src='{$service_image}' alt=''>
                            </div>
                            <div class='text lsBgColor'>
                                <h2 class='blumox'>{$service_title}</h2>
                                <p>{$meta_description}</p>
                                <p>Starting from INR {$inventory_price} /-</p>
                                <p><a href='{$link_url}' class='tryNowBtn'>Get Started!</a></p>
                            </div>
                        </div>
                    ";
                $i++;
            } else {
                echo "
                        <div class='projectBox'>
                            <div class='image leftImageBox'>
                                <img src='{$service_image}' alt=''>
                            </div>
                            <div class='text leftBox aleyaBgColor'>
                                <h2 class='blumox'>{$service_title}</h2>
                                <p>{$meta_description}</p>
                                <p>Starting from INR {$inventory_price} /-</p>
                                <p><a href='{$link_url}' class='tryNowBtn'>Get Started!</a></p>
                            </div>
                        </div>
                    ";
                $i = 0;
            }
        }
    } else {
        echo "<h3 class='notFound'>No service found</h3>";
    }

    ?>
</section>

<?php

require_once 'footer.php';

?>