<?php
header('Content-Type: application/javascript');

$baseDir = USER_PATH . 'assets/';
$baseUrl = '/assets/'; // Adjust if your public URL differs
$assetFiles = [];

// Recursively get only `.min.css` and `.min.js` files
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $filename = $file->getFilename();
    if (preg_match('/\.min\.(css|js)$/', $filename)) {
        // Build public path
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($baseDir, '', $file->getPathname()));
        $urlPath = $baseUrl . $relativePath;
        $assetFiles[] = $urlPath;
    }
}

// Add homepage
array_unshift($assetFiles, '/');
$assetFiles[] = '/offline.html';

// Encode array to JS
$urlsToCache = json_encode($assetFiles, JSON_UNESCAPED_SLASHES);
?>

const CACHE_NAME = "<?php echo str_replace('https://', '', str_replace('.', '-', SITE_DOMAIN)) ?>-cache-v1";
const urlsToCache = <?php echo $urlsToCache; ?>;

self.addEventListener("install", event => {
event.waitUntil(
caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
);
});

self.addEventListener("fetch", event => {
event.respondWith(
fetch(event.request).catch(() =>
caches.match(event.request)
)
);
});