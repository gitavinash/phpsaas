<?php

if ($checkConsultationWidget = mysqli_query($connection, "SELECT category_description FROM " . TABLE_PREFIX . "category WHERE category_title LIKE '%Book Your Free Consultation%' AND category_status = '1' AND category_site_id =" . SITE_ID)) {

    if (mysqli_num_rows($checkConsultationWidget) != 0) {
        extract(mysqli_fetch_assoc($checkConsultationWidget));
        echo $category_description;
    } else {
        echo "<section class='consultation-section' id='consultation'>
    <div class='container consult-grid'>
        <!-- Left Content Column -->
        <div class='consult-info reveal'>
            <!-- <span class='section-tag'>Elevate Your Self Care</span> -->
            <h2>Book Your Free Consultation</h2>
            <p class='consult-info-desc'>
                Experience the best aesthetic care in South Delhi. Book your free consultation today — call us or send a WhatsApp message.
            </p>

            <ul class='consult-trust-points'>
                <li class='consult-trust-item'>
                    <span class='consult-trust-icon-box'>
                        <i data-lucide='lock'></i>
                    </span>
                    <div class='consult-trust-content'>
                        <span class='consult-trust-label'>100% Confidential</span>
                        <span class='consult-trust-tagline'>Botox in Delhi</span>
                    </div>
                </li>
                <li class='consult-trust-item'>
                    <span class='consult-trust-icon-box'>
                        <i data-lucide='heart-handshake'></i>
                    </span>
                    <div class='consult-trust-content'>
                        <span class='consult-trust-label'>Zero Hard-sell</span>
                        <span class='consult-trust-tagline'>HydraFacial South Delhi</span>
                    </div>
                </li>
                <li class='consult-trust-item'>
                    <span class='consult-trust-icon-box'>
                        <i data-lucide='sparkles'></i>
                    </span>
                    <div class='consult-trust-content'>
                        <span class='consult-trust-label'>Clinically Safe</span>
                        <span class='consult-trust-tagline'>Lip Filler in Delhi</span>
                    </div>
                </li>
                <li class='consult-trust-item'>
                    <span class='consult-trust-icon-box'>
                        <i data-lucide='droplet'></i>
                    </span>
                    <div class='consult-trust-content'>
                        <span class='consult-trust-label'>Proven Hair Therapy</span>
                        <span class='consult-trust-tagline'>GFC Hair Treatment Delhi</span>
                    </div>
                </li>
                <li class='consult-trust-item'>
                    <span class='consult-trust-icon-box'>
                        <i data-lucide='shield-check'></i>
                    </span>
                    <div class='consult-trust-content'>
                        <span class='consult-trust-label'>FDA-Approved Safety</span>
                        <span class='consult-trust-tagline'>Dermal Fillers South Delhi</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Right Form Card -->
        <div class='consult-form-card reveal'>
            <h3 class='form-title-editorial'>Schedule Consultation</h3>

            <form id='consultationForm'>
                <!-- Name & Phone -->
                <div class='form-group-row'>
                    <div class='form-group'>
                        <input
                            type='text'
                            id='formName'
                            class='form-input'
                            placeholder='Full name'
                            name='full_name'
                            required />
                        <label for='formName' class='form-label'>Full Name *</label>
                    </div>
                    <div class='form-group'>
                        <input
                            type='tel'
                            id='formPhone'
                            class='form-input'
                            placeholder='Mobile'
                            name='mobile'
                            required />
                        <label for='formPhone' class='form-label'>Phone Number *</label>
                    </div>
                </div>

                <!-- Email & Concern -->
                <div class='form-group-row'>
                    <div class='form-group'>
                        <input
                            type='email'
                            id='formEmail'
                            class='form-input'
                            placeholder='Email'
                            name='email' />
                        <label for='formEmail' class='form-label'>Email Address</label>
                    </div>
                    <div class='form-group'>
                        <select id='formConcern' class='form-select' required name='concern'>
                            <option value='' disabled selected hidden>Select Your Concern</option>
                            <option value='Skin Pigmentation'>
                                Skin Pigmentation / Glow
                            </option>
                            <option value='Acne Scars'>Acne Scars / Texture</option>
                            <option value='Anti-Aging'>Anti-Aging / Wrinkles</option>
                            <option value='Hair Restoration'>Hair Fall / Thinning</option>
                            <option value='Body Contouring'>
                                Fat Reduction / Contouring
                            </option>
                        </select>
                        <label for='formConcern' class='form-label'>Primary Concern *</label>
                    </div>
                </div>

                <!-- Message -->
                <div class='form-group'>
                    <textarea
                        id='formMessage'
                        class='form-textarea'
                        placeholder='Message' name='message'></textarea>
                    <label for='formMessage' class='form-label'>Brief Message (Optional)</label>
                </div>

                <button
                    type='submit'
                    class='btn btn-primary form-btn-submit'
                    id='formSubmit'>
                    Confirm Booking
                </button>

                <a
                    href='https://wa.me/919999999999'
                    class='btn btn-whatsapp form-btn-whatsapp'
                    target='_blank'
                    rel='noopener'>
                    <i
                        data-lucide='message-square'
                        style='width: 16px; margin-right: 8px'></i>
                    WhatsApp Consultation
                </a>

                <p class='form-privacy-note'>
                    Your data is strictly secured. We never share contact details.
                </p>
            </form>
        </div>
    </div>
</section>";
    }
}
