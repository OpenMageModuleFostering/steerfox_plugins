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
 * Class Steerfox_Plugins_Block_Adminhtml_System_Config_Form_Fieldset_Steerfoxform
 */
class Steerfox_Plugins_Block_Adminhtml_System_Config_Form_Fieldset_Steerfoxform
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        // If no store is selecteed
        $selected_website = Mage::app()->getRequest()->getParam('website', null);
        $selected_store = Mage::app()->getRequest()->getParam('store', null);

        // Add the version notity if necessary
        Mage::helper('steerfox_plugins')->notifyOldRelease();

        // Valid user
        if (Mage::helper('steerfox_plugins')->isValidUser($selected_website, $selected_store)) {
            $tpl = $this->renderDashboardScreen();
            // Render Welcome / Settings + form
            return $tpl ;
        } else {
            return $this->renderWelcomeScreen();
        }
    }

    /**
     * Render Welcome screen with register action : scr-001
     * @return mixed
     */
    public function renderWelcomeScreen()
    {
        return Mage::app()->getLayout()->createBlock('adminhtml/template')->setTemplate(
            'steerfox/plugins/welcome.phtml'
        )->toHtml();
    }

    /**
     * Render Settings screen with dashboard : scr-002
     * @return mixed
     */
    public function renderDashboardScreen()
    {
        $block = Mage::app()->getLayout()->createBlock('adminhtml/template');
        $block->assign(array('checklist' => Mage::helper('steerfox_plugins')->getSettingsChecklist()));

        return $block->setTemplate(
            'steerfox/plugins/dashboard.phtml'
        )->toHtml();
    }
}
