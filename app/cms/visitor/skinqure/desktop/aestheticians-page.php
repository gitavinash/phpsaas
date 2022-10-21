<?php

require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
                    <div class="inner_content">
                        <p><?php echo ucwords($page_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($page_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_spacing_top section_spacing_bottom">
    <div class="container">


        <?php

        if ($checkTeams = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page WHERE page_type='team' AND page_status=1 AND page_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkTeams) != 0) {

                while ($teamsData = mysqli_fetch_assoc($checkTeams)) {

                    extract($teamsData);

                    echo "<div class='row section_spacing_bottom'>
                    <div class='col-12 col-md-4'>
                        <img src='{$page_image}' alt='' style='width: 100%' >
                    </div>
                    <div class='col-12 col-md-8'>
                        <div class='w-100 formfield text-white form-box-non-bg'>
                            <div class='section_heading'>
                                <h3 class='text-start'>{$page_title}</h3>
                            </div>
                            <p class='abt_p'>{$page_description}</p>
                        </div>
                    </div>
                </div>";
                }
            }
        }

        ?>


    </div>
</section>

<?php

require_once 'footer.php';

?>