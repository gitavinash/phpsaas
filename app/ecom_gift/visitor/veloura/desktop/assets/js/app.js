/**
 * Veloura Atelier Storefront Interactions & AJAX Workflows
 * Inspired by Fiore & Fiorello Luxury E-Commerce
 */

(function () {
  'use strict';

  let isMainProductAddedToCart = false;

  /**
   * Veloura Luxury SweetAlert Toast Notification
   * Shows subtle bottom-right toast and auto-hides after 1.8 seconds.
   * Completely avoids intrusive full-screen "Added to Your Bag" popups.
   */
  window.vlShowCartToast = function (payload) {
    if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
      return;
    }

    const opts = typeof payload === 'string' ? { heading: payload } : (payload || {});
    const heading = String(opts.heading || 'Added to Floral Bag').trim();
    const productName = String(opts.productName || '').trim();
    const qty = parseInt(opts.qty, 10) || 1;
    const imageUrl = String(opts.image || '').trim();

    const contentWrap = document.createElement('div');
    contentWrap.className = 'flex items-center gap-3 text-left';

    if (imageUrl) {
      const thumb = document.createElement('img');
      thumb.className = 'w-12 h-12 rounded-lg object-cover border border-[#E8DDD5] flex-shrink-0';
      thumb.src = imageUrl;
      thumb.alt = '';
      contentWrap.appendChild(thumb);
    }

    const textCol = document.createElement('div');
    textCol.className = 'flex-1 min-w-0';

    const titleEl = document.createElement('p');
    titleEl.className = 'text-xs uppercase tracking-wider font-semibold text-[#C47C68]';
    titleEl.textContent = heading;
    textCol.appendChild(titleEl);

    if (productName) {
      const subEl = document.createElement('p');
      subEl.className = 'font-serif text-sm text-[#242220] truncate font-medium';
      subEl.textContent = (qty > 1 ? qty + ' × ' : '') + productName;
      textCol.appendChild(subEl);
    }

    contentWrap.appendChild(textCol);

    const bagLink = document.createElement('a');
    bagLink.href = '/checkout/';
    bagLink.className = 'text-xs font-semibold uppercase tracking-wider text-[#242220] hover:text-[#C47C68] underline ml-2 whitespace-nowrap';
    bagLink.textContent = 'View Bag';
    contentWrap.appendChild(bagLink);

    Swal.fire({
      toast: true,
      position: 'bottom-end',
      icon: 'success',
      html: contentWrap,
      showConfirmButton: false,
      timer: 1800,
      timerProgressBar: true,
      customClass: {
        popup: 'vl-swal-toast',
      },
      didOpen: function (toast) {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      },
    });
  };

  /**
   * Helper to update header cart badges and mini-bag
   */
  window.vlUpdateCartBadges = function (delta) {
    const step = parseInt(delta, 10) || 1;
    const $cartItems = $('#cartItems');
    if ($cartItems.length) {
      const current = parseInt($cartItems.text().trim(), 10) || 0;
      const nextCount = Math.max(0, current + step);
      $cartItems.text(nextCount);
      if (nextCount > 0) {
        $cartItems.removeClass('bg-[#D5C4B7] text-[#635E59] bg-[#FAF7F5] text-[#6E675F]').addClass('bg-[#C47C68] text-white');
      } else {
        $cartItems.removeClass('bg-[#C47C68] text-white').addClass('bg-[#D5C4B7] text-[#635E59]');
      }
      $cartItems.removeClass('is-bumped');
      if ($cartItems[0]) {
        void $cartItems[0].offsetWidth;
        $cartItems.addClass('is-bumped');
        setTimeout(function () {
          $cartItems.removeClass('is-bumped');
        }, 450);
      }
      const $statusEl = $('#vlCartDropdownStatus');
      if ($statusEl.length && nextCount > 0) {
        $statusEl.html('<i class="ri-checkbox-circle-line text-[#3D5447] mr-1" aria-hidden="true"></i> You have <strong>' + nextCount + '</strong> curated items ready for atelier delivery.');
      }
    }
  };

  $(document).ready(function () {
    /**
     * Single Product & Workshop "Add to Bag" Handler
     */
    $('#addToCart').on('click', function (e) {
      e.preventDefault();
      const $btn = $(this);
      if ($btn.hasClass('is-loading')) return;

      const $info = $('#productInfo');
      let id = $info.data('product-id') || $info.attr('data-product-id');
      let price = $info.data('unit-price') || $info.attr('data-unit-price');
      let itemType = $info.data('item-type') || $info.attr('data-item-type') || $btn.data('item-type') || 'product';

      // Fallback for value="id_price_qty" format or legacy #qty
      if (!id) {
        let rawInfo = $info.attr('value') || $info.val() || $('#qty').val() || '';
        if (rawInfo) {
          const parts = rawInfo.split('_');
          id = parts[0];
          price = parts[1];
        }
      }

      if (!id) return;

      const qty = parseInt($('#quantity').val(), 10) || parseInt($('#qtyInput').val(), 10) || 1;
      const isEvent = itemType === 'event' || $btn.data('item-type') === 'event' || $info.data('item-type') === 'event' || window.location.pathname.indexOf('/workshop-events/') !== -1 || window.location.pathname.indexOf('/event/') !== -1;

      $btn.addClass('is-loading opacity-75');

      $.ajax({
        type: 'POST',
        url: '/api/ecom_gift/cart/',
        dataType: 'json',
        data: {
          id: id,
          quantity: qty,
          price: price,
          itemType: itemType,
          type: 'cart',
          action: 'add',
        },
        success: function (data) {
          $btn.removeClass('is-loading opacity-75');
          if (data && (data.status === 200 || data.status === '200' || data.success)) {
            vlUpdateCartBadges(qty);

            if (isEvent) {
              window.location.href = '/checkout/';
            } else {
              vlShowCartToast({
                heading: 'Added to Floral Bag',
                productName: data.productName || $('.font-serif.text-3xl').first().text(),
                image: data.productImage || $('#mainProductImage').attr('src'),
                qty: qty,
              });
            }
          } else {
            vlShowCartToast({
              heading: 'Notice',
              productName: (data && data.message) ? data.message : 'Item added to bag',
              qty: qty,
            });
          }
        },
        error: function () {
          $btn.removeClass('is-loading opacity-75');
          // Fallback optimistic toast
          vlUpdateCartBadges(qty);
          if (isEvent) {
            window.location.href = '/checkout/';
          } else {
            vlShowCartToast({
              heading: 'Added to Floral Bag',
              productName: $('.font-serif.text-3xl').first().text(),
              qty: qty,
            });
          }
        },
      });
    });

    /**
     * Grid Product Cards Quick Add to Bag
     */
    $(document).on('click', '.quickAddToCart', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const $btn = $(this);
      const dataInfo = $btn.attr('data-info');
      if (!dataInfo) return;

      const parts = dataInfo.split('_');
      const id = parts[0];
      const price = parts[1];
      const qty = parseInt(parts[2], 10) || 1;
      const $card = $btn.closest('.vl-product-card');
      const cardTitle = $card.find('.vl-product-title').text().trim();
      const cardImg = $card.find('.vl-product-img').attr('src');

      $btn.prop('disabled', true).addClass('opacity-75');

      $.ajax({
        type: 'POST',
        url: '/api/ecom_gift/cart/',
        dataType: 'json',
        data: {
          id: id,
          quantity: qty,
          price: price,
          type: 'cart',
          action: 'add',
        },
        success: function (data) {
          $btn.prop('disabled', false).removeClass('opacity-75');
          vlUpdateCartBadges(qty);
          vlShowCartToast({
            heading: 'Added to Floral Bag',
            productName: (data && data.productName) ? data.productName : cardTitle,
            image: (data && data.productImage) ? data.productImage : cardImg,
            qty: qty,
          });
        },
        error: function () {
          $btn.prop('disabled', false).removeClass('opacity-75');
          vlUpdateCartBadges(qty);
          vlShowCartToast({
            heading: 'Added to Floral Bag',
            productName: cardTitle,
            image: cardImg,
            qty: qty,
          });
        },
      });
    });

    /**
     * Quantity Stepper Controls
     */
    $('#qtyMinus').on('click', function () {
      const $input = $('#qtyInput');
      let val = parseInt($input.val(), 10) || 1;
      if (val > 1) {
        val -= 1;
        $input.val(val);
        updateProductInfoQty(val);
      }
    });

    $('#qtyPlus').on('click', function () {
      const $input = $('#qtyInput');
      let val = parseInt($input.val(), 10) || 1;
      if (val < 20) {
        val += 1;
        $input.val(val);
        updateProductInfoQty(val);
      }
    });

    function updateProductInfoQty(newQty) {
      const $prodInfo = $('#productInfo');
      if ($prodInfo.length) {
        const val = $prodInfo.attr('value') || '';
        const parts = val.split('_');
        if (parts.length >= 2) {
          $prodInfo.attr('value', parts[0] + '_' + parts[1] + '_' + newQty);
        }
      }
      const $qtyInput = $('#qty');
      if ($qtyInput.length) {
        const val = $qtyInput.val() || '';
        const parts = val.split('_');
        if (parts.length >= 2) {
          $qtyInput.val(parts[0] + '_' + newQty + '_' + (parts[2] || ''));
        }
      }
    }

    /**
     * Product Variation Pills
     */
    $('.pro-var-pill').on('click', function () {
      const $pill = $(this);
      $('.pro-var-pill').removeClass('is-active border-[#C47C68] text-[#C47C68] bg-[#FAF7F5]').addClass('border-[#E8DDD5] text-[#242220]');
      $pill.addClass('is-active border-[#C47C68] text-[#C47C68] bg-[#FAF7F5]').removeClass('border-[#E8DDD5] text-[#242220]');

      const price = parseFloat($pill.data('price')) || 0;
      const mrp = parseFloat($pill.data('mrp')) || 0;
      const id = $pill.data('id');
      const qty = parseInt($('#qtyInput').val(), 10) || 1;

      if (price > 0) {
        $('#effectivePriceDisplay').text('₹' + price.toLocaleString('en-IN'));
      }
      if (mrp > price) {
        $('#mrpDisplay').text('₹' + mrp.toLocaleString('en-IN')).removeClass('hidden');
      } else {
        $('#mrpDisplay').addClass('hidden');
      }

      if (id) {
        $('#productInfo').attr('value', id + '_' + price + '_' + qty);
        $('#qty').val(id + '_' + qty + '_' + price);
      }
    });

    /**
     * Fiorello Category Filter Tabs (Homepage Grid Home)
     */
    $('.vl-filter-nav button[data-filter]').on('click', function () {
      const $tab = $(this);
      const filter = $tab.data('filter');

      $('.vl-filter-nav button[data-filter]').removeClass('is-active');
      $tab.addClass('is-active');

      const $cards = $('.vl-product-card');
      if (filter === 'all' || !filter) {
        $cards.removeClass('hidden').css({ opacity: 0, transform: 'scale(0.96)' }).animate({ opacity: 1 }, {
          duration: 300,
          step: function (now) {
            const scale = 0.96 + 0.04 * now;
            $(this).css('transform', 'scale(' + scale + ')');
          }
        });
      } else {
        $cards.each(function () {
          const $card = $(this);
          const cat = ($card.attr('data-category') || '').toLowerCase();
          if (cat.indexOf(filter.toLowerCase()) !== -1) {
            $card.removeClass('hidden').css({ opacity: 0, transform: 'scale(0.96)' }).animate({ opacity: 1 }, {
              duration: 300,
              step: function (now) {
                const scale = 0.96 + 0.04 * now;
                $(this).css('transform', 'scale(' + scale + ')');
              }
            });
          } else {
            $card.addClass('hidden');
          }
        });
      }
    });

    /**
     * Auto-submit sort dropdown without inline onchange
     */
    $('[data-auto-submit]').on('change', function () {
      const $select = $(this);
      const sortVal = $select.val();
      const url = new URL(window.location.href);
      if (sortVal) {
        url.searchParams.set('sort', sortVal);
      } else {
        url.searchParams.delete('sort');
      }
      url.searchParams.delete('page');
      window.location.href = url.toString();
    });

    /**
     * Sticky Mobile Bar Triggers
     */
    $('[data-checkout-trigger]').on('click', function () {
      const target = $(this).data('target-btn');
      if (target && $(target).is(':visible')) {
        $(target).trigger('click');
      } else if ($('#continueShipping').is(':visible')) {
        $('#continueShipping').trigger('click');
      } else if ($('#continueCheckout').is(':visible')) {
        $('#continueCheckout').trigger('click');
      } else {
        $('#placeOrder').trigger('click');
      }
    });

    $('[data-trigger-payment]').on('click', function () {
      $('#continueToPayment').trigger('click');
    });

    $('#mobileAddToCart').on('click', function () {
      $('#addToCart').trigger('click');
    });

    /**
     * Gallery Thumbnail Switcher
     */
    $(document).on('click', '.thumb-btn', function () {
      const $btn = $(this);
      const imgSrc = $btn.data('img-src');
      if (imgSrc) {
        $('#mainProductImage').attr('src', imgSrc);
        $('.thumb-btn').removeClass('ring-2 ring-[#C47C68]');
        $btn.addClass('ring-2 ring-[#C47C68]');
      }
    });
  });
})();
