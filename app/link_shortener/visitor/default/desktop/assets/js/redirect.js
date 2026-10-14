(function () {
  var targetMeta = document.querySelector('meta[data-redirect-url]');
  if (!targetMeta) return;

  var targetUrl = targetMeta.getAttribute('data-redirect-url');
  var delay = parseInt(targetMeta.getAttribute('data-delay') || '1000', 10);

  if (!targetUrl) return;

  setTimeout(function () {
    try {
      window.location.replace(targetUrl);
    } catch (e) {
      window.location.href = targetUrl;
    }
  }, delay);
})();
