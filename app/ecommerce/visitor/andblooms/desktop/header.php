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
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>flickity.min.css">
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
