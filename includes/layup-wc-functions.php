<?php
add_action('wp_enqueue_scripts', 'register_layup_style');

function register_layup_style()
{

	wp_register_style("layup_css", plugins_url('../css/payment-plans.css', __FILE__) , array() , '1.0.0', 'all');

}

/**
 * Create layUp Merchant Settings in Woocommerce
 */

if (!class_exists('WC_Settings_LayUp'))
{

	function layup_add_settings()
	{

		/**
		 * Settings class
		 */
		class WC_Settings_LayUp extends WC_Settings_Page
		{

			/**
			 * Setup settings class
			 */
			public function __construct()
			{

				$this->id = 'layup-merchant';

				$this->label = __('LayUp Merchant Settings', 'woocommerce-settings-tab-layup');

				add_filter('woocommerce_settings_tabs_array', array(
					$this,
					'add_settings_page'
				) , 20);

				add_action('woocommerce_settings_' . $this->id, array(
					$this,
					'layup_output'
				));

				add_action('woocommerce_settings_save_' . $this->id, array(
					$this,
					'layup_save'
				));

				add_action('admin_notices', array(
					$this,
					'layup_admin_notices'
				));

			}

			/**
			 * Get settings array
			 */
			public function layup_get_settings()
			{

				$merchant_id = get_option('layup_merchant_id');

				if ($merchant_id == '')
				{

					$settings = apply_filters('layup_settings', array(

						array(

							'name' => __('Merchant Details', 'woocommerce-settings-tab-layup') ,

							'type' => 'title',

							'desc' => '',

							'id' => 'layup_merchant_settings_title'

						) ,

						array(

							'name' => __('Merchant ID', 'woocommerce-settings-tab-layup') ,

							'type' => 'text',

							'desc' => __('Your Merchant ID provided by LayUp, Please Enter it here and click save so we can fetch your merchant details', 'woocommerce-settings-tab-layup') ,

							'id' => 'layup_merchant_id'

						) ,

						array(

							'type' => 'sectionend',

							'id' => 'layup_merchant_section_end'

						)

					));

				}
				else
				{

					$settings = apply_filters('layup_settings', array(

						array(

							'name' => __('Merchant Details', 'woocommerce-settings-tab-layup') ,

							'type' => 'title',

							'desc' => '',

							'id' => 'layup_merchant_settings_title'

						) ,

						array(

							'name' => __('Merchant ID', 'woocommerce-settings-tab-layup') ,

							'type' => 'password',

							'desc' => __('Your Merchant ID provided by LayUp, Please Enter it here and click save so we can fetch your merchant details', 'woocommerce-settings-tab-layup') ,

							'id' => 'layup_merchant_id',

						) ,

						array(

							'name' => __('Merchant Name', 'woocommerce-settings-tab-layup') ,

							'type' => 'text',

							'desc' => __('The name of your merchant account, usually your company Name', 'woocommerce-settings-tab-layup') ,

							'id' => 'layup_merchant_name',

						) ,

						array(

							'name' => __('Merchant domain', 'woocommerce-settings-tab-layup') ,

							'type' => 'text',

							'desc' => __('The Domain of your website, e.g. yourdomain.co.za', 'woocommerce-settings-tab-layup') ,

							'id' => 'layup_merchant_domain',

						) ,

						array(

							'name' => __('Merchant notify URL', 'woocommerce-settings-tab-layup') ,

							'type' => 'text',

							'desc' => __('The notify URL of your website, usually the website`s payment thank you page', 'woocommerce-settings-tab-layup') ,

							'id' => 'layup_merchant_notifyurl',

						) ,

						array(

							'type' => 'sectionend',

							'id' => 'layup_merchant_section_end'

						)

					));

				}

				return apply_filters('woocommerce_get_settings_' . $this->id, $settings);

			}

			/**
			 * Output the settings
			 */

			public function layup_output()
			{

				global $woocommerce;

				$gateway_id = 'layup';

				$gateways = WC_Payment_Gateways::instance();

				$gateway = $gateways->payment_gateways() [$gateway_id];

				$merchant_id = get_option('layup_merchant_id');

				$api_key_check = $gateway->api_key;

				if ($merchant_id !== '' || $api_key_check !== '')
				{

					$api_key = $gateway->api_key;
					
				if ($gateway->testmode == 'yes')
						{

							$api_url = "https://sandbox-api.layup.co.za/";

						}
						else
						{

							$api_url = "https://api.layup.co.za/";

						}
					

					$headers = array(

						'accept' => 'application/json',

						'apikey' => $api_key,

					);

					$merchant_args = array(

						'headers' => $headers,

					);

					$merch_response = wp_remote_get($api_url . 'v1/merchants/' . $merchant_id, $merchant_args);

					if (!is_wp_error($merch_response))
					{

						if ($merch_response['body'] == 'Forbidden')
						{

							echo '<div class="error"><p>'
 . __('The Merchant ID was invalid, please try again', 'layup-gateway')
 . '</p></div>';

						}
						else
						{

							$body = json_decode($merch_response['body'], true);

							$name = $body['name'];

							$domain = $body['domain'];

							$notifyUrl = $body['notifyUrl'];

							update_option('layup_merchant_name', $name);

							update_option('layup_merchant_domain', esc_url_raw($domain));

							update_option('layup_merchant_notifyurl', esc_url_raw($notifyUrl));
				
						}

					}
					else
					{

						echo '<div class="error"><p>'
 . __('There was an error, please try again', 'layup-gateway')
 . '</p></div>';

					}
				
				}
				else
				{

					echo '<div class="error"><p>'
 . __('Please make sure you have entered your API key in the payment settings before you enter your Merchant ID', 'layup-gateway')
 . '</p></div>';

				}

				$settings = $this->layup_get_settings();

				WC_Admin_Settings::output_fields($settings);

			}

			/**
			 * Save settings
			 */

			public function layup_save()
			{

				if (array_key_exists('layup_merchant_name', $_POST))
				{

					if (esc_url_raw($_POST['layup_merchant_domain']) === $_POST['layup_merchant_domain'] && esc_url_raw($_POST['layup_merchant_notifyurl']) === $_POST['layup_merchant_notifyurl'])
					{

						global $woocommerce;
						echo 'layup';
						$gateway_id = 'layup';

						$gateways = WC_Payment_Gateways::instance();

						$gateway = $gateways->payment_gateways() [$gateway_id];

						$save_api_key_check = $gateway->api_key;
						
						$save_api_key = $gateway->api_key;

						$save_merchant_id = $_POST['layup_merchant_id'];

						if ($save_merchant_id !== '')
						{

							if ($gateway->testmode == 'yes')
							{

								$save_api_url = "https://sandbox-api.layup.co.za/";

							}
							else
							{

								$save_api_url = "https://api.layup.co.za/";

							}

							$save_merchant_details = array(

								'name' => sanitize_text_field($_POST['layup_merchant_name']) ,

								'domain' => sanitize_text_field($_POST['layup_merchant_domain']) ,

								'notifyUrl' => sanitize_text_field($_POST['layup_merchant_notifyurl'])

							);

							$save_merchant_details_json = json_encode($save_merchant_details, JSON_UNESCAPED_SLASHES);

							$save_headers = array(

								'accept' => 'application/json',

								'Content-Type' => 'application/json',

								'apikey' => $save_api_key,

							);

							$save_merchant_args = array(

								'method' => 'PUT',

								'headers' => $save_headers,

								'body' => $save_merchant_details_json

							);

							$save_merch_response = wp_remote_request($save_api_url . 'v1/merchants/' . $save_merchant_id, $save_merchant_args);
							
							

							if (!is_wp_error($save_merch_response))
							{

								if ($save_merch_response['body'] == 'Forbidden')
								{

									echo '<div class="error"><p>'
 . __('The Merchant ID was invalid, please try again', 'layup-gateway')
 . '</p></div>';

								}

							}
							else
							{

								echo '<div class="error"><p>'
 . __('There was an error, please try again', 'layup-gateway')
 . '</p></div>';
								

							}

						}

					}
					else
					{

						echo '<div class="error"><p>'
 . __('There was an error, please try again', 'layup-gateway')
 . '</p></div>';
						

					}

				}

				$settings = $this->layup_get_settings();

				WC_Admin_Settings::save_fields($settings);

			}

			/**
			 *  Show possible admin notices
			 */

			public function layup_admin_notices($merch_response)
			{

				if ($merch_response)
				{

					if ($merch_response['body'] == 'Forbidden')
					{

						echo '<div class="error"><p>'
 . __('The Merchant ID was invalid, please try again', 'layup-gateway')
 . '</p></div>';

					}
					else
					{

						return;

					}

				}

			}

		}

		return new WC_Settings_LayUp();

	}

	add_filter('woocommerce_get_settings_pages', 'layup_add_settings', 15);

}

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

