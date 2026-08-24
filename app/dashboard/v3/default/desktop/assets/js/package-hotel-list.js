/**
 * V3 Package Hotel List Module
 * Handles loading, night assignments, filtering, and lightbox previews via /api/v3/packages/hotels/
 */
(function ($) {
  'use strict';

  window.V3PackageHotelList = {
    packageId: null,
    encodedId: null,
    packageData: null,
    metricsData: null,
    hotelsData: [],
    activeStatusFilter: 'all',
    activeCityFilter: 'all',
    isLoading: false,
    isLoaded: false,

    init: function () {
      const $app = $('#packageHotelListApp');
      if (!$app.length) {
        this.isLoaded = false;
        this.isLoading = false;
        return;
      }

      let rawId = $app.attr('data-package-id') || $app.data('package-id');
      let encodedId = $app.attr('data-encoded-id') || $app.data('encoded-id');

      if (!rawId || rawId === '0' || rawId === 0) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlId = urlParams.get('id');
        if (urlId) {
          try {
            const decoded = atob(urlId);
            if (decoded && !isNaN(decoded)) {
              rawId = decoded;
              encodedId = urlId;
            } else if (!isNaN(urlId)) {
              rawId = urlId;
              encodedId = btoa(urlId);
            }
          } catch (e) {
            if (!isNaN(urlId)) {
              rawId = urlId;
              encodedId = btoa(urlId);
            }
          }
        }
      }

      this.packageId = rawId;
      this.encodedId = encodedId;
      this.activeStatusFilter = 'all';
      this.activeCityFilter = 'all';
      this.isLoading = false;
      this.isLoaded = false;
      this.bindEvents();
      this.loadHotels(true);
    },

    bindEvents: function () {
      const self = this;
      const $app = $('#packageHotelListApp');

      $app.off('click', '.theme-filter-btn').on('click', '.theme-filter-btn', function (e) {
        e.preventDefault();
        const $btn = $(this).closest('.theme-filter-btn');
        $('.theme-filter-btn').removeClass('active');
        $btn.addClass('active');
        self.activeStatusFilter = $btn.data('filter') || 'all';
        self.applyFilters();
      });

      $app.off('click', '.city-chip-btn').on('click', '.city-chip-btn', function (e) {
        e.preventDefault();
        const $chip = $(this).closest('.city-chip-btn');
        $('.city-chip-btn').removeClass('active');
        $chip.addClass('active');
        self.activeCityFilter = $chip.data('city') || 'all';
        self.applyFilters();
      });

      $app.off('click', '.assignHotelBtn').on('click', '.assignHotelBtn', function (e) {
        e.preventDefault();
        self.handleAssignHotel($(this));
      });

      $app.off('click', '.unassignHotelBtn').on('click', '.unassignHotelBtn', function (e) {
        e.preventDefault();
        self.handleUnassignHotel($(this));
      });

      $app.off('click', '.btn-preview-img').on('click', '.btn-preview-img', function (e) {
        e.preventDefault();
        const src = $(this).data('image');
        const name = $(this).data('name');
        $('#lightboxImg').attr('src', src);
        $('#lightboxTitle').text(name);
        $('#lightboxModal').addClass('active');
      });

      $('#lightboxClose').off('click').on('click', function () {
        $('#lightboxModal').removeClass('active');
      });

      $(document).off('keydown.hotelLightbox').on('keydown.hotelLightbox', function (e) {
        if ($('#lightboxModal').hasClass('active') && e.key === 'Escape') {
          $('#lightboxModal').removeClass('active');
        }
      });
    },

    loadHotels: function (force) {
      if (this.isLoading) return;
      if (!this.packageId && !this.encodedId) return;

      const self = this;
      this.isLoading = true;

      const $grid = $('#hotelGrid');
      $grid.html(`
        <div class="hotel-loading-state" style="grid-column: 1 / -1; padding: 48px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0;">
          <div class="v3-spinner" style="width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 14px;"></div>
          <p style="color: #64748b; font-size: 14px; font-weight: 500; margin: 0;">Loading package hotels & inventory...</p>
        </div>
      `);

      const queryParam = this.packageId ? `package_id=${encodeURIComponent(this.packageId)}` : `id=${encodeURIComponent(this.encodedId)}`;

      $.ajax({
        url: `/api/v3/packages/hotels/?${queryParam}`,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function (res) {
          self.isLoading = false;
          if (res && res.status === 'success') {
            self.packageData = res.package || {};
            self.metricsData = res.metrics || {};
            self.hotelsData = res.hotels || [];
            self.isLoaded = true;

            self.renderHeaderAndKPIs();
            self.renderCityChips();
            self.renderHotelCards();
          } else {
            self.renderErrorState(res.msg || 'Unable to load hotels data.');
          }
        },
        error: function (xhr) {
          self.isLoading = false;
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Server error loading hotels.';
          self.renderErrorState(msg);
        }
      });
    },

    renderHeaderAndKPIs: function () {
      const pkg = this.packageData || {};
      const m = this.metricsData || {};

      if (pkg.title) {
        $('#pkgHeaderTitle').text(pkg.title);
      }
      if (pkg.theme) {
        const themeSlug = (pkg.theme || 'standard').toLowerCase().replace(/[^a-z0-9]+/g, '-');
        $('#pkgMetaTheme').attr('class', `pkg-theme-pill pkg-theme-${themeSlug}`).text(pkg.theme);
      }
      if (pkg.duration) {
        $('#pkgMetaDuration').html(`<i class="fi fi-rr-moon pkg-duration-moon"></i> ${pkg.duration}N / <i class="fi fi-rr-sun pkg-duration-sun"></i> ${pkg.days || pkg.duration + 1}D`);
      }
      if (pkg.location_label) {
        $('#pkgMetaLocation').html(`<i class="fi fi-rr-marker" style="color: #0284c7;"></i> ${pkg.location_label}`);
      }

      // Live Package View CTA
      const liveUrl = pkg.link_url || pkg.linkUrl;
      if (liveUrl) {
        $('#btnViewLivePackage').attr('href', liveUrl).show();
      } else {
        $('#btnViewLivePackage').hide();
      }

      // Update KPIs
      $('#statTotalHotels').text(m.total_hotels || this.hotelsData.length);
      
      if (m.mentioned_cities && m.mentioned_cities.length > 0) {
        $('#statMentionedCitiesWrap').show();
        $('#statMentionedCitiesCount').text(m.mentioned_cities.length);
      } else {
        $('#statMentionedCitiesWrap').hide();
      }

      $('#statNightsCovered').text(`${m.nights_covered || 0} / ${pkg.duration || 1}`);

      const missing = m.nights_missing !== undefined ? m.nights_missing : Math.max(0, (pkg.duration || 1) - (m.nights_covered || 0));
      if (missing > 0) {
        $('#statNightsMissingPill')
          .removeClass('is-green').addClass('is-red')
          .html(`<i class="fi fi-rr-exclamation"></i> <b>${missing}</b> ${missing === 1 ? 'Night' : 'Nights'} Missing Hotel`);
      } else {
        $('#statNightsMissingPill')
          .removeClass('is-red').addClass('is-green')
          .html('<i class="fi fi-rr-check-circle"></i> All Nights Assigned!');
      }

      // Update Filter Tabs Counts
      $('#filterCountAll').text(this.hotelsData.length);
      $('#filterCountAssigned').text(m.assigned_hotels_count || 0);
      $('#filterCountUnassigned').text(m.unassigned_hotels_count || 0);
    },

    renderCityChips: function () {
      const m = this.metricsData || {};
      const counts = m.city_counts || {};
      const cityKeys = Object.keys(counts);

      const $bar = $('#cityFilterBar');
      if (!cityKeys.length) {
        $bar.hide();
        return;
      }

      const totalHotels = this.hotelsData.length;
      let html = `
        <span class="city-filter-header"><i class="fi fi-rr-marker"></i> Filter City:</span>
        <div class="city-chips-group">
          <button type="button" class="city-chip-btn ${this.activeCityFilter === 'all' ? 'active' : ''}" data-city="all">
            All Cities <span class="chip-count">${totalHotels}</span>
          </button>
      `;

      cityKeys.forEach((cityName) => {
        const count = counts[cityName];
        const isActive = this.activeCityFilter.toLowerCase() === cityName.toLowerCase() ? 'active' : '';
        html += `
          <button type="button" class="city-chip-btn ${isActive}" data-city="${cityName}">
            ${cityName} <span class="chip-count">${count}</span>
          </button>
        `;
      });

      html += '</div>';
      $bar.html(html).show();
    },

    renderHotelCards: function () {
      const $grid = $('#hotelGrid');
      const pkg = this.packageData || {};
      const duration = parseInt(pkg.duration) || 1;
      const occupiedMap = (this.metricsData && this.metricsData.occupied_map) ? this.metricsData.occupied_map : {};

      if (!this.hotelsData.length) {
        $grid.html(`
          <div style="grid-column: 1 / -1; padding: 48px 24px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
            <i class="fi fi-rr-building" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
            <h4 style="margin: 0 0 6px 0; color: #0f172a; font-size: 16px;">No hotels found for this package</h4>
            <p style="margin: 0; font-size: 13px;">Click the <b>+</b> button below to add a new hotel vendor property to your inventory.</p>
          </div>
        `);
        return;
      }

      let html = '';
      this.hotelsData.forEach((hotel) => {
        const assignedNight = parseInt(hotel.assigned_night) || 0;
        const isAssigned = assignedNight > 0;
        const cardClasses = `dash-gallery-card dash-hotel-card ${isAssigned ? 'is-assigned' : ''}`;
        const locationStr = [hotel.city, hotel.state].filter(Boolean).join(', ');

        // Night Options
        let nightOptions = `<option value="0" ${assignedNight === 0 ? 'selected' : ''}>-- Unassigned --</option>`;
        for (let n = 1; n <= duration; n++) {
          const isSelected = (assignedNight === n);
          const occupiedBy = occupiedMap[n] || '';
          const isOccupiedByOther = (occupiedBy && occupiedBy !== hotel.title);
          const labelSuffix = isOccupiedByOther ? ` (Occupied by ${occupiedBy})` : '';
          nightOptions += `<option value="${n}" ${isSelected ? 'selected' : ''}>Night ${n}${labelSuffix}</option>`;
        }

        // Room Options
        let roomOptions = '<option value="#">-- Select Room Type --</option>';
        if (hotel.rooms && hotel.rooms.length > 0) {
          hotel.rooms.forEach((r) => {
            const isSelected = (r.id === hotel.assigned_room_id || r.name === hotel.assigned_room_type) ? 'selected' : '';
            roomOptions += `<option value="${r.val}" ${isSelected}>${r.name}</option>`;
          });
        } else {
          roomOptions += '<option value="0:Standard Room" selected>Standard Room</option>';
        }

        html += `
          <div class="${cardClasses}"
               data-hotel-id="${hotel.id}"
               data-night="${assignedNight}"
               data-city="${hotel.city || ''}"
               data-title="${hotel.title || ''}"
               data-image="${hotel.thumbnail || ''}">

            <div class="dash-thumb-container">
              <img src="${hotel.thumbnail}" alt="${hotel.title}" loading="lazy">

              <div class="dash-card-badges">
                <div class="badge-night-wrap">
                  ${isAssigned ? `<span class="dash-badge-chip badge-day"><i class="fi fi-rr-moon"></i> Night ${assignedNight}</span>` : '<span class="dash-badge-chip badge-none">Unassigned</span>'}
                </div>
                <div>
                  <span class="dash-badge-chip badge-type"><i class="fi fi-rr-building"></i> ${hotel.type_label || 'Hotel'}</span>
                </div>
              </div>

              <div class="dash-thumb-overlay">
                <button type="button" class="dash-preview-btn btn-preview-img"
                        data-image="${hotel.thumbnail}"
                        data-name="${hotel.title}"
                        title="View Preview">
                  <i class="fi fi-rr-search"></i>
                </button>
              </div>
            </div>

            <div class="dash-card-body">
              <div class="hotel-card-header">
                <h3>${hotel.title}</h3>
                ${locationStr ? `<span class="hotel-location-tag"><i class="fi fi-rr-marker"></i> ${locationStr}</span>` : ''}
              </div>

              <!-- Assign Night Dropdown -->
              <div class="dash-day-select-wrap">
                <label><i class="fi fi-rr-moon"></i> Assign Night:</label>
                <select class="selectJS form-control assign-night-select">
                  ${nightOptions}
                </select>
              </div>

              <!-- Assign Room Type Dropdown -->
              <div class="dash-day-select-wrap">
                <label><i class="fi fi-rr-bed"></i> Room Type:</label>
                <select class="selectJS form-control assign-room-select">
                  ${roomOptions}
                </select>
              </div>

              <div class="dash-card-actions-row">
                <button type="button" class="dash-btn-featured assignHotelBtn ${isAssigned ? 'is-assigned-btn' : ''}" style="flex: 1;">
                  <i class="fi fi-rr-check"></i> ${isAssigned ? 'Update' : 'Assign to Package'}
                </button>
                ${isAssigned ? `
                  <button type="button" class="dash-btn-unassign unassignHotelBtn" title="Unassign this hotel from package">
                    <i class="fi fi-rr-trash"></i> Unassign
                  </button>
                ` : ''}
              </div>
            </div>
          </div>
        `;
      });

      $grid.html(html);
      this.initSelect2();
      this.applyFilters();
    },

    initSelect2: function () {
      if (typeof $.fn.select2 === 'function') {
        $('.assign-night-select, .assign-room-select').select2({
          minimumResultsForSearch: Infinity,
          width: '100%'
        });
      }
    },

    applyFilters: function () {
      const statusFilter = (this.activeStatusFilter || 'all').toLowerCase().trim();
      const cityFilter = (this.activeCityFilter || 'all').toLowerCase().trim();

      let visibleCount = 0;

      $('.dash-hotel-card').each(function () {
        const $card = $(this);
        const night = parseInt($card.data('night')) || 0;
        const cardCity = ($card.data('city') || '').toString().toLowerCase().trim();

        let statusPass = true;
        if (statusFilter === 'assigned' && night === 0) statusPass = false;
        if (statusFilter === 'unassigned' && night > 0) statusPass = false;

        let cityPass = true;
        if (cityFilter !== 'all' && cardCity !== cityFilter) {
          cityPass = false;
        }

        if (statusPass && cityPass) {
          $card.removeClass('is-hidden').show();
          visibleCount++;
        } else {
          $card.addClass('is-hidden').hide();
        }
      });

      const $noResults = $('#hotelGridEmptyFilter');
      if (visibleCount === 0 && this.hotelsData.length > 0) {
        if (!$noResults.length) {
          $('#hotelGrid').append(`
            <div id="hotelGridEmptyFilter" style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
              <i class="fi fi-rr-search" style="font-size: 28px; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
              <p style="margin: 0; font-size: 14px; font-weight: 500;">No hotels match the selected filter criteria.</p>
            </div>
          `);
        } else {
          $noResults.show();
        }
      } else {
        $noResults.remove();
      }
    },

    handleAssignHotel: function ($btn) {
      const self = this;
      const $card = $btn.closest('.dash-hotel-card');
      const hotelId = $card.data('hotel-id');
      const hotelTitle = $card.data('title');
      const chosenNight = parseInt($card.find('.assign-night-select').val()) || 0;
      const chosenRoom = $card.find('.assign-room-select').val();

      if (chosenNight > 0 && (!chosenRoom || chosenRoom === '#')) {
        if (typeof popUpMessage === 'function') {
          popUpMessage('center', 'Please select a valid Room Type before assigning.', 'warning');
        } else {
          alert('Please select a valid Room Type before assigning.');
        }
        return;
      }

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');

      $.ajax({
        url: '/api/v3/packages/hotels/',
        type: 'POST',
        data: JSON.stringify({
          property_id: hotelId,
          package_id: self.packageId,
          night: chosenNight,
          roomType: chosenRoom
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false);
          if (res && res.status === 'success') {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg, 'success');
            }

            // If chosenNight > 0, unassign other cards assigned to this night
            if (chosenNight > 0) {
              $('.dash-hotel-card').each(function () {
                const $otherCard = $(this);
                if (parseInt($otherCard.data('hotel-id')) !== parseInt(hotelId)) {
                  const $otherSelect = $otherCard.find('.assign-night-select');
                  if (parseInt($otherSelect.val()) === chosenNight) {
                    $otherSelect.val('0').trigger('change.select2');
                    $otherCard.data('night', 0).removeClass('is-assigned');
                    $otherCard.find('.badge-night-wrap').html('<span class="dash-badge-chip badge-none">Unassigned</span>');
                    $otherCard.find('.dash-card-actions-row').html(`
                      <button type="button" class="dash-btn-featured assignHotelBtn" style="flex: 1;">
                        <i class="fi fi-rr-check"></i> Assign to Package
                      </button>
                    `);
                  }
                }
              });
            }

            // Update current card UI
            $card.data('night', chosenNight);
            if (chosenNight > 0) {
              $card.addClass('is-assigned');
              $card.find('.badge-night-wrap').html(`<span class="dash-badge-chip badge-day"><i class="fi fi-rr-moon"></i> Night ${chosenNight}</span>`);
              $card.find('.dash-card-actions-row').html(`
                <button type="button" class="dash-btn-featured assignHotelBtn is-assigned-btn" style="flex: 1;">
                  <i class="fi fi-rr-check"></i> Update
                </button>
                <button type="button" class="dash-btn-unassign unassignHotelBtn" title="Unassign this hotel from package">
                  <i class="fi fi-rr-trash"></i> Unassign
                </button>
              `);
            } else {
              $card.removeClass('is-assigned');
              $card.find('.badge-night-wrap').html('<span class="dash-badge-chip badge-none">Unassigned</span>');
              $card.find('.dash-card-actions-row').html(`
                <button type="button" class="dash-btn-featured assignHotelBtn" style="flex: 1;">
                  <i class="fi fi-rr-check"></i> Assign to Package
                </button>
              `);
            }

            self.updateAllNightDropdownOccupiedLabels();
            self.sortCardsInDOM();
            self.updateMetricsPills();
            self.applyFilters();
          } else {
            $btn.html('<i class="fi fi-rr-check"></i> ' + (parseInt($card.data('night')) > 0 ? 'Update' : 'Assign to Package'));
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to assign hotel';
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errorMsg, 'error');
            } else {
              alert(errorMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-check"></i> ' + (parseInt($card.data('night')) > 0 ? 'Update' : 'Assign to Package'));
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'An error occurred';
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', msg, 'error');
          } else {
            alert(msg);
          }
        }
      });
    },

    handleUnassignHotel: function ($btn) {
      const self = this;
      const $card = $btn.closest('.dash-hotel-card');
      const hotelId = $card.data('hotel-id');

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i>');

      $.ajax({
        url: '/api/v3/packages/hotels/',
        type: 'POST',
        data: JSON.stringify({
          property_id: hotelId,
          package_id: self.packageId,
          night: 0,
          roomType: ''
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false);
          if (res && res.status === 'success') {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg || 'Hotel unassigned from package.', 'success');
            }

            // Reset night selector
            const $nightSelect = $card.find('.assign-night-select');
            $nightSelect.val('0');
            if (typeof $.fn.select2 === 'function') {
              $nightSelect.trigger('change.select2');
            }

            // Update card UI
            $card.data('night', 0).removeClass('is-assigned');
            $card.find('.badge-night-wrap').html('<span class="dash-badge-chip badge-none">Unassigned</span>');
            $card.find('.dash-card-actions-row').html(`
              <button type="button" class="dash-btn-featured assignHotelBtn" style="flex: 1;">
                <i class="fi fi-rr-check"></i> Assign to Package
              </button>
            `);

            self.updateAllNightDropdownOccupiedLabels();
            self.sortCardsInDOM();
            self.updateMetricsPills();
            self.applyFilters();
          } else {
            $btn.html('<i class="fi fi-rr-trash"></i> Unassign');
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to unassign hotel.';
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errorMsg, 'error');
            } else {
              alert(errorMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-trash"></i> Unassign');
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error unassigning hotel.';
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', msg, 'error');
          } else {
            alert(msg);
          }
        }
      });
    },

    updateAllNightDropdownOccupiedLabels: function () {
      const occupiedMap = {};
      $('.dash-hotel-card').each(function () {
        const title = $(this).data('title');
        const night = parseInt($(this).data('night')) || 0;
        if (night > 0) {
          occupiedMap[night] = title;
        }
      });

      $('.dash-hotel-card').each(function () {
        const $card = $(this);
        const currentTitle = $card.data('title');
        const $select = $card.find('.assign-night-select');

        $select.find('option').each(function () {
          const n = parseInt($(this).val());
          if (n > 0) {
            const occupiedBy = occupiedMap[n];
            const isOccupiedByOther = (occupiedBy && occupiedBy !== currentTitle);
            $(this).text(`Night ${n}${isOccupiedByOther ? ` (Occupied by ${occupiedBy})` : ''}`);
          }
        });

        if (typeof $.fn.select2 === 'function') {
          $select.trigger('change.select2');
        }
      });
    },

    sortCardsInDOM: function () {
      const $grid = $('#hotelGrid');
      const $cards = $grid.find('.dash-hotel-card').get();

      $cards.sort(function (a, b) {
        const nightA = parseInt($(a).data('night')) || 999999;
        const nightB = parseInt($(b).data('night')) || 999999;
        return nightA < nightB ? -1 : 1;
      });

      $.each($cards, function (idx, elem) {
        $grid.append(elem);
      });
    },

    updateMetricsPills: function () {
      const assignedNightsSet = new Set();
      let totalHotels = 0;
      let assignedCount = 0;
      let unassignedCount = 0;

      $('.dash-hotel-card').each(function () {
        totalHotels++;
        const night = parseInt($(this).data('night')) || 0;
        if (night > 0) {
          assignedNightsSet.add(night);
          assignedCount++;
        } else {
          unassignedCount++;
        }
      });

      const pkg = this.packageData || {};
      const duration = parseInt(pkg.duration) || 1;
      const nightsCovered = assignedNightsSet.size;
      const nightsMissing = Math.max(0, duration - nightsCovered);

      $('#statTotalHotels').text(totalHotels);
      $('#statNightsCovered').text(`${nightsCovered} / ${duration}`);

      if (nightsMissing > 0) {
        $('#statNightsMissingPill')
          .removeClass('is-green').addClass('is-red')
          .html(`<i class="fi fi-rr-exclamation"></i> <b>${nightsMissing}</b> ${nightsMissing === 1 ? 'Night' : 'Nights'} Missing Hotel`);
      } else {
        $('#statNightsMissingPill')
          .removeClass('is-red').addClass('is-green')
          .html('<i class="fi fi-rr-check-circle"></i> All Nights Assigned!');
      }

      $('#filterCountAll').text(totalHotels);
      $('#filterCountAssigned').text(assignedCount);
      $('#filterCountUnassigned').text(unassignedCount);
    },

    renderErrorState: function (msg) {
      $('#hotelGrid').html(`
        <div style="grid-column: 1 / -1; padding: 48px 24px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px solid #fee2e2; color: #dc2626;">
          <i class="fi fi-rr-cross-circle" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>
          <h4 style="margin: 0 0 6px 0; color: #991b1b; font-size: 16px;">Failed to Load Hotels</h4>
          <p style="margin: 0 0 16px 0; font-size: 13px; color: #b91c1c;">${msg}</p>
          <button type="button" class="btn-v3-primary" id="btnRetryLoadHotels" style="padding: 6px 16px; font-size: 13px;">Retry</button>
        </div>
      `);

      const self = this;
      $('#btnRetryLoadHotels').on('click', function () {
        self.loadHotels(true);
      });
    }
  };

  $(document).ready(function () {
    window.V3PackageHotelList.init();
  });

  $(document).on('spa:loaded', function () {
    window.V3PackageHotelList.init();
  });

})(jQuery);
