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
 * Class Steerfox_Plugins_Helper_Data
 */
class Steerfox_Plugins_Helper_Data extends Mage_Core_Helper_Abstract
{
    const STEERFOX_SOURCE_MAGE = 3;
    public $dashboard_action = 'http://dashboard.steerfox.com/';

    public function getDashboardAction()
    {
        return $this->dashboard_action;
    }

    public function getRegisterAction()
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/steerfoxindex/start/");
    }

    /**
     * Get the source (magento)
     * @return int
     */
    public function getSource()
    {
        return self::STEERFOX_SOURCE_MAGE;
    }

    /**
     * Return the current magento store.
     *
     * @return bool|Mage_Core_Model_Store
     */
    public function currentStore()
    {
        if (true) {
            return Mage::app()->getStore();
        }

        return false;
    }

    /**
     * Check if user is valid or not
     * @return bool
     */
    public function isValidUser($website = null, $store = null)
    {

        if ($store === null) {
            if ($website === null) {
                $api_key = Mage::getStoreConfig('steerfox_plugins/account/api_key');
            } else {
                $api_key = Mage::app()->getWebsite($website)->getConfig('steerfox_plugins/account/api_key');
            }
        } else {
            $api_key = Mage::app()->getStore($store)->getConfig('steerfox_plugins/account/api_key');
        }

        return (($api_key && !empty($api_key)) ? true : false);
    }

    /**
     * Get settings checklist to display
     * @return bool
     */
    public function getSettingsChecklist()
    {
        return array(
            array(
                'title' => $this->__('Connector status : Steerfox can be reached'),
                'state' => ((bool)function_exists('curl_version')) || ((bool)ini_get('allow_url_fopen')),
            ),
            array(
                'title' => $this->__('Steerfox authentication : API_KEY validity'),
                'state' => (Mage::getStoreConfig('steerfox_plugins/account/status') == 1 ? true : false),
            ),
        );
    }

    /**
     * Default product per page.
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        return 100;
    }

    /**
     * Get the config by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function getConfig($name)
    {
        return Mage::getStoreConfig('steerfox_plugins/'.$name);
    }

    /**
     * Update config.
     *
     * @param        $name
     * @param        $value
     * @param string $scope
     * @param int    $scopeId
     *
     * @return void
     */
    public function updateConfig($name, $value, $scope = 'default', $scopeId = 0)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($name, $value, $scope, $scopeId);
    }

    /**
     * Return the current extension version
     * .
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string)Mage::getConfig()->getNode()->modules->Steerfox_Plugins->version;
    }

    /**
     * Returns array with all availaible website.
     *
     * @return array
     */
    public function getWebsiteOption()
    {
        $websites = Mage::app()->getWebsites();


        $websiteOptions = array();
        foreach ($websites as $website) {
            $websiteOptions[$website->getId()] = $website->getName();
        }

        return $websiteOptions;
    }

    /**
     * Notify the user if the current plugin is older than the last release.
     */
    public function notifyOldRelease()
    {
        $lastReleaseVersion = Mage::getSingleton('core/session')->getData('steerfox_plugins_latest_version');

        if (null != $lastReleaseVersion) {
            $currentVersion = $this->getExtensionVersion();

            // Remove dots to reliable the test
            $lastReleaseVersion = str_replace('.', '', $lastReleaseVersion);
            $currentVersion = str_replace('.', '', $currentVersion);

            if ($currentVersion < $lastReleaseVersion) {
                Mage::getSingleton('adminhtml/session')->addWarning(
                    $this->__("A new version of Steerfox plugins is available. Please update to take advantage of new features.")
                );
            }
        }
    }
}
