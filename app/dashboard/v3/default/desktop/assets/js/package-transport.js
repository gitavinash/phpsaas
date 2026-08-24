/**
 * V3 Package Transportation & Transporter Assignment Module
 * Path: app/dashboard/v3/default/desktop/assets/js/package-transport.js
 * Handles vehicle configuration, transporter directory listing, filtering, search, and dynamic assignment via /api/v3/packages/transport/
 */
(function (factory) {
  if (typeof window.jQuery !== 'undefined') {
    factory(window.jQuery);
  } else {
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof window.jQuery !== 'undefined') {
        factory(window.jQuery);
      }
    });
  }
})(function ($) {
  'use strict';

  window.V3PackageTransport = {
    packageId: null,
    encodedId: null,
    packageData: null,
    metricsData: null,
    vehiclesData: [],
    transportersData: [],
    activeFilter: 'all',
    searchQuery: '',
    isLoading: false,
    isLoaded: false,

    init: function () {
      const $app = $('#packageTransportApp');
      if (!$app.length) {
        this.isLoaded = false;
        this.isLoading = false;
        return;
      }

      let rawId = $app.attr('data-package-id') || $app.data('package-id');
      let encodedId = $app.attr('data-encoded-id') || $app.data('encoded-id');

      if (!rawId || rawId === '0' || rawId === 0) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlId = urlParams.get('id') || urlParams.get('package_id');
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
      this.activeFilter = 'all';
      this.searchQuery = '';
      this.isLoading = false;
      this.isLoaded = false;

      this.bindEvents();
      this.loadTransportData(true);
    },

    bindEvents: function () {
      const self = this;
      const $app = $('#packageTransportApp');

      // Filter tabs
      $app.off('click', '.theme-filter-btn').on('click', '.theme-filter-btn', function (e) {
        e.preventDefault();
        const $btn = $(this).closest('.theme-filter-btn');
        $('.theme-filter-btn').removeClass('active');
        $btn.addClass('active');

        self.activeFilter = $btn.data('filter') || 'all';
        self.applyFilters();
      });

      // Search input
      $app.off('input', '#transportSearchInput').on('input', '#transportSearchInput', function () {
        self.searchQuery = ($(this).val() || '').toLowerCase().trim();
        self.applyFilters();
      });

      // Save Package Vehicle Options
      $app.off('click', '#btnSavePackageVehicles').on('click', '#btnSavePackageVehicles', function (e) {
        e.preventDefault();
        self.handleSavePackageVehicles($(this));
      });

      // Assign / Update Transporter
      $app.off('click', '.assignTransporterBtn').on('click', '.assignTransporterBtn', function (e) {
        e.preventDefault();
        self.handleAssignTransporter($(this));
      });

      // Unassign Transporter
      $app.off('click', '.unassignTransporterBtn').on('click', '.unassignTransporterBtn', function (e) {
        e.preventDefault();
        self.handleUnassignTransporter($(this));
      });
    },

    loadTransportData: function (force) {
      if (this.isLoading) return;
      if (!this.packageId && !this.encodedId) return;

      const self = this;
      this.isLoading = true;

      const $grid = $('#transportGrid');
      $grid.html(`
        <div class="hotel-loading-state" style="grid-column: 1 / -1; padding: 48px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0;">
          <div class="v3-spinner" style="width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 14px;"></div>
          <p style="color: #64748b; font-size: 14px; font-weight: 500; margin: 0;">Loading transporters & vehicle options...</p>
        </div>
      `);

      const queryParam = this.packageId ? `package_id=${encodeURIComponent(this.packageId)}` : `id=${encodeURIComponent(this.encodedId)}`;

      $.ajax({
        url: `/api/v3/packages/transport/?${queryParam}`,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          self.isLoading = false;
          self.isLoaded = true;

          if (res && res.status === 'success') {
            self.packageData = res.package || {};
            self.metricsData = res.metrics || {};
            self.vehiclesData = res.vehicles || [];
            self.transportersData = res.transporters || [];

            self.renderHeaderMeta();
            self.renderVehicleSelects();
            self.renderKpis();
            self.renderTransporterCards();
          } else {
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to load transport data.';
            self.renderErrorState(errorMsg);
          }
        },
        error: function (xhr) {
          self.isLoading = false;
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Server error loading transporters.';
          self.renderErrorState(msg);
        }
      });
    },

    renderHeaderMeta: function () {
      const pkg = this.packageData || {};

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

      // Live package URL
      const liveUrl = pkg.link_url || pkg.linkUrl;
      if (liveUrl) {
        $('#btnViewLivePackage').attr('href', liveUrl).show();
      } else {
        $('#btnViewLivePackage').hide();
      }
    },

    renderVehicleSelects: function () {
      const pkg = this.packageData || {};
      const vehicles = this.vehiclesData || [];

      let optionsHtml = '<option value="">-- Select Vehicle --</option>';
      vehicles.forEach(function (v) {
        const vTitle = v.title || v.type;
        const vType = v.type || v.title;
        optionsHtml += `<option value="${vType}">${vTitle}</option>`;
      });

      const $outSelect = $('#selectOutstationVehicle');
      const $locSelect = $('#selectLocalVehicle');

      $outSelect.html(optionsHtml);
      $locSelect.html(optionsHtml);

      if (pkg.outstation_vehicle) {
        $outSelect.val(pkg.outstation_vehicle);
      }
      if (pkg.localsightseen_vehicle) {
        $locSelect.val(pkg.localsightseen_vehicle);
      }

      if (typeof $.fn.select2 === 'function') {
        $('#selectOutstationVehicle, #selectLocalVehicle').select2({
          width: '100%'
        });
      }
    },

    renderKpis: function () {
      const m = this.metricsData || {};
      const total = m.total_transporters !== undefined ? m.total_transporters : this.transportersData.length;
      const assigned = m.assigned_transporters || 0;
      const nearby = m.nearby_transporters || 0;
      const unassigned = m.unassigned_transporters !== undefined ? m.unassigned_transporters : Math.max(0, total - assigned);

      $('#statTotalTransporters').text(total);
      $('#statAssignedTransporters').text(assigned);
      $('#statNearbyTransporters').text(nearby);

      $('#filterCountAll').text(total);
      $('#filterCountAssigned').text(assigned);
      $('#filterCountNearby').text(nearby);
      $('#filterCountUnassigned').text(unassigned);
    },

    renderTransporterCards: function () {
      const self = this;
      const $grid = $('#transportGrid');
      const transporters = this.transportersData || [];
      const vehicles = this.vehiclesData || [];

      if (!transporters || transporters.length === 0) {
        $grid.html(`
          <div style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <i class="fi fi-rr-truck-side" style="font-size: 36px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
            <h4 style="font-size: 16px; font-weight: 600; color: #334155; margin: 0 0 6px;">No Transporters Found</h4>
            <p style="color: #64748b; font-size: 13.5px; margin: 0 0 16px;">There are no transporters registered in your vendor directory yet.</p>
            <a href="/dashboard/vendors/transporter/add/" target="_blank" class="dash-btn-featured" style="display: inline-flex; width: auto; padding: 8px 18px; text-decoration: none;">
              <i class="fi fi-rr-plus"></i> Add New Transporter
            </a>
          </div>
        `);
        return;
      }

      let html = '';
      transporters.forEach(function (t) {
        const isAssigned = !!t.is_assigned;
        const isLocal = !!t.is_local;
        const assignedVehicle = t.assigned_vehicle_type || '';
        const assignedNotes = t.assigned_notes || '';

        // Generate initials
        const nameParts = (t.title || 'Transporter').trim().split(/\s+/);
        const initials = nameParts.length >= 2 
          ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
          : (nameParts[0].substring(0, 2)).toUpperCase();

        // Vehicle dropdown options
        let vehicleOptions = '<option value="">-- Select Preferred Vehicle --</option>';
        vehicles.forEach(function (v) {
          const vType = v.type || v.title;
          const vTitle = v.title || v.type;
          const isSel = (vType === assignedVehicle) ? 'selected' : '';
          vehicleOptions += `<option value="${vType}" ${isSel}>${vTitle}</option>`;
        });

        // Safe phone link & WhatsApp link
        const cleanPhone = (t.mobile || '').replace(/[^0-9+]/g, '');
        const waPhone = cleanPhone.replace(/^\+/, '');

        html += `
          <div class="transport-card ${isAssigned ? 'is-assigned' : ''} ${isLocal ? 'is-nearby' : ''}"
               data-transporter-id="${t.id}"
               data-title="${(t.title || '').toLowerCase()}"
               data-city="${(t.city || '').toLowerCase()}"
               data-state="${(t.service_state || '').toLowerCase()}"
               data-phone="${(t.mobile || '').toLowerCase()}"
               data-assigned="${isAssigned ? '1' : '0'}"
               data-nearby="${isLocal ? '1' : '0'}">

            <!-- Card Header Meta -->
            <div class="transport-card-header-v3">
              <div class="transport-avatar-badge">
                ${initials}
              </div>
              <div class="transport-meta-title-wrap">
                <div class="transport-title-row">
                  <h3 class="transport-transporter-name" title="${t.title || 'Transporter'}">
                    ${t.title || 'Transporter'}
                  </h3>
                  <div class="badge-status-wrap">
                    ${isAssigned 
                      ? '<span class="dash-badge-chip badge-day"><i class="fi fi-rr-check-circle"></i> Assigned</span>'
                      : '<span class="dash-badge-chip badge-none">Unassigned</span>'}
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                  <span class="badge-verified-partner">
                    <i class="fi fi-rr-shield-check"></i> Verified Partner
                  </span>
                  ${t.city ? `<span style="font-size: 11.5px; color: #64748b; font-weight: 500;"><i class="fi fi-rr-marker" style="font-size: 10px;"></i> ${t.city}</span>` : ''}
                </div>
              </div>
            </div>

            ${isLocal ? `
              <div class="transport-match-banner">
                <i class="fi fi-rr-marker"></i> Service Area: <b>${t.service_state || 'Destination Region'}</b> (Matches Itinerary)
              </div>
            ` : ''}

            <!-- Card Body Info -->
            <div class="transport-card-body-v3">
              
              <!-- Quick Contacts Strip -->
              <div class="transport-quick-contacts">
                ${t.mobile ? `
                  <a href="tel:${cleanPhone}" class="transport-contact-btn phone" data-tooltip="Call ${t.mobile}">
                    <i class="fi fi-rr-phone-call"></i> Call
                  </a>
                  <a href="https://wa.me/${waPhone}" target="_blank" class="transport-contact-btn whatsapp" data-tooltip="Chat on WhatsApp">
                    <i class="fi fi-brands-whatsapp"></i> WhatsApp
                  </a>
                ` : ''}
                ${t.email ? `
                  <a href="mailto:${t.email}" class="transport-contact-btn email" data-tooltip="Email ${t.email}">
                    <i class="fi fi-rr-envelope"></i> Email
                  </a>
                ` : ''}
              </div>

              ${t.address ? `
                <div style="font-size: 12.5px; color: #64748b; line-height: 1.4; background: #ffffff; border: 1px solid #f1f5f9; padding: 8px 10px; border-radius: 8px;">
                  <i class="fi fi-rr-map-pin" style="color: #f59e0b; margin-right: 4px;"></i>
                  <span style="color: #334155; font-weight: 500;">${t.address}</span>
                </div>
              ` : ''}

              <!-- Assignment & Vehicle Box -->
              <div class="transport-assignment-box">
                <div class="dash-day-select-wrap">
                  <label><i class="fi fi-rr-car-alt"></i> Assigned Vehicle Type:</label>
                  <select class="selectJS form-control transport-vehicle-select">
                    ${vehicleOptions}
                  </select>
                </div>

                <div class="dash-day-select-wrap">
                  <label><i class="fi fi-rr-document-signed"></i> Assignment Rate / Remarks:</label>
                  <div class="transport-input-group">
                    <i class="fi fi-rr-coins"></i>
                    <input type="text" class="transport-notes-input" placeholder="e.g. ₹3,500/day, AC Sedan, Airport Pickup" value="${assignedNotes}" />
                  </div>
                </div>
              </div>

              <!-- Action Buttons Footer -->
              <div class="transport-card-actions">
                <div class="dash-card-actions-row">
                  <button type="button" class="dash-btn-featured assignTransporterBtn ${isAssigned ? 'is-assigned-btn' : ''}" style="flex: 1;">
                    <i class="fi fi-rr-check"></i> ${isAssigned ? 'Update Assignment' : 'Assign to Package'}
                  </button>
                  ${isAssigned ? `
                    <button type="button" class="dash-btn-unassign unassignTransporterBtn" title="Unassign this transporter from package">
                      <i class="fi fi-rr-trash"></i> Unassign
                    </button>
                  ` : ''}
                </div>
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
        $('.transport-vehicle-select').select2({
          minimumResultsForSearch: Infinity,
          width: '100%'
        });
      }
    },

    applyFilters: function () {
      const filter = (this.activeFilter || 'all').toLowerCase().trim();
      const query = (this.searchQuery || '').toLowerCase().trim();

      let visibleCount = 0;

      $('.transport-card').each(function () {
        const $card = $(this);
        const isAssigned = $card.data('assigned') === 1 || $card.data('assigned') === '1';
        const isNearby = $card.data('nearby') === 1 || $card.data('nearby') === '1';
        const cardTitle = ($card.data('title') || '').toString();
        const cardCity = ($card.data('city') || '').toString();
        const cardState = ($card.data('state') || '').toString();
        const cardPhone = ($card.data('phone') || '').toString();

        let filterPass = true;
        if (filter === 'assigned' && !isAssigned) filterPass = false;
        if (filter === 'unassigned' && isAssigned) filterPass = false;
        if (filter === 'nearby' && !isNearby) filterPass = false;

        let queryPass = true;
        if (query) {
          const combined = `${cardTitle} ${cardCity} ${cardState} ${cardPhone}`.toLowerCase();
          if (combined.indexOf(query) === -1) {
            queryPass = false;
          }
        }

        if (filterPass && queryPass) {
          $card.removeClass('is-hidden').show();
          visibleCount++;
        } else {
          $card.addClass('is-hidden').hide();
        }
      });

      const $noResults = $('#transportGridEmptyFilter');
      if (visibleCount === 0 && this.transportersData.length > 0) {
        if (!$noResults.length) {
          $('#transportGrid').append(`
            <div id="transportGridEmptyFilter" style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
              <i class="fi fi-rr-search" style="font-size: 28px; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
              <p style="margin: 0; font-size: 14px; font-weight: 500;">No transporters match the selected filter criteria.</p>
            </div>
          `);
        } else {
          $noResults.show();
        }
      } else {
        $noResults.remove();
      }
    },

    handleSavePackageVehicles: function ($btn) {
      const self = this;
      const outstation = $('#selectOutstationVehicle').val() || '';
      const local = $('#selectLocalVehicle').val() || '';

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');

      $.ajax({
        url: '/api/v3/packages/transport/',
        type: 'POST',
        data: JSON.stringify({
          action: 'update_vehicles',
          package_id: self.packageId,
          outstation_vehicle: outstation,
          localsightseen_vehicle: local
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-disk"></i> Save Vehicle Options');
          if (res && res.status === 'success') {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg || 'Vehicle options updated successfully.', 'success');
            } else {
              alert('Vehicle options updated successfully.');
            }
          } else {
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to update vehicle options.';
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errorMsg, 'error');
            } else {
              alert(errorMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-disk"></i> Save Vehicle Options');
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error saving vehicle options.';
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', msg, 'error');
          } else {
            alert(msg);
          }
        }
      });
    },

    handleAssignTransporter: function ($btn) {
      const self = this;
      const $card = $btn.closest('.transport-card');
      const transporterId = $card.data('transporter-id');
      const vehicleType = $card.find('.transport-vehicle-select').val() || '';
      const notes = $card.find('.transport-notes-input').val() || '';

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');

      $.ajax({
        url: '/api/v3/packages/transport/',
        type: 'POST',
        data: JSON.stringify({
          action: 'assign_transporter',
          package_id: self.packageId,
          transporter_id: transporterId,
          vehicle_type: vehicleType,
          notes: notes,
          is_assigned: true
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false);
          if (res && res.status === 'success') {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg || 'Transporter assigned successfully.', 'success');
            }

            // Update card UI to assigned state
            $card.data('assigned', 1).addClass('is-assigned');
            $card.find('.badge-status-wrap').html('<span class="dash-badge-chip badge-day"><i class="fi fi-rr-check-circle"></i> Assigned</span>');
            $card.find('.dash-card-actions-row').html(`
              <button type="button" class="dash-btn-featured assignTransporterBtn is-assigned-btn" style="flex: 1;">
                <i class="fi fi-rr-check"></i> Update Assignment
              </button>
              <button type="button" class="dash-btn-unassign unassignTransporterBtn" title="Unassign this transporter from package">
                <i class="fi fi-rr-trash"></i> Unassign
              </button>
            `);

            self.updateMetricsCounts();
            self.applyFilters();
          } else {
            $btn.html('<i class="fi fi-rr-check"></i> ' + ($card.data('assigned') === 1 ? 'Update Assignment' : 'Assign to Package'));
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to assign transporter.';
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errorMsg, 'error');
            } else {
              alert(errorMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-check"></i> ' + ($card.data('assigned') === 1 ? 'Update Assignment' : 'Assign to Package'));
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error assigning transporter.';
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', msg, 'error');
          } else {
            alert(msg);
          }
        }
      });
    },

    handleUnassignTransporter: function ($btn) {
      const self = this;
      const $card = $btn.closest('.transport-card');
      const transporterId = $card.data('transporter-id');

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i>');

      $.ajax({
        url: '/api/v3/packages/transport/',
        type: 'POST',
        data: JSON.stringify({
          action: 'unassign_transporter',
          package_id: self.packageId,
          transporter_id: transporterId,
          is_assigned: false
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false);
          if (res && res.status === 'success') {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg || 'Transporter unassigned from package.', 'success');
            }

            // Update card UI to unassigned state
            $card.data('assigned', 0).removeClass('is-assigned');
            $card.find('.badge-status-wrap').html('<span class="dash-badge-chip badge-none">Unassigned</span>');
            $card.find('.dash-card-actions-row').html(`
              <button type="button" class="dash-btn-featured assignTransporterBtn" style="flex: 1;">
                <i class="fi fi-rr-check"></i> Assign to Package
              </button>
            `);

            self.updateMetricsCounts();
            self.applyFilters();
          } else {
            $btn.html('<i class="fi fi-rr-trash"></i> Unassign');
            const errorMsg = (res && res.msg) ? res.msg : 'Failed to unassign transporter.';
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', errorMsg, 'error');
            } else {
              alert(errorMsg);
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fi fi-rr-trash"></i> Unassign');
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error unassigning transporter.';
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', msg, 'error');
          } else {
            alert(msg);
          }
        }
      });
    },

    updateMetricsCounts: function () {
      let assignedCount = 0;
      let totalCount = 0;
      let nearbyCount = 0;

      $('.transport-card').each(function () {
        totalCount++;
        if ($(this).data('assigned') === 1 || $(this).data('assigned') === '1') {
          assignedCount++;
        }
        if ($(this).data('nearby') === 1 || $(this).data('nearby') === '1') {
          nearbyCount++;
        }
      });

      const unassignedCount = Math.max(0, totalCount - assignedCount);

      $('#statTotalTransporters').text(totalCount);
      $('#statAssignedTransporters').text(assignedCount);
      $('#statNearbyTransporters').text(nearbyCount);

      $('#filterCountAll').text(totalCount);
      $('#filterCountAssigned').text(assignedCount);
      $('#filterCountNearby').text(nearbyCount);
      $('#filterCountUnassigned').text(unassignedCount);
    },

    renderErrorState: function (msg) {
      $('#transportGrid').html(`
        <div style="grid-column: 1 / -1; padding: 48px 20px; text-align: center; background: #ffffff; border-radius: 12px; border: 1px dashed #fca5a5; color: #dc2626;">
          <i class="fi fi-rr-cross-circle" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
          <p style="margin: 0; font-size: 15px; font-weight: 600;">${msg}</p>
          <button type="button" class="dash-btn-featured" onclick="window.V3PackageTransport.loadTransportData(true);" style="display: inline-flex; width: auto; padding: 8px 18px; margin-top: 14px; background: #4f46e5; color: #ffffff;">
            <i class="fi fi-rr-refresh"></i> Retry Loading
          </button>
        </div>
      `);
    }
  };

  // Auto-init on direct load or SPA trigger
  $(document).ready(function () {
    if ($('#packageTransportApp').length > 0) {
      window.V3PackageTransport.init();
    }
  });

  $(document).on('spa:loaded', function () {
    if ($('#packageTransportApp').length > 0) {
      window.V3PackageTransport.init();
    }
  });
});
