<?php /* Cartt Homepage | Visitor View */ ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cartt — Modern eCommerce Platform for Indian Brands</title>
<meta name="description" content="Build your online store, accept payments, run COD, manage orders, and grow with powerful plugins. Cartt is India's complete commerce platform for serious sellers." />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" />
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
      colors: {
        brand: {50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',600:'#4f46e5',700:'#4338ca'},
        em:    {50:'#ecfdf5',500:'#10b981',600:'#059669'}
      }
    }
  }
}
</script>
<link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
</head>
<body class="bg-white text-slate-900 font-sans antialiased">

<!-- NAVBAR -->
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <a href="#" class="flex items-center gap-2 flex-shrink-0">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-500 flex items-center justify-center shadow-md">
          <span class="text-white font-black text-sm">C</span>
        </div>
        <span class="font-bold text-slate-900 text-xl tracking-tight">Cartt</span>
        <span class="hidden sm:inline-block text-xs text-slate-400 font-medium border-l border-slate-200 pl-2 ml-1">Build. Sell. Grow.</span>
      </a>
      <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-600">
        <a href="#features"  class="hover:text-indigo-600 transition-colors">Features</a>
        <a href="#plugins"   class="hover:text-indigo-600 transition-colors">Plugins</a>
        <a href="#templates" class="hover:text-indigo-600 transition-colors">Templates</a>
        <a href="#migration" class="hover:text-indigo-600 transition-colors">Migration</a>
        <a href="#pricing"   class="hover:text-indigo-600 transition-colors">Pricing</a>
        <a href="#faq"       class="hover:text-indigo-600 transition-colors">FAQ</a>
      </nav>
      <div class="hidden lg:flex items-center gap-3">
        <a href="/login" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors px-3 py-2">Login</a>
        <a href="#pricing" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all duration-200">
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
      <a href="#features"  class="py-2 hover:text-indigo-600">Features</a>
      <a href="#plugins"   class="py-2 hover:text-indigo-600">Plugins</a>
      <a href="#templates" class="py-2 hover:text-indigo-600">Templates</a>
      <a href="#migration" class="py-2 hover:text-indigo-600">Migration</a>
      <a href="#pricing"   class="py-2 hover:text-indigo-600">Pricing</a>
      <a href="#faq"       class="py-2 hover:text-indigo-600">FAQ</a>
    </nav>
    <div class="flex flex-col gap-2">
      <a href="/login" class="text-center text-sm font-medium text-slate-600 border border-slate-200 py-2.5 rounded-xl">Login</a>
      <a href="#pricing" class="text-center bg-indigo-600 text-white text-sm font-semibold py-2.5 rounded-xl">Start Selling Now</a>
    </div>
  </div>
</header>

<main>

