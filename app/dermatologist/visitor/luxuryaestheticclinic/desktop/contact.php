<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner">
  <div class="container inner-banner-content">
    <h1>GET IN TOUCH</h1>
    <div class="breadcrumb-path">
      <a href="/">Home</a>
      <span><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
      <span style="color: var(--color-primary-gold);">Contact Us</span>
    </div>
  </div>
</section>

<!-- 3. CONTACT LAYOUT & DETAILS -->
<section class="contact-section" id="contactInfo">
  <div class="container contact-grid">
    <!-- Left: Contact Details Cards -->
    <div class="contact-cards-container reveal">
      <span class="section-tag">Direct Concierge Channels</span>
      <h2 style="margin-bottom: 2.5rem;">Reach Our Medical Sanctuary</h2>

      <!-- Card 1: Address -->
      <div class="contact-detail-card">
        <div class="contact-card-icon">
          <i data-lucide="map-pin" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="contact-card-text">
          <h4>Clinic Location</h4>
          <p>E-42, Ring Road, Defence Colony,<br>New Delhi, Delhi 110024</p>
        </div>
      </div>

      <!-- Card 2: Calls -->
      <div class="contact-detail-card">
        <div class="contact-card-icon">
          <i data-lucide="phone" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="contact-card-text">
          <h4>Direct Scheduling Line</h4>
          <p>Call Direct: <a href="tel:+919999999999">+91 99999 99999</a><br>conciergeDesk@luxuryaesthetic.com</p>
        </div>
      </div>

      <!-- Card 3: WhatsApp -->
      <div class="contact-detail-card">
        <div class="contact-card-icon">
          <i data-lucide="message-square" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="contact-card-text">
          <h4>WhatsApp Concierge Desk</h4>
          <p>Chat Instantly: <a href="https://wa.me/919999999999" target="_blank" rel="noopener">Start Chat Session</a><br>Available 10:00 AM - 08:00 PM</p>
        </div>
      </div>

      <!-- Card 4: Timing -->
      <div class="contact-detail-card">
        <div class="contact-card-icon">
          <i data-lucide="clock" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="contact-card-text">
          <h4>Working Hours</h4>
          <p>Monday - Saturday: 10:00 AM - 08:00 PM<br>Sunday: By Prior Appointment Only</p>
        </div>
      </div>

      <!-- Card 5: Parking -->
      <div class="contact-detail-card">
        <div class="contact-card-icon">
          <i data-lucide="info" style="width: 22px; height: 22px;"></i>
        </div>
        <div class="contact-card-text">
          <h4>Parking & Valet Concierge</h4>
          <p>Complimentary premium valet parking available directly at our Defence Colony gate entrance for all patients.</p>
        </div>
      </div>
    </div>

    <!-- Right: Responsive Iframe Map & Quick Form -->
    <div style="width: 100%;">
      <!-- Interactive Google Map Frame -->
      <div class="map-box-wrapper reveal">
        <div class="map-frame">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3504.269229505505!2d77.22159187632612!3d28.56166167570417!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1d7ef43be7ff%3A0xe54d314adbb855a8!2sDefence%20Colony%20Flyover%2C%20Defence%20Colony%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Luxury Aesthetic Clinic Google Map location Defence Colony"></iframe>
        </div>
      </div>

      <!-- Quick Message Form -->
      <div class="consult-form-card reveal">
        <h3 class="form-title-editorial">Send an Instant Message</h3>

        <form id="consultationForm">
          <div class="form-group-row">
            <div class="form-group">
              <input type="text" id="formName" class="form-input" placeholder=" " required>
              <label for="formName" class="form-label">Full Name *</label>
            </div>
            <div class="form-group">
              <input type="tel" id="formPhone" class="form-input" placeholder=" " required>
              <label for="formPhone" class="form-label">Phone Number *</label>
            </div>
          </div>

          <div class="form-group">
            <input type="email" id="formEmail" class="form-input" placeholder=" " required>
            <label for="formEmail" class="form-label">Email Address *</label>
          </div>

          <div class="form-group">
            <textarea id="formMessage" class="form-textarea" placeholder=" " required></textarea>
            <label for="formMessage" class="form-label">Your message or inquiry *</label>
          </div>

          <button type="submit" class="btn btn-primary form-btn-submit" id="formSubmit" style="width: 100%;">Send Inquiry</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php

require_once 'footer.php';

?>