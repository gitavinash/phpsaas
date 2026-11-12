import json
import os

spec = {
    "openapi": "3.1.0",
    "info": {
        "title": "ISW App V3 REST API Suite",
        "version": "3.0.0",
        "description": "Comprehensive, multi-tenant REST API suite for CRM leads, tour packages, smart AI itineraries, visual automations, WhatsApp messaging, fleet logistics, accommodations, ledger accounts, and SaaS tenant management. Fully optimized for both human software engineers and autonomous AI agents.",
        "contact": {
            "name": "ISW Engineering Team",
            "url": "https://blumox.com"
        }
    },
    "servers": [
        {
            "url": "https://{domain}/api/v3",
            "description": "Production/Staging workspace tenant host",
            "variables": {
                "domain": {
                    "default": "crm.example.com",
                    "description": "Tenant custom domain or platform subdomain"
                }
            }
        },
        {
            "url": "/api/v3",
            "description": "Relative URL (Same-Origin SPA requests)"
        }
    ],
    "tags": [
        {"name": "Authentication", "description": "Session authentication, OTP verification, passwordless login, and tenant onboarding"},
        {"name": "Leads & CRM", "description": "Lead lifecycle, pipeline statuses, assignment, tasks, notes, interactions, and payment links"},
        {"name": "Tour Packages", "description": "Package catalog, day-wise itineraries, hotel bindings, transportation, pricing slabs"},
        {"name": "Smart Itineraries", "description": "AI itinerary generation, customized quote presentations, and public customer views"},
        {"name": "Dashboard Analytics", "description": "Executive KPIs, revenue charts, conversion analytics, agent performance"},
        {"name": "Automation Workflows", "description": "Visual flowchart automations, triggers, actions, test simulations, and execution logs"},
        {"name": "WhatsApp CRM", "description": "Multi-device WhatsApp pairing, QR code generation, active chat threads, messaging, media dispatch"},
        {"name": "Transporters & Fleet", "description": "Transporter vendors, vehicle fleet models, quotation engine, rate calculations"},
        {"name": "Accommodations", "description": "Hotel properties, room inventory, room types, and property amenities"},
        {"name": "Media & Gallery", "description": "Package media assets, AI image recommendations, day assignments, and featured photos"},
        {"name": "Policies & Compliance", "description": "Cancellation policies, payment terms, and terms of service management"},
        {"name": "Company & Branches", "description": "Multi-company and branch directory configurations"},
        {"name": "User Management & RBAC", "description": "Staff user directory, access roles, permissions, and KYC verification"},
        {"name": "Accounts & Ledger", "description": "Accounting transaction ledger, payment verification, and audit logs"},
        {"name": "Integrations Hub", "description": "Payment gateways, Google Ads, SMTP, webhooks, and embedded lead web forms"},
        {"name": "SaaS Control Panel", "description": "Superadmin multi-tenant provisioning, app store, themes, and platform invoices"},
        {"name": "Site Settings", "description": "Tenant configurations, banking details, contacts, notifications, and SEO"},
        {"name": "Geography & Utilities", "description": "Geographical master data (cities, states, countries) and URL link shortener"}
    ],
    "components": {
        "securitySchemes": {
            "CookieAuth": {
                "type": "apiKey",
                "in": "cookie",
                "name": "user_data",
                "description": "Base64-encoded user salt key stored in HTTP cookie alongside PHP session identifier"
            },
            "SessionAuth": {
                "type": "apiKey",
                "in": "cookie",
                "name": "PHPSESSID",
                "description": "Standard PHP session cookie holding user_id, site_id, and user_role"
            }
        },
        "schemas": {
            "StandardSuccessResponse": {
                "type": "object",
                "properties": {
                    "status": {"type": "integer", "example": 200},
                    "msg": {"type": "string", "example": "Operation completed successfully."},
                    "data": {"type": "array", "items": {"type": "object"}},
                    "pagination": {
                        "type": "object",
                        "properties": {
                            "current_page": {"type": "integer", "example": 1},
                            "total_pages": {"type": "integer", "example": 5},
                            "total_records": {"type": "integer", "example": 100},
                            "limit": {"type": "integer", "example": 20},
                            "offset": {"type": "integer", "example": 0}
                        }
                    }
                }
            },
            "StandardErrorResponse": {
                "type": "object",
                "properties": {
                    "status": {"type": "string", "example": "error"},
                    "code": {"type": "string", "example": "invalid_parameters"},
                    "message": {"type": "string", "example": "A required parameter was missing or invalid."},
                    "http_code": {"type": "integer", "example": 400}
                }
            },
            "Lead": {
                "type": "object",
                "properties": {
                    "id": {"type": "integer", "example": 42},
                    "encoded_id": {"type": "string", "example": "NDI="},
                    "first_name": {"type": "string", "example": "Rajesh"},
                    "last_name": {"type": "string", "example": "Sharma"},
                    "name": {"type": "string", "example": "Rajesh Sharma"},
                    "mobile": {"type": "string", "example": "+919876543210"},
                    "email": {"type": "string", "example": "rajesh@example.com"},
                    "subject": {"type": "string", "example": "Kashmir 5N/6D Package Enquiry"},
                    "source": {"type": "string", "example": "Google Ads"},
                    "status": {"type": "integer", "description": "1: Fresh, 2: In-Progress, 3: Follow-up, 4/16: Cancelled, 6: Confirmed, 9: Quote Sent", "example": 1},
                    "status_label": {"type": "string", "example": "Fresh"},
                    "agent_id": {"type": "integer", "example": 7},
                    "agent_name": {"type": "string", "example": "Anita Verma"},
                    "created_at": {"type": "string", "format": "date-time", "example": "2026-10-25 14:30:00"},
                    "followup_date": {"type": "string", "format": "date-time", "example": "2026-10-29 11:00:00"}
                }
            },
            "TourPackage": {
                "type": "object",
                "properties": {
                    "id": {"type": "integer", "example": 105},
                    "title": {"type": "string", "example": "Magical Kashmir & Ladakh 6D/5N"},
                    "type": {"type": "string", "enum": ["domestic", "international"], "example": "domestic"},
                    "duration": {"type": "integer", "description": "Duration in days", "example": 6},
                    "theme": {"type": "string", "example": "Honeymoon"},
                    "template": {"type": "string", "enum": ["mit", "standard", "modern", "aleya"], "example": "mit"},
                    "city": {"type": "string", "example": "Srinagar"},
                    "state": {"type": "string", "example": "Jammu and Kashmir"},
                    "country": {"type": "string", "example": "India"},
                    "hotel_type": {"type": "string", "example": "3_star_hotel"},
                    "status": {"type": "integer", "description": "1: Active, 2: Draft, 3: Deleted", "example": 1},
                    "status_label": {"type": "string", "example": "Active"},
                    "price_min": {"type": "number", "example": 18500.00}
                }
            },
            "Workflow": {
                "type": "object",
                "properties": {
                    "id": {"type": "integer", "example": 12},
                    "title": {"type": "string", "example": "Fresh Lead WhatsApp Welcome & Round Robin"},
                    "status": {"type": "integer", "description": "1: Active, 0: Inactive", "example": 1},
                    "trigger_type": {"type": "string", "enum": ["lead_created", "whatsapp_incoming", "lead_stage_changed", "payment_received", "itinerary_viewed", "webhook_received"], "example": "lead_created"},
                    "nodes": {"type": "array", "items": {"type": "object"}},
                    "edges": {"type": "array", "items": {"type": "object"}},
                    "execution_count": {"type": "integer", "example": 1420}
                }
            },
            "WhatsAppMessage": {
                "type": "object",
                "properties": {
                    "id": {"type": "integer", "example": 892},
                    "chat_id": {"type": "integer", "example": 45},
                    "sender_phone": {"type": "string", "example": "919876543210"},
                    "text": {"type": "string", "example": "Hello, thank you for your enquiry! Here is your custom travel proposal."},
                    "direction": {"type": "string", "enum": ["inbound", "outbound"], "example": "outbound"},
                    "type": {"type": "string", "enum": ["text", "image", "document", "template", "audio"], "example": "text"},
                    "media_url": {"type": "string", "example": "/upload/whatsapp/proposal_45.pdf"},
                    "status": {"type": "string", "enum": ["sent", "delivered", "read", "failed"], "example": "delivered"},
                    "timestamp": {"type": "string", "format": "date-time", "example": "2026-10-28 09:30:00"}
                }
            }
        }
    },
    "security": [
        {"CookieAuth": []},
        {"SessionAuth": []}
    ],
    "paths": {}
}

