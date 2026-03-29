(function () {
  if (!document.getElementById("rte-swal-zindex-style")) {
    var _s = document.createElement("style");
    _s.id = "rte-swal-zindex-style";
    // Always render SweetAlert above the fullscreen editor (z-index 9999)
    _s.textContent = ".swal2-container{z-index:100000!important;}";
    (document.head || document.documentElement).appendChild(_s);
  }
})();

// Thin alias so all dialogs use a consistent call site.
function rteSwal(opts) {
  var safeOpts = Object.assign({}, opts || {});

  // Older SweetAlert2 builds use `type` and warn on unknown `icon`.
  if (safeOpts.icon && !safeOpts.type) {
    safeOpts.type = safeOpts.icon;
  }

  if (typeof safeOpts.icon !== "undefined") {
    delete safeOpts.icon;
  }

  return Swal.fire(safeOpts);
}

function popUpMessage(msgPostion, msgTxt, msgType) {
  if (msgType == 200) {
    msgType = "success";
  }

  if (typeof msgType === "undefined" || msgType === null || msgType === "") {
    msgType = "info";
  }

  rteSwal({
    position: msgPostion,
    type: msgType,
    icon: msgType,
    title: msgTxt,
    showConfirmButton: false,
    timer: 1500,
  });
}

(function ($) {
  if (typeof $.fn.rte === "undefined") {
    var defaults = {
      max_height: 350,
    };

    $.fn.rte = function (options) {
      $.fn.rte.html = function (iframe) {
        return iframe.contentWindow.document.getElementsByTagName("body")[0]
          .innerHTML;
      };

      // build main options before element iteration
      var opts = $.extend(defaults, options);

      // iterate and construct the RTEs
      return this.each(function () {
        var textarea = $(this);
        var iframe;
        var element_id = textarea.attr("id");
        var pastePlainMode = true;
        var savedSelectionRange = null;
        var embedDebugEnabled = true;
        var selectedEditableElement = null;

        function sanitizeUrl(url) {
          if (!url) {
            return "";
          }

          var safeUrl = $.trim(url);
          if (safeUrl === "") {
            return "";
          }

          if (
            !/^[a-z][a-z0-9+.-]*:/i.test(safeUrl) &&
            safeUrl.indexOf("//") !== 0
          ) {
            safeUrl = "https://" + safeUrl;
          }

          if (/^javascript:/i.test(safeUrl)) {
            return "";
          }

          return safeUrl;
        }

        function extractFirstUrl(text) {
          var raw = $.trim(text || "");
          if (!raw) {
            return "";
          }

          var match = raw.match(/https?:\/\/[^\s"'<>]+/i);
          if (match && match[0]) {
            return match[0].replace(/[),.;]+$/, "");
          }

          return raw;
        }

        function escapeHtml(value) {
          return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#39;");
        }

        function showSwalValidation(msg) {
          if (typeof Swal.showValidationMessage === "function") {
            Swal.showValidationMessage(msg);
          } else if (typeof Swal.showValidationError === "function") {
            Swal.showValidationError(msg);
          }
        }

        function isSwalConfirmed(result) {
          if (typeof result === "undefined" || result === null) {
            return false;
          }

          if (typeof result.isConfirmed !== "undefined") {
            return !!result.isConfirmed;
          }

          if (typeof result.dismiss !== "undefined") {
            return !result.dismiss;
          }

          if (result === true) {
            return true;
          }

          if (typeof result.value !== "undefined") {
            return true;
          }

          return true;
        }

        function getSwalValue(result) {
          if (typeof result === "undefined" || result === null) {
            return null;
          }

          if (typeof result.value !== "undefined") {
            return result.value;
          }

          return result;
        }

        function logEmbedStep(step, payload) {
          if (!embedDebugEnabled) {
            return;
          }

          var logEntry = {
            time: new Date().toISOString(),
            editorId: element_id,
            step: step,
            payload: payload || null,
          };

          if (typeof window !== "undefined") {
            window.__RTE_EMBED_LOGS__ = window.__RTE_EMBED_LOGS__ || [];
            window.__RTE_EMBED_LOGS__.push(logEntry);
          }

          try {
            console.log("[RTE EMBED]", step, payload || "");
          } catch (e) {
            // no-op
          }
        }

        function insertHtmlAtCursor(html) {
          if (!iframe || !iframe.contentWindow) {
            logEmbedStep("insertHtmlAtCursor:no-iframe");
            return false;
          }

          try {
            logEmbedStep("insertHtmlAtCursor:start", {
              htmlLength: (html || "").length,
            });
            iframe.contentWindow.focus();
            restoreSelectionRange();

            var sel = iframe.contentWindow.getSelection();
            if (!sel) {
              logEmbedStep("insertHtmlAtCursor:no-selection");
              return false;
            }

            if (sel.rangeCount === 0) {
              var body = iframe.contentWindow.document.body;
              if (!body) {
                logEmbedStep("insertHtmlAtCursor:no-body");
                return false;
              }
              var endRange = iframe.contentWindow.document.createRange();
              endRange.selectNodeContents(body);
              endRange.collapse(false);
              sel.removeAllRanges();
              sel.addRange(endRange);
            }

            var range = sel.getRangeAt(0);
            range.deleteContents();

            var container = iframe.contentWindow.document.createElement("div");
            container.innerHTML = html;

            var frag = iframe.contentWindow.document.createDocumentFragment();
            var node;
            var lastNode = null;

            while ((node = container.firstChild)) {
              lastNode = frag.appendChild(node);
            }

            range.insertNode(frag);

            if (lastNode) {
              range = range.cloneRange();
              range.setStartAfter(lastNode);
              range.collapse(true);
              sel.removeAllRanges();
              sel.addRange(range);
              savedSelectionRange = range.cloneRange();
            }

            logEmbedStep("insertHtmlAtCursor:success");
            return true;
          } catch (e) {
            logEmbedStep("insertHtmlAtCursor:error", {
              error: e && e.message ? e.message : String(e),
            });
            return false;
          }
        }

        function appendHtmlToEditorEnd(html) {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return false;
          }

          try {
            var doc = iframe.contentWindow.document;
            var body = doc.body;
            if (!body) {
              return false;
            }

            var container = doc.createElement("div");
            container.innerHTML = html;

            while (container.firstChild) {
              body.appendChild(container.firstChild);
            }

            return true;
          } catch (e) {
            return false;
          }
        }

        function ensureEditorBodyReady() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            logEmbedStep("ensureEditorBodyReady:no-doc");
            return null;
          }

          var doc = iframe.contentWindow.document;
          if (doc.body) {
            logEmbedStep("ensureEditorBodyReady:ready");
            return doc;
          }

          try {
            doc.open();
            doc.write("<!doctype html><html><head></head><body></body></html>");
            doc.close();
            doc.designMode = "on";
            logEmbedStep("ensureEditorBodyReady:created-body");
          } catch (e) {
            logEmbedStep("ensureEditorBodyReady:error", {
              error: e && e.message ? e.message : String(e),
            });
            return null;
          }

          return doc.body ? doc : null;
        }

        function insertEmbedHtmlReliable(html) {
          logEmbedStep("insertEmbedHtmlReliable:start", {
            htmlLength: (html || "").length,
          });
          var doc = ensureEditorBodyReady();
          if (!doc || !doc.body) {
            logEmbedStep("insertEmbedHtmlReliable:body-unavailable");
            return false;
          }

          var before = doc.body.innerHTML;

          if (insertHtmlAtCursor(html)) {
            logEmbedStep("insertEmbedHtmlReliable:cursor-success");
            return true;
          }

          try {
            iframe.contentWindow.focus();
            doc.execCommand("insertHTML", false, html);
            logEmbedStep("insertEmbedHtmlReliable:execCommand-attempted");
          } catch (e) {
            logEmbedStep("insertEmbedHtmlReliable:execCommand-error", {
              error: e && e.message ? e.message : String(e),
            });
            // ignore and continue to hard append fallback
          }

          if (doc.body.innerHTML !== before) {
            logEmbedStep("insertEmbedHtmlReliable:execCommand-success");
            return true;
          }

          try {
            doc.body.insertAdjacentHTML(
              "beforeend",
              "<p><br></p>" + html + "<p><br></p>",
            );
            logEmbedStep(
              "insertEmbedHtmlReliable:insertAdjacentHTML-attempted",
            );
          } catch (e) {
            logEmbedStep("insertEmbedHtmlReliable:insertAdjacentHTML-error", {
              error: e && e.message ? e.message : String(e),
            });
            return appendHtmlToEditorEnd(html);
          }

          var changed = doc.body.innerHTML !== before;
          logEmbedStep("insertEmbedHtmlReliable:final", { changed: changed });
          return changed;
        }

        function saveSelectionRange() {
          if (!iframe || !iframe.contentWindow) {
            return;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              savedSelectionRange = sel.getRangeAt(0).cloneRange();
            }
          } catch (e) {
            savedSelectionRange = null;
          }
        }

        function restoreSelectionRange() {
          if (!iframe || !iframe.contentWindow || !savedSelectionRange) {
            return;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (!sel) {
              return;
            }
            sel.removeAllRanges();
            sel.addRange(savedSelectionRange);
          } catch (e) {
            savedSelectionRange = null;
          }
        }

        function getCurrentSelectionText() {
          if (!iframe || !iframe.contentWindow) {
            return "";
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              return $.trim(sel.toString() || "");
            }
          } catch (e) {
            return "";
          }

          return "";
        }

        function updateSelectionLinkAttributes(url, newTab) {
          if (!iframe || !iframe.contentWindow) {
            return;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (!sel) {
              return;
            }

            var anchor = null;
            if (sel.anchorNode) {
              anchor = $(normalizeElementNode(sel.anchorNode))
                .closest("a")
                .get(0);
            }

            if (!anchor && sel.focusNode) {
              anchor = $(normalizeElementNode(sel.focusNode))
                .closest("a")
                .get(0);
            }

            if (!anchor && sel.rangeCount > 0) {
              var rangeNode = normalizeElementNode(
                sel.getRangeAt(0).commonAncestorContainer,
              );
              if (rangeNode) {
                anchor = $(rangeNode).closest("a").get(0);
              }
            }

            if (!anchor) {
              return;
            }

            anchor.setAttribute("href", url);
            if (newTab) {
              anchor.setAttribute("target", "_blank");
              anchor.setAttribute("rel", "noopener noreferrer");
            } else {
              anchor.removeAttribute("target");
              anchor.removeAttribute("rel");
            }
          } catch (e) {
            // no-op
          }
        }

        function getSelectionToolsBox() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return $();
          }

          return $(
            ".rte-selection-tools-floating",
            iframe.contentWindow.document,
          );
        }

        function isInsideSelectionTools(node) {
          if (!node) {
            return false;
          }

          return $(node).closest(".rte-selection-tools-floating").length > 0;
        }

        function ensureFloatingSelectionTools() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return $();
          }

          var doc = iframe.contentWindow.document;
          var tools = getSelectionToolsBox();

          if (!doc.getElementById("rte-selection-tools-style")) {
            var style = doc.createElement("style");
            style.id = "rte-selection-tools-style";
            style.textContent =
              ".rte-selection-tools-floating{display:none;position:absolute;z-index:9999;align-items:center;gap:8px;padding:7px 9px;background:#0f172a;color:#f8fafc;border-radius:10px;box-shadow:0 12px 26px rgba(15,23,42,.35);font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-tools-label{opacity:.85;font-weight:600;padding-right:4px;border-right:1px solid rgba(255,255,255,.18);margin-right:2px;}" +
              ".rte-selection-tools-floating .rte-selection-action{border:0;background:transparent;color:inherit;padding:5px 8px;cursor:pointer;border-radius:6px;font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-selection-action:hover{background:rgba(255,255,255,.14);}" +
              ".rte-selection-tools-floating .rte-selection-action.active{background:#2563eb;color:#fff;}" +
              ".rte-selection-tools-floating .rte-selection-heading,.rte-selection-tools-floating .rte-selection-media-size{display:none;border:1px solid rgba(255,255,255,.2);background:#1e293b;color:#fff;padding:4px 6px;border-radius:6px;font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-selection-caption,.rte-selection-tools-floating .rte-selection-remove-caption{display:none;border:0;color:#fff;padding:5px 8px;cursor:pointer;border-radius:6px;font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-selection-image-alt{display:none;border:0;background:#334155;color:#fff;padding:5px 8px;cursor:pointer;border-radius:6px;font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-selection-caption{background:#2563eb;}" +
              ".rte-selection-tools-floating .rte-selection-remove-caption{background:#b91c1c;}" +
              ".rte-selection-tools-floating .rte-selection-image-alt:hover{filter:brightness(1.08);}" +
              ".rte-selection-tools-floating .rte-selection-caption:hover,.rte-selection-tools-floating .rte-selection-remove-caption:hover{filter:brightness(1.08);}";
            (doc.head || doc.documentElement).appendChild(style);
          }

          if (tools.length) {
            return tools;
          }

          tools = $(
            '<div class="rte-selection-tools-floating">' +
              '<select class="rte-selection-media-size" title="media size">' +
              '<option value="">Size</option>' +
              '<option value="50">Small (50%)</option>' +
              '<option value="75">Medium (75%)</option>' +
              '<option value="100">Full (100%)</option>' +
              '<option value="auto">Auto</option>' +
              "</select>" +
              '<button type="button" class="rte-selection-image-alt" title="set media alt/title text">Alt</button>' +
              '<button type="button" class="rte-selection-caption" title="add or update media caption">Caption</button>' +
              '<button type="button" class="rte-selection-remove-caption" title="remove media caption">Remove</button>' +
              "</div>",
          );

          $(doc.body || doc.documentElement).append(tools);

          tools.on("mousedown", function (event) {
            if ($(event.target).is("select, option, input")) {
              return;
            }
            event.preventDefault();
          });

          $(".rte-selection-caption", tools).on("click", function () {
            openMediaCaptionDialog();
          });

          $(".rte-selection-remove-caption", tools).on("click", function () {
            removeMediaCaption();
          });

          $(".rte-selection-media-size", tools).on("change", function () {
            var size = $(this).val();
            if (!size) {
              return;
            }
            applyMediaSizeToSelection(size);
          });

          $(".rte-selection-image-alt", tools).on("click", function () {
            openImageAltDialog();
          });

          return tools;
        }

        function normalizeElementNode(node) {
          if (!node) {
            return null;
          }

          if (node.nodeType === 3) {
            return node.parentNode;
          }

          return node;
        }

        function findClosestEditableElement(node) {
          var element = normalizeElementNode(node);
          if (!element || !element.tagName) {
            return null;
          }

          if (isInsideSelectionTools(element)) {
            return null;
          }

          if (/^(IMG|IFRAME|VIDEO|FIGURE|BLOCKQUOTE)$/i.test(element.tagName)) {
            return element;
          }

          var closest = $(element).closest(
            "figure, .rte-embed, .rte-embed-wrap, .rte-embed-inner, p, h1, h2, h3, h4, h5, h6, blockquote, blockquote.instagram-media, blockquote.tiktok-embed, li, div, body, iframe, img, video",
          );

          return closest.length ? closest.get(0) : null;
        }

        function getSelectionState() {
          var result = {
            hasRange: false,
            collapsed: true,
            node: null,
          };

          if (!iframe || !iframe.contentWindow) {
            return result;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              var range = sel.getRangeAt(0);
              result.hasRange = true;
              result.collapsed = !!range.collapsed;
              result.node = range.commonAncestorContainer || null;
            }
          } catch (e) {
            // keep defaults
          }

          return result;
        }

        function getSelectionRangeRect() {
          if (!iframe || !iframe.contentWindow) {
            return null;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            if (!sel || sel.rangeCount === 0) {
              return null;
            }

            var range = sel.getRangeAt(0);
            if (range.collapsed) {
              return null;
            }

            var rect = range.getBoundingClientRect();
            if (rect && (rect.width > 0 || rect.height > 0)) {
              return rect;
            }

            var clientRects = range.getClientRects();
            if (clientRects && clientRects.length > 0) {
              return clientRects[0];
            }
          } catch (e) {
            return null;
          }

          return null;
        }

        function hasActiveSelectionRange() {
          if (!iframe || !iframe.contentWindow) {
            return false;
          }

          try {
            var sel = iframe.contentWindow.getSelection();
            return !!(
              sel &&
              sel.rangeCount > 0 &&
              !sel.getRangeAt(0).collapsed
            );
          } catch (e) {
            return false;
          }
        }

        function getSelectedImageElement() {
          var element = normalizeElementNode(selectedEditableElement);
          if (!element || !element.tagName) {
            return null;
          }

          if (element.tagName === "IMG") {
            return element;
          }

          if (element.tagName === "FIGURE") {
            return element.querySelector("img");
          }

          return element.querySelector ? element.querySelector("img") : null;
        }

        function getSelectedMediaElement() {
          var element = normalizeElementNode(selectedEditableElement);
          if (!element || !element.tagName) {
            return null;
          }

          if (/^(IMG|IFRAME|VIDEO|FIGURE)$/i.test(element.tagName)) {
            return element;
          }

          if (
            element.tagName === "BLOCKQUOTE" &&
            ($(element).hasClass("instagram-media") ||
              $(element).hasClass("tiktok-embed"))
          ) {
            return element;
          }

          if (
            $(element).hasClass("rte-embed") ||
            $(element).hasClass("rte-embed-wrap") ||
            $(element).hasClass("rte-embed-inner")
          ) {
            return element;
          }

          return element.querySelector
            ? element.querySelector(
                "img, iframe, video, .rte-embed, .rte-embed-wrap, .rte-embed-inner, blockquote.instagram-media, blockquote.tiktok-embed",
              )
            : null;
        }

        function isMediaCandidateElement(element) {
          if (!element || !element.tagName) {
            return false;
          }

          if (
            /^(IMG|IFRAME|VIDEO|FIGURE)$/i.test(element.tagName) ||
            (element.tagName === "BLOCKQUOTE" &&
              ($(element).hasClass("instagram-media") ||
                $(element).hasClass("tiktok-embed"))) ||
            $(element).hasClass("rte-embed") ||
            $(element).hasClass("rte-embed-wrap") ||
            $(element).hasClass("rte-embed-inner")
          ) {
            return true;
          }

          return false;
        }

        function isBlockEditableElement(element) {
          if (!element || !element.tagName) {
            return false;
          }

          return (
            /^(P|H1|H2|H3|H4|H5|H6|BLOCKQUOTE|LI|DIV|BODY|FIGURE|IMG|IFRAME|VIDEO)$/i.test(
              element.tagName,
            ) || $(element).hasClass("rte-embed")
          );
        }

        function hideSelectionTools() {
          var tools = getSelectionToolsBox();
          tools.hide();
          $(".rte-selection-caption", tools).hide();
          $(".rte-selection-remove-caption", tools).hide();
          $(".rte-selection-image-alt", tools).hide();
          $(".rte-selection-heading", tools).hide();
          $(".rte-selection-media-size", tools).hide();
          $(".rte-selection-action", tools).removeClass("active");
        }

        function getSelectionContentType() {
          var element = normalizeElementNode(selectedEditableElement);
          if (!element || !element.tagName) {
            return "none";
          }

          if (getSelectedImageElement()) {
            return "image";
          }

          if (
            /^(IFRAME|VIDEO|FIGURE)$/i.test(element.tagName) ||
            (element.tagName === "BLOCKQUOTE" &&
              ($(element).hasClass("instagram-media") ||
                $(element).hasClass("tiktok-embed"))) ||
            $(element).hasClass("rte-embed") ||
            $(element).hasClass("rte-embed-wrap") ||
            $(element).hasClass("rte-embed-inner")
          ) {
            return "embed";
          }

          return "text";
        }

        function getComputedAlignment(element) {
          if (!element || !iframe || !iframe.contentWindow) {
            return "left";
          }

          var textAlign = "";
          try {
            textAlign =
              (element.style && element.style.textAlign) ||
              iframe.contentWindow.getComputedStyle(element).textAlign ||
              "";
          } catch (e) {
            textAlign = "";
          }

          textAlign = (textAlign || "").toLowerCase();
          if (textAlign.indexOf("center") !== -1) {
            return "center";
          }
          if (textAlign.indexOf("right") !== -1) {
            return "right";
          }
          if (textAlign.indexOf("justify") !== -1) {
            return "justify";
          }

          return "left";
        }

        function getExistingMediaFigure(mediaElement) {
          if (!mediaElement) {
            return null;
          }

          return $(mediaElement).closest("figure").get(0);
        }

        function getTextContainerElement() {
          var element = normalizeElementNode(selectedEditableElement);
          if (!element) {
            return null;
          }

          if (
            /^(P|H1|H2|H3|H4|H5|H6|BLOCKQUOTE|LI|DIV)$/i.test(element.tagName)
          ) {
            return element;
          }

          var closest = $(element).closest(
            "p, h1, h2, h3, h4, h5, h6, blockquote, li, div",
          );
          return closest.length ? closest.get(0) : null;
        }

        function applyHeadingToSelection(tagName) {
          var textElement = getTextContainerElement();
          if (!textElement || !tagName) {
            if (tagName && hasActiveSelectionRange()) {
              formatText("formatBlock", String(tagName).toLowerCase());
              updateSelectionTools();
            }
            return;
          }

          var normalizedTag = String(tagName).toLowerCase();
          var doc = textElement.ownerDocument;

          if (!doc || !textElement.parentNode) {
            return;
          }

          if (textElement.tagName.toLowerCase() === normalizedTag) {
            applyHeadingStyle(nextOrCurrentElement(textElement), normalizedTag);
            updateSelectionTools(textElement);
            return;
          }

          var next = doc.createElement(normalizedTag);
          next.innerHTML = textElement.innerHTML;
          if (textElement.getAttribute("style")) {
            next.setAttribute("style", textElement.getAttribute("style"));
          }

          applyHeadingStyle(next, normalizedTag);

          textElement.parentNode.replaceChild(next, textElement);
          selectedEditableElement = next;
          updateSelectionTools(next);
        }

        function nextOrCurrentElement(element) {
          return element && element.nodeType === 1 ? element : null;
        }

        function applyHeadingStyle(element, tagName) {
          if (!element || !element.style) {
            return;
          }

          var styles = {
            p: { fontSize: "16px", fontWeight: "400", lineHeight: "1.6" },
            h1: { fontSize: "36px", fontWeight: "700", lineHeight: "1.2" },
            h2: { fontSize: "30px", fontWeight: "700", lineHeight: "1.25" },
            h3: { fontSize: "24px", fontWeight: "700", lineHeight: "1.3" },
            h4: { fontSize: "20px", fontWeight: "700", lineHeight: "1.35" },
            h5: { fontSize: "18px", fontWeight: "700", lineHeight: "1.4" },
            h6: { fontSize: "16px", fontWeight: "700", lineHeight: "1.45" },
            blockquote: {
              fontSize: "18px",
              fontWeight: "500",
              lineHeight: "1.6",
              fontStyle: "italic",
            },
          };

          var key = (tagName || "").toLowerCase();
          var selected = styles[key] || styles.p;

          element.style.fontSize = selected.fontSize;
          element.style.fontWeight = selected.fontWeight;
          element.style.lineHeight = selected.lineHeight;
          if (selected.fontStyle) {
            element.style.fontStyle = selected.fontStyle;
          } else {
            element.style.fontStyle = "";
          }
          element.style.margin = "0 0 12px";
        }

        function getPrimaryMediaNode() {
          var mediaElement = getSelectedMediaElement();
          if (!mediaElement) {
            return null;
          }

          if (/^(IMG|IFRAME|VIDEO)$/i.test(mediaElement.tagName)) {
            return mediaElement;
          }

          if (mediaElement.querySelector) {
            return (
              mediaElement.querySelector("img, iframe, video") || mediaElement
            );
          }

          return mediaElement;
        }

        function applyMediaSizeToSelection(sizeValue) {
          var mediaNode = getPrimaryMediaNode();
          if (!mediaNode) {
            return;
          }

          if (sizeValue === "auto") {
            mediaNode.style.width = "";
            mediaNode.style.maxWidth = "100%";
            if (/^(IMG|VIDEO)$/i.test(mediaNode.tagName)) {
              mediaNode.style.height = "auto";
            }
          } else {
            mediaNode.style.width = sizeValue + "%";
            mediaNode.style.maxWidth = "100%";
            if (/^(IMG|VIDEO)$/i.test(mediaNode.tagName)) {
              mediaNode.style.height = "auto";
            }
          }

          updateSelectionTools(selectedEditableElement || mediaNode);
        }

        function openImageAltDialog() {
          var mediaNode = getPrimaryMediaNode();
          if (!mediaNode) {
            return;
          }

          var isImage = /^(IMG)$/i.test(mediaNode.tagName);
          var isFrameOrVideo = /^(IFRAME|VIDEO)$/i.test(mediaNode.tagName);
          var attrName = isImage ? "alt" : "title";

          if (!isImage && !isFrameOrVideo) {
            rteSwal({
              icon: "info",
              title: "Alt/Title not supported",
              text: "Select an image, iframe, or video to set accessibility text.",
            });
            return;
          }

          rteSwal({
            title: isImage ? "Image Alt Text" : "Media Title Text",
            input: "text",
            inputValue: mediaNode.getAttribute(attrName) || "",
            inputPlaceholder: isImage
              ? "Describe this image for accessibility"
              : "Describe this media for accessibility",
            showCancelButton: true,
            confirmButtonText: "Save",
          }).then(function (result) {
            if (!isSwalConfirmed(result)) {
              return;
            }

            var value = $.trim(getSwalValue(result) || "");
            mediaNode.setAttribute(attrName, value);
          });
        }

        function updateSelectionTools(targetNode) {
          // Disable all floating selection tool popups
          hideSelectionTools();
          return;
        }

        function ensureMediaFigure(mediaElement) {
          if (!mediaElement || !mediaElement.ownerDocument) {
            return null;
          }

          var figure = $(mediaElement).closest("figure").get(0);
          if (figure) {
            return figure;
          }

          var doc = mediaElement.ownerDocument;
          figure = doc.createElement("figure");
          figure.className = "rte-media-figure";
          figure.style.margin = "10px 0";

          var parent = mediaElement.parentNode;
          if (
            parent &&
            /^(P|DIV)$/i.test(parent.tagName) &&
            $(parent)
              .contents()
              .filter(function () {
                return this.nodeType === 1 || $.trim(this.textContent) !== "";
              }).length === 1
          ) {
            parent.parentNode.insertBefore(figure, parent);
            figure.appendChild(mediaElement);
            parent.parentNode.removeChild(parent);
          } else if (parent) {
            parent.insertBefore(figure, mediaElement);
            figure.appendChild(mediaElement);
          }

          return figure;
        }

        function applyAlignmentToSelection(alignment) {
          if (!selectedEditableElement) {
            updateSelectionTools();
          }

          var element = selectedEditableElement;
          var alignmentCommandMap = {
            left: "justifyLeft",
            center: "justifyCenter",
            right: "justifyRight",
            justify: "justifyFull",
          };

          if (!element) {
            if (hasActiveSelectionRange() && alignmentCommandMap[alignment]) {
              formatText(alignmentCommandMap[alignment]);
              updateSelectionTools();
            }
            return;
          }

          var mediaElement = getSelectedMediaElement();
          if (mediaElement) {
            var wrapper =
              ensureMediaFigure(mediaElement) || mediaElement.parentNode;
            if (mediaElement.style) {
              mediaElement.style.display = "inline-block";
              mediaElement.style.float = "none";
              mediaElement.style.marginLeft = "0";
              mediaElement.style.marginRight = "0";
              mediaElement.style.maxWidth = "100%";
            }

            if (wrapper && wrapper.style) {
              wrapper.style.textAlign =
                alignment === "justify" ? "left" : alignment;
            }

            selectedEditableElement = wrapper || mediaElement;
            updateSelectionTools(selectedEditableElement);
            return;
          }

          if (
            /^(BODY|DIV)$/i.test(element.tagName) &&
            hasActiveSelectionRange() &&
            alignmentCommandMap[alignment]
          ) {
            formatText(alignmentCommandMap[alignment]);
            updateSelectionTools();
            return;
          }

          if (element.style) {
            element.style.textAlign = alignment;
          }
          updateSelectionTools(element);
        }

        function openMediaCaptionDialog() {
          var mediaElement = getSelectedMediaElement();
          if (!mediaElement) {
            return;
          }

          var figure = ensureMediaFigure(mediaElement);
          var figcaption = figure ? figure.querySelector("figcaption") : null;
          var currentCaption = figcaption ? $.trim($(figcaption).text()) : "";

          rteSwal({
            title: "Media Caption",
            input: "text",
            inputValue: currentCaption,
            inputPlaceholder: "Write caption for selected media",
            showCancelButton: true,
            confirmButtonText: "Save Caption",
          }).then(function (result) {
            if (!isSwalConfirmed(result)) {
              return;
            }

            var captionValue = $.trim(getSwalValue(result) || "");
            var activeFigure = ensureMediaFigure(mediaElement);
            var activeCaption = activeFigure.querySelector("figcaption");

            if (!captionValue) {
              if (activeCaption) {
                activeCaption.parentNode.removeChild(activeCaption);
              }
              return;
            }

            if (!activeCaption) {
              activeCaption =
                mediaElement.ownerDocument.createElement("figcaption");
              activeFigure.appendChild(activeCaption);
            }

            activeCaption.textContent = captionValue;
            activeCaption.style.fontSize = "13px";
            activeCaption.style.color = "#666";
            activeCaption.style.marginTop = "6px";
            updateSelectionTools(activeFigure);
          });
        }

        function removeMediaCaption() {
          var mediaElement = getSelectedMediaElement();
          if (!mediaElement) {
            return;
          }

          var figure = getExistingMediaFigure(mediaElement);
          if (!figure) {
            return;
          }

          var figcaption = figure.querySelector("figcaption");
          if (figcaption) {
            figcaption.parentNode.removeChild(figcaption);
          }

          updateSelectionTools(figure);
        }

        function bindSelectionTracker() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return;
          }

          var scheduleSelectionRefresh = function (target) {
            saveSelectionRange();

            if (typeof window !== "undefined" && window.requestAnimationFrame) {
              window.requestAnimationFrame(function () {
                updateSelectionTools(target);
              });
            } else {
              setTimeout(function () {
                updateSelectionTools(target);
              }, 0);
            }
          };

          // Direct media element handler – fires before any selection-state confusion.
          // When the user clicks an image/figure/embed, immediately lock
          // selectedEditableElement to that element and schedule the toolbar update.
          var mediaClickSelector =
            "img, figure, video, .rte-embed-wrap, .rte-embed-inner, .rte-embed, blockquote.instagram-media, blockquote.tiktok-embed";

          $(iframe.contentWindow.document)
            .off("mousedown.rteMedia")
            .on("mousedown.rteMedia", mediaClickSelector, function () {
              var mediaEl = this;
              if (isInsideSelectionTools(mediaEl)) {
                return;
              }
              selectedEditableElement = mediaEl;
              window.requestAnimationFrame(function () {
                updateSelectionTools(mediaEl);
              });
            });

          $(iframe.contentWindow.document)
            .off("mouseup.rteSelection keyup.rteSelection click.rteSelection")
            .on(
              "mouseup.rteSelection keyup.rteSelection click.rteSelection",
              function (event) {
                if (isInsideSelectionTools(event.target)) {
                  return;
                }
                scheduleSelectionRefresh(event.target);
              },
            );

          $(iframe.contentWindow.document)
            .off("selectionchange.rteSelection")
            .on("selectionchange.rteSelection", function () {
              scheduleSelectionRefresh();
            });

          $(iframe.contentWindow)
            .off("scroll.rteSelection resize.rteSelection")
            .on("scroll.rteSelection resize.rteSelection", function () {
              updateSelectionTools();
            });
        }

        function processEmbeddedContent() {
          if (!iframe || !iframe.contentWindow) {
            logEmbedStep("processEmbeddedContent:no-iframe");
            return;
          }

          try {
            logEmbedStep("processEmbeddedContent:start");
            var w = iframe.contentWindow;
            var d = w.document;

            if (
              w.twttr &&
              w.twttr.widgets &&
              typeof w.twttr.widgets.load === "function"
            ) {
              logEmbedStep("processEmbeddedContent:twitter-load");
              w.twttr.widgets.load(d.body || d.documentElement);
            }

            if (
              w.instgrm &&
              w.instgrm.Embeds &&
              typeof w.instgrm.Embeds.process === "function"
            ) {
              logEmbedStep("processEmbeddedContent:instagram-process");
              w.instgrm.Embeds.process();
            }

            if (typeof w.tiktokEmbedLoad === "function") {
              logEmbedStep("processEmbeddedContent:tiktok-load");
              w.tiktokEmbedLoad();
            }
          } catch (e) {
            logEmbedStep("processEmbeddedContent:error", {
              error: e && e.message ? e.message : String(e),
            });
            console.log(e);
          }
        }

        function ensureEmbedScript(scriptConfig) {
          if (
            !scriptConfig ||
            !scriptConfig.src ||
            !iframe ||
            !iframe.contentWindow
          ) {
            logEmbedStep("ensureEmbedScript:skip", {
              hasScriptConfig: !!scriptConfig,
            });
            processEmbeddedContent();
            return;
          }

          try {
            logEmbedStep("ensureEmbedScript:start", { src: scriptConfig.src });
            var doc = iframe.contentWindow.document;
            var existing = doc.querySelector(
              'script[src="' + scriptConfig.src + '"]',
            );
            if (existing) {
              logEmbedStep("ensureEmbedScript:already-loaded", {
                src: scriptConfig.src,
              });
              processEmbeddedContent();
              return;
            }

            var script = doc.createElement("script");
            script.src = scriptConfig.src;
            script.async = true;

            if (scriptConfig.attrs) {
              Object.keys(scriptConfig.attrs).forEach(function (key) {
                script.setAttribute(key, scriptConfig.attrs[key]);
              });
            }

            script.onload = function () {
              logEmbedStep("ensureEmbedScript:loaded", {
                src: scriptConfig.src,
              });
              processEmbeddedContent();
            };

            (doc.body || doc.documentElement).appendChild(script);
          } catch (e) {
            logEmbedStep("ensureEmbedScript:error", {
              src: scriptConfig && scriptConfig.src ? scriptConfig.src : null,
              error: e && e.message ? e.message : String(e),
            });
            console.log(e);
          }
        }

        function buildEmbedFromInput(input) {
          var raw = $.trim(input || "");
          logEmbedStep("buildEmbedFromInput:start", { raw: raw });
          if (!raw) {
            logEmbedStep("buildEmbedFromInput:empty");
            return null;
          }

          if (/<iframe[\s\S]*?<\/iframe>/i.test(raw)) {
            logEmbedStep("buildEmbedFromInput:raw-iframe");
            return { html: raw };
          }

          var candidate = extractFirstUrl(raw);
          var url = sanitizeUrl(candidate);
          logEmbedStep("buildEmbedFromInput:url", {
            candidate: candidate,
            sanitized: url,
          });
          if (!url) {
            logEmbedStep("buildEmbedFromInput:invalid-url");
            return null;
          }

          if (/youtu\.be|youtube\.com/i.test(url)) {
            var id = "";
            var m = url.match(/[?&]v=([^&#]+)/i);
            if (m && m[1]) {
              id = m[1];
            }
            if (!id) {
              m = url.match(/youtu\.be\/([^?&#/]+)/i);
              if (m && m[1]) {
                id = m[1];
              }
            }
            if (!id) {
              m = url.match(/youtube\.com\/shorts\/([^?&#/]+)/i);
              if (m && m[1]) {
                id = m[1];
              }
            }
            if (!id) {
              m = url.match(/youtube\.com\/embed\/([^?&#/]+)/i);
              if (m && m[1]) {
                id = m[1];
              }
            }
            if (!id) {
              m = url.match(/youtube\.com\/live\/([^?&#/]+)/i);
              if (m && m[1]) {
                id = m[1];
              }
            }

            if (!id) {
              logEmbedStep("buildEmbedFromInput:youtube-id-missing", {
                url: url,
              });
              return null;
            }

            id = id.replace(/[^a-zA-Z0-9_-]/g, "");
            if (!id) {
              logEmbedStep("buildEmbedFromInput:youtube-id-invalid", {
                id: id,
              });
              return null;
            }

            logEmbedStep("buildEmbedFromInput:provider-youtube", { id: id });

            return {
              html:
                '<p class="rte-embed rte-embed-video" style="margin:10px 0;">' +
                '<iframe src="https://www.youtube.com/embed/' +
                id +
                '" width="560" height="315" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen style="max-width:100%;"></iframe>' +
                "</p>",
            };
          }

          if (/x\.com|twitter\.com/i.test(url)) {
            logEmbedStep("buildEmbedFromInput:provider-x", { url: url });
            var xId = "";
            var xMatch = url.match(/\/status\/(\d+)/i);
            if (xMatch && xMatch[1]) {
              xId = xMatch[1];
            }

            if (!xId) {
              logEmbedStep("buildEmbedFromInput:provider-x-id-missing", {
                url: url,
              });
              return null;
            }

            var xEmbedSrc =
              "https://platform.twitter.com/embed/Tweet.html?id=" +
              xId +
              "&dnt=true";
            logEmbedStep("buildEmbedFromInput:provider-x-src", {
              id: xId,
              src: xEmbedSrc,
            });
            return {
              html:
                '<div class="rte-embed rte-embed-x" style="margin:10px 0;">' +
                '<iframe src="' +
                xEmbedSrc +
                '" width="550" height="650" frameborder="0" scrolling="no" style="max-width:100%;border:none;overflow:hidden;" allowfullscreen="true" title="X post"></iframe>' +
                "</div>",
            };
          }

          if (/linkedin\.com\/.+/i.test(url)) {
            var liActivityId = "";
            var liMatch = url.match(/-(\d{10,})-[^/?#]+\/?$/i);
            if (liMatch && liMatch[1]) {
              liActivityId = liMatch[1];
            }
            if (!liActivityId) {
              liMatch = url.match(/activity[:\/-](\d{10,})/i);
              if (liMatch && liMatch[1]) {
                liActivityId = liMatch[1];
              }
            }
            if (!liActivityId) {
              liMatch = url.match(/share[:\/-](\d{10,})/i);
              if (liMatch && liMatch[1]) {
                liActivityId = liMatch[1];
              }
            }
            if (!liActivityId) {
              liMatch = url.match(/urn:li:(?:activity|share):(\d{10,})/i);
              if (liMatch && liMatch[1]) {
                liActivityId = liMatch[1];
              }
            }

            logEmbedStep("buildEmbedFromInput:provider-linkedin", {
              url: url,
              activityId: liActivityId || null,
            });

            if (liActivityId) {
              var liActivitySrc =
                "https://www.linkedin.com/embed/feed/update/urn:li:activity:" +
                liActivityId;
              var liShareSrc =
                "https://www.linkedin.com/embed/feed/update/urn:li:share:" +
                liActivityId;

              logEmbedStep("buildEmbedFromInput:provider-linkedin-src", {
                activity: liActivitySrc,
                share: liShareSrc,
              });

              return {
                html:
                  '<div class="rte-embed rte-embed-linkedin" style="margin:10px 0;">' +
                  '<iframe src="' +
                  liShareSrc +
                  '" height="700" width="504" frameborder="0" allowfullscreen="" title="LinkedIn post (share)" style="max-width:100%;border:0;display:block;"></iframe>' +
                  '<iframe src="' +
                  liActivitySrc +
                  '" height="700" width="504" frameborder="0" allowfullscreen="" title="LinkedIn post (activity)" style="max-width:100%;border:0;display:block;margin-top:8px;"></iframe>' +
                  "</div>",
              };
            }

            // Keep a hard fallback so something is inserted when ID extraction fails.
            return {
              html:
                '<div class="rte-embed rte-embed-linkedin" style="margin:10px 0;">' +
                '<iframe src="' +
                url +
                '" height="700" width="504" frameborder="0" allowfullscreen="" title="LinkedIn post" style="max-width:100%;border:0;display:block;"></iframe>' +
                "</div>",
            };
          }

          if (/instagram\.com\/(p|reel|tv)\//i.test(url)) {
            logEmbedStep("buildEmbedFromInput:provider-instagram", {
              url: url,
            });
            if (!/\/$/.test(url)) {
              url += "/";
            }
            return {
              html:
                '<blockquote class="instagram-media" data-instgrm-permalink="' +
                url +
                '" data-instgrm-version="14" style="margin:1px auto;max-width:540px;width:calc(100% - 2px);"></blockquote>',
              script: {
                src: "https://www.instagram.com/embed.js",
              },
            };
          }

          if (/facebook\.com\/.+/i.test(url)) {
            logEmbedStep("buildEmbedFromInput:provider-facebook", { url: url });

            if (/facebook\.com\/share\//i.test(url)) {
              logEmbedStep("buildEmbedFromInput:provider-facebook-share", {
                url: url,
              });
            }

            var fbSrc =
              "https://www.facebook.com/plugins/post.php?href=" +
              encodeURIComponent(url) +
              "&show_text=true&width=500";
            return {
              html:
                '<div class="rte-embed rte-embed-facebook" style="margin:10px 0;">' +
                '<iframe src="' +
                fbSrc +
                '" width="500" height="640" style="border:none;overflow:hidden;max-width:100%;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>' +
                "</div>",
            };
          }

          if (/tiktok\.com\/.+/i.test(url)) {
            logEmbedStep("buildEmbedFromInput:provider-tiktok", { url: url });
            return {
              html:
                '<blockquote class="tiktok-embed" cite="' +
                url +
                '" data-video-id="" style="max-width:605px;min-width:325px;"><section><a target="_blank" href="' +
                url +
                '"></a></section></blockquote>',
              script: {
                src: "https://www.tiktok.com/embed.js",
              },
            };
          }

          logEmbedStep("buildEmbedFromInput:provider-default-link", {
            url: url,
          });
          return {
            html:
              '<p><a href="' +
              url +
              '" target="_blank" rel="noopener noreferrer">' +
              url +
              "</a></p>",
          };
        }

        function openLinkDialog() {
          saveSelectionRange();
          var selectedText = $.trim(
            (savedSelectionRange && savedSelectionRange.toString()) ||
              getCurrentSelectionText() ||
              "",
          );

          rteSwal({
            title: "Insert Link",
            html:
              '<input id="rte-link-url" class="swal2-input" placeholder="Paste URL (https://...)" />' +
              '<input id="rte-link-text" class="swal2-input" placeholder="Optional link text" value="' +
              escapeHtml(selectedText) +
              '" />' +
              '<label for="rte-link-new-tab" style="display:flex;align-items:center;gap:8px;padding:0 1em 1em;"><input id="rte-link-new-tab" type="checkbox" checked /> Open in new tab</label>',
            showCancelButton: true,
            confirmButtonText: "Insert Link",
            focusConfirm: false,
            preConfirm: function () {
              var url = sanitizeUrl($("#rte-link-url").val());
              var text = $.trim($("#rte-link-text").val());
              var newTab = $("#rte-link-new-tab").is(":checked");

              if (!url) {
                showSwalValidation("Please enter a valid URL.");
                return false;
              }

              return { url: url, text: text, newTab: newTab };
            },
          }).then(function (result) {
            if (!isSwalConfirmed(result)) {
              return;
            }

            var data = getSwalValue(result);
            if (!data || !data.url) {
              return;
            }

            restoreSelectionRange();
            var liveSelectionText = getCurrentSelectionText();

            if (!data.text && liveSelectionText) {
              formatText("createlink", data.url);
              updateSelectionLinkAttributes(data.url, data.newTab);
              return;
            }

            var attrs = data.newTab
              ? ' target="_blank" rel="noopener noreferrer"'
              : "";
            var content = data.text || liveSelectionText || data.url;
            var html =
              '<a href="' +
              escapeHtml(data.url) +
              '"' +
              attrs +
              ">" +
              escapeHtml(content) +
              "</a>";

            if (!insertHtmlAtCursor(html)) {
              formatText("createlink", data.url);
            }
          });
        }

        function openEmbedDialog() {
          saveSelectionRange();
          logEmbedStep("openEmbedDialog:open");

          function updateEmbedLivePreview() {
            var size = $("#rte-embed-size").val() || "100";
            var align = $("#rte-embed-align").val() || "center";
            var caption = $.trim($("#rte-embed-caption").val() || "");

            var widthStyle =
              size === "auto"
                ? "width:auto;max-width:100%;"
                : "width:" + size + "%;max-width:100%;";

            var preview = "";
            preview += '<div style="text-align:' + align + ';">';
            preview +=
              '<div style="display:inline-block;' +
              widthStyle +
              'background:#0b1220;color:#fff;border:1px dashed #2f3f5c;border-radius:8px;padding:14px 10px;">';
            preview +=
              '<div style="font-size:12px;opacity:.8;">Embed Preview</div>';
            preview +=
              '<div style="font-size:11px;opacity:.7;margin-top:6px;">Size: ' +
              size +
              " | Position: " +
              align +
              "</div>";
            preview += "</div>";
            if (caption) {
              preview +=
                '<div style="font-size:12px;color:#334155;margin-top:6px;">' +
                escapeHtml(caption) +
                "</div>";
            }
            preview += "</div>";

            $("#rte-embed-live-preview").html(preview);
          }

          rteSwal({
            title: "Embed Media/Post",
            html:
              '<select id="rte-embed-type" class="swal2-input">' +
              '<option value="auto">Auto detect</option>' +
              '<option value="youtube">YouTube</option>' +
              '<option value="x">X / Twitter</option>' +
              '<option value="linkedin">LinkedIn</option>' +
              '<option value="instagram">Instagram</option>' +
              '<option value="facebook">Facebook</option>' +
              '<option value="tiktok">TikTok</option>' +
              '<option value="iframe">Raw iframe code</option>' +
              "</select>" +
              '<textarea id="rte-embed-input" class="swal2-textarea" placeholder="Paste URL or iframe code"></textarea>' +
              '<select id="rte-embed-size" class="swal2-input">' +
              '<option value="100">Size: Full (100%)</option>' +
              '<option value="75">Size: Medium (75%)</option>' +
              '<option value="50">Size: Small (50%)</option>' +
              '<option value="auto">Size: Auto</option>' +
              "</select>" +
              '<select id="rte-embed-align" class="swal2-input">' +
              '<option value="left">Position: Left</option>' +
              '<option value="center" selected>Position: Center</option>' +
              '<option value="right">Position: Right</option>' +
              "</select>" +
              '<input id="rte-embed-caption" class="swal2-input" placeholder="Optional caption" />' +
              '<div id="rte-embed-live-preview" style="padding:8px 4px 2px;"></div>',
            showCancelButton: true,
            confirmButtonText: "Insert Embed",
            focusConfirm: false,
            didOpen: function () {
              updateEmbedLivePreview();
              $("#rte-embed-size, #rte-embed-align").on(
                "change",
                updateEmbedLivePreview,
              );
              $("#rte-embed-caption").on("input", updateEmbedLivePreview);
            },
            preConfirm: function () {
              var embedType = $("#rte-embed-type").val();
              var source = $.trim($("#rte-embed-input").val());
              var size = $("#rte-embed-size").val() || "100";
              var align = $("#rte-embed-align").val() || "center";
              var caption = $.trim($("#rte-embed-caption").val());
              logEmbedStep("openEmbedDialog:preConfirm", {
                embedType: embedType,
                source: source,
                size: size,
                align: align,
                captionLength: caption.length,
              });

              if (!source) {
                logEmbedStep("openEmbedDialog:empty-source");
                showSwalValidation("Please paste a URL or embed code.");
                return false;
              }

              var embedData;
              if (embedType === "iframe") {
                embedData = /<iframe[\s\S]*?<\/iframe>/i.test(source)
                  ? { html: source }
                  : null;
              } else {
                embedData = buildEmbedFromInput(source);
              }

              if (!embedData || !embedData.html) {
                logEmbedStep("openEmbedDialog:embedData-invalid", {
                  embedType: embedType,
                  source: source,
                });
                if (embedType === "youtube") {
                  showSwalValidation(
                    "Invalid YouTube input. Paste a full YouTube URL or iframe code.",
                  );
                } else if (embedType === "linkedin") {
                  showSwalValidation(
                    "Invalid LinkedIn input. Paste a LinkedIn post/activity URL.",
                  );
                } else {
                  showSwalValidation("Unsupported or invalid embed input.");
                }
                return false;
              }

              return {
                embedData: embedData,
                caption: caption,
                size: size,
                align: align,
              };
            },
          }).then(function (result) {
            logEmbedStep("openEmbedDialog:then", { result: result });
            if (!isSwalConfirmed(result)) {
              logEmbedStep("openEmbedDialog:not-confirmed");
              return;
            }

            var payload = getSwalValue(result);
            if (!payload || !payload.embedData || !payload.embedData.html) {
              logEmbedStep("openEmbedDialog:payload-invalid", {
                payload: payload,
              });
              return;
            }
            var html = payload.embedData.html;

            var sizeValue = payload.size || "100";
            var alignValue = payload.align || "center";
            var widthStyle =
              sizeValue === "auto"
                ? "width:auto;max-width:100%;"
                : "width:" + sizeValue + "%;max-width:100%;";
            var alignStyle = "text-align:" + alignValue + ";";

            html =
              '<div class="rte-embed-wrap" style="' +
              alignStyle +
              'margin:10px 0;">' +
              '<div class="rte-embed-inner" style="display:inline-block;' +
              widthStyle +
              '">' +
              html +
              "</div></div>";

            if (payload.caption) {
              html +=
                '<p class="rte-embed-caption">' +
                escapeHtml(payload.caption) +
                "</p>";
            }

            logEmbedStep("openEmbedDialog:inserting", {
              htmlLength: html.length,
              hasScript: !!(
                payload.embedData &&
                payload.embedData.script &&
                payload.embedData.script.src
              ),
            });

            if (!insertEmbedHtmlReliable(html)) {
              logEmbedStep("openEmbedDialog:insert-failed");
              rteSwal({
                icon: "warning",
                type: "warning",
                title: "Embed not inserted",
                text: "Please click inside content area once, then try embed again.",
              });
              return;
            }
            logEmbedStep("openEmbedDialog:insert-success");
            ensureEmbedScript(payload.embedData.script);
            setTimeout(function () {
              logEmbedStep("openEmbedDialog:post-process-trigger");
              processEmbeddedContent();
            }, 0);
          });
        }

        function syncPastePlainButtonState(tb) {
          var button = $(".pastePlain", tb);
          if (!button.length) {
            return;
          }

          if (pastePlainMode) {
            button
              .addClass("active")
              .attr("title", "paste as plain text: ON")
              .css("background-color", "#ffe5a3");
          } else {
            button
              .removeClass("active")
              .attr("title", "paste as plain text: OFF")
              .css("background-color", "");
          }
        }

        function bindPlainPasteHandler() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return;
          }

          $(iframe.contentWindow.document)
            .off("paste.rtePlain")
            .on("paste.rtePlain", function (e) {
              if (!pastePlainMode) {
                return true;
              }

              var oe = e.originalEvent;
              var plainText =
                (oe &&
                  oe.clipboardData &&
                  oe.clipboardData.getData("text/plain")) ||
                (window.clipboardData &&
                  window.clipboardData.getData("Text")) ||
                "";

              if (!plainText) {
                return true;
              }

              e.preventDefault();
              formatText("insertText", plainText);
            });
        }

        function applyParagraphDefaults() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return;
          }

          try {
            iframe.contentWindow.document.execCommand(
              "defaultParagraphSeparator",
              false,
              "p",
            );
          } catch (e) {
            // ignore on unsupported browsers
          }

          try {
            iframe.contentWindow.document.execCommand(
              "insertBrOnReturn",
              false,
              false,
            );
          } catch (e) {
            // ignore on unsupported browsers
          }
        }

        function shouldKeepDivWrapper($node) {
          if (!$node || !$node.length) {
            return false;
          }

          if (
            $node.hasClass("rte-embed-wrap") ||
            $node.hasClass("rte-embed-inner") ||
            $node.hasClass("rte-embed") ||
            ($node.attr("class") &&
              /(^|\s)rte-embed/i.test($node.attr("class")))
          ) {
            return true;
          }

          if (
            $node.find(
              "img, iframe, video, figure, .rte-embed, blockquote.instagram-media, blockquote.tiktok-embed",
            ).length > 0
          ) {
            return true;
          }

          return false;
        }

        function normalizeEditorHtmlForSave(html) {
          var rawHtml = html || "";
          rawHtml = rawHtml.replace(
            /<div[^>]*>\s*<span[^>]*>\s*Block\s*<\/span>[\s\S]*?<option[^>]*>\s*Heading\s*<\/option>[\s\S]*?<button[^>]*>\s*Left\s*<\/button>[\s\S]*?<button[^>]*>\s*Center\s*<\/button>[\s\S]*?<button[^>]*>\s*Right\s*<\/button>[\s\S]*?<button[^>]*>\s*Justify\s*<\/button>[\s\S]*?<\/div>/gi,
            "",
          );

          var wrapper = $("<div></div>").html(rawHtml);

          // Remove editor helper UI artifacts before persisting content.
          $(".rte-selection-tools-floating", wrapper).remove();
          $("#rte-selection-tools-style", wrapper).remove();
          $("[id^='toolbar-']", wrapper).remove();

          $("[contenteditable]", wrapper).removeAttr("contenteditable");

          $("div", wrapper).each(function () {
            var $div = $(this);

            if (shouldKeepDivWrapper($div)) {
              return;
            }

            if ($div.closest("figure, li, td, th").length) {
              return;
            }

            if (
              $div.children(
                "div, p, h1, h2, h3, h4, h5, h6, ul, ol, table, blockquote, figure",
              ).length > 0
            ) {
              return;
            }

            var $p = $("<p></p>").html($div.html());

            var divClass = $div.attr("class");
            var divStyle = $div.attr("style");

            if (divClass) {
              $p.attr("class", divClass);
            }

            if (divStyle) {
              $p.attr("style", divStyle);
            }

            $div.replaceWith($p);
          });

          return wrapper.html();
        }

        // enable design mode
        function enableDesignMode() {
          var content = textarea.val();

          // Mozilla needs this to display caret
          if ($.trim(content) === "") {
            content = "";
          }

          // already created? show/hide
          if (iframe) {
            //console.log("already created");
            textarea.hide();
            $(iframe).contents().find("body").html(content);
            $(iframe).show();
            $("#toolbar-" + element_id).remove();
            textarea.before(toolbar());
            bindPlainPasteHandler();
            bindSelectionTracker();
            applyParagraphDefaults();
            return true;
          }

          // for compatibility reasons, need to be created this way
          iframe = document.createElement("iframe");
          iframe.frameBorder = 0;
          iframe.frameMargin = 0;
          iframe.framePadding = 0;
          iframe.height = 200;
          if (textarea.attr("class")) iframe.className = textarea.attr("class");
          if (textarea.attr("id")) iframe.id = element_id;
          if (textarea.attr("name")) iframe.title = textarea.attr("name");

          textarea.after(iframe);

          var doc = content;
          tryEnableDesignMode(doc, function () {
            $("#toolbar-" + element_id).remove();
            textarea.before(toolbar());
            bindPlainPasteHandler();
            bindSelectionTracker();
            applyParagraphDefaults();
            // hide textarea
            textarea.hide();
          });
        }

        function tryEnableDesignMode(doc, callback) {
          if (!iframe) {
            return false;
          }

          try {
            iframe.contentWindow.document.open();
            iframe.contentWindow.document.write(doc);
            iframe.contentWindow.document.close();
          } catch (error) {
            console.log(error);
          }
          if (document.contentEditable) {
            iframe.contentWindow.document.designMode = "On";
            callback();
            return true;
          } else if (document.designMode !== null) {
            try {
              iframe.contentWindow.document.designMode = "on";
              callback();
              return true;
            } catch (error) {
              console.log(error);
            }
          }
          setTimeout(function () {
            tryEnableDesignMode(doc, callback);
          }, 500);
          return false;
        }

        /** Disable Design Mode and show only html code */

        function disableDesignMode(submit) {
          var content = $(iframe).contents().find("body").html();
          content = normalizeEditorHtmlForSave(content);

          if ($(iframe).is(":visible")) {
            textarea.val(content);
          }

          if (submit !== true) {
            textarea.show();
            $(iframe).hide();
          }
        }

        // create toolbar and bind events to it's elements
        function toolbar() {
          var tb = $(
            "\
                        <div class='editor-menu' id='toolbar-" +
              element_id +
              "'>\
                            <select class='form-control editor-btn dropDownHeading heading'>\
                                <option value=''>Heading</option>\
                                <option value='p'>P</option>\
                                <option value='h1'>H1</option>\
                                <option value='h2'>H2</option>\
                                <option value='h3'>H3</option>\
                                <option value='h4'>H4</option>\
                                <option value='h5'>H5</option>\
                                <option value='h6'>H6</option>\
                            </select>\
                            <a class='editor-btn bold'><i class='fas fa-bold'></i></a>\
                            <a class='editor-btn italic'><i class='fas fa-italic'></i></a>\
                            <a class='editor-btn underline'><i class='fas fa-underline'></i></a>\
                            <a class='editor-btn subscript'><i class='fas fa-subscript'></i></a>\
                            <a class='editor-btn superscript'><i class='fas fa-superscript'></i></a>\
                            <a class='editor-btn unorderedlist'><i class='fas fa-list-ul'></i></a>\
                            <a class='editor-btn orderedlist'><i class='fas fa-list-ol'></i></a>\
                            <a class='editor-btn indent'><i class='fas fa-indent'></i></a>\
                            <a class='editor-btn outdent'><i class='fas fa-outdent'></i></a>\
                            <a class='editor-btn justifyLeft'><i class='fas fa-align-left'></i></a>\
                            <a class='editor-btn justifyRight'><i class='fas fa-align-right'></i></a>\
                            <a class='editor-btn justfyCenter'><i class='fas fa-align-center'></i></a>\
                            <a class='editor-btn justifyFull'><i class='fas fa-align-justify'></i></a>\
                            <a class='editor-btn link'><i class='fas fa-link'></i></a>\
                            <a class='editor-btn unlink'><i class='fas fa-unlink'></i></a>\
                            <a class='editor-btn image'><i class='fas fa-image'></i></a>\
                            <a class='editor-btn embed' title='embed post/media'><i class='fas fa-plug'></i></a>\
                            <a class='editor-btn pastePlain' title='paste as plain text: ON'><i class='fas fa-clipboard'></i></a>\
                            <a class='editor-btn fullScreen' title='full screen'><i class='fas fa-expand'></i></a>\
                            <a class='editor-btn minScreen' style='display: none' title='exit full screen'><i class='fas fa-compress'></i></a>\
                            <a class='editor-btn sourceCode' title='show source code'><i class='fas fa-file-code'></i></a>\
                            <a class='editor-btn htmlEnable' style='display: none' title='back to visual editor'><i class='fas fa-pen'></i></a>\
                        </div>\
                        <div class='imgBrowser' style='display: none;'>\
                            <div class='inner-imgBrowser'>\
                                <div class='box-side-menu'>\
                                    <ul>\
                                        <li class='library'>Media Library</li>\
                                        <li class='active upload'>Upload File</li>\
                                    </ul>\
                                </div>\
                                <div class='box-main-view'>\
                                    <div class='libraryBox' style='display: none'>\
                                        <div class='search-box'>\
                                            <input type='text' id='searchBar' name='imageSearch' placeholder='Search...'>\
                                        </div>\
                                        <div class='gallery-box'><ul class='gallery'></ul></div>\
                                    </div>\
                                    <div class='uploadBox' style='display: block'>\
                                        <form id='uploadFile' class='uploader-box'>\
                                            <h3>Drag &amp; drop files here</h3>\
                                            <div class='form-group'>\
                                                <span id='uploadText' >Open the file Browser</span>\
                                                <input type='file' name='upload' accept='image/x-png,image/gif,image/jpeg' />\
                                                <input type='hidden' name='type' value='singleUpload' />\
                                                <input type='hidden' name='action' value='upload' />\
                                            </div>\
                                        </form>\
                                        <div class='notice'><strong>only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls, .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></div>\
                                    </div>\
                                </div>\
                                <div class='close-window'><i class='fa fa-times'></i></div>\
                            </div>\
                        </div>\
                        ",
          );

          /* Disable all pop up box */

          $(document).on("keyup", function (evt) {
            if (evt.keyCode == 27) {
              $(".imgBrowser").hide();
              $(".linkBox").hide();
              $(".fullscreen-editor").removeClass("setFullScreen");
            }
          });

          $(".close-window", tb).click(function () {
            $(".imgBrowser").hide();
            $(".linkBox").hide();
          });

          $(".heading", tb).change(function () {
            var heading = $(this).val();
            if (!heading) {
              return;
            }
            formatText("formatBlock", heading);
            $(this).val("");
          });
          $(".bold", tb).click(function () {
            formatText("bold");
            return false;
          });
          $(".italic", tb).click(function () {
            formatText("italic");
            return false;
          });
          $(".underline", tb).click(function () {
            formatText("underline");
            return false;
          });
          $(".subscript", tb).click(function () {
            formatText("subscript");
            return false;
          });
          $(".superscript", tb).click(function () {
            formatText("superscript");
            return false;
          });
          $(".unorderedlist", tb).click(function () {
            formatText("insertunorderedlist");
            return false;
          });
          $(".orderedlist", tb).click(function () {
            formatText("insertorderedlist");
            return false;
          });
          $(".indent", tb).click(function () {
            formatText("indent");
            return false;
          });
          $(".outdent", tb).click(function () {
            formatText("outdent");
            return false;
          });
          $(".justifyLeft", tb).click(function () {
            formatText("justifyLeft");
            return false;
          });
          $(".justifyRight", tb).click(function () {
            formatText("justifyRight");
            return false;
          });
          $(".justfyCenter", tb).click(function () {
            formatText("justifyCenter");
            return false;
          });
          $(".justifyFull", tb).click(function () {
            formatText("justifyFull");
            return false;
          });
          $(".link", tb).click(function () {
            openLinkDialog();
            return false;
          });
          $(".unlink", tb).click(function () {
            formatText("unlink");
            return false;
          });
          $(".embed", tb).click(function () {
            openEmbedDialog();
            return false;
          });
          $(".pastePlain", tb).click(function () {
            pastePlainMode = !pastePlainMode;
            syncPastePlainButtonState(tb);
            return false;
          });
          $(".fullScreen", tb).click(function () {
            $(".fullScreen").hide();
            $(".minScreen").show();
            $(".fullscreen-editor").addClass("setFullScreen");
          });
          $(".minScreen", tb).click(function () {
            $(".minScreen").hide();
            $(".fullScreen").show();
            $(".fullscreen-editor").removeClass("setFullScreen");
          });

          /* Disable or enable text editor */

          $(".sourceCode", tb).click(function () {
            disableDesignMode();
            $(".heading").hide();
            $(".bold").hide();
            $(".italic").hide();
            $(".underline").hide();
            $(".subscript").hide();
            $(".superscript").hide();
            $(".unorderedlist").hide();
            $(".orderedlist").hide();
            $(".indent").hide();
            $(".outdent").hide();
            $(".justifyLeft").hide();
            $(".justifyRight").hide();
            $(".justfyCenter").hide();
            $(".justifyFull").hide();
            $(".link").hide();
            $(".unlink").hide();
            $(".image").hide();
            $(".embed").hide();
            $(".pastePlain").hide();
            hideSelectionTools();
            $(".sourceCode").hide();
            $(".htmlEnable").show();
          });

          $(".htmlEnable", tb).click(function () {
            $(".heading").show();
            $(".bold").show();
            $(".italic").show();
            $(".underline").show();
            $(".subscript").show();
            $(".superscript").show();
            $(".unorderedlist").show();
            $(".orderedlist").show();
            $(".indent").show();
            $(".outdent").show();
            $(".justifyLeft").show();
            $(".justifyRight").show();
            $(".justfyCenter").show();
            $(".justifyFull").show();
            $(".link").show();
            $(".unlink").show();
            $(".image").show();
            $(".embed").show();
            $(".pastePlain").show();
            hideSelectionTools();
            $(".sourceCode").show();
            $(".htmlEnable").hide();
            enableDesignMode();
          });

          syncPastePlainButtonState(tb);

          // call media browser

          $(".image", tb).click(function () {
            $(".imgBrowser").show();
          });

          // img browser pop up box

          $(".library", tb).click(function () {
            imageList("image");
            $(".library").addClass("active");
            $(".upload").removeClass("active");
            $(".libraryBox").css("display", "block");
            $(".uploadBox").css("display", "none");
          });

          $(".upload", tb).click(function () {
            $(".upload").addClass("active");
            $(".library").removeClass("active");
            $(".uploadBox").css("display", "block");
            $(".libraryBox").css("display", "none");
          });

          // search bar

          $("#searchBar", tb).on("keyup keypress", function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
              e.preventDefault();
              return false;
            }
            var search = $("#searchBar").val();
            if (search.length >= 3) {
              $(".gallery").empty();
              imageList("image", search);
            }
            if (search.length == 0) {
              $(".gallery").empty();
              imageList("image");
            }
          });

          // image uploader

          $("#uploadFile", tb).change(function (e) {
            var file = e.target.files[0];
            var imageType = /image.*/;

            if (!file.type.match(imageType)) return;

            var form_data = new FormData();
            form_data.append("file", file);

            $.ajax({
              type: "POST",
              data: new FormData(this),
              processData: false,
              contentType: false,
              dataType: "JSON",
              encrypt: "multipart/form-data",
              success: function (data) {
                if (data.status != 200) {
                  popUpMessage("center", data.msg, "warning");
                } else {
                  insertImage(data.msg);
                  $(".imgBrowser").hide();
                }
              },
              error: function (data) {
                console.log("i m here");
                console.log(data);
              },
            });
          });

          // Detect Paste to link

          $(iframe)
            .parents("form")
            .submit(function () {
              disableDesignMode();
            });

          return tb;
        }

        function imageList(imageType, name = "") {
          $.ajax({
            type: "GET",
            url: window.location.origin + "/api/media.php",
            dataType: "JSON",
            data: {
              name: name,
              media: imageType,
            },
            success: function (data) {
              $(".gallery").empty();
              var i = 1;
              $.each(data.images, function (index, value) {
                var showImage = $(
                  '<li class="imageBox"><img src="' + value + '" /></li>',
                ).on("click", function () {
                  insertImage(value);
                  $(".imgBrowser").hide();
                });
                $(".gallery").append(showImage);

                i++;
              });
              if (i == 1) {
                $(".gallery").empty();
                $(".gallery").append(
                  "\
                                   <p>Image Not Found</p>\
                                ",
                );
              }
            },
            error: function (data) {
              console.log("Error while fetching images");
            },
          });
        }

        function insertImage(src) {
          formatText("insertImage", src);
          setTimeout(function () {
            updateSelectionTools();
          }, 0);
          return false;
        }

        function formatText(command, option) {
          iframe.contentWindow.focus();
          try {
            iframe.contentWindow.document.execCommand(command, false, option);
          } catch (e) {
            console.log(e);
          }
          iframe.contentWindow.focus();
        }

        function getSelectionElement() {
          var selection;
          var range;
          var node;

          if (iframe.contentWindow.document.selection) {
            // IE selections
            selection = iframe.contentWindow.document.selection;
            range = selection.createRange();
            try {
              node = range.parentElement();
            } catch (e) {
              return false;
            }
          } else {
            // Mozilla selections
            try {
              selection = iframe.contentWindow.getSelection();
              range = selection.getRangeAt(0);
            } catch (e) {
              return false;
            }
            node = range.commonAncestorContainer;
          }

          return node;
        }

        // enable design mode now
        enableDesignMode();

        // publish all post where editor is enable

        $("#publishPost").click(function () {
          disableDesignMode();
          $("#publishPost").hide();
          $("#draftPost").hide();
          var postTitle = $("#title").val();
          var data = new FormData(document.getElementById("formData"));
          data.append("action", "publish");
          $.ajax({
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            dataType: "JSON",
            encrypt: "multipart/form-data",
            success: function (data) {
              if (data.status == 200) {
                popUpMessage("center", data.msg, data.status);
                window.setTimeout(function () {
                  window.location.href = data.redirect;
                }, 1600);
              } else {
                popUpMessage("center", data.msg, data.status);
              }
              $("#publishPost").show();
              $("#draftPost").show();
            },
            error: function (data) {
              console.log(data);
              $("#publishPost").show();
              $("#draftPost").show();
            },
          });
        });

        // Update all post where editor is enable

        $("#updatePost").click(function () {
          disableDesignMode();
          $("#updatePost").hide();
          $("#draftPost").hide();
          var postTitle = $("#title").val();
          var data = new FormData(document.getElementById("formData"));
          data.append("action", "update");

          $.ajax({
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            dataType: "JSON",
            encrypt: "multipart/form-data",
            success: function (data) {
              if (data.status == 200) {
                popUpMessage("center", data.msg, data.status);
                window.setTimeout(function () {
                  window.location.href = data.redirect;
                }, 1600);
              } else {
                popUpMessage("center", data.msg, data.status);
                $("#updatePost").show();
                $("#draftPost").show();
              }
            },
            error: function (data) {
              console.log(data);
              $("#updatePost").show();
              $("#draftPost").show();
            },
          });
        });

        // Schedule Post

        $("#schedulePost").click(function () {
          disableDesignMode();
          $("#schedulePost").hide();
          $("#draftPost").hide();
          var postTitle = $("#title").val();
          var data = new FormData(document.getElementById("formData"));
          data.append("type", "schedule");
          $.ajax({
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            dataType: "JSON",
            encrypt: "multipart/form-data",
            success: function (data) {
              if (data.status == 200) {
                popUpMessage("center", data.msg, data.status);
                window.setTimeout(function () {
                  window.location.href = data.redirect;
                }, 1600);
              } else {
                popUpMessage("center", data.msg, data.status);
              }
              $("#schedulePost").show();
              $("#draftPost").show();
            },
            error: function (data) {
              $("#schedulePost").show();
              $("#draftPost").show();
            },
          });
        });
      }); //return this.each
    }; // rte
  } // if

  $(".editor").rte({});
})(jQuery);
