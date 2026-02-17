<?php
header('Content-Type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$baseDir = USER_PATH . 'assets/';
$baseUrl = USER_DIR . '/assets/';

$baseDir = str_replace('//', '/', $baseDir);

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
$cacheVersion = substr(md5($urlsToCache . '|' . filemtime(__FILE__)), 0, 12);
?>

const CACHE_NAME =
"<?php echo str_replace('https://', '', str_replace('.', '-', SITE_DOMAIN)) ?>-cache-v2-<?php echo $cacheVersion; ?>";
const urlsToCache = <?php echo $urlsToCache; ?>;

self.addEventListener("install", event => {
console.log("Service Worker: Installing and caching files:", urlsToCache);

event.waitUntil(
caches.open(CACHE_NAME).then(cache => {
return Promise.all(
urlsToCache.map(url =>
fetch(url, { cache: "reload" })
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

self.addEventListener('message', event => {
if (event.data && event.data.type === 'SKIP_WAITING') {
self.skipWaiting();
}
});

self.addEventListener('activate', event => {
event.waitUntil(
caches.keys().then(cacheNames => {
return Promise.all(
cacheNames.map(cache => {
if (cache !== CACHE_NAME) {
return caches.delete(cache);
}
})
);
}).then(() => self.clients.claim())
);
});

self.addEventListener('fetch', event => {
if (event.request.method !== 'GET') {
return;
}

if (event.request.destination === 'style' || event.request.destination === 'script') {
event.respondWith(
fetch(event.request, { cache: 'no-store' })
.then(networkResponse => {
const networkClone = networkResponse.clone();
caches.open(CACHE_NAME).then(cache => cache.put(event.request, networkClone));
return networkResponse;
})
.catch(() => caches.match(event.request))
);
return;
}

event.respondWith(
caches.match(event.request).then(response => {
return response || fetch(event.request).catch(() => {
if (event.request.mode === 'navigate') {
return caches.match('/offline.html');
}
});
})
);
});