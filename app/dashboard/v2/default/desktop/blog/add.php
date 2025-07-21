<h2 class="heading"><?php echo 'Add New blog' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Blog details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewBlogForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter blog details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input name="title" id="title" type="text" />
                            <label class="form-lable" id="Label">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <div class="editor" id="editor0" style="min-height: 500px"></div>
                            <textarea name="description" id="editor-0" style="display:none"></textarea>
                            <label class="form-lable" id="Label">Description<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p><strong>Feature image<sup>*</sup></strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" style="display: none;" />
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn btn-green" id="addBlog">Add new blog</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>