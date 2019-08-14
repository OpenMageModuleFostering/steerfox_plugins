<?php
/**
 * Copyright 2016 SteerFox SAS.
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
 * @author    SteerFox <tech@steerfox.com>
 * @copyright 2016 SteerFox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

// Generate hash for secure url
$uniqid = uniqid();
$hash = hash('adler32', $uniqid);

Mage::getModel('core/config')->saveConfig('steerfox_plugins/catalog/hash_ws', $hash);

$websiteList = Mage::app()->getWebsites();
foreach($websiteList as $website){
    /* @var $website Mage_Core_Model_Website */
    Mage::getModel('core/config')->saveConfig(
        'steerfox_plugins/catalog/main_store_view',
        $website->getDefaultStore()->getId(),
        'website',
        $website->getId()
    );
}

// Update steerfox data for renew feed url infos
Mage::helper('steerfox_plugins/core_data')->apiUpdate();
