    <div class="trending-box">
        <div class="big-news">
            <h3>बड़ी खबर</h3>
            <?php
                for($i=0; $i<10; $i++) {
                    echo "<a href='{$topNewsPost[$i]['link_url']}' title='{$topNewsPost[$i]['post_title']}'><h2>{$topNewsPost[$i]['post_title']}</h2></a>";
                }
            ?>
        </div>
    </div>
    
    <div class='ad-box-300'>
        <span>Advertisement</span>
        <script type="text/javascript"><!--
        google_ad_client = "ca-pub-6070398767421094";
        /* PK.COM_300X250 */
        google_ad_slot = "PK.COM_300X250";
        google_ad_width = 300;
        google_ad_height = 250;
        //-->
        </script>
        <script type="text/javascript"
        src="//pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
    </div>