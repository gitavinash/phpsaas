<?php

    include_once 'header.php';

?>

<!-- Default post view -->

<section class='body'>

    <div class="main">
        <h1><?php echo $media_title; ?></h1>
        <div class='post-info'>
            <p><strong>Updated </strong> <?php echo date('D, dS M Y h:i A T', strtotime($link_modified)) ?></p>
        </div>
        <div class="feature-img">
            <img src="//<?php echo SITE_DOMAIN.$media_url ?>" alt="">
        </div>

        <div class="comment">
                                   
        </div>

        <div class="taboola">
            <div id="taboola-below-article-thumbnails"></div>
            <script type="text/javascript">
            window._taboola = window._taboola || [];
            _taboola.push({
                mode: 'thumbnails-a',
                container: 'taboola-below-article-thumbnails',
                placement: 'Below Article Thumbnails',
                target_type: 'mix'
            });
            </script>
        </div>

    </div>

    <div class="sidebar">
        <?php
        
            require_once 'sidebar.php';

        ?>
    </div>

</section>

<?php

    include_once 'footer.php';