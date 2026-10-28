/**
 * V3 Modern Rich Text Editor
 * Path: /app/dashboard/v3/default/desktop/assets/js/v3-editor.js
 * Designed exclusively for Aleya V3 Dashboard.
 * Uses native Flaticon (fi fi-rr-*) icons, modern glassmorphic styling, and two-way data binding.
 */

(function (window, $) {
  "use strict";

  const V3EditorDefaults = {
    minHeight: 340,
    maxHeight: null,
    placeholder: "Start writing your content here...",
    toolbar: [
      "history",
      "format",
      "size",
      "styling",
      "colors",
      "alignment",
      "lists",
      "inserts",
      "tools"
    ]
  };

  function V3Editor(textarea, options) {
    this.$textarea = $(textarea);
    this.opts = $.extend({}, V3EditorDefaults, options);
    this.id = this.$textarea.attr("id") || "v3_editor_" + Math.random().toString(36).substring(2, 9);
    this.isSourceMode = false;
    this.isFullscreen = false;
    this.savedRange = null;   // stores selection before toolbar button steals focus
    this.isExecuting = false;
    this.init();
  }

  V3Editor.prototype = {
    init: function () {
      const self = this;

      // Remove any existing editor container for this element
      if (this.$textarea.next(".v3-wysiwyg-wrapper").length) {
        this.$textarea.next(".v3-wysiwyg-wrapper").remove();
      }

      this.$textarea.hide();

      // Create container
      this.$wrapper = $(`
        <div class="v3-wysiwyg-wrapper" id="wrap_${this.id}">
          <div class="v3-editor-toolbar" id="tb_${this.id}"></div>
          <div class="v3-editor-body-wrap">
            <div class="v3-editor-content" id="content_${this.id}" contenteditable="true" spellcheck="true" data-placeholder="${self.opts.placeholder}"></div>
            <textarea class="v3-editor-source-view" id="source_${this.id}" style="display: none;"></textarea>
          </div>
        </div>
      `);

      this.$toolbar = this.$wrapper.find(".v3-editor-toolbar");
      this.$content = this.$wrapper.find(".v3-editor-content");
      this.$source = this.$wrapper.find(".v3-editor-source-view");

      // Apply initial content
      const initialHtml = this.$textarea.val() || "";
      this.$content.html(initialHtml);
      this.$source.val(initialHtml);

      // Render Toolbar Items
      this.buildToolbar();

      // Insert into DOM after textarea
      this.$textarea.after(this.$wrapper);

      // Bind all editor events
      this.bindEvents();
    },

    buildToolbar: function () {
      const tb = this.$toolbar;
      tb.empty();

      // Group: History (Undo/Redo)
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn" data-command="undo" title="Undo (Ctrl+Z)"><i class="fi fi-rr-undo"></i></button>
          <button type="button" class="v3-tb-btn" data-command="redo" title="Redo (Ctrl+Y)"><i class="fi fi-rr-redo"></i></button>
        </div>
      `);

      // Group: Typography Format & Size
      tb.append(`
        <div class="v3-tb-group">
          <select class="v3-tb-select v3-format-select" title="Text Style">
            <option value="p">Paragraph</option>
            <option value="h1">Heading 1</option>
            <option value="h2">Heading 2</option>
            <option value="h3">Heading 3</option>
            <option value="h4">Heading 4</option>
            <option value="blockquote">Quote</option>
            <option value="pre">Code Block</option>
          </select>
          <select class="v3-tb-select v3-size-select" title="Font Size">
            <option value="">Size</option>
            <option value="1">12px</option>
            <option value="2">14px</option>
            <option value="3" selected>16px</option>
            <option value="4">18px</option>
            <option value="5">24px</option>
            <option value="6">32px</option>
          </select>
        </div>
      `);

      // Group: Text Styling
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn" data-command="bold" title="Bold (Ctrl+B)"><i class="fi fi-rr-bold"></i></button>
          <button type="button" class="v3-tb-btn" data-command="italic" title="Italic (Ctrl+I)"><i class="fi fi-rr-italic"></i></button>
          <button type="button" class="v3-tb-btn" data-command="underline" title="Underline (Ctrl+U)"><i class="fi fi-rr-underline"></i></button>
          <button type="button" class="v3-tb-btn" data-command="strikeThrough" title="Strikethrough"><i class="fi fi-rr-strikethrough"></i></button>
          <button type="button" class="v3-tb-btn" data-command="removeFormat" title="Clear Formatting"><i class="fi fi-rr-eraser"></i></button>
        </div>
      `);

      // Group: Colors
      tb.append(`
        <div class="v3-tb-group">
          <label class="v3-tb-color-picker" title="Text Color">
            <i class="fi fi-rr-text"></i>
            <span class="v3-color-indicator" id="ind_text_${this.id}" style="background-color: #1e293b;"></span>
            <input type="color" class="v3-color-input" data-color-type="foreColor" value="#1e293b">
          </label>
          <label class="v3-tb-color-picker" title="Highlight Background">
            <i class="fi fi-rr-brush"></i>
            <span class="v3-color-indicator" id="ind_bg_${this.id}" style="background-color: #fef08a;"></span>
            <input type="color" class="v3-color-input" data-color-type="hiliteColor" value="#fef08a">
          </label>
        </div>
      `);

      // Group: Alignment
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn" data-command="justifyLeft" title="Align Left"><i class="ri-align-left"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyCenter" title="Align Center"><i class="ri-align-center"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyRight" title="Align Right"><i class="ri-align-right"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyFull" title="Justify"><i class="ri-align-justify"></i></button>
        </div>
      `);

      // Group: Lists & Indents
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn" data-command="insertUnorderedList" title="Bullet List"><i class="ri-list-unordered"></i></button>
          <button type="button" class="v3-tb-btn" data-command="insertOrderedList" title="Numbered List"><i class="ri-list-ordered"></i></button>
          <button type="button" class="v3-tb-btn" data-command="indent" title="Indent (Tab)"><i class="ri-indent-increase"></i></button>
          <button type="button" class="v3-tb-btn" data-command="outdent" title="Outdent (Shift+Tab)"><i class="ri-indent-decrease"></i></button>
        </div>
      `);

      // Group: Inserts (Link, Table, Line)
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn v3-btn-link" title="Insert Link"><i class="ri-link"></i></button>
          <button type="button" class="v3-tb-btn" data-command="unlink" title="Remove Link"><i class="ri-link-unlink"></i></button>
          <button type="button" class="v3-tb-btn v3-btn-table" title="Insert Table"><i class="fi fi-rr-table"></i></button>
          <button type="button" class="v3-tb-btn" data-command="insertHorizontalRule" title="Insert Horizontal Line"><i class="fi fi-rr-minus"></i></button>
        </div>
      `);

      // Group: View Tools & Fullscreen
      tb.append(`
        <div class="v3-tb-group" style="margin-left: auto;">
          <button type="button" class="v3-tb-btn v3-btn-source" title="Toggle HTML Source"><i class="fi fi-rr-code-simple"></i></button>
          <button type="button" class="v3-tb-btn v3-btn-fullscreen" title="Toggle Fullscreen"><i class="fi fi-rr-expand"></i></button>
        </div>
      `);
    },

    bindEvents: function () {
      const self = this;

      // 1. Standard Commands: Save selection and prevent button from stealing focus
      this.$toolbar.on("mousedown", ".v3-tb-btn", function (e) {
        self.saveSelection();
        e.preventDefault();
      });

      this.$toolbar.on("click", ".v3-tb-btn[data-command]", function (e) {
        e.preventDefault();
        const cmd = $(this).data("command");
        self.execCommand(cmd);
        self.updateActiveButtons();
      });

      // 2. Format Select (p, h1, h2, etc.)
      this.$toolbar.on("mousedown focus", ".v3-format-select", function () {
        self.saveSelection();
      });

      this.$toolbar.on("change", ".v3-format-select", function () {
        const val = $(this).val();
        if (val) {
          self.formatBlock(val);
        }
      });

      // 3. Font Size Select
      this.$toolbar.on("mousedown focus", ".v3-size-select", function () {
        self.saveSelection();
      });

      this.$toolbar.on("change", ".v3-size-select", function () {
        const size = $(this).val();
        if (size) {
          self.execCommand("fontSize", size);
        }
      });

      // 4. Color Pickers
      this.$toolbar.on("input change", ".v3-color-input", function () {
        const color = $(this).val();
        const colorType = $(this).data("color-type");
        const $ind = $(this).siblings(".v3-color-indicator");
        $ind.css("background-color", color);
        self.execCommand(colorType, color);
      });

      // 5. Insert Link Modal
      this.$toolbar.on("click", ".v3-btn-link", function (e) {
        e.preventDefault();
        self.openLinkModal();
      });

      // 6. Insert Table Modal
      this.$toolbar.on("click", ".v3-btn-table", function (e) {
        e.preventDefault();
        self.openTableModal();
      });

      // 7. HTML Source Toggle
      this.$toolbar.on("click", ".v3-btn-source", function (e) {
        e.preventDefault();
        self.toggleSourceMode();
      });

      // 8. Fullscreen Toggle
      this.$toolbar.on("click", ".v3-btn-fullscreen", function (e) {
        e.preventDefault();
        self.toggleFullscreen();
      });

      // 9. Input, Keyup, Mouseup events in content area
      this.$content.on("input keyup mouseup paste blur", function () {
        self.saveSelection();
        self.syncToTextarea();
        self.updateActiveButtons();
      });

      // Tab key indent/outdent inside list items
      this.$content.on("keydown", function (e) {
        if (e.key === "Tab") {
          const sel = window.getSelection();
          if (sel && sel.anchorNode) {
            const $li = $(sel.anchorNode).closest("li");
            if ($li.length && $.contains(self.$content[0], $li[0])) {
              e.preventDefault();
              self.execCommand(e.shiftKey ? "outdent" : "indent");
            }
          }
        }
      });

      // Source textarea input
      this.$source.on("input keyup paste", function () {
        const html = $(this).val();
        self.$content.html(html);
        self.$textarea.val(html).trigger("input").trigger("change");
      });

      // Selection change updates active button states and cached range
      $(document).on("selectionchange.v3editor_" + self.id, function () {
        if (self.isExecuting) return;
        const sel = window.getSelection();
        if (sel && sel.anchorNode && ($.contains(self.$content[0], sel.anchorNode) || self.$content[0] === sel.anchorNode)) {
          self.saveSelection();
          self.updateActiveButtons();
        }
      });
    },

    execCommand: function (command, value) {
      if (this.isSourceMode) return;

      if (command === "insertUnorderedList" || command === "insertOrderedList") {
        this.applyList(command === "insertOrderedList" ? "ol" : "ul");
        return;
      }

      this.isExecuting = true;
      try {
        if (document.activeElement !== this.$content[0]) {
          this.$content[0].focus();
        }
        this.restoreSelection();
        document.execCommand(command, false, value !== undefined ? value : null);
      } catch (err) {
        console.warn("V3Editor execCommand error:", err);
      } finally {
        this.isExecuting = false;
      }

      this.saveSelection();
      this.syncToTextarea();
      this.updateActiveButtons();
    },

    applyList: function (listType) {
      if (this.isSourceMode) return;
      this.isExecuting = true;

      try {
        if (document.activeElement !== this.$content[0]) {
          this.$content[0].focus();
        }
        this.restoreSelection();

        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) {
          this.isExecuting = false;
          return;
        }

        const range = sel.getRangeAt(0);

        // 1. If editor is empty, insert fresh list
        const text = this.$content.text().trim();
        const html = this.$content.html().trim();
        if (!text && (!html || html === "<br>" || html === "<p><br></p>")) {
          this.$content.html(`<${listType}><li><br></li></${listType}>`);
          const li = this.$content.find("li")[0];
          if (li) {
            const newRange = document.createRange();
            newRange.setStart(li, 0);
            newRange.collapse(true);
            sel.removeAllRanges();
            sel.addRange(newRange);
            this.savedRange = newRange.cloneRange();
          }
          this.syncToTextarea();
          this.updateActiveButtons();
          return;
        }

        // 2. Check if cursor/selection is already inside an existing list
        let node = range.commonAncestorContainer;
        if (node.nodeType === Node.TEXT_NODE) node = node.parentNode;
        const $closestList = $(node).closest("ul, ol");
        const insideEditor = $closestList.length && ($.contains(this.$content[0], $closestList[0]) || this.$content[0] === $closestList[0]);

        if (insideEditor) {
          document.execCommand(listType === "ol" ? "insertOrderedList" : "insertUnorderedList", false, null);
          this.saveSelection();
          this.syncToTextarea();
          this.updateActiveButtons();
          return;
        }

        // 3. Multi-line selection handling
        if (!range.collapsed) {
          const fragment = range.cloneContents();
          const temp = document.createElement("div");
          temp.appendChild(fragment);

          const fragmentHtml = temp.innerHTML;
          const hasBreaks = /<br\s*\/?>/i.test(fragmentHtml) || /<\/(p|div|li|h[1-6])>/i.test(fragmentHtml) || /\r?\n/.test(temp.textContent || "");

          if (hasBreaks) {
            let cleaned = fragmentHtml
              .replace(/<br\s*\/?>/gi, "\n")
              .replace(/<\/(p|div|li|h[1-6]|tr)>/gi, "\n")
              .replace(/<(p|div|li|h[1-6]|tr)[^>]*>/gi, "");

            let lines = cleaned
              .split(/\r?\n/)
              .map(l => l.trim().replace(/^[\*\-\•\–\—]\s*/, "").replace(/^\d+[\.\)]\s*/, ""))
              .filter(l => l.length > 0 && l !== "<br>");

            if (lines.length > 1) {
              range.deleteContents();

              const listEl = document.createElement(listType);
              lines.forEach(lineText => {
                const li = document.createElement("li");
                li.innerHTML = lineText;
                listEl.appendChild(li);
              });

              range.insertNode(listEl);

              // Lift list out of parent <p> or <div> if wrapped
              const parent = listEl.parentNode;
              if (parent && parent !== this.$content[0]) {
                if (parent.tagName === "P" || parent.tagName === "DIV") {
                  parent.parentNode.insertBefore(listEl, parent);
                  if (!parent.textContent.trim() && !parent.querySelector("img, input, table")) {
                    parent.parentNode.removeChild(parent);
                  }
                }
              }

              // Place caret at end of the last list item
              const lastLi = $(listEl).find("li").last()[0];
              if (lastLi) {
                const newRange = document.createRange();
                newRange.selectNodeContents(lastLi);
                newRange.collapse(false);
                sel.removeAllRanges();
                sel.addRange(newRange);
                this.savedRange = newRange.cloneRange();
              }

              this.syncToTextarea();
              this.updateActiveButtons();
              return;
            }
          }
        }

        // 4. Default: invoke native browser command
        document.execCommand(listType === "ol" ? "insertOrderedList" : "insertUnorderedList", false, null);
        this.saveSelection();
        this.syncToTextarea();
        this.updateActiveButtons();
      } catch (err) {
        console.warn("applyList error:", err);
      } finally {
        this.isExecuting = false;
      }
    },

    saveSelection: function () {
      const sel = window.getSelection();
      if (sel && sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        if (
          $.contains(this.$content[0], range.commonAncestorContainer) ||
          this.$content[0] === range.commonAncestorContainer
        ) {
          this.savedRange = range.cloneRange();
        }
      }
    },

    restoreSelection: function () {
      const sel = window.getSelection();
      if (!sel) return;

      if (this.savedRange) {
        try {
          if (
            $.contains(this.$content[0], this.savedRange.commonAncestorContainer) ||
            this.$content[0] === this.savedRange.commonAncestorContainer
          ) {
            sel.removeAllRanges();
            sel.addRange(this.savedRange);
            return;
          }
        } catch (e) {}
      }

      // If no valid saved range, place caret at the end of content
      try {
        const range = document.createRange();
        range.selectNodeContents(this.$content[0]);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
        this.savedRange = range.cloneRange();
      } catch (e) {}
    },

    syncToTextarea: function () {
      const html = this.$content.html();
      this.$source.val(html);
      this.$textarea.val(html).trigger("input").trigger("change");
    },

    formatBlock: function (tag) {
      if (this.isSourceMode) return;
      this.isExecuting = true;

      try {
        if (document.activeElement !== this.$content[0]) {
          this.$content[0].focus();
        }
        this.restoreSelection();

        const cleanTag = String(tag).toLowerCase().replace(/[<>]/g, "");

        let success = false;
        try {
          success = document.execCommand("formatBlock", false, `<${cleanTag}>`);
        } catch (e) {}

        if (!success) {
          try {
            document.execCommand("formatBlock", false, cleanTag);
          } catch (e) {}
        }
      } catch (err) {
        console.warn("formatBlock error:", err);
      } finally {
        this.isExecuting = false;
      }

      this.saveSelection();
      this.syncToTextarea();
      this.updateActiveButtons();
    },

    updateActiveButtons: function () {
      const self = this;
      const commands = [
        "bold", "italic", "underline", "strikeThrough",
        "justifyLeft", "justifyCenter", "justifyRight", "justifyFull",
        "insertUnorderedList", "insertOrderedList"
      ];

      commands.forEach(function (cmd) {
        let isActive = false;
        try {
          isActive = document.queryCommandState ? document.queryCommandState(cmd) : false;
        } catch (e) {}
        self.$toolbar.find(`.v3-tb-btn[data-command='${cmd}']`).toggleClass("active", !!isActive);
      });

      // Synchronize format select dropdown with current block element
      try {
        let blockVal = "p";
        let formatName = "";
        try {
          formatName = document.queryCommandValue ? document.queryCommandValue("formatBlock") : "";
        } catch (e) {}

        if (formatName) {
          blockVal = String(formatName).toLowerCase().replace(/[<>]/g, "");
        } else {
          const sel = window.getSelection();
          if (sel && sel.rangeCount > 0) {
            let node = sel.anchorNode;
            if (node) {
              if (node.nodeType === Node.TEXT_NODE) node = node.parentNode;
              const $block = $(node).closest("p, h1, h2, h3, h4, h5, h6, blockquote, pre", self.$content[0]);
              if ($block.length && ($.contains(self.$content[0], $block[0]) || self.$content[0] === $block[0])) {
                blockVal = $block[0].tagName.toLowerCase();
              }
            }
          }
        }

        const $formatSelect = self.$toolbar.find(".v3-format-select");
        if ($formatSelect.length && !document.activeElement.classList.contains("v3-format-select")) {
          if ($formatSelect.find(`option[value='${blockVal}']`).length) {
            $formatSelect.val(blockVal);
          } else {
            $formatSelect.val("p");
          }
        }
      } catch (e) {}
    },

    openLinkModal: function () {
      const self = this;
      const selectedText = window.getSelection().toString();

      if (window.Swal && typeof window.Swal.fire === "function") {
        window.Swal.fire({
          title: "Insert Link",
          html: `
            <div style="text-align: left; margin-top: 10px;">
              <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Link URL (e.g. https://...)</label>
              <input id="swal_link_url" class="v3-form-input" placeholder="https://example.com" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px;">
              
              <label style="font-size: 12px; font-weight: 600; color: #475569; margin: 12px 0 4px 0; display: block;">Display Text</label>
              <input id="swal_link_text" class="v3-form-input" value="${self.escapeHtml(selectedText)}" placeholder="Click here" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px;">
            </div>
          `,
          focusConfirm: false,
          showCancelButton: true,
          confirmButtonColor: "#4f46e5",
          confirmButtonText: "Insert Link",
          preConfirm: () => {
            const url = document.getElementById("swal_link_url").value.trim();
            const text = document.getElementById("swal_link_text").value.trim();
            if (!url) {
              window.Swal.showValidationMessage("Please enter a valid URL.");
              return false;
            }
            return { url: url, text: text || url };
          }
        }).then((result) => {
          if (result.isConfirmed && result.value) {
            self.$content.focus();
            let finalUrl = result.value.url;
            if (!/^https?:\/\//i.test(finalUrl) && !finalUrl.startsWith("/")) {
              finalUrl = "https://" + finalUrl;
            }
            const linkHtml = `<a href="${finalUrl}" target="_blank" rel="noopener noreferrer">${self.escapeHtml(result.value.text)}</a>`;
            self.execCommand("insertHTML", linkHtml);
          }
        });
      } else {
        const url = prompt("Enter link URL (e.g. https://...):", "https://");
        if (url) {
          self.execCommand("createLink", url);
        }
      }
    },

    openTableModal: function () {
      const self = this;

      if (window.Swal && typeof window.Swal.fire === "function") {
        window.Swal.fire({
          title: "Insert Table",
          html: `
            <div style="display: flex; gap: 14px; margin-top: 10px; text-align: left;">
              <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Rows</label>
                <input type="number" id="swal_tbl_rows" class="v3-form-input" value="3" min="1" max="20" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px;">
              </div>
              <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Columns</label>
                <input type="number" id="swal_tbl_cols" class="v3-form-input" value="3" min="1" max="10" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px;">
              </div>
            </div>
          `,
          focusConfirm: false,
          showCancelButton: true,
          confirmButtonColor: "#4f46e5",
          confirmButtonText: "Create Table",
          preConfirm: () => {
            const rows = parseInt(document.getElementById("swal_tbl_rows").value, 10) || 3;
            const cols = parseInt(document.getElementById("swal_tbl_cols").value, 10) || 3;
            return { rows: Math.min(20, Math.max(1, rows)), cols: Math.min(10, Math.max(1, cols)) };
          }
        }).then((result) => {
          if (result.isConfirmed && result.value) {
            let tableHtml = `<table class="v3-rich-table" style="width: 100%; border-collapse: collapse; margin: 12px 0;"><thead><tr>`;
            for (let c = 1; c <= result.value.cols; c++) {
              tableHtml += `<th style="border: 1px solid #cbd5e1; padding: 8px 12px; background: #f8fafc; font-weight: 600;">Header ${c}</th>`;
            }
            tableHtml += `</tr></thead><tbody>`;
            for (let r = 1; r <= result.value.rows; r++) {
              tableHtml += `<tr>`;
              for (let c = 1; c <= result.value.cols; c++) {
                tableHtml += `<td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Cell (${r}, ${c})</td>`;
              }
              tableHtml += `</tr>`;
            }
            tableHtml += `</tbody></table><p><br></p>`;
            self.execCommand("insertHTML", tableHtml);
          }
        });
      }
    },

    toggleSourceMode: function () {
      this.isSourceMode = !this.isSourceMode;
      const $btn = this.$toolbar.find(".v3-btn-source");

      if (this.isSourceMode) {
        this.$source.val(this.$content.html());
        this.$content.hide();
        this.$source.show().focus();
        $btn.addClass("active").attr("title", "Back to Visual Editor");
      } else {
        this.$content.html(this.$source.val());
        this.$source.hide();
        this.$content.show().focus();
        $btn.removeClass("active").attr("title", "Toggle HTML Source");
        this.syncToTextarea();
      }
    },

    toggleFullscreen: function () {
      this.isFullscreen = !this.isFullscreen;
      const $btn = this.$toolbar.find(".v3-btn-fullscreen");
      this.$wrapper.toggleClass("v3-editor-fullscreen", this.isFullscreen);
      $("body").toggleClass("v3-editor-fs-locked", this.isFullscreen);

      if (this.isFullscreen) {
        $btn.find("i").attr("class", "fi fi-rr-compress");
        $btn.attr("title", "Exit Fullscreen (Esc)");
      } else {
        $btn.find("i").attr("class", "fi fi-rr-expand");
        $btn.attr("title", "Toggle Fullscreen");
      }
    },

    setContent: function (html) {
      const content = html || "";
      this.$content.html(content);
      this.$source.val(content);
      this.$textarea.val(content).trigger("input").trigger("change");
    },

    getContent: function () {
      if (this.isSourceMode) {
        return this.$source.val();
      }
      return this.$content.html();
    },

    escapeHtml: function (text) {
      if (!text) return "";
      return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  };

  // jQuery Plugin registration
  $.fn.v3Editor = function (options) {
    return this.each(function () {
      if (!$.data(this, "v3Editor")) {
        $.data(this, "v3Editor", new V3Editor(this, options));
      }
    });
  };

  // Backward compatible $.fn.rte alias
  $.fn.rte = function (options) {
    return this.v3Editor(options);
  };

  // ESC key exits fullscreen
  $(document).on("keydown.v3EditorFs", function (e) {
    if (e.key === "Escape") {
      $(".v3-wysiwyg-wrapper.v3-editor-fullscreen").each(function () {
        const editor = $(this).prev("textarea").data("v3Editor");
        if (editor && editor.isFullscreen) {
          editor.toggleFullscreen();
        }
      });
    }
  });

  window.V3Editor = V3Editor;
})(window, jQuery);
