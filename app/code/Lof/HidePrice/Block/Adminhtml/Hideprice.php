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

namespace Lof\HidePrice\Block\Adminhtml;

class Hideprice extends \Magento\Backend\Block\Widget\Container
{


    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\HidePrice\Model\Email\TypeFactory $typeFactory
     * @param \Magento\HidePrice\Model\EmailFactory $emailFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context, 
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

   

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
