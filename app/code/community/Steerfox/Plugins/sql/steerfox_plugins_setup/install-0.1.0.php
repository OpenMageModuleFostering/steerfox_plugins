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

// Open
/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$sql = array();

// steerfox_products table : list of products for flow export
$table = $installer->getConnection()
    ->newTable($installer->getTable('steerfox_plugins/product'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Entity Id'
    )
    ->addColumn(
        'id_product',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false,
            'unsigned' => true,
        ),
        'Id of shared product'
    )
    ->addColumn(
        'id_lang',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
            'unsigned' => true,
            'default' => null,
        ),
        'Share language'
    )
    ->addColumn(
        'id_shop',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false,
            'unsigned' => true,
            'default' => 1,
        ),
        'Shop id'
    )
    ->addColumn(
        'active',
        Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        null,
        array(
            'nullable' => false,
            'default' => true,
        ),
        'Set share active or not'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ),
        'Creation time'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
        ),
        'Update time'
    )
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('steerfox_plugins/product'),
            array('id_product'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('id_product'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    )
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('steerfox_plugins/product'),
            array('id_shop'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('id_shop'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    )
    ->setComment('Steerfox product publish list');

$installer->getConnection()->createTable($table);

// steerfox_logs table : module logs

$table = $installer->getConnection()
    ->newTable($installer->getTable('steerfox_plugins/log'))
    ->addColumn(
        'id_log',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Entity Id'
    )
    ->addColumn(
        'action',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'default' => true,
        ),
        'Action'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20,
        array(
            'nullable' => false,
        ),
        'Log type'
    )
    ->addColumn(
        'feed_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => true,
            'default' => null,
        ),
        'Feed'
    )
    ->addColumn(
        'shop',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'default' => null,
        ),
        'Shop'
    )
    ->addColumn(
        'locale',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        5,
        array(
            'nullable' => true,
            'default' => null,
        ),
        'Locale'
    )
    ->addColumn(
        'message',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(
            'nullable' => false,
        ),
        'Message'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ),
        'Creation time'
    )
    ->setComment('Steerfox logs');

$installer->getConnection()->createTable($table);

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->addAttribute(
    'catalog_product',
    'product_profit',
    array(
        'group' => 'Prices',
        'type' => 'decimal',
        'label' => 'Product Profit',
        'class' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'backend' => 'steerfox_plugins/entity_attribute_backend_profit',
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        // this is what determines if it will be a system attribute or not
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
    )
);

$installer->addAttribute(
    'catalog_product',
    'product_profit_ratio',
    array(
        'group' => 'Prices',
        'type' => 'decimal',
        'label' => 'Product Profit Ratio',
        'class' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'backend' => 'steerfox_plugins/entity_attribute_backend_profit_ratio',
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        // this is what determines if it will be a system attribute or not
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
    )
);

$installer->endSetup();