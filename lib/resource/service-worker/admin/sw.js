importScripts(
  "https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"
);

const firebaseConfig = {
  apiKey: "AIzaSyBlo7GZPUUMNywiJYEHv-RqnFTTQUQDwUA",
  authDomain: "aleya-af985.firebaseapp.com",
  projectId: "aleya-af985",
  storageBucket: "aleya-af985.appspot.com",
  messagingSenderId: "876838268173",
  appId: "1:876838268173:web:8ad5afeae5de209e4d9444",
};

const firebaseApp = firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// messaging.onBackgroundMessage(function (payload) {
//   const openUrl = payload.data.click_action;
//   const payloadData = payload.notification;

//   console.log(`payload on background:`, payloadData);
//   console.log(`payload on url:`, openUrl);

//   if (!payload.hasOwnProperty("notification")) {
//     const notificationTitle = payloadDatatitle;
//     const notificationOptions = {
//       body: payloadData.body,
//       icon: payloadData.icon,
//       image: payloadData.image,
//     };
//     self.registration.showNotification(notificationTitle, notificationOptions);

//     self.addEventListener("notificationclick", function (event) {
//       const clickedNotification = event.notification;
//       clickedNotification.close();
//       if (openUrl) {
//         event.waitUntil(clients.openWindow(event.openUrl));
//       }
//     });
//   }
// });
messaging.onBackgroundMessage(function (payload) {
  const notificationData = payload.notification;
  const clickActionUrl = payload.data.click_action;

  console.log("Notification Data:", notificationData);

  if (notificationData) {
    const notificationTitle = notificationData.title;
    const notificationOptions = {
      body: notificationData.body,
      icon: notificationData.icon,
      image: notificationData.image,
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
  }
});

self.addEventListener("notificationclick", function (event) {
  const clickedNotification = event.notification;
  clickedNotification.close();

  const openUrl = event.notification.data.click_action;
  console.log("Click Action URL:", openUrl);

  if (openUrl) {
    event.waitUntil(
      clients
        .openWindow(openUrl)
        .then(() => console.log("Window opened successfully."))
        .catch((error) => console.error("Error opening window:", error))
    );
  } else {
    console.warn("No click action specified in the notification data.");
  }
});
