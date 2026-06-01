<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - DPJKS | Our Story, Mission & Values</title>
    <meta
      name="description"
      content="Learn about Dharam Prachar Jan Kalyan Samiti — our founding story since 2009, office bearers, core values, oath, and commitment to serving humanity with Annadaan, free education, tree plantation, and cleanliness awareness."
    />
    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <!-- Icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css?v=<?= time() ?>" />
    <style>
      .page-header {
        background:
          linear-gradient(
            135deg,
            rgba(13, 59, 102, 0.92),
            rgba(13, 59, 102, 0.75)
          ),
          url("assets/images/gallery/events/1000071350.jpg") center/cover;
        padding: 6rem 0;
        color: white;
        text-align: center;
      }

      .page-header h1 {
        color: white;
        font-size: 3rem;
        margin-bottom: 1rem;
      }
      .page-header p {
        opacity: 0.9;
        max-width: 600px;
        margin: 0.5rem auto 0;
        font-size: 1.1rem;
        line-height: 1.7;
      }

      /* ----- About Intro Grid ----- */
      .about-intro-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: center;
      }

      .about-img-stack {
        position: relative;
      }

      .img-primary {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
      }

      .img-secondary {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      }

      .eyebrow {
        color: var(--accent-color);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.82rem;
        font-weight: 700;
        display: block;
        margin-bottom: 0.8rem;
      }

      .about-text h2 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        line-height: 1.2;
      }

      .about-text p {
        color: var(--text-light);
        margin-bottom: 1.2rem;
        line-height: 1.85;
      }

      /* ----- Philosophy Section ----- */
      .philosophy-section {
        background: linear-gradient(135deg, #f0f4f8, #e8f2ff);
      }

      .philosophy-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3rem;
      }
      @media (max-width: 900px) {
        .philosophy-grid {
          grid-template-columns: 1fr;
        }
      }

      .philosophy-card {
        background: white;
        border-radius: 14px;
        padding: 2.2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        text-align: center;
        transition: transform 0.3s;
        border-top: 4px solid var(--secondary-color);
      }

      .philosophy-card:hover {
        transform: translateY(-6px);
      }

      .phi-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-color), #1a5fa6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.6rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 8px 20px rgba(13, 59, 102, 0.4);
      }

      .philosophy-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.8rem;
      }
      .philosophy-card p {
        color: var(--text-light);
        font-size: 0.95rem;
        line-height: 1.7;
      }

      /* ----- History Section ----- */
      .story-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: start;
      }

      .timeline {
        position: relative;
        padding-left: 2.5rem;
      }

      .timeline::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 8px;
        width: 2px;
        height: calc(100% - 16px);
        background: linear-gradient(
          to bottom,
          var(--secondary-color),
          var(--primary-color)
        );
      }

      .timeline-item {
        position: relative;
        margin-bottom: 2.5rem;
      }

      .tl-dot {
        position: absolute;
        left: -2.5rem;
        top: 4px;
        width: 18px;
        height: 18px;
        background: var(--secondary-color);
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px var(--secondary-color);
      }

      .tl-year {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.2rem 0.7rem;
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

      .story-images {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
      }

      .story-images img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        border-radius: 10px;
      }

      /* ----- Office Bearers ----- */
      .bearers-section {
        background: white;
      }

      .bearers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
        margin-top: 3rem;
      }
      @media (max-width: 900px) {
        .bearers-grid {
          grid-template-columns: repeat(3, 1fr);
        }
        /* Center the last 2 items when 5 items fall into a 3-col grid */
        .bearers-grid::after {
          content: "";
          display: block;
        }
        .bearers-grid .bearer-card:nth-child(4) {
          grid-column: 1 / 2;
          justify-self: end;
        }
        .bearers-grid .bearer-card:nth-child(5) {
          grid-column: 2 / 3;
          justify-self: start;
        }
      }
      @media (max-width: 600px) {
        .bearers-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        .bearers-grid .bearer-card:nth-child(4) {
          grid-column: auto;
          justify-self: auto;
        }
        .bearers-grid .bearer-card:nth-child(5) {
          grid-column: 1 / -1;
          justify-self: center;
          max-width: 50%;
        }
      }

      .bearer-card {
        background: #f8fafb;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
      }

      .bearer-card:hover {
        background: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transform: translateY(-4px);
        border-color: var(--secondary-color);
      }

      .bearer-role-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), #1a5fa6);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
        font-size: 1.4rem;
        margin: 0 auto 1.2rem;
      }

      .bearer-card .role-title {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--accent-color);
        font-weight: 700;
        margin-bottom: 0.3rem;
        display: block;
      }

      .bearer-card h4 {
        font-size: 1.1rem;
        color: var(--primary-color);
        margin: 0;
      }

      /* ----- Oath Section ----- */
      .oath-full {
        background: linear-gradient(135deg, var(--primary-color), #1a50a0);
        color: white;
        padding: 6rem 0;
      }

      .oath-full h2 {
        color: white;
        text-align: center;
        font-size: 2rem;
        margin-bottom: 1rem;
      }
      .oath-intro {
        text-align: center;
        color: rgba(255, 255, 255, 0.85);
        max-width: 650px;
        margin: 0 auto 3rem;
        line-height: 1.8;
      }

      .oath-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
      }
      @media (max-width: 900px) {
        .oath-grid {
          grid-template-columns: repeat(3, 1fr);
        }
        /* Center last 2 items in a 3-col grid */
        .oath-grid .oath-item:nth-child(4) {
          grid-column: 1 / 2;
          justify-self: end;
        }
        .oath-grid .oath-item:nth-child(5) {
          grid-column: 2 / 3;
          justify-self: start;
        }
      }
      @media (max-width: 640px) {
        .oath-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        .oath-grid .oath-item:nth-child(4) {
          grid-column: auto;
          justify-self: auto;
        }
        .oath-grid .oath-item:nth-child(5) {
          grid-column: 1 / -1;
          justify-self: center;
          max-width: 50%;
        }
      }

      .oath-item {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 2rem;
        backdrop-filter: blur(5px);
        transition: background 0.3s;
      }

      .oath-item:hover {
        background: rgba(255, 255, 255, 0.18);
      }

      .oath-item i {
        font-size: 1.8rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
        display: block;
      }

      .oath-item h4 {
        color: white;
        font-size: 1rem;
        margin-bottom: 0.5rem;
      }
      .oath-item p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.88rem;
        line-height: 1.6;
      }

      /* ----- Legal Box ----- */
      .legal-box {
        background: #f8f9fa;
        border-left: 4px solid var(--secondary-color);
        padding: 2.5rem;
        border-radius: 8px;
      }

      .legal-box h3 {
        margin-bottom: 1.2rem;
      }

      .legal-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        padding: 0.7rem 0;
        border-bottom: 1px solid #ececec;
        font-size: 0.97rem;
        color: var(--text-main);
      }

      .legal-list li:last-child {
        border-bottom: none;
      }

      .legal-list li i {
        color: #27ae60;
        margin-top: 2px;
        flex-shrink: 0;
      }

      /* ----- Responsive ----- */
      @media (max-width: 900px) {
        .about-intro-grid,
        .story-grid {
          grid-template-columns: 1fr;
          gap: 3rem;
        }
        .img-secondary {
          display: none;
        }
        .philosophy-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>About DPJKS</h1>
        <p>
          The story of a group of ordinary people who chose service over silence
          — and built something extraordinary.
        </p>
      </div>
    </header>

    <!-- ====== ABOUT INTRO ====== -->
    <section class="section-padding">
      <div class="container about-intro-grid">
        <div class="about-img-stack">
          <img
            src="assets/images/gallery/events/1000071341.jpg"
            alt="DPJKS volunteers at work"
            class="img-primary"
          />
          <img
            src="assets/images/gallery/education/1000071263.jpg"
            alt="Free education for children"
            class="img-secondary"
          />
        </div>

        <div class="about-text">
          <span class="eyebrow">Our Identity</span>
          <h2>Dharam Prachar Jan Kalyan Samiti</h2>
          <p>
            <strong>DPJKS</strong> was formed with one powerful intention: to
            serve religion, society, and humanity. Looking around at social
            conflicts, misunderstandings, and the suffering of people, our
            founders made a conscious decision — instead of fighting, they would
            serve.
          </p>
          <p>
            Since 2009, that decision has grown into a living organization. We
            are a registered social welfare body (Reg. No.
            <strong>K-46259</strong>) based in Gram Hindupur Sabalpur,
            Shivrajpur, Kanpur Nagar, Uttar Pradesh.
          </p>
          <p>
            Our members work across multiple programs — food distribution, tree
            plantation, free education, cleanliness awareness, and disaster
            relief — all driven by a shared oath of selfless, honest service.
          </p>
          <p>
            We believe that <strong>nature is a form of God</strong>, that
            <strong>food is life</strong>, that
            <strong>education is a birthright</strong>, and that service to
            humanity is the highest form of devotion.
          </p>
        </div>
      </div>
    </section>

    <!-- ====== VISION MISSION VALUES ====== -->
    <section class="section-padding philosophy-section">
      <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto">
          <span class="eyebrow">Our Foundation</span>
          <h2>Vision, Mission & Core Values</h2>
          <p style="color: var(--text-light); line-height: 1.8">
            Every program, every action, and every rupee we spend flows from
            these guiding principles.
          </p>
        </div>

        <div class="philosophy-grid">
          <div class="philosophy-card">
            <div class="phi-icon"><i class="fas fa-eye"></i></div>
            <h3>Our Vision</h3>
            <p>
              To build a socially aware, educated and self-reliant society —
              where every individual lives with dignity, and no one is left
              behind due to poverty, ignorance, or neglect.
            </p>
          </div>

          <div class="philosophy-card">
            <div class="phi-icon"><i class="fas fa-bullseye"></i></div>
            <h3>Our Mission</h3>
            <p>
              To actively serve the underprivileged through food distribution,
              free education, environmental protection, and community welfare
              programs — with compassion, Unity, and complete selflessness.
            </p>
          </div>

          <div class="philosophy-card">
            <div class="phi-icon"><i class="fas fa-heart"></i></div>
            <h3>Our Values</h3>
            <p>
              <strong>Seva</strong> (service),
              <strong>Samarpan</strong> (dedication),
              <strong>Ekta</strong> (unity),
              <strong>Karuna</strong> (compassion), and
              <strong>Nishpaksh</strong> (impartiality) — these are the pillars
              on which DPJKS stands.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ====== HISTORY / TIMELINE ====== -->
    <section class="section-padding" style="background: white">
      <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 4rem">
          <span class="eyebrow">Our Journey</span>
          <h2>From Vision to Action — Our History</h2>
          <p style="color: var(--text-light); line-height: 1.8">
            Our path was not without challenges. Opposition, hardship, and
            setbacks were many. But the flame of seva never went out.
          </p>
        </div>

        <div class="story-grid">
          <div>
            <div class="story-images">
              <img
                src="assets/images/gallery/events/1000071330.jpg"
                alt="DPJKS community gathering"
              />
              <img
                src="assets/images/gallery/events/1000071334.jpg"
                alt="DPJKS service event"
              />
              <img
                src="assets/images/gallery/events/1000071347.jpg"
                alt="DPJKS food distribution"
              />
              <img
                src="assets/images/gallery/events/1000071353.jpg"
                alt="DPJKS members together"
              />
            </div>

            <div
              style="
                background: linear-gradient(
                  135deg,
                  var(--primary-color),
                  #1a5fa6
                );
                color: white;
                padding: 2.5rem;
                border-radius: 16px;
                position: relative;
                overflow: hidden;
              "
            >
              <div
                style="
                  font-size: 5rem;
                  color: rgba(255, 255, 255, 0.08);
                  position: absolute;
                  top: -10px;
                  left: 10px;
                  font-family: Georgia, serif;
                  line-height: 1;
                "
              >
                "
              </div>
              <p
                style="
                  font-style: italic;
                  line-height: 1.8;
                  font-size: 1.05rem;
                  position: relative;
                  z-index: 1;
                "
              >
                "Our struggle was not just external — it was also internal.
                Every setback made us question ourselves. But every smile from a
                hungry person who received food, every child who learned to
                write their name — those moments gave us the strength to carry
                on."
              </p>
              <p
                style="
                  margin-top: 1.2rem;
                  color: var(--secondary-color);
                  font-weight: 700;
                  font-style: normal;
                  font-size: 0.95rem;
                  position: relative;
                  z-index: 1;
                "
              >
                — A Founding Member, DPJKS
              </p>
            </div>
          </div>

          <div class="timeline">
            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">~2009</span>
              <h4>The Beginning — "Why Not Serve?"</h4>
              <p>
                A group of individuals, deeply troubled by social conflicts and
                human suffering, decided to take a different path. Instead of
                engaging in disputes, they chose seva. The informal movement
                DPJKS was born.
              </p>
            </div>

            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">2009 – 2011</span>
              <h4>Spreading Awareness, Facing Opposition</h4>
              <p>
                Members worked to peacefully share their ideas of social welfare
                and religious harmony. While they faced opposition and
                misunderstanding, they never lost hope. They continued spreading
                awareness and expanding their community.
              </p>
            </div>

            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">2012</span>
              <h4>Formal Registration</h4>
              <p>
                DPJKS was officially registered as a social organization (Reg.
                No. K-46259) under the Societies Registration Act, 1860. A full
                governance structure was established — President, Vice
                President, Secretary, Treasurer, and Executive Members — all
                taking a solemn oath of honest and selfless service.
              </p>
            </div>

            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">2020–21</span>
              <h4>COVID-19 Food Relief</h4>
              <p>
                During the devastating COVID-19 lockdown, countless families in
                the region went without food. DPJKS mobilized its volunteers to
                distribute meals and rations to needy families, acting on the
                belief that Anna (food) is life — and life cannot wait.
              </p>
            </div>

            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">Jan 2023</span>
              <h4>Annadaan — Daily Meals Begin</h4>
              <p>
                On 19 January 2023, DPJKS launched its formal community meal
                program — serving approximately 100 people per day. A landmark
                milestone, this program runs continuously to the present day
                without a single interruption.
              </p>
            </div>

            <div class="timeline-item">
              <div class="tl-dot"></div>
              <span class="tl-year">2023–Present</span>
              <h4>Education & Environment Programs</h4>
              <p>
                With growing support, DPJKS launched free education classes for
                20–25 underprivileged children — appointing a teacher paid
                ₹3,000/month from community funds. Simultaneously, regular tree
                plantation drives with post-care monitoring became a central
                pillar of our environment work.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ====== OFFICE BEARERS ====== -->
    <section class="section-padding" style="background: #f8fafb">
      <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 1rem">
          <span class="eyebrow">Governance</span>
          <h2>Our Office Bearers</h2>
          <p style="color: var(--text-light); line-height: 1.8">
            DPJKS is governed by a dedicated team of individuals who have
            pledged their time and effort to serving the community honestly and
            selflessly.
          </p>
        </div>

        <div class="bearers-grid">
          <div class="bearer-card">
            <div class="bearer-role-icon"><i class="fas fa-crown"></i></div>
            <span class="role-title">President</span>
            <h4>Presiding Leader</h4>
          </div>
          <div class="bearer-card">
            <div class="bearer-role-icon">
              <i class="fas fa-user-shield"></i>
            </div>
            <span class="role-title">Vice President</span>
            <h4>Strategic Support</h4>
          </div>
          <div class="bearer-card">
            <div class="bearer-role-icon"><i class="fas fa-pen-nib"></i></div>
            <span class="role-title">Secretary</span>
            <h4>Administration & Records</h4>
          </div>
          <div class="bearer-card">
            <div class="bearer-role-icon"><i class="fas fa-coins"></i></div>
            <span class="role-title">Treasurer</span>
            <h4>Financial Oversight</h4>
          </div>
          <div class="bearer-card">
            <div class="bearer-role-icon"><i class="fas fa-users"></i></div>
            <span class="role-title">Executive Members</span>
            <h4>Field Operations Team</h4>
          </div>
        </div>

        <p
          style="
            text-align: center;
            color: var(--text-light);
            margin-top: 2rem;
            font-size: 0.95rem;
            font-style: italic;
          "
        >
          All office bearers take a formal pledge to serve honestly, work
          selflessly, and uphold the values of the organization at all times.
        </p>
      </div>
    </section>

    <!-- ====== OATH ====== -->
    <section class="oath-full">
      <div class="container">
        <h2>The Oath of DPJKS Members</h2>
        <p class="oath-intro">
          Every person who joins DPJKS — from office bearers to foot soldiers —
          makes a collective promise. This is not just a formality. It is the
          moral spine of everything we do.
        </p>

        <div class="oath-grid">
          <div class="oath-item">
            <i class="fas fa-hand-paper"></i>
            <h4>Seva Nishtha (Service Commitment)</h4>
            <p>
              To serve society sincerely and tirelessly, placing the needs of
              the community before personal gain or comfort.
            </p>
          </div>
          <div class="oath-item">
            <i class="fas fa-om"></i>
            <h4>Dharm Sadbhavna (Harmony)</h4>
            <p>
              To work for religious harmony and to treat all faiths with equal
              respect, serving every human being without discrimination.
            </p>
          </div>
          <div class="oath-item">
            <i class="fas fa-balance-scale-right"></i>
            <h4>Samajik Nyay (Social Justice)</h4>
            <p>
              To promote social welfare and stand with the needy, the helpless,
              and the marginalized — ensuring no one is forgotten.
            </p>
          </div>
          <div class="oath-item">
            <i class="fas fa-star"></i>
            <h4>Nishtha aur Anushasan (Integrity)</h4>
            <p>
              To conduct all work with strong ethical standards, discipline, and
              transparency that the public and beneficiaries can trust.
            </p>
          </div>
          <div class="oath-item">
            <i class="fas fa-leaf"></i>
            <h4>Prakriti Prem (Nature Love)</h4>
            <p>
              To protect and nurture the natural world — planting trees,
              preventing harm to nature, and spreading environmental awareness.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ====== LEGAL ====== -->
    <section class="section-padding" style="background: white">
      <div class="container">
        <div style="max-width: 800px; margin: 0 auto">
          <div class="legal-box">
            <h3>
              <i
                class="fas fa-certificate"
                style="color: var(--secondary-color); margin-right: 10px"
              ></i
              >Legal Registration & Transparency
            </h3>
            <p style="color: var(--text-light); margin-bottom: 1.5rem">
              DPJKS is fully registered and committed to operating with complete
              transparency in all financial and organizational matters.
            </p>
            <ul class="legal-list">
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>Organization Name:</strong> Dharam Prachar Jan Kalyan
                  Samiti (DPJKS)
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>Registration Number:</strong> K-46259 (Societies
                  Registration Act, 1860)
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>State of Operation:</strong> Uttar Pradesh, India
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>Organization Type:</strong> Non-Governmental
                  Organization / Registered Society
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>Headquarters:</strong> Gram Hindupur Sabalpur,
                  Shivrajpur, Kanpur Nagar, UP
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong>Governance:</strong> President, Vice President,
                  Secretary, Treasurer, and Executive Members
                </div>
              </li>
            </ul>
          </div>

          <div style="text-align: center; margin-top: 3rem">
            <h2 style="margin-bottom: 1rem">Support Our Mission</h2>
            <p
              style="
                color: var(--text-light);
                margin-bottom: 2rem;
                line-height: 1.8;
              "
            >
              Whether you donate, volunteer, or simply share our work — your
              support makes a direct difference to real lives.
            </p>
            <div
              style="
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
              "
            >
              <a href="donation.html" class="btn btn-primary">Donate Now</a>
              <a href="contact.html" class="btn btn-secondary"
                >Become a Volunteer</a
              >
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>
    <script src="<?php echo USER_JS ?>main.js?v=<?= time() ?>"></script>
  </body>
</html>
