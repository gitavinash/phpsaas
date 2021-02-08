<h2>Import product</h2>

<div class="box-2">
    <div class="form-box">
	    <form method="POST" id="importData" enctype="multipart/form-data"  >

            <div id="importFromBox" class="form-group">
                <label>Step 1</label>
				<select name="importFrom" id='importFrom' class='form-control inside-shadow' required >
				    <option value="" >Import from</option>
                    <option value="wordpress">WordPress</option>
                    <option value="etsy">Etsy</option>
                    <option value="csv">CSV</option>
				</select>
            </div>
            <div id="importCategoryBox" class="form-group" style="display:none">
                <label>Step 2</label>
				<select name="categoryID" id='importCategory' class='form-control inside-shadow' required >
				<option value="" >Select product category</option>
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$type}' AND category_site_id='".SITE_ID."' AND link_status=200 ORDER BY category_id DESC")) {
						if(mysqli_num_rows($categoryList)!=0) {
							while($categoryData=mysqli_fetch_assoc($categoryList)) {
								echo "<option value='{$categoryData['category_id']}_{$categoryData['link_url']}'>{$categoryData['category_title']}</option>";
							}
						} else {
							echo "<option>Please add new product category</option>";
						}
					}
				?>
				</select>
            </div>

            <div id='importFileBox' style="display:none" class='import-box' >
                <label style="float: left; margin-bottom: 10px" >Step 3</label>
                <div class='import-file-box'>
                    <div class="form-group">
                        <label>Upload product csv file</label>
                        <input type="file" id='importFile' name='importFile' class='form-control inside-shadow' required >
                    </div>
                </div>
            </div>
            
            <div class="form-group" id="importBtnBox" style="display:none" >
                <input type="hidden" name="type" value="import" >
                <input type="hidden" name="action" value="product" >
                <button id="importDataBtn" class="btn btn-green">Import Data</button>
            </div>
            
        </div>
     </form>
    </div>
    
    <div class="please-wait" style='text-align: center; display: none; margin: 100px 0px;' >
        <img src="<?php echo ADMIN_IMAGES ?>import-animation.gif" alt="">
        <h1>Please wait...</h1>
    </div>

    <div id='status' style="display:none;" >
        <span id="success" style='float: left;width: 100%;font-size: 20px;' ></span>
        <span id="dublicate" style='float: left;width: 100%;font-size: 20px;' ></span>
        <span id="failed" style='float: left;width: 100%;font-size: 20px;' ></span>
    </div>

</div>