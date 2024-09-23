<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="/app/system/visitor/install/assets/css/style.min.css">
    <title>Install website</title>
</head>

<body>

    <!-- Form area -->

    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
            <div class="col-md-8 col-lg-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-9 col-lg-8 mx-auto">
                                <h3 class="login-heading mb-4">Application installation form.</h3>
                                <form id="websiteSetupForm">

                                    <div class="form-label-group">
                                        <input type="text" id="inputFirstName" name="firstName" class="form-control" placeholder="First Name" required autofocus>
                                        <label for="inputFirstName">First Name</label>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="text" id="inputLastName" name="lastName" class="form-control" placeholder="Last Name" required>
                                        <label for="inputLastName">Last Name</label>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required>
                                        <label for="inputEmail">Email address</label>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="number" onwheel="this.blur()" id="inputMobile" name="mobile" class="form-control" placeholder="Mobile number" inputmode="numeric" pattern="[0-9]*" pattern="[6-9]{1}[0-9]{9}" title="Enter Your 10 Digit Mobile Number only" required>
                                        <label for="inputMobile">Mobile number</label>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
                                        <label for="inputPassword">Password</label>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="text" id="inputPasswordConfirm" class="form-control" placeholder="Confirm Password" required>
                                        <label for="inputPasswordConfirm">Confirm Password</label>
                                    </div>
                                    <input type="hidden" name="type" value="install">
                                    <input type="hidden" name="action" value="app">
                                    <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" id="setupApp">Sign in</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#setupApp').click(function(e) {
                e.preventDefault();
                $('#setupApp').hide();

                // var firstName = $('#inputFirstName').val();
                // var lastName = $('#inputLastName').val();
                // var email = $('#inputEmail').val();
                // var mobile = $('#inputMobile').val();
                var password = $('#inputPassword').val();
                var confirmPassword = $('#inputPasswordConfirm').val();

                var checkPassword = password.localeCompare(confirmPassword);

                if (checkPassword == 0) {

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: $('#websiteSetupForm').serialize(),
                        success: function(data) {

                            if (data.status == 200) {

                                window.location.href = data.redirect;

                            } else {

                                console.log(data);
                                $('#setupApp').show();

                            }
                        },
                        error: function(data) {
                            console.log(data);
                            $('#setupApp').show();
                        }
                    });

                } else {

                    alert('Password does not matched');
                    $('#setupApp').show();

                }

            });
        });
    </script>
</body>

</html>