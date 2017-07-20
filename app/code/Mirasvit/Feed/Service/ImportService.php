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



namespace Mirasvit\Feed\Service;

use Mirasvit\Feed\Api\Service\ImportServiceInterface;
use Symfony\Component\Yaml\Parser as YamlParser;

class ImportService implements ImportServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function import($object, $filePath)
    {
        $parser = new YamlParser();
        $content = file_get_contents($filePath);
        $data = $parser->parse($content);
        $object->setData($data);

        $object
            ->setIsActive(1)
            ->save();

        return $object;
    }
}