/**
 * V3 Package Price List Management Module
 * Path: app/dashboard/v3/default/desktop/assets/js/package-price-list.js
 * Handles dynamic API fetching, rendering, SPA navigation, and modal editing for pricing slabs.
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

  window.V3PackagePriceList = {
    packageId: 0,
    encodedId: '',
    packageType: 'domestic',
    pricesData: null,
    packageInfo: null,
    isLoading: false,
    loadedPackageId: null,
    eventsBound: false,
    selectedPriceId: 0,

    init: function (opts) {
      if (!opts || !opts.packageId) return;

      const pkgId = String(opts.packageId);
      const $app = $('#packagePriceListApp');
      if (!$app.length) return;

      const hasRenderedRows = $('#priceTableBody .price-row').length > 0;
      if (this.loadedPackageId === pkgId && hasRenderedRows && (this.isLoading || this.pricesData)) {
        return;
      }

      if (this.isLoading) return;

      this.loadedPackageId = pkgId;
      this.packageId = pkgId;
      this.encodedId = opts.encodedId || btoa(pkgId);
      this.packageType = opts.packageType || $app.attr('data-package-type') || 'domestic';
      this.pricesData = null;
      this.packageInfo = null;

      this.bindEvents();
      this.loadPricesFromApi();
    },

    autoInit: function () {
      const $app = $('#packagePriceListApp');
      if ($app.length > 0) {
        let pkgId = $app.attr('data-package-id') || $app.data('package-id');
        let encId = $app.attr('data-encoded-id') || $app.data('encoded-id') || '';
        let pkgType = $app.attr('data-package-type') || $app.data('package-type') || 'domestic';

        if (!pkgId || pkgId === '0') {
          const urlParams = new URLSearchParams(window.location.search);
          const rawId = urlParams.get('id');
          if (rawId) {
            try {
              const decoded = atob(rawId);
              if (decoded && !isNaN(decoded)) {
                pkgId = decoded;
                encId = rawId;
              } else if (!isNaN(rawId)) {
                pkgId = rawId;
                encId = btoa(rawId);
              }
            } catch (e) {
              if (!isNaN(rawId)) {
                pkgId = rawId;
                encId = btoa(rawId);
              }
            }
          }
        }

        if (pkgId && pkgId !== '0') {
          this.init({ packageId: pkgId, encodedId: encId, packageType: pkgType });
        }
      }
    },

    bindEvents: function () {
      if (this.eventsBound) return;
      this.eventsBound = true;
      const self = this;

      // 1. Open Add Price Modal
      $(document).on('click', '#btnAddPriceSlab', function (e) {
        e.preventDefault();
        self.openAddModal();
      });

      // 2. Open Edit Price Modal
      $(document).on('click', '.btn-edit-price-slab', function (e) {
        e.preventDefault();
        const priceId = $(this).attr('data-price-id') || $(this).data('price-id');
        self.openEditModal(priceId);
      });

      // 3. Close Price Modal
      $(document).on('click', '.v3-close-price-modal, .btn-cancel-price-edit', function (e) {
        e.preventDefault();
        self.closeModal();
      });

      // Close on backdrop click
      $(document).on('click', '#editPriceModal', function (e) {
        if (e.target === this) {
          self.closeModal();
        }
      });

      // Close on Escape key
      $(document).on('keydown', function (e) {
        if ((e.key === 'Escape' || e.keyCode === 27) && $('#editPriceModal').hasClass('is-active')) {
          self.closeModal();
        }
      });

      // 4. Submit Price Form
      $(document).on('submit', '#formEditPrice', function (e) {
        e.preventDefault();
        self.submitPriceUpdate($(this));
      });

      // 5. Delete Price Slab
      $(document).on('click', '.btn-delete-price-slab', function (e) {
        e.preventDefault();
        const priceId = $(this).attr('data-price-id') || $(this).data('price-id');
        self.deletePrice(priceId);
      });
    },

    loadPricesFromApi: function () {
      const self = this;
      if (self.isLoading) return;
      self.isLoading = true;

      self.renderLoadingState();

      $.ajax({
        url: `/api/v3/packages/prices/?id=${self.packageId}`,
        method: 'GET',
        dataType: 'json',
        cache: false,
        success: function (res) {
          self.isLoading = false;
          if (res && res.status === 'success') {
            self.pricesData = res.prices || [];
            self.packageInfo = res.package || {};
            self.renderHeaderAndKPIs(res.package, res.metrics);
            self.renderPriceTable(res.prices);
          } else {
            self.renderErrorState(res ? res.msg : 'Unable to load pricing data.');
          }
        },
        error: function (xhr) {
          self.isLoading = false;
          console.error('[V3 Package Prices API Error]:', xhr.statusText);
          self.renderErrorState('Failed to connect to the pricing server. Please try again.');
        }
      });
    },

    renderHeaderAndKPIs: function (pkg, metrics) {
      if (pkg) {
        $('#pkgHeaderTitleText').text(pkg.title || 'Tour Package');
        const themeRaw = pkg.theme || 'Standard';
        const themeSlug = themeRaw.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        $('#pkgMetaTheme').attr('class', 'pkg-theme-pill pkg-theme-' + themeSlug).text(themeRaw);
        $('#pkgMetaDuration').html(`
          <i class="fi fi-rr-moon pkg-duration-moon"></i> ${pkg.nights || 0}N / <i class="fi fi-rr-sun pkg-duration-sun"></i> ${pkg.days || 1}D
        `);
        $('#pkgMetaLocation').html(`
          <i class="fi fi-rr-marker" style="color: #0284c7;"></i> ${pkg.location_label || 'Destination'}
        `);

        const cat = pkg.category || 'domestic';
        $('#btnEditPackage').attr('href', `/dashboard/packages/${cat}/edit/?id=${pkg.encoded_id}`);
        $('#btnBackPackages').attr('href', `/dashboard/packages/${cat}/list/`);
      }

      if (metrics) {
        $('#statTotalSlabs').text(metrics.total_slabs || 0);
        $('#statActiveRates').text(metrics.active_slabs || 0);
        $('#statLowestAdult').text(
          metrics.lowest_adult_selling !== null && metrics.lowest_adult_selling !== undefined
            ? `₹ ${Number(metrics.lowest_adult_selling).toLocaleString('en-IN')}`
            : '—'
        );
        $('#statLowestChild').text(
          metrics.lowest_child_selling !== null && metrics.lowest_child_selling !== undefined
            ? `₹ ${Number(metrics.lowest_child_selling).toLocaleString('en-IN')}`
            : '—'
        );
        $('#statPriceCountBadge').text(
          `${metrics.total_slabs || 0} price slab${metrics.total_slabs !== 1 ? 's' : ''}`
        );
      }
    },

    renderLoadingState: function () {
      $('#priceTableBody').html(`
        <tr>
          <td colspan="12" class="table-loading-state">
            <div class="table-loading-spinner"></div>
            <p>Loading pricing matrix & seasonal slabs...</p>
          </td>
        </tr>
      `);
    },

    renderErrorState: function (msg) {
      $('#priceTableBody').html(`
        <tr>
          <td colspan="12" class="table-error-state" style="text-align: center; padding: 40px 20px; color: #ef4444;">
            <i class="fi fi-rr-exclamation" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
            <p style="font-size: 14.5px; font-weight: 600; margin-bottom: 6px;">${msg}</p>
            <button type="button" class="btn-action-pill btn-action-view" onclick="window.V3PackagePriceList.loadPricesFromApi()">
              <i class="fi fi-rr-refresh"></i> Retry Loading
            </button>
          </td>
        </tr>
      `);
    },

    renderPriceTable: function (prices) {
      const $tbody = $('#priceTableBody');
      if (!prices || prices.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="12" class="table-empty-state" style="text-align: center; padding: 48px 20px; color: #64748b;">
              <i class="fi fi-rr-box-open" style="font-size: 40px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
              <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">No Pricing Slabs Configured Yet</p>
              <p style="font-size: 13px; color: #94a3b8; margin-bottom: 16px;">Add single, double, or seasonal rates to power instant quotes and bookings.</p>
              <button type="button" class="btn-v3-primary" id="btnEmptyAddPriceSlab" style="display: inline-flex;">
                <i class="fi fi-rr-plus"></i> Add First Price Slab
              </button>
            </td>
          </tr>
        `);
        $(document).off('click', '#btnEmptyAddPriceSlab').on('click', '#btnEmptyAddPriceSlab', function () {
          window.V3PackagePriceList.openAddModal();
        });
        return;
      }

      let html = '';
      prices.forEach(function (item) {
        html += `
          <tr class="price-row" data-price-id="${item.id}">
            <td class="col-validity">
              <div class="validity-date-badge">
                <i class="fi fi-rr-calendar"></i>
                <span>${item.validity_text}</span>
              </div>
            </td>
            <!-- Adult Rates -->
            <td class="col-rate muted">₹ ${Number(item.net_adult).toLocaleString('en-IN')}</td>
            <td class="col-rate strike">₹ ${Number(item.mrp_adult).toLocaleString('en-IN')}</td>
            <td class="col-rate highlight-adult">₹ ${Number(item.selling_adult).toLocaleString('en-IN')}</td>
            <td class="col-rate muted col-divider">₹ ${Number(item.agent_adult).toLocaleString('en-IN')}</td>
            <!-- Child Rates -->
            <td class="col-rate muted">₹ ${Number(item.net_child).toLocaleString('en-IN')}</td>
            <td class="col-rate strike">₹ ${Number(item.mrp_child).toLocaleString('en-IN')}</td>
            <td class="col-rate highlight-child">₹ ${Number(item.selling_child).toLocaleString('en-IN')}</td>
            <td class="col-rate muted col-divider">₹ ${Number(item.agent_child).toLocaleString('en-IN')}</td>
            <!-- Type -->
            <td class="col-center">
              <span class="v3-price-type-pill ${item.type_class}">${item.type_label}</span>
            </td>
            <!-- Status -->
            <td class="col-center">
              <span class="v3-status-badge ${item.status_class}">
                <span class="status-pulse-dot"></span> ${item.status_label}
              </span>
            </td>
            <!-- Actions -->
            <td class="col-actions">
              <div class="table-actions-group">
                <button type="button" class="btn-action-icon edit btn-edit-price-slab" data-price-id="${item.id}" data-tooltip="Edit Price Slab" title="Edit Price Slab">
                  <i class="fi fi-rr-edit"></i>
                </button>
                <button type="button" class="btn-action-icon delete btn-delete-price-slab" data-price-id="${item.id}" data-tooltip="Delete Price Slab" title="Delete Price Slab">
                  <i class="fi fi-rr-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    },

    openAddModal: function () {
      this.selectedPriceId = 0;
      $('#editPriceId').val('');
      $('#modalPriceTitle').text('Add New Pricing Slab');
      $('#modalPriceSubtitle').text('Set seasonal validity dates, sharing type, and adult/child rates');
      $('#modalPriceIcon').attr('class', 'fi fi-rr-plus');
      $('#btnSubmitPriceText').text('Create Price Slab');

      const todayStr = (typeof moment === 'function') ? moment().format('DD MMMM') : '';
      const nextMonthStr = (typeof moment === 'function') ? moment().add(30, 'days').format('DD MMMM') : '';

      $('#editPriceType').val('single');
      $('#editStartDate').val(todayStr);
      $('#editEndDate').val(nextMonthStr);

      $('#editAdultNet').val('');
      $('#editAdultMRP').val('');
      $('#editAdultSelling').val('');
      $('#editAdultAgent').val('');

      $('#editChildNet').val('');
      $('#editChildMRP').val('');
      $('#editChildSelling').val('');
      $('#editChildAgent').val('');

      this.initDatePickers();
      $('#editPriceModal').addClass('is-active').show();
    },

    openEditModal: function (priceId) {
      if (!this.pricesData) return;
      const slab = this.pricesData.find((p) => String(p.id) === String(priceId));
      if (!slab) return;

      this.selectedPriceId = slab.id;
      $('#editPriceId').val(slab.id);
      $('#modalPriceTitle').text('Update Pricing Slab');
      $('#modalPriceSubtitle').text('Adjust seasonal validity dates and adult/child rates');
      $('#modalPriceIcon').attr('class', 'fi fi-rr-edit');
      $('#btnSubmitPriceText').text('Save Changes');

      $('#editPriceType').val(slab.type || 'single');
      $('#editStartDate').val(slab.date_start || '');
      $('#editEndDate').val(slab.date_end || '');

      $('#editAdultNet').val(slab.net_adult || '');
      $('#editAdultMRP').val(slab.mrp_adult || '');
      $('#editAdultSelling').val(slab.selling_adult || '');
      $('#editAdultAgent').val(slab.agent_adult || '');

      $('#editChildNet').val(slab.net_child || '');
      $('#editChildMRP').val(slab.mrp_child || '');
      $('#editChildSelling').val(slab.selling_child || '');
      $('#editChildAgent').val(slab.agent_child || '');

      this.initDatePickers();
      $('#editPriceModal').addClass('is-active').show();
    },

    initDatePickers: function () {
      if (typeof $.fn.daterangepicker === 'function') {
        const startVal = $('#editStartDate').val() || (typeof moment === 'function' ? moment().format('DD MMMM') : '');
        const endVal = $('#editEndDate').val() || (typeof moment === 'function' ? moment().add(30, 'days').format('DD MMMM') : '');

        try {
          if ($('#editStartDate').data('daterangepicker')) {
            $('#editStartDate').data('daterangepicker').remove();
          }
          if ($('#editEndDate').data('daterangepicker')) {
            $('#editEndDate').data('daterangepicker').remove();
          }
        } catch (e) {}

        const startMoment = (typeof moment === 'function' && moment(startVal, ['DD MMMM', 'DD MMM', 'YYYY-MM-DD']).isValid()) ? moment(startVal, ['DD MMMM', 'DD MMM', 'YYYY-MM-DD']) : moment();
        const endMoment = (typeof moment === 'function' && moment(endVal, ['DD MMMM', 'DD MMM', 'YYYY-MM-DD']).isValid()) ? moment(endVal, ['DD MMMM', 'DD MMM', 'YYYY-MM-DD']) : moment().add(30, 'days');

        const fullMonths = [
          'January', 'February', 'March', 'April', 'May', 'June',
          'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $('#editStartDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          autoApply: true,
          autoUpdateInput: true,
          startDate: startMoment,
          locale: {
            format: 'DD MMMM',
            monthNames: fullMonths
          },
          parentEl: '#editPriceModal'
        }).on('apply.daterangepicker', function (ev, picker) {
          $(this).val(picker.startDate.format('DD MMMM')).trigger('change');
        });

        $('#editEndDate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          autoApply: true,
          autoUpdateInput: true,
          startDate: endMoment,
          locale: {
            format: 'DD MMMM',
            monthNames: fullMonths
          },
          parentEl: '#editPriceModal'
        }).on('apply.daterangepicker', function (ev, picker) {
          $(this).val(picker.startDate.format('DD MMMM')).trigger('change');
        });
      }
    },

    closeModal: function () {
      $('#editPriceModal').removeClass('is-active').hide();
      this.selectedPriceId = 0;
    },

    submitPriceUpdate: function ($form) {
      const self = this;
      const $btn = $('#btnSubmitPriceUpdate');
      const originalHtml = $btn.html();

      $btn.prop('disabled', true).html('<i class="fi fi-rr-spinner animate-spin"></i> Saving...');

      const priceId = $('#editPriceId').val();
      const payload = {
        action: priceId ? 'update' : 'create',
        id: priceId || 0,
        package_id: self.packageId,
        priceType: $('#editPriceType').val() || 'single',
        startDate: $('#editStartDate').val(),
        endDate: $('#editEndDate').val(),
        adultNet: $('#editAdultNet').val(),
        adultMRP: $('#editAdultMRP').val(),
        adultSelling: $('#editAdultSelling').val(),
        adultAgent: $('#editAdultAgent').val(),
        childNet: $('#editChildNet').val(),
        childMRP: $('#editChildMRP').val(),
        childSelling: $('#editChildSelling').val(),
        childAgent: $('#editChildAgent').val()
      };

      $.ajax({
        url: '/api/v3/packages/prices/',
        method: 'POST',
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false).html(originalHtml);
          if (res && res.status === 'success') {
            self.closeModal();
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res.msg || 'Price updated successfully.', 'success');
            }
            // Seamlessly refresh prices without page reload
            self.loadPricesFromApi();
          } else {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', res ? res.msg : 'Failed to save price slab.', 'error');
            }
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html(originalHtml);
          if (typeof popUpMessage === 'function') {
            popUpMessage('center', 'Server error while saving price.', 'error');
          }
        }
      });
    },

    deletePrice: function (priceId) {
      const self = this;
      if (!priceId) return;

      const confirmDelete = function () {
        $.ajax({
          url: '/api/v3/packages/prices/',
          method: 'POST',
          contentType: 'application/json; charset=utf-8',
          data: JSON.stringify({ action: 'delete', id: priceId }),
          dataType: 'json',
          success: function (res) {
            if (res && res.status === 'success') {
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', res.msg || 'Price slab removed.', 'success');
              }
              self.loadPricesFromApi();
            } else {
              if (typeof popUpMessage === 'function') {
                popUpMessage('center', res ? res.msg : 'Unable to delete price slab.', 'error');
              }
            }
          },
          error: function () {
            if (typeof popUpMessage === 'function') {
              popUpMessage('center', 'Server error while deleting price slab.', 'error');
            }
          }
        });
      };

      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({
          title: 'Delete Price Slab?',
          text: 'Are you sure you want to remove this pricing session?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          if (result.value) {
            confirmDelete();
          }
        });
      } else {
        if (window.confirm('Are you sure you want to delete this price slab?')) {
          confirmDelete();
        }
      }
    }
  };

  // Auto-init on direct page load and SPA navigation
  $(function () {
    window.V3PackagePriceList.autoInit();
  });

  $(document).on('spa:loaded', function () {
    window.V3PackagePriceList.autoInit();
  });
});
