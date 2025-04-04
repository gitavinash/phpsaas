let dragAndDropFileList = [];
$(document).ready(function () {
  if ("#dropZone".length) {
    let dropZone = $("#dropZone");
    dragAndDropFileList = [];
    let selectedFiles = new FormData();
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const maxFiles = 10;

    dropZone.on("click", function (event) {
      if (!$(event.target).hasClass("removeBtn")) {
        $("#fileInput").click();
      }
    });

    $("#fileInput").on("change", function (e) {
      storeFiles(e.target.files);
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
          "You can only upload up to " + maxFiles + " files.",
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
          selectedFiles.append("files[]", file);
          dragAndDropFileList.push(file.name);
          let filePreview = file.type.startsWith("image/")
            ? `<img src="${URL.createObjectURL(file)}" alt="${file.name}">`
            : `<img src="/upload/others/docs/${file.name.split(".").pop()}.png" alt="${file.name}">`;
          let previewItem = `
                            <div class="previewItem" data-name="${file.name}">
                                ${filePreview}
                                <button class="removeBtn" data-name="${file.name}">&times;</button>
                            </div>`;
          $("#dropZone").append(previewItem);
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
  }
  if ("#editDropZone".length) {
    let editDropZone = $("#editDropZone");
    let selectedFiles = new FormData();
    dragAndDropFileList = [];
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const maxFiles = 10;

    editDropZone.on("click", function (event) {
      if (!$(event.target).hasClass("removeBtn")) {
        $("#editFileInput").click();
      }
    });

    $("#editFileInput").on("change", function (e) {
      storeFiles(e.target.files);
    });

    editDropZone.on("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass("dragover");
    });

    editDropZone.on("dragleave", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass("dragover");
    });

    editDropZone.on("drop", function (e) {
      e.preventDefault();
      $(this).removeClass("dragover");
      let files = e.originalEvent.dataTransfer.files;
      storeFiles(files);
    });

    function storeFiles(files) {
      if (dragAndDropFileList.length >= maxFiles) {
        popUpMessage(
          "center",
          "You can only upload up to " + maxFiles + " files.",
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
          selectedFiles.append("files[]", file);
          dragAndDropFileList.push(file.name);
          let filePreview = file.type.startsWith("image/")
            ? `<img src="${URL.createObjectURL(file)}" alt="${file.name}">`
            : `<img src="/upload/others/docs/${file.name.split(".").pop()}.png" alt="${file.name}">`;
          let editPreviewItem = `
                            <div class="editPreviewItem" data-name="${file.name}">
                                ${filePreview}
                                <button class="removeBtn" data-name="${file.name}">&times;</button>
                            </div>`;
          $("#editDropZone").append(editPreviewItem);
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
      if ($(".editPreviewItem").length > 0) {
        $(".editDefaultText").hide();
      } else {
        $(".editDefaultText").show();
      }
    }
  }
});
