<?php

    require_once 'header.php';

?>

		<div id="owl-carousel-1" class="owl-carousel owl-theme center-owl-nav">
		
			<?php
			
				// Feature post slider

				if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_is_featured=1 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 5")) {

					if(mysqli_num_rows($featurePostSlider)!=0) {

						while($featurePostSliderData=mysqli_fetch_assoc($featurePostSlider)) {

							extract($featurePostSliderData); ?>

								<article class="article thumb-article">
									<div class="article-img">
										<img src="<?php echo $post_image ?>" alt="">
									</div>
									<div class="article-body_slider">
										<ul class="article-info">
											<li class="article-category"><a href="<?php echo $link_url ?>"><?php echo $category_title ?></a></li>
										</ul>
										<h2 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h2>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
											<!-- <li><i class="fa fa-comments"></i> 33</li> -->
										</ul>
									</div>
								</article>

							<?php

						}

					}

				}

			?>

		</div>
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-title">
							<h2 class="title">Recent Posts</h2>
						</div>
						<div class="tab-content">
							<div id="tab1" class="tab-pane fade in active">
								<div class="row">

									<?php

									// Recent post

									if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 4")) {

										if(mysqli_num_rows($recentPost)!=0) {

											while($recentPostData=mysqli_fetch_assoc($recentPost)) {

												extract($recentPostData); ?>

													<div class="col-md-3 col-sm-6">
														<article class="article">
															<div class="article-img">
																<a href="<?php echo $link_url ?>">
																	<img src="<?php echo $post_image ?>" alt="">
																</a>
															</div>
															<div class="article-body">
																<h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
																<ul class="article-meta">
																	<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
																</ul>
															</div>
														</article>
													</div>

												<?php

											}

										}
									}

									?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-6 col-sm-6">
								<div class="section-title">
									<h2 class="title">Sports</h2>
								</div>

								<?php
									
									// News category post

									if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category='54' AND meta_type='post' ORDER BY link_published DESC LIMIT 0, 3")) {

										if(mysqli_num_rows($categoryPost)!=0) {

											$i=0;

											while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

												extract($categoryPostData);
												
												if($i==0) { ?>

													<div class="titlealign">
														<article class="article">
															<div class="article-img">
																<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
																</a>
															</div>
															<div class="article-body">
																<h3 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h3>
																<ul class="article-meta">
																	<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
																</ul>
																<p><?php echo $meta_description ?></p>
															</div>
														</article>
													</div>

												<?php
												} else { ?>

													<article class="article widget-article">
														<div class="article-img">
															<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
															</a>
														</div>
														<div class="article-body">
															<h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
															<ul class="article-meta">
																<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
															</ul>
														</div>
													</article>

												<?php
												}

												$i++;

											}

										}
									}

								?>

							</div>
							<div class="col-md-6 col-sm-6">
								<div class="section-title">
									<h2 class="title">Bollywood &amp; TV</h2>
								</div>
								<?php
									
									// News category post

									if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category=52 AND meta_type='post' ORDER BY link_published DESC LIMIT 0, 3")) {

										if(mysqli_num_rows($categoryPost)!=0) {

											$i=0;

											while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

												extract($categoryPostData);
												
												if($i==0) { ?>

													<div class="titlealign">
														<article class="article">
															<div class="article-img">
																<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
																</a>
															</div>
															<div class="article-body">
																<h3 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h3>
																<ul class="article-meta">
																	<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
																</ul>
																<p><?php echo $meta_description ?></p>
															</div>
														</article>
													</div>

												<?php
												} else { ?>

													<article class="article widget-article">
														<div class="article-img">
															<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
															</a>
														</div>
														<div class="article-body">
															<h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
															<ul class="article-meta">
																<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
															</ul>
														</div>
													</article>

												<?php
												}

												$i++;

											}

										}
									}

								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="section-title">
									<h2 class="title">Spiritual</h2>
								</div>
							</div>
							<?php
									
								// News category post

								if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND meta_type='post' AND link_site_id=".SITE_ID." AND post_category=73 ORDER BY link_published DESC LIMIT 0, 5")) {

									if($categoryPostSize=mysqli_num_rows($categoryPost)!=0) {

										$i=0;

										while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

											extract($categoryPostData);
												
											if($i==0 || $i==1) { ?>

												<div class="col-md-6 col-sm-6">
													<article class="article">
														<div class="article-img">
															<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
															</a>
														</div>
														<div class="article-body">
															<h3 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h3>
															<ul class="article-meta">
																<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
															</ul>
															<p><?php echo $meta_description ?></p>
														</div>
													</article>
												</div>

											<?php

												if($i==1) {
													echo "</div>";
												}

											} else {
												
												if($i==2) {
													echo '<div class="row">';
												}

											?>

											<div class="col-md-4 col-sm-4">
												<article class="article">
													<div class="article-img">
														<a href="<?php echo $link_url ?>">
															<img src="<?php echo $post_image ?>" alt="">
														</a>
													</div>
													<div class="article-body">
														<h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
														<ul class="article-meta">
															<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
														</ul>
													</div>
												</article>
											</div>
											<?php

												if($i==4) {
													echo '</div>';
												}
												
											}

											$i++;

										}

									}

								}

							?>
							
					</div>
					<div class="col-md-4">
						<div class="widget center-block hidden-xs">
							<img class="center-block" src="<?php echo USER_IMAGES ?>ad-1.jpg" alt=""> 
						</div>
						<div class="widget social-widget">
							<div class="widget-title">
								<h2 class="title">Stay Connected</h2>
							</div>
							<ul>
								<li><a href="https://www.facebook.com/helloajnabi/" class="facebook"><i class="fa fa-facebook"></i><br><span>Facebook</span></a></li>
								<li><a href="https://twitter.com/Hello_Ajnabi" class="twitter"><i class="fa fa-twitter"></i><br><span>Twitter</span></a></li>
								<li><a href="https://www.instagram.com/helloajnabi/" class="instagram"><i class="fa fa-instagram"></i><br><span>Instagram</span></a></li>
								<li><a href="https://www.youtube.com/channel/UC507HwLRcMaAqUNFTzSEK3w" class="youtube"><i class="fa fa-youtube"></i><br><span>Youtube</span></a></li>
							</ul>
						</div>
						<!-- <div class="widget subscribe-widget">
							<div class="widget-title">
								<h2 class="title">Subscribe to Newslatter</h2>
							</div>
							<form>
								<input class="input" type="email" placeholder="Enter Your Email">
								<button class="input-btn">Subscribe</button>
							</form>
						</div> -->
						<div class="widget">
							<div class="widget-title">
								<h2 class="title">Interesting</h2>
							</div>
							
							<?php

								// Recent post

								if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category=58 ORDER BY link_published DESC LIMIT 0, 4")) {

									if(mysqli_num_rows($recentPost)!=0) {

										$i=0;

										while($recentPostData=mysqli_fetch_assoc($recentPost)) {
											
											extract($recentPostData);
											
											if($i=0) {
												echo '<div id="owl-carousel-3" class="owl-carousel owl-theme center-owl-nav">';
											}

											?>

												<div class="owl-item cloned" style="width: 360px;">
													<article class="article">
														<div class="article-img">
															<a href="<?php echo $link_url ?>">
																<img src="<?php echo $post_image ?>" alt="">
															</a>
														</div>
														<div class="article-body">
															<h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
															<ul class="article-meta">
																<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
															</ul>
														</div>
													</article>
												</div>

											<?php

											if($i==3) {
												echo "</div>";
											}

										}

									}
									
								}

							?>
							
					</div>
				</div>
			</div>
		</div>
		<div class="visible-lg visible-md">
			<img class="center-block" src="<?php echo USER_IMAGES ?>ad-3.jpg" alt="">
		</div>
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-title">
							<h2 class="title">Popular Video</h2>
							<div id="nav-carousel-2" class="custom-owl-nav pull-right"></div>
						</div>
						<div id="owl-carousel-2" class="owl-carousel owl-theme">

						<?php

							// Recent post

							if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category=68 ORDER BY link_published DESC LIMIT 0, 4")) {

								if(mysqli_num_rows($recentPost)!=0) {

									$i=0;

									while($recentPostData=mysqli_fetch_assoc($recentPost)) {
										
										extract($recentPostData); ?>

											<article class="article thumb-article">
												<div class="article-img">
													<img src="<?php echo $post_image ?>" alt="">
												</div>
												<div class="article-body">
													<ul class="article-info">
														<li class="article-category"><a href="<?php echo $link_url ?>"><?php echo $category_title ?></a></li>
													</ul>
													<h3 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h3>
													<ul class="article-meta">
														<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
													</ul>
												</div>
											</article>

										<?php

									}

								}
								
							}

							?>	

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						<div class="section-title">
							<h2 class="title">Food</h2>
						</div>

						<?php
									
							// Feature post slider

							if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND post_category=63 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 5")) {

								if(mysqli_num_rows($featurePostSlider)!=0) {

									while($featurePostSliderData=mysqli_fetch_assoc($featurePostSlider)) {

										extract($featurePostSliderData); ?>

											<article class="article row-article">
												<div class="article-img">
													<a href="<?php echo $link_url ?>">
														<img src="<?php echo $post_image ?>" alt="">
													</a>
												</div>
												<div class="article-body">
													<ul class="article-info">
														<li class="article-category"><a href="<?php echo $link_url ?>"><?php echo $category_title ?></a></li>
													</ul>
													<h3 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h3>
													<ul class="article-meta">
														<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
													</ul>
													<p><?php echo $meta_description ?></p>
												</div>
											</article>

										<?php

									}

								}

							}

						?>

					</div>
					<div class="col-md-4">
						<div class="widget">
							<div class="widget-title">
								<h2 class="title">Featured Posts</h2>
							</div>
							<div id="owl-carousel-4" class="owl-carousel owl-theme">

								<?php
									
									// Feature post slider

									if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_is_featured=1 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 5")) {

										if(mysqli_num_rows($featurePostSlider)!=0) {

											while($featurePostSliderData=mysqli_fetch_assoc($featurePostSlider)) {

												extract($featurePostSliderData); ?>

													<article class="article thumb-article">
														<div class="article-img">
															<img src="<?php echo $post_image ?>" alt="">
														</div>
														<div class="article-body_slider">
															<ul class="article-info">
																<li class="article-category"><a href="<?php echo $link_url ?>"><?php echo $category_title ?></a></li>
															</ul>
															<h2 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h2>
															<ul class="article-meta">
																<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
																<!-- <li><i class="fa fa-comments"></i> 33</li> -->
															</ul>
														</div>
													</article>

												<?php

											}

										}

									}

								?>
							</div>
						</div>
						<div class="widget tweets-widget">
							<div class="widget-title">
								<h2 class="title">Facebook page feed</h2>
							</div>

							<div id="fb-root"></div>
							<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v10.0&appId=1712739128907798&autoLogAppEvents=1" nonce="RJPz3cKU"></script>

							<div class="fb-page" data-href="https://www.facebook.com/helloajnabi" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/helloajnabi" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/helloajnabi">Hello Ajnabi</a></blockquote></div>

						</div>
					</div>
				</div>
			</div>
		</div>

<?php

    require_once 'footer.php';

?>