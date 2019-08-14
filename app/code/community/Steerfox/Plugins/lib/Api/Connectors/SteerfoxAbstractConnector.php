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
 * Abstract api connection class
 */
abstract class SteerfoxAbstractConnector
{
    /**
     * Execute GET request.
     *
     * @param string $url    URL.
     * @param string $action Action.
     *
     * @return mixed
     */
    public function get($url, $action = '')
    {
        return $this->query($url, 'GET', array(), $action);
    }

    /**
     * Execute POST request.
     *
     * @param string $url            URL.
     * @param array  $bodyParameters Request body parameters.
     * @param string $action         Action.
     *
     * @return mixed
     */
    public function post($url, $bodyParameters = array(), $action = '')
    {
        return $this->query($url, 'POST', $bodyParameters, $action);
    }

    /**
     * Execute PUT request.
     *
     * @param string $url            URL
     * @param array  $bodyParameters Request body parameters.
     * @param string $action         Action.
     *
     * @return mixed
     */
    public function put($url, $bodyParameters = array(), $action = '')
    {
        return $this->query($url, 'PUT', $bodyParameters, $action);
    }

    /**
     * Execute HTTP request.
     *
     * @param string $url            URL.
     * @param string $method         HTTP Method.
     * @param array  $bodyParameters Request body parameters.
     * @param string $action         Action.
     *
     * @return mixed
     */
    abstract protected function query($url, $method, $bodyParameters = array(), $action = '');

    /**
     * Handle return HTTP code.
     *
     * @param string $code   HTTP Code
     * @param string $action Action
     * @param string $return Return infos.
     *
     * @throws SteerfoxConnectorException
     * @throws SteerfoxException
     */
    protected function handleErrors($code, $action, $return, $bodyParameters = null)
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxToolsAdapterInterface $tools */
        $tools = $container->get('tools');
        // If not 20x status -> error
        if ($tools->substr($code, 0, 2) != 20) {
            switch ($code) {
                case 400:
                    $message = '';
                    // Case of email already use
                    if (array_key_exists('errors', $return)) {
                        $message = $tools->jsonEncode($return['errors']);
                        if (
                            array_key_exists('children', $return['errors'])
                            && array_key_exists('email', $return['errors']['children'])
                            && array_key_exists('errors', $return['errors']['children']['email'])
                            && array_key_exists(0,$return['errors']['children']['email']['errors'])
                        ) {
                            if ('Email already taken' == $return['errors']['children']['email']['errors'][0]) {
                                $locale = 'en';
                                if (null != $bodyParameters && array_key_exists('locale', $bodyParameters)) {
                                    $locale = $bodyParameters['locale'];
                                }

                                if ('fr' == $tools->substr($locale, 0, 2)) {
                                    $message = ' - Un ancien compte existe déjà pour votre boutique. Veuillez cliquer sur "Parametères avancés" et rentrez votre clé API. Si vous ne la connaissez pas, veuillez contacter votre conseiller SteerFox.';
                                } else {
                                    $message = ' - An older account already exist for your shop. Please click "Advanced settings" and enter your api key. If you do not know it, please contact your Steerfox account manager.';
                                }
                            }

                        }
                    }

                    throw new SteerfoxConnectorException(
                        "HTTP 400 : Error in parameters".$message,
                        $action,
                        $return,
                        2
                    );
                case
                401:
                    throw new SteerfoxConnectorException(
                        "HTTP 401 : Authentication error".(isset($return['errors']) ? ' - '.$return['errors'] : ''),
                        $action,
                        $return,
                        3
                    );
                case 403:
                    throw new SteerfoxConnectorException("HTTP 403 : Access right error", $action, $return, 4);
                case 404:
                    throw new SteerfoxConnectorException("HTTP 404 : Route not found", $action, $return, 5);
                case 405:
                    throw new SteerfoxConnectorException(
                        "HTTP 405 : Method not allowed".(isset($return['errors']) ? ' - '.$return['errors'] : ''),
                        $action,
                        $return,
                        6
                    );
                case 429:
                    throw new SteerfoxConnectorException("HTTP 429 : Too many requests", $action, $return, 9);
                default:
                    throw new SteerfoxConnectorException(
                        "HTTP $code : To implement return",
                        $action,
                        $return,
                        7
                    );
            }
        }
        if (isset($return['errors']) && !empty($return['errors'])) {
            throw new SteerfoxConnectorException((string)$return['message'], $action, $return);
        }
    }

    protected
    function logCall(
        $params,
        $url,
        $return
    ) {
        /** @var SteerfoxContainer $container */
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxToolsAdapterInterface $tools */
        $tools = $container->get('tools');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');
        $logger->addLog(
            $tools->jsonEncode(
                array(
                    'call_parameters' => $params,
                    'url' => $url,
                    'api_return' => $return,
                )
            ),
            'api:call',
            SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
        );
    }
}
