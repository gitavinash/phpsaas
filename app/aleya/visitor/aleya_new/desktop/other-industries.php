<?php
/**
 * Aleya SaaS — Commercial CRM Landing Page (All Other Industries)
 * Accessible via /other-industries/ and /industries/other-industries/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['other-industries']);
