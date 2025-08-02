<?php
header('Content-Type: application/javascript');

$baseDir = USER_PATH . 'assets/';
$baseUrl = USER_PATH . '/assets/';
$assetFiles = [];

// Recursively get only `.min.css` and `.min.js` files
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $filename = $file->getFilename();
    if (preg_match('/\.min\.(css|js)$/', $filename)) {
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($baseDir, '', $file->getPathname()));
        $urlPath = $baseUrl . $relativePath;
        $assetFiles[] = $urlPath;
    }
}

// Add homepage and offline page
array_unshift($assetFiles, '/');
$assetFiles[] = '/offline.html';

$urlsToCache = json_encode($assetFiles, JSON_UNESCAPED_SLASHES);
?>

const CACHE_NAME = "<?php echo str_replace('https://', '', str_replace('.', '-', SITE_DOMAIN)) ?>-cache-v1";
const urlsToCache = <?php echo $urlsToCache; ?>;

self.addEventListener("install", event => {
console.log("Service Worker: Installing and caching files:", urlsToCache);

event.waitUntil(
caches.open(CACHE_NAME).then(cache => {
return Promise.all(
urlsToCache.map(url =>
fetch(url)
.then(response => {
if (!response.ok) {
throw new Error(`Request for ${url} failed with status ${response.status}`);
}
return cache.put(url, response);
})
.catch(error => {
console.error(`Failed to cache ${url}:`, error);
})
)
);
})
);
});

self.addEventListener("fetch", event => {
event.respondWith(
fetch(event.request).catch(() => {
return caches.match(event.request).then(response => {
return response || caches.match('/offline.html');
});
})
);
});