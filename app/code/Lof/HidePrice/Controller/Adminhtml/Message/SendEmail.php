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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class SendEmail extends \Magento\Backend\App\Action
{   
    const XML_PATH_EMAIL_TEMPLATE = 'lofhideprice/emailsend/emailtemplate';
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'lofhideprice/emailsend/emailto';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'lofhideprice/emailsend/emailsenderto';
 

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Lof\HidePrice\Model\Sender
     */ 
    protected $inlineTranslation;
      /**
     * @var DataPersistorInterface
     */
      private $transportBuilder;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Lof\HidePrice\Model\Sender $sender
     */
    
    protected $_helper;

    protected $message;

    public function __construct(
        Action\Context $context,
        \Lof\HidePrice\Model\TransportBuilder $transportBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Lof\HidePrice\Model\Message $message,
        \Lof\HidePrice\Helper\Data $helper
        ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->message           = $message;
        parent::__construct($context);   
        $this->_helper           = $helper;
        $this->transportBuilder  = $transportBuilder;
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
            $this->sendEmail($data);
        }

    }

    public function sendEmail($data)
    { 
        
        $arg =array();
        $arg['product_url'] = $data["product_url"];
        $arg['name'] = $data["name"];
        $arg['email'] = $data["email"];
        if($data['product_id']) {
            $arg['product_image'] = '<img src="'.$data['product_image'].'" style="max-width: 15rem; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;">';
            $arg['product_name'] = $data["product_name"];

            // SEND EMAIL
            $this->inlineTranslation->suspend();
            try{

                $this->transportBuilder
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]);

                    $this->transportBuilder->setTemplateVars($arg);
                    $this->transportBuilder->setTemplateData(
                    [
                        'template_subject' => ($data['subject']),
                        'template_text' => ($data['content'])
                    ]
                    );
                     $this->transportBuilder->setFrom($this->_helper->getConfig('emailsend/emailsenderto'));
                    
                     $this->transportBuilder->addTo($data['email'],$data['name']);
                    
                $this->prefixSubject = '';
              
                $transport = $this->transportBuilder->getTransport();
                
                
                try{
                    $transport->sendMessage();

                    $message = $this->message->getCollection()->addFieldToFilter('message_id',$data["message_id"]);
                     
                    foreach ($message as $key => $_message) {
                        $_message->setReply('Yes')
                            ->setReplySubject($data['subject'])
                            ->setReplyContent($data['content'])
                            ->save();

                    }
                   
                    $this->inlineTranslation->resume();
                     $this->messageManager->addSuccess(__('Email was successfully sent.'));
                }catch(\Exception $e){
                    $error = true;
                    $this->messageManager->addError(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                        );
                }
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }
       
        }    
       
    }


}
