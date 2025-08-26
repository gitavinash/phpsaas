/* app.js
 * Requires jQuery
 * Safely initializes features only when their elements exist.
 * Uses event namespaces to avoid duplicate bindings if re-initialized.
 */
(function ($) {
  "use strict";

  // Helper: safe exists check
  const exists = (sel) => $(sel).length > 0;

  // ----------------------------------------
  // 1) Profile Dropdown (from your snippet)
  // ----------------------------------------
  $(function () {
    if (exists("#profileButton") && exists("#profileDropdown")) {
      // Unbind first to prevent duplicates
      $(document).off("click.profile");
      $("#profileButton").off("click.profile");
      $("#profileDropdown").off("click.profile");

      $("#profileButton").on("click.profile", function (e) {
        e.stopPropagation();
        $("#profileDropdown").toggleClass("show");
      });

      $(document).on("click.profile", function () {
        $("#profileDropdown").removeClass("show");
      });

      $("#profileDropdown").on("click.profile", function (e) {
        e.stopPropagation();
      });
    }
  });

  // ----------------------------------------
  // 2) Category Dropdown
  // ----------------------------------------
  $(function () {
    if (exists("#categoryButton") && exists("#categoryDropdown")) {
      $(document).off("click.category");
      $("#categoryButton").off("click.category");
      $("#categoryDropdown").off("click.category");
      $(".category-option").off("click.category");

      $("#categoryButton").on("click.category", function (e) {
        e.stopPropagation();
        $("#categoryDropdown").toggleClass("show");
      });

      $(document).on("click.category", function () {
        $("#categoryDropdown").removeClass("show");
      });

      $(".category-option").on("click.category", function () {
        const text = $(this).text();
        if (exists("#categoryText")) {
          $("#categoryText")
            .text(text)
            .removeClass("text-gray-500")
            .addClass("text-gray-900");
        }
        $("#categoryDropdown").removeClass("show");
      });
    }
  });

  // ----------------------------------------
  // 3) Title Counter
  // ----------------------------------------
  $(function () {
    if (exists("#title") && exists("#titleCounter")) {
      $("#title")
        .off("input.titleCounter")
        .on("input.titleCounter", function () {
          const length = $(this).val().length;
          const $c = $("#titleCounter");
          $c.text(length + "/150");
          if (length > 140) {
            $c.addClass("text-red-600").removeClass(
              "text-yellow-600 text-gray-400"
            );
          } else if (length > 120) {
            $c.addClass("text-yellow-600").removeClass(
              "text-gray-400 text-red-600"
            );
          } else {
            $c.addClass("text-gray-400").removeClass(
              "text-yellow-600 text-red-600"
            );
          }
        });
    }
  });

  // ----------------------------------------
  // 4) Rich Text Editor toolbar (execCommand)
  // ----------------------------------------
  $(function () {
    if (exists("#editor") && $(".editor-btn").length) {
      $(".editor-btn")
        .off("click.editor")
        .on("click.editor", function (e) {
          e.preventDefault();
          const cmd = $(this).data("command");
          if (cmd === "createLink") {
            const url = prompt("Enter URL:");
            if (url) document.execCommand(cmd, false, url);
          } else {
            document.execCommand(cmd, false, null);
          }
          $("#editor").trigger("focus");
        });
    }
  });

  // ----------------------------------------
  // 5) Image Upload + Preview (max 5)
  // ----------------------------------------
  $(function () {
    if (
      exists("#uploadZone") &&
      exists("#imageInput") &&
      exists("#previewGrid")
    ) {
      let uploadedImages = [];

      function displayImages() {
        const $grid = $("#previewGrid");
        $grid.empty();
        uploadedImages.forEach((image, index) => {
          $grid.append(`
            <div class="relative">
              <img src="${image.url}" alt="Preview" class="w-full h-24 object-cover object-top rounded-lg">
              <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 remove-image" data-index="${index}">
                <i class="ri-close-line"></i>
              </button>
            </div>
          `);
        });
        if (exists("#imagePreview")) {
          $("#imagePreview").toggleClass("show", uploadedImages.length > 0);
        }
      }

      function handleFiles(files) {
        files.forEach((file) => {
          if (file.type?.startsWith("image/") && uploadedImages.length < 5) {
            const reader = new FileReader();
            reader.onload = function (e) {
              uploadedImages.push({ file: file, url: e.target.result });
              displayImages();
            };
            reader.readAsDataURL(file);
          }
        });
      }

      $("#uploadZone")
        .off("click.img dragover.img dragleave.img drop.img")
        .on("click.img", function () {
          $("#imageInput").trigger("click");
        })
        .on("dragover.img", function (e) {
          e.preventDefault();
          $(this).addClass("dragover");
        })
        .on("dragleave.img", function () {
          $(this).removeClass("dragover");
        })
        .on("drop.img", function (e) {
          e.preventDefault();
          $(this).removeClass("dragover");
          const files = Array.from(e.originalEvent.dataTransfer.files || []);
          handleFiles(files);
        });

      $("#imageInput")
        .off("change.img")
        .on("change.img", function () {
          const files = Array.from(this.files || []);
          handleFiles(files);
        });

      $(document)
        .off("click.imgRemove")
        .on("click.imgRemove", ".remove-image", function () {
          const idx = Number($(this).data("index"));
          if (!Number.isNaN(idx)) {
            uploadedImages.splice(idx, 1);
            displayImages();
          }
        });
    }
  });

  // ----------------------------------------
  // 6) Tag Input
  // ----------------------------------------
  $(function () {
    if (exists("#tagInput")) {
      let tags = [];
      const $field = $("#tagInput .tag-input-field");
      const render = () => {
        $("#tagInput .tag-item").remove();
        tags.forEach((t) => {
          $(`
            <div class="tag-item">
              <span>${t}</span>
              <span class="tag-remove" data-tag="${t}">×</span>
            </div>
          `).insertBefore($field);
        });
      };
      const add = (t) => {
        if (t && !tags.includes(t) && tags.length < 10) {
          tags.push(t);
          render();
        }
      };

      $field
        .off("keydown.tags blur.tags")
        .on("keydown.tags", function (e) {
          if (e.key === "Enter" || e.key === ",") {
            e.preventDefault();
            add($(this).val().trim());
            $(this).val("");
          }
        })
        .on("blur.tags", function () {
          const val = $(this).val().trim();
          if (val) {
            add(val);
            $(this).val("");
          }
        });

      $(document)
        .off("click.tagsRemove")
        .on("click.tagsRemove", ".tag-remove", function () {
          const t = $(this).data("tag");
          tags = tags.filter((x) => x !== t);
          render();
        });
    }
  });

  // ----------------------------------------
  // 7) Toggle Switches
  // ----------------------------------------
  $(function () {
    if ($(".toggle-switch").length) {
      $(".toggle-switch")
        .off("click.toggle")
        .on("click.toggle", function () {
          $(this).toggleClass("active");
        });
    }
  });

  // ----------------------------------------
  // 8) Preview Panel
  // ----------------------------------------
  $(function () {
    if (exists("#previewButton") && exists("#previewPanel")) {
      const typeMap = {
        discussion: { text: "Discussion", class: "bg-blue-100 text-blue-800" },
        question: { text: "Question", class: "bg-pink-100 text-pink-800" },
        "success-story": {
          text: "Success Story",
          class: "bg-yellow-100 text-yellow-800",
        },
      };

      function updatePreview() {
        const title = exists("#title")
          ? $("#title").val() || "Your discussion title will appear here"
          : "";
        const content = exists("#editor")
          ? $("#editor").html() || "Your content will be previewed here..."
          : "";
        const selectedType = $("input[name='postType']:checked").val();

        if (exists("#previewTitle")) $("#previewTitle").text(title);
        if (exists("#previewContent")) $("#previewContent").html(content);

        if (exists("#previewType") && typeMap[selectedType]) {
          $("#previewType")
            .text(typeMap[selectedType].text)
            .attr(
              "class",
              "px-2.5 py-1 text-xs font-medium rounded-full " +
                typeMap[selectedType].class
            );
        }
      }

      $("#previewButton")
        .off("click.preview")
        .on("click.preview", function () {
          updatePreview();
          $("#previewPanel").toggleClass("show");
          if ($("#previewPanel").hasClass("show")) {
            $(this).html('<i class="ri-eye-off-line mr-2"></i>Hide Preview');
          } else {
            $(this).html('<i class="ri-eye-line mr-2"></i>Preview Post');
          }
        });
    }
  });

  // ----------------------------------------
  // 9) Form Submission (Publish / Save Draft)
  // ----------------------------------------
  $(function () {
    if (exists("#discussionForm")) {
      $("#discussionForm")
        .off("submit.form")
        .on("submit.form", function (e) {
          e.preventDefault();
          if (exists("#publishButton")) {
            $("#publishButton")
              .html(
                '<i class="ri-loader-4-line mr-2 animate-spin"></i>Publishing...'
              )
              .prop("disabled", true);
          }
          setTimeout(function () {
            alert("Discussion published successfully!");
            window.location.href =
              "https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/bed2abc6-7e4f-48c1-a176-ebec3e67b0a3";
          }, 2000);
        });

      if (exists("#saveDraftButton")) {
        $("#saveDraftButton")
          .off("click.form")
          .on("click.form", function () {
            const $btn = $(this);
            $btn
              .html(
                '<i class="ri-loader-4-line mr-2 animate-spin"></i>Saving...'
              )
              .prop("disabled", true);
            setTimeout(function () {
              alert("Draft saved successfully!");
              $btn
                .html('<i class="ri-save-line mr-2"></i>Save as Draft')
                .prop("disabled", false);
            }, 1500);
          });
      }
    }
  });
})(jQuery);
