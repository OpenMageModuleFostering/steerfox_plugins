<?php
/**
 * Copyright 2015 Steerfox SAS.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @author    Steerfox <tech@steerfox.com>
 * @copyright 2015 Steerfox SAS
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Class Steerfox_Plugins_ProductController
 */
class Steerfox_Plugins_ProductController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action générant l'export steerfox les produits sélectionnés.
     */
    public function exportAction()
    {
        // we verify if the hash is correct
        $params = $this->getRequest()->getParams();
        $hash = Mage::getStoreConfig('steerfox_plugins/catalog/hash_ws');
        if (
            empty($hash)
            || !array_key_exists('secure', $params)
            || $hash !== $params['secure']
        ) {
            $this->returnError();
        }

        $shopId = $this->getRequest()->getParam('id_shop');
        // TODO keep for future use of export by store
//        if (0 !== $shopId && empty($shopId)) {
//            $shopId = Mage::getStoreConfig('steerfox_plugins/catalog/main_store_view');
//        }
//        if (!in_array($shopId, Mage::app()->getWebsite()->getGroupIds())) {
//            $this->returnError();
//        }
        // Fin TODO
        $exportFormat = $this->getRequest()->getParam('format', 'gz');

        $coreHelper = Mage::helper('steerfox_plugins/core_data');
        $steerfoxContainer = $coreHelper->getSteerfoxContainer();
        $steerfoxExport = $steerfoxContainer->get('export');

        $shop = new Steerfox_Plugins_Model_Adapter_Shop(Mage::app()->getWebsite($shopId));

        if ($this->getRequest()->getParam('file', false)) {
            $steerfoxExport->saveProductExporter($shop, $exportFormat);
        } else {
            /** @var SteerfoxProductExporter $exporter */
            $exporter = $steerfoxExport->getProductExporter($shop, $exportFormat);
            $fileContent = $exporter->getContent();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', 'application/force-download')
                ->setHeader('Content-Length', strlen($fileContent))
                ->setHeader('Content-Disposition', 'attachment' . '; filename=' . $exporter->getFileName());
            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();
            echo $fileContent;
        }
    }

    /**
     * Sends 404 header with no content.
     */
    private function returnError()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        exit;
    }
}