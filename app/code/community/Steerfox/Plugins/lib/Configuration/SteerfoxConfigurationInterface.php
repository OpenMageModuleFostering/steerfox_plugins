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
 * CMS Configuration interface.
 */
interface SteerfoxConfigurationInterface
{
    /**
     * Return configuration value for specific shop. If shop id is null, set for current shop.
     *
     * @param string   $key    Configuration key.
     * @param int|null $shopId Shop id.
     *
     * @return mixed
     */
    public function get($key, $shopId = null);

    /**
     * Return a global configuration value.
     *
     * @param string $key Configuration key.
     *
     * @return mixed
     */
    public function getGlobal($key);

    /**
     * Set a configuration value.
     *
     * @param string   $key    Configuration key.
     * @param mixed    $value  Configuration value.
     * @param int|null $shopId Shop id.
     *
     * @return mixed
     */
    public function set($key, $value, $shopId = null);

    /**
     * Set a global configuration value.
     *
     * @param string $key   Configuration key.
     * @param mixed  $value Configuration value.
     *
     * @return mixed
     */
    public function setGlobal($key, $value);

    /**
     * Return CMS ID for API.
     *
     * @return int
     */
    public function getCmsId();

    /**
     * Return email. If connected user has email, this one, neither, global config email.
     *
     * @return string
     */
    public function getEmail();
}
