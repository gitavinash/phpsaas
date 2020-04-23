// This is a service worker file

// This section for PWA dynamic caching

const cacheName = 'v2sdasdfddsdfw';

// Call Activate Event
self.addEventListener('activate', e => {
  console.log('Service Worker: Activated');
  // Remove unwanted caches
  e.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== cacheName) {
            console.log('Service Worker: Clearing Old Cache');
            return caches.delete(cache);
          } else {
            console.log('no cache found');
          }
        })
      );
    })
  );
})

// Call Fetch Event
self.addEventListener('fetch', e => {
  e.respondWith(
    fetch(e.request)
      .then(res => {
        // Make copy/clone of response
        const resClone = res.clone();
        // Open cahce
        caches.open(cacheName).then(cache => {
          // Add response to cache
          cache.put(e.request, resClone);
        });
        return res;
      })
      .catch(err => caches.match(e.request).then(res => res))
  );
})


// This section is dedicated to firebase push notification

importScripts('https://www.gstatic.com/firebasejs/7.12.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.12.0/firebase-messaging.js');
importScripts('/lib/resource/notification/config.js');

messaging.setBackgroundMessageHandler(function(payload) {

    // Customize notification here
    const notificationTitle = payload.notification.title;

    const notificationOptions = {
      body: payload.notification.body,
      icon: payload.notification.icon
    };

    // new Notification(notificationTitle, notificationOption);
  
    return self.registration.showNotification(notificationTitle, notificationOptions);
});