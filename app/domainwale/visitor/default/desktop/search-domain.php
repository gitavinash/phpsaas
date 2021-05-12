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
            <input type="submit" id="domainSearchBtn" class="domain-search-btn" value="Search Domain" >
            <h1 class="l-w-1 center" >Domain search result</h1>
        </form>
    </div>
</section>
<section class="show-domain-result l-w1" >
    <div class="container">
        <div class="result-loader center show">
            <img src="<?php echo IMG_LIBRARY ?>loader.gif" alt="">
        </div>
        <div class="domain-result l-w1 hide" >
            <!-- Result will be visible here after fecthing data from api -->
        </div>
    </div>
</section>

<?php

    require_once 'footer.php';

?>