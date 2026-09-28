<?php
/**
 * Aleya Lead Status Configuration & Helper
 * File: api/v3/leads/status-config.php
 * Based on official Aleya Lead Lifecycle Flow specification in readme.md
 */

if (!function_exists('getAleyaLeadStatuses')) {
    function getAleyaLeadStatuses()
    {
        return [
            0 => [
                'id'          => 0,
                'code'        => 'initiate',
                'label'       => 'Initiate',
                'type'        => 'lifecycle',
                'class'       => 'status-initiate',
                'dot_class'   => 'initiate',
                'badge_class' => 'badge-initiate',
                'icon'        => 'fi-rr-sparkles',
                'color'       => '#0284c7',
                'description' => 'Newly initiated lead inquiry before agent assignment'
            ],
            1 => [
                'id'          => 1,
                'code'        => 'success',
                'label'       => 'Success',
                'type'        => 'lifecycle',
                'class'       => 'status-success',
                'dot_class'   => 'success',
                'badge_class' => 'badge-success',
                'icon'        => 'fi-rr-check-circle',
                'color'       => '#059669',
                'description' => 'Successfully converted booking'
            ],
            2 => [
                'id'          => 2,
                'code'        => 'pending_assigned',
                'label'       => 'Pending (Assigned to Agent)',
                'type'        => 'lifecycle',
                'class'       => 'status-pending',
                'dot_class'   => 'pending',
                'badge_class' => 'badge-pending',
                'icon'        => 'fi-rr-user-time',
                'color'       => '#d97706',
                'description' => 'Pending because it is assigned to an agent'
            ],
            3 => [
                'id'          => 3,
                'code'        => 'followup',
                'label'       => 'Follow-up',
                'type'        => 'lifecycle',
                'class'       => 'status-followup',
                'dot_class'   => 'followup',
                'badge_class' => 'badge-followup',
                'icon'        => 'fi-rr-phone-call',
                'color'       => '#2563eb',
                'description' => 'Negotiation, date/destination adjustments'
            ],
            4 => [
                'id'          => 4,
                'code'        => 'failed',
                'label'       => 'Failed',
                'type'        => 'exception',
                'class'       => 'status-failed',
                'dot_class'   => 'failed',
                'badge_class' => 'badge-failed',
                'icon'        => 'fi-rr-cross-circle',
                'color'       => '#e11d48',
                'description' => 'Ingestion failure or uncontactable / invalid lead inquiry'
            ],
            5 => [
                'id'          => 5,
                'code'        => 'duplicate',
                'label'       => 'Duplicate',
                'type'        => 'exception',
                'class'       => 'status-duplicate',
                'dot_class'   => 'duplicate',
                'badge_class' => 'badge-duplicate',
                'icon'        => 'fi-rr-copy',
                'color'       => '#64748b',
                'description' => 'Repeated inquiry submitted by the same client'
            ],
            6 => [
                'id'          => 6,
                'code'        => 'payment_initiated',
                'label'       => 'Payment Initiated',
                'type'        => 'lifecycle',
                'class'       => 'status-payment-initiated',
                'dot_class'   => 'payment-initiated',
                'badge_class' => 'badge-payment-initiated',
                'icon'        => 'fi-rr-credit-card',
                'color'       => '#0891b2',
                'description' => 'Payment link generated & sent to client'
            ],
            7 => [
                'id'          => 7,
                'code'        => 'sales_request',
                'label'       => 'Sent for Sales Request',
                'type'        => 'exception',
                'class'       => 'status-sales-request',
                'dot_class'   => 'sales-request',
                'badge_class' => 'badge-sales-request',
                'icon'        => 'fi-rr-paper-plane',
                'color'       => '#db2777',
                'description' => 'Custom/complex inquiry forwarded to specialized sales team'
            ],
            8 => [
                'id'          => 8,
                'code'        => 'upay_done',
                'label'       => 'Upay Done',
                'type'        => 'lifecycle',
                'class'       => 'status-upay-done',
                'dot_class'   => 'upay-done',
                'badge_class' => 'badge-upay-done',
                'icon'        => 'fi-rr-wallet',
                'color'       => '#0d9488',
                'description' => 'Payment settled via UPAY'
            ],
            9 => [
                'id'          => 9,
                'code'        => 'itinerary_sent',
                'label'       => 'Itinerary Sent',
                'type'        => 'lifecycle',
                'class'       => 'status-itinerary-sent',
                'dot_class'   => 'itinerary-sent',
                'badge_class' => 'badge-itinerary-sent',
                'icon'        => 'fi-rr-route',
                'color'       => '#3b82f6',
                'description' => 'Itinerary created and dispatched to traveler'
            ],
            10 => [
                'id'          => 10,
                'code'        => 'quotation_approved',
                'label'       => 'Quotation Approved',
                'type'        => 'lifecycle',
                'class'       => 'status-quotation-approved',
                'dot_class'   => 'quotation-approved',
                'badge_class' => 'badge-quotation-approved',
                'icon'        => 'fi-rr-badge-check',
                'color'       => '#10b981',
                'description' => 'Quotation approved by traveler'
            ],
            11 => [
                'id'          => 11,
                'code'        => 'voucher_issued',
                'label'       => 'Voucher Issued',
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
                'type'        => 'lifecycle',
                'class'       => 'status-closed',
                'dot_class'   => 'closed',
                'badge_class' => 'badge-closed',
                'icon'        => 'fi-rr-lock',
                'color'       => '#475569',
                'description' => 'Final feedback & lead archived'
            ],
            16 => [
                'id'          => 16,
                'code'        => 'cancelled',
                'label'       => 'Cancelled',
                'type'        => 'exception',
                'class'       => 'status-cancelled',
                'dot_class'   => 'cancelled',
                'badge_class' => 'badge-cancelled',
                'icon'        => 'fi-rr-ban',
                'color'       => '#dc2626',
                'description' => 'Booking or trip cancelled by traveler or agency'
            ],
            17 => [
                'id'          => 17,
                'code'        => 'lost',
                'label'       => 'Lost',
                'type'        => 'exception',
                'class'       => 'status-lost',
                'dot_class'   => 'lost',
                'badge_class' => 'badge-lost',
                'icon'        => 'fi-rr-cross',
                'color'       => '#94a3b8',
                'description' => 'Traveler dropped out due to budget or selected a competitor'
            ],
            18 => [
                'id'          => 18,
                'code'        => 'payment_refund',
                'label'       => 'Payment Refund',
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
