<?php

$typeDropDown = '<option value="">Select amenities type</option>
                                        <option value="basic_facilities" >Basic Facilities</option>
                                        <option value="beauty_and_spa" >Beauty and Spa</option>
                                        <option value="food_and_drinks">Food and Drinks</option>
                                        <option value="payment_services">Payment Services</option>
                                        <option value="safety_andsecurity">Safety and Security</option>
                                        <option value="health_and_wellness">Health and wellness</option>
                                        <option value="general_services">General Services</option>
                                        <option value="outdoor_activities_and_sports">Outdoor Activities and Sports
                                        </option>
                                        <option value="indoor_activities and Sports">Indoor Activities and Sports
                                        </option>
                                        <option value="common_area">Common Area</option>
                                        <option value="other_facilities">Other Facilities</option>
                                        <option value="swimming_pool">Swimming Pool</option>
                                        <option value="kitchen">Kitchen</option>
                                        <option value="living_room">Living Room</option>
                                        <option value="parking">Parking</option>
                                        <option value="room_and_bathroom">Room and Bathroom</option>
                                        <option value="transfers">Transfers</option>
                                        <option value="media_and_technology">Media and technology</option>
                                        <option value="shopping">Shopping</option>
                                        <option value="business_center_and_conferences">Business Center and Conferences
                                        </option>
                                        <option value="terrace">Terrace</option>
                                        <option value="dining_area">Dining Area</option>
                                        <option value="entertainment">Entertainment</option>';

?>
<div class="package-page-wrapper amenities-list-wrapper" id="amenitiesListApp">
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon amenities-header-badge"><i class="fi fi-rr-star"></i></div>
            <div class="package-header-titles">
                <h2>Amenities Library</h2>
                <p>Manage the facilities and services available across your properties.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="#" class="btn-add-pkg-v3 addNewAmenities" data-tooltip="Add a new amenity"><i class="fi fi-rr-plus"></i> Add Amenity</a>
        </div>
    </div>

    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-star"></i></div>
            <div class="kpi-meta">
                <p>Total Amenities</p>
                <h3 id="statTotalAmenities">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-apps"></i></div>
            <div class="kpi-meta">
                <p>Categories</p>
                <h3 id="statAmenityCategories">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-picture"></i></div>
            <div class="kpi-meta">
                <p>With Icons</p>
                <h3 id="statAmenitiesWithIcons">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-list-check"></i></div>
            <div class="kpi-meta">
                <p>Most Used Category</p>
                <h3 id="statTopAmenityCategory">-</h3>
            </div>
        </div>
    </div>

    <div class="package-table-card amenities-directory-card">
        <div class="package-filter-toolbar">
            <div class="filter-search-box">
                <i class="fi fi-rr-search search-svg-icon"></i>
                <input type="search" id="amenitySearchInput" class="filter-search-input" placeholder="Search amenities..." autocomplete="off">
                <button type="button" id="clearAmenitySearchBtn" class="clear-search-btn" title="Clear search">&times;</button>
            </div>
            <div class="filter-dropdowns-wrap">
                <select id="amenityCategoryFilter" class="filter-select selectJS2" data-placeholder="All Categories">
                    <option value="">All Categories</option>
                </select>
                <button type="button" id="btnResetAmenityFilters" class="btn-v3-action" data-tooltip="Reset all filters"><i class="fi fi-rr-refresh"></i> Reset</button>
            </div>
        </div>
        <div id="amenitiesGrid" class="amenities-v3-grid">
            <div class="amenities-loading-state">
                <div class="table-loading-spinner"></div><span>Loading amenities library...</span>
            </div>
        </div>
    </div>

    <div class="popUp amenitiesBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="addAmenitiesForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new amenity</h4>
                                    <button type="button" class="amenity-modal-close" onclick="closePopup(this)" title="Close"><i class="fi fi-rr-cross-small"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" placeholder="Amenities name" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="description" placeholder="Description"
                                        class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <div class="amenity-upload-layout">
                                        <div class="amenity-upload-preview is-visible" id="addAmenityPreview">
                                            <img src="" alt="Selected amenity icon" id="addAmenityPreviewImage">
                                            <i class="fi fi-rr-picture amenity-preview-placeholder"></i>
                                        </div>
                                        <div class="amenity-upload-actions">
                                            <label for="icon" class="amenity-upload-button" title="Upload amenity icon"><i class="fi fi-rr-camera"></i><span>Upload icon</span></label>
                                            <input type="file" id="icon" name="icon" accept="image/*" />
                                            <button type="button" class="amenity-remove-button" data-input="icon" data-preview="addAmenityPreview" title="Remove selected icon"><i class="fi fi-rr-trash"></i><span>Remove</span></button>
                                            <span class="amenity-upload-hint">PNG, JPG, WEBP up to 5MB</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category"
                                        id="type"><?php echo $typeDropDown ?></select>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class=" widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addAmenities"><i class="fi-rr-check"></i> Add new
                        amenities</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editAmenitiesBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" id="updateAmenitiesForm" enctype="multipart/form-data">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit amenity</h4>
                                    <button type="button" class="amenity-modal-close" onclick="closePopup(this)" title="Close"><i class="fi fi-rr-cross-small"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="editName" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="editDescription"
                                        placeholder="Description" class="form-control" required></textarea>
                                    <label class="form-lable" id="editDescriptionLabel">Description</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Icon ( size 100px x 100px )</label>
                                    <div class="amenity-upload-layout">
                                        <div class="amenity-upload-preview is-visible" id="editAmenityPreview">
                                            <img src="" alt="Selected amenity icon" id="editAmenityPreviewImage">
                                            <i class="fi fi-rr-picture amenity-preview-placeholder"></i>
                                        </div>
                                        <div class="amenity-upload-actions">
                                            <label for="editIcon" class="amenity-upload-button" title="Upload replacement icon"><i class="fi fi-rr-camera"></i><span>Upload icon</span></label>
                                            <input type="file" id="editIcon" name="icon" accept="image/*" />
                                            <button type="button" class="amenity-remove-button" data-input="editIcon" data-preview="editAmenityPreview" title="Remove selected icon"><i class="fi fi-rr-trash"></i><span>Remove</span></button>
                                            <span class="amenity-upload-hint">PNG, JPG, WEBP up to 5MB</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <p>Type</p>
                                    <select class="selectJS form-control" name="category"
                                        id="editType"><?php echo $typeDropDown ?></select>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateAmenities"><i class="fi-rr-check"></i> Update
                        amenities</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                    <p>Current icon</p>
                    <img src="" id="amenitiesIcon" alt="">
                </div>
            </div>
        </div>
    </div>
</div>