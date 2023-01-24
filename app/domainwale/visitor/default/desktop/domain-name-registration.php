<?php

require_once 'header.php';

?>

<section class="info-section bg-blueies">
    <div class="domain-search-box l-w1 ">
        <div class="container">
            <div class="l-w7 l-m-w1">
                <h1>Unleashed your brand<span>with domain name.</span></h1>
                <form id="domainSearch">
                    <input type="hidden" name="type" value="domain">
                    <input type="text" id="domain" name="q" class="domain-search"
                        value="<?php echo (isset($domain) ? $domain : '') ?>" placeholder="Search your domain..."
                        required>
                    <input type="submit" id="domainSearchBtn" class="domain-search-btn" value="Search Domain">
                </form>
                <p><a href="/domain-transfer/">Transfer your Domain name</a></p>
            </div>
        </div>
    </div>
</section>

<section class="show-domain-result l-w1">
    <div class="container">
        <div class="result-loader center show">
            <img src="<?php echo IMG_LIBRARY ?>loader.gif" alt="">
        </div>
        <h1 class="l-w-1 center">Domain search result</h1>
        <div class="domain-result l-w1 hide">
            <!-- Result will be visible here after fecthing data from api -->
        </div>
    </div>
</section>

<section class="section">
    <div class="description">
        <div class="l-w2 l-m-w1">
            <img src="<?php echo USER_IMAGES ?>domain_registration_1.jpg" alt="">
        </div>
        <div class="l-w2 l-m-w1">
            <div class="content">
                <div class="box">
                    <h2></h2>
                    <p></p>
                    <a href="" class="btn bg-grey"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="description bg-green">
        <div class="l-w2 l-m-w1 desktop-right">
            <img src="<?php echo USER_IMAGES ?>domain_registration_1.jpg" alt="">
        </div>
        <div class="l-w2 l-m-w1">
            <div class="content">
                <div class="box">
                    <h2></h2>
                    <p></p>
                    <a href="" class="btn bg-grey"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="description">
        <div class="l-w2 l-m-w1">
            <img src="<?php echo USER_IMAGES ?>domain_registration_1.jpg" alt="">
        </div>
        <div class="l-w2 l-m-w1">
            <div class="content">
                <div class="box">
                    <h2></h2>
                    <p></p>
                    <a href="" class="btn bg-grey"></a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>