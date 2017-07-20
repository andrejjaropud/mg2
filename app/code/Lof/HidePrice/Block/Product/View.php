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

namespace Lof\HidePrice\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{


    public $hideprice;

    public $_resource;
    /**
     * @param \Magento\Catalog\Block\Product\Context              $context             
     * @param \Magento\Framework\Url\EncoderInterface             $urlEncoder          
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder         
     * @param \Magento\Framework\Stdlib\StringUtils               $string              
     * @param \Magento\Catalog\Helper\Product                     $productHelper       
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig   
     * @param \Magento\Framework\Locale\FormatInterface           $localeFormat        
     * @param \Magento\Customer\Model\Session                     $customerSession     
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository   
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface   $priceCurrency       
     * @param \Lof\HidePrice\Helper\Balance\Spend              $rewardsBalanceSpend 
     * @param \Lof\HidePrice\Helper\Data                       $rewardsData         
     * @param array                                               $data                
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Lof\HidePrice\Model\Hideprice $hideprice,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency
        );
        $this->hideprice  = $hideprice;
        $this->_resource = $resource;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product;
    }


}