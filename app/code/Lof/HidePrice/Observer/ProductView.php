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

namespace Lof\HidePrice\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductView implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

  
    protected $hideprice;

    protected $_resource;

    protected $helper;

    protected $validator;
    /**
     * @param \Magento\Framework\Registry   $coreRegistry           
     */
	public function __construct(
		\Magento\Framework\Registry $coreRegistry,
        \Lof\HidePrice\Model\Hideprice $hideprice,
        \Lof\HidePrice\Helper\Data $helper,
        \Lof\HidePrice\Model\Validator $validator,
        \Magento\Framework\App\ResourceConnection $resource
	) {
        $this->coreRegistry = $coreRegistry;
        $this->hideprice    = $hideprice;
        $this->_resource    = $resource;
        $this->helper       = $helper;
        $this->validator    = $validator;
	}
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->helper->getConfig('general/enable_module') == 1 ) {

            $ObjectManager= \Magento\Framework\App\ObjectManager::getInstance();
            $context = $ObjectManager->get('Magento\Framework\App\Http\Context');
            $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
            $customer_group_current = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);

            $product = $this->coreRegistry->registry('product');
            $product_id = $product->getId();
            $validate = $this->validator->validate($product->getId());
         
            $connection = $this->_resource->getConnection();
            $table = $this->_resource->getTableName('lof_hideprice_product');
            $table1 = $this->_resource->getTableName('lof_hideprice_hideprice');
            $where = ' a.hideprice_id = b.hideprice_id AND b.is_active = 1 AND entity_id = '.$product_id;
            $select = 'SELECT a.hideprice_id FROM ' . $table.' as a , '.$table1.' as b WHERE'.$where;
           
            $hideprice = $connection->fetchAll($select); 
            if(count($hideprice) > 0) {
                $hideprice = $this->hideprice->getCollection()->addFieldToFilter('hideprice_id',$hideprice[0]['hideprice_id']);
                foreach ($hideprice as $key => $_hideprice) {
                    $customer_group = json_decode($_hideprice->getCallforpriceCustomergroup(),true);
                    $store_id = json_decode($_hideprice->getStoreId(),true);
                    
                    if((in_array($customer_group_current,$customer_group) == true || ((in_array(0,$customer_group) == false && $isLoggedIn == false) || in_array(0,$customer_group) ==true)) && (in_array($this->helper->getStoreId(), $store_id) == true || in_array(0,$store_id ) == true)) {
                        
                        $product->setHidePrice($hideprice)->setIsProductView(true);
                    }
                }
            	
            } elseif($validate) {
                $hideprice = $this->hideprice->getCollection()->addFieldToFilter('hideprice_id',$validate);
                $product->setHidePrice($hideprice)->setIsProductView(true);
            }
            $this->coreRegistry->unregister('product');
            $this->coreRegistry->register('product', $product);
        }
    }
}