# Helper to populate paths
paths = spec["paths"]

def add_endpoint(path, method, summary, description, tag, params=None, body_schema=None, responses=None, ai_tool_name=None):
    if path not in paths:
        paths[path] = {}
    
    op = {
        "summary": summary,
        "description": description,
        "tags": [tag],
        "operationId": ai_tool_name or f"{method}_{path.replace('/', '_').replace('.', '_')}",
        "responses": responses or {
            "200": {
                "description": "Successful operation",
                "content": {"application/json": {"schema": {"$ref": "#/components/schemas/StandardSuccessResponse"}}}
            },
            "400": {
                "description": "Validation error or invalid request",
                "content": {"application/json": {"schema": {"$ref": "#/components/schemas/StandardErrorResponse"}}}
            },
            "401": {
                "description": "Unauthenticated access",
                "content": {"application/json": {"schema": {"$ref": "#/components/schemas/StandardErrorResponse"}}}
            },
            "403": {
                "description": "Forbidden - Insufficient permissions for user role",
                "content": {"application/json": {"schema": {"$ref": "#/components/schemas/StandardErrorResponse"}}}
            }
        }
    }
    
    if params:
        op["parameters"] = params
    
    if body_schema:
        op["requestBody"] = {
            "required": True,
            "content": {
                "application/json": {"schema": body_schema},
                "application/x-www-form-urlencoded": {"schema": body_schema}
            }
        }
    
    if ai_tool_name:
        op["x-ai-tool-definition"] = {
            "name": ai_tool_name,
            "description": summary
        }
        
    paths[path][method.lower()] = op

