<?php
$currentUserType = strtolower(defined('USER_TYPE') ? USER_TYPE : '');
if (!in_array($currentUserType, ['administrator', 'operator'])) {
    echo "<script>window.location.href = '/dashboard/setting/profile/';</script>";
    exit();
}
$packageId = isset($site_package_id) ? (int)$site_package_id : (defined('SITE_PACKAGE_ID') ? (int)SITE_PACKAGE_ID : 7);
$isPackageSix = ($packageId === 6 || (defined('SITE_PACKAGE_ID') && (int)SITE_PACKAGE_ID === 6));
?>
<div class="package-page-wrapper setting-page-wrapper<?php echo $isPackageSix ? ' is-package-six' : ''; ?>" id="settingAppConfigApp" data-package-id="<?php echo $packageId; ?>" data-is-package-six="<?php echo $isPackageSix ? '1' : '0'; ?>">
    <!-- Header Banner -->
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon app-config-header-icon">
                <i class="fi fi-rr-apps-sort"></i>
            </div>
            <div class="package-header-titles">
                <h2>App &amp; Business Configuration</h2>
                <p>Control business operating nature, quotation workflows, proposal modes, and core feature modules.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/setting/" class="btn-v3-action" data-tooltip="Back to Settings">
                <i class="fi fi-rr-settings"></i> <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Two-Column Form & Live Preview Layout -->
    <div class="v3-form-split-layout">
        <!-- Left: Main Form Cards -->
        <div class="v3-form-main-col">
            <form id="settingAppConfigForm" class="v3-form-container" autocomplete="off" novalidate>
                <!-- Card 1: Company Operating Nature -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon indigo">
                            <i class="fi fi-rr-building"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Business Operating Nature</h3>
                            <p>Select your primary industry vertical to tailor workspace workflows, forms, and vocabulary.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <input type="hidden" id="configCompanyNature" name="company_nature" value="travel_agency">

                        <div class="v3-nature-options-grid" id="natureOptionsContainer">
                            <!-- Option 1: Travel Agency -->
                            <div class="v3-nature-option-card is-selected" data-nature="travel_agency">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-plane-departure"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">Travel Agency &amp; Tour Operator</h4>
                                    <p class="v3-nature-option-desc">Custom holiday packages, day-wise itineraries, hotel vouchers, and traveler proposals.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>

                            <!-- Option 2: Transporter -->
                            <div class="v3-nature-option-card" data-nature="transporter">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-bus"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">Transporter &amp; Fleet Operator</h4>
                                    <p class="v3-nature-option-desc">Cabs, tempo travellers, coach rentals, route-based quotations, and fleet dispatching.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>

                            <!-- Option 3: DMC -->
                            <div class="v3-nature-option-card" data-nature="dmc">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-city"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">Destination Management (DMC)</h4>
                                    <p class="v3-nature-option-desc">B2B incoming tour operator handling regional ground services and supplier contracts.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>

                            <!-- Option 4: Hotel & Homestay -->
                            <div class="v3-nature-option-card" data-nature="hotel_resort">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-bed-alt"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">Hotel, Resort &amp; Homestay</h4>
                                    <p class="v3-nature-option-desc">Room inventory reservations, stay packages, guest hospitality, and direct booking slips.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>

                            <!-- Option 5: Spa & Wellness -->
                            <div class="v3-nature-option-card" data-nature="spa_wellness">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-flower"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">Spa, Wellness &amp; Retreat</h4>
                                    <p class="v3-nature-option-desc">Wellness therapy packages, appointment bookings, retreat sessions, and client passes.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>

                            <!-- Option 6: General Business -->
                            <div class="v3-nature-option-card" data-nature="general">
                                <div class="v3-nature-option-icon">
                                    <i class="fi fi-rr-briefcase"></i>
                                </div>
                                <div class="v3-nature-option-body">
                                    <h4 class="v3-nature-option-title">General Business &amp; Custom</h4>
                                    <p class="v3-nature-option-desc">Standard CRM pipeline, customer quotes, inquiries, and generalized proposal builder.</p>
                                </div>
                                <span class="v3-nature-radio-check"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Currency & Financial Defaults -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon emerald">
                            <i class="fi fi-rr-indian-rupee-sign"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Currency &amp; Financial Defaults</h3>
                            <p>Configure system base currency, taxation rules, proposal validity duration, and booking advance requirements.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Base System Currency -->
                            <div class="v3-form-group">
                                <label for="cfgCurrencyDefault" class="v3-form-label">
                                    Base System Currency <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <span class="input-icon v3-currency-symbol-icon" id="cfgCurrencyIcon">₹</span>
                                    <select id="cfgCurrencyDefault" name="currency_default" class="v3-form-select selectJS">
                                        <option value="INR">INR (₹) - Indian Rupee</option>
                                        <option value="USD">USD ($) - US Dollar</option>
                                        <option value="EUR">EUR (€) - Euro</option>
                                        <option value="AED">AED (د.إ) - UAE Dirham</option>
                                        <option value="GBP">GBP (£) - British Pound</option>
                                        <option value="THB">THB (฿) - Thai Baht</option>
                                        <option value="SGD">SGD (S$) - Singapore Dollar</option>
                                        <option value="CAD">CAD (C$) - Canadian Dollar</option>
                                        <option value="AUD">AUD (A$) - Australian Dollar</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Currency Symbol (Auto-managed based on Base Currency) -->
                            <input type="hidden" id="cfgCurrencySymbol" name="currency_symbol" value="₹">

                            <!-- Tax / GST Calculation Mode -->
                            <div class="v3-form-group">
                                <label for="cfgTaxMode" class="v3-form-label">
                                    Tax / GST Calculation Mode
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-receipt input-icon"></i>
                                    <select id="cfgTaxMode" name="tax_mode" class="v3-form-select selectJS">
                                        <option value="exclusive">Exclusive (Tax added on top of quotation amount)</option>
                                        <option value="inclusive">Inclusive (Taxes already included in quoted rates)</option>
                                        <option value="exempt">Exempt / Zero-Rated (No tax calculations)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quotation Validity Window -->
                            <div class="v3-form-group">
                                <label for="cfgQuotationValidityDays" class="v3-form-label">
                                    Default Quotation Validity Window
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-hourglass-end input-icon"></i>
                                    <select id="cfgQuotationValidityDays" name="quotation_validity_days" class="v3-form-select selectJS">
                                        <option value="3">3 Days before expiry</option>
                                        <option value="7" selected>7 Days (Standard proposal window)</option>
                                        <option value="15">15 Days</option>
                                        <option value="30">30 Days (1 Month)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Advance Payment Deposit -->
                            <div class="v3-form-group">
                                <label for="cfgAdvancePaymentPercent" class="v3-form-label">
                                    Standard Advance Booking Deposit (%)
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-percentage input-icon"></i>
                                    <select id="cfgAdvancePaymentPercent" name="advance_payment_percent" class="v3-form-select selectJS">
                                        <option value="20">20% Booking Deposit</option>
                                        <option value="25" selected>25% Booking Deposit (Recommended)</option>
                                        <option value="30">30% Booking Deposit</option>
                                        <option value="50">50% Half Payment</option>
                                        <option value="100">100% Full Payment Required</option>
                                        <option value="custom">Custom Percentage...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Custom Advance Deposit Input -->
                            <div class="v3-form-group is-hidden" id="cfgAdvanceCustomGroup">
                                <label for="cfgAdvancePaymentPercentCustom" class="v3-form-label">
                                    Custom Deposit Percentage (%) <span class="required">*</span>
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-calculator input-icon"></i>
                                    <input type="number" id="cfgAdvancePaymentPercentCustom" class="v3-form-input" min="1" max="100" step="1" placeholder="e.g. 35">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Lead Routing & Pipeline Automation -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon blue">
                            <i class="fi fi-rr-shuffle"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Lead Routing &amp; Pipeline Automation</h3>
                            <p>Manage lead assignment rules among sales team members, default follow-up cadence, and SLA inactivity flags.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Lead Assignment Strategy -->
                            <div class="v3-form-group">
                                <label for="cfgLeadAssignmentMode" class="v3-form-label">
                                    Lead Assignment Strategy
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-user-gear input-icon"></i>
                                    <select id="cfgLeadAssignmentMode" name="lead_assignment_mode" class="v3-form-select selectJS">
                                        <option value="manual">Manual Assignment (Admin / Manager allocates leads)</option>
                                        <option value="round_robin">Round-Robin Auto Distribution (Rotates evenly to active agents)</option>
                                        <option value="claim">Open Claim Pool (Available agents can claim unassigned leads)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Default Follow-Up Interval -->
                            <div class="v3-form-group">
                                <label for="cfgLeadFollowupHours" class="v3-form-label">
                                    Default Follow-Up Cadence
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-alarm-clock input-icon"></i>
                                    <select id="cfgLeadFollowupHours" name="lead_default_followup_hours" class="v3-form-select selectJS">
                                        <option value="4">Same Day (Within 4 Hours)</option>
                                        <option value="24" selected>Next Day (Within 24 Hours)</option>
                                        <option value="48">48 Hours (2 Days)</option>
                                        <option value="72">72 Hours (3 Days)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Lead Inactivity SLA Alert -->
                            <div class="v3-form-group">
                                <label for="cfgLeadSlaHours" class="v3-form-label">
                                    Lead Inactivity SLA Alert
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-bell-ring input-icon"></i>
                                    <select id="cfgLeadSlaHours" name="lead_sla_inactivity_hours" class="v3-form-select selectJS">
                                        <option value="0">Disabled (No inactivity alerts)</option>
                                        <option value="24" selected>24 Hours (Flag lead if untouched for 1 day)</option>
                                        <option value="48">48 Hours (Flag lead if untouched for 2 days)</option>
                                        <option value="72">72 Hours (Flag lead if untouched for 3 days)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Client Proposal Delivery & Communication -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon purple">
                            <i class="fi fi-rr-paper-plane"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Client Proposal Delivery &amp; Communication</h3>
                            <p>Configure default delivery channel and pricing presentation format for quotations and tour proposals.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Default Proposal Sharing Channel -->
                            <div class="v3-form-group">
                                <label for="cfgProposalDeliveryChannel" class="v3-form-label">
                                    Default Proposal Sharing Mode
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-share input-icon"></i>
                                    <select id="cfgProposalDeliveryChannel" name="proposal_delivery_channel" class="v3-form-select selectJS">
                                        <option value="whatsapp" selected>WhatsApp Direct (Pre-filled chat message with proposal link)</option>
                                        <option value="email">Email Notification (Email with attached PDF proposal)</option>
                                        <option value="web_link">Interactive Web Link (Mobile-friendly client approval portal)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quotation Price Display Mode -->
                            <div class="v3-form-group">
                                <label for="cfgQuotationPriceDisplay" class="v3-form-label">
                                    Quotation Price Presentation
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-eye input-icon"></i>
                                    <select id="cfgQuotationPriceDisplay" name="quotation_price_display" class="v3-form-select selectJS">
                                        <option value="lump_sum" selected>Lump-Sum Total (Displays overall package cost only)</option>
                                        <option value="itemized">Itemized Breakdown (Displays service-wise &amp; day-wise costs)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Fleet & Transport Tariff Preferences (Conditional: Only visible if Transporter) -->
                <div class="v3-card-section is-hidden" id="cardFleetTariffPreferences">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon amber">
                            <i class="fi fi-rr-bus"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Fleet &amp; Transport Tariff Preferences</h3>
                            <p>Default calculation rules, border tolls, and driver allowances for vehicle rentals and fleet dispatching.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <div class="v3-form-grid">
                            <!-- Default Tariff Calculation Mode -->
                            <div class="v3-form-group">
                                <label for="cfgTransporterTariffMode" class="v3-form-label">
                                    Default Tariff Calculation Mode
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-calculator input-icon"></i>
                                    <select id="cfgTransporterTariffMode" name="transporter_tariff_mode" class="v3-form-select selectJS">
                                        <option value="per_km" selected>Per-Kilometer (KM rate with daily minimum threshold)</option>
                                        <option value="fixed_route">Fixed Route Packages (Point-to-point / Airport Transfers)</option>
                                        <option value="daily_hire">Daily Lump-Sum Hire (Flat day vehicle rental)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Toll, State Tax & Parking Policy -->
                            <div class="v3-form-group">
                                <label for="cfgTransporterTollPolicy" class="v3-form-label">
                                    Toll, State Border Tax &amp; Parking Policy
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-road input-icon"></i>
                                    <select id="cfgTransporterTollPolicy" name="transporter_toll_policy" class="v3-form-select selectJS">
                                        <option value="included" selected>Included in Quoted Price</option>
                                        <option value="extra">Excluded (Billed extra as per actual receipts)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Driver Night Allowance Policy -->
                            <div class="v3-form-group">
                                <label for="cfgTransporterDriverAllowance" class="v3-form-label">
                                    Driver Night Allowance Policy
                                </label>
                                <div class="v3-input-icon-wrap">
                                    <i class="fi fi-rr-moon input-icon"></i>
                                    <select id="cfgTransporterDriverAllowance" name="transporter_driver_allowance" class="v3-form-select selectJS">
                                        <option value="included" selected>Included in Base Tariff</option>
                                        <option value="extra">Charged Extra per night halt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Lead View Actions & Controls -->
                <div class="v3-card-section">
                    <div class="v3-card-header">
                        <div class="v3-card-header-icon teal">
                            <i class="fi fi-rr-cursor"></i>
                        </div>
                        <div class="v3-card-header-text">
                            <h3>Lead View Actions &amp; Controls</h3>
                            <p>Control the visibility of quick action tiles and communication buttons displayed on the Lead Details View.</p>
                        </div>
                    </div>

                    <div class="v3-card-body">
                        <!-- Toggle 1: Send Payment Link (Premium) -->
                        <div class="v3-feature-toggle-row<?php echo $isPackageSix ? ' is-locked' : ''; ?>">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-link"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Send Payment Link <span class="<?php echo $isPackageSix ? 'v3-badge-upgrade' : 'v3-badge-premium'; ?>"><i class="fi fi-rr-crown"></i> <?php echo $isPackageSix ? 'Upgrade Required' : 'Premium'; ?></span></h4>
                                    <p>Display fast tile to generate and send instant online payment collection links to travelers.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewPaymentLink">
                                <input type="checkbox" id="cfgLeadViewPaymentLink" name="lead_view_show_payment_link" value="1" <?php echo $isPackageSix ? 'disabled' : 'checked'; ?>>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 2: Record Payment Received (Premium) -->
                        <div class="v3-feature-toggle-row<?php echo $isPackageSix ? ' is-locked' : ''; ?>">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-indian-rupee-sign"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Record Payment Received <span class="<?php echo $isPackageSix ? 'v3-badge-upgrade' : 'v3-badge-premium'; ?>"><i class="fi fi-rr-crown"></i> <?php echo $isPackageSix ? 'Upgrade Required' : 'Premium'; ?></span></h4>
                                    <p>Display fast tile to manually log offline cash, UPI, bank transfers, and upload payment receipts.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewRecordPayment">
                                <input type="checkbox" id="cfgLeadViewRecordPayment" name="lead_view_show_record_payment" value="1" <?php echo $isPackageSix ? 'disabled' : 'checked'; ?>>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 3: Send Tour Itinerary (Premium) -->
                        <div class="v3-feature-toggle-row<?php echo $isPackageSix ? ' is-locked' : ''; ?>">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-paper-plane"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Send Tour Itinerary <span class="<?php echo $isPackageSix ? 'v3-badge-upgrade' : 'v3-badge-premium'; ?>"><i class="fi fi-rr-crown"></i> <?php echo $isPackageSix ? 'Upgrade Required' : 'Premium'; ?></span></h4>
                                    <p>Display proposal action to build, customize, and share interactive day-wise tour package itineraries.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewSendItinerary">
                                <input type="checkbox" id="cfgLeadViewSendItinerary" name="lead_view_show_send_itinerary" value="1" <?php echo $isPackageSix ? 'disabled' : 'checked'; ?>>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 4: Send Quotation (Premium) -->
                        <div class="v3-feature-toggle-row<?php echo $isPackageSix ? ' is-locked' : ''; ?>">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-document"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Send Quotation <span class="<?php echo $isPackageSix ? 'v3-badge-upgrade' : 'v3-badge-premium'; ?>"><i class="fi fi-rr-crown"></i> <?php echo $isPackageSix ? 'Upgrade Required' : 'Premium'; ?></span></h4>
                                    <p>Display fast quotation generator for vehicle hire, point-to-point route tariffs, and fleet transport estimates.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewSendQuotation">
                                <input type="checkbox" id="cfgLeadViewSendQuotation" name="lead_view_show_send_quotation" value="1" <?php echo $isPackageSix ? 'disabled' : 'checked'; ?>>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 5: Convert to Confirmed Sale -->
                        <div class="v3-feature-toggle-row">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-check-circle"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Convert to Confirmed Sale Button</h4>
                                    <p>Display one-click button to directly advance inquiry status to won/confirmed booking.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewConvertSale">
                                <input type="checkbox" id="cfgLeadViewConvertSale" name="lead_view_show_convert_sale" value="1" checked>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 6: Quick Phone Call Button -->
                        <div class="v3-feature-toggle-row">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-phone-call"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Quick Phone Call Button</h4>
                                    <p>Display direct click-to-call mobile button in the lead header action bar.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewCallButton">
                                <input type="checkbox" id="cfgLeadViewCallButton" name="lead_view_show_call_button" value="1" checked>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Toggle 7: Quick WhatsApp Chat Button -->
                        <div class="v3-feature-toggle-row">
                            <div class="v3-feature-toggle-info">
                                <div class="v3-feature-toggle-icon">
                                    <i class="fi fi-rr-comment-alt"></i>
                                </div>
                                <div class="v3-feature-toggle-text">
                                    <h4>Quick WhatsApp Chat Button</h4>
                                    <p>Display direct click-to-WhatsApp chat button in the lead header action bar.</p>
                                </div>
                            </div>
                            <label class="v3-switch-label" for="cfgLeadViewWhatsappButton">
                                <input type="checkbox" id="cfgLeadViewWhatsappButton" name="lead_view_show_whatsapp_button" value="1" checked>
                                <span class="v3-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Action Footer -->
                <div class="v3-form-actions-bar">
                    <a href="/dashboard/setting/" class="v3-btn-secondary">
                        <i class="fi fi-rr-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" id="btnSaveAppConfig" class="v3-btn-primary">
                        <i class="fi fi-rr-disk"></i> <span id="btnSaveAppConfigText">Save App Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Operational Preview Card -->
        <div class="v3-form-sidebar-col">
            <div class="preview-card-sticky">
                <div class="v3-live-preview-card v3-app-config-preview-card">
                    <div class="v3-preview-header">
                        <span class="v3-preview-label">Active App Mode</span>
                        <span class="v3-status-badge status-active"><span class="status-pulse-dot"></span> Active</span>
                    </div>

                    <div class="v3-preview-nature-hero">
                        <div class="v3-preview-nature-icon" id="previewNatureIcon">
                            <i class="fi fi-rr-plane-departure"></i>
                        </div>
                        <div>
                            <h4 id="previewNatureTitle" class="v3-preview-nature-title">Travel Agency &amp; Tour Operator</h4>
                            <p id="previewNatureDesc" class="v3-preview-nature-desc">Optimized for holiday package sales &amp; proposal sharing.</p>
                        </div>
                    </div>

                    <div class="v3-preview-details-list">
                        <div class="v3-preview-detail-row">
                            <span class="v3-preview-detail-label">Lead Action:</span>
                            <span class="v3-nature-preview-chip" id="previewLeadAction">
                                <i class="fi fi-rr-paper-plane"></i> <span>Send Tour Itinerary</span>
                            </span>
                        </div>
                        <div class="v3-preview-detail-row">
                            <span class="v3-preview-detail-label">Currency &amp; Tax:</span>
                            <span class="v3-preview-detail-val" id="previewCurrencyText">INR (₹) • Exclusive</span>
                        </div>
                        <div class="v3-preview-detail-row">
                            <span class="v3-preview-detail-label">Proposal Sharing:</span>
                            <span class="v3-preview-detail-val" id="previewProposalChannel">WhatsApp Direct</span>
                        </div>
                        <div class="v3-preview-detail-row">
                            <span class="v3-preview-detail-label">Quotation Validity:</span>
                            <span class="v3-preview-detail-val" id="previewValidityText">7 Days</span>
                        </div>
                        <div class="v3-preview-detail-row is-hidden" id="previewFleetRow">
                            <span class="v3-preview-detail-label">Tariff Model:</span>
                            <span class="v3-preview-detail-val" id="previewFleetTariff">Per-Kilometer</span>
                        </div>
                        <div class="v3-preview-detail-row">
                            <span class="v3-preview-detail-label">Enabled Lead Fast Tiles:</span>
                        </div>
                        <div class="v3-preview-tags-wrap" id="previewLeadActionsWrap">
                            <span class="v3-preview-tag tag-active" id="tagPrevCallButton">Quick Call</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevWhatsappButton">WhatsApp</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevItinerary">Tour Itinerary</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevQuotation">Send Quotation</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevPaymentLink">Payment Link</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevRecordPayment">Record Payment</span>
                            <span class="v3-preview-tag tag-active" id="tagPrevConvertSale">Convert Sale</span>
                        </div>
                    </div>
                </div>

                <div class="v3-tips-box">
                    <div class="tips-icon"><i class="fi fi-rr-bulb"></i></div>
                    <div class="tips-content">
                        <strong>Workflow Tip</strong>
                        Changes saved here take immediate effect across your workspace. Switching operating nature adapts proposal workflows and lead action tiles automatically.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
