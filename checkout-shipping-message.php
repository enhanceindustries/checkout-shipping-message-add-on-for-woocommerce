<?php
/**
 * Plugin Name: Checkout Shipping Message Add-on for WooCommerce
 * Plugin URI: https://github.com/enhanceindustries/checkout-shipping-message-add-on-for-woocommerce
 * Description: Add notes on Shipping section for custom text
 * Has been tested on WooCommerce 3.8.1
 * Author: Enhance Industries
 * Author URI: https://www.enhanceindustries.com/
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/** Styles **/
function enhanceindustries_csm_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'shipping-note-css', $plugin_url . 'assets/css/shipping-note.css' );
}
add_action( 'wp_enqueue_scripts', 'enhanceindustries_csm_load_plugin_css' );

/**
*  Override cart-shipping on woocommerce to insert the custom shipping note
**/
function enhanceindustries_csm_locate_template( $template, $template_name, $template_path ) {
	$basename = basename( $template );
	if( $basename == 'cart-shipping.php' && is_checkout() ) {
		$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/cart-shipping.php';
	}
	return $template;
}
add_filter( 'woocommerce_locate_template', 'enhanceindustries_csm_locate_template', 10, 3 );

function enhanceindustries_csm_add_shipping_notice( $settings_tab ) {
	$settings_tab['enhanceindustries_shipping_notes'] = __( 'Shipping Note' );
	return $settings_tab;
}
add_filter( 'woocommerce_get_sections_shipping', 'enhanceindustries_csm_add_shipping_notice', 10, 2 );

function enhanceindustries_csm_shipnote_get_settings( $settings, $current_section ) {
	$custom_settings = array();
	if( 'enhanceindustries_shipping_notes' == $current_section ) {
		$custom_settings =  array(
			array(
			'name' => __( 'Shipping Note' ),
			'type' => 'title',
			'desc' => __( 'Add Shipping notes on checkout' ),
			'id'   => 'csm_shipping_note_lbl'
			),

			array(
				'name' => __( 'Message' ),
				'type' => 'textarea',
				'desc' => __( 'Message to display on the notice'),
				'desc_tip' => true,
				'id'	=> 'csm_shipping_note_msg'
			),

			array( 'type' => 'sectionend', 'id' => 'csm_shipping_note_lbl' )
		);
		return $custom_settings;
	} else {
		return $settings;
	}
}
add_filter( 'woocommerce_get_settings_shipping' , 'enhanceindustries_csm_shipnote_get_settings' , 10, 2 );
