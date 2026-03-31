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

<!-- HEADER WRAPPER -->
<div class="fixed top-0 left-0 right-0 z-[60] flex flex-col w-full">
  <!-- ANNOUNCEMENT BAR -->
  <div class="bg-indigo-600 text-white text-[13px] font-semibold text-center py-2.5 px-4 shadow-md w-full relative z-20">
    <span class="inline-flex items-center gap-2 flex-wrap justify-center">
      <span class="text-amber-300">&#127881; Limited Time Offer:</span> Get &#8377;5,000 Setup Fee Waived on Annual Plans!
      <a href="#pricing" class="underline underline-offset-2 hover:text-indigo-200 transition-colors ml-1">View Pricing &rarr;</a>
    </span>
  </div>

  <!-- NAVBAR -->
  <header id="navbar" class="bg-white/95 backdrop-blur-md border-b border-slate-100 w-full relative z-10">
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
        <a href="index.php#features" class="hover:text-indigo-600 transition-colors">Features</a>
        <a href="index.php#pricing"  class="hover:text-indigo-600 transition-colors">Pricing</a>
        <a href="migration.php"      class="hover:text-indigo-600 transition-colors">Migration</a>
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
      <a href="index.php#features" class="py-2 hover:text-indigo-600 transition-colors">Features</a>
      <a href="index.php#pricing"  class="py-2 hover:text-indigo-600 transition-colors">Pricing</a>
      <a href="migration.php"      class="py-2 hover:text-indigo-600 transition-colors">Migration</a>
    </nav>
    <div class="flex flex-col gap-2">
      <a href="/login" class="text-center text-sm font-medium text-slate-600 border border-slate-200 py-2.5 rounded-xl">Login</a>
      <a href="#pricing" class="text-center bg-indigo-600 text-white text-sm font-semibold py-2.5 rounded-xl">Start Selling Now</a>
    </div>
  </div>
</header>
</div>

<main>

<!-- HERO -->
<section id="hero" class="relative pt-28 overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50/40 to-white min-h-screen flex items-center">
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

        <!-- Hero Trust Signals -->
        <div class="flex items-center gap-4 mb-10">
          <div class="flex -space-x-2">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://i.pravatar.cc/100?img=1" alt="User 1">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://i.pravatar.cc/100?img=2" alt="User 2">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://i.pravatar.cc/100?img=3" alt="User 3">
            <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm" src="https://i.pravatar.cc/100?img=4" alt="User 4">
            <div class="w-8 h-8 rounded-full border-2 border-white shadow-sm bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">+</div>
          </div>
          <div>
            <div class="flex gap-0.5 text-amber-400 mb-0.5">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <div class="text-[11px] font-semibold text-slate-500">
              <strong class="text-slate-800">4.9/5</strong> from 100+ Indian sellers
            </div>
          </div>
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

