// This is a service worker file

// This section for PWA dynamic caching

const cacheName = siteName;

// Call Activate Event
self.addEventListener("activate", (e) => {
  console.log("Service Worker: Activated");
  // Remove unwanted caches
  e.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== cacheName) {
            console.log("Service Worker: Clearing Old Cache");
            return caches.delete(cache);
          } else {
            console.log("no cache found");
          }
        })
      );
    })
  );
});

// This section is dedicated to firebase push notification

importScripts("https://www.gstatic.com/firebasejs/7.12.0/firebase-app.js");
importScripts(
  "https://www.gstatic.com/firebasejs/7.12.0/firebase-messaging.js"
);
importScripts("/lib/resource/notification/config.js");

messaging.setBackgroundMessageHandler(function (payload) {
  // Customize notification here
  const notificationTitle = payload.notification.title;

  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon,
  };

  // new Notification(notificationTitle, notificationOption);

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions
  );
});
