// This is a service worker file

// This section for PWA dynamic caching

const cacheName = self.location.hostname+'_v1';

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
});

// Call fetch event
self.addEventListener('fetch', e => {
  console.log('Fetching cache');
  e.respondWith(
    fetch(e.request)
    .then(res => {
      
      const resClone = res.clone();

      caches.open(cacheName).then(cache => {
        cache.put(e.request, resClone);
      });
      return res;
    })
    .catch(
      err => caches.match(e.request).then(res => res).catch()
    )
  );
})

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.open('mysite-dynamic').then(function(cache) {
      return cache.match(event.request).then(function (response) {
        return response || fetch(event.request).then(function(response) {
          cache.put(event.request, response.clone());
          return response;
        });
      });
    })
  );
});

// This section is dedicated to firebase push notification

importScripts('https://www.gstatic.com/firebasejs/7.13.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.13.2/firebase-messaging.js');
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