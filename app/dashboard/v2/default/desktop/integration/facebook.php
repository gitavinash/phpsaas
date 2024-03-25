<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed her</span>
</h2>
<div class="main-box">
    <?php
    
    if($pageList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."fb_pages WHERE fb_page_status=true AND fb_page_site_id=".SITE_ID)) {

        if(mysqli_num_rows($pageList)!=0) { ?>

    <section class="fb_page_list">
    </section>

    <?php
        } else { ?>

    <script>
    window.location.href = 'https://aleya.in/facebook/?token=' + '<?php echo base64_encode($user_salt_key); ?>' +
        '&site=' + '<?php echo base64_encode(SITE_ID); ?>' +
        '&new=true';
    </script>

    <?php
        }

    }
    
    ?>
</div>