<!-- PREMIUM BENTO FEATURES -->
<section id="features" class="py-32 bg-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="text-center max-w-2xl mx-auto mb-20 reveal">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-4 block">The Platform</span>
      <h2 class="text-4xl sm:text-[3rem] font-black text-slate-900 tracking-tight leading-tight mb-6">
        Everything you need.<br/>
        <span class="text-indigo-600">Zero plugin chaos.</span>
      </h2>
      <p class="text-lg text-slate-500 leading-relaxed">We replaced the patchwork of 20+ plugins with one beautifully engineered dashboard optimized for Indian commerce.</p>
    </div>

    <!-- BENTO GRID -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 reveal reveal-stagger">
      
      <!-- BENTO 1: Large Feature -->
      <div class="md:col-span-8 bg-slate-900 rounded-[2.5rem] p-10 lg:p-14 relative overflow-hidden group shadow-xl">
        <div class="absolute -right-20 -bottom-20 w-[400px] h-[400px] bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/30 transition-colors duration-700"></div>
        <div class="relative z-10 max-w-lg mb-12">
          <h3 class="text-3xl font-black text-white mb-4">Unified Commerce Dashboard</h3>
          <p class="text-[16px] text-slate-400 leading-relaxed">Manage inventory, process orders, and track your cashflow from a single native interface. No more jumping between disjointed apps.</p>
        </div>
        <div class="relative z-10 bg-slate-800/80 backdrop-blur-xl rounded-3xl border border-white/10 p-6 shadow-2xl translate-y-6 group-hover:translate-y-2 transition-transform duration-700 lg:mr-10">
           <!-- Mock Dashboard UI -->
           <div class="flex items-center justify-between mb-6 border-b border-white/5 pb-4">
             <div class="text-[11px] font-black uppercase tracking-widest text-indigo-400">Live Metrics</div>
             <div class="flex gap-2"><div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)]"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div></div>
           </div>
           <div class="grid grid-cols-3 gap-4">
             <div><div class="text-3xl font-black text-white">&#8377;84K</div><div class="text-[11px] font-bold text-slate-500 mt-2 uppercase tracking-wide">Sales</div></div>
             <div><div class="text-3xl font-black text-white">347</div><div class="text-[11px] font-bold text-slate-500 mt-2 uppercase tracking-wide">Orders</div></div>
             <div><div class="text-3xl font-black text-emerald-400">+24%</div><div class="text-[11px] font-bold text-slate-500 mt-2 uppercase tracking-wide">Conversion</div></div>
           </div>
        </div>
      </div>

      <!-- BENTO 2: Mobile PWA -->
      <div class="md:col-span-4 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-[2.5rem] p-10 shadow-lg relative overflow-hidden group">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-30 relative z-0"></div>
        <div class="relative z-10 flex flex-col h-full">
          <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mb-6 text-white backdrop-blur-md border border-white/20 shadow-inner">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
          </div>
          <h3 class="text-2xl font-black text-white mb-4 leading-tight">Lightning Fast <br/>Mobile PWA</h3>
          <p class="text-[15px] text-indigo-100 leading-relaxed mb-8 font-medium">90% of Indian shoppers purchase on mobile. Deliver app-like speed without the download.</p>
          <div class="mt-auto">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[11px] font-black text-white uppercase tracking-widest">Speed Score</span>
              <span class="text-sm font-black text-emerald-300">99/100</span>
            </div>
            <div class="h-2 w-full bg-white/20 rounded-full overflow-hidden flex">
              <div class="h-full bg-emerald-400 w-[99%] shadow-[0_0_10px_rgba(52,211,153,0.8)]"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- BENTO 3: App Ecosystem -->
      <div class="md:col-span-4 lg:col-span-5 bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-sm flex flex-col justify-center items-start group hover:shadow-xl transition-shadow duration-500">
        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 text-emerald-600 border border-emerald-100 group-hover:bg-emerald-100 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <h3 class="text-2xl font-black text-slate-900 mb-3">Native Indian Features</h3>
        <p class="text-[15px] text-slate-500 mb-8 leading-relaxed">Built specifically for the nuances of local commerce, integrated directly into your core storefront without external dependencies.</p>
        <div class="flex flex-wrap gap-2.5">
          <?php foreach(['COD Options','OTP Verifications','Abandoned Carts','WhatsApp Flows','One-Tap Upsell'] as $a): ?>
          <span class="bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold px-3.5 py-2 rounded-full shadow-sm group-hover:border-indigo-200 group-hover:text-indigo-700 transition-colors"><?=$a?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- BENTO 4: Growth Focus -->
      <div class="md:col-span-8 lg:col-span-7 bg-slate-50 rounded-[2.5rem] p-10 lg:p-12 border border-slate-200 shadow-sm flex items-center justify-between overflow-hidden relative group hover:shadow-xl transition-shadow duration-500">
        <div class="absolute -right-32 top-0 bottom-0 w-[400px] bg-amber-100/50 rounded-full blur-3xl group-hover:bg-indigo-100/50 transition-colors duration-700"></div>
        <div class="relative z-10 max-w-sm">
          <h3 class="text-2xl font-black text-slate-900 mb-4">Relentless Conversion</h3>
          <p class="text-[15px] text-slate-500 leading-relaxed font-medium">Includes integrated tracking pixels, zero-friction checkouts, and dynamic upselling logic perfectly mapped to consumer behavior profiles.</p>
        </div>
        <div class="relative z-10 hidden sm:flex space-x-4 pr-4">
          <div class="bg-white p-5 rounded-3xl shadow-lg transform -rotate-12 group-hover:rotate-0 transition-transform duration-500 border border-slate-100 group-hover:shadow-[0_20px_40px_rgba(99,102,241,0.15)] overflow-hidden min-w-[140px]">
            <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
              <div class="w-10 h-10 bg-slate-900 rounded-xl"></div>
              <div><div class="w-16 h-2 bg-slate-200 rounded-full mb-1"></div><div class="w-8 h-2 bg-slate-100 rounded-full"></div></div>
            </div>
            <button class="w-full bg-slate-900 block rounded-[0.5rem] py-2.5 text-[10px] text-white font-bold uppercase tracking-widest text-center mt-3 shadow-md focus:outline-none">Buy Now</button>
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

