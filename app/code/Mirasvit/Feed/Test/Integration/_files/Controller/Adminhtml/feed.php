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


$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()
    ->query('DELETE FROM ' . $installer->getTableName('mst_feed_feed'));
$installer->getConnection()
    ->query('ALTER TABLE ' . $installer->getTableName('mst_feed_feed') . ' AUTO_INCREMENT = 1;');

/** @var $feed \Mirasvit\Feed\Model\Feed */
$feed = $objectManager->create('Mirasvit\Feed\Model\Feed');
$feed->setName('Sample XML Feed')
    ->setType('xml')
    ->setFilename('feed_xml')
    ->setFtpHost('example.com')
    ->setFtpProtocol('ftp')
    ->save();