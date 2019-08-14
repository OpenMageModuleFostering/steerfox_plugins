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

/**
 * Class Steerfox_Plugins_LogsController
 */
class Steerfox_Plugins_LogsController  extends Mage_Core_Controller_Front_Action
{
    /**
     * Action affichant au froma JSON les logs.
     */
    public function getAction(){
        $apiKey = $this->getRequest()->getParam('api_key', null);

        if(null == $apiKey){
            Mage::throwException('Missing parameter');
        }

        $configApiKey = Mage::getStoreConfig('steerfox_plugins/account/api_key');

        if($configApiKey != $apiKey){
            Mage::throwException('Invalid parameter');
        }

        $coreHelper = Mage::helper('steerfox_plugins/core_data');
        $steerfoxContainer = $coreHelper->getSteerfoxContainer();
        $logs = $steerfoxContainer->get('logger')->getAllLogs();

        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($logs));
    }
}