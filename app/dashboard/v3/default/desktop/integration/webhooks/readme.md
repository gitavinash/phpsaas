# Custom CRM Webhooks & Automation Integration

## Overview
Stream live CRM event notifications to external automation platforms (Zapier, Make.com, Pabbly Connect, n8n) or proprietary accounting and operations backend microservices in real time.

## Technical Specifications
- **Plugin Identifier**: `webhooks`
- **Category**: Developer & API
- **Payload Format**: JSON `application/json`
- **Security Signature**: HMAC SHA-256 (`X-Aleya-Signature`)

## Supported Events
- `lead.created`: Triggered immediately when a new inquiry is received.
- `payment.received`: Triggered when an online or bank remittance is recorded.
- `tour.booked`: Triggered when an itinerary quotation transitions into a confirmed booking.
