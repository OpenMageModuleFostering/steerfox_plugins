<?php

class Steerfox_Plugins_Model_Observer
{

    /**
     * Update a single Steerfox product.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterCatalogProductSave(Varien_Event_Observer $observer)
    {
        $this->updateSteerfoxProduct($observer->getProduct(), $observer);
    }

    /**
     * Delete a single Steerfox product.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterCatalogProductDelete(Varien_Event_Observer $observer)
    {
        $this->deleteSteerfoxProduct($observer->getEvent()->getProduct()->getId());
    }

    /**
     * Update all the Steerfox products for a mass update.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterProductMassUpdate(Varien_Event_Observer $observer)
    {
        $productIds = $observer->getData('product_ids');
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $productIds));
        foreach ($products as $product) {
            $loadedProduct = Mage::getModel('catalog/product')->load($product->getId());
            $this->updateSteerfoxProduct($loadedProduct, $observer);
        }
    }

    /**
     * Create Steerfox products after import.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterCatalogProductImport(Varien_Event_Observer $observer)
    {
        $products = $observer->getEvent()->getAdapter()->getNewSku();
        foreach ($products as $productData) {
            $product = Mage::getModel('catalog/product')->load($productData['entity_id']);
            $this->updateSteerfoxProduct($product, $observer);
        }
    }

    public function setTagsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('steerfox_fb_order_tags');
        if ($block) {
            $block->setOrders($collection);
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('steerfox_bing_order_tags');
        if ($block) {
            $block->setOrders($collection);
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock(
            'steerfox_adwords_order_tags'
        );
        if ($block) {
            $block->setOrders($collection);
        }
    }

    public function sectionUpdate(Varien_Event_Observer $observer)
    {
        Mage::helper('steerfox_plugins/core_data')->apiUpdate();
    }

    private function updateSteerfoxProduct($product, Varien_Event_Observer $observer)
    {
        // Remove product by default
        $steerfoxProductCollection = Mage::getModel('steerfox_plugins/product')->getCollection();
        $steerfoxProductCollection
            ->addFilter('id_product', $product->getId());

        $archivedStatus = array();

        if ($steerfoxProductCollection->count() > 0) {
            foreach ($steerfoxProductCollection as $steerfoxroduct) {
                $archivedStatus[$steerfoxroduct->getData('id_product') .'_' . $steerfoxroduct->getData('id_shop')] = $steerfoxroduct->getData('active');
                $steerfoxroduct->delete();
            }
        }

        // Add the product for on a good websites
        if (Mage_Catalog_Model_Product_Status::STATUS_ENABLED == $product->getData('status')) {
            if (Mage_Catalog_Model_Product_Status::STATUS_ENABLED == $product->getData('status')) {
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $active = 1;
                    if(array_key_exists($product->getId() . '_' . $websiteId, $archivedStatus)){
                        $active = $archivedStatus[$product->getId() . '_' . $websiteId];
                    }

                    Mage::getModel('steerfox_plugins/product')
                    ->setData(
                        array(
                            'id_product' => $product->getId(),
                            'id_shop' => $websiteId,
                            'active' => $active,
                        )
                    )
                    ->setOrigData()
                    ->save();
                }
            }
        }
    }

    /**
     * Delete a Steerfox product for all shop or for just one shop if precised.
     *
     * @param $productId
     * @param $shopId
     */
    private function deleteSteerfoxProduct($productId, $shopId = null)
    {
        // Remove product by default
        $steerfoxProductCollection = Mage::getModel('steerfox_plugins/product')->getCollection();
        $steerfoxProductCollection
            ->addFilter('id_product', $productId);
        if (null !== $shopId) {
            $steerfoxProductCollection->addFilter('id_shop', $shopId);
        }

        if ($steerfoxProductCollection->count() > 0) {
            foreach ($steerfoxProductCollection as $steerfoxProduct) {
                $steerfoxProduct->delete();
            }
        }
    }

    /**
     * Check the current plugin version with latest release.
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkPluginVersion(Varien_Event_Observer $observer){
        $version = null;
        $releaseInfoUrl = Mage::getConfig()->getNode('default/steerfox_plugins/latest_release_info');

        // Reduce timeout to get release info
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 5,
            )
        ));
        $releaseInfoJson = @file_get_contents($releaseInfoUrl, false, $ctx);

        if(false != $releaseInfoJson){
            // Version find
            $releaseInfoData = json_decode(trim($releaseInfoJson));

            if (false != $releaseInfoData){
                $version = $releaseInfoData->version;
            }
        }

        Mage::getSingleton('core/session')->setData('steerfox_plugins_latest_version', $version);

        // Add the version notity if necessary
        Mage::helper('steerfox_plugins')->notifyOldRelease();
    }

    /**
     * Save the add to cart action in cookies for tags
     *
     * @param Varien_Event_Observer $observer
     */
    public function notifyAddToCart(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $categories = $product->getCategoryIds();
        $catId = null;
        if (0 < count($categories)) {
            $catId = $categories[0];
        }
        $steerfoxProduct = Mage::getModel('steerfox_plugins/product')->getCollection()
            ->addFieldToFilter('id_product', $product->getId())
            ->getFirstItem();
        $productAdapter =  new Steerfox_Plugins_Model_Adapter_Product($steerfoxProduct);
        $sku = $productAdapter->getMpn();
        if ('' === $sku) {
            $sku = $product->getSku();
        }
        $productArray = array(
            'id' => $product->getId(),
            'sku' => $sku,
            'name' => $product->getName(),
            'cat' => $catId,
            'price' => $product->getPrice(),
            'margin' => $product->getProductProfit(),
            'qty' => $product->getQty(),
        );
        Mage::getSingleton('core/session')->setData(Steerfox_Plugins_Enum_Tags::FOXTAG_ADD_TO_CART, $productArray);
        Mage::getSingleton('core/session')->setData(Steerfox_Plugins_Enum_Tags::FACEBOOK_ADD_TO_CART, $productArray);
    }

    /**
     * Delete Steerfox products from a deleted website.
     *
     * @param Varien_Event_Observer $observer
     */
    public function websiteRemove(Varien_Event_Observer $observer)
    {
        /* @var $website Mage_Core_Model_Website */
        $productCollection = Mage::getModel('steerfox_plugins/product')
            ->getCollection()
            ->addFieldToFilter(
                'id_shop',
                array('eq' => $observer->getEvent()->getWebsite()->getId())
            );
        foreach($productCollection as $product){
            $product->delete();
        }
    }
}
