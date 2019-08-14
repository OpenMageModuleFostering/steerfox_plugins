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
 * Class Steerfox_Plugins_Block_Adminhtml_Product_Grid
 */
class Steerfox_Plugins_Block_adminhtml_Steerfoxproduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('steerfoxproduct_list_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
                'id'=>$row->getData('id_product'))
        );
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('steerfox_plugins/product_collection');
        $collection->getSelect()
            ->joinLeft(
                array('prod' => 'catalog_product_entity'),
                'prod.entity_id = main_table.id_product',
                array('sku')
            )
            ->group('prod.entity_id')
            ->group('main_table.id_shop');
        $collection->addProductData();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');

        $this->getMassactionBlock()->addItem(
            'publish',
            array(
                'label' => $this->_getHelper()->__('Publish on Steerfox'),
                'url' => $this->getUrl('*/*/massPublish'),
            )
        );

        $this->getMassactionBlock()->addItem(
            'unpublish',
            array(
                'label' => $this->_getHelper()->__('Unpublish on Steerfox'),
                'url' => $this->getUrl('*/*/massUnpublish'),
            )
        );

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'id_lang',
            array(
                'header' => $this->_getHelper()->__('ID language'),
                'type' => 'number',
                'index' => 'id_lang',
            )
        );

        $this->addColumn('id_shop',
            array(
                'header'=> Mage::helper('catalog')->__('Shop'),
                'width' => '100px',
                'sortable'  => false,
                'index'     => 'id_shop',
                'type'      => 'options',
                'options'   => Mage::helper('steerfox_plugins')->getWebsiteOption(),
            ));

        $this->addColumn(
            'id_product',
            array(
                'header' => $this->_getHelper()->__('ID product'),
                'type' => 'number',
                'index' => 'id_product',
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => $this->_getHelper()->__('SKU product'),
                'type' => 'text',
                'index' => 'sku',
            )
        );


        $this->addColumn(
            'product_name',
            array(
                'header' => $this->_getHelper()->__('Product name'),
                'type' => 'text',
                'index' => 'name_table.value',
            )
        );

        $this->addColumn(
            'product_price',
            array(
                'header' => $this->_getHelper()->__('Product price'),
                'type' => 'number',
                'index' => 'price_table.value',
            )
        );

        $this->addColumn(
            'product_cost',
            array(
                'header' => $this->_getHelper()->__('Product cost'),
                'type' => 'number',
                'index' => 'cost_table.value',
            )
        );

        if (false != Mage::helper('steerfox_plugins')->getConfig('catalog/export_margin')) {
            $this->addColumn(
                'product_profit',
                array(
                    'header' => $this->_getHelper()->__('Product profit'),
                    'type' => 'number',
                    'index' => 'product_profit_table.value',
                )
            );

            $this->addColumn(
                'product_profit_ratio',
                array(
                    'header' => $this->_getHelper()->__('Product profit ratio'),
                    'type' => 'number',
                    'index' => 'product_profit_ratio_table.value',
                )
            );
        }

        $brandSingleton = Mage::getSingleton('steerfox_plugins/product');
        $this->addColumn(
            'active',
            array(
                'header' => $this->_getHelper()->__('Export status'),
                'type' => 'options',
                'index' => 'active',
                'options' => $brandSingleton->getAvailableStates(),
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => $this->_getHelper()->__('Created'),
                'type' => 'datetime',
                'index' => 'created_at',
            )
        );

        $this->addColumn(
            'updated_at',
            array(
                'header' => $this->_getHelper()->__('Updated'),
                'type' => 'datetime',
                'index' => 'updated_at',
            )
        );

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }


    protected function _getHelper()
    {
        return Mage::helper('steerfox_plugins');
    }
}
