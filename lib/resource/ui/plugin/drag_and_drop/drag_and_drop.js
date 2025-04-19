$(document).ready(function () {
  if ($("#dropZone").length) {
    let dropZone = $("#dropZone");
    dragAndDropFileList = [];
    let selectedFiles = new FormData();
    const maxFileSize = 5 * 1024 * 1024; // 5MB

    // Get file limit from input
    const fileInput = $("#fileInput");
    const maxFiles = parseInt(fileInput.data("limit")) || 10;

    // Render predefined images
    renderOldGalleryImages();

    dropZone.on("click", function (event) {
      if (!$(event.target).hasClass("removeBtn")) {
        fileInput.click();
      }
    });

    fileInput.on("change", function (e) {
      storeFiles(e.target.files);
      // Reset file input so you can reselect the same file if needed
      $(this).val("");
    });

    dropZone.on("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass("dragover");
    });

    dropZone.on("dragleave", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass("dragover");
    });

    dropZone.on("drop", function (e) {
      e.preventDefault();
      $(this).removeClass("dragover");
      let files = e.originalEvent.dataTransfer.files;
      storeFiles(files);
    });

    function storeFiles(files) {
      if (dragAndDropFileList.length >= maxFiles) {
        popUpMessage(
          "center",
          `You can only upload up to ${maxFiles} files.`,
          "error"
        );
        return;
      }

      for (let i = 0; i < files.length; i++) {
        let file = files[i];

        if (file.size > maxFileSize) {
          popUpMessage(
            "center",
            file.name + " exceeds the maximum file size of 5MB.",
            "error"
          );
          continue;
        }

        if (dragAndDropFileList.length >= maxFiles) {
          break;
        }

        if (!dragAndDropFileList.includes(file.name)) {
          selectedFiles.append("featureImage", file); // Ensure "featureImage" is the key
          dragAndDropFileList.push(file.name);

          let filePreview = file.type.startsWith("image/")
            ? `<img src="${URL.createObjectURL(file)}" alt="${
                file.name
              }" style="width: 100px;">`
            : `<img src="/upload/others/docs/${file.name
                .split(".")
                .pop()}.png" alt="${file.name}">`;

          let previewItem = `
            <div class="previewItem" data-name="${file.name}">
              ${filePreview}
              <button class="removeBtn" data-name="${file.name}">&times;</button>
            </div>`;

          dropZone.append(previewItem);
        }
      }
      checkPreviewVisibility();
    }

    $(document).on("click", ".removeBtn", function (event) {
      event.stopPropagation();
      let fileName = $(this).attr("data-name");
      dragAndDropFileList = dragAndDropFileList.filter(
        (name) => name !== fileName
      );
      $(this).parent().remove();
      checkPreviewVisibility();
    });

    function checkPreviewVisibility() {
      if ($(".previewItem").length > 0) {
        $(".defaultText").hide();
      } else {
        $(".defaultText").show();
      }
    }

    function renderOldGalleryImages() {
      let oldImagesText = $("#oldGallery").text().trim();
      if (!oldImagesText) return;

      let oldImages = oldImagesText.split(",").map((name) => name.trim());
      oldImages.forEach((imageName) => {
        if (!imageName) return;

        let ext = imageName.split(".").pop().toLowerCase();
        let imageUrl = `${imageName}`; // Update path if needed

        let filePreview = ["jpg", "jpeg", "png"].includes(ext)
          ? `<img src="${imageUrl}" alt="${imageName}" style="width: 100px;">`
          : `<img src="/upload/others/docs/${ext}.png" alt="${imageName}">`;

        let previewItem = `
          <div class="previewItem" data-name="${imageName}">
            ${filePreview}
            <button class="removeBtn" data-name="${imageName}">&times;</button>
          </div>`;

        dropZone.append(previewItem);
        dragAndDropFileList.push(imageName);
      });

      checkPreviewVisibility();
    }
  }
});
