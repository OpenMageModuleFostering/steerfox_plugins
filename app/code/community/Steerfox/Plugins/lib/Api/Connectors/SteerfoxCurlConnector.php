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
 * CURL Api connector
 */
class SteerfoxCurlConnector extends SteerfoxAbstractConnector
{
    /**
     * @inheritdoc
     */
    protected function query($url, $method, $bodyParameters = array(), $action = '')
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxToolsAdapterInterface $tools */
        $tools = $container->get('tools');

        // Create curl resource
        $ch = curl_init();
        $datas = $tools->jsonEncode($bodyParameters);

        // Setups
        curl_setopt($ch, CURLOPT_URL, $url); // URL
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: '.$tools->strlen($datas),
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); // Post datas
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute
        $return = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close curl resource
        curl_close($ch);

        // Format return
        $return = $tools->jsonDecode($return, true);

        $this->logCall($bodyParameters, $url, $return);

        $this->handleErrors($http_code, $action, $return, $bodyParameters);
        
        return $return;
    }
}
