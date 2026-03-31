<?php /* Cartt Plugins Marketplace */ ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Plugins Marketplace — Cartt</title>
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
        <a href="index.php#faq"      class="hover:text-indigo-600 transition-colors">FAQ</a>
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
<section class="bg-slate-900 pt-36 pb-16 sm:pb-20 text-center relative overflow-hidden">
  <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-500 via-slate-900 to-slate-900"></div>
  <div class="relative z-10 max-w-2xl mx-auto px-4">
    <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold uppercase tracking-widest border border-indigo-500/30 mb-4">Plugin Hub</span>
    <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight mb-4">Supercharge Your Store</h1>
    <p class="text-lg text-slate-400">Discover native apps and extensions designed exclusively for Indian sellers.</p>
  </div>
</section>

<!-- MARKETPLACE -->
<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-12">
  <div class="flex flex-col md:flex-row gap-8">
    
    <!-- SIDEBAR FILTERS -->
    <aside class="w-full md:w-64 shrink-0">
      <div class="sticky top-24 space-y-8">
        <!-- Search -->
        <div>
          <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Search</h3>
          <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchInput" placeholder="Find a plugin..." class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
          </div>
        </div>

        <!-- Categories -->
        <div>
           <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Category</h3>
           <div class="space-y-2" id="categoryFilters">
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="cat" value="all" checked class="text-indigo-600 focus:ring-indigo-500"> All Plugins</label>
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="cat" value="sales" class="text-indigo-600 focus:ring-indigo-500"> Sales & Conversion</label>
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="cat" value="marketing" class="text-indigo-600 focus:ring-indigo-500"> Marketing</label>
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="cat" value="logistics" class="text-indigo-600 focus:ring-indigo-500"> Logistics</label>
           </div>
        </div>

        <!-- Price -->
        <div>
           <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Pricing</h3>
           <div class="space-y-2" id="priceFilters">
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="price" value="all" checked class="text-indigo-600 focus:ring-indigo-500"> Any Price</label>
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="price" value="free" class="text-indigo-600 focus:ring-indigo-500"> Free / Included</label>
             <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"><input type="radio" name="price" value="premium" class="text-indigo-600 focus:ring-indigo-500"> Premium</label>
           </div>
        </div>
      </div>
    </aside>

    <!-- PLUGIN GRID -->
    <div class="flex-grow">
      <div id="pluginGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php
        $plugins = [
          ['id'=>1, 'name'=>'WhatsApp Flows', 'cat'=>'marketing', 'price'=>'premium', 'desc'=>"Automated order alerts and abandoned cart reminders via WhatsApp API.", 'icon'=>'w'],
          ['id'=>2, 'name'=>'COD OTP Guard', 'cat'=>'sales', 'price'=>'free', 'desc'=>"Verify COD orders with automated OTP to reduce RTO.", 'icon'=>'c'],
          ['id'=>3, 'name'=>'Shiprocket Sync', 'cat'=>'logistics', 'price'=>'free', 'desc'=>"Native real-time sync for awb generation and tracking.", 'icon'=>'s'],
          ['id'=>4, 'name'=>'Smart Upsell', 'cat'=>'sales', 'price'=>'premium', 'desc'=>"AI-based product recommendations and post-purchase upsells.", 'icon'=>'u'],
          ['id'=>5, 'name'=>'SEO Optimizer', 'cat'=>'marketing', 'price'=>'free', 'desc'=>"Auto-generate meta tags and sitemaps for Google ranking.", 'icon'=>'s'],
          ['id'=>6, 'name'=>'Delhivery API', 'cat'=>'logistics', 'price'=>'free', 'desc'=>"Direct integration with Delhivery for scheduled pickups.", 'icon'=>'d'],
        ];
        foreach($plugins as $p):
        ?>
        <div class="plugin-card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-200" data-cat="<?=$p['cat']?>" data-price="<?=$p['price']?>">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg uppercase"><?=$p['icon']?></div>
            <?php if($p['price']=='free'): ?>
              <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Free</span>
            <?php else: ?>
              <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Premium</span>
            <?php endif; ?>
          </div>
          <h3 class="font-bold text-slate-900 plugin-title"><?=$p['name']?></h3>
          <p class="text-xs text-slate-500 mt-2 leading-relaxed h-10"><?=$p['desc']?></p>
          <a href="#" class="inline-block mt-4 text-sm font-bold text-indigo-600 hover:text-indigo-800">Install &rarr;</a>
        </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Empty State -->
      <div id="emptyState" class="hidden text-center py-20">
         <p class="text-slate-400 font-medium">No plugins found matching your criteria.</p>
         <button onclick="resetFilters()" class="mt-4 text-indigo-600 text-sm font-bold underline">Clear Filters</button>
      </div>
    </div>
  </div>
</main>

<script>
  // Simple filtering logic
  const searchInput = document.getElementById('searchInput');
  const catRadios = document.querySelectorAll('input[name="cat"]');
  const priceRadios = document.querySelectorAll('input[name="price"]');
  const cards = document.querySelectorAll('.plugin-card');
  const emptyState = document.getElementById('emptyState');

  function filterPlugins() {
    const term = searchInput.value.toLowerCase();
    const activeCat = document.querySelector('input[name="cat"]:checked').value;
    const activePrice = document.querySelector('input[name="price"]:checked').value;
    let visible = 0;

    cards.forEach(card => {
      const matchSearch = card.querySelector('.plugin-title').innerText.toLowerCase().includes(term);
      const matchCat = activeCat === 'all' || card.dataset.cat === activeCat;
      const matchPrice = activePrice === 'all' || card.dataset.price === activePrice;

      if(matchSearch && matchCat && matchPrice) {
        card.style.display = 'block';
        visible++;
      } else {
        card.style.display = 'none';
      }
    });

    emptyState.classList.toggle('hidden', visible > 0);
  }

  function resetFilters() {
    searchInput.value = '';
    document.querySelector('input[name="cat"][value="all"]').checked = true;
    document.querySelector('input[name="price"][value="all"]').checked = true;
    filterPlugins();
  }

  searchInput.addEventListener('input', filterPlugins);
  catRadios.forEach(r => r.addEventListener('change', filterPlugins));
  priceRadios.forEach(r => r.addEventListener('change', filterPlugins));
</script>

<!-- FOOTER -->
<footer class="bg-[#0a0f1e] text-slate-400 pt-20 pb-12 mt-10">
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
