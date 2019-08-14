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
 * Product adapter interface.
 */
interface SteerfoxProductAdapterInterface
{
    /**
     * Return id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return Title
     *
     * @return mixed
     */
    public function getTitle();

    /**
     * Return Description
     *
     * @return mixed
     */
    public function getDescription();

    /**
     * Return ProductType
     *
     * @return mixed
     */
    public function getProductType();

    /**
     * Return Link
     *
     * @return mixed
     */
    public function getLink();

    /**
     * Return Image Link
     *
     * @return mixed
     */
    public function getImageLink();

    /**
     * Return Condition
     *
     * @return mixed
     */
    public function getCondition();

    /**
     * Return Availability
     *
     * @return mixed
     */
    public function getAvailability();

    /**
     * Return Availability Date
     *
     * @return mixed
     */
    public function getAvailabilityDate();

    /**
     * Return Price
     *
     * @return mixed
     */
    public function getPrice();

    /**
     * Return Sale Price
     *
     * @return mixed
     */
    public function getSalePrice();

    /**
     * Return Sale Price Effective Date
     *
     * @return mixed
     */
    public function getSalePriceEffectiveDate();

    /**
     * Return Gtin
     *
     * @return mixed
     */
    public function getGtin();

    /**
     * Return Brand
     *
     * @return mixed
     */
    public function getBrand();

    /**
     * Return Mpn
     *
     * @return mixed
     */
    public function getMpn();

    /**
     * Return Identifier Exists
     *
     * @return mixed
     */
    public function getIdentifierExists();

    /**
     * Return Item Group Id
     *
     * @return mixed
     */
    public function getItemGroupId();

    /**
     * Return Gender
     *
     * @return mixed
     */
    public function getGender();

    /**
     * Return Age Group
     *
     * @return mixed
     */
    public function getAgeGroup();

    /**
     * Return Color
     *
     * @return mixed
     */
    public function getColor();

    /**
     * Return Size
     *
     * @return mixed
     */
    public function getSize();

    /**
     * Return Shipping
     *
     * @return mixed
     */
    public function getShipping();

    /**
     * Return Shipping Weight
     *
     * @return mixed
     */
    public function getShippingWeight();

    /**
     * Return GrossProfit
     *
     * @return mixed
     */
    public function getGrossProfit();

    /**
     * Return Gross Margin
     *
     * @return mixed
     */
    public function getGrossMargin();
}