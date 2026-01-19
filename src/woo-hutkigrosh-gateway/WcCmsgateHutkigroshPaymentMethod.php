<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use esas\cmsgate\Registry;

require_once('init.php');

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WcCmsgateHutkigroshPaymentMethod extends AbstractPaymentMethodType {
    /**
     * This property is a string used to reference your payment method. It is important to use the same name as in your
     * client-side JavaScript payment method registration.
     *
     * @var string
     */
    protected $name = '';

    function __construct()
    {
        // The global ID for this Payment method
        $this->name = Registry::getRegistry()->getPaySystemName();
    }

    private $gateway;
    /**
     * Initializes the payment method.
     *
     * This function will get called during the server side initialization process and is a good place to put any settings
     * population etc. Basically anything you need to do to initialize your gateway.
     *
     * Note, this will be called on every request so don't put anything expensive here.
     */
    public function initialize() {
        $this->settings = get_option( "woocommerce_{$this->name}_settings", array() );
        $gateways = WC()->payment_gateways->payment_gateways();
        $this->gateway  = $gateways[ $this->name ];
    }

    /**
     * Plugin includes.
     */
    public static function includes() {
        // Make the WcCmsgateEpos class available.
        if ( class_exists( 'WC_Payment_Gateway' ) ) {
            require_once 'WcCmsgateEpos.php';
        }
    }

    /**
     * This should return whether the payment method is active or not.
     *
     * If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        return filter_var( $this->get_setting( 'enabled', true ), FILTER_VALIDATE_BOOLEAN );
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * In this function you should register your payment method scripts (using `wp_register_script`) and then return the
     * script handles you registered with. This will be used to add your payment method as a dependency of the checkout script
     * and thus take sure of loading it correctly.
     *
     * Note that you should still make sure any other asset dependencies your script has are registered properly here, if
     * you're using Webpack to build your assets, you may want to use the WooCommerce Webpack Dependency Extraction Plugin
     * (https://www.npmjs.com/package/@woocommerce/dependency-extraction-webpack-plugin) to make this easier for you.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        wp_register_script(
            'hutkigrosh-payment-method',
            plugin_dir_url( __FILE__ ) . 'esas/cmsgate/woocommerce/hro/client/hutkigrosh-payment-method.js',
            array(
                'wc-blocks-registry',
                'wc-settings',
                'wc-blocks-checkout'
            ),
            wp_rand( 0, 9999 ),
            true
        );
        return [ 'hutkigrosh-payment-method' ];
    }

    /**
     * Returns an array of script handles to be enqueued for the admin.
     *
     * Include this if your payment method has a script you _only_ want to load in the editor context for the checkout block.
     * Include here any script from `get_payment_method_script_handles` that is also needed in the admin.
     */
    public function get_payment_method_script_handles_for_admin() {
        return $this->get_payment_method_script_handles();
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script client side.
     *
     * This data will be available client side via `wc.wcSettings.getSetting`. So for instance if you assigned `stripe` as the
     * value of the `name` property for this class, client side you can access any data via:
     * `wc.wcSettings.getSetting( 'stripe_data' )`. That would return an object matching the shape of the associative array
     * you returned from this function.
     *

    public function get_payment_method_data() {
        return apply_filters(
            'payshop_ifthen_blocks_payment_method_data',
            array(
                'title'         => isset( $this->settings['title'] ) ? $this->settings['title'] : '',
                'description'   => isset( $this->settings['description'] ) ? $this->settings['description'] : '',
                'icon'          => WC_IfthenPay_Webdados()->payshop_icon,
                'only_portugal' => $this->settings['only_portugal'] === 'yes',
                'only_above'    => floatval( $this->settings['only_above'] ) > 0 ? floatval( $this->settings['only_above'] ) : null,
                'only_bellow'   => floatval( $this->settings['only_bellow'] ) > 0 ? floatval( $this->settings['only_bellow'] ) : null,
                // We do not declare subscriptions support on Payshop
                // 'support_woocommerce_subscriptions' => isset( $this->settings['support_woocommerce_subscriptions'] ) && ( 'yes' === $this->settings['support_woocommerce_subscriptions'] ), // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
                // More settings needed?
            )
        );
    }
     *
     * @return array
     */
    public function get_payment_method_data() {
        return array(
            'title'         => $this->get_setting( 'title' ) | $this->gateway->title,
            'description'   => $this->get_setting( 'description' ) | $this->gateway->description
            // We do not declare subscriptions support on Payshop
            // 'support_woocommerce_subscriptions' => isset( $this->settings['support_woocommerce_subscriptions'] ) && ( 'yes' === $this->settings['support_woocommerce_subscriptions'] ), // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            // More settings needed?
        );
    }
}

