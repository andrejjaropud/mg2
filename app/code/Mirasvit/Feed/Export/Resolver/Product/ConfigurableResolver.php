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


namespace Mirasvit\Feed\Export\Resolver\Product;

use Magento\Catalog\Model\Product;
use Mirasvit\Feed\Export\Resolver\ProductResolver;

class ConfigurableResolver extends ProductResolver
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAssociatedProducts($product)
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $type */
        $type = $product->getTypeInstance();

        return $type->getUsedProducts($product);
    }

    public function getData($object, $key)
    {
        $value = parent::getData($object, $key);

        if (!$value) {
            $value = [];
            foreach ($this->getAssociatedProducts($object) as $child) {
                $val = explode(', ', parent::getData($child, $key));
                $value = array_merge($value, $val);
            }
            $value = array_unique(array_filter($value));
        }

        return $value;
    }
}