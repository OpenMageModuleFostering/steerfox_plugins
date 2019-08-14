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

//Add Steerfox Librarie
require_once dirname(__FILE__).'/../SteerfoxContainer.php';

SteerfoxContainer::createInstance('conf.xml');

$container = SteerfoxContainer::getInstance();
/** @var SteerfoxConfigurationInterface $config */
$config = $container->get('config');
/** @var SteerfoxAbstractShopService $shopService */
$shopService = $container->get('shop');
/** @var SteerfoxAbstractLog $logger */
$logger = $container->get('logger');
/** @var SteerfoxToolsAdapterInterface $tool */
$tool = $container->get('tools');
