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
 * Connect to steerfox API
 */
class SteerfoxApiService
{
    /**
     * Le connecteur (CURL ou FOPEN)
     *
     * @var SteerfoxAbstractConnector
     */
    protected $connector;

    /**
     * Api version.
     *
     * @var string
     */
    protected $apiVersion = '1.0';

    /**
     * Base url.
     *
     * @var string
     */
    protected $baseUrl;

    protected $requestParameters
        = array(
            'account' => array(
                'cPOST' => array(
                    'source' => array('required' => true),
                    'name' => array('required' => true),
                    'locale' => array('required' => false, 'default' => 'en_GB'),
                    'email' => array('required' => true),
                ),
            ),
            'shop' => array(
                'cPOST' => array(
                    'name' => array('required' => true),
                    'url' => array('required' => true),
                    'logo' => array('required' => true),
                    'email' => array('required' => true),
                    'locale' => array('required' => false, 'default' => 'en_GB'),
                    'currency' => array('required' => false, 'default' => 'EUR'),
                ),
                'iPUT' => array(
                    'name' => array('required' => true),
                    'url' => array('required' => true),
                    'logo' => array('required' => true),
                    'email' => array('required' => true),
                    'locale' => array('required' => false, 'default' => 'en_GB'),
                    'currency' => array('required' => false, 'default' => 'EUR'),
                ),
            ),
            'feed' => array(
                'cPOST' => array(
                    'url' => array('required' => true),
                    'locale' => array('required' => false, 'default' => 'en_GB'),
                    'currency' => array('required' => false, 'default' => 'EUR'),
                    'active' => array('required' => false, 'default' => 'true'),
                    'shop_id' => array('required' => true),
                ),
                'iPUT' => array(
                    'url' => array('required' => true),
                    'locale' => array('required' => false, 'default' => 'en_GB'),
                    'currency' => array('required' => false, 'default' => 'EUR'),
                    'active' => array('required' => false, 'default' => 'true'),
                    'shop_id' => array('required' => true),
                ),
                'cGet' => array(
                    'shop_id' => array('required' => false),
                ),
            ),
        );

    /**
     * SteerfoxApiConnector constructor.
     */
    public function __construct()
    {
        $this->setMethod();
        $this->setBaseUrl();
    }

