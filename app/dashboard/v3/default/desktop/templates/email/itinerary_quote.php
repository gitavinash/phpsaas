<?php
/**
 * V3 Email Template: Itinerary Proposal Quotation
 * Path: app/dashboard/v3/default/desktop/templates/email/itinerary_quote.php
 * Description: Clean responsive HTML email template for sending tour package proposals to customers.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Custom Tour Proposal</title>
</head>
<body style="font-family: 'Segoe UI', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; color: #1e293b;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <tr>
            <td style="padding: 30px 40px; background: linear-gradient(135deg, {{SITE_THEME_COLOR}}, {{SITE_BG_COLOR}}); text-align: center; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px; font-weight: 700;">{{SITE_NAME}}</h1>
                <p style="margin: 8px 0 0 0; font-size: 15px; opacity: 0.95;">Custom Tour Itinerary & Quotation</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 35px 40px;">
                <h2 style="font-size: 18px; color: #0f172a; margin-top: 0;">Dear {{CUSTOMER_NAME}},</h2>
                <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                    Thank you for reaching out to us. We have crafted a personalized travel itinerary for your upcoming journey to <strong>{{DESTINATION}}</strong>.
                </p>
                
                <table width="100%" cellpadding="12" cellspacing="0" style="margin: 24px 0; background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <tr>
                        <td style="font-size: 14px; color: #64748b;">Tour Package:</td>
                        <td style="font-size: 14px; font-weight: 600; color: #0f172a; text-align: right;">{{PACKAGE_TITLE}}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b;">Travel Dates:</td>
                        <td style="font-size: 14px; font-weight: 600; color: #0f172a; text-align: right;">{{TRAVEL_DATE}} ({{DURATION}})</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b;">Total Price:</td>
                        <td style="font-size: 18px; font-weight: 700; color: #16a34a; text-align: right;">₹ {{TOTAL_PRICE}}</td>
                    </tr>
                </table>

                <div style="text-align: center; margin: 35px 0 20px 0;">
                    <a href="{{PROPOSAL_URL}}" target="_blank" style="display: inline-block; padding: 14px 32px; background-color: {{SITE_THEME_COLOR}}; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        View Detailed Proposal & Download PDF
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 40px; background-color: #f1f5f9; text-align: center; font-size: 12.5px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0;">{{SITE_NAME}} &bull; {{SITE_DOMAIN}}</p>
                <p style="margin: 4px 0 0 0;">Powered by Aleya.in</p>
            </td>
        </tr>
    </table>
</body>
</html>
