<?php

require_once 'header.php';

?>

<!-- Simplified & Left-aligned Contact Hero Section -->
<section class="service-hero contact-hero-minimal reveal-on-load">
    <div class="container">
        <h1 class="hero-premium-headline" style="margin-bottom: 0">
            Contact <span class="hero-premium-hues">Us</span>
        </h1>
    </div>
</section>

<!-- Main Contact Content Section -->
<section style="padding: 80px 8% 120px; background-color: var(--white)">
    <div class="container reveal" style="
          max-width: 1400px;
          margin: 0 auto;
          display: flex;
          gap: 80px;
          flex-wrap: wrap;
        ">
        <!-- Left Sidebar Details (Info & Socials) -->
        <div style="
            flex: 1;
            min-width: 350px;
            display: flex;
            flex-direction: column;
            gap: 50px;
          ">
            <!-- Direct Contact Details -->
            <div>
                <h3 class="serif-normal">Direct Contact</h3>
                <p style="color: #666; margin-bottom: 1.5rem; font-size: 1.05rem">
                    Prefer reaching out directly? Here is how to easily find us.
                </p>
                <div style="display: flex; flex-direction: column; gap: 20px">
                    <a href="tel:+919910441788" style="
                  display: flex;
                  align-items: center;
                  gap: 20px;
                  color: #444;
                  text-decoration: none;
                  font-size: 1.15rem;
                  font-weight: 500;
                  transition: color 0.3s;
                " onmouseover="this.style.color = 'var(--primary-blue)'" onmouseout="this.style.color = '#444'">
                        <span style="
                    width: 50px;
                    height: 50px;
                    background: var(--hue-pink);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--primary-blue);
                    font-size: 1.2rem;
                    box-shadow: 0 10px 20px rgba(99, 125, 173, 0.1);
                  "><i class="fa-solid fa-phone"></i></span>
                        +91-9910441788
                    </a>
                    <a href="mailto:info@hueculture.in" style="
                  display: flex;
                  align-items: center;
                  gap: 20px;
                  color: #444;
                  text-decoration: none;
                  font-size: 1.15rem;
                  font-weight: 500;
                  transition: color 0.3s;
                " onmouseover="this.style.color = 'var(--primary-blue)'" onmouseout="this.style.color = '#444'">
                        <span style="
                    width: 50px;
                    height: 50px;
                    background: var(--hue-pink);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--primary-blue);
                    font-size: 1.2rem;
                    box-shadow: 0 10px 20px rgba(99, 125, 173, 0.1);
                  "><i class="fa-regular fa-envelope"></i></span>
                        info@hueculture.in
                    </a>
                </div>
            </div>

            <div style="height: 1px; background: #eee; width: 100%"></div>

            <!-- Follow/Socials Block -->
            <div>
                <h3 class="serif-normal">Follow Us</h3>
                <p style="
                color: #666;
                margin-bottom: 2rem;
                font-size: 1.05rem;
                line-height: 1.6;
              ">
                    Don't miss out on any updates. Follow us on social media to see
                    more of the vibrant color stories and inspiration behind Hue
                    Culture.
                </p>

                <div style="display: flex; gap: 20px">
                    <a href="#" aria-label="Instagram" style="
                  width: 55px;
                  height: 55px;
                  border-radius: 50%;
                  background: var(--charcoal);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: var(--white);
                  text-decoration: none;
                  transition:
                    transform 0.3s,
                    background-color 0.3s;
                  font-size: 1.3rem;
                " onmouseover="
                  this.style.transform = 'translateY(-5px)';
                  this.style.backgroundColor = 'var(--primary-blue)';
                " onmouseout="
                  this.style.transform = 'translateY(0)';
                  this.style.backgroundColor = 'var(--charcoal)';
                "><i class="fa-brands fa-instagram"></i></a>

                    <a href="#" aria-label="Facebook" style="
                  width: 55px;
                  height: 55px;
                  border-radius: 50%;
                  background: var(--charcoal);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: var(--white);
                  text-decoration: none;
                  transition:
                    transform 0.3s,
                    background-color 0.3s;
                  font-size: 1.3rem;
                " onmouseover="
                  this.style.transform = 'translateY(-5px)';
                  this.style.backgroundColor = 'var(--primary-blue)';
                " onmouseout="
                  this.style.transform = 'translateY(0)';
                  this.style.backgroundColor = 'var(--charcoal)';
                "><i class="fa-brands fa-facebook-f"></i></a>

                    <a href="#" aria-label="Pinterest" style="
                  width: 55px;
                  height: 55px;
                  border-radius: 50%;
                  background: var(--charcoal);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: var(--white);
                  text-decoration: none;
                  transition:
                    transform 0.3s,
                    background-color 0.3s;
                  font-size: 1.3rem;
                " onmouseover="
                  this.style.transform = 'translateY(-5px)';
                  this.style.backgroundColor = 'var(--primary-blue)';
                " onmouseout="
                  this.style.transform = 'translateY(0)';
                  this.style.backgroundColor = 'var(--charcoal)';
                "><i class="fa-brands fa-pinterest-p"></i></a>
                </div>
            </div>

            <div style="height: 1px; background: #eee; width: 100%"></div>
        </div>

        <!-- Right Page Details (Contact Form) -->
        <div style="flex: 1.2; min-width: 350px">
            <div style="
              background: var(--white);
              padding: 50px;
              border-radius: 30px;
              box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
              border: 1px solid rgba(0, 0, 0, 0.03);
            ">
                <h3 class="serif-normal">Get in Touch</h3>
                <p style="
                color: #666;
                margin-bottom: 2.5rem;
                font-size: 1.05rem;
                line-height: 1.6;
              ">
                    Your thoughts and messages are important to us. Use the form below
                    to send a direct message, and we'll get back to you as soon as
                    possible.
                </p>

                <form style="display: flex; flex-direction: column; gap: 20px">
                    <input type="text" placeholder="Your Name" style="
                  width: 100%;
                  padding: 18px 20px;
                  border: 1px solid #ddd;
                  border-radius: 12px;
                  font-family: var(--sans);
                  font-size: 1rem;
                  background: var(--charcoal-light);
                  transition: border 0.3s;
                " onfocus="this.style.borderColor = 'var(--primary-blue)'" onblur="this.style.borderColor = '#ddd'" />

                    <input type="email" placeholder="Your Email Address" style="
                  width: 100%;
                  padding: 18px 20px;
                  border: 1px solid #ddd;
                  border-radius: 12px;
                  font-family: var(--sans);
                  font-size: 1rem;
                  background: var(--charcoal-light);
                  transition: border 0.3s;
                " onfocus="this.style.borderColor = 'var(--primary-blue)'" onblur="this.style.borderColor = '#ddd'" />

                    <input type="text" placeholder="Subject" style="
                  width: 100%;
                  padding: 18px 20px;
                  border: 1px solid #ddd;
                  border-radius: 12px;
                  font-family: var(--sans);
                  font-size: 1rem;
                  background: var(--charcoal-light);
                  transition: border 0.3s;
                " onfocus="this.style.borderColor = 'var(--primary-blue)'" onblur="this.style.borderColor = '#ddd'" />

                    <textarea placeholder="Your Message" rows="6" style="
                  width: 100%;
                  padding: 18px 20px;
                  border: 1px solid #ddd;
                  border-radius: 12px;
                  font-family: var(--sans);
                  font-size: 1rem;
                  resize: vertical;
                  background: var(--charcoal-light);
                  transition: border 0.3s;
                " onfocus="this.style.borderColor = 'var(--primary-blue)'"
                        onblur="this.style.borderColor = '#ddd'"></textarea>

                    <button type="submit" class="btn-primary">Submit Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>