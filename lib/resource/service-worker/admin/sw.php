<?php

declare(strict_types=1);

/**
 * Service Worker (served via PHP)
 * Outputs JavaScript with correct headers. No closing PHP tag.
 */
header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate'); // always fetch fresh SW
header('Service-Worker-Allowed: /dashboard/');                 // allow scope

// Wire to your constants if you want:
$APP_VERSION = 'dash-v1.2.2';
$BASE        = '/dashboard';
$OFFLINE_URL = $BASE . '/offline.php';
$MANIFEST    = $BASE . '/manifest.json';
$LOGO        = defined('SITE_ICON') ? SITE_ICON : ($BASE . '/assets/logo.svg');
?>
/* =============================
Dashboard Service Worker + FCM
Scope: /dashboard/
Served by PHP as JS
============================= */

// --- Firebase (compat) ---
try {
importScripts("https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js");
} catch (e) { /* offline? keep going */ }

const firebaseConfig = {
apiKey: "AIzaSyBlo7GZPUUMNywiJYEHv-RqnFTTQUQDwUA",
authDomain: "aleya-af985.firebaseapp.com",
projectId: "aleya-af985",
storageBucket: "aleya-af985.appspot.com",
messagingSenderId: "876838268173",
appId: "1:876838268173:web:8ad5afeae5de209e4d9444",
};

if (self.firebase?.apps?.length === 0) {
firebase.initializeApp(firebaseConfig);
}
const messaging = self.firebase?.messaging?.();

if (messaging) {
messaging.onBackgroundMessage((payload) => {
const n = payload?.notification || {};
const title = n.title || payload?.data?.title || "Notification";
const body = n.body || payload?.data?.body || "";
const icon = n.icon || payload?.data?.icon || <?= json_encode($LOGO) ?>;
const image = n.image || payload?.data?.image;
const click_action = n.click_action || payload?.data?.click_action || <?= json_encode($BASE . '/') ?>;

self.registration.showNotification(title, {
body, icon, image, data: { click_action }
});
});
}

self.addEventListener("notificationclick", (event) => {
event.notification.close();
const openUrl =
event.notification?.data?.click_action ||
event.notification?.data?.FCM_MSG?.notification?.click_action ||
<?= json_encode($BASE . '/') ?>;

event.waitUntil((async () => {
const all = await clients.matchAll({ type: "window", includeUncontrolled: true });
for (const c of all) {
const u = new URL(c.url);
if (u.origin === location.origin && u.pathname.startsWith(<?= json_encode($BASE . '/') ?>)) {
try { await c.focus(); await c.navigate(openUrl); } catch {}
return;
}
}
await clients.openWindow(openUrl);
})());
});

// =============================
// Offline + Caching
// =============================
const APP_VERSION = <?= json_encode($APP_VERSION) ?>;
const PREFIX = 'dash';
const STATIC_CACHE = `${PREFIX}-static-${APP_VERSION}`;
const HTML_CACHE = `${PREFIX}-html-${APP_VERSION}`;
const RUNTIME_CACHE = `${PREFIX}-rt-${APP_VERSION}`;
const IMAGE_CACHE = `${PREFIX}-img-${APP_VERSION}`;
const API_CACHE = `${PREFIX}-api-${APP_VERSION}`;

const BASE = <?= json_encode($BASE) ?>;
const APP_SHELL = `${BASE}/`;
const OFFLINE_FALLBACK_HTML = <?= json_encode($OFFLINE_URL) ?>;
const MANIFEST_URL = <?= json_encode($MANIFEST) ?>;

const PRECACHE_URLS = [
APP_SHELL,
`${BASE}/index.php`,
OFFLINE_FALLBACK_HTML,
MANIFEST_URL,
// `${BASE}/assets/app.css`,
// `${BASE}/assets/app.js`,
];

const MAX_ENTRIES = {
[IMAGE_CACHE]: 80,
[API_CACHE]: 80,
[RUNTIME_CACHE]: 120
};

