<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
      integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/fontawesome.min.css"
      integrity="sha384-zIaWifL2YFF1qaDiAo0JFgsmasocJ/rqu7LKYH8CoBEXqGbb9eO+Xi3s6fQhgFWM"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.css"
      integrity="sha512-Yn5Z4XxNnXXE8Y+h/H1fwG/2qax2MxG9GeUOWL6CYDCSp4rTFwUpOZ1PS6JOuZaPBawASndfrlWYx8RGKgILhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/Home.css" />
    <link rel="stylesheet" href="./css/navbar.css" />
    <link rel="stylesheet" href="./css/Product-details.css" />
    <link rel="stylesheet" href="./css/responsive.css" />

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css"
      integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
      integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.all.min.js"
      integrity="sha512-Ty04j+bj8CRJsrPevkfVd05iBcD7Bx1mcLaDG4lBzDSd6aq2xmIHlCYQ31Ejr+JYBPQDjuiwS/NYDKYg5N7XKQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>

    <title>Hello, world!</title>
  </head>

  <body>
    <section class="bg-top">
      <!-- navbar -->
      <header>
        <nav
          class="navbar navbar-expand-lg navbar-dark side-margin nav-padding navbar-global"
        >
          <div class="container-fluid">
            <a class="navbar-brand" href="./index.html"
              ><img src="./assest/img/logo.png" alt="" class="img-fluid"
            /></a>
            <button
              class="navbar-toggler mobile-tpggle"
              type="button"
              aria-controls="navbarNav"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="navbar-toggler-icon" id="navbtn"></span>
            </button>
            <!-- 
              <button>  <span class="navbar-toggler-icon d-none"></span></button> -->
            <div class="collapse navbar-collapse">
              <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                  <a
                    class="nav-link active"
                    aria-current="page"
                    href="./index.html"
                    >Home</a
                  >
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="./About.html">About us</a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="./Product-details.html">Product</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="./contact.html">Contact us</a>
                </li>
              </ul>

              <div
                class="icons d-flex justify-content-center mb-3 mt-3 mt-xl-0 mb-xl-0"
              >
                <i class="fa-solid fa-user"></i>
                <i class="fa-solid fa-cart-shopping"></i>
                <i class="fa-solid fa-magnifying-glass"></i>
              </div>
            </div>
          </div>
        </nav>

        <div class="side-nav">
          <ul class="navbar-nav ms-3">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="./index.html"
                >Home</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./About.html">About us</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="./Product-details.html">Product</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./contact.html">Contact us</a>
            </li>
          </ul>
        </div>
      </header>
      <!-- navbar -->
      <div class="container-fluid">
        <div
          class="row product_banner_section sidepadding hero align-items-center py-5"
        >
          <div class="col-12 text-center my-5">
            <h5>HOME / <span>PRODUCT</span></h5>
          </div>
        </div>
      </div>
      <!-- hero -->

      <!-- end -->
    </section>

    <main>
      <!-- single product -->
      <section class="singel_product py-5 container-fluid sidepadding">
        <div class="row gx-0">
          <div class="col-12 col-sm-12 col-md-12 col-xl-7 col-xxl-6">
            <div class="col-12">
              <div class="row justify-content-center">
                <div
                  class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 d-none d-lg-block flex_div"
                >
                  <div class="sm-image-group mt-4 mt-xl-0">
                    <img
                      src="./assest/img/grou1.png"
                      class="img-fluid"
                      alt=""
                    />
                  </div>
                  <div class="sm-image-group mt-4">
                    <img
                      src="./assest/img/grou2.png"
                      class="img-fluid"
                      alt=""
                    />
                  </div>
                  <div class="sm-image-group mt-4">
                    <img
                      src="./assest/img/grou3.png"
                      class="img-fluid"
                      alt=""
                    />
                  </div>
                  <div class="sm-image-group mt-4">
                    <img
                      src="./assest/img/grou4.png"
                      class="img-fluid"
                      alt=""
                    />
                  </div>
                </div>

                <!-- mobile slider -->
                <div
                  class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 flex_div d-block d-xl-none"
                >
                  <div class="slider">
                    <div class="sm-image-group mt-4 mt-xl-0 mx-3">
                      <img
                        src="./assest/img/grou1.png"
                        class="img-fluid d-block m-auto"
                        alt=""
                      />
                    </div>

                    <div class="sm-image-group mt-4 mt-xl-0 mx-3">
                      <img
                        src="./assest/img/grou1.png"
                        class="img-fluid d-block m-auto"
                        alt=""
                      />
                    </div>

                    <div class="sm-image-group mt-4 mt-xl-0 mx-3">
                      <img
                        src="./assest/img/grou1.png"
                        class="img-fluid d-block m-auto"
                        alt=""
                      />
                    </div>

                    <div class="sm-image-group mt-4 mt-xl-0 mx-3">
                      <img
                        src="./assest/img/grou1.png"
                        class="img-fluid d-block m-auto"
                        alt=""
                      />
                    </div>
                  </div>
                </div>

                <!-- mobile slider -->
                <div
                  class="col-12 col-sm-12 col-md-12 col-xl-7 col-xxl-9 order-1 order-xxl-2"
                >
                  <div class="product_image">
                    <img
                      src="./assest/img/oriduct.png"
                      class="img-fluid"
                      alt=""
                    />
                  </div>
                  <div
                    class="d-flex align-items-center justify-content-center mt-4 qustion"
                  >
                    <img src="./assest/img/queston.png" alt="" />
                    <p class="mb-0 ms-3">
                      <span id="test">Have Questions?</span> Ask An Expert
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="col-12 col-sm-12 col-md-12 col-xl-5 col-xxl-6 mt-4 mt-xl-0"
          >
            <h1>Dinterdum pretium condimento</h1>

            <!-- <div class="d-flex align-items-center py-3">
            <div>
              <img src='./assest/img/str1.png' alt="">
              <img src='./assest/img/str1.png' alt="">
              <img src='./assest/img/str1.png' alt="">
              <img src='./assest/img/str1.png' alt="">
              <img src='./assest/img/Star 5.png' alt="">
            </div>
            <p class="mb-0 ms-5 mt-2">16 reviews</p>
          </div> -->

            <div class="product_details">
              <div>
                <p>Brand: Tomorrow</p>
              </div>
              <div>
                <p>Product Code: W0690036</p>
              </div>
              <div>
                <p>Availability: In stock</p>
              </div>
            </div>

            <!-- <div class="mt-4 product_details time_section ">
            <div class="col-12 ">
              <div class="row gx-0 align-items-center">
                <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-5">
                  <p>Limited-Time Offers, End in:</p>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-7 mt-3 mt-xl-0">
                  <div class="col-12">
                    <div class="row gx-1">
                      <div class="col-12 col-sm-12 col-md-3">
                        <div class="time_div">
                          <p>00D</p>
                        </div>
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 mt-2 mt-md-0">
                        <div class="time_div">
                          <p>00D</p>
                        </div>
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 mt-2 mt-md-0">
                        <div class="time_div">
                          <p>00D</p>
                        </div>
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 mt-2 mt-md-0">
                        <div class="time_div">
                          <p>00D</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div> -->

            <div class="price_div py-3 mt-4 prive_top_bodder">
              <div class="d-flex align-items-center">
                <h4>$202.00</h4>
                <p class="ms-5 line">$210.00</p>
              </div>
            </div>
            <!-- 
          <div class="col-12 mt-4 filter">
            <div class="row">
              <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-6">
                <div class="row">
                  <div class="col-12 mb-3">
                    <h5>Power Output</h5>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="filter-div">
                      <p>150 w</p>
                    </div>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="filter-div">
                      <p>200w</p>
                    </div>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="filter-div">
                      <p>300w</p>
                    </div>
                  </div>
                </div>

              </div>
              <div class="col-12 col-sm-12 col-md-1"></div>
              <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-5 mt-3 mt-xxl-0">
                <div class="row">
                  <div class="col-12 mb-3">
                    <h5>Color</h5>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="colr_div dark_cl colr_div_activ"></div>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="colr_div gray_cl "></div>
                  </div>
                  <div class="col-4 col-sm-4 col-md-4">
                    <div class="colr_div skin_cl   "></div>
                  </div>
                </div>

              </div>
            </div>
          </div> -->

            <div class="mt-4 prive_top_bodder py-5 filter fire">
              <div class="col-12">
                <div class="row">
                  <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-6">
                    <h5>QUANTITY</h5>
                    <div class="quntity-div mt-3">
                      <input type="number" />
                      <div class="controlles"></div>
                    </div>
                  </div>
                  <div
                    class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-6 d-flex align-content-center mt-4 mt-xxl-0"
                  >
                    <div class="d-flex align-items-center">
                      <img src="./assest/img/fire.png" alt="" />
                      <p class="mb-0 ms-2">8 sold in last 18 hours</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center cart_div">
              <button class="add_to_cart">ADD TO CART</button>
              <div class="like ms-3">
                <img src="./assest/img/heart.png" alt="" />
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- single product -->

      <!-- <section class="product_discription sidepadding">
      <div class="container-fluid py-5">
        <div class="row gx-0 justify-content-center justify-content-xxl-start">
          <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3">
            <div class="tabs_div active_div">
              <p>DESCRIPTION</p>
            </div>
          </div>
          <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3 my-3 mt-xl-0">
            <div class="tabs_div ">
              <p>CUSTOMER REVIEWS</p>
            </div>
          </div>
          <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3">
            <div class="tabs_div ">
              <p>SHIPPING & RETURNS</p>
            </div>
          </div>
        </div>
      </div>
      </div>
    </section> -->

      <section class="products-section pb-5 mb-5 mt-4 mt-xxl-0">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 text-center text-xl-start"
            >
              <img
                src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw4117cc35/pdp/JBL_GO3_WORKOUT_LIFESTYLE01_904x560px.png"
                class="img-fluid"
                alt=""
              />
            </div>
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 mt-4 mt-xl-0 text-center text-xl-start"
            >
              <h3 class="mb-4">High-fidelity Sound</h3>
              <p>
                This is a 2.1 ch soundbar that comes with a 16.5-cm (6.5)
                subwoofer to deliver intense and powerful sound with deep bass.
                This home audio system fills your room with surround sound for
                an immersive aural experience.
              </p>
            </div>
          </div>
          <div class="row align-items-center my-2 my-xl-0">
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 order-2 order-xl-1 mt-4 mt-xl-0 text-center text-xl-start"
            >
              <h3 class="mb-4">One Remote Control</h3>
              <p>
                With this One Remote Control, you do not need multiple remote
                controls for your entertainment. Control key soundbar operations
                like volume, power, and sound effects with your Samsung TV
                remote directly from your TV. This remote control is compatible
                with selected models of Samsung TVs.
              </p>
            </div>

            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 order-1 order-xl-2 text-center text-xl-start d-flex justify-content-end"
            >
              <img
                src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dwde7716a2/pdp/JBL_GO3_RED_LIFESTYLE02_904x560px.png"
                class="img-fluid"
                alt=""
              />
            </div>
          </div>
          <div class="row align-items-center">
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 text-center text-xl-start"
            >
              <img
                src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw1a34f218/pdp/JBL_GO3_GREY_LIFESTYLE03_904x560px.png"
                class="img-fluid"
                alt=""
              />
            </div>
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 mt-4 mt-xl-0 text-center text-xl-start"
            >
              <h3 class="mb-4">Optimized Sound for Different Content</h3>
              <p>
                This sound system analyzes the source of audio automatically,
                then optimises the audio track according to the specific
                content. So, you can enjoy loud cheers of sporting events as
                well as soothing dialogues of romantic dramas on this soundbar.
              </p>
            </div>
          </div>
          <div class="row align-items-center mt-2 mt-xl-0">
            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 order-2 order-xl-1 mt-4 mt-xl-0 text-center text-xl-start"
            >
              <h3 class="mb-4">Game Mode</h3>
              <p>
                You can plug in your gaming console to experience impressive
                sound effects while playing your favorite games. This soundbar
                has auto settings to optimise the game audio for impressive
                output. Also, it has the cross-talk cancellation technology to
                remove any distracting sound. As a result, you can hear clearly
                and follow the necessary instructions. This feature is available
                on compatible TVs and consoles (over Xbox One, PS4, and Nintendo
                Switch)
              </p>
            </div>

            <div
              class="col-12 col-sm-12 col-md-12 col-xl-6 order-1 order-xl-2 text-center text-xl-start d-flex justify-content-end"
            >
              <img
                src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw59f8399a/pdp/JBL_Go2_waterproof-f03.png"
                class="img-fluid"
                alt=""
              />
            </div>
          </div>
        </div>
      </section>

      <section class="spec sidepadding py-5">
        <div class="container-fluid table">
          <div class="row">
            <div class="col-12 border_bottom py-4 px-4">
              <h5>Specifications</h5>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">General</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Sales Package</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>
                      : 1 Unit Main Speaker, 1 Unit Sub Woofer, 1 Unit Remote
                      Controller, 1 Unit Wall Mount Bracket, 2 AAA Batteries,
                      User Manual
                    </p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Model Number</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Model Number</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Model Name</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: HW-T42E/XL Dolby Digital</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Power Source</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: AC Adapter</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Color</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Black</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Outdoor Usage</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Yes</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">Product Details</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Configuration</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 2.1 Channel</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Driver Type</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 1 Sub Woofer</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Compatible Devices</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Mobile</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">Audio Features</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Tweeter Support</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Yes</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">Other Details</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Remote Control Support</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: Wireless</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Power Input</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 0.5</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">Dimensions</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Width</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 86 cm</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Height</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 5.4 cm</p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Depth</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 7.4 cm</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 py-3 px-4 border_bottom">
              <h4 class="mb-0">Warranty</h4>

              <div class="col-12 table_content">
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Warranty Summary</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>
                      : 1 Year Warranty Provided by the Manufacturer from Date
                      of Invoice
                    </p>
                  </div>
                </div>
                <div class="row gx-0">
                  <div class="col-12 col-sm-12 col-md-2">
                    <p>Domestic Warranty</p>
                  </div>
                  <div class="col-12 col-sm-12 col-md-10">
                    <p>: 1 Year</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="vertical-padding sidepadding">
        <div class="container-fluid">
          <div class="row">
            <h5
              class="text-center font-dancing font-size-31"
              style="color: var(--golden)"
            >
              Treding Product
            </h5>

            <h3 class="text-center font-size-57 font-work-sans text-white">
              Recently <span style="color: var(--golden)">viewed</span>
            </h3>

            <div class="row products my-5 justify-content-center">
              <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                <img
                  src="./assest/product/image 27.png"
                  alt=""
                  class="img-fluid"
                />
                <div class="product-details">
                  <p class="text-white font-size-20 font-family-sans">
                    Blaupunkt Germany's SBW200
                  </p>
                </div>
                <div class="rating-price d-flex justify-content-between">
                  <div class="price text-white font-size-20 font-family-sans">
                    $122.00
                  </div>
                  <div class="rating d-flex">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                <img
                  src="./assest/product/image 28.png"
                  alt=""
                  class="img-fluid"
                />
                <div class="product-details">
                  <p class="text-white font-size-20 font-family-sans">
                    Blaupunkt Germany's SBW200
                  </p>
                </div>
                <div class="rating-price d-flex justify-content-between">
                  <div class="price text-white font-size-20 font-family-sans">
                    $122.00
                  </div>
                  <div class="rating d-flex">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                <img
                  src="./assest/product/image 29.png"
                  alt=""
                  class="img-fluid"
                />
                <div class="product-details">
                  <p class="text-white font-size-20 font-family-sans">
                    Blaupunkt Germany's SBW200
                  </p>
                </div>
                <div class="rating-price d-flex justify-content-between">
                  <div class="price text-white font-size-20 font-family-sans">
                    $122.00
                  </div>
                  <div class="rating d-flex">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                <img
                  src="./assest/product/image 30.png"
                  alt=""
                  class="img-fluid"
                />
                <div class="product-details">
                  <p class="text-white font-size-20 font-family-sans">
                    Blaupunkt Germany's SBW200
                  </p>
                </div>
                <div class="rating-price d-flex justify-content-between">
                  <div class="price text-white font-size-20 font-family-sans">
                    $122.00
                  </div>
                  <div class="rating d-flex">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- treding product -->

    <!-- testimonail -->

    <!-- /testimonial -->

    <!-- treding -end -->
    <footer class="vertical-padding sidepadding">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6 col-lg-4 fotter-margin px=5">
            <div class="">
              <img src="./assest/img/logo.png" alt="" />
              <p class="text-white py-3">
                Lorem Ipsum is simply dummy text of the printing and typesetting
                industry. Lorem Ipsum has been the industry's standard dummy
                text ever since the 1500s, when an unknown printer took a galley
                of type and scrambled it to make a type specimen book.
              </p>
              <div class="social text-white">
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-twitter"></i>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-2 px-2 fotter-margin">
            <h3 class="text-white font-size-25">INFORMATION</h3>
            <ul>
              <li><a href="#">About Us</a></li>
              <li><a href="#">Delivery Information`</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms & Conditions</a></li>
              <li><a href="#">Site Map</a></li>
            </ul>
          </div>

          <div class="col-md-6 col-lg-2 px-2 fotter-margin">
            <h3 class="text-white font-size-25">Extras</h3>
            <ul>
              <li><a href="#">Gift Certificates</a></li>
              <li><a href="#">Specials</a></li>
              <li><a href="#">Contact Us</a></li>
            </ul>
          </div>
          <div class="col-md-6 col-lg-4 px-2 .fotter-margin">
            <h3 class="text-white font-size-25">Contact</h3>
            <ul>
              <li>
                <a href="#"
                  ><i class="fa-solid fa-location-dot"></i> Lorem ipsum dolor
                  sit amet, c , 4022 Akouda, Tunisie</a
                >
              </li>
              <li>
                <a href="#"
                  ><i class="fa-solid fa-phone"></i>12 324 334 26 456 600</a
                >
              </li>
              <li>
                <a href="#"
                  ><i class="fa-solid fa-envelope"></i> conatact@mail.com</a
                >
              </li>
            </ul>
          </div>

          <div class="row">
            <div class="col-6"></div>
            <div class="col-6 text-end">
              <img src="./assest/img/payment.png" alt="" class="img-fluid" />
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- popup -->
    <!-- <div class="holder">
      <a href="#" id="test">Contact</a>
    </div> -->

    <div id="contactform">
      <form id="contact-form" class="contact-form" action="#" method="post">
        <div class="d-flex justify-content-between align-content-center">
          <h1>Popup Form</h1>
          <span class="fs-4 text-end closep" style="margin-top: 0">x</span>
        </div>
        <ul>
          <li class="comment">
            <!--Tip: textarea breaks if tags are not in the same line or broken up somehow -->
            <textarea
              class="text-comment"
              rows="2"
              id="text"
              spellcheck="false"
              placeholder="Say something."
              min-length="2"
              max-length="500"
              required
            ></textarea>
            <label for="message">Message</label>
            <span class="notification"></span>
            <span class="required"
              ><strong
                ><small><em> (required)</em></small></strong
              ></span
            >
            <span class="char-counter"></span>
          </li>
          <li class="name">
            <input
              type="text"
              class="input-name"
              name="name"
              placeholder="ie. Joe McDoodle"
              spellcheck="false"
              min-length="2"
              max-length="40"
              required
            />
            <label for="name">Name</label>
            <span class="notification"></span>
            <span class="required"
              ><strong
                ><small><em> (required)</em></small></strong
              ></span
            >
            <span class="char-counter"></span>
          </li>
          <li class="email">
            <input
              type="email"
              class="input-email"
              name="email"
              placeholder="ie. my@email.com"
              spellcheck="false"
              min-length="2"
              max-length="30"
              required
            />
            <label for="email">Email</label>
            <span class="notification"></span>
            <span class="required"
              ><strong
                ><small><em> (required)</em></small></strong
              ></span
            >
            <span class="char-counter"></span>
          </li>
        </ul>
        <!-- Submit -->
        <button class="btn1 my-3">Submit</button>
      </form>
    </div>
    <!-- popup -->
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

    <script src="./js/App.js"></script>
    <script src="./js/popup.js"></script>
    <script
      src="https://code.jquery.com/jquery-3.6.0.min.js"
      integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
      integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>

    <script>
      $(".slider").slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        autoplay: true,
        autoplaySpeed: 2000,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              dots: true,
            },
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2,
            },
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
          // You can unslick at a given breakpoint now by adding:
          // settings: "unslick"
          // instead of a settings object
        ],
      });
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
