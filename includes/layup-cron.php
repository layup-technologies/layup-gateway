<?php



add_action('layup_order_check', 'layup_check_payments');



function layup_check_payments() {





    if (get_option( 'lu_testmode', true ) == 'yes') {

         $api_key = "myApiKey";

        $api_url = "https://sandbox-api.layup.co.za/";

    } else {

        $api_key = get_option( 'lu_api_key' );

    $api_url = "https://api.layup.co.za/";

        }

        

        

        

    $orders = get_posts( array(

        'numberposts' => -1,

        'post_type'   => wc_get_order_types(),

        'post_status' => array('wc-partial', 'wc-placed')

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

                update_post_meta( $order->get_order_number(), 'layup_pp_months_'.$pp, $plans['months'] );



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

                if ( $order->get_status() == "partial" ) {

                $order->update_status('wc-placed', __('Deposit paid to LayUp', 'layup-gateway'));

                }



    

            } elseif ( $body['state'] == 'COMPLETED' ) { // Check if paid in full

               $order->update_status('processing', __('Order paid in full', 'layup-gateway'));



               $pp=0;



               foreach( $body['plans'] as $plans ) {

                

                update_post_meta( $order->get_order_number(), 'layup_pp_id_'.$pp, $plans['_id'] );

                update_post_meta( $order->get_order_number(), 'layup_pp_months_'.$pp, $plans['months'] );



                $pp++;

                

                }





               return;

           } elseif ( $body['state'] == 'CANCELLED' ) { // Check if cancelled and restore stock



            foreach ($order->get_items() as $item_id => $item) {

                // Get an instance of corresponding the WC_Product object

                $product = $item->get_product();

                $qty = $item->get_quantity(); // Get the item quantity

                wc_update_product_stock($product, $qty, 'increase');

            }

            $order->update_status('cancelled', __('Order cancelled: '.$body['cancelledBy'], 'layup-gateway'));



            return;

             

             

        }



    

       } else {



           return;

       }

    }





	

}

