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
    <link rel="stylesheet" href="assets/css/companyinfo.css">
    <link rel="stylesheet" href="assets/css/creategroup.css">
    <!-- Google Font Link for Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
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
                            <a href="index.html"><img src="assets/images/dashboard.svg" class="img-fluid"
                                    alt="">Dashboard</a>
                            <a href="leads.html" class="dashboard_active"><img src="assets/images/leadswhite.png"
                                    class="img-fluid" alt="">Leads</a>
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
                                <div class="lead_details__title">
                                    <h3 class="text-capitalize">lead details</h3>
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
                            <div class="details_container">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="details_left__container">
                                            <div class="details_left">
                                                <div class="details_flex">
                                                    <div class="user--details">
                                                        <div class="details_img">
                                                            <img src="assets/images/dashboard_profile.svg"
                                                                class="img-fluid" alt="">
                                                        </div>
                                                        <div class="details_name">
                                                            <h3>Priyanka</h3>
                                                            <p>Existing Customer</p>
                                                        </div>
                                                    </div>
                                                    <div class="social">
                                                        <a href="#"><img src="assets/images/mail.png" class="img-fluid"
                                                                alt=""></a>
                                                        <a href="#"><img src="assets/images/whatsapp.png"
                                                                class="img-fluid" alt=""></a>
                                                        <a href="#"><img src="assets/images/inbox.png" class="img-fluid"
                                                                alt=""></a>
                                                        <a href="#"><img src="assets/images/calling.png"
                                                                class="img-fluid" alt=""></a>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="back_page">
                                                <a href="leads.html"><img src="assets/images/Arrow - Left.png"
                                                        class="img-fluid" alt="">Back</a>
                                                <p>Last Update : 10 day(s) age</p>
                                            </div>
                                            <div class="tabs_wrapper">
                                                <div class="tabs_heading">
                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="home-tab"
                                                                data-bs-toggle="tab" data-bs-target="#home"
                                                                type="button" role="tab" aria-controls="home"
                                                                aria-selected="true">Details</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="profile-tab"
                                                                data-bs-toggle="tab" data-bs-target="#profile"
                                                                type="button" role="tab" aria-controls="profile"
                                                                aria-selected="false">Note</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="contact-tab"
                                                                data-bs-toggle="tab" data-bs-target="#contact"
                                                                type="button" role="tab" aria-controls="contact"
                                                                aria-selected="false">Payment</button>
                                                        </li>
                                                        <li class="nav-item myEdit" role="presentation">
                                                            <button class="nav-link" id="contact-tab"
                                                                data-bs-toggle="tab" data-bs-target="#edit"
                                                                type="button" role="tab" aria-controls="contact"
                                                                aria-selected="false">Edit</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="tabs_content">
                                                    <div class="tab-content" id="myTabContent">
                                                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                                                            aria-labelledby="home-tab">
                                                            <div class="details_box">
                                                                <div class="details_box__content">
                                                                    <h3>Lead Owner</h3>
                                                                    <div class="details_box__right">
                                                                        <p>Ramesh</p>
                                                                    </div>

                                                                </div>
                                                                <div class="details_box__content">
                                                                    <h3>Email</h3>
                                                                    <div class="details_box__right">
                                                                        <a href="#">www.sonu@gmail.com <img
                                                                                src="assets/images/Verified.png"
                                                                                class="img-fluid" alt=""></a>
                                                                    </div>
                                                                </div>
                                                                <div class="details_box__content">
                                                                    <h3>Phone NUmber</h3>
                                                                    <div class="details_box__right">
                                                                        <a href="#" class="number">9560XXXXXX <img
                                                                                src="assets/images/Verified.png"
                                                                                class="img-fluid" alt=""></a>
                                                                    </div>
                                                                </div>
                                                                <div class="details_box__content">
                                                                    <h3>Mobile Number</h3>
                                                                    <div class="details_box__right">
                                                                        <a href="#" class="number">9560XXXXXX <img
                                                                                src="assets/images/Close Circle.png"
                                                                                class="img-fluid" alt=""></a>
                                                                    </div>
                                                                </div>
                                                                <div class="details_msg">
                                                                    <h3>Message</h3>
                                                                    <p>Lorem Ipsum is simply dummy text of the printing
                                                                        and typesetting industry. Lorem Ipsum has been
                                                                        the industry's standard dummy text ever since
                                                                        the 1500s, when an unknown printer took a galley
                                                                        of type and scrambled it to make a type specimen
                                                                        book. It has survived not only five centuries,
                                                                        but also the leap intoelectronic typesetting,
                                                                        remaining essentially unchanged.</p>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="tab-pane fade" id="profile" role="tabpanel"
                                                            aria-labelledby="profile-tab">
                                                            <div class="note_wrapper">
                                                                <div class="note_date">
                                                                    <h3>21 Nov, 2023</h3>
                                                                </div>
                                                                <div class="note_box">
                                                                    <h3>The want 5 days stay</h3>
                                                                    <p>Nemo enim ipsam voluptatem quia voluptas sit
                                                                        aspernatur aut odit aut fugit, sed quia
                                                                        consequuntur ma esse cillum dolore eu fugiat
                                                                        nulla pariatuExcepteur sint occaecat cupidatat
                                                                        non proident, sunt in culpa qui officia deserunt
                                                                        mollit anim id es Sed ut perspiciatis unde omnis
                                                                        iste natus error sit voluptatem accusantium
                                                                        doloremque laudantium,</p>
                                                                    <div class="note_box_name">
                                                                        <h6><span>by</span> Sonu kumar jha</h6>
                                                                        <p>07:32 PM</p>
                                                                    </div>
                                                                </div>
                                                                <div class="note_wrapper__content">
                                                                    <div class="note_date">
                                                                        <img src="assets/images/Left 1.png"
                                                                            class="img-fluid" alt="">
                                                                    </div>
                                                                    <div class="note_box">
                                                                        <h3>The want 5 days stay</h3>
                                                                        <p>Nemo enim ipsam voluptatem quia voluptas sit
                                                                            aspernatur aut odit aut fugit, sed quia
                                                                            consequuntur ma esse cillum dolore eu fugiat
                                                                            nulla pariatuExcepteur sint occaecat
                                                                            cupidatat
                                                                            non proident, sunt in culpa qui officia
                                                                            deserunt
                                                                            mollit anim id es Sed ut perspiciatis unde
                                                                            omnis
                                                                            iste natus error sit voluptatem accusantium
                                                                            doloremque laudantium,</p>
                                                                        <div class="note_box_name">
                                                                            <h6><span>by</span> Sonu kumar jha</h6>
                                                                            <p>07:32 PM</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="note_wrapper__content">
                                                                    <div class="note_date">
                                                                        <img src="assets/images/Left 1.png"
                                                                            class="img-fluid" alt="">
                                                                    </div>
                                                                    <div class="note_box">
                                                                        <h3>The want 5 days stay</h3>
                                                                        <p>Nemo enim ipsam voluptatem quia voluptas sit
                                                                            aspernatur aut odit aut fugit, sed quia
                                                                            consequuntur ma esse cillum dolore eu fugiat
                                                                            nulla pariatuExcepteur sint occaecat
                                                                            cupidatat
                                                                            non proident, sunt in culpa qui officia
                                                                            deserunt
                                                                            mollit anim id es Sed ut perspiciatis unde
                                                                            omnis
                                                                            iste natus error sit voluptatem accusantium
                                                                            doloremque laudantium,</p>
                                                                        <div class="note_box_name">
                                                                            <h6><span>by</span> Sonu kumar jha</h6>
                                                                            <p>07:32 PM</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="note_wrapper__content">
                                                                    <div class="note_date">
                                                                        <img src="assets/images/Left 1.png"
                                                                            class="img-fluid" alt="">
                                                                    </div>
                                                                    <div class="note_box">
                                                                        <h3>The want 5 days stay</h3>
                                                                        <p>Nemo enim ipsam voluptatem quia voluptas sit
                                                                            aspernatur aut odit aut fugit, sed quia
                                                                            consequuntur ma esse cillum dolore eu fugiat
                                                                            nulla pariatuExcepteur sint occaecat
                                                                            cupidatat
                                                                            non proident, sunt in culpa qui officia
                                                                            deserunt
                                                                            mollit anim id es Sed ut perspiciatis unde
                                                                            omnis
                                                                            iste natus error sit voluptatem accusantium
                                                                            doloremque laudantium,</p>
                                                                        <div class="note_box_name">
                                                                            <h6><span>by</span> Sonu kumar jha</h6>
                                                                            <p>07:32 PM</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="note_wrapper__content">
                                                                    <div class="note_date">
                                                                        <img src="assets/images/Left 1.png"
                                                                            class="img-fluid" alt="">
                                                                    </div>
                                                                    <div class="note_box">
                                                                        <h3>The want 5 days stay</h3>
                                                                        <p>Nemo enim ipsam voluptatem quia voluptas sit
                                                                            aspernatur aut odit aut fugit, sed quia
                                                                            consequuntur ma esse cillum dolore eu fugiat
                                                                            nulla pariatuExcepteur sint occaecat
                                                                            cupidatat
                                                                            non proident, sunt in culpa qui officia
                                                                            deserunt
                                                                            mollit anim id es Sed ut perspiciatis unde
                                                                            omnis
                                                                            iste natus error sit voluptatem accusantium
                                                                            doloremque laudantium,</p>
                                                                        <div class="note_box_name">
                                                                            <h6><span>by</span> Sonu kumar jha</h6>
                                                                            <p>07:32 PM</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="contact" role="tabpanel"
                                                            aria-labelledby="contact-tab">
                                                            <div class="payment_wrapper">
                                                                <div class="payment_wrapper__heading">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_title">
                                                                                <h3>Date</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_title">
                                                                                <h3>Mood</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_title">
                                                                                <h3>Description</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_title">
                                                                                <h3>Amount</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_title">
                                                                                <h3>Status</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title">
                                                                                <h3>Successful</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title">
                                                                                <h3>Successful</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title">
                                                                                <h3>Successful</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title failed">
                                                                                <h3>Failed</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title">
                                                                                <h3>Successful</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="payment_content">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>21 Nov, 2023</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>Cart</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="payment_details_title">
                                                                                <h3>Travel Booking</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="payment_details_title">
                                                                                <h3>₹15,000</h3>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <div class="status_title">
                                                                                <h3>Successful</h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="tab-pane fade" id="edit" role="tabpanel"
                                                            aria-labelledby="contact-tab">
                                                            <div class="details_box">
                                                                <form action="">
                                                                    <div class="edit_wrapper">
                                                                        <div class="edit_content">
                                                                            <div class="edit_content__left">
                                                                                <h6>Lead Owner</h6>
                                                                            </div>
                                                                            <div class="edit_input">
                                                                                <input type="text" placeholder="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="edit_content">
                                                                            <div class="edit_content__left">
                                                                                <h6>Email</h6>
                                                                            </div>
                                                                            <div class="edit_input">
                                                                                <input type="text" placeholder="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="edit_content">
                                                                            <div class="edit_content__left">
                                                                                <h6>Phone Number</h6>
                                                                            </div>
                                                                            <div class="edit_input">
                                                                                <input type="text" placeholder="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="edit_content">
                                                                            <div class="edit_content__left">
                                                                                <h6>Mobile Number</h6>
                                                                            </div>
                                                                            <div class="edit_input">
                                                                                <input type="text" placeholder="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="details_msg">
                                                                            <h3>Message</h3>
                                                                            <p>Lorem Ipsum is simply dummy text of the
                                                                                printing
                                                                                and typesetting industry. Lorem Ipsum
                                                                                has been
                                                                                the industry's standard dummy text ever
                                                                                since
                                                                                the 1500s, when an unknown printer took
                                                                                a galley
                                                                                of type and scrambled it to make a type
                                                                                specimen
                                                                                book. It has survived not only five
                                                                                centuries,
                                                                                but also the leap intoelectronic
                                                                                typesetting,
                                                                                remaining essentially unchanged.</p>
                                                                        </div>
                                                                        <div class="form_btn">
                                                                            <button class="global_btn">Save</button>
                                                                            <button
                                                                                class="form_btn__cancel">Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="details_right">
                                            <div class="details_right__content">
                                                <button class="global_btn" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal3">Note</button>
                                                <button class="global_btn" data-bs-toggle="modal"
                                                    data-bs-target="#payment">Payment Link</button>
                                                <button class="global_btn" data-bs-toggle="modal"
                                                    data-bs-target="#follow">Follow Up</button>
                                                <button class="global_btn itinerary">Itinerary</button>
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
                                        <button type="button" class="btn cancel_btn"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn green_btn">Logout</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal note form-->
                        <div class="lead_form note_form">
                            <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="container-fluid px-0">
                                                <div class="row">
                                                    <div class="col-lg-7 px-0">
                                                        <div class="lead_form__left">
                                                            <h3>Note</h3>
                                                            <div class="lead_form__content">
                                                                <form action="">
                                                                    <div class="lead_form__input">
                                                                        <select class="form-select"
                                                                            aria-label="Default select example">
                                                                            <option selected>Title
                                                                            </option>
                                                                            <option value="1">One</option>
                                                                            <option value="2">Two</option>
                                                                            <option value="3">Three</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="lead_form__input">
                                                                        <textarea name="" placeholder="Message" id=""
                                                                            cols="30" rows="10"></textarea>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 px-0">
                                                        <div class="lead_form__right">
                                                            <div class="lead_button">
                                                                <button data-bs-dismiss="modal"><img
                                                                        src="assets/images/Close.png"
                                                                        alt="">Cancel</button>
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

                        <!-- Modal payment -->
                        <div class="payment_modal">
                            <div class="modal fade" id="payment" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="container-fluid px-0">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="payment_modal__content">
                                                            <div class="payment_heading">
                                                                <h3>Payment link</h3>
                                                                <div class="payment_btn">
                                                                    <button data-bs-dismiss="modal"><img
                                                                            src="assets/images/cross.png"
                                                                            class="img-fluid" alt=""></button>
                                                                </div>
                                                            </div>
                                                            <div class="payment_form">
                                                                <form action="">
                                                                    <div class="payment_form__content">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="flexRadioDefault"
                                                                            id="flexRadioDefault1">
                                                                        <label class="form-check-label"
                                                                            for="flexRadioDefault1">
                                                                            <div class="payment_form__left">
                                                                                <img src="assets/images/sack-dollar 1.png"
                                                                                    class="img-fluid" alt=""> Full
                                                                                Payment
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                    <div class="payment_form__content">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="flexRadioDefault"
                                                                            id="flexRadioDefault2" checked>
                                                                        <label class="form-check-label"
                                                                            for="flexRadioDefault2">
                                                                            <div class="payment_form__left">
                                                                                <img src="assets/images/hand-holding-usd 1.png"
                                                                                    class="img-fluid" alt=""> Minimum
                                                                                Amount
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                    <div class="payment_form__content">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="flexRadioDefault"
                                                                            id="flexRadioDefault3" checked>
                                                                        <label class="form-check-label"
                                                                            for="flexRadioDefault3">
                                                                            <div class="payment_form__left">
                                                                                <img src="assets/images/coins 1.png"
                                                                                    class="img-fluid" alt=""> Customise
                                                                                Payment
                                                                            </div>
                                                                            <div class="payment_amount">
                                                                                <h3>₹6,000</h3>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                    <div class="total_payment">
                                                                        <h6 class="text-capitalize">total amount</h6>
                                                                        <div class="total_payment__price">
                                                                            <h3>₹6,000</h3>
                                                                            <p>Import duties included</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="send_btn">
                                                                        <button class="global_btn">Send Link</button>
                                                                    </div>
                                                                </form>
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

                        <!-- calender add user-->
                        <div class="lead_form">
                            <div class="modal fade" id="follow" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="container-fluid px-0">
                                                <div class="row">
                                                    <div class="col-lg-7 px-0">
                                                        <div class="lead_form__left">
                                                            <h3>Follow up</h3>
                                                            <div class="lead_form__content">
                                                                <div class="wrapper">
                                                                    <header>
                                                                        <p class="current-date"></p>
                                                                        <div class="icons">
                                                                            <span id="prev"
                                                                                class="material-symbols-rounded">chevron_left</span>
                                                                            <span id="next"
                                                                                class="material-symbols-rounded">chevron_right</span>
                                                                        </div>
                                                                    </header>
                                                                    <div class="calendar">
                                                                        <ul class="weeks">
                                                                            <li>Sun</li>
                                                                            <li>Mon</li>
                                                                            <li>Tue</li>
                                                                            <li>Wed</li>
                                                                            <li>Thu</li>
                                                                            <li>Fri</li>
                                                                            <li>Sat</li>
                                                                        </ul>
                                                                        <ul class="days"></ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 px-0">
                                                        <div class="lead_form__right">
                                                            <div class="calender_msg">
                                                                <h6>Message</h6>
                                                                <div class="msg_box">
                                                                    <p>Nemo enim ipsam voluptatem voluptas sit
                                                                        aspernatur aut odit aut fugit, sed consequuntur
                                                                        maesse cillum dolore eu fugipariatuExcepteur
                                                                        sint occaecat cupidatat Nemo enim ipsam
                                                                        voluptatem voluptas sit aspernatur aut odit aut
                                                                        fugit, voluptas sit aspernatur aut odit aut
                                                                        fugit, sed consequuntur maesse cillum dolore eu
                                                                        fugipariatuExcepteur sint occaecat cupidatat
                                                                        Nemo enim ipsam voluptatem voluptas sit
                                                                        aspernatur aut odit aut fugit,</p>
                                                                </div>
                                                            </div>
                                                            <div class="lead_button">
                                                                <button data-bs-dismiss="modal"><img
                                                                        src="assets/images/Close.png"
                                                                        alt="">Cancel</button>
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="itinerary_wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="itinerary_wrapper__left">
                        <h3>Manali Trip</h3>
                        <p>Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma
                            esse cillum dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam
                            voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum dolore eu
                            fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas sit
                            aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit
                            aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum
                            dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit, Nemo enim ipsam voluptatem voluptas sit aspernatur aut
                            odit aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum
                            dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas sit aspernatur aut
                            odit aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum
                            dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas sit aspernatur aut
                            odit aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum
                            dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas sit aspernatur aut
                            odit aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed consequuntur ma esse cillum
                            dolore eu fugipariatuExcepteur sint occaecat cupidatat Nemo enim ipsam voluptatem voluptas
                            sit aspernatur aut odit aut fugit,Nemo enim ipsam voluptatem voluptas sit aspernatur aut
                            odit aut fugit, sed consequuntur ma esse cillum dolore eu fugipariatuExcepteur sint occaecat
                            cupidatat Nemo enim ipsam voluptatem voluptas sit aspernatur aut odit aut fugit,Nemo enim
                            ipsam voluptatem voluptas sit aspernatur aut odit aut fugit, sed</p>


                    </div>
                </div>
                <div class="col-lg-4">

                    <div class="lead_form__right">
                        <div class="lead_button itinerary_btn">
                            <button data-bs-dismiss="modal"><img src="assets/images/Download.png" class="img-fluid"
                                    alt="">Download PDF</button>
                        </div>
                        <div class="lead_button itinerary_btn">
                            <button><img src="assets/images/telegram-fill.png" class="img-fluid" alt="">Send
                                Itinerary</button>
                        </div>
                        <div class="cancel_itinerary">
                            <img src="assets/images/cancel_white.png" class="img-fluid" alt="">
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
    <script src="assets/js/calender.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>