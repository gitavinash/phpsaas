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

  <section class="hero side-padding">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12 col-lg-7">
          <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
            Appointment
          </h2>
        </div>
        <div class="col-12 col-lg-5 my-4 my-lg-0">
          <img src="./assest/img/appointment.png" alt="" class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
        </div>
      </div>
    </div>
  </section>

  <section class="vartical-padding side-padding checkout">
    <div class="container">
      <div>
        <h3 class="isfont-40 is-font600 is-color-blue">Appointment</h3>
      </div>
      <div class="row vartical-padding align-items-top">
        <div class="col-12 col-lg-6">
          <div class="checkout-table">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Name</th>
                  <th scope="col">Price</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="is-color-blue is-font600 isfont-27">
                    Blood Grouping ( A B O ) and rh type
                  </td>
                  <td class="is-color-blue is-font600 isfont-18">₹200</td>
                  <td class="text-center">
                    <i class="bx bx-x-circle text-center fs-3 text-danger"></i>
                  </td>
                </tr>

                <tr>
                  <td class="is-color-blue is-font600 isfont-27">
                    Nickel blood
                  </td>
                  <td class="is-color-blue is-font600 isfont-18">₹200</td>
                  <td class="text-center">
                    <i class="bx bx-x-circle text-center fs-3 text-danger"></i>
                  </td>
                </tr>

                <tr>
                  <td class="is-color-blue is-font600 isfont-27">
                    Blood Urea
                  </td>
                  <td class="is-color-blue is-font600 isfont-18">₹200</td>
                  <td class="text-center">
                    <i class="bx bx-x-circle text-center fs-3 text-danger"></i>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="apointment-form">
            <form>
              <div class="row">
                <div class="col-12">
                  <div class="mb-3">
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Your name" value="Ritik Gupta" disabled />
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <input type="date" class="form-control" id="exampleInputPassword1" value="06-10-2023" />
                  </div>
                </div>

                <div class="col-6">
                  <div class="mb-3">
                    <input type="Time" class="form-control" id="exampleInputPassword1" />
                  </div>
                </div>

                <div class="col-12">
                  <div class="mb-3">
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Your Email" value="mail@mail.com" disabled />
                  </div>
                </div>

                <div class="col-12">
                  <textarea class="form-control" placeholder="Address..." id="floatingTextarea" disabled>
  T545 naer karol bagh New Delhi 1110006</textarea>
                </div>
              </div>
              <a href="./payment.php" type="submit" class="btn btn-primary w-100 my-4 py-3">
                Submit
              </a>

              <div>
                <p class="isfont-12 text-center text-">
                  <b></b>By clicking, you agree to our
                  <a href="">privacy policy</a>, Terms of use &
                  <a href="">disclaimers</a>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php require('./footer.php') ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>