<?php /* Cartt Templates Gallery */ ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Templates Gallery — Cartt</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" />
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?php echo USER_STYLESHEET ?? 'assets/css/' ?>style.css" />
</head>
<body class="bg-white text-slate-900 font-sans min-h-screen flex flex-col antialiased">

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
<section class="bg-slate-50 pt-36 pb-16 sm:pb-24 text-center border-b border-slate-200">
  <div class="max-w-3xl mx-auto px-4">
    <span class="inline-block py-1 px-3 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest mb-4">Storefront Themes</span>
    <h1 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight mb-4">Start beautifully.</h1>
    <p class="text-lg text-slate-500">Pick an industry-optimized template designed for high conversion on Indian mobile devices.</p>
  </div>
</section>

<!-- TEMPLATES FEED -->
<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-12">
  
  <!-- Horizontal Filters -->
  <div class="flex items-center justify-between mb-10 pb-4 border-b border-slate-100">
    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide" id="templateFilters">
       <button class="filter-btn active bg-indigo-600 text-white px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap" data-filter="all">All Industries</button>
       <button class="filter-btn bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap" data-filter="fashion">Fashion & Apparel</button>
       <button class="filter-btn bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap" data-filter="electronics">Electronics</button>
       <button class="filter-btn bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap" data-filter="beauty">Beauty & Cosmetics</button>
       <button class="filter-btn bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap" data-filter="grocery">Grocery & Fresh</button>
    </div>
  </div>

  <!-- Grid -->
  <div id="templateGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php
    $templates = [
      ['id'=>1, 'name'=>'Aura', 'cat'=>'fashion', 'tag'=>'D2C Minimal', 'color'=>'bg-rose-100'],
      ['id'=>2, 'name'=>'TechHub', 'cat'=>'electronics', 'tag'=>'Grid Heavy', 'color'=>'bg-blue-100'],
      ['id'=>3, 'name'=>'Glow', 'cat'=>'beauty', 'tag'=>'Elegant', 'color'=>'bg-pink-100'],
      ['id'=>4, 'name'=>'FreshCart', 'cat'=>'grocery', 'tag'=>'High Throughput', 'color'=>'bg-emerald-100'],
      ['id'=>5, 'name'=>'Vogue', 'cat'=>'fashion', 'tag'=>'Editorial', 'color'=>'bg-stone-200'],
      ['id'=>6, 'name'=>'GadgetPro', 'cat'=>'electronics', 'tag'=>'Dark Mode', 'color'=>'bg-slate-800'],
    ];
    foreach($templates as $t):
    ?>
    <div class="template-card group" data-cat="<?=$t['cat']?>">
       <div class="aspect-[3/4] rounded-2xl overflow-hidden mb-4 relative border border-slate-100 shadow-sm group-hover:shadow-2xl transition-all duration-300 transform group-hover:-translate-y-2">
          <!-- Placeholder Graphic -->
          <div class="w-full h-full <?=$t['color']?> flex flex-col relative">
             <div class="bg-white/50 backdrop-blur-md px-4 py-3 border-b border-white/20 flex justify-between items-center z-10">
               <span class="w-6 h-6 rounded-md bg-white block shadow-sm"></span>
               <span class="w-16 h-2 rounded-full bg-slate-900/10 block"></span>
               <svg class="w-4 h-4 text-slate-900/30" fill="currentColor" viewBox="0 0 20 20"><path d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z"/></svg>
             </div>
             <div class="flex-grow p-4 space-y-3 z-10 flex flex-col justify-end">
                <div class="w-full h-32 bg-white/40 rounded-lg shadow-sm"></div>
                <div class="grid grid-cols-2 gap-2"><div class="h-20 bg-white/40 rounded-lg shadow-sm"></div><div class="h-20 bg-white/40 rounded-lg shadow-sm"></div></div>
             </div>
             
             <!-- Hover Overlay -->
             <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 flex items-center justify-center gap-3 transition-opacity duration-300 z-20 hidden md:flex">
               <a href="get-started.php" class="bg-white text-slate-900 font-bold px-5 py-2.5 rounded-xl text-sm hover:scale-105 transition-transform">Use Template</a>
               <button class="bg-white/20 text-white font-bold px-5 py-2.5 rounded-xl text-sm border border-white/30 hover:bg-white/30 transition-colors">Preview</button>
             </div>
          </div>
       </div>
       <div class="flex items-center justify-between px-1">
         <div>
           <h3 class="font-black text-slate-900"><?=$t['name']?></h3>
           <p class="text-xs text-slate-500 font-medium"><?=$t['tag']?></p>
         </div>
         <a href="get-started.php" class="md:hidden text-indigo-600 font-bold text-sm">Use &rarr;</a>
       </div>
    </div>
    <?php endforeach; ?>
  </div>
  
  <div id="emptyState" class="hidden text-center py-20">
    <p class="text-slate-400 font-medium pb-20">More templates coming soon for this category.</p>
  </div>
</main>

<script>
  const buttons = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.template-card');
  const emptyState = document.getElementById('emptyState');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Update active state
      buttons.forEach(b => {
        b.classList.remove('bg-indigo-600', 'text-white');
        b.classList.add('bg-slate-100', 'text-slate-700');
      });
      btn.classList.add('bg-indigo-600', 'text-white');
      btn.classList.remove('bg-slate-100', 'text-slate-700');

      // Filter
      const filter = btn.dataset.filter;
      let visible = 0;
      cards.forEach(card => {
        if (filter === 'all' || card.dataset.cat === filter) {
          card.style.display = 'block';
          visible++;
        } else {
          card.style.display = 'none';
        }
      });
      
      emptyState.classList.toggle('hidden', visible > 0);
    });
  });
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
