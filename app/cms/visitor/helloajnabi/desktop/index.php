<?php

    require_once 'header.php';

?>

		<div id="owl-carousel-1" class="owl-carousel owl-theme center-owl-nav">
		
			<?php
			
				// Feature post slider

				if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id WHERE link_type='post' AND link_is_featured=1 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 5")) {

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

									if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 4")) {

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

									if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category='54' AND meta_type='post' ORDER BY link_published DESC LIMIT 0, 3")) {

										if(mysqli_num_rows($categoryPost)!=0) {

											$i=0;

											while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

												extract($categoryPostData);
												
												if($i==0) { ?>

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

									if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category=52 AND meta_type='post' ORDER BY link_published DESC LIMIT 0, 3")) {

										if(mysqli_num_rows($categoryPost)!=0) {

											$i=0;

											while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

												extract($categoryPostData);
												
												if($i==0) { ?>

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

								echo "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND meta_type='post' AND link_site_id=".SITE_ID." AND post_category=73 ORDER BY link_published DESC LIMIT 0, 5";
								exit();

								if($categoryPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, meta_description, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_type='post' AND link_status=200 AND meta_type='post' AND link_site_id=".SITE_ID." AND post_category=73 ORDER BY link_published DESC LIMIT 0, 5")) {

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

												if($i==($categoryPostSize-1)) {
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
								<h2 class="title">Most Read</h2>
							</div>
							
							<?php

								// Recent post

								if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_id WHERE link_type='post' AND link_status=200 ORDER BY link_published DESC LIMIT 0, 4")) {

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
							
							<div id="owl-carousel-3" class="owl-carousel owl-theme center-owl-nav">
								<article class="article">
									<div class="article-img">
										<a href="<?php echo $link_url ?>">
											<img src="<?php echo USER_IMAGES ?>img-md-3.jpg" alt="">
										</a>
										<ul class="article-info">
											<li class="article-type"><i class="fa fa-file-text"></i></li>
										</ul>
									</div>
									<div class="article-body">
										<h4 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h4>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<article class="article">
									<div class="article-img">
										<a href="<?php echo $link_url ?>">
											<img src="<?php echo USER_IMAGES ?>img-md-4.jpg" alt="">
										</a>
										<ul class="article-info">
											<li class="article-type"><i class="fa fa-file-text"></i></li>
										</ul>
									</div>
									<div class="article-body">
										<h4 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h4>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
							</div>
							<article class="article widget-article">
								<div class="article-img">
									<a href="<?php echo $link_url ?>">
										<img src="<?php echo USER_IMAGES ?>img-widget-1.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<article class="article widget-article">
								<div class="article-img">
									<a href="<?php echo $link_url ?>">
										<img src="<?php echo USER_IMAGES ?>img-widget-2.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<article class="article widget-article">
								<div class="article-img">
									<a href="<?php echo $link_url ?>">
										<img src="<?php echo USER_IMAGES ?>img-widget-3.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
						</div>
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
							<article class="article thumb-article">
								<div class="article-img">
									<img src="<?php echo USER_IMAGES ?>img-thumb-1.jpg" alt="">
								</div>
								<div class="article-body">
									<ul class="article-info">
										<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
										<li class="article-type"><i class="fa fa-video-camera"></i></li>
									</ul>
									<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<article class="article thumb-article">
								<div class="article-img">
									<img src="<?php echo USER_IMAGES ?>img-thumb-2.jpg" alt="">
								</div>
								<div class="article-body">
									<ul class="article-info">
										<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
										<li class="article-type"><i class="fa fa-video-camera"></i></li>
									</ul>
									<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<article class="article thumb-article">
								<div class="article-img">
									<img src="<?php echo USER_IMAGES ?>img-thumb-3.jpg" alt="">
								</div>
								<div class="article-body">
									<ul class="article-info">
										<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
										<li class="article-type"><i class="fa fa-video-camera"></i></li>
									</ul>
									<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<article class="article thumb-article">
								<div class="article-img">
									<img src="<?php echo USER_IMAGES ?>img-thumb-4.jpg" alt="">
								</div>
								<div class="article-body">
									<ul class="article-info">
										<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
										<li class="article-type"><i class="fa fa-video-camera"></i></li>
									</ul>
									<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
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
							<h2 class="title">Popular Posts</h2>
						</div>
						<article class="article row-article">
							<div class="article-img">
								<a href="<?php echo $link_url ?>">
									<img src="<?php echo USER_IMAGES ?>img-md-1.jpg" alt="">
								</a>
							</div>
							<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
									<li class="article-type"><i class="fa fa-file-text"></i></li>
								</ul>
								<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
									<li><i class="fa fa-comments"></i> 33</li>
								</ul>
								<p>Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis, ne alia sadipscing mei. Te inciderint cotidieque pro, ei iisque docendi qui.</p>
							</div>
						</article>
						<article class="article row-article">
							<div class="article-img">
								<a href="<?php echo $link_url ?>">
									<img src="<?php echo USER_IMAGES ?>img-md-2.jpg" alt="">
								</a>
							</div>
							<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
									<li class="article-type"><i class="fa fa-file-text"></i></li>
								</ul>
								<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
									<li><i class="fa fa-comments"></i> 33</li>
								</ul>
								<p>Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis, ne alia sadipscing mei. Te inciderint cotidieque pro, ei iisque docendi qui.</p>
							</div>
						</article>
						<article class="article row-article">
							<div class="article-img">
								<a href="<?php echo $link_url ?>">
									<img src="<?php echo USER_IMAGES ?>img-md-3.jpg" alt="">
								</a>
							</div>
							<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
									<li class="article-type"><i class="fa fa-file-text"></i></li>
								</ul>
								<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
									<li><i class="fa fa-comments"></i> 33</li>
								</ul>
								<p>Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis, ne alia sadipscing mei. Te inciderint cotidieque pro, ei iisque docendi qui.</p>
							</div>
						</article>
						<article class="article row-article">
							<div class="article-img">
								<a href="<?php echo $link_url ?>">
									<img src="<?php echo USER_IMAGES ?>img-md-4.jpg" alt="">
								</a>
							</div>
							<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
									<li class="article-type"><i class="fa fa-file-text"></i></li>
								</ul>
								<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
									<li><i class="fa fa-comments"></i> 33</li>
								</ul>
								<p>Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis, ne alia sadipscing mei. Te inciderint cotidieque pro, ei iisque docendi qui.</p>
							</div>
						</article>
						<div class="article-pagination">
							<ul> 
								<li class="active"><a href="#" class="active">1</a></li>
								<li><a href="<?php echo $link_url ?>">2</a></li>
								<li><a href="<?php echo $link_url ?>">3</a></li>
								<li><a href="<?php echo $link_url ?>"><i class="fa fa-angle-right"></i></a></li>
							</ul>
						</div>
					</div>
					<div class="col-md-4">
						<div class="widget">
							<div class="widget-title">
								<h2 class="title">Featured Posts</h2>
							</div>
							<div id="owl-carousel-4" class="owl-carousel owl-theme">
								<article class="article thumb-article">
									<div class="article-img">
										<img src="<?php echo USER_IMAGES ?>img-thumb-1.jpg" alt="">
									</div>
									<div class="article-body">
										<ul class="article-info">
											<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
											<li class="article-type"><i class="fa fa-video-camera"></i></li>
										</ul>
										<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<article class="article thumb-article">
									<div class="article-img">
										<img src="<?php echo USER_IMAGES ?>img-thumb-2.jpg" alt="">
									</div>
									<div class="article-body">
										<ul class="article-info">
											<li class="article-category"><a href="<?php echo $link_url ?>">News</a></li>
											<li class="article-type"><i class="fa fa-video-camera"></i></li>
										</ul>
										<h3 class="article-title"><a href="<?php echo $link_url ?>">Duis urbanitas eam in, tempor consequat.</a></h3>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
							</div>
						</div>
						<div class="widget galery-widget">
							<div class="widget-title">
								<h2 class="title">Flickr Photos</h2>
							</div>
							<ul>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-3.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-4.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-5.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-6.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-7.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-8.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-9.jpg" alt=""></a></li>
								<li><a href="<?php echo $link_url ?>"><img src="<?php echo USER_IMAGES ?>img-widget-10.jpg" alt=""></a></li>
							</ul>
						</div>
						<div class="widget tweets-widget">
							<div class="widget-title">
								<h2 class="title">Recent Tweets</h2>
							</div>
							<ul>
								<li class="tweet">
									<i class="fa fa-twitter"></i>
									<div class="tweet-body">
										<p><a href="<?php echo $link_url ?>">@magnews</a> Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis <a href="<?php echo $link_url ?>">https://t.co/DwsTbsmxTP</a></p>
									</div>
								</li>
								<li class="tweet">
									<i class="fa fa-twitter"></i>
									<div class="tweet-body">
										<p><a href="<?php echo $link_url ?>">@magnews</a> Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis <a href="<?php echo $link_url ?>">https://t.co/DwsTbsmxTP</a></p>
									</div>
								</li>
								<li class="tweet">
									<i class="fa fa-twitter"></i>
									<div class="tweet-body">
										<p><a href="<?php echo $link_url ?>">@magnews</a> Populo tritani laboramus ex mei, no eum iuvaret ceteros euripidis <a href="<?php echo $link_url ?>">https://t.co/DwsTbsmxTP</a></p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php

    require_once 'footer.php';

?>