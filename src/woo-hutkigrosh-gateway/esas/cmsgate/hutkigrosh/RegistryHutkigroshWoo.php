<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\lang\LocaleLoaderWoo;
use esas\cmsgate\hutkigrosh\lang\TranslatorHutkigrosh;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormWoo;
use esas\cmsgate\hutkigrosh\view\admin\ManagedFieldsHutkigrosh;
use esas\cmsgate\wrappers\OrderWrapper;
use esas\cmsgate\wrappers\OrderWrapperWoo;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelWoo;
use esas\cmsgate\hutkigrosh\wrappers\ConfigWrapperHutkigroshWoo;
use WP_Post;

class RegistryHutkigroshWoo extends RegistryHutkigrosh
{
    /**
     * Переопделение для упрощения типизации
     * @return RegistryHutkigroshWoo
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @return TranslatorHutkigrosh
     */
    public function createTranslator()
    {
        $localeLoader = new LocaleLoaderWoo();
        return new TranslatorHutkigrosh($localeLoader);
    }

    public function getOrderWrapper($orderId)
    {
        return new OrderWrapperWoo($orderId);
    }

    /**
     * По локальному номеру заказа (может отличаться от id) возвращает wrapper
     * @param $orderNumber
     * @return OrderWrapper
     */
    public function getOrderWrapperByOrderNumber($orderNumber)
    {
        return $this->getOrderWrapper($orderNumber);
    }

    /**
     * По номеру транзакции внешней система возвращает wrapper
     * @param $extId
     * @return OrderWrapper
     */
    public function getOrderWrapperByExtId($extId)
    {
        /** @var WP_Post[] $posts */
        $posts = get_posts( array(
            'meta_key'    => OrderWrapperWoo::EXTID_METADATA_KEY,
            'meta_value'  => $extId,
            'post_type'   => 'shop_order',
            'post_status' => 'any'
        ));
        $post = $posts[0];
        return $this->getOrderWrapper($post->ID);
    }

    public function createConfigForm()
    {
        $managedFields = new ManagedFieldsHutkigrosh();
        $managedFields->addAllExcept([ConfigFieldsHutkigrosh::shopName()]);
        $configForm = new ConfigFormWoo(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields
        );
        $configForm->addCmsManagedFields();
        return $configForm;
    }

    public function getCompletionPanel($orderWrapper)
    {
        $completionPanel = new CompletionPanelWoo($orderWrapper);
        return $completionPanel;
    }

    public function createConfigWrapper()
    {
        return new ConfigWrapperHutkigroshWoo();
    }


}