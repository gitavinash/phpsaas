<?php
/**
 * Aleya SaaS — Distribution Management System (DMS) Landing Page
 * Accessible via /dms/ and /industries/dms/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['dms']);
