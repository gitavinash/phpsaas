<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
      Our Programs - DPJKS | Annadaan, Education, Environment & More
    </title>
    <meta
      name="description"
      content="Explore DPJKS programs: daily Annadaan (food distribution), free education for 20-25 children, tree plantation, cleanliness awareness, disaster relief, and religious harmony initiatives in Kanpur, UP."
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="css/style.css" />
    <style>
      .page-header {
        background:
          linear-gradient(
            135deg,
            rgba(13, 59, 102, 0.9),
            rgba(13, 59, 102, 0.75)
          ),
          url("assets/images/gallery/events/1000071337.jpg") center/cover;
        padding: 6rem 0;
        color: white;
        text-align: center;
      }
      .page-header h1 {
        color: white;
        font-size: 2.8rem;
        margin-bottom: 1rem;
      }
      .page-header p {
        opacity: 0.9;
        max-width: 620px;
        margin: 0 auto;
        font-size: 1.1rem;
        line-height: 1.7;
      }

      /* ---- Program cards ---- */
      .programs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        margin-top: 3rem;
      }
      @media (max-width: 1024px) {
        .programs-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (max-width: 640px) {
        .programs-grid {
          grid-template-columns: 1fr;
        }
      }

      .program-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        transition:
          transform 0.3s,
          box-shadow 0.3s;
        border: 1px solid #eef2f9;
        display: flex;
        flex-direction: column;
      }

      .program-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
      }

      .prog-img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        display: block;
      }

      .prog-body {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
      }

      .prog-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #eef5ff;
        color: var(--primary-color);
        padding: 0.3rem 0.9rem;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.2rem;
        width: fit-content;
      }

      .prog-body h3 {
        font-size: 1.35rem;
        color: var(--primary-color);
        margin-bottom: 0.9rem;
      }
      .prog-body p {
        color: var(--text-light);
        line-height: 1.75;
        margin-bottom: 1rem;
        font-size: 0.95rem;
        flex: 1;
      }

      .prog-facts {
        background: #f8fbff;
        border-radius: 8px;
        padding: 1rem 1.2rem;
        margin-bottom: 1.2rem;
      }

      .prog-facts li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        color: var(--text-main);
        font-size: 0.88rem;
        padding: 0.35rem 0;
        border-bottom: 1px solid #eef2f9;
      }

      .prog-facts li:last-child {
        border-bottom: none;
      }
      .prog-facts li i {
        color: var(--accent-color);
        margin-top: 2px;
        flex-shrink: 0;
      }

      /* ---- Impact Banner ---- */
      .impact-banner {
        background: linear-gradient(135deg, var(--primary-color), #1a5fa6);
        color: white;
        border-radius: 20px;
        padding: 3rem;
        margin: 5rem 0 0;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 2rem;
        text-align: center;
      }
      @media (max-width: 1024px) {
        .impact-banner {
          grid-template-columns: repeat(3, 1fr);
        }
        /* On 3-col with 5 items: 3+2. Center the last 2 */
        .impact-banner .impact-item:nth-child(4) {
          grid-column: 1 / 2;
        }
      }
      @media (max-width: 600px) {
        .impact-banner {
          grid-template-columns: repeat(2, 1fr);
        }
        .impact-banner .impact-item:nth-child(4) {
          grid-column: auto;
        }
        .impact-banner .impact-item:nth-child(5) {
          grid-column: 1 / -1;
        }
      }

      .impact-item h3 {
        color: var(--secondary-color);
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.3rem;
      }

      .impact-item p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.88rem;
        text-transform: uppercase;
        letter-spacing: 1px;
      }

      /* ---- Support CTA ---- */
      .support-cta {
        background: #f8fafb;
        border-radius: 20px;
        padding: 4rem;
        text-align: center;
        margin-top: 5rem;
        border: 1px solid #eef2f9;
      }

      .support-cta h2 {
        margin-bottom: 1rem;
      }
      .support-cta p {
        color: var(--text-light);
        max-width: 580px;
        margin: 0 auto 2rem;
        line-height: 1.8;
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>Our Programs & Causes</h1>
        <p>
          From daily meals to tree saplings, from classroom education to
          community cleanliness — every program we run is backed by a solemn
          pledge and real human effort.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container">
        <div style="text-align: center; max-width: 700px; margin: 0 auto">
          <h4
            style="
              color: var(--accent-color);
              text-transform: uppercase;
              letter-spacing: 2px;
              font-size: 0.85rem;
            "
          >
            What We Do
          </h4>
          <h2>Six Pillars of Service</h2>
          <p style="color: var(--text-light); line-height: 1.8">
            DPJKS doesn't just talk about change — we create it through
            sustained, measurable programs that directly help the people who
            need it most.
          </p>
        </div>

        <div class="programs-grid">
          <!-- 1. Annadaan -->
          <div class="program-card">
            <img
              src="assets/images/gallery/events/1000071337.jpg"
              alt="Annadaan Food Program"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-utensils"></i> Food Distribution</span
              >
              <h3>Annadaan — Anna Devo Bhava</h3>
              <p>
                Food is not charity — it is life. Our Annadaan program began
                during the COVID-19 lockdown when hunger was rampant. What
                started as emergency relief has become a permanent, daily
                commitment. Since 19 January 2023, we have served meals every
                single day without interruption.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>~100 meals served every day</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Launched: 19 January 2023</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Served during COVID-19 lockdown</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Continues without interruption to date</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- 2. Tree Plantation -->
          <div class="program-card">
            <img
              src="assets/images/gallery/events/1000071336.jpg"
              alt="Tree Plantation Drive"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-seedling"></i> Environment</span
              >
              <h3>Vriksharopan — Nature is God</h3>
              <p>
                DPJKS believes nature is a manifestation of the divine — and
                must be protected. Our tree plantation program is not a one-time
                event. Members personally plant trees, water them regularly,
                monitor their growth, and ensure each sapling survives to become
                a tree. We also actively spread awareness about not harming the
                natural world.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Regular plantation drives across Kanpur region</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Post-plantation care & growth monitoring</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Community awareness about environmental protection</span
                  >
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Members personally responsible for sapling survival</span
                  >
                </li>
              </ul>
            </div>
          </div>

          <!-- 3. Free Education -->
          <div class="program-card">
            <img
              src="assets/images/gallery/education/1000071263.jpg"
              alt="Free Education for Children"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-graduation-cap"></i> Education</span
              >
              <h3>Nishulk Shiksha — Free Education</h3>
              <p>
                No child should remain uneducated merely because their family
                cannot afford school fees. Observing that many children in our
                village were being denied education due to poverty, DPJKS
                established a free education program — complete with a dedicated
                teacher paid from our own community funds.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>20–25 children enrolled from poor families</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Dedicated teacher appointed for the program</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>₹3,000/month teacher salary paid by DPJKS</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Running in the home village</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- 4. Cleanliness -->
          <div class="program-card">
            <img
              src="assets/images/gallery/events/1000071345.jpg"
              alt="Cleanliness Awareness"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-broom"></i> Public Health</span
              >
              <h3>Swachh Samaj — Cleanliness Drives</h3>
              <p>
                We believe cleanliness is the foundation of good health, social
                equality, and community progress. DPJKS runs active awareness
                drives to promote sanitation, proper hygiene, and responsible
                waste management among community members — and works directly on
                solving local cleanliness problems.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Community-level sanitation awareness campaigns</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Hygiene education for families and children</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Direct intervention on local cleanliness issues</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Promoting connection between cleanliness and health</span
                  >
                </li>
              </ul>
            </div>
          </div>

          <!-- 5. Disaster Relief -->
          <div class="program-card">
            <img
              src="assets/images/gallery/events/1000071344.jpg"
              alt="Disaster Relief"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-hand-holding-heart"></i> Relief</span
              >
              <h3>Aapda Rahat — Disaster Response</h3>
              <p>
                When nature strikes — storms, floods, or scorching heat — DPJKS
                volunteers stand alongside those who suffer. Our members have
                been witnessed helping affected individuals during severe
                weather events, providing essentials, assistance, and moral
                support when it is needed most.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Rapid volunteer mobilisation during weather events</span
                  >
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Essential supplies distributed to affected families</span
                  >
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Relief during storms, heavy rains & extreme heat</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Emotional and moral support alongside material aid</span
                  >
                </li>
              </ul>
            </div>
          </div>

          <!-- 6. Social Harmony -->
          <div class="program-card">
            <img
              src="assets/images/gallery/events/1000071343.jpg"
              alt="Social and Religious Harmony"
              class="prog-img"
            />
            <div class="prog-body">
              <span class="prog-tag"
                ><i class="fas fa-praying-hands"></i> Harmony</span
              >
              <h3>Dharm-Samajik Samanvay — Harmony</h3>
              <p>
                At the heart of DPJKS is a deep commitment to religious harmony
                and social unity. We believe social reform requires collective
                effort, discipline, and ethical conduct. Every program we run —
                regardless of who benefits — reinforces this core principle:
                service to humanity is service to God.
              </p>
              <ul class="prog-facts">
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Service to all — no discrimination by religion or
                    caste</span
                  >
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Awareness programs for social unity</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span
                    >Promoting ethical conduct and collective
                    responsibility</span
                  >
                </li>
                <li>
                  <i class="fas fa-check-circle"></i
                  ><span>Building bridges across communities</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Impact Numbers -->
        <div class="impact-banner">
          <div class="impact-item">
            <h3>100+</h3>
            <p>Meals Daily</p>
          </div>
          <div class="impact-item">
            <h3>25</h3>
            <p>Children Educated</p>
          </div>
          <div class="impact-item">
            <h3>1000+</h3>
            <p>Trees Planted</p>
          </div>
          <div class="impact-item">
            <h3>15+</h3>
            <p>Years of Service</p>
          </div>
          <div class="impact-item">
            <h3>₹3K</h3>
            <p>Monthly: Teacher Salary</p>
          </div>
        </div>

        <!-- Support CTA -->
        <div class="support-cta">
          <h2>Support a Specific Cause</h2>
          <p>
            You can choose to direct your contribution toward any of these
            programs — whether it's buying a meal, sponsoring a child's
            education for a month, or funding tree plantation. Every rupee is
            accounted for with complete transparency.
          </p>
          <div
            style="
              display: flex;
              gap: 1rem;
              justify-content: center;
              flex-wrap: wrap;
            "
          >
            <a href="donation.html" class="btn btn-primary">Donate Today</a>
            <a href="contact.html" class="btn btn-secondary"
              >Volunteer With Us</a
            >
          </div>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>
    <script src="js/main.js"></script>
  </body>
</html>
