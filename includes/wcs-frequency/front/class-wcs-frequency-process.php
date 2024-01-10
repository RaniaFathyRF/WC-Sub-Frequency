<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WCS_Frequency_Process')) {
    class WCS_Frequency_Process
    {

        /**
         * @var WCS_Frequency_Process
         */
        public static $instance;

        public function __construct()
        {
            // add action wp_enqueue_scripts to enqueue front script
            add_action('wp_enqueue_scripts', array($this, 'enqueue_change_frequency_box'), 25);
            // filter to display loader
            add_filter('wc_sub_box_extra_action_display_loader_html', array($this, 'wcs_sub_box_change_frequency_display_loader'), 25);
            //hook to include template file and send all variations key value pair to new template file
            add_action('woocommerce_subscription_after_actions', array($this, 'wcs_change_frequency_modal'), 25);
            // hook to call change frequency ajax call to change frequency of subscription
            add_action('wp_ajax_wcs_switch_subscription', array($this, 'wcs_switch_subscription'));
        }


        /**
         * enqueue micro-modal scripts (css-js) from general utility class @class WC_Sub_Frequency_Utility
         * enqueue micro-modal scripts custom js file @file wc-sub-box-modal-script.js
         * localize ajax url for ajax call @return void
         */
        public static function enqueue_change_frequency_box()
        {

            // check if user not logged in and current screen not view subscription then don't include theses scripts
            if (!wcs_is_view_subscription_page())
                return;
            //enqueue main micro-modal scripts
            WC_Sub_Frequency_Utility::enqueue_micromodal_scripts();

            //enqueue custom micro-modal scripts
            wp_enqueue_script('wcs-sub-box-modal-script', WCS_FREQUENCY_URL . 'assets/js/front/wcs-frequency-modal.js', array('jquery', 'wc_sub_box_extra_actions-micromodal'), WCS_FREQUENCY_ASSETS_VERSION);

            //enqueue custom micro-modal style
            wp_enqueue_style('wcs-sub-box-modal-style', WCS_FREQUENCY_URL . 'assets/css/front/wcs-frequency-modal.css', false, WCS_FREQUENCY_ASSETS_VERSION);

            //localize ajax url
            wp_localize_script('wcs-sub-box-modal-script', 'wcs_frequency_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
            ));

        }

        /**
         * function to display loader
         * @param bool $display_loader
         * @return bool|mixed
         */
        public static function wcs_sub_box_change_frequency_display_loader($display_loader = false)
        {
            //   check if frequency is enabled in settings
            if (WCS_Frequency_Settings::is_change_frequency_box_enabled_in_settings())
                return true;


            return $display_loader;
        }

        /**
         * check if subscription frequency enabled then send all variations key value pair to new template file
         * @param WC_Subscription $subscription
         * @return void
         */
        public static function wcs_change_frequency_modal($subscription)
        {
            if (!wcs_is_view_subscription_page())
                return;

            if (!WCS_Frequency_Utility::is_wc_sub_box_frequency($subscription))
                return;

            if (empty($subscription->get_items()))
                return;
            // get attribute name from settings
            $attribute_name = WCS_Frequency_Settings::get_frequency_attribute_option();
            $selected_variation_id = 0;
            // to send it to template file and use it as a hidden input
            $subscription_id = $subscription->get_id();

            $found_item_id = null; // Initialize the variable to store the item_id
            $found_product_id = null; // Initialize the variable to store the item_id
            foreach ($subscription->get_items() as $item_id => $item) {
                if (!$item->get_product()->is_type('subscription_variation'))
                    continue;
                // subscription  selected variation id
                $subscription_selected_variation_id = $item->get_variation_id();
                // this section to get product variations
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                if (empty($product))
                    continue;
                $variations = $product->get_available_variations();
                if (empty($variations))
                    continue;
                // get  variations options key value pair to use it in select input
                $attributes = [];
                foreach ($variations as $variation) {
                    $variation_id = $variation['variation_id'];
                    foreach ($variation['attributes'] as $key => $value) {
                        // this check if attribute name match with settings if match push attribute key value pair variation id with variation name
                        if (!empty($attribute_name) && $key != 'attribute_' . $attribute_name)
                            continue;
                        $attributes[$variation_id] = $value; // make custom array key pair [variation_id=>variation_name].
                        $found_item_id = $item_id;
                        $found_product_id = $item['product_id'];
                    }
                }
                // send data to template file
                include WCS_FREQUENCY_PATH . "template/wcs-sub-box-frequency-modal.php";
            }
        }


        /**
         * validate @param $post_data
         * @return bool
         */
        private static function wcs_sub_box_check_change_frequency_validations($post_data): bool
        {
            // check if post data is not is empty then return
            if (!isset($post_data['subscription_id']))
                return false;

            // check if item_id is not empty
            if (!isset($post_data['item_id']))
                return false;

            // check if new_variation_name is not empty
            if (!isset($post_data['new_variation_name']))
                return false;

            // check if old_variation_name is not empty
            if (!isset($post_data['old_variation_name']))
                return false;

            // check if variation_id is not empty
            if (!isset($post_data['variation_id']))
                return false;

            return true;
        }

        /**
         * first condition check sub_box frequency is enabled , user can switch frequency  and subscription is active.
         * check if order is not empty
         * check if variation is not empty
         * @param WC_Subscription $subscription
         * @param $order
         * @param $variations
         * @return bool
         */
        private static function wcs_sub_box_switch_subscription_validation(WC_Subscription $subscription, $order, $variations): bool
        {
            // check sub_box frequency is enabled , user can switch frequency  and subscription is active.
            if (!WCS_Frequency_Utility::is_wc_sub_box_frequency($subscription))
                return false;

            // check if order is not empty
            if (empty($order))
                return false;

            // check if variation is not empty
            if (empty($variations))
                return false;

            return true;
        }

        /**
         * function to implement ajax call event  to change frequency of subscription
         * function use @hooked  wp_ajax_wc_switch_subscription for ajax call
         * @return void
         */
        public static function wcs_switch_subscription()
        {
            // check if sub_box frequency is enabled , user can switch frequency  and subscription is active.
            if (!self::wcs_sub_box_check_change_frequency_validations($_POST))
                return;

            // get subscription
            $subscription_id = $_POST['subscription_id'];
            // get item id
            $item_id = $_POST['item_id'];
            $subscription = wcs_get_subscription($subscription_id);
            $old_item = wcs_get_order_item($item_id, $subscription);

            // get subscription order.
            $order = wc_get_order($subscription->get_parent_id());

            // get old and new variation name.
            $new_variation_name = $_POST['new_variation_name'];

            // get old and old variation name.
            $old_variation_name = $_POST['old_variation_name'];

            // get chosen variation id and get variation object from id.
            $variation_id = $_POST['variation_id'];
            $variation = wc_get_product($variation_id);

            // check if subscription is valid and check if order and variation is not empty
            if (!self::wcs_sub_box_switch_subscription_validation($subscription, $order, $variation))
                return;

            // remove old item from subscription.
            $subscription->remove_item($item_id);

            // add new item to subscription with new variation.
            $new_item_id = $subscription->add_product($variation, 1, array('variation_id' => $variation_id));

            // set billing period for subscription ex: (month,year,week).
            $subscription->set_billing_period(WC_Subscriptions_Product::get_period($variation));

            // set billing interval for subscription  ex: (1,2,3).
            $subscription->set_billing_interval(WC_Subscriptions_Product::get_interval($variation));

            // recalculate total for subscription then save.
            $subscription->calculate_totals();
            $subscription->save();

            // add order note.
            $order->add_order_note("Subscription switched from $old_variation_name to $new_variation_name"); // Add order note to the order

            $response_data = array(
                'status' => 'success',
                'message' => 'Subscription switched successfully!',
                'success' => 'success',
            );

            wp_send_json($response_data);
        }


        /**
         * Make instance from class to call constructor when calling class inside index file and implement actions-hooks registered in constructor inside constructor
         * @return WCS_Frequency_Process
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

}

WCS_Frequency_Process::get_instance();


