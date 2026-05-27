<?php

// Check if cta1_style exists in the slide table (on-the-fly migration)
$checkColumn = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "slide LIKE 'cta1_style'");
if (mysqli_num_rows($checkColumn) == 0) {
    mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "slide ADD cta1_style VARCHAR(50) DEFAULT 'solid' AFTER cta1_text_color");
}

if (isset($sliderId)) {

    $sliderId = base64_decode($sliderId);

    if ($checkSlider = mysqli_query($connection, "SELECT slide_image as featureImage, slider_device, slide_title as title, slide_subtitle, slide_description as description, slide_link, slide_link2, slider_cta, slider_cta2, slide_alignment, slide_overlay, cta1_bg_color, cta1_text_color, cta1_style, cta2_style, cta2_bg_color, cta2_text_color FROM " . TABLE_PREFIX . "slide WHERE slide_id='{$sliderId}' AND slide_site_id=" . APP_SITE_ID)) {

        if (mysqli_num_rows($checkSlider) == 1) {
            extract(mysqli_fetch_assoc($checkSlider));
        } else {
            echo "
            <script>
            alert('Invalid slider Id');
            window.location = '/dashboard/appearance/?type=sliders';
            </script>
            ";
        }
    }
} else {

    $featureImage = '';
    $title = '';
    $description = '';
    $slide_link = '';
    $slide_link2 = '';
    $slider_cta = '';
    $slider_cta2 = '';
    $slider_device = '';
    $slide_alignment = 'center';
    $slide_overlay = 1;
    $cta1_bg_color = '#F5D154';
    $cta1_text_color = '#1E293B';
    $cta1_style = 'solid';
    $cta2_style = 'outline';
    $slide_subtitle = '';
    $cta2_bg_color = '#ffffff';
    $cta2_text_color = '#1e293b';
}

?>

<style>
/* Modern Premium Workspace Styles */
.builder-title-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 16px;
}

.builder-title-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.builder-title-header h2 span {
    font-size: 14px;
    font-weight: 400;
}

.slider-builder-layout {
    display: flex;
    gap: 32px;
    margin-bottom: 48px;
    align-items: flex-start;
}

@media (max-width: 1100px) {
    .slider-builder-layout {
        flex-direction: column;
        align-items: stretch;
    }
}

/* Control Panel (Left Column) */
.builder-form-panel {
    flex: 1.1;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.builder-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
}

.builder-card:hover {
    box-shadow: 0 4px 12px -1px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e1;
}

.card-header {
    background: #f8fafc;
    padding: 16px 20px;
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid transparent;
    transition: all 0.2s ease;
    user-select: none;
}

.card-header i.header-icon {
    margin-right: 8px;
    color: #64748b;
}