<!-- TESTIMONIALS -->
<section id="testimonials" class="py-28 bg-slate-50 border-t border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-4 block">Success Stories</span>
      <h2 class="text-3xl sm:text-[2.5rem] font-black text-slate-900 tracking-tight leading-tight mb-5">
        Join the brands growing with Cartt
      </h2>
      <p class="text-lg text-slate-500">Don't just take our word for it. Hear from sellers who escaped plugin chaos and grew their revenue.</p>
    </div>
    
    <div class="grid md:grid-cols-3 gap-6 reveal reveal-stagger">
      <?php foreach([
        ['quote'=>"Switching to Cartt saved us &#8377;15,000/mo in plugin fees. The native WhatsApp flows increased our COD conversions by 30%.",'author'=>"Aditi S.",'store'=>"Label Aditi",'img'=>"https://i.pravatar.cc/100?img=5"],
        ['quote'=>"Finally a platform that understands Indian commerce. We launched our D2C brand in 2 days. The zero transaction fee is a game changer.",'author'=>"Rohan M.",'store'=>"Brews & Beans",'img'=>"https://i.pravatar.cc/100?img=11"],
        ['quote'=>"The mobile storefront feels faster than native apps. Cartt's one-click checkout helped us double our sales during the festive season.",'author'=>"Shruti D.",'store'=>"Aura Decor",'img'=>"https://i.pravatar.cc/100?img=9"]
      ] as $t): ?>
      <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
        <div class="flex gap-1 text-amber-400 mb-6 flex-shrink-0">
          <?php for($i=0;$i<5;$i++): ?><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg><?php endfor; ?>
        </div>
        <p class="text-slate-700 text-[15px] font-medium leading-relaxed mb-8 flex-grow">"<?=$t['quote']?>"</p>
        <div class="flex items-center gap-4 mt-auto">
          <img src="<?=$t['img']?>" class="w-12 h-12 rounded-full object-cover bg-slate-100 ring-2 ring-slate-50" alt="<?=$t['author']?>">
          <div>
            <h4 class="font-bold text-slate-900 text-sm"><?=$t['author']?></h4>
            <p class="text-xs text-slate-500 font-medium"><?=$t['store']?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- PRICING -->
