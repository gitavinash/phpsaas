<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                            <a href="usermanage.html" class="dashboard_active"><img src="assets/images/userwhite.png"
                                    class="img-fluid" alt="">User manage
                            </a>
                            <div class="user_dropdown">
                                <a href="usermanage.html" class="dropdown_group"><img src="assets/images/user_shade.png"
                                        class="img-fluid" alt="">User
                                    Management</a>
                                <a href="accessgroup.html" class="dropdown_group"><img
                                        src="assets/images/whitegroup.png" class="img-fluid" alt="">Access
                                    Group</a>
                                <a href="creategroup.html"><img src="assets/images/greengroup.png" class="img-fluid"
                                        alt="">Create
                                    Group</a>
                            </div>
                            <a href="settings.html"><img src="assets/images/Setting.svg" class="img-fluid"
                                    alt="">Settings</a>
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
                                <div class="dashboard_search">
                                    <input type="text" placeholder="Search...">
                                    <div class="search_icon">
                                        <i class='bx bx-search'></i>
                                    </div>
                                </div>
                                <div class="header_right">
                                    <div class="date_select">
                                        <input type="text" id="demo" placeholder="Select a date">
                                        <div class="date_arrow">
                                            <i class='bx bx-chevron-down'></i>
                                        </div>
                                    </div>
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
                            <div class="access_group">
                                <div class="user_title">
                                    <h3>Create Group</h3>
                                    <p><img src="assets/images/refresh.png" class="img-fluid" alt="">Refresh</p>
                                </div>
                                <div class="access_group__right">
                                    <div class="dashboard_search access_search">
                                        <input type="text" placeholder="Search...">
                                        <div class="search_icon">
                                            <i class='bx bx-search'></i>
                                        </div>
                                    </div>
                                    <div class="invite add_user">
                                        <button class="global_btn" data-bs-toggle="modal" data-bs-target="#create"><img
                                                src="assets/images/pluswhite.png" class="img-fluid" alt="">Create Group
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="group_wrapper">
                            <div class="row">
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/Ellipse.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-6">
                                    <div class="group_card">
                                        <div class="card_top">
                                            <div class="card_logo">
                                                <img src="assets/images/in.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="group_name">
                                                <h6>Group Name</h6>
                                                <p>Lorem lipsom</p>
                                            </div>
                                        </div>
                                        <div class="card_parra">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore</p>
                                        </div>
                                        <div class="card_footer">
                                            <div class="card_footer__left">
                                                <h6>Sonu kumar jha</h6>
                                                <p>Created by</p>
                                            </div>
                                            <div class="card_footer__right card_footer__left">
                                                <h6>+25</h6>
                                                <p>Group Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal add user-->
                    <div class="lead_form">
                        <div class="modal fade" id="create" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="container-fluid px-0">
                                            <div class="row">
                                                <div class="col-lg-7 px-0">
                                                    <div class="lead_form__left">
                                                        <h3>Create Group</h3>
                                                        <div class="lead_form__content">
                                                            <form action="">
                                                                <div class="lead_form__input">
                                                                    <input type="text" placeholder="Name" name="" id="">
                                                                </div>
                                                                <div class="lead_form__input">
                                                                    <input type="number" placeholder="Create Date"
                                                                        name="" id="">
                                                                </div>
                                                                <div class="lead_form__input">
                                                                    <textarea name="" placeholder="Description" id=""
                                                                        cols="30" rows="4"></textarea>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 px-0">
                                                    <div class="lead_form__right">
                                                        <div class="lead_button">
                                                            <button data-bs-dismiss="modal"><img
                                                                    src="assets/images/Close.png" alt="">Cancel</button>
                                                        </div>
                                                        <div class="lead_button">
                                                            <button><img src="assets/images/Bookmark 1.png"
                                                                    class="img-fluid" alt="">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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