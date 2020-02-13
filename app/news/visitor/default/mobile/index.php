<?php

	include_once 'header.php';

?>

            <section>
                <div class="homepage-slider-box">
                    <div class="homepage-slider">
                        <?php
                        for($i=0; $i < 5 ; $i++) {
                            echo "<div class='slider'>
                                <a href='{$topNewsPost[$i]['link_url']}'>
                                    <div class='img-box'>
                                        <img src='{$topNewsPost[$i]['post_image']}' alt=''>
                                    </div>
                                    <div class='text-box'>
                                        <p class='skew'><span>खास खबरें</span></p>
                                        <h1>{$topNewsPost[$i]['post_title']}</h1>
                                    </div>
                                    <div class='summery-box'>
                                        <p>{$topNewsPost[$i]['meta_description']}</p>
                                    </div>
                                </a>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="trending-box">
                    <div class="big-news">
                        <h3>बड़ी खबर</h3>
                        <?php
                            for($i=5; $i<10; $i++) {
                                echo "<a href='{$topNewsPost[$i]['link_url']}' title='{$topNewsPost[$i]['post_title']}'><h2>{$topNewsPost[$i]['post_title']}</h2></a>";
                            }
                        ?>
                    </div>
                    </div>
                </div>
            </section>

            <section class="header-ad-box">
            
                <!-- /1009127/RW_PunjabKesari_300x250 -->
                <div id='div-gpt-ad-1549554871716-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549554871716-0'); });
                </script>
                </div>
            
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>देश</h2></div>
                <?php
                
                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE post_category=3 AND link_status=200 AND link_type = 'post' AND post_status=1 ORDER BY link_modified DESC LIMIT 8, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <section>
                <div class="title"><h2>विदेश</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE post_category=11 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <section class="header-ad-box">
                
                <!-- /1009127/RW_PunjabKesari_Resp_1 -->
                <div id='div-gpt-ad-1549975705782-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549975705782-0'); });
                </script>
                </div>
                
            </section>

            <!-- top news youtube -->

            <section>

                <div class="youtube-box">
                    <div class="top-video">
                    <h2>आज की बड़ी ख़बरें</h2>
                    <iframe width="712" height="400" src="https://www.youtube.com/embed/7qNAMWg1Mu4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <div class="feature-box">
                    
                    <h3>संपादकीय</h3>

                    <div class="feature-slider">

                        <?php

                        if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE post_category=14 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 1")) {
                            if(mysqli_num_rows($categoryNews)!=0) {
                                while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                    echo "<a href='{$categoryNewsData['link_url']}'><div class='slider'>
                                    <div class='img-box'>
                                        <img src='{$categoryNewsData['post_image']}' alt=''>
                                    </div>
                                    <div class='text-box'>
                                        <h2>{$categoryNewsData['post_title']}</h2>
                                    </div>
                                </div></a>";

                                }
                            }
                        }

                        ?>

                    </div>

                    <div class="youtube-banner">
                        <a href="https://www.youtube.com/channel/UCrthViLN3AFsHhb_k4qZ28w?sub_confirmation=1" target="_blank" title="Punjab Kesari Youtube Channel"><img src="<?php echo USER_IMAGES ?>youtube-banner.png" alt=""></a>
                    </div>

                </div>

            </section>

            <section class="header-ad-box">
            
                <!-- /1009127/RW_PunjabKesari_Resp_2 -->
                <div id='div-gpt-ad-1549975808942-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549975808942-0'); });
                </script>
                </div>
            
            </section>
            <!-- main news -->

            <section>
                <div class="bollywood-post red">
                    <div class="title"><h2>बॉलीवुड</h2></div>
                    <div class="entertainment-slider">

                        <?php

                        if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE post_category=18 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 5")) {
                            if(mysqli_num_rows($categoryNews)!=0) {
                                while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                    echo "<div class='category-post'>
                                        <img src='{$categoryNewsData['post_image']}' alt=''>
                                        <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                    </div>";

                                }
                            }
                        }

                        ?>

                    </div>
                    <div class="ad-box">
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
                </div>
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>खेल</h2></div>

                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=12 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
                
            </section>

            <section>
                <div class="title"><h2>दिल्ली – एन. सी. आर.</h2></div>
                <?php
                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=4 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>


            <section class="header-ad-box">
                
                <!-- /1009127/RW_PunjabKesari_Resp_3 -->
                <div id='div-gpt-ad-1549975922100-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549975922100-0'); });
                </script>
                </div>
            
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>उत्तर प्रदेश</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=5 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>हरियाणा</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=16 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <section class="header-ad-box">
            
                <!-- /1009127/RW_PunjabKesari_Resp_4 -->
                <div id='div-gpt-ad-1549976009867-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549976009867-0'); });
                </script>
                </div>
            
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>राजस्‍थान</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=6 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>जम्मू-कश्मीर</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=7 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>
            
            <section class="header-ad-box">
                
                <!-- /1009127/RW_PunjabKesari_Resp_5 -->
                <div id='div-gpt-ad-1549976105111-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549976105111-0'); });
                </script>
                </div>
            
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>पंजाब</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=8 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <!-- main news -->

            <section>
                <div class="title"><h2>व्यापार</h2></div>
                <?php

                    if($categoryNews=mysqli_query($connection, "SELECT link_url, post_image, post_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE post_category=13 AND link_status=200 AND link_type = 'post' ORDER BY link_published DESC LIMIT 0, 5")) {
                        if(mysqli_num_rows($categoryNews)!=0) {
                            while($categoryNewsData=mysqli_fetch_assoc($categoryNews)) {
                                    
                                echo "<div class='category-post'>
                                    <img src='{$categoryNewsData['post_image']}' alt=''>
                                    <a href='{$categoryNewsData['link_url']}'><h3>{$categoryNewsData['post_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

<?php

	include_once 'footer.php';

?>