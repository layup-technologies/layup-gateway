<?php



add_action('layup_order_check', 'layup_check_payments');



function layup_check_payments() {



    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways()[$gateway_id];


    if ($gateway->api_key != ''){
        $api_key = $gateway->api_key;
    } else {
        $api_key = "myApiKey";
    }

    if ($gateway->testmode == 'yes') {

        $api_url = "https://sandbox-api.layup.co.za/";

    } else {

        $api_url = "https://api.layup.co.za/";

    }

    $orders = get_posts( array(



        'numberposts' => 100,



        'post_type'   => wc_get_order_types(),



        'meta_key'     => '_payment_method',



        'meta_value'     => 'layup',



        'post_status' => array('wc-on-hold')



    ) );

    file_put_contents('test-cron.txt', json_encode($orders));



    if (empty($orders)) {



        return;



    }



    foreach( $orders as $order ) {



        $order = wc_get_order( $order->ID );



        $layup_order_id = get_post_meta( $order->get_id(), 'layup_order_id', true );



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



                update_post_meta( $order->get_id(), 'layup_pp_id_'.$pp, $plans['_id'] );



                update_post_meta( $order->get_id(), 'layup_pp_freq_'.$pp, strtolower($plans['frequency']) );



                update_post_meta( $order->get_id(), 'layup_pp_quant_'.$pp, $plans['quantity'] );



                //get monthly amount

                $due = '';

                foreach( $plans['payments'] as $payment) {



                    if ($payment['paid'] == false){


                        $due = $payment['due'];
                        $monthly = $payment['amount'];



                        break;



                    }



                }

                $paid = 0;

                foreach( $plans['payments'] as $payment) {

                    if ($payment['paid'] == true){

                        $paid += $payment['amount'];

                    }

                }


                //convert cents to rands


                $monthly_rands = $monthly/100;

                $outstanding = $plans['amountDue'] + $plans['depositDue'] - $paid;

                $outstanding_rands = $outstanding/100; 

                $due_str = strstr($due, '(', true);
                //formate numbers to work with WC

                $due_date = date("Y/m/d", strtotime($due_str));

                $outstanding_foramted = number_format($outstanding_rands, 2, '.', '');



                $monthly_payment = number_format($monthly_rands, 2, '.', '');

                update_post_meta( $order->get_id(), 'layup_pp_due_date_'.$pp, $due_date );

                update_post_meta( $order->get_id(), 'layup_pp_outstanding_'.$pp, $outstanding_foramted );



                update_post_meta( $order->get_id(), 'layup_pp_monthly_'.$pp, $monthly_payment );



                $pp++;



                }



            } 



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



            'posts_per_page' => 20,



            'no_found_rows' => true,



            'meta_query' => array(

                'relation' => 'OR',



                array(



                'key' => 'layup_preview_months',



                'compare' => 'NOT EXISTS'



                ),

                array(



                    'key' => 'layup_preview_min_months',
    
    
    
                    'compare' => 'NOT EXISTS'
    
    
    
                    ),

                array(



                    'key' => 'layup_preview_deposit_type',
    
    
    
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


	$api_key = 'myApiKey';

	$preview_api_url = "https://sandbox-api.layup.co.za/v1/payment-plan/preview";


foreach($products as $prod) {

    $product = wc_get_product( $prod->ID );
    
    $layup_custom_months_max = $product->get_meta('layup_custom_months_max');
    $layup_preview_months = $product->get_meta('layup_preview_months');
    $layup_preview_months_min = $product->get_meta('layup_preview_min_months');
    $layup_preview_deposit_type = $product->get_meta('layup_preview_deposit_type');
    $layup_preview_deposit_amount = $product->get_meta('layup_preview_deposit_amount');
    $layup_preview_amount = $product->get_meta('layup_preview_amount');
    $test_months = $layup_custom_months_max.'|'.$layup_preview_months;
    
    if ($layup_custom_months_max != $layup_preview_months || $layup_preview_months == '' || $layup_preview_deposit_type == '' || $layup_preview_deposit_amount == '' || $layup_preview_months_min == '' || $layup_preview_amount == '') {
        
$format_number = number_format($product->get_price(), 2, '.', '');

$price = (float)$format_number * 100;

$layup_custom_deposit = $product->get_meta('layup_custom_deposit');
$layup_custom_months = $product->get_meta('layup_custom_months');

if ($layup_custom_months == 'yes')
		{
            $layup_custom_months_min = $product->get_meta('layup_custom_months_min');
			$min_months = $layup_custom_months_min + 1;
			$max_months = $layup_custom_months_max + 1;
            
		} else {

			$min_months = $gateway->lu_min_end_date;
			$max_months = $gateway->lu_max_end_date;
            
		}

		$lu_min_date = date('Y-m-d', strtotime("+" . $min_months . " months", strtotime($lu_curr_date)));

		$lu_max_date = date('Y-m-d', strtotime("+" . $max_months . " months", strtotime($lu_curr_date)));


if ($layup_custom_deposit == 'yes')
		{

			$deposit_amount = $layup_custom_deposit_amount;

			$deposit_type = $layup_custom_deposit_type;

		} else {

			$deposit_amount = $gateway->layup_dep;

			$deposit_type = $gateway->layup_dep_type;
		}

        settype($deposit_amount, 'float');

$preview_details = array(

    'depositAmount' => (int)$deposit_amount * 100,

    'amountDue' => (int)$price,

    'depositPerc' => (int)$deposit_amount,

    'endDateMax' => $lu_max_date,

    'endDateMin' => $lu_min_date,

    'absorbsFee' => false,

    'depositType' => $deposit_type

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

$max_payments = count($preview_body['paymentPlans']);



		$amount_monthly = $preview_body['paymentPlans'][$max_payments - 1]['payments'][1]['amount'];
		$max_payment_months = $preview_body['paymentPlans'][$max_payments - 1]['quantity'];
        $min_payment_months = $preview_body['paymentPlans'][0]['quantity'];

$amount_monthly_form = number_format(($amount_monthly /100), 2, '.', ',');
$testing_val = $max_payment_months.'|'.$min_months.'|'.$amount_monthly_form.'|'.$deposit_type.'|'.$deposit_amount;

$product->update_meta_data('layup_preview_months', $max_payment_months);
		$product->update_meta_data('layup_preview_min_months', $min_payment_months);

		$product->update_meta_data('layup_preview_amount', $amount_monthly_form);

		$product->update_meta_data('layup_preview_deposit_type', $deposit_type);

		$product->update_meta_data('layup_preview_deposit_amount', $deposit_amount);

        $product->save();

}

        }

        

        return;



    }



}