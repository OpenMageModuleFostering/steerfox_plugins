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
 * Class Steerfox_Plugins_Block_Adminhtml_Product
 */
class Steerfox_Plugins_Block_Adminhtml_Steerfoxproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'steerfox_plugins';
        $this->_controller = 'adminhtml_steerfoxproduct';
        $this->_headerText = Mage::helper('steerfox_plugins')->__('Manage Steerfox products export');

        parent::__construct();
        $this->_removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('steerfox_plugins/adminhtml_steerfoxproduct/edit');
    }
}
