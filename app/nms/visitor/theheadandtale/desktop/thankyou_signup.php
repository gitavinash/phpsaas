<?php

require_once 'header.php'

?>
<section class="thankyou_wrapper" style="margin-bottom: 200px">
    <div class="container">
        <div class="thankyou_wrapper__content">
            <h1>Thank You for Signing Up</h1>
            <p>We appreciate your support and trust in us. If you have any questions, feedback, or suggestions,
                please don't hesitate to reach out to us at <a href="mailto: info@theheadandtale.com">
                    info@theheadandtale.com</a></p>
            <?php
            if (isset($redirect_url)) {
                echo '<p>This page will automatically redirect in 5 seconds. <a href="' . htmlspecialchars($redirect_url) . '">Click here</a> if you want to redirect now.</p>';
            ?>
                <script>
                    window.setTimeout(function() {

                        // Move to a new location or you can do something else
                        window.location.href = "<?php echo $redirect_url ?>";

                    }, 5000);
                </script>
            <?php
                exit();
            }
            ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>