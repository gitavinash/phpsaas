        <a href="" class="whatsapp"><img src="<?php echo USER_IMAGES ?>whatsapp_white.png" alt=""></a>
        <?php
        
            require_once NOTIFICATION.'index.php';

        ?>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="footer-section col-3 about">
                        <img src="<?php echo USER_IMAGES ?>logo.png" class="footerLogo"
                            alt="<?php echo SITE_NAME ?> logo" title="<?php echo SITE_NAME ?> logo">
                        <p>Your trusted partner for hassle-free visa processing.</p>
                    </div>
                    <div class="footer-section col-3 links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Visa Services</a></li>
                            <li><a href="#">How It Works</a></li>
                            <li><a href="#">Testimonials</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="footer-section col-3 destinations">
                        <h3>Top Countries</h3>
                        <ul>
                            <li><a href="#">Dubai</a></li>
                            <li><a href="#">Schengen Countries</a></li>
                            <li><a href="#">Thailand</a></li>
                            <li><a href="#">UK & USA</a></li>
                            <li><a href="#">Canada & Australia</a></li>
                        </ul>
                    </div>
                    <div class="footer-section col-3 contact">
                        <h3>Contact Us</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Your Address Here</p>
                        <p><i class="fas fa-envelope"></i> contact@yourwebsite.com</p>
                        <p><i class="fas fa-phone"></i> +91 123 456 7890</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p class="float-left">© <?php echo date('Y').' '.SITE_NAME  ?>. All rights reserved.</p>
                    <p class="float-right">Developed by <a href="#">Blumox Technologies</a></p>
                </div>
            </div>
        </footer>


        <script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
        <script src="<?php echo USER_JS ?>app.min.js" type="text/javascript"></script>
        </body>

        </html>