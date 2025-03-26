<?php

require_once 'header.php';

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner">
                    <div class="inner_content">
                        <p style="color: #E7B05D;"><?php echo urldecode($_GET['msg']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>