    /**
     * Création d'un compte d'essai.
     *
     * @throws SteerfoxConnectorException
     */
    public function createAccount()
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxConfigurationInterface $config */
        $config = $container->get('config');
        /** @var SteerfoxAbstractShopService $shopService */
        $shopService = $container->get('shop');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = $container->get('tools');
        try {
            /** @var SteerfoxShopAdapterInterface $mainShop */
            $mainShop = $shopService->getMainShop();
            $url = $this->getUrl('/account');
            $params = array(
                'source' => $config->getCmsId(),
                'name' => $mainShop->getName(),
                'locale' => $mainShop->getLocale(),
                'email' => $config->getEmail(),

            );
            $requestBody = $this->setRequestBody($this->requestParameters['account']['cPOST'], $params);
            $response = $this->connector->post($url, $requestBody, 'createAccount');
            $config->setGlobal('STEERFOX_ACCOUNT_API_KEY', $response['api_key']);
            $config->setGlobal('STEERFOX_ACCOUNT_STATUS', true);
            $confirmUrl = $response['confirm_url'];
            $shops = $shopService->getShops();

            /** @var SteerfoxShopAdapterInterface $shop */
            foreach ($shops as $shop) {
                $shopResponse = $this->createShop($response['api_key'], $shop);
                $this->createFeed($response['api_key'], $shopResponse['id'], $shop);
            }

            $logger->addLog(
                $tool->jsonEncode('Success'),
                'createAccount',
                SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
            );

            return $confirmUrl;
        } catch (SteerfoxException $ste) {
            $logger->addLog(
                addslashes($tool->jsonEncode($ste->getMessage())),
                'createAccount',
                SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_ERROR
            );
            throw new Exception($ste->getMessage());
        }
    }

    /**
     * Check if the current API KEY is good and get the stores API info.
     *
     * @return bool TRUE if account is found
     */
    public function retrieveAccount()
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxConfigurationInterface $config */
        $config = $container->get('config');
        /** @var SteerfoxAbstractShopService $shopService */
        $shopService = $container->get('shop');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = $container->get('tools');
        try {
            $apiKey = $config->get('STEERFOX_ACCOUNT_API_KEY');
            $accountStatus = $config->get('STEERFOX_ACCOUNT_STATUS');

            if (empty($apiKey) || 1 == $accountStatus) {
                // if empty apiKey or already enable account, stop treatment
                return false;
            }

            $retrieveUrl = $this->getUrl('/shops?api_key='.$apiKey);
            $localShopList = $shopService->getShops();
            $accountShopList = $this->connector->get($retrieveUrl, 'getShopList');
            $config->set('STEERFOX_ACCOUNT_STATUS', 1);

            foreach ($accountShopList as $shop) {

                /** @var SteerfoxShopAdapterInterface $localShop */
                foreach ($localShopList as $localShop) {
                    if ($localShop->getUrl() == $shop['url']) {
                        // Shop exist, define conf.
                        $config->set('STEERFOX_EXPORT_LANG', $localShop->getLanguage(), $localShop->getId());
                        $config->set('STEERFOX_EXPORT_CURRENCY', $shop['currency'], $localShop->getId());
                        $config->set('STEERFOX_SHOP_ID', $shop['id'], $localShop->getId());

                        //retrieve feed
                        $retrieveFeedUrl = $this->getUrl('/feeds?api_key='.$apiKey.'&shop_id='.$shop['id']);
                        $feedResponse = $this->connector->get($retrieveFeedUrl, 'getFeedList');

                        if (0 < count($feedResponse)) {
                            $config->set('STEERFOX_FEED_ID', $feedResponse[0]['id'], $localShop->getId());
                        }

                    }
                }
            }

            $logger->addLog(
                $tool->jsonEncode('Success'),
                'retrieveAccount',
                SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
            );
            $success = true;
        } catch (Exception $ste) {
            // Disable Account
            $config->set('STEERFOX_ACCOUNT_STATUS', 0);
            $logger->addLog(
                $tool->jsonEncode($ste->getMessage()),
                'retrieveAccount',
                SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_ERROR
            );
            $success = false;
        }

        return $success;
    }

    /**
     * Update API information's with CMS datas
     *
     * @return bool TRUE if account is updated
     */
    public function updateAccount()
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxConfigurationService $config */
        $config = $container->get('config');
        /** @var SteerfoxAbstractShopService $shopService */
        $shopService = $container->get('shop');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = $container->get('tools');
        try {
            $apiKey = $config->getGlobal('STEERFOX_ACCOUNT_API_KEY');

            if (empty($apiKey)) {
                // if empty apiKey, stop treatment
                return false;
            }

            $localShopList = $shopService->getShops();
            $oneUpdate = false;

            /** @var SteerfoxShopAdapterInterface $localShop */
            foreach ($localShopList as $localShop) {

                $shopIdApi = $config->get('STEERFOX_SHOP_ID', $localShop->getId());

                if (empty($shopIdApi)) {
                    // Shop ID not find in configuration
                    $retrieveShop = false;
                    try {
                        //Create new shop
                        $responseShop = $this->createShop($apiKey, $localShop);
                        $shopIdApi = $responseShop['id'];
                        $oneUpdate = true;
                    } catch (Exception $ex) {
                        // Error during create, try to retrieve shop.
                        $retrieveShop = true;
                    }

                    if ($retrieveShop) {
                        // Shop NOT EXIST in conf and CANNOT BE CREATE, try to retrieve and update it.
                        $shop = $this->retrieveShop($apiKey, $localShop);
                        if (null != $shop) {
                            $shopIdApi = $shop['id'];
                            $this->updateShop($apiKey, $shopIdApi, $localShop, $config);
                            $oneUpdate = true;
                        }
                    }
                } else {
                    $this->updateShop($apiKey, $shopIdApi, $localShop, $config);
                    $oneUpdate = true;
                }

                if (!empty($shopIdApi)) {
                    $feedIdApi = $config->get('STEERFOX_FEED_ID', $localShop->getId());

                    if (empty($feedIdApi)) {
                        $retrieveFeed = false;
                        try {
                            //Create new shop
                            $this->createFeed($apiKey, $shopIdApi, $localShop);
                            $oneUpdate = true;
                        } catch (Exception $ex) {
                            // Error during create, try to retrieve shop.
                            $retrieveFeed = true;
                        }

                        if ($retrieveFeed) {
                            // Feed NOT EXIST in conf and CANNOT BE CREATE, try to retrieve it.
                            $feed = $this->retrieveFeed($apiKey, $shopIdApi, $localShop);
                            if (null != $feed) {
                                $this->updateFeed($apiKey, $feed['id'], $shopIdApi, $localShop);
                                $oneUpdate = true;
                            }
                        }
                    } else {
                        //UPDATE
                        $this->updateFeed($apiKey, $feedIdApi, $shopIdApi, $localShop);
                        $oneUpdate = true;
                    }
                }
            }

            $success = true;

            if (!$oneUpdate) {
                $config->setGlobal('STEERFOX_ACCOUNT_STATUS', 0);
                $success = false;
                $logger->addLog(
                    $tool->jsonEncode('Nothing to update'),
                    'updateAccount',
                    SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
                );
            } else {
                $logger->addLog(
                    $tool->jsonEncode('Success'),
                    'updateAccount',
                    SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
                );
            }
        } catch (Exception $ste) {
            // Disable Account
            $config->setGlobal('STEERFOX_ACCOUNT_STATUS', 0);
            $logger->addLog(
                $tool->jsonEncode($ste->getMessage()),
                'updateAccount',
                SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_ERROR
            );
            $success = false;
        }

        return $success;
    }

    /**
     * Set request body parameters.
     *
     * @param array $requestParameters Request body parameters.
     * @param array $parameters        Parameters values.
     *
     * @return array
     * @throws SteerfoxConnectorException
     */
    protected function setRequestBody($requestParameters, $parameters)
    {
        $body = array();
        foreach ($requestParameters as $key => $requestParameter) {
            if (array_key_exists($key, $parameters) && trim($parameters[$key]) !== '') {
                $body[$key] = $parameters[$key];
            } else {
                if ($requestParameter['required']) {
                    throw new SteerfoxConnectorException(
                        $key.' is a required parameter',
                        '',
                        null,
                        E_ERROR
                    );
                } else {
                    if (array_key_exists('default', $requestParameter)) {
                        $body[$key] = $requestParameter['default'];
                    }
                }
            }
        }

        return $body;
    }

    /**
     * Return complete and formated url.
     *
     * @param string $path   Api entry path
     * @param array  $params Url parameters.
     *
     * @return string
     */
    protected function getUrl($path, $params = array())
    {
        foreach ($params as $key => $value) {
            $path = str_replace(':'.$key, $value, $path);
        }

        return $this->baseUrl.$path;
    }

    /**
     * Set method (CURL or FOPEN).
     *
     * @throws SteerfoxConnectorException
     */
    protected function setMethod()
    {
        if ((bool)function_exists('curl_version')) {
            $this->connector = new SteerfoxCurlConnector();
        } elseif ((bool)ini_get('allow_url_fopen')) {
            $this->connector = new SteerfoxFopenConnector();
        } else {
            throw new SteerfoxConnectorException(
                'Nor CURL nor FOPEN is activated on the server',
                'Api:Construct',
                null,
                E_ERROR
            );
        }
    }

    /**
     * Set api base url.
     */
    protected function setBaseUrl()
    {
        if (getenv('STEERFOX_API_DEV') &&
            getenv('STEERFOX_API_USER') &&
            getenv('STEERFOX_API_PWD')
        ) {
            $this->baseUrl = 'http://'.
                getenv('STEERFOX_API_USER').':'.
                getenv('STEERFOX_API_PWD').
                '@api.test.steerfox.com';
        } else {
            $this->baseUrl = 'http://api.steerfox.com';
        }
    }

    /**
     * Create a new shop with API and shop Object.
     *
     * @param                              $apiKey
     * @param SteerfoxShopAdapterInterface $shop
     *
     * @return array Create WS response.
     *
     * @throws SteerfoxConnectorException
     */
    protected function createShop($apiKey, SteerfoxShopAdapterInterface $shop)
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxConfigurationInterface $config */
        $config = $container->get('config');

        $shopUrl = $this->getUrl('/shops?api_key='.$apiKey);
        $params = array(
            'name' => $shop->getName(),
            'url' => $shop->getUrl(),
            'locale' => $shop->getLocale(),
            'email' => $config->getEmail(),
            'logo' => $shop->getLogo(),
            'currency' => $shop->getCurrency(),
        );

        $requestBody = $this->setRequestBody($this->requestParameters['shop']['cPOST'], $params);
        $shopResponse = $this->connector->post($shopUrl, $requestBody, 'addShop');
        $config->set('STEERFOX_SHOP_ID', $shopResponse['id'], $shop->getId());
        $config->set('STEERFOX_EXPORT_LANG', $shop->getLanguage(), $shop->getId());
        $config->set('STEERFOX_EXPORT_CURRENCY', $shopResponse['currency'], $shop->getId());

        return $shopResponse;
    }

    /**
     * Create a new Feed with API, shop API ID and shop Object.
     *
     * @param                              $apiKey
     * @param                              $shopApiId
     * @param SteerfoxShopAdapterInterface $localShop
     *
     * @return array Create WS response.
     *
     * @throws SteerfoxConnectorException
     */
    protected function createFeed($apiKey, $shopApiId, SteerfoxShopAdapterInterface $localShop)
    {
        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxConfigurationInterface $config */
        $config = $container->get('config');

        $feedUrl = $this->getUrl('/feeds?api_key='.$apiKey);

        $feedParams = array(
            'url' => $localShop->getShopFeedUrl(),
            'locale' => $localShop->getLocale(),
            'currency' => $localShop->getCurrency(),
            'shop_id' => $shopApiId,
        );

        $feedBody = $this->setRequestBody($this->requestParameters['feed']['cPOST'], $feedParams);
        $feedResponse = $this->connector->post($feedUrl, $feedBody, 'addFeed');
        $config->set('STEERFOX_FEED_ID', $feedResponse['id'], $localShop->getId());

        return $feedResponse;
    }

    /**
     * Retrieve APIshop who match with localShop
     *
     * @param                              $apiKey
     * @param SteerfoxShopAdapterInterface $localShop
     *
     * @return array|null
     */
    protected function retrieveShop($apiKey, SteerfoxShopAdapterInterface $localShop)
    {
        $retrieveUrl = $this->getUrl('/shops?api_key='.$apiKey);
        $accountShopList = $this->connector->get($retrieveUrl);

        foreach ($accountShopList as $shop) {
            if ($localShop->getUrl() == $shop['url']) {
                return $shop;
            }
        }

        return null;
    }

    /**
     * Retrieve APIFeed who match with localShop
     *
     * @param                              $apiKey
     * @param                              $shopIdApi
     * @param SteerfoxShopAdapterInterface $localShop
     *
     * @return array|null
     */
    protected function retrieveFeed($apiKey, $shopIdApi, SteerfoxShopAdapterInterface $localShop)
    {
        $retrieveUrl = $this->getUrl('/feeds?api_key='.$apiKey.'&shop_id='.$shopIdApi);
        $feedList = $this->connector->get($retrieveUrl);

        foreach ($feedList as $feed) {
            if ($localShop->getUrl() == $feed['url']) {
                return $feed;
            }
        }

        return null;
    }

    /**
     * Update API Shop
     *
     * @param                                $apiKey
     * @param                                $shopIdApi
     * @param SteerfoxShopAdapterInterface   $localShop
     * @param SteerfoxConfigurationInterface $config
     *
     * @return void
     *
     * @throws SteerfoxConnectorException
     */
    protected function updateShop(
        $apiKey,
        $shopIdApi,
        SteerfoxShopAdapterInterface $localShop,
        SteerfoxConfigurationService $config
    ) {
        //UPDATE
        $shopUrl = $this->getUrl('/shops/'.$shopIdApi.'?api_key='.$apiKey);
        $params = array(
            'name' => $localShop->getName(),
            'url' => $localShop->getUrl(),
            'locale' => $localShop->getLocale(),
            'email' => $config->getEmail(),
            'logo' => $localShop->getLogo(),
            'currency' => $localShop->getCurrency(),
        );

        $requestBody = $this->setRequestBody($this->requestParameters['shop']['iPUT'], $params);
        $this->connector->put($shopUrl, $requestBody, 'updateShop');
    }

    /**
     * Update API Feed
     *
     * @param                              $apiKey
     * @param                              $feedIdApi
     * @param                              $shopIdApi
     * @param SteerfoxShopAdapterInterface $localShop
     *
     * @return void
     *
     * @throws SteerfoxConnectorException
     */
    protected function updateFeed($apiKey, $feedIdApi, $shopIdApi, SteerfoxShopAdapterInterface $localShop)
    {
        $feedUrl = $this->getUrl('/feeds/'.$feedIdApi.'?api_key='.$apiKey);

        $feedParams = array(
            'url' => $localShop->getShopFeedUrl(),
            'locale' => $localShop->getLocale(),
            'currency' => $localShop->getCurrency(),
            'shop_id' => $shopIdApi,
        );

        $feedBody = $this->setRequestBody($this->requestParameters['feed']['iPUT'], $feedParams);
        $this->connector->put($feedUrl, $feedBody, 'updateFeed');
    }
}
