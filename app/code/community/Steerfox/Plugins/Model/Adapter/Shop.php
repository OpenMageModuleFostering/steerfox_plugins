<?php

require_once Mage::getModuleDir('', 'Steerfox_Plugins') . '/lib/SteerfoxContainer.php';

class Steerfox_Plugins_Model_Adapter_Shop implements SteerfoxShopAdapterInterface
{
    /**
     * Store Magento.
     *
     * @var Mage_Core_Model_Website
     */
    private $shop;

    /**
     * return feed Url.
     *
     * @return string
     */
    public function getShopFeedUrl()
    {
        $hash = Mage::getStoreConfig('steerfox_plugins/catalog/hash_ws');
        return Mage::getUrl('steerfox/product/export') . '/secure/' . $hash . '?id_shop=' . $this->getId();
    }

    public function __construct(Mage_Core_Model_Website $store)
    {
        $this->shop = $store;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->shop->getId();
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        $url = $this->shop->getDefaultStore()->getBaseUrl();

        if (getenv('STEERFOX_API_DEV') &&
            getenv('STEERFOX_API_USER') &&
            getenv('STEERFOX_API_PWD')
        ) {
            $url = 'http://test.' . uniqid() . '.com/';
        }

        return $url;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->shop->getName();
    }

    /**
     * @inheritDoc
     */
    public function getLanguage()
    {
        return Mage::getStoreConfig('general/locale/code', $this->shop->getDefaultStore());
    }

    /**
     * @inheritDoc
     */
    public function getLogo()
    {
        // Emulate front store env to get logo
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->shop->getDefaultStore()->getId());
        $logoUrl = Mage::getDesign()->getSkinUrl() . Mage::getStoreConfig('design/header/logo_src', $this->shop->getDefaultStore());
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $logoUrl;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency()
    {
        return $this->shop->getDefaultStore()->getCurrentCurrencyCode();
    }

    /**
     * @inheritDoc
     */
    public function getLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }
}
