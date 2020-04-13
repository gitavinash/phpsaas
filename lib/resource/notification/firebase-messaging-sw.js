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