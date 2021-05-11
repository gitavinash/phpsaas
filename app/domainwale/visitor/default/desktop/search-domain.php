<?php

    require_once 'header.php';

?>

<section class="domain-search-box l-w1 center" >
    <div class="container">
        <h1>Domain search result</h1>
        <form id="domainSearch" >
            <input type="hidden" name="type" value="domain" >
            <input type="text" id="domain" name="q" class="domain-search" value="<?php echo (isset($searchQuery) ? $searchQuery : '' ) ?>" placeholder="Search your domain..." required >
            <input type="submit" id="domainSeaarch" class="domain-search-btn" value="Search Domain" >
        </form>
    </div>
</section>

<?php

    require_once 'footer.php';

?>