<?php

require_once 'header.php';

?>
<div id="header-placeholder"></div>

<header class="page-header" style="
        background-image: url(<?php echo $page_image ?>);
      ">
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
            ">
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
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-utensils"></i> Food Distribution</span>
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
              <i class="fas fa-check-circle"></i><span>~100 meals served every day</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Launched: 19 January 2023</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Served during COVID-19 lockdown</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Continues without interruption to date</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- 2. Tree Plantation -->
      <div class="program-card">
        <img
          src="assets/images/gallery/events/1000071336.jpg"
          alt="Tree Plantation Drive"
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-seedling"></i> Environment</span>
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
              <i class="fas fa-check-circle"></i><span>Regular plantation drives across Kanpur region</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Post-plantation care & growth monitoring</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Community awareness about environmental protection</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Members personally responsible for sapling survival</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- 3. Free Education -->
      <div class="program-card">
        <img
          src="assets/images/gallery/education/1000071263.jpg"
          alt="Free Education for Children"
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-graduation-cap"></i> Education</span>
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
              <i class="fas fa-check-circle"></i><span>20–25 children enrolled from poor families</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Dedicated teacher appointed for the program</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>₹3,000/month teacher salary paid by DPJKS</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Running in the home village</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- 4. Cleanliness -->
      <div class="program-card">
        <img
          src="assets/images/gallery/events/1000071345.jpg"
          alt="Cleanliness Awareness"
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-broom"></i> Public Health</span>
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
              <i class="fas fa-check-circle"></i><span>Community-level sanitation awareness campaigns</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Hygiene education for families and children</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Direct intervention on local cleanliness issues</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Promoting connection between cleanliness and health</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- 5. Disaster Relief -->
      <div class="program-card">
        <img
          src="assets/images/gallery/events/1000071344.jpg"
          alt="Disaster Relief"
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-hand-holding-heart"></i> Relief</span>
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
              <i class="fas fa-check-circle"></i><span>Rapid volunteer mobilisation during weather events</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Essential supplies distributed to affected families</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Relief during storms, heavy rains & extreme heat</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Emotional and moral support alongside material aid</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- 6. Social Harmony -->
      <div class="program-card">
        <img
          src="assets/images/gallery/events/1000071343.jpg"
          alt="Social and Religious Harmony"
          class="prog-img" />
        <div class="prog-body">
          <span class="prog-tag"><i class="fas fa-praying-hands"></i> Harmony</span>
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
              <i class="fas fa-check-circle"></i><span>Service to all — no discrimination by religion or
                caste</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Awareness programs for social unity</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Promoting ethical conduct and collective
                responsibility</span>
            </li>
            <li>
              <i class="fas fa-check-circle"></i><span>Building bridges across communities</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<div id="footer-placeholder"></div>
<?php
require_once 'footer.php';
?>