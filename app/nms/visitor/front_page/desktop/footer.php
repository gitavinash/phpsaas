</main>

<!-- Footer -->
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img
        src="<?php echo SITE_LOGO ?>"
        alt="The Head &amp; Tale"
        width="160"
        height="38"
        class="logo-img" />
      <p>Decoding the noise in Fintech, AI, and the Startup Ecosystem.</p>

      <!-- Vector SVG Social Media Icons -->
      <div class="social-links">
        <a href="https://twitter.com/theheadandtale" target="_blank" rel="noopener" aria-label="X / Twitter" class="social-icon-btn">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
          </svg>
        </a>
        <a href="https://linkedin.com/company/theheadandtale" target="_blank" rel="noopener" aria-label="LinkedIn" class="social-icon-btn">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.327-.027-3.037-1.849-3.037-1.851 0-2.132 1.445-2.132 2.939v5.667H9.358V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.368 4.267 5.455v6.285zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM7.119 20.452H3.554V9h3.565v11.452zM22.225 0H1.771C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.771 24h20.451C23.2 24 24 23.229 24 22.271V1.723C24 .771 23.2 0 22.225 0z" />
          </svg>
        </a>
        <a href="https://instagram.com/theheadandtale" target="_blank" rel="noopener" aria-label="Instagram" class="social-icon-btn">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
          </svg>
        </a>
        <a href="https://facebook.com/theheadandtale" target="_blank" rel="noopener" aria-label="Facebook" class="social-icon-btn">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
          </svg>
        </a>
      </div>
    </div>

    <div class="footer-links">
      <h4>Company</h4>
      <ul>
        <?php generateMenu('SITE_NAV_FOOTER_1', 'mobile', true, 'nav-link'); ?>
      </ul>
    </div>

    <div class="footer-links">
      <h4>Quick Links</h4>
      <ul>
        <?php generateMenu('SITE_NAV_FOOTER_2', 'mobile', true, 'nav-link'); ?>
      </ul>
    </div>

    <div class="footer-links">
      <h4>Membership</h4>
      <ul>
        <?php generateMenu('SITE_NAV_FOOTER_3', 'mobile', true, 'nav-link'); ?>
      </ul>
    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; 2026 The Head and Tale. All rights reserved.</p>
    <p>Designed by Blumox Technologies</p>
  </div>
</footer>

<script src="<?php echo USER_JS; ?>script.js?v=<?php echo filemtime(__DIR__ . '/assets/js/script.js'); ?>"></script>
</body>

</html>