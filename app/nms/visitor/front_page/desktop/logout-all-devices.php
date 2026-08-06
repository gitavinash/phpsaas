<?php

require_once LOGIN_CHECK;

if (mysqli_query(
    $connection,
    "DELETE FROM " . TABLE_PREFIX . "user_sessions
     WHERE us_user_id='" . USER_ID . "'
     AND us_site_id='" . SITE_ID . "'"
)) {
    header('Location: /logout/');
    exit;
}

require_once 'header.php';
?>
<section class="not-found-page">
  <div class="container">
    <div class="not-found-card">
      <h1>Notice</h1>
      <p class="paywall-subdeck" style="margin-bottom: 24px;">Unable to log out from all devices at this time. Please try again or contact support.</p>
      <div class="not-found-actions">
        <a href="/manage-subscription/" class="btn btn-primary">Go to Account Settings</a>
        <a href="/" class="btn btn-outline-secondary">Return to Homepage</a>
      </div>
    </div>
  </div>
</section>
<?php require_once 'footer.php'; ?>