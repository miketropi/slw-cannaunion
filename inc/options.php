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
      Field::make('select', 'slwc_store_access', __('Store Access'))
        ->set_options([
          '' => __('???', 'slwc'),
          'global' => __('Global', 'slwc'),
          'private' => __('Private', 'slwc'),
        ])
        ->set_default_value('')
        ->set_help_text(__('Select access store', 'slwc')),
      Field::make('select', 'slwc_country_code', __('countryCode', 'slwc'))
        ->add_options('slwc_country_code_options')
        ->set_help_text(__('Select country store', 'slwc'))
        ->set_conditional_logic([
          [
            'field' => 'slwc_store_access',
            'value' => 'private'
          ]
        ]),
    ]);
}
add_action('carbon_fields_register_fields', 'slwc_location_term_meta');