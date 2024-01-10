<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WCS_Frequency_Settings')) {

    class WCS_Frequency_Settings
    {
        /**
         * @var WCS_Frequency_Settings
         */
        public static $instance;

        /**
         * @const FREQUENCY_BOX_ID enable frequency box in settings id in @var $settings
         */
        const FREQUENCY_BOX_ID = 'wcs_enable_frequency';

        /**
         * @const FREQUENCY_Attribute_ID enable frequency attribute in settings id in @var $settings
         */
        const FREQUENCY_Attribute_ID = 'wcs_enable_frequency_attribute_box';

        /**
         * @const SCREEN_NAME screen name in Admin Dashboard
         */
        const SCREEN_NAME = 'woocommerce_page_wc-settings';
        /**
         * @const FREQUENCY_BOX_ENABLE_KEY key in @var $settings
         */
        const FREQUENCY_BOX_ENABLE_KEY = 'wcs_enable_frequency_box';

        /**
         * @const FREQUENCY_ATTRIBUTE_BOX_ENABLE_KEY key in @var $settings
         */
        const FREQUENCY_ATTRIBUTE_BOX_ENABLE_KEY = 'wcs_enable_frequency_attribute_box';

        /**
         * //todo: check which is correct
         * @action  admin_enqueue_scripts to enqueue wc-sub-box-modal-script
         * @hooked admin_enqueue_scripts to enqueue wc-sub-box-modal-script
         * @filter wc_sub_box_extra_actions_add_settings to modify @var $settings and add new button in settings wc_sub_box_extra_actions
         */
        private function __construct()
        {
            // action to enqueue admin  scripts
            add_action('admin_enqueue_scripts', array($this, 'wcs_enqueue_change_frequency_box'));

            // filter to modify @var $settings and add new  wc_sub_box_extra_actions buttons ...
            add_filter('wc_sub_frequency_add_settings', array($this, 'wcs_add_frequency_settings_options'), 25);

        }

        /** @hooked admin_enqueue_scripts to enqueue wc-sub-box-modal-script
         *  enqueue scripts to admin panel to hide frequency attribute when frequency box not checked
         * @return void
         */
        public static function wcs_enqueue_change_frequency_box()
        {
            // get current screen
            $current_screen = get_current_screen()->id;

              // check if current screen is not equal to @const SCREEN_NAME
            if ($current_screen != self::SCREEN_NAME)
                return;

            // enqueue style
            wp_enqueue_style('wcs_frequency_actions', WCS_FREQUENCY_URL . 'assets/css/admin/wcs-frequency.css', false, WCS_FREQUENCY_ASSETS_VERSION);
            // enqueue script
            wp_enqueue_script('wcs_frequency_actions', WCS_FREQUENCY_URL . 'assets/js/admin/wcs-frequency.js', array('jquery'), WCS_FREQUENCY_ASSETS_VERSION);

        }

        /**
         * @filter wc_sub_box_extra_actions_add_settings callback to add new button in settings
         * @param $settings
         * @return mixed
         */
        public function wcs_add_frequency_settings_options($settings)
        {
            // add to settings input id with its name (label) and default value if it's not set also with input type
            $settings[self::FREQUENCY_BOX_ENABLE_KEY] = array(
                'name' => __('Enable Change Frequency Feature (wcs)', 'wc-sub-frequency'),
                'id' => self::FREQUENCY_BOX_ID,
                'type' => 'checkbox',
                'default' => 'no'
            );

            // FREQUENCY_ATTRIBUTE_BOX_ENABLE_KEY = 'enable_frequency_attribute_box' key in @var $settings
            $settings[self::FREQUENCY_ATTRIBUTE_BOX_ENABLE_KEY] = array(
                'name' => __('Enable Frequency attribute Box Feature', 'wc-sub-frequency'),
                'id' => self::FREQUENCY_Attribute_ID,
                'type' => 'text',
            );
            return $settings;
        }

        /**
         * check if frequency box is enabled in settings
         * @return bool
         */
        public static function is_change_frequency_box_enabled_in_settings()
        {
            return WC_Sub_Frequency_Utility::wc_sub_box_get_settings_options(self::FREQUENCY_BOX_ID) != 'no'? true : false;
        }

        /**
         * get frequency attribute option text field by pass to @function wc_sub_box_get_settings_options frequency attribute id
         * @return false|mixed|string
         *
         */
        public static function get_frequency_attribute_option()
        {
            return WC_Sub_Frequency_Utility::wc_sub_box_get_settings_options(self::FREQUENCY_Attribute_ID) ?? '';

        }

        /**
         *  Make instance from class to call constructor when calling class inside index file and implement actions-hooks registered in constructor inside constructor
         * @return WCS_Frequency_Settings
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

}
WCS_Frequency_Settings::get_instance();


