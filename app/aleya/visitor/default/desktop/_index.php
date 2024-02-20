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
                            <a href="#" class="dashboard_active"><img src="assets/images/dashboard_white.png"
                                    class="img-fluid" alt="">Dashboard</a>
                            <a href="leads.html"><img src="assets/images/leads.svg" class="img-fluid" alt="">Leads</a>
                            <a href="usermanage.html"><img src="assets/images/user_manage.svg" class="img-fluid"
                                    alt="">User
                                manage</a>
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
                            <div class="header_box__flex">
                                <div class="box_wrapper">
                                    <div class="header_box">
                                        <div class="header_box__left">
                                            <h3>active enquiry</h3>
                                            <p>02</p>
                                        </div>
                                        <div class="time_icon">
                                            <img src="assets/images/bluetime.png" class="img-fluid" alt="">
                                        </div>
                                    </div>

                                </div>
                                <div class="box_wrapper">
                                    <div class="header_box">
                                        <div class="header_box__left box2">
                                            <h3>new enquiry</h3>
                                            <p>03</p>
                                        </div>
                                        <div class="time_icon">
                                            <img src="assets/images/greentime.png" class="img-fluid" alt="">
                                        </div>
                                    </div>

                                </div>
                                <div class="box_wrapper">
                                    <div class="header_box">
                                        <div class="header_box__left box3">
                                            <h3>recent enquiry</h3>
                                            <p>04</p>
                                        </div>
                                        <div class="time_icon">
                                            <img src="assets/images/black_time.png" class="img-fluid" alt="">
                                        </div>
                                    </div>

                                </div>
                                <div class="box_wrapper">
                                    <div class="header_box">
                                        <div class="header_box__left box4">
                                            <h3>pending enquiry</h3>
                                            <p>07</p>
                                        </div>
                                        <div class="time_icon">
                                            <img src="assets/images/yellow_time.png" class="img-fluid" alt="">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="graph_wrapper">
                            <div class="graph_flex">
                                <div class="graph_box">
                                    <div class="graph_heading">
                                        <h3>leads by sources</h3>
                                        <div class="menu">
                                            <img src="assets/images/elipsis.svg" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                    <div class="graph_image">
                                        <img src="assets/images/graph_1.png" class="img-fluid" alt="">
                                    </div>
                                </div>
                                <div class="graph_box">
                                    <div class="graph_heading">
                                        <h3>overall lead funnel</h3>
                                        <div class="menu">
                                            <img src="assets/images/elipsis.svg" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                    <div class="graph_image">
                                        <img src="assets/images/graph_2.png" class="img-fluid" alt="">
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
                                        <button type="button" class="btn cancel_btn"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn green_btn">Logout</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="filter_wrapper">
                            <div class="filter_top">
                                <div class="filter_head">
                                    <div class="filter_left">
                                        <p>Show</p>
                                        <div class="number">
                                            <p id="count"></p>
                                            <div class="number_flex">
                                                <div class="arrow" id="minus-btn""><i class='bx bxs-up-arrow'></i></div>
                                            <div class=" arrow" id="plus-btn"><i class='bx bxs-down-arrow'></i></div>
                                            </div>
                                            <h6>Entries</h6>
                                        </div>
                                    </div>
                                    <div class="filter_right">
                                        <div class="filter_btn">
                                            <p><i class='bx bx-filter'></i> FIlter</p>
                                            <div class="filter_dropdown">
                                                <div class="filter_dropdown__content">
                                                    <a href="#">All</a>
                                                    <a href="#">whatsapp</a>
                                                    <a href="#">Inbound email</a>
                                                    <a href="#">landing page</a>
                                                    <a href="#">inbound phone call</a>
                                                    <a href="#">Other</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter_date">
                                            <input type="text" id="demo2" placeholder="Select Date">
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_sub__heading">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <h3>Sr no.</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="subheading_name">
                                                    <h3>Name</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="subheading_name">
                                                    <h3>lead by sources</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <h3>status</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="subheading_name">
                                                    <h3>action</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter_container">
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>01</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Sonu sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize">active</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>03</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Vipin sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize blue">new</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>05</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Kaushal sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize blue">new</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>01</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Sonu sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize yellow">pending</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>03</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Vipin sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize">active</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="filter_input">
                                                    <input type="checkbox" name="" id="">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="subheading_name">
                                                    <p>05</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="myName">
                                                    <h3>Kaushal sharma</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="sources_title">
                                                    <h3 class="text-capitalize">inbound phone call</h3>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="staus_title">
                                                    <h3 class="text-capitalize">active</h3>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="action_flex">
                                                    <div class="pencil">
                                                        <img src="assets/images/pencil.png" class="img-fluid" alt="">
                                                    </div>
                                                    <div class="delete">
                                                        <img src="assets/images/delete.png" class="img-fluid" alt="">
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