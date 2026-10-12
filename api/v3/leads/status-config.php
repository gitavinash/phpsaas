<?php
/**
 * Aleya Lead Status & Workflow Configuration
 * File: api/v3/leads/status-config.php
 * Supports full 20-stage travel CRM pipeline, customer interest tracking, and response tracking.
 * Fully backward-compatible with all legacy status IDs.
 */

if (!function_exists('getAleyaLeadStatuses')) {
    function getAleyaLeadStatuses()
    {
        return [
            0 => [
                'id'          => 0,
                'code'        => 'new_enquiry',
                'label'       => 'New Enquiry',
                'stage'       => 'inquiry',
                'type'        => 'lifecycle',
                'class'       => 'status-initiate',
                'dot_class'   => 'initiate',
                'badge_class' => 'badge-initiate',
                'icon'        => 'fi-rr-sparkles',
                'color'       => '#0284c7',
                'description' => 'A new travel enquiry has been received'
            ],
            2 => [
                'id'          => 2,
                'code'        => 'assigned',
                'label'       => 'Assigned',
                'stage'       => 'assignment',
                'type'        => 'lifecycle',
                'class'       => 'status-pending',
                'dot_class'   => 'pending',
                'badge_class' => 'badge-pending',
                'icon'        => 'fi-rr-user-time',
                'color'       => '#f59e0b',
                'description' => 'Lead assigned to a sales agent'
            ],
            19 => [
                'id'          => 19,
                'code'        => 'contacted',
                'label'       => 'Contacted',
                'stage'       => 'qualification',
                'type'        => 'lifecycle',
                'class'       => 'status-contacted',
                'dot_class'   => 'contacted',
                'badge_class' => 'badge-contacted',
                'icon'        => 'fi-rr-phone-call',
                'color'       => '#3b82f6',
                'description' => 'Agent has contacted the customer'
            ],
            20 => [
                'id'          => 20,
                'code'        => 'requirement_collected',
                'label'       => 'Requirement Collected',
                'stage'       => 'qualification',
                'type'        => 'lifecycle',
                'class'       => 'status-req-collected',
                'dot_class'   => 'req-collected',
                'badge_class' => 'badge-req-collected',
                'icon'        => 'fi-rr-clipboard-list-check',
                'color'       => '#6366f1',
                'description' => 'Destination, dates, budget, and requirements recorded'
            ],
            21 => [
                'id'          => 21,
                'code'        => 'itinerary_in_progress',
                'label'       => 'Itinerary In Progress',
                'stage'       => 'proposal',
                'type'        => 'lifecycle',
                'class'       => 'status-itin-progress',
                'dot_class'   => 'itin-progress',
                'badge_class' => 'badge-itin-progress',
                'icon'        => 'fi-rr-map-marker-edit',
                'color'       => '#8b5cf6',
                'description' => 'Travel itinerary is being prepared'
            ],
            9 => [
                'id'          => 9,
                'code'        => 'itinerary_sent',
                'label'       => 'Itinerary Sent',
                'stage'       => 'proposal',
                'type'        => 'lifecycle',
                'class'       => 'status-itinerary-sent',
                'dot_class'   => 'itinerary-sent',
                'badge_class' => 'badge-itinerary-sent',
                'icon'        => 'fi-rr-route',
                'color'       => '#2563eb',
                'description' => 'Itinerary shared with the customer'
            ],
            22 => [
                'id'          => 22,
                'code'        => 'quotation_in_progress',
                'label'       => 'Quotation In Progress',
                'stage'       => 'proposal',
                'type'        => 'lifecycle',
                'class'       => 'status-quote-progress',
                'dot_class'   => 'quote-progress',
                'badge_class' => 'badge-quote-progress',
                'icon'        => 'fi-rr-calculator',
                'color'       => '#a855f7',
                'description' => 'Quotation is being prepared'
            ],
            23 => [
                'id'          => 23,
                'code'        => 'quotation_sent',
                'label'       => 'Quotation Sent',
                'stage'       => 'proposal',
                'type'        => 'lifecycle',
                'class'       => 'status-quote-sent',
                'dot_class'   => 'quote-sent',
                'badge_class' => 'badge-quote-sent',
                'icon'        => 'fi-rr-paper-plane',
                'color'       => '#0ea5e9',
                'description' => 'Quotation shared with the customer'
            ],
            24 => [
                'id'          => 24,
                'code'        => 'awaiting_customer_decision',
                'label'       => 'Awaiting Customer Decision',
                'stage'       => 'negotiation',
                'type'        => 'lifecycle',
                'class'       => 'status-awaiting-decision',
                'dot_class'   => 'awaiting-decision',
                'badge_class' => 'badge-awaiting-decision',
                'icon'        => 'fi-rr-hourglass',
                'color'       => '#eab308',
                'description' => 'Customer has not yet made a decision'
            ],
            25 => [
                'id'          => 25,
                'code'        => 'negotiation',
                'label'       => 'Negotiation',
                'stage'       => 'negotiation',
                'type'        => 'lifecycle',
                'class'       => 'status-negotiation',
                'dot_class'   => 'negotiation',
                'badge_class' => 'badge-negotiation',
                'icon'        => 'fi-rr-comments',
                'color'       => '#f97316',
                'description' => 'Customer is discussing price or inclusions'
            ],
            10 => [
                'id'          => 10,
                'code'        => 'quotation_approved',
                'label'       => 'Quotation Approved',
                'stage'       => 'closing',
                'type'        => 'lifecycle',
                'class'       => 'status-quotation-approved',
                'dot_class'   => 'quotation-approved',
                'badge_class' => 'badge-quotation-approved',
                'icon'        => 'fi-rr-badge-check',
                'color'       => '#10b981',
                'description' => 'Customer has accepted the quotation'
            ],
            6 => [
                'id'          => 6,
                'code'        => 'payment_pending',
                'label'       => 'Payment Pending',
                'stage'       => 'closing',
                'type'        => 'lifecycle',
                'class'       => 'status-payment-initiated',
                'dot_class'   => 'payment-initiated',
                'badge_class' => 'badge-payment-initiated',
                'icon'        => 'fi-rr-credit-card',
                'color'       => '#0891b2',
                'description' => 'Awaiting payment'
            ],
            1 => [
                'id'          => 1,
                'code'        => 'converted_to_booking',
                'label'       => 'Converted to Booking',
                'stage'       => 'won',
                'type'        => 'lifecycle',
                'class'       => 'status-success',
                'dot_class'   => 'success',
                'badge_class' => 'badge-success',
                'icon'        => 'fi-rr-check-circle',
                'color'       => '#059669',
                'description' => 'Lead successfully converted to a booking'
            ],
            26 => [
                'id'          => 26,
                'code'        => 'on_hold',
                'label'       => 'On Hold',
                'stage'       => 'hold',
                'type'        => 'nurture',
                'class'       => 'status-on-hold',
                'dot_class'   => 'on-hold',
                'badge_class' => 'badge-on-hold',
                'icon'        => 'fi-rr-pause-circle',
                'color'       => '#78716c',
                'description' => 'Sales process temporarily paused'
            ],
            27 => [
                'id'          => 27,
                'code'        => 'nurture',
                'label'       => 'Nurture',
                'stage'       => 'nurture',
                'type'        => 'nurture',
                'class'       => 'status-nurture',
                'dot_class'   => 'nurture',
                'badge_class' => 'badge-nurture',
                'icon'        => 'fi-rr-seedling',
                'color'       => '#14b8a6',
                'description' => 'Customer may convert at a later date'
            ],
            28 => [
                'id'          => 28,
                'code'        => 'not_interested',
                'label'       => 'Not Interested',
                'stage'       => 'closed_lost',
                'type'        => 'exception',
                'class'       => 'status-not-interested',
                'dot_class'   => 'not-interested',
                'badge_class' => 'badge-not-interested',
                'icon'        => 'fi-rr-thumbs-down',
                'color'       => '#64748b',
                'description' => 'Customer has declined'
            ],
            17 => [
                'id'          => 17,
                'code'        => 'lost',
                'label'       => 'Lost',
                'stage'       => 'closed_lost',
                'type'        => 'exception',
                'class'       => 'status-lost',
                'dot_class'   => 'lost',
                'badge_class' => 'badge-lost',
                'icon'        => 'fi-rr-cross',
                'color'       => '#ef4444',
                'description' => 'Lead did not convert'
            ],
            5 => [
                'id'          => 5,
                'code'        => 'duplicate',
                'label'       => 'Duplicate',
                'stage'       => 'exception',
                'type'        => 'exception',
                'class'       => 'status-duplicate',
                'dot_class'   => 'duplicate',
                'badge_class' => 'badge-duplicate',
                'icon'        => 'fi-rr-copy',
                'color'       => '#94a3b8',
                'description' => 'Duplicate enquiry identified'
            ],
            29 => [
                'id'          => 29,
                'code'        => 'unqualified',
                'label'       => 'Unqualified',
                'stage'       => 'exception',
                'type'        => 'exception',
                'class'       => 'status-unqualified',
                'dot_class'   => 'unqualified',
                'badge_class' => 'badge-unqualified',
                'icon'        => 'fi-rr-ban',
                'color'       => '#a1a1aa',
                'description' => 'Lead does not meet business criteria'
            ],
            16 => [
                'id'          => 16,
                'code'        => 'cancelled',
                'label'       => 'Cancelled',
                'stage'       => 'closed_lost',
                'type'        => 'exception',
                'class'       => 'status-cancelled',
                'dot_class'   => 'cancelled',
                'badge_class' => 'badge-cancelled',
                'icon'        => 'fi-rr-circle-cross',
                'color'       => '#dc2626',
                'description' => 'Enquiry or sales process cancelled'
            ],
            // Legacy & ERP mappings preserved for historical integrity
            3 => [
                'id'          => 3,
                'code'        => 'followup',
                'label'       => 'Follow Up',
                'stage'       => 'negotiation',
                'type'        => 'lifecycle',
                'class'       => 'status-followup',
                'dot_class'   => 'followup',
                'badge_class' => 'badge-followup',
                'icon'        => 'fi-rr-phone-call',
                'color'       => '#2563eb',
                'description' => 'Follow-up note logged or task set (Legacy stage)'
            ],
            4 => [
                'id'          => 4,
                'code'        => 'failed',
                'label'       => 'Closed / Cancelled',
                'stage'       => 'closed_lost',
                'type'        => 'exception',
                'class'       => 'status-failed',
                'dot_class'   => 'failed',
                'badge_class' => 'badge-cancelled',
                'icon'        => 'fi-rr-cross-circle',
                'color'       => '#dc2626',
                'description' => 'Historical closed / cancelled lead'
            ],
            7 => [
                'id'          => 7,
                'code'        => 'sales_request',
                'label'       => 'Sent for Sales Request',
                'stage'       => 'exception',
                'type'        => 'exception',
                'class'       => 'status-sales-request',
                'dot_class'   => 'sales-request',
                'badge_class' => 'badge-sales-request',
                'icon'        => 'fi-rr-paper-plane',
                'color'       => '#db2777',
                'description' => 'Custom inquiry forwarded to specialized sales team'
            ],
            8 => [
                'id'          => 8,
                'code'        => 'upay_done',
                'label'       => 'Upay Done',
                'stage'       => 'won',
                'type'        => 'lifecycle',
                'class'       => 'status-upay-done',
                'dot_class'   => 'upay-done',
                'badge_class' => 'badge-upay-done',
                'icon'        => 'fi-rr-wallet',
                'color'       => '#0d9488',
                'description' => 'Payment settled via UPAY'
            ],
            11 => [
                'id'          => 11,
                'code'        => 'voucher_issued',
                'label'       => 'Voucher Issued',
                'stage'       => 'erp_operations',
                'type'        => 'lifecycle',
                'class'       => 'status-voucher-issued',
                'dot_class'   => 'voucher-issued',
                'badge_class' => 'badge-voucher-issued',
                'icon'        => 'fi-rr-ticket',
                'color'       => '#8b5cf6',
                'description' => 'Hotel, transport, and flight vouchers generated'
            ],
            12 => [
                'id'          => 12,
                'code'        => 'payment_received',
                'label'       => 'Payment Received',
                'stage'       => 'won',
                'type'        => 'lifecycle',
                'class'       => 'status-payment-received',
                'dot_class'   => 'payment-received',
                'badge_class' => 'badge-payment-received',
                'icon'        => 'fi-rr-dollar',
                'color'       => '#16a34a',
                'description' => 'Payment verified and received'
            ],
            13 => [
                'id'          => 13,
                'code'        => 'trip_in_progress',
                'label'       => 'Trip in Progress',
                'stage'       => 'erp_operations',
                'type'        => 'lifecycle',
                'class'       => 'status-trip-progress',
                'dot_class'   => 'trip-progress',
                'badge_class' => 'badge-trip-progress',
                'icon'        => 'fi-rr-plane-departure',
                'color'       => '#4f46e5',
                'description' => 'Travel commences and trip is actively in progress'
            ],
            14 => [
                'id'          => 14,
                'code'        => 'trip_completed',
                'label'       => 'Trip Completed',
                'stage'       => 'erp_operations',
                'type'        => 'lifecycle',
                'class'       => 'status-trip-completed',
                'dot_class'   => 'trip-completed',
                'badge_class' => 'badge-trip-completed',
                'icon'        => 'fi-rr-flag',
                'color'       => '#059669',
                'description' => 'Traveler returned safely, trip concluded'
            ],
            15 => [
                'id'          => 15,
                'code'        => 'closed',
                'label'       => 'Closed',
                'stage'       => 'closed_won',
                'type'        => 'lifecycle',
                'class'       => 'status-closed',
                'dot_class'   => 'closed',
                'badge_class' => 'badge-closed',
                'icon'        => 'fi-rr-lock',
                'color'       => '#475569',
                'description' => 'Final feedback & lead archived'
            ],
            18 => [
                'id'          => 18,
                'code'        => 'payment_refund',
                'label'       => 'Payment Refund',
                'stage'       => 'erp_operations',
                'type'        => 'exception',
                'class'       => 'status-refund',
                'dot_class'   => 'refund',
                'badge_class' => 'badge-refund',
                'icon'        => 'fi-rr-undo',
                'color'       => '#ea580c',
                'description' => 'Payment refunded back to traveler after cancellation'
            ]
        ];
    }
}

