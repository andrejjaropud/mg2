<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_HidePrice
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Lof\HidePrice\Controller\Adminhtml\Message;

use Magento\Backend\App\Action; 


class SetEmail extends \Magento\Backend\App\Action
{   


    protected $message;

    public function __construct(
        Action\Context $context,
        \Lof\HidePrice\Model\Message $message
        ) {
        $this->message           = $message;
        parent::__construct($context);   
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_HidePrice::email_edit');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {  
        $data = $this->getRequest()->getParams();
        
        if($data){ 
            $this->setEmail($data);
        }

    }
    public function setEmail($data)
    { 
      try {
        $message = $this->message->getCollection()->addFieldToFilter('message_id',$data["message_id"]);
        foreach ($message as $key => $_message) {
          $_message->setReply('Yes')
              ->setReplySubject($data['subject'])
              ->setReplyContent($data['content'])
              ->save();

        }
        $this->messageManager->addSuccess(__('Email was successfully set.'));
      }catch (\Exception $e) {
        $this->messageManager->addError(
            __('We can\'t process your request right now. Sorry, that\'s all we know.')
        );
      }
    }
}
