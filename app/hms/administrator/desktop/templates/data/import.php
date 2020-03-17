<h2>Import Website Data</h2>

<div class="box-2">
    <div class="form-box">
	    <form method="POST" id="formData" enctype="multipart/form-data"  >
            <div class="form-group">
                <label>Step 1</label>
                <select id='importData' class='form-control inside-shadow'>
                    <option value=''>Imoprt Data From</option>
                    <option value='cms'>CMS ( Content Management Software )</option>
                    <option value='url'>URL</option>
                    <option value='file'>File</option>
                </select>
            </div>
            <!-- CMS Box -->
            <div id='cms' class='import-box' style='display: none'>
                <div class="form-group">
                    <select class='form-control inside-shadow'>
                        <option value=''>Select Platform*</option>
                        <option value="">WordPress</option>
                        <option value="">Blogger</option>
                        <option value="">Joomla</option>
                        <option value="">Drupal</option>
                    </select>
                </div>
            </div>
            <!-- URL Box -->
            <div id='url' class='import-box' style='display: none'>

            </div>
            <!-- Import from File -->
            <div id='file' class='import-box' style='display: none'>
                <div class="form-group">
                    <label>Step 2</label>
                    <select name="category" id='importCategory' class="form-control inside-shadow" required >
                        <option value="">Select Category*</option>
                        <?php
                            if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_site_id='".SITE_ID."' AND link_type='category' AND link_relation_id=category_id AND link_status=200 ORDER BY category_title ASC")) {
                                while($category=mysqli_fetch_assoc($categoryList)) {
                                    echo "<option value='{$category['category_id']}_".str_replace('/category', '', $category['link_url'])."'>{$category['category_title']}</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class='import-file-box' style='display:none'>
                    <div class="form-group">
                        <label>Step 3</label>
                        <input type="file" id='importFile' name='importFile' class='form-control inside-shadow' required >
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="type" value="import" >
            </div>
        </div>
     </form>
    </div>


    <div class="box-2 right hide-info" style='display: none'>
        <h3>Import data from any of these file formats</h3>
        <ol>
            <li><strong style='float: left; width: 150px'>XML</strong> <a href='' style='color: #00820c; text-decoration: none' >Download Sample File</a></li>
            <li><strong style='float: left; width: 150px'>JSON</strong> <a href='' style='color: #00820c; text-decoration: none' >Download Sample File</a> </li>
            <li><strong style='float: left; width: 150px'>CSV</strong> <a href='' style='color: #00820c; text-decoration: none' >Download Sample File</a> </li>
        </ol>
    </div>

    
    <div class="please-wait" style='text-align: center; display: none; margin: 100px 0px;' >
        <img src="<?php echo ADMIN_IMAGES ?>import-animation.gif" alt="">
        <h1>Please wait...</h1>
    </div>

</div>