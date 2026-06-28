<?php

require_once 'header.php';

$sliderList = getSlider($connection);

if (!empty($sliderList)) {
    foreach ($sliderList as $slide) {
        $slideImage = $slide['slide_image'] ?? '';
        $slideSubtitle = $slide['slide_subtitle'] ?? '';
        $slideTitle = $slide['slide_title'] ?? '';
        $slideDescription = $slide['slide_description'] ?? '';
        $slideLink = $slide['slide_link'] ?? '';
        $slideLinkText = $slide['slider_cta'] ?? ($slide['slide_link_text'] ?? '');
        $slideLink2 = $slide['slide_link2'] ?? '';
        $slideLink2Text = $slide['slider_cta2'] ?? ($slide['slide_link2_text'] ?? '');
        $slideAlignment = strtolower(trim($slide['slide_alignment'] ?? 'center'));
        $slideOverlay = strtolower(trim((string)($slide['slide_overlay'] ?? 'on')));

        if (!in_array($slideAlignment, ['left', 'center', 'right'], true)) {
            $slideAlignment = 'center';
        }

        $contentStyle = 'text-align: center; margin: 0 auto; max-width: 860px; width: 100%; box-sizing: border-box;';
        $descriptionStyle = 'margin: 0 auto 2.5rem;';
        $buttonWrapStyle = 'justify-content: center;';

        if ($slideAlignment === 'left') {
            $contentStyle = 'text-align: left; margin: 0 auto 0 0; max-width: 860px; width: 100%; box-sizing: border-box;';
            $descriptionStyle = 'margin: 0 0 2.5rem 0;';
            $buttonWrapStyle = 'justify-content: flex-start;';
        } elseif ($slideAlignment === 'right') {
            $contentStyle = 'text-align: right; margin: 0 0 0 auto; max-width: 860px; width: 100%; box-sizing: border-box;';
            $descriptionStyle = 'margin: 0 0 2.5rem auto;';
            $buttonWrapStyle = 'justify-content: flex-end;';
        }

        $overlayEnabled = in_array($slideOverlay, ['on', '1', 'yes', 'true'], true);
        $heroBackgroundStyle = $overlayEnabled
            ? "background: linear-gradient(135deg, rgba(13, 59, 102, 0.88) 0%, rgba(13, 59, 102, 0.65) 100%), url({$slideImage}); background-size: cover; background-position: center; background-repeat: no-repeat;"
            : "background: url({$slideImage}); background-size: cover; background-position: center; background-repeat: no-repeat;";

        $cta1Style = strtolower(trim($slide['cta1_style'] ?? 'solid'));
        $cta1BgColor = $slide['cta1_bg_color'] ?? '#f4d35e';
        $cta1TextColor = $slide['cta1_text_color'] ?? '#0d3b66';
        $cta2Style = strtolower(trim($slide['cta2_style'] ?? 'outline'));
        $cta2BgColor = $slide['cta2_bg_color'] ?? 'rgba(255,255,255,0.8)';
        $cta2TextColor = $slide['cta2_text_color'] ?? '#ffffff';

        $cta1Background = ($cta1Style === 'solid') ? $cta1BgColor : 'transparent';
        $cta2Background = ($cta2Style === 'solid') ? $cta2BgColor : 'transparent';

        echo "<section class='hero' style='{$heroBackgroundStyle}'>
    <div class='container'>
        <div class='hero-content' style='{$contentStyle}'>
            <div class='hero-badge'>
                {$slideSubtitle}
            </div>
            <h1>{$slideTitle}</h1>
            <p style='{$descriptionStyle}'>
                {$slideDescription}
            </p>";

        if (!empty($slideLink) && !empty($slideLinkText)) {
            echo "<div class='hero-btns' style='{$buttonWrapStyle}'>
                    <a href='{$slideLink}' class='btn btn-secondary' style='background-color: {$cta1Background}; color: {$cta1TextColor}; border: 2px solid {$cta1BgColor};'>{$slideLinkText}</a>
                    " . (!empty($slideLink2) ? "<a href='{$slideLink2}' class='btn-outline-white' style='background-color: {$cta2Background}; color: {$cta2TextColor}; border: 2px solid {$cta2BgColor};'>{$slideLink2Text}</a>" : "") . "
                </div>";
        }

        echo "</div>
    </div>
</section>";
    }
}

