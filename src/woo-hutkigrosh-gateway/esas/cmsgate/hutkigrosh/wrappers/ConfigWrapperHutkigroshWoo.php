<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 12.03.2020
 * Time: 10:52
 */

namespace esas\cmsgate\hutkigrosh\wrappers;


use esas\cmsgate\ConfigStorageWoo;

class ConfigWrapperHutkigroshWoo extends ConfigWrapperHutkigrosh
{
    /**
     * ConfigWrapperBGPBWoo constructor.
     */
    public function __construct()
    {
        parent::__construct(new ConfigStorageWoo());
    }

    function getUrlAlfaclick($orderId)
    {
        return admin_url('admin-ajax.php') . "?action=alfaclick";
    }

    function getUrlWebpay($orderId)
    {
        $order = wc_get_order($orderId);
        return $order->get_checkout_order_received_url();
    }
}