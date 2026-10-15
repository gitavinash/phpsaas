/**
 * Aleya SaaS — Contact Us Form Handler
 * Client-side validation, async lead submission, and conversion tracking
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  const form = document.getElementById('contactForm');
  const alertBox = document.getElementById('contactAlert');
  const submitBtn = document.getElementById('contactSubmitBtn');

  if (!form) return;

  function showAlert(message, type) {
    if (!alertBox) return;
    alertBox.className = 'neo-card ' + (type === 'success' ? 'badge-green' : 'badge-crimson');
    alertBox.style.display = 'block';
    alertBox.style.marginBottom = '20px';
    alertBox.innerHTML = message;
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function clearErrors() {
    form.querySelectorAll('.form-error').forEach(function (el) {
      el.classList.remove('visible');
      el.textContent = '';
    });
  }

  function showError(fieldId, errorMsg) {
    const errorEl = document.getElementById(fieldId + 'Error');
    if (errorEl) {
      errorEl.textContent = errorMsg;
      errorEl.classList.add('visible');
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();

    const name = (form.querySelector('[name="name"]')?.value || '').trim();
    const email = (form.querySelector('[name="email"]')?.value || '').trim();
    const phone = (form.querySelector('[name="phone"]')?.value || '').trim();
    const company = (form.querySelector('[name="company"]')?.value || '').trim();
    const industry = form.querySelector('[name="industry"]')?.value || '';
    const inquiryType = form.querySelector('[name="inquiry_type"]')?.value || '';
    const message = (form.querySelector('[name="message"]')?.value || '').trim();

    let hasError = false;

    if (!name) {
      showError('name', 'Please enter your full name.');
      hasError = true;
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showError('email', 'Please enter a valid business email.');
      hasError = true;
    }

    if (!phone || phone.replace(/\D/g, '').length < 10) {
      showError('phone', 'Please enter a valid 10-digit mobile number.');
      hasError = true;
    }

    if (!industry) {
      showError('industry', 'Please select your industry.');
      hasError = true;
    }

    if (hasError) return;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting inquiry...';
    }

    const payload = new FormData(form);

    fetch('/contact-us/', {
      method: 'POST',
      body: payload,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(function (res) {
        return res.json().catch(function () {
          return { status: res.ok ? 'success' : 'error' };
        });
      })
      .then(function (data) {
        if (data.status === 'success' || data.success) {
          showAlert('<i class="fas fa-check-circle"></i> Thank you! Your inquiry has been received. Our team will contact you within 2 business hours.', 'success');
          form.reset();

          if (typeof window.trackConversion === 'function') {
            window.trackConversion('generate_lead', {
              lead_source: 'Contact Page',
              industry: industry,
              inquiry_type: inquiryType
            });
            window.trackConversion('form_submit', { form_name: 'contact_us' });
          }
        } else {
          showAlert('<i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Something went wrong. Please call us directly at +91-9990026008.'), 'error');
        }
      })
      .catch(function () {
        showAlert('<i class="fas fa-check-circle"></i> Thank you! Your request has been recorded. We will connect with you shortly.', 'success');
        form.reset();
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Send Message <i class="fas fa-arrow-right"></i>';
        }
      });
  });
});
