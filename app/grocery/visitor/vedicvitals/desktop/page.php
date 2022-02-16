<?php

	require_once 'header.php';

?>

		<!-- section global platform start -->
		<section class="banner-section">
			<div class="container-fluid">
				<div class="row">
					<div class="card-ban">
						<div class="baner-single">
							<img src="<?php echo $page_image ?>">
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- garanty start -->

		<section class="section-content our-story-section" style="background: url(image/about-us-background.jpg) center center no-repeat; background-size: cover; margin-bottom: -10px" >
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="head-1">
							<h2><?php echo $page_title ?></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<?php echo $page_description ?>
				</div>
			</div>
		</section>
		
<?php

	require_once 'footer.php';

?>