<section id="pricing" class="py-32 bg-white relative overflow-hidden border-t border-slate-100">

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

    <!-- Section header -->
    <div class="text-center max-w-2xl mx-auto mb-16 reveal">
      <h2 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-4">
        Simple Pricing.<br/>
        <span class="text-indigo-600">Keep 100% of Your Revenue.</span>
      </h2>
      <p class="text-lg text-slate-500 leading-relaxed">Pay a flat annual fee. Zero transaction fees. Cancel anytime.</p>
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
    <div class="grid lg:grid-cols-3 gap-0 lg:gap-6 items-center reveal max-w-5xl mx-auto">
      <?php
      $ck = '<svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
      $plans = [
        [
          'name'=>'Launch', 'tag'=>null,
          'for'=>'New businesses & entrepreneurs',
          'yPrice'=>'₹999', 'mPrice'=>'₹1,299',
          'yBilled'=>'₹11,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save ₹3,600/yr', 'mSave'=>'Switch yearly & save',
          'features'=>['Online storefront','Payments + COD integration','Order management','Core plugins included','Custom domain'],
          'noteY'=>'₹5000 Setup Waived',
          'noteM'=>'₹5000 Setup fee applies',
          'cta'=>'Start Free Trial', 'featured'=>false
        ],
        [
          'name'=>'Growth', 'tag'=>'Most Popular',
          'for'=>'Growing Indian commerce brands',
          'yPrice'=>'₹1,999', 'mPrice'=>'₹2,499',
          'yBilled'=>'₹23,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save ₹6,000/yr', 'mSave'=>'Switch yearly & save',
          'features'=>['Everything in Launch','Advanced marketing plugins','Bulk imports & exports','Free data migration','Priority support','Sales analytics'],
          'noteY'=>'₹5000 Setup Waived',
          'noteM'=>'₹5000 Setup fee applies',
          'cta'=>'Start Free Trial', 'featured'=>true
        ],
        [
          'name'=>'Scale', 'tag'=>null,
          'for'=>'Large D2C brands & enterprises',
          'yPrice'=>'₹3,999', 'mPrice'=>'₹4,999',
          'yBilled'=>'₹47,988 billed yearly', 'mNote'=>'billed monthly',
          'ySave'=>'Save ₹12,000/yr', 'mSave'=>'Switch yearly & save',
          'features'=>['Everything in Growth','Premium VIP integrations','Agentic AI workflows','Multi-warehouse sync','Dedicated success manager'],
          'noteY'=>'Premium onboarding included',
          'noteM'=>'Premium onboarding included',
          'cta'=>'Contact Sales', 'featured'=>false
        ],
      ];
      foreach($plans as $p):
        $f = $p['featured'];
      ?>
      <?php if($f): ?>
      <!-- GROWTH — FEATURED (DARK) -->
      <div class="pricing-card pricing-featured bg-slate-900 rounded-[2rem] px-8 pt-10 pb-8 flex flex-col relative z-10 lg:-my-4 shadow-xl border border-slate-800">
        <!-- Most Popular badge -->
        <div class="absolute -top-4 left-0 right-0 flex justify-center">
          <span class="bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[0.2em] px-5 py-2 rounded-full shadow-[0_0_20px_rgba(99,102,241,0.4)] border border-indigo-400">Most Popular</span>
        </div>
        <div class="mb-5 border-b border-white/10 pb-6">
          <div class="text-[17px] font-black text-white mb-1"><?=$p['name']?></div>
          <div class="text-[13px] text-slate-400"><?=$p['for']?></div>
        </div>
        <div class="mb-3">
          <div data-yearly class="block">
            <div class="flex items-baseline gap-1"><span class="text-5xl font-black text-white tracking-tight"><?=$p['yPrice']?></span><span class="text-slate-400 text-base font-medium">/mo</span></div>
            <div class="text-slate-400 text-xs mt-1 font-medium"><?=$p['yBilled']?></div>
          </div>
          <div data-monthly class="hidden">
            <div class="flex items-baseline gap-1"><span class="text-5xl font-black text-white tracking-tight"><?=$p['mPrice']?></span><span class="text-slate-400 text-base font-medium">/mo</span></div>
            <div class="text-slate-400 text-xs mt-1 font-medium"><?=$p['mNote']?></div>
          </div>
        </div>
        <div class="mb-8">
          <span data-yearly-note class="inline-flex items-center text-emerald-400 bg-emerald-400/10 text-[11px] font-bold px-2.5 py-1 rounded-full"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> <?=$p['ySave']?></span>
          <span data-monthly-note class="hidden inline-flex items-center text-slate-400 bg-white/5 text-[11px] font-bold px-2.5 py-1 rounded-full"><?=$p['mSave']?></span>
        </div>
        <ul class="space-y-4 mb-8 flex-1">
          <?php foreach($p['features'] as $feat): ?>
          <li class="flex items-start gap-3 text-[14px] text-slate-300 font-medium">
            <span class="text-indigo-400 mt-0.5"><?=$ck?></span><?=$feat?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="get-started.php" class="block w-full text-center bg-white hover:bg-slate-100 text-slate-900 font-bold py-4 rounded-xl transition-all duration-200 text-[15px]">
          <?=$p['cta']?>
        </a>
      </div>
      <?php else: ?>
      <!-- Standard plan -->
      <div class="pricing-card bg-white border border-slate-200 rounded-[2rem] px-8 pt-9 pb-8 flex flex-col shadow-sm hover:shadow-md transition-shadow">
        <div class="mb-5 border-b border-slate-100 pb-6">
          <div class="text-[17px] font-black text-slate-900 mb-1"><?=$p['name']?></div>
          <div class="text-[13px] text-slate-500"><?=$p['for']?></div>
        </div>
        <div class="mb-3">
          <div data-yearly class="block">
            <div class="flex items-baseline gap-1"><span class="text-4xl font-black text-slate-900 tracking-tight"><?=$p['yPrice']?></span><span class="text-slate-500 text-sm font-medium">/mo</span></div>
            <div class="text-slate-500 text-xs mt-1 font-medium"><?=$p['yBilled']?></div>
          </div>
          <div data-monthly class="hidden">
            <div class="flex items-baseline gap-1"><span class="text-4xl font-black text-slate-900 tracking-tight"><?=$p['mPrice']?></span><span class="text-slate-500 text-sm font-medium">/mo</span></div>
            <div class="text-slate-500 text-xs mt-1 font-medium"><?=$p['mNote']?></div>
          </div>
        </div>
        <div class="mb-8">
          <span data-yearly-note class="inline-flex items-center text-emerald-700 bg-emerald-50 text-[11px] font-bold px-2.5 py-1 rounded-full border border-emerald-100"><?=$p['ySave']?></span>
          <span data-monthly-note class="hidden inline-flex items-center text-slate-500 bg-slate-50 text-[11px] font-bold px-2.5 py-1 rounded-full border border-slate-100"><?=$p['mSave']?></span>
        </div>
        <ul class="space-y-4 mb-8 flex-1">
          <?php foreach($p['features'] as $feat): ?>
          <li class="flex items-start gap-3 text-[14px] text-slate-600 font-medium">
            <span class="text-indigo-600 mt-0.5"><?=$ck?></span><?=$feat?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="get-started.php" class="block w-full text-center bg-slate-50 hover:bg-slate-100 text-slate-900 font-bold border border-slate-200 py-3.5 rounded-xl transition-all duration-200 text-[15px]">
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

