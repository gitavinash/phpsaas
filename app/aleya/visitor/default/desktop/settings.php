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
    <link rel="stylesheet" href="assets/css/settings.css">
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
                                <a href="settings.html"><img src="assets/images/management.png" class="img-fluid"
                                        alt="">User Profile
                                </a>
                                <a href="companyinfo.html" class="dropdown_group"><img src="assets/images/group.png"
                                        class="img-fluid" alt="">Company Info</a>
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
                                    <h3 class="text-capitalize">User Profile</h3>
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
                        <div class="profile_wrapper">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="profile_box">
                                        <div class="profile_box__top">
                                            <div class="profile_left">
                                                <div class="user_img">
                                                    <img src="assets/images/profile.png" class="img-fluid" alt="">
                                                </div>
                                                <h6>Dinesh</h6>
                                            </div>
                                            <div class="profile_right">
                                                <div class="star">
                                                    <img src="assets/images/Star.png" class="img-fluid" alt="">
                                                </div>
                                                <div class="edit">
                                                    <a href="editprofile.html"><img src="assets/images/pencil.png"
                                                            class="img-fluid" alt=""></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="profile_details">
                                            <div class="profile_details__content">
                                                <a href="#" class="profile_flex"><img src="assets/images/callblack.png"
                                                        class="img-fluid" alt="">+91-9560XXXXXX</a>
                                            </div>
                                            <div class="profile_details__content">
                                                <a href="#" class="profile_flex"><img src="assets/images/Mask group.png"
                                                        class="img-fluid" alt="">Lorem ipsum dolor sit amet, consectetur
                                                    adipiscing
                                                    elit,</a>
                                            </div>
                                            <div class="profile_details__content">
                                                <p class="profile_flex"><img src="assets/images/male.png"
                                                        class="img-fluid" alt="">Male
                                                </p>
                                            </div>
                                            <div class="profile_details__content">
                                                <a href="#" class="profile_flex"><img src="assets/images/gmail.png"
                                                        class="img-fluid" alt="">www.gmail.com</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="profile_box">
                                        <div class="recent_heading">
                                            <h3>Recent Activity</h3>
                                            <a href="#"><img src="assets/images/pencil.png" class="img-fluid"
                                                    alt=""></a>
                                        </div>
                                        <div class="recent_wrapper">
                                            <div class="recent_wrapper__content">
                                                <h6>Owner</h6>
                                                <p>BDo Team Skinqure 3</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Lead Source</h6>
                                                <p>Inbound Phone Call</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Alternate Contact Number</h6>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Lead Age</h6>
                                                <p>238 Days</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Lead Group</h6>
                                                <p>Laser</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Lead Sub Group</h6>
                                                <p>Laser Hair Removal</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Notes</h6>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Lead Origin</h6>
                                                <p>SR-1 - 73XXXXXXXX</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Created On</h6>
                                                <p>08/11/2023 11:13:00 AM</p>
                                            </div>
                                            <div class="recent_wrapper__content">
                                                <h6>Created By</h6>
                                                <p>System</p>
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