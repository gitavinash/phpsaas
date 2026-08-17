<?php

if (!function_exists('renderShippingErrorPage')) {
    function renderShippingErrorPage($title = "Checkout Error", $message = "Something went wrong while processing your order request.", $errorCode = "CHECKOUT_ERROR", $debugInfo = "")
    {
        require_once 'header.php';
?>
        <section class="checkout-box checkout-error-page">
            <div class="container">
                <div class="checkout-error-card">
                    <div class="error-card-banner">
                        <div class="error-icon-ring">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <span class="error-code-badge"><?php echo htmlspecialchars(str_replace('_', ' ', $errorCode)); ?></span>
                        <h1 class="error-card-title"><?php echo htmlspecialchars($title); ?></h1>
                        <p class="error-card-desc"><?php echo htmlspecialchars($message); ?></p>
                    </div>

                    <div class="error-card-content">
                        <div class="error-tips-box">
                            <strong>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Helpful Suggestions
                            </strong>
                            <ul>
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Verify your order link or return to the home page to restart checkout.
                                </li>
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    If purchasing a subscription, check if your plan is already active.
                                </li>
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Contact our support team if you believe this is a system issue.
                                </li>
                            </ul>
                        </div>

                        <?php if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true && !empty($debugInfo)): ?>
                            <details class="error-debug-details">
                                <summary>Technical Diagnostics (Dev Mode)</summary>
                                <pre class="debug-code"><?php echo htmlspecialchars($debugInfo); ?></pre>
                            </details>
                        <?php endif; ?>

                        <div class="error-btn-group">
                            <a href="/" class="button btn-error-home">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                <span>Return to Home</span>
                            </a>
                            <a href="/contact-us/" class="btn-error-support">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span>Contact Support</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="creditBox">
                    <div class="container">
                        <p>By continuing you agree to our <a href="/terms-and-conditions">Terms &amp; Conditions</a> and <a href="/privacy-policy/">Privacy Policy</a></p>
                        <p class="powered-by">Powered by <a href="https://blumox.com" target="_blank" title="Build online e-commerce store with Cartt">Blumox Technologies</a></p>
                    </div>
                </div>
            </div>
        </section>
<?php
        require_once 'footer.php';
        exit();
    }
}

if (!isset($id)) {
    renderShippingErrorPage(
        "Missing Order Identifier",
        "No order ID was provided in your request. Please return to your cart and restart checkout.",
        "MISSING_ORDER_ID"
    );
}

if (SITE_ID == 49) {
    require_once LOGIN_CHECK;

    $userSaltKey = base64_decode($_COOKIE['user_data']);

    if ($checkUser = mysqli_query($connection, "SELECT subscription_status, subscription_relation_id, subscription_type, subscription_mode, subscription_time, subscription_expiry FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND CURDATE() <= subscription_expiry AND subscription_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
            if ($subscription_status == 1) {
                $redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');
                header("Location: {$redirect_url}", TRUE, 301);
                exit();
            }
        }
    }
}

$orderId = base64_decode($id);

