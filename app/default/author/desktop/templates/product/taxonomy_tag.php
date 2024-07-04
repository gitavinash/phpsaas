<?php

	$id=base64_decode($id);
	
	if($pageDetails=mysqli_query($connection, "SELECT tag_id, tag_title, tag_image, tag_description, tag_type FROM ".TABLE_PREFIX."tag WHERE tag_id={$id} AND tag_site_id=".SITE_ID)) {
		if(mysqli_num_rows($pageDetails)!=0) {
		
            extract(mysqli_fetch_assoc($pageDetails));

            if($checkTagMeta=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."meta WHERE meta_relation_tag='{$tag_title}' AND meta_type='tag' AND meta_site_id=".SITE_ID)) {

                if(mysqli_num_rows($checkTagMeta)!=0) {

                    extract(mysqli_fetch_assoc($checkTagMeta));

                } else {

                    $meta_title='';
                    $meta_description='';
                    $meta_keyword='';
                    $meta_canonical='';
                    $meta_robots='';
                    $fb_title='';
                    $fb_img='';
                    $fb_desc='';
                    $tw_img='';
                    $tw_desc='';
                    $tw_title='';

                }

            }
		
        } else { ?>

			<script type="text/javascript">
				alert('Invalid action found');
				window.location.href='/dashboard/product/?type=tags';
			</script>

		<?php
		}

	}

?>
<h2>Update tag</h2>

<div class="form-box">
	<form method="page" id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="form-group">
				<input type="text" class="form-control inside-shadow" name="title" placeholder="Title" value="<?php echo ${$taxonomy.'_title'} ?>" readonly >
			</div>
			<div class="form-group fullscreen-editor">
				<textarea name="description" class="editor inside-shadow" ><?php echo html_entity_decode(${$taxonomy.'_description'}); ?></textarea>
			</div>
			<section>
				<h3>Advance Options for digital marketing</h3>
				<ul class='tabs'>
					<li id="seo" class='active' >Search Engine Optimization</li>
					<li id="fb" >Facebook</li>
					<li id="tw" >Twitter</li>
				</ul>
				<div class="seo generalTab">
					<div class="form-group">
						<label>Meta Title</label>
						<input type="text" name="meta_title" value="<?php echo $meta_title; ?>" id="meta_title" class="form-control inside-shadow"  placeholder="Meta title" >
					</div>
					<div class="form-group">
						<label>Meta Description</label>
						<textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow" placeholder="Meta Description" ><?php echo $meta_description ?></textarea>
					</div>
					<div class="form-group">
						<label>Meta Keywords</label>
						<textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the page ( use , to seprate keywords )" ><?php echo $meta_keyword ?></textarea>
					</div>
					<div class="form-group">
						<label>Canonical URL</label>
						<input type="text" name="canonical" class="form-control inside-shadow" value="<?php echo $meta_canonical; ?>" id="canonical" placeholder="Actual URL of the <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') ?>"  >
					</div>
					<div class="form-group">
						<label>Allow search engines to show this page in search results?</label>
						<select name="robots" class="form-control inside-shadow">
							<option value="index, follow" <?php echo (($meta_robots=='index, follow') ? 'selected' : '' ) ?> >Yes</option>
							<option value="noindex, nofollow" <?php echo (($meta_robots=='noindex, nofollow') ? 'selected' : '' ) ?> >No</option>
						</select>
					</div>
				</div>
				<div class="fb generalTab"  style="display: none;">
					<div class="facebookPreviewBox" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;" >
						<input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
						<div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
						<p id="facebookChangeImage" class='changeImage' >Remove facebook feature image</p>
					</div>
					<div class="facebookUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
						<span>Set facebook feature image</span>
						<input type='file' id="facebookImage" name='facebookImage' />
					</div>
					<div class="form-group">
						<label>Facebook Title</label>
						<input type="text" name="fb_title" class="form-control inside-shadow" value="<?php echo $fb_title ?>" placeholder="Facebook title" >
					</div>
					<div class="form-group">
						<label>Facebook Description</label>
						<textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow" placeholder="Facebook Description" ><?php echo $fb_desc ?></textarea>
					</div>
				</div>
				<div class="tw generalTab"  style="display: none;">
					<div class="twitterPreviewBox" style="display: <?php echo (($tw_img=='') ? 'none' : 'block' ) ?>;">
						<input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
						<div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
						<p id="twitterChangeImage" class="changeImage" >Remove twitter feature image</p>
					</div>
					<div class="twitterUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
						<span>Set Twitter feature image</span>
						<input type='file' id="twitterImage" name='twitterImage' />
					</div>
					<div class="form-group">
						<label>Twitter Title</label>
						<input type="text" name="tw_title" class="form-control inside-shadow" value="<?php echo $tw_title; ?>" placeholder="Twitter title" >
					</div>
					<div class="form-group">
						<label>Twitter Description</label>
						<textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow" placeholder="Twitter Description" ><?php echo $tw_desc ?></textarea>
					</div>
				</div>
			</section>
		</div>
		<div class="right-box">
			<div class="action-box inside-shadow">
				<input type="button" id="updatePost" class="btn btn-green btn-block" value="Update tag">
				<input type="hidden" name="id" value="<?php echo $tag_id; ?>" >
				<input type="hidden" name="type" value="taxonomy" >
				<input type="hidden" name="taxonomyType" value="<?php echo $taxonomy; ?>" >
				<a href='<?php echo '/tag/'.str_replace(' ', '_', $tag_title).'/'; ?>' class='btn btn-block btn-blue' target='_blank' >View <?php echo $taxonomy ?></a>
			</div>
			<div class="tagList" style="display: none;"></div>
			<h2>Feature Image</h2>
			<div class="form-group featureImageUploadBox upload-btn" style="display: <?php echo ((${$taxonomy.'_image'}!='') ? 'none' : 'block') ?>;">
				<span>Set Feature Image</span>
				<input type="hidden" name="oldFeatureImage" value="<?php echo ${$taxonomy.'_image'} ?>">
				<input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
				<p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Recommended file size maximum 500kb</strong></p>
			</div>
			<div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo ((${$taxonomy.'_image'}!='') ? 'block' : 'none') ?>;">
				<img src="//<?php echo SITE_DOMAIN.${$taxonomy.'_image'} ?>" id="featureImagePath" alt="">
				<p id="featureChangeImage" class="btn btn-red btn-sm" >Remove feature image</p>
			</div>
		</div>
	</form>
</div>