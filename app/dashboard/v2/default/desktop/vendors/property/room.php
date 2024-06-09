<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));


if ($roomsData = mysqli_query($connection, "SELECT category_id, category_title, category_image, category_description, category_deletable FROM " . TABLE_PREFIX . "category WHERE category_type='room'")) {
    $roomsList = array();
    if (mysqli_num_rows($roomsData) != 0) {
        while ($data = mysqli_fetch_assoc($roomsData)) {
            $roomsList[] = $data;
        }
    } else {

        $rooms_title = '';
        $rooms_description = '';
        $rooms_parent_id = '';
        $link_url = '';
    }
}

?>
<h2 class="heading">Room types list<span>Complete room types list</span></h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>
                <?php

                if (count($roomsList) > 0) {
                    for ($i = 0; $i < sizeof($roomsList); $i++) {
                        echo "
                    <div class='featureList editRoomType' id='" . $roomsList[$i]['category_id'] . "' data_name='" . $roomsList[$i]['category_title'] . "' data_desc='" . $roomsList[$i]['category_description'] . "' >
                        <img src='" . (($roomsList[$i]['category_image'] != '') ?
                            $roomsList[$i]['category_image'] : ADMIN_IMAGES . 'default-picture.png') . "' >
                        <p>{$roomsList[$i]['category_title']}</p>
                    </div>
                    ";
                    }
                }

                ?>
                <div class="shortcut">
                    <a href="#" class="addNewRoomType"><i class="fi fi-rr-plus"></i></a>
                </div>
            </section>
        </div>
    </div>

    <div class="popUp roomTypeBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Add new room type</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="name" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="description" placeholder="Description*" class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="addRoomType"><i class="fi-rr-check"></i> Add new room
                        type</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popUp editRoomTypeBox">
        <div class="smallPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Edit rooms</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-lable">Name<sup>*</sup></label>
                                    <input id="editName" name="name" type="text" />
                                </div>
                                <div class="form-group">
                                    <textarea type="text" name="description" id="editDescription" placeholder="Description*" class="form-control" required></textarea>
                                    <label class="form-lable" id="descriptionLabel">Description</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <a href="#" class="btn popUpBtn" id="updateRoomType"><i class="fi-rr-check"></i> Update rooms</a>
                    <span href="#" class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>
</div>