# 1. AUTHENTICATION MODULE
add_endpoint(
    "/auth/login.php", "POST", "User Authentication & Session Initialization",
    "Authenticates staff or customer using email, mobile, or username with password. Establishes multi-tenant session and sets user_data salt cookie.",
    "Authentication",
    body_schema={
        "type": "object",
        "required": ["identifier", "password"],
        "properties": {
            "identifier": {"type": "string", "description": "Email address, mobile number, or username", "example": "agent@travelagency.com"},
            "password": {"type": "string", "description": "Plain text account password", "example": "Secret@2026"},
            "redirect_url": {"type": "string", "description": "Target redirection URL post-login", "default": "/dashboard/"}
        }
    },
    ai_tool_name="v3_auth_login"
)

add_endpoint(
    "/auth/send-otp.php", "POST", "Dispatch Email OTP for Passwordless Login",
    "Generates and sends a secure 6-digit one-time password to the specified user email address.",
    "Authentication",
    body_schema={
        "type": "object",
        "required": ["email"],
        "properties": {
            "email": {"type": "string", "format": "email", "example": "staff@travelagency.com"}
        }
    },
    ai_tool_name="v3_auth_send_otp"
)

add_endpoint(
    "/auth/verify-otp.php", "POST", "Verify Email OTP & Authenticate Session",
    "Validates submitted 6-digit OTP code against issued tokens and establishes authenticated user session.",
    "Authentication",
    body_schema={
        "type": "object",
        "required": ["email", "otp"],
        "properties": {
            "email": {"type": "string", "format": "email", "example": "staff@travelagency.com"},
            "otp": {"type": "string", "pattern": "^[0-9]{6}$", "example": "482910"}
        }
    },
    ai_tool_name="v3_auth_verify_otp"
)

add_endpoint(
    "/auth/check-domain.php", "GET", "Real-time Tenant Subdomain Availability Checker",
    "Verifies if a requested agency subdomain or workspace slug is available or already reserved.",
    "Authentication",
    params=[
        {"name": "subdomain", "in": "query", "required": True, "schema": {"type": "string"}, "description": "Desired workspace slug", "example": "bluetravels"}
    ],
    ai_tool_name="v3_auth_check_domain"
)

add_endpoint(
    "/auth/register.php", "POST", "Tenant & Workspace Registration",
    "Creates a brand new travel agency workspace tenant, registers master administrator user, and provisions site access.",
    "Authentication",
    body_schema={
        "type": "object",
        "required": ["company_name", "subdomain", "email", "mobile"],
        "properties": {
            "company_name": {"type": "string", "example": "Blue Sky Holidays"},
            "subdomain": {"type": "string", "example": "bluesky"},
            "first_name": {"type": "string", "example": "Aarav"},
            "last_name": {"type": "string", "example": "Kapoor"},
            "email": {"type": "string", "format": "email", "example": "aarav@bluesky.com"},
            "mobile": {"type": "string", "example": "+919876543210"},
            "app_id": {"type": "string", "example": "crm"}
        }
    },
    ai_tool_name="v3_auth_register_tenant"
)

# 2. LEADS & CRM MODULE
add_endpoint(
    "/leads/list.php", "GET", "Paginated Leads Directory with Filters & KPIs",
    "Fetches paginated enquiries/leads scoped to the active tenant site and user role. Supports date range, status filters, search, and returns aggregate KPI counters.",
    "Leads & CRM",
    params=[
        {"name": "page", "in": "query", "schema": {"type": "integer", "default": 1}, "description": "Page number"},
        {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 20}, "description": "Page size limit (max 100)"},
        {"name": "status", "in": "query", "schema": {"type": "string"}, "description": "Status code (1=fresh, 2=pending, 3=followup, 6=confirmed, 16=cancel, 9=quote sent, or comma-separated list)"},
        {"name": "search", "in": "query", "schema": {"type": "string"}, "description": "Search keyword in customer name, mobile, email, or subject"},
        {"name": "daterange", "in": "query", "schema": {"type": "string"}, "description": "Date range format: 'YYYY-MM-DD - YYYY-MM-DD' or 'all'"},
        {"name": "agentId", "in": "query", "schema": {"type": "string"}, "description": "Filter by assigned agent ID or 'unassigned' (privileged users only)"}
    ],
    ai_tool_name="v3_leads_list"
)

add_endpoint(
    "/leads/view.php", "GET", "Lead Detail Record with Full Timeline & Activities",
    "Retrieves full information for a single enquiry including customer contact, travel requirements, quotes, tasks, notes, and activity history.",
    "Leads & CRM",
    params=[
        {"name": "id", "in": "query", "required": True, "schema": {"type": "string"}, "description": "Lead identifier (numeric ID or base64 encoded string)", "example": "42"}
    ],
    ai_tool_name="v3_leads_view"
)

add_endpoint(
    "/leads/create.php", "POST", "Create New Enquiry / Lead",
    "Creates a new customer lead in the pipeline, automatically assigning site scope and triggering welcome automations.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["first_name", "mobile"],
        "properties": {
            "first_name": {"type": "string", "example": "Priya"},
            "last_name": {"type": "string", "example": "Nair"},
            "mobile": {"type": "string", "example": "+919811223344"},
            "email": {"type": "string", "format": "email", "example": "priya.nair@example.com"},
            "subject": {"type": "string", "example": "Goa 4N/5N Beach Holiday with Family"},
            "source": {"type": "string", "example": "Website Form"},
            "agent_id": {"type": "integer", "description": "Optional assigned agent ID", "example": 5},
            "status": {"type": "integer", "description": "Initial status code (default 1 = fresh)", "default": 1},
            "destination": {"type": "string", "example": "Goa"},
            "adults": {"type": "integer", "default": 2},
            "children": {"type": "integer", "default": 1},
            "travel_date": {"type": "string", "format": "date", "example": "2026-11-15"}
        }
    },
    ai_tool_name="v3_leads_create"
)

