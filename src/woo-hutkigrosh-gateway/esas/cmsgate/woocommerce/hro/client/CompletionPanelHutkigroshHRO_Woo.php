<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 24.06.2019
 * Time: 14:11
 */

namespace esas\cmsgate\woocommerce\hro\client;

use esas\cmsgate\hro\panels\MessagesPanelHROFactory;
use esas\cmsgate\hutkigrosh\hro\client\CompletionPanelHutkigroshHRO_v1;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\woocommerce\hro\accordions\AccordionHRO_Woo;
use esas\cmsgate\woocommerce\hro\accordions\AccordionTabHRO_Woo;

class CompletionPanelHutkigroshHRO_Woo extends CompletionPanelHutkigroshHRO_v1
{
    public static function builder() {
        return new CompletionPanelHutkigroshHRO_Woo();
    }

    public function build() {
        if (!$this->orderCanBePayed) {
            return MessagesPanelHROFactory::findBuilder()->build();
        }
        $completionPanel = element::content(
            element::div(
                attribute::id("completion-text"),
                attribute::clazz($this->getCssClass4CompletionTextDiv()),
                element::content($this->completionText)
            ),
            $this->elementTabs()
        );
        return $completionPanel;
    }

    public function accordionBuilder() {
        return AccordionHRO_Woo::builder();
    }

    public function elementTab($key, $header, $body, $selectable = true) {
        return AccordionTabHRO_Woo::builder()
            ->setChecked($this->isTabChecked($key))
            ->setHeader($header)
            ->setBody($body)
            ->setKey($key)
            ->setOnlyOneTabEnabled($this->isOnlyOneTabEnabled());
    }

    public function getCssClass4MsgSuccess() {
        return "woocommerce-message";
    }

    public function getCssClass4MsgUnsuccess() {
        return "woocommerce-error";
    }

    public function getCssClass4Button() {
        return "button";
    }

}