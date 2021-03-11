<h2>Import page</h2>

<div class="box-2">
    <div class="form-box">
	    <form method="page" id="importData" enctype="multipart/form-data"  >

            <div id="importCategoryBox" class="form-group">
                <label>Step 1</label>
				<select name="importCategoryBox" id='importCategoryBox' class='form-control inside-shadow' required >
				    <option value="" >Import from</option>
                    <option value="wordpress">WordPress</option>
                    <option value="csv">CSV</option>
				</select>
            </div>
            <div id='importFileBox' style="display:none" class='import-box' >
                <label style="float: left; margin-bottom: 10px" >Step 3</label>
                <div class='import-file-box'>
                    <div class="form-group">
                        <label>Upload page csv file</label>
                        <input type="file" id='importFile' name='importFile' class='form-control inside-shadow' required >
                    </div>
                </div>
            </div>
            
            <div class="form-group" id="importBtnBox" style="display:none" >
                <input type="hidden" name="type" value="import" >
                <input type="hidden" name="action" value="page" >
                <button id="importDataBtn" class="btn btn-green">Import Data</button>
            </div>
            
        </div>
     </form>
    </div>
    
    <div class="please-wait" style='text-align: center; display: none; margin: 100px 0px;' >
        <img src="<?php echo IMG_LIBRARY ?>circle-dot-loader.gif" alt="">
        <h1>Please wait...</h1>
    </div>

    <div id='status' >
        <span id="success" style='float: left;width: 100%;font-size: 20px;' ></span>
        <span id="dublicate" style='float: left;width: 100%;font-size: 20px;' ></span>
        <span id="failed" style='float: left;width: 100%;font-size: 20px;' ></span>
    </div>

</div>