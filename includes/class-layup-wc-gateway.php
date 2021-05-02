<?php



class WC_Layup_Gateway extends WC_Payment_Gateway {





     /**

      * Class constructor

      */



     public function __construct() {



        $this->version = WC_GATEWAY_LAYUP_VERSION;



        $this->id = 'layup'; // payment gateway plugin ID





        $this->icon = plugin_dir_url( dirname( __FILE__ ) ) . 'img/logo-color.168d4abe.png';  // URL of the icon that will be displayed on checkout page near your gateway name



        $this->has_fields = false; // in case of custom credit card form



        $this->method_title = __( 'LayUp', 'layup-gateway' );



        $this->method_description = 'Activate your payment plan with a small deposit and break down the total cost into more affordable monthly payments.'; // will be displayed on the options page



        $this->available_countries  = array( 'ZA' );



        $this->available_currencies = (array)apply_filters('layup_gateway_available_currencies', array( 'ZAR' ) );



        // gateways can support products, subscriptions, refunds, saved payment methods,



        $this->supports = array(



            'products'



        );





        // Method with all the options fields



        $this->init_form_fields();



        // Load the settings.



        $this->init_settings();



        $this->title = $this->get_option( 'title' );



        $this->description = $this->get_option( 'description' );



        $this->enabled = $this->get_option( 'enabled' );



        $this->lu_max_end_date = $this->get_option( 'lu_max_end_date') + 1;



        $this->lu_min_end_date = $this->get_option( 'lu_min_end_date');



        $this->btn_bg_color = $this->get_option( 'btn_bg_color' );



        $this->btn_text_color = $this->get_option( 'btn_text_color' );



        $this->testmode = 'yes' === $this->get_option( 'lu_testmode' );



        $this->payplan_disp = 'yes' === $this->get_option( 'payplan_disp' );



        $this->layup_dep = (int)$this->get_option( 'layup_dep' );

        $this->layup_dep_type = $this->get_option( 'layup_dep_type' );


		if ($this->get_option( 'lu_api_key' ) != ''){
			$this->api_key = $this->get_option( 'lu_api_key' );
		} else {
			$this->api_key = "myApiKey";
		}
        

        $this->api_url = $this->testmode ? "https://sandbox-api.layup.co.za/v1/orders" : "https://api.layup.co.za/v1/orders";


        // This action hook saves the settings

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

        add_action('woocommerce_api_wc_layup_gateway', array($this, 'layup_callback'));

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );



     }





    /**

      * Plugin admin options

      */



     public function init_form_fields(){



        $this->form_fields = array(



            'enabled' => array(



                'title'       => 'Enable/Disable',



                'label'       => 'Enable LayUp Gateway',



                'type'        => 'checkbox',



                'description' => '',



                'default'     => 'no'



            ),





            'lu_api_key' => array(



                'title'       => 'Live API Key',



                'type'        => 'password',



                'description' => 'The API Key for your Merchant Account provided by LayUp.'



            ),



            'title' => array(



                'title'       => 'Title',



                'type'        => 'text',



                'description' => 'This controls the title which the user sees during checkout.',



                'default'     => 'LayUp'



            ),



            'description' => array(



                'title'       => 'Description',



                'type'        => 'text',



                'description' => 'This controls the description which the user sees during checkout.',



                'default'     => 'Interest Free Lay-By | Safe & Easy Instalments | No Credit Checks | Instant Sign Up',



            ),



            'lu_testmode' => array(



                'title'       => 'Test mode',



                'label'       => 'Enable Test Mode',



                'type'        => 'checkbox',



                'description' => 'Place the payment gateway in test mode using test API keys.',



                'default'     => 'yes'



            ),



            'layup_dep_type' => array(



                'title'       => 'Deposit Type',


                'type'        => 'select',



                'description' => 'The deposit type that you want your customers to pay.',

                'options' => array(
                    'PERCENTAGE' => 'Percentage',
                    'INSTALMENT' => 'First instalment',
                    'FLAT' => 'Flat fee'
                ),

               
                'default'     => 'PERCENTAGE'

            ),

            'layup_dep' => array(



                'title'       => 'Deposit Amount',



                'type'        => 'number',



                'description' => 'The deposit amount based on what was chosen as the deposit type.',



                'default'     => '20'



            ),



            'lu_min_end_date' => array(



                'title'       => 'Min months',



                'type'        => 'number',



                'description' => 'The minimum number of months a customer can choose to pay off an order',



                'default'     => '1',



                'custom_attributes' => array(



                    'min'	=> '1'



                )



            ),



            'lu_max_end_date' => array(



                'title'       => 'Max months',



                'type'        => 'number',



                'description' => 'The maximum number of months a customer can choose to pay off an order',



                'default'     => '12',



                'custom_attributes' => array(



                    'min'	=> '2'



                )



            ),



            'payplan_disp' => array(





                'title'       => 'Show payment plan example',



                'type'        => 'checkbox',



                'description' => 'Show payment plan example under each product and on single product page',



                'default'     => 'yes'



            ),





        );



     }



    /**

     * Check if this gateway is enabled and available in the base currency being traded with.

     */



    public function is_valid_for_use() {



        $is_available          = false;



        $is_available_currency = in_array( get_woocommerce_currency(), $this->available_currencies );



        if ( $is_available_currency && $this->merchant_id && $this->merchant_key ) {



            $is_available = true;



        }

        return $is_available;

    }





    public function admin_options() {



        if ( in_array( get_woocommerce_currency(), $this->available_currencies ) ) {



            parent::admin_options();



        } else {



        ?>



            <h3><?php _e( 'LayUp', 'layup-gateway' ); ?></h3>



            <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'layup-gateway' ); ?></strong> <?php /* translators: 1: a href link 2: closing href */ echo sprintf( __( 'Choose South African Rands as your store currency in %1$sGeneral Settings%2$s to enable the LayUp Gateway.', 'layup-gateway' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '">', '</a>' ); ?></p></div>



            <?php



        }



    }



    /*

     * Processing the order and redirecting to layup

     */



    public function process_payment( $order_id ) {



        global $woocommerce;



        $products = array();



        $i = 0;



        // we need it to get any order detailes



        $order = wc_get_order( $order_id );



        $unid = md5(uniqid($order_id, true));



        $ref = substr($unid, 0, 10);



        $blog_title = get_bloginfo();



        $order_items = $order->get_items( array('line_item') );

        // Build product array

        $custom_dep_inarray = false;

        foreach( $order_items as $cd_item_id => $cd_order_item ) {



            $cd_product = $cd_order_item->get_product();

            $is_custom_dep = get_post_meta( $cd_product->get_id(), 'layup_custom_deposit', true );

            if (!empty($is_custom_dep))
            {
    
                $custom_dep_inarray = true; //set inarray to true
                
                $custom_dep_prod = $cd_product->get_title();
                $this->layup_dep = get_post_meta( $cd_product->get_id(), 'layup_custom_deposit_amount', true );
                $this->layup_dep_type = get_post_meta( $cd_product->get_id(), 'layup_custom_deposit_type', true );
                break;
    
            }

        }

            if ($custom_dep_inarray && count( WC()->cart->get_cart() ) > 1){

                wc_add_notice(  'The following product can only be checked out on its own using LayUp: '.$custom_dep_prod, 'error' );

                return;

            } else {

        $woo_thank_you = $order->get_checkout_order_received_url();



        // Build product array



        foreach( $order_items as $item_id => $order_item ) {



            $product = $order_item->get_product();



            $products[$i] = array(



                'amount'=> (int)$order_item->get_total() * 100,



                'link'=> get_permalink( $product->get_id() ),



                'sku'=> $product->get_sku()



            );



        // Format and add min and max dates



        $date_sel = get_post_meta( $order_id, 'layup_date_sel', true );



        if (!empty($date_sel)) {



            $curr_date = date('c');



            $min_date = date('c', strtotime("+" . $this->lu_min_end_date . " months", strtotime($curr_date)));



            $max_date = date('c', strtotime($date_sel));



        } else {



            $curr_date = date('c');



            $min_date = date('c', strtotime("+" . $this->lu_min_end_date . " months", strtotime($curr_date)));



            $max_date = date('c', strtotime("+" . $this->lu_max_end_date . " months", strtotime($curr_date)));



        }





            if($i == 0){



                $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()));



                if($featured_image) {



                    $order_image = $featured_image;



                } else {



                    $order_image[0] = wc_placeholder_img_src();



                }



            }



            $i++;



        }



        // Check for shipping total



        $order_shipping_total = $order->get_total_shipping();



        if ($order_shipping_total != '') {



            $products[$i] = array(



                'amount'=> (int)$order_shipping_total * 100,



                'link'=> get_site_url(),



                'sku'=> 'Shipping'



            );



            $i++;



        }



