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
namespace Lof\HidePrice\Model\ResourceModel\Hideprice;

use \Lof\HidePrice\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'hideprice_id';
    /**
     * Define resource model
     *
     * @return void
     */
    
     /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
         //$this->getProductsAfterLoad();
        return parent::_afterLoad();
    }


    protected function _construct()
    {
        $this->_init('Lof\HidePrice\Model\Hideprice', 'Lof\HidePrice\Model\ResourceModel\Hideprice');
    }

      /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('hideprice_id', 'title');
    }
    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
        public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    public function addLinkAttributeToFilter($code, $condition)
    {
        return $this;
    }

    public function addLinkAttributeToFilterProducts($code, $condition)
    {       
        return $this;
    }

        public function addHidepriceToFilter($productId)
    {
        $hideprice = [];
        if ($productId) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_hideprice_product'))
            ->where(
                'entity_id = '.(int)$productId
                );
            $hideprice = $connection->fetchAll($select);
        }

        return $hideprice;

    }
    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function getProductsAfterLoad()
    {
        $items = $this->getColumnValues("hideprice_id");
        if (count($items)) {
            $connection = $this->getConnection();
            $select = 'SELECT * FROM ' . $this->getTable('lof_hideprice_product');
            $products = $connection->fetchAll($select);
            foreach ($this as $item) {
                $select = 'SELECT entity_id FROM ' . $this->getTable('lof_hideprice_product') . ' WHERE hideprice_id = ' .  $item->getData("hideprice_id") . ' ORDER BY position DESC ';
                $products = $connection->fetchCol($select);
                $item->setData('products', $products);
            }
        }
    }


}