<?php
    header("Content-type: text/css");

    ?>
:root {
--theme-color : <?php echo $site_theme_color ?>;
--foreground-color : <?php echo ((isset($site_foreground_color)) ? $$site_foreground_color : '#F1F0F1') ?>;
}
<?php
    exit();