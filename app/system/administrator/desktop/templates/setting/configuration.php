<h2>Website Configuration</h2>

<div class="form-box">
	<div class="configMenu">
        <ul>
            <li id="pos1" class="active" >General</li>
            <li id="pos2">Ads.txt</li>
            <li id="pos3">Robots.txt</li>
            <li id="pos4">Mainfest.json</li>
        </ul>
    </div>
    <div class="configBody">
        <div id="pos1Box">
            
            <!-- General Configuration section -->
            
        </div>
        <div id="pos2Box" style="display: none" >

            <!-- Ads.txt section -->
            
            <h3>Manage ads.txt file</h3>
            <form>
                <textarea name="ads_txt" id="ads_txt" ><?php echo $site_ads_txt; ?></textarea>
                <input type="button" id="adsTXT" class="btn btn-green" value="Update File" >
            </form>

        </div>
        <div id="pos3Box" style="display: none" >
            
            <!-- Robots.txt section -->

            <h3>Manage robots.txt file</h3>
            <form>
                <textarea name="robots_txt" id="robots_txt" ><?php echo $site_robots_txt; ?></textarea>
                <input type="button" id="robotsTXT" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
        <div id="pos4Box" style="display: none" >
            
            <!-- Mainfest.json section -->

            <h3>Manage mainfest.json file</h3>
            <form>
                <textarea name="mainfest_json" id="mainfest_json" ><?php echo $site_mainfest_json; ?></textarea>
                <input type="button" id="mainfestJSON" class="btn btn-green" value="Update File" >
            </form>
            
        </div>
    </div>
</div>