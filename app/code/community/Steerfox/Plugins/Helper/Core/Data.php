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

//Add Steerfox Librarie
require_once dirname(__FILE__) . '/../../lib/SteerfoxContainer.php';

/**
 * Class Steerfox_Plugins_Helper_Core_Data
 */
class Steerfox_Plugins_Helper_Core_Data extends Mage_Core_Helper_Abstract
{
    public static $hParams = array();
    private $types
        = array(
            0 => 'none',
            1 => 'curl',
            2 => 'fopen',
        );
    private $services
        = array(
            'createAccount' => array(
                'path' => 'account',
                'method' => 'POST',
                'args' => array(
                    'source',
                    'shop_name',
                    'shop_url',
                    'logo_url',
                    'feed_url',
                    'locale',
                    'email',
                ),
            ),
            'updateAccount' => array(
                'path' => 'feeds/@id',
                'method' => 'PUT',
                'args' => array(
                    'id',
                    'url',
                    'locale',
                ),
                'get_args' => array(
                    'api_key',
                ),
            ),
            'retrieveAccount' => array(
                'path' => 'feeds',
                'method' => 'POST',
                'args' => array(
                    'url',
                    'locale',
                ),
                'get_args' => array(
                    'api_key',
                    'shop_url',
                ),
            ),
        );

    /**
     * Steerfox_Plugins_Helper_Core_Data constructor.
     */
    public function __construct()
    {
        SteerfoxContainer::createInstance(Mage::getModuleDir('etc', 'Steerfox_Plugins') . '/steerfox.xml');
    }

    /**
     * Return an instance of steerfox container.
     *
     * @return SteerfoxContainer
     */
    public function getSteerfoxContainer()
    {
        return SteerfoxContainer::getInstance();
    }

    /**
     * Update Steerfox API account information's.
     */
    public function apiUpdate()
    {
        // Update only if account key existe and Account status is active
        $apiKey = Mage::getStoreConfig('steerfox_plugins/account/api_key');
        $accountStatus = Mage::getStoreConfig('steerfox_plugins/account/status');

        if (!empty($apiKey) && 1 == $accountStatus) {
            $coreHelper = Mage::helper('steerfox_plugins/core_data');
            $steerfoxContainer = $coreHelper->getSteerfoxContainer();
            $steerfoxApi = $steerfoxContainer->get('api');
            $steerfoxApi instanceof SteerfoxApiService;
            $result = $steerfoxApi->updateAccount();

            if (!$result) {
                Mage::getSingleton('core/session')->addError('Steerfox update account : Error occurred.');
            }

            Mage::app()->getStore()->resetConfig();
        }
    }

}
