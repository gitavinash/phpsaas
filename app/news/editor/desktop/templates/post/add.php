<h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="form-group">
				<input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" autofocus required >
			</div>
			<div class="form-group">
				<div class="url">
					<p><strong>Permalink</strong></p><p><span><?php echo SITE_DOMAIN ?>/</span></p>
					<input type="text" id="url" class="form-control" name="url" >
				</div>
			</div>
			<div class="form-group fullscreen-editor">
				<textarea name="description" class="editor inside-shadow" ></textarea>
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
						<input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow"  placeholder="Meta title" >
					</div>
					<div class="form-group">
						<label>Meta Description</label>
						<textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow" placeholder="Meta Description" ></textarea>
					</div>
					<div class="form-group">
						<label>Meta Keywords</label>
						<textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the post ( use , to seprate keywords )" ></textarea>
					</div>
					<div class="form-group">
						<label>Canonical URL</label>
						<input type="text" name="canonical" class="form-control inside-shadow" id="canonical" placeholder="Actual URL of the <?php echo $postType ?>" >
					</div>
					<div class="form-group">
						<label>Allow search engines to show this Post in search results?</label>
						<select name="robots" class="form-control inside-shadow">
							<option value="index, follow">Yes</option>
							<option value="noindex, nofollow">No</option>
						</select>
					</div>
				</div>
				<div class="fb" style="display: none;">
					<div class="facebook-image" style="display: none;">
						<div class="img"><img src="" id="facebook-image" alt=""></div>
						<p id="facebookChangeImage" class='changeImage' >Remove facebook feature image</p>
					</div>
					<div class="facebookUploadBox">
						<span>Set facebook feature image</span>
						<input type='file' id="fbImage" name='facebookImage' />
					</div>
					<div class="form-group">
						<label>Facebook Title</label>
						<input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"  placeholder="Facebook title" >
					</div>
					<div class="form-group">
						<label>Facebook Description</label>
						<textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow" placeholder="Facebook Description" ></textarea>
					</div>
				</div>
				<div class="tw" style="display: none;">
					<div class="twitter-image" style="display: none;">
						<div class="img"><img src="" id="twitter-image" alt=""></div>
						<p id="twitterChangeImage" class="changeImage" >Remove twitter feature image</p>
					</div>
					<div class="twitterUploadBox">
						<span>Set twitters feature image</span>
						<input type='file' id="twImage" name='twitterImage' />
					</div>
					<div class="form-group">
						<label>Twitter Title</label>
						<input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"  placeholder="Twitter title" >
					</div>
					<div class="form-group">
						<label>Twitter Description</label>
						<textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow" placeholder="Twitter Description" ></textarea>
						</div>
				</div>
			</section>
		</div>
		<div class="right-box">
			<div class="action-box inside-shadow">
				<input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
				<input type="submit" class="btn btn-yellow btn-block" value="Save Draft">
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
				<input type="hidden" name="type" value="draft" >
				<input type="hidden" name="postType" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<input type="hidden" name="action" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
			</div>
			<div class="form-group">
				<h2>Fearure Post</h2>
				<div class="action-box inside-shadow">
					<input type="checkbox" value="1" name='featurePost' > Mark as Feature Post
				</div>
			</div>
			<h2>Categories</h2>
			<div class="action-box height-200 inside-shadow">
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_site_id='".SITE_ID."' AND link_type='category' AND link_relation_id=category_id AND link_status=200")) {
						while($category=mysqli_fetch_assoc($categoryList)) { ?>
							<div class="form-group">
								<input type="radio" name="category" value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>" <?php echo (($category['category_id']==1) ? 'checked' : '' ) ?> > <?php echo ucwords($category['category_title']); ?>
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
					<input type="hidden" id='tags' name="tags" value='' >
				</div>
			</div>
			<div class="tagList" style="display: none;"></div>
			<h2>Feature Image</h2>
			<div class="form-group upload-btn">
				<span>Set Feature Image</span>
				<input type="file" id="featureImage" name="featureImage">
				<p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls, .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></p>
			</div>
			<div class="img-preview action-box" style="display: none;">
				<img src="#" id="previewImage" alt="">
				<p id="changeImage" class="changeImage" >Remove feature image</p>
			</div>
		</div>
	</form>
</div>