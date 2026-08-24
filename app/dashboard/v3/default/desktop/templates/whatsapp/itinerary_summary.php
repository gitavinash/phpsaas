<?php

/**
 * V3 WhatsApp Template: Itinerary Summary Message
 * Path: app/dashboard/v3/default/desktop/templates/whatsapp/itinerary_summary.php
 */
return "*Custom Tour Itinerary from {{SITE_NAME}}* ✨\n\n"
    . "Hello *{{CUSTOMER_NAME}}*,\n"
    . "We have prepared a personalized quotation for your trip to *{{DESTINATION}}*:\n\n"
    . "📍 *Package:* {{PACKAGE_TITLE}}\n"
    . "🗓️ *Duration:* {{DURATION}}\n"
    . "💰 *Total Price:* ₹{{TOTAL_PRICE}}\n\n"
    . "📄 *View Detailed Itinerary & Download PDF:*\n"
    . "{{PROPOSAL_URL}}\n\n"
    . "Feel free to reach out to us if you'd like any customizations!\n"
    . "Best regards,\n*{{SITE_NAME}}*";
