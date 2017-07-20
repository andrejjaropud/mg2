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

namespace Lof\HidePrice\Model;

class Message extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @param \Magento\Framework\Model\Context                             $context            
     * @param \Magento\Framework\Registry                                  $registry           
     * @param \Lof\HidePrice\Model\ResourceModel\Message|null            $resource           
     * @param \Lof\HidePrice\Model\ResourceModel\Message\Collection|null $resourceCollection 
     * @param array                                                        $data               
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\HidePrice\Model\ResourceModel\Message $resource = null,
        \Lof\HidePrice\Model\ResourceModel\Message\Collection $resourceCollection = null,
        array $data = []
        ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Lof\HidePrice\Model\ResourceModel\Message');
    }
}