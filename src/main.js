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
    const text = $(select).find(`option[value="${select.value}"]`).text();
    const locationItem = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;

    $wrap.find('.slwc-text').remove();
    $wrap.prepend(`<div class="slwc-text">${locationItem} ${text}</div>`);
  }

  const validateQtyAfterLocalSelectUpdate = (select) => {
    
  }

  const storeSelected = () => {
    const selectQuery = ['form.cart select.slw_item_stock_location']; // Product page, 
    const selectFields = document.querySelectorAll(selectQuery.join(','));
    if(selectFields.length <= 0) return; 

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

  const _Init = () => {
    w.SLW_Store = defineClientStoreValue(); // get Store
    storeSelected();
    productVariableUpdate();
    stockLocationSelectFieldUpdate();
  }

  w.addEventListener('DOMContentLoaded', e => {
    _Init();
  })
})(window, jQuery) 