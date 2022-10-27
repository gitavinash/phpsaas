<h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title"
                    autofocus required>
            </div>
            <div class="form-group">
                <div class="url">
                    <p><strong>Permalink</strong></p>
                    <p><span><?php echo SITE_DOMAIN ?>/treatment/</span></p>
                    <input type="text" id="url" class="form-control" name="url">
                </div>
            </div>

            <?php

            if ($site_id == 31) { ?>
            <div class="form-group">
                <div class="form-group fullscreen-editor">
                    <textarea name="description" class="editor inside-shadow"></textarea>
                </div>
            </div>

            <section style="margin-top:30px">
                <h3>Feature Video</h3>
                <hr>
                <div class="form-group">
                    <label>Youtube video url</label>
                    <input type="text" class="form-control inside-shadow" name="featureVideo"
                        placeholder="Feature Video">
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Quote</h3>
                <hr>
                <div class="form-group">
                    <label>Quote message</label>
                    <textarea name="quote" class="form-control inside-shadow" placeholder="Quote message"></textarea>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 1</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_1"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_1"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_1"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_1"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_1"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_1"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_1"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 1 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_1" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 2</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_2"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_2"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_2"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_2"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_2"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_2"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_2"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 2 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_2" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 3</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_3"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_3"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_3"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_3"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_3"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_3"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_3"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 3 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_3" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 4</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_4"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_4"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_4"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_4"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_4"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_4"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_4"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 4 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_4" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 5</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_5"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_5"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_5"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_5"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_5"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_5"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_5"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 5 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_5" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 6</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_6"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_6"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_6"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_6"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_6"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_6"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_6"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 6 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_6" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 7</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_7"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_7"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_7"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_7"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_7"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_7"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_7"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 7 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_7" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 8</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_8"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_8"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_8"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_8"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_8"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_8"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_8"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 8 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_8" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 9</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_9"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_9"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_9"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_9"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_9"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_9"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_9"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 9 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_9" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 10</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_10"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_10"
                        placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_10"
                        placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_10"
                        placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_10"
                        placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_10"
                        placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_10"
                        placeholder="Watch Video Link">
                </div>
                <div class="row">
                    <h4>Treatment Option 10 Feature Image</h4>
                    <div class="form-group">
                        <label>Before image</label>
                        <input type="file" name="treatmentOptionFeatureImage_10" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>


            <section style="margin-top:30px">
                <h3>Before After</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="berforeAfterHeader"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Sub Header</label>
                    <input type="text" class="form-control inside-shadow" name="berforeAfterSubHeader"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="berforeAfterDescription" class="form-control inside-shadow"
                        placeholder="Description"></textarea>
                </div>
                <div class="form-group">
                    <label>Video link</label>
                    <input type="text" class="form-control inside-shadow" name="berforeAfterVideLink"
                        placeholder="Video link">
                </div>
                <div class="row">

                    <div class="productGallery generalTab">

                        <div class="input-field">
                            <div class="input-images" style="padding-top: .5rem;"></div>
                        </div>

                    </div>

                </div>
            </section>

            <section style="margin-top: 30px">
                <h3>Why Choose Us</h3>
                <hr>
                <div class="form-group">
                    <label>Feature image</label>
                    <input type="file" name="whyChooseUs" class="full-width"
                        accept="image/x-png, image/gif, image/jpeg">
                </div>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" name="whyChooseTitle" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control inside-shadow" name="whyChooseDescription"
                        placeholder="Description">
                </div>

                <div class="row">
                    <div class="col-4">
                        <h4>Step 1</h4>
                        <div class="form-group">
                            <label>Step 1 icon</label>
                            <input type="file" name="stepIcon_1" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
                        </div>
                        <div class="form-group">
                            <label>Step 1 Description</label>
                            <input type="text" class="form-control inside-shadow" name="stepHeader_1"
                                placeholder="Step 1 Description">
                        </div>
                    </div>
                    <div class="col-4">
                        <h4>Step 2</h4>
                        <div class="form-group">
                            <label>Step 2 icon</label>
                            <input type="file" name="stepIcon_2" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
                        </div>
                        <div class="form-group">
                            <label>Step 2 Description</label>
                            <input type="text" class="form-control inside-shadow" name="stepHeader_2"
                                placeholder="Step 2 Description">
                        </div>
                    </div>
                    <div class="col-4">
                        <h4>Step 3</h4>
                        <div class="form-group">
                            <label>Step 3 icon</label>
                            <input type="file" name="stepIcon_3" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
                        </div>
                        <div class="form-group">
                            <label>Step 3 Description</label>
                            <input type="text" class="form-control inside-shadow" name="stepHeader_3"
                                placeholder="Step 3 Description">
                        </div>
                    </div>
                </div>
            </section>

            <?php
            } else {
            ?>
            <div class="form-group fullscreen-editor">
                <textarea name="description" class="editor inside-shadow"></textarea>
            </div>

            <!-- Treatment description and other things -->

            <section style="margin-bottom:20px">
                <h3>Treatment Details</h3>
                <ul class='tabs'>
                    <li id="productGeneral" class='active'>General</li>
                    <li id="productInventory">Inventory</li>
                    <li id="productGallery">Gallery</li>
                    <li id="productAdditionalDetails">Additional Details</li>
                </ul>
                <div class="productGeneral generalTab">
                    <div class="form-group">
                        <label>Treatment short description / Highlights</label>
                        <textarea name="highlight" class="form-control inside-shadow"
                            placeholder="Treatment short description / Highlights"></textarea>
                    </div>
                </div>
                <div class="productInventory generalTab" style="display: none;">
                    <div class="box-2">
                        <div class="form-group">
                            <label>Sale price (₹)</label>
                            <input type="number" class="form-control inside-shadow" name="salePrice"
                                placeholder="Sale price">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Regular price (₹)</label>
                            <input type="number" class="form-control inside-shadow" name="mrp"
                                placeholder="Regular price">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="text" class="form-control inside-shadow" name="sku" placeholder="SKU">
                        </div>
                    </div>
                </div>
                <div class="productShipping generalTab" style="display: none;">
                    <div class="box-1">
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="text" class="form-control inside-shadow" name="weight" placeholder="Weight">
                        </div>
                    </div>
                    <label>Dimensions (cm)</label>
                    <div class="form-group">
                        <div class="box-3">
                            <label>Lenght</label>
                            <input type="text" class="form-control inside-shadow" name="lenght" placeholder="Lenght">
                        </div>
                        <div class="box-3">
                            <label>Width</label>
                            <input type="text" class="form-control inside-shadow" name="width" placeholder="Width">
                        </div>
                        <div class="box-3">
                            <label>Height</label>
                            <input type="text" class="form-control inside-shadow" name="height" placeholder="Height">
                        </div>
                    </div>
                </div>
                <div class="productGallery generalTab" style="display: none;">
                    <input type="file" id="gallery" class="galleryBtn" name="gallery[]" multiple>
                </div>
                <div class="productAdditionalDetails generalTab" style="display: none;">

                </div>
            </section>
            <?php
            }

            ?>

            <!-- SEO & social media marketing -->

            <section style="margin-top: 30px">
                <h3>Advance Options for digital marketing</h3>
                <ul class='tabs'>
                    <li id=" seo" class='active'>Search Engine Optimization</li>
                    <li id="fb">Facebook</li>
                    <li id="tw">Twitter</li>
                </ul>
                <div class="seo generalTab">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow"
                            placeholder="Meta title">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                            placeholder="Meta Description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                            placeholder="Enter keywords of the post ( use , to seprate keywords )"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow" id="canonical"
                            placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this Post in search results?</label>
                        <select name="robots" class="form-control inside-shadow">
                            <option value="index, follow">Yes</option>
                            <option value="noindex, nofollow">No</option>
                        </select>
                    </div>
                </div>
                <div class="fb generalTab" style="display: none;">
                    <div class="facebookPreviewBox" style="display: none;">
                        <div class="img"><img src="" id="facebookPath" alt=""></div>
                        <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                    </div>
                    <div class="facebookUploadBox">
                        <span>Set facebook feature image</span>
                        <input type='file' id="facebookImage" name='facebookImage' />
                    </div>
                    <div class="form-group">
                        <label>Facebook Title</label>
                        <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"
                            placeholder="Facebook title">
                    </div>
                    <div class="form-group">
                        <label>Facebook Description</label>
                        <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                            placeholder="Facebook Description"></textarea>
                    </div>
                </div>
                <div class="tw generalTab" style="display: none;">
                    <div class="twitterPreviewBox" style="display: none;">
                        <div class="img"><img src="" id="twitterPath" alt=""></div>
                        <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                    </div>
                    <div class="twitterUploadBox">
                        <span>Set Twitter feature image</span>
                        <input type='file' id="twitterImage" name='twitterImage' />
                    </div>
                    <div class="form-group">
                        <label>Twitter Title</label>
                        <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"
                            placeholder="Twitter title">
                    </div>
                    <div class="form-group">
                        <label>Twitter Description</label>
                        <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                            placeholder="Twitter Description"></textarea>
                    </div>
                </div>
            </section>

        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="publish">
            </div>
            <div class="form-group">
                <h2>Featured</h2>
                <div class="action-box inside-shadow" style="padding: 10px">
                    <input type="checkbox" value="1" name='featurePost'> Mark as feature treatment
                </div>
            </div>
            <h2>Categories</h2>
            <div class="action-box height-200 inside-shadow">
                <?php
                if ($categoryList = mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . SITE_ID . "' AND link_status=200 ORDER BY category_title ASC")) {
                    while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                <div class="form-group">
                    <input type="radio" name="category"
                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                        <?php echo (($category['category_id'] == 1) ? 'checked' : '') ?>>
                    <?php echo ucwords($category['category_title']); ?>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <h2>Tags</h2>
            <div class="action-box inside-shadow">
                <div class="form-group">
                    <input type="text" class='tags' />
                    <input type="hidden" id='tags' name="tags" value=''>
                </div>
            </div>
            <div class="tagList" style="display: none;"></div>
            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn">
                <span>Set Feature Image</span>
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                        file size maximum 500kb</p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box" style="display: none;">
                <img src="#" id="featureImagePath" alt="">
                <p id="changeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>