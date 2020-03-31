<html>
    <head>
        <title>Test Notification</title>
        <!-- <link rel="mainfest" href="/mainfest.json"> -->
    </head>
    <body>
        <header>

        </header>
        <!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-messaging.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script>
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyCD03dFMGIVROeCm84Q1TIBoGR4DJBcYOc",
    authDomain: "isw-one.firebaseapp.com",
    databaseURL: "https://isw-one.firebaseio.com",
    projectId: "isw-one",
    storageBucket: "isw-one.appspot.com",
    messagingSenderId: "611764399880",
    appId: "1:611764399880:web:9bd49954f9a2cdb3efc6bd",
    measurementId: "G-HGWHECC9K9"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();

  // Retrieve Firebase Messaging object.
    const messaging = firebase.messaging();

    // Add the public key generated from the console here.
messaging.usePublicVapidKey("BDqd94hGAtdU3-XdG3MtNtB8VTg1cjv8AOtHtAIxyfdPALJ9hHmjBJnfB8ZExRc-T5EFiFceYUrMztiwPZINOBY");

</script>
    </body>
</html>