if (!function_exists('getLeadStatusMeta')) {
    function getLeadStatusMeta($status)
    {
        $all = getAleyaLeadStatuses();
        $st = (int)$status;
        if (isset($all[$st])) {
            return $all[$st];
        }
        return [
            'id'          => $st,
            'code'        => 'unknown',
            'label'       => 'Status ' . $st,
            'stage'       => 'other',
            'type'        => 'other',
            'class'       => 'status-pending',
            'dot_class'   => 'pending',
            'badge_class' => 'badge-pending',
            'icon'        => 'fi-rr-info',
            'color'       => '#64748b',
            'description' => 'Status ' . $st
        ];
    }
}

if (!function_exists('isClosedLeadStatus')) {
    function isClosedLeadStatus($status)
    {
        $st = (int)$status;
        // Closed / Won or Closed / Lost / Cancelled
        return in_array($st, [1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29], true);
    }
}

if (!function_exists('isActivePipelineStatus')) {
    function isActivePipelineStatus($status)
    {
        return !isClosedLeadStatus($status);
    }
}

// ----------------------------------------------------
// Customer Interest Tracking Configuration & Helpers
// ----------------------------------------------------
if (!function_exists('getAleyaInterestLevels')) {
    function getAleyaInterestLevels()
    {
        return [
            'Hot' => [
                'value'       => 'Hot',
                'label'       => 'Hot',
                'badge_class' => 'badge-interest-hot',
                'pill_class'  => 'pill-interest-hot',
                'icon'        => 'fi-rr-flame',
                'color'       => '#ef4444',
                'bg_color'    => '#fee2e2',
                'border_color'=> '#fca5a5',
                'description' => 'Ready to book, immediate departure, high intent'
            ],
            'Warm' => [
                'value'       => 'Warm',
                'label'       => 'Warm',
                'badge_class' => 'badge-interest-warm',
                'pill_class'  => 'pill-interest-warm',
                'icon'        => 'fi-rr-sparkles',
                'color'       => '#f59e0b',
                'bg_color'    => '#fef3c7',
                'border_color'=> '#fcd34d',
                'description' => 'Interested, actively comparing options and discussing details'
            ],
            'Cold' => [
                'value'       => 'Cold',
                'label'       => 'Cold',
                'badge_class' => 'badge-interest-cold',
                'pill_class'  => 'pill-interest-cold',
                'icon'        => 'fi-rr-snowflake',
                'color'       => '#0284c7',
                'bg_color'    => '#e0f2fe',
                'border_color'=> '#7dd3fc',
                'description' => 'Tentative plans, long horizon, unresponsive to proposals'
            ],
            'Not Interested' => [
                'value'       => 'Not Interested',
                'label'       => 'Not Interested',
                'badge_class' => 'badge-interest-none',
                'pill_class'  => 'pill-interest-none',
                'icon'        => 'fi-rr-cross-circle',
                'color'       => '#64748b',
                'bg_color'    => '#f1f5f9',
                'border_color'=> '#cbd5e1',
                'description' => 'Customer explicitly declined travel or selected another option'
            ],
            'Unknown' => [
                'value'       => 'Unknown',
                'label'       => 'Unknown',
                'badge_class' => 'badge-interest-unknown',
                'pill_class'  => 'pill-interest-unknown',
                'icon'        => 'fi-rr-question',
                'color'       => '#94a3b8',
                'bg_color'    => '#f8fafc',
                'border_color'=> '#e2e8f0',
                'description' => 'Interest level not yet assessed by agent'
            ]
        ];
    }
}

