<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<header class="page-header">
  <div class="container">
    <h1>Upcoming Events</h1>
    <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
      Join us in our upcoming drives, workshops, and community gatherings.
    </p>
  </div>
</header>

<section class="section-padding">
  <div class="container">
    <h2
      style="
            margin-bottom: 2rem;
            font-weight: 400;
            color: var(--text-light);
          ">
      Upcoming Events
    </h2>

    <!-- Event 1 -->
    <div class="event-card-featured">
      <div style="position: relative; width: 35%">
        <img
          src="assets/images/gallery/healthcare/1000071305.jpg"
          alt="Health Camp"
          class="event-feature-img"
          style="width: 100%; height: 100%" />
        <div class="event-date-badge">
          <span class="ed-day">15</span>
          <span class="ed-month">Mar</span>
        </div>
      </div>
      <div class="event-card-body">
        <h3>Mega Health Camp 2026</h3>
        <div class="event-meta" style="margin-bottom: 1rem">
          <span><i class="fas fa-clock"></i> 9:00 AM - 5:00 PM</span>
          <span><i class="fas fa-map-marker-alt"></i> Village Bilhaur</span>
        </div>
        <p>
          Bringing specialized healthcare including dental, eye, and general
          checkups to over 1,500 beneficiaries in rural areas.
        </p>
        <div class="event-actions">
          <a href="event-detail.html" class="btn btn-primary">View Details</a>
          <a
            href="contact.html"
            class="btn btn-secondary"
            style="margin-left: 10px">Volunteer</a>
        </div>
      </div>
    </div>

    <!-- Event 2 -->
    <div class="event-card-featured">
      <div style="position: relative; width: 35%">
        <img
          src="assets/images/gallery/events/1000071350.jpg"
          alt="Water Conservation"
          class="event-feature-img"
          style="width: 100%; height: 100%" />
        <div class="event-date-badge">
          <span class="ed-day">22</span>
          <span class="ed-month">Mar</span>
        </div>
      </div>
      <div class="event-card-body">
        <h3>Water Conservation Workshop</h3>
        <div class="event-meta" style="margin-bottom: 1rem">
          <span><i class="fas fa-clock"></i> 11:00 AM - 2:00 PM</span>
          <span><i class="fas fa-map-marker-alt"></i> Community Center</span>
        </div>
        <p>
          An interactive workshop on rainwater harvesting techniques
          suitable for local agriculture to combat water scarcity.
        </p>
        <div class="event-actions">
          <a href="events.html" class="btn btn-primary">View Details</a>
        </div>
      </div>
    </div>

    <h2
      style="
            margin: 3rem 0 2rem;
            font-weight: 400;
            color: var(--text-light);
          ">
      Past Events
    </h2>

    <!-- Event 3 -->
    <div class="event-card-featured" style="opacity: 0.9">
      <div style="position: relative; width: 35%">
        <img
          src="assets/images/gallery/events/1000071351.jpg"
          alt="Winter Drive"
          class="event-feature-img"
          style="width: 100%; height: 100%; filter: grayscale(50%)" />
        <div class="event-date-badge">
          <span class="ed-day">10</span>
          <span class="ed-month">Feb</span>
        </div>
      </div>
      <div class="event-card-body">
        <h3>Winter Cloth Donation Drive</h3>
        <div class="event-meta" style="margin-bottom: 1rem">
          <span><i class="fas fa-check-circle"></i> Completed</span>
          <span><i class="fas fa-map-marker-alt"></i> South City</span>
        </div>
        <p>
          Successfully distributed blankets and warm clothes to over 500
          homeless individuals across the city during the peak winter.
        </p>
        <div class="event-actions">
          <a href="gallery-detail.html" class="btn btn-secondary">View Photos</a>
        </div>
      </div>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>
<?php

require_once 'footer.php';

?>