add_endpoint(
    "/leads/update.php", "POST", "Update Lead Contact & Travel Information",
    "Modifies personal profile, travel dates, passenger counts, budget, or destination preferences for an existing lead.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["id"],
        "properties": {
            "id": {"type": "string", "description": "Numeric or base64 lead ID", "example": "42"},
            "first_name": {"type": "string", "example": "Priya"},
            "last_name": {"type": "string", "example": "Nair"},
            "email": {"type": "string", "example": "priya.nair@example.com"},
            "mobile": {"type": "string", "example": "+919811223344"},
            "subject": {"type": "string", "example": "Goa Luxury Honeymoon 5N/6D"},
            "destination": {"type": "string", "example": "Goa"}
        }
    },
    ai_tool_name="v3_leads_update"
)

add_endpoint(
    "/leads/update-status.php", "POST", "Advance Lead Pipeline Stage / Status",
    "Updates the stage of an enquiry (e.g. from Fresh to In-Progress, Follow-up, Confirmed, or Cancelled). Enforces workflow triggers.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["id", "status"],
        "properties": {
            "id": {"type": "string", "description": "Lead ID (numeric or base64)", "example": "42"},
            "status": {"type": "integer", "description": "Target status integer (1: Fresh, 2: In-Progress, 3: Followup, 6: Confirmed, 16: Cancel)", "example": 3},
            "reason": {"type": "string", "description": "Optional status transition remark or cancel reason", "example": "Client requested follow-up next Tuesday afternoon"}
        }
    },
    ai_tool_name="v3_leads_update_status"
)

add_endpoint(
    "/leads/assign.php", "POST", "Assign Lead to Sales Agent / Executive",
    "Transfers lead ownership to an agent, updates pipeline status, logs an internal history record, and triggers notification alerts.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["lead_id", "agent_id"],
        "properties": {
            "lead_id": {"type": "string", "example": "42"},
            "agent_id": {"type": "integer", "description": "Target agent user ID", "example": 7},
            "notes": {"type": "string", "example": "High priority honeymoon inquiry, please contact via phone immediately."}
        }
    },
    ai_tool_name="v3_leads_assign_agent"
)

add_endpoint(
    "/leads/add-note.php", "POST", "Append Internal Note or Schedule Follow-up",
    "Adds an internal communication log, customer conversation record, or reminder timestamp to a lead.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["leadId", "msg"],
        "properties": {
            "leadId": {"type": "string", "example": "42"},
            "title": {"type": "string", "default": "Note", "example": "Phone Call Summary"},
            "msg": {"type": "string", "example": "Discussed 4-star hotel options in Candolim. Customer prefers beachfront villa."},
            "followupTime": {"type": "string", "format": "date-time", "example": "2026-10-30 15:30:00"},
            "finalNotes": {"type": "boolean", "default": False}
        }
    },
    ai_tool_name="v3_leads_add_note"
)

add_endpoint(
    "/leads/kanban.php", "GET", "Fetch Kanban Board Columns & Lead Cards",
    "Returns leads aggregated into pipeline stages (Fresh, In-Progress, Follow-up, Confirmed, Cancelled) for drag-and-drop workflow boards.",
    "Leads & CRM",
    params=[
        {"name": "daterange", "in": "query", "schema": {"type": "string"}, "description": "Optional date filter"},
        {"name": "agentId", "in": "query", "schema": {"type": "string"}, "description": "Filter cards by agent ID"}
    ],
    ai_tool_name="v3_leads_kanban"
)

add_endpoint(
    "/leads/payment-link.php", "POST", "Generate Instant Payment Link for Customer",
    "Creates a secure online payment checkout link via integrated payment gateway (Razorpay/Cashfree/PhonePe) tied to the lead enquiry.",
    "Leads & CRM",
    body_schema={
        "type": "object",
        "required": ["lead_id", "amount"],
        "properties": {
            "lead_id": {"type": "string", "example": "42"},
            "amount": {"type": "number", "example": 15000.00},
            "purpose": {"type": "string", "example": "Booking Advance for Kashmir Tour"},
            "gateway": {"type": "string", "enum": ["razorpay", "cashfree", "phonepe", "upi"], "default": "razorpay"}
        }
    },
    ai_tool_name="v3_leads_create_payment_link"
)

# 3. TOUR PACKAGES MODULE
add_endpoint(
    "/packages/list.php", "GET", "List Tour Packages Catalog with Filter & Search",
    "Retrieves tour package catalog records. Supports filtering by destination type (domestic / international), status (active / draft), theme, and keywords.",
    "Tour Packages",
    params=[
        {"name": "type", "in": "query", "schema": {"type": "string", "enum": ["domestic", "international", "all"], "default": "domestic"}},
        {"name": "page", "in": "query", "schema": {"type": "integer", "default": 1}},
        {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 20}},
        {"name": "search", "in": "query", "schema": {"type": "string"}, "description": "Search keyword for package title or destination"}
    ],
    ai_tool_name="v3_packages_list"
)

