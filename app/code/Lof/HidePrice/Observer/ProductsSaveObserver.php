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

class ProductsSaveObserver implements ObserverInterface
{
    protected $_resource;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    protected $helper;
        /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Backend\Helper\Js
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Backend\Helper\Js $jsHelper,
        \Lof\HidePrice\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry
        ) {
        $this->jsHelper  = $jsHelper;
        $this->_resource = $resource; 
        $this->helper    = $helper;
    }

    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        if($this->helper->getConfig('general/enable_module') == 1 ) {
            $hideprice = [];
            $controller = $observer->getController();
            $links = $controller->getRequest()->getPost('links');
      
            $links = is_array($links) ? $links : [];
           
            if(isset($links) && !empty($links['hideprice_id'])){
                $hideprice['hideprice_id'] = $links['hideprice_id'];
                $data['hideprice_id'] = $hideprice;
            }
          
            $connection = $this->_resource->getConnection();
            $productId = $controller->getRequest()->getParam('id');
              
            if($productId){
                $product_id = $productId;
            }else{
                $table = $this->_resource->getTableName('catalog_product_entity');
                $select = 'SELECT max(entity_id) as product_id FROM ' . $table.'';
                $productId = $connection->fetchAll($select);
                $product_id= $productId[0]['product_id'];
            }
           
            if($hideprice){
                $table = $this->_resource->getTableName('lof_hideprice_product');
                $where = ['entity_id = ?' => $product_id];
                $connection->delete($table, $where); 
                $data = [];
            
                $data[] = [
                'hideprice_id' => $hideprice['hideprice_id'],
                'entity_id' => $product_id,
                ];
                
                $connection->insertMultiple($table, $data);
            } else {
                $table = $this->_resource->getTableName('lof_hideprice_product');
                $where = ['entity_id = ?' => $product_id];
                $connection->delete($table, $where); 
            }
        }
    }
}