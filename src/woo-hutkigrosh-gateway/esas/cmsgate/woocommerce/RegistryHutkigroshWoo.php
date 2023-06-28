<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\woocommerce;


use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hro\HROManager;
use esas\cmsgate\hutkigrosh\ConfigFieldsHutkigrosh;
use esas\cmsgate\hutkigrosh\hro\client\CompletionPanelHutkigroshHRO;
use esas\cmsgate\hutkigrosh\PaysystemConnectorHutkigrosh;
use esas\cmsgate\hutkigrosh\RegistryHutkigrosh;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\woocommerce\hro\client\CompletionPanelHutkigroshHRO_Woo;
use esas\cmsgate\woocommerce\view\admin\ConfigFormWoo;

class RegistryHutkigroshWoo extends RegistryHutkigrosh
{
    /**
     * RegistryHutkigroshWoo constructor.
     */
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorWoo();
        $this->paysystemConnector = new PaysystemConnectorHutkigrosh();
    }

    public function init() {
        parent::init();
        HROManager::fromRegistry()->addImplementation(CompletionPanelHutkigroshHRO::class, CompletionPanelHutkigroshHRO_Woo::class);
    }

    /**
     * Переопделение для упрощения типизации
     * @return RegistryHutkigroshWoo
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }


    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsExcept(AdminViewFields::CONFIG_FORM_COMMON, [ConfigFieldsHutkigrosh::shopName()]);
        $configForm = new ConfigFormWoo(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields
        );
        $configForm->addCmsManagedFields();
        return $configForm;
    }

    function getUrlAlfaclick($orderWrapper)
    {
        return admin_url('admin-ajax.php') . "?action=alfaclick";
    }

    function getUrlWebpay($orderWrapper)
    {
        $order = wc_get_order($orderWrapper->getOrderId());
        return $order->get_checkout_order_received_url();
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "hutkigrosh",
            new VersionDescriptor("4.0.0", "2023-06-16"),
            "Прием платежей через ЕРИП (сервис ХуткiГрош)",
            "https://github.com/esasby/cmsgate-woocommerce-hutkigrosh",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }
}