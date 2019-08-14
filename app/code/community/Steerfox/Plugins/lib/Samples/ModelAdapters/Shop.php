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

require_once Mage::getModuleDir('', 'Steerfox_Plugins').'/lib/SteerfoxContainer.php';

class Steerfox_Plugins_Model_Adapter_Shop implements SteerfoxShopAdapterInterface
{
    /**
     * Store Magento.
     *
     * @var Mage_Core_Model_Store
     */
    private $shop;

    public function __construct(Mage_Core_Model_Store $store)
    {
        $this->shop = $store;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->shop->getId();
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        $url = Mage::app()->getStore($this->shop->getId())->getBaseUrl();

        if (getenv('STEERFOX_API_DEV') &&
            getenv('STEERFOX_API_USER') &&
            getenv('STEERFOX_API_PWD')
        ) {


            $url = 'http://test.'.uniqid().'.com/';
//
//            if(1== $this->shop->getId()){
//                $url = 'http://www.madison.com';
//            }else{
//                $url = 'http://www.second.com';
//            }
        }

        return $url;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->shop->getGroup()->getName();
    }

    /**
     * @inheritDoc
     */
    public function getLanguage()
    {
        return Mage::getStoreConfig('general/locale/code', $this->shop);
    }

    /**
     * @inheritDoc
     */
    public function getLogo()
    {
        // Emulate front store env to get logo
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->shop->getId());
        $logoUrl = Mage::getDesign()->getSkinUrl().Mage::getStoreConfig('design/header/logo_src', $this->shop);
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $logoUrl;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency()
    {
        return Mage::app()->getStore($this->shop->getId())->getCurrentCurrencyCode();
    }
}
