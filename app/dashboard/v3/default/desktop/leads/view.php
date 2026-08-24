<?php

if (isset($site_access_type) && $site_access_type === 'digital_marketing') {
    echo "Invalid Access";
    exit();
}

$rawLeadId = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : '');
if (empty($rawLeadId) && isset($urlStructure) && is_array($urlStructure)) {
    $lastSeg = end($urlStructure);
    if (!empty($lastSeg) && $lastSeg !== 'view') {
        $rawLeadId = $lastSeg;
    }
}
?>

<div id="leadViewApp" class="lead-view-wrapper" data-lead-id="<?php echo htmlspecialchars($rawLeadId); ?>">
    <!-- Dynamic Content Container Populated via /api/v3/leads/view.php -->
    <div id="leadViewMainContainer">
        <div class="v3-leads-table-loader">
            <div class="v3-table-spinner">
                <div class="v3-spinner-ring"></div>
                <div class="v3-spinner-inner-dot"></div>
            </div>
            <h4 class="v3-loader-title">Loading Lead Details...</h4>
            <p class="v3-loader-subtitle">Fetching customer profile, timeline, and booking records.</p>
        </div>
    </div>
</div>

<!-- Modal: Edit Lead Profile & Travel Details -->
<div class="v3-modal-overlay" id="modalEditLead">
    <div class="v3-modal-dialog v3-modal-dialog-lg">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title"><i class="fi fi-rr-edit"></i> Edit Lead & Travel Details</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body v3-modal-body-scrollable">
            <input type="hidden" id="editLeadId" value="<?php echo htmlspecialchars($rawLeadId); ?>">
            <h5 class="v3-modal-subheading">Customer Information</h5>
            <div class="v3-form-grid-2">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">First Name *</label>
                    <input type="text" id="editLeadFirstName" class="v3-modal-input" placeholder="e.g. Rohit">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Last Name</label>
                    <input type="text" id="editLeadLastName" class="v3-modal-input" placeholder="e.g. Sharma">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Email Address</label>
                    <input type="email" id="editLeadEmail" class="v3-modal-input" placeholder="e.g. rohit@gmail.com">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Primary Mobile *</label>
                    <input type="text" id="editLeadMobile" class="v3-modal-input" placeholder="e.g. 9876543210">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Alternate Mobile</label>
                    <input type="text" id="editLeadAltMobile" class="v3-modal-input" placeholder="e.g. 9876543211">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Current City / Location</label>
                    <input type="text" id="editLeadCurrentLocation" class="v3-modal-input" placeholder="e.g. Mumbai, India">
                </div>
            </div>

            <h5 class="v3-modal-subheading">Travel Logistics</h5>
            <div class="v3-form-grid-2">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Package / Subject *</label>
                    <input type="text" id="editLeadSubject" class="v3-modal-input" placeholder="e.g. Dubai Tour Package">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Date of Travel</label>
                    <input type="date" id="editLeadTravelDate" class="v3-modal-input">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Trip Duration (Nights)</label>
                    <input type="number" id="editLeadDuration" class="v3-modal-input" placeholder="e.g. 5" min="0">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Pickup Point</label>
                    <input type="text" id="editLeadPickup" class="v3-modal-input" placeholder="e.g. Delhi Airport">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Drop Point</label>
                    <input type="text" id="editLeadDrop" class="v3-modal-input" placeholder="e.g. Delhi Airport">
                </div>
            </div>

            <h5 class="v3-modal-subheading">Guests Breakdown</h5>
            <div class="v3-form-grid-3">
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Adults</label>
                    <input type="number" id="editLeadAdults" class="v3-modal-input" min="1" value="1">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Children</label>
                    <input type="number" id="editLeadChildren" class="v3-modal-input" min="0" value="0">
                </div>
                <div class="v3-modal-form-group">
                    <label class="v3-modal-label">Infants</label>
                    <input type="number" id="editLeadInfants" class="v3-modal-input" min="0" value="0">
                </div>
            </div>

            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Special Requirements / Notes</label>
                <textarea id="editLeadRequirements" class="v3-modal-textarea" placeholder="Add specific requirements (e.g. 4-star hotel, vegetarian food, beach view)..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalEditLead">
                <i class="fi fi-rr-check"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Modal: Full Screen Send & Customize Itinerary Cover -->
