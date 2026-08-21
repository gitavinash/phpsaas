<?php
$siteThemeColor = (defined('SITE_THEME_COLOUR') && SITE_THEME_COLOUR != '') ? SITE_THEME_COLOUR : '#4f46e5';
?>
<style>
    .not-found-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 65vh;
        padding: 2rem 1rem;
        width: 100%;
    }

    .not-found-card {
        background-color: #ffffff;
        border: 1px solid color-mix(in srgb, <?php echo htmlspecialchars($siteThemeColor); ?> 12%, #e2e8f0);
        border-radius: 24px;
        padding: 3rem 2rem;
        max-width: 440px;
        width: 100%;
        text-align: center;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05), 0 5px 15px -3px rgba(0, 0, 0, 0.02);
    }

    .not-found-number {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.04em;
        color: <?php echo htmlspecialchars($siteThemeColor); ?>;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }

    .not-found-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1.25rem;
    }

    .not-found-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: <?php echo htmlspecialchars($siteThemeColor); ?>;
    }

    .not-found-card h1 {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.75rem;
        color: #0f172a;
        letter-spacing: -0.01em;
    }

    .not-found-card p {
        font-size: 0.925rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }

    .not-found-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    @media (min-width: 400px) {
        .not-found-actions {
            flex-direction: row;
        }
    }

    .not-found-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        flex: 1;
        padding: 0.8rem 1.25rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        border: none;
    }

    .not-found-btn-primary {
        background-color: <?php echo htmlspecialchars($siteThemeColor); ?>;
        color: #ffffff !important;
        box-shadow: 0 4px 12px color-mix(in srgb, <?php echo htmlspecialchars($siteThemeColor); ?> 25%, transparent);
    }

    .not-found-btn-primary:hover {
        opacity: 0.93;
        transform: translateY(-1px);
    }

    .not-found-btn-secondary {
        background-color: #f8fafc;
        color: #0f172a !important;
        border: 1px solid #e2e8f0;
    }

    .not-found-btn-secondary:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
</style>

<div class="not-found-container">
    <div class="not-found-card">
        
        <div class="not-found-number">404</div>

        <div class="not-found-badge">
            <span class="not-found-badge-dot"></span>
            <span>Page Not Found</span>
        </div>

        <h1>Page Not Found</h1>

        <p>The page you are looking for doesn't exist, has been moved, or is temporarily unavailable.</p>

        <div class="not-found-actions">
            <a href="/dashboard/" class="not-found-btn not-found-btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <button type="button" class="not-found-btn not-found-btn-secondary" onclick="history.back()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Go Back
            </button>
        </div>

    </div>
</div>