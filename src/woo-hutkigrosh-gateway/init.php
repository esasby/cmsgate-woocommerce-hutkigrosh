<?php
if (!class_exists("esas\cmsgate\CmsPlugin"))
    require_once(dirname(__FILE__) . '/vendor/esas/cmsgate-core/src/esas/cmsgate/CmsPlugin.php');

use esas\cmsgate\CmsPlugin;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshWoo;


(new CmsPlugin(dirname(__FILE__) . '/vendor', dirname(__FILE__)))
    ->setRegistry(new RegistryHutkigroshWoo())
    ->init();