if (!function_exists('getInterestLevelMeta')) {
    function getInterestLevelMeta($level)
    {
        $levels = getAleyaInterestLevels();
        $normalized = ucwords(strtolower(trim((string)$level)));
        if ($normalized === 'Not interested' || $normalized === 'Not_interested') {
            $normalized = 'Not Interested';
        }
        $meta = isset($levels[$normalized]) ? $levels[$normalized] : $levels['Unknown'];
        $icon = !empty($meta['icon']) ? $meta['icon'] : 'fi-rr-question';
        $meta['icon_class'] = 'fi ' . $icon;
        $meta['icon_html']  = '<i class="fi ' . htmlspecialchars($icon) . '"></i>';
        $meta['badge_html'] = '<span class="v3-interest-pill ' . htmlspecialchars($meta['pill_class'] ?? 'pill-interest-unknown') . '"><i class="fi ' . htmlspecialchars($icon) . '"></i> ' . htmlspecialchars($meta['label'] ?? 'Unknown') . '</span>';
        return $meta;
    }
}

// ----------------------------------------------------
// Customer Response Tracking Configuration & Helpers
// ----------------------------------------------------
if (!function_exists('getAleyaResponseStatuses')) {
    function getAleyaResponseStatuses()
    {
        return [
            'No Activity' => [
                'key'         => 'no_activity',
                'label'       => 'No Activity',
                'badge_class' => 'badge-resp-no-activity',
                'icon'        => 'fi-rr-minus-circle',
                'color'       => '#94a3b8',
                'bg_color'    => '#f8fafc',
                'description' => 'No contact attempt logged yet'
            ],
            'Awaiting Customer Response' => [
                'key'         => 'awaiting_response',
                'label'       => 'Awaiting Customer Response',
                'badge_class' => 'badge-resp-awaiting',
                'icon'        => 'fi-rr-hourglass',
                'color'       => '#3b82f6',
                'bg_color'    => '#eff6ff',
                'description' => 'Agent contacted traveler (< 24 hours ago)'
            ],
            'Responded' => [
                'key'         => 'responded',
                'label'       => 'Responded',
                'badge_class' => 'badge-resp-responded',
                'icon'        => 'fi-rr-check-circle',
                'color'       => '#10b981',
                'bg_color'    => '#ecfdf5',
                'description' => 'Customer replied to agent communication'
            ],
            'No Response – 24 Hours' => [
                'key'         => 'no_response_24h',
                'label'       => 'No Response – 24 Hours',
                'badge_class' => 'badge-resp-24h',
                'icon'        => 'fi-rr-time-forward',
                'color'       => '#f59e0b',
                'bg_color'    => '#fef3c7',
                'description' => 'No customer reply for 24+ hours — follow-up due'
            ],
            'No Response – 48 Hours' => [
                'key'         => 'no_response_48h',
                'label'       => 'No Response – 48 Hours',
                'badge_class' => 'badge-resp-48h',
                'icon'        => 'fi-rr-exclamation',
                'color'       => '#ea580c',
                'bg_color'    => '#fff7ed',
                'description' => 'No customer reply for 48+ hours — escalated follow-up'
            ],
            'No Response – 7 Days' => [
                'key'         => 'no_response_7d',
                'label'       => 'No Response – 7 Days',
                'badge_class' => 'badge-resp-7d',
                'icon'        => 'fi-rr-alarm-exclamation',
                'color'       => '#dc2626',
                'bg_color'    => '#fef2f2',
                'description' => 'No customer reply for 7+ days — recommended for nurture or manager review'
            ]
        ];
    }
}

