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


use Magento\Framework\App\Filesystem\DirectoryList;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $mediaConfig \Magento\Catalog\Model\Product\Media\Config */
$mediaConfig = $objectManager->get('Magento\Catalog\Model\Product\Media\Config');

/** @var $mediaDirectory \Magento\Framework\Filesystem\Directory\WriteInterface */
$mediaDirectory = $objectManager->get('Magento\Framework\Filesystem')
    ->getDirectoryWrite(DirectoryList::MEDIA);
$targetDirPath = $mediaConfig->getBaseMediaPath() . str_replace('/', DIRECTORY_SEPARATOR, '/m/a/');
$targetTmpDirPath = $mediaConfig->getBaseTmpMediaPath() . str_replace('/', DIRECTORY_SEPARATOR, '/m/a/');
$mediaDirectory->create($targetDirPath);
$mediaDirectory->create($targetTmpDirPath);

$targetTmpPath = $mediaDirectory->getAbsolutePath() . DIRECTORY_SEPARATOR . $targetTmpDirPath . DIRECTORY_SEPARATOR;

$files = glob($mediaDirectory->getAbsolutePath('catalog/product/m/a') . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

copy(__DIR__ . '/images/image.jpg', $targetTmpPath . 'image.jpg');
copy(__DIR__ . '/images/small.jpg', $targetTmpPath . 'small.jpg');
copy(__DIR__ . '/images/thumbnail.jpg', $targetTmpPath . 'thumbnail.jpg');
copy(__DIR__ . '/images/gallery1.jpg', $targetTmpPath . 'gallery1.jpg');
copy(__DIR__ . '/images/gallery2.jpg', $targetTmpPath . 'gallery2.jpg');
copy(__DIR__ . '/images/gallery3.jpg', $targetTmpPath . 'gallery3.jpg');
