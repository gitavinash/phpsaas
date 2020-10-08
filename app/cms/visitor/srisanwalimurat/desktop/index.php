<?php

    require_once 'header.php';

?>
<section>
    <div class="main-left-box">
        <h2>Thought of The Day</h2>
        <p>Anger leads to bewilderment, bewilderment to loss of memory of true Self, and by that intelligence is destroyed, and with the destruction of intelligence he perishes.<span>Srimad Bhagwad Gita<span></p>
    </div>
    <div class="main-right-box">
        <div class="main-right-box-left-section">
            <!-- <div class="live-section">
                <a href="" class="live-btn" >Watch Live</a>
            </div> -->
            <div class="feature-video">
                <?php
                
                    if($checkVideo=mysqli_query($connection, "SELECT link_url, video_title, video_description, video_type FROM ".TABLE_PREFIX."video INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=video_id WHERE video_status=1 AND link_type='video' ORDER BY link_published DESC LIMIT 1")) {

                        if(mysqli_num_rows($checkVideo)!=0) {

                            extract(mysqli_fetch_assoc($checkVideo));

                            echo $video_description;

                        } else {

                            echo "<p>No video found</p>";

                        }

                    }

                ?>
            </div>
        </div>
        <div class="news-section">
            <h2>News</h2>
            <div class="news-slider">
                <div class="news-slide" >
                    <p>SSR case live: CBI grills Rhea Chakraborty</p>
                </div>
                <div class="news-slide" >
                    <p>Members of CSK contingent test +ve for Covid</p>
                </div>
                <div class="news-slide" >
                    <p>6 oppn-ruled states move SC over NEET-JEE</p>
                </div>
                <div class="news-slide" >
                    <p>'No degree without final-year exam': SC</p>
                </div>
                <div class="news-slide" >
                    <p>Listen, act per the wishes of students: Sonia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="bhagwat-geeta-left-box">
        <img src="<?php echo USER_IMAGES ?>bhagwat-geeta.jpg" alt="">
    </div>
    <div class="bhagwat-geeta-right-box">
        <h2>About Bhagwat Geeta</h2>
        <p>गीता एक दार्शनिक ग्रंथ है। गीता के वक्ता ने दार्शनिक समस्याओं को इतनी सरलता से सुलझाया है कि आज उन्हें समझने में सर्वसाधारणजन समर्थ हो सका है। प्रमाणभूत प्रस्थानत्रयी में गीता का अग्रस्थान है, गीताशास्त्र  हिंदू-वाङ्ग्मय का सर्वश्रेष्ठ निर्णायक ग्रंथ है; क्योंकि गीता ने समतावाद तथा मनुष्यमात्र के समानाधिकारों की घोषणा करके ऊँचनीच के भेदभाव को मिटाने का प्रशंसनीय कार्य किया है। स्त्री, वैश्य तथा शूद्रों पर लगे प्रतिबंधों को हटाकर सबको एक ही प्रगतिपथ पर लाने का श्रेय भी गीता को ही मिला है।</p>
        <p>गीता संसारभर का आदर्श ग्रंथ है। गीता ईश्वरीय-वाणी है। ईश्वरीय ज्ञान ही जीव-स्वरूपगत अविद्या आदि दोषों के मिटाने की क्षमता रखता है। सांसारिक संताप, पापविनाशिनी ब्रह्मवाणी ही निर्वाणपद प्रदायिनी मानी गयी है। यह ‘सरल हिंदी गीता’ श्रीजयकृष्णी धर्म के सिद्धांतों के आधार पर विशेषरूप से लिखी गयी है, अतः इसका पंथीय जनता समुचित लाभ उठायेगी, ऐसी हमें आशा है।</p>
        <a href="#" class="live-btn" >Free Download</a>
    </div>
</section>

<section>
    <div class="events">
        <h2>Recent Events</h2>
        <div class="event-slider">
            <div class="event-slide">
                <div class="event-box">
                    <img src="<?php echo USER_IMAGES ?>hall.jpg" alt="">
                    <div class="event-title">
                        <h3>Event Name Goes Here</h3>
                    </div>
                    <div class="event-details">
                        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Soluta rem, incidunt unde blanditiis consequuntur</p>
                    </div>
                </div>
            </div>
            <div class="event-slide">
                <div class="event-box">
                    <img src="<?php echo USER_IMAGES ?>hall.jpg" alt="">
                    <div class="event-title">
                        <h3>Event Name Goes Here</h3>
                    </div>
                    <div class="event-details">
                        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Soluta rem, incidunt unde blanditiis consequuntur</p>
                    </div>
                </div>
            </div>
            <div class="event-slide">
                <div class="event-box">
                    <img src="<?php echo USER_IMAGES ?>hall.jpg" alt="">
                    <div class="event-title">
                        <h3>Event Name Goes Here</h3>
                    </div>
                    <div class="event-details">
                        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Soluta rem, incidunt unde blanditiis consequuntur</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="about-us-left-box">
        <img src="<?php echo USER_IMAGES ?>murat.jpg" alt="">
    </div>
    <div class="about-us-right-box">
        <h2>Mahanubhav Panth: An Introduction</h2>
        <p>Mahanubhav Panth is a religious sect which follows the teachings of Srimad Bhagvad Gita and Sri Brahmvidya Shastra. Established around 800 years ago by Sarvagy Sri Chakradhar Swamiji in Maharashtra, this sect believes in single-minded devotion (“ananya bhakti”) of the Almighty God and its five incarnations (“panch avatars”). </p>
        <p>The five avatars we are firmly devoted to are: Lord Sri Krishna, Bhagvan Sri Dattatreya Maharaj, Sri Chakrapani Prabhu Maharaj, Sri Govind Prabhu Maharaj and Sarvagy Sri Chakradhar Swami ji. This means that Mahanubhav Panth believes in the Theory of Incarnation (“avatarvad”).</p>
        <p>The Supreme God took these five incarnations for the sole purpose of giving Godly knowledge (“Gyan”)and Godly Love (“Prem”) to the deserving human beings (Adhikari jeev) so that they can get rid of the cycle of life and death and attain Moksha (or Parmanand). From this position, soul never returns back to this earth again. So, we believe these two be the main sources of imancipation. </p>
    </div>
</section>

<?php

    require_once 'footer.php';

?>