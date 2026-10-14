/**
 * Link Shortener Client Application
 * Uses SweetAlert2 for modern alerts, confirmations, and copy notifications by default.
 */

document.addEventListener("DOMContentLoaded", function () {
  // 1. SweetAlert Helper Functions
  function showAlert(title, text, icon) {
    if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
      return Swal.fire({
        title: title || "Notice",
        text: text || "",
        icon: icon || "info",
        confirmButtonColor: "#00647a",
        customClass: {
          popup: "v3-swal-popup",
          confirmButton: "v3-swal-confirm-btn",
        },
      });
    } else {
      alert((title ? title + ": " : "") + text);
    }
  }

  function showToast(title, icon) {
    if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
      return Swal.fire({
        toast: true,
        position: "top-end",
        icon: icon || "success",
        title: title,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
      });
    } else {
      console.log(title);
    }
  }

  // 2. DOM Elements
  var form = document.getElementById("shortenForm");
  var urlInput = document.getElementById("longUrlInput");
  var aliasInput = document.getElementById("customAliasInput");
  var aliasBox = document.getElementById("aliasBox");
  var aliasToggleBtn = document.getElementById("aliasToggleBtn");
  var pasteBtn = document.getElementById("pasteBtn");
  var submitBtn = document.getElementById("submitBtn");
  var submitBtnText = document.getElementById("submitBtnText");
  var submitSpinner = document.getElementById("submitSpinner");

  var resultCard = document.getElementById("shortenerResult");
  var resultUrlText = document.getElementById("resultUrlText");
  var copyBtn = document.getElementById("copyBtn");
  var copyBtnText = document.getElementById("copyBtnText");
  var visitLinkBtn = document.getElementById("visitLinkBtn");
  var qrToggleBtn = document.getElementById("qrToggleBtn");
  var qrModal = document.getElementById("qrModal");
  var qrImg = document.getElementById("qrImg");
  var downloadQrBtn = document.getElementById("downloadQrBtn");

  var currentShortUrl = "";

  // 3. Custom Alias Toggle
  if (aliasToggleBtn && aliasBox) {
    aliasToggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      aliasBox.classList.toggle("open");
      if (aliasBox.classList.contains("open") && aliasInput) {
        aliasInput.focus();
      }
    });
  }

  // 4. Paste from Clipboard
  if (pasteBtn && urlInput) {
    pasteBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (navigator.clipboard && navigator.clipboard.readText) {
        navigator.clipboard
          .readText()
          .then(function (text) {
            if (text) {
              urlInput.value = text.trim();
              urlInput.focus();
              showToast("Pasted from clipboard", "info");
            }
          })
          .catch(function () {
            urlInput.focus();
          });
      } else {
        urlInput.focus();
      }
    });
  }

  // 5. Form Submit (Link Shortening via REST API)
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      var rawUrl = urlInput ? urlInput.value.trim() : "";
      var alias = aliasInput ? aliasInput.value.trim() : "";

      if (!rawUrl) {
        showAlert("Missing URL", "Please enter a destination URL to shorten.", "warning");
        if (urlInput) urlInput.focus();
        return;
      }

      // Prepend protocol if missing
      var longUrl = rawUrl;
      if (!/^https?:\/\//i.test(longUrl)) {
        longUrl = "https://" + longUrl;
        if (urlInput) urlInput.value = longUrl;
      }

      // UI Loading state
      if (submitBtn) submitBtn.disabled = true;
      if (submitBtnText) submitBtnText.textContent = "Shortening…";
      if (submitSpinner) submitSpinner.classList.add("active");

      var payload = {
        url: longUrl,
        long_url: longUrl,
        alias: alias,
      };

      // Call API endpoint with trailing slash to prevent .htaccess 302 redirection
      fetch("/api/v3/shortener/shorten/", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(payload),
      })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, status: res.status, data: data };
          });
        })
        .then(function (resp) {
          if (resp.ok && resp.data && resp.data.status === "success") {
            currentShortUrl = resp.data.short_url;

            // Render result in the UI
            if (resultUrlText) resultUrlText.textContent = currentShortUrl;
            if (visitLinkBtn) visitLinkBtn.setAttribute("href", currentShortUrl);

            // Generate QR Code URL
            var qrApiUrl =
              "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data=" +
              encodeURIComponent(currentShortUrl);
            if (qrImg) qrImg.src = qrApiUrl;
            if (downloadQrBtn) downloadQrBtn.setAttribute("href", qrApiUrl);

            if (resultCard) {
              resultCard.classList.add("active");
              resultCard.scrollIntoView({ behavior: "smooth", block: "nearest" });
            }

            // Reset copy button text
            if (copyBtnText) copyBtnText.textContent = "Copy";

            // SweetAlert confirmation
            if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
              Swal.fire({
                icon: "success",
                title: "Short Link Created!",
                html: '<div style="margin-top:8px;font-family:monospace;font-size:16px;color:#00647a;font-weight:700;">' + currentShortUrl + "</div>",
                confirmButtonColor: "#00647a",
                confirmButtonText: "Awesome",
                timer: 3500,
                timerProgressBar: true,
              });
            }
          } else {
            var msg =
              (resp.data && resp.data.msg) ||
              "Could not shorten link. Please verify the URL and try again.";
            showAlert("Could Not Shorten", msg, "error");
          }
        })
        .catch(function (err) {
          showAlert("Network Error", "Unable to connect to the link shortener service. Please check your network and try again.", "error");
        })
        .finally(function () {
          if (submitBtn) submitBtn.disabled = false;
          if (submitBtnText) submitBtnText.textContent = "Shorten URL";
          if (submitSpinner) submitSpinner.classList.remove("active");
        });
    });
  }

  // 6. Copy Short URL to Clipboard with SweetAlert Toast
  if (copyBtn) {
    copyBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (!currentShortUrl && resultUrlText) {
        currentShortUrl = resultUrlText.textContent.trim();
      }
      if (!currentShortUrl) return;

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
          .writeText(currentShortUrl)
          .then(function () {
            if (copyBtnText) copyBtnText.textContent = "Copied!";
            showToast("Short link copied to clipboard!", "success");
            setTimeout(function () {
              if (copyBtnText) copyBtnText.textContent = "Copy";
            }, 2500);
          })
          .catch(fallbackCopy);
      } else {
        fallbackCopy();
      }

      function fallbackCopy() {
        var tempInput = document.createElement("input");
        tempInput.value = currentShortUrl;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        if (copyBtnText) copyBtnText.textContent = "Copied!";
        showToast("Short link copied to clipboard!", "success");
        setTimeout(function () {
          if (copyBtnText) copyBtnText.textContent = "Copy";
        }, 2500);
      }
    });
  }

  // 7. QR Code Toggle
  if (qrToggleBtn && qrModal) {
    qrToggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      qrModal.classList.toggle("open");
    });
  }

  // 8. FAQ Accordion
  var faqHeaders = document.querySelectorAll(".faq-header");
  faqHeaders.forEach(function (header) {
    header.addEventListener("click", function () {
      var item = header.closest(".faq-item");
      if (!item) return;

      var wasActive = item.classList.contains("active");
      document.querySelectorAll(".faq-item").forEach(function (other) {
        other.classList.remove("active");
      });

      if (!wasActive) {
        item.classList.add("active");
      }
    });
  });

  // 9. Mobile Navigation Toggle
  var navToggle = document.getElementById("navToggle");
  var navMenu = document.getElementById("navMenu");
  if (navToggle && navMenu) {
    navToggle.addEventListener("click", function () {
      navMenu.classList.toggle("open");
    });
  }
});
