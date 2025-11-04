/* -----------------------------
     SweetAlert2 helpers
  ----------------------------- */
const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  timer: 2200,
  showConfirmButton: false,
  timerProgressBar: true,
});

const confirmAction = (
  title = "Are you sure?",
  text = "This action cannot be undone."
) =>
  Swal.fire({
    icon: "question",
    title,
    text,
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "Cancel",
    reverseButtons: true,
  });

// Share button

function shareDiscussion() {
  const shareData = {
    title: document.title,
    text: "Check out this discussion!",
    url: window.location.href,
  };

  // Detect if the user is on a mobile device
  const isMobile =
    /iPhone|iPad|iPod|Android|webOS|BlackBerry|Windows Phone/i.test(
      navigator.userAgent
    );

  if (isMobile && navigator.share) {
    // Use native share on mobile only
    navigator
      .share(shareData)
      .then(() => console.log("Successfully shared"))
      .catch((error) => console.error("Error sharing:", error));
  } else {
    // Show fallback options (desktop and unsupported mobile)
    document.getElementById("share-fallback").classList.remove("hidden");

    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent("Check out this discussion!");

    document.getElementById(
      "twitter-share"
    ).href = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
    document.getElementById(
      "facebook-share"
    ).href = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
    document.getElementById(
      "linkedin-share"
    ).href = `https://www.linkedin.com/shareArticle?url=${url}&title=${text}`;
    document.getElementById(
      "email-share"
    ).href = `mailto:?subject=${document.title}&body=${text}%0A${url}`;
  }
}

function copyLink() {
  navigator.clipboard
    .writeText(window.location.href)
    .then(() => alert("Link copied to clipboard!"))
    .catch(() => alert("Failed to copy link."));
}

const warn = (title, text) => Swal.fire({ icon: "warning", title, text });
const errorBox = (title, text) => Swal.fire({ icon: "error", title, text });
const successToast = (title) => Toast.fire({ icon: "success", title });