add_endpoint(
    "/packages/get.php", "GET", "Detailed Tour Package Record with Days & Hotels",
    "Retrieves complete tour package definition including day-by-day itineraries, associated gallery images, inclusions, exclusions, and vehicle assignments.",
    "Tour Packages",
    params=[
        {"name": "id", "in": "query", "required": True, "schema": {"type": "string"}, "description": "Package ID (integer or base64)", "example": "105"}
    ],
    ai_tool_name="v3_packages_get"
)

add_endpoint(
    "/packages/publish.php", "POST", "Create or Update Tour Package Itinerary",
    "Creates a new tour package catalog entry or publishes full modifications to an existing itinerary package.",
    "Tour Packages",
    body_schema={
        "type": "object",
        "required": ["packageName", "duration"],
        "properties": {
            "id": {"type": "string", "description": "Package ID (omit when creating new)", "example": "105"},
            "packageName": {"type": "string", "example": "Enchanting Himachal 6 Days"},
            "travelStyle": {"type": "string", "example": "standard"},
            "tourPackageTemplate": {"type": "string", "enum": ["mit", "standard", "modern", "aleya"], "default": "mit"},
            "packageType": {"type": "string", "enum": ["domestic", "international"], "default": "domestic"},
            "duration": {"type": "integer", "example": 6},
            "hotel": {"type": "string", "default": "3_star_hotel"},
            "outstationVehicle": {"type": "string", "example": "toyota_innova"},
            "localSighseeignVehicle": {"type": "string", "example": "sedan_swift_dzire"},
            "overview": {"type": "string", "example": "Explore scenic valleys, snow mountains, and apple orchards in Shimla and Manali."},
            "inclusions": {"type": "string", "example": "Daily breakfast, Private AC vehicle, 3-star hotel stay, Sightseeing tours"},
            "exclusions": {"type": "string", "example": "Flight tickets, Personal expenses, Entry tickets, Adventure sports"}
        }
    },
    ai_tool_name="v3_packages_save"
)

add_endpoint(
    "/packages/prices.php", "GET", "Fetch Package Price Slabs & Currency Rates",
    "Retrieves adult, child, and single occupancy pricing slabs with seasonal validity dates for a specific tour package.",
    "Tour Packages",
    params=[
        {"name": "id", "in": "query", "required": True, "schema": {"type": "string"}, "description": "Package ID", "example": "105"}
    ],
    ai_tool_name="v3_packages_get_prices"
)

add_endpoint(
    "/packages/prices.php", "POST", "Add, Modify or Delete Package Pricing Slab",
    "Creates a new price slab, updates existing pricing tiers, or deletes a pricing entry.",
    "Tour Packages",
    body_schema={
        "type": "object",
        "required": ["package_id", "selling_adult"],
        "properties": {
            "action": {"type": "string", "enum": ["update", "delete"], "default": "update"},
            "package_id": {"type": "string", "example": "105"},
            "price_id": {"type": "string", "description": "Price slab ID (omit when creating new)", "example": "41"},
            "selling_adult": {"type": "number", "example": 19500.00},
            "selling_child": {"type": "number", "example": 9800.00},
            "date_start": {"type": "string", "format": "date", "example": "2026-11-01"},
            "date_end": {"type": "string", "format": "date", "example": "2027-01-15"}
        }
    },
    ai_tool_name="v3_packages_save_price_slab"
)

# 4. SMART ITINERARIES MODULE
add_endpoint(
    "/itinerary/generate.php", "POST", "AI Smart Itinerary Generation Engine",
    "Generates custom tour itineraries using AI heuristics and catalog templates based on destination, duration, passenger profile, and interests.",
    "Smart Itineraries",
    body_schema={
        "type": "object",
        "required": ["destination", "duration"],
        "properties": {
            "destination": {"type": "string", "example": "Kerala"},
            "duration": {"type": "integer", "description": "Trip duration in days", "example": 5},
            "theme": {"type": "string", "example": "Honeymoon"},
            "lead_id": {"type": "string", "description": "Optional associated lead ID", "example": "42"},
            "travelers_adults": {"type": "integer", "default": 2},
            "travelers_children": {"type": "integer", "default": 0},
            "hotel_rating": {"type": "string", "enum": ["3_star", "4_star", "5_star"], "default": "4_star"}
        }
    },
    ai_tool_name="v3_itinerary_generate"
)

add_endpoint(
    "/itinerary/show.php", "GET", "Render Customer-Facing Itinerary Quotation Presentation",
    "Generates complete HTML/PDF quotation data using configured templates (MIT, Modern, Standard, Aleya) complete with agency branding and banking details.",
    "Smart Itineraries",
    params=[
        {"name": "id", "in": "query", "required": True, "schema": {"type": "string"}, "description": "Itinerary or Package quotation ID", "example": "105"},
        {"name": "lead_id", "in": "query", "schema": {"type": "string"}, "description": "Associated lead ID for personalized client quotation", "example": "42"},
        {"name": "template", "in": "query", "schema": {"type": "string", "enum": ["mit", "standard", "modern", "aleya"], "default": "mit"}}
    ],
    ai_tool_name="v3_itinerary_render_view"
)

