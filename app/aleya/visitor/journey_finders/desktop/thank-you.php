<?php

function renderItems($htmlString, $iconClass = 'ri-check-line', $iconColor = 'text-primary')
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlString);
$xpath = new DOMXPath($doc);

$items = $xpath->query('//p');

if ($items->length === 0 || trim($items[0]->textContent) === '') {
$items = $xpath->query('//ol/li');
}

echo '<ul class="space-y-2">';
    foreach ($items as $item) {
    $text = trim($item->textContent);
    if (!empty($text)) {
    echo '
    <li class="flex items-start">
        <div class="w-5 h-5 flex items-center justify-center ' . $iconColor . ' mr-2 mt-0.5">
            <i class="' . $iconClass . '"></i>
        </div>
        <span class="text-gray-700">' . htmlspecialchars($text) . '</span>
    </li>';
    }
    }
    echo '
</ul>';
}

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Tour Details -->
        <div class="lg:col-span-2">
            <!-- Overview Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mt-32">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <!-- Confirmation Dialog -->
                    <div id="confirmation-dialog"
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                            <div class="text-center mb-6">
                                <div
                                    class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-check-line ri-2x text-primary"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    Thank You for Your Enquiry!
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    We have received your information and will get back to you
                                    within 24 hours.
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="/" id="close-dialog"
                                    class="flex-1 px-6 py-2 bg-primary text-center text-white font-medium !rounded-button hover:bg-primary/90 transition">
                                    Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<?php

require_once 'footer.php';

?>