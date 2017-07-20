<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.52
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


require __DIR__ . '/product_image.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()
    ->query('DELETE FROM ' . $installer->getTableName('catalog_product_entity'));
$installer->getConnection()
    ->query('ALTER TABLE ' . $installer->getTableName('catalog_product_entity') . ' AUTO_INCREMENT = 1;');

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Name')
    ->setSku('simple-sku')
    ->setUrlKey('simple-url-key')
    ->setDescription('simple description')
    ->setMetaTitle('simple title')
    ->setMetaKeyword('simple keyword')
    ->setMetaDescription('simple description')
    ->setCategoryIds([4, 5, 6])
    ->setPrice(12.43)
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Model\Group::CUST_GROUP_ALL,
                'price_qty'  => 2,
                'price'      => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Model\Group::CUST_GROUP_ALL,
                'price_qty'  => 5,
                'price'      => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID,
                'price_qty'  => 3,
                'price'      => 5,
            ],
        ]
    )
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty'                     => 65,
            'is_qty_decimal'          => 0,
            'is_in_stock'             => 1,
        ]
    )
    ->setCanSaveCustomOptions(true)
    ->setProductOptions(
        [
            [
                'id'             => 1,
                'option_id'      => 0,
                'previous_group' => 'text',
                'title'          => 'Test Field',
                'type'           => 'field',
                'is_require'     => 1,
                'sort_order'     => 0,
                'price'          => 1,
                'price_type'     => 'fixed',
                'sku'            => '1-text',
                'max_characters' => 100,
            ],
            [
                'id'             => 2,
                'option_id'      => 0,
                'previous_group' => 'date',
                'title'          => 'Test Date and Time',
                'type'           => 'date_time',
                'is_require'     => 1,
                'sort_order'     => 0,
                'price'          => 2,
                'price_type'     => 'fixed',
                'sku'            => '2-date',
            ],
            [
                'id'             => 3,
                'option_id'      => 0,
                'previous_group' => 'select',
                'title'          => 'Test Select',
                'type'           => 'drop_down',
                'is_require'     => 1,
                'sort_order'     => 0,
                'values'         => [
                    [
                        'option_type_id' => -1,
                        'title'          => 'Option 1',
                        'price'          => 3,
                        'price_type'     => 'fixed',
                        'sku'            => '3-1-select',
                    ],
                    [
                        'option_type_id' => -1,
                        'title'          => 'Option 2',
                        'price'          => 3,
                        'price_type'     => 'fixed',
                        'sku'            => '3-2-select',
                    ],
                ]
            ],
            [
                'id'             => 4,
                'option_id'      => 0,
                'previous_group' => 'select',
                'title'          => 'Test Radio',
                'type'           => 'radio',
                'is_require'     => 1,
                'sort_order'     => 0,
                'values'         => [
                    [
                        'option_type_id' => -1,
                        'title'          => 'Option 1',
                        'price'          => 3,
                        'price_type'     => 'fixed',
                        'sku'            => '4-1-radio',
                    ],
                    [
                        'option_type_id' => -1,
                        'title'          => 'Option 2',
                        'price'          => 3,
                        'price_type'     => 'fixed',
                        'sku'            => '4-2-radio',
                    ],
                ]
            ],
        ]
    )
    ->setHasOptions(true)
    ->setImage('/m/a/image.jpg')
    ->setSmallImage('/m/a/small.jpg')
    ->setThumbnail('/m/a/thumbnail.jpg')
    ->setData('media_gallery', ['images' => [
        [
            'file'       => '/m/a/gallery1.jpg',
            'position'   => 1,
            'label'      => 'Image Alt Text',
            'disabled'   => 0,
            'media_type' => 'image'
        ],
        [
            'file'       => '/m/a/gallery2.jpg',
            'position'   => 2,
            'label'      => 'Image Alt Text',
            'disabled'   => 0,
            'media_type' => 'image'
        ],
        [
            'file'       => '/m/a/gallery3.jpg',
            'position'   => 3,
            'label'      => 'Image Alt Text',
            'disabled'   => 0,
            'media_type' => 'image'
        ],
    ]])
    ->save();


$simpleProducts[] = $objectManager->create('Magento\Catalog\Model\Product')
    ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(2)
    ->setAttributeSetId(4) #Bottom
    ->setWebsiteIds([1])
    ->setName('Simple Product 1')
    ->setSku('simple-1')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setCountryOfManufacture(1)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();

$simpleProducts[] = $objectManager->create('Magento\Catalog\Model\Product')
    ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product 2')
    ->setSku('simple-2')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
    ->setId(4)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Bundle Product')
    ->setSku('bundle-product')
    ->setDescription('Description with <b>html tag</b>')
    ->setShortDescription('Bundle')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'use_config_enable_qty_increments' => 1,
            'use_config_qty_increments' => 1,
            'is_in_stock' => 0,
        ]
    )
    ->setBundleOptionsData(
        [
            [
                'title' => 'Bundle Product Items',
                'default_title' => 'Bundle Product Items',
                'type' => 'checkbox',
                'required' => 1,
                'delete' => '',
                'position' => 0,
                'option_id' => '',
            ],
        ]
    )
    ->setBundleSelectionsData(
        [
            [
                [
                    'product_id' => $simpleProducts[0]->getId(),
                    'selection_qty' => 1,
                    'selection_can_change_qty' => 1,
                    'delete' => '',
                    'position' => 0,
                    'selection_price_type' => 0,
                    'selection_price_value' => 0.0,
                    'option_id' => '',
                    'selection_id' => '',
                    'is_default' => 1,
                ],
                [
                    'product_id' => $simpleProducts[1]->getId(),
                    'selection_qty' => 1,
                    'selection_can_change_qty' => 1,
                    'delete' => '',
                    'position' => 0,
                    'selection_price_type' => 0,
                    'selection_price_value' => 0.0,
                    'option_id' => '',
                    'selection_id' => '',
                    'is_default' => 1,
                ]
            ],
        ]
    )
    ->setCanSaveBundleSelections(true)
    ->setAffectBundleProductSelections(true)
    ->save();
