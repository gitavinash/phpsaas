<?php /* Cartt Fashion Solution */ ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Electronics Ecommerce Platform — Cartt</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" />
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?php echo USER_STYLESHEET ?? 'assets/css/' ?>style.css" />
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col antialiased">

<!-- HEADER WRAPPER -->
<div class="fixed top-0 left-0 right-0 z-[60] flex flex-col w-full">
  <!-- ANNOUNCEMENT BAR -->
  <div class="bg-indigo-600 text-white text-[13px] font-semibold text-center py-2.5 px-4 shadow-md w-full relative z-20">
    <span class="inline-flex items-center gap-2 flex-wrap justify-center">
      <span class="text-amber-300">&#127881; Limited Time Offer:</span> Get &#8377;5,000 Setup Fee Waived on Annual Plans!
      <a href="index.php#pricing" class="underline underline-offset-2 hover:text-indigo-200 transition-colors ml-1">View Pricing &rarr;</a>
    </span>
  </div>

  <!-- NAVBAR -->
  <header id="navbar" class="bg-white/95 backdrop-blur-md border-b border-slate-100 w-full relative z-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <a href="index.php" class="flex items-center gap-2 flex-shrink-0">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-500 flex items-center justify-center shadow-md">
          <span class="text-white font-black text-sm">C</span>
        </div>
        <span class="font-bold text-slate-900 text-xl tracking-tight">Cartt</span>
        <span class="hidden sm:inline-block text-xs text-slate-400 font-medium border-l border-slate-200 pl-2 ml-1">Build. Sell. Grow.</span>
      </a>
      <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-600">
        <a href="index.php#features" class="hover:text-indigo-600 transition-colors">Features</a>
        <a href="index.php#pricing"  class="hover:text-indigo-600 transition-colors">Pricing</a>
        <a href="migration.php"      class="hover:text-indigo-600 transition-colors">Migration</a>
      </nav>
      <div class="hidden lg:flex items-center gap-3">
        <a href="/login" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors px-3 py-2">Login</a>
        <a href="index.php#pricing" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200">
          Start Selling Now
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
      <button id="menu-btn" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>
  <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-100 bg-white px-4 pb-4 pt-2">
    <nav class="flex flex-col gap-1 text-sm font-medium text-slate-600 mb-4">
      <a href="index.php#features" class="py-2 hover:text-indigo-600 transition-colors">Features</a>
      <a href="index.php#pricing"  class="py-2 hover:text-indigo-600 transition-colors">Pricing</a>
      <a href="migration.php"      class="py-2 hover:text-indigo-600 transition-colors">Migration</a>
    </nav>
    <div class="flex flex-col gap-2">
      <a href="/login" class="text-center text-sm font-medium text-slate-600 border border-slate-200 py-2.5 rounded-xl">Login</a>
      <a href="index.php#pricing" class="text-center bg-indigo-600 text-white text-sm font-semibold py-2.5 rounded-xl">Start Selling Now</a>
    </div>
  </div>
  </header>
</div>

