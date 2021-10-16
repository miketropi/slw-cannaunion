/**
 * Checkout 
 * 
 */

;((w, $) => {
  'use strict';

  const setCountryFieldHandle = ($countryField) => {
    // Change to country by Client IP
    if(w.SLW_Store.country_code) {
      $countryField.val(w.SLW_Store.country_code).trigger('change');
    }

    // Disable field if store is private
    if(w.SLW_Store.access_store == 'private') {
      $countryField.prop('disabled', true);
    } else {
      const privateStores = PHP_DATA?.product_locations?.filter(store => {
        return store.access_store == 'private';
      })

      if(!privateStores) return;

      privateStores.forEach(item => {
        $countryField.find(`option[value=${item.country_code}]`).prop('disabled', true);
      });

      // $countryField.select2('refresh')
    }
  }

  const resetCountryField = ($countryField) => {
    if(w.SLW_Store.access_store == 'private') {
      $countryField.prop('disabled', false);
    } else {
      const privateStores = PHP_DATA?.product_locations?.filter(store => {
        return store.access_store == 'private';
      })

      if(!privateStores) return;

      privateStores.forEach(item => {
        $countryField.find(`option[value=${item.country_code}]`).prop('disabled', false);
      });

      // $countryField.select2('refresh')
    }
  }

  const setDefaultCountryField = () => {
    let shipDifferentAddressCheckbox = document.querySelector('#ship-to-different-address-checkbox');
    let countryField = document.querySelector('form.woocommerce-checkout select[name="billing_country"]');
    let countryField2 = document.querySelector('form.woocommerce-checkout select[name="shipping_country"]');
    if(!countryField) return;

    if(shipDifferentAddressCheckbox) {
      shipDifferentAddressCheckbox.addEventListener('change', e => {
        if(e.target.checked == true) {
          // custom shipping address
          resetCountryField($(countryField)); // reset
          setCountryFieldHandle($(countryField2)); // apply validate Country
        } else {
          // billing address
          if(countryField2) {
            resetCountryField($(countryField2)); // reset
          }
          setCountryFieldHandle($(countryField)); // apply validate Country
        }
      })
    }

    setCountryFieldHandle($(countryField));
  }

  document.addEventListener('DOMContentLoaded', () => {
    //
  })

  w.addEventListener('load', () => {
    setDefaultCountryField();
  })
})(window, jQuery) 