<?php 
use Carbon_Fields\Container;
use Carbon_Fields\Field;
/**
 * Options 
 */

function slwc_location_term_meta() {
  Container::make('term_meta', __('countryCode', 'slwc'))
    ->where('term_taxonomy', '=', 'location')
    ->add_fields([
      Field::make('select', 'slwc_country_code', __('countryCode', 'slwc'))
        ->add_options('slwc_country_code_options'),
      Field::make('checkbox', 'slwc_only_local', __('Only Local', 'slwc')),
    ]);
}
add_action('carbon_fields_register_fields', 'slwc_location_term_meta');