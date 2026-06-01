<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Achievements - DPJKS</title>
    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700&display=swap"
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
          linear-gradient(rgba(13, 59, 102, 0.9), rgba(13, 59, 102, 0.8)),
          url("assets/images/gallery/events/1000071328.jpg") center/cover;
        padding: 5rem 0;
        color: white;
        text-align: center;
      }

      .achievements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
      }

      .achievement-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
      }

      .achievement-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
      }

      .achievement-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
      }

      .achievement-info {
        padding: 1.5rem;
      }

      .achievement-info h3 {
        margin-bottom: 0.5rem;
        color: var(--primary-color);
        font-size: 1.25rem;
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>Our Achievements</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          Recognizing the milestones and impact we have created together.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container">
        <div class="achievements-grid">
          <!-- Achievement 1 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/blood-donation.jpeg"
              alt="Blood Donation Camp"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>Blood Donation Camp</h3>
              <p>
                Organized a successful blood donation drive creating significant
                impact.
              </p>
            </div>
          </div>

          <!-- Achievement 2 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/national-human-rights-comission.jpeg"
              alt="National Human Rights Commission"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>NHRC Recognition</h3>
              <p>
                Honored by the National Human Rights Commission for our efforts.
              </p>
            </div>
          </div>

          <!-- Achievement 3 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/niti-ayog.jpeg"
              alt="Niti Ayog"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>Niti Aayog Collaboration</h3>
              <p>Partnering for sustainable development goals.</p>
            </div>
          </div>

          <!-- Achievement 4 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/padhe-bharat-bade-bharat.jpeg"
              alt="Padhe Bharat Bade Bharat"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>Padhe Bharat Bade Bharat</h3>
              <p>Promoting education and literacy across rural areas.</p>
            </div>
          </div>

          <!-- Achievement 5 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/promise.jpeg"
              alt="Our Promise"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>Our Promise</h3>
              <p>Delivering on our commitments to the community.</p>
            </div>
          </div>

          <!-- Achievement 6 -->
          <div class="achievement-card">
            <img
              src="assets/images/achievements/woman.jpeg"
              alt="Women Empowerment"
              class="achievement-img"
            />
            <div class="achievement-info">
              <h3>Women Empowerment</h3>
              <p>Supporting women to become independent and self-reliant.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="<?php echo USER_JS ?>main.js?v=<?= time() ?>"></script>
    <script>
      // Highlight active link manually if needed, or rely on js/main.js
      // js/main.js handles it based on URL filename
    </script>
  </body>
</html>
