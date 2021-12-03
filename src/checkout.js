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
      // $countryField.prop('disabled', true);
      $countryField
        .next('.select2')
        .addClass('__non-select');
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
      // $countryField.prop('disabled', false);
      $countryField
        .next('.select2')
        .removeClass('__non-select');
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

  var disableBtnCheckout;
  const warrningDialogUpdateLocationPrivate = () => {
    let $shipDifferentAddressCheckbox = $('#ship-to-different-address-checkbox');
    let $countryField = $('form.woocommerce-checkout select[name="billing_country"]');
    let $countryField2 = $('form.woocommerce-checkout select[name="shipping_country"]');
    let $formCheckout = $('form.woocommerce-checkout');
    //console.log($formCheckout);
    if(!$countryField) return;
    const alertMessage = (countryCode) => {
      if(PHP_DATA.site_code === 'uk' && countryCode == 'GB') return;
      if(PHP_DATA.site_code === 'eu' && countryCode != 'GB') return;
      $.alert({
        title: '⚠️ Warning.',
        content: PHP_DATA.slwc_message_warning_checkout_page,
        useBootstrap: false,
        type: 'red',
      });
      $formCheckout.find('#place_order').prop('disabled', true);
      disableBtnCheckout = setInterval(disableBN, 1000);
    }

    // if(w.SLW_Store.country_code) {
    //   $countryField.val(w.SLW_Store.country_code).trigger('change');
    // }

    //if(w.SLW_Store.access_store !== 'private') return;

    $countryField.on('change', function(e) {
      //if($shipDifferentAddressCheckbox.prop('checked') == true) return;
      //alertMessage(this.value);
      $formCheckout.find('#place_order').prop('disabled', false);
    })

    $countryField2.on('change', function(e) {
      $formCheckout.find('#place_order').prop('disabled', false);
      alertMessage(this.value);
    });

    $formCheckout.submit(function(){
      if($shipDifferentAddressCheckbox.prop('checked') == true) return;
      alertMessage($countryField.val());
    });

    function disableBN() {
      if(!$(document).find('.blockOverlay').length){
        $formCheckout.find('#place_order').prop('disabled', true);
        clearInterval(disableBtnCheckout);
      }
    }


  }

  document.addEventListener('DOMContentLoaded', () => {
    //
  })

  w.addEventListener('load', () => {
    // setDefaultCountryField();

    // warrning message if location private
    warrningDialogUpdateLocationPrivate();
  })
})(window, jQuery)
