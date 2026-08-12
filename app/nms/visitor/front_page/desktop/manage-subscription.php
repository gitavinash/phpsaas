<?php

require_once 'header.php';

if ($activeSubscription == true) {
?>


  <section class="manage-subscription-wrapper">

    <!-- Mature News Portal Executive Member Hero Section -->
    <div class="member-hero-section">
      <div class="container">

        <nav class="article-breadcrumb" aria-label="Breadcrumb">
          <a href="/">Home</a>
          <span class="sep">/</span>
          <span class="current">Member Dashboard</span>
        </nav>

        <div class="member-hero-card">
          <div class="member-hero-grid">

            <!-- Left Side: Editorial Greeting & Member Details -->
            <div class="member-hero-main">
              <div class="member-badge-row">
                <span class="member-status-badge">
                  <span class="member-status-dot"></span> Active Subscriber
                </span>
                <span class="member-portal-kicker">Executive Reader Edition</span>
              </div>

              <h1 class="member-hero-title">Subscriber Dashboard</h1>
              <p class="member-hero-subtitle">
                Welcome to your <strong><?php echo SITE_NAME ?></strong>. Your active subscription grants full, access to all executive briefings, analyses, and deep-dive reporting.
              </p>

              <div class="member-info-pills">
                <div class="info-pill-item">
                  <span class="info-pill-label">Active Plan</span>
                  <span class="info-pill-val"><?php echo htmlspecialchars(${$subscription_type . '_title'} ?? 'Executive Subscription'); ?></span>
                </div>

                <div class="info-pill-item">
                  <span class="info-pill-label">Renewal / Expiry</span>
                  <span class="info-pill-val"><?php echo date('j F Y', strtotime($subscription_expiry)); ?></span>
                </div>

                <div class="info-pill-item">
                  <span class="info-pill-label">Reader Access</span>
                  <span class="info-pill-val highlight-active">Premium</span>
                </div>
              </div>
            </div>

            <!-- Right Side: Executive Action Box -->
            <div class="member-hero-side">
              <div class="member-quick-box">
                <div class="quick-box-icon">
                  <i class="ph ph-shield-check"></i>
                </div>
                <h3 class="quick-box-title">Account Status</h3>
                <p class="quick-box-desc">Your membership is active and verified in our publication system.</p>

                <?php if (isset($subscription_mode) && $subscription_mode == 'month'): ?>
                  <a href="/subscribe/" class="member-upgrade-btn">
                    <i class="ph ph-sparkle"></i> Upgrade to Annual Plan &rarr;
                  </a>
                <?php endif; ?>

                <a href="mailto:info@theheadandtale.com" class="member-support-link">
                  <i class="ph ph-envelope-simple"></i> Member Support / Cancel &rarr;
                </a>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- FAQ Section matching theme -->
    <div class="container">
      <div class="manage-sub-container">
        <div class="subscribe-faq-section">
          <h2 class="subscribe-faq-title">Frequently Asked Questions</h2>
          <div class="subscribe-faq-list">

            <div class="subscribe-faq-item">
              <div class="subscribe-faq-question">
                <span>What do I get with my subscription?</span>
                <span class="subscribe-faq-icon">+</span>
              </div>
              <div class="subscribe-faq-answer">
                <p>As a subscriber, you receive full unlimited access to 3-5 premium stories per month in a variety of formats, including in-depth long-form articles, concise explainers, startup ecosystem reports, and executive briefings.</p>
              </div>
            </div>

            <div class="subscribe-faq-item">
              <div class="subscribe-faq-question">
                <span>Can I get a trial before purchasing a subscription plan?</span>
                <span class="subscribe-faq-icon">+</span>
              </div>
              <div class="subscribe-faq-answer">
                <p>We do not offer trial periods for our subscription plans. However, we periodically publish free-to-read stories. Simply sign in to your account to read them.</p>
              </div>
            </div>

            <div class="subscribe-faq-item">
              <div class="subscribe-faq-question">
                <span>Do I need an account to buy a subscription?</span>
                <span class="subscribe-faq-icon">+</span>
              </div>
              <div class="subscribe-faq-answer">
                <p>Yes, you need to sign in or create an account using your email address to purchase and manage your subscription.</p>
              </div>
            </div>

            <div class="subscribe-faq-item">
              <div class="subscribe-faq-question">
                <span>How do I cancel or request changes to my subscription?</span>
                <span class="subscribe-faq-icon">+</span>
              </div>
              <div class="subscribe-faq-answer">
                <p>You can manage or cancel auto-renewal before your next billing cycle. If you need assistance with your subscription or billing queries, connect with us anytime at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a>.</p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const faqQuestions = document.querySelectorAll('.subscribe-faq-question');
      faqQuestions.forEach(function(question) {
        question.addEventListener('click', function() {
          const item = this.closest('.subscribe-faq-item');
          const isActive = item.classList.contains('active');

          // Close all open items
          document.querySelectorAll('.subscribe-faq-item').forEach(function(el) {
            el.classList.remove('active');
            const ans = el.querySelector('.subscribe-faq-answer');
            if (ans) {
              ans.style.maxHeight = null;
            }
          });

          // Toggle current item if it was not active
          if (!isActive) {
            item.classList.add('active');
            const ans = item.querySelector('.subscribe-faq-answer');
            if (ans) {
              ans.style.maxHeight = ans.scrollHeight + 40 + 'px';
            }
          }
        });
      });
    });
  </script>
<?php
} else { ?>
  <script>
    window.location.href = '/subscribe/';
  </script>
<?php
}

require_once 'footer.php';

?>