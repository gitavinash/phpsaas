<?php

require_once 'header.php';

?>

<!-- Product Overview Section -->
<section class="product-overview-section">
    <div class="container product-layout-grid reveal-on-load">
        <!-- Left: Gallery -->
        <div class="product-gallery">
            <div class="product-gallery-img-wrapper">
                <img src="neutrogena.png" class="product-gallery-main" alt="Neutrogena Hydro Boost Water Gel"
                    id="main-product-img" />
            </div>
            <div class="product-thumbnails">
                <img src="neutrogena.png" alt="Thumbnail 1" class="active" onclick="changeImage(this)" />
                <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=2070&auto=format&fit=crop"
                    alt="Thumbnail 2" onclick="changeImage(this)" />
                <img src="https://images.unsplash.com/photo-1610701596007-11502861dcfa?q=80&w=2070&auto=format&fit=crop"
                    alt="Thumbnail 3" onclick="changeImage(this)" />
            </div>
        </div>

        <!-- Right: Buying Options & Info -->
        <div class="product-info-col">
            <div class="product-breadcrumb">
                <a href="#">Store</a> / <a href="#">Skincare</a> / The Hue Palette
                Set
            </div>

            <h1 class="product-title serif-normal">
                Neutrogena Hydro Boost Water Gel
            </h1>
            <div class="product-price">₹1,050.00</div>

            <p class="product-desc">
                A scientifically curated set of hand-poured ceramic vases designed
                to harmonize any interior aesthetic. Each piece uses our proprietary
                matte finish to absorb indirect light and radiate soft, consistent
                hues regardless of the time of day.
            </p>

            <div class="buy-actions">
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="updateQuantity(-1)">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="text" class="quantity-input" id="qty-input" value="1" readonly />
                    <button class="quantity-btn" onclick="updateQuantity(1)">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

                <button class="btn-add-cart">
                    <i class="fa-solid fa-bag-shopping"></i> Buy Now
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Product Details Deep Dive -->
<section class="product-deep-dive">
    <div class="container reveal">
        <h2 class="serif-normal" style="font-size: 2.5rem; color: var(--charcoal); margin-bottom: 20px">
            The Science of Hydration
        </h2>
        <p style="
            font-size: 1.15rem;
            color: #555;
            max-width: 800px;
            line-height: 1.8;
          ">
            This set was engineered with natural light frequencies in mind. The
            varied heights allow for cascading shadow patterns, while the dense
            ceramic builds act as a physical anchor in highly illuminated spaces.
        </p>

        <div class="features-grid">
            <div class="feature-item">
                <i class="fa-solid fa-droplet feature-icon"></i>
                <div class="feature-text">
                    <h4>Hyaluronic Acid Base</h4>
                    <p>
                        Specialized glazing technique prevents harsh glare, softening
                        the color output.
                    </p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-fingerprint feature-icon"></i>
                <div class="feature-text">
                    <h4>100% Fragrance Free</h4>
                    <p>
                        Each set is poured by artisan hands ensuring no two silhouettes
                        are exactly identical.
                    </p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-ruler-combined feature-icon"></i>
                <div class="feature-text">
                    <h4>Non-Comedogenic</h4>
                    <p>
                        Tuned to the golden ratio for immediate visual ease when grouped
                        together.
                    </p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-boxes-packing feature-icon"></i>
                <div class="feature-text">
                    <h4>Dermatologist Recommended</h4>
                    <p>
                        Built with locally extracted clays and shipped in 100%
                        biodegradable casing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="related-products-section">
    <div class="container reveal">
        <h2 class="serif-normal related-products-title">Complete the Look</h2>

        <div class="related-blog-grid">
            <a href="product-details.html" class="product-card">
                <img src="https://images.unsplash.com/photo-1544457070-4cd773b4d71e?q=80&w=2130&auto=format&fit=crop"
                    class="product-card-img" alt="Linen Throw" />
                <div class="product-card-info">
                    <span class="product-card-tag">Textiles</span>
                    <h3 class="product-card-title serif-normal">Raw Linen Throw</h3>
                    <div class="product-card-price">₹120.00</div>
                </div>
            </a>

            <a href="product-details.html" class="product-card">
                <img src="https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?q=80&w=1976&auto=format&fit=crop"
                    class="product-card-img" alt="Ceramic Mug Base" />
                <div class="product-card-info">
                    <span class="product-card-tag">Tableware</span>
                    <h3 class="product-card-title serif-normal">Ceramic Base Mug</h3>
                    <div class="product-card-price">₹35.00</div>
                </div>
            </a>

            <a href="product-details.html" class="product-card">
                <img src="https://images.unsplash.com/photo-1610701596007-11502861dcfa?q=80&w=2070&auto=format&fit=crop"
                    class="product-card-img" alt="Ambient Lamp" />
                <div class="product-card-info">
                    <span class="product-card-tag">Lighting</span>
                    <h3 class="product-card-title serif-normal">Ambient Hue Lamp</h3>
                    <div class="product-card-price">₹210.00</div>
                </div>
            </a>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>