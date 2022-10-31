<h2>Add new slide</h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <label>Slider Title</label>
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" autofocus required>
            </div>

            <div class="form-group fullscreen-editor">
                <label>Slider Description</label>
                <textarea name="description" class="form-control inside-shadow"></textarea>
            </div>

        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="submit" id="publishPost" class="btn btn-green btn-block" value="Add new slide">
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="publish">
            </div>
            <h2>Slider Image</h2>
            <div class="form-group featureImageUploadBox upload-btn">
                <span>Set slider image</span>
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                        file size maximum 500kb</p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box" style="display: none;">
                <img src="#" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>

<h2>Slides list</h2>
<div class="form-box">
    <table id="table">
        <thead>
            <tr>
                <th style="width: 400px">Image</th>
                <th style="width: 150px">Title</th>
                <th style="width: 150px">Description</th>
                <th style="width: 100px"></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
</div>