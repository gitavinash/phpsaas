$(function () {
  const API_URL = "/api/discussion/create/";
  const MAX_IMAGES = 5;
  const MAX_SIZE_MB = 5;

  const $form = $("#discussionForm");
  const $editor = $("#editor");
  const $imageInput = $("#imageInput");
  const $beforeInput = $("#beforeImageInput");
  const $afterInput = $("#afterImageInput");
  const $submitBtn = $("#submitADiscussion");

  function setLoading(on) {
    $submitBtn
      .prop("disabled", on)
      .toggleClass("opacity-60 pointer-events-none", on)
      .html(
        on
          ? '<i class="ri-loader-4-line ri-spin mr-2"></i>Publishing...'
          : '<i class="ri-send-plane-line mr-2"></i>Publish Discussion'
      );
  }

  function isValidImageType(file) {
    const allowedMimeTypes = ["image/jpeg", "image/jpg", "image/png"];
    const allowedExtensions = ["jpg", "jpeg", "png"];
    const typeOk = allowedMimeTypes.includes(file.type.toLowerCase());
    const ext = file.name.split(".").pop().toLowerCase();
    const extOk = allowedExtensions.includes(ext);
    return typeOk && extOk;
  }

  function highlightError($el) {
    $el.addClass("ring-2 ring-red-500").attr("aria-invalid", "true");
    $el[0].scrollIntoView({ behavior: "smooth", block: "center" });
    setTimeout(() => {
      try {
        $el.trigger("focus");
      } catch (e) {}
    }, 150);
  }

  function clearHighlight($el) {
    $el.removeClass("ring-2 ring-red-500").attr("aria-invalid", "false");
  }

  function bytesToMB(n) {
    return n / (1024 * 1024);
  }

  $("#title").on("input blur", function () {
    clearHighlight($(this));
  });

  $("#categoryButton").on("click", function () {
    clearHighlight($(this));
  });

  $editor.on("input blur keyup paste", function () {
    clearHighlight($(this));
  });

  function getCategory() {
    const $hiddenVal = $("#categoryValue");
    if ($hiddenVal.length && $hiddenVal.val().trim())
      return $hiddenVal.val().trim();

    const txt = ($("#categoryText").text() || "").trim();
    return txt && txt !== "Select a category..."
      ? txt.replace(/\s+/g, "_").toLowerCase()
      : "";
  }

  function getTags() {
    return $("#tagInput .tag-item span[data-tag]")
      .map(function () {
        return $(this).data("tag");
      })
      .get();
  }

  function getPrivacySettings() {
    const obj = {};
    $(".toggle-switch").each(function () {
      obj[$(this).data("setting")] = $(this).hasClass("active");
    });
    return obj;
  }

  async function validateForm() {
    const title = ($("#title").val() || "").trim();
    if (!title) {
      await warn("Missing Title", "Please enter a title for your discussion.");
      highlightError($("#title"));
      return false;
    }

    if (title.length > 150) {
      await warn("Title Too Long", "Title must be 150 characters or fewer.");
      highlightError($("#title"));
      return false;
    }

    const category = getCategory();
    if (!category) {
      await warn("Missing Category", "Please select a category.");
      highlightError($("#categoryButton"));
      return false;
    }

    const postType = $('input[name="discussion_type"]:checked').val();
    if (!postType) {
      await warn(
        "Missing Post Type",
        "Please choose Discussion, Question, or Success Story."
      );
      highlightError($('input[name="discussion_type"]').first());
      return false;
    }

    const contentText = ($editor.text() || "").trim();
    if (!contentText || contentText.length < 10) {
      await warn(
        "Write your experience",
        "Your experience must have at least 10 characters."
      );
      highlightError($editor);
      return false;
    }

    // Gallery validation
    const galleryFiles = $imageInput[0].files || [];
    if (galleryFiles.length > MAX_IMAGES) {
      await warn(
        "Too Many Images",
        `You can upload a maximum of ${MAX_IMAGES} images.`
      );
      highlightError($("#uploadZone"));
      return false;
    }

    for (const file of galleryFiles) {
      if (!isValidImageType(file)) {
        await warn(
          "Invalid File",
          "Only JPG, JPEG, and PNG images are allowed."
        );
        highlightError($("#uploadZone"));
        return false;
      }
      if (bytesToMB(file.size) > MAX_SIZE_MB) {
        await warn(
          "File Too Large",
          `Each image must be less than ${MAX_SIZE_MB} MB.`
        );
        highlightError($("#uploadZone"));
        return false;
      }
    }

    // Before/After validation for success_story
    if (postType === "success_story") {
      if (!$beforeInput[0].files.length || !$afterInput[0].files.length) {
        await warn(
          "Before & After Required",
          "Please upload both before and after images."
        );
        highlightError($(".before-upload-zone"));
        return false;
      }

      const pair = [$beforeInput[0].files[0], $afterInput[0].files[0]];
      for (const f of pair) {
        if (!isValidImageType(f)) {
          await warn(
            "Invalid File",
            "Before/After images must be JPG, JPEG, or PNG."
          );
          return false;
        }
        if (bytesToMB(f.size) > MAX_SIZE_MB) {
          await warn(
            "File Too Large",
            `Before/After must be less than ${MAX_SIZE_MB} MB each.`
          );
          return false;
        }
      }
    }

    return true;
  }

  $imageInput.on("change", function () {
    const files = this.files;
    let invalidFiles = [];

    for (const file of files) {
      if (!isValidImageType(file)) {
        invalidFiles.push(file.name);
      }
    }

    if (invalidFiles.length > 0) {
      warn(
        "Invalid File(s)",
        `These files are not allowed: ${invalidFiles.join(", ")}`
      );
      $(this).val(""); // clear invalid files
    }
  });

  $form.on("submit", async function (e) {
    e.preventDefault();
    if ($submitBtn.prop("disabled")) return;

    const ok = await validateForm();
    if (!ok) return;

    const fd = new FormData();
    fd.append("title", ($("#title").val() || "").trim());
    fd.append("category", getCategory());
    fd.append("post_type", $('input[name="postType"]:checked').val());
    fd.append("content_html", ($editor.html() || "").trim());
    fd.append("content_text", ($editor.text() || "").trim());
    fd.append("tags", JSON.stringify(getTags()));

    const privacy = getPrivacySettings();
    fd.append("allow_comments", privacy.comments ? "1" : "0");
    fd.append("anonymous", privacy.anonymous ? "1" : "0");

    Array.from($imageInput[0].files).forEach((file) => {
      fd.append("files[]", file, file.name);
    });

    if ($beforeInput[0].files[0]) {
      fd.append(
        "beforeImage",
        $beforeInput[0].files[0],
        $beforeInput[0].files[0].name
      );
    }

    if ($afterInput[0].files[0]) {
      fd.append(
        "afterImage",
        $afterInput[0].files[0],
        $afterInput[0].files[0].name
      );
    }

    setLoading(true);

    $.ajax({
      url: API_URL,
      method: "POST",
      data: fd,
      processData: false,
      contentType: false,
      success: function (res) {
        let out = res;
        console.log(`res`);
        console.log(res);
        try {
          out = typeof res === "string" ? JSON.parse(res) : res;
        } catch (e) {}

        if (out?.status == "success") {
          successToast("Discussion published successfully!");
          $form[0].reset();
          $editor.html("");
          $imageInput.val("");
          $beforeInput.val("");
          $afterInput.val("");
          if (out?.redirectUrl)
            setTimeout(() => (window.location.href = out.redirectUrl), 600);
        } else {
          const msg = out?.msg || "Something went wrong. Please try again.";
          errorBox("Failed to Publish", msg);
        }
      },
      error: function (xhr) {
        const msg =
          xhr.responseJSON?.msg ||
          xhr.responseJSON?.message ||
          xhr.responseText ||
          "Something went wrong. Please try again.";
        errorBox(
          "Failed to Publish",
          typeof msg === "string" ? msg : "Request error"
        );
      },
      complete: function () {
        setLoading(false);
      },
    });
  });
});
