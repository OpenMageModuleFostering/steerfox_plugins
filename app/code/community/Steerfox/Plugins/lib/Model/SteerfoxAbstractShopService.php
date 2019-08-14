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
 * Shop service
 */
abstract class SteerfoxAbstractShopService
{
    protected $shopAdapterClass;

    /**
     * AbstractShopService constructor.
     *
     * @param string $shopAdapterClass Shop adapter class name.
     *
     * @throws SteerfoxException
     */
    public function __construct($shopAdapterClass)
    {
        $reflexion = new ReflectionClass($shopAdapterClass);
        if ($reflexion->isSubclassOf('SteerfoxShopAdapterInterface')) {
            $this->shopAdapterClass = $shopAdapterClass;
        } else {
            throw new SteerfoxException('Adapter class is not valid');
        }
    }

    /**
     * Return all shops.
     *
     * @return array
     */
    abstract public function getShops();

    /**
     * Return main shop.
     *
     * @return SteerfoxShopAdapterInterface
     */
    abstract public function getMainShop();

    /**
     * Return current shop.
     *
     * @return SteerfoxShopAdapterInterface
     */
    abstract public function getCurrentShop();


    /**
     * Return current shop.
     *
     * @param SteerfoxShopAdapterInterface $shop
     *
     * @return SteerfoxProductAdapterInterface[]
     */
    abstract public function getProductsToExport(SteerfoxShopAdapterInterface $shop);
}