<!-- HERO -->
<section id="hero" class="relative pt-16 overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50/40 to-white min-h-screen flex items-center">
  <!-- Ambient background blobs -->
  <div class="hero-blob w-[600px] h-[600px] bg-indigo-100/50 -top-40 -left-40"></div>
  <div class="hero-blob w-96 h-96 bg-emerald-100/30 bottom-0 right-0"></div>
  <div class="hero-blob w-64 h-64 bg-violet-100/20 top-1/3 left-1/3"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative z-10 w-full">
    <div class="grid lg:grid-cols-2 gap-16 items-center">

      <!-- LEFT CONTENT -->
      <div class="reveal">

        <!-- Eyebrow badge -->
        <div class="inline-flex items-center gap-2.5 bg-white border border-indigo-100 shadow-sm shadow-indigo-100/50 px-4 py-2 rounded-full mb-8">
          <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-ring inline-block flex-shrink-0"></span>
                    <span class="text-xs font-bold text-indigo-700 tracking-widest uppercase">Built for Indian Commerce</span>
        </div>

        <!-- Main headline -->
        <h1 class="text-[2.75rem] sm:text-5xl lg:text-[3.75rem] font-black text-slate-900 leading-[1.04] tracking-tight mb-6">
          Build Your Store.<br class="hidden sm:block"/>
          <span class="gradient-text">Start Selling&nbsp;Today.</span>
        </h1>

        <!-- Subheadline -->
        <p class="text-xl text-slate-500 leading-relaxed max-w-[480px] mb-9">
          The complete commerce platform for Indian businesses — launch, sell, manage, and grow, all without the plugin chaos.
        </p>

        <!-- CTA group -->
        <div class="flex flex-wrap gap-3.5 mb-10">
          <a href="get-started.php" class="group inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-4 rounded-2xl text-[15px] shadow-xl shadow-indigo-300/50 hover:shadow-indigo-400/60 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
            Start Selling Now
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </a>
          <a href="get-started.php" class="inline-flex items-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold px-8 py-4 rounded-2xl border border-slate-200 shadow-sm hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 text-[15px]">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            Book Free Setup Call
          </a>
        </div>

        <!-- Trust pills (icon + label) -->
        <div class="flex flex-wrap gap-2 mb-11">
          <?php
          $pills = [
            ['text'=>'0% Transaction Fees',  'cls'=>'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['text'=>'COD Ready',             'cls'=>'bg-indigo-50 text-indigo-700 border-indigo-200'],
            ['text'=>'Free Migration',        'cls'=>'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['text'=>'&#8377;5000 Setup Waived','cls'=>'bg-amber-50 text-amber-700 border-amber-200'],
          ];
          foreach($pills as $pill):
          ?>
          <span class="inline-flex items-center gap-1.5 <?=$pill['cls']?> border text-xs font-semibold px-3.5 py-1.5 rounded-full">
            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?=$pill['text']?>
          </span>
          <?php endforeach; ?>
        </div>

        <!-- Stats row -->
        <div class="flex items-center gap-8 pt-7 border-t border-slate-100">
          <div>
            <div class="text-3xl font-black text-slate-900 tracking-tight tabular-nums"><span data-count="100">0</span>+</div>
            <div class="text-[11px] text-slate-400 font-semibold mt-1 uppercase tracking-widest">Stores Launched</div>
          </div>
          <div class="w-px h-10 bg-slate-200"></div>
          <div>
            <div class="text-3xl font-black gradient-text-em tracking-tight">0%</div>
            <div class="text-[11px] text-slate-400 font-semibold mt-1 uppercase tracking-widest">Commission</div>
          </div>
          <div class="w-px h-10 bg-slate-200"></div>
          <div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">24/7</div>
            <div class="text-[11px] text-slate-400 font-semibold mt-1 uppercase tracking-widest">Setup Support</div>
          </div>
        </div>
      </div>

      <!-- RIGHT — Layered Dashboard Visual -->
      <div class="relative hidden lg:flex items-center justify-center reveal" style="min-height:560px;">

        <!-- CARD A: Main admin dashboard -->
        <div class="float-anim relative bg-white rounded-3xl overflow-hidden w-full max-w-sm z-10" style="box-shadow:0 32px 80px rgba(79,70,229,0.15),0 8px 24px rgba(0,0,0,0.06),0 0 0 1px rgba(99,102,241,0.08);">
          <!-- Browser bar -->
          <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 flex items-center gap-2">
            <div class="flex gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-white/20 inline-block"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-white/20 inline-block"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-white/20 inline-block"></span>
            </div>
            <div class="flex-1 mx-3 bg-white/12 rounded-md px-3 py-1">
              <span class="text-white/70 text-[11px] font-medium">mystore.cartt.in/admin</span>
            </div>
          </div>
          <div class="p-5">
            <!-- Store identity -->
            <div class="flex items-center gap-3 mb-5">
              <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-200">
                <span class="text-white text-xs font-black">MB</span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-slate-800">Meera's Boutique</div>
                <div class="text-xs text-slate-400">Fashion &middot; Jaipur</div>
              </div>
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Live
              </span>
            </div>
            <!-- Metrics row -->
            <div class="grid grid-cols-3 gap-2 mb-4">
              <div class="bg-indigo-50 rounded-xl p-3 text-center">
                <div class="text-[15px] font-black text-indigo-700">&#8377;84k</div>
                <div class="text-[10px] text-indigo-400 font-medium mt-0.5">Revenue</div>
              </div>
              <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <div class="text-[15px] font-black text-emerald-700">347</div>
                <div class="text-[10px] text-emerald-400 font-medium mt-0.5">Orders</div>
              </div>
              <div class="bg-violet-50 rounded-xl p-3 text-center">
                <div class="text-[15px] font-black text-violet-700">92%</div>
                <div class="text-[10px] text-violet-400 font-medium mt-0.5">Delivered</div>
              </div>
            </div>
            <!-- Mini bar chart -->
            <div class="bg-slate-50 rounded-xl p-3 mb-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">This Week</span>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">&#8593; 23%</span>
              </div>
              <div class="flex items-end gap-1 h-8">
                <?php foreach([38,52,34,68,58,80,100] as $pct): ?>
                <div class="flex-1 rounded-sm <?=$pct===100?'bg-indigo-500':'bg-indigo-200'?> transition-all" style="height:<?=$pct?>%"></div>
                <?php endforeach; ?>
              </div>
            </div>
            <!-- Product list -->
            <div class="space-y-1.5">
              <div class="flex items-center gap-2.5 bg-slate-50/80 rounded-xl px-3 py-2">
                <div class="w-7 h-7 bg-gradient-to-br from-rose-100 to-pink-200 rounded-lg flex-shrink-0"></div>
                <span class="text-xs text-slate-700 font-medium flex-1 truncate">Silk Kurta Set</span>
                <span class="text-xs font-bold text-slate-800">&#8377;2,499</span>
                <span class="text-[10px] text-emerald-600 font-bold">&#9679;</span>
              </div>
              <div class="flex items-center gap-2.5 bg-slate-50/80 rounded-xl px-3 py-2">
                <div class="w-7 h-7 bg-gradient-to-br from-amber-100 to-orange-200 rounded-lg flex-shrink-0"></div>
                <span class="text-xs text-slate-700 font-medium flex-1 truncate">Embr. Dupatta</span>
                <span class="text-xs font-bold text-slate-800">&#8377;899</span>
                <span class="text-[10px] text-emerald-600 font-bold">&#9679;</span>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD B: New order notification -->
        <div class="float-anim-slow absolute top-4 -right-10 bg-white rounded-2xl p-4 w-56 z-20" style="box-shadow:0 16px 48px rgba(0,0,0,0.10),0 0 0 1px rgba(0,0,0,0.04);">
          <div class="flex items-start gap-3">
            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-xs font-bold text-slate-800">New Order &#8212; &#8377;2,499</div>
              <div class="text-[11px] text-slate-400 mt-0.5">COD &middot; Jaipur</div>
              <div class="inline-flex items-center gap-1 mt-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> Just now
              </div>
            </div>
          </div>
        </div>

        <!-- CARD C: Payment settled -->
        <div class="float-anim absolute -bottom-4 left-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl px-5 py-3.5 z-20" style="box-shadow:0 12px 40px rgba(5,150,105,0.38);">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
              <div class="text-[10px] font-bold opacity-70 uppercase tracking-widest">Payment Settlement</div>
              <div class="text-base font-black tracking-tight">&#8377;12,450 received</div>
            </div>
          </div>
        </div>

        <!-- CARD D: Active plugins -->
        <div class="absolute -left-12 top-1/2 -translate-y-1/2 bg-white rounded-2xl p-4 w-48 z-20" style="box-shadow:0 12px 40px rgba(0,0,0,0.09),0 0 0 1px rgba(0,0,0,0.04);">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Active Plugins</div>
          <div class="flex flex-wrap gap-1.5">
            <span class="text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded-full font-semibold">Cart Recovery</span>
            <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 rounded-full font-semibold">SEO</span>
            <span class="text-[10px] bg-violet-50 text-violet-700 border border-violet-100 px-2 py-0.5 rounded-full font-semibold">WhatsApp</span>
            <span class="text-[10px] bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded-full font-semibold">Upsell</span>
            <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-semibold">+8 more</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- TRUST STRIP -->
<section class="bg-white border-y border-slate-100/80 py-9">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <p class="text-center text-[11px] font-bold text-slate-400 uppercase tracking-[0.14em] mb-6">Trusted by businesses across India</p>
    <div class="overflow-hidden [mask-image:linear-gradient(90deg,transparent,black_10%,black_90%,transparent)]">
      <div class="marquee-track flex gap-3 items-center">
        <?php foreach (['Fashion & Apparel','Beauty & Wellness','Electronics','Grocery','D2C Brands','Home Decor','Jewellery','Fitness','Organic Foods','Books & Stationery','Fashion & Apparel','Beauty & Wellness','Electronics','Grocery','D2C Brands','Home Decor','Jewellery','Fitness','Organic Foods','Books & Stationery'] as $c): ?>
        <span class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200/70 text-slate-500 text-[13px] font-medium px-4 py-2 rounded-full whitespace-nowrap hover:border-indigo-200 hover:text-indigo-600 transition-colors cursor-default">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0 inline-block"></span>
          <?= htmlspecialchars($c) ?>
        </span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ZIGZAG FEATURES (CONVERSION FLOW) -->
<section id="features" class="py-28 bg-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Row 1: Problem & Initial Solution -->
    <div class="flex flex-col lg:flex-row items-center gap-16 mb-28 reveal">
      <div class="lg:w-1/2">
        <span class="section-eyebrow">The Problem</span>
        <h2 class="text-3xl sm:text-[2.5rem] font-black text-slate-900 leading-tight mb-6">Stop the Plugin Chaos. <br/><span class="text-indigo-600">Start Selling Lean.</span></h2>
        <p class="text-[1.0625rem] text-slate-500 leading-relaxed mb-8">Most platforms require a patchwork of 20+ plugins just to handle basic Indian eCommerce needs. This slows down your store and drains your budget. Cartt is built differently.</p>
        <ul class="space-y-4 mb-8">
          <?php foreach(['Replace 20+ expensive monthly app subscriptions','Consolidate your stack into one native dashboard','Improve page speed by removing bloated scripts','Keep 100% control over your data and customer flow'] as $p): ?>
          <li class="flex items-start gap-3 text-[15px] font-semibold text-slate-700">
            <svg class="w-5 h-5 text-indigo-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <?=$p?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="get-started.php" class="btn-primary">Get Started</a>
      </div>
      <div class="lg:w-1/2 relative">
        <div class="absolute -inset-4 bg-indigo-100/50 rounded-[2.5rem] blur-2xl -z-10"></div>
        <div class="card p-8 border-indigo-100 shadow-2xl shadow-indigo-100/40 relative">
          <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-50">
            <h4 class="font-bold text-slate-900">Plugin Overload</h4>
            <span class="text-xs font-bold text-red-500 px-3 py-1 bg-red-50 rounded-full">Dangerous</span>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 opacity-60"><span class="text-sm font-medium text-slate-400 line-through">WhatsApp Flows</span> <span class="text-xs font-bold text-slate-400">$19/mo</span></div>
            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 opacity-60"><span class="text-sm font-medium text-slate-400 line-through">COD Fraud Guard</span> <span class="text-xs font-bold text-slate-400">$29/mo</span></div>
            <div class="flex justify-between items-center bg-indigo-600 p-4 rounded-xl shadow-lg shadow-indigo-200"><span class="text-sm font-bold text-white">Cartt Unified Hub</span> <span class="text-xs font-bold text-indigo-100">INCLUDED</span></div>
            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 opacity-60"><span class="text-sm font-medium text-slate-400 line-through">Product Reviews</span> <span class="text-xs font-bold text-slate-400">$15/mo</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 2: Core Platform (Zigzag Reverse) -->
    <div class="flex flex-col lg:flex-row-reverse items-center gap-16 mb-28 reveal">
      <div class="lg:w-1/2">
        <span class="section-eyebrow">The Solution</span>
        <h2 class="text-3xl sm:text-[2.5rem] font-black text-slate-900 leading-tight mb-6">Mobile-First Storefronts.<br/><span class="text-indigo-600">Built for Conversion.</span></h2>
        <p class="text-[1.0625rem] text-slate-500 leading-relaxed mb-8">90% of Indian shoppers purchase from their phones. Cartt provides a high-performance storefront designed for mobile-first behavior, including one-tap checkouts and lightning-fast loading.</p>
        <div class="grid sm:grid-cols-2 gap-6">
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-indigo-200 transition-colors">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-indigo-600 transition-all"><svg class="w-5 h-5 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
            <h4 class="font-bold text-[15px] mb-1">PWA Storefront</h4>
            <p class="text-xs text-slate-500">Your store feels like a native app on every phone.</p>
          </div>
          <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-indigo-200 transition-colors">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-indigo-600 transition-all"><svg class="w-5 h-5 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
            <h4 class="font-bold text-[15px] mb-1">One-Tap Checkout</h4>
            <p class="text-xs text-slate-500">Enable high-conversion UPI and COD flows in one tap.</p>
          </div>
        </div>
      </div>
      <div class="lg:w-1/2 relative">
        <div class="absolute -inset-4 bg-emerald-100/40 rounded-[2.5rem] blur-2xl -z-10"></div>
        <div class="bg-slate-900 rounded-3xl p-1 shadow-2xl overflow-hidden max-w-sm mx-auto">
          <div class="bg-indigo-600 h-10 w-full flex items-center justify-center gap-2"><div class="w-2 h-2 rounded-full bg-white animate-pulse"></div><span class="text-[10px] font-bold text-white uppercase tracking-widest">Storefront Live</span></div>
          <div class="p-6 bg-slate-50 space-y-4">
             <div class="h-48 rounded-xl bg-slate-200 relative overflow-hidden">
               <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-indigo-100/50 to-transparent"></div>
             </div>
             <div class="h-4 w-3/4 bg-slate-300 rounded-full"></div>
             <div class="h-10 w-full bg-emerald-500 rounded-xl flex items-center justify-center text-white text-xs font-black">BUY NOW</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 3: Growth & India-Specifics -->
    <div class="flex flex-col lg:flex-row items-center gap-16 reveal">
      <div class="lg:w-1/2">
        <span class="section-eyebrow">Scale</span>
        <h2 class="text-3xl sm:text-[2.5rem] font-black text-slate-900 leading-tight mb-6">Built for Indian Sellers.<br/><span class="text-indigo-600">Optimised for Growth.</span></h2>
        <p class="text-[1.0625rem] text-slate-500 leading-relaxed mb-8">From WhatsApp order confirmations to advanced COD verification and logistics sync, Cartt gives you the local advantages that global platforms miss.</p>
        <div class="space-y-4">
          <div class="flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-100 group-hover:bg-violet-600 group-hover:text-white transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
            <div class="text-sm">
              <div class="font-bold text-slate-900 text-sm">WhatsApp Native</div>
              <p class="text-slate-500 text-xs">Automatically send order alerts and cart reminders on WhatsApp.</p>
            </div>
          </div>
          <div class="flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="text-sm">
              <div class="font-bold text-slate-900 text-sm">Advanced COD Guard</div>
              <p class="text-slate-500 text-xs">Verify high-value COD orders using OTP and automated IVR.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="lg:w-1/2 relative">
        <div class="absolute -inset-4 bg-amber-100/30 rounded-[2.5rem] blur-2xl -z-10"></div>
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white p-6 rounded-2xl shadow-xl border border-slate-100 transform -rotate-1 translate-y-4">
            <div class="text-[10px] font-black text-slate-400 mb-4 uppercase tracking-[0.1em]">Recent Order</div>
            <div class="flex items-center gap-3 mb-4">
               <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center"><span class="text-indigo-600 text-xs font-bold">MB</span></div>
               <div class="text-[11px] font-bold">Meera's Boutique</div>
            </div>
            <div class="text-sm font-black text-slate-900">₹2,499</div>
          </div>
          <div class="bg-indigo-600 p-6 rounded-2xl shadow-xl transform rotate-2">
            <svg class="w-8 h-8 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-[10px] font-black text-indigo-100 mb-1 uppercase tracking-[0.1em]">Status</div>
            <div class="text-sm font-black text-white">COD Verified</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- PLUGIN ECOSYSTEM -->
<section id="plugins" class="py-28 bg-gradient-to-b from-indigo-950 via-indigo-900/90 to-slate-900 relative overflow-hidden">
  <div class="hero-blob w-[500px] h-[500px] bg-indigo-700/20 -top-32 -right-32"></div>
  <div class="hero-blob w-72 h-72 bg-violet-600/10 bottom-0 left-0"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center max-w-xl mx-auto mb-14 reveal">
      <span class="block text-[11px] font-bold tracking-[0.14em] uppercase text-indigo-400 mb-3">Marketplace</span>
      <h2 class="text-3xl sm:text-[2.5rem] font-black text-white tracking-tight leading-tight mb-4">Extend Your Store with Native Plugins</h2>
      <p class="text-[1.0625rem] text-indigo-200/60 leading-relaxed">Start with the essentials. Activate exactly what you need from our growing library of native, high-performance plugins.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal reveal-stagger">
      <?php
      $plugins = [
        ['cat'=>'Sales & Conversion','badge'=>'Popular','bCls'=>'bg-amber-400/20 text-amber-300 border-amber-400/30','icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z','items'=>['One-click Upsell','COD Confirmation','Cart Recovery','Gift Cards']],
        ['cat'=>'Marketing & Growth','badge'=>'Included','bCls'=>'bg-emerald-500/20 text-emerald-300 border-emerald-500/30','icon'=>'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z','items'=>['Email Automation','WhatsApp Flows','SEO Optimizer','Smart Discounts']],
        ['cat'=>'Operations & Logistics','badge'=>'Available','bCls'=>'bg-indigo-400/20 text-indigo-300 border-indigo-400/30','icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z','items'=>['Inventory Sync','Bulk Shipping','Invoice Gen','Stock Alerts']],
        ['cat'=>'Migration & Import','badge'=>'Included','bCls'=>'bg-emerald-500/20 text-emerald-300 border-emerald-500/30','icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4','items'=>['Shopify Bridge','CSV Bulk Import','SEO Redirects','Asset Manager']],
        ['cat'=>'Commerce Apps','badge'=>'New','bCls'=>'bg-violet-400/20 text-violet-300 border-violet-400/30','icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z','items'=>['Social Checkout','Pre-order Engine','Loyalty Points','Store Credit']],
        ['cat'=>'AI Intelligence','badge'=>'Add-on','bCls'=>'bg-amber-400/20 text-amber-300 border-amber-400/30','icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z','items'=>['Smart Descriptions','Demand Prediction','Auto-Tagging','Personalisation']],
      ];
      foreach($plugins as $p):
      ?>
      <div class="plugin-card group">
        <div class="flex items-start justify-between mb-5">
          <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors">
            <svg class="w-6 h-6 text-indigo-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?=$p['icon']?>"/></svg>
          </div>
          <span class="text-[10px] font-black border px-2.5 py-1 rounded-full <?=$p['bCls']?> uppercase tracking-wider"><?=$p['badge']?></span>
        </div>
        <h3 class="text-lg font-bold text-white mb-4 group-hover:text-indigo-300 transition-colors"><?=$p['cat']?></h3>
        <ul class="space-y-2.5">
          <?php foreach($p['items'] as $item): ?>
          <li class="flex items-center gap-3 text-sm text-indigo-100/60 group-hover:text-indigo-100 transition-colors">
            <div class="w-1.5 h-1.5 rounded-full bg-indigo-500/40"></div>
            <?=$item?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-20 reveal">
      <div class="inline-block relative">
        <!-- Subtle ambient glow -->
        <div class="absolute -inset-4 bg-indigo-500/20 rounded-[2rem] blur-2xl -z-10 group-hover:bg-indigo-500/30 transition-all duration-500"></div>
        
        <a href="get-started.php" class="group inline-flex items-center gap-4 bg-white text-indigo-700 font-extrabold px-12 py-5 rounded-2xl shadow-2xl hover:shadow-indigo-500/20 hover:-translate-y-1 transition-all duration-500 text-[16px] border border-indigo-100">
          Browse All 100+ Plug-ins
          <svg class="w-5 h-5 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <p class="mt-5 text-sm font-medium text-indigo-300/60 tracking-wide uppercase italic">New plug-ins added every week</p>
      </div>
    </div>
  </div>
</section>


<!-- PRICING -->
<section id="pricing" class="py-28 bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">
  <!-- Subtle ambient -->
  <div class="hero-blob w-96 h-96 bg-indigo-100/30 -top-20 right-0"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

    <!-- Section header -->
    <div class="text-center max-w-2xl mx-auto mb-12 reveal">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-4 block">Pricing</span>
      <h2 class="text-3xl sm:text-[2.75rem] font-black text-slate-900 tracking-tight leading-tight mb-4">
        Simple Pricing.<br/>
        <span class="gradient-text">        Keep 100% of Your Store Revenue.</span>
      </h2>
            <p class="text-lg text-slate-500 leading-relaxed">Pay a flat monthly or annual fee. No transaction fees from Cartt.</p>
    </div>

    <!-- Billing Toggle -->
    <div class="flex flex-col items-center gap-2 mb-8 reveal">
      <div class="relative flex items-center bg-slate-100 rounded-full p-1 w-60 shadow-inner">
        <div id="toggle-pill" class="toggle-pill"></div>
        <button id="toggle-yearly"  class="relative z-10 flex-1 text-sm font-semibold py-2.5 px-5 rounded-full transition-colors text-indigo-700">Yearly</button>
        <button id="toggle-monthly" class="relative z-10 flex-1 text-sm font-semibold py-2.5 px-5 rounded-full transition-colors text-slate-500">Monthly</button>
      </div>
      <span data-yearly-note class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold px-3.5 py-1.5 rounded-full">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        &#8377;5000 Store Setup Waived on Yearly
      </span>
      <span data-monthly-note class="hidden inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 text-xs font-semibold px-3 py-1 rounded-full">Switch to yearly to save more &amp; waive setup</span>
    </div>

    <!-- Trust trio -->
    <div class="grid sm:grid-cols-3 gap-4 max-w-3xl mx-auto mb-14 reveal">
      <div class="flex items-center gap-3 bg-white border border-emerald-100 rounded-2xl px-4 py-3.5 shadow-sm">
        <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg class="w-4.5 h-4.5 text-emerald-600" style="width:18px;height:18px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div><div class="text-sm font-bold text-slate-800">0% Transaction Fees</div><div class="text-[11px] text-slate-400 font-medium">Keep every rupee</div></div>
      </div>
      <div class="flex items-center gap-3 bg-white border border-indigo-100 rounded-2xl px-4 py-3.5 shadow-sm">
        <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg style="width:18px;height:18px" class="text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <div><div class="text-sm font-bold text-slate-800">&#8377;5000 Setup Waived</div><div class="text-[11px] text-slate-400 font-medium">On yearly plans</div></div>
      </div>
      <div class="flex items-center gap-3 bg-white border border-violet-100 rounded-2xl px-4 py-3.5 shadow-sm">
        <div class="w-9 h-9 bg-violet-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg style="width:18px;height:18px" class="text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div><div class="text-sm font-bold text-slate-800">Better Payment Rates</div><div class="text-[11px] text-slate-400 font-medium">Growth+ negotiated</div></div>
      </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid lg:grid-cols-3 gap-0 lg:gap-6 items-end reveal">
      <?php
      $ck = '<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
      $plans = [
        [
          'name'=>'Launch', 'tag'=>null,
          'for'=>'New sellers &amp; local businesses',
          'yPrice'=>'&#8377;999', 'mPrice'=>'&#8377;1,299',
          'yBilled'=>'&#8377;11,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save &#8377;3,600/yr', 'mSave'=>'Switch yearly — save &#8377;3,600',
          'features'=>['Online storefront','Payments + COD','Order management','Essential plugins','Analytics integrations','Custom domain'],
          'noteY'=>'&#8377;5000 Setup Support waived on yearly',
          'noteM'=>'&#8377;5000 Setup Support fee applies monthly',
          'cta'=>'Start for Free', 'featured'=>false
        ],
        [
          'name'=>'Growth', 'tag'=>'Most Popular',
          'for'=>'Growing brands &amp; active sellers',
          'yPrice'=>'&#8377;1,999', 'mPrice'=>'&#8377;2,499',
          'yBilled'=>'&#8377;23,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save &#8377;6,000/yr', 'mSave'=>'Switch yearly — save &#8377;6,000',
          'features'=>['Everything in Launch','More included plugins','Bulk import tools','Migration support','Advanced customisation','Priority support'],
          'noteY'=>'&#8377;5000 Setup Support waived on yearly',
          'noteM'=>'&#8377;5000 Setup Support fee applies monthly',
          'cta'=>'Start Growth Plan', 'featured'=>true
        ],
        [
          'name'=>'Scale', 'tag'=>null,
          'for'=>'D2C brands, SMEs &amp; serious sellers',
          'yPrice'=>'&#8377;3,999', 'mPrice'=>'&#8377;4,999',
          'yBilled'=>'&#8377;47,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save &#8377;12,000/yr', 'mSave'=>'Switch yearly — save &#8377;12,000',
          'features'=>['Everything in Growth','Premium plugins','Advanced workflows','Higher admin control','Priority support','Premium onboarding'],
          'noteY'=>'Premium onboarding included',
          'noteM'=>'Premium onboarding included',
          'cta'=>'Start Scale Plan', 'featured'=>false
        ],
      ];
      foreach($plans as $p):
        $f = $p['featured'];
      ?>
      <?php if($f): ?>
      <!-- GROWTH — FEATURED (elevated) -->
      <div class="pricing-card pricing-featured rounded-3xl px-8 pt-10 pb-8 flex flex-col relative z-10 shadow-2xl lg:-my-6" style="box-shadow:0 32px 80px rgba(79,70,229,0.28),0 8px 24px rgba(79,70,229,0.15);">
        <!-- Most Popular badge -->
        <div class="absolute -top-5 left-0 right-0 flex justify-center">
          <span class="bg-gradient-to-r from-amber-400 to-orange-400 text-white text-[11px] font-black uppercase tracking-widest px-5 py-2 rounded-full shadow-lg shadow-amber-200">&#9733; Most Popular</span>
        </div>
        <div class="mb-5">
          <div class="text-base font-black text-white/90 mb-0.5"><?=$p['name']?></div>
          <div class="text-sm text-indigo-200"><?=$p['for']?></div>
        </div>
        <div class="mb-3">
          <div data-yearly class="block">
            <div class="flex items-baseline gap-1.5"><span class="text-5xl font-black text-white tracking-tight"><?=$p['yPrice']?></span><span class="text-indigo-200 text-base font-semibold">/mo</span></div>
            <div class="text-indigo-300 text-xs mt-1"><?=$p['yBilled']?></div>
          </div>
          <div data-monthly class="hidden">
            <div class="flex items-baseline gap-1.5"><span class="text-5xl font-black text-white tracking-tight"><?=$p['mPrice']?></span><span class="text-indigo-200 text-base font-semibold">/mo</span></div>
            <div class="text-indigo-300 text-xs mt-1"><?=$p['mNote']?></div>
          </div>
        </div>
        <div class="mb-6">
          <span data-yearly-note class="inline-flex items-center gap-1.5 bg-white/15 text-white text-[11px] font-bold px-3 py-1.5 rounded-full"><?=$p['ySave']?></span>
          <span data-monthly-note class="hidden inline-flex items-center gap-1.5 bg-white/10 text-indigo-200 text-[11px] font-semibold px-3 py-1.5 rounded-full"><?=$p['mSave']?></span>
        </div>
        <ul class="space-y-3 mb-7 flex-1">
          <?php foreach($p['features'] as $feat): ?>
          <li class="flex items-center gap-2.5 text-sm text-indigo-100"><span class="text-emerald-300"><?=$ck?></span><?=$feat?></li>
          <?php endforeach; ?>
        </ul>
        <div class="text-xs text-indigo-300/80 pb-6 mb-6 border-b border-white/10">
          <strong data-yearly-note class="text-emerald-300"><?=$p['noteY']?></strong>
          <strong data-monthly-note class="hidden text-amber-300"><?=$p['noteM']?></strong>
        </div>
        <a href="get-started.php" class="group block text-center bg-white hover:bg-indigo-50 text-indigo-700 font-black py-4 rounded-2xl transition-all duration-200 text-sm shadow-xl shadow-indigo-900/20 hover:-translate-y-0.5">
          <?=$p['cta']?>
          <svg class="w-4 h-4 inline-block ml-1 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
      <?php else: ?>
      <!-- Standard plan -->
      <div class="pricing-card bg-white border border-slate-200 rounded-3xl px-7 pt-8 pb-7 flex flex-col shadow-sm hover:shadow-lg hover:border-indigo-100 transition-all duration-300 lg:first:rounded-r-xl lg:last:rounded-l-xl">
        <div class="mb-5">
          <div class="text-base font-black text-slate-900 mb-0.5"><?=$p['name']?></div>
          <div class="text-sm text-slate-500"><?=$p['for']?></div>
        </div>
        <div class="mb-3">
          <div data-yearly class="block">
            <div class="flex items-baseline gap-1.5"><span class="text-4xl font-black text-slate-900 tracking-tight"><?=$p['yPrice']?></span><span class="text-slate-400 text-sm font-semibold">/mo</span></div>
            <div class="text-slate-400 text-xs mt-1"><?=$p['yBilled']?></div>
          </div>
          <div data-monthly class="hidden">
            <div class="flex items-baseline gap-1.5"><span class="text-4xl font-black text-slate-900 tracking-tight"><?=$p['mPrice']?></span><span class="text-slate-400 text-sm font-semibold">/mo</span></div>
            <div class="text-slate-400 text-xs mt-1"><?=$p['mNote']?></div>
          </div>
        </div>
        <div class="mb-6">
          <span data-yearly-note class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-bold px-3 py-1.5 rounded-full"><?=$p['ySave']?></span>
          <span data-monthly-note class="hidden inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 text-[11px] font-semibold px-3 py-1 rounded-full"><?=$p['mSave']?></span>
        </div>
        <ul class="space-y-2.5 mb-6 flex-1">
          <?php foreach($p['features'] as $feat): ?>
          <li class="flex items-center gap-2.5 text-sm text-slate-600"><span class="text-emerald-500"><?=$ck?></span><?=$feat?></li>
          <?php endforeach; ?>
        </ul>
        <div class="text-xs text-slate-400 pb-5 mb-5 border-b border-slate-100">
          <strong data-yearly-note class="text-emerald-600"><?=$p['noteY']?></strong>
          <strong data-monthly-note class="hidden text-amber-600"><?=$p['noteM']?></strong>
        </div>
        <a href="get-started.php" class="group block text-center bg-slate-900 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition-all duration-200 text-sm hover:-translate-y-0.5">
          <?=$p['cta']?>
        </a>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!-- Bottom reassurance -->
    <p class="text-center text-sm text-slate-400 mt-20 reveal">All plans include a 14-day money-back guarantee &nbsp;&middot;&nbsp; No credit card required to start &nbsp;&middot;&nbsp; Cancel anytime</p>
  </div>
</section>


<!-- ADD-ONS -->
<section id="migration" class="py-28 bg-slate-50/70">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-xl mx-auto mb-12 reveal">
      <span class="section-eyebrow">Add-ons</span>
      <h2 class="section-title">Advanced Tools for Faster Growth</h2>
      <p class="section-sub">Extend your store with enterprise-grade capabilities when you're ready to scale.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal reveal-stagger">
      <div class="feature-card group">
        <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center mb-5"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
        <h3 class="font-bold text-slate-900 mb-2">WhatsApp Automation</h3>
        <p class="text-sm text-slate-500 leading-relaxed">Automated order updates, cart recovery, and customer engagement flows via WhatsApp Business API.</p>
      </div>
      <div class="feature-card group">
        <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center mb-5"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
        <h3 class="font-bold text-slate-900 mb-2">Agentic Commerce</h3>
        <p class="text-sm text-slate-500 leading-relaxed">AI agents that handle customer enquiries, personalise recommendations, and automate upsell journeys.</p>
      </div>
      <div class="feature-card group">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center mb-5"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <h3 class="font-bold text-slate-900 mb-2">Managed Setup</h3>
        <p class="text-sm text-slate-500 leading-relaxed">Our team fully sets up your store, imports products, configures payments, and launches your site for you.</p>
      </div>
      <div class="feature-card group">
        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center mb-5"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
        <h3 class="font-bold text-slate-900 mb-2">Premium Migration</h3>
        <p class="text-sm text-slate-500 leading-relaxed">White-glove migration from Shopify, WooCommerce, or WordPress with zero data loss and full SEO redirect mapping.</p>
      </div>
    </div>
    <div class="text-center mt-12 reveal">
      <a href="#contact" class="group inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5 transition-all duration-200 text-sm">
        Talk to Sales About Add-ons
        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>

<!-- FAQ -->
<section id="faq" class="py-28 bg-white">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 reveal">
      <span class="section-eyebrow">Support</span>
      <h2 class="section-title">Frequently Asked Questions</h2>
      <p class="section-sub">Quick answers to common questions about launching and growing with Cartt.</p>
    </div>
    <div class="space-y-4 reveal">
      <?php
      $faqs=[
        ['q'=>'Do you charge transaction fees?','a'=>'No. Cartt does not charge per-transaction fees on any plan. You keep 100% of your revenue. Standard payment gateway processing fees (e.g., from Razorpay) still apply at industry rates.'],
        ['q'=>'Is my data secure with Cartt?','a'=>'Absolutely. We use industry-standard SSL encryption for all data transmissions. Your store data is hosted on secure, high-availability cloud infrastructure with regular backups and proactive security monitoring.'],
        ['q'=>'Can I use a custom domain?','a'=>'Yes. You can connect your own custom domain (e.g., www.yourstore.com) to any Cartt plan. We provide free SSL certificates for all custom domains connected to the platform.'],
        ['q'=>'Do I need coding skills to build a store?','a'=>'Not at all. Cartt is a no-code platform. You can customize your storefront, manage products, and configure plugins using our intuitive visual dashboard.'],
        ['q'=>'Is the setup support fee mandatory?','a'=>'The ₹5,000 Store Launch Support is a managed service for those who want a white-glove setup. It is completely waived for all yearly plan subscribers.'],
        ['q'=>'Can I integrate with 3rd-party logistics?','a'=>'Yes. Cartt supports native integrations with leading Indian logistics providers (Shiprocket, Delhivery, etc.) for automated shipping label generation and real-time tracking.'],
        ['q'=>'What happens if I need help?','a'=>'We provide 24/7 email support for all plans. Growth and Scale plans include priority WhatsApp support and a dedicated account manager for the first 30 days.'],
      ];
      foreach($faqs as $faq):
      ?>
      <div class="faq-item border border-slate-200 rounded-2xl overflow-hidden transition-all duration-200">
        <button class="faq-trigger w-full flex items-center justify-between gap-4 px-7 py-5 text-left hover:bg-slate-50/50 transition-colors">
          <span class="font-bold text-slate-800 text-[15px] sm:text-base leading-snug"><?=$faq['q']?></span>
          <div class="faq-icon">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
          </div>
        </button>
        <div class="faq-content overflow-hidden transition-all duration-300">
          <div class="px-7 pb-6 text-[14px] sm:text-[15px] text-slate-500 leading-relaxed border-t border-slate-50 pt-4 mt-1">
            <?=$faq['a']?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Still have questions? -->
    <div class="mt-16 bg-indigo-50 rounded-3xl p-8 sm:p-10 text-center reveal">
      <h3 class="text-xl font-bold text-indigo-900 mb-3">Still have questions?</h3>
      <p class="text-indigo-700/70 mb-8 max-w-md mx-auto">Can't find the answer you're looking for? Reach out to our team for a personal consultation.</p>
      <div class="flex flex-wrap justify-center gap-4">
        <a href="get-started.php" class="bg-indigo-600 text-white font-bold px-7 py-3.5 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all text-sm">Contact Support</a>
        <a href="get-started.php" class="bg-white text-indigo-700 font-bold px-7 py-3.5 rounded-xl border border-indigo-100 shadow-sm hover:bg-indigo-50 transition-all text-sm">Book Setup Call</a>
      </div>
    </div>
  </div>
</section>


<!-- FINAL CTA -->
<section id="contact" class="py-28 bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 relative overflow-hidden">
  <div class="hero-blob w-80 h-80 bg-indigo-600/30 -top-10 -right-10"></div>
  <div class="hero-blob w-64 h-64 bg-emerald-500/10 bottom-0 left-0"></div>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
    <div class="reveal">
      <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-indigo-200 text-xs font-semibold px-4 py-1.5 rounded-full mb-6">
        <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-ring inline-block"></span>
                Modern Commerce Platform
      </span>
      <h2 class="text-3xl sm:text-[2.75rem] font-black text-white tracking-tight leading-tight mb-5">Launch Your Store.<br/><span class="text-indigo-200">Start Selling Today.</span></h2>
      <p class="text-lg text-indigo-200/80 max-w-2xl mx-auto mb-10">Launch faster, sell smarter, and grow your business with a platform built for Indian sellers.</p>
      <div class="flex flex-wrap justify-center gap-3 mb-12">
        <a href="#pricing" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-8 py-3.5 rounded-2xl hover:bg-indigo-50 transition-colors text-sm shadow-xl">
          Start Selling Now <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="inline-flex items-center gap-2 border border-white/30 text-white hover:bg-white/10 font-semibold px-8 py-3.5 rounded-2xl transition-colors text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          Book Free Setup Call
        </a>
      </div>
      <div class="flex flex-wrap justify-center gap-6">
        <?php foreach(['No coding required','Migration available','Setup support included','Plugin-powered growth'] as $t): ?>
        <div class="flex items-center gap-2 text-indigo-200 text-sm">
          <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <?=$t?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-[#0a0f1e] text-slate-400 pt-20 pb-12">
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
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Product</h4>
        <ul class="space-y-2.5 text-sm">
          <?php foreach(['Features','Plugins','Templates','Migration','Pricing','Changelog','Roadmap'] as $l): ?>
          <li><a href="#" class="hover:text-white transition-colors"><?=$l?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Solutions</h4>
        <ul class="space-y-2.5 text-sm">
          <?php foreach(['Fashion Stores','Beauty & Wellness','Electronics','Grocery','D2C Brands','Instagram Sellers','WhatsApp Commerce'] as $l): ?>
          <li><a href="#" class="hover:text-white transition-colors"><?=$l?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Support</h4>
        <ul class="space-y-2.5 text-sm">
          <?php foreach(['Help Center','Documentation','API Reference','System Status','Community','Book Setup Call','Contact Us'] as $l): ?>
          <li><a href="#" class="hover:text-white transition-colors"><?=$l?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-300 mb-4">Company</h4>
        <ul class="space-y-2.5 text-sm">
          <?php foreach(['About Us','Blog','Careers','Press Kit','Partners','Affiliates','Legal'] as $l): ?>
          <li><a href="#" class="hover:text-white transition-colors"><?=$l?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <div class="border-t border-slate-800/60 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-xs text-slate-600">&copy; <?=date('Y')?> Cartt Technologies Pvt. Ltd. All rights reserved.</p>
      <div class="flex gap-5 text-xs">
        <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
        <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
        <a href="#" class="hover:text-white transition-colors">Refund Policy</a>
        <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
      </div>
    </div>
  </div>
</footer>

</main>
<script src="<?php echo USER_JS ?>script.js"></script>
</body>
</html>
