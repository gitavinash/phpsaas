<?php

if ($checkConsultationWidget = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id WHERE link_url ='/trust-bar---widget/' AND link_status=200 AND link_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkConsultationWidget) != 0) {
        extract(mysqli_fetch_assoc($checkConsultationWidget));
        echo $page_description;
    } else {
        echo "<section class='trust-bar' id='trust-bar'>
    <div class='container trust-bar-grid'>
        <p class='counter-item reveal reveal-stagger active' style='transition-delay: 0s;'>
            <span class='counter-number' data-target='1500' data-suffix='+'><?php echo $statsForPatients ?>+</span>
            <span class='counter-label'>Happy Patients</span>
        </p>
        <p class='counter-item reveal reveal-stagger active' style='transition-delay: 0.15s;'>
            <span class='counter-number' data-target='10' data-suffix='+'><?php echo $statsForYears ?>+</span>
            <span class='counter-label'>Years Experience</span>
        </p>
        <p class='counter-item reveal reveal-stagger active' style='transition-delay: 0.3s;'>
            <span class='counter-number' data-target='20' data-suffix='+'><?php echo $statsForTreatments ?>+</span>
            <span class='counter-label'>Advanced Treatments</span>
        </p>
        <p class='counter-item reveal reveal-stagger active' style='transition-delay: 0.45s;'>
            <span class='counter-number' data-target='500' data-suffix='+'><?php echo $statsForHappyClients ?>+</span>
            <span class='counter-label'>Verified Reviews</span>
        </p>
    </div>
</section>";
    }
}
