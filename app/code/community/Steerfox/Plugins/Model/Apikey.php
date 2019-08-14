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


/**
 * Class Steerfox_Plugins_Model_Apikey
 */
class Steerfox_Plugins_Model_Apikey extends Mage_Core_Model_Config_Data
{
    /**
     * Méthode permettant de valider le champ de config steerfox_plugins/account/api_key
     *
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function save()
    {
        parent::save();

        // Refresh the config.
        Mage::app()->getStore()->resetConfig();

        $coreHelper = Mage::helper('steerfox_plugins/core_data');
        $steerfoxContainer = $coreHelper->getSteerfoxContainer();
        $steerfoxApi = $steerfoxContainer->get('api');
        $steerfoxApi instanceof SteerfoxApiService;
        $result = $steerfoxApi->retrieveAccount();

        if (!$result) {
            Mage::getSingleton('core/session')->addError('Invalid APi Key, module account disabled.');
        }

        return $this;  //call original save method so whatever happened
    }
}