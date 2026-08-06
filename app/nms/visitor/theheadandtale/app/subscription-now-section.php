<!-- INLINE PAYWALL -->
<div
    class="inline-paywall" ">
                    <h3 style=" font-size: 1.4rem; margin-bottom: 8px">
    Read the full deepdive
    </h3>
    <p style="color: var(--text-muted); margin-bottom: 24px">
        Unlock this article and all premium insights by subscribing
        to The Head and Tale.
    </p>
    <a
        href="/subscribe/"
        class="auth-btn primary-btn"
        style="
                      width: 100%;
                      padding: 16px;
                      border-radius: var(--border-radius-lg);
                      background: var(--accent-primary);
                      color: #fff;
                      font-weight: 600;
                      font-size: 1.05rem;
                      text-align: center;
                    ">
        Subscribe Now
    </a>
    <?php

    $showSignInCTA = true;

    if (isset($_COOKIE['isLogin'])) {
        if ($_COOKIE['isLogin'] == 1) {
            $showSignInCTA = false;
        }
    }

    if ($showSignInCTA == true) { ?>
        <p
            class="login-prompt"
            style="margin-top: 16px; font-size: 0.95rem">
            Already a member?
            <a
                href="/login/"
                style="
                        color: var(--accent-primary);
                        font-weight: 600;
                        display: inline;
                      ">Sign in</a>
        <?php
    }

        ?>
        </p>
</div>