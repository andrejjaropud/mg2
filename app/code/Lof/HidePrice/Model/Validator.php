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
 * 
 **/
namespace Lof\HidePrice\Model;


class Validator 
{
	 /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    protected $hidepriceFactory;

    protected $helper;

	public function __construct(
	   \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Lof\HidePrice\Model\HidepriceFactory $hidepriceFactory,
       \Lof\HidePrice\Helper\Data $helper,
       \Lof\HidePrice\Model\Condition\Sql\Builder $sqlBuilder	
	 ) {
	   $this->productCollectionFactory     = $productCollectionFactory;
       $this->sqlBuilder    = $sqlBuilder;
        $this->hidepriceFactory = $hidepriceFactory;
         $this->helper       = $helper;
	}
	
	  /**
     * Product Consition validation
     *
     * @param History $historyItem
     * @return bool
    */
    public function validate($product_id)
    {
        $ObjectManager= \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Framework\App\Http\Context');
        $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        $customer_group_current = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);

        $hideprice_id = '';
        if(isset($product_id)) {
            $collection = $this->getProductCollection();
            $hirepriceRules = $this->hidepriceFactory->create();
            $rules = [];
            foreach ($hirepriceRules->getCollection()->addFieldToFilter('is_active',1) as $rule) {
                $customer_group = json_decode($rule->getCallforpriceCustomergroup(),true);
                $store_id = json_decode($rule->getStoreId(),true);
                if((in_array($customer_group_current,$customer_group) == true || ((in_array(0,$customer_group) == false && $isLoggedIn == false) || in_array(0,$customer_group) ==true)) && (in_array($this->helper->getStoreId(), $store_id) == true || in_array(0,$store_id ) == true)) {

                    $collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);         
                    $conditions = $rule->getActions();   
                             
                    $conditions->collectValidatedAttributes($collection);
                    $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
                  
                    $sqlBuilder = $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
                    if($sqlBuilder != true) {
                        $collection->getSelect()->where('e.entity_id IN (?) ',$product_id);
                        if(!empty($collection->getData())) {
                            $hideprice_id = $rule->getHidepriceId();
                        }
                    }
                }
            } 
            if(empty($collection->getData())) {
              return;
            }
            return $hideprice_id;
        }
    }
    /**
     * 
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create(); 
        $collection->addMinimalPrice()
        ->addFinalPrice()
        ->addTaxPercents()
        ->addUrlRewrite()
        ->addStoreFilter();
        return $collection;

    }
}