<div class="v3-modal-overlay" id="modalSendItinerary">
    <div class="v3-modal-dialog v3-modal-dialog-fullscreen">
        <!-- Sticky Fullscreen Header (Plain & Minimal) -->
        <div class="v3-modal-fullscreen-header">
            <div class="v3-modal-fullscreen-title-wrap">
                <h4 class="v3-modal-title"><i class="fi fi-rr-paper-plane"></i> Search, Customize & Send Tour Itinerary</h4>
                <span class="v3-lead-badge badge-fresh" id="itinModalLeadBadge">Lead Itinerary</span>
            </div>
            <div class="v3-modal-fullscreen-actions">
                <a href="#" class="btn-v3-action btn-outline v3-hidden" id="btnDownloadItineraryPdf" target="_blank">
                    <i class="fi fi-rr-file-pdf"></i> Download PDF
                </a>
                <button type="button" class="btn-v3-action btn-green v3-hidden" id="btnPublishAndSendItinerary">
                    <i class="fi fi-rr-paper-plane"></i> Send Itinerary to Customer
                </button>
                <button type="button" class="v3-modal-close-btn v3-modal-close-trigger" title="Close"><i class="fi fi-rr-cross"></i></button>
            </div>
        </div>

        <!-- Scrollable Fullscreen Canvas Body -->
        <div class="v3-modal-fullscreen-body">
            <div class="v3-itin-canvas-container">
                
                <!-- 1. Search & Template Selector -->
                <div class="v3-itin-section-card">
                    <div class="v3-itin-section-header">
                        <div class="v3-itin-section-title">
                            <i class="fi fi-rr-search"></i>
                            <span>1. Search & Select Itinerary Template</span>
                        </div>
                    </div>
                    <div class="v3-itinerary-search-row">
                        <div class="v3-modal-form-group">
                            <label class="v3-modal-label"><i class="fi fi-rr-marker"></i> Destination / Country / City *</label>
                            <select class="internationalCityAutoComplete form-control v3-select-full" id="itineraryDestinationInput"></select>
                        </div>
                        <div class="v3-modal-form-group">
                            <label class="v3-modal-label"><i class="fi fi-rr-compass"></i> Travel Style / Theme</label>
                            <select class="leads-filter-select v3-select-full" id="itineraryTravelStyle">
                                <option value="">All Travel Styles</option>
                                <option value="adventure">Adventure Travel</option>
                                <option value="cultural">Cultural Travel</option>
                                <option value="luxury">Luxury Travel</option>
                                <option value="honeymoon">Honeymoon Travel</option>
                                <option value="couple">Couple Travel</option>
                                <option value="family">Family Travel</option>
                                <option value="budget">Budget Travel</option>
                                <option value="religious">Religious Travel</option>
                            </select>
                        </div>
                        <div class="v3-itinerary-search-btn-wrap">
                            <button type="button" class="btn-search-packages-cta" id="btnFindItineraries">
                                <i class="fi fi-rr-search"></i> Search Packages
                            </button>
                        </div>
                    </div>

                    <!-- Inline Search Spinner -->
                    <div id="itinerarySearchLoader" class="v3-itinerary-inline-loader v3-hidden">
                        <div class="v3-table-spinner">
                            <div class="v3-spinner-ring"></div>
                        </div>
                        <span id="itinerarySearchLoaderText">Searching matching tour packages...</span>
                    </div>

                    <!-- Inline No Package Found Box -->
                    <div id="itineraryNotFoundBox" class="v3-itinerary-not-found-box v3-hidden">
                        <div class="v3-not-found-left">
                            <div class="v3-not-found-icon-wrap">
                                <i class="fi fi-rr-info"></i>
                            </div>
                            <div>
                                <h5 class="v3-not-found-title">No Predefined Packages Found</h5>
                                <p class="v3-not-found-desc" id="itineraryNotFoundDesc">No package templates match this destination and travel style.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-v3-action btn-outline" id="btnStartCustomItinFromEmpty">
                            <i class="fi fi-rr-plus"></i> Create Custom Itinerary
                        </button>
                    </div>

                    <!-- Package Select Row -->
                    <div id="itineraryResultContainer" class="v3-hidden v3-mt-14">
                        <div class="v3-itinerary-package-select-card">
                            <div class="v3-package-select-header">
                                <label class="v3-modal-label"><i class="fi fi-rr-box"></i> Choose Package Template to Customize *</label>
                                <span class="v3-packages-count-badge" id="itineraryPackageCountBadge">0 Packages</span>
                            </div>
                            <select id="itineraryPackageSelect" class="leads-filter-select v3-select-full"></select>
                        </div>
                    </div>
                </div>

                <!-- 2. Main Customization Form -->
                <div id="itineraryCustomSection" class="v3-hidden v3-itin-custom-canvas">
                    
                    <!-- Section A: Overview & Pricing Configuration -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-settings-sliders"></i>
                                <span>2. General Package & Quotation Pricing</span>
                            </div>
                        </div>
                        
                        <div class="v3-form-grid-2">
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label">Itinerary Title *</label>
                                <input type="text" id="itineraryCustomTitle" class="v3-modal-input" placeholder="e.g. 5 Nights 6 Days Spectacular Dubai Holiday">
                            </div>
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label">Total Quotation Value (₹) *</label>
                                <input type="number" id="itineraryCustomTotalPrice" class="v3-modal-input" placeholder="0">
                            </div>
                        </div>

                        <div class="v3-form-grid-3 v3-mt-12">
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label">Adult Price / person (₹)</label>
                                <input type="number" id="itineraryCustomAdultPrice" class="v3-modal-input" placeholder="0">
                            </div>
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label">Child Price / person (₹)</label>
                                <input type="number" id="itineraryCustomChildPrice" class="v3-modal-input" placeholder="0">
                            </div>
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label">Infant Price / person (₹)</label>
                                <input type="number" id="itineraryCustomInfantPrice" class="v3-modal-input" value="0">
                            </div>
                        </div>

                        <div class="v3-modal-form-group v3-mt-12">
                            <label class="v3-modal-label">Tour Overview & Highlights</label>
                            <textarea id="itineraryCustomOverview" class="v3-modal-textarea" rows="3" placeholder="Brief introduction and trip summary for the traveler..."></textarea>
                        </div>
                    </div>

                    <!-- Section B: Interactive Day-Wise Breakdown -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-calendar"></i>
                                <span>3. Detailed Day-Wise Itinerary, Accommodation & Transport</span>
                            </div>
                            <button type="button" class="btn-v3-action btn-outline" id="btnAddItinDayTop">
                                <i class="fi fi-rr-plus"></i> Add Day
                            </button>
                        </div>

                        <!-- Dynamic Days Stack -->
                        <div class="v3-itin-days-stack" id="itinDaysStack">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <div class="v3-mt-16">
                            <button type="button" class="v3-btn-add-day-full" id="btnAddItinDayBottom">
                                <i class="fi fi-rr-plus"></i> Add Another Day to Itinerary
                            </button>
                        </div>
                    </div>

                    <!-- Section C: Inclusions, Exclusions & Notes -->
                    <div class="v3-itin-section-card">
                        <div class="v3-itin-section-header">
                            <div class="v3-itin-section-title">
                                <i class="fi fi-rr-list-check"></i>
                                <span>4. Inclusions, Exclusions & Special Terms</span>
                            </div>
                        </div>
                        
                        <div class="v3-form-grid-2">
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label"><i class="fi fi-rr-check-circle v3-text-green"></i> What's Included</label>
                                <textarea id="itineraryCustomInclusions" class="v3-modal-textarea" rows="4" placeholder="Hotel accommodation, Daily breakfast, Airport transfers, Sightseeing tours..."></textarea>
                            </div>
                            <div class="v3-modal-form-group">
                                <label class="v3-modal-label"><i class="fi fi-rr-cross-circle v3-text-red"></i> What's Excluded</label>
                                <textarea id="itineraryCustomExclusions" class="v3-modal-textarea" rows="4" placeholder="Flight tickets, Personal expenses, Visa fees, Travel insurance..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Set Follow-Up Reminder -->
