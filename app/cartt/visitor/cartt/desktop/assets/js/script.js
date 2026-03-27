/* ============================================================
   CARTT — Homepage Scripts (Vanilla JS, no frameworks)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ── Navbar scroll effect ─────────────────────────────── */
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', function () {
    if (window.scrollY > 20) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
  });

  /* ── Mobile menu toggle ───────────────────────────────── */
  const menuBtn  = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  menuBtn && menuBtn.addEventListener('click', function () {
    mobileMenu.classList.toggle('hidden');
  });

  /* ── Pricing toggle ───────────────────────────────────── */
  const toggleYearly  = document.getElementById('toggle-yearly');
  const toggleMonthly = document.getElementById('toggle-monthly');
  const pill          = document.getElementById('toggle-pill');
  const yearlyPrices  = document.querySelectorAll('[data-yearly]');
  const monthlyPrices = document.querySelectorAll('[data-monthly]');
  const yearlyNote    = document.querySelectorAll('[data-yearly-note]');
  const monthlyNote   = document.querySelectorAll('[data-monthly-note]');
  let isYearly = true;

  function applyBilling() {
    if (isYearly) {
      pill.classList.remove('monthly');
      toggleYearly.classList.add('text-indigo-700', 'font-semibold');
      toggleMonthly.classList.remove('text-indigo-700', 'font-semibold');
      yearlyPrices.forEach(el => el.classList.remove('hidden'));
      monthlyPrices.forEach(el => el.classList.add('hidden'));
      yearlyNote.forEach(el => el.classList.remove('hidden'));
      monthlyNote.forEach(el => el.classList.add('hidden'));
    } else {
      pill.classList.add('monthly');
      toggleMonthly.classList.add('text-indigo-700', 'font-semibold');
      toggleYearly.classList.remove('text-indigo-700', 'font-semibold');
      monthlyPrices.forEach(el => el.classList.remove('hidden'));
      yearlyPrices.forEach(el => el.classList.add('hidden'));
      monthlyNote.forEach(el => el.classList.remove('hidden'));
      yearlyNote.forEach(el => el.classList.add('hidden'));
    }
  }

  toggleYearly  && toggleYearly.addEventListener('click', function () { isYearly = true; applyBilling(); });
  toggleMonthly && toggleMonthly.addEventListener('click', function () { isYearly = false; applyBilling(); });
  applyBilling();

  /* ── FAQ accordion ────────────────────────────────────── */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    item.querySelector('.faq-trigger').addEventListener('click', function () {
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(function (i) {
        i.classList.remove('open');
        i.querySelector('.faq-content').classList.remove('open');
      });
      if (!isOpen) {
        item.classList.add('open');
        item.querySelector('.faq-content').classList.add('open');
      }
    });
  });

  /* ── Scroll reveal ────────────────────────────────────── */
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });

  /* ── Smooth scroll for nav links ──────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        mobileMenu && mobileMenu.classList.add('hidden');
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ── Counter animation ────────────────────────────────── */
  const counters = document.querySelectorAll('[data-count]');
  const countObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        const el    = entry.target;
        const end   = parseInt(el.getAttribute('data-count'), 10);
        const dur   = 1800;
        const step  = Math.ceil(end / (dur / 16));
        let current = 0;
        const timer = setInterval(function () {
          current += step;
          if (current >= end) { current = end; clearInterval(timer); }
          el.textContent = current.toLocaleString('en-IN');
        }, 16);
        countObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(function (el) { countObserver.observe(el); });

});
