
		<footer id="footer">
			<div id="top-footer" class="section">
				<div class="container">
					<div class="row">
						<div class="col-md-4">
							<div class="footer-widget about-widget">
								<div class="footer-logo">
									<a href="#" class="logo"><img src="<?php echo USER_IMAGES ?>logo-alt.png" alt=""></a>
									<p><?php echo SITE_DESC ?></p>
								</div>
							</div>
							<div class="footer-widget social-widget">
								<div class="widget-title">
									<h3 class="title">Follow Us</h3>
								</div>
								<ul>
									<li><a href="https://www.facebook.com/helloajnabi" class="facebook" target="black"><i class="fab fa-facebook"></i></a></li>
									<li><a href="https://twitter.com/Hello_Ajnabi" class="twitter" target="black"><i class="fab fa-twitter"></i></a></li>
									<li><a href="https://www.instagram.com/helloajnabi" class="instagram" target="black"><i class="fab fa-instagram"></i></a></li>
									<li><a href="https://www.youtube.com/channel/UC507HwLRcMaAqUNFTzSEK3w?sub_confirmation=1" class="youtube" target="black"><i class="fab fa-youtube"></i></a></li>
								</ul>
							</div>
						</div>
						<div class="col-md-4">
							<div class="footer-widget">
								<div class="widget-title">
									<h2 class="title">Featured Posts</h2>
								</div>
								<?php
									
									// Feature post slider

									if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_is_featured=1 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 3")) {

										if(mysqli_num_rows($featurePostSlider)!=0) {

											while($featurePostSliderData=mysqli_fetch_assoc($featurePostSlider)) {

												extract($featurePostSliderData); ?>

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

										}

									}

								?>
								
							</div>
						</div>
						<div class="col-md-4">
							<div class="footer-widget tweets-widget">
								<div class="widget-title">
									<h2 class="title">Facebook page</h2>
								</div>
								<div id="fb-root"></div>
								<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v10.0&appId=1712739128907798&autoLogAppEvents=1" nonce="UT7QLpdp"></script>
								<div class="fb-page" data-href="https://www.facebook.com/helloajnabi" data-tabs="timeline" data-width="" data-height="400" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/helloajnabi" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/helloajnabi">Hello Ajnabi</a></blockquote></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="bottom-footer" class="section">
				<div class="container">
					<div class="row">
						<div class="col-md-6 col-md-push-6">
							<ul class="footer-links">
								<li><a href="<?php echo $link_url ?>">About us</a></li>
								<li><a href="<?php echo $link_url ?>">Contact</a></li>
								<li><a href="<?php echo $link_url ?>">Advertisement</a></li>
								<li><a href="<?php echo $link_url ?>">Privacy</a></li>
							</ul>
						</div>
						<div class="col-md-6 col-md-pull-6">
							<div class="footer-copyright">
								<span>All rights reserved | Developed by <a href="https://itssoftworld.com">ITS Soft World</a> </span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
		<div id="back-to-top"></div>

		<?php require_once NOTIFICATION.'index.php'; ?>

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>owlcarousel2/owlcarousel.min.js"></script>
		<script>
			/**
			*  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
			*  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
			/*
			var disqus_config = function () {
			this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
			this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
			};
			*/
			(function() { // DON'T EDIT BELOW THIS LINE
			var d = document, s = d.createElement('script');
			s.src = 'https://hello-ajnabi.disqus.com/embed.js';
			s.setAttribute('data-timestamp', +new Date());
			(d.head || d.body).appendChild(s);
			})();
		</script>
		<script src="<?php echo USER_JS ?>app.min.js"></script>

	</body>
</html>
