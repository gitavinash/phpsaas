/**
 * V3 Package Edit Module
 * Fully API-driven (Zero Inline SQL), SPA-Compatible
 * Supports Tabbed Workflow, Sticky Accordion Jumper, Day Accordions, Day Add/Remove, Media & Stay Options
 */
(function ($) {
  'use strict';

  function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(String(str)).html();
  }

  const PROPERTY_TYPES = {
    '3_star_hotel': '3 Star Hotel (Comfort)',
    '4_star_hotel': '4 Star Hotel (First Class)',
    '5_star_hotel': '5 Star Hotel (Luxury)',
    '2_star_hotel': '2 Star Hotel (Standard)',
    '1_star_hotel': '1 Star Hotel (Tourist)',
    'resorts': 'Resorts',
    'home_stay': 'Home Stay',
    'tent': 'Tent',
    'guest_house': 'Guest House',
    'apartments': 'Apartments',
    'lodge': 'Lodge',
    'inn': 'Inn',
    'hostel': 'Hostel',
    'house': 'House',
    'motel': 'Motel',
    'farm_stay': 'Farm Stay',
    'capsule_hotel': 'Capsule Hotel'
  };

  function detectStayType(stayText) {
    if (!stayText) return 'all';
    const s = String(stayText).toLowerCase().trim();
    if (s.includes('same-day') || s.includes('no stay') || s.includes('same day')) return 'none';
    if (s.includes('transit') || s.includes('night journey') || s.includes('sleeper')) return 'transit';
    if (s.includes('5 star') || s.includes('5_star') || s.includes('5-star')) return '5_star_hotel';
    if (s.includes('4 star') || s.includes('4_star') || s.includes('4-star')) return '4_star_hotel';
    if (s.includes('3 star') || s.includes('3_star') || s.includes('3-star')) return '3_star_hotel';
    if (s.includes('2 star') || s.includes('2_star') || s.includes('2-star')) return '2_star_hotel';
    if (s.includes('1 star') || s.includes('1_star') || s.includes('1-star')) return '1_star_hotel';
    if (s.includes('resort')) return 'resorts';
    if (s.includes('camp') || s.includes('tent')) return 'tent';
    if (s.includes('home stay') || s.includes('homestay') || s.includes('home_stay')) return 'home_stay';
    if (s.includes('guest house') || s.includes('guesthouse') || s.includes('guest_house')) return 'guest_house';
    if (s.includes('apartment')) return 'apartments';
    if (s.includes('villa') || s.includes('cottage') || s.includes('house')) return 'house';
    if (s.includes('hostel')) return 'hostel';
    if (s.includes('inn')) return 'inn';
    if (s.includes('motel')) return 'motel';
    if (s.includes('lodge')) return 'lodge';
    if (s.includes('farm')) return 'farm_stay';
    if (s.includes('capsule')) return 'capsule_hotel';
    return 'all';
  }

  function parseGalleryList(raw) {
    if (!raw) return [];
    if (Array.isArray(raw)) {
      return raw.map(item => {
        if (typeof item === 'string') return item.trim();
        if (item && typeof item === 'object') return (item.path || item.url || item.image || '').trim();
        return '';
      }).filter(Boolean);
    }
    if (typeof raw === 'string') {
      try {
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) return parseGalleryList(parsed);
      } catch (e) {}
      return raw.split(',').map(s => s.trim()).filter(Boolean);
    }
    return [];
  }

  function formatCommaSeparatedToLines(content) {
    if (!content) return '';
    let str = content.toString().trim();
    if (!str) return '';

    // If it's a plain string or contains comma-separated text without block tags
    if (!str.includes('<p>') && !str.includes('<li>') && !str.includes('<div>') && !str.includes('<br>') && str.includes(',')) {
      const parts = str.split(',').map(s => s.trim()).filter(Boolean);
      return parts.map(p => `<p>${escapeHtml(p)}</p>`).join('');
    }

    // If it contains paragraphs or lists with commas inside
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = str;
    const blocks = Array.from(tempDiv.querySelectorAll('p, li, div'));
    if (blocks.length > 0) {
      let modified = false;
      blocks.forEach(function (b) {
        const txt = b.textContent || '';
        if (txt.includes(',') && !b.querySelector('p, li, div')) {
          const parts = txt.split(',').map(s => s.trim()).filter(Boolean);
          if (parts.length > 1) {
            modified = true;
            const newHtml = parts.map(p => `<p>${escapeHtml(p)}</p>`).join('');
            $(b).replaceWith(newHtml);
          }
        }
      });
      if (modified) {
        return tempDiv.innerHTML;
      }
    }

    return str;
  }

  function renderDayPhotosHtml(dayNum, galleryList, featureImg) {
    galleryList = parseGalleryList(galleryList);
    if (!galleryList || !galleryList.length) {
      return `
        <div class="v3-day-empty-gallery" style="grid-column: 1 / -1;">
          <i class="fi fi-rr-picture"></i> No photos added for Day ${dayNum} yet. Click <strong>Upload Photos</strong> to select imagery.
        </div>
      `;
    }

    const activeFeat = (featureImg || '').trim();

    return galleryList.map(function (url) {
      const isFeature = Boolean(activeFeat && url === activeFeat);
      return `
        <div class="v3-day-photo-card ${isFeature ? 'is-feature-card' : ''}" data-day="${dayNum}" data-url="${escapeHtml(url)}">
          <img src="${escapeHtml(url)}" alt="Day ${dayNum} Photo" loading="lazy" onerror="this.onerror=null; this.style.opacity='0.5';" />
          ${isFeature ? '<span class="feat-ribbon"><i class="fi fi-rr-star"></i> Feature</span>' : ''}
          <div class="v3-day-photo-overlay">
            <button type="button" class="btn-day-feat-star ${isFeature ? 'is-feature' : ''}" data-day="${dayNum}" data-url="${escapeHtml(url)}" title="${isFeature ? 'Featured Image' : 'Set as Day Feature Image'}">
              <i class="fi fi-rr-star"></i> ${isFeature ? 'Featured' : 'Feature'}
            </button>
            <button type="button" class="btn-day-photo-remove" data-day="${dayNum}" data-url="${escapeHtml(url)}" title="Remove Photo">
              <i class="fi fi-rr-trash"></i>
            </button>
          </div>
        </div>
      `;
    }).join('');
  }

  function detectDayCity(dayTitle, dayOverview) {
    const pkg = (window.V3PackageEdit && window.V3PackageEdit.data && window.V3PackageEdit.data.package) || {};
    const availableCities = (window.V3PackageEdit && window.V3PackageEdit.availableCities) || [];
    const textToSearch = `${dayTitle || ''} ${dayOverview || ''}`.toLowerCase();

    // 1. Check against all known DB cities first
    for (let i = 0; i < availableCities.length; i++) {
      const c = availableCities[i];
      if (c && c.length > 2 && textToSearch.includes(c.toLowerCase())) {
        return c;
      }
    }

    // 2. Fallback to package city if specified
    if (pkg.city) {
      const cityTokens = (pkg.city || '').split(/[,;\-\/|&]+/);
      for (let i = 0; i < cityTokens.length; i++) {
        const token = cityTokens[i].trim();
        if (token && textToSearch.includes(token.toLowerCase())) {
          return token;
        }
      }
      return cityTokens[0].trim();
    }

    return '';
  }

  function getHotelsForDay(hotelsList, dayTitle, dayOverview, categoryFilter) {
    hotelsList = hotelsList || (window.V3PackageEdit && window.V3PackageEdit.hotels) || [];
    if (!hotelsList.length) return [];

    const dayCity = detectDayCity(dayTitle, dayOverview);
    const dayCityLower = (dayCity || '').toLowerCase().trim();

    // 1. Prioritize day city hotels and include nearby hotels as in hotel-list.php
    let matchedHotels = hotelsList;
    if (dayCityLower) {
      const cityMatches = hotelsList.filter(function (h) {
        const hCity = (h.city || '').toLowerCase().trim();
        return hCity === dayCityLower || hCity.includes(dayCityLower) || dayCityLower.includes(hCity);
      });
      const nearbyMatches = hotelsList.filter(function (h) {
        const hCity = (h.city || '').toLowerCase().trim();
        return !(hCity === dayCityLower || hCity.includes(dayCityLower) || dayCityLower.includes(hCity));
      });
      matchedHotels = cityMatches.concat(nearbyMatches);
    }

    // 2. Filter strictly by Property Type from vendors/property/add.php
    if (!categoryFilter || categoryFilter === 'all' || categoryFilter === 'custom' || categoryFilter === 'transit' || categoryFilter === 'none') {
      return matchedHotels;
    }

    const catKey = categoryFilter.toLowerCase().trim();
    const catMatches = matchedHotels.filter(function (h) {
      const pType = (h.type || '').toLowerCase().trim();

      // Direct exact match on property_type key from add.php
      if (pType === catKey) {
        return true;
      }

      // Normalization matches
      if (catKey === 'resorts' && (pType === 'resort' || pType === 'resorts')) return true;
      if (catKey === 'home_stay' && (pType === 'home_stay' || pType === 'homestay')) return true;
      if (catKey === 'tent' && (pType === 'tent' || pType === 'camp')) return true;
      if (catKey === 'guest_house' && (pType === 'guest_house' || pType === 'guesthouse')) return true;

      // Check title fallback if property_type is empty
      const pTitle = (h.title || '').toLowerCase();
      if (!pType && pTitle.includes(catKey.replace(/_/g, ' '))) {
        return true;
      }

      return false;
    });

    return catMatches;
  }

  function renderCategoryOptionsHtml(selectedVal) {
    selectedVal = (selectedVal || 'all').toLowerCase();

    let html = `<option value="all" ${selectedVal === 'all' ? 'selected' : ''}>All Property Types</option>`;

    html += `<optgroup label="Property Types">`;
    Object.keys(PROPERTY_TYPES).forEach(function (key) {
      const isSel = selectedVal === key ? 'selected' : '';
      html += `<option value="${key}" ${isSel}>${escapeHtml(PROPERTY_TYPES[key])}</option>`;
    });
    html += `</optgroup>`;

    html += `
      <optgroup label="Other Options">
          <option value="transit" ${selectedVal === 'transit' ? 'selected' : ''}>Night Journey / In Transit</option>
          <option value="none" ${selectedVal === 'none' ? 'selected' : ''}>Same-Day Return / No Stay</option>
          <option value="custom" ${selectedVal === 'custom' ? 'selected' : ''}>Custom Stay</option>
      </optgroup>
    `;

    return html;
  }

  function capitalizeWords(str) {
    if (!str) return '';
    return str.toString().toLowerCase().replace(/(?:^|\s|\/|-|\(|\[)[a-z]/g, function (m) {
      return m.toUpperCase();
    });
  }

  function renderHotelOptionsHtml(hotelsList, currentStay, dayCity) {
    if (!hotelsList || !hotelsList.length) return '';
    const currentStayLower = (currentStay || '').toLowerCase().trim();
    const dayCityLower = (dayCity || '').toLowerCase().trim();

    function formatOption(h) {
      const capTitle = capitalizeWords(h.title);
      const capCity = capitalizeWords(h.city || '');
      const capState = capitalizeWords(h.state || '');
      const loc = [capCity, capState].filter(Boolean).join(', ');
      const pTypeLabel = PROPERTY_TYPES[h.type] || h.type || 'Hotel';
      const displayLabel = loc ? `${capTitle} (${loc})` : capTitle;
      const val = loc ? `${capTitle}, ${loc}` : capTitle;
      const isSel = currentStayLower && (currentStayLower === val.toLowerCase() || currentStayLower.includes(h.title.toLowerCase())) ? 'selected' : '';
      return `<option value="${escapeHtml(val)}" 
                data-title="${escapeHtml(capTitle)}" 
                data-city="${escapeHtml(capCity)}" 
                data-state="${escapeHtml(capState)}" 
                data-type="${escapeHtml(h.type || '')}" 
                data-type-label="${escapeHtml(pTypeLabel)}" 
                data-thumb="${escapeHtml(h.thumbnail || '')}" 
                ${isSel}>${escapeHtml(displayLabel)}</option>`;
    }

    if (!dayCityLower) {
      return hotelsList.map(formatOption).join('');
    }

    const exactCityHotels = [];
    const nearbyHotels = [];

    hotelsList.forEach(function (h) {
      const hCity = (h.city || '').toLowerCase().trim();
      if (hCity === dayCityLower || hCity.includes(dayCityLower) || dayCityLower.includes(hCity)) {
        exactCityHotels.push(h);
      } else {
        nearbyHotels.push(h);
      }
    });

    let html = '';
    if (exactCityHotels.length > 0) {
      if (nearbyHotels.length > 0) {
        html += `<optgroup label="Hotels in ${escapeHtml(capitalizeWords(dayCity))} (${exactCityHotels.length})">`;
        html += exactCityHotels.map(formatOption).join('');
        html += `</optgroup>`;
        html += `<optgroup label="Nearby Hotels (${nearbyHotels.length})">`;
        html += nearbyHotels.map(formatOption).join('');
        html += `</optgroup>`;
      } else {
        html += exactCityHotels.map(formatOption).join('');
      }
    } else if (nearbyHotels.length > 0) {
      html += `<optgroup label="Available Hotels (${nearbyHotels.length})">`;
      html += nearbyHotels.map(formatOption).join('');
      html += `</optgroup>`;
    } else {
      html += hotelsList.map(formatOption).join('');
    }

    return html;
  }

  function formatHotelOption(hotel) {
    if (!hotel.id) {
      return hotel.text;
    }
    const $el = $(hotel.element);
    const title = capitalizeWords($el.attr('data-title') || hotel.text);
    const city = capitalizeWords($el.attr('data-city') || '');
    const state = capitalizeWords($el.attr('data-state') || '');
    const typeLabel = $el.attr('data-type-label') || '';
    const thumb = $el.attr('data-thumb') || '';

    const loc = [city, state].filter(Boolean).join(', ');

    return $(`
      <div class="v3-hotel-select2-item">
        <div class="v3-hotel-select2-thumb">
          ${thumb ? `<img src="${escapeHtml(thumb)}" alt="${escapeHtml(title)}" onerror="this.parentElement.innerHTML='<i class=\\'fi fi-rr-building\\'></i>';" />` : '<i class="fi fi-rr-building"></i>'}
        </div>
        <div class="v3-hotel-select2-info">
          <div class="v3-hotel-select2-title">${escapeHtml(title)}</div>
          <div class="v3-hotel-select2-meta">
            ${loc ? `<span class="v3-hotel-meta-pill loc-pill"><i class="fi fi-rr-marker"></i> ${escapeHtml(loc)}</span>` : ''}
            ${typeLabel ? `<span class="v3-hotel-meta-pill type-pill"><i class="fi fi-rr-bed"></i> ${escapeHtml(typeLabel)}</span>` : ''}
          </div>
        </div>
      </div>
    `);
  }

  function formatHotelSelection(hotel) {
    if (!hotel.id) {
      return hotel.text;
    }
    const $el = $(hotel.element);
    const title = capitalizeWords($el.attr('data-title') || hotel.text);
    const city = capitalizeWords($el.attr('data-city') || '');
    return city ? `${title} (${city})` : title;
  }

  const v3CityClientCache = {};

  function initCitySelect2($el) {
    if (!$el || !$el.length || typeof $.fn.select2 === 'undefined') return;

    $el.select2({
      placeholder: '-- Search or Select Stay City (Worldwide) --',
      allowClear: true,
      width: '100%',
      tags: true,
      ajax: {
        url: '/api/v3/cities/',
        dataType: 'json',
        delay: 100,
        transport: function (params, success, failure) {
          const term = (params.data && params.data.q || '').trim().toLowerCase();
          if (v3CityClientCache[term]) {
            success(v3CityClientCache[term]);
            return { abort: function () {} };
          }
          const request = $.ajax(params);
          request.then(function (data) {
            v3CityClientCache[term] = data;
            success(data);
          });
          request.fail(failure);
          return request;
        },
        data: function (params) {
          const pkg = (window.V3PackageEdit && window.V3PackageEdit.data && window.V3PackageEdit.data.package) || {};
          return {
            q: params.term || '',
            state: pkg.state || (window.V3PackageEdit && window.V3PackageEdit.packageState) || '',
            package_id: pkg.id || (window.V3PackageEdit && window.V3PackageEdit.packageId) || ''
          };
        },
        processResults: function (data) {
          return {
            results: (data && data.results) || []
          };
        },
        cache: true
      }
    });
  }

  const activeHotelRequests = {};

  function fetchDayHotels(dayNum, stayCity, stayCategory, currentVal) {
    const $hotelSelect = $(`#dayHotelSelect_${dayNum}`);
    if (!$hotelSelect.length) return;

    stayCity = (stayCity || '').trim();
    stayCategory = stayCategory || $(`#dayStayTypeSelect_${dayNum}`).val() || 'all';
    currentVal = currentVal || $(`#dayStayInput_${dayNum}`).val() || '';

    // Show loading indication and disable dropdown to prevent double click
    $hotelSelect.prop('disabled', true);
    $hotelSelect.html(`<option value="" disabled selected>⏳ Loading hotels${stayCity ? ' in ' + escapeHtml(capitalizeWords(stayCity)) : ''}...</option>`);
    if (typeof $.fn.select2 !== 'undefined') {
      $hotelSelect.trigger('change.select2');
    }

    if (activeHotelRequests[dayNum] && typeof activeHotelRequests[dayNum].abort === 'function') {
      activeHotelRequests[dayNum].abort();
    }

    const params = new URLSearchParams();
    if (stayCity) params.append('city', stayCity);
    if (stayCategory && !['all', 'custom', 'transit', 'none'].includes(stayCategory)) {
      params.append('category', stayCategory);
    }

    activeHotelRequests[dayNum] = $.ajax({
      url: `/api/v3/packages/hotels/?${params.toString()}`,
      type: 'GET',
      dataType: 'json',
      success: function (res) {
        delete activeHotelRequests[dayNum];
        $hotelSelect.prop('disabled', false);
        const hotelsList = (res && res.hotels) || [];
        const countLabel = hotelsList.length ? ` (${hotelsList.length} available)` : ' (0 available)';
        $hotelSelect.html(`<option value="">-- Choose Hotel${countLabel} --</option>` + renderHotelOptionsHtml(hotelsList, currentVal, stayCity));
        if (typeof $.fn.select2 !== 'undefined') {
          $hotelSelect.trigger('change.select2');
        }
      },
      error: function (xhr, status) {
        delete activeHotelRequests[dayNum];
        if (status === 'abort') return;
        $hotelSelect.prop('disabled', false);
        const allHotels = (window.V3PackageEdit && window.V3PackageEdit.hotels) || [];
        const dayHotels = getHotelsForDay(allHotels, $(`#dayCard_${dayNum} .v3-day-title-input`).val(), $(`#editor-${dayNum}`).val(), stayCategory);
        const countLabel = dayHotels.length ? ` (${dayHotels.length} available)` : ' (0 available)';
        $hotelSelect.html(`<option value="">-- Choose Hotel${countLabel} --</option>` + renderHotelOptionsHtml(dayHotels, currentVal, stayCity));
        if (typeof $.fn.select2 !== 'undefined') {
          $hotelSelect.trigger('change.select2');
        }
      }
    });
  }

  function renderCityOptionsHtml(selectedCity, stateCities, otherCities, stateName) {
    selectedCity = (selectedCity || '').toLowerCase().trim();
    stateCities = stateCities || (window.V3PackageEdit && window.V3PackageEdit.stateCities) || [];
    otherCities = otherCities || (window.V3PackageEdit && window.V3PackageEdit.otherCities) || [];
    stateName = stateName || (window.V3PackageEdit && window.V3PackageEdit.packageState) || 'Package State';

    function formatCityOption(c) {
      const cap = capitalizeWords(c);
      const isSel = selectedCity && (selectedCity === c.toLowerCase() || selectedCity === cap.toLowerCase()) ? 'selected' : '';
      return `<option value="${escapeHtml(cap)}" ${isSel}>${escapeHtml(cap)}</option>`;
    }

    let html = '<option value="">-- Choose Stay City --</option>';

    if (selectedCity && !stateCities.some(c => c.toLowerCase() === selectedCity) && !otherCities.some(c => c.toLowerCase() === selectedCity)) {
      html += `<option value="${escapeHtml(capitalizeWords(selectedCity))}" selected>${escapeHtml(capitalizeWords(selectedCity))}</option>`;
    }

    if (stateCities.length > 0) {
      html += `<optgroup label="Destinations in ${escapeHtml(capitalizeWords(stateName))} (${stateCities.length})">`;
      html += stateCities.map(formatCityOption).join('');
      html += `</optgroup>`;
    }

    if (otherCities.length > 0) {
      html += `<optgroup label="Other Suggested Destinations (${otherCities.length})">`;
      html += otherCities.map(formatCityOption).join('');
      html += `</optgroup>`;
    }

    return html;
  }  function buildDayCardHtml(dayNum, dayItem, hotelsList) {
    dayItem = dayItem || {};
    const dayTitle = dayItem.day || `Day ${dayNum}`;
    const dayOverview = dayItem.overview || '';
    const dayStay = dayItem.stay || '';
    const dayGalleryList = parseGalleryList(dayItem.gallery);
    const dayFeatureImg = (dayItem.feature_image || '').trim();
    const dayCity = detectDayCity(dayTitle, dayOverview);
    const initialCity = (dayStay && !dayStay.toLowerCase().includes('hotel') && !dayStay.toLowerCase().includes('resort')) ? dayStay : (dayCity || dayStay);

    return `
      <div class="v3-day-card" id="dayCard_${dayNum}" data-day="${dayNum}">
          <div class="v3-day-card-header">
              <div class="v3-day-badge">
                  <i class="fi fi-rr-sun"></i> DAY ${dayNum}
              </div>
              <div class="v3-day-title-input-wrap">
                  <label class="v3-day-input-label">Day ${dayNum} Heading / Landmark</label>
                  <input name="dayTitle[]" value="${escapeHtml(dayTitle)}" type="text" class="v3-input v3-day-title-input" placeholder="e.g. Day ${dayNum}: Arrival & Sightseeing" required />
              </div>
              <div class="v3-day-header-actions">
                  <button type="button" class="btn-day-delete" data-day="${dayNum}" title="Delete Day ${dayNum}">
                      <i class="fi fi-rr-trash"></i>
                  </button>
                  <button type="button" class="v3-day-accordion-toggle" title="Toggle Day Details">
                      <i class="fi fi-rr-angle-small-up"></i> Collapse
                  </button>
              </div>
          </div>
          <div class="v3-day-card-body">
              <!-- Stay City Option Selector -->
              <div class="v3-stay-config-panel">
                  <div class="v3-stay-panel-title">
                      <i class="fi fi-rr-marker"></i> Stay City
                  </div>
                  <div class="v3-day-stay-row">
                      <div class="v3-day-stay-field" style="flex: 1; width: 100%;">
                          <label class="v3-form-label-xs"><i class="fi fi-rr-marker"></i> Select or Search Stay City</label>
                          <select class="v3-select selectJS v3-day-city-select select2-stay-city" data-day="${dayNum}" id="dayCitySelect_${dayNum}">
                              ${renderCityOptionsHtml(initialCity, window.V3PackageEdit ? window.V3PackageEdit.stateCities : [], window.V3PackageEdit ? window.V3PackageEdit.otherCities : [], window.V3PackageEdit ? window.V3PackageEdit.packageState : '')}
                          </select>
                          <input type="hidden" name="dayStay[]" id="dayStayInput_${dayNum}" class="v3-day-stay-input" value="${escapeHtml(dayStay)}" />
                      </div>
                  </div>
              </div>

              <!-- Day Description & Schedule -->
              <div class="v3-form-field">
                  <label class="v3-form-label-sm"><i class="fi fi-rr-edit-alt"></i> Day ${dayNum} Detailed Schedule & Activities</label>
                  <div class="v3-editor-container">
                      <div class="editor" id="dayEditor_${dayNum}"></div>
                      <textarea name="dayContent[]" id="editor-${dayNum}" class="d-none">${escapeHtml(dayOverview)}</textarea>
                  </div>
              </div>

              <!-- Hidden synchronization fields for Form Submit -->
              <input type="hidden" name="dayFeatureImage[]" id="dayFeatInput_${dayNum}" class="v3-day-feat-input" value="${escapeHtml(dayFeatureImg)}" />
              <input type="hidden" name="dayGallery[]" id="dayGalInput_${dayNum}" class="v3-day-gal-input" value="${escapeHtml(JSON.stringify(dayGalleryList))}" />

              <!-- Dedicated Day Update CTA -->
              <div class="v3-day-footer-bar">
                  <button type="button" class="btn-save-day-cta" data-day="${dayNum}">
                      <i class="fi fi-rr-disk"></i> Update Day ${dayNum} Details
                  </button>
              </div>
          </div>
      </div>
    `;
  }

  // Global Helpers for direct or delegated execution
  window.jumpToDay = function (dayNum, btn) {
    if (!dayNum) return;

    // 1. Activate tab 2 (Itinerary) if not active
    const $tabBtn = $('.edit-tab-btn[data-target="#tabItinerary"]');
    if (!$('#tabItinerary').hasClass('active') || !$('#tabItinerary').is(':visible')) {
      $('.edit-tab-btn').removeClass('active');
      $tabBtn.addClass('active');
      $('.edit-tab-pane').removeClass('active').hide();
      $('#tabItinerary').addClass('active').show();
    }

    // 2. Locate target card
    const card = document.getElementById('dayCard_' + dayNum);
    if (!card) return;

    const $card = $(card);

    // 3. Expand if collapsed
    if ($card.hasClass('is-collapsed')) {
      $card.removeClass('is-collapsed');
      $card.find('.v3-day-card-body').show();
      $card.find('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
    }

    // 4. Update pill and dropdown active states
    $('.day-jump-pill').removeClass('active');
    $(`.day-jump-pill[data-day="${dayNum}"]`).addClass('active');
    $('.jumper-menu-item').removeClass('active');
    $(`.jumper-menu-item[data-day="${dayNum}"]`).addClass('active');

    $('#activeDayText').text(`Day ${dayNum}`);
    $('#jumperAccordionMenu').removeClass('is-open');
    $('#btnJumperDropdown').removeClass('is-open').attr('aria-expanded', 'false');

    // 5. Scroll smoothly to the target day card
    try {
      card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {}

    const cardOffset = $card.offset();
    if (cardOffset) {
      const targetTop = Math.max(0, cardOffset.top - 120);
      $('html, body, main, .app-main').stop().animate({ scrollTop: targetTop }, 250);
    }

    // 6. Highlight pulse effect
    $card.addClass('day-card-highlight');
    setTimeout(function () {
      $card.removeClass('day-card-highlight');
    }, 1500);
  };

  window.toggleDayCard = function (btnOrCard) {
    const $card = $(btnOrCard).closest('.v3-day-card');
    const $body = $card.find('.v3-day-card-body');
    const $toggleBtn = $card.find('.v3-day-accordion-toggle');

    if ($card.hasClass('is-collapsed')) {
      $card.removeClass('is-collapsed');
      $body.slideDown(200);
      $toggleBtn.html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
    } else {
      $card.addClass('is-collapsed');
      $body.slideUp(200);
      $toggleBtn.html('<i class="fi fi-rr-angle-small-down"></i> Expand');
    }
  };

  window.toggleAllDayCards = function (btn) {
    const $cards = $('.v3-day-card');
    const hasCollapsed = $cards.hasClass('is-collapsed');

    if (hasCollapsed) {
      $cards.removeClass('is-collapsed');
      $cards.find('.v3-day-card-body').slideDown(200);
      $('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
      $(btn).html('<i class="fi fi-rr-compress-alt"></i> Collapse All');
    } else {
      $cards.addClass('is-collapsed');
      $cards.find('.v3-day-card-body').slideUp(200);
      $('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-down"></i> Expand');
      $(btn).html('<i class="fi fi-rr-expand-arrows-alt"></i> Expand All');
    }
  };

  window.addNewDay = function () {
    const totalDays = $('.v3-day-card').length;
    const newDayNum = totalDays + 1;
    const dayItem = {
      day: `Day ${newDayNum} - New Itinerary Day`,
      overview: '',
      stay: '',
      feature_image: '',
      gallery: []
    };

    const cardHtml = buildDayCardHtml(newDayNum, dayItem, window.V3PackageEdit ? window.V3PackageEdit.hotels : []);
    $('#daysContainer').append(cardHtml);

    // Init Quill Editor on new container
    if (typeof Quill !== 'undefined') {
      const el = document.getElementById(`dayEditor_${newDayNum}`);
      if (el && !$(el).hasClass('ql-container')) {
        const qDay = new Quill(el, { theme: 'snow' });
        window.V3PackageEdit.editors = window.V3PackageEdit.editors || {};
        window.V3PackageEdit.editors[`dayEditor_${newDayNum}`] = qDay;
        qDay.on('text-change', function () {
          $(`#editor-${newDayNum}`).val(qDay.root.innerHTML);
        });
      }
    }

    // Init Select2 on new Stay City dropdown
    if (typeof $.fn.select2 !== 'undefined') {
      initCitySelect2($(`#dayCitySelect_${newDayNum}`));
    }

    // Update Counts, Jumper Menu & Durations on the UI
    window.reindexDayCards();

    // Scroll to the newly added day form and focus title input
    window.jumpToDay(newDayNum);
    const $newCard = $(`#dayCard_${newDayNum}`);
    setTimeout(function () {
      $newCard.find('.v3-day-title-input').focus().select();
    }, 250);
  };

  window.removeDay = function (dayNum) {
    const $cards = $('.v3-day-card');
    const pkgId = $('#hiddenPackageId').val() || (window.V3PackageEdit && window.V3PackageEdit.packageId);

    if ($cards.length <= 1) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Cannot Delete',
          text: 'A tour package itinerary must have at least one day.',
          type: 'warning'
        });
      } else if (typeof popUpMessage === 'function') {
        popUpMessage('center', 'A tour package itinerary must have at least one day.', 'warning');
      } else {
        alert('A tour package itinerary must have at least one day.');
      }
      return;
    }

    const performDelete = function () {
      const $targetCard = $(`#dayCard_${dayNum}`);
      $targetCard.css('opacity', '0.5');

      if (pkgId) {
        $.ajax({
          url: '/api/v3/packages/delete-day/',
          type: 'POST',
          data: {
            packageId: pkgId,
            dayNumber: dayNum
          },
          dataType: 'json',
          success: function (res) {
            $targetCard.fadeOut(200, function () {
              $targetCard.remove();
              window.reindexDayCards();
              const msg = (res && res.message) ? res.message : `Day ${dayNum} has been successfully deleted from the itinerary.`;
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', msg, 'success');
              }
            });
          },
          error: function (xhr) {
            $targetCard.css('opacity', '1');
            let errMsg = `Failed to delete Day ${dayNum}. Please try again.`;
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errMsg, 'error');
            } else {
              alert(errMsg);
            }
          }
        });
      } else {
        $targetCard.fadeOut(200, function () {
          $targetCard.remove();
          window.reindexDayCards();
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', `Day ${dayNum} removed from the itinerary.`, 'success');
          }
        });
      }
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: `Delete Day ${dayNum}?`,
        text: `Are you sure you want to remove Day ${dayNum} from the itinerary? This will update the total tour duration.`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: `Yes, Delete Day ${dayNum}`,
        cancelButtonText: 'Cancel'
      }).then(result => {
        if (result && (result.value || result.isConfirmed)) {
          performDelete();
        }
      });
    } else if (confirm(`Are you sure you want to delete Day ${dayNum} from the itinerary?`)) {
      performDelete();
    }
  };

  window.reindexDayCards = function () {
    const $cards = $('.v3-day-card');
    const totalDays = $cards.length;
    const $pillList = $('#daysPillList');
    const $menuItems = $('#jumperMenuItems');
    $pillList.empty();
    $menuItems.empty();

    $cards.each(function (idx) {
      const dayNum = idx + 1;
      const $card = $(this);
      const dayTitle = $card.find('.v3-day-title-input').val() || `Day ${dayNum}`;

      $card.attr('id', `dayCard_${dayNum}`).attr('data-day', dayNum);
      $card.find('.v3-day-badge').html(`<i class="fi fi-rr-sun"></i> DAY ${dayNum}`);
      $card.find('.v3-day-input-label').text(`Day ${dayNum} Heading / Landmark`);
      $card.find('.btn-day-delete').attr('data-day', dayNum).attr('title', `Delete Day ${dayNum}`);
      
      const $editorWrap = $card.find('.v3-editor-container');
      $editorWrap.find('.editor').attr('id', `dayEditor_${dayNum}`);
      $editorWrap.find('textarea').attr('id', `editor-${dayNum}`);

      $card.find('.v3-day-city-select').attr('id', `dayCitySelect_${dayNum}`).attr('data-day', dayNum);
      $card.find('.v3-day-stay-type-select').attr('id', `dayStayTypeSelect_${dayNum}`).attr('data-day', dayNum);
      $card.find('.v3-day-hotel-select').attr('id', `dayHotelSelect_${dayNum}`).attr('data-day', dayNum);
      $card.find('.v3-day-stay-input').attr('id', `dayStayInput_${dayNum}`);
      $card.find('.v3-day-bulk-file-input').attr('data-day', dayNum);
      $card.find('.v3-day-photos-grid').attr('id', `dayGalleryGrid_${dayNum}`);
      $card.find('.v3-day-feat-input').attr('id', `dayFeatInput_${dayNum}`);
      $card.find('.v3-day-gal-input').attr('id', `dayGalInput_${dayNum}`);
      $card.find('.v3-day-photo-card').attr('data-day', dayNum);
      $card.find('.btn-day-feat-star').attr('data-day', dayNum);
      $card.find('.btn-day-photo-remove').attr('data-day', dayNum);

      // Quick Pill
      $pillList.append(`
        <button type="button" data-day="${dayNum}" data-no-spa="true" class="day-jump-pill ${dayNum === 1 ? 'active' : ''}">Day ${dayNum}</button>
      `);

      // Jumper Accordion Menu Item
      $menuItems.append(`
        <button type="button" data-day="${dayNum}" data-no-spa="true" class="jumper-menu-item ${dayNum === 1 ? 'active' : ''}">
            <span class="jumper-item-badge">Day ${dayNum}</span>
            <span class="jumper-item-title">${escapeHtml(dayTitle)}</span>
        </button>
      `);
    });

    const calculatedDuration = Math.max(0, totalDays - 1);
    $('#tabItineraryCountBadge').text(`${totalDays} Days`);
    $('#jumperMenuDayCount').text(`${totalDays} Days`);
    $('#hiddenDuration').val(calculatedDuration);
    $('#pkgMetaDuration').html(`<i class="fi-rr-moon icon-moon"></i> ${calculatedDuration}N / <i class="fi-rr-sun icon-sun"></i> ${totalDays}D`);
  };

  window.V3PackageEdit = {
    packageId: null,
    encodedId: null,
    packageType: 'domestic',
    data: null,
    hotels: [],
    availableCities: [],
    stateCities: [],
    otherCities: [],
    packageState: '',
    isLoading: false,
    hotelsLoading: false,
    activeXhr: null,
    hotelsXhr: null,
    eventsBound: false,
    dayPendingFiles: {},

    autoInit: function () {
      const $app = $('#packageEditApp, .package-edit-wrapper');
      if ($app.length > 0) {
        this.init();
      } else {
        this.packageId = null;
        this.encodedId = null;
        this.data = null;
        this.hotels = [];
        this.availableCities = [];
        this.stateCities = [];
        this.otherCities = [];
        this.packageState = '';
        this.isLoading = false;
        this.hotelsLoading = false;
        this.activeXhr = null;
        this.hotelsXhr = null;
        this.dayPendingFiles = {};
      }
    },

    init: function () {
      const $app = $('#packageEditApp, .package-edit-wrapper');
      if (!$app.length) return;

      let pkgId = $app.data('package-id') || $app.attr('data-package-id');
      let encodedId = $app.data('encoded-id') || $app.attr('data-encoded-id');
      const pkgType = $app.data('package-type') || $app.attr('data-package-type') || 'domestic';

      if (!pkgId || pkgId === '0' || pkgId === 0) {
        const urlParams = new URLSearchParams(window.location.search);
        const queryId = urlParams.get('id');
        if (queryId) {
          encodedId = queryId;
          try {
            const dec = atob(queryId);
            if (!isNaN(dec) && parseInt(dec) > 0) {
              pkgId = parseInt(dec);
            } else {
              pkgId = queryId;
            }
          } catch (e) {
            pkgId = queryId;
          }
        } else {
          // Parse last segment from pathname if not in query params
          const pathParts = window.location.pathname.split('/').filter(Boolean);
          const lastSeg = pathParts[pathParts.length - 1];
          if (lastSeg && lastSeg !== 'edit' && lastSeg !== 'domestic' && lastSeg !== 'international') {
            encodedId = lastSeg;
            try {
              const dec = atob(lastSeg);
              if (!isNaN(dec) && parseInt(dec) > 0) {
                pkgId = parseInt(dec);
              } else {
                pkgId = lastSeg;
              }
            } catch (e) {
              pkgId = lastSeg;
            }
          }
        }
      }

      if (!pkgId || String(pkgId) === '0') {
        this.renderError('Invalid package ID specified.');
        return;
      }

      this.packageId = pkgId;
      this.encodedId = encodedId;
      this.packageType = pkgType;
      this.bindEvents();
      this.eventsBound = true;
      this.loadPackageData(pkgId);
    },

    loadPackageData: function (pkgId) {
      const self = this;

      if (this.isLoading) {
        return;
      }

      if (this.activeXhr && typeof this.activeXhr.abort === 'function') {
        this.activeXhr.abort();
        this.activeXhr = null;
      }

      this.isLoading = true;
      $('#packageEditLoading').show();
      $('#packageEditError').hide();
      $('#packageEditContent').hide();

      this.activeXhr = $.ajax({
        url: `/api/v3/packages/get/?id=${encodeURIComponent(pkgId)}`,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          self.isLoading = false;
          self.activeXhr = null;
          if (res && res.status === 'success' && res.package) {
            self.data = res;
            self.packageData = res;
            self.renderPackage(res);
          } else {
            self.renderError(res.message || 'Failed to load tour package details.');
          }
        },
        error: function (xhr, status, err) {
          self.isLoading = false;
          self.activeXhr = null;
          if (status === 'abort') {
            return;
          }
          let msg = 'Failed to load tour package data. Please refresh.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          self.renderError(msg);
        }
      });
    },

    loadHotels: function (city) {
      const self = this;
      if (this.hotelsXhr && typeof this.hotelsXhr.abort === 'function') {
        this.hotelsXhr.abort();
        this.hotelsXhr = null;
      }

      this.hotelsLoading = true;
      const queryParams = new URLSearchParams();
      if (city) queryParams.append('city', city);

      this.hotelsXhr = $.ajax({
        url: `/api/v3/packages/hotels/?${queryParams.toString()}`,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          self.hotelsLoading = false;
          self.hotelsXhr = null;
          if (res && res.status === 'success') {
            self.hotels = res.hotels || [];
            self.availableCities = res.cities || [];
            self.stateCities = res.state_cities || [];
            self.otherCities = res.other_cities || [];
            self.packageState = res.state || state || '';

            // Update all stay city dropdowns with state-prioritized suggestions without firing duplicate change AJAX
            $('.v3-day-card').each(function () {
              const $card = $(this);
              const dayNum = $card.data('day');
              const $citySelect = $(`#dayCitySelect_${dayNum}`);
              const currentCity = $citySelect.val() || detectDayCity($card.find('.v3-day-title-input').val(), $(`#editor-${dayNum}`).val());
              $citySelect.html(renderCityOptionsHtml(currentCity, self.stateCities, self.otherCities, self.packageState));
            });

            self.refreshAllDayHotelSelects();
          }
        },
        error: function () {
          self.hotelsLoading = false;
          self.hotelsXhr = null;
        }
      });
    },

    refreshAllDayHotelSelects: function () {
      const self = this;
      $('.v3-day-card').each(function () {
        const $card = $(this);
        const dayNum = $card.data('day');
        const dayTitle = $card.find('.v3-day-title-input').val() || `Day ${dayNum}`;
        const dayOverview = $(`#editor-${dayNum}`).val() || '';
        const categoryFilter = $card.find('.v3-day-stay-type-select').val() || 'all';
        const currentStay = $card.find('.v3-day-stay-input').val() || '';
        const dayCity = ($(`#dayCitySelect_${dayNum}`).val() || detectDayCity(dayTitle, dayOverview) || '').trim();
        const dayHotels = getHotelsForDay(self.hotels, dayTitle, dayOverview, categoryFilter);

        const $hotelSelect = $(`#dayHotelSelect_${dayNum}`);
        $hotelSelect.html(`<option value="">-- Choose Hotel (${dayHotels.length} available) --</option>` + renderHotelOptionsHtml(dayHotels, currentStay, dayCity));
        if (typeof $.fn.select2 !== 'undefined') {
          $hotelSelect.trigger('change.select2');
        }

        // Update city tag badge in stay panel title
        const $badgeWrap = $card.find('.v3-stay-panel-title');
        $badgeWrap.find('.v3-badge-city-tag').remove();
        if (dayCity) {
          $badgeWrap.append(`<span class="v3-badge-city-tag" style="margin-left:auto; font-size:11px; font-weight:600; color:#0284c7; background:#e0f2fe; padding:2px 8px; border-radius:12px;"><i class="fi fi-rr-marker"></i> ${escapeHtml(capitalizeWords(dayCity))}</span>`);
        }
      });
    },

    renderError: function (msg) {
      $('#packageEditLoading').hide();
      $('#packageEditContent').hide();
      $('#packageEditError').show();
      $('#packageErrorText').text(msg);
    },

    renderPackage: function (res) {
      const self = this;
      const pkg = res.package;
      const vehicles = res.vehicles || [];
      const travelStyles = res.travel_styles || {};
      const hotelTypes = res.hotel_types || {};

      // 1. Header Meta & Actions
      $('#pkgHeaderTitle').text(pkg.title || 'Tour Package');
      
      const themeClean = (pkg.theme || 'Standard').toLowerCase().replace(/[^a-z0-9]/g, '');
      $('#pkgMetaTheme').attr('class', 'pkg-theme-pill pkg-theme-' + themeClean).text(pkg.theme ? (pkg.theme.charAt(0).toUpperCase() + pkg.theme.slice(1)) : 'Standard');
      
      $('#pkgMetaDuration').html(`<i class="fi-rr-moon icon-moon"></i> ${pkg.nights}N / <i class="fi-rr-sun icon-sun"></i> ${pkg.days}D`);
      
      const locationText = pkg.type === 'international' ? (pkg.country || pkg.city || 'International') : (pkg.city || 'Domestic');
      $('#pkgMetaLocation').html(`<i class="fi-rr-marker icon-marker"></i> ${locationText}`);

      // Website Publishing Checkbox (default to true if new/undefined/true)
      const isPublished = (pkg.is_published !== false);
      $('#chkPublishToWebsite').prop('checked', isPublished);

      if (pkg.link_url && isPublished) {
        $('#btnViewLivePackage').attr('href', pkg.link_url).show();
      } else {
        $('#btnViewLivePackage').hide();
      }

      const listUrl = pkg.type === 'international' ? '/dashboard/packages/international/list/' : '/dashboard/packages/domestic/list/';
      const galleryUrl = pkg.type === 'international' ? `/dashboard/packages/international/gallery/?id=${pkg.encoded_id}` : `/dashboard/packages/domestic/gallery/?id=${pkg.encoded_id}`;
      
      $('#btnManageGallery').attr('href', galleryUrl);
      $('#btnBackToList').attr('href', listUrl);
      $('#btnCancelEdit').attr('href', listUrl);
      $('#tabItineraryCountBadge').text(`${pkg.itinerary.length} Days`);

      // 2. Hidden Inputs
      $('#packageType').val(pkg.type);
      $('#hiddenPackageId').val(pkg.encoded_id);
      $('#hiddenDuration').val(pkg.duration);

      // 3. Tab 1: General & Logistics Fields
      $('#name').val(pkg.title);

      // Travel Styles Dropdown
      const $travelStyle = $('#travelStyle');
      $travelStyle.empty().append('<option value="select">Select travel style</option>');
      $.each(travelStyles, function (k, val) {
        const isSel = (pkg.theme && pkg.theme.toLowerCase() === k.toLowerCase()) ? 'selected' : '';
        $travelStyle.append(`<option value="${k}" ${isSel}>${val}</option>`);
      });

      // Template Selection Cards
      this.selectTemplate(pkg.template || 'mit');

      // Hotel Types Dropdown
      const $hotel = $('#hotel');
      $hotel.empty().append('<option value="select">Select hotel type</option>');
      $.each(hotelTypes, function (k, val) {
        const isSel = (pkg.hotel && pkg.hotel.toLowerCase() === k.toLowerCase()) ? 'selected' : '';
        $hotel.append(`<option value="${k}" ${isSel}>${val}</option>`);
      });

      // Vehicles Dropdowns
      const $outstation = $('#outstationVehicle');
      const $localsightseen = $('#localSighseeignVehicle');
      $outstation.empty().append('<option value="select">Select vehicle</option>');
      $localsightseen.empty().append('<option value="select">Select vehicle</option>');

      vehicles.forEach(function (v) {
        const typeLabel = v.type ? ` (${v.type.replace(/_/g, ' ')})` : '';
        const isOutSel = (pkg.outstation_vehicle && pkg.outstation_vehicle.toLowerCase() === v.title.toLowerCase()) ? 'selected' : '';
        const isLocSel = (pkg.localsightseen_vehicle && pkg.localsightseen_vehicle.toLowerCase() === v.title.toLowerCase()) ? 'selected' : '';

        $outstation.append(`<option value="${v.title}" ${isOutSel}>${v.title}${typeLabel}</option>`);
        $localsightseen.append(`<option value="${v.title}" ${isLocSel}>${v.title}${typeLabel}</option>`);
      });

      // 4. Tab 2: Day-Wise Itinerary
      const $pillList = $('#daysPillList');
      const $menuItems = $('#jumperMenuItems');
      const $daysContainer = $('#daysContainer');
      $pillList.empty();
      $menuItems.empty();
      $daysContainer.empty();

      const itineraryList = pkg.itinerary || [];

      itineraryList.forEach(function (dayItem, idx) {
        const dayNum = idx + 1;
        const dayTitle = dayItem.day || `Day ${dayNum}`;

        $pillList.append(`
          <button type="button" data-day="${dayNum}" data-no-spa="true" class="day-jump-pill ${dayNum === 1 ? 'active' : ''}">Day ${dayNum}</button>
        `);

        $menuItems.append(`
          <button type="button" data-day="${dayNum}" data-no-spa="true" class="jumper-menu-item ${dayNum === 1 ? 'active' : ''}">
              <span class="jumper-item-badge">Day ${dayNum}</span>
              <span class="jumper-item-title">${escapeHtml(dayTitle)}</span>
          </button>
        `);

        $daysContainer.append(buildDayCardHtml(dayNum, dayItem, self.hotels));
      });

      $('#jumperMenuDayCount').text(`${itineraryList.length} Days`);

      // Inclusions & Exclusions
      $('#editor-inclusion').val(pkg.inclusions || '');
      $('#editor-exclusion').val(pkg.exclusions || '');
      $('#editor-0').val(pkg.overview || '');

      // 5. Initialize Quill Rich Text Editors
      this.initEditors(pkg, itineraryList);

      // 6. Initialize Select2
      this.initSelects();

      // 7. Show Form Content
      $('#packageEditLoading').hide();
      $('#packageEditContent').fadeIn(150);
    },

    initEditors: function (pkg, itineraryList) {
      if (typeof Quill === 'undefined') return;
      window.V3PackageEdit.editors = window.V3PackageEdit.editors || {};

      // 1. Overview Editor
      const overviewContainer = document.getElementById('overviewEditor');
      if (overviewContainer && !$(overviewContainer).hasClass('ql-container')) {
        const qOverview = new Quill(overviewContainer, { theme: 'snow' });
        window.V3PackageEdit.editors.overviewEditor = qOverview;
        if (pkg && pkg.overview) {
          qOverview.root.innerHTML = pkg.overview;
        }
        qOverview.on('text-change', function () {
          $('#editor-0').val(qOverview.root.innerHTML);
        });
      }

      // 2. Day-Wise Editors
      if (itineraryList && itineraryList.length) {
        itineraryList.forEach(function (dayItem, idx) {
          const dayNum = idx + 1;
          const el = document.getElementById(`dayEditor_${dayNum}`);
          if (el && !$(el).hasClass('ql-container')) {
            const qDay = new Quill(el, { theme: 'snow' });
            window.V3PackageEdit.editors[`dayEditor_${dayNum}`] = qDay;
            if (dayItem.overview) {
              qDay.root.innerHTML = dayItem.overview;
            }
            qDay.on('text-change', function () {
              $(`#editor-${dayNum}`).val(qDay.root.innerHTML);
            });
          }
        });
      }

      // Helper to initialize and bind comma-to-newline logic for list editors
      function setupListEditor(elementId, hiddenInputId, initialText) {
        const el = document.getElementById(elementId);
        if (!el || $(el).hasClass('ql-container')) return null;

        const quill = new Quill(el, { theme: 'snow' });
        window.V3PackageEdit.editors[elementId] = quill;

        // Auto-format initial text with commas to new lines
        const formatted = formatCommaSeparatedToLines(initialText || '');
        if (formatted) {
          quill.root.innerHTML = formatted;
          $(hiddenInputId).val(quill.root.innerHTML);
        }

        quill.on('text-change', function () {
          $(hiddenInputId).val(quill.root.innerHTML);
        });

        // Intercept comma key to create new line
        $(el).find('.ql-editor').on('keydown', function (e) {
          if (e.key === ',' || e.keyCode === 188) {
            e.preventDefault();
            const range = quill.getSelection(true);
            if (range) {
              quill.insertText(range.index, '\n', Quill.sources.USER);
              quill.setSelection(range.index + 1, Quill.sources.SILENT);
            }
          }
        });

        // Intercept pasted text with commas
        $(el).find('.ql-editor').on('paste', function (e) {
          const clipboardData = e.originalEvent && e.originalEvent.clipboardData;
          if (clipboardData) {
            const pastedText = clipboardData.getData('text/plain');
            if (pastedText && pastedText.includes(',')) {
              e.preventDefault();
              const formattedLines = pastedText.split(',').map(s => s.trim()).filter(Boolean).join('\n');
              const range = quill.getSelection(true);
              if (range) {
                quill.insertText(range.index, formattedLines, Quill.sources.USER);
                quill.setSelection(range.index + formattedLines.length, Quill.sources.SILENT);
              }
            }
          }
        });

        return quill;
      }

      // 3. Inclusions Editor
      setupListEditor('inclusionsEditor', '#editor-inclusion', pkg ? pkg.inclusions : '');

      // 4. Exclusions Editor
      setupListEditor('exclusionsEditor', '#editor-exclusion', pkg ? pkg.exclusions : '');
    },

    initSelects: function () {
      if (typeof $.fn.select2 !== 'undefined') {
        if ($('#packageEditApp .selectJS:not(.select2-stay-city)').length) {
          $('#packageEditApp .selectJS:not(.select2-stay-city)').select2({
            placeholder: 'Select option',
            width: '100%'
          });
        }
        if ($('.select2-stay-city').length) {
          initCitySelect2($('.select2-stay-city'));
        }
      }
    },

    bindEvents: function () {
      const self = this;
      // 1. Tab Switching
      $(document).off('click.v3pkgTab', '.edit-tab-btn');
      $(document).on('click.v3pkgTab', '.edit-tab-btn', function (e) {
        e.preventDefault();
        const target = $(this).attr('data-target') || $(this).data('target');
        if (!target) return;

        $('.edit-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.edit-tab-pane').removeClass('active').hide();
        $(target).addClass('active').fadeIn(150);

        window.scrollTo({ top: 0, behavior: 'smooth' });
      });

      // Next / Prev Tab Buttons
      $(document).off('click.v3pkgNext', '.btn-v3-next-tab');
      $(document).on('click.v3pkgNext', '.btn-v3-next-tab', function (e) {
        e.preventDefault();
        const nextTarget = $(this).attr('data-next') || $(this).data('next');
        if (nextTarget) {
          $(`.edit-tab-btn[data-target="${nextTarget}"]`).trigger('click');
        }
      });

      $(document).off('click.v3pkgPrev', '.btn-v3-prev-tab');
      $(document).on('click.v3pkgPrev', '.btn-v3-prev-tab', function (e) {
        e.preventDefault();
        const prevTarget = $(this).attr('data-prev') || $(this).data('prev');
        if (prevTarget) {
          $(`.edit-tab-btn[data-target="${prevTarget}"]`).trigger('click');
        }
      });

      // 2. Accordion Jumper Toggle & Click
      $(document).off('click.v3pkgJumperToggle', '#btnJumperDropdown');
      $(document).on('click.v3pkgJumperToggle', '#btnJumperDropdown', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $menu = $('#jumperAccordionMenu');
        const isOpen = $menu.hasClass('is-open');
        if (isOpen) {
          $menu.removeClass('is-open');
          $(this).removeClass('is-open').attr('aria-expanded', 'false');
        } else {
          $menu.addClass('is-open');
          $(this).addClass('is-open').attr('aria-expanded', 'true');
        }
      });

      // Close jumper dropdown on outside click
      $(document).off('click.v3pkgJumperOutside');
      $(document).on('click.v3pkgJumperOutside', function (e) {
        if (!$(e.target).closest('.v3-jumper-dropdown-wrap').length) {
          $('#jumperAccordionMenu').removeClass('is-open');
          $('#btnJumperDropdown').removeClass('is-open').attr('aria-expanded', 'false');
        }
      });

      // 3. Day Jump Pills & Accordion Menu Items
      $(document).off('click.v3pkgJump', '.day-jump-pill, .jumper-menu-item');
      $(document).on('click.v3pkgJump', '.day-jump-pill, .jumper-menu-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const dayNum = $(this).data('day') || $(this).attr('data-day');
        if (dayNum) {
          window.jumpToDay(dayNum, this);
        }
      });

      // 4. Accordion Toggle on Day Card Header or Button
      $(document).off('click.v3pkgAccordion', '.v3-day-card-header, .v3-day-accordion-toggle, .v3-day-badge');
      $(document).on('click.v3pkgAccordion', '.v3-day-card-header', function (e) {
        if ($(e.target).closest('.v3-day-title-input, .btn-day-delete, .v3-day-input-label').length) {
          return;
        }
        e.preventDefault();
        window.toggleDayCard(this);
      });

      // 5. Expand All / Collapse All Days
      $(document).off('click.v3pkgExpandAll', '#btnExpandAllDays');
      $(document).on('click.v3pkgExpandAll', '#btnExpandAllDays', function (e) {
        e.preventDefault();
        window.toggleAllDayCards(this);
      });

      // 6. Add Day CTAs (Top and Bottom)
      $(document).off('click.v3pkgAddDay', '#btnAddDayTop, #btnAddDayBottom');
      $(document).on('click.v3pkgAddDay', '#btnAddDayTop, #btnAddDayBottom', function (e) {
        e.preventDefault();
        window.addNewDay();
      });

      // 7. Remove / Delete Day Button
      $(document).off('click.v3pkgDeleteDay', '.btn-day-delete');
      $(document).on('click.v3pkgDeleteDay', '.btn-day-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const dayNum = $(this).data('day') || $(this).attr('data-day');
        if (dayNum) {
          window.removeDay(dayNum);
        }
      });

      // 8. Update Day Title Live in Jumper Menu
      $(document).off('input.v3pkgTitleSync', '.v3-day-title-input');
      $(document).on('input.v3pkgTitleSync', '.v3-day-title-input', function () {
        const $card = $(this).closest('.v3-day-card');
        const dayNum = $card.data('day');
        const title = $(this).val();
        $(`.jumper-menu-item[data-day="${dayNum}"] .jumper-item-title`).text(title || `Day ${dayNum}`);
      });

      // 9. Stay City Select Change Handler
      $(document).off('change.v3pkgStayCity', '.select2-stay-city');
      $(document).on('change.v3pkgStayCity', '.select2-stay-city', function () {
        const dayNum = $(this).data('day');
        const selectedCity = ($(this).val() || '').trim();
        $(`#dayStayInput_${dayNum}`).val(selectedCity);
      });

      // 10. Dedicated Save Day Details CTA (Updates ONLY the particular day)
      $(document).off('click.v3pkgSaveDay', '.btn-save-day-cta');
      $(document).on('click.v3pkgSaveDay', '.btn-save-day-cta', function (e) {
        e.preventDefault();
        const dayNum = $(this).data('day');
        const $btn = $(this);
        const pkgId = $('#hiddenPackageId').val() || (window.V3PackageEdit && window.V3PackageEdit.packageId);

        if (!pkgId) {
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'Invalid package ID.', 'error');
          }
          return;
        }

        const $card = $(`#dayCard_${dayNum}`);
        const dayEl = document.querySelector(`#dayEditor_${dayNum} .ql-editor`);
        const dayOverview = dayEl ? dayEl.innerHTML : ($(`#editor-${dayNum}`).val() || '');
        const dayTitle = $card.find('.v3-day-title-input').val() || `Day ${dayNum}`;
        const dayStay = ($(`#dayCitySelect_${dayNum}`).val() || $(`#dayStayInput_${dayNum}`).val() || '').trim();
        $(`#dayStayInput_${dayNum}`).val(dayStay);

        const originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html(`<i class="fi fi-rr-spinner" style="animation: spin 1s linear infinite;"></i> Saving Day ${dayNum}...`);

        const dayData = new FormData();
        dayData.append('packageId', pkgId);
        dayData.append('dayNumber', dayNum);
        dayData.append('dayTitle', dayTitle);
        dayData.append('dayContent', dayOverview);
        dayData.append('dayStay', dayStay);

        $.ajax({
          url: '/api/v3/packages/update-day/',
          type: 'POST',
          data: dayData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function (res) {
            $btn.prop('disabled', false).html(originalBtnHtml);
            if (res && res.status === 'success') {
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', res.message || `Day ${dayNum} details updated successfully!`, 'success');
              }
            } else {
              const errMsg = (res && res.message) ? res.message : `Failed to update Day ${dayNum}.`;
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', errMsg, 'error');
              }
            }
          },
          error: function (xhr) {
            $btn.prop('disabled', false).html(originalBtnHtml);
            let errMsg = `Failed to update Day ${dayNum}.`;
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errMsg, 'error');
            }
          }
        });
      });

      // 11. Dedicated CTA for Tab 1 General & Logistics (Updates current tab data without switching tabs)
      $(document).off('click.v3pkgGeneral', '#btnUpdateGeneral');
      $(document).on('click.v3pkgGeneral', '#btnUpdateGeneral', function (e) {
        e.preventDefault();
        const pkgId = $('#hiddenPackageId').val() || (window.V3PackageEdit && window.V3PackageEdit.packageId);

        if (!pkgId) {
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'Invalid package ID.', 'error');
          }
          return;
        }

        // Sync overview editor
        const overviewEl = document.querySelector('#overviewEditor .ql-editor');
        if (overviewEl) {
          $('#editor-0').val(overviewEl.innerHTML);
        }

        const packageName = ($('#name').val() || '').trim();
        if (!packageName) {
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'Please enter a tour package title.', 'warning');
          }
          $('#name').focus();
          return;
        }

        const $btn = $('#btnUpdateGeneral');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner" style="animation: spin 1s linear infinite;"></i> Saving Itinerary...');

        const formData = new FormData();
        formData.append('packageId', pkgId);
        formData.append('packageName', packageName);
        formData.append('travelStyle', $('#travelStyle').val() || 'standard');
        formData.append('tourPackageTemplate', $('#tourPackageTemplate').val() || 'mit');
        formData.append('hotel', $('#hotel').val() || '');
        formData.append('outstationVehicle', $('#outstationVehicle').val() || '');
        formData.append('localSighseeignVehicle', $('#localSighseeignVehicle').val() || '');
        formData.append('overview', $('#editor-0').val() || '');
        formData.append('packageType', $('#packageType').val() || 'domestic');
        formData.append('publishToWebsite', $('#chkPublishToWebsite').is(':checked') ? 1 : 0);

        $.ajax({
          url: '/api/v3/packages/update-general/',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function (res) {
            $btn.prop('disabled', false).html(originalHtml);
            if (res && res.status === 'success') {
              if (res.packageName) {
                $('#pkgHeaderTitle').text(res.packageName);
              }
              if (typeof res.is_published !== 'undefined') {
                if (res.is_published && res.link_url) {
                  $('#btnViewLivePackage').attr('href', res.link_url).show();
                } else if (!res.is_published) {
                  $('#btnViewLivePackage').hide();
                }
              }
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', res.message || 'Itinerary updated successfully.', 'success');
              }
            } else {
              const errMsg = (res && res.message) ? res.message : 'Failed to update itinerary.';
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', errMsg, 'error');
              }
            }
          },
          error: function (xhr) {
            $btn.prop('disabled', false).html(originalHtml);
            let errMsg = 'Failed to update itinerary.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errMsg, 'error');
            }
          }
        });
      });

      // 12. Global Keyboard Shortcut (Ctrl+S / Cmd+S to update current active tab)
      $(document).off('keydown.v3pkgSave').on('keydown.v3pkgSave', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
          if ($('#packageEditApp').length > 0) {
            e.preventDefault();
            if ($('#tabGeneral').is(':visible')) {
              $('#btnUpdateGeneral').click();
            } else if ($('#tabPolicies').is(':visible')) {
              $('#btnUpdatePolicies').click();
            }
          }
        }
      });

      // 13. Complete Box Click-to-Focus for Rich Text Editors
      $(document).off('click.v3pkgEditorFocus', '.v3-editor-container .ql-container, .v3-editor-container');
      $(document).on('click.v3pkgEditorFocus', '.v3-editor-container .ql-container, .v3-editor-container', function (e) {
        if ($(e.target).closest('.ql-toolbar').length) return;
        const $editor = $(this).find('.ql-editor');
        if ($editor.length && !$(e.target).is('.ql-editor') && !$(e.target).closest('.ql-editor').length) {
          $editor.focus();
          try {
            const el = $editor[0];
            const range = document.createRange();
            const sel = window.getSelection();
            range.selectNodeContents(el);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
          } catch (err) {}
        }
      });

      // 13. Add Line Button for Inclusions / Exclusions
      $(document).off('click.v3pkgAddLine', '.btn-v3-add-line');
      $(document).on('click.v3pkgAddLine', '.btn-v3-add-line', function (e) {
        e.preventDefault();
        const editorId = $(this).data('editor') || $(this).attr('data-editor');
        const quill = (window.V3PackageEdit && window.V3PackageEdit.editors && window.V3PackageEdit.editors[editorId]) || (Quill && Quill.find && Quill.find(document.getElementById(editorId)));
        
        if (quill) {
          const length = quill.getLength();
          quill.insertText(length - 1, '\n', Quill.sources.USER);
          quill.setSelection(length, 0, Quill.sources.USER);
          quill.focus();
        } else {
          const $qlEditor = $(`#${editorId}`).find('.ql-editor');
          if ($qlEditor.length) {
            $qlEditor.append('<p><br></p>');
            $qlEditor.focus();
          }
        }
      });

      // 14. Dedicated CTA for Inclusions & Exclusions
      $(document).off('click.v3pkgPolicies', '#btnUpdatePolicies');
      $(document).on('click.v3pkgPolicies', '#btnUpdatePolicies', function (e) {
        e.preventDefault();
        const pkgId = $('#hiddenPackageId').val() || (window.V3PackageEdit && window.V3PackageEdit.packageId);

        if (!pkgId) {
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'Invalid package ID.', 'error');
          }
          return;
        }

        const incEl = document.querySelector('#inclusionsEditor .ql-editor');
        const excEl = document.querySelector('#exclusionsEditor .ql-editor');
        const inclusionHtml = incEl ? incEl.innerHTML : ($('#editor-inclusion').val() || '');
        const exclusionHtml = excEl ? excEl.innerHTML : ($('#editor-exclusion').val() || '');

        const $btn = $('#btnUpdatePolicies');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner" style="animation: spin 1s linear infinite;"></i> Saving Inclusions & Exclusions...');

        const formData = new FormData();
        formData.append('packageId', pkgId);
        formData.append('inclusion', inclusionHtml);
        formData.append('exclusion', exclusionHtml);

        $.ajax({
          url: '/api/v3/packages/update-policies/',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function (res) {
            $btn.prop('disabled', false).html(originalHtml);
            if (res && res.status === 'success') {
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', res.message || 'Inclusions & Exclusions updated successfully!', 'success');
              }
            } else {
              const errMsg = (res && res.message) ? res.message : 'Failed to update Inclusions & Exclusions.';
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', errMsg, 'error');
              }
            }
          },
          error: function (xhr) {
            $btn.prop('disabled', false).html(originalHtml);
            let errMsg = 'Failed to update Inclusions & Exclusions.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errMsg, 'error');
            }
          }
        });
      });

      // 13. Template Selection & Preview Handlers
      $(document).off('click.v3pkgTempCard', '.v3-template-card');
      $(document).on('click.v3pkgTempCard', '.v3-template-card', function (e) {
        if ($(e.target).closest('.btn-template-preview').length) {
          return;
        }
        const templateId = $(this).attr('data-template-id') || $(this).data('template-id') || 'mit';
        self.selectTemplate(templateId);
      });

      $(document).off('click.v3pkgTempSelect', '.btn-template-select');
      $(document).on('click.v3pkgTempSelect', '.btn-template-select', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const templateId = $(this).attr('data-template-id') || $(this).data('template-id') || $(this).closest('.v3-template-card').attr('data-template-id') || 'mit';
        self.selectTemplate(templateId);
      });

      $(document).off('click.v3pkgTempPrev', '.btn-template-preview');
      $(document).on('click.v3pkgTempPrev', '.btn-template-preview', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const templateId = $(this).attr('data-template-id') || $(this).data('template-id') || $(this).closest('.v3-template-card').attr('data-template-id') || 'mit';
        self.openTemplatePreviewModal(templateId);
      });

      // Next / Prev Template Navigation (In Preview Modal)
      $(document).off('click.v3pkgNavPrevTemp', '#btnPrevPreviewTemplate, #btnFloatPrevTemplate');
      $(document).on('click.v3pkgNavPrevTemp', '#btnPrevPreviewTemplate, #btnFloatPrevTemplate', function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.navigateTemplatePreview(-1);
      });

      $(document).off('click.v3pkgNavNextTemp', '#btnNextPreviewTemplate, #btnFloatNextTemplate');
      $(document).on('click.v3pkgNavNextTemp', '#btnNextPreviewTemplate, #btnFloatNextTemplate', function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.navigateTemplatePreview(1);
      });

      // Keyboard Left/Right arrow navigation when modal is active
      $(document).off('keydown.v3pkgModalNav');
      $(document).on('keydown.v3pkgModalNav', function (e) {
        if ($('#modalPackageTemplatePreview').hasClass('is-active')) {
          if (e.key === 'ArrowLeft' || e.keyCode === 37) {
            e.preventDefault();
            self.navigateTemplatePreview(-1);
          } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
            e.preventDefault();
            self.navigateTemplatePreview(1);
          }
        }
      });

      $(document).off('click.v3pkgClosePrev', '#btnCloseTemplatePreviewModal');
      $(document).on('click.v3pkgClosePrev', '#btnCloseTemplatePreviewModal', function (e) {
        e.preventDefault();
        $('#modalPackageTemplatePreview').removeClass('is-active');
      });

      // Close modal on clicking outside dialog
      $(document).off('click.v3pkgModalBackdrop', '#modalPackageTemplatePreview');
      $(document).on('click.v3pkgModalBackdrop', '#modalPackageTemplatePreview', function (e) {
        if (e.target === this) {
          $(this).removeClass('is-active');
        }
      });

      // Close on Escape key
      $(document).off('keydown.v3pkgModalEsc');
      $(document).on('keydown.v3pkgModalEsc', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          $('#modalPackageTemplatePreview').removeClass('is-active');
        }
      });

      $(document).off('click.v3pkgApplyPrev', '#btnApplyPreviewedTemplate');
      $(document).on('click.v3pkgApplyPrev', '#btnApplyPreviewedTemplate', function (e) {
        e.preventDefault();
        const templateId = $(this).data('template-id') || 'mit';
        self.selectTemplate(templateId);
        $('#modalPackageTemplatePreview').removeClass('is-active');
        if (typeof popUpMessage === 'function') {
          popUpMessage('center', `Selected ${templateId.toUpperCase()} template layout.`, 'success');
        }
      });

      // Download PDF from Preview Modal
      $(document).off('click.v3pkgDownloadPrev', '#btnDownloadPreviewPDF');
      $(document).on('click.v3pkgDownloadPrev', '#btnDownloadPreviewPDF', async function (e) {
        e.preventDefault();
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner animate-spin"></i> Generating PDF...');

        try {
          if (!window.html2canvas) {
            await self.loadScriptAsync('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
          }
          if (!window.jspdf) {
            await self.loadScriptAsync('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
          }

          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF('p', 'pt', 'a4');
          const pageWidth = pdf.internal.pageSize.getWidth();
          const pageHeight = pdf.internal.pageSize.getHeight();
          const pages = document.querySelectorAll('#templatePreviewCanvas .page');

          if (!pages || pages.length === 0) {
            const canvasEl = document.querySelector('#templatePreviewCanvas');
            const canvas = await html2canvas(canvasEl, { scale: 2, useCORS: true, allowTaint: true, backgroundColor: '#ffffff' });
            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            const imgProps = pdf.getImageProperties(imgData);
            const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;
            pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pdfImgHeight);
          } else {
            for (let i = 0; i < pages.length; i++) {
              const canvas = await html2canvas(pages[i], {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
              });
              const imgData = canvas.toDataURL('image/jpeg', 0.95);
              const imgProps = pdf.getImageProperties(imgData);
              const pdfImgHeight = (imgProps.height * pageWidth) / imgProps.width;
              pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, pdfImgHeight);
              if (i < pages.length - 1) {
                pdf.addPage();
              }
            }
          }

          const pkgTitle = ($('#name').val() || 'Tour_Itinerary').replace(/[^A-Za-z0-9_-]/g, '_');
          pdf.save(`${pkgTitle}_Proposal.pdf`);
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'PDF downloaded successfully!', 'success');
          }
        } catch (err) {
          console.error('PDF Generation Error:', err);
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'PDF generation error. You can print to Save as PDF.', 'danger');
          }
        } finally {
          $btn.prop('disabled', false).html(originalHtml);
        }
      });

      // Travel Style / Theme dropdown sync
      $(document).off('change.v3pkgTheme', '#travelStyle');
      $(document).on('change.v3pkgTheme', '#travelStyle', function () {
        const val = ($(this).val() || 'standard').trim();
        const themeClean = val.toLowerCase().replace(/[^a-z0-9]/g, '');
        $('#pkgMetaTheme').attr('class', 'pkg-theme-pill pkg-theme-' + themeClean).text(val ? (val.charAt(0).toUpperCase() + val.slice(1)) : 'Standard');
      });
    },

    loadScriptAsync: function (src) {
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    },

    templateOrder: ['mit', 'standard', 'modern', 'aleya'],
    templateLabels: {
      'mit': 'MIT Multi-Page Proposal (A4 PDF / Premium)',
      'standard': 'Standard Clean Layout',
      'modern': 'Modern Card Layout',
      'aleya': 'Aleya Luxury Proposal (A4 PDF / Gold)'
    },
    activePreviewTemplate: 'mit',

    navigateTemplatePreview: function (direction) {
      const templates = this.templateOrder || ['mit', 'standard', 'modern', 'aleya'];
      const current = this.activePreviewTemplate || $('#tourPackageTemplate').val() || 'mit';
      let index = templates.indexOf(current);
      if (index === -1) index = 0;
      const nextIndex = (index + direction + templates.length) % templates.length;
      const nextTemplateId = templates[nextIndex];
      this.openTemplatePreviewModal(nextTemplateId);
    },

    selectTemplate: function (templateId) {
      templateId = templateId || 'mit';
      $('#tourPackageTemplate').val(templateId);
      $('.v3-template-card').removeClass('active');
      const $card = $(`.v3-template-card[data-template-id="${templateId}"]`);
      if ($card.length) {
        $card.addClass('active');
      }
    },

    openTemplatePreviewModal: function (templateId) {
      templateId = templateId || $('#tourPackageTemplate').val() || 'mit';
      this.activePreviewTemplate = templateId;
      
      const templates = this.templateOrder || ['mit', 'standard', 'modern', 'aleya'];
      let idx = templates.indexOf(templateId);
      if (idx === -1) idx = 0;
      $('#previewTemplateCounter').text(`${idx + 1} / ${templates.length}`);

      const pkg = (this.packageData && this.packageData.package) ? this.packageData.package : {};
      const title = ($('#name').val() || pkg.title || 'Tour Package').trim();
      const theme = ($('#travelStyle').val() || pkg.theme || 'standard').trim();
      const hotel = ($('#hotel').val() || pkg.hotel || '').trim();
      const city = ($('#destinationCity').val() || pkg.city || 'Destination').trim();
      const outstationVehicle = ($('#outstationVehicle').val() || pkg.outstation_vehicle || '').trim();
      const localSightseeingVehicle = ($('#localSighseeignVehicle').val() || pkg.localsightseen_vehicle || '').trim();
      const coverImage = (pkg.image || pkg.featured_image || pkg.gallery_image || '');
      
      const overviewEl = document.querySelector('#overviewEditor .ql-editor');
      const overview = overviewEl ? overviewEl.innerHTML : ($('#editor-0').val() || pkg.overview || '');
      
      const incEl = document.querySelector('#inclusionsEditor .ql-editor');
      const inclusions = incEl ? incEl.innerHTML : ($('#editor-inclusion').val() || pkg.inclusions || '');
      
      const excEl = document.querySelector('#exclusionsEditor .ql-editor');
      const exclusions = excEl ? excEl.innerHTML : ($('#editor-exclusion').val() || pkg.exclusions || '');

      const days = [];
      const itineraryList = (pkg && pkg.itinerary && Array.isArray(pkg.itinerary)) ? pkg.itinerary : [];

      $('.v3-day-card').each(function (i) {
        const dayNum = $(this).data('day') || (i + 1);
        const dayTitle = $(this).find('.v3-day-title-input').val() || `Day ${dayNum}`;
        const dayEl = document.querySelector(`#dayEditor_${dayNum} .ql-editor`);
        const dayContent = dayEl ? dayEl.innerHTML : ($(`#editor-${dayNum}`).val() || '');
        const dayStay = $(this).find('.v3-day-stay-field select, .select2-stay-city').val() || $(`#dayStayInput_${dayNum}`).val() || '';
        const dayImg = (itineraryList[i] && (itineraryList[i].feature_image || itineraryList[i].image)) ? (itineraryList[i].feature_image || itineraryList[i].image) : '';
        days.push({
          day: dayTitle,
          overview: dayContent,
          stay: dayStay,
          image: dayImg
        });
      });

      if (days.length === 0 && itineraryList.length > 0) {
        itineraryList.forEach(function (d, idx) {
          days.push({
            day: d.day || `Day ${idx + 1}`,
            overview: d.overview || '',
            stay: d.stay || '',
            image: d.feature_image || d.image || ''
          });
        });
      }

      $('#btnApplyPreviewedTemplate').data('template-id', templateId);
      const titles = this.templateLabels || {
        'mit': 'MIT Multi-Page Proposal',
        'standard': 'Standard Timeline Proposal',
        'modern': 'Modern Card Proposal',
        'aleya': 'Aleya Luxury Proposal'
      };
      $('#previewModalTitle').text(titles[templateId] || 'Template Preview');

      let renderHtml = '';
      if (templateId === 'mit') {
        renderHtml = this.buildMitTemplatePreview({ title, theme, hotel, city, outstationVehicle, localSightseeingVehicle, coverImage, overview, inclusions, exclusions, days });
      } else if (templateId === 'modern') {
        renderHtml = this.buildModernTemplatePreview({ title, theme, hotel, city, outstationVehicle, localSightseeingVehicle, coverImage, overview, inclusions, exclusions, days });
      } else if (templateId === 'aleya') {
        renderHtml = this.buildAleyaTemplatePreview({ title, theme, hotel, city, outstationVehicle, localSightseeingVehicle, coverImage, overview, inclusions, exclusions, days });
      } else {
        renderHtml = this.buildStandardTemplatePreview({ title, theme, hotel, city, outstationVehicle, localSightseeingVehicle, coverImage, overview, inclusions, exclusions, days });
      }

      $('#templatePreviewCanvas').html(renderHtml);
      $('#modalPackageTemplatePreview').addClass('is-active');
      $('#modalPackageTemplatePreview .v3-preview-modal-body').scrollTop(0);
    },

    buildMitTemplatePreview: function (data) {
      function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      const siteName = window.SITE_NAME || window.siteName || 'Travel Partner';
      const totalDays = data.days.length || 1;
      const totalNights = Math.max(1, totalDays - 1);
      const themeGrad = 'linear-gradient(135deg, #ea580c 0%, #f97316 100%)';

      let html = `
        <div style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
          <div style="position: relative; width: 100%; min-height: 240px; background: ${themeGrad}; display: flex; align-items: flex-end; padding: 28px 34px; color: #ffffff; overflow: hidden;">
            ${data.coverImage ? `<img src="${esc(data.coverImage)}" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;" />` : ''}
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.75) 100%);"></div>
            <div style="position: relative; z-index: 2; width: 100%;">
              <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(6px); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase;">
                ${totalNights}N / ${totalDays}D &bull; ${esc(data.city || 'Destination')}
              </span>
              <h1 style="font-size: 26px; font-weight: 800; margin-top: 6px; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">${esc(data.title)}</h1>
            </div>
          </div>

          <div style="padding: 24px 34px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; text-align: center;">
                <span style="display:block; font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700;">Duration</span>
                <strong style="font-size: 14px; color: #0f172a;">${totalNights}N / ${totalDays}D</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; text-align: center;">
                <span style="display:block; font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700;">Travel Style</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.theme)}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; text-align: center;">
                <span style="display:block; font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700;">Hotel Tier</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.hotel || 'Deluxe 3★')}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; text-align: center;">
                <span style="display:block; font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700;">Vehicle</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.outstationVehicle || 'AC Dedicated')}</strong>
              </div>
            </div>

            ${data.overview ? `
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #ea580c; margin-bottom: 8px;">Tour Overview</h3>
                <div style="font-size: 13.5px; color: #334155; line-height: 1.75;">${data.overview}</div>
              </div>
            ` : ''}

            <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Day-by-Day Itinerary</h3>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
              ${data.days.map((d, idx) => {
                const isLast = (idx === data.days.length - 1);
                return `
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                      <span style="background: #ea580c; color: #ffffff; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 6px;">Day ${idx + 1}</span>
                      <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; flex: 1; margin-left: 12px;">${esc(d.day || `Day ${idx + 1}`)}</h4>
                    </div>
                    <div style="font-size: 13.5px; color: #334155; line-height: 1.7;">${d.overview || '<p style="color:#94a3b8; font-style:italic;">Sightseeing and transfers.</p>'}</div>
                    ${d.image ? `
                      <div style="margin-top: 12px; height: 160px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="${esc(d.image)}" style="width: 100%; height: 100%; object-fit: cover;" />
                      </div>
                    ` : ''}
                    ${d.stay ? `
                      <div style="margin-top: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #166534; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fi fi-rr-bed"></i> Night Stay: ${esc(d.stay)}
                      </div>
                    ` : (isLast ? `
                      <div style="margin-top: 12px; background: #fdf4ff; border: 1px solid #f5d0fe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #86198f; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fi fi-rr-plane"></i> Tour Concludes: Farewell & transfers! ✨
                      </div>
                    ` : '')}
                  </div>
                `;
              }).join('')}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px;">
              <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 18px;">
                <h4 style="color: #16a34a; font-size: 14px; font-weight: 800; margin-bottom: 8px;"><i class="fi fi-rr-check-circle"></i> Inclusions</h4>
                <div style="font-size: 13px; color: #166534; line-height: 1.6;">${data.inclusions || 'Standard package inclusions.'}</div>
              </div>
              <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 18px;">
                <h4 style="color: #dc2626; font-size: 14px; font-weight: 800; margin-bottom: 8px;"><i class="fi fi-rr-cross-circle"></i> Exclusions</h4>
                <div style="font-size: 13px; color: #991b1b; line-height: 1.6;">${data.exclusions || 'Personal expenses not included.'}</div>
              </div>
            </div>

            <div style="background: linear-gradient(135deg, #431407 0%, #7c2d12 100%); border-radius: 12px; padding: 22px; color: #ffffff; margin-bottom: 18px;">
              <h4 style="font-size: 16px; font-weight: 800;">${esc(siteName)}</h4>
              <p style="font-size: 12.5px; color: #fed7aa; margin-top: 2px;">Your dedicated travel planning specialist</p>
            </div>

            <div style="text-align: center; font-size: 11.5px; color: #64748b; padding-top: 8px; border-top: 1px solid #f1f5f9;">
              Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color: #ea580c; text-decoration: none; font-weight: 700;">Aleya</a> — Travel Agency Management Software
            </div>
          </div>
        </div>
      `;

      return html;
    },

    buildStandardTemplatePreview: function (data) {
      function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      const siteName = window.SITE_NAME || window.siteName || 'Travel Partner';
      const totalDays = data.days.length || 1;
      const totalNights = Math.max(1, totalDays - 1);

      let html = `
        <div style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
          <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 28px 34px; border-bottom: 3px solid #0284c7;">
            <div style="display: flex; gap: 8px; margin-bottom: 6px;">
              <span style="background: #0284c7; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                ${totalNights}N / ${totalDays}D
              </span>
              <span style="background: rgba(255,255,255,0.15); color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                ${esc(data.city || 'Destination')}
              </span>
            </div>
            <h1 style="font-size: 24px; font-weight: 800;">${esc(data.title)}</h1>
          </div>

          <div style="padding: 24px 34px;">
            ${data.coverImage ? `
              <div style="width: 100%; height: 200px; border-radius: 10px; overflow: hidden; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                <img src="${esc(data.coverImage)}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            ` : ''}

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Duration</span>
                <strong style="font-size: 14px; color: #0f172a;">${totalNights}N / ${totalDays}D</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Travel Style</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.theme)}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Hotel</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.hotel || 'Deluxe')}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Vehicle</span>
                <strong style="font-size: 14px; color: #0f172a; text-transform: capitalize;">${esc(data.outstationVehicle || 'AC Private')}</strong>
              </div>
            </div>

            ${data.overview ? `
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #0284c7; margin-bottom: 8px;">Itinerary Overview</h3>
                <div style="font-size: 13.5px; color: #334155; line-height: 1.75;">${data.overview}</div>
              </div>
            ` : ''}

            <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Day-Wise Itinerary</h3>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
              ${data.days.map((d, idx) => {
                const isLast = (idx === data.days.length - 1);
                return `
                  <div style="border-left: 3px solid #0284c7; background: #ffffff; border: 1px solid #e2e8f0; border-left: 3px solid #0284c7; border-radius: 8px; padding: 18px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                      <h4 style="font-size: 15px; font-weight: 800; color: #0f172a;">Day ${idx + 1}: ${esc(d.day || `Day ${idx + 1}`)}</h4>
                    </div>
                    <div style="font-size: 13.5px; color: #334155; line-height: 1.7;">${d.overview || '<p style="color:#94a3b8; font-style:italic;">Sightseeing and transfers.</p>'}</div>
                    ${d.image ? `
                      <div style="margin-top: 12px; height: 160px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="${esc(d.image)}" style="width: 100%; height: 100%; object-fit: cover;" />
                      </div>
                    ` : ''}
                    ${d.stay ? `
                      <div style="margin-top: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 8px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-bed"></i> Overnight Stay: ${esc(d.stay)}
                      </div>
                    ` : (isLast ? `
                      <div style="margin-top: 12px; background: #fdf4ff; border: 1px solid #f5d0fe; color: #86198f; padding: 8px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-plane"></i> Tour Conclusion: Safe Travels!
                      </div>
                    ` : '')}
                  </div>
                `;
              }).join('')}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px;">
              <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 18px;">
                <h4 style="color: #16a34a; font-size: 14px; font-weight: 800; margin-bottom: 8px;"><i class="fi fi-rr-check-circle"></i> Inclusions</h4>
                <div style="font-size: 13px; color: #334155; line-height: 1.6;">${data.inclusions || 'Standard inclusions apply.'}</div>
              </div>
              <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 18px;">
                <h4 style="color: #dc2626; font-size: 14px; font-weight: 800; margin-bottom: 8px;"><i class="fi fi-rr-cross-circle"></i> Exclusions</h4>
                <div style="font-size: 13px; color: #334155; line-height: 1.6;">${data.exclusions || 'Personal expenses not included.'}</div>
              </div>
            </div>

            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 10px; padding: 20px; color: #ffffff; margin-bottom: 16px;">
              <h4 style="font-size: 16px; font-weight: 800;">${esc(siteName)}</h4>
              <p style="font-size: 12.5px; color: #94a3b8; margin-top: 2px;">Your dedicated travel planning specialist</p>
            </div>

            <div style="text-align: center; font-size: 11.5px; color: #64748b; padding-top: 8px; border-top: 1px solid #f1f5f9;">
              Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color: #0284c7; text-decoration: none; font-weight: 700;">Aleya</a> — Travel Agency Management Software
            </div>
          </div>
        </div>
      `;

      return html;
    },

    buildModernTemplatePreview: function (data) {
      function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      const siteName = window.SITE_NAME || window.siteName || 'Travel Partner';
      const totalDays = data.days.length || 1;
      const totalNights = Math.max(1, totalDays - 1);

      let html = `
        <div style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
          <div style="position: relative; width: 100%; min-height: 240px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); display: flex; align-items: flex-end; padding: 28px 34px; color: #ffffff; overflow: hidden;">
            ${data.coverImage ? `<img src="${esc(data.coverImage)}" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;" />` : ''}
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15,23,42,0.2) 0%, rgba(15,23,42,0.85) 100%);"></div>
            <div style="position: relative; z-index: 2; width: 100%;">
              <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(6px); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase;">
                ${totalNights}N / ${totalDays}D &bull; ${esc(data.city || 'Destination')}
              </span>
              <h1 style="font-size: 26px; font-weight: 800; line-height: 1.25; margin-top: 6px;">${esc(data.title)}</h1>
            </div>
          </div>

          <div style="padding: 24px 34px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px;">
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center;">
                <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Theme</span>
                <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px;">${esc(data.theme)}</div>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center;">
                <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Hotel Style</span>
                <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px;">${esc(data.hotel || 'Deluxe 3★')}</div>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center;">
                <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Transport</span>
                <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px;">${esc(data.outstationVehicle || 'AC Dedicated')}</div>
              </div>
            </div>

            ${data.overview ? `
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px;">
                <div style="font-size: 14px; font-weight: 800; color: #4f46e5; margin-bottom: 8px;"><i class="fi fi-rr-sparkles"></i> Tour Overview</div>
                <div style="font-size: 13.5px; color: #334155; line-height: 1.75;">${data.overview}</div>
              </div>
            ` : ''}

            <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Day-by-Day Route & Activities</h3>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
              ${data.days.map((d, idx) => {
                const isLast = (idx === data.days.length - 1);
                return `
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    <div style="padding: 14px 18px; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                      <span style="background: #4f46e5; color: #ffffff; font-size: 11px; font-weight: 800; padding: 3px 9px; border-radius: 6px;">Day ${idx + 1}</span>
                      <h4 style="font-size: 15px; font-weight: 800; color: #1e1b4b; flex: 1; margin-left: 12px;">${esc(d.day || `Day ${idx + 1}`)}</h4>
                    </div>
                    <div style="padding: 18px;">
                      <div style="font-size: 13.5px; color: #334155; line-height: 1.7;">${d.overview || '<p style="color:#94a3b8; font-style:italic;">Planned sightseeing.</p>'}</div>
                      ${d.image ? `
                        <div style="margin-top: 12px; height: 160px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                          <img src="${esc(d.image)}" style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>
                      ` : ''}
                      ${d.stay ? `
                        <div style="margin-top: 12px; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #4338ca; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                          <i class="fi fi-rr-bed"></i> Overnight Stay: ${esc(d.stay)}
                        </div>
                      ` : (isLast ? `
                        <div style="margin-top: 12px; background: #fdf4ff; border: 1px solid #f5d0fe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #86198f; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                          <i class="fi fi-rr-plane"></i> End of a Wonderful Journey! ✨
                        </div>
                      ` : '')}
                    </div>
                  </div>
                `;
              }).join('')}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px;">
              <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 18px;">
                <h4 style="font-size: 14.5px; font-weight: 700; color: #15803d; margin-bottom: 8px;"><i class="fi fi-rr-check"></i> Inclusions</h4>
                <div style="font-size: 13px; color: #166534; line-height: 1.55;">${data.inclusions || 'All standard inclusions.'}</div>
              </div>
              <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 18px;">
                <h4 style="font-size: 14.5px; font-weight: 700; color: #b91c1c; margin-bottom: 8px;"><i class="fi fi-rr-cross"></i> Exclusions</h4>
                <div style="font-size: 13px; color: #991b1b; line-height: 1.55;">${data.exclusions || 'Personal expenses.'}</div>
              </div>
            </div>

            <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 12px; padding: 22px; color: #ffffff; margin-bottom: 18px;">
              <h4 style="font-size: 16px; font-weight: 800;">${esc(siteName)}</h4>
              <p style="font-size: 12.5px; color: #c7d2fe; margin-top: 2px;">Your dedicated travel planning specialist</p>
            </div>

            <div style="text-align: center; font-size: 11.5px; color: #64748b; padding-top: 8px; border-top: 1px solid #f1f5f9;">
              Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color: #4f46e5; text-decoration: none; font-weight: 700;">Aleya</a> — Travel Agency Management Software
            </div>
          </div>
        </div>
      `;

      return html;
    },

    buildAleyaTemplatePreview: function (data) {
      function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      const siteName = window.SITE_NAME || window.siteName || 'Travel Partner';
      const totalDays = data.days.length || 1;
      const totalNights = Math.max(1, totalDays - 1);

      let html = `
        <div style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px; font-family: 'Plus Jakarta Sans', sans-serif;">
          <div style="position: relative; width: 100%; min-height: 280px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); display: flex; align-items: flex-end; padding: 32px 34px; color: #ffffff; overflow: hidden;">
            ${data.coverImage ? `<img src="${esc(data.coverImage)}" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;" />` : ''}
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15,23,42,0.2) 0%, rgba(15,23,42,0.92) 100%);"></div>
            <div style="position: relative; z-index: 2; width: 100%;">
              <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <span style="background: rgba(197, 155, 39, 0.4); backdrop-filter: blur(6px); border: 1px solid #c59b27; color: #fef8ec; font-size: 11.5px; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
                  <i class="fi fi-rr-marker"></i> ${esc(data.city || 'Destination')}
                </span>
                <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(6px); font-size: 11.5px; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
                  ${totalNights}N / ${totalDays}D
                </span>
              </div>
              <h1 style="font-size: 28px; font-weight: 800; font-family: 'Playfair Display', serif; line-height: 1.25;">${esc(data.title)}</h1>
            </div>
          </div>

          <div style="padding: 28px 34px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Duration</span>
                <strong style="font-size: 14.5px; color: #0f172a;">${totalNights}N / ${totalDays}D</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Style</span>
                <strong style="font-size: 14.5px; color: #0f172a; text-transform: capitalize;">${esc(data.theme)}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Hotel</span>
                <strong style="font-size: 14.5px; color: #0f172a; text-transform: capitalize;">${esc(data.hotel || 'Deluxe 3★')}</strong>
              </div>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; text-align: center;">
                <span style="font-size: 10.5px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Vehicle</span>
                <strong style="font-size: 14.5px; color: #0f172a; text-transform: capitalize;">${esc(data.outstationVehicle || 'Private AC')}</strong>
              </div>
            </div>

            ${data.overview ? `
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); margin-bottom: 24px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 8px; border-bottom: 2px solid #fef8ec; padding-bottom: 6px;">
                  <span style="color: #c59b27;">&bull;</span> Tour Overview
                </h3>
                <div style="font-size: 13.5px; color: #334155; line-height: 1.8;">${data.overview}</div>
              </div>
            ` : ''}

            <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">
              Detailed Day-Wise Itinerary
            </h3>
            <div style="display: flex; flex-direction: column; gap: 18px; margin-bottom: 24px;">
              ${data.days.map((d, idx) => {
                const isLast = (idx === data.days.length - 1);
                return `
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                      <span style="background: #c59b27; color: #ffffff; font-size: 11.5px; font-weight: 800; padding: 4px 10px; border-radius: 6px;">Day ${idx + 1}</span>
                      <h4 style="font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; flex: 1; margin-left: 14px;">${esc(d.day || `Day ${idx + 1}`)}</h4>
                    </div>
                    <div style="padding: 20px;">
                      <div style="font-size: 13.5px; color: #334155; line-height: 1.75;">${d.overview || '<p style="color:#94a3b8; font-style:italic;">Sightseeing and transfers as scheduled.</p>'}</div>
                      ${d.image ? `
                        <div style="margin-top: 14px; height: 180px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                          <img src="${esc(d.image)}" style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>
                      ` : ''}
                      ${d.stay ? `
                        <div style="margin-top: 14px; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                          <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                              <i class="fi fi-rr-bed"></i>
                            </div>
                            <div>
                              <div style="font-size: 14px; font-weight: 800; color: #065f46;">${esc(d.stay)}</div>
                              <div style="font-size: 12px; color: #047857;">Overnight Stay in ${esc(data.city || 'Destination')} &bull; Confirmed Property</div>
                            </div>
                          </div>
                          <span style="font-size: 11px; font-weight: 700; background: #16a34a; color: #ffffff; padding: 4px 10px; border-radius: 6px;">Stay Included</span>
                        </div>
                      ` : (isLast ? `
                        <div style="margin-top: 14px; background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); border: 1px solid #f5d0fe; border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                          <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 8px; background: #fae8ff; color: #a21caf; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                              <i class="fi fi-rr-plane"></i>
                            </div>
                            <div>
                              <div style="font-size: 14px; font-weight: 800; color: #86198f;">End of a Magical Journey! ✨</div>
                              <div style="font-size: 12px; color: #a21caf;">Transfer to station/airport with lifelong memories.</div>
                            </div>
                          </div>
                          <span style="font-size: 11px; font-weight: 700; background: #a21caf; color: #ffffff; padding: 4px 10px; border-radius: 6px;">Farewell</span>
                        </div>
                      ` : '')}
                    </div>
                  </div>
                `;
              }).join('')}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px;">
              <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px;">
                <h4 style="font-size: 14.5px; font-weight: 700; color: #15803d; margin-bottom: 10px;"><i class="fi fi-rr-check"></i> Package Inclusions</h4>
                <div style="font-size: 13px; color: #166534; line-height: 1.6;">${data.inclusions || 'All standard package inclusions.'}</div>
              </div>
              <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px;">
                <h4 style="font-size: 14.5px; font-weight: 700; color: #b91c1c; margin-bottom: 10px;"><i class="fi fi-rr-cross"></i> Package Exclusions</h4>
                <div style="font-size: 13px; color: #991b1b; line-height: 1.6;">${data.exclusions || 'Personal expenses & entry fees.'}</div>
              </div>
            </div>

            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 24px 28px; color: #ffffff; margin-bottom: 18px;">
              <h4 style="font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700;">${esc(siteName)}</h4>
              <p style="font-size: 12.5px; color: #94a3b8; margin-top: 2px;">Your dedicated luxury travel planning partner</p>
            </div>

            <div style="text-align: center; font-size: 11.5px; color: #64748b; padding-top: 8px; border-top: 1px solid #f1f5f9;">
              Powered by <a href="https://aleya.in" target="_blank" rel="noopener" style="color: #c59b27; text-decoration: none; font-weight: 700;">Aleya</a> — Travel Agency Management Software
            </div>
          </div>
        </div>
      `;

      return html;
    }
  };

  $(function () {
    if (window.V3PackageEdit && typeof window.V3PackageEdit.autoInit === 'function') {
      window.V3PackageEdit.autoInit();
    }
  });

})(jQuery);
