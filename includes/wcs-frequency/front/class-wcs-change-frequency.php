<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WCS_Frequency')) {
    class WCS_Frequency
    {
        /**
         * @var WCS_Frequency $instance for class
         */
        public static $instance;
        const CHANGE_FREQUENCY_BUTTON_ID = 'change-frequency';

        /**
         * constructor for class Wc_Sub_Box_Frequency  every any action inside will be applied first when call instance from class
         */
        private function __construct()
        {
            // filter to add change frequency button to subscription view with priority 30
            add_filter('wcs_view_subscription_actions', array($this, 'add_change_frequency_button'), 30, 3);

        }

        /**
         * add change frequency button using @filter wcs_view_subscription_actions
         * check for subscription frequency enabled first
         * @param $actions
         * @param wc_Subscription $subscription
         * @param $user_id
         * @return mixed
         */
        public function add_change_frequency_button($actions, wc_Subscription $subscription, $user_id)
        {
                //check for subscription frequency enabled first
           if (!WCS_Frequency_Utility::is_wc_sub_box_frequency($subscription))
                return $actions;

           // add change frequency button through @var $actions array
            $actions[self::CHANGE_FREQUENCY_BUTTON_ID] = array(
                'url' => '#',
                'name' => __('Change frequency', 'wc-sub-frequency'),
            );

            return $actions;
        }

        /**
         * function to make instance from class to call constructor when calling class inside index file and implement actions-hooks registered in constructor inside constructor
         * @return WCS_Frequency
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

}
WCS_Frequency::get_instance();


