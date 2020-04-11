<h2>Manage Menu</h2>

<div class="form-box main-box">
    <div class="fix-box">
        <h3 id="categoryButton" >Categories</h3>
        <ul id="categoryBox" class="menu-creator" >
            <?php
            
                if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_id=link_relation_id AND link_type='category' AND category_site_id='".SITE_ID."' AND link_status=200 ORDER BY category_id DESC")) {
                    if(mysqli_num_rows($categoryList)!=0) {
                        while($categoryListData=mysqli_fetch_assoc($categoryList)) {
                            echo "<li>{$categoryListData['category_title']}</li>";
                        }
                    } else {
                        echo 'NO';
                    }
                }
            ?>
        </ul>
        <h3 id="pageButton" >Pages</h3>
        <ul id="pageBox" class="menu-creator" style="display: none" >
            <?php
            
                if($pageList=mysqli_query($connection, "SELECT page_id, page_title, link_url FROM ".TABLE_PREFIX."page, ".TABLE_PREFIX."link WHERE page_id=link_relation_id AND link_type='page' AND page_site_id='".SITE_ID."' AND link_status=200 ORDER BY page_id DESC")) {
                    if(mysqli_num_rows($pageList)!=0) {
                        while($pageListData=mysqli_fetch_assoc($pageList)) {
                            echo "<li>{$pageListData['page_title']}</li>";
                        }
                    } else {
                        echo 'NO';
                    }
                }
            ?>
        </ul>
    </div>
</div>