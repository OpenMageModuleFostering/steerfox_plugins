<?php

require_once Mage::getModuleDir('', 'Steerfox_Plugins') . '/lib/SteerfoxContainer.php';

class Steerfox_Plugins_Model_Adapter_Configuration implements SteerfoxConfigurationInterface
{

    protected static $nameMapping = array(
        'STEERFOX_ACCOUNT_API_KEY' => array(
            'key' => 'steerfox_plugins/account/api_key',
            'scope' => 'default',
        ),
        'STEERFOX_ACCOUNT_STATUS' => array(
            'key' => 'steerfox_plugins/account/status',
            'scope' => 'default',
        ),
        'STEERFOX_SHOP_ID' => array(
            'key' => 'steerfox_plugins/catalog/shop_id',
            'scope' => 'websites',
        ),
        'STEERFOX_EXPORT_LANG' => array(
            'key' => 'steerfox_plugins/catalog/export_lang',
            'scope' => 'websites',
        ),
        'STEERFOX_EXPORT_CURRENCY' => array(
            'key' => 'steerfox_plugins/catalog/export_currency',
            'scope' => 'websites',
        ),
        'STEERFOX_FEED_ID' => array(
            'key' => 'steerfox_plugins/catalog/feed_id',
            'scope' => 'websites',
        ),
    );

    const STEERFOX_SOURCE_MAGENTO = 3;

    /**
     * @inheritDoc
     */
    public function get($key, $shopId = null)
    {
        $key = $this->getMappedKey($key);
        return Mage::getStoreConfig($key, $shopId);
    }

    /**
     * @inheritDoc
     */
    public function getGlobal($key)
    {
        $key = $this->getMappedKey($key);
        return Mage::getStoreConfig($key);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $shopId = 0)
    {
        $key = $this->getMappedKey($key);
        $scope = $this->getMappedScope($key);
        return Mage::helper('steerfox_plugins')->updateConfig($key, $value, $scope, $shopId);
    }

    /**
     * @inheritDoc
     */
    public function setGlobal($key, $value)
    {
        $key = $this->getMappedKey($key);
        return Mage::helper('steerfox_plugins')->updateConfig($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCmsId()
    {
        return self::STEERFOX_SOURCE_MAGENTO;
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        $user = Mage::getSingleton('admin/session');
        $userEmail = $user->getUser()->getEmail();
        if (empty($userEmail)) {
            $userEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        }

        if (getenv('STEERFOX_API_DEV') &&
            getenv('STEERFOX_API_USER') &&
            getenv('STEERFOX_API_PWD')
        ) {
            $userEmail = uniqid() . '@test.com';
        }

        return $userEmail;
    }

    /**
     * Resolve the good key config if parameters keys is in mapping config.
     *
     * @param string $key
     *
     * @return string
     */
    private function getMappedKey($key)
    {

        if (array_key_exists($key, self::$nameMapping)) {
            $key = self::$nameMapping[$key]['key'];
        }

        return $key;
    }

    /**
     * Resolve the good scope config if parameters keys is in mapping config.
     *
     * @param string $key
     *
     * @return string
     */
    private function getMappedScope($key)
    {
        $scope = 'default';

        if (array_key_exists($key, self::$nameMapping)) {
            $scope = self::$nameMapping[$key]['scope'];
        }

        return $scope;
    }

    /**
     * Assert if scope is website, shopid cannot be empty.
     *
     * @param string $scope
     * @param integer $shopId
     * @throws Mage_Core_Exception
     */
    private function assertScope($scope, $shopId)
    {
        if ('websites' == $scope && (null == $shopId || 0 == $shopId)) {
            Mage::throwException('shopId cannot be empty if scope is "websites".');
        }
    }

}
