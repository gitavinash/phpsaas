<div class="legal">
    <p>By continuing you agree to our <a href="https://blumox.com/terms-and-conditions/" target="_blank">Terms
            of Service</a> and <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy
            Policy</a>
    </p>
    <p class="powered-by">Powered by <a href="<?php echo (defined('CREDIT_URL') && CREDIT_URL != '') ? CREDIT_URL : 'https://blumox.com/' ?>" target="_blank" rel="noopener"><?php echo defined('CREDIT') ? CREDIT : 'Blumox Technologies' ?></a></p>
</div>
</div>
</div>
</div>
</div>
<p class="version">
    v <?php echo ALEYA_APP_VERSION ?>
</p>
</div>
<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
<script src="<?php echo ADMIN_JS_LIBRARY ?>app.min.js"></script>
<script src="/app/system/visitor/default/desktop/assets/js/onboarding.js"></script>
<?php require_once PWA . 'footer.php' ?>
</body>

</html>