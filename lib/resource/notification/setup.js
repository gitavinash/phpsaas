// Initialize firebase app
firebase.initializeApp(firebaseConfig);

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

// Add the public key generated from the console here.
messaging.usePublicVapidKey("AAAAjm__7wg:APA91bE09Yzo6K0GidEpDzpkbTRdWpkAcjXSwi3mL7gasl4ZMi0tkpBUhFzSFdBORh-SWDsgWo-OU7xO3epzU7b85zBSku8LIQWfnjJkUTK-ngp-WRjJIE9sUdd4yjf1cjwlkRFwJkns");

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