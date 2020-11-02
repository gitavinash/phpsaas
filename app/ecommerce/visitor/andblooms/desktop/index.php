<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
      	<link rel="shortcut icon" href=" ">
		<title>Events</title>
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<!-- css -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="css/flickity.min.css">
		<!-- <script src="https://use.fontawesome.com/393c79d8c8.js"></script> -->
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600;800&display=swap" rel="stylesheet">
	</head>
	<body >
		<!-- header start -->
		<header>
			<div class="header-top">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="web-detail dn-s">
								<p>Latest Update: Delivery Available in selected Cities<span> <a href="">Check City</a></span></p>
								<!-- <p><span class="material-icons">email</span></p> -->
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<div class="header-mid dn-s">
				<div class="container-fluid">
					<div class="row">
						
						<div class="col-md-2">
							<div class="logo">
								<img src="image/logo.png" align="logo">
							</div>
						</div>
						<div class="col-md-3">

							<!-- <div class="select-city">
								<form>
								  <div class="form-group city-popup">
								  	<a href="#popup1">
								    <input class="form-control" id="user-selected-city-input" placeholder="Choose Delivery City" ></a>
								  </div>
								</form>
							</div> -->
						</div>
						<div class="col-md-7">
							<div class="search-box">
								<form class="form-inline">
							      <input class="form-control" type="search" placeholder="Search for gifts, flowers..." >
							    </form>
							</div>
							<div class="search-pro">
								<nav class="navbar navbar-expand-lg login-section justify-content-right ">
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-login">
									  <!-- <li class="searc-div">
									  	<form action="" autocomplete="on">
										  <input id="search" name="search" type="text" placeholder="What're we looking for ?"><input id="search_submit" value="Rechercher" type="submit">
										 </form>
									  </li> -->
									   <!-- <li class="wal-icn"><a href=""><img src="image/wallet.svg"></a><span class="span-item">Wallet</span></li>
									  <li><a href=""><img src="image/track.svg"></a><span class="span-item">Track Order</span></li> -->
									  <li><a href=""><img src="image/cart.svg"></a></a><span class="crt-crcl">0</span><span class="span-item">Cart</span></li>
									  <!-- user acount -->
									  <!-- <li class="nav-item dropdown us-icn">
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="image/signin.svg"><span class="span-item">My Account</span>
									      </a>
									      <div class="dropdown-menu">
									      	<div class="user-data">
									      		<img src="image/signin.svg">
									      		<h2>Puneet Jalindra</h2>
									      		<span>puneet.jalindra90@gmail.com</span>
									      		<a href="" class="edit-btn">Edit</a>
									      	</div>
									        <a class="dropdown-item" href="#"><img src="image/order.png"> Orders</a>
									        <a class="dropdown-item" href="#"><img src="image/feedback-fa.svg">  My Reviews</a>
									        <a class="dropdown-item" href="#"><img src="image/address-fa.png"> Manage Address</a>
									        <a class="dropdown-item" href="#"><img src="image/logout-fa.png"> Logout</a>
									        
									      </div>
									  </li> -->
									  
									  <li class="nav-item dropdown">
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="image/menu.svg"><span class="span-item">More</span>
									      </a>
									      <div class="dropdown-menu barMenu">
									        <a class="dropdown-item bm1" href="#"><img src="image/gifts-fa.svg"> Corporate Gifts</a>
									        <a class="dropdown-item bm2" href="#"><img src="image/franchise-fa.svg"> Franchise</a>
									        <a class="dropdown-item " href="#"><img src="image/need-fa.svg"> Need Help</a>
									        <a class="dropdown-item " href="#"><img src="image/feedback-fa.svg"> About Us</a>
									      </div>
									  </li>
									  
									</ul>
								</div>
							</nav>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="header-box">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<nav class="navbar navbar-expand-lg menu-top fixed">
								<a href="" class="dn-l"><img src="image/logo1.png" class="logo-mb"></a>
								<button class="navbar-toggler bar-mnu" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-icon bar-1"></span>
								<span class="navbar-icon bar-2"></span>
								<span class="navbar-icon bar-1"></span>
								<!-- <img src="image/menu.svg"> -->
								</button>
								<div class="mob-menu dn-l">
									<div class="cart-mob">
										<a href="" class="mob-bag sr-mob" onclick="divSearchPop()"><img src="image/search-fa.svg"></a></span>
										<a href="" class="mob-bag"><img src="image/cart.svg"></a><span class="crt-crcl">0</span></span>
									</div>

									<!-- more dots menu mobile -->
									<!-- <button class="navbar-toggler more-dots" type="button" data-toggle="collapse" data-target="#navbarMore" aria-controls="navbarMore" aria-expanded="false" aria-label="Toggle navigation">
										<img src="image/more.svg">		
									</button>
									<div class="collapse navbar-collapse" id="navbarMore">
										<ul class="navbar-nav mob-more">
											<li class="nav-item">
											    <a class="dropdown-item" href="#">Login</a>
											</li>
											<li class="nav-item">
											    <a class="dropdown-item" href="#">Register</a>
											</li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Track Order</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Same Day</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Midnight</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Contact Us</a>
										    </li>
										</ul>
									</div> -->
									<!-- more dots menu mobile end-->
								</div>
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav menu-ul">
									 
									  <li class="nav-item active">
									    <a class="nav-link" href="index.html">Home</a>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Luxury Collections
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega1">
										      	<div class="menu-titl"><a href="">New Arivals<span class="cart-bodr"></span></a></div>
										        <a class="dropdown-item" href="#">Fresh Flowers</a>
										        <a class="dropdown-item" href="#">Artistic Artificial</a>
									        </div>
									  	  </div>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="index.html">Infinity Everlasting Flowers</a>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Occasins
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega2">
									      	    <div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="">Menu Name <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="#">Anniversary</a>
											        <a class="dropdown-item" href="#">Birthday</a>
											        <a class="dropdown-item" href="#">Corporate Gifting</a>
											        <a class="dropdown-item" href="#">Congratulation</a>
											        <a class="dropdown-item" href="#">Get Well</a>
											        <a class="dropdown-item" href="#">House Warming</a>
									    		</div>
									    		<div class="mega-wht flot-menu">
									    			<div class="menu-titl"><a href="">Manu Name <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="#">I'M Sorry</a>
											        <a class="dropdown-item" href="#">Just Because</a>
											        <a class="dropdown-item" href="#">New Baby</a>
											        <a class="dropdown-item" href="#">Love & Romance</a>
											        <a class="dropdown-item" href="#">Retirement</a>
											        <a class="dropdown-item" href="#">Sympathy</a>
									    		</div>
									    		<div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="">Bumper Sale <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="#">
											        	<img class="mega-img" src="https://img.floweraura.com/sites/default/files/alloccasion/menucard/Plants.png">
											        </a>
									    		</div>
									    	</div>
									      </div>
									  </li>
									  <li class="nav-item ">
									    <a class="nav-link " href="#">Subscription</a>
									  </li>
									  <li class="nav-item ">
									    <a class="nav-link " href="#">Contact</a>
									  </li>

									  <div class="dn-l log-in">
									  	<li class="nav-item">
									    	<a class="nav-link" href="index.html">Login</a>
									  	</li>
									  </div>
									  <!-- <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Birthday
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Occasions
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Corporate Gifts
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li> -->
									</ul>

									<a href=" " class="cont-menu">Contact Us</a>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>
			<div class="dn-l location-map">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="map-tag">
								<a href=""><img src="image/ping-map.svg">Select the city</a>
							</div>
							<div id="search-mob-div" class="searchInput">
									<input title="Enter the terms you wish to search for." type="text" id="search_block" name="search_block_form" size="15" maxlength="128" class="form-text ui-autocomplete-input" placeholder="Search for gifts, flowers..." autocomplete="off" value=""><div id="close">x</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<!-- header end -->

		<!-- section global platform start -->
		<section class="banner-section">
			<div class="container-fluid">
				<div class="row">
					<div class="card-ban">
						<div class="carousel dn-s"  data-flickity='{ "autoPlay": true, "wrapAround": true }'>
							<div class="carousel-cell-2"><img src="image/1.png"></div>
							<div class="carousel-cell-2"><img src="image/2.png"></div>
						</div>
						<div class="carousel dn-l mob-baner"  data-flickity='{ "autoPlay": true, "wrapAround": true }'>
							<div class="carousel-cell-2 "><img src="image/mob-baner.jpg"></div>
							<div class="carousel-cell-2 "><img src="image/mob-baner1.jpg"></div>

						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- section global platform end -->

		<section class="section-content cloth-card" >
			<div class="container">
				<div class="row">
					<div class="main-card">
						<a href="" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Flowers</h4>
								</div>
							</div>
						</a>
				
						<a href="" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Cakes</h4>
								</div>
							</div>
						</a>

						<a href="" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Combos</h4>
								</div>
							</div>
						</a>
					
						<a href="" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Plants</h4>
								</div>
							</div>
						</a>

						<a href="" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Gifts</h4>
								</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content bg-light">
			<div class="container">
				<!-- catagery 1 carousel -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Love Collection</h2>
							<span class="cart-bodr-h2"></span>
							<div class="dn-l view-box">
								<a href="" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- catagery 2 carousel -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Luxury Collection</h2>
							<span class="cart-bodr-h2"></span>
							<div class="dn-l view-box">
								<a href="" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- catagery 3 carousel-->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Birthday</h2>
							<span class="cart-bodr-h2"></span>
							<div class="dn-l view-box">
								<a href="" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- catagery 4 carousel-->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Anniversary</h2>
							<span class="cart-bodr-h2"></span>
							<div class="dn-l view-box">
								<a href="" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-home">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>

				<!-- card demo -->
				<!-- <div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Anniversary</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-3">
						<div class="event-card">
							<div class="img-cd">
								<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
							</div>
								<h4>Alexander Mcqueen</h4>
								<p class="rate">Rs. 115</p>
						</div>
					</div>

					<div class="col-md-3">
						<div class="event-card">
							<div class="img-cd">
								<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
							</div>
								<h4>Alexander Mcqueen</h4>
								<p class="rate">Rs. 145</p>
						</div>
					</div>

					<div class="col-md-3">
						<div class="event-card">
							<div class="img-cd">
								<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
							</div>
								<h4>Alexander Mcqueen</h4>
								<p class="rate">Rs. 255</p>
						</div>
					</div>

					<div class="col-md-3">
						<div class="event-card">
							<div class="img-cd">
								<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
							</div>
								<h4>Alexander Mcqueen</h4>
								<p class="rate">Rs. 1255</p>
						</div>
					</div>
				</div> -->
			</div>
		</section>

		<section class="section-content cloth-card" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Customer Review</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-testimonial" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content info-setion" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="about-info">
							<h1>#1 Online Flower Delivery Portal In India</h1>
							<p>Is there anyone in this world who does not like flowers? Anyone who does not admire the beauty of flowers? Of course not! Because flowers undoubtedly bring a smile on everyone's face, they add the warmth of sunshine to the day and make the soul feel calm and peaceful. And so to speak the language of flowers and express emotions when words are just not enough, FlowerAura has taken the responsibility to become your messenger of Emotions, Send flower bouquet online and add more smiles to your loved one's face.</p>

							<p>But finding a beautiful bunch of flower that speaks your emotions perfectly might be a tough task. That is when FlowerAura steps in with amazing flower gifts of amazing varieties so that you don't have to hop to florist shops around you and be settled with a limited choice. FlowerAura shall be your reliable online florist shop in India so that you can sit at the comfort of your home and order amazing flowers and gifts with a few taps of your finger.</p>

							<p>So, what is their favorite flower? Does your recipient love Roses, Carnations, Gerberas, Anthuriums, orchids, Lilies or? Whichever be the flower of their choice, find it at the best online florist in India- FlowerAura. We deliver freshly cut flowers in the most beautiful and classic arrangements. Wrapped beautifully in different designs and colorful paper, these flower arrangements are the best quality products which shall make your loved one's face shine even brighter.</p>

							<p>So the next time you need to send flowers online in India, you know where to head to.</p>

							<h2>Send Flowers Online from FlowerAura - India's No. 1 Online Florist</h2>
							<p>FlowerAura stands as a No. 1 online flower shop that doesn't only deliver flowers, gifts, and cakes but makes sure that each of the hampers is wrapped in love too. Along with the pocket-friendly deals come the free shipping that makes FlowerAura top the charts and establishing itself as one of the most reliable online florists in the country.</p>


						</div>
					</div>
				</div>
			</div>
		</section>
	
		<section class="section-content bg-covid" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="safety-head">
							<h3>Your Safety is our Priority!</h3>
							<p>Steps we are taking to keep you safe</p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="list-covid">
							<div class="covid-box">
								<div class="covid-img">
									<img src="image/fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Sanitization</h4>
									<p>Sanitize our hands & kitchen surface in every 4 hours</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Contact Less Delivery</h4>
									<p>Maintaining the safe distance</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/facontactless.png">
								</div>
								<div class="covid-name">
									<h4>Temperature Checks</h4>
									<p>Real-time Temperature Tracking of kitchen & delivery staff</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/fasafety.png">
								</div>
								<div class="covid-name">
									<h4>Compulsory Mask & Gloves</h4>
									<p>Always on mask & Gloves environment</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="you-tube" >
							<iframe width="100%" height="280" src="https://www.youtube.com/embed/WDmrzNh5Ph8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>	
					</div>
					
				</div>
			</div>
		</section>

		<!-- stay conect -->
		<section class="section-content bg-gr dn-s">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h2 class="h2-socl">Spread The Love</h2>

						<div class="social-1">
							<a href=""><img src="image/fb.svg"></a>
							<a href=""><img src="image/tweet.svg"></a>
							<a href=""><img src="image/linkedin.svg"></a>
							<a href=""><img src="image/pinterest.svg"></a>
							<!-- <a href=""><img src="image/insta.svg"></a> -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- stay conect end -->

		<footer>
		<div class="top-section-footer bg-gr">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="fot-box">	
							<ul class="dn-s">
				              <li><a href="index">About Us</a></li>
				              <li><a href="">Coupons & Deals</a></li>
				              <li><a href="">Cancellation & Refund</a></li>
				              <li><a href="">Career</a></li>
				              <li><a href="">Media</a></li>
				              <li><a href="">Reviews</a></li>
				              <li><a href="">Blog</a></li>
				            </ul>
				            <ul class="dn-l">
				              <li><a href="">Terms</a></li>
				              <li><a href="">Cancellation</a></li>
				              <li><a href="">Privacy</a></li>
				            </ul>
						</div>
					</div>
					<div class="col-md-12">
						<div class="acept-py">
							<span class="partnr">Our Partner <a href="">Partner Name</a></span>
							<span class="payout dn-s">We Accept <img src="image/desktop-payment-option.png"></span>
							<span class="payout dn-l">We Accept <img src="image/paymentFooter.png"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-12">
						<div class="footer-btm">
							<p>© 2020 . All Rights Reserved</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		</footer>

		<!-- sections end -->

		<!-- script start -->
		<script src="js/jquery-3.4.1.slim.min.js" ></script>
		<script src="js/flickity.pkgd.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<!-- script end -->
		
		<!-- city search popup start -->
		<div id="popup1" class="overlay-pop">
			<div class="popup">
				<h2>Here i am</h2>
				<a class="close" href="#">&times;</a>
				<div class="content-pop">
					Thank to pop me out of that button, but now i'm done so you can close this window.
				</div>
			</div>
		</div>

		<!-- city search popup end -->

		<script type="text/javascript">
			$(document).ready(function() {
			  $('.card-slider').slick({
			    dots: false,
			    arrows: true,
			    slidesToShow: 4,
			    infinite: false,
			    responsive: [
			      {
			        breakpoint: 800,
			        settings: {
			          slidesToShow: 3
			        }
			      },
			      {
			        breakpoint: 600,
			        settings: {
			          slidesToShow: 2
			        }
			      }
			    ]
			  });
			});
		</script>
		
		<!-- search in mobile div show -->

		<script type="text/javascript">
			function divSearchPop() {
			  var x = document.getElementById("search-mob-div");
			  if (x.style.display === "none") {
			    x.style.display = "block";
			  } else {
			    x.style.display = "none";
			  }
			}

			$("#close").click(function(){
			  $("searchInput").hide();
			});

		</script>
	</body>
</html>