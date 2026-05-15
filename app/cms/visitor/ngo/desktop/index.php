<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        DPJKS - Dharam Prachar Jan Kalyan Samiti | Serving Humanity Since 2009
    </title>
    <meta name="description"
        content="Dharam Prachar Jan Kalyan Samiti (DPJKS) — A registered social welfare organization dedicated to food distribution (Annadaan), tree plantation, free education, and cleanliness awareness in Kanpur, Uttar Pradesh." />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
    <style>
    /* ========= HERO ========= */
    .hero {
        background:
            linear-gradient(135deg,
                rgba(13, 59, 102, 0.88) 0%,
                rgba(13, 59, 102, 0.65) 100%),
            url("assets/images/gallery/events/1000071350.jpg") no-repeat center center/cover;
        min-height: 90vh;
        display: flex;
        align-items: center;
        color: var(--white);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(to top, var(--bg-color), transparent);
        z-index: 2;
    }

    .hero-content {
        position: relative;
        z-index: 3;
        max-width: 860px;
        margin: 0 auto;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(244, 211, 94, 0.2);
        border: 1.5px solid var(--secondary-color);
        color: var(--secondary-color);
        padding: 0.4rem 1.2rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    .hero-content h1 {
        font-size: clamp(2.2rem, 5vw, 4rem);
        margin-bottom: 1.5rem;
        color: var(--white);
        line-height: 1.15;
    }

    .hero-content h1 span {
        color: var(--secondary-color);
    }

    .hero-content p {
        font-size: 1.15rem;
        max-width: 680px;
        margin: 0 auto 2.5rem;
        opacity: 0.9;
        line-height: 1.8;
    }

    .hero-btns {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-outline-white {
        border: 2px solid rgba(255, 255, 255, 0.8);
        color: white;
        background: transparent;
        padding: 0.8rem 1.8rem;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-outline-white:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    /* ========= STATS ========= */
    .stats-section {
        background-color: var(--white);
        transform: translateY(-50px);
        border-radius: 12px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        padding: 3rem;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 2rem;
        text-align: center;
        position: relative;
        z-index: 5;
    }

    .stat-item {
        padding: 1rem 0;
        border-right: 1px solid #f0f0f0;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-item h2 {
        font-size: 2.8rem;
        color: var(--primary-color);
        margin-bottom: 0.3rem;
        font-weight: 800;
    }

    .stat-item h2 span {
        color: var(--secondary-color);
    }

    .stat-item p {
        font-size: 0.9rem;
        color: var(--text-light);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ========= ABOUT SECTION ========= */
    .about-preview {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: center;
    }

    .about-img-wrap {
        position: relative;
    }

    .about-img-main {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .about-img-main img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        display: block;
    }

    .about-img-badge {
        position: absolute;
        bottom: -20px;
        right: -20px;
        background: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(13, 59, 102, 0.4);
        min-width: 130px;
    }

    .about-img-badge .badge-num {
        font-size: 2rem;
        font-weight: 800;
        color: var(--secondary-color);
        display: block;
        line-height: 1;
    }

    .about-img-badge .badge-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.85;
        margin-top: 0.3rem;
    }

    .about-eyebrow {
        color: var(--accent-color);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: block;
    }

    .about-text h2 {
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .about-text p {
        color: var(--text-light);
        margin-bottom: 1.2rem;
        line-height: 1.8;
    }

    .values-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem;
        margin: 1.5rem 0 2rem;
    }

    .values-list li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-main);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .values-list li i {
        color: var(--secondary-color);
        font-size: 0.85rem;
        background: #fffbea;
        padding: 4px;
        border-radius: 50%;
    }

    /* ========= INITIATIVES SECTION ========= */
    .initiatives-section {
        background: linear-gradient(135deg, #f0f4f8 0%, #e8f0f8 100%);
    }

    .section-intro {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 3.5rem;
    }

    .section-intro h2 {
        margin-bottom: 1rem;
        font-size: 2.2rem;
    }

    .section-intro p {
        color: var(--text-light);
        line-height: 1.8;
    }

    .initiatives-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    @media (max-width: 900px) {
        .initiatives-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 580px) {
        .initiatives-grid {
            grid-template-columns: 1fr;
        }
    }

    .initiative-card {
        background: var(--white);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;
        border: 1px solid #eef2f7;
    }

    .initiative-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .initiative-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .initiative-body {
        padding: 1.8rem;
    }

    .initiative-icon-wrap {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, var(--primary-color), #1a5fa6);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        margin-bottom: 1.2rem;
        box-shadow: 0 6px 15px rgba(13, 59, 102, 0.35);
    }

    .initiative-body h3 {
        font-size: 1.25rem;
        margin-bottom: 0.7rem;
        color: var(--primary-color);
    }

    .initiative-body p {
        color: var(--text-light);
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    .initiative-highlight {
        background: #f8fbff;
        border-left: 3px solid var(--secondary-color);
        padding: 0.6rem 0.9rem;
        border-radius: 0 6px 6px 0;
        font-size: 0.88rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    /* ========= OUR STORY / HISTORY SECTION ========= */
    .story-section {
        background: var(--white);
    }

    .story-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: start;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: "";
        position: absolute;
        left: 7px;
        top: 6px;
        width: 2px;
        height: calc(100% - 12px);
        background: linear-gradient(to bottom,
                var(--secondary-color),
                var(--primary-color));
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2.5rem;
    }

    .timeline-dot {
        position: absolute;
        left: -2rem;
        top: 4px;
        width: 16px;
        height: 16px;
        background: var(--secondary-color);
        border-radius: 50%;
        border: 3px solid var(--white);
        box-shadow: 0 0 0 2px var(--secondary-color);
    }

    .timeline-year {
        display: inline-block;
        background: var(--primary-color);
        color: var(--white);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.15rem 0.6rem;
        border-radius: 4px;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .timeline-item h4 {
        font-size: 1.1rem;
        color: var(--primary-color);
        margin-bottom: 0.4rem;
    }

    .timeline-item p {
        color: var(--text-light);
        font-size: 0.93rem;
        line-height: 1.7;
    }

    .story-quote {
        background: linear-gradient(135deg, var(--primary-color), #1a5fa6);
        color: white;
        padding: 2.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .story-quote::before {
        content: "\201C";
        font-size: 8rem;
        color: rgba(255, 255, 255, 0.08);
        position: absolute;
        top: -20px;
        left: 10px;
        line-height: 1;
        font-family: Georgia, serif;
    }

    .story-quote p {
        font-size: 1.1rem;
        line-height: 1.8;
        font-style: italic;
        position: relative;
        z-index: 1;
    }

    .story-quote .quote-author {
        margin-top: 1.5rem;
        font-weight: 700;
        color: var(--secondary-color);
        font-style: normal;
        font-size: 0.95rem;
    }

    .story-imgs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .story-imgs img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* ========= OATH / VALUES SECTION ========= */
    .oath-section {
        background: linear-gradient(135deg,
                var(--primary-color) 0%,
                #1a50a0 100%);
        color: white;
        padding: 6rem 0;
        text-align: center;
    }

    .oath-section h2 {
        color: white;
        font-size: 2.2rem;
        margin-bottom: 1rem;
    }

    .oath-section>.container>p {
        color: rgba(255, 255, 255, 0.85);
        max-width: 600px;
        margin: 0 auto 3rem;
        line-height: 1.8;
    }

    .oath-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
        margin-top: 3rem;
    }

    @media (max-width: 1100px) {
        .oath-cards {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Center last 2 items in a 3-col grid */
        .oath-cards .oath-card:nth-child(4) {
            grid-column: 1 / 2;
            justify-self: end;
        }

        .oath-cards .oath-card:nth-child(5) {
            grid-column: 2 / 3;
            justify-self: start;
        }
    }

    @media (max-width: 600px) {
        .oath-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .oath-cards .oath-card:nth-child(4) {
            grid-column: auto;
            justify-self: auto;
        }

        .oath-cards .oath-card:nth-child(5) {
            grid-column: 1 / -1;
            justify-self: center;
            max-width: 50%;
        }
    }

    .oath-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 2rem 1.5rem;
        transition: background 0.3s;
        backdrop-filter: blur(5px);
    }

    .oath-card:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .oath-card i {
        font-size: 2rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
        display: block;
    }

    .oath-card h4 {
        color: white;
        font-size: 1.05rem;
        margin-bottom: 0.5rem;
    }

    .oath-card p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.88rem;
        line-height: 1.6;
    }

    /* ========= TESTIMONIALS ========= */
    .testimonials-modern {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        margin-top: 3rem;
    }

    @media (max-width: 900px) {
        .testimonials-modern {
            grid-template-columns: 1fr;
        }
    }

    .testi-card {
        background: var(--white);
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        position: relative;
        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;
        border-top: 5px solid var(--secondary-color);
    }

    .testi-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .quote-icon {
        font-size: 4rem;
        color: rgba(0, 0, 0, 0.05);
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .testi-content {
        font-size: 1rem;
        color: #555;
        margin-bottom: 2rem;
        font-style: italic;
        line-height: 1.7;
    }

    .testi-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .author-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background-color: #ddd;
        object-fit: cover;
    }

    .author-info h4 {
        margin: 0;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .author-info span {
        font-size: 0.82rem;
        color: #888;
    }

    /* ========= CTA SECTION ========= */
    .donation-mature {
        background:
            linear-gradient(rgba(13, 59, 102, 0.92), rgba(13, 59, 102, 0.85)),
            url("assets/images/gallery/education/1000071263.jpg") center/cover fixed;
        color: white;
        padding: 7rem 0;
        text-align: center;
    }

    .donation-mature h2 {
        color: white;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .donation-mature p {
        color: rgba(255, 255, 255, 0.9);
        max-width: 680px;
        margin: 0 auto 2.5rem;
        font-size: 1.15rem;
        line-height: 1.8;
    }

    /* ========= RESPONSIVE ========= */
    @media (max-width: 900px) {

        .about-preview,
        .story-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        .about-img-badge {
            right: 0;
            bottom: -15px;
        }

        .stat-item {
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .values-list {
            grid-template-columns: 1fr;
        }

        .home-events-grid {
            grid-template-columns: 1fr;
        }

        .stats-section {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 600px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .stats-section {
            padding: 2rem;
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* ========= EVENTS ========= */
    .home-events-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 3rem;
    }

    .featured-event {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        height: 100%;
        min-height: 480px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .featured-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .featured-event:hover .featured-bg {
        transform: scale(1.05);
    }

    .featured-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        padding: 4rem 2rem 2rem;
        color: white;
    }

    .event-badge {
        background: var(--secondary-color);
        color: var(--primary-color);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .featured-content h3 {
        color: white;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .recent-events-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .recent-event-card {
        display: flex;
        gap: 1.5rem;
        background: var(--white);
        padding: 1rem 1.2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
        border: 1px solid #f0f0f0;
    }

    .recent-event-card:hover {
        transform: translateX(5px);
        border-color: var(--secondary-color);
    }

    .recent-date-box {
        background: var(--primary-color);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        min-width: 65px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .rd-num {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .rd-month {
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .recent-info h4 {
        margin-bottom: 0.25rem;
        font-size: 1rem;
    }

    .recent-info p {
        font-size: 0.88rem;
        color: #666;
        margin-bottom: 0.3rem;
    }

    .read-link {
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--accent-color);
    }
    </style>
</head>

<body>
    <div id="header-placeholder"></div>

    <!-- ======================== HERO ======================== -->
    <section class="hero">
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
                <a href="donation.html" class="btn btn-secondary">Support Our Cause</a>
                <a href="about.html" class="btn-outline-white">Our Journey</a>
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
                    <img src="assets/images/gallery/events/1000071341.jpg" alt="DPJKS team serving community" />
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

                <a href="about.html" class="btn btn-primary">Read Our Full Story</a>
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
                    <img src="assets/images/gallery/events/1000071337.jpg" alt="Annadaan - Food Distribution"
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
                    <img src="assets/images/gallery/events/1000071336.jpg" alt="Tree Plantation Drive"
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
                    <img src="assets/images/gallery/education/1000071263.jpg" alt="Free Education for Poor Children"
                        class="initiative-img" />
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
                    <img src="assets/images/gallery/events/1000071345.jpg" alt="Cleanliness and Health Awareness"
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
                    <img src="assets/images/gallery/events/1000071344.jpg" alt="Disaster and Seasonal Relief"
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
                    <img src="assets/images/gallery/events/1000071343.jpg" alt="Religious and Social Harmony"
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
                        <img src="assets/images/gallery/events/1000071348.jpg" alt="DPJKS community work" />
                        <img src="assets/images/gallery/events/1000071349.jpg" alt="DPJKS members serving" />
                        <img src="assets/images/gallery/events/1000071351.jpg" alt="DPJKS events" />
                        <img src="assets/images/gallery/events/1000071352.jpg" alt="DPJKS activities" />
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
                <a href="events.html" class="btn btn-secondary">View All</a>
            </div>

            <div class="home-events-grid">
                <!-- Featured -->
                <div class="featured-event">
                    <img src="assets/images/gallery/events/1000071342.jpg" alt="Annadaan - Daily Meal Program"
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
                        <a href="contact.html" class="btn btn-primary"
                            style="padding: 0.6rem 1.2rem; font-size: 0.9rem">
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
                            <a href="events.html" class="read-link">View Details <i class="fas fa-arrow-right"></i></a>
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
                            <a href="events.html" class="read-link">View <i class="fas fa-arrow-right"></i></a>
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
                            <a href="events.html" class="read-link">View <i class="fas fa-arrow-right"></i></a>
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
                            <a href="events.html" class="read-link">View <i class="fas fa-arrow-right"></i></a>
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

    <!-- ======================== CTA ======================== -->
    <section class="donation-mature">
        <div class="container">
            <h2>Join Us in Serving Humanity</h2>
            <p>
                Whether you wish to donate, volunteer your time, or simply spread the
                word — every act of support helps us serve 100 more meals, educate one
                more child, and plant one more tree. Together, we can write a brighter
                story for India's villages.
            </p>
            <div style="
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
          ">
                <a href="donation.html" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size: 1.1rem">
                    <i class="fas fa-gift" style="margin-right: 8px"></i>Make a Donation
                </a>
                <a href="contact.html" class="btn btn-outline-white" style="padding: 1rem 2.5rem; font-size: 1.1rem">
                    <i class="fas fa-hands-helping" style="margin-right: 8px"></i>Become
                    a Volunteer
                </a>
            </div>
        </div>
    </section>

    <div id="footer-placeholder"></div>
    <script src="<?php echo USER_JS ?>main.js"></script>
</body>

</html>