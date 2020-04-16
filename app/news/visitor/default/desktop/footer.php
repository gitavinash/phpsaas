
        
        </main>

        <footer>
            <div class="main-box">
                <div class="link-box">
                    <h3>News</h3>
                    <div class="category-list">
                        <?php
                            if($checkCategory=mysqli_query($connection, "SELECT category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE link_relation_id=category_id AND link_type='category'")) {
                                while($category=mysqli_fetch_assoc($checkCategory)) {
                                    echo "<a href='{$category['link_url']}'>{$category['category_title']}</a>";
                                }
                            } else {
                                echo mysqli_error($connection);
                            }
                        ?>
                    </div>
                    <h3>Trending Topics</h3>
                    <?php
                        if($tagList=mysqli_query($connection, "SELECT tag_title, COUNT(*) AS tag FROM ".TABLE_PREFIX."tag WHERE tag_title!='' GROUP BY tag_title ORDER BY tag DESC LIMIT 0, 20")) {
                            while($tags=mysqli_fetch_assoc($tagList)) {
                                echo "<a href='tag/".strtolower(str_replace(' ', '-',$tags['tag_title']))."/' class='tags'>{$tags['tag_title']}</a>";
                            }
                        }
                    ?>
                    <!-- <div class="about-us">
                        <a href="about-us/" class='english' >About Us</a>
                        <a href="advertise-with-us/" class='english' >Advertise with us</a>
                        <a href="book-print-ad/" class='english' >Book Print Ad</a>
                        <a href="disclaimer/" class='english' >Disclaimer</a>
                        <a href="privacy-policy/" class='english' >Privacy policy</a>
                        <a href="terms-and-condition/" class='english' >Terms & Condition</a>
                        <a href="contact-us/" class='english' >Contact Us</a>
                        <a href="sitemap.xml" class='english' >Sitemap</a>
                    </div> -->
                </div>
                <div class="contact-box">
                    <div class="footer-logo-box">
                        <a href=""><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
                    </div>
                    <div class="footer-social-link">
                        <h3>Follow Us On</h3>
                        <a href="https://www.facebook.com/punjabkesarinational" target='_blank' ><i class="fab fa-facebook-square"></i></a>
                        <a href="https://twitter.com/PunjabKesariCom" target='_blank' ><i class="fab fa-twitter-square"></i></a>
                        <a href="https://www.instagram.com/punjabkesarinews/" target='_blank' ><i class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/PunjabKesariDotCom" target='_blank' ><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="footer-social-link">
                        <h3>Our Mobile Apps</h3>
                        <a href="https://play.google.com/store/apps/details?id=com.punjab.kesari" target='_blank' ><i class="fab fa-google-play"></i></a>
                        <a href="https://itunes.apple.com/in/app/hindi-news-by-punjab-kesari/id1420361527" target='_blank' ><i class="fab fa-app-store-ios"></i></a>
                    </div>
                </div>
                <div class="credit">
                    &copy Copyright 2007 - <?php echo date('Y'); ?> <a href=""><?php echo SITE_NAME ?></a>
                </div>
            </div>
        </footer>
        
        <?php require_once NOTIFICATION.'index.php'; ?>
        <script type="text/javascript" src="<?php echo USER_PLUGINS ?>slick/slick.min.js"></script>
        <script type="text/javascript" src="<?php echo USER_JS ?>app.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/progressive-image.js/dist/progressive-image.js"></script>
        <script type="text/javascript" src="<?php echo USER_JS ?>script.js"></script>

    </body>

</html>