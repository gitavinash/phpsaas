/**
 * V3 Property Add / Creation Module
 * SPA asynchronous property creation controller
 */

(function ($) {
  'use strict';

  function escHTML(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  var PropertyAdd = {
    stagedGalleryFiles: [],

    init: function () {
      this._initSelect2();
      this._bindEvents();
    },

    _initSelect2: function () {
      if (!$.fn.select2) return;

      if ($('#propType').hasClass('select2-hidden-accessible')) {
        try { $('#propType').select2('destroy'); } catch (e) {}
      }

      $('#propType').select2({
        width: '100%',
        placeholder: '— Select property type —',
        allowClear: true,
        minimumResultsForSearch: 8,
        dropdownAutoWidth: false,
      });

      if ($('#propCity').hasClass('select2-hidden-accessible')) {
        try { $('#propCity').select2('destroy'); } catch (e) {}
      }

      $('#propCity').select2({
        width:            '100%',
        allowClear:       true,
        placeholder:      'Search city...',
        dropdownAutoWidth: false,
        ajax: {
          url:      '/api/v3/cities/',
          dataType: 'json',
          delay:    200,
          data: function (params) {
            return { search: params.term || '' };
          },
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

      $('#propCity').on('select2:select.propErr select2:clear.propErr change.select2.propErr', function () {
        $('#errPropCity').text('');
      });
      $('#propType').on('select2:select.propErr select2:clear.propErr change.select2.propErr', function () {
        $('#errPropType').text('');
      });
    },

    _bindEvents: function () {
      var self = this;

      // Status radio pill clicks
      $('.v3-radio-pill').on('click', function () {
        $('.v3-radio-pill').removeClass('active-pill inactive-pill');
        var val = $(this).find('input[type="radio"]').val();
        if (val === '1') {
          $(this).addClass('active-pill');
        } else {
          $(this).addClass('inactive-pill');
        }
      });

      // Feature Cover Image Dropzone & Input
      $('#propFeatureImage').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#propThumbCurrent').html('<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" />');
          $('#propThumbLabel').text(file.name);
          $('#propFileSelectedName').text(file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)');
        };
        reader.readAsDataURL(file);
      });

      // Multi-Gallery Dropzone & Input
      $('#propGalleryImages').on('change', function () {
        var files = Array.from(this.files);
        files.forEach(function (f) {
          self.stagedGalleryFiles.push(f);
        });
        self._renderGalleryStaging();
        $(this).val('');
      });

      // Drag & Drop for Feature Dropzone
      var $featDrop = $('#propFileUploadZone');
      $featDrop.on('dragover dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
      }).on('dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
      }).on('drop', function (e) {
        var dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files.length) {
          var file = dt.files[0];
          var reader = new FileReader();
          reader.onload = function (ev) {
            $('#propThumbCurrent').html('<img src="' + ev.target.result + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" />');
            $('#propThumbLabel').text(file.name);
            $('#propFileSelectedName').text(file.name);
          };
          reader.readAsDataURL(file);
          try {
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('propFeatureImage').files = dataTransfer.files;
          } catch (err) {}
        }
      });

      // Drag & Drop for Gallery Dropzone
      var $galDrop = $('#propGalleryUploadZone');
      $galDrop.on('dragover dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
      }).on('dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
      }).on('drop', function (e) {
        var dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files.length) {
          Array.from(dt.files).forEach(function (f) {
            self.stagedGalleryFiles.push(f);
          });
          self._renderGalleryStaging();
        }
      });

      // Remove Staged Gallery Item
      $(document).on('click', '.btn-remove-staged-gal', function (e) {
        e.preventDefault();
        var idx = $(this).data('idx');
        if (idx !== undefined && idx >= 0 && idx < self.stagedGalleryFiles.length) {
          self.stagedGalleryFiles.splice(idx, 1);
          self._renderGalleryStaging();
        }
      });

      // Form Submission
      $('#propertyAddForm').on('submit', function (e) {
        e.preventDefault();
        self._submitForm();
      });
    },

    _renderGalleryStaging: function () {
      var self = this;
      var $grid = $('#propStagedGalleryGrid');
      $grid.empty();

      if (self.stagedGalleryFiles.length === 0) {
        $grid.hide();
        return;
      }

      $grid.show();
      self.stagedGalleryFiles.forEach(function (file, idx) {
        var itemHtml = $(
          '<div class="prop-gallery-item staged-new" style="position: relative;">' +
          '  <img src="" alt="Gallery Image" class="prop-gallery-thumb" id="stagedGal_' + idx + '" />' +
          '  <button type="button" class="prop-gallery-delete-btn btn-remove-staged-gal" data-idx="' + idx + '" title="Remove">' +
          '    <i class="fi fi-rr-trash"></i>' +
          '  </button>' +
          '  <span class="prop-gallery-name-tag">' + escHTML(file.name) + '</span>' +
          '</div>'
        );
        $grid.append(itemHtml);

        var reader = new FileReader();
        reader.onload = function (e) {
          $('#stagedGal_' + idx).attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      });
    },

    _submitForm: function () {
      var self = this;

      // Clear previous field errors
      $('.v3-field-error').text('');

      var name = $.trim($('#propName').val());
      var type = $.trim($('#propType').val());
      var email = $.trim($('#propEmail').val());
      var mobile = $.trim($('#propMobile').val());
      var address = $.trim($('#propAddress').val());
      var city = $.trim($('#propCity').val());
      var pinCode = $.trim($('#propPinCode').val());

      var hasError = false;

      if (!name) {
        $('#errPropName').text('Please enter the property name.');
        hasError = true;
      }
      if (!type) {
        $('#errPropType').text('Please select a property type.');
        hasError = true;
      }
      if (!email) {
        $('#errPropEmail').text('Please enter a reservation email.');
        hasError = true;
      }
      if (!mobile) {
        $('#errPropMobile').text('Please enter a reservation contact number.');
        hasError = true;
      }
      if (!address) {
        $('#errPropAddress').text('Please enter the street address.');
        hasError = true;
      }
      if (!city) {
        $('#errPropCity').text('Please select a city.');
        hasError = true;
      }
      if (!pinCode) {
        $('#errPropPinCode').text('Please enter the postal pin code.');
        hasError = true;
      }

      if (hasError) {
        self._toast('Please fill all required fields.', 'warning');
        return;
      }

      var $btn = $('#btnCreateProperty');
      var $btnText = $('#btnCreateText');
      $btn.prop('disabled', true);
      $btnText.text('Creating Property...');

      var formData = new FormData(document.getElementById('propertyAddForm'));

      // Append staged gallery files
      if (self.stagedGalleryFiles.length > 0) {
        formData.delete('gallery[]');
        formData.delete('gallery');
        self.stagedGalleryFiles.forEach(function (f) {
          formData.append('gallery[]', f);
        });
      }

      formData.set('status', $('input[name="status"]:checked').val() || '1');
      formData.set('publishToWebsite', $('#chkPublishToWebsite').is(':checked') ? 1 : 0);

      $.ajax({
        url: '/api/v3/properties/create/',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (res) {
          $btn.prop('disabled', false);
          $btnText.text('Create Property & Configure Rooms');

          if (res.status === 'success') {
            self._toast(res.msg || 'Property created successfully!', 'success');
            setTimeout(function () {
              var targetId = res.encodedId || btoa(String(res.propertyId));
              window.location.href = '/dashboard/vendors/property/update/?id=' + targetId + '&tab=rooms';
            }, 1200);
          } else {
            self._toast(res.msg || 'Failed to create property.', 'error');
          }
        },
        error: function (xhr) {
          $btn.prop('disabled', false);
          $btnText.text('Create Property & Configure Rooms');
          var msg = 'Failed to create property. Please try again.';
          try {
            var r = JSON.parse(xhr.responseText);
            if (r && r.msg) msg = r.msg;
          } catch (e) {}
          self._toast(msg, 'error');
        },
      });
    },

    _toast: function (msg, type) {
      if (typeof popUpMessage === 'function') {
        popUpMessage('center', msg, type);
      } else {
        var icons = { success: '✓', error: '✕', warning: '⚠' };
        var $t = $('<div class="v3-toast v3-toast-' + type + '">' + (icons[type] || '') + ' ' + escHTML(msg) + '</div>');
        $('body').append($t);
        setTimeout(function () { $t.addClass('show'); }, 10);
        setTimeout(function () {
          $t.removeClass('show');
          setTimeout(function () { $t.remove(); }, 400);
        }, 3500);
      }
    },
  };

  window.V3PropertyAdd = PropertyAdd;

  $(function () {
    window.V3PropertyAdd.init();
  });
})(jQuery);
