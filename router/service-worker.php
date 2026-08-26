<?php
header('Content-Type: application/javascript');

$baseDir = USER_PATH . 'assets/';
$baseUrl = USER_DIR . '/assets/';
$baseDir = str_replace('//', '/', $baseDir);

$assetFiles = [];

if (is_dir($baseDir)) {
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($rii as $file) {
        if ($file->isDir()) {
            continue;
        }

        $filename = $file->getFilename();
        if (!preg_match('/\.min\.(css|js)$/', $filename)) {
            continue;
        }

        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($baseDir, '', $file->getPathname()));
        $assetFiles[] = $baseUrl . $relativePath;
    }
}

$assetFiles = array_values(array_unique($assetFiles));
$assetFiles[] = '/offline.html';

$cacheSeed = @filemtime(__FILE__) ?: time();
$cacheName = str_replace('https://', '', str_replace('.', '-', SITE_DOMAIN)) . '-cache-v2-' . $cacheSeed;
$urlsToCache = json_encode($assetFiles, JSON_UNESCAPED_SLASHES);
?>

const CACHE_NAME = <?php echo json_encode($cacheName); ?>;
const OFFLINE_URL = '/offline.html';
const urlsToCache = <?php echo $urlsToCache; ?>;

self.addEventListener('install', (event) => {
event.waitUntil((async () => {
const cache = await caches.open(CACHE_NAME);
await Promise.all(
urlsToCache.map(async (url) => {
try {
await cache.add(new Request(url, { cache: 'reload' }));
} catch (error) {
console.warn('SW: Failed to precache', url, error);
}
})
);
await self.skipWaiting();
})());
});

self.addEventListener('activate', (event) => {
event.waitUntil((async () => {
const keys = await caches.keys();
await Promise.all(
keys
.filter((key) => key !== CACHE_NAME)
.map((key) => caches.delete(key))
);
await self.clients.claim();
})());
});

self.addEventListener('fetch', (event) => {
const req = event.request;
if (req.method !== 'GET') {
return;
}

const url = new URL(req.url);
const isSameOrigin = url.origin === self.location.origin;
const isNavigation = req.mode === 'navigate';
const isCssOrJs = /\.(css|js)(\?|$)/i.test(url.pathname);

// Always try fresh network for pages and assets so users see latest deploy.
if (isNavigation || (isSameOrigin && isCssOrJs)) {
event.respondWith((async () => {
try {
const networkResponse = await fetch(req, { cache: 'no-store' });
if (isSameOrigin && networkResponse && networkResponse.ok) {
const cache = await caches.open(CACHE_NAME);
await cache.put(req, networkResponse.clone());
}
return networkResponse;
} catch (error) {
const cache = await caches.open(CACHE_NAME);
const cachedResponse = await cache.match(req, { ignoreSearch: false });
if (cachedResponse) {
return cachedResponse;
}
if (isNavigation) {
const offline = await cache.match(OFFLINE_URL);
if (offline) {
return offline;
}
}
throw error;
}
})());
return;
}

// Cache-first for other static requests.
event.respondWith((async () => {
const cache = await caches.open(CACHE_NAME);
const cachedResponse = await cache.match(req, { ignoreSearch: false });
if (cachedResponse) {
return cachedResponse;
}

const networkResponse = await fetch(req);
if (isSameOrigin && networkResponse && networkResponse.ok) {
await cache.put(req, networkResponse.clone());
}
return networkResponse;
})());
});
<?php exit(); ?>