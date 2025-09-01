/* register service worker */

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
        },
        (error) => {
          console.error(`Service worker registration failed: `, error);
        }
      );
  });
} else {
  console.error("Service workers are not supported.");
}

// // Check if the browser supports the Notification API
// if ("Notification" in window) {
//   // Check if the user has granted permission for notifications
//   if (Notification.permission === "granted") {
//     // If permission is granted, you can show notifications
//     // Your notification logic here
//   } else if (Notification.permission !== "denied") {
//     // If permission is not granted or denied, request permission
//     Notification.requestPermission().then((permission) => {
//       if (permission === "granted") {
//         // Permission granted, you can show notifications
//         // Your notification logic here
//       } else {
//         // Permission denied or dismissed
//         console.log("Notification permission denied");
//       }
//     });
//   }
// }

// // Check if the browser supports the Geolocation API
// if ("geolocation" in navigator) {
//   // Request location permission
//   navigator.permissions.query({ name: "geolocation" }).then((result) => {
//     if (result.state === "granted") {
//       // Permission already granted, you can use geolocation
//       // Your geolocation logic here
//     } else if (result.state === "prompt") {
//       // Permission not granted yet, request permission
//       navigator.geolocation.getCurrentPosition(
//         (position) => {
//           // Geolocation permission granted, you can use geolocation
//           // Your geolocation logic here
//         },
//         (error) => {
//           // Geolocation permission denied or error occurred
//           console.error("Geolocation permission denied or error:", error);
//         }
//       );
//     } else {
//       // Permission denied
//       console.log("Geolocation permission denied");
//     }
//   });
// } else {
//   // Geolocation not supported by the browser
//   console.log("Geolocation is not supported by this browser");
// }
