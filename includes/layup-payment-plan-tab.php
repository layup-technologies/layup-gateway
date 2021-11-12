<?php
/**
 *  Add payment plan Tab @ My Account
 */
// Register new endpoint to use for My Account page


function layup_add_payment_plans_endpoint()
{

    add_rewrite_endpoint('payment-plans', EP_ROOT | EP_PAGES);

}

add_action('init', 'layup_add_payment_plans_endpoint');

// Add new query var
function layup_payment_plans_query_vars($vars)
{

    $vars[] = 'payment-plans';

    return $vars;

}

add_filter('query_vars', 'layup_payment_plans_query_vars', 0);

// Insert the new endpoint into the My Account menu
function layup_add_payment_plans_link_my_account($items)
{

    $items['payment-plans'] = 'Payment Plans';

    return $items;

}

add_filter('woocommerce_account_menu_items', 'layup_add_payment_plans_link_my_account');

// Add content to the new endpoint
function layup_payment_plans_content()
{

    echo do_shortcode("[layup]");

}

add_action('woocommerce_account_payment-plans_endpoint', 'layup_payment_plans_content');

