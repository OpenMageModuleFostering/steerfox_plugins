<?php
/**
 * Copyright 2016 Steerfox SAS.
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
 * @copyright 2016 Steerfox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

// Remove all inactive product from steerfox product collection

$websiteList = Mage::app()->getWebsites();
$shopList = array();

foreach ($websiteList as $website) {

    $productCollection = Mage::getModel("catalog/product")
        ->getCollection()
        ->addWebsiteFilter($website->getId())
        ->addAttributeToFilter(
            'status',
            array('eq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
        )
        ->addAttributeToSelect("*");

    $productIdsToDelete = array();

    foreach ($productCollection as $product) {
        $productIdsToDelete[] = $product->getId();
    }

    if (!empty($productIdsToDelete)) {
        $steerfoxProducts = Mage::getModel('steerfox_plugins/product')->getCollection()
            ->addFieldToFilter('id_product', array('in' => $productIdsToDelete))
            ->addFieldToFilter('id_shop', array('eq' => $website->getId()));
        if (0 < $steerfoxProducts->count()) {
            foreach ($steerfoxProducts as $steerfoxProduct) {
                $steerfoxProduct->delete();
            }
        }
    }
}
