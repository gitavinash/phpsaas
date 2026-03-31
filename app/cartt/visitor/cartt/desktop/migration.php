<?php /* Cartt Migration Landing Page */ ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Migrate to Cartt — Fast, Free & Zero Downtime</title>
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

<main class="flex-grow">
  
  <!-- HERO -->
  <section class="pt-36 pb-20 sm:pb-32 bg-indigo-50/50 overflow-hidden relative border-b border-slate-100">
     <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
       <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-indigo-200 to-amber-200 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
     </div>
     
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
       <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100/50 text-emerald-700 text-xs font-bold uppercase tracking-widest rounded-full mb-6 border border-emerald-200">
         <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Zero Downtime Guarantee
       </span>
       <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 mb-6">
         Migrate to Cartt <br/>
         <span class="text-indigo-600">Without the Headache.</span>
       </h1>
       <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
         Moving platforms shouldn't mean losing traffic or order histories. Our experts handle the heavy lifting, migrating your store from Shopify or WooCommerce for free.
       </p>
       <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <a href="#book" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[15px] font-bold px-8 py-4 rounded-2xl shadow-xl shadow-indigo-600/20 transition-all hover:-translate-y-0.5 w-full sm:w-auto text-center">Book Free Setup Call</a>
          <a href="get-started.php" class="bg-white hover:bg-slate-50 text-slate-700 text-[15px] font-bold px-8 py-4 rounded-2xl shadow-sm border border-slate-200 transition-all hover:-translate-y-0.5 w-full sm:w-auto text-center">Try Demo Store</a>
       </div>
       
       <div class="mt-16 text-center">
         <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-200/50 pb-2 inline-block">Supported Migration Platforms</p>
         <div class="flex items-center justify-center gap-4 sm:gap-6 flex-wrap px-4">
           <!-- Shopify -->
           <div class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-sm px-6 py-3 rounded-2xl flex items-center gap-3 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 transition-all cursor-default">
             <svg class="w-6 h-6 text-[#95BF47]" fill="currentColor" viewBox="0 0 24 24"><path d="M15.337 23.979l7.216-1.561s-2.604-17.613-2.625-17.73c-.018-.116-.114-.192-.211-.192s-1.929-.136-1.929-.136-1.275-1.274-1.439-1.411c-.045-.037-.075-.057-.121-.074l-.914 21.104h.023zM11.71 11.305s-.81-.424-1.774-.424c-1.447 0-1.504.906-1.504 1.141 0 1.232 3.24 1.715 3.24 4.629 0 2.295-1.44 3.76-3.406 3.76-2.354 0-3.54-1.465-3.54-1.465l.646-2.086s1.245 1.066 2.28 1.066c.675 0 .975-.545.975-.932 0-1.619-2.654-1.694-2.654-4.359-.034-2.237 1.571-4.416 4.827-4.416 1.257 0 1.875.361 1.875.361l-.945 2.715-.02.01zM11.17.83c.136 0 .271.038.405.135-.984.465-2.064 1.639-2.508 3.992-.656.213-1.293.405-1.889.578C7.697 3.75 8.951.84 11.17.84V.83zm1.235 2.949v.135c-.754.232-1.583.484-2.394.736.466-1.777 1.333-2.645 2.085-2.971.193.501.309 1.176.309 2.1zm.539-2.234c.694.074 1.141.867 1.429 1.755-.349.114-.735.231-1.158.366v-.252c0-.752-.096-1.371-.271-1.871v.002zm2.992 1.289c-.02 0-.06.021-.078.021s-.289.075-.714.21c-.423-1.233-1.176-2.37-2.508-2.37h-.115C12.135.209 11.669 0 11.265 0 8.159 0 6.675 3.877 6.21 5.846c-1.194.365-2.063.636-2.16.674-.675.213-.694.232-.772.87-.075.462-1.83 14.063-1.83 14.063L15.009 24l.927-21.166z"/></svg>
             <span class="font-black text-slate-800 text-lg tracking-tight">Shopify</span>
           </div>
           <!-- WooCommerce -->
           <div class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-sm px-6 py-3 rounded-2xl flex items-center gap-3 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 transition-all cursor-default">
             <svg class="h-6 w-auto" viewBox="0 0 256 153" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><path d="M23.759 0h208.378C245.325 0 256 10.675 256 23.863v79.541c0 13.188-10.675 23.863-23.863 23.863H157.41l10.257 25.118-45.109-25.118H23.863c-13.187 0-23.862-10.675-23.862-23.863V23.863C-.104 10.78 10.57 0 23.759 0z" fill="#96588A"/><path d="M14.578 21.75c1.457-1.978 3.642-3.018 6.556-3.226 5.308-.417 8.326 2.08 9.054 7.492 3.226 21.75 6.764 40.17 10.51 55.259l22.79-43.395c2.082-3.955 4.684-6.036 7.806-6.244 4.579-.312 7.388 2.601 8.533 8.741 2.602 13.84 5.932 25.6 9.886 35.59 2.706-26.432 7.285-45.476 13.737-57.235 1.56-2.914 3.85-4.371 6.868-4.58 2.394-.207 4.579.521 6.556 2.082 1.977 1.561 3.018 3.538 3.226 5.932.104 1.873-.208 3.434-1.04 4.995-4.059 7.493-7.39 20.085-10.095 37.567-2.601 16.963-3.538 30.18-2.914 39.65.209 2.6-.208 4.89-1.248 6.868-1.25 2.289-3.122 3.538-5.516 3.746-2.706.208-5.515-1.04-8.221-3.85-9.678-9.887-17.379-24.664-22.998-44.332-6.765 13.32-11.76 23.31-14.986 29.97-6.14 11.76-11.343 17.796-15.714 18.108-2.81.208-5.203-2.186-7.284-7.18-5.307-13.633-11.031-39.962-17.17-78.986-.417-2.706.207-5.1 1.664-6.972zm223.636 16.338c-3.746-6.556-9.262-10.51-16.65-12.072-1.978-.416-3.85-.624-5.62-.624-9.99 0-18.107 5.203-24.455 15.61-5.412 8.845-8.117 18.627-8.117 29.346 0 8.013 1.665 14.881 4.995 20.605 3.746 6.556 9.262 10.51 16.65 12.071 1.977.417 3.85.625 5.62.625 10.094 0 18.211-5.203 24.455-15.61 5.411-8.95 8.117-18.732 8.117-29.45.104-8.117-1.665-14.882-4.995-20.501zm-13.112 28.826c-1.457 6.868-4.059 11.967-7.91 15.401-3.017 2.706-5.827 3.85-8.428 3.33-2.498-.52-4.58-2.705-6.14-6.764-1.25-3.226-1.873-6.452-1.873-9.47 0-2.601.208-5.203.728-7.596.937-4.267 2.706-8.43 5.515-12.384 3.435-5.1 7.077-7.18 10.823-6.452 2.498.52 4.58 2.706 6.14 6.764 1.249 3.226 1.873 6.452 1.873 9.47 0 2.706-.208 5.307-.728 7.7zm-52.033-28.826c-3.746-6.556-9.366-10.51-16.65-12.072-1.977-.416-3.85-.624-5.62-.624-9.99 0-18.107 5.203-24.455 15.61-5.411 8.845-8.117 18.627-8.117 29.346 0 8.013 1.665 14.881 4.995 20.605 3.746 6.556 9.262 10.51 16.65 12.071 1.978.417 3.85.625 5.62.625 10.094 0 18.211-5.203 24.455-15.61 5.412-8.95 8.117-18.732 8.117-29.45 0-8.117-1.665-14.882-4.995-20.501zm-13.216 28.826c-1.457 6.868-4.059 11.967-7.909 15.401-3.018 2.706-5.828 3.85-8.43 3.33-2.497-.52-4.578-2.705-6.14-6.764-1.248-3.226-1.872-6.452-1.872-9.47 0-2.601.208-5.203.728-7.596.937-4.267 2.706-8.43 5.516-12.384 3.434-5.1 7.076-7.18 10.822-6.452 2.498.52 4.58 2.706 6.14 6.764 1.25 3.226 1.873 6.452 1.873 9.47.105 2.706-.208 5.307-.728 7.7z" fill="#FFF"/></svg>
             <span class="font-black text-slate-800 text-lg tracking-tight">WooCommerce</span>
           </div>
           <!-- Magento -->
           <div class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-sm px-6 py-3 rounded-2xl flex items-center gap-3 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 transition-all cursor-default">
             <svg class="w-6 h-6 text-[#EE672F]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 24l-4.455-2.572v-12l2.97-1.715v12.001l1.485.902 1.485-.902V7.713l2.971 1.715v12L12 24zM22.391 6v12l-2.969 1.714V7.713L12 3.43 4.574 7.713v12.001L1.609 18V6L12 0l10.391 6z"/></svg>
             <span class="font-black text-slate-800 text-lg tracking-tight">Magento</span>
           </div>
           <!-- BigCommerce -->
           <div class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-sm px-6 py-3 rounded-2xl flex items-center gap-3 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 transition-all cursor-default">
             <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12.645 13.663h3.027c.861 0 1.406-.474 1.406-1.235 0-.717-.545-1.234-1.406-1.234h-3.027c-.1 0-.187.086-.187.172v2.125c.015.1.086.172.187.172zm0 4.896h3.128c.961 0 1.535-.488 1.535-1.35 0-.746-.545-1.35-1.535-1.35h-3.128c-.1 0-.187.087-.187.173v2.34c.015.115.086.187.187.187zM23.72.053l-8.953 8.93h1.464c2.281 0 3.63 1.435 3.63 3 0 1.235-.832 2.14-1.722 2.541-.143.058-.143.259.014.316 1.033.402 1.765 1.48 1.765 2.742 0 1.78-1.19 3.202-3.5 3.202h-6.342c-.1 0-.187-.086-.187-.172V13.85L.062 23.64c-.13.13-.043.359.143.359h23.631a.16.16 0 0 0 .158-.158V.182c.043-.158-.158-.244-.273-.13z"/></svg>
             <span class="font-black text-slate-800 text-lg tracking-tight">BigCommerce</span>
           </div>
         </div>
       </div>
     </div>
  </section>

  <!-- FEATURES BENTO GRID -->
  <section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl font-black text-slate-900 mb-4">What we move for you</h2>
      <p class="text-slate-500">A complete clone of your existing business logic, transferred securely.</p>
    </div>
    
    <div class="grid md:grid-cols-2 gap-6">
      <!-- Products -->
      <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow">
         <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-6"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
         <h3 class="text-xl font-black text-slate-900 mb-2">Products & Inventory</h3>
         <p class="text-slate-500 text-sm leading-relaxed">Prices, variations, SKUs, rich descriptions, and high-res images are pulled securely into your new Cartt catalog.</p>
      </div>
      <!-- SEO -->
      <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow">
         <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mb-6"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg></div>
         <h3 class="text-xl font-black text-slate-900 mb-2">SEO & URL Redirects</h3>
         <p class="text-slate-500 text-sm leading-relaxed">We map your old URLs to your new Cartt URLs using 301 redirects, ensuring you don't lose a single drop of Google traffic.</p>
      </div>
      <!-- Orders -->
      <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow">
         <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 mb-6"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
         <h3 class="text-xl font-black text-slate-900 mb-2">Customers & Orders</h3>
         <p class="text-slate-500 text-sm leading-relaxed">Historical orders, tracking statuses, and customer accounts are preserved. Your clients won't even notice the switch.</p>
      </div>
      <!-- Reviews -->
      <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-shadow">
         <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 mb-6"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
         <h3 class="text-xl font-black text-slate-900 mb-2">Product Reviews</h3>
         <p class="text-slate-500 text-sm leading-relaxed">Keep your hard-earned social proof. We import reviews from Judge.me, Loox, Yotpo, or standard WooCommerce comments.</p>
      </div>
    </div>
  </section>

  <!-- HOW MIGRATION WORKS PROCESS -->
  <section class="py-24 bg-slate-50 border-y border-slate-200/60 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16 max-w-2xl mx-auto">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 mb-4 block">The Process</span>
        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-6">How your migration happens</h2>
        <p class="text-slate-500 text-lg leading-relaxed">We take full accountability for your technical migration. You don’t need a developer or an agency. Our in-house experts handle it start to finish safely and transparently.</p>
      </div>

      <div class="grid lg:grid-cols-4 gap-8 relative z-10">
        <!-- Step 1 -->
        <div class="relative group bg-white border border-slate-200 p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(99,102,241,0.08)] hover:border-indigo-100 transition-all">
          <div class="w-12 h-12 bg-white border border-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-xl mb-6 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all">1</div>
          <h3 class="text-xl font-black text-slate-900 mb-3">System Audit</h3>
          <p class="text-[14px] text-slate-500 leading-relaxed font-medium">We hop on a quick screen-share to analyze your current setup, catalog size, third-party apps, and custom logic requirements.</p>
        </div>
        <!-- Step 2 -->
        <div class="relative group bg-white border border-slate-200 p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(99,102,241,0.08)] hover:border-indigo-100 transition-all">
          <div class="w-12 h-12 bg-white border border-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-xl mb-6 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all">2</div>
          <h3 class="text-xl font-black text-slate-900 mb-3">Secure Datasync</h3>
          <p class="text-[14px] text-slate-500 leading-relaxed font-medium">We securely replicate your active SKUs, live pricing, historical orders, and customer accounts in an isolated Cartt staging server.</p>
        </div>
        <!-- Step 3 -->
        <div class="relative group bg-white border border-slate-200 p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(99,102,241,0.08)] hover:border-indigo-100 transition-all">
          <div class="w-12 h-12 bg-white border border-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-xl mb-6 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all">3</div>
          <h3 class="text-xl font-black text-slate-900 mb-3">Design Matching</h3>
          <p class="text-[14px] text-slate-500 leading-relaxed font-medium">Our design engineers remap your storefront to perfectly match your brand's existing UX loop using our ultra-fast native template engine.</p>
        </div>
        <!-- Step 4 -->
        <div class="relative group bg-gradient-to-b from-indigo-50 to-white border border-indigo-100 p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(99,102,241,0.08)] hover:-translate-y-1 transition-all">
          <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-xl mb-6 shadow-lg shadow-indigo-600/30">4</div>
          <h3 class="text-xl font-black text-slate-900 mb-3">Zero-Downtime Live</h3>
          <p class="text-[14px] text-slate-500 leading-relaxed font-medium">We manage the final DNS domain flip and strict SEO 301 wildcard redirects to ensure zero lost traffic, hitting the ground running from Day 1.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section id="book" class="py-24 bg-slate-900 text-center relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_bottom,_var(--tw-gradient-stops))] from-indigo-500 via-slate-900 to-slate-900"></div>
    <div class="max-w-3xl mx-auto px-4 relative z-10">
      <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">Ready to upgrade your store?</h2>
      <p class="text-slate-400 mb-10">Book a 15-minute consultation to scope your migration. We handle technical setups for all annual plans completely free of cost.</p>
      
      <!-- Mock Form -->
      <form class="max-w-md mx-auto bg-white/5 backdrop-blur-md p-6 sm:p-8 rounded-[2rem] border border-white/10 text-left shadow-[0_20px_40px_rgba(0,0,0,0.3)] hover:border-white/20 transition-colors">
         <div class="space-y-4">
           <div>
             <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-widest mb-1.5">Full Name</label>
             <input type="text" placeholder="John Doe" class="w-full bg-slate-800/80 text-white border border-slate-700/80 rounded-xl px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-slate-800 transition-colors shadow-inner text-sm placeholder:text-slate-500">
           </div>
           <div>
             <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-widest mb-1.5">Mobile Number</label>
             <input type="tel" placeholder="+91 98765 43210" class="w-full bg-slate-800/80 text-white border border-slate-700/80 rounded-xl px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-slate-800 transition-colors shadow-inner text-sm placeholder:text-slate-500">
           </div>
           <div>
             <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-widest mb-1.5">Current Store Domain</label>
             <input type="text" placeholder="www.yourstore.com" class="w-full bg-slate-800/80 text-white border border-slate-700/80 rounded-xl px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-slate-800 transition-colors shadow-inner text-sm placeholder:text-slate-500">
           </div>
           <div>
             <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-widest mb-1.5">Current Platform</label>
             <select class="w-full bg-slate-800/80 text-white border border-slate-700/80 rounded-xl px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-slate-800 transition-colors shadow-inner text-sm appearance-none">
                <option>Shopify</option>
                <option>WooCommerce</option>
                <option>Magento</option>
                <option>BigCommerce / Wix</option>
                <option>Custom / Other</option>
             </select>
           </div>
           <button type="button" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-extrabold text-[15px] py-4 rounded-xl shadow-[0_0_20px_rgba(99,102,241,0.25)] transition-all mt-6 hover:-translate-y-0.5">Request Free Migration Call</button>
         </div>
         <p class="text-center text-[11px] font-medium text-slate-400/80 mt-6">No commitment required. 100% confidential discussion.</p>
      </form>
    </div>
  </section>

</main>

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
