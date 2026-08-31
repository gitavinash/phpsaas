<?php

declare(strict_types=1);

/**
 * Service Worker (served via PHP) — FULL FILE
 * Outputs JavaScript with correct headers. No closing PHP tag.
 */
header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate'); // always fetch fresh SW
header('Service-Worker-Allowed: /dashboard/');                 // allow scope

// Wire to your constants if you want:
$APP_VERSION = 'dash-v1.2.4';
$BASE        = '/dashboard';
$OFFLINE_URL = '/offline/'; // actual offline route (directory)
$MANIFEST    = $BASE . '/manifest.json';
$LOGO        = defined('SITE_ICON') ? SITE_ICON : ($BASE . '/assets/logo.svg');

// Pre-encode PHP strings for safe JS embedding:
$js_APP_VERSION = json_encode($APP_VERSION);
$js_BASE        = json_encode($BASE);
$js_OFFLINE_URL = json_encode($OFFLINE_URL);
$js_MANIFEST    = json_encode($MANIFEST);
$js_LOGO        = json_encode($LOGO);

?>
/* =============================
Dashboard Service Worker + FCM
Scope: /dashboard/
Served by PHP as JS
============================ */

// ------------ Firebase (compat) — aleya-notification project ------------
try {
importScripts("https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging-compat.js");
} catch (e) {
// offline? keep going
console.warn("[SW] Firebase importScripts failed (likely offline).", e);
}

const firebaseConfig = {
apiKey: "AIzaSyDALB6eWd90PHwZbZMjiFafazeVeLL_a1E",
authDomain: "aleya-notification.firebaseapp.com",
projectId: "aleya-notification",
storageBucket: "aleya-notification.firebasestorage.app",
messagingSenderId: "782586665015",
appId: "1:782586665015:web:de675497e5af4fd68e07b2",
};

try {
if (self.firebase && (!self.firebase.apps || self.firebase.apps.length === 0)) {
self.firebase.initializeApp(firebaseConfig);
}
} catch (e) {
console.warn("[SW] Firebase init failed.", e);
}

const messaging = (self.firebase && self.firebase.messaging) ? self.firebase.messaging() : null;

// Background notifications (FCM)
if (messaging) {
messaging.onBackgroundMessage((payload) => {
const n = payload && (payload.notification || {});
const title = (n && n.title) || (payload.data && payload.data.title) || "Notification";
const body = (n && n.body) || (payload.data && payload.data.body) || "";
const icon = (n && n.icon) || (payload.data && payload.data.icon) || <?php echo $js_LOGO; ?>;
const image = (n && n.image) || (payload.data && payload.data.image);
const click_action =
(n && n.click_action) || (payload.data && payload.data.click_action) || (<?php echo $js_BASE; ?> + "/");

self.registration.showNotification(title, { body, icon, image, data: { click_action } });
});
}

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const d = (event.notification && event.notification.data) || {};
  const fcmMsg = d.FCM_MSG || {};
  const fcmNotif = fcmMsg.notification || {};
  const openUrl = d.click_action || d.url || fcmNotif.click_action || fcmNotif.url || (<?php echo $js_BASE; ?> + "/");

  event.waitUntil((async () => {
    const all = await clients.matchAll({ type: "window", includeUncontrolled: true });
    for (const c of all) {
      try {
        const u = new URL(c.url);
        if (u.origin === location.origin) {
          await c.focus();
          await c.navigate(openUrl);
          return;
        }
      } catch {}
    }
    if (clients.openWindow) {
      await clients.openWindow(openUrl);
    }
  })());
});

// ---------------- Caching Names & Constants ----------------
const APP_VERSION = <?php echo $js_APP_VERSION; ?>;
const PREFIX = "dash";
const STATIC_CACHE = `${PREFIX}-static-${APP_VERSION}`;
const HTML_CACHE = `${PREFIX}-html-${APP_VERSION}`;
const RUNTIME_CACHE = `${PREFIX}-rt-${APP_VERSION}`;
const IMAGE_CACHE = `${PREFIX}-img-${APP_VERSION}`;
const API_CACHE = `${PREFIX}-api-${APP_VERSION}`;

const BASE = <?php echo $js_BASE; ?>;
const APP_SHELL = `${BASE}/`;

// Offline primary + secondary (handle both with/without index.php)
const OFFLINE_PRIMARY = <?php echo $js_OFFLINE_URL; ?>; // e.g. "/offline/"
const OFFLINE_SECONDARY = "/offline/index.php";

const MANIFEST_URL = <?php echo $js_MANIFEST; ?>;

const MAX_ENTRIES = { [IMAGE_CACHE]: 80, [API_CACHE]: 80, [RUNTIME_CACHE]: 120 };
const NAV_TIMEOUT_MS = 4000;

// ---------------- Utility Helpers ----------------
function timeout(ms) {
return new Promise((_, rej) => setTimeout(() => rej(new Error("timeout")), ms));
}

