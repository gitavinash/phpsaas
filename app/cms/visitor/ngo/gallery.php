<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Photo Gallery - DPJKS</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
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
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <div id="header-placeholder"></div>

    <!-- Detail Hero -->
    <section
      class="detail-hero"
      style="
        height: 40vh;
        background-image: url(&quot;assets/images/gallery/education/1000071261.jpg&quot;);
      "
    >
      <div class="container detail-hero-content">
        <a
          href="gallery.html"
          style="
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
            display: inline-block;
          "
          ><i class="fas fa-arrow-left"></i> Back to Gallery</a
        >
        <h1 style="font-size: 3rem">Winter Donation Drive 2025</h1>
        <p style="font-size: 1.1rem">
          Spreading warmth across 5 villages in Kanpur Dehat.
        </p>
      </div>
    </section>

    <!-- Album Description -->
    <div
      class="container"
      style="margin-top: 3rem; max-width: 800px; text-align: center"
    >
      <p style="color: #555; font-size: 1.1rem; line-height: 1.7">
        This January, our team distributed over 500 blankets and warm clothing
        kits to the homeless and underprivileged families. The smiles we saw
        were the warmest thing we experienced this winter.
      </p>
    </div>

    <!-- Gallery Grid -->
    <section class="section-padding">
      <div class="container">
        <div class="gallery-strip">
          <img
            src="assets/images/gallery/events/1000071328.jpg"
            alt="Distribution Event"
            data-caption="Distribution of relief material to flood victims in Bilhaur."
          />
          <img
            src="assets/images/gallery/education/1000071263.jpg"
            alt="Happy Children"
            data-caption="Smiles on the faces of children after receiving school kits."
          />
          <img
            src="assets/images/gallery/events/1000071336.jpg"
            alt="Team Work"
            data-caption="Our dedicated team of volunteers organizing the camp."
          />
          <img
            src="assets/images/gallery/events/1000071342.jpg"
            alt="Community Gathering"
            data-caption="Community members gathering for the awareness session."
          />
          <img
            src="assets/images/gallery/education/1000071287.jpg"
            alt="Education Kit"
            data-caption="Education kits ready for distribution."
          />
          <img
            src="assets/images/gallery/healthcare/1000071302.jpg"
            alt="Health Checkup"
            data-caption="General health checkup for elderly citizens."
          />
          <img
            src="assets/images/gallery/events/1000071349.jpg"
            alt="Volunteer Briefing"
            data-caption="Morning briefing session for our volunteers."
          />
          <img
            src="assets/images/gallery/healthcare/1000071304.jpg"
            alt="Village View"
            data-caption="A view of the beautiful village where we worked."
          />
        </div>
      </div>
    </section>

    <!-- Navigation Footer -->
    <div
      class="container"
      style="
        margin-bottom: 4rem;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #eee;
        padding-top: 2rem;
      "
    >
      <a href="#" class="btn" style="color: #666"
        ><i class="fas fa-chevron-left"></i> Previous Album</a
      >
      <a href="#" class="btn" style="color: #666"
        >Next Album <i class="fas fa-chevron-right"></i
      ></a>
    </div>

    <div id="footer-placeholder"></div>
    <script src="js/main.js"></script>
  </body>
</html>
