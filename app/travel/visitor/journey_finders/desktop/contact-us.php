<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - JourneyFinders</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#F15A22',secondary:'#10b981'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
body {
font-family: 'Inter', sans-serif;
}
input:focus, textarea:focus, select:focus {
outline: none;
border-color: #4f46e5;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
-webkit-appearance: none;
margin: 0;
}
.custom-checkbox {
position: relative;
display: inline-block;
width: 18px;
height: 18px;
border: 2px solid #d1d5db;
border-radius: 4px;
cursor: pointer;
}
.custom-checkbox.checked {
background-color: #4f46e5;
border-color: #4f46e5;
}
.custom-checkbox.checked::after {
content: '';
position: absolute;
top: 2px;
left: 6px;
width: 5px;
height: 10px;
border: solid white;
border-width: 0 2px 2px 0;
transform: rotate(45deg);
}
.custom-radio {
position: relative;
display: inline-block;
width: 18px;
height: 18px;
border: 2px solid #d1d5db;
border-radius: 50%;
cursor: pointer;
}
.custom-radio.checked {
border-color: #4f46e5;
}
.custom-radio.checked::after {
content: '';
position: absolute;
top: 3px;
left: 3px;
width: 8px;
height: 8px;
border-radius: 50%;
background-color: #4f46e5;
}
.faq-item.active .faq-answer {
max-height: 500px;
padding-top: 1rem;
padding-bottom: 1rem;
}
.faq-item.active .faq-icon {
transform: rotate(180deg);
}
.faq-answer {
max-height: 0;
overflow: hidden;
transition: all 0.3s ease;
}
.faq-icon {
transition: transform 0.3s ease;
}
</style>
</head>
<body class="bg-gray-50">
<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
<div class="container mx-auto px-4 py-4 flex items-center justify-between">
<div class="flex items-center gap-12">
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="h-8">
<img src="https://static.readdy.ai/image/e53293cd76d16da5ebc50b3bdf122b81/9d10c38df657779d6114b355aeab28cc.png" alt="JourneyFinders" class="h-full">
</a>
<nav class="hidden md:flex items-center space-x-8">
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="text-gray-600 hover:text-primary transition">Home</a>
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="text-gray-600 hover:text-primary transition">About Us</a>
<a href="#" class="text-primary font-medium">Contact</a>
<a href="#" class="text-gray-600 hover:text-primary transition">Destinations</a>
<a href="#" class="text-gray-600 hover:text-primary transition">Tours</a>
</nav>
</div>
<div class="flex items-center gap-4">
<div class="relative hidden md:block">
<input type="text" placeholder="Search destinations..." class="pl-10 pr-4 py-2 rounded-full border border-gray-300 text-sm w-64">
<div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
<i class="ri-search-line"></i>
</div>
</div>
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/69bf416b-7caf-490d-9237-76a66fcfb4d5" data-readdy="true" class="hidden md:block">
<button class="bg-primary text-white px-5 py-2 !rounded-button text-sm font-medium hover:bg-indigo-600 transition whitespace-nowrap">Login / Register</button>
</a>
<button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
<i class="ri-menu-line text-xl"></i>
</button>
</div>
</div>
</header>
<!-- Hero Section -->
<section class="relative py-20 overflow-hidden">
<div style="background-image: url('#api/search-image?query=professional%20travel%20agency%20customer%20service%20representatives%20in%20a%20modern%20office%20environment%20with%20soft%20lighting%2C%20people%20working%20at%20desks%20with%20headsets%2C%20travel%20posters%20on%20walls%2C%20clean%20and%20professional%20atmosphere%2C%20high%20quality%20professional%20photography&width=1920&height=600&seq=1&orientation=landscape'); background-size: cover; background-position: center;" class="absolute inset-0 z-0">
<div class="absolute inset-0 bg-primary bg-opacity-70"></div>
</div>
<div class="container mx-auto px-4 relative z-10">
<div class="max-w-3xl mx-auto text-center">
<h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Contact Us</h1>
<p class="text-xl text-white opacity-90 mb-8">We're here to help you plan your perfect journey. Reach out to our team of travel experts through multiple channels.</p>
<div class="flex flex-wrap justify-center gap-4">
<button class="bg-white text-primary px-6 py-3 !rounded-button font-medium hover:bg-gray-100 transition whitespace-nowrap">
<i class="ri-phone-line mr-2"></i>Call Now
</button>
<button class="bg-transparent border-2 border-white text-white px-6 py-3 !rounded-button font-medium hover:bg-white hover:bg-opacity-10 transition whitespace-nowrap">
<i class="ri-message-3-line mr-2"></i>Live Chat
</button>
</div>
</div>
</div>
</section>
<!-- Contact Methods Grid -->
<section class="py-16 bg-white">
<div class="container mx-auto px-4">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold text-gray-900 mb-4">How Can We Help You?</h2>
<p class="text-lg text-gray-600 max-w-2xl mx-auto">Choose the most convenient way to connect with our travel experts who are ready to assist you 24/7.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- Phone Support -->
<div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
<div class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
<i class="ri-phone-line text-primary text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Phone Support</h3>
<p class="text-gray-600 mb-4">Speak directly with our travel consultants for immediate assistance.</p>
<div class="font-medium text-lg text-primary mb-2">+1 (800) 123-4567</div>
<p class="text-sm text-gray-500">Available 24/7</p>
<div class="mt-6">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
<span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
Available Now
</span>
</div>
</div>
<!-- Email Support -->
<div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
<div class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
<i class="ri-mail-line text-primary text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Email Support</h3>
<p class="text-gray-600 mb-4">Send us your inquiries and we'll respond within 24 hours.</p>
<div class="font-medium text-lg text-primary mb-2">info@journeyfinders.com</div>
<p class="text-sm text-gray-500">Response time: 24 hours</p>
<div class="mt-6">
<button class="bg-primary text-white px-4 py-2 !rounded-button text-sm font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-mail-send-line mr-1"></i>Send Email
</button>
</div>
</div>
<!-- Live Chat -->
<div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition duration-300 border border-gray-100">
<div class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
<i class="ri-chat-1-line text-primary text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Live Chat</h3>
<p class="text-gray-600 mb-4">Chat with our travel experts in real-time for instant help.</p>
<div class="font-medium text-lg text-primary mb-2">Live Chat Support</div>
<p class="text-sm text-gray-500">Average wait time: 2 minutes</p>
<div class="mt-6">
<button class="bg-white border border-primary text-primary px-4 py-2 !rounded-button text-sm font-medium hover:bg-primary hover:text-white transition whitespace-nowrap">
<i class="ri-chat-1-line mr-1"></i>Start Chat
</button>
</div>
</div>
</div>
</div>
</section>
<!-- Contact Form Section -->
<section class="py-16 bg-gray-50">
<div class="container mx-auto px-4">
<div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm overflow-hidden">
<div class="flex flex-col md:flex-row">
<!-- Form Left Side -->
<div class="md:w-7/12 p-8 md:p-12">
<h2 class="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
<p class="text-gray-600 mb-8">Fill out the form below and our team will get back to you as soon as possible.</p>
<form>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
<div>
<label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
<input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm" placeholder="John Smith" required>
</div>
<div>
<label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
<input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm" placeholder="john@example.com" required>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
<div>
<label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
<input type="tel" id="phone" class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm" placeholder="+1 (123) 456-7890">
</div>
<div>
<label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
<div class="relative">
<select id="subject" class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm appearance-none pr-8" required>
<option value="" disabled selected>Select a subject</option>
<option value="general">General Inquiry</option>
<option value="booking">Booking Question</option>
<option value="support">Customer Support</option>
<option value="feedback">Feedback</option>
<option value="partnership">Partnership Opportunity</option>
</select>
<div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
<i class="ri-arrow-down-s-line text-gray-400"></i>
</div>
</div>
</div>
</div>
<div class="mb-6">
<label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
<textarea id="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded text-gray-900 text-sm" placeholder="Tell us how we can help you..." required></textarea>
</div>
<div class="mb-6">
<div class="flex items-start">
<div class="custom-checkbox" id="privacy-checkbox"></div>
<label for="privacy-checkbox" class="ml-3 text-sm text-gray-600">
I agree to the <a href="#" class="text-primary hover:underline">Privacy Policy</a> and consent to having my data processed.
</label>
</div>
</div>
<button type="submit" class="w-full bg-primary text-white py-3 !rounded-button font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-send-plane-fill mr-2"></i>Send Message
</button>
</form>
</div>
<!-- Form Right Side -->
<div class="md:w-5/12 bg-indigo-50 p-8 md:p-12 flex flex-col justify-between">
<div>
<h3 class="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
<div class="space-y-6">
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
<i class="ri-map-pin-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">Main Office</h4>
<p class="text-gray-600">123 Travel Avenue, Suite 500<br>New York, NY 10001, USA</p>
</div>
</div>
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
<i class="ri-time-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">Business Hours</h4>
<p class="text-gray-600">Monday - Friday: 9:00 AM - 8:00 PM<br>Saturday: 10:00 AM - 6:00 PM<br>Sunday: Closed</p>
</div>
</div>
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-primary mr-4">
<i class="ri-customer-service-2-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">Customer Support</h4>
<p class="text-gray-600">24/7 Emergency Travel Assistance<br>+1 (800) 987-6543</p>
</div>
</div>
</div>
</div>
<div class="mt-8">
<h4 class="font-medium text-gray-900 mb-4">Connect With Us</h4>
<div class="flex space-x-4">
<a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
<i class="ri-facebook-fill"></i>
</a>
<a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
<i class="ri-twitter-fill"></i>
</a>
<a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
<i class="ri-instagram-line"></i>
</a>
<a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-600 hover:text-primary transition">
<i class="ri-linkedin-fill"></i>
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Office Locations with Map -->
<section class="py-16 bg-white">
<div class="container mx-auto px-4">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold text-gray-900 mb-4">Our Office Location</h2>
<p class="text-lg text-gray-600 max-w-2xl mx-auto">Visit our office for personalized travel planning assistance.</p>
</div>
<div class="flex flex-col md:flex-row gap-8">
<div class="md:w-1/2">
<div class="bg-gray-100 rounded-xl overflow-hidden h-full relative">
<div style="background-image: url('https://public.readdy.ai/gen_page/map_placeholder_1280x720.png'); background-size: cover; background-position: center;" class="absolute inset-0"></div>
</div>
</div>
<div class="md:w-1/2">
<!-- New York Office -->
<div class="bg-white rounded-lg p-8 shadow-sm border border-gray-100 hover:shadow-md transition h-full">
<div class="flex items-center mb-6">
<div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mr-4">
<i class="ri-building-line text-xl"></i>
</div>
<h3 class="text-2xl font-semibold text-gray-900">New York Headquarters</h3>
</div>
<div class="space-y-4 mb-8">
<div class="flex">
<div class="w-8 h-8 flex items-center justify-center text-gray-500 mr-4 flex-shrink-0">
<i class="ri-map-pin-line text-lg"></i>
</div>
<p class="text-gray-600 text-lg">123 Travel Avenue, Suite 500, New York, NY 10001, USA</p>
</div>
<div class="flex">
<div class="w-8 h-8 flex items-center justify-center text-gray-500 mr-4 flex-shrink-0">
<i class="ri-phone-line text-lg"></i>
</div>
<p class="text-gray-600 text-lg">+1 (212) 555-7890</p>
</div>
<div class="flex">
<div class="w-8 h-8 flex items-center justify-center text-gray-500 mr-4 flex-shrink-0">
<i class="ri-mail-line text-lg"></i>
</div>
<p class="text-gray-600 text-lg">contact@journeyfinders.com</p>
</div>
<div class="flex">
<div class="w-8 h-8 flex items-center justify-center text-gray-500 mr-4 flex-shrink-0">
<i class="ri-time-line text-lg"></i>
</div>
<p class="text-gray-600 text-lg">Monday - Friday: 9:00 AM - 8:00 PM<br>Saturday: 10:00 AM - 6:00 PM<br>Sunday: Closed</p>
</div>
</div>
<div class="flex gap-4">
<button class="flex-1 bg-primary text-white py-3 !rounded-button font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-calendar-line mr-2"></i>Schedule a Visit
</button>
<button class="flex-1 bg-white border border-primary text-primary py-3 !rounded-button font-medium hover:bg-primary hover:text-white transition whitespace-nowrap">
<i class="ri-map-2-line mr-2"></i>Get Directions
</button>
</div>
</div>
</div>
</div>
</section>
<!-- FAQ Section -->
<section class="py-16 bg-gray-50">
<div class="container mx-auto px-4">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
<p class="text-lg text-gray-600 max-w-2xl mx-auto">Find answers to common questions about our services, booking process, and travel policies.</p>
</div>
<div class="max-w-3xl mx-auto">
<div class="mb-8">
<div class="relative">
<input type="text" placeholder="Search for questions..." class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 text-gray-900">
<div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
<i class="ri-search-line text-xl"></i>
</div>
</div>
</div>
<div class="space-y-4" id="faq-container">
<!-- FAQ Item 1 -->
<div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
<div class="faq-question p-5 cursor-pointer flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900">How do I book a travel package?</h3>
<div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
<i class="ri-arrow-down-s-line"></i>
</div>
</div>
<div class="faq-answer px-5 text-gray-600">
<p>You can book a travel package through our website by browsing available options, selecting your preferred package, and following the booking steps. Alternatively, you can contact our travel consultants via phone, email, or live chat for personalized assistance with your booking. We recommend booking at least 3 months in advance for international trips to secure the best rates and availability.</p>
</div>
</div>
<!-- FAQ Item 2 -->
<div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
<div class="faq-question p-5 cursor-pointer flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900">What is your cancellation policy?</h3>
<div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
<i class="ri-arrow-down-s-line"></i>
</div>
</div>
<div class="faq-answer px-5 text-gray-600">
<p>Our standard cancellation policy allows for a full refund if canceled 30 days or more before departure. Cancellations made 15-29 days before departure receive a 50% refund. Cancellations less than 15 days before departure are non-refundable. However, we offer travel insurance options that can provide coverage for unexpected cancellations. Each package may have specific terms, so please check the details at the time of booking.</p>
</div>
</div>
<!-- FAQ Item 3 -->
<div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
<div class="faq-question p-5 cursor-pointer flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900">Do you offer travel insurance?</h3>
<div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
<i class="ri-arrow-down-s-line"></i>
</div>
</div>
<div class="faq-answer px-5 text-gray-600">
<p>Yes, we offer comprehensive travel insurance options through our trusted partners. Our insurance packages can cover trip cancellation, medical emergencies, lost luggage, travel delays, and more. We highly recommend purchasing travel insurance for all international trips. Our consultants can help you select the right coverage based on your destination, trip duration, and specific needs.</p>
</div>
</div>
<!-- FAQ Item 4 -->
<div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
<div class="faq-question p-5 cursor-pointer flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900">Can I customize a travel package?</h3>
<div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
<i class="ri-arrow-down-s-line"></i>
</div>
</div>
<div class="faq-answer px-5 text-gray-600">
<p>Absolutely! We specialize in creating customized travel experiences tailored to your preferences and needs. Our travel consultants can help you design a personalized itinerary, including accommodations, activities, transportation, and special requests. Contact us through phone, email, or schedule a consultation to start planning your custom travel package.</p>
</div>
</div>
<!-- FAQ Item 5 -->
<div class="faq-item bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
<div class="faq-question p-5 cursor-pointer flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900">What payment methods do you accept?</h3>
<div class="faq-icon w-6 h-6 flex items-center justify-center text-primary">
<i class="ri-arrow-down-s-line"></i>
</div>
</div>
<div class="faq-answer px-5 text-gray-600">
<p>We accept various payment methods including major credit cards (Visa, MasterCard, American Express), PayPal, bank transfers, and in some locations, cash payments. For certain destinations and packages, we also offer payment plans allowing you to pay in installments. All online payments are secured with industry-standard encryption to protect your financial information.</p>
</div>
</div>
</div>
<div class="mt-10 text-center">
<p class="text-gray-600 mb-4">Still have questions? Our team is here to help.</p>
<button class="bg-primary text-white px-6 py-3 !rounded-button font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-customer-service-2-line mr-2"></i>Contact Support
</button>
</div>
</div>
</div>
</section>
<!-- Book Consultation CTA -->
<section class="py-16 bg-primary bg-opacity-5">
<div class="container mx-auto px-4">
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm overflow-hidden">
<div class="flex flex-col md:flex-row">
<div class="md:w-7/12 p-8 md:p-12">
<h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Book a Free Travel Consultation</h2>
<p class="text-gray-600 mb-8">Schedule a personalized consultation with our travel experts to plan your dream vacation. We'll help you create the perfect itinerary tailored to your preferences, budget, and travel style.</p>
<div class="space-y-6 mb-8">
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mr-4 flex-shrink-0">
<i class="ri-user-star-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">Expert Guidance</h4>
<p class="text-gray-600">Get personalized recommendations from experienced travel consultants.</p>
</div>
</div>
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mr-4 flex-shrink-0">
<i class="ri-money-dollar-circle-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">No Obligation</h4>
<p class="text-gray-600">Our consultation is completely free with no pressure to book.</p>
</div>
</div>
<div class="flex items-start">
<div class="w-10 h-10 flex items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mr-4 flex-shrink-0">
<i class="ri-time-line"></i>
</div>
<div>
<h4 class="font-medium text-gray-900 mb-1">Flexible Scheduling</h4>
<p class="text-gray-600">Choose a time that works best for your schedule.</p>
</div>
</div>
</div>
<button class="bg-primary text-white px-6 py-3 !rounded-button font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-calendar-line mr-2"></i>Schedule Your Consultation
</button>
</div>
<div class="md:w-5/12 bg-gray-100 flex items-center justify-center p-8">
<div style="background-image: url('#api/search-image?query=professional%20travel%20consultant%20meeting%20with%20clients%2C%20discussing%20travel%20plans%20with%20a%20map%20and%20laptop%2C%20warm%20lighting%2C%20professional%20office%20environment%2C%20high%20quality%20photography&width=500&height=600&seq=2&orientation=portrait'); background-size: cover; background-position: center;" class="w-full h-80 rounded-lg"></div>
</div>
</div>
</div>
</div>
</section>
<!-- Footer -->
<footer class="bg-gray-900 text-white pt-16 pb-8">
<div class="container mx-auto px-4">
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
<div>
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="h-8 mb-6 block">
<img src="https://static.readdy.ai/image/e53293cd76d16da5ebc50b3bdf122b81/9d10c38df657779d6114b355aeab28cc.png" alt="JourneyFinders" class="h-full">
</a>
<p class="text-gray-400 mb-6">Your trusted partner for unforgettable travel experiences since 2005. We specialize in creating personalized journeys that match your travel style and preferences.</p>
<div class="flex space-x-4">
<a href="#" class="text-gray-400 hover:text-white transition">
<i class="ri-facebook-fill"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition">
<i class="ri-twitter-fill"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition">
<i class="ri-instagram-line"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition">
<i class="ri-linkedin-fill"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition">
<i class="ri-youtube-fill"></i>
</a>
</div>
</div>
<div>
<h3 class="text-lg font-semibold mb-6">Quick Links</h3>
<ul class="space-y-3">
<li><a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="text-gray-400 hover:text-white transition">Home</a></li>
<li><a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/9cde62b7-5ea0-486d-ae8d-dbcabe113397" data-readdy="true" class="text-gray-400 hover:text-white transition">About Us</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Destinations</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Tours & Packages</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Travel Blog</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Careers</a></li>
</ul>
</div>
<div>
<h3 class="text-lg font-semibold mb-6">Support</h3>
<ul class="space-y-3">
<li><a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">FAQs</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Terms & Conditions</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Cancellation Policy</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition">Travel Insurance</a></li>
</ul>
</div>
<div>
<h3 class="text-lg font-semibold mb-6">Newsletter</h3>
<p class="text-gray-400 mb-4">Subscribe to our newsletter for travel tips, exclusive offers, and inspiration.</p>
<div class="flex mb-4">
<input type="email" placeholder="Your email address" class="bg-gray-800 border-none text-white px-4 py-2 rounded-l w-full text-sm">
<button class="bg-primary text-white px-4 py-2 rounded-r font-medium hover:bg-indigo-600 transition whitespace-nowrap">
<i class="ri-send-plane-fill"></i>
</button>
</div>
<h3 class="text-lg font-semibold mb-4 mt-6">We Accept</h3>
<div class="flex space-x-3">
<div class="w-10 h-6 flex items-center justify-center bg-white rounded">
<i class="ri-visa-fill text-blue-800 text-lg"></i>
</div>
<div class="w-10 h-6 flex items-center justify-center bg-white rounded">
<i class="ri-mastercard-fill text-red-500 text-lg"></i>
</div>
<div class="w-10 h-6 flex items-center justify-center bg-white rounded">
<i class="ri-paypal-fill text-blue-600 text-lg"></i>
</div>
<div class="w-10 h-6 flex items-center justify-center bg-white rounded">
<i class="ri-apple-fill text-gray-800 text-lg"></i>
</div>
</div>
</div>
</div>
<div class="border-t border-gray-800 pt-8">
<div class="flex flex-col md:flex-row justify-between items-center">
<p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 JourneyFinders. All rights reserved.</p>
<div class="flex space-x-6">
<a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/7250de12-0e97-4257-afd4-78df0ec8f1ea" data-readdy="true" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a>
<a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a>
<a href="#" class="text-gray-400 hover:text-white text-sm transition">Cookie Policy</a>
</div>
</div>
</div>
</div>
</footer>
<!-- Floating Elements -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-4">
<button class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-green-600 transition">
<i class="ri-whatsapp-line text-2xl"></i>
</button>
<button class="w-14 h-14 bg-primary rounded-full flex items-center justify-center text-white shadow-lg hover:bg-indigo-600 transition">
<i class="ri-message-3-line text-2xl"></i>
</button>
<button id="back-to-top" class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-gray-700 transition">
<i class="ri-arrow-up-line text-2xl"></i>
</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
// Custom checkbox functionality
const privacyCheckbox = document.getElementById('privacy-checkbox');
privacyCheckbox.addEventListener('click', function() {
this.classList.toggle('checked');
});
// FAQ accordion functionality
const faqItems = document.querySelectorAll('.faq-item');
faqItems.forEach(item => {
const question = item.querySelector('.faq-question');
question.addEventListener('click', () => {
item.classList.toggle('active');
});
});
// Back to top button
const backToTopButton = document.getElementById('back-to-top');
backToTopButton.addEventListener('click', () => {
window.scrollTo({
top: 0,
behavior: 'smooth'
});
});
// Show/hide back to top button based on scroll position
window.addEventListener('scroll', () => {
if (window.pageYOffset > 300) {
backToTopButton.classList.remove('opacity-0');
backToTopButton.classList.add('opacity-100');
} else {
backToTopButton.classList.remove('opacity-100');
backToTopButton.classList.add('opacity-0');
}
});
});
</script>
</body>
</html>
