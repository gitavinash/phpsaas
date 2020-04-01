const firebaseConfig = {
  apiKey: "AIzaSyCD03dFMGIVROeCm84Q1TIBoGR4DJBcYOc",
  authDomain: "isw-one.firebaseapp.com",
  databaseURL: "https://isw-one.firebaseio.com",
  projectId: "isw-one",
  storageBucket: "isw-one.appspot.com",
  messagingSenderId: "611764399880",
  appId: "1:611764399880:web:9bd49954f9a2cdb3efc6bd",
  measurementId: "G-HGWHECC9K9"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Add the public key generated from the console here.
messaging.usePublicVapidKey("BDqd94hGAtdU3-XdG3MtNtB8VTg1cjv8AOtHtAIxyfdPALJ9hHmjBJnfB8ZExRc-T5EFiFceYUrMztiwPZINOBY");


// Get Instance ID token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
messaging.getToken().then((currentToken) => {
  if (currentToken) {
    sendTokenToServer(currentToken);
    updateUIForPushEnabled(currentToken);
  } else {
    // Show permission request.
    console.log('No Instance ID token available. Request permission to generate one.');
    // Show permission UI.
    updateUIForPushPermissionRequired();
    setTokenSentToServer(false);
  }
}).catch((err) => {
  console.log('An error occurred while retrieving token. ', err);
  showToken('Error retrieving Instance ID token. ', err);
  setTokenSentToServer(false);
});