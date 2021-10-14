/**
 * Main Javscript 
 */
import * as Helpers from './helpers';
import './main.scss';

;((w, $) => {
  'use strict';
  const AllStore = PHP_DATA.product_locations;
  const {countryCode, country, timezone} = PHP_DATA.user_location;
  
  const defineClientStoreValue = () => {
    const _found = AllStore.find(store => {
      return (store.country_code == countryCode)
    })

    console.log(_found);
  }

  const storeSelected = () => {
    const select = document.querySelectorAll('select.slw_item_stock_location');
    console.log(select);
    if(select.length <= 0) return; 
    defineClientStoreValue();
  }

  const _Init = () => {
    console.log(PHP_DATA);
    storeSelected();
  }

  w.addEventListener('DOMContentLoaded', e => {
    _Init();
  })
})(window, jQuery) 