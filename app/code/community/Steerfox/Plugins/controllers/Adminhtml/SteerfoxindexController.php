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
 * Class Steerfox_Plugins_Adminhtml_IndexController
 */
class Steerfox_Plugins_Adminhtml_SteerfoxindexController extends Mage_Adminhtml_Controller_Action
{

    const STEERFOX_SOURCE_MAGENTO = 2;


    protected function startAction()
    {
        try{
            $coreHelper = Mage::helper('steerfox_plugins/core_data');
            $steerfoxContainer = $coreHelper->getSteerfoxContainer();
            $steerfoxApi = $steerfoxContainer->get('api');
            $steerfoxApi instanceof SteerfoxApiService;
            $confirmUrl = $steerfoxApi->createAccount();
            Mage::app()->getStore()->resetConfig();

            $this->_redirectUrl($confirmUrl);
        }catch(Exception $ex) {
            // Refresh the config.
            Mage::app()->getStore()->resetConfig();
            Mage::logException($ex);
            Mage::getSingleton('core/session')->addError($ex->getMessage());

            $this->_redirect('adminhtml/system_config/edit', array('section' => 'steerfox_plugins'));
        }
    }

    /**
     * ACL
     * @return mixed
     */
    protected function _isAllowed()
    {
        $actionName = $this->getRequest()->getActionName();
        switch ($actionName) {
            case 'index':
            case 'edit':
            case 'delete':
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession->isAllowed('system/config/steerfox_plugins');
                break;
        }

        return $isAllowed;
    }
}
