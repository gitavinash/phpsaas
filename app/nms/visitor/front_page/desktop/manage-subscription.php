<?php

require_once 'header.php';

if ($activeSubscription == true) {
?>
  <style>
    /* Manage Subscription Page Styles */
    .manage-subscription-wrapper {
      padding-bottom: 80px;
    }

    .manage-sub-container {
      max-width: 840px;
      margin: 0 auto;
    }

    /* Mature News Portal Executive Member Hero Section */
    .member-hero-section {
      background: var(--bg-color, #fdfbf7);
      padding: 32px 0 44px 0;
      border-bottom: 1px solid #e2e8f0;
      margin-bottom: 48px;
    }

    .member-hero-section .article-breadcrumb {
      margin-bottom: 24px;
    }

    .member-hero-card {
      background: var(--white, #ffffff);
      border: 1px solid var(--border-color, #e0e0e0);
      border-radius: 16px;
      box-shadow: 0 12px 35px -10px rgba(0, 0, 0, 0.05), 0 2px 8px rgba(0, 0, 0, 0.02);
      position: relative;
      overflow: hidden;
    }

    .member-hero-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--accent-color);
    }

    .member-hero-grid {
      display: grid;
      grid-template-columns: 1.6fr 1fr;
      gap: 0;
    }

    .member-hero-main {
      padding: 44px 44px 40px 44px;
      border-right: 1px solid #f1f5f9;
    }

    .member-badge-row {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .member-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.9px;
      background: #dcfce7;
      color: #15803d;
      padding: 4px 12px;
      border-radius: 20px;
      border: 1px solid #bbf7d0;
    }

    .member-status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #16a34a;
    }

    .member-portal-kicker {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748b;
    }

    .member-hero-title {
      font-family: var(--font-heading, "Playfair Display", Georgia, serif);
      font-size: 2.35rem;
      font-weight: 700;
      color: var(--primary-color, #0a0a0a);
      margin-bottom: 12px;
      line-height: 1.18;
      letter-spacing: -0.4px;
    }

    .member-hero-subtitle {
      font-size: 1rem;
      color: var(--text-secondary, #555555);
      line-height: 1.6;
      margin-bottom: 32px;
      max-width: 560px;
    }

    .member-info-pills {
      display: flex;
      gap: 28px;
      flex-wrap: wrap;
      padding-top: 20px;
      border-top: 1px dashed #e2e8f0;
    }

    .info-pill-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .info-pill-label {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #64748b;
    }

    .info-pill-val {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--primary-color, #0a0a0a);
    }

    .info-pill-val.highlight-active {
      color: #10b981;
    }

    .member-hero-side {
      background: #f8fafc;
      padding: 44px 36px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .member-quick-box {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .quick-box-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      color: #10b981;
      margin-bottom: 16px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .quick-box-title {
      font-family: var(--font-body, "Inter", sans-serif);
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-color, #0a0a0a);
      margin-bottom: 6px;
    }

    .quick-box-desc {
      font-size: 0.88rem;
      color: #64748b;
      line-height: 1.5;
      margin-bottom: 20px;
    }

    .member-upgrade-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      justify-content: center;
      padding: 12px 18px;
      background: var(--primary-color, #0a0a0a);
      color: var(--white, #ffffff) !important;
      font-weight: 700;
      font-size: 0.88rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
      margin-bottom: 10px;
      box-sizing: border-box;
    }

    .member-upgrade-btn:hover {
      background: var(--accent-color, #d93025);
      transform: translateY(-1px);
    }

    .member-support-link {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-color, #d93025);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: color 0.2s ease;
    }

    .member-support-link:hover {
      color: var(--primary-color, #0a0a0a);
      text-decoration: underline;
    }

    /* FAQ Accordion Styling */
    .subscribe-faq-section {
      max-width: 840px;
      margin: 0 auto;
    }

    .subscribe-faq-title {
      font-family: var(--font-heading, "Playfair Display", Georgia, serif);
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color, #0a0a0a);
      margin-bottom: 28px;
      text-align: center;
    }

    .subscribe-faq-item {
      background: var(--white, #ffffff);
      border: 1px solid var(--border-color, #e0e0e0);
      border-radius: 12px;
      margin-bottom: 14px;
      overflow: hidden;
      transition: all 0.25s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .subscribe-faq-item:hover {
      border-color: #cbd5e1;
    }

    .subscribe-faq-item.active {
      border-color: var(--primary-color, #0a0a0a);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .subscribe-faq-question {
      padding: 22px 28px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: var(--font-body, "Inter", sans-serif);
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--primary-color, #0a0a0a);
      user-select: none;
    }

    .subscribe-faq-icon {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      font-weight: 600;
      color: #475569;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s ease, color 0.3s ease;
      flex-shrink: 0;
    }

    .subscribe-faq-item.active .subscribe-faq-icon {
      transform: rotate(45deg);
      background: var(--primary-color, #0a0a0a);
      color: #ffffff;
    }

    .subscribe-faq-answer {
      max-height: 0;
      overflow: hidden;
      padding: 0 28px;
      font-size: 0.95rem;
      color: var(--text-secondary, #555555);
      line-height: 1.65;
      transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), padding 0.35s ease;
    }

    .subscribe-faq-item.active .subscribe-faq-answer {
      max-height: 500px;
      padding: 0 28px 24px 28px;
    }

    @media (max-width: 860px) {
      .member-hero-grid {
        grid-template-columns: 1fr;
      }

      .member-hero-main {
        border-right: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 32px 24px;
      }

      .member-hero-side {
        padding: 32px 24px;
      }

      .subscribe-faq-question {
        padding: 18px 20px;
        font-size: 0.98rem;
      }

      .subscribe-faq-answer {
        padding: 0 20px;
      }

      .subscribe-faq-item.active .subscribe-faq-answer {
        padding: 0 20px 20px 20px;
      }
    }
  </style>

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
                  <span class="info-pill-val highlight-active">Unlimited &bull; Premium</span>
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