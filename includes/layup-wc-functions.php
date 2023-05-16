<?php
add_action('wp_enqueue_scripts', 'register_layup_style');

function register_layup_style()
{

    wp_register_style("layup_css", plugins_url('../css/payment-plans.css', __FILE__) , array() , '1.0.0', 'all');

}

/**
 * Create layUp Merchant Settings in Woocommerce
 */

// if (!class_exists('WC_Settings_LayUp'))
// {
// 	function layup_add_settings()
// 	{
// 		/**
// 		 * Settings class
// 		 */
// 		class WC_Settings_LayUp extends WC_Settings_Page
// 		{
// 			/**
// 			 * Setup settings class
// 			 */
// 			public function __construct()
// 			{
// 				$this->id = 'layup-merchant';
// 				$this->label = __('LayUp Merchant Settings', 'woocommerce-settings-tab-layup');
// 				add_filter('woocommerce_settings_tabs_array', array(
// 					$this,
// 					'add_settings_page'
// 				) , 20);
// 				add_action('woocommerce_settings_' . $this->id, array(
// 					$this,
// 					'layup_output'
// 				));
// 				add_action('woocommerce_settings_save_' . $this->id, array(
// 					$this,
// 					'layup_save'
// 				));
// 				add_action('admin_notices', array(
// 					$this,
// 					'layup_admin_notices'
// 				));
// 			}
// 			/**
// 			 * Get settings array
// 			 */
// 			public function layup_get_settings()
// 			{
// 				$merchant_id = get_option('layup_merchant_id');
// 				if ($merchant_id == '')
// 				{
// 					$settings = apply_filters('layup_settings', array(
// 						array(
// 							'name' => __('Merchant Details', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'title',
// 							'desc' => '',
// 							'id' => 'layup_merchant_settings_title'
// 						) ,
// 						array(
// 							'name' => __('Merchant ID', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'text',
// 							'desc' => __('Your Merchant ID provided by LayUp, Please Enter it here and click save so we can fetch your merchant details', 'woocommerce-settings-tab-layup') ,
// 							'id' => 'layup_merchant_id'
// 						) ,
// 						array(
// 							'type' => 'sectionend',
// 							'id' => 'layup_merchant_section_end'
// 						)
// 					));
// 				}
// 				else
// 				{
// 					$settings = apply_filters('layup_settings', array(
// 						array(
// 							'name' => __('Merchant Details', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'title',
// 							'desc' => '',
// 							'id' => 'layup_merchant_settings_title'
// 						) ,
// 						array(
// 							'name' => __('Merchant ID', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'password',
// 							'desc' => __('Your Merchant ID provided by LayUp, Please Enter it here and click save so we can fetch your merchant details', 'woocommerce-settings-tab-layup') ,
// 							'id' => 'layup_merchant_id',
// 						) ,
// 						array(
// 							'name' => __('Merchant Name', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'text',
// 							'desc' => __('The name of your merchant account, usually your company Name', 'woocommerce-settings-tab-layup') ,
// 							'id' => 'layup_merchant_name',
// 						) ,
// 						array(
// 							'name' => __('Merchant domain', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'text',
// 							'desc' => __('The Domain of your website, e.g. yourdomain.co.za', 'woocommerce-settings-tab-layup') ,
// 							'id' => 'layup_merchant_domain',
// 						) ,
// 						array(
// 							'name' => __('Merchant notify URL', 'woocommerce-settings-tab-layup') ,
// 							'type' => 'text',
// 							'desc' => __('The notify URL of your website, usually the website`s payment thank you page', 'woocommerce-settings-tab-layup') ,
// 							'id' => 'layup_merchant_notifyurl',
// 						) ,
// 						array(
// 							'type' => 'sectionend',
// 							'id' => 'layup_merchant_section_end'
// 						)
// 					));
// 				}
// 				return apply_filters('woocommerce_get_settings_' . $this->id, $settings);
// 			}
// 			/**
// 			 * Output the settings
// 			 */
// 			public function layup_output()
// 			{
// 				global $woocommerce;
// 				$gateway_id = 'layup';
// 				$gateways = WC_Payment_Gateways::instance();
// 				$gateway = $gateways->payment_gateways() [$gateway_id];
// 				$merchant_id = get_option('layup_merchant_id');
// 				$api_key_check = $gateway->api_key;
// 				if ($merchant_id !== '' || $api_key_check !== '')
// 				{
// 					$api_key = $gateway->api_key;
// 				if ($gateway->testmode == 'yes')
// 						{
// 							$api_url = "https://sandbox-api.layup.co.za/";
// 						}
// 						else
// 						{
// 							$api_url = "https://api.layup.co.za/";
// 						}


// 					$headers = array(
// 						'accept' => 'application/json',
// 						'apikey' => $api_key,
// 					);
// 					$merchant_args = array(
// 						'headers' => $headers,
// 					);
// 					$merch_response = wp_remote_get($api_url . 'v1/merchants/' . $merchant_id, $merchant_args);
// 					if (!is_wp_error($merch_response))
// 					{
// 						if ($merch_response['body'] == 'Forbidden')
// 						{
// 							echo '<div class="error"><p>'
//  . __('The Merchant ID was invalid, please try again', 'layup-gateway')
//  . '</p></div>';
// 						}
// 						else
// 						{
// 							$body = json_decode($merch_response['body'], true);
// 							$name = $body['name'];
// 							$domain = $body['domain'];
// 							$notifyUrl = $body['notifyUrl'];
// 							update_option('layup_merchant_name', $name);
// 							update_option('layup_merchant_domain', esc_url_raw($domain));
// 							update_option('layup_merchant_notifyurl', esc_url_raw($notifyUrl));
// 						}
// 					}
// 					else
// 					{
// 						echo '<div class="error"><p>'
//  . __('There was an error, please try again', 'layup-gateway')
//  . '</p></div>';
// 					}
// 				}
// 				else
// 				{
// 					echo '<div class="error"><p>'
//  . __('Please make sure you have entered your API key in the payment settings before you enter your Merchant ID', 'layup-gateway')
//  . '</p></div>';
// 				}
// 				$settings = $this->layup_get_settings();
// 				WC_Admin_Settings::output_fields($settings);
// 			}
// 			/**
// 			 * Save settings
// 			 */
// 			public function layup_save()
// 			{
// 				if (array_key_exists('layup_merchant_name', $_POST))
// 				{
// 					if (esc_url_raw($_POST['layup_merchant_domain']) === $_POST['layup_merchant_domain'] && esc_url_raw($_POST['layup_merchant_notifyurl']) === $_POST['layup_merchant_notifyurl'])
// 					{
// 						global $woocommerce;
// 						echo 'layup';
// 						$gateway_id = 'layup';
// 						$gateways = WC_Payment_Gateways::instance();
// 						$gateway = $gateways->payment_gateways() [$gateway_id];
// 						$save_api_key_check = $gateway->api_key;
// 						$save_api_key = $gateway->api_key;
// 						$save_merchant_id = $_POST['layup_merchant_id'];
// 						if ($save_merchant_id !== '')
// 						{
// 							if ($gateway->testmode == 'yes')
// 							{
// 								$save_api_url = "https://sandbox-api.layup.co.za/";
// 							}
// 							else
// 							{
// 								$save_api_url = "https://api.layup.co.za/";
// 							}
// 							$save_merchant_details = array(
// 								'name' => sanitize_text_field($_POST['layup_merchant_name']) ,
// 								'domain' => sanitize_text_field($_POST['layup_merchant_domain']) ,
// 								'notifyUrl' => sanitize_text_field($_POST['layup_merchant_notifyurl'])
// 							);
// 							$save_merchant_details_json = json_encode($save_merchant_details, JSON_UNESCAPED_SLASHES);
// 							$save_headers = array(
// 								'accept' => 'application/json',
// 								'Content-Type' => 'application/json',
// 								'apikey' => $save_api_key,
// 							);
// 							$save_merchant_args = array(
// 								'method' => 'PUT',
// 								'headers' => $save_headers,
// 								'body' => $save_merchant_details_json
// 							);
// 							$save_merch_response = wp_remote_request($save_api_url . 'v1/merchants/' . $save_merchant_id, $save_merchant_args);


// 							if (!is_wp_error($save_merch_response))
// 							{
// 								if ($save_merch_response['body'] == 'Forbidden')
// 								{
// 									echo '<div class="error"><p>'
//  . __('The Merchant ID was invalid, please try again', 'layup-gateway')
//  . '</p></div>';
// 								}
// 							}
// 							else
// 							{
// 								echo '<div class="error"><p>'
//  . __('There was an error, please try again', 'layup-gateway')
//  . '</p></div>';


// 							}
// 						}
// 					}
// 					else
// 					{
// 						echo '<div class="error"><p>'
//  . __('There was an error, please try again', 'layup-gateway')
//  . '</p></div>';


