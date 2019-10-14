<?php

/*

 * Plugin Name: WooCommerce LayUp Payment Gateway

 * Plugin URI: https://layup.co.za/how-it-works

 * Description: Activate your payment plan with a small deposit and break down the total cost into more affordable monthly payments.

 * Author: LayUp Dev Team

 * Author URI: https://layup.co.za

 * Version: 1.1.1

 *

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /*

 *  register WP cron event on activation

 */



add_filter( 'cron_schedules', 'layup_ten_add_cron_interval' );

 

function layup_ten_add_cron_interval( $schedules ) {

    $schedules['ten_mins'] = array(

        'interval' => 600,

        'display'  => esc_html__( 'Every Ten Minutes' ),

    );

 

    return $schedules;

}



register_activation_hook(__FILE__, 'layup_activation');



function layup_activation() {

    if (! wp_next_scheduled ( 'layup_order_check' )) {

		wp_schedule_event(time(), 'ten_mins', 'layup_order_check');
	
		}
		global $post;
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
		);
	
		$products = get_posts($args);
		
		$lu_curr_date = date('c');
		$lu_min_date = date('Y-m-d', strtotime("+" . 1 . " months", strtotime($lu_curr_date)));
		$lu_max_date = date('Y-m-d', strtotime("+" . 13 . " months", strtotime($lu_curr_date)));
		$api_key = "myApiKey";
		$preview_api_url = "https://sandbox-api.layup.co.za/v1/payment-plan/preview";
		
		foreach($products as $prod) {
		
		$product = wc_get_product( $prod->ID );
		
		$price = $product->get_price() * 100;
	
		$preview_details = array(
	
			'amountDue' => $price,
	
			'depositPerc' => 20,
	
			'endDateMax' => $lu_max_date,
	
			'endDateMin' => $lu_min_date,
	
			'absorbsFee' => false
	
		);
	
		$preview_headers = array(
	
			'Content-Type' => 'application/json',
	
			'apikey' => $api_key,
	
		);
	
			
	
		$preview_details_json = json_encode( $preview_details , JSON_UNESCAPED_SLASHES );
	
		$preview_args = array(
	
			'headers' => $preview_headers,
	
			'body' => $preview_details_json
	
			);
	
	
		$preview_response = wp_remote_post( $preview_api_url, $preview_args);
	
		$preview_body = json_decode( $preview_response['body'], true );
		
		$max_payment_months = count($preview_body['paymentPlans']);
			
		
		$amount_monthly = $preview_body['paymentPlans'][$max_payment_months - 1]['payments'][1]['amount'];
		$amount_monthly_form = number_format(($amount_monthly /100), 2, '.', ' ');
			
		update_post_meta( $prod->ID, 'layup_preview_months', $max_payment_months );	
		update_post_meta( $prod->ID, 'layup_preview_amount', $amount_monthly_form );	
	
		}
	
}



 /*

 *  deregister WP cron event on deactivation

 */

register_deactivation_hook(__FILE__, 'layup_deactivation');



function layup_deactivation() {

	wp_clear_scheduled_hook('layup_order_check');

}



require_once( plugin_basename( 'includes/layup-cron.php' ) );

require_once( plugin_basename( 'includes/layup-wc-functions.php' ) );



 /*

 * This action hook registers our PHP class as a WooCommerce payment gateway

 */

add_filter( 'woocommerce_payment_gateways', 'layup_add_gateway_class' );

function layup_add_gateway_class( $gateways ) {

	$gateways[] = 'WC_Layup_Gateway';

	return $gateways;

}

 

/*

 * The class itself from class-layup-wc-gateway.php, it is inside plugins_loaded action hook

 */

add_action( 'plugins_loaded', 'layup_init_gateway_class' );

function layup_init_gateway_class() {



    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {

		return;

	}



    define( 'WC_GATEWAY_LAYUP_VERSION', '0.0.1' );



    require_once( plugin_basename( 'includes/layup-payment-plan-shortcode.php' ) );

    require_once( plugin_basename( 'includes/layup-payment-plan-tab.php' ) );

    require_once( plugin_basename( 'includes/class-layup-wc-gateway.php' ) );



   

}



/*

 * Plugin links on installed plugins page

 */

function woocommerce_layup_plugin_links( $links ) {

	$settings_url = add_query_arg(

		array(

			'page' => 'wc-settings',

			'tab' => 'checkout',

			'section' => 'wc_layup_gateway',

		),

		admin_url( 'admin.php' )

	);



	$plugin_links = array(

		'<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'layup-gateway' ) . '</a>',

		'<a href="https://layup.co.za/contact-us">' . __( 'Support', 'layup-gateway' ) . '</a>',

		

	);



	return array_merge( $plugin_links, $links );

}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocommerce_layup_plugin_links' );
