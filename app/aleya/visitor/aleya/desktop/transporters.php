<?php
/**
 * Aleya SaaS — Transporters & Fleet Operators Landing Page
 * Accessible via /transporters/ and /industries/transporters/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['transporters']);
