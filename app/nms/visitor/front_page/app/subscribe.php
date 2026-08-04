<?php

if (SITE_ID == 234) {
    $redirectURL = HTTP_PROTOCOL . MAIN_DOMAIN . '/manage-subscription/';
    header("Location: {$redirectURL}", true, 301);
    exit;
}

if (DEVELOPMENT_MODE == false) {
    $fetchIPData = file_get_contents("http://ip-api.com/json/" . ((IP == '127.0.0.1') ? '110.235.235.20' : IP) . "?fields=status,message,continent,country,regionName,city,zip,lat,lon,isp,org");

    extract(json_decode($fetchIPData, TRUE));
} else {
    $country = 'india';
}

require_once 'header.php';

if ($activeSubscription == true) {
    if ($subscription_mode == 'year') { ?>
        <script>
            window.location.href = '/manage-subscription/';
        </script>
<?php
    }
}

?>
<div id="view-subscribe" class="page-view active">
    <div class="page-header">
        <h1 class="greeting-title">Subscribe</h1>
    </div>

    <div class="subscribe-hero">
        <h2 class="subscribe-title">Stay ahead in fintech leadership</h2>
        <p class="subscribe-subtitle">Access premium journalism and ecosystem mastery.</p>
    </div>

    <div class="pricing-cards">
        <?php
        $subscription_relation_id = ((isset($subscription_relation_id)) ? $subscription_relation_id : 0);

        if ($subsriptionPlan = mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price, link_is_featured FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id AND link_status=200 AND link_site_id=" . SITE_ID . " AND link_type='product' INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_category='" . ((strtolower($country) == 'india') ? '231' : '291') . "' AND product_type='subscription' AND product_site_id=" . SITE_ID . " ORDER BY inventory_price DESC")) {
            if (mysqli_num_rows($subsriptionPlan) != 0) {
                $planIndex = 0;
                while ($subsriptionPlanData = mysqli_fetch_assoc($subsriptionPlan)) {
                    extract($subsriptionPlanData);
                    if ($product_id != $subscription_relation_id) {
                        $planIndex++;

                        $priceFormatted = number_format((float)$inventory_price, 2, '.', '');
                        $priceParts = explode('.', $priceFormatted);
                        $priceMain = $priceParts[0];
                        $priceDec = isset($priceParts[1]) ? '.' . $priceParts[1] : '.00';
                        $hasMrp = (isset($inventory_mrp) && (float)$inventory_mrp > (float)$inventory_price);
                        $savingsPercent = $hasMrp ? round((((float)$inventory_mrp - (float)$inventory_price) / (float)$inventory_mrp) * 100) : 0;
        ?>
                        <div class="pricing-card native-card <?php echo $link_is_featured ? 'featured' : ''; ?>">
                            <?php if ($link_is_featured) { ?>
                                <div class="pricing-badge">Most Popular</div>
                            <?php } ?>

                            <div class="pricing-header">
                                <h3><?php echo htmlspecialchars($product_title); ?></h3>
                                <h2 class="price">
                                    <?php echo htmlspecialchars($inventory_currency_symbol); ?> <?php echo htmlspecialchars($priceMain); ?><span><?php echo htmlspecialchars($priceDec); ?></span>
                                </h2>
                                <?php if ($hasMrp) { ?>
                                    <div class="price-mrp-row">
                                        <span class="mrp-price"><?php echo htmlspecialchars($inventory_currency_symbol) . htmlspecialchars(number_format((float)$inventory_mrp, 0)); ?></span>
                                        <?php if ($savingsPercent > 0) { ?>
                                            <span class="discount-badge"><?php echo $savingsPercent; ?>% OFF</span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <ul class="pricing-features">
                                <li><i class="ph ph-check-circle"></i> Unlimited access to all premium stories & archives</li>
                                <li><i class="ph ph-check-circle"></i> Concise explainers & ecosystem analysis</li>
                            </ul>

                            <div class="continue">
                                <?php
                                if (isset($_COOKIE['user_data'])) {
                                    echo "
                                        <form action='/subscription/' method='post'>
                                            <input type='hidden' name='id' value='{$product_id}'>
                                            <input type='submit' class='auth-btn primary-btn " . ($link_is_featured ? "" : "outline") . "' value='Continue'>
                                        </form>
                                    ";
                                } else {
                                    echo "<a href='/login/?duration=monthly' class='auth-btn primary-btn " . ($link_is_featured ? "" : "outline") . "'>Continue</a>";
                                }
                                ?>
                            </div>
                        </div>
        <?php
                    }
                }
            }
        }
        ?>
    </div>

    <div class="trust-banner">
        <div class="trust-item">
            <i class="ph ph-shield-check"></i>
            <span>Secure Checkout</span>
        </div>
        <div class="trust-item">
            <i class="ph ph-lightning"></i>
            <span>Instant Access</span>
        </div>
        <div class="trust-item">
            <i class="ph ph-lock-key"></i>
            <span>Cancel Auto-Renew Anytime</span>
        </div>
    </div>

    <div class="subscription_mail">
        <div class="container">
            <div class="myMail">
                <p>Facing some trouble while purchasing The Head and Tale subscription? Email at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></p>
            </div>
            <div class="faq_wrapper">
                <div class="faq_heading">
                    <h6>Also, please refer to our <span>FAQs (Frequently Asked Questions)</span></h6>
                </div>
                <div class="faq__box-container">
                    <div class="faq__box active">
                        <div class="question">
                            <h3>What do I get if I take the subscription?</h3>
                            <i class='ph ph-caret-down'></i>
                        </div>
                        <div class="answer">
                            <p>As a reader, you can anticipate receiving 3-5 stories per month in a variety of formats. This includes at least 2 long-form articles, concise explainers, analyses, and more.</p>
                        </div>
                    </div>
                    <div class="faq__box">
                        <div class="question">
                            <h3>Can I get a trial before purchasing a subscription plan?</h3>
                            <i class='ph ph-caret-down'></i>
                        </div>
                        <div class="answer">
                            <p>We do not offer trials with any of our subscription plans. However, we periodically publish stories that are free to read. To access these stories, you'll need to sign in to your account.</p>
                        </div>
                    </div>
                    <div class="faq__box">
                        <div class="question">
                            <h3>Do I need to create an account to buy a subscription?</h3>
                            <i class='ph ph-caret-down'></i>
                        </div>
                        <div class="answer">
                            <p>Yes. You need to sign-up or sign-in using your email address or Gmail to purchase The Head and Tale subscription.</p>
                        </div>
                    </div>
                    <div class="faq__box">
                        <div class="question">
                            <h3>Can I cancel my subscription?</h3>
                            <i class='ph ph-caret-down'></i>
                        </div>
                        <div class="answer">
                            <span>We do not offer cancellation and refund once you have purchased the subscription.</span>
                            <p>You can cancel your subscription only if it's set to auto-renew for the next payment cycle. Simply go to your profile, click on 'Manage My Subscription' in the drop-down menu, and disable auto-renewal to stop it from renewing for the next cycle. For further queries, you can connect with us at <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const faqBoxes = document.querySelectorAll(".faq__box");
        faqBoxes.forEach(function(box) {
            const question = box.querySelector(".question");
            if (question) {
                question.addEventListener("click", function() {
                    box.classList.toggle("active");
                });
            }
        });
    });
</script>

<?php

require_once 'footer.php';

?>