async function trimCache(cacheName, max) {
const c = await caches.open(cacheName);
const keys = await c.keys();
const over = keys.length - max;
for (let i = 0; i < over; i++) { await c.delete(keys[i]); } } // ---------------- Install (precache) ----------------
    self.addEventListener("install", (e)=> {
    e.waitUntil((async () => {
    const c = await caches.open(STATIC_CACHE);
    const toPrecache = [
    APP_SHELL,
    `${BASE}/index.php`,
    MANIFEST_URL,
    OFFLINE_PRIMARY,
    OFFLINE_SECONDARY,
    ];

    for (const u of toPrecache) {
    try {
    console.log("[SW] precache:", u);
    await c.add(new Request(u, { cache: "reload" }));
    } catch (err) {
    console.warn("[SW] precache failed:", u, err);
    }
    }
    self.skipWaiting();
    })());
    });

    // ---------------- Activate ----------------
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

    // ---------------- Background Sync queue (failed mutations) ----------------
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
    await (tx.done && tx.done.catch ? tx.done.catch(() => {}) : Promise.resolve());
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
    } catch {
    // keep queued
    }
    }
    await (tx.done && tx.done.catch ? tx.done.catch(() => {}) : Promise.resolve());
    }

    self.addEventListener("sync", (e) => {
    if (e.tag === `${PREFIX}-bg-sync`) e.waitUntil(dequeueAll());
    });

    // ---------------- Messages from page ----------------
    self.addEventListener("message", async (e) => {
    const data = e.data || {};
    if (data.type === "SKIP_WAITING") self.skipWaiting();

    if (data.type === "WARMUP" && Array.isArray(data.payload && data.payload.urls)) {
    e.waitUntil((async () => {
    const rt = await caches.open(RUNTIME_CACHE);
    await Promise.all(data.payload.urls.map((u) => rt.add(u).catch(() => {})));
    })());
    }

    // Debugging: list cached URLs in all known caches
    if (data.type === "DUMP_CACHES") {
    const names = [STATIC_CACHE, HTML_CACHE, API_CACHE, IMAGE_CACHE, RUNTIME_CACHE];
    for (const name of names) {
    const c = await caches.open(name);
    const keys = await c.keys();
    console.log(`[SW] Cache "${name}" has ${keys.length} entries:`);
    keys.forEach((r, idx) => console.log(` ${idx + 1}.`, r.url));
    }
    }
    });

    // ---------------- Router ----------------
    self.addEventListener("fetch", (event) => {
    const req = event.request;
    const url = new URL(req.url);
    const sameOrigin = url.origin === self.location.origin;
    const inDash = (url.pathname === BASE) || url.pathname.startsWith(`${BASE}/`);

    // 1) Navigations (only /dashboard/*)
    if (req.mode === "navigate" && inDash) {
    event.respondWith((async () => {
    const htmlCache = await caches.open(HTML_CACHE);

    // Navigation Preload (fast path)
    const preload = await event.preloadResponse;
    if (preload) {
    console.log("[SW] using navigation preload for:", url.href);
    htmlCache.put(req, preload.clone());
    trimCache(HTML_CACHE, 50);
    return preload;
    }

    // Network with timeout
    try {
    const net = await Promise.race([
    fetch(req, { credentials: "include" }),
    timeout(NAV_TIMEOUT_MS),
    ]);
    if (net && net.ok) {
    console.log("[SW] network OK for:", url.href);
    htmlCache.put(req, net.clone());
    trimCache(HTML_CACHE, 50);
    return net;
    }
    } catch (err) {
    console.warn("[SW] network failed for:", url.href, err);
    }

    // Exact cached page
    const cachedExact = await htmlCache.match(req, { ignoreSearch: false });
    if (cachedExact) {
    console.log("[SW] using cached page for:", url.href);
    return cachedExact;
    }

    // App shell
    const shell = await caches.match(APP_SHELL, { ignoreSearch: true });
    if (shell) {
    console.log("[SW] using app shell for:", url.href, "->", APP_SHELL);
    return shell;
    }

    // OFFLINE fallback (try both)
    let offline = await caches.match(OFFLINE_PRIMARY);
    if (offline) {
    console.log("[SW] using OFFLINE (primary):", OFFLINE_PRIMARY);
    return offline;
    }
    offline = await caches.match(OFFLINE_SECONDARY);
    if (offline) {
    console.log("[SW] using OFFLINE (secondary):", OFFLINE_SECONDARY);
    return offline;
    }

    console.warn("[SW] offline fallback missing – returning text response");
    return new Response("Offline", { status: 503, headers: { "Content-Type": "text/plain" } });
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
    return new Response(JSON.stringify({ offline: true }), {
    status: 200, headers: { "Content-Type": "application/json" }
    });
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
    return new Response(JSON.stringify({ queued: true, offline: true }), {
    status: 202, headers: { "Content-Type": "application/json" }
    });
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

    // default passthrough (do nothing)
    });