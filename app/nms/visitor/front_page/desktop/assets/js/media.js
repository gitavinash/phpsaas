/**
 * Media Page Interactions
 * Handles copy-to-clipboard, share action, and responsive media viewer controls.
 */
document.addEventListener('DOMContentLoaded', function () {
  // Copy Link Button
  var copyButtons = document.querySelectorAll('.media-copy-btn');
  copyButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = btn.getAttribute('data-url') || window.location.href;
      var originalHtml = btn.innerHTML;

      var updateState = function () {
        btn.classList.add('copied');
        btn.innerHTML = '<i class="ph ph-check" aria-hidden="true"></i> <span>Copied!</span>';
        setTimeout(function () {
          btn.classList.remove('copied');
          btn.innerHTML = originalHtml;
        }, 2200);
      };

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(updateState).catch(function () {
          fallbackCopy(url, updateState);
        });
      } else {
        fallbackCopy(url, updateState);
      }
    });
  });

  // Native Share Button
  var shareButtons = document.querySelectorAll('.media-share-btn');
  shareButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var title = btn.getAttribute('data-title') || document.title;
      var url = btn.getAttribute('data-url') || window.location.href;

      if (navigator.share) {
        navigator.share({
          title: title,
          url: url
        }).catch(function (err) {
          if (err.name !== 'AbortError') {
            console.warn('Share could not be completed:', err);
          }
        });
      } else if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function () {
          btn.classList.add('copied');
          var origHtml = btn.innerHTML;
          btn.innerHTML = '<i class="ph ph-check" aria-hidden="true"></i> <span>Link Copied!</span>';
          setTimeout(function () {
            btn.classList.remove('copied');
            btn.innerHTML = origHtml;
          }, 2200);
        });
      }
    });
  });

  function fallbackCopy(text, callback) {
    try {
      var textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.top = '0';
      textArea.style.left = '0';
      textArea.style.position = 'fixed';
      textArea.style.opacity = '0';
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      var successful = document.execCommand('copy');
      document.body.removeChild(textArea);
      if (successful && typeof callback === 'function') {
        callback();
      }
    } catch (err) {
      console.warn('Fallback copy error:', err);
    }
  }
});
