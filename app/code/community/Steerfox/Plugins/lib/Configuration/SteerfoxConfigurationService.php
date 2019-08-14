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
 * Configuration service.
 *
 * @method mixed get($key, $shopId)
 * @method mixed getGlobal($key)
 * @method mixed set($key, $value, $shopId)
 * @method mixed setGlobal($key, $value)
 * @method int getCmsId()
 * @method string getEmail()
 */
class SteerfoxConfigurationService
{
    /**
     * Configuration.
     *
     * @var SteerfoxConfigurationInterface
     */
    protected $configuration;

    /**
     * ConfigurationService constructor.
     *
     * @param string $configuration Configuration adapter class name.
     *
     * @throws SteerfoxException
     */
    public function __construct($configuration)
    {
        $reflexion = new ReflectionClass($configuration);
        if ($reflexion->isSubclassOf('SteerfoxConfigurationInterface')) {
            $this->configuration = $reflexion->newInstance();
        } else {
            throw new SteerfoxException('Invalid configuration adapter');
        }

    }

    /**
     * Passthru to configuration adapter.
     *
     * @param string $name      Method name.
     * @param array  $arguments Arguments.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $reflexionMethod = new ReflectionMethod($this->configuration, $name);

        return $reflexionMethod->invokeArgs($this->configuration, $arguments);
    }
}
