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
 * 
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\HidePrice\Block\Adminhtml\Hideprice\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

     /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Module\Manager        $moduleManager
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $jsonEncoder, $authSession);

    }//end __construct()
    protected function _construct()
    {
        parent::_construct(); 
        $this->setId('hideprice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));

        $this->addTab(
            'hideprice_information',
            [
            'label' => __('Hideprice Information'),
            'content' => $this->getLayout()->createBlock('Lof\HidePrice\Block\Adminhtml\Hideprice\Edit\Tab\Main')->toHtml()
            ]
        );
        $this->addTab(
            'related_products',
            [
                'label' => __('Quick Hideprice Product'),
                'url' => $this->getUrl('lofhideprice/hideprice/relatedproducts', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        $this->addTab(
                'messages',
                [
                    'label' => __('Messages'),
                    'url' => $this->getUrl('lofhideprice/hideprice/messages', ['_current' => true]),
                    'class' => 'ajax'
                ]
        );
        if ($this->_moduleManager->isEnabled('Lof_Formbuilder')) {
            $this->addTabAfter(
                'related_messages',
                [
                 'label' => __('Formbuilder Message'),
                 'url'   => $this->getUrl('lofhideprice/hideprice/relatedmessages', ['_current' => true]),
                 'class' => 'ajax relatedmessages',
                ],
                'display'
            );
        }
        
        $this->addTab(
            'conditions',
            [
            'label' => __('Conditions(Auto Hideprice)'),
            'content' => $this->getLayout()->createBlock('Lof\HidePrice\Block\Adminhtml\Hideprice\Edit\Tab\Conditions')->toHtml()
            ]
        );
    }

}
