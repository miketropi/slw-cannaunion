/**
 * Main Javscript 
 */
import * as Helpers from './helpers';
import './checkout';
import './main.scss';

;((w, $) => {
  'use strict';
  w.SLW_Store = null;
  const AllStore = PHP_DATA.product_locations;
  const {countryCode, country, timezone} = PHP_DATA.user_location;
  
  const defineClientStoreValue = () => {
    const _found = AllStore.find(store => {
      return (store.country_code == countryCode)
    })

    return _found ? _found : AllStore.find(store => {
      return (store.access_store == 'global')
    })
  }

  const updateTextNearbyLocationSelect = (select) => {
    const $wrap = $(select).parent();
    const $opt = $(select).find(`option[value="${select.value}"]`);
    const text = $opt.length ? $opt.text() : `Product not available.`;
    const locationItem = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;

    $wrap.find('.slwc-text').remove();
    $wrap.prepend(`<div class="slwc-text">${locationItem} ${text}</div>`);
  }

  const validateQtyAfterLocalSelectUpdate = (select) => {
    const $select = $(select);
    const $form = $select.parents('form');
    const $opt = $select.find(`option[value="${select.value}"]`);

    if($opt.length === 0 || $opt[0].disabled === true) {
      $form
        .find('button[type=submit], input[name=quantity]')
        .addClass('slwc-field-disable-css');
    } else {
      $form
        .find('button[type=submit], input[name=quantity]')
        .removeClass('slwc-field-disable-css');
    }
  } 

  const storeSelected = () => {
    const selectQuery = ['form.cart select.slw_item_stock_location']; // Product page, 
    const selectFields = document.querySelectorAll(selectQuery.join(','));
    if(selectFields.length <= 0) {
      $('.single-product form.cart').addClass('slwc-field-disable-css');
      return;
    }; 

    // Add onChang event
    $(selectQuery.join(',')).off('change').on('change', function(e) {
      updateTextNearbyLocationSelect(this);
      validateQtyAfterLocalSelectUpdate(this);
    })

    selectFields.forEach(select => {
      select.classList.add('slwc-custom');
      select.value = w.SLW_Store.term_id;
      
      $(select).trigger('change'); // trigger onChange event to update something
      select.classList.add('slwc-field-disable-css', 'noselect'); // disable field
    })
  }

  const stockLocationSelectFieldUpdate = () => {
    $(document).ajaxComplete((event, request, settings) => {
      if(settings?.data.startsWith('action=get_variation_locations')) {
        storeSelected();
      }
    })
  }

  const productVariableUpdate = () => {
    $('.variations_form').on('found_variation', e => {
      // console.log('Mike log');
    });
  }

  const buttonAddToCart2 = () => {
    const Buttons = $('.button.product_type_simple.add_to_cart_button.ajax_add_to_cart');
    
    Buttons.each((_, b) => {
      b.classList.add('slwc-hidden');

      const product_id = parseInt(b.dataset.product_id);
      const isUnavailable = $(b).parents('.__slwc-product-unavailable').length;

      const ButtonClone = $(`<a class="button slwc-custom-button-ajax-add-to-cart-simple-product ${isUnavailable ? 'slwc-field-disable-css' : ''}" href="javascript:" data-product-id="${product_id}">${b.text}</a>`);
      $(b).before([isUnavailable ? `<small class="product-unavailable-tag">Unavailable for your location</small>` : '', ButtonClone]);
      b.href = `${b.href}&slw_add_to_cart_item_stock_location=${SLW_Store.term_id}`;
    })

    $(document.body).on('click', 'a.slwc-custom-button-ajax-add-to-cart-simple-product', function(e) {
      e.preventDefault();
      const $button = $(this);
      const product_id = parseInt($(this).data('product-id'));
      $button.addClass('loading');

      $.ajax({
        type: 'POST',
        url: '?wc-ajax=add_to_cart',
        data: {
          // product_id,
          'add-to-cart': product_id,
          // quantity: 1,
          slw_add_to_cart_item_stock_location: SLW_Store.term_id,
        },
        success() {
          $(document.body).trigger('wc_fragment_refresh');
          setTimeout(() => {
            $button.removeClass('loading');
          }, 1500)
        },
        error(e) {
          console.log(e)
        }
      })
    })
  }

  const outofstockHandle = () => {
    $('.product.type-product.outofstock').each(function() {
      if($(this).find('.product-unavailable-tag').length > 0) return;
      if($(this).hasClass(`__slwc-qty-${w.SLW_Store.term_id}_0`)) {
        $(this).append(`<small class="product-unavailable-tag">Out of stock</small>`);
      }
    })
  }

  const _Init = () => {
    w.SLW_Store = defineClientStoreValue(); // get Store
    storeSelected();
    productVariableUpdate();
    stockLocationSelectFieldUpdate();
    buttonAddToCart2();
    outofstockHandle(); 
  }

  w.addEventListener('DOMContentLoaded', e => {
    _Init();
  })
})(window, jQuery) 