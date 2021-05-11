<?php

    if($searchQuery=='') {
        header("Location: /domain-name-registration/", TRUE, 301);
        exit();
    }

    require_once 'header.php';

?>

<section class="domain-search-box l-w1 center" >
    <div class="container">
        <form id="domainSearch" >
            <input type="hidden" name="type" value="domain" >
            <input type="text" id="domain" name="q" class="domain-search" value="<?php echo (isset($searchQuery) ? $searchQuery : '' ) ?>" placeholder="Search your domain..." required >
            <input type="submit" id="domainSearch" class="domain-search-btn" value="Search Domain" >
        </form>
        <h1 class="l-w-1 center" >Domain search result</h1>
    </div>
</section>
<section class="show-domain-result l-w1" >
    <div class="container">
        <div class="result-loader center">
            <img src="<?php echo IMG_LIBRARY ?>loader.gif" alt="">
        </div>
        <div class="domain-result l-w1">
            <div class="domain-result-box">
                <div class="mob-hide">
                    <div class="l-w2 l-m-w1">
                        <p>DomainName<strong>.in</strong></p>
                    </div>
                    <div class="l-w2 l-m-w1 fr">
                        <div class="l-w2 l-m-w1">
                            <p class="domain-price" ><strike>₹ 999</strike> <strong>₹ 799</strong><span class="fr" >Renewal ₹ 899</span></p>
                        </div>
                        <div class="l-w2 l-m-w1">
                            <button href="#" class="add-to-cart" ><i class="fi-rr-shopping-cart-add"></i> Add to Cart</button>
                        </div>
                    </div>
                </div>
                <div class="mob-show">
                    <div class="l-m-w7">
                        <p>DomainName<strong>.in</strong></p>
                        <p class="domain-price fl mob-show" ><strong>₹ 799</strong> <strike>₹ 999</strike></p>
                    </div>
                    <div class="l-m-w3">
                        <button href="#" class="add-to-cart" ><i class="fi-rr-shopping-cart-add"></i></button>
                    </div>
                </div>
            </div>
            <div class="domain-result-box">
                <div class="mob-hide">
                    <div class="l-w2 l-m-w1">
                        <p>DomainName<strong>.in</strong></p>
                    </div>
                    <div class="l-w2 l-m-w1 fr">
                        <div class="l-w2 l-m-w1">
                            <p class="domain-price" ><strike>₹ 999</strike> <strong>₹ 799</strong><span class="fr" >Renewal ₹ 899</span></p>
                        </div>
                        <div class="l-w2 l-m-w1">
                            <button href="#" class="add-to-cart" disabled ><i class="fi-rr-ban"></i> Not Available</button>
                        </div>
                    </div>
                </div>
                <div class="mob-show">
                    <div class="l-m-w7">
                        <p>DomainName<strong>.in</strong></p>
                        <p class="domain-price fl mob-show" ><strong>₹ 799</strong> <strike>₹ 999</strike></p>
                    </div>
                    <div class="l-m-w3">
                        <button href="#" class="add-to-cart" disabled ><i class="fi-rr-ban"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

    require_once 'footer.php';

?>