<div class="input_fields_wrap">

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

</div>

    <?php
}

add_action('woocommerce_product_options_general_product_data', 'woo_add_layup_date_fields');

add_action('admin_footer', 'layup_admin_footer_script');

function layup_admin_footer_script()
{

	global $post;

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

add_action('woocommerce_product_options_general_product_data', 'create_layup_disable_field');

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

	$product->update_meta_data('layup_disable', $layup_disable);

	$price = $product->get_price() * 100;

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

	$dates = isset($_POST['layup_date']) ? preg_replace("([^0-9-])", "", $_POST['layup_date']) : '';

	$product->update_meta_data('layup_date', $dates);

	$lu_min_date = date('Y-m-d', strtotime("+" . $gateway->lu_min_end_date . " months", strtotime($lu_curr_date)));
	
	$api_key = $gateway->api_key;

	if ($gateway->testmode == 'yes')
	{

		$preview_api_url = "https://sandbox-api.layup.co.za/v1/payment-plan/preview";

	}
	else
	{

		$preview_api_url = "https://api.layup.co.za/v1/payment-plan/preview";

	}

	if ($dates == '')
	{

		$lu_curr_date = date('c');

		$lu_max_date = date('Y-m-d', strtotime("+" . $gateway->lu_max_end_date . " months", strtotime($lu_curr_date)));

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

		$preview_details_json = json_encode($preview_details, JSON_UNESCAPED_SLASHES);

		$preview_args = array(

			'headers' => $preview_headers,

			'body' => $preview_details_json

		);

		$preview_response = wp_remote_post($preview_api_url, $preview_args);

		$preview_body = json_decode($preview_response['body'], true);

		$max_payment_months = count($preview_body['paymentPlans']);

		$amount_monthly = $preview_body['paymentPlans'][$max_payment_months - 1]['payments'][1]['amount'];

		$amount_monthly_form = number_format(($amount_monthly / 100) , 2, '.', ' ');

		$product->update_meta_data('layup_preview_months', $max_payment_months);

		$product->update_meta_data('layup_preview_amount', $amount_monthly_form);

	}
	else
	{

		$max_date = max($dates);

		$lu_max_date = date('c', strtotime($max_date));

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

		$preview_details_json = json_encode($preview_details, JSON_UNESCAPED_SLASHES);

		$preview_args = array(

			'headers' => $preview_headers,

			'body' => $preview_details_json

		);

		$preview_response = wp_remote_post($preview_api_url, $preview_args);

		$preview_body = json_decode($preview_response['body'], true);

		$max_payment_months = count($preview_body['paymentPlans']);

		$amount_monthly = $preview_body['paymentPlans'][$max_payment_months - 1]['payments'][1]['amount'];

		$amount_monthly_form = number_format(($amount_monthly / 100) , 2, '.', ',');

		$product->update_meta_data('layup_preview_months', $max_payment_months);

		$product->update_meta_data('layup_preview_amount', $amount_monthly_form);

	}

	$product->save();

}

