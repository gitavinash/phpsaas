<?php
require_once DATA_FUNCTIONS;
require_once 'header.php';

// Fetch testimonials data
$testimonials = testimonialsList($connection, 1000);
$totalCount = !empty($testimonials) ? count($testimonials) : 0;

// Helper function to clean quote content
if (!function_exists('clean_quote')) {
    function clean_quote($s)
    {
        $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
        $s = strip_tags($s);                                // remove inline styles/tags
        $s = preg_replace('/\xC2\xA0|&nbsp;?/i', ' ', $s); // remove nbsp
        $s = preg_replace('/\s{2,}/', ' ', $s);            // extra spaces
        return trim($s);
    }
}
?>

<!-- Scoped Custom Theme Styles for Who Reads Us Page -->
<style>
    /* Hero Section Center Alignment */
    .category-header-hero {
        text-align: center;
    }

    .category-header-hero .article-breadcrumb {
        justify-content: center;
    }

    .category-hero-content {
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .category-hero-desc {
        margin-left: auto;
        margin-right: auto;
    }

    .wru-hero-metrics {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 36px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        flex-wrap: wrap;
    }

    .wru-metric-item {
        display: flex;
        flex-direction: column;
    }

    .wru-metric-num {
        font-family: var(--font-heading);
        font-size: 1.85rem;
        font-weight: 700;
        color: #f59e0b;
        line-height: 1.1;
    }

    .wru-metric-label {
        font-size: 0.775rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
        margin-top: 4px;
    }

    /* Interactive Controls Bar */
    .wru-controls-wrapper {
        margin-bottom: 32px;
    }

    .wru-controls-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        background: #ffffff;
        padding: 16px 20px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .wru-search-box {
        position: relative;
        flex: 1;
        min-width: 280px;
    }

    .wru-search-box i.ph-magnifying-glass {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.15rem;
        color: #9ca3af;
        pointer-events: none;
    }

    .wru-search-input {
        width: 100%;
        padding: 10px 38px 10px 42px;
        font-size: 0.925rem;
        font-family: var(--font-body);
        color: #111827;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        outline: none;
        transition: all 0.2s ease;
    }

    .wru-search-input:focus {
        background: #ffffff;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(217, 48, 37, 0.1);
    }

    .wru-clear-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 1.1rem;
        cursor: pointer;
        display: none;
        padding: 0;
    }

    .wru-clear-btn:hover {
        color: #374151;
    }

    .wru-filter-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .wru-pill {
        padding: 8px 14px;
        font-size: 0.825rem;
        font-weight: 600;
        font-family: var(--font-body);
        color: #4b5563;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .wru-pill:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .wru-pill.active {
        background: #0a0a0a;
        color: #ffffff;
        border-color: #0a0a0a;
    }

    .wru-pill-count {
        font-size: 0.75rem;
        opacity: 0.85;
        margin-left: 4px;
    }

    .wru-results-meta {
        margin-top: 12px;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Testimonial Card Grid */
    .wru-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
        margin-bottom: 48px;
    }

    .wru-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .wru-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
        border-color: #d1d5db;
    }

    .wru-card-quote-icon {
        position: absolute;
        top: 24px;
        right: 24px;
        font-size: 2.2rem;
        color: var(--accent-color);
        opacity: 0.12;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .wru-card:hover .wru-card-quote-icon {
        opacity: 0.25;
    }

    .wru-card-body {
        margin-bottom: 24px;
    }

    .wru-quote {
        font-family: var(--font-heading);
        font-size: 1.08rem;
        line-height: 1.65;
        color: #1f2937;
        font-weight: 400;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .wru-card-footer {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .wru-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .wru-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f3f4f6;
        background: #f9fafb;
    }

    .wru-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        background: #0284c7;
        color: #ffffff;
        font-size: 0.65rem;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
    }

    .wru-reader-info {
        display: flex;
        flex-direction: column;
    }

    .wru-reader-name {
        font-family: var(--font-body);
        font-size: 0.975rem;
        font-weight: 700;
        color: #0a0a0a;
        line-height: 1.3;
    }

    .wru-reader-role {
        font-size: 0.825rem;
        color: #6b7280;
        line-height: 1.4;
        margin-top: 2px;
    }

    /* Empty Search State */
    .wru-empty-state {
        text-align: center;
        padding: 64px 24px;
        background: #ffffff;
        border: 1px dashed #d1d5db;
        border-radius: 16px;
        margin-bottom: 48px;
        display: none;
    }

    .wru-empty-icon {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 16px;
    }

    .wru-empty-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        color: #111827;
        margin-bottom: 8px;
    }

    .wru-empty-desc {
        font-size: 0.95rem;
        color: #6b7280;
        max-width: 440px;
        margin: 0 auto 20px auto;
    }

    .wru-reset-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #ffffff;
        background: #0a0a0a;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .wru-reset-btn:hover {
        background: #262626;
    }

    /* Call To Action Card */
    .wru-cta-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 48px 40px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        margin-bottom: 64px;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
        position: relative;
        overflow: hidden;
    }

    .wru-cta-card::after {
        content: "";
        position: absolute;
        right: -50px;
        top: -50px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(217, 48, 37, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .wru-cta-content {
        max-width: 600px;
        position: relative;
        z-index: 2;
    }

    .wru-cta-kicker {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #f59e0b;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .wru-cta-title {
        font-family: var(--font-heading);
        font-size: 2.2rem;
        color: #ffffff;
        line-height: 1.2;
        margin-bottom: 12px;
    }

    .wru-cta-desc {
        font-size: 1.025rem;
        color: #94a3b8;
        line-height: 1.55;
        margin: 0;
    }

    .wru-cta-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }

    .wru-btn-primary {
        padding: 13px 24px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #ffffff;
        background: var(--accent-color);
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(217, 48, 37, 0.35);
    }

    .wru-btn-primary:hover {
        background: #b91c1c;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(217, 48, 37, 0.45);
    }

    .wru-btn-secondary {
        padding: 13px 24px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .wru-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.35);
    }

    @media (max-width: 992px) {
        .wru-cta-card {
            flex-direction: column;
            text-align: center;
            padding: 36px 24px;
        }

        .wru-cta-actions {
            justify-content: center;
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .category-header-hero h1 {
            font-size: 2.3rem;
        }

        .wru-grid {
            grid-template-columns: 1fr;
        }

        .wru-controls-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .wru-cta-actions {
            flex-direction: column;
        }

        .wru-btn-primary,
        .wru-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<section class="category-page">
    <!-- Full-Width Executive Readership Hero Banner -->
    <div class="category-header-hero">
        <div class="container">
            <nav class="article-breadcrumb" aria-label="Breadcrumb">
                <a href="/">Home</a>
                <span class="sep">/</span>
                <span class="current">Who Reads Us</span>
            </nav>

            <div class="category-hero-content">
                <h1><?php echo $page_title ?></h1>
                <p class="category-hero-desc">
                    <?php echo $page_description ?>
                </p>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($testimonials)): ?>

            <!-- Testimonials Grid -->
            <div class="wru-grid" id="wruGrid">
                <?php foreach ($testimonials as $row): ?>
                    <?php
                    $readerInfo     = explode(",", $row["category_title"]);
                    $readerName     = trim($readerInfo[0]);
                    $readerPosition = ucwords(trim(str_replace($readerName . (sizeof($readerInfo) == 1 ? "" : ","), "", $row["category_title"])));

                    $defaultAvatar = IMG_LIBRARY . 'default-black-profile-pic-icon.png';
                    $img           = !empty($row['category_image']) ? $row['category_image'] : $defaultAvatar;
                    $quote         = clean_quote($row['category_description']);

                    // Safe Escaping
                    $nameEsc  = htmlspecialchars($readerName, ENT_QUOTES, 'UTF-8');
                    $roleEsc  = htmlspecialchars($readerPosition, ENT_QUOTES, 'UTF-8');
                    $imgEsc   = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    $quoteEsc = htmlspecialchars($quote, ENT_QUOTES, 'UTF-8');
                    $defaultAvatarEsc = htmlspecialchars($defaultAvatar, ENT_QUOTES, 'UTF-8');

                    // Categorization tagging for client-side filter
                    $roleLower = strtolower($readerPosition);
                    $categoryTag = 'executive';
                    if (preg_match('/(founder|ceo|co-founder|owner|partner|president)/i', $roleLower)) {
                        $categoryTag = 'founder';
                    } elseif (preg_match('/(investor|vc|partner|capital|angel|managing director)/i', $roleLower)) {
                        $categoryTag = 'investor';
                    }
                    ?>

                    <article class="wru-card" data-name="<?php echo strtolower($nameEsc); ?>" data-role="<?php echo strtolower($roleEsc); ?>" data-quote="<?php echo strtolower($quoteEsc); ?>" data-category="<?php echo $categoryTag; ?>">
                        <i class="ph-fill ph-quotes wru-card-quote-icon"></i>

                        <div class="wru-card-body">
                            <p class="wru-quote">&ldquo;<?php echo $quoteEsc; ?>&rdquo;</p>
                        </div>

                        <header class="wru-card-footer">
                            <div class="wru-avatar-wrapper">
                                <img class="wru-avatar" src="<?php echo $imgEsc; ?>" alt="<?php echo $nameEsc; ?>" loading="lazy" onerror="this.onerror=null; this.src='<?php echo $defaultAvatarEsc; ?>';">
                                <span class="wru-badge" title="Verified Reader"><i class="ph-fill ph-seal-check"></i></span>
                            </div>
                            <div class="wru-reader-info">
                                <div class="wru-reader-name"><?php echo $nameEsc; ?></div>
                                <div class="wru-reader-role"><?php echo $roleEsc; ?></div>
                            </div>
                        </header>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Empty State for Search Filter -->
            <div class="wru-empty-state" id="wruEmptyState">
                <div class="wru-empty-icon">
                    <i class="ph ph-magnifying-glass-slash"></i>
                </div>
                <h3 class="wru-empty-title">No matching reader reviews found</h3>
                <p class="wru-empty-desc">We couldn't find any reader testimonials matching your search. Try resetting your search terms or filters.</p>
                <button type="button" class="wru-reset-btn" id="wruResetFiltersBtn">
                    <i class="ph ph-arrow-counter-clockwise"></i> Reset Search & Filters
                </button>
            </div>

        <?php endif; ?>

    </div>
</section>


<!-- Subscribe Section -->
<section id="subscribe" class="newsletter-section">
    <div class="container">
        <div class="newsletter-grid">
            <div class="newsletter-info">
                <span class="newsletter-badge">WEEKLY DEEPDIVE</span>
                <h2>Get sharper fintech and tech insights in your inbox</h2>
                <p>
                    Sign up for concise summaries of powerful fintech and emerging tech insights delivered directly to your inbox every time we publish.
                </p>
            </div>
            <div class="newsletter-action">
                <form class="newsletter-form" action="/thankyou_newsletter/" method="GET">
                    <div class=" input-wrapper">
                        <input type="email" id="newsletterEmail" name="email" placeholder="Enter your email address..." required />
                        <button type="submit" class="btn btn-newsletter" id="submitNewsletter">
                            Join Now <span class="arrow">→</span>
                        </button>
                    </div>
                    <div class="newsletter-trust">
                        <svg class="shield-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Zero spam. Unsubscribe at any time with 1 click.</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>