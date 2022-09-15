<?php
	
	$service_id=base64_decode(mysqli_real_escape_string($connection, $_GET['id']));
    
	if($serviceDetails=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."service, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta WHERE link_type='service' AND link_relation_id=service_id AND meta_relation_id=link_id AND service_id='{$service_id}' AND link_status!=301 AND link_type='service' AND service_type='treatment' AND link_site_id=".SITE_ID." LIMIT 1")) {
		if(mysqli_num_rows($serviceDetails)!=0) {
			extract(mysqli_fetch_assoc($serviceDetails));

            $serviceSection = json_decode($service_section);
            
		} else { ?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = 'dashboard/service/?type=list&status=all';
</script>
<?php
		}
	}

?>
<h2>Update <?php echo (($link_status=='206') ? 'Drafted ' : '').ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?>
    <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span>
</h2>

<div class="form-box">
    <form method="service" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <input type="text" class="form-control inside-shadow" name="title" placeholder="Title"
                    value="<?php echo $service_title ?>" required="">
            </div>
            <div class="form-group">
                <div class="url">
                    <?php
						$url=explode('/', ltrim(rtrim($link_url, '/'), '/'));
					?>
                    <p><strong>Permalink</strong></p>
                    <p><span><?php echo SITE_DOMAIN.'/...'; ?>/</span></p>
                    <input type="text" id="url" class="form-control" value="<?php echo end($url); ?>" name="url">
                    <input type="hidden" name='oldURL' value='<?php echo $link_url ?>'>
                </div>
            </div>

            <?php
            
            if($site_id==31) {?>

            <div class="form-group">
                <div class="form-group fullscreen-editor">
                    <textarea name="description"
                        class="editor inside-shadow"><?php echo $service_description ?></textarea>
                </div>
            </div>

            <section style="margin-top:30px">
                <h3>Feature Video</h3>
                <hr>
                <div class="form-group">
                    <label>Youtube video url</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->feature_video->url ?>" name="featureVideo"
                        placeholder="Feature video">
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Quote</h3>
                <hr>
                <div class="form-group">
                    <label>Quote message</label>
                    <textarea name="quote" class="form-control inside-shadow"
                        placeholder="Quote message"><?php echo $serviceSection->quote->quote  ?></textarea>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Treatment Option 1</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->header ?>"
                        name="treatmentOptionHeader_1" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->indications ?>"
                        name="treatmentOptionIndications_1" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->process ?>"
                        name="treatmentOptionProcess_1" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->duration ?>"
                        name="treatmentOptionDuration_1" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->sitting ?>"
                        name="treatmentOptionSitting_1" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->bookNow ?>"
                        name="treatmentOptionBookNow_1" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[0]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_1" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->header ?>"
                        name="treatmentOptionHeader_2" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->indications ?>"
                        name="treatmentOptionIndications_2" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->process ?>"
                        name="treatmentOptionProcess_2" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->duration ?>"
                        name="treatmentOptionDuration_2" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->sitting ?>"
                        name="treatmentOptionSitting_2" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->bookNow ?>"
                        name="treatmentOptionBookNow_2" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[1]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_2" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->header ?>"
                        name="treatmentOptionHeader_3" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->indications ?>"
                        name="treatmentOptionIndications_3" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->process ?>"
                        name="treatmentOptionProcess_3" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->duration ?>"
                        name="treatmentOptionDuration_3" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->sitting ?>"
                        name="treatmentOptionSitting_3" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->bookNow ?>"
                        name="treatmentOptionBookNow_3" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[2]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_3" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->header ?>"
                        name="treatmentOptionHeader_4" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->indications ?>"
                        name="treatmentOptionIndications_4" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->process ?>"
                        name="treatmentOptionProcess_4" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->duration ?>"
                        name="treatmentOptionDuration_4" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->sitting ?>"
                        name="treatmentOptionSitting_4" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->bookNow ?>"
                        name="treatmentOptionBookNow_4" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[3]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_4" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->header ?>"
                        name="treatmentOptionHeader_5" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->indications ?>"
                        name="treatmentOptionIndications_5" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->process ?>"
                        name="treatmentOptionProcess_5" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->duration ?>"
                        name="treatmentOptionDuration_5" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->sitting ?>"
                        name="treatmentOptionSitting_5" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->bookNow ?>"
                        name="treatmentOptionBookNow_5" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[4]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_5" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->header ?>"
                        name="treatmentOptionHeader_6" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->indications ?>"
                        name="treatmentOptionIndications_6" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->process ?>"
                        name="treatmentOptionProcess_6" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->duration ?>"
                        name="treatmentOptionDuration_6" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->sitting ?>"
                        name="treatmentOptionSitting_6" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->bookNow ?>"
                        name="treatmentOptionBookNow_6" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[5]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_6" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->header ?>"
                        name="treatmentOptionHeader_7" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->indications ?>"
                        name="treatmentOptionIndications_7" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->process ?>"
                        name="treatmentOptionProcess_7" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->duration ?>"
                        name="treatmentOptionDuration_7" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->sitting ?>"
                        name="treatmentOptionSitting_7" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->bookNow ?>"
                        name="treatmentOptionBookNow_7" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[6]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_7" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->header ?>"
                        name="treatmentOptionHeader_8" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->indications ?>"
                        name="treatmentOptionIndications_8" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->process ?>"
                        name="treatmentOptionProcess_8" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->duration ?>"
                        name="treatmentOptionDuration_8" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->sitting ?>"
                        name="treatmentOptionSitting_8" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->bookNow ?>"
                        name="treatmentOptionBookNow_8" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[7]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_8" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->header ?>"
                        name="treatmentOptionHeader_9" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->indications ?>"
                        name="treatmentOptionIndications_9" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->process ?>"
                        name="treatmentOptionProcess_9" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->duration ?>"
                        name="treatmentOptionDuration_9" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->sitting ?>"
                        name="treatmentOptionSitting_9" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->bookNow ?>"
                        name="treatmentOptionBookNow_9" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[8]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_9" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->header ?>"
                        name="treatmentOptionHeader_10" placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Indications</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->indications ?>"
                        name="treatmentOptionIndications_10" placeholder="Indications">
                </div>
                <div class="form-group">
                    <label>Process</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->process ?>"
                        name="treatmentOptionProcess_10" placeholder="Process">
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->duration ?>"
                        name="treatmentOptionDuration_10" placeholder="Duration">
                </div>
                <div class="form-group">
                    <label>No. of sittings</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->sitting ?>"
                        name="treatmentOptionSitting_10" placeholder="No. of sittings">
                </div>
                <div class="form-group">
                    <label>Book Now</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->bookNow ?>"
                        name="treatmentOptionBookNow_10" placeholder="Book Now Link">
                </div>
                <div class="form-group">
                    <label>Watch Video</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->treatment_option[9]->watchVideo ?>"
                        name="treatmentOptionWatchVideo_10" placeholder="Watch Video Link">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->before_after->header ?>" name="berforeAfterHeader"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Sub Header</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->before_after->subHeader ?>" name="berforeAfterSubHeader"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="berforeAfterDescription" class="form-control inside-shadow"
                        placeholder="Description"><?php echo $serviceSection->before_after->description ?></textarea>
                </div>
                <div class="form-group">
                    <label>Video link</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->before_after->videoLink ?>" name="berforeAfterVideLink"
                        placeholder="Video link">
                </div>
                <div class="row">

                    <div class="col-6">
                        <h4>Before Image</h4>
                        <div class="form-group">
                            <label>Before image</label>
                            <input type="file" name="before" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
                        </div>
                    </div>

                    <div class="col-6">
                        <h4>After Image</h4>
                        <div class="form-group">
                            <label>After image</label>
                            <input type="file" name="after" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
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
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->why_choose_us->header ?>" name="whyChooseTitle"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control inside-shadow"
                        value="<?php echo $serviceSection->why_choose_us->description ?>" name="whyChooseDescription"
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
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo $serviceSection->why_choose_us->steps[0]->description ?>"
                                name="stepHeader_1" placeholder="Step 1 Description">
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
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo $serviceSection->why_choose_us->steps[1]->description ?>"
                                name="stepHeader_2" placeholder="Step 2 Description">
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
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo $serviceSection->why_choose_us->steps[2]->description ?>"
                                name="stepHeader_3" placeholder="Step 3 Description">
                        </div>
                    </div>
                </div>
            </section>

            <?php
            } else {
                ?>


            <div class="form-group fullscreen-editor">
                <textarea name="description"
                    class="editor inside-shadow"><?php echo html_entity_decode($service_description); ?></textarea>
            </div>
            <section>
                <h3>Advance Options for digital marketing</h3>
                <ul class='tabs'>
                    <li id="seo" class='active'>Search Engine Optimization</li>
                    <li id="fb">Facebook</li>
                    <li id="tw">Twitter</li>
                </ul>
                <div class="seo generalTab">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" value="<?php echo $meta_title; ?>" id="meta_title"
                            class="form-control inside-shadow" placeholder="Meta title">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                            placeholder="Meta Description"><?php echo $meta_description ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                            placeholder="Enter keywords of the service ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow"
                            value="<?php echo $meta_canonical; ?>" id="canonical"
                            placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this service in search results?</label>
                        <select name="robots" class="form-control inside-shadow">
                            <option value="index, follow"
                                <?php echo (($meta_robots=='index, follow') ? 'selected' : '' ) ?>>Yes</option>
                            <option value="noindex, nofollow"
                                <?php echo (($meta_robots=='noindex, nofollow') ? 'selected' : '' ) ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="fb generalTab" style="display: none;">
                    <div class="facebookPreviewBox" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;">
                        <input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
                        <div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
                        <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                    </div>
                    <div class="facebookUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
                        <span>Set facebook feature image</span>
                        <input type='file' id="facebookImage" name='facebookImage' />
                    </div>
                    <div class="form-group">
                        <label>Facebook Title</label>
                        <input type="text" name="fb_title" class="form-control inside-shadow"
                            value="<?php echo $fb_title ?>" placeholder="Facebook title">
                    </div>
                    <div class="form-group">
                        <label>Facebook Description</label>
                        <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                            placeholder="Facebook Description"><?php echo $fb_desc ?></textarea>
                    </div>
                </div>
                <div class="tw generalTab" style="display: none;">
                    <div class="twitterPreviewBox" style="display: <?php echo (($tw_img=='') ? 'none' : 'block' ) ?>;">
                        <input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
                        <div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
                        <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                    </div>
                    <div class="twitterUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
                        <span>Set Twitter feature image</span>
                        <input type='file' id="twitterImage" name='twitterImage' />
                    </div>
                    <div class="form-group">
                        <label>Twitter Title</label>
                        <input type="text" name="tw_title" class="form-control inside-shadow"
                            value="<?php echo $tw_title; ?>" placeholder="Twitter title">
                    </div>
                    <div class="form-group">
                        <label>Twitter Description</label>
                        <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                            placeholder="Twitter Description"><?php echo $tw_desc ?></textarea>
                    </div>
                </div>
            </section>

            <?php
            }

            ?>
            <section>
                <h3>Advance Options for digital marketing</h3>
                <ul class='tabs'>
                    <li id="seo" class='active'>Search Engine Optimization</li>
                    <li id="fb">Facebook</li>
                    <li id="tw">Twitter</li>
                </ul>
                <div class="seo generalTab">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" value="<?php echo $meta_title; ?>" id="meta_title"
                            class="form-control inside-shadow" placeholder="Meta title">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                            placeholder="Meta Description"><?php echo $meta_description ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                            placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow"
                            value="<?php echo $meta_canonical; ?>" id="canonical"
                            placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this post in search results?</label>
                        <select name="robots" class="form-control inside-shadow">
                            <option value="index, follow"
                                <?php echo (($meta_robots=='index, follow') ? 'selected' : '' ) ?>>Yes</option>
                            <option value="noindex, nofollow"
                                <?php echo (($meta_robots=='noindex, nofollow') ? 'selected' : '' ) ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="fb generalTab" style="display: none;">
                    <div class="facebookPreviewBox" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;">
                        <input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
                        <div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
                        <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                    </div>
                    <div class="facebookUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
                        <span>Set facebook feature image</span>
                        <input type='file' id="facebookImage" name='facebookImage' />
                    </div>
                    <div class="form-group">
                        <label>Facebook Title</label>
                        <input type="text" name="fb_title" class="form-control inside-shadow"
                            value="<?php echo $fb_title ?>" placeholder="Facebook title">
                    </div>
                    <div class="form-group">
                        <label>Facebook Description</label>
                        <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                            placeholder="Facebook Description"><?php echo $fb_desc ?></textarea>
                    </div>
                </div>
                <div class="tw generalTab" style="display: none;">
                    <div class="twitterPreviewBox" style="display: <?php echo (($tw_img=='') ? 'none' : 'block' ) ?>;">
                        <input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
                        <div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
                        <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                    </div>
                    <div class="twitterUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
                        <span>Set Twitter feature image</span>
                        <input type='file' id="twitterImage" name='twitterImage' />
                    </div>
                    <div class="form-group">
                        <label>Twitter Title</label>
                        <input type="text" name="tw_title" class="form-control inside-shadow"
                            value="<?php echo $tw_title; ?>" placeholder="Twitter title">
                    </div>
                    <div class="form-group">
                        <label>Twitter Description</label>
                        <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                            placeholder="Twitter Description"><?php echo $tw_desc ?></textarea>
                    </div>
                </div>
            </section>

        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="button" id="updatePost" class="btn btn-green btn-block"
                    value=" <?php echo (($service_status==1) ? 'Update ' : 'Publish ' ); ?>service">
                <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name='serviceStatus' value='<?php echo $service_status ?>'>
                <input type="hidden" name="id" value="<?php echo $service_id ?>">
                <input type="hidden" name="postType" value="<?php echo $service_type ?>">
                <a href='<?php echo SITE_URL.$link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View Live
                    treatment</a>
            </div>
            <div class="form-group">
                <h2>Featured</h2>
                <div class="action-box inside-shadow" style="padding: 10px">
                    <input type="checkbox" value="1" name='featurePost'
                        <?php echo (($link_is_featured=='1') ? 'checked' : '' ) ?>> Mark as feature
                </div>
            </div>
            <h2>Categories</h2>
            <div class="action-box height-200 inside-shadow">
                <?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta WHERE category_site_id='".SITE_ID."' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='".rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')."' AND link_site_id=".SITE_ID)) {
						while($category=mysqli_fetch_assoc($categoryList)) {
							if($category['category_id']==${'service_category'}) { ?>
                <div class="form-group">
                    <input type="radio" name="category"
                        value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>"
                        <?php echo (($category['category_id']==$service_category) ? 'checked' : '' ) ?>>
                    <?php echo ucwords($category['category_title']); ?>
                </div>
                <?php
							} else { ?>
                <div class="form-group">
                    <input type="radio" name="category"
                        value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>">
                    <?php echo ucwords($category['category_title']); ?>
                </div>
                <?php
							}
						}
					}
				?>
            </div>
            <h2>Tags</h2>
            <?php
				$tagserviceID=(($service_parent_id==0) ? $service_id : $service_parent_id );
				
				if($tagList=mysqli_query($connection, "SELECT tag_title FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$tagserviceID}' AND tag_site_id='".SITE_ID."' AND tag_type='service'")) {
					if(mysqli_num_rows($tagList)!=0) {
						while($tagsName=mysqli_fetch_assoc($tagList)) {
							$tags[]=$tagsName['tag_title'];
						}
						$tags=implode(',', $tags);
					} else {
						$tags='';
					}
				}
			?>
            <div class="action-box inside-shadow">
                <div class="form-group">
                    <input type="text" class='tags' />
                    <input type="hidden" id="tagValue" value="<?php echo $tags; ?>">
                    <input type="hidden" id='tags' name="tags" value=''>
                </div>
            </div>
            <div class="tagList" style="display: none;"></div>
            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn"
                style="display: <?php echo (($service_image!='') ? 'none' : 'block') ?>;">
                <span>Set Feature Image</span>
                <input type="hidden" name="oldFeatureImage" value="<?php echo $service_image ?>">
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload
                        limit is 300 MB</strong></p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box"
                style="display: <?php echo (($service_image!='') ? 'block' : 'none') ?>;">
                <img src="//<?php echo SITE_DOMAIN.$service_image ?>" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>