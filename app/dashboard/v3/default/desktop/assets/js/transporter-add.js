/**
 * V3 Transporter Form Module (Add & Edit)
 * Path: app/dashboard/v3/default/desktop/assets/js/transporter-add.js
 * Handles live preview, interactive tags, fleet checklist, and asynchronous API communication with /api/v3/transporters/
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

  window.V3TransporterAdd = {
    isLoadingData: false,
    lastLoadedId: null,
    activeFetchXhr: null,

    init: function () {
      const $addApp = $('#addTransporterApp');
      const $editApp = $('#editTransporterApp');

      if (!$addApp.length && !$editApp.length) {
        this.lastLoadedId = null;
        return;
      }

      this.isSubmitting = false;
      this.addAnotherMode = false;
      this.selectedStates = [];

      // Determine mode
      this.isEditMode = $editApp.length > 0;
      this.transporterId = this.resolveTransporterId();

      this.clearAllErrors();
      this.initCitySelect2();
      this.initServiceStatesSelect2();
      this.bindEvents();

      if (this.isEditMode && this.transporterId) {
        this.fetchTransporterData(this.transporterId);
      } else {
        this.lastLoadedId = null;
        $('#transporterEditLoading').hide();
        $('#transporterEditLayout').show();
        this.syncChipActiveStates();
        this.updateLivePreview();
      }
    },

    autoInit: function () {
      this.init();
    },

    initCitySelect2: function () {
      const self = this;
      const $citySelect = $('#transporterCity');

      if (!$citySelect.length || typeof $.fn.select2 === 'undefined') return;

      if ($citySelect.hasClass('select2-hidden-accessible')) {
        $citySelect.select2('destroy');
      }

      $citySelect.select2({
        placeholder: 'Select or search operational city...',
        allowClear: true,
        width: '100%',
        tags: true,
        dropdownAutoWidth: true,
        ajax: {
          url: '/api/v3/cities/',
          dataType: 'json',
          delay: 200,
          data: function (params) {
            return {
              search: params.term || '',
              q: params.term || ''
            };
          },
          processResults: function (data) {
            return {
              results: (data && data.results) || []
            };
          },
          cache: true
        },
        templateResult: function (item) {
          if (item.loading) return item.text || 'Searching operational cities...';
          if (!item.id) return item.text;
          const city = item.city || item.text || item.id;
          const state = item.state || '';
          const country = item.country || '';
          const meta = [state, country].filter(Boolean).join(', ');

          const $wrap = $(`
            <div class="v3-select2-city-row">
              <div class="v3-select2-city-name"><i class="fi fi-rr-city"></i> <span>${escapeHtml(city)}</span></div>
              ${meta ? `<div class="v3-select2-city-meta"><i class="fi fi-rr-marker"></i> ${escapeHtml(meta)}</div>` : ''}
            </div>
          `);
          return $wrap;
        },
        templateSelection: function (item) {
          return item.city || item.id || item.text || '';
        },
        createTag: function (params) {
          const term = $.trim(params.term);
          if (term === '') {
            return null;
          }
          return {
            id: term,
            text: term,
            city: term,
            isNew: true
          };
        }
      });

      $citySelect.off('change.v3city select2:select.v3city select2:clear.v3city')
        .on('change.v3city select2:select.v3city select2:clear.v3city', function (e) {
          self.updateLivePreview();
          const cityVal = ($(this).val() || '').trim();
          if (cityVal) {
            self.clearFieldError('transporterCity');
          }

          // Auto-populate pincode when city is selected
          let pincodeFound = '';
          if (e && e.params && e.params.data && e.params.data.pincode) {
            pincodeFound = e.params.data.pincode;
          }

          if (!pincodeFound && cityVal) {
            pincodeFound = self.lookupPincode(cityVal);
          }

          if (pincodeFound) {
            $('#transporterPincode').val(pincodeFound).trigger('input');
            self.clearFieldError('transporterPincode');
          }

          self.updateLivePreview();
        });
    },

    initServiceStatesSelect2: function () {
      const self = this;
      const $statesSelect = $('#transporterServiceStates');

      if (!$statesSelect.length || typeof $.fn.select2 === 'undefined') return;

      if ($statesSelect.hasClass('select2-hidden-accessible')) {
        $statesSelect.select2('destroy');
      }

      $statesSelect.select2({
        placeholder: 'Search & select operational states, regions or cities...',
        allowClear: true,
        width: '100%',
        tags: true,
        dropdownAutoWidth: true,
        ajax: {
          url: '/api/v3/cities/',
          dataType: 'json',
          delay: 200,
          data: function (params) {
            return {
              search: params.term || '',
              q: params.term || ''
            };
          },
          processResults: function (data) {
            const results = [];
            const seen = new Set();

            if (data && data.results && data.results.length > 0) {
              data.results.forEach(function (item) {
                const val = item.city || item.state || item.id || item.text;
                if (!val || seen.has(val.toLowerCase())) return;
                seen.add(val.toLowerCase());

                const isState = item.type === 'state' || (item.state && !item.city);
                results.push({
                  id: val,
                  text: val,
                  type: isState ? 'state' : 'city',
                  state: item.state || '',
                  country: item.country || 'India',
                  subtext: isState ? 'State' : (item.state ? item.state + ', ' + (item.country || 'India') : '')
                });
              });
            }

            if (data && data.states && Array.isArray(data.states)) {
              data.states.forEach(function (st) {
                if (st && !seen.has(st.toLowerCase())) {
                  seen.add(st.toLowerCase());
                  results.push({
                    id: st,
                    text: st,
                    type: 'state',
                    subtext: 'State'
                  });
                }
              });
            }

            return { results: results };
          },
          cache: true
        },
        templateResult: function (item) {
          if (item.loading) return item.text || 'Searching operational regions...';
          if (!item.id) return item.text;

          const title = item.text || item.id;
          const isState = item.type === 'state';
          const sub = item.subtext || (item.state ? item.state + (item.country ? ', ' + item.country : '') : '');

          const $wrap = $(`
            <div class="v3-select2-state-row">
              <div class="v3-select2-state-name">
                <i class="fi ${isState ? 'fi-rr-map' : 'fi-rr-marker'}"></i>
                <span>${escapeHtml(title)}</span>
              </div>
              ${sub ? `<span class="v3-select2-state-badge">${escapeHtml(sub)}</span>` : ''}
            </div>
          `);
          return $wrap;
        },
        templateSelection: function (item) {
          return item.text || item.id || '';
        },
        createTag: function (params) {
          const term = $.trim(params.term);
          if (term === '') return null;
          return {
            id: term,
            text: term,
            isNew: true
          };
        }
      });

      $statesSelect.off('change.v3states select2:select.v3states select2:unselect.v3states select2:clear.v3states')
        .on('change.v3states select2:select.v3states select2:unselect.v3states select2:clear.v3states', function () {
          const rawVal = $(this).val() || [];
          const arr = Array.isArray(rawVal) ? rawVal : [rawVal];
          self.selectedStates = arr.map(function (s) { return (s || '').trim(); }).filter(Boolean);

          if (self.selectedStates.length > 0) {
            self.clearFieldError('transporterServiceArea');
          }

          $('#transporterServiceArea').val(self.selectedStates.join(', '));
          self.syncChipActiveStates();
          self.updateLivePreview();
        });
    },

    lookupPincode: function (cityName) {
      if (!cityName) return '';
      const clean = cityName.toLowerCase().trim();
      const pinMap = {
        'delhi': '110001', 'new delhi': '110001', 'mumbai': '400001', 'bangalore': '560001',
        'bengaluru': '560001', 'kolkata': '700001', 'chennai': '600001', 'hyderabad': '500001',
        'pune': '411001', 'ahmedabad': '380001', 'jaipur': '302001', 'udaipur': '313001',
        'jodhpur': '342001', 'jaisalmer': '345001', 'bikaner': '334001', 'pushkar': '305022',
        'ajmer': '305001', 'mount abu': '307501', 'shimla': '171001', 'manali': '175131',
        'kullu': '175101', 'dharamshala': '176215', 'mcleodganj': '176219', 'dalhousie': '176304',
        'kasauli': '173204', 'jibhi': '175119', 'spiti': '172114', 'srinagar': '190001',
        'gulmarg': '193403', 'pahalgam': '192126', 'leh': '194101', 'ladakh': '194101',
        'agra': '282001', 'varanasi': '221001', 'lucknow': '226001', 'kanpur': '208001',
        'amritsar': '143001', 'chandigarh': '160001', 'ludhiana': '141001', 'jalandhar': '144001',
        'dehradun': '248001', 'haridwar': '249401', 'rishikesh': '249201', 'nainital': '263001',
        'mussoorie': '248179', 'ranikhet': '263645', 'jim corbett': '244715', 'almora': '263601',
        'kausani': '263639', 'auli': '246443', 'goa': '403001', 'panaji': '403001',
        'arambol': '403524', 'calangute': '403516', 'candolim': '403515', 'baga': '403516',
        'margao': '403601', 'kochi': '682001', 'cochin': '682001', 'munnar': '685612',
        'alleppey': '688001', 'alappuzha': '688001', 'thekkady': '685509', 'wayanad': '673121',
        'kovalam': '695527', 'varkala': '695141', 'trivandrum': '695001', 'thiruvananthapuram': '695001',
        'ooty': '643001', 'kodaikanal': '624101', 'coimbatore': '641001', 'madurai': '625001',
        'rameswaram': '623526', 'kanyakumari': '629702', 'mysore': '570001', 'mysuru': '570001',
        'coorg': '571201', 'hampi': '583239', 'gokarna': '581326', 'darjeeling': '734101',
        'gangtok': '737101', 'kalimpong': '734301', 'pelling': '737113', 'shillong': '793001',
        'guwahati': '781001', 'kaziranga': '785609', 'port blair': '744101', 'havelock': '744211',
        'puri': '752001', 'bhubaneswar': '751001', 'patna': '800001', 'gaya': '823001',
        'bodhgaya': '824231', 'ranchi': '834001', 'jamshedpur': '831001', 'indore': '452001',
        'bhopal': '462001', 'gwalior': '474001', 'khajuraho': '471606', 'ujjain': '456001',
        'surat': '395001', 'vadodara': '390001', 'rajkot': '360001', 'bhuj': '370001',
        'rann of kutch': '370510', 'somnath': '362268', 'dwarka': '361335'
      };

      if (pinMap[clean]) return pinMap[clean];
      const keys = Object.keys(pinMap);
      for (let i = 0; i < keys.length; i++) {
        const k = keys[i];
        if (clean.includes(k) || k.includes(clean)) {
          return pinMap[k];
        }
      }
      return '';
    },

    resolveTransporterId: function () {
      const $hidden = $('#transporterId');
      if ($hidden.val()) {
        return $hidden.val();
      }

      const params = new URLSearchParams(window.location.search);
      const idParam = params.get('id') || params.get('transporter_id');
      if (idParam) {
        return idParam;
      }

      // Check URL pathname (e.g. /dashboard/vendors/transporter/update/123 or base64)
      const pathSegments = window.location.pathname.split('/').filter(Boolean);
      const updateIdx = pathSegments.indexOf('update');
      if (updateIdx !== -1 && pathSegments[updateIdx + 1]) {
        return pathSegments[updateIdx + 1];
      }

      return null;
    },

    fetchTransporterData: function (id) {
      const self = this;
      if (!id) return;

      // Prevent duplicate concurrent requests for the same transporter
      if (this.isLoadingData && this.lastLoadedId === id) {
        return;
      }

      if (this.activeFetchXhr && typeof this.activeFetchXhr.abort === 'function') {
        this.activeFetchXhr.abort();
        this.activeFetchXhr = null;
      }

      this.isLoadingData = true;
      this.lastLoadedId = id;

      $('#transporterEditLoading').show();
      $('#transporterEditLayout').hide();

      this.activeFetchXhr = $.ajax({
        url: '/api/v3/transporters/list/',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function (res) {
          self.isLoadingData = false;
          self.activeFetchXhr = null;
          $('#transporterEditLoading').hide();
          $('#transporterEditLayout').show();

          let item = null;
          if (res && res.status === 'success' && res.transporters && res.transporters.length > 0) {
            item = res.transporters[0];
          } else if (res && res.data) {
            item = res.data;
          }

          if (item) {
            self.populateForm(item);
          } else {
            self.showGeneralError('Transporter not found.');
          }
        },
        error: function (xhr, status) {
          if (status === 'abort') return;
          self.isLoadingData = false;
          self.activeFetchXhr = null;
          $('#transporterEditLoading').hide();
          $('#transporterEditLayout').show();
          self.showGeneralError('Failed to load transporter details from server.');
        }
      });
    },

    populateForm: function (item) {
      const actualId = item.id || item.transporter_id;
      this.transporterId = actualId;
      $('#transporterId').val(actualId);
      $('#editTransporterId').val(actualId);
      $('#transporterName').val(item.title || item.name || '');
      $('#transporterMobile').val(item.mobile || item.phone || '');
      $('#transporterEmail').val(item.email || '');

      const cityVal = item.city || item.transporter_city || '';
      if (cityVal) {
        const $citySelect = $('#transporterCity');
        if ($citySelect.find(`option[value="${cityVal}"]`).length === 0) {
          const newOption = new Option(cityVal, cityVal, true, true);
          $citySelect.append(newOption);
        } else {
          $citySelect.val(cityVal);
        }
        if (typeof $.fn.select2 !== 'undefined' && $citySelect.hasClass('select2-hidden-accessible')) {
          $citySelect.trigger('change.select2');
        }
      } else {
        $('#transporterCity').val('').trigger('change.select2');
      }

      $('#transporterPincode').val(item.pincode || item.pinCode || '');
      $('#transporterAddress').val(item.address || '');

      if (item.title) {
        $('#headerTransporterTitle').text('Edit: ' + item.title);
      }

      // Use API-provided fields first, then fall back to parsing description metadata
      let rawDesc = item.description || '';
      let descClean = rawDesc;
      let contactPerson = item.contact_person || '';
      let altMobile = item.alt_mobile || '';

      // Parse embedded metadata "[Contact Person: X | Alternate Contact: Y]" only if direct fields are missing
      if ((!contactPerson || !altMobile) && rawDesc.indexOf('[') !== -1 && rawDesc.indexOf(']') !== -1) {
        const metaBlock = rawDesc.substring(rawDesc.lastIndexOf('[') + 1, rawDesc.lastIndexOf(']'));
        descClean = rawDesc.substring(0, rawDesc.lastIndexOf('[')).trim();

        const parts = metaBlock.split('|').map(p => p.trim());
        parts.forEach(p => {
          if (!contactPerson && p.toLowerCase().startsWith('contact person:')) {
            contactPerson = p.substring(15).trim();
          } else if (!altMobile && p.toLowerCase().startsWith('alternate contact:')) {
            altMobile = p.substring(18).trim();
          }
        });
      }

      if ($('#transporterContactPerson').length) {
        $('#transporterContactPerson').val(contactPerson);
      }
      if ($('#transporterAltMobile').length) {
        $('#transporterAltMobile').val(altMobile);
      }
      $('#transporterDescription').val(descClean);


      // Populate Service States
      const serviceStr = item.service_state || item.serviceArea || '';
      if (serviceStr) {
        this.selectedStates = serviceStr.split(',').map(s => s.trim()).filter(Boolean);
      } else {
        this.selectedStates = [];
      }

      const $statesSelect = $('#transporterServiceStates');
      if ($statesSelect.length) {
        $statesSelect.empty();
        this.selectedStates.forEach(st => {
          const opt = new Option(st, st, true, true);
          $statesSelect.append(opt);
        });
        if (typeof $.fn.select2 !== 'undefined' && $statesSelect.hasClass('select2-hidden-accessible')) {
          $statesSelect.trigger('change.select2');
        }
      }

      $('#transporterServiceArea').val(this.selectedStates.join(', '));
      this.syncChipActiveStates();
      this.updateLivePreview();
    },

    bindEvents: function () {
      const self = this;
      const $container = $(document);

      if ($('body').data('v3-transporter-form-bound')) {
        return;
      }
      $('body').data('v3-transporter-form-bound', true);

      // Autocomplete suppression for Select2 search fields
      $container.off('select2:open.v3suppress').on('select2:open.v3suppress', function () {
        setTimeout(function () {
          const $search = $('.select2-container--open .select2-search__field');
          if ($search.length) {
            $search.attr('autocomplete', 'new-password')
                   .attr('autocorrect', 'off')
                   .attr('autocapitalize', 'off')
                   .attr('spellcheck', 'false')
                   .attr('data-lpignore', 'true')
                   .attr('data-form-type', 'other');
          }
        }, 10);
      });

      // 1. Quick State Chips Click Handler
      $container.off('click', '.v3-chip-pill').on('click', '.v3-chip-pill', function (e) {
        e.preventDefault();
        const stateName = $(this).data('state');
        self.toggleStateTag(stateName);
      });

      // 2. Live Form Inputs Change
      $container.off('input change', '#transporterAddForm input, #transporterAddForm textarea, #transporterAddForm select').on('input change', '#transporterAddForm input, #transporterAddForm textarea, #transporterAddForm select', function () {
        const id = $(this).attr('id');
        if (id) {
          self.clearFieldError(id);
        }
        self.updateLivePreview();
      });

      // 2b. Auto-clone Primary Mobile to WhatsApp Number if blank
      $container.off('input.v3mobclone change.v3mobclone', '#transporterMobile').on('input.v3mobclone change.v3mobclone', '#transporterMobile', function () {
        const primaryMob = ($(this).val() || '').trim();
        const $altMob = $('#transporterAltMobile');
        if ($altMob.length) {
          const currentAlt = ($altMob.val() || '').trim();
          if (!currentAlt || $altMob.data('auto-cloned') === true) {
            $altMob.val(primaryMob);
            if (primaryMob) {
              $altMob.data('auto-cloned', true);
            } else {
              $altMob.removeData('auto-cloned');
            }
          }
        }
      });

      // Track manual changes to WhatsApp Number so auto-clone doesn't overwrite user custom numbers
      $container.off('input.v3manualalt', '#transporterAltMobile').on('input.v3manualalt', '#transporterAltMobile', function () {
        const altVal = ($(this).val() || '').trim();
        const primaryMob = ($('#transporterMobile').val() || '').trim();
        if (altVal && altVal !== primaryMob) {
          $(this).data('auto-cloned', false);
        } else if (!altVal) {
          $(this).data('auto-cloned', true);
        }
      });

      // 3. Header Save Button Proxy
      $container.off('click', '#btnHeaderSaveTransporter').on('click', '#btnHeaderSaveTransporter', function (e) {
        e.preventDefault();
        self.addAnotherMode = false;
        $('#transporterAddForm').trigger('submit');
      });

      // 4. Save & Add Another Button
      $container.off('click', '#btnSaveAndAddAnother').on('click', '#btnSaveAndAddAnother', function (e) {
        e.preventDefault();
        self.addAnotherMode = true;
        $('#transporterAddForm').trigger('submit');
      });

      // 5. Reset Form Button
      $container.off('click', '#btnResetTransporterForm').on('click', '#btnResetTransporterForm', function (e) {
        e.preventDefault();
        self.resetForm();
      });

      // 6. Form Submit Handler
      $container.off('submit', '#transporterAddForm').on('submit', '#transporterAddForm', function (e) {
        e.preventDefault();
        self.handleFormSubmit();
      });
    },

    toggleStateTag: function (stateName) {
      if (!stateName) return;
      const clean = stateName.trim();
      if (!clean) return;

      const $select = $('#transporterServiceStates');
      let currentVals = $select.val() || [];
      if (!Array.isArray(currentVals)) currentVals = [currentVals];
      currentVals = currentVals.map(s => (s || '').trim()).filter(Boolean);

      const existsIdx = currentVals.findIndex(s => s.toLowerCase() === clean.toLowerCase());

      if (existsIdx !== -1) {
        currentVals.splice(existsIdx, 1);
      } else {
        if (clean.toLowerCase() === 'all india') {
          currentVals = ['All India'];
        } else {
          currentVals = currentVals.filter(s => s.toLowerCase() !== 'all india');
          currentVals.push(clean);
        }
      }

      currentVals.forEach(v => {
        if ($select.find(`option[value="${v}"]`).length === 0) {
          const newOption = new Option(v, v, true, true);
          $select.append(newOption);
        }
      });

      $select.val(currentVals).trigger('change');
    },

    syncChipActiveStates: function () {
      const self = this;
      $('.v3-chip-pill').each(function () {
        const pillState = ($(this).data('state') || '').toString().toLowerCase();
        const isActive = self.selectedStates.some(s => s.toLowerCase() === pillState);
        if (isActive) {
          $(this).addClass('active');
        } else {
          $(this).removeClass('active');
        }
      });
    },

    updateLivePreview: function () {
      const name = ($('#transporterTitle').val() || $('#transporterName').val() || '').trim() || 'Transporter Agency';
      const contactPerson = ($('#transporterContactPerson').val() || '').trim();
      const mobile = ($('#transporterMobile').val() || '').trim();
      const email = ($('#transporterEmail').val() || '').trim();
      const city = ($('#transporterCity').val() || '').trim();
      const pincode = ($('#transporterPincode').val() || '').trim();
      const desc = ($('#transporterDescription').val() || '').trim();

      // 1. Title & Avatar
      $('#previewTitle').text(name);
      const initials = getInitials(name);
      $('#previewInitials').text(initials);

      // 2. Contact Person
      if (contactPerson) {
        $('#previewContactPerson').html(`<i class="fi fi-rr-user"></i> <span>${escapeHtml(contactPerson)}</span>`).show();
      } else {
        $('#previewContactPerson').hide();
      }

      // 3. Mobile
      if (mobile) {
        $('#previewMobile').text(mobile).removeClass('placeholder-text');
      } else {
        $('#previewMobile').text('Contact Phone').addClass('placeholder-text');
      }

      // 4. Email
      if (email) {
        $('#previewEmail').text(email).removeClass('placeholder-text');
      } else {
        $('#previewEmail').text('Email Address').addClass('placeholder-text');
      }

      // 5. Location
      let locationText = [];
      if (city) locationText.push(city);
      if (pincode) locationText.push(pincode);
      if (locationText.length) {
        $('#previewLocation').text(locationText.join(', ')).removeClass('placeholder-text');
      } else {
        $('#previewLocation').text('Base City, PIN').addClass('placeholder-text');
      }

      // 6. Service Coverage Preview
      const $coverageContainer = $('#previewCoverageList');
      if ($coverageContainer.length) {
        $coverageContainer.empty();
        if (this.selectedStates.length > 0) {
          this.selectedStates.forEach(st => {
            $coverageContainer.append(`<span class="live-coverage-pill"><i class="fi fi-rr-map-marker"></i> ${escapeHtml(st)}</span>`);
          });
        } else {
          $coverageContainer.append('<span class="live-empty-pill">No service regions added</span>');
        }
      }

      // 7. Notes
      if (desc) {
        $('#previewNotesText').text(desc);
        $('#previewNotesBox').show();
      } else {
        $('#previewNotesBox').hide();
      }

      // 8. Completion Progress Calculation
      let score = 0;
      let totalFields = 6;
      if (($('#transporterTitle').val() || $('#transporterName').val() || '').trim()) score++;
      if ($('#transporterMobile').val().trim()) score++;
      if ($('#transporterCity').val().trim()) score++;
      if ($('#transporterPincode').val().trim()) score++;
      if ($('#transporterAddress').val().trim()) score++;
      if (this.selectedStates.length > 0) score++;

      const pct = Math.round((score / totalFields) * 100);
      $('#formProgressPct').text(pct + '%');
      $('#formProgressBar').css('width', pct + '%');

      if (pct === 100) {
        $('#formProgressBar').css('background', 'linear-gradient(90deg, #10b981, #059669)');
      } else {
        $('#formProgressBar').css('background', 'linear-gradient(90deg, #6366f1, #4f46e5)');
      }
    },

    validateForm: function () {
      let isValid = true;
      this.clearAllErrors();

      const name = ($('#transporterTitle').val() || $('#transporterName').val() || '').trim();
      const mobile = $('#transporterMobile').val().trim();
      const city = $('#transporterCity').val().trim();
      const pincode = $('#transporterPincode').val().trim();
      const address = $('#transporterAddress').val().trim();
      const email = $('#transporterEmail').val().trim();

      const nameFieldId = $('#transporterTitle').length ? 'transporterTitle' : 'transporterName';
      if (!name) {
        this.showFieldError(nameFieldId, 'Transporter / Agency name is required.');
        isValid = false;
      }

      if (!mobile) {
        this.showFieldError('transporterMobile', 'Primary contact mobile is required.');
        isValid = false;
      } else if (mobile.replace(/[^0-9]/g, '').length < 8) {
        this.showFieldError('transporterMobile', 'Please enter a valid contact phone number.');
        isValid = false;
      }

      if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          this.showFieldError('transporterEmail', 'Please enter a valid email address.');
          isValid = false;
        }
      }

      if (!city) {
        this.showFieldError('transporterCity', 'Operational base city is required.');
        isValid = false;
      }

      if (!pincode) {
        this.showFieldError('transporterPincode', 'Postal / PIN code is required.');
        isValid = false;
      }

      if (!address) {
        this.showFieldError('transporterAddress', 'Operating address is required.');
        isValid = false;
      }

      if (this.selectedStates.length === 0) {
        this.showFieldError('transporterServiceArea', 'Please add at least one service region or select from quick chips.');
        isValid = false;
      }

      return isValid;
    },

    showFieldError: function (fieldId, message) {
      const $field = $('#' + fieldId);
      $field.closest('.v3-input-icon-wrap, .v3-tags-input-container').addClass('has-error');

      const capitalized = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
      const $errSpan = $('#err' + capitalized);
      if ($errSpan.length) {
        $errSpan.text(message).show();
      }
    },

    clearFieldError: function (fieldId) {
      const $field = $('#' + fieldId);
      $field.closest('.v3-input-icon-wrap, .v3-tags-input-container').removeClass('has-error');

      const capitalized = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
      const $errSpan = $('#err' + capitalized);
      if ($errSpan.length) {
        $errSpan.text('').hide();
      }
    },

    clearAllErrors: function () {
      $('.v3-input-icon-wrap, .v3-tags-input-container').removeClass('has-error');
      $('.v3-field-error').text('').hide();
    },

    resetForm: function () {
      $('#transporterAddForm')[0].reset();
      this.selectedStates = [];
      if (typeof $.fn.select2 !== 'undefined') {
        if ($('#transporterCity').hasClass('select2-hidden-accessible')) {
          $('#transporterCity').val(null).trigger('change');
        }
        if ($('#transporterServiceStates').hasClass('select2-hidden-accessible')) {
          $('#transporterServiceStates').val(null).trigger('change');
        }
      }
      this.clearAllErrors();
      this.syncChipActiveStates();
      this.updateLivePreview();
      const $firstInput = $('#transporterTitle').length ? $('#transporterTitle') : $('#transporterName');
      $firstInput.focus();
    },

    handleFormSubmit: function () {
      const self = this;
      if (this.isSubmitting) return;

      if (!this.validateForm()) {
        const $firstError = $('.has-error').first();
        if ($firstError.length) {
          $('html, body').animate({
            scrollTop: $firstError.offset().top - 120
          }, 200);
          $firstError.find('input, textarea').focus();
        }
        return;
      }

      const transporterId = $('#transporterId').val() || $('#editTransporterId').val() || self.transporterId || self.resolveTransporterId();
      const isUpdate = self.isEditMode || (!!transporterId && transporterId !== '' && transporterId !== '0');
      const agencyName = ($('#transporterName').val() || $('#transporterTitle').val() || '').trim();

      const payload = {
        transporter_id: transporterId,
        name: agencyName,
        title: agencyName,
        contact_person: ($('#transporterContactPerson').val() || '').trim(),
        mobile: $('#transporterMobile').val().trim(),
        alt_mobile: ($('#transporterAltMobile').val() || '').trim(),
        email: $('#transporterEmail').val().trim(),
        city: $('#transporterCity').val().trim(),
        pincode: $('#transporterPincode').val().trim(),
        address: $('#transporterAddress').val().trim(),
        serviceArea: self.selectedStates.join(', '),
        description: $('#transporterDescription').val().trim()
      };

      const targetUrl = isUpdate ? '/api/v3/transporters/update/' : '/api/v3/transporters/create/';

      this.isSubmitting = true;
      this.setButtonsLoading(true, isUpdate);

      $.ajax({
        url: targetUrl,
        type: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        success: function (res) {
          self.isSubmitting = false;
          self.setButtonsLoading(false, isUpdate);

          if (res && res.status === 'success') {
            const successTitle = isUpdate ? 'Transporter Updated!' : 'Transporter Added!';
            const successMsg = res.msg || (isUpdate ? 'Transporter changes saved successfully!' : 'New transporter added successfully!');

            if (typeof window.swal === 'function') {
              window.swal({
                title: successTitle,
                text: successMsg,
                type: 'success',
                icon: 'success',
                confirmButtonText: self.addAnotherMode ? 'Add Another' : 'View Directory',
                button: self.addAnotherMode ? 'Add Another' : 'View Directory',
                timer: self.addAnotherMode ? 2000 : 1500
              }).then(function () {
                if (self.addAnotherMode) {
                  self.resetForm();
                } else {
                  window.location.href = '/dashboard/vendors/transporter/list/';
                }
              });
            } else if (typeof window.popUpMessage === 'function') {
              window.popUpMessage('center', successMsg, 'success');
              setTimeout(function () {
                if (self.addAnotherMode) {
                  self.resetForm();
                } else {
                  window.location.href = '/dashboard/vendors/transporter/list/';
                }
              }, 1200);
            } else {
              alert(successMsg);
              if (self.addAnotherMode) {
                self.resetForm();
              } else {
                window.location.href = '/dashboard/vendors/transporter/list/';
              }
            }

            if (self.addAnotherMode) {
              self.resetForm();
            }
          } else {
            const errorMsg = (res && res.msg) ? res.msg : 'Unable to save transporter.';
            self.showGeneralError(errorMsg);
          }
        },
        error: function (xhr) {
          self.isSubmitting = false;
          self.setButtonsLoading(false, isUpdate);

          let errorMsg = 'An unexpected server error occurred while saving the transporter.';
          try {
            const res = JSON.parse(xhr.responseText);
            if (res && res.msg) {
              errorMsg = res.msg;
            }
          } catch (e) {}

          self.showGeneralError(errorMsg);
        }
      });
    },

    setButtonsLoading: function (isLoading, isUpdate) {
      const $submitBtn = $('#btnSubmitTransporter, #btnSubmitTransporterForm');
      const $headerSaveBtn = $('#btnHeaderSaveTransporter');
      const $addAnotherBtn = $('#btnSaveAndAddAnother');
      const defaultText = isUpdate ? 'Save Changes' : 'Save Transporter';

      if (isLoading) {
        $submitBtn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');
        $headerSaveBtn.prop('disabled', true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');
        $addAnotherBtn.prop('disabled', true);
      } else {
        $submitBtn.prop('disabled', false).html('<i class="fi fi-rr-check"></i> ' + defaultText);
        $headerSaveBtn.prop('disabled', false).html('<i class="fi fi-rr-check"></i> ' + defaultText);
        $addAnotherBtn.prop('disabled', false);
      }
    },

    showGeneralError: function (msg) {
      if (typeof window.swal === 'function') {
        window.swal('Failed to Save', msg, 'error');
      } else if (typeof window.popUpMessage === 'function') {
        window.popUpMessage('center', msg, 'error');
      } else {
        alert(msg);
      }
    }
  };

  // Helper: Escape HTML
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Helper: Get Initials
  function getInitials(name) {
    if (!name) return 'TR';
    const words = name.trim().split(/\s+/);
    if (words.length === 1) {
      return words[0].substring(0, 2).toUpperCase();
    }
    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
  }

  // Auto-initialize on DOM ready (SPA transitions are driven by app.js initPageModules)
  $(document).ready(function () {
    if ($('#addTransporterApp').length || $('#editTransporterApp').length) {
      window.V3TransporterAdd.init();
    }
  });
});
