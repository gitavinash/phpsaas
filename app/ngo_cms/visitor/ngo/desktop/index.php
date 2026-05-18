<?php

require_once 'header.php';

?>
<!-- ======================== HERO ======================== -->
<section class="hero" style="background:
    linear-gradient(
      135deg,
      rgba(13, 59, 102, 0.88) 0%,
      rgba(13, 59, 102, 0.65) 100%
    ),
    url('<?php echo USER_IMAGES ?>/gallery/events/1000071350.jpg'),
    no-repeat center center/cover;">
    <div class="container hero-content">
        <div class="hero-badge">
            <i class="fas fa-hands-helping" style="margin-right: 6px"></i>
            Serving Religion, Society & Humanity
        </div>
        <h1>
            Seva, Samarpan,<br />
            <span>Sankalp</span>
        </h1>
        <p>
            Dharam Prachar Jan Kalyan Samiti has been walking the path of selfless
            service since 2009 — feeding the hungry, planting life, educating
            children, and building a cleaner, more compassionate community.
        </p>
        <div class="hero-btns">
            <a href="/donation/" class="btn btn-secondary">Support Our Cause</a>
            <a href="/about/" class="btn-outline-white">Our Journey</a>
        </div>
    </div>
</section>

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

<!-- ======================== INITIATIVES ======================== -->
<section class="section-padding initiatives-section">
    <div class="container">
        <div class="section-intro">
            <h2>Our Core Initiatives</h2>
            <p>
                We believe service must be tangible. Here are the programs through
                which we actively serve our community — with commitment,
                consistency, and care.
            </p>
        </div>

        <div class="initiatives-grid">
            <!-- Initiative 1: Annadaan -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071337.jpg" alt="Annadaan - Food Distribution"
                    class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Annadaan — Food is God</h3>
                    <p>
                        We firmly believe that <em>Anna (food) is life</em>. During the
                        COVID-19 lockdown, when countless families went hungry, DPJKS
                        stepped forward to distribute food to the needy. Our community
                        meal program, started on <strong>19 January 2023</strong>, now
                        serves approximately
                        <strong>100 people every single day</strong>.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-calendar-check" style="margin-right: 6px; color: var(--accent-color)"></i>
                        Running continuously since January 2023
                    </div>
                </div>
            </div>

            <!-- Initiative 2: Tree Plantation -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071336.jpg" alt="Tree Plantation Drive"
                    class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Nature is God — Protect It</h3>
                    <p>
                        We see nature not just as environment, but as a divine gift. Our
                        members personally plant trees, water them, monitor their
                        growth, and spread awareness about why harming nature is harming
                        ourselves. Tree plantation is not an event for us — it is an
                        ongoing commitment.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-tree" style="margin-right: 6px; color: var(--accent-color)"></i>
                        Regular plantation drives with post-care monitoring
                    </div>
                </div>
            </div>

            <!-- Initiative 3: Free Education -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/education/1000071263.jpg"
                    alt="Free Education for Poor Children" class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Free Education Initiative</h3>
                    <p>
                        No child should be denied education because of poverty.
                        Recognizing that many children from poor families were unable to
                        attend school, we started a free education program in our
                        village. We currently support <strong>20–25 children</strong>,
                        have appointed a dedicated teacher, and pay a monthly salary of
                        <strong>₹3,000</strong> from our own funds.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-child" style="margin-right: 6px; color: var(--accent-color)"></i>
                        20–25 children enrolled, dedicated teacher appointed
                    </div>
                </div>
            </div>

            <!-- Initiative 4: Cleanliness -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071345.jpg" alt="Cleanliness and Health Awareness"
                    class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-broom"></i>
                    </div>
                    <h3>Cleanliness & Health Awareness</h3>
                    <p>
                        Cleanliness is the foundation of good health and social dignity.
                        We run awareness drives in our community to promote sanitation,
                        hygiene, and proper waste management. We actively work to solve
                        local cleanliness problems and educate people on their
                        connection to community development.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-heartbeat" style="margin-right: 6px; color: var(--accent-color)"></i>
                        Community hygiene drives and sanitation awareness
                    </div>
                </div>
            </div>

            <!-- Initiative 5: Disaster Relief -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071344.jpg" alt="Disaster and Seasonal Relief"
                    class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Disaster & Seasonal Support</h3>
                    <p>
                        When storms rage, rains flood, or summers scorch, DPJKS
                        volunteers are among the first to respond. Our members have been
                        witnessed helping affected individuals during severe weather
                        events, distributing essentials and moral support to those most
                        vulnerable.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-shield-alt" style="margin-right: 6px; color: var(--accent-color)"></i>
                        Relief during storms, floods, and extreme heat
                    </div>
                </div>
            </div>

            <!-- Initiative 6: Religious Harmony -->
            <div class="initiative-card">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071343.jpg" alt="Religious and Social Harmony"
                    class="initiative-img" />
                <div class="initiative-body">
                    <div class="initiative-icon-wrap">
                        <i class="fas fa-praying-hands"></i>
                    </div>
                    <h3>Dharm & Samajik Samanvay</h3>
                    <p>
                        Beyond material service, DPJKS works to spread the message of
                        religious harmony, social cohesion, and unity. We believe a
                        strong, peaceful society is built on mutual respect, and every
                        one of our programs reinforces this principle — serving all
                        without discrimination of religion, caste, or class.
                    </p>
                    <div class="initiative-highlight">
                        <i class="fas fa-users" style="margin-right: 6px; color: var(--accent-color)"></i>
                        Serving all — no discrimination of religion or caste
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

