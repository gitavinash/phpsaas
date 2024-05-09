importScripts(
  "https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"
);

self.addEventListener("notificationclick", function (event) {
  const openUrl = event.notification?.data?.FCM_MSG?.notification?.click_action;
  console.log(`I will open this url: `, openUrl);
  if (openUrl) {
    event.waitUntil(clients.openWindow(openUrl));
  }
});

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

messaging.onBackgroundMessage(function (payload) {
  const payloadData = payload.notification;

  if (!payload.hasOwnProperty("notification")) {
    const notificationTitle = payloadDatatitle;
    const notificationOptions = {
      body: payloadData.body,
      icon: payloadData.icon,
      image: payloadData.image,
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
  }
});
