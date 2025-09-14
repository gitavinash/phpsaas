<!-- Footer -->
<footer class="bg-gray-800 text-white py-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <a href="/">
                    <h3 class="text-xl font-['Pacifico'] mb-4">HairBud</h3>
                </a>
                <p class="text-gray-300 text-sm">The leading online community for hair loss solutions and hair
                    transplant information. Connect with specialists and others on similar journeys.</p>
                <div class="flex mt-4 space-x-3">
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-600">
                        <i class="ri-facebook-fill ri-lg"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-600">
                        <i class="ri-twitter-x-fill ri-lg"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-600">
                        <i class="ri-instagram-fill ri-lg"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-600">
                        <i class="ri-youtube-fill ri-lg"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-medium mb-4">Quick Links</h4>
                <ul class="space-y-2 text-gray-300 text-sm">
                    <li><a href="#" class="hover:text-white">Home</a></li>
                    <li><a href="#" class="hover:text-white">Find a Specialist</a></li>
                    <li><a href="#" class="hover:text-white">Before & After Gallery</a></li>
                    <li><a href="#" class="hover:text-white">Treatment Options</a></li>
                    <li><a href="#" class="hover:text-white">Cost Calculator</a></li>
                    <li><a href="#" class="hover:text-white">Success Stories</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-4">Resources</h4>
                <ul class="space-y-2 text-gray-300 text-sm">
                    <li><a href="#" class="hover:text-white">Hair Loss Guide</a></li>
                    <li><a href="#" class="hover:text-white">Procedure Types</a></li>
                    <li><a href="#" class="hover:text-white">Recovery Tips</a></li>
                    <li><a href="#" class="hover:text-white">Financing Options</a></li>
                    <li><a href="#" class="hover:text-white">FAQ</a></li>
                    <li><a href="#" class="hover:text-white">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-4">Stay Updated</h4>
                <p class="text-gray-300 text-sm mb-3">Subscribe to our newsletter for the latest treatments,
                    research, and community events.</p>
                <div class="flex">
                    <input type="email" placeholder="Your email address"
                        class="w-full px-3 py-2 text-sm text-gray-900 rounded-l border-none focus:outline-none focus:ring-2 focus:ring-primary">
                    <button
                        class="bg-primary px-4 py-2 rounded-r text-sm font-medium !rounded-button whitespace-nowrap">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
            <div class="text-gray-400 text-sm">
                © 2025 HairBud Forum. All rights reserved.
            </div>
            <div class="flex mt-4 md:mt-0 space-x-4 text-gray-400 text-sm">
                <a href="#" class="hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-white">Terms of Service</a>
                <a href="#" class="hover:text-white">Cookie Policy</a>
                <a href="#" class="hover:text-white">Contact Us</a>
            </div>
        </div>
    </div>
</footer>
<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#4F46E5',
                secondary: '#10B981'
            },
            borderRadius: {
                'none': '0px',
                'sm': '4px',
                DEFAULT: '8px',
                'md': '12px',
                'lg': '16px',
                'xl': '20px',
                '2xl': '24px',
                '3xl': '32px',
                'full': '9999px',
                'button': '8px'
            }
        }
    }
}
</script>
<script src="<?php echo USER_JS ?>app.js"></script>
<?php

if (ACTIVE_URL == '/start-a-discussion/') {
    echo "<script src='" . USER_JS . "discussion.js'></script>";
}

?>
</body>

</html>