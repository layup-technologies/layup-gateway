<?php

/*

*register and create shortcode for diplaying customer's active payment plans

*/

add_shortcode( 'layup', 'layup_payment_plans_shortcode' );



function layup_payment_plans_shortcode() {



    wp_enqueue_style("layup_css");



    if (get_option( 'lu_testmode', true ) == 'yes') {

        $layupurl = "https://sandbox.layup.co.za/";

    } else {

        $layupurl = "https://layup.co.za/";

        }



    $orders = get_posts( array(

        'numberposts' => -1,

        'post_type'   => wc_get_order_types(),

        'post_status' => 'wc-placed'

    ) );

    $html = '';

    if (!empty($orders)) {


        foreach( $orders as $order ) {



            $order = wc_get_order( $order->ID );

            $blog_title = get_bloginfo();

            $order_id = $order->get_order_number();

            $outstanding = get_post_meta( $order_id, 'layup_pp_outstanding_0', true );

            $months = get_post_meta( $order_id, 'layup_pp_months_0', true );

            $monthly = get_post_meta( $order_id, 'layup_pp_monthly_0', true );

            $ref = get_post_meta( $order_id, 'layup_order_ref', true );

            $layup_order_id = get_post_meta( $order_id, 'layup_order_id', true );

            $gateway_id = 'layup';

            $gateways = WC_Payment_Gateways::instance();

            $gateway = $gateways->payment_gateways()[$gateway_id];

            $html .= '<article class="pp-entry">

            <h2 class="pp-entry-title">'.$blog_title.' #'.$order_id.'</h2>

            <p class="pp-content"><strong>Outstanding:</strong> R '.$outstanding.'</p>

            <p class="pp-content"><strong>Payment Plan:</strong> R '.$monthly.' over '.$months.' months</p>

            <a target="_blank" style="text-decoration: none;" href="'.$layupurl.'dashboard/purchases/'.$layup_order_id.'"><div style="font-size: 10px;padding: 10px 20px;margin-bottom: 15px;background-color: '.$gateway->btn_bg_color.';box-shadow: 0 0 13px #d6d6d6;-moz-box-shadow: 0 0 13px #d6d6d6;-webkit-box-shadow: 0 0 13px #d6d6d6;color: '.$gateway->btn_text_color.';border-radius: 150px; width: 100%;text-align: center;" class="btn-layup">

            PAY WITH

            <img style="width: 60px; vertical-align: middle; border-style: none" src="https://layup.co.za/img/logo-color.168d4abe.png">

            </div></a>

            <p class="pp-ref">'.$ref.'</p>

            </article>';

        }
    
    } else {

        $html .= '<article class="pp-entry">

        <h3 class="pp-entry-title">You have no active payment plans.</h3>

        </article>';

    }

    return $html;
 }

