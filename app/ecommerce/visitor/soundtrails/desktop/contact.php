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
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/Home.css" />
    <link rel="stylesheet" href="./css/navbar.css" />
    <link rel="stylesheet" href="./css/Contact.css" />

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
            <h5>HOME / <span>CONTACT</span></h5>
          </div>
        </div>
      </div>
      <!-- hero -->

      <!-- end -->
    </section>

    <section class="vertical-padding sidepadding">
      <div class="container-fluid">
        <div class="row py-3">
          <h2 class="text-white text-center">
            Need help? Have a question about an order, or about getting in
            touch?<br />
            We’re always happy to hear from you.
          </h2>

          <div class="row vertical-padding my-5">
            <div
              class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center"
            >
              <div>
                <img src="./assest/img/3.png" alt="" />
                <h4
                  class="font-size-57 fw-bold mt-3 text-white text-center my-3"
                >
                  <span class="font-size-40">Whatsapp</span>
                </h4>
                <h4 class="text-white text-center">+91 99719 22259</h4>
              </div>
            </div>
            <div
              class="col-12 col-sm-12 col-lg-4 col-md-4 side-boder d-flex justify-content-center text-center"
            >
              <div>
                <img src="./assest/img/1.png" alt="" />
                <h4
                  class="font-size-57 fw-bold mt-3 text-white text-center my-3"
                >
                  <span class="font-size-40">Call Us</span>
                </h4>
                <h4 class="text-white text-center">+91 99719 22259</h4>
              </div>
            </div>

            <div
              class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center"
            >
              <div>
                <img src="./assest/img/2.png" alt="" />
                <h4
                  class="font-size-57 fw-bold mt-3 text-white text-center my-3"
                >
                  <span class="font-size-40">Write To us</span>
                </h4>
                <h4 class="text-white text-center">Mail@mail.com</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="form-data sidepadding mb-5">
      <div class="container">
        <div class="row d-flex justify-content-center">
          <div class="col-12 col-md-8">
            <form action="">
              <div class="d-flex flex-column">
                <input
                  type="text"
                  class="forms"
                  placeholder="Enter Your name"
                />

                <input
                  type="text"
                  class="forms"
                  placeholder="Enter Your Email"
                />
                <input
                  type="text"
                  class="forms"
                  placeholder="Enter Your phone"
                />
                <textarea
                  name=""
                  id=""
                  cols="30"
                  rows="10"
                  placeholder="Message"
                ></textarea>

                <input
                  type="submit"
                  value="Submit"
                  class="submit rounded-pill text-center"
                />
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <section class="vertical-padding sidepadding d-flex align-content-center">
      <div class="container-fluid">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-12 col-lg-6">
            <h5 class="text-white font-size-25">
              Stay Connected With Our Email Updates
            </h5>
          </div>
          <div class="col-12 col-lg-6">
            <div
              class="newsleeter d-flex m-0 justify-content-center mt-3 mt-md-0"
            >
              <input
                type="search"
                class="newsleeter-bar m-0"
                placeholder="Enter Your Email"
              />
              <button class="btn1 m-0">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </section>

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
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>
    <script src="./js/App.js"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
