<footer class="bg-gray-900 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div>
                <a href="/" class="text-3xl font-['Pacifico'] text-white mb-6 block">logo</a>
                <p class="text-gray-400 mb-6">Simplifying visa applications with expert assistance and fast
                    processing.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="ri-facebook-fill w-6 h-6 flex items-center justify-center"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="ri-twitter-fill w-6 h-6 flex items-center justify-center"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="ri-linkedin-fill w-6 h-6 flex items-center justify-center"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="ri-instagram-fill w-6 h-6 flex items-center justify-center"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-medium mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="/about-us/" data-readdy="true" class="text-gray-400 hover:text-white">About Us</a></li>
                    <li><a href="/services/" data-readdy="true" class="text-gray-400 hover:text-white">Visa Services</a>
                    </li>
                    <li><a href="/track-application/" data-readdy="true" class="text-gray-400 hover:text-white">Track
                            Application</a></li>
                    <li><a href="/travel-agent-registration/" data-readdy="true"
                            class="text-gray-400 hover:text-white">Travel Agent Registration</a></li>
                    <li><a href="/contact-us/" data-readdy="true" class="text-gray-400 hover:text-white">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-medium mb-4">Popular Visas</h4>
                <ul class="space-y-2">
                    <li><a href="/start-application/?country=united_states" data-readdy="true" data-readdy="true"
                            class="text-gray-400 hover:text-white">USA Visa</a></li>
                    <li><a href="/start-application/?country=united_kingdom" class="text-gray-400 hover:text-white">UK
                            Visa</a></li>
                    <li><a href="/start-application/?country=canada" class="text-gray-400 hover:text-white">Canada
                            Visa</a></li>
                    <li><a href="/start-application/?country=australia" class="text-gray-400 hover:text-white">Australia
                            Visa</a></li>
                    <li><a href="/start-application/?country=cchengen" class="text-gray-400 hover:text-white">Schengen
                            Visa</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-medium mb-4">Newsletter</h4>
                <p class="text-gray-400 mb-4">Subscribe to our newsletter for updates and offers.</p>
                <form class="space-y-4">
                    <input type="email" placeholder="Your email address"
                        class="w-full px-4 py-2 rounded-button bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:outline-none focus:border-primary">
                    <button type="submit"
                        class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-gray-400 text-center md:text-left">© <?php echo date('Y ') . SITE_NAME ?>. All rights
                    reserved. | Design and Developed by <a href="https://blumox.com" target="_blank">Blumox
                        Technologies</a></div>
                <div class="flex flex-wrap justify-center md:justify-end gap-4 md:space-x-6">
                    <a href="/privacy-policy/" data-readdy="true" class="text-gray-400 hover:text-white">Privacy
                        Policy</a>
                    <a href="/terms-of-services/" class="text-gray-400 hover:text-white">Terms of Service</a>
                    <a href="/refund-policy/" class="text-gray-400 hover:text-white">Refund Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50 space-y-4">
    <button id="chatButton"
        class="w-14 h-14 bg-[#25D366] text-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#22C55E] cursor-pointer">
        <i class="ri-whatsapp-line ri-lg"></i>
    </button>
    <button id="backToTop"
        class="back-to-top w-14 h-14 bg-white text-primary rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 cursor-pointer">
        <i class="ri-arrow-up-line ri-lg"></i>
    </button>
</div>
<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?php echo USER_JS ?>tailwind.config.js"></script>
<script src="<?php echo USER_JS ?>app.min.js"></script>
</body>

</html>