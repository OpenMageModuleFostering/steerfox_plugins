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
 * Export CMS data for Steerfox Api
 */
class SteerfoxExportService
{
    /**
     * @param SteerfoxShopAdapterInterface $shop
     * @param string                       $format
     *
     * @return SteerfoxProductExporter
     * @throws Exception
     * @throws SteerfoxException
     */
    public function getProductExporter(SteerfoxShopAdapterInterface $shop, $format = 'gz')
    {
        if (false == in_array($format, SteerfoxProductExporter::$AUTHORIZED_FORMATS)) {
            throw new Exception('Unrecognized format.');
        }

        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxAbstractShopService $shopService */
        $shopService = $container->get('shop');
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = $container->get('tools');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');


        $products = $shopService->getProductsToExport($shop);
        $productExporter = new SteerfoxProductExporter($shop->getId(), $products, $format);
        $logger->addLog(
            $tool->jsonEncode('Success'),
            'exportProduct',
            SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
        );

        return $productExporter;
    }


    /**
     * @param SteerfoxShopAdapterInterface $shop
     * @param string                       $format
     *
     * @throws Exception
     * @throws SteerfoxException
     */
    public function saveProductExporter(SteerfoxShopAdapterInterface $shop, $format = 'gz')
    {
        /** @var SteerfoxProductExporter $productExporter */
        $productExporter = $this->getProductExporter($shop, $format);

        $container = SteerfoxContainer::getInstance();
        /** @var SteerfoxToolsAdapterInterface $tool */
        $tool = $container->get('tools');
        /** @var SteerfoxAbstractLoggerService $logger */
        $logger = $container->get('logger');
        $tool->saveExportFile(
            $productExporter->getFileName(),
            $productExporter->getContent()
        );
        $logger->addLog(
            $tool->jsonEncode('Success'),
            'saveExportProduct',
            SteerfoxAbstractLoggerService::STEERFOX_LOG_TYPE_INFO
        );
    }
}
