<?php

require_once 'header.php';

?>
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
      <?php

      echo $page_description;

      ?>

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
              required />
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input
              type="email"
              class="form-control"
              placeholder="john@example.com"
              required />
          </div>
          <div class="form-group">
            <label class="form-label">Subject</label>
            <input
              type="text"
              class="form-control"
              placeholder="Inquiry about..."
              required />
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea
              class="form-control"
              placeholder="Your message here..."
              required></textarea>
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
        referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>

<?php

require_once 'footer.php';

?>