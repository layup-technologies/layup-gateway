<?php

add_action('layup_order_check', 'layup_check_payments');

function layup_check_payments()
{

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    if ($gateway->api_key != '')
    {
        $api_key = $gateway->api_key;
    }
    else
    {
        $api_key = "myApiKey";
    }

    if ($gateway->testmode == 'yes')
    {

        $api_url = "https://sandbox-api.layup.co.za/";
    }
    else
    {

        $api_url = "https://api.layup.co.za/";
    }

    $orders = get_posts(array(

        'numberposts' => 100,

        'post_type' => wc_get_order_types() ,

        'meta_key' => '_payment_method',

        'meta_value' => 'layup',

        'post_status' => array(
            'wc-on-hold'
        )

    ));

    if (empty($orders))
    {

        return;
    }

    foreach ($orders as $order)
    {

        $order = wc_get_order($order->ID);

        $layup_order_id = get_post_meta($order->get_id() , 'layup_order_id', true);

        $headers = array(

            'accept' => 'application/json',

            'apikey' => $api_key,

        );

        $order_args = array(

            'headers' => $headers,

        );

        $order_response = wp_remote_get($api_url . 'v1/orders/' . $layup_order_id . '?populate=plans,plans.payments', $order_args);

        if (!is_wp_error($order_response))
        {

            $body = json_decode($order_response['body'], true);

            // Check payments
            

            if ($body['state'] == 'PLACED')
            {

                $pp = 0;

                // Save LayUp payment plans to Woocommerce order
                

                foreach ($body['plans'] as $plans)
                {

                    update_post_meta($order->get_id() , 'layup_pp_id_' . $pp, $plans['_id']);

                    update_post_meta($order->get_id() , 'layup_pp_freq_' . $pp, strtolower($plans['frequency']));

                    update_post_meta($order->get_id() , 'layup_pp_quant_' . $pp, $plans['quantity']);

                    //get monthly amount
                    $due = '';

                    foreach ($plans['payments'] as $payment)
                    {

                        if ($payment['paid'] == false && $payment['amount'] > 0)
                        {

                            $due = $payment['due'];
                            $monthly = $payment['amount'];

                            break;
                        }
                    }

                    $paid = 0;

                    foreach ($plans['payments'] as $payment)
                    {

                        if ($payment['paid'] == true)
                        {

                            $paid += $payment['amount'];
                        }
                    }

                    //convert cents to rands
                    

                    $monthly_rands = $monthly / 100;

                    $outstanding = $plans['amountDue'] + $plans['depositDue'] - $paid;

                    $outstanding_rands = $outstanding / 100;

                    
                    //formate numbers to work with WC
                    $due_date = date("Y/m/d", strtotime($due));

                    $outstanding_foramted = number_format($outstanding_rands, 2, '.', '');

                    $monthly_payment = number_format($monthly_rands, 2, '.', '');

                    update_post_meta($order->get_id() , 'layup_pp_due_date_' . $pp, $due_date);

                    update_post_meta($order->get_id() , 'layup_pp_outstanding_' . $pp, $outstanding_foramted);

                    update_post_meta($order->get_id() , 'layup_pp_monthly_' . $pp, $monthly_payment);

                    $pp++;
                }
            }
        }
    }
}

add_action('layup_canceled_order_check', 'layup_check_canceled_order');

