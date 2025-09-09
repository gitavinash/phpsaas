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
  "background_color" => "#ffffff",
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
  "shortcuts" => [
    [
      "name" => "Overview",
      "short_name" => "Overview",
      "description" => "Quick access to your dashboard overview",
      "url" => "/dashboard/?view=overview"
    ],
    [
      "name" => "Check In",
      "short_name" => "Check-in",
      "description" => "View your business reports",
      "url" => "/dashboard/memberhsip/check-in/"
    ],
    [
      "name" => "Active Rooms",
      "short_name" => "Rooms",
      "description" => "Quickly view and manage active spa rooms",
      "url" => "/dashboard/rooms/active/"
    ],
  ],
  "prefer_related_applications" => false
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);