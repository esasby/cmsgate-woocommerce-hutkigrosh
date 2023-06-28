<?php

use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPanel;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshWebpayForm;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\woocommerce\WcCmsgate;
use esas\cmsgate\wrappers\OrderWrapper;

require_once('init.php');


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WcCmsgateHutkigrosh extends WcCmsgate
{
    function __construct()
    {
        parent::__construct();
        add_action('woocommerce_api_gateway_hutkigrosh', array($this, 'hutkigrosh_callback'));
        add_filter('woocommerce_thankyou_' . $this->id, array($this, 'hutkigrosh_thankyou_text'));
    }

    // Submit payment and handle response

    /**
     * @param OrderWrapper $orderWrapper
     * @throws Throwable
     */
    protected function process_payment_safe($orderWrapper)
    {
        if (empty($orderWrapper->getExtId())) {
            $controller = new ControllerHutkigroshAddBill();
            $controller->process($orderWrapper);
        }
    }

    function hutkigrosh_thankyou_text($order_id)
    {
        try {
            if (!ControllerHutkigroshWebpayForm::isSuccessReturnUrl()) {
                $controller = new ControllerHutkigroshCompletionPanel();
                $completionPanel = $controller->process($order_id);
                echo $completionPanel->build();
            }
        } catch (Throwable $e) {
            Logger::getLogger("payment")->error("Exception:", $e);
        }
    }

    public static function alfaclick_callback()
    {
        try {
            $controller = new ControllerHutkigroshAlfaclick();
            $controller->process($_POST[RequestParamsHutkigrosh::BILL_ID], $_POST[RequestParamsHutkigrosh::PHONE]);
        } catch (Throwable $e) {
            Logger::getLogger("alfaclick")->error("Exception: ", $e);
        }
        wp_die();
    }

    public function hutkigrosh_callback()
    {
        try {
            $billId = $_GET[RequestParamsHutkigrosh::PURCHASE_ID];
            $controller = new ControllerHutkigroshNotify();
            $controller->process($billId);
        } catch (Throwable $e) {
            Logger::getLogger("callback")->error("Exception:", $e);
        }
    }
}