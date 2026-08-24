<?php
$rawId = $_GET['id'] ?? ($id ?? 0);
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;
}
$encodedId = base64_encode((string)$packageId);
?>

<div class="package-page-wrapper package-price-list-wrapper" id="packagePriceListApp" data-package-id="<?php echo $packageId; ?>" data-encoded-id="<?php echo $encodedId; ?>" data-package-type="domestic">
    <!-- Header Bar -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon">
                <i class="fi fi-rr-badge-percent"></i>
            </div>
            <div class="package-header-titles">
                <h2 id="pkgHeaderTitleText">Domestic Tour Package</h2>
                <div class="pkg-meta-row">
                    <span class="pkg-theme-pill pkg-theme-standard" id="pkgMetaTheme">Standard</span>
                    <span class="pkg-duration-badge" id="pkgMetaDuration">
                        <i class="fi fi-rr-moon pkg-duration-moon"></i> 0N / <i class="fi fi-rr-sun pkg-duration-sun"></i> 1D
                    </span>
                    <span class="pkg-duration-badge" id="pkgMetaLocation">
                        <i class="fi fi-rr-marker" style="color: #0284c7;"></i> Domestic
                    </span>
                </div>
            </div>
        </div>
        <div class="package-header-actions">
            <button type="button" class="btn-v3-primary" id="btnAddPriceSlab" title="Add New Price Slabs">
                <i class="fi fi-rr-plus"></i> Add Price Slabs
            </button>
            <a href="/dashboard/packages/domestic/edit/?id=<?php echo $encodedId; ?>" class="btn-v3-action" id="btnEditPackage" title="Edit Package Details">
                <i class="fi fi-rr-edit"></i> Edit Package
            </a>
            <a href="/dashboard/packages/domestic/list/" class="btn-v3-action" id="btnBackPackages" title="Back to Domestic Packages List">
                <i class="fi fi-rr-arrow-left"></i> Packages
            </a>
        </div>
    </div>

    <!-- Quick KPI Metric Cards -->
    <div class="package-kpi-row">
        <div class="pkg-mini-kpi indigo">
            <div class="kpi-icon"><i class="fi fi-rr-receipt"></i></div>
            <div class="kpi-meta">
                <p>Total Slabs</p>
                <h3 id="statTotalSlabs">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi emerald">
            <div class="kpi-icon"><i class="fi fi-rr-check-circle"></i></div>
            <div class="kpi-meta">
                <p>Active Rates</p>
                <h3 id="statActiveRates">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi amber">
            <div class="kpi-icon"><i class="fi fi-rr-user"></i></div>
            <div class="kpi-meta">
                <p>Lowest Adult Rate</p>
                <h3 id="statLowestAdult">-</h3>
            </div>
        </div>
        <div class="pkg-mini-kpi sky">
            <div class="kpi-icon"><i class="fi fi-rr-child-head"></i></div>
            <div class="kpi-meta">
                <p>Lowest Child Rate</p>
                <h3 id="statLowestChild">-</h3>
            </div>
        </div>
    </div>

    <!-- Table Card Container -->
    <div class="package-table-card">
        <div class="v3-table-header-bar">
            <div class="table-header-title">
                <i class="fi fi-rr-layers"></i> Configured Pricing Matrix & Seasonal Slabs
            </div>
            <div class="table-header-counter" id="statPriceCountBadge">
                Loading price slabs...
            </div>
        </div>

        <div class="v3-table-responsive-wrapper">
            <table class="showTableList price-matrix-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="th-main">Validity Duration</th>
                        <th colspan="4" class="th-adult-group">Adult Pricing (INR)</th>
                        <th colspan="4" class="th-child-group">Child Pricing (INR)</th>
                        <th rowspan="2" class="th-main col-center">Sharing Type</th>
                        <th rowspan="2" class="th-main col-center">Status</th>
                        <th rowspan="2" class="th-main col-actions">Actions</th>
                    </tr>
                    <tr>
                        <!-- Adult sub-headers -->
                        <th class="th-sub-adult">Net</th>
                        <th class="th-sub-adult">MRP</th>
                        <th class="th-sub-adult selling">Selling</th>
                        <th class="th-sub-adult col-divider">Agent</th>
                        <!-- Child sub-headers -->
                        <th class="th-sub-child">Net</th>
                        <th class="th-sub-child">MRP</th>
                        <th class="th-sub-child selling">Selling</th>
                        <th class="th-sub-child col-divider">Agent</th>
                    </tr>
                </thead>
                <tbody id="priceTableBody">
                    <tr>
                        <td colspan="12" class="table-loading-state">
                            <div class="table-loading-spinner"></div>
                            <p>Loading pricing matrix & seasonal slabs...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit / Add Price Modal Dialog -->