async function trimCache(cacheName, max) {
const c = await caches.open(cacheName);
const keys = await c.keys();
const over = keys.length - max;
for (let i = 0; i < over; i++) { await c.delete(keys[i]); } } // -------- install --------
    self.addEventListener("install", (e)=> {
    e.waitUntil((async () => {
    const c = await caches.open(STATIC_CACHE);
    // ensure fresh copies
    await Promise.all(
    PRECACHE_URLS.map((u) =>
    c.add(new Request(u, { cache: "reload" })).catch(() => {})
    )
    );
    self.skipWaiting();
    })());
    });

    // -------- activate --------
    self.addEventListener("activate", (e) => {
    e.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys.map((k) => {
    if (![STATIC_CACHE, HTML_CACHE, RUNTIME_CACHE, IMAGE_CACHE, API_CACHE].includes(k)) {
    return caches.delete(k);
    }
    }));
    if ("navigationPreload" in self.registration) {
    try { await self.registration.navigationPreload.enable(); } catch {}
    }
    await self.clients.claim();
    })());
    });

    // ---- Background Sync queue for failed mutations ----
    const DB_NAME = `${PREFIX}-bgq`;
    const STORE = "requests";

    function idb() {
    return new Promise((resolve, reject) => {
    const open = indexedDB.open(DB_NAME, 1);
    open.onupgradeneeded = () => open.result.createObjectStore(STORE, { keyPath: "id", autoIncrement: true });
    open.onsuccess = () => resolve(open.result);
    open.onerror = () => reject(open.error);
    });
    }

    async function enqueue(req) {
    const db = await idb();
    const tx = db.transaction(STORE, "readwrite");
    const body = req.method === "GET" ? null : await req.clone().arrayBuffer();
    tx.objectStore(STORE).add({
    url: req.url,
    method: req.method,
    headers: [...req.headers.entries()],
    body,
    ts: Date.now()
    });
    await tx.done?.catch?.(() => {});
    }

    async function dequeueAll() {
    const db = await idb();
    const tx = db.transaction(STORE, "readwrite");
    const store = tx.objectStore(STORE);

    const items = await new Promise((resolve) => {
    const out = [];
    const cur = store.openCursor();
    cur.onsuccess = () => {
    const c = cur.result;
    if (c) { out.push({ key: c.key, value: c.value }); c.continue(); }
    else { resolve(out); }
    };
    cur.onerror = () => resolve(out);
    });

    for (const { key, value } of items) {
    try {
    const res = await fetch(value.url, { method: value.method, headers: value.headers, body: value.body, credentials:
    "include" });
    if (res.ok) store.delete(key);
    } catch {}
    }
    await tx.done?.catch?.(() => {});
    }

    self.addEventListener("sync", (e) => {
    if (e.tag === `${PREFIX}-bg-sync`) e.waitUntil(dequeueAll());
    });

    // ---- Messages from page ----
    self.addEventListener("message", (e) => {
    const { type, payload } = e.data || {};
    if (type === "SKIP_WAITING") self.skipWaiting();
    if (type === "WARMUP" && Array.isArray(payload?.urls)) {
    e.waitUntil((async () => {
    const rt = await caches.open(RUNTIME_CACHE);
    await Promise.all(payload.urls.map(u => rt.add(u).catch(() => {})));
    })());
    }
    });

    // ---- Router ----
    self.addEventListener("fetch", (event) => {
    const req = event.request;
    const url = new URL(req.url);
    const sameOrigin = url.origin === self.location.origin;
    const inDash = url.pathname === BASE || url.pathname.startsWith(`${BASE}/`);

    // 1) Navigations (only dashboard)
    if (req.mode === "navigate" && inDash) {
    event.respondWith((async () => {
    try {
    const preload = await event.preloadResponse;
    if (preload) {
    const c = await caches.open(HTML_CACHE);
    c.put(req, preload.clone());
    trimCache(HTML_CACHE, 50);
    return preload;
    }
    const net = await fetch(req, { credentials: "include" });
    const c = await caches.open(HTML_CACHE);
    c.put(req, net.clone());
    trimCache(HTML_CACHE, 50);
    return net;
    } catch {
    const cachedExact = await caches.match(req, { ignoreSearch: false });
    if (cachedExact) return cachedExact;
    const shell = await caches.match(APP_SHELL, { ignoreSearch: true });
    if (shell) return shell;
    return (await caches.match(OFFLINE_FALLBACK_HTML)) ||
    new Response("Offline", { status: 503, headers: { "Content-Type": "text/plain" } });
    }
    })());
    return;
    }

    // 2) Dashboard API only
    if (inDash && url.pathname.startsWith(`${BASE}/api/`)) {
    if (req.method === "GET") {
    event.respondWith((async () => {
    const c = await caches.open(API_CACHE);
    try {
    const res = await fetch(req, { credentials: "include" });
    if (res.ok) c.put(req, res.clone());
    trimCache(API_CACHE, MAX_ENTRIES[API_CACHE]);
    return res;
    } catch {
    const cached = await c.match(req);
    if (cached) return cached;
    return new Response(JSON.stringify({ offline: true }), { status: 200, headers: { "Content-Type": "application/json"
    } });
    }
    })());
    return;
    }
    if (["POST","PUT","PATCH","DELETE"].includes(req.method)) {
    event.respondWith((async () => {
    try {
    return await fetch(req.clone(), { credentials: "include" });
    } catch {
    try {
    await enqueue(req);
    if ("sync" in self.registration) await self.registration.sync.register(`${PREFIX}-bg-sync`);
    } catch {}
    return new Response(JSON.stringify({ queued: true, offline: true }), { status: 202, headers: { "Content-Type":
    "application/json" } });
    }
    })());
    return;
    }
    }

    // 3) Same-origin assets (CSS/JS)
    if (sameOrigin && /\.(?:js|css)$/.test(url.pathname)) {
    event.respondWith((async () => {
    const c = await caches.open(RUNTIME_CACHE);
    const cached = await c.match(req);
    const updating = fetch(req).then((res) => {
    if (res && res.status === 200) c.put(req, res.clone());
    trimCache(RUNTIME_CACHE, MAX_ENTRIES[RUNTIME_CACHE]);
    return res;
    }).catch(() => null);
    return cached || (await updating) || fetch(req);
    })());
    return;
    }

    // 4) Same-origin images & fonts
    if (sameOrigin && /\.(?:png|jpg|jpeg|webp|gif|svg|ico|avif|woff2?)$/.test(url.pathname)) {
    event.respondWith((async () => {
    const c = await caches.open(IMAGE_CACHE);
    const cached = await c.match(req);
    if (cached) return cached;
    try {
    const res = await fetch(req);
    await c.put(req, res.clone());
    trimCache(IMAGE_CACHE, MAX_ENTRIES[IMAGE_CACHE]);
    return res;
    } catch {
    return cached || Response.error();
    }
    })());
    return;
    }

    // default passthrough
    // event.respondWith(fetch(req));
    });