<div class="v3-modal-overlay" id="modalSetFollowUp">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title">Schedule Follow-Up Reminder</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Reason / Objective *</label>
                <select id="modalFollowUpReason" class="leads-filter-select v3-select-full">
                    <option value="">Select reason</option>
                    <option value="Interested - Price Discussion">Interested - Price Discussion</option>
                    <option value="Itinerary Customization Request">Itinerary Customization Request</option>
                    <option value="Client Asked to Call Later">Client Asked to Call Later</option>
                    <option value="Mobile Not Reachable / Busy">Mobile Not Reachable / Busy</option>
                    <option value="Payment Confirmation Follow-up">Payment Confirmation Follow-up</option>
                    <option value="General Follow-up">General Follow-up</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Follow-Up Date & Time *</label>
                <input type="datetime-local" id="modalFollowUpTime" class="v3-modal-input" value="<?php echo date('Y-m-d\TH:i', strtotime('+1 day 11:00')); ?>">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Notes / Instructions</label>
                <textarea id="modalFollowUpComment" class="v3-modal-textarea" placeholder="Add specific follow-up points..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalFollowUp">Set Reminder</button>
        </div>
    </div>
</div>

<!-- Modal: Send Payment Link -->
<div class="v3-modal-overlay" id="modalSendPaymentLink">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title">Send Payment Link</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Quotation Price</label>
                <div id="modalQuotationPrice" class="v3-modal-quote-price">₹ 0</div>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Amount to Collect (₹) *</label>
                <input type="number" id="modalPaymentAmountInput" class="v3-modal-input" placeholder="e.g. 15000">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Link Notes</label>
                <textarea id="modalPaymentLinkNote" class="v3-modal-textarea" placeholder="e.g. Advance deposit for booking confirmation"></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-purple" id="btnSubmitModalSendPaymentLink">Send Payment Link</button>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div class="v3-modal-overlay" id="modalUpdatePayment">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title">Log Payment Record</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Amount (₹) *</label>
                <input type="number" id="modalPayAmount" class="v3-modal-input" placeholder="e.g. 25000">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Method *</label>
                <select id="modalPayMethod" class="leads-filter-select v3-select-full">
                    <option value="UPI / QR">UPI / QR Code</option>
                    <option value="Net Banking">Net Banking (NEFT/IMPS)</option>
                    <option value="Credit / Debit Card">Credit / Debit Card</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque / DD">Cheque / Demand Draft</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Payment Date *</label>
                <input type="date" id="modalPayDate" class="v3-modal-input" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Transaction / UTR Reference Number</label>
                <input type="text" id="modalPayRef" class="v3-modal-input" placeholder="e.g. UTR192837465">
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Remarks / Description</label>
                <textarea id="modalPayNotes" class="v3-modal-textarea" placeholder="Payment remarks..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-green" id="btnSubmitModalPayment">Record Payment</button>
        </div>
    </div>
