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

									echo "<li><a href='/tag/{$tagName[$i]}'>{$tagName[$i]}</a></li>";

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
							
							<!-- owl carousel 3 -->
							<div id="owl-carousel-3" class="owl-carousel owl-theme center-owl-nav">
								<!-- ARTICLE -->
								<article class="article">
									<div class="article-img">
										<a href="#">
											<img src="./img/img-md-3.jpg" alt="">
										</a>
										<ul class="article-info">
											<li class="article-type"><i class="fa fa-file-text"></i></li>
										</ul>
									</div>
									<div class="article-body">
										<h4 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h4>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<!-- /ARTICLE -->
								
								<!-- ARTICLE -->
								<article class="article">
									<div class="article-img">
										<a href="#">
											<img src="./img/img-md-4.jpg" alt="">
										</a>
										<ul class="article-info">
											<li class="article-type"><i class="fa fa-file-text"></i></li>
										</ul>
									</div>
									<div class="article-body">
										<h4 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h4>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<!-- /ARTICLE -->
							</div>
							<!-- /owl carousel 3 -->
							
							<!-- ARTICLE -->
							<article class="article widget-article">
								<div class="article-img">
									<a href="#">
										<img src="./img/img-widget-1.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<!-- /ARTICLE -->
							
							<!-- ARTICLE -->
							<article class="article widget-article">
								<div class="article-img">
									<a href="#">
										<img src="./img/img-widget-2.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<!-- /ARTICLE -->
							
							<!-- ARTICLE -->
							<article class="article widget-article">
								<div class="article-img">
									<a href="#">
										<img src="./img/img-widget-3.jpg" alt="">
									</a>
								</div>
								<div class="article-body">
									<h4 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h4>
									<ul class="article-meta">
										<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
										<li><i class="fa fa-comments"></i> 33</li>
									</ul>
								</div>
							</article>
							<!-- /ARTICLE -->
						</div>
						<!-- /article widget -->
						
						<!-- article widget -->
						<div class="widget">
							<div class="widget-title">
								<h2 class="title">Featured Posts</h2>
							</div>
							
							<!-- owl carousel 4 -->
							<div id="owl-carousel-4" class="owl-carousel owl-theme">
								<!-- ARTICLE -->
								<article class="article thumb-article">
									<div class="article-img">
										<img src="./img/img-thumb-1.jpg" alt="">
									</div>
									<div class="article-body">
										<ul class="article-info">
											<li class="article-category"><a href="#">News</a></li>
											<li class="article-type"><i class="fa fa-video-camera"></i></li>
										</ul>
										<h3 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h3>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<!-- /ARTICLE -->
							
								<!-- ARTICLE -->
								<article class="article thumb-article">
									<div class="article-img">
										<img src="./img/img-thumb-2.jpg" alt="">
									</div>
									<div class="article-body">
										<ul class="article-info">
											<li class="article-category"><a href="#">News</a></li>
											<li class="article-type"><i class="fa fa-video-camera"></i></li>
										</ul>
										<h3 class="article-title"><a href="#">Duis urbanitas eam in, tempor consequat.</a></h3>
										<ul class="article-meta">
											<li><i class="fa fa-clock-o"></i> January 31, 2017</li>
											<li><i class="fa fa-comments"></i> 33</li>
										</ul>
									</div>
								</article>
								<!-- /ARTICLE -->
							</div>
							<!-- /owl carousel 4 -->
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