if ($checkDetails = mysqli_query($connection, "SELECT order_cart_session_id, order_total_price, order_currency, cart_relation_type FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id WHERE order_id='{$orderId}' AND order_status=0 AND order_site_id=" . SITE_ID . " ORDER BY cart_updated DESC")) {
    if (mysqli_num_rows($checkDetails) != 0) {
        extract(mysqli_fetch_assoc($checkDetails));

        switch ($cart_relation_type) {
            case 'news':
                $cartQuery = "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "{$cart_relation_type} ON cart_relation_id= {$cart_relation_type}_id WHERE cart_relation_type='{$cart_relation_type}' AND cart_session_id='{$order_cart_session_id}' AND cart_site_id=" . SITE_ID . " ORDER BY cart_updated DESC";
                break;
            default:
                $cartQuery = "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "{$cart_relation_type} ON cart_relation_id= {$cart_relation_type}_id INNER JOIN " . TABLE_PREFIX . "inventory ON {$cart_relation_type}_id =inventory_relation_id WHERE cart_relation_type='{$cart_relation_type}' AND cart_session_id='{$order_cart_session_id}' AND inventory_type='{$cart_relation_type}' AND cart_site_id=" . SITE_ID . " ORDER BY cart_updated DESC";
                break;
        }

        if ($cartInfo = mysqli_query($connection, $cartQuery)) {
            if (mysqli_num_rows($cartInfo) == 0) {
                renderShippingErrorPage(
                    "Your Shipping Cart is Empty",
                    "There are no active items found in your shipping cart session.",
                    "EMPTY_CART"
                );
            } else {
                $inventory_currency_symbol = ((isset($inventory_currency_symbol)) ? $inventory_currency_symbol : '₹');
                $inventory_price = ((isset($inventory_price)) ? $inventory_price : $order_total_price);
                $inventory_tax_status = ((isset($inventory_tax_status)) ? $inventory_tax_status : 0);
            }
        } else {
            renderShippingErrorPage(
                "Cart Data Fetch Failed",
                "Something went wrong while trying to fetch cart items from the database.",
                "CART_FETCH_ERROR"
            );
        }
    } else {
        $devSql = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) ? "Query: SELECT order_cart_session_id, order_total_price, order_currency, cart_relation_type FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id WHERE order_id='{$orderId}' AND order_status=0 AND order_site_id=" . SITE_ID . " ORDER BY cart_updated DESC" : "";
        renderShippingErrorPage(
            "Order Session Expired or Not Found",
            "This checkout link may have expired or the order has already been completed.",
            "ORDER_NOT_FOUND",
            $devSql
        );
    }
} else {
    renderShippingErrorPage(
        "Database System Error",
        "Something went wrong while attempting to retrieve your order details.",
        "DATABASE_ERROR"
    );
}

require_once 'header.php';

