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

class Steerfox_Plugins_Model_Adapter_Service_Log extends SteerfoxAbstractLoggerService
{
    /**
     * Return all logs.
     *
     * @return array
     */
    public function getAllLogs()
    {
        $logList = array();
        $logCollection = Mage::getModel('steerfox_plugins/log')->getCollection();

        foreach ($logCollection as $log) {
            $logInfo = array(
                'action' => $log->getAction(),
                'type' => $log->getType(),
                'feed_id' => $log->getFeedId(),
                'shop' => $log->getShop(),
                'locale' => $log->getLocale(),
                'message' => json_decode($log->getMessage()),
                'created_at' => $log->getCreatedAt(),
                'created_at_zone' => date('O'),
            );
            $logList[] = $logInfo;
        }

        return $logList;
    }

    /**
     * @inheritDoc
     */
    protected function getPluginVersion()
    {
        return (string)Mage::helper('steerfox_plugins')->getExtensionVersion();
    }

    /**
     * @inheritDoc
     */
    protected function getCmsVersion()
    {
        return Mage::getVersion();
    }


    /**
     * Add a log in database.
     *
     * @param string $message The message.
     * @param string $action  The action.
     * @param int    $level   The level.
     */
    public function addLog($message, $action, $level)
    {
        $log = Mage::getModel('steerfox_plugins/log');
        $log->setData(
            array(
                'action' => $action,
                'type' => $level,
                'message' => $message,
            )
        )->setOrigData()->save();
    }
}
