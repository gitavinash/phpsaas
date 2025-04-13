<footer class="bg-gray-900 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-4 gap-8 mb-12">
            <div>
                <a href="/" class="text-3xl font-['Pacifico'] text-white mb-6 block">logo</a>
                <p class="text-gray-400 mb-6">
                    Simplifying visa applications with expert assistance and fast
                    processing.
                </p>
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
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">About Us</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Visa Services</a>
                    </li>
                    <li>
                        <a href="#home/da68aad3-a508-4219-bc9a-0f79e66cb4a4/bfd35c08-38de-47ad-9329-be4e37217bf2"
                            data-readdy="true" class="text-gray-400 hover:text-white">Track Application</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Support Center</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-medium mb-4">Popular Visas</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">USA Visa</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">UK Visa</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Canada Visa</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Australia Visa</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white">Schengen Visa</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-medium mb-4">Newsletter</h4>
                <p class="text-gray-400 mb-4">
                    Subscribe to our newsletter for updates and offers.
                </p>
                <form class="space-y-4">
                    <input type="email" placeholder="Your email address"
                        class="w-full px-4 py-2 rounded-button bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:outline-none focus:border-primary" />
                    <button type="submit"
                        class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8">
            <div class="flex justify-between items-center">
                <div class="text-gray-400">
                    © 2025 Visa2Fly. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="fixed bottom-6 right-6 z-50 space-y-4">
    <button id="chatButton"
        class="w-14 h-14 bg-primary text-white rounded-full shadow-lg flex items-center justify-center hover:bg-primary/90 cursor-pointer">
        <i class="ri-message-3-line ri-lg"></i>
    </button>
    <button id="backToTop"
        class="back-to-top w-14 h-14 bg-white text-primary rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 cursor-pointer">
        <i class="ri-arrow-up-line ri-lg"></i>
    </button>
</div>
<?php require_once NOTIFICATION.'index.php'; ?>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>
// Tailwind CSS Configurations
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: "#0066CC",
                secondary: "#FF9933",
            },
            borderRadius: {
                none: "0px",
                sm: "4px",
                DEFAULT: "8px",
                md: "12px",
                lg: "16px",
                xl: "20px",
                "2xl": "24px",
                "3xl": "32px",
                full: "9999px",
                button: "8px",
            },
        },
    },
};

document.addEventListener("DOMContentLoaded", function() {
    const header = document.querySelector(".sticky-header");
    const backToTop = document.querySelector(".back-to-top");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 100) {
            header.classList.add("shadow");
            backToTop.classList.add("visible");
        } else {
            header.classList.remove("shadow");
            backToTop.classList.remove("visible");
        }
    });
    backToTop.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });
    const chatButton = document.getElementById("chatButton");
    let chatWindow = null;
    chatButton.addEventListener("click", () => {
        if (!chatWindow) {
            const div = document.createElement("div");
            div.className =
                "fixed bottom-24 right-6 w-80 bg-white rounded-lg shadow-xl z-50";
            div.innerHTML = `
    <div class="p-4 bg-primary rounded-t-lg flex justify-between items-center">
        <h3 class="text-white font-medium">Chat Support</h3>
        <button class="text-white hover:text-gray-200" id="closeChat">
            <i class="ri-close-line"></i>
        </button>
    </div>
    <div class="h-96 p-4 overflow-y-auto">
        <div class="text-center text-gray-500 text-sm mb-4">How can we help you today?</div>
    </div>
    <div class="p-4 border-t">
        <div class="flex space-x-2">
            <input type="text" placeholder="Type your message..."
                class="flex-1 px-3 py-2 border rounded-button text-sm">
            <button class="bg-primary text-white px-4 !rounded-button hover:bg-primary/90">
                <i class="ri-send-plane-script</i>
            </button>
        </div>
    </div>
    `;
            document.body.appendChild(div);
            chatWindow = div;
            document.getElementById("closeChat").addEventListener("click", () => {
                chatWindow.remove();
                chatWindow = null;
            });
        }
    });
});
</script>
</body>

</html>