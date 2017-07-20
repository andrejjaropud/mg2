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


namespace Lof\HidePrice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    public $hideprice;

    public $_resource;
    
    /**
     * @param \Magento\Framework\App\Helper\Context                $context         
     * @param \Magento\Cms\Model\Template\FilterProvider           $filterProvider  
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager    
     * @param \Magento\Framework\Locale\CurrencyInterface          $localeCurrency  
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate      
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager   
     * @param \Magento\Customer\Model\Session                      $customerSession 
     * @param \Magento\Checkout\Model\Session                      $checkoutSession 
     * @param \Magento\Framework\Registry                          $coreRegistry    
     * @param \Magento\Framework\Filter\FilterManager              $filterManager   
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Lof\HidePrice\Model\Hideprice $hideprice,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager   = $storeManager;
        $this->_localeDate     = $localeDate;
        $this->_localeCurrency = $localeCurrency;
        $this->_objectManager  = $objectManager;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->coreRegistry    = $coreRegistry;
        $this->filterManager   = $filterManager;
        $this->hideprice  = $hideprice;
        $this->_resource = $resource;
    }

    public function filter($str)
    {
        $str  = $this->formatCustomVariables($str);
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }
    
     /**
     * Get config value
     */
    public function getConfigValue($value = '') {
        return $this->scopeConfig
                ->getValue(
                        $value,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
    }

    /**
     * Get base url
     */
    public function getBaseUrl() {
        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        );
    }

    /**
     * Get current url
     */
    public function getCurrentUrls() {
        return $this->_urlBuilder->getCurrentUrl();
    }

    public function getIp() {

        //Just get the headers if we can or else use the SERVER global
        if ( function_exists( 'apache_request_headers' ) ) {

            $headers = apache_request_headers();

        } else {

            $headers = $_SERVER;

        }

        //Get the forwarded IP if it exists
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

            $the_ip = $headers['X-Forwarded-For'];

        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
            ) {

            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];

        } else {

            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

        }

        return $the_ip;

    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofhideprice/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

    public function getFormatDate($date, $type = 'full'){
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

    public function getSymbol(){
        $currency = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode());
        $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();

        if(!$symbol) $symbol = '';
        return $symbol;
    }

    public function getMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

    public function getFieldPrefix(){
        return 'loffield_';
    }

    public function getCurrentProduct()
    {
        if ($this->coreRegistry->registry('product')) {
            return $this->coreRegistry->registry('product');
        }
        return false;
    }

    public function getCurrentCategory()
    {
        if ($this->coreRegistry->registry('current_category')) {
            return $this->coreRegistry->registry('current_category');
        }
        return false;
    }

    /**
     * Quote object getter
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote;
    }

    public function getCustomer($customerId = '')
    {
        $customer = $this->customerSession->getCustomer();
        return $customer;
    }
     /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    public function formatCustomVariables($str)
    {
        $customer = $this->getCustomer();
        $quote    = $this->getQuote();
        $category = $this->getCurrentCategory();
        $store    = $this->_storeManager->getStore();
        $product  = $this->getCurrentProduct();

        $data = [
            "customer"    => $customer,
            "quote"       => $quote,
            "product"     => $product,
            "category"    => $category,
            "store"       => $store
        ];
        $result = $this->filterManager->template($str, ['variables' => $data]);
        return $result;
    }
}