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
  "background_color" => SITE_BACKGROUND_COLOUR,
  "id" => strtolower(str_replace(' ', '-', SITE_NAME)) . "-dashboard",
  "lang" => SITE_LANG,
  "dir" => "ltr",
  "categories" => ["business", "productivity", "management"],
  "icons" => [
    [
      "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_ICON) . '&width=512&height=512',
      "sizes" => "512x512",
      "type" => "image/png",
      "purpose" => "any maskable"
    ],
    [
      "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_ICON) . '&width=192&height=192',
      "sizes" => "192x192",
      "type" => "image/png",
      "purpose" => "any maskable"
    ],
    [
      "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_ICON) . '&width=96&height=96',
      "sizes" => "96x96",
      "type" => "image/png",
      "purpose" => "any maskable"
    ]
  ],
  "screenshots" => [
    [
      "src" => SITE_URL . "/lib/resource/ui/img/app/screenshot/desktop/aleya/dashboard.png",
      "sizes" => "1600x900",
      "type" => "image/png",
      "form_factor" => "wide",
      "label" => "Dashboard Overview"
    ],
    [
      "src" => SITE_URL . "/lib/resource/ui/img/app/screenshot/mobile/aleya/dashboard.png",
      "sizes" => "1080x1920",
      "type" => "image/png",
      "form_factor" => "narrow",
      "label" => "Mobile View of Dashboard"
    ]
  ],
  "prefer_related_applications" => false
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);