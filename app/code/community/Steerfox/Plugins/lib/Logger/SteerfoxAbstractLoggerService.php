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

/**
 * Logger service
 */
abstract class SteerfoxAbstractLoggerService
{
    const STEERFOX_LOG_TYPE_INFO = 'info';
    const STEERFOX_LOG_TYPE_ERROR = 'error';
    const STEERFOX_LOG_TYPE_WARNING = 'warning';

    /**
     * Add a log in database.
     *
     * @param string $message The message.
     * @param string $action  The action.
     * @param int    $level   The level.
     */
    abstract public function addLog($message, $action, $level);

    /**
     * Return all logs.
     *
     * @return array
     */
    abstract public function getAllLogs();

    /**
     * Return environment informations
     *
     * @return string
     */
    protected function getEnvironementInformations()
    {
        return array(
            'plugin' => $this->getPluginVersion(),
            'cms' => $this->getCmsVersion(),
            'php' => array(
                'version' => phpversion(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ),
        );
    }

    /**
     * Return plugin version.
     *
     * @return string
     */
    abstract protected function getPluginVersion();

    /**
     * Return cms version.
     *
     * @return string
     */
    abstract protected function getCmsVersion();
}