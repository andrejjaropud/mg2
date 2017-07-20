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
 * @hideprice   Landofcoder
 * @package    Lof_HidePrice
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\HidePrice\Controller\Adminhtml\Hideprice;

class Save extends \Lof\HidePrice\Controller\Adminhtml\Hideprice
{

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;
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
         \Magento\Framework\Registry $coreRegistry,
          \Magento\Backend\Helper\Js $jsHelper
        ) {
        $this->jsHelper = $jsHelper;
        parent::__construct($context, $coreRegistry);
    }
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
   
        
        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];

        if(!empty($links) && isset($links['related'])){
            $productsRelated = $this->jsHelper->decodeGridSerializedInput($links['related']);
            $data['products_related'] = $productsRelated;
        }

        if ($data) {
            $id = $this->getRequest()->getParam('hideprice_id');
            $model = $this->_objectManager->create('Lof\HidePrice\Model\Hideprice')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Hideprice no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            } 
            if(isset($data['inquiry_form'])) {
                    $data['inquiry_form'] = json_encode($data['inquiry_form']);
            } 
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            } 
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            } 
            unset($data['rule']); 
            // init model and set data
            $model->loadPost($data);
            //$model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the Hideprice.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);


                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['hideprice_id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['hideprice_id' => $this->getRequest()->getParam('hideprice_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
