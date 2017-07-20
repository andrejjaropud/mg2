<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\HidePrice\Block\Adminhtml\Hideprice\Renderer;
class InquiryForm extends Group\AbstractGroup

{
    /**
     * @var string
     */
    protected $_template = 'form/inquiry_form.phtml';

 
    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return [$this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')];
    }
     /**
     * Retrieve list of initial type
     *
     * @return array
     */
    protected function getType()
    {
        $data = array(

        );
        return $this->_type;
    }
     /**
     * Retrieve list of initial minutes
     *
     * @return array
     */
    protected function getHours()
    {
        return $this->_hours;
    }
    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, [$this, '_sortTierPrices']);
        return $data;
    }

    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _sortTierPrices($a, $b)
    {
        /*if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['cust_group'] != $b['cust_group']) {
            return $this->getCustomerGroups($a['cust_group']) < $this->getCustomerGroups($b['cust_group']) ? -1 : 1;
        }
        if ($a['price_qty'] != $b['price_qty']) {
            return $a['price_qty'] < $b['price_qty'] ? -1 : 1;
        }*/

        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {

        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Add'), 'onclick' => 'return inquiryFormControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_inquiry_form_item_button');
         
        $this->setChild('add_button', $button);

       
        return parent::_prepareLayout();
    }
}