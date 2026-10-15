<?php
/**
 * Aleya SaaS — Travel Agencies & Tour Operators Landing Page
 * Accessible via /travel-agencies/ and /industries/travel-agencies/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['travel-agencies']);
