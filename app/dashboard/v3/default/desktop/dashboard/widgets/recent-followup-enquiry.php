<?php
// Dashboard V3 Upcoming Followups Widget (API Driven)
?>
<div class="widget-info v3-followups-widget">
    <div class="info-box">
        <p><i class="fi fi-rr-calendar-clock"></i> Upcoming Follow-ups</p>
    </div>
    <div class="v3-table-responsive">
        <table class="no-margin v3-followups-table" id="recentFollowupsTable">
            <thead>
                <tr>
                    <th>Lead Name</th>
                    <th>Mobile</th>
                    <th>Enquiry For</th>
                    <th>Source</th>
                    <th>Follow-up Time</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody id="recentFollowupsTableBody">
                <tr>
                    <td colspan="6" class="table-loading-state" style="text-align: center; padding: 24px; color: #64748b;">
                        <div class="table-loading-spinner" style="margin: 0 auto 8px auto; width: 24px; height: 24px; border: 2px solid #e2e8f0; border-top-color: #0284c7; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                        Loading upcoming follow-ups...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>