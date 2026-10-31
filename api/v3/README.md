# ISW V3 API Engine Directory

Welcome to the **V3 REST API Suite**. This directory contains the complete backend endpoint implementations for the Single Page Application (SPA) dashboard, mobile applications, autonomous AI agents, and visual workflow automations.

## Quick Reference Links
- 📘 **Full Developer & AI Specification:** [v3-api-specification.md](file:///opt/homebrew/var/www/php/isw-app/docs/api/v3-api-specification.md)
- 🤖 **Machine-Readable OpenAPI 3.1.0 JSON:** [v3-openapi.json](file:///opt/homebrew/var/www/php/isw-app/docs/api/v3-openapi.json)
- 🛡️ **Workspace Agent Boundary Rules:** [.agents/rules/v3agent.md](file:///opt/homebrew/var/www/php/isw-app/.agents/rules/v3agent.md)

---

## Directory & Functional Module Map

```
API/v3/
├── accounts/          # Financial ledger, transaction tracking & payment verification
├── amenities/         # Master hotel property amenities
├── auth/              # Passwordless OTP, Google OAuth, tenant onboarding & registration
├── auth.php           # Master router bridge for authentication endpoints
├── cities/            # Master city catalog directory
├── cities.php         # Root bridge for city listings
├── company/           # Multi-branch agency and corporate entities
├── control_panel/     # SaaS superadmin platform management (sites, apps, themes, invoices)
├── countries/         # Global country directory
├── countries.php      # Root bridge for country listings
├── cron/              # Automated scheduled cron tasks (follow-up reminders)
├── dashboard/         # Executive KPIs, revenue analytics, conversion charts & pipeline flow
├── gallery/           # Tour package media library, AI photo suggestions & day assignments
├── geo/               # Geographical lookup services (cities, states)
├── integration/       # Payment gateways (Razorpay, Cashfree, PhonePe), SMTP, Ads & Webhooks
├── itinerary/         # Smart AI itinerary generator, templates & quotation renderer
├── leads/             # Core CRM pipeline (list, view, create, status, assign, tasks, notes, kanban)
├── master/            # Master catalog schema & copy utilities
├── packages/          # Tour packages catalog, day plans, hotel bindings & pricing slabs
├── policy/            # Cancellation policies, terms & conditions, payment terms
├── properties/        # Hotels & resort management, rooms & photo galleries
├── quote/             # Quotation presentation templates
├── reports/           # Staff & executive performance reports
├── reports.php        # Root bridge for report endpoints
├── room-types/        # Room categorization catalog (deluxe, suite, standard)
├── setting/           # Tenant configuration, banking details, contacts, notifications & SEO
├── setting.php        # Master router bridge for setting endpoints
├── shortener/         # Link shortener utility for SMS and WhatsApp dispatch
├── transporter/       # Transporter fleet proposal bridges
├── transporters/      # Transporter vendor management, fleet tariffs & quotations
├── transporters.php   # Root bridge for transporters
├── users/             # User directory, access roles, permissions & KYC verification
├── users.php          # Root bridge for user operations
├── vehicles/          # Master vehicle models catalog & seating capacities
├── vehicles.php       # Root bridge for vehicle operations
├── whatsapp/          # WhatsApp CRM conversations, device pairing, messages & webhooks
└── workflows/   # Visual automation flowchart engine (triggers, nodes, actions, logs)
```

---

## Workspace Architecture Rules

1. **Strict API Layer Isolation:** Never execute direct `mysql_query` or SQL logic inside frontend HTML/JS. All frontend data communications must flow through `/API/v3/`.
2. **Multi-Tenant Scoping:** All operations are strictly filtered by `site_id` (via active session, cookie, or incoming domain).
3. **Dual URL Support:** Both `/api/v3/{module}/{action}.php` and `/api/v3/{module}/{action}/` are valid endpoints.
4. **Flexible Input Handling:** Endpoints automatically parse JSON bodies (`php://input`), `$_POST`, and `$_GET`.
5. **Agnostic Identifiers:** All lead, package, and entity ID parameters accept both integer IDs (`42`) and base64 strings (`NDI=`).
6. **PII Masking:** Users with role `digital_marketing` receive redacted contact information (phone and email are masked).

---

## Common Endpoint Cheat Sheet

| Domain | Action | Method | Path |
| :--- | :--- | :--- | :--- |
| **Auth** | Login | `POST` | `/api/v3/auth/login.php` |
| **Auth** | Send OTP | `POST` | `/api/v3/auth/send-otp.php` |
| **Leads** | List Enquiries | `GET` | `/api/v3/leads/list.php` |
| **Leads** | Create Enquiry | `POST` | `/api/v3/leads/create.php` |
| **Leads** | Update Status | `POST` | `/api/v3/leads/update-status.php` |
| **Leads** | Assign Agent | `POST` | `/api/v3/leads/assign.php` |
| **Packages** | List Catalog | `GET` | `/api/v3/packages/list.php` |
| **Packages** | Publish Package | `POST` | `/api/v3/packages/publish.php` |
| **Itinerary** | AI Generator | `POST` | `/api/v3/itinerary/generate.php` |
| **Dashboard** | Executive KPIs | `GET` | `/api/v3/dashboard/overview.php` |
| **WhatsApp** | List Chats | `GET` | `/api/v3/whatsapp/chats.php` |
| **WhatsApp** | Send Message | `POST` | `/api/v3/whatsapp/messages.php` |
| **Workflows** | Graph Nodes | `GET` / `POST` | `/api/v3/workflows/
| **Accounts** | Audit Ledger | `GET` | `/api/v3/accounts/transactions.php` |
| **Accounts** | Verify Payment | `POST` | `/api/v3/accounts/verify-payment.php` |
| **Utility** | Shorten URL | `POST` | `/api/v3/shortener/shorten.php` |

