<?php

if ($checkConsultationWidget = mysqli_query($connection, "SELECT category_description FROM " . TABLE_PREFIX . "category WHERE category_title LIKE '%Governance%' AND category_status = '1' AND category_site_id =" . SITE_ID)) {

    if (mysqli_num_rows($checkConsultationWidget) != 0) {
        extract(mysqli_fetch_assoc($checkConsultationWidget));
        echo $category_description;
    } else {
        echo "
<section class='philosophy-section' id='philosophy'>
    <div class='container text-center reveal'>
        <span class='section-tag'>Clinical Governance</span>
        <h2>Our Pillars of Ethical Medical Care</h2>
        <p class='max-width-600'>
            Every specialist on our Delhi panel operates under a strict code of clinical governance to guarantee skin health and patient satisfaction above all:
        </p>
    </div>

    <div class='container philosophy-grid'>
        <!-- Pillar 1 -->
        <div class='philosophy-card reveal reveal-stagger'>
            <div class='philosophy-icon'>
                <i data-lucide='activity' style='width: 24px; height: 24px;'></i>
            </div>
            <h3>Tissue Preservation</h3>
            <p>We completely prioritize non-aggressive clinical treatments that preserve skin barriers and structures. Our methods bypass extreme outer layer ablation to target roots smoothly with minimal downtime.</p>
        </div>

        <!-- Pillar 2 -->
        <div class='philosophy-card reveal reveal-stagger'>
            <div class='philosophy-icon'>
                <i data-lucide='heart' style='width: 24px; height: 24px;'></i>
            </div>
            <h3>Autologous Activation</h3>
            <p>We leverage advanced medical solutions like PRP (Platelet-Rich Plasma) and GFC (Growth Factor Concentrate) to activate your body's natural cellular healing matrices for organic skin and hair regrowth.</p>
        </div>

        <!-- Pillar 3 -->
        <div class='philosophy-card reveal reveal-stagger'>
            <div class='philosophy-icon'>
                <i data-lucide='shield' style='width: 24px; height: 24px;'></i>
            </div>
            <h3>FDA-Certified Safety</h3>
            <p>All aesthetic injectables, laser tools, and diagnostic systems used inside our South Delhi rooms carry official US FDA certifications, guaranteeing strict quality and clinical hygiene standards.</p>
        </div>
    </div>
</section>";
    }
}