add_action('woocommerce_process_product_meta', 'save_layup_disable_field');

/**
 * Check products in cart for Disable LayUp field and disable LayUp gateway
 */

function check_layup_disable_field($gateways)
{

	global $woocommerce;

	$inarray = false;

	//wc_clear_notices();
	

	foreach ($woocommerce->cart->cart_contents as $key => $values)
	{ //enumerate over all cart contents
		

		$layup_disable_meta = get_post_meta($values['product_id'], 'layup_disable', true);

		if (!empty($layup_disable_meta))
		{

			$inarray = true; //set inarray to true
			

			break;

		}

	}

	if ($inarray)
	{ //product is in the cart
		

		unset($gateways['layup']);

		if (is_checkout())
		{

			wc_add_notice('You currently have items in your cart that do not allow you to use LayUp as a payment method, please remove them if you wish to use the LayUp payment method.', 'error');

		}

	}

	return $gateways;

}

add_filter('woocommerce_available_payment_gateways', 'check_layup_disable_field', 1);

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

	$layup_disable_meta = $product->get_meta('layup_disable');
	$layup_disable_meta = $product->get_meta('layup_disable');

	if ($gateway->payplan_disp == 'yes')
	{

		if ($layup_disable_meta != 'yes')
		{

			// Only display LayUp icon if Disable LayUp field is not checked
			

			$layup_preview_amount = $product->get_meta('layup_preview_amount');

			$layup_preview_months = $product->get_meta('layup_preview_months');
			
			if (metadata_exists('product', $post->ID, 'layup_preview_months') || $layup_preview_months != 0){

			echo '<div class="clearfix"><div style="font-size: 10px;padding: 10px 20px;margin-top: 15px;margin-right: 15px;margin-bottom: 15px;background-color: ' . esc_attr($gateway->btn_bg_color) . ';color: ' . esc_attr($gateway->btn_text_color) . ';border-radius: 150px;text-align: center;" class="btn-layup">

    PAY IT OFF WITH

    <img style="width: 60px !important; top: 0 !important; vertical-align: middle; border-style: none" src="' . plugin_dir_url(dirname(__FILE__)) . 'img/logo-color.168d4abe.png">

    </div>

    <div style="font-size: 12px;padding: 10px;margin-top: 15px;margin-bottom: 15px;" class="btn-est-layup">

    From R' . esc_attr($layup_preview_amount) . '/month for ' . esc_attr($layup_preview_months) . ' Months. Interest-free. ' . esc_attr($gateway->layup_dep) . '% deposit.<br>
    <span id="lumodallink" style="color:#1295a5;">Learn More</span>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand">
    <style>


/* The Modal (background) */

.btn-layup {
	float:left; 
	max-width: 50%;
}
.btn-est-layup {
	margin-left: 15px;
}

@media screen and (max-width: 1040px) {
    .btn-layup {
        float:none;
		max-width: 80%;
    }
	.btn-est-layup {
	margin-left: 0px;
	}
  }
  
  @media screen and (max-width: 600px) {
  .btn-layup {
		max-width: 100%;
    }
}

.lumodal {
  font-family: "Quicksand", serif !important;
  display: none ; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 99999; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.lumodal-content {
  background-color: #f7f9fc;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 60%;
  text-align:center;
  overflow:auto;
}
.lumodal-content .center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  
}

.lumodal-content .lu-modal-col {
    float:left; width:31.33%; margin:1%; margin-bottom:1em; padding: 2%;
  
}

@media (max-width: 600px) {
  /* CSS that should be displayed if width is equal to or less than 600px goes here */
  .lumodal-content {
  width: 80%;
}
  .lumodal-content .lu-modal-col {
    width:80%;
    margin: 0 auto;
    display: table;
    float:none;
}
.lumodal {
    padding-top: 0px;
}
}

.lumodal-content .lu-modal-col:nth-of-type(3n+4) {clear:left;}

/* The Close Button */
.luclose {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  border-radius: 5px;
  border:#808080 solid 1px;
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

<!-- The Modal -->
<div id="lumyModal" class="lumodal">

  <!-- Modal content -->
  <div class="lumodal-content">
    <span class="luclose">&times;</span>
    <img alt="Layup Logo" class="center" style="width:250px !important;height:auto !important;" src="' . plugin_dir_url(dirname(__FILE__)) . 'img/layup-logo-color.png">
    <p style="color:#0c4152;font-weight: 700;">Simple, Smart, Instalments</p>
    <h2 style="font-family: Quicksand !important; color:#0c4152;font-weight: 700;font-size: 2em;">How it <span style="color:#1295a5;">works?</span></h2>
    <p style="color:#151a30;font-weight: 700;">No credit checks | Interest free payments | No ID required</p>
    <div style="margin: 0 auto;display: table;width: 90%;">
    <div class="lu-modal-col">
        <img alt="activate" style="width:131px !important;height:auto !important;" class="center" src="' . plugin_dir_url(dirname(__FILE__)) . 'img/modal-imageAsset 2.png">
        <h3 style="font-family: Quicksand !important;color:#0c4152;font-weight: 700;">Activate</h3>
        <p style="color:#151a30;font-weight: 500;font-size: 1em;">Select to <strong>pay it off with LayUp,</strong> using your debit/credit card or instant EFT</p>
    </div>
    <div class="lu-modal-col">
        <img alt="activate" style="width:131px !important;height:auto !important;" class="center" src="' . plugin_dir_url(dirname(__FILE__)) . 'img/modal-imageAsset 4.png">
        <h3 style="font-family: Quicksand !important;color:#0c4152;font-weight: 700;">Payment Plan</h3>
        <p style="color:#151a30;font-weight: 500;font-size: 1em;">Pay over time, on your terms, <strong>interest free</strong></p>
    </div>
    <div class="lu-modal-col">
        <img alt="activate" style="width:131px !important;height:auto !important;" class="center" src="' . plugin_dir_url(dirname(__FILE__)) . 'img/modal-imageAsset 3.png">
        <h3 style="font-family: Quicksand !important;color:#0c4152;font-weight: 700;">Complete</h3>
        <p style="color:#151a30;font-weight: 500;font-size: 1em;">Receive the purchase once <strong>paid in full</strong></p>
    </div>
</div>
<hr style="color:#aaaaaa;background-color: #d0d0d0;height: 1px;border: none;">
<p style="color:#151a30;font-weight: 500;font-size: 1em;">To see LayUp complete terms visit:</p>
<p style="color:#151a30;font-weight: 700;font-size: 1em;"><a target="_blank" href="https://layup.co.za/terms-and-conditions/">https://layup.co.za/terms-and-conditions/</a></p>
  </div>

</div>

<script>
// Get the modal
var modal = document.getElementById("lumyModal");

// Get the button that opens the modal
var btn = document.getElementById("lumodallink");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("luclose")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>


    </div></div>';
}

		}

	}

}

