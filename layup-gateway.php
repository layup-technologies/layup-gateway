<?php



/*

 * Plugin Name: WooCommerce LayUp Payment Gateway

 * Plugin URI: https://layup.co.za/how-it-works

 * Description: Activate your payment plan with a small deposit and break down the total cost into more affordable monthly payments.

 * Author: LayUp Dev Team

 * Author URI: https://layup.co.za

 * Version: 1.7.6

 *

*/



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function layup_cron_schedules($schedules){
    if(!isset($schedules["layup10min"])){
        $schedules["layup10min"] = array(
            'interval' => 10*60,
            'display' => __('Once every 10 minutes'));
    }
    
    return $schedules;
}
add_filter('cron_schedules','layup_cron_schedules');


 /*
 *  register WP cron event on activation
 */

register_activation_hook(__FILE__, 'layup_activation');


function layup_activation() {





    if (! wp_next_scheduled ( 'layup_order_check' )) {



		wp_schedule_event(time(), 'weekly', 'layup_order_check');



	}



	if (! wp_next_scheduled ( 'layup_prod_check' )) {



		wp_schedule_event(time(), 'layup10min', 'layup_prod_check');



	}



}



 /*

 *  deregister WP cron event on deactivation

 */



register_deactivation_hook(__FILE__, 'layup_deactivation');



function layup_deactivation() {



	wp_clear_scheduled_hook('layup_order_check');



	wp_clear_scheduled_hook('layup_prod_check');



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



    define( 'WC_GATEWAY_LAYUP_VERSION', '1.7.6' );



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







