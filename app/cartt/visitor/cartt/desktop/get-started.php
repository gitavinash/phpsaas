<?php
/**
 * Cartt — Get Started / Onboarding Flow
 * A high-conversion multi-step form to launch a new store.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Get Started — Cartt | Launch Your Brand in India</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.4s ease-out forwards; }
  </style>
</head>
<body class="bg-slate-50 min-h-screen font-inter flex flex-col">

  <!-- Minimal Header -->
  <header class="py-6 px-4 sm:px-8 border-b border-slate-200 bg-white">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-black text-xl">c</span>
        </div>
        <span class="text-xl font-black text-slate-900 tracking-tight">Cartt</span>
      </a>
      <div class="text-sm font-medium text-slate-500">
        Already have a store? <a href="#" class="text-indigo-600 font-bold hover:underline">Login</a>
      </div>
    </div>
  </header>

  <main class="flex-grow flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-xl">
      
      <!-- Progress Bar -->
      <div class="mb-10">
        <div class="flex items-center justify-between mb-2">
          <span id="step-label" class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Step 1 of 4</span>
          <span id="step-percent" class="text-xs font-bold text-slate-400">25% Complete</span>
        </div>
        <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
          <div id="progress-bar" class="h-full bg-indigo-600 transition-all duration-500" style="width: 25%;"></div>
        </div>
      </div>

      <!-- ONBOARDING FORM -->
      <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 p-8 sm:p-10 relative overflow-hidden">
        <form id="onboarding-form" method="POST" action="checkout.php">
          
          <!-- STEP 1: Store Vision -->
          <div id="step-1" class="onboarding-step space-y-6">
            <div class="text-center mb-8">
              <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Let's name your vision.</h1>
              <p class="text-slate-500 text-sm">Don't worry, you can change this anytime.</p>
            </div>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <label for="store_name" class="text-xs font-bold text-slate-700 uppercase tracking-widest ml-1">Store Name</label>
                <input type="text" id="store_name" name="store_name" placeholder="e.g. Meera's Boutique" required
                       class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-medium text-slate-900 shadow-sm">
              </div>
              <div class="p-5 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center gap-4">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 border border-indigo-100">
                  <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <div>
                   <div class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.12em]">Your preview domain</div>
                   <div class="text-sm font-bold text-indigo-900 opacity-80" id="domain-preview">storename.cartt.me</div>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Category -->
          <div id="step-2" class="onboarding-step space-y-6 hidden">
            <div class="text-center mb-8">
              <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">What are you selling?</h2>
              <p class="text-slate-500 text-sm">We'll pre-configure your dashboard for this industry.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <?php
              $categories = [
                ['id'=>'fashion','label'=>'Fashion & Apparel','icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                ['id'=>'beauty','label'=>'Beauty & Personal Care','icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['id'=>'electronics','label'=>'Electronics','icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['id'=>'home','label'=>'Home & Decor','icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
              ];
              foreach($categories as $cat):
              ?>
              <label class="category-card relative group cursor-pointer">
                <input type="radio" name="category" value="<?=$cat['id']?>" required class="peer absolute opacity-0">
                <div class="p-6 bg-white border border-slate-200 rounded-3xl text-center transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50/50 peer-checked:ring-4 peer-checked:ring-indigo-600/5 group-hover:border-indigo-200 shadow-sm">
                  <svg class="w-8 h-8 text-slate-400 mx-auto mb-3 transition-colors peer-checked:text-indigo-600 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?=$cat['icon']?>"/></svg>
                  <span class="block text-xs font-bold text-slate-600 peer-checked:text-indigo-900"><?=$cat['label']?></span>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- STEP 3: Logistics -->
          <div id="step-3" class="onboarding-step space-y-6 hidden">
            <div class="text-center mb-8">
              <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Build for India.</h2>
              <p class="text-slate-500 text-sm">Quick setup for the local market.</p>
            </div>
            <div class="space-y-4">
              <label class="flex items-center justify-between p-5 bg-slate-50 border border-slate-200 rounded-3xl cursor-pointer hover:border-indigo-600 transition-colors">
                 <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg></div>
                    <div class="text-left">
                       <p class="text-[15px] font-bold text-slate-900">Enable COD</p>
                       <p class="text-xs text-slate-500">Most popular payment method in India.</p>
                    </div>
                 </div>
                 <input type="checkbox" name="enable_cod" checked class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
              </label>
              <label class="flex items-center justify-between p-5 bg-slate-50 border border-slate-200 rounded-3xl cursor-pointer hover:border-indigo-600 transition-colors">
                 <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center border border-amber-100"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015.414 7H14z" /></svg></div>
                    <div class="text-left">
                       <p class="text-[15px] font-bold text-slate-900">Logistics Sync</p>
                       <p class="text-xs text-slate-500">Native Shiprocket & Delhivery sync.</p>
                    </div>
                 </div>
                 <input type="checkbox" name="logistics_sync" checked class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
              </label>
            </div>
          </div>

          <!-- STEP 4: Login Info -->
          <div id="step-4" class="onboarding-step space-y-6 hidden">
            <div class="text-center mb-8">
              <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">Secure your store.</h2>
              <p class="text-slate-500 text-sm">Final step to start selling.</p>
            </div>
            <div class="space-y-4">
               <div class="space-y-1.5">
                  <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-widest ml-1">Work Email</label>
                  <input type="email" id="email" name="email" placeholder="you@company.com" required
                         class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:border-indigo-500 transition-all outline-none font-medium shadow-sm">
               </div>
               <div class="space-y-1.5 relative">
                  <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-widest ml-1">Password</label>
                  <div class="relative">
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                           class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:border-indigo-500 transition-all outline-none font-medium shadow-sm pr-12">
                    <button type="button" id="toggle-password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
               </div>
            </div>
            
            <div class="flex items-center justify-center mt-3 mb-4">
              <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold px-3 py-1.5 rounded-full border border-emerald-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                No credit card required &middot; 14-day free trial
              </span>
            </div>
            
            <p class="text-[11px] text-slate-400 text-center leading-relaxed">By clicking 'Launch My Store', you agree to our Terms of Service and Privacy Policy.</p>
          </div>

          <!-- CONTROLS -->
          <div class="mt-12 flex items-center justify-between gap-4 border-t border-slate-100 pt-8">
            <button type="button" id="prev-btn" class="hidden text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest px-4">Previous</button>
            <div class="flex-grow"></div>
            <button type="button" id="next-btn" class="bg-indigo-600 text-white font-black px-10 py-5 rounded-2xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all text-sm uppercase tracking-[0.1em]">Next Step</button>
            <button type="submit" id="submit-btn" class="hidden bg-emerald-600 text-white font-black px-10 py-5 rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all text-sm uppercase tracking-[0.1em]">Launch My Store</button>
          </div>

        </form>
      </div>

    </div>
  </main>

  <!-- JS for multi-step -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let currentStep = 1;
      const totalSteps = 4;
      const form = document.getElementById('onboarding-form');
      const nextBtn = document.getElementById('next-btn');
      const prevBtn = document.getElementById('prev-btn');
      const submitBtn = document.getElementById('submit-btn');
      const progressBar = document.getElementById('progress-bar');
      const stepLabel = document.getElementById('step-label');
      const stepPercent = document.getElementById('step-percent');
      const storeNameInput = document.getElementById('store_name');
      const domainPreview = document.getElementById('domain-preview');

      // Sync domain preview
      storeNameInput.addEventListener('input', (e) => {
        const value = e.target.value.toLowerCase().replace(/\s+/g, '').replace(/[^\w-]/g, '');
        domainPreview.innerText = (value || 'storename') + '.cartt.me';
      });

      function updateUI() {
        // Show/hide steps
        document.querySelectorAll('.onboarding-step').forEach((el, idx) => {
          if (idx === (currentStep - 1)) {
            el.classList.remove('hidden');
            el.classList.add('fade-in');
          } else {
            el.classList.add('hidden');
            el.classList.remove('fade-in');
          }
        });

        // Update progress bar
        const perc = (currentStep / totalSteps) * 100;
        progressBar.style.width = perc + '%';
        stepLabel.innerText = `Step ${currentStep} of ${totalSteps}`;
        stepPercent.innerText = `${Math.round(perc)}% Complete`;

        // Update buttons
        prevBtn.classList.toggle('hidden', currentStep === 1);
        nextBtn.classList.toggle('hidden', currentStep === totalSteps);
        submitBtn.classList.toggle('hidden', currentStep !== totalSteps);
      }

      nextBtn.addEventListener('click', () => {
        // Basic validation
        const currentInputs = document.querySelectorAll(`#step-${currentStep} input[required]`);
        let valid = true;
        let firstInvalid = null;
        
        const radioGroups = {};
        currentInputs.forEach(input => {
          if (input.type === 'radio') {
            if(!radioGroups[input.name]) radioGroups[input.name] = [];
            radioGroups[input.name].push(input);
          } else if (!input.value) {
            valid = false;
            if(!firstInvalid) firstInvalid = input;
          }
        });

        for (const name in radioGroups) {
          const checked = radioGroups[name].some(r => r.checked);
          if(!checked) {
            valid = false;
            if(!firstInvalid) firstInvalid = radioGroups[name][0];
          }
        }
        
        if(valid) {
          if(currentStep < totalSteps) {
            currentStep++;
            updateUI();
          }
        } else {
          if(firstInvalid) firstInvalid.reportValidity();
        }
      });

      prevBtn.addEventListener('click', () => {
        if(currentStep > 1) {
          currentStep--;
          updateUI();
        }
      });

      // Password visibility toggle
      const togglePwd = document.getElementById('toggle-password');
      const pwdInput = document.getElementById('password');
      if (togglePwd && pwdInput) {
        togglePwd.addEventListener('click', () => {
          pwdInput.type = pwdInput.type === 'password' ? 'text' : 'password';
        });
      }
    });
  </script>

</body>
</html>
