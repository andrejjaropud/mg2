<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_HidePrice
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\HidePrice\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('lof_hideprice_hideprice'));
        $table = $installer->getConnection()->newTable(
        $installer->getTable('lof_hideprice_hideprice')
        )->addColumn(
        'hideprice_id',
        Table::TYPE_SMALLINT,
        null,
        [
         'identity' => true,
         'nullable' => false,
         'primary'  => true,
        ],
        'Hide Price Id'
        )->addColumn(
        'callforprice_text',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Call/Hide Price Text'
        )->addColumn(
        'callforprice_customergroup',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Call/Hide Price For Customer Group'
        )
        ->addColumn(
        'is_active',
        Table::TYPE_SMALLINT,
        1,
        ['nullable' => false, 'default' => '1'],
        'Active'
        )
        ->addColumn(
        'inquiry_form',
        Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Inquiry form'
        )
        ->addColumn(
        'content',
        Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Content'
        )
        ->addColumn(
        'store_id',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Store'
        )
        ->addColumn(
        'actions_serialized',
        Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Actions'
        )
        ->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Form Creation Time'
        )
        ->setComment(
        'Hide Price Table'
        );
        $installer->getConnection()->createTable($table);


        $setup->getConnection()->dropTable($setup->getTable('lof_hideprice_product'));
        $table = $installer->getConnection()->newTable(
        $installer->getTable('lof_hideprice_product')
        )->addColumn(
        'hideprice_id',
        Table::TYPE_SMALLINT,
        null,
        ['nullable' => false, 'primary' => true],
        'Hide Price Id'
        )->addColumn(
        'entity_id',
        Table::TYPE_INTEGER,
        null,
        ['nullable' => false, 'primary' => true],
        'Entity id'
        )->addColumn(
        'position',
        Table::TYPE_SMALLINT,
        null,
        ['nullable' => false],
        'Call/Hide Price Text'
        )->setComment(
        'Hide Product Table'
        )->addIndex(
        $installer->getIdxName('lof_hideprice_product', ['hideprice_id']),
        ['hideprice_id']
        );
        $installer->getConnection()->createTable($table);

        $setup->getConnection()->dropTable($setup->getTable('lof_hideprice_message'));
        $table = $installer->getConnection()->newTable(
        $installer->getTable('lof_hideprice_message')
        )->addColumn(
        'message_id',
        Table::TYPE_SMALLINT,
        null,
        ['identity' => true,'nullable' => false, 'primary' => true],
        'Hide Price Id'
        )->addColumn(
        'entity_id',
        Table::TYPE_INTEGER,
        null,
        ['nullable' => false, 'primary' => true],
        'Entity id'
        )->addColumn(
        'hideprice_id',
        Table::TYPE_INTEGER,
        null,
        ['nullable' => false, 'primary' => true],
        'Hide Price id'
        )->addColumn(
        'name',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Name'
        )->addColumn(
        'email',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Email'
        )->addColumn(
        'phone',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Phone'
        )->addColumn(
        'subject',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Subject'
        )->setComment(
        'Meassage Table'
        )
        ->addColumn(
        'content',
        Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Content'
        )
        ->addColumn(
        'product_image',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Product Image'
        )
        ->addColumn(
        'product_url',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Product Url'
        )
        ->addColumn(
        'reply',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false,'default' => 'No'],
        'Product Image'
        )
        ->addColumn(
        'reply_subject',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Subject'
        )
        ->addColumn(
        'reply_content',
        Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Content'
        )
        ->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Form Creation Time'
        )
        ->addIndex(
        $installer->getIdxName('lof_hideprice_product', ['hideprice_id']),
        ['hideprice_id']
        );
        $installer->getConnection()->createTable($table);

        /*
            * Create table TABLE_GALLERY_ALBUM_POST
         */
        $setup->getConnection()->dropTable($setup->getTable('lof_hideprice_hideprice_message'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_hideprice_hideprice_message')
        )->addColumn(
            'hideprice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
             'nullable' => false,
             'primary'  => true,
            ],
            'Hide Price ID'
        )->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
             'nullable' => false,
             'primary'  => true,
            ],
            'Message Id'
        )
        ->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Form Creation Time'
        )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => false],
            'Position'
        )->addForeignKey(
            $installer->getFkName('lof_hideprice_hideprice_message', 'hideprice_id', 'lof_hideprice_hideprice', 'hideprice_id'),
            'hideprice_id',
            $installer->getTable('lof_hideprice_hideprice'),
            'hideprice_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lof_hideprice_hideprice_message', 'message_id', 'lof_formbuilder_message', 'message_id'),
            'message_id',
            $installer->getTable('lof_formbuilder_message'),
            'message_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Hide Price Messages Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}