?>


<!-- ======================== STATS ======================== -->
<div class="container">
    <div class="stats-section">
        <div class="stat-item">
            <h2>15+</h2>
            <p>Years of Service</p>
        </div>
        <div class="stat-item">
            <h2>100<span>+</span></h2>
            <p>Meals Served Daily</p>
        </div>
        <div class="stat-item">
            <h2>20<span>–25</span></h2>
            <p>Children Educated Free</p>
        </div>
        <div class="stat-item">
            <h2>1000<span>+</span></h2>
            <p>Trees Planted & Cared</p>
        </div>
        <div class="stat-item">
            <h2>100<span>%</span></h2>
            <p>Selfless Volunteers</p>
        </div>
    </div>
</div>

<!-- ======================== ABOUT ======================== -->
<section class="section-padding story-section">
    <div class="container about-preview">
        <div class="about-img-wrap">
            <div class="about-img-main">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071341.jpg" alt="DPJKS team serving community" />
            </div>
            <div class="about-img-badge">
                <span class="badge-num">2009</span>
                <span class="badge-label">Founded</span>
            </div>
        </div>

        <div class="about-text">
            <span class="about-eyebrow">About DPJKS</span>
            <h2>Service Before Self — A Promise Since 2009</h2>
            <p>
                Dharam Prachar Jan Kalyan Samiti was born from a simple, powerful
                belief: that instead of engaging in social conflicts and disputes,
                one can choose the path of <strong>seva (service)</strong>. Our
                founders looked at the suffering around them and chose compassion
                over argument.
            </p>
            <p>
                We are a formally registered social organization with a structured
                governance body — President, Vice President, Secretary, Treasurer,
                and executive members — all of whom have taken a solemn oath to work
                honestly and selflessly for the betterment of society.
            </p>

            <ul class="values-list">
                <li><i class="fas fa-check"></i> Compassion & Empathy</li>
                <li><i class="fas fa-check"></i> Selfless Dedication</li>
                <li><i class="fas fa-check"></i> Unity in Diversity</li>
                <li><i class="fas fa-check"></i> Human Welfare First</li>
                <li><i class="fas fa-check"></i> Religious Harmony</li>
                <li><i class="fas fa-check"></i> Environmental Protection</li>
            </ul>

            <a href="/about/" class="btn btn-primary">Read Our Full Story</a>
        </div>
    </div>
</section>

<?php

$initiativesList = getCategories($connection, 'service', true, 6, false);

if (!empty($initiativesList)) {
    echo "<section class='section-padding initiatives-section'>
    <div class='container'>
        <div class='section-intro'>
            <h2>Our Core Initiatives</h2>
            <p>
                We believe service must be tangible. Here are the programs through
                which we actively serve our community — with commitment,
                consistency, and care.
            </p>
        </div>

        <div class='initiatives-grid'>";

    foreach ($initiativesList as $initiative) {
        extract($initiative);

        echo "<a href='{$link_url}' title='{$title}'>
            <div class='initiative-card'>
                <img src='{$link_url}' alt='{$title}'
                    class='initiative-img' />
                <div class='initiative-body'>
                    <h3>{$title}</h3>
                    <div class='initiative-description'>
                        {$description}
                    </div>
                </div>
            </div>
        </a>";
    }

    echo "</div>
    </div>
</section>";
}


?>


