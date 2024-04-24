<?php

    require_once 'header.php';

    if($activeSubscription==true) {
        if($subscription_mode=='year') { ?>
<script>
window.location.href = '/manage-subscription/';
</script>
<?php
        }
    }

?>
<section class="subscribe--wrapper">
    <div class="container">
        <div class="subscribe--wrapper_box">
            <mark>In the realm of journalism, thriving often hinges on subscription models. Subscribing not only
                sustains quality reporting but also fosters a direct connection between readers and the content they
                value. By selecting the optimal subscription plan, you’re not just accessing premium journalism but
                also supporting the vital role of independent media in society.</mark>
        </div>
    </div>
</section>

<div class="subscribe_wrapper subscribe_padding">
    <div class="container">
        <div class="subscribe_wrapper__heading">
            <!-- <h6>Subscribe to read the full story </h6> -->
            <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
            <p>Access premium journalism by selecting a <a href="#"> Subscription Plan</a></p>
        </div>

        <div class="subscription_card">
            <?php
           
           $subscription_relation_id=((isset($subscription_relation_id)) ? $subscription_relation_id : 0);

            if($subsriptionPlan=mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_type='subscription' AND product_site_id=".SITE_ID)) {
                if(mysqli_num_rows($subsriptionPlan)!=0) {
                    while($subsriptionPlanData=mysqli_fetch_assoc($subsriptionPlan)) {
                        extract($subsriptionPlanData);
                            if($product_id!=$subscription_relation_id) { ?>
            <div class="subscription_card__box">
                <p><?php echo $product_title ?></p>
                <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6>
                <div class="continue"> <?php
                                                    
                                                        if(isset($_COOKIE['user_data'])) {
                                                            echo "
                                                                <form action='/subscription/' method='post' >
                                                                    <input type='hidden' name='id' value='{$product_id}'>
                                                                    <input type='submit' class='button' value='continue'>
                                                                </form>
                                                            ";
                                                        } else {
                                                            echo "<a href='/login/?duration=monthly' class='button'>continue</a>";
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
    </div>
</div>
<div class="subscription_mail">
    <div class="container">
        <div class="myMail">
            <p>Facing some trouble while purchasing The Head and Tale subscription? Email at <a
                    href="mailto:info@theheadandtale.com"> info@theheadandtale.com</a></p>
        </div>
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