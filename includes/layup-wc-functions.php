<?php

add_action( 'wp_enqueue_scripts', 'register_layup_style' );

function register_layup_style() {
    wp_register_style("layup_css", plugins_url( '../css/payment-plans.css', __FILE__ ), array(), '1.0.0', 'all' );
}

/**
 * Create the disable LayUp checkbox field on product admin page
 */
function create_layup_disable_field() {
    $args = array(
    'id' => 'layup_disable',
    'label' => __( 'Disable LayUp checkout', 'layup-gateway' ),
    'class' => 'lu-layup-disable',
    'desc_tip' => true,
    'description' => __( 'Check this box if you don`t want customers to be able to checkout using the LayUp gateway with this product in their cart.', 'layup-gateway' ),
    );
    woocommerce_wp_checkbox( $args );
   }
add_action( 'woocommerce_product_options_general_product_data', 'create_layup_disable_field' );

/**
 * Save the Disable LayUp field
 */
function save_layup_disable_field( $post_id ) {
    $product = wc_get_product( $post_id );
    $title = isset( $_POST['layup_disable'] ) ? $_POST['layup_disable'] : '';
    $product->update_meta_data( 'layup_disable', sanitize_text_field( $title ) );
    $product->save();
}
   add_action( 'woocommerce_process_product_meta', 'save_layup_disable_field' );

/**
 * Check products in cart for Disable LayUp field and disable LayUp gateway
 */
function check_layup_disable_field($gateways){

    global $woocommerce;

    $inarray = false;
    
    foreach ($woocommerce->cart->cart_contents as $key => $values ) { //enumerate over all cart contents
        $layup_disable_meta = get_post_meta($values['product_id'], 'layup_disable', true);
        if ( !empty( $layup_disable_meta ) ){
            $inarray = true;//set inarray to true
            break;
        }
    }

    if($inarray) { //product is in the cart
        unset($gateways['layup']);
    } 
    return $gateways;
}
add_filter('woocommerce_available_payment_gateways','check_layup_disable_field',1);

/**
 * Display LayUp icon on single product page
 */
function layup_display_icon() {
    global $post;
    global $woocommerce;
    // Check for the Disable LayUp field value
    $product = wc_get_product( $post->ID );
    $layup_disable_meta = $product->get_meta( 'layup_disable' );
    if( $layup_disable_meta != 'yes' ) {
    // Only display LayUp icon if Disable LayUp field is checked
    $checkout_url = $woocommerce->cart->get_checkout_url();
    echo '<a href="'.$checkout_url.'?add-to-cart='.$post->ID.'"><div style="font-size: 10px;padding: 10px 20px;margin-bottom: 15px;background-color: #fff;box-shadow: 0 0 13px #d6d6d6;-moz-box-shadow: 0 0 13px #d6d6d6;-webkit-box-shadow: 0 0 13px #d6d6d6;color: #2c3e50;border-radius: 150px; max-width: 50%;text-align: center;" class="btn-layup">
    PAY WITH
    <img style="width: 60px; vertical-align: middle; border-style: none" src="https://layup.co.za/img/logo-color.168d4abe.png">
    </div></a>';
    }
   }
   add_action( 'woocommerce_before_add_to_cart_button', 'layup_display_icon' );


 // register WC Order status Partial and Placed
function register_layup_order_statuses() {
    register_post_status( 'wc-partial', array(
        'label'                     => _x( 'Partial', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Partial <span class="count">(%s)</span>', 'Partial <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-placed', array(
        'label'                     => _x( 'Placed', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Placed <span class="count">(%s)</span>', 'Placed <span class="count">(%s)</span>' )
    ) );
}

add_action( 'init', 'register_layup_order_statuses' );
// Add to list of WC Order statuses
function add_layup_to_order_statuses( $order_statuses ) {
    
        // add new order statuses
        $order_statuses['wc-partial'] = _x( 'Partial', 'Order status', 'woocommerce' );
        $order_statuses['wc-placed'] = _x( 'Placed', 'Order status', 'woocommerce' );

    return $order_statuses;
}
add_filter( 'wc_order_statuses', 'add_layup_to_order_statuses' );

/**
 *   Send Formatted Email @ WooCommerce "Placed" Order Status
 */

 // Adding action for 'Order Placed'
add_filter( 'woocommerce_email_actions', 'placed_email_actions', 20, 1 );
function placed_email_actions( $action ) {
    $actions[] = 'woocommerce_order_status_wc-placed';
    return $actions;
}
  
add_action( 'woocommerce_order_status_changed', 'layup_status_custom_notification', 20, 4 );
  
function layup_status_custom_notification( $order_id, $old_status, $new_status, $order ) {
    if ( $new_status == 'placed' ) {
        $heading = 'Order Placed';
        $subject = 'Order Placed and payment waiting to be completed';
    
        // Get WooCommerce email objects
        $mailer = WC()->mailer()->get_emails();
    
        // Assign heading & subject to chosen object
        $mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
        
        $mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
        
    
        // Send the email with custom heading & subject
        $mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
    
        }
    }


/**
 *   Add Content to the Customer Processing Order Email - WooCommerce
 */
 
add_action( 'woocommerce_email_before_order_table', 'layup_add_content_email', 20, 4 );
 
function layup_add_content_email( $order, $sent_to_admin, $plain_text, $email ) {
   if ( $email->id == 'customer_processing_order' && $order->get_status() == "placed" ) {
      echo '<h2 class="email-upsell-title">Awaiting Full Payment</h2><p class="email-upsell-p">Thank you for placing an order using LayUp, You order will be monitored and as soon as full payment has been recieved, your goods wll be shipped.</p>';
   }
}
