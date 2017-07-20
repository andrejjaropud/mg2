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
namespace Lof\HidePrice\Block\Adminhtml\Message;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context  
     * @param \Magento\Framework\Registry           $registry 
     * @param array                                 $data     
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'message_id';
        $this->_blockGroup = 'Lof_HidePrice';
        $this->_controller = 'adminhtml_message';
        parent::_construct();
        $this->buttonList->add('set_email', [
                        'label' => __('Set Email Template'),
                        'class' => 'primary'
                    ],
        0);
        $this->buttonList->add('send_button', [
                        'label' => __('Send Email'),
                        'class' => 'primary'
                    ],
        0);
        $this->buttonList->remove('save');
        if ($this->_isAllowedAction('Lof_HidePrice::message_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Message'));
        } else {
            $this->buttonList->remove('delete');
        }

    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('lofhideprice_form')->getId()) {
            return __("Edit Message '%1'", $this->escapeHtml($this->_coreRegistry->registry('lofhideprice_message')->getTitle()));
        } else {
            return __('New Message');
        }
    }
     protected function _getJsInitScripts()
    {
        $refreshUrls = [];
        $model = $this->_coreRegistry->registry('lofhideprice_message');
        $dataModel = $model->getData();
    
        $data_send = json_encode($dataModel);
        
        $editUrl = $this->getUrl(
            'lofhideprice/message/sendemail' 
        );
        $setEmailUrl = $this->getUrl('lofhideprice/message/setemail');
          return "
        <script>
        require(['jquery'], function(jQuery){
            jQuery(document).ready(function() {
                jQuery('#send_button').click(function(){
                    jQuery.ajax(
                    {
                    url:'".$editUrl."',
                    type: 'post',
                    dataType: 'html',
                    data : {
                        subject : jQuery('#page_reply_subject').val(),
                        content : jQuery('#page_reply_content').val(),
                        message_id : ".$dataModel['message_id'].",
                        product_id : ".$dataModel['entity_id'].",
                        product_image : '".$dataModel['product_image']."',
                        product_name : jQuery('#page_entity_id').text(),
                        name : jQuery('#page_name').text(),
                        email : jQuery('#page_email').text()
                      },
                      showLoader: true
                    }).done(function(data) 
                    {  
                        location.reload();
                    }); 

                });
                 jQuery('#set_email').click(function(){
                    jQuery.ajax(
                    {
                    url:'".$setEmailUrl."',
                    type: 'post',
                    dataType: 'html',
                    data : {
                        subject : jQuery('#page_reply_subject').val(),
                        content : jQuery('#page_reply_content').val(),
                        message_id : ".$dataModel['message_id'].",
                      },
                      showLoader: true
                    }).done(function(data) 
                    {  
                        location.reload();
                    }); 

                });
            });
        });
        </script>";
    }
     /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    
    protected function _afterToHtml($html)
    {
        return parent::_afterToHtml($html . $this->_getJsInitScripts());
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('lofhideprice/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
    
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
