/**
 * V3 Modern Rich Text Editor
 * Path: /app/dashboard/v3/default/desktop/assets/js/v3-editor.js
 * Designed exclusively for ISW-App V3 Dashboard.
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
          <button type="button" class="v3-tb-btn" data-command="justifyLeft" title="Align Left"><i class="fi fi-rr-align-left"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyCenter" title="Align Center"><i class="fi fi-rr-align-center"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyRight" title="Align Right"><i class="fi fi-rr-align-right"></i></button>
          <button type="button" class="v3-tb-btn" data-command="justifyFull" title="Justify"><i class="fi fi-rr-align-justify"></i></button>
        </div>
      `);

      // Group: Lists & Indents
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn" data-command="insertUnorderedList" title="Bullet List"><i class="fi fi-rr-list"></i></button>
          <button type="button" class="v3-tb-btn" data-command="insertOrderedList" title="Numbered List"><i class="fi fi-rr-list-number"></i></button>
        </div>
      `);

      // Group: Inserts (Link, Table, Line)
      tb.append(`
        <div class="v3-tb-group">
          <button type="button" class="v3-tb-btn v3-btn-link" title="Insert Link"><i class="fi fi-rr-link-alt"></i></button>
          <button type="button" class="v3-tb-btn" data-command="unlink" title="Remove Link"><i class="fi fi-rr-unlink"></i></button>
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

      // 1. Standard Commands
      this.$toolbar.on("click", ".v3-tb-btn[data-command]", function (e) {
        e.preventDefault();
        const cmd = $(this).data("command");
        self.execCommand(cmd);
        self.updateActiveButtons();
      });

      // 2. Format Select (p, h1, h2, etc.)
      this.$toolbar.on("change", ".v3-format-select", function () {
        const val = $(this).val();
        if (val === "p" || val.startsWith("h")) {
          self.execCommand("formatBlock", `<${val}>`);
        } else if (val === "blockquote") {
          self.execCommand("formatBlock", "<blockquote>");
        } else if (val === "pre") {
          self.execCommand("formatBlock", "<pre>");
        }
      });

      // 3. Font Size Select
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

      // 9. Input & Keyup events in content area
      this.$content.on("input keyup paste blur", function () {
        self.syncToTextarea();
        self.updateActiveButtons();
      });

      // Source textarea input
      this.$source.on("input keyup paste", function () {
        const html = $(this).val();
        self.$content.html(html);
        self.$textarea.val(html).trigger("input").trigger("change");
      });

      // Selection change updates active button states
      $(document).on("selectionchange.v3editor_" + self.id, function () {
        if ($.contains(self.$content[0], window.getSelection().anchorNode)) {
          self.updateActiveButtons();
        }
      });
    },

    execCommand: function (command, value) {
      this.$content.focus();
      document.execCommand(command, false, value || null);
      this.syncToTextarea();
    },

    syncToTextarea: function () {
      const html = this.$content.html();
      this.$source.val(html);
      this.$textarea.val(html).trigger("input").trigger("change");
    },

    updateActiveButtons: function () {
      const self = this;
      const commands = ["bold", "italic", "underline", "strikeThrough", "justifyLeft", "justifyCenter", "justifyRight", "justifyFull", "insertUnorderedList", "insertOrderedList"];

      commands.forEach(function (cmd) {
        const isActive = document.queryCommandState ? document.queryCommandState(cmd) : false;
        self.$toolbar.find(`.v3-tb-btn[data-command='${cmd}']`).toggleClass("active", !!isActive);
      });
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
