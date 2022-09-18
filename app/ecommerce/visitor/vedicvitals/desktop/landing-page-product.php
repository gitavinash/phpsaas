<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo SITE_URL ?>">
		<meta charset="utf-8">
      	<link rel="shortcut icon" href="<?php echo SITE_ICON ?>">
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.': '.SITE_DESC ) ?></title>

		<!-- css -->
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>landing-page/style.min.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
		<!-- <script src="https://use.fontawesome.com/393c79d8c8.js"></script> -->
		<!-- <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600,900&display=swap" rel="stylesheet"> -->
		<link href="https://fonts.googleapis.com/css2?family=Engagement&display=swap" rel="stylesheet">

		<!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='<?php echo ((isset($meta_robots)) ? $meta_robots : 'index, follow' ) ?>' />
		<link rel="manifest" href="mainfest.json">
		<link rel="apple-touch-icon" href="<?php echo SITE_ICON ?>">
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-55MD564');</script>
		<!-- End Google Tag Manager -->

		<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-176806223-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-176806223-1');
</script>

	</head>
	<body>
		<!-- header start -->
		<header>
			<div class="header-mid">
				<div class="container-fluid">
					<div class="row">
					
						<div class="col-md-12">
							<nav class="navbar navbar-expand-lg menu-top ">
								<div class="logo">
									<a href="/" ><img src="<?php echo USER_IMAGES ?>landing-page/logo.png" class="logo-mb"></a>
								</div>
								<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-icon"></span>
								<span class="navbar-icon"></span>
								<span class="navbar-icon"></span>
								</button>
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-ul">
									  
										<li class="nav-item"><a class="nav-link" href="/">Home</a></li>
										<li class="nav-item dropdown">
											<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Our Products
											</a>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="/product/immunity/immunity-vitals/">Immunity Vitals</a>
												<a class="dropdown-item" href="/product/tar/tar-free/">Tar Free</a>
												<a class="dropdown-item" href="/product/gyno/gyno-vitals/">Gyno Vitals</a>
												<a class="dropdown-item" href="/product/liver/v-liver-vitals/">V-Liver Vitals</a>
											</div>
										</li>
										<li class="nav-item"><a class="nav-link" href="/our-story/">Our Story</a></li>
										<li class="nav-item"><a class="nav-link" href="/blog/">Blogs</a></li>
										<li class="nav-item"><a class="nav-link" href="https://vedicvitals.shiprocket.co/">Track Orders</a></li>
										<li class="nav-item"><a class="nav-link" href="/contact-us/">Contact Us</a></li>

									</ul>


								<div class="search-pro">
									<nav class="navbar navbar-expand-lg login-section justify-content-right ">
										<div class="collapse navbar-collapse" id="navbarNavDropdown">
											<ul class="navbar-nav justify-content-center menu-login">
											  
											  <li> <a href="/checkout/"><img src="<?php echo USER_IMAGES ?>landing-page/bag.png">
											  	<!-- <svg aria-hidden="true" focusable="false" role="presentation" class="icon-a icon-cart" viewBox="0 0 37 40"><path d="M36.5 34.8L33.3 8h-5.9C26.7 3.9 23 .8 18.5.8S10.3 3.9 9.6 8H3.7L.5 34.8c-.2 1.5.4 2.4.9 3 .5.5 1.4 1.2 3.1 1.2h28c1.3 0 2.4-.4 3.1-1.3.7-.7 1-1.8.9-2.9zm-18-30c2.2 0 4.1 1.4 4.7 3.2h-9.5c.7-1.9 2.6-3.2 4.8-3.2zM4.5 35l2.8-23h2.2v3c0 1.1.9 2 2 2s2-.9 2-2v-3h10v3c0 1.1.9 2 2 2s2-.9 2-2v-3h2.2l2.8 23h-28z"></path></svg> --></a>
											  </li>
											</ul>

										</div>
									</nav>
								</div>

								</div>

							</nav>
						</div>

					</div>
				</div>
			</div>
		</header>
		<!-- header end -->

		<section class="banner-section bg-1">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<div class="card-ban">
							<img src="<?php echo USER_IMAGES ?>landing-page/botle1.png">
						</div>
					</div>
					<div class="col-md-6">
						<div class="baner-dtl">
							<div class="hed-bx">
								<h1>Detox</h1>
								<h2>Your Lungs<br>Naturally</h2>
							</div>
							<div class="tag-scrn">
								<select id="qty" style="display:none" >
									<option value="<?php echo $product_id ?>_1_880">3</option>
								</select>
								<a href="#" class="btn-by buyNow">Buy Now</a> <img src="<?php echo USER_IMAGES ?>landing-page/price-tag1.png"><br><span>3Pcs combo</span>
							</div>
							<div class="wrnt">
								<img src="<?php echo USER_IMAGES ?>landing-page/asset1.png">
								<img src="<?php echo USER_IMAGES ?>landing-page/asset2.png">
								<img src="<?php echo USER_IMAGES ?>landing-page/asset3.png">
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-5">
						<div class="pro-cont">
							<p>Let your lungs breathe again by flushing out tar accumulated in your lungs Naturally without any side effects.<br><br>Tar Free is a scientific ayurvedic formulation backed with high quality herbs to detox your lungs from the tar accumulated by consumption of tobacco & pollution.</p>
							<ul>
								<li>Removes harmful toxins from lungs responsible for lung cancer.</li>
								<li>Removes congestion in lungs & helps to breathe freely.</li>
								<li>Improves stamina and allows you to perform physical work without breathlessness.</li>
							</ul>
						</div>
					</div>
					<div class="col-md-7">
						<div class="botel-box">
							<div class="botl-img">
								<img src="<?php echo USER_IMAGES ?>landing-page/Glass-Bottle.png">
							</div>
							<div class="botl-cont">
								<h3>Perfect Combination of </h3>
								<ul>
									<li>Mulethi</li>
									<li>Tulsi</li>
									<li>Trifala</li>
									<li>Haldi</li>
									<li>Lahsun</li>
									<li>Manjitha</li>
									<li>Shikajai</li>
								</ul>
								<div class="tag-scrn">
									<a href="#" class="btn-by buyNow">Buy Now</a> <img src="<?php echo USER_IMAGES ?>landing-page/price-tag3.png"><br><span>3Pcs combo</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content-1 bg-2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="disclamer">
							<h2>How it works?</h2>
							<p>The herbs used in our syrup are natural Mucolytics & Expectorants. Mucolytic means a substance that breaks down the mucous. Harmful toxins from smoke and polluted air, gather inside our lungs by sticking to mucous. When we use Tar Free – this mucous and toxins combination gets broken down, thereby making it easier to cough them out. Expectorants are substances that make the body cough out. These broken down mucous + toxins (can be in the form of phlegm) get swallowed and eliminated through our digestive system & excretory system.</p>
						</div>
					</div>
				</div>
			</div>
		</section>


		<section class="section-content-1 bg-3">
			<div class="container">
				<div class="row">
					<div class="col-md-9">
						<div class="disclamer-pro">
							<h2 class="head-2">How to use it?</h2>
							<h2 class="head-2">Get control over your lungs</h2>
							<div class="box-data">
								<div class="cont-bx">
									<span>
										<h5>Dosage</h5>
										<p>90 Days (May Vary For Person To Person)Take After Meal</p>
									</span>
								</div>
								<div class="tag-scrn1 dn-mb">
									<img src="<?php echo USER_IMAGES ?>landing-page/price-tag.png"><br><span>3pc combo</span>
								</div>
							</div>
							<div class="icon-bx">
								<div class="icn-n">
									<img src="<?php echo USER_IMAGES ?>landing-page/night.png"><span>NIght*<br>Morning</span>
								</div>
								<div class="icn-n">
									<img src="<?php echo USER_IMAGES ?>landing-page/spoon.png"><span>2 Table*<br>Spoon</span>
								</div>
								<div class="icn-n">
									<img src="<?php echo USER_IMAGES ?>landing-page/day.png"><span>90 Days*<br>Regular</span>
								</div>
								<div class="icn-n dn-mb">
									<a href="#" class="btn-by buyNow">Buy Now</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="pro-3">
							<img src="<?php echo USER_IMAGES ?>landing-page/Product.png">
						</div>
						<div class="tag-scrn1 mob-1">
							<img src="<?php echo USER_IMAGES ?>landing-page/price-tag.png"><br><span>3pc combo</span>
							<a href="#" class="btn-by buyNow">Buy Now</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content-1 ">
			<div class="container">
				<div class="row">
					<div class="prodct-are">
						<h3>27.1% of India’s all cancer cases in 2020 will be tobacco-related, ICMR report estimates - The Print</h3>
						<h2>Vedic vitals product are</h2>
						<div class="img-icn">
							<img src="<?php echo USER_IMAGES ?>landing-page/1.png">
							<img src="<?php echo USER_IMAGES ?>landing-page/2.png">
							<img src="<?php echo USER_IMAGES ?>landing-page/3.png">
							<img src="<?php echo USER_IMAGES ?>landing-page/4.png">
							<img src="<?php echo USER_IMAGES ?>landing-page/5.png">
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content-1 bg-4">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="table-sctn">
							<h3>Vedic Vitals vs other</h3>
							<div class="table-bx">
								<div class="table-hed">
									<span class="san1"><h4>Comparison</h4></span>
									<span class="san2"><h4>Vedic Vitals</h4></span>
									<span class="san2"><h4>Other</h4></span>
								</div>
								<div class="tabl-con">
									<span class="san1"><img src="<?php echo USER_IMAGES ?>landing-page/herb1.png"><span>Ayurvedic Herbs</span></span>
									<span class="san2">Yes</span>
									<span class="san2">No</span>
								</div>
								<div class="tabl-con drk">
									<span class="san1"><img src="<?php echo USER_IMAGES ?>landing-page/herb2.png"><span>100% Safe & No Side Effects</span></span>
									<span class="san2">Yes</span>
									<span class="san2">No</span>
								</div>
								<div class="tabl-con ">
									<span class="san1"><img src="<?php echo USER_IMAGES ?>landing-page/herb3.png"><span>Treat Root Cause</span></span>
									<span class="san2">Yes</span>
									<span class="san2">No</span>
								</div>
								<div class="tabl-con drk1">
									<span class="san1"><img src="<?php echo USER_IMAGES ?>landing-page/herb4.png"><span>Remove Tar</span></span>
									<span class="san2">Yes</span>
									<span class="san2">No</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content-1 bg-5">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="rating-sectn">
							<h2>Review and Rating</h2>
							<div class="star-rating">
							 <span>4.3 / 5.0</span> <input type="radio" id="5-stars" name="rating" value="5" />
							  <label for="5-stars" class="star">&#9733;</label>
							  <input type="radio" id="4-stars" name="rating" value="4" />
							  <label for="4-stars" class="star">&#9733;</label>
							  <input type="radio" id="3-stars" name="rating" value="3" />
							  <label for="3-stars" class="star">&#9733;</label>
							  <input type="radio" id="2-stars" name="rating" value="2" />
							  <label for="2-stars" class="star">&#9733;</label>
							  <input type="radio" id="1-star" name="rating" value="1" />
							  <label for="1-star" class="star">&#9733;</label> 
							</div>
						</div>	
					</div>
					<div class="col-md-12">
						<div class="slider-test">
							<div class="carousel" data-flickity='{ "groupCells": true, "contain": true }'>
								  <div class="carousel-cell">
								  	<div class="testimonal">
								  		<h3>Simmi Nagpal</h3>
								  		<p>I have used couple of products from Vedic Vitals like Gyno Vitals and Tar Free. Quality is up to mark. Keep going</p>
								  		<div class="star-rating">
										  <input type="radio" id="5-stars" name="rating" value="5" />
										  <label for="5-stars" class="star">&#9733;</label>
										  <input type="radio" id="4-stars" name="rating" value="4" />
										  <label for="4-stars" class="star">&#9733;</label>
										  <input type="radio" id="3-stars" name="rating" value="3" />
										  <label for="3-stars" class="star">&#9733;</label>
										  <input type="radio" id="2-stars" name="rating" value="2" />
										  <label for="2-stars" class="star">&#9733;</label>
										  <input type="radio" id="1-star" name="rating" value="1" />
										  <label for="1-star" class="star">&#9733;</label> 
										</div>
										<span>27 jan</span>
								  	</div>
								  </div>
								  <div class="carousel-cell">
								  	<div class="testimonal">
								  		<h3>Rajveer Nanda</h3>
								  		<p>It's good😊.. smoking ki Talab mitatihai... breathing improved after playing football</p>
								  		<div class="star-rating">
										  <input type="radio" id="5-stars" name="rating" value="5" />
										  <label for="5-stars" class="star">&#9733;</label>
										  <input type="radio" id="4-stars" name="rating" value="4" />
										  <label for="4-stars" class="star">&#9733;</label>
										  <input type="radio" id="3-stars" name="rating" value="3" />
										  <label for="3-stars" class="star">&#9733;</label>
										  <input type="radio" id="2-stars" name="rating" value="2" />
										  <label for="2-stars" class="star">&#9733;</label>
										  <input type="radio" id="1-star" name="rating" value="1" />
										  <label for="1-star" class="star">&#9733;</label> 
										</div>
										<span>27 jan</span>
								  	</div>
								  </div>
								  <div class="carousel-cell">
								  	<div class="testimonal">
								  		<h3>Alamgeer Sadiq</h3>
								  		<p>Used the product and found a difference in my breathing. Felt the lungs were clear when everi took 5 ml of syrup.</p>
								  		<div class="star-rating">
										  <input type="radio" id="5-stars" name="rating" value="5" />
										  <label for="5-stars" class="star">&#9733;</label>
										  <input type="radio" id="4-stars" name="rating" value="4" />
										  <label for="4-stars" class="star">&#9733;</label>
										  <input type="radio" id="3-stars" name="rating" value="3" />
										  <label for="3-stars" class="star">&#9733;</label>
										  <input type="radio" id="2-stars" name="rating" value="2" />
										  <label for="2-stars" class="star">&#9733;</label>
										  <input type="radio" id="1-star" name="rating" value="1" />
										  <label for="1-star" class="star">&#9733;</label> 
										</div>
										<span>27 jan</span>
								  	</div>
								  </div>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="prod-4">
							<img src="<?php echo USER_IMAGES ?>landing-page/Product-ombo.png">
						</div>
					</div>
					<div class="col-md-4">
						<div class="tag-scrn1 ful-w">
							<img src="<?php echo USER_IMAGES ?>landing-page/price-tag.png"><br><span>3pc combo</span><br>
							<a href="#" class="btn-by buyNow">Buy Now</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="media-sectn">
			<h2>Media and Press</h2>
			<div class="container">
				<div class="row">
					<div class="col-md-12">

						<div class="logo-media">
							<img src="<?php echo USER_IMAGES ?>landing-page/times.png">
						</div>
						<div class="logo-media">
							<img src="<?php echo USER_IMAGES ?>landing-page/Zindgi.png">
						</div>
						<div class="logo-media">
							<img src="<?php echo USER_IMAGES ?>landing-page/restro.png">
						</div>
						<div class="logo-media">
							<img src="<?php echo USER_IMAGES ?>landing-page/Aviation.png">
						</div>

					</div>
				</div>
			</div>
		</section>

		<section class="faq-sectn">
			<h2>Faq</h2>
			<div class="container">
				<div class="col-md-12">
					<div class="faq-box">
						<div id="accordion">
							  <div class="card">
							    <div class="card-header" id="headingOne">
							      <h5 class="mb-0">
							        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							          How Tar Free can help people?
							        </button>
							      </h5>
							    </div>

							    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
							      <div class="card-body">
							        Tar Free helps to cleanse the lungs by flushing out the Tar accumulated from constant exposure to pollution and smoking habits.
							      </div>
							    </div>
							  </div>
							  <div class="card">
							    <div class="card-header" id="headingTwo">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
							          What are the basic ingredients used to make Tar Free syrup?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
							      <div class="card-body">
							        Tar Free syrup is made with high-quality herbs known for their detoxification and healing properties. The ingredients used in Tar Free are – Mulethi, Tulsi, Trifala, Haldi, Lahsun, Manjitha, Shikakai and Trikatu.
							      </div>
							    </div>
							  </div>
							  <div class="card">
							    <div class="card-header" id="headingThree">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							          Is the Syrup sugar-free or not?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
							      <div class="card-body">
							       We love your wellbeing, so all products from Vedic Vitals are sugar-free and have no artificial sweeteners. Thus, it can be taken by diabetic people too.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingFour">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
							         Are there any side effects of taking Tar Free syrup?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
							      <div class="card-body">
							       Research on the product reveals it doesn’t have any side effects on the body. It is an Ayurvedic formulation and is approved by the Ayush Ministry.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingFive">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
							         Can this syrup be used by asthmatic patients?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
							      <div class="card-body">
							       Tar Free does not cure Asthma; however, Tar Free aids proper respiration after consumption.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingSix">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
							         Is the product suitable for chain smokers?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
							      <div class="card-body">
							       Chain smokers tend to collect the most amount of Tar in their lungs from smoking habits and air pollution. Tar Free is the best combination of herbs to detox their lungs that results in proper respiration.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingSeven">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
							         How to consume the product properly?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
							      <div class="card-body">
							       We recommend 5 – 7.5 ml of syrup twice a day after breakfast and dinner for moderate users. Chain smokers and people with respiratory problems can consume it three times a day after meals.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingEight">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
							         How long do we need to take it for best results?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#accordion">
							      <div class="card-body">
							       A dosage of 3-6 months is recommended for best results. However, people with a long history of smoking may need a longer treatment.
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingNine">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
							         What is the cost of Tar Free and How can we order it?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#accordion">
							      <div class="card-body">
							       Combo of Three Bottles of Tar Free containing 150 ml/each costs Rs. 880 only after a 15% Flat Discount (MRP – 1035).
							      </div>
							    </div>
							  </div>

							  <div class="card">
							    <div class="card-header" id="headingTen">
							      <h5 class="mb-0">
							        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
							         How many days will it take to reach me?
							        </button>
							      </h5>
							    </div>
							    <div id="collapseTen" class="collapse" aria-labelledby="headingTen" data-parent="#accordion">
							      <div class="card-body">
							       We ship all our products via Air Mode (Bluedart Express, Delhivery, Express bees, Ecom Express) and it generally reaches you within 5-7 days. However it may take little longer in J&K & North East regions. Any delay due to unforeseen reasons can also lead to delay in delivery. 
							      </div>
							    </div>
							  </div>
							</div>
					</div>
				</div>
			</div>
		</section>


		<!-- stay conect -->
		<section class="section-content socl-box">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="social-1"> <span>Stay Connected </span>
								<a href="https://www.facebook.com/vedicvitals/"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
								<a href="https://twitter.com/vedicvitals/"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
								<a href="https://www.instagram.com/vedicvitals/"><i class="fa fa-instagram" aria-hidden="true"></i></a>
							</div>
					</div>
				</div>
			</div>
		</section>
		<!-- stay conect end -->

	<footer>
			<div class="top-section-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Quick Links</h3>
								<ul class="list-unstyled">
									<li><a href="https://vedicvitals.com/">Home</a></li>
									<li><a href="https://vedicvitals.com/our-story/">Our Story</a></li>
									<li><a href="https://vedicvitals.com/contact-us/">Contact Us</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Products</h3>
								<ul class="list-unstyled">
									<li><a href="https://vedicvitals.com/product/immunity/immunity-vitals/">Immunity Vitals</a></li>
									<li><a href="https://vedicvitals.com/product/tar/tar-free/">Tar Free</a></li>
									<li><a href="https://vedicvitals.com/product/gyno/gyno-vitals/">Gyno Vitals</a></li>
									<li><a href="https://vedicvitals.com/product/liver/v-liver-vitals/">V-Liver Vitals</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Information</h3>
								<ul class="list-unstyled">
									<li><a href="https://vedicvitals.com/privacy-policy/">Privacy Policy</a></li>
									<li><a href="https://vedicvitals.com/disclaimer/">Disclaimer</a></li>
									<li><a href="https://vedicvitals.com/return-policy/">Return Policy</a></li>
									<li><a href="https://vedicvitals.com/terms-of-use/">Terms of Use</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">
								<h3>Contact Us</h3>
								<p>622-A, Ground Floor, Guru Ram Das Nagar, Laxmi Nagar, DELHI - 110092, India</p>
								<p>care@vedicvitals.com</p>
								<p>+91 9310142836</p>

								<section class="hideOnMobile" >
									<div class="container-fluid">
										<div class="row">

											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/secure-payment.png" width="100%">
												<p class="text-dark py-2">Safe Payments</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/24-hours-support.png" width="100%">
												<p class="text-dark py-2">24 Hour Support</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/free-shipping.png" width="100%">
												<p class="text-dark py-2">Free shipping</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/made-with-love.png" width="100%">
												<p class="text-dark py-2">Made With love</p>
											</div>
									
										</div>
									</div>
								</section>
								
							</div>
						</div>
						
					</div>
					<div class="row">
						<div class="col-12">
							<section class="hideOnDesktop" >
									<div class="container-fluid">
										<div class="row">

											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/secure-payment.png" width="100%">
												<p class="text-dark py-2">Safe Payments</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/24-hours-support.png" width="100%">
												<p class="text-dark py-2">24 Hour Support</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/free-shipping.png" width="100%">
												<p class="text-dark py-2">Free shipping</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="https://vedicvitals.com/app/ecommerce/visitor/vedicvitals/desktop/assets/images/made-with-love.png" width="100%">
												<p class="text-dark py-2">Made With love</p>
											</div>
									
										</div>
									</div>
							</section>
						</div>
					</div>
				</div>
			</div>
			<div class="section-footer">
				<div class="container">
					<div class="row">																	
						<div class="col-md-12 col-12">
							<div class="footer-btm">
								<p>&copy; All rights reserved by Vedic Vitals<br />Made with <i class="fa fa-heart"></i> by <a href="http://www.itssoftworld.com" title="Software development company in delhi">ITS Soft World</a> in India</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
		<!-- section global platform start -->
		<!-- <section class="banner-section">
			<div class="container-fluid">
				<div class="row">
					<div class="card-ban">
						<div class="carousel"  data-flickity='{ "autoPlay": false, "wrapAround": false }'>
							<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>landing-page/bg-1.jpg"></div>
						</div>
					</div>
				</div>
			</div>
		</section> -->
		<!-- garanty start -->

		<div class="whatsapp">
			<a href="https://wa.me/919310142836?text=Hello"><img src="<?php echo IMG_LIBRARY ?>whatsapp.png" alt=""></a>
		</div>


		<!-- sections end -->

		<?php
		
			require_once NOTIFICATION.'index.php';

		?>

		<!-- script start -->
		<script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		<script src="<?php echo APP_JS_LIBRARY ?>app.min.js" ></script>
		<!-- script end -->
		
		<!-- click js popup -->
		<script type="text/javascript">
			function show_project(theId){
				document.getElementById(theId).style.display = "inline";
			}

			function hide_project(theId){
				document.getElementById(theId).style.display = "none";
			}
		</script>

	
	
	</body>
</html>