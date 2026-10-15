<?php
/**
 * Aleya SaaS — Global Header & Navigation Component
 */
$currentUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<header class="site-header" id="siteHeader">
    <div class="container header-container">
        <!-- Brand Logo -->
        <a href="/" class="header-brand" aria-label="Aleya Homepage">
            <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_logo.png" alt="Aleya SaaS" class="header-logo" width="140" height="42" />
        </a>

        <!-- Desktop Navigation -->
        <nav class="nav-links-desktop" aria-label="Main Navigation">
            <div class="nav-item nav-item-dropdown">
                <a href="/#industries" class="nav-link" aria-haspopup="true">
                    Industries <i class="fas fa-chevron-down icon-sm"></i>
                </a>
                <div class="dropdown-menu" role="menu">
                    <a href="/industries/travel-agencies/" class="dropdown-link" role="menuitem">
                        <div class="dropdown-icon"><i class="fas fa-plane-departure"></i></div>
                        <div>
                            <span class="dropdown-link-title">Travel Agencies</span>
                            <span class="dropdown-link-desc">Itineraries, vouchers, DMC suppliers & team pipeline</span>
                        </div>
                    </a>
                    <a href="/industries/travel-agents/" class="dropdown-link" role="menuitem">
                        <div class="dropdown-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <span class="dropdown-link-title">Travel Agents</span>
                            <span class="dropdown-link-desc">Lightweight CRM, fast quotes & WhatsApp follow-ups</span>
                        </div>
                    </a>
                    <a href="/industries/transporters/" class="dropdown-link" role="menuitem">
                        <div class="dropdown-icon"><i class="fas fa-truck-moving"></i></div>
                        <div>
                            <span class="dropdown-link-title">Transporters</span>
                            <span class="dropdown-link-desc">Vehicle rosters, trip costing, photo quotes & duty slips</span>
                        </div>
                    </a>
                    <a href="/industries/dms/" class="dropdown-link" role="menuitem">
                        <div class="dropdown-icon"><i class="fas fa-warehouse"></i></div>
                        <div>
                            <span class="dropdown-link-title">DMS (Distribution)</span>
                            <span class="dropdown-link-desc">Multi-warehouse inventory, dealer orders & dispatches</span>
                        </div>
                    </a>
                    <a href="/industries/spa-management/" class="dropdown-link" role="menuitem">
                        <div class="dropdown-icon"><i class="fas fa-spa"></i></div>
                        <div>
                            <span class="dropdown-link-title">SPA Management</span>
                            <span class="dropdown-link-desc">Therapist roster, room slots, packages & zero-no-shows</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="nav-item">
                <a href="/#features" class="nav-link <?php echo $currentUrl === '/#features' ? 'active' : ''; ?>">Features</a>
            </div>

            <div class="nav-item">
                <a href="/pricing/" class="nav-link <?php echo $currentUrl === '/pricing/' ? 'active' : ''; ?>">Pricing</a>
            </div>

            <div class="nav-item">
                <a href="/about-us/" class="nav-link <?php echo $currentUrl === '/about-us/' ? 'active' : ''; ?>">About Us</a>
            </div>

            <div class="nav-item">
                <a href="/contact-us/" class="nav-link <?php echo $currentUrl === '/contact-us/' ? 'active' : ''; ?>">Contact Us</a>
            </div>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
            <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" data-track="demo">
                <i class="far fa-calendar-check"></i> Book Demo
            </a>
            <a href="/checkout/" class="btn btn-primary btn-sm">
                Get Started <i class="fas fa-arrow-right"></i>
            </a>

            <!-- Mobile Menu Toggle Button -->
            <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Navigation Menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Slide-over Drawer -->
    <div class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
        <div class="mobile-nav-heading">Industry Solutions</div>
        <div class="mobile-nav-links">
            <a href="/industries/travel-agencies/" class="nav-link"><i class="fas fa-plane-departure text-brand"></i> Travel Agencies</a>
            <a href="/industries/travel-agents/" class="nav-link"><i class="fas fa-user-tie text-brand"></i> Travel Agents</a>
            <a href="/industries/transporters/" class="nav-link"><i class="fas fa-truck-moving text-brand"></i> Transporters & Fleet</a>
            <a href="/industries/dms/" class="nav-link"><i class="fas fa-warehouse text-brand"></i> DMS (Distribution)</a>
            <a href="/industries/spa-management/" class="nav-link"><i class="fas fa-spa text-brand"></i> Spa Management</a>
        </div>

        <div class="mobile-nav-heading mt-4">Core Pages</div>
        <div class="mobile-nav-links">
            <a href="/#features" class="nav-link"><i class="fas fa-layer-group"></i> Features</a>
            <a href="/pricing/" class="nav-link"><i class="fas fa-tag"></i> Pricing Plans</a>
            <a href="/about-us/" class="nav-link"><i class="fas fa-building"></i> About Us</a>
            <a href="/contact-us/" class="nav-link"><i class="fas fa-envelope"></i> Contact Us</a>
        </div>

        <div class="mt-6 d-flex flex-column gap-2">
            <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-block">
                <i class="far fa-calendar-check"></i> Book a Demo
            </a>
            <a href="/checkout/" class="btn btn-primary btn-block">
                Buy Subscription <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</header>
