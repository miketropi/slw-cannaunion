(()=>{var e={643:()=>{((e,t)=>{"use strict";const o=t=>{if(e.SLW_Store.country_code&&t.val(e.SLW_Store.country_code).trigger("change"),"private"==e.SLW_Store.access_store)t.prop("disabled",!0);else{const e=PHP_DATA?.product_locations?.filter((e=>"private"==e.access_store));if(!e)return;e.forEach((e=>{t.find(`option[value=${e.country_code}]`).prop("disabled",!0)}))}},r=t=>{if("private"==e.SLW_Store.access_store)t.prop("disabled",!1);else{const e=PHP_DATA?.product_locations?.filter((e=>"private"==e.access_store));if(!e)return;e.forEach((e=>{t.find(`option[value=${e.country_code}]`).prop("disabled",!1)}))}};document.addEventListener("DOMContentLoaded",(()=>{})),e.addEventListener("load",(()=>{(()=>{let e=document.querySelector("#ship-to-different-address-checkbox"),c=document.querySelector('form.woocommerce-checkout select[name="billing_country"]'),n=document.querySelector('form.woocommerce-checkout select[name="shipping_country"]');c&&(e&&e.addEventListener("change",(e=>{1==e.target.checked?(r(t(c)),o(t(n))):(n&&r(t(n)),o(t(c)))})),o(t(c)))})()}))})(window,jQuery)}},t={};function o(r){var c=t[r];if(void 0!==c)return c.exports;var n=t[r]={exports:{}};return e[r](n,n.exports,o),n.exports}o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";o(643),((e,t)=>{e.SLW_Store=null;const o=PHP_DATA.product_locations,{countryCode:r,country:c,timezone:n}=PHP_DATA.user_location,s=()=>{const o=["form.cart select.slw_item_stock_location"],r=document.querySelectorAll(o.join(","));r.length<=0||(t(o.join(",")).off("change").on("change",(function(e){(e=>{const o=t(e).parent(),r=t(e).find(`option[value="${e.value}"]`).text();o.find(".slwc-text").remove(),o.prepend(`<div class="slwc-text"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg> ${r}</div>`)})(this)})),r.forEach((o=>{o.classList.add("slwc-custom"),o.value=e.SLW_Store.term_id,t(o).trigger("change"),o.classList.add("slwc-field-disable-css","noselect")})))};e.addEventListener("DOMContentLoaded",(c=>{e.SLW_Store=o.find((e=>e.country_code==r))||o.find((e=>"global"==e.access_store)),s(),t(".variations_form").on("found_variation",(e=>{})),t(document).ajaxComplete(((e,t,o)=>{o?.data.startsWith("action=get_variation_locations")&&s()}))}))})(window,jQuery)})()})();
//# sourceMappingURL=main.slw-cannaunion.bundle.js.map