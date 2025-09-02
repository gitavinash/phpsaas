<h2 class="heading"><?php echo ucwords(str_replace('_', ' ', $postType)) ?> <span>Single line intro about this
        section</span></h2>
<div class="main-menu-list">
    <div class="widget box-2-5">
        <a href="inventory/list/">
            <div class="widget-setting-info">
                <div class="feature-icon">
                    <i class="fi-rr-apps-sort"></i>
                </div>
                <div class="about-feature">
                    <h3>All blocks</h3>
                    <!-- <p>this feature is used</p> -->
                </div>
            </div>
        </a>
    </div>
    <div class="widget box-2-5">
        <a href="inventory/add/">
            <div class="widget-setting-info">
                <div class="feature-icon">
                    <i class="fi-rr-add"></i>
                </div>
                <div class="about-feature">
                    <h3>Add new <?php echo ((SITE_APP == 'ims_marble') ? 'Block' : 'Product') ?></h3>
                </div>
            </div>
        </a>
    </div>
    <?php

    if (SITE_APP == 'batchno') { ?>
    <div class="widget box-2-5">
        <a href="inventory/category/">
            <div class="widget-setting-info">
                <div class="feature-icon">
                    <i class="fi-rr-category-alt"></i>
                </div>
                <div class="about-feature">
                    <h3>Product Category</h3>
                </div>
            </div>
        </a>
    </div>
    <?php
    }

    ?>