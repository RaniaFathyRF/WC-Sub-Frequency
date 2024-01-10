<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WC_Sub_Frequency_General_Settings')) {

    class WC_Sub_Frequency_General_Settings
    {
        /**
         * @var WC_Sub_Frequency_General_Settings
         */
        public static $instance;

        const SECTION_TITLE_ID = 'wcs_frequency_section';
        const SECTION_END_ID = 'wcs_frequency_section';
        const SECTION_TITLE_KEY = 'section_title';
        const SECTION_END_KEY = 'section_end';

        private function __construct()
        {
            // add wc sub box general settings
            add_filter('woocommerce_settings_tabs_array', array($this, 'wc_sub_box_add_settings_tab'), 99);
            add_action('woocommerce_settings_tabs_wcs_frequency_section', array($this, 'wc_sub_box_settings_tab_content'));
            add_action('woocommerce_update_options_wcs_frequency_section', array($this, 'wc_sub_box_update_settings'));

        }

        public function wc_sub_box_add_settings_tab($tabs)
        {
            $tabs['wcs_frequency_section'] = __('WC Sub Frequency', 'wc-sub-frequency');

            return $tabs;
        }

        public function wc_sub_box_settings_tab_content()
        {
            woocommerce_admin_fields($this->wc_sub_box_get_settings());
        }

        public function wc_sub_box_get_settings()
        {

            $settings[self::SECTION_TITLE_KEY] = array(
                'name' => __('WC Sub Frequency Settings', 'wc-sub-frequency'),
                'type' => 'title',
                'desc' => __('Configure your WC Sub Frequency settings below:', 'wc-sub-frequency'),
                'id' => self::SECTION_TITLE_ID
            );
            // add custom settings
            $settings = apply_filters('wc_sub_frequency_add_settings', $settings);

            $settings[self::SECTION_END_KEY] = array(
                'type' => 'sectionend',
                'id' => self::SECTION_END_ID
            );


            return $settings;
        }

        public function wc_sub_box_update_settings()
        {
            woocommerce_update_options($this->wc_sub_box_get_settings());
        }

        /**
         * @return WC_Sub_Frequency_General_Settings
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

}
WC_Sub_Frequency_General_Settings::get_instance();

