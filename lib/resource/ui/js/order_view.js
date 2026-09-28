/**
 * Order View Module
 * Handles order detail UI interactions, product management, partner assignments,
 * modal dialogs, and AJAX submissions.
 */
(function($) {
    'use strict';

    // Global UI Helper for processing state
    window.orderUiProcessing = window.orderUiProcessing || {
        setButtonProcessing: function(button, labelText) {
            var $btn = $(button);
            if (!$btn.length || $btn.attr('data-processing') === '1') return;

            $btn.attr('data-processing', '1')
                .attr('aria-disabled', 'true')
                .data('originalHtml', $btn.html())
                .css({ 'pointer-events': 'none', 'opacity': '0.85' })
                .html('<i class="fas fa-spinner fa-spin"></i> ' + (labelText || 'Processing...'));
        },
        clearButtonProcessing: function(button) {
            var $btn = $(button);
            if (!$btn.length || $btn.attr('data-processing') !== '1') return;

            $btn.removeAttr('data-processing')
                .removeAttr('aria-disabled')
                .css({ 'pointer-events': '', 'opacity': '' });

            if ($btn.data('originalHtml')) {
                $btn.html($btn.data('originalHtml'));
                $btn.removeData('originalHtml');
            }
        }
    };

    $(document).ready(function() {
        var $root = $('.order-info-ui');
        if (!$root.length) return;

        var orderId = $root.attr('data-order-id') || 'default';

        // -------------------------------------------------------------
        // 1. Toast & Feedback Helpers
        // -------------------------------------------------------------
        function showToast(message, status) {
            var $toast = $('#orderToastNotification');
            if (!$toast.length) return;

            $toast.text(message)
                .attr('class', 'order-toast-notification ' + (status === 'success' ? 'success' : 'error'))
                .addClass('is-visible');

            setTimeout(function() {
                $toast.removeClass('is-visible');
            }, 3000);
        }

        function showFeedback(status, title, message, meta) {
            var $box = $('#orderActionFeedback');
            var $title = $('#orderActionFeedbackTitle');
            var $meta = $('#orderActionFeedbackMeta');
            var $body = $('#orderActionFeedbackBody');
            var $toggle = $('#toggleOrderActionFeedback');

            if (!$box.length) {
                showToast(message || 'Action processed.', status || 'success');
                return;
            }

            $box.removeClass('is-success is-error is-open')
                .addClass((status === 'success' ? 'is-success' : 'is-error') + ' is-open')
                .show();

            $title.text(title || 'Order Update');
            $meta.text(meta || (status === 'success' ? 'Success' : 'Attention required'));
            $body.text(message || '');
            $toggle.text('Hide').attr('aria-expanded', 'true');
        }

        $('#toggleOrderActionFeedback').on('click', function() {
            var $box = $('#orderActionFeedback');
            var isOpen = $box.hasClass('is-open');
            $box.toggleClass('is-open', !isOpen);
            $(this).text(isOpen ? 'Show' : 'Hide').attr('aria-expanded', isOpen ? 'false' : 'true');
        });

        function showAlert(icon, title, message, timer) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon,
                    type: icon,
                    title: title,
                    text: message,
                    timer: timer || 2000,
                    showConfirmButton: false
                });
            } else {
                alert(message);
            }
        }

        // -------------------------------------------------------------
        // 2. Collapsible Cards Persistence
        // -------------------------------------------------------------
        var storagePrefix = 'order-ui-collapse-' + orderId + '-';
        $('.order-collapse-toggle').each(function() {
            var $toggle = $(this);
            var targetId = $toggle.attr('data-target');
            var $panel = $toggle.parent();
            var storageKey = storagePrefix + targetId;

            try {
                var savedState = localStorage.getItem(storageKey);
                if (savedState !== null) {
                    var isOpen = (savedState === 'open');
                    $panel.toggleClass('is-open', isOpen);
                    $toggle.attr('aria-expanded', isOpen ? 'true' : 'false');
                }
            } catch (e) {
                // Ignore localStorage errors
            }

            $toggle.on('click', function() {
                var isOpen = $panel.hasClass('is-open');
                var nextOpen = !isOpen;
                $panel.toggleClass('is-open', nextOpen);
                $toggle.attr('aria-expanded', nextOpen ? 'true' : 'false');

                try {
                    localStorage.setItem(storageKey, nextOpen ? 'open' : 'closed');
                } catch (e) {}
            });
        });

        // -------------------------------------------------------------
        // 3. Select2 Initialization & Product Dropdown Utilities
        // -------------------------------------------------------------
        function ensureSelect2(callback) {
            if ($.fn.select2) {
                callback();
            } else {
                var cssUrl = (typeof SITE_URL !== 'undefined' ? SITE_URL : '') + '/' + (typeof PLUGIN_LIBRARY !== 'undefined' ? PLUGIN_LIBRARY : '') + 'select2/css/select2.min.css';
                var jsUrl = (typeof SITE_URL !== 'undefined' ? SITE_URL : '') + '/' + (typeof PLUGIN_LIBRARY !== 'undefined' ? PLUGIN_LIBRARY : '') + 'select2/js/select2.min.js';

                if (!$('link[href="' + cssUrl + '"]').length) {
                    $('head').append('<link rel="stylesheet" href="' + cssUrl + '">');
                }
                $.getScript(jsUrl, callback);
            }
        }

        function formatProductOption(state) {
            if (!state.id || !state.element) return state.text;
            if (state.element.disabled) return null;

            var imgUrl = $(state.element).attr('data-image') || $(state.element).data('image');
            var imgHtml = (imgUrl && $.trim(imgUrl) !== '') ?
                '<img class="select-product-option-img" src="' + imgUrl + '" alt="" />' :
                '<div class="select-product-option-no-img">📦</div>';

            return '<div class="select-product-option-wrapper">' + imgHtml + '<span class="select-product-option-text">' + state.text + '</span></div>';
        }

        function initSelect2($selects) {
            if (!$selects || !$selects.length) return;
            ensureSelect2(function() {
                $selects.each(function() {
                    var $el = $(this);
                    var isProductSelect = $el.hasClass('select-product-dropdown') || $el.attr('name') === 'assigned_product_id';

                    if ($el.data('select2')) {
                        $el.select2('destroy');
                    }

                    var opts = { width: '100%' };
                    if (isProductSelect) {
                        opts.templateResult = formatProductOption;
                        opts.templateSelection = formatProductOption;
                        opts.escapeMarkup = function(m) { return m; };
                    }
                    $el.select2(opts);
                });
            });
        }

        function updateProductDropdownOptions() {
            var selectedIds = [];
            $('.item-product-id-input').each(function() {
                var val = $.trim($(this).val());
                if (val !== '') selectedIds.push(String(val));
            });

            $('.order-item-row').find('.select-product-dropdown').each(function() {
                var $select = $(this);
                var currentVal = String($select.val() || '');
                $select.find('option').each(function() {
                    var optVal = String($(this).val() || '');
                    if (optVal !== '') {
                        $(this).prop('disabled', (selectedIds.indexOf(optVal) !== -1 && optVal !== currentVal));
                    }
                });
                if ($select.data('select2')) {
                    initSelect2($select);
                }
            });

            var $unassignedSelect = $('#productEditMode select[name="assigned_product_id"]');
            if ($unassignedSelect.length) {
                var currentVal = String($unassignedSelect.val() || '');
                $unassignedSelect.find('option').each(function() {
                    var optVal = String($(this).val() || '');
                    if (optVal !== '') {
                        $(this).prop('disabled', (selectedIds.indexOf(optVal) !== -1 && optVal !== currentVal));
                    }
                });
                if ($unassignedSelect.data('select2')) {
                    initSelect2($unassignedSelect);
                }
            }
        }

        function reindexProducts() {
            var $rows = $('.order-item-row');
            if ($rows.length === 0) {
                $('input[name="totalProducts"]').remove();
                $('#noProductWarning').css('display', 'flex');
                $('#productEditMode').show();
                $('#orderItemsListContainer, #addProductBtnWrapper').hide();
                return;
            }

            $rows.each(function(index) {
                var $row = $(this);
                $row.attr('data-index', index);
                $row.find('.item-product-id-input').attr('name', 'product_id_' + index);
                $row.find('.edit-qty-input').attr('name', 'qty_' + index);
            });

            var $totalInput = $('input[name="totalProducts"]');
            if (!$totalInput.length) {
                $totalInput = $('<input>', { type: 'hidden', name: 'totalProducts' }).appendTo('#orderData');
            }
            $totalInput.val($rows.length);

            if ($('input[name="totalProducts"]').length > 1) {
                $('input[name="totalProducts"]').slice(1).remove();
            }

            updateProductDropdownOptions();
        }

        function createProductRowHtml(index, productId, productTitle, productImage, qty) {
            var imgHtml = (productImage && $.trim(productImage) !== '') ?
                '<img class="view-item-image" src="' + productImage + '" alt="' + productTitle + '">' :
                '<div class="view-item-image-placeholder select-product-option-no-img">📦</div>';

            var optionsHtml = '<option value="">Select product</option>';
            if (window.allProductsList && Array.isArray(window.allProductsList)) {
                window.allProductsList.forEach(function(prod) {
                    var selected = (String(prod.product_id) === String(productId)) ? 'selected' : '';
                    optionsHtml += '<option value="' + prod.product_id + '" data-image="' + (prod.product_image || '') + '" ' + selected + '>' + prod.product_title + '</option>';
                });
            }

            var viewStyle = !productId ? 'style="display: none !important;"' : '';
            var editStyle = !productId ? 'style="display: block;"' : 'style="display: none;"';

            var html =
                '<div class="order-item-row" data-index="' + index + '">' +
                '    <div class="order-item-view-container" ' + viewStyle + '>' +
                '        <div class="order-item-view-left">' +
                '            <div class="order-item-thumb-wrapper">' + imgHtml + '</div>' +
                '            <div class="order-item-details">' +
                '                <div class="order-item-title view-item-title">' + productTitle + '</div>' +
                '                <div class="order-item-qty">Qty: <span class="view-item-qty">' + qty + '</span></div>' +
                '            </div>' +
                '        </div>' +
                '        <div class="order-item-edit-btn-wrapper" style="display: flex; gap: 8px;">' +
                '            <button type="button" class="btn-premium-action btn-edit edit-item-btn"><i class="fas fa-edit"></i> Edit</button>' +
                '            <button type="button" class="btn-premium-action btn-cancel remove-item-btn" style="background-color: #ef4444 !important; color: #fff !important;"><i class="fas fa-trash-alt"></i> Remove</button>' +
                '        </div>' +
                '    </div>' +
                '    <div class="order-item-edit-container" ' + editStyle + '>' +
                '        <input type="hidden" name="product_id_' + index + '" class="item-product-id-input" value="' + productId + '">' +
                '        <div class="order-item-edit-grid">' +
                '            <div class="premium-form-group">' +
                '                <label>Select Product</label>' +
                '                <select class="premium-form-control select-product-dropdown selectJS" style="width: 100%;">' + optionsHtml + '</select>' +
                '            </div>' +
                '            <div class="premium-form-group">' +
                '                <label>Qty</label>' +
                '                <input type="text" name="qty_' + index + '" class="premium-form-control edit-qty-input" value="' + qty + '">' +
                '            </div>' +
                '            <div style="display: flex; gap: 8px; align-items: flex-end; height: 100%;">' +
                '                <button type="button" class="btn-premium-action btn-save save-item-btn" style="flex: 1; height: 38px;"><i class="fas fa-check"></i> Assign Product</button>' +
                '                <button type="button" class="btn-premium-action btn-cancel cancel-item-btn" style="height: 38px; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important;"><i class="fas fa-times"></i> Cancel</button>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>';

            return $(html);
        }

        // -------------------------------------------------------------
        // 4. Product Item Action Event Listeners
        // -------------------------------------------------------------
        $(document).on('click', '.edit-item-btn', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.order-item-row');
            $row.find('.order-item-view-container').css('display', 'none', 'important');
            $row.find('.order-item-edit-container').show();
            initSelect2($row.find('.select-product-dropdown'));
        });

        $(document).on('click', '.save-item-btn', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.order-item-row');
            var $select = $row.find('.select-product-dropdown');
            var $qtyInput = $row.find('.edit-qty-input');
            var $productIdInput = $row.find('.item-product-id-input');

            var newProductId = $select.val();
            if (!newProductId || $.trim(newProductId) === '') {
                showAlert('error', 'Product Required', 'Please select a product before saving.');
                return;
            }

            var $selectedOpt = $select.find('option:selected');
            var newProductTitle = $.trim($selectedOpt.text());
            var newProductImage = $selectedOpt.attr('data-image');
            var newQty = $qtyInput.val();
            var oldProductId = $productIdInput.val();

            $.ajax({
                type: 'POST',
                url: '/api/order/assign_product/',
                data: {
                    id: orderId,
                    product_id: newProductId,
                    qty: newQty,
                    old_product_id: oldProductId
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 200) {
                        $productIdInput.val(newProductId);
                        $row.find('.view-item-title').text(newProductTitle);
                        $row.find('.view-item-qty').text(newQty);

                        var $thumbWrapper = $row.find('.order-item-thumb-wrapper').empty();
                        if (newProductImage && $.trim(newProductImage) !== '') {
                            $('<img>', { class: 'view-item-image', src: newProductImage, alt: newProductTitle }).appendTo($thumbWrapper);
                        } else {
                            $('<div>', { class: 'view-item-image-placeholder select-product-option-no-img', text: '📦' }).appendTo($thumbWrapper);
                        }

                        $row.find('.order-item-edit-container').hide();
                        $row.find('.order-item-view-container').removeAttr('style');
                        reindexProducts();
                        showAlert('success', 'Success', res.msg || 'Product assigned successfully.', 1500);
                    } else {
                        showAlert('error', 'Error', res.msg || 'Something went wrong.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error', 'Failed to assign product.');
                }
            });
        });

        $(document).on('click', '.cancel-item-btn', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.order-item-row');
            var $productIdInput = $row.find('.item-product-id-input');

            if (!$productIdInput.val()) {
                $row.remove();
                reindexProducts();
                return;
            }

            var $select = $row.find('.select-product-dropdown');
            var $qtyInput = $row.find('.edit-qty-input');
            $select.val($productIdInput.val()).trigger('change');
            $qtyInput.val($row.find('.view-item-qty').text().trim());

            $row.find('.order-item-edit-container').hide();
            $row.find('.order-item-view-container').removeAttr('style');
            updateProductDropdownOptions();
        });

        $(document).on('click', '.remove-item-btn', function(e) {
            e.preventDefault();
            var $row = $(this).closest('.order-item-row');
            var productId = $row.find('.item-product-id-input').val();

            var performRemoval = function() {
                if (productId && $.trim(productId) !== '') {
                    $.ajax({
                        type: 'GET',
                        url: '/api/order/remove_product/',
                        data: { id: orderId, product_id: productId },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status == 200) {
                                $row.remove();
                                reindexProducts();
                                showAlert('success', 'Success', res.msg || 'Product removed successfully.', 1500);
                            } else {
                                showAlert('error', 'Error', res.msg || 'Something went wrong.');
                            }
                        },
                        error: function() {
                            showAlert('error', 'Error', 'Failed to remove product.');
                        }
                    });
                } else {
                    $row.remove();
                    reindexProducts();
                }
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to remove this product from the order?',
                    icon: 'warning',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, remove it!'
                }).then(function(result) {
                    if (result.value) performRemoval();
                });
            } else if (confirm('Are you sure you want to remove this product from the order?')) {
                performRemoval();
            }
        });

        $('#addProductBtn').on('click', function(e) {
            e.preventDefault();
            var $container = $('#orderItemsListContainer');
            if ($container.length) {
                var nextIndex = $('.order-item-row').length;
                var $newRow = createProductRowHtml(nextIndex, '', '', '', '1');
                $container.append($newRow);
                initSelect2($newRow.find('.select-product-dropdown'));
                reindexProducts();
            }
        });

        $('#saveProductEditBtn').on('click', function(e) {
            e.preventDefault();
            var $select = $('#productEditMode select[name="assigned_product_id"]');
            var $qtyInput = $('#assignedQtyInput');
            var selectedId = $select.val();

            if (!selectedId || $.trim(selectedId) === '') {
                showAlert('error', 'Product Required', 'Please select a product to assign.');
                return;
            }

            var $selectedOpt = $select.find('option:selected');
            var title = $.trim($selectedOpt.text());
            var image = $selectedOpt.attr('data-image');
            var qty = $qtyInput.length ? $qtyInput.val() : '1';

            $.ajax({
                type: 'POST',
                url: '/api/order/assign_product/',
                data: { id: orderId, product_id: selectedId, qty: qty },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 200) {
                        var $container = $('#orderItemsListContainer');
                        if ($container.length) {
                            var $newRow = createProductRowHtml(0, selectedId, title, image, qty);
                            $container.append($newRow);
                            reindexProducts();
                            showAlert('success', 'Success', res.msg || 'Product assigned successfully.', 1500);

                            $('#noProductWarning, #productEditMode').hide();
                            $('#orderItemsListContainer, #addProductBtnWrapper').css('display', 'block');
                            $select.val('');
                            if ($qtyInput.length) $qtyInput.val('1');
                            if ($select.data('select2')) $select.trigger('change');
                        }
                    } else {
                        showAlert('error', 'Error', res.msg || 'Something went wrong.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error', 'Failed to assign product.');
                }
            });
        });

        $('#cancelProductEditBtn').on('click', function(e) {
            e.preventDefault();
            var $select = $('#productEditMode .selectJS');
            if ($select.length && $select.val() !== '') {
                $select.val('').trigger('change');
            }
            $('#assignedQtyInput').val('1');
        });

        // -------------------------------------------------------------
        // 5. Vendor & Delivery Partner Assignments
        // -------------------------------------------------------------
        $('#changeVendorBtn').on('click', function(e) {
            e.preventDefault();
            $('#vendorViewMode').hide();
            $('#vendorEditMode').show();
        });

        $('#cancelVendorEditBtn').on('click', function(e) {
            e.preventDefault();
            $('#vendorEditMode').hide();
            $('#vendorViewMode').show();
        });

        $('#saveVendorEditBtn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.prop('disabled') || $btn.attr('data-processing') === '1') return;

            var vendorCode = $('#vendorEditMode select[name="assigned_vendor"]').val();
            if (!vendorCode || $.trim(vendorCode) === '') {
                showAlert('error', 'Vendor Required', 'Please select a partner.');
                return;
            }

            var origHtml = $btn.html();
            $btn.prop('disabled', true).attr('data-processing', '1').html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                type: 'POST',
                url: '/api/order/assign_vendor/',
                data: { id: orderId, assigned_vendor: vendorCode },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 200) {
                        showAlert('success', 'Success', res.msg || 'Vendor assigned successfully.', 1500);
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        showAlert('error', 'Error', res.msg || 'Something went wrong.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error', 'Failed to assign vendor.');
                },
                complete: function() {
                    $btn.prop('disabled', false).removeAttr('data-processing').html(origHtml);
                }
            });
        });

        $(document).on('click', '#saveDeliveryPartnerBtn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.prop('disabled') || $btn.attr('data-processing') === '1') return;

            var agentCode = $('#deliveryAgentEditMode select[name="assigned_delivery_agent"]').val();
            if (!agentCode || $.trim(agentCode) === '') {
                showAlert('error', 'Delivery Partner Required', 'Please select a delivery partner.');
                return;
            }

            var origHtml = $btn.html();
            $btn.prop('disabled', true).attr('data-processing', '1').html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                type: 'POST',
                url: '/api/order/assign_delivery_agent/',
                data: { id: orderId, assigned_delivery_agent: agentCode },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 200) {
                        showAlert('success', 'Success', res.msg || 'Delivery Agent assigned successfully.', 1500);
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        showAlert('error', 'Error', res.msg || 'Something went wrong.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error', 'Failed to assign delivery agent.');
                },
                complete: function() {
                    $btn.prop('disabled', false).removeAttr('data-processing').html(origHtml);
                }
            });
        });

        // -------------------------------------------------------------
        // 6. Action Modal Logic (Reschedule, Return, Deliver)
        // -------------------------------------------------------------
        var $modal = $('#orderActionModal');
        var $form = $('#orderActionForm');

        if ($modal.length && $form.length) {
            var today = new Date();
            var tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);

            function formatDateForInput(dateObj) {
                var yyyy = dateObj.getFullYear();
                var mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                var dd = String(dateObj.getDate()).padStart(2, '0');
                return yyyy + '-' + mm + '-' + dd;
            }

            function isFutureDateOnly(dateValue) {
                return dateValue && dateValue >= formatDateForInput(tomorrow);
            }

            var minAllowedDate = formatDateForInput(tomorrow);
            var currentActionConfig = null;

            var $deliveryDateInput = $('#deliveryDateInput');
            if ($deliveryDateInput.length) {
                $deliveryDateInput.attr('min', minAllowedDate);
                $deliveryDateInput.on('focus click', function() {
                    if (typeof this.showPicker === 'function') this.showPicker();
                }).on('change', function() {
                    if (this.value && !isFutureDateOnly(this.value)) {
                        this.value = '';
                        showToast('Please select a future date only.', 'error');
                    }
                });
            }

            var modalConfigs = {
                'orderRescheduleBtn': {
                    action: 'rescdule_delivery',
                    endpoint: '/api/order/rescdule_delivery/',
                    title: 'Confirm Other Day Delivery',
                    btnText: 'Confirm Other Day Delivery',
                    colorClass: 'action-reschedule',
                    requireComment: true,
                    requireSchedule: true,
                    requirePhoto: false,
                    showPhotoField: false,
                    requireReturnReason: false
                },
                'orderReturnBtn': {
                    action: 'return',
                    endpoint: '/api/order/return/',
                    title: 'Confirm Return',
                    btnText: 'Confirm Return',
                    colorClass: 'action-return',
                    requireComment: true,
                    requireSchedule: false,
                    requirePhoto: false,
                    showPhotoField: false,
                    requireReturnReason: true
                },
                'orderDeliverBtn': {
                    action: 'delivered',
                    endpoint: '/api/order/delivered/',
                    title: 'Confirm Delivery',
                    btnText: 'Confirm Delivery',
                    colorClass: 'action-deliver',
                    requireComment: true,
                    requireSchedule: false,
                    requirePhoto: true,
                    showPhotoField: true,
                    requireReturnReason: false
                }
            };

            function getOrderDeliveryDate() {
                var dateVal = (window.orderDeliveryDate !== undefined && window.orderDeliveryDate !== null) ? String(window.orderDeliveryDate) : '';
                if (!dateVal) {
                    dateVal = $modal.attr('data-delivery-date') || '';
                }
                if (!dateVal) {
                    dateVal = $('.order-info-ui').attr('data-delivery-date') || '';
                }
                dateVal = $.trim(dateVal);
                if (dateVal.length >= 10 && dateVal.indexOf('-') !== -1) {
                    return dateVal.substring(0, 10);
                }
                return dateVal;
            }

            function isPhotoRequiredForDelivery() {
                var deliveryDate = getOrderDeliveryDate();
                if (!deliveryDate) {
                    return true;
                }
                var todayStr = formatDateForInput(new Date());
                var normalizedDate = deliveryDate;
                if (!/^\d{4}-\d{2}-\d{2}$/.test(normalizedDate)) {
                    var parsed = Date.parse(deliveryDate);
                    if (!isNaN(parsed)) {
                        normalizedDate = formatDateForInput(new Date(parsed));
                    }
                }
                if (/^\d{4}-\d{2}-\d{2}$/.test(normalizedDate)) {
                    if (normalizedDate < todayStr) {
                        return false;
                    }
                    return true;
                }
                return true;
            }

            function closeModal() {
                $modal.removeClass('is-active');
                $form[0].reset();
                currentActionConfig = null;

                if (window.orderUiProcessing && typeof window.orderUiProcessing.clearButtonProcessing === 'function') {
                    window.orderUiProcessing.clearButtonProcessing($('#orderRescheduleBtn'));
                    window.orderUiProcessing.clearButtonProcessing($('#orderReturnBtn'));
                    window.orderUiProcessing.clearButtonProcessing($('#orderDeliverBtn'));
                }

                $('#rescheduleFields, #returnReasonGroup').hide();
                $('#deliveryDateInput, #deliveryPreferredTimeInput, #returnReasonInput').prop('required', false).val('');
                $('#deliveryDateRequiredMark, #deliveryTimeRequiredMark, #photoRequiredMark, #returnReasonRequiredMark').hide();
                $('#actionPhoto').prop('required', false);
                $('#actionComment').prop('required', false).attr('placeholder', 'Enter comments here...');
                $('#commentRequiredMark').show();
                $('#actionPhotoGroup').show();

                var $previewBox = $('#photoPreviewBox');
                $previewBox.removeClass('has-image').css('background-image', '');
                $previewBox.find('i').show();
                $previewBox.find('span').text('Select or capture photo');
            }

            $.each(modalConfigs, function(btnId, config) {
                $('#' + btnId).on('click', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    currentActionConfig = config;
                    $('#actionTypeInput').val(config.action);
                    $('#modalTitle').text(config.title);
                    $('#submitActionBtn').text(config.btnText);

                    $('#rescheduleFields').toggle(!!config.requireSchedule);
                    if ($deliveryDateInput.length) {
                        $deliveryDateInput.attr('min', minAllowedDate).prop('required', !!config.requireSchedule);
                        if (!config.requireSchedule) $deliveryDateInput.val('');
                    }
                    $('#deliveryPreferredTimeInput').prop('required', !!config.requireSchedule);
                    if (!config.requireSchedule) $('#deliveryPreferredTimeInput').val('');

                    $('#deliveryDateRequiredMark, #deliveryTimeRequiredMark').toggle(!!config.requireSchedule);

                    var isDeliveredAction = (config.action === 'delivered' || config.action === 'deliverd');
                    var photoNeeded = isDeliveredAction ? isPhotoRequiredForDelivery() : !!config.requirePhoto;
                    $('#actionPhoto').prop('required', photoNeeded);
                    $('#photoRequiredMark').toggle(photoNeeded);

                    $('#returnReasonGroup').toggle(!!config.requireReturnReason);
                    $('#returnReasonInput').prop('required', !!config.requireReturnReason).val('');
                    $('#returnReasonRequiredMark').toggle(!!config.requireReturnReason);

                    $('#actionComment').prop('required', !!config.requireComment)
                        .attr('placeholder', config.action === 'return' ? 'Write additional comment (optional)...' : 'Enter comments here...');
                    $('#commentRequiredMark').toggle(!!config.requireComment);
                    $('#actionPhotoGroup').toggle(!!config.showPhotoField);

                    // Atomic class application to prevent style reflow flash
                    $modal.attr('class', 'order-action-modal ' + config.colorClass + ' is-active');

                    // Focus without viewport scroll/bounce
                    setTimeout(function() {
                        var commentEl = document.getElementById('actionComment');
                        if (commentEl && typeof commentEl.focus === 'function') {
                            commentEl.focus({ preventScroll: true });
                        }
                    }, 220);
                });
            });

            $modal.find('.close-modal-btn, .order-action-modal-backdrop').on('click', closeModal);

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $modal.hasClass('is-active')) {
                    closeModal();
                }
            });

            $('#actionPhoto').on('change', function() {
                var file = this.files ? this.files[0] : null;
                var $previewBox = $('#photoPreviewBox');
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        $previewBox.addClass('has-image')
                            .css('background-image', 'url(' + evt.target.result + ')');
                        $previewBox.find('i').hide();
                        $previewBox.find('span').text(file.name);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $previewBox.removeClass('has-image').css('background-image', '');
                    $previewBox.find('i').show();
                    $previewBox.find('span').text('Select or capture photo');
                }
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                var action = $('#actionTypeInput').val();
                var comment = $.trim($('#actionComment').val());
                var fileInput = $('#actionPhoto')[0];
                var selectedPhoto = fileInput && fileInput.files ? fileInput.files[0] : null;
                var selectedReturnReason = $.trim($('#returnReasonInput').val());
                var deliveryDateValue = $.trim($('#deliveryDateInput').val());
                var deliveryPreferredTimeValue = $.trim($('#deliveryPreferredTimeInput').val());

                if (action !== 'return' && !comment) {
                    showToast('Comment is required.', 'error');
                    return;
                }
                if (action === 'return' && !selectedReturnReason) {
                    showToast('Please select a return reason.', 'error');
                    return;
                }
                if ((action === 'reschulde' || action === 'rescdule_delivery') && !deliveryDateValue) {
                    showToast('Delivery date is required for reschedule.', 'error');
                    return;
                }
                if ((action === 'reschulde' || action === 'rescdule_delivery') && !isFutureDateOnly(deliveryDateValue)) {
                    showToast('Please select a future date only.', 'error');
                    return;
                }
                if ((action === 'reschulde' || action === 'rescdule_delivery') && !deliveryPreferredTimeValue) {
                    showToast('Delivery preferred time is required for reschedule.', 'error');
                    return;
                }
                if ((action === 'delivered' || action === 'deliverd') && isPhotoRequiredForDelivery() && !selectedPhoto) {
                    showToast('Delivery photo is required for today\'s delivery.', 'error');
                    return;
                }

                var $submitBtn = $('#submitActionBtn');
                $submitBtn.prop('disabled', true);
                var originalBtnText = $submitBtn.text();
                $submitBtn.text('Processing...');

                var formData = new FormData($form[0]);
                var actionUrl = (currentActionConfig && currentActionConfig.endpoint) ? currentActionConfig.endpoint : ('/api/order/' + action + '/');

                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 200 || res.status === 'success') {
                            showAlert('success', 'Success', res.msg || 'Action processed successfully!', 1500);
                            showToast(res.msg || 'Action processed successfully!', 'success');
                            setTimeout(function() { window.location.reload(); }, 1500);
                        } else {
                            showAlert('error', 'Error', res.msg || 'Failed to process action.', 2000);
                            showToast(res.msg || 'Failed to process action.', 'error');
                            $submitBtn.prop('disabled', false).text(originalBtnText);
                        }
                    },
                    error: function(err) {
                        console.error('Error submitting order action:', err);
                        showAlert('error', 'Error', 'Something went wrong. Please try again.', 2000);
                        showToast('Something went wrong. Please try again.', 'error');
                        $submitBtn.prop('disabled', false).text(originalBtnText);
                    }
                });
            });
        }

        // -------------------------------------------------------------
        // 7. Footer CTA Action Buttons (Update Order, Accept, Reject)
        // -------------------------------------------------------------
        function validateProductSelection() {
            var $productInputs = $('.item-product-id-input');

            if ($productInputs.length > 0) {
                var valid = true;
                $productInputs.each(function() {
                    var val = $.trim($(this).val());
                    if (!val) {
                        valid = false;
                        return false;
                    }
                });
                return valid;
            }

            var $editModeContainer = $('#productEditMode');
            var $assignedProductSelect = $('select[name="assigned_product_id"]');
            if ($editModeContainer.length && $editModeContainer.is(':visible') && $assignedProductSelect.length) {
                var val = $.trim($assignedProductSelect.val());
                if (!val) return false;
            }

            return true;
        }

        // -------------------------------------------------------------
        // 6.5. Order Meta Datepicker Validation (Current + Future Dates)
        // -------------------------------------------------------------
        function getTodayDateStr() {
            var d = new Date();
            var yyyy = d.getFullYear();
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return yyyy + '-' + mm + '-' + dd;
        }

        var $metaDatepickers = $('.order-meta-datepicker');
        if ($metaDatepickers.length) {
            var todayStr = getTodayDateStr();
            $metaDatepickers.each(function() {
                var $dp = $(this);
                if (!$dp.attr('min')) {
                    $dp.attr('min', todayStr);
                }
                $dp.on('focus click', function() {
                    if (typeof this.showPicker === 'function') {
                        this.showPicker();
                    }
                }).on('change', function() {
                    var val = $.trim(this.value);
                    if (val && val < todayStr) {
                        this.value = '';
                        showAlert('error', 'Invalid Date', 'Please select a current or future date for Delivery Date.', 2500);
                    }
                });
            });
        }

        function validateDeliveryDateSelection() {
            var valid = true;
            var todayStr = getTodayDateStr();
            $('.order-meta-datepicker').each(function() {
                var val = $.trim($(this).val());
                if (val && val < todayStr) {
                    valid = false;
                    return false;
                }
            });
            return valid;
        }

        // Validate product selection on Update Order click and handle form submission
        $('#orderUpdateData').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if (!validateProductSelection()) {
                showAlert('error', 'Product Required', 'Please select a product before updating the order.', 2500);
                return;
            }

            if (!validateDeliveryDateSelection()) {
                showAlert('error', 'Invalid Date', 'Please select a current or future date for Delivery Date.', 2500);
                return;
            }

            runFooterAction($(this), '/api/order/update/', 'update', 'Order details updated successfully.');
        });

        $('#orderWaitingBtn').on('click', function(e) {
            if (!validateProductSelection()) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showAlert('error', 'Product Required', 'Please select a product before updating the order.', 2500);
            }
        });

        function runFooterAction($button, requestUrl, actionValue, successMessage) {
            if (!$button.length || $button.attr('data-processing') === '1') return;

            if (window.orderUiProcessing && typeof window.orderUiProcessing.setButtonProcessing === 'function') {
                window.orderUiProcessing.setButtonProcessing($button[0], 'Processing...');
            }

            var formData = $('#orderData').serializeArray();
            formData.push({ name: 'action', value: actionValue });

            $.ajax({
                type: 'POST',
                url: requestUrl,
                dataType: 'json',
                data: formData,
                success: function(res) {
                    if (res.status == 200 || res.status == 'success') {
                        showAlert('success', 'Success', res.msg || successMessage, 2000);
                        showFeedback('success', 'Success', res.msg || successMessage, 'Action completed successfully.');
                        setTimeout(function() { window.location.reload(); }, 2000);
                    } else {
                        showAlert('error', 'Error', res.msg || 'Something went wrong.', 2500);
                        showFeedback('error', 'Error', res.msg || 'Something went wrong.', 'Please review the order and try again.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error', 'Request failed.', 2500);
                    showFeedback('error', 'Error', 'Request failed.', 'The server did not accept the request.');
                },
                complete: function() {
                    if (window.orderUiProcessing && typeof window.orderUiProcessing.clearButtonProcessing === 'function') {
                        window.orderUiProcessing.clearButtonProcessing($button[0]);
                    }
                }
            });
        }

        $('#orderConfirmBtn').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            runFooterAction($(this), '/api/order/order_accept/', 'accept', 'Order accepted successfully.');
        });

        $('#orderCancelBtn').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var $btn = $(this);

            var proceed = function() {
                runFooterAction($btn, '/api/order/order_reject/', 'cancel', 'Order rejected successfully.');
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to reject this order?',
                    icon: 'warning',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, reject it!'
                }).then(function(result) {
                    if (result.value) proceed();
                });
            } else if (confirm('Do you want to reject this order?')) {
                proceed();
            }
        });

        // Listen for changes on product dropdowns
        $(document).on('change', '.select-product-dropdown, .selectJS', function() {
            updateProductDropdownOptions();
        });

        // Initial Select2 setup
        var $allSelects = $('.selectJS');
        if ($allSelects.length) {
            updateProductDropdownOptions();
            initSelect2($allSelects);
        }
    });
})(jQuery);
