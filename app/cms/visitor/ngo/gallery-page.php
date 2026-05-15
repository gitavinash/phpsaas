<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gallery - DPJKS</title>
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
    <link rel="stylesheet" href="css/style.css" />
    <style>
      .page-header {
        background:
          linear-gradient(rgba(13, 59, 102, 0.9), rgba(13, 59, 102, 0.8)),
          url("assets/images/gallery/events/1000071351.jpg") center/cover;
        padding: 5rem 0;
        color: white;
        text-align: center;
      }

      .gallery-filters {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
      }

      .filter-btn {
        background: transparent;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
      }

      .filter-btn:hover,
      .filter-btn.active {
        background: var(--primary-color);
        color: white;
      }

      .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
      }

      .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        cursor: pointer;
        height: 250px;
      }

      .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
      }

      .gallery-item:hover img {
        transform: scale(1.1);
      }

      .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 1rem;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .gallery-item:hover .overlay {
        opacity: 1;
      }

      /* Video Section */
      .video-section {
        margin-top: 4rem;
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>Moments of Impact</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          Capturing the smiles and stories of change from our journey in Kanpur
          and beyond.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container">
        <!-- Filters -->
        <div class="gallery-filters">
          <button class="filter-btn active">All</button>
          <button class="filter-btn">Education</button>
          <button class="filter-btn">Healthcare</button>
          <button class="filter-btn">Events</button>
          <button class="filter-btn">Volunteers</button>
        </div>

        <!-- Image Grid -->
        <div class="gallery-grid">
          <!-- Album 1: Education -->
          <a href="gallery-detail.html?album=education" class="gallery-item">
            <img
              src="assets/images/gallery/education/1000071261.jpg"
              alt="Education Album"
            />
            <div class="overlay">
              <h5>Education Drives</h5>
              <p><i class="fas fa-images"></i> 7 Photos</p>
            </div>
          </a>

          <!-- Album 2: Healthcare -->
          <a href="gallery-detail.html?album=healthcare" class="gallery-item">
            <img
              src="assets/images/gallery/healthcare/1000071301.jpg"
              alt="Healthcare Album"
            />
            <div class="overlay">
              <h5>Healthcare Camps</h5>
              <p><i class="fas fa-images"></i> 6 Photos</p>
            </div>
          </a>

          <!-- Album 3: Events -->
          <a href="gallery-detail.html?album=events" class="gallery-item">
            <img
              src="assets/images/gallery/events/1000071328.jpg"
              alt="Events Album"
            />
            <div class="overlay">
              <h5>Community Events</h5>
              <p><i class="fas fa-images"></i> 18 Photos</p>
            </div>
          </a>
        </div>

        <!-- Video Section -->
        <div class="video-section">
          <h3 style="margin-bottom: 2rem; color: var(--primary-color)">
            Featured Videos
          </h3>
          <div
            style="
              background: #000;
              height: 400px;
              display: flex;
              align-items: center;
              justify-content: center;
              color: white;
              border-radius: 10px;
            "
          >
            <!-- Placeholder for video embed -->
            <div class="text-center">
              <i
                class="fas fa-play-circle"
                style="font-size: 4rem; opacity: 0.8; cursor: pointer"
              ></i>
              <p style="margin-top: 1rem">Watch our Annual Report Video</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="js/main.js"></script>
    <script>
      // Simple Filter Logic (Demo)
      const buttons = document.querySelectorAll(".filter-btn");
      buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
          buttons.forEach((b) => b.classList.remove("active"));
          btn.classList.add("active");
          // Real filtering code would go here
        });
      });
    </script>
  </body>
</html>
