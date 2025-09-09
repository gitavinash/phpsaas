<?php
header('Content-Type: application/json');

// Dynamically return JSON for PWA manifest
echo json_encode([
  "name" => SITE_NAME,
  "short_name" => SITE_NAME,
  "description" => SITE_DESC,
  "start_url" => "/dashboard/?source=pwa",
  "scope" => "/dashboard/",
  "display" => "standalone",
  "orientation" => "portrait-primary",
  "theme_color" => SITE_THEME_COLOUR,
  "background_color" => ((!defined('SITE_BACKGROUND_COLOR')) ? '#ffffff' : SITE_BACKGROUND_COLOR),
  "id" => strtolower(str_replace(' ', '-', SITE_NAME)) . "-dashboard",
  "lang" => SITE_LANG,
  "dir" => "ltr",
  "categories" => ["business", "productivity", "management"],
  "icons" => [
    [
      "src" => SITE_ICON,
      "sizes" => "512x512",
      "type" => "image/png",
      "purpose" => "any maskable"
    ]
  ],
  "prefer_related_applications" => false
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);