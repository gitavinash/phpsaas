<!DOCTYPE html>
<html lang="hi_IN">

    <head>
    	<base href="<?php echo SITE_URL ?>/" >
        <meta charset="utf-8">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link rel="dns-prefetch" href="//www.googletagservices.com">
        <link rel="dns-prefetch" href="//www.googletagmanager.com">
        <link rel="dns-prefetch" href="//www.youtube.com">
        <link rel="dns-prefetch" href="//d1uck549nef0ok.cloudfront.net">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" value="index" >
        <meta property="fb:pages" content="1779174612300730" />
        <link rel="icon" href="<?php echo USER_IMAGES ?>favicon.png">
        <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.' '.SITE_DESC ) ?></title>
        <link rel="canonical" href="<?php echo ((isset($meta_canonical)) ?  (($meta_canonical!='') ? $meta_canonical : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) : HTTP_PROTOCOL.SITE_DOMAIN.BASE_URL ) ?>" />
        <meta name="theme-color" content="#FE6F06">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
        <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>all.min.css" >
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick.min.css">
        <link rel="stylesheet" href="<?php echo USER_PLUGINS ?>slick/slick-theme.min.css">
        <link href="https://fonts.googleapis.com/css?family=Eczar:400,500,700|Fira+Sans+Condensed:300,400&display=swap" rel="stylesheet">

<script src="https://cdn.rwadx.com/prod-global-351532.js" async></script>

    </head>

    <body>
        
        <?php

            /** Show menu bar only for login users */

            if(isset($_COOKIE['user_data'])) {
                if($urlStructure[0]!='dashboard') { ?>
                    <div class="dashboard-bar english">
                        <ul class='left-menu'>
                            <li><a href="dashboard/"><i class='fas fa-tachometer-alt'></i><?php echo SITE_NAME ?></a></li>
                            <?php
                                if((isset($link_type)) ? $link_type=='page' || $link_type=='post' : '' ) { ?>
                                    <li><a href="dashboard/<?php echo $link_type ?>/?type=edit&editor=true&<?php echo $link_type ?>_id=<?php echo $link_relation_id ?>"><i class='fas fa-pencil-alt'></i>Edit <?php echo ucwords($link_type); ?></a></li>
                                <?php
                                }
                            ?>
                            <li>
                                <form action="/search/" method="GET">
                                    <input type="text" class="header-search" name="q" placeholder="Search..." >
                                </form>
                            </li>
                        </ul>
                        <ul class='right-menu'>
                            <li><a href="logout/"><i class='fas fa-sign-out-alt'></i>Log Out</a></li>
                        </ul>
                    </div>
                <?php
                }
            }
        ?>

        <header>
            <div class="breaking-box">
                <div class="breaking-title">
                    <p class="english">BREAKING NEWS</p>
                </div>
                <div class="breaking-slider">

                <!-- Fetch Top News Data -->

                <?php

                if($topNews=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE link_status=200 AND link_is_featured=1 AND link_type='post' AND post_status=1 ORDER BY link_modified DESC LIMIT 0, 20")) {
                    if(mysqli_num_rows($topNews)!=0) {
                        while($topNewsData=mysqli_fetch_assoc($topNews)) {

                            $topNewsPost[]=$topNewsData;

                        }
                    }
                } else {

                    exit();

                }

                echo "<marquee>";

                for($i=0; $i<sizeof($topNewsPost); $i++) {

                    echo $topNewsPost[$i]['post_title'].'&#9726;';

                }

                echo "</marquee>";

                ?>    
                </div>
            </div>
            <div class="header">
                <div class="logo">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>logo-main.png" alt=""></a>
                </div>
                <div class="menu">
                    <nav>
                        <ul>
                            <li>
                                <a href="/category/india-news/">देश</a>
                                <ul>
                                    <li><a href="/category/delhi-ncr/">दिल्ली</a></li>
                                    <li><a href="/category/uttar-pradesh/">उत्तर प्रदेश</a></li>
                                    <li><a href="/category/haryana-news/">हरियाणा</a></li>
                                    <li><a href="/category/rajasthan-news/">राजस्‍थान</a></li>
                                    <li><a href="/category/jammu-and-kashmir/">जम्मू-कश्मीर</a></li>
                                    <li><a href="/category/punjab-news/">पंजाब</a></li>
                                    <li><a href="/category/bihar-news/">बिहार</a></li>
                                    <li><a href="/category/other-states/">अन्य राज्य</a></li>
                                </ul>
                            </li>
                            <li><a href="/category/world-news/">विदेश</a></li>
                            <li><a href="/category/bollywood-kesari/">बॉलीवुड</a></li>
                            <li><a href="/category/sports-news/">खेल</a></li>
                            <li><a href="/category/editorial/">संपादकीय</a></li>
                            <li><a href="/category/business-news/">व्यापार</a></li>
                            <li><a href="/category/horoscope/">भविष्यफल</a></li>
                            <li><a href="/category/social-news/">दिलचस्प खबरें</a></li>
                            <li><a href="http://epaper.punjabkesari.com" target="_blank" class="english">Old E-PAPER</a></li>
                            <li><a href="http://mpaper.punjabkesari.com" target="_blank" class="english">New E-PAPER</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <main>

            <section class="header-ad-box">
            
                <!-- /1009127/RW_PunjabKesari_728x90
                <div id='div-gpt-ad-1549609719157-0'>
                <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-1549609719157-0'); });
                </script>
                </div>
                -->

                <a href="//jrmedia.in/ads/bjmc/" target="_blank"><img src="<?php echo USER_IMAGES.'jr_media.png'; ?>" alt=""></a>
            
            </section>

        <!-- Default post view -->

