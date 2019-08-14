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
 * Shop adapter interface.
 */
interface SteerfoxShopAdapterInterface
{
    /**
     * Return id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return URL.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Return name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return language.
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Return locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Return logo.
     *
     * @return string
     */
    public function getLogo();

    /**
     * Return currency code.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * return feed Url.
     *
     * @return string
     */
    public function getShopFeedUrl();
}
