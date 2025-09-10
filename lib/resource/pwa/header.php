<!-- PWA Manifest -->
<link rel="manifest" href="/dashboard/manifest.json">

<!-- Theme Color (matches manifest) -->
<meta name="theme-color" content="<?php echo SITE_THEME_COLOUR; ?>">

<!-- iOS Support -->
<!-- iOS PWA (required for fullscreen) -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- status bar: default | black | black-translucent -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Honey 2 Spa">

<!-- Exact iOS touch icons (fix double slash; ensure these are real sizes) -->
<link rel="apple-touch-icon" sizes="180x180"
    href="https://honey2spa.sourcenear.com/viewmedia/?url=<?php echo urlencode(SITE_ICON); ?>&width=180&height=180">
<!-- Optional extra sizes for older devices -->
<link rel="apple-touch-icon" sizes="167x167"
    href="https://honey2spa.sourcenear.com/viewmedia/?url=<?php echo urlencode(SITE_ICON); ?>&width=167&height=167">
<link rel="apple-touch-icon" sizes="152x152"
    href="https://honey2spa.sourcenear.com/viewmedia/?url=<?php echo urlencode(SITE_ICON); ?>&width=152&height=152">
<link rel="apple-touch-icon" sizes="120x120"
    href="https://honey2spa.sourcenear.com/viewmedia/?url=<?php echo urlencode(SITE_ICON); ?>&width=120&height=120">


<!-- Basic PWA Meta -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="<?php echo SITE_NAME; ?>">