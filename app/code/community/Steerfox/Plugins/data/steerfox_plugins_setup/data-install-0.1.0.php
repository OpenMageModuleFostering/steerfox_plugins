<?php
/**
 * Copyright 2015 Steerfox SAS.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @author    Steerfox <tech@steerfox.com>
 * @copyright 2015 Steerfox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

$websiteList = Mage::app()->getWebsites();
$shopList = array();

foreach($websiteList as $website){

    $productCollection = Mage::getModel("catalog/product")
        ->getCollection()
        ->addWebsiteFilter($website->getId())
        ->addAttributeToFilter(
            'status',
            array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
        )
        ->addAttributeToSelect("*");
    foreach($productCollection as $product){
        Mage::getModel('steerfox_plugins/product')
            ->setData(
                array(
                    'id_product' => $product->getId(),
                    'id_shop' => $website->getId(),
                    'active' => 1,
                )
            )
            ->setOrigData()
            ->save();
    }
}

// Set default values
Mage::getModel('core/config')->saveConfig('steerfox_plugins/account/status', 0);
Mage::getModel('core/config')->saveConfig('steerfox_plugins/account/feed_id', 0);
Mage::getModel('core/config')->saveConfig(
    'steerfox_plugins/catalog/export_lang', Mage::getStoreConfig('general/locale/code')
);
Mage::getModel('core/config')->saveConfig(
    'steerfox_plugins/catalog/export_currency', Mage::getStoreConfig('currency/options/default')
);

// Insert installation log
$store = Mage::app()->getStore();
$steerfox_helper = Mage::helper('steerfox_plugins');
$conf = array(
    'source' => $steerfox_helper->getSource(),
    'version' => Mage::getVersion(),
    'shop_url' => $store->getHomeUrl(),
    'php' => array(
        'version' => phpversion(),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ),
    'message' => 'Installation complete',
);
$log = Mage::getModel('steerfox_plugins/log');
$log->setData(
    array(
        'action' => 'module::install',
        'type' => Steerfox_Plugins_Model_Log::STEERFOX_LOG_TYPE_INFO,
        'message' => json_encode($conf),
    )
)->setOrigData()->save();