# 5. DASHBOARD ANALYTICS MODULE
add_endpoint(
    "/dashboard/overview.php", "GET", "Executive Dashboard Overview & Aggregate Metrics",
    "Provides aggregated business metrics including fresh leads, confirmed conversions, revenue, pending follow-ups, and pipeline charts.",
    "Dashboard Analytics",
    params=[
        {"name": "daterange", "in": "query", "schema": {"type": "string"}, "description": "Date range: 'YYYY-MM-DD - YYYY-MM-DD'"}
    ],
    ai_tool_name="v3_dashboard_overview"
)

add_endpoint(
    "/dashboard/kpis.php", "GET", "Quick Counter KPIs for Top Bar",
    "Returns real-time numerical counters for active leads across all major status stages.",
    "Dashboard Analytics",
    ai_tool_name="v3_dashboard_kpis"
)

# 6. AUTOMATION WORKFLOWS MODULE
add_endpoint(
    "/workflows/", "GET", "Automation Workflows Directory & Canvas State",
    "Lists visual automation workflows or retrieves the complete flowchart graph (nodes, edges, conditions) for a specific automation ID.",
    "Automation Workflows",
    params=[
        {"name": "action", "in": "query", "schema": {"type": "string", "enum": ["list", "get", "templates", "logs", "meta"], "default": "list"}},
        {"name": "id", "in": "query", "schema": {"type": "integer"}, "description": "Workflow ID (required when action=get or action=logs)"},
        {"name": "status", "in": "query", "schema": {"type": "string", "enum": ["all", "active", "inactive"]}}
    ],
    ai_tool_name="v3_workflows_get"
)

add_endpoint(
    "/workflows/", "POST", "Save, Toggle, Duplicate, or Test Run Visual Automations",
    "Persists workflow flowchart canvas nodes/edges, toggles active status, duplicates templates, or triggers dry-run test simulations.",
    "Automation Workflows",
    body_schema={
        "type": "object",
        "required": ["action"],
        "properties": {
            "action": {"type": "string", "enum": ["save", "toggle_status", "duplicate", "delete", "test_run", "install_template"], "example": "save"},
            "id": {"type": "integer", "description": "Workflow ID (omit when creating new)", "example": 12},
            "title": {"type": "string", "example": "WhatsApp Instant Quote Followup"},
            "category": {"type": "string", "example": "sales"},
            "trigger_type": {"type": "string", "enum": ["lead_created", "whatsapp_incoming", "lead_stage_changed", "payment_received", "itinerary_viewed", "webhook_received"], "example": "lead_created"},
            "nodes": {"type": "array", "items": {"type": "object"}},
            "edges": {"type": "array", "items": {"type": "object"}},
            "test_lead_id": {"type": "integer", "description": "Used when action=test_run"}
        }
    },
    ai_tool_name="v3_workflows_action"
)

# 7. WHATSAPP CRM MODULE
add_endpoint(
    "/whatsapp/chats.php", "GET", "List Active WhatsApp Conversations & Recent Messages",
    "Retrieves ongoing WhatsApp threads for the current tenant workspace, enriched with matched lead CRM metadata and unread counts.",
    "WhatsApp CRM",
    params=[
        {"name": "page", "in": "query", "schema": {"type": "integer", "default": 1}},
        {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 30}},
        {"name": "search", "in": "query", "schema": {"type": "string"}, "description": "Search by contact name or phone number"}
    ],
    ai_tool_name="v3_whatsapp_list_chats"
)

add_endpoint(
    "/whatsapp/messages.php", "GET", "Fetch Message History for WhatsApp Thread",
    "Returns message timeline (text, images, PDFs, voice notes, delivery statuses) for a given chat thread.",
    "WhatsApp CRM",
    params=[
        {"name": "chat_id", "in": "query", "required": True, "schema": {"type": "integer"}, "example": 45},
        {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 50}},
        {"name": "before_id", "in": "query", "schema": {"type": "integer"}, "description": "Fetch older messages before this message ID"}
    ],
    ai_tool_name="v3_whatsapp_get_messages"
)

add_endpoint(
    "/whatsapp/messages.php", "POST", "Send Outbound WhatsApp Message / Media / Document",
    "Dispatches outbound WhatsApp communication to a customer phone number or chat thread. Supports plain text, file upload, or base64 media.",
    "WhatsApp CRM",
    body_schema={
        "type": "object",
        "properties": {
            "chat_id": {"type": "integer", "description": "Existing chat ID (if known)", "example": 45},
            "phone": {"type": "string", "description": "Destination phone number with country code", "example": "+919876543210"},
            "text": {"type": "string", "description": "Message body content", "example": "Hi Rajesh, please review your customized Kashmir tour itinerary quotation."},
            "type": {"type": "string", "enum": ["text", "image", "document", "template"], "default": "text"},
            "media_url": {"type": "string", "example": "/upload/whatsapp/quote_105.pdf"},
            "image_base64": {"type": "string", "description": "Optional inline data:image/... base64 string"}
        }
    },
    ai_tool_name="v3_whatsapp_send_message"
)