// 					}
// 				}
// 				$settings = $this->layup_get_settings();
// 				WC_Admin_Settings::save_fields($settings);
// 			}
// 			/**
// 			 *  Show possible admin notices
// 			 */
// 			public function layup_admin_notices($merch_response)
// 			{
// 				if ($merch_response)
// 				{
// 					if ($merch_response['body'] == 'Forbidden')
// 					{
// 						echo '<div class="error"><p>'
//  . __('The Merchant ID was invalid, please try again', 'layup-gateway')
//  . '</p></div>';
// 					}
// 					else
// 					{
// 						return;
// 					}
// 				}
// 			}
// 		}
// 		return new WC_Settings_LayUp();
// 	}
// 	add_filter('woocommerce_get_settings_pages', 'layup_add_settings', 15);
// }

/**
 * Create the date LayUp checkbox field on product admin page
 */

function woo_add_layup_date_fields()
{

    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    $date_field_type = get_post_meta($post->ID, 'layup_date', true);

    $lu_curr_date = date('Y-m-d');

    $lu_min_date = date('Y-m-d', strtotime("+" . $gateway->lu_min_end_date . " months", strtotime($lu_curr_date)));

    $lu_max_date = date('Y-m-d', strtotime("+" . $gateway->lu_max_end_date . " months", strtotime($lu_curr_date)));

?>

<div class="options_group input_fields_wrap">
	<p class="form-field">
    	<a class="add_field_button button-secondary">Add Field</a>

    	<span class="description"><?php esc_attr(_e('Add a date if this product needs to be paid off before a given time frame. Make sure its after your minimum and before your maximum dates set in layup settings', 'woocommerce')); ?></span>

    <?php
    if (!empty($date_field_type))
    {

        $i = 0;

        foreach ($date_field_type as $date)
        {

?>

        <p class="form-field date_field_type">

            <span class="wrap">

                <label><?php echo esc_attr(__('Departure/Event Start Date', 'woocommerce')); ?></label>	

                <input placeholder="<?php esc_attr(_e('Start Date', 'woocommerce')); ?>" class="" type="date" max="<?php echo esc_attr($lu_max_date); ?>" min="<?php echo esc_attr($lu_min_date); ?>" name="layup_date[<?php echo esc_attr($i); ?>]" format="" value="<?php echo esc_attr($date); ?>"  style="width: 150px;" />

            </span><a href="#" class="remove_field">Remove</a>

        </p>

    <?php
            $i++;

        }

    }

?>
</p>
</div>

    <?php
}

add_action('woocommerce_product_options_inventory_product_data', 'woo_add_layup_date_fields');

add_action('admin_footer', 'layup_admin_footer_script');

function layup_admin_footer_script()
{

    global $post;

    if (is_object($post))
    {

        global $woocommerce;

        $gateway_id = 'layup';

        $gateways = WC_Payment_Gateways::instance();

        $gateway = $gateways->payment_gateways() [$gateway_id];

        $dates = get_post_meta($post->ID, 'layup_date', true);

        $x = - 1;

        if (is_array($dates))
        {

            $x = - 1;

            foreach ($dates as $date)
            {

                $x++;

            }

        }

        if ('product' == $post->post_type)
        {

            $curr_date = date('Y-m-d');

            $max_date = date('Y-m-d', strtotime("+" . $gateway->lu_max_end_date . " months", strtotime($curr_date)));

            $min_date = date('Y-m-d', strtotime("+" . $gateway->lu_min_end_date . " months", strtotime($curr_date)));

            echo '<script type="text/javascript">
                    jQuery(document).ready(function($) {
                        var today = new Date();
                        var date = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
                        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
                        var add_button      = $(".add_field_button"); //Add button ID
                        var x = ' . esc_attr($x) . '; //initlal text box count
                        $(add_button).click(function(e){ //on add input button click
                        e.preventDefault();
                        x++;
                        $(wrapper).append(`<p class="form-field date_field_type"><span class="wrap"><label>Departure/Event Date</label><input placeholder="Date" max="' . esc_attr($max_date) . '" min="' . esc_attr($min_date) . '" class="" type="date" name="layup_date[`+ x +`]" value=""  style="width: 150px;" /></span><a href="#" class="remove_field">Remove</a></p>`);
                        });
                        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                        e.preventDefault(); 
                        $(this).parent("p").remove();
                        })
                    });
                </script>';

        }
    }

}

/*
 * Display date select on single product page
*/

function layup_date_option()
{

    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    $curr_date = date('Y-m-d');

    $dates = get_post_meta($post->ID, 'layup_date', true);

    $min_date = date('Y-m-d', strtotime("+" . $gateway->lu_min_end_date . " months", strtotime($curr_date)));

    if (is_array($dates))
    {

        echo '<label>' . esc_attr(__('Select a date', 'woocomerce')) . '</label><select name="layup_date_sel"/>';

        foreach ($dates as $date)
        {

            if ($date >= $min_date)
            {

                echo '<option value="' . esc_attr($date) . '">' . esc_attr($date) . '</option>';

            }

        }

        echo '</select>';

    }

}

add_action('woocommerce_before_add_to_cart_button', 'layup_date_option', 9);

/*
 *   Validate when adding to cart
*/

function layup_add_to_cart_validation($passed, $product_id, $qty)
{

    if (isset($_POST['layup_date_sel']) && sanitize_text_field($_POST['layup_date_sel']) == '')
    {

        $product = wc_get_product($product_id);

        wc_add_notice(sprintf(__('%s cannot be added to the cart until you selet a date.', 'woocommerce') , $product->get_title()) , 'error');

        return false;

    }

    return $passed;

}

add_filter('woocommerce_add_to_cart_validation', 'layup_add_to_cart_validation', 10, 3);

/*
 * Add date data to the cart item
*/

function layup_add_cart_item_date($cart_item, $product_id)
{

    if (isset($_POST['layup_date_sel']))
    {

        $cart_item['layup_date_sel'] = sanitize_text_field($_POST['layup_date_sel']);

    }

    return $cart_item;

}

add_filter('woocommerce_add_cart_item_data', 'layup_add_cart_item_date', 10, 2);

/*
 * Load cart data from session
*/

function layup_get_cart_item_from_session($cart_item, $values)
{

    if (isset($values['layup_date_sel']))
    {

        $cart_item['layup_date_sel'] = $values['layup_date_sel'];

    }

    return $cart_item;

}

add_filter('woocommerce_get_cart_item_from_session', 'layup_get_cart_item_from_session', 20, 2);

/*
 * Get item date to display in cart
*/

function layup_get_item_date($other_data, $cart_item)
{

    if (isset($cart_item['layup_date_sel']))
    {

        $other_data[] = array(

            'name' => __('Date', 'woocommerce') ,

            'value' => $cart_item['layup_date_sel']

        );

    }

    return $other_data;

}

add_filter('woocommerce_get_item_data', 'layup_get_item_date', 10, 2);

/*
 * Show date in order overview
*/

function layup_order_item_product($cart_item, $order_item)
{

    if (isset($order_item['layup_date_sel']))
    {

        $cart_item_meta['layup_date_sel'] = $order_item['layup_date_sel'];

    }

    return $cart_item;

}

add_filter('woocommerce_order_item_product', 'layup_order_item_product', 10, 2);

/*
 * Add the date to order emails
*/

function layup_email_order_meta_fields($fields)
{

    $fields['layup_date_sel'] = __('Date', 'woocommerce');

    return $fields;

}

add_filter('woocommerce_email_order_meta_fields', 'layup_email_order_meta_fields');

/**
 * Save date to order
 */

function layup_save_date_to_order_items($item, $cart_item_key, $values, $order)
{

    if (empty($values['layup_date_sel']))
    {

        return;

    }

    $order->add_meta_data('layup_date_sel', $values['layup_date_sel']);

    $item->add_meta_data(__('Date', 'woocommerce') , $values['layup_date_sel']);

}

add_action('woocommerce_checkout_create_order_line_item', 'layup_save_date_to_order_items', 10, 4);

/**
 * Create the disable LayUp checkbox field on product admin page
 */