.card-header.active {
    border-bottom-color: #e2e8f0;
    background: #ffffff;
    color: var(--brand-700, #3b82f6);
}

.card-header.active i.header-icon {
    color: var(--brand-700, #3b82f6);
}

.card-header .toggle-icon {
    font-size: 12px;
    color: #64748b;
    transition: transform 0.25s ease;
}

.card-header.active .toggle-icon {
    transform: rotate(180deg);
    color: var(--brand-700, #3b82f6);
}

.card-body {
    padding: 20px;
}

/* Fields Styling */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-grid.single {
    grid-template-columns: 1fr;
}

.form-group-builder {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group-builder label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.input-builder {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s;
    width: 100%;
    box-sizing: border-box;
}

.input-builder:focus {
    border-color: var(--brand-700, #3b82f6);
    outline: none;
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-700, #3b82f6) 15%, transparent);
}

/* Color Pickers Custom Styles */
.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
    background: #ffffff;
    cursor: pointer;
    transition: border-color 0.2s;
    height: 41px;
    box-sizing: border-box;
}

.color-picker-wrapper:hover {
    border-color: #94a3b8;
}

.color-picker-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 24px;
    height: 24px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    background: none;
    padding: 0;
    flex-shrink: 0;
}

.color-picker-input::-webkit-color-swatch-wrapper {
    padding: 0;
}

.color-picker-input::-webkit-color-swatch {
    border: 1px solid #cbd5e1;
    border-radius: 50%;
}

.color-picker-text {
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    font-family: monospace;
}

/* Alignment toggle */
.alignment-selector-group {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 10px;
    gap: 4px;
    border: 1px solid #e2e8f0;
}

.alignment-btn {
    flex: 1;
    cursor: pointer;
    margin: 0;
}

.alignment-btn input {
    display: none;
}

.alignment-btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px;
    border-radius: 8px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
    background: transparent;
}

.alignment-btn input:checked+.alignment-btn-content {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

/* Device toggle and switches */
.device-selector-group {
    display: flex;
    gap: 12px;
}

.device-selector-label {
    flex: 1;
    cursor: pointer;
    margin: 0;
}

.device-selector-label input[type="radio"] {
    display: none;
}

.device-selector-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    transition: all 0.2s;
    background: #ffffff;
}

.device-selector-label input[type="radio"]:checked+.device-selector-content {
    border-color: var(--brand-700, #3b82f6);
    color: var(--brand-700, #3b82f6);
    background: color-mix(in srgb, var(--brand-700, #3b82f6) 8%, #ffffff);
}

.modern-switch-group {
    display: inline-flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 10px;
    gap: 4px;
    border: 1px solid #e2e8f0;
}

.modern-switch-label {
    flex: 1;
    margin: 0;
    cursor: pointer;
}

.modern-switch-label input[type="radio"] {
    display: none;
}

.modern-switch-text {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 16px;
    border-radius: 8px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    width: 100%;
    box-sizing: border-box;
}

.modern-switch-label input[type="radio"]:checked+.modern-switch-text {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

/* Image Upload box */
.featureImageUploadBox {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
}

.featureImageUploadBox:hover {
    border-color: var(--brand-700, #3b82f6);
    background: color-mix(in srgb, var(--brand-700, #3b82f6) 4%, #f8fafc);
}

.featureImageUploadBox span {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    display: block;
    margin-bottom: 6px;
}

.featureImageUploadBox p {
    font-size: 11px;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

.featureImageUploadBox input[type="file"] {
    display: none;
}

/* Live Preview Panel (Right Column) */
.builder-preview-panel {
    flex: 0.9;
    position: sticky;
    top: 32px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    gap: 20px;
}

@media (max-width: 1100px) {
    .builder-preview-panel {
        position: relative;
        top: 0;
    }
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-header span {
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.viewport-toggle {
    display: flex;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 8px;
    gap: 2px;
}

.view-btn {
    border: none;
    background: transparent;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.view-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Simulated Device */
.simulated-device-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    min-height: 380px;
    background: #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    box-sizing: border-box;
}

.simulated-device {
    position: relative;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    background: #1e293b;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.simulated-device.desktop-mode {
    width: 100%;
    aspect-ratio: 16/9;
    border-radius: 10px;
    border: 8px solid #1e293b;
}

.simulated-device.mobile-mode {
    width: 250px;
    aspect-ratio: 9/16;
    border-radius: 32px;
    border: 10px solid #1e293b;
}

/* Phone notch */
.mobile-notch {
    display: none;
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 110px;
    height: 14px;
    background: #1e293b;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    z-index: 10;
}

.simulated-device.mobile-mode .mobile-notch {
    display: block;
}

/* Live slider preview inside frame */
.simulated-slider {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-color: #475569;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    box-sizing: border-box;
    transition: all 0.3s;
}

.preview-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.preview-overlay.active {
    opacity: 1;
}

.preview-content {
    position: relative;
    z-index: 2;
    color: #ffffff;
    width: 100%;
    max-width: 90%;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: all 0.3s;
}

.simulated-device.mobile-mode .preview-content {
    max-width: 100%;
    gap: 4px;
}

.preview-subtitle {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #f5d154;
    margin-bottom: 2px;
}

.simulated-device.mobile-mode .preview-subtitle {
    font-size: 9px;
}

.preview-title {
    font-size: 26px;
    font-weight: 700;
    line-height: 1.25;
    margin: 0;
    color: #ffffff;
}

.simulated-device.mobile-mode .preview-title {
    font-size: 15px;
}

.preview-description {
    font-size: 13px;
    opacity: 0.9;
    margin: 4px 0 12px 0;
    line-height: 1.4;
    font-weight: 400;
}

.simulated-device.mobile-mode .preview-description {
    font-size: 10px;
    margin: 2px 0 6px 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.preview-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.preview-actions .preview-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.simulated-device.mobile-mode .preview-actions .preview-cta {
    padding: 6px 12px;
    font-size: 9px;
    border-radius: 4px;
}

/* Save Action Box */
.builder-submit-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.builder-submit-card .btn-block {
    padding: 12px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    text-align: center;
}

/* Modern bottom listing layout */
.modern-grid-title {
    margin-top: 56px;
    margin-bottom: 24px;
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.slides-modern-grid {
    float: left;
    width: 100%;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 24px;
}

.slide-card-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    transition: transform 0.25s, box-shadow 0.25s;
}

.slide-card-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08);
}

.slide-card-thumbnail {
    height: 150px;
    background-size: cover;
    background-position: center;
    background-color: #475569;
    position: relative;
    padding: 16px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.slide-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 20%, rgba(15, 23, 42, 0.8) 100%);
    z-index: 1;
}

.slide-card-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
    background: rgba(15, 23, 42, 0.8);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.slide-card-badge.mobile {
    background: rgba(37, 99, 235, 0.85);
}

.slide-card-preview-text {
    position: relative;
    z-index: 2;
    color: #ffffff;
}

.preview-mini-kicker {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 0.9;
    letter-spacing: 0.05em;
    display: block;
    margin-bottom: 2px;
    color: #f5d154;
}

.preview-mini-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.slide-card-info {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.slide-card-desc {
    font-size: 13px;
    color: #64748b;
    margin: 0 0 16px 0;
    line-height: 1.5;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.slide-card-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid #f1f5f9;
    padding-top: 12px;
}

.btn-card-action {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-card-edit {
    background: #f1f5f9;
    color: #475569;
}

.btn-card-edit:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.btn-card-delete {
    background: #fef2f2;
    color: #ef4444;
}

.btn-card-delete:hover {
    background: #fee2e2;
    color: #dc2626;
}

.no-slides-box {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px;
    background: #f8fafc;
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    color: #64748b;
}

.no-slides-box i {
    font-size: 36px;
    margin-bottom: 8px;
    display: block;
    color: #cbd5e1;
}
</style>

<div class="builder-title-header">
    <h2>
        <i class="fi-rr-picture" style="color:var(--brand-700);"></i>
        <?php echo ((isset($sliderId)) ? "Update Slide" : "Add New Slide") ?>
    </h2>
    <?php if (isset($sliderId)) { ?>
    <a href='/dashboard/appearance/?type=sliders' class='btn btn-blue btn-sm'><i class="fi-rr-add"></i> Add New
        Slide</a>
    <?php } ?>
</div>

<div class="form-box" style="box-shadow:none; padding:0; background:transparent;">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="slider-builder-layout">

            <!-- LEFT PANEL: SETTINGS ACCORDIONS -->
            <div class="builder-form-panel">

                <!-- CARD 1: CONTENT -->
                <div class="builder-card">
                    <div class="card-header active">
                        <span><i class="fi-rr-document-signed header-icon"></i>1. Slide Content Details</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group-builder">
                                <label>Subtitle (Kicker Text)</label>
                                <input type="text" id="slide_subtitle" value="<?php echo $slide_subtitle ?>"
                                    class="input-builder" name="slide_subtitle" placeholder="e.g. Special Offer">
                            </div>
                            <div class="form-group-builder">
                                <label>Slide Title</label>
                                <input type="text" id="title" value="<?php echo $title ?>" class="input-builder"
                                    name="title" placeholder="e.g. Summer Collection" autofocus>
                            </div>
                        </div>
                        <div class="form-group-builder">
                            <label>Slide Description</label>
                            <textarea name="description" id="slide_description" rows="4" class="input-builder"
                                placeholder="Introduce the slider feature, products or main campaign themes..."><?php echo $description ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: CTA 1 (PRIMARY ACTION) -->
                <div class="builder-card">
                    <div class="card-header">
                        <span><i class="fi-rr-link header-icon"></i>2. Primary Action Button (CTA 1)</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group-builder">
                                <label>Button Text</label>
                                <input type="text" id="cta" value="<?php echo $slider_cta ?>" class="input-builder"
                                    name="cta" placeholder="e.g. Shop Now">
                            </div>
                            <div class="form-group-builder">
                                <label>Link URL</label>
                                <input type="text" id="link" value="<?php echo $slide_link ?>" class="input-builder"
                                    name="link" placeholder="e.g. https://domain.com/shop">
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div class="form-group-builder">
                                <label>Background Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="cta1_bg_color" value="<?php echo $cta1_bg_color ?>"
                                        class="color-picker-input">
                                    <span class="color-picker-text"><?php echo $cta1_bg_color ?></span>
                                </div>
                            </div>
                            <div class="form-group-builder">
                                <label>Text Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="cta1_text_color" value="<?php echo $cta1_text_color ?>"
                                        class="color-picker-input">
                                    <span class="color-picker-text"><?php echo $cta1_text_color ?></span>
                                </div>
                            </div>
                            <div class="form-group-builder">
                                <label>Button Style</label>
                                <select name="cta1_style" id="cta1_style" class="input-builder" style="height:41px;">
                                    <option value="solid" <?php echo ($cta1_style == 'solid' ? 'selected' : '') ?>>Solid Fill</option>
                                    <option value="outline" <?php echo ($cta1_style == 'outline' ? 'selected' : '') ?>>Outline (Border Only)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: CTA 2 (SECONDARY ACTION) -->
                <div class="builder-card">
                    <div class="card-header">
                        <span><i class="fi-rr-redo header-icon"></i>3. Secondary Action Button (CTA 2)</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group-builder">
                                <label>Button Text</label>
                                <input type="text" id="cta2" value="<?php echo $slider_cta2 ?>" class="input-builder"
                                    name="cta2" placeholder="e.g. Learn More">
                            </div>
                            <div class="form-group-builder">
                                <label>Link URL</label>
                                <input type="text" id="link2" value="<?php echo $slide_link2 ?>" class="input-builder"
                                    name="link2" placeholder="e.g. https://...">
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div class="form-group-builder">
                                <label>Background Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="cta2_bg_color" value="<?php echo $cta2_bg_color ?>"
                                        class="color-picker-input">
                                    <span class="color-picker-text"><?php echo $cta2_bg_color ?></span>
                                </div>
                            </div>
                            <div class="form-group-builder">
                                <label>Text Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="cta2_text_color" value="<?php echo $cta2_text_color ?>"
                                        class="color-picker-input">
                                    <span class="color-picker-text"><?php echo $cta2_text_color ?></span>
                                </div>
                            </div>
                            <div class="form-group-builder">
                                <label>Button Style</label>
                                <select name="cta2_style" id="cta2_style" class="input-builder" style="height:41px;">
                                    <option value="outline" <?php echo ($cta2_style == 'outline' ? 'selected' : '') ?>>
                                        Outline (Border Only)</option>
                                    <option value="solid" <?php echo ($cta2_style == 'solid' ? 'selected' : '') ?>>Solid
                                        Fill</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: DISPLAY SETTINGS -->
                <div class="builder-card">
                    <div class="card-header">
                        <span><i class="fi-rr-settings-sliders header-icon"></i>4. Layout & Overlay Config</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-body">
                        <div class="form-group-builder" style="margin-bottom: 20px;">
                            <label style="margin-bottom:6px;">Target Screen Device</label>
                            <div class="device-selector-group">
                                <label class="device-selector-label">
                                    <input type="radio" name="device" value="desktop"
                                        <?php echo (strtolower($slider_device) == 'desktop' || empty($slider_device)) ? 'checked' : ''; ?>>
                                    <div class="device-selector-content">
                                        <i class="fas fa-desktop"></i>
                                        <span>Desktop Visibility</span>
                                    </div>
                                </label>
                                <label class="device-selector-label">
                                    <input type="radio" name="device" value="mobile"
                                        <?php echo (strtolower($slider_device) == 'mobile') ? 'checked' : ''; ?>>
                                    <div class="device-selector-content">
                                        <i class="fas fa-mobile-alt"></i>
                                        <span>Mobile Visibility</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group-builder">
                                <label>Text Alignment</label>
                                <div class="alignment-selector-group">
                                    <label class="alignment-btn">
                                        <input type="radio" name="slide_alignment" value="left"
                                            <?php echo ($slide_alignment == 'left' ? 'checked' : '') ?>>
                                        <div class="alignment-btn-content">
                                            <i class="fas fa-align-left"></i>
                                            <span>Left</span>
                                        </div>
                                    </label>
                                    <label class="alignment-btn">
                                        <input type="radio" name="slide_alignment" value="center"
                                            <?php echo ($slide_alignment == 'center' ? 'checked' : '') ?>>
                                        <div class="alignment-btn-content">
                                            <i class="fas fa-align-center"></i>
                                            <span>Center</span>
                                        </div>
                                    </label>
                                    <label class="alignment-btn">
                                        <input type="radio" name="slide_alignment" value="right"
                                            <?php echo ($slide_alignment == 'right' ? 'checked' : '') ?>>
                                        <div class="alignment-btn-content">
                                            <i class="fas fa-align-right"></i>
                                            <span>Right</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group-builder">
                                <label>Dark Overlay Mask</label>
                                <div class="modern-switch-group">
                                    <label class="modern-switch-label">
                                        <input type="radio" name="slide_overlay" value="1"
                                            <?php echo ($slide_overlay == 1 ? 'checked' : '') ?>>
                                        <span class="modern-switch-text">On</span>
                                    </label>
                                    <label class="modern-switch-label">
                                        <input type="radio" name="slide_overlay" value="0"
                                            <?php echo ($slide_overlay != 1 ? 'checked' : '') ?>>
                                        <span class="modern-switch-text">Off</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAVE CARD -->
                <div class="builder-submit-card">
                    <input type="submit" id="publishPost" class="btn btn-green btn-block"
                        value="<?php echo ((isset($sliderId)) ? 'Update Slide Settings' : 'Publish New Slide') ?>">

                    <?php
                    if (isset($sliderId)) {
                        echo "<input type='hidden' name='sliderId' value='" . $sliderId . "'>";
                        echo "<input type='hidden' name='featureImage' value='" . $featureImage . "'>";
                    }
                    ?>
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="slider">
                </div>

            </div>

            <!-- RIGHT PANEL: INTERACTIVE LIVE PREVIEW -->
            <div class="builder-preview-panel">
                <div class="preview-header">
                    <span><i class="fi-rr-eye" style="margin-right:6px;"></i>Live Builder Preview</span>
                    <div class="viewport-toggle">
                        <button type="button" class="view-btn active" data-viewport="desktop">
                            <i class="fas fa-desktop"></i> Desktop
                        </button>
                        <button type="button" class="view-btn" data-viewport="mobile">
                            <i class="fas fa-mobile-alt"></i> Mobile
                        </button>
                    </div>
                </div>

                <div class="simulated-device-wrapper">
                    <div class="simulated-device desktop-mode">
                        <div class="mobile-notch"></div>
                        <div class="simulated-slider" id="liveSliderPreview"
                            style="background-image: <?php echo (!empty($featureImage) ? "url('//" . SITE_DOMAIN . $featureImage . "')" : 'none') ?>;">
                            <div class="preview-overlay <?php echo ($slide_overlay == 1 ? 'active' : '') ?>"></div>

                            <div class="preview-content"
                                style="text-align: <?php echo $slide_alignment ?>; align-items: <?php echo ($slide_alignment == 'left' ? 'flex-start' : ($slide_alignment == 'right' ? 'flex-end' : 'center')) ?>;">
                                <span class="preview-subtitle"
                                    style="display: <?php echo (!empty($slide_subtitle) ? 'block' : 'none') ?>;"><?php echo $slide_subtitle ?></span>
                                <h1 class="preview-title"><?php echo (!empty($title) ? $title : 'Slide Main Title') ?>
                                </h1>
                                <p class="preview-description">
                                    <?php echo (!empty($description) ? $description : 'A beautiful descriptive kicker summary explaining what this slide contains.') ?>
                                </p>

                                <div class="preview-actions">
                                    <?php
                                    $cta1Bg = ($cta1_style == 'outline') ? 'transparent' : $cta1_bg_color;
                                    $cta1Border = '2px solid ' . $cta1_bg_color;
                                    ?>
                                    <a class="preview-cta btn-cta-1"
                                        style="display: <?php echo (!empty($slider_cta) ? 'inline-flex' : 'none') ?>; background-color: <?php echo $cta1Bg ?>; color: <?php echo $cta1_text_color ?>; border: <?php echo $cta1Border ?>;">
                                        <?php echo $slider_cta ?>
                                    </a>

                                    <?php
                                    $cta2Bg = ($cta2_style == 'outline') ? 'transparent' : $cta2_bg_color;
                                    $cta2Border = '2px solid ' . $cta2_bg_color;
                                    ?>
                                    <a class="preview-cta btn-cta-2"
                                        style="display: <?php echo (!empty($slider_cta2) ? 'inline-flex' : 'none') ?>; background-color: <?php echo $cta2Bg ?>; color: <?php echo $cta2_text_color ?>; border: <?php echo $cta2Border ?>;">
                                        <?php echo $slider_cta2 ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide Image Loader Settings inside Preview panel -->
                <div>
                    <h3
                        style="font-size:13px; font-weight:700; color:#334155; text-transform:uppercase; margin-bottom:8px;">
                        Slide Background Image</h3>
                    <div class="form-group featureImageUploadBox upload-btn"
                        style="display: <?php echo (($featureImage != '') ? 'none' : 'block') ?>;">
                        <i class="fi-rr-upload"
                            style="font-size:24px; color:#94a3b8; display:block; margin-bottom:8px;"></i>
                        <span style="color: #ffffff;">Upload Background Image</span>
                        <input type="file" id="featureImage" name="featureImage"
                            accept="image/x-png, image/gif, image/jpeg"
                            <?php echo ((isset($sliderId)) ? '' : 'required') ?>>
                        <p>Supported: PNG, JPG, JPEG, GIF (Max 500kb)</p>
                    </div>
                    <div class="img-preview featureImagePreviewBox"
                        style="display: <?php echo (($featureImage != '') ? 'block' : 'none') ?>; text-align:center;">
                        <img src="//<?php echo SITE_DOMAIN . $featureImage ?>" id="featureImagePath"
                            style="max-height:100px; border-radius:8px; border:1px solid #cbd5e1; margin-bottom:8px;"
                            alt="">
                        <p id="featureChangeImage" class="btn btn-red btn-sm"
                            style="display:block; max-width:180px; margin: 0 auto; padding: 6px 12px; font-size:12px; font-weight:600;">
                            <i class="fi-rr-trash"></i> Remove Image
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<h2 class="modern-grid-title"><i class="fi-rr-apps-sort" style="color:var(--brand-700);"></i> All Slides List</h2>
<div class="slides-modern-grid">
    <?php
    if ($sliderList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slide_site_id=" . APP_SITE_ID)) {
        if (mysqli_num_rows($sliderList) > 0) {
            while ($sliderData = mysqli_fetch_assoc($sliderList)) {
                extract($sliderData); ?>
    <div class="slide-card-item">
        <div class="slide-card-thumbnail" style="background-image: url('<?php echo $slide_image ?>');">
            <div class="slide-card-overlay"></div>
            <div class="slide-card-badge <?php echo strtolower($slider_device) ?>">
                <i
                    class="fas <?php echo (strtolower($slider_device) == 'mobile' ? 'fa-mobile-alt' : 'fa-desktop') ?>"></i>
                <span><?php echo ucfirst($slider_device) ?></span>
            </div>
            <div class="slide-card-preview-text">
                <?php if (!empty($slide_subtitle)) { ?>
                <span class="preview-mini-kicker"><?php echo $slide_subtitle ?></span>
                <?php } ?>
                <h4 class="preview-mini-title"><?php echo $slide_title ?></h4>
            </div>
        </div>
        <div class="slide-card-info">
            <p class="slide-card-desc">
                <?php echo (strlen($slide_description) > 90 ? substr($slide_description, 0, 90) . '...' : $slide_description) ?>
            </p>
            <div class="slide-card-actions">
                <a href="/dashboard/appearance/?type=sliders&sliderId=<?php echo base64_encode($slide_id); ?>"
                    class="btn-card-action btn-card-edit">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
                <a href="#" id="<?php echo base64_encode($slide_id) ?>_slide"
                    class="btn-card-action btn-card-delete deleteBtn">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
    </div>
    <?php
            }
        } else { ?>
    <div class="no-slides-box">
        <i class="fi-rr-box-open"></i>
        <p>No active slides found. Use the builder above to publish your first slide!</p>
    </div>
    <?php }
    } else {
        echo "<div class='error-box'>" . mysqli_error($connection) . "</div>";
    }
    ?>
</div>

<script>
window.addEventListener('DOMContentLoaded', (event) => {
    if (typeof jQuery !== 'undefined') {
        (function($) {
            
            // Intercept delete click to clear sliderId from URL on reload
            $('.deleteBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                var btnId = this.id;
                var parts = btnId.split("_");
                var slideId = parts[0];
                var action = parts[1];
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            data: {id: slideId, type: "delete", action: action},
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire(
                                        res.label ? res.label : "Deleted!",
                                        res.msg ? res.msg : "Your slide has been deleted.",
                                        "success"
                                    );
                                    // Redirect to clear sliderId query param
                                    window.setTimeout(function() {
                                        window.location.href = "/dashboard/appearance/?type=sliders";
                                    }, 1200);
                                } else {
                                    Swal.fire("Error", res.msg, "error");
                                }
                            },
                            error: function(err) {
                                console.log(err);
                                Swal.fire("Error", "Something went wrong while deleting.", "error");
                            }
                        });
                    }
                });
            });

            // Collapsible Panels (Accordion) Action
            $('.card-header').click(function() {
                var body = $(this).next('.card-body');
                if (body.is(':visible')) {
                    body.slideUp(200);
                    $(this).removeClass('active');
                } else {
                    $('.card-body').slideUp(200);
                    $('.card-header').removeClass('active');
                    body.slideDown(200);
                    $(this).addClass('active');
                }
            });

            // Viewport Desktop/Mobile preview toggle
            $('.view-btn').click(function(e) {
                e.preventDefault();
                $('.view-btn').removeClass('active');
                $(this).addClass('active');
                
                var viewport = $(this).data('viewport');
                if (viewport === 'mobile') {
                    $('.simulated-device').removeClass('desktop-mode').addClass('mobile-mode');
                } else {
                    $('.simulated-device').removeClass('mobile-mode').addClass('desktop-mode');
                }
            });

            // Dynamic Live Preview Synchronization
            function updateLivePreview() {
                // 1. Kicker/Subtitle
                var subtitleVal = $('#slide_subtitle').val();
                if (subtitleVal && subtitleVal.trim() !== '') {
                    $('.preview-subtitle').text(subtitleVal).show();
                } else {
                    $('.preview-subtitle').hide();
                }

                // 2. Title
                var titleVal = $('#title').val();
                $('.preview-title').text(titleVal && titleVal.trim() !== '' ? titleVal : 'Slide Main Title');

                // 3. Description
                var descVal = $('#slide_description').val();
                $('.preview-description').text(descVal && descVal.trim() !== '' ? descVal : 'A beautiful descriptive kicker summary explaining what this slide contains.');

                // 4. CTA 1 Action Button
                var cta1Text = $('#cta').val();
                var cta1Bg = $('input[name="cta1_bg_color"]').val();
                var cta1Color = $('input[name="cta1_text_color"]').val();
                var cta1Style = $('#cta1_style').val();
                
                if (cta1Text && cta1Text.trim() !== '') {
                    if (cta1Style === 'outline') {
                        $('.btn-cta-1').text(cta1Text).css({
                            'background-color': 'transparent',
                            'color': cta1Bg,
                            'border': '2px solid ' + cta1Bg
                        }).show();
                    } else {
                        $('.btn-cta-1').text(cta1Text).css({
                            'background-color': cta1Bg,
                            'color': cta1Color,
                            'border': '2px solid ' + cta1Bg
                        }).show();
                    }
                } else {
                    $('.btn-cta-1').hide();
                }

                // 5. CTA 2 Action Button
                var cta2Text = $('#cta2').val();
                var cta2Bg = $('input[name="cta2_bg_color"]').val();
                var cta2Color = $('input[name="cta2_text_color"]').val();
                var cta2Style = $('#cta2_style').val();

                if (cta2Text && cta2Text.trim() !== '') {
                    if (cta2Style === 'outline') {
                        $('.btn-cta-2').text(cta2Text).css({
                            'background-color': 'transparent',
                            'color': cta2Color || cta2Bg,
                            'border': '2px solid ' + cta2Bg
                        }).show();
                    } else {
                        $('.btn-cta-2').text(cta2Text).css({
                            'background-color': cta2Bg,
                            'color': cta2Color,
                            'border': '2px solid ' + cta2Bg
                        }).show();
                    }
                } else {
                    $('.btn-cta-2').hide();
                }

                // 6. Text Alignment
                var alignment = $('input[name="slide_alignment"]:checked').val();
                $('.preview-content').css({
                    'text-align': alignment,
                    'align-items': alignment === 'left' ? 'flex-start' : (alignment === 'right' ? 'flex-end' : 'center')
                });

                // 7. Dark Overlay
                var overlay = $('input[name="slide_overlay"]:checked').val();
                if (overlay == '1') {
                    $('.preview-overlay').addClass('active');
                } else {
                    $('.preview-overlay').removeClass('active');
                }
            }

            // Input listeners for live updates
            $('#slide_subtitle, #title, #slide_description, #cta, #cta2').on('input keyup', updateLivePreview);
            
            // Select listeners
            $('#cta1_style, #cta2_style').on('change', updateLivePreview);

            // Radios change listeners
            $('input[name="slide_alignment"], input[name="slide_overlay"]').on('change', updateLivePreview);

            // Custom Color Input text update & preview trigger
            $('input[type="color"]').on('input change', function() {
                var colorVal = $(this).val();
                $(this).next('.color-picker-text').text(colorVal);
                updateLivePreview();
            });

            // Simulated triggering of custom color wraps & labels
            $('.color-picker-wrapper').click(function(e) {
                if (!$(e.target).hasClass('color-picker-input')) {
                    $(this).find('.color-picker-input').click();
                }
            });
            $('.form-group-builder label').click(function() {
                var picker = $(this).next('.color-picker-wrapper').find('.color-picker-input');
                if (picker.length) {
                    picker.click();
                }
            });

            // File Input change for instant local preview background image
            $('#featureImage').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#liveSliderPreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#featureImagePath').attr('src', e.target.result);
                        $('.featureImagePreviewBox').show();
                        $('.featureImageUploadBox').hide();
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Remove legacy preview image click
            $('#featureChangeImage').click(function() {
                $('#liveSliderPreview').css('background-image', 'none');
                $('#featureImage').val('');
                $('#featureImagePath').attr('src', '');
                $('.featureImagePreviewBox').hide();
                $('.featureImageUploadBox').show();
            });

            // Populate preview on initial load
            updateLivePreview();

            // AJAX Form submission
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                
                var form = this;
                var formData = new FormData(form);
                formData.append('ajax', '1');
                
                var originalBtnVal = $('#publishPost').val();
                $('#publishPost').prop('disabled', true).val('Publishing, please wait...');
                
                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(data) {
                        $('#publishPost').prop('disabled', false).val(originalBtnVal);
                        if(data.status == 200) {
                            Swal.fire({
                                title: 'Success', 
                                text: data.msg, 
                                type: 'success',
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error', 
                                text: data.msg, 
                                type: 'error',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        $('#publishPost').prop('disabled', false).val(originalBtnVal);
                        Swal.fire({
                            title: 'Error', 
                            text: 'Something went wrong while submitting.', 
                            type: 'error',
                            icon: 'error'
                        });
                    }
                });
            });
            
        })(jQuery);
    }
});
</script>