/**
 * Calculates current response indicator and timing context dynamically
 */
if (!function_exists('calculateCustomerResponseStatus')) {
    function calculateCustomerResponseStatus($lastContactedAt, $lastCustomerResponseAt, $leadStatusId = 0)
    {
        $allStatuses = getAleyaResponseStatuses();

        // Closed leads never generate active no-response flags
        if (isClosedLeadStatus($leadStatusId)) {
            $m = $allStatuses['No Activity'];
            return [
                'status'           => 'No Activity',
                'label'            => $m['label'],
                'badge_class'      => $m['badge_class'],
                'description'      => $m['description'],
                'icon'             => $m['icon'],
                'color'            => $m['color'],
                'elapsed_string'   => '',
                'meta'             => $m,
                'is_overdue'       => false,
                'escalation_level' => 0,
                'last_contact_text'=> '',
                'last_response_text'=> '',
                'elapsed_hours'    => 0,
            ];
        }

        $now = time();
        $contactTs = (!empty($lastContactedAt) && $lastContactedAt !== '0000-00-00 00:00:00') ? strtotime($lastContactedAt) : 0;
        $responseTs = (!empty($lastCustomerResponseAt) && $lastCustomerResponseAt !== '0000-00-00 00:00:00') ? strtotime($lastCustomerResponseAt) : 0;

        $lastContactText = '';
        if ($contactTs > 0) {
            $diffC = max(0, $now - $contactTs);
            $lastContactText = formatElapsedDuration($diffC) . ' ago';
        }

        $lastResponseText = '';
        if ($responseTs > 0) {
            $diffR = max(0, $now - $responseTs);
            $lastResponseText = formatElapsedDuration($diffR) . ' ago';
        }

        // Case 1: No contact attempt logged
        if ($contactTs <= 0 && $responseTs <= 0) {
            $m = $allStatuses['No Activity'];
            return [
                'status'            => 'No Activity',
                'label'             => $m['label'],
                'badge_class'       => $m['badge_class'],
                'description'       => $m['description'],
                'icon'              => $m['icon'],
                'color'             => $m['color'],
                'elapsed_string'    => '',
                'meta'              => $m,
                'is_overdue'        => false,
                'escalation_level'  => 0,
                'last_contact_text' => 'Never contacted',
                'last_response_text'=> 'No inbound reply',
                'elapsed_hours'     => 0,
            ];
        }

        // Case 2: Customer replied after last outbound contact attempt
        if ($responseTs > 0 && $responseTs >= $contactTs) {
            $m = $allStatuses['Responded'];
            return [
                'status'            => 'Responded',
                'label'             => $m['label'],
                'badge_class'       => $m['badge_class'],
                'description'       => $m['description'],
                'icon'              => $m['icon'],
                'color'             => $m['color'],
                'elapsed_string'    => $lastResponseText,
                'meta'              => $m,
                'is_overdue'        => false,
                'escalation_level'  => 0,
                'last_contact_text' => $lastContactText ?: 'None',
                'last_response_text'=> $lastResponseText,
                'elapsed_hours'     => round(max(0, $now - $responseTs) / 3600, 1),
            ];
        }

        // Case 3: Outbound contact was made, awaiting customer reply
        $elapsedSec = max(0, $now - $contactTs);
        $elapsedHours = round($elapsedSec / 3600, 1);

        if ($elapsedSec >= 7 * 86400) { // 7+ days
            $stKey = 'No Response – 7 Days';
            $escLevel = 3;
            $isOverdue = true;
        } elseif ($elapsedSec >= 48 * 3600) { // 48+ hours
            $stKey = 'No Response – 48 Hours';
            $escLevel = 2;
            $isOverdue = true;
        } elseif ($elapsedSec >= 24 * 3600) { // 24+ hours
            $stKey = 'No Response – 24 Hours';
            $escLevel = 1;
            $isOverdue = true;
        } else {
            $stKey = 'Awaiting Customer Response';
            $escLevel = 0;
            $isOverdue = false;
        }

        $m = $allStatuses[$stKey];
        return [
            'status'            => $stKey,
            'label'             => $m['label'],
            'badge_class'       => $m['badge_class'],
            'description'       => $m['description'],
            'icon'              => $m['icon'],
            'color'             => $m['color'],
            'elapsed_string'    => $lastContactText,
            'meta'              => $m,
            'is_overdue'        => $isOverdue,
            'escalation_level'  => $escLevel,
            'last_contact_text' => $lastContactText,
            'last_response_text'=> $lastResponseText ?: 'None',
            'elapsed_hours'     => $elapsedHours,
        ];
    }
}

if (!function_exists('formatElapsedDuration')) {
    function formatElapsedDuration($seconds)
    {
        if ($seconds < 60) return 'Just now';
        if ($seconds < 3600) return floor($seconds / 60) . 'm';
        if ($seconds < 86400) return floor($seconds / 3600) . 'h';
        if ($seconds < 604800) return floor($seconds / 86400) . 'd';
        return floor($seconds / 604800) . 'w';
    }
}