<!-- ======================== OUR STORY / TIMELINE ======================== -->
<section class="section-padding story-section">
    <div class="container">
        <div class="story-grid">
            <!-- Images Side -->
            <div>
                <span class="about-eyebrow">Our Journey</span>
                <h2 style="font-size: 2rem; margin-bottom: 1.5rem">
                    From an Idea to a Movement
                </h2>
                <p style="
                color: var(--text-light);
                line-height: 1.8;
                margin-bottom: 2rem;
              ">
                    Our path was not easy. We faced opposition, misunderstandings, and
                    hardships. But our members never gave up. United by a shared
                    belief in the dignity of service, we built something that has
                    touched thousands of lives.
                </p>
                <div class="story-imgs" style="margin-bottom: 2rem">
                    <img src="<?php echo USER_IMAGES ?>gallery/events/1000071348.jpg" alt="DPJKS community work" />
                    <img src="<?php echo USER_IMAGES ?>gallery/events/1000071349.jpg" alt="DPJKS members serving" />
                    <img src="<?php echo USER_IMAGES ?>gallery/events/1000071351.jpg" alt="DPJKS events" />
                    <img src="<?php echo USER_IMAGES ?>gallery/events/1000071352.jpg" alt="DPJKS activities" />
                </div>
                <div class="story-quote">
                    <p>
                        "We did not choose to fight — we chose to serve. In a world full
                        of conflict and misunderstanding, the highest answer is
                        compassion and selfless action."
                    </p>
                    <div class="quote-author">— Founding Principle, DPJKS</div>
                </div>
            </div>

            <!-- Timeline Side -->
            <div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2009</span>
                        <h4>The Seed of Service</h4>
                        <p>
                            Around 2009, a group of like-minded individuals came together
                            with the vision of serving religion, society, and humanity.
                            The organization began as an informal movement committed to
                            public welfare.
                        </p>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2009–2012</span>
                        <h4>Spreading the Message</h4>
                        <p>
                            Early members worked to peacefully spread their message of
                            social unity and service. Despite opposition and challenges,
                            they remained determined and began building a formal structure
                            with office bearers.
                        </p>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2012</span>
                        <h4>Official Registration</h4>
                        <p>
                            DPJKS was formally registered as a social welfare organization
                            (Reg. No. K-46259) under the Societies Registration Act, 1860.
                            A President, Vice President, Secretary, Treasurer, and
                            executive body were formally appointed — all taking an oath of
                            honest, selfless service.
                        </p>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">COVID-19</span>
                        <h4>Feeding the Hungry During Crisis</h4>
                        <p>
                            During the COVID-19 lockdown, DPJKS mobilized to distribute
                            food to hundreds of needy families and individuals,
                            recognizing that "food is life" when all other support systems
                            failed.
                        </p>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">Jan 2023</span>
                        <h4>Daily Annadaan Program Launched</h4>
                        <p>
                            On 19 January 2023, DPJKS officially launched a continuous
                            public welfare meal program, serving approximately 100 people
                            every day. The program continues running uninterrupted to this
                            day.
                        </p>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <span class="timeline-year">2023–Present</span>
                        <h4>Education & Environment Initiatives</h4>
                        <p>
                            The free education program for 20–25 village children was
                            launched with a dedicated teacher paid ₹3,000/month.
                            Simultaneously, regular tree plantation drives with post-care
                            monitoring were intensified across the region.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================== OATH & VALUES ======================== -->
<section class="oath-section">
    <div class="container">
        <h2>Our Pledge to Society</h2>
        <p>
            Every member of DPJKS — from the President to the newest volunteer —
            has taken a solemn oath to work honestly, selflessly, and without
            discrimination for the betterment of all.
        </p>

        <div class="oath-cards">
            <div class="oath-card">
                <i class="fas fa-heart"></i>
                <h4>Seva (Service)</h4>
                <p>
                    To serve all human beings with equal compassion, never
                    differentiating on the basis of caste, religion, or social status.
                </p>
            </div>
            <div class="oath-card">
                <i class="fas fa-handshake"></i>
                <h4>Samarpan (Dedication)</h4>
                <p>
                    To remain committed to our mission even during hardship,
                    opposition, or personal sacrifice — because service is its own
                    reward.
                </p>
            </div>
            <div class="oath-card">
                <i class="fas fa-users"></i>
                <h4>Ekta (Unity)</h4>
                <p>
                    To stand together as one family, ensuring that collective effort
                    brings more impact than individual action ever could.
                </p>
            </div>
            <div class="oath-card">
                <i class="fas fa-balance-scale"></i>
                <h4>Nishpaksh (Impartiality)</h4>
                <p>
                    To help the needy without discrimination — food, education, and
                    support belong to every person regardless of their background.
                </p>
            </div>
            <div class="oath-card">
                <i class="fas fa-leaf"></i>
                <h4>Prakriti Raksha (Nature)</h4>
                <p>
                    To protect and nurture the environment we live in, for harming
                    nature is to harm God's greatest gift to humanity.
                </p>
            </div>
        </div>
    </div>
