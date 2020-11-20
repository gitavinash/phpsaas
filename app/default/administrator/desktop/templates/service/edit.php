<?php

	if($serviceDetails=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."service, ".TABLE_PREFIX."inventory, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta, ".TABLE_PREFIX."category WHERE category_id=service_category AND inventory_relation_id=service_id AND link_type='service' AND link_relation_id=service_id AND meta_relation_id=link_id AND service_id='{$id}' AND link_status!=301 AND link_type='service' LIMIT 1")) {
		if(mysqli_num_rows($serviceDetails)!=0) {
			extract(mysqli_fetch_assoc($serviceDetails));
		} else { ?>
			<script type="text/javascript">
				alert('Invalid action found');
				window.location.href='dashboard/service/?type=list&status=all';
			</script>
		<?php
		}
	}

?>
<h2>Update <?php echo (($link_status=='206') ? 'Drafted ' : '').ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> <span><a href="service/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span></h2>

<div class="form-box">
	<form id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="form-group">
				<input type="text" class="form-control inside-shadow" name="title" placeholder="Title" value="<?php echo $service_title ?>" required="" >
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
				<textarea name="description" class="editor inside-shadow" ><?php echo html_entity_decode($service_description); ?></textarea>
			</div>

			<!-- service description and other things -->

			<section style="margin-bottom:20px" >
				<h3>service Details</h3>
				<ul class='tabs'>
					<li id="serviceGeneral" class='active' >General</li>
					<li id="serviceInventory" >Inventory</li>
					<li id="serviceShipping" >Shipping</li>
					<li id="serviceGallery" >Gallery</li>
					<li id="serviceAdditionalDetails" >Additional Details</li>
				</ul>
				<div class="serviceGeneral generalTab">
					<div class="form-group">
						<label>service short description / Highlights</label>
						<textarea name="highlight" class="form-control inside-shadow" placeholder="service short description / Highlights" ><?php echo $service_highlight ?></textarea>
					</div>
				</div>
				<div class="serviceInventory generalTab" style="display: none;">
					
					<div class="box-2">
						<input type="checkbox" name="in_stock" <?php (($inventory_in_stock==1) ? 'checked' : '' ) ?> > In Stock
					</div>
					<div class="box-2">
						<input type="checkbox" name="backorder"  <?php (($inventory_backorder==1) ? 'checked' : '' ) ?> > Allowed Backorders (if service is out of stock )
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>Sale price (₹)</label>
							<input type="number" class="form-control inside-shadow" value="<?php echo $inventory_price ?>" name="salePrice" placeholder="Sale price" >
						</div>
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>Regular price (₹)</label>
							<input type="number" class="form-control inside-shadow" value="<?php echo $inventory_mrp ?>" name="mrp" placeholder="Regular price" >
						</div>
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>SKU</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_sku ?>" name="sku" placeholder="SKU" >
						</div>
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>Stock Alert</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_stock_notice ?>" name="stock_warning" placeholder="Stock Alert" >
						</div>
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>Calculate tax</label>
							<select name="taxStatus" class="form-control inside-shadow" >
								<option value="0" <?php echo (($inventory_tax_status==0) ? 'selected' : ' ') ?> >No</option>
								<option value="1" <?php echo (($inventory_tax_status==1) ? 'selected' : ' ') ?>>Yes</option>
							</select>
						</div>
					</div>
					<div class="box-2">
						<div class="form-group">
							<label>Tax rate ( in % )</label>
							<input type="number" name="taxRate" value="<?php echo $inventory_tax_rate ?>" placeholder="Tax rate ( in % )" class="form-control inside-shadow" >
						</div>
					</div>
				</div>
				<div class="serviceShipping generalTab" style="display: none;">
					<div class="box-1">
						<div class="form-group">
							<label>Weight (kg)</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_weight ?>" name="weight" placeholder="Weight" >
						</div>
					</div>
					<label>Dimensions (cm)</label>
					<div class="form-group">
						<div class="box-3">
							<label>Lenght</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_lenght ?>" name="lenght" placeholder="Lenght" >
						</div>
						<div class="box-3">
							<label>Width</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_width ?>" name="width" placeholder="Width" >
						</div>
						<div class="box-3">
							<label>Height</label>
							<input type="text" class="form-control inside-shadow" value="<?php echo $inventory_height ?>" name="height" placeholder="Height" >
						</div>
					</div>
				</div>
				<div class="serviceGallery generalTab" style="display: none;">
					<input type="hidden" name="oldGallery" value="<?php echo $service_gallery ?>" >
					<input type="file" id="gallery" class="galleryBtn" name="gallery[]" multiple >					
				</div>
				<div class="serviceAdditionalDetails generalTab" style="display: none;">
					
				</div>
			</section>

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
						<textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the service ( use , to seprate keywords )" ><?php echo $meta_keyword ?></textarea>
					</div>
					<div class="form-group">
						<label>Canonical URL</label>
						<input type="text" name="canonical" class="form-control inside-shadow" value="<?php echo $meta_canonical; ?>" id="canonical" placeholder="Actual URL of the service"  >
					</div>
					<div class="form-group">
						<label>Allow search engines to show this service in search results?</label>
						<select name="robots" class="form-control inside-shadow">
							<option value="index, follow" <?php echo (($meta_robots=='index, follow') ? 'selected' : '' ) ?> >Yes</option>
							<option value="noindex, nofollow" <?php echo (($meta_robots=='noindex, nofollow') ? 'selected' : '' ) ?> >No</option>
						</select>
					</div>
				</div>
				<div class="fb" style="display: none;">
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
				<div class="tw" style="display: none;">
					<div class="twitterPreviewBox" style="display: <?php echo (($fb_img=='') ? 'none' : 'block' ) ?>;">
						<input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
						<div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
						<p id="twitterChangeImage" class="changeImage" >Remove twitter feature image</p>
					</div>
					<div class="twitterUploadBox" style="display: <?php echo (($fb_img=='') ? 'block' : 'none' ) ?>;">
						<span>Set twitters feature image</span>
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
				<input type="button" id="updatePost" class="btn btn-green btn-block" value="<?php echo (($service_status==1) ? 'Update ' : 'Publish ' ); ?>service">
				<!-- <input type="submit" class="btn btn-yellow btn-block" value="Save Draft"> -->
				<input type="hidden" name='postStatus' value='<?php echo $service_status ?>' >
				<input type="hidden" name="postType" value="<?php echo $service_type; ?>" >
				<input type="hidden" name="id" value="<?php echo $id ?>" >
				<input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<a href='<?php echo SITE_URL.$link_url; ?>' class='btn btn-block btn-blue' target='_blank' >View live service</a>
			</div>
			<div class="form-group">
				<h2>Fearure service</h2>
				<div class="action-box inside-shadow">
					<input type="checkbox" value="1" name='featureservice' <?php echo (($link_is_featured=='1') ? 'checked' : '' ) ?> > Mark as Feature service
				</div>
			</div>
			<h2>Categories</h2>
			<div class="action-box height-200 inside-shadow">

				<?php

					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_site_id='".SITE_ID."' AND link_type='category' AND link_relation_id=category_id AND link_status=200 ORDER BY category_title ASC")) {
						while($category=mysqli_fetch_assoc($categoryList)) { ?>
							<div class="form-group">
								<input type="radio" name="category" value="<?php echo $category['category_id'].'_'.str_replace('/category', '', $category['link_url']); ?>" <?php echo (($category['category_id']==$category_id) ? 'checked' : '' ) ?> > <?php echo ucwords($category['category_title']); ?>
							</div>
						<?php
						}
					}
				?>
			</div>
			<h2>Tags</h2>
			<?php

				$tagserviceID=(($service_parent_id==0) ? $id : $service_parent_id );
				
				if($tagList=mysqli_query($connection, "SELECT tag_title FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$tagserviceID}' AND tag_site_id='".SITE_ID."'")) {
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
			<div class="form-group featureImageUploadBox" style="display: <?php echo (($service_image!='') ? 'none' : 'block') ?>;">
				<span>Set Feature Image</span>
				<input type="hidden" name="oldFeatureImage" value="<?php echo $service_image ?>">
				<input type="file" id="featureImage" name="featureImage">
				<p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls, .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></p>
			</div>
			<div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo (($service_image!='') ? 'block' : 'none') ?>;">
				<img src="//<?php echo SITE_DOMAIN.$service_image ?>" id="featureImagePath" alt="">
				<p id="changeImage" class="btn btn-red btn-sm" >Remove feature image</p>
			</div>
		</div>
	</form>
</div>