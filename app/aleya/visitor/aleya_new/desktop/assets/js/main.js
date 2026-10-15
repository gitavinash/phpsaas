/**
 * Aleya SaaS — Main Application Scripts
 * Mobile navigation, accordions, and Google Ads / Analytics conversion tracking
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // --- Mobile Navigation Drawer ---
  const mobileToggle = document.getElementById('mobileNavToggle');
  const mobileDrawer = document.getElementById('mobileNavDrawer');

  if (mobileToggle && mobileDrawer) {
    mobileToggle.addEventListener('click', function () {
      const isOpen = mobileDrawer.classList.toggle('open');
      mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      mobileToggle.innerHTML = isOpen 
        ? '<i class="fas fa-times" aria-hidden="true"></i>' 
        : '<i class="fas fa-bars" aria-hidden="true"></i>';
    });

    // Close drawer when clicking any link
    const mobileLinks = mobileDrawer.querySelectorAll('a');
    mobileLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        mobileDrawer.classList.remove('open');
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.innerHTML = '<i class="fas fa-bars" aria-hidden="true"></i>';
      });
    });
  }

  // --- Accessible Accordion FAQs ---
  const accordionHeaders = document.querySelectorAll('.accordion-header');
  accordionHeaders.forEach(function (header) {
    header.addEventListener('click', function () {
      const item = header.closest('.accordion-item');
      if (!item) return;

      const isOpen = item.classList.contains('active');

      // Close other accordion items in the same container
      const parent = item.parentElement;
      if (parent) {
        parent.querySelectorAll('.accordion-item').forEach(function (sibling) {
          if (sibling !== item) {
            sibling.classList.remove('active');
            const sibHeader = sibling.querySelector('.accordion-header');
            if (sibHeader) sibHeader.setAttribute('aria-expanded', 'false');
          }
        });
      }

      if (isOpen) {
        item.classList.remove('active');
        header.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('active');
        header.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // --- Google Ads & Analytics Unified Conversion Dispatcher ---
  window.trackConversion = function (eventName, eventParams) {
    eventParams = eventParams || {};

    // Google Tag Manager / GA4 DataLayer
    if (window.dataLayer && Array.isArray(window.dataLayer)) {
      window.dataLayer.push({
        event: eventName,
        ...eventParams
      });
    }

    // Google Ads / gtag
    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, eventParams);
    }
  };

  // Track WhatsApp Clicks across all pages
  const whatsappTriggers = document.querySelectorAll('a[href*="wa.me"], .whatsapp-float, [data-track="whatsapp"]');
  whatsappTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      window.trackConversion('click_whatsapp', {
        channel: 'WhatsApp',
        source_url: window.location.pathname
      });
    });
  });

  // Track Demo Requests
  const demoTriggers = document.querySelectorAll('a[href*="calendar.app.google"], [data-track="demo"]');
  demoTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      window.trackConversion('demo_request', {
        source_url: window.location.pathname
      });
    });
  });
});
