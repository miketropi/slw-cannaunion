(()=>{"use strict";((t,o)=>{console.log(PHP_DATA),t.SLW_Store=null;const e=PHP_DATA.product_locations,{countryCode:n,country:s,timezone:c}=PHP_DATA.user_location,a=()=>{const e=["form.cart select.slw_item_stock_location"],n=document.querySelectorAll(e.join(","));n.length<=0||(o(e.join(",")).off("change").on("change",(function(t){(t=>{const e=o(t).parent(),n=o(t).find(`option[value="${t.value}"]`).text();e.find(".slwc-text").remove(),e.prepend(`<div class="slwc-text"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg> ${n}</div>`)})(this)})),n.forEach((e=>{e.classList.add("slwc-custom"),e.value=t.SLW_Store.term_id,o(e).trigger("change"),e.classList.add("slwc-field-disable-css","noselect")})))};t.addEventListener("DOMContentLoaded",(s=>{t.SLW_Store=e.find((t=>t.country_code==n))||e.find((t=>"global"==t.access_store)),a(),o(".variations_form").on("found_variation",(t=>{})),o(document).ajaxComplete(((t,o,e)=>{e?.data.startsWith("action=get_variation_locations")&&a()}))}))})(window,jQuery)})();