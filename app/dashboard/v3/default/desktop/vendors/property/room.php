<?php

?>
<div class="package-page-wrapper room-types-list-wrapper" id="roomTypesListApp">
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon room-types-header-badge"><i class="fi fi-rr-bed-empty"></i></div>
            <div class="package-header-titles">
                <h2>Room Types Library</h2>
                <p>Manage the room categories available across your properties.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="#" class="btn-add-pkg-v3 addNewRoomType" data-tooltip="Add a new room type"><i class="fi fi-rr-plus"></i> Add Room Type</a>
        </div>
    </div>

    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-bed-empty"></i></div>
            <div class="kpi-meta">
                <p>Total Room Types</p>
                <h3 id="statTotalRoomTypes">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>With Descriptions</p>
                <h3 id="statRoomTypesWithDescriptions">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-layers"></i></div>
            <div class="kpi-meta">
                <p>Categories</p>
                <h3 id="statRoomTypeCategories">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi purple">
            <div class="kpi-icon"><i class="fi fi-rr-list-check"></i></div>
            <div class="kpi-meta">
                <p>Library Status</p>
                <h3>Ready</h3>
            </div>
        </div>
    </div>

    <div class="package-table-card room-types-directory-card">
        <div class="package-filter-toolbar">
            <div class="filter-search-box"><i class="fi fi-rr-search search-svg-icon"></i><input type="search" id="roomTypeSearchInput" class="filter-search-input" placeholder="Search room types..." autocomplete="off"><button type="button" id="clearRoomTypeSearchBtn" class="clear-search-btn" title="Clear search" style="display:none">&times;</button></div>
            <button type="button" id="btnResetRoomTypeFilters" class="btn-v3-action" data-tooltip="Reset search"><i class="fi fi-rr-refresh"></i> Reset</button>
        </div>
        <div id="roomTypesGrid" class="room-types-v3-grid">
            <div class="room-types-loading-state">
                <div class="table-loading-spinner"></div><span>Loading room types...</span>
            </div>
        </div>
    </div>

    <div class="popUp roomTypeBox" style="display: none;">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <div>
                                        <h4 class="heading">Add new room type</h4>
                                        <p class="room-type-modal-subtitle">Create a room category for your property listings.</p>
                                    </div>
                                    <button type="button" class="room-type-modal-close" onclick="closePopup(this)" title="Close"><i class="fi fi-rr-cross-small"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" placeholder="e.g. Deluxe Family Room" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Description</label>
                                    <textarea type="text" name="description" id="description" placeholder="Add a short description (optional)"
                                        class="form-control" required></textarea>
                                    <span class="room-type-field-hint">Describe the room features or guest capacity.</span>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addRoomType"><i class="fi fi-rr-check"></i> Save room type</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editRoomTypeBox" style="display: none;">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <div>
                                        <h4 class="heading">Edit room type</h4>
                                        <p class="room-type-modal-subtitle">Update the name and description used in listings.</p>
                                    </div>
                                    <button type="button" class="room-type-modal-close" onclick="closePopup(this)" title="Close"><i class="fi fi-rr-cross-small"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="editName" name="name" type="text" placeholder="e.g. Deluxe Family Room" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Description<sup>*</sup></label>
                                    <textarea type="text" name="description" id="editDescription"
                                        placeholder="Add a short description (optional)" class="form-control" required></textarea>
                                    <span class="room-type-field-hint">Describe the room features or guest capacity.</span>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateRoomType"><i class="fi fi-rr-check"></i> Update room type</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>
</div>