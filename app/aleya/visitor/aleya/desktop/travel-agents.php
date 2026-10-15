<?php
/**
 * Aleya SaaS — Independent Travel Agents Landing Page
 * Accessible via /travel-agents/ and /industries/travel-agents/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['travel-agents']);
