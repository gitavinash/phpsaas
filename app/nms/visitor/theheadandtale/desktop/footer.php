<?php

if (ACTIVE_URL != '/thankyou_newsletter/') {
    echo "<section class='newsletter_wrapper'>
    <div class='container container--box'>
        <div class='row'>
            <div class='newsletter'>
                <form action='/thankyou_newsletter/'>
                    <h3>Sign up for concise summaries of powerful fintech insights delivered straight to your
                        inbox every time we publish.</h3>
                    <div class='input_btn'>
                        <input type='text' class='input newsletter_input' id='newsletterEmail' placeholder='Enter your email to subscribe'>
                        <button class='newsletter_btn' id='submitNewsletter' ><i class='bx bxs-paper-plane'></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>";
}

?>
</main>
<footer>
    <div class="footer_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="footer_left">
                        <div class="footer_logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" alt="logo"></a>
                            <p>Dive into the dynamic realm of fintech with firsthand insights and balanced
                                journalism at The Head and Tale.</p>
                        </div>
                        <div class="social_links">
                            <h3>Follow us on</h3>
                            <a href="https://twitter.com/theheadandtale"><img src="<?php echo USER_IMAGES ?>social_media/twitter.png" alt="twitter"></a>
                            <a href="https://www.linkedin.com/company/theheadandtale/"><img src="<?php echo USER_IMAGES ?>social_media/linkedin.png" alt="linkden"></a>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="footer_right">
                        <div class="page_link">
                            <a href="/about-us/">about us</a>
                            <a href="/terms-and-conditions/">terms and conditions</a>
                            <a href="/privacy-policy/">privacy policy</a>
                            <a href="/refund-policy/">refund policy</a>
                            <a href="/subscribe/" class="subscribe_btn">subscribe</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="copyright">
                <p>Copyright <?php echo date('Y') ?> @ <?php echo SITE_NAME ?></p>
                <p class="right">Design and Developed by <a href="https://blumox.com" title="News website development company in new delhi">Blumox
                        Technologies</a></p>
            </div>
        </div>
    </div>
</footer>

<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
<script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>