</div>

<!-- Modal: Close Lead -->
<div class="v3-modal-overlay" id="modalCloseLead">
    <div class="v3-modal-dialog">
        <div class="v3-modal-header">
            <h4 class="v3-modal-title">Close or Cancel Lead</h4>
            <button type="button" class="v3-modal-close-btn v3-modal-close-trigger"><i class="fi fi-rr-cross"></i></button>
        </div>
        <div class="v3-modal-body">
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Reason for Closing *</label>
                <select id="modalCloseReason" class="leads-filter-select v3-select-full">
                    <option value="">Select reason</option>
                    <option value="Not interested">Not Interested</option>
                    <option value="Purchased Elsewhere">Purchased Elsewhere / Competitor</option>
                    <option value="Budget Constraint">Budget Constraint</option>
                    <option value="Trip Cancelled / Postponed">Trip Cancelled / Postponed</option>
                    <option value="Unqualified Lead">Unqualified Lead</option>
                    <option value="Fake / Invalid Enquiry">Fake / Invalid Enquiry</option>
                    <option value="Client Not Responding">Client Not Responding</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="v3-modal-form-group">
                <label class="v3-modal-label">Final Conclusion Notes</label>
                <textarea id="modalCloseMsg" class="v3-modal-textarea" placeholder="Provide additional details on why this lead is being closed..."></textarea>
            </div>
        </div>
        <div class="v3-modal-footer">
            <button type="button" class="btn-v3-action btn-outline v3-modal-close-trigger">Cancel</button>
            <button type="button" class="btn-v3-action btn-red" id="btnSubmitModalCloseLead">Close Lead</button>
        </div>
    </div>
</div>

<script src="<?php echo ADMIN_JS ?>lead-view.js?v=<?php echo time(); ?>"></script>