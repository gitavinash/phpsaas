// Add the public key generated from the console here.
messaging.usePublicVapidKey("BDqd94hGAtdU3-XdG3MtNtB8VTg1cjv8AOtHtAIxyfdPALJ9hHmjBJnfB8ZExRc-T5EFiFceYUrMztiwPZINOBY");

navigator.serviceWorker.register('/lib/resource/notification/firebase-messaging-sw.js').then((registration) => {

    messaging.useServiceWorker(registration);

});

// Get Instance ID token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
messaging.getToken().then(
    (currentToken) => {
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
    }
);