// Check for tax total



        $order_tax_total = $order->get_total_tax();



        if ($order_tax_total != '') {



            $products[$i] = array(



                'amount'=> (int)$order_tax_total * 100,



                'link'=> get_site_url(),



                'sku'=> 'VAT'



            );



        }



        // Build and send LayUp order request



        $order_details = array(

            'depositAmount' => $this->layup_dep * 100,

            'products' => $products,


            'endDateMax' => $max_date,


            'endDateMin' => $min_date,


            'depositPerc' => $this->layup_dep,


            'absorbsFee' => true,


            'reference' => $ref,


            'name' => $blog_title.' #'.$order_id,


            'imageUrl' => $order_image[0],

            'depositType' => $this->layup_dep_type,



        );


        $headers = array(



             'Content-Type' => 'application/json',



             'apikey' => $this->api_key,



        );





        $order_details_json = json_encode( $order_details , JSON_UNESCAPED_SLASHES );



        $args = array(



            'headers' => $headers,



            'body' => $order_details_json



            );



        $response = wp_remote_post( $this->api_url, $args);





        if( !is_wp_error( $response ) ) {



            $body = json_decode( $response['body'], true );



            // Check if order was created successfully 



            if ( $body['state'] == 'PARTIAL' ) {



                // Link LayUp Order to Woocommerce order



                update_post_meta( $order_id, 'layup_order_id', $body['_id'] );



                update_post_meta( $order_id, 'layup_order_ref', $body['reference'] );



                // some notes added to Woocommerce order on admin dashboard



                $order->update_status('wc-pending', __('Order created with LayUp', 'layup-gateway'));



                //reduce stock and empty cart



                //wc_reduce_stock_levels($order_id);



			    $woocommerce->cart->empty_cart();



               // Redirect to LayUp



               return array(



                   'result' => 'success',



                   'redirect' => ($this->testmode) ? 'https://sandbox.layup.co.za/order/'. $body['_id'] . '?notifyUrl='. $woo_thank_you : 'https://shopper.layup.co.za/order/'. $body['_id'] . '?notifyUrl='. $woo_thank_you



               );



            } else {



               wc_add_notice( $body['state'], 'error' );



               return;



           }



       } else {



           wc_add_notice(  'something else happened', 'error' );



           return;



       }

    }



     }


    // Handles the callbacks received from the payment backend. give this url to your payment processing comapny as the ipn response URL:
    // USAGE:  http://myurl.com/?wc-api=WC_Layup_Gateway
    function layup_callback() {
        
        $layup_order_id = $_POST['body']['orderId'];

         $orders = new WP_Query( array(

        'post_type'   => wc_get_order_types(),

        'meta_key'     => 'layup_order_id',

        'meta_value'     => $layup_order_id,

        'post_status' => array('wc-pending', 'wc-on-hold')

        ) );
    
        if ($orders->have_posts()) {

        $first_post = $orders->posts[0];
        $order = wc_get_order( $first_post->ID );

            if ($_POST['type'] == 'ORDERPLACED') 
            {
                $headers = array(
                    'accept' => 'application/json',
                    'apikey' => $this->api_key,
                );

                $order_args = array(
                    'headers' => $headers,
                    );
        
                $order_response = wp_remote_get($this->api_url.'/'.$layup_order_id.'?populate=plans,plans.payments', $order_args);

                if( !is_wp_error( $order_response ) ) {

                    $body = json_decode( $order_response['body'], true );
error_log( print_r( $body, true ) );
                    $pp=0;

                    // Save LayUp payment plans to Woocommerce order
    
                    foreach( $body['plans'] as $plans ) {
    
                    update_post_meta( $order->get_order_number(), 'layup_pp_id_'.$pp, $plans['_id'] );
                    update_post_meta( $order->get_order_number(), 'layup_pp_freq_'.$pp, strtolower($plans['frequency']) );
                    update_post_meta( $order->get_order_number(), 'layup_pp_quant_'.$pp, $plans['quantity'] );
    
                    //get monthly amount
    
                    foreach( $plans['payments'] as $payment) {
    
                        if ($payment['paymentType'] != 'DEPOSIT'){
    
                            $monthly = $payment['amount'];
                             break;
    
                        }
    
                    }
    
                    $amount = 0;
    
                    //convert cents to rands
    
                    $monthly_rands = $monthly/100;
                    $amount_rands = $plans['amountDue']/100; 

    
                    //format numbers to work with WC

                    $outstanding = number_format($amount_rands, 2, '.', '');
    
                    $monthly_payment = number_format($monthly_rands, 2, '.', '');
    
                    update_post_meta( $order->get_order_number(), 'layup_pp_outstanding_'.$pp, $outstanding );
                    update_post_meta( $order->get_order_number(), 'layup_pp_monthly_'.$pp, $monthly_payment );
    
                    $pp++;
    
                    }

                    if ( $order->get_status() == "pending" ) {

                        $order->update_status('wc-on-hold', __('Deposit paid to LayUp.', 'layup-gateway'));
        
                    }
                }

            } elseif ($_POST['type'] == 'ORDERCOMPLETED') {
                

                $order->payment_complete();

                $order->add_order_note( __('LayUp order paid in full.', 'layup-gateway') );
                update_post_meta( $order->get_order_number(), 'layup_pp_outstanding_0', '0' );

            } elseif ($_POST['type'] == 'ORDERCANCELLED') {

                $order->update_status('wc-cancelled', __('Order cancelled by LayUp.', 'layup-gateway'));

            } else {
                return;
            }
        }
        
    }



    /**

	*  Show possible admin notices

	*/



     public function admin_notices() {



		if ( 'yes' !== $this->get_option( 'enabled' )



			|| 'yes' !== $this->get_option( 'lu_testmode' ) ) {



            return;

            

        }



        $settings_url = add_query_arg(



            array(



                'page' => 'wc-settings',



                'tab' => 'checkout',



                'section' => 'wc_layup_gateway',



            ),



            admin_url( 'admin.php' )



        );



        echo '<div class="error"><p>'

        

			. __( 'LayUp is currently in test mode and requires additional configuration to function correctly. Complete setup ', 'layup-gateway' )



            . '<a href="' . esc_url( $settings_url ) . '">'. __( 'here.', 'layup-gateway' ) . '</a>



			</p></div>';



	}



 }







