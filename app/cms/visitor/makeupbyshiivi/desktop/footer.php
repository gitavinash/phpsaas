<!-- Footer -->
<footer class="mbs-footer-section text-center">
    <div class="container">
        <!-- Social Link -->
        <div class="row">
            <div class="col-md-12 text-center mb-20">
                <div class="text-center">
                    <ul class="mbs-social-link">
                        <!-- <li><a href="/#"><i class="fab fa-facebook"></i></a></li>
                            <li><a href="/#"><i class="fab fa-twitter"></i></a></li> -->
                        <li><a href="https://instagram.com/shiivani_.gupta"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Logo -->
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="brand"><img src="<?php echo USER_IMAGES ?>favicon.png" alt="Makeup by Shiivi"></div>
            </div>
        </div>
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-12">
                <p class="copright"><?php echo date('Y') ?> &copy; <?php echo SITE_NAME ?>. All rights reserved.</p>
                <p class="copright">Made with <i class="fa fa-heart"></i> by <a href="http://www.blumox.com" title="Software development company in delhi" style="color: #fff">Blumox Technologies</a> in India </p>
            </div>
        </div>
    </div>
</footer>
<!-- JS -->
<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>owlcarousel2/owlcarousel.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>imagesloaded/imagesloaded.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>isotope/isotope.min.js"></script>
<script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>