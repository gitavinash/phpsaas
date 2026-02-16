<?php

http_response_code(404);

require_once 'header.php';

?>
<section class="thankyou_wrapper" style="margin-bottom: 200px;">
    <div class="container">
        <div class="thankyou_wrapper__content">
            <h1>Page not Found</h1>
        </div>
    </div>
</section>
<script>
setTimeout(function() {
    window.location.replace('/');
}, 3000);
</script>
<?php

require_once 'footer.php';

?>