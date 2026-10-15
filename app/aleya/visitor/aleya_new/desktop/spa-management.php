<?php
/**
 * Aleya SaaS — Spa & Wellness Center Landing Page
 * Accessible via /spa-management/ and /industries/spa-management/
 */
require_once __DIR__ . '/includes/industries-data.php';
require_once __DIR__ . '/includes/industry-template.php';

$industries = get_all_industries();
render_industry_landing_page($industries['spa-management']);