?>
<section class="checkout-box">
    <div class="container">
        <!-- Progress Stepper -->
        <div class="checkout-stepper">
            <div class="step-item completed">
                <span class="step-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span class="step-text">Cart</span>
            </div>
            <div class="step-line active"></div>
            <div class="step-item active">
                <span class="step-number">2</span>
                <span class="step-text">Shipping &amp; Review</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <span class="step-number">3</span>
                <span class="step-text">Payment</span>
            </div>
        </div>

        <div class="checkout-layout">
            <div class="checkout-main">
                <!-- Dynamic / Session Error Information Banner Container -->
                <div id="checkoutErrorContainer">
                    <?php
                    $checkoutErrorMsg = isset($_GET['error']) ? trim($_GET['error']) : (isset($errorMsg) ? $errorMsg : '');
                    if (!empty($checkoutErrorMsg)):
                    ?>
                        <div class="checkout-alert-banner alert-danger" role="alert">
                            <div class="alert-icon-ring">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="alert-content">
                                <strong class="alert-heading">Notice</strong>
                                <span class="alert-message"><?php echo htmlspecialchars($checkoutErrorMsg); ?></span>
                            </div>
                            <button type="button" class="alert-dismiss-btn" onclick="this.closest('.checkout-alert-banner').remove();" aria-label="Close Notice">&times;</button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php

                $checkAppFile = DEFAULT_USER_PATH . ltrim(BASE_URL, '/') . SITE_APP . '.php';
                if (file_exists($checkAppFile)) {
                    require_once $checkAppFile;
                } else {
                    $fallbackAppFile = DEFAULT_USER_PATH . ltrim(BASE_URL, '/') . 'default.php';
                    if (file_exists($fallbackAppFile)) {
                        require_once $fallbackAppFile;
                    }
                }

                ?>
                <div class="box white-bg guarantee-card">
                    <div class="guarantee-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <div>
                            <strong>100% Guaranteed Secure</strong>
                            <span>Encrypted transactions and data privacy</span>
                        </div>
                    </div>
                    <div class="guarantee-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                        </svg>
                        <div>
                            <strong>Instant Order Processing</strong>
                            <span>Quick &amp; seamless digital checkout</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="checkout-sidebar">
                <div class="shadow-box summary-card">
                    <div class="summary-header" onclick="toggleMobileSummary();">
                        <h2>
                            <svg class="header-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            <span>Payment summary</span>
                        </h2>
                        <button type="button" class="mobile-summary-toggle-btn" aria-label="Toggle Summary Breakdown">
                            <span id="summaryToggleText">Details</span>
                            <svg class="toggle-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                    <?php
                    $subTotal = isset($subTotal) ? (float)$subTotal : 0;
                    $taxAmount = 0;
                    if (isset($inventory_tax_status) && $inventory_tax_status == 1 && isset($inventory_tax_rate)) {
                        $taxAmount = (float)number_format(($inventory_tax_rate / 100) * $subTotal, 2, '.', '');
                    }
                    $totalPrice = $subTotal + $taxAmount;
                    $currencySymbol = isset($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';
                    ?>
                    <ul class="summary-list">
                        <li class="summary-item">
                            <p><strong>Subtotal</strong><span><?php echo $currencySymbol . ' ' . number_format($subTotal, 2); ?></span></p>
                        </li>
                        <?php if (isset($inventory_tax_status) && $inventory_tax_status == 1 && isset($inventory_tax_rate)): ?>
                            <li class="summary-item">
                                <p><strong>Tax (<?php echo $inventory_tax_rate; ?>%)</strong><span><?php echo $currencySymbol . ' ' . number_format($taxAmount, 2); ?></span></p>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="totalBox">
                        <p><strong>Total Payable</strong>
                            <span><?php echo $currencySymbol . ' ' . number_format($totalPrice, 2); ?></span>
                        </p>
                    </div>
                    <input type="hidden" name="order" id="orderId" value="<?php echo trim($orderId); ?>">
                    <input type="hidden" name="app" id="app" value="<?php echo trim(SITE_APP); ?>">
                    <input type="hidden" name="orderType" id="orderType" value="<?php echo isset($order) ? trim($order) : 'default'; ?>">
                    <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                    <input type="hidden" name="paymentMode" id="paymentMode" value="online">

                    <a href="#" id="processToPayment" class="button btn-payment">
                        <span>Proceed to payment</span>
                        <svg class="btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>

                    <div class="payment-methods-pills">
                        <span class="pay-pill pay-pill-upi">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo-vector.svg/3840px-UPI-Logo-vector.svg.png?utm_source=commons.wikimedia.org&amp;utm_campaign=index&amp;utm_content=thumbnail" alt="UPI" class="pay-pill-img">
                            <span>UPI</span>
                        </span>
                        <span class="pay-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            <span>Cards</span>
                        </span>
                        <span class="pay-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"></path>
                            </svg>
                            <span>NetBanking</span>
                        </span>
                        <span class="pay-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
                                <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
                                <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
                            </svg>
                            <span>Wallets</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Fixed Bottom Action Bar -->
    <div class="mobile-bottom-bar">
        <div class="mobile-price-summary">
            <div class="mobile-price-row">
                <span class="mobile-total-label">Total Amount</span>
                <button type="button" class="mobile-view-details-link" onclick="openMobileDrawer(); return false;">
                    <span>View details</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                </button>
            </div>
            <span class="mobile-total-value"><?php echo $currencySymbol . ' ' . number_format($totalPrice, 2); ?></span>
        </div>
        <a href="#" class="button btn-payment mobile-payment-btn" onclick="document.getElementById('processToPayment').click(); return false;">
            <span>Proceed</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>

    <!-- Mobile Payment Summary Bottom Sheet Drawer -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay" onclick="closeMobileDrawer();"></div>
    <div class="mobile-summary-drawer" id="mobileSummaryDrawer">
        <div class="drawer-header">
            <h3>
                <svg class="header-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                <span>Payment Summary</span>
            </h3>
            <button type="button" class="drawer-close-btn" onclick="closeMobileDrawer();" aria-label="Close Summary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="drawer-body">
            <ul class="summary-list">
                <li class="summary-item">
                    <p><strong>Subtotal</strong><span><?php echo $currencySymbol . ' ' . number_format($subTotal, 2); ?></span></p>
                </li>
                <?php if (isset($inventory_tax_status) && $inventory_tax_status == 1 && isset($inventory_tax_rate)): ?>
                    <li class="summary-item">
                        <p><strong>Tax (<?php echo $inventory_tax_rate; ?>%)</strong><span><?php echo $currencySymbol . ' ' . number_format($taxAmount, 2); ?></span></p>
                    </li>
                <?php endif; ?>
                <li class="summary-item highlight-delivery">
                    <p><strong>Delivery</strong><span class="free-badge">Included</span></p>
                </li>
            </ul>
            <div class="totalBox">
                <p><strong>Total Payable</strong>
                    <span><?php echo $currencySymbol . ' ' . number_format($totalPrice, 2); ?></span>
                </p>
            </div>
            <a href="#" class="button btn-payment drawer-payment-btn" onclick="closeMobileDrawer(); document.getElementById('processToPayment').click(); return false;">
                <span>Proceed to payment</span>
                <svg class="btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <div class="payment-methods-pills">
                <span class="pay-pill pay-pill-upi">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo-vector.svg/3840px-UPI-Logo-vector.svg.png?utm_source=commons.wikimedia.org&amp;utm_campaign=index&amp;utm_content=thumbnail" alt="UPI" class="pay-pill-img">
                    <span>UPI</span>
                </span>
                <span class="pay-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <span>Cards</span>
                </span>
                <span class="pay-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"></path>
                    </svg>
                    <span>NetBanking</span>
                </span>
                <span class="pay-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
                        <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
                        <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
                    </svg>
                    <span>Wallets</span>
                </span>
            </div>
            <div class="trust-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>256-Bit SSL Encrypted &amp; Secure</span>
            </div>
        </div>
    </div>

    <script>
        function openMobileDrawer() {
            var drawer = document.getElementById('mobileSummaryDrawer');
            var overlay = document.getElementById('mobileDrawerOverlay');
            if (drawer && overlay) {
                drawer.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileDrawer() {
            var drawer = document.getElementById('mobileSummaryDrawer');
            var overlay = document.getElementById('mobileDrawerOverlay');
            if (drawer && overlay) {
                drawer.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function showCheckoutError(msg) {
            var container = document.getElementById('checkoutErrorContainer');
            if (!container) return;
            var html = '<div class="checkout-alert-banner alert-danger animated-shake" role="alert">' +
                '<div class="alert-icon-ring">' +
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>' +
                '</svg>' +
                '</div>' +
                '<div class="alert-content">' +
                '<strong class="alert-heading">Action Required</strong>' +
                '<span class="alert-message">' + (msg || 'Payment processing failed. Please try again or choose another payment method.') + '</span>' +
                '</div>' +
                '<button type="button" class="alert-dismiss-btn" onclick="this.closest(\'.checkout-alert-banner\').remove();" aria-label="Close Notice">&times;</button>' +
                '</div>';
            container.innerHTML = html;
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    </script>

    <div class="creditBox">
        <div class="container">
            <p>By continuing you agree to our <a href="/terms-and-conditions">Terms &amp; Conditions</a> and <a href="/privacy-policy/">Privacy Policy</a></p>
            <p class="powered-by">Powered by <a href="https://blumox.com" target="_blank" title="Build online e-commerce store with Cartt">Blumox Technologies</a></p>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';