$(document).ready(function () {
  /* =========================
     PROFILE DROPDOWN
  ========================= */
  const $profileBtn = $("#profileButton");
  const $profileMenu = $("#profileDropdown");
  if ($profileBtn.length && $profileMenu.length) {
    $profileBtn.on("click", function (e) {
      e.stopPropagation();
      $profileMenu.toggleClass("show");
    });
    $profileMenu.on("click", function (e) {
      e.stopPropagation();
    });
    $(document).on("click", function () {
      $profileMenu.removeClass("show");
    });
  }

  /* =========================
     CATEGORY DROPDOWN
  ========================= */
  const $catBtn = $("#categoryButton");
  const $catMenu = $("#categoryDropdown");
  const $catText = $("#categoryText");
  const $catValue = $("#categoryValue"); // hidden input to store value

  $catBtn.on("click", function (e) {
    e.stopPropagation();
    $catMenu.toggleClass("show");
  });
  $(document).on("click", function () {
    $catMenu.removeClass("show");
  });

  $catMenu.on("click", ".category-option", function () {
    const text = $(this).text().trim();
    const value = $(this).data("value");
    $catText.text(text).removeClass("text-gray-500").addClass("text-gray-900");
    if ($catValue.length)
      $catValue.val(value || text.replace(/\s+/g, "_").toLowerCase());
    $catMenu.removeClass("show");
  });

  /* =========================
     TITLE COUNTER
  ========================= */
  const $title = $("#title");
  const $counter = $("#titleCounter");
  $title.on("input", function () {
    const len = this.value.length;
    $counter.text(`${len}/150`);
    $counter
      .toggleClass("text-yellow-600", len > 120 && len <= 140)
      .toggleClass("text-red-600", len > 140)
      .toggleClass("text-gray-400", len <= 120);
  });

  /* =========================
     RICH-TEXT TOOLBAR (execCommand) with SweetAlert input for links
  ========================= */
  const $editor = $("#editor");
  $(".editor-toolbar").on("click", ".editor-btn", async function (e) {
    e.preventDefault();
    const cmd = $(this).data("command");
    if (cmd === "createLink") {
      const { value: url } = await Swal.fire({
        title: "Insert link",
        input: "url",
        inputPlaceholder: "https://example.com",
        inputAttributes: { autocapitalize: "off" },
        showCancelButton: true,
        confirmButtonText: "Insert",
      });
      if (url) document.execCommand(cmd, false, url);
    } else {
      document.execCommand(cmd, false, null);
    }
    $editor.trigger("focus");
  });

  // keep editor empty when blur/focus if only whitespace
  $editor.on("focus blur", function () {
    if (($editor.text() || "").trim() === "") $editor.html("");
  });

  /* =========================
     POST TYPE -> BEFORE/AFTER TOGGLE
  ========================= */
  const $beforeAfter = $("#beforeAfterSection");
  $(document).on("change", 'input[name="discussion_type"]', function () {
    $(this).val() === "success_story"
      ? $beforeAfter.removeClass("hidden")
      : $beforeAfter.addClass("hidden");
  });

  /* =========================
     GALLERY UPLOAD + PREVIEW (max 5)
  ========================= */
  const $uploadZone = $("#uploadZone");
  const $imageInput = $("#imageInput");
  const $imagePreview = $("#imagePreview");
  const $previewGrid = $("#previewGrid");

  // open file picker
  $uploadZone.on("click", function () {
    $imageInput.trigger("click");
  });

  // drag & drop
  $uploadZone.on("dragover", function (e) {
    e.preventDefault();
    $(this).addClass("dragover");
  });
  $uploadZone.on("dragleave", function () {
    $(this).removeClass("dragover");
  });
  $uploadZone.on("drop", async function (e) {
    e.preventDefault();
    $(this).removeClass("dragover");
    const dt = e.originalEvent.dataTransfer;
    if (!dt || !dt.files?.length) return;

    const notImages = Array.from(dt.files).filter(
      (f) => !f.type.startsWith("image/")
    );
    if (notImages.length) {
      await warn("Invalid file(s)", "Only images are allowed.");
    }

    // merge dropped files with existing, keep up to 5
    const current = Array.from($imageInput[0].files || []);
    const incoming = Array.from(dt.files).filter((f) =>
      f.type.startsWith("image/")
    );
    const all = current.concat(incoming).slice(0, 5);
    const dataT = new DataTransfer();
    all.forEach((f) => dataT.items.add(f));
    $imageInput[0].files = dataT.files;
    renderGalleryPreviews($imageInput[0].files);
  });

  // file input change
  $imageInput.on("change", function () {
    const files = Array.from(this.files || []);
    const notImages = files.filter((f) => !f.type.startsWith("image/"));
    if (notImages.length) warn("Invalid file(s)", "Only images are allowed.");
    renderGalleryPreviews(this.files);
  });

  async function renderGalleryPreviews(fileList) {
    $previewGrid.empty();
    const files = Array.from(fileList || []);
    if (!files.length) {
      $imagePreview.removeClass("show");
      return;
    }

    const items = await Promise.all(
      files.map((f, i) =>
        readFileAsDataURL(f).then((url) => ({ url, index: i }))
      )
    );

    items.forEach(({ url, index }) => {
      $previewGrid.append(`
        <div class="relative">
          <img src="${url}" alt="Preview" class="w-full h-24 object-cover object-top rounded-lg">
          <button type="button"
                  class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 js-remove-image"
                  data-index="${index}" title="Remove">
            <i class="ri-close-line"></i>
          </button>
        </div>
      `);
    });
    $imagePreview.addClass("show");
  }

  function readFileAsDataURL(file) {
    return new Promise((res) => {
      const r = new FileReader();
      r.onload = (e) => res(e.target.result);
      r.readAsDataURL(file);
    });
  }

  // remove one image (confirm + rebuild FileList)
  $previewGrid.on("click", ".js-remove-image", async function () {
    const removeIdx = +$(this).data("index");

    const dt = new DataTransfer();
    Array.from($imageInput[0].files).forEach((f, i) => {
      if (i !== removeIdx) dt.items.add(f);
    });
    $imageInput[0].files = dt.files;
    renderGalleryPreviews($imageInput[0].files);
  });

  /* =========================
     BEFORE / AFTER PICKERS (single image each)
  ========================= */
  makeSinglePicker(
    ".before-upload-zone",
    "#beforeImageInput",
    "#beforePreview",
    "before"
  );
  makeSinglePicker(
    ".after-upload-zone",
    "#afterImageInput",
    "#afterPreview",
    "after"
  );

  function makeSinglePicker(zoneSel, inputSel, previewSel, label) {
    const $zone = $(zoneSel),
      $input = $(inputSel),
      $preview = $(previewSel);

    $zone.on("click", () => $input.trigger("click"));
    $input.on("change", async function () {
      const file = this.files?.[0];
      if (!file) return;
      if (!file.type.startsWith("image/")) {
        await warn("Invalid file", "Only images are allowed.");
        $input.val("");
        return;
      }
      const url = await readFileAsDataURL(file);
      $preview.find("img").attr("src", url);
      $preview.removeClass("hidden");
      $zone.addClass("hidden");
    });

    // remove/reset
    $preview.on("click", "button", async function () {
      const res = await confirmAction(
        `Remove ${label} image?`,
        "You can upload a different one."
      );
      if (!res.isConfirmed) return;
      $preview.addClass("hidden").find("img").attr("src", "");
      $zone.removeClass("hidden");
      $input.val("");
    });
  }

  /* =========================
     TAGS (Enter / comma, max 10)
  ========================= */
  const $tagWrap = $("#tagInput");
  const $tagField = $tagWrap.find(".tag-input-field");
  let tags = [];

  function renderTags() {
    $tagWrap.find(".tag-item").remove();
    tags.forEach((t) => {
      const $pill = $(`
        <div class="tag-item">
          <span data-tag="${t}">${t}</span>
          <span class="tag-remove" role="button" aria-label="Remove tag" title="Remove tag">&times;</span>
        </div>`);
      $pill.find(".tag-remove").on("click", async function () {
        const res = await confirmAction(
          "Remove this tag?",
          `"${t}" will be deleted.`
        );
        if (!res.isConfirmed) return;
        tags = tags.filter((x) => x !== t);
        renderTags();
      });
      $pill.insertBefore($tagField);
    });
  }

  async function addTag(text) {
    const t = (text || "").trim();
    if (!t) return;
    if (tags.includes(t)) {
      return;
    }
    if (tags.length >= 10) {
      await warn("Tag limit reached", "You can add up to 10 tags.");
      return;
    }
    tags.push(t);
    renderTags();
  }

  $tagField.on("keydown", function (e) {
    if (e.key === "Enter" || e.key === ",") {
      e.preventDefault();
      addTag(this.value);
      this.value = "";
    }
    if (e.key === "Backspace" && !this.value && tags.length) {
      // quick remove last (no confirm for speed)
      tags.pop();
      renderTags();
    }
  });
  $tagField.on("blur", function () {
    const v = this.value.trim();
    if (v) {
      addTag(v);
      this.value = "";
    }
  });

  /* =========================
     TOGGLE SWITCHES
  ========================= */
  $(document).on("click", ".toggle-switch", function () {
    $(this).toggleClass("active");
  });
});
