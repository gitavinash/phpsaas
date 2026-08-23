/**
 * V3 Package Edit Module
 * Fully API-driven (Zero Inline SQL), SPA-Compatible
 * Supports Tabbed Workflow, Sticky Jump Bar, Day Accordions, and Rich Text Editors
 */
(function ($) {
  'use strict';

  // Global Helpers for direct or delegated execution
  window.jumpToDay = function (dayNum, btn) {
    // 1. Activate tab 2 (Itinerary) if not active
    const $tabBtn = $('.edit-tab-btn[data-target="#tabItinerary"]');
    if (!$('#tabItinerary').hasClass('active')) {
      $tabBtn.trigger('click');
    }

    // 2. Locate target card
    const card = document.getElementById('dayCard_' + dayNum);
    if (!card) return;

    const $card = $(card);

    // 3. Expand if collapsed
    if ($card.hasClass('is-collapsed')) {
      $card.removeClass('is-collapsed');
      $card.find('.v3-day-card-body').slideDown(150);
      $card.find('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
    }

    // 4. Update pill active state
    $('.day-jump-pill').removeClass('active');
    if (btn) {
      $(btn).addClass('active');
    } else {
      $(`.day-jump-pill[data-day="${dayNum}"]`).addClass('active');
    }

    // 5. Accurate smooth scroll calculation
    const cardRect = card.getBoundingClientRect();
    const absoluteTop = (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0) + cardRect.top - 125;

    window.scrollTo({
      top: Math.max(0, absoluteTop),
      behavior: 'smooth'
    });

    // 6. Highlight effect
    $card.addClass('day-card-highlight');
    setTimeout(function () {
      $card.removeClass('day-card-highlight');
    }, 1400);
  };

  window.toggleDayCard = function (btn) {
    const $card = $(btn).closest('.v3-day-card');
    const $body = $card.find('.v3-day-card-body');

    if ($card.hasClass('is-collapsed')) {
      $card.removeClass('is-collapsed');
      $body.slideDown(200);
      $(btn).html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
    } else {
      $card.addClass('is-collapsed');
      $body.slideUp(200);
      $(btn).html('<i class="fi fi-rr-angle-small-down"></i> Expand');
    }
  };

  window.toggleAllDayCards = function (btn) {
    const $cards = $('.v3-day-card');
    const hasCollapsed = $cards.hasClass('is-collapsed');

    if (hasCollapsed) {
      $cards.removeClass('is-collapsed');
      $cards.find('.v3-day-card-body').slideDown(200);
      $('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-up"></i> Collapse');
      $(btn).html('<i class="fi fi-rr-compress-alt"></i> Collapse All Days');
    } else {
      $cards.addClass('is-collapsed');
      $cards.find('.v3-day-card-body').slideUp(200);
      $('.v3-day-accordion-toggle').html('<i class="fi fi-rr-angle-small-down"></i> Expand');
      $(btn).html('<i class="fi fi-rr-expand-arrows-alt"></i> Expand All Days');
    }
  };

  window.V3PackageEdit = {
    packageId: null,
    encodedId: null,
    packageType: 'domestic',
    data: null,
    isLoading: false,
    activeFetchId: null,
    eventsBound: false,

    init: function () {
      const $app = $('#packageEditApp');
      if (!$app.length) return;

      const pkgId = $app.data('package-id') || $app.attr('data-package-id');
      const encodedId = $app.data('encoded-id') || $app.attr('data-encoded-id');
      const pkgType = $app.data('package-type') || $app.attr('data-package-type') || 'domestic';

      if (!pkgId || parseInt(pkgId) <= 0) {
        this.renderError('Invalid package ID specified.');
        return;
      }

      // Guard: If already loading or already loaded for this exact package ID, do not hit API again
      if (this.activeFetchId === String(pkgId) && (this.isLoading || this.data)) {
        return;
      }

      this.packageId = pkgId;
      this.encodedId = encodedId;
      this.packageType = pkgType;
      this.activeFetchId = String(pkgId);
      this.isLoading = true;

      this.loadPackageData();
      if (!this.eventsBound) {
        this.bindEvents();
        this.eventsBound = true;
      }
    },

    loadPackageData: function () {
      const self = this;
      $('#packageEditLoading').show();
      $('#packageEditContent').hide();
      $('#packageEditError').hide();

      $.ajax({
        url: '/api/v3/packages/get/?id=' + encodeURIComponent(self.packageId),
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          self.isLoading = false;
          if (res && res.status === 'success' && res.package) {
            self.data = res;
            self.renderPackage(res);
          } else {
            self.renderError(res.message || 'Unable to retrieve package details.');
          }
        },
        error: function (xhr) {
          self.isLoading = false;
          let errorMsg = 'Package not found or access denied.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
          }
          self.renderError(errorMsg);
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
      const pkg = res.package;
      const vehicles = res.vehicles || [];
      const travelStyles = res.travel_styles || {};
      const hotelTypes = res.hotel_types || {};

      // 1. Header Meta & Actions
      $('#pkgHeaderTitle').text(pkg.title || 'Tour Package');
      
      const themeClean = (pkg.theme || 'Standard').toLowerCase().replace(/[^a-z0-9]/g, '');
      $('#pkgMetaTheme').attr('class', 'pkg-theme-pill pkg-theme-' + themeClean).text(pkg.theme ? (pkg.theme.charAt(0).toUpperCase() + pkg.theme.slice(1)) : 'Standard');
      
      $('#pkgMetaDuration').html(`<i class="fi-rr-moon" style="color: #6366f1;"></i> ${pkg.nights}N / <i class="fi-rr-sun" style="color: #f59e0b;"></i> ${pkg.days}D`);
      
      const locationText = pkg.type === 'international' ? (pkg.country || pkg.city || 'International') : (pkg.city || 'Domestic');
      $('#pkgMetaLocation').html(`<i class="fi-rr-marker" style="color: #0284c7;"></i> ${locationText}`);

      if (pkg.link_url) {
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
      $('#durationDisplay').text(`${pkg.nights} Nights / ${pkg.days} Days`);

      // Travel Styles Dropdown
      const $travelStyle = $('#travelStyle');
      $travelStyle.empty().append('<option value="select">Select travel style</option>');
      $.each(travelStyles, function (k, val) {
        const isSel = (pkg.theme && pkg.theme.toLowerCase() === k.toLowerCase()) ? 'selected' : '';
        $travelStyle.append(`<option value="${k}" ${isSel}>${val}</option>`);
      });

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
      const $daysContainer = $('#daysContainer');
      $pillList.empty();
      $daysContainer.empty();

      const itineraryList = pkg.itinerary || [];
      let editorIndex = 1;

      itineraryList.forEach(function (dayItem, idx) {
        const dayNum = idx + 1;
        const dayTitle = dayItem.day || `Day ${dayNum}`;
        const dayOverview = dayItem.overview || '';

        // Sticky Pill
        $pillList.append(`
          <a href="#dayCard_${dayNum}" data-day="${dayNum}" onclick="if(window.jumpToDay){window.jumpToDay(${dayNum}, this); return false;}" class="day-jump-pill ${dayNum === 1 ? 'active' : ''}">Day ${dayNum}</a>
        `);

        // Day Card HTML
        $daysContainer.append(`
          <div class="v3-day-card" id="dayCard_${dayNum}">
              <div class="v3-day-card-header">
                  <div class="v3-day-badge">
                      <i class="fi fi-rr-sun"></i> DAY ${dayNum}
                  </div>
                  <div class="v3-day-title-input-wrap">
                      <label class="v3-day-input-label">Day ${dayNum} Heading / Landmark</label>
                      <input name="dayTitle[]" value="${$('<div>').text(dayTitle).html()}" type="text" class="v3-input v3-day-title-input" placeholder="e.g. Day ${dayNum}: Arrival & Sightseeing" />
                  </div>
                  <div class="v3-day-header-actions">
                      <button type="button" class="v3-day-accordion-toggle" onclick="if(window.toggleDayCard){window.toggleDayCard(this); return false;}" title="Toggle Day Details">
                          <i class="fi fi-rr-angle-small-up"></i> Collapse
                      </button>
                  </div>
              </div>
              <div class="v3-day-card-body">
                  <label class="v3-form-label-sm"><i class="fi fi-rr-edit-alt"></i> Day ${dayNum} Detailed Schedule & Activities</label>
                  <div class="v3-editor-container">
                      <div class="editor" id="dayEditor_${dayNum}" style="min-height: 140px"></div>
                      <textarea name="dayContent[]" id="editor-${dayNum}" style="display:none">${$('<div>').text(dayOverview).html()}</textarea>
                  </div>
              </div>
          </div>
        `);
        editorIndex++;
      });

      // Inclusions & Exclusions Editor indices
      const inclusionIdx = editorIndex;
      const exclusionIdx = editorIndex + 1;

      $('#editor-inclusion').attr('id', `editor-${inclusionIdx}`).val(pkg.inclusions || '');
      $('#editor-exclusion').attr('id', `editor-${exclusionIdx}`).val(pkg.exclusions || '');
      $('#editor-0').val(pkg.overview || '');

      // 5. Initialize Quill Rich Text Editors
      this.initEditors(pkg, itineraryList, inclusionIdx, exclusionIdx);

      // 6. Initialize Select2
      this.initSelects();

      // 7. Show Form Content
      $('#packageEditLoading').hide();
      $('#packageEditContent').fadeIn(150);
    },

    initEditors: function (pkg, itineraryList, inclusionIdx, exclusionIdx) {
      if (typeof Quill === 'undefined') return;

      // 1. Overview Editor
      const overviewContainer = document.getElementById('overviewEditor');
      if (overviewContainer && !$(overviewContainer).hasClass('ql-container')) {
        const qOverview = new Quill(overviewContainer, { theme: 'snow' });
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
            if (dayItem.overview) {
              qDay.root.innerHTML = dayItem.overview;
            }
            qDay.on('text-change', function () {
              $(`#editor-${dayNum}`).val(qDay.root.innerHTML);
            });
          }
        });
      }

      // 3. Inclusions Editor
      const incEl = document.getElementById('inclusionsEditor');
      if (incEl && !$(incEl).hasClass('ql-container')) {
        const qInc = new Quill(incEl, { theme: 'snow' });
        if (pkg && pkg.inclusions) {
          qInc.root.innerHTML = pkg.inclusions;
        }
        qInc.on('text-change', function () {
          $(`#editor-${inclusionIdx}`).val(qInc.root.innerHTML);
        });
      }

      // 4. Exclusions Editor
      const excEl = document.getElementById('exclusionsEditor');
      if (excEl && !$(excEl).hasClass('ql-container')) {
        const qExc = new Quill(excEl, { theme: 'snow' });
        if (pkg && pkg.exclusions) {
          qExc.root.innerHTML = pkg.exclusions;
        }
        qExc.on('text-change', function () {
          $(`#editor-${exclusionIdx}`).val(qExc.root.innerHTML);
        });
      }
    },

    initSelects: function () {
      if (typeof $.fn.select2 !== 'undefined' && $('#packageEditApp .selectJS').length) {
        $('#packageEditApp .selectJS').select2({
          placeholder: 'Select option',
          width: '100%'
        });
      }
    },

    bindEvents: function () {
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

      // 2. Day Jump Pills
      $(document).off('click.v3pkgJump', '.day-jump-pill');
      $(document).on('click.v3pkgJump', '.day-jump-pill', function (e) {
        e.preventDefault();
        const dayNum = $(this).data('day');
        if (dayNum) {
          window.jumpToDay(dayNum, this);
        }
      });

      // 3. Accordion Toggle for Individual Day Cards
      $(document).off('click.v3pkgAccordion', '.v3-day-accordion-toggle');
      $(document).on('click.v3pkgAccordion', '.v3-day-accordion-toggle', function (e) {
        e.preventDefault();
        window.toggleDayCard(this);
      });

      // 4. Expand All / Collapse All Days
      $(document).off('click.v3pkgExpandAll', '#btnExpandAllDays');
      $(document).on('click.v3pkgExpandAll', '#btnExpandAllDays', function (e) {
        e.preventDefault();
        window.toggleAllDayCards(this);
      });

      // 5. Global Keyboard Shortcut (Ctrl+S / Cmd+S to update)
      $(document).off('keydown.v3pkgSave').on('keydown.v3pkgSave', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
          if ($('#packageEditApp').length > 0) {
            e.preventDefault();
            $('#updateTourPackageBtn').click();
          }
        }
      });
    }
  };

  // Auto-init on page load / script load
  $(document).ready(function () {
    if ($('#packageEditApp').length > 0) {
      window.V3PackageEdit.init();
    }
  });

  $(document).on('spa:loaded', function () {
    if ($('#packageEditApp').length > 0) {
      window.V3PackageEdit.init();
    }
  });

})(jQuery);
