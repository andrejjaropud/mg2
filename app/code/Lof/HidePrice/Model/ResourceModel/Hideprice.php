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
namespace Lof\HidePrice\Model\ResourceModel;

/**
 * CMS block model
 */
class Hideprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;



    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_hideprice_hideprice', 'hideprice_id');
    }
    
     /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    { 
        $object->setData('inquiry_form', json_decode($object->getData('inquiry_form'),true));
        $object->setData('callforprice_customergroup', json_decode($object->getData('callforprice_customergroup'),true));
        $object->setData('product_type_ids', json_decode($object->getData('product_type_ids'),true));
        $object->setData('store_id', json_decode($object->getData('store_id'),true));
        if ($id = $object->getId()) {

            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_hideprice_product'))
            ->where(
                'hideprice_id = '.(int)$id
                );
            $products = $connection->fetchAll($select);
            $object->setData('related_products', $products);
        }
        return parent::_afterLoad($object);

    }
    public function emptyArray($array) {
        $empty = TRUE;
        if (is_array($array)) {
            foreach ($array as $value) {
              if (!$this->emptyArray($value)) {
                $empty = FALSE;
              }
            }
        }
        elseif (!empty($array)) {
            $empty = FALSE;
        }
        return $empty;
    }
    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    { 
        if(!$object->getData("isfrontend")){
        // Products Related
         
            //if(isset($object->getProductsRelated())) {
                if($productsRelated = $object->getProductsRelated()){

                    $table = $this->getTable('lof_hideprice_product');
                    $where = ['hideprice_id = ?' => (int)$object->getId()];
                    $where1   = 'hideprice_id NOT IN  ('.(int)$object->getId().')';
                    $connection = $this->getConnection();
                    $select = 'SELECT entity_id as product_id FROM ' . $table.' WHERE '. $where1.'';
                    $productId = $connection->fetchAll($select);

                    $this->getConnection()->delete($table, $where);
                    $data = [];
                    $content = '';
                    foreach ($productsRelated as $k => $_post) {
                         foreach ($productId as $key => $_productId) {
                           if($k == $_productId['product_id']) {
                                $content .= $k.',';
                                $k ='';
                           } 
                        }
                        if(!empty($k)) {
                             $data[] = [
                            'hideprice_id' => (int)$object->getId(),
                            'entity_id' => $k,
                            ];
                        }
                        
                    } 
                   
                    if(!empty($k)) {
                    $this->getConnection()->insertMultiple($table, $data);
                    }
                    if(!empty($content)) {
                         throw new \Magento\Framework\Exception\LocalizedException(
                            __('Produc id '.$content.' was isset in hide price.Other Product saved')
                        );
                    }
                  
                } else {
                    $table = $this->getTable('lof_hideprice_product');
                    $where = ['hideprice_id = ?' => (int)$object->getId()];
                    $this->getConnection()->delete($table, $where);
                }
            //}
        }
        return parent::_afterSave($object);
    }
     /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    { 

        $object->setData('callforprice_customergroup', json_encode($object->getData('callforprice_customergroup')));
        $object->setData('product_type_ids', json_encode($object->getData('product_type_ids')));
        $object->setData('store_id', json_encode($object->getData('store_id')));
        return $this;

    }
}
