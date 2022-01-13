<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\CmsConnectorWoo;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormWoo;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelHutkigroshWoo;
use esas\cmsgate\hutkigrosh\wrappers\ConfigWrapperHutkigroshWoo;

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

    public function getCompletionPanel($orderWrapper)
    {
        $completionPanel = new CompletionPanelHutkigroshWoo($orderWrapper);
        return $completionPanel;
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
            new VersionDescriptor("3.11.5", "2022-01-13"),
            "Прием платежей через ЕРИП (сервис ХуткiГрош)",
            "https://bitbucket.org/esasby/cmsgate-woocommerce-hutkigrosh/src/master/",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }
}