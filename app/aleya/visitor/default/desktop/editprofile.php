<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/leads.css">
    <link rel="stylesheet" href="assets/css/leaddetails.css">
    <link rel="stylesheet" href="assets/css/usermanage.css">
    <link rel="stylesheet" href="assets/css/accessgroup.css">
    <link rel="stylesheet" href="assets/css/creategroup.css">
    <link rel="stylesheet" href="assets/css/settings.css">
    <link rel="stylesheet" href="assets/css/companyinfo.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <section class="dashboard_wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-2 col-3 px-0">
                    <div class="dashboard_fixed">
                        <div class="dashboard_logo">
                            <a href="index.html"><img src="assets/images/aleya_dashboard.png" class="img-fluid"
                                    alt=""></a>
                        </div>
                        <div class="dashboard_menu">
                            <a href="index.html"><img src="assets/images/dashboardblack.png" class="img-fluid"
                                    alt="">Dashboard</a>
                            <a href="leads.html"><img src="assets/images/leads.svg" class="img-fluid" alt="">Leads</a>
                            <a href="usermanage.html"><img src="assets/images/user_manage.svg" class="img-fluid"
                                    alt="">User
                                manage</a>
                            <a href="settings.html" class="dashboard_active"><img src="assets/images/settingwhite.png"
                                    class="img-fluid" alt="">Settings</a>
                            <div class="user_dropdown">
                                <a href="settings.html" class="dropdown_group"><img src="assets/images/profilewhite.png"
                                        class="img-fluid" alt="">User Profile
                                </a>
                                <a href="companyinfo.html"><img src="assets/images/infogreen.png" class="img-fluid"
                                        alt="">Company Info</a>
                            </div>
                        </div>
                        <div class="logout">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal"><img src="assets/images/Login.png" class="img-fluid"
                                    alt="">Log Out</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-10 col-lg-9 col-12 px-0">
                    <div class="dashboard_wrapper__right">
                        <div class="dashboard_header">
                            <div class="dashboard_header__top">
                                <div class="lead_details__title">
                                    <h3 class="text-capitalize">Edit Profile</h3>
                                    <p>Lorem ipsum dolor sit amet.</p>
                                </div>
                                <div class="header_right">
                                    <div class="header_link">
                                        <a href="#"><img src="assets/images/message.svg" class="img-fluid" alt=""></a>
                                    </div>
                                    <div class="header_link notification">
                                        <a href="#"><img src="assets/images/notfication.svg" class="img-fluid"
                                                alt=""></a>
                                        <div class="notification_container">
                                            <div class="notification_dropdown">
                                                <div class="notification_content">
                                                    <div class="user_profile">
                                                        <img src="assets/images/dashboard_profile.svg" class="img-fluid"
                                                            alt="">
                                                    </div>
                                                    <div class="user_details">
                                                        <h3>Divya Thakur <span>new lead</span></h3>
                                                        <p>1 hours ago</p>
                                                    </div>
                                                </div>
                                                <div class="notification_content">
                                                    <div class="user_profile">
                                                        <img src="assets/images/dashboard_profile.svg" class="img-fluid"
                                                            alt="">
                                                    </div>
                                                    <div class="user_details">
                                                        <h3>Divya Thakur <span>new lead</span></h3>
                                                        <p>1 hours ago</p>
                                                    </div>
                                                </div>
                                                <div class="notification_content">
                                                    <div class="user_profile">
                                                        <img src="assets/images/dashboard_profile.svg" class="img-fluid"
                                                            alt="">
                                                    </div>
                                                    <div class="user_details">
                                                        <h3>Divya Thakur <span>new lead</span></h3>
                                                        <p>1 hours ago</p>
                                                    </div>
                                                </div>
                                                <div class="notification_content">
                                                    <div class="user_profile">
                                                        <img src="assets/images/dashboard_profile.svg" class="img-fluid"
                                                            alt="">
                                                    </div>
                                                    <div class="user_details">
                                                        <h3>Divya Thakur <span>new lead</span></h3>
                                                        <p>1 hours ago</p>
                                                    </div>
                                                </div>
                                                <div class="see_all">
                                                    <a href="#">See All</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="header_link">
                                        <a href="settings.html"><img src="assets/images/dashboard_profile.svg"
                                                class="img-fluid" alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="company_wrapper">
                            <div class="company_wrapper__top">
                                <div class="company_logo">
                                    <a href="settings.html"><img src="assets/images/Ellipse 10.png" class="img-fluid"
                                            alt=""></a>
                                </div>
                                <div class="company_top__right">
                                    <button class="blue"><img src="assets/images/pencil.png" alt="">Upload new</button>
                                    <button class="red"><img src="assets/images/delete.png" alt="">Remove</button>
                                </div>
                            </div>

                            <div class="register_form">
                                <form action="">
                                    <div class="register_form__content form_flex">
                                        <div class="register_input">
                                            <label for="">First Name</label>
                                            <input type="text">
                                        </div>
                                        <div class="register_input">
                                            <label for="">Last Name</label>
                                            <input type="text">
                                        </div>
                                    </div>
                                    <div class="register_form__content register_input">
                                        <label for="">Email</label>
                                        <input type="email">
                                    </div>
                                    <div class="register_form__content register_input">
                                        <label for="">Mobile Number</label>
                                        <input type="number">
                                    </div>
                                    <div class="register_form__content register_input">
                                        <label for="">address</label>
                                        <input type="text">
                                    </div>
                                    <div class="register_form__content form_flex">
                                        <div class="register_input">
                                            <label for="">City</label>
                                            <input type="text">
                                        </div>
                                        <div class="register_input">
                                            <label for="">State</label>
                                            <input type="text">
                                        </div>
                                    </div>
                                    <div class="register_form__content form_flex">
                                        <div class="register_input">
                                            <label for="">ZIP Code</label>
                                            <input type="text">
                                        </div>
                                        <div class="register_input">
                                            <label for="">Country</label>
                                            <input type="text">
                                        </div>
                                    </div>
                                    <div class="register_form__content register_input">
                                        <label for="">Password</label>
                                        <input type="password">
                                    </div>

                                    <div class="form_btn">
                                        <button class="global_btn">Submit</button>
                                        <button class="form_btn__cancel">Cancel</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- Modal logout -->
                    <div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <div class="logot_heading">
                                        <h3>Ae you sure, you want to <span>logout?</span></h3>
                                    </div>
                                    <div class="logout_img">
                                        <img src="assets/images/logout.png" class="img-fluid" alt="">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn cancel_btn" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn green_btn">Logout</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>












    <script src="assets/js/counter.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="assets/js/datepicker.js"></script>
    <script src="assets/js/notification.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>