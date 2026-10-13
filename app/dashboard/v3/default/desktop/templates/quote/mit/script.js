/**
 * MIT Commercial Transport Quotation Template Logic
 * Path: app/dashboard/v3/default/desktop/templates/quote/mit/script.js
 * External JavaScript - No inline event handlers
 */

document.addEventListener("DOMContentLoaded", function () {
    var printBtn = document.getElementById("btnPdfPrint");
    if (printBtn) {
        printBtn.addEventListener("click", function () {
            window.print();
        });
    }

    var whatsappBtn = document.getElementById("btnPdfWhatsapp");
    if (whatsappBtn) {
        whatsappBtn.addEventListener("click", function (e) {
            var url = whatsappBtn.getAttribute("data-wa-url");
            if (url) {
                window.open(url, "_blank", "noopener,noreferrer");
            }
        });
    }

    // Expose downloadQuotationPDF globally if iframe or parent invokes it
    window.downloadQuotationPDF = function () {
        window.print();
    };
});
