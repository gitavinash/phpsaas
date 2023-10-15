<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Diagnostic care</title>
    <?php require('./links.php') ?>
</head>

<body>
    <?php require('./header.php') ?>

    <main>
        <section class="hero side-padding">
            <div class="container">
                <div class="row align-items-center" style="height: 30vh;">
                    <div class="col-12 col-lg-12 ">
                        <h2 class="isfont-40 text-white my-3 is-font600 text-center ">
                            payment
                        </h2>
                    </div>

                </div>
            </div>
        </section>

        <section>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <img src="./assest/img/upi.jpeg" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php require('./footer.php') ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        setInterval(() => {
            window.location.replace('./thankyou.php')
        }, 3000)
    </script>

</body>

</html>