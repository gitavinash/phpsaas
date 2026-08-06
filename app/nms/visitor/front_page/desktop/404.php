<?php

http_response_code(404);
require_once 'header.php';

?>
<section class="not-found-page">
  <div class="container">
    <div class="not-found-card">
      <h1>404</h1>
      <p class="paywall-subdeck" style="margin-bottom: 24px;">The story or page you are looking for may have been moved, renamed, or is temporarily unavailable.</p>
      <div class="not-found-actions">
        <a href="/" class="btn btn-primary">Return to Homepage</a>
        <a href="#" class="btn btn-outline-secondary open-search-trigger">Search Articles</a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>