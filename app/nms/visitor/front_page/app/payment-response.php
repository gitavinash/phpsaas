<?php

require_once 'header.php';

?>

<div class="status-page-wrapper">
    <div class="container">
        <div class="status-card">
            <div class="status-icon-wrapper info">
                <i class="ph ph-code"></i>
            </div>
            <h1 class="status-title">Payment Response</h1>
            <div class="code-response-box">
                <pre class="code-block"><?php print_r($_REQUEST); ?></pre>
            </div>
            <div class="status-actions">
                <a href="/" class="auth-btn primary-btn outline">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>