<?php

if (isset($request_id)) {
    if ($request_id != '') {
        $validId = true;
    }
}

if (!isset($validId)) {
    header("Location: /", TRUE, 301);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once DEFAULT_META ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>

<body>
    <div class="container-fullwidth">
        <div class="row g-0">
            <div class="col-12 col-md-4">
                <aside class="d-flex flex-column justify-content-center align-items-center vh-100">
                    <div class="logo mb-5 text-center">
                        <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
                    </div>
                    <div class="w-100 d-flex justify-content-center align-items-center flex-grow-1">
                        <form action="" class="w-100" id="agreementForm">
                            <div id="infoBox">
                                <button id="landlord" class="btn btn-success w-100">Next, add landlord details</button>
                            </div>
                            <div id="landlordBox" style="display:none">
                                <button id="tenate" class="btn btn-success w-100">Next, add tenate
                                    details</button>
                            </div>
                            <div id="tenateBox" style="display:none">
                                <button id="house" class="btn btn-success w-100">Next, add property
                                    details</button>
                            </div>
                            <div id="houseBox" style="display:none">
                                <button id="" class="btn btn-success w-100">Next, add contract details</button>
                            </div>
                            <div id="contractBox" style="display:none">
                                <button id="contract" class="btn btn-success w-100">Submit & Continue</button>
                            </div>
                        </form>
                    </div>

                    <footer>
                        <p class="creditOnForm">Design and Developed
                            by <a href="https://blumox.com" target="_blank"><strong>Blumox Technologies</strong></a>
                        </p>
                        </a></p>
                    </footer>
                </aside>
            </div>
            <div class="col-12 col-md-8 d-none d-sm-block">
                <main>
                    <div class="mainBox d-flex align-items-center">
                        <div class="w-100">
                            <h1 class="w-100">Rent Agreement</h1>
                            <p class="w-100">Your rent agreement is just a few clicks away. After providing the
                                following
                                information...</p>
                            <div class="row">
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>info-active.png" id="infoImg" alt="">
                                        <p>Basic info</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>landlord.png" id="landlordImg" alt="">
                                        <p>Lnadlord</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>tenate.png" id="tenateImg" alt="">
                                        <p>Tenate</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>house.png" id="houseImg" alt="">
                                        <p>Property</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>contract.png" id="contractImg" alt="">
                                        <p>Contract</p>
                                    </div>
                                </div>
                                <!-- <div class="col">
                                <div class="featureIcon dir-left">
                                    <img src="<?php echo USER_IMAGES ?>secure.png" alt="">
                                    <p>Secure</p>
                                </div>
                            </div> -->
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="<?php echo USER_JS ?>app.min.js"></script>
</body>

</html>