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
 * Class Steerfox_Plugins_Model_Log
 */
class Steerfox_Plugins_Model_Log extends Mage_Core_Model_Abstract
{
    const STEERFOX_LOG_TYPE_INFO = 'info';
    const STEERFOX_LOG_TYPE_ERROR = 'error';
    const STEERFOX_LOG_TYPE_WARNING = 'warning';

    protected function _construct()
    {
        $this->_init('steerfox_plugins/log');
    }


    /**
     * Before save hook : update timestamps
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Update Shop and locale
        $this->_updateShop();

        // Actions before save
        $this->_updateTimestamps();

        return $this;
    }

    /**
     * Implements timestamps => now + zone
     * @return $this
     */
    protected function _updateTimestamps()
    {
        $timestamp = now();

        // Set the last updated timestamp.
        $this->setCreatedAt($timestamp);

        // Set the timezone
        $this->setCreatedAtZone(date('O'));

        return $this;
    }


    /**
     * Implements locale + shop
     * @return $this
     */
    protected function _updateShop()
    {
        $store = Mage::app()->getStore();

        // Locale
        $this->setLocale(Mage::getStoreConfig('steerfox_plugins/catalog/export_lang') === null ? Mage::getStoreConfig('general/locale/code', $this->getData('id_shop'))  : Mage::getStoreConfig('steerfox_plugins/catalog/export_lang'));

        // Feed_id
        $feed_id = Mage::getStoreConfig('steerfox_plugins/account/feed_id');
        if ($feed_id > 0) {
            $this->setFeedId($feed_id);
        }

        // Shop : {websiteid}-{group_name}
        if (null === $this->getShop) {
            $this->setShop($store->getWebsiteId() . '-' . $store->getGroup()->getName());
        }

        return $this;
    }
}