add_endpoint(
    "/whatsapp/webhook/", "POST", "WhatsApp Baileys / Cloud API Inbound Webhook",
    "Receives and processes incoming WhatsApp messages, updates delivery statuses (sent/delivered/read), and triggers active visual workflows.",
    "WhatsApp CRM",
    body_schema={
        "type": "object",
        "description": "Raw WhatsApp inbound message payload from Baileys or Cloud API engine"
    },
    ai_tool_name="v3_whatsapp_webhook"
)

# 8. TRANSPORTERS & FLEET LOGISTICS
add_endpoint(
    "/transporters/list.php", "GET", "Transporter Vendors Directory",
    "Lists active transport vendors, fleet capabilities, operating states/cities, and contact details.",
    "Transporters & Fleet",
    params=[
        {"name": "search", "in": "query", "schema": {"type": "string"}},
        {"name": "city", "in": "query", "schema": {"type": "string"}}
    ],
    ai_tool_name="v3_transporters_list"
)

add_endpoint(
    "/transporters/quotation.php", "POST", "Generate & Dispatch Fleet Quotation Proposal",
    "Generates transporter vehicle rate calculations, renders proposal PDFs, or sends formatted WhatsApp fleet quotations to customers or vendors.",
    "Transporters & Fleet",
    body_schema={
        "type": "object",
        "properties": {
            "action": {"type": "string", "enum": ["save", "save_pdf", "send"], "default": "save"},
            "quotation_id": {"type": "string", "example": "210"},
            "lead_id": {"type": "string", "example": "42"},
            "vehicle_type": {"type": "string", "example": "Toyota Innova Crysta"},
            "pickup_city": {"type": "string", "example": "Delhi"},
            "drop_city": {"type": "string", "example": "Manali"},
            "total_km": {"type": "number", "example": 1250},
            "rate_per_km": {"type": "number", "example": 18.50},
            "driver_allowance": {"type": "number", "example": 2500},
            "toll_tax_included": {"type": "boolean", "default": True}
        }
    },
    ai_tool_name="v3_transporters_quotation"
)

add_endpoint(
    "/vehicles/list.php", "GET", "Vehicle Fleet Models Catalog",
    "Returns available vehicle fleet models (Sedans, SUVs, Tempo Travellers, Luxury Coaches) with seating capacities and amenities.",
    "Transporters & Fleet",
    ai_tool_name="v3_vehicles_list"
)

# 9. ACCOMMODATIONS & PROPERTIES
add_endpoint(
    "/properties/index.php", "GET", "Hotels & Properties Directory",
    "Fetches hotel properties catalog with location filters, star ratings, and room types.",
    "Accommodations",
    params=[
        {"name": "search", "in": "query", "schema": {"type": "string"}},
        {"name": "location", "in": "query", "schema": {"type": "string"}, "description": "City name filter"},
        {"name": "property_type", "in": "query", "schema": {"type": "string"}}
    ],
    ai_tool_name="v3_properties_list"
)

add_endpoint(
    "/properties/detail.php", "GET", "Property Detail Record & Room Inventories",
    "Returns full hotel specifications including address, room categories, pricing, photos, and amenities.",
    "Accommodations",
    params=[
        {"name": "id", "in": "query", "required": True, "schema": {"type": "string"}, "example": "56"}
    ],
    ai_tool_name="v3_properties_detail"
)

# 10. ACCOUNTS & FINANCIAL LEDGER
add_endpoint(
    "/accounts/transactions.php", "GET", "Financial Ledger & Payment Transactions",
    "Audits incoming payments, gateway settlements, offline receipts, and commission splits. Restricted to Accountant and Administrator roles.",
    "Accounts & Ledger",
    params=[
        {"name": "page", "in": "query", "schema": {"type": "integer", "default": 1}},
        {"name": "limit", "in": "query", "schema": {"type": "integer", "default": 20}},
        {"name": "status", "in": "query", "schema": {"type": "string", "enum": ["all", "pending", "approved", "rejected"]}},
        {"name": "search", "in": "query", "schema": {"type": "string"}}
    ],
    ai_tool_name="v3_accounts_transactions"
)

add_endpoint(
    "/accounts/verify-payment.php", "POST", "Verify & Approve Offline / Gateway Payment",
    "Marks a customer advance or final payment transaction as approved or rejected with financial reconciliation remarks.",
    "Accounts & Ledger",
    body_schema={
        "type": "object",
        "required": ["note_id", "action"],
        "properties": {
            "note_id": {"type": "integer", "description": "Transaction note ID", "example": 89},
            "action": {"type": "string", "enum": ["approve", "reject"], "example": "approve"},
            "reason": {"type": "string", "example": "Bank statement reference matched successfully."}
        }
    },
    ai_tool_name="v3_accounts_verify_payment"
)

# 11. SITE SETTINGS & REDIRECTIONS
add_endpoint(
    "/setting/redirection.php", "GET", "List or Retrieve Tenant URL Redirection Rules",
    "Returns all URL redirection rules (excluding link-shortener entries) for the active tenant site along with KPI counters. Supports HTTP status and keyword filtering or fetching a single rule by ID.",
    "Site Settings",
    params=[
        {"name": "id", "in": "query", "schema": {"type": "string"}, "description": "Optional redirection ID (numeric or base64) to fetch a single record"},
        {"name": "http_status", "in": "query", "schema": {"type": "string", "enum": ["all", "301", "302", "307", "308", "300", "303", "304"], "default": "all"}, "description": "Filter by HTTP redirect status code"},
        {"name": "q", "in": "query", "schema": {"type": "string"}, "description": "Case-insensitive keyword filter across source path, destination URL, or HTTP status"}
    ],
    ai_tool_name="v3_setting_redirection_list"
)

