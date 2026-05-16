<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate - DPJKS</title>
    <!-- Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700&display=swap"
      rel="stylesheet"
    />
    <!-- Icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css" />
    <style>
      .donation-header {
        background:
          linear-gradient(rgba(13, 59, 102, 0.9), rgba(13, 59, 102, 0.8)),
          url("assets/images/gallery/education/1000071289.jpg") center/cover;
        padding: 4rem 0;
        color: white;
        text-align: center;
      }

      .donation-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        margin-top: 3rem;
      }

      .donation-methods {
        background: var(--white);
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
      }

      .bank-details {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1.5rem;
        border-left: 4px solid var(--secondary-color);
      }

      .bank-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.8rem;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 0.5rem;
      }

      .bank-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
      }

      .bank-label {
        font-weight: 600;
        color: var(--primary-color);
      }

      .qr-section {
        text-align: center;
        margin-top: 2rem;
      }

      .qr-code {
        width: 200px;
        height: 200px;
        background: #ddd; /* Placeholder */
        margin: 1rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        border: 1px solid #ccc;
      }

      .impact-card {
        background: var(--primary-color);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        text-align: center;
      }

      .form-group {
        margin-bottom: 1.5rem;
      }

      .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-main);
      }

      .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: inherit;
      }

      .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
      }

      @media (max-width: 768px) {
        .donation-wrapper {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <div id="header-placeholder"></div>

    <header class="donation-header">
      <div class="container">
        <h1>Donate to Empower</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 1rem auto 0">
          Your contribution brings light to lives in rural Kanpur. Support us in
          building a better future.
        </p>
      </div>
    </header>

    <section class="section-padding">
      <div class="container">
        <div class="donation-wrapper">
          <!-- Left Column: Bank Details & QR -->
          <div class="donation-info">
            <h2 style="margin-bottom: 1.5rem">Ways to Support</h2>
            <p style="margin-bottom: 2rem; color: var(--text-light)">
              We accept direct bank transfers and online payments. Contributions support social welfare and education.
            </p>

            <div class="donation-methods">
              <h3 style="color: var(--primary-color)">
                <i class="fas fa-university" style="margin-right: 10px"></i>
                Bank Transfer
              </h3>
              <div class="bank-details">
                <div class="bank-row">
                  <span class="bank-label">Account Name:</span>
                  <span>Dharam Prachar Jan Kalyan Samiti</span>
                </div>
                <div class="bank-row">
                  <span class="bank-label">Account Number:</span>
                  <span>1234 5678 9012</span>
                </div>
                <div class="bank-row">
                  <span class="bank-label">IFSC Code:</span>
                  <span>SBIN0001234</span>
                </div>
                <div class="bank-row">
                  <span class="bank-label">Bank Name:</span>
                  <span>State Bank of India</span>
                </div>
                <div class="bank-row">
                  <span class="bank-label">Branch:</span>
                  <span>Civil Lines, Kanpur</span>
                </div>
              </div>

              <div class="qr-section">
                <h3 style="color: var(--primary-color); margin-bottom: 1rem">
                  <i class="fas fa-qrcode" style="margin-right: 10px"></i> Scan
                  & Pay (UPI)
                </h3>
                <div class="qr-code">
                  <!-- Placeholder for QR Image -->
                  <span>UPI QR Code Here</span>
                </div>
                <p style="font-size: 0.9rem; color: var(--text-light)">
                  GPay / PhonePe / Paytm
                </p>
                <p style="font-weight: 500; margin-top: 0.5rem">
                  UPI ID: dpjks@upi
                </p>
              </div>
            </div>
          </div>

          <!-- Right Column: Pledge/Contact Form -->
            <div class="donation-methods">
              <h3 style="margin-bottom: 1.5rem">
                Make a Donation
              </h3>
              <p style="margin-bottom: 1.5rem">
                Select an amount to support our social welfare programs.
              </p>
              
              <div class="donation-options" style="display: flex; gap: 10px; margin-bottom: 1.5rem;">
                <button type="button" class="btn btn-outline" onclick="selectAmount(500)" style="flex: 1; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">₹500</button>
                <button type="button" class="btn btn-outline" onclick="selectAmount(1000)" style="flex: 1; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">₹1000</button>
                <button type="button" class="btn btn-outline" onclick="selectAmount(2000)" style="flex: 1; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">₹2000</button>
              </div>

               <div class="payment-gateway-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #eef2f7; border-radius: 8px; border: 1px solid #d1d9e6;">
                <p style="font-weight: 600; margin-bottom: 0.5rem; color: #0d3b66;"><i class="fas fa-lock"></i> Secure Payment via Razorpay</p>
                <p style="font-size: 0.85rem; color: #555;">We use Razorpay to process donations securely. Access to your transaction details is confidential.</p>
              </div>

              <form onsubmit="event.preventDefault(); alert('Redirecting to Razorpay secure payment gateway...');">
                <div class="form-group">
                  <label class="form-label">Full Name</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="John Doe"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input
                    type="email"
                    class="form-control"
                    placeholder="john@example.com"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Phone Number</label>
                  <input
                    type="tel"
                    class="form-control"
                    placeholder="+91 93051 47080"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Amount (₹)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="donationAmount"
                    placeholder="Enter Amount"
                    required
                  />
                </div>
                <button
                  type="submit"
                  class="btn btn-primary"
                  style="width: 100%"
                >
                  Donate via Razorpay
                </button>
                 <p style="margin-top: 1rem; font-size: 0.8rem; color: #888; text-align: center;">
                    * As per applicable laws, donations are eligible for tax exemption.
                </p>
              </form>
              <script>
                  function selectAmount(amount) {
                      document.getElementById('donationAmount').value = amount;
                  }
              </script>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="js/main.js"></script>
  </body>
</html>
