<?php

    include_once 'header.php';

?>
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<ul class="article-breadcrumb">
						<li><a href="/">Home</a></li>
						<li><?php echo $post_title ?></li>
					</ul>
					<article class="article article-post">
						<div class="article-share">
							<a href="https://www.facebook.com/sharer.php?u=<?php echo ACTIVE_SITE_URL ?>" target='_blank' class="facebook"><i class="fab fa-facebook"></i></a>
							<a href="https://twitter.com/intent/tweet?url=<?php echo ACTIVE_SITE_URL ?>" target='_blank' class="twitter"><i class="fab fa-twitter"></i></a>
						</div>
						<div class="article-main-img">
							<img src="<?php echo $post_image ?>" alt="">
						</div>
						<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><a><?php echo $category_title ?></a></li>
								</ul>
								<h1 class="article-title"><?php echo $post_title ?></h1>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?></li>
								</ul>
								<p><?php echo $post_description ?></p>
							</div>
						</article>
						<!-- /ARTICLE POST -->
						
						<!-- widget tags -->
						<div class="widget-tags">
							<ul>
								<?php
								
								$tagName=explode(', ', $tagName);

								for($i=0; $i<sizeof($tagName); $i++) {

									echo "<li><a href='/tag/".str_replace(' ', '-', $tagName[$i])."'>{$tagName[$i]}</a></li>";

								}

								?>
							</ul>
						</div>
						<!-- /widget tags -->
						
						<!-- article comments -->
						<div class="article-comments">
							<div class="section-title">
								<h2 class="title">Comments</h2>
							</div>
							
							<div id="disqus_thread"></div>
							
						</div>
						<!-- /article comments -->
						
					</div>
					<!-- /Main Column -->
					
					<!-- Aside Column -->
					<div class="col-md-4">
						<!-- Ad widget -->
						<div class="widget center-block hidden-xs">
							<img class="center-block" src="./img/ad-1.jpg" alt=""> 
						</div>
						<!-- /Ad widget -->
						
						<!-- social widget -->
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
						<!-- /social widget -->
						
						<!-- /subscribe widget -->
						
						<!-- article widget -->
						<div class="widget">
							<div class="widget-title">
								<h2 class="title">Most Read</h2>
							</div>
							
							<?php

								// Recent post

								if($recentPost=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_status=200 AND link_site_id=".SITE_ID." AND post_category=58 ORDER BY link_published DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($recentPost)!=0) {

										$i=0;

										while($recentPostData=mysqli_fetch_assoc($recentPost)) {
											
											extract($recentPostData); ?>

											<!-- ARTICLE -->
											<article class="article">
												<div class="article-img">
													<a href="#">
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
						<!-- /article widget -->
					</div>
					<!-- /Aside Column -->
				</div>
				<!-- /ROW -->
			</div>
			<!-- /CONTAINER -->
		</div>

<?php

    include_once 'footer.php';

?>