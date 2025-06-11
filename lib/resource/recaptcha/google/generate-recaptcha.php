<script src="https://www.google.com/recaptcha/api.js?render=<?php echo GOOGLE_RECAPTCHA_SITE_KEY ?>"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('<?php echo GOOGLE_RECAPTCHA_SITE_KEY ?>', {
        action: 'submit'
    }).then(function(token) {
        // Update every recaptcha_token input in all forms
        document.querySelectorAll('input[name="recaptcha_token"]').forEach(function(input) {
            input.value = token;
        });
    });
});
</script>