function layup_check_canceled_order()
{

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    if ($gateway->api_key != '')
    {
        $api_key = $gateway->api_key;
    }
    else
    {
        $api_key = "myApiKey";
    }

    if ($gateway->testmode == 'yes')
    {

        $api_url = "https://sandbox-api.layup.co.za/";
    }
    else
    {

        $api_url = "https://api.layup.co.za/";
    }

    $orders = get_posts(array(

        'numberposts' => 100,

        'post_type' => wc_get_order_types() ,

        'meta_key' => '_payment_method',

        'meta_value' => 'layup',

        'date_query' => array(
            array(
                'after' => '24 hours ago'
            )
        ) ,

        'post_status' => array(
            'wc-pending',
            'wc-cancelled'
        )

    ));

    if (empty($orders))
    {

        return;
    }

    foreach ($orders as $order)
    {

        $order = wc_get_order($order->ID);

        $layup_order_id = get_post_meta($order->get_id() , 'layup_order_id', true);

        $headers = array(

            'accept' => 'application/json',

            'apikey' => $api_key,

        );

        $order_args = array(

            'headers' => $headers,

        );

        $order_response = wp_remote_get($api_url . 'v1/orders/' . $layup_order_id . '?populate=plans,plans.payments', $order_args);

        if (!is_wp_error($order_response))
        {

            $body = json_decode($order_response['body'], true);

            // Check payments
            

            if ($body['state'] == 'PLACED')
            {

                $pp = 0;

                // Save LayUp payment plans to Woocommerce order
                

                foreach ($body['plans'] as $plans)
                {

                    update_post_meta($order->get_id() , 'layup_pp_id_' . $pp, $plans['_id']);

                    update_post_meta($order->get_id() , 'layup_pp_freq_' . $pp, strtolower($plans['frequency']));

                    update_post_meta($order->get_id() , 'layup_pp_quant_' . $pp, $plans['quantity']);

                    //get monthly amount
                    $due = '';

                    foreach ($plans['payments'] as $payment)
                    {

                        if ($payment['paid'] == false)
                        {

                            $due = $payment['due'];
                            $monthly = $payment['amount'];

                            break;
                        }
                    }

                    $paid = 0;

                    foreach ($plans['payments'] as $payment)
                    {

                        if ($payment['paid'] == true)
                        {

                            $paid += $payment['amount'];
                        }
                    }

                    //convert cents to rands
                    

                    $monthly_rands = $monthly / 100;

                    $outstanding = $plans['amountDue'] + $plans['depositDue'] - $paid;

                    $outstanding_rands = $outstanding / 100;

                    $due_str = strstr($due, '(', true);
                    //formate numbers to work with WC
                    $due_date = date("Y/m/d", strtotime($due_str));

                    $outstanding_foramted = number_format($outstanding_rands, 2, '.', '');

                    $monthly_payment = number_format($monthly_rands, 2, '.', '');

                    update_post_meta($order->get_id() , 'layup_pp_due_date_' . $pp, $due_date);

                    update_post_meta($order->get_id() , 'layup_pp_outstanding_' . $pp, $outstanding_foramted);

                    update_post_meta($order->get_id() , 'layup_pp_monthly_' . $pp, $monthly_payment);

                    $pp++;
                }

                $order->update_status('wc-on-hold', __('LayUp cron detected that this order is in a PLACED state.', 'layup-gateway'));
            }
            elseif ($body['state'] == 'COMPLETED')
            {
                $order->payment_complete();

                $order->add_order_note(__('LayUp detected that this order is paid in full. Order changed to Processing', 'layup-gateway'));
                update_post_meta($order->get_id() , 'layup_pp_outstanding_0', '0');
            }
        }
    }
}


add_action('layup_api_key_check', 'layup_check_api_key');

function layup_check_api_key()
{
    global $woocommerce;

    $gateway_id = 'layup';
    $gateways = WC_Payment_Gateways::instance();
    $gateway = $gateways->payment_gateways() [$gateway_id];
    $api_key = $gateway->api_key;
    
    if ($gateway->testmode == 'yes')
    {
        $api_url = "https://sandbox-api.layup.co.za/v1/auth/me";
    } else {
        $api_url = "https://api.layup.co.za/v1/auth/me";
    }

    $headers = array(
        'Content-Type' => 'application/json',
        'apikey' => $api_key,
    );

    $args = array(
        'headers' => $headers,
    );

    $response = wp_remote_get($api_url, $args);

    if (!is_wp_error($response)) {
        if ($response['body'] == "Unauthorized") {
            $gateway->update_option( 'api_key_error', '1' );
        } else {
            $gateway->update_option( 'api_key_error', '0' );
        }
    }
}
