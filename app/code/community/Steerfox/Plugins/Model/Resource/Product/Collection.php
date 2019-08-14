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
 * Class Steerfox_Plugins_Model_Resource_Product_Collection
 */
class Steerfox_Plugins_Model_Resource_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();

        $this->_init('steerfox_plugins/product');
    }

    public function prepareForList($page)
    {
        $this->setPageSize(Mage::helper('steerfox_plugins')->getProductsPerPage());
    }

    public function addProductData()
    {
        // alias then field name
        $productAttributes = array('product_name' => 'name', 'product_cost' => 'cost', 'product_profit' => 'product_profit', 'product_profit_ratio' => 'product_profit_ratio', 'product_price' => 'price');
        foreach ($productAttributes as $alias => $attributeCode) {
            $tableAlias = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            //Add eav attribute value
            $this->getSelect()->joinLeft(
                array($tableAlias => $attribute->getBackendTable()),
                "main_table.id_product = $tableAlias.entity_id AND $tableAlias.attribute_id={$attribute->getId()}",
                array($tableAlias.'.value' => 'value')
            );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        // Count doesn't work with group by columns keep the group by
        if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }
}
