// Firebase notification config — aleya-notification project (FCM v1)
// Supports both Service Worker and Window context safely

const firebaseConfig = {
  apiKey: "AIzaSyDALB6eWd90PHwZbZMjiFafazeVeLL_a1E",
  authDomain: "aleya-notification.firebaseapp.com",
  projectId: "aleya-notification",
  storageBucket: "aleya-notification.firebasestorage.app",
  messagingSenderId: "782586665015",
  appId: "1:782586665015:web:de675497e5af4fd68e07b2",
  measurementId: "G-QDHBYJHHVV"
};

const isSW =
  (typeof ServiceWorkerGlobalScope !== "undefined" && self instanceof ServiceWorkerGlobalScope) ||
  (typeof window === "undefined" && typeof importScripts === "function");
const isWindow = typeof window !== "undefined" && typeof document !== "undefined";

// ==========================================
// 1. Service Worker Context (Background)
// ==========================================
if (isSW && typeof firebase !== "undefined") {
  try {
    if (!firebase.apps || firebase.apps.length === 0) {
      firebase.initializeApp(firebaseConfig);
    }
    const messaging = firebase.messaging();

    if (typeof messaging.onBackgroundMessage === "function") {
      messaging.onBackgroundMessage(function (payload) {
        const n = payload && (payload.notification || {});
        const notificationTitle = (n && n.title) || (payload.data && payload.data.title) || "New Notification";
        const notificationOptions = {
          body: (n && n.body) || (payload.data && payload.data.body) || "",
          icon: (n && n.icon) || (payload.data && payload.data.icon) || "/favicon.ico",
          image: (n && n.image) || (payload.data && payload.data.image) || "",
          data: payload.data || {},
          badge: (n && n.icon) || "/favicon.ico",
        };
        return self.registration.showNotification(notificationTitle, notificationOptions);
      });
    } else if (typeof messaging.setBackgroundMessageHandler === "function") {
      messaging.setBackgroundMessageHandler(function (payload) {
        const n = payload && (payload.notification || {});
        const notificationTitle = (n && n.title) || (payload.data && payload.data.title) || "New Notification";
        const notificationOptions = {
          body: (n && n.body) || (payload.data && payload.data.body) || "",
          icon: (n && n.icon) || (payload.data && payload.data.icon) || "/favicon.ico",
          image: (n && n.image) || (payload.data && payload.data.image) || "",
          data: payload.data || {},
        };
        return self.registration.showNotification(notificationTitle, notificationOptions);
      });
    }
  } catch (e) {
    console.warn("[SW] Background messaging init skipped:", e);
  }

  // Notification click: open the target URL
  self.addEventListener("notificationclick", function (event) {
    event.notification.close();
    const urlToOpen = (event.notification.data && (event.notification.data.url || event.notification.data.click_action))
      || "/dashboard/leads/list/";
    event.waitUntil(
      clients.matchAll({ type: "window", includeUncontrolled: true }).then(function (windowClients) {
        for (var i = 0; i < windowClients.length; i++) {
          var client = windowClients[i];
          if (client.url === urlToOpen && "focus" in client) {
            return client.focus();
          }
        }
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
    );
  });
}

// ==========================================
// 2. Browser Window Context (Foreground)
// ==========================================
if (isWindow && typeof firebase !== "undefined") {
  try {
    if (!firebase.apps || firebase.apps.length === 0) {
      firebase.initializeApp(firebaseConfig);
    }
    const messagingFg = firebase.messaging();

    const activeVapidKey = typeof vapidKey !== "undefined"
      ? vapidKey
      : "BCrkm9qYYcj18SvNMa4906f8jm9fcDnE9ld1hHM0OT0TkiyPzEJN_SCwH3P8ioSQGYkhh3e3saO9t9QeC8TdRUA";

    messagingFg.getToken({
      vapidKey: activeVapidKey,
    }).then(function (currentToken) {
      if (currentToken) {
        if (typeof $ !== "undefined" && typeof domain !== "undefined") {
          $.ajax({
            url: domain + "/api/notification/register/",
            type: "GET",
            data: { device: (typeof deviceType !== "undefined" ? deviceType : "web"), token: currentToken },
            success: function () { /* registered */ },
            error: function () { /* silent fail */ }
          });
        }
      }
    }).catch(function () {
      /* Permission denied or blocked — silent */
    });

    messagingFg.onMessage(function (payload) {
      const n = payload && (payload.notification || {});
      const notificationTitle = (n && n.title) || (payload.data && payload.data.title) || "New Notification";
      const notificationOptions = {
        body: (n && n.body) || (payload.data && payload.data.body) || "",
        icon: (n && n.icon) || (payload.data && payload.data.icon) || "/favicon.ico",
        image: (n && n.image) || (payload.data && payload.data.image) || "",
        data: payload.data || {},
      };
      if ("Notification" in window && Notification.permission === "granted") {
        if ("serviceWorker" in navigator && navigator.serviceWorker.controller) {
          navigator.serviceWorker.ready.then(function (reg) {
            reg.showNotification(notificationTitle, notificationOptions);
          });
        } else {
          new Notification(notificationTitle, notificationOptions);
        }
      }
    });
  } catch (e) {
    console.warn("[FCM] Foreground messaging init skipped:", e);
  }
}
