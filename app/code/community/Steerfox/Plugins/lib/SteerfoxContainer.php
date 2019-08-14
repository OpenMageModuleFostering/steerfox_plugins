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

require_once dirname(__FILE__).'/Exceptions/SteerfoxConnectorException.php';
require_once dirname(__FILE__).'/Exceptions/SteerfoxException.php';
require_once dirname(__FILE__).'/Logger/SteerfoxAbstractLoggerService.php';
require_once dirname(__FILE__).'/Api/Connectors/SteerfoxAbstractConnector.php';
require_once dirname(__FILE__).'/Api/Connectors/SteerfoxCurlConnector.php';
require_once dirname(__FILE__).'/Api/Connectors/SteerfoxFopenConnector.php';
require_once dirname(__FILE__).'/Api/SteerfoxApiService.php';
require_once dirname(__FILE__).'/Export/SteerfoxExportService.php';
require_once dirname(__FILE__).'/Export/Model/SteerfoxProductExporter.php';
require_once dirname(__FILE__).'/Configuration/SteerfoxConfigurationInterface.php';
require_once dirname(__FILE__).'/Configuration/SteerfoxConfigurationService.php';
require_once dirname(__FILE__).'/Model/SteerfoxShopAdapterInterface.php';
require_once dirname(__FILE__).'/Model/SteerfoxAbstractShopService.php';
require_once dirname(__FILE__).'/Model/SteerfoxToolsAdapterInterface.php';
require_once dirname(__FILE__).'/Model/SteerfoxProductAdapterInterface.php';


/**
 * SteerfoxContainer.
 */
class SteerfoxContainer
{
    /**
     * Instance.
     *
     * @var SteerfoxContainer
     */
    protected static $instance;

    /**
     * Active services
     * @var array
     */
    protected $services;

    /**
     * SteerfoxContainer constructor.
     *
     * @param string $configFile XML configuration file.
     *
     * @throws SteerfoxException
     */
    final private function __construct($configFile)
    {
        $this->services = array();
        if (file_exists($configFile) && is_readable($configFile)) {
            $xml = simplexml_load_file($configFile);
            $this->loadServices($xml, $this->loadParameters($xml));
        } else {
            throw new SteerfoxException('XML configuration doesn\'t exist or isn\t readable');
        }
    }

    /**
     * Create instance from xml file.
     *
     * @param string $configFile XML configuration file.
     *
     * @return SteerfoxContainer
     */
    public static function createInstance($configFile)
    {
        self::$instance = new SteerfoxContainer($configFile);

        return self::$instance;
    }

    /**
     * Return created instance.
     *
     * @return SteerfoxContainer
     *
     * @throws SteerfoxException
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            throw new SteerfoxException('SteerfoxContainer was not instanciated');
        }

        return self::$instance;
    }

    /**
     * Return a service.
     *
     * @param string $serviceName The service name.
     *
     * @return mixed
     */
    public function get($serviceName)
    {
        if (array_key_exists($serviceName, $this->services)) {
            return $this->services[$serviceName];
        } else {
            throw new SteerfoxException('Invalid service name : '.$serviceName);
        }
    }

    /**
     * Load parameters from xml.
     *
     * @param SimpleXMLElement $xml xml
     *
     * @return array
     */
    protected function loadParameters($xml)
    {
        $xmlParameters = $xml->xpath('/container/parameters/parameter');
        $parameters = array();
        if (is_array($xmlParameters)) {
            foreach ($xmlParameters as $parameter) {
                $parameters[(string)$parameter->attributes()->{'id'}] = (string)$parameter;
                if (file_exists(dirname(__FILE__).'/../libadapters/'.$parameter.'.php')) {
                    /** @noinspection PhpIncludeInspection */
                    require_once dirname(__FILE__).'/../libadapters/'.$parameter.'.php';
                }
            }
        }

        return $parameters;
    }

    /**
     * Load services from xml.
     *
     * @param SimpleXMLElement $xml        xml.
     * @param array            $parameters Services parameters.
     *
     * @throws SteerfoxException
     */
    protected function loadServices($xml, $parameters)
    {
        $services = $xml->xpath('/container/services/service');
        if (is_array($services)) {
            /** @var SimpleXMLElement $service */
            foreach ($services as $service) {
                $class = (string)$service->attributes()->{'class'};
                $isParameter = preg_match('/%(.*)%/', $class, $matches);
                if ($isParameter) {
                    if (array_key_exists($matches[1], $parameters)) {
                        $class = $parameters[$matches[1]];
                    } else {
                        throw new SteerfoxException('Invalid service class : '.$class);
                    }
                }
                if (file_exists(dirname(__FILE__).'/../libadapters/'.$class.'.php')) {
                    /** @noinspection PhpIncludeInspection */
                    require_once dirname(__FILE__).'/../libadapters/'.$class.'.php';
                }
                $arguments = array();
                foreach ($service->children() as $child) {
                    $isParameter = preg_match('/%(.*)%/', (string)$child, $matches);
                    if ($isParameter) {
                        if (array_key_exists($matches[1], $parameters)) {
                            $arguments[] = $parameters[$matches[1]];
                        } else {
                            throw new SteerfoxException('Invalid argment : '.$child);
                        }
                    }
                }
                $reflexion = new ReflectionClass($class);
                if (count($arguments) > 0) {
                    $this->services[(string)$service->attributes()->{'id'}]
                        = $reflexion->newInstanceArgs($arguments);
                } else {
                    $this->services[(string)$service->attributes()->{'id'}]
                        = $reflexion->newInstance();
                }
            }
        }
    }
}
