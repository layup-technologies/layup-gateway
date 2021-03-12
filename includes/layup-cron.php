<?php

add_action('layup_order_check', 'layup_check_payments');

function layup_check_payments() {

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways()[$gateway_id];

    if ($gateway->testmode == 'yes') {

         $api_key = "myApiKey";

        $api_url = "https://sandbox-api.layup.co.za/";

    } else {

        $api_key = $gateway->api_key;

        $api_url = "https://api.layup.co.za/";

    }

    $orders = get_posts( array(

        'numberposts' => 100,

        'post_type'   => wc_get_order_types(),

        'meta_key'     => '_payment_method',

        'meta_value'     => 'layup',

        'post_status' => array('wc-pending', 'wc-on-hold')

    ) );

    if (empty($orders)) {

        return;

    }

    foreach( $orders as $order ) {

        $order = wc_get_order( $order->ID );

        $layup_order_id = get_post_meta( $order->get_order_number(), 'layup_order_id', true );

        $headers = array(

            'accept' => 'application/json',

            'apikey' => $api_key,

        );

        $order_args = array(

            'headers' => $headers,

            );

        $order_response = wp_remote_get($api_url.'v1/orders/'.$layup_order_id.'?populate=plans,plans.payments', $order_args);


        if( !is_wp_error( $order_response ) ) {

            $body = json_decode( $order_response['body'], true );

            // Check payments 

            if ( $body['state'] == 'PLACED' ) {

                $pp=0;

                // Save LayUp payment plans to Woocommerce order

                foreach( $body['plans'] as $plans ) {

                update_post_meta( $order->get_order_number(), 'layup_pp_id_'.$pp, $plans['_id'] );

                update_post_meta( $order->get_order_number(), 'layup_pp_freq_'.$pp, strtolower($plans['frequency']) );

                update_post_meta( $order->get_order_number(), 'layup_pp_quant_'.$pp, $plans['quantity'] );

                //get monthly amount

                foreach( $plans['payments'] as $payment) {

                    if ($payment['paid'] == false){

                        $monthly = $payment['amount'];

                        break;

                    }

                }

                $amount = 0;

                //get total due amount

                foreach( $plans['payments'] as $payment) {

                    if ($payment['paid'] == false){

                        $amount = $amount + $payment['amount'];

                    }

                }

                //convert cents to rands

                $monthly_rands = $monthly/100;

                $amount_rands = $amount/100; 

                //formate numbers to work with WC

                $outstanding = number_format($amount_rands, 2, '.', '');

                $monthly_payment = number_format($monthly_rands, 2, '.', '');

                update_post_meta( $order->get_order_number(), 'layup_pp_outstanding_'.$pp, $outstanding );

                update_post_meta( $order->get_order_number(), 'layup_pp_monthly_'.$pp, $monthly_payment );

                $pp++;

                }

                if ( $order->get_status() == "pending" ) {

                $order->update_status('wc-on-hold', __('Deposit paid to LayUp', 'layup-gateway'));

                }

            } elseif ( $body['state'] == 'COMPLETED' ) { // Check if paid in full

               //$order->update_status('wc-processing', __('Order paid in full', 'layup-gateway'), true);
               $order->payment_complete();
               $order->add_order_note( __('LayUp order paid in full', 'layup-gateway') );

               $pp=0;

               foreach( $body['plans'] as $plans ) {

                update_post_meta( $order->get_order_number(), 'layup_pp_id_'.$pp, $plans['_id'] );

                update_post_meta( $order->get_order_number(), 'layup_pp_months_'.$pp, $plans['months'] );

                $pp++;

                }

               return;

           } elseif ( $body['state'] == 'CANCELLED' ) { // Check if cancelled

            $order->update_status('wc-cancelled', __('Order cancelled by LayUp', 'layup-gateway'));

            return;

        }

       } else {

           return;

       }

    }
}

add_action('layup_prod_check', 'layup_check_prod');

function layup_check_prod() {

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways()[$gateway_id];
    
    global $post;

        $args = array(

            'post_type' => 'product',

            'status' => 'publish',

            'posts_per_page' => 50,

            'no_found_rows' => true,

            'meta_query' => array(
                'relation' => 'OR',

                array(

                'key' => 'layup_preview_months',

                'compare' => 'NOT EXISTS'

                ),
                array(

                    'key' => 'layup_preview_months',

                    'value'   => $gateway->lu_max_end_date - 1,

                    'compare' => '!=',
    
                )

)

        );

    $products = get_posts($args);

if (empty($products)){

    return;

} else {

$lu_curr_date = date('c');

$lu_min_date = date('Y-m-d', strtotime("+" . 1 . " months", strtotime($lu_curr_date)));

$lu_max_date = date('Y-m-d', strtotime("+" . $gateway->lu_max_end_date . " months", strtotime($lu_curr_date)));

$api_key = "myApiKey";

$preview_api_url = "https://sandbox-api.layup.co.za/v1/payment-plan/preview";

foreach($products as $prod) {

$product = wc_get_product( $prod->ID );

$price = $product->get_price() * 100;

$preview_details = array(

    'amountDue' => $price,

    'depositPerc' => $gateway->layup_dep,

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

$amount_monthly_form = number_format(($amount_monthly /100), 2, '.', ',');

update_post_meta( $prod->ID, 'layup_preview_months', $max_payment_months );	

update_post_meta( $prod->ID, 'layup_preview_amount', $amount_monthly_form );	

        }

        return;

    }

}