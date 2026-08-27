// Firebase notification config — aleya-notification project (FCM v1)
// Uses Firebase compat SDK loaded via importScripts in service worker context

const firebaseConfig = {
  apiKey: "AIzaSyDALB6eWd90PHwZbZMjiFafazeVeLL_a1E",
  authDomain: "aleya-notification.firebaseapp.com",
  projectId: "aleya-notification",
  storageBucket: "aleya-notification.firebasestorage.app",
  messagingSenderId: "782586665015",
  appId: "1:782586665015:web:de675497e5af4fd68e07b2",
  measurementId: "G-QDHBYJHHVV"
};

// Initialize Firebase (guard against re-init in SW context)
if (typeof firebase !== "undefined") {
  if (!firebase.apps || firebase.apps.length === 0) {
    firebase.initializeApp(firebaseConfig);
  }
  const messaging = firebase.messaging();

  // Background message handler (service worker context)
  if (typeof messaging.onBackgroundMessage === "function") {
    messaging.onBackgroundMessage(function (payload) {
      const notificationTitle = (payload.notification && payload.notification.title) || "New Notification";
      const notificationOptions = {
        body: (payload.notification && payload.notification.body) || "",
        icon: (payload.notification && payload.notification.icon) || "/favicon.ico",
        image: (payload.notification && payload.notification.image) || "",
        data: payload.data || {},
        badge: (payload.notification && payload.notification.icon) || "/favicon.ico",
      };
      return self.registration.showNotification(notificationTitle, notificationOptions);
    });
  } else if (typeof messaging.setBackgroundMessageHandler === "function") {
    // Compat fallback
    messaging.setBackgroundMessageHandler(function (payload) {
      const notificationTitle = (payload.notification && payload.notification.title) || "New Notification";
      const notificationOptions = {
        body: (payload.notification && payload.notification.body) || "",
        icon: (payload.notification && payload.notification.icon) || "/favicon.ico",
        image: (payload.notification && payload.notification.image) || "",
        data: payload.data || {},
      };
      return self.registration.showNotification(notificationTitle, notificationOptions);
    });
  }
}

// Notification click: open the click_action URL
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

// Foreground token registration (runs in browser window context, not SW)
if (typeof firebase !== "undefined" && typeof window !== "undefined") {
  const messagingFg = firebase.messaging();

  messagingFg.getToken({
    vapidKey: "BCrkm9qYYcj18SvNMa4906f8jm9fcDnE9ld1hHM0OT0TkiyPzEJN_SCwH3P8ioSQGYkhh3e3saO9t9QeC8TdRUA",
  }).then(function (currentToken) {
    if (currentToken) {
      if (typeof $ !== "undefined" && typeof domain !== "undefined") {
        $.ajax({
          url: domain + "/api/notification/register/",
          type: "GET",
          data: { device: (typeof deviceType !== "undefined" ? deviceType : "web"), token: currentToken },
          success: function (data) { /* registered */ },
          error: function () { /* silent fail */ }
        });
      }
    }
  }).catch(function (err) {
    /* Permission denied or blocked — silent */
  });

  messagingFg.onMessage(function (payload) {
    const notificationTitle = (payload.notification && payload.notification.title) || "New Notification";
    const notificationOptions = {
      body: (payload.notification && payload.notification.body) || "",
      icon: (payload.notification && payload.notification.icon) || "/favicon.ico",
      image: (payload.notification && payload.notification.image) || "",
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
}