<section class='body'>

<div class="main">
    <div class='title'>
        <h2>Test</h2>
    </div>
    <h1>Test page</h1>

    <div class="feature-img">
        <img src="https://punjabkesari.com/upload/2019/07/1562585906_raj_thackeray.jpg" alt="">
    </div>

    
    <div class="content">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris id lacinia ligula. Nulla in metus sodales tellus porta pharetra. Nunc placerat ex orci, non lacinia lacus ullamcorper a. Sed interdum leo a volutpat ullamcorper. Sed in ipsum leo. Nullam magna dolor, molestie vitae erat ut, dictum consequat arcu. Duis viverra eleifend dolor quis aliquet. Aenean consectetur nisl non sapien auctor iaculis et at nulla. Integer eros quam, viverra ac libero a, eleifend laoreet arcu. Maecenas purus velit, aliquam eget risus vitae, feugiat vestibulum ipsum. Donec sodales tempor urna, id posuere tellus vehicula sodales. Aenean ultrices arcu id tempor semper. Nunc sed ligula imperdiet, vestibulum augue nec, vulputate tortor.</p>
        <p>Maecenas vel ante vitae diam cursus varius. Ut congue iaculis eros, vel consequat mi porta faucibus. Phasellus pretium blandit semper. Aliquam erat volutpat. Phasellus vestibulum, mi nec malesuada mattis, mi libero euismod leo, id tempus mi turpis et nunc. Vestibulum non cursus felis. Integer finibus placerat eros, a bibendum odio ultricies fermentum. Mauris rhoncus blandit purus non pellentesque. Nunc quis turpis nec felis placerat mattis. Quisque vel turpis ac est eleifend finibus. Nam elementum leo lorem, sed mattis dui tempor accumsan. Proin purus tortor, semper vitae nibh sit amet, faucibus scelerisque lacus. Sed porttitor nunc urna, sit amet vestibulum mi interdum nec.</p>
        <p>Vivamus iaculis vehicula turpis, ac rhoncus tortor pellentesque nec. Fusce ultricies vestibulum diam, quis faucibus sem dapibus at. Suspendisse vulputate, nisl at placerat luctus, dolor orci ullamcorper velit, vel tincidunt augue urna quis elit. Aenean ac aliquet dui. Nulla in felis at nibh condimentum lobortis. Vivamus lacus magna, molestie et gravida sed, faucibus sed arcu. Nunc sit amet tincidunt elit. Sed bibendum volutpat urna, sit amet imperdiet nisi sollicitudin id. Interdum et malesuada fames ac ante ipsum primis in faucibus. Suspendisse lacinia tortor et lorem elementum sollicitudin. In ac lorem mattis, bibendum mi id, aliquam dui. Nulla molestie convallis mollis.</p>
        <p>Sed erat nibh, tincidunt tristique ultricies volutpat, gravida sit amet libero. Phasellus malesuada, dolor porta porta bibendum, tortor lorem congue ligula, eu tristique nisi ipsum quis nunc. Duis vestibulum, ligula ac pharetra faucibus, est leo interdum erat, id vestibulum odio ex ac urna. Sed pulvinar, magna sit amet ultrices mattis, lacus tellus suscipit est, rhoncus venenatis ex justo eu neque. Donec aliquet tempus mauris, sed hendrerit nisi semper sit amet. Maecenas a quam in dolor elementum convallis. Nullam venenatis tortor consectetur mattis euismod. Curabitur id mauris massa. Sed vitae purus eu diam tincidunt tempus.</p>
        <p>Phasellus a urna risus. Pellentesque eu orci libero. Etiam placerat scelerisque neque, non tincidunt felis. Duis tellus mi, dignissim tincidunt vulputate venenatis, hendrerit quis velit. Phasellus vel facilisis tellus. Maecenas semper eleifend sapien a volutpat. Duis vestibulum pulvinar purus, id cursus purus tincidunt eu.</p>
    </div>

    <div class="comment">
                               
    </div>
    
    <!-- You plus -->

    <div id="yp_widget"></div>

    <div class="taboola">
        <div id="taboola-below-article-thumbnails"></div>
        <script type="text/javascript">
        window._taboola = window._taboola || [];
        _taboola.push({
            mode: 'thumbnails-a',
            container: 'taboola-below-article-thumbnails',
            placement: 'Below Article Thumbnails',
            target_type: 'mix'
        });
        </script>
    </div>

</div>

<div class="sidebar">
    <?php
    
        require_once 'sidebar.php';

    ?>
</div>

</section>

<?php

    require_once 'footer.php';

?>