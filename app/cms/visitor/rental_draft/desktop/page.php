<?php

require_once 'header.php';

?>

<!-- FAQ's -->

<div class="container">
    <div class="row">
        <div class="col">
            <section class="faqSection dir-left">
                <h2 class="text-center"><?php echo $page_title ?><br /></h2>
                <div class="accordion" id="faqAccordion">
                    <?php echo $page_description ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>