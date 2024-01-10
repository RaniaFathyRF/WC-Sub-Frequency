<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WCS_Frequency_Utility')) {

    class WCS_Frequency_Utility
    {
        /**
         *
         * @var WCS_Frequency_Utility
         */
        public static $instance;

        /**
         * constructor everything inside will be applied first when call instance from class
         */
        public function __construct()
        {

        }




        /**
         * Take subscription and check if it's active
         * @param WC_Subscription $subscription
         * @return bool
         */
        public static function is_active_subscription(WC_Subscription $subscription)
        {
            return $subscription->get_status() == 'active' ? true : false;
        }

        /**
         * take subscription and check if user can switch subscription
         * @param WC_Subscription $subscription
         * @return bool
         */
        public static function can_user_switch_subscription(WC_Subscription $subscription)
        {
            if (empty($subscription))
                return false;

            foreach ($subscription->get_items() as $item) {
                $can_be_switched = WC_Subscriptions_Switcher:: can_item_be_switched_by_user($item, $subscription);

                if ($can_be_switched)
                    return true;

            }
            return false;
        }

        /**
         * take subscription and check if subscription frequency is valid
         *
         * @param WC_Subscription$subscription
         * @return bool
         */
        public static function is_wc_sub_box_frequency( $subscription)
        {
            // check if subscription is empty
            if (empty($subscription))
                return false;

            // check if subscription frequency is enabled
            if (!WCS_Frequency_Settings::is_change_frequency_box_enabled_in_settings())
                return false;

            // check if user can switch subscription
            if (!self::can_user_switch_subscription($subscription))
                return false;

            // check if subscription is active
            if (!self::is_active_subscription($subscription))
                return false;

            if(!WC_Sub_Frequency_Utility::is_wcs_subscription($subscription))
                return false;

            return true;
        }

        /**
         * function to make instance from class to call constructor when calling class inside index file and implement actions-hooks registered in constructor inside constructor
         * @return WCS_Frequency_Utility
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

}
WCS_Frequency_Utility::get_instance();