function create_layup_disable_field()
{

    $args = array(

        'id' => 'layup_disable',

        'label' => __('Disable LayUp checkout', 'layup-gateway') ,

        'class' => 'lu-layup-disable',

        'desc_tip' => true,

        'description' => __('Check this box if you don`t want customers to be able to checkout using the LayUp gateway with this product in their cart.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_disable_field');

/**
 * Create the custom deposit LayUp checkbox field on product admin page
 */

function create_layup_custom_deposit_field()
{

    $args = array(

        'id' => 'layup_custom_deposit',

        'label' => __('Use custom layup deposit for this product', 'layup-gateway') ,

        'class' => 'lu-custom-deposit',

        'desc_tip' => true,

        'description' => __('Check this box if you want this product to use its own deposit type and amount for layup checkout.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_deposit_field');

/**
 * Create the custom deposit type LayUp field on product admin page
 */

function create_layup_custom_deposit_type_field()
{

    $args = array(

        'id' => 'layup_custom_deposit_type',

        'label' => __('Select a deposit type', 'layup-gateway') ,

        'options' => array(
            'PERCENTAGE' => 'Percentage',
            'INSTALMENT' => 'First instalment',
            'FLAT' => 'Flat fee'
        ) ,

        'class' => 'lu-custom-deposit-type',

        'desc_tip' => false,

        'description' => __('<br><br>Select one of the following deposit types, required to initiate a payment plan and activate an order, applicable to all payment plans created by this Merchant Account.<br>
		Percentage: Define a percentage of the total order value e.g. 10%.<br>
		First Instalment: Deposit equal to the instalment value determined by the customer according to the payment plan duration e.g. R5,000 order paid over 5 months = R1,000 deposit.<br>
		Flat Fee: Define a specific amount (lower than the max order value) e.g. R150.', 'layup-gateway') ,

        'default' => 'PERCENTAGE'
    );

    woocommerce_wp_select($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_deposit_type_field');

/**
 * Create the custom deposit amount LayUp field on product admin page
 */

function create_layup_custom_deposit_amount_field()
{

    $args = array(

        'id' => 'layup_custom_deposit_amount',

        'label' => __('Choose a deposit amount (only applicable if pecentage or flat fee is chosen for the deposit type.)', 'layup-gateway') ,

        'class' => 'lu-custom-deposit-amount',

        'desc_tip' => true,

        'description' => __('The deposit amount that you want your customers to pay when checking out with LayUp.', 'layup-gateway') ,

        'default' => '20'
    );

    woocommerce_wp_text_input($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_deposit_amount_field');

/**
 * Create the custom months LayUp checkbox field on product admin page
 */

function create_layup_custom_months_field()
{

    $args = array(

        'id' => 'layup_custom_months',

        'label' => __('Use custom layup Months for this product', 'layup-gateway') ,

        'class' => 'lu-custom-months',

        'desc_tip' => true,

        'description' => __('Check this box if you want this product to use its own Max and Min Months for layup checkout.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_months_field');

/**
 * Create the custom months min LayUp field on product admin page
 */

function create_layup_custom_months_min_field()
{

    $args = array(

        'id' => 'layup_custom_months_min',

        'label' => __('Minimum months', 'layup-gateway') ,

        'class' => 'lu-custom-months-min',

        'desc_tip' => true,

        'description' => __('Choose a minimum amount of months for the payment plan.', 'layup-gateway') ,

    );

    woocommerce_wp_text_input($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_months_min_field');

/**
 * Create the custom months max LayUp field on product admin page
 */

function create_layup_custom_months_max_field()
{

    $args = array(

        'id' => 'layup_custom_months_max',

        'label' => __('Maximum months', 'layup-gateway') ,

        'class' => 'lu-custom-months-max',

        'desc_tip' => true,

        'description' => __('Choose a maximum amount of months for the payment plan.', 'layup-gateway') ,

    );

    woocommerce_wp_text_input($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_months_max_field');

/**
 * Create the custom Payment plan preview LayUp checkbox field on product admin page
 */

function create_layup_custom_payment_plan_field()
{

    $args = array(

        'id' => 'layup_custom_payment_plan',

        'label' => __('Use custom layup payment plan preview for this product', 'layup-gateway') ,

        'class' => 'lu-custom-payment-plan',

        'desc_tip' => true,

        'description' => __('Check this box if you want this product to use its own payment plan preview on the product page.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_payment_plan_field');

/**
 * Create the custom Payment plan preview LayUp field on product admin page
 */

function create_layup_custom_payment_plan_template_field()
{

    $args = array(

        'id' => 'layup_custom_payment_plan_template',

        'label' => __('Payment plan preview', 'layup-gateway') ,

        'class' => 'lu-custom-payment-plan-template',

        'desc_tip' => true,

        'description' => __('Configure your own payment plan preview. You can make use the following variables: "{amount}", "{months}", "{deposit}". ie "Pay only {deposit} and {amount}/pm for {months} months". For default leave blank.', 'layup-gateway') ,

    );

    woocommerce_wp_text_input($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_payment_plan_template_field');

/**
 * Create the custom Payment plan preview LayUp checkbox field on product admin page
 */

function create_layup_custom_learn_more_field()
{

    $args = array(

        'id' => 'layup_custom_learn_more',

        'label' => __('Use custom layup Learn more popup style for this product', 'layup-gateway') ,

        'class' => 'lu-custom-learn-more',

        'desc_tip' => true,

        'description' => __('Check this box if you want this product to use its own Learn more popup style on the product page.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_learn_more_field');

/**
 * Create the custom Learn more popup style LayUp field on product admin page
 */

function create_layup_custom_learn_more_popup_field()
{

    $args = array(

        'id' => 'layup_custom_learn_more_popup',

        'label' => __('Learn more popup style', 'layup-gateway') ,

        'options' => array(
            'layby' => 'Layby',
            'subscription' => 'Subscription'
        ) ,

        'class' => 'lu-custom-learn-more-popup',

        'desc_tip' => false,

        'description' => __('Change between a Layby style or a Subscription style depending on product offering', 'layup-gateway') ,

        'default' => 'layby'
    );

    woocommerce_wp_select($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_learn_more_popup_field');


function create_use_layup_custom_product_type_field()
{

    $args = array(

        'id' => 'layup_use_custom_product_type',

        'label' => __('Use custom product type for this product', 'layup-gateway') ,

        'class' => 'lu-use-custom-product-type',

        'desc_tip' => true,

        'description' => __('Check this box if you want this product to use its own product type on the product page.', 'layup-gateway') ,

    );

    woocommerce_wp_checkbox($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_use_layup_custom_product_type_field');

function create_layup_custom_product_type_field()
{

    $args = array(

        'id' => 'layup_custom_product_type',

        'label' => __('Product type', 'layup-gateway') ,

        'options' => array(
            'layby' => 'Layby',
            'subscription' => 'Subscription',
            'pre-order' => 'Pre-order'
        ) ,

        'class' => 'lu-custom-product-type',

        'desc_tip' => false,

        'description' => __('Change between a Layby, Subscription or Pre-order depending on your product offering', 'layup-gateway') ,

        'default' => 'layby'
    );

    woocommerce_wp_select($args);

}

add_action('woocommerce_product_options_inventory_product_data', 'create_layup_custom_product_type_field');


/**
 * Save the LayUp product fields
 */
function save_layup_disable_field($post_id)
{

    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    $product = wc_get_product($post_id);

    $layup_disable = isset($_POST['layup_disable']) ? sanitize_text_field($_POST['layup_disable']) : '';
    $layup_custom_deposit = isset($_POST['layup_custom_deposit']) ? sanitize_text_field($_POST['layup_custom_deposit']) : '';
    $layup_custom_deposit_type = isset($_POST['layup_custom_deposit_type']) ? sanitize_text_field($_POST['layup_custom_deposit_type']) : '';
    $layup_custom_deposit_amount = isset($_POST['layup_custom_deposit_amount']) ? sanitize_text_field($_POST['layup_custom_deposit_amount']) : '';

    $layup_custom_months = isset($_POST['layup_custom_months']) ? sanitize_text_field($_POST['layup_custom_months']) : '';
    $layup_custom_months_min = isset($_POST['layup_custom_months_min']) ? sanitize_text_field($_POST['layup_custom_months_min']) : '';
    $layup_custom_months_max = isset($_POST['layup_custom_months_max']) ? sanitize_text_field($_POST['layup_custom_months_max']) : '';

    $layup_custom_payment_plan_template = isset($_POST['layup_custom_payment_plan_template']) ? sanitize_text_field($_POST['layup_custom_payment_plan_template']) : '';
    $layup_custom_learn_more_popup = isset($_POST['layup_custom_learn_more_popup']) ? sanitize_text_field($_POST['layup_custom_learn_more_popup']) : '';

    $layup_custom_payment_plan = isset($_POST['layup_custom_payment_plan']) ? sanitize_text_field($_POST['layup_custom_payment_plan']) : '';
    $layup_custom_learn_more = isset($_POST['layup_custom_learn_more']) ? sanitize_text_field($_POST['layup_custom_learn_more']) : '';

    $layup_custom_product_type = isset($_POST['layup_custom_product_type']) ? sanitize_text_field($_POST['layup_custom_product_type']) : '';
    $layup_use_custom_product_type = isset($_POST['layup_use_custom_product_type']) ? sanitize_text_field($_POST['layup_use_custom_product_type']) : '';

    settype($layup_custom_months_max, 'int');
    $product->update_meta_data('layup_disable', $layup_disable);
    $product->update_meta_data('layup_custom_deposit', $layup_custom_deposit);
    $product->update_meta_data('layup_custom_deposit_type', $layup_custom_deposit_type);
    $product->update_meta_data('layup_custom_deposit_amount', $layup_custom_deposit_amount);

    $product->update_meta_data('layup_custom_months', $layup_custom_months);
    $product->update_meta_data('layup_custom_months_min', $layup_custom_months_min);
    $product->update_meta_data('layup_custom_months_max', $layup_custom_months_max);

    $product->update_meta_data('layup_custom_payment_plan_template', $layup_custom_payment_plan_template);
    $product->update_meta_data('layup_custom_learn_more_popup', $layup_custom_learn_more_popup);

    $product->update_meta_data('layup_custom_payment_plan', $layup_custom_payment_plan);
    $product->update_meta_data('layup_custom_learn_more', $layup_custom_learn_more);

    $product->update_meta_data('layup_custom_product_type', $layup_custom_product_type);
    $product->update_meta_data('layup_use_custom_product_type', $layup_use_custom_product_type);

    $price = (float)$product->get_price() * 100;

    if ($_POST['layup_date'] != '')
    {

        foreach ($_POST['layup_date'] as $postdate)
        {

            $d = DateTime::createFromFormat('Y-m-d', $postdate);

            $valid_date = $d && $d->format('Y-m-d') === $postdate;

            if ($valid_date == false)
            {

                unset($_POST['layup_date']);

                break;

            }

        }
    }

    $dates = isset($_POST['layup_date']) ? preg_replace("([^0-9-])", "", $_POST['layup_date']) : '';

    $product->update_meta_data('layup_date', $dates);

    $lu_curr_date = date('c');

    $product->save();

}

add_action('woocommerce_process_product_meta', 'save_layup_disable_field');

/**
 * Display LayUp icon and estimate text on single product page
 */

function layup_display_icon()
{

    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    // Check for the Disable LayUp field value
    

    $product = wc_get_product($post->ID);

    $price = (float)$product->get_price();

    $layup_disable_meta = $product->get_meta('layup_disable');

    if ($gateway->payplan_disp == 'yes')
    {

        if ($layup_disable_meta != 'yes')
        {

            // Only display LayUp icon if Disable LayUp field is not checked


            $layup_custom_deposit = $product->get_meta('layup_custom_deposit');
            $layup_custom_deposit_type = $product->get_meta('layup_custom_deposit_type');
            $layup_custom_deposit_amount = $product->get_meta('layup_custom_deposit_amount');
            $layup_custom_months = $product->get_meta('layup_custom_months');
            $layup_custom_months_min = $product->get_meta('layup_custom_months_min');
            $layup_custom_months_max = $product->get_meta('layup_custom_months_max');
            $layup_custom_payment_plan = $product->get_meta('layup_custom_payment_plan');
            $layup_custom_payment_plan_template = $product->get_meta('layup_custom_payment_plan_template');
            $layup_custom_learn_more = $product->get_meta('layup_custom_learn_more');
            $layup_custom_learn_more_popup = $product->get_meta('layup_custom_learn_more_popup');

            $layup_use_custom_product_type = $product->get_meta('layup_use_custom_product_type');
            $layup_custom_product_type = $product->get_meta('layup_custom_product_type');

            if ($layup_custom_deposit == 'yes' && $layup_custom_deposit_type != "" && $layup_custom_deposit_amount != ""){
                $layup_preview_deposit_type = $layup_custom_deposit_type;
                $layup_preview_deposit_amount = $layup_custom_deposit_amount;
            } else {
                $layup_preview_deposit_type = $gateway->layup_dep_type;
                $layup_preview_deposit_amount = $gateway->layup_dep;
            }

            if ($layup_custom_months == 'yes' && $layup_custom_months_max != ""){
                $layup_preview_months = $layup_custom_months_max;
                $layup_preview_months_min = $layup_custom_months_min;
            } else {
                $layup_preview_months = $gateway->lu_max_end_date;
                $layup_preview_months_min = $gateway->lu_min_end_date;
            }

            if ($layup_custom_payment_plan == 'yes' && $layup_custom_payment_plan_template != ""){
                $layup_preview_payment_plan_template = $layup_custom_payment_plan_template;
            } else {
                $layup_preview_payment_plan_template = $gateway->payment_plan_template;
            }

            if ($layup_custom_learn_more == 'yes' && $layup_custom_learn_more_popup != ""){
                $layup_preview_learn_more_popup = $layup_custom_learn_more_popup;
            } else {
                $layup_preview_learn_more_popup = $gateway->learn_more_style;
            }

            if ($layup_use_custom_product_type == 'yes' && $layup_custom_product_type != ""){
                $layup_preview_product_type = $layup_custom_product_type;
            } else {
                $layup_preview_product_type = $gateway->product_type;
            }


            $priceNoDep = 0;
            $newInstalment = 0;
            $months = $layup_preview_months;
            if ($layup_preview_deposit_type == 'FLAT')
            {
                $priceNoDep = $price - $layup_preview_deposit_amount;
                $newInstalment = $priceNoDep / $months;
            }
            else if ($layup_preview_deposit_type == 'PERCENTAGE')
            {
                $deposit = $layup_preview_deposit_amount / 100 * $price;
                $priceNoDep = $price - $deposit;
                $newInstalment = $priceNoDep / $months;
            }
            else if ($layup_preview_deposit_type == 'INSTALMENT')
            {
                $months = $months + 1;
                $newInstalment = $price / $months;
            }
            $layup_preview_amount = number_format($newInstalment, 2);
            

            if ($layup_preview_deposit_type == 'PERCENTAGE')
            {
                $layup_preview_deposit = $layup_preview_deposit_amount . '%';
            }
            elseif ($layup_preview_deposit_type == 'FLAT')
            {
                $layup_preview_deposit = 'R' . $layup_preview_deposit_amount;
            }
            elseif ($layup_preview_deposit_type == 'INSTALMENT')
            {
                $layup_preview_deposit = '';
                $layup_preview_months = $layup_preview_months + 1;
            }

            if ($layup_preview_months != 0)
            {
                
                if ($layup_preview_learn_more_popup == 'subscription')
                {
                    $learn_more_url = "https://layup.co.za/learn-more-popup-subscription/";
                } else {
                    $learn_more_url = "https://layup.co.za/learn-more-popup-layby/";
                }

                if ($layup_preview_product_type == 'pre-order')
                {
                    $button_text = "PRE-ORDER WITH";
                } elseif($layup_preview_product_type == 'subscription') {
                    $button_text = "SUBSCRIBE WITH";
                } else {
                    $button_text = "PAY IT OFF WITH";
                }
                
                $finalString = "";

                if ($layup_preview_payment_plan_template == "") {

                    $finalString = '<p style="margin: 0px; width: 70%; ">From <span class="layup-installment-amount">R' . esc_attr($layup_preview_amount) . '/pm</span> for <span class="layup-months-amount">' . esc_attr($layup_preview_months) . '</span> months.<br>Interest-free. | Deposit: <span class="layup-deposit-amount">' . esc_attr($layup_preview_deposit) . '</span></p>';
                } else {

                    if ($layup_preview_deposit_type == 'PERCENTAGE')
                    {
                        $layupDepositFormat = $layup_preview_deposit_amount . '%';
                    }
                    elseif ($layup_preview_deposit_type == 'FLAT')
                    {
                        $layupDepositFormat = 'R' . $layup_preview_deposit_amount;
                    }
                    elseif ($layup_preview_deposit_type == 'INSTALMENT')
                    {
                        $layupDepositFormat = 'R'.$layup_preview_amount;
                    }

                    $finalString = str_replace('{amount}', '<span class="layup-installment-amount">R' . esc_attr($layup_preview_amount) . '/pm</span>', $layup_preview_payment_plan_template);
                    $finalString = str_replace('{months}', '<span class="layup-months-amount">'.esc_attr($layup_preview_months).'</span>', $finalString);
                    
                    $finalString = str_replace('{deposit}', '<span class="layup-deposit-amount">'.esc_attr($layupDepositFormat).'</span>', $finalString);
                    $finalString = "<p style='margin: 0px; width: 70%;'>".$finalString."</p>";
                }

                echo '<div style="align-items: center;max-width: 100%;">
			<div style="display: flex;font-family:Quicksand,Arial, Helvetica, sans-serif ;padding: 10px 30px 10px 30px;margin-top: 15px;color: #ffffff;text-align: center;align-items: center;background-color: #0C4152;box-sizing: border-box;justify-content: center;border-radius:3px;"
				class="btn-layup">
		
				<div class="btn-layup-text">'.esc_attr($button_text).'</div>
		
				<div class="btn-layup-logo"><img style="top: 0 !important; vertical-align: middle; border-style: none"
					src="' . plugin_dir_url(dirname(__FILE__)) . 'img/layup-logo-light.svg">
				</div>
			</div>
		
			<div style="font-family:Quicksand, Arial, Helvetica, sans-serif ;font-weight: 500;display: flex;align-items: center;gap: 20px;justify-content: space-between;flex-wrap: nowrap;" class="btn-est-layup">
		
				' .$finalString. '
				<span id="lumodallink" style="color:#1295a5;">Learn More</span>

            </div>
		</div>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
				<style>
                /* The Modal (background) */
		
                .btn-layup {
                    
                    margin-right:0px;
                }
    
                .btn-est-layup {
                    max-width: 100%;
                    padding: 10px 0px;
                    margin-left:0px;
                    font-size: 16px;
                    color: #0C4152;
                }
    
                .btn-layup-text {
                    padding: 10px;
                    font-size: 16px;
                    font-weight: 500;
                }

                .btn-layup-logo {
                    padding: 10px;
                }

                .layup-installment-amount, .layup-months-amount, .layup-deposit-amount {
                    font-weight: 700;
                }

                #lumodallink {
                    font-weight: 700;
                }

                .btn-layup-logo img {
                    width: 125px;
                }

                @media (max-width: 600px) {
		
                    .btn-layup-logo img {
                        width: 80px;
                    }

                    .btn-layup-text {
                        font-size: 14px;
                    }

                    .btn-est-layup {
                        font-size: 13px;
                    }
    
                }
		
					.lumodal {
						font-family: "Quicksand", serif !important;
						display: none;
						/* Hidden by default */
						position: fixed;
						/* Stay in place */
						z-index: 99999;
						/* Sit on top */
						padding-top: 100px;
						/* Location of the box */
						left: 0;
						top: 0;
						width: 100%;
						/* Full width */
						height: 100%;
						/* Full height */
						overflow: auto;
						/* Enable scroll if needed */
						background-color: rgb(0, 0, 0);
						/* Fallback color */
						background-color: rgba(0, 0, 0, 0.4);
						/* Black w/ opacity */
					}
		
					/* Modal Content */
					.lumodal-content {
						background-color: #f7f9fc;
						margin: auto;
						padding: 10px;
						border: 1px solid #888;
						width: 60%;
						text-align: center;
						overflow: auto;
					}
		
					.lumodal-content .center {
						display: block;
						margin-left: auto;
						margin-right: auto;
		
					}
		
					.lumodal-content .lu-modal-col {
						float: left;
						width: 31.33%;
						margin: 1%;
						margin-bottom: 1em;
						padding: 2%;
		
					}
		
					@media (max-width: 600px) {
		
						/* CSS that should be displayed if width is equal to or less than 600px goes here */
						.lumodal-content {
							width: 80%;
						}
		
						.lumodal-content .lu-modal-col {
							width: 80%;
							margin: 0 auto;
							display: table;
							float: none;
						}
		
						.lumodal {
							padding-top: 50px;
						}
					}
		
					.lumodal-content .lu-modal-col:nth-of-type(3n+4) {
						clear: left;
					}
		
					/* The Close Button */
					.luclose {
						color: #aaaaaa;
						float: right;
						font-size: 28px;
						font-weight: bold;
						border-radius: 5px;
						border: #808080 solid 1px;
						line-height: 0;
						padding: 14px 10px 14px 10px;
					}
		
					.luclose:hover,
					.luclose:focus {
						color: #000;
						text-decoration: none;
						cursor: pointer;
					}
		
					#lumodallink:hover {
						text-decoration: underline;
						cursor: pointer;
					}
				</style>
		
				<script>

                jQuery(document).ready(function(){

                    jQuery( "body" ).prepend( `<div id="lumyModal" class="lumodal"><div class="lumodal-content"><span class="luclose">×</span><br><br></div></div>` );

                    // Get the modal
					var modal = document.getElementById("lumyModal");
		
					// Get the button that opens the modal
					var btn = document.getElementById("lumodallink");
		
					// Get the <span> element that closes the modal
					var span = document.getElementsByClassName("luclose")[0];
		
					// When the user clicks the button, open the modal 
					btn.onclick = function () {
                        jQuery( "#lumyModal .lumodal-content" ).append( `<iframe id="layup-iframe" title="LayUp - How it works" src="'.$learn_more_url.'" width="100%" scrolling="no"></iframe>` );
                        let iframe=document.querySelector("#layup-iframe");window.addEventListener("message",function(e){let t=e.data;iframe.style.height=t.height+"px"; e.data;iframe.style.border="none";},false);
                        modal.style.display = "block";
					}
		
					// When the user clicks on <span> (x), close the modal
					span.onclick = function () {
						modal.style.display = "none";
                        jQuery("#layup-iframe").remove();
					}
		
					// When the user clicks anywhere outside of the modal, close it
					window.onclick = function (event) {
						if (event.target == modal) {
							modal.style.display = "none";
                            jQuery("#layup-iframe").remove();
						}
					}

                });

				jQuery( document ).ready(function() {
				
					let selector = "variation_id";
					jQuery(`input[name=${selector}]`).change(function() {
						let value = jQuery(this).val();
						
						variantUpdateEvent(value);
					});
				
				});

				variantUpdateEvent = function (e) {
					// assume were only dealing with a single product
					let variants = JSON.parse(document.querySelector(".variations_form").getAttribute("data-product_variations"));
					if(variants !== false){

						let variantObject = variants.find(function (element) {
							return element.variation_id.toString() === e.toString();
						});
						
						if (variantObject === undefined) {
							console.warn("Could not find variant data")
						}
						
						updatePreview(variantObject);
					}
				};

				updatePreview = function(variant) {
					if(typeof variant !== "undefined") {
					let price = variant.display_price.toFixed(2);
					let priceNoDep = 0;
					let newInstalment = 0;
                    let deposit = document.querySelector(".layup-deposit-amount");
					
                    let months = document.querySelector(".layup-months-amount");
                    if(months != null){
					    months = parseInt(months.innerHTML);
                    } else {
                        months = 0;
                    }
                    if(deposit != null){
					    deposit = deposit.innerHTML;
                        if (deposit.startsWith("R")) {
                            deposit = deposit.substring(1).replace(/[^\d.-]/g, "");
                            priceNoDep = price - deposit;
                            newInstalment = priceNoDep / months;
                        } else if(deposit.endsWith("%")) {
                          deposit = deposit.slice(0, -1).replace(/[^\d.-]/g, "");
                          deposit = deposit / 100 * price;
                          priceNoDep = price - deposit;
                          newInstalment = priceNoDep / months;
                        } else if(deposit == "") {
                            deposit = deposit.slice(0, -1);
                            newInstalment = price / months;
                        }
                    }
					console.log(newInstalment);
                    if(document.querySelector(".layup-installment-amount") != null){
					    document.querySelector(".layup-installment-amount").innerHTML = newInstalment.toFixed(2);
                    }
					
				}


				}

				</script>';
                
                if ($gateway->popup_warning == "yes") {

                    if ($gateway->dynamic_deposit != "yes"){
                    
                    $cart_products = $woocommerce->cart->cart_contents;
        
                    // Build product array
                    $custom_dep_inarray = false;
        
                    $check_dep_type_comp = [];
                    $check_dep_amount_comp = [];
                    $check_dep_months_min_comp = [];
                    $check_dep_months_max_comp = [];
        
                    foreach ($cart_products as $cart_item_key => $cart_item)
                    {
        
                        $cart_product = $cart_item['data'];
        
                        if ($cart_product->is_type('variation'))
                        {
                            $cart_product = wc_get_product($cart_product->get_parent_id());
        
                        }
        
                        $layup_custom_deposit_comp = get_post_meta($cart_product->get_id() , 'layup_custom_deposit', true);
                        $layup_custom_deposit_type_comp = get_post_meta($cart_product->get_id() , 'layup_custom_deposit_type', true);
                        $layup_custom_deposit_amount_comp = get_post_meta($cart_product->get_id() , 'layup_custom_deposit_amount', true);
                        $layup_custom_months_comp = get_post_meta($cart_product->get_id() , 'layup_custom_months', true);
                        $layup_custom_months_min_comp = get_post_meta($cart_product->get_id() , 'layup_custom_months_min', true);
                        $layup_custom_months_max_comp = get_post_meta($cart_product->get_id() , 'layup_custom_months_max', true);
        
                        if ($layup_custom_deposit_comp == "yes")
                        {
                            array_push($check_dep_type_comp, $layup_custom_deposit_type_comp);
                            array_push($check_dep_amount_comp, $layup_custom_deposit_amount_comp);
                        }
                        else
                        {
                            array_push($check_dep_type_comp, $gateway->layup_dep_type);
                            array_push($check_dep_amount_comp, $gateway->layup_dep);
                        }
                        
                        if ($layup_custom_months_comp == "yes")
                        {
                            array_push($check_dep_months_min_comp, $layup_custom_months_min_comp);
                            array_push($check_dep_months_max_comp, $layup_custom_months_max_comp);
                        }
                        else
                        {
                            array_push($check_dep_months_min_comp, $gateway->lu_min_end_date);
                            array_push($check_dep_months_max_comp, $gateway->lu_max_end_date);
                        }
        
                    }

                    array_push($check_dep_type_comp, $layup_preview_deposit_type);
                    array_push($check_dep_amount_comp, $layup_preview_deposit_amount);
                    array_push($check_dep_months_min_comp, $layup_preview_months_min);
                    array_push($check_dep_months_max_comp, $layup_preview_months);

                    if (count(array_unique($check_dep_type_comp)) > 1 && count(array_unique($check_dep_amount_comp)) > 1 && count(array_unique($check_dep_months_min_comp)) > 1 && count(array_unique($check_dep_months_max_comp)) > 1)
                    {

                        echo '<script>

                        jQuery(document).ready(function(){
                            
                            document.querySelector("form.cart button[type=submit]").addEventListener("click", function(event){
                                event.preventDefault();
                            });

                            jQuery( "body" ).prepend( `<div id="lumyModalComp" class="lumodal"><div class="lumodal-content"><span class="luclose">×</span><br><br></div></div>` );

                            // Get the modal
                            var luModal = document.getElementById("lumyModalComp");
                
                            // Get the button that opens the modal
                            var luBtn = document.querySelector("form.cart button[type=submit]");
                            console.log(luBtn);
                            // Get the <span> element that closes the modal
                            var luSpan = document.getElementsByClassName("luclose")[0];
                
                            // When the user clicks the button, open the modal 
                            luBtn.onclick = function () {
                            
                                jQuery( "#lumyModalComp .lumodal-content" ).append( `<div id="lu-warning"><p>Products currently in your basket have different deposit configurations than this one. Are you sure you want to add this to your basket?</p><button class="luContinue" style="width:40%;background:#1295a5;color:#FFF;padding:15px;margin:5px;border:0px;cursor: pointer;" >Continue</button><button class="luCancel" style="width:40%;background:#1295a5;color:#FFF;padding:15px;margin:5px;border:0px;cursor: pointer;" >Cancel</button></div>` );
                                
                                luModal.style.display = "block";
                                var luCont = document.querySelector("button.luContinue");
                                luCont.onclick = function () {
                                    document.querySelector("form.cart").submit();
                                }
                                var luCancel = document.querySelector("button.luCancel");
                                luCancel.onclick = function () {
                                    luModal.style.display = "none";
                                    jQuery("#lu-warning").remove();
                                }
                            }
                
                            // When the user clicks on <span> (x), close the modal
                            luSpan.onclick = function () {
                                luModal.style.display = "none";
                                jQuery("#lu-warning").remove();
                            }
                
                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function (event) {
                                if (event.target == luModal) {
                                    luModal.style.display = "none";
                                    jQuery("#lu-warning").remove();
                                }
                            }

                        });
                        </script>';
                        }
                    }
                }
            }

        }

    }

}

add_action('woocommerce_after_add_to_cart_form', 'layup_display_icon', 30);

/**
 * Display LayUp extimate text on shop page
 */

function layup_display_estimate()
{

    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    // Check for the Disable LayUp field value
    $product = wc_get_product($post->ID);
    
    if (is_object($product))
    {
        $price = (float)$product->get_price();

        $layup_disable_meta = $product->get_meta('layup_disable');

        if ($gateway->payplan_disp == 'yes')
        {

            if ($layup_disable_meta != 'yes')
            {

                // Only display LayUp icon if Disable LayUp field is not checked
                

            $layup_custom_deposit = $product->get_meta('layup_custom_deposit');
            $layup_custom_deposit_type = $product->get_meta('layup_custom_deposit_type');
            $layup_custom_deposit_amount = $product->get_meta('layup_custom_deposit_amount');
            $layup_custom_months = $product->get_meta('layup_custom_months');
            $layup_custom_months_min = $product->get_meta('layup_custom_months_min');
            $layup_custom_months_max = $product->get_meta('layup_custom_months_max');
            $layup_custom_payment_plan = $product->get_meta('layup_custom_payment_plan');
            $layup_custom_payment_plan_template = $product->get_meta('layup_custom_payment_plan_template');


            if ($layup_custom_deposit == 'yes' && $layup_custom_deposit_type != "" && $layup_custom_deposit_amount != ""){
                $layup_preview_deposit_type = $layup_custom_deposit_type;
                $layup_preview_deposit_amount = $layup_custom_deposit_amount;
            } else {
                $layup_preview_deposit_type = $gateway->layup_dep_type;
                $layup_preview_deposit_amount = $gateway->layup_dep;
            }

            if ($layup_custom_months == 'yes' && $layup_custom_months_max != ""){
                $layup_preview_months = $layup_custom_months_max;
            } else {
                $layup_preview_months = $gateway->lu_max_end_date;
            }

            if ($layup_custom_payment_plan == 'yes' && $layup_custom_payment_plan_template != ""){
                $layup_preview_payment_plan_template = $layup_custom_payment_plan_template;
            } else {
                $layup_preview_payment_plan_template = $gateway->payment_plan_template;
            }

            $priceNoDep = 0;
            $newInstalment = 0;
            $months = $layup_preview_months;
            if ($layup_preview_deposit_type == 'FLAT')
            {
                $priceNoDep = $price - $layup_preview_deposit_amount;
                $newInstalment = $priceNoDep / $months;
            }
            else if ($layup_preview_deposit_type == 'PERCENTAGE')
            {
                $deposit = $layup_preview_deposit_amount / 100 * $price;
                $priceNoDep = $price - $deposit;
                $newInstalment = $priceNoDep / $months;
            }
            else if ($layup_preview_deposit_type == 'INSTALMENT')
            {
                $months = $months + 1;
                $newInstalment = $price / $months;
            }
            $layup_preview_amount = number_format($newInstalment, 2);

                if ($layup_preview_deposit_type == 'PERCENTAGE')
                {
                    $layup_preview_deposit = $layup_preview_deposit_amount . '%';
                }
                elseif ($layup_preview_deposit_type == 'FLAT')
                {
                    $layup_preview_deposit = 'R' . $layup_preview_deposit_amount;
                }
                elseif ($layup_preview_deposit_type == 'INSTALMENT')
                {
                    $layup_preview_deposit = '';
                    $layup_preview_months = $layup_preview_months + 1;
                }

                if ($layup_preview_months != 0 && $layup_preview_months != null)
                {

                    if ($layup_preview_payment_plan_template == "") {

                        echo '<div style="font-size: 12px;margin-bottom: 10px;" class="est-layup">
	                    From R' . esc_attr($layup_preview_amount) . '/month for ' . esc_attr($layup_preview_months) . ' months. ' . esc_attr($layup_preview_deposit) .'
                        </div>';
                    } else {
                        $finalString = str_replace('{amount}', 'R' . esc_attr($layup_preview_amount), $layup_preview_payment_plan_template);
                        $finalString = str_replace('{months}', esc_attr($layup_preview_months), $finalString);
                        if ($layup_preview_deposit_type == 'PERCENTAGE')
                    {
                        $finalString = str_replace('{deposit}', esc_attr($layup_preview_deposit), $finalString);
                    } elseif($layup_preview_deposit_type == 'FLAT'){
                        $finalString = str_replace('{deposit}', esc_attr($layup_preview_deposit), $finalString);
                    } elseif($layup_preview_deposit_type == 'INSTALMENT'){
                        $finalString = str_replace('{deposit}', 'R'.esc_attr($layup_preview_amount), $finalString);
                    }

                    echo '<div style="font-size: 12px;margin-bottom: 10px;" class="est-layup">'.$finalString.'</div>';
                        
                    }

                }

            }

        }
    }

}

add_action('woocommerce_after_shop_loop_item_title', 'layup_display_estimate', 20);

function my_error_notice()
{
    global $post;

    global $woocommerce;

    $gateway_id = 'layup';

    $gateways = WC_Payment_Gateways::instance();

    $gateway = $gateways->payment_gateways() [$gateway_id];

    if ($gateway->api_key == '')
    {

?>



    <div class="error notice">



        <p><?php _e('Offering customers the option to pay with LayUp payment plans at checkout. ', 'woocommerce'); ?>



        <a target="_blank" href="https://layup.co.za/contact-us/"><?php _e('Register for a LayUp merchant account ', 'woocommerce'); ?></a><?php _e(' and start offering payment plans today. If you already have one please fill out your merchant details ', 'woocommerce'); ?>



        <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=layup'); ?>"><?php _e('here.', 'woocommerce'); ?></a></p>



    </div>



    <?php
    }

}

add_action('admin_notices', 'my_error_notice');

/*
 * New columns
*/
add_filter('manage_product_posts_columns', 'layup_disable_column');
// the above hook will add columns only for default 'post' post type, for CPT:
// manage_{POST TYPE NAME}_posts_columns
function layup_disable_column($column_array)
{

    $column_array['layup_disable'] = 'Layup Disabled';
    // the above code will add columns at the end of the array
    // if you want columns to be added in another place, use array_slice()
    return $column_array;
}

/*
 * Populate our new columns with data
*/
add_action('manage_posts_custom_column', 'layup_populate_column', 10, 2);
function layup_populate_column($column_name, $id)
{

    // if you have to populate more that one columns, use switch()
    switch ($column_name):
    case 'layup_disable':
        {
                if (get_post_meta($id, 'layup_disable', true) == 'yes') echo 'Yes';
                break;
            }
        endswitch;

    }

    /*
     * quick_edit_custom_box allows to add HTML in Quick Edit
     * Please note: it files for EACH column, so it is similar to manage_posts_custom_column
    */

    add_action('quick_edit_custom_box', 'layup_quick_edit_fields', 10, 2);

    function layup_quick_edit_fields($column_name, $post_type)
    {

        // you can check post type as well but is seems not required because your columns are added for specific CPT anyway
        switch ($column_name):
        case 'featured':
            {
                    echo '<fieldset class="inline-edit-col-right">
				<div class="inline-edit-col">
					<div class="inline-edit-group wp-clearfix">';
                    echo '<label class="alignleft">
					<input type="checkbox" name="layup_disable" value="yes">
					<span class="checkbox-title">Disable LayUp checkout</span>
				</label>';

                    // for the LAST column only - closing the fieldset element
                    echo '</div></div></fieldset>';

                break;

            }

        endswitch;

    }

    /*
     * Quick Edit Save
    */
    add_action('save_post', 'layup_quick_edit_save');

    function layup_quick_edit_save($post_id)
    {
        if (!$_POST)
        {
            return;
        }

        // check user capabilities
        if (!current_user_can('edit_post', $post_id))
        {
            return;
        }

        // update checkbox
        if (isset($_POST['layup_disable']))
        {
            update_post_meta($post_id, 'layup_disable', 'yes');
        }
        else
        {
            update_post_meta($post_id, 'layup_disable', '');
        }

    }

    if (!function_exists('layup_quick_edit_js'))
    {
        function layup_quick_edit_js()
        {
            // # check the current screen
            // https://developer.wordpress.org/reference/functions/get_current_screen/
            $current_screen = get_current_screen();

            /*
            * ****************************************
            * # List of default screen ID in WordPress
            * ****************************************
            PAGE               $SCREEN_ID           FILE
            -----------------  -------------------  -----------------------
            Media Library      upload               upload.php
            Comments           edit-comments        edit-comments.php
            Tags               edit-post_tag        edit-tags.php
            Plugins            plugins              plugins.php
            Links              link-manager         link-manager.php
            Users              users                users.php
            Posts              edit-post            edit.php
            Pages              edit-page            edit.php
            Edit Site: Themes  site-themes-network  network/site-themes.php
            Themes             themes-network       network/themes
            Users              users-network        network/users
            Edit Site: Users   site-users-network   network/site-users
            Sites              sites-network        network/sites
            
            If you use the custom post type just like me, you can print out the get_current_screen() object
            for checking what screen ID do you need for the next checking.
            My current screen ID of project post type is "edit-mms_project_cpt".
            
            var_dump($current_screen);
            exit;
            */

            if ($current_screen->id != 'edit-product' || $current_screen->post_type !== 'product') return;

            // # Make sure jQuery library is loaded because we will use jQuery for populate our custom field value.
            wp_enqueue_script('jquery');
?>


        <!-- add JS script -->
        <script type="text/javascript">
            jQuery(function($){

// it is a copy of the inline edit function
var wp_inline_edit_function = inlineEditPost.edit;

// we overwrite the it with our own
inlineEditPost.edit = function( post_id ) {

	// let's merge arguments of the original function
	wp_inline_edit_function.apply( this, arguments );

	// get the post ID from the argument
	var id = 0;
	if ( typeof( post_id ) == 'object' ) { // if it is object, get the ID number
		id = parseInt( this.getId( post_id ) );
	}

	//if post id exists
	if ( id > 0 ) {

		// add rows to variables
		var specific_post_edit_row = $( '#edit-' + id ),
			specific_post_row = $( '#post-' + id ),
			layup_disabled = false; // let's say by default checkbox is unchecked

		// check if the Featured Product column says Yes
		if( $( '.column-layup_disable', specific_post_row ).text() == 'Yes' ) layup_disabled = true;

		// populate the inputs with column data
		$( ':input[name="layup_disabled"]', specific_post_edit_row ).prop('checked', layup_disabled );
	}
}
});

        </script>
<?php
        }
    }

    // https://developer.wordpress.org/reference/hooks/admin_print_footer_scripts-hook_suffix/
    add_action('admin_print_footer_scripts-edit.php', 'layup_quick_edit_js');

    add_action('woocommerce_product_bulk_edit_start', 'layup_custom_field_bulk_edit_input');

    function layup_custom_field_bulk_edit_input()
    {
?>
    <div class="inline-edit-group">
      <label class="alignleft">
         <span class="title"><?php _e('Disable LayUp checkout', 'woocommerce'); ?></span>
         <span class="input-text-wrap">
		 <input type="checkbox" name="layup_disable" value="yes">
         </span>
        </label>
    </div>
    <?php
    }

    add_action('woocommerce_product_bulk_edit_save', 'layup_custom_field_bulk_edit_save');

    function layup_custom_field_bulk_edit_save($product)
    {
        $post_id = $product->get_id();
        if (isset($_REQUEST['layup_disable']))
        {
            $custom_field = $_REQUEST['layup_disable'];
            update_post_meta($post_id, 'layup_disable', wc_clean($custom_field));
        }
    }

    function layup_display_cart()
    {

        global $post;

        global $woocommerce;

        $gateway_id = 'layup';

        $gateways = WC_Payment_Gateways::instance();

        $gateway = $gateways->payment_gateways() [$gateway_id];

        // Check for the Disable LayUp field value
        

        $cart_products = $woocommerce
            ->cart->cart_contents;

        if ($gateway->payplan_disp_cart == 'yes')
        {
            $cart_inarray = false;
            $product_names = '';

            foreach ($cart_products as $cart_product)
            { //enumerate over all cart contents
                $layup_disable_meta = get_post_meta($cart_product['data']->get_id() , 'layup_disable', true);

                if (!empty($layup_disable_meta))
                {

                    $cart_inarray = true; //set inarray to true
                    $product_names .= $cart_product['data']->get_title() . ', ';

                }

            }

            if ($cart_inarray)
            { //product is in the cart
                echo '<div style="display:inline-block;font-family:Arial, Helvetica, sans-serif ;margin-top: 15px;margin-bottom: 15px;" class="btn-est-layup">
		<p style="color:red">You currently have the following items in your cart that do not allow you to use LayUp as a payment method: ' . $product_names . 'please remove them if you wish to use the LayUp payment method.</p>
		</div>';

                return;
            }

            // Build product array
            $custom_dep_inarray = false;

            $check_dep_type = [];
            $check_dep_amount = [];
            $check_dep_months_min = [];
            $check_dep_months_max = [];

            foreach ($cart_products as $cart_item_key => $cart_item)
            {

                $cart_product = $cart_item['data'];

                if ($cart_product->is_type('variation'))
                {
                    $cart_product = wc_get_product($cart_product->get_parent_id());

                }

                $layup_custom_deposit = get_post_meta($cart_product->get_id() , 'layup_custom_deposit', true);
                $layup_custom_deposit_type = get_post_meta($cart_product->get_id() , 'layup_custom_deposit_type', true);
                $layup_custom_deposit_amount = get_post_meta($cart_product->get_id() , 'layup_custom_deposit_amount', true);
                $layup_custom_months = get_post_meta($cart_product->get_id() , 'layup_custom_months', true);
                $layup_custom_months_min = get_post_meta($cart_product->get_id() , 'layup_custom_months_min', true);
                $layup_custom_months_max = get_post_meta($cart_product->get_id() , 'layup_custom_months_max', true);

                if ($layup_custom_deposit == "yes")
                {
                    array_push($check_dep_type, $layup_custom_deposit_type);
                    array_push($check_dep_amount, $layup_custom_deposit_amount);
                }
                else
                {
                    array_push($check_dep_type, $gateway->layup_dep_type);
                    array_push($check_dep_amount, $gateway->layup_dep);
                }
                
                if ($layup_custom_months == "yes")
                {
                    array_push($check_dep_months_min, $layup_custom_months_min);
                    array_push($check_dep_months_max, $layup_custom_months_max);
                }
                else
                {
                    array_push($check_dep_months_min, $gateway->lu_min_end_date);
                    array_push($check_dep_months_max, $gateway->lu_max_end_date);
                }

            }

            if ($gateway->dynamic_deposit == "yes")
            {
                if (count(array_flip($check_dep_type)) > 1 || count(array_flip($check_dep_amount)) > 1) {
                    $combine_amount = [];
                    foreach($cart_products as $combine_cart_item_id => $combine_cart_item) {
                        $combine_cart_product = $combine_cart_item['data'];
                        $combine_product_price = $combine_cart_product->get_price();
                        if ($combine_cart_product->is_type('variation')) {
                            $combine_cart_product = wc_get_product($combine_cart_product->get_parent_id());
                        }
                        
                        $combine_product_id = $combine_cart_product->get_id();
                        
                        $layup_custom_deposit_combine = get_post_meta($combine_product_id , 'layup_custom_deposit', true);
                        $layup_custom_deposit_type_combine = get_post_meta($combine_product_id , 'layup_custom_deposit_type', true);
                        $layup_custom_deposit_amount_combine = get_post_meta($combine_product_id, 'layup_custom_deposit_amount', true);
                        $layup_custom_months_max_combine = get_post_meta($combine_product_id , 'layup_custom_months_max', true);
                        if ($layup_custom_deposit_combine == "yes")
                        {
                            if ($layup_custom_deposit_type_combine == "FLAT") {
                                array_push($combine_amount, $layup_custom_deposit_amount_combine);
                            } elseif ($layup_custom_deposit_type_combine == "PERCENTAGE") {
                                $perc_flat_amount = $layup_custom_deposit_amount_combine/100 * $combine_product_price;
                                array_push($combine_amount, $perc_flat_amount);
                            } elseif ($layup_custom_deposit_type_combine == "INSTALMENT") {
                                $instal_flat_amount = $combine_product_price / ($layup_custom_months_max_combine + 1);
                                array_push($combine_amount, $instal_flat_amount);
                            }
                        } else {
                            if ($gateway->layup_dep_type == "FLAT") {
                                array_push($combine_amount, $gateway->layup_dep);
                            } elseif ($gateway->layup_dep_type == "PERCENTAGE") {
                                $perc_flat_amount = $gateway->layup_dep/100 * $combine_product_price;
                                array_push($combine_amount, $perc_flat_amount);
                            } elseif ($gateway->layup_dep_type == "INSTALMENT") {
                                $instal_flat_amount = $combine_product_price / ($gateway->lu_max_end_date + 1);
                                array_push($combine_amount, $instal_flat_amount);
                            }
                        }
                    }
                    $check_dep_amount = array(array_sum($combine_amount));
                    $check_dep_type = array("FLAT");
                }
        
                if (count(array_flip($check_dep_months_min)) > 1 || count(array_flip($check_dep_months_max)) > 1) {
                    $check_dep_months_min = array(max($check_dep_months_min));
                    $check_dep_months_max = array(min($check_dep_months_max));
                }
            }

            if (count(array_unique($check_dep_type)) <= 1 && count(array_unique($check_dep_amount)) <= 1 && count(array_unique($check_dep_months_min)) <= 1 && count(array_unique($check_dep_months_max)) <= 1)
            {

                // Only display LayUp icon if Disable LayUp field is not checked
                

                if (!empty($check_dep_amount[0]))
                {
                    $gateway->layup_dep = $check_dep_amount[0];
                    settype($gateway->layup_dep, 'float');
                }

                if (!empty($check_dep_type[0]))
                {
                    $gateway->layup_dep_type = $check_dep_type[0];
                }

                if (!empty($check_dep_months_min[0]))
                {
                    $gateway->lu_min_end_date = $check_dep_months_min[0];
                }

                if (!empty($check_dep_months_max[0]))
                {
                    $gateway->lu_max_end_date = $check_dep_months_max[0];
                }

                if ($gateway->layup_dep_type == 'PERCENTAGE')
                {
                    $layup_preview_deposit = 'Deposit: ' . $gateway->layup_dep . '%';
                }
                elseif ($gateway->layup_dep_type == 'FLAT')
                {
                    $layup_preview_deposit = 'Deposit: R' . $gateway->layup_dep;
                }
                elseif ($gateway->layup_dep_type == 'INSTALMENT')
                {
                    $layup_preview_deposit = '';
                }

                if ($gateway->lu_max_end_date != 0)
                {

                    $price = $woocommerce
                        ->cart->total;
                    $priceNoDep = 0;
                    $newInstalment = 0;
                    $months = $gateway->lu_max_end_date;
                    if ($gateway->layup_dep_type == 'FLAT')
                    {
                        $deposit = $gateway->layup_dep;
                        $priceNoDep = $price - $gateway->layup_dep;
                        $newInstalment = $priceNoDep / $months;
                    }
                    else if ($gateway->layup_dep_type == 'PERCENTAGE')
                    {
                        $deposit = $gateway->layup_dep / 100 * $price;
                        $priceNoDep = $price - $deposit;
                        $newInstalment = $priceNoDep / $months;
                    }
                    else if ($gateway->layup_dep_type == 'INSTALMENT')
                    {
                        $deposit = $gateway->layup_dep;
                        $newInstalment = $price / $months;
                        $months = $months + 1;
                    }
                    $formatInstalment = number_format($newInstalment, 2);

                    if ($gateway->learn_more_style == 'subscription')
                {
                    $learn_more_url = "https://layup.co.za/learn-more-popup-subscription/";
                } else {
                    $learn_more_url = "https://layup.co.za/learn-more-popup-layby/";
                }
                    
                    echo '<div style="font-family:Arial, Helvetica, sans-serif ;margin-top: 15px;margin-bottom: 15px;" class="btn-est-layup">
				<p style="margin-top: 0px; "><span class="btn-layup-text"><strong>PAY IT OFF</strong> with <em style="color:#1295a5;">LayUp</em></span> From R<span class="layup-installment-amount">' . esc_attr($formatInstalment) . '</span>/month for <span class="layup-months-amount">' . esc_attr($months) . '</span> Months. Interest-free. <span class="layup-deposit-amount">' . esc_attr($layup_preview_deposit) . ' </span><span id="lumodallink" style="color:#1295a5;">Learn More</span></p>
				<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand">
				<style>
					/* The Modal (background) */
		
					.btn-layup {
						float: left;
						max-width: 30%;
						margin-right:0px;
					}
		
					.btn-est-layup {
						max-width: 100%;
						padding: 10px 10px 10px 30px;
						margin-left:0px;
						float: left;
						font-size: 12px;
					}
		
					@media screen and (max-width: 1040px) {
						.btn-layup {
							float: none;
							max-width: 40%;
						}
		
						.btn-est-layup {
							margin-left: 0px;
						}
					}
		
					@media screen and (max-width: 600px) {
						.btn-layup-text {
							font-size: 75%;
						}
						.btn-layup {
							max-width: 50%;
						}
						.btn-est-layup {
						font-size: 10px;
					}
					}
		
					.lumodal {
						font-family: "Quicksand", serif !important;
						display: none;
						/* Hidden by default */
						position: fixed;
						/* Stay in place */
						z-index: 99999;
						/* Sit on top */
						padding-top: 100px;
						/* Location of the box */
						left: 0;
						top: 0;
						width: 100%;
						/* Full width */
						height: 100%;
						/* Full height */
						overflow: auto;
						/* Enable scroll if needed */
						background-color: rgb(0, 0, 0);
						/* Fallback color */
						background-color: rgba(0, 0, 0, 0.4);
						/* Black w/ opacity */
					}
		
					/* Modal Content */
					.lumodal-content {
						background-color: #f7f9fc;
						margin: auto;
						padding: 20px;
						border: 1px solid #888;
						width: 60%;
						text-align: center;
						overflow: auto;
					}
		
					.lumodal-content .center {
						display: block;
						margin-left: auto;
						margin-right: auto;
		
					}
		
					.lumodal-content .lu-modal-col {
						float: left;
						width: 31.33%;
						margin: 1%;
						margin-bottom: 1em;
						padding: 2%;
		
					}
		
					@media (max-width: 600px) {
		
						/* CSS that should be displayed if width is equal to or less than 600px goes here */
						.lumodal-content {
							width: 80%;
						}
		
						.lumodal-content .lu-modal-col {
							width: 80%;
							margin: 0 auto;
							display: table;
							float: none;
						}
		
						.lumodal {
							padding-top: 0px;
						}
					}
		
					.lumodal-content .lu-modal-col:nth-of-type(3n+4) {
						clear: left;
					}
		
					/* The Close Button */
					.luclose {
						color: #aaaaaa;
						float: right;
						font-size: 28px;
						font-weight: bold;
						border-radius: 5px;
						border: #808080 solid 1px;
						line-height: 0;
						padding: 10px 10px 14px 10px;
					}
		
					.luclose:hover,
					.luclose:focus {
						color: #000;
						text-decoration: none;
						cursor: pointer;
					}
		
					#lumodallink:hover {
						text-decoration: underline;
						cursor: pointer;
					}
				</style>
		
				<script>

                jQuery(document).ready(function(){

                    jQuery( "body" ).prepend( `<div id="lumyModal" class="lumodal"><div class="lumodal-content"><span class="luclose">×</span><br><br></div></div>` );

                    // Get the modal
					var modal = document.getElementById("lumyModal");
		
					// Get the button that opens the modal
					var btn = document.getElementById("lumodallink");
		
					// Get the <span> element that closes the modal
					var span = document.getElementsByClassName("luclose")[0];
		
					// When the user clicks the button, open the modal 
					btn.onclick = function () {
                        jQuery( "#lumyModal .lumodal-content" ).append( `<iframe id="layup-iframe" title="LayUp - How it works" src="'.$learn_more_url.'" width="100%" scrolling="no"></iframe>` );
                        let iframe=document.querySelector("#layup-iframe");window.addEventListener("message",function(e){let t=e.data;iframe.style.height=t.height+"px"; e.data;iframe.style.border="none";},false);
                        modal.style.display = "block";
					}
		
					// When the user clicks on <span> (x), close the modal
					span.onclick = function () {
						modal.style.display = "none";
                        jQuery("#layup-iframe").remove();
					}
		
					// When the user clicks anywhere outside of the modal, close it
					window.onclick = function (event) {
						if (event.target == modal) {
							modal.style.display = "none";
                            jQuery("#layup-iframe").remove();
						}
					}

                });
				</script>
		
		
			</div>';
                }

            }
            else
            {

                echo '<div style="display:inline-block;font-family:Arial, Helvetica, sans-serif ;margin-top: 15px;margin-bottom: 15px;" class="btn-est-layup">
		<p style="color:red">Some products are using a custom deposit for LayUp checkout. Please make sure that all products in your cart have the same deposit type and months before checking out with LayUp.</p>
		</div>';

            }
        }

    }

    add_action('woocommerce_after_cart_totals', 'layup_display_cart', 10);
    
    function general_admin_notice(){

        global $woocommerce;
        $gateway_id = 'layup';
        $gateways = WC_Payment_Gateways::instance();
        $gateway = $gateways->payment_gateways() [$gateway_id];

       if ( $gateway->api_key_error == "1" ) {
             echo '<div class="notice notice-error">
                 <p>Your LayUp API key is incorrect, this will cause errors when customers try to checkout using the LayUp Payment Method.</p>
                 <p><stong>Please check that you have entered the correct API key.</stong></p>
             </div>';
        }
    }
    add_action('admin_notices', 'general_admin_notice');