<!-- ======================== EVENTS ======================== -->
<section class="section-padding" style="background: #f9f9f9">
    <div class="container">
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 3rem;
          ">
            <div>
                <h4 style="
                color: var(--accent-color);
                text-transform: uppercase;
                letter-spacing: 1px;
                font-size: 0.85rem;
              ">
                    Get Involved
                </h4>
                <h2>Activities & Programs</h2>
            </div>
            <a href="/events/" class="btn btn-secondary">View All</a>
        </div>

        <div class="home-events-grid">
            <!-- Featured -->
            <div class="featured-event">
                <img src="<?php echo USER_IMAGES ?>gallery/events/1000071342.jpg" alt="Annadaan - Daily Meal Program"
                    class="featured-bg" />
                <div class="featured-content">
                    <span class="event-badge">Daily Program</span>
                    <h3>Annadaan — Community Meal Service</h3>
                    <p style="margin-bottom: 1rem; opacity: 0.9">
                        <i class="fas fa-map-marker-alt" style="margin-right: 8px"></i>
                        Gram Hindupur Sabalpur, Shivrajpur, Kanpur
                    </p>
                    <p style="margin-bottom: 1.5rem">
                        Every day, our volunteers ensure approximately 100 people
                        receive a nutritious meal. Food is our first prayer — Anna Devo
                        Bhava.
                    </p>
                    <a href="/contact/" class="btn btn-primary" style="padding: 0.6rem 1.2rem; font-size: 0.9rem">
                        Volunteer Now
                    </a>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="recent-events-list">
                <div class="recent-event-card">
                    <div class="recent-date-box">
                        <span class="rd-num">19</span>
                        <span class="rd-month">Jan</span>
                    </div>
                    <div class="recent-info">
                        <h4>Annadaan Program Launch</h4>
                        <p>Community Meal for 100+ People Daily</p>
                        <a href="/events/" class="read-link">View Details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="recent-event-card">
                    <div class="recent-date-box">
                        <span class="rd-num">29</span>
                        <span class="rd-month">Jan</span>
                    </div>
                    <div class="recent-info">
                        <h4>Public Welfare Meal Program</h4>
                        <p>Ongoing — Village Outreach Initiative</p>
                        <a href="/events/" class="read-link">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="recent-event-card">
                    <div class="recent-date-box">
                        <span class="rd-num">12</span>
                        <span class="rd-month">Feb</span>
                    </div>
                    <div class="recent-info">
                        <h4>Tree Plantation & Care Drive</h4>
                        <p>Kanpur Outskirts — Community Forestation</p>
                        <a href="/events/" class="read-link">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="recent-event-card">
                    <div class="recent-date-box">
                        <span class="rd-num">05</span>
                        <span class="rd-month">Mar</span>
                    </div>
                    <div class="recent-info">
                        <h4>Free Education Classes Continue</h4>
                        <p>Village School — 20–25 Children Enrolled</p>
                        <a href="/events/" class="read-link">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================== TESTIMONIALS ======================== -->
<section class="section-padding" style="background: white">
    <div class="container">
        <div class="section-intro">
            <h2>Voices of Impact</h2>
            <p>
                Hear from those whose lives have been touched by our work and the
                volunteers who make it all possible.
            </p>
        </div>

        <div class="testimonials-modern">
            <div class="testi-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testi-content">
                    "During the COVID lockdown, we had no food for three days. DPJKS
                    volunteers came to our door with meals and kind words. I will
                    never forget that act of humanity. Food truly is God."
                </p>
                <div class="testi-author">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Savita Devi"
                        class="author-avatar" />
                    <div class="author-info">
                        <h4>Savita Devi</h4>
                        <span>Beneficiary, Annadaan Program</span>
                    </div>
                </div>
            </div>

            <div class="testi-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testi-content">
                    "My daughter was unable to go to school because we could not
                    afford it. DPJKS started free classes in our village. She is
                    learning to read and write now. I am grateful beyond words."
                </p>
                <div class="testi-author">
                    <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Ramesh Kumar"
                        class="author-avatar" />
                    <div class="author-info">
                        <h4>Ramesh Kumar</h4>
                        <span>Parent, Free Education Program</span>
                    </div>
                </div>
            </div>

            <div class="testi-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testi-content">
                    "I took the oath to serve selflessly, and two years later, I can
                    say it was the best decision of my life. Seeing our tree saplings
                    grow and our children learn — this is the real reward."
                </p>
                <div class="testi-author">
                    <img src="https://randomuser.me/api/portraits/men/47.jpg" alt="Suresh Mishra"
                        class="author-avatar" />
                    <div class="author-info">
                        <h4>Suresh Mishra</h4>
                        <span>DPJKS Executive Member & Volunteer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>