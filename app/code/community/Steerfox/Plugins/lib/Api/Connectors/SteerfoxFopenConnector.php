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
 * FOPEN Api connector
 */
class SteerfoxFopenConnector extends SteerfoxAbstractConnector
{
    /**
     * @inheritdoc
     */
    protected function query($url, $method, $bodyParameters = array(), $action = '')
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxToolsAdapterInterface $tools */
        $tools = $container->get('tools');
        $datas = $tools->jsonEncode($bodyParameters);
        // Format header
        $header = array(
            'http' =>
                array(
                    'method' => $method,
                    'header' => "Content-Type: application/json\r\n".
                        "Content-Length: ".$tools->strlen($datas)."\r\n".
                        "Connection: close\r\n",
                    'content' => $datas, // Datas
                    'timeout' => 5,
                    'ignore_errors' => true, // Ignore HTTP error codes
                ),
        );
        $context = stream_context_create($header);

        // Execute
        $return = $tools->fileGetContents($url, false, $context);
        $responseCode = $this->getHttpResponseCode($http_response_header);

        // Format return
        $return = $tools->jsonDecode($return, true);

        // HTTP code Errors management
        $this->handleErrors($responseCode, $action, $return, $bodyParameters);
    }

    /**
     * Determine HTTP response code.
     *
     * @param array $httpResponseHeader HTTP response headers
     *
     * @return mixed
     */
    private function getHttpResponseCode($httpResponseHeader)
    {
        $head = array();
        foreach ($httpResponseHeader as $header) {
            $t = explode(':', $header, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $header;
                if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $out)) {
                    $head['reponse_code'] = (int)$out[1];
                }
            }
        }

        return $head['reponse_code'];
    }
}
