<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - DPJKS</title>
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
          url("assets/images/gallery/events/1000071337.jpg") center/cover;
        padding: 5rem 0;
        color: white;
        text-align: center;
      }

      /* Contact Wrapper */
      .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
      }

      .contact-info-item {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
      }

      .icon-box {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
      }

      .info-content h3 {
        margin-bottom: 0.3rem;
      }

      .info-content p {
        color: var(--text-light);
      }

      /* Form */
      .contact-form {
        background: var(--white);
        padding: 2.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
      }

      .form-group {
        margin-bottom: 1.5rem;
      }

      .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
      }

      .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: inherit;
      }

      .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
      }

      textarea.form-control {
        resize: vertical;
        min-height: 120px;
      }

      /* Map */
      .map-section {
        width: 100%;
        height: 450px;
        margin-top: 4rem;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      @media (max-width: 768px) {
        .contact-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="page-header">
      <div class="container">
        <h1>Get in Touch</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          Have a question or want to get involved? We'd love to hear from you.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container">
        <div class="contact-grid">
          <!-- Contact Info -->
          <div class="contact-info">
            <h2 style="margin-bottom: 2rem">We are here to help</h2>

            <div class="contact-info-item">
              <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
              <div class="info-content">
                <h3>Our Office</h3>
                <p>Gram Hindupur Sabalpur, Shivrajpur,</p>
                <p>Kanpur Nagar, Uttar Pradesh, India</p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
              <div class="info-content">
                <h3>Phone Number</h3>
                <p>+91 93051 47080</p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="icon-box"><i class="fas fa-envelope"></i></div>
              <div class="info-content">
                <h3>Email Address</h3>
                <p>gauravmishra2k22@gmail.com</p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="icon-box"><i class="fas fa-clock"></i></div>
              <div class="info-content">
                <h3>Working Hours</h3>
                <p>Monday - Saturday</p>
                <p>9:00 AM - 6:00 PM</p>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="contact-form">
            <h3 style="margin-bottom: 1.5rem">Send us a message</h3>
            <form>
              <div class="form-group">
                <label class="form-label">Your Name</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="John Doe"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input
                  type="email"
                  class="form-control"
                  placeholder="john@example.com"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Subject</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Inquiry about..."
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Message</label>
                <textarea
                  class="form-control"
                  placeholder="Your message here..."
                  required
                ></textarea>
              </div>
              <button type="submit" class="btn btn-primary">
                Send Message
              </button>
            </form>
          </div>
        </div>

        <!-- Google Map -->
        <div class="map-section">
          <!-- Embedding random Kanpur location -->
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d114313.43940183057!2d80.25881077793442!3d26.447101235555474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399c4770b127c44f%3A0x63d6b1d42857ddce!2sKanpur%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1716383020993!5m2!1sen!2sin"
            width="100%"
            height="100%"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="js/main.js"></script>
  </body>
</html>
