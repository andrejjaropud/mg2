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

class Login  extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
 
     /**
     *
     * @var int
     */
    private $_username = - 1;

    
    protected $_registry;
    /**
     *
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
         $this->_registry = $registry;
         $this->_customerSession = $customerSession;
          $this->urlBuilder = $context->getUrlInterface();
        parent::__construct($context, $data);
        $this->setTemplate('Lof_HidePrice::widget/login.phtml');
    }

    public function getCurrentProduct()
    {       
        return $this->_registry->registry('current_product');
    }  
     /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername() {
        if (- 1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    } 
    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl() {
        /**
         * Get forgot password url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/forgotpassword' );
    }
    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled() {
        return ( bool ) ! $this->_scopeConfig->getValue ( \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
    /**
     * Get form action url
     */
    public function getFormActionUrl() {
        return $this->getUrl('lofhideprice/widget/login');
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