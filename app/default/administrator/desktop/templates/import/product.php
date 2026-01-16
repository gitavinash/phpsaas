<h2>Import product</h2>

<div class="box-2">
    <div class="form-box">
        <form method="POST" id="importData" enctype="multipart/form-data">

            <div id="importFromBox" class="form-group">
                <label>Step 1</label>
                <select name="importFrom" id='importFrom' class='form-control inside-shadow' required>
                    <option value="">Import from</option>
                    <!-- <option value="wordpress">WordPress</option>
                    <option value="etsy">Etsy</option> -->
                    <option value="csv">CSV</option>
                    <option value="meesho">Meesho</option>
                    <option value="amazon">Amazon</option>
                </select>
                <label style="margin-top: 10px;">Category</label>
                <select name="categoryID" id="categoryID" class='form-control inside-shadow' required>
                    <?php

                    if ($categoryData = mysqli_query($connection, "SELECT link_url, category_id, category_title FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id AND link_type='category' AND link_site_id=" . SITE_ID . " WHERE category_site_id=" . SITE_ID . " AND category_status='1' ORDER BY category_title ASC")) {
                        if (mysqli_num_rows($categoryData) > 0) {
                            while ($categoryRow = mysqli_fetch_array($categoryData)) {
                                echo "<option value='" . $categoryRow['category_id'] . '_' . $categoryRow['link_url'] . "'>" . $categoryRow['category_title'] . "</option>";
                            }
                        }
                    }

                    ?>
                </select>
            </div>

            <div id='importFileBox' style="display:none" class='import-box'>
                <label style="float: left; margin-bottom: 10px">Step 2</label>
                <div class='import-file-box'>
                    <div class="form-group">
                        <label>Upload product csv file</label>
                        <input type="file" id='importFile' name='importFile' class='form-control inside-shadow'
                            required>
                        <label><a
                                href="https://docs.google.com/spreadsheets/d/1w2-uVc5hcGcqg4SwIoxVm9VhfY8LwD908HqfHrBd2NE/edit?usp=sharing"
                                target="_blank"><span>Click
                                    here to download
                                    sample product import file</span></a></label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="importBtnBox" style="display:none">
                <input type="hidden" name="type" value="import">
                <input type="hidden" name="action" value="product">
                <button id="importDataBtn" class="btn btn-green">Import Data</button>
            </div>

    </div>
    </form>
</div>

<div class="please-wait" style='text-align: center; display: none; margin: 100px 0px;'>
    <img src="<?php echo IMG_LIBRARY ?>circle-dot-loader.gif" alt="">
    <h1>Please wait...</h1>
</div>

<div id='status'>
    <span id="success" style='float: left;width: 100%;font-size: 20px;'></span>
    <span id="dublicate" style='float: left;width: 100%;font-size: 20px;'></span>
    <span id="failed" style='float: left;width: 100%;font-size: 20px;'></span>
</div>

</div>