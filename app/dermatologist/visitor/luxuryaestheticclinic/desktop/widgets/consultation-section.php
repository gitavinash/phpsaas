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
                    <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='currentColor' style='margin-right:12px;flex-shrink:0'><path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z'/></svg>
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
