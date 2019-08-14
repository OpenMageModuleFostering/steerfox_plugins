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
 * Class Steerfox_Plugins_Model_Product
 */
class Steerfox_Plugins_Model_Product extends Mage_Core_Model_Abstract
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    protected function _construct()
    {
        $this->_init('steerfox_plugins/product');
    }


    /**
     * Return available states as an array
     * @return array
     */
    public function getAvailableStates()
    {
        return array(
            self::ACTIVE => Mage::helper('steerfox_plugins')->__('Active'),
            self::INACTIVE => Mage::helper('steerfox_plugins')->__('Inactive'),
        );
    }

    /**
     * Before save hook : update timestamps
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Actions before save
        $this->_updateTimestamps();

        return $this;
    }


    /**
     * Implements timestamps => now
     * @return $this
     */
    protected function _updateTimestamps()
    {
        // Set the last updated timestamp.
        $this->setData('updated_at', Varien_Date::now());

        // If we have a brand new object, set the created timestamp.
        if ($this->isObjectNew()) {
            $this->setData('created_at', Varien_Date::now());
        }

        return $this;
    }
}