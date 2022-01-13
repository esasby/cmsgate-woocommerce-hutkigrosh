<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/*
Plugin Name: WooCommerce Hutkigrosh Gateway
Plugin URI: https://bitbucket.esas.by/projects/CG/repos/cmsgate-woocommerce-hutkigrosh/browse
Description: Модуль для выставления счетов в систему ЕРИП через сервис ХуткiГрош
Version: 3.11.5
Author: ESAS
Author Email: n.mekh@hutkigrosh.by
Text Domain: woocommerce-hutkigrosh-payments
WC requires at least: 3.0.0
WC tested up to: 5.9.0
*/

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'wc_cmsgate_hutkigrosh_init', 0);
function wc_cmsgate_hutkigrosh_init()
{
    // If the parent WC_Payment_Gateway class doesn't exist
    // it means WooCommerce is not installed on the site
    // so do nothing
    if (!class_exists('WC_Payment_Gateway')) return;
    // If we made it this far, then include our Gateway Class
    include_once('WcCmsgateHutkigrosh.php');
    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter('woocommerce_payment_gateways', 'wc_cmsgate_hutkigrosh_add_payment_gateway');

    function wc_cmsgate_hutkigrosh_add_payment_gateway($methods)
    {
        $methods[] = 'WcCmsgateHutkigrosh';
        return $methods;
    }
}

// Add custom action links
require_once dirname(__FILE__) . '/vendor/esas/cmsgate-woocommerce-lib/src/esas/cmsgate/woocommerce/cmsgate-woocommerce-hooks.php';
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cmsgate_settings_link');

/**
 * Custom text on the receipt page.
 */
add_action('wp_ajax_alfaclick', 'alfaclick_callback');
add_action('wp_ajax_nopriv_alfaclick', 'alfaclick_callback');
function alfaclick_callback()
{
    return WcCmsgateHutkigrosh::alfaclick_callback();
}