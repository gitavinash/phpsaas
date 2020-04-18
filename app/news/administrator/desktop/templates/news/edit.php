<?php
	
	${$postType.'_id'}=mysqli_real_escape_string($connection, $_GET['id']);

	if($postDetails=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."{$postType}, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta, ".TABLE_PREFIX."category WHERE category_id={$postType}_category AND link_type='{$postType}' AND link_relation_id=${$postType.'_id'} AND meta_relation_id=${$postType.'_id'} AND {$postType}_id='${$postType.'_id'}' AND link_status!=301 AND link_type='{$postType}' LIMIT 1")) {
		if(mysqli_num_rows($postDetails)!=0) {
			extract(mysqli_fetch_assoc($postDetails));
		} else { ?>
			<script type="text/javascript">
				alert('Invalid action found');
				window.location.href='dashboard/news/?type=list&status=all';
			</script>
		<?php
		}
	}

?>
<h2>Update <?php echo (($link_status=='206') ? 'Drafted ' : '').ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="form-group">
				<input type="text" class="form-control inside-shadow" name="title" placeholder="Title" value="<?php echo ${$postType.'_title'} ?>" required="" >
			</div>
			<div class="form-group">
				<div class="url">
					<?php
						$url=explode('/', ltrim(rtrim($link_url, '/'), '/'));
					?>
					<p><strong>Permalink</strong></p><p><span><?php echo SITE_DOMAIN.'/...'; ?>/</span></p>
					<input type="text" id="url" class="form-control" value="<?php echo end($url); ?>" name="url" >
					<input type="hidden" name='oldURL' value='<?php echo $link_url ?>' >
				</div>
			</div>
			<div class="form-group fullscreen-editor">
				<textarea name="description" class="editor inside-shadow" ><?php echo html_entity_decode(${$postType.'_description'}); ?></textarea>
			</div>
			<section>
				<h3>Advance Options for digital marketing</h3>
				<ul class='tabs'>
					<li id="seo" class='active' >Search Engine Optimization</li>
					<li id="fb" >Facebook</li>
					<li id="tw" >Twitter</li>
				</ul>
				<div class="seo">
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
						<textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the post ( use , to seprate keywords )" ><?php echo $meta_keyword ?></textarea>
					</div>
					<div class="form-group">
						<label>Canonical URL</label>
						<input type="text" name="canonical" class="form-control inside-shadow" value="<?php echo $meta_canonical; ?>" id="canonical" placeholder="Actual URL of the <?php echo $postType ?>"  >
					</div>
					<div class="form-group">
						<label>Allow search engines to show this Post in search results?</label>
						<select name="robots" class="form-control inside-shadow">
							<option value="index, follow" <?php echo (($meta_robots=='index, follow') ? 'selected' : '' ) ?> >Yes</option>
							<option value="noindex, nofollow" <?php echo (($meta_robots=='noindex, nofollow') ? 'selected' : '' ) ?> >No</option>
						</select>
					</div>
				</div>
				<div class="fb" style="display: none;">
					<div class="facebook-image" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;" >
						<input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
						<div class="img"><img src="<?php echo $fb_img; ?>" id="facebook-image" alt=""></div>
						<p id="facebookChangeImage" class='changeImage' >Remove facebook feature image</p>
					</div>
					<div class="facebookUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
						<span>Set facebook feature image</span>
						<input type='file' id="fbImage" name='facebookImage' />
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
				<div class="tw" style="display: none;">
					<div class="twitter-image" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;">
						<input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
						<div class="img"><img src="<?php echo $tw_img ?>" id="twitter-image" alt=""></div>
						<p id="twitterChangeImage" class="changeImage" >Remove twitter feature image</p>
					</div>
					<div class="twitterUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
						<span>Set Twitter feature image</span>
						<input type='file' id="twImage" name='twitterImage' />
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
				<div class="publishGroup">
					<input type="button" class="updatePost btn btn-green btn-block" value="<?php echo ((${$postType.'_status'}==1) ? 'Update ' : 'Publish ' ).ucwords($postType); ?>" >
					<input type="submit" class="btn btn-yellow btn-block" value="Save &amp; Update Draft">
					<input type="button" id="scheduleBtn" class="btn btn-blue btn-block" value="Schedule News">
				</div>
				<div class="scheduleGroup" style="display: none;" >
					<input type="button" class="updatePost btn btn-green btn-block" value="<?php echo ((${$postType.'_status'}==1) ? 'Update ' : 'Publish ' ).ucwords($postType); ?>" >
					<span style="float: left; font-size: 12px; color: #333; margin-bottom: 5px;" >Change date and time</span>
					<input type="text" name="scheduleTime" class="form-control inside-shadow datetimepicker" style="margin-bottom: 10px" value="<?php echo date('m/d/Y h:i A', strtotime($link_published)) ?>" placeholder="Click here to select time" >
					<input type="button" id="schedulePost" class="btn btn-green btn-block" value="Schedule News">
				</div>
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
				<input type="hidden" name="type" value="draft" >
				<input type="hidden" name='postStatus' value='<?php echo ${$postType.'_status'} ?>' >
				<input type="hidden" name="postType" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<input type="hidden" name="post_id" value="<?php echo ${$postType.'_id'} ?>" >
				<input type="hidden" name="oldCategory" value="<?php echo ${$postType.'_category'} ?>" >
				<input type="hidden" name="action" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<a href='<?php echo SITE_URL.$link_url; ?>' class='btn btn-block btn-blue' target='_blank' >View Live <?php echo $postType; ?></a>
			</div>
			<div class="form-group">
				<h2>Fearure Post</h2>
				<div class="action-box inside-shadow" style="padding-bottom: 10px" >
					<input type="checkbox" value="1" name='featurePost' <?php echo (($link_is_featured=='1') ? 'checked' : '' ) ?> > Mark as Feature <?php echo ucwords($postType); ?>
				</div>
			</div>
			<h2>Categories</h2>
			<div class="action-box height-200 inside-shadow">
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_site_id='".SITE_ID."' AND link_type='category' AND link_relation_id=category_id AND link_status=200 ORDER BY category_id ASC")) {
						while($category=mysqli_fetch_assoc($categoryList)) {
							if($category['category_id']==${$postType.'_category'}) { ?>
								<div class="form-group">
									<input type="radio" name="category" value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>" <?php echo (($category['category_id']==$category_id) ? 'checked' : '' ) ?> > <?php echo ucwords($category['category_title']); ?>
								</div>
							<?php
							} else { ?>
								<div class="form-group">
									<input type="radio" name="category" value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>" > <?php echo ucwords($category['category_title']); ?>
								</div>
							<?php
							}
						}
					}
				?>
			</div>
			<h2>Tags</h2>
			<?php

				$tagPostID=((${$postType.'_parent_id'}==0) ? ${$postType.'_id'} : ${$postType.'_parent_id'} );
				
				if($tagList=mysqli_query($connection, "SELECT tag_title FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$tagPostID}' AND tag_site_id='".SITE_ID."'")) {
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
					<input type="hidden" id="tagValue" value="<?php echo $tags; ?>" >
					<input type="hidden" id='tags' name="tags" value='' >
				</div>
			</div>
			<div class="tagList" style="display: none;"></div>
			<h2>Feature Image</h2>
			<div class="form-group upload-btn" style="display: <?php echo ((${$postType.'_image'}!='') ? 'none' : 'block') ?>;">
				<span>Set Feature Image</span>
				<input type="hidden" name="oldFeatureImage" value="<?php echo ${$postType.'_image'} ?>">
				<input type="hidden" name="oldThumbnailImage" value="<?php echo ${$postType.'_thumbnail'} ?>">
				<input type="file" id="featureImage" name="featureImage">
				<p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload limit is 300 MB</strong></p>
			</div>
			<div class="img-preview action-box" style="display: <?php echo ((${$postType.'_image'}!='') ? 'block' : 'none') ?>;">
				<img src="//<?php echo SITE_DOMAIN.${$postType.'_image'} ?>" id="previewImage" alt="">
				<p id="changeImage" class="changeImage" >Remove feature image</p>
			</div>
		</div>
	</form>
</div>