</section>

<?php

// Events

$eventsList = getEvents($connection, '', true, 5);

if (!empty($eventsList)) {
    echo "<section class='section-padding' style='background: #f9f9f9'>
    <div class='container'>
        <div style='
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 3rem;
          '>
            <div>
                <h4 style='
                color: var(--accent-color);
                text-transform: uppercase;
                letter-spacing: 1px;
                font-size: 0.85rem;
              '>
                    Get Involved
                </h4>
                <h2>Activities & Programs</h2>
            </div>
            <a href='/events/' class='btn btn-secondary'>View All</a>
        </div>

        <div class='home-events-grid'>";

    $i = 1;

    foreach ($eventsList as $eventInfo) {
        extract($eventInfo);
        if ($i == 1) {
            echo "<div class='featured-event'>
                <img src='{$image}' alt='{$title}'
                    class='featured-bg' />
                <div class='featured-content'>
                    <span class='event-badge'>{$category}</span>
                    <h3>{$title}</h3>
                    <p style='margin-bottom: 1rem; opacity: 0.9'>
                        <i class='fas fa-map-marker-alt' style='margin-right: 8px'></i>
                        {$location}
                    </p>
                    <p style='margin-bottom: 1.5rem'>
                        {$description}
                    </p>
                    <a href='{$link_url}' title='{$title}' class='btn btn-primary' style='padding: 0.6rem 1.2rem; font-size: 0.9rem'>
                        Volunteer Now
                    </a>
                </div>
            </div>";

            echo "<div class='recent-events-list'>";
            $i++;
            continue;
        }

        echo "<div class='recent-event-card'>
                    <div class='recent-date-box'>
                        <span class='rd-num'>" . date('d', strtotime($event_date)) . "</span>
                        <span class='rd-month'>" . date('M', strtotime($event_date)) . "</span>
                    </div>
                    <div class='recent-info'>
                        <h4>{$title}</h4>
                        <p>{$location}</p>
                        <a href='{$link_url}' title='{$title}' class='read-link'>View <i class='fas fa-arrow-right'></i></a>
                    </div>
                </div>
            ";
    }
    echo "</div>";

    echo "</div>
    </div>
</section>";
}

// Testimonials

$testimonialsList = getTestimonials($connection, true, 3);

if (!empty($testimonialsList)) {
    echo "<section class='section-padding' style='background: white'>
    <div class='container'>
        <div class='section-intro'>
            <h2>Voices of Impact</h2>
            <p>
                Hear from those whose lives have been touched by our work and the
                volunteers who make it all possible.
            </p>
        </div>

        <div class='testimonials-modern'>";

    foreach ($testimonialsList as $testimonial) {
        extract($testimonial);
        $authorInfo = explode(',', $title);
        $authorName = $authorInfo[0];
        $authorRole = ((isset($authorInfo[1])) ? trim($authorInfo[1]) : '');
        echo "<div class='testi-card'>
                <i class='fas fa-quote-right quote-icon'></i>
                <p class='testi-content'>
                    {$description}
                </p>
                <div class='testi-author'>
                    <img src='{$profile_image}' alt='{$authorName}'
                        class='author-avatar' />
                    <div class='author-info'>
                        <h4>{$authorName}</h4>
                        <span>{$authorRole}</span>
                    </div>
                </div>
            </div>";
    }

    echo "
        </div>
    </div>
</section>";
}


require_once 'footer.php';

?>