<?php

http_response_code(404);

require_once 'header.php';

// Schedule redirect after 3 seconds
header("Refresh: 3; url=/");

// Optionally, set 301 redirect intent (Google still sees 404 but browser refreshes)
header("Status: 301 Moved Permanently");

?>
<section class="thankyou_wrapper" style="margin-bottom: 200px;">
    <div class="container">
        <div class="thankyou_wrapper__content">
            <h1>Page not Found</h1>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>