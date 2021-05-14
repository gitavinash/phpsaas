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

						<div id="M575956ScriptRootC891767"></div>

						<!-- This belongs to <body/> -->
						<fw-embed-feed
						app_id="oOQUHCT4_aavCGIpi1jCIUqL_liYVxH2"
						mode="grid"
						open_in="_iframe"
						max_videos="10"
						placement="middle"
						></fw-embed-feed>
						
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
						
						<?php require_once 'post-sidebar.php'; ?>

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