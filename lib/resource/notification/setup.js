console.log(
  "%c Developed by Blumox Technologies",
  "color : #01ABDF; font-weight : bold; font-size: 18px"
);
console.log(
  "%c see www.blumox.com for more information",
  "color: #333; font-weight: bold; font-size: 14px"
);

// Add the public key generated from the console here.
messaging.usePublicVapidKey(
  "BOgqBVEQIhfp7YZ-vzhQ6ih1d2bvFlSFnzL3behhenljYXpLNrMtLz0yHCPj5gJsw80NmWVyOkLtkIOFTan2i60"
);

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        // Handle retrieved location
        var longitude = position.coords.longitude;
        var latitude = position.coords.latitude;
        // Perform relevant operations with latitude and longitude
        console.log("Latitude:", latitude, "Longitude:", longitude);
      },
      function (error) {
        console.error("Error getting geolocation:", error.message);
      }
    );
  } else {
    console.error("Geolocation is not supported by this browser.");
  }
}

function showPosition(position) {
  longitude = position.coords.longitude;
  latitude = position.coords.latitude;
}

if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    // declaring scope manually
    navigator.serviceWorker
      .register("./sw.js", {
        scope: "/dashboard/",
      })
      .then(
        (registration) => {
          console.log("Service worker registration succeeded:", registration);

          messaging.useServiceWorker(registration);

          messaging
            .getToken()
            .then((currentToken) => {
              if (currentToken) {
                /** Store Browser Id into Database */
                sendTokenToServer(currentToken);
              } else {
                console.log(
                  "No Instance ID token available. Request permission to generate one."
                );
                setTokenSentToServer(false);
              }
            })
            .catch((err) => {
              console.warn(`Kindly enable push notification`);
              setTokenSentToServer(false);
            });

          messaging.onTokenRefresh(() => {
            messaging
              .getToken()
              .then((refreshedToken) => {
                console.log(`device token is refreshed`);
                setTokenSentToServer(false);
                sendTokenToServer(refreshedToken);
              })
              .catch((err) => {
                console.log("Unable to retrieve refreshed token ", err);
              });
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
        },
        (error) => {
          console.error(`Service worker registration failed: `, error);
        }
      );
    messaging.onMessage((payload) => {
      console.log(`onMessage: `, payload);
    });

    // messaging.onBackgroundMessage((payload) => {
    //   console.log(`onBackgroundMessage: `, payload);
    // });
  });
} else {
  console.error("Service workers are not supported.");
}

/** Fetch device location */

getLocation();
