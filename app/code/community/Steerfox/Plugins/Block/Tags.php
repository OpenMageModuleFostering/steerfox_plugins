<?php

class Steerfox_Plugins_Block_Tags extends Mage_Core_Block_Template
{

    public function getGoogleAdwordsId()
    {
        return $this->getConfig('tags/adwords_id');
    }

    public function getGoogleAdwordsLabel()
    {
        return $this->getConfig('tags/adwords_label');
    }

    public function getBingId()
    {
        return $this->getConfig('tags/bing_id');
    }

    public function getFacebookId()
    {
        return $this->getConfig('tags/facebook_id');
    }

    public function getGtmId()
    {
        return $this->getConfig('tags/gtm_id');
    }

    /**
     * Return the foxtag id store in conf.
     *
     * @return String
     */
    public function getFoxtagId()
    {
        return (String)$this->getConfig('tags/foxtag_id');
    }

    public function getGsv()
    {
        return $this->getConfig('google/google_site_verification');
    }

    /**
     * Return the conversion amount of the transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getConvertionAmount($order)
    {
        $withShipping = (boolean)$this->getConfig('tags/shipping');

        return  number_format(round($order->getGrandTotal() - ($withShipping ? 0 : $order->getShippingAmount()) - $order->getTaxAmount(), 2), 2, '.', '');
    }

    /**
     * Return the conversion subtotal of the transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getConvertionSubtotal($order)
    {
        return $order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount();
    }

    public function getConvertionMargin($order)
    {
        $margin = 0;

        if ($order instanceof Mage_Sales_Model_Order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $margin += ($product->getProductProfit() * $item->getQtyOrdered());
            }
        }

        return $margin;
    }

    /**
     * Return the conversion taxes of the transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getConvertionTax($order)
    {
        return $order->getTaxAmount();
    }

    /**
     * Return the conversion amount of the transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getConvertionShipping($order)
    {
        return $order->getShippingAmount();
    }

    /**
     * Return the conversion discount of the transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getConvertionDiscount($order)
    {
        return $order->getDiscountAmount();
    }

    /**
     * Return the currency code of the current store
     *
     * @return String
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();;
    }

    /**
     * Get the order object if we are on the checkout success page.
     *
     * @return null
     */
    public function getOrder()
    {
        // Do work only on checkout success page
        if (('success' == Mage::app()->getRequest()->getActionName() && 'checkout' == Mage::app()->getRequest()->getRouteName())) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());;
        } else {
            $order = null;
        }

        return $order;
    }

    /**
     * Return the items formatted for tags
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getItemList($order)
    {
        $itemList = array();

        if ($order instanceof Mage_Sales_Model_Order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $categories = $product->getCategoryIds();
                $catId = null;
                if (0 < count($categories)) {
                    $catId = $categories[0];
                }
                $itemList[] = array(
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'cat' => $catId,
                    'price' => $product->getPrice(),
                    'margin' => $product->getProductProfit(),
                    'qty' => $item->getQtyOrdered()
                );
            }
        }
        return $itemList;
    }

    /**
     * Return the ids of the order's products as a string separate by a comma
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getProductsIdsString($order)
    {
        $returnArray = array();

        if ($order instanceof Mage_Sales_Model_Order) {
            foreach ($order->getAllItems() as $item) {
                $returnArray[] = $item->getProductId();
            }
        }
        return implode(',', $returnArray);
    }

    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Get the url of the js file for foxtag.
     * TODO: delete this function and use the long therm URL when it's ready
     * @return string
     */
    public function getFoxtagJsUrl()
    {
        return '/skin/frontend/default/default/steerfox/foxtagTMP.js';
    }

    /**
     * Get the url of the server for foxtag.
     * TODO: delete this function and use the long therm URL when it's ready
     * @return string
     */
    public function getFoxtagUrl()
    {
        return 'http://logstash-test.docker:800/track';
    }

    /**
     * Return the foxtag page corresponding to the current page.
     *
     * @return string
     */
    public function getFoxtagPage()
    {
        $page = 'other';
        if ($this->isHomePage()) {
            $page = 'home';
        } elseif (Mage::registry('current_product') instanceof Mage_Catalog_Model_Product) {
            $page = 'product';
        } elseif (Mage::registry('current_category') instanceof Mage_Catalog_Model_Category) {
            $page = 'category';
        } elseif ($this->isSearchResultsPage()) {
            $page = 'searchresults';
        } elseif ($this->isCartPage()) {
            $page = 'cart';
        }
        return $page;
    }

    /**
     * Return the Id of the user if he is logged in.
     *
     * @return string
     */
    public function getUserId()
    {
        $userId = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $userId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        return $userId;
    }

    /**
     * Return true if current page is home page.
     *
     * @return bool
     */
    public function isHomePage()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        return 'cms' == $request->getModuleName() && 'index' == $request->getControllerName() && 'index' == $request->getActionName();
    }

    /**
     * Return true if current page is search results page.
     *
     * @return bool
     */
    public function isSearchResultsPage()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        return 'catalogsearch' == $request->getModuleName() && 'result' == $request->getControllerName() && 'index' == $request->getActionName();
    }

    /**
     * Return true if current page is cart page.
     *
     * @return bool
     */
    public function isCartPage()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        return 'checkout' == $request->getModuleName() && 'cart' == $request->getControllerName() && 'index' == $request->getActionName();
    }

    /**
     * Return the product of the page if we are on a product page.
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getCurrentProduct()
    {
        $product = null;
        $request = Mage::app()->getFrontController()->getRequest();
        if ('catalog' == $request->getModuleName() && 'product' == $request->getControllerName() && 'view' == $request->getActionName()) {
            $product = Mage::registry('current_product');
        }
        return $product;
    }

    /**
     * Return the category of the page if we are on a category page.
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = null;
        $request = Mage::app()->getFrontController()->getRequest();
        if ('catalog' == $request->getModuleName() && 'category' == $request->getControllerName() && 'view' == $request->getActionName()) {
            $category = Mage::registry('current_category');
        }
        return $category;
    }

    /**
     * Return product added to cart for foxtag if it exists.
     *
     * @return array
     */
    public function getFoxtagProductAddToCart()
    {
        $product = Mage::getSingleton('core/session')->getData(Steerfox_Plugins_Enum_Tags::FOXTAG_ADD_TO_CART);
        Mage::getSingleton('core/session')->setData(Steerfox_Plugins_Enum_Tags::FOXTAG_ADD_TO_CART, null);
        return $product;
    }

    /**
     * Return product added to cart for facebook if it exists.
     *
     * @return array
     */
    public function getFacebookProductAddToCart()
    {
        $product = Mage::getSingleton('core/session')->getData(Steerfox_Plugins_Enum_Tags::FACEBOOK_ADD_TO_CART);
        Mage::getSingleton('core/session')->setData(Steerfox_Plugins_Enum_Tags::FACEBOOK_ADD_TO_CART, null);
        return $product;
    }

    protected function getConfig($path)
    {
        return Mage::helper('steerfox_plugins')->getConfig($path);
    }
}
