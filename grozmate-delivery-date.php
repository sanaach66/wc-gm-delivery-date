<?php
/**
 * Plugin Name: Groz Mate Delivery Date
 * Description: Adds a simple delivery date field with fixed time (2 PM – 6 PM) to WooCommerce checkout.
 * Version: 1.0
 * Author: Sana Ullah
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add Delivery Date Field
 */
add_action('woocommerce_after_order_notes', 'gm_add_delivery_date_field');

function gm_add_delivery_date_field($checkout) {

    echo '<div id="gm_delivery_date"><h3>Delivery Information</h3>';

    woocommerce_form_field('delivery_date', array(
    'type'        => 'date',
    'class'       => array('form-row-wide'),
    'label'       => 'Select Delivery Date',
    'required'    => true,
    'custom_attributes' => array(
        'min' => date('Y-m-d') // Allow today
    )
), $checkout->get_value('delivery_date'));

    echo '<p><strong>Delivery Time:</strong> 2:00 PM – 6:00 PM</p>';

    echo '</div>';
}


/**
 * Validate Delivery Date
 */
add_action('woocommerce_checkout_process', 'gm_validate_delivery_date');

function gm_validate_delivery_date() {

    if ( empty($_POST['delivery_date']) ) {
        wc_add_notice( __('Please select a delivery date.'), 'error' );
    }

    if ( ! empty($_POST['delivery_date']) ) {

        $selected_date = strtotime($_POST['delivery_date']);
        $today = strtotime('today');

        if ( $selected_date < $today ) {
            wc_add_notice( __('Delivery date cannot be in the past.'), 'error' );
        }
    }
}


/**
 * Save Delivery Date to Order
 */
add_action('woocommerce_checkout_create_order', 'gm_save_delivery_date');

function gm_save_delivery_date($order) {

    if ( ! empty($_POST['delivery_date']) ) {

        $order->update_meta_data('Delivery Date', sanitize_text_field($_POST['delivery_date']));
        $order->update_meta_data('Delivery Time', '2:00 PM – 6:00 PM');
    }
}


/**
 * Show in Admin Order Page
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'gm_show_delivery_date_admin', 10, 1);

function gm_show_delivery_date_admin($order){

    $delivery_date = $order->get_meta('Delivery Date');

    if ($delivery_date) {
        echo '<p><strong>Delivery Date:</strong> ' . esc_html($delivery_date) . '</p>';
        echo '<p><strong>Delivery Time:</strong> 2:00 PM – 6:00 PM</p>';
    }
}


/**
 * Show in Customer Emails
 */
add_action('woocommerce_email_order_meta', 'gm_show_delivery_date_email', 10, 3);

function gm_show_delivery_date_email($order, $sent_to_admin, $plain_text) {

    $delivery_date = $order->get_meta('Delivery Date');

    if ($delivery_date) {

        echo '<p><strong>Delivery Date:</strong> ' . esc_html($delivery_date) . '</p>';
        echo '<p><strong>Delivery Time:</strong> 2:00 PM – 6:00 PM</p>';
    }
}