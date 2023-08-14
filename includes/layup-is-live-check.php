<?php

// Get settings: Enabled, Test Mode, Version, time stamp

function is_live_check( $data ) {

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id]; 

    $data = [
        'enabled' => $gateway->enabled,
        'testMode' => $gateway->testmode,
       'version' => WC_GATEWAY_LAYUP_VERSION,
       'timestamp' => time()
    ];
  
    return $data;
  }

add_action( 'rest_api_init', function () {
  register_rest_route( 'layup/v1', '/is-live', array(
    'methods' => 'GET',
    'callback' => 'is_live_check',
    'permission_callback' => '__return_true',
  ) );
} );