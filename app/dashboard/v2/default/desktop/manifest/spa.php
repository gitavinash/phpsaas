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
      "purpose" => "any"
    ],
    [
      "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_ICON) . '&width=192&height=192',
      "sizes" => "192x192",
      "type" => "image/png",
      "purpose" => "any"
    ],
    [
      "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_ICON) . '&width=96&height=96',
      "sizes" => "96x96",
      "type" => "image/png",
      "purpose" => "any"
    ]
  ],
  "shortcuts" => [
    [
      "name" => "Overview",
      "short_name" => "Overview",
      "description" => "Quick access to your dashboard overview",
      "url" => "/dashboard/?view=overview",
      "icons" => [[
        "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_URL . '/lib/resource/ui/img/flaticon/user-add.png') . '&width=96&height=96',
        "sizes" => "96x96",
        "type" => "image/png"
      ]]
    ],
    [
      "name" => "Check In",
      "short_name" => "Check-in",
      "description" => "Check members in and track attendance",
      "url" => "/dashboard/membership/check-in/",
      "icons" => [[
        "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_URL . '/lib/resource/ui/img/flaticon/entrance.png') . '&width=96&height=96',
        "sizes" => "96x96",
        "type" => "image/png"
      ]]
    ],
    [
      "name" => "Active Rooms",
      "short_name" => "Rooms",
      "description" => "Quickly view and manage active spa rooms",
      "url" => "/dashboard/rooms/active/",
      "icons" => [[
        "src" => SITE_URL . '/viewmedia/?url=' . urlencode(SITE_URL . '/lib/resource/ui/img/flaticon/door-open.png') . '&width=96&height=96',
        "sizes" => "96x96",
        "type" => "image/png"
      ]]
    ]
  ],
  "prefer_related_applications" => false
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);