<div class="v3-modal-overlay" id="editPriceModal" aria-hidden="true">
    <div class="v3-modal-dialog modal-price-dialog" role="dialog" aria-modal="true">
        <div class="v3-modal-header">
            <div class="v3-modal-title-group">
                <div class="v3-modal-icon-badge" id="modalPriceIconBadge">
                    <i class="fi fi-rr-edit" id="modalPriceIcon"></i>
                </div>
                <div>
                    <h3 id="modalPriceTitle">Update Pricing Slab</h3>
                    <p id="modalPriceSubtitle">Adjust seasonal validity dates and adult/child rates</p>
                </div>
            </div>
            <button type="button" class="v3-modal-close-btn v3-close-price-modal" aria-label="Close Modal">
                <i class="fi fi-rr-cross"></i>
            </button>
        </div>
        <form id="formEditPrice">
            <div class="v3-modal-body">
                <input type="hidden" id="editPriceId" name="id" value="" />

                <!-- Validity & Sharing Type Section -->
                <div class="v3-form-section">
                    <div class="v3-form-section-title">
                        <i class="fi fi-rr-calendar"></i> Seasonal Validity & Sharing
                    </div>
                    <div class="v3-form-grid-3">
                        <div class="v3-form-field">
                            <label for="editStartDate">Starting Date *</label>
                            <input type="text" id="editStartDate" name="startDate" class="v3-price-input datePickerWithoutYear" placeholder="15 July" required autocomplete="off" />
                        </div>
                        <div class="v3-form-field">
                            <label for="editEndDate">Ending Date *</label>
                            <input type="text" id="editEndDate" name="endDate" class="v3-price-input datePickerWithoutYear" placeholder="15 August" required autocomplete="off" />
                        </div>
                        <div class="v3-form-field">
                            <label for="editPriceType">Sharing Type *</label>
                            <select id="editPriceType" name="priceType" class="v3-price-input" required>
                                <option value="single">Single Sharing</option>
                                <option value="double">Double Sharing</option>
                                <option value="triple">Triple Sharing</option>
                                <option value="quad">Quad Sharing</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Adult Rates Section -->
                <div class="v3-form-section card-box">
                    <div class="v3-form-section-title adult">
                        <i class="fi fi-rr-user"></i> Adult Rates (Per Person)
                    </div>
                    <div class="v3-form-grid-4">
                        <div class="v3-form-field">
                            <label for="editAdultNet">Net Cost *</label>
                            <input type="number" id="editAdultNet" name="adultNet" class="v3-price-input" placeholder="999" required />
                        </div>
                        <div class="v3-form-field">
                            <label for="editAdultMRP">MRP</label>
                            <input type="number" id="editAdultMRP" name="adultMRP" class="v3-price-input" placeholder="1999" />
                        </div>
                        <div class="v3-form-field">
                            <label for="editAdultSelling" class="highlight">Selling Price *</label>
                            <input type="number" id="editAdultSelling" name="adultSelling" class="v3-price-input highlight-adult" placeholder="1799" required />
                        </div>
                        <div class="v3-form-field">
                            <label for="editAdultAgent">Agent Price</label>
                            <input type="number" id="editAdultAgent" name="adultAgent" class="v3-price-input" placeholder="1499" />
                        </div>
                    </div>
                </div>

                <!-- Child Rates Section -->
                <div class="v3-form-section card-box">
                    <div class="v3-form-section-title child">
                        <i class="fi fi-rr-child-head"></i> Child Rates (Per Person)
                    </div>
                    <div class="v3-form-grid-4">
                        <div class="v3-form-field">
                            <label for="editChildNet">Net Cost *</label>
                            <input type="number" id="editChildNet" name="childNet" class="v3-price-input" placeholder="599" required />
                        </div>
                        <div class="v3-form-field">
                            <label for="editChildMRP">MRP</label>
                            <input type="number" id="editChildMRP" name="childMRP" class="v3-price-input" placeholder="1299" />
                        </div>
                        <div class="v3-form-field">
                            <label for="editChildSelling" class="highlight">Selling Price *</label>
                            <input type="number" id="editChildSelling" name="childSelling" class="v3-price-input highlight-child" placeholder="999" required />
                        </div>
                        <div class="v3-form-field">
                            <label for="editChildAgent">Agent Price</label>
                            <input type="number" id="editChildAgent" name="childAgent" class="v3-price-input" placeholder="899" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="v3-modal-footer-actions">
                <button type="button" class="btn-v3-action btn-cancel-price-edit">
                    <i class="fi fi-rr-cross"></i> Cancel
                </button>
                <button type="submit" class="btn-v3-primary" id="btnSubmitPriceUpdate">
                    <i class="fi fi-rr-check"></i> <span id="btnSubmitPriceText">Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>