(function () {
    'use strict';

    var printBtn = document.getElementById('crmPrintNowBtn');
    var closeBtn = document.getElementById('crmPrintCloseBtn');

    if (printBtn) {
        printBtn.addEventListener('click', function () {
            window.print();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            window.close();
        });
    }

    var body = document.body;
    if (body && body.getAttribute('data-auto-print') === '1') {
        window.addEventListener('load', function () {
            window.setTimeout(function () {
                window.print();
            }, 250);
        });
    }
})();

