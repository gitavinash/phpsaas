<?php

require_once 'header.php';

?>

<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-check-line text-green-500 ri-2x"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Application Submitted!</h3>
            <p class="text-gray-600 mb-6">Your visa application has been successfully submitted. You will receive a
                confirmation email shortly.</p>
            <!-- <div class="text-lg font-medium mb-6">Application ID: <span class="text-primary">VS-2025031501</span> -->
        </div>
        <a href="/dashboard/" data-readdy="true"
            class="block bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer w-full text-center">Continiue
            to dashboard</a>
    </div>
</div>
</div>

<?php

require_once 'footer.php';

?>