add_endpoint(
    "/setting/redirection.php", "POST", "Create URL Redirection Rule (or Dispatch Update/Delete Action)",
    "Creates a new URL redirection rule and activates the matching isw_link redirect route atomically. Also accepts action='update' or action='delete' for clients limited to POST.",
    "Site Settings",
    body_schema={
        "type": "object",
        "required": ["from_url", "to_url", "http_status"],
        "properties": {
            "action": {"type": "string", "enum": ["create", "update", "delete"], "default": "create"},
            "id": {"type": "string", "description": "Redirection ID (required for update/delete)", "example": "151"},
            "from_url": {"type": "string", "description": "Relative source path on the active site", "example": "/old-page/"},
            "to_url": {"type": "string", "description": "Relative target path (/new-page/) or absolute https:// URL", "example": "/new-page/"},
            "http_status": {"type": "string", "enum": ["301", "302", "307", "308", "300", "303", "304"], "default": "301"}
        }
    },
    ai_tool_name="v3_setting_redirection_create"
)

add_endpoint(
    "/setting/redirection.php", "PUT", "Update Existing URL Redirection Rule",
    "Updates source path, destination URL, and HTTP status code for an existing tenant redirection rule while keeping the linked router route synchronized.",
    "Site Settings",
    body_schema={
        "type": "object",
        "required": ["id", "from_url", "to_url", "http_status"],
        "properties": {
            "id": {"type": "string", "description": "Numeric or base64 redirection ID", "example": "151"},
            "from_url": {"type": "string", "example": "/old-page-renamed/"},
            "to_url": {"type": "string", "example": "/updated-destination/"},
            "http_status": {"type": "string", "enum": ["301", "302", "307", "308", "300", "303", "304"], "example": "301"}
        }
    },
    ai_tool_name="v3_setting_redirection_update"
)

add_endpoint(
    "/setting/redirection.php", "DELETE", "Delete URL Redirection Rule",
    "Deletes a tenant-scoped URL redirection rule and removes its associated redirect route from isw_link.",
    "Site Settings",
    body_schema={
        "type": "object",
        "required": ["id"],
        "properties": {
            "id": {"type": "string", "description": "Numeric or base64 redirection ID", "example": "151"}
        }
    },
    ai_tool_name="v3_setting_redirection_delete"
)

add_endpoint(
    "/appearance/themes.php", "GET", "List Current App Themes & Preview State",
    "Returns active themes from isw_theme matching the current tenant site's application along with disk template verification, live theme metadata, and isolated preview status.",
    "Appearance",
    params=[
        {"name": "q", "in": "query", "schema": {"type": "string"}, "description": "Case-insensitive keyword filter matching theme title, path, description, or version"}
    ],
    ai_tool_name="v3_appearance_themes_list"
)

add_endpoint(
    "/appearance/themes.php", "POST", "Preview, Switch, or Exit Website Theme Preview",
    "Applies a theme live (action='apply'), starts an isolated signed preview session without modifying live visitor rendering (action='preview'), or exits preview mode (action='exit_preview').",
    "Appearance",
    body_schema={
        "type": "object",
        "required": ["action"],
        "properties": {
            "action": {"type": "string", "enum": ["preview", "apply", "exit_preview"], "example": "preview"},
            "theme_id": {"type": "integer", "description": "Target theme ID (required for preview and apply)", "example": 6}
        }
    },
    ai_tool_name="v3_appearance_themes_action"
)

# 12. INTEGRATIONS HUB
add_endpoint(
    "/integration/index.php", "GET", "List Configured Third-Party Integrations & Gateways",
    "Returns status of payment gateways (Razorpay, Cashfree, PhonePe), Google Ads conversion pixels, WhatsApp Web engines, and Webhooks.",
    "Integrations Hub",
    ai_tool_name="v3_integrations_list"
)

# 13. UTILITIES & GEOGRAPHY
add_endpoint(
    "/geo/cities.php", "GET", "Cities Lookup by State",
    "Returns list of active cities within a specified state ID.",
    "Geography & Utilities",
    params=[
        {"name": "state_id", "in": "query", "required": True, "schema": {"type": "integer"}, "example": 15}
    ],
    ai_tool_name="v3_geo_cities"
)

add_endpoint(
    "/shortener/shorten.php", "POST", "Generate Shortened URL",
    "Shortens long customer-facing quotation or itinerary URLs to concise links suitable for WhatsApp and SMS dispatch.",
    "Geography & Utilities",
    body_schema={
        "type": "object",
        "required": ["url"],
        "properties": {
            "url": {"type": "string", "format": "uri", "example": "https://crm.example.com/dashboard/quote/?refId=NDI="}
        }
    },
    ai_tool_name="v3_shortener_shorten"
)

# Write output file
out_path = "docs/api/v3-openapi.json"
with open(out_path, "w", encoding="utf-8") as f:
    json.dump(spec, f, indent=2)

print(f"Generated OpenAPI 3.1.0 specification with {len(spec['paths'])} paths at {out_path}")
