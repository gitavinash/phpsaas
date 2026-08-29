(function (factory) {
  if (window.jQuery) factory(window.jQuery);
  else document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery) factory(window.jQuery);
  });
})(function ($) {
  'use strict';

  /* ─── Helpers ────────────────────────────────────────────────── */
  var escHTML = function (v) {
    return $('<div>').text(v == null ? '' : String(v)).html();
  };

  var PROPERTY_TYPES = {
    apartments:    'Apartments',
    capsule_hotel: 'Capsule Hotel',
    guest_house:   'Guest House',
    inn:           'Inn',
    hostel:        'Hostel',
    house:         'House',
    home_stay:     'Home Stay',
    '1_star_hotel':'1 Star Hotel',
    '2_star_hotel':'2 Star Hotel',
    '3_star_hotel':'3 Star Hotel',
    '4_star_hotel':'4 Star Hotel',
    '5_star_hotel':'5 Star Hotel',
    motel:         'Motel',
    lodge:         'Lodge',
    tent:          'Tent / Camp',
    farm_stay:     'Farm Stay',
    resorts:       'Resort',
  };

  /* ─── Module ─────────────────────────────────────────────────── */
  window.V3PropertyUpdate = {
    encodedId:          '',
    propertyId:         0,
    data:               null,
    saving:             false,

    // Hotel Gallery State
    existingGallery:    [],
    stagedGalleryFiles: [],

    // Master Amenities State
    masterAmenities:    [],
    selectedAmenities:  [],

    // Rooms State
    masterRoomTypes:    [],
    propertyRooms:      [],
    editingRoomId:      0,
    roomExistingGallery:[],
    roomStagedFiles:    [],

    /* Required fields for progress tracking */
    REQUIRED_FIELDS: [
      '#propName', '#propType', '#propAddress',
      '#propCity', '#propPincode', '#propEmail', '#propMobile',
    ],

    init: function () {
      var root = document.getElementById('propertyUpdateApp');
      if (!root) return;

      var urlParams = new URLSearchParams(window.location.search);
      this.encodedId = root.getAttribute('data-id') || urlParams.get('id') || '';
      this.propertyId = parseInt(root.getAttribute('data-raw-id'), 10) || 0;

      if (!this.encodedId) {
        this._showLoadError('No property ID supplied in the URL. Please go back and try again.');
        return;
      }

      this.saving = false;
      this.existingGallery = [];
      this.stagedGalleryFiles = [];
      this.selectedAmenities = [];
      this.propertyRooms = [];
      this.roomExistingGallery = [];
      this.roomStagedFiles = [];

      this._initSelect2();
      this._bindEvents();
      this._loadProperty();
      this._loadMasterAmenities();
      this._loadMasterRoomTypes();
    },

    /* ── Data Load ─────────────────────────────────────────────── */
    _loadProperty: function () {
      var self = this;
      $('#propertyEditLoading').show();
      $('#propertyEditLayout').addClass('d-none').hide();

      $.ajax({
        url:      '/api/v3/properties/detail/',
        method:   'GET',
        data:     { id: self.encodedId },
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success' && res.property) {
            self.data       = res.property;
            self.propertyId = res.property.id;
            self._populate(res.property);
            self._loadPropertyRooms();
            self._showLayout();
          } else {
            self._showLoadError(res.msg || 'Failed to load property details.');
          }
        },
        error: function (xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.msg) || 'Server error while loading property.';
          self._showLoadError(msg);
        },
      });
    },

    /* ── Populate Form ─────────────────────────────────────────── */
    _populate: function (p) {
      $('#propId').val(p.id);
      $('#propName').val(p.title);
      $('#propType').val(p.type);
      $('#propDescription').val(p.description);
      $('#propAddress').val(p.address);
      $('#propPincode').val(p.pincode);
      $('#propEmail').val(p.email);
      $('#propMobile').val(p.mobile);
      $('#propSupportEmail').val(p.support_email);
      $('#propSupportMobile').val(p.support_mobile);
      $('#propMapUrl').val(p.map_url);

      /* 1. Property Listing Status (Active: 1, Inactive: 0) */
      $('input[name="status"][value="' + p.status + '"]').prop('checked', true);

      /* 2. Website Publishing Checkbox (Independent) */
      var isWebPublished = !!p.is_published_website;
      $('#chkPublishToWebsite').prop('checked', isWebPublished);

      /* 3. Website Live Link Display */
      if (p.live_url && isWebPublished) {
        $('#btnViewLiveHotel').attr('href', p.live_url).show();
        $('#propLiveWebsiteUrl').text(p.live_url);
        $('#propLiveWebsiteBtn').attr('href', p.live_url);
        $('#propLiveWebsiteBox').show();
      } else if (p.live_url) {
        $('#propLiveWebsiteUrl').text(p.live_url);
        $('#propLiveWebsiteBtn').attr('href', p.live_url);
        $('#btnViewLiveHotel').attr('href', p.live_url).hide();
        $('#propLiveWebsiteBox').hide();
      } else {
        $('#btnViewLiveHotel').hide();
        $('#propLiveWebsiteBox').hide();
      }

      /* Property Type Select2 */
      if (p.type) {
        $('#propType').val(p.type).trigger('change.select2');
      }

      /* City Select2 — pre-populate with saved value */
      if (p.city) {
        var $citySelect = $('#propCity');
        if ($citySelect.find('option[value="' + p.city + '"]').length === 0) {
          $citySelect.append(new Option(p.city, p.city, true, true)).trigger('change.select2');
        } else {
          $citySelect.val(p.city).trigger('change.select2');
        }
      }

      /* Feature Thumbnail preview */
      this._setThumbPreview(p.thumbnail, p.title);

      /* Hotel Photo Gallery */
      this.existingGallery = Array.isArray(p.gallery) ? p.gallery.slice() : [];
      this._renderExistingGallery();

      /* Hotel Selected Amenities */
      this.selectedAmenities = Array.isArray(p.amenities) ? p.amenities.slice() : [];
      this._syncSelectedAmenitiesUI();

      /* Header */
      $('#propHeaderTitle').text('Edit — ' + (p.title || 'Property'));

      /* Live preview */
      this._updateLivePreview();
      this._updateProgress();
    },

    /* ══════════════════════════════════════════════════════════════
       AMENITIES (FROM MASTER DATA)
    ══════════════════════════════════════════════════════════════ */
    _getAmenityIcon: function (title, cat) {
      var t = (title || '').toLowerCase();
      if (t.indexOf('wifi') !== -1 || t.indexOf('internet') !== -1 || t.indexOf('lan') !== -1) return 'fi fi-rr-wifi';
      if (t.indexOf('pool') !== -1 || t.indexOf('swim') !== -1) return 'fi fi-rr-swimmer';
      if (t.indexOf('air condition') !== -1 || t.indexOf('ac') !== -1 || t.indexOf('cooling') !== -1) return 'fi fi-rr-snowflake';
      if (t.indexOf('park') !== -1 || t.indexOf('valet') !== -1 || t.indexOf('garage') !== -1) return 'fi fi-rr-car';
      if (t.indexOf('room service') !== -1 || t.indexOf('concierge') !== -1 || t.indexOf('bell') !== -1) return 'fi fi-rr-concierge-bell';
      if (t.indexOf('elevator') !== -1 || t.indexOf('lift') !== -1) return 'fi fi-rr-elevator';
      if (t.indexOf('housekeep') !== -1 || t.indexOf('clean') !== -1) return 'fi fi-rr-broom';
      if (t.indexOf('laundry') !== -1 || t.indexOf('dry clean') !== -1 || t.indexOf('iron') !== -1) return 'fi fi-rr-washer';
      if (t.indexOf('smoke') !== -1 || t.indexOf('smoking') !== -1) return 'fi fi-rr-smoking';
      if (t.indexOf('power') !== -1 || t.indexOf('generator') !== -1 || t.indexOf('backup') !== -1) return 'fi fi-rr-bolt';
      if (t.indexOf('refrigerat') !== -1 || t.indexOf('fridge') !== -1 || t.indexOf('minibar') !== -1) return 'fi fi-rr-temperature-frigid';
      if (t.indexOf('newspaper') !== -1 || t.indexOf('news') !== -1) return 'fi fi-rr-newspaper';
      if (t.indexOf('umbrella') !== -1) return 'fi fi-rr-umbrella';
      if (t.indexOf('breakfast') !== -1 || t.indexOf('restaurant') !== -1 || t.indexOf('dining') !== -1 || t.indexOf('food') !== -1 || t.indexOf('cafe') !== -1 || t.indexOf('bar') !== -1) return 'fi fi-rr-restaurant';
      if (t.indexOf('gym') !== -1 || t.indexOf('fitness') !== -1 || t.indexOf('workout') !== -1) return 'fi fi-rr-dumbbell';
      if (t.indexOf('spa') !== -1 || t.indexOf('massage') !== -1 || t.indexOf('sauna') !== -1 || t.indexOf('jacuzzi') !== -1) return 'fi fi-rr-spa';
      if (t.indexOf('tv') !== -1 || t.indexOf('television') !== -1 || t.indexOf('screen') !== -1) return 'fi fi-rr-screen';
      if (t.indexOf('safe') !== -1 || t.indexOf('locker') !== -1) return 'fi fi-rr-shield-check';
      if (t.indexOf('balcony') !== -1 || t.indexOf('terrace') !== -1 || t.indexOf('view') !== -1) return 'fi fi-rr-mountain';
      if (t.indexOf('coffee') !== -1 || t.indexOf('tea') !== -1 || t.indexOf('kettle') !== -1) return 'fi fi-rr-coffee';
      if (t.indexOf('doctor') !== -1 || t.indexOf('medical') !== -1 || t.indexOf('first aid') !== -1) return 'fi fi-rr-stethoscope';
      if (t.indexOf('desk') !== -1 || t.indexOf('reception') !== -1 || t.indexOf('check') !== -1) return 'fi fi-rr-clock';
      return 'fi fi-rr-check-circle';
    },

    activeAmenityCategory: 'all',

    _getCategoryIcon: function (catKey) {
      var k = (catKey || '').toLowerCase();
      if (k.indexOf('basic') !== -1) return 'fi fi-rr-building';
      if (k.indexOf('beauty') !== -1 || k.indexOf('spa') !== -1) return 'fi fi-rr-spa';
      if (k.indexOf('dining') !== -1 || k.indexOf('food') !== -1) return 'fi fi-rr-restaurant';
      if (k.indexOf('room') !== -1) return 'fi fi-rr-bed';
      if (k.indexOf('transport') !== -1 || k.indexOf('transfer') !== -1) return 'fi fi-rr-car';
      if (k.indexOf('media') !== -1 || k.indexOf('tech') !== -1) return 'fi fi-rr-screen';
      if (k.indexOf('bath') !== -1) return 'fi fi-rr-bath';
      return 'fi fi-rr-layers';
    },

    _loadMasterAmenities: function () {
      var self = this;
      $.ajax({
        url:      '/api/v3/amenities/',
        method:   'GET',
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success' && Array.isArray(res.amenities)) {
            self.masterAmenities = res.amenities;
            self._renderMasterAmenities();
            self._syncSelectedAmenitiesUI();
          }
        },
      });
    },

    _renderMasterAmenities: function () {
      var self = this;
      var $container = $('#propAmenitiesCategoriesContainer');
      var $catFilterBar = $('#propAmenityCatFilterBar');

      if (!this.masterAmenities.length) {
        $container.html('<div class="prop-gallery-empty-state"><i class="fi fi-rr-swimmer"></i><span>No master amenities available.</span></div>');
        $catFilterBar.html('');
        return;
      }

      // Group by category type
      var grouped = {};
      this.masterAmenities.forEach(function (a) {
        var cat = a.type || 'other_facilities';
        if (!grouped[cat]) grouped[cat] = [];
        grouped[cat].push(a);
      });

      // Populate Category Filter Select2 Dropdown
      var totalCount = this.masterAmenities.length;
      var catOptionsHtml = '<option value="all">All Categories (' + totalCount + ')</option>';

      var html = '';
      Object.keys(grouped).forEach(function (catKey) {
        var items = grouped[catKey];
        var catLabel = items[0].type_label || (catKey.replace(/_/g, ' ')).toUpperCase();
        catOptionsHtml += '<option value="' + escHTML(catKey) + '">' + escHTML(catLabel) + ' (' + items.length + ')</option>';

        html += '<div class="prop-amenity-category-block" data-category="' + escHTML(catKey) + '">' +
                  '<div class="prop-amenity-category-title">' +
                    '<span>' + escHTML(catLabel) + ' (' + items.length + ')</span>' +
                    '<div class="prop-amenity-category-actions">' +
                      '<button type="button" class="prop-btn-text-action btn-select-cat-all" data-cat="' + escHTML(catKey) + '">Select All</button>' +
                      '<span style="color:#cbd5e1;">|</span>' +
                      '<button type="button" class="prop-btn-text-action btn-clear-cat-all" data-cat="' + escHTML(catKey) + '">Clear</button>' +
                    '</div>' +
                  '</div>' +
                  '<div class="prop-amenities-grid">';

        items.forEach(function (item) {
          var icon = self._getAmenityIcon(item.title, catKey);
          html += '<div class="prop-amenity-chip" data-id="' + item.id + '" data-title="' + escHTML(item.title) + '" data-cat="' + escHTML(catKey) + '">' +
                    '<div class="prop-amenity-icon-box">' +
                      '<i class="' + icon + '"></i>' +
                    '</div>' +
                    '<span class="amenity-title">' + escHTML(item.title) + '</span>' +
                    '<div class="prop-amenity-check-box">' +
                      '<i class="fi fi-rr-check"></i>' +
                    '</div>' +
                  '</div>';
        });

        html += '</div></div>';
      });

      $('#propAmenityCatSelect').html(catOptionsHtml);
      if ($.fn.select2) {
        if ($('#propAmenityCatSelect').data('select2')) {
          $('#propAmenityCatSelect').select2('destroy');
        }
        $('#propAmenityCatSelect').select2({ width: '100%' });
      }

      $container.html(html);
      self._applyAmenityFilters();
    },

    _applyAmenityFilters: function () {
      var q = ($('#propAmenitySearch').val() || '').toLowerCase().trim();
      var activeCat = this.activeAmenityCategory || 'all';

      $('.prop-amenity-category-block').each(function () {
        var blockCat = $(this).attr('data-category');
        var isCatMatch = (activeCat === 'all' || activeCat === blockCat);

        if (!isCatMatch) {
          $(this).hide();
          return;
        }

        var visibleInBlock = 0;
        $(this).find('.prop-amenity-chip').each(function () {
          var title = $(this).find('.amenity-title').text().toLowerCase();
          if (!q || title.indexOf(q) !== -1) {
            $(this).show();
            visibleInBlock++;
          } else {
            $(this).hide();
          }
        });

        if (visibleInBlock > 0) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    },

    _syncSelectedAmenitiesUI: function () {
      var self = this;
      var count = this.selectedAmenities.length;
      $('#propAmenitySelectedCount').text(count);
      $('#tabAmenitiesCountBadge').text(count + ' Selected');
      $('#propAmenitiesInput').val(JSON.stringify(this.selectedAmenities));

      // Highlight selected chips
      $('.prop-amenity-chip').each(function () {
        var id = parseInt($(this).attr('data-id'), 10);
        var title = $(this).attr('data-title');
        var isSelected = self.selectedAmenities.some(function (x) {
          return x === id || x === title || (x && x.id === id) || (x && x.title === title);
        });
        if (isSelected) {
          $(this).addClass('selected');
        } else {
          $(this).removeClass('selected');
        }
      });
    },

    /* ══════════════════════════════════════════════════════════════
       ROOMS & RATE PLANS (WITH ROOM GALLERY)
    ══════════════════════════════════════════════════════════════ */
    _loadMasterRoomTypes: function (callback) {
      var self = this;
      $.ajax({
        url:      '/api/v3/room-types/',
        method:   'GET',
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success' && Array.isArray(res.room_types)) {
            self.masterRoomTypes = res.room_types;

            // Populate Add/Edit Room Modal Type Dropdown
            var $sel = $('#roomModalType');
            $sel.empty().append('<option value="">— Select Room Type from Master Library —</option>');
            res.room_types.forEach(function (rt) {
              var val = rt.title.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
              $sel.append(new Option(rt.title, val));
            });
            if ($.fn.select2) {
              $sel.trigger('change.select2');
            }

            // Populate Master Gallery Type Dropdown
            var $galSel = $('#roomGalleryTypeSelect');
            $galSel.empty();
            res.room_types.forEach(function (rt) {
              var val = rt.title.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
              $galSel.append(new Option(rt.title, val));
            });
            if ($.fn.select2) {
              $galSel.trigger('change.select2');
            }
          }
          if (typeof callback === 'function') callback();
        },
        error: function () {
          if (typeof callback === 'function') callback();
        }
      });
    },

    _loadPropertyRooms: function () {
      var self = this;
      if (!this.propertyId) return;

      $.ajax({
        url:      '/api/v3/properties/rooms/',
        method:   'GET',
        data:     { property_id: self.propertyId },
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success' && Array.isArray(res.rooms)) {
            self.propertyRooms = res.rooms;
            self._renderPropertyRooms();
          }
        },
        error: function () {
          $('#propRoomsListContainer').html('<div class="prop-gallery-empty-state"><i class="fi fi-rr-bed"></i><span>Failed to load rooms.</span></div>');
        },
      });
    },

    _renderPropertyRooms: function () {
      var self = this;
      var $list = $('#propRoomsListContainer');
      var count = this.propertyRooms.length;

      $('#propRoomsHeaderBadge').text(count + (count === 1 ? ' Room' : ' Rooms'));
      $('#tabRoomsCountBadge').text(count + (count === 1 ? ' Room' : ' Rooms'));

      if (count === 0) {
        $list.html(
          '<div class="prop-gallery-empty-state" style="padding: 36px 20px;">' +
            '<i class="fi fi-rr-bed-alt" style="font-size: 32px; color: #94a3b8;"></i>' +
            '<span style="font-size: 14px; font-weight: 600; color: #475569; margin-top: 4px;">No rooms configured yet</span>' +
            '<p style="font-size: 12px; color: #64748b; margin: 4px 0 14px;">Add room types and meal plan tariffs (EP, CP, MAP, AP) for this hotel.</p>' +
            '<button type="button" class="v3-btn-primary" id="btnEmptyAddRoom"><i class="fi fi-rr-plus"></i> Add First Room</button>' +
          '</div>'
        );
        return;
      }

      var rowsHtml = '';
      this.propertyRooms.forEach(function (room) {
        var seasonText = (room.price_from && room.price_to) ? (escHTML(room.price_from) + ' → ' + escHTML(room.price_to)) : 'All Year';
        var seasonName = room.season_name ? escHTML(room.season_name) : 'Standard';

        var fmt = function (num) {
          var n = Number(num || 0);
          return n > 0 ? ('₹&nbsp;' + n.toLocaleString('en-IN')) : '₹&nbsp;0';
        };

        rowsHtml += '<tr class="price-row" data-room-id="' + room.id + '">' +
                      '<td class="col-validity">' +
                        '<div style="font-weight: 700; color: #0f172a; font-size: 13.5px; display: flex; align-items: center; gap: 6px;">' +
                          '<i class="fi fi-rr-bed" style="color: #0d9488;"></i>' +
                          '<span>' + escHTML(room.type_label || room.type) + '</span>' +
                        '</div>' +
                        '<div class="validity-date-badge" style="margin-top: 4px; font-size: 12px; color: #475569;">' +
                          '<i class="fi fi-rr-calendar"></i>' +
                          '<span>' + seasonText + '</span>' +
                          (seasonName ? '<span class="v3-price-type-pill" style="background:#f1f5f9; color:#475569; font-weight:600; font-size:10.5px; padding:1px 7px; border-radius:10px; border:1px solid #e2e8f0; margin-left:4px;">' + seasonName + '</span>' : '') +
                        '</div>' +
                      '</td>' +

                      '<!-- EP Rates -->' +
                      '<td class="col-rate">' + fmt(room.rates.ep.mrp) + '</td>' +
                      '<td class="col-rate highlight-ep">' + fmt(room.rates.ep.b2c) + '</td>' +
                      '<td class="col-rate col-divider">' + fmt(room.rates.ep.b2b) + '</td>' +

                      '<!-- CP Rates -->' +
                      '<td class="col-rate">' + fmt(room.rates.cp.mrp) + '</td>' +
                      '<td class="col-rate highlight-cp">' + fmt(room.rates.cp.b2c) + '</td>' +
                      '<td class="col-rate col-divider">' + fmt(room.rates.cp.b2b) + '</td>' +

                      '<!-- MAP Rates -->' +
                      '<td class="col-rate">' + fmt(room.rates.map.mrp) + '</td>' +
                      '<td class="col-rate highlight-map">' + fmt(room.rates.map.b2c) + '</td>' +
                      '<td class="col-rate col-divider">' + fmt(room.rates.map.b2b) + '</td>' +

                      '<!-- AP Rates -->' +
                      '<td class="col-rate">' + fmt(room.rates.ap.mrp) + '</td>' +
                      '<td class="col-rate highlight-ap">' + fmt(room.rates.ap.b2c) + '</td>' +
                      '<td class="col-rate col-divider">' + fmt(room.rates.ap.b2b) + '</td>' +

                      '<!-- Actions -->' +
                      '<td class="col-actions" style="text-align: center; white-space: nowrap;">' +
                        '<div class="table-actions-group" style="display: inline-flex; gap: 6px; justify-content: center;">' +
                          '<button type="button" class="btn-action-icon edit btn-edit-room" data-room-id="' + room.id + '" data-tooltip="Edit Room & Rates" title="Edit Room & Rates">' +
                            '<i class="fi fi-rr-edit"></i>' +
                          '</button>' +
                          '<button type="button" class="btn-action-icon delete btn-delete-room" data-room-id="' + room.id + '" data-tooltip="Delete Room" title="Delete Room">' +
                            '<i class="fi fi-rr-trash"></i>' +
                          '</button>' +
                        '</div>' +
                      '</td>' +
                    '</tr>';
      });

      var tableHtml = '<div class="package-table-card">' +
                        '<div class="v3-table-header-bar">' +
                          '<div class="table-header-title">' +
                            '<i class="fi fi-rr-layers"></i> Configured Pricing Matrix & Seasonal Slabs' +
                          '</div>' +
                          '<div class="table-header-counter">' +
                            count + (count === 1 ? ' price slab' : ' price slabs') +
                          '</div>' +
                        '</div>' +
                        '<div class="v3-table-responsive-wrapper" style="overflow-x: auto;">' +
                          '<table class="showTableList prop-rooms-matrix-table">' +
                            '<thead>' +
                              '<tr>' +
                                '<th rowspan="2" class="th-main" style="min-width: 220px;">Room Type &amp; Duration</th>' +
                                '<th colspan="3" class="th-plan-ep">EP Pricing (INR)</th>' +
                                '<th colspan="3" class="th-plan-cp">CP Pricing (INR)</th>' +
                                '<th colspan="3" class="th-plan-map">MAP Pricing (INR)</th>' +
                                '<th colspan="3" class="th-plan-ap">AP Pricing (INR)</th>' +
                                '<th rowspan="2" class="th-main col-center" style="width: 80px;">Actions</th>' +
                              '</tr>' +
                              '<tr>' +
                                '<th class="th-sub">MRP</th>' +
                                '<th class="th-sub selling">Selling</th>' +
                                '<th class="th-sub col-divider">Agent</th>' +
                                '<th class="th-sub">MRP</th>' +
                                '<th class="th-sub selling">Selling</th>' +
                                '<th class="th-sub col-divider">Agent</th>' +
                                '<th class="th-sub">MRP</th>' +
                                '<th class="th-sub selling">Selling</th>' +
                                '<th class="th-sub col-divider">Agent</th>' +
                                '<th class="th-sub">MRP</th>' +
                                '<th class="th-sub selling">Selling</th>' +
                                '<th class="th-sub col-divider">Agent</th>' +
                              '</tr>' +
                            '</thead>' +
                            '<tbody>' + rowsHtml + '</tbody>' +
                          '</table>' +
                        '</div>' +
                      '</div>';

      $list.html(tableHtml);
    },

    _parseDayMonth: function (val) {
      if (!val) return { day: '', month: '' };
      var s = $.trim(val);
      var parts = s.split(/[\s-]+/);
      if (parts.length >= 2) {
        var p1 = parts[0], p2 = parts[1];
        var day = parseInt(p1, 10);
        var dayStr = (!isNaN(day) && day >= 1 && day <= 31) ? (day < 10 ? '0' + day : '' + day) : '';
        var monthNames = {
          '01':'Jan','02':'Feb','03':'Mar','04':'Apr','05':'May','06':'Jun',
          '07':'Jul','08':'Aug','09':'Sep','10':'Oct','11':'Nov','12':'Dec',
          '1':'Jan','2':'Feb','3':'Mar','4':'Apr','5':'May','6':'Jun',
          '7':'Jul','8':'Aug','9':'Sep'
        };
        var monthStr = monthNames[p2] || p2;
        return { day: dayStr, month: monthStr };
      }
      return { day: '', month: '' };
    },

    dmState: {
      activeInput: null,
      currentMonth: 0,
      selectedDay: null,
      selectedMonth: null,
      showingMonthsGrid: false,
      monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
      monthShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      monthDays: [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
    },

    _openDayMonthPopover: function ($input) {
      this.dmState.activeInput = $input;
      var rawVal = $input.val() || '';
      var parsed = this._parseDayMonth(rawVal);

      if (parsed.month) {
        var mIdx = this.dmState.monthShort.findIndex(function (m) {
          return m.toLowerCase() === parsed.month.toLowerCase();
        });
        if (mIdx === -1) {
          mIdx = this.dmState.monthNames.findIndex(function (m) {
            return m.toLowerCase().startsWith(parsed.month.toLowerCase());
          });
        }
        if (mIdx !== -1) {
          this.dmState.currentMonth = mIdx;
          this.dmState.selectedMonth = mIdx;
        }
      } else {
        this.dmState.currentMonth = new Date().getMonth();
        this.dmState.selectedMonth = null;
      }

      this.dmState.selectedDay = parsed.day ? parseInt(parsed.day, 10) : null;
      this.dmState.showingMonthsGrid = false;
      $('#propDmMonthsGrid').hide();
      $('#propDmCalendarView').show();

      this._renderDayMonthCalendar();

      // Position popover relative to $input
      var offset = $input.offset();
      var popoverWidth = 270;
      var top = offset.top + $input.outerHeight() + 6;
      var left = offset.left;

      if (left + popoverWidth > $(window).width() - 10) {
        left = $(window).width() - popoverWidth - 10;
      }

      $('#propDayMonthPopover').css({ top: top + 'px', left: left + 'px' }).fadeIn(140);
    },

    _renderDayMonthCalendar: function () {
      var m = this.dmState.currentMonth;
      var mName = this.dmState.monthNames[m];
      $('#propDmMonthLabel').text(mName);

      // Render Months selector grid
      var monthsHtml = '';
      for (var i = 0; i < 12; i++) {
        var isAct = (i === m) ? 'active' : '';
        monthsHtml += '<div class="prop-dm-month-cell ' + isAct + '" data-month="' + i + '">' + this.dmState.monthShort[i] + '</div>';
      }
      $('#propDmMonthsGrid').html(monthsHtml);

      // Render Days grid
      var totalDays = this.dmState.monthDays[m];
      var firstDayOfWeek = new Date(2026, m, 1).getDay();

      var daysHtml = '';
      for (var b = 0; b < firstDayOfWeek; b++) {
        daysHtml += '<div class="prop-dm-day-cell empty"></div>';
      }

      for (var d = 1; d <= totalDays; d++) {
        var isSelected = (this.dmState.selectedMonth === m && this.dmState.selectedDay === d);
        var cls = isSelected ? 'active' : '';
        daysHtml += '<div class="prop-dm-day-cell ' + cls + '" data-day="' + d + '">' + d + '</div>';
      }

      $('#propDmDaysGrid').html(daysHtml);
    },

    _selectDayMonthDate: function (day) {
      var m = this.dmState.currentMonth;
      this.dmState.selectedDay = day;
      this.dmState.selectedMonth = m;
      var dStr = (day < 10 ? '0' + day : '' + day);
      var mStr = this.dmState.monthShort[m];
      var formatted = dStr + ' ' + mStr;

      if (this.dmState.activeInput) {
        this.dmState.activeInput.val(formatted);
        var targetHidden = this.dmState.activeInput.attr('data-target');
        if (targetHidden) {
          $(targetHidden).val(formatted);
        }
      }
      $('#propDayMonthPopover').fadeOut(120);
    },

    inheritedFeatureImage: null,

    _syncRoomTypePhotos: function (selectedType) {
      if (!selectedType) {
        $('#roomPhotoInheritNotice').hide();
        return;
      }
      var self = this;
      var selSlug = selectedType.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');

      // Find any existing room under this property that has photos for this room type
      var matchingRoom = this.propertyRooms.find(function (r) {
        if (self.editingRoomId && r.id === self.editingRoomId) return false;
        var rSlug = (r.type || '').toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
        return (rSlug === selSlug || r.type === selectedType) && (r.feature_image || (Array.isArray(r.gallery) && r.gallery.length > 0));
      });

      if (matchingRoom) {
        // If current modal has no gallery yet, inherit from matching room
        if (!this.roomExistingGallery || this.roomExistingGallery.length === 0) {
          if (Array.isArray(matchingRoom.gallery) && matchingRoom.gallery.length > 0) {
            this.roomExistingGallery = JSON.parse(JSON.stringify(matchingRoom.gallery));
            this._renderRoomModalGallery();
          }
        }
        // If current modal has no custom cover image yet, inherit
        if (matchingRoom.feature_image) {
          this.inheritedFeatureImage = matchingRoom.feature_image;
          var curSub = $('#roomFeatureImgSub').text();
          if (!curSub || curSub === 'JPG, PNG or WebP' || curSub.indexOf('Inherited') !== -1) {
            $('#roomFeatureImgSub').text('Inherited cover: ' + matchingRoom.feature_image.split('/').pop());
          }
        }

        var label = matchingRoom.type_label || matchingRoom.type || selectedType;
        $('#inheritedRoomTypeName').text(label);
        $('#roomPhotoInheritNotice').fadeIn(150);
      } else {
        if (!this.editingRoomId) {
          this.inheritedFeatureImage = null;
        }
        $('#roomPhotoInheritNotice').hide();
      }
    },

    _openRoomModal: function (roomId) {
      this.editingRoomId = roomId || 0;

      $('#propRoomForm')[0].reset();
      var currentPropId = this.propertyId || this.encodedId || $('#propertyUpdateApp').attr('data-id') || $('#propertyUpdateApp').attr('data-raw-id') || '';
      $('#roomModalPropertyId').val(currentPropId);
      $('#roomModalId').val(this.editingRoomId);

      if (this.editingRoomId > 0) {
        $('#roomModalTitle').text('Edit Room Pricing Slab');
        $('#btnSaveRoomText').text('Update Pricing Slab');
        var room = this.propertyRooms.find(function (r) { return r.id === roomId; });
        if (room) {
          var targetType = (room.type || '').trim();
          var targetLabel = (room.type_label || targetType).trim();
          var targetSlug = targetType.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');

          var $typeSelect = $('#roomModalType');
          var matchedVal = null;

          $typeSelect.find('option').each(function () {
            var optVal = $(this).val();
            var optText = $(this).text();
            if (!optVal) return;

            var optValSlug = optVal.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            var optTextSlug = optText.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');

            if (optVal === targetType || 
                optValSlug === targetSlug || 
                optText.toLowerCase() === targetLabel.toLowerCase() ||
                optTextSlug === targetSlug) {
              matchedVal = optVal;
              return false;
            }
          });

          if (matchedVal) {
            $typeSelect.val(matchedVal);
          } else if (targetType) {
            var newOpt = new Option(targetLabel || targetType, targetType, true, true);
            $typeSelect.append(newOpt).val(targetType);
          }

          if ($.fn.select2) {
            if (!$typeSelect.data('select2')) {
              $typeSelect.select2({ dropdownParent: $('#propRoomModal'), width: '100%' });
            }
            $typeSelect.trigger('change.select2');
          }
          $('#roomModalDesc').val(room.description || '');

          // Season & Price Duration (Day & Month Calendar)
          $('#roomPriceFromDisplay').val(room.price_from || '');
          $('#roomPriceToDisplay').val(room.price_to || '');
          $('#roomPriceFrom').val(room.price_from || '');
          $('#roomPriceTo').val(room.price_to || '');
          $('#roomSeasonName').val(room.season_name || '');

          // Rates
          $('#roomEpMrp').val(room.rates.ep.mrp || '');
          $('#roomEpB2c').val(room.rates.ep.b2c || '');
          $('#roomEpB2b').val(room.rates.ep.b2b || '');

          $('#roomCpMrp').val(room.rates.cp.mrp || '');
          $('#roomCpB2c').val(room.rates.cp.b2c || '');
          $('#roomCpB2b').val(room.rates.cp.b2b || '');

          $('#roomMapMrp').val(room.rates.map.mrp || '');
          $('#roomMapB2c').val(room.rates.map.b2c || '');
          $('#roomMapB2b').val(room.rates.map.b2b || '');

          $('#roomApMrp').val(room.rates.ap.mrp || '');
          $('#roomApB2c').val(room.rates.ap.b2c || '');
          $('#roomApB2b').val(room.rates.ap.b2b || '');
        }
      } else {
        $('#roomModalTitle').text('Add Room Pricing Slab');
        $('#btnSaveRoomText').text('Save Pricing Slab');
        $('#roomModalType').val('');
        if ($.fn.select2) {
          if (!$('#roomModalType').data('select2')) {
            $('#roomModalType').select2({ dropdownParent: $('#propRoomModal'), width: '100%' });
          }
          $('#roomModalType').trigger('change.select2');
        }
        $('#roomPriceFromDisplay, #roomPriceToDisplay, #roomPriceFrom, #roomPriceTo, #roomSeasonName').val('');
        $('#roomModalDesc').val('');
      }

      $('#propRoomModal').fadeIn(200).css('display', 'flex');
    },

    _closeRoomModal: function () {
      $('#propRoomModal').fadeOut(180);
      this.editingRoomId = 0;
    },

    /* ── Master Room Galleries Manager ────────────────────────── */
    masterGalleryState: {
      activeType: '',
      coverImage: '',
      stagedCoverFile: null,
      existingGallery: [],
      stagedGalleryFiles: [],
    },

    _openRoomGalleryModal: function (preferredType) {
      var self = this;

      if (!self.masterRoomTypes || !self.masterRoomTypes.length) {
        self._loadMasterRoomTypes(function () {
          self._openRoomGalleryModal(preferredType);
        });
        return;
      }

      var $select = $('#roomGalleryTypeSelect');
      $select.empty();

      var typeMap = {};

      // 1. From loaded master room types API
      if (Array.isArray(self.masterRoomTypes)) {
        self.masterRoomTypes.forEach(function (rt) {
          if (rt && rt.title) {
            var val = (rt.val || rt.slug || rt.title).toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            typeMap[val] = rt.title;
          }
        });
      }

      // 2. Standard fallback room types
      var defaultTypes = {
        deluxe_room: 'Deluxe Room',
        super_deluxe: 'Super Deluxe Room',
        executive_room: 'Executive Room',
        suite: 'Suite',
        standard_room: 'Standard Room',
        family_suite: 'Family Suite',
        luxury_room: 'Luxury Room',
        presidential_suite: 'Presidential Suite'
      };
      Object.keys(defaultTypes).forEach(function (k) {
        if (!typeMap[k]) typeMap[k] = defaultTypes[k];
      });

      // 3. Add types from existing property rooms
      if (Array.isArray(self.propertyRooms)) {
        self.propertyRooms.forEach(function (r) {
          if (r.type && !typeMap[r.type]) {
            typeMap[r.type] = r.type_label || r.type.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
          }
        });
      }

      Object.keys(typeMap).forEach(function (k) {
        $select.append(new Option(typeMap[k], k));
      });

      var initialType = preferredType || (self.propertyRooms.length > 0 ? self.propertyRooms[0].type : Object.keys(typeMap)[0] || 'deluxe_room');

      if ($.fn.select2) {
        if (!$select.data('select2')) {
          $select.select2({ dropdownParent: $('#propRoomGalleryModal'), width: '100%' });
        }
        $select.val(initialType).trigger('change.select2');
      } else {
        $select.val(initialType);
      }

      self.masterGalleryState.activeType = initialType;
      self.masterGalleryState.stagedCoverFile = null;
      self.masterGalleryState.stagedGalleryFiles = [];

      self._loadMasterGalleryData(initialType);
      $('#propRoomGalleryModal').fadeIn(200).css('display', 'flex');
    },

    _loadMasterGalleryData: function (roomType) {
      var self = this;
      self.masterGalleryState.activeType = roomType;
      self.masterGalleryState.stagedCoverFile = null;
      self.masterGalleryState.stagedGalleryFiles = [];

      // Find from propertyRooms local cache first
      var matching = self.propertyRooms.filter(function (r) {
        return (r.type || '').toLowerCase() === (roomType || '').toLowerCase();
      });

      var existingCover = '';
      var existingGal = [];

      for (var i = 0; i < matching.length; i++) {
        if (!existingCover && matching[i].feature_image) {
          existingCover = matching[i].feature_image;
        }
        if (existingGal.length === 0 && matching[i].gallery && matching[i].gallery.length) {
          existingGal = matching[i].gallery.slice();
        }
      }

      self.masterGalleryState.coverImage = existingCover;
      self.masterGalleryState.existingGallery = existingGal;
      self._renderMasterGalleryUI();
    },

    _renderMasterGalleryUI: function () {
      var self = this;
      var state = self.masterGalleryState;

      // Render Cover
      var coverSrc = state.stagedCoverFile ? state.stagedCoverFile.previewUrl : state.coverImage;
      if (coverSrc) {
        $('#masterCoverImgPreview').attr('src', coverSrc);
        $('#masterCoverName').text(state.stagedCoverFile ? state.stagedCoverFile.name : coverSrc.split('/').pop());
        $('#masterCoverPreviewWrap').show();
        $('#masterCoverDropzone').hide();
      } else {
        $('#masterCoverPreviewWrap').hide();
        $('#masterCoverDropzone').show();
      }

      // Render Gallery
      var $grid = $('#masterGalleryGrid');
      var html = '';

      state.existingGallery.forEach(function (item, index) {
        var src = item.path || item.src || (typeof item === 'string' ? item : '');
        var name = item.name || ('Photo ' + (index + 1));
        html += '<div class="prop-gallery-item">' +
                  '<img src="' + escHTML(src) + '" alt="' + escHTML(name) + '">' +
                  '<button type="button" class="prop-gallery-remove-btn btn-del-master-gal" data-index="' + index + '" title="Remove Photo"><i class="fi fi-rr-cross"></i></button>' +
                '</div>';
      });

      state.stagedGalleryFiles.forEach(function (file, index) {
        html += '<div class="prop-gallery-item">' +
                  '<img src="' + escHTML(file.previewUrl) + '" alt="' + escHTML(file.name) + '">' +
                  '<span class="prop-gallery-new-tag">New</span>' +
                  '<button type="button" class="prop-gallery-remove-btn btn-del-master-staged" data-staged-idx="' + index + '" title="Remove Photo"><i class="fi fi-rr-cross"></i></button>' +
                '</div>';
      });

      if (html) {
        $grid.html(html).show();
      } else {
        $grid.html('').hide();
      }
    },

    _closeRoomGalleryModal: function () {
      $('#propRoomGalleryModal').fadeOut(180);
      this.masterGalleryState.stagedCoverFile = null;
      this.masterGalleryState.stagedGalleryFiles = [];
    },

    /* ── Hotel Gallery Section (Card 5) ────────────────────────── */
    _renderExistingGallery: function () {
      var $grid = $('#propExistingGalleryGrid');
      var $empty = $('#propGalleryEmptyState');
      var $badge = $('#propGalleryCountBadge');
      var count = this.existingGallery.length;

      $badge.text(count + (count === 1 ? ' Photo' : ' Photos'));
      $('#propKeptGallery').val(JSON.stringify(this.existingGallery));

      if (count === 0) {
        $grid.html('').hide();
        if (this.stagedGalleryFiles.length === 0) $empty.show();
        else $empty.hide();
        return;
      }

      $empty.hide();
      var html = '';
      this.existingGallery.forEach(function (item, index) {
        var src = item.path || item.src || (typeof item === 'string' ? item : '');
        var name = item.name || ('Photo ' + (index + 1));
        html += '<div class="prop-gallery-item" data-index="' + index + '">' +
                  '<img src="' + escHTML(src) + '" alt="' + escHTML(name) + '">' +
                  '<button type="button" class="prop-gallery-remove-btn btn-remove-existing-photo" data-index="' + index + '" title="Remove this photo">' +
                    '<i class="fi fi-rr-cross"></i>' +
                  '</button>' +
                '</div>';
      });
      $grid.html(html).show();
    },

    _renderStagedGallery: function () {
      var $grid = $('#propStagedGalleryGrid');
      var $title = $('#propGalleryFileTitle');
      var $subtitle = $('#propGalleryFileSubtitle');
      var count = this.stagedGalleryFiles.length;

      if (count === 0) {
        $grid.html('').hide();
        $title.text('Choose images or drag & drop multiple files');
        $subtitle.text('Select multiple JPG, PNG, GIF or WebP photos');
        if (this.existingGallery.length === 0) $('#propGalleryEmptyState').show();
        return;
      }

      $('#propGalleryEmptyState').hide();
      $title.text(count + ' new ' + (count === 1 ? 'photo' : 'photos') + ' selected');
      $subtitle.text('Ready to upload when you save');

      var html = '';
      this.stagedGalleryFiles.forEach(function (file, index) {
        var src = file.previewUrl || '';
        html += '<div class="prop-gallery-item" data-staged-index="' + index + '">' +
                  '<img src="' + escHTML(src) + '" alt="' + escHTML(file.name) + '">' +
                  '<span class="prop-gallery-new-tag">New</span>' +
                  '<button type="button" class="prop-gallery-remove-btn btn-remove-staged-photo" data-staged-index="' + index + '" title="Remove from upload">' +
                    '<i class="fi fi-rr-cross"></i>' +
                  '</button>' +
                '</div>';
      });
      $grid.html(html).show();
    },

    /* ── Select2 Initialisation ──────────────────────────────────── */
    _initSelect2: function () {
      if (!$.fn.select2) return;

      $('#propType').select2({
        width: '100%',
        placeholder: '— Select property type —',
        allowClear: true,
        minimumResultsForSearch: 8,
        dropdownAutoWidth: false,
      });

      $('#roomModalType').select2({
        width: '100%',
        placeholder: '— Select Room Type from Master Library —',
        allowClear: true,
        dropdownParent: $('#propRoomModal'),
      });

      $('#roomGalleryTypeSelect').select2({
        width: '100%',
        placeholder: '— Select Room Category —',
        allowClear: false,
        dropdownParent: $('#propRoomGalleryModal'),
      });

      $('#propCity').select2({
        width:            '100%',
        allowClear:       true,
        placeholder:      'Search city...',
        dropdownAutoWidth: false,
        ajax: {
          url:      '/api/v3/cities/',
          dataType: 'json',
          delay:    200,
          data: function (params) { return { search: params.term || '' }; },
          processResults: function (res) {
            return {
              results: (res.results || []).map(function (item) {
                return {
                  id:   item.city || item.id,
                  text: item.city + (item.state ? ', ' + item.state : ''),
                };
              }),
            };
          },
          cache: true,
        },
      });
    },

    /* ── Events ────────────────────────────────────────────────── */
    _bindEvents: function () {
      var self = this;

      /* Segmented Navigation Tabs */
      $(document)
        .off('click.propTab', '.prop-tab-btn, .btn-jump-tab')
        .on('click.propTab', '.prop-tab-btn, .btn-jump-tab', function (e) {
          e.preventDefault();
          var target = $(this).attr('data-target') || $(this).attr('data-tab');
          if (!target) return;

          $('.prop-tab-btn').removeClass('active');
          $('.prop-tab-btn[data-target="' + target + '"]').addClass('active');

          $('.prop-tab-pane').removeClass('active');
          $(target).addClass('active');
          $('html, body').animate({ scrollTop: $('#propertyUpdateApp').offset().top - 20 }, 250);
        });

      /* Live preview update on any input or select2 change */
      $(document)
        .off('input.propUpdate change.propUpdate select2:select.propUpdate select2:clear.propUpdate change.select2.propUpdate', '#propertyUpdateApp')
        .on('input.propUpdate change.propUpdate select2:select.propUpdate select2:clear.propUpdate change.select2.propUpdate', '#propertyUpdateApp input, #propertyUpdateApp textarea, #propertyUpdateApp select', function () {
          self._updateLivePreview();
          self._updateProgress();
        });

      /* Open Native Datepicker on input or icon wrap click */
      $(document)
        .off('click.openDatePicker', 'input[type="date"], .v3-input-icon-wrap:has(input[type="date"])')
        .on('click.openDatePicker', 'input[type="date"], .v3-input-icon-wrap:has(input[type="date"])', function (e) {
          var input = $(this).is('input[type="date"]') ? this : $(this).find('input[type="date"]')[0];
          if (input && typeof input.showPicker === 'function') {
            try {
              input.showPicker();
            } catch (err) {
              // fallback
            }
          }
        });

      /* Clear select2 validation error */
      $('#propType').on('select2:select.propErr select2:clear.propErr change.select2.propErr', function () {
        if ($(this).val()) $('#errPropType').text('');
      });
      $('#propCity').on('select2:select.propErr select2:clear.propErr change.select2.propErr', function () {
        if ($(this).val()) $('#errPropCity').text('');
      });

      /* Publish to Website Checkbox Change */
      $(document)
        .off('change.propPublish', '#chkPublishToWebsite')
        .on('change.propPublish', '#chkPublishToWebsite', function () {
          var isChecked = $(this).is(':checked');
          if (isChecked && self.data && self.data.live_url) {
            $('#btnViewLiveHotel').fadeIn(200);
            $('#propLiveWebsiteBox').fadeIn(200);
          } else {
            $('#btnViewLiveHotel').fadeOut(200);
            $('#propLiveWebsiteBox').fadeOut(200);
          }
        });

      /* ── Amenities Interactive Events ────────────────────────── */
      // Click chip to toggle
      $(document)
        .off('click.amenityChip', '.prop-amenity-chip')
        .on('click.amenityChip', '.prop-amenity-chip', function (e) {
          e.preventDefault();
          var id = parseInt($(this).attr('data-id'), 10);
          var title = $(this).attr('data-title');

          var idx = self.selectedAmenities.findIndex(function (x) {
            return x === id || x === title || (x && x.id === id) || (x && x.title === title);
          });

          if (idx >= 0) {
            self.selectedAmenities.splice(idx, 1);
          } else {
            self.selectedAmenities.push({ id: id, title: title });
          }
          self._syncSelectedAmenitiesUI();
        });

      // Search amenities
      $(document)
        .off('input.amenitySearch', '#propAmenitySearch')
        .on('input.amenitySearch', '#propAmenitySearch', function () {
          self._applyAmenityFilters();
        });

      // Change Category Filter Dropdown (Select2)
      $(document)
        .off('change.propAmenityCatSelect', '#propAmenityCatSelect')
        .on('change.propAmenityCatSelect', '#propAmenityCatSelect', function () {
          self.activeAmenityCategory = $(this).val() || 'all';
          self._applyAmenityFilters();
        });

      // Select All in category
      $(document)
        .off('click.selCatAll', '.btn-select-cat-all')
        .on('click.selCatAll', '.btn-select-cat-all', function (e) {
          e.preventDefault();
          var cat = $(this).attr('data-cat');
          $('.prop-amenity-chip[data-cat="' + cat + '"]').each(function () {
            var id = parseInt($(this).attr('data-id'), 10);
            var title = $(this).attr('data-title');
            var exists = self.selectedAmenities.some(function (x) {
              return x === id || x === title || (x && x.id === id) || (x && x.title === title);
            });
            if (!exists) {
              self.selectedAmenities.push({ id: id, title: title });
            }
          });
          self._syncSelectedAmenitiesUI();
        });

      // Clear All in category
      $(document)
        .off('click.clearCatAll', '.btn-clear-cat-all')
        .on('click.clearCatAll', '.btn-clear-cat-all', function (e) {
          e.preventDefault();
          var cat = $(this).attr('data-cat');
          $('.prop-amenity-chip[data-cat="' + cat + '"]').each(function () {
            var id = parseInt($(this).attr('data-id'), 10);
            var title = $(this).attr('data-title');
            self.selectedAmenities = self.selectedAmenities.filter(function (x) {
              return !(x === id || x === title || (x && x.id === id) || (x && x.title === title));
            });
          });
          self._syncSelectedAmenitiesUI();
        });

      /* ── Month-Day Calendar Popover Events (No Year) ─────────── */
      // Click input or wrapper to open popover
      $(document)
        .off('click.openDmPicker', '.prop-dm-picker-input, .prop-dm-picker-input-wrap')
        .on('click.openDmPicker', '.prop-dm-picker-input, .prop-dm-picker-input-wrap', function (e) {
          e.stopPropagation();
          var $input = $(this).is('.prop-dm-picker-input') ? $(this) : $(this).find('.prop-dm-picker-input');
          self._openDayMonthPopover($input);
        });

      // Prev Month
      $(document)
        .off('click.dmPrevMonth', '#btnPropDmPrevMonth')
        .on('click.dmPrevMonth', '#btnPropDmPrevMonth', function (e) {
          e.stopPropagation();
          self.dmState.currentMonth = (self.dmState.currentMonth + 11) % 12;
          self._renderDayMonthCalendar();
        });

      // Next Month
      $(document)
        .off('click.dmNextMonth', '#btnPropDmNextMonth')
        .on('click.dmNextMonth', '#btnPropDmNextMonth', function (e) {
          e.stopPropagation();
          self.dmState.currentMonth = (self.dmState.currentMonth + 1) % 12;
          self._renderDayMonthCalendar();
        });

      // Toggle Months Grid vs Days Grid
      $(document)
        .off('click.dmToggleMonth', '#btnPropDmToggleMonth')
        .on('click.dmToggleMonth', '#btnPropDmToggleMonth', function (e) {
          e.stopPropagation();
          self.dmState.showingMonthsGrid = !self.dmState.showingMonthsGrid;
          if (self.dmState.showingMonthsGrid) {
            $('#propDmCalendarView').hide();
            $('#propDmMonthsGrid').show();
          } else {
            $('#propDmMonthsGrid').hide();
            $('#propDmCalendarView').show();
          }
        });

      // Select Month from Grid
      $(document)
        .off('click.dmSelectMonth', '.prop-dm-month-cell')
        .on('click.dmSelectMonth', '.prop-dm-month-cell', function (e) {
          e.stopPropagation();
          var m = parseInt($(this).attr('data-month'), 10);
          self.dmState.currentMonth = m;
          self.dmState.showingMonthsGrid = false;
          $('#propDmMonthsGrid').hide();
          $('#propDmCalendarView').show();
          self._renderDayMonthCalendar();
        });

      // Select Day
      $(document)
        .off('click.dmSelectDay', '.prop-dm-day-cell:not(.empty)')
        .on('click.dmSelectDay', '.prop-dm-day-cell:not(.empty)', function (e) {
          e.stopPropagation();
          var d = parseInt($(this).attr('data-day'), 10);
          if (d) self._selectDayMonthDate(d);
        });

      // Clear Date
      $(document)
        .off('click.dmClear', '#btnPropDmClear')
        .on('click.dmClear', '#btnPropDmClear', function (e) {
          e.stopPropagation();
          if (self.dmState.activeInput) {
            self.dmState.activeInput.val('');
            var targetHidden = self.dmState.activeInput.attr('data-target');
            if (targetHidden) $(targetHidden).val('');
          }
          $('#propDayMonthPopover').fadeOut(120);
        });

      // Close Popover
      $(document)
        .off('click.dmClose', '#btnPropDmClose')
        .on('click.dmClose', '#btnPropDmClose', function (e) {
          e.stopPropagation();
          $('#propDayMonthPopover').fadeOut(120);
        });

      // Close popover when clicking anywhere else
      $(document)
        .off('click.dmOutside')
        .on('click.dmOutside', function (e) {
          if (!$(e.target).closest('#propDayMonthPopover, .prop-dm-picker-input-wrap').length) {
            $('#propDayMonthPopover').fadeOut(120);
          }
        });

      /* ── Rooms & Rate Plans Pricing Slabs Events ─────────────── */
      $(document)
        .off('click.openAddRoom', '#btnOpenAddRoomModal, #btnEmptyAddRoom')
        .on('click.openAddRoom', '#btnOpenAddRoomModal, #btnEmptyAddRoom', function (e) {
          e.preventDefault();
          self._openRoomModal(0);
        });

      $(document)
        .off('click.editRoom', '.btn-edit-room')
        .on('click.editRoom', '.btn-edit-room', function (e) {
          e.preventDefault();
          var rId = parseInt($(this).attr('data-room-id'), 10);
          self._openRoomModal(rId);
        });

      $(document)
        .off('click.closeRoomModal', '#btnCloseRoomModal, #btnCancelRoomModal')
        .on('click.closeRoomModal', '#btnCloseRoomModal, #btnCancelRoomModal', function (e) {
          e.preventDefault();
          self._closeRoomModal();
        });

      $(document)
        .off('click.closeRoomBackdrop', '#propRoomModal')
        .on('click.closeRoomBackdrop', '#propRoomModal', function (e) {
          if (e.target === this) self._closeRoomModal();
        });

      // Delete room pricing slab
      $(document)
        .off('click.delRoom', '.btn-delete-room')
        .on('click.delRoom', '.btn-delete-room', function (e) {
          e.preventDefault();
          var rId = parseInt($(this).attr('data-room-id'), 10);
          if (!rId) return;
          if (!confirm('Are you sure you want to delete this room pricing slab? This cannot be undone.')) return;

          $.ajax({
            url:      '/api/v3/properties/rooms/delete/',
            method:   'POST',
            data:     { room_id: rId, property_id: self.propertyId },
            dataType: 'json',
            success: function (res) {
              if (res.status === 'success') {
                self._toast('Room pricing slab deleted successfully.', 'success');
                self._loadPropertyRooms();
              } else {
                self._toast(res.msg || 'Failed to delete pricing slab.', 'error');
              }
            },
          });
        });

      // Save Room Pricing Slab Submit
      $(document)
        .off('submit.roomForm', '#propRoomForm')
        .on('submit.roomForm', '#propRoomForm', function (e) {
          e.preventDefault();
          var $btn = $('#btnSaveRoom');
          var $btnText = $('#btnSaveRoomText');
          $btn.prop('disabled', true);
          $btnText.text('Saving...');

          var formEl = document.getElementById('propRoomForm');
          var formData = new FormData(formEl);

          var currentPropId = self.propertyId || self.encodedId || $('#propertyUpdateApp').attr('data-id') || $('#propertyUpdateApp').attr('data-raw-id') || $('#roomModalPropertyId').val() || '';
          formData.set('property_id', currentPropId);
          formData.set('room_id', self.editingRoomId || $('#roomModalId').val() || '0');

          $.ajax({
            url:         '/api/v3/properties/rooms/',
            method:      'POST',
            data:        formData,
            processData: false,
            contentType: false,
            dataType:    'json',
            success: function (res) {
              $btn.prop('disabled', false);
              $btnText.text('Save Pricing Slab');
              if (res.status === 'success') {
                self._toast(res.msg || 'Room pricing slab saved successfully!', 'success');
                self._closeRoomModal();
                self._loadPropertyRooms();
              } else {
                self._toast(res.msg || 'Failed to save room pricing slab.', 'error');
              }
            },
            error: function (xhr) {
              $btn.prop('disabled', false);
              $btnText.text('Save Pricing Slab');
              var msg = 'Failed to save room pricing slab.';
              try {
                var r = JSON.parse(xhr.responseText);
                if (r && r.msg) msg = r.msg;
              } catch (e) {}
              self._toast(msg, 'error');
            },
          });
        });

      /* ── Master Room Galleries Manager Events ────────────────── */
      $(document)
        .off('click.openMasterGal', '#btnOpenRoomGalleryModal')
        .on('click.openMasterGal', '#btnOpenRoomGalleryModal', function (e) {
          e.preventDefault();
          self._openRoomGalleryModal();
        });

      $(document)
        .off('click.closeMasterGal', '#btnCloseRoomGalleryModal, #btnCancelRoomGalleryModal')
        .on('click.closeMasterGal', '#btnCloseRoomGalleryModal, #btnCancelRoomGalleryModal', function (e) {
          e.preventDefault();
          self._closeRoomGalleryModal();
        });

      $(document)
        .off('click.closeMasterGalBackdrop', '#propRoomGalleryModal')
        .on('click.closeMasterGalBackdrop', '#propRoomGalleryModal', function (e) {
          if (e.target === this) self._closeRoomGalleryModal();
        });

      // Switch Room Category in Master Gallery (Select2 support)
      $(document)
        .off('change.masterTypeSelect select2:select.masterTypeSelect', '#roomGalleryTypeSelect')
        .on('change.masterTypeSelect select2:select.masterTypeSelect', '#roomGalleryTypeSelect', function () {
          var val = $(this).val();
          if (val) self._loadMasterGalleryData(val);
        });

      // Master Cover Image selection
      $(document)
        .off('change.masterCoverInput', '#masterCoverFileInput')
        .on('change.masterCoverInput', '#masterCoverFileInput', function () {
          var file = this.files && this.files[0];
          if (!file) return;
          var reader = new FileReader();
          reader.onload = function (e) {
            file.previewUrl = e.target.result;
            self.masterGalleryState.stagedCoverFile = file;
            self._renderMasterGalleryUI();
          };
          reader.readAsDataURL(file);
        });

      // Change Master Cover button
      $(document)
        .off('click.changeMasterCover', '#btnChangeMasterCover')
        .on('click.changeMasterCover', '#btnChangeMasterCover', function (e) {
          e.preventDefault();
          $('#masterCoverFileInput').trigger('click');
        });

      // Remove Master Cover button
      $(document)
        .off('click.removeMasterCover', '#btnRemoveMasterCover')
        .on('click.removeMasterCover', '#btnRemoveMasterCover', function (e) {
          e.preventDefault();
          self.masterGalleryState.coverImage = '';
          self.masterGalleryState.stagedCoverFile = null;
          $('#masterCoverFileInput').val('');
          self._renderMasterGalleryUI();
        });

      // Master Gallery Multi-photo selection
      $(document)
        .off('change.masterGalInput', '#masterGalleryFileInput')
        .on('change.masterGalInput', '#masterGalleryFileInput', function () {
          var files = this.files;
          if (!files || !files.length) return;
          for (var i = 0; i < files.length; i++) {
            (function (file) {
              var reader = new FileReader();
              reader.onload = function (e) {
                file.previewUrl = e.target.result;
                self.masterGalleryState.stagedGalleryFiles.push(file);
                self._renderMasterGalleryUI();
              };
              reader.readAsDataURL(file);
            })(files[i]);
          }
        });

      // Delete existing photo in Master Gallery
      $(document)
        .off('click.delMasterGal', '.btn-del-master-gal')
        .on('click.delMasterGal', '.btn-del-master-gal', function (e) {
          e.preventDefault();
          var idx = parseInt($(this).attr('data-index'), 10);
          if (!isNaN(idx) && idx >= 0 && idx < self.masterGalleryState.existingGallery.length) {
            self.masterGalleryState.existingGallery.splice(idx, 1);
            self._renderMasterGalleryUI();
          }
        });

      // Delete staged photo in Master Gallery
      $(document)
        .off('click.delMasterStaged', '.btn-del-master-staged')
        .on('click.delMasterStaged', '.btn-del-master-staged', function (e) {
          e.preventDefault();
          var idx = parseInt($(this).attr('data-staged-idx'), 10);
          if (!isNaN(idx) && idx >= 0 && idx < self.masterGalleryState.stagedGalleryFiles.length) {
            self.masterGalleryState.stagedGalleryFiles.splice(idx, 1);
            self._renderMasterGalleryUI();
          }
        });

      // Submit Master Room Gallery Form
      $(document)
        .off('submit.masterGalleryForm', '#propRoomGalleryForm')
        .on('submit.masterGalleryForm', '#propRoomGalleryForm', function (e) {
          e.preventDefault();
          var $btn = $('#btnSaveRoomGallery');
          var $btnText = $('#btnSaveRoomGalleryText');
          $btn.prop('disabled', true);
          $btnText.text('Saving Gallery...');

          var currentPropId = self.propertyId || self.encodedId || $('#propertyUpdateApp').attr('data-id') || $('#propertyUpdateApp').attr('data-raw-id') || '';
          var roomType = $('#roomGalleryTypeSelect').val() || self.masterGalleryState.activeType;

          var formData = new FormData();
          formData.set('property_id', currentPropId);
          formData.set('room_type', roomType);
          formData.set('kept_feature_image', self.masterGalleryState.coverImage || '');
          formData.set('kept_gallery', JSON.stringify(self.masterGalleryState.existingGallery));

          if (self.masterGalleryState.stagedCoverFile) {
            formData.set('feature_image', self.masterGalleryState.stagedCoverFile, self.masterGalleryState.stagedCoverFile.name);
          }

          if (self.masterGalleryState.stagedGalleryFiles.length > 0) {
            self.masterGalleryState.stagedGalleryFiles.forEach(function (f) {
              formData.append('gallery_files[]', f, f.name);
            });
          }

          $.ajax({
            url:         '/api/v3/properties/rooms/gallery/',
            method:      'POST',
            data:        formData,
            processData: false,
            contentType: false,
            dataType:    'json',
            success: function (res) {
              $btn.prop('disabled', false);
              $btnText.text('Save Room Gallery');
              if (res.status === 'success') {
                var typeLabel = res.room_type_label || (res.room_type ? res.room_type.replace(/[^a-zA-Z0-9]+/g, ' ').trim().replace(/\b\w/g, function (c) { return c.toUpperCase(); }) : 'Room Category');
                self._toast(res.msg || ('Master gallery for ' + typeLabel + ' updated successfully!'), 'success');
                self._closeRoomGalleryModal();
                self._loadPropertyRooms();
              } else {
                self._toast(res.msg || 'Failed to update room gallery.', 'error');
              }
            },
            error: function (xhr) {
              $btn.prop('disabled', false);
              $btnText.text('Save Room Gallery');
              var msg = 'Failed to save master room gallery.';
              try {
                var r = JSON.parse(xhr.responseText);
                if (r && r.msg) msg = r.msg;
              } catch (e) {}
              self._toast(msg, 'error');
            },
          });
        });

      /* ── Hotel Feature Image & Hotel Gallery ─────────────────── */
      $(document)
        .off('change.propThumb', '#propFeatureImage')
        .on('change.propThumb', '#propFeatureImage', function () {
          var file = this.files && this.files[0];
          if (!file) return;
          var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
          $('#propFileTitle').text('Selected: ' + file.name);
          $('#propFileSelectedName').text(file.name + ' (' + sizeMB + ' MB)');
          $('#propFileUploadZone').addClass('has-selected-file');
          var reader = new FileReader();
          reader.onload = function (e) {
            self._setThumbPreview(e.target.result, $('#propName').val());
          };
          reader.readAsDataURL(file);
        });

      $(document)
        .off('change.propGallery', '#propGalleryImages')
        .on('change.propGallery', '#propGalleryImages', function () {
          var files = this.files;
          if (!files || !files.length) return;
          for (var i = 0; i < files.length; i++) {
            (function (file) {
              var reader = new FileReader();
              reader.onload = function (e) {
                file.previewUrl = e.target.result;
                self.stagedGalleryFiles.push(file);
                self._renderStagedGallery();
              };
              reader.readAsDataURL(file);
            })(files[i]);
          }
        });

      $(document)
        .off('click.removeExistingPhoto', '.btn-remove-existing-photo')
        .on('click.removeExistingPhoto', '.btn-remove-existing-photo', function (e) {
          e.preventDefault();
          var idx = parseInt($(this).attr('data-index'), 10);
          if (!isNaN(idx) && idx >= 0 && idx < self.existingGallery.length) {
            self.existingGallery.splice(idx, 1);
            self._renderExistingGallery();
          }
        });

      $(document)
        .off('click.removeStagedPhoto', '.btn-remove-staged-photo')
        .on('click.removeStagedPhoto', '.btn-remove-staged-photo', function (e) {
          e.preventDefault();
          var idx = parseInt($(this).attr('data-staged-index'), 10);
          if (!isNaN(idx) && idx >= 0 && idx < self.stagedGalleryFiles.length) {
            self.stagedGalleryFiles.splice(idx, 1);
            self._renderStagedGallery();
          }
        });

      /* Form submit */
      $(document)
        .off('submit.propUpdate', '#propertyUpdateForm')
        .on('submit.propUpdate', '#propertyUpdateForm', function (e) {
          e.preventDefault();
          self._submitForm();
        });
    },

    /* ── Submit Main Hotel Form ────────────────────────────────── */
    _submitForm: function () {
      if (this.saving) return;
      if (!this._validate()) return;

      this.saving = true;
      var $btn  = $('#btnSaveProperty');
      var $text = $('#btnSaveText');
      $btn.prop('disabled', true);
      $text.text('Saving...');
      $btn.prepend('<i class="fi fi-rr-spinner spinner btn-spinner"></i> ');

      var formElement = document.getElementById('propertyUpdateForm');
      var formData = new FormData(formElement);

      // Kept hotel gallery
      formData.set('kept_gallery', JSON.stringify(this.existingGallery));

      // Selected hotel amenities
      formData.set('property_amenities', JSON.stringify(this.selectedAmenities));

      // Listing Status
      var statusVal = $('input[name="status"]:checked').val() || '1';
      formData.set('status', statusVal);

      // Publish to Website
      var isWebPublished = $('#chkPublishToWebsite').is(':checked');
      formData.set('publishToWebsite', isWebPublished ? '1' : '0');

      // Staged hotel gallery photos
      if (this.stagedGalleryFiles.length > 0) {
        formData.delete('gallery[]');
        this.stagedGalleryFiles.forEach(function (file) {
          formData.append('gallery[]', file, file.name);
        });
      }

      var self = this;

      $.ajax({
        url:         '/api/v3/properties/update/',
        method:      'POST',
        data:        formData,
        processData: false,
        contentType: false,
        dataType:    'json',
        success: function (res) {
          self.saving = false;
          $btn.prop('disabled', false);
          $text.text('Save Property Details');
          $btn.find('.btn-spinner').remove();

          if (res.status === 'success') {
            self._toast(res.msg || 'Property updated successfully!', 'success');

            if (res.live_url && res.is_published_website) {
              $('#btnViewLiveHotel').attr('href', res.live_url).fadeIn(200);
              $('#propLiveWebsiteUrl').text(res.live_url);
              $('#propLiveWebsiteBtn').attr('href', res.live_url);
              $('#propLiveWebsiteBox').fadeIn(200);
            } else {
              $('#btnViewLiveHotel').fadeOut(200);
              $('#propLiveWebsiteBox').fadeOut(200);
            }

            setTimeout(function () {
              window.location.href = '/dashboard/vendors/property/update/?id=' + (res.encoded_id || self.encodedId);
            }, 1200);
          } else {
            self._toast(res.msg || 'Update failed. Please try again.', 'error');
          }
        },
        error: function (xhr) {
          self.saving = false;
          $btn.prop('disabled', false);
          $text.text('Save Property Details');
          $btn.find('.btn-spinner').remove();
          var msg = (xhr.responseJSON && xhr.responseJSON.msg) || 'Server error. Please try again.';
          self._toast(msg, 'error');
        },
      });
    },

    /* ── Validation ────────────────────────────────────────────── */
    _validate: function () {
      var isValid = true;
      var fields = [
        { selector: '#propName',    errId: '#errPropName',    msg: 'Property name is required.' },
        { selector: '#propType',    errId: '#errPropType',    msg: 'Please select a property type.' },
        { selector: '#propAddress', errId: '#errPropAddress', msg: 'Address is required.' },
        { selector: '#propCity',    errId: '#errPropCity',    msg: 'City is required.' },
        { selector: '#propPincode', errId: '#errPropPincode', msg: 'Pin code is required.' },
        { selector: '#propEmail',   errId: '#errPropEmail',   msg: 'Reservation email is required.' },
        { selector: '#propMobile',  errId: '#errPropMobile',  msg: 'Reservation contact is required.' },
      ];

      fields.forEach(function (f) { $(f.errId).text(''); });

      fields.forEach(function (f) {
        var val = $(f.selector).val();
        if (!val || !val.trim()) {
          $(f.errId).text(f.msg);
          isValid = false;
        }
      });

      if (!isValid) {
        // Switch to Tab 1 if errors are there
        $('.prop-tab-btn[data-target="#tabProfile"]').trigger('click');
        var $firstErr = $('.v3-field-error').filter(function () {
          return $(this).text().trim() !== '';
        }).first();
        if ($firstErr.length) {
          $('html, body').animate({ scrollTop: $firstErr.offset().top - 120 }, 300);
        }
        this._toast('Please fill all required fields.', 'warning');
      }
      return isValid;
    },

    /* ── Live Preview & Progress (Legacy stubs) ───────────────── */
    _updateLivePreview: function () {},
    _updateProgress: function () {},

    /* ── Thumbnail Preview ─────────────────────────────────────── */
    _setThumbPreview: function (src, title) {
      var $wrap  = $('#propThumbCurrent');
      var $label = $('#propThumbLabel');
      var $live  = $('#livePropThumb');

      if (src && src !== '') {
        var imgEl = '<img src="' + escHTML(src) + '" alt="" class="prop-thumb-img">';
        $wrap.html(imgEl);
        $live.html(imgEl.replace('class="prop-thumb-img"', 'class="live-prop-thumb-img"'));
        $label.text('Current feature image');
        $('#propThumbPreviewWrap').addClass('has-image');
      } else {
        $wrap.html('<i class="fi fi-rr-building"></i>');
        $live.html('<i class="fi fi-rr-building"></i>');
        $label.text('No feature image uploaded');
        $('#propThumbPreviewWrap').removeClass('has-image');
      }
    },

    /* ── UI Helpers ────────────────────────────────────────────── */
    _showLayout: function () {
      $('#propertyEditLoading').hide();
      $('#propertyEditLayout').removeClass('d-none').css('display', 'grid').hide().fadeIn(250);
    },

    _showLoadError: function (msg) {
      $('#propertyEditLayout').addClass('d-none').hide();
      $('#propertyEditLoading').html(
        '<div class="v3-load-error">' +
        '<i class="fi fi-rr-exclamation"></i>' +
        '<p>' + escHTML(msg) + '</p>' +
        '<a href="/dashboard/vendors/property/list/" class="v3-btn-secondary">' +
        '<i class="fi fi-rr-arrow-left"></i> Back to List</a>' +
        '</div>'
      ).show();
    },

    _toast: function (msg, type) {
      if (typeof popUpMessage === 'function') {
        popUpMessage('center', msg, type);
      } else {
        var icons = { success: '✓', error: '✕', warning: '⚠' };
        var $t = $('<div class="v3-toast v3-toast-' + type + '">' + (icons[type] || '') + ' ' + escHTML(msg) + '</div>');
        $('body').append($t);
        setTimeout(function () { $t.addClass('show'); }, 10);
        setTimeout(function () { $t.removeClass('show'); setTimeout(function () { $t.remove(); }, 400); }, 3500);
      }
    },
  };

  /* ── Auto-init on DOM ready ─────────────────────────────────── */
  $(function () {
    window.V3PropertyUpdate.init();
  });
});
