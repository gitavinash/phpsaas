<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog & News - DPJKS</title>
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
          url("assets/images/gallery/events/1000071336.jpg") center/cover;
        padding: 5rem 0;
        color: white;
        text-align: center;
      }

      .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2.5rem;
      }

      .blog-card {
        background: var(--white);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s;
        border: 1px solid #f0f0f0;
      }

      .blog-card:hover {
        transform: translateY(-5px);
      }

      .blog-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
      }

      .blog-content {
        padding: 1.5rem;
      }

      .blog-date {
        font-size: 0.85rem;
        color: var(--text-light);
        display: block;
        margin-bottom: 0.5rem;
      }

      .blog-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.8rem;
      }

      .read-more {
        color: var(--primary-color);
        font-weight: 600;
        margin-top: 1rem;
        display: inline-block;
      }

      .sidebar {
        background: #f9f9f9;
        padding: 1.5rem;
        border-radius: 10px;
      }

      .sidebar h4 {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--secondary-color);
      }

      .category-list li {
        margin-bottom: 0.5rem;
      }

      .category-list a {
        color: var(--text-light);
      }

      .category-list a:hover {
        color: var(--primary-color);
      }

      @media (min-width: 992px) {
        .blog-layout {
          display: grid;
          grid-template-columns: 2fr 1fr;
          gap: 3rem;
        }
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>News & Stories</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          In-depth stories, updates, and insights from our team and the
          communities we serve.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container blog-layout">
        <!-- Main Content -->
        <div class="blog-grid">
          <!-- Post 1 -->
          <div class="blog-card">
            <img
              src="assets/images/gallery/education/1000071271.jpg"
              alt="Blog 1"
              class="blog-img"
            />
            <div class="blog-content">
              <span class="blog-date">March 10, 2026</span>
              <h3>Empowering Women Through Skill Development</h3>
              <p style="font-size: 0.95rem; color: var(--text-light)">
                How our vocational training centers are helping women in rural
                Kanpur become financially independent...
              </p>
              <a href="#" class="read-more">Read Full Story →</a>
            </div>
          </div>

          <!-- Post 2 -->
          <div class="blog-card">
            <img
              src="assets/images/gallery/healthcare/1000071306.jpg"
              alt="Blog 2"
              class="blog-img"
            />
            <div class="blog-content">
              <span class="blog-date">February 28, 2026</span>
              <h3>Clean Water Project Success</h3>
              <p style="font-size: 0.95rem; color: var(--text-light)">
                We successfully installed solar-powered water pumps in 5 new
                villages, ensuring clean drinking water...
              </p>
              <a href="#" class="read-more">Read Full Story →</a>
            </div>
          </div>

          <!-- Post 3 -->
          <div class="blog-card">
            <img
              src="assets/images/gallery/education/1000071261.jpg"
              alt="Blog 3"
              class="blog-img"
            />
            <div class="blog-content">
              <span class="blog-date">January 15, 2026</span>
              <h3>Annual Education Drive Report</h3>
              <p style="font-size: 0.95rem; color: var(--text-light)">
                A look back at our achievements in the education sector over the
                last year, distributing 10k books...
              </p>
              <a href="#" class="read-more">Read Full Story →</a>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
          <h4>Categories</h4>
          <ul class="category-list">
            <li><a href="#">Education (12)</a></li>
            <li><a href="#">Healthcare (8)</a></li>
            <li><a href="#">Events (5)</a></li>
            <li><a href="#">Success Stories (15)</a></li>
            <li><a href="#">Press Releases (3)</a></li>
          </ul>

          <h4 style="margin-top: 2rem">Newsletter</h4>
          <p style="font-size: 0.9rem; margin-bottom: 1rem">
            Subscribe to get the latest updates directly in your inbox.
          </p>
          <form>
            <input
              type="email"
              placeholder="Your Email"
              style="
                width: 100%;
                padding: 0.5rem;
                margin-bottom: 0.5rem;
                border: 1px solid #ddd;
                border-radius: 4px;
              "
            />
            <button
              type="submit"
              class="btn btn-primary"
              style="width: 100%; padding: 0.5rem"
            >
              Subscribe
            </button>
          </form>
        </aside>
      </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="js/main.js"></script>
  </body>
</html>
