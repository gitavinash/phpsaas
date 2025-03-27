$(document).ready(function (e) {
  var globalCopyContent = "";

  function copyToClipboard(content) {
    var tempDiv = $("<div contenteditable='true'>").html(content).css({
      position: "absolute",
      left: "-9999px",
    });

    $("body").append(tempDiv);

    var range = document.createRange();
    var selection = window.getSelection();
    range.selectNodeContents(tempDiv[0]);
    selection.removeAllRanges();
    selection.addRange(range);

    document.execCommand("copy"); // ✅ Copy as rich text (HTML)

    selection.removeAllRanges(); // Clear selection
    tempDiv.remove(); // Remove temp div
  }

  $("#upayDetailsBtn").click(function (e) {
    toggleDisplay("#upayDetailsBox", false);
    e.preventDefault();
  });

  $("#upayBtn").click(function (e) {
    document.querySelector(".upayBox").style.display = "flex";
    $(".upayBox").show();
    hidePageLoader();
    e.preventDefault();
  });

  $("#addUpayBtn").click(function (e) {
    e.preventDefault();

    var data = new FormData(document.getElementById("addUpayForm"));
    data.append("leadId", leadId);
    $.ajax({
      url: "/api/astro_client/leads/upay/",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      dataType: "JSON",
      encrypt: "multipart/form-data",
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () {
          location.reload();
        }, 1500);
      },
      error: function (error, response) {
        popUpMessage(
          "center",
          error.responseJSON.msg,
          error.responseJSON.status
        );
        $("#addAmenities").show();
      },
    });
  });

  $(".upayEditButton").click(function (e) {
    e.preventDefault();
    var dataId = $(this).attr("data-id");
    var dataContent = $(this).attr("data-msg");
    var decodedHtml = utf8Base64Decode(dataContent);
    var tempDiv = $("<div>").html(decodedHtml);
    tempDiv.find(".imagesList").remove();
    var cleanedHtml = tempDiv.find(".comment").html().trim();
    $("#editUpayComment").val(cleanedHtml);
    globalCopyContent = cleanedHtml;
    document.querySelector(".editUpayBox").style.display = "flex";
    $(".editUpayBox").show();
    hidePageLoader();
  });

  function utf8Base64Decode(str) {
    return decodeURIComponent(escape(window.atob(str)));
  }

  $("#upayContentValue").click(function (e) {
    e.preventDefault();
    if (globalCopyContent) {
      copyToClipboard(globalCopyContent);
      popUpMessage(
        "center",
        "Copied to clipboard! Now paste it in the editor.",
        "success"
      );
      setTimeout(function () {
        window.open($("#upayContentValue").attr("href"), "_blank");
      }, 500);
    } else {
      alert("No content available to copy.");
    }
  });
});
