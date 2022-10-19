<h2>Add new testimonial</h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title"
                    autofocus required>
            </div>
            <div class="form-group fullscreen-editor">
                <textarea name="description" class="editor inside-shadow"></textarea>
            </div>
        </div>
        <div class="right-box">

            <div class="action-box inside-shadow">
                <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                <!-- <input type="button" id="draftBtn"  class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo ltrim(rtrim(ADMIN_BASE_URL, '/'), '/') ?>">
            </div>

            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn">
                <span>Set Feature Image</span>
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls,
                    .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box" style="display: none;">
                <img src="#" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>

        </div>
    </form>
</div>