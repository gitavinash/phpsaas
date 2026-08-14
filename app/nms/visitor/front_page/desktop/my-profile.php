<?php

require_once 'header.php';

// Redirect to login if user is not logged in
if (!isset($_COOKIE['isLogin']) && !isset($_COOKIE['user_data'])) {
  header("Location: /login/?redirect_url=" . urlencode('/my-profile/'));
  exit();
}

$displayName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : '';
$emailAddr   = defined('PROFILE_EMAIL') ? PROFILE_EMAIL : '';
$avatarImg   = defined('PROFILE_IMAGE') ? PROFILE_IMAGE : '';
$initials    = !empty($displayName) ? strtoupper(substr($displayName, 0, 2)) : 'CU';

?>
<main class="my-profile-wrapper">
  <div class="container">

    <!-- Breadcrumb -->
    <nav class="article-breadcrumb" aria-label="Breadcrumb">
      <a href="/">Home</a>
      <span class="sep">/</span>
      <span class="current">My Profile</span>
    </nav>

    <!-- Profile Header Banner Card -->
    <div class="profile-header-card">
      <div class="profile-header-flex">

        <div class="profile-avatar-wrap">
          <?php if (!empty($avatarImg)): ?>
            <img src="<?php echo htmlspecialchars($avatarImg); ?>" alt="<?php echo htmlspecialchars($displayName); ?>" class="profile-avatar-img" />
          <?php else: ?>
            <div class="profile-avatar-initials">
              <?php echo htmlspecialchars($initials); ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="profile-header-details">
          <div class="profile-badge-row">
            <?php if ($activeSubscription): ?>
              <span class="member-status-badge">
                <span class="member-status-dot"></span> Active Subscriber
              </span>
            <?php else: ?>
              <span class="member-status-badge guest-badge">
                Free Reader
              </span>
            <?php endif; ?>
          </div>

          <h1 class="profile-user-name"><?php echo htmlspecialchars($displayName); ?></h1>
          <p class="profile-user-email">
            <i class="ph ph-envelope-simple"></i> <?php echo htmlspecialchars($emailAddr); ?>
          </p>
        </div>

        <div class="profile-header-action">
          <?php if ($activeSubscription): ?>
            <a href="/manage-subscription/" class="btn-profile-primary">
              <i class="ph ph-gear-six"></i> Manage Subscription
            </a>
          <?php else: ?>
            <a href="/subscribe/" class="btn-profile-accent">
              <i class="ph ph-crown"></i> Upgrade to Premium
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>

    <!-- Main Content Layout Grid -->
    <div class="profile-content-grid">

      <!-- Column 1: Personal Profile Details Form -->
      <div class="profile-card">
        <div class="profile-card-header">
          <h3 class="profile-card-title">
            <i class="ph ph-user-circle"></i> Personal Information
          </h3>
          <p class="profile-card-sub">Manage your public display name and account information.</p>
        </div>

        <div class="profile-card-body">
          <form id="updateProfileForm" onsubmit="return false;">

            <!-- Inline Alert Message -->
            <div id="profileAlert" class="auth-notice-banner" style="display: none;">
              <i id="profileAlertIcon" class="ph ph-info"></i>
              <span id="profileAlertText"></span>
            </div>

            <div class="profile-form-group">
              <label for="profileNameInput">Full Name / Display Name</label>
              <input type="text" id="profileNameInput" name="name" class="profile-input" value="<?php echo htmlspecialchars($displayName); ?>" required placeholder="Enter your full name" />
            </div>

            <div class="profile-form-group">
              <label for="profileEmailInput">Email Address</label>
              <div class="input-with-badge">
                <input type="email" id="profileEmailInput" class="profile-input disabled-input" value="<?php echo htmlspecialchars($emailAddr); ?>" readonly disabled />
                <span class="verified-badge"><i class="ph ph-check-circle-fill"></i> Verified</span>
              </div>
              <small class="form-hint">Email address cannot be modified directly. Contact support for assistance.</small>
            </div>

            <div class="profile-form-actions">
              <button type="button" id="saveProfileBtn" class="btn-save-profile">
                Save Changes
              </button>
            </div>

          </form>
        </div>
      </div>

      <!-- Column 2: Subscription Status & Quick Links -->
      <div class="profile-sidebar-stack">

        <!-- Membership Status Card -->
        <div class="profile-card membership-summary-card">
          <div class="profile-card-header">
            <h3 class="profile-card-title">
              <i class="ph ph-credit-card"></i> Membership & Plan
            </h3>
          </div>
          <div class="profile-card-body">
            <?php if ($activeSubscription): ?>
              <div class="plan-status-box active-plan-box">
                <div class="plan-status-header">
                  <div class="plan-icon-tag"><i class="ph-fill ph-crown"></i></div>
                  <div>
                    <h4 class="plan-name"><?php echo htmlspecialchars(${$subscription_type . '_title'} ?? 'Executive Subscription'); ?></h4>
                    <span class="plan-access-tag">Unlimited Access</span>
                  </div>
                </div>
                <div class="plan-details-list">
                  <div class="plan-detail-row">
                    <span>Renewal / Expiry:</span>
                    <strong><?php echo date('j F Y', strtotime($subscription_expiry)); ?></strong>
                  </div>
                  <div class="plan-detail-row">
                    <span>Reader Access:</span>
                    <strong class="text-success">Full Digital Access</strong>
                  </div>
                </div>
                <a href="/manage-subscription/" class="btn-manage-link">
                  Manage Subscription Details &rarr;
                </a>
              </div>
            <?php else: ?>
              <div class="plan-status-box guest-plan-box">
                <div class="guest-plan-header">
                  <i class="ph ph-lock-key"></i>
                  <h4>Free Reader Account</h4>
                </div>
                <p class="guest-plan-desc">Subscribe to unlock deep-dive reporting, exclusive market analysis, and executive briefings.</p>
                <div class="guest-plan-actions">
                  <a href="/subscribe/" class="btn-subscribe-now">
                    <i class="ph ph-sparkle"></i> View Subscription Plans
                  </a>
                  <a href="/corporate-subscription/" class="btn-corporate-link">
                    Corporate Team Seats &rarr;
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Quick Account Navigation Links -->
        <div class="profile-card quick-links-card">
          <div class="profile-card-header">
            <h3 class="profile-card-title">
              <i class="ph ph-squares-four"></i> Account Actions
            </h3>
          </div>
          <div class="profile-card-body">
            <div class="quick-nav-list">
              <a href="/manage-subscription/" class="quick-nav-item">
                <div class="quick-nav-icon"><i class="ph ph-receipt"></i></div>
                <div class="quick-nav-text">
                  <strong>Manage Subscription</strong>
                  <span>View plan details, billing, or options</span>
                </div>
                <i class="ph ph-caret-right"></i>
              </a>

              <a href="/manage-active-session/" class="quick-nav-item">
                <div class="quick-nav-icon"><i class="ph ph-desktop"></i></div>
                <div class="quick-nav-text">
                  <strong>Active Sessions</strong>
                  <span>Manage logged-in devices & security</span>
                </div>
                <i class="ph ph-caret-right"></i>
              </a>

              <a href="/logout/" class="quick-nav-item logout-nav-item">
                <div class="quick-nav-icon"><i class="ph ph-sign-out"></i></div>
                <div class="quick-nav-text">
                  <strong>Sign Out</strong>
                  <span>Log out of your account on this device</span>
                </div>
                <i class="ph ph-caret-right"></i>
              </a>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('saveProfileBtn');
    const nameInput = document.getElementById('profileNameInput');
    const alertBox = document.getElementById('profileAlert');
    const alertText = document.getElementById('profileAlertText');
    const alertIcon = document.getElementById('profileAlertIcon');

    function showAlert(msg, type = 'success') {
      if (!alertBox || !alertText) return;
      alertText.textContent = msg;
      alertBox.className = `auth-notice-banner ${type}`;
      if (alertIcon) {
        alertIcon.className = type === 'error' ? 'ph ph-warning-circle' : 'ph ph-check-circle';
      }
      alertBox.style.display = 'flex';
    }

    if (saveBtn && nameInput) {
      saveBtn.addEventListener('click', async function() {
        const userName = nameInput.value.trim();
        if (!userName) {
          showAlert('Please enter your name.', 'error');
          return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
          const res = await fetch('/api/auth/user-profile-update/', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'name=' + encodeURIComponent(userName)
          });
          const data = await res.json();

          if (data.status === 'success') {
            showAlert('Profile updated successfully!', 'success');
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
            showAlert(data.msg || 'Failed to update profile.', 'error');
          }
        } catch (err) {
          console.error(err);
          saveBtn.disabled = false;
          saveBtn.textContent = 'Save Changes';
          showAlert('Error updating profile. Please try again.', 'error');
        }
      });
    }
  });
</script>

<?php require_once 'footer.php'; ?>