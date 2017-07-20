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

namespace Lof\HidePrice\Block\Widget;

class HidePrice  extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_registry;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
         $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->setTemplate('widget/hideprice.phtml');
    }

    public function getCurrentProduct()
    {       
        return $this->_registry->registry('current_product');
    }   

    /**
     * Get form action url
     */
    public function getFormActionUrl() {
        return $this->getUrl('lofhideprice/widget/index');
    }

    /**
     * Get config value
     */
    public function getConfigValue($value = '') {
        return $this->_scopeConfig
        ->getValue(
            $value,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }
}