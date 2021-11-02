<?php

/*
*register and create shortcode for diplaying customer's active payment plans
*/

add_shortcode( 'layup', 'layup_payment_plans_shortcode' );

function layup_payment_plans_shortcode() {

    wp_enqueue_style("layup_css");

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways()[$gateway_id];

    if ($gateway->testmode == 'yes') {

        $layupurl = "https://sandbox.layup.co.za/";

    } else {

        $layupurl = "https://shopper.layup.co.za/";

        }

        $orders = get_posts( array(

            'numberposts' => -1,
            
            'meta_query' => array(
                array(
                    'key'     => '_customer_user',
                    'value'   => get_current_user_id()
                ),
                array(
                    'key' => '_payment_method',
                    'value'   => 'layup'
                )),
    
            'post_type'   => wc_get_order_types(),
    
            'post_status' => 'wc-on-hold'
    
        ) );

    $html = '';

    if (!empty($orders)) {

        foreach( $orders as $order ) {

            $order = wc_get_order( $order->ID );

            $blog_title = get_bloginfo();

            $order_id = $order->get_id();

            $order_number = $order->get_order_number();

            $outstanding = get_post_meta( $order_id, 'layup_pp_outstanding_0', true );

            $quant = get_post_meta( $order_id, 'layup_pp_quant_0', true );

            $freq = get_post_meta( $order_id, 'layup_pp_freq_0', true );

            $monthly = get_post_meta( $order_id, 'layup_pp_monthly_0', true );
            $due = get_post_meta( $order_id, 'layup_pp_due_date_0', true );

            $ref = get_post_meta( $order_id, 'layup_order_ref', true );

            $layup_order_id = get_post_meta( $order_id, 'layup_order_id', true );

            $gateway_id = 'layup';

            $gateways = WC_Payment_Gateways::instance();

            $gateway = $gateways->payment_gateways()[$gateway_id];

            $html .= '<article class="pp-entry">

            <h2 class="pp-entry-title">'.esc_attr($blog_title).' #'.esc_attr($order_number).'</h2>

            <p class="pp-content"><strong>Outstanding:</strong> R '.esc_attr($outstanding).'</p>

            <p class="pp-content"><strong>Next Payment:</strong> R '.esc_attr($monthly).' due on '.esc_attr($due).'</p>

            <a target="_blank" style="text-decoration: none;" href="'.esc_url($layupurl).'dashboard/purchases/'.esc_attr($layup_order_id).'"><div style="font-size: 10px;padding: 10px 20px;margin-bottom: 15px;background-color: '.esc_attr($gateway->btn_bg_color).';box-shadow: 0 0 13px #d6d6d6;-moz-box-shadow: 0 0 13px #d6d6d6;-webkit-box-shadow: 0 0 13px #d6d6d6;color: '.esc_attr($gateway->btn_text_color).';border-radius: 150px; width: 100%;text-align: center;" class="btn-layup">

            PAY WITH

            <img style="width: 60px; vertical-align: middle; border-style: none" src="'.plugin_dir_url( dirname( __FILE__ ) ) . 'img/logo-color.168d4abe.png">

            </div></a>

            <p class="pp-ref">ref: '.esc_attr($ref).'</p>

            </article>';

        }

    } else {

        $html .= '<article class="pp-entry">

        <h3 class="pp-entry-title">You have no active payment plans.</h3>

        </article>';

    }

    return $html;

 }







