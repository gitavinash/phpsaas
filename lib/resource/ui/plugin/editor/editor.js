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
      fullscreen: true,
      fullScreen: true,
    };

    $.fn.rte = function (options) {
      $.fn.rte.html = function (iframe) {
        return iframe.contentWindow.document.getElementsByTagName("body")[0]
          .innerHTML;
      };

      // build main options before element iteration
      var opts = $.extend(defaults, options);
      var isFullScreenEnabled =
        opts.fullscreen !== false && opts.fullScreen !== false;

      // iterate and construct the RTEs
      return this.each(function () {
        var textarea = $(this);
        var iframe;
        var element_id = textarea.attr("id");
        var pastePlainMode = true;
        var savedSelectionRange = null;
        var embedDebugEnabled = true;
        var selectedEditableElement = null;
        var isFirstClick = true;
        var replacingImageNode = null;

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

        function applyEditorContentSpacing() {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return;
          }

          var doc = iframe.contentWindow.document;
          var target = doc.head || doc.documentElement;

          if (!target) {
            return;
          }

          var styleTag = doc.getElementById("rte-editor-content-spacing");
          if (!styleTag) {
            styleTag = doc.createElement("style");
            styleTag.id = "rte-editor-content-spacing";
            target.appendChild(styleTag);
          }

          styleTag.textContent =
            "html,body{margin:0;padding:0;background:#fff;}" +
            "body{padding:14px;box-sizing:border-box;line-height:1.6;}" +
            "body > *:first-child{margin-top:0!important;}" +
            "p{margin:0 0 24px;}" +
            "h1,h2,h3,h4,h5,h6,blockquote,figure,ul,ol,table,pre,.rte-embed,.rte-embed-wrap,.rte-embed-inner{margin:0 0 18px;}" +
            "img,iframe,video{display:block;max-width:100%;margin:0 0 18px;}" +
            "figure img,figure iframe,figure video,.rte-embed img,.rte-embed iframe,.rte-embed video,.rte-embed-wrap img,.rte-embed-wrap iframe,.rte-embed-wrap video,.rte-embed-inner img,.rte-embed-inner iframe,.rte-embed-inner video{margin-bottom:0;}" +
            "figure > *:last-child,blockquote > *:last-child{margin-bottom:0;}";
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

          var tools = $(
            ".rte-selection-tools-floating",
            iframe.contentWindow.document,
          );

          return tools.length ? tools.eq(0) : tools;
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

          // Keep exactly one floating tools node in the editor document.
          var allTools = $(".rte-selection-tools-floating", doc);
          if (allTools.length > 1) {
            allTools.slice(1).remove();
            tools = allTools.eq(0);
          }

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
              ".rte-selection-tools-floating .rte-selection-image-alt,.rte-selection-tools-floating .rte-selection-image-replace{display:none;border:0;background:#334155;color:#fff;padding:5px 8px;cursor:pointer;border-radius:6px;font:12px/1.2 sans-serif;}" +
              ".rte-selection-tools-floating .rte-selection-image-replace{background:#059669;}" +
              ".rte-selection-tools-floating .rte-selection-caption{background:#2563eb;}" +
              ".rte-selection-tools-floating .rte-selection-remove-caption{background:#b91c1c;}" +
              ".rte-selection-tools-floating .rte-selection-image-alt:hover,.rte-selection-tools-floating .rte-selection-image-replace:hover{filter:brightness(1.08);}" +
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
              '<button type="button" class="rte-selection-image-replace" title="replace media image">Replace</button>' +
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

          $(".rte-selection-image-replace", tools).on("click", function () {
            var imgEl = getSelectedImageElement();
            if (imgEl) {
              replacingImageNode = imgEl;
              $(".imgBrowser").show();
            }
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

        function resolveClosestMediaElement(node) {
          var element = normalizeElementNode(node);
          if (!element || !element.tagName) {
            return null;
          }

          if (/^(IMG|IFRAME|VIDEO|FIGURE)$/i.test(element.tagName)) {
            return element;
          }

          var closestMedia = $(element).closest("img, iframe, video, figure");
          if (closestMedia.length) {
            return closestMedia.get(0);
          }

          return null;
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

        function getToolbarElement() {
          return $("#toolbar-" + element_id);
        }

        function setToolbarButtonState(toolbarElement, selector, isActive) {
          $(selector, toolbarElement).toggleClass("is-active", !!isActive);
        }

        function getCommandState(command) {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return false;
          }

          try {
            return !!iframe.contentWindow.document.queryCommandState(command);
          } catch (e) {
            return false;
          }
        }

        function getCurrentHeadingValue(targetNode) {
          var element =
            findClosestEditableElement(targetNode) ||
            findClosestEditableElement(getSelectionState().node) ||
            findClosestEditableElement(selectedEditableElement);

          if (!element) {
            return "";
          }

          if ($(element).closest("blockquote").length > 0) {
            return "blockquote";
          }

          var textElement = getTextContainerElement() || element;
          if (!textElement || !textElement.tagName) {
            return "";
          }

          var tagName = String(textElement.tagName).toLowerCase();
          if (/^h[1-6]$/.test(tagName) || tagName === "blockquote") {
            return tagName;
          }

          if (/^(p|div|body|li)$/.test(tagName)) {
            return "p";
          }

          return "";
        }

        function getNoCodeEditorLinkUrlValue() {
          var value = $.trim($("input[name='oldURL']").first().val() || "");
          return value;
        }

        function shouldShowSwitchToNoCodeButton() {
          var searchParams = new URLSearchParams(window.location.search);
          var pageType = (searchParams.get("type") || "").toLowerCase();
          var linkUrlValue = getNoCodeEditorLinkUrlValue();

          // Show this action only on edit pages where an existing link URL is available.
          if (!linkUrlValue) {
            return false;
          }

          if (pageType && pageType !== "edit") {
            return false;
          }

          return true;
        }

        function getSwitchToNoCodeUrl() {
          var linkUrlValue = getNoCodeEditorLinkUrlValue();
          var searchParams = new URLSearchParams(window.location.search);
          var pageId = $.trim(searchParams.get("id") || "");
          var pageType = $.trim(searchParams.get("type") || "");

          if (!linkUrlValue) {
            return "/dashboard/editor/";
          }

          var url =
            "/dashboard/editor/?url=" + encodeURIComponent(linkUrlValue);
          if (pageId) {
            url += "&id=" + encodeURIComponent(pageId);
          }
          if (pageType) {
            url += "&type=" + encodeURIComponent(pageType);
          }

          return url;
        }

        function rgbComponentToHex(value) {
          var parsed = Math.max(0, Math.min(255, parseInt(value, 10) || 0));
          var hex = parsed.toString(16);
          return hex.length === 1 ? "0" + hex : hex;
        }

        function normalizeColorToHex(colorValue, fallbackValue) {
          var color = $.trim(colorValue || "");
          if (!color) {
            return fallbackValue;
          }

          if (/^#[0-9a-f]{6}$/i.test(color)) {
            return color.toLowerCase();
          }

          if (/^#[0-9a-f]{3}$/i.test(color)) {
            return (
              "#" +
              color.charAt(1) +
              color.charAt(1) +
              color.charAt(2) +
              color.charAt(2) +
              color.charAt(3) +
              color.charAt(3)
            ).toLowerCase();
          }

          if (
            color === "transparent" ||
            /^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*0(?:\.0+)?\s*\)$/i.test(
              color,
            )
          ) {
            return fallbackValue;
          }

          var rgbMatch = color.match(
            /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i,
          );
          if (rgbMatch) {
            return (
              "#" +
              rgbComponentToHex(rgbMatch[1]) +
              rgbComponentToHex(rgbMatch[2]) +
              rgbComponentToHex(rgbMatch[3])
            ).toLowerCase();
          }

          return fallbackValue;
        }

        function ensureSelectValueOption($select, value, label) {
          if (!$select || !$select.length) {
            return;
          }

          $("option.rte-dynamic-option", $select).remove();

          if (!value) {
            $select.val("");
            return;
          }

          if ($select.find("option[value='" + value + "']").length === 0) {
            $select.prepend(
              $("<option></option>")
                .addClass("rte-dynamic-option")
                .attr("value", value)
                .text(label || value),
            );
          }

          $select.val(value);
        }

        function updateColorToolPreview($input) {
          if (!$input || !$input.length) {
            return;
          }

          var color = $input.val() || "#111111";
          $input
            .closest(".colorTool")
            .find(".color-preview")
            .css("background", color);
        }

        function syncToolbarVisualControls(toolbarElement, targetNode) {
          var element =
            findClosestEditableElement(targetNode) ||
            findClosestEditableElement(getSelectionState().node) ||
            findClosestEditableElement(selectedEditableElement);

          var textElement = getTextContainerElement() || element;
          var textColor = "#111111";
          var backgroundColor = "#fff59d";
          var fontSize = "";

          if (
            textElement &&
            iframe &&
            iframe.contentWindow &&
            typeof iframe.contentWindow.getComputedStyle === "function"
          ) {
            try {
              var computedStyle =
                iframe.contentWindow.getComputedStyle(textElement);
              fontSize = $.trim(computedStyle.fontSize || "");
              textColor = normalizeColorToHex(computedStyle.color, textColor);
              backgroundColor = normalizeColorToHex(
                computedStyle.backgroundColor,
                backgroundColor,
              );
            } catch (e) {
              // ignore and keep defaults
            }
          }

          ensureSelectValueOption(
            $(".fontSize", toolbarElement),
            fontSize,
            fontSize,
          );

          var $textColor = $(".textColor", toolbarElement);
          var $highlightColor = $(".highlightColor", toolbarElement);

          $textColor.val(textColor);
          updateColorToolPreview($textColor);

          $highlightColor.val(backgroundColor);
          updateColorToolPreview($highlightColor);
        }

        function syncToolbarButtonStates(targetNode) {
          var toolbarElement = getToolbarElement();
          if (!toolbarElement.length) {
            return;
          }

          var selectors = [
            ".bold",
            ".italic",
            ".underline",
            ".strikethrough",
            ".subscript",
            ".superscript",
            ".unorderedlist",
            ".orderedlist",
            ".justifyLeft",
            ".justifyRight",
            ".justfyCenter",
            ".justifyFull",
            ".blockquote",
          ];

          $(selectors.join(","), toolbarElement).removeClass("is-active");
          $(".heading", toolbarElement).val("");

          if (!iframe || !$(iframe).is(":visible")) {
            return;
          }

          $(".heading", toolbarElement).val(getCurrentHeadingValue(targetNode));
          syncToolbarVisualControls(toolbarElement, targetNode);

          setToolbarButtonState(
            toolbarElement,
            ".bold",
            getCommandState("bold"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".italic",
            getCommandState("italic"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".underline",
            getCommandState("underline"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".strikethrough",
            getCommandState("strikeThrough"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".subscript",
            getCommandState("subscript"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".superscript",
            getCommandState("superscript"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".unorderedlist",
            getCommandState("insertUnorderedList"),
          );
          setToolbarButtonState(
            toolbarElement,
            ".orderedlist",
            getCommandState("insertOrderedList"),
          );

          var element =
            findClosestEditableElement(targetNode) ||
            findClosestEditableElement(getSelectionState().node) ||
            findClosestEditableElement(selectedEditableElement);

          var textElement = getTextContainerElement() || element;
          var alignment = getComputedAlignment(textElement);

          setToolbarButtonState(
            toolbarElement,
            ".justifyLeft",
            alignment === "left",
          );
          setToolbarButtonState(
            toolbarElement,
            ".justfyCenter",
            alignment === "center",
          );
          setToolbarButtonState(
            toolbarElement,
            ".justifyRight",
            alignment === "right",
          );
          setToolbarButtonState(
            toolbarElement,
            ".justifyFull",
            alignment === "justify",
          );

          var isBlockquote = false;
          if (element) {
            isBlockquote = $(element).closest("blockquote").length > 0;
          }
          setToolbarButtonState(toolbarElement, ".blockquote", isBlockquote);
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

          var mediaAncestor = resolveClosestMediaElement(element);
          if (mediaAncestor) {
            if (mediaAncestor.tagName === "IMG") {
              return mediaAncestor;
            }

            if (mediaAncestor.tagName === "FIGURE") {
              return mediaAncestor.querySelector("img");
            }
          }

          return null;
        }

        function getSelectedMediaElement() {
          var element = normalizeElementNode(selectedEditableElement);
          if (!element || !element.tagName) {
            return null;
          }

          if (/^(IMG|IFRAME|VIDEO|FIGURE)$/i.test(element.tagName)) {
            return element;
          }

          return resolveClosestMediaElement(element);
        }

        function isMediaCandidateElement(element) {
          if (!element || !element.tagName) {
            return false;
          }

          if (/^(IMG|IFRAME|VIDEO|FIGURE)$/i.test(element.tagName)) {
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
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document
          ) {
            return;
          }

          var tools = $(
            ".rte-selection-tools-floating",
            iframe.contentWindow.document,
          );
          tools.hide();
          $(".rte-selection-caption", tools).hide();
          $(".rte-selection-remove-caption", tools).hide();
          $(".rte-selection-image-alt", tools).hide();
          $(".rte-selection-image-replace", tools).hide();
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

        function getMediaSizeControlValue(mediaNode) {
          if (!mediaNode || !mediaNode.style) {
            return "auto";
          }

          var widthValue = $.trim(mediaNode.style.width || "");
          if (!widthValue) {
            return "auto";
          }

          if (/^\d+%$/.test(widthValue)) {
            return widthValue.replace("%", "");
          }

          return "auto";
        }

        function applyImageMetaValues(imageNode, altText, captionText) {
          if (!imageNode || imageNode.tagName !== "IMG") {
            return;
          }

          imageNode.setAttribute("alt", altText || "");

          var figure = ensureMediaFigure(imageNode);
          var figcaption = figure ? figure.querySelector("figcaption") : null;

          if (!captionText) {
            if (figcaption) {
              figcaption.parentNode.removeChild(figcaption);
            }
            updateSelectionTools(figure || imageNode);
            return;
          }

          if (!figcaption) {
            figcaption = imageNode.ownerDocument.createElement("figcaption");
            figure.appendChild(figcaption);
          }

          figcaption.textContent = captionText;
          figcaption.style.fontSize = "13px";
          figcaption.style.color = "#222";
          figcaption.style.background = "#f6f8fa";
          figcaption.style.borderRadius = "6px";
          figcaption.style.marginTop = "6px";
          figcaption.style.padding = "6px 10px";
          figcaption.style.display = "block";
          figcaption.style.textAlign = "center";
          figcaption.style.boxShadow = "0 1px 4px rgba(0,0,0,0.04)";
          updateSelectionTools(figure);
        }
        // --- TABLE SUPPORT ---
        function openTableDialog() {
          rteSwal({
            title: "Insert Table",
            html:
              '<input id="rte-table-rows" class="swal2-input" type="number" min="1" max="20" value="2" placeholder="Rows" />' +
              '<input id="rte-table-cols" class="swal2-input" type="number" min="1" max="10" value="2" placeholder="Columns" />',
            showCancelButton: true,
            confirmButtonText: "Insert Table",
            focusConfirm: false,
            preConfirm: function () {
              var rows = parseInt($("#rte-table-rows").val(), 10) || 2;
              var cols = parseInt($("#rte-table-cols").val(), 10) || 2;
              if (rows < 1 || cols < 1) {
                showSwalValidation("Rows and columns must be at least 1.");
                return false;
              }
              return { rows: rows, cols: cols };
            },
          }).then(function (result) {
            if (!isSwalConfirmed(result)) return;
            var data = getSwalValue(result);
            if (!data) return;
            insertTableAtCursor(data.rows, data.cols);
          });
        }

        function insertTableAtCursor(rows, cols) {
          var html =
            '<table class="rte-table" style="border-collapse:collapse;width:100%;margin-bottom:18px;background:#fff">';
          html += "<thead><tr>";
          for (var c = 0; c < cols; c++) {
            html +=
              '<th style="border:1px solid #d5dbe3;padding:8px;background:#f3f4f6;">Header</th>';
          }
          html += "</tr></thead><tbody>";
          for (var r = 0; r < rows; r++) {
            html += "<tr>";
            for (var c2 = 0; c2 < cols; c2++) {
              html +=
                '<td style="border:1px solid #d5dbe3;padding:8px;">Cell</td>';
            }
            html += "</tr>";
          }
          html += "</tbody></table>";
          insertHtmlAtCursor(html);
        }

        // Table context menu for editing
        function bindTableContextMenu() {
          if (!iframe || !iframe.contentWindow) return;
          $(iframe.contentWindow.document)
            .off("contextmenu.rteTable")
            .on(
              "contextmenu.rteTable",
              "table.rte-table, th, td",
              function (e) {
                e.preventDefault();
                var $cell = $(e.target).closest("td,th");
                var $table = $(e.target).closest("table.rte-table");
                if (!$table.length) return;
                var menuHtml =
                  '<div id="rte-table-menu" style="position:fixed;z-index:99999;background:#fff;border:1px solid #d5dbe3;padding:8px 12px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.08);font-size:13px;">' +
                  '<button type="button" class="rte-table-add-row">Add Row</button> ' +
                  '<button type="button" class="rte-table-add-col">Add Col</button> ' +
                  '<button type="button" class="rte-table-del-row">Delete Row</button> ' +
                  '<button type="button" class="rte-table-del-col">Delete Col</button> ' +
                  '<button type="button" class="rte-table-props">Table Properties</button>' +
                  "</div>";
                $("#rte-table-menu").remove();
                $(iframe.contentWindow.document.body).append(menuHtml);
                var menu = $("#rte-table-menu", iframe.contentWindow.document);
                menu.css({ left: e.clientX + 10, top: e.clientY + 10 });

                menu.on("mousedown", function (ev) {
                  ev.stopPropagation();
                });
                menu.on("click", ".rte-table-add-row", function () {
                  var idx = $cell.parent().index();
                  var newRow = $cell.parent().clone();
                  newRow.find("td,th").text("Cell");
                  $cell.parent().after(newRow);
                  menu.remove();
                });
                menu.on("click", ".rte-table-add-col", function () {
                  var colIdx = $cell.index();
                  $table.find("tr").each(function () {
                    var cellType = $(this).find("th").length ? "th" : "td";
                    var cell = $("<" + cellType + ">Cell</" + cellType + ">");
                    if (colIdx >= 0) {
                      $(this).find(cellType).eq(colIdx).after(cell);
                    } else {
                      $(this).append(cell);
                    }
                  });
                  menu.remove();
                });
                menu.on("click", ".rte-table-del-row", function () {
                  $cell.parent().remove();
                  menu.remove();
                });
                menu.on("click", ".rte-table-del-col", function () {
                  var colIdx = $cell.index();
                  $table.find("tr").each(function () {
                    $(this).find("td,th").eq(colIdx).remove();
                  });
                  menu.remove();
                });
                menu.on("click", ".rte-table-props", function () {
                  if ($cell.length) {
                    var cellNode = $cell.get(0);
                    var sel = iframe.contentWindow.getSelection();
                    var range = iframe.contentWindow.document.createRange();
                    range.selectNodeContents(cellNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                  }
                  // Let the existing selection listeners decide table toolbar visibility.
                  $(iframe.contentWindow.document).triggerHandler(
                    "selectionchange",
                  );
                  $(iframe.contentWindow.document).triggerHandler("click");
                  menu.remove();
                });
                $(iframe.contentWindow.document).one(
                  "mousedown click keydown",
                  function () {
                    menu.remove();
                  },
                );
              },
            );
        }

        function openImageMetaDialog(
          imageNode,
          dialogTitle,
          confirmButtonText,
        ) {
          if (!imageNode || imageNode.tagName !== "IMG") {
            return;
          }

          var figure = getExistingMediaFigure(imageNode);
          var figcaption = figure ? figure.querySelector("figcaption") : null;
          var currentCaption = figcaption ? $.trim($(figcaption).text()) : "";
          var currentAlt = $.trim(imageNode.getAttribute("alt") || "");

          rteSwal({
            title: dialogTitle || "Image Details",
            html:
              '<input id="rte-image-alt" class="swal2-input" placeholder="Alt text" value="' +
              escapeHtml(currentAlt) +
              '" />' +
              '<input id="rte-image-caption" class="swal2-input" placeholder="Optional caption" value="' +
              escapeHtml(currentCaption) +
              '" />',
            showCancelButton: true,
            cancelButtonText: "Skip",
            confirmButtonText: confirmButtonText || "Save",
            focusConfirm: false,
            preConfirm: function () {
              return {
                alt: $.trim($("#rte-image-alt").val() || ""),
                caption: $.trim($("#rte-image-caption").val() || ""),
              };
            },
          }).then(function (result) {
            if (!isSwalConfirmed(result)) {
              updateSelectionTools(figure || imageNode);
              return;
            }

            var data = getSwalValue(result) || {};
            applyImageMetaValues(imageNode, data.alt || "", data.caption || "");
          });
        }

        function findInsertedImageBySrc(src) {
          if (
            !iframe ||
            !iframe.contentWindow ||
            !iframe.contentWindow.document ||
            !src
          ) {
            return null;
          }

          var images = $(iframe.contentWindow.document).find("img").get();
          for (var index = images.length - 1; index >= 0; index--) {
            var imageNode = images[index];
            var imageSrc = imageNode.getAttribute("src") || imageNode.src || "";
            if (imageSrc === src || imageNode.src === src) {
              return imageNode;
            }
          }

          return images.length ? images[images.length - 1] : null;
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
          if (!iframe || !iframe.contentWindow) {
            return;
          }

          iframe.contentWindow.focus();
          restoreSelectionRange();

          var activeSelectionNode = findClosestEditableElement(
            getSelectionState().node,
          );
          if (activeSelectionNode) {
            selectedEditableElement = activeSelectionNode;
          }

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
          saveSelectionRange();
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
          var resolvedTarget = targetNode
            ? findClosestEditableElement(targetNode) ||
              normalizeElementNode(targetNode)
            : null;

          if (resolvedTarget) {
            selectedEditableElement = resolvedTarget;
          }

          syncToolbarButtonStates(resolvedTarget || targetNode);

          var tools = ensureFloatingSelectionTools();
          var mediaElement = getSelectedMediaElement();
          var primaryMediaNode = getPrimaryMediaNode() || mediaElement;
          var contentType = getSelectionContentType();

          if (!tools.length || !mediaElement || contentType === "text") {
            hideSelectionTools();
            return;
          }

          var doc = iframe.contentWindow.document;
          var win = iframe.contentWindow;
          $(".rte-selection-tools-floating", doc).hide();
          var rect =
            primaryMediaNode && primaryMediaNode.getBoundingClientRect
              ? primaryMediaNode.getBoundingClientRect()
              : null;

          if (!rect || (!rect.width && !rect.height)) {
            hideSelectionTools();
            return;
          }

          var figure = getExistingMediaFigure(mediaElement);
          var figcaption = figure ? figure.querySelector("figcaption") : null;
          var hasCaption = !!(figcaption && $.trim($(figcaption).text()));
          var sizeValue = getMediaSizeControlValue(primaryMediaNode);
          var supportsAltOrTitle =
            !!primaryMediaNode &&
            /^(IMG|IFRAME|VIDEO)$/i.test(primaryMediaNode.tagName);
          var scrollTop =
            win.pageYOffset ||
            doc.documentElement.scrollTop ||
            doc.body.scrollTop ||
            0;
          var scrollLeft =
            win.pageXOffset ||
            doc.documentElement.scrollLeft ||
            doc.body.scrollLeft ||
            0;

          var isImage =
            !!primaryMediaNode && primaryMediaNode.tagName === "IMG";
          $(".rte-selection-image-replace", tools).toggle(isImage);
          $(".rte-selection-image-alt", tools).toggle(supportsAltOrTitle);
          $(".rte-selection-caption", tools)
            .show()
            .text(hasCaption ? "Edit caption" : "Caption");
          $(".rte-selection-remove-caption", tools).toggle(hasCaption);
          $(".rte-selection-media-size", tools).show().val(sizeValue);

          tools.css({
            display: "flex",
            visibility: "hidden",
            top: 0,
            left: 0,
          });

          var toolsWidth = tools.outerWidth() || 0;
          var toolsHeight = tools.outerHeight() || 0;
          var minLeft = scrollLeft + 8;
          var maxLeft =
            scrollLeft +
            Math.max(
              8,
              (win.innerWidth || doc.documentElement.clientWidth || 0) -
                toolsWidth -
                8,
            );
          var computedLeft =
            scrollLeft + rect.left + rect.width / 2 - toolsWidth / 2;
          var computedTop = scrollTop + rect.top - toolsHeight - 10;

          if (computedTop < scrollTop + 8) {
            computedTop = scrollTop + rect.bottom + 10;
          }

          if (computedLeft < minLeft) {
            computedLeft = minLeft;
          }

          if (computedLeft > maxLeft) {
            computedLeft = maxLeft;
          }

          tools.css({
            top: computedTop + "px",
            left: computedLeft + "px",
            visibility: "visible",
          });
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
          var mediaClickSelector = "img, figure, figcaption, video, iframe";

          $(iframe.contentWindow.document)
            .off("mousedown.rteMedia")
            .on("mousedown.rteMedia", mediaClickSelector, function () {
              var mediaEl = resolveClosestMediaElement(this) || this;
              if (isInsideSelectionTools(mediaEl)) {
                return;
              }
              selectedEditableElement = mediaEl;
              if (
                typeof window !== "undefined" &&
                window.requestAnimationFrame
              ) {
                window.requestAnimationFrame(function () {
                  updateSelectionTools(mediaEl);
                });
              } else {
                setTimeout(function () {
                  updateSelectionTools(mediaEl);
                }, 0);
              }
            });

          $(iframe.contentWindow.document)
            .off("dblclick.rteMedia")
            .on("dblclick.rteMedia", "img", function () {
              replacingImageNode = this;
              $(".imgBrowser").show();
            });

          $(iframe.contentWindow.document)
            .off("mouseup.rteSelection keyup.rteSelection click.rteSelection")
            .on(
              "mouseup.rteSelection keyup.rteSelection click.rteSelection",
              function (event) {
                if (isInsideSelectionTools(event.target)) {
                  return;
                }
                var mediaTarget = resolveClosestMediaElement(event.target);
                scheduleSelectionRefresh(mediaTarget || event.target);
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

          // Keep first click in normal mode; fullscreen remains manual via toolbar button.
          $(iframe.contentWindow.document).one(
            "click.rteFirstClick",
            function () {
              isFirstClick = false;
            },
          );
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

          // Clean up p tags: remove empty class and style attributes
          $("p", wrapper).each(function () {
            var $p = $(this);
            var classAttr = $p.attr("class") || "";
            var styleAttr = $p.attr("style") || "";

            // Remove class if empty or whitespace only
            if (classAttr === "" || /^\s*$/.test(classAttr)) {
              $p.removeAttr("class");
            }

            // Remove style if empty or whitespace only
            if (styleAttr === "" || /^\s*$/.test(styleAttr)) {
              $p.removeAttr("style");
            }
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
            applyEditorContentSpacing();
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
          // Restrict editor height to window height and make scrollable if needed
          iframe.style.maxHeight = window.innerHeight - 60 + "px";
          iframe.style.overflowY = "auto";
          iframe.style.width = "100%";
          iframe.style.boxSizing = "border-box";
          if (textarea.attr("class")) iframe.className = textarea.attr("class");
          if (textarea.attr("id")) iframe.id = element_id;
          if (textarea.attr("name")) iframe.title = textarea.attr("name");

          textarea.after(iframe);

          var doc = content;
          tryEnableDesignMode(doc, function () {
            $("#toolbar-" + element_id).remove();
            textarea.before(toolbar());
            applyEditorContentSpacing();
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
                            <a class='editor-btn undo' title='Undo last change'><i class='fas fa-undo'></i></a>\
                            <a class='editor-btn redo' title='Redo last undone change'><i class='fas fa-redo'></i></a>\
                            <select class='form-control editor-btn dropDownHeading heading group-start' title='Choose paragraph or heading style'>\
                                <option value=''>Heading</option>\
                                <option value='p'>P</option>\
                                <option value='h1'>H1</option>\
                                <option value='h2'>H2</option>\
                                <option value='h3'>H3</option>\
                                <option value='h4'>H4</option>\
                                <option value='h5'>H5</option>\
                                <option value='h6'>H6</option>\
                              <option value='blockquote'>Quote</option>\
                            </select>\
                            <select class='form-control editor-btn dropDownFontSize fontSize' title='Change selected text size'>\
                              <option value=''>Size</option>\
                              <option value='12px'>12px</option>\
                              <option value='14px'>14px</option>\
                              <option value='16px'>16px</option>\
                              <option value='18px'>18px</option>\
                              <option value='20px'>20px</option>\
                              <option value='24px'>24px</option>\
                              <option value='28px'>28px</option>\
                              <option value='32px'>32px</option>\
                              <option value='36px'>36px</option>\
                              <option value='40px'>40px</option>\
                              <option value='48px'>48px</option>\
                              <option value='56px'>56px</option>\
                              <option value='64px'>64px</option>\
                              <option value='72px'>72px</option>\
                              <option value='96px'>96px</option>\
                              <option value='120px'>120px</option>\
                              <option value='144px'>144px</option>\
                            </select>\
                            <a class='editor-btn bold group-start' title='Bold: make selected text bold'><i class='fas fa-bold'></i></a>\
                            <a class='editor-btn italic' title='Italic: make selected text italic'><i class='fas fa-italic'></i></a>\
                            <a class='editor-btn underline' title='Underline: add underline to selected text'><i class='fas fa-underline'></i></a>\
                            <a class='editor-btn strikethrough' title='Strikethrough: cross selected text'><i class='fas fa-strikethrough'></i></a>\
                            <a class='editor-btn subscript' title='Subscript: lower small text'><i class='fas fa-subscript'></i></a>\
                            <a class='editor-btn superscript' title='Superscript: raise small text'><i class='fas fa-superscript'></i></a>\
                            <label class='editor-btn colorTool textColorControl' title='Text color: change selected text color'>\
                              <i class='fas fa-font'></i>\
                              <span class='color-preview' style='background:#111111;'></span>\
                              <input type='color' class='textColor' value='#111111' aria-label='Text color'>\
                            </label>\
                            <label class='editor-btn colorTool highlightColorControl' title='Highlight color: set text background color'>\
                              <i class='fas fa-highlighter'></i>\
                              <span class='color-preview' style='background:#fff59d;'></span>\
                              <input type='color' class='highlightColor' value='#fff59d' aria-label='Background color'>\
                            </label>\
                            <a class='editor-btn clearFormatting' title='Clear formatting: remove style from selection'><i class='fas fa-eraser'></i></a>\
                            <a class='editor-btn unorderedlist group-start' title='Bulleted list: create bullet points'><i class='fas fa-list-ul'></i></a>\
                            <a class='editor-btn orderedlist' title='Numbered list: create numbered points'><i class='fas fa-list-ol'></i></a>\
                            <a class='editor-btn indent' title='Increase indent: move text right'><i class='fas fa-indent'></i></a>\
                            <a class='editor-btn outdent' title='Decrease indent: move text left'><i class='fas fa-outdent'></i></a>\
                            <a class='editor-btn justifyLeft' title='Align left: align text to left edge'><i class='fas fa-align-left'></i></a>\
                            <a class='editor-btn justfyCenter' title='Align center: center selected text'><i class='fas fa-align-center'></i></a>\
                            <a class='editor-btn justifyRight' title='Align right: align text to right edge'><i class='fas fa-align-right'></i></a>\
                            <a class='editor-btn justifyFull' title='Justify: align text to both edges'><i class='fas fa-align-justify'></i></a>\
                            <a class='editor-btn blockquote group-start' title='Quote: format selected text as a quote'><i class='fas fa-quote-left'></i></a>\
                            <a class='editor-btn codeBlock' title='Code block: wrap selection in code style'><i class='fas fa-code'></i></a>\
                            <a class='editor-btn horizontalLine' title='Horizontal line: insert a divider'><i class='fas fa-minus'></i></a>\
                            <a class='editor-btn link group-start' title='Insert link: add URL to selected text'><i class='fas fa-link'></i></a>\
                            <a class='editor-btn unlink' title='Remove link: clear URL from selected text'><i class='fas fa-unlink'></i></a>\
                            <a class='editor-btn image' title='Insert image: open media library'><i class='fas fa-image'></i></a>\
                            <a class='editor-btn embed' title='Embed post or media from supported link'><i class='fas fa-plug'></i></a>\
                            <a class='editor-btn pastePlain' title='Paste plain text mode: toggle on or off'><i class='fas fa-clipboard'></i></a>\
                            <a class='editor-btn htmlEnable' style='display: none' title='Back to visual editor mode'><i class='fas fa-pen'></i></a>\
                            <a class='editor-btn sourceCode group-start' title='Show HTML source mode'><i class='fas fa-file-code'></i></a>\
                            <a class='editor-btn fullScreen group-start' title='Full Screen'><i class='fas fa-expand'></i></a>\
                            <a class='editor-btn minScreen group-start' style='display: none' title='Exit Full Screen'><i class='fas fa-compress'></i></a>\
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

          // if (shouldShowSwitchToNoCodeButton()) {
          //   $(".sourceCode", tb).after(
          //     "<a class='editor-btn switchNoCode' href='" +
          //       getSwitchToNoCodeUrl() +
          //       "' title='Open no-code editor' style='width:auto!important;min-width:170px;padding:0 14px;background:#1d4ed8;color:#fff;border-color:#1d4ed8;box-shadow:0 6px 20px rgba(59,130,246,0.18);font-weight:700;'>" +
          //       "<i class='fas fa-magic' style='margin-right:8px;font-size:12px;'></i>Open no-code editor</a>",
          //   );
          // }

          // Use custom hover tooltips for actionable controls only.
          // Keep native title on select controls to avoid UI quirks.
          $("a.editor-btn[title], label.editor-btn[title]", tb).each(
            function () {
              var tip = $(this).attr("title");
              if (!tip) {
                return;
              }
              $(this).attr("data-tooltip", tip);
              if (!$(this).attr("aria-label")) {
                $(this).attr("aria-label", tip);
              }
              $(this).removeAttr("title");
            },
          );

          // Keep tooltips visible in fullscreen and near viewport edges.
          tb.on("mouseenter focusin", ".editor-btn[data-tooltip]", function () {
            var rect = this.getBoundingClientRect();
            var viewportHeight =
              window.innerHeight || document.documentElement.clientHeight;
            var viewportWidth =
              window.innerWidth || document.documentElement.clientWidth;
            var tipText = $(this).attr("data-tooltip") || "";
            var estimatedTooltipWidth = Math.min(
              260,
              Math.max(90, tipText.length * 6.4 + 24),
            );
            var nearTopEdge = rect.top < 70;
            var nearBottomEdge = rect.bottom > viewportHeight - 70;
            var nearLeftEdge = rect.left < estimatedTooltipWidth / 2 + 12;
            var nearRightEdge =
              rect.right > viewportWidth - estimatedTooltipWidth / 2 - 12;

            $(this)
              .toggleClass("tooltip-down", nearTopEdge && !nearBottomEdge)
              .toggleClass("tooltip-right", nearLeftEdge && !nearRightEdge)
              .toggleClass("tooltip-left", nearRightEdge && !nearLeftEdge);
          });

          tb.on(
            "mouseleave focusout",
            ".editor-btn[data-tooltip]",
            function () {
              $(this).removeClass("tooltip-down tooltip-left tooltip-right");
            },
          );

          /* Disable all pop up box */

          $(document).on("keyup", function (evt) {
            if (evt.keyCode == 27) {
              $(".imgBrowser").hide();
              $(".linkBox").hide();
              $(".fullscreen-editor").removeClass("setFullScreen");
              $(".minScreen").hide();
              $(".fullScreen").show();
              replacingImageNode = null;
            }
          });

          $(".close-window", tb).click(function () {
            $(".imgBrowser").hide();
            $(".linkBox").hide();
            replacingImageNode = null;
          });

          $(".heading", tb).change(function () {
            var heading = $(this).val();
            if (!heading) {
              return;
            }

            iframe.contentWindow.focus();
            restoreSelectionRange();
            applyHeadingToSelection(heading);
            saveSelectionRange();
            syncToolbarButtonStates();
          });

          $(".fontSize", tb).change(function () {
            var fontSize = $(this).val();
            if (!fontSize) {
              return;
            }

            iframe.contentWindow.focus();

            var applied = false;
            var selection = iframe.contentWindow.getSelection();
            var doc = iframe.contentWindow.document;

            if (selection && selection.rangeCount > 0) {
              var range = selection.getRangeAt(0);

              if (!range.collapsed) {
                var span = doc.createElement("span");
                span.style.fontSize = fontSize;

                try {
                  range.surroundContents(span);
                } catch (e) {
                  var fragment = range.extractContents();
                  span.appendChild(fragment);
                  range.insertNode(span);
                }

                applied = true;
              }
            }

            if (!applied) {
              var textElement = getTextContainerElement();
              if (textElement && textElement.style) {
                textElement.style.fontSize = fontSize;
                applied = true;
                updateSelectionTools(textElement);
              }
            }

            if (!applied) {
              iframe.contentWindow.document.execCommand(
                "styleWithCSS",
                false,
                true,
              );
              iframe.contentWindow.document.execCommand("fontSize", false, "3");
            }

            saveSelectionRange();
            syncToolbarButtonStates();
            return false;
          });

          function updateColorToolPreview($input) {
            if (!$input || !$input.length) {
              return;
            }

            var color = $input.val() || "#111111";
            $input
              .closest(".colorTool")
              .find(".color-preview")
              .css("background", color);
          }

          function bindColorTool(selector, command) {
            $(selector, tb).each(function () {
              updateColorToolPreview($(this));
            });

            $(selector, tb).on("mousedown click focus", function () {
              saveSelectionRange();
            });

            $(selector, tb).on("input change", function () {
              var color = $(this).val();
              updateColorToolPreview($(this));
              iframe.contentWindow.focus();
              restoreSelectionRange();

              try {
                iframe.contentWindow.document.execCommand(
                  "styleWithCSS",
                  false,
                  true,
                );
              } catch (e) {
                // ignore on unsupported browsers
              }

              iframe.contentWindow.document.execCommand(command, false, color);
              saveSelectionRange();
              syncToolbarButtonStates();
              return false;
            });
          }

          bindColorTool(".textColor", "foreColor");
          bindColorTool(".highlightColor", "backColor");

          $(".undo", tb).click(function () {
            formatText("undo");
            return false;
          });

          $(".redo", tb).click(function () {
            formatText("redo");
            return false;
          });

          $(".strikethrough", tb).click(function () {
            formatText("strikeThrough");
            return false;
          });

          $(".clearFormatting", tb).click(function () {
            iframe.contentWindow.focus();
            iframe.contentWindow.document.execCommand(
              "removeFormat",
              false,
              null,
            );
            iframe.contentWindow.document.execCommand("unlink", false, null);
            return false;
          });

          $(".blockquote", tb).click(function () {
            iframe.contentWindow.focus();
            restoreSelectionRange();
            applyHeadingToSelection("blockquote");
            saveSelectionRange();
            return false;
          });

          $(".horizontalLine", tb).click(function () {
            iframe.contentWindow.focus();
            restoreSelectionRange();
            if (
              !insertHtmlAtCursor(
                "<hr style='margin:18px 0;border:0;border-top:1px solid #d5dbe3;' />",
              )
            ) {
              appendHtmlToEditorEnd(
                "<hr style='margin:18px 0;border:0;border-top:1px solid #d5dbe3;' />",
              );
            }
            saveSelectionRange();
            return false;
          });

          $(".codeBlock", tb).click(function () {
            iframe.contentWindow.focus();
            var selection = iframe.contentWindow.getSelection();
            if (selection && selection.rangeCount > 0) {
              var range = selection.getRangeAt(0);
              if (!range.collapsed) {
                var selectedText = range.toString();
                var pre = iframe.contentWindow.document.createElement("pre");
                var code = iframe.contentWindow.document.createElement("code");
                code.style.backgroundColor = "#f5f5f5";
                code.style.padding = "10px";
                code.style.borderRadius = "4px";
                code.style.fontFamily = "Monaco, Consolas, monospace";
                code.style.fontSize = "12px";
                code.textContent = selectedText;
                pre.appendChild(code);
                pre.style.overflow = "auto";
                pre.style.margin = "10px 0";
                var newRange = range.cloneRange();
                newRange.deleteContents();
                newRange.insertNode(pre);
              }
            }
            return false;
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
          function performUnlink() {
            formatText("unlink");
          }
          $(".link", tb).click(function () {
            openLinkDialog();
            return false;
          });
          $(".unlink", tb).click(function () {
            performUnlink();
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
          if (!isFullScreenEnabled) {
            $(".fullScreen, .minScreen", tb).hide();
          }

          $(".fullScreen", tb).click(function () {
            var container = textarea.closest(".fullscreen-editor");
            if (!container.length) {
              container = textarea.parent();
              container.addClass("fullscreen-editor");
            }
            $(".fullScreen", tb).hide();
            $(".minScreen", tb).show();
            container.addClass("setFullScreen");
            return false;
          });
          $(".minScreen", tb).click(function () {
            var container = textarea.closest(".fullscreen-editor");
            if (!container.length) {
              container = textarea.parent();
            }
            $(".minScreen", tb).hide();
            $(".fullScreen", tb).show();
            container.removeClass("setFullScreen");
            return false;
          });

          /* Disable or enable text editor */

          $(".sourceCode", tb).click(function () {
            disableDesignMode();
            $(".heading").hide();
            $(".fontSize").hide();
            $(".textColorControl").hide();
            $(".highlightColorControl").hide();
            $(".undo").hide();
            $(".redo").hide();
            $(".bold").hide();
            $(".italic").hide();
            $(".underline").hide();
            $(".strikethrough").hide();
            $(".subscript").hide();
            $(".superscript").hide();
            $(".clearFormatting").hide();
            $(".blockquote").hide();
            $(".horizontalLine").hide();
            $(".codeBlock").hide();
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
            $(".fontSize").show();
            $(".textColorControl").show();
            $(".highlightColorControl").show();
            $(".undo").show();
            $(".redo").show();
            $(".bold").show();
            $(".italic").show();
            $(".underline").show();
            $(".strikethrough").show();
            $(".subscript").show();
            $(".superscript").show();
            $(".clearFormatting").show();
            $(".blockquote").show();
            $(".horizontalLine").show();
            $(".codeBlock").show();
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
            var selectedImg = getSelectedImageElement();
            if (selectedImg) {
              replacingImageNode = selectedImg;
            } else {
              replacingImageNode = null;
            }
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
                  if (replacingImageNode) {
                    replaceImageSrc(replacingImageNode, data.msg);
                  } else {
                    insertImage(data.msg, false);
                  }
                  $(".imgBrowser").hide();
                }
              },
              error: function (data) {
                console.log("i m here");
                console.log(data);
              },
            });
          });

          // Keyboard shortcut inside the editor iframe:
          // Cmd/Ctrl+Shift+K = Remove link.
          // Also: highlight active tag, handle Enter/Enter for paragraph, and exit caption on Enter.
          function focusCurrentLineAfterEnter() {
            setTimeout(function () {
              if (
                !iframe ||
                !iframe.contentWindow ||
                !iframe.contentWindow.document
              ) {
                return;
              }

              var doc = iframe.contentWindow.document;
              var sel = iframe.contentWindow.getSelection();
              if (!sel || sel.rangeCount === 0) {
                return;
              }

              $(".rte-active-block", doc).removeClass("rte-active-block");

              var node = sel.getRangeAt(0).startContainer;
              while (node && node.nodeType === 3) node = node.parentNode;
              while (
                node &&
                !/^(P|DIV|LI|BLOCKQUOTE|H1|H2|H3|H4|H5|H6|TD|TH|FIGCAPTION)$/.test(
                  node.tagName,
                )
              ) {
                node = node.parentNode;
              }

              if (!node) {
                return;
              }

              $(node).addClass("rte-active-block");

              try {
                if (typeof node.scrollIntoView === "function") {
                  node.scrollIntoView({ block: "nearest", inline: "nearest" });
                }
              } catch (err) {
                // no-op fallback
              }
            }, 0);
          }

          $(iframe.contentWindow.document)
            .off("keydown.rteShortcuts")
            .on("keydown.rteShortcuts", function (e) {
              if (textarea.is(":visible")) return;
              var modPressed = !!(e.ctrlKey || e.metaKey);
              var keyCode = e.keyCode || e.which;
              var key = (e.key || "").toLowerCase();
              // Remove link
              if (e.shiftKey && (key === "k" || keyCode === 75)) {
                e.preventDefault();
                performUnlink();
              }
              // Double Enter: escape current tag and start new paragraph
              if (key === "enter" || keyCode === 13) {
                var sel = iframe.contentWindow.getSelection();
                if (sel && sel.rangeCount > 0) {
                  var range = sel.getRangeAt(0);
                  var container = range.startContainer;
                  var parent =
                    container.nodeType === 1 ? container : container.parentNode;
                  // If inside figcaption, exit to new paragraph below figure
                  if (parent && parent.tagName === "FIGCAPTION") {
                    e.preventDefault();
                    var figure = $(parent).closest("figure").get(0);
                    if (figure) {
                      var p = iframe.contentWindow.document.createElement("p");
                      p.innerHTML = "<br>";
                      if (figure.nextSibling) {
                        figure.parentNode.insertBefore(p, figure.nextSibling);
                      } else {
                        figure.parentNode.appendChild(p);
                      }
                      var newRange =
                        iframe.contentWindow.document.createRange();
                      newRange.setStart(p, 0);
                      newRange.collapse(true);
                      sel.removeAllRanges();
                      sel.addRange(newRange);
                    }
                    focusCurrentLineAfterEnter();
                    return false;
                  }
                  // Double Enter: if previous key was Enter, insert new paragraph
                  if (
                    window.__rte_last_enter &&
                    Date.now() - window.__rte_last_enter < 600
                  ) {
                    e.preventDefault();
                    var p = iframe.contentWindow.document.createElement("p");
                    p.innerHTML = "<br>";
                    var node = parent;
                    while (
                      node &&
                      !/^(P|DIV|LI|BLOCKQUOTE|H1|H2|H3|H4|H5|H6)$/.test(
                        node.tagName,
                      )
                    ) {
                      node = node.parentNode;
                    }
                    if (node && node.parentNode) {
                      if (node.nextSibling) {
                        node.parentNode.insertBefore(p, node.nextSibling);
                      } else {
                        node.parentNode.appendChild(p);
                      }
                      var newRange =
                        iframe.contentWindow.document.createRange();
                      newRange.setStart(p, 0);
                      newRange.collapse(true);
                      sel.removeAllRanges();
                      sel.addRange(newRange);
                    }
                    window.__rte_last_enter = 0;
                    focusCurrentLineAfterEnter();
                    return false;
                  }
                  window.__rte_last_enter = Date.now();
                  focusCurrentLineAfterEnter();
                }
              }
            });

          // Highlight active tag/block
          $(iframe.contentWindow.document)
            .off("mouseup.rteHighlight keyup.rteHighlight click.rteHighlight")
            .on(
              "mouseup.rteHighlight keyup.rteHighlight click.rteHighlight",
              function () {
                $(
                  ".rte-active-block",
                  iframe.contentWindow.document,
                ).removeClass("rte-active-block");
                var sel = iframe.contentWindow.getSelection();
                if (sel && sel.rangeCount > 0) {
                  var node = sel.getRangeAt(0).startContainer;
                  while (node && node.nodeType === 3) node = node.parentNode;
                  while (
                    node &&
                    !/^(P|DIV|LI|BLOCKQUOTE|H1|H2|H3|H4|H5|H6|TD|TH|FIGCAPTION)$/.test(
                      node.tagName,
                    )
                  )
                    node = node.parentNode;
                  if (node) {
                    $(node).addClass("rte-active-block");
                  }

                  var $activeTable = node
                    ? $(node).closest("table.rte-table")
                    : $();
                  if ($activeTable.length) {
                    highlightTablePropertiesToolbar($activeTable);
                  } else {
                    removeHighlightTablePropertiesToolbar();
                  }
                } else {
                  removeHighlightTablePropertiesToolbar();
                }
              },
            );

          // Detect Paste to link

          $(iframe)
            .parents("form")
            .submit(function () {
              disableDesignMode();
            });

          // Add Table button
          var tableBtn = $(
            "<a class='editor-btn table' title='Insert table'><i class='fas fa-table'></i></a>",
          );
          tableBtn.insertAfter($(".embed", tb));
          tableBtn.on("click", function () {
            openTableDialog();
            return false;
          });

          // Inline Table Properties controls (hidden by default, live update)
          var tablePropsBtn = $(
            "<div class='table-properties-btn' style='display:none;'>" +
              "<div class='table-properties-box'>" +
              "<a class='editor-btn table-props-trigger' title='Table Properties'><i class='fas fa-table'></i></a>" +
              "<label class='editor-btn table-prop-field' title='Border thickness'>" +
              "<input type='number' min='0' step='1' class='rte-table-border-thickness' value='1' aria-label='Border thickness'>" +
              "</label>" +
              "<label class='editor-btn table-prop-field' title='Border type'>" +
              "<select class='rte-table-border-type' aria-label='Border type'>" +
              "<option value='solid'>Solid</option>" +
              "<option value='dashed'>Dashed</option>" +
              "<option value='dotted'>Dotted</option>" +
              "<option value='double'>Double</option>" +
              "<option value='none'>None</option>" +
              "</select>" +
              "</label>" +
              "<label class='editor-btn table-prop-field' title='Table radius'>" +
              "<input type='number' min='0' step='1' class='rte-table-radius' value='0' aria-label='Table radius'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableRowBgControl' title='Selected row background color'>" +
              "<i class='fas fa-fill-drip'></i>" +
              "<span class='color-preview' style='background:#ffffff;'></span>" +
              "<input type='color' class='rte-table-row-bg' value='#ffffff' aria-label='Selected row background color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableBgControl' title='Table background color'>" +
              "<i class='fas fa-paint-roller'></i>" +
              "<span class='color-preview' style='background:#ffffff;'></span>" +
              "<input type='color' class='rte-table-bg' value='#ffffff' aria-label='Table background color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableHeadBgControl' title='Header background color'>" +
              "<i class='fas fa-heading'></i>" +
              "<span class='color-preview' style='background:#f3f4f6;'></span>" +
              "<input type='color' class='rte-table-head-bg' value='#f3f4f6' aria-label='Header background color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableHeadTextControl' title='Header font color'>" +
              "<i class='fas fa-font'></i>" +
              "<span class='color-preview' style='background:#111111;'></span>" +
              "<input type='color' class='rte-table-head-color' value='#111111' aria-label='Header font color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableRowTextControl' title='Row font color'>" +
              "<i class='fas fa-text-height'></i>" +
              "<span class='color-preview' style='background:#111111;'></span>" +
              "<input type='color' class='rte-table-row-color' value='#111111' aria-label='Row font color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableAltRowBgControl' title='Alternate row background color'>" +
              "<i class='fas fa-grip-lines'></i>" +
              "<span class='color-preview' style='background:#f8fafc;'></span>" +
              "<input type='color' class='rte-table-alt-row-bg' value='#f8fafc' aria-label='Alternate row background color'>" +
              "</label>" +
              "<label class='editor-btn colorTool tableAltRowTextControl' title='Alternate row font color'>" +
              "<i class='fas fa-i-cursor'></i>" +
              "<span class='color-preview' style='background:#111111;'></span>" +
              "<input type='color' class='rte-table-alt-row-color' value='#111111' aria-label='Alternate row font color'>" +
              "</label>" +
              "<a class='editor-btn rte-table-add-row' title='Add row'><i class='fas fa-plus'></i>R</a>" +
              "<a class='editor-btn rte-table-del-row' title='Delete row'><i class='fas fa-minus'></i>R</a>" +
              "<a class='editor-btn rte-table-add-col' title='Add column'><i class='fas fa-plus'></i>C</a>" +
              "<a class='editor-btn rte-table-del-col' title='Delete column'><i class='fas fa-minus'></i>C</a>" +
              "</div>" +
              "</div>",
          );
          tablePropsBtn.insertAfter(tableBtn);

          tablePropsBtn.on("click", ".table-props-trigger", function (e) {
            e.preventDefault();
            var $table = getActiveTableFromToolbarState();
            if ($table.length) {
              syncInlineTableControls($table);
            }
          });

          // Apply custom toolbar tooltips to dynamically inserted table controls.
          $("a.editor-btn[title], label.editor-btn[title]", tablePropsBtn).each(
            function () {
              var tip = $(this).attr("title");
              if (!tip) {
                return;
              }
              $(this).attr("data-tooltip", tip);
              if (!$(this).attr("aria-label")) {
                $(this).attr("aria-label", tip);
              }
              $(this).removeAttr("title");
            },
          );

          $(
            ".rte-table-bg, .rte-table-row-bg, .rte-table-head-bg, .rte-table-head-color, .rte-table-row-color, .rte-table-alt-row-bg, .rte-table-alt-row-color",
            tablePropsBtn,
          ).on("input change", function () {
            updateColorToolPreview($(this));
          });

          var activeTableNode = null;

          function getActiveTableFromToolbarState() {
            if (activeTableNode) {
              var $activeTable = $(activeTableNode).closest("table.rte-table");
              if ($activeTable.length) {
                return $activeTable;
              }
              activeTableNode = null;
            }

            var sel = iframe.contentWindow.getSelection();
            var node =
              sel && sel.rangeCount > 0
                ? sel.getRangeAt(0).startContainer
                : null;
            return $(node).closest("table.rte-table");
          }

          function getSelectedTableRow($table) {
            if (!$table || !$table.length) {
              return $();
            }

            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              var node = sel.getRangeAt(0).startContainer;
              while (node && node.nodeType === 3) node = node.parentNode;
              var $row = $(node).closest("tr");
              if (
                $row.length &&
                $row.closest("table.rte-table").get(0) === $table.get(0)
              ) {
                return $row;
              }
            }

            return $table.find("tbody tr:first");
          }

          function getSelectedTableCell($table) {
            if (!$table || !$table.length) {
              return $();
            }

            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              var node = sel.getRangeAt(0).startContainer;
              while (node && node.nodeType === 3) node = node.parentNode;
              var $cell = $(node).closest("td, th");
              if (
                $cell.length &&
                $cell.closest("table.rte-table").get(0) === $table.get(0)
              ) {
                return $cell;
              }
            }

            return $table
              .find("tbody tr:first td:first, thead tr:first th:first")
              .first();
          }

          function refreshTableTheme($table) {
            if (!$table || !$table.length) {
              return;
            }
            syncInlineTableControls($table);
            applyInlineTableControls();
          }

          function syncInlineTableControls($table) {
            if (!$table || !$table.length) {
              return;
            }

            var borderWidthRaw =
              $table.find("tbody tr:first td").css("border-width") ||
              $table.css("border-width") ||
              "1px";
            var borderTypeRaw =
              $table.find("tbody tr:first td").css("border-style") ||
              $table.css("border-style") ||
              "solid";
            var radiusRaw = $table.css("border-radius") || "0px";
            var bg = normalizeColorToHex(
              $table.css("background-color"),
              "#ffffff",
            );
            var rowBg = normalizeColorToHex(
              $table.find("tbody tr:first td:first").css("background-color"),
              "#ffffff",
            );
            var headBg = normalizeColorToHex(
              $table.find("thead th:first").css("background-color"),
              "#f3f4f6",
            );
            var headColor = normalizeColorToHex(
              $table.find("thead th:first").css("color"),
              "#111111",
            );
            var rowColor = normalizeColorToHex(
              $table.find("tbody tr:first td:first").css("color"),
              "#111111",
            );
            var altRowBg = normalizeColorToHex(
              $table
                .find("tbody tr:nth-child(2) td:first")
                .css("background-color"),
              rowBg,
            );
            var altRowColor = normalizeColorToHex(
              $table.find("tbody tr:nth-child(2) td:first").css("color"),
              rowColor,
            );

            $(".rte-table-border-thickness", tablePropsBtn).val(
              parseInt(borderWidthRaw, 10) || 0,
            );
            $(".rte-table-border-type", tablePropsBtn).val(
              borderTypeRaw || "solid",
            );
            $(".rte-table-radius", tablePropsBtn).val(
              parseInt(radiusRaw, 10) || 0,
            );
            $(".rte-table-bg", tablePropsBtn).val(bg);
            $(".rte-table-row-bg", tablePropsBtn).val(rowBg);
            $(".rte-table-head-bg", tablePropsBtn).val(headBg);
            $(".rte-table-head-color", tablePropsBtn).val(headColor);
            $(".rte-table-row-color", tablePropsBtn).val(rowColor);
            $(".rte-table-alt-row-bg", tablePropsBtn).val(altRowBg);
            $(".rte-table-alt-row-color", tablePropsBtn).val(altRowColor);
            updateColorToolPreview($(".rte-table-row-bg", tablePropsBtn));
            updateColorToolPreview($(".rte-table-bg", tablePropsBtn));
            updateColorToolPreview($(".rte-table-head-bg", tablePropsBtn));
            updateColorToolPreview($(".rte-table-head-color", tablePropsBtn));
            updateColorToolPreview($(".rte-table-row-color", tablePropsBtn));
            updateColorToolPreview($(".rte-table-alt-row-bg", tablePropsBtn));
            updateColorToolPreview(
              $(".rte-table-alt-row-color", tablePropsBtn),
            );
          }

          function applyInlineTableControls() {
            var $table = getActiveTableFromToolbarState();
            if (!$table.length) {
              return;
            }

            var borderThickness =
              parseInt(
                $(".rte-table-border-thickness", tablePropsBtn).val(),
                10,
              ) || 0;
            var borderType =
              $(".rte-table-border-type", tablePropsBtn).val() || "solid";
            var radius =
              parseInt($(".rte-table-radius", tablePropsBtn).val(), 10) || 0;
            var bg = $(".rte-table-bg", tablePropsBtn).val() || "#ffffff";
            var rowBg =
              $(".rte-table-row-bg", tablePropsBtn).val() || "#ffffff";
            var headBg =
              $(".rte-table-head-bg", tablePropsBtn).val() || "#f3f4f6";
            var headColor =
              $(".rte-table-head-color", tablePropsBtn).val() || "#111111";
            var rowColor =
              $(".rte-table-row-color", tablePropsBtn).val() || "#111111";
            var altRowBg =
              $(".rte-table-alt-row-bg", tablePropsBtn).val() || rowBg;
            var altRowColor =
              $(".rte-table-alt-row-color", tablePropsBtn).val() || rowColor;

            $table.css({
              background: bg,
              borderRadius: radius + "px",
              overflow: radius > 0 ? "hidden" : "",
            });

            $table
              .find("thead th")
              .css({ "background-color": headBg, color: headColor });

            $table.find("tbody tr").each(function (index) {
              var isAlt = index % 2 === 1;
              $(this)
                .find("td")
                .css({
                  "background-color": isAlt ? altRowBg : rowBg,
                  color: isAlt ? altRowColor : rowColor,
                });
            });

            $table.find("th, td").css({
              "border-width": borderThickness + "px",
              "border-style": borderType,
            });
          }

          tablePropsBtn.on(
            "input change",
            ".rte-table-border-thickness, .rte-table-border-type, .rte-table-radius, .rte-table-row-bg, .rte-table-bg, .rte-table-head-bg, .rte-table-head-color, .rte-table-row-color, .rte-table-alt-row-bg, .rte-table-alt-row-color",
            function () {
              applyInlineTableControls();
            },
          );

          tablePropsBtn.on("click", ".rte-table-add-row", function (e) {
            e.preventDefault();
            var $table = getActiveTableFromToolbarState();
            var $cell = getSelectedTableCell($table);
            if (!$table.length || !$cell.length) {
              return;
            }

            var $row = $cell.closest("tr");
            var $newRow = $row.clone();
            $newRow.find("th").text("Header");
            $newRow.find("td").text("Cell");
            $row.after($newRow);
            refreshTableTheme($table);
          });

          tablePropsBtn.on("click", ".rte-table-del-row", function (e) {
            e.preventDefault();
            var $table = getActiveTableFromToolbarState();
            var $cell = getSelectedTableCell($table);
            if (!$table.length || !$cell.length) {
              return;
            }

            var $row = $cell.closest("tr");
            var isHeadRow = $row.closest("thead").length > 0;
            if (isHeadRow) {
              if ($table.find("thead tr").length > 1) {
                $row.remove();
              }
            } else {
              if ($table.find("tbody tr").length > 1) {
                $row.remove();
              }
            }
            refreshTableTheme($table);
          });

          tablePropsBtn.on("click", ".rte-table-add-col", function (e) {
            e.preventDefault();
            var $table = getActiveTableFromToolbarState();
            var $cell = getSelectedTableCell($table);
            if (!$table.length || !$cell.length) {
              return;
            }

            var colIdx = $cell.index();
            $table.find("tr").each(function () {
              var $row = $(this);
              var isHead = $row.closest("thead").length > 0;
              var tag = isHead ? "th" : "td";
              var $cells = $row.children("th, td");
              var cellText = isHead ? "Header" : "Cell";
              var $newCell = $("<" + tag + ">" + cellText + "</" + tag + ">");
              var refIdx = Math.max(0, Math.min(colIdx, $cells.length - 1));
              var $templateCell = $cells.eq(refIdx);

              if ($templateCell.length) {
                var templateStyle = $templateCell.attr("style");
                var templateClass = $templateCell.attr("class");
                if (typeof templateStyle !== "undefined") {
                  $newCell.attr("style", templateStyle);
                }
                if (templateClass) {
                  $newCell.attr("class", templateClass);
                }
              }

              if ($cells.length === 0 || colIdx >= $cells.length - 1) {
                $row.append($newCell);
              } else {
                $cells.eq(colIdx).after($newCell);
              }
            });
            refreshTableTheme($table);
          });

          tablePropsBtn.on("click", ".rte-table-del-col", function (e) {
            e.preventDefault();
            var $table = getActiveTableFromToolbarState();
            var $cell = getSelectedTableCell($table);
            if (!$table.length || !$cell.length) {
              return;
            }

            var colIdx = $cell.index();
            var maxCols = Math.max(
              1,
              $table.find("tr:first").children("th,td").length,
            );
            if (maxCols <= 1) {
              return;
            }

            $table.find("tr").each(function () {
              $(this).children("th, td").eq(colIdx).remove();
            });
            refreshTableTheme($table);
          });

          // Table context menu
          setTimeout(bindTableContextMenu, 500);

          // --- Toolbar Table Properties Button ---
          function highlightTablePropertiesToolbar($table) {
            var tb = getToolbarElement();
            if (!tb.length) return;
            activeTableNode = $table.get(0);
            syncInlineTableControls($table);
            tb.find(".table-properties-btn").show().addClass("is-active");
          }
          function removeHighlightTablePropertiesToolbar() {
            var tb = getToolbarElement();
            if (!tb.length) return;
            activeTableNode = null;
            tb.find(".table-properties-btn").hide().removeClass("is-active");
          }

          function syncTablePropertiesToolbarVisibility() {
            var sel = iframe.contentWindow.getSelection();
            if (sel && sel.rangeCount > 0) {
              var node = sel.getRangeAt(0).startContainer;
              while (node && node.nodeType === 3) node = node.parentNode;
              var $table = $(node).closest("table.rte-table");
              if ($table.length) {
                highlightTablePropertiesToolbar($table);
                return;
              }
            }

            removeHighlightTablePropertiesToolbar();
          }

          // --- Table selection detection for toolbar ---
          function bindTableToolbarToggle() {
            if (!iframe || !iframe.contentWindow) return;
            $(iframe.contentWindow.document)
              .off(
                "mouseup.rteTableToolbar keyup.rteTableToolbar click.rteTableToolbar selectionchange.rteTableToolbar",
              )
              .on(
                "mouseup.rteTableToolbar keyup.rteTableToolbar click.rteTableToolbar selectionchange.rteTableToolbar",
                function () {
                  syncTablePropertiesToolbarVisibility();
                },
              );

            removeHighlightTablePropertiesToolbar();
          }
          setTimeout(bindTableToolbarToggle, 500);

          return tb;
        }

        function imageList(imageType, name = "") {
          $.ajax({
            type: "GET",
            url: window.location.origin + "/api/media/",
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
                  if (replacingImageNode) {
                    replaceImageSrc(replacingImageNode, value);
                  } else {
                    insertImage(value, false);
                  }
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

        function insertImage(src, promptForMeta) {
          formatText("insertImage", src);
          setTimeout(function () {
            var insertedImage = findInsertedImageBySrc(src);

            if (insertedImage) {
              selectedEditableElement = insertedImage;
              updateSelectionTools(insertedImage);
              if (promptForMeta !== false) {
                openImageMetaDialog(
                  insertedImage,
                  "Image Details",
                  "Insert Image",
                );
              }
              return;
            }

            updateSelectionTools();
          }, 0);
          return false;
        }

        function replaceImageSrc(imageNode, newSrc) {
          if (!imageNode || imageNode.tagName !== "IMG") {
            return;
          }
          imageNode.setAttribute("src", newSrc);
          var figure = getExistingMediaFigure(imageNode);
          updateSelectionTools(figure || imageNode);
          replacingImageNode = null;
        }

        function formatText(command, option) {
          iframe.contentWindow.focus();
          try {
            iframe.contentWindow.document.execCommand(command, false, option);
          } catch (e) {
            console.log(e);
          }
          iframe.contentWindow.focus();
          saveSelectionRange();
          syncToolbarButtonStates();
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

        // Add CSS for highlighting active block/tag and table style
        var style = document.createElement("style");
        style.textContent =
          ".rte-active-block { outline: 1px solid #93c5fd !important; background: #f8fbff !important; box-shadow: inset 0 0 0 1px rgba(147,197,253,.28); } " +
          "table.rte-table th, table.rte-table td { transition: background 0.2s; } " +
          "table.rte-table th:hover, table.rte-table td:hover { background: #e0e7ef !important; } " +
          "figcaption { transition: background 0.2s; } " +
          ".table-properties-btn { margin-left: 6px; } " +
          ".table-properties-btn .table-properties-box { display:flex; align-items:center; gap:6px; padding:3px 6px; border:1px solid #d5dbe3; border-radius:8px; background:#f8fafc; } " +
          ".table-properties-btn .editor-btn.table-prop-field { min-width:52px; display:flex; align-items:center; justify-content:center; } " +
          ".table-properties-btn .editor-btn.table-prop-field input, .table-properties-btn .editor-btn.table-prop-field select { width:100%; min-width:44px; border:0; background:transparent; outline:none; font-size:12px; color:#1f2937; } " +
          ".table-properties-btn .colorTool { min-width:34px; } " +
          ".fullscreen-editor.setFullScreen { position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background: #f9f9f9 !important; padding: 15px !important; box-sizing: border-box !important; margin: 0 !important; display: flex !important; flex-direction: column !important; } " +
          ".fullscreen-editor.setFullScreen .editor-menu { flex: 0 0 auto !important; margin-bottom: 10px !important; } " +
          ".fullscreen-editor.setFullScreen iframe, .fullscreen-editor.setFullScreen textarea.editor { flex: 1 1 auto !important; height: calc(100vh - 80px) !important; max-height: none !important; }";
        document.head.appendChild(style);

        // publish all post where editor is enable

        function togglePostActionButtons(show) {
          var method = show ? "show" : "hide";
          $("#publishPost")[method]();
          $("#updatePost")[method]();
          $("#draftPost")[method]();
          $("#schedulePost")[method]();
        }

        function getPostFormData(actionName) {
          disableDesignMode();
          var data = new FormData(document.getElementById("formData"));
          if (typeof data.delete === "function") {
            data.delete("action");
            data.delete("featurePost");
          }
          var $featurePost = jQuery('#formData input[name="featurePost"]');
          if ($featurePost.length) {
            data.append("featurePost", $featurePost.is(":checked") ? 1 : 0);
          }
          data.append("action", actionName);
          return data;
        }

        function submitPostAction(actionName, triggerSelector) {
          togglePostActionButtons(false);

          if (actionName === "schedule") {
            var scheduleAt = $.trim($("#scheduleAt").val());
            if (!scheduleAt) {
              togglePostActionButtons(true);
              popUpMessage(
                "center",
                "Please choose schedule date and time",
                "warning",
              );
              return;
            }

            var scheduleAtDate = new Date(scheduleAt);
            if (
              isNaN(scheduleAtDate.getTime()) ||
              scheduleAtDate.getTime() <= Date.now()
            ) {
              togglePostActionButtons(true);
              popUpMessage(
                "center",
                "Please choose a future schedule date and time",
                "warning",
              );
              return;
            }
          }

          var data = getPostFormData(actionName);

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
                  window.__bypassUnsavedChangesPrompt = true;
                  window.location.href = data.redirect;
                }, 1600);
              } else {
                popUpMessage("center", data.msg, data.status);
              }
              togglePostActionButtons(true);
            },
            error: function (data) {
              console.log(data);
              togglePostActionButtons(true);
            },
          });
        }

        $("#publishPost").click(function () {
          submitPostAction("publish", "#publishPost");
        });

        // Update all post where editor is enable

        $("#updatePost").click(function () {
          submitPostAction("update", "#updatePost");
        });

        $("#draftPost").click(function () {
          submitPostAction("draft", "#draftPost");
        });

        // Schedule Post

        $("#schedulePost").click(function () {
          submitPostAction("schedule", "#schedulePost");
        });

        // Keep schedule datetime picker selection-only.
        $(document)
          .off(
            "keydown.rteSchedule paste.rteSchedule drop.rteSchedule",
            "#scheduleAt",
          )
          .on(
            "keydown.rteSchedule paste.rteSchedule drop.rteSchedule",
            "#scheduleAt",
            function (e) {
              e.preventDefault();
            },
          );

        $(document)
          .off("focus.rteSchedule click.rteSchedule", "#scheduleAt")
          .on(
            "focus.rteSchedule click.rteSchedule",
            "#scheduleAt",
            function () {
              if (
                typeof this.showPicker === "function" &&
                !this.readOnly &&
                !this.disabled
              ) {
                try {
                  this.showPicker();
                } catch (err) {
                  // Ignore browsers/states where picker invocation is blocked.
                }
              }
            },
          );
      }); //return this.each
    }; // rte
  } // if

  $(".editor").rte({});
})(jQuery);
