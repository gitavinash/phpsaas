<h2 class="heading">
    <?php echo ucwords(str_replace('_', ' ', $postType)); ?>
    <span>Quick overview of the <?php echo ucwords(str_replace('_', ' ', $postType)); ?> section</span>
</h2>

<div class="main-menu-list">
    <div class="widget box-2-5">
        <a href="inventory/list/" aria-label="All <?php echo ((SITE_APP == 'ims_marble') ? 'Blocks' : 'Products') ?><">
            <div class="widget-setting-info">
                <div class="feature-icon" aria-hidden="true">
                    <i class="fi-rr-apps-sort"></i>
                </div>
                <div class="about-feature">
                    <h3>All <?php echo ((SITE_APP == 'ims_marble') ? 'Blocks' : 'Products') ?></h3>
                    <!-- <p>this feature is used</p> -->
                </div>
            </div>
        </a>
    </div>

    <div class="widget box-2-5">
        <a href="inventory/add/" aria-label="Add new <?php echo ((SITE_APP == 'ims_marble') ? 'Block' : 'Product') ?>">
            <div class="widget-setting-info">
                <div class="feature-icon" aria-hidden="true">
                    <i class="fi-rr-add"></i>
                </div>
                <div class="about-feature">
                    <h3>Add new <?php echo ((SITE_APP == 'ims_marble') ? 'Block' : 'Product') ?></h3>
                </div>
            </div>
        </a>
    </div>

    <?php if (SITE_APP == 'batchno') { ?>
    <div class="widget box-2-5">
        <a href="inventory/category/" aria-label="Product Category">
            <div class="widget-setting-info">
                <div class="feature-icon" aria-hidden="true">
                    <i class="fi-rr-category-alt"></i>
                </div>
                <div class="about-feature">
                    <h3>Product Category</h3>
                </div>
            </div>
        </a>
    </div>
    <?php } ?>
</div>