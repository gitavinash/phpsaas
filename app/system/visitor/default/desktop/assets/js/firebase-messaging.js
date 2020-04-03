
  // Give the service worker access to Firebase Messaging.
  // Note that you can only use Firebase Messaging here, other Firebase libraries
  // are not available in the service worker.
  importScripts('https://www.gstatic.com/firebasejs/7.12.0/firebase-app.js');
  importScripts('https://www.gstatic.com/firebasejs/7.12.0/firebase-messaging.js');

  // Initialize the Firebase app in the service worker by passing in
  // your app's Firebase config object.
  // https://firebase.google.com/docs/web/setup#config-object

  firebase.initializeApp({
    apiKey: "AIzaSyCD03dFMGIVROeCm84Q1TIBoGR4DJBcYOc",
    authDomain: "isw-one.firebaseapp.com",
    databaseURL: "https://isw-one.firebaseio.com",
    projectId: "isw-one",
    storageBucket: "isw-one.appspot.com",
    messagingSenderId: "611764399880",
    appId: "1:611764399880:web:9bd49954f9a2cdb3efc6bd",
    measurementId: "G-HGWHECC9K9"
  });

  messaging.onMessage((payload) => {
    console.log('Message received. ', payload);
  });