<!-- HERO -->
<main class="flex-grow pt-32">
  <section class="bg-slate-900 w-full rounded-3xl max-w-7xl mx-auto py-24 sm:py-32 text-center relative overflow-hidden shadow-2xl mx-4">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-500 via-slate-900 to-slate-900"></div>
    <div class="relative z-10 max-w-3xl mx-auto px-4">
      <span class="inline-block py-1.5 px-4 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-black uppercase tracking-widest border border-indigo-500/30 mb-6 tracking-widest">Electronics Solution</span>
      <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-white tracking-tight mb-6">Built for Electronics Brands</h1>
      <p class="text-lg text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">High-performance search, detailed technical specifications, and smart inventory routing. Power your complex electronics catalog effortlessly.</p>
      <a href="index.php#pricing" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-8 rounded-xl transition-all shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:-translate-y-0.5">Start Building <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
    </div>
  </section>

  <!-- LOGO STRIPE -->
  <section class="py-10 border-y border-slate-200/50 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="text-center text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Powering India's fastest-growing electronics brands</p>
      <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-50 grayscale font-black tracking-tighter text-slate-800">
        <span class="text-2xl">SAMSUNG<span class="text-indigo-600">.</span></span>
        <span class="text-2xl">BOAT<span class="text-indigo-600">.</span></span>
        <span class="text-xl">CROMA<span class="text-indigo-600">.</span></span>
        <span class="text-2xl">RELIANCE<span class="text-indigo-600">.</span></span>
      </div>
    </div>
  </section>

  <!-- FEATURES BENTO -->
  <section class="py-20 sm:py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-4">Everything you need to scale</h2>
        <p class="text-lg text-slate-600 font-medium">Stop patching third-party apps together. Get a unified engine designed specifically for complex electronics catalogs.</p>
      </div>
      
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Feature 1 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/60 hover:shadow-xl transition-all h-full">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-3">Faceted Tech Search</h3>
          <p class="text-slate-600 leading-relaxed">Let customers filter by RAM, Storage, Processor, and dozens of custom meta-fields instantly without touching the database.</p>
        </div>
        <!-- Feature 2 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/60 hover:shadow-xl transition-all h-full">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-3">Dynamic Comparison</h3>
          <p class="text-slate-600 leading-relaxed">Generate side-by-side technical specification tables automatically for every product category, boosting buyer confidence.</p>
        </div>
        <!-- Feature 3 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/60 hover:shadow-xl transition-all h-full">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-3">IMEI &amp; Serial Tracking</h3>
          <p class="text-slate-600 leading-relaxed">Perfectly map out complex inventory models down to the individual serial number for massive, foolproof warranty controls.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- SPLIT SECTION -->
  <section class="py-20 sm:py-32 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row items-center gap-16">
        <div class="w-full lg:w-1/2">
          <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-6">Handle absolute complexity.</h2>
          <p class="text-lg text-slate-600 font-medium mb-6 leading-relaxed">Handling thousands of complex SKUs with daily price fluctuations? Standard platforms buckle under the weight. Cartt's underlying architecture is designed specifically for deep, heavy catalogs.</p>
          <ul class="space-y-4">
            <li class="flex items-center gap-3 text-slate-700 font-medium"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Mass SKU Pricing Sync</li>
            <li class="flex items-center gap-3 text-slate-700 font-medium"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Automated Marketplace Routing</li>
            <li class="flex items-center gap-3 text-slate-700 font-medium"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Advanced Store Locator APIs</li>
          </ul>
        </div>
        <div class="w-full lg:w-1/2 relative">
           <div class="absolute inset-0 bg-indigo-500 rounded-[2.5rem] transform rotate-3 scale-105 opacity-10"></div>
           <div class="bg-slate-900 rounded-[2rem] p-8 shadow-2xl relative border border-slate-800">
             <div class="flex items-center gap-2 mb-6 border-b border-slate-700/50 pb-4">
               <div class="w-3 h-3 rounded-full bg-rose-500"></div>
               <div class="w-3 h-3 rounded-full bg-amber-500"></div>
               <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
             </div>
             <div class="space-y-4 font-mono text-sm text-indigo-300">
               <p><span class="text-pink-400">await</span> CarttCatalog.<span class="text-green-300">bulkUpdate</span>({ category: "Smartphones" });</p>
               <p>console.<span class="text-blue-300">log</span>("Syncing to POS & Marketplaces...");</p>
               <p class="text-emerald-400 mt-4">&gt; Processed 14,200 variants in 1.2s</p>
               <p class="text-emerald-400">&gt; Status: All channels synchronized successfully</p>
             </div>
           </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-24 bg-indigo-600 text-center px-4">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-3xl sm:text-5xl font-black text-white mb-6 tracking-tight">Ready to dominate the electronics market?</h2>
      <p class="text-lg text-indigo-100 mb-10 font-medium">Join the fastest growing tech brands on India's most powerful eCommerce engine.</p>
      <a href="index.php#pricing" class="inline-flex items-center gap-2 bg-white text-indigo-600 font-bold py-4 px-10 rounded-xl shadow-xl hover:-translate-y-1 transition-all">Start Your 14-Day Free Trial</a>
    </div>
  </section>
</main>

<!-- FOOTER -->
<footer class="bg-[#0a0f1e] text-slate-400 pt-20 pb-12 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10 mb-14">
      <div class="col-span-2 lg:col-span-1">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center"><span class="text-white font-black text-sm">C</span></div>
          <span class="font-bold text-white text-lg">Cartt</span>
        </div>
        <p class="text-[13px] leading-relaxed mb-4 text-slate-500">India's complete eCommerce platform &mdash; build, sell, and grow without the plugin chaos.</p>
        <p class="text-xs text-slate-600">Built in India &#127470;&#127475; for Indian sellers</p>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-5">Product</h4>
        <ul class="space-y-3 text-[14px]">
          <li><a href="index.php#features" class="hover:text-white transition-colors">Features</a></li>
          <li><a href="index.php#pricing" class="hover:text-white transition-colors">Pricing</a></li>
          <li><a href="migration.php" class="hover:text-white transition-colors">Migration</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Solutions</h4>
        <ul class="space-y-2.5 text-[14px]">
          <li><a href="fashion.php" class="hover:text-white transition-colors">Fashion Stores</a></li>
          <li><a href="beauty.php" class="hover:text-white transition-colors">Beauty & Wellness</a></li>
          <li><a href="electronics.php" class="hover:text-white transition-colors">Electronics</a></li>
          <li><a href="grocery.php" class="hover:text-white transition-colors">Grocery</a></li>
          <li><a href="d2c.php" class="hover:text-white transition-colors">D2C Brands</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Support</h4>
        <ul class="space-y-2.5 text-sm">
          <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
          <li><a href="#" class="hover:text-white transition-colors">API Reference</a></li>
          <li><a href="#" class="hover:text-white transition-colors">System Status</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Community</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Company</h4>
        <ul class="space-y-2.5 text-sm">
          <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Press Kit</a></li>
          <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-slate-800/60 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-xs text-slate-600">&copy; <?=date('Y')?> Cartt is a product of <a href="https://blumox.com" target="_blank" class="hover:text-indigo-400 transition-colors font-medium">Blumox Technologies</a>. All rights reserved.</p>
      <div class="flex gap-5 text-xs">
        <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
        <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
<script src="<?php echo USER_JS ?? 'assets/js/' ?>script.js"></script>
</body>
</html>
