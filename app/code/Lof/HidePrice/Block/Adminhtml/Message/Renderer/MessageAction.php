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
namespace Lof\HidePrice\Block\Adminhtml\Message\Renderer;
use Magento\Framework\UrlInterface;

class MessageAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	/**
	 * @var Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @param \Magento\Backend\Block\Context
	 * @param \Magento\Framework\Url
	 */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Url $url
    ) {
		$this->_urlBuilder   = $url;
        parent::__construct($context);
	}

	public function _getValue(\Magento\Framework\DataObject $row){
		$editUrl = $this->_urlBuilder->getUrl(
                                'lofhideprice/message/edit',
                                [
                                    'message_id' => $row['message_id']
                                ]
                            );
		return sprintf("<a target='_blank' href='%s'>Edit</a>", $editUrl);
	}

	
}