add_action('woocommerce_before_add_to_cart_form', 'layup_display_icon', 30);

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
	
	if (is_object($product)) {
		
	$layup_disable_meta = $product->get_meta('layup_disable');

	if ($gateway->payplan_disp == 'yes')
	{

		if ($layup_disable_meta != 'yes')
		{

			// Only display LayUp icon if Disable LayUp field is not checked
			

			$layup_preview_amount = $product->get_meta('layup_preview_amount');

			$layup_preview_months = $product->get_meta('layup_preview_months');

			if (metadata_exists('product', $post->ID, 'layup_preview_months') || $layup_preview_months != 0){

				echo '<div style="font-size: 12px;margin-bottom: 10px;" class="est-layup">
	
		  From R' . esc_attr($layup_preview_amount) . '/month for ' . esc_attr($layup_preview_months) . ' Months
	
		  </div>';
					
				}

		}

	}
}

}

add_action('woocommerce_after_shop_loop_item_title', 'layup_display_estimate', 20);

function my_error_notice()
{

	$merchant_id = get_option('layup_merchant_id');

	if ($merchant_id == '')
	{

?>



    <div class="error notice">



        <p><?php _e('Offering customers the option to pay with LayUp payment plans at checkout. ', 'woocommerce'); ?>



        <a target="_blank" href="https://layup.co.za/contact-us/"><?php _e('Register for a LayUp merchant account ', 'woocommerce'); ?></a><?php _e(' and start offering payment plans today. If you already have one please fill out your merchant details ', 'woocommerce'); ?>



        <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=layup-merchant'); ?>"><?php _e('here.', 'woocommerce'); ?></a></p>



    </div>



    <?php

	}

}

add_action('admin_notices', 'my_error_notice');

