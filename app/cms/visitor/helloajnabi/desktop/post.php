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
							<a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
							<a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
							<a href="#" class="google"><i class="fa fa-google-plus"></i></a>
						</div>
						<div class="article-main-img">
							<img src="<?php echo $post_image ?>" alt="">
						</div>
						<div class="article-body">
								<ul class="article-info">
									<li class="article-category"><?php echo $category_title ?></li>
								</ul>
								<h1 class="article-title"><?php echo $post_title ?></h1>
								<ul class="article-meta">
									<li><i class="fa fa-clock-o"></i> <?php echo date('M d, Y', strtotime($link_published)) ?></li>
									<li><i class="fa fa-comments"></i> 33</li>
								</ul>
								<p>Facilisi complectitur eos eu. Est tritani argumentum in, ei suas ignota admodum vim, ipsum choro has ut. Ei vim noluisse luptatum, nominavi mandamus qui ut. Ne usu lucilius mnesarchum, vim ex nisl summo expetenda, in dicta appareat usu. Ea cum altera fuisset adipisci, in sed eius tacimates, eu duo magna numquam placerat.</p>
								<p>Sea at dolorum nominavi adipiscing, ei eam mundi legimus, sit deleniti definiebas et. Pri dicit latine reformidans ne, offendit rationibus incorrupte an qui, eum populo molestie tacimates te. Nec ea facer vituperatoribus, cu pro feugiat minimum platonem. Elit accusam ei per. Duis illum est ut.</p>
								<p>Ex eos esse sale eligendi. Eos ut exerci audire nostrum, at pro dolores tacimates voluptaria. Facete disputando at quo, omittantur philosophia id qui. Ad labore facete suscipiantur sed. Cu iisque sanctus inciderint has, per quodsi liberavisse ea.</p>
								<p>Sit falli nonumes atomorum ex, ipsum populo iisque eum at. Sumo solet omnium eum ad, quis omnium ut ius, volumus splendide sed ad. Mea vide dicta ne, appareat patrioque has an. Wisi sale delectus eum eu, corpora salutatus no sit. Sale interesset eu per.</p>
								<h4>Sit falli nonumes atomorum ex, ipsum populo iisque eum at</h4>
								<p>Facilisi complectitur eos eu. Est tritani argumentum in, ei suas ignota admodum vim, ipsum choro has ut. Ei vim noluisse luptatum, nominavi mandamus qui ut. Ne usu lucilius mnesarchum, vim ex nisl summo expetenda, in dicta appareat usu. Ea cum altera fuisset adipisci, in sed eius tacimates, eu duo magna numquam placerat.</p>
								<p>Sea at dolorum nominavi adipiscing, ei eam mundi legimus, sit deleniti definiebas et. Pri dicit latine reformidans ne, offendit rationibus incorrupte an qui, eum populo molestie tacimates te. Nec ea facer vituperatoribus, cu pro feugiat minimum platonem. Elit accusam ei per. Duis illum est ut.</p>
								<p>Ex eos esse sale eligendi. Eos ut exerci audire nostrum, at pro dolores tacimates voluptaria. Facete disputando at quo, omittantur philosophia id qui. Ad labore facete suscipiantur sed. Cu iisque sanctus inciderint has, per quodsi liberavisse ea.</p>
								<p>Sit falli nonumes atomorum ex, ipsum populo iisque eum at. Sumo solet omnium eum ad, quis omnium ut ius, volumus splendide sed ad. Mea vide dicta ne, appareat patrioque has an. Wisi sale delectus eum eu, corpora salutatus no sit. Sale interesset eu per.</p>
							</div>
						</article>
						<!-- /ARTICLE POST -->
						
						<!-- widget tags -->
						<div class="widget-tags">
							<ul>
								<li><a href="#">News</a></li>
								<li><a href="#">Sport</a></li>
								<li><a href="#">Lifestyle</a></li>
								<li><a href="#">Fashion</a></li>
								<li><a href="#">Music</a></li>
								<li><a href="#">Business</a></li>
							</ul>
						</div>
						<!-- /widget tags -->
						
						<!-- article comments -->
						<div class="article-comments">
							<div class="section-title">
								<h2 class="title">Comments</h2>
							</div>
								
							<!-- comment -->
							<div class="media">
								<div class="media-left">
									<img src="img/av-1.jpg" alt="">
								</div>
								<div class="media-body">
									<div class="media-heading">
										<h5>John Doe <span class="reply-time">April 04, 2017 At 9:30 AM</span></h5>
									</div>
									<p>Eu usu aliquip vivendo. Impedit suscipit invidunt te vel, sale periculis id mea. Ne nec atqui paulo,</p>				
									<a href="#" class="reply-btn">Reply</a>
								</div>
								
								<!-- comment -->
								<div class="media">
									<div class="media-left">
										<img src="img/av-2.jpg" alt="">
									</div>
									<div class="media-body">
										<div class="media-heading">
											<h5>John Doe <span class="reply-time">April 04, 2017 At 9:30 AM</span></h5>
										</div>
										<p>Eu usu aliquip vivendo. Impedit suscipit invidunt te vel, sale periculis id mea. Ne nec atqui paulo,</p>				
										<a href="#" class="reply-btn">Reply</a>
									</div>
									
									<!-- comment -->
									<div class="media">
										<div class="media-left">
											<img src="img/av-1.jpg" alt="">
										</div>
										<div class="media-body">
											<div class="media-heading">
												<h5>John Doe <span class="reply-time">April 04, 2017 At 9:30 AM</span></h5>
											</div>
											<p>Eu usu aliquip vivendo. Impedit suscipit invidunt te vel, sale periculis id mea. Ne nec atqui paulo,</p>				
											<a href="#" class="reply-btn">Reply</a>
										</div>
									</div>
									<!-- /comment -->
								</div>
								<!-- /comment -->
							</div>
							<!-- /comment -->
							
							<!-- comment -->
							<div class="media">
								<div class="media-left">
									<img src="img/av-2.jpg" alt="">
								</div>
								<div class="media-body">
									<div class="media-heading">
										<h5>John Doe <span class="reply-time">April 04, 2017 At 9:30 AM</span></h5>
									</div>
									<p>Eu usu aliquip vivendo. Impedit suscipit invidunt te vel, sale periculis id mea. Ne nec atqui paulo,</p>				
									<a href="#" class="reply-btn">Reply</a>
								</div>
							</div>
							<!-- /comment -->
						</div>
						<!-- /article comments -->
						
						<!-- reply form -->
						<div class="article-reply-form">
							<div class="section-title">
								<h2 class="title">Leave a reply</h2>
							</div>
								
							<form>
								<input class="input" placeholder="Name" type="text">
								<input class="input" placeholder="Email" type="email">
								<input class="input" placeholder="Website" type="url">
								<textarea class="input" placeholder="Message"></textarea>
								<button class="input-btn">Send Message</button>
							</form>
						</div>
						<!-- /reply form -->
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
								<li><a href="#" class="facebook"><i class="fa fa-facebook"></i><br><span>Facebook</span></a></li>
								<li><a href="#" class="twitter"><i class="fa fa-twitter"></i><br><span>Twitter</span></a></li>
								<li><a href="#" class="google"><i class="fa fa-google"></i><br><span>Google+</span></a></li>
								<li><a href="#" class="instagram"><i class="fa fa-instagram"></i><br><span>Instagram</span></a></li>
								<li><a href="#" class="youtube"><i class="fa fa-youtube"></i><br><span>Youtube</span></a></li>
								<li><a href="#" class="rss"><i class="fa fa-rss"></i><br><span>RSS</span></a></li>
							</ul>
						</div>
						<!-- /social widget -->
						
						<!-- subscribe widget -->
						<div class="widget subscribe-widget">
							<div class="widget-title">
								<h2 class="title">Subscribe to Newslatter</h2>
							</div>
							<form>
								<input class="input" type="email" placeholder="Enter Your Email">
								<button class="input-btn">Subscribe</button>
							</form>
						</div>
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