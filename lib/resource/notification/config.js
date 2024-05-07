// For Firebase JS SDK v7.20.0 and later, measurementId is optional
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

messaging
  .getToken({
    validKey:
      "BOgqBVEQIhfp7YZ-vzhQ6ih1d2bvFlSFnzL3behhenljYXpLNrMtLz0yHCPj5gJsw80NmWVyOkLtkIOFTan2i60",
  })
  .then((currentToken) => {
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
  console.log("Message received ", payload);
});
