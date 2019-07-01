<?php



class WC_Layup_Gateway extends WC_Payment_Gateway {



     /**

      * Class constructor

      */

     public function __construct() {

        $this->version = WC_GATEWAY_LAYUP_VERSION;

        $this->id = 'layup'; // payment gateway plugin ID

        $this->icon = 'https://layup.co.za/img/logo-color.168d4abe.png'; // URL of the icon that will be displayed on checkout page near your gateway name

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

        $this->layup_dep = (int)$this->get_option( 'layup_dep' );

        $this->api_key = $this->testmode ? "myApiKey" : $this->get_option( 'lu_api_key' );

        $this->api_url = $this->testmode ? "https://sandbox-api.layup.co.za/v1/orders" : "https://api.layup.co.za/v1/orders";

    

        // This action hook saves the settings

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

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

            'title' => array(

                'title'       => 'Title',

                'type'        => 'text',

                'description' => 'This controls the title which the user sees during checkout.',

                'default'     => 'LayUp',

                'desc_tip'    => true,

            ),

            'description' => array(

                'title'       => 'Description',

                'type'        => 'textarea',

                'description' => 'This controls the description which the user sees during checkout.',

                'default'     => 'Activate your payment plan with a small deposit and break down the total cost into more affordable monthly payments.',

            ),

            'lu_testmode' => array(

                'title'       => 'Test mode',

                'label'       => 'Enable Test Mode',

                'type'        => 'checkbox',

                'description' => 'Place the payment gateway in test mode using test API keys.',

                'default'     => 'yes',

                'desc_tip'    => true,

            ),

            'layup_dep' => array(

                'title'       => 'Deposit Amount',

                'type'        => 'number',

                'description' => 'The deposit amount as a percentage',

                'default'     => '20'

            ),

            'lu_min_end_date' => array(

                'title'       => 'Min months',

                'type'        => 'number',

                'description' => 'The minimum number of months a customer can choose to pay off an order',

                'default'     => '6',
                'custom_attributes' => array(
                    'min'	=> '4'
                )

            ),

            'lu_max_end_date' => array(

                'title'       => 'Max months',

                'type'        => 'number',

                'description' => 'The maximum number of months a customer can choose to pay off an order',

                'default'     => '12',
                'custom_attributes' => array(
                    'min'	=> '6'
                )

            ),

            'lu_api_key' => array(

                'title'       => 'Live API Key',

                'type'        => 'password'

            ),

            'btn_bg_color' => array(

                'title'       => 'LayUp Button Colour',

                'type'        => 'color',

                'description' => 'Changes the background color of the buttons on a single product page',

                'default'     => '#ffffff'

            ),

            'btn_text_color' => array(

                'title'       => 'LayUp Button text Colour',

                'type'        => 'color',

                'description' => 'Changes the text colour of the buttons on a single product page',

                'default'     => '#000000'

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

        foreach( $order_items as $item_id => $order_item ) {

            $product = $order_item->get_product();

            $products[$i] = array(

                'amount'=> (int)$order_item->get_total() * 100,

                'link'=> get_permalink( $product->get_id() ),

                'sku'=> $product->get_sku()

            );



        // Format and add min and max dates

        $date_sel = get_post_meta( $order_id, 'layup_date_sel', true );
        var_dump($date_sel);
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

                    $order_image[0] = get_site_url().'/wp-content/plugins/woocommerce/assets/images/placeholder.png';

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

        }



        // Build and send LayUp order request

        $order_details = array(

            'products'=> $products,

            'endDateMax' => $max_date,

            'endDateMin' => $min_date,

            'state' => 'CANCELLED',

            'depositPerc' => $this->layup_dep,

            'absorbsFee' => true,

            'reference' => $ref,

            'name' => $blog_title.' #'.$order_id,

            'imageUrl' => $order_image[0],

  

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

                $order->update_status('wc-partial', __('Order created with LayUp', 'layup-gateway'));

    

                //reduce stock and empty cart

                wc_reduce_stock_levels($order_id);

			    $woocommerce->cart->empty_cart();



               // Redirect to LayUp

               return array(

                   'result' => 'success',

                   'redirect' => 'https://sandbox.layup.co.za/order/'. $body['_id']

               );

    

            } else {

               wc_add_notice( 'Error, please try again.', 'error' );

               

               return;

           }

    

       } else {

           wc_add_notice(  'Connection error.', 'error' );

           return;

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



		echo '<div class="error"><p>'

			. __( 'LayUp is currently in test mode and requires additional configuration to function correctly.', 'layup-gateway' )

			. '</p></div>';

	}

 }

