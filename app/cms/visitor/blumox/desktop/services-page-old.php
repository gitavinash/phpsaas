<?php

require_once 'header.php';

?>

<section>
    <div class="introBox">
        <h1>Our Services</h1>
    </div>
</section>


<section>
    <?php

    if ($services = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "service INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=service_id WHERE service_status=1 AND link_site_id=" . SITE_ID . " AND link_type='service' AND  service_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($services) != 0) {
            $i = 0;
            while ($servicesData = mysqli_fetch_assoc($services)) {
                extract($servicesData);
                if ($i == 0) {
                    echo "
                        <div class='projectBox'>
                            <div class='image'>
                                <img src='{$service_image}' alt=''>
                            </div>
                            <div class='text lsBgColor'>
                                <h2 class='blumox'>{$service_title}</h2>
                                <p>{$service_description}</p>
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
                                <p>{$service_description}</p>
                                <p><a href='{$link_url}' class='tryNowBtn'>Get Started!</a></p>
                            </div>
                        </div>
                    ";
                    $i = 0;
                }
            }
        }
    }

    ?>

</section>

<?php

require_once 'footer.php';

?>