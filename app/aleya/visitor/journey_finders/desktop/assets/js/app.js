$(document).ready(function () {
  $("#menuToggle").on("click", function () {
    $("#mobileMenu").removeClass("-translate-x-full").addClass("translate-x-0");
  });

  $("#menuClose").on("click", function () {
    $("#mobileMenu").removeClass("translate-x-0").addClass("-translate-x-full");
  });

  $("#searchTours").on("click", function () {
    const destination = $("#destination").val();
    if (destination) {
      console.log(`Searching tours for destination: ${destination}`);
      window.location.href = `/destination/${encodeURIComponent(destination)}/`;
    } else {
      console.log("No destination entered.");
      alert("Please enter a destination to search for tours.");
    }
  });
});