<!-- FAQ ALEYA STYLE -->
<section id="faq" class="py-32 bg-slate-50 border-t border-slate-100">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start reveal">
      <div class="lg:col-span-4 lg:sticky lg:top-32">
        <h2 class="text-3xl sm:text-[2.5rem] font-black text-slate-900 mb-4 tracking-tight leading-tight">Questions?<br/>We've got answers.</h2>
        <p class="text-slate-500 mb-8 text-lg">Can't find what you're looking for?</p>
        <a href="#contact" class="inline-flex text-[15px] font-bold text-slate-900 border-b-2 border-slate-900 hover:text-indigo-600 hover:border-indigo-600 transition-colors pb-0.5">Chat with us</a>
      </div>
      <div class="lg:col-span-8 space-y-4 text-left">
      <?php
      $faqs=[
        ['q'=>'Do you charge transaction fees?','a'=>'No. Cartt does not charge per-transaction fees on any plan. You keep 100% of your revenue. Standard payment gateway processing fees (e.g., from Razorpay or PhonePe) still apply at industry rates.'],
        ['q'=>'Is my data secure with Cartt?','a'=>'Absolutely. We use industry-standard SSL encryption for all data transmissions. Your store data is hosted on secure, high-availability cloud infrastructure with regular backups and proactive security monitoring.'],
        ['q'=>'Can I use a custom domain?','a'=>'Yes. You can connect your own custom domain (e.g., www.yourstore.com) to any Cartt plan. We provide free SSL certificates for all custom domains connected to the platform.'],
        ['q'=>'Do I need coding skills to build a store?','a'=>'Not at all. Cartt is a no-code platform. You can customize your storefront, manage products, and configure plugins using our intuitive visual dashboard.'],
        ['q'=>'Can I integrate with 3rd-party logistics?','a'=>'Yes. Cartt supports native integrations with leading Indian logistics providers like Shiprocket, Delhivery, and XpressBees for automated shipping label generation and tracking.'],
        ['q'=>'What happens after the 14-day free trial?','a'=>'If you choose not to upgrade, your store will be paused. You will not be automatically charged unless you explicitly subscribe to a paid plan. Your data is kept safe.'],
        ['q'=>'Can I migrate an existing Shopify or WooCommerce store?','a'=>'Yes. We offer a dedicated white-glove migration service with zero downtime. All products, customers, and SEO URLs are securely transferred to Cartt.'],
        ['q'=>'Does Cartt support Cash on Delivery (COD)?','a'=>'Absolutely. COD is native to Cartt, complete with automated OTP verification flows to significantly reduce RTOs (Return to Origin).'],
        ['q'=>'What payment gateways are supported?','a'=>'We integrate seamlessly with Razorpay, PhonePe, Cashfree, and PayU, allowing you to accept UPI, credit/debit cards, and net banking right out of the box.'],
        ['q'=>'Is hosting included in the pricing?','a'=>'Yes! Premium, enterprise-grade cloud hosting with unlimited bandwidth is included across all plans. You will never face surprise traffic server bills.'],
      ];
      foreach($faqs as $faq):
      ?>
      <div class="faq-item bg-white border border-slate-200 rounded-2xl overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
        <button class="faq-trigger w-full flex items-center justify-between gap-4 py-6 px-7 text-left hover:text-indigo-600 transition-colors group">
          <span class="font-bold text-slate-900 text-[18px] sm:text-lg leading-snug group-hover:text-indigo-600 transition-colors"><?=$faq['q']?></span>
          <div class="text-slate-400 group-hover:text-indigo-600 transition-colors flex-shrink-0">
            <svg class="w-6 h-6 faq-icon-svg transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
          </div>
        </button>
        <div class="faq-content overflow-hidden transition-all duration-300">
          <div class="pb-8 px-7 text-[16px] text-slate-500 leading-relaxed pr-8 pt-2">
            <?=$faq['a']?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
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
      <p class="text-xs text-slate-600">&copy; <?=date('Y')?> Cartt is a product of <a href="https://blumox.com" target="_blank" class="hover:text-indigo-400 transition-colors font-medium">Blumox Technologies</a>. All rights reserved.</p>
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

<!-- MOBILE STICKY CTA -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 bg-white/95 backdrop-blur-md border-t border-slate-200 z-[60] shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
  <a href="get-started.php" class="flex items-center justify-center w-full gap-2 bg-indigo-600 active:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition-colors text-sm">
    Start Selling Now
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
  </a>
</div>

</body>
</html>
