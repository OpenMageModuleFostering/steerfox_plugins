<?php
/**
 *  Copyright 2015 SteerFox SAS.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License"); you may
 *  not use this file except in compliance with the License. You may obtain
 *  a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 *  WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 *  License for the specific language governing permissions and limitations
 *  under the License.
 *
 * @author    SteerFox <tech@steerfox.com>
 * @copyright 2015 SteerFox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

require_once Mage::getModuleDir('', 'Steerfox_Plugins') . '/lib/SteerfoxContainer.php';

/**
 * Tool adapter.
 */
class Steerfox_Plugins_Model_Adapter_Service_Shop extends SteerfoxAbstractShopService
{
    /**
     * @inheritDoc
     */
    public function getShops()
    {
        $websiteList = Mage::app()->getWebsites();
        $shopList = array();

        foreach ($websiteList as $website) {
            $shopList[] = new Steerfox_Plugins_Model_Adapter_Shop($website);
        }

        return $shopList;
    }

    /**
     * @inheritDoc
     */
    public function getMainShop()
    {
        $websiteList = Mage::app()->getWebsites();
        //On récupère le premier webstite
        $websiteArray = array_values($websiteList);
        $website = $websiteArray[0];

        return new Steerfox_Plugins_Model_Adapter_Shop($website);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentShop()
    {
        return new Steerfox_Plugins_Model_Adapter_Shop(Mage::app()->getWebsite());
    }

    /**
     * Return current shop.
     *
     * @return SteerfoxProductAdapterInterface[]
     */
    public function getProductsToExport(SteerfoxShopAdapterInterface $shop)
    {
        //On charge les produits à exporter.
        $steerfoxProductCollection = Mage::getModel('steerfox_plugins/product')->getCollection();
        $steerfoxProductCollection->addFilter('active', 1);
        $steerfoxProductCollection->addFilter('id_shop', $shop->getId());

        // TODO Keep this for future use of export by store group.
//        // recreate this function
//        $shopCategory = Mage::getModel('catalog/category')->load($shop->getDefaultCategoryId());
//        $steerfoxProductCollection = Mage::getModel('steerfox_plugins/product')
//            ->getCollection();
//        $steerfoxProductCollection
//            ->getSelect()
//            ->joinLeft(
//                array('categ' => Mage::getSingleton('core/resource')->getTableName('catalog/category_product')),
//                'categ.product_id=main_table.id_product',
//                array()
//            )
//            ->group('main_table.id_product')
//            ->group('main_table.id_shop')
//            ->order('main_table.entity_id');
//        ;
//        $steerfoxProductCollection
//            ->addFieldToFilter('active', 1)
//            ->addFieldToFilter('id_shop', $shop->getId())
//            ->addFieldToFilter('category_id', array('in' => explode(',', $shopCategory->getAllChildren())))
//        ;
        // FIN TODO
        $productsToExport = array();

        foreach ($steerfoxProductCollection as $steerfoxProduct) {
            $productsToExport[] = new Steerfox_Plugins_Model_Adapter_Product($steerfoxProduct);
        }

        return $productsToExport;
    }


}
