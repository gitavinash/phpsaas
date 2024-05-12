self.addEventListener("notificationclick", function (event) {
  console.log(`event: `, event.notification);
  const clickedNotification = payload.fcmOptions;
  clickedNotification.close();
  if (openUrl) {
    event.waitUntil(clients.openWindow(event.openUrl));
  }
});
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCuX0bl2uzinWe8ZF0cTMdpQpJ1iHOETlI",
  authDomain: "aleya-eea0c.firebaseapp.com",
  projectId: "aleya-eea0c",
  storageBucket: "aleya-eea0c.appspot.com",
  messagingSenderId: "908078528641",
  appId: "1:908078528641:web:f377926009d81321b4f580",
};

const firebaseApp = firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging
  .getToken({
    validKey:
      "BOgqBVEQIhfp7YZ-vzhQ6ih1d2bvFlSFnzL3behhenljYXpLNrMtLz0yHCPj5gJsw80NmWVyOkLtkIOFTan2i60",
  })
  .then((currentToken) => {
    $(".enableNotification").hide();
    console.log(`currentToken:`, currentToken);
    sendTokenToServer(currentToken);
  })
  .catch((err) => {
    console.log(
      `Something went wrong while fetching device token for notification:`,
      err
    );
    setTokenSentToServer(false);
  });

function sendTokenToServer(currentToken) {
  if (!isTokenSentToServer()) {
    setTokenSentToServer(true);
    saveToken(currentToken);
  }
}

function isTokenSentToServer() {
  return window.localStorage.getItem("sentToServer") === "1";
}

function setTokenSentToServer(sent) {
  window.localStorage.setItem("sentToServer", sent ? "1" : "0");
}

function saveToken(currentToken) {
  $.ajax({
    url: domain + "/api/notification/register/",
    type: "GET",
    data: {
      device: deviceType,
      token: currentToken,
    },
    success: function (data) {
      console.log(data.msg);
    },
    error: function (data) {
      console.error(`Error : `, data);
    },
  });
}

messaging.onMessage((payload) => {
  const payloadData = payload.notification;

  console.log(`onMessage payload on background:`, payloadData);

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
