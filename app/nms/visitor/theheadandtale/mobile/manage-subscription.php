<?php

    require_once 'header.php';

    if($activeSubscription==true) {
        echo "<section class='manage_container'>
            <div class='container'>
                <h3>Manage My Subscription</h3>
                <div class='manage_container--wrapper'>
                    <div class='manage_plane__box'>
                        <h6>Your current plan</h6>
                        <p>{${$subscription_type.'_title'}} <span> (Expires on {$expiryDate})</span></p>
                        ".(($subscription_mode=='month') ? "<div class='upgrade--plan'>
                            <a href='/subscribe/' style='text-decoration: none' ><mark>Upgrade my plan to Annual</mark></a>
                        </div>" : "")."
                    </div>
                    <div class='cancel__subscription'>
                        <a href='mailto:info@theheadandtale.com' style='text-decoration: none' ><p>Cancel my subscription</p></a>
                    </div>
                </div>
            </div>
        </section>";
    } else { ?>
<script>
window.location.href = '/';
</script>
<?php
    }

?>

<div class="subscription_mail">
    <div class="container">
        <div class="faq_wrapper">
            <div class="faq_heading">
                <h6>Also, please refer to our <span> FAQs (Frequently Asked Questions)</span></h6>
            </div>
            <div class="faq__box-container">
                <div class="faq__box">
                    <div class="question">
                        <h3>What do I get if I take the subscription? </h3>
                        <i class='bx bx-chevron-down'></i>
                    </div>
                    <div class="answer">
                        <p>As a reader, you can anticipate receiving 3-5 stories per month in a variety of formats.
                            This includes at least 2 long-form articles, concise explainers, analyses, and more.</p>
                    </div>
                </div>
                <div class="faq__box">
                    <div class="question">
                        <h3>Can I get a trial before purchasing a subscription plan? </h3>
                        <i class='bx bx-chevron-down'></i>
                    </div>
                    <div class="answer">
                        <p>We do not offer trials with any of our subscription plans. However, we periodically
                            publish stories that are free to read. To access these stories, you'll need to sign in
                            to your account.</p>
                    </div>
                </div>
                <div class="faq__box">
                    <div class="question">
                        <h3>Do I need to create an account to buy a subscription? </h3>
                        <i class='bx bx-chevron-down'></i>
                    </div>
                    <div class="answer">
                        <p>Yes. You need to sign-up or sign-in using your email address or Gmail to purchase The
                            Head and Tale subscription.</p>
                    </div>
                </div>
                <div class="faq__box">
                    <div class="question">
                        <h3>Can I cancel my subscription? </h3>
                        <i class='bx bx-chevron-down'></i>
                    </div>
                    <div class="answer">
                        <span>We do not offer cancellation and refund once you have purchased the subscription.
                        </span>
                        <p>You can cancel your subscription only if it's set to auto-renew for the next payment
                            cycle. Simply go to your profile, click on 'Manage My Subscription' in the drop-down
                            menu, and disable auto-renewal to stop it from renewing for the next cycle. For further
                            queries, you can connect with us at <a
                                href="mailto:info@theheadandtale.com">info@theheadandtale.com</a> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

    require_once 'footer.php';

?>