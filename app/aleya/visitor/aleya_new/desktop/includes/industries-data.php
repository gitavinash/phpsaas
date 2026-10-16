<?php
/**
 * Aleya SaaS — Centralized Industry Data Configuration
 * Genuine capabilities, operational challenges, savings assumptions, and tailored FAQs
 */

function get_all_industries(): array {
    return [
        'travel-agencies' => [
            'slug' => 'travel-agencies',
            'canonical_slug' => 'travel-agencies',
            'meta_title' => 'Aleya for Travel Agencies — Tour Operator CRM, Itinerary Builder & ERP',
            'meta_desc' => 'Replace scattered spreadsheets. Aleya centralizes inquiries, builds day-wise itineraries, issues booking vouchers, and manages DMC supplier payables.',
            'badge' => 'Travel Agency & Tour Operator ERP',
            'h1' => 'Manage Your Travel Agency Operations With Greater Clarity.',
            'hero_desc' => 'Capture inquiries from Meta & Google Ads instantly, build custom day-wise itineraries in 3 minutes, issue supplier vouchers, and automate WhatsApp client follow-ups with zero dropped leads.',
            'hero_cta_primary' => 'Book a Travel Agency Demo',
            'hero_cta_secondary' => 'View Agency Plans',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_packages.png',
            'metrics' => [
                ['value' => '3 mins', 'label' => 'Avg. Itinerary Creation'],
                ['value' => '100%', 'label' => 'Lead Capture Sync'],
                ['value' => '0', 'label' => 'Spreadsheet Overlaps']
            ],
            'problems' => [
                [
                    'badge' => 'Operational Friction',
                    'title' => 'Hours Lost Building Itineraries in Word & Canva',
                    'desc' => 'Travel agents waste 2 to 3 hours manually formatting sightseeing plans, inclusions, hotel details, and price tables for every individual customer quote.',
                    'consequence' => 'Prospects receive quotes late and book with faster competitors.'
                ],
                [
                    'badge' => 'Revenue Leakage',
                    'title' => 'Inquiries Lost in Scattered WhatsApp Chats',
                    'desc' => 'Lead queries from Facebook, Instagram, Google Ads, and referrals arrive across multiple staff phones without a unified pipeline or assignment system.',
                    'consequence' => '30%+ of inbound inquiries go cold with zero timely follow-up.'
                ],
                [
                    'badge' => 'Administrative Chaos',
                    'title' => 'Supplier Confusion & Voucher Errors',
                    'desc' => 'Hotel bookings, DMC cab allocations, and flight tickets are confirmed across separate email threads, making payment tracking and voucher generation prone to errors.',
                    'consequence' => 'Unclear profit margins per tour and awkward check-in disputes.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'Creating itineraries takes hours per customer.',
                    'capability' => 'Interactive Day-Wise Itinerary Builder',
                    'benefit' => 'Generate beautiful branded itineraries with day-by-day sightseeing and instant PDF/share links in under 3 minutes.'
                ],
                [
                    'problem' => 'Inquiries slip through cracks across staff personal phones.',
                    'capability' => 'Centralized Inbound Pipeline & Auto-Assignment',
                    'benefit' => 'Meta and Google ad leads sync in 0 seconds with instant WhatsApp notification and salesperson assignment.'
                ],
                [
                    'problem' => 'Manual voucher generation causes check-in disputes.',
                    'capability' => '1-Click Voucher & Confirmation Issuance',
                    'benefit' => 'Issue professional branded hotel and cab vouchers automatically once client advance payment is confirmed.'
                ],
                [
                    'problem' => 'Unclear DMC supplier payables and profit margins.',
                    'capability' => 'Supplier Costing & Net Margin Calculator',
                    'benefit' => 'Log supplier net cost vs selling price per package to maintain clear gross margins on every booking.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-route',
                    'title' => 'Day-Wise Itinerary Engine',
                    'desc' => 'Pre-load destination sightseeing, hotel inclusions, flight details, and meal plans into flexible templates ready for instant customization.'
                ],
                [
                    'icon' => 'fa-comments-dollar',
                    'title' => 'Native WhatsApp Quotation Nudges',
                    'desc' => 'Share professional PDF quotes directly on WhatsApp. Set automated reminder alerts before departure dates or payment milestones.'
                ],
                [
                    'icon' => 'fa-hotel',
                    'title' => 'DMC & Supplier Payables',
                    'desc' => 'Keep accurate ledgers of hotel advances, transport vendor balances, and local tour guide payments in one organized screen.'
                ],
                [
                    'icon' => 'fa-passport',
                    'title' => 'Visa Tracking Workflow',
                    'desc' => 'Track client document submissions, embassy appointment slots, and visa approval stages without sticky notes.'
                ],
                [
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'GST Invoicing & Advance Links',
                    'desc' => 'Generate compliant travel invoices with SAC codes, collect 20% advance deposits via UPI, and issue instant receipts.'
                ],
                [
                    'icon' => 'fa-users-cog',
                    'title' => 'Agent Sales Leaderboard',
                    'desc' => 'Track each sales executive’s assigned leads, response speed, quotation conversion rate, and closed booking revenue.'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 6,
                'admin_hours' => 14,
                'hourly_cost' => 350,
                'software_cost' => 3000,
                'plan_cost' => 3999
            ],
            'matrix' => [
                ['dimension' => 'Inquiry Capture', 'before' => 'Manual copy-pasting from Meta Ads to Excel', 'after' => 'Instant 0s sync into centralized sales pipeline'],
                ['dimension' => 'Itinerary Delivery', 'before' => '1.5 to 3 hours formatting Word/Canva files', 'after' => 'Custom day-wise PDF quote in under 3 minutes'],
                ['dimension' => 'Customer Follow-up', 'before' => 'Irregular manual texts from personal phones', 'after' => 'Structured pipeline stages with WhatsApp alerts'],
                ['dimension' => 'Voucher Generation', 'before' => 'Manual typing, risky typos on hotel dates', 'after' => '1-click automated voucher from verified booking'],
                ['dimension' => 'Profit Visibility', 'before' => 'Guesswork at month-end after supplier bills', 'after' => 'Real-time gross margin per travel booking']
            ],
            'workflow' => [
                ['time' => '09:30 AM', 'title' => 'Instant Lead Intake', 'desc' => '3 Meta ad inquiries arrive. Leads are auto-assigned to sales reps with instant WhatsApp context.'],
                ['time' => '11:15 AM', 'title' => 'Quotation Sent', 'desc' => 'Rep selects Kashmir 5N template, adjusts hotel category, and shares branded PDF quote via WhatsApp.'],
                ['time' => '02:45 PM', 'title' => 'Advance Deposit Received', 'desc' => 'Client pays 20% advance via payment link. System automatically issues booking confirmation.'],
                ['time' => '05:30 PM', 'title' => 'Supplier Vouchers Dispatched', 'desc' => '1-click voucher issued to hotel and transporter; copy delivered directly to traveler WhatsApp.']
            ],
            'recommended_plan' => [
                'name' => 'ERP Complete',
                'price_monthly_billed_annually' => '3,999',
                'price_monthly' => '4,999',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Unlimited lead capture from Meta & Google Ads',
                    'Full visual day-wise itinerary builder',
                    'Voucher & booking confirmation automation',
                    'DMC & supplier payable ledger',
                    'Native WhatsApp customer chat & quotes',
                    'GST invoice generation & payment tracking'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Can we customize the look and branding of the itineraries?',
                    'a' => 'Yes. Aleya generates professional itineraries featuring your agency logo, brand colors, contact numbers, and customized terms & conditions.'
                ],
                [
                    'q' => 'How does lead capture connect with our Facebook and Instagram ads?',
                    'a' => 'Aleya integrates directly with Meta Lead Ads. The instant a prospect submits your ad form, the lead is created in your pipeline and your sales executive receives an alert.'
                ],
                [
                    'q' => 'How long does onboarding take for a travel agency?',
                    'a' => 'Most agencies are fully set up within 1 business day. Our team helps you pre-load destination packages, customize voucher formats, and connect your lead sources.'
                ],
                [
                    'q' => 'Is customer and supplier data securely stored?',
                    'a' => 'Yes. Your database and agency records are isolated in your dedicated workspace. We never share or expose agency data or customer contacts.'
                ]
            ]
        ],

        'travel-agents' => [
            'slug' => 'travel-agents',
            'canonical_slug' => 'travel-agents',
            'meta_title' => 'Aleya for Travel Agents — Simple CRM, Quotations & Lead Follow-ups',
            'meta_desc' => 'Designed for independent travel agents and small consulting teams. Track customer inquiries, send professional quotes, and stop losing bookings to missed follow-ups.',
            'badge' => 'Independent Travel Agent CRM',
            'h1' => 'Close More Travel Bookings Without Enterprise Complexity.',
            'hero_desc' => 'A lightweight, affordable travel CRM designed specifically for independent consultants and small travel-selling teams. Keep inquiries organized, send fast quotations, and follow up systematically via WhatsApp.',
            'hero_cta_primary' => 'Get Started with Agent CRM',
            'hero_cta_secondary' => 'See How It Works',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_travel_agents.png',
            'metrics' => [
                ['value' => '₹1,499', 'label' => 'Affordable Starting Plan'],
                ['value' => '10 min', 'label' => 'Fast Setup Time'],
                ['value' => '2x', 'label' => 'Faster Follow-ups']
            ],
            'problems' => [
                [
                    'badge' => 'Lost Opportunities',
                    'title' => 'Missed Follow-Ups While Juggling Inquiries',
                    'desc' => 'Managing 20+ inquiries across personal WhatsApp, phone calls, and scribbled notepads leads to forgotten calls and missed client decisions.',
                    'consequence' => 'Ready-to-buy travelers book elsewhere because you replied 24 hours too late.'
                ],
                [
                    'badge' => 'Software Overkill',
                    'title' => 'Enterprise Travel ERPs Are Too Expensive & Complex',
                    'desc' => 'Traditional travel software demands hefty setup fees, months of training, and complicated menus unsuited for solo or boutique agents.',
                    'consequence' => 'Agents abandon the tool and retreat back to disorganized notebooks.'
                ],
                [
                    'badge' => 'Credibility Gap',
                    'title' => 'Sending Plain Text Messages Looks Unprofessional',
                    'desc' => 'Typing long itinerary texts on WhatsApp with unformatted bullet points makes independent agents look amateurish compared to larger brands.',
                    'consequence' => 'Travelers question agency credibility and demand excessive discounts.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'Juggling client chats leads to missed follow-up calls.',
                    'capability' => 'Clean Visual Inquiry Pipeline',
                    'benefit' => 'See every active prospect organized by stage: Inquired, Quote Sent, Advance Due, Confirmed.'
                ],
                [
                    'problem' => 'Enterprise tools are overly complicated and cost-prohibitive.',
                    'capability' => 'Affordable, Intuitive Interface',
                    'benefit' => 'Get up and running in 10 minutes for ₹1,499/mo without expensive consultants or training.'
                ],
                [
                    'problem' => 'Text message quotes reduce customer trust.',
                    'capability' => 'Clean Quotation & PDF Generator',
                    'benefit' => 'Share structured travel proposals that highlight your professional expertise and justify your service margin.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-filter',
                    'title' => 'Simple Inbound Pipeline',
                    'desc' => 'Capture inquiries from Facebook ads, website forms, or phone inquiries into a drag-and-drop lead board.'
                ],
                [
                    'icon' => 'fa-bell',
                    'title' => 'Smart Follow-up Reminders',
                    'desc' => 'Set follow-up dates and receive notifications so you never forget to check back with a warm prospect.'
                ],
                [
                    'icon' => 'fa-address-card',
                    'title' => 'Customer Travel Profiles',
                    'desc' => 'Save traveler passport details, family dietary preferences, previous trip history, and budget notes in one client card.'
                ],
                [
                    'icon' => 'fa-file-invoice',
                    'title' => 'Fast Quote & Invoice Builder',
                    'desc' => 'Send accurate travel quotes with payment milestones and generate simple GST/retail invoices in seconds.'
                ],
                [
                    'icon' => 'fa-mobile-screen',
                    'title' => 'Mobile-First Accessibility',
                    'desc' => 'Access your client records, call leads, and send quotes whether you are at your desk, home, or traveling.'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 2,
                'admin_hours' => 10,
                'hourly_cost' => 300,
                'software_cost' => 1500,
                'plan_cost' => 1499
            ],
            'matrix' => [
                ['dimension' => 'Inquiry Tracking', 'before' => 'Scribbled in personal diary and WhatsApp stars', 'after' => 'Searchable client cards with complete chat history'],
                ['dimension' => 'Quotation Format', 'before' => 'Messy copy-pasted text messages', 'after' => 'Branded professional quotes with itemized inclusions'],
                ['dimension' => 'Follow-up Discipline', 'before' => 'Dependent on memory, leads routinely slip away', 'after' => 'Automated reminder alerts on follow-up due dates'],
                ['dimension' => 'Cost & Commitment', 'before' => '₹40,000+ enterprise software with annual lock-in', 'after' => 'Transparent ₹1,499/month with no hidden setup fees']
            ],
            'workflow' => [
                ['time' => '10:00 AM', 'title' => 'Check Due Follow-ups', 'desc' => 'Review the 5 leads flagged for follow-up today; 1-click dial to discuss weekend travel plans.'],
                ['time' => '12:30 PM', 'title' => 'Prepare Quick Quote', 'desc' => 'Select a pre-configured Goa package, adjust hotel room type, and send WhatsApp quote.'],
                ['time' => '03:15 PM', 'title' => 'Record Advance Payment', 'desc' => 'Mark booking confirmed as customer transfers advance; trigger automatic receipt.'],
                ['time' => '06:00 PM', 'title' => 'Review Day’s Activity', 'desc' => 'Check active prospects and schedule tomorrow’s callback list in 2 minutes.']
            ],
            'recommended_plan' => [
                'name' => 'CRM Starter',
                'price_monthly_billed_annually' => '1,499',
                'price_monthly' => '1,799',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Multi-channel inquiry capture & lead manager',
                    'Smart follow-up alerts & reminders',
                    'Client profile & travel preference archive',
                    'Quotation sharing & GST billing',
                    'Clean mobile-friendly dashboard',
                    'Email & WhatsApp customer support'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Is this suitable for a single independent consultant?',
                    'a' => 'Absolutely. Aleya CRM Starter was specifically built to give solo agents the professional tools of a large agency without the complexity.'
                ],
                [
                    'q' => 'Can I use Aleya on my mobile browser?',
                    'a' => 'Yes. Aleya is completely responsive and works smoothly on mobile browsers so you can manage inquiries on the go.'
                ],
                [
                    'q' => 'Can I upgrade to the full itinerary and voucher builder later?',
                    'a' => 'Yes. As your agency sales grow, you can upgrade to ERP Complete with 1 click without losing any client records.'
                ]
            ]
        ],

        'transporters' => [
            'slug' => 'transporters',
            'canonical_slug' => 'transporters',
            'meta_title' => 'Aleya for Transporters — Fleet CRM, Trip Quotes & Duty Slips',
            'meta_desc' => 'Manage vehicle rosters, driver allocations, outstation trip costing, WhatsApp vehicle photo quotes, and digital duty slips in one organized system.',
            'badge' => 'Transporter & Fleet Logistics CRM',
            'h1' => 'Organize Vehicles, Duty Slips, and Trip Quotations in One Reliable Place.',
            'hero_desc' => 'Manage your fleet availability, assign certified drivers, send vehicle photo quotes via WhatsApp, issue digital duty slips, and track trip expenses without spreadsheet chaos.',
            'hero_cta_primary' => 'Book Transporter Demo',
            'hero_cta_secondary' => 'Explore Fleet Features',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_transporters.png',
            'metrics' => [
                ['value' => '1 Click', 'label' => 'WhatsApp Photo Quotes'],
                ['value' => '100%', 'label' => 'Duty Slip Delivery'],
                ['value' => '₹0', 'label' => 'Unrecorded Tolls/Bata']
            ],
            'problems' => [
                [
                    'badge' => 'Trip Confusion',
                    'title' => 'Manual Duty Slips & Lost Fuel/Toll Receipts',
                    'desc' => 'Handwritten duty slips get lost, drivers forget to report toll tickets, and fuel expenses are scribbled on random slips of paper.',
                    'consequence' => 'Fleet owners cannot calculate actual trip profit margins and suffer billing disputes.'
                ],
                [
                    'badge' => 'Coordination Breakdown',
                    'title' => 'Endless Phone Calls Between Passenger, Driver & Garage',
                    'desc' => 'Staff spend hours on phone calls relaying pickup locations, flight arrival changes, and driver phone numbers.',
                    'consequence' => 'Miscommunicated pickup times irritate clients and lead to vehicle delays.'
                ],
                [
                    'badge' => 'Payment Delays',
                    'title' => 'Unpaid Balances on Outstation & Corporate Trips',
                    'desc' => 'Without structured advance collection rules and automated balance alerts, corporate clients and retail passengers delay final settlements.',
                    'consequence' => 'Working capital gets trapped in overdue trip balances.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'Paper duty slips get lost or damaged on long trips.',
                    'capability' => 'Digital Duty Slips via WhatsApp',
                    'benefit' => 'Driver receives pickup instructions and passenger contact on WhatsApp; passenger receives vehicle registration and driver credentials.'
                ],
                [
                    'problem' => 'Customers hesitate to book cabs without seeing actual condition.',
                    'capability' => '1-Click WhatsApp Vehicle Photo Gallery',
                    'benefit' => 'Send genuine photos of the exact assigned vehicle (interior, exterior, boot space) directly to the lead in seconds.'
                ],
                [
                    'problem' => 'Unrecorded driver allowances and fuel expenses.',
                    'capability' => 'Trip Expense & Net P&L Logger',
                    'benefit' => 'Log driver bata, tolls, parking, and diesel against trip billing to view net profitability per vehicle.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-car-side',
                    'title' => 'Fleet Roster & Status Matrix',
                    'desc' => 'Track sedans, SUVs, Urbania, and luxury coaches by registration number, seating capacity, and current assignment status.'
                ],
                [
                    'icon' => 'fa-images',
                    'title' => 'Send Vehicle Photos via WhatsApp',
                    'desc' => 'Convert inquiries faster by sending actual vehicle photographs directly into the customer’s WhatsApp with 1 click.'
                ],
                [
                    'icon' => 'fa-calculator',
                    'title' => 'Outstation & Local Quote Calculator',
                    'desc' => 'Calculate trip quotes automatically factoring per-km rates, night charges, state permits, toll estimates, and driver allowance.'
                ],
                [
                    'icon' => 'fa-id-badge',
                    'title' => 'Driver Assignment & Verification',
                    'desc' => 'Maintain driver contact details, license expiration alerts, and police verification status in one registry.'
                ],
                [
                    'icon' => 'fa-receipt',
                    'title' => 'Advance Deposit Collection Links',
                    'desc' => 'Collect 20% to 50% advance before dispatching vehicles, with automated balance recovery reminders upon trip completion.'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 4,
                'admin_hours' => 16,
                'hourly_cost' => 300,
                'software_cost' => 2000,
                'plan_cost' => 2999
            ],
            'matrix' => [
                ['dimension' => 'Quotation Speed', 'before' => 'Mental math, delayed quotes, no vehicle photos', 'after' => 'Calculated rate + verified vehicle photos on WhatsApp in 2 mins'],
                ['dimension' => 'Duty Dispatch', 'before' => 'Carbon copy paper slips given by hand', 'after' => 'Digital duty slip with Google Maps pickup link sent via WhatsApp'],
                ['dimension' => 'Expense Tracking', 'before' => 'Receipts crumpled in driver glovebox', 'after' => 'Logged per trip with fuel, toll, and net profit visibility'],
                ['dimension' => 'Balance Collection', 'before' => 'Awkward driver calls, forgotten balances', 'after' => 'Automatic balance payment link sent upon trip completion']
            ],
            'workflow' => [
                ['time' => '08:00 AM', 'title' => 'Dispatch Duty Slips', 'desc' => 'Review today’s 6 airport pickups; dispatches sent to driver and client WhatsApp with 1 click.'],
                ['time' => '11:30 AM', 'title' => 'Outstation Quote Sent', 'desc' => 'Calculate Delhi-Jaipur 3-day Innova package; send photo gallery and quote link to corporate client.'],
                ['time' => '03:00 PM', 'title' => 'Advance Confirmed', 'desc' => 'Advance payment verified; vehicle DL-1ZB-4092 locked on the fleet roster for weekend.'],
                ['time' => '07:30 PM', 'title' => 'Trip Expense Settlement', 'desc' => 'Returning driver logs toll and fuel totals; net trip profit calculated instantly.']
            ],
            'recommended_plan' => [
                'name' => 'CRM Starter / Fleet Edition',
                'price_monthly_billed_annually' => '1,499',
                'price_monthly' => '1,799',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Vehicle roster & driver assignment registry',
                    'WhatsApp vehicle photo gallery sharing',
                    'Outstation & local trip quotation generator',
                    'Digital duty slip creation & WhatsApp delivery',
                    'Trip expense & net margin calculation',
                    'Advance UPI collection & balance reminders'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Do you provide live GPS tracking hardware?',
                    'a' => 'No. Aleya focuses on business management, inquiry conversion, trip quotations, vehicle photos, and digital duty slips. We do not provide GPS hardware or telematics.'
                ],
                [
                    'q' => 'Can we send car photos to customer WhatsApp directly?',
                    'a' => 'Yes. Aleya includes a dedicated feature allowing you to store vehicle photo galleries and dispatch them directly to customer WhatsApp chats during quotation.'
                ],
                [
                    'q' => 'Does the driver need to download a heavy mobile app?',
                    'a' => 'No. Drivers receive duty slips and pickup details directly on standard WhatsApp, eliminating app download issues and driver onboarding friction.'
                ]
            ]
        ],

        'dms' => [
            'slug' => 'dms',
            'canonical_slug' => 'dms',
            'meta_title' => 'Aleya DMS — Distribution, Multi-Warehouse & Dealer Network Management',
            'meta_desc' => 'Centralize dealer orders, multi-warehouse stock levels, purchase approvals, dispatch manifests, and dealer ledger collections in one scalable SaaS platform.',
            'badge' => 'Distribution Management System (DMS)',
            'h1' => 'Streamline Dealer Reorders, Multi-Warehouse Stock & Wholesale Billing.',
            'hero_desc' => 'Built for manufacturers, wholesalers, stockists, and building material suppliers. Manage dealer sales requests, track multi-warehouse inventory, approve replenishment orders, and recover receivables on time.',
            'hero_cta_primary' => 'Request DMS Walkthrough',
            'hero_cta_secondary' => 'Explore DMS Modules',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_dms.png',
            'metrics' => [
                ['value' => '100%', 'label' => 'Warehouse Stock Visibility'],
                ['value' => '4x', 'label' => 'Faster PO Approval'],
                ['value' => '0', 'label' => 'Disputed Balances']
            ],
            'problems' => [
                [
                    'badge' => 'Stock Confusion',
                    'title' => 'Inventory Discrepancies Across Multiple Warehouses',
                    'desc' => 'Stock counts in central depots, transit hubs, and regional warehouses conflict with what sales agents promise to dealers on the ground.',
                    'consequence' => 'Orders are accepted for out-of-stock items, resulting in emergency cancellations and damaged dealer relationships.'
                ],
                [
                    'badge' => 'Order Bottlenecks',
                    'title' => 'Dealer Orders Trapped in Phone Calls & WhatsApp Groups',
                    'desc' => 'Reorders from 50+ dealers arrive in scattered WhatsApp messages and phone calls without standardized SKU codes or quantity confirmations.',
                    'consequence' => 'Wrong items dispatched, delayed billing, and endless return disputes.'
                ],
                [
                    'badge' => 'Credit Exposure',
                    'title' => 'Overdue Dealer Balances & Unclear Credit Limits',
                    'desc' => 'Dispatches continue to be released to dealers who have exceeded their agreed credit terms because billing and warehouse teams lack real-time ledger visibility.',
                    'consequence' => 'Severe working capital strain and high bad-debt risk.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'Sales agents promise stock that is unavailable in the regional warehouse.',
                    'capability' => 'Real-Time Multi-Warehouse Inventory',
                    'benefit' => 'Check exact stock by warehouse depot, batch number, or lot size before confirming any wholesale order.'
                ],
                [
                    'problem' => 'Dealer reorders sent over phone calls cause wrong dispatches.',
                    'capability' => 'Structured Dealer Sales Request Workflow',
                    'benefit' => 'Dealers or sales reps submit standardized orders with SKU confirmation, pricing approvals, and dispatch verification.'
                ],
                [
                    'problem' => 'Dispatches issued to dealers with overdue outstanding balances.',
                    'capability' => 'Credit Limit Enforcement & Ledger Alerts',
                    'benefit' => 'Automated balance checks and ledger statements sent via WhatsApp before approving new dispatch manifests.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-warehouse',
                    'title' => 'Multi-Warehouse Stock Management',
                    'desc' => 'Manage inventory across main factory plants, regional depots, and transit hubs with real-time stock transfer visibility.'
                ],
                [
                    'icon' => 'fa-cart-flatbed',
                    'title' => 'Dealer Sales Order & Approval Flow',
                    'desc' => 'Standardized order submission workflow: Sales Request → Admin Price Approval → Warehouse Pick & Pack → Dispatch Manifest.'
                ],
                [
                    'icon' => 'fa-rotate-left',
                    'title' => 'Return & Credit Note Handling',
                    'desc' => 'Systematic return request module with inspection verification before issuing replacement dispatches or credit notes.'
                ],
                [
                    'icon' => 'fa-truck-ramp-box',
                    'title' => 'Stock Restock & Supplier POs',
                    'desc' => 'Trigger reorder point alerts when warehouse inventory falls below minimum safety stock, and issue vendor POs.'
                ],
                [
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'B2B GST Invoicing & Ledger Statements',
                    'desc' => 'Issue GST invoices with HSN/SAC codes, e-way bill references, and automated outstanding ledger statements via WhatsApp.'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 8,
                'admin_hours' => 18,
                'hourly_cost' => 400,
                'software_cost' => 5000,
                'plan_cost' => 3999
            ],
            'matrix' => [
                ['dimension' => 'Stock Accuracy', 'before' => 'Daily phone calls to warehouse managers to verify counts', 'after' => 'Real-time multi-depot stock numbers visible to all reps'],
                ['dimension' => 'Order Processing', 'before' => 'Informal WhatsApp texts, handwritten packing slips', 'after' => 'Standardized sales requests with SKU and price approvals'],
                ['dimension' => 'Dealer Collections', 'before' => 'Disputed balances and delayed monthly statements', 'after' => 'Automated ledger balance alerts sent to dealer WhatsApp'],
                ['dimension' => 'Returns & Replacements', 'before' => 'Uncontrolled deductions from dealer payment cheques', 'after' => 'Formal return request with inspection sign-off']
            ],
            'workflow' => [
                ['time' => '09:00 AM', 'title' => 'Review Inbound Dealer Orders', 'desc' => 'Check 14 wholesale dealer reorders submitted overnight; approve pricing and payment terms.'],
                ['time' => '11:30 AM', 'title' => 'Warehouse Dispatch Allocation', 'desc' => 'Assign approved orders to Bhiwandi Central and Gurgaon Hub for picking and packing.'],
                ['time' => '02:00 PM', 'title' => 'Restock Alert Triggered', 'desc' => 'Safety threshold reached for Porcelain tiles; system alerts purchasing team to generate supplier PO.'],
                ['time' => '05:00 PM', 'title' => 'Ledger Statements Delivered', 'desc' => 'Automated WhatsApp payment statements sent to all dealers with balances due over 15 days.']
            ],
            'recommended_plan' => [
                'name' => 'ERP Complete (DMS Edition)',
                'price_monthly_billed_annually' => '3,999',
                'price_monthly' => '4,999',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Multi-warehouse stock & inventory management',
                    'Dealer sales request & approval hierarchy',
                    'Warehouse restock & vendor PO tracking',
                    'Formal return request & credit note module',
                    'B2B GST invoicing with HSN support',
                    'WhatsApp automated ledger balance notifications'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Can we manage multiple warehouse locations with different staff?',
                    'a' => 'Yes. Aleya allows you to set up multiple warehouse depots (e.g. Factory, North Hub, South Hub) and assign specific staff members to manage inventory in their facility.'
                ],
                [
                    'q' => 'How does the dealer order approval workflow function?',
                    'a' => 'When a dealer submits an order, it enters as a "Sales Request." Managers verify pricing and credit limits before approving it for warehouse dispatch, preventing unauthorized shipments.'
                ],
                [
                    'q' => 'Can we export inventory and ledger data to Tally or Excel?',
                    'a' => 'Yes. All sales, order, inventory, and billing reports can be exported to standard CSV/Excel formats for accounting reconciliation.'
                ]
            ]
        ],

        'spa-management' => [
            'slug' => 'spa-management',
            'canonical_slug' => 'spa-management',
            'meta_title' => 'Aleya for Spas & Wellness Centers — Booking, Rooms & Membership CRM',
            'meta_desc' => 'Eliminate no-shows with automated WhatsApp reminders. Coordinate therapist rosters, room allocation, client wellness records, and package billing in one system.',
            'badge' => 'Spa & Wellness Management Platform',
            'h1' => 'Eliminate No-Shows, Streamline Therapist Scheduling & Grow Member Visits.',
            'hero_desc' => 'Unified appointment booking, real-time therapy room allocation, automated WhatsApp pre-session reminders, client treatment histories, and prepaid package membership tracking.',
            'hero_cta_primary' => 'Book Spa Management Demo',
            'hero_cta_secondary' => 'View Spa Features',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_spa.png',
            'metrics' => [
                ['value' => '85%', 'label' => 'Reduction in No-Shows'],
                ['value' => '100%', 'label' => 'Room Overlap Prevention'],
                ['value' => '3x', 'label' => 'Higher Membership Renewals']
            ],
            'problems' => [
                [
                    'badge' => 'Revenue Loss',
                    'title' => 'Costly No-Shows on Booked Therapy Slots',
                    'desc' => 'Clients book appointments via phone or Instagram and forget to show up, leaving therapists idle and treatment rooms empty during peak hours.',
                    'consequence' => '15% to 25% of potential daily salon/spa revenue is permanently lost.'
                ],
                [
                    'badge' => 'Front Desk Chaos',
                    'title' => 'Therapist Double-Booking & Room Conflicts',
                    'desc' => 'Paper appointment registers lead to overlapping room allocations, confusion over therapist availability, and flustered reception staff.',
                    'consequence' => 'Clients arrive on time and are forced to wait, hurting guest satisfaction.'
                ],
                [
                    'badge' => 'Lost Client Retention',
                    'title' => 'Zero Systematic Follow-up for Packages & Re-bookings',
                    'desc' => 'Client treatment histories, massage oil preferences, and prepaid session balances remain trapped in paper cards with zero renewal follow-up.',
                    'consequence' => 'Valuable guests visit once and never return for recurring wellness treatments.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'No-shows leave valuable therapy rooms empty.',
                    'capability' => 'Automated WhatsApp Pre-Session Reminders',
                    'benefit' => 'Automated booking confirmations and 2-hour pre-session reminders with Google Maps directions cut no-shows down to under 3%.'
                ],
                [
                    'problem' => 'Therapist double-booking and room allocation friction.',
                    'capability' => 'Visual Room & Therapist Roster Matrix',
                    'benefit' => 'Real-time schedule grid shows open, occupied, and cleaning slots across all therapy rooms and staff members.'
                ],
                [
                    'problem' => 'Prepaid package credits and renewal dates are lost.',
                    'capability' => 'Membership & Prepaid Session Credit Tracker',
                    'benefit' => 'Track remaining package sessions (e.g., 3/5 used) and trigger automated renewal offers before credits expire.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-calendar-check',
                    'title' => 'Multi-Channel Appointment Booking',
                    'desc' => 'Capture bookings from your website, Instagram DMs, Google profile, and phone calls into a unified calendar.'
                ],
                [
                    'icon' => 'fa-door-open',
                    'title' => 'Room & Therapist Allocation Matrix',
                    'desc' => 'Assign specific treatment rooms (e.g. Couples Suite, Ayurveda Room) and matching certified therapists without overlaps.'
                ],
                [
                    'icon' => 'fa-notes-medical',
                    'title' => 'Client Wellness Notes & Allergies',
                    'desc' => 'Record client preferences (pressure intensity, oil allergies, problem areas) for a personalized, high-end guest experience.'
                ],
                [
                    'icon' => 'fa-gem',
                    'title' => 'Prepaid Packages & Memberships',
                    'desc' => 'Sell multi-session wellness packages, track remaining credits on check-in, and alert guests when renewal is due.'
                ],
                [
                    'icon' => 'fa-qrcode',
                    'title' => 'Front Desk UPI QR & GST Billing',
                    'desc' => 'Generate dynamic UPI QR codes at reception for quick payment settlement, and send digital invoices directly to customer WhatsApp.'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 5,
                'admin_hours' => 12,
                'hourly_cost' => 300,
                'software_cost' => 2500,
                'plan_cost' => 1499
            ],
            'matrix' => [
                ['dimension' => 'Appointment Scheduling', 'before' => 'Messy scribbles on paper desk diary', 'after' => 'Color-coded digital calendar with room & therapist visibility'],
                ['dimension' => 'No-Show Management', 'before' => 'Staff forget to call clients to re-confirm', 'after' => 'Automated WhatsApp reminders sent 2 hours before session'],
                ['dimension' => 'Client Preferences', 'before' => 'Therapist has no idea of guest allergies or notes', 'after' => 'Instant client history card visible prior to treatment'],
                ['dimension' => 'Package Renewals', 'before' => 'Lost punch-cards, disputed remaining sessions', 'after' => 'Accurate digital credit balance with renewal alerts']
            ],
            'workflow' => [
                ['time' => '09:30 AM', 'title' => 'Check Day Roster', 'desc' => 'Review 18 booked appointments; verify room assignments and therapist shifts.'],
                ['time' => '11:00 AM', 'title' => 'Automated Reminder Dispatched', 'desc' => 'WhatsApp reminder delivered to afternoon guests with 1-click confirmation button.'],
                ['time' => '02:30 PM', 'title' => 'Guest Check-In & Treatment', 'desc' => 'Reception verifies 4th session of Swedish Package; therapist reviews shoulder tension note.'],
                ['time' => '06:00 PM', 'title' => 'Digital Billing & Review Request', 'desc' => 'Guest pays via UPI QR; invoice delivered to WhatsApp with request for Google review.']
            ],
            'recommended_plan' => [
                'name' => 'CRM Starter / Spa Edition',
                'price_monthly_billed_annually' => '1,499',
                'price_monthly' => '1,799',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Multi-channel appointment calendar',
                    'Therapist & treatment room allocation',
                    'Zero-no-show automated WhatsApp reminders',
                    'Guest wellness notes & allergy preference cards',
                    'Prepaid package credit & membership tracking',
                    'Front desk UPI QR billing & revenue reports'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Can we manage multiple treatment rooms and therapists?',
                    'a' => 'Yes. Aleya lets you configure all your treatment rooms (Ayurveda, Massage, Facial, Couples Suite) and assign specific therapists to each slot.'
                ],
                [
                    'q' => 'How do automated WhatsApp reminders reduce no-shows?',
                    'a' => 'As soon as an appointment is booked, a confirmation is sent. 2 hours before the session, a friendly reminder with Google Maps directions is delivered, cutting no-shows by up to 85%.'
                ],
                [
                    'q' => 'Can we track multi-session packages like 5 Massages for ₹12,000?',
                    'a' => 'Yes. Aleya includes dedicated membership tracking where each visit automatically deducts 1 credit and notifies the client of their remaining balance.'
                ]
            ]
        ],
        'other-industries' => [
            'slug' => 'other-industries',
            'canonical_slug' => 'other-industries',
            'meta_title' => 'Aleya for All Other Industries — Lead CRM, Product Catalog, Proforma Quotes & Payment Requests',
            'meta_desc' => 'Streamline sales operations for B2B and service businesses. Inbound lead capture, product catalog, proforma quotation builder, payment requests, and optional WhatsApp Web sync.',
            'badge' => 'General Businesses & Service Providers',
            'h1' => 'Streamline Inbound Leads, Proforma Quotes & Payment Requests.',
            'hero_desc' => 'Built specifically for businesses that want a fast, focused commercial sales engine without bloated operational complexity. Capture inbound leads, manage your service catalog, issue GST-compliant proforma quotations, and collect advance payments with optional team WhatsApp Web integration.',
            'hero_cta_primary' => 'Start Free Commercial CRM Trial',
            'hero_cta_secondary' => 'View Commercial Plans',
            'hero_mockup' => '/app/aleya/visitor/aleya_new/desktop/assets/images/actual_dashboard_leads.png',
            'metrics' => [
                ['value' => '0s', 'label' => 'Inbound Lead Sync'],
                ['value' => '2 mins', 'label' => 'Proforma Quote Creation'],
                ['value' => '100%', 'label' => 'Advance Deposit Tracking']
            ],
            'problems' => [
                [
                    'badge' => 'Pipeline Leakage',
                    'title' => 'Inbound Leads Lost Across Staff Personal Inboxes',
                    'desc' => 'Leads from Facebook, Instagram, Google Ads, and website contact forms arrive across scattered personal phones without a central pipeline or auto-assignment.',
                    'consequence' => '35%+ of inquiries go cold before a sales rep even dials the customer.'
                ],
                [
                    'badge' => 'Quotation Delays',
                    'title' => 'Slow & Inconsistent Quotes Built in Excel or Word',
                    'desc' => 'Sales reps spend 30 to 45 minutes manually typing product descriptions, prices, discounts, and GST rates for every individual proforma proposal.',
                    'consequence' => 'Frustrated prospects buy from faster competitors, and calculation mistakes erode margins.'
                ],
                [
                    'badge' => 'Cash Flow Friction',
                    'title' => 'Chasing Advance Payments with WhatsApp Screenshots',
                    'desc' => 'Collecting advance deposits relies on manual bank transfers, messy payment screenshots over chat, and endless back-and-forth reconciliations.',
                    'consequence' => 'Orders get delayed, receipts are forgotten, and accounting visibility remains blurry.'
                ]
            ],
            'mappings' => [
                [
                    'problem' => 'Inbound ad leads and web inquiries slip through cracks across team phones.',
                    'capability' => 'Centralized Multi-Channel Pipeline & Auto-Assignment',
                    'benefit' => 'Meta webhooks, Google Ads, and web inquiries sync in 0 seconds with instant salesperson alerts.'
                ],
                [
                    'problem' => 'Manual quotations in Excel take 30+ minutes with frequent pricing mistakes.',
                    'capability' => '2-Minute Proforma Quotation Builder with GST Rules',
                    'benefit' => 'Pick items from your product catalog, apply tax rules automatically, and generate branded PDFs ready to share.'
                ],
                [
                    'problem' => 'Disorganized payment collection and manual screenshot verification.',
                    'capability' => 'Integrated Payment Requests & Transaction Ledger',
                    'benefit' => 'Send digital payment requests with dynamic UPI QR codes and track advance deposits directly on the deal.'
                ]
            ],
            'features' => [
                [
                    'icon' => 'fa-funnel-dollar',
                    'title' => 'Inbound Lead Management & Pipeline',
                    'desc' => 'Capture leads from Meta ads, Google forms, and website inquiries into a visual pipeline. Auto-assign reps, schedule follow-ups, and track status transitions from Inquiry to Won.'
                ],
                [
                    'icon' => 'fa-boxes',
                    'title' => 'Product & Service Catalog Management',
                    'desc' => 'Standardize your offerings with SKU codes, unit types, descriptions, and pre-set GST brackets (0%, 5%, 12%, 18%, 28%). Ensure sales reps quote accurate, authorized prices every time.'
                ],
                [
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'Proforma Quotation Builder',
                    'desc' => 'Generate professional, GST-compliant proforma quotations in under 2 minutes. Add line items from your catalog, include payment terms, bank details, and export crisp branded PDFs.'
                ],
                [
                    'icon' => 'fa-hand-holding-usd',
                    'title' => 'Payment Requests & Deposit Tracking',
                    'desc' => 'Dispatch instant payment request links and dynamic UPI QR codes. Track partial advance payments, record balance due, and issue automated payment receipts.'
                ],
                [
                    'icon' => 'fa-comments',
                    'title' => 'WhatsApp Web Team Sync (Optional Add-on)',
                    'desc' => 'Equip your sales team with WhatsApp Web directly inside Aleya. Send 1-click quotation links, dispatch payment requests, and log conversations. Paid add-on: ₹500/user/mo (min. 5 users).'
                ]
            ],
            'calc_defaults' => [
                'team_size' => 5,
                'admin_hours' => 14,
                'hourly_cost' => 350,
                'software_cost' => 2500,
                'plan_cost' => 1499
            ],
            'matrix' => [
                ['dimension' => 'Lead Ingestion & Assignment', 'before' => 'Inquiries scattered across personal phones with delayed replies', 'after' => '0-second sync from Meta & Web with automatic sales rep assignment'],
                ['dimension' => 'Quotation & Proposals', 'before' => 'Manual Word or Excel proforma templates taking 30-45 minutes', 'after' => 'Standardized 2-minute proforma quote builder with instant PDF generation'],
                ['dimension' => 'Product & Pricing Control', 'before' => 'Reps quote arbitrary rates, outdated prices, or incorrect GST', 'after' => 'Centralized product & service catalog with locked pricing rules'],
                ['dimension' => 'Payment Collections', 'before' => 'Chasing WhatsApp payment screenshots and manual passbook checks', 'after' => 'Trackable payment request links, dynamic UPI QR, and instant receipts'],
                ['dimension' => 'WhatsApp Team Collaboration', 'before' => 'Client chats locked inside individual sales rep private devices', 'after' => 'Integrated WhatsApp Web add-on with lead timeline synchronizations']
            ],
            'workflow' => [
                ['time' => '09:15 AM', 'title' => 'Review Inbound Pipeline', 'desc' => 'Check new inbound leads captured overnight from Meta webhooks and website forms; verify rep assignments.'],
                ['time' => '10:45 AM', 'title' => 'Client Discovery & Requirement Logging', 'desc' => 'Sales rep connects with prospect, logs discovery notes, and selects appropriate packages from product catalog.'],
                ['time' => '11:30 AM', 'title' => 'Proforma Quotation Dispatched', 'desc' => 'Generate a branded proforma quote with itemized GST breakdown and payment terms in 2 minutes; share via WhatsApp.'],
                ['time' => '03:15 PM', 'title' => 'Payment Request & Advance Confirmation', 'desc' => 'Client approves quotation and pays 50% advance deposit via payment request link; accounts ledger updates in real-time.'],
                ['time' => '05:00 PM', 'title' => 'Automated Receipt & Order Handover', 'desc' => 'Digital payment confirmation receipt is delivered to client WhatsApp; lead transitions automatically to Won / Closed.']
            ],
            'recommended_plan' => [
                'name' => 'Commercial Sales & CRM Starter',
                'price_monthly_billed_annually' => '1,499',
                'price_monthly' => '1,799',
                'users' => 'Up to 10 users included',
                'key_points' => [
                    'Multi-source inbound lead capture & visual pipeline',
                    'Product & service catalog with GST / SAC rules',
                    '2-minute proforma quotation builder with branded PDF',
                    'Payment request links & advance deposit tracking',
                    'Real-time collections ledger & verified receipts',
                    'Optional Add-on: WhatsApp Web sync (+₹500/user/mo, min. 5 users)',
                    'Zero ops clutter: No unwanted tour, fleet, or salon modules'
                ]
            ],
            'faqs' => [
                [
                    'q' => 'Does this plan include industry-specific operations like itineraries, driver logs, or salon rooms?',
                    'a' => 'No. This edition is purposefully designed for general businesses, agencies, distributors, and service companies who only require a focused commercial sales engine — Lead CRM, Product Catalog, Proforma Quotations, and Payment Collections — without paying for or dealing with vertical operational tools.'
                ],
                [
                    'q' => 'How does the WhatsApp Web integration work and what is the pricing?',
                    'a' => 'WhatsApp Web is a premium team add-on priced at ₹500 per user/month, with a minimum requirement of 5 users (₹2,500/month). It lets your sales reps manage WhatsApp Web directly inside Aleya, dispatch 1-click quotation links, send payment requests, and keep complete conversation history logged against customer profiles.'
                ],
                [
                    'q' => 'Can I customize my proforma quotations with our business logo, GSTIN, and bank details?',
                    'a' => 'Yes. You can upload your business logo, specify your GSTIN, address, bank account details, and UPI QR code. Proforma quotations are exported as crisp, print-ready branded PDFs ready to be shared with prospects via WhatsApp or email.'
                ],
                [
                    'q' => 'Can we collect partial advance deposits against quotations?',
                    'a' => 'Yes. You can set custom advance payment amounts or percentages (e.g. 50% advance, 50% on completion). Clients pay via integrated payment request links or dynamic UPI QR codes, and your transaction ledger automatically logs the payment and updates the balance due.'
                ]
            ]
        ]
    ];
}
