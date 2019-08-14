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
 * Connector exception
 */
class SteerfoxConnectorException extends Exception
{
    /**
     * SteerfoxConnectorException constructor.
     *
     * @param string         $message   The message.
     * @param string         $action    The action executed.
     * @param mixed          $apiReturn The api return message.
     * @param int            $code      The code.
     * @param Exception|null $previous  The previous exception.
     */
    public function __construct($message, $action, $apiReturn, $code = 0, Exception $previous = null)
    {
        /** @var SteerfoxLogService $logger */
        $logger = SteerfoxContainer::getInstance()->get('logger');
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = SteerfoxContainer::getInstance()->get('tools');

        $messageToLog = $message;
        if (!empty($apiReturn)) {
            if (is_array($message)) {
                $message['api-return'] = $apiReturn;
            } else {
                $messageToLog .= $tool->jsonEncode($apiReturn);
            }
        }

        $logger->addLog(
            $tool->jsonEncode($messageToLog),
            'ws::'.$action,
            SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_ERROR
        );

        parent::__construct(
            $message.($previous ? "\n".$previous->getMessage() : ''),
            $code
        );
    }
}
